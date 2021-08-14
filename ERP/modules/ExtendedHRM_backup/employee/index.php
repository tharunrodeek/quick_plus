<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_EML_PROFILE';
$path_to_root = "../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/modules/ExtendedHRM/includes/ui/empl_leave.inc" );
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

if (isset($_GET['vw'])){
	$view_id = $_GET['vw'];
	$empl_ow = GetRow('kv_empl_leave_applied', array('id' => $view_id));
    	header("Content-type: application/octet-stream");
    	//header('Content-Length: '.$row['filesize']);
 		header('Content-Disposition: attachment; filename='.$empl_ow['filename']);
 		//display_error( company_path(). "/attachments/empldocs/".$row['unique_name']);
	   	echo file_get_contents(company_path(). "/attachments/empldocs/".$empl_ow['empl_id']."/".$empl_ow['filename']);
    	exit();
	//}		
}

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

$empl_row = GetRow('kv_empl_info', array('user_id' => $_SESSION['wa_current_user']->user));
hidden('empl_id' , $empl_row['empl_id']);

page($empl_row['empl_firstname']. ' '.$empl_row['empl_lastname'].' - '.trans("Profile"), @$_REQUEST['popup'], false, "", $js);

if(!$empl_row) {
	display_warning(trans("Sorry, No Profile Linked with your User Account. Kindly Contact Administrator to connect it."));
	end_page();
	exit;
}

if(kv_check_payroll_table_exist()){}else {
	display_error(trans("There are no Allowance defined in this system. Kindly Setup <a href='".$path_to_root."/modules/ExtendedHRM/manage/allowances.php' target='_blank'>Allowances</a> Your Allowances."));
	end_page();
    exit;
}
check_db_has_Departments(trans("There is no Department in the system to add employees. Please Add some <a href='".$path_to_root."/modules/ExtendedHRM/manage/department.php' target='_blank'>Department</a> "));

$new_item = get_post('empl_id')=='' || get_post('cancel') || get_post('clone'); 
//------------------------------------------------------------------------------------

$_POST['empl_id']=$empl_row['empl_id'];

if(list_updated('dept_id'))
	$Ajax->activate('empl_id');
if (list_updated('empl_id')) {
	$empl_id = get_post('empl_id');
    clear_data();
   $_POST['empl_id'] = $_POST['employee_id'] = $empl_id;
	$Ajax->activate('details');
	$Ajax->activate('controls');
	$Ajax->activate('profile');
}
$empl_id = get_post('empl_id');

