<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Enhanced HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
/*  Leave Encashment
*****************************************/
$page_security = 'SA_OPEN';

$path_to_root="../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

global $path_to_root, $systypes_array, $kv_empl_gender;

	if(isset($_GET['rep_v'])){
		include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
	}
    $empl_id = (isset($_POST['PARAM_0']) ? $_POST['PARAM_0'] : (isset($_GET['PARAM_0']) ? $_GET['PARAM_0'] : 1));
    $year = (isset($_POST['PARAM_1']) ? $_POST['PARAM_1'] : (isset($_GET['PARAM_1']) ? $_GET['PARAM_1'] : 01));    
	$comment = (isset($_POST['PARAM_2']) ? $_POST['PARAM_2'] : (isset($_GET['PARAM_2']) ? $_GET['PARAM_2'] : ''));
	$_POST['REP_ID'] = 811; 
	$dec = user_price_dec();

	$myrow=GetRow('kv_empl_leave_encashment', array('empl_id' => $empl_id, 'year' => $year));

	if(is_array($myrow) && $myrow['carry_forward'] == 0){

		include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

		$orientation = 'P';

		$cols = array(15,200, 480);
		
		$headers = array(trans("Allowances"),  trans(" "));

		$aligns = array('left',	'right');

		//$Allowance = kv_get_allowances('Earnings');
		$selected_alowances = array();
		if(isset($myrow['allowances']) && $myrow['allowances'] != ''){
						//display_error($sal_row['allowances']);
			$allowances = unserialize(base64_decode($myrow['allowances']));
			unset($myrow['allowances']);
			foreach($allowances as $key => $value){
				$selected_alowances[$key] = $value;
			}
		}

	    $rep = new FrontReport(trans("Leave Encashment"), "Leave Encashment", 'A4', 9, $orientation);
		
		$name_and_dept = get_empl_name_dept($myrow['empl_id']);
		$kyc = GetRow('kv_empl_job',array('empl_id' => $myrow['empl_id']));
			$employee_info = array(
					'empl_id' => $myrow['empl_id'],
					'empl_name' => $name_and_dept['name'],
					'department' => GetSingleValue('kv_empl_departments', 'description', array('id' => $name_and_dept ['deptment'])),
					'desig' => GetSingleValue('kv_empl_designation', 'description', array('id' => $kyc['desig'])),
					'joining' => sql2date(get_employee_join_date($myrow['empl_id'])),
					'month' => '',										
					'ESIC' => $kyc['ESIC'],										
					'PF' => $kyc['PF'],										
					'PAN' => $kyc['PAN'],
					'year' => $year );

			$baccount = get_empl_bank_acc_details($myrow['empl_id']);
			
			$rep->SetHeaderType('Header2');	
		    $rep->Font();
		    $rep->Info(null, $cols, $headers, $aligns);
		    		  
		    $contacts = array( 
		    			'email' => $name_and_dept['email'],
		    			'name2'	=>	null,
		    			'name'  => 	$name_and_dept['name'],
		    			'lang'	=> null,
		    		);
		    $employee_info['id'] = $myrow['id'];
		    $rep->SetCommonData($employee_info, $baccount, array( $contacts),  'annual_encashment');
		    $rep->NewPage();
		    $rep->Font('bold');
			//$rep->TextCol(1, 2,	trans("Amount"), -34,-91);
			$rep->TextCol(1, 2,	trans("Amount"), -15,-91);
			$rep->Font();  
			$rep->NewLine();		

			$text_value=40;
			$line_value=670;			
			$Value = -70;			

			foreach( $selected_alowances as $key => $value ){
				
				$rep->Text($text_value+10, GetSingleValue('kv_empl_allowances', 'description', array('id' => $key)),0,0,$Value);
				$rep->TextCol(1,2, number_format2($value, $dec), -25,-70);				
				$rep->NewLine(2);
				$Value++;
			}

			$rep->row = 200;						
			$rep->Text(330, trans("Annual Leave"),0,0,$Value);
			$rep->Text(420, $myrow['payable_days'],0,0,-70);			
						
			$rep->Line(165, 0.00001,0,0);	
			$rep->SetTextColor(16, 123, 15);
			$rep->NewLine(8);
			$rep->Text($text_value+10, 'Net Encashment Amount',0,0,-40);
			$rep->TextCol(1,2,  number_format2($myrow['amount'], $dec), -5,-40);			
			$rep->NewLine(1);

			$rep->Line(135, 0.00001,0,0);
			$rep->Line($line_value-585, 0.00001,0,0);	
			$rep->row = 180;	
			if($comment){				
				$rep->SetTextColor(0, 0, 0);
				$rep->Text($text_value, 'Comments',0,0,65);
				$rep->Text(200, $comment,0,0,65);  //$rep->NewLine(2);	
			}		
			$rep->Line($line_value-635, 0.00001,0,0);
	 			
	if ($rep->row < $rep->bottomMargin )
		$rep->NewPage();	
	$rep->End(); //1, 'Payslip ');
} elseif(is_array($myrow) && $myrow['carry_forward'] == 1)
	display_warning("The Leave Encashment Carry Forwarded to Next year");
else
	display_warning("No Encashment Data found for the selected Employee.");

?>