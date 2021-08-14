<?php 
// Unavailable Arrays
// ST_PURCHORDER, //Already blocked
// ST_SALESORDER, //Already blocked
// ST_SALESQUOTE, //Already blocked
// ST_COSTUPDATE, //Already blocked       
// ST_BANKDEPOSIT, //nop
// ST_CUSTCREDIT, //nop
// ST_DIMENSION,  //not in switch case      
// ST_CUSTDELIVERY,  //nop
// ST_LOCTRANSFER,  //nop
// ST_INVADJUST,  //nop
// ST_WORKORDER,  //nop
// ST_CHEQUE,     //not in switch case
// ST_SUPPCREDIT, //nop
// ST_SUPPRECEIVE,  //nop
// ST_MANUISSUE, //28  //nop
// ST_MANURECEIVE //29 //nop

// Available Arrays
// ST_JOURNAL //void_journal_trans($type, $type_no);
// ST_BANKTRANSFER //void_bank_trans($type, $type_no);
// ST_BANKPAYMENT //void_bank_trans($type, $type_no);
// ST_CUSTPAYMENT //post_void_customer_trans($type, $type_no);
// ST_SALESINVOICE //post_void_customer_trans($type, $type_no);
// ST_SUPPINVOICE //post_void_supp_trans($type, $type_no)
// ST_SUPPAYMENT //post_void_supp_trans($type, $type_no)






// ST_MANUISSUE
// ST_MANURECEIVE


// define('ST_JOURNAL', 0);

// define('ST_BANKPAYMENT', 1);
// define('ST_BANKDEPOSIT', 2);
// define('ST_BANKTRANSFER', 4);

// define('ST_SALESINVOICE', 10);
// define('ST_CUSTCREDIT', 11);
// define('ST_CUSTPAYMENT', 12);
// define('ST_CUSTDELIVERY', 13);

// define('ST_LOCTRANSFER', 16);
// define('ST_INVADJUST', 17);

// define('ST_PURCHORDER', 18);
// define('ST_SUPPINVOICE', 20);
// define('ST_SUPPCREDIT', 21);
// define('ST_SUPPAYMENT', 22);
// define('ST_SUPPRECEIVE', 25);

// define('ST_WORKORDER', 26);
// define('ST_MANUISSUE', 28);
// define('ST_MANURECEIVE', 29);

// //
// //	Depreciation period types
// //
// define('FA_MONTHLY', 0);
// define('FA_YEARLY', 1);

// define('ST_SALESORDER', 30);
// define('ST_SALESQUOTE', 32);
// define('ST_COSTUPDATE', 35);
// define('ST_DIMENSION', 40);

// // Don't include these defines in the $systypes_array.
// // They are used for documents only.
// define ('ST_STATEMENT', 91);
// define ('ST_CHEQUE', 92);



// //Invoice Payment Flags

// define('INV_AMER_NORMAL',0);
// define('INV_TASHEEL_ED',1);
// define('INV_TASHEEL_CC',2);
// define('INV_AMER_CC',3);
// define('INV_TADBEER_ED',4);
// define('INV_TADBEER_CC',5);
function get_table_columns($table_name,$prefix_for_column=''){
	// $columns = '';
	// $sql = "SELECT GROUP_CONCAT(DISTINCT column_name ORDER BY ordinal_position) as columns
	// FROM information_schema.columns
	// WHERE table_name = '$table_name'";
	// $result = db_query($sql);
	// $return_result = db_fetch_assoc($result);
	// if($prefix_for_column!=''){
	// 	$column_array = explode(',',$return_result['columns']);
	// 	foreach ($column_array as $key => $column) {
	// 		$column_array[$key] = $prefix_for_column . ".".$column;
	// 	}
	// 	$columns = implode (',', $column_array);

	// }else{
	// 	$columns = $return_result['columns'];
	// }

	// return $columns;


	
	switch ($table_name) {
		case '0_debtor_trans':
		case '0_voided_debtor_trans':
			$columns = 'trans_no,type,version,debtor_no,branch_code,tran_date,due_date,created_at,reference,barcode,tpe,order_,ov_amount,ov_gst,ov_freight,ov_freight_tax,ov_discount,alloc,prep_amount,rate,ship_via,dimension_id,dimension2_id,payment_terms,tax_included,display_customer,customer_trn,customer_mobile,customer_email,customer_ref,payment_method,credit_card_charge,show_bank_charge,payment_flag,cust_emp_id,cust_emp_name,invoice_type,inv_total,customer_card_amount,created_by,payment_type,cheq_no,cheq_date,contact_person,round_of_amount';
			break;

		case '0_bank_trans':
		case '0_voided_bank_trans':
			$columns = 'id,type,trans_no,bank_act,ref,trans_date,amount,dimension_id,dimension2_id,person_type_id,person_id,reconciled,payment_type,cheq_no,cheq_date,created_by';
			break;

		case '0_customer_rewards':
		case '0_voided_customer_rewards':
			$columns = 'id,trans_no,detail_id,trans_type,tran_date,stock_id,reward_type,customer_id,qty,conversion_rate,reward_point,reward_amount,created_by,created_at';
			break;
		case '0_cust_allocations':
		case '0_voided_cust_allocations':
			$columns = 'id,person_id,amt,date_alloc,trans_no_from,trans_type_from,trans_no_to,trans_type_to';
			break;
		case '0_debtor_trans_details':
		case '0_voided_debtor_trans_details':
			$columns = 'id,debtor_trans_no,debtor_trans_type,stock_id,description,unit_price,unit_tax,quantity,discount_percent,discount_amount,standard_cost,qty_done,src_id,govt_fee,govt_bank_account,bank_service_charge,bank_service_charge_vat,pf_amount,transaction_id,ed_transaction_id,application_id,user_commission,created_by,updated_by,created_at,updated_at,ref_name,transaction_id_updated_at';
			break;
		case '0_gl_trans':
		case '0_voided_gl_trans':
			$columns = 'counter,type,type_no,tran_date,account,axispro_subledger_code,memo_,amount,transaction_id,dimension_id,dimension2_id,person_type_id,person_id,reconciled,created_by';
			break;
		case '0_grn_items':
		case '0_voided_grn_items':
			$columns = 'id,grn_batch_id,po_detail_item,item_code,description,qty_recd,quantity_inv';
			break;
		case '0_journal':
		case '0_voided_journal':
			$columns = 'type,trans_no,tran_date,reference,source_ref,event_date,doc_date,currency,amount,rate';
			break;
		case '0_purch_orders':
		case '0_voided_purch_orders':
			$columns = 'order_no,supplier_id,comments,ord_date,reference,requisition_no,into_stock_location,delivery_address,total,prep_amount,alloc,tax_included,pay_terms,quote_file,terms_and_cond';
			break;
		case '0_purch_order_details':
		case '0_voided_purch_order_details':
			$columns = 'po_detail_item,order_no,item_code,description,delivery_date,qty_invoiced,unit_price,act_price,std_cost_unit,quantity_ordered,quantity_received';
			break;
		case '0_sales_order_details':
		case '0_voided_sales_order_details':
			$columns = 'id,order_no,trans_type,stk_code,description,qty_sent,unit_price,quantity,invoiced,discount_percent,discount_amount,govt_fee,bank_service_charge,bank_service_charge_vat,pf_amount,transaction_id,ed_transaction_id,application_id,govt_bank_account,ref_name';
			break;
		case '0_sales_orders':
		case '0_voided_sales_orders':
			$columns = 'order_no,trans_type,version,type,debtor_no,branch_code,reference,customer_ref,comments,ord_date,order_type,ship_via,delivery_address,contact_phone,contact_email,deliver_to,freight_cost,from_stk_loc,delivery_date,payment_terms,total,prep_amount,alloc';
			break;
		case '0_voided_moves':
		case '0_voided_stock_moves':
			$columns = 'trans_id,trans_no,stock_id,type,loc_code,tran_date,price,reference,qty,standard_cost';
			break;
		case '0_supp_allocations':
		case '0_voided_supp_allocations':
			$columns = 'id,person_id,amt,date_alloc,trans_no_from,trans_type_from,trans_no_to,trans_type_to';
			break;
		case '0_supp_invoice_items':
		case '0_voided_supp_invoice_items':
			$columns = 'id,supp_trans_no,supp_trans_type,gl_code,grn_item_id,po_detail_item_id,stock_id,description,quantity,unit_price,unit_tax,memo_,dimension_id,dimension2_id';
			break;
		case '0_supp_trans':
		case '0_voided_supp_trans':
			$columns='trans_no,type,supplier_id,reference,supp_reference,tran_date,due_date,ov_amount,ov_discount,ov_gst,rate,alloc,tax_included';
			break;
		case '0_trans_tax_details':
		case '0_voided_trans_tax_details':
			$columns = 'id,trans_type,trans_no,tran_date,tax_type_id,rate,ex_rate,included_in_price,net_amount,amount,memo,reg_type';
			break;
		
		default:
			$columns = '*';
			break;
	}
	if($prefix_for_column!=''){
		$column_array = explode(',',$columns);
		foreach ($column_array as $key => $column) {
			$column_array[$key] = $prefix_for_column . ".".$column;
		}
		$columns = implode (',', $column_array);

	}

	return $columns;
}
function before_void_journal_trans($type, $type_no){
	if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_journal WHERE type=".db_escape($type)." AND trans_no=".db_escape($type_no)))==0){
		$columns = get_table_columns('0_voided_journal');
		$sql_before_void = "INSERT INTO ".TB_PREF."voided_journal($columns) SELECT $columns from ".TB_PREF."journal WHERE type=".db_escape($type)." AND trans_no=".db_escape($type_no);
		db_query($sql_before_void, "could not void journal transaction for type=$type and trans_no=$type_no");
	}
}