//------------------------------------------------------------------------------------
function empl_personal_data(&$empl_id) {
	br();
	global $Ajax, $SysPrefs, $path_to_root, $page_nested, $new_item, $pic_height;
	
	if(get_post('empl_id') || list_updated('empl_id'))
		$Ajax->activate('profile');
	
	div_start('profile');
	start_outer_table(TABLESTYLE2);
	table_section(1);
	table_section_title(trans("Employee Informations"));
	if(get_post('empl_id') || ( list_updated('empl_id') && get_post('empl_id') )|| $empl_id)	{ // Must be modifying an existing item
		//if (get_post('employee_id') != $empl_id || get_post('addupdate')) { // first item display
			if(get_post('empl_id'))
				$_POST['employee_id'] = $_POST['empl_id'];
			else
				$_POST['employee_id'] = $empl_id;
		
			$myrow = GetRow('kv_empl_info', array('empl_id' => $_POST['employee_id']));
			$_POST['empl_salutation'] = $myrow["empl_salutation"];
			$_POST['empl_firstname'] = $myrow["empl_firstname"];
			$_POST['empl_lastname'] = $myrow["empl_lastname"];
			$_POST['addr_line1']  = $myrow["addr_line1"];
			$_POST['addr_line2']  = $myrow["addr_line2"];
			$_POST['address2']  = $myrow["address2"];
			$_POST['empl_city']  = $myrow["empl_city"];
			$_POST['country']  = $myrow["country"];
			$_POST['empl_state']  = $myrow["empl_state"];
			$_POST['home_phone']	= $myrow["home_phone"];
			$_POST['mobile_phone']  = $myrow["mobile_phone"];
			$_POST['email']  = $myrow["email"];
			$_POST['gender']  = $myrow["gender"];
			$_POST['report_to']  = $myrow["report_to"];
			$_POST['ice_name']	= $myrow["ice_name"];
			$_POST['ice_phone_no']  = $myrow["ice_phone_no"];

			if(!isset($myrow['date_of_birth']) || $myrow['date_of_birth'] == '0000-00-00')
				$_POST['date_of_birth']=add_years(Today(), -20);
			else
				$_POST['date_of_birth']  = (isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : sql2date($myrow["date_of_birth"]));
			$_POST['marital_status']  = $myrow["marital_status"];
			if(list_updated('status'))
				$_POST['status'] = get_post('status');
			else
				$_POST['status'] = $myrow["status"];			
			$_POST['empl_pic'] = $myrow["empl_pic"];			
			$_POST['del_image'] = 0;
			$_POST['pic'] = '';

			hidden('old_status', $myrow["status"]);
		if (!isset($_POST['empl_id']) || $new_item) {
			text_row(trans("Employee Id:"), 'employee_id', $_POST['employee_id'], 21, 20);
		}else
			label_row(trans("Employee Id:*"),$_POST['employee_id']);

		hidden('employee_id', $_POST['employee_id']);
		
		set_focus('description');
			
	}  else {
		if(GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'empl_ref_type')))
			$_POST['employee_id'] = $employee_id = kv_get_next_empl_id();
		
		if(!isset($_POST['employee_id']))
			$_POST['employee_id'] = '';
			
		text_row(trans("Employee Id:"), 'employee_id', $_POST['employee_id'], 21, 20);
		//unset($_POST['empl_id']);
		$_POST['inactive'] = 0;
		if(!isset($_POST['empl_firstname']))
			$_POST['empl_firstname'] = '';
		if(!isset($_POST['empl_lastname']))
			$_POST['empl_lastname'] = '';
		if(!isset($_POST['addr_line1']))
			$_POST['addr_line1']= ''; 
		if(!isset($_POST['addr_line2']))
			$_POST['addr_line2']= ''; 
		if(!isset($_POST['empl_city'])) 
			$_POST['empl_city']= ''; 
		if(!isset($_POST['empl_state']))
			$_POST['empl_state']= ''; 
		if(!isset($_POST['date_of_birth']) || $_POST['date_of_birth'] == '0000-00-00')
			$_POST['date_of_birth']=add_years(Today(), -20);
	}

	kv_empl_salutation_list_row( trans("Salutation:"), 'empl_salutation', null,false,true);
	label_row(trans("First Name:*"), $_POST['empl_firstname']);
	label_row(trans("Last Name:"), $_POST['empl_lastname']);
	table_section_title(trans("Permanent Address"));
	label_row(trans("Line 1:"), $_POST['addr_line1']);
	label_row(trans("Line 2:"), $_POST['addr_line2']);
	label_row(trans("City:"), $_POST['empl_city']);
	label_row(trans("State:"), $_POST['empl_state']);
	if(!isset($_POST['country']))
		$_POST['country'] = GetSingleValue('kv_empl_option','option_value',array('option_name' => 'home_country'));
 	label_row(trans("Country:"),GetSingleValue('kv_empl_country','local_name',array('id' =>$_POST['country'])));

 	table_section_title(trans("Residential Address"));
	label_row(trans("Residential Address:"), $_POST['address2']);
	
	table_section_title(trans("Contact Details"));
	label_row(trans("Home Phone:"),  $_POST['home_phone']);
	label_row(trans("Mobile Phone:*"),  $_POST['mobile_phone']);
	label_row(trans("Email:"),  $_POST['email']);

	if (!isset($_POST['empl_id']) || $new_item) {		
		kv_empl_gender_list_row( trans("Gender:"), 'gender', null);
		date_row(trans("Date of Birth") . ":", 'date_of_birth', null, null, 0,0,0,null, true);
		if (list_updated('date_of_birth') || $new_item) {
			$_POST['age'] = date_diff(date_create(date2sql($_POST['date_of_birth'])), date_create('today'))->y;
		}
		label_row(trans("Age:"), $_POST['age']);
		hrm_empl_marital_list_row( trans("Marital Status:"), 'marital_status', null);
		//check_row(trans("Create User Account*:"), 'register_user_acc', null);
	/*	table_section_title(trans("Job Details"));
			
		department_list_row( trans("Department :"), 'department', null, false, false, false,false, true);
		hrm_empl_desig_group(trans("Designation Group *:"), 'desig_group', null);
		designation_list_row(trans("Designation *:"), 'desig', null);
		//text_row(trans("Desgination *:"), 'desig', null,  35, 100);
		//text_row(trans("Basic Salary *:"), 'basic_salary', null, 30, 30);
		date_row(trans("Date of Join") . ":", 'joining');
		hrm_empl_type_row(trans("Employment Type*:"), 'empl_type', null);
		workcenter_list_row(trans("Working Place*:"), 'working_place');
		table_section_title(trans("KYC IDS"));
		text_row(trans("Insurance Number:"), 'ESIC', null,  30, 100);
		text_row(trans("Insurance Company Name:"), 'PF', null,  30, 100);
		//text_row(trans("PAN Card No*:"), 'PAN', null,  30, 100);*/

	}
		
	hrm_empl_status_list(trans("Status*:"), 'status', null, true,true);
	if(isset($_POST['empl_id']) && list_updated('status')){		
		if($_POST['status'] != 1)
			textarea_row(trans("Reason for Leaving:"), 'reason_status_change', null, 30, 5);

	}
	table_section_title(trans("Contact(ICE)"));
		label_row(trans("Name:*"), $_POST['ice_name']);
		label_row(trans("Mobile Phone:*"), $_POST['ice_phone_no']);
	
	table_section(3);
	div_start('payroll_tbl');	// Add image upload for New Item 
	table_section_title(trans("Personal Details"));
	$stock_img_link = "";
	$check_remove_image = false;
	if ($empl_id!= '' && file_exists(company_path().'/images/empl/'.$_POST['empl_pic'])){	
		$stock_img_link .= "<img id='empl_profile_pic' alt = '[".$_POST['employee_id'].".jpg"."]' src='".company_path().'/images/empl/'.$_POST['empl_pic']."?nocache=".rand()."'"." height='150' border='1'>";
		$check_remove_image = true;
	} else {
		$stock_img_link .= "<img id='empl_profile_pic' alt = '[".$_POST['employee_id'].".jpg"."]' src='".$path_to_root.'/modules/ExtendedHRM/images/no-image.png'. "?nocache=".rand()."'"." height='150' border='1'>";
	}
	label_row("&nbsp;", $stock_img_link);		
	//kv_image_row(trans("Photo (.jpg)") . ":", 'pic', 'pic');
	//if ($check_remove_image)
	//	check_row(trans("Delete Image:"), 'del_image');
	if (isset($_POST['empl_id']) && !$new_item){
		kv_empl_gender_list_row( trans("Gender:"), 'gender', null,false,true);
		label_row(trans("Date of Birth") . ":", $_POST['date_of_birth']);
		if (list_updated('date_of_birth') || $new_item|| get_post('date_of_birth')) {
			$_POST['age'] = date_diff(date_create(date2sql($_POST['date_of_birth'])), date_create('today'))->y;
		}
		label_row(trans("Age:"), $_POST['age']);
		hidden('age', $_POST['age']);
		hrm_empl_marital_list_row( trans("Marital Status:"), 'marital_status', null,false,true);
	}	
	
	hidden('empl_page', 'info');

	if (!isset($_POST['empl_id']) || $new_item) { 
		if(!isset($_POST['currency'])){
			$company_record = get_company_prefs();
			$_POST['currency']  = $company_record["curr_default"];
		}		
		currencies_list_row(trans("Currency"), 'currency', null);
		kv_empl_grade_list_row( trans("Grade :"), 'grade_id', null, false, true);
		$EarAllowance = get_allowances('Earnings', 'Profile Input', 'Percentage', 'Gross Percentage', 0, get_post('grade_id'), 'Formula');

		if(db_num_rows($EarAllowance) > 0){
			$KVAllowance = kv_get_allowances(null, 0, get_post('grade_id'));
			$allowance_var_ar = array();
			foreach($KVAllowance as $single) {
				$allowance_var_ar[$single['id']] = '{$'.$single['unique_name'].'}';	
			}
			$gross = GetRow('kv_empl_allowances', array('gross' => 1));
			$basic_allowed = true;
			if($gross){
				kv_basic_row(get_allowance_name($gross['id']), 'gross', 15, 100, null, true);
			} else {
				$basic = GetRow('kv_empl_allowances', array('basic' => 1));
				kv_basic_row(get_allowance_name($basic['id']), $basic['id'], 15, 100, null, true);
				$basic_allowed = false;
			}
				
			while ($single = db_fetch($EarAllowance)) {	
				if($single['basic'] == 1)
					$basic_id = $single['id'];				
				if(is_numeric($single['formula']))
					$_POST['E_'.$single['id']] = $single['formula'];
				elseif($single['value'] == 'Percentage'  && $single['percentage']>0){		
					$_POST['E_'.$single['id']] = get_post($basic_id)*($single['percentage']/100);
				}elseif( $single['value'] == 'Gross Percentage' && $single['percentage']>0 ) {
					$_POST['E_'.$single['id']] = get_post('gross')*($single['percentage']/100);					
				} elseif($single['formula'] != '' && !is_numeric($single['formula']) && (strpos($single['formula'], '{$ctc}') === false)){
					foreach($allowance_var_ar as $key => $allown){
						$single['formula'] = str_replace($allown,$_POST[$key],strtolower($single['formula']));
					}
					if(strpos($single['formula'], '{$gros}'))
						$single['formula'] = str_replace('{$gros}', $_POST['gross'],strtolower($single['formula']));
					$_POST['E_'.$single['id']] = round(calculate_string($single['formula']),2);					
				}else
					$_POST['E_'.$single['id']] = null;				
			
				if((($single['basic'] != 1 || $basic_allowed ) && $single['gross'] != 1 ) ){
					kv_text_row_ex(trans($single['description']." :"), 'E_'.$single['id'], 15, 100, null, null, null, null, true);
				}
			}
		}
		
		$ReimbursementAllowance = get_allowances('Reimbursement', 'Profile Input', 'Percentage', 'Gross Percentage', 0, get_post('grade_id'));
		if(db_num_rows($ReimbursementAllowance) > 0){
			while ($single = db_fetch($ReimbursementAllowance)){
				if(is_numeric($single['formula']))
					$_POST['E_'.$single['id']] = $single['formula'];
				elseif($single['value'] == 'Percentage'  && $single['percentage']>0){		
					$_POST['E_'.$single['id']] = get_post($basic_id)*($single['percentage']/100);
				}elseif( $single['value'] == 'Gross Percentage' && $single['percentage']>0 ) {
					$_POST['E_'.$single['id']] = get_post('gross')*($single['percentage']/100);					
				} elseif($single['formula'] != '' && !is_numeric($single['formula']) && (strpos($single['formula'], '{$ctc}') === false)){
					foreach($allowance_var_ar as $key => $allown){
						$single['formula'] = str_replace($allown,$_POST[$key],strtolower($single['formula']));
					}
					if(strpos($single['formula'], '{$gros}'))
						$single['formula'] = str_replace('{$gros}', $_POST['gross'],strtolower($single['formula']));
					$_POST['E_'.$single['id']] = round(calculate_string($single['formula']),2);
				} else
					$_POST['E_'.$single['id']] = null;
				
				kv_text_row_ex(trans($single['description']." :"), 'E_'.$single['id'], 15, 100, null, null, null, null, true);
					
			}
		}
		$CTCAllowance = get_allowances('Employer Contribution', 'Profile Input', 'Percentage', 'Gross Percentage', 0, get_post('grade_id'));	
		if(db_num_rows($CTCAllowance) > 0){	
			while ($single = db_fetch($CTCAllowance)){
				if(is_numeric($single['formula']))
					$_POST['E_'.$single['id']] = $single['formula'];
				elseif($single['value'] == 'Percentage'  && $single['percentage']>0){		
					$_POST['E_'.$single['id']] = get_post($basic_id)*($single['percentage']/100);
				}elseif( $single['value'] == 'Gross Percentage' && $single['percentage']>0 ) {
					$_POST['E_'.$single['id']] = get_post('gross')*($single['percentage']/100);					
				} elseif($single['formula'] != '' && !is_numeric($single['formula']) && (strpos($single['formula'], '{$ctc}') === false)){
					foreach($allowance_var_ar as $key => $allown){
						$single['formula'] = str_replace($allown,$_POST[$key],strtolower($single['formula']));
					}
					if(strpos($single['formula'], '{$gros}'))
						$single['formula'] = str_replace('{$gros}', $_POST['gross'],strtolower($single['formula']));
					$_POST['E_'.$single['id']] = round(calculate_string($single['formula']),2);
				} else
					$_POST['E_'.$single['id']] = null;
				
				kv_text_row_ex(trans($single['description']." :"), 'E_'.$single['id'], 15, 100, null, null, null, null, true);
			}
		}

		$DedAllowance = get_allowances('Deductions', 'Profile Input', 'Percentage', 'Gross Percentage', 0, get_post('grade_id'), 'Formula');
		if(db_num_rows($DedAllowance) > 0){			
			while ($single = db_fetch($DedAllowance)) {				
				if($single['Tax'] != 1 && $single['loan'] != 1){
					if(is_numeric($single['formula'])){
						$_POST['E_'.$single['id']] = $single['formula'];						
					}elseif($single['value'] == 'Percentage'  && $single['percentage']>0){		
						$_POST['E_'.$single['id']] = get_post($basic_id)*($single['percentage']/100);
					}elseif( $single['value'] == 'Gross Percentage' && $single['percentage']>0 ) {
						$_POST['E_'.$single['id']] = get_post('gross')*($single['percentage']/100);					
					} elseif($single['formula'] != '' && !is_numeric($single['formula']) && (strpos($single['formula'], '{$ctc}') === false)){
						foreach($allowance_var_ar as $key => $allown){
							$single['formula'] = str_replace($allown,$_POST[$key],strtolower($single['formula']));
						}
						if(strpos($single['formula'], '{$gros}'))
							$single['formula'] = str_replace('{$gros}', $_POST['gross'],strtolower($single['formula']));
						$_POST['E_'.$single['id']] = round(calculate_string($single['formula']),2);
					}  else
						$_POST['E_'.$single['id']] = null;
					
					kv_text_row_ex(trans($single['description']." :"), 'E_'.$single['id'], 15, 100, null, null, null, null, true);
				}
			}
		}
		if(!isset($_POST['empl_id']) || $new_item){
			text_row_ex(trans("Maximum allowed Limit Percentage:"), 'expd_percentage_amt', 10, 10, '', null, null, "% for Loan Monthly Pay");
		}
		
	}	
	employee_list_row(trans("Report To:"), 'report_to', null, trans("Select an Employee"),false,false,false,true,false,false,true);
	
	/*if(!isset($_POST['empl_id']) || $new_item){
		if(GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'enable_employee_access')))
			security_roles_list_cells(trans("Access Role:"). "&nbsp;", 'role', null, false, false, check_value('show_inactive'));
		empl_shifts_list_row(trans("Employee Shift"), 'shift', null, trans("Company Time"));
		table_section_title(trans("Leave Details"));
		$_POST['al'] = $_POST['cl'] = $_POST['ml'] =  0;
		if(get_post('grade_id') || list_updated('grade_id')){
			$leave_values = GetRow('kv_empl_grade', array('id' => get_post('grade_id')));
			$_POST['al'] = round($leave_values['al']);
			$_POST['cl'] = round($leave_values['cl']);
			$_POST['ml'] = round($leave_values['ml']);
		}
		text_row(trans('Annual Leave'), 'al', null,  5, 10);
		text_row(trans('Medical Leave'), 'ml', null,  5, 10);
		text_row(trans('Casual Leave'), 'cl', null,  5, 10);
		table_section_title(trans("Unused Existing Leave Details"));
		text_row(trans('Annual Leave'), 'ual', null,  5, 10);
		text_row(trans('Medical Leave'), 'uml', null,  5, 10);
		text_row(trans('Casual Leave'), 'ucl', null,  5, 10);
	} */	
	
	end_outer_table(1);
	div_end();
	div_end();
	br();	
}

