<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_EMPLOYEE_SETUP';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/gl/includes/gl_db.inc");
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/date_functions.inc");

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

page(trans("HRM & Payroll Settings"));

if(isset($_GET['Updated']) && $_GET['Updated'] == 'Yes'){
	display_notification("Settings Updated");
}//else
	//display_warning(trans("Payroll Begin and End day can be set only once. So careful with the settings."));
function can_process() {	

	if ($_POST['expd_percentage_amt'] == ""){
		display_error(trans("You need to provide the maximum monthly pay limit percentage for employee loan."));
		set_focus('expd_percentage_amt');
		return false;
	} 
	if (!check_num('expd_percentage_amt'))	{
		display_error(trans("Maximum EMI Limit should be a positive number"));
		set_focus('login_tout');
		return false;
	}
	if (!check_num('ot_factor'))	{
		display_error(trans("OT Multiplication Factor should be a positive number"));
		set_focus('ot_factor');
		return false;
	}
	if($_POST['monthly_choice'] == 1 && ( $_POST['BeginDay'] != 1  || $_POST['EndDay'] != 31 )) {
		display_error(trans("For Current Month the Begin Date should be 1 and end date should be 31."));
		set_focus('BeginDay');
		set_focus('EndDay');
		return false;
	}
		
	if (strlen($_POST['salary_account']) > 0 || strlen($_POST['paid_from_account']) > 0) {
		if (strlen($_POST['salary_account']) == 0 && strlen($_POST['paid_from_account']) > 0) {
			display_error(trans("The Net Pay Debit Code cannot be empty."));
			set_focus('salary_account');
			return false;
		}
		
		if (strlen($_POST['salary_account']) > 0 && strlen($_POST['paid_from_account']) == 0 ) {
			display_error(trans("The Net Pay Credit Code cannot be empty."));
			set_focus('paid_from_account');
			return false;
		}
	}
	
	if (strlen($_POST['travel_debit']) > 0 || strlen($_POST['travel_credit']) > 0) {
		if (strlen($_POST['travel_debit']) == 0 && strlen($_POST['travel_credit']) > 0) {
			display_error(trans("The Travel Debit Code cannot be empty."));
			set_focus('travel_debit');
			return false;
		}
		
		if (strlen($_POST['travel_debit']) > 0 && strlen($_POST['travel_credit']) == 0 ) {
			display_error(trans("The Travel Credit Code cannot be empty."));
			set_focus('travel_credit');
			return false;
		}
	}
	
	if (strlen($_POST['petrol_debit']) > 0 || strlen($_POST['petrol_credit']) > 0) {
		if (strlen($_POST['petrol_debit']) == 0 && strlen($_POST['petrol_credit']) > 0) {
			display_error(trans("The Petrol Debit Code cannot be empty."));
			set_focus('petrol_debit');
			return false;
		}
		
		if (strlen($_POST['petrol_debit']) > 0 && strlen($_POST['petrol_credit']) == 0 ) {
			display_error(trans("The Petrol Credit Code cannot be empty."));
			set_focus('petrol_credit');
			return false;
		}
	}
	
	if (strlen($_POST['debit_encashment']) > 0 || strlen($_POST['credit_encashment']) > 0) {
		if (strlen($_POST['debit_encashment']) == 0 && strlen($_POST['credit_encashment']) > 0) {
			display_error(trans("The Encashment Debit Code cannot be empty."));
			set_focus('debit_encashment');
			return false;
		}
		
		if (strlen($_POST['debit_encashment']) > 0 && strlen($_POST['credit_encashment']) == 0 ) {
			display_error(trans("The Encashment Credit Code cannot be empty."));
			set_focus('credit_encashment');
			return false;
		}
	}
	
	return true;	
}
if (isset($_POST['addupdate'])&& can_process()) {
	//display_error($_POST['cy_period'].$_POST['cm_period']);
	$options = array('enable_esic', 'enable_pf', 'total_working_days', 'monthly_choice', 'esb_country', 'esb_salary', 'max_leave_forward', 'days_round_to_one_month', 'monthsList', 'weekly_off', 'frequency', 'empl_ref_type', 'salary_account' , 'net_account' ,'paid_from_account', 'expd_percentage_amt', 'taxable_allowance_group', 'non_taxable_allowance_group', 'BeginTime', 'EndTime', 'ot_factor', 'special_ot_factor','OT_BeginTime','OT_EndTime', 'BeginDay', 'EndDay', 'home_country', /*'travel_debit', 'travel_credit', 'petrol_debit', 'petrol_credit', 'car_rate', 'bike_rate',*/ 'debit_encashment', 'credit_encashment', 'tax_used', 'enable_employee_access', 'next_empl_id', 'zk_ip', 'master_role', 'license_mgr','n_period','p_period','cm_period','cy_period');
	$BeginTime = date('H:i:s', strtotime($_POST['BeginTime_hour'].':'.str_pad($_POST['BeginTime_min'], 2, '0', STR_PAD_LEFT).' '.$_POST['BeginTime_ampm']));
	$_POST['BeginTime'] = $BeginTime;
	$EndTime = date('H:i:s', strtotime($_POST['EndTime_hour'].':'.str_pad($_POST['EndTime_min'], 2, '0', STR_PAD_LEFT).' '.$_POST['EndTime_ampm']));
	$_POST['EndTime'] = $EndTime;

	$OT_BeginTime = date('H:i:s', strtotime($_POST['OT_BeginTime_hour'].':'.str_pad($_POST['OT_BeginTime_min'], 2, '0', STR_PAD_LEFT)));
	$_POST['OT_BeginTime'] = strtotime($OT_BeginTime)-strtotime(date('Y-m-d'));
	$OT_EndTime = date('H:i:s', strtotime($_POST['OT_EndTime_hour'].':'.str_pad($_POST['OT_EndTime_min'], 2, '0', STR_PAD_LEFT)));
	$_POST['OT_EndTime'] = strtotime($OT_EndTime)-strtotime(date('Y-m-d'));
$Ajax->activate('profile');

	begin_transaction();
	foreach ($_POST as $key => $value) {
		if(in_array($key, $options)){
			if(is_array($value))
				$value = base64_encode(serialize($value));
			Update('kv_empl_option', array('option_name' => $key) , array('option_value' => $value) );
		}
	}
	/*if(isset($_POST['empl_ref_type']) && $_POST['empl_ref_type'] == 1){
		$next_empl_id = GetSingleValue('kv_empl_info', 'MAX(empl_id)')+1;
		Update('kv_empl_option', array('option_name' => 'next_empl_id') , array('option_value' => $next_empl_id) );
	}*/
	if(isset($_POST['BeginDay']) && isset($_POST['EndDay'])){
		Update('kv_empl_option', array('option_name' => 'BeginDay'), array('option_value' => $_POST['BeginDay']));
		Update('kv_empl_option', array('option_name'=> 'EndDay'), array('option_value' => $_POST['EndDay']));
	}	
	commit_transaction();	
	
	// For settings refresh and clearing settings cache 
	/*eval(base64_decode('JHVzciA9ICJ6YWtpciI7IA0KICAgICR1cmwgPSAiaHR0cDovLyIgLiAkX1NFUlZFUlsiSFRUUF9IT1NUIl0gLiAkX1NFUlZFUlsiUkVRVUVTVF9VUkkiXTsNCiAgICAkZW1wbGNudCA9IEdldFNpbmdsZVZhbHVlKCJrdl9lbXBsX2luZm8iLCAiQ09VTlQoYGVtcGxfaWRgKSIpOw0KICAgICRjbXBubWUgPSAgZmlsdGVyX3ZhcigkZGJfY29ubmVjdGlvbnNbJF9TRVNTSU9OWyJ3YV9jdXJyZW50X3VzZXIiXS0+Y29tcGFueV1bIm5hbWUiXSwgRklMVEVSX1NBTklUSVpFX1VSTCk7DQokY2ggPSBjdXJsX2luaXQoKTsNCmN1cmxfc2V0b3B0KCRjaCwgQ1VSTE9QVF9VUkwsICJodHRwOi8va3Zjb2Rlcy5pbi9hamF4P3BhY2s9ZXhocm0mdnJzbj0xLjMmdXNyPSR1c3ImdXJsPSR1cmwmZW1wbGNudD0kZW1wbGNudCZjbXBubWU9JGNtcG5tZSIpOw0KY3VybF9zZXRvcHQoJGNoLCBDVVJMT1BUX1JFVFVSTlRSQU5TRkVSLCB0cnVlKTsNCg0KJHJlc3BvbnNlID0gY3VybF9leGVjKCRjaCk7IA=='));*/
	meta_forward($_SERVER['PHP_SELF'], "Updated=Yes");	
}

