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
//---------------------------------------------------------------------------
//
//	Entry/Modify Sales Invoice against single delivery
//	Entry/Modify Batch Sales Invoice against batch of deliveries
//
$page_security = 'SA_SALESINVOICE';
$path_to_root = "..";
include_once($path_to_root . "/sales/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");
include_once($path_to_root . "/admin/db/shipping_db.inc");


$js = "";
if ($SysPrefs->use_popup_windows) {
    $js .= get_js_open_window(900, 500);
}
if (user_use_date_picker()) {
    $js .= get_js_date_picker();
}

if (isset($_GET['ModifyInvoice'])) {
    $_SESSION['page_title'] = sprintf(trans("Modifying Sales Invoice # %d."), $_GET['ModifyInvoice']);
    $help_context = "Modifying Sales Invoice";
} elseif (isset($_GET['DeliveryNumber'])) {
    $_SESSION['page_title'] = trans($help_context = "Issue an Invoice for Delivery Note");
} elseif (isset($_GET['BatchInvoice'])) {
    $_SESSION['page_title'] = trans($help_context = "Issue Batch Invoice for Delivery Notes");
} elseif (isset($_GET['AllocationNumber']) || isset($_GET['InvoicePrepayments'])) {
    $_SESSION['page_title'] = trans($help_context = "Prepayment or Final Invoice Entry");
}
page($_SESSION['page_title'], false, false, "", $js);

//-----------------------------------------------------------------------------

check_edit_conflicts(get_post('cart_id'));

if (isset($_GET['AddedID'])) {

    $invoice_no = $_GET['AddedID'];
    $trans_type = ST_SALESINVOICE;

    display_notification(trans("Selected deliveries has been processed"), true);

    display_note(get_customer_trans_view_str($trans_type, $invoice_no, trans("&View This Invoice")), 0, 1);

    display_note(print_document_link($invoice_no . "-" . $trans_type, trans("&Print This Invoice"), true, ST_SALESINVOICE));
    display_note(print_document_link($invoice_no . "-" . $trans_type, trans("&Email This Invoice"), true, ST_SALESINVOICE, false, "printlink", "", 1), 1);

    display_note(get_gl_view_str($trans_type, $invoice_no, trans("View the GL &Journal Entries for this Invoice")), 1);

    hyperlink_params("$path_to_root/sales/inquiry/sales_deliveries_view.php", trans("Select Another &Delivery For Invoicing"), "OutstandingOnly=1");

    if (!db_num_rows(get_allocatable_from_cust_transactions(null, $invoice_no, $trans_type)))
        hyperlink_params("$path_to_root/sales/customer_payments.php", trans("Entry &customer payment for this invoice"),
            "SInvoice=" . $invoice_no);

    hyperlink_params("$path_to_root/admin/attachments.php", trans("Add an Attachment"), "filterType=$trans_type&trans_no=$invoice_no");

    display_footer_exit();

} elseif (isset($_GET['UpdatedID'])) {

    $invoice_no = $_GET['UpdatedID'];
    $trans_type = ST_SALESINVOICE;

    display_notification_centered(sprintf(trans('Sales Invoice # %d has been updated.'), $invoice_no));

    display_note(get_trans_view_str(ST_SALESINVOICE, $invoice_no, trans("&View This Invoice")));
    echo '<br>';
    display_note(print_document_link($invoice_no . "-" . $trans_type, trans("&Print This Invoice"), true, ST_SALESINVOICE));
    display_note(print_document_link($invoice_no . "-" . $trans_type, trans("&Email This Invoice"), true, ST_SALESINVOICE, false, "printlink", "", 1), 1);

    hyperlink_no_params($path_to_root . "/sales/inquiry/customer_inquiry.php", trans("Select Another &Invoice to Modify"));

    display_footer_exit();

} elseif (isset($_GET['RemoveDN'])) {

    for ($line_no = 0; $line_no < count($_SESSION['Items']->line_items); $line_no++) {
        $line = &$_SESSION['Items']->line_items[$line_no];
        if ($line->src_no == $_GET['RemoveDN']) {
            $line->quantity = $line->qty_done;
            $line->qty_dispatched = 0;
        }
    }
    unset($line);

    // Remove also src_doc delivery note
    $sources = &$_SESSION['Items']->src_docs;
    unset($sources[$_GET['RemoveDN']]);
}

//-----------------------------------------------------------------------------

