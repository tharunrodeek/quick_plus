<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_LOAN_INQ';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
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
include_once($path_to_root . "/includes/date_functions.inc");
page(trans("Loan Approve Inquiry"), false, false, "", $js);
kv_simple_page_mode(true);

$dec = user_price_dec();
check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));

//---------------------------------------------------------------------------------------------------
$input_error = 0;



//---------------------------------------------------------------------------------------------------
$action = $_SERVER['PHP_SELF'];
if ($page_nested)
	$action .= "?empl_id=".get_post('empl_id');
start_form(false, false, $action);


if (!$page_nested)
{
	start_table(TABLESTYLE_NOBORDER);
			start_row();
			 	department_list_cells(trans("Select a Department")." :", 'dept_id', null,	trans('No Department'), true, check_value('show_inactive'));
				employee_list_cells(trans("Select an Employee"). " :", 'empl_id', null,	trans("Select an Employee"), true, check_value('show_inactive'), false, false,true);
				$new_item = get_post('dept_id')=='';
		 	end_row();
	 	end_table();
	echo "<hr></center>";
}
else
	br(2);


//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
if ($Mode=='UPDATE_ITEM' ){
	begin_transaction();
	if(isset($_POST['repayment'])  && $_POST['repayment'] == 'yes') {
			//$_POST['credit_code'] $_POST['debit_code'] $_POST['monthly_pay']
			$paid_now  = $_POST['periods_paid']+$_POST['period_to']  ;		
			$arra = array( 'periods_paid' => $paid_now); 
			$amount = $_POST['period_to'] * (input_num('monthly_pay')*$_POST['rate']);
			if($_POST['periods'] == $paid_now)
				$arra['status'] = 'Inactive';
		Update('kv_empl_loan', array('id' => $selected_id), $arra);
		$memo = 'Employee Loan Repayment #'.$_POST['employee_id'].'-'. kv_get_empl_name($_POST['employee_id']);
		if($_POST['debit_code'])
			add_gl_trans(98, $selected_id, $_POST['date'], $_POST['debit_code'], 0,0, $memo, -$amount);
		if($_POST['credit_code'])
			add_gl_trans(98, $selected_id, $_POST['date'], $_POST['credit_code'], 0,0, $memo, $amount);
		add_audit_trail(98, $selected_id, $_POST['date']);
		display_notification(trans('Selected Employee Repayment has been updated'));
		
		/*if($row['debit_code'] != '')
				Update('gl_trans', array('type_no' => $selected_id, 'type' => 98, 'account' => $row['debit_code'] ), array(  'memo_' => $memo_,   'amount' => -input_num('loan_amount'), 'tran_date' => array($_POST['date'], 'date')));
				//add_gl_trans(98, $loan_id, $_POST['date'], $row['debit_code'], 0,0, 'Employee Loan Amount #'.$_POST['empl_id'].'-'. kv_get_empl_name($_POST['empl_id']), input_num('loan_amount'));
			if($row['credit_code'] != '')
				Update('gl_trans', array('type_no' => $selected_id, 'account' => $row['credit_code'], 'type' => 98 ), array(  'memo_' => $memo_,   'amount' => input_num('loan_amount'), 'tran_date' => array($_POST['date'], 'date')));
		*/
	}  elseif(can_process() ) { 
		$interest =GetSingleValue('kv_empl_loan_types', 'interest_rate', array('id' => $_POST['loan_type_id']));
				
		if($interest > 0){
			$_POST['monthly_pay'] = number_format((float)PMT($interest, trim($_POST['periods']), trim(input_num('loan_amount'))), 2);
		} else {
			$_POST['monthly_pay'] = price_format(trim(input_num('loan_amount')) / trim($_POST['periods']));
		}

		Update('kv_empl_loan', array('id' => $selected_id), array('empl_id' => $_POST['empl_id'], 'loan_type_id' => $_POST['loan_type_id'],
			 'loan_amount' => input_num('loan_amount'), 'date' => array($_POST['date'], 'date'), 'monthly_pay' => input_num('monthly_pay'), 'periods' => $_POST['periods']));
		$sql = "SELECT allow.* FROM ".TB_PREF."kv_empl_allowances AS allow LEFT JOIN ".TB_PREF."kv_empl_loan_types AS types ON types.allowance_id= allow.id WHERE types.id=".$_POST['loan_type_id'];
		$res = db_query($sql, "Can't get allowances for loan");
		if($row = db_fetch($res)){
			$memo_ = 'Employee Loan Amount #'.$_POST['empl_id'].'-'. kv_get_empl_name($_POST['empl_id']);
			if($row['debit_code'] != '')
				Update('gl_trans', array('type_no' => $selected_id, 'type' => 98, 'account' => $row['debit_code'] ), array(  'memo_' => $memo_,   'amount' => (input_num('loan_amount')*$_POST['rate']), 'tran_date' => array($_POST['date'], 'date')));
				
			if($row['credit_code'] != '')
				Update('gl_trans', array('type_no' => $selected_id, 'account' => $row['credit_code'], 'type' => 98 ), array(  'memo_' => $memo_,   'amount' => -(input_num('loan_amount')*$_POST['rate']), 'tran_date' => array($_POST['date'], 'date')));
			add_audit_trail(98, $selected_id, $_POST['date'], trans("Updated."));
			
		}	
		display_notification(trans('Selected Loan has been updated'));
	}
	commit_transaction();
	//$Mode = 'RESET';
}

