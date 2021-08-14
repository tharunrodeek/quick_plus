<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

//-----------------------------------------------------------------------------------------------
function get_empl_loan_monthly_payment($id, $date){
	$datey = date2sql($date);
	$sql = "SELECT SUM(`monthly_pay`) FROM ".TB_PREF."kv_empl_loan WHERE `periods_paid` < `periods` AND `empl_id`=".db_escape($id)." AND date <= ".db_escape($datey)." AND status='Active'";
	
	$result = db_query($sql, "could not get bonus type");
	
	$row = db_fetch_row($result);
	
	//display_error($sql);
	if(!empty($row[0]))
		return $row[0];
	else
		return 0;
}

function paid_empl_loan_month_payment($empl_id,$loan_ids, $date){
	//$loan_id_alone= array();
	begin_transaction();
	foreach($loan_ids as $id){
		$sql = "SELECT * FROM ".TB_PREF."kv_empl_loan WHERE id=".db_escape($id[0])." LIMIT 1";	
		$result = db_query($sql, "could not get bonus type");
			
		if($row = db_fetch($result)) {			
			$paid_now  = $row['periods_paid'] +$id[2];		
			if($row['periods'] == $paid_now)
				$sql = "UPDATE ".TB_PREF."kv_empl_loan SET periods_paid = ".db_escape($paid_now).", status = 'Inactive' WHERE id = ".db_escape($id[0]);
			else
				$sql = "UPDATE ".TB_PREF."kv_empl_loan SET periods_paid = ".db_escape($paid_now)." WHERE id = ".db_escape($id[0]);
			
			db_query($sql, "could not update employee loan");				
			$sql = "SELECT allow.* FROM ".TB_PREF."kv_empl_allowances AS allow LEFT JOIN ".TB_PREF."kv_empl_loan_types AS types ON types.allowance_id= allow.id WHERE types.id=".$row['loan_type_id'];
			$res = db_query($sql, "Can't get allowances for loan");
			if($row2 = db_fetch($res)){		
				$memo = 'Employee Loan Repayment #'.$empl_id.'-'. kv_get_empl_name($empl_id);
				if($row2['debit_code'] != '')  // Credit here
					add_gl_trans(98, $row2['id'], $date, $row2['debit_code'], 0,0, $memo, -($row['monthly_pay']*$row['rate']*$id[2]));
				if($row2['credit_code'] != '')  // Debit here
					add_gl_trans(98, $row2['id'], $date, $row2['credit_code'], 0,0, $memo, ($row['monthly_pay']*$row['rate']*$id[2]));
				add_audit_trail(98, $row2['id'], $date_);
			}
		}
	}
	 
	//if($total_amount == $amount*$rate)
		commit_transaction();
	//else
		//cancel_transaction();
	//display_error($total_amount.'--'.$amount);
	//exit;
}

function get_empl_loan_details_Complete($empl_id){ 
	$loans = GetDataJoin('kv_empl_loan AS loan', array( 
					0 => array('join' => 'INNER', 'table_name' => 'kv_empl_loan_types AS type', 'conditions' => '`type`.`id` = `loan`.`loan_type_id`'),
					1 => array('join' => 'INNER', 'table_name' => 'kv_empl_info AS info', 'conditions' => '`info`.`empl_id` = `loan`.`empl_id`') ), 
							array('`info`.`empl_id`, `info`.`empl_firstname`, `type`.`loan_name`, `loan`.`loan_amount`, `loan`.`monthly_pay`, `loan`.`periods`, `loan`. `periods_paid`, `loan`. `date`, `loan`.`status`'),
							array('`loan`.`empl_id`' => $empl_id));
	return $loans;
}


function db_has_empl_loan($empl_id){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_loan WHERE status='Active' AND empl_id=".db_escape($empl_id));	
}

function db_empl_has_this_loan($loan_id, $empl_id){
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_loan WHERE status='Active' AND empl_id=".db_escape($empl_id)." AND loan_type_id=".db_escape($loan_id));	
}
function db_has_empl_inactive_loan($empl_id){

	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_loan WHERE status='Inactive' AND empl_id=".db_escape($empl_id));
	
}

function db_has_empl_loan_all_status($empl_id){

	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_loan WHERE empl_id=".db_escape($empl_id));
	
}

function get_empl_loan($empl_id){

	$sql = "SELECT * FROM ".TB_PREF."kv_empl_loan WHERE empl_id=".db_escape($empl_id);
	
	$result = db_query($sql, "could not get employee loan");
	
	return db_fetch($result);
}
?>