<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HRM_EMPLOYEE_MANAGE';
$path_to_root = "../../..";

include_once($path_to_root . "/includes/session.inc");
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
if (isset($_GET['vw'])){
	$view_id = $_GET['vw'];
	$empl_row = GetRow('kv_empl_job', array('empl_id' => $view_id));
    	header("Content-type: application/octet-stream");
    	//header('Content-Length: '.$row['filesize']);
 		header('Content-Disposition: attachment; filename='.$empl_row['bond_doc']);
 		//display_error( company_path(). "/attachments/empldocs/".$row['unique_name']);
	   	echo file_get_contents(company_path(). "/attachments/empldocs/".$empl_row['empl_id']."/".$empl_row['bond_doc']);
    	exit();
	//}
		
}



page(trans($help_context = "Employee Job informations"));

//---------------------------------------------------------------------------------------------------
function can_process(){ 
	
	/*if(date2sql($_POST['joining']) > date('Y-m-d')){
		display_error(trans("Invalid Joining Date for the Employee1."));
		set_focus('joining');
		return false;
	}*/
	return true; 
}
if (isset($_POST['UPDATE_ITEM']) && can_process()) {
//display_error($_POST['weekly_off']);

  /*  if ($_POST['basic_amt']=='' || $_POST['basic_amt']=='0') {
        display_error(trans("The employee basic salary can't be empty."));
        return false;
    }*/


   /* $total_salary= $_POST['empl_basic_salary']+$_POST['empl_house_rent']+$_POST['empl_meal_allowance']+$_POST['empl_perfo_allowance']
                   +$_POST['empl_other_allowance'];

    if($total_salary!=$_POST['empl_monthly_salary'])
    {
        display_error(trans("The sum of basic salary and other benefits not matching with monthly salary."));
        return false;
    }*/

	/*display_error(date('Y-m-d',strtotime($_POST['joining'])).'------');


die;*/
    begin_transaction();
		$jobs_arr =  array( //'grade' => $_POST['grade'],
							 'department' => $_POST['department'],
							 'desig_group' => $_POST['desig_group'],
							 'desig' => $_POST['desig'],
							 'shift' => $_POST['shift'],
							 'currency' => $_POST['currency'],
							 'nationality' => $_POST['nationality'],
							 'medi_category' => $_POST['medi_category'],
							 'family' => $_POST['family'],
							 'weekly_off' => base64_encode(serialize($_POST['weekly_off'])),
							 'joining' => date('Y-m-d',strtotime(str_replace("/","-",$_POST['joining']))),
							 'bond_period' => date2sql($_POST['bond_period']), 
							 'empl_type' =>  $_POST['empl_type'],
							 'empl_contract_type' =>  $_POST['empl_contract_type'],
							 'working_branch' =>  $_POST['working_place'],
						 	 'mod_of_pay' => $_POST['mod_of_pay'],
						 	 'expd_percentage_amt' => $_POST['expd_percentage_amt'],
							 'bank_name' => $_POST['bank_name'],
							 'nominee_address' => $_POST['nominee_address'],
							 'nominee_name' => $_POST['nominee_name'],
							 'nominee_phone' => $_POST['nominee_phone'],
							 'nominee_email' => $_POST['nominee_email'], 
							 'aadhar' => $_POST['aadhar'], 
							 'bloog_group' => $_POST['bloog_group'], 
							 'ifsc' => $_POST['ifsc'], 
							 'ESIC' => $_POST['ESIC'],
						     //'PAN' => $_POST['PAN'], 							 
							 'branch_detail' => $_POST['branch_detail'],
							 'acc_no' => $_POST['acc_no'],
			                 'iban' => $_POST['iban'],
							 'work_hours' => $_POST['work_hours'],
			                 'calculate_commission'=>$_POST['check_commission'],
			                 'calculate_pf'=>$_POST['check_pf'],
			                 'head_of_dept'=>$_POST['head_of_dept'],
						     'payable_empl_subledger'=>$_POST['axispro_subledger_code'],
							 'esb_empl_subledger'=>$_POST['esb_sub_ledger'],
					         'cost_center'=>$_POST['cost_center'],
			                 'card_no_salary'=>$_POST['card_no_salary'],
							 'dept_supervisor'=>$_POST['dept_supervisor'],

                             );
		/*$Allowance = get_allowances(null, null, null, null, 0, $_POST['grade']);
		$gross_Earnings = 0;
		while ($single = db_fetch($Allowance)) {
			if(isset($_POST[$single['id']])){	
				$jobs_arr[$single['id']] = $_POST[$single['id']];
				
				if($single['type'] == 'Earnings')
					$gross_Earnings += $_POST[$single['id']];
			}
		}

		if(isset($_POST['gross']) && $_POST['gross'] > 0 )
				$jobs_arr['gross'] = $_POST['gross'];
			else
				$jobs_arr['gross'] = $gross_Earnings;
		$jobs_arr['gross_pay_annum'] = $gross_Earnings*12;
		*/
		if(trim($_POST['desig']) != trim($_POST['prev_desig'])){

			$jobs_arr['date_of_desig_change']=array(Today(),'date');
			$Yesterday = add_days(Today(), -1);
			$expe_arr=array('empl_id'=> $_POST['empl_id'],
							'company_name' => $SysPrefs->prefs['coy_name'],
							'company_location'=> GetSingleValue('workcentres', 'name', array('id' => $_POST['working_place'])),
							'department' => GetSingleValue('kv_empl_departments', 'description', array('id'=> $_POST['department'])),
							'designation' => GetSingleValue('kv_empl_designation', 'description', array('id'=> $_POST['desig'])),
							's_date' => array($_POST['prev_desig_date'],'date'),
							'e_date' => array($Yesterday,'date')
						);
			Insert('kv_empl_experience', $expe_arr);
		}
	 //display_error(json_encode($jobs_arr));

	//	die;
	if(!db_employee_has_job($_POST['empl_id'])) { 		
			$jobs_arr['empl_id'] = $_POST['empl_id']; 
			Insert('kv_empl_job', $jobs_arr);
			set_focus('empl_id');
			$Ajax->activate('empl_id'); // in case of status change
			display_notification(trans("A new Employee Job has been added."));
	} else { 
			$job = GetRow('kv_empl_job', array('empl_id' => $_POST['empl_id']));		
			Update('kv_empl_job', array('empl_id' => $_POST['empl_id']), $jobs_arr);
			set_focus('empl_id'); 
			$Ajax->activate('empl_id');
			//$jobs_arr["date_of_birth"]=date2sql($_POST['date_of_birth']);
			/*//$result=array_diff($jobs_arr,$job);
			//display_error(json_encode($job));
			$last_change=Today();
			foreach ($result as $key => $value) {
				Insert('kv_empl_history', array('empl_id' => $_POST['empl_id'], 'option_name' => $key, 'option_value' => $value,'last_change' =>array($last_change,'date')));
			}*/
			//display_err // in case of status change
			display_notification(trans("Employee Job Information has been updated."));
	}



	/************************************SAVE EMP- ACCOUNTS**************************/
	if($_POST['empl_payroll_accounts']=='1')
	{
		$chk=check_employee_accounts_exists($_POST['empl_id']);


		foreach($_POST as $p=>$val) {

			if(substr($p, 0, 4) == 'acc_') {
				$basic_or_not = get_element_basic_type(substr($p, 4));

				$pay_elements[] = array(
					'emp_id' =>  $_POST['empl_id'],
					'element_id' => substr($p, 4),
					'sub_account_code' => input_num($p)
				);
			}
		}


		    save_employee_mapping_accounts($pay_elements);



			/*save_employee_mapping_accounts($_POST['payroll_account_code'],$_POST['axispro_subledger_code'],$_POST['advance_account_code'],
				$_POST['advance_subledger_account_code'],$_POST['loan_account_code'],$_POST['loan_subledger_account_code'],0,$_POST['empl_id']
				,$_POST['base_salary_account_code'],$_POST['base_salary_sub_account_code'],$_POST['pf_account_code']*/

	}
	/******************************************END***********************************/

    $pay_elements = array();
   /* $pay_elements[] = array(
        'emp_id' =>  $_POST['empl_id'],
        'pay_rule_id' => 0,
        'pay_amount' => input_num('basic_amt'),
        'type' => DEBIT,
        'is_basic' => 1
    );*/

//dd($_POST);
    foreach($_POST as $p=>$val) {

        if(substr($p, 0, 4) == 'amt_') {
            $basic_or_not = get_element_basic_type(substr($p, 4));

            $pay_elements[] = array(
                'emp_id' =>  $_POST['empl_id'],
                'pay_rule_id' => substr($p, 4),
                'pay_amount' => input_num($p),
                'type' => input_num($p) > 0 ? DEBIT : CREDIT,
                'is_basic' => $basic_or_not[0]
            );
        }
    }

    //dd($pay_elements);
    save_personal_salary($pay_elements);


    $leave_elements = array();
    foreach($_POST as $p=>$val) {
        if(substr($p, 0, 6) == 'leave_') {
            if(input_num($p)=='0'|| input_num($p)>'0')
            {
                $leave_elements[] = array(
                    'emp_id' => $_POST['empl_id'],
                    'leave_id' => substr($p, 6),
                    'days' => input_num($p)
                );
            }
        }
    }

    save_employee_leave($leave_elements);

	UploadHandle($_POST['empl_id']);
	commit_transaction();

}

