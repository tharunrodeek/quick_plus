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
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
if (isset($_GET['vw']))
	$view_id = $_GET['vw'];
else
	$view_id = find_submit('view');
if ($view_id != -1){	//echo $view_id;
	$row = GetRow('kv_empl_license', array('id' => $view_id));
	if ($row['unique_name'] != ""){
		if(in_ajax()) {
			$Ajax->popup($_SERVER['PHP_SELF'].'?vw='.$view_id);
		} else {
			$type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';	
    		header("Content-type: ".$type);
    		header('Content-Length: '.$row['filesize']);
 			header("Content-Disposition: inline");
 			//display_error( company_path(). "/attachments/empldocs/".$row['unique_name']);
	    	echo file_get_contents(company_path(). "/attachments/licenses/".$row['unique_name']);
    		exit();
		}
	}	
}
if (isset($_GET['dl']))
	$download_id = $_GET['dl'];
else
	$download_id = find_submit('download');

if ($download_id != -1){
	$row = GetRow('kv_empl_cv', array('id' => $download_id));
	if ($row['unique_name'] != ""){
		if(in_ajax()) {
			$Ajax->redirect($_SERVER['PHP_SELF'].'?dl='.$download_id);
		} else {
			
    		header("Content-type: 'application/octet-stream' ");
	    	//header('Content-Length: '.$row['filesize']);
    		header('Content-Disposition: attachment; filename='.$row['filename']);
    		echo file_get_contents(company_path()."/attachments/empldocs/".$row['unique_name']);
	    	exit();
		}
	}	
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

page(trans($help_context = "Employees"), @$_REQUEST['popup'], false, "", $js);
echo '<script language="javascript" type="text/javascript" src="'.$path_to_root.'/modules/ExtendedHRM/js/nicEdit-latest.js"></script>';
if(kv_check_payroll_table_exist()){}else {
	display_error(trans("There are no Allowance defined in this system. Kindly Setup <a href='".$path_to_root."/modules/ExtendedHRM/manage/allowances.php' target='_blank'>Allowances</a> Your Allowances."));
	end_page();
    exit;
}
/*if(db_has_basic_pay()){ } else{
	display_error(trans("Basic Pay is not Setup in the system. Kindly Setup <a href='".$path_to_root."/modules/ExtendedHRM/manage/allowances.php' target='_blank'>Basic Pay</a> here."));
	end_page();
    exit;
}*/
/*if(db_has_tax_pay()){ } else{
	display_error(trans("Tax is not Setup in the system. Kindly Setup <a href='".$path_to_root."/modules/ExtendedHRM/manage/allowances.php' target='_blank'>Tax </a> here."));
	end_page();
    exit;
}
check_db_has_salary_account(trans("There are no Salary Account defined in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/hrm_settings.php' target='_blank'>Settings</a> to update it."));
*/
check_db_has_Departments(trans("There is no Department in the system to add employees. Please Add some <a href='".$path_to_root."/modules/ExtendedHRM/manage/department.php' target='_blank'>Department</a> "));

$new_item = get_post('empl_id')=='' || get_post('cancel') || get_post('clone'); 

 
//------------------------------------------------------------------------------------

if (isset($_GET['empl_id']))
{
	$_POST['empl_id'] = $_GET['empl_id'];
}
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
if (get_post('cancel')) {
	$_POST['employee_id'] = $empl_id = $_POST['empl_id'] = '';
    clear_data();
	set_focus('empl_id');
	$Ajax->activate('_page_body');
}
if(isset($_GET['Added']) && $_GET['Added'] == 'yes')
	display_notification(trans("A new Employee has been added."));
elseif(isset($_GET['Updated']) && $_GET['Updated'] == 'yes')
	display_notification(trans("The Selected Employee Information has been Updated."));
$basic_id = kv_get_basic();
if(list_updated('grade_id') || get_post('RefreshInquiry')|| get_post('gross')) {
		$Allowance = get_allowances(null, null, null, null, 0, get_post('grade_id'));
		$KVAllowance = kv_get_allowances(null, 0, get_post('grade_id'));
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
			//display_error($_POST[$single['id']]);
		}
		
		$Ajax->activate('payroll_tbl');
}

if (get_post('cancel')) {
	$_POST['empl_id'] = $empl_id = $_POST['empl_id'] = '';
    clear_data();
	set_focus('empl_id');
	$Ajax->activate('_page_body');
}
if (list_updated('category_id') || list_updated('mb_flag') || list_updated('date_of_birth')) {
	$Ajax->activate('details');
}

function clear_data(){
	unset($_POST['empl_id']);
	unset($_POST['empl_salutation']); 
	unset($_POST['empl_firstname']); 
	unset($_POST['empl_lastname']); 
	unset($_POST['addr_line1']); 
	unset($_POST['addr_line2']); 
	unset($_POST['address2']); 
	unset($_POST['empl_city']); 
	unset($_POST['empl_state']); 
	unset($_POST['gender']); 
	unset($_POST['date_of_birth']); 
	unset($_POST['age']);  
	unset($_POST['marital_status']); 
	unset($_POST['home_phone']);	
	unset($_POST['mobile_phone']);  
	unset($_POST['email']);  
	unset($_POST['status']); 
	unset($_POST['del_image']); 
	unset($_POST['pic']);
	unset($_POST['ice_name']); 
	unset($_POST['ice_phone_no']);  

}

function validate_mobile($mobile){
    return preg_match('/^[0-9]{10}+$/', $mobile);
}


