<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
/*  Payslips bulk PDF
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
    $year = (isset($_POST['PARAM_0']) ? $_POST['PARAM_0'] : (isset($_GET['PARAM_0']) ? $_GET['PARAM_0'] : 1));
    $month = (isset($_POST['PARAM_1']) ? $_POST['PARAM_1'] : (isset($_GET['PARAM_1']) ? $_GET['PARAM_1'] : 01));
    $dep_id = (isset($_POST['PARAM_2']) ? $_POST['PARAM_2'] : (isset($_GET['PARAM_2']) ? $_GET['PARAM_2'] : 0));
	$comment = (isset($_POST['PARAM_3']) ? $_POST['PARAM_3'] : (isset($_GET['PARAM_3']) ? $_GET['PARAM_3'] :0));
	//$comment = (isset($_POST['PARAM_4']) ? $_POST['PARAM_4'] : (isset($_GET['PARAM_4']) ? $_GET['PARAM_4'] : ''));
	$_POST['REP_ID'] = 809; 
//display_error("sergsre");

function get_dept_empl_sal_details_file($dep_id, $month, $year){
	
	$get_employees_list = get_empl_ids_from_dept_id($dep_id);
	
	//$empls_id=array_map('intval', explode(',', implode($get_employees_list)));
	$empls_id = implode(',', array_map('intval', $get_employees_list));
	//display_error($empls_id);
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE empl_id IN (".$empls_id.") AND month=".db_escape($month)." AND year=".db_escape($year)." GROUP BY empl_id" ;
	//display_error($sql);
	return db_query($sql,"No transactions were returned");
}


	
//	$all_tax_values =  GetRow('kv_empl_taxes', array('empl_id' => $empl_id, 'month' => $month, 'year' => $year));
	
    $result = get_dept_empl_sal_details_file($dep_id, $month, $year);	
	$exists = (db_num_rows($result))?TRUE:FALSE;
	if($exists){

		include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

		$orientation = 'P';
		
		$dec = user_price_dec();

		$cols = array(10,215,290,470);	

		$aligns = array('left',	'right', 'left', 'right');
		$name_and_dept = GetSingleValue('kv_empl_departments', 'description', array('id' => $dep_id));

	    $rep = new FrontReport(trans("Payslip"), "Payslip", 'A4', 9, $orientation);
		while ($myrow = db_fetch_assoc($result))	{
						
			$headers = array(trans("Earnings"), trans(" "), trans("Deductions"), trans(" "));
			$kyc = GetRow('kv_empl_job',array('empl_id' => $myrow['empl_id']));	
			$employee_details = GetRow('kv_empl_info', array('empl_id' => $myrow['empl_id']));
			$dept_id=GetSingleValue('kv_empl_job','department' ,array('empl_id'=>$myrow['empl_id']));
			$dept_name=GetSingleValue('kv_empl_departments','description' ,array('id'=>$dept_id));
			$employee_info = array(
					'id' => $myrow['id'],
					'empl_id' => $myrow['empl_id'],
					'empl_name' => kv_get_empl_name($myrow['empl_id'], true),
					'department' => $dept_name,
					'desig' => GetSingleValue('kv_empl_designation', 'description', array('id' => $kyc['desig'])),
					'joining' => sql2date(get_employee_join_date($myrow['empl_id'])),
					'curr' => '', //$empl_curr,
					'month' => $month,
					'ESIC' => $kyc['ESIC'],										
					'PF' => $kyc['PF'],										
					'PAN' => $kyc['PAN'],
					'year' => $year );

			$baccount = get_empl_bank_acc_details($myrow['empl_id']);
			
			$rep->SetHeaderType('Header2');	
		    $rep->Font();
		    $rep->Info(null, $cols, $headers, $aligns);
		    $contacts = array( 
		    			'email' => $employee_details['email'],
		    			'name2'	=>	$employee_details['empl_lastname'],
		    			'name'  => 	$employee_details['empl_firstname'],
		    			'lang'	=> null,
		    		);

		    $rep->SetCommonData($employee_info, $baccount, array( $contacts),  'payslip');
		    $rep->NewPage();
			//header text
			$rep->Font('bold');
			$rep->TextCol(1, 2,	trans("Amount"), -34,-91);
			$rep->TextCol(3, 4,	trans("Amount"), -10,-91);
			$rep->Font();
			$leaves =  GetRow('kv_empl_attendancee', array('empl_id '=> $myrow['empl_id'], 'month' => $month, 'year' => $year), false);	
			$AL = $SL = $ML = 0 ;
			for($kv= 1; $kv<=31;$kv++ ){
				if($leaves[$kv] == 'AL')
					$AL++;
				if($leaves[$kv] == 'SL')
					$SL++;
				if($leaves[$kv] == 'ML')
					$ML++;
			}

			$rep->NewLine();		

			$text_value=40;
			$line_value=670;
			$earallows = $dedallows = array();
			$Allowance = kv_get_allowances(null, 0, $kyc['grade']);
			foreach($Allowance as $single){
				if($single['type'] == 'Earnings' )
					$earallows[] = $single;
				elseif($single['type'] == 'Reimbursement' )
					$earallows[] =  $single;
				//elseif($single['type'] == 'Employer Contribution' )
				//	$earallows[] =  $single;
				elseif($single['type'] == 'Deductions' )
					$dedallows[] =  $single;
			}
			$earnings_count = get_allowances_count('Earnings')+get_allowances_count('Reimbursement')+get_allowances_count('Employer Contribution') ;
			$deductions_count = get_allowances_count('Deductions');

			//display_error(json_encode($dedallows));
			if($earnings_count > $deductions_count){
				$count_final  = $earnings_count;
			}else{
				$count_final = $deductions_count;
			}
			//display_error($count_final);
			$Value = -70;
			$total_deduction = 0;
			$count_difference = $count_final- $deductions_count;
			if($count_difference >= 2)
				$else_deduct = 0;
			else
				$else_deduct = 3;
			$ot_earnings = 1;
			for($vj=0; $vj<$count_final;$vj++){
				if(isset($earallows[$vj])){
					$rep->Text($text_value+10, $earallows[$vj]['description'],0,0,$Value);
					$rep->TextCol(1,2, number_format2($myrow[$earallows[$vj]['id']], $dec), -25,-70);
					//$rep->Text(250, number_format2($myrow[$earallows[$vj]['id']], $dec),0,0,$Value);
				}elseif($ot_earnings){
					$rep->Text($text_value+10, trans("OT"),0,0,$Value);
					$rep->TextCol(1,2, number_format2($myrow['ot_earnings'], $dec), -25,-70);
					//$rep->Text(230, number_format2($myrow['ot_earnings'], $dec),0,0,$Value);
					$ot_earnings = 0;
				}
				if(isset($dedallows[$vj])){
					$rep->Text(320, $dedallows[$vj]['description'],0,0,$Value);
					$rep->TextCol(3,4,  number_format2($myrow[$dedallows[$vj]['id']], $dec), -5,-70);
					//$rep->Text(530, number_format2($myrow[$dedallows[$vj]['id']], $dec),0,0,$Value);
					$total_deduction += $myrow[$dedallows[$vj]['id']];
				}/*&elseif($else_deduct==0){
					$rep->Text(320, trans("Loan Amount"),0,0,$Value);
					//$rep->Text(530, number_format2($myrow['loan'],$dec),0,0,$Value);
					$rep->TextCol(3,4,  number_format2($myrow['lop_amount'], $dec), -5,-70);
					$total_deduction += $myrow['loan'];
					$else_deduct++;
				}*/elseif($else_deduct==1){
					$rep->Text(320, trans("Absent Deduction"),0,0,$Value);
					//$rep->Text(530, number_format2($myrow['lop_amount'], $dec),0,0,$Value);
					$rep->TextCol(3,4,  number_format2($myrow['lop_amount'], $dec), -5,-70);
					$total_deduction += $myrow['lop_amount'];
					$else_deduct++;
				}
				
				//$rep->Line($line_value, 0.00001,0,0);
				$rep->NewLine(2);
				$Value++;
			}
			
			
			/*$Value++;
			$rep->Text(330, trans("Misc"),0,0,$Value);
			$rep->Text(530, $myrow['misc'],0,0,$Value);
			$total_deduction += $myrow['misc'];
			
			$rep->NewLine(2);
			$rep->Text($text_value+10, trans("Other Allowance"),0,0,$Value);
			$rep->Text(230, $myrow['ot_other_allowance'],0,0,$Value);
			
		//	$rep->Line($line_value-125, 0.00001,0,0);
			$rep->NewLine(2);
			$Value++;
			if($else_deduct == 3){
				$rep->Text($text_value, ' ',0,0,-$Value);
				$rep->Text(230,  ' ',0,0,$Value);
				$rep->Text(330, trans("Loan Amount"),0,0,$Value);
				$rep->Text(530, $myrow['loan'],0,0,$Value);
				$total_deduction += $myrow['loan'];
			//	$rep->Line($line_value-125, 0.00001,0,0);
				$rep->NewLine(2);
				$Value++;

				$rep->Text($text_value, '',0,0,$Value);
				$rep->Text(230, '',0,0,-65);
				$rep->Text(330, trans("Absent Deduction"),0,0,$Value);
				$rep->Text(530, $myrow['lop_amount'],0,0,$Value);
				$total_deduction += $myrow['lop_amount'];
			//	$rep->Line($line_value-125, 0.00001,0,0);
				$rep->NewLine(2);
				$Value++;
			}
			*/
			$rep->row = 120;
			
			/*$rep->Font('bold');
			$rep->Text($text_value+10, trans("Taken Leave"),0,0,-70);
			$rep->Text(330, trans("Paid Leave"),0,0,$Value);
			$rep->Font();  
			$rep->NewLine(2);		
			
			$rep->Text($text_value+10, trans("Annual Leave"),0,0,-70);
			$rep->Text(150, $AL,0,0,-70);			
			$rep->Text(330, trans("Annual Leave"),0,0,$Value);
			$rep->Text(420, $myrow['AL'],0,0,-70);
			$rep->NewLine();
			
			$rep->Text($text_value+10, trans("Sick Leave"),0,0,-70);
			$rep->Text(150, $SL,0,0,-70);			
			$rep->Text(330, trans("Sick Leave"),0,0,$Value);
			$rep->Text(420, $myrow['ML'],0,0,-70);
			$rep->NewLine();
			
			$rep->Text($text_value+10, trans("General Leave"),0,0,-70);
			$rep->Text(150, $ML,0,0,-70);			
			$rep->Text(330, trans("General Leave"),0,0,$Value);
			$rep->Text(420, $myrow['GL'],0,0,-70);
			$rep->NewLine();
			$rep->row = 120;
			$rep->Line(205, 0.00001,0,0);	
			/* Gross pay*/
			$rep->SetTextColor(255, 152, 0);
			$rep->Text($text_value+10, trans("Gross Pay(Total Earnings)"),0,0,$Value);
			$rep->Text(230, number_format2($myrow['gross'], $dec),0,0,$Value);
			$rep->SetTextColor(203, 0, 0);
			$rep->Text(320, trans("Total Deduction"),0,0,$Value);
			$rep->Text(530, number_format2($total_deduction, $dec),0,0,$Value);
		//	$rep->Line($line_value-150, 0.00001,0,0);
			$rep->NewLine(1);
			$rep->SetTextColor(0, 0, 0);		
			
			/* $rep->Text($text_value, 'Advance Salary',0,0,1);
			$rep->Text(400, $myrow['adv_sal'],0,0,1);
			$rep->Line($line_value-225, 0.00001,0,0);
			$rep->NewLine(2);
			*/				
			$rep->Line(165, 0.00001,0,0);	
			$rep->SetTextColor(16, 123, 15);
			$rep->Text($text_value+10, trans("Net Amount ( Total Earnings - Total Deduction)"),0,0,-40);
			$rep->Text(530, number_format2($myrow['net_pay'], $dec),0,0,-40);			
			$rep->NewLine(1);

			$rep->Line(135, 0.00001,0,0);
			$rep->Line($line_value-585, 0.00001,0,0);	
			$rep->row = 180;	
			if($comment){				
				$rep->SetTextColor(0, 0, 0);
				$rep->Text($text_value, trans("Comments"),0,0,65);
				$rep->Text(200, $comment,0,0,65);  //$rep->NewLine(2);	
			}		
			$rep->Line($line_value-635, 0.00001,0,0);		 
	} 
			
	if ($rep->row < $rep->bottomMargin )
		$rep->NewPage();	
	$rep->End(); //1, 'Payslip ');
}else{
	display_warning("No Payroll Entry Found.");
}

?>