if (isset($_POST['RESET'])){
	$empl_id = -1;
}

if (isset($_GET['empl_id'])){
	$empl_id = $_POST['empl_id'] =$_POST['empl_id'] = $_GET['empl_id'];
}

//------------------------------------------------------------------------------------------------------
if (get_post('UPDATE_ITEM') != 'Update' && (list_updated('empl_id') || list_updated('grade') || get_post('RefreshInquiry') || get_post('gross') ) ) {
	$_POST['gross'] = get_post('gross');
	$allow_calculate = true; 
	$Ajax->activate('job_details');
}else
	$allow_calculate = false;
	hidden('_tabs_sel', get_post('_tabs_sel'));
	hidden('popup', @$_GET['popup']);

$action = $_SERVER['PHP_SELF'];
if ($page_nested)
	$action .= "?empl_id=".get_post('empl_id');
start_form(false, false, $action);

div_start('job_details');	
//---------------------------------------------------------------------------------------
$job_details = get_employee_job($_POST['empl_id']);
$get_employee_id=get_employee_id($_POST['empl_id']);


	
	//$_POST['empl_id'] = $get_employee_id[0];
	if(!isset($_POST['grade']))
		$_POST['grade'] = $job_details['grade'];
//$_POST['department'] = $job_details['department'];
	//$_POST['desig_group'] = $job_details['desig_group'];
	//$_POST['desig'] = $job_details['desig'];
	$_POST['nationality'] = $job_details['nationality'];	
	$_POST['medi_category'] = $job_details['medi_category'];	
	$_POST['family'] = $job_details['family'];	
	$_POST['weekly_off'] = unserialize(base64_decode($job_details['weekly_off']));	
	$_POST['shift'] = $job_details['shift'];	
	$_POST['currency'] = $job_details['currency'];	
	$_POST['date_of_desig_change'] = sql2date($job_details['date_of_desig_change']);
	$_POST['joining'] = sql2date($job_details['joining']);
	$_POST['bond_period'] = sql2date($job_details['bond_period']);
	if(!list_updated('empl_type'))
	$_POST['empl_type'] = $job_details['empl_type'];
	$_POST['empl_contract_type'] = $job_details['empl_contract_type'];
	$_POST['nominee_address'] = $job_details['nominee_address'];	
	$_POST['nominee_name'] = $job_details['nominee_name'];
	$_POST['nominee_phone'] = $job_details['nominee_phone'];
	$_POST['nominee_email'] = $job_details['nominee_email'];
	$_POST['aadhar'] = $job_details['aadhar'];
	$_POST['ifsc'] = $job_details['ifsc'];
	$_POST['ESIC'] = $job_details['ESIC'];
	$_POST['PF'] = $job_details['PF'];
	$_POST['expd_percentage_amt'] = $job_details['expd_percentage_amt'];
	$_POST['bloog_group'] = $job_details['bloog_group'];
	$_POST['branch_detail'] = $job_details['branch_detail'];
	$_POST['working_place'] = $job_details['working_branch']; 
	$_POST['mod_of_pay'] = $job_details['mod_of_pay'];
	$_POST['bank_name'] = $job_details['bank_name'];
	$_POST['acc_no'] = $job_details['acc_no'];
	$basic_id = kv_get_basic();
	$_POST['gross'] = $job_details['gross'];
    $_POST['empl_basic_salary'] = $job_details['basic_pay'];
    $_POST['empl_house_rent'] = $job_details['house_rent'];
    $_POST['empl_meal_allowance'] = $job_details['meal_pay'];
    $_POST['empl_perfo_allowance'] = $job_details['perfom_bonus'];
    $_POST['empl_other_allowance'] = $job_details['other_bonus'];
    $_POST['empl_monthly_salary'] = $job_details['monthly_salary'];
	$_POST['iban'] = $job_details['iban'];
	$_POST['work_hours']=$job_details["work_hours"];
	$_POST['card_no_salary']=$job_details["card_no_salary"];