//------------------------------------------------------------------------------------
$upload_file = "";
if (isset($_POST['addupdate'])) {

	$input_error = 0;
	if ($upload_file == 'No')
		$input_error = 1;
/*	if (!validate_mobile($_POST['mobile_phone'])) {
		display_error(trans("The Employee mobile number is not valid one.".$_POST['gross']));
		set_focus('mobile_phone');
		return false;
	}*/

	if (strlen($_POST['employee_id']) == 0) {
		display_error(trans("The employee Id Can't be empty."));
		set_focus('employee_id');
		return false;
	}

    if (strlen($_POST['employee_id'])!=0 && $_POST['empl_id']==0) {
        $sqls = "SELECT id FROM 0_kv_empl_info where empl_id='".$_POST['employee_id']."'";
        print_r($sqls);
        $result = db_query($sqls, "Could not get data");
        $data = db_fetch($result);
        $employee_chk_id=$data[0];

        if($employee_chk_id!='')
        {
            display_error(trans("The employee Id already exists."));
            set_focus('employee_id');
            return false;
        }

    }

    /*if (strlen($_POST['empl_id']) < 3) {
        display_error(trans("The employee Id must have minimum three characters."));
        set_focus('empl_id');
        return false;
    } */
	if($new_item && ctype_alnum($_POST['employee_id']) == false){
		display_error(trans("The employee Id must be Combinations of Letters and Numbers, Not symbols."));
		set_focus('employee_id');
		return false;	
	}
	/*if ($new_item && db_has_selected_employee($_POST['empl_id']) !=null ) {
		display_error(trans("The employee Id Already Exist."));
		set_focus('empl_id');
		return false;
	} */
	if (strlen($_POST['empl_firstname']) == 0) {
		display_error(trans("The employee name cannot be empty."));
		set_focus('empl_firstname');
		return false;
	} 
	
	if (strlen($_POST['mobile_phone']) == 0) {
		display_error(trans("The employee mobile number Can't be empty."));
		set_focus('mobile_phone');
		return false;
	}
	if (strlen($_POST['ice_name']) == 0) {
		display_error(trans("The Emergency Contact name cannot be empty."));
		set_focus('ice_name');
		return false;
	} 
	
	if (strlen($_POST['ice_phone_no']) == 0) {
		display_error(trans("The Emergency Contact mobile number Can't be empty."));
		set_focus('ice_phone_no');
		return false;
	}

	if($_POST['cost_center']=='0')
    {
        display_error(trans("Please select cost center"));
        set_focus('cost_center');
        return false;
    }
	/*if ($new_item &&strlen($_POST['aadhar']) == 0) {
		display_error(trans("The employee Aadhar Number Can't be empty."));
		set_focus('aadhar');
		return false;
	}
	if ($new_item &&strlen($_POST['aadhar']) >12) {
		display_error(trans("The employee Aadhar Number in not valid."));
		set_focus('aadhar');
		return false;
	}*/
	/*if ($new_item &&strlen($_POST['ESIC']) == 0) {
		display_error(trans("The employee ESIC number Can't be empty."));
		set_focus('ESIC');
		return false;
	}
	if ($new_item &&strlen($_POST['PF']) == 0) {
		display_error(trans("The employee PF number Can't be empty."));
		set_focus('PF');
		return false;
	}*/
	/*if ($new_item &&strlen($_POST['PAN']) == 0) {
		display_error(trans("The employee PAN Card number Can't be empty."));
		set_focus('PAN');
		return false;
	}*/
	if($_POST['employee_id'] == $_POST['report_to']){
		display_error(trans("The report to Can't be same employee."));
		set_focus('report_to');
		return false;
	}

	/*if(!preg_match('/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/', $_POST['mobile_phone']))
    {
     display_error(trans("The employee mobile number Can't be Invalid."));
		set_focus('mobile_phone');
		return false;
    } */
    if ($new_item && db_has_employee_email($_POST['email'])) {
		display_error(trans("The E-mail already in Use."));
		set_focus('email');
		return false;
	} 

	if (isset($_POST['email']) && strlen(trim($_POST['email']))>0 && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === true) {
		display_error(trans("The Entered E-Mail is not Valid."));
		set_focus('basic');
		return false;
	}

	if($new_item &&  date2sql($_POST['joining']) > date('Y-m-d')){
		display_error(trans("Invalid Joining Date for the Employee."));
		set_focus('joining');
		return false;
	}
	/*if(strlen($_POST['age']) >2 || !check_num('age', 0)){
		display_error(trans("The entered age is invalid."));
		set_focus('age');
		return false;
	}	*/
	
	 if ($new_item){
		//$joining = new DateTime(date2sql($_POST['joining']));
		//$dob = new DateTime(date2sql($_POST['date_of_birth']));

		//$diff = $dob->diff($joining);

         $d1 = new DateTime(date('Y-m-d',strtotime($_POST['joining'])));
         $d2 = new DateTime(date('Y-m-d',strtotime($_POST['date_of_birth'])));

         $diff = $d2->diff($d1);

	 	/*if($diff->y < 18){
	 		display_error(trans("The employee Date of Birth is not valid one."));
			set_focus('date_of_birth');
			return false;
	 	}*/
	 }
	
	if (!isset($_POST['empl_id']) && (!isset($_POST['weekly_off']) || count($_POST['weekly_off']) == 0)) {
		display_error(trans("The employee weeky off Can't be empty."));
		set_focus('weekly_off');
		return false; 
	} 
 /*
	if ($new_item && strlen($_POST['empl_da']) == 0) {
		display_error(trans("The employee DA Can't be empty."));
		set_focus('empl_da');
		return false;
	} 
	if ($new_item && strlen($_POST['empl_hra']) == 0) {
		display_error(trans("The employee HRA Can't be empty."));
		set_focus('empl_hra');
		return false;
	} 

	if ($new_item && strlen($_POST['conveyance']) == 0) {
		display_error(trans("The employee Conveyance Can't be empty."));
		set_focus('conveyance');
		return false;
	} 

	if ($new_item && strlen($_POST['edu_other']) == 0) {
		display_error(trans("The employee Education Can't be empty."));
		set_focus('edu_other');
		return false;
	} 
	if ($new_item && strlen($_POST['medical_allowance']) == 0) {
		display_error(trans("The employee Medical Allowance Can't be empty."));
		set_focus('medical_allowance');
		return false;
	} 

	if ($new_item && strlen($_POST['empl_pf']) == 0) {
		display_error(trans("The employee PF Can't be empty."));
		set_focus('empl_pf');
		return false;
	} 
	if ($new_item &&  strlen($_POST['bank_name']) == 0 && $_POST['mod_of_pay']== 2) {
		display_error(trans("The employee Bank Name Can't be empty."));
		set_focus('bank_name');
		return false;
	} 
	if ($new_item &&  strlen($_POST['acc_no']) == 0 && $_POST['mod_of_pay']== 2) {
		display_error(trans("The employee Account Number Can't be empty."));
		set_focus('acc_no');
		return false;
	} 	*/
	
	if ($input_error != 1){
		if (check_value('del_image') || isset($_FILES['pic']) && $_FILES['pic']['name'] != '')	{
			$get_old_ext = GetSingleValue('kv_empl_info', 'empl_pic', array('empl_id' => $_POST['empl_id']));
			$filename = company_path().'/images/empl/'.$get_old_ext;
			if (file_exists($filename) && !is_dir($filename))
				unlink($filename);
		}
		$extension = '';
		if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '') {
			$empl_id = $_POST['employee_id'];
			$max_image_size = 500;
			$result = $_FILES['pic']['error'];
			$upload_file = 'Yes'; //Assume all is well to start off with
			$filename = company_path().'/images/empl';
			if (!file_exists($filename))
				mkdir($filename);

			$path_parts = pathinfo($_FILES["pic"]["name"]);
			$extension = $path_parts['extension'];	
			$flname = empl_img_name($empl_id).".".$extension;
			$filename .= "/".$flname;
						
			if ((list($width, $height, $type, $attr) = getimagesize($_FILES['pic']['tmp_name'])) !== false)
				$imagetype = $type;
			else
				$imagetype = false;
			
			if ($imagetype != IMAGETYPE_GIF && $imagetype != IMAGETYPE_JPEG && $imagetype != IMAGETYPE_PNG){	
				display_warning( trans("Only graphics files can be uploaded"));
				$upload_file ='No';
			}
			elseif (!in_array(strtoupper(substr(trim($_FILES['pic']['name']), strlen($_FILES['pic']['name']) - 3)), array('JPG','PNG','GIF'))){
				display_warning(trans("Only graphics files are supported - a file extension of .jpg, .png or .gif is expected"));
				$upload_file ='No';
			} 
			elseif ( $_FILES['pic']['size'] > ($max_image_size * 1024)) { //File Size Check
				display_warning(trans("The file size is over the maximum allowed. The maximum size allowed in KB is") . ' ' . $max_image_size);
				$upload_file ='No';
			} 
			elseif (file_exists($filename) && !is_dir($filename)){
				$result = unlink($filename);
				if (!$result) 	{
					display_error(trans("The existing image could not be removed"));
					$upload_file ='No';
				}
			}
			
			if ($upload_file == 'Yes'){
				$result  =  move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
			}
			$Ajax->activate('details');	 
		} else 
			$flname = null ;
		$array_info =  array( 'empl_salutation' => $_POST['empl_salutation'], 'empl_firstname' => $_POST['empl_firstname'],			'empl_lastname' => $_POST['empl_lastname'], 
				'addr_line1' => $_POST['addr_line1'], 'addr_line2' => $_POST['addr_line2'], 'address2' => $_POST['address2'], 'empl_city' => $_POST['empl_city'],
				'empl_state' => $_POST['empl_state'], 'country' => $_POST['country'], 'gender' =>$_POST['gender'], 'date_of_birth' => date('Y-m-d',strtotime($_POST['date_of_birth'])),
				'marital_status' => $_POST['marital_status'], 'home_phone' => $_POST['home_phone'], 'mobile_phone' => $_POST['mobile_phone'], 'email' => $_POST['email'],
				'status' => $_POST['status'], 'report_to' => $_POST['report_to'],'supervisor' => $_POST['supervisor'],'leave_request_forward'=>$_POST['forward_to'],
                'ice_name' => $_POST['ice_name'], 'ice_phone_no' => $_POST['ice_phone_no']
                ,'p_period' => (isset($_POST['p_period']) ? array($_POST['p_period'], 'date'):'0000-00-00'),
                'cmy_period' => (isset($_POST['cmy_period']) ? array($_POST['cmy_period'], 'date'):'0000-00-00'),
                'leave_update_permission'=>$_POST['leave_update_permission'],'document_approve_empl_id'=>$_POST['approved_by'],
                'emp_code'=>$_POST['emp_code'],'empl_arabic'=>$_POST['empl_arabic']);



		if($flname)
			$array_info['empl_pic'] = $flname;
		if (!$new_item) { /*so its an existing one */
			begin_transaction();
			$kv_empl_id = $_POST['empl_id']; 
			$date_of_change = end_month(Today());
			if(isset($_POST['old_status']) && $_POST['old_status'] != $_POST['status'] && $_POST['status'] != 1){
				$array_info['reason_status_change'] = '';
				$array_info['date_of_status_change'] = array($date_of_change, 'date');
			}
			$job = GetRow('kv_empl_info', array('id' => $empl_id));
			Update('kv_empl_info', array('id' => $empl_id), $array_info);
			// Update('users', array('user_id' => $empl_id), ['inactive'=>$_POST['status'] > 1 ? 1:0]);
			$inactive = $_POST['status'] > 1 ? '1' : '0' ;
			db_query('UPDATE `0_users` SET inactive = '.$inactive.' WHERE employee_id = '.$empl_id.' ', "Could not update users");
			set_focus('employee_id');
			commit_transaction();
			$array_info["date_of_birth"]=date2sql($_POST['date_of_birth']);
			//$result=array_diff($array_info,$job);
			//display_error(json_encode($array_info));
		//	display_error(json_encode($job));
			/*$last_change=Today();
			foreach ($result as $key => $value) {
				Insert('kv_empl_history', array('empl_id' => $_POST['empl_id'], 'option_name' => $key, 'option_value' => $value,'last_change' =>array($last_change,'date')));
			}*/
			//display_error(json_encode($result));
			meta_forward($_SERVER['PHP_SELF'], "empl_id=$kv_empl_id&Updated=yes"); 			
		} 
		else { //it is a NEW part
			begin_transaction();
			$array_info['empl_id'] = $_POST['emp_prefix'].''.$_POST['employee_id'];

            Insert('kv_empl_info', $array_info);

            /*------------------------------GET EMPLOYEE ID------------*/
            $sql = "SELECT id FROM 0_kv_empl_info  order by id desc Limit 1";
            $result = db_query($sql, "Could not get data");
            $employee_pk_id='';
            if(sizeof($result)>0)
            {
                $data = db_fetch($result);
                $employee_pk_id=$data[0];
            }

            /*---------------------Saving Employee Salary-------------*/
            //$basic_acc = $basic['pay_rule_id'];
            $pay_elements = array();
            $pay_elements[] = array(
                'emp_id' => $employee_pk_id,
                'pay_rule_id' => 0,
                'pay_amount' => input_num('basic_amt'),
                'type' => DEBIT,
                'is_basic' => 1
            );


            foreach($_POST as $p=>$val) {

                if(substr($p, 0, 4) == 'amt_') {

                  $pay_elements[] = array(
                        'emp_id' => $employee_pk_id,
                        'pay_rule_id' => substr($p, 4),
                        'pay_amount' => input_num($p),
                        'type' => input_num($p) > 0 ? DEBIT : CREDIT,
                        'is_basic' => 0
                    );
                }
            }
            //dd($pay_elements);
            save_personal_salary($pay_elements);

            $leave_elements = array();
            foreach($_POST as $p=>$val) {
                if(substr($p, 0, 6) == 'leave_') {
                    if(input_num($p)!='' && input_num($p)!='0')
                    {
                        $leave_elements[] = array(
                            'emp_id' => $employee_pk_id,
                            'leave_id' => substr($p, 6),
                            'days' => input_num($p)
                        );
                    }

                }
            }

           save_employee_leave($leave_elements);


            /*--------------------------------END-----------------------*/

			$kv_empl_id = $employee_pk_id;
			$jobs_arr =  array('empl_id' => $kv_empl_id,
							 'grade' => $_POST['grade_id'],
							 'al' => $_POST['al'],
							 'hl' => $_POST['hl'],
							 'ml' => $_POST['ml'],
							 'sl' => $_POST['sl'],
							 'department' => $_POST['department'],
							 'desig_group' => $_POST['desig_group'],
							 'desig' => $_POST['desig'],
							 'shift' => $_POST['shift'],
							 'currency' => $_POST['currency'],
							 'nationality' => $_POST['nationality'],
							 'medi_category' => $_POST['medi_category'],
							 'family' => $_POST['family'],
							 'weekly_off' => base64_encode(serialize($_POST['weekly_off'])), 
							 'joining' => date('Y-m-d',strtotime($_POST['joining'])),
							 'bond_period' => date2sql($_POST['bond_period']), 
							 'empl_type' =>  $_POST['empl_type'], 
							 'empl_contract_type' => $_POST['empl_contract_type'],
							 'expd_percentage_amt' =>  ($_POST['expd_percentage_amt'] == '' ? 0 : $_POST['expd_percentage_amt']), 
							 'working_branch' =>  $_POST['working_place'],
						 	 'mod_of_pay' => $_POST['mod_of_pay'],
							 'bank_name' => $_POST['bank_name'],
							 'nominee_address' => $_POST['nominee_address'],
							 'nominee_name' => $_POST['nominee_name'],
							 'nominee_phone' => $_POST['nominee_phone'],
							 'nominee_email' => $_POST['nominee_email'], 							 
							 'aadhar' => $_POST['aadhar'], 
							 'bloog_group' => $_POST['bloog_group'],
							 'ifsc' => $_POST['ifsc'],							 
							 'ESIC' => $_POST['ESIC'], 
							 'PF' => $_POST['PF'],							  
							 'branch_detail' => $_POST['branch_detail'], 
							 'acc_no' => $_POST['acc_no'],
                             'iban' => $_POST['iban'],
                             'work_hours' => $_POST['txt_emp_working_hrs'],
                             'cost_center'=> $_POST['cost_center'],
             'card_no_salary'=> $_POST['card_no_salary']
                );
			//dd($_POST['joining']);
			$Allowance = get_allowances(null, null, null, null, 0, $_POST['grade_id']);
			$gross_Earnings = 0 ;
			while ($single = db_fetch($Allowance)) {	
				if(input_num('E_'.$single['id'])){
					$jobs_arr[$single['id']] = input_num('E_'.$single['id']);
					if($single['type'] == 'Earnings')
						$gross_Earnings += input_num('E_'.$single['id']);
				}				
			}
			if(input_num('gross')  > 0 )
				$jobs_arr['gross'] = input_num('gross');
			else
				$jobs_arr['gross'] = $gross_Earnings;
			$jobs_arr['gross_pay_annum'] = $jobs_arr['gross']*12;


			Insert('kv_empl_job', $jobs_arr);
			UploadHandle($_POST['employee_id']);
			if(db_has_auto_empl_id()) {
				kv_update_next_empl_id_new($_POST['employee_id'],false,$_POST['emp_prefix']);
			}
			if(isset($_POST['role'])){
			//	$user_id = Insert('users' , array('user_id' => $_POST['emp_prefix'].''.$_POST['employee_id'], 'real_name' => $_POST['empl_firstname'], 'password' => md5('password'), 'email' => $_POST['email'], 'language' => 'C', 'role_id' => $_POST['role'], 'pos' => 1, 'print_profile' => 1, 'rep_popup' => 1, 'phone' => $_POST['mobile_phone'],'theme' => 'daxis'));
			//	$array_info['user_id'] = $user_id;

                /*------------------------Update user against Employee-----------------*/
               // $sqlQry = "Update 0_kv_empl_info  set user_id='".$user_id."' where id='".$kv_empl_id."' ";
               // $result = db_query($sqlQry, "Could not get data");
                /*--------------------------------END----------------------------------*/
			}

			if((isset($_POST['ual']) && $_POST['ual'] != '') || (isset($_POST['usl']) && $_POST['usl'] != '')||(isset($_POST['uml']) && $_POST['uml'] != '') || (isset($_POST['ucl']) && $_POST['ucl'] != '')){
				$fiscal_year = get_current_fiscalyear();
				$previous_month = str_pad((date("m", strtotime($fiscal_year['begin']))-1), 2, '0', STR_PAD_LEFT);
				Insert('kv_empl_salary', array('empl_id' => $_POST['employee_id'], 'year' => $fiscal_year['id'], 'month' => (int)$previous_month, 'date' => array(Today(), 'date'),
								'al' => -($_POST['ual']), 'hl' => -($_POST['uhl']),'sl' => -($_POST['usl']), 'ml' => -($_POST['uml']), 'net_pay' => -1 ));
			}
			clear_data();	
			$_POST['empl_id'] = $kv_empl_id;		
			set_focus('empl_id');
			$Ajax->activate('_page_body');
			commit_transaction();
			meta_forward($_SERVER['PHP_SELF'], "empl_id=$kv_empl_id&Added=yes");
		}			
	}
}

