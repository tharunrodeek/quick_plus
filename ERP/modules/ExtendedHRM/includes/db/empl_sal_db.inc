<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

//-----------------------------------------------------------------------------
function get_empl_sal_details($id, $month, $yr, $dim=0, $dim2=0){

	if(db_has_employee_payslip($id, $month, $yr)){
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($id)." AND month=".db_escape((int)$month)." AND year=".db_escape($yr);
		if($dim > 0 )
			$sql .=" AND dimension=".db_escape($dim);
		if($dim2 > 0 )
			$sql .=" AND dimension2 = ".db_escape($dim2);
	}else{
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($id);
	}
	$result = db_query($sql, "could not get bonus type");
	
	return db_fetch($result);
}

function get_empl_encashment_details($id, $month, $yr){

	if(db_has_employee_leave_encashment($id, $month, $yr)){
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_leave_encashment WHERE empl_id=".db_escape($id)." AND year=".db_escape($yr);
		if($month > 0 )
			$sql .= " AND month = ".db_escape($month);
	}else{
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($id);
	}	
	$result = db_query($sql, "could not get bonus type");	
	return db_fetch($result);
}
function kv_get_allowance_transactions($rep, $month, $year){

	$sql = "SELECT id, date, empl_id, `{$rep}` FROM ".TB_PREF."kv_empl_salary WHERE month=".db_escape($month)." AND year=".db_escape($year);
	
	return $result = db_query($sql, "could not get bonus type");
	
	//return db_fetch($result);
}

function db_has_employee_payslip($empl_id, $month, $year){

	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($empl_id)." AND month=".db_escape((int)$month)." AND year=".db_escape($year));
}

function db_has_employee_leave_encashment($empl_id,  $month, $year){

	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_leave_encashment WHERE empl_id=".db_escape($empl_id)." AND year=".db_escape($year)." AND month=".db_escape($month));
}

function db_has_some_entry_to_this_allowance($rep, $month, $year){

	return check_empty_result("SELECT IF(`{$rep}` >0, 1, 0) FROM ".TB_PREF."kv_empl_salary WHERE month=".db_escape($month)." AND year=".db_escape($year));
}

function db_has_sal_for_selected_dept($dept_id, $month, $year){

	$sql = "SELECT empl_id FROM ".TB_PREF."kv_empl_salary WHERE month=".db_escape($month)." AND year=".db_escape($year);

	$salaried_employes = $selected_empl_list= array();
	
	$ret = db_query($sql, "Can't get empl attendance");
	
	while($cont = db_fetch($ret)) 
		$salaried_employes[] = $cont;
	$salaried_employees = array_values($salaried_employes);

	$get_employees_list = get_empl_ids_from_dept_id($dept_id);

	display_notification(json_encode($salaried_employees).'---'. json_encode($get_employees_list));
	
	if(empty($salaried_employees) || empty($get_employees_list)){
		return 0;
	}else{
		$selected_empl_list = array_intersect($salaried_employees, $get_employees_list);

		if(empty($selected_empl_list)){
			return 0;
		}else {
			return $selected_empl_list;
		}
	}
	
}

function get_empl_name_dept($id) {

	$sql = "SELECT CONCAT_WS(' ', empl.empl_firstname, empl.empl_lastname) as name, dept.department as deptment, empl.email,empl.empl_id as EMployeeCode FROM ".TB_PREF."kv_empl_info as empl, ".TB_PREF."kv_empl_job as dept WHERE empl.id=".db_escape($id)." AND dept.empl_id=".db_escape($id);
	//dd($sql);
	$result = db_query($sql, "could not get bonus type");
	
	return db_fetch($result);

}

function get_empl_gross_pay($id) {
	$sql = "SELECT	gross FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($id)."LIMIT 1";

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);

	return $row[0];
}

//--------------------------------------------------------------------------------------------
function get_emply_salary($id, $year=null, $dimension=0, $dimension2=0) {

	$sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($id)." AND net_pay <> -1 AND dimension = ".db_escape($dimension)." AND dimension2 = ".db_escape($dimension2);

	if($year)
		$sql .= ' AND year='.db_escape($year);
	$salaries = array();
	$ret = db_query($sql, "Can't get empl attendance");
	
	while($cont = db_fetch($ret)) 
		$salaries[] = $cont;
	return $salaries;
}