//$_POST['payroll_account_code']='10114';





//$_POST['desig_group'] = $job_details['desig_group'];
//$_POST['desig'] = $job_details['desig'];

//print_r($_POST);

	$Allowance = get_allowances(null, null, null, null, 0, $_POST['grade']);
	while ($single = db_fetch($Allowance)) {	
		$_POST[$single['id']] = $job_details[$single['id']];
	}

	$emp_salary = get_emp_salary_structure($_POST['empl_id']);
	$salary_array='0';
if(db_num_rows($emp_salary)>0)
{
	$sign='';

    foreach($emp_salary as $pay_element) {
  //display_error(print_r($pay_element,true));
        $element_code = $pay_element['pay_rule_id'];
       // if($pay_element['is_basic'] == 1)
          //  $_POST['basic_amt'] = price_format($pay_element['pay_amount']);
        //else
            $_POST['amt_'.$element_code] = price_format($pay_element['pay_amount']);
		if($pay_element['pay_type'] == 1)
		{
			$salary_array-=$pay_element['pay_amount'];
		}
		else
		{
			$salary_array+=$pay_element['pay_amount'];
		}

        //$salary_array+=$pay_element['pay_amount'];
    }
}
else
{
    $elements = get_payroll_elements();
    while($row = db_fetch($elements)) {
        $_POST['amt_'.$row['id']] ='';
    }

}