function empl_leave_data($empl_id) {
	br();
	//div_start('details');	
		
		start_table();
			kv_fiscalyears_list_row(trans("Fiscal Year:"), 'attendance_year', null, true);
		end_table();
		br();
		$selected_empl = get_employee_whole_attendance($empl_id, get_post('attendance_year'));
		
		//	var_dump($selected_empl);
		if(!empty($selected_empl)){
			$total_days =  31;
			$months_list = kv_get_months_in_fiscal_year();
			//display_notification(json_encode(array_keys($months_list)));
			$months_array_count = array_keys($months_list);
			start_table(TABLESTYLE);
			$th = array(trans("Year"),trans("Month"));
			for($kv=1; $kv<=$total_days; $kv++){						
				$th[] = $kv;
			}	
			$th1 = array(trans("Worked Days"));
			$th_final = array_merge($th, $th1);
			table_header($th_final);
						
			$total_abs_days = $counted_month = 0;
			
			//$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
			//$single_month['year'] = date("Y", strtotime($months_with_years_list[get_post('month')]));
			$total_leave = $leaveDays = $AL = $SL = $ML =$CL =  0;
			
			foreach ($selected_empl as $month => $single_month) {
				$fiscal_yr = get_fiscalyear(get_post('attendance_year')); 
				$ext_year = date('Y', strtotime($fiscal_yr['begin']));
				echo '<tr style="text-align:center"><td>'.sql2date($fiscal_yr['begin']).' - '.sql2date($fiscal_yr['end']).'</td><td>'.trans(date("F", strtotime($ext_year."-".$month."-01"))).'</td>';
				//$beginDay = date("Y-m-d", strtotime($single_month['year']."-".$single_month['month']."-01"));
				//$total_days =  date("t", strtotime($single_month['year']."-".$single_month['month']."-01"));
				$workingDays = 0 ;
				for($vj = 1; $vj <= 31; $vj++){
					echo '<td>'.(isset($single_month[$vj]) ? $single_month[$vj] : '-').'</td>';
					if(isset($single_month[$vj]) && $single_month[$vj] == 'P')
						$workingDays++;
					elseif(isset($single_month[$vj]) && $single_month[$vj] == 'A')
						$leaveDays++;
					elseif(isset($single_month[$vj]) && $single_month[$vj] == 'AL'){
						$AL++;
						$total_leave++;
					}elseif(isset($single_month[$vj]) && $single_month[$vj] == 'SL'){
						$SL++;
						$total_leave++;
					}elseif(isset($single_month[$vj]) && $single_month[$vj] == 'CL'){
						$CL++;
						$total_leave++;
					}elseif(isset($single_month[$vj]) && $single_month[$vj] == 'ML'){
						$ML++;
						$total_leave++;
					}
				}
				echo '<td>'.$workingDays.'</td></tr>';
			}
			$lastMonth = end($selected_empl);
			$lastMonth = key($lastMonth);								
			$monthLeft = 12 - $lastMonth ;
			$ALEncashed = GetSingleValue('kv_empl_leave_encashment', 'payable_days', array('empl_id' => $empl_id, 'year' => get_post('attendance_year')));
			$ALleave = GetSingleValue('kv_empl_job', 'al', array('empl_id' => $empl_id))/12;
			if($ALEncashed > 0 )
				$ALAvailable = 12*$ALleave-$ALEncashed;
			else
				$ALAvailable = 12*$ALleave;
			$ALPayable = $lastMonth*$ALleave - $ALEncashed;
			$CLleave = GetSingleValue('kv_empl_job', 'hl', array('empl_id' => $empl_id))/12;
			$CLAvailable = $CLleave-$CL;
			$SLleave = GetSingleValue('kv_empl_job', 'sl', array('empl_id' => $empl_id))/12;
			$SLAvailable = $SLleave-$SL;
			$MLleave = GetSingleValue('kv_empl_job', 'ml', array('empl_id' => $empl_id));
			$MLAvailable = $MLleave-$ML;
			end_table(1);
			br();
			echo '<center> <h3> '.("Key Statistics").' </h3> </center>';
			br();
			start_table(TABLESTYLE_NOBORDER, "width=70%");
				start_row();
					label_cell(trans("Leaves").'<br><h2>'.$total_leave.'</h2>');
					label_cell(trans("Absents").'<br><h2>'.$leaveDays.'</h2>');
					label_cell('AL<br><h2>'.$AL.'</h2>');
					label_cell('SL<br><h2>'.$SL.'</h2>');
					label_cell('ML<br><h2>'.$CL.'</h2>');					
					label_cell('ML<br><h2>'.$ML.'</h2>');					
				end_row();				
			/*	start_row();
					label_cell("<center><h3>".trans("Available Leave")."</h3></center><br> <hr>", "colspan='10'");
				end_row();
			
			echo '<hr>';
						 
				start_row();
					label_cell('AL Paid<br><h2>'.$ALEncashed.'</h2>');
					label_cell('AL Payable<br><h2>'.$ALPayable.'</h2>');
					label_cell('AL<br><h2>'.$ALAvailable.'</h2>');						
					label_cell('CL<br><h2>'.$CLAvailable.'</h2>');						
					label_cell('SL<br><h2>'.$SLAvailable.'</h2>');						
					label_cell('ML<br><h2>'.$MLAvailable.'</h2>');			 					
				end_row();*/
			end_table();			
		}else 
			display_notification(trans("No data Exist for the selected Employee."));
}

