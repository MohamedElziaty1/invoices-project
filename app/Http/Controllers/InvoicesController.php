<?php

namespace App\Http\Controllers;

use App\Exports\InvoicesExport;
use App\Models\invoice_attachments;
use App\Models\invoices;
use App\Models\invoices_details;
use App\Models\sections;
use App\Models\User;
use App\Notifications\AddInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
$invoices=invoices::all();
        return view('invoices.invoices',compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections=sections::all();
        return view('invoices.add_invoice',compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        invoices::create([
'invoice_number'=>$request->input('invoice_number'),
            'invoice_Date' => $request->input('invoice_Date'),
            'Due_date' => $request->input('Due_date'),
            'product' => $request->input('product'),
            'section_id' => $request->Section,
            'Amount_collection' => $request->input('Amount_collection'),
            'Amount_Commission' => $request->input('Amount_Commission'),
            'Discount' => $request->input('Discount'),
            'Value_VAT' => $request->input('Value_VAT'),
            'Rate_VAT' => $request->input('Rate_VAT'),
            'Total' => $request->input('Total'),
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->input('note'),
        ]);
        $invoice_id=invoices::latest()->first()->id;
invoices_details::create([
'id_Invoice'=>$invoice_id,
    'invoice_number'=>$request->input('invoice_number'),
    'product'=>$request->input('product'),
    'Section'=>$request->Section,
    'Status'=>'غير مدفوعة',
    'Value_Status'=>2,
    'note'=>$request->input('note'),
    'user'=>(Auth::user()->name),
]);
if($request->hasFile('pic')){

    $invoice_id=invoices::latest()->first()->id;
    $image=$request->file('pic');
    $file_name=$image->getClientOriginalName();
$invoice_number=$request->invoice_number;
$attachments=new invoice_attachments();
$attachments->file_name=$file_name;
$attachments->invoice_number=$invoice_number;
$attachments->Created_by=Auth::user()->name;
$attachments->invoice_id=$invoice_id;
$attachments->save();

$imageName=$request->pic->getClientOriginalName();
$request->pic->move(public_path('Attachments/'.$invoice_number),$imageName);

}
//$user=User::first();
//$user->notify(new AddInvoice($invoice_id));

//        Notification::send($user,new AddInvoice($invoice_id));

        $user = User::get();
        $invoices = invoices::latest()->first();
        Notification::send($user, new \App\Notifications\Add_invoices_new($invoices));




return redirect()->back()->with('success','تم اضافة الفاتورة بنجاح');

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $invoices=invoices::where('id',$id)->first();
        return view('invoices.status_update',compact('invoices'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoices=invoices::where('id',$id)->first();
        $sections=sections::all();
        return view('invoices.edit_invoice',compact('invoices','sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, invoices $invoices)
    {
        $inovices=invoices::findOrFail($request->invoice_id);
        $invoices->update([
'invoice_number'=>$request->input('inovice_number'),
        'invoice_Date'=>$request->input('invoice_Date'),
            'Due_date'=>$request->input('Due_date'),
'product'=>$request->input('product'),
            'section_id'=>$request->Section,
            'Amount_collection'=>$request->input('Amount_collection'),
            'Amount_Commission'=>$request->input('Amount_Commission'),
            'Discount'=>$request->input('Discount'),
            'Value_VAT'=>$request->input('Value_VAT'),
            'Rate_VAT'=>$request->input('Rate_VAT'),
            'Total'=>$request->input('Total'),
            'note'=>$request->input('note'),
      ]);
        return redirect()->back()->with('success','تم تعديل الفاتورة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = invoices::where('id', $id)->first();
        $Details = invoice_attachments::where('id', $id)->first();
        $id_page = $request->id_page;
if(!$id_page==2){
        if (!empty($Details->invoice_number)) {
            Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);
        }
        $invoices->forceDelete();
        return redirect()->back()->with('success', 'تم حذف المرفق بنجاح');
    }
    else{
        $invoices->delete();
session()->flash('archive_invoice');
return redirect('/Archive');

    }
    }

    public function getproducts ($id){
        $products=DB::table("products")->where('section_id',$id)->pluck("Product_name","id");
return json_encode($products);

    }

    public function Status_Update($id,Request $request){
$invoices=invoices::findOrFail($id);

if($request->Status ==='مدفوعة'){
    $invoices->update([
        'Value_Status'=>1,
        'Status'=>$request->Status,
        'Payment_Date'=>$request->Payment_Date,
    ]);
    invoices_Details::create([
      'id_Inovice'=>$request->invoice_id,
      'invoice_number'=>$request->invoice_number,
      'product'=>$request->product,
      'Section'=>$request->Section,
      'Status'=>$request->Status,
      'Value_Status'=>1,
        'note'=>$request->note,
        'Payment_Date'=>$request->Payment_Date,
        'user'=>(Auth::user()->name),
    ]);

}else{

    $invoices->update([
        'Value_Status'=>3,
        'Status'=>$request->Status,
        'Payment_Date'=>$request->Payment_Date
    ]);
    invoices_Details::create([
        'id_Invoice' => $request->invoice_id,
        'invoice_number' => $request->invoice_number,
        'product' => $request->product,
        'Section' => $request->Section,
        'Status' => $request->Status,
        'Value_Status' => 3,
        'note' => $request->note,
        'Payment_Date' => $request->Payment_Date,
        'user' => (Auth::user()->name),
    ]);

}
return redirect('/invoices')->with('success','تم تعديل الفاتورة بنجاح');

    }

    public function Invoice_Paid(){

$invoices=Invoices::where('Value_Status',1)->get();
return view('invoices.invoices_paid',compact('invoices'));

    }

 public function Invoice_UnPaid(){
$invoices=Invoices::where('Value_Status',2)->get();
return view('invoices.invoices_unpaid',compact('invoices'));
    }

    public function Invoice_Partial(){
$invoices=Invoices::where('Value_Status',3)->get();
return view('invoices.invoices_Partial',compact('invoices'));

    }
    public function Print_invoice($id){
        $invoices=invoices::where('id',$id)->first();
return view('invoices.Print_invoice',compact('invoices'));

    }
    public function export()
    {
        return Excel::download(new InvoicesExport, 'users.xlsx');
    }

    public function MarkAsRead_all(Request $request){
$userUnreadNotification=auth()->user()->unreadNotifications;
if($userUnreadNotification){
    $userUnreadNotification->markAsRead();
    return back();
}


    }

}
