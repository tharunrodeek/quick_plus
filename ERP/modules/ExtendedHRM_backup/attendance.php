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
include($path_to_root . "/includes/ui.inc");
add_access_extensions();
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
$js = get_js_date_picker();	
page(trans("Attendance"), false, false, "", $js);
$all_settings =  GetAll('kv_empl_option');
	$hrmsetup = array(); 
	foreach($all_settings as $settings){
		$data_offdays = @unserialize(base64_decode($settings['option_value']));
		if ($data_offdays !== false) {
			$hrmsetup[$settings['option_name']] = unserialize(base64_decode($settings['option_value']));
		} else {
			$hrmsetup[$settings['option_name']] = $settings['option_value']; 
		}
	}
$dim = get_company_pref('use_dimension');
if(list_updated('from') || get_post('from')){
	$_POST['from'] = get_post('from');
	$Ajax->activate('details');
}


if (isset($_POST['addupdate']) ) {
	$employees = array();
	foreach($_POST as $empls =>$val) {			
		if (substr($empls,0,2) == 'E_'){
			$empl_id_tmp = substr($empls,2);
			$pos = strpos($empl_id_tmp, '_');
			$empl_id = substr($empls, 2,$pos);
			if(!in_array($empl_id, $employees))
				$employees[] = $empl_id;
		}
	}
	

	$negative = $AttendanceSettings = array();

	if(get_post('dept_id')> 0 ){
		$att_dept = GetAll('kv_empl_attendance_settings', array('dept_id' => $_POST['dept_id']));		
		foreach($att_dept as $settings){			
			$AttendanceSettings[$settings['option_name']] = $settings['option_value']; 			
		}
	}
	
	$companyEndTime = $hrmsetup['EndTime'];
	foreach ($employees as $empl_id) {		
		
		$each = array();
		if(empty($AttendanceSettings)){
			$dept_id = GetSingleValue('kv_empl_job', 'department', array('empl_id'=> $empl_id));
			$att_dept = GetAll('kv_empl_attendance_settings', array('dept_id' => $dept_id));		
			foreach($att_dept as $settings){			
				$AttendanceSettings[$settings['option_name']] = $settings['option_value']; 			
			}
		}
		$companyEndTime = $_POST[$empl_id.'_EndTime'];
		for($vj=0;$vj<7;$vj++){
			$attendance_date = date2sql(add_days($_POST['from'], $vj));
			$attendance_date_plus_1 = date2sql(add_days($_POST['from'], $vj+1));
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

							if( $office_time_late_grace_time <= strtotime($BeginTime)){	//Here also half day conversion needs to be clarified..
								$_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year] = 'HD';
								$duration = (strtotime($attendance_date.' '.$_POST[$empl_id.'_EndTime'])-strtotime($attendance_date.' '.$_POST[$empl_id.'_BeginTime']))/2;
								if($duration <0 )
									$duration = (strtotime($attendance_date_plus_1.' '.$_POST[$empl_id.'_EndTime'])-strtotime($attendance_date.' '.$_POST[$empl_id.'_BeginTime']))/2;
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
								$_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year] = 'HD';
								$duration = (strtotime($attendance_date.' '.$_POST[$empl_id.'_EndTime'])-strtotime($attendance_date.' '.$_POST[$empl_id.'_BeginTime']))/2;
								if($duration <0 )
									$duration = (strtotime($attendance_date_plus_1.' '.$_POST[$empl_id.'_EndTime'])-strtotime($attendance_date.' '.$_POST[$empl_id.'_BeginTime']))/2;
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
						if($duration <0 )
							$duration = (strtotime($attendance_date_plus_1.' '.$_POST[$empl_id.'_EndTime'])-strtotime($attendance_date.' '.$_POST[$empl_id.'_BeginTime']))/2;
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
					if($duration <0 )
						$duration  = strtotime($attendance_date_plus_1.' '.$companyEndTime)- strtotime($attendance_date.' '.$cal_BeginTime);
						
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
					if($duration <0 )
						$duration  = strtotime($attendance_date_plus_1.' '.$cal_EndTime)- strtotime($attendance_date.' '.$cal_BeginTime);
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
						
						if(isset($_POST['SL_'.$empl_id.'_'.$month.'_'.$day.'_'.$year]) && $_POST['SL_'.$empl_id.'_'.$month.'_'.$day.'_'.$year] == 'HD'){
							$duration =  (strtotime($_POST[$empl_id."_EndTime"]) - strtotime($_POST[$empl_id."_BeginTime"]))/2;
							if($duration <0 )
								$duration  = (strtotime($attendance_date_plus_1.' '.$_POST[$empl_id."_EndTime"])- strtotime($attendance_date.' '.$_POST[$empl_id."_BeginTime"]))/2;
							$ot = $sot = 0;
						}
						$array = array('in_time' => $BeginTime, 'out_time' => $EndTime,  'shift' =>$_POST[$empl_id.'_shift'],  'duration' => $duration, 'ot' => $ot, 'sot' => $sot, 'code' => $_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year]);	
						if(isset($_POST['SL_'.$empl_id.'_'.$month.'_'.$day.'_'.$year]) && $_POST['SL_'.$empl_id.'_'.$month.'_'.$day.'_'.$year] == 'HD'){
							$array['description'] = 'Full day Sick leave exhausted. It will be considered  as Half day';
						}elseif(isset($_POST['SL_'.$empl_id.'_'.$month.'_'.$day.'_'.$year]) && $_POST['SL_'.$empl_id.'_'.$month.'_'.$day.'_'.$year] == 'A'){
							$array['description'] = 'sick leave Completely exhausted. It will be considered as Absent';
						}
						
						if($_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year] == 'SL'){
							$array['in_time'] = $array['out_time'] = '00:00:00';
						}
					/*	if($_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year]=='A'){
							$array['in_time'] =$array['out_time'] = '00:00:00';	
							$array['duration'] = $array['ot'] = $array['sot'] = 0;
						}	*/							
						
						Update('kv_empl_attendance', array('empl_id' => $empl_id, 'dimension' => $_POST['dimension_id'], 'dimension2' => $_POST['dimension2_id'], 'a_date' => $attendance_date), $array);
					} else {
						$each[] = sql2date($attendance_date)." ".trans("has some attendance Already, we can't Override it. Check inquiry page for the selected date").'<br>';
					}
				} elseif($_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year] != 'A' && $_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year] != 'SL') {
					$each[] = sql2date($attendance_date).'-'.$_POST['E_'.$empl_id.'_'.$month.'_'.$day.'_'.$year];
				}
			}
		}
		if(!empty($each))
			$negative[$empl_id] = $each;
	}	
	if(!empty($negative)){
		display_error(trans("Some date times are wrong kindly Check and Update them properly"));
		foreach($negative as $empl => $errors){
			display_error($empl.' - '.implode(', ', $errors));
		}
	} else 
		display_notification(trans("Attendance Register Saved Successfully"));
}

