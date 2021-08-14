<?php
global $hrm_attendance_mark; 

/*$hrm_attendance_mark = array(
    'P'  => trans("Present"),
    'A'  => trans("Absent"),
    'AL' => trans("Annual Leave"),
    'ML' => trans("Maternity Leave"),
    'SL' => trans("Sick Leave"),
    'HL' => trans("Hajj Leave"),
	/*'HD' => trans(" Half Day")*/
//);*/

function dropdown_attendance_mark($name, $selected_id, $submit_on_change=false, $disabled=false){
	global $hrm_attendance_mark;
	$options = array(
	    'select_submit'=> $submit_on_change,
		'disabled' => $disabled
	);
	$leaveTypes = array();
	$sql = "SELECT char_code,description FROM ".TB_PREF."kv_empl_leave_types order by description ASC" ;
	$res =  db_query($sql , "Can't get result");
	 if(db_num_rows($res)){
		while($cont = db_fetch($res))
		{
			$leaveTypes[$cont[0]] = $cont[1];
		}
	}

	return array_selector($name, $selected_id,$leaveTypes, $options);
}

function dropdown_attendance_mark_cells($label, $name, $selected_id, $submit_on_change=false, $inTime=false, $outTime=false,$attendance=null, $empl_shift_time=null){
	if ($label != null)
		echo "<td>$label</td>\n";
		
	$inTime = (isset($empl_shift_time['BeginTime']) ? $empl_shift_time['BeginTime'] : null);
	//display_error($label.'-'.(isset($attendance['in_time']) ? $attendance['in_time'] : $inTime));
	$OutTime = (isset($empl_shift_time['EndTime']) ? $empl_shift_time['EndTime'] : null);
	echo "<td> <div>". trans("Status"). ' '.dropdown_attendance_mark($name, (isset($attendance['code'])? $attendance['code'] : null), $submit_on_change, ((isset($attendance['inactive']) && $attendance['inactive'] == 1 ) ? true : false)).'</div>';
	if($inTime)
		echo '<div>'. trans("In Time"). ' '.TimeDropDown($name.'_in', (isset($attendance['in_time']) ? $attendance['in_time'] : $inTime), false, ((isset($attendance['inactive']) && $attendance['inactive'] == 1 ) ? true : false)).'</div>';
	if($outTime)
		echo '<div>'.trans("Out Time"). ' '. TimeDropDown($name.'_out', (isset($attendance['out_time']) ? $attendance['out_time'] : $OutTime), false, ((isset($attendance['inactive']) && $attendance['inactive'] == 1 ) ? true : false)).'</div>';
	echo "</td>\n";
}

function dropdown_attendance_mark_row($label, $name, $selected_id=null, $submit_on_change=false, $inTime=false, $outTime=false,$attendance=null, $empl_shift_time =null){
	echo "<tr><td class='label'>$label</td>";
	dropdown_attendance_mark_cells(null, $name, $selected_id, $submit_on_change, $inTime, $outTime,$attendance, $empl_shift_time);
	echo "</tr>\n";
}

function _24to12($time){ // 24-hour time to 12-hour time 
	return date("g:i A", strtotime($time));
}

function _12to24($time){
	return date("H:i", strtotime($time));
}

function TimeSeparator($time, $type){
	return date($type, strtotime($time));
}

function TimeDropDown_cells($label, $name, $selected_id=null, $submit_on_change=false, $noampm=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo TimeDropDown($name, $selected_id, $submit_on_change, false, $noampm);
	echo "</td>\n";
}

function TimeDropDown_row($label, $name, $selected_id=null, $submit_on_change=false, $noampm=false){
	echo "<tr><td class='label'>$label</td>";
	TimeDropDown_cells(null, $name, $selected_id, $submit_on_change, $noampm);
	echo "</tr>\n";
}

function TimeDropDown($name, $time, $submit_on_change=false, $disabled=false, $noampm=false){
	if($time == null)
		$time =  date('g:i A');

	$hours =  array(0,1,2,3,4,5,6,7,8,9,10,11);
	$mins = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59);
	$ampm = array('AM' => 'AM', 'PM' => 'PM');
	//$selected_hour = $selected_min = $selected_ampm = 0;
	unset($_POST[$name.'_min']);
	unset($_POST[$name.'_hour']);
	unset($_POST[$name.'_ampm']);
	$selected_hour = TimeSeparator($time, 'g');
	$selected_min = (int)TimeSeparator($time, 'i');
	$selected_ampm = TimeSeparator($time, 'A');
	//display_error($time.'--'.$_POST[$name.'_min'].':'.$selected_min.':'.$selected_ampm);
	return '<div>'.array_selector($name.'_hour', $selected_hour, $hours, 
		array( 
			'select_submit'=> $submit_on_change,
			'async' => false,
			'disabled' => $disabled ) ) . 
		array_selector($name.'_min', $selected_min, $mins, 
			array( 
				'select_submit'=> $submit_on_change,
				'async' => false,
				'disabled' => $disabled ) ).
	(!$noampm ? 	array_selector($name.'_ampm', $selected_ampm, $ampm, 
			array( 
				'select_submit'=> $submit_on_change,
				'async' => false,
				'disabled' => $disabled ) ) : '' );
	
}