//----------------------------------------------------------------------------------------------------
if ($Mode == 'Delete'){	
	$get_selected = GetRow('kv_empl_loan', array('id' => $selected_id));
	if($get_selected['periods_paid'] == 0){
		Delete('kv_empl_loan', array('id' => $selected_id));
		Delete('gl_trans', array('type' => 98, 'type_no' => $selected_id));
		display_notification(trans('Selected employee loan has been deleted'));
		//meta_forward($_SERVER['PHP_SELF'], "Deleted=yes&empl_id=".$_POST['empl_id']);
	} else
		display_warning(trans("Sorry, you can't delete this Loan, it has some repayments."));
	
	$Mode = 'RESET';
}

if ($Mode == 'RESET'){
	$selected_id = -1;
	$sav = get_post('show_inactive');
	$dept = get_post('dept_id');
	unset($_POST);
	$_POST['dept_id'] = $dept;
	$_POST['show_inactive'] = $sav;
	$Ajax->activate('_page_body');
}


if (list_updated('empl_id')) {
	$Ajax->activate('price_table');
	$Ajax->activate('Edit_Loan');
}
if (list_updated('empl_id') || isset($_POST['periods']) || isset($_POST['loan_type_id'])) {
	// after change of stock, currency or salestype selector
	// display default calculated price for new settings. 
	// If we have this price already in db it is overwritten later.
	unset($_POST['monthly_pay']);
	//$Mode = $_POST['Mode'];
	$Ajax->activate('Edit_Loan');
}
//---------------------------------------------------------------------------------------------------

