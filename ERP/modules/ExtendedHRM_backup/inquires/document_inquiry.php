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

page(trans("Documents Inquiry"), @$_REQUEST['popup'], false, "", $js);

check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));

//----------------------------------------------------------------------------------------
if (isset($_GET['delete_id'])){
	$selected_del_id = $_GET['delete_id'];
	$row = GetRow('kv_empl_cv', array('id' => $selected_del_id));
	Delete('kv_empl_cv', array('id' => $selected_del_id));
	$filename = company_path().'/attachments/empldocs/'.$row['unique_name'];
	if (!is_dir($filename) && file_exists($filename))
		unlink($filename);
	display_notification(trans("Selected data has been deleted."));
	$Ajax->activate('_page_body');	
	
}
function edit_link($row){
		$str = "/modules/ExtendedHRM/manage/attachments.php?empl_id=".$row['empl_id'];
  	return $str ? pager_link(trans("Edit"), $str, ICON_EDIT) : '';
}

function prt_link($row)
{
	global $path_to_root;

  	return '<a target="_blank" href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep812.php?PARAM_0=0&PARAM_1='.$row['empl_id'].'&rep_v=yes" class="printlink"><img src="'.$path_to_root.'/themes/default/images/print.png" style="vertical-align:middle;width:12px;height:12px;border:0;" title="Print"></a>';
}

function is_inactive($row){
	return  (strtotime($row['notify_from']) < strtotime(date('Y-m-d')) ? $row["alert"] > 0 : 0);
}

function delete_link($row){
  	$str = "/modules/ExtendedHRM/inquires/document_inquiry.php?delete_id=".$row['id'];
  	return $str ? pager_link(trans("Edit"), $str, ICON_DELETE) : '';
}
function  kv_filter_cell($label, $name, $selected_id=null, $submit_on_change=false) {
	$kv_empl_mop = array(
		1 => trans('Greater than'),
		2 => trans('Less Than'),
		3 => trans('Not equal')
	);
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $kv_empl_mop, $options);
	echo "</td>\n";
}

start_form(true);

start_table(TABLESTYLE_NOBORDER);
			start_row();			
				department_list_cells(trans("Select a Department")." :", 'dept_id', null,	trans("No Department"), true, check_value('show_inactive'));
				kv_filter_cell(trans("Filter Type"), 'filterType', null, true);
				date_cells(trans("Expiry Date:"), 'exp_date', '', null, -5);
				employee_list_cells(trans("Select an Employee"). " :", 'selected_id', null,	trans("Select an Employee"), true, check_value('show_inactive'), false, false,true);
			end_row();	
		end_table();

		br();

//----------------------------------------------------------------------------------------
	$condition_array = array();
	if(get_post('selected_id')){
		$condition_array['`cv`.`empl_id`'] = get_post('selected_id');
	}
	
	$sql = GetDataJoin('kv_empl_cv AS cv', array( 
			0 => array('join' => 'INNER', 'table_name' => 'kv_empl_doc_type AS type', 'conditions' => '`type`.`id` = `cv`.`doc_type`'),
			1 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`cv`.`empl_id` = `info`.`empl_id`')
		), 
		array('`cv`.`empl_id`, CONCAT(`info`.`empl_firstname`,  `info`.`empl_lastname`) AS empl_name, `cv`.`cv_title`, `type`.`description`, `cv`.`filename`, `cv`.`exp_date`, `cv`.`notify_from`, `cv`. `related_to`, `cv`. `alert`, `cv`.`id`'), $condition_array, array(), false, true);

	$filterType = get_post('filterType');
	if($filterType && get_post('exp_date') ){
		if($filterType == 1)
			$sql .=" AND cv.exp_date > '". get_post('exp_date') . "' ";
		elseif($filterType == 2)
			$sql .=" AND cv.exp_date < '". get_post('exp_date'). "' ";
		elseif($filterType == 3)
			$sql .=" AND cv.exp_date <> '". get_post('exp_date') . "' ";
	}
	$sql .= " ORDER BY cv.notify_from ASC ";
	$cols = array(
		trans("Empl Id") => array('name'=>'empl_id'),
	    trans("Empl Name") => array('name'=>'empl_name'),
	    trans("Title") => array('name'=>'cv_title'),
	    trans("Document Type") => array('name'=>'description'),
	    trans("Filename") => array('name'=>'filename'),
	    trans("Exp Date") => array('name'=>'exp_date', 'type'=>'date'),
	    trans("Notify from") => array('name'=>'notify_from', 'type'=>'date'),
	    trans("Related To") => array('name'=>'related_to'),
	    trans("Alert") => array('name'=>'alert'),
		array('insert'=>true, 'fun'=>'edit_link'),
		array('insert'=>true, 'fun'=>'delete_link')
	);	
	$table =& new_db_pager('info', $sql, $cols);
	$table->set_marker('is_inactive', trans("Marked Documents are under notification to renew the documents."), 'warning_msg', 'warning_bottom_msg');
	$table->width = "80%";
	display_db_pager($table);

	end_form();
end_page();

//SELECT `cv`.`empl_id`, CONCAT(`info`.`empl_firstname`, `info`.`empl_lastname`) AS empl_name, `cv`.`cv_title`, `type`.`description`, `cv`.`filename`, `cv`.`exp_date`, `cv`.`notify_from`, `cv`. `related_to`, `cv`. `alert`, `cv`.`id`, IF(CURDATE() > `notify_from`, TRUE, FALSE) as QueryResult FROM 0_kv_empl_cv AS cv INNER JOIN 0_kv_empl_doc_type AS type ON `type`.`id` = `cv`.`doc_type` INNER JOIN 0_kv_empl_info AS info ON `cv`.`empl_id` = `info`.`empl_id` WHERE (1=1 ) ORDER BY cv.notify_from desc LIMIT 0, 10
?>
<style>
tr.warning_msg td{  background-color: #FFC107;}
.warning_bottom_msg { color: #f53b00; line-height:54px; }
</style>
