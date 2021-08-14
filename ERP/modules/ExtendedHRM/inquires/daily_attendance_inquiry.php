<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_SELATTENDANCE';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
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
 
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/db_pager.inc");
page(trans("Daily Attendance Inquiry"));
 
check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));
 
simple_page_mode(true);
//----------------------------------------------------------------------------------------
	$new_item = get_post('dept_id')=='' || get_post('cancel') ;
	$month = get_post('month','');
	$year = get_post('year','');
	if (isset($_GET['dept_id'])){
		$_POST['dept_id'] = $_GET['dept_id'];
	}

//----------------------------------------------------------------------------------------
	start_form(true);
		start_table(TABLESTYLE_NOBORDER);
			echo '<tr>';
				date_cells(trans("Date") . ":", 'attendance_date', null, null, 0,0,0, null, true);
			 	department_list_cells(trans("Select a Department")." :", 'dept_id', null,	trans("No Department"), true, check_value('show_inactive'));
				empl_workcenter_list_cells(trans("Work Center"), 'work_centre', null, true, true);
				employee_list_cells(trans("Select an Employee"). " :", 'empl_id', null,	trans("Select an Employee"), true, check_value('show_inactive'), false, false,true);
				$new_item = get_post('dept_id')=='';
		 	echo '</tr>';
	 	end_table(1);

	 	if (get_post('attendance_date') || get_post('empl_id') || list_updated('work_centre') || list_updated('dept_id')) {
			$Ajax->activate('details');	
		}
		$month = $year = 0; 			
