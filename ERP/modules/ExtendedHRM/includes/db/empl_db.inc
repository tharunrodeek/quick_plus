<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
//--------------------------------------------------------------------------------------------
function kv_get_employees_based_on_dept($dept_id, $date=null) {
	if($date== null){
		$date = date('Y-m-d');
	}else
		$date = date2sql($date);
	if($dept_id == 0){		
		$sql = "SELECT DISTINCT empl_id, CONCAT(empl_firstname, ' ' , empl_lastname) AS empl_name FROM ".TB_PREF."kv_empl_info WHERE status=1 ORDER BY empl_id";  		
	} else{
		$sql="SELECT DISTINCT info.empl_id, job.joining, info.status, info.date_of_status_change FROM ".TB_PREF."kv_empl_job job, ".TB_PREF."kv_empl_info info WHERE info.empl_id= job.empl_id AND job.department=".db_escape($dept_id)." ORDER BY info.empl_id";		
	}
	return db_query($sql, "The employee table is inaccessible");
}

function kv_get_employees_count_based_on_dept($dept_id) {

	if($dept_id == 0){
		$sql = "SELECT COUNT(empl_id) FROM ".TB_PREF."kv_empl_info";
	} else{
		$sql="SELECT COUNT(empl_id) FROM ".TB_PREF."kv_empl_job WHERE department=".db_escape($dept_id);
	}
	return check_empty_result($sql);
}
function kv_get_employees_based_on_dept_for_attandance($dept_id, $month, $year){
	/*if($date== null){
		$date = date('Y-m-d');
	}else
		$date = date2sql($date);*/
		
	if($dept_id == 0){	
			$sql = "SELECT DISTINCT empl_id FROM ".TB_PREF."kv_empl_info AS info, ".TB_PREF."kv_empl_salary AS salary WHERE info.status=1 AND info.empl_id = salary.empl_id AND salary.month !=".abs($month)." AND salary.year != ".abs($year);  
			
	} else{
		$sql="SELECT DISTINCT info.empl_id, job.joining, info.status, info.date_of_status_change FROM ".TB_PREF."kv_empl_job job, ".TB_PREF."kv_empl_info info, ".TB_PREF."kv_empl_salary AS salary WHERE info.empl_id= job.empl_id AND info.status=1 AND job.department=".db_escape($dept_id)." AND info.empl_id = salary.empl_id AND salary.month !=".abs($month)." AND salary.year != ".abs($year);		
	}
	return db_query($sql, "The employee table is inaccessible");
}

function kv_get_empl_name($id, $last=false) {
	if($last)
		$name = "CONCAT(empl_firstname,' ', empl_lastname) AS Name"; 
	else
		$name = "empl_firstname" ; 

	$sql = "SELECT ".$name." FROM ".TB_PREF."kv_empl_info WHERE id=".db_escape($id)."LIMIT 1";
//pp($sql);
	$result = db_query($sql, "could not get sales type");
	$row = db_fetch_row($result);
	return $row[0];
}
 
//--------------------------------------------------------------------------------------------
function db_has_employees(){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_info");
}

function db_has_employee_payslips($empl_id){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_salary WHERE empl_id= ".db_escape($empl_id)." AND net_pay <> -1 ");
}
//--------------------------------------------------------------------------------------------
function db_has_employee_email($email){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_info WHERE email=".db_escape($email));
}

function db_has_selected_employee($empl_id){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_info WHERE empl_id=".db_escape($empl_id));
}
//--------------------------------------------------------------------------------------------
function count_employees(){
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_info";
	$result = db_query($sql, "could not get employee");
	return db_num_rows($result);
}


function delete_employee($empl_id){
	$tables_array = array('kv_empl_info', 'kv_empl_job', 'kv_empl_attendancee', 'kv_empl_cv', 'kv_empl_degree', 'kv_empl_experience', 'kv_empl_loan', 'kv_empl_salary', 'kv_empl_training');
	$field='';
	foreach($tables_array as $tabl){
		if($tabl=='kv_empl_info')
		{
			$field='id';
		}
		else
		{
			$field='empl_id';
		}
		$sql="DELETE FROM ".TB_PREF."{$tabl} WHERE ".$field."=".db_escape($empl_id);
		//display_error($sql);
		db_query($sql, "could not delete the selected Employee");
	}
}
function db_has_auto_empl_id(){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_option WHERE option_name = 'empl_ref_type' AND option_value = 1");
}

