<?php 

function get_allowances($type=null, $value=null, $value2= null, $value3 = null, $inactive=0, $grade_id=false, $value4=null ){

	$sql = "SELECT `allowance`.*, `adv`.`formula`, `adv`. `value`, `adv`.`percentage` FROM ".TB_PREF."kv_empl_allowances AS allowance INNER JOIN ".TB_PREF."kv_empl_allowance_advanced AS adv ON `adv`.`allowance_id` = `allowance`.`id` 
	WHERE allowance.inactive={$inactive} AND allowance.gross <> 1";
	
	if($grade_id)
		$sql .=" AND adv.grade_id=".db_escape($grade_id);
	if($type != null)
		$sql .= " AND allowance.type=".db_escape($type); 

	if($value != null){
		$sql .= " AND ( adv.value=".db_escape($value); 
		if($value2 != null)
			$sql .= " OR adv.value=".db_escape($value2); 
		if($value3 != null)
			$sql .= " OR adv.value=".db_escape($value3); 
		if($value4 != null)
			$sql .= " OR adv.value=".db_escape($value4); 
		$sql .= " )";
	}

	$sql .= " ORDER BY sort_order ASC";
	return db_query($sql,"could not get allowances");
}

function kv_get_allowances($value=null, $inactive=0, $grade_id =false, $value2= null, $value3 = null,  $value4=null){

	$sql = "SELECT `allowance`.*, `adv`.`formula`, `adv`. `value`, `adv`.`percentage` FROM ".TB_PREF."kv_empl_allowances AS allowance INNER JOIN ".TB_PREF."kv_empl_allowance_advanced AS adv ON `adv`.`allowance_id` = `allowance`.`id` 
	WHERE allowance.inactive={$inactive} AND allowance.gross <> 1 ";

	if($value != null)
		$sql .= " AND allowance.type=".db_escape($value); 
	if($grade_id)
		$sql .=" AND adv.grade_id=".db_escape($grade_id);
		
	$sql .= "GROUP BY allowance.id ORDER BY sort_order ASC";
	
	//display_error($sql);
	$allowances = array();
	$ret = db_query($sql, "Can't get employee experiences");
	
	while($cont = db_fetch_assoc($ret))
		$allowances[] = $cont;
	
	return $allowances;
}
function get_all_allowances($inactive=0){

	$sql = "SELECT * FROM ".TB_PREF."kv_empl_allowances WHERE inactive={$inactive} AND gross <> 1 ORDER BY sort_order "; 
	/*  LEFT JOIN ".TB_PREF."chart_master ON ".TB_PREF."chart_master.account_code=".TB_PREF."kv_empl_allowances.debit_code OR ".TB_PREF."chart_master.account_code=".TB_PREF."kv_empl_allowances.credit_code"; */
	$ret = db_query($sql, "Can't get empl allowance");
	//display_error(db_num_rows($ret));
	if(db_num_rows($ret)){
		$selected_allowances = array();	
		while($cont = db_fetch_assoc($ret))
			$selected_allowances[] = $cont;

		return $selected_allowances;
	}else{
		return false;
	}    
}

function kv_get_sal_details_file($month, $year){
		$Allowance = kv_get_allowances();

		$sql0 = "SELECT SUM(`gross`) AS `gross_sum`, SUM(`net_pay`) AS `net_pay_sum`, SUM(`loan`) AS `loan_sum`, SUM(`lop_amount`) AS `lop_amount_sum`, SUM(`ot_other_allowance`) AS `ot_other_allowance_sum`, SUM(`ot_earnings`) AS `ot_earnings_sum`, SUM(`misc`) AS `misc_sum`,";

		foreach($Allowance as $single) {	
			$sql0 .= " SUM(`{$single['id']}`) AS `{$single['id']}_sum`," ;
		}
		$sql = substr($sql0 , 0, -1);
		$sql .=" FROM ".TB_PREF."kv_empl_salary	WHERE month=".db_escape($month)." AND year=".db_escape($year)." AND net_pay <> -1 ";

		$result = db_query($sql,"No transactions were returned");

		if($get_des=db_fetch_assoc($result))
			return $get_des;
		else
			return false;
	}

