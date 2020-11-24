<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cosmetologist extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    private static string $password = "1234qwer";

    protected $fillable = [
        "origin_id", "specialization", "avatar", "schedule_till", "user_id", "team_id", "deleted_at", "name"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function budgets()
    {
        return $this->belongsToMany(Budget::class)->withTimestamps();
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
            return $item["specialization"] == "Косметолог";
        });

        $collection->each(function ($item) {
            self::createOrUpdate(self::peel($item));
        });

        note("info", "cosmetologist:seed", "Обновлены косметологи из апи", self::class);
    }

    private static function createOrUpdate(array $item)
    {
        if (empty($item["origin_id"])) {
            return null;
        }

        return self::createOrUpdateWithRelations($item, self::findByOriginId($item["origin_id"]));
    }

    private static function createOrUpdateWithRelations(array $data, ?Cosmetologist $cosmetologist)
    {
        if (empty($cosmetologist)) {
            $cosmetologist = self::create($data);
        } else {
            $cosmetologist->update($data);
            $cosmetologist->refresh();
        }

        if (empty($data["deleted_at"] ?? null)) {
            $cosmetologist->restore();
        }

        $role = Role::findByCode("cosmetologist");

        if (empty($cosmetologist->user)) {
            $user = User::create([
                "account" => translit($data["name"] . "-" . $data["origin_id"]),
                "email" => $data["user"]["email"] ?? null,
                "phone" => $data["user"]["phone"] ?? null,
                "password" => bcrypt(self::$password),
                "open_password" => self::$password,
                "role_id" => $role->id
            ]);

            $cosmetologist->user()->associate($user);
            $cosmetologist->save();
        } else {
            $user = $cosmetologist->user->update([
                "email" => $data["user"]["email"] ?? null,
                "phone" => $data["user"]["phone"] ?? null
            ]);
        }

        return $cosmetologist;
    }


    public function getComission(string $startDate, string $endDate)
    {
        $budgetType = BudgetType::findByCode("cosmetologist:comission:income");

        $amount = round(
            $this->budgets
                ->whereBetween("date", [$startDate, $endDate])
                ->where("budget_type_id", $budgetType->id)
                ->sum(function ($budget) {
                    return $budget->amount ?? 0;
                })
        );

        return $amount == 0 ? 0 : $amount *  $budgetType->sign();
    }
}