//-----------------------------------------------------------------------------------------
function empl_payroll_data($empl_id){
	global $SysPrefs, $path_to_root, $Ajax;
	br();
	start_table();
		kv_fiscalyears_list_row(trans("Fiscal Year:"), 'year', null, true);
		end_table();
		br();
	$dec = user_price_dec();
	if(list_updated('year')){
		$Ajax->activate('Payroll');
	}
	div_start('Payroll');		
		
	$get_employees_list = get_emply_salary($empl_id, get_post('year'));
	if(!empty($get_employees_list)){
		
	start_table(TABLESTYLE);
    $th = array(trans("Fiscal Year"),trans("Month"));
    $grade = GetSingleValue('kv_empl_job', 'grade', array('empl_id' => $empl_id));
    $Allowance = kv_get_allowances(null, 0, $grade);
	$Earnings_colum_count = 2;
	foreach($Allowance as $single) {	
		if($single['type'] == 'Earnings'){
			$th[] = array($single['description'] , '#f9f2bb', '#FF9800');
			$Earnings_colum_count++;
		}
	}
	$th[] = array(trans("OT"), '#f9f2bb', '#FF9800');
	$th[] = array(trans("Other Allowance"), '#f9f2bb', '#FF9800');
	$Reim_colum_count = 0;
	foreach($Allowance as $single) {	
		if($single['type'] == 'Reimbursement'){
			$th[] = array($single['description'] , '#f9f2bb', '#FF9800');
			$Reim_colum_count++;
		}
	}
	$th[] = array(trans("Gross Pay"), '#f9f2bb', '#FF9800');
	$ctc_colum_count = 0;
	foreach($Allowance as $single) {	
		if($single['type'] == 'Employer Contribution'){
			$th[] = array($single['description'] , 'rgba(156, 39, 176, 0.23)', '#9C27B0');
			$ctc_colum_count++;
		}
	}
	$Deductions_colum_count = 0;
	$th[] = array(trans("CTC"), 'rgba(156, 39, 176, 0.23)', '#9C27B0');
	foreach($Allowance as $single) {	
		if($single['type'] == 'Deductions') {
			$th[] = array($single['description'] , '#fed', '#f55');
			$Deductions_colum_count++;
		}
	}
   	$th1 = array(array(trans("LOP Days"), '#fed', '#f55'), array(trans("LOP Amount") , '#fed', '#f55'), array(trans("Misc.") , '#fed', '#f55'), array(trans("Total Deduction") , '#fed', '#f55'),array(trans("Net Salary"), '#B7DBC1' ,  '#107B0F'), trans(" "), trans(" "), trans(" "));
   	$th_final = array_merge($th, $th1);

	start_row();
	foreach($th_final as $header){
		if(is_array($header)){
			echo '<td style="background:'.$header[1].';color:'.$header[2].'"> '.$header[0].'</td>';
		} else {
			echo '<td class="tableheader"> '.$header.'</td>';
		}
	}end_row();		
			
	$Total_gross = $total_net = 0; 
	foreach($get_employees_list as $data_for_empl) { 

		if($data_for_empl) {
			start_row();
			
			$fiscal_yr = get_fiscalyear($data_for_empl['year']); 
				$employee_leave_record = get_empl_attendance_for_month($data_for_empl['empl_id'], $data_for_empl['month'], $data_for_empl['year']);
				label_cell(sql2date($fiscal_yr['begin']).' - '.sql2date($fiscal_yr['end']));
				label_cell(date("F", strtotime("2016-".$data_for_empl['month']."-01")));				
					foreach($Allowance as $single) {	
						if($single['type'] == 'Earnings')
							label_cell(number_format2($data_for_empl[$single['id']], $dec), '', 'kv_gross_amt');
					}
					label_cell($data_for_empl['ot_earnings'], '', 'kv_gross_amt');
					label_cell($data_for_empl['ot_other_allowance'], '', 'kv_gross_amt');
					foreach($Allowance as $single) {	
						if($single['type'] == 'Reimbursement')
							label_cell(number_format2($data_for_empl[$single['id']], $dec), '','kv_gross_amt');
					}
					label_cell($data_for_empl['gross'], '','kv_gross_amt');
					$ctc = $data_for_empl['gross'];
					foreach($Allowance as $single) {	
						if($single['type'] == 'Employer Contribution'){
							label_cell(number_format2($data_for_empl[$single['id']], $dec), '', 'kv_ctc_amt');
							$ctc += $data_for_empl[$single['id']];
						}
					}
					label_cell(number_format2($ctc, $dec), '', 'kv_ctc_amt');
					$total_deduct = $data_for_empl['misc']+$data_for_empl['lop_amount']; 
					foreach($Allowance as $single) {	
						if($single['type'] == 'Deductions'){
							label_cell(number_format2($data_for_empl[$single['id']], $dec), '', 'kv_ded_amt');
							$total_deduct += $data_for_empl[$single['id']];
						}
					}
					
					//label_cell($data_for_empl['adv_sal']);
					//label_cell($data_for_empl['loan'], '', 'kv_ded_amt');
					label_cell($employee_leave_record, '', 'kv_ded_amt');
					label_cell(number_format2($data_for_empl['lop_amount'], $dec), '', 'kv_ded_amt');
					label_cell(number_format2($data_for_empl['misc'], $dec), '', 'kv_ded_amt');					
					label_cell(number_format2($total_deduct, $dec), '', 'kv_ded_amt');
					label_cell(number_format2($data_for_empl['net_pay'], $dec), '', 'kv_net_amt');

					$Total_gross += $data_for_empl['gross'];
					$total_net += $data_for_empl['net_pay'];
					//label_cell($data_for_empl['other_deduction']);
					label_cell('<a href="'.$path_to_root.'/modules/ExtendedHRM/payslip.php?employee_id='.$data_for_empl['empl_id'].'&month='.$data_for_empl['month'].'&year='.$data_for_empl['year'].'" onclick="javascript:openWindow(this.href,this.target); return false;"  target="_blank" > <img src="'.$path_to_root.'/themes/default/images/gl.png" width="12" height="12" border="0" title="GL"></a>');
					label_cell('<a onclick="javascript:openWindow(this.href,this.target); return false;" href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep802.php?PARAM_0='.$data_for_empl['year'].'&PARAM_1='.$data_for_empl['month'].'&PARAM_2='.$data_for_empl["empl_id"].'&rep_v=yes" target="_blank" class="printlink"> <img src="'.$path_to_root.'/themes/default/images/print.png" width="12" height="12" border="0" title="Print"> </a>');
					label_cell('<a onclick="javascript:openWindow(this.href,this.target); return false;" href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep802.php?PARAM_0='.$data_for_empl['year'].'&PARAM_1='.$data_for_empl['month'].'&PARAM_2='.$data_for_empl["empl_id"].'&rep_v=yes&email=yes" class="printlink"> <img src="'.$path_to_root.'/modules/ExtendedHRM/images/email-icon.png" width="20" height="20" border="0" title="Print"> </a>');
				end_row();
			}
		}
		start_row();
		$gross_colm_cnt = $Earnings_colum_count+$Reim_colum_count; 
		$net_colm_cnt = $Deductions_colum_count+$ctc_colum_count+3; 
		echo " <td colspan='".$gross_colm_cnt."'> </td> <td colspan='2'><strong>Total Gross</strong></td><td><strong>".$Total_gross."</strong></td> ";
		echo "<td colspan='".$net_colm_cnt."' align='right'></td> <td colspan='2'><strong>Total Net Salary</strong></td> <td><strong>". $total_net."</strong></td><td colspan='3'> </td>";
			
		end_row();		
    end_table(1);
	}else {
		display_notification(trans("No data Exist for the selected Employee."));
	}
	div_end();
}

