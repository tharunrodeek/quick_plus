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
$page_security = 'SA_DIMENSION';
$path_to_root = "..";
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/admin/db/tags_db.inc");
include_once($path_to_root . "/dimensions/includes/dimensions_db.inc");
include_once($path_to_root . "/dimensions/includes/dimensions_ui.inc");

$js = "";
if (user_use_date_picker())
	$js .= get_js_date_picker();
page(trans($help_context = "Cost center Entry"), false, false, "", $js);

//---------------------------------------------------------------------------------------

if (isset($_GET['trans_no']))
{
	$selected_id = $_GET['trans_no'];
} 
elseif(isset($_POST['selected_id']))
{
	$selected_id = $_POST['selected_id'];
}
else
	$selected_id = -1;
//---------------------------------------------------------------------------------------

if (isset($_GET['AddedID'])) 
{
	$id = $_GET['AddedID'];

	display_notification_centered(trans("The cost center has been entered."));

	safe_exit();
}

//---------------------------------------------------------------------------------------

if (isset($_GET['UpdatedID'])) 
{
	$id = $_GET['UpdatedID'];

	display_notification_centered(trans("The cost center has been updated."));
	safe_exit();
}

//---------------------------------------------------------------------------------------

if (isset($_GET['DeletedID'])) 
{
	$id = $_GET['DeletedID'];

	display_notification_centered(trans("The cost center has been deleted."));
	safe_exit();
}

//---------------------------------------------------------------------------------------

if (isset($_GET['ClosedID'])) 
{
	$id = $_GET['ClosedID'];

	display_notification_centered(trans("The cost center has been closed. There can be no more changes to it.") . " #$id");
	safe_exit();
}

//---------------------------------------------------------------------------------------

if (isset($_GET['ReopenedID'])) 
{
	$id = $_GET['ReopenedID'];

	display_notification_centered(trans("The cost center has been re-opened. ") . " #$id");
	safe_exit();
}

//-------------------------------------------------------------------------------------------------

function safe_exit()
{
	global $path_to_root;

	hyperlink_no_params("", trans("Enter a &new Cost Center"));
	echo "<br>";
	hyperlink_no_params($path_to_root . "/dimensions/inquiry/search_dimensions.php", trans("&Select an existing cost center"));

	display_footer_exit();
}

//-------------------------------------------------------------------------------------

function can_process()
{
	global $selected_id, $Refs;

	if ($selected_id == -1) 
	{
    	if (!check_reference($_POST['ref'], ST_DIMENSION))
    	{
			set_focus('ref');
    		return false;
    	}
	}

	if (strlen($_POST['name']) == 0) 
	{
		display_error( trans("The name must be entered."));
		set_focus('name');
		return false;
	}

	if (!is_date($_POST['date_']))
	{
		display_error( trans("The date entered is in an invalid format."));
		set_focus('date_');
		return false;
	}

	if (!is_date($_POST['due_date']))
	{
		display_error( trans("The required by date entered is in an invalid format."));
		set_focus('due_date');
		return false;
	}

	return true;
}

//-------------------------------------------------------------------------------------

if (isset($_POST['ADD_ITEM']) || isset($_POST['UPDATE_ITEM'])) 
{
	if (!isset($_POST['dimension_tags']))
		$_POST['dimension_tags'] = array();
		
	if (can_process()) 
	{

		if ($selected_id == -1) 
		{
			$id = add_dimension($_POST['ref'], $_POST['name'], $_POST['type_'], $_POST['date_'], $_POST['due_date'], $_POST['memo_']);
			add_tag_associations($id, $_POST['dimension_tags']);
			meta_forward($_SERVER['PHP_SELF'], "AddedID=$id");
		} 
		else 
		{

			update_dimension($selected_id, $_POST['name'], $_POST['type_'], $_POST['date_'], $_POST['due_date'], $_POST['memo_']);
			update_tag_associations(TAG_DIMENSION, $selected_id, $_POST['dimension_tags']);

			meta_forward($_SERVER['PHP_SELF'], "UpdatedID=$selected_id");
		}
	}
}

//--------------------------------------------------------------------------------------

