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

function add_customer(
	$CustName,
	$cust_ref,
	$address,
	$tax_id,
	$curr_code,
	$dimension_id,
	$dimension2_id,
	$credit_status,
	$payment_terms,
	$discount,
	$pymt_discount, 
	$credit_limit,
	$sales_type,
	$notes,
	$mobile,
	$email,
	$cr_notice_level,
	$cr_warning_level,
	$contact_person = null,
	$iban_no = null
) {

	if(!user_check_access('SA_UPDATECRLMT')){
		$credit_limit = $GLOBALS['SysPrefs']->default_credit_limit();
		$cr_notice_level = $GLOBALS['SysPrefs']->prefs['dflt_cr_lmt_notice_lvl'];
		$cr_warning_level = $GLOBALS['SysPrefs']->prefs['dflt_cr_lmt_warning_lvl'];
	}

    $show_discount = isset($_POST['show_discount']) ? $_POST['show_discount'] : 0;
    $always_use_customer_card = empty($_POST['always_use_customer_card']) ? 0 : 1;

	$sql = (
		"INSERT INTO ".TB_PREF."debtors_master 
			(
				name,
				debtor_ref,
				address,
				tax_id,
				dimension_id,
				dimension2_id,
				curr_code,
				credit_status,
				payment_terms,
				discount, 
				pymt_discount,
				credit_limit,
				sales_type,
				notes,
				mobile,
				debtor_email,
				show_discount,
				always_use_customer_card,
				contact_person,
				iban_no,
				cr_lmt_notice_lvl,
				cr_lmt_warning_lvl
			) 
		VALUES 
			(
				" 	   . db_escape($CustName) 
				. ", " . db_escape($cust_ref) 
				. ", " . db_escape($address) 
				. ", " . db_escape($tax_id) 
				. ", " . db_escape($dimension_id) 
				. ", " . db_escape($dimension2_id) 
				. ", " . db_escape($curr_code) 
				. ", " . db_escape($credit_status) 
				. ", " . db_escape($payment_terms) 
				. ", " . $discount 
				. ", " . $pymt_discount 
				. ", " . $credit_limit 
				. ", " . db_escape($sales_type)
				. ", " . db_escape($notes) 
				. ", " . db_escape($mobile)
				. ", " . db_escape($email)
				. ", " . $show_discount
				. ", " . $always_use_customer_card
				. ", " . db_escape($contact_person) 
				. ", " . db_escape($iban_no)
				. ", " . $cr_notice_level
				. ", " . $cr_warning_level .
			")"
	);
	db_query($sql,"The customer could not be added");
}

function update_customer(
	$customer_id,
	$CustName,
	$cust_ref,
	$address,
	$tax_id,
	$curr_code,
	$dimension_id,
	$dimension2_id,
	$credit_status,
	$payment_terms,
	$discount,
	$pymt_discount,
	$credit_limit,
	$sales_type,
	$notes,
	$activated_till=null,
	$mobile=null,
	$email,
	$cr_notice_level,
	$cr_warning_level,
	$contact_person = null,
	$iban_no = null
){
    $show_discount = $_POST['show_discount'];
    $always_use_customer_card = empty($_POST['always_use_customer_card']) ? 0 : 1;

	$updates = [
		'name' => db_escape($CustName), 
		'debtor_ref' => db_escape($cust_ref),
		'address' => db_escape($address), 
		'tax_id' => db_escape($tax_id), 
		'curr_code' => db_escape($curr_code), 
		'dimension_id' => db_escape($dimension_id), 
		'dimension2_id' => db_escape($dimension2_id), 
		'credit_status' => db_escape($credit_status), 
		'payment_terms' => db_escape($payment_terms), 
		'discount' => $discount, 
		'pymt_discount' => $pymt_discount,
		'sales_type' => db_escape($sales_type), 
		'notes' => db_escape($notes),
		'activated_till' => db_escape($activated_till),
		'mobile' => db_escape($mobile),
		'show_discount' => $show_discount,
		'always_use_customer_card' => $always_use_customer_card,
		'debtor_email' => db_escape($email), 
		'contact_person' => db_escape($contact_person), 
		'iban_no' => db_escape($iban_no)
	];

	if(user_check_access('SA_UPDATECRLMT')){
		$updates['credit_limit'] = $credit_limit;
		$updates['cr_lmt_notice_lvl'] = $cr_notice_level;
		$updates['cr_lmt_warning_lvl'] = $cr_warning_level;
	}

	$_updates = '';
	foreach($updates as $col => $value){
		$_updates .= "{$col} = $value, ";
	}
	$_updates = rtrim($_updates, " ,");

	$sql = "UPDATE ".TB_PREF."debtors_master SET {$_updates} WHERE debtor_no = ".db_escape($customer_id);

	db_query($sql,"The customer could not be updated");
}

function delete_customer($customer_id)
{
	begin_transaction();

	delete_customer_branches($customer_id);

	delete_entity_contacts('customer', $customer_id);

	$sql = "DELETE FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($customer_id);;
	db_query($sql,"cannot delete customer");
	commit_transaction();
}


function delete_customer_branches($customer_id) {
    $sql = "DELETE FROM ".TB_PREF."cust_branch WHERE debtor_no=".db_escape($customer_id);;
    db_query($sql,"cannot delete branches");
}

/*
	This function probably should be renamed to get_customer_summary
*/
function get_customer_details($customer_id, $to=null, $all=true)
{

	if ($to == null)
		$todate = date("Y-m-d");
	else
		$todate = date2sql($to);
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
	// removed - debtor_trans.alloc from all summations
	$sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT))."), -1, 1)";
	if ($all)
    	$value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount),0)";
    else		
    	$value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - 
    		trans.alloc),0)";
	$due = "IF (trans.type=".ST_SALESINVOICE.", trans.due_date, trans.tran_date)";
    $sql = "SELECT debtor.name, debtor.curr_code, terms.terms, debtor.credit_limit,
    			credit_status.dissallow_invoices, credit_status.reason_description,
				Sum($value) AS Balance,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$value,0)) AS Due,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past1,$value,0)) AS Overdue1,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past2,$value,0)) AS Overdue2
			FROM ".TB_PREF."debtors_master debtor
				 LEFT JOIN ".TB_PREF."debtor_trans trans ON trans.tran_date <= '$todate' AND debtor.debtor_no = trans.debtor_no AND trans.type <> ".ST_CUSTDELIVERY.","
				 .TB_PREF."payment_terms terms,"
				 .TB_PREF."credit_status credit_status
			WHERE
					debtor.payment_terms = terms.terms_indicator
	 			AND debtor.credit_status = credit_status.id";
	 if ($customer_id)
		$sql .= " AND debtor.debtor_no = ".db_escape($customer_id);

	if (!$all)
		$sql .= " AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - trans.alloc) > ".FLOAT_COMP_DELTA;
	$sql .= " GROUP BY
			  	debtor.name,
			  	terms.terms,
			  	terms.days_before_due,
			  	terms.day_in_following_month,
			  	debtor.credit_limit,
			  	credit_status.dissallow_invoices,
			  	credit_status.reason_description";
    $result = db_query($sql,"The customer details could not be retrieved");

