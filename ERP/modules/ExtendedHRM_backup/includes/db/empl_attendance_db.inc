<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

function db_has_day_attendancee($empl_id, $month, $year){
	$sql = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_attendancee WHERE month=".db_escape($month)." AND empl_id=".db_escape($empl_id)." AND year=".db_escape($year);
	return check_empty_result($sql);
}


function add_employee_attendance($empl_attend, $empl_id, $month, $year, $day, $in, $out,$dept_id){
	 $dayy = (int)$day;
	$sql = "INSERT INTO ".TB_PREF."kv_empl_attendancee (empl_id, month, year, `{$dayy}`,dept_id, `{$dayy}vj_in`, `{$dayy}vj_out`) VALUES (".db_escape($empl_id).", ".db_escape($month).", ".db_escape($year).", ".db_escape($empl_attend).",".db_escape($dept_id).", ".db_escape($in).", ".db_escape($out).")";

	db_query($sql, "The employee could not be added");
}

//--------------------------------------------------------------------------------------------
function update_employee_attendance($empl_attend, $empl_id, $month, $year, $day, $in, $out){
	$dayy = (int)$day;
	$sql = "UPDATE ".TB_PREF."kv_empl_attendancee SET `{$dayy}` =".db_escape($empl_attend).", `{$dayy}vj_in` =".db_escape($in).", `{$dayy}vj_out` =".db_escape($out)." WHERE month=".db_escape($month)." AND empl_id=".db_escape($empl_id)." AND year=".db_escape($year);

	db_query($sql,"The employee could not be updated");
}

//-----------------------------------------------------------------------------------------
function GetEmployeeAvailableLeave($empl_id, $month, $year, $leave_code){
	$avl_leave = GetRow('kv_empl_leave_days', array('empl_id' => $empl_id, 'leave_char' => $leave_code), array('date' => 'DESC'));
	$employee_joining = GetSingleValue('kv_empl_job', 'joining', array('empl_id' => $empl_id));
	$current_fiscal =  get_current_fiscalyear();
	$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
 	$ext_year = date("Y", strtotime($months_with_years_list[$month]));

	if(($leave_code == 'AL' || $leave_code == 'SL' ) && (is_date_in_fiscalyear(sql2date($employee_joining)) || is_date_in_fiscalyear(sql2date($avl_leave['date'])))){
		$per_month = number_format(($avl_leave['days']/12),1);
		if(is_date_in_fiscalyear(sql2date($employee_joining)))
			$joining = new DateTime($employee_joining);
		else
			$joining = new DateTime($avl_leave['date']);
		$end_of_fiscal = new DateTime(date2sql(end_fiscalyear()));
		$diff = $end_of_fiscal->diff($joining);		
		$total_this_year_avail_annual_leave = number_format(($per_month*$diff->m));				 	
		//$total_month_avail_annual_leave = number_format(($per_month*$diff->m));				 	
	}else{
		$total_this_year_avail_annual_leave = $avl_leave['days'];
		/*if($leave_code == 'ML')
			$total_this_year_avail_annual_leave = $avl_leave['days'];
		else{
			$per_month = number_format(($avl_leave['days']/12),1);
			$d1 = new DateTime($current_fiscal['begin']);
			$d2 = new DateTime(date('Y-m-d', strtotime($ext_year.'-'.$month.'-01')));
			
			$left_months = (12-($d1->diff($d2)->m)); 
			$total_this_year_avail_annual_leave = $left_months*$per_month;
		}*/
	}

	return $total_this_year_avail_annual_leave;
}

//------------------------------------------------------------------------------------------
function UsedPaidLeaves($empl_id, $year){
	$usedLeaves =  array();
	$sql = "SELECT paid_lop_days FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".$empl_id." AND year =".$year;
	$result = db_query($sql, "Can't get results for usedpaidleaves");
	if(db_num_rows($result) > 0 ){
		while($row = db_fetch($result)){
		$data = @unserialize($row['paid_lop_days']);
		if ($data !== false)
	   		$usedLeaves[] = unserialize($row['paid_lop_days']);
		}
	}
	$finalReturn =  array();
	foreach($usedLeaves as $leave){
		foreach($leave as $key => $value){
			$finalReturn[$key] += $value;
		}
	}
	//display_error(json_encode($finalReturn));
	return $finalReturn;
}