//------------------------------------------------------------------------------------
if (isset($_POST['delete']) && strlen($_POST['delete']) > 1) {
	$empl_id = $_POST['empl_id'];

	if (key_in_foreign_table($empl_id, 'kv_empl_salary', 'empl_id')){		
		display_error(trans("Cannot delete this Employee because Payroll Processed to this employee And it will be  added in the financial Transactions."));
	} else {
		$get_old_ext = GetSingleValue('kv_empl_info', 'empl_pic', array('empl_id' => $empl_id));
		delete_employee($empl_id);
		$filename = company_path().'/images/empl/'.$get_old_ext;
		if (file_exists($filename) && !is_dir($filename))
			unlink($filename);
		/*-------------------Delete USER Against Employee------------*/
        $sql = "SELECT user_id FROM 0_kv_empl_info WHERE id='".$empl_id."'";
        $result = db_query($sql, "could not get sales type");
        $row = db_fetch_row($result);
        if($row[0]!='')
        {
            $sql_del="DELETE FROM 0_users  WHERE id='".$row[0]."' ";
            db_query($sql_del, "could not delete the selected Employee");
        }

		/*--------------------END------------------------------------*/
		display_notification(trans("Selected Employee has been deleted."));
		$_POST['empl_id'] = '';
		clear_data(); 
		set_focus('empl_id');
		$new_item = true;
		$Ajax->activate('_page_body');	
	}}

//------------------------------------------------------------------------------------
function empl_personal_data(&$empl_id) {
	br();
	global $Ajax, $SysPrefs, $path_to_root, $page_nested, $new_item;	
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
		
			$myrow = GetRow('kv_empl_info', array('id' => $_POST['employee_id']));
		 
			$_POST['empl_salutation'] = $myrow["empl_salutation"];
			$_POST['empl_firstname'] = $myrow["empl_firstname"];
			$_POST['empl_arabic'] = $myrow["empl_arabic"];
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
			$_POST['supervisor']  = $myrow["supervisor"];
			$_POST['ice_name']	= $myrow["ice_name"];
			$_POST['ice_phone_no']  = $myrow["ice_phone_no"];
            $_POST['leave_update_permission']  = $myrow["leave_update_permission"];
            $_POST['forward_to']  = $myrow["leave_request_forward"];
            $_POST['forward_to']  = $myrow["leave_request_forward"];
            $_POST['approved_by']  = $myrow["approved_by"];
           $_POST['approved_by']  = $myrow["approved_by"];
			//$_POST['n_period'] = sql2date($myrow['n_period']);
			

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

            $get_employee_id=get_employee_id($_POST['employee_id']);
			label_row(trans("Employee Id:*"),$get_employee_id[0]);
        /*label_row(trans("Employee Code:*"),$get_employee_id[1]);*/

		hidden('employee_id', $_POST['employee_id']);
		
		set_focus('description');
			
	}  else {
		if(GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'empl_ref_type')))
		    if($_POST['employee_id']=='')
            {
                $_POST['employee_id'] = $employee_id = kv_get_next_empl_id();
            }

            $_POST['emp_prefix'] = kv_get_next_empl_prefix(); /*---------------Getting EMpID Prefix----------*/
		if(!isset($_POST['employee_id']))
			$_POST['employee_id'] = '';
		//text_row(trans("Employee Id:"), 'employee_id', $_POST['employee_id'], 21, 20);
        echo '<tr><td class="label">Employee Id:</td><td>