function before_void_gl_trans($type, $trans_id){
	if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_gl_trans WHERE type=".db_escape($type)." AND type_no=".db_escape($trans_id)))==0){
	$columns = get_table_columns('0_voided_gl_trans'); 
		$sql_before_void = "INSERT INTO ".TB_PREF."voided_gl_trans($columns) SELECT $columns from ".TB_PREF."gl_trans WHERE type=".db_escape($type)." AND type_no=".db_escape($trans_id);
		db_query($sql_before_void, "could not void gl transactions for type=$type and trans_no=$trans_id");
	}
}

function get_sql_before_void_sales_orders($type,$type_no,$void=false){
	$sales_orders_table = $void==false ? "0_sales_orders" : "0_voided_sales_orders";
	$cust_allocations_table = $void==false ? "0_cust_allocations" : "0_voided_cust_allocations";
	$columns = get_table_columns($sales_orders_table,'so');
	$sql = "SELECT $columns from ".$sales_orders_table." so
	LEFT JOIN ".$cust_allocations_table." ca ON ca.trans_type_to=so.trans_type AND ca.trans_no_to=so.order_no AND ca.person_id=so.debtor_no
	WHERE  ((ca.trans_type_from=".db_escape($type)." AND ca.trans_no_from=".db_escape($type_no).")
		OR (ca.trans_type_to=".db_escape($type)." AND ca.trans_no_to=".db_escape($type_no)."))";
return $sql;
}
function before_void_cust_allocations($type, $type_no){
	// $sql_before_void = "INSERT INTO ".TB_PREF."voided_debtor_trans SELECT * from ".TB_PREF."debtor_trans WHERE type=".db_escape($type)." AND trans_no=".db_escape($type_no);
	// db_query($sql_before_void, "could not void debtor transactions for type=$type and trans_no=$type_no");
	before_void_cust_trans($type,$type_no);

	if(db_num_rows(db_query(get_sql_before_void_sales_orders($type,$type_no,true)))==0){
	$columns = get_table_columns('0_voided_sales_orders');  
		$sql_before_void2 = "INSERT IGNORE INTO ".TB_PREF."voided_sales_orders($columns) ".get_sql_before_void_sales_orders($type,$type_no,false);
		db_query($sql_before_void2, "could not void sales orders for type=$type and trans_no=$type_no");
	}

	if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_cust_allocations  WHERE  ((trans_type_from=".db_escape($type)." AND trans_no_from=".db_escape($type_no).")
		OR (trans_type_to=".db_escape($type)." AND trans_no_to=".db_escape($type_no)."))"))==0){ 

		$columns = get_table_columns('0_voided_cust_allocations');
		$sql_before_void3 = "INSERT INTO ".TB_PREF."voided_cust_allocations($columns) SELECT $columns from ".TB_PREF."cust_allocations WHERE  ((trans_type_from=".db_escape($type)." AND trans_no_from=".db_escape($type_no).")
			OR (trans_type_to=".db_escape($type)." AND trans_no_to=".db_escape($type_no)."))";
db_query($sql_before_void3, "could not void customer allocations for type=$type and trans_no=$type_no");

}

}
function get_sql_before_void_cust_trans($type,$type_no,$void=false){
	$debtor_trans_table = $void==false ? "0_debtor_trans" : "0_voided_debtor_trans";
	$cust_allocations_table = $void==false ? "0_cust_allocations" : "0_voided_cust_allocations";
	$paym_columns = get_table_columns('0_voided_debtor_trans','paym');
	$dt_columns = get_table_columns('0_voided_debtor_trans','dt');
	$trans_columns = get_table_columns('0_voided_debtor_trans','trans');

	$sql = "(SELECT $dt_columns FROM ".$debtor_trans_table." dt
		LEFT JOIN ".$cust_allocations_table." ca ON ca.trans_type_to=dt.type AND ca.trans_no_to=dt.trans_no AND ca.person_id=dt.debtor_no
		WHERE  ((ca.trans_type_from=".db_escape($type)." AND ca.trans_no_from=".db_escape($type_no).")
			OR (ca.trans_type_to=".db_escape($type)." AND ca.trans_no_to=".db_escape($type_no)."))
)
UNION
(SELECT $paym_columns FROM ".$debtor_trans_table." paym
	LEFT JOIN ".$cust_allocations_table." ca ON ca.trans_type_from=paym.type AND ca.trans_no_from=paym.trans_no AND ca.person_id=paym.debtor_no
	WHERE  ((ca.trans_type_from=".db_escape($type)." AND ca.trans_no_from=".db_escape($type_no).")
		OR (ca.trans_type_to=".db_escape($type)." AND ca.trans_no_to=".db_escape($type_no).")))
