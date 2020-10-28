<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        //
    }

    public function saveMany(Request $request)
    {
        access(["can-manager"]);

        $data = $request->validate([
            "contacts" => "required|array",
            "contacts.*.team" => "required|array"
        ]);

        foreach ($data["contacts"] as $contactId => $teamData) {
            $contact = Contact::find($contactId);

            if (empty($contact)) return back()->with(["error" => "Контакт не найден"]);

            $teams = json_decode($contact->teams, true);

            foreach ($teamData as $items) {
                foreach ($items as $teamId => $amount) {
                    foreach ($teams as $key => $team) {
                        if ($team["team_id"] == $teamId) {
                            $teams[$key]["amount"] = intval($amount["amount"]);
                        }
                    }
                }
            }

            $contact->update([
                "teams" => json_encode($teams)
            ]);
        }

        return back()->with(["success" => __("common.saved-success")]);
    }
}