<input type="text" name="emp_prefix" style="width: 23%;display:none;" value="'.$_POST['emp_prefix'].'">
<input type="text" name="employee_id" size="21" maxlength="20" value="'.$_POST['employee_id'].'"></td>
</tr>';

        echo '<tr style="display: none;"><td class="label">Employee Code:</td><td>
<input type="text" name="emp_code" size="21" maxlength="20" value="'.$_POST['emp_code'].'"></td>
</tr>';
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
		if(!isset($_POST['medi_category']))
				$_POST['medi_category'] = '';
		if(!isset($_POST['family']))
				$_POST['family'] = '';
		//if(!isset($_POST['weekly_off']))
			//	$_POST['weekly_off'] = '';
		$_POST['nationality'] = (isset($_POST['nationality']) ? $_POST['nationality'] : GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'nationality')));
	}

	//kv_empl_salutation_list_row( trans("Salutation:"), 'empl_salutation', null);
	text_row(trans("First Name:*"), 'empl_firstname', null, 35, 100);
	text_row(trans("Last Name:"), 'empl_lastname', null,  35, 100);
    text_row(trans("Arabic Name:"), 'empl_arabic', null,  35, 100);
    //users_list_cells_display(trans("Assign user to employee:"), 'ddl_user', null,'');
	table_section_title(trans("Permanent Address"));
	text_row(trans("Line 1:"), 'addr_line1', null, 35, 100);
	text_row(trans("Line 2:"), 'addr_line2', null, 35, 100);
	text_row(trans("City:"), 'empl_city', null, 35, 100);
	text_row(trans("State:"), 'empl_state', null, 35, 100);
	if(!isset($_POST['country']))
		$_POST['country'] = GetSingleValue('kv_empl_option','option_value',array('option_name' => 'home_country'));
 	country_list_row(trans("Country:"), 'country', null);

 	table_section_title(trans("Residential Address"));
	textarea_row(trans("Residential Address:"), 'address2', null, 30, 5);
	
	table_section_title(trans("Contact Details"));
	text_row(trans("Home Phone:"), 'home_phone', null,  35, 100);
	text_row(trans("Mobile Phone:*"), 'mobile_phone', null,  35, 100);
	text_row(trans("Email:"), 'email', null, 35, 100);

	if (!isset($_POST['empl_id']) || $new_item) {		
		kv_empl_gender_list_row( trans("Gender:"), 'gender', null);
		 //date_row(trans("Date of Birth") . ":", 'date_of_birth', null, null, 0,0,0,null, true);
       echo "<tr><td class=\"label\">".trans("Date of Birth")."</td><td>
<input type=\"date\" name=\"date_of_birth\" id=\"datepicker-13\"   value='".$_POST['date_of_birth']."'  /> <a tabindex=\"-1\" alt=\"date_of_birth\" autocomplete=\"off\">	</a>
</td>
</tr>";
		if (list_updated('date_of_birth') || $new_item) {
			$_POST['age'] = date_diff(date_create(date2sql($_POST['date_of_birth'])), date_create('today'))->y;
		}
		//label_row(trans("Age:"), $_POST['age']);
		hrm_empl_marital_list_row( trans("Marital Status:"), 'marital_status', null);
		hrm_empl_nationality_row( trans("Nationality :"), 'nationality', null);
		//kv_empl_number_list_row(trans("Family :"), 'family', null, 0, 20);
		//hrm_empl_medical_premium_row(trans("Medical Category :"), 'medi_category', null);
		hrm_empl_workings_days('Weekend/Weekly Off:*', 'weekly_off', null, false, true);		
		
	}
		
	//hrm_empl_status_list(trans("Status*:"), 'status', null, true);
	hrm_empl_status_list(trans("Status*:"), 'status', null, true);
	if(isset($_POST['empl_id']) && list_updated('status')){
		if($_POST['status'] != 1)
			textarea_row(trans("Reason for Leaving:"), 'reason_status_change', null, 30, 5);

	}
	table_section(2); 
	div_start('payroll_tbl');	// Add image upload for New Item 
	table_section_title(trans("Personal Details"));
	$stock_img_link = "";
	$check_remove_image = false;
	if ($empl_id!= '' && file_exists(company_path().'/images/empl/'.$_POST['empl_pic']) && !is_dir(company_path().'/images/empl/'.$_POST['empl_pic'])){	
		$stock_img_link .= "<img id='empl_profile_pic' alt = '[".$_POST['empl_pic']."]' src='".company_path().'/images/empl/'.$_POST['empl_pic']."?nocache=".rand()."'"." height='150' border='1'>";
		$check_remove_image = true;
	} else {
		$stock_img_link .= "<img id='empl_profile_pic' alt = '[".$_POST['employee_id'].".jpg"."]' src='".$path_to_root.'/modules/ExtendedHRM/images/no-image.png'. "?nocache=".rand()."'"." height='150' border='1'>";
	}
	label_row("&nbsp;", $stock_img_link);		
	kv_image_row(trans("Photo (.jpg)") . ":", 'pic', 'pic');
	if ($check_remove_image)
		check_row(trans("Delete Image:"), 'del_image');
	if (isset($_POST['empl_id']) && !$new_item){
		kv_empl_gender_list_row( trans("Gender:"), 'gender', null);
		//date_row(trans("Date of Birth") . ":", 'date_of_birth', null, null, 0,0,0,null, true);
        echo "<tr><td class=\"label\">".trans("Date of Birth")."</td><td>
<input type=\"date\" autocomplete=\"off\" name=\"date_of_birth\" id=\"datepicker-16\"  value=".date('Y-m-d',strtotime($_POST['date_of_birth']))." /> 
</td>
</tr>";
		if (list_updated('date_of_birth') || $new_item|| get_post('date_of_birth')) {
			$_POST['age'] = date_diff(date_create(date2sql($_POST['date_of_birth'])), date_create('today'))->y;
		}
		//label_row(trans("Age:"), $_POST['age']);
		hidden('age', $_POST['age']);
		hrm_empl_marital_list_row( trans("Marital Status:"), 'marital_status', null);
	}
    hidden('family', '0') ;
    hidden('age', $_POST['age']);
    hidden('empl_page', 'info') ;

	if (!isset($_POST['empl_id']) || $new_item) { 
		if(!isset($_POST['currency'])){
			$company_record = get_company_prefs();
			$_POST['currency']  = $company_record["curr_default"];
		}		
		currencies_list_row(trans("Currency"), 'currency', null);
		//kv_empl_grade_list_row( trans("Grade :"), 'grade_id', null, false, true);
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
		
		table_section_title(trans("Payment Mode"));
		hrm_empl_mop_list(trans("Mode of Pay *:"), 'mod_of_pay', null);
		text_row(trans("Bank Name :"), 'bank_name', null,  15, 100);
		text_row(trans("Bank Account No :"), 'acc_no', null,  15, 100);
		text_row(trans("Bank Branch :"), 'branch_detail', null,  15, 100);
		text_row(trans("SWIFT Code :"), 'ifsc', null,  15, 100);
        text_row(trans("IBAN :"), 'iban', null,  null, 100);
        text_row(trans("Salary Card No :"), 'card_no_salary', null,  null, 100);

	
	}	
	table_section_title(trans("Emergency Contact(ICE)"));
	text_row(trans("Name:*"), 'ice_name', null,  35, 100);
	text_row(trans("Mobile Phone:*"), 'ice_phone_no', null,  35, 100);
	table_section_title(trans("Period Settings"));
		//if(isset($myrow['n_period'])&& $myrow['n_period'] != '0000-00-00')
		//	$_POST['n_period_setting'] = 1;
		if(isset($myrow['p_period'])&& $myrow['p_period'] != '0000-00-00')
			$_POST['p_period_setting'] = 1;
		if(isset($myrow['cmy_period'])&& $myrow['cmy_period'] != '0000-00-00')
			$_POST['c_period_setting'] = 1;
		/*check_row(trans("Notice Period"), 'n_period_setting',null,true);
		if((list_updated('n_period_setting') || get_post('n_period_setting'))&& check_value('n_period_setting')==1){
				$n1_period = GetSingleValue('kv_empl_option','option_value',array('option_name' => 'n_period'));
			date_row(trans("Notice Period (Days) :") , 'n_period',null,null,$n1_period);
			$Ajax->activate('profile');
		}*/
		check_row(trans("Probationary Period"), 'p_period_setting',null,true);
		if((list_updated('p_period_setting')|| get_post('p_period_setting')) && check_value('p_period_setting')==1){
			$p1_period = GetSingleValue('kv_empl_option','option_value',array('option_name' => 'p_period'));
			$_POST['p_period'] = (isset($myrow['p_period']) ? sql2date($myrow['p_period']) : Today());
			$_POST['p_period'] = add_months($_POST['p_period'], $p1_period);
            hidden('p_period','0000-00-00');
			//date_row(trans("Probationary Period (Month) :") , 'p_period',null,null,0,$p1_period);

			$Ajax->activate('profile');
		}
		check_row(trans("Contract Period"), 'c_period_setting',null,true);
		if((list_updated('c_period_setting')|| get_post('c_period_setting')) && check_value('c_period_setting')==1){
			$_POST['cmy_period'] = (isset($myrow['cmy_period']) ? sql2date($myrow['cmy_period']) : Today());  //sql2date($myrow['cmy_period']);
			$cm1_period = GetSingleValue('kv_empl_option','option_value',array('option_name' => 'cm_period'));
			$cy1_period = GetSingleValue('kv_empl_option','option_value',array('option_name' => 'cy_period'));
			//date_row(trans("Contract Period (Month/Year) :") , 'cmy_period',null,null,0,$cm1_period,$cy1_period);
            hidden('cmy_period','0000-00-00');
			$Ajax->activate('profile');
		}	
	//employee_list_row(trans("Report To:"), 'report_to', null, trans("Select an Employee"));
	employee_list_row(trans("Line Manager:"), 'report_to', null, trans("Select an Employee"));
   //employee_list_row(trans("Leave Request Forward To:"), 'forward_to', null, trans("Select an Employee"));
    //kv_empl_leave_approve_permission( trans("Allow leave status update:"), 'leave_update_permission', null);
    //employee_list_row(trans("Document Approved By:"), 'approved_by', null, trans("Select an Employee"));

	//employee_list_row(trans("Supervisor"), 'supervisor', null, trans("Select"));
	
	

	if(!isset($_POST['empl_id']) || $new_item){	
		//table_section_title(trans("Bond Details"));
		/*check_row(trans("Bond Period"), 'b_period_setting',null,true);
		if((list_updated('b_period_setting') || get_post('b_period_setting'))&& check_value('b_period_setting')==1){
				$_POST['bond_period']=Today(); 
				date_row(trans("End of bond date") . ":", 'bond_period');
				$Ajax->activate('payroll_tbl');
				kv_doc_row(trans("Select Docs") . ":", 'kv_attach_name', 'kv_attach_name');
		}else{
			hidden('bond_period','0000-00-00');
		}*/

        hidden('bond_period','0000-00-00');
		table_section(3);
		table_section_title(trans("Job Details"));

        dimensions_list_row(trans("Cost Center :"), 'cost_center', null, true, " ", false, 1);
		department_list_row( trans("Department :"), 'department', null, false, true, false,false, true);

        divison_list_row( trans("Division :"), 'desig_group', null, $_POST['department'], true, false,false, true);
		//hrm_empl_desig_group(trans("Division *:"), 'desig_group', null,$_POST['department'], true, false,false, true);
/*$get_desintions=get_divsions($_POST['department']);
        echo "<tr><td class=\"label\">Division *:</td><td><span id=\"_desig_group_sel\">
<select id=\"desig_group\" autocomplete=\"off\" name=\"desig_group\" class=\"combo\" title=\"\" _last=\"0\" >";
   while($row = db_fetch($get_desintions)) {
            echo '<option value="'.$row['id'].'">'.$row['description'].'</option>';
        }
echo "</select>
</span>
</td>
</tr>";*/

		designation_list_row(trans("Designation *:"), 'desig', null,$_POST['desig_group']);
     /*   if(GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'enable_employee_access')))
            security_roles_list_cells(trans("Access Role:"). "&nbsp;", 'role', null, false, false, check_value('show_inactive'));*/
		//text_row(trans("Desgination *:"), 'desig', null,  35, 100);
		//text_row(trans("Basic Salary *:"), 'basic_salary', null, 30, 30);
		//date_row(trans("Date of Join") . ":", 'joining');
     /*  echo "<tr><td class=\"label\">".trans("Date of Join:")."</td><td>
              <input type=\"text\" name=\"joining\" class=\"date\" id = \"datepicker-14\" size=\"11\"  autocomplete=\"off\"> <a tabindex=\"-1\" alt=\"joining\" class=\"clsClander\" >	<img src=\"../../../themes/daxis/images/cal.gif\" style=\"vertical-align:middle;padding-bottom:4px;width:16px;height:16px;border:0;\" alt=\"Click Here to Pick up the date\"></a>
              </td>
              </tr>";*/

        echo "<tr><td class=\"label\">".trans("Date of Join")."</td><td>
<input type=\"date\" autocomplete=\"off\" name=\"joining\" id=\"datepicker-14\" value='".$_POST['joining']."' /> <a tabindex=\"-1\" alt=\"date_of_join\">	</a>
</td>
</tr>";
		hrm_empl_type_row(trans("Employment Type*:"), 'empl_type', null,true);
		if(list_updated('empl_type') && get_post('empl_type')== 3)
			empl_picklist_row(trans("Contract Type:"), 'empl_contract_type',null, false, false, 4);
		else
			hidden('empl_contract_type',0);
			//hrm_empl_contract_type_row(trans("Contract Type*:"), 'empl_contract_type', null);

		workcenter_list_row(trans("Working Place*:"), 'working_place');

        echo '<tr>
                <td class="label">Working Hours :</td>
                <td><input type="text" id="txt_emp_working_hrs" name="txt_emp_working_hrs"/></td>
              </tr>';



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
        $pay_class='';
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
                  <td><input class="'.$pay_class.'" type="text" name="amt_'.$row['id'].'" size="15" maxlength="15" dec="2" alt_type="'.$row['type'].'" alt_per_calc="'.$row['calculate_percentage'].'"></td>
                  </tr>';

        }
        echo '<tr>
                <td class="label">Total Salary :</td>
                <td><label id="lblTotalSalary" name="lblTotalSalary" style="color: red;font-size: 18pt;font-weight: bold;"></label></td>
              </tr>';

		/*--------------------------------------END Salary Details-----------*/
		/*table_section_title(trans("KYC IDS"));
		text_row(trans("Insurance Number:"), 'ESIC', null,  30, 100);
		text_row(trans("Insurance Company Name:"), 'PF', null,  30, 100);
		//text_row(trans("PAN Card No*:"), 'PAN', null,  30, 100);
		//text_row_ex(trans("Maximum allowed Limit Percentage:"), 'expd_percentage_amt', 10, 10, '', null, null, "% for Loan Monthly Pay");
		if(GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'enable_employee_access')))
			security_roles_list_cells(trans("Access Role:"). "&nbsp;", 'role', null, false, false, check_value('show_inactive'));
		empl_shifts_list_row(trans("Employee Shift"), 'shift', null, trans("Company Time"));
		table_section_title(trans("KYC Details"));
		hrm_empl_blood_list(trans("Blood Group:"), 'bloog_group', null);
		text_row(trans("National ID No:"), 'aadhar', null,  25, 100);
		text_row(trans("Nominee Name:"), 'nominee_name', null,  25, 100);
		text_row(trans("Nominee Phone Number:"), 'nominee_phone', null,  25, 100);
		text_row(trans("Nominee Email:"), 'nominee_email', null,  30, 100);
		textarea_row(trans("Nominee Address :"), 'nominee_address', null,  30, 5);*/

		//table_section_title(trans("Leave Details"));
		/*$_POST['al'] = $_POST['sl'] = $_POST['ml']= $_POST['hl']  =  0;
		if(get_post('grade_id') || list_updated('grade_id')){
			$leave_values = GetRow('kv_empl_grade', array('id' => get_post('grade_id')));
			$_POST['al'] = round($leave_values['al']);
			$_POST['sl'] = round($leave_values['sl']);
			$_POST['slh'] = round($leave_values['slh']);
			$_POST['ml'] = round($leave_values['ml']);
			$_POST['hl'] = round($leave_values['hl']);
		}
		text_row(trans('Annual Leave'), 'al', null,  5, 10);
		text_row(trans('Sick Leave Full Days'), 'sl', null,  5, 10);
		text_row(trans('Sick Leave Half Days'), 'slh', null,  5, 10);
		text_row(trans('Maternity Leave'), 'ml', null,  5, 10);
		text_row(trans('Hajj Leave'), 'hl', null,  5, 10);
		table_section_title(trans("Unused Existing Leave Details"));
		text_row(trans('Annual Leave'), 'ual', null,  5, 10);
		text_row(trans('Sick Leave Full Days'), 'usl', null,  5, 10);
		text_row(trans('Sick Leave Half Days'), 'uslh', null,  5, 10);
		text_row(trans('Maternity Leave'), 'uml', null,  5, 10);
		text_row(trans('Hajj Leave'), 'uhl', null,  5, 10);*/

        /*$elements = get_leave_elements();
        while($row = db_fetch($elements)) {
            text_row($row['description'].':', 'leave_'.$row['id'], null, null, null, null, true);
        }*/
	} 	
	end_outer_table(1);
	div_end();
	div_end();
	div_start('controls');
	br();
	if (!isset($_POST['empl_id']) || $new_item) {
		submit_center('addupdate', trans("Add New Employee"), true, '', 'default');
	} else {
		submit_center_first('addupdate', trans("Update Employee Information"), '',@$_REQUEST['popup'] ? true : 'default');
		submit('delete', trans("Delete employee"), true, '', true);
		echo '<a target="_blank" href="'.$path_to_root.'/modules/ExtendedHRM/reports/rep812.php?PARAM_0=0&PARAM_1='.$_POST['empl_id'].'&rep_v=yes" class="printlink"><button  name="print" id="Print" value="Print"><img src="'.$path_to_root.'/themes/default/images/print.png" style="vertical-align:middle;width:12px;height:12px;border:0;" title="Print"><span>Print Employee Details</span></button></a>';
		submit_center_last('cancel', trans("Cancel"), trans("Cancel Edition"), 'cancel');
	}
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
			$total_leave = $leaveDays = $AL = $SL = $ML =$HL= 0;
			
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
					}elseif(isset($single_month[$vj]) && $single_month[$vj] == 'ML'){
						$ML++;
						$total_leave++;
					}elseif(isset($single_month[$vj]) && $single_month[$vj] == 'HL'){
						$HL++;
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
			$SLleave = GetSingleValue('kv_empl_job', 'sl', array('empl_id' => $empl_id))/12;
			$SLAvailable = $SLleave-$SL;
			$MLleave = GetSingleValue('kv_empl_job', 'ml', array('empl_id' => $empl_id))/12;
			$MLAvailable = $MLleave-$ML;
			$HLleave = GetSingleValue('kv_empl_job', 'hl', array('empl_id' => $empl_id))/12;
			$HLAvailable = $HLleave-$HL;
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
					label_cell('ML<br><h2>'.$ML.'</h2>');					
					label_cell('HL<br><h2>'.$HL.'</h2>');					
				end_row();
			/*	start_row();
					label_cell("<center><h3>".trans("Available Leave")."</h3></center><br> <hr>", "colspan='10'");
				end_row();
			
			echo '<hr>';
						
				start_row();
					label_cell('AL Paid<br><h2>'.$ALEncashed.'</h2>');
					label_cell('AL Payable<br><h2>'.$ALPayable.'</h2>');
					label_cell('AL<br><h2>'.$ALAvailable.'</h2>');						
					label_cell('SL<br><h2>'.$SLAvailable.'</h2>');						
					label_cell('ML<br><h2>'.$MLAvailable.'</h2>');						
					label_cell('HL<br><h2>'.$HLAvailable.'</h2>');						
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
							label_cell($data_for_empl[$single['id']], '', 'kv_gross_amt');
					}
					label_cell($data_for_empl['ot_earnings'], '', 'kv_gross_amt');
					label_cell($data_for_empl['ot_other_allowance'], '', 'kv_gross_amt');
					foreach($Allowance as $single) {	
						if($single['type'] == 'Reimbursement')
							label_cell($data_for_empl[$single['id']], '','kv_gross_amt');
					}
					label_cell($data_for_empl['gross'], '','kv_gross_amt');
					$ctc = $data_for_empl['gross'];
					foreach($Allowance as $single) {	
						if($single['type'] == 'Employer Contribution'){
							label_cell($data_for_empl[$single['id']], '', 'kv_ctc_amt');
							$ctc += $data_for_empl[$single['id']];
						}
					}
					label_cell($ctc, '', 'kv_ctc_amt');
					$total_deduct = $data_for_empl['misc']+$data_for_empl['lop_amount']; 
					foreach($Allowance as $single) {	
						if($single['type'] == 'Deductions'){
							label_cell($data_for_empl[$single['id']], '', 'kv_ded_amt');
							$total_deduct += $data_for_empl[$single['id']];
						}
					}
					
					//label_cell($data_for_empl['adv_sal']);
					//label_cell($data_for_empl['loan'], '', 'kv_ded_amt');
					label_cell($employee_leave_record, '', 'kv_ded_amt');
					label_cell($data_for_empl['lop_amount'], '', 'kv_ded_amt');
					label_cell($data_for_empl['misc'], '', 'kv_ded_amt');					
					label_cell($total_deduct, '', 'kv_ded_amt');
					label_cell($data_for_empl['net_pay'], '', 'kv_net_amt');

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
function empl_increment_data($empl_id){
	global $Ajax;
	$job_details = get_employee_job($empl_id);
	
	$incremnt_per = 1; 
	if(!isset($_POST['increment_percent']))
		$_POST['increment_percent'] = '';
	if(get_post('RefreshIncrement') && input_num('increment_percent') > 0 ) {		
		$incremnt_per = input_num('increment_percent')/100;		
		$Ajax->activate('Increment_table');
	}

	if (isset($_POST['applyincrement'])) {
		$Allowance = kv_get_allowances(null, 0, $job_details['grade']);
		$gross_Earnings = 0;
		foreach($Allowance as $single) {
			if(isset($_POST[$single['id']])){	
				$job_arr[$single['id']] = input_num($single['id'].'_');
				
				if($single['type'] == 'Earnings')
					$gross_Earnings += input_num($single['id'].'_');
			}
		}

		if(!isset($_POST['gross'])){
			$job_arr['gross'] = $gross_Earnings;
			$job_arr['gross_pay_annum'] = $gross_Earnings*12;
		} else {
			$job_arr['gross'] = $_POST['gross'];
			$job_arr['gross_pay_annum'] = $_POST['gross']*12;
		}
		//display_error(json_encode($job_arr));
		Update('kv_empl_job', array('empl_id' => $_POST['empl_id']), $job_arr);
		foreach($Allowance as $single) {
			unset($_POST[$single['id']]);
		}
		display_notification(trans("Your Increment ".$_POST['increment_percent'].'% Was Applied to the Selected Employee'));
	}

	div_start('Increment_table');
	$Allowance = kv_get_allowances(null,0, $job_details['grade']);
	//$DedAllowance = kv_get_allowances('Deductions',0, $job_details['grade']);
	//$Reimbursement = kv_get_allowances('Reimbursement',0, $job_details['grade']);
	//$AddToCTC = kv_get_allowances('Employer Contribution',0, $job_details['grade']);
	$Ear_allowance = $Ded_allowance = array();
	foreach($Allowance as $single){
		if($single['type'] == 'Earnings')
			$_POST[$single['id'].'_'] = ($incremnt_per != 1 ? $job_details[$single['id']]*$incremnt_per : 0);
		if($single['type'] == 'Deductions')
			$_POST[$single['id'].'_'] = ($incremnt_per != 1 ? $job_details[$single['id']]*$incremnt_per : 0);
	}
	br();
	start_outer_table(TABLESTYLE);
	table_section(1);
		table_section_title(trans("Current Salary"));
		$basic_id = kv_get_basic();
		table_section_title(trans("Earnings"));
		$AddToCTC = $Reimbursement = $DedAllowance = 0;
		foreach($Allowance as $single) {
			if($single['type'] == 'Earnings'){
				label_row(trans($single['description']), $job_details[$single['id']] /*.($_POST[$single['id']] > 0 ? ('+'.$_POST[$single['id']].'='.($job_details[$single['id']]+$_POST[$single['id']]))  : '' )*/);
					
				if($single['basic'] == 1 ){
					$_POST[$basic_id.'_'] = $job_details[$single['id']];
				}
			} 
			if($single['type'] == 'Reimbursement')
				$Reimbursement++;
			if($single['type'] == 'Employer Contribution')
				$AddToCTC++;
			if($single['type'] == 'Deductions')
				$DedAllowance++;
		}
		if(count($Reimbursement) > 0 ){
			table_section_title(trans("Reimbursement"));
			foreach($Allowance as $single) {
				if($single['type'] == 'Reimbursement')
					label_row(trans($single['description']), $job_details[$single['id']] );
			}
		}
		label_row(trans("Gross Salary"), price_format($job_details['gross'], 2));
		if(count($AddToCTC) > 0){
			table_section_title(trans("AddToCTC"));
			foreach($Allowance as $single) {
				if($single['type'] == 'Employer Contribution')
					label_row(trans($single['description']), $job_details[$single['id']]);
			}
		}
		if(count($DedAllowance) > 0){
			table_section_title(trans("Deductions"));
			$prof_tax = kv_get_Tax_allowance();
			$loan_id = kv_get_loan_field();
			foreach($Allowance as $single) {
				if($single['type'] == 'Deductions' && $single['id'] != $prof_tax && $single['loan'] != 1)
					label_row(trans($single['description']), $job_details[$single['id']]);
			}
		}
		table_section(2);
		table_section_title(trans("Current Salary + Increment"));
		text_row_ex(trans("Increment Percentage :"), 'increment_percent', 10, 10, '', null, null, "%");
		echo '<tr><td> </td>';
		submit_cells('RefreshIncrement', trans("Refresh"),'',trans("Show Results"), 'default');
		echo '</tr>';
		
		if(input_num('increment_percent') > 0 )
			$_POST[$basic_id.'_'] = $_POST[$basic_id.'_'] + $_POST[$basic_id.'_']*(input_num('increment_percent')/100);
		
		kv_text_row_ex(trans(get_allowance_name($basic_id))." :", $basic_id.'_', 15, 100, null, null, null, null, true);
		$EarAllowance = get_allowances('Earnings', null, null, null, 0, $job_details['grade']);		
		foreach($Allowance as $single) {	
			if($single['type'] == 'Earnings'){
				if($single['value'] == 'Percentage' && $single['percentage']>0){			
					$default_value = (get_post($basic_id.'_') +  get_post($basic_id.'_')*(input_num('increment_percent')/100))*($single['percentage']/100);
				}else {
					$default_value = 0;					
				}
				if($single['id'] != $basic_id){
					$_POST[$single['id']] = $default_value;
					kv_text_row_ex(trans($single['description']." :"), $single['id'].'_', 15, 100, null, $default_value, null, null, true);
					//$gross_pay += get_post($single['id']);						
				}					
			}					
		}

		$ReimbursementAllowance = get_allowances('Reimbursement', null, null, null, 0, $job_details['grade']);
		if(count($Reimbursement) > 0 ){
			table_section_title(trans("Reimbursement"));
			foreach($Allowance as $single){
				if($single['type'] == 'Reimbursement'){
					if($single['formula'] == '' && $single['value'] == 'Percentage' && $single['percentage']>0){	
						$default_value = (get_post($basic_id) +  get_post($basic_id)*(input_num('increment_percent')/100))*($single['percentage']/100);
					}else {
						$default_value = 0;
					}
					$_POST[$single['id']] = $default_value;
					kv_text_row_ex(trans($single['description']." :"), $single['id'].'_', 15, 100, null, $default_value, null, null, true);
				}
			}
		}
		$gross_increment = $job_details['gross']+($job_details['gross']*(input_num('increment_percent')/100));
		label_row(trans("Gross Salary"), price_format($gross_increment, 2));
		hidden('gross', $gross_increment);
		$CTCAllowance = get_allowances('Employer Contribution', null, null, null, 0, $job_details['grade']);
		if(count($CTCAllowance) > 0){					
			table_section_title(trans("Employer Contribution"));
			foreach($Allowance as $single){
				if($single['type'] == 'Employer Contribution'){
					if($single['formula'] == '' && $single['value'] == 'Percentage' && $single['percentage']>0){		
						$default_value = (get_post($basic_id) +  get_post($basic_id)*(input_num('increment_percent')/100))*($single['percentage']/100);
					}else {
						$default_value = 0;
					}
					$_POST[$single['id']] = $default_value;
					kv_text_row_ex(trans($single['description']." :"), $single['id'].'_', 15, 100, null, $default_value);
					//$to_ctc += get_post($single['id']);
				}
			}
		}

		$DedAllowance = get_allowances('Deductions', null, null, null, 0, $job_details['grade']);
		if(count($DedAllowance) > 0){	
			table_section_title(trans("Deductions"));
			foreach($Allowance as $single) {				
				if($single['type'] == 'Deductions'){
					if( $single['value'] == 'Percentage' && $single['percentage']>0){		
						$default_value = (get_post($basic_id) +  get_post($basic_id)*(input_num('increment_percent')/100))*($single['percentage']/100);
					}else {
						$default_value = 0;
					}
					if($single['id'] != $prof_tax && $single['loan'] != 1 && $single['value'] != 'Payroll Input' ){
						$_POST[$single['id']] = $default_value;
						kv_text_row_ex(trans($single['description']." (-) :"), $single['id'].'_', 15, 100, null, $default_value);
					}		
				}
			}
		}		
	end_outer_table(2);
	br();
	submit_center('applyincrement', trans("Apply Increment"), true, '', 'default');
	br();
	div_end();
}	