function Get_Monthly_WorkingHours($year, $month,$sec=false, $shift=false){
	
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
	if($shift){
		$shiftDetails = GetRow('kv_empl_shifts', array('id' => $shift));
		$beginTime = $shiftDetails['BeginTime'];
		$endTime = $shiftDetails['EndTime'];
	}else {
		$beginTime = $hrmsetup['BeginTime'];
		$endTime = $hrmsetup['EndTime'];
	}
	$total_days = 0;

	$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
 	$ext_year = date("Y", strtotime($months_with_years_list[(int)$month]));
	$workingDayHours = $payable_days = 0;
	if($hrmsetup['monthly_choice'] == 2){
		$firstMonth = $month;		
		$secondMonth = $month+1;
	} elseif($hrmsetup['monthly_choice'] == 3) {
		$firstMonth = $month-1;
		$secondMonth = $month;
	} else {
		$firstMonth = $secondMonth = $month;
	}	
	$firstMthDays = date("t", strtotime($year."-".$firstMonth."-01"));		
	$kv = $hrmsetup['BeginDay'];	
	$total_days =  date("t", strtotime($year."-".$firstMonth."-01"));

	for($kv;$kv<=$firstMthDays;$kv++){			
 		if(date("D", strtotime($ext_year."-".$firstMonth."-".$kv))  == $hrmsetup['weekly_off'] ){
			$payable_days++;
			$workingDayHours += strtotime($endTime)-strtotime($beginTime);
 		}else{
 			$workingDayHours += strtotime($endTime)-strtotime($beginTime);
 			$payable_days++;
 		}
	}		
		
	if($hrmsetup['monthly_choice'] != 1){ 		
		$total_days = $hrmsetup['EndDay'];
 		for($kv=1; $kv<=$total_days; $kv++){
	 		$day_letters = date("D", strtotime($ext_year."-".$secondMonth."-".$kv));
	 		if(in_array($day_letters, $hrmsetup['weekly_off'] ) ){
				$payable_days++;
				$workingDayHours += strtotime($endTime)-strtotime($beginTime);
	 		}else{
	 			$workingDayHours += strtotime($endTime)-strtotime($beginTime);
	 			$payable_days++;
	 		}
	 	}		
	}
	
 	if($sec){
 		return array($workingDayHours, $payable_days);
 	}else{
 		$whours = floor($workingDayHours / 3600);
		$wmins = floor($workingDayHours / 60 % 60);
		//$wsecs = floor($workingDayHours % 60);
		$Working_hours_final = $whours.':'.$wmins; //.':'.$wsecs;
	 	return array($Working_hours_final, $payable_days);
 	} 	
}

