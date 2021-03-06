<?php
/*=======================================================\
|                        FrontHrm                        |
|--------------------------------------------------------|
|   Creator: Phương                                      |
|   Date :   09-Jul-2017                                 |
|   Description: Frontaccounting Payroll & Hrm Module    |
|   Free software under GNU GPL                          |
|                                                        |
\=======================================================*/

$page_security = 'SA_EMPL';
$path_to_root  = '../../..';

include_once($path_to_root . '/includes/db_pager.inc');
include_once($path_to_root . '/includes/session.inc');
add_access_extensions();

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

include_once($path_to_root . '/includes/ui.inc');
include_once($path_to_root . '/modules/FrontHrm/includes/frontHrm_db.inc');
include_once($path_to_root . '/modules/FrontHrm/includes/frontHrm_ui.inc');

//--------------------------------------------------------------------------

foreach(db_query(get_employees(false, true)) as $emp_row) {
	
	if(isset($_POST[$emp_row['emp_id']])) {
		
		$_SESSION['EmpId'] = $emp_row['emp_id'];
		$_POST['_tabs_sel'] = 'add';
		$Ajax -> activate('_page_body');
	}
}

$cur_id = isset($_SESSION['EmpId']) ? $_SESSION['EmpId'] : '';

$upload_file = "";
$avatar_path = company_path()."/FrontHrm/images/";
if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '') {
	
	$result = $_FILES['pic']['error'];
 	$upload_file = 'Yes';
	$filename = $avatar_path;
    
    if(!file_exists(company_path().'/FrontHrm')) {
		mkdir(company_path().'/FrontHrm');
		copy(company_path().'/index.php', company_path().'/FrontHrm/index.php');
    }
	if(!file_exists($filename)) {
		mkdir($filename);
		copy(company_path().'/index.php', $filename.'index.php');
	}
	
	$filename .= emp_img_name($cur_id).'.jpg';
	
	if($_FILES['pic']['error'] == UPLOAD_ERR_INI_SIZE) {

		display_error(trans('The file size is over the maximum allowed.'));
		$upload_file = 'No';
	}
	elseif($_FILES['pic']['error'] > 0) {

		display_error(trans('Error uploading file.'));
		$upload_file = 'No';
	}
	if((list($width, $height, $type, $attr) = getimagesize($_FILES['pic']['tmp_name'])) !== false)
		$imagetype = $type;
	else
		$imagetype = false;

	if($imagetype != IMAGETYPE_GIF && $imagetype != IMAGETYPE_JPEG && $imagetype != IMAGETYPE_PNG) {

		display_warning( trans('Only graphics files can be uploaded'));
		$upload_file = 'No';
	}
	elseif(!in_array(strtoupper(substr(trim($_FILES['pic']['name']), strlen($_FILES['pic']['name']) - 3)), array('JPG','PNG','GIF'))) {

		display_warning(trans('Only graphics files are supported - a file extension of .jpg, .png or .gif is expected'));
		$upload_file ='No';
	}
	elseif( $_FILES['pic']['size'] > ($SysPrefs->max_image_size * 1024)) {

		display_warning(trans('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $SysPrefs->max_image_size);
		$upload_file ='No';
	} 
	elseif( $_FILES['pic']['type'] == "text/plain" ) {

		display_warning( trans('Only graphics files can be uploaded'));
        $upload_file ='No';
	}
	elseif(file_exists($filename)) {

		$result = unlink($filename);
		if(!$result) {
			display_error(trans('The existing image could not be removed'));
			$upload_file ='No';
		}
	}
	if($upload_file == 'Yes')
		$result  =  move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
	
	$Ajax->activate('_page_body');
}

//--------------------------------------------------------------------------

function can_process() {
	
	if(strlen($_POST['EmpFirstName']) == 0 || $_POST['EmpFirstName'] == '') {

		display_error(trans("The employee first name must be entered."));
		set_focus('EmpFirstName');
		return false;
	}
	if(strlen($_POST['EmpLastName']) == 0 || $_POST['EmpLastName'] == '') {

		display_error(trans('Employee last name must be entered.'));
		set_focus('EmpLastName');
		return false;
	}
	if(strlen($_POST['EmpEmail']) > 0 && !filter_var($_POST['EmpEmail'], FILTER_VALIDATE_EMAIL)) {

		display_error(trans('Invalid email.'));
		set_focus('EmpEmail');
		return false;
	}
	if (!is_date($_POST['EmpBirthDate'])) {

		display_error( trans('Invalid birth date.'));
		set_focus('EmpBirthDate');
		return false;
	}
	if (!is_date($_POST['EmpHireDate']) && $_POST['EmpHireDate'] != null && $_POST['EmpHireDate'] != '00/00/0000') {

		display_error( trans('Invalid hire date.'));
		set_focus('EmpHireDate');
		return false;
	}
	if (get_post('EmpInactive') == 1) {

	    if (!is_date($_POST['EmpReleaseDate'])) {
		display_error( trans('Invalid release date.'));
		set_focus('EmpReleaseDate');
		return false;
	    }
	}
	return true;
}

//--------------------------------------------------------------------------

function can_delete($cur_id) {

	$employee = get_employees($cur_id, true);

	if($employee['emp_hiredate'] && $employee['emp_hiredate'] != '0000-00-00') {
		display_error('Employed person cannot be deleted.');
		return false;
	}
	return true;
}

//--------------------------------------------------------------------------

function id_link($row) {
	return button($row['emp_id'], $row['emp_id']);
}
function get_name($row) {
	return "<b>".button($row['emp_id'], $row['emp_first_name'].' '.$row['emp_last_name'])."</b>";
}
function gender_name($row) {
	if($row['gender'] == 0)
		return  'Female';
	elseif($row['gender'] == 1)
	    return 'Male';
	else
	    return 'Other';
}
function emp_hired($row) {
	return ($row['emp_hiredate'] == '0000-00-00') ? trans('Not hired') : "<center>".sql2date($row['emp_hiredate'])."</center>";
}
function emp_department($row) {
	
	if($row['emp_hiredate'] == '0000-00-00' || $row['department_id'] == 0)
		return trans('Not selected');
	else
		return get_departments($row['department_id'])['dept_name'];

}

function employees_table() {
	
	$_SESSION['EmpId'] = '';
	if(db_has_employee()) {
		
		$sql = get_employees(false, check_value('show_inactive'), get_post('DeptId'));
		
		start_table(TABLESTYLE_NOBORDER);
		start_row();
		department_list_cells(trans('Department').':', 'DeptId', null, trans('All departments'), true);
		check_cells(trans('Show resigned').':', 'show_inactive', null, true);
		end_row();
		end_table(1);
		
        $cols = array(
          trans('ID'),
		  'first_name' => 'skip',
          trans('Name') => array('fun'=>'get_name'),
		  trans('Gender') => array('fun'=>'gender_name'),
		  'address' => 'skip',
		  trans('Mobile') => array(),
		  trans('Email'),
		  trans('Birth') => array('type'=>'date'),
		  'notes' => 'skip',
		  trans('Hired Date') => array('fun'=>'emp_hired'),
		  trans('Department') => array('fun'=>'emp_department')
        );

        $table =& new_db_pager('student_tbl', $sql, $cols);
        $table->width = "80%";
	
	    // display_note(trans('Press name to edit employee details.'));
        display_db_pager($table);
	}
	else
		display_note(trans('No employee defined.'), 1);
}

//--------------------------------------------------------------------------

function employee_settings($cur_id) {
	global $path_to_root, $avatar_path;
	
	if($cur_id) {
		$employee = get_employees($cur_id, true);
		$_POST['EmpFirstName'] = $employee['emp_first_name'];
		$_POST['EmpLastName'] = $employee['emp_last_name'];
		$_POST['EmpGender'] = $employee['gender'];
		$_POST['EmpAddress'] = $employee['emp_address'];
		$_POST['EmpMobile'] = $employee['emp_mobile'];
		$_POST['EmpEmail'] = $employee['emp_email'];
		$_POST['EmpBirthDate'] = sql2date($employee['emp_birthdate']);
		$_POST['EmpNotes'] = $employee['emp_notes'];
		$_POST['EmpHireDate'] = sql2date($employee['emp_hiredate']);
		$_POST['DepartmentId'] = $employee['department_id'];
		$_POST['EmpSalary'] = $employee['salary_scale_id'];
		$_POST['EmpReleaseDate'] = sql2date($employee['emp_releasedate']);
		$_POST['EmpInactive'] = $employee['inactive'];
	}
	start_outer_table(TABLESTYLE2);

	table_section(1);
	hidden('emp_id');

	file_row(trans('Image File').':', 'pic', 'pic');
	$emp_img_link = '';
	$check_remove_image = false;
	if ($cur_id && file_exists($avatar_path.emp_img_name($cur_id).'.jpg')) {
		$emp_img_link .= "<img id='emp_img' alt = '[".$cur_id.".jpg".
			"]' src='".$avatar_path.emp_img_name($cur_id).
			".jpg?nocache=".rand()."'"." height='100'>";
		$check_remove_image = true;
	} 
	else 
		$emp_img_link .= "<img id='emp_img' alt = '.jpg' src='".$path_to_root."/modules/FrontHrm/images/avatar/no_image.svg' height='100'>";

	label_row("&nbsp;", $emp_img_link);
	if ($check_remove_image)
		check_row(trans('Delete Image').':', 'del_image');
	
	table_section_title(trans('Personal Information'));

	if($cur_id)
		label_row(trans('Employee Id').':', $cur_id);

	text_row(trans('First Name').':', 'EmpFirstName', get_post('EmpFirstName'), 37, 50);
	text_row(trans('Last Name').':', 'EmpLastName', get_post('EmpLastName'), 37, 50);
	gender_radio_row(trans('Gender').':', 'EmpGender', get_post('EmpGender'));
	textarea_row(trans('Address').':', 'EmpAddress', get_post('EmpAddress'), 35, 5);
	text_row(trans('Mobile').':', 'EmpMobile', get_post('EmpMobile'), 37, 30);
	email_row(trans('e-Mail').':', 'EmpEmail', get_post('EmpEmail'), 37, 100);
	date_row(trans('Birth Date').':', 'EmpBirthDate', null, null, 0, 0, -13);
	
	table_section(2);
	
	table_section_title(trans('Job Information'));
	
	textarea_row(trans('Notes').':', 'EmpNotes', null, 35, 5);
	date_row(trans('Hire Date').':', 'EmpHireDate', null, null, 0, 0, 1001);
	
	if($cur_id) {
		if($employee['emp_hiredate'] != '0000-00-00')
			department_list_row(trans('Department').':', 'DepartmentId', null, trans('Select department'));
		else {
			label_row(trans('Department').':', trans('Set hire date first'));
			hidden('DepartmentId');
		}
	}
	else
		department_list_row(trans('Department').':', 'DepartmentId', null, trans('Select department'));
		
	salaryscale_list_row(trans('Salary').':', 'EmpSalary', null, trans('Select salary scale'));
	if($cur_id) {
		check_row(trans('Resigned').':', 'EmpInactive');
		date_row(trans('Release Date').':', 'EmpReleaseDate', null, null, 0, 0, 1001);
	}
	else{
		hidden('EmpInactive');
		hidden('EmpReleaseDate');
	}
	end_outer_table(1);
	
	div_start('controls');
	
	if ($cur_id) {
		
		submit_center_first('addupdate', trans('Update Employee'), trans('Update employee details'), 'default');
		submit_return('select', get_post('emp_id'), trans('Select this employee and return to document entry.'));
		submit_center_last('delete', trans('Delete Employee'), trans('Delete employee data if have been never used'), true);
	}
	else
		submit_center('addupdate', trans('Add New Employee Details'), true, '', 'default');
	
	div_end();
}

//--------------------------------------------------------------------------

if (isset($_POST['addupdate'])) {
	
	if(!can_process())
		return;
	write_employee(
		$cur_id,
		$_POST['EmpFirstName'],
		$_POST['EmpLastName'],
		$_POST['EmpGender'],
		$_POST['EmpAddress'],
		$_POST['EmpMobile'],
		$_POST['EmpEmail'],
		$_POST['EmpBirthDate'],
		$_POST['EmpNotes'],
		$_POST['EmpHireDate'],
		$_POST['DepartmentId'],
		$_POST['EmpSalary'],
		$_POST['EmpReleaseDate'],
		$_POST['EmpInactive']
	);

	if (check_value('del_image')) {
		$filename = $avatar_path.emp_img_name($cur_id).".jpg";
		if (file_exists($filename))
			unlink($filename);
	}
	if($cur_id) {
		$_SESSION['EmpId'] = $cur_id;
		display_notification(trans('Employee details has been updated.'));
	}
	else {
		$_SESSION['EmpId'] = db_insert_id();
		$cur_id = $_SESSION['EmpId'];
		display_notification(trans('A new employee has been added.'));
	}
	
	$Ajax->activate('_page_body');
}
elseif(isset($_POST['delete'])) {

	if(!can_delete($cur_id))
		return;
	delete_employee($cur_id);
	display_notification(trans('Employee details has been deleted.'));
	$Ajax -> activate('_page_body');
}

//--------------------------------------------------------------------------

page(trans($help_context = 'Manage Employees'), false, false, '', $js);

start_form(true);

tabbed_content_start(
	'tabs',
	array(
		'list' => array(trans('Employees &List'), 999),
		'add' => array(trans('&Add/Edit Employee'), 999)
	)
);

if(get_post('_tabs_sel') == 'list')
	employees_table();
elseif(get_post('_tabs_sel') == 'add')
	employee_settings($cur_id);

br();

tabbed_content_end();

end_form();
end_page();