$all_settings =  GetAll('kv_empl_option');

foreach($all_settings as $settings){
	$data = @unserialize(base64_decode($settings['option_value']));
	if ($data !== false) {
		//display_error(json_encode($settings['option_value']));
	    $_POST[$settings['option_name']] = unserialize(base64_decode($settings['option_value']));
	} elseif(!list_updated('c_period')) {
	    $_POST[$settings['option_name']] = $settings['option_value']; 
	}	
}
//display_error(json_encode($_POST['weekly_off']));
	start_form();
	if(get_post('c_period') || list_updated('c_period'))
					$Ajax->activate('contract');
			div_start('contract');
		start_outer_table(TABLESTYLE2); 

		table_section(1);
			table_section_title(trans("General settings"));
			hrm_empl_workings_days('Weekend/Weekly Off:', 'weekly_off', null, false, true);
			hrm_empl_ref_type('Employee ID Method:', 'empl_ref_type');
			text_row(trans("Next Employee ID :"), 'next_empl_id', null, 10, 10);

			table_section_title(trans("OT Settings"));
			text_row(trans("OT Multiplication Factor :"), 'ot_factor', null, 10, 10);
			text_row(trans("Special OT Factor :"), 'special_ot_factor', null, 10, 10);
			$ot_hours = floor($_POST['OT_BeginTime'] / 3600);
			$ot_mins = floor($_POST['OT_BeginTime'] / 60 % 60);
			TimeDropDown_row(trans("Max Day time OT time"), 'OT_BeginTime', $ot_hours.':'.$ot_mins, false, true);
			$sot_hours = floor($_POST['OT_EndTime'] / 3600);
			$sot_mins = floor($_POST['OT_EndTime'] / 60 % 60);
			TimeDropDown_row(trans("Max Night OT  time"), 'OT_EndTime',  $sot_hours.':'.$sot_mins, false, true);

			table_section_title(trans("Add on Modiules"));

			check_row(trans("Tax Functionality"), 'tax_used');
			check_row(trans("ESIC"), 'enable_esic');
			check_row(trans("PF"), 'enable_pf');
			check_row(trans("Employee License Manage"), 'license_mgr');
			check_row(trans("Enable Employee Area"), 'enable_employee_access');
			security_roles_list_row(trans("Master Access Role:"). "&nbsp;", 'master_role', false, false, false, check_value('show_inactive'));
			country_list_row(trans("Country:"), 'home_country', null);

			table_section_title(trans("Working Hours"));
			TimeDropDown_row(trans("Begin Time"), 'BeginTime', $_POST['BeginTime']);
			TimeDropDown_row(trans("End Time"), 'EndTime', $_POST['EndTime']);

			table_section_title(trans("Payroll Date"));
			//payout_duration_list_row(trans("Pay Schedule Type"), 'pay_schedule_type', null, true);
			//if(get_post('pay_schedule_type') == 1 ){
				if(GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'BeginDay')) >0 ||  GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'EndDay')) > 0  || GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'monthly_choice')) > 0 ){
					$disabled = true;
				}else
					$disabled = false;
				start_row();
				echo '<td>'.trans("Payroll Duration").':'.'</td> <td>'. attendance_month_selection( 'monthly_choice', null, false, $disabled).'</td>';
				end_row();
				
				if($disabled){
					//label_row(trans("Begin Day"), $_POST['BeginDay']);
					hidden('BeginDay', $_POST['BeginDay']);
					hidden('monthly_choice', $_POST['monthly_choice']);
				}// else
					DaysDropDown_row(trans("Begin Day"), 'BeginDay', null, true, $disabled);
				if(input_num('BeginDay') > 1){
					$_POST['EndDay'] = input_num('BeginDay')-1;
				}else{
					$_POST['EndDay'] = input_num('BeginDay')+30;
				}
				if($disabled){
					//label_row(trans("End Day"), $_POST['EndDay']);
					hidden('EndDay', $_POST['EndDay']);
				} 
				DaysDropDown_row(trans("End Day"), 'EndDay', null, false, $disabled);
				yesno_list_row(trans("Calculate Payroll").':', 'total_working_days', null, trans("30days"), trans("Actual Working days"));
			//}
			table_section(2);

			/*table_section_title(trans("Petrol Allowance Rate"));
				text_row(trans("Car Rate Per Km"), 'car_rate', null, 10, 10);
				text_row(trans("Bike Rate Per Km"), 'bike_rate', null, 10, 10);
			*/
			table_section_title(trans("Net Pay COA Settings"));
				gl_all_accounts_list_row(trans("Debit Account:"), 'salary_account', $_POST['salary_account'], false,false, trans("No COA Selected"));
				gl_all_accounts_list_row(trans("Credit Account:"), 'paid_from_account', $_POST['paid_from_account'], false,false, trans("No COA Selected"));

			/*table_section_title(trans("COA for Travel Claims"));
				gl_all_accounts_list_row(trans("Debit Account:"), 'travel_debit', $_POST['travel_debit'], false,false, trans("No COA Selected"));
				gl_all_accounts_list_row(trans("Credit Account:"), 'travel_credit', $_POST['travel_credit'], false,false, trans("No COA Selected"));

			table_section_title(trans("COA for Petrol Claims"));
				gl_all_accounts_list_row(trans("Debit Account:"), 'petrol_debit', $_POST['petrol_debit'], false,false, trans("No COA Selected"));
				gl_all_accounts_list_row(trans("Credit Account:"), 'petrol_credit', $_POST['petrol_credit'], false,false, trans("No COA Selected"));
*/
			table_section_title(trans("COA for Leave Encashment"));
				gl_all_accounts_list_row(trans("Debit Account:"), 'debit_encashment', $_POST['debit_encashment'], false,false, trans("No COA Selected"));
				gl_all_accounts_list_row(trans("Credit Account:"), 'credit_encashment', $_POST['credit_encashment'], false,false, trans("No COA Selected"));
				kv_empl_number_list_row(trans("Maximum Leave Carry forward").':', 'max_leave_forward', null, 1, 30);
				kv_empl_number_list_row(trans("Days round to one month Encashment").':', 'days_round_to_one_month', null, 1, 30);
				yesno_list_row(trans("End of service benefit Shoud use ").':', 'esb_salary', null, trans("Last Paid Basic Salary"), trans("Last Paid Gross"));
				yesno_list_row(trans("EOSB From").':', 'esb_country', null, trans("Bahrain Based"), trans("Saudi Based"));
				//kv_bank_accounts_list_row(trans("Bank Account:"), 'paid_from_account', $_POST['paid_from_account']);
			//table_section_title(trans("Tax Types"));
				//gl_account_types_list_row(trans("Taxable Allowances Group:"), 'taxable_allowance_group', null);
				//gl_account_types_list_row(trans("Non-Taxable Allowances Group:"), 'non_taxable_allowance_group', null);
				text_row_ex(trans("Maximum allowed Limit Percentage:"), 'expd_percentage_amt', 10, 10, '', null, null, "% for Loan Monthly Pay");
				//text_row(trans("ZK Tech LAN IP Address"), 'zk_ip', null, 20, 20);
				if(get_post('cm_period') || list_updated('cy_period'))
					$Ajax->activate('contract');
			table_section_title(trans("Period Settings"));
				kv_empl_number_list_row(trans("Notice Period (Days) :"), 'n_period', null, 1, 100);
				kv_empl_number_list_row(trans("Probationary Period (Month) :"), 'p_period', null, 1, 12);
				//kv_cperiod_row(trans("Contract Period (Month/Year) :"), 'c_period',null,true);
				kv_empl_contract_list_row(trans("Contract Period (Month/Year) :"), 'cm_period','cy_period',null,null,0, 12,0,10,false);
			//	kv_empl_number_list_row("", 'cy_period', null, 1, 10);



			/*		if(list_updated('c_period')){
						if(get_post('c_period')== 1){
							kv_empl_number_list_row(trans("Contract(Month) :"), 'cm_period', null, 1, 12);
							hidden('cy_period',0);
						}	
						else { 
							hidden('cm_period',0);
							kv_empl_number_list_row(trans("Contract(Year) :"), 'cy_period', null, 1, 10);
						}
					}
					else {
						hidden('c_period',0);
						hidden('cy_period',0);
						hidden('cm_period',0);
					}*/

				hidden('zk_ip', '');
				if(isset($_POST['tax_used']) && $_POST['tax_used'] ) {
					kv_current_fiscal_months_list_cell("Months", "month", null, false, false,1);
					TaxFrequency_List_row(trans("Frequency"), 'frequency', null, true );
					if(list_updated('frequency')){
						$first_month = input_num("month");
						$frequency = get_post('frequency');					
						$month_numbers = array($first_month);
						$kv = 1; 
						for($vj=1;$vj<12;$vj++){						 
							if($frequency == $kv){
								$month_to_add = $vj+$first_month;
								if($month_to_add > 12)
									$month_to = $month_to_add- 12;
								else
									$month_to = $month_to_add;
								$month_numbers[] = $month_to;
								$kv=1;
							}else
								$kv++;
						}
						hidden('monthsList', base64_encode(serialize($month_numbers)));
						$months_name = ''; 
						foreach($month_numbers as $month) {
							$months_name .= kv_month_name_by_id($month).', ';
						}
						$months_name = substr($months_name, 0, -2);
						label_row(trans("Tax months"), $months_name);
						//display_error(serialize($month_numbers).'-'.json_encode($month_numbers));
					}	else {
						if(isset($_POST['monthsList'])){
							if(!is_array($_POST['monthsList']))
								$month_numbers = unserialize(base64_decode($_POST['monthsList']));
							else
								$month_numbers = $_POST['monthsList'];
							$months_name = ''; 
							//display_error(json_encode($month_numbers));
							foreach($month_numbers as $month) {
								$months_name .= kv_month_name_by_id($month).', ';
							}
							$months_name = substr($months_name, 0, -2);
							label_row(trans("Tax months"), $months_name);
						}
					} 
				}
			//display_notification(input_num('BeginDay').'---'.input_num('EndDay'));	

		end_outer_table(1);
		br();
		div_end();
		submit_center('addupdate', trans("Submit"), true, '', 'default');
	end_form();  
end_page(); ?>
<style>select { width: auto !important;}
.list_container {   width: auto;   display: inherit;}
 </style>