//--------------------------------------------------------------------------------------------
function get_employees_attendances($attendance_date, $dept_id){
	//echo date2sql($attendance_date);
	$month = (int)date("m", strtotime(date2sql($attendance_date)));
	$day = date("d", strtotime(date2sql($attendance_date)));
	$year = get_fiscal_year_id_from_date($attendance_date);

	$degrees = array();
	$dayy = (int)$day;
	$sql = "SELECT empl_id, `{$dayy}` , `{$dayy}vj_in`, `{$dayy}vj_out` FROM ".TB_PREF."kv_empl_attendancee WHERE year=".(db_escape($year))." AND month=".(db_escape($month))." AND dept_id=".(db_escape($dept_id));
	$ret = db_query($sql, "could not retrieve security roles");
	while($get_des=db_fetch_assoc($ret)){
		$degrees[$get_des['empl_id']] = $get_des[$dayy];	
		$degrees[$get_des['empl_id'].'vj_in'] = $get_des[$dayy.'vj_in'];	
		$degrees[$get_des['empl_id'].'vj_out'] = $get_des[$dayy.'vj_out'];			
	}
	//display_error(json_encode($degrees).$sql);
	return $degrees;
}

//--------------------------------------------------------------------------------------------
function GetEmplAttendanceDuration($empl_id, $from = null, $to = null, $month = null, $year = null, $dimension = -1, $dimension2= -1, $terminate_date =null, $weekly_off =false ){
	
	$sql = "SELECT at.empl_id, at.duration AS Duration, at.ot AS OT, at.sot AS SOT, at.dimension, at.dimension2, info.al/12 AS al , at.code, info.sl/12 AS sl, info.weekly_off AS weekly_off FROM ".TB_PREF."kv_empl_attendance AS at, ".TB_PREF."kv_empl_job AS info WHERE at.empl_id = info.empl_id AND at.empl_id=".db_escape($empl_id);
	$sql0 = " SELECT COUNT(a_date) FROM ".TB_PREF."kv_empl_attendance WHERE empl_id = ".db_escape($empl_id)." AND code = 'P' ";
	if($dimension != -1){
		$sql .=" AND dimension = ".db_escape($dimension);
		$sql0 .=" AND dimension =". db_escape($dimension);
	}	
	if($dimension != -1)
		$sql .=" AND dimension2 = ".db_escape($dimension2);	
	
	if($month > 0 && $year > 0 ){
		$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
		$to = end_month(sql2date($months_with_years_list[(int)get_post('month')])); 
		$from = begin_month(sql2date($months_with_years_list[(int)get_post('month')])); 
	}
	
	if(is_date($from)){
		$sql .=" AND a_date >= ".db_escape(date2sql($from));
		$sql0 .=" AND a_date >= ".db_escape(date2sql($from));
	}
	if(is_date($to)){
		$sql .=" AND a_date <= ".db_escape(date2sql($to));
		$sql0 .=" AND a_date <= ".db_escape(date2sql($to));
	}
	if($terminate_date){
		$sql .=" AND a_date <= ".db_escape(date2sql($terminate_date));
		$sql0 .=" AND a_date <= ".db_escape(date2sql($terminate_date));
	}
	
	//$sql .= " GROUP BY dimension, dimension2 " ;
	$sql0 .= " GROUP BY a_date " ;
	//display_error($sql);
	$ret = db_query($sql, "could not retrieve attendance");	
	$final = [];
	if(db_num_rows($ret)> 0){
		$Duration = $OT = $SOT = $at_code = 0;
		while($cont = db_fetch_assoc($ret)){
			$Duration  += $cont['Duration'];
			$OT  += $cont['OT'];
			$SOT  += $cont['SOT'];
			$dimension = $cont['dimension'];
			$dimension2 = $cont['dimension2'];
			$al = $cont['al'];
			$weekly_off = $cont['weekly_off'];
			if($cont['code'] == 'AL')
				$at_code++;
		}
		
		$weekly_off = unserialize(base64_decode($weekly_off));
		$final[] = array('Duration' => $Duration, 'OT' => $OT, 'SOT' => $SOT, 'dimension' => $dimension, 'dimension2' => $dimension2, 'al' => $at_code,'weekly_off'=>$weekly_off);
		
	}
	
	if(!empty($final)){
		$days = date('d', strtotime(date2sql($to)));
		$beginDay = date('d', strtotime(date2sql($from)));
		$month = date('m', strtotime(date2sql($to)));
		$ext_year = date('Y', strtotime(date2sql($to)));
		//if(!$weekly_off)
		//	$weekly_off = unserialize(base64_decode($weekly_off));
		$days_count = $dayOff = 0 ;
		for($vj = $beginDay; $vj <= $days; $vj++){
			$day_letters =date("D", strtotime($ext_year."-".$month."-".$vj)) ;		
			if(!in_array($day_letters, $weekly_off ) )
				$days_count++;
			else
				$dayOff++;
		}
		//display_error($dayOff.'-'.$days_count);
		$totalDays = date('t', strtotime(date2sql($to)));
		$workable_days= $workable_hrs = 0 ;
		$shift = GetEmplShiftDetails($empl_id);
		$time = strtotime($shift['EndTime']) - strtotime($shift['BeginTime']);
		//display_error($time.'-'.$shift['EndTime'].'-'.$shift['BeginTime']);
		if($time < 0 ) {
			$attendance_date_plus_1 = date2sql(add_days(Today(), 1));
			$time  = strtotime($attendance_date_plus_1.' '.$shift['EndTime']) - strtotime($shift['BeginTime']); 
			//display_error($time.'-'.$attendance_date_plus_1.'--'.strtotime($attendance_date_plus_1.' '.$shift['EndTime']) .'- -'.strtotime($shift['BeginTime']));
		}
		$off_duration = 0;
		for($js = $beginDay; $js <= $days; $js++){
			$day_letters =date("D", strtotime($ext_year."-".$month."-".$js)) ;		
			if(!in_array($day_letters, $weekly_off ) ){
				$workable_days++;
				$workable_hrs += $time;
			} else {
				$off_duration += $time;
				//display_error($off_duration.'-'.json_encode($js));
			}
		} 
		
		$total_working_days = GetSingleValue('kv_empl_option', 'option_value' , array('option_name' => 'total_working_days'));
		if($total_working_days){
			$final[0]['total_hrs'] = 30*($time);
			$final[0]['Duration'] += $off_duration;
			$final[0]['total_days'] = $final[0]['days_count'] = 30;
		} else {
			$final[0]['total_hrs'] = $workable_hrs;
			$final[0]['total_days'] = $workable_days;
			$final[0]['days_count'] = $days_count;
		}
		/*	$final[0]['total_hrs'] = $workable_hrs;
			$final[0]['Duration'] += $off_duration;
			$final[0]['total_days'] =$dayOff + $days_count;
			$final[0]['days_count'] = $days_count;*/

		
		$res = db_query($sql0, "Can't get date Count ");
		if(db_num_rows($res)> 0 )
			$final[0]['days'] = db_num_rows($res);
		else 
			$final[0]['days'] = 0;		
		return $final;
	}else
		return false;
}
//--------------------------------------------------------------------------------------------
function GetEmployeesAttendanceDuration($empl_ids, $from = null, $to = null, $month = null, $year = null, $dimension = -1, $dimension2= -1 ){
	
	$sql = "SELECT empl_id, SUM(duration) AS Duration, SUM(ot) AS OT, dimension, dimension2 FROM ".TB_PREF."kv_empl_attendance WHERE empl_id IN (".$empls_id = implode(',', array_map('intval', $empl_ids)).")";
	
	if($dimension != -1)
		$sql .=" AND dimension = ".db_escape($dimension);
		
	if($dimension != -1)
		$sql .=" AND dimension2 = ".db_escape($dimension2);	
	
	if($month > 0 && $year > 0 ){
		$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
		$to = end_month(sql2date($months_with_years_list[get_post('month')])); 
		$from = begin_month(sql2date($months_with_years_list[get_post('month')])); 		
	}
	
	if(is_date($from)){
		$sql .=" AND a_date >= ".db_escape(date2sql($from));
	}
	if(is_date($to)){
		$sql .=" AND a_date <= ".db_escape(date2sql($to));
	}
	
	$sql .= " GROUP BY empl_id, dimension, dimension2 " ;
	$ret = db_query($sql, "could not retrieve security roles");
	$final = array();
	while($cont = db_fetch_assoc($ret))
		$final[] = $cont;
	
	if(!empty($final))
		return $final;
	else
		return false;
}
function GetEmplShiftDetails($empl_id){
	$sql ="SELECT * FROM ".TB_PREF."kv_empl_shifts AS shift INNER JOIN ".TB_PREF."kv_empl_job AS job ON job.shift=shift.id AND job.shift > 0 AND job.empl_id = ".db_escape($empl_id);
	$sql2 = "SELECT * FROM `".TB_PREF."kv_empl_option` WHERE `option_name` = 'BeginTime' OR option_name= 'EndTime'";
	$res = db_query($sql, "Cant get shift results");
	if(db_num_rows($res) > 0 ){
		if($row = db_fetch($res)){
			return $row;
		} else
			$return = false;
	} else
		$return = false;
		
	if($return == false){
		$res = db_query($sql2, "Cant get shift results");
		if(db_num_rows($res) > 0 ){
			$shift = [];
			while($row = db_fetch($res)){
				$shift[$row['option_name']] = $row['option_value'];
			}
			return $shift;
		}
	}
}