if ((isset($_GET['DeliveryNumber']) && ($_GET['DeliveryNumber'] > 0))
    || isset($_GET['BatchInvoice'])) {

    processing_start();

    if (isset($_GET['BatchInvoice'])) {
        $src = $_SESSION['DeliveryBatch'];
        unset($_SESSION['DeliveryBatch']);
    } else {
        $src = array($_GET['DeliveryNumber']);
    }

    /*read in all the selected deliveries into the Items cart  */
    $dn = new Cart(ST_CUSTDELIVERY, $src, true);

    if ($dn->count_items() == 0) {
        hyperlink_params($path_to_root . "/sales/inquiry/sales_deliveries_view.php",
            trans("Select a different delivery to invoice"), "OutstandingOnly=1");
        die ("<br><b>" . trans("There are no delivered items with a quantity left to invoice. There is nothing left to invoice.") . "</b>");
    }

    $_SESSION['Items'] = $dn;
    copy_from_cart();

} elseif (isset($_GET['ModifyInvoice']) && $_GET['ModifyInvoice'] > 0) {

    check_is_editable(ST_SALESINVOICE, $_GET['ModifyInvoice']);

    processing_start();
    $_SESSION['Items'] = new Cart(ST_SALESINVOICE, $_GET['ModifyInvoice']);

    hidden('invoice_type',$_SESSION['Items']->invoice_type);
    hidden('payment_flag',$_SESSION['Items']->payment_flag);
    hidden('dimension_id',$_SESSION['Items']->dimension_id);


//    pp($_SESSION['Items']);

//    pp($_SESSION['Items']->dimension_id);

//    pp($_SESSION['Items']);

    //IS_TADBEER/TASHEEL
    $_SESSION['Items']->is_tadbeer = $_GET['is_tadbeer'];
    $_SESSION['Items']->show_items = $_GET['show_items'];



    $trans_info = get_customer_trans($_GET['ModifyInvoice'],ST_SALESINVOICE);

    global $global_pay_types_array;

    $pay_type = array_search($trans_info['payment_method'], $global_pay_types_array);

    $_SESSION['Items']->pay_type=$pay_type;



    if ($_SESSION['Items']->count_items() == 0) {
        echo "<center><br><b>" . trans("All quantities on this invoice has been credited. There is nothing to modify on this invoice") . "</b></center>";
        display_footer_exit();
    }
    copy_from_cart();
} elseif (isset($_GET['AllocationNumber']) || isset($_GET['InvoicePrepayments'])) {

    check_deferred_income_act(trans("You have to set Deferred Income Account in GL Setup to entry prepayment invoices."));

    if (isset($_GET['AllocationNumber'])) {
        $payments = array(get_cust_allocation($_GET['AllocationNumber']));

        if (!$payments || ($payments[0]['trans_type_to'] != ST_SALESORDER)) {
            display_error(trans("Please select correct Sales Order Prepayment to be invoiced and try again."));
            display_footer_exit();
        }
        $order_no = $payments[0]['trans_no_to'];
    } else {
        $order_no = $_GET['InvoicePrepayments'];
    }
    processing_start();

    $_SESSION['Items'] = new cart(ST_SALESORDER, $order_no, ST_SALESINVOICE);


    $_SESSION['Items']->order_no = $order_no;
    $_SESSION['Items']->src_docs = array($order_no);
    $_SESSION['Items']->trans_no = 0;
    $_SESSION['Items']->trans_type = ST_SALESINVOICE;

    $_SESSION['Items']->update_payments();

    copy_from_cart();
} elseif (!processing_active()) {
    /* This page can only be called with a delivery for invoicing or invoice no for edit */
    display_error(trans("This page can only be opened after delivery selection. Please select delivery to invoicing first."));

    hyperlink_no_params("$path_to_root/sales/inquiry/sales_deliveries_view.php", trans("Select Delivery to Invoice"));

    end_page();
    exit;
} elseif (!isset($_POST['process_invoice']) && (!$_SESSION['Items']->is_prepaid() && !check_quantities())) {
    display_error(trans("Selected quantity cannot be less than quantity credited nor more than quantity not invoiced yet."));
}

if (isset($_POST['Update'])) {
    $Ajax->activate('Items');
}
if (isset($_POST['_InvoiceDate_changed'])) {
    $_POST['due_date'] = get_invoice_duedate($_SESSION['Items']->payment, $_POST['InvoiceDate']);
    $Ajax->activate('due_date');
}

