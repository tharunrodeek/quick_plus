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
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/db_pager.inc");
page(trans("Attendance Inquiry"));
 $dim = get_company_pref('use_dimension');
 check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));
 
 simple_page_mode(true);
//----------------------------------------------------------------------------------------
	$new_item = get_post('selected_id')=='' || get_post('cancel') ;
	$month = get_post('month','');
	$year = get_post('year','');
	if (isset($_GET['selected_id'])){
		$_POST['selected_id'] = $_GET['selected_id'];
	}
	$selected_id = get_post('selected_id');
	 if (list_updated('selected_id')) {
		$_POST['empl_id'] = $selected_id = get_post('selected_id');
	    $Ajax->activate('details');
	}
	if (isset($_GET['month'])){
		$_POST['month'] = $_GET['month'];
	}
	if (isset($_GET['year'])){
		$_POST['year'] = $_GET['year'];
	}
	
	if (list_updated('month')) {
		$month = get_post('month');   
		$Ajax->activate('details');
	}

//$month = date("m");
//----------------------------------------------------------------------------------------

	start_form(true);
		start_table(TABLESTYLE_NOBORDER);
			echo '<tr>';
				kv_fiscalyears_list_cells(trans("Fiscal Year:"), 'year', null, true);
			 	kv_current_fiscal_months_list_cell(trans("Months"), "month", null, true);
			 	department_list_cells(trans("Select a Department")." :", 'selected_id', null,	trans("All Departments"), true, check_value('show_inactive'));
				
				if ($dim >= 1){
					dimensions_list_cells(trans("Dimension")." 1", 'dimension_id', null, true, " ", false, 1, true);
					//if ($dim > 1)
						//dimensions_list_cells(trans("Dimension")." 2", 'dimension2_id', null, true, " ", false, 2, true);
					//else
						//hidden('dimension2_id', 0);
				}
				if ($dim < 1)
					hidden('dimension_id', 0);
				//if ($dim < 2)
					//hidden('dimension2_id', 0);
			
				$new_item = get_post('selected_id')=='';
		 	echo '</tr>';
	 	end_table(1);

	 	if (get_post('_show_inactive_update')) {
			$Ajax->activate('month');
			$Ajax->activate('details');
			$Ajax->activate('selected_id');		
			set_focus('month');
		}
		if($month==null){			 
			$month = $_POST['month'];
		}
		if($year==null){			 
			$year = $_POST['year'];
		}
		//echo $month;

		$all_settings =  GetAll('kv_empl_option');
		$hrmsetup = array(); 
		foreach($all_settings as $settings){
			//$hrmsetup[$settings['option_name']] = $settings['option_value'];
			$data_offdays = @unserialize(base64_decode($settings['option_value']));
			if ($data_offdays !== false) {
				$hrmsetup[$settings['option_name']] = unserialize(base64_decode($settings['option_value']));
			} else {
				$hrmsetup[$settings['option_name']] = $settings['option_value']; 
			}
		}
		$months_with_years_list = kv_get_months_with_years_in_fiscal_year($year);
 		$ext_year = date("Y", strtotime($months_with_years_list[(int)get_post('month')]));
		$total_days =  date("t", strtotime($ext_year."-".$month."-01"));
		div_start('details');
		
			$selected_empl = kv_get_employees_list_based_on_dept($selected_id);
			//$selected_empl_attend_details=kv_get_attend_details($selected_id);
			start_table(TABLESTYLE);	 

			echo "<tr><td rowspan=2 class='tableheader'>" . trans("Empl ID") . "</td><td rowspan=2 class='tableheader'>" . trans("Empl Name") . "</td>					
					<td colspan=".$total_days." class='tableheader'>" . trans(date("Y - F", strtotime($ext_year."-".$month."-01"))) . "</td>
					<td rowspan=2 class='tableheader'>" . trans("Payable (Hours)")."</td>
					<td rowspan=2 class='tableheader'>" .trans("OT (Hours)")."</td>
					<td rowspan=2 class='tableheader'>" .trans("Special OT (Hours)") . "</td></tr><tr>";
					$weekly_off = $hrmsetup['weekly_off'];					
					$off_count = 0;
					$weekly_offdate = $gazatted_holiday_count = 0 ; 
					if($hrmsetup['BeginDay'] > 1)
						$kv = $hrmsetup['BeginDay'];
					else
						$kv = 1;					
					$sql_query = "SELECT date FROM ".TB_PREF."kv_empl_gazetted_holidays WHERE (date BETWEEN '".date("Y-m-d", strtotime($ext_year."-".$month."-".$hrmsetup['BeginDay']))."' AND '".date("Y-m-d", strtotime($ext_year."-".$month."-".$hrmsetup['EndDay']))."' ) ";
					$first_gazetted_leaves = array();
					$result = db_query($sql_query, "Can't get results");
					if(db_num_rows($result)){
						while($cont = db_fetch($result))
							$first_gazetted_leaves[] = $cont[0];
					}						
					for($kv; $kv<=$total_days; $kv++){
						$day_letters = date("D", strtotime($ext_year."-".$month."-".$kv)) ; 
						if( in_array($day_letters, $weekly_off) ){
							$style_head = "style='background-color:#e0db98 !important;'";
							if($weekly_offdate==0)
								$weekly_offdate=$kv;
							$off_count++;
						}elseif( !empty($first_gazetted_leaves) && in_array(date("Y-m-d", strtotime($ext_year."-".$month."-".$kv)), $first_gazetted_leaves)){
								$style_head = "style='background-color:#FF9800'";
								$gazatted_holiday_count++;
						}else{
							$style_head = '';
						}						
						echo "<td class='tableheader' >". trans(date("l", strtotime($ext_year."-".$month."-".$kv))) .'<br>'. date("d", strtotime($ext_year."-".$month."-".$kv))."</td>";
					}									
					echo "</tr>";
					$beginDay = date("Y-m-d", strtotime($ext_year."-".$month."-01"));
					while ($row = db_fetch_assoc($selected_empl)) {	
					$weekly_off1 = GetSingleValue('kv_empl_job', 'weekly_off', array('empl_id'=>$row['empl_id']));			
						if(isset($_POST['dimension_id']) && $_POST['dimension_id'] > 0 ){
							$sql = "SELECT * FROM ".TB_PREF."kv_empl_attendance WHERE empl_id=".db_escape($row['empl_id'])." AND a_date >= '".$beginDay."' AND a_date <= '".date("Y-m-t", strtotime($ext_year."-".$month."-01"))."' AND dimension = ".db_escape($_POST['dimension_id'])." AND dimension2=".(isset($_POST['dimension2_id']) ? db_escape($_POST['dimension2_id']) : 0 )." GROUP BY a_date ORDER BY a_date ASC";
							$res = db_query($sql, "Can't get attendance");
							$final = array();
							while($row2 = db_fetch_assoc($res)){
								$final[$row2['a_date']] = $row2;
							}	
							echo '<tr> <td>'.$row['empl_id'].'</td><td>'.$row['empl_name'].'</td>';
							 
							$duration = $OT_hours = 0;
							for($vj=1; $vj <= $total_days; $vj++){							
								if(isset($final[date("Y-m-d", strtotime($ext_year."-".$month."-".$vj))])){
									$datee = $final[date("Y-m-d", strtotime($ext_year."-".$month."-".$vj))] ;
									echo  '<td>'.$datee['code'].'<span>'.($datee['code'] == 'P' ? date('h:iA', strtotime($datee['in_time'])).' '.date('h:iA', strtotime($datee['out_time'])) : '' ) .'</span></td>';
									$duration += $datee['duration'];
									$OT_hours += $datee['ot'];
								} else{
									$day_letters = date("D", strtotime($ext_year."-".$month."-".$vj)) ;									
									if( in_array($day_letters, $weekly_off1) )								
										echo '<td style="background: #fed;"> '.trans("OFF").' </td>';
									else
										echo '<td> </td>';
								}
							} 
							$hours = floor($duration / 3600);
							$mins = floor($duration / 60 % 60);
							$Payable_hours = $hours.':'.$mins;
								
							$ot_h = floor($OT_hours / 3600);
							$ot_min = floor($OT_hours / 60 % 60);
							$OT_hours_mins = $ot_h.':'.$ot_min; 							
							echo '<td>'.$Payable_hours.' - '.$OT_hours_mins.'</td></tr>';
					} else {
							$sql = "SELECT * FROM ".TB_PREF."kv_empl_attendance WHERE empl_id=".db_escape($row['empl_id'])." AND a_date >= '".$beginDay."' AND a_date <= '".date("Y-m-t", strtotime($ext_year."-".$month."-01"))."' GROUP BY a_date, dimension ORDER BY a_date ASC";
							$res = db_query($sql, "Can't get attendance");
							$final = array();
							while($row2 = db_fetch_assoc($res)){
								$final[$row2['a_date']][$row2['dimension']] = $row2;
							}	
							echo '<tr> <td>'.$row['empl_id'].'</td><td>'.$row['empl_name'].'</td>';
							$duration = $OT_hours = $SplOT = 0;
							for($vj=1; $vj <= $total_days; $vj++){							
								if(isset($final[date("Y-m-d", strtotime($ext_year."-".$month."-".$vj))])){
									echo '<td>';
									foreach($final[date("Y-m-d", strtotime($ext_year."-".$month."-".$vj))] as $dim => $val){
										$datee = $final[date("Y-m-d", strtotime($ext_year."-".$month."-".$vj))] ;
										echo $val['code'].'<span>'.($val['code'] == 'P' ? date('h:iA', strtotime($val['in_time'])).' '.date('h:iA', strtotime($val['out_time'])) : '') .'</span>';
										$duration += $val['duration'];
										$OT_hours += $val['ot'];
										$SplOT += $val['sot'];
									}
									echo '</td>';
								} else{
									$day_letters = date("D", strtotime($ext_year."-".$month."-".$vj)) ; 
									if(!is_array($weekly_off1))
										$weekly_off1=array($weekly_off1);
									if( in_array($day_letters, $weekly_off1))								
										echo '<td style="background: #fed;"> '.trans("OFF").' </td>';
									else
										echo '<td> </td>';
								}
							} 
							$hours = floor($duration / 3600);
							$mins = floor($duration / 60 % 60);
							$Payable_hours = $hours.':'.$mins;
								
							$ot_h = floor($OT_hours / 3600);
							$ot_min = floor($OT_hours / 60 % 60);
							$OT_hours_mins = $ot_h.':'.$ot_min; 

							$sot_h = floor($SplOT / 3600);
							$sot_min = floor($SplOT / 60 % 60);
							$SOT_hours_mins = $sot_h.':'.$sot_min; 
							echo '<td>'.$Payable_hours.'</td><td> '.$OT_hours_mins.'</td><td> '.$SOT_hours_mins.'</td></tr>';						
					}
				}
			end_table();		
		div_end();
	end_form();?>
	<style>
	#details table.tablestyle { min-width:1330px; } 
	#details table.tablestyle td { text-align:center; border: 1px solid #cccccc; padding: 1px;  } 
	#details table.tablestyle td span {font-size:10px;display:block;  } 
	table tr td {   line-height: 1.5em;}
	</style><?php 
end_page(); ?>
