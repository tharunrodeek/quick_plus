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
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

page(trans("Document Types"), @$_REQUEST['popup']);
 
simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){

	$input_error = 0;

	if (strlen($_POST['description']) == 0) {
		$input_error = 1;
		display_error(trans("The Document Type description cannot be empty."));
		set_focus('description');
	}
	if (!check_num('days')) {
		$input_error = 1;
		display_error(trans("The Expiry notification is day count, So it should be integer."));
		set_focus('description');
	}

	if ($input_error != 1)	{
    	if ($selected_id != -1)     	{
    		Update('kv_empl_doc_type', array('id' => $selected_id), array('description'=>$_POST['description'], 'days' => input_num('days')));
			$note = trans("Selected Document Type has been updated");
    	}     	else     	{
    		Insert('kv_empl_doc_type', array('description' => $_POST['description'], 'days' => input_num('days')));
			$note = trans("New Document Type has been added");
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete'){

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'kv_empl_job', 'department'))	{
		$cancel_delete = 1;
		display_error(trans("Cannot delete this Document Type because Employees have been created using this Document Type."));
	} 
	if ($cancel_delete == 0) {
		Delete('kv_empl_doc_type', array('id' => $selected_id));
		display_notification(trans("Selected Document Type has been deleted"));
	} //end if Delete Document Type
	$Mode = 'RESET';
} 

if ($Mode == 'RESET'){
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}
//-------------------------------------------------------------------------------------------------


if(check_value('show_inactive'))
	$result = GetAll('kv_empl_doc_type', array('inactive' => check_value('show_inactive')));
else
	$result = GetAll('kv_empl_doc_type');

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(trans("ID"), trans("Document Type"), trans("Notify Before"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0; 

 foreach($result as $myrow) {
	
	alt_table_row_color($k);
		
	label_cell($myrow["id"]);
	label_cell($myrow["description"]);
	label_cell($myrow["days"].' '.trans("Days"));
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
 	if ($Mode == 'Edit') { //editing an existing department
		$myrow = GetRow('kv_empl_doc_type', array('id' => $selected_id));

		$_POST['description']  = $myrow["description"];
		$_POST['days']  = $myrow["days"];
	}
	hidden("selected_id", $selected_id);
	label_row(trans("ID"), $myrow["id"]);
} 

text_row_ex(trans("Document Type:"), 'description', 30);
text_row_ex(trans("Notify Before").":", 'days', 4, 10, '', null, null, trans("Days"));

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