$emp_leave = get_emp_leave_structure($_POST['empl_id']);

if(db_num_rows($emp_leave)>0)
{
    foreach($emp_leave as $leave) {
        $element_code = $leave['leave_id'];
        $_POST['leave_'.$element_code] = $leave['days'];
    }
}
else
{
    $elements = get_leave_elements();
    while($row = db_fetch($elements)) {
        $_POST['leave_'.$row['id']] ='';
    }

}

/***************************EMPLOYEE PAYROLL ACCOUNTSDATA FETCH********/

$get_emp_account_edit_data=get_emp_account_edit_data($_POST['empl_id']);
//$get_emp_account_edit_data);
/*if(sizeof($get_emp_account_edit_data)>1)
{
    if($_POST['payroll_account_code']=='')
    {
        $_POST['payroll_account_code']=$get_emp_account_edit_data['payable_empl_base_acc'];
    }

    if($_POST['axispro_subledger_code']=='')
    {
        $_POST['axispro_subledger_code']=$get_emp_account_edit_data['payable_emp_sub_ledger'];
    }

    if($_POST['advance_account_code']=='')
    {
        $_POST['advance_account_code']=$get_emp_account_edit_data['advance_salary_account_base'];
    }
    if($_POST['advance_subledger_account_code']=='')
    {
        $_POST['advance_subledger_account_code']=$get_emp_account_edit_data['advance_emp_sub_ledger'];
    }
    if($_POST['loan_account_code']=='')
    {
        $_POST['loan_account_code']=$get_emp_account_edit_data['emp_loan_base_account'];
    }

    if($_POST['loan_subledger_account_code']=='')
    {
        $_POST['loan_subledger_account_code']=$get_emp_account_edit_data['emp_loan_subledger_account'];
    }

	if($_POST['base_salary_account_code']=='')
	{
		$_POST['base_salary_account_code']=$get_emp_account_edit_data['base_salary_account'];
	}

	if($_POST['base_salary_sub_account_code']=='')
	{
		$_POST['base_salary_sub_account_code']=$get_emp_account_edit_data['base_salary_account'];
	}

	if($_POST['pf_account_code']=='')
	{
		$_POST['pf_account_code']=$get_emp_account_edit_data['emp_pf_base_account'];
	}

	if($_POST['pf_subledger_account_code']=='')
	{
		$_POST['pf_subledger_account_code']=$get_emp_account_edit_data['emp_pf_sub_legder'];
	}

	if($_POST['commssion_account_code']=='')
	{
		$_POST['commssion_account_code']=$get_emp_account_edit_data['emp_commison_base_acc'];
	}

	if($_POST['commssion_subledger_account_code']=='')
	{
		$_POST['commssion_subledger_account_code']=$get_emp_account_edit_data['emp_commsion_sub_legder'];
	}




}*/





$checked_or_hide='';
$style_param='';

if(sizeof($get_emp_account_edit_data)>1)
{
	$checked_or_hide='checked="checked"';
	$style_param='style="display:block;"';
}
else if($_POST['hdn_Emp_pay_option']=='1')
{
	$style_param='style="display:block;"';
	$checked_or_hide='checked="checked"';
}
else
{
	$style_param='style="display:none;"';
}

