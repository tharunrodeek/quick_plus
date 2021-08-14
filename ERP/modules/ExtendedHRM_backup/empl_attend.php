<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module  : Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_ATTENDANCE';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/includes/ui.inc");

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
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
page(trans("Employee Attendance"));
$new_item = get_post('dept_id')=='' || get_post('cancel') ;

if (isset($_GET['dept_id'])){
	$_POST['dept_id'] = $_GET['dept_id'];
}
$dept_id = get_post('dept_id');
 if (list_updated('dept_id')) {
	$dept_id = get_post('dept_id');
    $Ajax->activate('details');
}

if (isset($_POST['addupdate'])) {
		$input_error = 0;
		$employees = array();
		foreach($_POST as $empls =>$val) {			
			if (substr($empls,0,5) == 'Empl_' && substr($empls,0,7) != 'Empl_in' && substr($empls,0,8) != 'Empl_out' && substr($empls,0,6) != 'Empl_c' )
				$employees[] = substr($empls, 5);
		}
		$empl_count=get_dep_employees_count($_POST['dept_id'], $_POST['attendance_date']);
		$employees = array_values($employees);
		$attend_count=count($employees);

		if($input_error==0){
		$attendance_date = strtotime(date2sql($_POST['attendance_date']));
		$month = date("m", $attendance_date);
		$day = date("d", $attendance_date);

		$year = get_fiscal_year_id_from_date($_POST['attendance_date']);

		foreach ($employees as $empl_id) {
			
			if(isset($_POST['Empl_c_'.$empl_id]) && $_POST['Empl_c_'.$empl_id] != 1)
				continue;

			$BeginTime = date('H:i:s', strtotime($_POST['Empl_in_'.$empl_id.'_hour'].':'.str_pad($_POST['Empl_in_'.$empl_id.'_min'], 2, '0', STR_PAD_LEFT).' '.$_POST['Empl_in_'.$empl_id.'_ampm']));
			$_POST['Empl_in_'.$empl_id] = $BeginTime;

			$EndTime = date('H:i:s', strtotime($_POST['Empl_out_'.$empl_id.'_hour'].':'.str_pad($_POST['Empl_out_'.$empl_id.'_min'], 2, '0', STR_PAD_LEFT).' '.$_POST['Empl_out_'.$empl_id.'_ampm']));
			$_POST['Empl_out_'.$empl_id] = $EndTime;
			//$early_punch_allowed = GetSingleValue('kv_empl_attendance_settings', 'option_value', array('dept_id' => $_POST['dept_id'], 'option_name' => 'early_coming_punch'));
			//if($early_punch_allowed  && $_POST['office_begin_time'] )

			//Consider Early In
			if(isset($_POST['early_coming_punch']) && $_POST['early_coming_punch'] != 1 && strtotime($_POST['office_begin_time']) >= strtotime($_POST['Empl_in_'.$empl_id])  ){
				$_POST['Empl_in_'.$empl_id] = $_POST['office_begin_time']; 
			}

			//Consider Early Going Punch
			if(isset($_POST['late_going_punch']) && $_POST['late_going_punch'] != 1 && strtotime($_POST['office_end_time']) <= strtotime($_POST['Empl_out_'.$empl_id])  ){
				$_POST['Empl_out_'.$empl_id] = $_POST['office_end_time']; 
			}			

			// Grace time for Late Punch in 
			if(strtotime($_POST['office_begin_time']) <= strtotime($_POST['Empl_in_'.$empl_id]) && isset($_POST['grace_in_time'])){

				$secs = strtotime($_POST['grace_in_time'])-strtotime("00:00:00");
				$office_time_grace_time = strtotime($_POST['office_begin_time'])+$secs;

				if( $office_time_grace_time < strtotime($_POST['Empl_in_'.$empl_id])){  
					if($_POST['mark_half_day_late'] == 1) { // Mark Half day, if late coming by 
						$morning_late_time = strtotime($_POST['mark_half_day_late_min'])-strtotime("00:00:00");
						$office_time_late_grace_time = strtotime($_POST['office_begin_time'])+$morning_late_time;

						if( $office_time_late_grace_time < strtotime($_POST['Empl_in_'.$empl_id])){
							//$_POST['Empl_in_'.$empl_id] = $_POST['office_begin_time'];
							//Here also half day conversion needs to be clarified..
							$_POST['Empl_'.$empl_id] = 'HD';
						}
					}
				}				
			}			
			
			// Grace Time for Early Punch Go out
			if( strtotime($_POST['office_end_time']) > strtotime($_POST['Empl_out_'.$empl_id]) && isset($_POST['grace_out_time']) ){ 

				$secs = strtotime($_POST['grace_out_time'])-strtotime("00:00:00");
				$office_time_grace_time = strtotime($_POST['office_end_time'])-$secs;

				if( $office_time_grace_time > strtotime($_POST['Empl_out_'.$empl_id])){  // Mark Half day, if early going by 
					if($_POST['mark_half_day_early_go'] == 1){
						$evening_early_time = strtotime($_POST['mark_half_day_early_go_min'])-strtotime("00:00:00");
						$office_time_early_grace_time = strtotime($_POST['office_end_time'])-$evening_early_time;

						if( $office_time_early_grace_time > strtotime($_POST['Empl_out_'.$empl_id])){
							//$_POST['Empl_in_'.$empl_id] = $_POST['office_begin_time'];
							//Here also half day conversion needs to be clarified..
							$_POST['Empl_'.$empl_id] = 'HD';
						}
					}
				}
			}
			
			// Mark half day if work duration less than ...
			if(isset($_POST['Halfday_workduration']) && $_POST['Halfday_workduration'] == 1){
				$secs = strtotime($_POST['Halfday_workduration_min'])-strtotime("00:00:00");
				$worked_time =  strtotime($_POST['Empl_out_'.$empl_id]) - strtotime($_POST['Empl_in_'.$empl_id]);
				if($worked_time < $secs){
					$_POST['Empl_'.$empl_id] = 'HD';
				}
			} 

			// Mark Absent if work duration less than ...
			if(isset($_POST['absent_workduration']) && $_POST['absent_workduration'] == 1) {
				$secs = strtotime($_POST['absent_workduration_min'])-strtotime("00:00:00");
				$worked_time =  strtotime($_POST['Empl_out_'.$empl_id]) - strtotime($_POST['Empl_in_'.$empl_id]);
				if($worked_time < $secs){
					$_POST['Empl_'.$empl_id] = 'A';
				}
			}
			
			if(db_has_day_attendancee($empl_id, $month, $year)){
				update_employee_attendance($_POST['Empl_'.$empl_id], $empl_id, $month, $year,$day, $_POST['Empl_in_'.$empl_id], $_POST['Empl_out_'.$empl_id]);
			}else{
				if(!$_POST['dept_id'])
					$_POST['dept_id'] = GetSingleValue('kv_empl_job', 'department', array('empl_id' => $_POST['empl_id']));
				add_employee_attendance($_POST['Empl_'.$empl_id], $empl_id, $month, $year, $day, $_POST['Empl_in_'.$empl_id], $_POST['Empl_out_'.$empl_id],	$_POST['dept_id']);
			}

			if(isset($_POST['absent_workduration_both']) &&  $_POST['absent_workduration_both'] == 1) {
				$att_date  = date2sql($_POST['attendance_date']);
				$Yesterday = date('d', strtotime('-1 day', strtotime($att_date))); 
				$Tomorrow  = date('d', strtotime('+1 day', strtotime($att_date))); 
				if( isset($_POST['Yesterday']) && $_POST['Yesterday'] == 'Holiday'){					 
					update_employee_attendance($_POST['Empl_'.$empl_id], $empl_id, $month, $year,$Yesterday, $_POST['Empl_in_'.$empl_id], $_POST['Empl_out_'.$empl_id]);
				}elseif(isset($_POST['Tomorrow']) && $_POST['Tomorrow'] == 'Holiday'){
					update_employee_attendance($_POST['Empl_'.$empl_id], $empl_id, $month, $year,$Tomorrow, $_POST['Empl_in_'.$empl_id], $_POST['Empl_out_'.$empl_id]);
				}
			}
		}
		display_notification("Attendance Register Saved Successfully");
	}
	$new_role = true;	//clear_data();
	$Ajax->activate('_page_body');	
}

