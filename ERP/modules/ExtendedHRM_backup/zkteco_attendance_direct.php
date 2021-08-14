<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'SA_OPEN';
$path_to_root="../..";

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
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/zklibrary.php" );

page(trans("ZKTeco Attendance Import"));

if(list_updated('month') || list_updated('year'))
	$Ajax->activate('totals_tbl');


start_form();
	if (db_has_employees()) {
		start_table(TABLESTYLE_NOBORDER);
		start_row();
		kv_fiscalyears_list_cells(trans("Fiscal Year:"), 'year', null, true);
		kv_current_fiscal_months_list_cell(trans("Months"), "month", null, true, false);
	
		end_row();
		end_table();
		br();
		if (get_post('_show_inactive_update')) {
			$Ajax->activate('month');
			$Ajax->activate('year');
			$Ajax->activate('totals_tbl');
		}
	} else {	
		hidden('month');
		hidden('year');
	}

div_start('totals_tbl');


$months_with_years_list = kv_get_months_with_years_in_fiscal_year(get_post('year'));
$end_of_selected_month = end_month(sql2date($months_with_years_list[(int)get_post('month')])); 
$begin_of_selected_month = begin_month(sql2date($months_with_years_list[(int)get_post('month')])); 
$zk_ip = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'zk_ip'));
$zk = new ZKLibrary($zk_ip, 4370);
$zk->connect();
$zk->disableDevice();