//-----------------------------------------------------------------------------------------
function empl_termination($empl_id){
	global $Ajax;	
	if (isset($_POST['changeStatus'])) {
		Update('kv_empl_info', array('empl_id' => $_POST['empl_id']), array('reason_status_change' => $_POST['reason_status_change'],'n_period' => (isset($_POST['n_period']) ? array($_POST['n_period'], 'date'):'0000-00-00'), 'status' => $_POST['status'], 'date_of_status_change' => array(Today(), 'date')));
		display_notification(trans("Status Changed successfully with reason"));
	}
	$empl_details = GetRow('kv_empl_info', array('empl_id' => $empl_id));
    $get_employee_id=get_employee_id($empl_id);
	$_POST['n_period']=sql2date($empl_details['n_period']);
	br();
	div_start('termination');
	start_table(TABLESTYLE);
		label_row(trans("Employee ID"), $get_employee_id[0]);
		label_row(trans("Employee Name"), $empl_details['empl_firstname'].' '.$empl_details['empl_lastname']);
		hrm_empl_status_list(trans("Status*:"), 'status', null, true);
		if( $empl_details['user_id'] > 0 )
			hidden('user_id', $empl_details['user_id']);
		textarea_row(trans("Reason for Leaving:"), 'reason_status_change', $empl_details['reason_status_change'], 30, 5);
		if(isset($empl_details['n_period'])&& $empl_details['n_period'] != '0000-00-00')
			$_POST['n_period_setting'] = 1;
				check_row(trans("Notice Period"), 'n_period_setting',null,true);
		if((list_updated('n_period_setting') || get_post('n_period_setting'))&& check_value('n_period_setting')==1){
				$n1_period = GetSingleValue('kv_empl_option','option_value',array('option_name' => 'n_period'));
			date_row(trans("Notice Period (Days) :") , 'n_period',null,null,$n1_period);
		//	display_error('n_period');
			$Ajax->activate('termination');
		}

	end_outer_table(2);
	br();
	submit_center('changeStatus', trans("Change Status"), true, '', 'default');
	br();	
	div_end();
}	