/**************************************END*****************************/

	/*
	$KVAllowance = kv_get_allowances(null, 0, $_POST['grade']);
	if($allow_calculate){
		$allowance_var_ar = array();
		foreach($KVAllowance as $single) {
			$allowance_var_ar[$single['id']] = '{$'.$single['unique_name'].'}';			
			$_POST[$single['id']] = 0;
		}
		
		while ($single = db_fetch($Allowance)) {			
			if($single['formula'] == '' && $single['value'] == 'Percentage' && $single['percentage']>0){			
				$_POST[$single['id']] = get_post($basic_id)*($single['percentage']/100);
			}elseif($single['value'] == 'Gross Percentage' && $single['percentage']>0){			
				$_POST[$single['id']] = get_post('gross')*($single['percentage']/100);
			}elseif($single['formula'] != '' && is_numeric($single['formula'])) {
				$_POST[$single['id']] = $single['formula'];					
			} elseif($single['formula'] != '' && !is_numeric($single['formula']) && (strpos($single['formula'], '{$ctc}') === false)){
				foreach($allowance_var_ar as $key => $allown){
					$single['formula'] = str_replace($allown,$_POST[$key],strtolower($single['formula']));
				}
				if(strpos($single['formula'], '{$gros}'))
					$single['formula'] = str_replace('{$gros}', $_POST['gross'],strtolower($single['formula']));
				//display_error($single['formula']);
				$_POST[$single['id']] = round(calculate_string($single['formula']),2);
			}
		}
		
	} elseif (get_post('UPDATE_ITEM') != 'Update') { 
		while ($single = db_fetch($Allowance)) {	
			$_POST[$single['id']] = $job_details[$single['id']];
		}	
		
		//display_error('aegaegawe'.json_encode($_POST));
	}
		*/

	if(($_POST['desig_group']!=$job_details['desig_group']) && $_POST['desig_group']!='' && $job_details['desig_group']!='' )
	{
		$job_details['desig_group']=$_POST['desig_group'];
	}

if(($_POST['department']!=$job_details['department']) && $_POST['department']!='' && $job_details['department']!='' )
{
	 $job_details['department']=$_POST['department'];
}

	//print_r( $_POST['desig_group'].'---'.$job_details['desig_group']);
	br();
	start_outer_table(TABLESTYLE2);
	table_section(1);
	table_section_title(trans("Job Details"));

	label_row(trans("Employee Id:"),$get_employee_id[0]);
dimensions_list_row(trans("Cost Center :"), 'cost_center', null, true, " ", false, 1);
department_list_row( trans("Department :"), 'department', $job_details['department'], false, true, false,false, true);
divison_list_row( trans("Division :"), 'desig_group', $job_details['desig_group'], $_POST['department'], true, false,false, true);
designation_list_row(trans("Designation *:"), 'desig', $job_details['desig'],$_POST['desig_group']);

     //hrm_empl_desig_group(trans("Designation Group *:"), 'desig_group', null);
	 //designation_list_row(trans("Designation *:"), 'desig', null);
	//hidden('prev_desig',$_POST['desig']);
	//hidden('prev_desig_date',$_POST['date_of_desig_change']);
	//date_row(trans("Joining") . ":", 'joining');
   /* echo "<tr><td class=\"label\">".trans("Date of Joinn:")."</td><td>
              <input type=\"text\" name=\"joining\" class=\"date\" id =\"datepicker-15\" size=\"11\" value='".date('d-m-Y',strtotime($_POST['joining']))."' autocomplete=\"off\"> <a tabindex=\"-1\" alt=\"joining\" class=\"clsClander\" >	<img src=\"../../../themes/daxis/images/cal.gif\" style=\"vertical-align:middle;padding-bottom:4px;width:16px;height:16px;border:0;\" alt=\"Click Here to Pick up the date\"></a>
              </td>
              </tr>";*/

echo "<tr><td class=\"label\">".trans("Date of Join")."</td><td>
<input type=\"date\" name=\"joining\" id=\"datepicker-15\" value='".date('Y-m-d',strtotime($_POST['joining']))."' />  
</td>
</tr>";

	hrm_empl_nationality_row( trans("Nationality :"), 'nationality', null);
