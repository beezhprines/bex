<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function chats(Request $request)
    {
        access(["can-owner", "can-host", "can-manager"]);

        if (!$request->has("startDate") && !$request->has("endDate")) {
            return redirect()->route("charts.chats", [
                "startDate" => week()->beforeWeeks(4, week()->start()),
                "endDate" => week()->end(),
            ]);
        }

        $data = $request->validate([
            "startDate" => "required|date_format:Y-m-d",
            "endDate" => "required|date_format:Y-m-d",
        ]);

        $startDate = $data["startDate"];
        $endDate = $data["endDate"];

        $chats = collect();

        $teams = Team::all();
        foreach ($teams as $team) {
            $contacts = $team->contacts()
                ->whereBetween(DB::raw("DATE(date)"), [$startDate, $endDate])
                ->get();

            $chats->push([
                "id" => $team->id,
                "data" => collect([
                    "title" => "Диаграмма чатов {$team->title}",
                    "x" => collect(array_keys($contacts->groupBy("date")->toArray()))->map(function ($date) {
                        return viewdate($date);
                    })->toArray(),
                    "y" => array_values(
                        $contacts->groupBy("date")->map(function ($date) {
                            return $date->sum(function ($contact) {
                                return $contact->amount;
                            });
                        })->toArray()
                    )
                ])
            ]);
        }

        return view("charts.chats", [
            "teams" => $teams,
            "chats" => $chats
        ]);
    }
}
