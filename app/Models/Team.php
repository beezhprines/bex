<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Team extends Model
{
    use HasFactory, SoftDeletes, ModelBase, ClearsResponseCache;

    protected $fillable = [
        "title", "operator_id", "city_id"
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function masters()
    {
        return $this->hasMany(Master::class);
    }

    public function cosmetologists()
    {
        return $this->hasMany(Cosmetologist::class);
    }

    public function currency()
    {
        return $this->city->country->currency ?? null;
    }

    public function currencyRate(string $date)
    {
        return CurrencyRate::findByCurrencyAndDate($this->team->currency(), $date);
    }

    public function budgets()
    {
        return $this->belongsToMany(Budget::class)->withTimestamps();
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public static function seedOutcomes(string $date)
    {
        $budgetTypeInstagram = BudgetType::findByCode("marketer:team:instagram:outcome");
        $budgetTypeVK = BudgetType::findByCode("marketer:team:vk:outcome");

        $teams = self::all();

        $budget = Budget::findByDateAndType($date, $budgetTypeInstagram);
        if (empty($budget)) {
            Budget::create([
                "date" => $date,
                "json" => $teams->map(function ($team) {
                    return [
                        "team_id" => $team->id,
                        "amount" => 0
                    ];
                })->toJson(),
                "budget_type_id" => $budgetTypeInstagram->id,
            ]);
        }

        $budget = Budget::findByDateAndType($date, $budgetTypeVK);
        if (empty($budget)) {
            Budget::create([
                "date" => $date,
                "json" => $teams->map(function ($team) {
                    return [
                        "team_id" => $team->id,
                        "amount" => 0
                    ];
                })->toJson(),
                "budget_type_id" => $budgetTypeVK->id,
            ]);
        }

        note("info", "budget:seed", "Созданы затраты на команду на дату {$date}", Budget::class);
    }

    public function solveComission(string $startDate, string $endDate)
    {
        // get total team comission in KZT
        $comission = $this->masters->sum(function ($master) use ($startDate, $endDate) {
            return $master->solveComission(
                $startDate,
                $endDate
            );
        });

        $comission += $this->cosmetologists->sum(function ($cosmetologist) use ($startDate, $endDate) {
            return $cosmetologist->getComission($startDate, $endDate) ?? 0;
        });

        return $comission;
    }

    public function getContactsIncrease(string $date)
    {
        $amount = 0;
        $contactTypes = ContactType::all();

        foreach ($contactTypes as $contactType) {
            if ($contactType->code == "phone") {
                $prevDate = week()->previous($date);
                $currentAmount = $this->contacts()->firstWhere(["date" => $date, "contact_type_id" => $contactType->id])->amount ?? 0;
                $prevAmount = $this->contacts()->firstWhere(["date" => $prevDate, "contact_type_id" => $contactType->id])->amount ?? 0;
                $amount += $currentAmount - $prevAmount;
            } else {
                $amount += $this->contacts()->firstWhere(["date" => $date, "contact_type_id" => $contactType->id])->amount ?? 0;
            }
        }

        return $amount;
    }

    public function getRecords(string $startDate, string $endDate, bool $attendanceOnly)
    {
        return $this->masters->sum(function ($master) use ($startDate, $endDate, $attendanceOnly) {
            $amount = $master->getRecords($startDate, $endDate, true, true)->count();
            if (!$attendanceOnly) {
                $amount += $master->getRecords($startDate, $endDate, false, true)->count();
            }
            return $amount;
        });
    }

    public function solveConversion(string $startDate, string $endDate, string $type)
    {
        $contacts = $this->getContactsIncrease($endDate);

        switch ($type) {
            case "records":
                $records = $this->getRecords($startDate, $endDate, false);
                break;

            case "attendance_records":
                $records = $this->getRecords($startDate, $endDate, true);
                break;

            default:
                abort(500, "Bad conversion type");
                break;
        }

        return $records != 0 ? round($contacts / $records * 100) : 0;
    }

    public function getOutcomes(string $date)
    {
        $budgetTypeInstagram = BudgetType::findByCode("marketer:team:instagram:outcome");
        $budgetTypeVK = BudgetType::findByCode("marketer:team:vk:outcome");

        $amount = 0;

        $instagram = collect(json_decode(Budget::findByDateAndType($date, $budgetTypeInstagram)->json, true));
        $vk = collect(json_decode(Budget::findByDateAndType($date, $budgetTypeVK)->json, true));
        $amount += $instagram->firstWhere("team_id", $this->id)["amount"] ?? 0;
        $amount += $vk->firstWhere("team_id", $this->id)["amount"] ?? 0;

        return $amount;
    }


    public function getIncomes(string $date)
    {
        $amount = 0;
        $budgetUnexpectedIn= BudgetType::findByCode("master:unexpected:income");
        $budgetTypeComission = BudgetType::findByCode("total:comission:income");
        $monday = date(config('app.iso_date'), strtotime("this week Monday", strtotime($date)));
        $sunday = date(config('app.iso_date'), strtotime("this week Sunday", strtotime($date)));

        $sql = "select sum(b.amount) amount  from budgets b
inner join budget_master bm on b.id = bm.budget_id
inner join masters m on bm.master_id = m.id
where m.team_id = {$this->id}
and b.date between '{$monday}' and '{$sunday}'
and b.budget_type_id in (5,12)";
        $result = DB::select(DB::raw($sql));
        return $result[0];
    }
}
