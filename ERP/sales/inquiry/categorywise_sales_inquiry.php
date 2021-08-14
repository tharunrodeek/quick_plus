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
/**********************************************************************
 * Page for searching item list and select it to item selection
 * in pages that have the item dropdown lists.
 * Author: bogeyman2007 from Discussion Forum. Modified by Joe Hunt
 ***********************************************************************/
$page_security = "SA_SALESANALYTIC";

$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/inventory/includes/db/items_db.inc");

$js = "";

if (user_use_date_picker()) {
    $js .= get_js_date_picker();
}

page(trans($help_context = "Category wise Sales Inquiry"), false, false, "", $js);

if (get_post("search")) {
    $Ajax->activate("item_tbl");
}

if (!isset($_POST['customer_id']))
    $_POST['customer_id'] = null;

start_form(false, false, $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);

start_table(TABLESTYLE_NOBORDER);

start_row();

//customer_list_cells(trans("Select a customer: "), 'customer_id', $_POST['customer_id'], true);

date_cells(trans("from:"), 'TransAfterDate', '', null);
date_cells(trans("to:"), 'TransToDate', '', null);

customer_list_row(trans('Customer'),'customer_id',null,true);

dimensions_list_cells(trans('Cost Center'),'cost_center',null,true,'--All--');


submit_cells("search", trans("Search"), "", trans("Search items"), "default");

end_row();

end_table();

end_form();

div_start("item_tbl");
start_table(TABLESTYLE);
$sql_extra = [];
$th = array(trans("Category"), trans("Count"),trans("Total Govt. Fee"),
    trans("Total Service Charge"),
    trans("Round Off/Extra Charge"),
    trans("Tax"),trans("Total"),
    trans("P.R.O Discount"),trans('Customer Commission'), trans("Net Service Charge"));
table_header($th);
$k = 0;
$where = "";
//$fdate = date2sql($_POST['filter_date']);

$from = get_post('TransAfterDate');
$to = get_post('TransToDate');
$cost_center = get_post('cost_center');

if (empty($from) || empty($to)) {
    display_error(trans('Please provide a date'));
    exit;
}

$date_from = date2sql($from);
$date_to = date2sql($to);

//$sql = "select 0_stock_category.description,
//SUM(quantity) AS inv_count,
//SUM((quantity*unit_price)-
//(quantity*discount_amount) - (IFNULL(reward_amount,0)) -
//IFNULL((((`unit_price` * `total_customer_commission`) / 100) * `quantity`),0) -
//(`pf_amount` * `quantity`)) AS net_service_charge from invoice_report_detail_view
//LEFT JOIN 0_stock_category ON 0_stock_category.category_id=invoice_report_detail_view.category_id
//where 1=1 ";
//
//$sql .= " and transaction_date>='$date_from' AND transaction_date <= '$date_to'";
//$sql .= " group by invoice_report_detail_view.category_id";

$sql = get_sql_for_categorywise_sales_inquiry($date_from,$date_to,$cost_center);

$result = db_query($sql, "Transactions could not be calculated");


$i = 0;
$current_loop = null;

$total_service_qty = 0;
$total_net_service_charge = 0;
$total_service_charge = 0;
$total_pro_discount = 0;
$total_govt_fee = 0;
$total_tax = 0;
$total_customer_commission = 0;
$total_invoice_amount = 0;
$total_sum=  0;
$total_extra_service_charge = 0;

while ($myrow = db_fetch_assoc($result)) {
    alt_table_row_color($k);

    $total_service_qty += $myrow["total_service_count"];
    $total_service_charge += $myrow["total_service_charge"];
    $total_net_service_charge += $myrow["net_service_charge"];
    $total_pro_discount += $myrow["total_pro_discount"];
    $total_govt_fee += $myrow["total_govt_fee"];
    $total_tax += $myrow["total_tax"];
    $total_customer_commission += $myrow["total_customer_commission"];
    $total_invoice_amount += $myrow["total_invoice_amount"];
    $total_extra_service_charge += $myrow["total_extra_service_charge"];

    $total = $myrow["total_service_charge"]+$myrow["total_govt_fee"]+$myrow["total_extra_service_charge"]+$myrow["total_tax"];

    $total_sum +=$total;

    label_cell($myrow["description"], "style='text-align:center'");
    label_cell($myrow["total_service_count"], "style='text-align:center'");
    label_cell($myrow["total_govt_fee"], "style='text-align:center'");
    label_cell($myrow["total_service_charge"], "style='text-align:center'");
    label_cell($myrow["total_extra_service_charge"], "style='text-align:center'");
    label_cell($myrow["total_tax"], "style='text-align:center'");
    label_cell($total, "style='text-align:center'");
    label_cell($myrow["total_pro_discount"], "style='text-align:center'");
    label_cell($myrow["total_customer_commission"], "style='text-align:center'");
    label_cell($myrow["net_service_charge"], "style='text-align:center'");
    end_row();

}
start_row("id='total_row'");
label_cell("", "style='text-align:center'");
label_cell($total_service_qty, "style='text-align:center'");
label_cell($total_govt_fee, "style='text-align:center'");
label_cell($total_service_charge, "style='text-align:center'");
label_cell($total_extra_service_charge, "style='text-align:center'");
label_cell($total_tax, "style='text-align:center'");
label_cell($total_sum, "style='text-align:center'");
label_cell($total_pro_discount, "style='text-align:center'");
label_cell($total_customer_commission, "style='text-align:center'");
label_cell($total_net_service_charge, "style='text-align:center'");
end_row();


echo "<style>#total_row td {background: #009688 !important; color: white !important;}</style>";

end_table(1);
div_end();


/** EXPORT */
$Ajax->activate("PARAM_0");
$Ajax->activate("PARAM_1");
$Ajax->activate("PARAM_2");
$Ajax->activate("PARAM_3");
$Ajax->activate("PARAM_4");
$Ajax->activate("PARAM_5");
$Ajax->activate("PARAM_6");


start_form(false, false, $path_to_root . "/reporting/reports_main.php", "export_from");
hidden("Class", "6");
hidden("REP_ID", "1001");
hidden("PARAM_0", $_POST['TransAfterDate']);
hidden("PARAM_1", $_POST['TransToDate']);
hidden("PARAM_2", "0");
hidden("PARAM_3", "");
hidden("PARAM_4", "0");
hidden("PARAM_6", $cost_center);

//hidden("PARAM_5", "0");

echo array_selector("PARAM_5", null, [trans("Export to PDF"), trans("Export to EXCEL")]);
br(2);

submit_cells('Rep1001', trans("EXPORT"), '', "Export to PDF or EXCEL", 'default');

end_form();

/** END -- EXPORT */

end_page(true);

?>

<style>
    form[name="export_from"] {
        text-align: center;
    }
</style>