//-----------------------------------------------------------------------------
function check_quantities()
{

    global $trans_info;
    $ok = 1;
    foreach ($_SESSION['Items']->line_items as $line_no => $itm) {
        if (isset($_POST['Line' . $line_no])) {
            if ($_SESSION['Items']->trans_no) {
                $min = $itm->qty_done;
                $max = $itm->quantity;
            } else {
                $min = 0;
                $max = $itm->quantity - $itm->qty_done;
            }
            if (check_num('Line' . $line_no, $min, $max)) {
                $_SESSION['Items']->line_items[$line_no]->qty_dispatched =
                    input_num('Line' . $line_no);
            } else {
                $ok = 0;
            }

        }



        $_SESSION['Items']->payment_card = $_SESSION['Items']->line_items[$line_no]->govt_bank_account;
        $other_charges_info = [];
        $detail_id = $itm->id;
//        $sql = "SELECT a.*,b.account_name FROM 0_other_charges_trans_details a
//                    LEFT JOIN 0_chart_master b ON b.account_code = a.acc_code
//                    WHERE a.debtor_trans_detail_id=$detail_id";
//        $query = db_query($sql);
//
//        while ($res = db_fetch($query)) {
//            $other_charges_info[] = [
//                "account_code" => $res['acc_code'],
//                "amount" => $res['amount'],
//                "description" => $res['description'],
//                "account_name" => $res['account_name'],
//            ];
//        }


        $other_charges_info_encoded = base64_encode(json_encode($other_charges_info));
        $_SESSION['Items']->line_items[$line_no]->other_fee_info_json = $other_charges_info_encoded;



//        if (isset($_SESSION['Items']->is_tadbeer) && $_SESSION['Items']->is_tadbeer==1) {
//            $sql = "SELECT * FROM 0_debtor_trans_details WHERE id=$detail_id";
//            $ln_details = db_fetch(db_query($sql));
//            $gv_amt = $ln_details['govt_fee']+$ln_details['unit_price'];
//            $_SESSION['Items']->line_items[$line_no]->govt_fee = $gv_amt;
//        }


        $sql = "SELECT * FROM 0_debtor_trans_details WHERE id=$detail_id";
        $ln_details = db_fetch(db_query($sql));

        $_SESSION['Items']->line_items[$line_no]->govt_bank_account = $ln_details['govt_bank_account'];
        $_SESSION['Items']->line_items[$line_no]->bank_service_charge
            = $_SESSION['Items']->line_items[$line_no]->bank_service_charge+$ln_details['extra_service_charge'];


        if (isset($_POST['Line' . $line_no . 'govt_fee'])) {
            $line_govt_fee = $_POST['Line' . $line_no . 'govt_fee'];
            if (strlen($line_govt_fee) > 0) {
                $_SESSION['Items']->line_items[$line_no]->govt_fee = $line_govt_fee;
            }
        }



        if (isset($_POST['Line' . $line_no . 'Desc'])) {
            $line_desc = $_POST['Line' . $line_no . 'Desc'];
            if (strlen($line_desc) > 0) {
                $_SESSION['Items']->line_items[$line_no]->item_description = $line_desc;
            }
        }


        if (isset($_POST['Line' . $line_no . 'transaction_id'])) {
            $line_transaction_id = $_POST['Line' . $line_no . 'transaction_id'];
            if (strlen($line_transaction_id) > 0) {
                $_SESSION['Items']->line_items[$line_no]->transaction_id = $line_transaction_id;
            }
        }


        if (isset($_POST['Line' . $line_no . 'application_id'])) {
            $line_application_id = $_POST['Line' . $line_no . 'application_id'];
            if (strlen($line_application_id) > 0) {
                $_SESSION['Items']->line_items[$line_no]->application_id = $line_application_id;
            }
        }


        if (isset($_POST['Line' . $line_no . 'ref_name'])) {
            $line_ref_name = $_POST['Line' . $line_no . 'ref_name'];
            if (strlen($line_ref_name) > 0) {
                $_SESSION['Items']->line_items[$line_no]->ref_name = $line_ref_name;
            }
        }



        if (isset($_POST['Line' . $line_no . 'ed_transaction_id'])) {
            $line_ed_transaction_id = $_POST['Line' . $line_no . 'ed_transaction_id'];
            if (strlen($line_ed_transaction_id) > 0) {
                $_SESSION['Items']->line_items[$line_no]->ed_transaction_id = $line_ed_transaction_id;
            }
        }


        if (isset($_POST['Line' . $line_no . 'user_id'])) {

            $user_id = $_POST['Line' . $line_no . 'user_id'];
            if (strlen($user_id) > 0) {
                $user = get_user_by_login($user_id);
                $created_by = $user['id'];
                $_SESSION['Items']->line_items[$line_no]->user_id = $created_by;
            }
        }



        if (isset($_POST['Line' . $line_no . 'discount_amount'])) {
            $line_discount_amount = $_POST['Line' . $line_no . 'discount_amount'];

            if (is_numeric($line_discount_amount)) {
                $_SESSION['Items']->line_items[$line_no]->discount_amount = $line_discount_amount;
            }
        }


        if (isset($_POST['Line' . $line_no . 'transaction_id_updated_at'])) {
            $transaction_id_updated_at = $_POST['Line' . $line_no . 'transaction_id_updated_at'];
            $_SESSION['Items']->line_items[$line_no]->transaction_id_updated_at = $transaction_id_updated_at;

        }


//        if (isset($_POST['Line' . $line_no . 'govt_fee'])) {
//            $govt_fee = $_POST['Line' . $line_no . 'govt_fee'];
//
//            if (is_numeric($govt_fee)) {
//                $_SESSION['Items']->line_items[$line_no]->govt_fee = $govt_fee;
//            }
//        }


    }

    return $ok;
}

function set_delivery_shipping_sum($delivery_notes)
{

    $shipping = 0;

    foreach ($delivery_notes as $delivery_num) {
        $myrow = get_customer_trans($delivery_num, ST_CUSTDELIVERY);

        $shipping += $myrow['ov_freight'];
    }
    $_POST['ChargeFreightCost'] = price_format($shipping);
}


function copy_to_cart()
{
    $cart = &$_SESSION['Items'];
    $cart->due_date = $cart->document_date = $_POST['InvoiceDate'];
    $cart->Comments = $_POST['Comments'];
    $cart->due_date = $_POST['due_date'];

    $cart->display_customer = $_POST['display_customer'];
    $cart->customer_trn = $_POST['customer_trn'];
    $cart->customer_mobile = $_POST['customer_mobile'];
    $cart->customer_email = $_POST['customer_email'];
    $cart->customer_ref = $_POST['customer_ref'];


    if (($cart->pos['cash_sale'] || $cart->pos['credit_sale']) && isset($_POST['payment'])) {
        $cart->payment = $_POST['payment'];
        $cart->payment_terms = get_payment_terms($_POST['payment']);
    }
    if ($_SESSION['Items']->trans_no == 0)
        $cart->reference = $_POST['ref'];
    if (!$cart->is_prepaid()) {
        $cart->ship_via = $_POST['ship_via'];
        $cart->freight_cost = input_num('ChargeFreightCost');
    }

    $cart->update_payments();


//    $cart->dimension_id = $_POST['dimension_id'];//YBC
    $cart->dimension2_id = $_POST['dimension2_id'];
}

