<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
/*  Payslip PDF
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
    $empl_id = (isset($_POST['PARAM_2']) ? $_POST['PARAM_2'] : (isset($_GET['PARAM_2']) ? $_GET['PARAM_2'] : 0));
	$comment = (isset($_POST['PARAM_3']) ? $_POST['PARAM_3'] : (isset($_GET['PARAM_3']) ? $_GET['PARAM_3'] : ''));;
    //$destination = (isset($_POST['PARAM_4']) ? $_POST['PARAM_4'] : (isset($_GET['PARAM_4']) ? $_GET['PARAM_4'] : ''));
    $_POST['REP_ID'] = 802; 

if(db_has_employee_payslip($empl_id, $month, $year)){
//	if ($destination)
	//	include_once($path_to_root . "/reporting/includes/excel_report.inc");
	//else
		include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

	$orientation = 'P';
	$dec = user_price_dec();
	
	$cols = array(10,215,290,480);	
	
	$headers = array(trans("Earnings"), trans(""), trans("Deductions"), trans(""));

	$aligns = array('left',	'right', 'left', 'right');

    $rep = new FrontReport(trans("Payslip"), "Payslip", user_pagesize(), 9, $orientation);

	function get_empl_sal_details_file($empl_id, $month, $year){
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_salary	WHERE empl_id=".db_escape($empl_id)." AND month=".db_escape($month)." AND year=".db_escape($year);

		return db_query($sql,"No transactions were returned");
	}
	
    $result = get_empl_sal_details_file($empl_id, $month, $year);	
	
	if ($myrow = db_fetch($result))	{
		$name_and_dept = get_empl_name_dept($myrow['empl_id']);
		$kyc = GetRow('kv_empl_job',array('empl_id' => $myrow['empl_id']));
		//$dept_id=GetSingleValue('kv_empl_job','department' ,array('empl_id'=>$myrow['empl_id']));
		$dept_name=GetSingleValue('kv_empl_departments','description' ,array('id'=>$kyc['department']));
		$employee_info = array(
					'id' => $myrow['id'],
					'empl_id' => $myrow['empl_id'],
					'empl_name' => $name_and_dept['name'],
					'department' => $dept_name,
					'desig' => kv_get_empl_desig($myrow['empl_id']),
					'joining' => sql2date(get_employee_join_date($myrow['empl_id'])),
					'month' => $month,
					'ESIC' => $kyc['ESIC'],										
					'PF' => $kyc['PF'],										
					'PAN' => $kyc['PAN'],					
					'year' => $year );

			$baccount = get_empl_bank_acc_details($myrow['empl_id']);
			$leaves =  GetRow('kv_empl_attendancee', array('empl_id '=> $empl_id, 'month' => $month, 'year' => $year), false);			
			$AL = $SL = $ML = 0 ;
			for($kv= 1; $kv<=31;$kv++ ){
				if($leaves[$kv] == 'AL')
					$AL++;
				if($leaves[$kv] == 'SL')
					$SL++;
				if($leaves[$kv] == 'ML')
					$ML++;
			}
			$rep->SetHeaderType('Header2');	
		    $rep->Font();
		    $rep->Info(null, $cols, $headers, $aligns);
		    //display_error($AL.'-'.$SL.'-'.$ML);
		   //display_error(json_encode($myrow)."ygukgygug".json_encode($employee_info));

		    $contacts = array( 
		    			'email' => $name_and_dept['email'],
		    			'name2'	=>	null,
		    			'name'  => 	$name_and_dept['name'],
		    			'lang'	=> null,
		    		);

		    $rep->SetCommonData($employee_info, $baccount, array( $contacts),  'payslip');
		    $rep->NewPage();
		    $rep->Font('bold');
			$rep->TextCol(1, 2,	trans("Amount"), -34,-91);
			$rep->TextCol(3, 4,	trans("Amount"), -15,-91);
			$rep->Font();  
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
			//	elseif($single['type'] == 'Employer Contribution' )
				//	$earallows[] =  $single;
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
				}elseif($ot_earnings){
					$rep->Text($text_value+10, trans("OT"),0,0,$Value);
					$rep->TextCol(1,2, number_format2($myrow['ot_earnings'], $dec), -25,-70);
					$ot_earnings = 0;
				}
				if(isset($dedallows[$vj])){
					$rep->Text(330, $dedallows[$vj]['description'],0,0,$Value);
					$rep->TextCol(3,4,  number_format2($myrow[$dedallows[$vj]['id']], $dec), -5,-70);
					$total_deduction += $myrow[$dedallows[$vj]['id']];
				}/*elseif($else_deduct==0){
					$rep->Text(330, trans("Loan Amount"),0,0,$Value);
					$rep->TextCol(3,4,  number_format2($myrow['loan'], $dec), -5,-70);
					$total_deduction += $myrow['loan'];
					$else_deduct++;
				}*/elseif($else_deduct==1){
					$rep->Text(330, trans("LOP Amount"),0,0,$Value);
					$rep->TextCol(3,4,  number_format2($myrow['lop_amount'], $dec), -5,-70);
					$total_deduction += $myrow['lop_amount'];
					$else_deduct++;
				}				
				//$rep->Line($line_value, 0.00001,0,0);
				$rep->NewLine(2);
				$Value++;				
			}

			
			$Value++;
			
			/*$rep->Text(330, trans("Misc"),0,0,$Value);
			$rep->TextCol(3,4,  number_format2($myrow['misc'], $dec), -5,-70);
			$total_deduction += $myrow['misc'];
			
			$rep->NewLine(2);
			$rep->Text($text_value+10, trans("Other Allowance"),0,0,$Value);
			$rep->TextCol(1,2, number_format2($myrow['ot_other_allowance'], $dec), -25,-70);
			
		//	$rep->Line($line_value-125, 0.00001,0,0);
			$rep->NewLine(2);
			$Value++;
			if($else_deduct == 3){
				$rep->Text($text_value, ' ',0,0,-$Value);
				$rep->Text(250,  ' ',0,0,$Value);
				$rep->Text(330, trans("Loan Amount"),0,0,$Value);
				$rep->TextCol(3,4,  number_format2($myrow['loan'], $dec), -5,-70);
				$total_deduction += $myrow['loan'];
			//	$rep->Line($line_value-125, 0.00001,0,0);
				$rep->NewLine(2);
				$Value++;

				$rep->Text($text_value, '',0,0,$Value);
				$rep->Text(250, '',0,0,-65);
				$rep->Text(330, trans("LOP Amount"),0,0,$Value);
				$rep->TextCol(3,4,  number_format2($myrow['lop_amount'], $dec), -5,-70);
				$total_deduction += $myrow['lop_amount'];
			//	$rep->Line($line_value-125, 0.00001,0,0);
				$rep->NewLine(2);
				$Value++;
			}
			$rep->row = 200;
			
			$rep->Font('bold');
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
			$rep->Line(205, 0.00001,0,0);	*/
			/* Gross pay*/
			$rep->SetTextColor(255, 152, 0);
			$rep->Text($text_value, trans("Gross Pay(Total Earnings)"),0,0,-70);
			$rep->TextCol(1,2, number_format2($myrow['gross'], $dec), -25,-70);
			$rep->SetTextColor(203, 0, 0);
			$rep->Text(330, trans("Total Deduction"),0,0,-70);
			$rep->TextCol(3,4,  number_format2($total_deduction, $dec), -5,-70);
			$rep->NewLine(1);
			$rep->SetTextColor(0, 0, 0);		
			
			$rep->Line(165, 0.00001,0,0);	
			$rep->SetTextColor(16, 123, 15);
			$rep->Text($text_value, trans("Net Amount ( Total Earnings - Total Deduction)"),0,0,-40);
			$rep->TextCol(3,4,  number_format2($myrow['net_pay'], $dec), -5,-40);			
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
	if(isset($_GET['email']) && $_GET['email']== 'yes')
		$rep->End(1, 'Payslip ');
	else
		$rep->End();
}else{
	display_warning(trans("No Payroll Entry Found For Selected Period."));
}
?>