function db_has_sal_for_selected_Date($empl_id, $month, $year){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($empl_id)." AND month=".db_escape($month)." AND year=".db_escape($year));
}

function get_employee_sal_history($empl_id , $year) { 
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($empl_id)." AND year=".db_escape($year);	
	$result = db_query($sql, "could not get bonus type");	
	return db_fetch($result);
} 

function get_gross_pay_of_an_employee($empl_id){
	
	$sql = "SELECT 	gross FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($empl_id)."LIMIT 1";
	$result = db_query($sql, "could not get sales type");
	$row = db_fetch_row($result);
	if(!empty($row[0]))
		return $row[0];
	else
		return 0;
}

function get_employee_net_pay($empl_id){
	
	$Allowance = get_allowances('Deductions');
	$sum = '';
	while ($single = db_fetch($Allowance)) {	
		$sum .=$single['id']."+";
	}
	$summ = substr($sum, 0, -1);

	$sql = "SELECT SUM(".$summ.") FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($empl_id)."LIMIT 1";

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);

	if(!empty($row[0]))
		$othr_ded =  $row[0];
	else
		$othr_ded = 0;

	$net_pay = get_gross_pay_of_an_employee($empl_id) - (get_empl_loan_monthly_payment($empl_id, Today()) + $othr_ded); 
	return ($net_pay > 0 ? $net_pay : 0);
}

//-----------------------------------------------------------------------------------------------
function get_empl_bank_acc_details($empl_id){
	$sql="SELECT mod_of_pay, bank_name, acc_no FROM  ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($empl_id)." LIMIT 1";
	$result = db_query($sql,"The employee job details could not be updated"); 
	$empl_id_list=array();
	while($get_des=db_fetch($result)){
		$empl_id_list['mod_of_pay']=$get_des['mod_of_pay'];
		$empl_id_list['bank_name']=$get_des['bank_name'];
		$empl_id_list['acc_no']=$get_des['acc_no'];
	}
		
	return $empl_id_list;
}


//------------------------------------------------------------------------------
function get_employee_job($employee_id){

	if(is_numeric($employee_id))
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".$employee_id;
	else
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get employee");
	
	return db_fetch($result);	
	
}

//--------------------------------------------------------------------------------------------
function db_employee_has_job($employee_id){
	$sql = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_job WHERE empl_id=".db_escape($employee_id);
	
	$result = db_query($sql, "could not get employee");
	
	if (db_fetch_row($result)==0) { 
		return false;	
	} else { 
		return true ;
	}
}

function check_employee_accounts_exists($employee_id){
	$sql = "SELECT COUNT(*) as Cnt FROM ".TB_PREF."kv_empl_account_mapping WHERE emp_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get employee");
	$row=db_fetch_row($result);

	return $row;
}


function get_employee_id($employee_id)
{
	$sql = "SELECT empl_id,emp_code FROM ".TB_PREF."kv_empl_info WHERE id=".db_escape($employee_id);

	$result = db_query($sql, "could not get employee");

	return db_fetch_row($result);
}

function get_empl_ids_from_dept_id($dept_id, $grade=null) {

	$empl_id_list=array();
	$sql= "SELECT empl_id FROM ".TB_PREF."kv_empl_job  WHERE 1 ";
	
	if($dept_id != '') {
		 $sql .= "AND department = ".db_escape($dept_id);
	}
	if($grade != '') {
		 $sql .= "AND grade = ".db_escape($grade);
	} 
	$sql .= " ORDER BY empl_id";
	
	$result = db_query($sql, "could not get sales type");

	while($get_des=db_fetch($result))
		$empl_id_list[]=$get_des['empl_id'];
		
	return $empl_id_list;
}


function get_empl_ids_from_dept_id_payroll($dept_id, $grade=null) {

	$empl_id_list=array();
	$sql= "SELECT info.empl_id, info.status, info.date_of_status_change, job.grade FROM ".TB_PREF."kv_empl_job AS job , ".TB_PREF."kv_empl_info AS info WHERE info.empl_id = job.empl_id ";
	
	if($dept_id != '') {
		 $sql .= " AND job.department = ".db_escape($dept_id);
	}
	if($grade != '') {
		 $sql .= " AND job.grade = ".db_escape($grade);
	}
	$sql .= " ORDER BY info.empl_id";
	$result = db_query($sql, "could not get sales type");

	while($get_des=db_fetch($result))
		$empl_id_list[]=$get_des;
		
	return $empl_id_list;
}

