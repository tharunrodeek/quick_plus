<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_EMPLOYEE_SETUP';
$path_to_root="../..";

include($path_to_root . "/includes/session.inc"); 
add_access_extensions();
include($path_to_root . "/includes/ui.inc");
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

page(trans("Grade Setup"), false, false, "", $js);

simple_page_mode(true);
//----------------------------------------------------------------------------------------------------

function can_process(){
	if (strlen($_POST['description']) == 0){
		display_error(trans("The Description cannot be empty."));
		set_focus('description');
		return false;
	}

	/*if (isset($_POST['selected_id']) && $_POST['selected_id'] ==-1 && key_in_foreign_table(date2sql($_POST['date']), 'kv_empl_grade', 'date')){
		display_error(trans("Date Already exist in Holiday."));
		set_focus('date');
		return false;
	}*/
	return true;
}

//----------------------------------------------------------------------------------------------------
if ($Mode=='ADD_ITEM' && can_process()){
	Insert('kv_empl_grade', array('description' => $_POST['description'], 'min_salary' => input_num('min_salary'), 'max_salary' => input_num('max_salary'), 'hl' => $_POST['hl'],'sl' => $_POST['sl'],'slh' => $_POST['slh'], 'al' => $_POST['al'], 'ml' => $_POST['ml'], 'inactive' => ($_POST['inactive']==1 ? $_POST['inactive'] : 0 )));
	display_notification(trans("New Grade has been added"));
	$Mode = 'RESET';
}

//----------------------------------------------------------------------------------------------------
if ($Mode=='UPDATE_ITEM' && can_process()){
	Update('kv_empl_grade', array('id' => $selected_id), array('description' => $_POST['description'], 'min_salary' => input_num('min_salary'), 'max_salary' => input_num('max_salary'),'sl' => $_POST['sl'],'slh' => $_POST['slh'], 'hl' => $_POST['hl'], 'al' => $_POST['al'], 'ml' => $_POST['ml'], 'inactive' => ($_POST['inactive'] == 1 ? $_POST['inactive'] : 0 )));
	
	display_notification(trans("Selected Grade has been updated"));
	$Mode = 'RESET';
}

//----------------------------------------------------------------------------------------------------
if ($Mode == 'Delete'){

	Delete('kv_empl_grade',array('id' => $selected_id));
	display_notification(trans("Selected Grade has been deleted"));
		
	$Mode = 'RESET';
}

if ($Mode == 'RESET'){
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}


if(list_updated('per_month') ){
  	if(get_post('per_month') == 1)
		$checkAll = 12;
	else
   		$checkAll = 1;
   	$Ajax->activate('GradeDisplay');
}else
  	$checkAll = 1;

  $dec = get_qty_dec();
//----------------------------------------------------------------------------------------------------
	start_form();

		start_table(TABLESTYLE_NOBORDER);
			check_row(trans("Show Per month"), 'per_month', null, true);
		end_table();
		br();
		div_start('GradeDisplay');
		$result = GetAll('kv_empl_grade');
		start_table(TABLESTYLE, "width=80%");

		$th = array (trans("Name"), trans("Minimum CTC"), trans("Maximum CTC"), trans("Annual Leave"),trans("Sick Leave Full"),trans("Sick Leave Half"),trans("Maternity Leave"),trans("Hajj Leave"), trans("Allowed Leaves"), '','');
		inactive_control_column($th);
		table_header($th);

		foreach($result as $myrow){
			
			label_cell($myrow["description"]);			
			label_cell(round($myrow["min_salary"]/$checkAll, $dec));
			label_cell(round($myrow["max_salary"]/$checkAll, $dec));
			label_cell(round($myrow["al"]/$checkAll, $dec));
			label_cell(round($myrow["sl"]/$checkAll, $dec));
			label_cell(round($myrow["slh"]/$checkAll, $dec));
			label_cell(round($myrow["ml"]/$checkAll, $dec));
			label_cell(round($myrow["hl"]/$checkAll, $dec));
			label_cell(round( ($myrow["sl"]/$checkAll+$myrow["ml"]/$checkAll+$myrow['al']/$checkAll+$myrow['hl']/$checkAll), $dec));
			inactive_control_cell($myrow["id"], $myrow["inactive"], 'kv_empl_grade', 'id');
		 	edit_button_cell("Edit".$myrow['id'], trans("Edit"));
		 	delete_button_cell("Delete".$myrow['id'], trans("Delete"));
			end_row();
		}
		inactive_control_row($th);
		end_table();
		br(2); 
		div_end();

		//----------------------------------------------------------------------------------------------------
		start_table(TABLESTYLE2);
		table_section_title(trans("Gade System"));
		if ($selected_id != -1){

		 	if ($Mode == 'Edit') {
				$myrow = GetRow('kv_empl_grade', array('id' => $selected_id));

				$_POST['description']  = $myrow["description"];
				$_POST['min_salary']  = $myrow["min_salary"];
				$_POST['max_salary']  = $myrow["max_salary"];
				$_POST['hl']  = $myrow["hl"];
				$_POST['al']  = $myrow["al"];
				$_POST['ml']  = $myrow["ml"];
				$_POST['sl']  = $myrow["sl"];
				$_POST['slh']  = $myrow["slh"];
				$_POST['inactive']  = $myrow["inactive"];
			}
			hidden('selected_id', $selected_id);
			//hidden('year', $myrow['year']);
		}else{
			$_POST['description']  = $_POST['hl'] = $_POST['al']=$_POST['sl'] = $_POST['ml'] = '';
			$_POST['min_salary']  = $_POST['max_salary']  = $_POST['inactive']  = 0; 
			hidden('selected_id', -1);
		}

		text_row(trans("Description:"), 'description', null, 40, 80);
		amount_row(trans("Minimum Salary"). " :", 'min_salary', null, null, '/'.trans("Annum"));
		amount_row(trans("Maximum Salary"). " :", 'max_salary', null, null, '/'.trans("Annum"));
		text_row_ex(trans("Annual Leave"). " :", 'al', 10, 10, '', null, null, "Annum");
		text_row_ex(trans("Sick Leave Full days"). " :", 'sl', 10, 10, '', null, null, "Annum");
		text_row_ex(trans("Sick Leave Half days"). " :", 'slh', 10, 10, '', null, null, "Annum");
		text_row_ex(trans("Maternity Leave"). " :", 'ml', 10, 10, '', null, null, "Annum");
		text_row_ex(trans("Hajj Leave"). " :", 'hl', 10, 10, '', null, null, "Annum");

		check_row(trans("Inactive").':', 'inactive', null);

		end_table(1);

		submit_add_or_update_center($selected_id == -1, '', 'both');

	end_form();
end_page(); ?>
<style>
table { width: auto; }
</style>