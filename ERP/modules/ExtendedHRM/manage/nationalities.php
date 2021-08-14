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

page(trans("Nationalities"), @$_REQUEST['popup'], false, "", $js);
 
simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){

	$input_error = 0;

	if (strlen($_POST['description']) == 0) {
		$input_error = 1;
		display_error(trans("The Nationality description cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)	{
    	if ($selected_id != -1)     	{
    		Update('kv_empl_nationalities', array('id' => $selected_id), array('description' => $_POST['description']));    		
			$note = trans('Selected Nationality has been updated');
    	}   else  	{
    		Insert('kv_empl_nationalities', array('description' => $_POST['description']));
			$note = trans('New Nationality has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete'){

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'kv_empl_info', 'nationality'))	{
		$cancel_delete = 1;
		display_error(trans("Cannot delete this Nationality because Employee have been created using this Nationality."));
	} 
	if ($cancel_delete == 0) {
		Delete('kv_empl_nationalities', array('id' => $selected_id));
		display_notification(trans('Selected Nationality has been deleted'));
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
	$sql= "CREATE TABLE IF NOT EXISTS `".TB_PREF."kv_empl_nationalities` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(60) NOT NULL DEFAULT '',
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;" ; 

	return db_query($sql, "Db Table creation failed, Kv Manufacturer table");
}

$result = GetAll('kv_empl_nationalities');

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(trans("ID"), trans("Nationalities"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0; 

foreach($result as $myrow) {
	
	alt_table_row_color($k);
		
	label_cell($myrow["id"]);
	label_cell($myrow["description"]);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'departments', 'id');
 	edit_button_cell("Edit".$myrow["id"], trans("Edit"));
 	delete_button_cell("Delete".$myrow["id"], trans("Delete"));
	end_row();
}

inactive_control_row($th);
end_table(1);

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) {
 	if ($Mode == 'Edit') {
		//editing an existing department
		$myrow = GetRow('kv_empl_nationalities', array('id' =>$selected_id));

		$_POST['description']  = $myrow["description"];
	}
	hidden("selected_id", $selected_id);
	label_row(trans("ID"), $myrow["id"]);
} 

text_row_ex(trans("Nationality Description:"), 'description', 30);
end_table(1);
submit_add_or_update_center($selected_id == -1, '', 'both');
end_form();
end_page();

function  db_has_picktype_exist(){
	$result = db_query("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_nationalities", "Can't Select department table");
	if(!$result) {		
		return  false; 
	} else return true ; 	
}
?>
