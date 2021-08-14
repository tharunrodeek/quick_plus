<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-Jul-2017                                 |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|                                                        |
\=======================================================*/

$page_security = 'SA_HRSETUP';
$path_to_root  = '../../..';

include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_db.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_ui.inc");

//--------------------------------------------------------------------------

page(trans($help_context = "Manage Salary Scales"));
simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') {

	if(strlen($_POST['name']) == 0 || $_POST['name'] == '') {
		display_error( trans("Name field cannot be empty."));
		set_focus('name');
	}
	elseif(strlen($_POST['amount']) == 0 || $_POST['amount'] == '') {
		display_error( trans("Amount field cannot be empty."));
		set_focus('amount');
	}
	else {
		$id = $selected_id == -1 ? false : $selected_id;
		write_scale($id, $_POST['name'], $_POST['payBasis']);

		if($selected_id == -1) {
			$new = true;
			$added_scale = db_insert_id();
		}
		else {
			$new = false;
			$added_scale = $selected_id;
		}
		
		set_basic_salary($_POST['AccountId'], input_num('amount'), $added_scale, $new);
		
    	if ($selected_id != -1)
			display_notification(trans('Selected salary scale has been updated'));
    	else
			display_notification(trans('New salary scale has been added'));
		
		$Mode = 'RESET';
	}
}

if ($Mode == 'Delete') {

	if(salary_scale_used($selected_id))
		display_error( trans("This salary scale cannot be deleted."));
	else {
		delete_salary_scale($selected_id);
		display_notification(trans('Selected salary scale has been deleted'));
	}
	$Mode = 'RESET';
}

if($Mode == 'RESET') {
	$selected_id = -1;
	$_POST['name'] = $_POST['amount'] = '';
}

//--------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE);
$th = array(trans("Id"), trans("Name"), trans('Salary amount'), trans('Pay basis'), "", "");
inactive_control_column($th);
table_header($th);

$result = db_query(get_salary_scale(false, check_value('show_inactive')));
$k = 0;
while ($myrow = db_fetch($result)) {
	alt_table_row_color($k);
	$pay_basis = $myrow['pay_basis'] == 0 ? trans('Monthly') : trans('Daily');

	label_cell($myrow["scale_id"]);
	label_cell($myrow['scale_name']);
	amount_cell($myrow['pay_amount']);
	label_cell($pay_basis);
	inactive_control_cell($myrow["scale_id"], $myrow["inactive"], 'salaryscale', 'scale_id');
	edit_button_cell("Edit".$myrow["scale_id"], trans("Edit"));
	delete_button_cell("Delete".$myrow["scale_id"], trans("Delete"));
	end_row();
}
inactive_control_row($th);
end_table(1);

start_table(TABLESTYLE2);

if($selected_id != -1) {
	
 	if ($Mode == 'Edit') {
		
		$myrow = get_salary_scale($selected_id);
		$_POST['name']  = $myrow["scale_name"];
		$_POST['AccountId']  = $myrow["pay_rule_id"];
		$_POST['amount']  = price_format($myrow["pay_amount"]);
		$_POST['payBasis']  = $myrow["pay_basis"];
		hidden('selected_id', $selected_id);
 	}
}

text_row_ex(trans('Salary Scale Name').':', 'name', 37, 50);
gl_all_accounts_list_row(trans('Salary Basic Account'), 'AccountId');
amount_row(trans("Salary Amount").':', 'amount', null, null, null, null, true);
label_row(trans('Pay Basis').':', radio(trans('Monthly salary'), 'payBasis', 0, 1).'&nbsp;&nbsp;'.radio(trans('Daily wage'), 'payBasis', 1));

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();
end_page();
