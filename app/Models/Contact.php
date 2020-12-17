<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        "date", "team_id", "contact_type_id", "amount"
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function contactType()
    {
        return $this->belongsTo(ContactType::class);
    }

    public static function seed(string $startDate, string $endDate, $teams)
    {
        $contactTypes = ContactType::all();

        foreach ($contactTypes as $contactType) {
            foreach (daterange($startDate, $endDate, true) as $date) {
                $date = date_format($date, config("app.iso_date"));
                if (date("D", strtotime($date)) != "Sun") continue;

                foreach ($teams as $team) {
                    $contact = self::firstWhere([
                        "contact_type_id" => $contactType->id,
                        "date" => $date,
                        "team_id" => $team->id
                    ]);

                    if (empty($contact)) {
                        self::create([
                            "date" => $date,
                            "contact_type_id" => $contactType->id,
                            "team_id" => $team->id
                        ]);
                    }
                }
            }
        }

        note("info", "contact:seed", "Созданы контакты с {$startDate} по {$endDate}", self::class);
    }
}
