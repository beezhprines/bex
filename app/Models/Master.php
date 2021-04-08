<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Master extends Model
{
    use HasFactory, SoftDeletes, ModelBase, ClearsResponseCache;

    private static string $password = "1234qwer";

    protected $fillable = [
        "origin_id", "specialization", "avatar", "schedule_till", "user_id", "team_id", "deleted_at", "name"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function currency()
    {
        return optional($this->team)->currency() ?? null;
    }

    public function operator()
    {
        return optional($this->team)->operator ?? null;
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    public function budgets()
    {
        return $this->belongsToMany(Budget::class)->withTimestamps();
    }

    public function getRecords(string $startDate, string $endDate, bool $attendance = true, bool $onlyConversionServices = false)
    {
        $records = $this->records()
            ->whereBetween(DB::raw("DATE(started_at)"), array($startDate, $endDate))
            ->where("attendance", $attendance)
            ->get();

        if ($onlyConversionServices) {
            $records = $records->filter(function ($record) {
                return $record->services->filter(function ($service) {
                    return $service->conversion;
                });
            });
        }

        return $records;
    }

    public function getRecordByService(string $startDate, string $endDate)
    {
        $records = collect();
        foreach ($this->services as $service) {
            foreach (daterange($startDate, $endDate, true) as $date) {
                $date = date_format($date, config('app.iso_date'));
                $records = $records->merge($service->getRecordsBetweenDates($date, $date));
            }
        }
        return $records;
    }

    public function solveComission(string $startDate, string $endDate)
    {
        return Record::solveComission($this->getRecords($startDate, $endDate));
    }

    public function solveProfit(string $startDate, string $endDate)
    {
        return Record::solveProfit($this->getRecords($startDate, $endDate));
    }

    public function solvePenalty(string $date, float $weekComission)
    {
        $penaltyPercent = floatval(Configuration::findByCode("master:penalty")->value);
        $firstDay = date(config('app.iso_date'), strtotime("this week Monday", strtotime($date)));
        $today = isodate();
        $diff = (strtotime($today) - strtotime($firstDay))/(60*60*24);

        if ( $diff >= 9) {
            if($diff-6>5){
                $coef = 5;
            }else{
                $coef = $diff-6;
            }
            return round($weekComission * $penaltyPercent / 100)*$coef;
        }
        return null;
    }

    public function getPenalty(string $startDate, string $endDate)
    {
        $budgetType = BudgetType::findByCode("master:penalty:income");

        $amount = round(
            $this->budgets
                ->whereBetween("date", [$startDate, $endDate])
                ->where("budget_type_id", $budgetType->id)
                ->sum(function ($budget) {
                    return $budget->amount;
                })
        );

        return $amount == 0 ? 0 : $amount *  $budgetType->sign();
    }

    public function getComission(string $startDate, string $endDate)
    {
        $budgetType = BudgetType::findByCode("master:comission:income");

        $amount = round(
            $this->budgets
                ->whereBetween("date", [$startDate, $endDate])
                ->where("budget_type_id", $budgetType->id)
                ->sum(function ($budget) {
                    return $budget->amount;
                })
        );

        return $amount == 0 ? 0 : $amount *  $budgetType->sign();
    }

    public function getComissionWithoutExchange(string $startDate, string $endDate)
    {
        return $this->getRecords($startDate, $endDate)->sum(function ($record) {
            return $record->services->sum(function ($service) {
                return $service->comission ?? 0;
            });
        });
    }

    public function getProfit(string $startDate, string $endDate)
    {
        $budgetType = BudgetType::findByCode("master:profit:outcome");

        $amount = round(
            $this->budgets
                ->whereBetween("date", [$startDate, $endDate])
                ->where("budget_type_id", $budgetType->id)
                ->sum(function ($budget) {
                    return $budget->amount;
                })
        );

        return $amount == 0 ? 0 : $amount *  $budgetType->sign();
    }

    public function getTotalProfit()
    {
        $budgetType = BudgetType::findByCode("master:profit:outcome");

        $amount = round(
            $this->budgets
                ->where("budget_type_id", $budgetType->id)
                ->sum(function ($budget) {
                    return $budget->amount;
                })
        );

        return $amount == 0 ? 0 : $amount *  $budgetType->sign();
    }

    public function getBudget(string $date, int $budgetTypeId)
    {
        return $this->budgets
            ->where("date", $date)
            ->where("budget_type_id", $budgetTypeId)
            ->first();
    }

    private static function peel(array $item)
    {
        if (!empty($item["id"])) {
            $item["origin_id"] = intval(trim($item["id"]));
            unset($item["id"]);
        }

        if (($item["fired"] ?? 1) == 1 || ($item["hidden"] ?? 1 == 1)) {
            $item["deleted_at"] = date(config("app.iso_datetime"));
        }

        return $item;
    }

    public static function seed($items)
    {
        $collection = collect($items)->filter(function ($item) {
            return $item["specialization"] != "Косметолог";
        });

        $collection->each(function ($item) {
            self::createOrUpdate(self::peel($item));
        });

        note("info", "master:seed", "Обновлены мастера из апи", self::class);
    }

    private static function createOrUpdate(array $item)
    {
        if (empty($item["origin_id"])) {
            return null;
        }

        return self::createOrUpdateWithRelations($item, self::findByOriginId($item["origin_id"]));
    }

    private static function createOrUpdateWithRelations(array $data, ?Master $master)
    {
        if (empty($master)) {
            $master = self::create($data);
        } else {
            $master->update($data);
            $master->refresh();
        }

        if (empty($data["deleted_at"] ?? null)) {
            $master->restore();
        }

        $role = Role::findByCode("master");

        if (empty($master->user)) {
            $user = User::create([
                "account" => translit($data["name"] . "-" . $data["origin_id"]),
                "email" => $data["user"]["email"] ?? null,
                "phone" => $data["user"]["phone"] ?? null,
                "password" => bcrypt(self::$password),
                "open_password" => self::$password,
                "role_id" => $role->id
            ]);

            $master->user()->associate($user);
            $master->save();
        } else {
            $user = $master->user->update([
                "email" => $data["user"]["email"] ?? null,
                "phone" => $data["user"]["phone"] ?? null
            ]);
        }

        return $master;
    }

    public function solveOperatorsPoints(string $startDate, string $endDate)
    {
        return optional($this->operator())->solvePointsPerMaster($this, $startDate, $endDate);
    }

    public function solveManagerBonus(float $masterComission, float $comission, float $managerBonus)
    {
        return $comission != 0 ?  $masterComission / $comission * $managerBonus : 0;
    }

    public function getUnexpectedComission(string $startDate, string $endDate, bool $exchange = false)
    {
        $budgetType = BudgetType::findByCode("master:unexpected:income");

        $amount = round(
            $this->budgets
                ->whereBetween("date", [$startDate, $endDate])
                ->where("budget_type_id", $budgetType->id)
                ->sum(function ($budget) {
                    return $budget->amount ?? 0;
                })
        );

        if ($exchange) {
            $currency_rate = self::getCurrencyRate(week()->last())[0];
            $rateTemp =$currency_rate['currency_rate'];
            if($rateTemp==0){
                $rateTemp = 1;
            }
            $amount = empty($this->currency()) ? 0 : $amount / $rateTemp;
        }

        return $amount == 0 ? 0 : $amount *  $budgetType->sign();
    }
    public function getCurrencyRate(string $date){
        $result = array([
            'currency_rate' => 0,
            'currency' => 0
        ]);
        if(! empty($this->currency())){
            $nextWeekMonday = date(config('app.iso_date'), strtotime("next week Monday", strtotime($date)));

            $currencyRate = CurrencyRate::findByCurrencyAndDate($this->currency(),$nextWeekMonday);
            if(empty($currencyRate)){
                $currencyRate = CurrencyRate::findByCurrencyAndDate($this->currency(),isodate());
            }
            $result = array([
                'currency_rate' => $currencyRate->rate,
                'currency_name' => $this->currency()->title
            ]);
        }



        return $result;
    }
    public function updateUnexpectedComission(string $date, float $amount,float $enterAmmount = 0)
    {
        $budgetType = BudgetType::findByCode("master:unexpected:income");
        $budget = $this->getBudget($date, $budgetType->id);

        if (empty($budget)) {
            $budget = Budget::create([
                "amount" => $amount,
                "date" => $date,
                "budget_type_id" => $budgetType->id,
                "json" => json_encode([
                    "amount" => $enterAmmount
                ])
            ]);

            $budget->masters()->attach($this);
        } else {
            $budget->update([
                "amount" => $amount,
                "json" => json_encode([
                    "amount" => $enterAmmount
                ])
            ]);
        }
    }
}