//-----------------------------------------------------------------------------------------
function empl_notes($empl_id){
	global $Ajax;
	if (isset($_POST['submitNote'])) {
		Update('kv_empl_info', array('empl_id' => $_POST['empl_id']), array('notes' => $_POST['description']));
		display_notification(trans("Note Updated successfully"));
	}
	br();
	$_POST['description'] = GetSingleValue('kv_empl_info', 'notes', array('empl_id' => $empl_id));
	start_table(TABLESTYLE);
		kv_hrm_textarea_row(null, 'description', null, 70, 10);
		//textarea_row(trans("Notes:"), 'notes', null, 50, 8);
	end_outer_table(2);
	br();
	submit_center('submitNote', trans("Submit"), true, '', 'default');
	br();	
}
//-------------------------------------------------------------------------------------------- 
//-----------------------------------------------------------------------------------------
function empl_life_to_date_history($empl_id){
	global $Ajax;
	$empl_history = GetAll('kv_empl_history', array('empl_id' => $empl_id),array('last_change'=>'DESC'));
	//display_error(json_encode($empl_history));
	$date_group = '0000-00-00';
	br(2);
	start_table(TABLESTYLE);
		foreach($empl_history as $history )	{
			if($date_group != $history['last_change']){
					$date_group = $history['last_change'];
					table_section_title(sql2date($history['last_change']));
				}
				label_row($history['option_name'], $history['option_value']);
			}
	end_outer_table(2);
	br();
	
}
//-------------------------------------------------------------------------------------------- 
start_form(true);
if (db_has_employees()) {
	start_table(TABLESTYLE_NOBORDER);
	start_row();
    department_list_cells(trans("Select a Department")." :", 'dept_id', null,	trans("No Department"), true, check_value('show_inactive'));
	employee_list_cells(trans("Select an Employee")." :", 'empl_id', null,	trans("---SELECT EMPLOYEE---"), true, check_value('show_inactive'), false, false,true);
	$new_item = get_post('empl_id')=='';
	end_row();
	end_table();
	if (get_post('_show_inactive_update')) {
		$Ajax->activate('empl_id');
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
		'job' => array(trans("Job"), (user_check_access('HRM_EMPLOYEE_MANAGE') ? $empl_id : null) ),
		//'leave' => array(trans("Attendance"), $empl_id),
		//'payroll' => array(trans("Payroll History"), $empl_id),
		//'license' => array(trans("License"), $empl_id),
		//'loan' => array(trans("Loan History"), $empl_id),
		//'increments' => array(trans("Increments"), $empl_id),
		'education' => array(trans("Education"), $empl_id),
		'skills' => array(trans("Language"), $empl_id),
		'previous_emplment' => array(trans("Employment History"), $empl_id),
		'training' => array(trans("Training"), $empl_id),
		//'attachments' => array(trans("Attachments"), $empl_id),
		//'family' => array(trans("Family"), $empl_id),
		//'termination' => array(trans("Termination"), $empl_id),
		//'note' => array(trans("Notes"), $empl_id)
		//'history' => array(trans("Life 2 Date"), $empl_id)
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
			include_once($path_to_root."/modules/ExtendedHRM/manage/add_empl_info_job.php");			
			break;
		case 'loan':			
			//empl_job_data($empl_id);	
			$_GET['empl_id'] = $empl_id;
			$_GET['page_level'] = 1;
			include_once($path_to_root."/modules/ExtendedHRM/manage/loan_form.php");			
			break;
		case 'education':
			$degree = new degree('degree', $empl_id, 'employee');
			$degree->show();	
			break;
		case 'skills':
			$degree = new skill('skills', $empl_id, 'employee');
			$degree->show();	
			break;
		case 'training':
			$training = new training('training', $empl_id, 'employee');
			$training->show();
			break;
		case 'previous_emplment':
			$exp = new experience('previous_emplment', $empl_id, 'employee');
			$exp->show();
			break;
		case 'license':
			$exp = new license('license', $empl_id, 'employee');
			$exp->show();
			break; 
		case 'leave':
			empl_leave_data($empl_id); 
			break;
		case 'payroll':			
			empl_payroll_data($empl_id); 
			break;
		case 'increments':
			empl_increment_data($empl_id); 
			break;
		case 'attachments':
			$_GET['empl_id'] = $empl_id;
			$_GET['page_level'] = 1;
			include_once($path_to_root."/modules/ExtendedHRM/manage/attachments.php"); 
			break;
		case 'family':
			$_GET['empl_id'] = $empl_id;
			$_GET['page_level'] = 1;
			include_once($path_to_root."/modules/ExtendedHRM/manage/family_data.php"); 
			break;
		case 'termination':
			//display_error($empl_id.'--');
			empl_termination($empl_id); 
			break;
		case 'note':
			empl_notes($empl_id); 
			break;
		//case 'history':
			//empl_life_to_date_history($empl_id); 
			//break;
	}
br();
tabbed_content_end();
div_end();
end_form();
//echo "<script type='text/javascript'> kvcodes_crm_nicEditor();  </script> \n";
end_page(@$_REQUEST['popup']);
function UploadHandle($empl_id){
		if (isset($_FILES['kv_attach_name']) && $_FILES['kv_attach_name']['name'] != '') {
			$max_image_size = 5000;
			$result = $_FILES['kv_attach_name']['error'];
			$upload_file = 'Yes'; 
			$attr_dir = company_path().'/attachments' ; 
			if (!file_exists($attr_dir)){				
				mkdir($attr_dir);
			}
			$dir = $attr_dir.'/empldocs/'.$empl_id.'/';
			if (!file_exists($dir)){
				mkdir($dir);
			}	
			/*$doc_ext = substr(trim($_FILES['kv_attach_name']['name']), strlen($_FILES['kv_attach_name']['name'])-3); 
			if($doc_ext == 'ocx' ) {
				$doc_ext = substr(trim($_FILES['kv_attach_name']['name']),strlen($_FILES['kv_attach_name']['name'])-4); 
			}*/
			$filename = basename($_FILES['kv_attach_name']['name']);
			$tmp = explode('.', $filename);
			$ext = strtolower(end($tmp));
			
			if(in_array($ext, array('docx','doc', 'pdf', 'jpg', 'jpeg', 'gif', 'png', 'bmp', 'rtf', 'txt'))){	
				
				$filesize = $_FILES['kv_attach_name']['size'];
				$filetype = $_FILES['kv_attach_name']['type'];
				
				$unique_name = $empl_id.'-'.$filename;

				if ( $filesize > ($max_image_size * 1024)) { //File Size Check
					display_warning(trans('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $max_image_size);
					$upload_file ='No';
				}elseif (file_exists($dir."/".$unique_name)){
					$result = unlink($dir."/".$unique_name);
					if (!$result) 	{
						display_error(trans('The existing Bill could not be removed'));
						$upload_file ='No';
					}
				}else {
					$attach = GetRow('kv_empl_job', array('empl_id' => $empl_id));
					$attr_dir = company_path().'/attachments/empldocs/'.$attach['empl_id'].'/'.$attach['bond_doc']; 
					if($attach['bond_doc'] && file_exists($attr_dir) && !is_dir($attr_dir))
						unlink($attr_dir);
				}
					
				if ($upload_file == 'Yes'){
					$result = move_uploaded_file($_FILES['kv_attach_name']['tmp_name'], $dir."/".$unique_name);			
				}
				Update('kv_empl_job', array('empl_id' => $empl_id), array( 'bond_doc' => $unique_name));
			} else 
				display_error(trans("The Selected File format is not supported, try files within this format (.jpg, png, doc,docx, rtf,pdf)"));
		}
	}


?>
<style>
#empl_profile_pic { border: 1px solid rgba(128, 128, 128, 0.68);    border-radius: 2px;}
td#kv_gross_amt {	color: #FF9800;    /*background-color: #f9f2bb; */ }
td#kv_ctc_amt{	color: #9C27B0;    /*background-color: rgba(156, 39, 176, 0.23); */ }
td#kv_net_amt{	color: #107B0F;   /* background-color: #B7DBC1; */}
td#kv_ded_amt{	color: #107B0F;   /* background-color: #f55; */}
ul.ajaxtabs li button {      padding: 3px 8px; } 
table { width: 100%; }
table.tablestyle_noborder {
    width:50%;
}
input#_empl_id_edit {
    display: none;
}
</style>

<link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
      rel = "stylesheet">
<script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script>
 const formatter = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'AED',
        minimumFractionDigits: 2
});





