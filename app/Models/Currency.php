<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Currency extends Model
{
    use HasFactory, SoftDeletes, ModelBase, ClearsResponseCache;

    protected $fillable = [
        "title", "code"
    ];

    public function countries()
    {
        return $this->hasMany(Country::class);
    }

    public function currencyRates()
    {
        return $this->hasMany(CurrencyRate::class);
    }

    public function avgRate(string $startDate, string $endDate)
    {
        $nextMonday = week()->monday(week()->next($endDate));

        if (isodate() >= $nextMonday) {
            return CurrencyRate::where("date", $nextMonday)
                ->where("currency_id", $this->id)
                ->first()
                ->rate;
        }

        return CurrencyRate::whereBetween(DB::raw("DATE(date)"), array($startDate, $endDate))
            ->where("currency_id", $this->id)
            ->get()
            ->avg("rate");
    }

    public static function currencyAndTeam()
    {
        $result = DB::select(DB::raw("select cur.code cur_code,cur.id cur_id,cur.title cur_title,t.id team_id from currencies cur
inner join countries coun on cur.id = coun.currency_id
inner join cities c on coun.id = c.country_id
inner join teams t on c.id = t.city_id;"));
        return $result;
    }
}
