<?php 
/****************************************
/*  Author  : Kvvaradha
/*  Module  : Extended HRM
/*  E-mail  : admin@kvcodes.com
/*  Version : 1.0
/*  Http    : www.kvcodes.com
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
 
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
global $hrm_empl_salutation, $kv_empl_gender, $hrm_empl_marital, $hrm_empl_desig_group, $hrm_empl_status; 


simple_page_mode(true);
//------------------------------------------------------------------------------------
page(trans("CSV Employee Profile Bulk Import"));

//------------------------------------------------------------------------------------
if (isset($_POST['submitcsv'])){

	//$content = base64_encode(file_get_contents($_FILES['filename']['tmp_name']));
	$tmpname = $_FILES['filename']['tmp_name'];
	if(pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION) == 'csv'){
		$dir =  dirname(dirname(__FILE__)).'/backups/tmp';
		if (!file_exists($dir))	{
			mkdir ($dir,0777);
			$index_file = "<?php\nheader(\"Location: ../index.php\");\n";
			$fp = fopen($dir."/index.php", "w");
			fwrite($fp, $index_file);
			fclose($fp);
		}

		$filename = basename($_FILES['filename']['name']);
				
		//save the file
		move_uploaded_file($tmpname, $dir."/".$filename);
	
		//$arrResult  = array();
		$handle     = fopen($dir.'/'.$filename, "r");
		if(empty($handle) === false) {
			$flag = true;
			$id_exsit = $email_exist = 0; 

		    while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
		    	if($flag) { $flag = false; $header = $data; continue; }
		    	elseif(db_has_selected_employee($data[0])){
		    		$id_exsit++;
		    	}elseif($data[11] != '' && db_has_employee_email($data[11])){
		    		$email_exist++;
		    	}else{		    		
		    		add_employee($data[0],
			 			 (in_array($data[1], $hrm_empl_salutation) ? array_search ($data[1], $hrm_empl_salutation) : 1),
			 			 $data[2],
			 			 $data[3],
			 			 $data[4],
			 			 $data[5],
			 			 $data[9], 
			 			 $data[10],
			 			 $data[11], 
			 			 (in_array($data[20], $kv_empl_gender) ? array_search ($data[20], $kv_empl_gender) : 1), 
			 			 sql2date(date('Y-m-d', strtotime($data[21]))),
			 			 (is_numeric($data[22]) ? $data[22] : date_diff(date_create($data[21]), date_create('today'))->y),  
			 			(in_array($data[23], $hrm_empl_marital) ? array_search ($data[23], $hrm_empl_marital) : 1), 
			 			 (in_array($data[19], $hrm_empl_status) ? array_search($data[19], $hrm_empl_status) : 1), 
			 			 $data[6],
			 			 $data[7],
			 			 (($data[8]) ?(Insert_country_of_Get_existing_id($data[8])): ' ')); 

		    		$jobs_arr =  array('empl_id' => $data[0],
							 'grade' => $data[12],
							 'department' => (($data[13]) ? (Insert_dept_of_Get_existing_id($data[13])) : 1),
							 'desig_group' => (in_array($data[14], $hrm_empl_desig_group) ? array_search ($data[14], $hrm_empl_desig_group) : 1),
							 'desig' => $data[15] ,
							 'joining' => array(sql2date(date('Y-m-d', strtotime($data[16]))), 'date'), 
							 'empl_type' =>  (($data[17]) ? (Insert_pick_of_Get_existing_id($data[17], 1)): 1), 
							 'working_branch' => (($data[18]) ? (Insert_pick_of_Get_existing_id($data[18], 2)): 1),  //Working Place
						 	 'mod_of_pay' => (($data[24]) ? (Insert_pick_of_Get_existing_id($data[24],3)): 1),  //'mod_of_pay'
							 'bank_name' => $data[25], //'bank_name'
							 'swift_code' => $data[27],  //swift_code
							 'acc_no' => $data[26]); //acc_no

					$Allowance = kv_get_empl_allowance();
					$gross_Earnings = 0 ;
					$allowances_ar =  array(); 
					foreach($Allowance as $single) {	
						$account_code = array_search($single['account_code'], $header); 
						if($account_code){
							$allowances_ar[$single['account_code']] = $data[$account_code];
							if($single['type'] == 'Earnings' && $single['esb'] != 1 && $single['ot_other'] != 1 && $single['air_ticket'] != 1)
							$gross_Earnings += (isset($data[$account_code]) ? $data[$account_code] : 0);
						}
						
					}
					$jobs_arr['gross'] = $gross_Earnings;
					$jobs_arr['allowances'] = $allowances_ar;
					$jobs_arr['gross_pay_annum'] = $gross_Earnings*12;
					Insert('kv_empl_job', $jobs_arr);
		    	}
		        //print_r($data);
		    }
		    fclose($handle);
		}
		//print_r($arrResult);
		unlink($dir.'/'.$filename);
		$note = '';
		if($id_exsit > 0){
			$note .= ' And '.$id_exsit.' Existing user Id in it.';
		}elseif($email_exist >0 )
			$note .= ' And '.$email_exist.' Emails Exist.';
		display_notification(trans("Successfully Imported all Employee Profiles".$note));
	}else{
		display_error("Please Select a Valid CSV File to import Employee data.");
	}
	//$Mode = 'RESET';
}

echo '<center><b> Note: </b> You need to use this format to import Employee Profile details <a href="'.$path_to_root.'/modules/ExtendedHRM/import.csv" > Download CSV format </a></center>.<br><hr><br>
	<center> <p style="text-align: justify; max-width:400px; width:100%;"> From the given CSV, you can add Employee Profile informations. After that you need to add your employee allowances after end of the column and add allowance amount for each employee. If you left this as blank, the amount will be stored as 0. </p> 
	<p style="text-align: justify; max-width:400px; width:100%;"> And also the Date format should be `YYYY-MM-DD` for  both Date of join and Birthdate.</p></center>';
start_form(true);
	start_table(TABLESTYLE, "width=30%");
	file_row("Select CSV to Import", "filename", null);

	end_table();
	br();
	submit_center('submitcsv', 'Import CSV');
end_form();
end_page();

?>