function AvailableLeaveDays($empl_id, $year, $except_this_month=false, $total=true, $BeginDay=null, $EndDay=null){
	$grade_leaves = GetRow('kv_empl_job', array('empl_id' => $empl_id));

	$selected_empl = get_employee_whole_attendance($empl_id, $year, $except_this_month);
	$leave_encashed_days = GetRow('kv_empl_leave_encashment', array('empl_id' => $empl_id, 'year' => $year));
	
	if(!empty($selected_empl)){		
		$counted_month = 0;
		$months_list = kv_get_months_in_fiscal_year();			
		$months_array_count = array_keys($months_list);			
		$sql = "SELECT SUM(`AL`) AS AL, SUM(`SL`) AS SL, SUM(`HL`) AS HL, SUM(`ML`) AS ML, COUNT(`month`) AS months FROM ".TB_PREF."kv_empl_salary WHERE `id` <= (SELECT MAX(`id`) FROM ".TB_PREF."kv_empl_salary WHERE empl_id =".$empl_id." AND year = ".$year. ($except_this_month ? " AND month <".(int)$except_this_month : '') .") AND empl_id =".$empl_id." AND year = ".$year." AND net_pay <> -1 " ;
		$res =  db_query($sql , "Can't get result");
		if($past_leave_sum =  db_fetch_assoc($res)){
			if($except_this_month)
				$counted_month = $past_leave_sum['months'] + 1;
			elseif($past_leave_sum['months'])
				$counted_month = $past_leave_sum['months'];
			else
				$counted_month = 1; 			
		} else
			$counted_month = 1; 		
		$result_ar = array('months' => $counted_month, 'al' => (($counted_month*round(($grade_leaves['al']/12), 2) )- $past_leave_sum['AL']), 'sl' =>(($counted_month*round(($grade_leaves['sl']/12), 2)) - $past_leave_sum['SL']), 'ml' => (($counted_month*round(($grade_leaves['ml']/12), 2)) - $past_leave_sum['ML']), 'hl' => (($counted_month*round(($grade_leaves['hl']/12), 2)) - $past_leave_sum['HL']) );
		if($leave_encashed_days['carry_forward'] == 0)
			$result_ar['al'] =  ($leave_encashed_days['payable_days'] ? ($result_ar['al'] - $leave_encashed_days['payable_days']) : $result_ar['al']);
		else
			$result_ar['al'] =  ($leave_encashed_days['payable_days'] ? ($result_ar['al'] + $leave_encashed_days['payable_days']) : $result_ar['al']);
			
		$past_leave_encashment = GetDataFilter('kv_empl_leave_encashment', array('SUM(`payable_days`) AS PayableDays', 'id'), array('empl_id' => $empl_id, 'carry_forward' => 1, '!inactive'));
		if(!empty($past_leave_encashment)){
			$result_ar['al'] =  ($past_leave_encashment[0]['PayableDays'] ? ($result_ar['al'] + $past_leave_encashment[0]['PayableDays']) : $result_ar['al']);
			$result_ar['id'] = $past_leave_encashment[0]['id'];
		}		
		return $result_ar;	
	} else{
		return array('months' => 1, 'al' => round(($grade_leaves['al']/12), 2), 'sl' => round(($grade_leaves['sl']/12), 2), 'ml' => round(($grade_leaves['ml']/12), 2),'hl' => round(($grade_leaves['hl']/12), 2));
	}	
}

