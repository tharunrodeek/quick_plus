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
/**********************************************************************
 * Page for searching item list and select it to item selection
 * in pages that have the item dropdown lists.
 * Author: bogeyman2007 from Discussion Forum. Modified by Joe Hunt
 ***********************************************************************/
//$page_security = "SA_ITEM";
$page_security = "SA_SALESORDER";
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/inventory/includes/db/items_db.inc");

?>

<?php

$js="";

if (user_use_date_picker()) {
    $js .= get_js_date_picker();
}

page(_($help_context = "Bulk-Invoice Printing"), false, false, "", $js);

if (get_post("search")) {
    $Ajax->activate("item_tbl");
}

if (!isset($_POST['customer_id']))
    $_POST['customer_id'] = null;



function payment_status($row)
{
    return [
        0 => 'All',
        1 => 'Fully Paid',
        2 => 'Not Paid',
        3 => 'Partially Paid',
    ][$row['payment_status']];
}

function payment_status_cell($label,$name,$selected_id=null)
{
    echo "<td>$label</td>
            <td>" . array_selector(
            $name, $selected_id,
            [
                0 => 'All',
                1 => 'Fully Paid',
                2 => 'Not Paid',
                3 => 'Partially Paid'
            ]
        ) . "</td>";

}


start_form(false, false, $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);

start_table(TABLESTYLE2, "style='width:50%'");
table_section_title(_("PRINT MULTIPLE INVOICES"));

customer_list_cells(_("Customer: "), 'customer_id', $_POST['customer_id'], "--");

start_row();
date_cells("Date From", 'filter_date', _('Filter by Date'),
    true, -30, 0, 0, null, false);
end_row();

start_row();

date_cells("Date To", 'filter_date_to', _('Filter by Date'),
    true, 0, 0, 0, null, false);

end_row();

start_row();

text_cells("Invoice Range From",'inv_range_from');

end_row();
start_row();

text_cells("Invoice Range To",'inv_range_to');

end_row();

start_row();

payment_status_cell('Payment Status','payment_status');

end_row();

submit_row('print', "Print Invoices", '1', '', '','default');


end_table();
end_form();


end_page(true);


if($_POST['print']) {

    $from_date = get_post('filter_date');
    $to_date = get_post('filter_date_to');
    $inv_range_from = get_post('inv_range_from');
    $inv_range_to = get_post('inv_range_to');
    $customer = get_post('customer_id');
    $payment_status = get_post('payment_status');

//    if(empty($customer)) {
//        display_warning("Please select a customer");
//        return false;
//    }

    if(empty($inv_range_from)) {
        display_warning("Please enter INVOICE RANGE FROM reference");
        set_focus('inv_range_from');
        return false;
    }

    if(empty($inv_range_to)) {
        display_warning("Please enter INVOICE RANGE TO reference");
        set_focus('inv_range_to');
        return false;
    }


    $from_date_ = db_escape(date2sql($from_date));
    $to_date_ = db_escape(date2sql($to_date));
    $inv_range_from_ = db_escape($inv_range_from);
    $inv_range_to_ = db_escape($inv_range_to);


    $result = get_print_bulk_invoices($from_date_,$to_date_,$inv_range_from_,$inv_range_to_,$customer,$payment_status);


    $total_count = db_num_rows($result);


    if($total_count == 0) {
        display_warning("No Invoices Found.");
        return false;
    }


    if($total_count > 50) {
        display_warning("Cannot print more than 50 Invoices. 
        <b><u>Found $total_count Invoices.</u></b> Please change the filter criteria");
        return false;
    }


    meta_forward($path_to_root."/invoice_print/index.php",
        "_=bulk_invoice&from_date=$from_date
        &to_date=$to_date&r_from=$inv_range_from&r_to=$inv_range_to&customer=$customer");

}

?>


<style>
    form[name="export_from"] {
        text-align: center;
        /*clear: both;*/
    }

    .print_page_link {
        display: none !important;
    }


</style>

