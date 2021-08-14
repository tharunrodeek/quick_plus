<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'SA_OPEN';
$path_to_root="../../..";
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

	if (!is_numeric(input_num('min_sal')) || (input_num('min_sal')<= 0) ) {
		$input_error = 1;
		display_error(trans("The Minimum Salary cannot be empty | Non numeric OR less than zero."));
		set_focus('min_sal');
	}

	if (!is_numeric(input_num('max_sal')) || (input_num('max_sal')<1 ) || (input_num('max_sal')< input_num('min_sal') ) ) {
		$input_error = 1;
		display_error(trans("The Maximum Salary cannot be empty | Non numeric OR less than zero."));
		set_focus('max_sal');
	}

	if ($input_error != 1)	{
		if(get_post('per_month')) {
			$min_sal = input_num('min_sal')*12;
			$max_sal = input_num('max_sal')*12;
		} else {
			$min_sal = input_num('min_sal');
			$max_sal = input_num('max_sal');
		}
    	if ($selected_id != -1)   {			
    		Update('kv_empl_taxes', array('id' => $selected_id), array('year' => $_POST['selected_year'], 'description' => $_POST['description'], 'min_sal' => $min_sal, 'max_sal' => $max_sal, 'taxable_salary' => input_num('taxable_salary'), 'percentage' => $_POST['percentage'],  'offset' => $_POST['offset']));	
    		$note = trans("Selected Tax Type has been updated");
		} 	else  	{			
			Insert('kv_empl_taxes', array('year' => $_POST['selected_year'], 'description' => $_POST['description'], 'min_sal' => $min_sal, 'max_sal' => $max_sal,  'taxable_salary' => input_num('taxable_salary'), 'percentage' => $_POST['percentage'],  'offset' => $_POST['offset']));
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
		Delete('kv_empl_taxes', array('id' => $selected_id));	
		display_notification(trans("Selected Tax has been deleted"));
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

if(list_updated('per_month') ){
  	//if(get_post('per_month'))
		//$checkAll = 12;
	//else
   		//$checkAll = 1;
   	$Ajax->activate('TaxDisplay');
}//else
  	//$checkAll = 1;

  $dec = get_qty_dec();

start_form();
	//if (db_has_employees()) {
		start_table(TABLESTYLE_NOBORDER);
		start_row();
		kv_fiscalyears_list_cells(trans("Fiscal Year:"), 'year', null, true);
		check_cells(trans("Show Per month"), 'per_month', null, true);
		end_row();
		end_table();
		br();
		if (get_post('_show_inactive_update')) {
			$Ajax->activate('year');
			set_focus('year');
		}
	//} 
	//else {			
	//	hidden('year');
	//}

	$year = get_post('year');

	$rows = GetAll('kv_empl_taxes', array('year' => $year));

	div_start('TaxDisplay');
	start_table(TABLESTYLE, "width=60%");
	if(get_post('per_month')){
		$th = array(trans("Year"), trans("Description"), trans("Minimum Wage/Month"), trans("Maximum Wage/Month"), trans("Percentage(%)"),trans("Taxable Salary"), trans("Offset"),"", "");
		$groupString = trans("Month");
		$checkAll = 12;
	} else {
		$th = array(trans("Year"), trans("Description"), trans("Minimum Wage/Annum"), trans("Maximum Wage/Anum"), trans("Percentage(%)"),trans("Taxable Salary"), trans("Offset"),"", "");
		$groupString = trans("Annum");
		$checkAll = 1;
	}
	//inactive_control_column($th);

	table_header($th);
	$k = 0; 

	foreach($rows as $myrow) {
		
		alt_table_row_color($k);
			$yearselected = get_fiscalyear($myrow['year']);
		label_cell(sql2date($yearselected['begin']).' - '.sql2date($yearselected['end']));
		label_cell($myrow["description"]);
		label_cell(round($myrow["min_sal"]/$checkAll, $dec));
		label_cell(round($myrow["max_sal"]/$checkAll, $dec));
		label_cell($myrow["percentage"]);		
		label_cell($myrow["taxable_salary"]);
		label_cell($myrow["offset"]);
		//inactive_control_cell($myrow["id"], $myrow["inactive"], 'departments', 'id');
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
			$myrow = GetRow('kv_empl_taxes', array('id' => $selected_id));

			$_POST['year']  = $myrow["year"];
			$_POST['description']  = $myrow["description"];
			$_POST['min_sal']  = round($myrow["min_sal"]/$checkAll, $dec);
			$_POST['max_sal']  = round($myrow["max_sal"]/$checkAll, $dec);
			$_POST['taxable_salary']  = $myrow["taxable_salary"];
			$_POST['percentage']  = $myrow["percentage"];			//$_POST['frequency']  = $myrow["frequency"];
			$_POST['offset']  = $myrow["offset"];
		}

		hidden("selected_id", $selected_id);
		
		label_row(trans("ID"), $myrow["id"]);
	} 
	hidden("selected_year", $_POST['year']);
	text_row_ex(trans("Description"). " :", 'description', 30);
	amount_row(trans("Minimum Salary"). " :", 'min_sal', null, null, '/'.$groupString );
	amount_row(trans("Maximum Salary"). " :", 'max_sal', null, null, '/'.$groupString );
	amount_row(trans("Taxable Salary"). " :", 'taxable_salary', null, null);
	text_row_ex(trans("Percentage"). " :", 'percentage', 10, 10, '', null, null, "%");	//TaxFrequency_List_row(trans("Frequency"), 'frequency', null );
	text_row_ex(trans("Offset"). " :", 'offset', 30);
	end_table(1);

	submit_add_or_update_center($selected_id == -1, '', 'both');

	div_end();
	//display_notification($selected_id);

	end_form();
end_page();
?>