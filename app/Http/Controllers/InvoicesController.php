<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Notification;

use App\Models\User;

use App\Models\invoices;
use App\Models\sections;
use App\Models\invoices_details;
use App\Models\invoices_attachments;
use App\Notifications\AddInvoice;
use App\Exports\InvoicesExport;



use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices  = invoices::all();
        return view('invoices.invoices' , compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sections = sections::all();
        return view('invoices.add_invoice' , compact('sections'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
        {

                    //insert to invoice table 

            invoices::create([

            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            ]); 


            // insert to details_invoice table at the same time 
            $invoice_id = invoices::latest()->first()->id;
            // var_dump($invoice_id) ; 
            invoices_details::create([
                'id_Invoice' => $invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->section,
                'status' => 'غير مدفوعة',
                'value_Status' => 2,
                'note' => $request->note,
                'user' => (Auth::user()->name),
            ]);


            // insert to attachments table 

            if ($request->hasFile('pic')) {

                $invoice_id = invoices::latest()->first()->id;
                $image = $request->file('pic');
                $file_name = $image->getClientOriginalName();
                $invoice_number = $request->invoice_number;
    
                $attachments = new invoices_attachments();
                $attachments->file_name = $file_name;
                $attachments->invoice_number = $invoice_number;
                $attachments->Created_by = Auth::user()->name;
                $attachments->invoice_id = $invoice_id;
                $attachments->save();
    
                // move pic
                $imageName = $request->pic->getClientOriginalName();
                $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
            }


            //notification when you add invoice 
            // $user = User::first(); 
            // Notification::send($user , new AddInvoice($invoice_id)) ; 



            $user = User::get();
            $invoices = invoices::latest()->first();
            Notification::send($user, new \App\Notifications\add_invoice($invoices));


            session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
            return redirect('/invoices');







        }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoices = invoices::where('id', $id)->first();
        return view('invoices.status_update', compact('invoices'));
    }

    /////////////////////////////////////////////////////////////////////////////
    public function Status_Update($id, Request $request)
    {
        $invoices = invoices::findOrFail($id);

        if ($request->Status === 'مدفوعة') {

            $invoices->update([
                'Value_Status' => 1,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);

            invoices_Details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->section,
                'status' => $request->Status,
                'value_Status' => 1,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }

        else {
            $invoices->update([
                'Value_Status' => 3,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            invoices_Details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->section,
                'status' => $request->Status,
                'value_Status' => 3,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('Status_Update');
        return redirect('/invoices');

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $invoices = invoices::where('id' , $id)->first();
        $sections = sections::all();

        return view('invoices.edit_invoices' , compact('invoices' , 'sections' ));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $invoices = invoices::findOrFail($request->invoice_id);
        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'note' => $request->note,
        ]);

        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {





        $id = $request->invoice_id;
        $invoices = invoices::where('id', $id)->first();
        $Details = invoices_attachments::where('invoice_id', $id)->first();

         $id_page =$request->id_page;


        if (!$id_page==2) {

        if (!empty($Details->invoice_number)) {

            Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);
        }

        $invoices->forceDelete();
        session()->flash('delete_invoice');
        return redirect('/invoices');

        }

        else {

            $invoices->delete();
            session()->flash('archive_invoice');
            return redirect('/Archive');
        }




    }

        //get clear and champoin
        public function getProducts($id) {

        $products = DB::table('products')->where('section_id' , $id)->pluck('product_name' , 'id');

        return json_encode($products);

    }


    // paid methode and athors 
    public function Invoice_Paid()
    {
        $invoices = Invoices::where('Value_Status', 1)->get();
        return view('invoices.invoices_paid',compact('invoices'));
    }

    public function Invoice_unPaid()
    {
        $invoices = Invoices::where('Value_Status',2)->get();
        return view('invoices.invoices_unpaid',compact('invoices'));
    }

    public function Invoice_Partial()
    {
        $invoices = Invoices::where('Value_Status',3)->get();
        return view('invoices.invoices_Partial',compact('invoices'));
    }



    public function Print_invoice($id)
    {
        $invoices = invoices::where('id', $id)->first();
        return view('invoices.Print_invoice',compact('invoices'));
    }


    public function export() 
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }


    public function MarkAsRead_all (Request $request)
    {

        $userUnreadNotification= auth()->user()->unreadNotifications;

        if($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
            return back();
        }


    }

    public function unreadNotifications_count()

    {
        return auth()->user()->unreadNotifications->count();
    }




    public function unreadNotifications()
    {
        foreach (auth()->user()->unreadNotifications as $notification){
        return $notification->data['title'];

        }

    }




}