UNION 
(SELECT $trans_columns FROM ".$debtor_trans_table." trans
	WHERE trans.trans_no = ".db_escape($type_no)." AND trans.type = ".db_escape($type)."
	)";

return $sql;

}
function before_void_cust_trans($type,$type_no){
	if(db_num_rows(db_query(get_sql_before_void_cust_trans($type,$type_no,true)))==0){
		$columns = get_table_columns('0_voided_debtor_trans');
		$sql_before_void = "INSERT IGNORE INTO ".TB_PREF."voided_debtor_trans($columns) ".get_sql_before_void_cust_trans($type,$type_no,false);
		db_query($sql_before_void, "could not void debtor transactions for type=$type and trans_no=$type_no");

	}
}
function get_sql_before_void_purch_orders($type,$type_no,$void=false){
	$purch_orders_table = $void==false ? "0_purch_orders" : "0_voided_purch_orders";
	$supp_allocations_table = $void==false ? "0_supp_allocations" : "0_voided_supp_allocations";
	$columns = get_table_columns($purch_orders_table,'po');

	$sql = "SELECT $columns from ".$purch_orders_table." po
	LEFT JOIN ".$supp_allocations_table." ca ON ca.trans_type_to=".ST_PURCHORDER." AND ca.trans_no_to=po.order_no AND ca.person_id=po.supplier_id
	WHERE  ((ca.trans_type_from=".db_escape($type)." AND ca.trans_no_from=".db_escape($type_no).")
		OR (ca.trans_type_to=".db_escape($type)." AND ca.trans_no_to=".db_escape($type_no)."))";
return $sql;
}
function before_void_supp_allocations($type, $type_no){
	// $sql_before_void = "INSERT INTO ".TB_PREF."voided_supp_trans SELECT * from ".TB_PREF."supp_trans WHERE type=".db_escape($type)." AND trans_no=".db_escape($type_no);
	// 	db_query($sql_before_void, "could not void debtor transactions for type=$type and trans_no=$type_no");
	before_void_supp_trans($type, $type_no);

	if(db_num_rows(db_query(get_sql_before_void_purch_orders($type,$type_no,true)))==0){ 
		$columns = get_table_columns('0_voided_purch_orders');
		$sql_before_void2 = "INSERT INTO ".TB_PREF."voided_purch_orders($columns) ".get_sql_before_void_purch_orders($type,$type_no,false)."ON DUPLICATE KEY UPDATE total = po.total,alloc = po.alloc, ord_date = po.ord_date";
		db_query($sql_before_void2, "could not void purchase orders for type=$type and trans_no=$type_no");
	}

	if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_supp_allocations WHERE  ((trans_type_from=".db_escape($type)." AND trans_no_from=".db_escape($type_no).")
		OR (trans_type_to=".db_escape($type)." AND trans_no_to=".db_escape($type_no)."))"))==0){
		$columns = get_table_columns('0_voided_supp_allocations');
		$sql_before_void3 = "INSERT INTO ".TB_PREF."voided_supp_allocations($columns) SELECT $columns from ".TB_PREF."supp_allocations WHERE  ((trans_type_from=".db_escape($type)." AND trans_no_from=".db_escape($type_no).")
			OR (trans_type_to=".db_escape($type)." AND trans_no_to=".db_escape($type_no)."))";
db_query($sql_before_void3, "could not void customer allocations for type=$type and trans_no=$type_no");

}
}
function get_sql_before_void_supp_trans($type,$type_no,$void=false){
	$supp_trans_table = $void==false ? "0_supp_trans" : "0_voided_supp_trans";
	$supp_allocations_table = $void==false ? "0_supp_allocations" : "0_voided_supp_allocations";
	$st_columns = get_table_columns($supp_trans_table,'st');
	$pay_columns = get_table_columns($supp_trans_table,'paym');
	$trans_columns = get_table_columns($supp_trans_table,'trans');

	$sql = "(SELECT $st_columns FROM ".$supp_trans_table." st
		LEFT JOIN ".$supp_allocations_table." ca ON ca.trans_type_to=st.type AND ca.trans_no_to=st.trans_no AND ca.person_id=st.supplier_id
		WHERE  ((ca.trans_type_from=".db_escape($type)." AND ca.trans_no_from=".db_escape($type_no).")
			OR (ca.trans_type_to=".db_escape($type)." AND ca.trans_no_to=".db_escape($type_no)."))
)
UNION
(SELECT $pay_columns FROM ".$supp_trans_table." paym
	LEFT JOIN ".$supp_allocations_table." ca ON ca.trans_type_from=paym.type AND ca.trans_no_from=paym.trans_no AND ca.person_id=paym.supplier_id
	WHERE  ((ca.trans_type_from=".db_escape($type)." AND ca.trans_no_from=".db_escape($type_no).")
		OR (ca.trans_type_to=".db_escape($type)." AND ca.trans_no_to=".db_escape($type_no).")))
UNION 
(SELECT $trans_columns FROM ".$supp_trans_table." trans
	WHERE trans.trans_no = ".db_escape($type_no)." AND trans.type = ".db_escape($type)."
	)";

return $sql;

}
function before_void_supp_trans($type, $type_no){
	if(db_num_rows(db_query(get_sql_before_void_supp_trans($type,$type_no,true)))==0){
		$columns = get_table_columns('0_voided_supp_trans');
		$sql_before_void = "INSERT IGNORE INTO ".TB_PREF."voided_supp_trans($columns) ".get_sql_before_void_supp_trans($type,$type_no,false);
		db_query($sql_before_void, "could not void supplier transactions for type=$type and trans_no=$type_no");

	}
}
function before_void_trans_tax_details($type, $type_no){
	if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_trans_tax_details WHERE trans_type=".db_escape($type)." AND trans_no=".db_escape($type_no)))==0){
		$columns = get_table_columns('0_voided_trans_tax_details');
		$sql_before_void = "INSERT INTO ".TB_PREF."voided_trans_tax_details($columns) SELECT $columns from ".TB_PREF."trans_tax_details WHERE trans_type=".db_escape($type)." AND trans_no=".db_escape($type_no);
		db_query($sql_before_void, "The transaction tax details could not be voided");
	}
}

// Related to post_void customer_trans
function before_void_reward_points($type,$type_no){
	if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_customer_rewards WHERE trans_type=".db_escape($type)." AND trans_no=".db_escape($type_no)))==0){
		$columns = get_table_columns('0_voided_customer_rewards');
		$sql_before_void = "INSERT INTO ".TB_PREF."voided_customer_rewards($columns) SELECT $columns from customer_rewards WHERE trans_type=".db_escape($type)." AND trans_no=".db_escape($type_no);
		db_query($sql_before_void, "Could not delete from customer rewards table");
		
	}
}