var total_value = [];
var test= [];

 $(document).on('change','.amount', function (){
    var textname=$(this).attr('name');
    var repl_txt=textname.replace("amt_", "");
    var hdn=$("#hdnTota_salary").val();
    if(hdn!='')
    {

        $("#lblTotalSalary").html(formatter.format(calculateSum(repl_txt)));
    }
    else
    {
        total_value[repl_txt]=$(this).val();
        $("#lblTotalSalary").html(formatter.format(ArrayNumberSum(total_value)));
    }

});

function ArrayNumberSum(total_value)
{
    var s='';
    for (i = 0; i < total_value.length; i += 1)
    {
        if(isNaN(total_value[i])) {
            continue;
        }
        s= Number(total_value[i])+Number(s);
    }
    return s;
}

function calculateSum(repl_txt)
{
    var totalsum='0';
    var ded_or_incr='';
    var sum_total= [];
    $(".amount").each(function(index, box) {
        var repl=$(this).val();
        var repl_number=repl.replace(",","");


            if($(this).attr('alt_type')=='2')
            {
                ded_or_incr=-repl_number;
            }
            else {
                ded_or_incr=repl_number;
            }
            sum_total.push(ded_or_incr);


    });


    return ArrayNumberSum(sum_total);
}


 function ArrayNumberSum(total_value)
 {
     var s='';
     for (i = 0; i < total_value.length; i += 1)
     {
         if(isNaN(total_value[i])) {
             continue;
         }
         s= Number(total_value[i])+Number(s);
     }
     return s;
 }


