<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
/*  Monthly Summary 
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
    $result_type = (isset($_POST['PARAM_2']) ? $_POST['PARAM_2'] : (isset($_GET['PARAM_2']) ? $_GET['PARAM_2'] : 0));
	$comment = (isset($_POST['PARAM_3']) ? $_POST['PARAM_3'] : (isset($_GET['PARAM_3']) ? $_GET['PARAM_3'] : ''));;
    //$destination = (isset($_POST['PARAM_4']) ? $_POST['PARAM_4'] : (isset($_GET['PARAM_4']) ? $_GET['PARAM_4'] : ''));
    $_POST['REP_ID'] = 806; 
    $dec = user_price_dec();
//if(db_has_employee_payslip($empl_id, $month, $year)){
//	if ($destination)
	//	include_once($path_to_root . "/reporting/includes/excel_report.inc");
	//else
	//$myrow = kv_get_sal_details_file($month, $year);	
	
    	//employer
		$emplr_esic_id= GetSingleValue('kv_empl_allowances', 'id', array('esic' => 1, 'type' => 'Employer Contribution'));
		$emplr_pf_id= GetSingleValue('kv_empl_allowances', 'id', array('pf' => 1, 'type' => 'Employer Contribution'));
		//$emplr_tax_id= GetSingleValue('kv_empl_allowances', 'id', array('Tax' => 1, 'type' => 'Deductions'));
		//employee
		$empl_esic_id= GetSingleValue('kv_empl_allowances', 'id', array('esic' => 1, 'type' => 'Deductions'));
		$empl_pf_id= GetSingleValue('kv_empl_allowances', 'id', array('pf' => 1, 'type' => 'Deductions'));
	
		if($emplr_esic_id && $emplr_pf_id && $empl_esic_id && $empl_pf_id){
			$sql =" SELECT info.empl_id, CONCAT_WS(' ', info.empl_firstname, info.empl_lastname) AS Name , job.bank_name, job.acc_no, job.branch_detail, job.ifsc, job.ESIC, job.PF, salary.net_pay
			, `salary`.`".$emplr_esic_id."`, `salary`.`".$empl_esic_id."`, `salary`.`".$empl_pf_id."`, `salary`.`".$emplr_pf_id."` FROM ".TB_PREF."kv_empl_info as info, ".TB_PREF."kv_empl_job as job, ".TB_PREF."kv_empl_salary  as salary
			WHERE info.empl_id = job.empl_id AND info.empl_id = salary.empl_id AND salary.month = ".$month." AND salary.year=".$year;
		}	 else {
			$sql =" SELECT info.empl_id, CONCAT_WS(' ', info.empl_firstname, info.empl_lastname) AS Name , job.bank_name, job.acc_no, job.branch_detail, job.ifsc, job.ESIC, job.PF, salary.net_pay
			 FROM ".TB_PREF."kv_empl_info as info, ".TB_PREF."kv_empl_job as job, ".TB_PREF."kv_empl_salary  as salary
			WHERE info.empl_id = job.empl_id AND info.empl_id = salary.empl_id AND salary.month = ".$month." AND salary.year=".$year;						
		}
		
		$result = db_query($sql, "can't get result");
	if (db_num_rows($result) > 0)	{
		include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

		$orientation = 'P';		
			
			if($result_type == 'bank'){
				$cols = array(0,35, 205,290, 365, 420, 490, 560);	
				$headers = array(trans("ID"), trans("Employee Name"), trans("Bank Name"), trans("Bank Account No"), trans("Bank Branch"), trans("IFSC Code"), trans("Net Salary Payable"));
				$aligns = array('left',	'left', 'left', 'left', 'left', 'left', 'right');
				$title = trans("Payroll Report").' - '.kv_month_name_by_id($month);
			} elseif($result_type == 'esic'){
				$cols = array(0,35, 135, 230, 340,520);	 
				$headers = array(trans("ID"), trans("Employee Name"),trans("ESIC No"), trans("Employer ESIC Amount"),trans("Employee ESIC Amount"));
				$aligns = array('left',	'left','left', 'right', 'right');
				$title =trans("ESIC Report").' - '.kv_month_name_by_id($month);
			} elseif($result_type == 'pf'){
				$cols = array(0,35, 135, 230, 340,520);	
				$headers = array(trans("ID"), trans("Employee Name"),trans("PF Account No"), trans("Employer PF"),trans("Employee PF") );
				$aligns = array('left',	'left', 'left', 'right', 'right');
				$title =trans("PF Report").' - '.kv_month_name_by_id($month);
			}
			
			$rep = new FrontReport($title, $title, user_pagesize(), 9, $orientation);
	   
			$rep->SetHeaderType('Header');	
		    $rep->Font();			
		    $rep->Info(null, $cols, $headers, $aligns);	
		    $rep->NewPage();
			
			$total=$total1 =$total2= 0;
			while ($row = db_fetch_assoc($result)) {			
				//display_error(json_encode($row));
				$rep->NewLine(1, 2);
				$rep->TextCol(0, 1, $row['empl_id']);
				$rep->TextCol(1, 2, $row['Name']);
				if($result_type == 'bank'){
					$rep->TextCol(2, 3, $row['bank_name']);
					$rep->TextCol(3, 4, $row['acc_no']);
					$rep->TextCol(4, 5, $row['branch_detail']);
					$rep->TextCol(5, 6, $row['ifsc']);
					$rep->TextCol(6, 7, number_format2($row['net_pay'], 2));
					$total += $row['net_pay'];
				}elseif($result_type == 'esic'){
					$rep->TextCol(2, 3, $row['ESIC']);
					$rep->TextCol(3, 4, number_format2($row[$emplr_esic_id], 2));
					$$total1 += $row[$emplr_esic_id];
					$rep->TextCol(4, 5, number_format2($row[$empl_esic_id], 2));
					$$total2 += $row[$empl_esic_id];
				}elseif($result_type == 'pf'){ 
					$rep->TextCol(2, 3, $row['PF']);
					$rep->TextCol(3, 4, number_format2($row[$emplr_pf_id], 2));
					$total1 += $row[$emplr_pf_id];
					$rep->TextCol(4, 5, number_format2($row[$empl_pf_id], 2));
					$total2 += $row[$empl_pf_id];
				}
			}
			
			$rep->row = 160;
			if($result_type == 'bank'){
				$rep->TextCol(4,6, trans("Net Salary Payable")." :");
				$rep->TextCol(6, 7,	number_format2($total,$dec));
			}elseif($result_type == 'esic'){
				$rep->TextCol(2,3, trans("Net Amount")." :");
				$rep->TextCol(3, 4,	number_format2($total1,$dec));
				$rep->TextCol(4, 5,	number_format2($total2,$dec));
			}elseif($result_type == 'pf'){ 
				$rep->TextCol(2,3, trans("Net Amount")." :");
				$rep->TextCol(3, 4,	number_format2($total1,$dec));
				$rep->TextCol(4, 5,	number_format2($total2,$dec));

			}

			$rep->NewLine(1);
			$rep->row = 180;	
			if($comment){				
				$rep->SetTextColor(0, 0, 0);
				$rep->Text(40, trans("Comments"),0,0,65);
				$rep->Text(200, $comment,0,0,65);  //$rep->NewLine(2);	
			}		
			$rep->Line(65, 0.00001,0,0);		
			
		if ($rep->row < $rep->bottomMargin )
			$rep->NewPage();	
		$rep->End(); //1, 'Payslip ');
	}else{
		display_warning("No Payroll Entry Found For Selected Period.");
	}
?>