function before_update_prepaid_so_line($line_id){
	if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_sales_order_details WHERE id=".db_escape($line_id)))==0){
		$columns = get_table_columns('0_voided_sales_order_details');
		$sql_before_void = "INSERT INTO ".TB_PREF."voided_sales_order_details($columns) SELECT $columns from ".TB_PREF."sales_order_details WHERE id=".db_escape($line_id);
		db_query($sql_before_void, "The document detail record could not be updated with invoiced qty");
	}
}

function before_update_parent_line($line_id){
	before_update_prepaid_so_line($line_id);
}

function before_void_customer_trans_details($type, $type_no){

	if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_debtor_trans_details WHERE debtor_trans_no=" . db_escape($type_no) . "
		AND debtor_trans_type=" . db_escape($type)))==0){
		$columns = get_table_columns('0_voided_debtor_trans_details');
		$sql_before_void = "INSERT INTO ".TB_PREF."voided_debtor_trans_details($columns) SELECT $columns from ".TB_PREF."debtor_trans_details WHERE debtor_trans_no=" . db_escape($type_no) . "
	AND debtor_trans_type=" . db_escape($type);
	db_query($sql_before_void, "The debtor transaction details could not be voided");
}

}

function before_void_stock_move($type, $type_no){
	if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_stock_moves WHERE trans_no=" . db_escape($type_no) . "
		AND type=" . db_escape($type)))==0){
		$columns = get_table_columns('0_voided_stock_moves');
		$sql_before_void = "INSERT INTO ".TB_PREF."voided_stock_moves($columns) SELECT $columns from ".TB_PREF."stock_moves WHERE trans_no=" . db_escape($type_no) . "
	AND type=" . db_escape($type);
	db_query($sql_before_void, "The stock movement cannot be delated");
}
}

// Related to post_void_supp_trans($type, $type_no)
function before_void_bank_trans($type, $type_no){
	if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_bank_trans WHERE trans_no=" . db_escape($type_no) . "
		AND type=" . db_escape($type)))==0){
		$columns = get_table_columns('0_voided_bank_trans');
		$sql_before_void = "INSERT INTO ".TB_PREF."voided_bank_trans($columns) SELECT $columns from ".TB_PREF."bank_trans WHERE trans_no=" . db_escape($type_no) . "
	AND type=" . db_escape($type);
	db_query($sql_before_void, "could not void bank transactions for type=$type and trans_no=$type_no");
}
}
function before_void_supp_invoice_items($type, $type_no){
	if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_supp_invoice_items WHERE supp_trans_no=" . db_escape($type_no) . "
		AND supp_trans_type=" . db_escape($type)))==0){
		$columns = get_table_columns('0_voided_supp_invoice_items');
		$sql_before_void = "INSERT INTO ".TB_PREF."voided_supp_invoice_items($columns) SELECT $columns from ".TB_PREF."supp_invoice_items WHERE supp_trans_type = ".db_escape($type)." AND supp_trans_no=".db_escape($type_no);
	db_query($sql_before_void, "could not void bank transactions for type=$type and trans_no=$type_no");
}
}


// ALTER TABLE `0_voided_debtor_trans` ADD UNIQUE KEY (trans_no,type);
// ALTER TABLE `0_voided_supp_trans` ADD UNIQUE KEY (trans_no,type);


// // ST_SUPPINVOICE , ST_SUPPAYMENT ERP\purchasing\includes\db\supp_trans_db.inc:
// function post_void_supp_trans($type, $type_no)
// {
// 	if ($type == ST_SUPPAYMENT)
// 	{
//     		void_supp_payment($type, $type_no); //done
//     		return true;
//     	}

//     	if ($type == ST_SUPPINVOICE || $type == ST_SUPPCREDIT)
//     	{
//     		void_supp_invoice($type, $type_no);
//     		return true;
//     	}

//     	if ($type == ST_SUPPRECEIVE)
//     	{
//     		return void_grn($type_no);
//     	}

//     	return false;
//     }
// // ERP\purchasing\includes\db\supp_payment_db.inc
//     function void_supp_payment($type, $type_no)
//     {
//     	begin_transaction();
//     	hook_db_prevoid($type, $type_no);

//     	void_bank_trans($type, $type_no, true); //done
//     	void_gl_trans($type, $type_no, true); //done
//     	void_supp_allocations($type, $type_no); //done
//     	void_supp_trans($type, $type_no); //done

//     	commit_transaction();
//     }

// // ERP\purchasing\includes\db\invoice_db.inc
//     function void_supp_invoice($type, $type_no)
//     {
//     	begin_transaction();

//     	hook_db_prevoid($type, $type_no);
//     	$trans = get_supp_trans($type_no, $type);

//     	void_bank_trans($type, $type_no, true); //done

//     	void_gl_trans($type, $type_no, true); //done

//     	void_supp_allocations($type, $type_no); //done

//     	void_supp_trans($type, $type_no); //done

//     	$result = get_supp_invoice_items($type, $type_no);

// 	// now remove this invoice/credit from any GRNs/POs that it's related to
//     	if (db_num_rows($result) > 0)
//     	{
//     		$date_ = Today();
//     		while ($details_row = db_fetch($result))
//     		{
//          	if ((int)$details_row["grn_item_id"] > 0) // it can be empty for GL items
//          	{
//          		// Changed 2008-10-17 by Joe Hunt to get the avg. material cost updated
//          		$old = update_supp_received_items_for_invoice($details_row["grn_item_id"],
//          			$details_row["po_detail_item_id"], -$details_row["quantity"], $details_row["FullUnitPrice"]); 

// 				//$diff = $details_row["FullUnitPrice"] - $old[2];
//          		$old_date = sql2date($old[1]);

//          		$batch = get_grn_batch_from_item($details_row["grn_item_id"]);	
//          		$grn = get_grn_batch($batch);
//          		if ($type == ST_SUPPCREDIT) // credit note 2009-06-14 Joe Hunt Must restore the po and grn
//          		{	// We must get the corresponding invoice item to check for price chg.
//          			$match = get_matching_invoice_item($details_row["stock_id"], $details_row["po_detail_item_id"]);
// 					//Chaitanya : Skipped costing block & handle in void_stock_move
// 					// We must get the corresponding invoice item to check for price chg.
// 					/*if ($match !== false)
// 						$mat_cost = update_average_material_cost($grn["supplier_id"], $details_row["stock_id"],  
// 							$match["unit_price"], -$details_row["quantity"], sql2date($match['tran_date']), $match['tran_date'] !== $trans['tran_date']);
// 					else		
// 						$mat_cost = update_average_material_cost($grn["supplier_id"], $details_row["stock_id"],  
// 							$details_row["FullUnitPrice"], -$details_row["quantity"], $old_date, $old[1] !== $trans['tran_date']);*/

// if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_purch_order_details WHERE po_detail_item = ".$details_row["po_detail_item_id"]))==0){

// 	$sql_before_void = "INSERT INTO ".TB_PREF."voided_purch_order_details SELECT * from ".TB_PREF."purch_order_details WHERE po_detail_item = ".$details_row["po_detail_item_id"];
// 	db_query($sql_before_void, "a purchase order details record could not be updated. This receipt of goods has not been processed ");
// }