//--------------------------------------------------------------------------------

function hrm_get_gl_trans($type, $trans_id, $dimension=0, $dimension2=0, $employee_id=0, $month=0, $year=0){	
	
	$sql = "SELECT gl.*, cm.account_name, IFNULL(refs.reference, '') AS reference, user.real_name, 
			COALESCE(st.tran_date, dt.tran_date, bt.trans_date, grn.delivery_date, gl.tran_date) as doc_date,
			IF(ISNULL(st.supp_reference), '', st.supp_reference) AS supp_reference
	FROM ".TB_PREF."gl_trans as gl
		LEFT JOIN ".TB_PREF."chart_master as cm ON gl.account = cm.account_code
		LEFT JOIN ".TB_PREF."refs as refs ON (gl.type=refs.type AND gl.type_no=refs.id)
		LEFT JOIN ".TB_PREF."audit_trail as audit ON (gl.type=audit.type AND gl.type_no=audit.trans_no AND NOT ISNULL(gl_seq))
		LEFT JOIN ".TB_PREF."users as user ON (audit.user=user.id)
	# all this below just to retrieve doc_date :>
		LEFT JOIN ".TB_PREF."supp_trans st ON gl.type_no=st.trans_no AND st.type=gl.type AND (gl.type!=".ST_JOURNAL." OR gl.person_id=st.supplier_id)
		LEFT JOIN ".TB_PREF."grn_batch grn ON grn.id=gl.type_no AND gl.type=".ST_SUPPRECEIVE." AND gl.person_id=grn.supplier_id
		LEFT JOIN ".TB_PREF."debtor_trans dt ON gl.type_no=dt.trans_no AND dt.type=gl.type AND (gl.type!=".ST_JOURNAL." OR gl.person_id=dt.debtor_no)
		LEFT JOIN ".TB_PREF."bank_trans bt ON bt.type=gl.type AND bt.trans_no=gl.type_no AND bt.amount!=0
			 AND bt.person_type_id=gl.person_type_id AND bt.person_id=gl.person_id
		LEFT JOIN ".TB_PREF."journal j ON j.type=gl.type AND j.trans_no=gl.type_no"

		." WHERE gl.type= ".db_escape($type) 		
		." AND gl.amount <> 0";
		
	if($dimension == 0 && $dimension2 == 0) {
		$sql .= " AND gl.type_no IN (SELECT id FROM ".TB_PREF."kv_empl_salary WHERE empl_id=".db_escape($employee_id)." AND month=".db_escape($month)." AND year=".db_escape($year).")";
	} else {
		$sql .=" AND gl.type_no = ".db_escape($trans_id); 
	}
	if ($dimension > 0)
		$sql .= " AND gl.dimension_id = ".($dimension<0 ? 0 : db_escape($dimension));

	if ($dimension2 > 0)
		$sql .= " AND gl.dimension2_id = ".($dimension2<0 ? 0 : db_escape($dimension2));
	//if($dimension == 0 && $dimension2 == 0)
	//	$sql .=" GROUP BY gl.account";
	$sql .=" ORDER BY tran_date, counter";
	return db_query($sql, "The gl transactions could not be retrieved");
}



function save_employee_mapping_accounts($pay_accounts=array())
{
	foreach($pay_accounts as $acco)
	{
		if($acco['sub_account_code']!='0')
		{
			$sql_chk="select id from 0_kv_empl_account_mapping where emp_id='".$acco['emp_id']."' and element_id='".$acco['element_id']."'";
			$row_id=db_fetch(db_query($sql_chk));
			if($row_id[0]!='')
			{
				$sql="UPDATE 0_kv_empl_account_mapping set sub_account_code='".$acco['sub_account_code']."' where id='".$row_id[0]."'";
			}
			else
			{
				$sql="Insert into 0_kv_empl_account_mapping (emp_id,element_id,sub_account_code) values ('".$acco['emp_id']."','".$acco['element_id']."',
		     		'".$acco['sub_account_code']."')";

			}

			db_query($sql);

		}

	}


}

function get_emp_account_edit_data($empl_id)
{
	$sql= "SELECT * FROM ".TB_PREF."kv_empl_account_mapping  WHERE emp_id='".$empl_id."' ";
	$result = db_query($sql, "could not get sales type");
	$row=db_fetch($result);
	return $row;
}



?>
