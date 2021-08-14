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
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();
include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/ui.inc");


include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/db_pager.inc");
page(trans("Month Attendance"));
 
 check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));
 
 simple_page_mode(true);
//----------------------------------------------------------------------------------------
	$new_item = get_post('selected_id')=='' || get_post('cancel') ;
	$month = get_post('month','');
	$year = get_post('year','');
	if (isset($_GET['selected_id'])){
		$_POST['selected_id'] = $_GET['selected_id'];
	}
	$selected_id = get_post('selected_id');
	 if (list_updated('selected_id')) {
	 	
		$_POST['empl_id'] = $selected_id = get_post('selected_id');
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

if (isset($_POST['UpdateAttendance'])  && can_process()){
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
		$total_days_count = $pre_mnth = 0;
		if($hrmsetup['BeginDay'] > 1 && $hrmsetup['BeginDay'] < 31){			
			$pre_mnth =  (($month > 1)? ($month-1) : '1');
			$total_days_count =  (date("t", strtotime($year."-".$pre_mnth."-01"))- $hrmsetup['BeginDay'])+1;
		}
		
		if($hrmsetup['EndDay'] <= 31){
			$total_days_count +=date("t", strtotime($year."-".$month."-01"));
		}			
		
		$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
		//display_error(json_encode(($months_with_years_list)).get_post('month'));
 		$ext_year = date("Y", strtotime($months_with_years_list[get_post('month')]));
		$weekly_off = $hrmsetup['weekly_off'];
		$selected_empl = kv_get_employees_list_based_on_dept($_POST['selected_id']);
		while ($row = db_fetch_assoc($selected_empl)) {
			$empl_shift_time = array();
			/*$empl_shift_time = GetDataJoinRow('kv_empl_shifts AS shift', array( 
			0 => array('join' => 'INNER', 'table_name' => 'kv_empl_job AS job','conditions'=>'`job`.`shift` = `shift`.`id`')), 
			array('`shift`.`BeginTime`, `shift`.`EndTime`'), array('`job`.`empl_id`' => $row['empl_id']));
			if(empty($empl_shift_time)){*/
				$empl_shift_time['BeginTime'] = $hrmsetup['BeginTime'];
				$empl_shift_time['EndTime'] = $hrmsetup['EndTime'];
			//}			
		
			$details_single_empl = GetRow('kv_empl_attendancee', array('month' => $month, 'year' => $year, 'empl_id' => $row['empl_id'])); 		
			if($pre_mnth)
				$details_single_empl_pre_mnth = GetRow('kv_empl_attendancee', array('month' => $pre_mnth, 'year' => $year, 'empl_id' => $row['empl_id']));
			else
				$details_single_empl_pre_mnth = 0;
			if(!$details_single_empl && !$details_single_empl_pre_mnth){
				
				
				$kv_empl_attendancee_ar = array('month' => $_POST['month'], 'year' => $_POST['year'], 'empl_id' => $row['empl_id'], 'dept_id' => $row['department']);
				$proceed = false;
				for($vj=1; $vj<=$total_days_count; $vj++){
					$attendance_date = date("Y-m-d", strtotime($ext_year."-".$month."-".$vj)) ; 
					if($row['joining'] <= $attendance_date && ($row['status'] == 1 || ($row['status']>1 && $row['date_of_status_change'] >= $attendance_date))){
						$day_letters = date("D", strtotime($ext_year."-".$month."-".$vj)) ; 
						if(!in_array($day_letters, $weekly_off) ){
							$kv_empl_attendancee_ar[$vj] = 'P';
							$kv_empl_attendancee_ar[$vj."vj_in"] = $empl_shift_time['BeginTime'];
							$kv_empl_attendancee_ar[$vj."vj_out"] = $empl_shift_time['EndTime'];
						}
						$proceed = true;
					}
				}
				if($proceed)
				  dd($kv_empl_attendancee_ar);
					Insert('kv_empl_attendancee', $kv_empl_attendancee_ar);				
			}
		}
	display_notification(trans("Selected Month Attendance Processed"));
}

//--------------------------------------------------------------------------------
function can_process() {

	if(!isset($_POST['selected_id'])){
		display_error(trans("Please select a department to record Attendance"));
		set_focus('selected_id');
		return false;
	}
	return true;
}
	//$month = date("m");
//----------------------------------------------------------------------------------------

	start_form(true);
		start_table(TABLESTYLE_NOBORDER);
			echo '<tr>';
				kv_fiscalyears_list_cells(trans("Fiscal Year:"), 'year', null, true);
			 	kv_current_fiscal_months_list_cell("Months", "month", null, true);
			 	department_list_cells(trans("Select a Department")." :", 'selected_id', null,	trans("No Department"), true, check_value('show_inactive'));
				//employee_list_cells(trans("Select an Employee"). " :", 'empl_id', null,	trans("Select an Employee"), true, check_value('show_inactive'), false, false,true);
				$new_item = get_post('selected_id')=='';
		 	echo '</tr>';
	 	end_table(1);

	 	if (get_post('_show_inactive_update') || get_post('empl_id')) {
			$Ajax->activate('month');
			$Ajax->activate('details');
			$Ajax->activate('selected_id');		
			set_focus('month');
		}
		if($month==null){			 
			$month = $_POST['month'];
		}
		if($year==null){			 
			$year = $_POST['year'];
		}		
	
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
		
		div_start('details');
		
			$selected_empl = kv_get_employees_list_based_on_dept($selected_id);
			//$selected_empl_attend_details=kv_get_attend_details($selected_id);
			start_table(TABLESTYLE);

			$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
 			$ext_year = date("Y", strtotime($months_with_years_list[get_post('month')]));
 			$total_days =  date("t", strtotime($ext_year."-".$month."-01"));
				echo  "<tr>
					<td rowspan=2 class='tableheader'>" . trans("Empl ID") . "</td>
					<td rowspan=2 class='tableheader'>" . trans("Empl Name") . "</td>					
					<td colspan=".$total_days." class='tableheader'>" . trans(date("Y - F", strtotime($ext_year."-".$month."-01"))) . "</td>					
					</tr><tr>";
					$weekly_off = $hrmsetup['weekly_off'];
					
					$off_count = 0;
					$weekly_offdate = $gazatted_holiday_count = 0 ; 
					if($hrmsetup['BeginDay'] > 1)
						$kv = $hrmsetup['BeginDay'];
					else
						$kv = 1;
					$sql_query = "SELECT date FROM ".TB_PREF."kv_empl_gazetted_holidays WHERE (date BETWEEN '".date("Y-m-d", strtotime($ext_year."-".$month."-".$kv))."' AND '".date("Y-m-d", strtotime($ext_year."-".$month."-".$total_days))."' ) ";
					
					for($kv; $kv<=$total_days; $kv++){
						$day_letters = date("D", strtotime($ext_year."-".$month."-".$kv)) ; 
						if( in_array($day_letters, $weekly_off) ){
							$style_head = "style='background-color:#e0db98'";
							if($weekly_offdate==0)
								$weekly_offdate=$kv;
							$off_count++;
						}elseif(!empty($gazetted_leaves) && in_array(date("Y-m-d", strtotime($ext_year."-".$month."-".$vj)), $gazetted_leaves) ){
							$style_head = "style='background-color:#e0db98'";
							$gazatted_holiday_count++;
						}else{
							$style_head = '';
						}						
						echo "<td class='tableheader' ".$style_head." >". trans(date("D d", strtotime($ext_year."-".$month."-".$kv))) . "</td>";
					}						

					$gazetted_leaves = array();
					$result = db_query($sql_query, "Can't get results");
					if(db_num_rows($result)){
						while($cont = db_fetch($result))
							$gazetted_leaves[] = $cont[0];
					}	

					if($hrmsetup['EndDay'] > 1 && $hrmsetup['EndDay'] < 31){
						$kv_end_days = $hrmsetup['EndDay'];

						$nxt_mnth =  (($month < 12)? ($month+1) : '1');
						$sql_query = "SELECT date FROM ".TB_PREF."kv_empl_gazetted_holidays WHERE (date BETWEEN '".date("Y-m-d", strtotime($ext_year."-".$nxt_mnth."-1"))."' AND '".date("Y-m-d", strtotime($ext_year."-".$nxt_mnth."-".$kv_end_days))."' ) ";

						$result = db_query($sql_query, "Can't get results");
						if(db_num_rows($result)){
							while($cont = db_fetch($result))
								$gazetted_leaves[] = $cont[0];
						}	

						for($kv=1;$kv<=$kv_end_days;$kv++){
							$day_letters = date("D", strtotime($ext_year."-".$nxt_mnth."-".$kv)) ; 
							if( in_array($day_letters, $weekly_off) ){
								$style_head = "style='background-color:#e0db98'";
								if($weekly_offdate==0)
									$weekly_offdate=$kv;
								$off_count++;
							}elseif( !empty($gazetted_leaves) && in_array(date("Y-m-d", strtotime($ext_year."-".$nxt_mnth."-".$kv)), $gazetted_leaves)){
								$style_head = "style='background-color:#e0db98'";
								$gazatted_holiday_count++;
							}else{
								$style_head = '';
							}						
							echo "<td class='tableheader' ".$style_head." >". trans(date("D d", strtotime($ext_year."-".$nxt_mnth."-".$kv))) . "</td>";
						}
					}
									
					echo "</tr>";
					while ($row = db_fetch_assoc($selected_empl)) {
						$details_single_empl = GetRow('kv_empl_attendancee', array('month' => $month, 'year' => $year, 'empl_id' => $row['empl_id'])); 		
						$nxt_mnth =  (($month < 12)? ($month+1) : '1');		
						if($hrmsetup['EndDay'] > 1 && $hrmsetup['EndDay'] < 31){	
							$details_single_empl_nxt_mnth = GetRow('kv_empl_attendancee', array('month' => $nxt_mnth, 'year' => $year, 'empl_id' => $row['empl_id']));
						} else
							$details_single_empl_nxt_mnth = false;
						if(!$details_single_empl && !$details_single_empl_nxt_mnth){
							echo '<tr style="text-align:center"><td>'.$row['empl_id'].'</td><td>'.$row['empl_name'].'</td>';
							
							$leave_Day = $absDays = 0 ;
							$week_end=1;
							$weekly_offdat = $weekly_offdate;
							$WorkingSeconds = $OT_hours = $holidays = 0;
							$kvj = 1;
							
							if($hrmsetup['BeginDay'] > 1)
								$vj = $hrmsetup['BeginDay'];
							else
								$vj = 1;
							
							$off_day_attendance = 0;
							
							for($vj; $vj<=$total_days; $vj++){		
								$day_letter =  '';
								$day_letters = date("D", strtotime($ext_year."-".$month."-".$vj)) ; 
								if( in_array($day_letters, $weekly_off) ){							
								//if(date("D", strtotime($ext_year."-".$month."-".$vj)) == $weekly_off){
									$style="style='background-color: #fda8a8;'"; 
									$week_end = $workingDayToday =1;
									$weekly_offdat = 7;
									$off_day_attendance = 1;
									$day_letter =  'W';
								} else{
									$off_day_attendance = 0;
									if( !empty($gazetted_leaves) && in_array(date("Y-m-d", strtotime($ext_year."-".$month."-".$vj)), $gazetted_leaves)) {
										$style="style='background-color: #fda8a8;'"; 																			
										$off_day_attendance = 1;
										$day_letter =  'H';
									}else {
										$style=""; 
										$workingDayToday =0;										
									}
									$week_end++;									
								}
								echo '<td '.$style.' >'. ($off_day_attendance == 0 ? 'P':  $day_letter).'</span></td>';								 
							}

							if($hrmsetup['EndDay'] > 1 && $hrmsetup['EndDay'] < 31){
								$kv_end_days = $hrmsetup['EndDay'];									
								$off_day_attendance = 0;
								
								for($vj=1; $vj<=$kv_end_days; $vj++){	
									$day_letter =  '';
									$day_letters = date("D", strtotime($ext_year."-".$nxt_mnth."-".$vj)) ; 
									if( in_array($day_letters, $weekly_off) ){									
										$style="style='background-color: #fda8a8;'"; 
										$week_end = $workingDayToday =1;
										$weekly_offdat = 7;
										$off_day_attendance = 1;
										$day_letter =  'W';
									} else{
										$off_day_attendance = 0;
										if ( !empty($gazetted_leaves) && in_array(date("Y-m-d", strtotime($ext_year."-".$nxt_mnth."-".$vj)), $gazetted_leaves)) {
											$style="style='background-color: #fda8a8;'"; 
											$workingDayToday =1;											
											$off_day_attendance = 1;
											$day_letter =  'H';
										} else {
											$style=""; 
											$workingDayToday =0;
										}
										$week_end++;									
									}
									echo '<td '.$style.' >'. ($off_day_attendance == 0 ? 'P':  $day_letter).'</span></td>';	
								}
							}
							echo '<tr>';
						}
					}
			end_table(1);

		submit_center_first('UpdateAttendance', trans("Update Attendance"),   trans('Check entered data and save document'), 'default');
		
	end_form();?>
	<style>
	#details table.tablestyle { min-width:1330px; } 
	</style><?php 
end_page(); ?>