//-----------------------------------------------------------------------------

function copy_from_cart()
{
    $cart = &$_SESSION['Items'];

    $_POST['Comments'] = $cart->Comments;
    $_POST['InvoiceDate'] = $cart->document_date;
    $_POST['ref'] = $cart->reference;
    $_POST['cart_id'] = $cart->cart_id;
    $_POST['due_date'] = $cart->due_date;
    $_POST['payment'] = $cart->payment;
// 	$_POST['display_customer'] = $cart->display_customer;
    if (!$_SESSION['Items']->is_prepaid()) {
        $_POST['ship_via'] = $cart->ship_via;
        $_POST['ChargeFreightCost'] = price_format($cart->freight_cost);
    }
    $_POST['dimension_id'] = $cart->dimension_id;
    $_POST['dimension2_id'] = $cart->dimension2_id;
}

//-----------------------------------------------------------------------------

function check_data()
{
    global $Refs;

    $prepaid = $_SESSION['Items']->is_prepaid();

    if (!isset($_POST['InvoiceDate']) || !is_date($_POST['InvoiceDate'])) {
        display_error(trans("The entered invoice date is invalid."));
        set_focus('InvoiceDate');
        return false;
    }

    if (!is_date_in_fiscalyear($_POST['InvoiceDate'])) {
        display_error(trans("The entered date is out of fiscal year or is closed for further data entry."));
        set_focus('InvoiceDate');
        return false;
    }


   /* if (!$prepaid && (!isset($_POST['due_date']) || !is_date($_POST['due_date']))) {
        display_error(trans("The entered invoice due date is invalid."));
        set_focus('due_date');
        return false;
    }*/

    if ($_SESSION['Items']->trans_no == 0) {
        if (!$Refs->is_valid($_POST['ref'], ST_SALESINVOICE)) {
            display_error(trans("You must enter a reference."));
            set_focus('ref');
            return false;
        }
    }


    if (!$prepaid) {


        if ($_POST['ChargeFreightCost'] == "") {
            $_POST['ChargeFreightCost'] = price_format(0);
        }

        if (!check_num('ChargeFreightCost', 0)) {
            display_error(trans("The entered shipping value is not numeric."));
            set_focus('ChargeFreightCost');
            return false;
        }

        if ($_SESSION['Items']->has_items_dispatch() == 0 && input_num('ChargeFreightCost') == 0) {
            display_error(trans("There are no item quantities on this invoice."));
            return false;
        }

        if (!check_quantities()) {
            display_error(trans("Selected quantity cannot be less than quantity credited nor more than quantity not invoiced yet."));
            return false;
        }

        $transaction_ids = [];
        $duplicate = false;
        foreach ($_SESSION['Items']->line_items as $row) {
            if (!empty($row->transaction_id) && strtolower($row->transaction_id) != 'na') {
                if (in_array(db_escape($row->transaction_id), $transaction_ids)) {
                    $duplicate = true;
                }
                array_push($transaction_ids, db_escape($row->transaction_id));
                if (check_duplicates_row("SELECT COUNT(*) FROM " . TB_PREF . "debtor_trans_details WHERE debtor_trans_type=10 AND quantity <> 0 AND transaction_id = '$row->transaction_id' AND id != $row->id AND LOWER(transaction_id) <> 'na' ")) {
                    display_error(trans("Duplicate Transaction ID found.."));
                    return false;
                }
            }
        }
        if ($duplicate) {
            display_error(trans("Duplicate Transaction ID found"));
            return false;
        }



    } else {
        if (($_SESSION['Items']->payment_terms['days_before_due'] == -1) && !count($_SESSION['Items']->prepayments)) {
            display_error(trans("There is no non-invoiced payments for this order. If you want to issue final invoice, select delayed or cash payment terms."));
            return false;
        }
    }


    return true;
}

//-----------------------------------------------------------------------------
if (isset($_POST['process_invoice']) && check_data()) {


    $newinvoice = $_SESSION['Items']->trans_no == 0;
    copy_to_cart();

//    dd($_SESSION['Items']);

    if ($newinvoice)
        new_doc_date($_SESSION['Items']->document_date);

    $invoice_no = $_SESSION['Items']->write();
    if ($invoice_no == -1) {
        display_error(trans("The entered reference is already in use."));
        set_focus('ref');
    } else {
        processing_end();

        if ($newinvoice) {
            meta_forward($_SERVER['PHP_SELF'], "AddedID=$invoice_no");
        } else {
            meta_forward($_SERVER['PHP_SELF'], "UpdatedID=$invoice_no");
        }
    }
}