start_form(true);

if (db_has_employees()) {
	if (isset($_POST['dept_id']) && $_POST['dept_id'] >0) {
		$_POST['dept_id'] = input_num('dept_id');
	}
	start_table(TABLESTYLE2);
		start_row();   
			date_cells(trans("Date") . ":", 'from', null, null, 0,0,0, null, true);
			department_list_cells(trans("Select a Department")." :", 'dept_id', null, trans("No Department"), true, check_value('show_inactive'));
			employee_list_cells(trans("Select an Employee"). " :", 'empl_id', null,	trans("Select an Employee"), true, check_value('show_inactive'), false, false,true);
			$new_item = get_post('dept_id')=='';			
			if ($dim >= 1){
				dimensions_list_cells(trans("Dimension")." 1", 'dimension_id', null, true, '', false, 1, true);
				if ($dim > 1)
					dimensions_list_cells(trans("Dimension")." 2", 'dimension2_id', null, true, false, false, 2, true);
			}
			if ($dim < 1)
				hidden('dimension_id', 0);
			if ($dim < 2)
				hidden('dimension2_id', 0);		
		end_row();	
	end_table();	
	br(2);		
	
div_start('details');
	start_table(TABLESTYLE);
		echo '<tr> <td class="tableheader"> '.trans("Empl ID").'</td> <td class="tableheader"> '.trans("Employee Name").' </td> ';
		$date_ar = array(date2sql($_POST['from']));
		for($vj=0;$vj<7;$vj++){		
			$date_at = add_days($_POST['from'], $vj);
			$date_ar[] = date2sql($date_at);			
			echo '<td class="tableheader" >'.$date_at.' <br> '.trans(date("l", strtotime(date2sql($date_at)))) .'</td>';
		}
		echo '</tr>' ;
		if(get_post('dept_id')> 0 || get_post('empl_id') > 0 ) {			
			if(( list_updated('empl_id') && $_POST['empl_id'] > 0 ) || get_post('empl_id') > 0 ){	//$selected_empl = GetRow('kv_empl_info', array('empl_id' => ));
				$sql = "SELECT DISTINCT info.empl_id, job.joining,job.weekly_off, job.al, job.sl, job.slh, job.ml, job.hl, info.status, info.date_of_status_change, CONCAT(info.empl_firstname, ' ' , info.empl_lastname) AS empl_name FROM ".TB_PREF."kv_empl_job job, ".TB_PREF."kv_empl_info info WHERE info.id= job.empl_id AND  info.id=".get_post('empl_id');
				$selected_empl =  db_query($sql, "The employee table is inaccessible");
			} else {				
				$selected_empl = kv_get_employees_list_based_on_dept(get_post('dept_id'));	
			}

           // dd(get_post('dept_id'));
			$f_year = GetRow('fiscal_year', array('begin' => array(date2sql($_POST['from']), '<='), 'end' => array(date2sql($_POST['from']), '>=')));
			$year = $f_year['id'];
			
			while ($row = db_fetch_assoc($selected_empl)) {	
				$month = (int)date('m', strtotime($_POST['from']));	
				//display_error($month.'---'.$year);			
				$sal_id = GetSingleValue('kv_empl_salary', 'id', array('empl_id' => $row['empl_id'], 'month' => $month, 'year' => $year, 'dimension' => 0, 'dimension2' => 0 ));
				if( $row['joining'] <= date2sql($_POST['from']) && ($row['status'] == 1 || ($row['status']>1 && $row['date_of_status_change'] >= date2sql($_POST['from'])))){
				$sql = "SELECT * FROM ".TB_PREF."kv_empl_attendance WHERE empl_id=".db_escape($row['empl_id'])." AND a_date IN ('". implode("', '", $date_ar) . "') AND dimension = ".db_escape($_POST['dimension_id']);//." AND dimension2=".db_escape($_POST['dimension2_id']);
				$res = db_query($sql, "Can't get attendance");
				$final = array();
				while($row2 = db_fetch($res)){
					$final[$row2['a_date']] = $row2;
				}
				$where = array('`job`.`empl_id`' => $row['empl_id']);
				if(get_post('dimension') > 0 )
					$where['`shift`.`dimension`'] = get_post('dimension'); 
				//if(get_post('dimension2') > 0 )
					//$where['`shift`.`dimension2`'] = get_post('dimension2'); 
				
				$empl_shift_time = GetDataJoinRow('kv_empl_shifts AS shift', array(0 => array('join' => 'INNER', 'table_name' => 'kv_empl_job AS job','conditions'=>'`job`.`shift` = `shift`.`id`')),
					array('`shift`.`BeginTime`, `shift`.`EndTime`, `job`.`empl_id`, `job`.`shift`'), $where);
				if(empty($empl_shift_time)){
					$empl_shift_time['BeginTime'] = $hrmsetup['BeginTime'];
					$empl_shift_time['EndTime'] = $hrmsetup['EndTime'];				
					$empl_shift_time['shift'] = 0;				
				}	
				hidden($row['empl_id'].'_BeginTime', $empl_shift_time['BeginTime']);
				hidden($row['empl_id'].'_EndTime', $empl_shift_time['EndTime']);	
				hidden($row['empl_id'].'_shift', $empl_shift_time['shift']);	

				echo '<tr style="border-bottom: 1px solid #dcdbda;"> <td> '.$row['empl_id'].'</td><td>'.$row['empl_name'].'</td>';
				$sql2 = "SELECT MAX(out_time) AS outt, a_date FROM ".TB_PREF."kv_empl_attendance WHERE empl_id=".db_escape($row['empl_id'])." AND a_date IN ('".implode("', '", $date_ar). "')";

				if(isset($_POST['dimension']) && $_POST['dimension'] > 0 )
					$sql2 .=" AND dimension <> ".db_escape($_POST['dimension_id']);
				$sql2 .=" GROUP BY a_date";
				$res2 = db_query($sql2, "Can't get attendance");
				$final2 = array();
				while($row3 = db_fetch($res2)){
					$final2[$row3['a_date']] = $row3['outt'];
				}
				//display_error(json_encode($row));
				$from_d = date2sql($_POST['from']);
				$to_d = date2sql(add_days($_POST['from'], 6));
				for($vj=0;$vj<7;$vj++){		
					$attendance_date = date2sql(add_days($_POST['from'], $vj));
					$month = date("m", strtotime($attendance_date));
					$day = date("d", strtotime($attendance_date));
					$year = date("Y", strtotime($attendance_date));
					$day_letters = date("D", strtotime($attendance_date)) ;
					
					if(isset($final2[$attendance_date]) && !isset($final[$attendance_date]['in_time'])){
						$final[$attendance_date]['in_time'] = $final2[$attendance_date];
					}elseif(!isset($final[$attendance_date]['in_time'])){
						$final[$attendance_date]['in_time'] = $empl_shift_time['BeginTime'];						
					}
					if(isset($final[$attendance_date]['id']) && $final[$attendance_date]['id'] > 0)
						hidden($row['empl_id'].'_'.$month.'_'.$day.'_id', $final[$attendance_date]['id']);
					$off_dates=unserialize(base64_decode($row['weekly_off']));
					//display_error($off_dates);
					$disabled = (in_array($day_letters, $off_dates) ? array('inactive' => 1) :(isset($final[$attendance_date]) ? $final[$attendance_date] : null));
					//display_error($sal_id);
					if($row['status'] > 1 && $row['date_of_status_change'] <= $attendance_date || $sal_id || $month != date('m', strtotime(date2sql($_POST['from']))))
						$disabled = array('inactive' => 1);
					//display_error($_POST['E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year]);
					
					if(list_updated('E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year)){	
						if(get_post('E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year) == 'AL' ) {// For AL
							$sql_sss = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_attendance WHERE empl_id = ".db_escape($row['empl_id'])." AND (a_date NOT BETWEEN ".db_escape($from_d)." AND ".db_escape($to_d)." ) AND a_date >= ".db_escape($f_year['begin'])." AND a_date <= ".db_escape($f_year['end'])." AND code = 'AL' ";
							$res = db_query($sql_sss, "Can't get actual al");
							if(db_num_rows($res)> 0  && $srow = db_fetch($res)){
								$actual_al = $srow[0];
							} else
								$actual_al = 0;
							
							for($js=0;$js<7;$js++){
								$attendance_datee = date2sql(add_days($_POST['from'], $js));
								$dayy = date("d", strtotime($attendance_datee));
								if(get_post('E_'.$row['empl_id'].'_'.$month.'_'.$dayy.'_'.$year) == 'AL')
									$actual_al++; 								
							}
							if($row['al']+1 <= $actual_al){
								display_warning(trans("Sorry the maximum annual leave limit reached. Here after Annual leaves will be marked as Absent"));
								$disabled['code'] = $_POST['E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year] = 'A';
							} else 
								$disabled['code'] = $_POST['E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year];
						} elseif(get_post('E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year) == 'SL' ){		//For SL and SLH
							$sql_sl = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_attendance WHERE empl_id = ".db_escape($row['empl_id'])." AND (a_date NOT BETWEEN ".db_escape($from_d)." AND ".db_escape($to_d)." ) AND a_date >= ".db_escape($f_year['begin'])." AND a_date <= ".db_escape($f_year['end'])." AND code = 'SL' ";
							$res = db_query($sql_sl, "Can't get actual al");
							if(db_num_rows($res)> 0  && $srow = db_fetch($res)){
								$actual_sl = $srow[0];
							} else
								$actual_sl = 0;
							
							for($js=0;$js<7;$js++){
								$attendance_datee = date2sql(add_days($_POST['from'], $js));
								$dayy = date("d", strtotime($attendance_datee));
								if(get_post('E_'.$row['empl_id'].'_'.$month.'_'.$dayy.'_'.$year) == 'SL' )
									$actual_sl++; 								
							}
							
							if($row['sl']+1 <= $actual_sl){
								if(($row['sl']+$row['slh']+1) <= $actual_sl){
									$disabled['code'] = $_POST['E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year] = 'A';
									display_warning(trans("Sorry the maximum Sick leave limit reached. Here after sick leaves will be marked as Absent"));
									$_POST['SL_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year] = 'A';
								}else {
									$_POST['SL_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year] = 'HD';
									$disabled['code'] = $_POST['E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year];
									display_warning(trans("Sorry the maximum Sick leave limit reached. The Sick leaves will be half paid"));
								}							
							}
						}elseif(get_post('E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year) == 'ML' ){		//For Maternity Leave
							$sql_sl = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_attendance WHERE empl_id = ".db_escape($row['empl_id'])." AND (a_date NOT BETWEEN ".db_escape($from_d)." AND ".db_escape($to_d)." ) AND a_date >= ".db_escape($f_year['begin'])." AND a_date <= ".db_escape($f_year['end'])." AND code = 'ML' ";
							$res = db_query($sql_sl, "Can't get actual al");
							if(db_num_rows($res)> 0  && $srow = db_fetch($res)){
								$actual_ml = $srow[0];
							} else
								$actual_ml = 0;
							
							for($js=0;$js<7;$js++){
								$attendance_datee = date2sql(add_days($_POST['from'], $js));
								$dayy = date("d", strtotime($attendance_datee));
								if(get_post('E_'.$row['empl_id'].'_'.$month.'_'.$dayy.'_'.$year) == 'ML' )
									$actual_ml++; 								
							}
							
							if($row['ml']+1 <= $actual_ml){								
								$disabled['code'] = $_POST['E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year] = 'A';
								display_warning(trans("Sorry the maximum Materinity leave limit reached. Here after maternity leaves will be marked as Absent"));
								hidden('ML_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year, 'A');															
							}
						}elseif(get_post('E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year) == 'HL' ){		//For Hajj Leave
							$sql_sl = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_attendance WHERE empl_id = ".db_escape($row['empl_id'])." AND (a_date NOT BETWEEN ".db_escape($from_d)." AND ".db_escape($to_d)." ) AND a_date >= ".db_escape($f_year['begin'])." AND a_date <= ".db_escape($f_year['end'])." AND code = 'HL' ";
							$res = db_query($sql_sl, "Can't get actual al");
							if(db_num_rows($res)> 0  && $srow = db_fetch($res)){
								$actual_hl = $srow[0];
							} else
								$actual_hl = 0;
							
							for($js=0;$js<7;$js++){
								$attendance_datee = date2sql(add_days($_POST['from'], $js));
								$dayy = date("d", strtotime($attendance_datee));
								if(get_post('E_'.$row['empl_id'].'_'.$month.'_'.$dayy.'_'.$year) == 'HL' )
									$actual_hl++; 								
							}
							
							if($row['hl']+1 <= $actual_hl){								
								$disabled['code'] = $_POST['E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year] = 'A';
								display_warning(trans("Sorry the maximum Hajj leave limit reached. Here after Hajj leaves will be marked as Absent"));
								hidden('HL_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year, 'A');															
							}
						}else 
							$disabled['code'] = $_POST['E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year];
						
					} 	//else				
						//unset($_POST['E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year]);
					if(isset($_POST['SL_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year]))
						hidden('SL_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year, $_POST['SL_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year]);
					
					dropdown_attendance_mark_cells(null, 'E_'.$row['empl_id'].'_'.$month.'_'.$day.'_'.$year,null, true, true, true, $disabled, $empl_shift_time);						
				}
				echo '</tr>' ;
			}
		}
		} else {
			echo '<tr> <td colspan="9" style="text-align:center;" > '.trans("Either select a Department or an Employee to continue"). '</td> </tr>';
		}		
	end_table();	
	br(2);
	if(get_post('dept_id')> 0 || get_post('empl_id') > 0 )
		submit_center('addupdate', trans("Submit Attendance"), true, '', 'default');
div_end();
}
else
{
    display_notification(trans("There is no employe exists, Create employe and try again!!!"));
}
end_form();
end_page(); 
?>
<style>
table.tablestyle tr td:nth-child(3), table.tablestyle tr td:nth-child(4), table.tablestyle tr td:nth-child(5), 
table.tablestyle tr td:nth-child(6), table.tablestyle tr td:nth-child(7), table.tablestyle tr td:nth-child(8), 
table.tablestyle tr td:nth-child(9) { min-wdith: 180px;}
select{ min-height:23px !important; width: auto !important;}
</style>