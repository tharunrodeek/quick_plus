<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module  : Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
/*  Edit Employee Attendance
*****************************************/
$page_security = 'HR_ATTENDANCE';
$path_to_root="../..";
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
page(trans("Edit Employee Attendance"));
$new_item = get_post('dept_id')=='' || get_post('cancel') ;

$dim = get_company_pref('use_dimension');
if (isset($_GET['dept_id'])){
	$_POST['dept_id'] = $_GET['dept_id'];
}
$dept_id = get_post('dept_id');
 if (list_updated('dept_id')) {
	$_POST['empl_id'] = $dept_id = get_post('dept_id');
    $Ajax->activate('details');
}

if (isset($_POST['addupdate'])) {
	$input_error = 0;
		
	if($input_error==0){
		
		$month = $_POST['month'];
		$year = $_POST['year'];
		$day = $_POST['year'];
		
		$kv_empl_attendancee_ar = array();
		for($vj=1; $vj<=$_POST['total_days_count']; $vj++){	
			if(isset($_POST[$vj])){
				$BeginTime = date('H:i:s', strtotime($_POST['Empl_in_'.$vj.'_hour'].':'.str_pad($_POST['Empl_in_'.$vj.'_min'], 2, '0', STR_PAD_LEFT).' '.$_POST['Empl_in_'.$vj.'_ampm']));
				$_POST['Empl_in_'.$vj] = $BeginTime;

				$EndTime = date('H:i:s', strtotime($_POST['Empl_out_'.$vj.'_hour'].':'.str_pad($_POST['Empl_out_'.$vj.'_min'], 2, '0', STR_PAD_LEFT).' '.$_POST['Empl_out_'.$vj.'_ampm']));
				$_POST['Empl_out_'.$vj] = $EndTime;
				
				$kv_empl_attendancee_ar[$vj] = $_POST[$vj];
				$kv_empl_attendancee_ar[$vj."vj_in"] = $_POST['Empl_in_'.$vj];
				$kv_empl_attendancee_ar[$vj."vj_out"] = $_POST['Empl_out_'.$vj];
			}
			/*if(db_has_day_attendancee($vj, $month, $year)){
				update_employee_attendance($_POST['Empl_'.$empl_id], $empl_id, $month, $year,$day, $_POST['Empl_in_'.$empl_id], $_POST['Empl_out_'.$empl_id]);
			}else{
				add_employee_attendance($_POST['Empl_'.$empl_id], $empl_id, $month, $year, $day, $_POST['Empl_in_'.$empl_id], $_POST['Empl_out_'.$empl_id],	$_POST['dept_id']);
			}*/
		}
		//display_error(json_encode($kv_empl_attendancee_ar));
		Update('kv_empl_attendancee', array('empl_id' => $_POST['empl_id'], 'month' => $month, 'year' => $year), $kv_empl_attendancee_ar);
		display_notification("Attendance Register Saved Successfully");
	}
	$new_role = true;	//clear_data();
	$Ajax->activate('_page_body');	
}

//function clear_data(){	unset($_POST);	}

start_form(true);
$month = get_post('month','');
	$year = get_post('year','');
