<?php
/**********************************************************************
    Direct Axis Technology L.L.C.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
//----------------------------------------------------------------------------------------

function add_cust_allocation($amount, $trans_type_from, $trans_no_from,
	$trans_type_to, $trans_no_to, $person_id, $date_)
{
	$date = date2sql($date_);
	$sql = "INSERT INTO ".TB_PREF."cust_allocations (
		amt, date_alloc,
		trans_type_from, trans_no_from, trans_no_to, trans_type_to, person_id)
		VALUES ($amount, '$date', ".db_escape($trans_type_from).", ".db_escape($trans_no_from).", ".db_escape($trans_no_to)
		.", ".db_escape($trans_type_to).", ".db_escape($person_id).")";

	db_query($sql, "A customer allocation could not be added to the database");
}

//----------------------------------------------------------------------------------------


function delete_cust_allocation($trans_id)
{
	$sql = "DELETE FROM ".TB_PREF."cust_allocations WHERE id = ".db_escape($trans_id);
	return db_query($sql, "The existing allocation $trans_id could not be deleted");
}

//----------------------------------------------------------------------------------------

function get_cust_allocation($trans_id)
{
	$sql = "SELECT * FROM ".TB_PREF."cust_allocations WHERE id = ".db_escape($trans_id);
	return db_fetch(db_query($sql), "Cannot retrieve customer allocation $trans_id");
}

//----------------------------------------------------------------------------------------
//	Update debtor trans alloc field according to current status of cust_allocations
//
function update_debtor_trans_allocation($trans_type, $trans_no, $person_id)
{
	$sql = 
		"UPDATE `".TB_PREF.($trans_type==ST_SALESORDER ? 'sales_orders' : 'debtor_trans')."` trans,
			(SELECT sum(amt) amt FROM ".TB_PREF."cust_allocations
				WHERE person_id=".db_escape($person_id)." AND ((trans_type_to=".db_escape($trans_type)." AND trans_no_to=".db_escape($trans_no).")
				OR (trans_type_from=".db_escape($trans_type)." AND trans_no_from=".db_escape($trans_no)."))) allocated
		SET 
			trans.alloc=IFNULL(allocated.amt,0)
		WHERE trans.debtor_no=".db_escape($person_id)." AND ".($trans_type==ST_SALESORDER ?
			   "trans.trans_type=".db_escape($trans_type)." AND order_no=".db_escape($trans_no)
			:  "trans.type=".db_escape($trans_type)." AND trans_no=".db_escape($trans_no));
	db_query($sql, "The debtor transaction record could not be modified for the allocation against it");
}

//-------------------------------------------------------------------------------------------------------------
//
function void_cust_allocations($type, $type_no, $date="")
{
	before_void_cust_allocations($type, $type_no);
	return clear_cust_alloctions($type, $type_no, null, $date);
}

//-------------------------------------------------------------------------------------------------------------
//
function clear_cust_alloctions($type, $type_no, $person_id=null, $date="")
{
	$sql = "UPDATE  ".TB_PREF."cust_allocations ca
				LEFT JOIN ".TB_PREF."debtor_trans paym ON ca.trans_type_from=paym.type AND ca.trans_no_from=paym.trans_no AND ca.person_id=paym.debtor_no
				LEFT JOIN ".TB_PREF."debtor_trans dt ON ca.trans_type_to=dt.type AND ca.trans_no_to=dt.trans_no AND ca.person_id=dt.debtor_no
				LEFT JOIN ".TB_PREF."sales_orders so ON ca.trans_type_to=so.trans_type AND ca.trans_no_to=so.order_no AND ca.person_id=so.debtor_no
	 		SET paym.alloc=paym.alloc - ca.amt,
	 			dt.alloc=dt.alloc -  ca.amt,
	 			so.alloc=so.alloc -  ca.amt
			WHERE  ((ca.trans_type_from=".db_escape($type)." AND ca.trans_no_from=".db_escape($type_no).")
				OR (ca.trans_type_to=".db_escape($type)." AND ca.trans_no_to=".db_escape($type_no)."))";
	if ($person_id)
		$sql .= " AND ca.person_id=".db_escape($person_id);
	db_query($sql, "could not clear allocation");

	// remove any allocations for this transaction
	$sql = "DELETE FROM ".TB_PREF."cust_allocations
			WHERE  ((trans_type_from=".db_escape($type)." AND trans_no_from=".db_escape($type_no).")
				OR (trans_type_to=".db_escape($type)." AND trans_no_to=".db_escape($type_no)."))";
	if ($person_id)
		$sql .= " AND person_id=".db_escape($person_id);

	db_query($sql, "could not void debtor transactions for type=$type and trans_no=$type_no");
// is this necessary?
//	if ($date != "")
//		exchange_variation($type, $type_no, $row['trans_type_to'], $row['trans_no_to'], $date,
//			$row['amt'], PT_CUSTOMER, true);
}
//-------------------------------------------------------------------------------------------------------------

function get_allocatable_from_cust_sql($customer_id=null, $settled, $for_update = false)
{
	$sql = "SELECT
		trans.type,
		trans.trans_no,
		trans.reference,
		trans.tran_date,
		debtor.name AS DebtorName, 
		debtor.curr_code,
		ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount AS Total,
		trans.alloc,
		trans.due_date,
		debtor.address,
		trans.version,
		round(abs(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount) - alloc,6) <= 1e-6 AS settled,
		trans.debtor_no

	 FROM "
	 	.TB_PREF."debtor_trans as trans, "
		.TB_PREF."debtors_master as debtor"
	." WHERE trans.debtor_no=debtor.debtor_no
		AND (((type=".ST_CUSTPAYMENT." OR type=".ST_BANKDEPOSIT.") AND (trans.ov_amount > 0))
		 OR (type=".ST_CUSTCREDIT. " AND (ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)>0)
		 OR (type=".ST_JOURNAL. " AND (trans.ov_amount < 0)))";

	if (!$settled)
		$sql .= " AND (round(abs(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount) - alloc,6) > 1e-6)";

	if ($customer_id != null)
		$sql .= " AND trans.debtor_no = ".db_escape($customer_id);

	if($for_update){
		$sql .= " FOR UPDATE";
	}

	return $sql;
}

function get_allocatable_sales_orders($customer_id = null, $trans_no=null, $type=null,$voided=false)
{
	if($voided){
	$sql = "SELECT
		sorder.trans_type as type,
		sorder.order_no as trans_no,
		sorder.reference,
		sorder.ord_date,
		debtor.name AS DebtorName, 
		debtor.curr_code,
		total-IFNULL(invoiced.amount,0) as Total,
		sorder.alloc,
		sorder.delivery_date as due_date,
		debtor.address,
		sorder.version,
		amt,
		sorder.debtor_no,
		sorder.branch_code,'',''  
		FROM ".TB_PREF."voided_sales_orders as sorder
			LEFT JOIN ".TB_PREF."debtors_master as debtor ON sorder.debtor_no = debtor.debtor_no
			LEFT JOIN ".TB_PREF."voided_cust_allocations as alloc 
				ON sorder.order_no = alloc.trans_no_to AND sorder.trans_type = alloc.trans_type_to AND alloc.person_id=sorder.debtor_no
			LEFT JOIN (SELECT order_, sum(prep_amount) amount FROM ".TB_PREF."voided_debtor_trans dt
			WHERE prep_amount>0 AND dt.type=".ST_SALESINVOICE." GROUP BY order_) as invoiced ON sorder.order_no = invoiced.order_
		WHERE sorder.trans_type=".ST_SALESORDER;
	}else{
	$sql = "SELECT
		sorder.trans_type as type,
		sorder.order_no as trans_no,
		sorder.reference,
		sorder.ord_date,
		debtor.name AS DebtorName, 
		debtor.curr_code,
		total-IFNULL(invoiced.amount,0) as Total,
		sorder.alloc,
		sorder.delivery_date as due_date,
		debtor.address,
		sorder.version,
		amt,
		sorder.debtor_no,
		sorder.branch_code,'',''  
		FROM ".TB_PREF."sales_orders as sorder
			LEFT JOIN ".TB_PREF."debtors_master as debtor ON sorder.debtor_no = debtor.debtor_no
			LEFT JOIN ".TB_PREF."cust_allocations as alloc 
				ON sorder.order_no = alloc.trans_no_to AND sorder.trans_type = alloc.trans_type_to AND alloc.person_id=sorder.debtor_no
			LEFT JOIN (SELECT order_, sum(prep_amount) amount FROM ".TB_PREF."debtor_trans dt
			WHERE prep_amount>0 AND dt.type=".ST_SALESINVOICE." GROUP BY order_) as invoiced ON sorder.order_no = invoiced.order_
		WHERE sorder.trans_type=".ST_SALESORDER;
		
	}

	if ($trans_no != null and $type != null)
	{
		$sql .= " AND alloc.trans_no_from=".db_escape($trans_no)."
				  AND alloc.trans_type_from=".db_escape($type);
	}
	else
	{
		$sql .= " AND round(sorder.prep_amount)>0 and Total>0"; // only sales orders with prepayment level set and no yet fully invoiced
	}
	if ($customer_id)
		$sql .= " AND sorder.debtor_no=".db_escape($customer_id);

	$sql .= " GROUP BY sorder.order_no";

	return $sql;
}
//-------------------------------------------------------------------------------------------------------------
// Before changing for voided transactions recovery
// function get_allocatable_to_cust_transactions($customer_id = null, $trans_no=null, $type=null,
//                                               $barcode=null,$invoice_from_date=null,$invoice_to_date=null,$dimension_id = null)
// {


//     if(isset($_SESSION['dim_id'])){
//         $dimension_id = $_SESSION['dim_id'];
//         unset($_SESSION['dim_id']);
//     }


// 	$sql = "SELECT
// 		trans.type,
// 		trans.trans_no,
// 		trans.reference,
// 		trans.tran_date,
// 		debtor.name AS DebtorName, 
// 		debtor.curr_code,
		
// 		IF(prep_amount, prep_amount, ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount) AS Total,
		
// 		trans.alloc,
// 		trans.due_date,
// 		debtor.address,
// 		trans.version,
// 		amt,
// 		trans.debtor_no,
// 		trans.branch_code, trans.payment_flag  
// 	 FROM ".TB_PREF."debtor_trans as trans
// 			LEFT JOIN ".TB_PREF."cust_allocations as alloc
// 				ON trans.trans_no = alloc.trans_no_to AND trans.type = alloc.trans_type_to AND alloc.person_id=trans.debtor_no,"
// 	 		.TB_PREF."debtors_master as debtor
// 	 WHERE
// 	 	 trans.debtor_no=debtor.debtor_no";
// 	if ($customer_id)
// 		$sql .= " AND trans.debtor_no=".db_escape($customer_id);


// 	if($barcode)
//         $sql .= " AND (trans.barcode=".db_escape($barcode)."OR trans.reference=".db_escape($barcode).") ";

//     if(!empty($dimension_id))
//         $sql .= " AND trans.dimension_id=$dimension_id ";


// 	$sql.= " AND trans.payment_flag <> 2 ";


// 	if($invoice_from_date) {
//         $invoice_from_date = date2sql($invoice_from_date);
//         $sql .= " AND trans.tran_date >= '$invoice_from_date'";
//     }

//     if($invoice_to_date) {
//         $invoice_to_date = date2sql($invoice_to_date);
//         $sql .= " And trans.tran_date <= '$invoice_to_date'";
//     }


// 	if ($trans_no != null and $type != null)
// 	{
// 		$sql .= " AND alloc.trans_no_from=".db_escape($trans_no)."
// 				  AND alloc.trans_type_from=".db_escape($type);
// 	}
// 	else
// 	{
// 		$sql .= "
// 				 AND (
// 					trans.type='".ST_SALESINVOICE."'
// 					AND round(IF(prep_amount, prep_amount, inv_total)-alloc,6) > 0
// 					OR
// 					trans.type='". ST_CUSTCREDIT."'
// 					AND round(-IF(prep_amount, prep_amount, ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc,6) > 0
// 					OR
// 				  	trans.type = '". ST_JOURNAL."'
// 					AND ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount>0
// 					OR
// 				  	trans.type = '". ST_BANKPAYMENT."'
// 					AND ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount>0
// 				)";
// 		$sql .= " GROUP BY type, trans_no";
// 	}

// 	$orders = get_allocatable_sales_orders($customer_id, $trans_no, $type);
// 	$sql = "($sql ORDER BY trans_no) \nUNION \n($orders)";


// //    display_error(print_r($sql ,true));


//     return db_query($sql, "Cannot retreive alloc to transactions");
// }
// after changing for voided transactions recovery
function get_allocatable_to_cust_transactions(
	$customer_id = null,
	$trans_no = null,
	$type = null,
    $barcode = null,
	$invoice_from_date = null,
	$invoice_to_date = null,
	$dimension_id = null,
	$voided = false
)
{
	if(isset($_SESSION['dim_id'])){
        $dimension_id = $_SESSION['dim_id'];
        unset($_SESSION['dim_id']);
    }

	if($voided){
	$sql = "SELECT
		trans.type,
		trans.trans_no,
		trans.reference,
		trans.tran_date,
		debtor.name AS DebtorName, 
		debtor.curr_code,
		
		IF(prep_amount, prep_amount, ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount) AS Total,
		
		trans.alloc,
		trans.due_date,
		debtor.address,
		trans.version,
		amt,
		trans.debtor_no,
		trans.branch_code, trans.payment_flag ,trans.barcode  
	 FROM ".TB_PREF."voided_debtor_trans as trans
			LEFT JOIN ".TB_PREF."voided_cust_allocations as alloc
				ON trans.trans_no = alloc.trans_no_to AND trans.type = alloc.trans_type_to AND alloc.person_id=trans.debtor_no,"
	 		.TB_PREF."debtors_master as debtor
	 WHERE
	 	 trans.debtor_no=debtor.debtor_no";	
	}else{

	$sql = "SELECT
		trans.type,
		trans.trans_no,
		trans.reference,
		trans.tran_date,
		debtor.name AS DebtorName, 
		debtor.curr_code,
		IF(prep_amount, prep_amount, ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount) AS Total,
		trans.alloc,
		trans.due_date,
		debtor.address,
		trans.version,
		amt,
		trans.debtor_no,
		trans.branch_code, trans.payment_flag,trans.barcode   
	 FROM ".TB_PREF."debtor_trans as trans
			LEFT JOIN ".TB_PREF."cust_allocations as alloc
				ON trans.trans_no = alloc.trans_no_to AND trans.type = alloc.trans_type_to AND alloc.person_id=trans.debtor_no,"
	 		.TB_PREF."debtors_master as debtor
	 WHERE
	 	 trans.debtor_no=debtor.debtor_no";
	}

	if ($customer_id)
		$sql .= " AND trans.debtor_no=".db_escape($customer_id);


	if($barcode)
        $sql .= " AND (trans.barcode=".db_escape($barcode)."OR trans.reference=".db_escape($barcode).") ";

    if(!empty($dimension_id))
        $sql .= " AND trans.dimension_id=$dimension_id ";

//	$sql.= " AND trans.payment_flag <> 2 ";


	if($invoice_from_date) {
        $invoice_from_date = date2sql($invoice_from_date);
        $sql .= " AND trans.tran_date >= '$invoice_from_date'";
    }

    if($invoice_to_date) {
        $invoice_to_date = date2sql($invoice_to_date);
        $sql .= " And trans.tran_date <= '$invoice_to_date'";
    }


	if ($trans_no != null and $type != null)
	{
		$sql .= " AND alloc.trans_no_from=".db_escape($trans_no)."
				  AND alloc.trans_type_from=".db_escape($type);
	}
	else
	{
		$sql .= "
				 AND (
					trans.type='".ST_SALESINVOICE."'
					AND round(IF(prep_amount, prep_amount, ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc,6) > 0
					OR
					trans.type='". ST_CUSTCREDIT."'
					AND round(-IF(prep_amount, prep_amount, ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc,6) > 0
					OR
				  	trans.type = '". ST_JOURNAL."'
					AND ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount>0
					OR
				  	trans.type = '". ST_BANKPAYMENT."'
					AND ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount>0
				)";
		$sql .= " GROUP BY type, trans_no";
	}

	$orders = get_allocatable_sales_orders($customer_id, $trans_no, $type,$voided);
	$sql = "($sql ORDER BY trans_no) \nUNION \n($orders)";


   // display_error(print_r($sql ,true));


    return db_query($sql, "Cannot retreive alloc to transactions");
}

//-------------------------------------------------------------------------------------------------------------

function get_allocatable_from_cust_transactions($customer_id, $trans_no=null, $type=null,$voided=false)
{
	if($voided){
	$sql = "SELECT
		trans.type,
		trans.trans_no,
		trans.reference,
		trans.tran_date,
		debtor.name AS DebtorName, 
		debtor.curr_code,
		ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount AS Total,
		trans.alloc,
		trans.due_date,
		debtor.address,
		trans.version,
		amt,
		trans.debtor_no,
		trans.payment_method,
		trans.round_of_amount,
		trans.credit_card_charge  
	 FROM  ".TB_PREF."voided_debtor_trans as trans,"
			.TB_PREF."debtors_master as debtor,"
			.TB_PREF."voided_cust_allocations as alloc
	 WHERE trans.debtor_no=debtor.debtor_no
			AND trans.trans_no = alloc.trans_no_from
			AND trans.type = alloc.trans_type_from
			AND trans.debtor_no = alloc.person_id";
	}else{
	$sql = "SELECT
		trans.type,
		trans.trans_no,
		trans.reference,
		trans.tran_date,
		debtor.name AS DebtorName, 
		debtor.curr_code,
		ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount AS Total,
		trans.alloc,
		trans.due_date,
		debtor.address,
		trans.version,
		amt,
		trans.debtor_no,
		trans.payment_method,
		trans.round_of_amount,
		trans.credit_card_charge    
	 FROM  ".TB_PREF."debtor_trans as trans,"
			.TB_PREF."debtors_master as debtor,"
			.TB_PREF."cust_allocations as alloc
	 WHERE trans.debtor_no=debtor.debtor_no
			AND trans.trans_no = alloc.trans_no_from
			AND trans.type = alloc.trans_type_from
			AND trans.debtor_no = alloc.person_id";
		
	}


	if ($trans_no != null and $type != null)
	{
		$sql .=	" AND alloc.trans_no_to=".db_escape($trans_no)."
		  		  AND alloc.trans_type_to=".db_escape($type);
	}
	else
	{
		$sql .= " AND round(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount-alloc,6) > 0
			AND trans.type NOT IN (".implode(',',array(ST_CUSTPAYMENT,ST_BANKDEPOSIT,ST_CUSTCREDIT,ST_CUSTDELIVERY)).")";
		$sql .= " GROUP BY type, trans_no";
	}

	if($customer_id)
		$sql .= " AND trans.debtor_no=".db_escape($customer_id);

	// display_error($sql);

	return db_query($sql." ORDER BY trans_no", "Cannot retreive alloc to transactions");
}

function get_sql_for_customer_allocation_inquiry($from, $to, $customer, $filterType, $settled,$dimension_id=null)
{
  	$data_after = date2sql($from);
  	$date_to = date2sql($to);

  	$sql = "SELECT 
  		trans.type,
		trans.trans_no,
		trans.reference,
		trans.order_,
		trans.tran_date,
		trans.due_date,
		debtor.name,
		debtor.curr_code,
    	
			
        (trans.ov_amount + trans.ov_gst + trans.ov_freight 
        + trans.ov_freight_tax + trans.ov_discount+trans.credit_card_charge) AS TotalAmount,
			
			
		trans.alloc AS Allocated,
		((trans.type = ".ST_SALESINVOICE.")
			AND trans.due_date < '" . date2sql(Today()) . "') AS OverDue,
		trans.debtor_no,
		
		
		(SELECT GROUP_CONCAT(0_debtor_trans.reference SEPARATOR ', ') from 0_cust_allocations  
left join 0_debtor_trans on 0_debtor_trans.trans_no = 0_cust_allocations.trans_no_to 
where trans_no_from = trans.trans_no and 0_debtor_trans.type=10
 ) as invoice_numbers  
		
		
    	FROM "
			.TB_PREF."debtor_trans as trans, "
			.TB_PREF."debtors_master as debtor
    	WHERE debtor.debtor_no = trans.debtor_no
			AND (trans.ov_amount + trans.ov_gst + trans.ov_freight 
				+ trans.ov_freight_tax + trans.ov_discount != 0)
    		AND trans.tran_date >= '$data_after'
    		AND trans.tran_date <= '$date_to'";

   	if ($customer != ALL_TEXT)
   		$sql .= " AND trans.debtor_no = ".db_escape($customer);

   	if (isset($filterType) && $filterType != ALL_TEXT)
   	{
   		if ($filterType == '1' || $filterType == '2')
   		{
   			$sql .= " AND trans.type = ".ST_SALESINVOICE." ";
   		}
   		elseif ($filterType == '3')
   		{
			$sql .= " AND trans.type = " . ST_CUSTPAYMENT;
   		}
   		elseif ($filterType == '4')
   		{
			$sql .= " AND trans.type = ".ST_CUSTCREDIT." ";
   		}

    	if ($filterType == '2')
    	{
    		$today =  date2sql(Today());
    		$sql .= " AND trans.due_date < '$today'
				AND (round(abs(trans.ov_amount + "
				."trans.ov_gst + trans.ov_freight + "
				."trans.ov_freight_tax + trans.ov_discount) - trans.alloc,6) > 0) ";
    	}
   	}
   	else
   	{
	    $sql .= " AND trans.type <> ".ST_CUSTDELIVERY." ";
   	}


//   	pp($dimension_id);

    if(!empty($dimension_id))
        $sql .= " AND trans.dimension_id=$dimension_id ";


   	if (!$settled)
   	{
   		$sql .= " AND (round(IF(trans.prep_amount,trans.prep_amount, abs(trans.ov_amount + trans.ov_gst + "
		."trans.ov_freight + trans.ov_freight_tax + "
		."trans.ov_discount)) - trans.alloc,6) != 0) ";
   	}
   	return $sql;
}

function credit_sales_invoice_allocate($invoice_no, $credit_no, $amount, $date)
{

	$sql = "SELECT ov_freight+ov_gst+ov_amount+ov_freight_tax as total, alloc, debtor_no FROM ".TB_PREF."debtor_trans
		WHERE (`type`=".ST_SALESINVOICE." AND trans_no=".db_escape($invoice_no).")";
	$result = db_query($sql, "can't retrieve invoice totals");
	$invoice = db_fetch($result);
	$free = $invoice['total'] - $invoice['alloc'];

	if ($free < $amount) {
	 // if there is not enough unallocated amount - remove some payment allocations
		$sql = "SELECT * FROM ".TB_PREF."cust_allocations
			WHERE (trans_type_to=".ST_SALESINVOICE." AND trans_no_to=".db_escape($invoice_no)." AND person_id=".db_escape($invoice['debtor_no']).")
			AND trans_type_from <> ".ST_CUSTCREDIT;
		$result = db_query($sql, "can't retrieve invoice allocations");

		while(($free < $amount) && ($alloc = db_fetch($result))) {
			$unalloc = min($alloc['amt'], $amount-$free);

			delete_cust_allocation($alloc['id']);
			if ($unalloc < $alloc['amt'])
				add_cust_allocation($alloc['amt']-$unalloc, $alloc['trans_type_from'],
					$alloc['trans_no_from'], ST_SALESINVOICE, $invoice_no, $invoice['debtor_no'], $date);

			update_debtor_trans_allocation($alloc['trans_type_to'], $alloc['trans_no_to'], $invoice['debtor_no']);
			update_debtor_trans_allocation($alloc['trans_type_from'], $alloc['trans_no_from'], $invoice['debtor_no']);
			$free += $unalloc;
		}
	}
	if (floatcmp($free, $amount)<0) {
		// this should never happen unless sparse credit notices were allocated to 
		// the invoice, or summarized freight costs on credit notes is more than those on invoice.
		display_error(trans("Unsuspected overallocation happened due to sparse credit notes exists for this invoice.
 Check all credit notes allocated to this invoice for summarized freight charges."));
		return false;
	}
	add_cust_allocation($amount, ST_CUSTCREDIT, $credit_no, ST_SALESINVOICE, $invoice_no, $invoice['debtor_no'], $date);
	update_debtor_trans_allocation(ST_SALESINVOICE, $invoice_no, $invoice['debtor_no']);
	update_debtor_trans_allocation(ST_CUSTCREDIT, $credit_no, $invoice['debtor_no']);

	exchange_variation(ST_CUSTCREDIT, $credit_no, ST_SALESINVOICE, $invoice_no, $date,
		$amount, PT_CUSTOMER);
	return true;
}

/**
 * Retrieves all the customer's transactions which are qualified allocatee
 * 
 * @param int 		$customer_id 	Optional customer filter
 * 
 * @return mysqli_result
 */
