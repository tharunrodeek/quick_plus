<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_ATTENDANCE';
$path_to_root = "../..";

include($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
global $hrm_empl_leave_types;

if (isset($_GET['vw'])){
	$view_id = $_GET['vw'];
	$empl_row = GetRow('kv_empl_leave_applied', array('id' => $view_id));
    	header("Content-type: application/octet-stream");
    	//header('Content-Length: '.$row['filesize']);
 		header('Content-Disposition: attachment; filename='.$empl_row['filename']);
 		//display_error( company_path(). "/attachments/empldocs/".$row['unique_name']);
	   	echo file_get_contents(company_path(). "/attachments/empldocs/".$empl_row['empl_id']."/".$empl_row['filename']);
    	exit();
	//}
		
}

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

page(trans($help_context = "Leave Request & Approval"), @$_REQUEST['popup'], false, "", $js);

 /*echo $_SESSION["wa_current_user"]->user;
die;*/

function GetLeaveBalances($empl_id, $date, $type) {
	$f_year = GetRow('fiscal_year', array('begin' => array(date2sql($date), '<'), 'end' => array(date2sql($date), '>')));
	$sql_sl = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_attendance WHERE empl_id = ".db_escape($empl_id)." AND a_date >= ".db_escape($f_year['begin'])." AND a_date <= ".db_escape($f_year['end'])." AND code = ".db_escape($type);
	$res = db_query($sql_sl, "Can't get actual al");
	//dd($sql_sl);
	if(db_num_rows($res)> 0  && $srow = db_fetch($res)){
		return $srow[0];
	} else
		return 0;
}


$employee_id = get_post('employee_id'); //GetSingleValue('users', 'employee_id', array('id' => $_SESSION["wa_current_user"]->user));
simple_page_mode(true);
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){
	$input_error = 0;
	if (strlen($_POST['reason']) == 0) {
		$input_error = 1;
		display_error(trans("The Reason cannot be empty."));
		set_focus('reason');
	}

	if (strlen($_POST['days']) == 0) {
		$input_error = 1;
		display_error(trans("The Days cannot be empty."));
		set_focus('days');
	}
	$GetLeaveBalances = GetLeaveBalances($_POST['empl_id'], $_POST['date'], $_POST['leave_type']);
	//$get_leave_type_employe=GetAvailableleaveBalanceFromLeaveType($_POST['empl_id']);
	 //dd($_POST);
	/*if(($GetLeaveBalances+$_POST['days'] ) > $_POST[$_POST['leave_type']] && $_POST['Leave_type'] != 'SL'){
		$input_error = 1;
		display_error(trans("The maximum Limit reached for the selected leave type.Allocated Leave Days -".$_POST[$_POST['leave_type']]." And Given Leaves - ".$GetLeaveBalances));
		set_focus('days');		
	} elseif($_POST['Leave_type'] == 'SL' && $GetLeaveBalances+$_POST['days']  > ($_POST[$_POST['leave_type']]+ $_POST['SLH'])){
		$input_error = 1;
		display_error(trans("The maximum Limit reached for the selected leave type.Allocated Leave Days -".($_POST[$_POST['leave_type']]+$_POST['SLH'])." And Given Leaves - ".$GetLeaveBalances));
		set_focus('days');	
	}*/
	if ($input_error != 1)	{
		begin_transaction();
    	if ($selected_id != -1)     	{
    		Update('kv_empl_leave_applied', array('id' => $selected_id), array('reason' => $_POST['reason'], 'leave_type' => $_POST['leave_type'], 'days' => $_POST['days'], 'date' => array($_POST['date'], 'date'),  'empl_id' => $_POST['empl_id'], 'year' => date('Y'), 'status' => $_POST['status']));
			$note = trans("The Applied Leave was Edited and Approved");
    	}   else  	{
    		$selected_id = Insert('kv_empl_leave_applied', array('reason' => $_POST['reason'], 'leave_type' => $_POST['leave_type'], 'days' => $_POST['days'], 'date' => array($_POST['date'], 'date'), 'empl_id' => $_POST['empl_id'], 'year' => $_POST['attendance_year'], 'status' => $_POST['status'],'created_by'=>$_SESSION["wa_current_user"]->user));
			$note = trans("The leave has applied successfully");
    	}
    	UploadHandle($selected_id, $_POST['empl_id']);


    	if($_POST['status'] == 1){
			$leave_date = $date = date2sql($_POST['date']);
			$shift_id = GetSingleValue('kv_empl_job', 'shift', array('empl_id' => $_POST['empl_id']));
			$weekly_off = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'weekly_off'));
			for($kv=1;$kv<=$_POST['days']; $kv++){

				$day_letters =date("D", strtotime($date));
				//if(in_array($day_letters, $weekly_off ))
					//continue;
				$array = array('in_time' => '00:00:00', 'out_time' => '00:00', 'duration' => 0, 'ot' => 0, 'sot' => 0, 'code' => $_POST['leave_type']);
                $employee_code = GetSingleValue('kv_empl_info', 'empl_id', array('id' => get_post('empl_id')));
				Update('kv_empl_attendance', array('empl_id' => $employee_code, 'shift' => '8', 'a_date' => $date), $array);
				$date = date('Y-m-d', strtotime($leave_date. ' + '.$kv.' days'));					
			}
    	}
		commit_transaction();
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete'){
	$cancel_delete = 0;
	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'
	if (GetSingleValue('kv_empl_leave_applied', 'status', array('id' => $selected_id)) == 1)	{
		$cancel_delete = 1;
		display_error(trans("Cannot delete this Leave because It's already Accepted."));
	} 
	if ($cancel_delete == 0) {
		Delete('kv_empl_leave_applied', array('id' => $selected_id));
		display_notification(trans("Selected Leave has been deleted"));
	} //end if Delete department
	$Mode = 'RESET';
} 

