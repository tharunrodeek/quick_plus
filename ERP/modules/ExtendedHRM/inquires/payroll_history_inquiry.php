<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_PAYROLL_INQ';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
$version_id = get_company_prefs('version_id');

$js = '';
if($version_id['version_id'] == '2.4.1' || $version_id['version_id'] == '2.4.2' || $version_id['version_id'] == '2.4.3' || $version_id['version_id'] == '2.4.4'){
	if (!@$_GET['popup']){
		if ($SysPrefs->use_popup_windows) 
			$js .= get_js_open_window(800, 700);	

		if (user_use_date_picker()) 
			$js .= get_js_date_picker();
	}
}else{
	if (!@$_GET['popup']){
		if ($use_popup_windows)
			$js .= get_js_open_window(800, 700);
		if ($use_date_picker)
			$js .= get_js_date_picker();
	}
}
 include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");

page(trans("Payroll Inquiry"), @$_REQUEST['popup'], false, "", $js);
 
check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));

if (isset($_GET['dept_id'])){
	$_POST['dept_id'] = $_GET['dept_id'];
}
if (isset($_GET['grade'])){
	$_POST['grade'] = $_GET['grade'];
}
if (isset($_GET['month'])){
	$_POST['month'] = $_GET['month'];
}
if (isset($_GET['year'])){
	$_POST['year'] = $_GET['year'];
}

if(isset($_GET['Added'])){
	display_notification(' The Employees Payroll Processed Successfully');
}

$dept_id = get_post('dept_id','');
$month = get_post('month','');
$current_year =  get_current_fiscalyear();
$year = get_post('year',$current_year['id']);
 
if(list_updated('month')) {
		$month = get_post('month');   
		$Ajax->activate('totals_tbl');
}

if(isset($_POST['UpdateGL'])){
	$salaries = array();
	foreach($_POST as $empls =>$val) {			
		if (isset($_POST[$empls]) && $_POST[$empls] == 1 && substr($empls,0,5) == 'Empl_')
			$salaries[] = substr($empls, 5);
	}
	//display_error(json_encode($employees));
	if(!empty($salaries)){
		$sql= "SELECT * FROM ".TB_PREF."kv_empl_allowances WHERE debit_code > 0 AND credit_code > 0 ";
		$res_al = db_query($sql, "Can't get allowances list");
		$allowances_list = array();
		if(db_num_rows($res_al)){
			while($row= db_fetch($res_al)){
				$allowances_list[$row['id']] = array('debit_code' => $row['debit_code'], 'credit_code' => $row['credit_code'], 'loan' => $row['loan']);
			}
		}
		//display_error(json_encode($allowances_list));
		//exit; 
		$sal_ids = implode(',', array_map('intval', $salaries));	
		$sql2 = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE id IN (".$sal_ids.")" ;
		$res2 = db_query($sql2, "Can't get salaries list");
		if(db_num_rows($res2)){
			$salary_account= GetSingleValue('kv_empl_option', 'option_value', array('option_name'=>'salary_account'));
			$paid_from_account= GetSingleValue('kv_empl_option', 'option_value', array('option_name'=>'paid_from_account'));			

			while($row2 = db_fetch_assoc($res2)){
				if($row2['id']){
					$empl_name = kv_get_empl_name($row2['empl_id']); 
					begin_transaction();
					if($salary_account)
						add_gl_trans(99, $row2['id'], $_POST['date_of_pay'], $salary_account, $row2['dimension'],$row2['dimension2'], 'Employee Salary #'.$row2['empl_id'].'-'. $empl_name, ($row2['net_pay']*$row2['rate']));
					if($paid_from_account)
						add_gl_trans(99, $row2['id'], $_POST['date_of_pay'], $paid_from_account, $row2['dimension'],$row2['dimension2'], 'Employee Salary #'.$row2['empl_id'].'-'. $empl_name, -($row2['net_pay']*$row2['rate']));
					if(isset($_POST['bank_accounts'])){
						add_bank_trans(99, $row2['id'], $_POST['bank_accounts'], $row2['empl_id'], $_POST['date_of_pay'],	-($row2['net_pay']*$row2['rate']), null, null);
						$BankGL = GetSingleValue('bank_accounts', 'account_code', array('id' => $_POST['bank_accounts']));
						if($BankGL && $paid_from_account) {
							add_gl_trans(99, $row2['id'], $_POST['date_of_pay'], $BankGL, $row2['dimension'],$row2['dimension2'], 'Employee Salary #'.$row2['empl_id'].'-'. $empl_name, -($row2['net_pay']*$row2['rate']));
							add_gl_trans(99, $row2['id'], $_POST['date_of_pay'], $paid_from_account, $row2['dimension'],$row2['dimension2'], 'Employee Salary #'.$row2['empl_id'].'-'. $empl_name, ($row2['net_pay']*$row2['rate']));
						}
					}					
					if(!empty($allowances_list)) {
						$vj= 1; 
						//display_error(json_encode($allowances_list));
						foreach($allowances_list as $id => $single) {	
							if($single['loan'] != 1){
								add_gl_trans(99, $row2['id'], $_POST['date_of_pay'], $single['debit_code'], $row2['dimension'],$row2['dimension2'], 'Employee Salary #'.$row2['empl_id'].'-'. $empl_name, ($row2[$id]*$row2['rate']));
								add_gl_trans(99, $row2['id'], $_POST['date_of_pay'], $single['credit_code'], $row2['dimension'],$row2['dimension2'], 'Employee Salary #'.$row2['empl_id'].'-'. $empl_name, -($row2[$id]*$row2['rate']));
								//display_error($vj++.'-'.$id.'-'.$row2[$id]*$row2['rate'].'+'.json_encode($row2));
							}
						}
						//cancel_transaction();
					}
					if(db_insert_id()){
						Update('kv_empl_salary', array('id' => $row2['id']), array('GL' => 1));
						$loan_ids = unserialize(base64_decode($row2['loans']));
						paid_empl_loan_month_payment($row2['empl_id'], $loan_ids, $_POST['date_of_pay']);
						display_notification(trans("Selected Salaries Posted to GL"));
					}	
					add_audit_trail(99, $row2['id'], $_POST['date_of_pay']);
					commit_transaction();

				}
			}
		}
	}
} elseif(isset($_POST['Delete'])) {
	$salaries = array();
	foreach($_POST as $empls =>$val) {			
		if (isset($_POST[$empls]) && $_POST[$empls] == 1 && substr($empls,0,5) == 'Empl_')
			$salaries[] = substr($empls, 5);
	}
	//display_error(json_encode($employees));
	if(!empty($salaries)){
		foreach($salaries as $salid)
			Delete('kv_empl_salary', array('id' => $salid, 'GL' => 0));
	}
	display_notification(trans("Selected Payroll Entries Deleted Successfully"));
}elseif(isset($_POST['Void'])){
	//Void_Payroll($_POST['Void']);
	display_notification(trans(" Void Payroll clicked"));
}
start_form(true);

