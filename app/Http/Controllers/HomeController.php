<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
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

        return response()->json(['success' => true]);
    }
}
