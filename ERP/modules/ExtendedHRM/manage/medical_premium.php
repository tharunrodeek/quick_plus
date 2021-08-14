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

page(trans("Medical Premium"), @$_REQUEST['popup'], false, "", $js);
 
$dec = user_price_dec();
simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){
	$input_error = 0;
	if (strlen($_POST['yrly_premium']) == 0 || !is_numeric($_POST['yrly_premium'])) {
		$input_error = 1;
		display_error(trans("The Medical Premium amount cannot be empty or not string."));
		set_focus('amount');
	}

	if ($input_error != 1)	{
    	if ($selected_id != -1)     	{
    		Update('kv_empl_medical_premium', array('id' => $selected_id), array('description'  => $_POST["description"], 'yrly_premium'  => $_POST["yrly_premium"],	'self'  => 1,	'family'  => $_POST["family"],	'family_amt'  => $_POST["family_amt"], 'total'  => $_POST["total"], 'month'  => $_POST['month']));    		
			$note = trans("Selected Medical Premium has been updated");
    	}   else  	{
    		Insert('kv_empl_medical_premium', array('description'  => $_POST["description"], 'yrly_premium'  => $_POST["yrly_premium"],	'self'  => 1,	'family'  => $_POST["family"],	'family_amt'  => $_POST["family_amt"],	'total'  => $_POST["total"], 'month'  => $_POST['month']));
			$note = trans("New Medical Premium has been added");
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
		display_error(trans("Cannot delete this Medical Premium because Employee have been created using this Medical Premium."));
	} */
	if ($cancel_delete == 0) {
		Delete('kv_empl_medical_premium', array('id' => $selected_id));
		display_notification(trans("Selected Medical Premium has been deleted"));
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
$result = GetAll('kv_empl_medical_premium');
start_form();
start_table(TABLESTYLE, "width=50%");
$th = array(trans("ID"), trans("Medical Category"), trans("Yearly Premium"), trans("Self"), trans("Family"), trans("Family Amount"), trans("Total"), trans("Month"), "", "");
table_header($th);
$k = 0; 
foreach($result as $myrow) {	
	alt_table_row_color($k);		
	label_cell($myrow["id"]);
	label_cell($myrow["description"] );
	label_cell($myrow["yrly_premium"]);
	label_cell($myrow["self"]);
	label_cell($myrow["family"]);
	label_cell($myrow["family_amt"]);
	label_cell($myrow["total"]);
	label_cell(number_format2($myrow["month"], $dec));
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
		$myrow = GetRow('kv_empl_medical_premium', array('id' =>$selected_id));
		$_POST['description']  = $myrow["description"];
		$_POST['yrly_premium']  = $myrow["yrly_premium"];
		$_POST['self']  = $myrow["self"];
		$_POST['family']  = $myrow["family"];
		$_POST['family_amt']  = $myrow["family_amt"];
		$_POST['total']  = $myrow["total"];
		$_POST['month']  = $myrow["month"];
	}
	hidden("selected_id", $selected_id);
	label_row(trans("ID"), $myrow["id"]);
} 
text_row_ex(trans("Description").":", 'description', 50);
text_row_ex(trans("Medical Premium :"), 'yrly_premium', 10);

label_row(trans("Self"), 1);
kv_empl_number_list_row(trans("Family :"), 'family', null, 0, 20, true);
text_row_ex(trans("Family Single Person Amount :"), 'family_amt', 10);
submit_row('RefreshInquiry', trans("Refresh"),'',trans("Show Results"), 'default');

$_POST['total'] = (get_post('yrly_premium'))+(get_post('family_amt')*get_post('family')); 
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