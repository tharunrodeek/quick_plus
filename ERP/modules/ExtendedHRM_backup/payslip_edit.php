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

page(trans("Edit PaySlip"), true);

check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));
 
check_db_has_salary_account(trans("There are no Salary Account defined in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/hrm_settings.php'>Settings</a> to update it."));

if(isset($_GET['Added'])){
	display_notification(' The Employee Payslip is added #' .$_GET['Added']);
}
if(user_theme() == 'Saaisaran'){ ?>
<style>
	@media (min-width: 900px){
		table {  width: auto !important; } 
	}
</style>

<?php }

$pay_row['empl_id'] = get_post('employee_id', '');
$month = get_post('month','');
$current_year =  get_current_fiscalyear();
$year = get_post('year',$current_year['id']);
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
if (isset($_GET['payslip_id'])){
	$_POST['id'] =  $payslip_id = $_POST['payslip_id'] = $_GET['payslip_id'];
	//$pay_row = GetRow('kv_empl_salary', array('id' => $payslip_id));	
}
if( get_post('RefreshInquiry') ) {
	$Ajax->activate('totals_tbl');
}
	
$_POST['ear_tot']=$_POST['deduct_tot']=$_POST['empl_dept']=$_POST['reimbursement']=$_POST['adv_sal']=$_POST['net_pay']=$ot_earnings=0;

$dim = get_company_pref('use_dimension');
div_start('totals_tbl');
start_form();
	if (db_has_employees()) {
		start_table(TABLESTYLE_NOBORDER);
		start_row();
			
		if ($dim >= 1){
			dimensions_list_cells(trans("Dimension")." 1", 'dimension_id', null, true, " ", false, 1);
			if ($dim > 1)
				dimensions_list_cells(trans("Dimension")." 2", 'dimension2_id', null, true, " ", false, 2);
		}
		if ($dim < 1)
			hidden('dimension_id', 0);
		if ($dim < 2)
			hidden('dimension2_id', 0);
		end_row();
		end_table();
		br();		
	} 	
	hidden('id', $_POST['id']);