if (db_has_employees()) {
	if (isset($_POST['dept_id']) && $_POST['dept_id'] >0) {
		$_POST['dept_id'] = input_num('dept_id');
	}
	start_table(TABLESTYLE_NOBORDER);
		start_row();
			kv_fiscalyears_list_cells(trans("Fiscal Year:"), 'year', null, true);
		 	kv_current_fiscal_months_list_cell("Months", "month", null, true);
		 	department_list_cells(trans("Select a Department")." :", 'dept_id', null, trans("No Department"), true, check_value('show_inactive'));
			employee_list_cells(trans("Select an Employee"). " :", 'empl_id', null,	trans("Select an Employee"), true, check_value('show_inactive'), false, false,true);
			$new_item = get_post('dept_id')=='';
			if ($dim >= 1){
				dimensions_list_cells(trans("Dimension")." 1", 'dimension_id', null, true, '', false, 1, true);
				//if ($dim > 1)
					//dimensions_list_cells(trans("Dimension")." 2", 'dimension2_id', null, true, false, false, 2, true);
			}
			if ($dim < 1)
				hidden('dimension_id', 0);
		end_row();
	end_table(1);

	if (get_post('_show_inactive_update') || get_post('empl_id')) {
			$Ajax->activate('month');
			$Ajax->activate('details');
			$Ajax->activate('dept_id');		
			set_focus('month');
	}
	if($month==null){			 
		$month = $_POST['month'];
	}
	if($year==null){			 
		$year = $_POST['year'];
	}	
	
div_start('totals_tbl');
	$dept_id = get_post('dept_id');
	$attendance_date = get_post('attendance_date');   
		$Ajax->activate('_page_body');
	if (!$dept_id) 
		$dept_id = 0 ; 	
	$day_absentees = array();
	
	//echo " <center> Select the Absentees only ...</center>";
	
	$disabled= '';
	$submit = true;
	/*if(strtotime(date2sql($attendance_date)) > strtotime(date('Y-m-d'))){
		display_warning("You can't Enter Yet to born day Attendance!");
		set_focus('attendance_date');
		$disabled = 'disabled';
		$submit = false;
	}elseif(key_in_foreign_table(date2sql($attendance_date), 'kv_empl_gazetted_holidays', 'date')){
		display_warning("It's Official Holiday, you can't Input to a holiday!");
		set_focus('attendance_date');
		$disabled = 'disabled';
		$submit = false;
	}else{
		$submit = true;
	}*/
	$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
 	$ext_year = date("Y", strtotime($months_with_years_list[(int)get_post('month')]));

	$all_settings =  GetAll('kv_empl_option');
	$hrmsetup = array(); 
	foreach($all_settings as $settings){
		$data_offdays = @unserialize(base64_decode($settings['option_value']));
		if ($data_offdays !== false) {
			$hrmsetup[$settings['option_name']] = unserialize(base64_decode($settings['option_value']));
		} else {
			$hrmsetup[$settings['option_name']] = $settings['option_value']; 
		}
	}	
	$total_days_count = $pre_mnth = 0;
		if($hrmsetup['BeginDay'] > 1 && $hrmsetup['BeginDay'] < 31){			
			$pre_mnth =  (($month > 1)? ($month-1) : '1');
			$total_days_count =  (date("t", strtotime($year."-".$pre_mnth."-01"))- $hrmsetup['BeginDay'])+1;
		}
		
		if($hrmsetup['EndDay'] <= 31){
			$total_days_count +=date("t", strtotime($year."-".$month."-01"));
		}
	
	echo '<center><p style="color: #F44336;"><b>Note</b>: * AL-General Leave, CL-Common Leave, ML-Medical Leave</p></center>';

	start_table(TABLESTYLE);
	
	//table_section_title(trans("Employees List"));
	echo '<tr> <td class="tableheader"> Date</td> <td class="tableheader"> Present</td> <td  class="tableheader"> In Time</td> <td  class="tableheader"> Out Time</td><td  class="tableheader"> Leave </td><td class="tableheader"> Absent </td> <td class="tableheader"> On Duty</td> <td class="tableheader"> Half Day</td> </tr>' ;
	if(!get_post('empl_id')	) 
		label_row(" Select an employee to continue ", '', "colspan=4", " ", 4 ); 
	else {
		$row = GetRow('kv_empl_job', array('empl_id' => get_post('empl_id')));	
		$info = GetRow('kv_empl_info', array('empl_id' => get_post('empl_id')));	
		$day_absentees = GetAll('kv_empl_attendance', array('empl_id' => $_POST['empl_id'], 'month' => $_POST['month'], 'year' => $_POST['year']));
		hidden('total_days_count', $total_days_count);
		//display_error(json_encode($day_absentees));
		if(is_array($day_absentees)){
			for($vj=1; $vj<=$total_days_count; $vj++){	
				$attendance_date = date("Y-m-d", strtotime($ext_year."-".$month."-".$vj)) ; 
				$day_letters = date("D", strtotime($ext_year."-".$month."-".$vj));
				if(in_array($day_letters, $hrmsetup['weekly_off'] ) )				
					$weekly_off_style="weekly_off";
				else 
					$weekly_off_style="";
				if($row['joining'] <= $attendance_date && ($info['status'] == 1 || ($info['status']>1 && $info['date_of_status_change'] >= $attendance_date))){
					echo '<tr class="'.$weekly_off_style.'"> <td>'.sql2date($attendance_date).'</td><td>';
					$_POST[$vj] = (isset($day_absentees[$vj]) ? $day_absentees[$vj] : ''); 
					if(array_key_exists($vj, $day_absentees)  && $day_absentees[$vj] == "P")
						echo kv_radio(" ", $vj, "P", "selected", false, $disabled);
					else
						echo kv_radio(" ", $vj, "P", "", false, $disabled);
						
					echo '</td><td>';
					if(array_key_exists($vj, $day_absentees)  && (date('H:i:s', strtotime($day_absentees[$vj.'vj_in'])) != '00:00:00' ? true : false))
						echo TimeDropDown('Empl_in_'.$vj, $day_absentees[$vj.'vj_in']);
					else
						echo TimeDropDown( 'Empl_in_'.$vj, $hrmsetup['BeginTime']);
						
					echo '</td><td>';

					if(array_key_exists($vj, $day_absentees)  && (date('H:i:s', strtotime($day_absentees[$vj.'vj_out'])) != '00:00:00' ? true : false))
						echo TimeDropDown('Empl_out_'.$vj, $day_absentees[$vj.'vj_out']);
					else
						echo TimeDropDown( 'Empl_out_'.$vj, $hrmsetup['EndTime']);
						
					echo '</td><td>';

					if(array_key_exists($row['empl_id'], $day_absentees)  && $day_absentees[$vj] == "AL")
						echo kv_radio("AL", $vj, "AL", "selected", false, $disabled);
					else
						echo kv_radio("AL", $vj, "AL", null, false , $disabled);

					if(array_key_exists($row['empl_id'], $day_absentees)  && $day_absentees[$vj] == "CL")
						echo kv_radio("CL", $vj, "CL", "selected", false, $disabled);
					else
						echo kv_radio("CL", $vj, "CL", null, false , $disabled);

					if(array_key_exists($row['empl_id'], $day_absentees)  && $day_absentees[$vj] == "ML")
						echo kv_radio("ML", $vj, "ML", "selected", false, $disabled);
					else
						echo kv_radio("ML", $vj, "ML", null, false , $disabled);
						
					echo '</td><td>';

					if(array_key_exists($row['empl_id'], $day_absentees)  && $day_absentees[$vj] == "A")
						echo kv_radio(" ", $vj, "A", "selected", false, $disabled);
					else
						echo kv_radio(" ", $vj, "A", null, false , $disabled);
						
					echo '</td><td>';

					if(array_key_exists($row['empl_id'], $day_absentees)  && $day_absentees[$vj] == "OD")
						echo kv_radio(" ", $vj, "OD", "selected", false, $disabled);
					else
						echo kv_radio(" ", $vj, "OD", null, false , $disabled);
						
					echo '</td><td>';

					if(array_key_exists($row['empl_id'], $day_absentees)  && $day_absentees[$vj] == "HD")
						echo kv_radio(" ", $vj, "HD", "selected", false, $disabled);
					else
						echo kv_radio(" ", $vj, "HD", null, false , $disabled);				
					echo '</td></tr>';
				}					
			}			
		}	else {
					display_warning(trans("The Selected employee does not have any existing data"));
				}	
	}
	end_table();
	br();
	if($submit){
		submit_center('addupdate', trans("Submit Attendance"), true, '', 'default');
	}
div_end();

}else{
	check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));
}
 
end_form();
end_page(); 
?>
<style>
tr.weekly_off td { background-color: #FF9800;}
.list_container {   width: auto;   display: inherit;}
table.tablestyle tr td:nth-child(4), table.tablestyle tr td:nth-child(5) { width:150px; }
</style>