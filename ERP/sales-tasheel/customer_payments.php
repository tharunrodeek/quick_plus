<?php
/**********************************************************************
 * Direct Axis Technology L.L.C.
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
include_once($path_to_root . "/sales-tasheel/includes/sales_db.inc");
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

page(trans($help_context = "Customer Payment Entry"), false, false, "", $js);

//----------------------------------------------------------------------------------------------

check_db_has_customers(trans("There are no customers defined in the system."));

check_db_has_bank_accounts(trans("There are no bank accounts defined in the system."));

//----------------------------------------------------------------------------------------
if (isset($_GET['customer_id'])) {
    $_POST['customer_id'] = $_GET['customer_id'];

}

if (!isset($_POST['bank_account'])) { // first page call

//    display_error(print_r(142342342222222 ,true));


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
            display_error(trans("Invalid sales invoice number."));
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

    display_notification_centered(trans("The customer payment has been successfully entered."));

    submenu_print(trans("&Print This Receipt"), ST_CUSTPAYMENT, $payment_no . "-" . ST_CUSTPAYMENT, 'prtopt');

    submenu_view(trans("&View this Customer Payment"), ST_CUSTPAYMENT, $payment_no);

    submenu_option(trans("Enter Another &Customer Payment"), "/sales-tasheel/customer_payments.php");
    submenu_option(trans("Enter Other &Deposit"), "/gl/gl_bank.php?NewDeposit=Yes");
    submenu_option(trans("Enter Payment to &Supplier"), "/purchasing/supplier_payment.php");
    submenu_option(trans("Enter Other &Payment"), "/gl/gl_bank.php?NewPayment=Yes");
    submenu_option(trans("Bank Account &Transfer"), "/gl/bank_transfer.php");

    display_note(get_gl_view_str(ST_CUSTPAYMENT, $payment_no, trans("&View the GL Journal Entries for this Customer Payment")));

    display_footer_exit();
} elseif (isset($_GET['UpdatedID'])) {
    $payment_no = $_GET['UpdatedID'];

    display_notification_centered(trans("The customer payment has been successfully updated."));

    submenu_print(trans("&Print This Receipt"), ST_CUSTPAYMENT, $payment_no . "-" . ST_CUSTPAYMENT, 'prtopt');

    display_note(get_gl_view_str(ST_CUSTPAYMENT, $payment_no, trans("&View the GL Journal Entries for this Customer Payment")));

//	hyperlink_params($path_to_root . "/sales-tasheel/allocations/customer_allocate.php", trans("&Allocate this Customer Payment"), "trans_no=$payment_no&trans_type=12");

    hyperlink_no_params($path_to_root . "/sales-tasheel/inquiry/customer_inquiry.php?", trans("Select Another Customer Payment for &Edition"));

    hyperlink_no_params($path_to_root . "/sales-tasheel/customer_payments.php", trans("Enter Another &Customer Payment"));

    display_footer_exit();
}

//----------------------------------------------------------------------------------------------

function can_process()
{
    global $Refs;

    if (!get_post('customer_id')) {
        display_error(trans("There is no customer selected."));
        set_focus('customer_id');
        return false;
    }

    if (!get_post('BranchID')) {
        display_error(trans("This customer has no branch defined."));
        set_focus('BranchID');
        return false;
    }

    if (!isset($_POST['DateBanked']) || !is_date($_POST['DateBanked'])) {
        display_error(trans("The entered date is invalid. Please enter a valid date for the payment."));
        set_focus('DateBanked');
        return false;
    } elseif (!is_date_in_fiscalyear($_POST['DateBanked'])) {
        display_error(trans("The entered date is out of fiscal year or is closed for further data entry."));
        set_focus('DateBanked');
        return false;
    }

    if (!check_reference($_POST['ref'], ST_CUSTPAYMENT, @$_POST['trans_no'])) {
        set_focus('ref');
        return false;
    }

    if (!check_num('amount', 0)) {
        display_error(trans("The entered amount is invalid or negative and cannot be processed."));
        set_focus('amount');
        return false;
    }

    if (isset($_POST['charge']) && !check_num('charge', 0)) {
        display_error(trans("The entered amount is invalid or negative and cannot be processed."));
        set_focus('charge');
        return false;
    }


    if (isset($_POST['charge']) && input_num('charge') > 100) {
        display_error(trans("Bank charges should be in percentage and should not exceed 100"));
        set_focus('charge');
        return false;
    }


    if (isset($_POST['charge']) && input_num('charge') > 0) {
        $charge_acct = get_bank_charge_account($_POST['bank_account']);
        if (get_gl_account($charge_acct) == false) {
            display_error(trans("The Bank Charge Account has not been set in System and General GL Setup."));
            set_focus('charge');
            return false;
        }
    }

    if (@$_POST['discount'] == "") {
        $_POST['discount'] = 0;
    }

    if (!check_num('discount')) {
        display_error(trans("The entered discount is not a valid number."));
        set_focus('discount');
        return false;
    }

    if (input_num('amount') <= 0) {
        display_error(trans("The balance of the amount and discount is zero or negative. Please enter valid amounts."));
        set_focus('discount');
        return false;
    }

    if (isset($_POST['bank_amount']) && input_num('bank_amount') <= 0) {
        display_error(trans("The entered payment amount is zero or negative."));
        set_focus('bank_amount');
        return false;
    }


    if ($_POST['discount'] && ($_POST['discount'] > 0) && getAvailableRewardAmount($_POST['customer_id']) < $_POST['discount']) {
        display_error(trans("Available reward amount is less than entered amount"));
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

    new_doc_date($_POST['DateBanked']);

    $new_pmt = !$_SESSION['alloc']->trans_no;
    $payment_no = write_customer_payment($_SESSION['alloc']->trans_no, $_POST['customer_id'], $_POST['BranchID'],
        $_POST['bank_account'], $_POST['DateBanked'], $_POST['ref'],
        input_num('amount'), input_num('discount'), $_POST['memo_'], 0, input_num('charge'), input_num('bank_amount', input_num('amount')), $_POST['payment_method']);

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

if ($_POST['customer_id']) {

    $_POST['available_reward'] = getAvailableRewardAmount($_POST['customer_id']);
}

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
    customer_list_row(trans("From Customer:"), 'customer_id', null, true, true);
else {
    label_cells(trans("From Customer:"), $_SESSION['alloc']->person_name, "class='label'");
    hidden('customer_id', $_POST['customer_id']);
}

if (db_customer_has_branches($_POST['customer_id'])) {

    hidden('BranchID', $_POST['customer_id']);

//	customer_branches_list_row(trans("Branch:"), $_POST['customer_id'], 'BranchID', null, false, true, true);
} else {
    hidden('BranchID', ANY_NUMERIC);
}


text_row(trans("Barcode:"), 'barcode', null, 50, 50);
date_row(trans("Invoice date from:"), 'invoice_from_date', '', true, 0, 0, 1001, null, true);
date_row(trans("Invoice date to:"), 'invoice_to_date', '', true, 0, 0, 1001, null, true);


label_row(trans("Total Available Reward Amount:"), $_POST['available_reward'], null);

if (list_updated('customer_id') || list_updated('barcode')
    || ($new && list_updated('bank_account')) || $_POST['invoice_from_date'] || $_POST['invoice_to_date']) {

//    display_error(print_r(242342342423 ,true));

    $_SESSION['alloc']->barcode = $_POST['barcode'];
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
    display_warning(trans("This customer account is on hold."));
$display_discount_percent = percent_format($_POST['pymt_discount'] * 100) . "%";

table_section(2);

table_section_title("Payment Information");

bank_accounts_list_row(trans("Into Bank Account:"), 'bank_account', null, true);

date_row(trans("Date of Deposit:"), 'DateBanked', '', true, 0, 0, 0, null, true);

ref_row(trans("Reference:"), 'ref', '', null, '', ST_CUSTPAYMENT);

//table_section(3);


$comp_currency = get_company_currency();
$cust_currency = $_SESSION['alloc']->set_person($_POST['customer_id'], PT_CUSTOMER);
if (!$cust_currency)
    $cust_currency = $comp_currency;
$_SESSION['alloc']->currency = $bank_currency = get_bank_account_currency($_POST['bank_account']);

if ($cust_currency != $bank_currency) {
    amount_row(trans("Payment Amount:"), 'bank_amount', null, '', $bank_currency);
}

//hidden('charge',$_POST['charge']);




echo '<tr>

<td class="label">Card Type</td>

<td><select id="card_type" autocomplete="off" name="card_type" class="combo" title="" _last="2">
<option value="0">Select</option>
<option value="1">Customer Card</option>
<option value="2">Center Card</option>
</select></td></tr>';


echo '<tr class="center_cards">

<td class="label">Center Cards</td>

<td><select id="center_cards" autocomplete="off" name="card_type" class="combo" title="" _last="2">
<option value="0">Select</option>
<option value="1">E-Dirham 1</option>
<option value="2">E-Dirham 2</option>
<option value="3">E-Dirham 3</option>
<option value="4">E-Dirham 4</option>
</select></td></tr>';




echo '<tr>

<td class="label">Payment Method</td>

<td><select id="payment_method" autocomplete="off" name="payment_method" class="combo" title="" _last="2">
<option value="Cash">Cash</option>
<option value="CreditCard">Credit Card</option>
<option value="E-Dirham">E-Dirham</option>
<option value="Cheque">Cheque</option>
</select></td></tr>';


percent_row(trans("Bank Charge:"), 'charge', null);

//$_POST['invoice_from_date']='';


set_focus('barcode');

end_outer_table(1);

div_start('alloc_tbl');
show_allocatable(false);
div_end();


start_table(TABLESTYLE, "width='60%'");

//label_row(trans("Customer prompt payment discount :"), $display_discount_percent);



if ($_POST['available_reward'] && $_POST['available_reward'] > 0)
    amount_row(trans("Redeem reward amount:"), 'discount', null, '', $cust_currency);
else
    hidden('discount', null);

amount_row(trans("Amount:"), 'amount', null, '', $cust_currency);

//if ($_POST['available_reward'] && $_POST['available_reward'] > 0)
//    amount_row(trans("Redeem Reward Amount:"), 'redeem_reward_amount', null, '', $cust_currency);
//else
//    hidden('redeem_reward_amount', null);

textarea_row(trans("Memo:"), 'memo_', null, 22, 4);
end_table(1);


start_table(TABLESTYLE, "width='60%'");
amount_row(trans("Given Amount:"), 'given_amount', null, '', $cust_currency);
amount_row(trans("Balance to be given:"), 'balance_amount', null, '', $cust_currency);
end_table(1);


if ($new)
    submit_center('AddPaymentItem', trans("Add Payment"), true, '', 'default');
else
    submit_center('AddPaymentItem', trans("Update Payment"), true, '', 'default');

br();

end_form();
end_page();

?>

<script src="../js/jquery3.3.1.min.js"></script>
<script>

    $(document).ready(function (e) {
        $("#card_type").trigger("change");
    });

    $(document).on("keyup", "input[name='given_amount']", function () {
        // alert(1)
        var given_amount = $(this).val();
        var payment_amount = $("input[name='amount']").val();

        var balance = 0;
        if (payment_amount) {
            balance = given_amount - payment_amount
        }

        $("input[name='balance_amount']").val(balance.toFixed(2));
    });

    $(document).on("change", "#payment_method", function (e) {

        var method = $(this).val();
        if (method == 'CreditCard') {
            $("input[name='charge']").val("2.00");
        }
        else {
            $("input[name='charge']").val("");
        }

    });


    $(document).on("change", "#card_type", function (e) {

            var value = $(this).val();
            if(value == '2') {
                $(".center_cards").show();
            }
            else {
                $(".center_cards").hide();
            }

    });


    //     $(document).on("change", "input[name='invoice_from_date']", function () {
    //
    // alert(1);
    //
    //     });


</script>
