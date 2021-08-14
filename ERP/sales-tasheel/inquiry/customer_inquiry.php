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
$page_security = 'SA_SALESTRANSVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/sales-tasheel/includes/sales_ui.inc");
include_once($path_to_root . "/sales-tasheel/includes/sales_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(900, 500);
if (user_use_date_picker())
    $js .= get_js_date_picker();
page(trans($help_context = "Customer Transactions"), isset($_GET['customer_id']), false, "", $js);

//------------------------------------------------------------------------------------------------

function systype_name($dummy, $type)
{
    global $systypes_array;

    return $systypes_array[$type];
}

function order_view($row)
{
    return $row['order_'] > 0 ?
        get_customer_trans_view_str(ST_SALESORDER, $row['order_'])
        : "";
}

function trans_view($trans)
{
    return get_trans_view_str($trans["type"], $trans["trans_no"]);
}

function due_date($row)
{
    return $row["type"] == ST_SALESINVOICE ? $row["due_date"] : '';
}

function gl_view($row)
{
    return get_gl_view_str($row["type"], $row["trans_no"]);
}

function fmt_amount($row)
{
    $value =
        $row['type'] == ST_CUSTCREDIT || $row['type'] == ST_CUSTPAYMENT || $row['type'] == ST_BANKDEPOSIT ?
            -$row["TotalAmount"] : $row["TotalAmount"];
    return price_format($value);
}

function credit_link($row)
{
    global $page_nested;

    if ($page_nested)
        return '';
    return $row['type'] == ST_SALESINVOICE && $row["Outstanding"] > 0 ?
        pager_link(trans("Credit This"),
            "/sales-tasheel/customer_credit_invoice.php?InvoiceNumber=" . $row['trans_no'], ICON_CREDIT) : '';
}

function edit_link($row)
{
    global $page_nested;

    $str = '';
    if ($page_nested)
        return '';

    return $row['type'] == ST_CUSTCREDIT && $row['order_'] ? '' :    // allow  only free hand credit notes edition
        trans_editor_link($row['type'], $row['trans_no']);
}


function invoice_edit_link($row)
{
    global $SysPrefs;
    global $path_to_root;

    if (!$SysPrefs->enable_invoice_editing || !in_array($_SESSION["wa_current_user"]->access, $SysPrefs->invoice_edit_permitted_roles))
        return false;

    global $page_nested;
    $str = '';
    if ($page_nested)
        return '';


    if (user_graphic_links() && ICON_DOC)
        $link_text = set_icon(ICON_DOC, trans("Edit"));

    $href = $path_to_root . "/sales-tasheel/sales_order_entry.php?EditFlag=true&NewInvoice=" . $row['order_'];
    return "<a  onclick=\"return confirm('Are you sure?')\" href='$href'>" . $link_text . "</a>";

//    return pager_link(trans("Edit"), , ICON_DOC,"invoice_edit_click(this)");

}

function prt_link($row)
{
    if ($row['type'] == ST_CUSTPAYMENT || $row['type'] == ST_BANKDEPOSIT)
        return print_document_link($row['trans_no'] . "-" . $row['type'], trans("Print Receipt"), true, ST_CUSTPAYMENT, ICON_PRINT);
    elseif ($row['type'] == ST_BANKPAYMENT) // bank payment printout not defined yet.
        return '';
    else
        return print_document_link($row['trans_no'] . "-" . $row['type'], trans("Print"), true, $row['type'], ICON_PRINT);
}

function check_overdue($row)
{
    return $row['OverDue'] == 1
        && floatcmp($row["TotalAmount"], $row["Allocated"]) != 0;
}

//------------------------------------------------------------------------------------------------

function display_customer_summary($customer_record)
{
    $past1 = get_company_pref('past_due_days');
    $past2 = 2 * $past1;
    if ($customer_record["dissallow_invoices"] != 0) {
        echo "<center><font color=red size=4><b>" . trans("CUSTOMER ACCOUNT IS ON HOLD") . "</font></b></center>";
    }

    $nowdue = "1-" . $past1 . " " . trans('Days');
    $pastdue1 = $past1 + 1 . "-" . $past2 . " " . trans('Days');
    $pastdue2 = trans('Over') . " " . $past2 . " " . trans('Days');

    start_table(TABLESTYLE, "width='80%'");
    $th = array(trans("Currency"), trans("Terms"), trans("Current"), $nowdue,
        $pastdue1, $pastdue2, trans("Total Balance"));
    table_header($th);

    start_row();
    label_cell($customer_record["curr_code"]);
    label_cell($customer_record["terms"]);
    amount_cell($customer_record["Balance"] - $customer_record["Due"]);
    amount_cell($customer_record["Due"] - $customer_record["Overdue1"]);
    amount_cell($customer_record["Overdue1"] - $customer_record["Overdue2"]);
    amount_cell($customer_record["Overdue2"]);
    amount_cell($customer_record["Balance"]);
    end_row();

    end_table();
}

if (isset($_GET['customer_id'])) {
    $_POST['customer_id'] = $_GET['customer_id'];
}

//------------------------------------------------------------------------------------------------

start_form();

if (!isset($_POST['customer_id']))
    $_POST['customer_id'] = get_global_customer();

start_table(TABLESTYLE_NOBORDER);
start_row();


text_cells("Invoice No.", "inv_no", null, 11);

if (!$page_nested)
    customer_list_cells(trans("Select a customer: "), 'customer_id', null, true, true, false, true);

cust_allocations_list_cells(null, 'filterType', 1, true, 'style="display:none"');


if ($_POST['filterType'] != '2') {
    date_cells(trans("From:"), 'TransAfterDate', '', null, -user_transaction_days());
    date_cells(trans("To:"), 'TransToDate', '', null);
}

submit_cells('RefreshInquiry', trans("Search"), '', trans('Refresh Inquiry'), 'default');
end_row();
end_table();

set_global_customer($_POST['customer_id']);

//------------------------------------------------------------------------------------------------

div_start('totals_tbl');
if ($_POST['customer_id'] != "" && $_POST['customer_id'] != ALL_TEXT) {
    $customer_record = get_customer_details(get_post('customer_id'), get_post('TransToDate'));
    display_customer_summary($customer_record);
    echo "<br>";
}
div_end();

if (get_post('RefreshInquiry') || list_updated('filterType')) {
    $Ajax->activate('_page_body');
}
//------------------------------------------------------------------------------------------------
$sql = get_sql_for_customer_inquiry(get_post('TransAfterDate'), get_post('TransToDate'),
    get_post('customer_id'), get_post('filterType'), get_post('inv_no'));

//------------------------------------------------------------------------------------------------
//db_query("set @bal:=0");

$cols = array(
    trans("Type") => array('fun' => 'systype_name', 'ord' => ''),
    trans("#") => array('fun' => 'trans_view', 'ord' => '', 'align' => 'right'),
    trans("Order") => array('fun' => 'order_view', 'align' => 'right'),
    trans("Reference"),
    trans("Date") => array('name' => 'tran_date', 'type' => 'date', 'ord' => 'desc'),
    trans("Due Date") => array('type' => 'date', 'fun' => 'due_date'),
    trans("Customer") => array('ord' => ''),
    trans("Branch") => array('ord' => ''),
    trans("Currency") => array('align' => 'center'),
    trans("Amount") => array('align' => 'right', 'fun' => 'fmt_amount'),
    trans("Balance") => array('align' => 'right', 'type' => 'amount'),
    array('insert' => true, 'fun' => 'gl_view'),
    array('insert' => true, 'fun' => 'invoice_edit_link'),
    array('insert' => true, 'fun' => 'credit_link'),
    array('insert' => true, 'fun' => 'edit_link'),
    array('insert' => true, 'fun' => 'prt_link')
);


if ($_POST['customer_id'] != ALL_TEXT) {
    $cols[trans("Customer")] = 'skip';
    $cols[trans("Currency")] = 'skip';
}
if ($_POST['filterType'] != '2')
    $cols[trans("Balance")] = 'skip';

$table =& new_db_pager('trans_tbl', $sql, $cols);
$table->set_marker('check_overdue', trans("Marked items are overdue."));

$table->width = "85%";

display_db_pager($table);

end_form();
end_page();


echo "<script>

 if(performance.navigation.type == 2){
        location.reload(true);
    }
    
    function invoice_edit_click(x) {
     alert(x);
     return false;
    }
</script>";
