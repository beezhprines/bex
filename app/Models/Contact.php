<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Contact extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        "date", "teams", "contact_type_id"
    ];

    public function team(int $team_id)
    {
        return collect(json_decode($this->teams, true))->firstWhere("team_id", $team_id) ?? null;
    }

    public function contactType()
    {
        return $this->belongsTo(ContactType::class);
    }

    public static function seed(string $startDate, string $endDate, $teams)
    {
        foreach (daterange($startDate, $endDate, true) as $date) {
            foreach (ContactType::all() as $contactType) {
                $contact = self::firstWhere([
                    "contact_type_id" => $contactType->id,
                    "date" => $date
                ]);
                if (empty($contact)) {
                    self::create([
                        "date" => $date,
                        "contact_type_id" => $contactType->id,
                        "teams" => $teams->map(function ($team) {
                            return [
                                "team_id" => $team->id,
                                "amount" => 0
                            ];
                        })->toJson()
                    ]);
                } else {
                    $contact->update([
                        "teams" => $teams->map(function ($team) {
                            return [
                                "team_id" => $team->id,
                                "amount" => 0
                            ];
                        })->toJson()
                    ]);
                }
            }
        }

        note("info", "contact:seed", "Созданы/обновлены контакты с {$startDate} по {$endDate}", self::class);
    }

    public static function getByDatesTypeTeam(string $startDate, string $endDate, Team $team, ContactType $contactType)
    {
        return self::whereBetween(DB::raw("DATE(date)"), array($startDate, $endDate))
            ->where("contact_type_id", $contactType->id)
            ->get()
            ->map(function ($contact) use ($team, $contactType) {
                $data = collect(json_decode($contact->teams, true))->firstWhere("team_id", $team->id);
                if (!empty($data)) {
                    return [
                        "self" => $contact,
                        "contact_type" => $contactType,
                        "teamId" => $data["team_id"],
                        "amount" => $data["amount"],
                        "date" => $contact->date
                    ];
                }
            });
    }

    public static function getDifference(string $startDate, string $endDate, Team $team, ContactType $contactType)
    {
        $max = self::getByDatesTypeTeam($startDate, $endDate, $team, $contactType)
            ->max("amount");

        $min = self::getByDatesTypeTeam($startDate, $endDate, $team, $contactType)
            ->min("amount");

        return $max - $min;
    }
}
