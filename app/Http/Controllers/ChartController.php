<?php

namespace App\Http\Controllers;

use App\Models\Master;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function chats(Request $request)
    {
        $data = $request->validate([
            "startDate" => "required|date_format:Y-m-d",
            "endDate" => "required|date_format:Y-m-d",
        ]);

        $startDate = $data["startDate"];
        $endDate = $data["endDate"];

        $chats = Team::select(["id", "title"])
            ->with("masters:id,name,team_id")
            ->with(["contacts" => function ($query) use ($startDate, $endDate) {
                $query->select(["team_id", "date", DB::raw("SUM(amount) as total_amount")])
                    ->whereBetween(DB::raw("DATE(date)"), array($startDate, $endDate))
                    ->groupBy("team_id", "date");
            }])
            ->get()
            ->map(function ($team) {
                $team->total_amount = $team->contacts->sum(function ($contact) {
                    return $contact->total_amount;
                });
                return $team;
            })
            ->first();
        return $chats;
    }
}
