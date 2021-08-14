<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'SA_OPEN';

$path_to_root="../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

global $path_to_root, $systypes_array, $kv_empl_gender;

    $year = $_POST['PARAM_0'];
    $month = $_POST['PARAM_1'];
    $dept_id = $_POST['PARAM_2'];
    $comment = $_POST['PARAM_3'];
    $destination = $_POST['PARAM_4'];

    $dec = user_price_dec();
   // if(db_has_sal_for_selected_dept($dept_id,$month, $year)){
    	if ($destination)
			include_once($path_to_root . "/reporting/includes/excel_report.inc");
		else
			include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

		$orientation = 'L';


		$th = array(trans("Id"), trans("Name") );

	    $Allowance = get_all_allowances();
		foreach($Allowance as $single) {	
			if($single['type'] == 'Earnings')
				$th[] = $single['description'] ; //substr($single['description'], 0, 5);
		}
		$th[] = trans("OT");
		$th[] = trans("Ot.Allo.");
		$th[] = trans("Gross");
		foreach($Allowance as $single) {
			if($single['type'] == 'Deductions')	
				$th[] = $single['description']; //substr($single['description'], 0, 7);
		}

	   	$th1 = array(trans("Abs Days"),trans("Abs Amt"),trans("Misc."),trans("Total Ded."),trans("Net Sal"));
	   	$headers = array_merge($th, $th1);
	   	$count_header = count($headers)+2;

		//$cols = array(0, 35, 90, 130, 150, 170, 190, 220, 250, 280, 310, 340, 370, 400, 430, 460, 490, 530, 580);
			
		$cols = array(0, 30, 130);
		$aligns =  array('left', 'left');
		$Col_count = 890/$count_header;
		for($vj=4; $vj<=$count_header; $vj++){
			$aligns[] ='right';
			$cols[] = $vj*$Col_count;
		}
		array_pop($cols);
		//display_error(count($aligns).'--'.count($cols));
	    $rep = new FrontReport(trans('Month of Payroll: '.kv_month_name_by_id($month).' - Dept: '.GetSingleValue('kv_empl_departments', 'description', array('id' => $dept_id))), "Payroll History", 'A3', 9, $orientation);
	    
	    if ($orientation == 'L')
	    	recalculate_cols($cols);
			
	    $rep->Font();
	    $rep->Info(null, $cols, $headers, $aligns);
	    $rep->NewPage();	   

	   $get_employees_list = get_empl_ids_from_dept_id($dept_id);
				
				//$Total_gross = $total_net = 0; 
				foreach($get_employees_list as $single_empl) { 
					
					$data_for_empl = GetRow('kv_empl_salary', array('empl_id' => $single_empl, 'month' => $month, 'year' => $year));
					if($data_for_empl) {
						$rep->NewLine(1, 2);
											
						$vj = 0; $jv = 1; 
						$rep->TextCol($vj, $jv, $data_for_empl['empl_id']);
						$vj++; $jv++;
					   $rep->TextCol($vj,$jv,kv_get_empl_name($data_for_empl['empl_id']));

						foreach($Allowance as $single) {
							if($single['type'] == 'Earnings'){
								$vj++; $jv++;
								$rep->TextCol($vj, $jv, number_format2($data_for_empl[$single['id']]*$data_for_empl['rate'],$dec));
							}
							
						}
						$vj++; $jv++;
						$rep->TextCol($vj, $jv, number_format2($data_for_empl['ot_earnings']*$data_for_empl['rate'],$dec));

						$vj++; $jv++;
						$rep->TextCol($vj, $jv, number_format2($data_for_empl['ot_other_allowance']*$data_for_empl['rate'],$dec));

						$vj++; $jv++;
						$rep->TextCol($vj, $jv, number_format2($data_for_empl['gross']*$data_for_empl['rate'],$dec));

						$total_ded = 0; 
						foreach($Allowance as $single) {
							if($single['type'] == 'Deductions'){
								$vj++; $jv++;
								$rep->TextCol($vj, $jv, number_format2($data_for_empl[$single['id']]*$data_for_empl['rate'],$dec));
								$total_ded += $data_for_empl[$single['id']]; 
							}
						}
						//$vj++; $jv++;
						//$rep->TextCol($vj, $jv, number_format2($data_for_empl['loan']*$data_for_empl['rate'],$dec));
						//$total_ded += $data_for_empl['loan']; 

						$vj++; $jv++;
						$rep->TextCol($vj, $jv, get_empl_attendance_for_month($data_for_empl['empl_id'], $month, $year));

						$vj++; $jv++;
						$rep->TextCol($vj, $jv, number_format2($data_for_empl['lop_amount']*$data_for_empl['rate'],$dec));
						$total_ded += $data_for_empl['lop_amount']; 

						$vj++; $jv++;
						$rep->TextCol($vj, $jv, number_format2($data_for_empl['misc']*$data_for_empl['rate'],$dec));
						$total_ded += $data_for_empl['misc']; 						

						$vj++; $jv++;
						$rep->TextCol($vj, $jv, number_format2($total_ded*$data_for_empl['rate'],$dec));

						$vj++; $jv++;
						$rep->TextCol($vj, $jv, number_format2($data_for_empl['net_pay']*$data_for_empl['rate'],$dec));

						//$Total_gross += $data_for_empl['gross'];
						//$total_net += $data_for_empl['net_pay'];
						
						$rep->NewLine(); 
					}
				}

				
		if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
			$rep->NewPage();	
		$rep->End();
	//} else{
	//	display_warning("No Data found for the selected Period for the Selected Department"//);
	//}
	

?>