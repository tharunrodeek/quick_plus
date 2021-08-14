<?php
/**********************************************************************
    Direct Axis Technology L.L.C.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
$page_security = 'SA_SUPPTRANSVIEW';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/purchasing/includes/purchasing_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();
page(trans($help_context = "Search Purchase Orders"), false, false, "", $js);

//---------------------------------------------------------------------------------------------
function trans_view($trans)
{
	return get_trans_view_str(ST_PURCHORDER, $trans["order_no"]);
}

function edit_link($row) 
{
	global $page_nested;

	return $page_nested || !$row['isopen'] ? '' :
		trans_editor_link(ST_PURCHORDER, $row["order_no"]);
}

function prt_link($row)
{
	return print_document_link($row['order_no'], trans("Print"), true, ST_PURCHORDER, ICON_PRINT);
}

if (isset($_GET['order_number']))
{
	$_POST['order_number'] = $_GET['order_number'];
}

//-----------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('SearchOrders')) 
{
	$Ajax->activate('orders_tbl');
} elseif (get_post('_order_number_changed')) 
{
	$disable = get_post('order_number') !== '';

	$Ajax->addDisable(true, 'OrdersAfterDate', $disable);
	$Ajax->addDisable(true, 'OrdersToDate', $disable);
	$Ajax->addDisable(true, 'StockLocation', $disable);
	$Ajax->addDisable(true, '_SelectStockFromList_edit', $disable);
	$Ajax->addDisable(true, 'SelectStockFromList', $disable);

	if ($disable) {
		$Ajax->addFocus(true, 'order_number');
	} else
		$Ajax->addFocus(true, 'OrdersAfterDate');

	$Ajax->activate('orders_tbl');
}
//---------------------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
ref_cells(trans("#:"), 'order_number', '',null, '', true);

date_cells(trans("from:"), 'OrdersAfterDate', '', null, -user_transaction_days());
date_cells(trans("to:"), 'OrdersToDate');

locations_list_cells(trans("into location:"), 'StockLocation', null, true);
end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

stock_items_list_cells(trans("for item:"), 'SelectStockFromList', null, true);

if (!$page_nested)
	supplier_list_cells(trans("Select a supplier: "), 'supplier_id', null, true, true);

check_cells(trans('Also closed:'), 'also_closed', check_value('also_closed'));

submit_cells('SearchOrders', trans("Search"),'',trans('Select documents'), 'default');
end_row();
end_table(1);

//---------------------------------------------------------------------------------------------

$sql = get_sql_for_po_search_completed(get_post('OrdersAfterDate'), get_post('OrdersToDate'),
	get_post('supplier_id'), get_post('StockLocation'), get_post('order_number'),
	get_post('SelectStockFromList'), get_post('also_closed'));

$cols = array(
		trans("#") => array('fun'=>'trans_view', 'ord'=>'', 'align'=>'right'),
		trans("Reference"),
		trans("Supplier") => array('ord'=>''),
		trans("Location"),
		trans("Supplier's Reference"),
		trans("Order Date") => array('name'=>'ord_date', 'type'=>'date', 'ord'=>'desc'),
		trans("Currency") => array('align'=>'center'),
		trans("Order Total") => 'amount',
		array('insert'=>true, 'fun'=>'edit_link'),
		array('insert'=>true, 'fun'=>'prt_link'),
);

if (get_post('StockLocation') != ALL_TEXT) {
	$cols[trans("Location")] = 'skip';
}

//---------------------------------------------------------------------------------------------------

$table =& new_db_pager('orders_tbl', $sql, $cols);

$table->width = "80%";

display_db_pager($table);

end_form();
end_page();