if (list_updated('payment')) {
    $order = &$_SESSION['Items'];
    copy_to_cart();
    $order->payment = get_post('payment');
    $order->payment_terms = get_payment_terms($order->payment);
    $_POST['due_date'] = $order->due_date = get_invoice_duedate($order->payment, $order->document_date);
    $_POST['Comments'] = '';
    $Ajax->activate('due_date');
    $Ajax->activate('options');
    if ($order->payment_terms['cash_sale']) {
        $_POST['Location'] = $order->Location = $order->pos['pos_location'];
        $order->location_name = $order->pos['location_name'];
    }
}

// find delivery spans for batch invoice display
$dspans = array();
$lastdn = '';
$spanlen = 1;

for ($line_no = 0; $line_no < count($_SESSION['Items']->line_items); $line_no++) {
    $line = $_SESSION['Items']->line_items[$line_no];
    if ($line->quantity == $line->qty_done) {
        continue;
    }
    if ($line->src_no == $lastdn) {
        $spanlen++;
    } else {
        if ($lastdn != '') {
            $dspans[] = $spanlen;
            $spanlen = 1;
        }
    }
    $lastdn = $line->src_no;
}
$dspans[] = $spanlen;

//-----------------------------------------------------------------------------

$is_batch_invoice = count($_SESSION['Items']->src_docs) > 1;
$prepaid = $_SESSION['Items']->is_prepaid();

$is_edition = $_SESSION['Items']->trans_type == ST_SALESINVOICE && $_SESSION['Items']->trans_no != 0;
start_form();
hidden('cart_id');

start_table(TABLESTYLE2, "width='80%'", 5);

start_row();
$colspan = 1;
$dim = get_company_pref('use_dimension');
if ($dim > 0)
    $colspan = 3;
label_cells(trans("Customer"), $_SESSION['Items']->customer_name, "class='tableheader2'");


//label_cells(trans("Branch"), get_branch_name($_SESSION['Items']->Branch), "class='tableheader2'");
label_cell(trans("Customer TRN"), "class='tableheader2'");
text_cells(null, 'customer_trn', $_SESSION['Items']->customer_trn, 15, 80);


label_cell(trans("Ref"), "class='tableheader2'");
text_cells(null, 'customer_ref', $_SESSION['Items']->customer_ref, 15, 80);


if (($_SESSION['Items']->pos['credit_sale'] || $_SESSION['Items']->pos['cash_sale'])) {
    $paymcat = !$_SESSION['Items']->pos['cash_sale'] ? PM_CREDIT :
        (!$_SESSION['Items']->pos['credit_sale'] ? PM_CASH : PM_ANY);
//	label_cells(trans("Payment terms:"), sale_payment_list('payment', $paymcat),
//		"class='tableheader2'", "colspan=$colspan");

//    display_error(print_r($_SESSION['Items']->payment ,true));

    hidden('payment', $_SESSION['Items']->payment);

} else
    label_cells(trans('Payment:'), $_SESSION['Items']->payment_terms['terms'], "class='tableheader2'", "colspan=$colspan");

end_row();
start_row();

if ($_SESSION['Items']->trans_no == 0) {
    ref_cells(trans("Reference"), 'ref', '', null, "class='tableheader2'", false, ST_SALESINVOICE,
        array('customer' => $_SESSION['Items']->customer_id,
            'branch' => $_SESSION['Items']->Branch,
            'date' => get_post('InvoiceDate')));
} else {
    label_cells(trans("Reference"), $_SESSION['Items']->reference, "class='tableheader2'");
}

//label_cells(trans("Sales Type"), $_SESSION['Items']->sales_type_name, "class='tableheader2'");


label_cell(trans("Mobile"), "class='tableheader2'");
text_cells(null, 'customer_mobile', $_SESSION['Items']->customer_mobile, 15, 80);


label_cell(trans("Email"), "class='tableheader2'");
text_cells(null, 'customer_email', $_SESSION['Items']->customer_email, 15, 80);


//label_cells(trans("Currency"), $_SESSION['Items']->customer_currency, "class='tableheader2'");
//if ($dim > 0) {
//	label_cell(trans("Dimension").":", "class='tableheader2'");
//	$_POST['dimension_id'] = $_SESSION['Items']->dimension_id;
//	dimensions_list_cells(null, 'dimension_id', null, true, ' ', false, 1, false);
//}
//else
hidden('dimension_id', 0);

end_row();
start_row();

if (!isset($_POST['ship_via'])) {
    $_POST['ship_via'] = $_SESSION['Items']->ship_via;
}
label_cell(trans("Display Customer"), "class='tableheader2'");
text_cells(null, 'display_customer', $_SESSION['Items']->display_customer, 15, 80);


if (!isset($_POST['InvoiceDate']) || !is_date($_POST['InvoiceDate'])) {
    $_POST['InvoiceDate'] = new_doc_date();
    if (!is_date_in_fiscalyear($_POST['InvoiceDate'])) {
        $_POST['InvoiceDate'] = end_fiscalyear();
    }
}

date_cells(trans("Date"), 'InvoiceDate', '', $_SESSION['Items']->trans_no == 0,
    0, 0, 0, "class='tableheader2'", true);

if (!isset($_POST['due_date']) || !is_date($_POST['due_date'])) {
    $_POST['due_date'] = get_invoice_duedate($_SESSION['Items']->payment, $_POST['InvoiceDate']);
}