div_start('price_table');
	if(get_post('empl_id') > 0 ){
					$loans = GetDataJoin('kv_empl_loan AS loan', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_loan_types AS type', 'conditions' => '`type`.`id` = `loan`.`loan_type_id`'),
						1 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `loan`.`empl_id`')
					), 
					array('`info`.`empl_id`, CONCAT(`info`.`empl_firstname`, " ", `info`.`empl_lastname`) AS name, `type`.`loan_name`, `loan`.`loan_amount`, `loan`.`monthly_pay`, `loan`.`periods`, `loan`. `periods_paid`, `loan`. `date`, `loan`.`status`, `loan`.`id`, `type`.`interest_rate`'), array('`info`.`empl_id`' => get_post('empl_id')));
					//display_error(get_post('empl_id'));
			} else { 
				if(get_post('dept_id') > 0){
					//display_error(get_post('dept_id'));
					$loans = GetDataJoin('kv_empl_loan AS loan', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_loan_types AS type', 'conditions' => '`type`.`id` = `loan`.`loan_type_id`'),
						1 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `loan`.`empl_id`'), 
						2 => array('join' => 'INNER', 'table_name' => 'kv_empl_job AS job', 'conditions' => '`job`.`empl_id` = `loan`.`empl_id`') 
					), 
					array('`info`.`empl_id`, CONCAT(`info`.`empl_firstname`, " ", `info`.`empl_lastname`) AS name, `type`.`loan_name`, `loan`.`loan_amount`, `loan`.`monthly_pay`, `loan`.`periods`, `loan`. `periods_paid`, `loan`. `date`, `loan`.`status`, `loan`.`id`, `type`.`interest_rate`'), array('`job`.`department`' => get_post('dept_id')), array('info.empl_id' => 'asc'));
				}else {
					$loans = GetDataJoin('kv_empl_loan AS loan', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_loan_types AS type', 'conditions' => '`type`.`id` = `loan`.`loan_type_id`'),
						1 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `loan`.`empl_id`'), 
						//2 => array('join' => 'INNER', 'table_name' => 'kv_empl_job AS job', 'conditions' => '`job`.`empl_id` = `loan`.`empl_id`') 
					), 
					array('`info`.`empl_id`, CONCAT(`info`.`empl_firstname`, " ", `info`.`empl_lastname`) AS name, `type`.`loan_name`, `loan`.`loan_amount`, `loan`.`monthly_pay`, `loan`.`periods`, `loan`. `periods_paid`, `loan`. `date`, `loan`.`status`, `loan`.`id`, `type`.`interest_rate`'), array(), array('info.empl_id' => 'asc'));
				}
			}
			
			start_table(TABLESTYLE);

			$th = array (trans('Empl ID'), trans('Empl Name'), trans('Loan Type'),trans("Loan Amount"),  trans("Paid Amount"), trans("Monthly Pay"), trans("Periods"), trans("Periods Paid"), trans("Balance Amount"), trans("Start Date"), trans("End Date"), trans("Status"), '','', '');
			inactive_control_column($th);
			table_header($th);
			foreach($loans as $loan_single) {
				//display_error(json_encode($loan_single));
				$date_of_end = date('Y-m-d', strtotime("+".$loan_single[5]." months", strtotime($loan_single[7]))); 
				echo '<tr style="text-align:left"><td>'.$loan_single[0].'</td><td>'.$loan_single[1].'</td><td>'.$loan_single[2].'( '.$loan_single[10].' % )</td><td style="text-align:right;">'.price_format($loan_single[3]).'</td><td style="text-align:right;">'.price_format($loan_single[4]*$loan_single[6]).'</td><td style="text-align:right;">'.price_format($loan_single[4]).'</td><td>'.$loan_single[5].'</td><td>'.$loan_single[6].'</td><td>'.price_format($loan_single[4]* ($loan_single[5]-$loan_single[6])).'</td><td>'.sql2date($loan_single[7]).'</td><td>'.sql2date($date_of_end).'</td><td>'.($loan_single[8] ).'</td>';

				edit_button_cell("Edit".$loan_single['id'], trans("Edit"));
				repay_button_cell("Pay".$loan_single['id'], trans("Repayment"));
 				delete_button_cell("Delete".$loan_single['id'], trans("Delete"));
 				echo '<tr>';
			}
			end_table();
div_end();
//------------------------------------------------------------------------------------------------