function kv_get_next_empl_id() {
	 $sql = "SELECT option_value FROM ".TB_PREF."kv_empl_option WHERE option_name='next_empl_id' LIMIT 1";
	 $result = db_query($sql, "could not get sales type");
	 $row = db_fetch_row($result);
	 return $row[0];
}

function kv_get_next_empl_prefix() {
	$sql = "SELECT option_value FROM ".TB_PREF."kv_empl_option WHERE option_name='empl_id_prefix' LIMIT 1";
	$result = db_query($sql, "could not get sales type");
	$row = db_fetch_row($result);
	return $row[0];
}

function delete_empl_id(){
	$sql="DELETE FROM ".TB_PREF."kv_empl_option WHERE option_name= ='next_empl_id'" ;
	db_query($sql, "could not delete the selected Employee"); 
}

function kv_add_next_empl_id_new($empl_id){ 
	$option_value = (int)substr($empl_id, 1); 
	$option_value = $option_value+1 ; 
	$option_2 = substr($empl_id, 0,1); 
	$empl_next_id = $option_2.(string)$option_value; 

	$sql_insert = "";				
	db_query($sql_insert,"The employee could not be added");	
}
function kv_update_next_empl_id_new($empl_id, $new,$prefix){

	$option_value = (int)$empl_id +1;

	if($new)
		$sql = "INSERT INTO ".TB_PREF."kv_empl_option (option_name,option_value) VALUES ('next_empl_id',$option_value)";
	else
		$sql = "UPDATE ".TB_PREF."kv_empl_option SET option_value=".db_escape($option_value)." WHERE option_name ='next_empl_id' ";
	    db_query($sql, "The employee could not be updated");

	$emp_prefx= "UPDATE ".TB_PREF."kv_empl_option SET option_value=".db_escape($prefix)." WHERE option_name ='empl_id_prefix' ";
	    db_query($emp_prefx,"Data could not updated");
}

function kv_get_empl_desig($id) {
	$sql = "SELECT desig FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($id)."LIMIT 1";
	$result = db_query($sql, "could not get sales type");
	$row = db_fetch_row($result);
	return $row[0];
}

function check_db_has_salary_account($msg){
    if (!check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_option WHERE option_name='salary_account'"))  {
    	display_error($msg, true);
    	end_page();
    	exit;	
    }	
}

function check_db_has_employees($msg){	
    if (!check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_info"))  {
    	display_error($msg, true);
    	end_page();
    	exit;	
    }	
}

function get_employee_join_date($empl_id){
	$sql = "SELECT joining FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($empl_id)."LIMIT 1";
	$result = db_query($sql, "could not get sales type");
	$row = db_fetch_row($result);
	return $row[0];
}

function check_db_has_Departments($msg){
	global $path_to_root;
    if (!check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_departments"))  {
    	display_error($msg, true);
    	end_page();
    	exit;	
    }	
}

function check_db_has_Loan_types($msg){
	global $path_to_root;
    if (!check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_loan_types"))  {
    	display_error($msg, true);
    	end_page();
    	exit;	
    }	
}
function get_payroll_elements($id=false, $account=false) {
	$sql = "SELECT e.id, e.element_name,e.type,e.calculate_percentage,e.account_code
            FROM ".TB_PREF."kv_empl_pay_elements e 
            WHERE e.status='1' AND e.salary_ded_flag='0' AND e.hide_from_empl='0' ORDER BY e.id ASC";
//dd($sql);
	/*if($id)
		$sql .= " AND e.element_id = ".db_escape($id);
	if($account)
		$sql .= " AND e.account_code = ".db_escape($account);*/

	$result = db_query($sql, trans('Could not get accounts'));

	if($account || $id)
		return db_fetch($result);
	else
		return $result;
}


function get_payroll_elements_to_assign_acc($id=false, $account=false) {
	$sql = "SELECT e.id, e.element_name,e.type,e.calculate_percentage,e.account_code
            FROM ".TB_PREF."kv_empl_pay_elements e 
            WHERE e.status='1'   ORDER BY e.id ASC";

	$result = db_query($sql, trans('Could not get accounts'));

	if($account || $id)
		return db_fetch($result);
	else
		return $result;
}

function fetch_account_if_assigned($empl_id,$element_id)
{
      $sql="SELECT sub_account_code FROM 0_kv_empl_account_mapping 
            Where emp_id='".$empl_id."' and element_id='".$element_id."'";
      $res=db_query($sql);
      return db_fetch($res);
}


function get_divsions($id) {
	$sql = "SELECT e.id, e.description
            FROM 0_kv_empl_designation_group as e
            WHERE  e.depti_id='".$id."'
            and e.inactive='0'";



	$result = db_query($sql, trans('Could not get accounts'));
    return $result;
}

