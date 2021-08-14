<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_LEAVE_ENCASHMENT';
$path_to_root="../..";
include_once($path_to_root . "/includes/session.inc");
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
 
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");

page(trans("leave Encashment"));

check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));
 
check_db_has_salary_account(trans("There are no Salary Account defined in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/hrm_settings.php'>Settings</a> to update it."));

if(user_theme() == 'Saaisaran'){ ?>
<style>
	@media (min-width: 900px){
		table {  width: auto !important; } 
	}
</style>

<?php }
if(isset($_GET['Added'])){
	display_notification(' The Employee leave encashment processed #' .$_GET['Added']);
}

if(isset($_GET['Carry'])){
	display_notification(' The Employee leave encashment carry forwarded to next year with reference ID#' .$_GET['Carry']);
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
$current_year =  get_current_fiscalyear();
$year = get_post('year',$current_year['id']);

if(list_updated('month') || get_post('RefreshInquiry') || get_post('employee_id')) {
	$month = get_post('month');   
	$Ajax->activate('totals_tbl');
}	
$dim = get_company_pref('use_dimension');
	
div_start('totals_tbl');
start_form();
	if (db_has_employees()) {
		start_table(TABLESTYLE_NOBORDER);
		start_row();
		kv_fiscalyears_list_cells(trans("Fiscal Year:"), 'year', null, true);
		kv_current_fiscal_months_list_cell(trans("Months"), "month", null, true, false,1);
		//employee_list_cells(trans("Select an Employee"). " :", 'employee_id', null,	trans("Select Employee"), true, check_value('show_inactive'));
		if ($dim >= 1){
			dimensions_list_cells(trans("Dimension")." 1", 'dimension_id', null, true, " ", false, 1);
			if ($dim > 1)
				dimensions_list_cells(trans("Dimension")." 2", 'dimension2_id', null, true, " ", false, 2);
		}
		if ($dim < 1)
			hidden('dimension_id', 0);
		if ($dim < 2)
			hidden('dimension2_id', 0);
		end_row();
		start_row();
		department_list_cells(trans("Select a Department")." :", 'dept_id', null, trans("No Department"), true, check_value('show_inactive'));
		employee_list_cells(trans("Select an Employee"). " :", 'employee_id', null,	trans("Select an Employee"), true, check_value('show_inactive'), false, false,true);
		end_row();
		end_table();
		br();
		if (get_post('_show_inactive_update')) {
			$Ajax->activate('employee_id');
			$Ajax->activate('month');
			$Ajax->activate('year');
			$Ajax->activate('totals_tbl');
			set_focus('employee_id');
		}
	} 
	else {	
		hidden('employee_id');
		hidden('month');
		hidden('year');
	}

	$_POST['EmplName']=$_POST['desig']=''; 
	$_POST['ear_tot']=$_POST['empl_dept']=$_POST['employee_id']= 0;

	$Empl_grade =GetSingleValue('kv_empl_job', 'grade', array('empl_id' => $employee_id));	
	$kv_empl_allowances  =  kv_get_allowances('Earnings', 0, $Empl_grade);
	//$kv_empl_allowances = array();
	
	foreach($kv_empl_allowances as $single) {
		$_POST[$single['id']]=0;	
		if(!isset($_POST['c_'.$single['id']]))
			$_POST['c_'.$single['id']] = 0;			
	}
	hidden('date', Today());
 	$completed_years=GetSingleValue('kv_empl_leave_encashment','MIN(`year`)',array('empl_id' => $employee_id ));
	if($completed_years == false || $completed_years <= $year)
		$allow= true;
	else{
		$allow = false;
		display_warning(trans("Sorry , you can't input to this year"));
	}	
 	if(isset($employee_id)  && $employee_id > 0 && $allow ){
 		$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
 		
	 	$joining_date = get_employee_join_date($employee_id);
	 	$month_name = kv_month_name_by_id((int)get_post('month'));
		$end_of_selected_month = end_month(sql2date($months_with_years_list[(int)get_post('month')])); 

		//$get_status_and_date =  GetRow('kv_empl_job', array('empl_id' => $employee_id));
		$get_empl_status =  GetRow('kv_empl_info',  array('empl_id' => $employee_id));
			if(isset($employee_id) && $employee_id != '' && date2sql($end_of_selected_month) >= $joining_date && ($get_empl_status['status'] == 1 || ($get_empl_status['status']>1 && $get_empl_status['date_of_status_change'] >= date2sql($end_of_selected_month) ) )) {
				$sal_row = get_empl_encashment_details($employee_id, $month, $year);  
				$name_and_dept = get_empl_name_dept($employee_id); 
				$_POST['empl_dept']=GetSingleValue('kv_empl_departments', 'description', array('id' => $name_and_dept ['deptment']));

				$_POST['desig'] = kv_get_empl_desig($employee_id);
				$_POST['EmplName']=$name_and_dept ['name'];
				$allowance_var_ar = array();
				//display_error(json_encode($sal_row));
				if($sal_row){
					
					if(isset($sal_row['allowances']) && $sal_row['allowances'] != ''){
						//display_error($sal_row['allowances']);
						$allowances = unserialize(base64_decode($sal_row['allowances']));
						unset($sal_row['allowances']);
						foreach($allowances as $key => $value){
							$_POST[$key] = $value;
							$_POST['c_'.$key] = 1;
						}
					}else { 			
						foreach($kv_empl_allowances as $single) {					
							$_POST[$single['id']] = ((isset($sal_row[$single['id']]) && $sal_row[$single['id']] > 0 ) ? $sal_row[$single['id']] : (isset($_POST[$single['id']]) ? $_POST[$single['id']] : 0 ));					
						}
					
						foreach($kv_empl_allowances as $single) {
							$allowance_var_ar[$single['id']] = '{$'.strtolower($single['description']).'}';							
							if((isset($sal_row[$single['id']]) && $sal_row[$single['id']] > 0))	
								$_POST[$single['id']] = ((isset($sal_row[$single['id']]) && $sal_row[$single['id']] > 0 ) ? $sal_row[$single['id']] : (isset($_POST[$single['id']]) ? $_POST[$single['id']] : 0 ));
						}		
					}
					$_POST['employee_id'] = $employee_id;
				}			
			
			$_POST['ear_tot'] = 0;

			start_table(TABLESTYLE);		
			table_section_title(trans("Details"));
			label_row(trans("Employee No:"), $_POST['employee_id']);
			label_row(trans("Employee Name:"), $_POST['EmplName']);
			label_row(trans("Department:"), $_POST['empl_dept']);
			hidden('department', $name_and_dept ['deptment']);
			label_row(trans("Designation:"), GetSingleValue('kv_empl_designation', 'description', array('id' => $_POST['desig'])));
			if(!isset($sal_row['date']))
				date_row(trans("Date of Payment:"), Today()/*date("d-F-Y")*/);
			else
				label_row(trans("Date of Payment:"), date("d-F-Y", strtotime($sal_row['date'])));
			if(!isset($sal_row['payable_days'])){
				$available_leaves = AvailableLeaveDays($_POST['employee_id'], $year);
				$payable_days = $available_leaves['al'];
			}else
				$payable_days = $sal_row['payable_days'];
			if(isset($available_leaves['id']))
				hidden('carry_last_year_id', $available_leaves['id']);
			label_row(trans("Payable Days"), $payable_days);
			hidden('payable_days', $payable_days);
			table_section_title(trans("Earnings"));
			foreach($kv_empl_allowances as $single) {				
				label_row(checkbox(null, 'c_'.$single['id'], $_POST['c_'.$single['id']]). trans($single['description']), price_format($_POST[$single['id']]), '', ' style="text-align:right" ');
				if((isset($_POST['c_'.$single['id']]) && $_POST['c_'.$single['id']] == 1) ){	
					$_POST['ear_tot'] += $_POST[$single['id']];
					hidden('a_'.$single['id'], $_POST[$single['id']]);
				}				
			}
			if(!isset($sal_row['amount'])){
				label_row(trans("Calculated Gross Salary:"), price_format($_POST['ear_tot']), 'style="color:#FF9800; background-color:#f9f2bb;"', 'style="color:#FF9800; background-color:#f9f2bb;text-align:right;"');
				echo '<tr> <td colspan="2" style="padding: 5px 0; "><center>'.submit('RefreshInquiry', trans("Calculate"),false, false, 'default').'</center></td></tr>';
				$_POST['amount'] = ((isset($_POST['amount']) && $_POST['amount'] != 0 ) ? $_POST['amount'] : price_format($payable_days *(($_POST['ear_tot']*12)/365)));
				//hidden('amount', round($payable_encashment, 2));				
				amount_row(trans("Net Encashment :"), 'amount', null);
			} else{
				$payable_encashment = $sal_row['amount'];			
				label_row(trans("Net Encashment :"), price_format($payable_encashment), 'style="color:#107B0F; background-color:#B7DBC1;"', 'style="color:#107B0F; background-color:#B7DBC1;text-align:right;"');
			}
			end_table();		
			
			if(db_has_employee_leave_encashment($employee_id, $month, $year) == false && $employee_id != null){
				br(2);
		
				submit_center_first('pay_out', trans("Process Payout"), true, trans("Payout to Employees"), 'default');
				submit_center_last('carry_forward', trans("Carry Forward"), true, trans("Carry Forward"), 'default');
				br();
				end_form();
			}
		}else{
			if($months_with_years_list[(int)get_post('month')] < $joining_date)
				display_warning(trans("You can't Pay before his Joining Date!"));
		}
			if(db_has_employee_leave_encashment($employee_id, $month, $year) == true && $employee_id != null){
				br(2);
				
				if(!isset($sal_row['id']))
					$sal_row['id'] = 0;
				$result = get_gl_trans(97, $sal_row['id']);

				if (db_num_rows($result) == 0){
					echo "<p><center>".trans("No general ledger transactions have been created for")."</center></p><br>";
					end_page(true);
					exit;
				}

				/*show a table of the transactions returned by the sql */
				$dim = get_company_pref('use_dimension');

				if ($dim == 2)
					$th = array(trans("Account Code"), trans("Account Name"), trans("Dimension")." 1", trans("Dimension")." 2",		trans("Debit"), trans("Credit"), trans("Memo"));
				else if ($dim == 1)
					$th = array(trans("Account Code"), trans("Account Name"), trans("Dimension"),trans("Debit"), trans("Credit"), trans("Memo"));
				else		
					$th = array(trans("Account Code"), trans("Account Name"),trans("Debit"), trans("Credit"), trans("Memo"));
				$k = 0; //row colour counter
				$heading_shown = false;

				$credit = $debit = 0;
				while ($myrow = db_fetch($result)) 	{
					if ($myrow['amount'] == 0) continue;
					if (!$heading_shown){
						//display_gl_heading($myrow);
						start_table(TABLESTYLE, "width='95%'");
						table_header($th);
						$heading_shown = true;
					}	

					alt_table_row_color($k);
					
					label_cell($myrow['account']);
					label_cell($myrow['account_name']);
					if ($dim >= 1)
						label_cell(get_dimension_string($myrow['dimension_id'], true));
					if ($dim > 1)
						label_cell(get_dimension_string($myrow['dimension2_id'], true));

					display_debit_or_credit_cells($myrow['amount']);
					label_cell($myrow['memo_']);
					end_row();
					if ($myrow['amount'] > 0 ) 
						$debit += $myrow['amount'];
					else 
						$credit += $myrow['amount'];
				}
				if ($heading_shown){
					start_row("class='inquirybg' style='font-weight:bold'");
					label_cell(trans("Total"), "colspan=2");
					if ($dim >= 1)
						label_cell('');
					if ($dim > 1)
						label_cell('');
					amount_cell($debit);
					amount_cell(-$credit);
					label_cell('');
					end_row();
					end_table(1);
				}
				echo '<center> <a href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep802.php?PARAM_0='.$year.'&PARAM_1='.$month.'&PARAM_2='.$employee_id.'&rep_v=yes" target="_blank" class="printlink"> Print </a> </center>'; 
				br();
			}
		}
	div_end();

	if(get_post('pay_out')) {		
		$payout =  array('empl_id' => $_POST['employee_id'],
							 'month' => $_POST['month'],
							 'year' => $_POST['year'],
							 'department' => $_POST['department'],
							 'amount' => input_num('amount'),
							 'date' => array($_POST['date'], 'date'), 
							 'payable_days' => $_POST['payable_days']);
		$Allowance = kv_get_allowances('Earnings');
		$allowances_ar = array();
		foreach($Allowance as $single) {	 
			if((isset($_POST['c_'.$single['id']]) && $_POST['c_'.$single['id']] == 1) && isset($_POST[$single['id']]) )
				$allowances_ar[$single['id']] = $_POST[$single['id']];					
		}
		$payout['allowances'] = base64_encode(serialize($allowances_ar));
		$pay_slip_id = Insert('kv_empl_leave_encashment', $payout);
				
		$debit_encashment= GetSingleValue('kv_empl_option', 'option_value', array('option_name'=>'debit_encashment'));
		$credit_encashment= GetSingleValue('kv_empl_option', 'option_value', array('option_name'=>'credit_encashment'));

		if($debit_encashment)
			add_gl_trans(97, $pay_slip_id, $_POST['date'], $debit_encashment, $_POST['dimension_id'],$_POST['dimension2_id'], 'Employee Leave Encashment #'.$_POST['employee_id'].'-'. kv_get_empl_name($_POST['employee_id']), $_POST['amount']);
		if($credit_encashment)
			add_gl_trans(97, $pay_slip_id, $_POST['date'], $credit_encashment, $_POST['dimension_id'],$_POST['dimension2_id'], 'Employee Leave Encashment #'.$_POST['employee_id'].'-'. kv_get_empl_name($_POST['employee_id']), -$_POST['amount']);
		add_audit_trail(97, $pay_slip_id, $_POST['date']);
		if(isset($_POST['carry_last_year_id']))
			Update('kv_empl_leave_encashment', array('id' => $_POST['carry_last_year_id']), array('inactive' => 1));
		meta_forward($_SERVER['PHP_SELF'], "Added=$pay_slip_id&employee_id=".$_POST['employee_id'].'&month='.$_POST['month'].'&year='.$_POST['year']);
	}	elseif(get_post('carry_forward')){
		
		$max_leave_forward= GetSingleValue('kv_empl_option', 'option_value', array('option_name'=>'max_leave_forward'));
		if($max_leave_forward < $_POST['payable_days'])
			$_POST['payable_days'] = $max_leave_forward;
		$payout =  array('empl_id' => $_POST['employee_id'],
							 'month' => $_POST['month'],
							 'year' => $_POST['year'],
							 'amount' => input_num('amount'),
							 'carry_forward' => 1,
							 'date' => array($_POST['date'], 'date'), 
							 'payable_days' => $_POST['payable_days']);
		$Allowance = kv_get_allowances('Earnings');
		$allowances_ar = array();
		foreach($Allowance as $single) {	
			if((isset($_POST['c_'.$single['id']]) && $_POST['c_'.$single['id']] == 1) && isset($_POST[$single['id']]) )
				$allowances_ar[$single['id']] = $_POST[$single['id']];					
		}
		$payout['allowances'] = base64_encode(serialize($allowances_ar));
		$pay_slip_id = Insert('kv_empl_leave_encashment', $payout);
				
		meta_forward($_SERVER['PHP_SELF'], "Carry=$pay_slip_id&employee_id=".$_POST['employee_id'].'&month='.$_POST['month'].'&year='.$_POST['year']);
	}

end_page(); ?>
<style>
table.tablestyle td {     padding: 5px 15px 5px 15px;}
</style>