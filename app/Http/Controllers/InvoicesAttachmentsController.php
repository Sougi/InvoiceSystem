<?php

namespace App\Http\Controllers;

use App\Models\invoices_attachments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class InvoicesAttachmentsController extends Controller
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
        $this->validate($request , [
            'file_name' => 'mimes:png,jpg,jpng,pdf' , 
        ] , 

      [  'file_name.mimes' => ' صيغة المرفق يجب ان تكون  png,jpg,jpng,pdf' ]);


            $image = $request->file('file_name') ; 
            $fullname = $image->getClientOriginalName() ; 


            $atta = new invoices_attachments() ; 

            $atta->file_name = $fullname ;
            $atta->invoice_number = $request->invoice_number ; 
            $atta->invoice_id = $request->invoice_id ; 
            $atta->created_by = Auth::user()->name; 

            $atta->save();

            //move pic and pdf 

            $image_name = $request->file_name->getClientOriginalName() ; 
            $request->file_name->move(public_path('Attachments/'  . $request->invoice_number) , $image_name);


            session()->flash('Add' , 'تم اضافة المرفق بنجاح' ) ; 
            return back();






    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\invoices_attachments  $invoices_attachments
     * @return \Illuminate\Http\Response
     */
    public function show(invoices_attachments $invoices_attachments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\invoices_attachments  $invoices_attachments
     * @return \Illuminate\Http\Response
     */
    public function edit(invoices_attachments $invoices_attachments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\invoices_attachments  $invoices_attachments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, invoices_attachments $invoices_attachments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\invoices_attachments  $invoices_attachments
     * @return \Illuminate\Http\Response
     */
    public function destroy(invoices_attachments $invoices_attachments)
    {
        //
    }
}
