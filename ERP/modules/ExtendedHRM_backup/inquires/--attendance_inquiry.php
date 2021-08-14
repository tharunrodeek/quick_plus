<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_ATTENDANCE';
$path_to_root="../../..";
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();
include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
 
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/db_pager.inc");
page(trans("Attendance Inquiry"));
 $dim = get_company_pref('use_dimension');
 check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));
 
 simple_page_mode(true);
//----------------------------------------------------------------------------------------
	$new_item = get_post('dept_id')=='' || get_post('cancel') ;
	$month = get_post('month','');
	$year = get_post('year','');
	if (isset($_GET['dept_id']) ){
		$_POST['dept_id'] = $_GET['dept_id'];
	}
	$dept_id = get_post('dept_id');
	if (list_updated('empl_id')) {
		$_POST['empl_id'] = $empl_id = get_post('empl_id');
	    $Ajax->activate('details');
	}
	if (isset($_GET['month'])){
		$_POST['month'] = $_GET['month'];
	}
	if (isset($_GET['year'])){
		$_POST['year'] = $_GET['year'];
	}
	
	if (list_updated('month')) {
		$month = get_post('month');   
		$Ajax->activate('details');
	}

//$month = date("m");
//----------------------------------------------------------------------------------------
	start_form(true);
		start_table(TABLESTYLE_NOBORDER);
			echo '<tr>';
				kv_fiscalyears_list_cells(trans("Fiscal Year:"), 'year', null, true);
			 	kv_current_fiscal_months_list_cell(trans("Months"), "month", null, true);
			 	department_list_cells(trans("Select a Department")." :", 'selected_id', null,	trans("All Departments"), true, check_value('show_inactive'));
				
				if ($dim >= 1){
					dimensions_list_cells(trans("Dimension")." 1", 'dimension_id', null, true, " ", false, 1, true);
					//if ($dim > 1)
						//dimensions_list_cells(trans("Dimension")." 2", 'dimension2_id', null, true, " ", false, 2, true);
					//else
						//hidden('dimension2_id', 0);
				}
				if ($dim < 1)
					hidden('dimension_id', 0);
				//if ($dim < 2)
					//hidden('dimension2_id', 0);
			
				$new_item = get_post('selected_id')=='';
		 	echo '</tr>';
	 	end_table(1);

	 	if (get_post('_show_inactive_update') || get_post('empl_id')) {
			$Ajax->activate('month');
			$Ajax->activate('details');
			$Ajax->activate('dept_id');		
			set_focus('month');
		}
		if($month==null){			 
			$month = $_POST['month'];
		}
		if($year==null){			 
			$year = $_POST['year'];
		}
		//echo $month;

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
		
		if($hrmsetup['monthly_choice'] == 2){
			$firstMonth = $month;		
			$secondMonth = $month+1;
		} elseif($hrmsetup['monthly_choice'] == 3) {
			$firstMonth = $month-1;
			$secondMonth = $month;
		} else {
			$firstMonth = $secondMonth = $month;
		}	
		$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
 		$ext_year = date("Y", strtotime($months_with_years_list[(int)get_post('month')]));
		if($firstMonth != $secondMonth) {	
			$firstDay = new DateTime($ext_year."-".$firstMonth."-".$hrmsetup['BeginDay']);
			$LastDay = new DateTime($ext_year."-".$secondMonth."-".$hrmsetup['EndDay']);
			$total_days_count = $LastDay->diff($firstDay)->format("%a")+1;
		}	else 
			$total_days_count = date("t", strtotime($year."-".$firstMonth."-01"));
		div_start('details');
			if(get_post('empl_id') > 0 ){
				$sql = "SELECT empl_id, empl_firstname FROM ".TB_PREF."kv_empl_info WHERE empl_id=".get_post('empl_id');  
				$selected_empl =  db_query($sql, "The employee table is inaccessible");
			} else 
				$selected_empl = kv_get_employees_list_based_on_dept($dept_id);
			
			start_table(TABLESTYLE);
			
 			$Working_hours_final = Get_Monthly_WorkingHours($year, get_post('month'));
				echo  "<tr>	<td rowspan=2 class='tableheader'>" . trans("Empl ID") . "</td>
					<td rowspan=2 class='tableheader'>" . trans("Empl Name") . "</td>					
					<td colspan=".$total_days_count." class='tableheader'>" . trans(date("Y - F", strtotime($ext_year."-".$month."-01"))) . "</td>
					<td rowspan=2 class='tableheader'>" . trans("Working Days") . "</td>
					<td rowspan=2 class='tableheader'>" . trans("Holidays") . "</td>
					<td rowspan=2 class='tableheader'>" . trans("Leave Days") . "</td>
					<td rowspan=2 class='tableheader'>" . trans("Absent Days") . "</td>
					<!-- <td rowspan=2 class='tableheader'>" . trans("LOP Days") . "</td>-->
					<td rowspan=2 class='tableheader'>" . trans("Worked Days(Hours)(OT)") . "</td>	</tr><tr>";
					$weekly_off = $hrmsetup['weekly_off'];
					$off_count = 0;
					$weekly_offdate = $gazatted_holiday_count = 0; 
					$sql_query = "SELECT date FROM ".TB_PREF."kv_empl_gazetted_holidays WHERE (date BETWEEN '".date("Y-m-d", strtotime($ext_year."-".$firstMonth."-".$hrmsetup['BeginDay']))."' AND '".date("Y-m-d", strtotime($ext_year."-".$secondMonth."-".$hrmsetup['EndDay']))."' ) ";

						$first_gazetted_leaves = array();
						$result = db_query($sql_query, "Can't get results");
						if(db_num_rows($result)){
							while($cont = db_fetch($result))
								$first_gazetted_leaves[] = $cont[0];
						}
						$firstMthDays = date("t", strtotime($year."-".$firstMonth."-01"));		
						$kv = $hrmsetup['BeginDay'];
						for($kv; $kv<=$total_days_count; $kv++){
							$day_letters = date("D", strtotime($ext_year."-".$firstMonth."-".$kv)) ; 
							if( in_array($day_letters, $weekly_off) ){							
								$style_head = "style='background-color:#FF9800'";
								if($weekly_offdate==0)
									$weekly_offdate=$kv;
								$off_count++;
							}elseif( !empty($first_gazetted_leaves) && in_array(date("Y-m-d", strtotime($ext_year."-".$firstMonth."-".$kv)), $first_gazetted_leaves)){
								$style_head = "style='background-color:#FF9800'";
								$gazatted_holiday_count++;
							}else{
								$style_head = '';
							}						
							echo "<td class='tableheader' ".$style_head." >". trans(date("D d", strtotime($ext_year."-".$firstMonth."-".$kv))) . "</td>";
						}
						
						if($hrmsetup['monthly_choice'] != 1){ 		
							$total_days = $hrmsetup['EndDay'];
							for($kv=1; $kv<=$total_days; $kv++){
								$day_letters = date("D", strtotime($ext_year."-".$secondMonth."-".$kv)) ; 
								if( in_array($day_letters, $weekly_off) ){							
									$style_head = "style='background-color:#FF9800'";
									if($weekly_offdate==0)
										$weekly_offdate=$kv;
									$off_count++;
								}elseif( !empty($first_gazetted_leaves) && in_array(date("Y-m-d", strtotime($ext_year."-".$secondMonth."-".$kv)), $first_gazetted_leaves)){
									$style_head = "style='background-color:#FF9800'";
									$gazatted_holiday_count++;
								}else{
									$style_head = '';
								}						
								echo "<td class='tableheader' ".$style_head." >". trans(date("D d", strtotime($ext_year."-".$secondMonth."-".$kv))) . "</td>";
							}
						}			
									
					echo "</tr>";
					while ($row = db_fetch_assoc($selected_empl)) {
						
						$empl_shift_time = GetDataJoinRow('kv_empl_shifts AS shift', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_job AS job','conditions'=>'`job`.`shift` = `shift`.`id`')), 
						array('`shift`.`BeginTime`, `shift`.`EndTime`, `job`.`empl_id`'), array('`job`.`empl_id`' => $row['empl_id']));
						if(empty($empl_shift_time)){
							$empl_shift_time['BeginTime'] = $hrmsetup['BeginTime'] ;
							$empl_shift_time['EndTime'] = $hrmsetup['EndTime'] ;
						}

					//display_error(json_encode($empl_shift_time));
						$details_single_empl = GetRow('kv_empl_attendancee', array('month' => $firstMonth, 'year' => $year, 'empl_id' => $row['empl_id'])); 		
						if($secondMonth != $firstMonth)
							$details_single_empl_pre_mnth = GetRow('kv_empl_attendancee', array('month' => (int)$secondMonth, 'year' => $year, 'empl_id' => $row['empl_id']));
						else
							$details_single_empl_pre_mnth = 0;

						//if($row['empl_id'] == 123)
							//display_error(json_encode($details_single_empl_pre_mnth));

						if($details_single_empl || $details_single_empl_pre_mnth){
							echo '<tr style="text-align:center"><td>'.$row['empl_id'].'</td><td>'.$row['empl_firstname'].'</td>';
							$AvailableLeaveDays = AvailableLeaveDays($row['empl_id'], $year, $month, false); 
							//display_error(json_encode(($AvailableLeaveDays)));
							$leave_Day = $absDays = 0 ;
							$week_end=1;
							$weekly_offdat = $weekly_offdate;
							$WorkingSeconds = $OT_hours = $holidays = 0;
							$kvj = 1;
							$workingDayToday = $Payable_days= $allowed_gl_days = $allowed_cl_days =  $allowed_ml_days = $total_working_days = 0;
							$off_day_attendance = 0;
							$vj = $hrmsetup['BeginDay'];
							for($vj; $vj<=$firstMthDays; $vj++){													
								$day_letters = date("D", strtotime($ext_year."-".$firstMonth."-".$vj)) ; 
								if( in_array($day_letters, $weekly_off) ){								
									$style="style='background-color: #FF9800;'"; 
									$week_end = $workingDayToday =1;
									$weekly_offdat = 7;
									$off_day_attendance = 1;
								} else{
									$off_day_attendance = 0;
									if(!empty($first_gazetted_leaves) && in_array(date("Y-m-d", strtotime($ext_year."-".$firstMonth."-".$vj)), $first_gazetted_leaves)){
										$style="style='background-color: #FF9800;'"; 
										$workingDayToday =1;
										$holidays++;
										//$total_working_days++;
									}elseif($details_single_empl[$vj] == 'A' || ($details_single_empl[$vj] == '' && (strtotime($ext_year."-".$firstMonth."-".$vj)<= strtotime(date('Y-m-d'))) ) ){
										$style="style='background-color:#f32c1d;color:#fff'";
										$workingDayToday =0;
										$absDays += 1;
									}
									elseif($details_single_empl[$vj] == 'L' || $details_single_empl[$vj] == 'ML' || $details_single_empl[$vj] == 'GL' || $details_single_empl[$vj] == 'CL'){
										$style="style='background-color:#FF9800;color:#fff'";
										$workingDayToday =0;
										$leave_Day += 1;
									} elseif($details_single_empl[$vj] == 'HD'){
										$leave_Day += 0.5;
										$workingDayToday =0;
										$style=""; 
									} else {
										$style=""; 
										$workingDayToday =0;
									}
									$week_end++;									
								}
								if($workingDayToday == 0)
									$total_working_days++;
								echo '<td '.$style.' >'. ($workingDayToday == 1 ? ($off_day_attendance == 0 ? '-': ($details_single_empl[$vj.'vj_in'] != '00:00:00' ? date('h:iA', strtotime($details_single_empl[$vj.'vj_in'])) : '').' '.($details_single_empl[$vj.'vj_out'] != '00:00:00' ? date('h:iA', strtotime($details_single_empl[$vj.'vj_out'])): '' ) ) : ($details_single_empl[$vj]? $details_single_empl[$vj]: ( (strtotime($ext_year."-".$firstMonth."-".$vj)<= strtotime(date('Y-m-d'))) ?  'A' : '-' )).(($details_single_empl[$vj] == 'P' || $details_single_empl[$vj] == 'HD'  )? '<br> <span style="font-size:8px;" >'.date('h:iA', strtotime($details_single_empl[$vj.'vj_in'])).' '.date('h:iA', strtotime($details_single_empl[$vj.'vj_out'])): '')).'</span></td>';
								if($details_single_empl[$vj] == 'P' ){									
									$endTime = (strtotime($empl_shift_time['EndTime']) <= strtotime($details_single_empl[$vj.'vj_out']) ? $empl_shift_time['EndTime'] : $details_single_empl[$vj.'vj_out']);
									$WorkingSeconds += strtotime($endTime) - strtotime($details_single_empl[$vj.'vj_in']);
									$Payable_days++;
									$OT_hours += ( strtotime($empl_shift_time['EndTime']) <= strtotime($details_single_empl[$vj.'vj_out']) ?  strtotime($details_single_empl[$vj.'vj_out'])- strtotime($empl_shift_time['EndTime']) : 0 );
								}elseif( $details_single_empl[$vj] == 'HD'){
									$full_day_hours = strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
									$WorkingSeconds += $full_day_hours/2;
									$OT_hours += (strtotime($empl_shift_time['EndTime']) <= strtotime($details_single_empl[$vj.'vj_out']) ? strtotime($details_single_empl[$vj.'vj_out'])-strtotime($empl_shift_time['EndTime']) : 0);
									$Payable_days += 0.5;
								}elseif($details_single_empl[$vj] == 'OD' ){
									$WorkingSeconds +=strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
									$Payable_days++;
								}elseif($details_single_empl[$vj] == 'GL' && $AvailableLeaveDays['gl'] >0 && $allowed_gl_days < $AvailableLeaveDays['gl'])	{
									$WorkingSeconds += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
									//$Payable_days++;
									$allowed_gl_days++;
								}elseif($details_single_empl[$vj] == 'CL' && $AvailableLeaveDays['cl'] > 0 && $allowed_cl_days < $AvailableLeaveDays['cl'])	{
									$WorkingSeconds += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
									//$Payable_days++;
									$allowed_cl_days++;
								}elseif($details_single_empl[$vj] == 'ML' && $AvailableLeaveDays['ml'] >0 && $allowed_ml_days < $AvailableLeaveDays['ml'])	{
									$WorkingSeconds += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
									//$Payable_days++;
									$allowed_ml_days++;
								}elseif(!empty($first_gazetted_leaves) && in_array(date("Y-m-d", strtotime($ext_year."-".$firstMonth."-".$vj)), $first_gazetted_leaves)){
									//$Payable_days++;	
									$WorkingSeconds += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);									
								}
								//$kvj++;
							}	
							//display_error(json_encode($details_single_empl_pre_mnth));
							if($hrmsetup['monthly_choice'] != 1 && is_array($details_single_empl_pre_mnth)) {
								for($kvc=1; $kvc<=$hrmsetup['EndDay']; $kvc++){													
								$day_letters = date("D", strtotime($ext_year."-".$secondMonth."-".$kvc)) ; 
								if( in_array($day_letters, $weekly_off) ){								
									$style="style='background-color: #FF9800;'"; 
									$week_end = $workingDayToday =1;
									$weekly_offdat = 7;
									$off_day_attendance = 1;
								} else{
									$off_day_attendance = 0;
									if(!empty($first_gazetted_leaves) && in_array(date("Y-m-d", strtotime($ext_year."-".$secondMonth."-".$kvc)), $first_gazetted_leaves)){
										$style="style='background-color: #FF9800;'"; 
										$workingDayToday =1;
										$holidays++;
										//$total_working_days++;
									}elseif($details_single_empl_pre_mnth[$kvc] == 'A' || ($details_single_empl_pre_mnth[$kvc] == '' && (strtotime($ext_year."-".$secondMonth."-".$kvc)<= strtotime(date('Y-m-d'))) ) ){
										$style="style='background-color:#f32c1d;color:#fff'";
										$workingDayToday =0;
										$absDays += 1;
									}
									elseif($details_single_empl_pre_mnth[$kvc] == 'L' || $details_single_empl_pre_mnth[$kvc] == 'ML' || $details_single_empl_pre_mnth[$kvc] == 'GL' || $details_single_empl_pre_mnth[$kvc] == 'CL'){
										$style="style='background-color:#FF9800;color:#fff'";
										$workingDayToday =0;
										$leave_Day += 1;
									} elseif($details_single_empl_pre_mnth[$kvc] == 'HD'){
										$leave_Day += 0.5;
										$workingDayToday =0;
										$style=""; 
									} else {
										$style=""; 
										$workingDayToday =0;
									}
									$week_end++;									
								}
								if($workingDayToday == 0)
									$total_working_days++;
								echo '<td '.$style.' >'. ($workingDayToday == 1 ? ($off_day_attendance == 0 ? '-': ($details_single_empl_pre_mnth[$kvc.'vj_in'] != '00:00:00' ? date('h:iA', strtotime($details_single_empl_pre_mnth[$kvc.'vj_in'])) : '').' '.($details_single_empl_pre_mnth[$kvc.'vj_out'] != '00:00:00' ? date('h:iA', strtotime($details_single_empl_pre_mnth[$kvc.'vj_out'])): '' ) ) : ($details_single_empl_pre_mnth[$kvc]? $details_single_empl_pre_mnth[$kvc]: ( (strtotime($ext_year."-".$secondMonth."-".$kvc)<= strtotime(date('Y-m-d'))) ?  'A' : '-' )).(($details_single_empl_pre_mnth[$kvc] == 'P' || $details_single_empl_pre_mnth[$kvc] == 'HD'  )? '<br> <span style="font-size:8px;" >'.date('h:iA', strtotime($details_single_empl_pre_mnth[$kvc.'vj_in'])).' '.date('h:iA', strtotime($details_single_empl_pre_mnth[$kvc.'vj_out'])): '')).'</span></td>';
								if($details_single_empl_pre_mnth[$kvc] == 'P' ){									
									$endTime = (strtotime($empl_shift_time['EndTime']) <= strtotime($details_single_empl_pre_mnth[$kvc.'vj_out']) ? $empl_shift_time['EndTime'] : $details_single_empl_pre_mnth[$kvc.'vj_out']);
									$WorkingSeconds += strtotime($endTime) - strtotime($details_single_empl_pre_mnth[$kvc.'vj_in']);
									$Payable_days++;
									$OT_hours += ( strtotime($empl_shift_time['EndTime']) <= strtotime($details_single_empl_pre_mnth[$kvc.'vj_out']) ?  strtotime($details_single_empl_pre_mnth[$kvc.'vj_out'])- strtotime($empl_shift_time['EndTime']) : 0 );
								}elseif( $details_single_empl_pre_mnth[$kvc] == 'HD'){
									$full_day_hours = strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
									$WorkingSeconds += $full_day_hours/2;
									$OT_hours += (strtotime($empl_shift_time['EndTime']) <= strtotime($details_single_empl_pre_mnth[$kvc.'vj_out']) ? strtotime($details_single_empl_pre_mnth[$kvc.'vj_out'])-strtotime($empl_shift_time['EndTime']) : 0);
									$Payable_days += 0.5;
								}elseif($details_single_empl_pre_mnth[$kvc] == 'OD' ){
									$WorkingSeconds +=strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
									$Payable_days++;
								}elseif($details_single_empl_pre_mnth[$kvc] == 'GL' && $AvailableLeaveDays['gl'] >0 && $allowed_gl_days < $AvailableLeaveDays['gl'])	{
									$WorkingSeconds += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
									//$Payable_days++;
									$allowed_gl_days++;
								}elseif($details_single_empl_pre_mnth[$kvc] == 'CL' && $AvailableLeaveDays['cl'] > 0 && $allowed_cl_days < $AvailableLeaveDays['cl'])	{
									$WorkingSeconds += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
									//$Payable_days++;
									$allowed_cl_days++;
								}elseif($details_single_empl_pre_mnth[$kvc] == 'ML' && $AvailableLeaveDays['ml'] >0 && $allowed_ml_days < $AvailableLeaveDays['ml'])	{
									$WorkingSeconds += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);
									//$Payable_days++;
									$allowed_ml_days++;
								}elseif(!empty($first_gazetted_leaves) && in_array(date("Y-m-d", strtotime($ext_year."-".$secondMonth."-".$kvc)), $first_gazetted_leaves)){
									//$Payable_days++;	
									$WorkingSeconds += strtotime($empl_shift_time['EndTime'])-strtotime($empl_shift_time['BeginTime']);									
								}
								//$kkvc++;
							}							
								
							} elseif($hrmsetup['monthly_choice'] != 1 ) {
								for($kvc=1; $kvc<=$hrmsetup['EndDay']; $kvc++){	
									echo '<td> - </td>';
								}
							}
							$hours = floor($WorkingSeconds / 3600);
							$mins = floor($WorkingSeconds / 60 % 60);
							$Payable_hours = $hours.':'.$mins;
							$ot_h = floor($OT_hours / 3600);
							$ot_min = floor($OT_hours / 60 % 60);
							$OT_hours_mins = $ot_h.':'.$ot_min; 
							$lopDays = $leave_Day+$absDays;
							echo '<td >'.$total_working_days.'('.$Working_hours_final[0].') </td> <td> '.$holidays.'</td> <td>'. $leave_Day.'</td> <td>'. $absDays.'</td> <!--<td>'. $lopDays.' </td> --> <td>'.$Payable_days.' - ('.$Payable_hours.') ('.$OT_hours_mins.') </td>';
							echo '<tr>';
						}
					}
			end_table(1);
		
		div_end();
	end_form();?>
	<style>
	#details table.tablestyle { min-width:1330px; } 
	#details table.tablestyle td { text-align:center; border: 1px solid #cccccc; padding: 1px;  } 
	#details table.tablestyle td span {font-size:10px;display:block;  } 
	table tr td {   line-height: 1.5em;}
	</style><?php 
end_page(); ?>
