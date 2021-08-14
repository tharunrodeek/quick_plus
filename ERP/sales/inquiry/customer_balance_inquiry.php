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
$page_security = 'SA_SALESALLOC';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(900, 500);
if (user_use_date_picker())
    $js .= get_js_date_picker();
page(_($help_context = "Customer Balance Inquiry"), false, false, "", $js);

if (isset($_GET['customer_id'])) {
    $_POST['customer_id'] = $_GET['customer_id'];
}

//------------------------------------------------------------------------------------------------

if (!isset($_POST['customer_id']))
    $_POST['customer_id'] = null;


start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
customer_list_cells(_("Select a customer: "), 'customer_id', $_POST['customer_id'], true);



set_global_customer($_POST['customer_id']);

end_row();

start_row();

//if(empty($_POST['filter_date_from']))
//    $_POST['filter_date_from'] = begin_fiscalyear();

//date_cells("Date FROM", 'filter_date_from', _('Filter by Date'),
//    false, 0, 0, 0, null, false);

date_cells("Till Date", 'filter_date_to', _('Filter by Date'),
    false, 0, 0, 0, null, false);

submit_cells('RefreshInquiry', _("Search"), '', _('Refresh Inquiry'), 'default');

end_row();

end_table();
//------------------------------------------------------------------------------------------------


function systype_name($dummy, $type)
{
    global $systypes_array;

    return $systypes_array[$type];
}

function check_redeemed($row)
{
    return false;
}

function fmt_prepaid_bal($row)
{

    $bal_info = get_customer_total_balance($row['debtor_no']);
    $_SESSION['tmp_bal_info'] = $bal_info;
    return number_format2($bal_info['prepaid'],2);
}

function fmt_pending_bal($row)
{
    return number_format2($_SESSION['tmp_bal_info']['pending'],2);
}

function fmt_balance_amount($row)
{
    $amount = ($_SESSION['tmp_bal_info']['pending']) - ($_SESSION['tmp_bal_info']['prepaid']);
    return $amount < 0 ? number_format2(abs($amount),2) : "0.00";
}

function fmt_out_standing_amount($row)
{
    $amount = ($_SESSION['tmp_bal_info']['pending']) - ($_SESSION['tmp_bal_info']['prepaid']);
    return $amount > 0 ? number_format2(abs($amount),2) : "0.00";
}

function fmt_total_charges($row) {

    $bal_info = get_customer_total_balance($row['debtor_no']);
    $_SESSION['tmp_bal_info'] = $bal_info;
    return number_format2($bal_info['total_charges'],2);

//    return number_format2($_SESSION['tmp_bal_info']['total_charges'],2);
}
function fmt_total_allocated($row) {
    return number_format2($_SESSION['tmp_bal_info']['total_allocated'],2);
}


function get_customer_total_balance($customer_id)
{

    $date_from = begin_fiscalyear();
    $date_to = $_POST['filter_date_to'];

    $sql = get_sql_for_customer_allocation_inquiry($date_from, $date_to,
        $customer_id, null, null);

    $result = db_query($sql, "could not get customer");
    $prepaid_bal = 0;
    $out_standing_bal = 0;

    $total_charges = 0;
    $total_allocated = 0;

    while ($row = db_fetch($result)) {
        $balance = ($row["type"] == ST_JOURNAL && $row["TotalAmount"] < 0 ? -$row["TotalAmount"] :
                $row["TotalAmount"]) - $row["Allocated"];


        $total_charges+=$row["TotalAmount"];
        $total_allocated+=$row["Allocated"];

        if ($row["type"] == ST_CUSTCREDIT && $row['TotalAmount'] > 0) {
            /*its a credit note which could have an allocation */
            $prepaid_bal += $balance;
        } elseif ($row["type"] == ST_JOURNAL && $row['TotalAmount'] < 0) {
            $prepaid_bal += $balance;


        }

        elseif (($row["type"] == ST_CUSTPAYMENT || $row["type"] == ST_BANKDEPOSIT) &&
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

    return ["prepaid" => $prepaid_bal, "pending" => $out_standing_bal,"total_charges" => $total_charges-$prepaid_bal,
        "total_allocated" => $total_allocated];

}

//------------------------------------------------------------------------------------------------
function get_the_sql()
{
    $customer_id = get_post('customer_id');
    $sql = "select name,debtor_no from 0_debtors_master WHERE 1=1 ";
    if (!empty($customer_id)) {
        $sql .= " AND debtor_no=" . db_escape($customer_id);
    }
    return $sql;

}

$sql = get_the_sql();
//------------------------------------------------------------------------------------------------
$cols = array(
    _("Customer Name") => array('align' => 'center'),
    _("Total Charges") => array('align' => 'center', 'fun' => 'fmt_total_charges'),
    _("Total Allocated") => array('align' => 'center', 'fun' => 'fmt_total_allocated'),
    _("Prepaid") => array('align' => 'center', 'fun' => 'fmt_prepaid_bal'),
    _("Pending payment") => array('fun' => 'fmt_pending_bal', 'align' => 'center'),
    _("Balance") => array('fun' => 'fmt_balance_amount', 'align' => 'center'),
    _("Outstanding") => array('fun' => 'fmt_out_standing_amount', 'align' => 'center'),
    array('insert' => true, 'fun' => 'alloc_link')
);


$table =& new_db_pager('trans_tbl', $sql, $cols);
$table->set_marker('check_redeemed', _(""));

$table->width = "80%";

display_db_pager($table);

unset($_SESSION['tmp_bal_info']);

end_form();

/** EXPORT */
$Ajax->activate("PARAM_0");
$Ajax->activate("PARAM_1");


start_form(false, false, $path_to_root . "/reporting/reports_main.php", "export_from");
hidden("Class", "6");
hidden("REP_ID", "1003");
hidden("PARAM_0", $_POST['customer_id']);
hidden("PARAM_1", $_POST['filter_date_to']);

echo array_selector("DESTINATION", null, ["Export to PDF", "Export to EXCEL"]);
br(2);

submit_cells('Rep1003', _("EXPORT"), '', "Export to PDF or EXCEL", 'default');

end_form();

/** END -- EXPORT */

end_page();

?>

<style>
    form[name="export_from"] {
        text-align: center;
    }
</style>

