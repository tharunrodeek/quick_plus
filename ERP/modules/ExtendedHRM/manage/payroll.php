<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_PAYSLIP';
$path_to_root="../../..";
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

page(trans("Payroll Process"));
 
check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));

if (isset($_GET['dept_id'])){
	$_POST['dept_id'] = $_GET['dept_id'];
}
if (isset($_GET['month'])){
	$_POST['month'] = $_GET['month'];
}
if (isset($_GET['year'])){
	$_POST['year'] = $_GET['year'];
}
$dept_id = get_post('dept_id','');
$month = get_post('month','');
$current_year =  get_current_fiscalyear();
$year = get_post('year',$current_year['id']);
 
 if(list_updated('month')) {
		$month = get_post('month');   
		$Ajax->activate('RefreshPayroll');
}

$dim = get_company_pref('use_dimension');
start_form();

if (db_has_employees()) {
	start_table(TABLESTYLE_NOBORDER);
	start_row();	
		kv_fiscalyears_list_cells(trans("Fiscal Year:"), 'year', null, true);
		kv_current_fiscal_months_list_cell("Months", "month", null, true,false, 1);		
		end_row();
	start_row();
		kv_empl_grade_list_cells( trans("Grade :"), 'grade', null, false, true);
		department_list_cells(trans("Select a Department")." :", 'dept_id', null,	trans("No Department"), true, check_value('show_inactive'));
		//employee_list_cells(trans("Select an Employee"). " :", 'empl_id', null,	trans("Select an Employee"), true, check_value('show_inactive'), false, false,true);
	end_row();	
	end_table();

	if (get_post('_show_inactive_update') || get_post('month') || get_post('year') || list_updated('dept_id')) {
		$Ajax->activate('dept_id');
		$Ajax->activate('month');
		$Ajax->activate('year');
		$Ajax->activate('sal_calculation');
		set_focus('dept_id');
	}
}
else{
	hidden('dept_id', get_post('dept_id'));
	hidden('month', get_post('month'));
	hidden('year', get_post('year'));
}

