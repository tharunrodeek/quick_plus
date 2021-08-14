<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_LOANFORM';
$path_to_root="../../..";
include_once($path_to_root . "/includes/session.inc");
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
 
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/date_functions.inc");

page(trans("Loan Application Form"));

kv_simple_page_mode(true);
$dec = user_price_dec();
check_db_has_employees(trans("There is no employee in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php' target='_blank'>Add And Manage Employees</a> to update it"));

check_db_has_Loan_types(trans("There is no Loan Type defined in the system. Please define some <a href='".$path_to_root."/modules/ExtendedHRM/manage/loan_type.php' target='_blank'>Loan Type</a> "));
if (isset($_GET['empl_id']))
	$_POST['empl_id'] = $_GET['empl_id'];
if(isset($_GET['Added'])){
	display_notification("New Loan Added for the Selected Employee");
}elseif(isset($_GET['Deleted']))
	display_notification(trans("The selected loan has been deleted Successfully"));
	
$empl_id = get_post('empl_id');

if (list_updated('empl_id')) {	
	$Ajax->activate('_page_body');	
}

function can_process() {
	
	if ($_POST['empl_id'] == ""){
		display_error(trans("There is no Employee selected."));
		set_focus('empl_id');
		return false;
	} 
	if ($_POST['loan_amount'] == ""  || !check_num('loan_amount', 0)){
		display_error(trans("Loan Amount is Empty or Not number"));
		set_focus('loan_amount');
		return false;
	} 
	if ($_POST['periods'] == ""|| !check_num('periods', 0)){
		display_error(trans("Term Period is Empty or not Number."));
		set_focus('periods');
		return false;
	} 
	if ($_POST['loan_type_id'] == ""){
		display_error(trans("Select Loan Type."));
		set_focus('loan_id');
		return false;
	} 
	if ($_POST['monthly_pay'] == "" || !check_num('monthly_pay', 0)){
		display_error(trans("Calculate the Monthly Pay and Save it."));
		set_focus('loan_id');
		return false;
	} 
	/*$form_date = date('Y-m-d', strtotime(date2sql($_POST['date'])));
	$today_date = date("Y-m-d");
	if($form_date < $today_date && GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'test_mode')) == 0){
		display_error(trans("Enter a Valid Date."));
		set_focus('date');
		return false;
	}*/
	if(db_empl_has_this_loan($_POST['loan_type_id'], $_POST['empl_id'])) {
		display_warning(trans("Selected Employee has a same Type Loan, He has to complete the current Loan due to get new one."));
		set_focus('date');
		return false;
	}
return true;	
}
if ($Mode=='ADD_ITEM' && can_process()) { 
	
	$loan_id = Insert('kv_empl_loan', array('empl_id' => $_POST['empl_id'], 'date' => array($_POST['date'], 'date'), 'rate' => $_POST['rate'], 'currency' => $_POST['currency'], 'loan_date' => array($_POST['loan_date'], 'date'), 'loan_amount' => input_num('loan_amount'), 'loan_type_id' => $_POST['loan_type_id'], 'periods' => $_POST['periods'], 'monthly_pay' => input_num('monthly_pay'), 'periods_paid' => 0, 'status' => 'Active'));
	//$loan_allowance = GetRow('kv_empl_allowances', array('loan' => '1'));
	$sql = "SELECT allow.* FROM ".TB_PREF."kv_empl_allowances AS allow LEFT JOIN ".TB_PREF."kv_empl_loan_types AS types ON types.allowance_id= allow.id WHERE types.id=".$_POST['loan_type_id'];
	$res = db_query($sql, "Can't get allowances for loan");
	if($row = db_fetch($res)){
		if($row['debit_code'])
			add_gl_trans(98, $loan_id, $_POST['loan_date'], $row['debit_code'], 0,0, 'Employee Loan Amount #'.$_POST['empl_id'].'-'. kv_get_empl_name($_POST['empl_id']), (input_num('loan_amount')*$_POST['rate']));
		if($row['credit_code'])
			add_gl_trans(98, $loan_id, $_POST['loan_date'], $row['credit_code'], 0,0, 'Employee Loan Amount #'.$_POST['empl_id'].'-'. kv_get_empl_name($_POST['empl_id']),-(input_num('loan_amount')*$_POST['rate']));
	add_audit_trail(98, $loan_id, $_POST['loan_date']);
	}
	//add_bank_trans(98, $loan_id, $_POST['bank_account'], $_POST['empl_id'], $_POST['date'],	-input_num('loan_amount'), null, null);
	meta_forward($_SERVER['PHP_SELF'], "Added=yes&empl_id=".$_POST['empl_id']);
}

