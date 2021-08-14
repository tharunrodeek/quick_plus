<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
/*  ESB Print PDF
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
	$comment = (isset($_POST['PARAM_1']) ? $_POST['PARAM_1'] : (isset($_GET['PARAM_1']) ? $_GET['PARAM_1'] : ''));
    $_POST['REP_ID'] = 813; 

function db_has_employee_esb($empl_id){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_esb WHERE empl_id=".db_escape($empl_id));
}
if(db_has_employee_esb($empl_id)){

	include_once($path_to_root . "/modules/ExtendedHRM/reports/pdf_report.inc");

	$orientation = 'P';
	$dec = user_price_dec();
	$cols = array(15,400,400,500);		
	$headers = array(trans("Particulars"), trans(" "));
	$aligns = array('left',	'right');
	
	$resignation =  GetRow('kv_empl_job', array('empl_id' => $empl_id));
	$info_empl =  GetRow('kv_empl_info', array('empl_id' => $empl_id));
	$status_id = GetSingleValue('kv_empl_esb', 'status', array('empl_id' => $empl_id));
	$status = GetSingleValue('kv_empl_status_types', 'description', array('id' => $status_id));
		
    $rep = new FrontReport(trans("End of Service"), "End of Service", user_pagesize(), 9, $orientation);
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_esb WHERE empl_id=".db_escape($empl_id);	
    $result = db_query($sql,"No transactions were returned");
	
	if ($myrow = db_fetch($result))	{
		$name_and_dept = get_empl_name_dept($myrow['empl_id']);
		
		$employee_info = array(
					'id' => $myrow['empl_id'],
					'empl_id' => $myrow['empl_id'],
					'empl_name' => $name_and_dept['name'],
					'department' => GetsingleValue('kv_empl_departments', 'description', array('id' => $name_and_dept ['deptment'])),
					'desig' => kv_get_empl_desig($myrow['empl_id']),
					'joining' => sql2date(get_employee_join_date($myrow['empl_id'])),
					'resignation' => sql2date($info_empl['date_of_status_change']),
					'status' => $status,
					'month' => '',
					'year' => '' ); 

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

		    $rep->SetCommonData($employee_info, $baccount, array( $contacts),  'esb');			
		    $rep->NewPage();
			$rep->NewLine();
			$text_value=40; 
			$line_value=670;
			$Value = -70;	
			//header text
			$rep->Font('bold');
			$rep->TextCol(1, 2,	trans("Amount"), -55,-104);
			
			$rep->Font(); 
			$rep->Text($text_value+14, trans("Days Employed"),0,0,$Value);
			$rep->TextCol(1, 2,	$myrow['days_worked'], -25,-70);
			$rep->NewLine(2);
			$rep->Text($text_value+14, trans("Loan Amount"),0,0,$Value);
			$rep->TextCol(1, 2,	number_format2($myrow['loan_amount'], $dec), -25,-70);
			$rep->NewLine(2);
			$rep->Text($text_value+14, trans("Last Paid Gross Salary"),0,0,$Value);
			$rep->TextCol(1, 2,	number_format2($myrow['last_gross'],$dec), -25,-70);
			if($myrow['amount']!=0){
				$rep->NewLine(2);
				$rep->Text($text_value+14, trans("ESB Amount Before Loan "),0,0,$Value);
				$rep->TextCol(1, 2,	($myrow['amount'] > 0 ? number_format2(($myrow['amount']),$dec) : trans("Not Eligible")),-25,-70);
			}
			
			$rep->NewLine(2);
			$Value++;
			$rep->row = 120;
			$rep->NewLine(3);
			$rep->Line(165, 0.00001,0,0);	
			$rep->SetTextColor(16, 123, 15);
			$rep->Text($text_value+14, trans("Payable ESB Amount After loan deduction"),0,0,$Value+7);
			$rep->TextCol(1, 2,	($myrow['amount'] > 0 ? number_format2(($myrow['amount']-$myrow['loan_amount']),$dec) : trans("Not Eligible")), -25,-64);
			$rep->NewLine();
			$rep->Line(135, 0.00001,0,0);
			$rep->Line($line_value-585, 0.00001,0,0);	
			$rep->row = 180;	
			if($comment){				
				$rep->SetTextColor(0, 0, 0);
				$rep->Text($text_value, trans("Comments"),0,0,65);
				$rep->Text(200, $comment,0,0,65);  
			}
	}
			
	if ($rep->row < $rep->bottomMargin )
		$rep->NewPage();	
	if(isset($_GET['email']) && $_GET['email']== 'yes')
		$rep->End(1, 'ESB');
	else
		$rep->End();
}else{
	display_warning(trans("No ESB Found For Selected Employee."));
}
?>