//$users = $zk->getUser();
//$zk->clearAttendance();
$attendances = $zk->getAttendance(date2sql($begin_of_selected_month), date2sql($end_of_selected_month));	
//$zk->setUser('7', '7', 'Arun', '1234', '0');
//var_dump($attendances);
$users = array();
if($attendances) {
	echo json_encode($attendances);
	foreach($attendances as $key => $attendance){
		$date_to_Day = date('d', strtotime($attendance[3]));
		$users[$attendance[1]][$date_to_Day][] = $attendance[3];
	}

	start_table(TABLESTYLE, "width=40%");

	$th = array(trans("User ID"),trans("Attendance"));
	table_header($th);
	$companyBeginTime = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'BeginTime'));
	$companyEndTime = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'EndTime'));
	//echo json_encode($users);
	foreach($users as $userID => $usr){  //2018-07-22 14:04:26
		$empl_shift_time = GetDataJoinRow('kv_empl_shifts AS shift', array( 
			0 => array('join' => 'INNER', 'table_name' => 'kv_empl_job AS job','conditions'=>'`job`.`shift` = `shift`.`id`')), 
			array('`shift`.`BeginTime`, `shift`.`EndTime`'), array('`job`.`empl_id`' => $userID));
		if(empty($empl_shift_time)){
			$empl_shift_time['BeginTime'] = $companyBeginTime;
			$empl_shift_time['EndTime'] = $companyEndTime;
		}
		//echo  $empl_shift_time['BeginTime'];
		//var_dump($empl_shift_time);
		$empl_dept = GetSingleValue('kv_empl_job', 'department', array('empl_id' => $userID));
		$attend_array = array();
		label_row($userID , $date_wise);
		//echo ('-----'.$empl_shift_time['EndTime'].'ssegrser');
		if(count($usr) > 0) {
			$attend_Settings = GetAll('kv_empl_attendance_settings', array('dept_id' => $empl_dept));
			$attendance_Settings = array();
			foreach ($attend_Settings as $value) {
				$attendance_Settings[$value['option_name']] = $value['option_value'];
			}
			foreach($usr as $day => $dates){
				//echo ('-'.$empl_shift_time['EndTime'].'ssegrser');
				if(count($dates) > 1){
					if(count($dates) == 2){
						$attend_array[(int)$day] = 'P';
						$attend_array[(int)$day.'_in'] = date('H:i:s', strtotime($dates[0]));
						$attend_array[(int)$day.'_out'] = date('H:i:s', strtotime($dates[1]));
					}else {
						$iterat = count($dates);

						$attend_array[(int)$day] = 'P';
						$attend_array[(int)$day.'_in'] = date('H:i:s', strtotime($dates[0]));

						if ($iterat % 2 == 1)
							$iterate = $iterat-1;
						else
							$iterate = $iterat;
						
						$time_dif = 0;
						for($vj=0; $vj<=$iterate; $vj += 2){
							$time_dif += (strtotime($dates[$vj]) - strtotime($dates[$vj+1]));
						}
						if ($iterat % 2 == 1){
							$time_dif += (strtotime($dates[$iterate]) - strtotime($dates[$iterat]));
						}
						$attend_array[(int)$day.'_out'] = date('H:i:s', strtotime(strtotime($dates[0])+$time_dif));
					}
				} elseif(strtotime($dates[0]) < strtotime(date('Y-m-d')) ) {
					//echo ('--'.$empl_shift_time['EndTime'].'ssegrser');
					$attend_array[(int)$day] = 'P';
					$attend_array[(int)$day.'_in'] = date('H:i:s', strtotime($dates[0]));
					$attend_array[(int)$day.'_out'] = $empl_shift_time['EndTime'];
				}
				
				//Consider Early In
				//echo json_encode($attendance_Settings['early_coming_punch']).'-1---'.$attend_array[(int)$day.'_in'];
				if($attendance_Settings['early_coming_punch'] == 0 && strtotime($empl_shift_time['BeginTime']) >= strtotime($attend_array[(int)$day.'_in'])){
					$attend_array[(int)$day.'_in'] = $empl_shift_time['BeginTime']; 
				}
				//echo '++'.$attend_array[(int)$day.'_in'];
				//Consider Early Going Punch
				if($attendance_Settings['late_going_punch'] == 0 && strtotime($empl_shift_time['EndTime']) <= strtotime($attend_array[(int)$day.'_out'])  ){
					$attend_array[(int)$day.'_out'] = $empl_shift_time['EndTime']; 
				}

				// Grace time for Late Punch in 
				if(strtotime($empl_shift_time['BeginTime']) <= strtotime($attend_array[(int)$day.'_in'])){

					$secs = strtotime($attendance_Settings['grace_in_time'])-strtotime("00:00:00");
					$office_time_grace_time = strtotime($empl_shift_time['BeginTime'])+$secs;

					if( $office_time_grace_time < strtotime($attend_array[(int)$day.'_in'])){  
						if($attendance_Settings['mark_half_day_late'] == 1) { // Mark Half day, if late coming by 
							$morning_late_time =strtotime($attendance_Settings['mark_half_day_late_min'])-strtotime("00:00:00");
							$office_time_late_grace_time = strtotime($empl_shift_time['BeginTime'])+$morning_late_time;
							if( $office_time_late_grace_time < strtotime($attend_array[(int)$day.'_in'])){
								//$_POST['Empl_in_'.$empl_id] = $_POST['office_begin_time'];								
								$attend_array[(int)$day] = 'HD'; //Here also half day conversion needs to be clarified..
							}
						}
					}				
				}			
			
				// Grace Time for Early Punch Go out
				if( strtotime($empl_shift_time['EndTime']) > strtotime($attend_array[(int)$day.'_out'])  ){ 

					$secs = strtotime($attendance_Settings['grace_out_time'])-strtotime("00:00:00");
					$office_time_grace_time = strtotime($empl_shift_time['EndTime'])-$secs;

					if( $office_time_grace_time > strtotime($attend_array[(int)$day.'_out'])){  // Mark Half day, if early going by 
						if($attendance_Settings['mark_half_day_early_go'] == 1){
							$evening_early_time = strtotime($attendance_Settings['mark_half_day_early_go_min'])-strtotime("00:00:00");
							$office_time_early_grace_time = strtotime($empl_shift_time['EndTime'])-$evening_early_time;

							if( $office_time_early_grace_time > strtotime($attend_array[(int)$day.'_out'])){
								//$_POST['Empl_in_'.$empl_id] = $_POST['office_begin_time'];								
								$attend_array[(int)$day] = 'HD'; //Here also half day conversion needs to be clarified..
							}
						}
					}
				}
				//display_error(strtotime($_POST['mark_half_day_early_go_min']).'-'.strtotime($office_time_early_grace_time));
				// Mark half day if work duration less than ...
				if($attendance_Settings['Halfday_workduration'] == 1){
					$secs = strtotime($attendance_Settings['Halfday_workduration_min'])-strtotime("00:00:00");
					$worked_time =  strtotime($attend_array[(int)$day.'_out']) - strtotime($attend_array[(int)$day.'_in']);
					if($worked_time < $secs){
						$attend_array[(int)$day] = 'HD';
					}
				} 

				// Mark Absent if work duration less than ...
				if($attendance_Settings['absent_workduration'] == 1) {
					$secs = strtotime($attendance_Settings['absent_workduration_min'])-strtotime("00:00:00");
					$worked_time =  strtotime($attend_array[(int)$day.'_out']) - strtotime($attend_array[(int)$day.'_in']);
					if($worked_time < $secs){
						$attend_array[(int)$day] = 'A';
					}
				}
			}

			if(!empty($attend_array)){
				$attend_array['dept_id'] = $empl_dept;
				Update('kv_empl_attendancee', array('empl_id' => $userID, 'month' => get_post('month'), 'year' => get_post('year')), $attend_array);
			}
		}		
	}
}
end_table();
end_form();
end_page(); ?>