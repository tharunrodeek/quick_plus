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
page(_($help_context = "Customer Balance Summary"), false, false, "", $js);

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

if(empty($_POST['filter_date_from']))
    $_POST['filter_date_from'] = begin_fiscalyear();

date_cells("Date FROM", 'filter_date_from', _('Filter by Date'),
    false, 0, 0, 0, null, false);

date_cells("Date TO", 'filter_date_to', _('Filter by Date'),
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



//------------------------------------------------------------------------------------------------
function get_the_sql()
{
    $customer_id = get_post('customer_id');
    $date_from = date2sql(get_post('filter_date_from'));
    $date_to = date2sql(get_post('filter_date_to'));

    $where = "";

    if(!empty($customer_id))
        $where .= " AND dt.debtor_no=$customer_id";

    if(!empty($date_from))
        $where .= " AND dt.tran_date >= ".db_escape($date_from);

    if(!empty($date_to))
        $where .= " AND dt.tran_date <= ".db_escape($date_to);

    $sql = "SELECT dm.name AS customer, ROUND(SUM(ov_amount+ov_gst),2) AS invoice_total, 
            ROUND(SUM(dt.alloc),2) AS total_allocated,
            ROUND(SUM(ov_amount+ov_gst)- SUM(dt.alloc),2) AS balance
            FROM 0_debtor_trans dt
            LEFT JOIN 0_debtors_master dm ON dm.debtor_no=dt.debtor_no
            WHERE dt.`type`=10 $where 
            GROUP BY dt.debtor_no order by balance desc";

    return $sql;

}

$sql = get_the_sql();
//------------------------------------------------------------------------------------------------
$cols = array(
    _("Customer Name") => array('align' => 'center'),
    _("Total Invoice Amount") => array('align' => 'center'),
    _("Total Invoice Payment") => array('align' => 'center'),
    _("Balance") => array('align' => 'center'),
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
$Ajax->activate("PARAM_2");


start_form(false, false, $path_to_root . "/reporting/reports_main.php", "export_from");
hidden("Class", "6");
hidden("REP_ID", "1007");
hidden("PARAM_0", $_POST['customer_id']);
hidden("PARAM_1", $_POST['filter_date_from']);
hidden("PARAM_2", $_POST['filter_date_to']);

echo array_selector("DESTINATION", null, ["Export to PDF", "Export to EXCEL"]);
br(2);

submit_cells('Rep1007', _("EXPORT"), '', "Export to PDF or EXCEL", 'default');

end_form();

/** END -- EXPORT */

end_page();

?>

<style>
    form[name="export_from"] {
        text-align: center;
    }
</style>

