<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'SA_OPEN';
$path_to_root = "../../..";

include($path_to_root . "/includes/session.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

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

page(trans($help_context = "Leave Form"), @$_REQUEST['popup'], false, "", $js);

//echo $_SESSION["wa_current_user"]->user;

$employee_id = GetSingleValue('users', 'employee_id', array('id' => $_SESSION["wa_current_user"]->user));

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){

	$input_error = 0;

	if (strlen($_POST['reason']) == 0) {
		$input_error = 1;
		display_error(trans("The Reason cannot be empty."));
		set_focus('reason');
	}

	if (strlen($_POST['days']) == 0) {
		$input_error = 1;
		display_error(trans("The Days cannot be empty."));
		set_focus('days');
	}

	if ($input_error != 1)	{
    	if ($selected_id != -1)     	{
    		Update('kv_empl_leave_applied', array('id' => $selected_id), array('reason' => $_POST['reason'], 'leave_type' => $_POST['leave_type'], 'days' => $_POST['days'], 'date' => array($_POST['date'], 'date'), 'year' => $_POST['attendance_year']));
			$note = trans('You have updated the leave and waiting for approval from your employer');
    	}   else  	{
            $edmp_sql="SELECT id FROM " .TB_PREF."kv_empl_info where user_id=".$_SESSION['wa_current_user']->user." ";
            $result_d = db_query($edmp_sql, "Can't get your allowed user details");
            $rowData= db_fetch($result_d);
    		Insert('kv_empl_leave_applied', array('reason' => $_POST['reason'], 'leave_type' => $_POST['leave_type'], 'days' => $_POST['days'], 'date' => array($_POST['date'], 'date'), 'empl_id' => $rowData[0], 'year' => $_POST['attendance_year']));
			$note = trans('You are submitted new Leave, Which is pending for now. Please Wait until it read by Employer');
    	}

		display_notification($note);
		$Mode = 'RESET';
	}
}

if ($Mode == 'Delete'){

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'
	if (GetSingleValue('kv_empl_leave_applied', 'status', array('id' => $selected_id)) == 1)	{
		$cancel_delete = 1;
		display_error(trans("Cannot delete this Leave because It's already Accepted."));
	}
	if ($cancel_delete == 0) {
		Delete('kv_empl_leave_applied', array('id' => $selected_id));
		display_notification(trans('Selected Leave has been deleted'));
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
if(db_has_picktype_exist() == false) {
	$sql= "CREATE TABLE IF NOT EXISTS `".TB_PREF."kv_empl_leave_applied` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(60) NOT NULL DEFAULT '',
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;" ;

	return db_query($sql, "Db Table creation failed, Kv Manufacturer table");
}



start_form();

	start_table();
		kv_fiscalyears_list_row(trans("Fiscal Year:"), 'attendance_year', null, true);
	end_table();
	br();

start_table(TABLESTYLE, "width=30%");
$th = array(trans("Leave Type"), trans("Reason"), trans("Date"), trans("Days"), trans("Status"), "", "");

table_header($th);
$k = 0;
$edmp_sql="SELECT id FROM " .TB_PREF."kv_empl_info where user_id=".$_SESSION['wa_current_user']->user." ";
$result_d = db_query($edmp_sql, "Can't get your allowed user details");
$rowData= db_fetch($result_d);

$result = GetAll('kv_empl_leave_applied', array('empl_id' => $rowData[0], 'year' => get_post('attendance_year')));
foreach($result as $myrow) {

	alt_table_row_color($k);

	//label_cell($myrow["id"]);
	label_cell($myrow["leave_type"]);
	label_cell($myrow["reason"]);
	label_cell($myrow["date"]);
	label_cell($myrow["days"]);
	//label_cell($myrow["status"]);
	label_cell(($myrow["status"]== 1 ? 'Accepted' :  ($myrow["status"]== 0 ? 'Pending' : 'Rejected')));
 	edit_button_cell("Edit".$myrow["id"], trans("Edit"));
 	delete_button_cell("Delete".$myrow["id"], trans("Delete"));
	end_row();
}


end_table(1);

//-------------------------------------------------------------------------------------------------
start_table(TABLESTYLE2);
table_section_title(trans("Leave Form"));
if ($selected_id != -1) {
 	if ($Mode == 'Edit') { //editing an existing department
		$myrow = GetRow('kv_empl_leave_applied', array('id' =>$selected_id));

		$_POST['leave_type']  = $myrow["leave_type"];
		$_POST['reason']  = $myrow["reason"];
		$_POST['date']  = sql2date($myrow["date"]);
		$_POST['days']  = $myrow["days"];
		$_POST['status']  = $myrow["status"];
	}
	hidden("selected_id", $selected_id);
	label_row(trans("ID"), $myrow["id"]);
}

hrm_empl_leave_type_row(trans("Leave Type"), "leave_type");
date_row(trans("Date") . ":", 'date');
text_row(trans("Days:"), 'days', null, 5, 10);
textarea_row(trans("Reason:"), 'reason', null, 35, 5);
hidden('employee_id', $employee_id);
end_table(1);
submit_add_or_update_center($selected_id == -1, '', 'both');
end_form();
end_page();

function  db_has_picktype_exist(){
	$result = db_query("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_leave_applied", "Can't Select department table");
	if(!$result) {
		return  false;
	} else return true ;
}
?>