//--------------------------------------------------------------------------------------------
function get_employee_attendances($attendance_date, $empl_id){
	//echo date2sql($attendance_date);
	$month = (int)date("m", strtotime(date2sql($attendance_date)));
	$day = date("d", strtotime(date2sql($attendance_date)));
	$year = get_fiscal_year_id_from_date($attendance_date);

	$degrees = array();
	$dayy = (int)$day;
	$sql = "SELECT empl_id, `{$dayy}` , `{$dayy}vj_in`, `{$dayy}vj_out` FROM ".TB_PREF."kv_empl_attendancee WHERE year=".(db_escape($year))." AND month=".(db_escape($month))." AND empl_id=".(db_escape($empl_id));
	$ret = db_query($sql, "could not retrieve security roles");
	while($get_des=db_fetch_assoc($ret)){
		$degrees[$get_des['empl_id']] = $get_des[$dayy];	
		$degrees[$get_des['empl_id'].'vj_in'] = $get_des[$dayy.'vj_in'];	
		$degrees[$get_des['empl_id'].'vj_out'] = $get_des[$dayy.'vj_out'];			
	}
	//display_error(json_encode($degrees).$sql);
	return $degrees;
}

//--------------------------------------------------------------------------------------------
function get_fiscal_year_id_from_date($date){
	$attendance_date = strtotime(date2sql($date));
	$all_fiscal_years  = get_all_fiscalyears();
	$year = 0;	
	while($get_des=db_fetch($all_fiscal_years)){
		$begin = strtotime($get_des['begin']); 
		$end = strtotime($get_des['end']); 
		if( $begin <= $attendance_date && $end >= $attendance_date){
			$year = $get_des['id'];
			break;
		}
		//display_error(date2sql($date).'--'.$attendance_date.'==='.$begin.'--'.$end);
	}
	return $year; 
}

