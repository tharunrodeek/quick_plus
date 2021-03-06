<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-Jul-2017                                 |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|                                                        |
\=======================================================*/

$page_security = 'SA_EMPL';
$path_to_root = '../../..';
include_once($path_to_root . '/includes/ui/items_cart.inc');
include_once($path_to_root . '/includes/session.inc');
add_access_extensions();

$js = '';
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

include_once($path_to_root . '/includes/ui.inc');
include_once($path_to_root . '/modules/FrontHrm/includes/frontHrm_db.inc');
include_once($path_to_root . '/modules/FrontHrm/includes/frontHrm_ui.inc');
include_once($path_to_root . '/reporting/includes/reporting.inc');

if(isset($_GET['PayslipNo']))
	$_SESSION['page_title'] = trans($help_context = 'Make Payment Advice for Payslip #').$_GET['PayslipNo'];	
else {
	$_POST['NewPaymentAdvice'] = 'Yes';
	$_SESSION['page_title'] = trans($help_context = 'Make Payment Advice');
}

page($_SESSION['page_title'], false, false, '', $js);

//--------------------------------------------------------------------------

function line_start_focus() {
  global $Ajax;

  $Ajax->activate('items_table');
  set_focus('_code_id_edit');
}

//--------------------------------------------------------------------------

if (isset($_GET['AddedID'])) {
	$trans_no = $_GET['AddedID'];
	$trans_type = ST_JOURNAL;
    $payslip_no = get_payslip_from_advice($trans_no)['payslip_no'];
   	display_notification_centered( sprintf(trans("Employee Payment Advice #%d has been entered"), $trans_no));
   	display_note(hrm_print_link($payslip_no, trans('Print this Payslip'), true, ST_PAYSLIP, false, '', '', 0));br();
    display_note(get_gl_view_str($trans_type, $trans_no, trans('&View this Payment Advice')));

	reset_focus();
	hyperlink_params($_SERVER['PHP_SELF'], trans('Enter &New Payment Advice'), 'NewPaymentAdvice=Yes');
	hyperlink_params($path_to_root.'/admin/attachments.php', trans('Add an Attachment'), 'filterType='.$trans_type.'&trans_no='.$trans_no);

	display_footer_exit();
}
elseif (isset($_GET['UpdatedID'])) {
    
	$trans_no = $_GET['UpdatedID'];
	$trans_type = ST_JOURNAL;

   	display_notification_centered(sprintf(trans("Employee Payment Advice #%d has been updated"), $trans_no));
    display_note(get_gl_view_str($trans_type, $trans_no, trans('&View this Journal Entry')));

   	hyperlink_no_params($path_to_root.'/gl/inquiry/journal_inquiry.php', trans('Return to Journal &Inquiry'));

	display_footer_exit();
}

//--------------------------------------------------------------------------

if (isset($_GET['PayslipNo'])) {

	if(has_payment_advice($_GET['PayslipNo'])) {

		display_error(trans('Payment advice exist'));
		hyperlink_params($path_to_root.'/modules/FrontHrm/inquiry/payment_advices.php',trans('Payment Advices'));
		display_footer_exit();
	}
	elseif(get_payslip(false, $_GET['PayslipNo'])['payslip_no'] == null) {
		display_error(trans('Payslip number does not exist'));
		hyperlink_params($path_to_root.'/modules/FrontHrm/inquiry/payment_advices.php',trans('Payment Advices'));
		display_footer_exit();
	}
    else {
		$payslip = get_payslip(false, $_GET['PayslipNo']);
		$_POST['PaySlipNo'] = $payslip['payslip_no'];
	}
	// $_POST['PaySlipNo'] = $_GET['PayslipNo'];

	create_cart(0, 0, $payslip);
} 
elseif (isset($_GET['NewPaymentAdvice'])) {
    $_POST['PaySlipNo'] = 0;
    create_cart(0, 0);
}

//--------------------------------------------------------------------------

