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
$page_security = 'SA_VOIDTRANSACTION';
$path_to_root = "..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/admin/db/transactions_db.inc");

include_once($path_to_root . "/admin/db/voiding_db.inc");

include_once($path_to_root . "/reporting/includes/reporting.inc");


$js = "";
if (user_use_date_picker())
    $js .= get_js_date_picker();
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(800, 500);

page(trans($help_context = "Void a Transaction"), false, false, "", $js);

simple_page_mode(true);
//----------------------------------------------------------------------------------------
function exist_transaction($type, $type_no)
{
    $void_entry = get_voided_entry($type, $type_no);

    if ($void_entry != null)
        return false;

    switch ($type) {
        case ST_JOURNAL : // it's a journal entry
            if (!exists_gl_trans($type, $type_no))
                return false;
            break;

        case ST_BANKPAYMENT : // it's a payment
        case ST_BANKDEPOSIT : // it's a deposit
        case ST_BANKTRANSFER : // it's a transfer
            if (!exists_bank_trans($type, $type_no))
                return false;
            break;

        case ST_SALESINVOICE : // it's a customer invoice
        case ST_CUSTCREDIT : // it's a customer credit note
        case ST_CUSTPAYMENT : // it's a customer payment
        case ST_CUSTDELIVERY : // it's a customer dispatch
            if (!exists_customer_trans($type, $type_no))
                return false;
            break;

        case ST_LOCTRANSFER : // it's a stock transfer
            if (get_stock_transfer_items($type_no) == null)
                return false;
            break;

        case ST_INVADJUST : // it's a stock adjustment
            if (get_stock_adjustment_items($type_no) == null)
                return false;
            break;

        case ST_PURCHORDER : // it's a PO
            return false;

        case ST_SUPPRECEIVE : // it's a GRN
            if (!exists_grn($type_no))
                return false;
            break;

        case ST_SUPPINVOICE : // it's a suppler invoice
        case ST_SUPPCREDIT : // it's a supplier credit note
        case ST_SUPPAYMENT : // it's a supplier payment
            if (!exists_supp_trans($type, $type_no))
                return false;
            break;

        case ST_WORKORDER : // it's a work order
            if (!get_work_order($type_no, true))
                return false;
            break;

        case ST_MANUISSUE : // it's a work order issue
            if (!exists_work_order_issue($type_no))
                return false;
            break;

        case ST_MANURECEIVE : // it's a work order production
            if (!exists_work_order_produce($type_no))
                return false;
            break;

        case ST_SALESORDER: // it's a sales order
        case ST_SALESQUOTE: // it's a sales quotation
            return false;
        case ST_COSTUPDATE : // it's a stock cost update
            return false;
    }

    return true;
}

function view_link($trans)
{
    if (!isset($trans['type']))
        $trans['type'] = $_POST['filterType'];
    return get_trans_view_str($trans["type"], $trans["trans_no"]);
}

function select_link($row)
{
    if (!isset($row['type']))
        $row['type'] = $_POST['filterType'];
    if (!is_date_in_fiscalyear($row['trans_date'], true))
        return trans("N/A");
    return button('Edit' . $row["trans_no"], trans("Select"), trans("Select"), ICON_EDIT);
}

function gl_view($row)
{
    if (!isset($row['type']))
        $row['type'] = $_POST['filterType'];
    return get_gl_view_str($row["type"], $row["trans_no"]);
}

function date_view($row)
{
    return $row['trans_date'];
}

function ref_view($row)
{
    return $row['ref'];
}

function selected_row($row)
{
    global $selected_id;
//    pp($selected_id);
    return $selected_id == $row["trans_no"];
}

function print_trans_link($row)
{
    if ($row['type'] == ST_CUSTPAYMENT || $row['type'] == ST_BANKDEPOSIT)
        return print_document_link($row['trans_no'] . "-" . $row['type'], trans("Print Receipt"), true, ST_CUSTPAYMENT, ICON_PRINT);
    elseif ($row['type'] == ST_BANKPAYMENT) // bank payment printout not defined yet.
        return '';
    else
        return print_document_link($row['trans_no'] . "-" . $row['type'], trans("Print"), true, $row['type'], ICON_PRINT);
}