if ($Mode=='UPDATE_ITEM' && can_process()){

	Update('kv_empl_loan', array('id' => $selected_id), array('empl_id' => $_POST['empl_id'], 'loan_type_id' => $_POST['loan_type_id'],
	     'loan_amount' => (input_num('loan_amount')*$_POST['rate']), 'date' => array($_POST['date'], 'date'), 'monthly_pay' => $_POST['monthly_pay']));
	$sql = "SELECT allow.* FROM ".TB_PREF."kv_empl_allowances AS allow LEFT JOIN ".TB_PREF."kv_empl_loan_types AS types ON types.allowance_id= allow.id WHERE types.id=".$_POST['loan_type_id'];
	$res = db_query($sql, "Can't get allowances for loan");
	if($row = db_fetch($res)){
		$memo_ = 'Employee Loan Amount #'.$_POST['empl_id'].'-'. kv_get_empl_name($_POST['empl_id']);
		if($row['debit_code'] != '')
			Update('gl_trans', array('type_no' => $selected_id, 'type' => 98, 'account' => $row['debit_code'] ), array(  'memo_' => $memo_,   'amount' => (input_num('loan_amount')*$_POST['rate']), 'tran_date' => array($_POST['loan_date'], 'date')));
			//add_gl_trans(98, $loan_id, $_POST['date'], $row['debit_code'], 0,0, 'Employee Loan Amount #'.$_POST['empl_id'].'-'. kv_get_empl_name($_POST['empl_id']), input_num('loan_amount'));
		if($row['credit_code'] != '')
			Update('gl_trans', array('type_no' => $selected_id, 'account' => $row['credit_code'], 'type' => 98 ), array(  'memo_' => $memo_,   'amount' => -(input_num('loan_amount')*$_POST['rate']), 'tran_date' => array($_POST['loan_date'], 'date')));
		//add_gl_trans(98, $loan_id, $_POST['date'], $row['credit_code'], 0,0, 'Employee Loan Amount #'.$_POST['empl_id'].'-'. kv_get_empl_name($_POST['empl_id']),-input_num('loan_amount'));
		add_audit_trail(98, $selected_id, $_POST['loan_date'], trans("Updated."));
	}
	
	//$debit_loan = GetSingleValue('kv_empl_allowances', 'debit_code', array('loan' => '1'));
	//if($debit_loan != '')
		//Update('gl_trans', array('type_no' => $selected_id, 'type' => 98 ), array( 'account' => $debit_loan, 'memo_' => 'Employee Loan Amount #'.$_POST['empl_id'].'-'. kv_get_empl_name($_POST['empl_id']),   'amount' => input_num('loan_amount'), 'tran_date' => array($_POST['date'], 'date')));
		//add_gl_trans(98, $selected_id, $_POST['date'], $debit_loan, 0,0, 'Employee Loan Amount #'.$_POST['empl_id'].'-'. kv_get_empl_name($_POST['empl_id']), input_num('loan_amount'));

	display_notification(trans("Selected Loan has been updated"));
	
}
div_start('details');

$action = $_SERVER['PHP_SELF'];

if ($page_nested)
	$action .= "?empl_id=".get_post('empl_id');
start_form(false, false, $action);

