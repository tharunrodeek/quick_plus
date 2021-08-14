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

$page_security = 'SA_EMPL';
$path_to_root  = '../../..';

include_once($path_to_root . '/includes/db_pager.inc');
include_once($path_to_root . '/includes/session.inc');
add_access_extensions();

include_once($path_to_root . '/includes/ui.inc');
include_once($path_to_root . '/modules/FrontHrm/includes/frontHrm_db.inc');
include_once($path_to_root . '/modules/FrontHrm/includes/frontHrm_ui.inc');
include_once($path_to_root . '/reporting/includes/reporting.inc');

$js = '';
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

//--------------------------------------------------------------------------

page(trans($help_context = "Employee Transaction"), isset($_GET['EmpId']), false, '', $js);

if (isset($_GET['EmpId']))
	$_POST['EmpId'] = $_GET['EmpId'];

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();

ref_cells(trans('Reference:'), 'Ref', '',null, trans('Enter reference fragment or leave empty'));
ref_cells(trans('Memo:'), 'Memo', '',null, trans('Enter memo fragment or leave empty'));
date_cells(trans('From:'), 'FromDate', '', null, 0, -1, 0, null, true);
date_cells(trans('To:'), 'ToDate', '', null, 0, 0, 0, null, true);

end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

department_list_cells(trans('Department:'), 'DeptId', null, trans('All departments'), true);
employee_list_cells(trans('Employee:'), "EmpId", null, trans('All employees'), true, false, get_post('DeptId'));
check_cells(trans('Only unpaid:'), 'OnlyUnpaid', null, true);
submit_cells('Search', trans('Search'), '', '', 'default');

end_row();
end_table(1);
    
//--------------------------------------------------------------------------

function check_overdue($row) {

}
function trans_type($row) {
	return $row['Type'] == 0 ? 'Payslip' : 'Payment advice';
}
function view_link($row) {
	return get_trans_view_str(ST_JOURNAL, $row["trans_no"]);
}
function prt_link($row) {
	if($row['Type'] == 1)
	    return hrm_print_link($row['payslip_no'], trans('Print this Payslip'), true, ST_PAYSLIP, ICON_PRINT, '', '', 0);
}

$sql = get_sql_for_payslips(get_post('Ref'), get_post('Memo'), get_post('FromDate'), get_post('ToDate'), get_post('DeptId'), get_post('EmpId'), check_value('OnlyUnpaid'));

$cols = array (
	trans('Date') => array('type'=>'date'),
	trans('Trans #') => array('fun'=>'view_link'),
	trans('Type') => array('fun'=>'trans_type'),
	trans('Employee ID'),
	trans('Employee Name'),
	trans('Payslip No') => '',
	trans('Pay from') => array('type'=>'date'),
	trans('Pay to') => array('type'=>'date'),
	trans('Amount') => array('type'=>'amount'),
	'' => array('align'=>'center', 'fun'=>'prt_link')
);

$table =& new_db_pager('trans_tbl', $sql, $cols, null, null, 15);
$table->set_marker('check_overdue', trans('Marked items are overdue.'));

$table->width = "80%";

display_db_pager($table);

end_form();
end_page();