<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_PAYROLL_SETUP';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
$js = '';
global $allowances_type_list; 
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
page(trans("Allowances Setup"), false, false, "", $js);
CheckEmptyResult('kv_empl_grade', sprintf(trans("There is no grade in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/grade.php'>".trans("Grades")."</a>"));
simple_page_mode(true);

if(kv_check_payroll_table_exist()){
	$disabled = true;
}else{
	$disabled = false;
}
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){

	$input_error = 0;
	if($_POST['grade_id'] < 1 ) {
		$input_error = 1;
		display_error(trans("The Grade cannot be empty."));
		set_focus('grade_id');	
	}
	if (strlen($_POST['description']) == 0) {
		$input_error = 1;
		display_error(trans("The Allowance description cannot be empty."));
		set_focus('description');
	}
	if (strlen($_POST['debit_code']) > 0 || strlen($_POST['credit_code']) > 0) {
		if (strlen($_POST['debit_code']) == 0 && strlen($_POST['credit_code']) > 0) {
			$input_error = 1;
			display_error(trans("The Allowance Debit Code cannot be empty."));
			set_focus('debit_code');
		}
		
		if (strlen($_POST['debit_code']) > 0 && strlen($_POST['credit_code']) == 0 ) {
			$input_error = 1;
			display_error(trans("The Allowance Credit Code cannot be empty."));
			set_focus('credit_code');
		}
	}
	$unique_name_length = strlen(trim($_POST['unique_name']));
	if ($unique_name_length == 0) {
		$input_error = 1;
		display_error(trans("The Unique Name cannot be empty."));
		set_focus('unique_name');
	}
	if($unique_name_length != 4){
		$input_error = 1;		
		display_error(trans("The Unique Name must be 4 letters, no space or symbols allowed."));
		set_focus('unique_name');
	}elseif (preg_match('/[^A-Za-z0-9]/', trim($_POST['unique_name']))){
		display_error(trans("The Unique Name must be 4 letters and numbers."));
		$input_error = 1;
		set_focus('unique_name');
	}			

	if(db_has_this_allowance_unique_name($_POST['unique_name'], $selected_id, $_POST['grade_id'])){
		$input_error = 1;
		display_error(trans("The Allowance Unique name already exist."));
		set_focus('unique_name');
	}

	if($selected_id == -1 && db_has_this_allowance($_POST['description'])){
		$input_error = 1;
		display_error(trans("The Allowance name already exist."));
		set_focus('description');
	}
	if (isset($_POST['Tax']) && $_POST['Tax'] == 1 && $_POST['type'] == 'Earnings') {
		$input_error = 1;
		display_error(trans("The Tax Can't be an Earnings."));
		set_focus('type');
	}

	if (isset($_POST['basic']) && $_POST['basic'] == 1 && $_POST['type'] == 'Deductions') {
		$input_error = 1;
		display_error(trans("The Basic Can't be a Deduction."));
		set_focus('type');
	}

	if (isset($_POST['loan']) && $_POST['loan'] == 1 && $_POST['type'] == 'Earnings') {
		$input_error = 1;
		display_error(trans("The Loan Can't be an Earnings."));
		set_focus('type');
	}

	if (isset($_POST['basic']) && $_POST['basic'] == 1 && $_POST['value'] == 'Percentage') {
		$input_error = 1;
		display_error(trans("The Basic Can't set in Percentage."));
		set_focus('type');
	}

	if (isset($_POST['basic']) && $_POST['basic'] == 1 && ($_POST['Tax'] == 1 || $_POST['loan'] == 1 || $_POST['esic'] == 1 || $_POST['pf'] == 1 )) {
		$input_error = 1;
		display_error(trans("The Basic Can't be Reassigned."));
		set_focus('type');
	}

	/*if (isset($_POST['Tax']) && $_POST['Tax'] == 1 && ( $_POST['loan'] == 1 || $_POST['esic'] == 1 || $_POST['pf'] == 1 )) {
		$input_error = 1;
		display_error(trans("The Tax Can't be Reassigned."));
		set_focus('type');
	}*/
	if($_POST['value'] == 'Formula' && $_POST['formula'] == ''){
		$input_error = 1;
		display_error(trans("You have empty formula."));
		set_focus('type');
	}
	if ($input_error != 1)	{
		begin_transaction();
		$gross = (isset($_POST['gross']) ? $_POST['gross']: 0 );
		if($_POST['description'] > 0 ){
			$_POST['description'] = GetSingleValue('kv_empl_allowances', 'description', array('id' => $_POST['description']));
		}else
			$_POST['description'] = $_POST['description_'];
		
    	if ($selected_id != -1)   {
    		update_allowance($selected_id, $_POST['debit_code'], $_POST['credit_code'],$_POST['formula'], $_POST['description'], $_POST['type'], $_POST['value'], $gross,
    			(isset($_POST['basic']) && $_POST['basic'] != '' ? $_POST['basic']: 0 ), 
    			(isset($_POST['percentage']) && $_POST['percentage'] != '' ? $_POST['percentage']: 0 ), 
    			(isset($_POST['Tax'])  && $_POST['Tax'] != '' ? $_POST['Tax']: 0 ), 
    			(isset($_POST['loan'])  && $_POST['loan'] != '' ? $_POST['loan']: 0 ), 
    			(isset($_POST['esic'])  && $_POST['esic'] != '' ? $_POST['esic']: 0 ), 
    			(isset($_POST['pf'])  && $_POST['pf'] != '' ? $_POST['pf']: 0 ),
				$_POST['al_type'],				
    			(isset($_POST['inactive'])  && $_POST['inactive'] != '' ? $_POST['inactive']: 0 ), 
    			$_POST['sort_order'], strtolower($_POST['unique_name']), $_POST['grade_id']);
			$note = trans("Selected Allowance has been updated");

			if($_POST['formula'] != ''){
				$sql =" UPDATE ".TB_PREF."kv_empl_job SET `".$selected_id."` ='0' ";
				$result = db_query($sql, "Can't update formula change here on job information table");
			}
    	}  else  {
    		
    		$Input_id = add_allowance($_POST['debit_code'], $_POST['credit_code'],$_POST['formula'], $_POST['description'], $_POST['type'], $_POST['value'],$gross,
    			(isset($_POST['basic']) && $_POST['basic'] != '' ? $_POST['basic']: 0 ), 
    			(isset($_POST['percentage']) && $_POST['percentage'] != '' ? $_POST['percentage']: 0 ), 
    			(isset($_POST['Tax'])  && $_POST['Tax'] != '' ? $_POST['Tax']: 0 ), 
    			(isset($_POST['loan'])  && $_POST['loan'] != '' ? $_POST['loan']: 0 ), 
    			(isset($_POST['esic'])  && $_POST['esic'] != '' ? $_POST['esic']: 0 ), 
    			(isset($_POST['pf'])  && $_POST['pf'] != '' ? $_POST['pf']: 0 ), 
				$_POST['al_type'],
    			(isset($_POST['inactive'])  && $_POST['inactive'] != '' ? $_POST['inactive']: 0 ), 

    			$_POST['sort_order'], strtolower($_POST['unique_name']), $_POST['grade_id']);
			
			if($gross ==  0){
				$rslt = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_salary` LIKE '{$Input_id}'", "Can't Query this Table");
				$exists = (db_num_rows($rslt))?TRUE:FALSE;
				if(!$exists) {
					$sql0 ="ALTER TABLE `".TB_PREF."kv_empl_salary` ADD `{$Input_id}` double NOT NULL DEFAULT '0' ";
					db_query($sql0, "Db Table creation failed, Kv empl_salary table");
				}
				$result = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_job` LIKE '{$Input_id}'", "Can't Query this Table");
				$existss = (db_num_rows($result))?TRUE:FALSE;
				if(!$existss) {
					$sql1 ="ALTER TABLE `".TB_PREF."kv_empl_job` ADD `{$Input_id}` double NOT NULL DEFAULT '0' ";
					db_query($sql1, "Db Table creation failed, Kv empl_job table");
				}
			}
				
			$note = trans("New Allowance has been added");
    	}
		commit_transaction();
		display_notification($note); 
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete'){
	$delete_allow = 0; 
	$result = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_job` LIKE '{$selected_id}'", "Can't Query this Table");
	$exists = (db_num_rows($result))?TRUE:FALSE;
	if($exists) {
	   	$sql = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_job WHERE `{$selected_id}` > 0"; 
	   	$result =  db_query($sql,"could not get kv_empl_departments");
		$row = db_fetch($result);
		if($row[0]> 0){
			$delete_allow = 1; 
		}else{
			$delete_allow = 0; 
		}
	}
	$result = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_salary` LIKE '{$selected_id}'", "Can't Query this Table");
	$exists = (db_num_rows($result))?TRUE:FALSE;
	if($exists) {
	   	$sql = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_salary WHERE `{$selected_id}` > 0"; 
	   	$result =  db_query($sql,"could not get kv_empl_departments");
		$row = db_fetch($result);
		if($row[0]> 0){
			$delete_allow = 1; 
		}else{
			$delete_allow = 0; 
		}
	}

	if($delete_allow== 0){
		begin_transaction();
		$res = delete_allowance($selected_id);
		commit_transaction();
		display_notification(trans("Selected  Allowance has been deleted"));
		$Mode = 'RESET';
	}else{
		display_warning(sprintf(trans("Sorry, you cannot Delete this Allowance. Its already used and created with few entires in it."),$row[0]));
	}
} 

if ($Mode == 'RESET'){
	$selected_id = -1;
	$grade = $_POST['grade_id']; 
	unset($_POST);	
	$_POST['grade_id'] = $grade;
}

//----------------------------------------------------------------------------------------

start_form();

	start_table(TABLESTYLE_NOBORDER);
	start_row();
	kv_empl_grade_list_cells(trans("Select A Grade:"), 'grade_id', null, trans("Select Grade"), true);
	$new_item = get_post('selected_id')=='';
	end_row();
	end_table();

	if (get_post('_show_inactive_update')|| list_updated('grade_id') || list_updated('description')) {		
		set_focus('grade_id');
		$_POST['description'] =  get_post('description');
		$Ajax->activate('AllowanceDetails');
	}
	
	br();
div_start('AllowanceDetails');

$all_settings1 =  GetAll('kv_empl_option');
		$hrmsetup = array(); 
		foreach($all_settings1 as $settings){
			$data_offdays = @unserialize(base64_decode($settings['option_value']));
			if ($data_offdays !== false) {
				$hrmsetup[$settings['option_name']] = unserialize(base64_decode($settings['option_value']));
			} else {
				$hrmsetup[$settings['option_name']] = $settings['option_value']; 
			}
		}
		
	start_table(TABLESTYLE, "width=80%");
	$th = array( trans("Unique Name"), trans("Name  - (Debit) (Credit)"), trans("Type"), trans("Input Type"), trans("Formula"), trans("Gross"),trans("Basic"), trans("Tax"), trans("Loan"), trans("ESIC"), trans("PF"), trans("Allowance Type"), trans("Status"),"","");
		if(!isset($hrmsetup['enable_pf']))
			unset($th[10]);
		if(!isset($hrmsetup['enable_esic']))
			unset($th[9]);
		if(!isset($hrmsetup['tax_used']))
			unset($th[7]);
	table_header($th);

	if(get_post('grade_id') > 0){
			$result = GetDataJoin('kv_empl_allowances AS allowance', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_allowance_advanced AS adv', 'conditions' => '`adv`.`allowance_id` = `allowance`.`id`')
			), array('`allowance`.*,  `adv`.`formula`, `adv`. `value`, `adv`.`percentage`, `adv`.`id` AS al_id'), array('`adv`.`grade_id`' => get_post('grade_id')), array('`allowance`.`sort_order`' => 'asc'));
		}else 
			display_warning(trans("Please Select a Grade to continue"));
				
	if(isset($result) && $result){
		foreach($result as $myrow) {	
			label_cell($myrow["unique_name"]);
			label_cell($myrow["description"].' '. ($myrow['debit_code'] != 0 ? '('.$myrow['debit_code'].')' : '').' '.($myrow['credit_code'] != 0 ? '('.$myrow['credit_code'].')' : ''));
			label_cell($myrow["type"]);		
			label_cell(($myrow["value"] == 'Percentage' ? trans("Basic"). " ". $myrow["value"].'('.$myrow["percentage"].'%)': ($myrow["value"] == 'Gross Percentage' ? $myrow["value"].'('.$myrow["percentage"].'%)': $myrow["value"])));
			label_cell(($myrow["formula"] ? $myrow["formula"] :  '-'));
			label_cell(($myrow["gross"]== 1 ? 'Yes' :  '-'));
			label_cell(($myrow["basic"]== 1 ? 'Yes' :  '-'));
			if(isset($hrmsetup['tax_used']))
				label_cell(($myrow["Tax"]== 1 ? 'Yes' :  '-'));
			label_cell(($myrow["loan"]== 1 ? 'Yes' :  '-'));
			if(isset($hrmsetup['enable_esic']))
				label_cell(($myrow["esic"]== 1 ? 'Yes' :  '-'));
			if(isset($hrmsetup['enable_pf']))
				label_cell(($myrow["pf"]== 1 ? 'Yes' :  '-'));
			label_cell($allowances_type_list[$myrow["al_type"]]);
			label_cell(($myrow["inactive"]== 1 ? 'Inactive' :  'Active'));

			edit_button_cell("Edit".$myrow["id"], trans("Edit"));
			kv_delete_button_cell("Delete".$myrow["al_id"], trans("Delete"), false); //, $disabled);
			end_row();
		}
	}
	end_table(1);

	//----------------------------------------------------------------------------------------
	start_table(TABLESTYLE2);
		$max_sort = GetSingleValue('kv_empl_allowances', "MAX(sort_order)")+ 1;
	if ($selected_id != -1) {
		if ($Mode == 'Edit') { //editing an existing department
			$myrow = GetDataJoinRow('kv_empl_allowances AS allowance', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_allowance_advanced AS adv', 'conditions' => '`adv`.`allowance_id` = `allowance`.`id`')
			), array('`allowance`.*,  `adv`.`formula`, `adv`. `value`, `adv`.`percentage`'), array('`adv`.`grade_id`' => get_post('grade_id'), '`allowance`.`id`' => $selected_id), array('`allowance`.`sort_order`' => 'asc'));  //get_allowance($selected_id);
			//display_error($selected_id.'--'.json_encode($myrow));
			$_POST['description']  = $myrow["id"];
			$_POST['unique_name']  = $myrow["unique_name"];
			$_POST['type']  = $myrow["type"];
			$_POST['value']  = $myrow["value"];
			$_POST['basic']  = $myrow["basic"];
			$_POST['gross']  = $myrow["gross"];
			$_POST['Tax']  = $myrow["Tax"];
			$_POST['loan']  = $myrow["loan"];
			$_POST['esic']  = $myrow["esic"];
			$_POST['pf']  = $myrow["pf"];
			$_POST['al_type']  = $myrow["al_type"];
			$_POST['sort_order']  = $myrow["sort_order"];
			$_POST['debit_code']  = $myrow["debit_code"];
			$_POST['credit_code']  = $myrow["credit_code"];
			$_POST['inactive']  = $myrow["inactive"];
			$_POST['formula']  = $myrow["formula"];
			$_POST['percentage']  = $myrow["percentage"];
		}
		hidden("selected_id", $selected_id);
	}else {
		$_POST['gross'] = $_POST['basic'] = $_POST['Tax'] = $_POST['loan'] = $_POST['esic'] = $_POST['pf'] = $_POST['inactive'] = 0; 
		$_POST['credit_code'] = (isset($_POST['credit_code']) ? $_POST['credit_code'] : 0);		
		$_POST['debit_code'] = (isset($_POST['debit_code']) ? $_POST['debit_code'] : 0);
		$_POST['percentage'] = (isset($_POST['percentage']) ? $_POST['percentage'] : 0);
		$_POST['formula'] = (isset($_POST['formula']) ? $_POST['formula'] : '');
		$_POST['unique_name'] = (isset($_POST['unique_name']) ? $_POST['unique_name'] : '');
		$_POST['sort_order'] = (isset($_POST['sort_order']) ? $_POST['sort_order'] : $max_sort); 
	} 
	$_POST['description'] = (isset($_POST['description']) ? $_POST['description'] : $selected_id);
	if(list_updated('description')){
		$selected_row = GetRow('kv_empl_allowances', array('id' => $_POST['description']));
		$_POST['unique_name'] = $selected_row['unique_name'];
		$_POST['sort_order'] = $selected_row['sort_order'];
		$_POST['basic'] = $selected_row['basic'];
		$_POST['gross'] = $selected_row['gross'];
		$_POST['Tax'] = $selected_row['Tax'];
		$_POST['loan'] = $selected_row['loan'];
		$_POST['type'] = $selected_row['type'];
		//$_POST['value'] = $selected_row['value'];
		$_POST['debit_code'] = $selected_row['debit_code'];
		$_POST['credit_code'] = $selected_row['credit_code'];
		//$_POST['percentage'] = $selected_row['percentage'];
	}
	$_POST['gross'] = (isset($_POST['gross']) ? $_POST['gross'] : 0);
	empl_allowances_list_row(trans("Allowance Name:"), 'description', null, trans("Select an Allowance"), true, true);
	kv_text_row_ex(trans("Allowance Unique Name:"), 'unique_name', 30, null);

	//gl_account_types_list_row(trans("COA Allowance:"), 'debit_code', null);
	
	percentage_amount_list_row(trans("Input Type:"), 'value', null, true);
	if (isset($_POST['value']) && $_POST['value'] == 'Formula' )
		text_row(trans("Custom Formula :"), 'formula', null, 100, 100);
	else
		hidden('formula', '');
	if((list_updated('value') && ( get_post('value')=='Percentage' || get_post('value')=='Gross Percentage' ) )|| $_POST['percentage'] >0 ){
		kv_text_row_ex(trans("Percentage from Basic:"), 'percentage', 10, null, null, null, null, '%');
	}else{
		hidden('percentage', null);
	}
	
	echo '<tr><td>'.trans("Debit Account:").'</td><td>'.gl_all_accounts_list('debit_code', null, false, false, trans("Select account"), false, false, false).'</td></tr>';
	echo '<tr><td>'.trans("Credit Account:").'</td><td>'.gl_all_accounts_list('credit_code', null, false, false, trans("Select account"), false, false, false).'</td></tr>';

	if(db_has_gross_pay()){ 
		kv_check_row(trans("Gross Pay:"), 'gross', $_POST['gross'], false, false, $disabled);
		hidden('gross', (($_POST['gross'] == 1 ) ? 1 : 0));
	} else {
		kv_check_row(trans("Gross Pay:"), 'gross', $_POST['gross']);
	}

	if(db_has_basic_pay()){ 
		kv_check_row(trans("Basic Pay:"), 'basic', $_POST['basic'], false, false, $disabled);
		hidden('basic', $_POST['basic']);
	} else{
		kv_check_row(trans("Basic Pay:"), 'basic', $_POST['basic']);
	}
	if($Mode == 'Edit'){
		earning_deductions_list_row(trans("Type:"), 'type', null, "", "", false, $disabled);
		hidden('type', $_POST['type']);
	}
	else
		earning_deductions_list_row(trans("Type:"), "type");
	if(isset($hrmsetup['tax_used']))
		kv_check_row(trans("Professional Tax:"), 'Tax', $_POST['Tax'], false, false);
	else
		hidden('Tax', 0);
	kv_check_row(trans("Loan Account:"), 'loan', $_POST['loan'], false, false);

	if($disabled){
		if(db_has_tax_pay())
			hidden('Tax', $_POST['Tax']);
		if(db_has_basic_pay()){ 
			hidden('basic', $_POST['basic']);
		}
		if($Mode == 'Edit')
			hidden('type', $_POST['type']);
	}
	if(isset($hrmsetup['enable_esic']))
		kv_check_row(trans("ESIC:"), 'esic', $_POST['esic'], false, false);
	else
		hidden('esic', 0);
	if(isset($hrmsetup['enable_pf']))
		kv_check_row(trans("PF:"), 'pf', $_POST['pf'], false, false);
	else
		hidden('pf', 0);
	kv_allowances_type_list_row(trans("Allowances Type :"),'al_type',null,false);
	
	kv_empl_number_list_row(trans("Sort Order"), 'sort_order', null, 1, $max_sort);
	kv_check_row(trans("Inactive:"), 'inactive', $_POST['inactive'], false, false);
	end_table(1);
	submit_add_or_update_center($selected_id == -1, '', 'both');
div_end();
end_form();
end_page(); ?> 