function voiding_controls()
{
    global $selected_id;

//	$not_implemented =  array(ST_PURCHORDER, ST_SALESORDER, ST_SALESQUOTE, ST_COSTUPDATE);
    $not_implemented = array(
        ST_PURCHORDER, ST_SALESORDER, ST_SALESQUOTE, ST_COSTUPDATE,
         ST_CUSTCREDIT, ST_DIMENSION,
        ST_CUSTDELIVERY, ST_LOCTRANSFER, ST_INVADJUST, ST_WORKORDER, ST_CHEQUE, ST_PURCHORDER,
        ST_SUPPCREDIT, ST_SUPPRECEIVE, 28, 29
    );

    start_form();

    start_table(TABLESTYLE_NOBORDER);
    start_row();

    $_POST['filterType'] = ($_POST['filterType'] == '') ? '10' : $_POST['filterType'];

    systypes_list_cells(trans("Transaction Type:"), 'filterType', null, true, $not_implemented);
    if (list_updated('filterType'))
        $selected_id = -1;

    if (!isset($_POST['FromTransNo']))
        $_POST['FromTransNo'] = "1";
    if (!isset($_POST['ToTransNo']))
        $_POST['ToTransNo'] = "999999";

    text_cells("Reference No", 'ref_no', null);

    ref_cells(trans("from #:"), 'FromTransNo', null, null, "style='display:none'");

    ref_cells(trans("to #:"), 'ToTransNo');

    submit_cells('ProcessSearch', trans("Search"), '', '', 'default');

    end_row();
    end_table(1);


    //

    if ($selected_id != -1) {
        start_table(TABLESTYLE2);

        if ($selected_id != -1) {
            hidden('trans_no', $selected_id);
            hidden('selected_id', $selected_id);
        } else {
            hidden('trans_no', '');
            $_POST['memo_'] = '';
        }



        $print_link = "";
        $reference = "";

        if($selected_id != -1) {
            $print_link = print_trans_link(['type' => get_post('filterType'),'trans_no' => $selected_id]);
            $reference = get_reference(get_post('filterType'),$selected_id);
        }


        label_row(trans("Transaction #:"), $reference.$print_link);

        date_row(trans("Voiding Date:"), 'date_');


//        if (get_post('filterType') == ST_SALESINVOICE)
//            check_row(trans("Clear Payment Transactions ?"), 'clear_payments', null);

        textarea_row(trans("Memo:"), 'memo_', null, 30, 4);

        end_table(1);

        if (!isset($_POST['ProcessVoiding']))
            submit_center('ProcessVoiding', trans("Void Transaction"), true, '', 'default');
        else {
            if (!exist_transaction($_POST['filterType'], $_POST['trans_no'])) {
                display_error(trans("The entered transaction does not exist or cannot be voided."));
                unset($_POST['trans_no']);
                unset($_POST['memo_']);
                unset($_POST['date_']);
                submit_center('ProcessVoiding', trans("Void Transaction"), true, '', 'default');
            } else {
                display_warning(trans("Are you sure you want to void this transaction ? This action cannot be undone."), 0, 1);
                br();
                submit_center_first('ConfirmVoiding', trans("Proceed"), '', true);
                submit_center_last('CancelVoiding', trans("Cancel"), '', 'cancel');
            }
        }

    }


    if ($_POST['ref_no'] != "")
        $trans_ref_no = $_POST['ref_no'];


//    get_tran


    br();

    $trans_ref = false;
    $sql = get_sql_for_view_transactions(get_post('filterType'), get_post('FromTransNo'), get_post('ToTransNo'), $trans_ref, $trans_ref_no);

//            display_error(print_r($sql ,true)); die;


    if ($sql == "")
        return;

    $cols = array(
        trans("#") => array('insert' => true, 'fun' => 'view_link'),
        trans("Reference") => array('fun' => 'ref_view'),
        trans("Date") => array('type' => 'date', 'fun' => 'date_view'),
        trans("GL") => array('insert' => true, 'fun' => 'gl_view'),
        trans("Select") => array('insert' => true, 'fun' => 'select_link'),
        trans("Print") => array('align' => 'center', 'insert' => true, 'fun' => 'print_trans_link')

    );

    $table =& new_db_pager('transactions', $sql, $cols);

    $table->set_marker('selected_row', '');

    $table->width = "40%";
    display_db_pager($table);


    end_form();
}

//----------------------------------------------------------------------------------------

function check_valid_entries()
{
    if (is_closed_trans($_POST['filterType'], $_POST['trans_no'])) {
        display_error(trans("The selected transaction was closed for edition and cannot be voided."));
        set_focus('trans_no');
        return false;
    }
    if (!is_date($_POST['date_'])) {
        display_error(trans("The entered date is invalid."));
        set_focus('date_');
        return false;
    }
    if (!is_date_in_fiscalyear($_POST['date_'])) {
        display_error(trans("The entered date is out of fiscal year or is closed for further data entry."));
        set_focus('date_');
        return false;
    }

    if (!is_numeric($_POST['trans_no']) OR $_POST['trans_no'] <= 0) {
        display_error(trans("The transaction number is expected to be numeric and greater than zero."));
        set_focus('trans_no');
        return false;
    }


    if (empty(trim($_POST['memo_']))) {

        display_error(trans("Memo is mandatory"));
        set_focus('memo_');
        return false;

    }

    return true;
}


//function delete_reward_table_entry($trans_no,$trans_type) {
//
//}

//----------------------------------------------------------------------------------------

function handle_void_transaction()
{
    if (check_valid_entries() == true) {
        $void_entry = get_voided_entry($_POST['filterType'], $_POST['trans_no']);
        if ($void_entry != null) {
            display_error(trans("The selected transaction has already been voided."), true);
            unset($_POST['trans_no']);
            unset($_POST['memo_']);
            unset($_POST['date_']);
            set_focus('trans_no');
            return;
        }

        $msg = void_transaction($_POST['filterType'], $_POST['trans_no'],
            $_POST['date_'], $_POST['memo_']);


        if (!$msg) {
            display_notification_centered(trans("Selected transaction has been voided."));
            unset($_POST['trans_no']);
            unset($_POST['memo_']);
        } else {
            display_error($msg);
            set_focus('trans_no');

        }
    }
}

//----------------------------------------------------------------------------------------

if (!isset($_POST['date_'])) {
    $_POST['date_'] = Today();
    if (!is_date_in_fiscalyear($_POST['date_']))
        $_POST['date_'] = end_fiscalyear();
}

if (isset($_POST['ProcessVoiding'])) {
    if (!check_valid_entries())
        unset($_POST['ProcessVoiding']);
    $Ajax->activate('_page_body');
}

if (isset($_POST['ConfirmVoiding'])) {
    handle_void_transaction();
    $selected_id = '';
    $Ajax->activate('_page_body');
}

if (isset($_POST['CancelVoiding'])) {
    $selected_id = -1;
    $Ajax->activate('_page_body');
}

//----------------------------------------------------------------------------------------

voiding_controls();

end_page();

?>


<style>

    .tablestyle2 {
        border: 1px solid #22664b !important;
    }

</style>

