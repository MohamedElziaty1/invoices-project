<?php

namespace App\Http\Controllers;

use App\Models\invoice_attachments;
use App\Models\invoices;
use App\Models\invoices_details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoicesDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(invoices_details $invoices_details)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoices=invoices::where('id',$id)->first();
    $details=invoices_details::where('id_Invoice',$id)->get();

        $attachments=invoice_attachments::where('invoice_id',$id)->get();
        return view('invoices.details_invoice',compact('invoices','details','attachments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, invoices_details $invoices_details)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $invoices=invoice_attachments::findOrFail($request->id_file);
$invoices->delete();
Storage::disk('public_uploads')->delete($request->invoice_number.'/'.$request->file_name);
return redirect()->back()->with('delete','تم حذف المرفق بجاح');
    }

public function open_file($invoice_number,$file_name){
    $files = Storage::disk('public_uploads')->path($invoice_number.'/'.$file_name);
    return response()->file($files);


}
    public function get_file($invoice_number,$file_name)

    {
        $contents= Storage::disk('public_uploads')->path($invoice_number.'/'.$file_name);
        return response()->download( $contents);
    }

}