echo "<br>";
if ($selected_id != -1){	
	div_start('Edit_Loan');
	//display_error($selected_id.'--'.$Mode);
	hidden('Mode' , $Mode);
 	if ($Mode == 'Edit') {
		
		$myrow = GetRow('kv_empl_loan', array('id' => $selected_id));
		$_POST['empl_id'] = $myrow['empl_id'];
		$_POST['loan_type_id']  = (isset($_POST['loan_type_id']) ? $_POST['loan_type_id'] : $myrow["loan_type_id"]);
		$_POST['loan_amount']  = (isset($_POST['loan_amount']) ? input_num('loan_amount') : number_format2($myrow["loan_amount"], $dec));
		$_POST['periods']  = (isset($_POST['periods']) ? $_POST['periods'] : $myrow["periods"]);
		$_POST['periods_paid']  = $myrow["periods_paid"];
		$_POST['monthly_pay']  = (isset($_POST['monthly_pay']) ? input_num('monthly_pay') : number_format2($myrow["monthly_pay"], $dec));
		$_POST['date']  = sql2date($myrow["date"]);
		$_POST['loan_date']  = sql2date($myrow["loan_date"]);
		$_POST['currency']  = $myrow["currency"];
		$_POST['rate']  = $myrow["rate"];
		
		start_table(TABLESTYLE2);
			table_section_title(trans("Edit Loan Information"));
			hidden('selected_id', $selected_id);
			hidden('empl_id', $myrow['empl_id']);
			hidden('currency', $_POST['currency']);
			hidden('rate', $_POST['rate']);
			hidden('periods_paid', $_POST['periods_paid']);
			label_row(trans("Employee Name").":", GetSingleValue('kv_empl_info', 'CONCAT(`empl_firstname`, " ", `empl_lastname`) AS name', array('empl_id' => $myrow['empl_id'])));
			amount_row(trans("Loan Amount").':', 'loan_amount', null);
			kv_empl_number_list_row(trans("Periods").':', 'periods', null, 1, 60, true);
			kv_loan_list_cells(trans("Loan type")." :", 'loan_type_id', null,	trans("Select a Loan Type"), true);
			date_row(trans("Start Date") . ":", 'date');
			date_row(trans("Loan Date") . ":", 'loan_date');
			$interest = get_loan_interest_rate($_POST['loan_type_id']);
				
			if($interest > 0){
				$_POST['monthly_pay'] = number_format((float)PMT($interest, trim($_POST['periods']), trim(input_num('loan_amount'))), 2);
			} else {
				$_POST['monthly_pay'] = price_format(trim(input_num('loan_amount')) / trim($_POST['periods']));
			}
				//display_error($_POST['monthly_pay']);
			amount_row(trans("Net Pay").':', 'monthly_pay', null);
		end_table(1);	
	
		if(!isset($_POST['periods_paid']) || $_POST['periods_paid'] == 0 )
			submit_add_or_update_center($selected_id == -1, '', 'both');
		else
			display_warning(trans("Sorry You can't edit after collecting a single repayment"));
	}elseif($Mode == 'Pay') {
		$myrow = GetRow('kv_empl_loan', array('id' => $selected_id));
		$_POST['empl_id'] = $myrow['empl_id'];
		$_POST['loan_type_id']  = GetSingleValue('kv_empl_loan_types', 'loan_name', array('id' => $myrow["loan_type_id"]));
		$_POST['loan_amount']  = number_format2($myrow["loan_amount"], $dec);
		$_POST['periods']  = $myrow["periods"];
		$_POST['rate']  = $myrow["rate"];
		$_POST['periods_paid']  = $myrow["periods_paid"];
		$_POST['monthly_pay']  = number_format2($myrow["monthly_pay"], $dec);		
	
		start_table(TABLESTYLE2);
			table_section_title(trans("Loan Repayment"));
			hidden('selected_id', $selected_id);
			hidden('monthly_pay', $_POST['monthly_pay']);
			hidden('periods_paid', $_POST['periods_paid']);
			hidden('periods', $_POST['periods']);
			hidden('repayment', 'yes');
			hidden('employee_id', $myrow["empl_id"]);
			hidden('rate', $_POST['rate']);
			//employee_list_cells(trans("Select an Employee: "), 'empl_id', null,	trans("Select An Employee"), false, check_value('show_inactive'));
			label_row(trans("Employee Name")." :", GetSingleValue('kv_empl_info', 'CONCAT(`empl_firstname`, " ", `empl_lastname`) AS name', array('empl_id')));
			label_row(trans("Loan Amount").' :', $_POST['loan_amount']);
			label_row(trans("Periods").' :', $_POST['periods']);
			label_row(trans("Loan type") . " :", $_POST['loan_type_id']);
			date_row(trans("Date") . " :", 'date');

			$interest = get_loan_interest_rate($_POST['loan_type_id']);
			
			if($interest > 0){
				$_POST['monthly_pay'] = number_format((float)PMT($interest, trim($_POST['periods']), trim(input_num('loan_amount'))), 2);
			} else {
				$_POST['monthly_pay'] = price_format(trim(input_num('loan_amount')) / trim($_POST['periods']));
			}
			$balance_mount = (($myrow['monthly_pay']*$myrow['periods'])-($myrow['monthly_pay']*$myrow['periods_paid']));
			label_row(trans("Balance Amount").':', $balance_mount);
			$repay_ar = array();
			for($vj =1; $vj<= ($myrow["periods"]-$myrow['periods_paid']); $vj++)
				$repay_ar[$vj] = $myrow['monthly_pay']*$vj;
			$options = array();
			start_row(); 
			echo '<td> '.trans("Re-Pay Amount").'</td> <td>'.array_selector('period_to', null, $repay_ar, $options).'</td>';
			end_row();
			$sql = "SELECT allow.* FROM ".TB_PREF."kv_empl_allowances AS allow LEFT JOIN ".TB_PREF."kv_empl_loan_types AS types ON types.allowance_id= allow.id WHERE types.id=".$myrow['loan_type_id'];
			$res = db_query($sql, "Can't get allowances for loan");
			if($row = db_fetch($res)){
				$_POST['debit_code'] = $row['debit_code'];
				$_POST['credit_code'] = $row['credit_code'];
			}
			echo '<tr><td>'.trans("Debit Account") . " :".'</td><td>'.gl_all_accounts_list('credit_code', null, false, false, trans("Select account"), false, false, false).'</td></tr>';
			echo '<tr><td>'.trans("Credit Account") . " :".'</td><td>'.gl_all_accounts_list('debit_code', null, false, false, trans("Select account"), false, false, false).'</td></tr>';
			end_row();
			//text_row_ex(trans("Re-Pay Amount").':', 'monthly_pay', 20);
		end_table(1);	

		if($_POST['periods_paid'] != $_POST['periods'] )
			submit_add_or_update_center($selected_id == -1, '', 'both');
		else
			display_warning(trans("Sorry You can't repay after paying the full amount"));
	}	
	div_end();
} 	

end_form();
end_page();

function PMT($i, $n, $p) {
	$i = $i/1200; 
	$p = -$p; 
	return $i * $p * pow((1 + $i), $n) / (1 - pow((1 + $i), $n));
}
//----------------------------------------------------------------------------------------------------
function get_loan_interest_rate($id){
	return GetSingleValue('kv_empl_loan_types', 'interest_rate', array('id' => $id));
}
//----------------------------------------------------------------------------------------------------
function can_process(){
	if ($_POST['empl_id'] == ""){
		display_error(trans("There is no Employee selected."));
		set_focus('selected_id');
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
	/*$form_date = date2sql($_POST['date']);
	$today_date = date("Y-m-d");
	if($form_date < $today_date && GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'test_mode')) == 0){
		display_error(trans("Enter a Valid Date."));
		set_focus('date');
		return false;
	}
	if(GetSingleValue('kv_empl_loan', 'id', array('empl_id' => $_POST['empl_id'], 'status' => 'Active')) != $_POST['selected_id']){
		display_error(trans("The Selected Employee has another loan, which has not closed yet."));
		set_focus('date');
		return false;
	}*/
	return true;
}
?>
