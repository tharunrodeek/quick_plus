<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_EMPL_INFO';
$path_to_root="../../..";
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();
include_once($path_to_root . "/admin/db/attachments_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
global $hrm_empl_relation;
if (isset($_GET['vw']))
	$view_id = $_GET['vw'];
else
	$view_id = find_submit('view');
if ($view_id != -1){	//echo $view_id;
	$row = GetRow('kv_empl_family', array('id' => $view_id));
	if ($row['unique_name'] != ""){
		if(in_ajax()) {
			$Ajax->popup($_SERVER['PHP_SELF'].'?vw='.$view_id);
		} else {
			$type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';	
    		header("Content-type: ".$type);
    		header('Content-Length: '.$row['filesize']);
 			header("Content-Disposition: inline");
 			//display_error( company_path(). "/attachments/empldocs/".$row['unique_name']);
	    	echo file_get_contents(company_path(). "/attachments/empldocs/".$row['unique_name']);
    		exit();
		}
	}	
}
if (isset($_GET['dl']))
	$download_id = $_GET['dl'];
else
	$download_id = find_submit('download');

if ($download_id != -1){
	$row = GetRow('kv_empl_family', array('id' => $download_id));
	if ($row['unique_name'] != ""){
		if(in_ajax()) {
			$Ajax->redirect($_SERVER['PHP_SELF'].'?dl='.$download_id);
		} else {
			
    		header("Content-type: 'application/octet-stream' ");
	    	//header('Content-Length: '.$row['filesize']);
    		header('Content-Disposition: attachment; filename='.$row['filename']);
    		echo file_get_contents(company_path()."/attachments/empldocs/".$row['unique_name']);
	    	exit();
		}
	}	
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
page(trans($help_context = "Employee Family Data"), false, false, "", $js);

simple_page_mode(true);

check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));

//----------------------------------------------------------------------------------------
if (isset($_GET['empl_id']))
	 $empl_id = $_POST['empl_id'] = $_GET['empl_id'];

if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM'){
		//if(!isset($max_image_size))
			$max_image_size = 5000000;
		$upload_file = "";
		if (isset($_FILES['kv_attach_name']) && $_FILES['kv_attach_name']['name'] != '') {
		
			$result = $_FILES['kv_attach_name']['error'];
			$upload_file = 'Yes'; 
			$attr_dir = company_path().'/attachments' ; 
			if (!file_exists($attr_dir)){
				
				mkdir($attr_dir);
			}
			$attach_dir = $attr_dir.'/empldocs';
			if (!file_exists($attach_dir)){
				mkdir($attach_dir);
			}	
			$doc_ext = substr(trim($_FILES['kv_attach_name']['name']), strlen($_FILES['kv_attach_name']['name']) - 3) ; 
			if($doc_ext == 'ocx' ) {
				$doc_ext = substr(trim($_FILES['kv_attach_name']['name']), strlen($_FILES['kv_attach_name']['name']) - 4) ; 
			}
			if(isset($_POST['unique_name']) && $_POST['unique_name'] == '')
				$kv_file_name = uniqid();			
			else
				$kv_file_name = $_POST['unique_name']; 
				
			$filename = $attach_dir."/".$kv_file_name; 
			if ( $_FILES['kv_attach_name']['size'] > ($max_image_size * 1024)) { //File Size Check
				display_warning(trans("The file size is over the maximum allowed. The maximum size allowed in KB is") . ' ' . $max_image_size);
				$upload_file ='No';
			} 
			elseif (file_exists($filename)){
				$result = unlink($filename);
				if (!$result) 	{
					display_error(trans("The existing Docs could not be removed"));
					$upload_file ='No';
				}
			}
			
			if ($upload_file == 'Yes'){				
				$actual_file_nam = $_FILES['kv_attach_name']['name'];
				$result  =  move_uploaded_file($_FILES['kv_attach_name']['tmp_name'], $filename);
				Update('kv_empl_family', array('id' => $_POST['selected_id']), array('empl_id' => $_POST['empl_id'], 'full_name' => $_POST['full_name'],  'relation' => $_POST['relation'], 'filename' => $actual_file_nam, 'unique_name' => $kv_file_name)); 
				display_notification(trans("Employee Docs has been attached!."));
			}
			$Mode = 'RESET';
			$Ajax->activate('_page_body');
	}	else {
		//$notify_from_days = GetSingleValue('kv_empl_doc_type', 'days', array('id' => $_POST['doc_type']));
		//$notify_from = add_days($_POST['exp_date'], -$notify_from_days);		
		Update('kv_empl_family', array('id' => $_POST['selected_id']), array('empl_id' => $_POST['empl_id'], 'full_name' => $_POST['full_name'],'relation' => $_POST['relation'] )); 
	}
}