//date_cells(trans("Due Date"), 'due_date', '', null, 0, 0, 0, "class='tableheader2'");
hidden('dimension2_id', 0);
if ($dim > 1) {
    label_cell(trans("Dimension") . " 2:", "class='tableheader2'");
    $_POST['dimension2_id'] = $_SESSION['Items']->dimension2_id;
    dimensions_list_cells(null, 'dimension2_id', null, true, ' ', false, 2, false);
} else
    hidden('dimension2_id', 0);
end_row();
end_table();

$row = get_customer_to_order($_SESSION['Items']->customer_id);
if ($row['dissallow_invoices'] == 1) {
    display_error(trans("The selected customer account is currently on hold. Please contact the credit control personnel to discuss."));
    end_form();
    end_page();
    exit();
}

display_heading($prepaid ? trans("Sales Order Items") : trans("Invoice Items"));

div_start('Items');

start_table(TABLESTYLE, "width='80%'");


$trans_id_one_text = "NOQODI - ID";
$trans_id_two_text = "E-Dirham - ID";

if ($_SESSION['Items']->show_items == 'ts')
    $trans_id_one_text = "E-Dirham ID";

if(!isset($_SESSION['Items']->show_items) || empty($_SESSION['Items']->show_items))
    $trans_id_one_text = "Bank Reference Number";

if ($prepaid)
    $th = array(trans("Item Code"), trans("Item Description"), trans("Units"), trans("Quantity"),
        trans("Price"), trans("Tax Type"), trans("Discount"), trans("Total"));
else
    $th = array(trans("Item Code"), trans("Item Description"), trans("Qty"), trans("Govt.Fee"),
        trans("Bank Charges"), trans("Serv. Charge"), trans("Discount"),
        trans($trans_id_one_text),
        trans($trans_id_two_text),
        trans("Transaction Date"),
        trans("Application ID"), trans("Ref Name"),trans("User"), trans("Total"));

if ($is_batch_invoice) {
    $th[] = trans("DN");
    $th[] = "";
}

if ($is_edition) {
//    $th[4] = trans("Credited");
}


//IF TASHEEL, HIDE EXTRA TRANSACTION ID INPUT
if ($_SESSION['Items']->show_items == 'ts' || !isset($_SESSION['Items']->show_items) ) {

    unset($th[8]);
}


table_header($th);
$k = 0;
$has_marked = false;
$show_qoh = true;

$dn_line_cnt = 0;


function get_debtor_trans_detail($id)
{
    $sql = "SELECT * FROM " . TB_PREF . "debtor_trans_details WHERE id=" . db_escape($id);

    $result = db_query($sql, "an debtor_trans_detail item could not be retrieved");

    return db_fetch($result);
}


