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
//-----------------------------------------------------------------------------
//
//	Entry/Modify Sales Quotations
//	Entry/Modify Sales Order
//	Entry Direct Delivery
//	Entry Direct Invoice
//

$path_to_root = "..";
$page_security = 'SA_SALESORDER';

include_once($path_to_root . "/sales/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/sales/includes/ui/sales_order_ui.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_types_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

set_page_security(@$_SESSION['Items']->trans_type,
    array(ST_SALESORDER => 'SA_SALESORDER',
        ST_SALESQUOTE => 'SA_SALESQUOTE',
        ST_CUSTDELIVERY => 'SA_SALESDELIVERY',
        ST_SALESINVOICE => 'SA_SALESINVOICE'),
    array('NewOrder' => 'SA_SALESORDER',
        'ModifyOrderNumber' => 'SA_SALESORDER',
        'AddedID' => 'SA_SALESORDER',
        'UpdatedID' => 'SA_SALESORDER',
        'NewQuotation' => 'SA_SALESQUOTE',
        'ModifyQuotationNumber' => 'SA_SALESQUOTE',
        'NewQuoteToSalesOrder' => 'SA_SALESQUOTE',
        'AddedQU' => 'SA_SALESQUOTE',
        'UpdatedQU' => 'SA_SALESQUOTE',
        'NewDelivery' => 'SA_SALESDELIVERY',
        'AddedDN' => 'SA_SALESDELIVERY',
        'NewInvoice' => 'SA_SALESINVOICE',
        'AddedDI' => 'SA_SALESINVOICE'
    )
);

$js = '';

$isEditingOldInvoice = $_GET['NewInvoice'] != '0' && ($_GET['EditFlag'] && $_GET['EditFlag'] == 'true');

if ($SysPrefs->use_popup_windows) {
    $js .= get_js_open_window(1300, 720);
}

if (user_use_date_picker()) {
    $js .= get_js_date_picker();
}

if (isset($_GET['NewInvoice']) && is_numeric($_GET['NewInvoice'])) {

//    display_error(12121);

    create_cart(ST_SALESINVOICE, $_GET['NewInvoice']);

    if (isset($_GET['FixedAsset'])) {
        $_SESSION['page_title'] = trans($help_context = "Fixed Assets Sale");
        $_SESSION['Items']->fixed_asset = true;
    } else
        $_SESSION['page_title'] = trans($help_context = "Direct Sales Invoice");


    $_SESSION['Items']->dim_id = $_GET['dim_id'];
    $_SESSION['Items']->token_number = $_GET['SRQ_TOKEN'];
    $_SESSION['Items']->service_req_id = $_GET['req_id'];

    /**
     * Daxis : Bipin
     * 10-July-2018
     * If Invoice is Edit Mode
     */
    if ($isEditingOldInvoice) {
        $page_security = 'SA_EDITSALESINVOICE';
        $_SESSION['Items']->edit_invoice = true;
        $_SESSION['Items']->sales_order_no = $_GET['NewInvoice'];
        $_SESSION['page_title'] = trans($help_context = "Edit Sales Invoice");
    }
    /** End */


}

//elseif (isset($_GET['ModifyOrderNumber']) && is_numeric($_GET['ModifyOrderNumber'])) {
//
//    $help_context = 'Modifying Sales Order';
//    $_SESSION['page_title'] = sprintf(trans("Modifying Sales Order # %d"), $_GET['ModifyOrderNumber']);
//    create_cart(ST_SALESORDER, $_GET['ModifyOrderNumber']);
//
//} elseif (isset($_GET['ModifyQuotationNumber']) && is_numeric($_GET['ModifyQuotationNumber'])) {
//
//    $help_context = 'Modifying Sales Quotation';
//    $_SESSION['page_title'] = sprintf(trans("Modifying Sales Quotation # %d"), $_GET['ModifyQuotationNumber']);
//    create_cart(ST_SALESQUOTE, $_GET['ModifyQuotationNumber']);
//
//} elseif (isset($_GET['NewOrder'])) {
//
//    $_SESSION['page_title'] = trans($help_context = "New Sales Order Entry");
//    create_cart(ST_SALESORDER, 0);
//} elseif (isset($_GET['NewQuotation'])) {
//
//    $_SESSION['page_title'] = trans($help_context = "New Sales Quotation Entry");
//    create_cart(ST_SALESQUOTE, 0);
//} elseif (isset($_GET['NewQuoteToSalesOrder'])) {
//    $_SESSION['page_title'] = trans($help_context = "Sales Order Entry");
//    create_cart(ST_SALESQUOTE, $_GET['NewQuoteToSalesOrder']);
//}


page($_SESSION['page_title'], false, false, "", $js);

if ($isEditingOldInvoice && isset($_SESSION['Items']->editing_invoice_no)){
    check_is_editable(ST_SALESINVOICE, $_SESSION['Items']->editing_invoice_no);
}
if (isset($_GET['ModifyOrderNumber']) && is_prepaid_order_open($_GET['ModifyOrderNumber'])) {
    display_error(trans("This order cannot be edited because there are invoices or payments related to it, and prepayment terms were used."));
    end_page();
    exit;
}
if (isset($_GET['ModifyOrderNumber']))
    check_is_editable(ST_SALESORDER, $_GET['ModifyOrderNumber']);
elseif (isset($_GET['ModifyQuotationNumber']))
    check_is_editable(ST_SALESQUOTE, $_GET['ModifyQuotationNumber']);

//-----------------------------------------------------------------------------

if (list_updated('branch_id')) {

    // when branch is selected via external editor also customer can change
    $br = get_branch(get_post('branch_id'));
    $_POST['customer_id'] = $br['debtor_no'];
    $Ajax->activate('customer_id');
}


if (isset($_SESSION['Items']->dim_id)) {
    if (empty($_SESSION['Items']->edit_invoice) || $_SESSION['Items']->edit_invoice !== true) {
        $dimension_id = $_SESSION['Items']->dim_id;
        $_POST['ref'] = get_next_invoice_ref($dimension_id);
    }
}


if (isset($_GET['AddedID'])) {
    $order_no = $_GET['AddedID'];
    display_notification_centered(sprintf(trans("Order # %d has been entered."), $order_no));

    submenu_view(trans("&View This Order"), ST_SALESORDER, $order_no);

    submenu_print(trans("&Print This Order"), ST_SALESORDER, $order_no, 'prtopt');
    submenu_print(trans("&Email This Order"), ST_SALESORDER, $order_no, null, 1);
    set_focus('prtopt');

    submenu_option(trans("Make &Delivery Against This Order"),
        "/sales/customer_delivery.php?OrderNumber=$order_no");

    submenu_option(trans("Work &Order Entry"), "/manufacturing/work_order_entry.php?");

    submenu_option(trans("Enter a &New Order"), "/sales/sales_order_entry.php?NewOrder=0");

    display_footer_exit();

} elseif (isset($_GET['UpdatedID'])) {
    $order_no = $_GET['UpdatedID'];

    display_notification_centered(sprintf(trans("Order # %d has been updated."), $order_no));

    submenu_view(trans("&View This Order"), ST_SALESORDER, $order_no);

    submenu_print(trans("&Print This Order"), ST_SALESORDER, $order_no, 'prtopt');
    submenu_print(trans("&Email This Order"), ST_SALESORDER, $order_no, null, 1);
    set_focus('prtopt');

    submenu_option(trans("Confirm Order Quantities and Make &Delivery"),
        "/sales/customer_delivery.php?OrderNumber=$order_no");

    submenu_option(trans("Select A Different &Order"),
        "/sales/inquiry/sales_orders_view.php?OutstandingOnly=1");

    display_footer_exit();

} elseif (isset($_GET['AddedQU'])) {
    $order_no = $_GET['AddedQU'];
    display_notification_centered(sprintf(trans("Quotation # %d has been entered."), $order_no));

    submenu_view(trans("&View This Quotation"), ST_SALESQUOTE, $order_no);

    submenu_print(trans("&Print This Quotation"), ST_SALESQUOTE, $order_no, 'prtopt');
    submenu_print(trans("&Email This Quotation"), ST_SALESQUOTE, $order_no, null, 1);
    set_focus('prtopt');

    submenu_option(trans("Make &Sales Order Against This Quotation"),
        "/sales/sales_order_entry.php?NewQuoteToSalesOrder=$order_no");

    submenu_option(trans("Enter a New &Quotation"), "/sales/sales_order_entry.php?NewQuotation=0");

    display_footer_exit();

} elseif (isset($_GET['UpdatedQU'])) {
    $order_no = $_GET['UpdatedQU'];

    display_notification_centered(sprintf(trans("Quotation # %d has been updated."), $order_no));

    submenu_view(trans("&View This Quotation"), ST_SALESQUOTE, $order_no);

    submenu_print(trans("&Print This Quotation"), ST_SALESQUOTE, $order_no, 'prtopt');
    submenu_print(trans("&Email This Quotation"), ST_SALESQUOTE, $order_no, null, 1);
    set_focus('prtopt');

    submenu_option(trans("Make &Sales Order Against This Quotation"),
        "/sales/sales_order_entry.php?NewQuoteToSalesOrder=$order_no");

    submenu_option(trans("Select A Different &Quotation"),
        "/sales/inquiry/sales_orders_view.php?type=" . ST_SALESQUOTE);

    display_footer_exit();
} elseif (isset($_GET['AddedDN'])) {
    $delivery = $_GET['AddedDN'];

    display_notification_centered(sprintf(trans("Delivery # %d has been entered."), $delivery));

    submenu_view(trans("&View This Delivery"), ST_CUSTDELIVERY, $delivery);

    submenu_print(trans("&Print Delivery Note"), ST_CUSTDELIVERY, $delivery, 'prtopt');
    submenu_print(trans("&Email Delivery Note"), ST_CUSTDELIVERY, $delivery, null, 1);
    submenu_print(trans("P&rint as Packing Slip"), ST_CUSTDELIVERY, $delivery, 'prtopt', null, 1);
    submenu_print(trans("E&mail as Packing Slip"), ST_CUSTDELIVERY, $delivery, null, 1, 1);
    set_focus('prtopt');

    display_note(get_gl_view_str(ST_CUSTDELIVERY, $delivery, trans("View the GL Journal Entries for this Dispatch")), 0, 1);

    submenu_option(trans("Make &Invoice Against This Delivery"),
        "/sales/customer_invoice.php?DeliveryNumber=$delivery");

    if ((isset($_GET['Type']) && $_GET['Type'] == 1))
        submenu_option(trans("Enter a New Template &Delivery"),
            "/sales/inquiry/sales_orders_view.php?DeliveryTemplates=Yes");
    else
        submenu_option(trans("Enter a &New Delivery"),
            "/sales/sales_order_entry.php?NewDelivery=0");

    display_footer_exit();

} elseif (isset($_GET['AddedDI'])) {
    $invoice = $_GET['AddedDI'];

    if (isset($_GET['EditedDI'])) {
        display_notification_centered(sprintf(trans("Invoice # %s has been updated."), $_GET['EditedDI']));
    } else {
        $invoice_info = get_customer_trans($invoice, ST_SALESINVOICE);
        display_notification_centered(sprintf(trans("Invoice # %s has been entered."), $invoice_info['reference']));
    }

    submenu_view(trans("&View This Invoice"), ST_SALESINVOICE, $invoice);
    submenu_print(trans("&Print Sales Invoice"), ST_SALESINVOICE, $invoice . "-" . ST_SALESINVOICE, 'prtopt');
//     submenu_print(trans("&Email Sales Invoice"), ST_SALESINVOICE, $invoice . "-" . ST_SALESINVOICE, null, 1);


    submenu_print(_("&Email Sales Invoice"), ST_SALESINVOICE, $invoice."-".ST_SALESINVOICE, null, 1);


    set_focus('prtopt');


    //Open invoice pdf automaticallydfdf
    echo "<script>var print_url = $(\"#inv_print\").attr(\"href\"); window.open(print_url+'&reprint=false','Invoice print','300','300');</script>";

    $row = db_fetch(get_allocatable_from_cust_transactions(null, $invoice, ST_SALESINVOICE));


    if ($row !== false)
        submenu_print(trans("Print &Receipt"), $row['type'], $row['trans_no'] . "-" . $row['type'], 'prtopt');

    display_note(get_gl_view_str(ST_SALESINVOICE, $invoice, trans("View the GL &Journal Entries for this Invoice")), 0, 1);

    // if ((isset($_GET['Type']) && $_GET['Type'] == 1))
    //     submenu_option(trans("Enter a &New Template Invoice"),
    //         "/sales/inquiry/sales_orders_view.php?InvoiceTemplates=Yes");
    // else
    //     submenu_option(trans("Enter a &New Direct Invoice"),
    //         "/sales/sales_order_entry.php?NewInvoice=0");

    // if ($row === false)
    //     submenu_option(trans("Add an Attachment"), "/admin/attachments.php?filterType=" . ST_SALESINVOICE . "&trans_no=$invoice");

    display_footer_exit();
} else {
    check_edit_conflicts(get_post('cart_id'));
}
//-----------------------------------------------------------------------------

function copy_to_cart()
{

    global $global_pay_types_array;

    $cart = &$_SESSION['Items'];

    $cart->trans_type = ST_SALESINVOICE;

    $cart->reference = $_POST['ref'];
    $cart->Comments = $_POST['Comments'];
    $cart->document_date = $_POST['OrderDate'];
    $cart->display_customer = $_POST['display_customer'];
    $cart->customer_trn = $_POST['customer_trn'];
    $cart->customer_mobile = $_POST['customer_mobile'];
    $cart->customer_email = $_POST['customer_email'];
    $cart->customer_ref = $_POST['customer_ref'];

    $newpayment = false;

    if($cart->edit_invoice) {
        $pay_type = array_search($cart->pay_type, $global_pay_types_array);
        if (!empty($pay_type))
            $cart->pay_type = $pay_type;
    }
    else {
        $cart->pay_type = $_POST['invoice_payment'];
    }


    if ($_POST['invoice_payment'] == 'PayNow')
    $_POST['payment'] = 7;


    if (isset($_POST['payment']) && ($cart->payment != $_POST['payment'])) {
        $cart->payment = $_POST['payment'];
        $cart->payment_terms = get_payment_terms($_POST['payment']);
        $newpayment = true;
    }
    if ($cart->payment_terms['cash_sale']) {
        if ($newpayment) {
            $cart->due_date = $cart->document_date;
            $cart->phone = $cart->cust_ref = $cart->delivery_address = '';
            $cart->ship_via = 0;
            $cart->deliver_to = '';
            $cart->prep_amount = 0;
        }
    } else {
        $cart->due_date = $_POST['delivery_date'];
        $cart->cust_ref = $_POST['cust_ref'];
        $cart->deliver_to = $_POST['deliver_to'];
        $cart->delivery_address = $_POST['delivery_address'];
        $cart->phone = $_POST['phone'];
        $cart->ship_via = $_POST['ship_via'];
        if (!$cart->trans_no || ($cart->trans_type == ST_SALESORDER && !$cart->is_started()))
            $cart->prep_amount = input_num('prep_amount', 0);
    }
    $cart->Location = $_POST['Location'];
    $cart->freight_cost = input_num('freight_cost');
    if (isset($_POST['email']))
        $cart->email = $_POST['email'];
    else
        $cart->email = '';
    $cart->customer_id = $_POST['customer_id'];
    $cart->Branch = $_POST['branch_id'];
    $cart->sales_type = $_POST['sales_type'];

    if ($cart->trans_type != ST_SALESORDER && $cart->trans_type != ST_SALESQUOTE) { // 2008-11-12 Joe Hunt
        $cart->dimension_id = $_POST['dimension_id'];
        $cart->dimension2_id = $_POST['dimension2_id'];

    }
    $cart->ex_rate = input_num('_ex_rate', null);

}

//-----------------------------------------------------------------------------

function copy_from_cart()
{
    $cart = &$_SESSION['Items'];

    $_POST['ref'] = $cart->reference;
    $_POST['Comments'] = $cart->Comments;

    $_POST['OrderDate'] = $cart->document_date;
    $_POST['delivery_date'] = $cart->due_date;
    $_POST['cust_ref'] = $cart->cust_ref;
    $_POST['freight_cost'] = price_format($cart->freight_cost);

    $_POST['deliver_to'] = $cart->deliver_to;
    $_POST['delivery_address'] = $cart->delivery_address;
    $_POST['phone'] = $cart->phone;
    $_POST['Location'] = $cart->Location;
    $_POST['ship_via'] = $cart->ship_via;

    $_POST['customer_id'] = $cart->customer_id;
    $_POST['branch_id'] = $cart->Branch;
    $_POST['sales_type'] = $cart->sales_type;
    $_POST['prep_amount'] = price_format($cart->prep_amount);
    // POS
    $_POST['payment'] = $cart->payment;
    $_POST['invoice_payment'] = array_flip($GLOBALS['global_pay_types_array'])[$cart->pay_type];
    if ($cart->trans_type != ST_SALESORDER && $cart->trans_type != ST_SALESQUOTE) { // 2008-11-12 Joe Hunt
        $_POST['dimension_id'] = $cart->dimension_id;
        $_POST['dimension2_id'] = $cart->dimension2_id;

    }
    $_POST['cart_id'] = $cart->cart_id;
    $_POST['_ex_rate'] = $cart->ex_rate;
}

//--------------------------------------------------------------------------------

function line_start_focus()
{
    global $Ajax;

    $Ajax->activate('items_table');
    set_focus('_stock_id_edit');
}

//--------------------------------------------------------------------------------
function can_process()
{

    global $Refs, $SysPrefs;

    copy_to_cart();

    if (!get_post('customer_id')) {
        display_error(trans("There is no customer selected."));
        set_focus('customer_id');
        return false;
    }

    if(!empty(get_post('round_of_amount'))){

        $round_of_amt = get_post('round_of_amount');

        if(abs($round_of_amt) > 0.99) {
            display_error(trans("Round of amount shouldn't be greater than 0.99 OR greater than -0.99"));
            set_focus('round_of_amount');
            return false;
        }

    }


    if (empty($_SESSION['Items']->pay_type)) {
        display_error(trans("Please select a payment method."));
        set_focus('invoice_payment');
        return false;
    }


    if($_SESSION['Items']->pay_type == "PayCashAndCard") {

        $cash_amount = input_num('cash_amount');
        $card_amount = input_num('card_amount');

        if (empty($cash_amount) && empty($card_amount)) {
            display_error(trans("Please input CASH PAYMENT AMOUNT and CARD PAYMENT AMOUNT."));
            return false;
        }
    }


    if($_SESSION['Items']->pay_type == "PayForStaffMistake" && empty($_POST['mistake_staff_id'])) {

        display_error(trans("Please choose a staff ( Staff Mistake )."));
        return false;

    }

    $payment_accounts = [
        'PayNow'            => empty($SysPrefs->prefs['dflt_csh_pmt_act']) 
            ? $_SESSION['wa_current_user']->cashier_account
            : $SysPrefs->prefs['dflt_csh_pmt_act'],
        'PayNoWCC'          => $SysPrefs->prefs['dflt_credit_card_pmt_act'],
        'PayByBankTransfer' => $SysPrefs->prefs['dflt_bnk_trnsfr_pmt_act'],
        'PayByCustomerCard' => $SysPrefs->prefs['customer_card_act']
    ];

    foreach($payment_accounts as $bnk_acc){
        if(empty($bnk_acc)) {
            display_error('Please configure the payment accounts before accepting any payments');
            return false;
        }
    }

    if($_SESSION['Items']->pay_type == "PayNoWCC" && $_SESSION['Items']->edit_invoice !== true) {

        $credit_card_no = $_POST['credit_card_no'];

        /*$error = "";
        if(empty($_POST['credit_card_no']) || strlen($credit_card_no) != 4){
            $error = "Please Enter Last 4 Digit Credit Card Number";
        }

        if(!empty($error)) {
            display_error(trans($error));
            return false;
        }*/

    }



    if (empty(get_post('display_customer'))) {
        display_error(trans("Please enter display customer/Company name."));
        set_focus('display_customer');
        return false;
    }

    if (get_post('display_customer') == "Walk-in Customer") {
        display_error(trans("Company/DisplayCustomer name should not be as Walk-in Customer."));
        set_focus('display_customer');
        return false;
    }


    if (empty(get_post('customer_mobile'))) {
        display_error(trans("Please enter customer mobile number"));
        set_focus('customer_mobile');
        return false;
    }

//    if (empty(get_post('contact_person')) && !in_array($_SESSION['Items']->dim_id, [DT_TYPING])) {
//        display_error(trans("Please enter the contact person."));
//        set_focus('contact_person');
//        return false;
//    }

    if (!get_post('branch_id')) {
        display_error(trans("This customer has no branch defined."));
        set_focus('branch_id');
        return false;
    }

    if (!is_date($_POST['OrderDate'])) {
        display_error(trans("The entered date is invalid."));
        set_focus('OrderDate');
        return false;
    }
    if ($_SESSION['Items']->trans_type != ST_SALESORDER && $_SESSION['Items']->trans_type != ST_SALESQUOTE && !is_date_in_fiscalyear($_POST['OrderDate'])) {
        display_error(trans("The entered date is out of fiscal year or is closed for further data entry."));
        set_focus('OrderDate');
        return false;
    }
    if (count($_SESSION['Items']->line_items) == 0) {
        display_error(trans("You must enter at least one non empty item line."));
        set_focus('AddItem');
        return false;
    }
    if (!$SysPrefs->allow_negative_stock() && ($low_stock = $_SESSION['Items']->check_qoh())) {
        display_error(trans("This document cannot be processed because there is insufficient quantity for items marked."));
        return false;
    }
    if ($_SESSION['Items']->payment_terms['cash_sale'] == 0) {
        if (!$_SESSION['Items']->is_started() && ($_SESSION['Items']->payment_terms['days_before_due'] == -1) && ((input_num('prep_amount') <= 0) ||
                input_num('prep_amount') > $_SESSION['Items']->get_trans_total())) {
            display_error(trans("Pre-payment required have to be positive and less than total amount."));
            set_focus('prep_amount');
            return false;
        }
        if (strlen($_POST['deliver_to']) <= 1) {
            display_error(trans("You must enter the person or company to whom delivery should be made to."));
            set_focus('deliver_to');
            return false;
        }

        if ($_SESSION['Items']->trans_type != ST_SALESQUOTE && strlen($_POST['delivery_address']) <= 1) {
            // display_error( trans("You should enter the street address in the box provided. Orders cannot be accepted without a valid street address."));
            // set_focus('delivery_address');
            // return false;
        }

        if ($_POST['freight_cost'] == "")
            $_POST['freight_cost'] = price_format(0);

        if (!check_num('freight_cost', 0)) {
            display_error(trans("The shipping cost entered is expected to be numeric."));
            set_focus('freight_cost');
            return false;
        }
        if (!is_date($_POST['delivery_date'])) {
            if ($_SESSION['Items']->trans_type == ST_SALESQUOTE)
                display_error(trans("The Valid date is invalid."));
            else
                display_error(trans("The delivery date is invalid."));
            set_focus('delivery_date');
            return false;
        }
        if (date1_greater_date2($_POST['OrderDate'], $_POST['delivery_date'])) {
            if ($_SESSION['Items']->trans_type == ST_SALESQUOTE)
                display_error(trans("The requested valid date is before the date of the quotation."));
            else
                display_error(trans("The requested delivery date is before the date of the order."));
            set_focus('delivery_date');
            return false;
        }
    } else {
        if (!db_has_cash_accounts()) {
            display_error(trans("You need to define a cash account for your Sales Point."));
            return false;
        }
    }


    $empty_trans_id = false;
    foreach ($_SESSION['Items']->line_items as $row) {

        $item_info = get_item($row->stock_id);

        if (in_array($item_info['category_id'], [6])) {

            if (empty(trim($row->transaction_id)))
                $empty_trans_id = true;

        }

    }

    if ($empty_trans_id) {
        // display_error(trans("Transaction is mandatory for all line items"));
        // return false;
    }


    //DUPLICATE_TRANSACTION_ID
    $transaction_ids = [];
    $duplicate = false;
    foreach ($_SESSION['Items']->line_items as $row) {
        if ($row->transaction_id && $row->transaction_id != '' && strtolower($row->transaction_id) != 'na') {

            if (in_array(db_escape($row->transaction_id), $transaction_ids)) {
                $duplicate = true;
            }
            array_push($transaction_ids, db_escape($row->transaction_id));
        }
    }

    if ($duplicate) {
        display_error(trans("Duplicate Transaction ID found on same invoice"));
        return false;
    }

    if (!empty($transaction_ids)) {
        $transaction_ids = implode(",", $transaction_ids);

        $sql = "SELECT COUNT(a.id) FROM " . TB_PREF . "debtor_trans_details a LEFT JOIN 
        " . TB_PREF . "debtor_trans b ON b.trans_no=a.debtor_trans_no WHERE a.transaction_id in ($transaction_ids) AND b.type=10 AND a.quantity <> 0 AND LOWER(a.transaction_id) <> 'na' ";

        if ($_SESSION['Items']->edit_invoice) {
            $sql .= " AND b.reference != " . db_escape($_POST['ref']);
        }

        if (check_duplicates_row($sql)) {
            display_error(trans("Duplicate Transaction ID found"));
            return false;
        }
    }


    if (!$Refs->is_valid($_POST['ref'], $_SESSION['Items']->trans_type)) {

        // display_error(trans("You must enter a reference."));
        // set_focus('ref');
        // return false;
    }

    if (!db_has_currency_rates($_SESSION['Items']->customer_currency, $_POST['OrderDate']))
        return false;

    if ($_SESSION['Items']->get_items_total() < 0) {
        display_error("Invoice total amount cannot be less than zero.");
        return false;
    }


    if (empty(trim(get_post('invoice_type')))) {
        display_error("Please select payment type.");
        return false;
    }


    if (get_post('invoice_type') == 'Credit') {

        if (!check_credit_limit_validation()) {
            display_error("Credit Limit Exceeded. Please Request to Administrator.
            <button type=\"button\" class=\"req_credit_button\">Request</button></td>");
            return false;
        }

    }


    if (get_post('invoice_payment') == 'PayLater') {

        if (!check_credit_limit_validation()) {
            display_error("Credit Limit Exceeded");
            return false;
        }

    }


    return true;
}


function check_credit_limit_validation()
{
    global $path_to_root;

    return true;

    if (get_post('invoice_payment') != 'PayLater' || $_POST['customer_type'] == "PRO") {

        return true;

    }

    //pp(123123123123);


    //return false;

    $customer_id = get_post('customer_id');
    $available_credit_limit = $_SESSION['Items']->credit;
    $total_invoice_amount = $_SESSION['Items']->get_items_trans_total();


    include_once $path_to_root . "/API/API_Call.php";
    $api = new API_Call();

    $current_cust_bal_info = $api->get_customer_balance($customer_id, 'array');
    $current_cust_bal = $current_cust_bal_info['customer_balance'];


    // $available_credit_limit += $current_cust_bal;


    if ($available_credit_limit < $total_invoice_amount)
        return false;


    return true;//

}

//-----------------------------------------------------------------------------

if (isset($_POST['update'])) {
    copy_to_cart();
    $Ajax->activate('items_table');
}

if (isset($_POST['ProcessOrder']) && can_process()) {

    $modified = ($_SESSION['Items']->trans_no != 0);
    $so_type = $_SESSION['Items']->so_type;

    $ret = $_SESSION['Items']->write(1);
    if ($ret == -1) {


        display_error(trans("The entered reference is already in use."));
        // $ref = $Refs->get_next($_SESSION['Items']->trans_type, null, array('date' => Today()));
        $ref = get_next_invoice_ref($_SESSION['Items']->dim_id);

        if ($ref != $_SESSION['Items']->reference) {
            unset($_POST['ref']); // force refresh reference
            display_error(trans("The reference number field has been increased. Please save the document again."));
        }
        set_focus('ref');
    } else {


        if (count($messages)) { // abort on failure or error messages are lost
            $Ajax->activate('_page_body');
            display_footer_exit();
        }
        $trans_no = key($_SESSION['Items']->trans_no);
        $trans_type = $_SESSION['Items']->trans_type;
        new_doc_date($_SESSION['Items']->document_date);

        $edited_invoice = ($_SESSION['Items']->edit_invoice && $_SESSION['Items']->edit_invoice == true) ? $_SESSION['Items']->reference : false;

        processing_end();
        if ($modified) {
            if ($trans_type == ST_SALESQUOTE)
                meta_forward($_SERVER['PHP_SELF'], "UpdatedQU=$trans_no");
            else
                meta_forward($_SERVER['PHP_SELF'], "UpdatedID=$trans_no");
        } elseif ($trans_type == ST_SALESORDER) {
            meta_forward($_SERVER['PHP_SELF'], "AddedID=$trans_no");
        } elseif ($trans_type == ST_SALESQUOTE) {
            meta_forward($_SERVER['PHP_SELF'], "AddedQU=$trans_no");
        } elseif ($trans_type == ST_SALESINVOICE) {

            if ($edited_invoice)
                meta_forward($_SERVER['PHP_SELF'], "AddedDI=$trans_no&Type=$so_type&EditedDI=" . urlencode($edited_invoice));
            else
                meta_forward($_SERVER['PHP_SELF'], "AddedDI=$trans_no&Type=$so_type");

        } else {

            meta_forward($_SERVER['PHP_SELF'], "AddedDN=$trans_no&Type=$so_type");
        }
    }
}

//--------------------------------------------------------------------------------

function check_item_data()
{
    global $SysPrefs;

    $is_inventory_item = is_inventory_item(get_post('stock_id'));
    if (!get_post('stock_id_text', true)) {
        display_error(trans("Item description cannot be empty."));
        set_focus('stock_id_edit');
        return false;
    } elseif (!check_num('qty', 0) || !check_num('Disc', 0, 100)) {
        display_error(trans("The item could not be updated because you are attempting to set the quantity ordered to less than 0, or the discount percent to more than 100."));
        set_focus('qty');
        return false;
    } elseif (!check_num('price', 0) && (!$SysPrefs->allow_negative_prices() || $is_inventory_item)) {
        display_error(trans("Price for inventory item must be entered and can not be less than 0"));
        set_focus('price');
        return false;
    } elseif (isset($_POST['LineNo']) && isset($_SESSION['Items']->line_items[$_POST['LineNo']])
        && !check_num('qty', $_SESSION['Items']->line_items[$_POST['LineNo']]->qty_done)) {

        set_focus('qty');
        display_error(trans("You attempting to make the quantity ordered a quantity less than has already been delivered. The quantity delivered cannot be modified retrospectively."));
        return false;
    }

    $cost_home = get_unit_cost(get_post('stock_id')); // Added 2011-03-27 Joe Hunt
    $cost = $cost_home / get_exchange_rate_from_home_currency($_SESSION['Items']->customer_currency, $_SESSION['Items']->document_date);
    if (input_num('price') < $cost) {
        $dec = user_price_dec();
        $curr = $_SESSION['Items']->customer_currency;
        $price = number_format2(input_num('price'), $dec);
        if ($cost_home == $cost)
            $std_cost = number_format2($cost_home, $dec);
        else {
            $price = $curr . " " . $price;
            $std_cost = $curr . " " . number_format2($cost, $dec);
        }
        display_warning(sprintf(trans("Price %s is below Standard Cost %s"), $price, $std_cost));
    }
    return true;
}


function check_duplicate_transaction_id($transaction_ids)
{

    $sql = "SELECT COUNT(a.id),b.reference FROM " . TB_PREF . "debtor_trans_details a LEFT JOIN 
        " . TB_PREF . "debtor_trans b ON b.trans_no=a.debtor_trans_no 
        WHERE a.transaction_id in ($transaction_ids) AND b.type=10 AND a.quantity <> 0 AND transaction_id <> '' AND LOWER(transaction_id) <> 'na'";


    if ($_SESSION['Items']->edit_invoice) {
        $sql .= " AND b.reference != " . db_escape($_POST['ref']);
    }

    $result = db_query($sql);

    $row = db_fetch($result);

    if ($row['reference'] != "") {
        return $row;
    }
    return false;

}


function check_duplicate_application_id($application_ids)
{

    $sql = "SELECT COUNT(a.id),b.reference FROM " . TB_PREF . "debtor_trans_details a LEFT JOIN 
        " . TB_PREF . "debtor_trans b ON b.trans_no=a.debtor_trans_no 
        WHERE a.application_id in ($application_ids) AND b.type=10 AND a.quantity <> 0 AND application_id <> '' AND LOWER(application_id) <> 'na'";


    if ($_SESSION['Items']->edit_invoice) {
        $sql .= " AND b.reference != " . db_escape($_POST['ref']);
    }

    $result = db_query($sql);

    $row = db_fetch($result);

    if ($row['reference'] != "") {
        return $row;
    }
    return false;

}

//--------------------------------------------------------------------------------

function handle_update_item()
{

//    /** For TAMAAM */
    $item_info = get_item(get_post('stock_id'));
    $govt_fee = input_num('govt_fee');
//    if($item_info['category_id'] == IMMIGRATION_CATEGORY) {
//        $govt_fee = input_num('govt_fee');
//        if(empty($govt_fee)) {
//            display_error("Govt. Fee Should not be empty for Immigration Category");
//            return false;
//        }
//        $govt_fee  = $govt_fee - input_num('price');
//    }
//    /** END -- For TAMAAM */

    if (empty(trim(get_post('invoice_type')))) {
        display_error("Please select payment type.");
        return false;
    }

    if (empty(trim(get_post('transaction_id')))
        && !in_array($_SESSION['Items']->dim_id, [DT_TYPING,DT_AMER])
        && !in_array($_SESSION['wa_current_user']->access, [48])
        && !in_array($_SESSION['wa_current_user']->user, [118])
    ) {
        display_error("Bank Reference Number should not be empty");
        return false;
    }

    if (empty(trim(get_post('application_id')))
        && !in_array($_SESSION['Items']->dim_id, [DT_TYPING])
        && !in_array($_SESSION['wa_current_user']->access, [48])
        && !in_array($_SESSION['wa_current_user']->user, [118])
    ) {
        display_error("Application ID should not be empty");
        return false;
    }
//
//    if(empty(trim(get_post('application_id')))) {
//        display_error("Application ID should not be empty");
//        return false;
//    }
//
//    if(empty(trim(get_post('ref_name'))) && !in_array($_SESSION['Items']->dim_id, [DT_TYPING]) ) {
//        display_error("Narration should not be empty");
//        return false;
//    }


    if ($duplicates = check_duplicate_transaction_id(db_escape(get_post('transaction_id')))) {
        display_error(trans("Duplicate MB/ST/RWID found on Invoice Number - " . $duplicates['reference']));
        return false;
    }

    if ($application_duplicates = check_duplicate_application_id(db_escape(get_post('application_id')))) {
        display_error(trans("Duplicate Application ID found on Invoice Number - " . $application_duplicates['reference']));
        return false;
    }

    if ($_POST['UpdateItem'] != '' && check_item_data()) {
        $_SESSION['Items']->update_cart_item($_POST['LineNo'],
            input_num('qty'), input_num('price'),
            input_num('Disc') / 100, $_POST['item_description'], $govt_fee,
            input_num('bank_service_charge'), input_num('bank_service_charge_vat'),
            input_num('discount_amount'), get_post('transaction_id'), null,
            get_post('application_id'), get_post('govt_bank_account'),
            get_post('ref_name'), get_post('ed_transaction_id'));
    }
    page_modified();
    line_start_focus();
}

//--------------------------------------------------------------------------------

function handle_delete_item($line_no)
{
    if ($_SESSION['Items']->some_already_delivered($line_no) == 0) {
        $_SESSION['Items']->remove_from_cart($line_no);
    } else {
        display_error(trans("This item cannot be deleted because some of it has already been delivered."));
    }
    line_start_focus();
}

//--------------------------------------------------------------------------------

function handle_new_item()
{
    global $SysPrefs;

    if (!check_item_data()) {
        return;
    }

    //FOR TAMAAM
    $item_info = get_item(get_post('stock_id'));
    $govt_fee = input_num('govt_fee');

    if ($item_info['category_id'] == $SysPrefs->prefs['immigration_category']) {

//        if(empty($govt_fee)) {
//            display_error("Govt. Fee Should not be empty");
//            return false;
//        }

//        $govt_fee  = $govt_fee - input_num('price');
    }
    //END -- FOR TAMAAM

    if (empty(trim(get_post('invoice_type')))) {
        display_error("Please select payment type.");
        return false;
    }

    if (empty(trim(get_post('transaction_id')))
        && !in_array($_SESSION['Items']->dim_id, [DT_TYPING,DT_AMER])
        && !in_array($_SESSION['wa_current_user']->access, [48])
        && !in_array($_SESSION['wa_current_user']->user, [118])
    ) {
        display_error("Bank Reference Number should not be empty");
        return false;
    }

    if (empty(trim(get_post('application_id')))
        && !in_array($_SESSION['Items']->dim_id, [DT_TYPING])
        && !in_array($_SESSION['wa_current_user']->access, [48])
        && !in_array($_SESSION['wa_current_user']->user, [118])
    ) {
        display_error("Application ID should not be empty");
        return false;
    }
//
//    if(empty(trim(get_post('application_id')))) {
//        display_error("Application ID should not be empty");
//        return false;
//    }
//
//    if(empty(trim(get_post('ref_name'))) && !in_array($_SESSION['Items']->dim_id, [DT_TYPING]) ) {
//        display_error("Narration should not be empty");
//        return false;
//    }


    if ($duplicates = check_duplicate_transaction_id(db_escape(get_post('transaction_id')))) {
        display_error(trans("Duplicate Bank Reference Number found on Invoice Number - " . $duplicates['reference']));
        return false;
    }

    if ($application_duplicates = check_duplicate_application_id(db_escape(get_post('application_id')))) {
        display_error(trans("Duplicate Application ID found on Invoice Number - " . $application_duplicates['reference']));
        return false;
    }

    add_to_order($_SESSION['Items'], get_post('stock_id'), input_num('qty'),
        input_num('price'), input_num('Disc') / 100, get_post('stock_id_text'),
        $govt_fee, input_num('bank_service_charge'), input_num('bank_service_charge_vat'), get_post('transaction_id'),
        input_num('discount_amount'), null,
        get_post('application_id'), get_post('govt_bank_account'), get_post('ref_name'), get_post('ed_transaction_id'));


    unset($_POST['_stock_id_edit'], $_POST['stock_id']);
    page_modified();
    line_start_focus();

}

//--------------------------------------------------------------------------------

function handle_cancel_order()
{
    global $path_to_root, $Ajax;

    if ($_SESSION['Items']->trans_type == ST_CUSTDELIVERY) {
        display_notification(trans("Direct delivery entry has been cancelled as requested."), 1);
        submenu_option(trans("Enter a New Sales Delivery"), "/sales/sales_order_entry.php?NewDelivery=1");
    } elseif ($_SESSION['Items']->trans_type == ST_SALESINVOICE) {
        display_notification(trans("Direct invoice entry has been cancelled as requested."), 1);
        submenu_option(trans("Enter a New Sales Invoice"), "/sales/sales_order_entry.php?NewInvoice=1");
    } elseif ($_SESSION['Items']->trans_type == ST_SALESQUOTE) {
        if ($_SESSION['Items']->trans_no != 0)
            delete_sales_order(key($_SESSION['Items']->trans_no), $_SESSION['Items']->trans_type);
        display_notification(trans("This sales quotation has been cancelled as requested."), 1);
        submenu_option(trans("Enter a New Sales Quotation"), "/sales/sales_order_entry.php?NewQuotation=Yes");
    } else { // sales order
        if ($_SESSION['Items']->trans_no != 0) {
            $order_no = key($_SESSION['Items']->trans_no);
            if (sales_order_has_deliveries($order_no)) {
                close_sales_order($order_no);
                display_notification(trans("Undelivered part of order has been cancelled as requested."), 1);
                submenu_option(trans("Select Another Sales Order for Edition"), "/sales/inquiry/sales_orders_view.php?type=" . ST_SALESORDER);
            } else {
                delete_sales_order(key($_SESSION['Items']->trans_no), $_SESSION['Items']->trans_type);

                display_notification(trans("This sales order has been cancelled as requested."), 1);
                submenu_option(trans("Enter a New Sales Order"), "/sales/sales_order_entry.php?NewOrder=Yes");
            }
        } else {
            processing_end();
            meta_forward($path_to_root . '/index.php', 'application=orders');
        }
    }
    processing_end();
    display_footer_exit();
}

//--------------------------------------------------------------------------------

function create_cart($type, $trans_no)
{


    global $Refs, $SysPrefs;

    if (!$SysPrefs->db_ok) // create_cart is called before page() where the check is done
        return;

    processing_start();

    if (isset($_GET['NewQuoteToSalesOrder'])) {
        $trans_no = $_GET['NewQuoteToSalesOrder'];
        $doc = new Cart(ST_SALESQUOTE, $trans_no, true);
        $doc->Comments = trans("Sales Quotation") . " # " . $trans_no;
        $_SESSION['Items'] = $doc;
    } elseif ($type != ST_SALESORDER && $type != ST_SALESQUOTE && $trans_no != 0) { // this is template

        $doc = new Cart(ST_SALESORDER, array($trans_no));

        $doc->reference = $Refs->get_next($doc->trans_type, null, array('date' => Today()));

        /**
         * Daxis : Bipin
         * 10-July-2018
         * If Invoice is in Edit Mode, Populate the editing invoice data
         */

        if ($_GET['EditFlag'] && $_GET['EditFlag'] == 'true') {

            $editing_invoice_order = db_fetch_assoc(get_sales_order_invoices($_GET['NewInvoice']));

            $result = get_customer_trans_details(ST_SALESINVOICE, $editing_invoice_order['trans_no']);


//            dd($editing_invoice_order);

            $_SESSION['editing_invoice_no'] = $editing_invoice_order['trans_no'];


            if (db_num_rows($result) > 0) {

                //Modified for AMER
                for ($line_no = 0; $myrow = db_fetch($result); $line_no++) {


                    $doc->line_items[$line_no] = new line_details(
                        $myrow["stock_id"], $myrow["quantity"],
                        $myrow["unit_price"], $myrow["discount_percent"],
                        $myrow["qty_done"], $myrow["standard_cost"],
                        $myrow["StockDescription"], $myrow["id"],
                        $myrow["debtor_trans_no"],
                        @$myrow["src_id"],
                        $doc->line_items[$line_no]->govt_fee ?
                            $doc->line_items[$line_no]->govt_fee : $myrow['govt_fee'],
                        $myrow['bank_service_charge']+$myrow['extra_service_charge'],
                        $doc->line_items[$line_no]->bank_service_charge_vat ?
                            $doc->line_items[$line_no]->bank_service_charge_vat : $myrow['bank_service_charge_vat'],
                        $doc->line_items[$line_no]->transaction_id ?
                            $doc->line_items[$line_no]->transaction_id : $myrow['transaction_id'],
                        $doc->line_items[$line_no]->discount_amount,
                        null,
                        $doc->line_items[$line_no]->application_id ?
                            $doc->line_items[$line_no]->application_id : $myrow['application_id'],
                        $myrow['govt_bank_account'],
                        $doc->line_items[$line_no]->ref_name ?
                            $doc->line_items[$line_no]->ref_name : $myrow['ref_name'],
                        $doc->line_items[$line_no]->ed_transaction_id ?
                            $doc->line_items[$line_no]->ed_transaction_id : $myrow['ed_transaction_id']

                    );

                    $_SESSION["invoiced_by"] = $myrow['created_by_id'];

                }
            }

            $doc->reference = $editing_invoice_order['reference'];
            $doc->pay_type = $editing_invoice_order['payment_method'];
            $doc->contact_person = $editing_invoice_order['contact_person'];
            $doc->customer_name = $editing_invoice_order['display_customer'];
            $doc->phone = $editing_invoice_order['customer_mobile'];
            $doc->email = $editing_invoice_order['customer_email'];
            $doc->tax_id = $editing_invoice_order['customer_trn'];
            $doc->edit_document_date = sql2date($editing_invoice_order['tran_date']);
            $_POST['customer_ref'] = $editing_invoice_order['customer_ref'];
            $_POST['OrderDate'] = $editing_invoice_order['tran_date'];
            $_POST['invoice_type'] = $editing_invoice_order['invoice_type'];

//            $_POST['dimension_id'] = $editing_invoice_order['dimension_id'];
            $doc->dimension_id = $editing_invoice_order['dimension_id'];

//            pp($_POST['dimension_id']);

            $_SESSION['Items']->customer_id = $editing_invoice_order['debtor_no'];


            $doc->customer_ref = $editing_invoice_order['customer_ref'];
            $doc->mistake_staff_id = $editing_invoice_order['mistake_staff_id'];


        }

        /** End--Modification for Invoice editing */

        $doc->editing_invoice_no = $editing_invoice_order['trans_no'];

        $doc->trans_type = $type;
        $doc->trans_no = 0;
        $doc->document_date = new_doc_date();
        if ($type == ST_SALESINVOICE) {
            $doc->due_date = get_invoice_duedate($doc->payment, $doc->document_date);
            $doc->pos = get_sales_point(user_pos());
        } else
            $doc->due_date = $doc->document_date;


        //$doc->Comments='';
        foreach ($doc->line_items as $line_no => $line) {
            $doc->line_items[$line_no]->qty_done = 0;
//            $doc->line_items[$line_no]->transaction_id = 243234;
        }
        $_SESSION['Items'] = $doc;
    } else {


        $_SESSION['Items'] = new Cart($type, array($trans_no));
        if (isset($_GET['SRQ_TOKEN']) && !empty($_GET['SRQ_TOKEN'])) {

            $req_id = $_GET['req_id'];


            $sql = "SELECT * FROM 0_service_requests where id=$req_id";

            $sql .= " ORDER BY id DESC LIMIT 1";
            $get = db_query($sql);
            $service_request = db_fetch($get);


            $sql = "select * from 0_service_request_items where req_id = " . $service_request['id'];

//            pp($sql);

            $get = db_query($sql);

            if (db_num_rows($get) > 0) {

                //Modified for AMER
                for ($line_no = 0; $myrow = db_fetch($get); $line_no++) {


                    $item_info = get_item($myrow["stock_id"]);
                    $bank_service_charge = $item_info['bank_service_charge'];
                    $bank_service_charge_vat = $item_info['bank_service_charge_vat'];
                    $pf_amount = $item_info['pf_amount'];

                    $govt_bank_acc = $item_info['govt_bank_account'];

                    /** 
                     * If AL ADHEED and has gov fee; but not service charge, 
                     * Then set the default bank service charge
                     * (Already done when service request) so just override the amount here 
                     */
                    if(in_array($_SESSION['Items']->dimension_id, [DT_ADHEED, DT_ADHEED_OTH])) {
                        $bank_service_charge = $myrow['bank_service_charge'];
                        /** Because $bank_service_charge is inclusive of VAT, so no need to add again */
                        $bank_service_charge_vat = 0.00;
                    }

//                    $sql= "select * from customer_discount_items
//                    where customer_id = ".$service_request['customer_id']." and item_id=".$item_info['category_id'];
//                    $get = db_query($sql);

//                    $discount_info = db_fetch($get);
                    $discount_info = getCategoryDiscountInfo($service_request['customer_id'], $item_info['category_id']);


                    $discount_amount = 0;
                    $discount_percent = 0;
                    if (!empty($discount_info)) {
                        $discount_amount = $discount_info;


                        $discount_percent = ($discount_amount * 100) / $myrow["price"];
                        $discount_percent = $discount_percent / 100;
                    }

                    $_SESSION['Items']->line_items[$line_no] = new line_details(
                        $myrow["stock_id"], $myrow["qty"],
                        $myrow["price"], $discount_percent,
                        $myrow["qty"], 0,
                        $myrow["description"], 0,
                        0,
                        0,
                        $myrow['govt_fee'],
                        $myrow['bank_service_charge'],
                        $myrow['bank_service_charge_vat'],
                        null,//TR ID
                        $discount_amount,//DISC AMT
                        null,
                        $myrow["application_id"],
                        $govt_bank_acc,//govt_bank
                        $myrow["ref_name"],
                        null

                    );

//                    pp($line_no);

                }
            }

            $_SESSION["invoiced_by"] = $service_request['created_by'];


            $_SESSION['Items']->contact_person = $service_request['contact_person'];
            $_SESSION['Items']->customer_name = $service_request['display_customer']; //dsplay customer;
            $_SESSION['Items']->phone = $service_request['mobile'];
            $_SESSION['Items']->email = $service_request['email'];
//            $_SESSION['Items']->tax_id = $service_request['tax_id'];
//            $_POST['customer_ref'] = "Customer REf";
//            $_POST['OrderDate'] = Today();
            $_POST['invoice_type'] = $service_request['payment_method'];
            $_SESSION['Items']->dimension_id = $service_request['cost_center_id'];
            $_SESSION['Items']->customer_id = $service_request['customer_id'];
            $_SESSION['Items']->customer_ref = $service_request['iban'];

            $_SESSION['Items']->token_number = $service_request['token_number'];
            $_SESSION['Items']->service_req_id = $service_request['id'];

        }


    }

    //DEFAULT_CUSTOMER_ID set to WALK-IN CUSTOMER
    if ((!isset($_GET['EditFlag']) || $_GET['EditFlag'] == 'false') &&
        (!isset($_GET['SRQ_TOKEN']) || empty($_GET['SRQ_TOKEN']))) {


        $_SESSION['Items']->customer_id = 1;

//        $user_id = $_SESSION['wa_current_user']->user;
//        $user = get_user($user_id);

//        $_POST['dimension_id'] = $user['dflt_dimension_id'];


    }

    copy_from_cart();


}

function getCategoryDiscountInfo($customer_id, $category_id)
{

    $sql = "select * from customer_discount_items
                    where customer_id = " . $customer_id . " and item_id=" . $category_id;
    $get = db_query($sql);

    $discount = 0;
    if (db_num_rows($get) > 0) {

        $res = db_fetch($get);
        $discount = $res['discount'];
    }
    return $discount;

}

//--------------------------------------------------------------------------------

if (isset($_POST['CancelOrder']))
    handle_cancel_order();

$id = find_submit('Delete');
if ($id != -1)
    handle_delete_item($id);

if (isset($_POST['UpdateItem']))
    handle_update_item();

if (isset($_POST['AddItem']))
    handle_new_item();

if (isset($_POST['CancelItemChanges'])) {
    line_start_focus();
}

//--------------------------------------------------------------------------------
if ($_SESSION['Items']->fixed_asset)
    check_db_has_disposable_fixed_assets(trans("There are no fixed assets defined in the system."));
else
    check_db_has_stock_items(trans("There are no items defined in the system."));

check_db_has_customer_branches(trans("There are no customers, or there are no customers with branches. Please define customers and customer branches."));

if ($_SESSION['Items']->trans_type == ST_SALESINVOICE) {
    $idate = trans("Invoice Date:");
    $orderitems = trans("Sales Invoice Items");
    $deliverydetails = trans("Enter Delivery Details and Confirm Invoice");
    $cancelorder = trans("Cancel Invoice");
    $porder = trans("Place Invoice");


    if (isset($_GET['EditFlag']) && $_GET['EditFlag'] == 'true') {

        $porder = trans("Update Invoice");

    }


} elseif ($_SESSION['Items']->trans_type == ST_CUSTDELIVERY) {
    $idate = trans("Delivery Date:");
    $orderitems = trans("Delivery Note Items");
    $deliverydetails = trans("Enter Delivery Details and Confirm Dispatch");
    $cancelorder = trans("Cancel Delivery");
    $porder = trans("Place Delivery");
} elseif ($_SESSION['Items']->trans_type == ST_SALESQUOTE) {
    $idate = trans("Quotation Date:");
    $orderitems = trans("Sales Quotation Items");
    $deliverydetails = trans("Enter Delivery Details and Confirm Quotation");
    $cancelorder = trans("Cancel Quotation");
    $porder = trans("Place Quotation");
    $corder = trans("Commit Quotations Changes");
} else {
    $idate = trans("Order Date:");
    $orderitems = trans("Sales Order Items");
    $deliverydetails = trans("Enter Delivery Details and Confirm Order");
    $cancelorder = trans("Cancel Order");
    $porder = trans("Place Order");
    $corder = trans("Commit Order Changes");
}
start_form();

hidden('cart_id');


if ($_SESSION['Items']->customer_id != get_post('customer_id', -1)) {

//    $_POST['token_no'] = "";

    if (!empty($_SESSION['Items']->line_items)) {

        //pp($_SESSION['Items']->line_items);

        $has_discount = false;

        foreach ($_SESSION['Items']->line_items as $line_row) {

            if(!empty($line_row->discount_amount)) {
                $has_discount = true;
//                return true;
                break;
            }


        }

        if($has_discount) {
            display_error("Customer cannot be changed because customer discount is applied. Please remove the service items before changing customer");
            $_POST['customer_id'] = $_SESSION['Items']->customer_id;
        }
    }
}


//YBC


$customer_error = display_order_header($_SESSION['Items'], !$_SESSION['Items']->is_started(), $idate);


if ($customer_error == "") {
    start_table(TABLESTYLE, "width='80%'", 10);

//    display_error(print_r($_SESSION['Items'] ,true));


    $auto_button_html = '<span style="float: left"><button type="button" id="auto_fetch_button" class="auto_fetch_button  btn btn btn-primary">' . trans("Auto") . '</button>
<button type="button" id="auto_fetch_batch_open" class="auto_fetch_batch_open btn btn btn-primary">' . trans("Auto Batch") . '</button>&emsp;</span>';

    if (!in_array($_SESSION['Items']->dim_id, [2])) {
        $auto_button_html = "";
    }

//    $auto_button_html='';
    echo "<tr><td>";
    display_order_summary($auto_button_html . $orderitems, $_SESSION['Items'], true);
    echo "</td></tr>";
    echo "<tr><td>";
    display_delivery_details($_SESSION['Items']);
    echo "</td></tr>";
    end_table(1);

    if (check_credit_limit_validation()) {


        if ($_SESSION['Items']->trans_no == 0) {

            submit_center_first('ProcessOrder', $porder,
                trans('Check entered data and save document'), 'default');


//        submit_center('PlaceAndPrintInvoice', "Place and Print",
//            trans('Cancels document entry or removes sales order when editing an old document'));


            submit_center_last('CancelOrder', $cancelorder,
                trans('Cancels document entry or removes sales order when editing an old document'));
            submit_js_confirm('CancelOrder', trans('You are about to void this Document.\nDo you want to continue?'));


        } else {
            submit_center_first('ProcessOrder', $corder,
                trans('Validate changes and update document'), 'default');
            submit_center_last('CancelOrder', $cancelorder,
                trans('Cancels document entry or removes sales order when editing an old document'));
            if ($_SESSION['Items']->trans_type == ST_SALESORDER)
                submit_js_confirm('CancelOrder', trans('You are about to cancel undelivered part of this order.\nDo you want to continue?'));
            else
                submit_js_confirm('CancelOrder', trans('You are about to void this Document.\nDo you want to continue?'));
        }
    }


    //$Ajax->activate('ProcessOrder');


} else {
    display_error($customer_error);
}


end_form();
end_page(); ?>

<?php if (!isset($_GET['EditFlag']) || $_GET['EditFlag'] == 'false') {


    echo '
<script>
    $(document).ready(function() {

        setTimeout(function () {
//            $("#dimension_id").trigger(\'change\');

        },1000)

    })
</script>';


} ?>


<style>
    input[name="OrderDate"], input[name="ref"] {
        pointer-events: none;
        background: #ccc;
    }
</style>

