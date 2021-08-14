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
$page_security = 'SA_INVENTORYADJUSTMENT';
$path_to_root = "../../..";
include_once($path_to_root . "/includes/ui/items_cart.inc");

include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/inventory/includes/item_adjustments_ui.inc");
include_once($path_to_root . "/inventory/includes/inventory_db.inc");
include_once($path_to_root . "/modules/textcart/includes/textcart_manager.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(800, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
page(trans($help_context = "Item Adjustments Note"), false, false, "", $js);

//-----------------------------------------------------------------------------------------------

check_db_has_costable_items(trans("There are no inventory items defined in the system which can be adjusted (Purchased or Manufactured)."));

check_db_has_movement_types(trans("There are no inventory movement types defined in the system. Please define at least one inventory adjustment type."));

//-----------------------------------------------------------------------------------------------

if (isset($_GET['AddedID'])) 
{
	$trans_no = $_GET['AddedID'];
	$trans_type = ST_INVADJUST;

	display_notification_centered(trans("Items adjustment has been processed"));
	display_note(get_trans_view_str($trans_type, $trans_no, trans("&View this adjustment")));

	display_note(get_gl_view_str($trans_type, $trans_no, trans("View the GL &Postings for this Adjustment")), 1, 0);

	hyperlink_no_params($_SERVER['PHP_SELF'], trans("Enter &Another Adjustment"));

	hyperlink_params("$path_to_root/admin/attachments.php", trans("Add an Attachment"), "filterType=$trans_type&trans_no=$trans_no");

	display_footer_exit();
}
//--------------------------------------------------------------------------------------------------

function line_start_focus() {
  global 	$Ajax;

  $Ajax->activate('items_table');
  set_focus('_stock_id_edit');
}
//-----------------------------------------------------------------------------------------------

function handle_new_order()
{
	if (isset($_SESSION['adj_items']))
	{
		$_SESSION['adj_items']->clear_items();
		unset ($_SESSION['adj_items']);
	}

    $_SESSION['adj_items'] = new items_cart(ST_INVADJUST);
	$_POST['AdjDate'] = new_doc_date();
	if (!is_date_in_fiscalyear($_POST['AdjDate']))
		$_POST['AdjDate'] = end_fiscalyear();
	$_SESSION['adj_items']->tran_date = $_POST['AdjDate'];	
}

//-----------------------------------------------------------------------------------------------

function can_process()
{
	global $Refs, $SysPrefs;

	$adj = &$_SESSION['adj_items'];

	if (count($adj->line_items) == 0)	{
		display_error(trans("You must enter at least one non empty item line."));
		set_focus('stock_id');
		return false;
	}
	if (!$Refs->is_valid($_POST['ref'])) 
	{
		display_error( trans("You must enter a reference."));
		set_focus('ref');
		return false;
	}

	if (!is_new_reference($_POST['ref'], ST_INVADJUST)) 
	{
		display_error( trans("The entered reference is already in use."));
		set_focus('ref');
		return false;
	}

	if (!is_date($_POST['AdjDate'])) 
	{
		display_error(trans("The entered date for the adjustment is invalid."));
		set_focus('AdjDate');
		return false;
	} 
	elseif (!is_date_in_fiscalyear($_POST['AdjDate'])) 
	{
		display_error(trans("The entered date is not in fiscal year."));
		set_focus('AdjDate');
		return false;
	}
	elseif (!$SysPrefs->allow_negative_stock())
	{
		$low_stock = $adj->check_qoh($_POST['StockLocation'], $_POST['AdjDate'], !$_POST['Increase']);

		if ($low_stock)
		{
    		display_error(trans("The adjustment cannot be processed because it would cause negative inventory balance for marked items as of document date or later."));
			unset($_POST['Process']);
			return false;
		}
	}
	return true;
}

//-------------------------------------------------------------------------------

if (isset($_POST['Process']) && can_process()){

	$trans_no = add_stock_adjustment($_SESSION['adj_items']->line_items,
		$_POST['StockLocation'], $_POST['AdjDate'],	$_POST['type'], $_POST['Increase'],
		$_POST['ref'], $_POST['memo_']);
	new_doc_date($_POST['AdjDate']);
	$_SESSION['adj_items']->clear_items();
	unset($_SESSION['adj_items']);

   	meta_forward($_SERVER['PHP_SELF'], "AddedID=$trans_no");

} /*end of process credit note */

//-----------------------------------------------------------------------------------------------

function check_item_data()
{
	if (!check_num('qty',0))
	{
		display_error(trans("The quantity entered is negative or invalid."));
		set_focus('qty');
		return false;
	}

	if (!check_num('std_cost', 0))
	{
		display_error(trans("The entered standard cost is negative or invalid."));
		set_focus('std_cost');
		return false;
	}

   	return true;
}

//-----------------------------------------------------------------------------------------------

function handle_update_item()
{
    if($_POST['UpdateItem'] != "" && check_item_data())
    {
		$id = $_POST['LineNo'];
    	$_SESSION['adj_items']->update_cart_item($id, input_num('qty'), 
			input_num('std_cost'));
    }
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function handle_delete_item($id)
{
	$_SESSION['adj_items']->remove_from_cart($id);
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function handle_new_item()
{
	if (!check_item_data())
		return;

	add_to_order($_SESSION['adj_items'], $_POST['stock_id'], 
	  input_num('qty'), input_num('std_cost'));
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------
$id = find_submit('Delete');
if ($id != -1)
	handle_delete_item($id);

if (isset($_POST['AddItem']))
	handle_new_item();

if (isset($_POST['UpdateItem']))
	handle_update_item();

if (isset($_POST['CancelItemChanges'])) {
	line_start_focus();
}
//-----------------------------------------------------------------------------------------------

if (isset($_GET['NewAdjustment']) || !isset($_SESSION['adj_items']))
{
	handle_new_order();
}

//-----------------------------------------------------------------------------------------------
$textcart_mgr = new ItemsAdjTextCartManager();
$textcart_mgr->handle_post_request();
  function display_order_in_tab($title, $cart) {
    display_adjustment_items($title, $cart);
  }

start_form();

display_order_header($_SESSION['adj_items']);

start_outer_table(TABLESTYLE, "width=70%", 10);

$textcart_mgr->tab_display(trans("Adjustment Items"), $_SESSION['adj_items'], "display_order_in_tab");
adjustment_options_controls();

end_outer_table(1, false);

submit_center_first('Update', trans("Update"), '', null);
submit_center_last('Process', trans("Process Adjustment"), '', 'default');

end_form();
end_page();

?>
