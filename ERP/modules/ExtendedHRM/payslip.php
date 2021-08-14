<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_PAYSLIP';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");

if (!@$_GET['popup']){
	page(trans("PaySlip"));
} else
	page(trans("Payslip"), true);

check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));
 
check_db_has_salary_account(trans("There are no Salary Account defined in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/hrm_settings.php'>Settings</a> to update it."));

if(isset($_GET['Added'])){
	display_notification(' The Employee Payslip is added #' .$_GET['Added']);
}
if(user_theme() == 'Saaisaran'){ ?>
<style>
@media (min-width: 900px){  table {  width: auto !important; } 	}
</style>
<?php }
$employee_id = get_post('employee_id', '');
$month = get_post('month','');
$current_year =  get_current_fiscalyear();
$year = get_post('year',$current_year['id']);

if (isset($_GET['employee_id'])){
	$employee_id = $_POST['employee_id'] = $_GET['employee_id'];
}
if (isset($_GET['month'])){
	$month = $_POST['month'] = $_GET['month'];
}
if (isset($_GET['year'])){
	$year = $_POST['year'] = $_GET['year'];
}

if(list_updated('month') || get_post('RefreshInquiry') || list_updated('employee_id')|| list_updated('year')) {
	$month = get_post('month');   
	//$_POST['lop_amount'] = 0;
	$Ajax->activate('totals_tbl');
}
if(list_updated('month') || list_updated('year') || list_updated('employee_id'))
	unset($_POST['lop_amount']);

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
$home_nationality = $hrmsetup['nationality'];
$_POST['ear_tot']=$_POST['deduct_tot']=$_POST['empl_dept']=$_POST['adv_sal']=$_POST['net_pay']=$ot_earnings=0;
if(!isset($_POST['lop_amount']))
	$_POST['lop_amount']= 0;
$dim = get_company_pref('use_dimension');
div_start('totals_tbl');
start_form();
	if (db_has_employees()) {
		start_table(TABLESTYLE_NOBORDER);
		start_row();
		kv_fiscalyears_list_cells(trans("Fiscal Year:"), 'year', null, true);
		kv_current_fiscal_months_list_cell("Months", "month", null, true, false,1);
		$db_has_employee_payslip = db_has_employee_payslip($employee_id, $month, $year) ;
		if($db_has_employee_payslip == true){
			if ($dim >= 1){
				dimensions_list_cells(trans("Dimension")." 1", 'dimension_id', null, true, " ", false, 1);
				if ($dim > 1)
					dimensions_list_cells(trans("Dimension")." 2", 'dimension2_id', null, true, " ", false, 2);
			}
			if ($dim < 1)
				hidden('dimension_id', 0);
			if ($dim < 2)
				hidden('dimension2_id', 0);
		}
		end_row();
		start_row();
		department_list_cells(trans("Select a Department")." :", 'dept_id', null, trans("No Department"), true, check_value('show_inactive'));
		employee_list_cells(trans("Select an Employee")." :", 'employee_id', null,	trans("Select Employee"), true, check_value('show_inactive'),false, false,true);
		end_row();
		end_table();
		br();
		if (get_post('_show_inactive_update')) {
			$Ajax->activate('employee_id');
			$Ajax->activate('month');
			$Ajax->activate('year');
			set_focus('employee_id');
		}
	} 
	else {	
		hidden('employee_id');
		hidden('month');
		hidden('year');
	}

