<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    private $githash;

    function __construct()
    {
        $this->githash = env("GIT_HASH");
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $user = User::find(Auth::id());

        if ($user->isMaster()) {
            return redirect()->route("masters.statistics");
        } elseif ($user->isMarketer()) {
            return redirect()->route("marketers.analytics");
        } elseif ($user->isOperator()) {
            return redirect()->route("operators.statistics");
        } elseif ($user->isManager()) {
            return redirect()->route("managers.weekplan");
        } elseif ($user->isOwner() || $user->isHost()) {
            return redirect()->route("managers.weekplan");
        }

        return view("dashboard");
    }

    public function calendar(Request $request)
    {
        $data = $request->validate([
            "startDate" => "required|date_format:Y-m-d",
            "endDate" => "required|date_format:Y-m-d",
        ]);

        $start = Carbon::parse($data["startDate"]);
        $end = Carbon::parse($data["endDate"]);

        week()->set(
            $start->format(config("app.iso_date")),
            $end->format(config("app.iso_date"))
        );

        return back();
    }

    public function denied()
    {
        return view("shared.denied");
    }

    public function pull(Request $request)
    {
        $data = $request->validate([
            "githash" => "required|string",
            "branch" => "required|string|in:staging,master",
        ]);

        if ($data["githash"] != $this->githash) {
            return response()->with(["error" => "Hash is invalid"]);
        }

        $branch = $data["branch"];

        Artisan::call("git:pull --branch={$branch}");

        return response()->json([
            "status" => true
        ]);
    }

    public function dbbackup(Request $request)
    {
        $data = $request->validate([
            "githash" => "required|string"
        ]);

        if ($data["githash"] != $this->githash) {
            return response()->with(["error" => "Hash is invalid"]);
        }

        Artisan::call("db:restore --backup");

        return Storage::exists("dshpyrk3_bex_prd_backup.sql") ? Storage::download("dshpyrk3_bex_prd_backup.sql") : abort(404, "dshpyrk3_bex_prd_backup.sql not found");
    }
}