start_form(true);

if (db_has_employees()) {
	if (isset($_POST['dept_id']) && $_POST['dept_id'] >0) {
		$_POST['dept_id'] = input_num('dept_id');
	}
	start_table(TABLESTYLE2);
		start_row();   
			date_cells(trans("Date") . ":", 'attendance_date', null, null, 0,0,0, null, true);
			department_list_cells(trans("Select a Department")." :", 'dept_id', null,	trans("No Department"), true, check_value('show_inactive'));
			employee_list_cells(trans("Select an Employee"). " :", 'empl_id', null,	trans("Select an Employee"), true, check_value('show_inactive'), false, false,true);
			$new_item = get_post('dept_id')=='';
		end_row();	
	end_table();
	
	if (get_post('_show_inactive_update')|| get_post('empl_id')) {
		
		$Ajax->activate('EmplAttendance');
		set_focus('empl_id');		
	}	
		
	if(get_post('dept_id') || get_post('empl_id')){
		if(get_post('empl_id'))
			$department_id = GetSingleValue('kv_empl_job', 'department', array('empl_id' => get_post('empl_id')));
		else
			$department_id = get_post('dept_id');
		$all_settings =  GetAll('kv_empl_attendance_settings', array('dept_id' => $department_id));
		foreach($all_settings as $settings){
			hidden($settings['option_name'], $settings['option_value']);
		}
	}
	if (get_post('_show_inactive_update')) {
		$Ajax->activate('dept_id');
		$attendance_date = get_post('attendance_date');
		set_focus('dept_id');
	}
	if(get_post('attendance_date') ) {
		$attendance_date = get_post('attendance_date');   
		$Ajax->activate('EmplAttendance');
	}
	
div_start('EmplAttendance');
	$dept_id = get_post('dept_id');
	$attendance_date = get_post('attendance_date');   
	//$Ajax->activate('_page_body');
	if (!$dept_id) 
		$dept_id = 0 ; 	
	$day_absentees = array();
	
	br();
	$disabled= '';
	$submit = false;
	if(strtotime(date2sql($attendance_date)) > strtotime(date('Y-m-d'))){
		display_warning("You can't Enter Yet to born day Attendance!");
		set_focus('attendance_date');
		$disabled = 'disabled';
		$submit = false;
	}elseif(key_in_foreign_table(date2sql($attendance_date), 'kv_empl_gazetted_holidays', 'date')){
		display_warning("It's Official Holiday, you can't Input to a holiday!");
		set_focus('attendance_date');
		$disabled = 'disabled';
		$submit = false;
	}else{
		$submit = true;
	}
	$all_settings1 =  GetAll('kv_empl_option');
	$hrmsetup = array(); 
	foreach($all_settings1 as $settings){
		$data_offdays = @unserialize(base64_decode($settings['option_value']));
		if ($data_offdays !== false) {
			$hrmsetup[$settings['option_name']] = unserialize(base64_decode($settings['option_value']));
		} else {
			$hrmsetup[$settings['option_name']] = $settings['option_value']; 
		}
	}
	//display_error($hrmsetup['weekly_off']);
	if(in_array(date('D', strtotime(date2sql($attendance_date))), $hrmsetup['weekly_off'])){
		display_warning(trans("It's Weekly off day, Please try another day!"));
		set_focus('attendance_date'); 
		$disabled = 'disabled';
	}
	echo '<center><p style="color: #F44336;"><b>Note</b>: * AL-Earned Leave, CL-Common Leave, ML-Medical Leave</p></center>';

	$att_date  = date2sql($attendance_date);
	$Yesterday = date('Y-m-d', strtotime('-1 day', strtotime($att_date))); 
	$Tomorrow = date('Y-m-d', strtotime('+1 day', strtotime($att_date))); 
	if(key_in_foreign_table($Yesterday, 'kv_empl_gazetted_holidays', 'date') || $hrmsetup['weekly_off'] == date('D', strtotime($Yesterday)) ){
		hidden('Yesterday', 'Holiday');
	}

	if(key_in_foreign_table($Tomorrow, 'kv_empl_gazetted_holidays', 'date') || $hrmsetup['weekly_off'] 	== date('D', strtotime($Tomorrow)) ){
		hidden('Tomorrow', 'Holiday');
	}

	start_table(TABLESTYLE);	
	//table_section_title(trans("Employees List"));
	echo '<tr> <td class="tableheader">'.checkbox(null, 'CheckAll', null, true).trans("Empl ID").'</td> <td class="tableheader">'. trans("Employee Name").'</td> <td class="tableheader"> '. trans("Present").'</td> <td  class="tableheader"> '. trans("In Time").'</td> <td  class="tableheader">'. trans("Out Time").'</td><td  class="tableheader"> '. trans("Leave").' </td><td class="tableheader"> '. trans("Absent").' </td> <td class="tableheader"> '. trans("On Duty").'</td> <td class="tableheader"> '. trans("Half Day").'</td> </tr>' ;
	
	if(list_updated('CheckAll') ){
   		if(get_post('CheckAll') == 1)
			$checkAll = 1;
		else
   			$checkAll = 0;
   		$Ajax->activate('sal_calculation');
   	}else
   		$checkAll = null;
	
	if($dept_id || get_post('empl_id') ) {
		//label_row(" Select a Department to note attendance ", '', "colspan=4", " ", 4 ); 
	//else {
		if(( list_updated('empl_id') && $_POST['empl_id'] > 0 ) || get_post('empl_id') > 0 ){	//$selected_empl = GetRow('kv_empl_info', array('empl_id' => ));
			$sql = "SELECT DISTINCT info.empl_id, job.joining, info.status, info.date_of_status_change FROM ".TB_PREF."kv_empl_job job, ".TB_PREF."kv_empl_info info WHERE info.empl_id= job.empl_id AND  info.empl_id=".get_post('empl_id');  
			$selected_empl =  db_query($sql, "The employee table is inaccessible");
			$day_absentees = get_single_employee_attendances($attendance_date,$_POST['empl_id']);
			//display_error("fnhdthdr".get_post('empl_id'));
		} else {				
			$selected_empl = kv_get_employees_based_on_dept($dept_id);	
			$day_absentees = get_employees_attendances($attendance_date,$_POST['dept_id']);
		}
		//display_error(json_encode($day_absentees));
		
		$month = date("m", strtotime(date2sql($attendance_date)));
		$year = get_fiscal_year_id_from_date($attendance_date);
		if($year) {
			$get_payroll = GetAll('kv_empl_salary', array('month' => $month, 'year' => $year));
			
			while ($row = db_fetch_assoc($selected_empl)) {
				//display_error(json_encode($row));
				$mesg = '';
				foreach($get_payroll as $payroll){
					if($payroll['empl_id'] == $row['empl_id']){
						//$months_with_yrs_list = kv_get_months_with_years_in_fiscal_year($payroll['year']);
						//$ext_year = date("Y", strtotime($months_with_yrs_list[(int)$payroll['month']]));
						//$sal_date = date("Y-m-d", strtotime($ext_year."-".$payroll['month']."-".$hrmsetup['EndDay']));
						//if()
						$disabled = 'disabled';
						$mesg = trans("The Disabled Employee's Payroll Processed Already.");
						break;
					}else
						$disabled = '';
				}
				if($disabled == ''){
					$sql = "SELECT month, year FROM ".TB_PREF."kv_empl_salary WHERE id=(SELECT MIN(`id`) FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".$row['empl_id'].") ";
					$res =  db_query($sql, "Can't get result");
					if($first_sal_month = db_fetch_assoc($res)){
						$months_with_years_list = kv_get_months_with_years_in_fiscal_year($first_sal_month['year']);
						$ext_year = date("Y", strtotime($months_with_years_list[(int)$first_sal_month['month']]));
						$first_sal_date = date("Y-m-d", strtotime($ext_year."-".$first_sal_month['month']."-".$hrmsetup['EndDay']));
						//display_error($first_sal_date.'---'.date2sql($attendance_date));
						if(strtotime($first_sal_date) >= strtotime(date2sql($attendance_date))){
							$disabled = 'disabled';
							$mesg = trans("Sorry you are not allowed to input before the first salary.");
						}
					}
				}				
					
				if($mesg != '' && $disabled != ''){
					display_warning($mesg);
					set_focus('attendance_date');
				}
				if($row['joining'] <= date2sql($attendance_date) && ($row['status'] == 1 || ($row['status']>1 && $row['date_of_status_change'] >= date2sql($attendance_date)))){
					echo '<tr> <td>'. checkbox(null, 'Empl_c_'.$row['empl_id'], $checkAll).' '.$row['empl_id'].'</td> <td>'.kv_get_empl_name($row['empl_id']).'</td><td>';
					//display_error($_POST['Empl_'.$row['empl_id']]);
					//display_error($day_absentees[$row['empl_id']]);
					$_POST['Empl_'.$row['empl_id']] = (isset($day_absentees[$row['empl_id']]) ? $day_absentees[$row['empl_id']] : ''); 
					if(array_key_exists($row['empl_id'], $day_absentees)  && $day_absentees[$row['empl_id']] == "P")
						echo kv_radio(" ", 'Empl_'.$row['empl_id'], "P", "selected", false, $disabled);
					else
						echo kv_radio(" ", 'Empl_'.$row['empl_id'], "P", "selected", false, $disabled);
						
					echo '</td><td>';

					if(array_key_exists($row['empl_id'], $day_absentees)  && (date('H:i:s', strtotime($day_absentees[$row['empl_id'].'vj_in'])) != '00:00:00' ? true : false))
						echo TimeDropDown('Empl_in_'.$row['empl_id'], $day_absentees[$row['empl_id'].'vj_in'], false, $disabled);
					else
						echo TimeDropDown( 'Empl_in_'.$row['empl_id'], $hrmsetup['BeginTime'], false, $disabled);
						
					echo '</td><td>';

					if(array_key_exists($row['empl_id'], $day_absentees)  && (date('H:i:s', strtotime($day_absentees[$row['empl_id'].'vj_out'])) != '00:00:00' ? true : false))
						echo TimeDropDown('Empl_out_'.$row['empl_id'], $day_absentees[$row['empl_id'].'vj_out'], false, $disabled);
					else
						echo TimeDropDown( 'Empl_out_'.$row['empl_id'], $hrmsetup['EndTime'], false, $disabled);
						
					echo '</td><td>';

					if(array_key_exists($row['empl_id'], $day_absentees)  && $day_absentees[$row['empl_id']] == "AL")
						echo kv_radio("<label>AL</label>", 'Empl_'.$row['empl_id'], "AL", "selected", false, $disabled);
					else
						echo kv_radio("<label>AL</label>", 'Empl_'.$row['empl_id'], "AL", null, false , $disabled);

					if(array_key_exists($row['empl_id'], $day_absentees)  && $day_absentees[$row['empl_id']] == "CL")
						echo kv_radio("<label>CL</label>", 'Empl_'.$row['empl_id'], "CL", "selected", false, $disabled);
					else
						echo kv_radio("<label>CL</label>", 'Empl_'.$row['empl_id'], "CL", null, false , $disabled);

					if(array_key_exists($row['empl_id'], $day_absentees)  && $day_absentees[$row['empl_id']] == "ML")
						echo kv_radio("<label>ML</label>", 'Empl_'.$row['empl_id'], "ML", "selected", false, $disabled);
					else
						echo kv_radio("<label>ML</label>", 'Empl_'.$row['empl_id'], "ML", null, false , $disabled);
						
					echo '</td><td>';

					if(array_key_exists($row['empl_id'], $day_absentees)  && $day_absentees[$row['empl_id']] == "A")
						echo kv_radio(" ", 'Empl_'.$row['empl_id'], "A", "selected", false, $disabled);
					else
						echo kv_radio(" ", 'Empl_'.$row['empl_id'], "A", null, false , $disabled);
						
					echo '</td><td>';

					if(array_key_exists($row['empl_id'], $day_absentees)  && $day_absentees[$row['empl_id']] == "OD")
						echo kv_radio(" ", 'Empl_'.$row['empl_id'], "OD", "selected", false, $disabled);
					else
						echo kv_radio(" ", 'Empl_'.$row['empl_id'], "OD", null, false , $disabled);
						
					echo '</td><td>';

					if(array_key_exists($row['empl_id'], $day_absentees)  && $day_absentees[$row['empl_id']] == "HD")
						echo kv_radio(" ", 'Empl_'.$row['empl_id'], "HD", "selected", false, $disabled);
					else
						echo kv_radio(" ", 'Empl_'.$row['empl_id'], "HD", null, false , $disabled);				
					echo '</td></tr>';
				}							
			}		
		}	else 
			display_warning("Fiscal year not found, Please insert corresponding fiscal year Setup->fiscal years");
	}
		hidden('office_begin_time', $hrmsetup['BeginTime']);
		hidden('office_end_time', $hrmsetup['EndTime']);
	end_table();
	br();
	if($submit){
		submit_center('addupdate', trans("Submit Attendance"), true, '', 'default');
	}
div_end();

}else{
	check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));
}
 
end_form();
end_page(); 
?>
<style>
select { width: auto !important; } 
.list_container {   width: auto;   display: inherit;}
table.tablestyle tr td:nth-child(4), table.tablestyle tr td:nth-child(5) { width:150px; }
</style>