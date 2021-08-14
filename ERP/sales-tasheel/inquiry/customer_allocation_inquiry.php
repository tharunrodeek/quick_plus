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
$page_security = 'SA_SALESALLOC';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/sales-tasheel/includes/sales_ui.inc");
include_once($path_to_root . "/sales-tasheel/includes/sales_db.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();
page(trans($help_context = "Customer Allocation Inquiry"), false, false, "", $js);

if (isset($_GET['customer_id']))
{
	$_POST['customer_id'] = $_GET['customer_id'];
}

//------------------------------------------------------------------------------------------------

if (!isset($_POST['customer_id']))
	$_POST['customer_id'] = get_global_customer();

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();

customer_list_cells(trans("Select a customer: "), 'customer_id', $_POST['customer_id'], true);

date_cells(trans("from:"), 'TransAfterDate', '', null, -user_transaction_days());
date_cells(trans("to:"), 'TransToDate', '', null, 1);

cust_allocations_list_cells(trans("Type:"), 'filterType', null);

check_cells(" " . trans("show settled:"), 'showSettled', null);

submit_cells('RefreshInquiry', trans("Search"),'',trans('Refresh Inquiry'), 'default');

set_global_customer($_POST['customer_id']);

end_row();
end_table();
//------------------------------------------------------------------------------------------------
function check_overdue($row)
{
	return ($row['OverDue'] == 1 
		&& (abs($row["TotalAmount"]) - $row["Allocated"] != 0));
}

function order_link($row)
{
	return $row['order_']>0 ?
		get_customer_trans_view_str(ST_SALESORDER, $row['order_'])
		: "";
}

function systype_name($dummy, $type)
{
	global $systypes_array;

	return $systypes_array[$type];
}

function view_link($trans)
{
	return get_trans_view_str($trans["type"], $trans["trans_no"]);
}

function due_date($row)
{
	return $row["type"] == ST_SALESINVOICE ? $row["due_date"] : '';
}

function fmt_balance($row)
{
	return ($row["type"] == ST_JOURNAL && $row["TotalAmount"] < 0 ? -$row["TotalAmount"] : $row["TotalAmount"]) - $row["Allocated"];
}

function alloc_link($row)
{
	$link = 
	pager_link(trans("Allocation"),
		"/sales-tasheel/allocations/customer_allocate.php?trans_no=" . $row["trans_no"]
		."&trans_type=" . $row["type"]."&debtor_no=" . $row["debtor_no"], ICON_ALLOC);

	if ($row["type"] == ST_CUSTCREDIT && $row['TotalAmount'] > 0)
	{
		/*its a credit note which could have an allocation */
		return $link;
	} elseif ($row["type"] == ST_JOURNAL && $row['TotalAmount'] < 0)
	{
		return $link;
	} elseif (($row["type"] == ST_CUSTPAYMENT || $row["type"] == ST_BANKDEPOSIT) &&
		(floatcmp($row['TotalAmount'], $row['Allocated']) >= 0))
	{
		/*its a receipt  which could have an allocation*/
		return $link;
	}
	elseif ($row["type"] == ST_CUSTPAYMENT && $row['TotalAmount'] <= 0)
	{
		/*its a negative receipt */
		return '';
	} elseif (($row["type"] == ST_SALESINVOICE && ($row['TotalAmount'] - $row['Allocated']) > 0) || $row["type"] == ST_BANKPAYMENT)
		return pager_link(trans("Payment"),
			"/sales-tasheel/customer_payments.php?customer_id=".$row["debtor_no"]."&SInvoice=" . $row["trans_no"], ICON_MONEY);

}

function fmt_debit($row)
{
	$value =
	    $row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT ?
		-$row["TotalAmount"] : $row["TotalAmount"];
	return $value>=0 ? price_format($value) : '';

}

function fmt_credit($row)
{
	$value =
	    !($row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT) ?
		-$row["TotalAmount"] : $row["TotalAmount"];
	return $value>0 ? price_format($value) : '';
}
//------------------------------------------------------------------------------------------------

$sql = get_sql_for_customer_allocation_inquiry(get_post('TransAfterDate'), get_post('TransToDate'),
		get_post('customer_id'), get_post('filterType'), check_value('showSettled'));

//------------------------------------------------------------------------------------------------
$cols = array(
	trans("Type") => array('fun'=>'systype_name'),
	trans("#") => array('fun'=>'view_link', 'align'=>'right'),
	trans("Reference"),
	trans("Order") => array('fun'=>'order_link', 'ord'=>'', 'align'=>'right'),
	trans("Date") => array('name'=>'tran_date', 'type'=>'date', 'ord'=>'asc'),
	trans("Due Date") => array('type'=>'date', 'fun'=>'due_date'),
	trans("Customer") => array('name' =>'name',  'ord'=>'asc'),
	trans("Currency") => array('align'=>'center'),
	trans("Debit") => array('align'=>'right','fun'=>'fmt_debit'),
	trans("Credit") => array('align'=>'right','insert'=>true, 'fun'=>'fmt_credit'),
	trans("Allocated") => 'amount',
	trans("Balance") => array('type'=>'amount', 'insert'=>true, 'fun'=>'fmt_balance'),
	array('insert'=>true, 'fun'=>'alloc_link')
	);

if ($_POST['customer_id'] != ALL_TEXT) {
	$cols[trans("Customer")] = 'skip';
	$cols[trans("Currency")] = 'skip';
}

$table =& new_db_pager('doc_tbl', $sql, $cols);
$table->set_marker('check_overdue', trans("Marked items are overdue."));

$table->width = "80%";

display_db_pager($table);

end_form();
end_page();