// $sql = "UPDATE ".TB_PREF."purch_order_details
// SET quantity_ordered = quantity_ordered + ".-$details_row["quantity"].", ";
// if ($match !== false)
// 	$sql .= "act_price=".$match['unit_price'].", ";
// $sql .= "quantity_received = quantity_received + ".-$details_row["quantity"]."
// WHERE po_detail_item = ".$details_row["po_detail_item_id"];
// db_query($sql, "a purchase order details record could not be updated. This receipt of goods has not been processed ");

// if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_grn_items WHERE id=".$details_row["grn_item_id"]))==0){

// 	$sql_before_void2 = "INSERT INTO ".TB_PREF."voided_grn_items SELECT * from ".TB_PREF."grn_items WHERE id=".$details_row["grn_item_id"];
// 	db_query($sql_before_void2, "could not update grn items");

// }

// $sql = "UPDATE ".TB_PREF."grn_items SET qty_recd=qty_recd+".-$details_row["quantity"]."
// WHERE id=".$details_row["grn_item_id"];
// db_query($sql);

// }
// else
// {
// 	$diff = get_diff_in_home_currency($grn["supplier_id"], $old_date, sql2date($trans['tran_date']), $old[2], 
// 		$details_row["FullUnitPrice"]);
// 					// Only adjust the avg for the diff
// 	$mat_cost = update_average_material_cost(null, $details_row["stock_id"],
// 		$diff, -$details_row["quantity"], $old_date, true);

// 					//Chaitanya : Reverse effect
// 					//If QOH is 0 or negative then update_average_material_cost will be skipped
// 					//Thus difference in PO and Supp Invoice should be handled separately

// 	$qoh = get_qoh_on_date($details_row["stock_id"]);
// 	if ($diff*$details_row["quantity"] !=0 && $qoh <= 0)
// 	{
// 		global $Refs;

// 						//Chaitanya : Post a journal entry
// 		$id = get_next_trans_no(ST_JOURNAL);
// 		$ref = $Refs->get_next(ST_JOURNAL, null, $date_);
// 		add_journal(ST_JOURNAL, $id, $details_row["quantity"] * $diff, $old_date, get_company_currency(), $ref);
// 		$stock_id = $details_row["stock_id"];
// 		$stock_gl_code = get_stock_gl_code($stock_id);
// 		$memo = "Reversing Supplier invoice adjustment for zero inventory of ".$stock_id." Invoice: ".$trans['reference'];
// 						//Reverse the inventory effect if $qoh <=0
// 		add_gl_trans_std_cost(ST_JOURNAL, $id, $old_date, 
// 			$stock_gl_code["inventory_account"],
// 			$dim, $dim2, $memo, $details_row["quantity"] * $diff);
// 						//GL Posting to inventory adjustment account
// 		add_gl_trans_std_cost(ST_JOURNAL, $id, $old_date, 
// 			$stock_gl_code["adjustment_account"],
// 			$dim, $dim2, $memo, -$details_row["quantity"] * $diff);

// 		add_audit_trail(ST_JOURNAL, $id, $old_date);
// 		add_comments(ST_JOURNAL, $id, $old_date, $memo);
// 		$Refs->save(ST_JOURNAL, $id, $ref);
// 	}
// }
// }
// }
// }

// 	if ($type == ST_SUPPCREDIT) // void the credits in stock moves
// 	void_stock_move($type, $type_no); //done
// 	void_supp_invoice_items($type, $type_no); //done
// 	void_trans_tax_details($type, $type_no); //done

// 	commit_transaction();
// }

// // F:\xampp7.2\htdocs\direct-axistech\egfm_axispro\ERP\purchasing\includes\db\grn_db.inc
// function void_grn($grn_batch)
// {
// 	// if this grn is references on any invoices/credit notes, then it
// 	// can't be voided
// 	if (exists_grn_on_invoices($grn_batch))
// 		return false;

// 	begin_transaction();
// 	hook_db_prevoid(ST_SUPPRECEIVE, $grn_batch);

// 	void_bank_trans(ST_SUPPRECEIVE, $grn_batch, true);
// 	void_gl_trans(ST_SUPPRECEIVE, $grn_batch, true);

// 	// clear the quantities of the grn items in the POs and invoices
// 	$result = get_grn_items($grn_batch);

// 	if (db_num_rows($result) > 0)
// 	{

// 		while ($myrow = db_fetch($result))
// 		{
// 			if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_purch_order_details WHERE po_detail_item = " . $myrow["po_detail_item"]))==0){

// 				$sql_before_void = "INSERT INTO ".TB_PREF."voided_purch_order_details SELECT * from ".TB_PREF."purch_order_details WHERE po_detail_item = " . $myrow["po_detail_item"];
// 				db_query($sql_before_void, "a purchase order details record could not be voided.");
// 			}

// 			$sql = "UPDATE ".TB_PREF."purch_order_details
// 			SET quantity_received = quantity_received - " . $myrow["qty_recd"] . "
// 			WHERE po_detail_item = " . $myrow["po_detail_item"];

// 			db_query($sql, "a purchase order details record could not be voided.");
// 		}
// 	}

// 	if(db_num_rows(db_query("SELECT * from ".TB_PREF."voided_grn_items WHERE grn_batch_id=".db_escape($grn_batch)))==0){

// 		$sql_before_void = "INSERT INTO ".TB_PREF."voided_grn_items SELECT * from ".TB_PREF."grn_items WHERE WHERE grn_batch_id=".db_escape($grn_batch);
// 		db_query($sql_before_void, "A grn detail item could not be voided.");
// 	}

// 	// clear the quantities in the grn items
// 	$sql = "UPDATE ".TB_PREF."grn_items SET qty_recd=0, quantity_inv=0
// 	WHERE grn_batch_id=".db_escape($grn_batch);

// 	db_query($sql, "A grn detail item could not be voided.");

//     // clear the stock move items
// 	void_stock_move(ST_SUPPRECEIVE, $grn_batch);

// 	commit_transaction();

// 	return true;
// }

// // F:\xampp7.2\htdocs\direct-axistech\egfm_axispro\ERP\gl\includes\db\gl_db_bank_trans.inc
// function void_bank_trans($type, $type_no, $nested=false)
// {

// 	if (!$nested)
// 		begin_transaction();

// 	before_void_bank_trans($type, $type_no); //done
// 	$sql = "UPDATE ".TB_PREF."bank_trans 
// 	SET amount=0
// 	WHERE type=".db_escape($type)." AND trans_no=".db_escape($type_no);

// 	db_query($sql, "could not void bank transactions for type=$type and trans_no=$type_no");

// 	void_gl_trans($type, $type_no, true); //done

// 	// in case it's a customer trans - probably better to check first
// 	void_cust_allocations($type, $type_no); //done
// 	void_customer_trans($type, $type_no); //done

// 	// in case it's a supplier trans - probably better to check first
// 	void_supp_allocations($type, $type_no); //done
// 	void_supp_trans($type, $type_no); //done