function get_All_divsions() {
	$sql = "SELECT e.id, e.description
            FROM 0_kv_empl_designation_group as e
            WHERE  e.inactive='0'";



	$result = db_query($sql, trans('Could not get accounts'));
	return $result;
}

function get_leave_elements($id=false, $account=false) {
	$sql = "SELECT description,char_code,id
			FROM 0_kv_empl_leave_types WHERE salary_cut!='1' AND char_code NOT IN ('p','wl','a')";
//dd($sql);

	$result = db_query($sql, trans('Could not get accounts'));

	if($account || $id)
		return db_fetch($result);
	else
		return $result;
}



function save_personal_salary($pay_rules=array()) {

	if(empty($pay_rules))
		return false;

	foreach($pay_rules as $rule)
		write_personal_salary($rule['emp_id'], $rule['pay_rule_id'], $rule['pay_amount'], $rule['type'], $rule['is_basic']);
}

function write_personal_salary($emp_id, $pay_rule_id, $pay_amount, $type, $is_basic = 0) {

	$date = date2sql(Today());

	if($pay_amount == 0)
		$sql = "DELETE FROM ".TB_PREF."kv_empl_salary_details WHERE emp_id = ".db_escape($emp_id)." AND pay_rule_id = ".db_escape($pay_rule_id)." AND is_basic = ".db_escape($is_basic);
	elseif(!check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_salary_details WHERE emp_id = ".db_escape($emp_id)." AND pay_rule_id = ".db_escape($pay_rule_id)." AND is_basic = ".db_escape($is_basic))) {
		$sql = "INSERT INTO ".TB_PREF."kv_empl_salary_details(date, emp_id, pay_rule_id, pay_amount, type, is_basic) VALUES ('"
			.$date."',"
			.db_escape($emp_id).","
			.db_escape($pay_rule_id).","
			.db_escape($pay_amount).","
			.db_escape($type).","
			.db_escape($is_basic)
			.")";
	}
	else {
		$sql = "UPDATE ".TB_PREF."kv_empl_salary_details SET date = '".$date."', pay_amount = ".db_escape($pay_amount).", type = ".db_escape($type).", is_basic = ".db_escape($is_basic)." WHERE emp_id = ".db_escape($emp_id)." AND pay_rule_id = ".db_escape($pay_rule_id);
	}

	db_query($sql, trans('could not write salary structure'));
}

function save_employee_leave($leaves=array())
{
	if(empty($leaves))
		return false;

	foreach($leaves as $leave)
		insert_employee_leave($leave['emp_id'], $leave['leave_id'], $leave['days']);
}


function insert_employee_leave($empl_id,$leave_id,$leave_days)
{
	  if(!check_empty_result("SELECT COUNT(*) FROM 0_kv_empl_leave_details WHERE emp_id='".$empl_id."' AND leave_id='".$leave_id."' "))
	  {
	     $sql="INSERT INTO 0_kv_empl_leave_details (emp_id,leave_id,days)
           VALUES ('".$empl_id."','".$leave_id."','".$leave_days."')";
	  }
	  else
	  {
	  	$sql="UPDATE 0_kv_empl_leave_details SET days='".$leave_days."' WHERE emp_id='".$empl_id."' AND leave_id='".$leave_id."' ";
	  }
	 db_query($sql);
}




function get_emp_salary_structure($emp_id) {
		$sql = "SELECT s.*,p.type as pay_type 
        		FROM ".TB_PREF."kv_empl_salary_details s
		  		INNER JOIN ".TB_PREF."kv_empl_info AS e ON s.emp_id=e.id
		  		INNER JOIN 0_kv_empl_pay_elements as p ON p.id=s.pay_rule_id
        		WHERE e.id ='".$emp_id."' ";
 //echo $sql;

	return db_query($sql, trans('Could not get employee salary structure'));
}

function get_element_basic_type($elememt_id)
{
	$sql = "SELECT is_basic FROM 0_kv_empl_pay_elements WHERE id='".$elememt_id."' ";
	$result = db_query($sql, trans('Exceution failed'));
	return db_fetch($result);
}

function get_emp_leave_structure($emp_id) {
	$sql = "SELECT s.* 
        		FROM ".TB_PREF."kv_empl_leave_details s
		  		INNER JOIN ".TB_PREF."kv_empl_info AS e ON s.emp_id=e.id
        		WHERE e.id ='".$emp_id."' ";
 //echo $sql;

	return db_query($sql, trans('Could not get employee salary structure'));
}

?>