function kv_get_sal_details_Annual($year){
		$Allowance = kv_get_allowances();
		$sql0 = "SELECT SUM(`gross`) AS `gross_sum`, SUM(`net_pay`) AS `net_pay_sum`, SUM(`loan`) AS `loan_sum`, SUM(`lop_amount`) AS `lop_amount_sum`, SUM(`ot_other_allowance`) AS `ot_other_allowance_sum`, SUM(`misc`) AS `misc_sum`,";
		foreach($Allowance as $single) {	
			$sql0 .= " SUM(`{$single['id']}`) AS `{$single['id']}_sum`," ;
		}
		$sql = substr($sql0 , 0, -1);
		$sql .=" FROM ".TB_PREF."kv_empl_salary	WHERE year=".db_escape($year)." AND net_pay <> -1 ";

		$result = db_query($sql,"No transactions were returned");

		if($get_des=db_fetch_assoc($result))
			return $get_des;
		else
			return false;
	}

function get_all_allowances_count($value=null){
	$sql = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_allowances ";

	if($value != null)
		$sql .= "WHERE type=".db_escape($value); 

	$sql .= " ORDER BY sort_order";
	$result = db_query($sql,"could not get kv_empl_departments");
	$row = db_fetch($result);
	return $row[0];
}
function get_allowances_count($value=null, $inactive=0, $grade_id =false){
	//$sql = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_allowances WHERE inactive={$inactive} ";

	$sql = "SELECT COUNT(*) FROM ".TB_PREF."kv_empl_allowances AS allowance INNER JOIN ".TB_PREF."kv_empl_allowance_advanced AS adv ON `adv`.`allowance_id` = `allowance`.`id` 
	WHERE allowance.inactive={$inactive} AND allowance.gross <> 1 ";
	if($value != null)
		$sql .= "AND allowance.type=".db_escape($value); 
	if($grade_id)
		$sql .=" AND adv.grade_id=".db_escape($grade_id);
	$sql .= " ORDER BY allowance.sort_order";
	$result =  db_query($sql,"could not get kv_empl_departments");
	$row = db_fetch($result);
	return $row[0];
}

function add_allowance($debit_code, $credit_code, $formula, $description, $type, $value, $gross, $basic, $percentage, $Tax,$loan, $esic, $pf, $al_type, $status=0, $sort_order, $unique_name, $grade_id){
	if($value == 'Amount')
		$percentage = 0;
		
	if($basic == 1){
		$basic_id = kv_get_basic();
		if($basic_id > 0){
			$sql = "UPDATE ".TB_PREF."kv_empl_allowances SET basic=0 WHERE id = ".db_escape($basic_id);
   			db_query($sql,"The sales department could not be updated");
		}			
	}	

	if($Tax == 1){
		$Tax_id = kv_get_Taxable_field();
		if($Tax_id > 0){
			$sql = "UPDATE ".TB_PREF."kv_empl_allowances SET Tax=0 WHERE id = ".db_escape($Tax_id);
   			db_query($sql,"The sales department could not be updated");
		}			
	}	

	/*if($loan == 1){
		$loan_id = kv_get_loan_field();
		if($loan_id > 0){
			$sql = "UPDATE ".TB_PREF."kv_empl_allowances SET loan=0 WHERE id = ".db_escape($loan_id);
   			db_query($sql,"The sales department could not be updated");
		}			
	}	*/
	//if(db_has_this_allowance($description) {
		$allowance_id = GetSingleValue('kv_empl_allowances', 'id', array('description' => $description));
		if($allowance_id) {			
			Update('kv_empl_allowance_advanced', array('allowance_id' => $allowance_id, 'grade_id' => $grade_id), array('formula' => $formula, 'value' => $value, 'percentage' => $percentage ));		
			Update('kv_empl_allowances', array('id' => $allowance_id), array('debit_code' => $debit_code, 'credit_code' => $credit_code, 'description' => $description, 'type' => $type, 'gross' => $gross, 'basic' => $basic, 'Tax' => $Tax, 'loan' => $loan, 'esic' => $esic,'pf' => $pf, 'al_type' => $al_type,'inactive' => $status, 'sort_order' => $sort_order, 'unique_name' => $unique_name));
		}else{
			$allowance_id = Insert('kv_empl_allowances', array('debit_code' => $debit_code, 'credit_code' => $credit_code, 'description' => $description, 'type' => $type, 'gross' => $gross, 'basic' => $basic, 'Tax' => $Tax, 'loan' => $loan, 'esic' => $esic,'pf' => $pf, 'al_type' => $al_type, 'inactive' => $status, 'sort_order' => $sort_order, 'unique_name' => $unique_name));
			Insert('kv_empl_allowance_advanced', array('allowance_id' => $allowance_id, 'grade_id' => $grade_id,'formula' => $formula, 'value' => $value, 'percentage' => $percentage ));
		} 
	//}
	//$sql = "INSERT INTO ".TB_PREF."kv_empl_allowances (debit_code, credit_code, formula, description, type, value, gross, basic, percentage, Tax, loan, esic,pf, inactive, sort_order, unique_name) VALUES (".db_escape($debit_code) . ", ".db_escape($credit_code) . ",".db_escape($formula) . ",".db_escape($description) . ", ".db_escape($type)." , ".db_escape($value).", ".db_escape($gross).", ".db_escape($basic).", ".db_escape($percentage).", ".db_escape($Tax).", ".db_escape($loan).", ".db_escape($esic).", ".db_escape($pf).", ".db_escape($status).", ".db_escape($sort_order).", ".db_escape($unique_name).")";
   	//db_query($sql,"The sales department could not be added");
   	return $allowance_id;
}

