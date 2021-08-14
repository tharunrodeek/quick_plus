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
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

page(trans("Attendance Settings"));

CheckEmptyResult('kv_empl_departments', sprintf(trans("There is no departments in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/department.php'>".trans("Departments")."</a>"));

if(isset($_GET['Updated']) && $_GET['Updated'] == 'Yes'){
	display_notification("Attendance Settings Updated");
}//else
	//display_warning(trans("Payroll Begin and End day can be set only once. So careful with the settings."));

if(isset($_GET['dept_id']) && $_GET['dept_id'] > 0 && !isset($_POST['dept_id']))
	$_POST['dept_id'] = $_GET['dept_id'];

function can_process() {	

	 /*
	if (!check_num('expd_percentage_amt'))	{
		display_error(trans("Maximum EMI Limit should be a positive number"));
		set_focus('login_tout');
		$input_error = 1;
	}
	if (!check_num('ot_factor'))	{
		display_error(trans("OT Multiplication Factor should be a positive number"));
		set_focus('ot_factor');
		$input_error = 1;
	}*/
	
	return true;	
}
if (isset($_POST['addupdate'])&& can_process()) {
	if($_POST['dept_id'] > 0 ){
		$options = array( 'early_coming_punch', 'Halfday_workduration', 'absent_workduration' , 'mark_half_day_late', 'mark_half_day_early_go', 'absent_workduration_both', 'late_going_punch');
		$option_times = array('grace_in_time', 'grace_out_time', 'Halfday_workduration_min', 'absent_workduration_min', 'mark_half_day_late_min', 'mark_half_day_early_go_min');
		foreach($option_times as $opt_time){
			$BeginTime = date('H:i:s', strtotime($_POST[$opt_time.'_hour'].':'.str_pad($_POST[$opt_time.'_min'], 2, '0', STR_PAD_LEFT).':00'));
			$_POST[$opt_time] = $BeginTime;
		}
		$process = true;
		if (strtotime($_POST['mark_half_day_late_min']) < strtotime($_POST['grace_in_time'])){
			display_error(trans("Your grace In time is greater than this time."));
			set_focus('mark_half_day_late_hour');
			$process =  false;
		}
		if($process) {
			foreach ($_POST as $key => $value) {
				if((in_array($key, $options) || in_array($key, $option_times) ) && $key != 'debt_id'){
					Update('kv_empl_attendance_settings', array('option_name' => $key, 'dept_id' => $_POST['dept_id']) , array('option_value' => $value) );
				}
			}
			meta_forward($_SERVER['PHP_SELF'], "dept_id=".$_POST['dept_id']."&Updated=Yes");	
		}
	} else	
		display_warning(trans("No Department selected"));
}

	start_form();
		start_table(TABLESTYLE_NOBORDER);
			department_list_cells(trans("Select a Department")." :", 'dept_id', null, trans("Select Department"), true, check_value('show_inactive'), false, true);

		end_table();
		br();
		$_POST['grace_in_time'] = $_POST['grace_out_time'] = $_POST['Halfday_workduration_min'] = $_POST['absent_workduration_min'] =  $_POST['mark_half_day_late_min'] = $_POST['mark_half_day_early_go_min'] = '';

		if(get_post('dept_id')){
			$all_settings =  GetAll('kv_empl_attendance_settings', array('dept_id' => get_post('dept_id')));

			foreach($all_settings as $settings){
				//if(isset($_POST['BeginDay']) && $settings['option_name'] != $_POST['BeginDay'])
					$_POST[$settings['option_name']] = $settings['option_value'];
			}
		}
		start_outer_table(TABLESTYLE2);

			table_section(1);
			//table_section_title(trans("Grace Times"));
				//text_row(trans("Grace Time for Late Coming"), 'grace_in_time', null, 10, 10);
				TimeDropDown_row(trans('Grace Time for Late Coming Punch'), 'grace_in_time', $_POST['grace_in_time'], false, true);

				check_row(trans("Consider Early Coming Punch:"), 'early_coming_punch');
				table_section_title(trans(" "));
				check_row(trans("Calculate Halfday if work duration is less than"), 'Halfday_workduration');
				check_row(trans("Calculate Absent if work duration is less than"), 'absent_workduration');
				table_section_title(trans(" "));
				//check_row(trans("On Partial day Calculation Half Day if work duration is less than  "), 'partial_halfday_workduration');
				//check_row(trans("On Partial day Calculation Absent if work duration is less than"), 'partial_absent_workduration');
				
				check_row(trans("Mark Halfday if late by"), 'mark_half_day_late');
				check_row(trans("Mark Halfday if Early Going by "), 'mark_half_day_early_go');

				//check_row(trans("Mark Weekly Off and Holiday as Absent, if both Sufix, Prefix is Absent"), 'absent_workduration_both');
			table_section(2);
				//text_row(trans("Grace Time for Early Going"), 'grace_out_time', null, 10, 10);
				TimeDropDown_row(trans('Grace Time for Early Going Punch'), 'grace_out_time', $_POST['grace_out_time'], false,  true);
				check_row(trans("Consider Late Going Punch:"), 'late_going_punch');
				table_section_title(trans(" "));
				TimeDropDown_row(trans(' '), 'Halfday_workduration_min', $_POST['Halfday_workduration_min'], false,  true);
				//text_row_ex(trans(" "), 'Halfday_workduration_min', 10, 10, '', null, null, " Mins");
				TimeDropDown_row(trans(' '), 'absent_workduration_min', $_POST['absent_workduration_min'], false,  true);
				table_section_title(trans(" "));
				//TimeDropDown_row(trans(' '), 'partial_halfday_workduration_min', $_POST['partial_halfday_workduration_min'], false, false, true);
				//TimeDropDown_row(trans(' '), 'partial_absent_workduration_min', $_POST['partial_absent_workduration_min'], false, false, true);

				TimeDropDown_row(trans(' '), 'mark_half_day_late_min', $_POST['mark_half_day_late_min'], false,  true);
				TimeDropDown_row(trans(' '), 'mark_half_day_early_go_min', $_POST['mark_half_day_early_go_min'], false, true);
		end_outer_table(1);
		br();
		submit_center('addupdate', trans("Submit"), true, '', 'default');

	end_form();  
end_page(); ?>
<style>select { width: auto !important; </style>