// 	void_trans_tax_details($type, $type_no); //done

// 	if (!$nested)
// 		commit_transaction();
// }

// // F:\xampp7.2\htdocs\direct-axistech\egfm_axispro\ERP\purchasing\includes\db\invoice_items_db.inc
// function void_supp_invoice_items($type, $type_no)
// {
// 	before_void_supp_invoice_items($type, $type_no);
// 	$sql = "UPDATE ".TB_PREF."supp_invoice_items SET quantity=0, unit_price=0
// 	WHERE supp_trans_type = ".db_escape($type)." AND supp_trans_no=".db_escape($type_no);
// 	db_query($sql, "could not void supptrans details");
// }










// ST_CUSTPAYMENT , ST_SALESINVOICE ERP\sales\includes\db\cust_trans_db.inc:
// function post_void_customer_trans($type, $type_no)
// {


// 	switch ($type) {
// 		case ST_SALESINVOICE :
// 		case ST_CUSTCREDIT   :

//             /**
//              * Voiding Payment entries related to this invoice
//              */

//             if (isset($_POST['clear_payments']) && $_POST['clear_payments'] == 1) {

//             	$sql = "select trans_no_from from " . TB_PREF . "cust_allocations where trans_type_to=" . ST_SALESINVOICE . " 
//             	and trans_no_to=$type_no";
//             	$res = db_query($sql, "Cannot retrieve invoice payment data");;
//             	while ($row = db_fetch($res)) {
//             		void_customer_payment(ST_CUSTPAYMENT, $row['trans_no_from']);
//             		add_voided_entry(ST_CUSTPAYMENT, $row['trans_no_from'], $_POST['date_'], $_POST['memo_']);
//             	}
//             }
//             /** End */

//             void_sales_invoice($type, $type_no);

//             break;
//             case ST_CUSTDELIVERY :
//             void_sales_delivery($type, $type_no);
//             break;
//             case ST_CUSTPAYMENT :

// //            display_error(print_r($type_no,true)); die;

//             void_customer_payment($type, $type_no);
//             break;
//         }
//     }
//  // ERP\sales\includes\db\payment_db.inc
//     function void_customer_payment($type, $type_no)
//     {
//     	begin_transaction();

//     	hook_db_prevoid($type, $type_no);
// 	void_bank_trans($type, $type_no, true); //done
// 	void_gl_trans($type, $type_no, true); //done
// 	void_cust_allocations($type, $type_no); //done
// 	void_customer_trans($type, $type_no); //done


//     void_reward_points($type,$type_no); //done

//     commit_transaction();
// }

// function void_reward_points($type,$type_no) {
// 	before_void_reward_points($type,$type_no);
// 	$sql="DELETE FROM customer_rewards WHERE trans_no=".db_escape($type_no)." AND trans_type=".db_escape($type);
// 	db_query($sql,"Could not delete from customer rewards table");
// }

// // ERP\sales\includes\db\sales_invoice_db.inc
// function void_sales_invoice($type, $type_no)
// {
// 	begin_transaction();

// 	hook_db_prevoid($type, $type_no);
// 	void_bank_trans($type, $type_no, true);
// 	void_gl_trans($type, $type_no, true);

//     // reverse all the changes in parent document(s)
// 	$factor = get_cust_prepayment_invoice_factor($type_no);
// 	if ($factor != 0) {
// 		$lines = get_customer_trans_details($type, $type_no);
// 		while ($line = db_fetch($lines)) {
// 			before_update_prepaid_so_line($line['src_id']);
// 			update_prepaid_so_line($line['src_id'], -$factor * $line['quantity']);
// 		}
// 	} else {
// 		$deliveries = get_sales_parent_numbers($type, $type_no);

// 		if ($deliveries !== 0) {
// 			if ($type == ST_SALESINVOICE && count($deliveries) == 1 && get_reference(ST_CUSTDELIVERY, $deliveries[0]) == "auto") {
// 				void_sales_delivery(ST_CUSTDELIVERY, $deliveries[0], false);
// 				$date_ = Today();
// 				add_audit_trail(ST_CUSTDELIVERY, $deliveries[0], $date_, trans("Voided."));
// 				add_voided_entry(ST_CUSTDELIVERY, $deliveries[0], $date_, "");
// 			} else {
// 				$srcdetails = get_sales_parent_lines($type, $type_no);
// 				while ($row = db_fetch($srcdetails)) {
// 					before_update_parent_line($row['id']);
// 					update_parent_line($type, $row['id'], -$row['quantity']);
// 				}
// 			}
// 		}
// 	}
//     // clear details after they've been reversed in the sales order
// 	void_customer_trans_details($type, $type_no);

//     void_stock_move($type, $type_no); // in case of credit note with return

//     void_trans_tax_details($type, $type_no);

//     void_cust_allocations($type, $type_no);

//     // do this last because other voidings can depend on it - especially voiding
//     // DO NOT MOVE THIS ABOVE VOIDING or we can end up with trans with alloc < 0
//     void_customer_trans($type, $type_no);

//     commit_transaction();
// }
// // ERP\sales\includes\db\sales_order_db.inc
// function update_prepaid_so_line($line_id, $qty_invoiced)
// {
// 	$sql = "UPDATE " . TB_PREF . "sales_order_details
// 	SET invoiced = invoiced + " . (float)$qty_invoiced . "
// 	WHERE id=" . db_escape($line_id);

// 	db_query($sql, "The document detail record could not be updated with invoiced qty");
// 	return true;
// }
// // ERP\sales\includes\db\sales_delivery_db.inc
// function void_sales_delivery($type, $type_no, $transactions=true)
// {
// 	if ($transactions)
// 		begin_transaction();

// 	hook_db_prevoid($type, $type_no);

// 	void_gl_trans($type, $type_no, true); //done

// 	// reverse all the changes in the sales order
// 	$items_result = get_customer_trans_details($type, $type_no);

// 	$order = get_customer_trans_order($type, $type_no);

// 	if ($order) {
// 		$auto = (get_reference(ST_SALESORDER, $order) == "auto");
// 		while ($row = db_fetch($items_result)) {
// 			before_update_parent_line($row['src_id']);
// 			update_parent_line(ST_CUSTDELIVERY, $row['src_id'], -$row['quantity'], $auto);
// 		}
// 	}

// 	// clear details after they've been reversed in the sales order
// 	void_customer_trans_details($type, $type_no);

// 	void_trans_tax_details($type, $type_no); //done

// 	void_cust_allocations($type, $type_no); //done

// 	// do this last because other voidings can depend on it
// 	// DO NOT MOVE THIS ABOVE VOIDING or we can end up with trans with alloc < 0
// 	void_customer_trans($type, $type_no); //done

// 	if ($transactions)
// 		commit_transaction();
// }
// // ERP\sales\includes\sales_db.inc
// function update_parent_line($doc_type, $line_id, $qty_dispatched, $auto = false)
// {
// 	$doc_type = get_parent_type($doc_type);

// 	$qty_dispatched = (float)$qty_dispatched;