function update_allowance($selected_id, $debit_code, $credit_code, $formula, $description, $type, $value, $gross, $basic, $percentage, $Tax, $loan, $esic, $pf, $al_type, $status=0, $sort_order, $unique_name, $grade_id){
	if($value== 'Amount')
		$percentage = 0;

	if($basic == 1){
		$basic_id = kv_get_basic();
		if($basic_id > 0){
			$sql = "UPDATE ".TB_PREF."kv_empl_allowances SET basic=0 WHERE id = ".db_escape($basic_id);
   			db_query($sql,"The sales department could not be updated");
		}			
	}	

	if($Tax == 1){
		$Tax_id = kv_get_Taxable_field();
		if($Tax_id > 0){
			$sql = "UPDATE ".TB_PREF."kv_empl_allowances SET Tax=0 WHERE id = ".db_escape($Tax_id);
   			db_query($sql,"The sales department could not be updated");
		}			
	}	

	/* if($loan == 1){
		$loan_id = kv_get_loan_field();
		if($loan_id > 0){
			$sql = "UPDATE ".TB_PREF."kv_empl_allowances SET loan=0 WHERE id = ".db_escape($loan_id);
   			db_query($sql,"The sales department could not be updated");
		}			
	}
	*/
	Update('kv_empl_allowance_advanced', array('allowance_id' => $selected_id, 'grade_id' => $grade_id), array('formula' => $formula, 'value' => $value, 'percentage' => $percentage ));		
	Update('kv_empl_allowances', array('id' => $selected_id), array('debit_code' => $debit_code, 'credit_code' => $credit_code, 'description' => $description,  'type' => $type, 'gross' => $gross, 'basic' => $basic, 'Tax' => $Tax, 'loan' => $loan, 'esic' => $esic,'pf' => $pf, 'al_type' => $al_type, 'inactive' => $status, 'sort_order' => $sort_order, 'unique_name' => $unique_name));
			
	//$sql = "UPDATE ".TB_PREF."kv_empl_allowances SET formula=".db_escape($formula)." , debit_code=".db_escape($debit_code)." , credit_code=".db_escape($credit_code)." , description=".db_escape($description)." , type=".db_escape($type)." , value=".db_escape($value).",  gross=".db_escape($gross).",  basic=".db_escape($basic).", percentage=".db_escape($percentage).", Tax=".db_escape($Tax).", loan=".db_escape($loan).",  esic=".db_escape($esic).",  pf=".db_escape($pf).",  inactive=".db_escape($status).", sort_order=".db_escape($sort_order).", unique_name= ".db_escape($unique_name)." WHERE id = ".db_escape($selected_id);
   //	db_query($sql,"The sales department could not be updated");
}

function db_has_basic_pay(){
	$sql = "SELECT id FROM ".TB_PREF."kv_empl_allowances WHERE basic = 1";
	$result = db_query($sql, "could not get department");
	return (db_num_rows($result))?TRUE:FALSE;
}

function db_has_gross_pay(){
	$sql = "SELECT id FROM ".TB_PREF."kv_empl_allowances WHERE gross = 1";
	$result = db_query($sql, "could not get department");
	return (db_num_rows($result))?TRUE:FALSE;
}
function kv_get_basic(){
	$sql = "SELECT id FROM ".TB_PREF."kv_empl_allowances WHERE basic = 1";
	$result = db_query($sql, "could not get department");
	$row = db_fetch($result);
	return $row[0];
}

