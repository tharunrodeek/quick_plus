<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_PAYROLL_INQ';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
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
 
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
page(trans("Advance Salary Inquiry"));
 
 simple_page_mode(true);

check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));
//----------------------------------------------------------------------------------------
	$new_item = get_post('selected_id')=='' || get_post('cancel') ;
	if (isset($_GET['selected_id'])){
		$_POST['selected_id'] = $_GET['selected_id'];
	}
	$selected_id = get_post('selected_id');
	//$month = get_post('month');
	//$year = get_post('year');
	 if (list_updated('selected_id')) {
		$_POST['dept_id'] = $selected_id = get_post('selected_id');
	    $Ajax->activate('details');
	}
//----------------------------------------------------------------------------------------

	start_form(true);

		start_table(TABLESTYLE_NOBORDER);
			echo '<tr>';
				//fiscalyears_list_cells(trans("Fiscal Year:"), 'year');
			 //	kv_current_fiscal_months_list_cell("Months", "month", null, true);
			 	department_list_cells(trans("Select a Department")." :", 'selected_id', null,	trans("No Department"), true, check_value('show_inactive'));
				employee_list_cells(trans("Select an Employee"). " :", 'empl_id', null,	trans("Select an Employee"), true, check_value('show_inactive'), false, false,true);
				$new_item = get_post('selected_id')=='';
		 	echo '</tr>';
	 	end_table(1);
		if (get_post('_show_inactive_update') || get_post('empl_id') ){		
			$Ajax->activate('details');
			set_focus('empl_id');
		}
	
		div_start('details');		
			
			if(get_post('empl_id')> 0) {
				if(get_post('selected_id') > 0){
					$salaries = GetDataJoin('kv_empl_salary_advance AS salary', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `salary`.`empl_id`'), 
						1 => array('join' => 'INNER', 'table_name' => 'kv_empl_job AS job', 'conditions' => '`job`.`empl_id` = `salary`.`empl_id`') 
					), 
					array('`info`.`empl_id`, CONCAT(`info`.`empl_firstname`, `info`.`empl_lastname`) AS name, `salary`.`date`, `salary`.`month`, `salary`.`year`, `salary`.`amount`'), array('`job`.`department`' => get_post('selected_id')), array('info.empl_id' => 'asc'));
				}else {
					$salaries = GetDataJoin('kv_empl_salary_advance AS salary', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `salary`.`empl_id`'),
					), 
					array('`info`.`empl_id`, CONCAT(`info`.`empl_firstname`, `info`.`empl_lastname`) AS name, `salary`.`date`, `salary`.`month`, `salary`.`year`, `salary`.`amount`'), array('`info`.`empl_id`' => get_post('empl_id')), array('info.empl_id' => 'asc'));
				}
			} else {
				if(get_post('selected_id') > 0){
					$salaries = GetDataJoin('kv_empl_salary_advance AS salary', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `salary`.`empl_id`'), 
						1 => array('join' => 'INNER', 'table_name' => 'kv_empl_job AS job', 'conditions' => '`job`.`empl_id` = `salary`.`empl_id`') 
					), 
					array('`info`.`empl_id`, CONCAT(`info`.`empl_firstname`, `info`.`empl_lastname`) AS name, `salary`.`date`, `salary`.`month`, `salary`.`year`, `salary`.`amount`'), array('`job`.`department`' => get_post('selected_id')), array('info.empl_id' => 'asc'));
				}else {
					$salaries = GetDataJoin('kv_empl_salary_advance AS salary', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `salary`.`empl_id`'),
					), 
					array('`info`.`empl_id`, CONCAT(`info`.`empl_firstname`, `info`.`empl_lastname`) AS name, `salary`.`date`, `salary`.`month`, `salary`.`year`, `salary`.`amount`'), array(), array('info.empl_id' => 'asc'));
				}
			}
			
			start_table(TABLESTYLE);
				echo  "<tr> <td class='tableheader'>" . trans("Employee ID") . "</td>
					<td class='tableheader'>" . trans("Employee Name") . "</td>	
					<td class='tableheader'>" . trans("Date") . "</td>	
					<td class='tableheader'>" . trans("Month") . "</td>
					<td class='tableheader'>" . trans("Year") . "</td>
					<td class='tableheader'>" . trans("Amount") . "</td></tr>";

				foreach($salaries as $salary_single) {	
					$fiscal_yr = GetRow('fiscal_year', array('id' => $salary_single[4]));
					echo '<tr style="text-align:center"><td>'.$salary_single[0].'</td><td>'.$salary_single[1].'</td><td>'.sql2date($salary_single[2]).'</td><td>'.kv_month_name_by_id($salary_single[3]).'</td><td>'.sql2date($fiscal_yr['begin']).' - '.sql2date($fiscal_yr['end']).'</td><td align="right">'.price_format($salary_single[5]).'</td><tr>';
				}
			end_table(1);
		
		div_end();
	end_form();
 
 
end_page();
 
?>