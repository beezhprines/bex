<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'date', 'teams', 'contact_type_id'
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
                self::create([
                    'date' => $date,
                    'contact_type_id' => $contactType->id,
                    'teams' => json_encode($teams->map(function ($team) {
                        return [
                            'team_id' => $team->id,
                            'amount' => null
                        ];
                    }))
                ]);
            }
        }

        note("info", "contact:seed", "Созданы контакты с {$startDate} по {$endDate}", self::class);
    }
}
