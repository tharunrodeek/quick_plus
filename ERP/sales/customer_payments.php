<?php
/**********************************************************************
 * Copyright (C) FrontAccounting, LLC.
 * Released under the terms of the GNU General Public License, GPL,
 * as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 ***********************************************************************/
$page_security = 'SA_SALESPAYMNT';
$path_to_root = "..";
include_once($path_to_root . "/includes/ui/allocation_cart.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$js = "";
if ($SysPrefs->use_popup_windows) {
    $js .= get_js_open_window(900, 500);
}
if (user_use_date_picker()) {
    $js .= get_js_date_picker();
}
add_js_file('payalloc.js');
//add_js_file('customer_payments.js');

page(_($help_context = "Customer Payment Entry"), false, false, "", $js);

//----------------------------------------------------------------------------------------------

check_db_has_customers(_("There are no customers defined in the system."));

check_db_has_bank_accounts(_("There are no bank accounts defined in the system."));

//----------------------------------------------------------------------------------------
if (isset($_GET['customer_id'])) {
    $_POST['customer_id'] = $_GET['customer_id'];

}

if (!isset($_POST['bank_account'])) { // first page call




    $_SESSION['alloc'] = new allocation(ST_CUSTPAYMENT, 0, get_post('customer_id'));

    if (isset($_GET['SInvoice'])) {
        //  get date and supplier
        $inv = get_customer_trans($_GET['SInvoice'], ST_SALESINVOICE);
        $dflt_act = get_default_bank_account($inv['curr_code']);
        $_POST['bank_account'] = $dflt_act['id'];
        if ($inv) {
            $_POST['customer_id'] = $inv['debtor_no'];
            $_SESSION['alloc']->set_person($inv['debtor_no'], PT_CUSTOMER);
            $_SESSION['alloc']->read();
            $_POST['BranchID'] = $inv['branch_code'];
            $_POST['DateBanked'] = sql2date($inv['tran_date']);
            foreach ($_SESSION['alloc']->allocs as $line => $trans) {
                if ($trans->type == ST_SALESINVOICE && $trans->type_no == $_GET['SInvoice']) {
                    $un_allocated = $trans->amount - $trans->amount_allocated;
                    if ($un_allocated) {
                        $_SESSION['alloc']->allocs[$line]->current_allocated = $un_allocated;
                        $_POST['amount'] = $_POST['amount' . $line] = price_format($un_allocated);
                    }
                    break;
                }
            }
            unset($inv);
        } else
            display_error(_("Invalid sales invoice number."));
    }
}

if (list_updated('BranchID')) {
    // when branch is selected via external editor also customer can change
    $br = get_branch(get_post('BranchID'));
    $_POST['customer_id'] = $br['debtor_no'];
    $_SESSION['alloc']->person_id = $br['debtor_no'];
    $Ajax->activate('customer_id');
}

//$Ajax->activate('invoice_from_date');


if (isset($_POST['invoice_from_date']) || isset($_POST['invoice_to_date'])) {
    $Ajax->activate('customer_id');
}


//display_error(print_r($_POST['payment_method'],true)); die;


if (!isset($_POST['customer_id'])) {


//    display_error(print_r(234234 ,true));

//    $_POST['invoice_from_date']=null;
//    $_POST['invoice_to_date']=null;

//    $_POST['customer_id'] = get_global_customer(false);
    $_POST['customer_id'] = null;
    $_SESSION['alloc']->set_person($_POST['customer_id'], PT_CUSTOMER);
    $_SESSION['alloc']->read();
    $dflt_act = get_default_bank_account($_SESSION['alloc']->person_curr);
    $_POST['bank_account'] = $dflt_act['id'];
}
if (!isset($_POST['DateBanked'])) {
    $_POST['DateBanked'] = new_doc_date();
    if (!is_date_in_fiscalyear($_POST['DateBanked'])) {
        $_POST['DateBanked'] = end_fiscalyear();
    }
}


if (isset($_GET['AddedID'])) {
    $payment_no = $_GET['AddedID'];

    display_notification_centered(_("The customer payment has been successfully entered."));

    submenu_print(_("&Print This Receipt"), ST_CUSTPAYMENT, $payment_no . "-" . ST_CUSTPAYMENT, 'prtopt');

    submenu_view(_("&View this Customer Payment"), ST_CUSTPAYMENT, $payment_no);

    submenu_option(_("Enter Another &Customer Payment"), "/sales/customer_payments.php");
    submenu_option(_("Enter Other &Deposit"), "/gl/gl_bank.php?NewDeposit=Yes");
    submenu_option(_("Enter Payment to &Supplier"), "/purchasing/supplier_payment.php");
    submenu_option(_("Enter Other &Payment"), "/gl/gl_bank.php?NewPayment=Yes");
    submenu_option(_("Bank Account &Transfer"), "/gl/bank_transfer.php");

    display_note(get_gl_view_str(ST_CUSTPAYMENT, $payment_no, _("&View the GL Journal Entries for this Customer Payment")));

    display_footer_exit();
} elseif (isset($_GET['UpdatedID'])) {
    $payment_no = $_GET['UpdatedID'];

    display_notification_centered(_("The customer payment has been successfully updated."));

    submenu_print(_("&Print This Receipt"), ST_CUSTPAYMENT, $payment_no . "-" . ST_CUSTPAYMENT, 'prtopt');

    display_note(get_gl_view_str(ST_CUSTPAYMENT, $payment_no, _("&View the GL Journal Entries for this Customer Payment")));

//	hyperlink_params($path_to_root . "/sales/allocations/customer_allocate.php", _("&Allocate this Customer Payment"), "trans_no=$payment_no&trans_type=12");

    hyperlink_no_params($path_to_root . "/sales/inquiry/customer_inquiry.php?", _("Select Another Customer Payment for &Edition"));

    hyperlink_no_params($path_to_root . "/sales/customer_payments.php", _("Enter Another &Customer Payment"));

    display_footer_exit();
}

//----------------------------------------------------------------------------------------------

function can_process()
{
    global $Refs;

    if (!get_post('customer_id')) {
        display_error(_("There is no customer selected."));
        set_focus('customer_id');
        return false;
    }

    if (!get_post('BranchID')) {
        display_error(_("This customer has no branch defined."));
        set_focus('BranchID');
        return false;
    }

    if (empty($_POST['dimension_id'])) {
        display_error(_("Please select a cost center."));
        set_focus('dimension_id');
        return false;
    }

    if (!isset($_POST['DateBanked']) || !is_date($_POST['DateBanked'])) {
        display_error(_("The entered date is invalid. Please enter a valid date for the payment."));
        set_focus('DateBanked');
        return false;
    } elseif (!is_date_in_fiscalyear($_POST['DateBanked'])) {
        display_error(_("The entered date is out of fiscal year or is closed for further data entry."));
        set_focus('DateBanked');
        return false;
    }

    if (!check_reference($_POST['ref'], ST_CUSTPAYMENT, @$_POST['trans_no'])) {
        set_focus('ref');
        return false;
    }

    if (!check_num('amount', 0)) {
        display_error(_("The entered amount is invalid or negative and cannot be processed."));
        set_focus('amount');
        return false;
    }

    if (isset($_POST['charge']) && !check_num('charge', 0)) {
        display_error(_("The entered amount is invalid or negative and cannot be processed."));
        set_focus('charge');
        return false;
    }


    if (isset($_POST['charge']) && input_num('charge') > 100) {
        display_error(_("Bank charges should be in percentage and should not exceed 100"));
        set_focus('charge');
        return false;
    }


    if (isset($_POST['charge']) && input_num('charge') > 0) {
        $charge_acct = get_bank_charge_account($_POST['bank_account']);
        if (get_gl_account($charge_acct) == false) {
            display_error(_("The Bank Charge Account has not been set in System and General GL Setup."));
            set_focus('charge');
            return false;
        }
    }

    if (@$_POST['discount'] == "") {
        $_POST['discount'] = 0;
    }
    if (@$_POST['add_discount'] == "") {
        $_POST['add_discount'] = 0;
    }

    if (!check_num('add_discount')) {
        display_error(_("The entered additional discount is not a valid number."));
        set_focus('add_discount');
        return false;
    }


    if (!check_num('discount')) {
        display_error(_("The entered discount is not a valid number."));
        set_focus('discount');
        return false;
    }

    if (input_num('amount') <= 0) {
        display_error(_("The balance of the amount and discount is zero or negative. Please enter valid amounts."));
        set_focus('discount');
        return false;
    }

    if (isset($_POST['bank_amount']) && input_num('bank_amount') <= 0) {
        display_error(_("The entered payment amount is zero or negative."));
        set_focus('bank_amount');
        return false;
    }


    if ($_POST['discount'] && ($_POST['discount'] > 0) && getAvailableRewardAmount($_POST['customer_id']) < $_POST['discount']) {
        display_error(_("Available reward amount is less than entered amount"));
        set_focus('redeem_reward_amount');
        return false;
    }

    if (!db_has_currency_rates(get_customer_currency($_POST['customer_id']), $_POST['DateBanked'], true))
        return false;

    $_SESSION['alloc']->amount = input_num('amount');


    if (isset($_POST["TotalNumberOfAllocs"]))
        return check_allocations();
    else
        return true;
}

//----------------------------------------------------------------------------------------------

if (isset($_POST['_customer_id_button'])) {
//	unset($_POST['branch_id']);
    $Ajax->activate('BranchID');
}

//----------------------------------------------------------------------------------------------

if (get_post('AddPaymentItem') && can_process()) {

    if(!isset($_POST['bank_account']) || empty($_POST['bank_account']))
        $_POST['bank_account'] = $_POST['bank_account_id'];

    $ref = get_next_payment_ref($_POST['dimension_id']);


    new_doc_date($_POST['DateBanked']);

    $discount = input_num('discount') + input_num('add_discount');

    $new_pmt = !$_SESSION['alloc']->trans_no;
    $payment_no = write_customer_payment($_SESSION['alloc']->trans_no, $_POST['customer_id'], $_POST['BranchID'],
        $_POST['bank_account'], $_POST['DateBanked'], $ref,
        input_num('amount'), $discount, $_POST['memo_'], 0,
        input_num('charge'),
        input_num('bank_amount',
            input_num('amount')),
        $_POST['payment_method'],$_POST['dimension_id']);

    $_SESSION['alloc']->trans_no = $payment_no;
    $_SESSION['alloc']->write();

    unset($_SESSION['alloc']);
    meta_forward($_SERVER['PHP_SELF'], $new_pmt ? "AddedID=$payment_no" : "UpdatedID=$payment_no");
}

//----------------------------------------------------------------------------------------------

function read_customer_data()
{


    global $Refs;

    $myrow = get_customer_habit($_POST['customer_id']);

    $_POST['HoldAccount'] = $myrow["dissallow_invoices"];
    $_POST['pymt_discount'] = $myrow["pymt_discount"];
    // To support Edit feature
    // If page is called first time and New entry fetch the nex reference number
    if (!$_SESSION['alloc']->trans_no && !isset($_POST['charge']))
        $_POST['ref'] = $Refs->get_next(ST_CUSTPAYMENT, null, array(
            'customer' => get_post('customer_id'), 'date' => get_post('DateBanked')));

    $_POST['given_amount'] = "";
    $_POST['balance_amount'] = "";

}

//function getCustomerBalance($customer_id)
//{
//    $sql = "select IFNULL(sum(ov_amount),0) as invoiced_amt from 0_debtor_trans where debtor_no='$customer_id' and type=10";
//    $result = db_query($sql, "could not get customer");
//    $result = db_fetch($result);
//    $invoiced_amount = $result['invoiced_amt'];
//
//    $sql = "select IFNULL(sum(ov_amount),0) as paid_amt from 0_debtor_trans where debtor_no='$customer_id' and type=12";
//    $result = db_query($sql, "could not get customer");
//    $result = db_fetch($result);
//    $paid_amt = $result['paid_amt'];
//
//    return round($paid_amt-$invoiced_amount,2);
//
//}


function getCustomerBalance($customer_id)
{
//    $sql = "select IFNULL(sum(ov_amount),0) as invoiced_amt from 0_debtor_trans where debtor_no='$customer_id' and type=10";
//    $result = db_query($sql, "could not get customer");
//    $result = db_fetch($result);
//    $invoiced_amount = $result['invoiced_amt'];
//
//    $sql = "select IFNULL(sum(ov_amount),0) as paid_amt from 0_debtor_trans where debtor_no='$customer_id' and type=12";
//    $result = db_query($sql, "could not get customer");
//    $result = db_fetch($result);
//    $paid_amt = $result['paid_amt'];
//
//    return round($paid_amt-$invoiced_amount,2);


    $sql = get_sql_for_customer_allocation_inquiry(begin_fiscalyear(), Today(),
        $customer_id, null, null);

    $result = db_query($sql, "could not get customer");
    $prepaid_bal = 0;
    $out_standing_bal = 0;
    while ($row = db_fetch($result)) {
        $balance = ($row["type"] == ST_JOURNAL && $row["TotalAmount"] < 0 ? -$row["TotalAmount"] :
                $row["TotalAmount"]) - $row["Allocated"];
        if ($row["type"] == ST_CUSTCREDIT && $row['TotalAmount'] > 0) {
            /*its a credit note which could have an allocation */
            $prepaid_bal += $balance;
        } elseif ($row["type"] == ST_JOURNAL && $row['TotalAmount'] < 0) {
            $prepaid_bal += $balance;
        } elseif (($row["type"] == ST_CUSTPAYMENT || $row["type"] == ST_BANKDEPOSIT) &&
            (floatcmp($row['TotalAmount'], $row['Allocated']) >= 0)) {
            /*its a receipt  which could have an allocation*/
            $prepaid_bal += $balance;
        } elseif ($row["type"] == ST_CUSTPAYMENT && $row['TotalAmount'] <= 0) {
            /*its a negative receipt */
            $prepaid_bal += 0;
//        return '';
        } elseif (($row["type"] == ST_SALESINVOICE && ($row['TotalAmount'] - $row['Allocated']) > 0) || $row["type"] == ST_BANKPAYMENT) {
            $out_standing_bal += $balance;
        }
        else{
            $out_standing_bal += $balance;
        }

    }

    return $prepaid_bal-$out_standing_bal;


}

if ($_POST['customer_id']) {

    $_POST['available_reward'] = getAvailableRewardAmount($_POST['customer_id']);
    $cust_bal_amt = getCustomerBalance($_POST['customer_id']);
}
else{
    $cust_bal_amt = '';
}

//if ($_POST['customer_id']) {
//
//    $_POST['available_reward'] = getAvailableRewardAmount($_POST['customer_id']);
//}

//----------------------------------------------------------------------------------------------
$new = 1;

// To support Edit feature
if (isset($_GET['trans_no']) && $_GET['trans_no'] > 0) {

    $_POST['trans_no'] = $_GET['trans_no'];

    $new = 0;
    $myrow = get_customer_trans($_POST['trans_no'], ST_CUSTPAYMENT);
    $_POST['customer_id'] = $myrow["debtor_no"];
    $_POST['customer_name'] = $myrow["DebtorName"];
    $_POST['BranchID'] = $myrow["branch_code"];
    $_POST['bank_account'] = $myrow["bank_act"];
    $_POST['ref'] = $myrow["reference"];
    $charge = get_cust_bank_charge(ST_CUSTPAYMENT, $_POST['trans_no']);
    $_POST['charge'] = price_format($charge);
    $_POST['DateBanked'] = sql2date($myrow['tran_date']);
    $_POST["amount"] = price_format($myrow['Total'] - $myrow['ov_discount']);
    $_POST["bank_amount"] = price_format($myrow['bank_amount'] + $charge);
    $_POST["discount"] = price_format($myrow['ov_discount']);
    $_POST["memo_"] = get_comments_string(ST_CUSTPAYMENT, $_POST['trans_no']);

    //Prepare allocation cart
    if (isset($_POST['trans_no']) && $_POST['trans_no'] > 0)
        $_SESSION['alloc'] = new allocation(ST_CUSTPAYMENT, $_POST['trans_no']);
    else {
        $_SESSION['alloc'] = new allocation(ST_CUSTPAYMENT, $_POST['trans_no']);
        $Ajax->activate('alloc_tbl');
    }
}

//----------------------------------------------------------------------------------------------
$new = !$_SESSION['alloc']->trans_no;
start_form();

hidden('trans_no');

start_outer_table(TABLESTYLE2, "width='60%'", 5);

table_section(1);
table_section_title("Filters");
echo "<style>table.tablestyle2 td:nth-child(2) {width: 30% !important;}</style>";


if ($new)
    customer_list_row(_("From Customer:"), 'customer_id', null, true, true);
else {
    label_cells(_("From Customer:"), $_SESSION['alloc']->person_name, "class='label'");
    hidden('customer_id', $_POST['customer_id']);
}


if(!list_updated('dimension_id')) {
    $user_id = $_SESSION['wa_current_user']->user;
    $user = get_user($user_id);
    $_POST['dimension_id'] = $user['dflt_dimension_id'];

//    pp(get_next_payment_ref($_POST['dimension_id']));
}



$dim = get_company_pref('use_dimension');
if ($dim > 0)
    dimensions_list_row(trans("Dimension") . ":", 'dimension_id',
        $_POST['dimension_id'], true, '--All--', false, 1, false);
else
    hidden('dimension_id', 0);
if ($dim > 1)
    dimensions_list_row(trans("Dimension") . " 2:", 'dimension2_id',
        null, true, ' ', false, 2, false);
else
    hidden('dimension2_id', 0);




if ($_POST['customer_id']) {

    if(floatval($cust_bal_amt) > 0) {
        $add_text = "(Advance)";
    }

    if(floatval($cust_bal_amt) < 0) {
        $add_text = "(Outstanding)";
    }

    label_row("Available Balance", $cust_bal_amt.$add_text, null, null, null, 'available_bal');
}
if (db_customer_has_branches($_POST['customer_id'])) {

    hidden('BranchID', $_POST['customer_id']);

//	customer_branches_list_row(_("Branch:"), $_POST['customer_id'], 'BranchID', null, false, true, true);
} else {
    hidden('BranchID', ANY_NUMERIC);
}


text_row(_("Barcode/Invoice No :"), 'barcode', null, 50, 50);
date_row(_("Invoice date from:"), 'invoice_from_date', '', true, 0, 0, 1001, null, true);
date_row(_("Invoice date to:"), 'invoice_to_date', '', true, 0, 0, 1001, null, true);


if(!isset($_POST['available_reward'])) {
    $_POST['available_reward'] = "";
}

//label_row(_("Total Available Reward Amount:"), $_POST['available_reward'], null);

if (list_updated('customer_id') || list_updated('barcode')
    || ($new && list_updated('bank_account')) || $_POST['invoice_from_date'] || $_POST['invoice_to_date']) {

//    display_error(print_r(242342342423 ,true));

    $_SESSION['alloc']->barcode = $_POST['barcode'];
    $_SESSION['alloc']->dimension_id = $_POST['dimension_id'];
    $_SESSION['alloc']->invoice_from_date = $_POST['invoice_from_date'];
    $_SESSION['alloc']->invoice_to_date = $_POST['invoice_to_date'];


    $_SESSION['alloc']->set_person($_POST['customer_id'], PT_CUSTOMER);
    $_SESSION['alloc']->read();
    $_POST['memo_'] = $_POST['amount'] = $_POST['discount'] = '';
    if (list_updated('customer_id')) {
        $dflt_act = get_default_bank_account($_SESSION['alloc']->person_curr);
        $_POST['bank_account'] = $dflt_act['id'];
    }
    $Ajax->activate('_page_body');
}


read_customer_data();

set_global_customer($_POST['customer_id']);
if (isset($_POST['HoldAccount']) && $_POST['HoldAccount'] != 0)
    display_warning(_("This customer account is on hold."));
$display_discount_percent = percent_format($_POST['pymt_discount'] * 100) . "%";

table_section(2);

table_section_title("Payment Information");


$curr_user = get_user($_SESSION["wa_current_user"]->user);
if(!empty($curr_user['cashier_account']) && !list_updated('bank_account'))
    $_POST['bank_account'] = $curr_user['cashier_account'];



/** Showing Bank accounts by Type */

$options = array('select_submit' => true, 'disabled' => null,'id' => 'payment_method');
$select_opt = array(
    "Cash" => "Cash",
    "CreditCard" => "Credit Card",
    "BankTransfer" => "Bank Transfer"
);
echo '<tr><td class="label">Payment Method </td><td>' . array_selector('payment_method', null, $select_opt, $options) . '</td> </tr>';

$type_id = [];
if(isset($_POST['payment_method'])) {
    $_POST['charge'] = "0.00";
    if($_POST['payment_method'] == 'CreditCard')
        $_POST['charge'] = '0';

    $Ajax->activate('charge');

    if($_POST['payment_method'] == 'Cash') {
        $type_id = [3,0];
    }
    if($_POST['payment_method'] == 'CreditCard')
        $type_id = [2];

    if($_POST['payment_method'] == 'BankTransfer')
        $type_id = [1];

    $options = array('select_submit' => false, 'disabled' => ($_POST['payment_method'] == 'Cash'));
}

$sql = "SELECT * FROM 0_bank_accounts WHERE 1=1";
if(!empty($type_id)) {
    $type_id = implode(",",$type_id);
    $sql .= " AND account_type in ($type_id)";
}
// echo $sql;
$result = db_query($sql);
$bank_accounts = array();
while($row = db_fetch($result)) {
    $bank_accounts[$row['id']] = $row['bank_account_name'];
}
if($_POST['payment_method'] == 'Cash') {

    hidden('bank_account',$_POST['bank_account']);

    $bank = get_bank_account($_POST['bank_account']);
    label_row('Into Bank/Cash Account',$bank['bank_account_name']);

}
else {
    echo '<tr><td class="label">'._("Into Bank/Cash Account:").'</td>
    <td>' . array_selector('bank_account', null, $bank_accounts, $options) . '</td> </tr>';
}
$Ajax->activate('bank_account');
$Ajax->activate('_page_body');


//bank_accounts_list_row(_("Into Bank Account:"), 'bank_account', null, false);
/** END -- Showing Bank accounts by Type */


date_row(_("Date of Deposit:"), 'DateBanked', '', true, 0, 0, 0, null, true);

//ref_row(_("Reference:"), 'ref', '', null, '', ST_CUSTPAYMENT);

hidden('ref');

//table_section(3);


$comp_currency = get_company_currency();
$cust_currency = $_SESSION['alloc']->set_person($_POST['customer_id'], PT_CUSTOMER);
if (!$cust_currency)
    $cust_currency = $comp_currency;
$_SESSION['alloc']->currency = $bank_currency = get_bank_account_currency($_POST['bank_account']);

if ($cust_currency != $bank_currency) {
    amount_row(_("Payment Amount:"), 'bank_amount', null, '', $bank_currency);
}

//hidden('charge',$_POST['charge']);


//echo '<tr>
//
//<td class="label">Payment Method</td>
//
//<td><select id="payment_method" autocomplete="off" name="payment_method" class="combo" title="" _last="2">
//<option value="Cash">Cash</option>
//<option value="CreditCard">Credit Card</option>
//<option value="E-Dirham">E-Dirham</option>
//<option value="Cheque">Cheque</option>
//</select></td></tr>';



percent_row(_("Bank Charge:"), 'charge', null);

//$_POST['invoice_from_date']='';


set_focus('barcode');

end_outer_table(1);

div_start('alloc_tbl');
show_allocatable(false);
div_end();


start_table(TABLESTYLE, "width='60%'");

if(in_array($_SESSION['wa_current_user']->access,[9,2]) ) {

    if ($_POST['available_reward'] && $_POST['available_reward'] > 0)
        amount_row(_("Redeem reward amount:"), 'discount', null, '', $cust_currency);
    else
        hidden('discount', null);

    amount_row(_("Additional Discount:"), 'add_discount', null, '', $cust_currency);

}


if(list_updated('payment_method')) {
    $_POST['amount'] = 0;
}


amount_row(_("Amount:"), 'amount', null, '', $cust_currency);


label_row(_("Amount to be collected:"), null, null, null, null, "amount_to_be_collected");

//if ($_POST['available_reward'] && $_POST['available_reward'] > 0)
//    amount_row(_("Redeem Reward Amount:"), 'redeem_reward_amount', null, '', $cust_currency);
//else
//    hidden('redeem_reward_amount', null);

textarea_row(_("Memo:"), 'memo_', null, 22, 4);
end_table(1);


start_table(TABLESTYLE, "width='60%'");
amount_row(_("Given Amount:"), 'given_amount', null, '', $cust_currency);
amount_row(_("Balance to be given:"), 'balance_amount', null, '', $cust_currency);
end_table(1);


if ($new)
    submit_center('AddPaymentItem', _("Add Payment"), true, '', 'default');
else
    submit_center('AddPaymentItem', _("Update Payment"), true, '', 'default');

br();

end_form();
end_page();

?>

<style>
    input {
        width: 230px !important;
    }

    select[name="dimension_id"] {
        pointer-events: none;
        background: #ccc;
    }

</style>

<!--<script src="../js/jquery3.3.1.min.js"></script>-->
<script>

    $(document).on("change", "input[name='add_discount']", function () {

        var add_disc = $(this).val();
        // var payment_amount = $("input[name='amount']").val();
        var payment_amount = allocating_amount;

        // alert(payment_amount);
        //
        // var noCommas = payment_amount.replace(/,/g, ''),
        //     asANumber = +noCommas;
        // payment_amount = noCommas;

        var noCommas1 = add_disc.replace(/,/g, ''),
            asANumber = +noCommas1;
        add_disc = noCommas1;

        var coll_amt = payment_amount-add_disc;

        $("input[name='amount']").val(coll_amt.toFixed(2));

    });


    $(document).on("keyup", "input[name='given_amount']", function () {
        // alert(1)
        var given_amount = $(this).val();
        var payment_amount = $("input[name='amount']").val();


        var noCommas = payment_amount.replace(/,/g, ''),
            asANumber = +noCommas;
        payment_amount = noCommas;

        var balance = 0;
        if (payment_amount) {
            balance = given_amount - payment_amount
        }

        $("input[name='balance_amount']").val(balance.toFixed(2));
    });

    $(document).on("change", "input[name='payment_method']", function (e) {

        var method = $(this).val();
        if (method == 'CreditCard') {
            $("input[name='charge']").val("0");
        }
        else {
            $("input[name='charge']").val("");
        }

    });


    setInterval(function() {

        var payment_amount = $("input[name='amount']").val();
        var noCommas = payment_amount.replace(/,/g, ''),
            asANumber = +noCommas;
        payment_amount = noCommas;
        var coll_amount = payment_amount;
        var charge = $("input[name='charge']").val();

        if(charge) {
            charge = parseFloat(charge);
            payment_amount = parseFloat(payment_amount);
            coll_amount = payment_amount+((payment_amount*charge)/100)
        }
        $("#amount_to_be_collected").html(parseFloat(coll_amount))

    },500)



    $(document).on("click", "#all_alloc", function () {
        $('[name^="Alloc"]').click();
    });



    //CODE FOR AMER
    //BARCODE SCANNING OF INNVOICE

    $(document).on("change","input[name='barcode']", function() {

        var barcode = $(this).val();
        var cost_center = $("#dimension_id").val();
        $.ajax({
            url: "readbarcode.php",
            type: "get", //send it through get method
            data: {
                barcode: barcode,
                cost_center : cost_center
            },
            success: function(response) {
                if(response != 'false') {
                    $("#customer_id").val(response).trigger("change");

                    if(response == '1') {
                        setTimeout(function () {
                            $("#all_alloc").trigger('click');
                        },1000);

                    }

                }
                else {
                    alert("No invoice with this barcode has been found");
                }
            },
            error: function(xhr) {
            }
        });

    });




</script>
