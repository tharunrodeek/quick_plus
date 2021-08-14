<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'SA_OPEN';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
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
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

page(trans("Taxes"), @$_REQUEST['popup'], false, "", $js);
 
 //check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));
 
 check_db_has_salary_account(trans("There are no Salary Account defined in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/hrm_settings.php'>Settings</a> to update it."));


simple_page_mode(true);

if (isset($_GET['year'])){
	$_POST['year'] = $_GET['year'];
}

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){

	$input_error = 0;

	/*if (!is_numeric(input_num('min_sal')) || (input_num('min_sal')<= 0) ) {
		$input_error = 1;
		display_error(trans("The Minimum Salary cannot be empty | Non numeric OR less than zero."));
		set_focus('min_sal');
	}

	if (!is_numeric(input_num('max_sal')) || (input_num('max_sal')<1 ) || (input_num('max_sal')< input_num('min_sal') ) ) {
		$input_error = 1;
		display_error(trans("The Maximum Salary cannot be empty | Non numeric OR less than zero."));
		set_focus('max_sal');
	}*/

	if ($input_error != 1)	{
    	if ($selected_id != -1)   {			
    		Update('kv_empl_esic_pf', array('id' => $selected_id), array('year' => $_POST['selected_year'], 'description' => $_POST['description'], 'min_sal' => input_num('min_sal'), 'max_sal' => input_num('max_sal'), 'percentage' => $_POST['percentage'],  'offset' => $_POST['offset']));	
    		//kv_update_tax($selected_id,  $, $,input_num('min_sal'), input_num('max_sal'), , $_POST['frequency'], $_POST['offset']);
			$note = trans("Selected Tax Type has been updated");
		} 	else  	{			
			Insert('kv_empl_esic_pf', array('year' => $_POST['selected_year'], 'description' => $_POST['description'], 'min_sal' => input_num('min_sal'), 'max_sal' => input_num('max_sal'), 'percentage' => $_POST['percentage'],  'offset' => $_POST['offset']));
    		//kv_add_tax($_POST['selected_year'], $_POST['description'], , , $_POST['percentage'], $_POST['frequency'], $_POST['offset']);
			$note = trans("New Tax type has been added");
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete'){

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	/*if (key_in_foreign_table($selected_id, 'kv_empl_job', 'department'))	{
		$cancel_delete = 1;
		display_error(trans("Cannot delete this department because Employees have been created using this department."));
	} */
	if ($cancel_delete == 0) {
		//kv_delete_tax($selected_id);
		//display_notification(trans("Selected  Tax has been deleted"));
	} //end if Delete department
	$Mode = 'RESET';
} 

if ($Mode == 'RESET'){
	$selected_id = -1;
	$sav = get_post('show_inactive');
	$selected_year = $_POST['selected_year'];
	unset($_POST);
	$_POST['year'] = $selected_year;
	if ($sav) $_POST['show_inactive'] = 1;
}


start_form();
	
	//$result = GetAll('kv_empl_esic_pf');
	$result = GetDataJoin('kv_empl_esic_pf AS esic', array( 
					0 => array('join' => 'INNER', 'table_name' => 'kv_empl_allowances AS allowance', 'conditions' => '`allowance`.`id` = `esic`.`allowance_id`'),
				), 
				array('`allowance`.`description`, `esic`.`amt_limit`, `esic`.`date`, `esic`.`employer`, `esic`.`company`'));

	start_form();
	start_table(TABLESTYLE);
	$th = array(trans("Components"), trans("Limit"), trans("Date"), trans("Employer Contribution"), trans("Company Contribution"),"", "");
	//inactive_control_column($th);

	table_header($th);
	$k = 0; 

	foreach($result as $myrow) {
		
		alt_table_row_color($k);
			
		label_cell($myrow["description"]);
		label_cell($myrow["amt_limit"]);
		label_cell(sql2date($myrow["date"]));		//label_cell($myrow["frequency"].' Months');
		label_cell($myrow["employer"]);
		label_cell($myrow["company"]);
	 	edit_button_cell("Edit".$myrow["id"], trans("Edit"));
	 	delete_button_cell("Delete".$myrow["id"], trans("Delete"));
		end_row();
	}

	//inactive_control_row($th);
	end_table(1);



	//-------------------------------------------------------------------------------

	start_table(TABLESTYLE2);

	if ($selected_id != -1) {
	 	if ($Mode == 'Edit') {
			//editing an existing department
			$myrow = GetRow('kv_empl_esic_pf', array('id' => $selected_id));

			$_POST['allowance_id']  = $myrow["allowance_id"];
			$_POST['amt_limit']  = $myrow["amt_limit"];
			$_POST['date']  = $myrow["date"];
			$_POST['employer']  = $myrow["employer"];
			$_POST['company']  = $myrow["company"];	
		}

		hidden("selected_id", $selected_id);
		
		label_row(trans("ID"), $myrow["id"]);
	} 
	text_row_ex(trans("Description:"), 'description', 30);
	amount_row(trans("Minimum Salary")." :", 'min_sal');
	amount_row(trans("Maximum Salary")." :", 'max_sal');
	text_row_ex(trans("Percentage")." :", 'percentage', 10, 10, '', null, null, "%");	//TaxFrequency_List_row(trans("Frequency"), 'frequency', null );
	text_row_ex(trans("Offset")." :", 'offset', 30);
	end_table(1);

	submit_add_or_update_center($selected_id == -1, '', 'both');


	//display_notification($selected_id);

	end_form();
end_page();
?>