// 	if ($doc_type == 0)
// 		return false;
// 	else {
// 		if ($doc_type == ST_SALESORDER || $doc_type == ST_SALESQUOTE) {
// 			$sql = "UPDATE " . TB_PREF . "sales_order_details
// 			SET qty_sent = qty_sent + $qty_dispatched";
// 			if ($auto)
// 				$sql .= ", quantity = quantity + $qty_dispatched";
// 			$sql .= " WHERE id=" . db_escape($line_id);
// 		} else
// 		$sql = "UPDATE " . TB_PREF . "debtor_trans_details
// 		SET qty_done = qty_done + $qty_dispatched
// 		WHERE id=" . db_escape($line_id);
// 	}
// 	db_query($sql, "The parent document detail record could not be updated");
// 	return true;
// }
// // ERP\sales\includes\db\cust_trans_details_db.inc
// function void_customer_trans_details($type, $type_no)
// {

// 	$sql = "select created_by from " . TB_PREF . "debtor_trans_details
// 	WHERE debtor_trans_no=" . db_escape($type_no) . " AND debtor_trans_type=" . db_escape($type);;

// 	$result = db_query($sql, "Cannot retreive a debtor transaction");
// 	$row = db_fetch($result);

// 	if ($row && ($row['created_by'] != '')) {
// 		$_SESSION['voided_trans_created_by'] = $row['created_by'];
// 	}
// 	before_void_customer_trans_details($type, $type_no);
// 	$sql = "UPDATE " . TB_PREF . "debtor_trans_details SET quantity=0, unit_price=0,
// 	unit_tax=0, discount_percent=0, standard_cost=0, src_id=0
// 	WHERE debtor_trans_no=" . db_escape($type_no) . "
// 	AND debtor_trans_type=" . db_escape($type);


// 	db_query($sql, "The debtor transaction details could not be voided");

//     // clear the stock move items
// 	void_stock_move($type, $type_no);
// }

// // egfm_axispro\ERP\includes\db\inventory_db.inc
// function void_stock_move($type, $type_no)
// {
// 	$sql = "SELECT move.*, supplier.supplier_id
// 	FROM ".TB_PREF."stock_moves move
// 	LEFT JOIN ".TB_PREF."supp_trans credit ON credit.trans_no=move.trans_no AND credit.type=move.type
// 	LEFT JOIN ".TB_PREF."grn_batch grn ON grn.id=move.trans_no AND 25=move.type
// 	LEFT JOIN ".TB_PREF."suppliers supplier ON IFNULL(grn.supplier_id, credit.supplier_id)=supplier.supplier_id
// 	WHERE move.type=".db_escape($type)." AND move.trans_no=".db_escape($type_no);

// 	$result = db_query($sql, "Could not void stock moves");
// 	while ($row = db_fetch($result))
// 	{
// 		//Skip cost averaging of service items
// 		if (is_inventory_item($row["stock_id"]))
// 		{
// 			// The cost has to be adjusted.
// 			// Transaction rates are stored either as price or standard_cost depending on types
// 			$types = array(ST_SUPPCREDIT, ST_SUPPRECEIVE);
// 			if (in_array($type, $types))
// 				$unit_cost = $row["price"];
// 			else
// 				$unit_cost = $row["standard_cost"];

// 			update_average_material_cost($row["supplier_id"], $row["stock_id"],
// 				$unit_cost, -$row["qty"], sql2date($row["tran_date"]));
// 		}
// 	}
// 	before_void_stock_move($type, $type_no);
// 	$sql = "DELETE FROM ".TB_PREF."stock_moves
// 	WHERE type=".db_escape($type)
// 	."	AND trans_no=".db_escape($type_no);
// 	db_query($sql, "The stock movement cannot be delated");
// }

// // ERP\purchasing\includes\db\grn_db.inc
// function update_average_material_cost($supplier, $stock_id, $price, $qty, $date, $adj_only=false)
// {
// 	// probably this function should be optimized
// 	// passing transaction cart as argument. This would
// 	// save a couple of db calls like get_supplier()

// 	$supp = get_supplier($supplier);
// 	if ($supplier != null)
// 		$currency = $supp['curr_code'];
// 	else
// 		$currency = null;

// 	if ($supp['tax_included'])
// 		$price = get_tax_free_price_for_item($stock_id, $price, $supp['tax_group_id'],
// 			$supp['tax_included']);

// 	if ($currency != null)
// 	{
// 		$ex_rate = get_exchange_rate_to_home_currency($currency, $date);
// 		$price_in_home_currency = $price / $ex_rate;
// 	}	
// 	else
// 		$price_in_home_currency = $price;

// 	$price_in_home_currency_ = $price_in_home_currency;

// 	// Handle if inventory will become negative
//     // Skip negative inventory adjustment for case of adjust_only
//     if (is_inventory_item($stock_id) && !$adj_only) 
//         handle_negative_inventory($stock_id, $qty, $price_in_home_currency, $date);

// 	$sql = "SELECT mb_flag, material_cost FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($stock_id);
// 	$result = db_query($sql);
// 	$myrow = db_fetch($result);
// 	$material_cost = $myrow['material_cost'];

// 	$cost_adjust = false;

// 	$qoh = get_qoh_on_date($stock_id);

// 	if ($adj_only)
// 	{
// 		if ($qoh > 0)
// 			$material_cost = ($qoh * $material_cost + $qty * $price_in_home_currency) /	$qoh;
// 	}
// 	else
// 	{
// 		if ($qoh < 0)
// 		{
// 			if ($qoh + $qty >= 0)
// 				$cost_adjust = true;
// 			$qoh = 0;
// 		}
// 		if ($qoh + $qty > 0)
// 			$material_cost = ($qoh * $material_cost + $qty * $price_in_home_currency) /	($qoh + $qty);
// 	}

// 	if ($cost_adjust) // Material_cost replaced with price
// 		adjust_deliveries($stock_id, $price_in_home_currency_, $date);
// 	$sql = "UPDATE ".TB_PREF."stock_master SET material_cost=".db_escape($material_cost)."
// 		WHERE stock_id=".db_escape($stock_id);

// 	db_query($sql,"The cost details for the inventory item could not be updated");
// 	return $material_cost;
// }








// ST_JOURNAL ERP\gl\includes\db\gl_journal.inc: //done
// function void_journal_trans($type, $type_no, $use_transaction=true)
// {
// 	if ($use_transaction)
// 		begin_transaction();

// 	before_void_journal_trans($type, $type_no);


// 	$sql = "UPDATE ".TB_PREF."journal SET amount=0
// 	WHERE type=".db_escape($type)." AND trans_no=".db_escape($type_no);

// 	db_query($sql, "could not void journal transaction for type=$type and trans_no=$type_no");

// 	void_bank_trans_edited($type, $type_no, true);

// 	if ($use_transaction)
// 		commit_transaction();
// }

// ERP\gl\includes\db\gl_db_trans.inc: //done
// function void_gl_trans($type, $trans_id, $nested=false)
// {
// 	if (!$nested)
// 		begin_transaction();


// 	$sql = "SELECT SUM(abs(amount)/2) as voided_amount,tran_date FROM ".TB_PREF."gl_trans WHERE type=".db_escape($type)
// 	." AND type_no=".db_escape($trans_id);