function get_dep_employees_count($dep_id, $attendance_date) {
	
	//$sql= "SELECT COUNT(empl_id) FROM ".TB_PREF."kv_empl_job  WHERE department=".db_escape($dep_id); 
	$sql= "SELECT DISTINCT info.empl_id, job.joining, info.status, info.date_of_status_change FROM ".TB_PREF."kv_empl_job job, ".TB_PREF."kv_empl_info info WHERE info.empl_id= job.empl_id AND job.department=".db_escape($dep_id); 	
	$result = db_query($sql, "could not get department employee count");
	$employees_count = 0;
	while($row=db_fetch($result)){
		if($row['joining'] <= date2sql($attendance_date) && ($row['status'] == 1 || ($row['status']>1 && $row['date_of_status_change'] >= date2sql($attendance_date)))){
			$employees_count++;
		}
	}
	return $employees_count; 
} 

//---------------------------------------------------------------------------------
function get_employee_whole_attendance($empl_id, $year=null){
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_attendance WHERE empl_id=".db_escape($empl_id);
	
	if($year){
		$f_year = GetRow('fiscal_year', array('id' => $year));		
		$sql .=" AND a_date >= ".db_escape($f_year['begin'])." AND a_date <= ".db_escape($f_year['end']);		
	}
	$sql .= ' ORDER BY a_date';
	$attendance = array();
	$ret = db_query($sql, "Can't get empl attendance");
	
	while($cont = db_fetch_assoc($ret)) {	
		$day = (int)date('d', strtotime($cont['a_date']));
		$attendance[(int)date('m', strtotime($cont['a_date']))][$day] = $cont['code'];
	}
	return $attendance;
}

