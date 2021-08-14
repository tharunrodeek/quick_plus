<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_EMPLOYEE_INQ';
$path_to_root="../../..";

include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

$js = '';

page(trans("Employees Inquiry"), @$_REQUEST['popup'], false, "", $js);

check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));

//----------------------------------------------------------------------------------------
if (isset($_GET['delete_id'])){
	$selected_del_id = $_GET['delete_id'];

	if (key_in_foreign_table($selected_del_id, 'kv_empl_salary', 'empl_id')){
		
		display_error(trans("Cannot delete this Employee because Payroll Processed to this employee And it will be  added in the financial Transactions."));
	}else {
		delete_employee($selected_del_id);
		$filename = company_path().'/images/empl/'.empl_img_name($selected_del_id).".jpg";
		if (file_exists($filename))
			unlink($filename);
		display_notification(trans("Selected Employee has been deleted."));
		$Ajax->activate('_page_body');	
	}
}
function edit_link($row){
		$str = "/modules/ExtendedHRM/manage/employees.php?empl_id=".$row['empl_id'];
  	return $str ? pager_link(trans("Edit"), $str, ICON_EDIT) : '';
}

function prt_link($row)
{
	global $path_to_root;

  	return '<a target="_blank" href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep812.php?PARAM_0=0&PARAM_1='.$row['empl_id'].'&rep_v=yes" class="printlink"><img src="'.$path_to_root.'/themes/default/images/print.png" style="vertical-align:middle;width:12px;height:12px;border:0;" title="Print"></a>';
}
function is_inactive($row){
	return $row["status"] > 1;
}

function delete_link($row){
  	$str = "/modules/ExtendedHRM/inquires/employees_inquiry.php?delete_id=".$row['empl_id'];
  	return $str ? pager_link(trans("Edit"), $str, ICON_DELETE) : '';
}


start_form(true);

start_table(TABLESTYLE_NOBORDER);
			start_row();			
				department_list_cells(trans("Select a Department")." :", 'dept_id', null,	trans("No Department"), true, check_value('show_inactive'));
				
				employee_list_cells(trans("Select an Employee"). " :", 'selected_id', null,	trans("Select an Employee"), true, check_value('show_inactive'), false, false,true);
			end_row();	
		end_table();

		br();


//----------------------------------------------------------------------------------------

//if (isset($_GET['delete_id'])){} else{
//	display_warning(trans("Once you delete the Employee, The whole informations can be removed from the Database"));
//}

	$sql = "SELECT job.empl_id, CONCAT(empl_info.empl_lastname,' ', empl_info.empl_firstname) AS empl_name, " ."empl_info.email, "."empl_info.mobile_phone, "."dpt.description, grade.description AS grade_desc,"."empl_info.addr_line1, ". " job.joining , empl_info.status FROM ".TB_PREF."kv_empl_info empl_info JOIN ".TB_PREF."kv_empl_job job ON  job.empl_id = empl_info.empl_id JOIN ".TB_PREF."kv_empl_departments dpt ON job.department= dpt.id JOIN ".TB_PREF."kv_empl_grade grade ON job.grade= grade.id WHERE 1=1";

	if(get_post('dept_id') > 0)
		$sql .=" AND job.department=".get_post('dept_id');
	
	if(get_post('selected_id') > 0)
		$sql .=" AND job.empl_id=".get_post('selected_id');
	$sql .=" ORDER BY empl_info.empl_id";
	$cols = array(
		trans("Empl Id") => array('name'=>'empl_id'),
	    trans("Empl Name") => array('name'=>'empl_name'),
	    trans("Email") => array('name'=>'email'),
	    trans("Mobile No") => array('name'=>'mobile_phone'),
	    trans("Department") => array('name'=>'grade_desc'),
	    trans("Designation") => array('name'=>'degination'),
	    trans("Present Address") => array('name'=>'present_address'),
	    trans("Date of Join") => array('name'=>'tran_date', 'type'=>'date'),
		array('insert'=>true, 'fun'=>'edit_link'),
		array('insert'=>true, 'fun'=>'prt_link')
	);	
	$table =& new_db_pager('info', $sql, $cols);
	$table->set_marker('is_inactive', trans("Marked Employees are either Resigned, Absconded, Terminated,Retired,Deceased,Suspended."), 'warning_msg', 'warning_bottom_msg');
	$table->width = "80%";
	display_db_pager($table);

	end_form();
end_page();
?>
<style>
tr.warning_msg td{  background-color: #FFC107;}
.warning_bottom_msg { color: #f53b00; line-height:54px; }
</style>