// 	$result = db_query($sql, "Cannot retreive a debtor transaction");
// 	$row = db_fetch($result);




// 	if($row && ($row['voided_amount'] != 0)) {
// 		$_SESSION['voided_amount'] = ($row['voided_amount']);
// 		$_SESSION['voided_gl_trans_date'] = $row['tran_date'];
// 	}

// //    display_error(print_r($_SESSION['voided_amount'] ,true));
// //    display_error(print_r($_SESSION['voided_gl_trans_date'] ,true));

// 	before_void_gl_trans($type, $trans_id);

// 	$sql = "UPDATE ".TB_PREF."gl_trans SET amount=0 WHERE type=".db_escape($type)
// 	." AND type_no=".db_escape($trans_id);

// 	db_query($sql, "could not void gl transactions for type=$type and trans_no=$trans_id");

// 	if (!$nested)
// 		commit_transaction();
// }
//ERP\sales\includes\db\custalloc_db.inc //done
// function void_cust_allocations($type, $type_no, $date="")
// {
// 	before_void_cust_allocations($type, $type_no);
// 	return clear_cust_alloctions($type, $type_no, null, $date);
// }
// function clear_cust_alloctions($type, $type_no, $person_id=null, $date="")
// {
// 	$sql = "UPDATE  ".TB_PREF."cust_allocations ca
// 	LEFT JOIN ".TB_PREF."debtor_trans paym ON ca.trans_type_from=paym.type AND ca.trans_no_from=paym.trans_no AND ca.person_id=paym.debtor_no
// 	LEFT JOIN ".TB_PREF."debtor_trans dt ON ca.trans_type_to=dt.type AND ca.trans_no_to=dt.trans_no AND ca.person_id=dt.debtor_no
// 	LEFT JOIN ".TB_PREF."sales_orders so ON ca.trans_type_to=so.trans_type AND ca.trans_no_to=so.order_no AND ca.person_id=so.debtor_no
// 	SET paym.alloc=paym.alloc - ca.amt,
// 	dt.alloc=dt.alloc -  ca.amt,
// 	so.alloc=so.alloc -  ca.amt
// 	WHERE  ((ca.trans_type_from=".db_escape($type)." AND ca.trans_no_from=".db_escape($type_no).")
// 		OR (ca.trans_type_to=".db_escape($type)." AND ca.trans_no_to=".db_escape($type_no)."))";
// if ($person_id)
// 	$sql .= " AND ca.person_id=".db_escape($person_id);
// db_query($sql, "could not clear allocation");

// 	// remove any allocations for this transaction
// $sql = "DELETE FROM ".TB_PREF."cust_allocations
// WHERE  ((trans_type_from=".db_escape($type)." AND trans_no_from=".db_escape($type_no).")
// 	OR (trans_type_to=".db_escape($type)." AND trans_no_to=".db_escape($type_no)."))";
// if ($person_id)
// 	$sql .= " AND person_id=".db_escape($person_id);

// db_query($sql, "could not void debtor transactions for type=$type and trans_no=$type_no");
// // is this necessary?
// //	if ($date != "")
// //		exchange_variation($type, $type_no, $row['trans_type_to'], $row['trans_no_to'], $date,
// //			$row['amt'], PT_CUSTOMER, true);
// }

// ERP\sales\includes\db\cust_trans_db.inc: //done
// function void_customer_trans($type, $type_no)
// {
// 	before_void_cust_trans($type,$type_no);
//     // clear all values and mark as void
// 	$sql = "UPDATE " . TB_PREF . "debtor_trans SET ov_amount=0, ov_discount=0, ov_gst=0, ov_freight=0,inv_total=0,
// 	ov_freight_tax=0, alloc=0, prep_amount=0, version=version+1 WHERE type=" . db_escape($type) . " AND trans_no=" . db_escape($type_no);

// 	db_query($sql, "could not void debtor transactions for type=$type and trans_no=$type_no");
// }

// ERP\purchasing\includes\db\suppalloc_db.inc: //done
// function void_supp_allocations($type, $type_no, $date="")
// {
// 	before_void_supp_allocations($type, $type_no);
// 	return clear_supp_alloctions($type, $type_no, null, $date);
// }
// function clear_supp_alloctions($type, $type_no, $person_id=null, $date="",$voided=false)
// {
// 	$sql = "UPDATE  ".TB_PREF."supp_allocations ca
// 	LEFT JOIN ".TB_PREF."supp_trans paym ON ca.trans_type_from=paym.type AND ca.trans_no_from=paym.trans_no AND ca.person_id=paym.supplier_id
// 	LEFT JOIN ".TB_PREF."supp_trans st ON ca.trans_type_to=st.type AND ca.trans_no_to=st.trans_no AND ca.person_id=st.supplier_id
// 	LEFT JOIN ".TB_PREF."purch_orders po ON ca.trans_type_to=".ST_PURCHORDER." AND ca.trans_no_to=po.order_no AND ca.person_id=po.supplier_id
// 	SET paym.alloc=paym.alloc - ca.amt,
// 	st.alloc=st.alloc -  ca.amt,
// 	po.alloc=po.alloc -  ca.amt
// 	WHERE  ((ca.trans_type_from=".db_escape($type)." AND ca.trans_no_from=".db_escape($type_no).")
// 		OR (ca.trans_type_to=".db_escape($type)." AND ca.trans_no_to=".db_escape($type_no)."))";
// if ($person_id)
// 	$sql .= " AND ca.person_id=".db_escape($person_id);
// db_query($sql, "could not clear allocation");

// 	// remove any allocations for this transaction
// $sql = "DELETE FROM ".TB_PREF."supp_allocations
// WHERE  ((trans_type_from=".db_escape($type)." AND trans_no_from=".db_escape($type_no).")
// 	OR (trans_type_to=".db_escape($type)." AND trans_no_to=".db_escape($type_no)."))";
// if ($person_id)
// 	$sql .= " AND person_id=".db_escape($person_id);

// db_query($sql, "could not void supp transactions for type=$type and trans_no=$type_no");
// }

// // ERP\purchasing\includes\db\supp_trans_db.inc: //done
// function void_supp_trans($type, $type_no)
// {
// 	before_void_supp_trans($type, $type_no);
// 	$sql = "UPDATE ".TB_PREF."supp_trans SET ov_amount=0, ov_discount=0, ov_gst=0,
// 	alloc=0 WHERE type=".db_escape($type)." AND trans_no=".db_escape($type_no);

// 	db_query($sql, "could not void supp transactions for type=$type and trans_no=$type_no");
// }

// ERP\gl\includes\db\gl_db_trans.inc: //done
// function void_trans_tax_details($type, $type_no)
// {
// 	before_void_trans_tax_details($type, $type_no);

// 	$sql = "UPDATE ".TB_PREF."trans_tax_details SET amount=0, net_amount=0
// 	WHERE trans_no=".db_escape($type_no)
// 	." AND trans_type=".db_escape($type);

// 	db_query($sql, "The transaction tax details could not be voided");
// }