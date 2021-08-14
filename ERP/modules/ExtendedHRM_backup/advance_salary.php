<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'HR_PAYSLIP';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/includes/ui.inc");
$version_id = get_company_prefs('version_id');

$js = '';
if($version_id['version_id'] == '2.4.1'){
	if ($SysPrefs->use_popup_windows) 
		$js .= get_js_open_window(900, 500);	

	if (user_use_date_picker()) 
		$js .= get_js_date_picker();
	
}else{
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
}

include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");

page(trans("Advance Salary"));

check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));
 
 check_db_has_salary_account(trans("There are no Salary Account defined in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/hrm_settings.php'>Settings</a> to update it."));


if(isset($_GET['Added'])){
	display_notification(' The Employee Advance Salary is added #' .$_GET['Added']);
}
if (isset($_GET['employee_id'])){
	$_POST['employee_id'] = $_GET['employee_id'];
}
if (isset($_GET['month'])){
	$_POST['month'] = $_GET['month'];
}
if (isset($_GET['year'])){
	$_POST['year'] = $_GET['year'];
}
$employee_id = get_post('employee_id','');
$month = get_post('month','');
$year = get_post('year','');

if(list_updated('month') || get_post('RefreshInquiry') || list_updated('employee_id')) {
		$month = get_post('month');   
		$Ajax->activate('totals_tbl');
}
div_start('totals_tbl');
	start_form();
		if (db_has_employees()) {
			start_table(TABLESTYLE_NOBORDER);
			start_row();
			kv_fiscalyears_list_cells(trans("Fiscal Year:"), 'year', null, true);
			kv_current_fiscal_months_list_cell("Months", "month", null, true);
			//label_cells(trans("Month"), )
			employee_list_cells(trans("Select an Employee")." :", 'employee_id', null,	trans("Select an Employee"), true, check_value('show_inactive'),false, false,true);
			end_row();
			end_table();
			br();
			if (get_post('_show_inactive_update')) {
				$Ajax->activate('employee_id');
				$Ajax->activate('month');
				$Ajax->activate('year');
				set_focus('employee_id');
			}
		} 
		else {	
			hidden('employee_id');
			hidden('month');
			hidden('year');
		}
		if($employee_id != ''){
			$advance_pay =  GetSingleValue('kv_empl_salary_advance', 'amount', array('empl_id'=> $employee_id, 'month' => $month, 'year' => $year));
			//if($advance_pay ){
				$max_amount = GetSingleValue('kv_empl_job', 'gross', array('empl_id' => $employee_id)); 
				start_table(TABLESTYLE);
					$month_name = kv_month_name_by_id(get_post('month'));
					label_row(trans("Employee No:"), $_POST['employee_id']);
					label_row(trans("Employee Name:"), kv_get_empl_name($_POST['employee_id'],true));
					label_row(trans("Max. Available:"), $max_amount);
					hidden('max_amount', $max_amount);
					label_row(trans("Month of Payment:"), $month_name);
					
					if($advance_pay){
						label_row(trans("Advance Amount"), $advance_pay);
						$paid_adv =GetSingleValue('kv_empl_salary', 'adv_sal', array('empl_id'=> $employee_id, 'month' => $month, 'year' => $year));
						if($paid_adv){
							label_row(trans("Status"), trans("Paid and Procssed Payroll"));
						}
						$submit = false;
					}
					else{
						if(db_has_employee_payslip($employee_id, $month, $year) == false ){
							text_row(trans("Advance Amount"), 'adv_sal', null, 15, 20);
							$submit =  true;
						}else{
							label_row(trans("Advance Amount"), trans("Sorry, Payroll already Processed to this month"));
							$submit =  false;
						}
					}						

				end_table();

				br();br();

				if($month == date('m'))
					$submit =  true;
				else
					$submit = false;

				if($submit)
					submit_center('pay_adv_salary', trans("Process Payout"), true, trans("Payout to Employees"), 'default');
			//}else{
			//	display_warning(trans("Sorry, you can't Process Advance Salary for salay paid Month"));
			//}
		} else { 
			echo '<center><h4> No Employee selected to Process Advance Salary </h4> </center>';
		}
	end_form();
		
div_end();

	if(get_post('pay_adv_salary') ) {		
		if( get_post('max_amount') >= get_post('adv_sal')){
			$id = Insert('kv_empl_salary_advance', array('empl_id' => $_POST['employee_id'], 'month' => $_POST['month'], 'year' => $_POST['year'], 'amount' => $_POST['adv_sal'], 'date' =>  array(Today(), 'date') ) );

			meta_forward($_SERVER['PHP_SELF'], "Added=$id&employee_id=".$_POST['employee_id'].'&month='.$_POST['month'].'&year='.$_POST['year']);
		}else
		display_error(trans("Sorry the Amount is more than the maximum Allowed amount"));
	}

end_page(); ?>
<style>
	table { width: auto; }
</style>