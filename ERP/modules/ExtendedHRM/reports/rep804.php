<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
/* Attendance PDF
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

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = 'L';
	$selected_fiscal_year = get_fiscalyear($year);
    $start = date("Y", strtotime($selected_fiscal_year['begin']));
    $end = 	date("Y", strtotime($selected_fiscal_year['end']));
    $get_year_frm_fiscalyear = date("Y-m-d", strtotime($start.'-'.$month.'-01'));
    if(is_date_in_fiscalyear(sql2date($get_year_frm_fiscalyear))){ 
    	$total_days =  date("t", strtotime($start.'-'.$month.'-01'));
    	$rep_year = $start;
    } else {
    	$total_days =  date("t", strtotime($start.'-'.$month.'-01'));
    	$rep_year = $end;
    }
    $months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
 	$ext_year = date("Y", strtotime($months_with_years_list[(int)$month]));

	$cols = array(0, 45, 180);

	$headers = array(trans('ID#'), trans('Name'));
	$aligns = array('left',	'left');
	$all_settings =  GetAll('kv_empl_option');
		$hrmsetup = array(); 
		foreach($all_settings as $settings){
			$data_offdays = @unserialize($settings['option_value']);
			if ($data_offdays !== false) {
				$hrmsetup[$settings['option_name']] = unserialize($settings['option_value']);
			} else {
				$hrmsetup[$settings['option_name']] = $settings['option_value']; 
			}
		}

	$vj= 1;
	/*if($hrmsetup['BeginDay'] >= 1 && $hrmsetup['BeginDay'] < 31){
		$kv = $hrmsetup['BeginDay'];
		$pre_mnth =  (($month > 1)? ($month-1) : '1');
		$total_days =  date("t", strtotime($year."-".$pre_mnth."-01"));
		for($kv; $kv<=$total_days; $kv++){		
			$col_span = 100+($vj*11); 	
			$cols[$vj+2] =  $col_span;
			$headers[$vj+1] =  trans($kv);
			$aligns[$vj+1] =  'left';
			$vj++;
		}	
	}*/
	
	//if($hrmsetup['EndDay'] < 31){ 
		$kv_end_days = date("t", strtotime($year."-".$month."-01"));
		//$nxt_mnth =  (($month < 12)? ($month+1) : '1');

		for($js=1;$js<=$kv_end_days;$js++){			
			$col_span = 180+($vj*16); 	
			$cols[$vj+2] =  $col_span;
			$headers[$vj+1] =  trans($js);
			$aligns[$vj+1] =  'left';
			$vj++;
		}
	//}

	$headers[] = trans("Worked Days");
	$headers[] = trans("Absent");
	$headers[] = trans("Leave Days");
	
	$aligns[] =  'right';
	$aligns[] =  'right';
	$aligns[] =  'right';
	$cols[] = 100+$col_span-50;
	$cols[] = 120+$col_span-10;
	$cols[] = 140+$col_span+30;
	//array_pop($cols);	
	$weekly_off = unserialize(base64_decode($hrmsetup['weekly_off']));
	
    $rep = new FrontReport(trans('Month of Attendance: '.kv_month_name_by_id($month).' - Dept: '.GetSingleValue('kv_empl_departments', 'description', array('id' => $dept_id))), "Attendance", 'A3', 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);
		
    $rep->Font();
    $rep->Info(null, $cols, $headers, $aligns);
    $rep->NewPage();
    $selected_empl = kv_get_employees_list_based_on_dept($dept_id);
	//$pre_mnth =  (($month > 1)? ($month-1) : '12');
	$total_days_count = date("t", strtotime($ext_year."-".$month."-01"));
	$sql_query = "SELECT date FROM ".TB_PREF."kv_empl_gazetted_holidays WHERE (date BETWEEN '".date("Y-m-d", strtotime($ext_year."-".$month."-1"))."' AND '".date("Y-m-d", strtotime($ext_year."-".$month."-".$total_days_count))."' ) ";

		$first_gazetted_leaves = array();
		$result = db_query($sql_query, "Can't get results");
		if(db_num_rows($result)){
			while($cont = db_fetch($result))
				$first_gazetted_leaves[] = $cont[0];
		}

	while ($row = db_fetch_assoc($selected_empl)) {
		
		//$details_single_empl = GetRow('kv_empl_attendancee', array('month' => $pre_mnth, 'year' => $year, 'empl_id' => $row['empl_id'])); 		
		$details_single_empl = GetRow('kv_empl_attendancee', array('month' => $month, 'year' => $year, 'empl_id' => $row['empl_id']));
		//display_error(json_encode($details_single_empl).$pre_mnth);
		//display_error(json_encode($details_single_empl_nxt_mnth).$month);

		if($details_single_empl){
			$rep->NewLine(1, 2);
			$rep->TextCol(0, 1, $row['empl_id']);
			$rep->TextCol(1, 2, $row['empl_firstname']);
			$absent_days = $workingDays = $leave_days = 0 ;
			$j = 2;
			$v = 3; 
			
			//if($hrmsetup['BeginDay'] >= 1 && $hrmsetup['BeginDay'] < 31){				
				
				for($vj = 1; $vj<=$total_days_count; $vj++){

					if( in_array(date("D", strtotime($ext_year."-".$month."-".$vj)), $weekly_off) ){
						//$workingDays++;
						$rep->TextCol($j, $v, ($details_single_empl[$vj]? $details_single_empl[$vj]: 'W'));
					}else {
						if($details_single_empl[$vj] == 'P' || $details_single_empl[$vj] == 'OD')
							$workingDays++;
						if($details_single_empl[$vj] == 'A' || ($details_single_empl[$vj] == '' && (strtotime($ext_year."-".$month."-".$vj)<= strtotime(date('Y-m-d'))) ))
							$absent_days += 1;
						if($details_single_empl[$vj] == 'GL' || $details_single_empl[$vj] == 'CL' || $details_single_empl[$vj] == 'ML')
							$leave_days += 1;
						if($details_single_empl[$vj] == 'HD'){
							$absent_days += 0.5;
							$workingDays += 0.5;
						}
						$rep->TextCol($j, $v, ($details_single_empl[$vj]? $details_single_empl[$vj]: (strtotime($ext_year."-".$month."-".$vj)<= strtotime(date('Y-m-d')))? 'A' : '-'));
					}					

					$j++; $v++;

				}
			
			//$Payable_days=$total_days_count-$absent_days;

			$rep->TextCol($j, $v, $workingDays);
			$j++;
			$v++;
			$rep->TextCol($j, $v, $absent_days);

			$j++;
			$v++;
			$rep->TextCol($j, $v, $leave_days);
			$rep->NewLine();  
		}				
	}	
	if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
		$rep->NewPage();	
	$rep->End();
?>