function db_has_tax_pay(){
	$sql = "SELECT id FROM ".TB_PREF."kv_empl_allowances WHERE Tax = 1";
	$result = db_query($sql, "could not get department");
	return (db_num_rows($result))?TRUE:FALSE;
}
function kv_get_Tax_allowance(){
	$sql = "SELECT id FROM ".TB_PREF."kv_empl_allowances WHERE Tax = 1";
	$result = db_query($sql, "could not get department");
	$row = db_fetch($result);
	return $row[0];
}

function kv_get_Taxable_field(){
	$sql = "SELECT id FROM ".TB_PREF."kv_empl_allowances WHERE Tax = 1";
	$result = db_query($sql, "could not get department");
	$row = db_fetch($result);
	return $row[0];
}

function kv_get_loan_field(){
	$sql = "SELECT id FROM ".TB_PREF."kv_empl_allowances WHERE loan = 1";
	$result = db_query($sql, "could not get department");
	$row = db_fetch($result);
	return $row[0];
}

function delete_allowance($selected_id){
	$allowance_id = GetSingleValue('kv_empl_allowance_advanced', 'allowance_id', array('id' => $selected_id));
	$adv_allowance = Delete('kv_empl_allowance_advanced', array('id' => $selected_id));
	$count = GetSingleValue('kv_empl_allowance_advanced', 'COUNT(`id`)', array('allowance_id' => $allowance_id));
	if($count == 0 || $count == false){
		$sql="DELETE FROM ".TB_PREF."kv_empl_allowances WHERE id=".db_escape($allowance_id);	
		$result = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_salary` LIKE '{$allowance_id}'", "Can't Query this Table");
		$exists = (db_num_rows($result))?TRUE:FALSE;
		if($exists) {
			$sql1="ALTER TABLE ".TB_PREF."kv_empl_salary DROP COLUMN `{$allowance_id}`";
			db_query($sql1,"could not delete sales department");
		}
		$result = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_job` LIKE '{$allowance_id}'", "Can't Query this Table");
		$exists = (db_num_rows($result))?TRUE:FALSE;
		if($exists) {
			$sql1="ALTER TABLE ".TB_PREF."kv_empl_job DROP COLUMN `{$allowance_id}`";
			db_query($sql1,"could not delete sales department");
		}
		db_query($sql,"could not delete sales department");
	}
}


function get_allowance($selected_id){
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_allowances WHERE id=".db_escape($selected_id);
	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}

function get_allowance_name($id){
	$sql = "SELECT description FROM ".TB_PREF."kv_empl_allowances WHERE id = ".db_escape($id);
	$result = db_query($sql, "could not get department");
	$row = db_fetch($result);
	return $row[0];
}
function kv_check_payroll_table_exist(){
	$table_exist = db_query("SHOW TABLES LIKE '".TB_PREF."kv_empl_job'");
    $tbl_ext = db_num_rows($table_exist) > 0;
    if($tbl_ext){
    	return true; 
    }else{
    	$table_exist = db_query("SHOW TABLES LIKE '".TB_PREF."kv_empl_salary'");
	    $tbl_ext = db_num_rows($table_exist) > 0;
	    if($tbl_ext){
	    	return true; 
	    }else
	    	return false;
    }
    return false;
}

//--------------------------------------------------------------------------------------------
function db_has_this_allowance($description){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_allowances WHERE description=".db_escape($description));
}

//--------------------------------------------------------------------------------------------
function db_has_this_allowance_grade($allowance_id, $grade_id){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_allowance_advanced WHERE allowance_id=".db_escape($allowance_id)." AND grade_id=".db_escape($grade_id));
}
//--------------------------------------------------------------------------------------------
function db_has_this_allowance_id($allowance_id){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_allowances WHERE id=".db_escape($allowance_id));
}
//--------------------------------------------------------------------------------------------
function db_has_this_allowance_unique_name($unique_name, $selected_id, $grade){
	$sql = "SELECT allowance.id FROM ".TB_PREF."kv_empl_allowances AS allowance INNER JOIN ".TB_PREF."kv_empl_allowance_advanced AS adv ON allowance.id= adv.allowance_id WHERE allowance.unique_name=".db_escape($unique_name)." AND adv.grade_id=".db_escape($grade);
	$result = db_query($sql, "Cant'check count");
	$result_count =  db_num_rows($result); 
	if($result_count > 0){
		if($result_count == 1 ){
			if($res = db_fetch($result)){
				return ($res[0] == $selected_id)?FALSE:TRUE;
			}
			return ($selected_id != -1)?FALSE:TRUE;
		} else {
			return true;
		} 
	}
	else
		return false;
}
?>
