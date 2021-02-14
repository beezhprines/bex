<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ResponseCache;

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
        } elseif ($user->isRecruiter()) {
            return redirect()->route("managers.weekplan");
        } elseif ($user->isChiefOperator()) {
            return redirect()->route("operators.salesplan");
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
            abort(403, "Hash is invalid");
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
            abort(403, "Hash is invalid");
        }

        if (!Storage::exists("dshpyrk3_bex_prd_backup.sql")) {
            abort(404, "dshpyrk3_bex_prd_backup.sql not found");
        }

        return Storage::download("dshpyrk3_bex_prd_backup.sql");
    }

    public function cacheClear()
    {
        ResponseCache::clear();
        return back()->with(["success" => "Кэш очищен"]);
    }
}