//-----------------------------------------------------------------------------------------
function empl_notes($empl_id){
	global $Ajax;
	
	if (isset($_POST['submitNote'])) {
		Update('kv_empl_info', array('empl_id' => $_POST['empl_id']), array('notes' => $_POST['notes']));
		display_notification(trans("Note Updated successfully"));
	}

	br();
	$_POST['notes'] = GetSingleValue('kv_empl_info', 'notes', array('empl_id' => $empl_id));
	
	echo '<div style="width: 65%;margin: 0 auto;"> '.htmlspecialchars_decode($_POST['notes']).'</div>';
	
	br(2);	
}
//-------------------------------------------------------------------------------------------- 
start_form(true);

if (db_has_employees()) {
	start_table(TABLESTYLE_NOBORDER);
	start_row();
   
$Ajax->activate('empl_id');
	$new_item = get_post('empl_id')=='';
	end_row();
	end_table();

	if (get_post('_show_inactive_update')) {
		
		set_focus('empl_id');
	}
} else {
	hidden('empl_id', get_post('empl_id'));
}

div_start('details');

$empl_id = get_post('empl_id');
if (!$empl_id)
	unset($_POST['_tabs_sel']); // force settings tab for new customer

$tab_cont_ar = array(
		'personal' => array(trans("Personal Info"), $empl_id),
		'job' => array(trans("Job"), (user_check_access('HR_EML_PROFILE') ? $empl_id : null) ),
		'leave' => array(trans("Attendance"), (user_check_access('HR_EML_LEAVE_INQUIRY') ? $empl_id : null)),
		'empl_leave' => array(trans("Leave"), (user_check_access('HR_EML_LEAVE') ? $empl_id : null) ),
		'payroll' => array(trans("Payroll History"), (user_check_access('HR_EML_PAYROLL') ? $empl_id : null ) ),
		'loan' => array(trans("Loan History"), (user_check_access('HR_EML_LOAN') ? $empl_id : null )),
		'license' => array(trans("License"), $empl_id),
		'education' => array(trans("Education"), $empl_id),
		'skills' => array(trans("Language"), $empl_id),
		'previous_emplment' => array(trans("Employment History"), $empl_id),
		'training' => array(trans("Training"), $empl_id),
		'attachments' => array(trans("Attachments"), $empl_id),
		'family' => array(trans("Family"), $empl_id),
		'note' => array(trans("Notes"), $empl_id)
	);