//	kv_empl_number_list_row(trans("Family :"), 'family', null, 0, 20);
	hrm_empl_workings_days('Weekend/Weekly Off:', 'weekly_off', null, false, true);
	hrm_empl_medical_premium_row(trans("Medical Category :"), 'medi_category', null);
	hrm_empl_type_row(trans("Employment Type*:"), 'empl_type', null,true);
	if(list_updated('empl_type'))
		$Ajax->activate('job_details');
	if(get_post('empl_type')== 3)
			empl_picklist_row(trans("Contract Type:"), 'empl_contract_type',null, false, false, 4);
		else
			hidden('empl_contract_type',0);
	//hrm_empl_shift(trans("Shift*:"), 'shift', null);
	workcenter_list_row(trans("Working Place*:"), 'working_place');
    $checked_commision='';
    $checked_pf='';
	if($job_details["calculate_commission"]=='1')
	{
		$checked_commision='checked="checked"';
	}

	if($job_details["calculate_pf"]=='1')
	{
		$checked_pf='checked="checked"';
	}

	if($job_details["head_of_dept"]=='1')
	{
		$head_of_pf='checked="checked"';
	}

	if($job_details["dept_supervisor"]=='1')
	{
		$supervisor='checked="checked"';
	}

		echo '<tr>
                <td class="label">Working Hours :</td>
                <td><input type="text" id="work_hours" name="work_hours" value="'.$_POST["work_hours"].'"/></td>
              </tr>
              <tr>
                <td class="label">Calculate Commission :</td>
                <td><input type="checkbox" id="check_commission" name="check_commission" '.$checked_commision.'/></td>
              </tr>
              <tr>
                <td class="label">Calculate PF :</td>
                <td><input type="checkbox" id="check_pf" name="check_pf" '.$checked_pf.'/></td>
              </tr>
               <tr>
                <td class="label">Head Of Department :</td>
                <td><input type="checkbox" id="head_of_dept" name="head_of_dept" '.$head_of_pf.'/></td>
               </tr>
               <tr>
                <td class="label">Department Supervisor :</td>
                <td><input type="checkbox" id="dept_supervisor" name="dept_supervisor" '.$supervisor.'/></td>
               </tr>
              ';

echo '<tr style="background-color: #009688;">
                <td class="label" style="color: white;">Check Employee Has Specific Account </td>
                <td><input type="checkbox" id="empl_payroll_accounts" name="empl_payroll_accounts" '.$checked_or_hide.'/></td>
                <td><input type="hidden" id="hdn_Emp_pay_option" name="hdn_Emp_pay_option" value="'.$_POST['hdn_Emp_pay_option'].'"/></td>
              </tr>';

echo '</table>
<div style="height: 402px;
    overflow: auto;">
<table class="tablestyle_inner" id="ShowOrHideEmployeeParyollOpt"  '.$style_param.'>';

/****************************BASE SLAARY ACCOUNT EACH EMPLOYEE WISE*************/
/*echo gl_all_accounts_list_row(trans("Base Salary Head Account:"), 'base_salary_account_code',false, true, false,false, true);
axispro_subledger_list_cells('Base Salary Sub-ledger', 'base_salary_sub_account_code', $_POST['base_salary_account_code'], null);*/
/***********************************END*************************************/


/****************************PAYABEL ACCOUNT EACH EMPLOYEE WISE*************/
$sql_payroll="select value
             from 0_sys_prefs
             where name='payroll_payable_act'";
$res_payable= db_query($sql_payroll,"Query execution failed");
$payroll_payable_data = db_fetch($res_payable);
axispro_subledger_list_cells('Payroll Payable Sub-ledger', 'axispro_subledger_code', $payroll_payable_data['value'], null);
/***********************************END*************************************/


/****************************ESB ACCOUNT EACH EMPLOYEE WISE*************/
$sql_esb="select value
             from 0_sys_prefs
             where name='payroll_esb_account'";
$res_esb= db_query($sql_esb,"Query execution failed");
$esb_data = db_fetch($res_esb);
axispro_esb_list_cells('Payroll Esb Account', 'esb_sub_ledger', $esb_data['value'], null);
/***********************************END*************************************/


$elements = get_payroll_elements_to_assign_acc();
$sub_account_code='';
while($row = db_fetch($elements)) {

	$personal_account=fetch_account_if_assigned($_POST['empl_id'],$row['id']);
	 if($personal_account['sub_account_code']!='')
	 {
		 $_POST['acc_'.$row['id']]=$personal_account['sub_account_code'];
	 }


	axispro_subledger_list_cells_pay_elements($row['element_name'], 'acc_'.$row['id'], $row['account_code'], null);
}