function get_customer_allocatee($customer_id = null, $for_update = false){
	$sql = get_sql_for_customer_allocatee($customer_id);
	$sql .= " GROUP BY `type`, trans_no ORDER BY trans.created_at";

	$orders = get_sql_for_customer_allocatee_from_sales_orders($customer_id);
	$orders .= "  GROUP BY sorder.order_no";

	if($for_update){
		$sql .= " FOR UPDATE";
		$orders .= " FOR UPDATE";
	}

	return db_query("($sql) \nUNION \n($orders)");
}

/**
 * Retrieves all the customer's transactions which are qualified allocatee along with their allocations without grouping
 * 
 * @param int 		$customer_id 	Optional customer filter
 * 
 * @return mysqli_result
 */
function get_customer_allocatees_without_grouping($customer_id = null, $for_update = false){
	$sql = get_sql_for_customer_allocatee($customer_id);
	$orders = get_sql_for_customer_allocatee_from_sales_orders($customer_id);

	if($for_update){
		$sql .= " FOR UPDATE";
		$orders .= " FOR UPDATE";
	}

	return db_query("($sql) \nUNION \n($orders)");
}

/**
 * Returns the main sql for finding qualified allocatee from debtor transactions
 * 
 * @param int 		$customer_id 	Optional customer filter
 * 
 * @return string
 */