function Get_Monthly_EmployeeWorkingHours($empl_id, $year, $month, $sec=false, $Empl_job_row = false){
	$total_days =  date("t", strtotime($year."-".$month."-01"));
	$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
 	$ext_year = date("Y", strtotime($months_with_years_list[(int)$month]));

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
	
	$empl_shift_time = GetDataJoinRow('kv_empl_shifts AS shift', array( 0 => array('join' => 'INNER', 'table_name' => 'kv_empl_job AS job','conditions'=>'`job`.`shift` = `shift`.`id`')), 
			array('`shift`.`BeginTime`, `shift`.`EndTime`, `job`.`empl_id`'), array('`job`.`empl_id`' => $empl_id));
	if(empty($empl_shift_time)){
		$empl_shift_time['BeginTime'] = $hrmsetup['BeginTime'] ;
		$empl_shift_time['EndTime'] = $hrmsetup['EndTime'] ;
	}
	
	if($hrmsetup['monthly_choice'] == 2){
		$firstMonth = $month;		
		$secondMonth = $month+1;
	} elseif($hrmsetup['monthly_choice'] == 3) {
		$firstMonth = $month-1;
		$secondMonth = $month;
	} else {
		$firstMonth = $secondMonth = $month;
	}
		
	$firstMthDays = date("t", strtotime($year."-".$firstMonth."-01"));	
	$workingDayHours = $OT_hours = $payable_days = $allowed_gl_days = $allowed_cl_days =  $allowed_ml_days = 0;
	$details_single_empl = GetRow('kv_empl_attendancee', array('month' => $firstMonth, 'year' => $year, 'empl_id' => $empl_id));	
	$AvailableLeaveDays = AvailableLeaveDays($empl_id, $year, $month, true, $hrmsetup['BeginDay'], $hrmsetup['EndDay']);
	//display_error(json_encode($AvailableLeaveDays));
	$total_days =  date("t", strtotime($year."-".$month."-01"));	
	
	if($details_single_empl){
		
		$sql_query = "SELECT date FROM ".TB_PREF."kv_empl_gazetted_holidays WHERE (date BETWEEN '".date("Y-m-d", strtotime($ext_year."-".$firstMonth."-".$empl_shift_time['BeginTime']))."' AND '".date("Y-m-d", strtotime($ext_year."-".$secondMonth."-".$empl_shift_time['EndTime']))."' ) ";
		$gazetted_leaves = array();
		$result = db_query($sql_query, "Can't get results");
		if(db_num_rows($result)){
			while($cont = db_fetch($result))
				$gazetted_leaves[] = $cont[0];
		}
		
			
		$kv = $hrmsetup['BeginDay'];	
	
		for($kv; $kv<=$firstMthDays; $kv++){
		 	if(strtotime(date('Y-m-d')) < strtotime($ext_year."-".$firstMonth."-".$kv))
		 		continue;
			$day_letters = date("D", strtotime($ext_year."-".$firstMonth."-".$kv)); 			
			$attendance_date = date("Y-m-d", strtotime($ext_year."-".$firstMonth."-".$kv)); 			
			if(in_array($day_letters, $hrmsetup['weekly_off'] ) ){
				if($Empl_job_row['joining'] <= $attendance_date && ($Empl_job_row['status'] == 1 || ($Empl_job_row['status']>1 && $Empl_job_row['date_of_status_change'] >= $attendance_date))){
					$workingDayHours += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
					$payable_days++;
				}		 		
			} elseif($details_single_empl[$kv] == 'P'){ 			
		 		$endTime =  ( strtotime($empl_shift_time['EndTime']) <= strtotime($details_single_empl[$kv.'vj_out']) ? $empl_shift_time['EndTime'] : $details_single_empl[$kv.'vj_out']);
		 		$workingDayHours += strtotime($endTime) - strtotime($empl_shift_time['BeginTime']);
		 		$OT_hours += (strtotime($empl_shift_time['EndTime']) <= strtotime($details_single_empl[$kv.'vj_out']) ? strtotime($details_single_empl[$kv.'vj_out'])-strtotime($empl_shift_time['EndTime']) : 0);
		 		$payable_days++;
		 	} elseif( $details_single_empl[$kv] == 'HD'){
		 		$full_day_hours = strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
		 		$workingDayHours += $full_day_hours/2;
		 		$OT_hours += (strtotime($empl_shift_time['EndTime']) <= strtotime($details_single_empl[$kv.'vj_out']) ? strtotime($details_single_empl[$kv.'vj_out'])-strtotime($empl_shift_time['EndTime']) : 0);
		 		$payable_days += 0.5;
		 	} elseif($details_single_empl[$kv] == 'OD' || ( !empty($gazetted_leaves) && in_array(date("Y-m-d", strtotime($ext_year."-".$firstMonth."-".$kv)), $gazetted_leaves)) ) {
		 		$workingDayHours += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
		 		$payable_days++;
		 	}elseif($details_single_empl[$kv] == 'AL' && $AvailableLeaveDays['al'] >0 && $allowed_gl_days < $AvailableLeaveDays['al'])	{
		 		$workingDayHours += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
		 		$payable_days++;
		 		$allowed_gl_days++;
		 	}elseif($details_single_empl[$kv] == 'SL' && $AvailableLeaveDays['sl'] > 0 && $allowed_sl_days < $AvailableLeaveDays['sl'])	{
		 		$workingDayHours += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
		 		$payable_days++;
		 		$allowed_sl_days++;
		 	}elseif($details_single_empl[$kv] == 'HL' && $AvailableLeaveDays['hl'] > 0 && $allowed_hl_days < $AvailableLeaveDays['hl'])	{
		 		$workingDayHours += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
		 		$payable_days++;
		 		$allowed_hl_days++;
		 	
		 	}elseif($details_single_empl[$kv] == 'ML' && $AvailableLeaveDays['ml'] >0 && $allowed_ml_days < $AvailableLeaveDays['ml'])	{
		 		$workingDayHours += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
		 		$payable_days++;
		 		$allowed_ml_days++;
		 	}
		}
		
		if($hrmsetup['monthly_choice'] != 1){ 		
			$details_single_empl2 = GetRow('kv_empl_attendancee', array('month' => $secondMonth, 'year' => $year, 'empl_id' => $empl_id));
			if(is_array($details_single_empl2)) {
				$total_days = $hrmsetup['EndDay'];
				for($kv=1; $kv<=$total_days; $kv++){
					if(strtotime(date('Y-m-d')) < strtotime($ext_year."-".$secondMonth."-".$kv))
						continue;
					$day_letters = date("D", strtotime($ext_year."-".$secondMonth."-".$kv)); 			
					$attendance_date = date("Y-m-d", strtotime($ext_year."-".$secondMonth."-".$kv)); 			
					if(in_array($day_letters, $hrmsetup['weekly_off'] ) ){
						if($Empl_job_row['joining'] <= $attendance_date && ($Empl_job_row['status'] == 1 || ($Empl_job_row['status']>1 && $Empl_job_row['date_of_status_change'] >= $attendance_date))){
							$workingDayHours += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
							$payable_days++;
						}		 		
					} elseif($details_single_empl2[$kv] == 'P'){ 			
						$endTime =  ( strtotime($empl_shift_time['EndTime']) <= strtotime($details_single_empl2[$kv.'vj_out']) ? $empl_shift_time['EndTime'] : $details_single_empl2[$kv.'vj_out']);
						$workingDayHours += strtotime($endTime) - strtotime($empl_shift_time['BeginTime']);
						$OT_hours += (strtotime($empl_shift_time['EndTime']) <= strtotime($details_single_empl2[$kv.'vj_out']) ? strtotime($details_single_empl2[$kv.'vj_out'])-strtotime($empl_shift_time['EndTime']) : 0);
						$payable_days++;
					} elseif( $details_single_empl2[$kv] == 'HD'){
						$full_day_hours = strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
						$workingDayHours += $full_day_hours/2;
						$OT_hours += (strtotime($empl_shift_time['EndTime']) <= strtotime($details_single_empl2[$kv.'vj_out']) ? strtotime($details_single_empl2[$kv.'vj_out'])-strtotime($empl_shift_time['EndTime']) : 0);
						$payable_days += 0.5;
					} elseif($details_single_empl2[$kv] == 'OD' || ( !empty($gazetted_leaves) && in_array(date("Y-m-d", strtotime($ext_year."-".$secondMonth."-".$kv)), $gazetted_leaves)) ) {
						$workingDayHours += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
						$payable_days++;
					}elseif($details_single_empl2[$kv] == 'AL' && $AvailableLeaveDays['al'] >0 && $allowed_gl_days < $AvailableLeaveDays['al'])	{
						$workingDayHours += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
						$payable_days++;
						$allowed_gl_days++;
					}elseif($details_single_empl2[$kv] == 'SL' && $AvailableLeaveDays['sl'] > 0 && $allowed_sl_days < $AvailableLeaveDays['sl'])	{
						$workingDayHours += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
						$payable_days++;
						$allowed_sl_days++;
					}elseif($details_single_empl2[$kv] == 'HL' && $AvailableLeaveDays['hl'] > 0 && $allowed_hl_days < $AvailableLeaveDays['hl'])	{
						$workingDayHours += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
						$payable_days++;
						$allowed_hl_days++;
					}
					elseif($details_single_empl2[$kv] == 'ML' && $AvailableLeaveDays['ml'] >0 && $allowed_ml_days < $AvailableLeaveDays['ml'])	{
						$workingDayHours += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
						$payable_days++;
						$allowed_ml_days++;
					}
				}		
			}		
		}
	}		
 	
 	if($sec){
 		return array($workingDayHours, $OT_hours, $payable_days, $allowed_ml_days,$allowed_cl_days,$allowed_gl_days);
 	}else{
 		$whours = floor($workingDayHours / 3600);
		$wmins = floor($workingDayHours / 60 % 60);
		//$wsecs = floor($workingDayHours % 60);
		$Working_hours_final = $whours.':'.$wmins; //.':'.$wsecs;
		$ot_hours = floor($OT_hours / 3600);
		$ot_mins = floor($OT_hours / 60 % 60);
		$OT_hours_min = $ot_hours.':'.$ot_mins;
	 	return array( $Working_hours_final, $OT_hours_min, $payable_days, $allowed_ml_days,$allowed_cl_days,$allowed_gl_days);
 	}
}

function DaysDropDown($name, $day, $submit_on_change=false, $disabled =false){
	$Days = array('1' => 1,'2' =>2,'3'=>3,'4'=>4,'5'=>5,'6'=>6,'7'=>7,'8'=>8,'9'=>9,'10'=>10,'11'=>11,'12'=>12,'13'=>13,'14'=>14,'15'=>15,'16'=>16,'17'=>17,'18'=>18,'19'=>19,'20'=>20,'21'=>21,'22'=>22,'23'=>23,'24'=>24,'25'=>25,'26'=>26,'27'=>27,'28'=>28,'29'=>29,'30'=>30,'31'=>31);
	return array_selector($name, $day, $Days, 
			array( 
				'select_submit'=> $submit_on_change,
				'async' => false,				
				'spec_id' => 3, 
				'default' => 'None',
				'disabled' => $disabled ) );
}

function DaysDropDown_row($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false){
	echo "<tr><td class='label'>$label</td><td>".DaysDropDown($name, $selected_id, $submit_on_change, $disabled)."</td></tr>\n";
}

?>
