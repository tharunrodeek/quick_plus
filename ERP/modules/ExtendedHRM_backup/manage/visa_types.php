<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Enhanced HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'SA_OPEN';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
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
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

page(trans("Visa And Immgiration Expenses"), @$_REQUEST['popup'], false, "", $js);
$dec = user_price_dec();
simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){
	$input_error = 0;
	if ($input_error != 1)	{
    	if ($selected_id != -1)     	{
    		Update('kv_empl_visa_exp', array('id' => $selected_id), array('nationality'  => $_POST["nationality"], 'self_amt'  => $_POST["self_amt"],	'self'  => 1,	'family'  => $_POST["family"], 'family_amt'  => $_POST["family_amt"],	'total'  => $_POST["total"], 'month'  => $_POST['month']));    		
			$note = trans("Selected Visa And Immgiration Expense has been updated");
    	}   else  	{
    		Insert('kv_empl_visa_exp',  array('nationality'  => $_POST["nationality"], 'self_amt'  => $_POST["self_amt"],	'self'  => 1,	'family'  => $_POST["family"], 'family_amt'  => $_POST["family_amt"],	'total'  => $_POST["total"], 'month'  => $_POST['month']));
			$note = trans("New Visa And Immgiration Expense has been added");
    	}    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete'){
	$cancel_delete = 0;
	if ($cancel_delete == 0) {
		Delete('kv_empl_visa_exp', array('id' => $selected_id));
		display_notification(trans("Selected Visa And Immgiration Expense has been deleted"));
	} 
	$Mode = 'RESET';
} 

if ($Mode == 'RESET'){
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}
//-------------------------------------------------------------------------------------------------
$result = GetAll('kv_empl_visa_exp');

start_form();
start_table(TABLESTYLE, "width=50%");
$th = array(trans("ID"), trans("Nationality"), trans("Self"), trans("Family"), trans("Family Amount"), trans("Total"), trans("Month"), "", "");

table_header($th);
$k = 0; 

foreach($result as $myrow) {	
	alt_table_row_color($k);		
	label_cell($myrow["id"]);
	label_cell(($myrow["nationality"] != '0' ? GetSingleValue('kv_empl_nationalities', 'description', array('id' => $myrow["nationality"])): 'All Nationalities'));
	label_cell($myrow["self"]);
	label_cell($myrow["family"]);
	label_cell($myrow["family_amt"]);
	label_cell($myrow["total"]);
	label_cell($myrow["month"]);
 	edit_button_cell("Edit".$myrow["id"], trans("Edit"));
 	delete_button_cell("Delete".$myrow["id"], trans("Delete"));
	end_row();
}

//inactive_control_row($th);
end_table(1);

//-------------------------------------------------------------------------------------------------
div_start('Medical_table');
start_table(TABLESTYLE2);

if ($selected_id != -1) {
 	if ($Mode == 'Edit') {
		//editing an existing department
		$myrow = GetRow('kv_empl_visa_exp', array('id' =>$selected_id));

		$_POST['nationality']  = $myrow["nationality"];
		$_POST['self']  = $myrow["self"];
		$_POST['self_amt']  = $myrow["self_amt"];
		$_POST['family']  = $myrow["family"];
		$_POST['family_amt']  = $myrow["family_amt"];
		$_POST['total']  = $myrow["total"];
		$_POST['month']  = $myrow["month"];
	}
	hidden("selected_id", $selected_id);
	label_row(trans("ID"), $myrow["id"]);
} 
hrm_empl_nationality_row( trans("Nationality :"), 'nationality', null, true);

label_row(trans("Self"), 1);
text_row_ex(trans("Self Amount :"), 'self_amt', 10);
kv_empl_number_list_row(trans("Family :"), 'family', null, 0, 20, true);
text_row_ex(trans("Family Single Person Amount :"), 'family_amt', 10);
submit_row('RefreshInquiry', trans("Refresh"),'',trans("Show Results"), 'default');

$_POST['total'] = (get_post('self_amt'))+(get_post('family_amt')*get_post('family')); 
$_POST['month'] = $_POST['total']/12; 
$_POST['month'] = number_format2($_POST["month"], $dec);
text_row_ex(trans("Total :"), 'total', 10);
text_row_ex(trans("Per Month :"), 'month', 10);

end_table(1);
div_end();

submit_add_or_update_center($selected_id == -1, '', 'both');
end_form();
end_page();

if(list_updated('family') || get_post('RefreshInquiry')) {	  
	$Ajax->activate('Medical_table');
}
?>