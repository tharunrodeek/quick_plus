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
page(_($help_context = "Invoice Collection Report"), false, false, "", $js);

if (isset($_GET['customer_id'])) {
    $_POST['customer_id'] = $_GET['customer_id'];
}

if (isset($_GET['user_id'])) {
    $_POST['user_id'] = $_GET['user_id'];
}

if (isset($_GET['bank_account'])) {
    $_POST['bank_account'] = $_GET['bank_account'];
}

//------------------------------------------------------------------------------------------------

if (!isset($_POST['customer_id']))
    $_POST['customer_id'] = null;

if (!isset($_POST['user_id']))
    $_POST['user_id'] = null;

if (!isset($_POST['bank_account']))
    $_POST['bank_account'] = get_default_bank_account('AED');

if (!isset($_POST['pay_method']))
    $_POST['pay_method'] = null;

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
customer_list_cells(_("Select a customer: "), 'customer_id', $_POST['customer_id'], true);
users_list_cells(_("Select a user: "), 'user_id', $_POST['user_id'], true);
//payment_method_cell("Payment Method",'pay_method',$_POST['pay_method']);

end_row();
start_row();
bank_accounts_list_cells("Bank Account", 'bank_account', $_POST['bank_account'], false);
date_cells(_("from:"), 'TransAfterDate', '', null);
date_cells(_("to:"), 'TransToDate', '', null);

submit_cells('RefreshInquiry', _("Search"), '', _('Refresh Inquiry'), 'default');

set_global_customer($_POST['customer_id']);

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

function fmt_format_inv($row)
{
//    display_error(wordwrap($row['invoice_numbers'],1,"<br>\n")); die;
    return wordwrap($row['invoice_numbers'], 32, "<br>\n");
}


function payment_method_cell($label,$name,$selected_id=null)
{
    echo "<td>$label</td>
            <td>" . array_selector(
            $name, $selected_id,
            [
                "" => "All",
                "Cash"=>"Cash",
                "CreditCard"=>"CreditCard",
            ]
        ) . "</td>";

}


//------------------------------------------------------------------------------------------------

$customer_id = get_post('customer_id');
$user_id = get_post('user_id');
$from = get_post('TransAfterDate');
$to = get_post('TransToDate');
$bank = get_post('bank_account');

$data_after = date2sql($from);
$date_to = date2sql($to);

$sql = get_sql_for_invoice_payment_inquiry($customer_id,$user_id,$data_after,$date_to,$bank);
//display_error($sql);
//------------------------------------------------------------------------------------------------
$cols = array(
    _("Date") => array('align' => 'center'),
    _("Receipt No") => array('align' => 'center'),
    _("Invoice Numbers allocated in this receipt") => array('fun' => 'fmt_format_inv', 'align' => 'center'),
    _("Sum of Invoices") => array('align' => 'center'),
    _("Total Discount or Reward Point") => array('align' => 'center'),
    _("Net Payment Received") => array('align' => 'center'),
    _("Collected Bank") => array('align' => 'center'),
    _("Customer") => array('align' => 'center'),
    _("User") => array('align' => 'center'),
    _("Pay.Method") => array('align' => 'center'),
    array('insert' => true, 'fun' => 'alloc_link')
);


$table =& new_db_pager('trans_tbl', $sql, $cols);

$gs_total_result = db_query("select ROUND(sum(net_payment),2) as total_net_payment from ($sql) as MyTable", "Transactions could not be calculated");
$gs_total_row = db_fetch($gs_total_result);
$table->set_marker('check_redeemed', _("Gross Total : " . $gs_total_row['total_net_payment']));

$table->width = "80%";

display_db_pager($table);

end_form();


/** EXPORT */
$Ajax->activate("PARAM_0");
$Ajax->activate("PARAM_1");
$Ajax->activate("PARAM_6");
$Ajax->activate("PARAM_7");
$Ajax->activate("PARAM_8");


start_form(false, false, $path_to_root . "/reporting/reports_main.php", "export_from");
hidden("Class", "6");
hidden("REP_ID", "1000");
hidden("PARAM_0", $_POST['TransAfterDate']);
hidden("PARAM_1", $_POST['TransToDate']);
hidden("PARAM_2", "0");
hidden("PARAM_3", "");
hidden("PARAM_4", "0");
hidden("PARAM_6", $_POST["customer_id"]);
hidden("PARAM_7", $_POST["bank_account"]);
hidden("PARAM_8", $_POST["user_id"]);
//hidden("PARAM_5", "0");

echo array_selector("PARAM_5", null, ["Export to PDF", "Export to EXCEL"]);
br(2);

submit_cells('Rep1000', _("EXPORT"), '', "Export to PDF or EXCEL", 'default');

end_form();

/** END -- EXPORT */

end_page();

?>

<style>
    form[name="export_from"] {
        text-align: center;
    }
</style>