function get_sql_for_customer_allocatee($customer_id = null){

	$where = '';
    if(!empty($customer_id)){
        $where .= " AND trans.debtor_no = "  . db_escape($customer_id);
    }

	$sql = (
		"SELECT 
            trans.`type`,
            trans.trans_no,
            trans.reference,
            trans.tran_date,
            debtor.name AS DebtorName,
            debtor.curr_code, 
            IF(prep_amount, prep_amount, ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount) AS Total,
            trans.alloc,
            trans.due_date, 
            debtor.address,
            trans.version,
            amt,
            trans.debtor_no,
            trans.branch_code,
            trans.payment_flag,
			alloc.id AS alloc_id,
			IFNULL(alloc.trans_no_from, 'N/A') AS trans_no_from,
			IFNULL(alloc.trans_type_from, 'N/A') AS trans_type_from
        FROM 0_debtor_trans AS trans
        LEFT JOIN 0_cust_allocations AS alloc ON
            trans.trans_no = alloc.trans_no_to
            AND trans.`type` = alloc.trans_type_to 
            AND alloc.person_id = trans.debtor_no
        INNER JOIN 0_debtors_master AS debtor ON trans.debtor_no=debtor.debtor_no
        WHERE 
            trans.payment_flag <> 2 
            AND (
                (trans.type = 10 AND ROUND(IF(prep_amount, prep_amount, ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc,6) > 1e-6)
                OR (trans.type = 10 AND ROUND(-IF(prep_amount, prep_amount, ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc,6) > 1e-6) 
                OR (trans.type IN (0,1) AND ROUND(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount - alloc, 6) > 1e-6)
            ) {$where}"
	);

	return $sql;
}

/**
 * Returns the main sql for finding qualified allocatee from sales orders
 * 
 * @param int 		$customer_id 	Optional customer filter
 * 
 * @return string
 */
function get_sql_for_customer_allocatee_from_sales_orders($customer_id = null){

	$where = '';
	if(!empty($customer_id)){
		$where .= "  AND sorder.debtor_no=" . db_escape($customer_id);
	}

	$sql = (
		"SELECT
			sorder.trans_type as `type`,
			sorder.order_no as trans_no,
			sorder.reference,
			sorder.ord_date as tran_date,
			debtor.name AS DebtorName, 
			debtor.curr_code,
			total-IFNULL(invoiced.amount,0) as Total,
			sorder.alloc,
			sorder.delivery_date as due_date,
			debtor.address,
			sorder.version,
			amt,
			sorder.debtor_no,
			sorder.branch_code,
			NULL AS payment_flag,
			alloc.id AS alloc_id,
			IFNULL(alloc.trans_no_from, 'N/A') AS trans_no_from,
			IFNULL(alloc.trans_type_from, 'N/A') AS trans_type_from
		FROM `0_sales_orders` as sorder
			LEFT JOIN `0_debtors_master` as debtor ON sorder.debtor_no = debtor.debtor_no
			LEFT JOIN `0_cust_allocations` as alloc 
				ON sorder.order_no = alloc.trans_no_to
				AND sorder.trans_type = alloc.trans_type_to
				AND alloc.person_id=sorder.debtor_no
			LEFT JOIN 
				(
					SELECT 
						order_,
						sum(prep_amount) amount
					FROM `0_debtor_trans` dt
					WHERE
						prep_amount > 0
						AND dt.`type` = 10
					GROUP BY order_
				) AS invoiced ON sorder.order_no = invoiced.order_
		WHERE sorder.trans_type = 30
		AND round(sorder.prep_amount) > 0 and Total > 0
		{$where}"
	);

	return $sql;
}