/*old functions*/

function add_employees_attendance($a_date, $employees){
	 	//display_error(json_encode($employees));
		$a_date = date2sql($a_date); 
		$sql = "INSERT INTO ".TB_PREF."kv_empl_attendance (a_date, employees) VALUES (".db_escape($a_date).", ".db_escape(implode(';', $employees)).")";

	db_query($sql, "The employee could not be added");
}


//--------------------------------------------------------------------------------------------
function update_employees_attendance($a_date, $employees){
	$a_date = date2sql($a_date); 
	$sql = "UPDATE ".TB_PREF."kv_empl_attendance SET employees=".db_escape(implode(';',$employees))." WHERE a_date=".db_escape($a_date);

	db_query($sql,"The employee could not be updated");

}

//--------------------------------------------------------------------------------------------
function db_has_day_attendance($a_date){
	$a_date = date2sql($a_date);
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_attendance WHERE a_date=".db_escape($a_date));
}

function get_empl_attendance_for_month($empl_id,$month, $year) {

 	$leave_Day = 0 ;
	$get_des=GetRow('kv_empl_attendancee', array('empl_id' => $empl_id,'month' => $month, 'year' => $year));
	if($get_des != null || !empty($get_des)){										
		foreach($get_des as $single){
			if($single == 'A')
				$leave_Day += 1;
			if($single == 'HD')
				$leave_Day += 0.5;
		}
	}
	return $leave_Day;
}

function get_employee_attendance_details($empl_id, $start, $end) {

	$start_date = date2sql($start);
	$end_date = date2sql($end);
	$leave_dates=array();
	$sql= "SELECT a_date FROM ".TB_PREF."kv_empl_attendance  WHERE a_date >= ".db_escape($start_date)." AND a_date <= ".db_escape($end_date)." AND employees LIKE '%".$empl_id."%'"; 
	$result = db_query($sql, "could not get sales type");

	while($get_des=db_fetch($result))
		$leave_dates[]=$get_des['a_date'];
		
	return $leave_dates;

} 

function get_employees_absent_count($empl_ids_arr, $start, $end) {

	$start_date = date2sql($start);
	$end_date = date2sql($end);
	$leave_dates=array();
	foreach($empl_ids_arr as $empl_id) {
		$sql= "SELECT COUNT(a_date) FROM ".TB_PREF."kv_empl_attendance  WHERE a_date >= ".db_escape($start_date)." AND a_date <= ".db_escape($end_date)." AND employees LIKE '%".$empl_id."%'"; 
		$result = db_query($sql, "could not get sales type");

		$get_des=db_fetch($result);
		$leave_dates[] = array( 'empl_id' =>$empl_id, 'days' => $get_des[0]); 
	}
	return $leave_dates;

} 
?>