//    dd($sql);

    $customer_record = db_fetch($result);

    return $customer_record;

}


function get_customer($customer_id)
{
	$sql = "SELECT * FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($customer_id);

	$result = db_query($sql, "could not get customer");

	return db_fetch($result);
}

function get_customer_name($customer_id)
{
	$sql = "SELECT name FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($customer_id);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_customer_habit($customer_id)
{
	$sql = "SELECT debtor.pymt_discount, credit_status.dissallow_invoices
			FROM ".TB_PREF."debtors_master debtor,"
				.TB_PREF."credit_status credit_status
			WHERE debtor.credit_status = credit_status.id
			AND debtor.debtor_no = ".db_escape($customer_id);

	$result = db_query($sql, "could not query customers");

	return db_fetch($result);
}

function get_customer_contacts($customer_id, $action=null)
{
	$results = array();
	$res = get_crm_persons('customer', $action, $customer_id);
	while($contact = db_fetch($res))
	{
		if ($contact['lang'] == 'C') // Fix for improper lang in demo sql files.
			$contact['lang'] = '';
		$results[] = $contact;
	}	
	return $results;
}

function get_current_cust_credit($customer_id)
{
	$custdet = get_customer_details($customer_id);

	return $custdet['credit_limit']-$custdet['Balance'];

}

function is_new_customer($id)
{
	$tables = array('cust_branch', 'debtor_trans', 'recurrent_invoices', 'sales_orders');

	return !key_in_foreign_table($id, $tables, 'debtor_no');
}

function get_customer_by_ref($reference)
{
	$sql = "SELECT * FROM ".TB_PREF."debtors_master WHERE debtor_ref=".db_escape($reference);

	$result = db_query($sql, "could not get customer");

	return db_fetch($result);
}

//----------------------------------------------------------------------------------

function get_customer_currency($customer_id=null, $branch_id=null)
{
    $sql = "SELECT curr_code
    	FROM ".TB_PREF."debtors_master cust
    		LEFT JOIN ".TB_PREF."cust_branch branch ON branch.debtor_no=cust.debtor_no
    	WHERE " .(isset($branch_id) ? "branch_code = ".db_escape($branch_id) : "cust.debtor_no = ".db_escape($customer_id));

	$result = db_query($sql, "Retreive currency of customer $customer_id");

	$myrow=db_fetch_row($result);
	return $myrow[0];
}

function get_customers_search($customer)
{
	global $SysPrefs;

	if (isset($SysPrefs->max_rows_in_search))
		$limit = $SysPrefs->max_rows_in_search;
	else
		$limit = 10;

    $sql = "SELECT debtor_no, name, debtor_ref, address, tax_id FROM ".TB_PREF."debtors_master 
	  WHERE (  name LIKE " . db_escape("%" . $customer. "%") . " OR 
    		 debtor_ref LIKE " . db_escape("%" . $customer. "%") . " OR 
	        address LIKE " . db_escape("%" . $customer. "%") . " OR 
    	     tax_id LIKE " . db_escape("%" . $customer. "%")." OR
    	     mobile LIKE " . db_escape("%" . $customer. "%").")
	  ORDER BY name LIMIT 0,".(int)($limit);
    
	return db_query($sql, "Failed in retreiving customer list.");
}

function get_sql_for_customers_balance_inquiry($customer_id = null, $from = null, $to = null)
{
	if(empty($from)) {
		$from = get_current_fiscalyear()['begin'];
	}

	if(empty($to)){
		$to = date(MYSQL_DATE_FORMAT);
	}

    $where = '';
    if (!empty($customer_id)) {
        $where .= "AND trans.debtor_no = " . db_escape($customer_id);
    }

    $excluded_customers = $GLOBALS['SysPrefs']->prefs['excluded_customers'];
    if(!empty($excluded_customers)) {
        $where .= "AND trans.debtor_no NOT IN ($excluded_customers)";
    }

    $totalAmount = "trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount";
    $mainSql = (
        "SELECT 
            debtor.name,
            debtor.debtor_no, 
            trans.type,	
            ({$totalAmount}) AS TotalAmount,	
            trans.alloc AS Allocated
        FROM 0_debtor_trans AS trans
            LEFT JOIN 0_debtors_master AS debtor ON debtor.debtor_no = trans.debtor_no
        WHERE ({$totalAmount} != 0)
            AND trans.tran_date >= '{$from}'
            AND trans.tran_date <= '{$to}' 
            AND trans.type <> 13  
            AND round(IF(trans.prep_amount, trans.prep_amount, abs({$totalAmount})) - trans.alloc, 4) != 0
            {$where}"
    );


    $floatCompDelta = FLOAT_COMP_DELTA;
    $balance   = "IF(t1.type = 0 AND t1.TotalAmount < 0, 0 - t1.TotalAmount, t1.TotalAmount - t1.Allocated)";
    $isPrepaid = (
        "(
            (t1.type = 11 AND t1.TotalAmount > 0)
            OR (t1.type = 0 AND t1.TotalAmount < 0)
            OR (t1.type IN (12, 2) AND (NOT (t1.Allocated - t1.TotalAmount > {$floatCompDelta})))
        )"
    );
    $isNegativeReciepts = "t1.type = 12 AND t1.TotalAmount <= 0";
    // $isOutstanding = (
    //     "(
    //         (t1.type = 10 AND t1.TotalAmount - t1.Allocated > 0)
    //         OR t1.type = 1
    //     )"
    // );
    $sql_1 = (
        "SELECT 
            t1.debtor_no,
            t1.name,
            CASE
                WHEN {$isPrepaid} THEN {$balance}
                ELSE 0.00
            END prepaid,
            IF(
                (NOT ({$isPrepaid})) AND (NOT ({$isNegativeReciepts})),
                {$balance},
                0.00
            ) pending
        FROM ({$mainSql}) AS t1"
    );

    $sql_2 = (
        "SELECT 
			t2.debtor_no,
            t2.name,
            SUM(t2.prepaid) AS prepaid,
            SUM(t2.pending) AS pending
        FROM ({$sql_1}) AS t2
        GROUP BY t2.debtor_no"
    );

    $sql_3 = (
        "SELECT 
			t3.debtor_no,
            t3.name,
            t3.prepaid,
            t3.pending,
            IF(t3.pending - t3.prepaid < 0, t3.pending - t3.prepaid, '0.00') AS advance,
            IF(t3.pending - t3.prepaid > 0, t3.pending - t3.prepaid, '0.00') AS due
        FROM ({$sql_2}) AS t3 ORDER BY t3.pending - t3.prepaid DESC"
    );

    return $sql_3;
}

