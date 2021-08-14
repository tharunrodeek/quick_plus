<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_ATTENDANCE';
$path_to_root="../..";

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
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/zklibrary.php" );

page(trans("ZKTeco Attendance Import - ZKTeco.dat"));

if(list_updated('month') || list_updated('year'))
	$Ajax->activate('totals_tbl');

//------------------------------------------------------------------------------------
if (isset($_POST['submitcsv'])){

	$attendances=array();
	$tmpname = $_FILES['filename']['tmp_name'];
	if(pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION) == 'dat'){
		$dir = dirname(__FILE__).'/backups/tmp';
		if (!file_exists($dir))	{
			mkdir ($dir,0777);
			$index_file = "<?php\nheader(\"Location: ../index.php\");\n";
			$fp = fopen($dir."/index.php", "w");
			fwrite($fp, $index_file);
			fclose($fp);
		}
		$filename = basename($_FILES['filename']['name']);				
		move_uploaded_file($tmpname, $dir."/".$filename);		
		$fp     = fopen($dir.'/'.$filename,"r");
		if(empty($fp) === false){
			while ( !feof($fp) ){
				$line = fgets($fp);
				$row =   str_getcsv($line, "\t"); 
				if(is_array($row))
					$attendances[] = $row;		
			}
		}
		fclose($fp);
		unlink($dir.'/'.$filename);
	}
	//$months_with_years_list = kv_get_months_with_years_in_fiscal_year(get_post('year'));
	//$end_of_selected_month = end_month(sql2date($months_with_years_list[(int)get_post('month')])); 
	//$begin_of_selected_month = begin_month(sql2date($months_with_years_list[(int)get_post('month')])); 
		/*$zk_ip = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'zk_ip'));
		$zk = new ZKLibrary($zk_ip, 4370);
		$zk->connect();
		$zk->disableDevice();

		//$users = $zk->getUser();
		//$zk->clearAttendance();
		$attendances = $zk->getAttendance(date2sql($begin_of_selected_month), date2sql($end_of_selected_month));	*/
		//$zk->setUser('7', '7', 'Arun', '1234', '0');
		//var_dump($attendances.$dir.'/'.$filename);
	$users = array();
	if($attendances) {
		//echo json_encode($attendances);
		foreach($attendances as $key => $attendance){
			$date_to_Day = date('d', strtotime($attendance[2]));
			$users[$attendance[0]][$date_to_Day][] = $attendance[2];
		}
		//start_table(TABLESTYLE, "width=40%");
		//$th = array(trans("User ID"),trans("Attendance"));
		//table_header($th);
		$companyBeginTime = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'BeginTime'));
		$companyEndTime = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'EndTime'));
		//echo json_encode($users);
		foreach($users as $userID => $usr){  //2018-07-22 14:04:26
			$empl_shift_time = GetDataJoinRow('kv_empl_shifts AS shift', array( 
				0 => array('join' => 'INNER', 'table_name' => 'kv_empl_job AS job','conditions'=>'`job`.`shift` = `shift`.`id`')), 
				array('`shift`.`BeginTime`, `shift`.`EndTime`, `job`.`department`'), array('`job`.`empl_id`' => $userID));
			if(empty($empl_shift_time)){
				$empl_shift_time['BeginTime'] = $companyBeginTime;
				$empl_shift_time['EndTime'] = $companyEndTime;
				$empl_shift_time['dept_id'] = GetSingleValue('kv_empl_job', 'department', array('empl_id' => $userID));
			}
			
			$attend_array = array();
			//label_row($userID , $date_wise);//echo ('-----'.$empl_shift_time['EndTime'].'ssegrser');
			if(count($usr) > 0) {
				$attend_Settings = GetAll('kv_empl_attendance_settings', array('dept_id' => $empl_shift_time['dept_id']));
				$attendance_Settings = array();
				if($attend_Settings) {
					foreach ($attend_Settings as $value) {
						$attendance_Settings[$value['option_name']] = $value['option_value'];
					}
				}
				foreach($usr as $day => $dates){//echo ('-'.$empl_shift_time['EndTime'].'ssegrser');
					if(count($dates) > 1){
						if(count($dates) == 2){
							$attend_array[(int)$day] = 'P';
							$attend_array[(int)$day.'vj_in'] = date('H:i:s', strtotime($dates[0]));
							$attend_array[(int)$day.'vj_out'] = date('H:i:s', strtotime($dates[1]));
						}else {
							$iterat = count($dates);

							$attend_array[(int)$day] = 'P';
							$attend_array[(int)$day.'vj_in'] = date('H:i:s', strtotime($dates[0]));

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
							$attend_array[(int)$day.'vj_out'] = date('H:i:s', strtotime(strtotime($dates[0])+$time_dif));
						}
					} elseif(strtotime($dates[0]) < strtotime(date('Y-m-d')) ) {
						//echo ('--'.$empl_shift_time['EndTime'].'ssegrser');
						$attend_array[(int)$day] = 'P';
						$attend_array[(int)$day.'vj_in'] = date('H:i:s', strtotime($dates[0]));
						$attend_array[(int)$day.'vj_out'] = $empl_shift_time['EndTime'];
					}
					if(!empty($attendance_Settings )) {
						//Consider Early In
						if($attendance_Settings['early_coming_punch'] == 0 && strtotime($empl_shift_time['BeginTime']) >= strtotime($attend_array[(int)$day.'vj_in'])){
							$attend_array[(int)$day.'vj_in'] = $empl_shift_time['BeginTime']; 
						}
						//Consider Early Going Punch
						if($attendance_Settings['late_going_punch'] == 0 && strtotime($empl_shift_time['EndTime']) <= strtotime($attend_array[(int)$day.'vj_out'])  ){
							$attend_array[(int)$day.'vj_out'] = $empl_shift_time['EndTime']; 
						}

						// Grace time for Late Punch in 
						if(strtotime($empl_shift_time['BeginTime']) <= strtotime($attend_array[(int)$day.'vj_in'])){

							$secs = strtotime($attendance_Settings['grace_in_time'])-strtotime("00:00:00");
							$office_time_grace_time = strtotime($empl_shift_time['BeginTime'])+$secs;

							if( $office_time_grace_time < strtotime($attend_array[(int)$day.'vj_in'])){  
								if($attendance_Settings['mark_half_day_late'] == 1) { // Mark Half day, if late coming by 
									$morning_late_time =strtotime($attendance_Settings['mark_half_day_late_min'])-strtotime("00:00:00");
									$office_time_late_grace_time = strtotime($empl_shift_time['BeginTime'])+$morning_late_time;
									if( $office_time_late_grace_time < strtotime($attend_array[(int)$day.'vj_in'])){
										//$_POST['Empl_in_'.$empl_id] = $_POST['office_begin_time'];								
										$attend_array[(int)$day] = 'HD'; //Here also half day conversion needs to be clarified..
									}
								}
							}				
						}			
					
						// Grace Time for Early Punch Go out
						if( strtotime($empl_shift_time['EndTime']) > strtotime($attend_array[(int)$day.'vj_out'])  ){ 

							$secs = strtotime($attendance_Settings['grace_out_time'])-strtotime("00:00:00");
							$office_time_grace_time = strtotime($empl_shift_time['EndTime'])-$secs;

							if( $office_time_grace_time > strtotime($attend_array[(int)$day.'vj_out'])){  // Mark Half day, if early going by 
								if($attendance_Settings['mark_half_day_early_go'] == 1){
									$evening_early_time = strtotime($attendance_Settings['mark_half_day_early_go_min'])-strtotime("00:00:00");
									$office_time_early_grace_time = strtotime($empl_shift_time['EndTime'])-$evening_early_time;

									if( $office_time_early_grace_time > strtotime($attend_array[(int)$day.'vj_out'])){
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
							$worked_time =  strtotime($attend_array[(int)$day.'vj_out']) - strtotime($attend_array[(int)$day.'vj_in']);
							if($worked_time < $secs){
								$attend_array[(int)$day] = 'HD';
							}
						} 

						// Mark Absent if work duration less than ...
						if($attendance_Settings['absent_workduration'] == 1) {
							$secs = strtotime($attendance_Settings['absent_workduration_min'])-strtotime("00:00:00");
							$worked_time =  strtotime($attend_array[(int)$day.'vj_out']) - strtotime($attend_array[(int)$day.'vj_in']);
							if($worked_time < $secs){
								$attend_array[(int)$day] = 'A';
							}
						}
					}
				}

				if(!empty($attend_array)){
					$attend_array['dept_id'] = $empl_shift_time['dept_id'];
					Update('kv_empl_attendancee', array('empl_id' => $userID, 'month' => get_post('month'), 'year' => get_post('year')), $attend_array);
				}
			}		
		}
	}
}

div_start('totals_tbl');
start_form(true);
	if (db_has_employees()) {
		start_table(TABLESTYLE_NOBORDER);
		start_row();
		kv_fiscalyears_list_cells(trans("Fiscal Year:"), 'year', null, false);
		kv_current_fiscal_months_list_cell(trans("Months"), "month", null, false, false);
	
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
	
	start_table(TABLESTYLE, "width=30%");
	file_row("Select Dat file to Import", "filename", null);

	end_table();
	br();
	submit_center('submitcsv', 'Import Attendance');
end_form();
div_end();
end_page(); 

function ProcessAttendance($from){
	$attendance_date = date2sql(add_days($from, $vj));
			$month = date("m", strtotime($attendance_date));
			$day = date("d", strtotime($attendance_date));
			$year = date("Y", strtotime($attendance_date));			
			if(isset($_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year])){
				$out_hour = ($_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year.'_out_hour'] == 0 ? 12 : $_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year.'_out_hour']);
				$in_hour = ($_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year.'_in_hour'] == 0 ? 12 : $_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year.'_in_hour']);
				$cal_BeginTime = $BeginTime = date('H:i:s', strtotime($in_hour.':'.str_pad($_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year.'_in_min'], 2, '0', STR_PAD_LEFT).' '.$_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year.'_in_ampm']));
				$cal_EndTime = $EndTime = date('H:i:s', strtotime($out_hour.':'.str_pad($_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year.'_out_min'], 2, '0', STR_PAD_LEFT).' '.$_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year.'_out_ampm']));

				//Consider Early In
				if((!isset($AttendanceSettings['early_coming_punch']) || $AttendanceSettings['early_coming_punch'] != 1 ) && strtotime($_POST[$empl_id.'_BeginTime']) >= strtotime($BeginTime)  ){ 
					$cal_BeginTime = $_POST[$empl_id.'_BeginTime']; 
				}						

				//Consider Early Going Punch
				if((!isset($AttendanceSettings['late_going_punch']) || $AttendanceSettings['late_going_punch'] != 1 ) && strtotime($_POST[$empl_id.'_EndTime']) <= strtotime($EndTime) ){
					$cal_EndTime = $_POST[$empl_id.'_EndTime']; 
				}					

				// Grace time for Late Punch in 
				if(strtotime($_POST[$empl_id.'_BeginTime']) <= strtotime($BeginTime) && isset($AttendanceSettings['grace_in_time'])){

					$secs = strtotime($AttendanceSettings['grace_in_time'])-strtotime("00:00:00");
					$office_time_grace_time = strtotime($_POST[$empl_id.'_BeginTime'])+$secs;

					if( $office_time_grace_time < strtotime($BeginTime)){  
						if($AttendanceSettings['mark_half_day_late'] == 1) { // Mark Half day, if late coming by 
							$morning_late_time = strtotime($AttendanceSettings['mark_half_day_late_min'])-strtotime("00:00:00");
							$office_time_late_grace_time = strtotime($_POST[$empl_id.'_BeginTime'])+$morning_late_time;

							if( $office_time_late_grace_time <= strtotime($BeginTime)){								
								//Here also half day conversion needs to be clarified..
								//$_POST['Empl_'.$empl_id] = 'HD';
								$_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year] = 'HD';
								$duration = (strtotime($attendance_date.' '.$_POST[$empl_id.'_EndTime'])-strtotime($attendance_date.' '.$_POST[$empl_id.'_BeginTime']))/2;
							}
						}
					}				
				}			
				
				// Grace Time for Early Punch Go out
				if( strtotime($_POST[$empl_id.'_EndTime']) > strtotime($EndTime) && isset($AttendanceSettings['grace_out_time']) ){ 

					$secs = strtotime($AttendanceSettings['grace_out_time'])-strtotime("00:00:00");
					$office_time_grace_time = strtotime($_POST[$empl_id.'_EndTime'])-$secs;

					if( $office_time_grace_time > strtotime($EndTime)){  // Mark Half day, if early going by 
						if($AttendanceSettings['mark_half_day_early_go'] == 1){
							$evening_early_time = strtotime($AttendanceSettings['mark_half_day_early_go_min'])-strtotime("00:00:00");
							$office_time_early_grace_time = strtotime($_POST[$empl_id.'_EndTime'])-$evening_early_time;

							if( $office_time_early_grace_time >= strtotime($EndTime)){
								//display_error($attendance_date.' '.$office_time_early_grace_time.'-['.strtotime($EndTime));
								$_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year] = 'HD';
								$duration = (strtotime($attendance_date.' '.$_POST[$empl_id.'_EndTime'])-strtotime($attendance_date.' '.$_POST[$empl_id.'_BeginTime']))/2;
							}
						}
					}
				}


				// Mark half day if work duration less than ...
				if(isset($AttendanceSettings['Halfday_workduration']) && $AttendanceSettings['Halfday_workduration'] == 1){
					$secs = strtotime($AttendanceSettings['Halfday_workduration_min'])-strtotime("00:00:00");
					$worked_time =  strtotime($EndTime) - strtotime($BeginTime);

					if($worked_time <= $secs){
						//display_error($worked_time.'-'.$secs);
						$_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year] = 'HD';
						$duration = (strtotime($attendance_date.' '.$_POST[$empl_id.'_EndTime'])-strtotime($attendance_date.' '.$_POST[$empl_id.'_BeginTime']))/2;
					}
				} 

				$recordTime= true;
				// Mark Absent if work duration less than ...
				if(isset($AttendanceSettings['absent_workduration']) && $AttendanceSettings['absent_workduration'] == 1) {
					$secs = strtotime($AttendanceSettings['absent_workduration_min'])-strtotime("00:00:00");
					$worked_time =   strtotime($EndTime) - strtotime($BeginTime);
					if($worked_time <= $secs){
						$_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year] = 'A';
						$recordTime= false;
					}
				}

				if(strtotime($EndTime) > strtotime($companyEndTime)){
					$duration  = strtotime($attendance_date.' '.$companyEndTime)- strtotime($attendance_date.' '.$cal_BeginTime);
					$ot = strtotime($attendance_date.' '.$cal_EndTime)-strtotime($attendance_date.' '.$companyEndTime);
					//display_error(strtotime($attendance_date.' '.$EndTime).'-'.$attendance_date.'-'.strtotime('0000-00-00 '.$hrmsetup['OT_BeginTime']));
					if($ot > $hrmsetup['OT_BeginTime']){	
						if( $hrmsetup['OT_EndTime'] < $ot)
							$sot = $hrmsetup['OT_EndTime']- $ot;
						else
							$sot = $ot-$hrmsetup['OT_BeginTime'];
							
						$ot = $hrmsetup['OT_BeginTime'];
					}else 
						$sot = 0; 
				} else{
					$ot = $sot = 0 ;
					$duration = strtotime($attendance_date.' '.$cal_EndTime)-strtotime($attendance_date.' '.$cal_BeginTime);
				}				

				if($duration>0){	
					$exists = false;
					$date_attendance = GetAll('kv_empl_attendance', array('empl_id' => $empl_id, 'a_date' => $attendance_date));
					if(!empty($date_attendance) && !isset($_POST[$empl_id.'_'.$month.'_'.$day.'_id']) ){
						foreach($date_attendance as $single){
							if(($single['in_time'] < $BeginTime && $single['out_time'] > $BeginTime) || ($single['in_time'] < $EndTime && $single['out_time'] > $EndTime)){
								$exists = true;
								$_POST[$empl_id.'_shift'] = $single['shift'];
								break;
							}
						}
					}
					$exists = check_empty_result("SELECT COUNT(id) FROM ".TB_PREF."kv_empl_salary WHERE from_date <= ".db_escape($attendance_date)." AND to_date >= ".db_escape($attendance_date));
					if(!$exists) {
						if($_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year] == 'A'){
							$duration = $ot = $sot = 0;
							if($recordTime)
								$BeginTime = $EndTime = '00:00:00';
							Delete('kv_empl_attendance', array('empl_id' => $empl_id, 'a_date' => $attendance_date));
						}
						if($ot <= 900)
							$ot = 0;
						if($sot <= 900)
							$sot = 0;					

						$array = array('in_time' => $BeginTime, 'out_time' => $EndTime,  'shift' =>$_POST[$empl_id.'_shift'],  'duration' => $duration, 'ot' => $ot, 'sot' => $sot, 'code' => $_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year]);			
						Update('kv_empl_attendance', array('empl_id' => $empl_id, 'dimension' => $_POST['dimension_id'], 'dimension2' => $_POST['dimension2_id'], 'a_date' => $attendance_date), $array);
					} else {
						$each[] = sql2date($attendance_date)." ".trans("has some attendance Already, we can't Override it. Check inquiry page for the selected date").'<br>';
					}
				} elseif($_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year] != 'A') {
					$each[] = sql2date($attendance_date);
				}
			}

}?>