<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HRM_LOAN_MASTER';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
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
 
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
page(trans("Loan Type Setup"));
 
simple_page_mode(true);
//----------------------------------------------------------------------------------------------------

function can_process(){
	if (strlen($_POST['loan_name']) == 0){
		display_error(trans("The loans type description cannot be empty."));
		set_focus('s_date');
		return false;
	}

	if (strlen($_POST['loan_name']) == 0){
		display_error(trans("Calculation factor must be valid positive number."));
		set_focus('s_date');
		return false;
	}
	return true;
}

//------------------------------------------------------------------------------------
if ($Mode=='ADD_ITEM' && can_process()){
	Insert('kv_empl_loan_types', array('loan_name' => $_POST['loan_name'], 'allowance_id' => $_POST['allowance_id'], 'interest_rate' => $_POST['interest_rate']));
	display_notification(trans("New Loan type has been added"));
	$Mode = 'RESET';
}

//----------------------------------------------------------------------------------------
if ($Mode=='UPDATE_ITEM' && can_process()){

	Update('kv_empl_loan_types', array('id' => $selected_id), array('loan_name' => $_POST['loan_name'], 'allowance_id' => $_POST['allowance_id'], 'interest_rate' => $_POST['interest_rate']));
	display_notification(trans("Selected Loan type has been updated"));
	$Mode = 'RESET';
}

//----------------------------------------------------------------------------------------------------

if ($Mode == 'Delete'){	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtor_trans'
	if (key_in_foreign_table($selected_id, 'kv_empl_loan', 'loan_type_id'))	{
		display_error(trans("Cannot delete this loan type because employees are currently set up to use this loans type."));
	}else	{
		Delete('kv_empl_loan_types', array('id' => $selected_id));
		display_notification(trans("Selected Loan type has been deleted"));
	}
	$Mode = 'RESET';
}

if ($Mode == 'RESET'){
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$Ajax->activate('_page_body');
	$_POST['show_inactive'] = $sav;
}
//-------------------------------------------------------------------
$result = GetAll('kv_empl_loan_types');

start_form();
	start_table(TABLESTYLE, "width=30%");
		$_POST['interest_rate'] = $_POST['loan_name'] = ''; 
		//$th = array (trans('Loan Name'), trans('Interest Rate %'), trans("Allowance Name"), '','');
		$th = array (trans('Loan Name'), '','');
		inactive_control_column($th);
		table_header($th);
		$k = 0;

		foreach($result as $myrow){
			
			label_cell($myrow["loan_name"]);	
			//label_cell($myrow["interest_rate"]);
			//label_cell(GetSingleValue('kv_empl_allowances', 'description', array('id' => $myrow["allowance_id"])));
		 	edit_button_cell("Edit".$myrow['id'], trans("Edit"));
		 	delete_button_cell("Delete".$myrow['id'], trans("Delete"));
			end_row();
		}
		//inactive_control_row($th);
	end_table();
	br(2); 
	//----------------------------------------------------------------
	start_table(TABLESTYLE2);
		table_section_title(trans("Loan Types Entry"));
		if ($selected_id != -1){

		 	if ($Mode == 'Edit') {
				$myrow = GetRow('kv_empl_loan_types', array('id' => $selected_id));
		//print_r($myrow);
				$_POST['loan_name']  = $myrow[1];
				$_POST['interest_rate']  = $myrow[2];
				$_POST['allowance_id']  = $myrow[3];
			}
			hidden('selected_id', $selected_id);
		}

		text_row(trans("Loan Name")." :", 'loan_name', $_POST['loan_name'], 40, 80);
		//text_row(trans("Interest Rate:"), 'interest_rate', $_POST['interest_rate'], 6, 8);
		//text_row_ex(trans("Interest Rate")." :", 'interest_rate', 10, 10, '', null, null, "%");
		//hrm_empl_allowances_list_row(trans("Loan Allowances:"), 'allowance_id');

	end_table(1);

	submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();
 
end_page();
 
?>