$sub_total = 0;
$total_line_tax = 0;
foreach ($_SESSION['Items']->line_items as $line => $ln_itm) {


    $line_row = get_debtor_trans_detail($ln_itm->id);
    $created_by = get_user($line_row["created_by"]);
    $created_by = $created_by['user_id'];

    $sql = "SELECT * FROM 0_debtor_trans_details WHERE id=$ln_itm->id";
    $ln_details = db_fetch(db_query($sql));

    if (isset($_SESSION['Items']->is_tadbeer) && $_SESSION['Items']->is_tadbeer==1) {

        $ln_itm->govt_fee = $ln_details['govt_fee']+$ln_details['unit_price'];
//        $_SESSION['Items']->line_items[$ln_itm->id]->govt_fee = $ln_itm->govt_fee;
    }




    if (!$prepaid && ($ln_itm->quantity == $ln_itm->qty_done)) {
        continue; // this line was fully invoiced
    }
    alt_table_row_color($k);
    view_stock_status_cell($ln_itm->stock_id);

    if ($prepaid)
        label_cell($ln_itm->item_description);
    else
        text_cells(null, 'Line' . $line . 'Desc', ltrim($ln_itm->item_description,'"'), 30, 50);
    $dec = get_qty_dec($ln_itm->stock_id);
    if (!$prepaid)
        qty_cell($ln_details['quantity'], false, $dec);
//	label_cell($ln_itm->units);

    $govt_fee_display = $ln_itm->govt_fee;
    if (isset($_SESSION['Items']->is_tadbeer) && $_SESSION['Items']->is_tadbeer==1) {

        $govt_fee_display = $ln_itm->govt_fee;

    }

    if (!$prepaid)
        amount_cell($govt_fee_display);
//		qty_cell($ln_itm->qty_done, false, $dec);

    if ($is_batch_invoice || $prepaid) {
        // for batch invoices we can only remove whole deliveries
        echo '<td nowrap align=right>';
        hidden('Line' . $line, $ln_itm->qty_dispatched);
        echo number_format2($ln_itm->qty_dispatched, $dec) . '</td>';
    } else {

        amount_cell($ln_itm->bank_service_charge + $ln_itm->bank_service_charge_vat+$line_row['extra_service_charge']);
//		small_qty_cells(null, 'Line'.$line, qty_format($ln_itm->qty_dispatched, $ln_itm->stock_id, $dec), null, null, $dec);
    }
    $display_discount_percent = percent_format($ln_itm->discount_percent * 100) . " %";

    //Modified for AMER
    $line_total = (($ln_itm->qty_dispatched * $ln_itm->price) - ($ln_itm->discount_amount)) +
        ($ln_itm->govt_fee + $ln_itm->bank_service_charge + $ln_itm->bank_service_charge_vat+$line_row['extra_service_charge']);

//    pp($line_total);

if (isset($_SESSION['Items']->is_tadbeer) && $_SESSION['Items']->is_tadbeer==1) {
    $line_total = $govt_fee_display;
}

    amount_cell($ln_itm->price);
//    label_cell($ln_itm->discount_amount, "nowrap align=right");


    if (in_array($_SESSION["wa_current_user"]->access, [2, 9]) && $trans_info['alloc'] <= 0) {
        small_amount_cells(null,'Line' . $line.'discount_amount',$ln_itm->discount_amount);
    }
    else {
        label_cell($ln_itm->discount_amount, "nowrap align=right");
    }

    if ((in_array($_SESSION["wa_current_user"]->access, [2, 9,19])) ||
        empty($ln_itm->transaction_id)) {

        if($trans_info['debtor_no'] == 1 && $trans_info['alloc'] <= 0) {
            display_hidden_cells($line,$ln_itm,$line_row);
        }
        else {
            display_show_cells($line,$ln_itm,$line_row);
        }
    }
    else if($_SESSION["wa_current_user"]->access=='3')
    {

        if(date('Y-m-d',strtotime($_POST['InvoiceDate']))!=date('Y-m-d'))
        {
            display_hidden_cells($line,$ln_itm,$line_row);
        }
        else
        {
            display_show_cells($line,$ln_itm,$line_row);
        }

    }
    else {
        display_hidden_cells($line,$ln_itm,$line_row);
    }


    if (in_array($_SESSION["wa_current_user"]->access, [2, 9])) {
        users_list_cells(null, 'Line' . $line . 'user_id', $created_by, false, false);
    }
    else {

        if(empty($ln_itm->transaction_id)) {
            $created_by = $_SESSION["wa_current_user"]->loginname;
        }

        label_cell($created_by);
        hidden('Line' . $line . 'user_id',$created_by);
    }


    $sub_total += $line_total;
    $total_line_tax += $ln_itm->unit_tax*$ln_itm->quantity;

//    text_cells(null, 'transaction_id', $ln_itm->transaction_id, 10, 50);
    amount_cell($line_total);

    if ($is_batch_invoice) {
        if ($dn_line_cnt == 0) {
            $dn_line_cnt = $dspans[0];
            $dspans = array_slice($dspans, 1);
            label_cell($ln_itm->src_no, "rowspan=$dn_line_cnt class='oddrow'");
            label_cell("<a href='" . $_SERVER['PHP_SELF'] . "?RemoveDN=" .
                $ln_itm->src_no . "'>" . trans("Remove") . "</a>", "rowspan=$dn_line_cnt class='oddrow'");
        }
        $dn_line_cnt--;
    }
    end_row();
}



function display_show_cells($line,$ln_itm,$line_row) {
    text_cells(null, 'Line' . $line . 'transaction_id', $ln_itm->transaction_id, 10, 50);

    if (isset($_SESSION['Items']->show_items) &&
        ($_SESSION['Items']->show_items == 'tb')) {
        text_cells(null, 'Line' . $line . 'ed_transaction_id', $ln_itm->ed_transaction_id, 10, 50);

    }

//
   // hidden('Line' . $line . 'application_id',$line_row['application_id']);

    $_POST['Line'.$line.'transaction_id_updated_at'] = sql2date($line_row['transaction_id_updated_at']);

//    if($_GET['xx'] == 1) {
//        display_note($line_row['transaction_id_updated_at']);
//    }

//        date_cells(null,'transaction_id_updated_at');

    if($_SESSION["wa_current_user"]->access=='3')
    {
        label_cell(sql2date($line_row['transaction_id_updated_at']));
    }
    else
    {
        date_cells(null, 'Line' . $line .'transaction_id_updated_at', '');
    }
    text_cells(null, 'Line' . $line . 'application_id', $line_row['application_id'], 10, 50);

    text_cells(null, 'Line' . $line . 'ref_name', $line_row['ref_name'], 10, 50);
}

function display_hidden_cells($line,$ln_itm,$line_row) {
    label_cell($ln_itm->transaction_id);

    if (isset($_SESSION['Items']->show_items) &&
        ($_SESSION['Items']->show_items == 'tb')) {
        label_cell($ln_itm->ed_transaction_id);
    }

//        label_cell($line_row['application_id']);

    $tdu_text = "";
    if(!empty($line_row['transaction_id_updated_at'])) {
        $tdu_text = sql2date($line_row['transaction_id_updated_at']);
    }

    label_cell($tdu_text);

    label_cell($line_row['application_id']);

    hidden('Line' . $line . 'transaction_id',$ln_itm->transaction_id);
    hidden('Line' . $line . 'ed_transaction_id',$ln_itm->ed_transaction_id);
    hidden('Line' . $line . 'application_id',$line_row['application_id']);
    hidden('Line' . $line . 'ref_name',$line_row['ref_name']);
    hidden('Line' . $line . 'transaction_id_updated_at',$tdu_text);
}

