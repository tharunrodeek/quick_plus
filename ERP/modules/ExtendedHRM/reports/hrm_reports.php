<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_REPORTS';
$path_to_root="../../..";

include($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/ExtendedHRM/reports/reports_classes.inc");

$version_id = get_company_prefs('version_id');

$js = '';
if($version_id['version_id'] == '2.4.1'){
	if ($SysPrefs->use_popup_windows) 
		$js .= get_js_open_window(900, 500);	

	if (user_use_date_picker()) 
		$js .= get_js_date_picker();
	
}else{
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
}

add_js_file('reports.js');

page(trans("Reports and Analysis"), false, false, "", $js);

check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));

$reports = new BoxReports;

$dim = get_company_pref('use_dimension');

$reports->addReportClass(trans("HRM & Payroll"), RC_EMPLOYEES);
$reports->addReportClass(trans("Allowances"), RC_ALLOWANCES);
$reports->addReport(RC_EMPLOYEES, 801, trans("Employees"),
	array( 				
			trans("Department") => 'DEPARTMENTS',
			trans("Comments") => 'TEXTBOX',
			trans("Orientation") => 'ORIENTATION',
			trans("Destination") => 'DESTINATION'));

$reports->addReport(RC_EMPLOYEES, 802, trans("Payslip"),
	array(	trans("Year") => 'KV_TRANS_YEARS',
			trans("Months") => 'MONTHS',
			trans("Employee") => 'KV_EMPLOYEES',
			trans("Comments") => 'TEXTBOX',
			trans("E-mail")  => 'YES_NO'));
$reports->addReport(RC_EMPLOYEES, 809, trans("Bulk Payslips"),
	array(	trans("Year") => 'KV_TRANS_YEARS',
			trans("Months") => 'MONTHS',
			trans("Department") => 'DEPARTMENTS',
			trans("Comments") => 'TEXTBOX') );
$reports->addReport(RC_EMPLOYEES, 803, trans("Payroll History"),
	array(	trans("Year") => 'KV_TRANS_YEARS',
			trans("Months") => 'MONTHS',
			trans("Department") => 'DEPARTMENTS',
			trans("Comments") => 'TEXTBOX',
			trans("Destination") => 'DESTINATION'));

$reports->addReport(RC_EMPLOYEES, 804, trans("Attendance"),
	array(	trans("Year") => 'KV_TRANS_YEARS',
			trans("Months") => 'MONTHS',
			trans("Department") => 'DEPARTMENTS',
			trans("Comments") => 'TEXTBOX',
			trans("Destination") => 'DESTINATION'));

$reports->addReport(RC_EMPLOYEES, 805, trans("Loan"),
	array(	trans("Employee") => 'KV_EMPLOYEES',
			trans("Comments") => 'TEXTBOX'));

$reports->addReport(RC_EMPLOYEES, 806, trans("Monthly Summary"),
	array(	trans("Year") => 'KV_TRANS_YEARS',
			trans("Months") => 'MONTHS',
			trans("Result") => 'KV_PDF_STATEMENT',
			trans("Comments") => 'TEXTBOX'));

$reports->addReport(RC_EMPLOYEES, 808, trans("Annual Summary"),
	array(	trans("Year") => 'KV_TRANS_YEARS',
			trans("Result") => 'KV_PDF_STATEMENT',
			trans("Comments") => 'TEXTBOX'));
			
$reports->addReport(RC_EMPLOYEES, 811, trans("Leave Encashment"),
	array(	trans("Employee") => 'KV_EMPLOYEES',
			trans("Year") => 'KV_TRANS_YEARS',
			trans("Comments") => 'TEXTBOX'));
$reports->addReport(RC_EMPLOYEES, 812, trans("Employee Details"),
	array(	trans("Year") => 'KV_TRANS_YEARS',
			trans("Employee") => 'KV_ALL_EMPLOYEES' ,
			trans("Destination") => 'DESTINATION'));
			
$reports->addReport(RC_EMPLOYEES, 813, trans("End of Service"),
	array(	trans("Employee") => 'KV_INACTIVE_EMPLOYEES',
			/*trans("Orientation") => 'ORIENTATION',*/
			trans("Comments") => 'TEXTBOX'));
			
/*$reports->addReport(RC_EMPLOYEES, 6, trans("Professional Tax"),
	array(	trans("Year") => 'KV_TRANS_YEARS',
			trans("Months") => 'MONTHS',
			trans("Comments") => 'TEXTBOX'));	*/
$result = get_allowances();	

while ($myrow = db_fetch($result)) {	
	if($myrow['Tax'] != 1) {
		$reports->addReport(RC_ALLOWANCES, $myrow['id'], trans($myrow['description']),
		array(	trans("Year") => 'KV_TRANS_YEARS',
			trans("Months") => 'MONTHS',
			trans("Comments") => 'TEXTBOX'));
	}
}		
add_custom_reports($reports);

echo $reports->getDisplay(); 
end_page(); ?>
