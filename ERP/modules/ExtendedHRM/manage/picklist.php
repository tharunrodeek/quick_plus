<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
/*  Pick List for Employment Type, Location work, Mode of Pay
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
 
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/db/picklist.inc" );

simple_page_mode(true);
//----------------------------------------------------------------------------------------------------
if (isset($_GET['type'])){
	$_POST['type'] = $_GET['type'];
	$_POST['title'] = GetSingleValue('kv_empl_pick_type', 'description', array('id' => $_GET['type']));
}
page(trans($_POST['title']));
$type = get_post('type','');


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){

	$input_error = 0;

	if (strlen($_POST['description']) == 0) {
		$input_error = 1;
		display_error(trans("The Picklist Item description cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)	{
    	if ($selected_id != -1)     	{
    		Update('kv_empl_picklist', array('id' => $selected_id), array('type' => $_POST['type'], 'description' => $_POST['description'], 'inactive' => check_value('inactive')));    		
			$note = trans('Selected Picklist Item has been updated');
    	}   else  	{
    		Insert('kv_empl_picklist', array('type' => $_POST['type'], 'description' => $_POST['description'], 'inactive' => check_value('inactive')));
			$note = trans('New Picklist Item has been added');
    	}    
		display_notification($note); 
		$Mode ='RESET';
	}
} 

if ($Mode == 'Delete'){

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'kv_empl_job', 'department'))	{
		$cancel_delete = 1;
		display_error(trans("Cannot delete this Picklist Item because Employees have been created using this Picklist Item."));
	} 
	if ($cancel_delete == 0) {
		Delete('kv_empl_picklist', array('id' => $selected_id));
		display_notification(trans('Selected Picklist Item has been deleted'));
	} //end if DeletePicklist Item
	$Mode = 'RESET';
} 

if ($Mode == 'RESET'){
	set_focus('description');
}
//-------------------------------------------------------------------------------------------------

if(list_updated('type')) {
	$type = get_post('type');   
		$Ajax->activate('totals_tbl');
}else
	$type = 0;

//----------------------------------------------------------------------------------------------------
div_start('totals_tbl');
start_form();

	/*start_table(TABLESTYLE_NOBORDER);			
		echo "<tr><td class='label'>".trans("Pick Type:")."</td><td nowrap>";
		echo hrm_pick_list_type('type', null, true, 1, true);
		echo "</td>\n</tr>\n";
	end_table();
	br(2);	*/
	hidden('type', $_POST['type']);

	start_table(TABLESTYLE, "width=30%");
		$_POST['id'] = $_POST['description'] = $_POST['inactive'] = ''; 
		$th = array (trans('ID'), 'Description',  '','');
		table_header($th);
		$k =  0;
		$type = $_POST['type'];
		if($type > 0 ){
			$result = get_all_picks($type);
			foreach($result as $myrow){			
				label_cell($myrow["id"]);	
				label_cell($myrow["description"]);				
				inactive_control_cell($myrow["id"], $myrow["inactive"], 'kv_empl_picklist', 'id');
			 	edit_button_cell("Edit".$myrow['id'], trans("Edit"));
			 	delete_button_cell("Delete".$myrow['id'], trans("Delete"));
				end_row();
			}	
		} else
			label_cell(trans("No Type selected"), 'colspan = "5" style="text-align: center; "')	;
	end_table();
		br(2); 
	//----------------------------------------------------------------
	start_table(TABLESTYLE2);
		table_section_title(trans("Description Entry"));
		if ($selected_id != -1){

		 	if ($Mode == 'Edit') {
				$myrow = get_pick($selected_id);
				$_POST['description']  = $myrow['description'];
				$_POST['id']  = $myrow['id'];
				$_POST['inactive']  = $myrow['inactive'];
				$type  = $myrow['type'];				
			}
			hidden('selected_id', $selected_id);
		}
		text_row(trans("Description :"), 'description', $_POST['description'], 40, 80);
		kv_check_row(trans("Is This Inactive:"), 'inactive', $_POST['inactive'], false, false);
		//check_row(trans("Inactive"), 'inactive', null);
		//hidden('type', $type);
	end_table(1);
	if(get_post('type') >0)
		submit_add_or_update_center($selected_id == -1, '', 'both');
	else
		display_error(trans("Please Select Pick Type."));
		
end_form();
div_end();
end_page();
?>
<style>
table{ width: auto; } 
</style>