/*$(document).ready(function()
{
$("#datepicker-14").datepicker();
$("#datepicker-13").datepicker();
$("#datepicker-15").datepicker();
$("#datepicker-16").datepicker();
});*/



 /*$(document).on('click','#datepicker-15', function () {
     $('#datepicker-15').datepicker({ dateFormat: 'dd/mm/yy',changeYear: true,changeMonth: true });
     $("#datepicker-15").datepicker();
 });

 $(document).on('click','#datepicker-16', function () {
     $('#datepicker-16').datepicker({ dateFormat: 'dd/mm/yy',changeYear: true,changeMonth: true  });
     $("#datepicker-16").datepicker();
 });*/




 $(document).on('click','#datepicker-14', function () {
     /* $('#datepicker-13').datepicker({ dateFormat: 'dd/mm/yy',changeYear: true,changeMonth: true  });
      $("#datepicker-13").datepicker();*/

      /*$('#datepicker-14').datepicker({ dateFormat: 'dd/mm/yy',changeYear: true ,changeMonth: true });
      $("#datepicker-14").datepicker();*/
      

 });

 /*$(document).on('click','#datepicker-13', function () {
      $('#datepicker-13').datepicker({ dateFormat: 'dd/mm/yy',changeYear: true,changeMonth: true  });
      $("#datepicker-13").datepicker();
 });*/



 $(document).on('change','#empl_id', function () {
      if($(this).val()=='')
      {
          location.reload();
      }
 });


 $(document).on('change','#desig_group', function () {


 });

/*alert($('#hdn_payroll_account_cde').val());
 $('#payroll_account_code').val($('#hdn_payroll_account_cde').val());
 $('#axispro_subledger_code').val($('#hdn_payroll_account_cde').val());
 $('#advance_account_code').val($('#hdn_payroll_account_cde').val());
 $('#advance_subledger_account_code').val($('#hdn_payroll_account_cde').val());
 $('#loan_account_code').val($('#hdn_payroll_account_cde').val());
 $('#loan_subledger_account_code').val($('#hdn_payroll_account_cde').val());*/





 function isDate(txtDate) {
     var currVal = txtDate;

     //alert(currVal);
     if (currVal == '')
         return false;
     var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
     var dtArray = currVal.match(rxDatePattern);

     if (dtArray == null)
         return false;

     dtMonth = dtArray[1];
     dtDay = dtArray[3];
     dtYear = dtArray[5];

     if (dtMonth < 1 || dtMonth > 12)
         return false;
     else if (dtDay < 1 || dtDay > 31)
         return false;
     else if ((dtMonth == 4 || dtMonth == 6 || dtMonth == 9 || dtMonth == 11) && dtDay == 31)
         return false;
     else if (dtMonth == 2) {
         var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
         if (dtDay > 29 || (dtDay == 29 && !isleap))
             return false;
     }
     return true;
 }


 function vali()
 {
     var s = document.getElementById("getDate").value;

     if (isDate(s) == false) {
         alert('');

     } else {
         document.getElementById("getDate").value = s;

     }
 }

 $(document).on('change', '#empl_payroll_accounts', function() {
     if(this.checked) {
         $('#ShowOrHideEmployeeParyollOpt').show();
          $('#hdn_Emp_pay_option').val('1');
     }
     else
     {
         $('#ShowOrHideEmployeeParyollOpt').hide();
         $('#hdn_Emp_pay_option').val('0');
     }
 });


 /*$(document).ready(function() {
     $("#payroll_account_code").change(function() {
         var clicked = $(this)
             .find('option:selected') // get selected option
             .parent()   // get that option's optgroup
             .attr("label");   // get optgroup's label
         alert( clicked );
     });
 });
*/



</script>
