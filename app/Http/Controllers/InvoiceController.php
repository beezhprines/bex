<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Invoice;
use App\Models\Master;
use Illuminate\Http\Request;

class InvoiceController extends Controller
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
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        note("info", "invoice:delete", "Удален чек", Invoice::class, $invoice->id);

        return back()->with(['success' => __('common.deleted-success')]);
    }

    public function storeMany(Request $request)
    {
        $data = $request->validate([
            'master_id' => 'required|exists:masters,id',
            'budget_id' => 'required|exists:budgets,id',
            'invoices' => 'required',
            'invoices.*' => 'image|max:5120'
        ]);

        if ($request->hasFile('invoices')) {
            foreach ($request->file('invoices') as $file) {
                $base64 = base64_encode(file_get_contents($file));

                $budget = Budget::find($data["budget_id"]);
                $master = Master::find($data["master_id"]);

                $budget->invoices()->create([
                    'file' => $base64
                ]);
            }
        }

        note("info", "invoice:create", "Созданы чеки на бюджет мастером {$master->name}", Budget::class, $budget->id);

        return back()->with(['success' => __('common.saved-success')]);
    }
}