/*Don't re-calculate freight if some of the order has already been delivered -
depending on the business logic required this condition may not be required.
It seems unfair to charge the customer twice for freight if the order
was not fully delivered the first time ?? */

if (!isset($_POST['ChargeFreightCost']) || $_POST['ChargeFreightCost'] == "") {
    if ($_SESSION['Items']->any_already_delivered() == 1) {
        $_POST['ChargeFreightCost'] = price_format(0);
    } else {
        $_POST['ChargeFreightCost'] = price_format($_SESSION['Items']->freight_cost);
    }

    if (!check_num('ChargeFreightCost')) {
        $_POST['ChargeFreightCost'] = price_format(0);
    }
}

$accumulate_shipping = get_company_pref('accumulate_shipping');
if ($is_batch_invoice && $accumulate_shipping)
    set_delivery_shipping_sum(array_keys($_SESSION['Items']->src_docs));

$colspan = $prepaid ? 6 : 12;

//IF TASHEEL, Reduce Cols Span, coz extra transaction id input is hidden already
if ($_SESSION['Items']->show_items == 'ts' || !isset($_SESSION['Items']->show_items) ) {
    $colspan = 11;
}

start_row();
//label_cell(trans("Shipping Cost"), "colspan=$colspan align=right");
//if ($prepaid)
//	label_cell($_POST['ChargeFreightCost'], 'align=right');
//else
//	small_amount_cells(null, 'ChargeFreightCost', null);
if ($is_batch_invoice) {
    label_cell('', 'colspan=2');
}

end_row();
//$inv_items_total = $_SESSION['Items']->get_items_total_dispatch();
$inv_items_total = $sub_total;

$display_sub_total = price_format($inv_items_total + input_num('ChargeFreightCost'));

label_row(trans("Sub-total"), $display_sub_total, "colspan=$colspan align=right", "align=right", $is_batch_invoice ? 2 : 0);

$taxes = $_SESSION['Items']->get_taxes(input_num('ChargeFreightCost'));
$tax_total = display_edit_tax_items($taxes, $colspan, $_SESSION['Items']->tax_included, $is_batch_invoice ? 2 : 0);

$display_total = price_format(($inv_items_total + input_num('ChargeFreightCost') + $tax_total));

label_row(trans("Invoice Total"), $display_total, "colspan=$colspan align=right", "align=right", $is_batch_invoice ? 2 : 0);

end_table(1);
div_end();
div_start('options');
start_table(TABLESTYLE2);
if ($prepaid) {

    label_row(trans("Sales order:"), get_trans_view_str(ST_SALESORDER, $_SESSION['Items']->order_no, get_reference(ST_SALESORDER, $_SESSION['Items']->order_no)));

    $list = array();
    $allocs = 0;
    if (count($_SESSION['Items']->prepayments)) {
        foreach ($_SESSION['Items']->prepayments as $pmt) {
            $list[] = get_trans_view_str($pmt['trans_type_from'], $pmt['trans_no_from'], get_reference($pmt['trans_type_from'], $pmt['trans_no_from']));
            $allocs += $pmt['amt'];
        }
    }

    label_row(trans("Payments received:"), implode(',', $list));
    label_row(trans("Invoiced here:"), price_format($_SESSION['Items']->prep_amount), 'class=label');
    label_row(trans("Left to be invoiced:"), price_format($_SESSION['Items']->get_trans_total() - max($_SESSION['Items']->prep_amount, $allocs)), 'class=label');
}

textarea_row(trans("Memo:"), 'Comments', null, 50, 4);

end_table(1);
div_end();
submit_center_first('Update', trans("Update"),
    trans('Refresh document page'), true);
submit_center_last('process_invoice', trans("Process Invoice"),
    trans('Check entered data and save document'), 'default');

end_form();

end_page();


//if(!isset($_POST['Update'])) { ?>

<script src="../js/jquery3.3.1.min.js"></script>
<script>

    $("#Update")
        .val("Cancel")
        .removeClass('ajaxsubmit')
        .attr("type", "button")
        .css("background-color", "#585858");

    $("#Update span").html("Cancel");

    $("#Update").click(function (e) {
        window.location.href = "inquiry/customer_inquiry.php";
    });



</script>

<?php //} ?>


<?php //if(!in_array($_SESSION["wa_current_user"]->access,[41,45,40,2])): ?>
<!--    <style>-->
<!--        input[name="display_customer"], input[name="customer_trn"], input[name="customer_mobile"],input[name="InvoiceDate"],-->
<!--        input[name="customer_ref"],input[name="customer_email"]{-->
<!--            pointer-events: none;-->
<!--            background: #ccc;-->
<!--        }-->
<!---->
<!--        .clsClander-->
<!--        {-->
<!--            display:none;-->
<!--        }-->
<!---->
<!--    </style>-->
<?php //endif; ?>


<?php if(!in_array($_SESSION["wa_current_user"]->access,[2])): ?>
    <style>
        input[name="InvoiceDate"],
        input[name="customer_ref"]{
            pointer-events: none;
            background: #ccc;
        }

        .clsClander
        {
            display:none;
        }

    </style>

<?php endif; ?>