if ($Mode == 'RESET'){
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}
$admin_role = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'master_role'));
//-------------------------------------------------------------------------------------------------
start_form(true);
	start_table();
		//kv_fiscalyears_list_row(trans("Fiscal Year:"),'attendance_year', null, true);
	end_table();
	br();
if($_SESSION['wa_current_user']->access != $admin_role)
{
    filter_leave_types(trans("Check Assigned And Applied Leaves : "),'filter_leave_types', null, true);
}

    br();

if($_SESSION['wa_current_user']->access == $admin_role)
    {

        $result = GetAll('kv_empl_leave_applied', array('year' =>date('Y')));

    }
else {
	$result = array();
	$empl_id = GetSingleValue('users', 'employee_id', array('id' => $_SESSION['wa_current_user']->user));

	if($_POST['filter_leave_types']=='0')
    {
            $sql = "SELECT * FROM " .TB_PREF."kv_empl_leave_applied 
	        WHERE empl_id IN ( SELECT id FROM ".TB_PREF."kv_empl_info 
	        WHERE  user_id=".$_SESSION['wa_current_user']->user." )";
    }

	if($_POST['filter_leave_types']=='1')
    {
        $edmp_sql="SELECT id FROM " .TB_PREF."kv_empl_info where user_id=".$_SESSION['wa_current_user']->user." ";
        $result_d = db_query($edmp_sql, "Can't get your allowed user details");
        $rowData= db_fetch($result_d);

        $sql = "SELECT * FROM " .TB_PREF."kv_empl_leave_applied 
	        WHERE empl_id IN ( SELECT id FROM ".TB_PREF."kv_empl_info 
	        WHERE  report_to=".$rowData[0]." )";
    }

	if($_POST['filter_leave_types']=='2')
    {
        $edmp_sql="SELECT id FROM " .TB_PREF."kv_empl_info where user_id=".$_SESSION['wa_current_user']->user." ";
        $result_d = db_query($edmp_sql, "Can't get your allowed user details");
        $rowData= db_fetch($result_d);

        $sql = "SELECT * FROM " .TB_PREF."kv_empl_leave_applied 
	        WHERE empl_id IN ( SELECT id FROM " .TB_PREF."kv_empl_info 
                        WHERE  report_to IN (SELECT id 
            FROM " .TB_PREF."kv_empl_info   
            WHERE leave_request_forward=".$rowData[0]." )) and status<>'0'";

        //echo  $sql;
    }


	$res = db_query($sql, "Can't get your allowed user details");
	while($row= db_fetch($res)){
		$result[] = $row;
	}
}
if(!empty($result)){
    div_start('Employee_applied_leaves');
	start_table(TABLESTYLE, "width=60%");
	$th = array(trans("Employee ID"), trans("Employee Name"), trans("Leave Type"), trans("Reason"), trans("Date"), trans("Days"), trans("Status"), "", "");

	table_header($th);
	$k = 0; 

	foreach($result as $myrow) {

		alt_table_row_color($k);
		$name_and_dept = get_empl_name_dept($myrow['empl_id']);
		label_cell($name_and_dept["EMployeeCode"]);
		label_cell($name_and_dept['name']);
		label_cell($hrm_empl_leave_types[$myrow["leave_type"]]);
		label_cell($myrow["reason"]);
		label_cell(sql2date($myrow["date"]));
		label_cell($myrow["days"]);
		//label_cell($myrow["status"]);
		label_cell(($myrow["status"]== 1 ? 'Accepted' :  ($myrow["status"]== 0 ? 'Pending' : 'Rejected')));
		if($myrow['status'] == 1 && $myrow['date'] < date('Y-m-d')){
			label_cell("");
			label_cell("");
		} else {
			edit_button_cell("Edit".$myrow["id"], trans("Edit"));
			delete_button_cell("Delete".$myrow["id"], trans("Delete"));
		}
		end_row();
	}
	end_table(1);
	div_end();

} else {
	display_warning(trans("Sorry No Leave Details Found for you"));
}
div_start('Leave_details');
//-------------------------------------------------------------------------------------------------

	start_table(TABLESTYLE2);
		table_section_title(trans("Leave Details"));
		if(get_post('empl_id'))
			$_POST['empl_id'] = get_post('empl_id');
		else
			$_POST['empl_id'] = 0;
		if ($selected_id != -1) {
			if ($Mode == 'Edit') { //editing an existing department
				$myrow = GetRow('kv_empl_leave_applied', array('id' =>$selected_id));

				$_POST['leave_type']  = $myrow["leave_type"];
				$_POST['reason']  = $myrow["reason"];
				$_POST['date']  = sql2date($myrow["date"]);
				$_POST['days']  = $myrow["days"];
				$_POST['empl_id']  = $myrow["empl_id"];
				$_POST['status']  = $myrow["status"];
				$_POST['filename']  = $myrow["filename"];
			}
			hidden("selected_id", $selected_id);
			hidden('empl_id', $_POST['empl_id']);
            $employee_code = GetSingleValue('kv_empl_info', 'empl_id', array('id' => get_post('empl_id')));
			label_row(trans("ID"),  $employee_code);
			label_row(trans("Name"), kv_get_empl_name($_POST["empl_id"]));
		} else{
			start_row();
			employee_list_cells(trans("Select an Employee")." :", 'empl_id', null,	trans("Select Employee"), true, check_value('show_inactive'),false, false,true);
			end_row();
		}
		 //dd( get_post('empl_id'));
		$job_row = GetRow('kv_empl_job', array('empl_id' => get_post('empl_id')));
		//if(get_post('empl_id') > 0 ) {
			
			hidden('al', $job_row['al']);
			hidden('sl', $job_row['sl']);
			hidden('slh', $job_row['slh']);
			hidden('ml', $job_row['ml']);
			hidden('hl', $job_row['hl']);
		//}
		//hrm_empl_leave_type_row(trans("Leave Type"), "leave_type");
        get_leave_types(trans("Leave Type:"), "leave_type");
		date_row(trans("Date") . ":", 'date');
		text_row(trans("Days:"), 'days', null, 5, 10);
		textarea_row(trans("Reason:"), 'reason', null, 35, 5);
		hidden('department', $job_row['department']);

        /*---------------------Check user has permission for update leave status--------*/
            $edmp_sql="SELECT leave_update_permission FROM " .TB_PREF."kv_empl_info where user_id=".$_SESSION['wa_current_user']->user." ";
            $result_d = db_query($edmp_sql, "Can't get your allowed user details");
            $rowData= db_fetch($result_d);
        /*--------------------------------------END--------------------------------------*/
        if($rowData[0]=='1') {
            accept_reject_pending_list_row(trans("Status"), 'status');
        }
		if(isset($_POST['filename']) && $_POST['filename'] != null){
			label_row(trans("Attachment"), viewer_link($_POST["filename"], 'modules/ExtendedHRM/leave_approval.php?vw='.$myrow["id"]));
		}
		kv_doc_row(trans("Select Docs") . ":", 'kv_attach_name', 'kv_attach_name');
		//print_r(get_post('empl_id'));
		$report_to = GetSingleValue('kv_empl_info', 'report_to', array('id' => get_post('empl_id')));

		label_row(trans("Report To:"), GetSingleValue('kv_empl_info', 'CONCAT(`empl_firstname`, " ", `empl_lastname`)', array('id' => $report_to)));
	end_table(1);
	div_end();
	submit_add_or_update_center($selected_id == -1, '', 'both');
