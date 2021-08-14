<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_EMPLOYEE_SETUP';
$path_to_root="../../..";

include_once($path_to_root . "/includes/session.inc");
add_access_extensions();
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

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

page(trans("Gazetted Holidays"));

$selected_id = get_post('selected_id');

if (list_updated('year')) {
	$_POST['year'] = $year = get_post('year');
   	$Ajax->activate('_page_body');	
}


simple_page_mode(true);
//----------------------------------------------------------------------------------------------------

function can_process(){
	if (strlen($_POST['reason']) == 0){
		display_error(trans("The Reason cannot be empty."));
		set_focus('reason');
		return false;
	}

	if (isset($_POST['selected_id']) && $_POST['selected_id'] ==-1 && key_in_foreign_table(date2sql($_POST['date']), 'kv_empl_gazetted_holidays', 'date')){
		display_error(trans("Date Already exist in Holiday."));
		set_focus('date');
		return false;
	}
	return true;
}

//----------------------------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' && can_process()){
	Insert('kv_empl_gazetted_holidays', array('date' => array($_POST['date'], 'date'), 'year' => $_POST['year'], 'reason' => $_POST['reason'], 'inactive' =>(isset($_POST['inactive'])  && $_POST['inactive'] != '' ? $_POST['inactive']: 0 )));
	display_notification(trans("New Holiday has been added"));
	$Mode = 'RESET';
}

//----------------------------------------------------------------------------------------------------
if ($Mode=='UPDATE_ITEM' && can_process()){
	Update('kv_empl_gazetted_holidays', array('id' => $selected_id), array('date' => array($_POST['date'], 'date'), 'reason' => $_POST['reason'], 'year' => $_POST['year'], 'inactive' => (isset($_POST['inactive'])  && $_POST['inactive'] != '' ? $_POST['inactive']: 0 )));
	
	display_notification(trans("Selected Holiday has been updated"));
	$Mode = 'RESET';
}

//----------------------------------------------------------------------------------------------------
if ($Mode == 'Delete'){

	Delete('kv_empl_gazetted_holidays',array('id' => $selected_id));
	display_notification(trans("Selected Holiday has been deleted"));
		
	$Mode = 'RESET';
}

if ($Mode == 'RESET'){
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}
//----------------------------------------------------------------------------------------------------

 //check_value('show_inactive'));

start_form();

start_table(TABLESTYLE_NOBORDER);
	start_row();	
		kv_fiscalyears_list_cells(trans("Fiscal Year:"), 'year', null, true);
	end_row();	
	end_table();

	if (get_post('_show_inactive_update')) {
		$Ajax->activate('year');
		set_focus('year');
	}
$result = GetAll('kv_empl_gazetted_holidays', array('year' => (get_post('year') > 0 ? get_post('year') : 0 )));
start_table(TABLESTYLE, "width=30%");

$th = array (trans("Date"), trans("Reason"), '','');
inactive_control_column($th);
table_header($th);

foreach($result as $myrow){
	
	label_cell(sql2date($myrow["date"]));
	
	label_cell($myrow["reason"]);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'kv_empl_gazetted_holidays', 'id');
 	edit_button_cell("Edit".$myrow['id'], trans("Edit"));
 	delete_button_cell("Delete".$myrow['id'], trans("Delete"));
	end_row();
}
inactive_control_row($th);
end_table();
br(2); 
//----------------------------------------------------------------------------------------------------
start_table(TABLESTYLE2);
table_section_title(trans("Gazetted Holidays"));
if ($selected_id != -1){

 	if ($Mode == 'Edit') {
		$myrow = GetRow('kv_empl_gazetted_holidays', array('id' => $selected_id));

		$_POST['date']  = sql2date($myrow["date"]);
		$_POST['reason']  = $myrow["reason"];
		$_POST['inactive']  = $myrow["inactive"];
	}
	hidden('selected_id', $selected_id);
	//hidden('year', $myrow['year']);
}else{
	$_POST['date']  = $_POST['reason']  = $_POST['inactive']  = '';
	hidden('selected_id', -1);
	//hidden('year', get_post('year'));
}

date_row(trans("Date") . ":", 'date', 1);
//text_row(trans("Number of Days:"), 'no_of_days', $_POST['no_of_days'], 3, 8);
text_row(trans("Reason:"), 'reason', $_POST['reason'], 40, 80);
check_row(trans("Inactive").':', 'inactive', $_POST['inactive']);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();

?>