//echo '</table></td></tr>';
hidden('family', '0') ;
	hidden('empl_page', 'job') ; 
	/*table_section_title(trans("KYC IDS"));
		text_row(trans("Insurance Number:"), 'ESIC', null,  25, 100);
		text_row(trans("Insurance Company Name:"), 'PF', null,  25, 100);*/
		//text_row(trans("PAN Card No*:"), 'PAN', null,  25, 100);
	
//	table_section_title(trans("KYC Details"));
//		hrm_empl_blood_list(trans("Blood Group:"), 'bloog_group', null);
//		text_row(trans("National ID No:"), 'aadhar', null,  25, 100);
//		text_row(trans("Nominee Name:"), 'nominee_name', null,  25, 100);
//		text_row(trans("Nominee Phone Number:"), 'nominee_phone', null,  25, 100);
//		text_row(trans("Nominee Email:"), 'nominee_email', null,  30, 100);
//		textarea_row(trans("Nominee Address :"), 'nominee_address', null,  30, 5);
	table_section(3);
	if(!isset($_POST['currency'])){
		$company_record = get_company_prefs();
		$_POST['currency']  = $company_record["curr_default"];
	}elseif($_POST['currency'] == ''){
		$company_record = get_company_prefs();
		$_POST['currency']  = $company_record["curr_default"];
	}
	currencies_list_row(trans("Currency"), 'currency', null);
	//kv_empl_grade_list_row( trans("Grade :"), 'grade', null, false, true);
	label_row(trans("Grade").":", GetSingleValue('kv_empl_grade', 'description', array('id' => $_POST['grade'])));
	//$basic_id = kv_get_basic();
	
	$EarAllowance = get_allowances('Earnings', 'Profile Input', 'Percentage', 'Gross Percentage', 0, get_post('grade'), 'Formula');
	//if(get_post('2'))
	//	display_error($_POST['2']);	
	if(db_num_rows($EarAllowance) > 0){
		table_section_title(trans("Pay Details - Earnings"));
		$gross = GetRow('kv_empl_allowances', array('gross' => 1));
		
		//kv_basic_row(get_allowance_name($gross['id']), 'gross', 15, 100, null, true);		
		//label_row(get_allowance_name($gross['id']), price_format($_POST['gross']));
		//text_row(trans(get_allowance_name($basic_id)), $basic_id, null,  15, 100);
		while ($single = db_fetch($EarAllowance)) {	
			//if($single['id'] != $basic_id)
				label_row(trans($single['description'])." :", price_format($_POST[$single['id']])); //, null,  15, 100);
				//display_error($_POST['2']);
		}
	}
	
	$ReimbursementAllowance = get_allowances('Reimbursement', 'Profile Input', 'Percentage', null, 0, get_post('grade'));
	if(db_num_rows($ReimbursementAllowance) > 0){
		table_section_title(trans("Reimbursement"));
		while ($single = db_fetch($ReimbursementAllowance)){
			//text_row(trans($single['description'])." :", $single['id'], null,  15, 100);
			label_row(trans($single['description'])." :", price_format($_POST[$single['id']]));
		}
	}

	$CTCAllowance = get_allowances('Employer Contribution', 'Profile Input', 'Percentage', null, 0, get_post('grade'));	
	if(db_num_rows($CTCAllowance) > 0){	
		table_section_title(trans("Employer Contribution"));
		while ($single = db_fetch($CTCAllowance)){
			//text_row(trans($single['description'])." :", $single['id'], null,  15, 100);
			label_row(trans($single['description'])." :", price_format($_POST[$single['id']]));
		}
	}

	$DedAllowance = get_allowances('Deductions', 'Profile Input', 'Percentage', null, 0, get_post('grade'));
	if(db_num_rows($DedAllowance) > 0){
		table_section_title(trans("Deductions"));
		while ($single = db_fetch($DedAllowance)) {				
			if($single['Tax'] != 1 && $single['esic'] !=1 && $single['pf'] != 1 && $single['loan'] != 1){
				//kv_text_row_ex(trans($single['description'])."(-) :", $single['id'], 15, 100);
				label_row(trans($single['description'])." :", price_format($_POST[$single['id']]));
			}		
		}
	}
	//text_row_ex(trans("Maximum allowed Limit Percentage:"), 'expd_percentage_amt', 10, 10, '', null, null, "% for Loan Monthly Pay");
	empl_shifts_list_row(trans("Employee Shift"), 'shift', null, trans("Company Time"));

    table_section_title(trans("Pay Elements"));
    /*-------------------------------Employee Salary Details-------------*/
    /*text_row(trans("Basic Salary Amount:*"), 'empl_basic_salary', null, 35, 100);
    text_row(trans("House Rent Allowance:"), 'empl_house_rent', null, 35, 100);
    text_row(trans("Meal Allowance:"), 'empl_meal_allowance', null, 35, 100);
    text_row(trans("Performance Bonus:"), 'empl_perfo_allowance', null, 35, 100);
    text_row(trans("Others:"), 'empl_other_allowance', null, 35, 100);
    text_row(trans("Monthly Salary: *"), 'empl_monthly_salary', null, 35, 100);*/
