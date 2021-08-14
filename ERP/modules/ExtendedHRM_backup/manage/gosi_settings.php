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

page(trans("GOSI Settings"), @$_REQUEST['popup'], false, "", $js);
 
simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){

	$input_error = 0;

	if (strlen($_POST['nationality']) == 0 || !is_numeric($_POST['nationality'])) {
		$input_error = 1;
		display_error(trans("The Nationality cannot be empty."));
		set_focus('nationality');
	}

	if ($input_error != 1)	{		
		$allowances = kv_get_allowances('Earnings');
		$all_arr = [];
		foreach($allowances as $single){
			if(isset($_POST[$single['id']]) && $_POST[$single['id']] == 1)
				$all_arr[] = $single['id'];
		}
		if(!empty($all_arr)){
			$_POST['allowances'] = base64_encode(serialize($all_arr));
		}else 
			$_POST['allowances'] = '';
    	if ($selected_id != -1)     	{
    		Update('kv_empl_gosi_settings', array('id' => $selected_id), array('employee' => $_POST['employee'], 'employer' => $_POST['employer'], 'nationality' => $_POST['nationality'], 'allowances' => $_POST['allowances']));    		
			$note = trans("Selected GOSI Settings has been updated");
    	}   else  	{
    		Insert('kv_empl_gosi_settings', array('employee' => $_POST['employee'], 'employer' => $_POST['employer'], 'nationality' => $_POST['nationality'], 'allowances' => $_POST['allowances']));
			$note = trans("New GOSI Settings has been added");
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
		display_error(trans("Cannot delete this GOSI Settings because Employee have been created using this GOSI Settings."));
	} */
	if ($cancel_delete == 0) {
		Delete('kv_empl_gosi_settings', array('id' => $selected_id));
		display_notification(trans("Selected GOSI Settings has been deleted"));
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
$result = GetAll('kv_empl_gosi_settings');

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(trans("ID"), trans("Nationalities"), trans("Employer"), trans("Employee"), "", "");
//inactive_control_column($th);

table_header($th);
$k = 0; 

foreach($result as $myrow) {	
	alt_table_row_color($k);		
	label_cell($myrow["id"]);
	label_cell(($myrow["nationality"] != '0' ? GetSingleValue('kv_empl_nationalities', 'description', array('id' => $myrow["nationality"])): 'All Nationalities'));
	label_cell($myrow["employer"]);
	label_cell($myrow["employee"]);
	//inactive_control_cell($myrow["id"], $myrow["inactive"], 'departments', 'id');
 	edit_button_cell("Edit".$myrow["id"], trans("Edit"));
 	delete_button_cell("Delete".$myrow["id"], trans("Delete"));
	end_row();
}

//inactive_control_row($th);
end_table(1);

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) {
 	if ($Mode == 'Edit') {
		//editing an existing department
		$myrow = GetRow('kv_empl_gosi_settings', array('id' =>$selected_id));

		$_POST['employee']  = $myrow["employee"];
		$_POST['employer']  = $myrow["employer"];
		$_POST['nationality']  = $myrow["nationality"];
		$_POST['allowances']  = unserialize(base64_decode($myrow["allowances"]));
	}
	hidden("selected_id", $selected_id);
	label_row(trans("ID"), $myrow["id"]);
} 
hrm_empl_nationality_row( trans("Nationality :"), 'nationality', null, true);
text_row_ex(trans("Employer :"), 'employer', 3, null, null, null, null, '%');
text_row_ex(trans("Employee :"), 'employee', 3, null, null, null, null, '%');
$allowances = kv_get_allowances('Earnings');
foreach($allowances as $single){
	unset($_POST[$single['id']]);
	if(isset($_POST['allowances']) && !empty($_POST['allowances']) && in_array($single['id'], $_POST['allowances']))
		$_POST[$single['id']] = 1;
	kv_check_row(trans($single['description']), $single['id'], null);
}
end_table(1);
submit_add_or_update_center($selected_id == -1, '', 'both');
end_form();
end_page();

?>
