<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_LEAVE_ENCASHMENT';
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

page(trans("Leave Encashment Inquiry"));
 
simple_page_mode(true);

check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));
//----------------------------------------------------------------------------------------
	$new_item = get_post('dept_id')=='' || get_post('cancel') ;
	$month = get_post('month','');
	$year = get_post('year','');
	if (isset($_GET['dept_id'])){
		$_POST['dept_id'] = $_GET['dept_id'];
	}
	$dept_id = get_post('dept_id');
	 if (list_updated('dept_id')) {
		$_POST['empl_id'] = $dept_id = get_post('dept_id');
	    $Ajax->activate('details');
	}
	if (isset($_GET['month'])){
		$_POST['month'] = $_GET['month'];
	}
	if (isset($_GET['year'])){
		$_POST['year'] = $_GET['year'];
	}
	
	if (list_updated('empl_id') || get_post('empl_id') >0 ) {		
		$Ajax->activate('details');
	}

//$month = date("m");
//----------------------------------------------------------------------------------------
	start_form(true);		
		start_table(TABLESTYLE_NOBORDER);
			echo '<tr>';
				kv_fiscalyears_list_cells(trans("Fiscal Year:"), 'year', null, true);
			 	department_list_cells(trans("Select a Department:"), 'dept_id', null, trans("All Departments"), true, check_value('show_inactive'));
				employee_list_cells(trans("Select an Employee"). " :", 'empl_id', null,	trans("Select an Employee"), true, check_value('show_inactive'), false, false,true);
				$new_item = get_post('dept_id')=='';
		 	echo '</tr>';
	 	end_table(1);
		div_start('details');
			$filter_arr = array();
			if(get_post('dept_id') > 0){				
				$filter_arr['`encash`.`department`'] = get_post('dept_id');
				if(get_post('year')> 0 ){
					$filter_arr['`encash`.`year`'] = get_post('year');
				}				
			}else {
				if(get_post('year')> 0 ){
					$filter_arr['`encash`.`year`'] = get_post('year');
				}
			}
			if(get_post('empl_id')>0){
				$filter_arr['`info`.`empl_id`'] = get_post('empl_id');
			}
			$encashs = GetDataJoin('kv_empl_leave_encashment AS encash', array( 
					0 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info','conditions' =>'`info`.`empl_id` = `encash`.`empl_id`')), 
				array('`info`.`empl_id`, CONCAT(`info`.`empl_firstname`, " ", `info`.`empl_lastname`) AS name,  `encash`.`amount`, `encash`.`month`, `encash`.`payable_days`, IF(`encash`. `carry_forward` = 0, "Paid", "Forwarded"), `encash`. `date`, `encash`.`year`'), $filter_arr, array('info.empl_id' => 'asc'));
				
			start_table(TABLESTYLE);
				echo  "<tr> <td class='tableheader'>" . trans("Empl ID") . "</td>					
					<td class='tableheader'>" . trans("Employee Name") . "</td>
					<td class='tableheader'>" . trans("Amount") . "</td>
					<td class='tableheader'>" . trans("Month Paid") . "</td>
					<td class='tableheader'>" . trans("AL Days") . "</td>
					<td class='tableheader'>" . trans("Status") . "</td>					
					<td class='tableheader'>" . trans("Date") . "</td></tr>";
					foreach($encashs as $encash_single) {
						$date_of_end = date('Y-m-d', strtotime("+".$encash_single[5]." months", strtotime($encash_single[7]))); 
						echo '<tr style="text-align:center"><td>'.$encash_single[0].'</td><td>'.$encash_single[1].'</td><td>'.price_format($encash_single[2]).'</td><td style="text-align:right;">'.price_format($encash_single[3]).'</td><td style="text-align:right;">'.$encash_single[4].'</td><td>'.$encash_single[5].'</td><td>'.sql2date($encash_single[6]).'</td><tr>';
					}
			end_table(1);		
		div_end();
	end_form(); 
end_page();
 
?>