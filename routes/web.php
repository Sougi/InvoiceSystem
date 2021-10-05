<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\InvoicesAttachmentsController;
use App\Http\Controllers\InvoiceAchiveController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\invoices_report;

use App\Http\Controllers\Customers_Report
;
















 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();//auth
// Auth::routes(['register' => false]);

// invoices routes 
Route::resource('/invoices' , InvoicesController::class) ; 


//sections routes 
Route::resource('/sections' , SectionsController::class) ; 


//products routes
Route::resource('/products' , ProductsController::class) ; 


// home page
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//get products route
Route::get('/section/{id}' , [InvoicesController::class , 'getProducts']);




//route to invoices details 
Route::get('/InvoicesDetails/{id}' , [InvoicesDetailsController::class , 'index']);


//route to display attachements 
Route::get('View_file/{invoice_number}/{file_name}', [InvoicesDetailsController::class , 'open_file']);


//route to download file 
Route::get('download/{invoice_number}/{file_name}',  [InvoicesDetailsController::class , 'get_file']);


//route to delete fils 
Route::post('delete_file' , [InvoicesDetailsController::class , 'destroy'])->name('delete_file') ; 


//route to attachments invoices 
Route::resource('/InvoiceAttachments', InvoicesAttachmentsController::class);


//edit invoice
Route::get('/edit_invoice/{id}', [InvoicesController::class , 'edit'] );


//route of show status of invoice
Route::get('/Status_show/{id}',  [InvoicesController::class , 'show'] )->name('Status_show');
Route::post('/Status_Update/{id}',  [InvoicesController::class , 'Status_Update'] )->name('Status_Update');



//route of paid and unpaid , partial invoices 

Route::get('Invoice_Paid',[InvoicesController::class , 'Invoice_Paid']);

Route::get('Invoice_UnPaid',[InvoicesController::class , 'Invoice_UnPaid']);

Route::get('Invoice_Partial',[InvoicesController::class , 'Invoice_Partial']);


//route to archive invoices 
Route::resource('Archive', InvoiceAchiveController::class);


//route of impimer invoices  
Route::get('Print_invoice/{id}',[InvoicesController::class , 'Print_invoice']);



//route of export invoices as ecxel
Route::get('export_invoices', [InvoicesController::class , 'export']);



//grouuuuuuuuuuuuup 
Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles',RoleController::class);
    Route::resource('users',UserController::class);
});


// route to enter to search page 
Route::get('invoices_report', [invoices_report::class , 'index']);



// post request to search using query 
Route::post('Search_invoices',  [invoices_report::class , 'Search_invoices']);



// route to enter to search page 
Route::get('customers_report' , [Customers_Report::class , 'index'])->name("customers_report");


// post request to search using query 
Route::post('Search_customers', [Customers_Report::class , 'Search_customers']);






//notification 
        Route::get('MarkAsRead_all',[InvoicesController::class , 'MarkAsRead_all'])->name('MarkAsRead_all');

        Route::get('unreadNotifications_count', [InvoicesController::class , 'unreadNotifications_count'])->name('unreadNotifications_count');

        Route::get('unreadNotifications', [InvoicesController::class , 'unreadNotifications'])->name('unreadNotifications');




//admin template routes
Route::get('/{page}', [AdminController::class , 'index']);