if (db_has_employees()) {
	if (!$page_nested){
		start_table(TABLESTYLE_NOBORDER);
		start_row();   
		employee_list_cells(trans("Select an Employee")." :", 'empl_id', null,	trans("Employee"), true, check_value('show_inactive'), false, false);
		end_row();
		end_table();
	} else
	br(2);
}
else{
	hidden('empl_id', get_post('empl_id'));
}

function PMT($i, $n, $p) {
	$i = $i/1200; 
	$p = -$p; 
	return $i * $p * pow((1 + $i), $n) / (1 - pow((1 + $i), $n));
}

//echo (number_format(PMT(3.56 , 36, 20000),2)); 

if(db_has_empl_loan($empl_id)){
		$loans = GetDataJoin('kv_empl_loan AS loan', array( 
					0 => array('join' => 'INNER', 'table_name' => 'kv_empl_loan_types AS type', 'conditions' => '`type`.`id` = `loan`.`loan_type_id`'),
					1 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `loan`.`empl_id`') ), 
							array('`info`.`empl_id`, `info`.`empl_firstname`, `type`.`loan_name`, `loan`.`loan_amount`, `loan`.`monthly_pay`, `loan`.`periods`, `loan`. `periods_paid`, `loan`. `start_date`, `loan`.`status`, `loan`.`id`, `loan`. `loan_date`'),
							array('`loan`.`empl_id`' => $empl_id));
			
			start_table(TABLESTYLE);
				echo  "<tr> <td class='tableheader'>" . trans("Loan Type") . "</td>
					<td class='tableheader'>" . trans("Loan Amount") . "</td>
					<td class='tableheader'>" . trans("Monthly Pay") . "</td>
					<td class='tableheader'>" . trans("Periods") . "</td>
					<td class='tableheader'>" . trans("Periods Paid") . "</td>
					<td class='tableheader'>" . trans("Loan Date") . "</td>
					<td class='tableheader'>" . trans("Start Date") . "</td>
					<td class='tableheader'>" . trans("End Date") . "</td>
					<td class='tableheader'>" . trans("Status") . "</td>";
				if (!$page_nested)
					echo " <td class='tableheader'> </td> <td class='tableheader'> </td>"; // <td class='tableheader'> </td>";
				echo " </tr>";

					foreach($loans as $loan_single) {
						$date_of_end = date('Y-m-d', strtotime("+".$loan_single[5]." months", strtotime($loan_single[7]))); 
						echo '<tr style="text-align:center"><td>'.$loan_single[2].'</td><td>'.number_format2($loan_single[3], $dec).'</td><td>'.number_format2($loan_single[4], $dec).'</td><td>'.$loan_single[5].'</td><td>'.$loan_single[6].'</td><td>'.sql2date($loan_single[10]).'</td><td>'.sql2date($loan_single[7]).'</td><td>'.sql2date($date_of_end).'</td><td>'.$loan_single[8].'</td>';
						if (!$page_nested){
							edit_button_cell("Edit".$loan_single[9], trans("Edit"));
							delete_button_cell("Delete".$loan_single[9], trans("Delete"));
						}
						end_row();
					}
			end_table(1);
	}
if (!$page_nested){
	if ($selected_id != -1){
		
		div_start('Recalculate');

	 	if ($Mode == 'Edit') {
			$myrow = GetRow('kv_empl_loan', array('id' => $selected_id));
			$_POST['empl_id'] = $myrow['empl_id'];
			$_POST['loan_type_id']  = $myrow["loan_type_id"];
			$_POST['loan_amount']  = number_format2($myrow["loan_amount"], $dec);
			$_POST['periods']  = $myrow["periods"];
			$_POST['periods_paid']  = $myrow["periods_paid"];
			$_POST['monthly_pay']  = number_format2($myrow["monthly_pay"], $dec);
			$_POST['date']  = sql2date($myrow["date"]);
			$Ajax->activate('Recalculate');
			
		//display_error(json_encode($myrow));
			start_table(TABLESTYLE2);
				hidden('selected_id', $selected_id);
				hidden('periods_paid', $_POST['periods_paid']);
				if (!$page_nested){
					employee_list_cells(trans("Select an Employee: "), 'empl_id', null,	trans("Select An Employee"), false, check_value('show_inactive'));
				} else {
					hidden('empl_id', $_POST['empl_id']);
					label_row(trans("Employee Name")." :", GetSingleValue('kv_empl_info', 'CONCAT(`empl_firstname`, `empl_lastname`) AS empl_name', array('empl_id' => $_POST['empl_id'])));
				}
				text_row_ex(trans("Loan Amount").':', 'loan_amount', 15);
				text_row_ex(trans("Periods").':', 'periods', 8);
				kv_loan_list_cells(trans("Loan type: "), 'loan_type_id', null,	trans("Select a Loan Type"), true);
				
				if($_POST['periods_paid'] == 0 )
					submit_row('Refreshloan', trans("Calculate"),'',trans("Show Results"), 'default');
				
				date_row(trans("Start Date") . ":", 'date');

				$interest =GetSingleValue('kv_empl_loan_types', 'interest_rate', array('id' => $_POST['loan_type_id']));
				
				if($interest > 0){
					$_POST['monthly_pay'] = number_format((float)PMT($interest, trim($_POST['periods']), trim(input_num('loan_amount'))), 2);
				} else {
					$_POST['monthly_pay'] = price_format(trim(input_num('loan_amount')) / trim($_POST['periods']));
				}
				
				text_row_ex(trans("Net Pay").':', 'monthly_pay', 20);
				$date_of_end = date('Y-m-d', strtotime("+".$_POST['periods']." months", strtotime(date2sql($_POST['date'])))); 
				label_row(trans("End Date"), sql2date($date_of_end));
			end_table(1);
		} elseif($Mode == 'Delete'){
			$get_selected = GetRow('kv_empl_loan', array('id' => $selected_id));
			if($get_selected['periods_paid'] == 0){
				Delete('kv_empl_loan', array('id' => $selected_id));
				Delete('gl_trans', array('type' => 98, 'type_no' => $selected_id));
				
				meta_forward($_SERVER['PHP_SELF'], "Deleted=yes&empl_id=".$_POST['empl_id']);
			}
			else
				display_warning(trans("Sorry, you can't delete this Loan, it has some repayments."));
		}
		div_end();	
	} else { 
		$_POST['empl_name'] = kv_get_empl_name($empl_id);
		start_outer_table(TABLESTYLE2);
		table_section(1);
		label_row(trans("Empl Id").':', $empl_id, 20);
			
		$expd_percentage_amt = GetSingleValue('kv_empl_job', 'expd_percentage_amt', array('empl_id'=>$empl_id))/100; 
		if(!$expd_percentage_amt)
			$expd_percentage_amt = GetSingleValue('kv_empl_option', 'option_value', array('option_name'=>'expd_percentage_amt'))/100; // Here i have to add the get_option function to reterive the settings details.

		$expected_netpay = get_employee_net_pay($empl_id);
		
		$Ongoing_loan_amt = GetSingleValue('kv_empl_loan', 'SUM(`monthly_pay`)', array('empl_id' => $empl_id, 'status' => 'Active'));
		$get_maximum_monthly_pmt = ($expected_netpay > 0 ? (($expected_netpay * $expd_percentage_amt) - $Ongoing_loan_amt) : 0 ); 
		
		label_row(trans("Max. Available:").':', number_format2($get_maximum_monthly_pmt, $dec), 20);
		hidden('get_maximum_monthly_pmt', $get_maximum_monthly_pmt); 

		table_section_title(trans(" "));
		$empl_currency = GetSingleValue('kv_empl_job', 'currency', array('empl_id'=>$empl_id));
		
		if(isset($empl_currency) && $empl_currency != ''){
			$curr_code =  $empl_currency;
			$ex_dat = (Today());
			$ex_rate = number_format(get_exchange_rate_from_home_currency($curr_code, $ex_dat), 4);
		} else{
			$curr_code = get_company_currency();
			$ex_rate = 1; 
		} 
		hidden('currency', $curr_code);
		hidden('rate', $ex_rate);

		label_row(trans("Currency:"), GetSingleValue('currencies', 'currency', array('curr_abrev' => $curr_code)));
		
		text_row_ex(trans("Loan Amount").':', 'loan_amount', 15);
		kv_empl_number_list_row(trans("Periods").':', 'periods', null, 1, 60, true);
		kv_loan_list_cells(trans("Loan type: "), 'loan_id', null,	trans("Select a Loan Type"), true);
			//kv_bank_accounts_list_row(trans("Bank Account:"), 'bank_account', null, false, trans("No Bank Account"));
		submit_row('Refreshloan', trans("Calculate"),'',trans("Show Results"), 'default');
		
		table_section(2);

		$expected_netpay = get_employee_net_pay($empl_id);
		label_row(trans("Employee Name").':', $_POST['empl_name'], 20);
		label_row(trans("Net Pay").':', number_format2($expected_netpay, 2), 20);
		hidden('net_pay', $expected_netpay); 

		table_section_title(trans(" "));
			label_row(trans("Exchange Rate").":", price_format($ex_rate));
			div_start('totals_tbl');
			if (($_POST['loan_id'] != "") && ($_POST['loan_id'] != ALL_TEXT)&& ($_POST['periods'] != "") && ($_POST['periods'] != ALL_TEXT) && ($_POST['loan_amount'] != "") && ($_POST['loan_amount'] != ALL_TEXT)){
				$interest =GetSingleValue('kv_empl_loan_types', 'interest_rate', array('id' => $_POST['loan_id'])); //get_loan_interest_rate($_POST['loan_id']);
				hidden('loan_type_id', $_POST['loan_id']); 
				if($interest > 0){
					$_POST['monthly_pay'] = number_format((float)PMT($interest, trim($_POST['periods']), trim(input_num('loan_amount'))), 2);
				} else {
					$_POST['monthly_pay'] = number_format(trim(input_num('loan_amount')) / trim($_POST['periods']), $dec);
				}
				
			}
			text_row_ex(trans("Monthly Payment").':', 'monthly_pay', 8);
			date_row(trans("Loan Date") . ":", 'loan_date');
			date_row(trans("Start Period") . ":", 'date');
			if($_POST['periods'] > 0 ) {
				$date_of_end = date('Y-m-d', strtotime("+".$_POST['periods']." months", strtotime(date2sql($_POST['date'])))); 
				label_row(trans("End Date"), sql2date($date_of_end));
			}
			div_end();
		end_outer_table(1);	
	}

	if($Mode != 'Delete'){
		if($selected_id != -1){
			if(!isset($_POST['periods_paid']) || $_POST['periods_paid'] == 0 )
				submit_add_or_update_center($selected_id == -1, '', 'both');
			else
				display_warning(trans("Sorry You can't edit after collecting a single repayment").$_POST['periods_paid']);
		} else {
			if(isset($_POST['get_maximum_monthly_pmt']) && ($_POST['get_maximum_monthly_pmt'] < $_POST['monthly_pay'])){
				display_warning(sprintf(trans("The Selected Employee's Maximum Monthly Installment(%s), You can't provide loan more than that. You better revise the periods of pay or change the loan amount."), input_num('get_maximum_monthly_pmt')));
			}else{
				//if(GetSingleValue('kv_empl_loan', 'id', array('loan_type_id' => $_POST['loan_id'], 'empl_id' => $_POST['empl_id'], 'status' => 'Active')) > 0) 
					//display_warning(trans("Sorry You can't create loan again before completing Active one."));
				//else
					submit_add_or_update_center($selected_id == -1, '', 'both');
			}
		}
	}
}
end_form();  
div_end();
end_page();
 
if(get_post('Refreshloan') || list_updated('periods') || list_updated('loan_id')){
	$Ajax->activate('_page_body');
}
?>
<style>
	table.tablestyle2 { width: auto; }
</style>