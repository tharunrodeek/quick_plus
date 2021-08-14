<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
/*  Employee details  PDF
*****************************************/
$page_security = 'SA_OPEN';
$path_to_root="../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

global $path_to_root, $systypes_array, $print_as_quote, $print_invoice_no, $packing_slip, $dflt_lang,$hrm_empl_salutation,$hrm_empl_status,$kv_empl_gender,$hrm_empl_marital,$hrm_empl_bloog_group,$hrm_empl_desig_group,$hrm_empl_grade_list,$hrm_empl_type,$kv_empl_mop;
	if(isset($_GET['rep_v'])){
		include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
	}
    $year = (isset($_POST['PARAM_0']) ? $_POST['PARAM_0'] : (isset($_GET['PARAM_0']) ? $_GET['PARAM_0'] : 1));
    $empl_id = (isset($_POST['PARAM_1']) ? $_POST['PARAM_1'] : (isset($_GET['PARAM_1']) ? $_GET['PARAM_1'] : 01));
    $destination = (isset($_POST['PARAM_2']) ? $_POST['PARAM_2'] : (isset($_GET['PARAM_2']) ? $_GET['PARAM_2'] : 0));
    $_POST['REP_ID'] = 812; 

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

	$orientation = 'L';
	$cols = array();		
	$headers = array();
	$aligns = array();

    $rep = new FrontReport(trans("Employee Details"), "employeedetails", user_pagesize(), 9, $orientation);
	 
	$dec = user_price_dec();
	$name_and_dept = get_empl_name_dept($empl_id);		
	$kyc = GetRow('kv_empl_job',array('empl_id' => $empl_id));
	$employee_info = array(
					'id' => $empl_id,
					'empl_id' => $empl_id,
					'empl_name' => $name_and_dept['name'],
					'department' => GetSingleValue('kv_empl_departments', 'description', array('id' =>$name_and_dept ['deptment'])),
					'desig' => kv_get_empl_desig($empl_id),
					'joining' => sql2date(get_employee_join_date($empl_id)),			
					'year' => $year );

	$baccount = get_empl_bank_acc_details($empl_id);
	$rep->SetHeaderType('Header4');	
	$rep->Font();

	/*Employee personal details*/

	$rep->Info(null, $cols, $headers, $aligns);
	$contacts = array( 'email' => $name_and_dept['email'],
		    			'name2'	=>	null,
		    			'name'  => 	$name_and_dept['name'],
		    			'lang'	=> null,
		    		);
	$_POST['page_titl']=trans("Employee Profile");
	$rep->SetCommonData($employee_info, $baccount, array( $contacts),  'payslip');
	$rep->NewPage();	

	$rep->NewLine();	
		$empl_details = GetRow('kv_empl_info', array('empl_id' => $empl_id));
		$job_details = GetRow('kv_empl_job', array('empl_id' => $empl_id));	
		$empl_image = company_path() . "/images/empl/" .$empl_details['empl_pic'];
		//display_error($empl_image);
		$ccol=43;
		$icol = 0;
		$row_value=-164;
		$rep->Text($ccol+282, trans("Date of Joining"), $icol,0,$row_value);
		$rep->Text($ccol+378, sql2date($job_details['joining']), $icol,0,$row_value);
		$row_value=-157;
		if(file_exists($empl_image)){
			$rep->AddImage($empl_image, $ccol+530, $rep->row+100, 0, 125);
		}
		
		$rep->NewLine();
		$rep->Text($ccol-4, trans("Home Phone"), $icol,0,$row_value);
		$rep->Text($ccol+117, $empl_details['home_phone'], $icol,0,$row_value);
		$rep->Text($ccol+282, trans("Employment Type"), $icol,0,$row_value);
		$rep->Text($ccol+378, $hrm_empl_type[$job_details['empl_type']], $icol,0,$row_value);
		$row_value+=8;
		$rep->NewLine();
		$rep->Text($ccol-4, trans("Gender"), $icol,0,$row_value);
		$rep->Text($ccol+117, $kv_empl_gender[($empl_details['gender'] > 0 ? $empl_details['gender'] : 1)], $icol,0,$row_value);	
		$rep->Text($ccol+282, trans("Working Place"), $icol,0,$row_value);
		$working_place = GetSingleValue('workcentres', 'name', array('id'=> $job_details['working_branch']));		 
		$rep->Text($ccol+378, $working_place, $icol,0,$row_value);
		$row_value+=8;
		$rep->NewLine();
		$rep->Text($ccol-4, trans("Date of Birth"), $icol,0,$row_value);
		$rep->Text($ccol+117, $empl_details['date_of_birth'], $icol,0,$row_value);
		$rep->Text($ccol+282, trans("Mode of Pay"), $icol,0,$row_value);
		$rep->Text($ccol+378, $kv_empl_mop[$job_details['mod_of_pay']], $icol,0,$row_value);	
		$row_value+=8;
		$rep->NewLine();
		$rep->Text($ccol-4, trans("Age"), $icol,0,$row_value);
		$age = date_diff(date_create($empl_details['date_of_birth']), date_create('today'))->y;
		$rep->Text($ccol+117, $age, $icol,0,$row_value);	
		$rep->Text($ccol+282, trans("Bank Name"), $icol,0,$row_value);
		$rep->Text($ccol+378, $job_details['bank_name'], $icol,0,$row_value);
		$row_value+=8; 
		$rep->NewLine();
		$rep->Text($ccol-4, trans("Marital Status"), $icol,0,$row_value);
		$rep->Text($ccol+117, $hrm_empl_marital[($empl_details['marital_status'] > 0 ? $empl_details['marital_status'] : 1)], $icol,0,$row_value);
		$rep->Text($ccol+282, trans("Bank Account No "), $icol,0,$row_value);
		$rep->Text($ccol+378, $job_details['acc_no'], $icol,0,$row_value);
		$row_value+=8;
		$rep->NewLine();
		$rep->Text($ccol-4, trans("Blood Group"), $icol,0,$row_value);
		$rep->Text($ccol+117, $hrm_empl_bloog_group[$job_details['bloog_group']], $icol,0,$row_value);
		$rep->Text($ccol+282, 'Status', $icol,0,$row_value);		 
		$rep->Text($ccol+378, $hrm_empl_status[$empl_details['status']], $icol,0,$row_value);	
		$row_value+=8;
		$rep->NewLine();
		$rep->Text($ccol-4, trans("Designation Group"), $icol,0,$row_value);
		$rep->Text($ccol+117, $hrm_empl_desig_group[$job_details['desig_group']], $icol,0,$row_value);	
		$row_value+=8;
		$rep->NewLine();
		$rep->Text($ccol-4, trans("Designation"), $icol,0,$row_value);
		$desig = GetSingleValue('kv_empl_designation', 'description', array('id'=> $job_details['desig']));
		$rep->Text($ccol+117, $desig, $icol,0,$row_value);	
		$row_value+=8;
		$rep->NewLine();
		$rep->Text($ccol-4, trans("Address"), $icol,0,$row_value);
		$rep->Text($ccol+117, $empl_details['addr_line1'] .' '. $empl_details['addr_line2'], $icol,0,$row_value);	
		$row_value+=8;
		$rep->NewLine();
		$rep->Text($ccol-4, trans("City"), $icol,0,$row_value);
		$rep->Text($ccol+117, $empl_details['empl_city'], $icol,0,$row_value);
		$row_value+=8;
		$rep->NewLine();
		$rep->Text($ccol-4, trans("State"), $icol,0,$row_value);
		$rep->Text($ccol+117, $empl_details['empl_state'], $icol,0,$row_value);
		$row_value+=8;
		$rep->NewLine();
		$country_name = GetSingleValue('kv_empl_country', 'local_name', array('id'=> $empl_details['country']));
		$rep->Text($ccol-4, trans("Country"), $icol,0,$row_value);
		$rep->Text($ccol+117, $country_name, $icol,0,$row_value);
		$row_value+=8;
		$rep->NewLine();
		$rep->Text($ccol-4, trans("Residential Address"), $icol,0,$row_value);
		$rep->Text($ccol+117, $empl_details['address2'], $icol,0,$row_value);		 
		$row_value+=8;
	

	//$rep->NewLine(4);

	/*Employee attendance details*/
	$attendance_detail=GetAll('kv_empl_attendancee',array('empl_id' => $empl_id));

	if(count($attendance_detail) > 0 ){
		$cols2 = array(0, 40, 130);
		$headers2 = array(trans("Month"), trans("Year"));
		$aligns2 = array('left', 'left');
		$vj = 1;
		for($js=1;$js<=31;$js++){			
				$col_span = 130+($vj*14); 	
				$cols2[$vj+2] =  $col_span;
				$headers2[$vj+1] =  trans($js);
				$aligns2[$vj+1] =  'left';
				$vj++;
			}
		
	    if ($orientation == 'L')
	    	recalculate_cols($cols2);	

		$rep->Info(null, $cols2, $headers2, $aligns2);
		$_POST['page_titl']=trans("Employee Attendance");
		$rep->NewPage();
		$ccol=43;
		$icol = 0;
		$row_value=-164;		

		$P=$GL=$CL=$ML=$A=$OD=$HD = 0;
		foreach ($attendance_detail as $single_row) {
			$fyear = GetRow('fiscal_year', array('id' => $single_row['year']));
			$rep->NewLine(2);
			$rep->TextCol(0, 1, kv_month_name_by_id($single_row['month']),0,-60);
			$rep->TextCol(1, 2, $fyear['begin'].'-'.$fyear['end'],0,-60);
			$j = 2;
			$v = 3; 
			for($vj=1; $vj<=31; $vj++){
				switch ($single_row[$vj]) {
					case 'P':
						$P++;
						break;
						case 'GL':
						$GL++;
						break;
						case 'CL':
						$CL++;
						break;
						case 'ML':
						$ML++;
						break;
						case 'A':
						$A++;
						break;
						case 'OD':
						$OD++;
						break;
						case 'HD':
						$HD++;
						break;
					
					default:
						break;
				}
				$rep->TextCol($j, $v, ($single_row[$vj]? $single_row[$vj]: '-'),0,-60);
				$j++;
				$v++;
			}
		}
		
		//$actual_row = $rep->row; 
		//$stable_footer = $rep->bottomMargin ;
		//$rep->row = $stable_footer;
		$row_value = $rep->bottomMargin;
		//display_error($stable_footer.'-'.$actual_row.'-'.$row_value);
		$rep->Text($ccol, trans("Total Present Days"), $icol,0,$row_value);
		$rep->Text($ccol+120, $P, $icol,0,$row_value);
		$rep->Text($ccol+260, trans("Absent"), $icol,0,$row_value);
		$rep->Text($ccol+380, $A, $icol,0,$row_value);
		$rep->Text($ccol+520, trans("Half Day"), $icol,0,$row_value);
		$rep->Text($ccol+640, $HD, $icol,0,$row_value);
		$rep->NewLine();
		$rep->Text($ccol, trans("Total General Leave"), $icol,0,$row_value);
		$rep->Text($ccol+120, $GL, $icol,0,$row_value);
		$rep->Text($ccol+260, trans("Total Common Leave"), $icol,0,$row_value);
		$rep->Text($ccol+380, $CL, $icol,0,$row_value);
		$rep->Text($ccol+520, trans("Medical Leave"), $icol,0,$row_value);
		$rep->Text($ccol+640, $ML, $icol,0,$row_value);
		$rep->NewLine();
		$rep->Text($ccol, trans("On Duty"), $icol,0,$row_value);
		$rep->Text($ccol+120, $OD, $icol,0,$row_value);
		//$rep->row = $actual_row;
	}

	/*function get_empl_sal_details_file($empl_id, $year){
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_salary	WHERE empl_id=".db_escape($empl_id)." AND year=".db_escape($year);
		return db_query($sql,trans("No transactions were returned"));
	}*/
	
   // $result = get_empl_sal_details_file($empl_id, $year);	
    $get_employee_payroll = GetAll('kv_empl_salary',array('empl_id' => $empl_id));
	if ($get_employee_payroll)	{  //payroll
		$th = array(trans("Month"), trans("Year") );

	    $Allowance = kv_get_allowances(null, 0, $job_details['grade']);
		foreach($Allowance as $single) {	
			if($single['type'] == 'Earnings')
				$th[] = substr($single['description'], 0, 5);
		}
		$th[] = trans("OT");
		$th[] = trans("Ot.Allo.");
		
		foreach($Allowance as $single) {
			if($single['type'] == 'Deductions')	
				$th[] = substr($single['description'], 0, 7);
		}

	   	$th1 = array(trans("LOP Amt"),trans("Misc."),trans("Gross"),trans("Total Ded."),trans("Net Sal"));
	   	$headers2 = array_merge($th, $th1);
	   	$count_header = count($headers2);

		$aligns2 = $cols2 = array();
		$Col_count = 570/$count_header;
		for($vj=0; $vj<=$count_header; $vj++){
			$aligns2[] ='left';
			$cols2[] = $vj*$Col_count;
		}
	
	    if ($orientation == 'L')
	    	recalculate_cols($cols2);	

		$rep->Info(null, $cols2, $headers2, $aligns2);
		$_POST['page_titl']=trans("Employee Payroll");
		$rep->aligns = $aligns2;
		$rep->NewPage();
		$ccol=43;
		$icol = 0;
		$row_value=-164;
		
		$Total_gross = $total_net = 0; 
			foreach($get_employee_payroll as $data_for_empl) {
				if($data_for_empl) {
					$rep->NewLine();
						
					$vj = 0; $jv = 1;
					$rep->TextCol($vj, $jv, kv_month_name_by_id($data_for_empl['month']),0,-70);
					$vj++; $jv++;
					$rep->TextCol($vj,$jv,$fyear['begin'].'-'.$fyear['end'],0,-70);

					foreach($Allowance as $single) {
						if($single['type'] == 'Earnings'){
							$vj++; $jv++;
							$rep->TextCol($vj, $jv, number_format2($data_for_empl[$single['id']], $dec),0,-70);
						}
						
					}
					$vj++; $jv++;
					$rep->TextCol($vj, $jv, number_format2($data_for_empl['ot_earnings'], $dec),0,-70);

					$vj++; $jv++;
					$rep->TextCol($vj, $jv, number_format2($data_for_empl['ot_other_allowance'], $dec),0,-70);
					$total_ded = 0; 
					foreach($Allowance as $single) {
						if($single['type'] == 'Deductions'){
							$vj++; $jv++;
							$rep->TextCol($vj, $jv, number_format2($data_for_empl[$single['id']], $dec),0,-70);
							$total_ded += $data_for_empl[$single['id']]; 
						}
					}
					/*$vj++; $jv++;
					$rep->TextCol($vj, $jv, number_format2($data_for_empl['loan'], $dec),0,-70);
					$total_ded += $data_for_empl['loan'];*/
					$vj++; $jv++;
					$rep->TextCol($vj, $jv, number_format2($data_for_empl['lop_amount'],$dec),0,-70);
					$total_ded += $data_for_empl['lop_amount']; 

					$vj++; $jv++;
					$rep->TextCol($vj, $jv, number_format2($data_for_empl['misc'], $dec),0,-70);
					$total_ded += $data_for_empl['misc']; 

					$vj++; $jv++;
					$rep->TextCol($vj, $jv, number_format2($data_for_empl['gross'], $dec),0,-70);

					$vj++; $jv++;
					$rep->TextCol($vj, $jv, number_format2($total_ded, $dec),0,-70);

					$vj++; $jv++;
					$rep->TextCol($vj, $jv, number_format2($data_for_empl['net_pay'], $dec),0,-70);

					$Total_gross += $data_for_empl['gross'];
					$total_net += $data_for_empl['net_pay'];
					
					$rep->NewLine(); 
				}
			}
		
		$row_value = $rep->bottomMargin;
		//display_error($stable_footer.'-'.$actual_row.'-'.$row_value);
		$rep->Text($ccol, trans("Total Present Days"), $icol,0,$row_value);
		$rep->Text($ccol+120, $P, $icol,0,$row_value);
		$rep->NewLine(3);
		$rep->Text($ccol, trans("Total Gross Amount"), $icol,0,$row_value);
		$rep->Text($ccol+120, $Total_gross, $icol,0,$row_value);
		$rep->Text($ccol+260, trans("Total Net Amount"), $icol,0,$row_value);
		$rep->Text($ccol+380, $total_net, $icol,0,$row_value);
	}

	//loan details
	$loans = get_empl_loan_details_Complete($empl_id);
	if(count($loans) > 0 ){
		$cols2 = array(10,70,140,200, 270, 315, 365, 425, 480, 525);
		$headers2 = array(trans("Loan Type"), trans("Loan Amount"), trans("Paid Amount"), trans("Balance Amount"), trans("Monthly Pay"), '    '.trans("Periods"), trans("Periods Paid"), trans("Start Date"), trans("End Date"), trans("Status"));
		$aligns2 = array('left', 'right', 'right','right', 'right', 'left','left', 'left', 'left', 'left');
		if ($orientation == 'L')
	    	recalculate_cols($cols2);	

		$rep->Info(null, $cols2, $headers2, $aligns2);
		$_POST['page_titl']=trans("Employee Loan");
		$rep->NewPage();
		
		foreach($loans as $loan_single){
			$rep->NewLine();
			$vj = 0; $jv = 1; 
			$rep->TextCol($vj, $jv, $loan_single[2],0,-70);  // Loan Type
			$vj++; $jv++;
			$rep->TextCol($vj, $jv, number_format2($loan_single[3], $dec),0,-70);  // Loan full Amount

			$vj++; $jv++;
			$rep->TextCol($vj, $jv, number_format2($loan_single[4]* $loan_single[6], $dec),0,-70); //Paid Amount

			$vj++; $jv++;
			$rep->TextCol($vj, $jv, number_format2($loan_single[4]* ($loan_single[5]-$loan_single[6]), $dec),0,-70); //Balance Amount

			$vj++; $jv++;
			$rep->TextCol($vj, $jv, number_format2($loan_single[4], $dec),0,-70); //Monthly Pay
			$vj++; $jv++;
			$rep->TextCol($vj, $jv, '        '.$loan_single[5],0,-70); // Periods
			$vj++; $jv++;
			$rep->TextCol($vj, $jv, $loan_single[6],0,-70);  //Periods Paid
			$vj++; $jv++;
			$rep->TextCol($vj, $jv, sql2date($loan_single[7]),0,-70);
			$date_of_end = date('Y-m-d', strtotime("+".$loan_single[5]." months", strtotime($loan_single[7])));
			$vj++; $jv++;
			$rep->TextCol($vj, $jv, sql2date($date_of_end),0,-70);
			$vj++; $jv++;
			$rep->TextCol($vj, $jv, $loan_single[8],0,-70);
					
				
		} 
	}
	if($empl_details['status'] != 1 ){
		$esb = GetRow('kv_empl_esb', array('empl_id' => $empl_id));
		if(count($esb) > 0 ){
			$cols2 = array(200, 405, 455);
			$headers2 = array(trans("Particulars"), trans(" Amount"));
			$aligns2 = array('left',	'left');
			//if ($orientation == 'L')
				//recalculate_cols($cols2);	
			
			$rep->Info(null, $cols2, $headers2, $aligns2);
			$_POST['page_titl']=trans("End of Service Benefit");
			$rep->NewPage();
			$vj = 0; $jv = 1; 
			$rep->TextCol($vj++, $jv++, trans("Days Employed"));
			$rep->TextCol($vj++, $jv++,	$esb['days_worked']);
			$rep->NewLine(2);
			$vj = 0; $jv = 1;
			$rep->TextCol($vj++, $jv++, trans("Loan Amount"));
			$rep->TextCol($vj++, $jv++,	number_format2($esb['loan_amount'], $dec));
			$rep->NewLine(2);
			$vj = 0; $jv = 1;
			$rep->TextCol($vj++, $jv++, trans("Last Paid Gross Salary"));
			$rep->TextCol($vj++, $jv++,	number_format2($esb['last_gross'],$dec));
			$vj = 0; $jv = 1;
			if($esb['amount']!=0){
				$rep->NewLine(2);
				$rep->TextCol($vj++, $jv++, trans("ESB Amount Before Loan "));
				$rep->TextCol($vj++, $jv++,	($esb['amount'] > 0 ? number_format2(($esb['amount']),$dec) : trans("Not Eligible")));
			}
			$vj = 0; $jv = 1;
						
			$rep->row = 120;
			$rep->NewLine(3);
			$rep->Line(165, 0.00001,0,0);	
			$rep->SetTextColor(16, 123, 15);
			$rep->TextCol($vj++, $jv++, trans("Payable ESB Amount After loan deduction"));
			$rep->TextCol($vj++, $jv++,	($esb['amount'] > 0 ? number_format2(($esb['amount']-$esb['loan_amount']),$dec) : trans("Not Eligible")));
		}
	}
	if(isset($_GET['email']) && $_GET['email']== 'yes')
		$rep->End(1, 'Payslip ');
	else
		$rep->End();
?>