if(get_post('employee_id')){
	$_POST['EmplName']=$_POST['desig']=''; 	
	$Empl_job_row =GetRow('kv_empl_job', array('empl_id' => get_post('employee_id')));
	$Empl_info_row =  GetRow('kv_empl_info', array('empl_id' => $employee_id));
	$empl_attendance_ar = array('joining' => $Empl_job_row['joining'], 'status' => $Empl_info_row['status'], 'date_of_status_change' => $Empl_info_row['date_of_status_change']);
	$Empl_grade = $Empl_job_row['grade'];
	hidden('grade_id', $Empl_grade);
	$Allowance  =  kv_get_allowances(null, 0, $Empl_grade);
	//display_error(json_encode($Empl_job_row));
	foreach($Allowance as $single) {	
		//display_error($single['id']);
		if($single['type']=='Earnings' && !isset($_POST[$employee_id.'_'.$single['id']])){
			$_POST[$employee_id.'_'.$single['id']] = 0;
		}
		if($single['type']=='Reimbursement' && !isset($_POST[$employee_id.'_'.$single['id']])){
			$_POST[$employee_id.'_'.$single['id']] = 0;
		}
		if($single['type']=='Deductions' && !isset($_POST[$employee_id.'_'.$single['id']])){
			$_POST[$employee_id.'_'.$single['id']] = 0;
		}
		if($single['type']=='Employer Contribution' && !isset($_POST[$employee_id.'_'.$single['id']])){ 
			$_POST[$employee_id.'_'.$single['id']] = 0;
		}					
	}
	$pf_amt_actual = $gross_4_LOP = $ctc_final = 0; 
 	$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
 	//$total_working_hours =  Get_Monthly_WorkingHours($year, get_post('month'), true);
 	//$weekly_off = unserialize(base64_decode($Empl_job_row['weekly_off'])); //GetSingleValue('kv_empl_job', 'weekly_off', array('empl_id'=>$employee_id));
 	// display_error(json_encode($weekly_off));
 	$duration  = GetEmplAttendanceDuration($employee_id,null,null, (int)get_post('month'), $year,-1,-1, ($Empl_info_row['status'] > 1 ? sql2date($Empl_info_row['date_of_status_change']) : null));
 	//display_error(json_encode($duration));
 	if(!empty($duration))
		$employee_Lop_hours = $duration[0]['total_hrs'] - $duration[0]['Duration'];
	else
		$employee_Lop_hours = 0;
	//display_error($employee_Lop_hours);
	$workedHours  = $otHours = $SplotHours = 0 ;
	if(!empty($duration)){
		foreach($duration as $key => $single){
			$workedHours += $single['Duration'];
			$otHours += $single['OT'];
			$SplotHours += $single['SOT'];
		}
	}

 	$joining_date = $Empl_job_row['joining'];
 	$month_name = kv_month_name_by_id(get_post('month'));
	$end_of_selected_month = end_month(sql2date($months_with_years_list[(int)get_post('month')])); 
	$ext_year = date("Y", strtotime($months_with_years_list[(int)get_post('month')]));

	if($hrmsetup['monthly_choice'] == 2){
		$secondMonth = $month+1;
	} elseif($hrmsetup['monthly_choice'] == 3) {				
		$secondMonth = $month;
	} else {
		$secondMonth = $month;
	}
			
	$payroll_process_end = date("Y-m-d", strtotime($ext_year."-".$secondMonth."-".$hrmsetup['EndDay']));
	//display_error($payroll_process_end);

		$_POST['today_date']=date("d-F-Y");
		//display_error("dsgaegaweg".$employee_id );
		if(isset($employee_id) && $employee_id != '' && date2sql($end_of_selected_month) >= $joining_date && ($Empl_info_row['status'] == 1 || ($Empl_info_row['status']>1 && $Empl_info_row['date_of_status_change'] >= date2sql($end_of_selected_month) ) )) {
			$sal_row = get_empl_sal_details($employee_id, $month, $year); 
			$_POST['empl_dept']=GetSingleValue('kv_empl_departments', 'description', array('id' => $Empl_job_row['department']));
			$_POST['desig'] = $Empl_job_row['desig'];
			$_POST['EmplName']=$Empl_info_row ['empl_firstname'].' '.$Empl_info_row['empl_lastname'];
			$end_of_selected_month = end_month(sql2date($months_with_years_list[(int)get_post('month')])); 
			$begin_this_month = begin_month(sql2date($months_with_years_list[(int)get_post('month')])); 
			 $gross_4_LOP = $ctc_cal = $Reimbursement = $basic_id = $gross_calculation = 0 ;
			foreach($Allowance as $single) {				
				if($single['al_type'] == 0 ){
					if($single['type']=='Earnings'){
						//$allowance_var_ar[$employee_id.'_'.$single['id']] = '{$'.$single['unique_name'].'}';
						if( (isset($sal_row[$single['id']]) && $sal_row[$single['id']] > 0))	
							$_POST[$employee_id.'_'.$single['id']] = ((isset($sal_row[$single['id']]) && $sal_row[$single['id']] > 0 ) ? $sal_row[$single['id']] : (isset($_POST[$employee_id.'_'.$single['id']]) ? $_POST[$employee_id.'_'.$single['id']] : 0 ));
					}
					if($single['type']=='Reimbursement'){
					//	$allowance_var_ar[$employee_id.'_'.$single['id']] = '{$'.$single['unique_name'].'}';
						if((isset($sal_row[$single['id']]) && $sal_row[$single['id']] > 0))	
							$_POST[$employee_id.'_'.$single['id']] = ((isset($sal_row[$single['id']]) && $sal_row[$single['id']] > 0 ) ? $sal_row[$single['id']] : (isset($_POST[$employee_id.'_'.$single['id']]) ? $_POST[$employee_id.'_'.$single['id']] : 0 ));
					}
					if($single['type']=='Deductions'){
						//$allowance_var_ar[$employee_id.'_'.$single['id']] = '{$'.$single['unique_name'].'}';
						if((isset($sal_row[$single['id']]) && $sal_row[$single['id']] > 0))	
							$_POST[$employee_id.'_'.$single['id']] = ((isset($sal_row[$single['id']]) && $sal_row[$single['id']] > 0 ) ? $sal_row[$single['id']] : (isset($_POST[$employee_id.'_'.$single['id']]) ? $_POST[$employee_id.'_'.$single['id']] : 0 ));
					}
					if($single['type']=='Employer Contribution'){ 
						//$allowance_var_ar[$employee_id.'_'.$single['id']] = '{$'.$single['unique_name'].'}';
						if( (isset($sal_row[$single['id']]) && $sal_row[$single['id']] > 0))	
							$_POST[$employee_id.'_'.$single['id']] = ((isset($sal_row[$single['id']]) && $sal_row[$single['id']] > 0 ) ? $sal_row[$single['id']] : (isset($_POST[$employee_id.'_'.$single['id']]) ? $_POST[$employee_id.'_'.$single['id']] : 0 ));
					}	
					
					if($single['basic'] == 1) {
						$basic_id = $single['id'];
					}				
					
					if($single['pf'] == 1){
						if($single['type'] == 'Earnings')
							$pf_amt_actual = $_POST[$employee_id.'_'.$single['id']];
						if($single['type'] == 'Reimbursement')
							$pf_amt_actual = $_POST[$employee_id.'_'.$single['id']];
						if($single['type'] == 'Employer Contribution')
							$pf_amt_actual = $_POST[$employee_id.'_'.$single['id']];
						if($single['type'] == 'Deductions')
							$pf_amt_actual = $_POST[$employee_id.'_'.$single['id']];
					}	

					if($single['loan'] == 1 && !isset($sal_row['net_pay'])) {
						$loans = GetDataJoinRow('kv_empl_loan_types AS type', array( 
									0 => array('join' => 'INNER', 'table_name' => 'kv_empl_loan AS loan', 'conditions' => '`type`.`id` = `loan`.`loan_type_id`'),
									1 => array('join' => 'INNER', 'table_name' => 'kv_empl_allowances AS info', 'conditions' => '`type`.`allowance_id` = `info`.`id`'), 						
								), 
								array('`loan`.`monthly_pay`, `loan`.`date`, `loan`.`periods_paid` , `loan`.`periods`, `loan`.`id`'), array('`loan`.`empl_id`' => $employee_id, '`info`.`id`' => $single['id'], '`loan`.`status`' => "Active"));
								
						if(!empty($loans) && $loans['periods_paid'] < $loans['periods'] && strtotime($loans['date']) <= strtotime($months_with_years_list[(int)get_post('month')]) ){
							$_POST[$employee_id.'_'.$single['id']] = (isset($_POST[$employee_id.'_'.$single['id']]) ? $_POST[$employee_id.'_'.$single['id']] : 1); //$loans[0]['monthly_pay'];	
							$_POST['loan_'.$single['id']] = $loans['id'];
							$_POST['loan_mnth_pay_'.$single['id']] = $loans['monthly_pay'];		
							hidden('loan_'.$single['id'], $_POST['loan_'.$single['id']]);
							hidden('loan_mnth_pay_'.$single['id'], $_POST['loan_mnth_pay_'.$single['id']]);
						}					
					}
				}elseif($single['al_type']==1){  // LMRA Calculation	
							//display_error($get_status_and_date['nationality']);						
							$amount =GetRow('kv_empl_lmra_fees',  array('nationality' => $Empl_job_row['nationality'])); 
							if(!empty($amount))
								$_POST[$employee_id.'_'.$single['id']] = $amount['amount'];
							elseif($amount2 =  GetSingleValue('kv_empl_lmra_fees', 'amount', array('nationality' => 0)))
								$_POST[$employee_id.'_'.$single['id']] = $amount2;
							else
								$_POST[$employee_id.'_'.$single['id']] = 0 ;
						}elseif($single['al_type']==3){  // Medical Allowance
							$medi_category= GetSingleValue('kv_empl_medical_premium', 'month', array('id' => $Empl_job_row['medi_category'])); 
							if($medi_category)
								$_POST[$employee_id.'_'.$single['id']] = $medi_category;
							else
								$_POST[$employee_id.'_'.$single['id']] = null;
						} elseif($single['al_type']==4){	// Visa and immgration
							$visa_details= GetRow('kv_empl_visa_exp', array('nationality' => $Empl_job_row['nationality'], 'family' => $Empl_job_row['family']));							
							if ($visa_details){  
								$_POST[$employee_id.'_'.$single['id']] = $visa_details['month'];
							}else {
								$_POST[$employee_id.'_'.$single['id']] = GetSingleValue('kv_empl_visa_exp', 'month', array('nationality' => 0, 'family' => $Empl_job_row['family']));
							}
						} elseif($single['al_type']==5){ //Leave Travel
							$leave_travel= GetRow('kv_empl_leave_travel', array('nationality' => $Empl_job_row['nationality'], 'family' => $Empl_job_row['family']));
							if ($leave_travel){  
								$_POST[$employee_id.'_'.$single['id']] = $leave_travel['month'];
							}else {
								$_POST[$employee_id.'_'.$single['id']] = GetSingleValue('kv_empl_leave_travel', 'month', array('nationality' => 0, 'family' => $Empl_job_row['family']));
							}
						} elseif($single['al_type']==6){   // Leave Salary 
							$ALDays = ($Empl_job_row['al']/12);
							
							$amt_for_cal = 0 ;
								foreach($Allowance as $singl) {									
									if( !empty($hrmsetup['leave_pay']) && in_array($singl['id'], $hrmsetup['leave_pay']))
										$amt_for_cal += $_POST[$employee_id.'_'.$singl['id']] ;
								}								
								if($amt_for_cal==0)
									$amt_for_cal = $gross_calculation;								
								
							$_POST[$employee_id.'_'.$single['id']] =((($amt_for_cal*12)/365)*$ALDays);
							if(date2sql($begin_this_month) < $Empl_job_row['joining']){
								$days = 30 - (int)date('d', strtotime($Empl_job_row['joining']));								
								$ALDays = ($ALDays/30)*$days;
								$_POST[$employee_id.'_'.$single['id']] =((($amt_for_cal*12)/365)*($ALDays));
							} 
							if(!isset($sal_row['net_pay']) && $Empl_info_row['status']>1 && $Empl_info_row['date_of_status_change'] > date2sql($begin_this_month) && $Empl_info_row['date_of_status_change'] < date2sql($end_of_selected_month) ) {
								$days = (int)date('d', strtotime($Empl_info_row['date_of_status_change']));								
								$ALDays = ($ALDays/30)*$days;
								$_POST[$employee_id.'_'.$single['id']] =((($amt_for_cal*12)/365)*($ALDays));
							}
						} elseif($single['al_type']==7){   // Indemnity
							if($home_nationality  != $Empl_job_row['nationality']){
								$date1 = new DateTime($Empl_job_row['joining']);
								$date2 = new DateTime(date('Y-m-d'));
								$interval = $date1->diff($date2);
								$years_exp = $interval->y + ($interval->m/12) + ($interval->d/365);							
								$cal_days = (($years_exp > 3) ? 30 : 15 );
								$amt_for_cal = 0 ;
								foreach($Allowance as $singl) {									
									if( !empty($hrmsetup['indemnity']) && in_array($singl['id'], $hrmsetup['indemnity']))
										$amt_for_cal += $_POST[$employee_id.'_'.$singl['id']] ;
								}								
								if($amt_for_cal==0)
									$amt_for_cal = $gross_calculation;
								$_POST[$employee_id.'_'.$single['id']] =((($amt_for_cal*12)/365)*$cal_days)/12;								
							} else {
								$_POST[$employee_id.'_'.$single['id']] = 0;
							}
						} elseif($single['al_type'] == 2) {

							if((!isset($_POST[$employee_id.'_'.$single['id']]) || $_POST[$employee_id.'_'.$single['id']] == '')){ //GOSI Calculation
								
								$gosi_emplee= GetRow('kv_empl_gosi_settings', array('nationality' => $Empl_job_row['nationality']));
								if(empty($gosi_emplee))
									$gosi_emplee =  GetRow('kv_empl_gosi_settings', array('nationality' => 0));
								$gosi_cal_amt = 0 ;
								$gosi_al = unserialize(base64_decode($gosi_emplee['allowances']));
								if(!empty($gosi_al)){									
									foreach($gosi_al as $singl){
										$gosi_cal_amt += input_num($employee_id.'_'.$singl);
									}
								} 
								if($gosi_cal_amt == 0 )
									$gosi_cal_amt = $gross_calculation; 
								
								if($single['type']=='Deductions' ){									
									if ($gosi_emplee){  							
										$_POST[$employee_id.'_'.$single['id']] = $gosi_cal_amt * ($gosi_emplee['employee']/100);
									}else {  	
										$_POST[$employee_id.'_'.$single['id']] = $gosi_cal_amt * ($gosi_emplee['employee']/100);
									}
								}	elseif( $single['type']=='Employer Contribution' ){									
									if ($gosi_emplee){  							
										$_POST[$employee_id.'_'.$single['id']] = $gosi_cal_amt * ($gosi_emplee['employer']/100);
									} else {  
										$_POST[$employee_id.'_'.$single['id']] = $gosi_cal_amt * ($gosi_emplee['employer']/100);
									}
								}
								//display_error($_POST[$employee_id.'_'.$single['id']].json_encode($gosi_emplee));
							} 
						}
				if($single['Tax'] == 1 )	
					$Tax_id = $employee_id.'_'.$single['id'];
				
				if($single['type'] == 'Earnings' && $single['value'] != 'Payroll Input'){
					$gross_4_LOP += input_num($employee_id.'_'.$single['id']);
				}
				if($single['type']=='Earnings')
						$gross_calculation  += input_num($employee_id.'_'.$single['id']);
				if($single['type'] == 'Reimbursement') { 
					$Reimbursement += input_num($employee_id.'_'.$single['id']);
				}
				if( $single['type'] == 'Employer Contribution' ){  
					$ctc_cal += input_num($employee_id.'_'.$single['id']);
				}	
				if($single['basic'] == 1)	
						$basic_allowance = input_num($employee_id.'_'.$single['id']);
			}
			
			if($gross_4_LOP > 0 )
				$gross_amt = $gross_4_LOP;
			else
				$gross_amt = $Empl_job_row['gross'];
			
			$ctc_final = $ctc_cal + $gross_amt+ $Reimbursement;	
			$_POST['adv_sal']= GetSingleValue('kv_empl_salary_advance', 'amount', array('empl_id' => $employee_id, 'month' => get_post('month'), 'year' => get_post('year')));
								
			if(isset($sal_row['net_pay'])){
				$_POST['lop_amount'] =  $sal_row['lop_amount'];
				$_POST['adv_sal']= $sal_row['adv_sal'];
				$_POST['net_pay'] = $sal_row['net_pay'];		 
				$ot_earnings = $sal_row['ot_earnings'];			 
				$_POST['today_date'] = $sal_row['date'];	
				//$_POST['loan'] = $sal_row['loan'] ;
			} else{
				if(!isset($_POST['lop_amount']) || $_POST['lop_amount'] == 0){
					if(isset($duration[0]['total_hrs']))									
						$_POST['lop_amount'] = round((($gross_4_LOP/$duration[0]['total_hrs'])*abs($employee_Lop_hours)), 2);
					else
						$_POST['lop_amount'] = $gross_4_LOP;		
				}
				$basic_amount = ($basic_id ? $Empl_job_row[$basic_id] : 0 );

					$ot_mul_factor =$hrmsetup['ot_factor'];
					if($duration[0]['days_count'] > 0 )
						$days_count = $duration[0]['days_count'];
					else					
						$days_count = 30; 
					$shift_time = GetRow('kv_empl_shifts', array('id' => $Empl_job_row['shift']));
					if(!empty($shift_time)){
						$BeginTime = $shift_time['BeginTime'];
						$EndTime = $shift_time['EndTime'];
					} else {
						$BeginTime = $hrmsetup['BeginTime'];
						$EndTime = $hrmsetup['EndTime'];
					}
					if((strtotime($EndTime)-strtotime($BeginTime)) < 0){
						$attendance_date_plus_1 = date2sql(add_days(Today(), 1));
						$EndTime  = $attendance_date_plus_1.' '.$EndTime; 
					}
					
					//SplitHours

					$ot_earnings = round((($hrmsetup['ot_factor']) * (($basic_allowance/$days_count)/(strtotime($EndTime)-strtotime($BeginTime)))*$otHours),2);
					//display_error($ot_earnings.'--'.$days_count);
					//display_error($hrmsetup['ot_factor'].'--'.$basic_allowance.'--'.$days_count.'--'.$EndTime.'--'.$BeginTime.'--'.$otHours);
					$ot_earnings += round((($hrmsetup['special_ot_factor']) * (($basic_allowance/$days_count)/(strtotime($EndTime)-strtotime($BeginTime)))*$SplotHours),2);
					//display_error('---'.$ot_earnings);

			}				
		}else{
			if($months_with_years_list[(int)get_post('month')] < $joining_date)
				display_warning(trans("You can't Pay Employee Salary before his Joining Date!"));
		}		
		$gross_4_grand = 0 ;
		foreach($Allowance as $single) {	
			if($single['type'] == 'Earnings' ){
				$gross_4_grand += input_num($employee_id.'_'.$single['id']);
			}								
		}	
		$_POST['ear_tot'] = $ot_earnings+$gross_4_grand;
		
		if(!isset($sal_row['net_pay']) && isset($Tax_id)){	
			if(isset($_POST[$Tax_id]))
				$_POST[$Tax_id] = input_num($Tax_id);
			else
				$_POST[$Tax_id] = kv_get_tax_for_an_employee($employee_id, get_post('year'), $_POST['ear_tot'], get_post('month'));		
		}

		//hidden('allowed_ml',$duration[0]['ml']);
		//hidden('allowed_sl',$duration[0]['sl']);
		//hidden('allowed_slh',$duration[0]['slh']);
		//hidden('allowed_hl',$durartion[0]['hl']);
		hidden('used_al',$duration[0]['al']);
		start_outer_table(TABLESTYLE);
		table_section(1);
		label_row(trans("Employee No:"), $employee_id);
		label_row(trans("Employee Name:"), $_POST['EmplName']);
		label_row(trans("Department:"), $_POST['empl_dept']);
		label_row(trans("Designation:"), GetSingleValue('kv_empl_designation', 'description', array('id' => $_POST['desig'])));
		label_row(trans("Month of Payment:"), $month_name);
		if(isset($sal_row['currency']) && $sal_row['currency'] != ''){
			$curr_code =  $sal_row['currency'];
			$ex_dat = (isset($sal_row['date']) ? sql2date($sal_row['date']) : Today());
			$ex_rate = number_format(get_exchange_rate_from_home_currency($curr_code, $ex_dat), 4);
		} else{
			$curr_code = get_company_currency();
			$ex_rate = 1; 
		} 

		label_row(trans("Currency:"), GetSingleValue('currencies', 'currency', array('curr_abrev' => $curr_code)));
		hidden('currency', $curr_code);
		table_section_title(trans("Earnings"));
		foreach($Allowance as $single) {	
			if($single['type'] == 'Earnings' ){
				if($single['value'] == 'Payroll Input' && $single['basic'] != 1){
					if(isset($sal_row['net_pay']))
						label_row(trans($single['description']), price_format($_POST[$employee_id.'_'.$single['id']]), '', ' style=" text-align:right;" ');
					else
						amount_row(trans($single['description']), $employee_id.'_'.$single['id'], null);
				}else
					label_row(trans($single['description']), price_format($_POST[$employee_id.'_'.$single['id']]), '', ' style=" text-align:right;" ');
			} 
		}
		label_row(trans("OT Earnings:"), price_format($ot_earnings), '', ' style=" text-align:right;" ');
		hidden('ot_earnings', $ot_earnings);
		
		$reimburse = 0; 
		table_section_title(trans("Reimbursement"));
		foreach ($Allowance as $single) {
			if($single['type'] == 'Reimbursement' ){	
				if(!isset($sal_row['net_pay']) && $single['value'] == 'Payroll Input')
					amount_row(trans($single['description']), $employee_id.'_'.$single['id'], null);
				else
					label_row(trans($single['description']), price_format($_POST[$employee_id.'_'.$single['id']]), '', ' style=" text-align:right;" ');
				$reimburse += input_num($employee_id.'_'.$single['id']);
			}
		}	
		$_POST['payable_gross'] = $_POST['ear_tot']+ $reimburse;
		label_row(trans("Total Earning(Gross Salary):"), price_format($_POST['payable_gross']), 'style="color:#FF9800; background-color:#f9f2bb;"', 'style="color:#FF9800; background-color:#f9f2bb;text-align:right"');
		hidden('gross_for_cal', $_POST['payable_gross']);
		
		table_section_title(trans(" "));
		if($hrmsetup['enable_esic']) {
			$sql = "SELECT amt_limit FROM ".TB_PREF."kv_empl_esic_pf WHERE date = (SELECT MAX(date) FROM ".TB_PREF."kv_empl_esic_pf WHERE allowance_id='esic' LIMIT 0, 1) "; 
			$result = db_query($sql, "Can't get esic amount");
			if($row =  db_fetch($result)){
				$esic_gross_amount =  $row[0];
			}else{
				display_warning(trans("No ESIC Limit set. Please set it under HRM-> ESIC PF Settngs"));
			}
		} else
			$esic_gross_amount = 0;
		
		
		$esic = $pf = false; 
		if($esic_gross_amount >= $_POST['ear_tot'])	{
			$esic = true;		
		}else
			$esic = false;
		if($hrmsetup['enable_pf']) {
			$pf_sql = "SELECT amt_limit FROM ".TB_PREF."kv_empl_esic_pf WHERE date = (SELECT MAX(date) FROM ".TB_PREF."kv_empl_esic_pf WHERE allowance_id='pf' LIMIT 0, 1) "; 
			$pf_result = db_query($pf_sql, "Can't get esic amount");
			if($pf_row =  db_fetch($pf_result)){
				$pf_amount =  $pf_row[0];
			}else{
				display_warning(trans("No PF Limit set. Please set it under HRM-> ESIC PF Settngs"));
			}
		}else 
			$pf_amount = 0;
		if($pf_amount >= $pf_amt_actual)	{
			$pf = true;
		}
		$ctc_grand = $_POST['ear_tot']+$reimburse;
		table_section_title(trans("Employer Contribution"));
		foreach ($Allowance as $single) {
			if( $single['type'] == 'Employer Contribution' ){  
				if($single['esic'] != 1 && $single['pf'] != 1){
					if(!isset($sal_row['net_pay']) && $single['value'] == 'Payroll Input')
						amount_row(trans($single['description']), $employee_id.'_'.$single['id'], null);
					else
						label_row(trans($single['description']), price_format($_POST[$employee_id.'_'.$single['id']]), '', ' style=" text-align:right;" ');
					$ctc_grand += input_num($employee_id.'_'.$single['id']);
				}else{
					if(($single['pf'] == 1 && $pf == false) || ($single['esic'] == 1 && $esic ==false))
						$_POST[$employee_id.'_'.$single['id']] = 0 ;
					if($esic && $single['esic'] == 1){	
						if(!isset($sal_row['net_pay']) && $single['value'] == 'Payroll Input')
							amount_row(trans($single['description']), $employee_id.'_'.$single['id'], null);
						else
							label_row(trans($single['description']), price_format($_POST[$employee_id.'_'.$single['id']]), '', ' style=" text-align:right;" ');
					}
					if($pf && $single['pf'] == 1){	
						if(!isset($sal_row['net_pay']) && $single['value'] == 'Payroll Input')
							amount_row(trans($single['description']), $employee_id.'_'.$single['id'], null);
						else
							label_row(trans($single['description']), price_format($_POST[$employee_id.'_'.$single['id']]), '', ' style=" text-align:right;" ');
					}
					$ctc_grand += input_num($employee_id.'_'.$single['id']);
				}				
			}
		}				
		
		label_row(trans('CTC '), price_format($ctc_grand), 'style="color:#9C27B0; background-color:rgba(156, 39, 176, 0.23);"', 'style="color:#9C27B0; background-color:rgba(156, 39, 176, 0.23);text-align:right;"');
		table_section(2);
		hidden('ctc', $ctc_grand);
		label_row(trans("Date of Payment:"), date("d-F-Y", strtotime($_POST['today_date'])));
		label_row(trans("Grade:"), GetSingleValue('kv_empl_grade', 'description', array('id' => $Empl_job_row['grade'])));
		//label_row(trans("Total No of days:"), $duration[0]['total_days']);
		hidden('workedHours', $workedHours);
		hidden('otHours', $otHours);
		hidden('SplotHours', $SplotHours);		
		$hours = floor($workedHours / 3600);
		$mins = floor($workedHours / 60 % 60);
		$employee_working_hours_display = ($hours < 10 ? '0'.$hours : $hours).':'.($mins < 10 ? '0'.$mins : $mins); 
		$ot_hours = floor($otHours / 3600);
		$ot_mins = floor($otHours / 60 % 60);
		$employee_OT_hours_display = ($ot_hours < 10 ? '0'.$ot_hours : $ot_hours).':'.($ot_mins < 10 ? '0'.$ot_mins : $ot_mins);
				
		label_row(trans("Payable Hours:"), $employee_working_hours_display);
		label_row(trans("OT Hours:"), $employee_OT_hours_display);
		$sot_hours = floor($SplotHours / 3600);
		$sot_mins = floor($SplotHours / 60 % 60);
		$employee_sOT_hours_display = ($sot_hours < 10 ? '0'.$sot_hours : $sot_hours).':'.($sot_mins < 10 ? '0'.$sot_mins : $sot_mins);
		
		label_row(trans("Special OT Hours:"), $employee_sOT_hours_display);
	    label_row(trans("Exchange Rate").":", number_format2($ex_rate,4));
	    hidden('rate', $ex_rate); 
		table_section_title(trans("Deduction"));
		$deduct_tot = 0 ;		 
		foreach($Allowance as $single) {
			if($single['type'] == 'Deductions'){
				if($single['esic'] != 1 && $single['pf'] != 1){
					if(!isset($sal_row['net_pay']) && $single['value'] == 'Payroll Input' && $single['loan'] != 1){
						amount_row($single['description'], $employee_id.'_'.$single['id'], null);
						$deduct_tot += $_POST[$employee_id.'_'.$single['id']];
					} elseif( $single['loan'] != 1 || isset($sal_row['net_pay'])){
						label_row(trans($single['description']), price_format($_POST[$employee_id.'_'.$single['id']]), '', ' style=" text-align:right;" ');
						$deduct_tot += $_POST[$employee_id.'_'.$single['id']];
					} elseif(isset($_POST['loan_mnth_pay_'.$single['id']])) {
						kv_loan_balance_dropdown_row($single['description'], $employee_id.'_'.$single['id'], get_post('loan_'.$single['id']), false, true);
						$deduct_tot += $_POST[$employee_id.'_'.$single['id']]*$_POST['loan_mnth_pay_'.$single['id']];
						if(list_updated($employee_id.'_'.$single['id']))
							$Ajax->activate('totals_tbl');
					}
					
				}else {
					if(($single['pf'] == 1 && $pf == false) || ($single['esic'] == 1 && $esic ==false))
						$_POST[$employee_id.'_'.$single['id']] = 0 ;
					if($single['esic'] == 1 && $esic){						
						label_row(trans($single['description']), price_format($_POST[$employee_id.'_'.$single['id']]), '', ' style=" text-align:right;" ');
						$deduct_tot += $_POST[$employee_id.'_'.$single['id']];
					}
					if($single['pf'] == 1 && $pf){
						label_row(trans($single['description']), price_format($_POST[$employee_id.'_'.$single['id']]), '', ' style=" text-align:right;" ');
						$deduct_tot += $_POST[$employee_id.'_'.$single['id']];
					}
				}
			}
		}
		if(!isset($sal_row['net_pay']))
			amount_row(trans("Absent Deduction:"), 'lop_amount', null);
		else
			label_row(trans("Absent Deduction"), price_format($_POST['lop_amount']), '', ' style=" text-align:right;" ');
		label_row(trans("Advance Salary:"), price_format($_POST['adv_sal']), '', ' style=" text-align:right;" ');
		hidden('adv_sal', $_POST['adv_sal']);
		if(!isset($sal_row['net_pay'])){
			submit_cells('RefreshInquiry', trans("Refresh"),'',trans('Show Results'), 'default');
		}		
		table_section_title(trans(" "));
		
		$deduct_tot += $_POST['adv_sal']+input_num('lop_amount');		
		label_row(trans("Total Deductions"), price_format($deduct_tot), 'style="color:#f55; background-color:#fed;"', 'style="color:#f55; background-color:#fed;text-align:right;"');
		label_row(trans(" "), '', null, 30, 30);
		if(!isset($sal_row['net_pay'])){
			$_POST['net_pay'] = $_POST['payable_gross']-$deduct_tot;
			//display_error($_POST['ear_tot']);
		}		
		label_row(trans("Net Salary Payable:"), price_format($_POST['net_pay']), 'style="color:#107B0F; background-color:#B7DBC1;"', 'style="color:#107B0F; background-color:#B7DBC1;text-align:right;"');
		end_outer_table();
		
		if($db_has_employee_payslip == false && $employee_id != null){
			br(2);		
			foreach($Allowance as $single) {	
				if($single['type'] == 'Deductions')
					hidden($single['id'], $_POST[$employee_id.'_'.$single['id']]);
			}
			//hidden('lop_amount', $_POST['lop_amount']);
			hidden('net_pay', $_POST['net_pay']);
			//hidden('date_of_pay', Today());
			
			//$end_day_of_this = date("Y-m-d", strtotime($months_with_years_list[(int)get_post('month')]));
			//display_error($payroll_process_end .'-'.date('Y-m-d') );
			if( $payroll_process_end < date('Y-m-d'))
				submit_center('pay_salary', trans("Process Payout"), true, trans('Payout to Employees'), 'default');
			else
				display_warning(trans("You can't Process Payroll of future!"));
			br();
			end_form();
		}
		if($db_has_employee_payslip == true && $employee_id != null){
			br(2);
			if(!isset($sal_row['id']))
				$sal_row['id'] = 0;
			$result = get_gl_trans(99, $sal_row['id']);

			if (db_num_rows($result) == 0){
				echo "<p><center>".trans("No general ledger transactions have been created for")."</center></p><br>";
				end_page(true);
				exit;
			}

			/*show a table of the transactions returned by the sql */
			$dim = get_company_pref('use_dimension');

			if ($dim == 2)
				$th = array(trans("Account Code"), trans("Account Name"), trans("Dimension")." 1", trans("Dimension")." 2", trans("Debit"), trans("Credit"), trans("Memo"));
			else if ($dim == 1)
				$th = array(trans("Account Code"),trans("Account Name"),trans("Dimension"),trans("Debit"),trans("Credit"),trans("Memo"));
			else		
				$th = array(trans("Account Code"), trans("Account Name"), trans("Debit"), trans("Credit"), trans("Memo"));
			$k = 0; //row colour counter
			$heading_shown = false;

			$credit = $debit = 0;
			while ($myrow = db_fetch($result)) 	{
				if ($myrow['amount'] == 0) continue;
				if (!$heading_shown){
					//display_gl_heading($myrow);
					start_table(TABLESTYLE, "width='95%'");
					table_header($th);
					$heading_shown = true;
				}	

				alt_table_row_color($k);
				
				label_cell($myrow['account']);
				label_cell($myrow['account_name']);
				if ($dim >= 1)
					label_cell(get_dimension_string($myrow['dimension_id'], true));
				if ($dim > 1)
					label_cell(get_dimension_string($myrow['dimension2_id'], true));

				display_debit_or_credit_cells($myrow['amount']);
				label_cell($myrow['memo_']);
				end_row();
				if ($myrow['amount'] > 0 ) 
					$debit += $myrow['amount'];
				else 
					$credit += $myrow['amount'];
			}
			if ($heading_shown){
				start_row("class='inquirybg' style='font-weight:bold'");
				label_cell(trans("Total"), "colspan=2");
				if ($dim >= 1)
					label_cell('');
				if ($dim > 1)
					label_cell('');
				amount_cell($debit);
				amount_cell(-$credit);
				label_cell('');
				end_row();
				end_table(1);
			}

			echo '<center> <a href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep802.php?PARAM_0='.$year.'&PARAM_1='.$month.'&PARAM_2='.$employee_id.'&rep_v=yes" target="_blank" class="printlink"> Print </a> </center>'; 
			br();
			hidden('payslip_id', $sal_row['id']);
			//submit_center('Void', trans("Void"), true, trans('Void'), 'default');
			echo '<center><button class="inputsubmit" type="submit" aspect="default nonajax process" name="Void" id="Void" value="Void"><img src="'.$path_to_root.'/themes/Saaisaran/images/ok.gif" height="12" alt=""><span>Void</span></button></center>';
			end_form();
		}
	}
	div_end();
	if(get_post('Void')){
		Void_Payroll($_POST['payslip_id']);
		display_notification(trans("Selected Payslip Voided Successfully"));
		echo "<script type='text/javascript'>function kv_setComboItem(){  window.opener.location.reload(); window.close();  } kv_setComboItem(); </script>";
	}
	if(get_post('pay_salary')) {
		$Ajax->activate('totals_tbl');
		begin_transaction();
		$jobs_arr =  array('empl_id' => $_POST['employee_id'],
							 'month' => $_POST['month'],
							 'year' => $_POST['year'],
							 'currency' => $_POST['currency'],
							 'rate' => input_num('rate'),
							 'gross' => input_num('ear_tot'),							 
							// 'ml' => $_POST['allowed_ml'],							 
							//'sl' => $_POST['allowed_sl'],							 
							//'slh' => $_POST['allowed_slh'],							 
							//'hl' => $_POST['allowed_hl'],							 
							 'al' => $_POST['used_al'],								 
							 'dimension' => $_POST['dimension_id'],
							 'dimension2' => $_POST['dimension2_id'], 						 
							 'ctc' => input_num('ctc'),							 
							 'date' => array(Today(), 'date'), 
							 'adv_sal' => input_num('adv_sal'),
							 'net_pay' =>  input_num('net_pay'),							
							 'ot_earnings' =>  input_num('ot_earnings'),
						 	 'lop_amount' => input_num('lop_amount'));
			$Allowance = kv_get_allowances(null, 0, $_POST['grade_id']);
			$loan_id = array();
			$loan_id_amount = 0;
			foreach($Allowance as $single) {	
				if($single['type'] == 'Deductions' && $single['loan'] != 1)
					$jobs_arr[$single['id']] = input_num($_POST['employee_id'].'_'.$single['id']);	
				if($single['type'] == 'Earnings')
					$jobs_arr[$single['id']] = input_num($_POST['employee_id'].'_'.$single['id']);	
				if($single['type'] == 'Reimbursement')
					$jobs_arr[$single['id']] = input_num($_POST['employee_id'].'_'.$single['id']);	
				if($single['type'] == 'Employer Contribution')
					$jobs_arr[$single['id']] = input_num($_POST['employee_id'].'_'.$single['id']);	
				if($single['loan'] == 1 && isset($_POST['loan_'.$single['id']])){
					$loan_id[] = array($_POST['loan_'.$single['id']], $_POST['loan_mnth_pay_'.$single['id']], $_POST[$_POST['employee_id'].'_'.$single['id']]);  
					$jobs_arr[$single['id']] = input_num($_POST['employee_id'].'_'.$single['id'])*$_POST['loan_mnth_pay_'.$single['id']];
					$loan_id_amount += input_num($_POST['employee_id'].'_'.$single['id'])*$_POST['loan_mnth_pay_'.$single['id']];
				}				
			}
			$jobs_arr['loans'] = base64_encode(serialize($loan_id));
			$pay_slip_id = Insert('kv_empl_salary', $jobs_arr);
		commit_transaction();
		meta_forward($_SERVER['PHP_SELF'], "Added=$pay_slip_id&employee_id=".$_POST['employee_id'].'&month='.$_POST['month'].'&year='.$_POST['year']);
	}	

end_page(); ?>