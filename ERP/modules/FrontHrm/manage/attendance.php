<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-Jul-2017                                  |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|                                                        |
\=======================================================*/

$page_security = 'SA_EMPL';
$path_to_root  = '../../..';

include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_db.inc");
include_once($path_to_root . "/modules/FrontHrm/includes/frontHrm_ui.inc");

//--------------------------------------------------------------------------

function can_process() {

	if (!is_date($_POST['AttDate'])) {

		display_error(trans("The entered date is invalid."));
		set_focus('AttDate');
		return false;
	}
	if (date_comp($_POST['AttDate'], Today()) > 0) {

		display_error(trans("Cannot make attendance for the date in the future."));
		set_focus('AttDate');
		return false;
	} 
	
	foreach(db_query(get_employees(false, false, get_post('DeptId'))) as $emp) {
		
		if(strlen($_POST[$emp['emp_id'].'-0']) != 0 && (!preg_match("/^(?(?=\d{2})(?:2[0-3]|[01][0-9])|[0-9]):[0-5][0-9]$/", $_POST[$emp['emp_id'].'-0']) && (!is_numeric($_POST[$emp['emp_id'].'-0']) || $_POST[$emp['emp_id'].'-0'] >= 24))) {
			display_error(trans("Attendance input data must be less than 24 hours and formatted in <b>HH:MM</b> or <b>Integer</b>, example - 02:25 , 2:25, 8, 23:59 ..."));
			set_focus($emp['emp_id'].'-0');
			return false;
		}
		foreach(db_query(get_overtime()) as $ot) {
			
			if(strlen($_POST[$emp['emp_id'].'-'.$ot['overtime_id']]) != 0 && (!preg_match("/^(?(?=\d{2})(?:2[0-3]|[01][0-9])|[0-9]):[0-5][0-9]$/", $_POST[$emp['emp_id'].'-'.$ot['overtime_id']]) && (!is_numeric($_POST[$emp['emp_id'].'-'.$ot['overtime_id']]) || $_POST[$emp['emp_id'].'-'.$ot['overtime_id']] >= 24))) {
				
				display_error(trans("Attendance input data must be less than 24 hours and formatted in <b>HH:MM</b> or <b>Integer</b>, example - 02:25 , 2:25, 8, 23:59 ..."));
				set_focus($emp['emp_id'].'-'.$ot['overtime_id']);
				return false;
			}
		}
	}
	return true;
}

//--------------------------------------------------------------------------

page(trans($help_context = "Employees Attendance"), false, false, "", $js);

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
date_cells(trans("Date:"), 'AttDate');
department_list_cells(trans("For department:"), "DeptId", null, trans("All departments"), true);
end_row();
end_table(1);

start_table(TABLESTYLE2);
$initial_cols = array("ID", trans("Employee"), trans("Regular time"));
$overtimes = db_query(get_overtime());
$remaining_cols = array();
$overtime_id    = array();
$k=0;
while($overtime = db_fetch($overtimes)) {
    $remaining_cols[$k] = $overtime['overtime_name'];
    $overtime_id[$k] = $overtime['overtime_id'];
    $k++;
}

$th = array_merge($initial_cols, $remaining_cols);
$employees = db_query(get_employees(false, false, get_post('DeptId')));

$emp_ids = array();

table_header($th);

$k=0;
while($employee = db_fetch($employees)) {
    
    start_row();
    label_cell($employee['emp_id']);
    label_cell($employee['name']);
    $name1 = $employee['emp_id'].'-0';
    text_cells(null, $name1, null, 10, 10);
    $emp_ids[$k] = $employee['emp_id'];
    
    $i=0;
    while($i < count($remaining_cols)) {
        $name2 = $employee['emp_id'].'-'.$overtime_id[$i];
        text_cells(null, $name2, null, 10, 10);
        $i++;
    }
    $k++;
    end_row();
}

end_table(1);
    
submit_center('addatt', trans("Save attendance"), true, '', 'default');

//--------------------------------------------------------------------------

if(!db_has_employee())
	display_error(trans("There are no employees for attendance."));

if(isset($_POST['addatt'])) {
	
	if(!can_process())
		return;
    
    $att_items = 0;
    foreach($emp_ids as $id) {
        
		if($_POST[$id.'-0'] && check_date_paid($id, $_POST['AttDate'])) {
			
			display_error(trans('Attendance registration for this date has been approved, cannot be updated.'));
            set_focus($id.'-0');
			exit();
		}
		else {
			if(strlen($_POST[$id.'-0']) > 0)
                $att_items ++;
			
			write_attendance($id, 0, time_to_float($_POST[$id.'-0']), 1, $_POST['AttDate']);
		}
        
        foreach($overtime_id as $ot) {
			
			if($_POST[$id.'-0'] && check_date_paid($id, $_POST['AttDate'])){
			
				display_error(trans('Selected date has already paid for Employee $id'));
            	set_focus($id.'-'.$ot);
				exit();
			}
			else {
				$rate = get_overtime($ot)['overtime_rate'];
				if(strlen($_POST[$id.'-'.$ot]) > 0)
				    $att_items ++;
				write_attendance($id, $ot, time_to_float($_POST[$id.'-'.$ot]), $rate, $_POST['AttDate']);
			}
        }
    }
	if($att_items > 0)
		display_notification(trans('Attendance has been saved.'));
	else
		display_notification(trans('Nothing added'));
	$Ajax->activate('_page_body');
}

end_form();
end_page();
