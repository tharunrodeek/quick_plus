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

page(trans("Leave Travel/Passages"), @$_REQUEST['popup'], false, "", $js);
$dec = user_price_dec();

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){

	$input_error = 0;

	/*if (strlen($_POST['yrly_premium']) == 0 || !is_numeric($_POST['yrly_premium'])) {
		$input_error = 1;
		display_error(trans("The Visa And Immgiration Expense amount cannot be empty or not string."));
		set_focus('amount');
	}*/

	if ($input_error != 1)	{
    	if ($selected_id != -1)     	{
    		Update('kv_empl_leave_travel', array('id' => $selected_id), array('nationality'  => $_POST["nationality"], 'amount'  => $_POST["amount"],	'self'  => 1,	'family'  => $_POST["family"], 'family_amt'  => $_POST["family_amt"], 'eligibility'  => $_POST["eligibility"],	'ticket_per_yr'  => $_POST["ticket_per_yr"], 'month'  => $_POST['month']));    		
			$note = trans("Selected Visa And Immgiration Expense has been updated");
    	}   else  	{
    		Insert('kv_empl_leave_travel',  array('nationality'  => $_POST["nationality"], 'amount'  => $_POST["amount"],	'self'  => 1,	'family'  => $_POST["family"], 'family_amt'  => $_POST["family_amt"], 'eligibility'  => $_POST["eligibility"],	'ticket_per_yr'  => $_POST["ticket_per_yr"], 'month'  => $_POST['month']));
			$note = trans("New Visa And Immgiration Expense has been added");
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete'){

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	/*if (key_in_foreign_table($selected_id, 'kv_empl_info', 'nationality'))	{
		$cancel_delete = 1;
		display_error(trans("Cannot delete this Visa And Immgiration Expense because Employee have been created using this Visa And Immgiration Expense."));
	} */
	if ($cancel_delete == 0) {
		Delete('kv_empl_leave_travel', array('id' => $selected_id));
		display_notification(trans("Selected Visa And Immgiration Expense has been deleted"));
	} //end if Delete department
	$Mode = 'RESET';
} 

if ($Mode == 'RESET'){
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}
//-------------------------------------------------------------------------------------------------
$result = GetAll('kv_empl_leave_travel');

start_form();
start_table(TABLESTYLE, "width=50%");
$th = array(trans("ID"), trans("Nationality"), trans("Amount"), trans("Self"), trans("Family"), trans("Family Amount"), trans("Total"), trans("Month"), "", "");
//inactive_control_column($th);

table_header($th);
$k = 0; 

foreach($result as $myrow) {
	
	alt_table_row_color($k);
		
	label_cell($myrow["id"]);
	label_cell(($myrow["nationality"] != '0' ? GetSingleValue('kv_empl_nationalities', 'description', array('id' => $myrow["nationality"])): 'All Nationalities'));
	label_cell(number_format2($myrow["amount"], $dec));
	label_cell($myrow["self"]);
	label_cell($myrow["family"]);
	label_cell($myrow["family_amt"]);
	label_cell($myrow["ticket_per_yr"]);
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
		$myrow = GetRow('kv_empl_leave_travel', array('id' =>$selected_id));

		$_POST['nationality']  = $myrow["nationality"];
		$_POST['self']  = $myrow["self"];
		$_POST['eligibility']  = $myrow["eligibility"];
		$_POST['family']  = $myrow["family"];
		$_POST['family_amt']  = $myrow["family_amt"];
		$_POST['amount']  = $myrow["amount"];
		$_POST['ticket_per_yr']  = $myrow["ticket_per_yr"];
		$_POST['month']  = $myrow["month"];
	}
	hidden("selected_id", $selected_id);
	label_row(trans("ID"), $myrow["id"]);
} 
hrm_empl_nationality_row( trans("Nationality :"), 'nationality', null, true);
yesno_list_row(trans("Eligibility"), 'eligibility', null,  'Biennially', 'Yearly');
label_row(trans("Self"), 1);
text_row_ex(trans("Amount :"), 'amount', 10);

kv_empl_number_list_row(trans("Family :"), 'family', null, 0, 20, true);
text_row_ex(trans("Family Single Person Amount :"), 'family_amt', 10);
submit_row('RefreshInquiry', trans("Refresh"),'',trans("Show Results"), 'default');

$_POST['ticket_per_yr'] = ((get_post('family')*get_post('family_amt')) + get_post('amount'))/(get_post('eligibility') == 1 ? 2 : 1); 
$_POST['month'] = ($_POST['ticket_per_yr'])/12; 
$_POST['month'] = number_format2($_POST["month"], $dec);
text_row_ex(trans("Total :"), 'ticket_per_yr', 10);
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