if ($Mode == 'Delete'){
	$row = GetRow('kv_empl_family', array('id' => $selected_id));
	$dir =  company_path()."/attachments/empldocs";
	if (file_exists($dir."/".$row['unique_name']))
		unlink($dir."/".$row['unique_name']);
	Delete('kv_empl_family', array('id' => $selected_id));	
	display_notification(trans("Employee Docs has been deleted."));
	$Mode = 'RESET';
}

if ($Mode == 'RESET'){
	unset($_POST['trans_no']);
	unset($_POST['description']);
	$selected_id = -1;
	$Ajax->activate('Attachments');
}



function edit_link($row){
  	return '<td>'.button('Edit'.$row["id"], trans("Edit"), trans("Edit"), ICON_EDIT).'</td>';
}

function view_link($row){
  	return '<td>'.button('view'.$row["id"], trans("View"), trans("View"), ICON_VIEW).'</td>';
}

function download_link($row){
  	return '<td>'.button('download'.$row["id"], trans("Download"), trans("Download"), ICON_DOWN).'</td>';
}

function delete_link($row){
  	return '<td>'.button('Delete'.$row["id"], trans("Delete"), trans("Delete"), ICON_DELETE).'</td>';
}

function display_rows(){
	global $hrm_empl_relation;
	$all_attachments =  GetAll('kv_empl_family', array('empl_id' => get_post('empl_id')));
	if(get_post('empl_id') > 0 ){
		$all_attachments = GetAll('kv_empl_family', array('`empl_id`' => get_post('empl_id')));
	}else
		$all_attachments = '';

	start_table(TABLESTYLE, "width=60%");
    $th = array(trans("ID"),trans("Name"), trans("Relation"),  trans("Filename"), '');
    if(basename($_SERVER['PHP_SELF']) == 'employees.php')
    	unset($th[6]);
    table_header($th);
    foreach($all_attachments as $attach){
    	start_row();
    	label_cell($attach['id']);
    	label_cell($attach['full_name']);    	
    	label_cell($hrm_empl_relation[$attach['relation']] );
		label_cell($attach['filename']);
    	//if(basename($_SERVER['PHP_SELF']) != 'employees.php')
    	//	echo edit_link($attach);
    	echo download_link($attach);
    //	echo delete_link($attach);
    	end_row();
    }
    end_table();	
}

//----------------------------------------------------------------------------------------
$action = $_SERVER['PHP_SELF'];

if ($page_nested)
	$action .= "?empl_id=".get_post('empl_id');
start_form(true, false, $action);

	if (db_has_employees()) {
		if (!$page_nested){
			start_table(TABLESTYLE_NOBORDER);
			start_row();
			department_list_cells(trans("Select a Department")." :", 'dept_id', null,	trans("No Department"), true, check_value('show_inactive'));
			employee_list_cells(trans("Select an Employee")." :", 'empl_id', null,	trans("Select an Employee"), true, check_value('show_inactive'),false, false,true);
			$new_item = get_post('empl_id')=='';
			
			end_row();
			end_table();

			if (get_post('_show_inactive_update')) {
				$Ajax->activate('empl_id');
				set_focus('empl_id');
			}
		}
	}
	else{
		hidden('empl_id', get_post('empl_id'));
	}

	if(list_updated('empl_id'))
		$Ajax->activate('Attachments');

	div_start('Attachments');
	if(get_post('empl_id') > 0 ){
		br();
		display_rows();	
		br();
		$empl_details = GetRow('kv_empl_info', array('empl_id' => get_post('empl_id')));
		/*start_table(TABLESTYLE2);
		label_row(trans("Employee Id"), $empl_details['empl_id']);
		label_row(trans("Employee Name"), $empl_details['empl_firstname']);

		if ($selected_id != -1 ){	
			if($Mode == 'Edit')	{
				$row = GetRow('kv_empl_family', array('id' => $selected_id));
				$_POST['full_name']  = $row["full_name"];	
				$_POST['relation']  = $row["relation"];			
				hidden('unique_name', $row['unique_name']);	
			} 		
		} else {	
			hidden('unique_name', '');
			$_POST['full_name'] = ''; 
		}
		hidden('selected_id', $selected_id);
		text_row_ex(trans("Full Name").':', 'full_name', 40);
		hrm_empl_relation_list_row(trans("Relation").':', 'relation', null);
		kv_doc_row(trans("Select Docs") . ":", 'kv_attach_name', 'kv_attach_name');
		
		end_table(1);
		submit_add_or_update_center($selected_id == -1, '', 'process');*/
	}
	
	div_end();		

end_form();
end_page();
?>
