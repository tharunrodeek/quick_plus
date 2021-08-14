<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HRM_SHIFT';
$path_to_root="../../..";
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );


page(trans("Company Shifts"), @$_REQUEST['popup']);
simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){

	$input_error = 0;

	if (strlen($_POST['description']) == 0) {
		$input_error = 1;
		display_error(trans("The Shift description cannot be empty."));
		set_focus('description');
	}	

    

	$BeginTime = date('H:i:s', strtotime($_POST['BeginTime_hour'].':'.str_pad($_POST['BeginTime_min'], 2, '0', STR_PAD_LEFT).' '.$_POST['BeginTime_ampm']));
		$_POST['BeginTime'] = $BeginTime;
		$EndTime = date('H:i:s', strtotime($_POST['EndTime_hour'].':'.str_pad($_POST['EndTime_min'], 2, '0', STR_PAD_LEFT).' '.$_POST['EndTime_ampm']));
		$_POST['EndTime'] = $EndTime;


/*	if(strtotime($_POST['BeginTime']) > strtotime($_POST['EndTime'])){
		$input_error = 1;
		display_error(trans("The Shift Begin time is Greater than end time"));
		set_focus('BeginTime');
	}*/

	if ($input_error != 1)	{		

    	if ($selected_id != -1)     	{
    		Update('kv_empl_shifts', array('id' => $selected_id), array('description'=>$_POST['description'], 'BeginTime' => $_POST['BeginTime'], 'EndTime' => $_POST['EndTime'], 'dimension' => $_POST['dimension_id']
    			,'shift_color' => $_POST['shift_color']));
			$note = trans("Selected Shift has been updated");
    	}     	else     	{
    		Insert('kv_empl_shifts', array('description' => $_POST['description'], 'BeginTime' => $_POST['BeginTime'], 'EndTime' => $_POST['EndTime'], 'dimension' => $_POST['dimension_id']
    			,'shift_color' => $_POST['shift_color']));
			$note = trans("New Shift has been added");
    	}    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete'){
	$cancel_delete = 0;
	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'
	$sql="select id from 0_kv_empl_shiftdetails where shift_id='".$selected_id."' ";
	//display_error($sql);
	$res=db_query($sql);
	$shift_id=db_fetch($res);

	if ($shift_id['id']!='')	{
		$cancel_delete = 1;
		display_error(trans("Cannot delete this Shift because Employees have been created using this Shift."));
	} 
	if ($cancel_delete == 0) {
		Delete('kv_empl_shifts', array('id' => $selected_id));
		display_notification(trans("Selected Shift has been deleted"));
	} //end if Delete Shift
	$Mode = 'RESET';
} 

if ($Mode == 'RESET'){
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}
//-------------------------------------------------------------------------------------------------



$dim = get_company_pref('use_dimension'); 
if(check_value('show_inactive'))
	$result = GetAll('kv_empl_shifts', array('inactive' => check_value('show_inactive')));
else
	$result = GetAll('kv_empl_shifts');

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(trans("ID"), trans("Description"), trans("Begin Time"), trans("End Time"), trans("Shift Color"),  "", "");
inactive_control_column($th);
table_header($th);
$k = 0; 
$style='';
 foreach($result as $myrow) {

  $style='style="background-color:'.$myrow["shift_color"].';width:50px;height:30px;margin-left: 37%;"';


	alt_table_row_color($k);		
	label_cell($myrow["id"]);
	label_cell($myrow["description"]);
	label_cell(date('h:i:s a', strtotime($myrow["BeginTime"])).' ');
	label_cell(date('h:i:s a', strtotime($myrow["EndTime"])).' ');
	echo '<td><div '.$style.'></div></td>';
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'kv_empl_shifts', 'id');
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
		$myrow = GetRow('kv_empl_shifts', array('id' => $selected_id));
		$_POST['description']  = $myrow["description"];
		$_POST['BeginTime']  = $myrow["BeginTime"];
		$_POST['EndTime']  = $myrow["EndTime"];
		$_POST['dimension_id']  = $myrow["dimension"];
	}
	hidden("selected_id", $selected_id);
	label_row(trans("ID"), $myrow["id"]);
} else{
	$_POST['BeginTime'] = $_POST['EndTime']  = '';
}
text_row_ex(trans("Description:"), 'description', 30);
TimeDropDown_row(trans("Begin Time"), 'BeginTime', $_POST['BeginTime']);
TimeDropDown_row(trans("End Time"), 'EndTime', $_POST['EndTime']);
echo '<tr>
           <td style="font-weight:bold;">Choose Shift Color :</td>
           <td><input type="color" name="shift_color" value="'.$myrow["shift_color"].'" /></td>
	  </tr>';
if ($dim >= 1){
	//dimensions_list_row(trans("Dimension")." 1", 'dimension_id', null, true, " ", false, 1);
}
if ($dim < 1)
	hidden('dimension_id', 0);

end_table(1);
br(2);
submit_add_or_update_center($selected_id == -1, '', 'both');
end_form();
end_page();
?>
