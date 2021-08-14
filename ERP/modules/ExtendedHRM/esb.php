<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Enhanced HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'HR_PAYSLIP';
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
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

page(trans("Leaving Indemnity"), @$_REQUEST['popup'], false, "", $js);
if (isset($_GET['employee_id'])){
	$_POST['employee_id'] = $_GET['employee_id'];
}
if(isset($_GET['added']))
	display_notification(trans("The Employee End of Service Benefit generated Successfully!"));
$employee_id = get_post('employee_id','');
 
if(isset($_POST['ProcessESB'])){
	Insert('kv_empl_esb', array('empl_id' => $_POST['empl_id'], 'last_gross' => $_POST['last_gross'], 'days_worked' => $_POST['days_worked'], 'date' => array(Today(), 'date'), 'status' => $_POST['status'], 'amount' => $_POST['amount'], 'loan_amount' => $_POST['loan_amount']));
	meta_forward($path_to_root.'/modules/ExtendedHRM/esb.php?', 'employee_id='.$_POST['empl_id'].'&added=yes');
} 
if(list_updated('employee_id')) {
	$Ajax->activate('ESB');
}
div_start('ESB');

start_form();
		if (db_has_employees()) {
			start_table(TABLESTYLE_NOBORDER);
			start_row();
			department_list_cells(trans("Select a Department")." :", 'dept_id', null, trans("No Department"), true, check_value('show_inactive'));
			employee_list_cells(trans("Select an Employee:"), 'employee_id', null,trans("Select An Employee"), true, check_value('show_inactive'), false, false, false, true);
			end_row();	
			end_table();
			br();
			if (get_post('_show_inactive_update')) {
				$Ajax->activate('employee_id');
				set_focus('employee_id');
			}
		} 
		else {	
			hidden('employee_id');
		}
		
		if($employee_id){ 
			$paid_esb = GetRow('kv_empl_esb', array('empl_id' => $employee_id));
			global $hrm_empl_status;
			$status = GetSingleValue('kv_empl_info', 'status', array('empl_id' => $employee_id));  
			$status_text  = $hrm_empl_status[$status];
			$n_period = GetSingleValue('kv_empl_info', 'n_period', array('empl_id' => $employee_id)); 
			$date_of_status_change = GetSingleValue('kv_empl_info', 'date_of_status_change', array('empl_id' => $employee_id)); 
			$sql = "SELECT TIMESTAMPDIFF( YEAR, joining, info.date_of_status_change ) as year, TIMESTAMPDIFF( MONTH, joining, info.date_of_status_change ) % 12 as month, FLOOR( TIMESTAMPDIFF( DAY, joining, info.date_of_status_change ) % 30.4375 ) as day FROM  ".TB_PREF."kv_empl_job AS job, ".TB_PREF."kv_empl_info AS info  WHERE  job.empl_id=info.empl_id AND job.empl_id=".$employee_id; 
			$result_3 = db_query($sql, "Can't get result");
			if($empl_service = db_fetch($result_3)){
					$years = $empl_service['year'];
					$month = $empl_service['month'];
					$days = $empl_service['day'];
					$yearsonly =  $years + ($empl_service['month']/12) + ($empl_service['day']/365);
			}
			$last_payroll_date=GetSingleValue('kv_empl_salary', 'MAX(`date`)', array('empl_id' => $employee_id),null,array('empl_id')); 
			start_outer_table(TABLESTYLE);
					table_section(1);
					label_row(trans("Employee No:"), $employee_id, null, 30, 30);
					label_row(trans("Employee Name:"), kv_get_empl_name($employee_id), null, 30, 30);
					label_row(trans("Service Period:"), $years .' Years and '.$month.' Months and '.$days.' Days');
					label_row(trans("Employee Status"), $status_text);
					label_row(trans("Actual termination date"), sql2date($date_of_status_change));
					label_row(trans("Notice Period"),  sql2date($n_period));
					label_row(trans("Last payroll process date"),  sql2date($last_payroll_date));

					
			if(empty($paid_esb)){			
				$esb_country = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'esb_country'));
				$esb_salary = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'esb_salary'));
				$basic_id = kv_get_basic();
				//display_error($esb_salary);
				if($esb_salary && $basic_id){								
					$last_paid_salary =  GetSingleValue('kv_empl_salary', "`".$basic_id."`", array('empl_id' => $employee_id), array('id' => 'DESC')); 
				} else {
					$last_paid_salary =  GetSingleValue('kv_empl_salary', 'gross', array('empl_id' => $employee_id), array('id' => 'DESC')); 
				}
				
				$sql_2 = "SELECT TIMESTAMPDIFF( DAY, joining, info.date_of_status_change ) FROM  ".TB_PREF."kv_empl_job AS job, ".TB_PREF."kv_empl_info AS info  WHERE job.empl_id=info.empl_id AND job.empl_id=".$employee_id; 
				$result_2 = db_query($sql_2, "Can't get result");
				if($empl_esb = db_fetch($result_2))
					$DaysOnly =  $empl_esb[0]; 
				else
					$DaysOnly = 0;
				if($esb_salary)
					label_row(trans("Last Paid Basic Salary"), price_format($last_paid_salary));
				else
					label_row(trans("Last Paid Gross"), price_format($last_paid_salary));
				if($esb_country){
					if($status_text == 'Resigned' || $status_text == 'Terminated' || $status_text == 'Decesed'){						
						$firstPeriod = $secondPeriod = 0; // set periods
						if ($yearsonly > 3) {
							$firstPeriod = 3;
							$secondPeriod = $yearsonly - 3;
						}   else {
							$firstPeriod = $yearsonly;
						}
						// calculate
						$result = ($firstPeriod * $last_paid_salary * 0.5) + ($secondPeriod * $last_paid_salary);
					}
				} else {
					if($status_text == 'Resigned'){	
						if ($yearsonly < 2) {
							 $result = trans("Not eligible end of service benefits");
						}  else if ($yearsonly <= 5) {
							 $result = (1 / 6) * $last_paid_salary * $yearsonly;
						} else if ($yearsonly <= 10) {
							 $result = ((1 / 3) * $last_paid_salary * 5) + ((2 / 3) * $last_paid_salary * ($yearsonly - 5));
						} else {
							 $result = (0.5 * $last_paid_salary * 5) + ($last_paid_salary * ($yearsonly - 5));
						}   	
					} elseif($status_text == 'Terminated' || $status_text == 'Decesed'){						
						$firstPeriod = $secondPeriod = 0; // set periods
						if ($yearsonly > 5) {
							$firstPeriod = 5;
							$secondPeriod = $yearsonly - 5;
						} else 
							 $firstPeriod = $yearsonly;
							 
						$result = ($firstPeriod * $last_paid_salary * 0.5) + ($secondPeriod * $last_paid_salary); // calculate
					} else
						$result = 0;
				}
				
				$loan_amt = get_employee_balance_loan($employee_id);

				if(is_numeric($result)){					
					label_row(trans("Loan Balance"), price_format($loan_amt));
					label_row(trans("Calculated EOSB"), price_format($result));
					label_row(trans("Payable EOSB After Loan Deduction"), price_format($result-$loan_amt));
					hidden('amount', $result);	
					hidden('loan_amount', $loan_amt);	
				}
				else{
					label_row(trans("Loan Balance"), price_format($loan_amt));
					label_row(trans("ESB"), $result, ' style="border: 1px solid #ff9900;background-color: #ffff00; color: #ff5500;" ', ' style="border: 1px solid #ff9900;background-color: #ffff00; color: #ff5500; " ');
					hidden('amount', '-1');	
					hidden('loan_amount', $loan_amt);
				}
				
				hidden('empl_id', $employee_id);			
				hidden('last_gross', $last_paid_salary);			
				hidden('status', $status);		
				hidden('days_worked', $DaysOnly);	
				hidden('date', date('Y-m-d'));	

				end_outer_table();
				br();
				submit_center('ProcessESB', trans("Process ESB"));
			}else {
				label_row(trans("Last Paid Gross"), price_format($paid_esb['last_gross']));
				label_row(trans("Loan Balance"), price_format($paid_esb['loan_amount']));
				label_row(trans("Payable ESB :"), price_format($paid_esb['amount']-$paid_esb['loan_amount']));
				end_outer_table();
			}
		}
	end_form();
	div_end();
end_page();?>
<style>
	table { width: auto; }
</style>