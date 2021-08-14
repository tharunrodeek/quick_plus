<?php

if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return redirect('invoice_report');
});


/**
 * ROUTE for INVOICE REPORT
 */
Route::group(['prefix'=>'invoice_report','as'=>'invoice_report.'], function(){

    Route::get('/', ['as' => 'list', 'uses' => 'InvoiceReportController@index']);
    Route::get('export/{type}', ['as' => 'export', 'uses' => 'InvoiceReportController@export']);

});

/**
 * ROUTE for SERVICE REPORT
 */
Route::group(['prefix'=>'service_report','as'=>'service_report.'], function(){

    Route::get('/', ['as' => 'list', 'uses' => 'ServiceReportController@index']);
    Route::get('export/{type}', ['as' => 'export', 'uses' => 'ServiceReportController@export']);

});

/**
 * ROUTE for EMPLOYEE COMMISSION REPORT
 */
Route::group(['prefix'=>'employee_commission_report','as'=>'employee_commission_report.'], function(){

    Route::get('/', ['as' => 'list', 'uses' => 'EmployeeCommissionReportController@index']);
    Route::get('export/{type}', ['as' => 'export', 'uses' => 'EmployeeCommissionReportController@export']);

});

/**
 * ROUTE for CUSTOMER COMMISSION REPORT
 */
Route::group(['prefix'=>'customer_commission_report','as'=>'customer_commission_report.'], function(){

    Route::get('/', ['as' => 'list', 'uses' => 'CustomerCommissionReportController@index']);
    Route::get('export/{type}', ['as' => 'export', 'uses' => 'CustomerCommissionReportController@export']);

});

/**
 * ROUTE for VOIDED TRANSACTION REPORT
 */
Route::group(['prefix'=>'voided_trans_report','as'=>'voided_trans_report.'], function(){

    Route::get('/', ['as' => 'list', 'uses' => 'VoidedTransactionReportController@index']);
    Route::get('export/{type}', ['as' => 'export', 'uses' => 'VoidedTransactionReportController@export']);

});

/**
 * ROUTE for SERVICE LIST
 */
Route::group(['prefix'=>'service_list','as'=>'service_list.'], function(){

    Route::get('/', ['as' => 'list', 'uses' => 'ServiceListController@index']);
    Route::get('export/{type}', ['as' => 'export', 'uses' => 'ServiceListController@export']);

});

/**
 * ROUTE for INVOICE PAYMENT REPORT
 */
Route::group(['prefix'=>'invoice_payment_report','as'=>'invoice_payment_report.'], function(){

    Route::get('/', ['as' => 'list', 'uses' => 'InvoicePaymentReportController@index']);
    Route::get('export/{type}', ['as' => 'export', 'uses' => 'InvoicePaymentReportController@export']);

});