function create_cart($type = 0, $trans_no =0, $payslip=array()) {
	global $Refs, $Payable_act;

	if (isset($_SESSION['journal_items']))
		unset ($_SESSION['journal_items']);
    
    check_is_closed($type, $trans_no);
	$cart = new items_cart($type);

    $cart->payslip_no = $_POST['PaySlipNo'];
    $cart->order_id = $trans_no;
    $cart->person_id = 0;
	$cart->paytype = PT_EMPLOYEE;

	$cart->reference = $Refs->get_next(ST_JOURNAL, null, $cart->tran_date);
	$cart->tran_date = new_doc_date();
	if (!is_date_in_fiscalyear($cart->tran_date))
		$cart->tran_date = end_fiscalyear();
	$_POST['ref_original'] = -1;

	$_POST['ref'] = $cart->reference;
	$_POST['date_'] = $cart->tran_date;
    $cart->to_the_order_of = '';

	if($payslip && count($payslip) > 0) {

		$cart->payslip_trans_no = $payslip['trans_no'];
		$cart->person_id = $payslip['emp_id'];
		$cart->payslip_no = $payslip['payslip_no'];
        $_POST['emp_id'] = $cart->person_id;
        $_POST['for_payslip'] = $cart->payslip_no;
        $_POST['memo_'] = trans('Payment advice gl entry For Payslip #').$cart->payslip_no;

		$pay_amt = $payslip['payable_amount'];

		$bank = get_default_bank_account();

		$_POST['bank_account'] = $bank['id'];
	
		$cart->add_gl_item($Payable_act, 0, 0, $pay_amt, '');
		$cart->add_gl_item($bank['account_code'], 0, 0, -$pay_amt, '');
	}
	$_SESSION['journal_items'] = &$cart;
}

//--------------------------------------------------------------------------

if (isset($_POST['Process'])) {
	$input_error = 0;

	if ($_SESSION['journal_items']->count_gl_items() < 1) {
		display_error(trans('You must enter at least one journal line.'));
		set_focus('code_id');
		$input_error = 1;
	}
	if (abs($_SESSION['journal_items']->gl_items_total()) > 0.0001) {
		display_error(trans('The journal must balance (debits equal to credits) before it can be processed.'));
		set_focus('code_id');
		$input_error = 1;
	}
	if (!is_date($_POST['date_'])) {
		display_error(trans('The entered date is invalid.'));
		set_focus('date_');
		$input_error = 1;
	} 
	elseif (!is_date_in_fiscalyear($_POST['date_'])) {
		display_error(trans('The entered date is not in fiscal year.'));
		set_focus('date_');
		$input_error = 1;
	} 
	if (!check_reference($_POST['ref'], ST_JOURNAL, $_SESSION['journal_items']->order_id)) {
   		set_focus('ref');
   		$input_error = 1;
	}
	if ($input_error == 1)
		unset($_POST['Process']);
}

if (isset($_POST['Process'])) {
	$cart = &$_SESSION['journal_items'];
	$new = $cart->order_id == 0;
//  ------------- Giấu thông báo lỗi---
    $cart->to_date = 
    $cart->from_date = 
    $cart->leaves = 
    $cart->deductable_leaves = '';
//  -----------------------------------
	$cart->person_id = $_POST['emp_id'];
	$cart->paytype = $_POST['PayType'];

	$cart->to_the_order_of = $_POST['to_the_order_of'];
	$cart->payslip_no = $_POST['PaySlipNo'];

	$cart->reference = $_POST['ref'];
	$cart->memo_ = $_POST['memo_'];
	$cart->tran_date = $_POST['date_'];

	$trans_no = write_payslip($cart, check_value('Reverse'));

	$cart->clear_items();
	new_doc_date($_POST['date_']);
	unset($_SESSION['journal_items']);
	if($new)
		meta_forward($_SERVER['PHP_SELF'], 'AddedID='.$trans_no);
	else
		meta_forward($_SERVER['PHP_SELF'], 'UpdatedID='.$trans_no);
}

//--------------------------------------------------------------------------

