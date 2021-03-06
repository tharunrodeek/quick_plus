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
$page_security = 'SA_REQUISITIONS';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();

page(trans($help_context = "Requisitions"));

include_once($path_to_root . "/modules/requisitions/includes/modules_db.inc");
include_once($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	//initialise no input errors assumed initially before we test
	$input_error = 0;

	if (strlen($_POST['point_of_use']) == 0) 
	{
		$input_error = 1;
		display_error(trans("The point of use cannot be empty."));
		set_focus('name');
	}
	if (strlen($_POST['narrative']) == 0) 
	{
		$input_error = 1;
		display_error(trans("The narrative be empty."));
		set_focus('rate');
	}

	if ($input_error != 1) 
	{
    	if ($selected_id != -1) 
    	{
    		update_requisition($selected_id, $_POST['point_of_use'], $_POST['narrative'], $_POST['details']);
			display_notification(trans('Selected requisition has been updated.'));


    	} 
    	else 
    	{
    		add_requisition( $_POST['point_of_use'], $_POST['narrative'], $_POST['details']);
			display_notification(trans('New requisition has been added'));
    	}
    	
		$Mode = 'RESET';
	}
} 

//-----------------------------------------------------------------------------------

function can_delete($selected_id)
{
	if (requisitions_in_details($selected_id))
	{
		display_error(trans("Cannot delete this requisition because details transactions have been created referring to it."));
		return false;
	}
	
	return true;
}


//-----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{
	if (can_delete($selected_id))
	{
		delete_requisition($selected_id);
		display_notification(trans('Selected requisition has been deleted'));
	}
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}
//-----------------------------------------------------------------------------------

$result = get_all_requisitions(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width=50%");

$th = array(trans("RNo. "), trans("Point of use"), trans("Narrative"), trans("Application Date"), "", "", trans("Details"));
inactive_control_column($th);
table_header($th);
$k = 0;
while ($myrow = db_fetch($result)) 
{
	alt_table_row_color($k);	

	label_cell($myrow["requisition_id"]);
	label_cell($myrow["point_of_use"]);
	label_cell($myrow["narrative"]);
	label_cell(sql2date($myrow["application_date"]));

 	edit_button_cell("Edit".$myrow['requisition_id'], trans("Edit"));
	inactive_control_cell($myrow["requisition_id"], $myrow["inactive"], 'requisitions', 'requisition_id');
 	delete_button_cell("Delete".$myrow['requisition_id'], trans("Delete"));

	echo "<td><a href='requisition_details.php?requisitionid=".$myrow['requisition_id']."'>".trans("Details")."</a></td>\n";

	end_row();
}
inactive_control_row($th);
end_table(1);

//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing status code

		$myrow = get_requisition($selected_id);

		$_POST['point_of_use']  = $myrow["point_of_use"];
		$_POST['narrative']  = $myrow["narrative"];
		$_POST['details']  = $myrow["details"];
	}
	hidden('selected_id', $selected_id);
} 

text_row(trans("Point of use :"), 'point_of_use', null, 50, 50);
text_row(trans("Narrative :"), 'narrative', null, 50, 50);
textarea_row(trans("Details :"), 'details', null, 50, 5);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
