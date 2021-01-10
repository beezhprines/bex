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
            $contactsCollection = $team->contacts()
                ->whereBetween(DB::raw("DATE(date)"), [$startDate, $endDate])
                ->get();

            $datesCollection = collect(array_keys($contactsCollection->groupBy("date")->toArray()));

            $totalContactsCollection = $contactsCollection->groupBy("date")->map(function ($date) {
                return $date->sum(function ($contact) {
                    return $contact->amount;
                });
            });
            $sumTotalContacts = $totalContactsCollection->sum(function ($totalContact) {
                return round($totalContact);
            });

            $outcomes = [];
            foreach ($datesCollection as $date) {
                $outcomes[$date] = $team->getOutcomes($date);
            }
            $sumOutcomes = collect($outcomes)->sum(function ($outcome) {
                return round($outcome);
            });

            $leads = [];

            foreach ($datesCollection as $date) {
                $leads[$date] = $totalContactsCollection->toArray()[$date] == 0 ? 0 : round($outcomes[$date] / $totalContactsCollection->toArray()[$date]);
            };

            $sumLeads = $sumTotalContacts == 0 ? 0 : round($sumOutcomes / $sumTotalContacts);

            $chats->push([
                "info" => [
                    "team_id" => $team->id
                ],
                "title" => ["text" => $team->title],
                "subtitle" => [
                    "text" => "За период: $sumTotalContacts, расходы: $sumOutcomes, цена за лид: $sumLeads",
                    "useHTML" => true
                ],
                "xAxis" => [
                    "categories" => $datesCollection
                        ->map(function ($date) {
                            return viewdate($date);
                        })
                        ->toArray(),
                    "gridLineWidth" => 1
                ],
                "yAxis" => [
                    "title" => ["text" => null],
                    "gridLineWidth" => 1
                ],
                "series" => [
                    [
                        "name" => "Контакты",
                        "data" => array_values($totalContactsCollection->toArray()),
                        "color" => "#c2de80",
                    ],
                    [
                        "name" => "Затраты (тыс. тг)",
                        "data" => array_values(collect($outcomes)->map(function ($outcome) {
                            return round($outcome / 1000);
                        })->toArray()),
                        "color" => "#db9876",
                    ],
                    [
                        "name" => "Лид (тг)",
                        "data" => array_values($leads),
                        "color" => "#aaaaaa",
                    ]
                ]
            ]);
        }

        return view("charts.chats", [
            "teams" => $teams,
            "chats" => $chats
        ]);
    }

    public function conversion(Request $request)
    {
        access(["can-owner", "can-host", "can-manager"]);

        if (!$request->has("startDate") && !$request->has("endDate")) {
            return redirect()->route("charts.conversion", [
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
            $contactsCollection = $team->contacts()
                ->whereBetween(DB::raw("DATE(date)"), [$startDate, $endDate])
                ->get();

            $datesCollection = collect(array_keys($contactsCollection->groupBy("date")->toArray()));

            $conversionRecords = $datesCollection->map(function ($date) use ($team) {
                return $team->solveConversion($date, $date, "records");
            });

            $conversionAttendanceRecords = $datesCollection->map(function ($date) use ($team) {
                return $team->solveConversion($date, $date, "attendance_records");
            });

            $chats->push([
                "info" => [
                    "team_id" => $team->id
                ],
                "title" => ["text" => $team->title],
                "subtitle" => [
                    "text" => null
                ],
                "xAxis" => [
                    "categories" => $datesCollection
                        ->map(function ($date) {
                            return viewdate($date);
                        })
                        ->toArray(),
                    "gridLineWidth" => 1
                ],
                "yAxis" => [
                    "title" => ["text" => null],
                    "gridLineWidth" => 1
                ],
                "series" => [
                    [
                        "name" => "Конверсия записей",
                        "data" => $conversionRecords->toArray(),
                        "color" => "#c2de80",
                    ],
                    [
                        "name" => "Конверсия пришедших",
                        "data" => $conversionAttendanceRecords->toArray(),
                        "color" => "#db9876",
                    ]
                ]
            ]);
        }

        return view("charts.conversion", [
            "teams" => $teams,
            "chats" => $chats
        ]);
    }
}