if(!GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'license_mgr')))
		unset($tab_cont_ar['license']);

tabbed_content_start('tabs', $tab_cont_ar);
	
	switch (get_post('_tabs_sel')) {
		default:
		case 'personal':
			empl_personal_data($empl_id); 
			break;
		case 'job':			
			//empl_job_data($empl_id);	
			$_GET['empl_id'] = $empl_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/modules/ExtendedHRM/employee/add_empl_info_job.php");			
			break;
		case 'loan':			
			//empl_job_data($empl_id);	
			$_GET['empl_id'] = $empl_id;
			$_GET['page_level'] = 1;
			include_once($path_to_root."/modules/ExtendedHRM/manage/loan_form.php");			
			break;
		case 'education':
			$degree = new empl_degree('degree', $empl_id, 'employee');
			$degree->show();	
			break;
		case 'skills':
			$degree = new empl_skill('skills', $empl_id, 'employee');
			$degree->show();	
			break;
		case 'training':
			$training = new empl_training('training', $empl_id, 'employee');
			$training->show();
			break;
		case 'previous_emplment':
			$exp = new empl_experience('previous_emplment', $empl_id, 'employee');
			$exp->show();
			break;
		case 'license':
			$exp = new empl_license('license', $empl_id, 'employee');
			$exp->show();
			break; 
		case 'leave':
			empl_leave_data($empl_id); 
			break;
		case 'payroll':			
			empl_payroll_data($empl_id); 
			break;
		case 'empl_leave':
			$exp = new empl_leave('leave', $empl_id, 'employee');
			$exp->show();
			break;
		case 'attachments':
			$_GET['empl_id'] = $empl_id;
			$_GET['page_level'] = 1;
			include_once($path_to_root."/modules/ExtendedHRM/employee/attachments.php"); 
			break;
		case 'family':
			$_GET['empl_id'] = $empl_id;
			$_GET['page_level'] = 1;
			include_once($path_to_root."/modules/ExtendedHRM/employee/family_data.php"); 
			break;
		case 'note':
			empl_notes($empl_id); 
			break;
	}
br();
tabbed_content_end();
div_end();
end_form();
end_page(@$_REQUEST['popup']);
?>
<style>
#empl_profile_pic { border: 1px solid rgba(128, 128, 128, 0.68);    border-radius: 2px;}
td#kv_gross_amt {	color: #FF9800;    /*background-color: #f9f2bb; */ }
td#kv_ctc_amt{	color: #9C27B0;    /*background-color: rgba(156, 39, 176, 0.23); */ }
td#kv_net_amt{	color: #107B0F;   /* background-color: #B7DBC1; */}
td#kv_ded_amt{	color: #107B0F;   /* background-color: #f55; */}
ul.ajaxtabs li button {      padding: 3px 15px; } 
table { width: auto; }
table.tablestyle_noborder {
    width: 70%;
}
</style>