<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Record extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        "origin_id", "started_at", "duration", "comment", "attendance", "master_id"
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)->withTimestamps()->withPivot(["comission", "profit"]);
    }

    public static function get(string $startDate, string $endDate, bool $attendance = true)
    {
        return self::whereBetween(DB::raw("DATE(started_at)"), array($startDate, $endDate))
            ->where("attendance", $attendance)
            ->get();
    }

    public static function solveComission($records, bool $withTeamPremiumRate = false)
    {
        return $records->sum(function ($record) use ($withTeamPremiumRate) {
            return $record->services->sum(function ($service) use ($withTeamPremiumRate) {
                $premiumRate = 1;
                if ($withTeamPremiumRate) {
                    $team = $service->master->team;
                    if (!empty($team->premium_rate)) {
                        $premiumRate = floatval($team->premium_rate);
                    }
                }
                return floatval($service->pivot->comission) * $premiumRate;
            });
        });
    }

    public static function solveProfit($records)
    {
        return $records->sum(function ($record) {
            return $record->services->sum(function ($service) {
                return floatval($service->pivot->profit);
            });
        });
    }

    public static function seed($items, string $date)
    {
        // clean all records for given date
        $records = self::whereDate("started_at", $date)->get();
        foreach ($records as $record) {
            $record->services()->detach();
            $record->forceDelete();
        }

        // add new records
        foreach ($items as $item) {
            if (!isset($item["visit_attendance"]) || !in_array($item["visit_attendance"], [1, -1])) continue;

            if (empty($item["staff_id"])) continue;
            if (empty($item["services"])) continue;

            // find master
            $master = Master::findByOriginId($item["staff_id"]);
            if (empty($master)) continue;

            // create or update record
            $record = self::createOrUpdate(self::peel($item));

            // associate with existing master
            $record->master()->associate($master);

            foreach ($item["services"] as $serviceData) {
                // find current service in database
                $service = Service::findByOriginId($serviceData["id"]);

                // if service not found or master has no team, we skip exchange
                if (empty($service) || empty($master->currency())) continue;

                $currencyRateDate = $date;
                // if week is over - we get currency of next monday
                $nextMonday = week()->monday(week()->next($date));
                if (isodate() >= $nextMonday) {
                    $currencyRateDate = $nextMonday;
                }

                // exchange to kzt
                $pivot = [
                    "comission" => CurrencyRate::exchange(
                        $currencyRateDate,
                        $master->currency(),
                        $service->comission
                    ),
                    "profit" => !empty($service->price) ? CurrencyRate::exchange(
                        $currencyRateDate,
                        $master->currency(),
                        $service->price - $service->comission
                    ) : 0
                ];

                $record->services()->attach($service->id, $pivot);
            }

            $record->save();
        }

        note("info", "records:seed", "Обновлены записи из апи на дату {$date}", self::class);
    }

    private static function createOrUpdate(array $data)
    {
        if (empty($data["origin_id"])) return null;

        $record = self::findByOriginId($data["origin_id"]);

        if (empty($record)) {
            $record = self::create($data);
        } else {
            $record->update($data);
            $record->refresh();
        };

        return $record;
    }

    private static function peel(array $item)
    {
        $data = [];

        if (!empty($item["id"])) {
            $data["origin_id"] = $item["id"];
        }

        if (!empty($item["datetime"])) {
            $data["started_at"] = date(config("app.iso_datetime"), strtotime($item["datetime"]));
        }

        if (!empty($item["seance_length"])) {
            $data["duration"] = $item["seance_length"];
        }

        if (!empty($item["comment"])) {
            $data["comment"] = $item["comment"];
        }

        if (!empty($item["attendance"])) {
            $data["attendance"] = $item["attendance"] == 1;
        }

        return $data;
    }
}
