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
        'origin_id', 'started_at', 'duration', 'comment', 'attendance', 'master_id'
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)->withTimestamps()->withPivot(['comission', 'profit']);
    }

    public static function get(string $startDate, string $endDate, bool $attendance = true)
    {
        return self::whereBetween(DB::raw('DATE(started_at)'), array($startDate, $endDate))
            ->where('attendance', $attendance)
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

    public static function seed($records, $date)
    {
        foreach ($records as $item) {
            if (!in_array($item["visit_attendance"], [1, -1])) continue;

            // create or update record
            $record = self::createOrUpdate(self::peel($item));

            // associate with its staff
            if (!empty($item["staff_id"])) {
                $master = Master::findByOriginId($item["staff_id"]);

                if (!empty($master)) {
                    $record->master()->associate($master);

                    if (!empty($item["services"])) {

                        foreach ($item['services'] as $serviceData) {
                            $service = Service::findByOriginId($serviceData["id"]);

                            if (!empty($service) && !empty($master->currency())) {
                                // associate service with record and exchange comission

                                $pivot = [
                                    'comission' => CurrencyRate::exchange(
                                        date_format(new DateTime($record->started_at), config('app.iso_date')),
                                        $master->currency(),
                                        $service->comission
                                    ),
                                    'profit' => !empty($service->price) ? CurrencyRate::exchange(
                                        date_format(new DateTime($record->started_at), config('app.iso_date')),
                                        $master->currency(),
                                        $service->price - $service->comission
                                    ) : 0
                                ];

                                if ($record->services()->where('service_id', $service->id)->exists()) {
                                    $record->services()->updateExistingPivot($service->id, $pivot);
                                } else {
                                    $record->services()->attach($service->id, $pivot);
                                }
                            }
                        }
                    }
                }
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

        if (!empty($item['id'])) {
            $data['origin_id'] = $item['id'];
        }

        if (!empty($item["datetime"])) {
            $data['started_at'] = date(config('app.iso_datetime'), strtotime($item['datetime']));
        }

        if (!empty($item["seance_length"])) {
            $data['duration'] = $item["seance_length"];
        }

        if (!empty($item["comment"])) {
            $data['comment'] = $item["comment"];
        }

        if (!empty($item["attendance"])) {
            $data['attendance'] = $item["attendance"] == 1;
        }

        return $data;
    }
}