div_start('sal_calculation');

	if(empty($dept_id)) $dept_id = 0;
	$grade = get_post('grade') ; 
	$get_employees_list = get_empl_ids_from_dept_id_payroll($dept_id, $grade);

	start_table(TABLESTYLE_NOBORDER, "width=40%");
	label_row("**".trans("Here, you can view the un-Processed Salaries."), '', ' style="text-align:center;" ' );
	end_table();
   //$prof_tax = kv_get_Taxable_field();
	start_table(TABLESTYLE, "width=90%");
	$th = array(trans("Empl Id"),trans("Employee Name"), trans("Currrency"), trans("Exchange Rate"));
    $All_Allowance = kv_get_allowances(null, 0, $grade);
	$basic_id = 0 ;
	foreach($All_Allowance as $single) {	
		if($single['type'] == 'Earnings')
			$th[] = array($single['description'] , '#f9f2bb', '#2196F3');
		if($single['basic'] == 1)
			$basic_id = $single['id'];
	}
	$th[] = array(trans("OT"), '#f9f2bb', '#2196F3');
	foreach($All_Allowance as $single) {	
		if($single['type'] == 'Reimbursement')
			$th[] = array($single['description'] , '#f9f2bb', '#2196F3');
	}
	$th[] = array(trans("Gross Pay"), '#f9f2bb', '#2196F3');
	foreach($All_Allowance as $single) {	
		if($single['type'] == 'Employer Contribution')
			$th[] = array($single['description'] , 'rgba(156, 39, 176, 0.23)', '#9C27B0');
	}
	$th[] = array(trans("CTC"), 'rgba(156, 39, 176, 0.23)', '#9C27B0');
	foreach($All_Allowance as $single) {	
		if($single['type'] == 'Deductions')
			$th[] = array($single['description'] , '#fed', '#f55');
	}
   	$th1 = array(array(trans("Adv Salary") , '#fed', '#f55'), /*array(trans("LOP Days"), '#fed', '#f55'),*/ array(trans(" Absent Deduction") , '#fed', '#f55'),  array(trans("Total Deduction") , '#fed', '#f55'),array(trans("Net Salary"), '#B7DBC1' ,  '#107B0F'));
   	$th_final = array_merge($th, $th1);
	if ($dim >= 1)
		$dimension = trans("Dimensions");
	else
		$dimension ="";
	$th_final[] = $dimension;
	//table_header($th_final);	
	echo '<tr>';	
	foreach($th_final as $header){
		if(is_array($header)){
			echo '<td style="background:'.$header[1].';color:'.$header[2].'"> '.$header[0].'</td>';
		} else {
			echo '<td class="tableheader"> '.$header.'</td>';
		}
	} echo '</tr>';

	$ipt_error = 0;
	//$total_working_hours =  Get_Monthly_WorkingHours($year, get_post('month'), true);	
	if(empty($dept_id)) $dept_id = -1;		
		$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
		if($months_with_years_list[(int)get_post('month')] > date('Y-m-d')){
			display_error(trans("The Selected Month Yet to Born!"));
			$ipt_error = 1;
		}
		if($ipt_error ==0) {		
			if(list_updated('month') || list_updated('year') || list_updated('grade') || list_updated('dept_id')){
				foreach($get_employees_list as $single_empl){
					unset($_POST[$single_empl['empl_id'].'lop_amount']);
				}
			}
			if(get_post('RefreshPayroll')){	
				foreach($get_employees_list as $single_empl) {
					if(isset($_POST[$single_empl['empl_id'].'lop_amount']))
						$_POST[$single_empl['empl_id'].'lop_amount'] = input_num($single_empl['empl_id'].'lop_amount');
					else
						$_POST[$single_empl['empl_id'].'lop_amount'] = 0;
				}
				$Ajax->activate('RefreshPayrollCalculation');
			}
			$Total_gross = $total_net = 0; 
			$end_of_selected_month = end_month(sql2date($months_with_years_list[(int)get_post('month')])); 
		
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
		$ext_year = date("Y", strtotime($months_with_years_list[(int)get_post('month')]));
		if($hrmsetup['monthly_choice'] == 2){
			$secondMonth = $month+1;
		} elseif($hrmsetup['monthly_choice'] == 3) {				
			$secondMonth = $month;
		} else {
			$secondMonth = $month;
		}
				
		$payroll_process_end = date("Y-m-d", strtotime($ext_year."-".$secondMonth."-".$hrmsetup['EndDay']));		
		div_start('RefreshPayrollCalculation');
		$date_of_pay = Today();
		hidden('date_of_pay', $date_of_pay);
		
		$empl_ids = array();
			foreach($get_employees_list as $single_empl) { 
				$Allowance = kv_get_allowances(null, 0, $single_empl['grade']);
				if($single_empl['status'] == 1 || ($single_empl['status']>1 && $single_empl['date_of_status_change'] >= date2sql($end_of_selected_month ) )){
				
					$Empl_job_row = GetRow('kv_empl_job', array('empl_id' => $single_empl['empl_id']));
					$Empl_info_row = GetRow('kv_empl_info', array('empl_id' => $single_empl['empl_id']));
					$empl_attendance_ar = array('joining' => $Empl_job_row['joining'], 'status' => $single_empl['status'], 'date_of_status_change' => $single_empl['date_of_status_change']);
					$empl_id = $Empl_job_row['empl_id'];
					$existing_empl_sal = GetRow('kv_empl_salary', array('empl_id' => $empl_id, 'month' => (int)get_post('month'), 'year' => $year));					
					$end_of_selected_month = end_month(sql2date($months_with_years_list[(int)get_post('month')]));
					if($Empl_job_row && empty($existing_empl_sal) && date2sql($end_of_selected_month) >= get_employee_join_date($empl_id)) {						
						$_POST[$empl_id.'empl_id']= $empl_id; 						
						foreach($Allowance as $single) {
							if($single['al_type'] == 0 ){
								if($single['type']=='Earnings' && !isset($_POST[$empl_id.'_'.$single['id']]))
									$_POST[$empl_id.'_'.$single['id']]= $Empl_job_row[$single['id']];

								if($single['type']=='Reimbursement' && !isset($_POST[$empl_id.'_'.$single['id']]))
									$_POST[$empl_id.'_'.$single['id']] = $Empl_job_row[$single['id']];
			
								if($single['type']=='Deductions' && !isset($_POST[$empl_id.'_'.$single['id']]))
									$_POST[$empl_id.'_'.$single['id']] = $Empl_job_row[$single['id']];
								
								if($single['type']=='Employer Contribution' && !isset($_POST[$empl_id.'_'.$single['id']])){
									$_POST[$empl_id.'_'.$single['id']] = $Empl_job_row[$single['id']];										
								}
							}						
						}						
					}
				}
			
				if($single_empl['empl_id'] && empty($existing_empl_sal) && date2sql($end_of_selected_month) >= get_employee_join_date($single_empl['empl_id']) && ($single_empl['status'] == 1 || ($single_empl['status']>1 && $single_empl['date_of_status_change'] >= date2sql($end_of_selected_month) ) )) {
					$empl_ids[] = $single_empl['empl_id'];					
					$pf_amt_actual = 0; 
					$duration  = GetEmplAttendanceDuration($single_empl['empl_id'],null,null, (int)get_post('month'), $year,-1,-1, ($Empl_info_row['status'] > 1 ? sql2date($Empl_info_row['date_of_status_change']) : null), $hrmsetup['weekly_off']);
					//display_error(json_encode($duration));
					if(!empty($duration))
						$employee_Lop_hours = $duration[0]['total_hrs'] - $duration[0]['Duration'];
					else
						$employee_Lop_hours = 0;
					$workedHours  = $otHours = $SplotHours = 0 ;
					if(!empty($duration)){
						foreach($duration as $key => $single){
							$workedHours += $single['Duration'];
							$otHours += $single['OT'];
							$SplotHours += $single['SOT'];
						}
					}	
	
					//$employee_working_hours=Get_Monthly_EmployeeWorkingHours($single_empl['empl_id'], $year, get_post('month'), true, $empl_attendance_ar);
					//$employee_Lop_hours = $total_working_hours[0] - $employee_working_hours[0];
					$ot_mul_factor = GetSingleValue('kv_empl_option', 'option_value', array('option_name'=>'ot_factor'));	
					$gross_4_LOP =  $ctc_cal =  $Reimbursement =0 ;
					$end_of_selected_month = end_month(sql2date($months_with_years_list[(int)get_post('month')])); 
					$begin_this_month = begin_month(sql2date($months_with_years_list[(int)get_post('month')])); 
					foreach($Allowance as $single) {
						if($single['al_type'] == 0 ){
							$_POST[$single_empl['empl_id'].$single['id']] = (isset($_POST[$single_empl['empl_id'].$single['id']]) ? $_POST[$single_empl['empl_id'].$single['id']] : 0 );

							if($single['type']=='Earnings'){
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = (isset($_POST[$single_empl['empl_id'].'_'.$single['id']]) ? $_POST[$single_empl['empl_id'].'_'.$single['id']] : 0 );
							}
							if($single['type']=='Reimbursement'){
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = (isset($_POST[$single_empl['empl_id'].'_'.$single['id']]) ? $_POST[$single_empl['empl_id'].'_'.$single['id']] : 0 );
							}
							if($single['type']=='Deductions'){
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = (isset($_POST[$single_empl['empl_id'].'_'.$single['id']]) ? $_POST[$single_empl['empl_id'].'_'.$single['id']] : 0 );
							}
							if($single['type']=='Employer Contribution'){ 							
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = (isset($_POST[$single_empl['empl_id'].'_'.$single['id']]) ? $_POST[$single_empl['empl_id'].'_'.$single['id']] : 0 );
							}	
						} elseif($single['al_type']==1){  // LMRA Calculation			
							$amount =GetRow('kv_empl_lmra_fees',  array('nationality' => $Empl_job_row['nationality'])); 
							if(!empty($amount))
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = $amount['amount'];
							elseif($amount2 =  GetSingleValue('kv_empl_lmra_fees', 'amount', array('nationality' => 0)))
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = $amount2;
							else
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = 0 ;
						}elseif($single['al_type']==3){  // Medical Allowance
							$medi_category= GetSingleValue('kv_empl_medical_premium', 'month', array('id' => $Empl_job_row['medi_category'])); 
							if($medi_category)
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = $medi_category;
							else
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = null;
						} elseif($single['al_type']==4){	// Visa and immgration
							$visa_details= GetRow('kv_empl_visa_exp', array('nationality' => $Empl_job_row['nationality'], 'family' => $Empl_job_row['family']));							
							if ($visa_details){  
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = $visa_details['month'];
							}else {
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = GetSingleValue('kv_empl_visa_exp', 'month', array('nationality' => 0, 'family' => $Empl_job_row['family']));
							}
						} elseif($single['al_type']==5){ //Leave Travel
							$leave_travel= GetRow('kv_empl_leave_travel', array('nationality' => $Empl_job_row['nationality'], 'family' => $Empl_job_row['family']));
							if ($leave_travel){  
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = $leave_travel['month'];
							}else {
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = GetSingleValue('kv_empl_leave_travel', 'month', array('nationality' => 0, 'family' => $Empl_job_row['family']));
							}
						} elseif($single['al_type']==6){   // Leave Salary 
							$ALDays = ($Empl_job_row['al']/12);
							
							$amt_for_cal = 0 ;
								foreach($Allowance as $singl) {									
									if( !empty($hrmsetup['leave_pay']) && in_array($singl['id'], $hrmsetup['leave_pay']))
										$amt_for_cal += $_POST[$single_empl['empl_id'].'_'.$singl['id']] ;
								}								
								if($amt_for_cal==0)
									$amt_for_cal = $gross_4_LOP;								
								
							$_POST[$single_empl['empl_id'].'_'.$single['id']] =((($amt_for_cal*12)/365)*$ALDays);
							if(date2sql($begin_this_month) < $Empl_job_row['joining']){
								$days = 30 - (int)date('d', strtotime($Empl_job_row['joining']));								
								$ALDays = ($ALDays/30)*$days;
								$_POST[$single_empl['empl_id'].'_'.$single['id']] =((($amt_for_cal*12)/365)*($ALDays));
							} 
							if(!isset($sal_row['net_pay']) && $Empl_info_row['status']>1 && $Empl_info_row['date_of_status_change'] > date2sql($begin_this_month) && $Empl_info_row['date_of_status_change'] < date2sql($end_of_selected_month) ) {
								$days = (int)date('d', strtotime($Empl_info_row['date_of_status_change']));								
								$ALDays = ($ALDays/30)*$days;
								$_POST[$single_empl['empl_id'].'_'.$single['id']] =((($amt_for_cal*12)/365)*($ALDays));
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
										$amt_for_cal += $_POST[$single_empl['empl_id'].'_'.$singl['id']] ;
								}								
								if($amt_for_cal==0)
									$amt_for_cal = $gross_4_LOP;
								$_POST[$single_empl['empl_id'].'_'.$single['id']] =((($amt_for_cal*12)/365)*$cal_days)/12;								
							} else {
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = 0;
							}
						} elseif($single['al_type'] == 2) { //GOSI Calculation	
							if((!isset($_POST[$single_empl['empl_id'].'_'.$single['id']]) || $_POST[$single_empl['empl_id'].'_'.$single['id']] == '' || get_post($single_empl['empl_id'].'_'.$single['id']) == 0)){ 							
								$gosi_emplee= GetRow('kv_empl_gosi_settings', array('nationality' => $Empl_job_row['nationality']));
								if(empty($gosi_emplee))
									$gosi_emplee =  GetRow('kv_empl_gosi_settings', array('nationality' => 0));
								$gosi_cal_amt = 0 ;
								$gosi_al = unserialize(base64_decode($gosi_emplee['allowances']));
								if(!empty($gosi_al)){									
									foreach($gosi_al as $singl){
										$gosi_cal_amt += input_num($single_empl['empl_id'].'_'.$singl);
									}
								} 
								if($gosi_cal_amt == 0 )
									$gosi_cal_amt = $gross_4_LOP; 
								
								if($single['type']=='Deductions' ){									
									if ($gosi_emplee){  							
										$_POST[$single_empl['empl_id'].'_'.$single['id']] = $gosi_cal_amt * ($gosi_emplee['employee']/100);
									}else {  	
										$_POST[$single_empl['empl_id'].'_'.$single['id']] = $gosi_cal_amt * ($gosi_emplee['employee']/100);
									}
								}	elseif( $single['type']=='Employer Contribution' ){									
									if ($gosi_emplee){  							
										$_POST[$single_empl['empl_id'].'_'.$single['id']] = $gosi_cal_amt * ($gosi_emplee['employer']/100);
									} else {  
										$_POST[$single_empl['empl_id'].'_'.$single['id']] = $gosi_cal_amt * ($gosi_emplee['employer']/100);
									}
								}
								//display_error($single['id'].'_'.$_POST[$single_empl['empl_id'].'_'.$single['id']]);
							} 
							
						}
						if($single['pf'] == 1){
							if($single['type'] == 'Earnings')
								$pf_amt_actual = $_POST[$single_empl['empl_id'].'_'.$single['id']];
							if($single['type'] == 'Reimbursement')
								$pf_amt_actual = $_POST[$single_empl['empl_id'].'_'.$single['id']];
							if($single['type'] == 'Employer Contribution')
								$pf_amt_actual = $_POST[$single_empl['empl_id'].'_'.$single['id']];
							if($single['type'] == 'Deductions')
								$pf_amt_actual = $_POST[$single_empl['empl_id'].'_'.$single['id']];
						}							
						
						if($single['type'] == 'Earnings' && $single['value'] != 'Payroll Input'){
							$gross_4_LOP += input_num($single_empl['empl_id'].'_'.$single['id']);
						}
						if($single['type'] == 'Reimbursement') { 
							$Reimbursement += input_num($single_empl['empl_id'].'_'.$single['id']);
						}
						if( $single['type'] == 'Employer Contribution' ){  
							$ctc_cal += input_num($single_empl['empl_id'].'_'.$single['id']);
						}	
						
						if($single['loan'] == 1 ) {
							$loans = GetDataJoinRow('kv_empl_loan_types AS type', array( 
									0 => array('join' => 'INNER', 'table_name' => 'kv_empl_loan AS loan', 'conditions' => '`type`.`id` = `loan`.`loan_type_id`'),
									1 => array('join' => 'INNER', 'table_name' => 'kv_empl_allowances AS info', 'conditions' => '`type`.`allowance_id` = `info`.`id`'), 
									//2 => array('join' => 'INNER', 'table_name' => 'kv_empl_job AS job', 'conditions' => '`job`.`empl_id` = `loan`.`empl_id`') 
								), 
							array('`loan`.`monthly_pay`, `loan`.`date`, `loan`.`periods_paid` , `loan`.`periods`, `loan`.`id`'), array('`loan`.`empl_id`' => $single_empl['empl_id'], '`info`.`id`' => $single['id'], '`loan`.`status`' => "Active"));
								
							if(!empty($loans) && $loans['periods_paid'] < $loans['periods'] && strtotime($loans['date']) <= strtotime($months_with_years_list[(int)get_post('month')]) ){
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = (isset($_POST[$single_empl['empl_id'].'_'.$single['id']]) ? $_POST[$single_empl['empl_id'].'_'.$single['id']] : 1); //$loans['monthly_pay'];	
								$_POST['loan_'.$single_empl['empl_id'].'_'.$single['id']] = $loans['id'];
								$_POST['loan_mnth_pay_'.$single_empl['empl_id'].'_'.$single['id']] = $loans['monthly_pay'];		
								hidden('loan_'.$single_empl['empl_id'].'_'.$single['id'], $_POST['loan_'.$single_empl['empl_id'].'_'.$single['id']]);
								hidden('loan_mnth_pay_'.$single_empl['empl_id'].'_'.$single['id'], $_POST['loan_mnth_pay_'.$single_empl['empl_id'].'_'.$single['id']]);
							}							
						}
						if($single['Tax'] == 1 )	
							$Tax_id = $single_empl['empl_id'].'_'.$single['id'];
						if($single['basic'] == 1)	
							$basic_allowance = input_num($single_empl['empl_id'].'_'.$single['id']);
					}			 
					
					$gross_emp_amt = $Empl_job_row['gross'];
					if($gross_4_LOP > 0 )
						$gross_amt = $gross_4_LOP;
					else
						$gross_amt = $gross_emp_amt; 
							
					$ctc_final = $ctc_cal + $gross_amt+ $Reimbursement;										
					
					$_POST[$single_empl['empl_id'].'adv_sal']= GetSingleValue('kv_empl_salary_advance', 'amount', array('empl_id' => $single_empl['empl_id'], 'month' => get_post('month'), 'year' => get_post('year')));
				/*	if(!isset($_POST['lop_amount'])){
						if($gross_4_LOP>0)
							$_POST[$single_empl['empl_id'].'lop_amount'] = 0; //round(($gross_amt/$total_working_hours[0])*$employee_Lop_hours);
						else
							$_POST[$single_empl['empl_id'].'lop_amount'] = 0;
					}*/
					if(!isset($_POST[$single_empl['empl_id'].'lop_amount']) || $_POST[$single_empl['empl_id'].'lop_amount'] == 0){
						if(isset($duration[0]['total_hrs']))									
							$_POST[$single_empl['empl_id'].'lop_amount'] = round((($gross_4_LOP/$duration[0]['total_hrs'])*$employee_Lop_hours), 2);
						else
							$_POST[$single_empl['empl_id'].'lop_amount'] = $gross_4_LOP;											
					}
					
					$basic_amount = ($basic_id ? $Empl_job_row[$basic_id] : 0);
					/*$ot_mul_factor =GetSingleValue('kv_empl_option', 'option_value', array('option_name'=>'ot_factor'));

					$OT_whours = floor($employee_working_hours[1] / 3600);
					$OT_wmins = floor($employee_working_hours[1] / 60 % 60);
					$OT_Fraction = $OT_whours+ round($OT_wmins / 60, 2);
					$ot_earnings = round(($ot_mul_factor * ($basic_amount/208))*$OT_Fraction);
					*/
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
					if(isset($basic_allowance)){	//SplitHours
						$ot_earnings = round((($hrmsetup['ot_factor']) * (($basic_allowance/$days_count)/(strtotime($EndTime)-strtotime($BeginTime)))*$otHours),2);
						$ot_earnings += round((($hrmsetup['special_ot_factor']) * (($basic_allowance/$days_count)/(strtotime($EndTime)-strtotime($BeginTime)))*$SplotHours),2);
					} else 
						$ot_earnings = 0;
					$_POST[$single_empl['empl_id'].'_'.'ot_earnings'] = round($ot_earnings);					
					$gross_4_grand = 0 ;
					foreach($Allowance as $single) {	
						if($single['type'] == 'Earnings' ){
							$gross_4_grand += input_num($single_empl['empl_id'].'_'.$single['id']);			
						}								
					}			
					$_POST[$single_empl['empl_id'].'ear_tot'] = $_POST[$single_empl['empl_id'].'_'.'ot_earnings']+$gross_4_grand;			
					if( isset($Tax_id)){			
						if(isset($_POST[$Tax_id]))
							$_POST[$Tax_id] = input_num($Tax_id);
						else
							$_POST[$Tax_id] = kv_get_tax_for_an_employee($single_empl['empl_id'], $year, $_POST[$single_empl['empl_id'].'ear_tot'], get_post('month'));		
					}

					if(isset($Empl_job_row['currency']) && $Empl_job_row['currency'] != ''){
						$curr_code =  $Empl_job_row['currency'];
						$ex_dat =  Today();
						$ex_rate = number_format(get_exchange_rate_from_home_currency($curr_code, $ex_dat), 4);
					} else{
						$curr_code = get_company_currency();
						$ex_rate = 1; 
					}

					hidden($empl_id.'currency', $curr_code);
					hidden($empl_id.'rate', $ex_rate);
					start_row();			
					label_cell($single_empl['empl_id']);
					label_cell(kv_get_empl_name($single_empl['empl_id']));
					label_cell(GetSingleValue('currencies', 'currency', array('curr_abrev' => $curr_code)));
					label_cell( number_format2($ex_rate, 4));
					for($vj = 0; $vj<count($All_Allowance);$vj++){
						if(isset($Allowance[$vj]['id']) && $All_Allowance[$vj]['id'] == $Allowance[$vj]['id']){
							//$All_Allowance[$vj] = $All_Allowance[$vj];
							$All_Allowance[$vj]['value'] = $Allowance[$vj]['value'];
						}
					}
					foreach($All_Allowance as $single) {							
						if($single['type'] == 'Earnings' ){
							if(!isset($_POST[$single_empl['empl_id'].'_'.$single['id']]))
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = 0;
							if($single['value'] == 'Payroll Input' && $single['basic'] != 1){	
								amount_cells_ex(null, $single_empl['empl_id'].'_'.$single['id'], null, 15);
							}else{
								label_cell(price_format($_POST[$single_empl['empl_id'].'_'.$single['id']]), ' style=" text-align:right;" ');								
							}
							hidden($single_empl['empl_id'].$single['id'], $_POST[$single_empl['empl_id'].'_'.$single['id']]);
						} 
					}  					
					
					label_cell(price_format($_POST[$single_empl['empl_id'].'_'.'ot_earnings']),' style=" text-align:right;" ');
					hidden($single_empl['empl_id'].'ot_earnings', $_POST[$single_empl['empl_id'].'_'.'ot_earnings'] );
					
					$reimburse = 0; 
					foreach($All_Allowance as $single) {							
						if($single['type'] == 'Reimbursement'){
							if(!isset($_POST[$single_empl['empl_id'].'_'.$single['id']]))
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = 0;
							if( $single['value'] == 'Payroll Input')
								amount_cells_ex(null, $single_empl['empl_id'].'_'.$single['id'], null, 15);
							else{
								label_cell(price_format($_POST[$single_empl['empl_id'].'_'.$single['id']]), ' style=" text-align:right;" ');								
							}	
							hidden($single_empl['empl_id'].$single['id'], $_POST[$single_empl['empl_id'].'_'.$single['id']]);	
							$reimburse += input_num($single_empl['empl_id'].'_'.$single['id']);							
						}
					}	
					
					$_POST[$single_empl['empl_id'].'payable_gross'] = $reimburse + $_POST[$single_empl['empl_id'].'ear_tot'];
					label_cell( price_format($_POST[$single_empl['empl_id'].'payable_gross']), ' style=" text-align:right;" ' );
					hidden($single_empl['empl_id'].'gross_salary', $_POST[$single_empl['empl_id'].'payable_gross'] );
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
					if($esic_gross_amount >= $_POST[$single_empl['empl_id'].'ear_tot'])	{
						$esic = true;
					}
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
					if($pf_amount >= $pf_amt_actual) {
						$pf = true;
					}
					$ctc_grand = get_post($single_empl['empl_id'].'payable_gross');
					
					foreach ($All_Allowance as $single) {						
						if( $single['type'] == 'Employer Contribution' ){  
							if(!isset($_POST[$single_empl['empl_id'].'_'.$single['id']]))
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = 0;
							if($single['esic'] != 1 && $single['pf'] != 1){
								if($single['value'] == 'Payroll Input')
									amount_cells_ex(null, $single_empl['empl_id'].'_'.$single['id'], null, 10, 10);
								else
									label_cell( price_format($_POST[$single_empl['empl_id'].'_'.$single['id']]), '', ' style=" text-align:right;" ');
								$ctc_grand += input_num($single_empl['empl_id'].'_'.$single['id']);
							}else{
								if(($single['pf'] == 1 && $pf == false) || ($single['esic'] == 1 && $esic ==false))
										$_POST[$single_empl['empl_id'].'_'.$single['id']] = 0 ;
									
								if($esic && $single['esic'] == 1){	
									if($single['value'] == 'Payroll Input')
										amount_cells_ex(null, $single_empl['empl_id'].'_'.$single['id'], null, 10, 10);
									else
										label_cell( price_format($_POST[$single_empl['empl_id'].'_'.$single['id']]), '', ' style=" text-align:right;" ');
								}elseif($pf && $single['pf'] == 1){	
									if($single['value'] == 'Payroll Input')
										amount_cells_ex(null, $single_empl['empl_id'].'_'.$single['id'], null, 10, 10);
									else
										label_cell( price_format($_POST[$single_empl['empl_id'].'_'.$single['id']]), '', ' style=" text-align:right;" ');
								} else{									
									label_cell( price_format($_POST[$single_empl['empl_id'].'_'.$single['id']]), '', ' style=" text-align:right;" ');
								}
								$ctc_grand += input_num($single_empl['empl_id'].'_'.$single['id']);
							}	
							hidden($single_empl['empl_id'].$single['id'], $_POST[$single_empl['empl_id'].'_'.$single['id']]);						
						}
					}		
					//display_error($ctc_final.'-'.$ctc_grand);					
					label_cell( price_format($ctc_grand), ' style=" text-align:right;" ' );
					hidden($single_empl['empl_id'].'ctc', $ctc_grand );					
					$deduct_tot = 0 ;		
					foreach($All_Allowance as $single) {
						if($single['type'] == 'Deductions'){
							if(!isset($_POST[$single_empl['empl_id'].'_'.$single['id']]))
								$_POST[$single_empl['empl_id'].'_'.$single['id']] = 0;
							if($single['esic'] != 1 && $single['pf'] != 1 ){
								if($single['value'] == 'Payroll Input' && $single['loan'] != 1){
									amount_cells_ex(null, $single_empl['empl_id'].'_'.$single['id'], null, 10, 10);
									$deduct_tot += $_POST[$single_empl['empl_id'].'_'.$single['id']];
								}elseif( $single['loan'] != 1){
									label_cell($_POST[$single_empl['empl_id'].'_'.$single['id']], '', ' style=" text-align:right;" ');
									$deduct_tot += $_POST[$single_empl['empl_id'].'_'.$single['id']];
								}else {
									kv_loan_balance_dropdown_cells(null, $single_empl['empl_id'].'_'.$single['id'], input_num('loan_'.$single_empl['empl_id'].'_'.$single['id']), false, true);
									if(isset($_POST['loan_mnth_pay_'.$single_empl['empl_id'].'_'.$single['id']]))
										$deduct_tot += $_POST[$single_empl['empl_id'].'_'.$single['id']]*$_POST['loan_mnth_pay_'.$single_empl['empl_id'].'_'.$single['id']];
									if(list_updated($single_empl['empl_id'].'_'.$single['id']))
										$Ajax->activate('sal_calculation');
								}								
							}else {
								if(($single['pf'] == 1 && $pf == false) || ($single['esic'] == 1 && $esic ==false))
										$_POST[$single_empl['empl_id'].'_'.$single['id']] = 0 ;
								if($single['esic'] == 1 && $esic){						
									label_cell(price_format($_POST[$single_empl['empl_id'].'_'.$single['id']]), '', ' style=" text-align:right;" ');
									$deduct_tot += $_POST[$single_empl['empl_id'].'_'.$single['id']];
								}elseif($single['pf'] == 1 && $pf){
									label_cell( price_format($_POST[$single_empl['empl_id'].'_'.$single['id']]), '', ' style=" text-align:right;" ');
									$deduct_tot += $_POST[$single_empl['empl_id'].'_'.$single['id']];
								}else{
									label_cell(price_format($_POST[$single_empl['empl_id'].'_'.$single['id']]), '', ' style=" text-align:right;" ');
									$deduct_tot += $_POST[$single_empl['empl_id'].'_'.$single['id']];
								}
							}
							hidden($single_empl['empl_id'].$single['id'], $_POST[$single_empl['empl_id'].'_'.$single['id']]);
						}
					}

					label_cell(price_format($_POST[$single_empl['empl_id'].'adv_sal']), ' style=" text-align:right;" ');
					hidden($single_empl['empl_id'].'adv_sal', $_POST[$single_empl['empl_id'].'adv_sal'] ); 	
					amount_cells(null, $single_empl['empl_id'].'lop_amount', $_POST[$single_empl['empl_id'].'lop_amount']);					
					$deduct_tot += input_num($single_empl['empl_id'].'lop_amount')+ $_POST[$single_empl['empl_id'].'adv_sal'];
					label_cell(price_format($deduct_tot), ' style=" text-align:right;" ' );
					hidden($single_empl['empl_id'].'net_deductions', $deduct_tot ); 
					$net_pay = $_POST[$single_empl['empl_id'].'payable_gross'] - $deduct_tot; 
					label_cell(price_format($net_pay), ' style=" text-align:right;" ');
					hidden($single_empl['empl_id'].'net_pay', $net_pay ); 
					$Total_gross += $_POST[$single_empl['empl_id'].'payable_gross'];
					$total_net += $net_pay;
					if ($dim >= 1){
						dimensions_list_cells(null, $single_empl['empl_id'].'dimension_id', null, true, " ", false, 1);
						if ($dim > 1)
							dimensions_list_cells(null, $single_empl['empl_id'].'dimension2_id', null, true, " ", false, 2);
					}
					if ($dim < 1)
						hidden($single_empl['empl_id'].'dimension_id', 0);
					if ($dim < 2)
						hidden($single_empl['empl_id'].'dimension2_id', 0);
					
					//hidden($single_empl['empl_id'].'allowed_ml',$duration[0]['ml']);
					//hidden($single_empl['empl_id'].'allowed_cl',$duration[0]['cl']);
					hidden($single_empl['empl_id'].'allowed_al',$duration[0]['al']);
					end_row();
				}
			}			
			hidden('empl_ids', implode("-", $empl_ids));
		div_end();			
			start_row();
			$Earnings_colum_count = get_allowances_count('Earnings',0, $grade);
			$Reim_colum_count = get_allowances_count('Reimbursement',0, $grade);
			$Deductions_colum_count = get_allowances_count('Deductions',0, $grade);
			$ctc_colum_count = get_allowances_count('Employer Contribution',0, $grade);
			$gross_colm_cnt = $Earnings_colum_count+$Reim_colum_count+4; 			
			if(($Deductions_colum_count+$ctc_colum_count+2) >= 4 ){
				$net_colm_cnt = $Deductions_colum_count+2+$ctc_colum_count;
			} else
				$net_colm_cnt = (($Deductions_colum_count+$ctc_colum_count) + (2 -  ($Deductions_colum_count+$ctc_colum_count)));
				echo " <td colspan='".$gross_colm_cnt."'> </td> <td><strong>Total Gross</strong></td><td align='right'><strong>".price_format($Total_gross)."</strong></td> ";
				echo "<td colspan='".$net_colm_cnt."' align='right'></td> <td colspan='2'><strong>Total Net Salary</strong></td> <td align='right'><strong>". price_format($total_net)."</strong></td>";
				submit_cells('RefreshPayroll', trans("Refresh"),'',trans("Show Results"), true);
			end_row();
		}
    end_table(1);

	if( $payroll_process_end < date('Y-m-d'))
		submit_center('pay_salary', trans("Process Payout"), true, trans("Payout to Employees"), 'default');
	else
		display_warning(trans("You can't Process Payroll of future!"));

	div_end(); 
	end_form(); 

	if(get_post('pay_salary')) {
	$Ajax->activate('sal_calculation');			
	$get_employees_list = explode("-", $_POST['empl_ids']);			

	$Allowance = kv_get_allowances(null, 0, get_post('grade'));
	begin_transaction();
	foreach($get_employees_list as $empl_id) {  

		$jobs_arr =  array('empl_id' => $empl_id,
							 'month' => $_POST['month'],
							 'year' => $_POST['year'],
							 'currency' => $_POST[$empl_id.'currency'],
							 'rate' => input_num($empl_id.'rate'),
							 'gross' => input_num($empl_id.'gross_salary'),	
							 'ctc' => input_num($empl_id.'ctc'),	
							 //'ML' => $_POST[$empl_id.'allowed_ml'],							 
							// 'CL' => $_POST[$empl_id.'allowed_cl'],							 
							 'AL' => $_POST[$empl_id.'allowed_al'],	
							 'dimension' => $_POST[$empl_id.'dimension_id'],
							 'dimension2' => $_POST[$empl_id.'dimension2_id'], 
							 'date' => array(Today(), 'date'), 
							 'adv_sal' => input_num($empl_id.'adv_sal'),
							 'net_pay' => input_num($empl_id.'net_pay'), 
							 'ot_earnings'=>input_num($empl_id.'ot_earnings'),
						 	 'lop_amount' => input_num($empl_id.'lop_amount'));
		$loan_id = array();
		$loan_id_amount = 0;
		foreach($Allowance as $single) {	
			
			if($single['loan'] == 1 && isset($_POST['loan_'.$empl_id.'_'.$single['id']])){
				$loan_id[] = array(input_num('loan_'.$empl_id.'_'.$single['id']), $_POST['loan_mnth_pay_'.$empl_id.'_'.$single['id']], input_num($empl_id.'_'.$single['id']));  
				$jobs_arr[$single['id']] = input_num($empl_id.'_'.$single['id'])*$_POST['loan_mnth_pay_'.$empl_id.'_'.$single['id']];
				$loan_id_amount += input_num($empl_id.'_'.$single['id'])*$_POST['loan_mnth_pay_'.$empl_id.'_'.$single['id']];
			}else{
				$jobs_arr[$single['id']]= (input_num($empl_id.$single['id']) ? input_num($empl_id.$single['id']) : 0);
			}
		}
		
		$jobs_arr['loans'] = base64_encode(serialize($loan_id));
		$pay_slip_id = Insert('kv_empl_salary', $jobs_arr);
		unset($jobs_arr);
	}
	commit_transaction();	
	meta_forward($path_to_root.'/modules/ExtendedHRM/inquires/payroll_history_inquiry.php', "grade=".get_post('grade')."&dept_id=".$_POST['dept_id'].'&month='.$_POST['month'].'&year='.$_POST['year'].'&Added=yes');
}

end_page(); ?>