if (isset($_POST['delete'])) 
{

	$cancel_delete = false;

	// can't delete it there are productions or issues
	if (dimension_has_payments($selected_id) || dimension_has_deposits($selected_id))
	{
		display_error(trans("This cost center cannot be deleted because it has already been processed."));
		set_focus('ref');
		$cancel_delete = true;
	}

	if ($cancel_delete == false) 
	{ //ie not cancelled the delete as a result of above tests

		// delete
		delete_dimension($selected_id);
		delete_tag_associations(TAG_DIMENSION,$selected_id, true);
		meta_forward($_SERVER['PHP_SELF'], "DeletedID=$selected_id");
	}
}

//-------------------------------------------------------------------------------------

if (isset($_POST['close'])) 
{

	// update the closed flag
	close_dimension($selected_id);
	meta_forward($_SERVER['PHP_SELF'], "ClosedID=$selected_id");
}

if (isset($_POST['reopen'])) 
{

	// update the closed flag
	reopen_dimension($selected_id);
	meta_forward($_SERVER['PHP_SELF'], "ReopenedID=$selected_id");
}
//-------------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);

if ($selected_id != -1)
{
	$myrow = get_dimension($selected_id);

	if (strlen($myrow[0]) == 0) 
	{
		display_error(trans("The cost center sent is not valid."));
		display_footer_exit();
	}

	// if it's a closed cost center can't edit it
	//if ($myrow["closed"] == 1) 
	//{
	//	display_error(trans("This cost center is closed and cannot be edited."));
	//	display_footer_exit();
	//}

	$_POST['ref'] = $myrow["reference"];
	$_POST['closed'] = $myrow["closed"];
	$_POST['name'] = $myrow["name"];
	$_POST['type_'] = $myrow["type_"];
	$_POST['date_'] = sql2date($myrow["date_"]);
	$_POST['due_date'] = sql2date($myrow["due_date"]);
	$_POST['memo_'] = get_comments_string(ST_DIMENSION, $selected_id);
	
 	$tags_result = get_tags_associated_with_record(TAG_DIMENSION, $selected_id);
 	$tagids = array();
 	while ($tag = db_fetch($tags_result)) 
 	 	$tagids[] = $tag['id'];
 	$_POST['dimension_tags'] = $tagids;	

	hidden('ref', $_POST['ref']);

	label_row(trans("Reference:"), $_POST['ref']);

	hidden('selected_id', $selected_id);
} 
else 
{
	$_POST['dimension_tags'] = array();

	$_POST['date_']=begin_fiscalyear();
	$_POST['due_date']=end_fiscalyear();

	ref_row(trans("Reference:"), 'ref', '', $Refs->get_next(ST_DIMENSION), false, ST_DIMENSION);
}

text_row_ex(trans("Name") . ":", 'name', 50, 75);

$dim = get_company_pref('use_dimension');


hidden('type_',1);
hidden('date_',$_POST['date_']);
hidden('due_date',$_POST['due_date']);
hidden('dimension_tags');
hidden('memo_');

//number_list_row(trans("Type"), 'type_', null, 1, $dim);

//date_row(trans("Start Date") . ":", 'date_');

//date_row(trans("Date Required By") . ":", 'due_date', '', null, $SysPrefs->default_dimension_required_by());

//tag_list_row(trans("Tags:"), 'dimension_tags', 5, TAG_DIMENSION, true);

//textarea_row(trans("Memo:"), 'memo_', null, 40, 5);


end_table(1);

if (isset($_POST['closed']) && $_POST['closed'] == 1)
	display_note(trans("This Cost Center is closed."), 0, 0, "class='currentfg'");

if ($selected_id != -1) 
{
	echo "<br>";
	submit_center_first('UPDATE_ITEM', trans("Update"), trans('Save changes to cost center'), 'default');
//	if ($_POST['closed'] == 1)
//		submit('reopen', trans("Re-open This cost center"), true, trans('Mark this cost center as re-opened'), true);
//	else
//		submit('close', trans("Close This cost center"), true, trans('Mark this cost center as closed'), true);
	submit_center_last('delete', trans("Delete This cost center"), trans('Delete unused cost center'), true);
}
else
{
	submit_center('ADD_ITEM', trans("Add"), true, '', 'default');
}
end_form();

//--------------------------------------------------------------------------------------------

end_page();