div_start('details');				
	
	if(get_post('attendance_date')){
		if(strtotime(date2sql(get_post('attendance_date'))) > strtotime(date('Y-m-d'))){
			display_warning("It's Yet to born day, No Attendance!");
			end_page();
		} 
		$Day = date('d', strtotime(date2sql(get_post('attendance_date'))));
		$month = (int)date("m", strtotime(date2sql(get_post('attendance_date'))));
		$year = get_fiscal_year_id_from_date(get_post('attendance_date'));
	}
		
	if(get_post('empl_id') > 0 ){	
		$selected_empl = GetDataJoin('kv_empl_job AS job', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `job`.`empl_id`'),
						1 => array('join' => 'INNER', 'table_name' => 'kv_empl_attendancee AS attendance', 'conditions' => '`attendance`.`empl_id` = `job`.`empl_id`'),
						2 => array('join' => 'LEFT OUTER', 'table_name' => 'workcentres AS wcentre', 'conditions' => '`wcentre`.`id` = `job`.`working_branch`'),
						3 => array('join' => 'LEFT OUTER', 'table_name' => 'kv_empl_departments AS dept', 'conditions' => '`dept`.`id` = `job`.`department`'),						
					), 
					array('`info`.`empl_id`, CONCAT(`info`.`empl_firstname`, `info`.`empl_lastname`) AS empl_name, `dept`.`description`, `attendance`.`'.(int)$Day.'`, `attendance`.`'.(int)$Day.'vj_in`,`attendance`.`'.(int)$Day.'vj_out`,  `wcentre`.`name`'), array('`attendance`.`month`' => $month, '`attendance`.`year`' => $year, '`job`.`empl_id`' => get_post('empl_id') ), array('info.empl_id' => 'asc'));
		$all_empl = GetDataJoin('kv_empl_job AS job', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `job`.`empl_id`'),
						//1 => array('join' => 'INNER', 'table_name' => 'kv_empl_attendancee AS attendance', 'conditions' => '`attendance`.`empl_id` = `job`.`empl_id`'),
						2 => array('join' => 'LEFT OUTER', 'table_name' => 'workcentres AS wcentre', 'conditions' => '`wcentre`.`id` = `job`.`working_branch`'),
						3 => array('join' => 'LEFT OUTER', 'table_name' => 'kv_empl_departments AS dept', 'conditions' => '`dept`.`id` = `job`.`department`'),						
					), 
					array('`info`.`empl_id`, CONCAT(`info`.`empl_firstname`, `info`.`empl_lastname`) AS empl_name, `dept`.`description`, `wcentre`.`name`'), array('`job`.`empl_id`' => get_post('empl_id') ), array('info.empl_id' => 'asc'));
		$sql_count =  1 ; 
		
	} elseif(get_post('dept_id') > 0 && !get_post('work_centre')) {				
	
		$selected_empl = GetDataJoin('kv_empl_job AS job', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `job`.`empl_id`'),
						1 => array('join' => 'INNER', 'table_name' => 'kv_empl_attendancee AS attendance', 'conditions' => '`attendance`.`empl_id` = `job`.`empl_id`'),
						2 => array('join' => 'LEFT OUTER', 'table_name' => 'workcentres AS wcentre', 'conditions' => '`wcentre`.`id` = `job`.`working_branch`'),
						3 => array('join' => 'LEFT OUTER', 'table_name' => 'kv_empl_departments AS dept', 'conditions' => '`dept`.`id` = `job`.`department`'),						
					), 
					array('`info`.`empl_id`, `attendance`.`'.(int)$Day.'`, `attendance`.`'.(int)$Day.'vj_in`,`attendance`.`'.(int)$Day.'vj_out`'), array('`attendance`.`month`' => $month, '`attendance`.`year`' => $year, '`job`.`department`' => get_post('dept_id') ), array('info.empl_id' => 'asc'));
					
		$all_empl = GetDataJoin('kv_empl_job AS job', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `job`.`empl_id`'),
						//1 => array('join' => 'INNER', 'table_name' => 'kv_empl_attendancee AS attendance', 'conditions' => '`attendance`.`empl_id` = `job`.`empl_id`'),
						2 => array('join' => 'LEFT OUTER', 'table_name' => 'workcentres AS wcentre', 'conditions' => '`wcentre`.`id` = `job`.`working_branch`'),
						3 => array('join' => 'LEFT OUTER', 'table_name' => 'kv_empl_departments AS dept', 'conditions' => '`dept`.`id` = `job`.`department`'),						
					), 
					array('`info`.`empl_id`, CONCAT(`info`.`empl_firstname`, `info`.`empl_lastname`) AS empl_name, `dept`.`description`,  `wcentre`.`name`'), array('`job`.`department`' => get_post('dept_id') ), array('info.empl_id' => 'asc'));
								
		$sql_count = count($all_empl); 
		
	} elseif(!get_post('dept_id') && get_post('work_centre') > 0) {				
		
		$selected_empl = GetDataJoin('kv_empl_job AS job', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `job`.`empl_id`'),
						1 => array('join' => 'INNER', 'table_name' => 'kv_empl_attendancee AS attendance', 'conditions' => '`attendance`.`empl_id` = `job`.`empl_id`'),
						2 => array('join' => 'LEFT OUTER', 'table_name' => 'workcentres AS wcentre', 'conditions' => '`wcentre`.`id` = `job`.`working_branch`'),
						3 => array('join' => 'LEFT OUTER', 'table_name' => 'kv_empl_departments AS dept', 'conditions' => '`dept`.`id` = `job`.`department`'),						
					), 
					array('`info`.`empl_id`, `attendance`.`'.(int)$Day.'`, `attendance`.`'.(int)$Day.'vj_in`,`attendance`.`'.(int)$Day.'vj_out`'), array('`attendance`.`month`' => $month, '`attendance`.`year`' => $year, '`job`.`working_branch`' => get_post('work_centre') ), array('info.empl_id' => 'asc'));
					
		$all_empl = GetDataJoin('kv_empl_job AS job', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `job`.`empl_id`'),
						//1 => array('join' => 'INNER', 'table_name' => 'kv_empl_attendancee AS attendance', 'conditions' => '`attendance`.`empl_id` = `job`.`empl_id`'),
						2 => array('join' => 'LEFT OUTER', 'table_name' => 'workcentres AS wcentre', 'conditions' => '`wcentre`.`id` = `job`.`working_branch`'),
						3 => array('join' => 'LEFT OUTER', 'table_name' => 'kv_empl_departments AS dept', 'conditions' => '`dept`.`id` = `job`.`department`'),						
					), 
					array('`info`.`empl_id`, CONCAT(`info`.`empl_firstname`, `info`.`empl_lastname`) AS empl_name, `dept`.`description`, `wcentre`.`name`'), array('`job`.`working_branch`' => get_post('work_centre') ), array('info.empl_id' => 'asc'));
					
					
		$sql_count = count($all_empl);
		
	} elseif(get_post('dept_id') >0 && get_post('work_centre') > 0) {				
		
		$selected_empl = GetDataJoin('kv_empl_job AS job', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `job`.`empl_id`'),
						1 => array('join' => 'INNER', 'table_name' => 'kv_empl_attendancee AS attendance', 'conditions' => '`attendance`.`empl_id` = `job`.`empl_id`'),
						2 => array('join' => 'LEFT OUTER', 'table_name' => 'workcentres AS wcentre', 'conditions' => '`wcentre`.`id` = `job`.`working_branch`'),
						3 => array('join' => 'LEFT OUTER', 'table_name' => 'kv_empl_departments AS dept', 'conditions' => '`dept`.`id` = `job`.`department`'),						
					), 
					array('`info`.`empl_id`, `attendance`.`'.(int)$Day.'`, `attendance`.`'.(int)$Day.'vj_in`,`attendance`.`'.(int)$Day.'vj_out`'), array('`attendance`.`month`' => $month, '`attendance`.`year`' => $year, '`job`.`working_branch`' => get_post('work_centre'), '`job`.`department`' => get_post('dept_id') ), array('info.empl_id' => 'asc'));
					
		$all_empl = GetDataJoin('kv_empl_job AS job', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `job`.`empl_id`'),
						//1 => array('join' => 'INNER', 'table_name' => 'kv_empl_attendancee AS attendance', 'conditions' => '`attendance`.`empl_id` = `job`.`empl_id`'),
						1 => array('join' => 'LEFT OUTER', 'table_name' => 'workcentres AS wcentre', 'conditions' => '`wcentre`.`id` = `job`.`working_branch`'),
						2 => array('join' => 'LEFT OUTER', 'table_name' => 'kv_empl_departments AS dept', 'conditions' => '`dept`.`id` = `job`.`department`'),						
					), 
					array('`info`.`empl_id`, CONCAT(`info`.`empl_firstname`, `info`.`empl_lastname`) AS empl_name, `dept`.`description`, `wcentre`.`name`'), array('`job`.`working_branch`' => get_post('work_centre'), '`job`.`department`' => get_post('dept_id') ), array('info.empl_id' => 'asc'));
										
		$sql_count = count($all_empl);
		
	} else {
		$selected_empl = GetDataJoin('kv_empl_job AS job', array( 
						1 => array('join' => 'INNER', 'table_name' => 'kv_empl_attendancee AS attendance', 'conditions' => '`attendance`.`empl_id` = `job`.`empl_id`')			
					), 
					array('`job`.`empl_id`, `attendance`.`'.(int)$Day.'`, `attendance`.`'.(int)$Day.'vj_in`,`attendance`.`'.(int)$Day.'vj_out`'), array('`attendance`.`month`' => $month, '`attendance`.`year`' => $year), array('job.empl_id' => 'asc'));
		
		$all_empl = GetDataJoin('kv_empl_job AS job', array( 
						0 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `job`.`empl_id`'),
						//1 => array('join' => 'INNER', 'table_name' => 'kv_empl_attendancee AS attendance', 'conditions' => '`attendance`.`empl_id` = `job`.`empl_id`'),
						1 => array('join' => 'LEFT OUTER', 'table_name' => 'workcentres AS wcentre', 'conditions' => '`wcentre`.`id` = `job`.`working_branch`'),
						2 => array('join' => 'LEFT OUTER', 'table_name' => 'kv_empl_departments AS dept', 'conditions' => '`dept`.`id` = `job`.`department`'),						
					), 
					array('`info`.`empl_id`, CONCAT(`info`.`empl_firstname`, `info`.`empl_lastname`) AS empl_name, `dept`.`description`, `wcentre`.`name`'), array(), array('info.empl_id' => 'asc'));
					
		$sql_count = count($all_empl); 		
	}
	
	$dept_name = '';
	$total_present = $total_absent = $total_od = $total_hd = $total_gl = $total_cl = $total_ml = 0;
	$finalar = array();
	if(!empty($all_empl)){
		//display_error(json_encode($all_empl));
		foreach( $all_empl as $res ){
			foreach($selected_empl as $single){
				if($res['empl_id'] == $single['empl_id']){
					$res[(int)$Day] = $single[(int)$Day];
					$res[(int)$Day.'vj_in'] = $single[(int)$Day.'vj_in'];
					$res[(int)$Day.'vj_out'] = $single[(int)$Day.'vj_out'];
					$total_present += ($res[(int)$Day] == 'P' ? 1 : 0 );
					$total_od += ($res[(int)$Day] == 'OD' ? 1 : 0 );
					$total_hd += ($res[(int)$Day] == 'HD' ? 1 : 0 );
					$total_gl += ($res[(int)$Day] == 'AL' ? 1 : 0 );
					$total_cl += ($res[(int)$Day] == 'CL' ? 1 : 0 );
					$total_ml += ($res[(int)$Day] == 'ML' ? 1 : 0 );
					break;
				}
			}
			$finalar[] = $res;
		}
	}
	$total_absent = $sql_count - ($total_present+$total_od +$total_hd + $total_gl + $total_cl + $total_ml);
		echo "<center><a href='javascript:printDiv();'>".trans("Print")."</a></center>\n";
	start_table(TABLESTYLE_NOBORDER);
		
		echo '<tr><td>'.trans("Total Present").' :</td>  <td>'.$total_present.'</td> <td>'.trans("Total Absent").'</td> <td>'.$total_absent.'<td><td></td> <td><td></tr>';
		echo '<tr><td> '.trans("On Duty").' :</td> <td>'.$total_od.'</td><td>'.trans("Half Day").' :</td>  <td>'.$total_hd.'</td> <td></td> <td><td></tr>';
		echo '<tr><td> '.trans("Earned Leave").' :</td> <td>'.$total_gl.'</td><td>'.trans("Common Leave").' :</td>  <td>'.$total_cl.'</td> <td>'.trans("Medical Leave").'</td> <td>'.$total_ml.'<td></tr>';

	end_table();
	br();
	if(count($finalar) > 0 ) { 
	
		start_table(TABLESTYLE, "width:60%");	
		echo '<tr> <td class="tableheader">Empl ID</td> <td class="tableheader"> Employee Name </td> <td class="tableheader"> Department</td> <td class="tableheader"> Work Centre </td>  <td class="tableheader"> Attendance</td> <td  class="tableheader"> In Time</td> <td  class="tableheader"> Out Time</td> </tr>' ;
		$row_class ="evenrow";
		foreach($finalar as $single){
			//if($single[(int)$Day] != '' )
			echo '<tr class="'.$row_class.'"> <td>'.$single['empl_id'].'</td> <td> '.$single['empl_name'].' </td> <td> '.$single['description'].'</td> <td>'.$single['name'].'</td> <td> '.(isset($single[(int)$Day]) ? ($single[(int)$Day] != '' ? $single[(int)$Day] : 'A' ): 'A').'</td> <td > '.((isset($single[(int)$Day.'vj_in']) && $single[(int)$Day.'vj_in'] != '00:00:00' )? $single[(int)$Day.'vj_in'] : '').'</td> <td >'.((isset($single[(int)$Day.'vj_out']) && $single[(int)$Day.'vj_out'] != '00:00:00') ? $single[(int)$Day.'vj_out'] : '').'</td></tr>' ;
			if($row_class == 'evenrow')
				$row_class='oddrow';
			else
				$row_class ='evenrow';
		}
		end_table();
		
		echo '<center><p style="color: #F44336;"><b>Note</b>: * AL-Earned Leave, CL-Common Leave, ML-Medical Leave</p></center>';

	} else 
		display_warning(trans("No Employee Information found to display"));

div_end();
	end_form();?>
	<style>
	#details table.tablestyle { min-width:1330px; } 
	</style><?php 
	echo "
		<script>
	function printDiv() {
	var divName ='details';
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
</script>

\n";

end_page(); ?>