function get_sql_for_opening_balance_of_customer_balance_inquiry($customer_id = null, $till = null){

	$from = get_current_fiscalyear()['begin'];

	if(empty($till)){
		$till = date(MYSQL_DATE_FORMAT);
	}

    $where = '';
    if (!empty($customer_id)) {
        $where .= "AND trans.debtor_no = " . db_escape($customer_id);
    }

    $excluded_customers = $GLOBALS['SysPrefs']->prefs['excluded_customers'];
    if(!empty($excluded_customers)) {
        $where .= "AND trans.debtor_no NOT IN ($excluded_customers)";
    }

    $totalAmount = "trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount";
    $mainSql = (
        "SELECT 
            trans.debtor_no, 
			debtor.name,
            trans.type,	
            ({$totalAmount}) AS TotalAmount,	
            trans.alloc AS Allocated
        FROM 0_debtor_trans AS trans
			LEFT JOIN 0_debtors_master AS debtor ON debtor.debtor_no = trans.debtor_no
        WHERE ({$totalAmount} != 0)
            AND trans.tran_date >= '{$from}'
            AND trans.tran_date < '{$till}' 
            AND trans.type <> 13  
            AND round(IF(trans.prep_amount, trans.prep_amount, abs({$totalAmount})) - trans.alloc, 4) != 0
            {$where}"
    );


    $floatCompDelta = FLOAT_COMP_DELTA;
    $balance   = "IF(t1.type = 0 AND t1.TotalAmount < 0, 0 - t1.TotalAmount, t1.TotalAmount - t1.Allocated)";
    $isPrepaid = (
        "(
            (t1.type = 11 AND t1.TotalAmount > 0)
            OR (t1.type = 0 AND t1.TotalAmount < 0)
            OR (t1.type IN (12, 2) AND (NOT (t1.Allocated - t1.TotalAmount > {$floatCompDelta})))
        )"
    );
    $isNegativeReciepts = "t1.type = 12 AND t1.TotalAmount <= 0";
    // $isOutstanding = (
    //     "(
    //         (t1.type = 10 AND t1.TotalAmount - t1.Allocated > 0)
    //         OR t1.type = 1
    //     )"
    // );
    $sql_1 = (
        "SELECT 
            t1.debtor_no,
			t1.name,
            CASE
                WHEN {$isPrepaid} THEN {$balance}
                ELSE 0.00
            END prepaid,
            IF(
                (NOT ({$isPrepaid})) AND (NOT ({$isNegativeReciepts})),
                {$balance},
                0.00
            ) pending
        FROM ({$mainSql}) AS t1"
    );

    $sql_2 = (
        "SELECT 
			t2.debtor_no,
			t2.name,
			SUM(t2.pending) - SUM(t2.prepaid) AS opening_bal
        FROM ({$sql_1}) AS t2
        GROUP BY t2.debtor_no"
    );

    return $sql_2;
}