function check_item_data() {

	if (isset($_POST['dimension_id']) && $_POST['dimension_id'] != 0 && dimension_is_closed($_POST['dimension_id'])) {
		display_error(trans('Dimension is closed.'));
		set_focus('dimension_id');
		return false;
	}
	if (isset($_POST['dimension2_id']) && $_POST['dimension2_id'] != 0 && dimension_is_closed($_POST['dimension2_id'])) {
		display_error(trans('Dimension is closed.'));
		set_focus('dimension2_id');
		return false;
	}
	if (!(input_num('AmountDebit')!=0 ^ input_num('AmountCredit')!=0) ) {
		display_error(trans('You must enter either a debit amount or a credit amount.'));
		set_focus('AmountDebit');
    	return false;
  	}
	if (strlen($_POST['AmountDebit']) && !check_num('AmountDebit', 0)) {
    	display_error(trans('The debit amount entered is not a valid number or is less than zero.'));
		set_focus('AmountDebit');
    	return false;
  	}
  	elseif (strlen($_POST['AmountCredit']) && !check_num('AmountCredit', 0)) {
    	display_error(trans('The credit amount entered is not a valid number or is less than zero.'));
		set_focus('AmountCredit');
    	return false;
  	}
	if (!is_tax_gl_unique(get_post('code_id'))) {
   		display_error(trans('Cannot post to GL account used by more than one tax type.'));
		set_focus('code_id');
   		return false;
	}
	if (!$_SESSION['wa_current_user']->can_access('SA_BANKJOURNAL') && is_bank_account($_POST['code_id'])) {
		display_error(trans('Cannot make a journal entry for a bank account. Use banking functions for bank transactions.'));
		set_focus('code_id');
		return false;
	}
   	return true;
}

//--------------------------------------------------------------------------

function handle_update_item() {

    if($_POST['UpdateItem'] != '' && check_item_data()) {
    	if (input_num('AmountDebit') > 0)
    		$amount = input_num('AmountDebit');
    	else
    		$amount = -input_num('AmountCredit');

    	$_SESSION['journal_items']->update_gl_item($_POST['Index'], $_POST['code_id'], $_POST['dimension_id'], $_POST['dimension2_id'], $amount, $_POST['LineMemo']);
    }
	line_start_focus();
}

function handle_delete_item($id) {
	$_SESSION['journal_items']->remove_gl_item($id);
	line_start_focus();
}

function handle_new_item() {
	if (!check_item_data())
		return;

	if (input_num('AmountDebit') > 0)
		$amount = input_num('AmountDebit');
	else
		$amount = -input_num('AmountCredit');
	
	$_SESSION['journal_items']->add_gl_item($_POST['code_id'], $_POST['dimension_id'], $_POST['dimension2_id'], $amount, $_POST['LineMemo']);
	line_start_focus();
}

//--------------------------------------------------------------------------

$id = find_submit('Delete');
if ($id != -1)
	handle_delete_item($id);

if (isset($_POST['AddItem'])) 
	handle_new_item();

if (isset($_POST['UpdateItem'])) 
	handle_update_item();
	
if (isset($_POST['CancelItemChanges']))
	line_start_focus();

if (isset($_POST['go'])) {
	display_quick_entries($_SESSION['journal_items'], $_POST['emp_id'], input_num('totamount'), QE_JOURNAL);
	$_POST['totamount'] = price_format(0); $Ajax->activate('totamount');
	line_start_focus();
}

//--------------------------------------------------------------------------

start_form();
display_advice_header($_SESSION['journal_items']);

if(isset($_GET['NewPaymentAdvice']))
	hidden('NewPaymentAdvice');

start_table(TABLESTYLE2, "width='90%'", 10);
start_row();
echo '<td>';
display_gl_items(trans('Rows'), $_SESSION['journal_items']);
gl_options_controls();
echo '</td>';
end_row();
end_table(1);

submit_center('Process', trans('Process Payment Advice'), true , trans('Process journal entry only if debits equal to credits'));

end_form();
end_page();