end_form();
end_page();
if(list_updated('empl_id')){
	$Ajax->activate('Leave_details');
}

function UploadHandle($id, $empl_id){
		if (isset($_FILES['kv_attach_name']) && $_FILES['kv_attach_name']['name'] != '') {
			$max_image_size = 5000;
			$result = $_FILES['kv_attach_name']['error'];
			$upload_file = 'Yes'; 
			$attr_dir = company_path().'/attachments' ; 
			if (!file_exists($attr_dir)){				
				mkdir($attr_dir);
			}
			$dir = $attr_dir.'/empldocs/'.$empl_id.'/';
			if (!file_exists($dir)){
				mkdir($dir);
			}	
			/*$doc_ext = substr(trim($_FILES['kv_attach_name']['name']), strlen($_FILES['kv_attach_name']['name'])-3); 
			if($doc_ext == 'ocx' ) {
				$doc_ext = substr(trim($_FILES['kv_attach_name']['name']),strlen($_FILES['kv_attach_name']['name'])-4); 
			}*/
			$filename = basename($_FILES['kv_attach_name']['name']);
			$tmp = explode('.', $filename);
			$ext = strtolower(end($tmp));
			
			if(in_array($ext, array('docx','doc', 'pdf', 'jpg', 'jpeg', 'gif', 'png', 'bmp', 'rtf', 'txt'))){	
				
				$filesize = $_FILES['kv_attach_name']['size'];
				$filetype = $_FILES['kv_attach_name']['type'];
				
				$unique_name = $id.'-'.$filename;

				if ( $filesize > ($max_image_size * 1024)) { //File Size Check
					display_warning(trans('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $max_image_size);
					$upload_file ='No';
				}elseif (file_exists($dir."/".$unique_name)){
					$result = unlink($dir."/".$unique_name);
					if (!$result) 	{
						display_error(trans('The existing Bill could not be removed'));
						$upload_file ='No';
					}
				}else {
					$attach = GetRow('kv_empl_leave_applied', array('id' => $id));
					$attr_dir = company_path().'/attachments/empldocs/'.$attach['empl_id'].'/'.$attach['filename']; 
					if($attach['filename'] && file_exists($attr_dir) && !is_dir($attr_dir))
						unlink($attr_dir);
				}
					
				if ($upload_file == 'Yes'){
					$result = move_uploaded_file($_FILES['kv_attach_name']['tmp_name'], $dir."/".$unique_name);			
				}
				Update('kv_empl_leave_applied', array('id' => $id), array( 'filename' => $unique_name));
			} else 
				display_error(trans("The Selected File format is not supported, try files within this format (.jpg, png, doc,docx, rtf,pdf)"));
		}
	}
?>
<style>
	table.tablestyle2 { width: 100%; }
	span#_empl_id_sel {  display: inline-block; }
    #Employee_applied_leaves
    {
        height: 200px;
        overflow: auto;
    }
    #Leave_details
    {
        margin-top: 2%;
    }
</style>