//amount_row(trans('Basic Salary Amount:'), 'basic_amt', null, null, null, null, true);
$elements = get_payroll_elements();
while($row = db_fetch($elements)) {
	if($row['calculate_percentage']=='0')
	{
		$pay_class='amount';
	}
	else
	{
		$pay_class='';
	}
	echo '<tr><td class="label">'.$row['element_name'].' :</td>
                  <td><input class="'.$pay_class.'" type="text" name="amt_'.$row['id'].'" size="15" maxlength="15" dec="2" alt_type="'.$row['type'].'" value="'.$_POST['amt_'.$row['id']].'"></td>
                  </tr>';

}
echo '<tr>
                <td class="label">Total Salary :</td>
                <td><label id="lblTotalSalary" name="lblTotalSalary" style="color: red;font-size: 18pt;font-weight: bold;">
                AED'. price_format(abs($salary_array)).' 
</label><input type="hidden" id="hdnTota_salary" value="'.$salary_array.'" /></td>
              </tr>';

    /*--------------------------------------END Salary Details-----------*/

	//table_section_title(trans("Bond Details"));
	if(isset($job_details['bond_period'])&& $job_details['bond_period'] != '0000-00-00')
			$_POST['b_period_setting'] = 1;
		//check_row(trans("Bond Period"), 'b_period_setting',null,true);
		/*if((list_updated('b_period_setting') || get_post('b_period_setting'))&& check_value('b_period_setting')==1){
				date_row(trans("End of bond date") . ":", 'bond_period');
				if(isset($job_details['bond_doc']) && $job_details['bond_doc'] != null){
					label_row(trans("Attachment"), viewer_link($job_details["bond_doc"], 'modules/ExtendedHRM/manage/add_empl_info_job.php?vw='.$job_details["empl_id"]));
				}
				$Ajax->activate('job_details');
				kv_doc_row(trans("Select Docs") . ":", 'kv_attach_name', 'kv_attach_name');
		}else{
			hidden('bond_period','0000-00-00');
		}*/

hidden('bond_period','0000-00-00');
	    table_section_title(trans("Payment Mode"));
		hrm_empl_mop_list(trans("Mode of Pay *:"), 'mod_of_pay', null);
		text_row(trans("Bank Name :"), 'bank_name', null, 30, 30);
		text_row(trans("Bank Account No :"), 'acc_no', null, 30, 30);
		text_row(trans("Bank Branch :"), 'branch_detail', null,  15, 100);
		text_row(trans("SWIFT Code :"), 'ifsc', null,  15, 100);
		text_row(trans("IBAN :"), 'iban', null,  null, 100);
text_row(trans("Salary Card No :"), 'card_no_salary', null,  null, 100);
	  table_section_title(trans("Leave Details"));
/* $_POST['al'] = (isset($_POST['al']) ? $_POST['al'] : $job_details['al']);
$_POST['hl'] = (isset($_POST['hl']) ? $_POST['hl'] : $job_details['hl']);
$_POST['ml'] = (isset($_POST['ml']) ? $_POST['ml'] : $job_details['ml']);
$_POST['sl'] = (isset($_POST['sl']) ? $_POST['sl'] : $job_details['sl']);
$_POST['slh'] = (isset($_POST['slh']) ? $_POST['slh'] : $job_details['slh']);

text_row(trans('Annual Leave'), 'al', null,  5, 10);
text_row(trans('Sick Leave Full Days'), 'sl', null,  5, 10);
text_row(trans('Sick Leave Half Days'), 'slh', null,  5, 10);
text_row(trans('Maternity Leave'), 'ml', null,  5, 10);
text_row(trans('Hajj Leave'), 'hl', null,  5, 10);*/

        $elements = get_leave_elements();
        while($row = db_fetch($elements)) {
            text_row($row['description'].':', 'leave_'.$row['id'], null, null, null, null, true);
        }

	end_outer_table(1);	
 
	submit_add_or_update_center($empl_id == -1, '', 'both');
	div_end();

	end_form();
	end_page(@$_GET['popup'], false, false);

	
?>
