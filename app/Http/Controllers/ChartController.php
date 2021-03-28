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
                "startDate" => week()->beforeWeeks(12, week()->sunday(isodate())),
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
        $datesCollectionTotal = collect();
        $commonContact = array();
        $commonOutCome = array();
        $commonLead = array();

        $teams = Team::all();
        foreach ($teams as $team) {
            $contactsCollection = $team->contacts()
                ->whereBetween(DB::raw("DATE(date)"), [$startDate, $endDate])
                ->get();

            $datesCollection = collect(array_keys($contactsCollection->groupBy("date")->toArray()));
            if($datesCollectionTotal->count()<$datesCollection->count()){
                $datesCollectionTotal = $datesCollection;
            }
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

            $masterNames = implode(",", $team->masters->pluck("name")->toArray());
            $chatArray = [
                "info" => [
                    "team_id" => $team->id
                ],
                "title" => ["text" => $team->title],
                "subtitle" => [
                    "text" => "$masterNames<br>За период: $sumTotalContacts, расходы: $sumOutcomes, цена за лид: $sumLeads",
                    "useHTML" => true
                ],
                "xAxis" => [
                    "categories" => $datesCollection
                        ->map(function ($date) {
                            return date("d M", strtotime($date));
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
                        "visible" => false,
                    ]
                ]
            ];
            $chats->push($chatArray);
            $temp = array_values($totalContactsCollection->toArray());;
            if(sizeof($commonContact)){
                foreach ($temp as $k => $v){
                    if(isset($commonContact[$k])){
                        $commonContact[$k] = $commonContact[$k] + $temp[$k];
                    }else{
                        $commonContact[$k] = $temp[$k];
                    }
                }
            }else{
                $commonContact=array_values($totalContactsCollection->toArray());
            }
            //
            $temp = array_values(collect($outcomes)->map(function ($outcome) {
                return round($outcome / 1000);
            })->toArray());

            if(sizeof($commonOutCome)){
                foreach ($temp as $k => $v){
                    if(isset($commonOutCome[$k])){
                        $commonOutCome[$k] = $commonOutCome[$k] + $temp[$k];
                    }else{
                        $commonOutCome[$k] = $temp[$k];
                    }
                }
            }else{
                $commonOutCome=array_values($totalContactsCollection->toArray());
            }
            //
            //leads
            $temp = array_values($leads);

            if(sizeof($commonLead)){
                foreach ($temp as $k => $v){
                    if(isset($commonLead[$k])){
                        $commonLead[$k] = $commonLead[$k] + $temp[$k];
                    }else{
                        $commonLead[$k] = $commonLead[$k];
                    }
                }
            }else{
                $commonLead=array_values($totalContactsCollection->toArray());
            }

        }
        $commonContactToChat = [
            "info" => [
                "id" => "common_contacts"
            ],
            "title" => ["text" => "Контакты и затраты"],
            "subtitle" => [
                "text" => "Text",
                "useHTML" => true
            ],
            "xAxis" => [
                "categories" => $datesCollectionTotal
                    ->map(function ($date) {
                        return date("d M", strtotime($date));
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
                    "data" => array_values($commonContact),
                    "color" => "#c2de80",
                ],
                [
                    "name" => "Затраты (тыс. тг)",
                    "data" => array_values($commonOutCome),
                    "color" => "#db9876",
                ]
            ]
        ];
        //leads
        $commonLeadsToCart = [
            "info" => [
                "id" => "common_contacts"
            ],
            "title" => ["text" => "Лид"],
            "subtitle" => [
                "text" => "Text",
                "useHTML" => true
            ],
            "xAxis" => [
                "categories" => $datesCollectionTotal
                    ->map(function ($date) {
                        return date("d M", strtotime($date));
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
                    "name" => "Лид (тг)",
                    "data" => array_values($commonLead),
                    "color" => "#aaaaaa",
                ]
            ]
        ];

        return view("charts.chats", [
            "teams" => $teams,
            "chats" => $chats,
            "commonContactToChat" => $commonContactToChat,
            "commonLeadsToCart" => $commonLeadsToCart
        ]);
    }
    public function chatsCommon(Request $request)
    {
        access(["can-owner", "can-host", "can-manager"]);

        if (!$request->has("startDate") && !$request->has("endDate")) {
            return redirect()->route("charts.chats-common", [
                "startDate" => week()->beforeWeeks(12, week()->sunday(isodate())),
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
        $datesCollectionTotal = collect();
        $commonContact = array();
        $commonOutCome = array();
        $commonLead = array();

        $teams = Team::all();
        foreach ($teams as $team) {
            $contactsCollection = $team->contacts()
                ->whereBetween(DB::raw("DATE(date)"), [$startDate, $endDate])
                ->get();

            $datesCollection = collect(array_keys($contactsCollection->groupBy("date")->toArray()));
            if($datesCollectionTotal->count()<$datesCollection->count()){
                $datesCollectionTotal = $datesCollection;
            }
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

            $masterNames = implode(",", $team->masters->pluck("name")->toArray());
            $chatArray = [
                "info" => [
                    "team_id" => $team->id
                ],
                "title" => ["text" => $team->title],
                "subtitle" => [
                    "text" => "$masterNames<br>За период: $sumTotalContacts, расходы: $sumOutcomes, цена за лид: $sumLeads",
                    "useHTML" => true
                ],
                "xAxis" => [
                    "categories" => $datesCollection
                        ->map(function ($date) {
                            return date("d M", strtotime($date));
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
                        "visible" => false,
                    ]
                ]
            ];
            $chats->push($chatArray);
            $temp = array_values($totalContactsCollection->toArray());;
            if(sizeof($commonContact)){
                foreach ($temp as $k => $v){
                    if(isset($commonContact[$k])){
                        $commonContact[$k] = $commonContact[$k] + $temp[$k];
                    }else{
                        $commonContact[$k] = $temp[$k];
                    }
                }
            }else{
                $commonContact=array_values($totalContactsCollection->toArray());
            }
            //
            $temp = array_values(collect($outcomes)->map(function ($outcome) {
                return round($outcome / 1000);
            })->toArray());

            if(sizeof($commonOutCome)){
                foreach ($temp as $k => $v){
                    if(isset($commonOutCome[$k])){
                        $commonOutCome[$k] = $commonOutCome[$k] + $temp[$k];
                    }else{
                        $commonOutCome[$k] = $temp[$k];
                    }
                }
            }else{
                $commonOutCome=array_values($totalContactsCollection->toArray());
            }
            //


        }

        $sizeContacts = sizeof($commonContact);
        $sizeOutCome = sizeof($commonOutCome);
        $sizeLeads = 0;

        if($sizeContacts>=$sizeOutCome){
            $sizeLeads = $sizeContacts;
        }else{
            $sizeLeads = $sizeOutCome;
        }

        for ($i = 0; $i<$sizeLeads;$i++){
            if(isset($commonContact[$i]) && isset($commonOutCome[$i]) && $commonContact[$i]!=0 && $commonOutCome[$i]!= 0){
                $commonLead [$i] =  round(($commonOutCome[$i]*1000)/$commonContact[$i]);
            }else{
                $commonLead [$i] = 0;
            }
        }


        $commonContactToChat = [
            "info" => [
                "id" => "common_contacts"
            ],
            "title" => ["text" => "Контакты и затраты"],
            "subtitle" => [
                "text" => "Text",
                "useHTML" => true
            ],
            "xAxis" => [
                "categories" => $datesCollectionTotal
                    ->map(function ($date) {
                        return date("d M", strtotime($date));
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
                    "data" => array_values($commonContact),
                    "color" => "#c2de80",
                ],
                [
                    "name" => "Затраты (тыс. тг)",
                    "data" => array_values($commonOutCome),
                    "color" => "#db9876",
                ]
            ]
        ];
        //leads
        $commonLeadsToCart = [
            "info" => [
                "id" => "common_contacts"
            ],
            "title" => ["text" => "Средняя цена за лид"],
            "subtitle" => [
                "text" => "Text",
                "useHTML" => true
            ],
            "xAxis" => [
                "categories" => $datesCollectionTotal
                    ->map(function ($date) {
                        return date("d M", strtotime($date));
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
                    "name" => "Лид (тг)",
                    "data" => array_values($commonLead),
                    "color" => "#aaaaaa",
                ]
            ]
        ];

        return view("charts.chats-common", [
            "teams" => $teams,
            "chats" => $chats,
            "commonContactToChat" => $commonContactToChat,
            "commonLeadsToCart" => $commonLeadsToCart
        ]);
    }

    public function conversion(Request $request)
    {
        access(["can-owner", "can-host", "can-manager"]);

        if (!$request->has("startDate") && !$request->has("endDate")) {
            return redirect()->route("charts.conversion", [
                "startDate" => week()->beforeWeeks(12, week()->sunday(isodate())),
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

            $masterNames = implode(",", $team->masters->pluck("name")->toArray());

            $chats->push([
                "info" => [
                    "team_id" => $team->id
                ],
                "title" => [
                    "text" => $team->title,
                    "useHTML" => true
                ],
                "subtitle" => [
                    "text" => $masterNames
                ],
                "xAxis" => [
                    "categories" => $datesCollection
                        ->map(function ($date) {
                            return date("d M", strtotime($date));
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