if($_POST['id']){
	$pay_row = GetRow('kv_empl_salary', array('id' => $_POST['id']));
	$empl_job =GetRow('kv_empl_job',  array('empl_id' => $pay_row['empl_id']));
	hidden('grade_id', $empl_job['grade']);
	hidden('empl_id', $empl_job['empl_id']);
	$Allowance  =  kv_get_allowances(null, 0, $empl_job['grade']);
	$empl_info = GetRow('kv_empl_info', array('empl_id' => $pay_row['empl_id']));
	hidden('date', sql2date($pay_row['date']));
	foreach($Allowance as $single) {	
		if($single['type']=='Earnings' && !isset($_POST['E_'.$pay_row['empl_id'].$single['id']]) && $single['gross'] != 1){
			$_POST['E_'.$pay_row['empl_id'].$single['id']] = $pay_row[$single['id']];			
		}
		if($single['type']=='Reimbursement' && !isset($_POST['R_'.$pay_row['empl_id'].$single['id']])){
			$_POST['R_'.$pay_row['empl_id'].$single['id']] = $pay_row[$single['id']];
		}
		if($single['type']=='Deductions' && !isset($_POST['D_'.$pay_row['empl_id'].$single['id']])){
			$_POST['D_'.$pay_row['empl_id'].$single['id']] = $pay_row[$single['id']];
		}
		if($single['type']=='Employer Contribution' && !isset($_POST['CTC_'.$pay_row['empl_id'].$single['id']])){ 
			$_POST['CTC_'.$pay_row['empl_id'].$single['id']] = $pay_row[$single['id']];
		}					
	}
	$_POST['ot_earnings'] = (isset($_POST['ot_earnings']) ? $_POST['ot_earnings'] : $pay_row['ot_earnings']);
		
	start_outer_table(TABLESTYLE);
		table_section(1);
		label_row(trans("Employee No:"), $pay_row['empl_id']);
		label_row(trans("Employee Name:"), $empl_info['empl_firstname'].' '.$empl_info['empl_lastname']);
		label_row(trans("Department:"), GetSingleValue('kv_empl_departments', 'description', array('id' => $empl_job['department'])));
		label_row(trans("Designation:"), GetSingleValue('kv_empl_designation', 'description', array('id' => $empl_job['desig'])));
		$month_name = kv_month_name_by_id($pay_row['month']);
		label_row(trans("Month of Payment:"), $month_name);
		if(isset($pay_row['currency']) && $pay_row['currency'] != ''){
			$curr_code =  $pay_row['currency'];
			$ex_dat = (isset($pay_row['date']) ? sql2date($pay_row['date']) : Today());
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
				amount_row(trans($single['description']), 'E_'.$pay_row['empl_id'].$single['id'], null);
				$_POST['ear_tot'] += input_num('E_'.$pay_row['empl_id'].$single['id']);				
			} 
		}
		amount_row(trans("OT Earnings:"), 'ot_earnings', null);
		$_POST['ear_tot'] += input_num('ot_earnings');
		table_section_title(trans("Reimbursement"));
		
		foreach ($Allowance as $single) {
			if($single['type'] == 'Reimbursement' ){					
				amount_row(trans($single['description']), 'R_'.$pay_row['empl_id'].$single['id'], null);
				$_POST['reimbursement'] += input_num('R_'.$pay_row['empl_id'].$single['id']);				
			}
		}	
		$_POST['ear_tot'] += $_POST['reimbursement'];
		label_row(trans("Total Earning(Gross Salary):"), price_format($_POST['ear_tot']), 'style="color:#FF9800; background-color:#f9f2bb;"', 'style="color:#FF9800; background-color:#f9f2bb;text-align:right"');
		hidden('gross_for_cal', $_POST['ear_tot']);
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
			$esic_gross_amount=0;
		
		$esic = $pf = false; 
		
		if($esic_gross_amount >= $_POST['ear_tot'])	{
			$esic = true;		
		}else
			$esic = false;
		if($hrmsetup['enable_esic']) {
			$pf_sql = "SELECT amt_limit FROM ".TB_PREF."kv_empl_esic_pf WHERE date = (SELECT MAX(date) FROM ".TB_PREF."kv_empl_esic_pf WHERE allowance_id='pf' LIMIT 0, 1) "; 
			$pf_result = db_query($pf_sql, "Can't get esic amount");
			if($pf_row =  db_fetch($pf_result)){
				$pf_amount =  $pf_row[0];
			}else{
				display_warning(trans("No PF Limit set. Please set it under HRM-> ESIC PF Settngs"));
			}
		} else
			$pf_amount = 0;
		$ctc_grand = $_POST['ear_tot'];
		table_section_title(trans("Employer Contribution"));
		foreach ($Allowance as $single) {
			if( $single['type'] == 'Employer Contribution' ){  						
				amount_row(trans($single['description']), 'CTC_'.$pay_row['empl_id'].$single['id'], null);
				$ctc_grand += input_num('CTC_'.$pay_row['empl_id'].$single['id']);						
			}
		}				
		
		label_row(trans('CTC '), price_format($ctc_grand), 'style="color:#9C27B0; background-color:rgba(156, 39, 176, 0.23);"', 'style="color:#9C27B0; background-color:rgba(156, 39, 176, 0.23);text-align:right;"');
		table_section(2);
		hidden('ctc', $ctc_grand);
		label_row(trans("Date of Payment:"), date("d-F-Y", strtotime($pay_row['date'])));
		label_row(trans("Grade:"), GetSingleValue('kv_empl_grade', 'description', array('id' => GetSingleValue('kv_empl_job', 'grade', array('empl_id' => $pay_row['empl_id'])))));
				
	    label_row(trans("Exchange Rate").":", number_format2($ex_rate,4));
	    hidden('rate', $ex_rate);
		table_section_title(trans("Deduction"));
		$loans =  unserialize(base64_decode($pay_row['loans']));
		foreach($Allowance as $single) {
			if($single['type'] == 'Deductions' && $single['loan'] != 1){
				amount_row(trans($single['description']), 'D_'.$pay_row['empl_id'].$single['id'], null);
				$_POST['deduct_tot'] += input_num('D_'.$pay_row['empl_id'].$single['id']);				
			}elseif($single['loan'] == 1){
				/*foreach($loans as $loan){
					if($loan[0] == $single['id'])
						kv_loan_balance_dropdown_row($single['description'], 'D_'.$pay_row['empl_id'].$single['id'], $loan[0]);
				}*/
				label_row(trans($single['description']), price_format($pay_row[$single['id']]), '', ' style=" text-align:right;" ');
				$_POST['deduct_tot'] += input_num('D_'.$pay_row['empl_id'].$single['id']);
			}
		}
		//display_error(json_encode($loans));
		
		$_POST['lop_amount'] = ((isset($_POST['lop_amount']) && $_POST['lop_amount'] != 0) ? $_POST['lop_amount'] : $pay_row['lop_amount']);
		$_POST['adv_sal'] = (isset($_POST['adv_sal']) ? $_POST['adv_sal'] : $pay_row['adv_sal']);
		amount_row(trans("Absent Deduction:"), 'lop_amount', null);
		label_row(trans("Advance Salary:"), price_format($_POST['adv_sal']), '', ' style=" text-align:right;" ');
		hidden('adv_sal', $_POST['adv_sal']);
		//if(isset($pay_row['net_pay'])){
			submit_cells('RefreshInquiry', trans("Refresh"),'',trans('Show Results'), 'default');
		//}		
		table_section_title(trans(" "));
		
		$_POST['deduct_tot'] += $_POST['adv_sal']+input_num('lop_amount');
		
		label_row(trans("Total Deductions"), price_format($_POST['deduct_tot']), 'style="color:#f55; background-color:#fed;"', 'style="color:#f55; background-color:#fed;text-align:right;"');
		label_row(trans(" "), '');
		$_POST['net_pay_calculated'] = $_POST['ear_tot']- $_POST['deduct_tot'];
		$_POST['net_pay'] = ((isset($_POST['net_pay_calculated']) && $_POST['net_pay_calculated'] != 0) ? $_POST['net_pay_calculated'] : $pay_row['net_pay']);
		label_row(trans("Net Salary Payable:"), price_format($_POST['net_pay']), 'style="color:#107B0F; background-color:#B7DBC1;"', 'style="color:#107B0F; background-color:#B7DBC1;text-align:right;"');
		end_outer_table();
		br();
		submit_center('pay_salary', trans("Update Payout"), true, trans('Payout to Employees'), 'default');
				
	}
	end_form();
	div_end();

	if(get_post('pay_salary')) {
		$Ajax->activate('totals_tbl');
		begin_transaction();
		$jobs_arr =  array(	 'gross' => $_POST['ear_tot'],	
							 'dimension' => $_POST['dimension_id'],
							 'dimension2' => $_POST['dimension2_id'], 						 
							 'ctc' => (isset($_POST['ctc'])? $_POST['ctc']: 0 ),							 
							 'date' => array(Today(), 'date'), 
							 'adv_sal' => $_POST['adv_sal'],
							 'net_pay' =>  $_POST['net_pay'],							
							 'ot_earnings' =>  input_num('ot_earnings'),
						 	 'lop_amount' => input_num('lop_amount'));
			$Allowance = kv_get_allowances(null, 0, $_POST['grade_id']);
			
			foreach($Allowance as $single) {	
				if($single['type'] == 'Deductions' && $single['loan'] != 1)
					$jobs_arr[$single['id']] = input_num('D_'.$_POST['empl_id'].$single['id']);	
				if($single['type'] == 'Earnings')
					$jobs_arr[$single['id']] = input_num('E_'.$_POST['empl_id'].$single['id']);	
				if($single['type'] == 'Reimbursement')
					$jobs_arr[$single['id']] = input_num('R_'.$_POST['empl_id'].$single['id']);	
				if($single['type'] == 'Employer Contribution')
					$jobs_arr[$single['id']] = input_num('CTC_'.$_POST['empl_id'].$single['id']);	
						
			}
			$pay_slip_id = Update('kv_empl_salary', array('id' => $_POST['id']), $jobs_arr);	
		
		commit_transaction();
		display_notification(trans("Selected Employee Salary Edited, Close Window and refresh the Payroll Inquiry to check changes"));
		echo "<script>function kv_popup_close(){    window.close();  }	kv_popup_close(); </script>";
	}	

end_page(); ?>