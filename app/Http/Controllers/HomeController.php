<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

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

        return view('dashboard');
    }

    public function calendar(Request $request)
    {
        $data = $request->validate([
            'startDate' => 'required|date_format:Y-m-d',
            'endDate' => 'required|date_format:Y-m-d',
        ]);

        $start = Carbon::parse($data['startDate']);
        $end = Carbon::parse($data['endDate']);

        week()->set(
            $start->format(config('app.iso_date')),
            $end->format(config('app.iso_date'))
        );

        return back();
    }

    public function denied()
    {
        return view("shared.denied");
    }
}
