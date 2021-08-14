<?php
/**
 * Created by Bipin.
 * User: hp
 * Date: 6/6/2018
 * Time: 4:52 PM
 */
$page_security = 'SA_SALESINVOICE';
$path_to_root = "..";
include_once($path_to_root . "/sales/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");
include_once($path_to_root . "/admin/db/shipping_db.inc");
include_once($path_to_root . "/themes/daxis/kvcodes.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/admin/db/company_db.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
include_once($path_to_root . "/includes/db/crm_contacts_db.inc");


include_once($path_to_root . "/API/API_Call.php");
include_once($path_to_root . "/API/API_HRM_Call.php");
include_once($path_to_root . "/API/API_HRM_Timesheet.php");
include_once($path_to_root . "/API/HRM_Mail.php");
include_once($path_to_root . "/API/AxisProLog.php");
include_once($path_to_root . "/API/Log.php");
//include_once($path_to_root . "/API/API_Salesman.php");
//include_once($path_to_root . "/API/API_InvoiceReport.php");
include_once($path_to_root . "/API/API_HRM_Reports.php");
include_once($path_to_root . "/API/API_HRM_Shift.php");
include_once($path_to_root . "/API/API_Subledger_Report.php");
include_once($path_to_root . "/API/API_HRM_Document_Request.php");
include_once($path_to_root . "/API/API_HRM_Request_Flow.php");



$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : "";


if (empty($method)) {
    echo json_encode(['status' => 'FAIL', 'msg' => 'PARAM_METHOD_EMPTY']);
    exit();
}

$api = new API_Call();
if(method_exists($api,$method)) {
    $api->$method();
}

$api_hrm = new API_HRM_Call();
if(method_exists($api_hrm,$method)) {
    $api_hrm->$method();
}

$api_hrm_timesheet = new API_HRM_Timesheet();
if(method_exists($api_hrm_timesheet,$method)) {
    $api_hrm_timesheet->$method();
}

$api_hrm_mail = new HRM_Mail();
if(method_exists($api_hrm_mail,$method)) {
    $api_hrm_mail->$method();
}

$api_hrm_reports = new API_HRM_Reports();
if(method_exists($api_hrm_reports,$method)) {
    $api_hrm_reports->$method();
}

$api_hrm_shift = new API_HRM_Shift();
if(method_exists($api_hrm_shift,$method)) {
    $api_hrm_shift->$method();
}

$api_subled_report = new API_Subledger_Report();
if(method_exists($api_subled_report,$method)) {
    $api_subled_report->$method();
}

$api_document = new API_HRM_Document_Request();
if(method_exists($api_document,$method)) {
    $api_document->$method();
}


$api_document_request = new API_HRM_Request_Flow();
if(method_exists($api_document_request,$method)) {
    $api_document_request->$method();
}

//$api_sales_man = new API_Salesman();
//if(method_exists($api_sales_man,$method)) {
//    $api_sales_man->$method();
//}

//$api_hrm_mail = new HRM_Mail();
//if(method_exists($api_hrm_mail,$method)) {
//    $api_hrm_mail->$method();
//}

//$api_invoice = new API_InvoiceReport();
//if(method_exists($api_invoice,$method)) {
//    $api_invoice->$method();
//}