if (db_has_employees()) {
	start_table(TABLESTYLE_NOBORDER);
	start_row();	
		kv_fiscalyears_list_cells(trans("Fiscal Year:"), 'year', null, true);
		kv_current_fiscal_months_list_cell("Months", "month", null, true,false, 1);
		kv_empl_grade_list_cells( trans("Grade :"), 'grade', null, false, true);
		end_row();
	start_row();
		department_list_cells(trans("Select a Department")." :", 'dept_id', null,	trans("All Department"), true, check_value('show_inactive'));
		employee_list_cells(trans("Select an Employee"). " :", 'empl_id', null,	trans("Select an Employee"), true, check_value('show_inactive'), false, false,true);
	end_row();	
	end_table();

	if (get_post('_show_inactive_update') || get_post('month') || get_post('year') || get_post('empl_id') || list_updated('dept_id')){
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
	if(get_post('empl_id') > 0 ) {
		$get_employees_list = array(get_post('empl_id'));
		$grade = GetSingleValue('kv_empl_job', 'grade', array('empl_id' => get_post('empl_id')));
	} else {
		$grade = get_post('grade') ; 
		$get_employees_list = get_empl_ids_from_dept_id($dept_id, $grade);
	}

	start_table(TABLESTYLE_NOBORDER, "width=40%");
	label_row("**".trans("Here, you can view the Processed Salaries."), '', ' style="text-align:center;" ' );
	end_table();
   
	start_table(TABLESTYLE, "width=90%");
    $th = array(trans("Empl Id"),trans("Employee Name"), trans("Currrency"), trans("Exchange Rate") );

    $Allowance = kv_get_allowances(null, 0, $grade);
	foreach($Allowance as $single) {	
		if($single['type'] == 'Earnings')
			$th[] = array($single['description'] , '#f9f2bb', '#FF9800');
	}
	$th[] = array(trans("OT"), '#f9f2bb', '#FF9800');
	$th[] = array(trans("Other Allowance"), '#f9f2bb', '#FF9800');
	foreach($Allowance as $single) {	
		if($single['type'] == 'Reimbursement')
			$th[] = array($single['description'] , '#f9f2bb', '#FF9800');
	}
	$th[] = array(trans("Gross Pay"), '#f9f2bb', '#FF9800');
	foreach($Allowance as $single) {	
		if($single['type'] == 'Employer Contribution')
			$th[] = array($single['description'] , 'rgba(156, 39, 176, 0.23)', '#9C27B0');
	}
	$th[] = array(trans("CTC"), 'rgba(156, 39, 176, 0.23)', '#9C27B0');
	foreach($Allowance as $single) {	
		if($single['type'] == 'Deductions')
			$th[] = array($single['description'] , '#fed', '#f55');
	}
   	$th1 = array(/*array(trans("Adv Salary") , '#fed', '#f55'), /*array(trans("Loan") , '#fed', '#f55'),*/array(trans("Absent Days"), '#fed', '#f55'), array(trans("Absent Deduction") , '#fed', '#f55'), array(trans("Misc.") , '#fed', '#f55'), array(trans("Total Deduction") , '#fed', '#f55'),array(trans("Net Salary"), '#B7DBC1' ,  '#107B0F'), trans(" "), trans(" "), trans(" "), (" "), trans("GL").'<br>'.checkbox(null, 'CheckAll', null, true));
   	$th_final = array_merge($th, $th1);
   	if(list_updated('CheckAll') ){
   		if(get_post('CheckAll') == 1)
			$checkAll = 1;
		else
   			$checkAll = 0;
   		$Ajax->activate('sal_calculation');
   	}else
   		$checkAll = null;
	//table_header($th_final);	
	echo '<tr>';	
	foreach($th_final as $header){
		if(is_array($header)){
			echo '<td style="background:'.$header[1].';color:'.$header[2].'"> '.$header[0].'</td>';
		} else {
			echo '<td class="tableheader"> '.$header.'</td>';
		}
	} echo '</tr>';
	$total_working_hours =  Get_Monthly_WorkingHours($year, get_post('month'), true);
	$ipt_error = 0;

		/*if($hrm_year_list[$year]<= date('Y')){}
		else {
			display_error(trans("The Selected Year Yet to Born!"));
			$ipt_error = 1;
		}*/
		
		$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
		if($months_with_years_list[(int)get_post('month')] > date('Y-m-d')){
			display_error(trans("The Selected Month Yet to Born!"));
			$ipt_error = 1;
		}
		if($ipt_error ==0) {			
			
			$Total_gross = $total_net = 0; 
			foreach($get_employees_list as $single_empl) { 
				//display_error(json_encode($single_empl));
				$data_for_empl = GetRow('kv_empl_salary', array('empl_id' => $single_empl, 'month' => $month, 'year' => $year, 'net_pay' => array('-1', '<>')));
				if($data_for_empl) {
					start_row();
					$employee_leave_record = get_empl_attendance_for_month($data_for_empl['empl_id'], $month, $year);
					label_cell($data_for_empl['empl_id']);
					label_cell(kv_get_empl_name($data_for_empl['empl_id']));
					label_cell(GetSingleValue('currencies', 'currency', array('curr_abrev' => $data_for_empl['currency'])));
					label_cell(number_format2($data_for_empl['rate'], 4));
					foreach($Allowance as $single) {	
						if($single['type'] == 'Earnings')
							label_cell(price_format($data_for_empl[$single['id']]), ' style=" text-align:right;" ');
					}

					label_cell(price_format($data_for_empl['ot_earnings']), ' style=" text-align:right;" ');
					label_cell(price_format($data_for_empl['ot_other_allowance']), ' style=" text-align:right;" ');
					foreach($Allowance as $single) {	
						if($single['type'] == 'Reimbursement')
							label_cell(price_format($data_for_empl[$single['id']]), ' style=" text-align:right;" ');
					}
					label_cell(price_format($data_for_empl['gross']), ' style=" text-align:right;" ');
					$ctc = 0 ;
					foreach($Allowance as $single) {	
						if($single['type'] == 'Employer Contribution'){
							label_cell(price_format($data_for_empl[$single['id']]), ' style="text-align: right;" ' );
							$ctc += $data_for_empl[$single['id']];
						}
					}
					label_cell(price_format(($data_for_empl['gross']+$ctc)), ' style=" text-align:right;" ');
					$total_deduct = $data_for_empl['misc']+$data_for_empl['lop_amount']; 
					foreach($Allowance as $single) {	
						if($single['type'] == 'Deductions'){
							label_cell(price_format($data_for_empl[$single['id']]), ' style=" text-align:right;" ');
							$total_deduct += $data_for_empl[$single['id']];
						}
					}
					$employee_working_hours=Get_Monthly_EmployeeWorkingHours($data_for_empl['empl_id'], $year, get_post('month'), true);
					$employee_Lop_hours = $total_working_hours[0] - $employee_working_hours[0];

					$empl_hours = floor($employee_Lop_hours / 3600);
					$empl_mins = floor($employee_Lop_hours / 60 % 60);
					$payable_hours  = $empl_hours.':'.$empl_mins;

					label_cell($total_working_hours[1] - $employee_working_hours[2].'('.$payable_hours.')');
					label_cell(price_format($data_for_empl['lop_amount']), ' style=" text-align:right;" ');
					label_cell(price_format($data_for_empl['misc']), ' style=" text-align:right;" ');					
					label_cell(price_format($total_deduct), ' style=" text-align:right;" ');
					label_cell(price_format($data_for_empl['net_pay']), ' style=" text-align:right;" ');

					$Total_gross += $data_for_empl['gross'];
					$total_net += $data_for_empl['net_pay'];

					label_cell('<a href="'.$path_to_root.'/modules/ExtendedHRM/payslip.php?employee_id='.$data_for_empl['empl_id'].'&month='.$month.'&year='.$year.'&popup=yes" onclick="javascript:openWindow(this.href,this.target); return false;"  target="_blank" > <img src="'.$path_to_root.'/themes/default/images/gl.png" width="12" height="12" border="0" title="GL"></a>', 'style="min-width:20px;"');
					label_cell('<a onclick="javascript:openWindow(this.href,this.target); return false;" href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep802.php?PARAM_0='.$year.'&PARAM_1='.$month.'&PARAM_2='.$data_for_empl["empl_id"].'&rep_v=yes" target="_blank" class="printlink"> <img src="'.$path_to_root.'/themes/default/images/print.png" width="12" height="12" border="0" title="Print"> </a>', 'style="min-width:20px;"');
					label_cell('<a onclick="javascript:openWindow(this.href,this.target); return false;" href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep802.php?PARAM_0='.$year.'&PARAM_1='.$month.'&PARAM_2='.$data_for_empl["empl_id"].'&rep_v=yes&email=yes" class="printlink"> <img src="'.$path_to_root.'/modules/ExtendedHRM/images/email-icon.png" width="20" height="20" border="0" title="Print"> </a>', 'style="min-width:20px;"');
					if(isset($data_for_empl['GL']) && $data_for_empl['GL'] == 1){
						label_cell(''); //button('Void', $data_for_empl['id'], true, '', 'default'));
						label_cell('');
					} else{
						label_cell('<a onclick="javascript:openWindow(this.href,this.target); return false;"  href="'.$path_to_root.'/modules/ExtendedHRM/payslip_edit.php?payslip_id='.$data_for_empl['id'].'" ><img src="'.$path_to_root.'/themes/default/images/edit.gif" style="vertical-align:middle;width:12px;height:12px;border:0;"></a>', 'style="min-width:20px;"');
						check_cells(null, 'Empl_'.$data_for_empl['id'], $checkAll);
					}			
					 
					end_row();
				}
			}
			start_row();
			$Earnings_colum_count = get_allowances_count('Earnings',0, $grade);
			$Reim_colum_count = get_allowances_count('Reimbursement',0, $grade);
			$Deductions_colum_count = get_allowances_count('Deductions',0, $grade);
			$ctc_colum_count = get_allowances_count('Employer Contribution',0, $grade);
			$gross_colm_cnt = $Earnings_colum_count+$Reim_colum_count+4; 			
			if(($Deductions_colum_count+$ctc_colum_count+3) >= 5 ){
				$net_colm_cnt = $Deductions_colum_count+3+$ctc_colum_count;
			} else
				$net_colm_cnt = (($Deductions_colum_count+$ctc_colum_count) + (3 - ($Deductions_colum_count+$ctc_colum_count)));
			echo "<td colspan='".$gross_colm_cnt."'> </td> <td colspan='2'><strong>".trans("Total Gross")."</strong></td><td align='right'><strong>".price_format($Total_gross)."</strong></td> ";
			echo "<td colspan='".$net_colm_cnt."' align='right'></td> <td colspan='2'><strong>".trans("Total Net Salary")."</strong></td> <td align='right'><strong>". price_format($total_net)."</strong></td><td colspan='5'> </td>  ";
			end_row();
		}
    end_table(1);
    start_table(TABLESTYLE_NOBORDER, 'Width="30%"');
    date_row(trans("GL Entry Date"), 'date_of_pay');
    kv_bank_accounts_list_row(trans("Bank Accounts"), 'bank_accounts', null, false, trans("No Bank Account"));
    end_table();
    submit_center_first('UpdateGL', trans("Post to GL"), true,  'default');
    submit_center_last('Delete', trans("Delete"), true, 'cancel');
	div_end(); 
if (!@$_GET['popup']){
	end_form();
	end_page(@$_GET['popup'], false, false);
} 
?>
