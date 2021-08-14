<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Enhanced HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HRM_DESIGNATION';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
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

page(trans("Designations"), @$_REQUEST['popup'], false, "", $js);
 
simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){

	$input_error = 0;

	if (strlen($_POST['description']) == 0) {
		$input_error = 1;
		display_error(trans("The Designation description cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)	{
    	if ($selected_id != -1)     	{
    		Update('kv_empl_designation', array('id' => $selected_id), array('division'=> $_POST['desig_group'],'description' => $_POST['description']));
			$note = trans("Selected Designation has been updated");
    	}   else  	{
    		Insert('kv_empl_designation', array('division'=> $_POST['desig_group'],'description' => $_POST['description']));
			$note = trans("New Designation has been added");
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete'){

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'kv_empl_job', 'desig'))	{
		$cancel_delete = 1;
		display_error(trans("Cannot delete this Designation because employee have been created using this Designation."));
	} 
	if ($cancel_delete == 0) {
		Delete('kv_empl_designation', array('id' => $selected_id));
		display_notification(trans("Selected Designation has been deleted"));
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

$result = GetAll('kv_empl_designation');

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(trans("ID"),trans("Description"), trans("Division"),  "", "");
inactive_control_column($th);

table_header($th);
$k = 0; 

foreach($result as $myrow) {
	
	alt_table_row_color($k);
	$divi_name=getDivnmae($myrow['division']);
	label_cell($myrow["id"]);

	label_cell($myrow["description"]);
	label_cell($divi_name);
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
		$myrow = GetRow('kv_empl_designation', array('id' =>$selected_id));

		$_POST['description']  = $myrow["description"];
	}
	hidden("selected_id", $selected_id);
	label_row(trans("ID"), $myrow["id"]);
}
$get_desintions=get_All_divsions();
echo "<tr><td class=\"label\">Division *:</td><td><span id=\"_desig_group_sel\">
<select id=\"desig_group\" autocomplete=\"off\" name=\"desig_group\" class=\"combo\" title=\"\" _last=\"0\">";
while($row = db_fetch($get_desintions)) {
	echo '<option value="'.$row['id'].'">'.$row['description'].'</option>';
}
echo "</select>
</span>
</td>
</tr>";
text_row_ex(trans("Designation:"), 'description', 30);
end_table(1);
submit_add_or_update_center($selected_id == -1, '', 'both');
end_form();
end_page();
?>
