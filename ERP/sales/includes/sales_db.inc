<?php
/**********************************************************************
 * Direct Axis Technology L.L.C.
 * Released under the terms of the GNU General Public License, GPL,
 * as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 ***********************************************************************/
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/inventory.inc");
include_once($path_to_root . "/includes/db/allocations_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_order_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_credit_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_invoice_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_delivery_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_types_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_points_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_groups_db.inc");
include_once($path_to_root . "/sales/includes/db/recurrent_invoices_db.inc");
include_once($path_to_root . "/sales/includes/db/custalloc_db.inc");
include_once($path_to_root . "/sales/includes/db/cust_trans_db.inc");
include_once($path_to_root . "/sales/includes/db/cust_trans_details_db.inc");
include_once($path_to_root . "/sales/includes/db/payment_db.inc");
include_once($path_to_root . "/sales/includes/db/branches_db.inc");
include_once($path_to_root . "/sales/includes/db/customers_db.inc");

//----------------------------------------------------------------------------------------
// add a debtor-related gl transaction
// $date_ is display date (non-sql)
// $amount is in CUSTOMER'S currency

function add_gl_trans_customer($type, $type_no, $date_, $account, $dimension, $dimension2,
                               $amount, $customer_id, $err_msg = "", $rate = 0, $memo = "", $transaction_id = "")
{
    if ($err_msg == "")
        $err_msg = "The customer GL transaction could not be inserted";

    return add_gl_trans($type, $type_no, $date_, $account, $dimension, $dimension2, $memo, $amount,
        get_customer_currency($customer_id),
        PT_CUSTOMER, $customer_id, $err_msg, $rate, $transaction_id);
}

//----------------------------------------------------------------------------------------

function get_calculated_price($stock_id, $add_pct)
{
    $avg = get_unit_cost($stock_id);
    if ($avg == 0)
        return 0;
    return round2($avg * (1 + $add_pct / 100), user_price_dec());
}

function round_to_nearest($price, $round_to)
{
    if ($price == 0)
        return 0;
    $pow = pow(10, user_price_dec());
    if ($pow >= $round_to)
        $mod = ($pow % $round_to);
    else
        $mod = ($round_to % $pow);
    if ($mod != 0)
        $price = ceil($price) - ($pow - $round_to) / $pow;
    else
        $price = ceil($price * ($pow / $round_to)) / ($pow / $round_to);
    return $price;

}

function get_price($stock_id, $currency, $sales_type_id, $factor = null, $date = null)
{

    if ($date == null)
        $date = new_doc_date();

    if ($factor === null) {
        $myrow = get_sales_type($sales_type_id);
        $factor = $myrow['factor'];
    }

    $add_pct = get_company_pref('add_pct');
    $base_id = get_base_sales_type();
    $home_curr = get_company_currency();
    //	AND (sales_type_id = $sales_type_id	OR sales_type_id = $base_id)
    $sql = "SELECT price, curr_abrev, sales_type_id,c.pf_amount 
		FROM " . TB_PREF . "prices left join " . TB_PREF . "stock_master c on c.stock_id=" . TB_PREF . "prices.stock_id  
		WHERE " . TB_PREF . "prices.stock_id = " . db_escape($stock_id) . "
			AND (curr_abrev = " . db_escape($currency) . " OR curr_abrev = " . db_escape($home_curr) . ")";


    $result = db_query($sql, "There was a problem retrieving the pricing information for the part $stock_id for customer");
    $num_rows = db_num_rows($result);
    $rate = round2(get_exchange_rate_from_home_currency($currency, $date),
        user_exrate_dec());
    $round_to = get_company_pref('round_to');
    $prices = array();

    $pf_amount = 0;
    while ($myrow = db_fetch($result)) {
        $pf_amount = $myrow['pf_amount'];
        $prices[$myrow['sales_type_id']][$myrow['curr_abrev']] = $myrow['price'];
    }
    $price = false;
    if (isset($prices[$sales_type_id][$currency])) {
        $price = $prices[$sales_type_id][$currency];
    } elseif (isset($prices[$base_id][$currency])) {
        $price = $prices[$base_id][$currency] * $factor;
    } elseif (isset($prices[$sales_type_id][$home_curr])) {
        $price = $prices[$sales_type_id][$home_curr] / $rate;
    } elseif (isset($prices[$base_id][$home_curr])) {
        $price = $prices[$base_id][$home_curr] * $factor / $rate;
    } elseif ($num_rows == 0 && $add_pct != -1) {
        $price = get_calculated_price($stock_id, $add_pct);
        if ($currency != $home_curr)
            $price /= $rate;
        if ($factor != 0)
            $price *= $factor;
    }

    $price = $price + $pf_amount;

    if ($price === false)
        return 0;
    elseif ($round_to != 1)
        return round_to_nearest($price, $round_to);
    else
        return round2($price, user_price_dec());
}


function get_sales_type_price($stock_id, $sales_type_id)
{


    if (!isset($sales_type_id) || empty($sales_type_id)) {
        $sales_type_id = 1;
    }


    $sql = "select * from 0_prices where stock_id='$stock_id' and sales_type_id=$sales_type_id";


    $result = db_query($sql, "Error retrieving price info by sales type from 150");
    return db_fetch($result);
}


function get_other_fee_total($stock_id, $sales_type_id)
{

//    $sales_type_id = 1;

    $sql = "select sum(amount) as other_fee_total from other_charges_master where stock_id='$stock_id' and sales_type_id=$sales_type_id";
    $result = db_query($sql, "Error retrieving price info by sales type 160 ");
    $result = db_fetch($result);
    return $result['other_fee_total'];
}

function get_other_fee_info($stock_id, $sales_type_id)
{
    $sql = "select a.account_code,a.amount,a.description,b.account_name from other_charges_master a left join 0_chart_master b on 
a.account_code = b.account_code 
where a.stock_id='$stock_id' and a.sales_type_id=$sales_type_id";
    $result = db_query($sql, "Error retrieving price info by sales type 169");

    $res = [];

    while ($myrow = db_fetch_assoc($result)) {
        array_push($res, $myrow);
    }

    return $res;
}


//----------------------------------------------------------------------------------------
//
//	Get price for given item or kit.
//  When $std==true price is calculated as a sum of all included stock items,
//	otherwise all prices set for kits and items are accepted.
//
function get_kit_price($item_code, $currency, $sales_type_id, $factor = null,
                       $date = null, $std = false)
{

    $kit_price = 0.00;
    if (!$std) {
        $kit_price = get_price($item_code, $currency, $sales_type_id,
            $factor, $date);

        if ($kit_price !== 0) {

            return $kit_price;
        }
    }
    // no price for kit found, get total value of all items
    $kit = get_item_kit($item_code);

    while ($item = db_fetch($kit)) {
        if ($item['item_code'] != $item['stock_id']) {
            // foreign/kit code
            $kit_price += $item['quantity'] * get_kit_price($item['stock_id'],
                    $currency, $sales_type_id, $factor, $date, $std);

        } else {
            // stock item
            $kit_price += $item['quantity'] * get_price($item['stock_id'],
                    $currency, $sales_type_id, $factor, $date);
        }
    }

    return $kit_price;
}

//--------------------------------------------------------------------------------------------------
function update_parent_line($doc_type, $line_id, $qty_dispatched, $auto = false)
{
    $doc_type = get_parent_type($doc_type);

    $qty_dispatched = (float)$qty_dispatched;

    if ($doc_type == 0)
        return false;
    else {
        if ($doc_type == ST_SALESORDER || $doc_type == ST_SALESQUOTE) {
            $sql = "UPDATE " . TB_PREF . "sales_order_details
				SET qty_sent = qty_sent + $qty_dispatched";
            if ($auto)
                $sql .= ", quantity = quantity + $qty_dispatched";
            $sql .= " WHERE id=" . db_escape($line_id);
        } else
            $sql = "UPDATE " . TB_PREF . "debtor_trans_details
				SET qty_done = qty_done + $qty_dispatched
				WHERE id=" . db_escape($line_id);
    }
    db_query($sql, "The parent document detail record could not be updated");
    return true;
}

//--------------------------------------------------------------------------------------------------
// find inventory location for given transaction
//
function get_location(&$cart)
{
    $sql = "SELECT location.*
		FROM " . TB_PREF . "stock_moves move,"
        . TB_PREF . "locations location" .
        " WHERE type=" . db_escape($cart->trans_type) .
        " AND trans_no=" . key($cart->trans_no) .
        " AND qty!=0 " .
        " AND location.loc_code=move.loc_code";

    $result = db_query($sql, 'Retreiving inventory location');

    if (db_num_rows($result)) {
        return db_fetch($result);
    }
    return null;
}

//--------------------------------------------------------------------------------------------------
// Generic read debtor transaction into cart
//
//	$trans_no - array of trans nums; special case trans_no==0 - new doc
//
function read_sales_trans($doc_type, $trans_no, &$cart, $extra_param_trans_no = 0)
{


    if (!is_array($trans_no) && $trans_no)
        $trans_no = array($trans_no);

    $cart->trans_type = $doc_type;

    if (!$trans_no) { // new document
        $cart->trans_no = $trans_no;
    } else {
        // read header data from first document
        $myrow = get_customer_trans($trans_no[0], $doc_type);

        if (count($trans_no) > 1)
            $cart->trans_no = get_customer_trans_version($doc_type, $trans_no);
        else
            $cart->trans_no = array($trans_no[0] => $myrow["version"]);

        $cart->set_sales_type($myrow["tpe"], $myrow["sales_type"], $myrow["tax_included"], 0);

        $cart->set_customer($myrow["debtor_no"], $myrow["DebtorName"],
            $myrow["curr_code"], $myrow["discount"], $myrow["payment_terms"]);

        $cart->set_branch($myrow["branch_code"], $myrow["tax_group_id"],
            $myrow["tax_group_name"]);

//		$cart->set_invoice_display_details($myrow['display_customer'],$myrow['customer_trn'],
//            $myrow['mobile'],$myrow['email'],$myrow['customer_ref']);

        $cart->reference = $myrow["reference"];
        $cart->prepaid = $myrow["prepaid"];
        $cart->order_no = $myrow["order_"];
        $cart->due_date = sql2date($myrow["due_date"]);
        $cart->document_date = sql2date($myrow["tran_date"]);
        $cart->dimension_id = $myrow['dimension_id']; // added 2.1 Joe Hunt 2008-11-12
        $cart->dimension2_id = $myrow['dimension2_id'];
        $cart->display_customer = $myrow['display_customer'];
        $cart->customer_trn = $myrow['customer_trn'];
        $cart->contact_person = $myrow['contact_person'];

        $cart->customer_mobile = $myrow['customer_mobile'];
        $cart->customer_email = $myrow['customer_email'];
        $cart->customer_ref = $myrow['customer_ref'];


        $cart->invoice_type = $myrow['invoice_type'];
        $cart->payment_flag = $myrow['payment_flag'];

        $cart->Comments = '';
        foreach ($trans_no as $trans) {
            $cart->Comments .= get_comments_string($doc_type, $trans);
        }

        // FIX this should be calculated sum() for multiply parents

        $cart->set_delivery($myrow["ship_via"], $myrow["br_name"],
            $myrow["br_address"], $myrow["ov_freight"]);

        $location = 0;
        $myrow = get_location($cart); // find location from movement

        if ($myrow != null) {
            $cart->set_location($myrow['loc_code'], $myrow['location_name']);
        }

        $result = get_customer_trans_details($doc_type, $trans_no);
        if (db_num_rows($result) > 0) {

            //Modified for AMER
            for ($line_no = 0; $myrow = db_fetch($result); $line_no++) {

                $cart->line_items[$line_no] = new line_details(
                    $myrow["stock_id"], $myrow["quantity"],
                    $myrow["unit_price"], $myrow["discount_percent"],
                    $myrow["qty_done"], $myrow["standard_cost"],
                    $myrow["StockDescription"], $myrow["id"],
                    $myrow["debtor_trans_no"],
                    @$myrow["src_id"],
                    $cart->line_items[$line_no]->govt_fee ?
                        $cart->line_items[$line_no]->govt_fee : $myrow['govt_fee'],
                    $cart->line_items[$line_no]->bank_service_charge ?
                        $cart->line_items[$line_no]->bank_service_charge : $myrow['bank_service_charge'],
                    $cart->line_items[$line_no]->bank_service_charge_vat ?
                        $cart->line_items[$line_no]->bank_service_charge_vat : $myrow['bank_service_charge_vat'],
                    $cart->line_items[$line_no]->transaction_id ?
                        $cart->line_items[$line_no]->transaction_id : $myrow['transaction_id'],
                    $cart->line_items[$line_no]->discount_amount ? $cart->line_items[$line_no]->discount_amount : $myrow['discount_amount'],
                    $cart->line_items[$line_no]->other_fee_info_json,
                    $cart->line_items[$line_no]->application_id,
                    $cart->line_items[$line_no]->govt_bank_account,
                    $cart->line_items[$line_no]->ref_name,
                    $cart->line_items[$line_no]->ed_transaction_id
                );


                $cart->line_items[$line_no]->application_id = $cart->line_items[$line_no]->application_id ?
                    $cart->line_items[$line_no]->application_id : $myrow['application_id'];

                $cart->line_items[$line_no]->govt_bank_account = $myrow['govt_bank_account'] ? $myrow['govt_bank_account'] :
                    $cart->line_items[$line_no]->govt_bank_account;

                $cart->line_items[$line_no]->ref_name = $cart->line_items[$line_no]->ref_name ?
                    $cart->line_items[$line_no]->ref_name : $myrow['ref_name'];

                $cart->line_items[$line_no]->ed_transaction_id = $cart->line_items[$line_no]->ed_transaction_id ?
                    $cart->line_items[$line_no]->ed_transaction_id : $myrow['ed_transaction_id'];


                $cart->line_items[$line_no]->transaction_id_updated_at = sql2date($myrow['transaction_id_updated_at']);

            }
        }

        $cart->prepayments = get_payments_for($trans_no, $doc_type, $myrow["debtor_no"]);

    } // !newdoc


    return true;
}

//----------------------------------------------------------------------------------------

function get_sales_child_lines($trans_type, $trans_no, $lines = true)
{
    if (!($ctype = get_child_type($trans_type)))
        return false;
    if (!is_array($trans_no)) {
        $trans_no = array($trans_no);
    }

    $par_tbl = $trans_type == ST_SALESORDER ? "sales_order_details" : "debtor_trans_details";
    $par_no = $trans_type == ST_SALESORDER ? "trans.order_no" : "trans.debtor_trans_no";

    foreach ($trans_no as $n => $trans) {
        $trans_no[$n] = db_escape($trans);
    }
    $sql = "SELECT child.*
			FROM
				" . TB_PREF . "debtor_trans_details child
			LEFT JOIN " . TB_PREF . "$par_tbl trans 
				ON child.src_id=trans.id AND child.debtor_trans_type=$ctype
			WHERE $par_no IN(" . implode(',', array_values($trans_no)) . ")";

    if (!$lines)
        $sql .= " GROUP BY child.debtor_trans_no";

    $sql .= " ORDER BY child.debtor_trans_no";

    return db_query($sql, "can't retrieve child trans");
}

function get_sales_child_numbers($trans_type, $trans_no)
{
    $trans = array();
    $res = get_sales_child_lines($trans_type, $trans_no, false);
    while ($line = db_fetch($res)) {
        $trans[] = $line['debtor_trans_no'];
    }
    return $trans;
}

function get_sales_parent_lines($trans_type, $trans_no, $lines = true)
{
    $partype = get_parent_type($trans_type);

    if (!$partype)
        return false;

    $par_tbl = $partype == ST_SALESORDER ? "sales_order_details" : "debtor_trans_details";
    $par_no = $partype == ST_SALESORDER ? "parent.order_no" : "parent.debtor_trans_no";
    $sql = "SELECT parent.*
			FROM
				" . TB_PREF . "$par_tbl parent
			LEFT JOIN " . TB_PREF . "debtor_trans_details trans 
				ON trans.src_id=parent.id
			WHERE
				trans.debtor_trans_type=" . db_escape($trans_type)
        . " AND trans.debtor_trans_no=" . db_escape($trans_no);
    if (!$lines)
        $sql .= " GROUP BY $par_no";

    $sql .= " ORDER BY $par_no";

    return db_query($sql, "can't retrieve child trans");

}

function get_sales_parent_numbers($trans_type, $trans_no)
{
    $trans = array();
    $res = get_sales_parent_lines($trans_type, $trans_no, false);
    while ($line = db_fetch($res))
        $trans[] = $line[$trans_type == ST_CUSTDELIVERY ? 'order_no' : 'debtor_trans_no'];
    return $trans;
}

//----------------------------------------------------------------------------------------

function get_sales_child_documents($trans_type, $trans_no)
{
    // FIXME -  credit notes retrieved here should be those linked to invoices containing
    // at least one line from related invoice

    if (!count($trans_no))
        return false;
    $childs = get_sales_child_numbers($trans_type, $trans_no, false);
    if (!count($childs))
        return false;

    $sql = "SELECT * FROM " . TB_PREF . "debtor_trans
		WHERE type=" . get_child_type($trans_type) . " AND trans_no IN(" . implode(',', array_values($childs)) . ")";

    return db_query($sql, "The related credit notes could not be retreived");
}


function get_sql_for_daily_report($date = null, $cost_center = null)
{

    if (empty($date))
        $date = date2sql(Today());

    $where = "";
    if (!empty($cost_center))
        $where .= " and b.dimension_id=$cost_center";


    $sql = "SELECT 

'Invoice Count' AS description, COUNT(*) AS desc_val
FROM 0_debtor_trans b
WHERE TYPE=10 AND tran_date='$date' AND ov_amount <> 0 $where UNION

SELECT 'Service Count' AS description, IFNULL(SUM(a.quantity),0) AS desc_val
FROM 0_debtor_trans_details a
LEFT JOIN 0_debtor_trans b ON b.trans_no=a.debtor_trans_no AND a.debtor_trans_type=10 AND b.type=10
WHERE a.debtor_trans_type=10 AND b.tran_date='$date' AND b.ov_amount <> 0 $where  UNION

SELECT 'Total Invoice Amount' As description, IFNULL(ROUND(SUM(b.ov_amount+b.ov_gst),2),0) FROM 
0_debtor_trans b WHERE b.tran_date = '$date' and type=10 $where UNION 

SELECT 'Total Govt Charges' AS description, 
ROUND(IFNULL(SUM((a.govt_fee+a.bank_service_charge+a.bank_service_charge_vat+a.pf_amount)*a.quantity),0),2) AS desc_val
FROM 0_debtor_trans_details a
LEFT JOIN 0_debtor_trans b ON b.trans_no=a.debtor_trans_no AND a.debtor_trans_type=10 AND b.type=10
WHERE a.debtor_trans_type=10 AND b.tran_date='$date' AND b.ov_amount <> 0 $where UNION 

SELECT 'Total Service Charge' AS description, 
IFNULL(SUM(a.unit_price*a.quantity),0) AS desc_val
FROM 0_debtor_trans_details a
LEFT JOIN 0_debtor_trans b ON b.trans_no=a.debtor_trans_no AND a.debtor_trans_type=10 AND b.type=10
WHERE a.debtor_trans_type=10 AND b.tran_date='$date' AND b.ov_amount <> 0 $where UNION 

SELECT 'Total PRO Discount' AS description, 
IFNULL(ROUND(SUM(a.discount_amount*a.quantity),2),0) AS desc_val
FROM 0_debtor_trans_details a
LEFT JOIN 0_debtor_trans b ON b.trans_no=a.debtor_trans_no AND a.debtor_trans_type=10 AND b.type=10
WHERE a.debtor_trans_type=10 AND b.tran_date='$date' AND b.ov_amount <> 0 $where UNION 



SELECT 'Total Employee Commission' AS description, 
IFNULL(ROUND(SUM(a.user_commission*a.quantity),2),0) AS desc_val
FROM 0_debtor_trans_details a
LEFT JOIN 0_debtor_trans b ON b.trans_no=a.debtor_trans_no AND a.debtor_trans_type=10 AND b.type=10
WHERE a.debtor_trans_type=10 AND b.tran_date='$date' AND b.ov_amount <> 0 $where UNION 



SELECT 'Net Service Charge', IFNULL((SUM(
(a.unit_price*a.quantity) - 
(a.discount_amount*a.quantity)
) - 
SUM(IFNULL(e.reward_amount,0))-
SUM(IFNULL((((`a`.`unit_price` * `f`.`customer_commission`) / 100) * `a`.`quantity`),0))  - 
SUM(`a`.`pf_amount` * `a`.`quantity`)),0) - 
 
 SUM(IFNULL((a.user_commission * `a`.`quantity`),0))
 
 AS desc_val  
from 0_debtor_trans_details a 
left join 0_debtor_trans b on b.trans_no = a.debtor_trans_no 
LEFT JOIN `customer_rewards` `e` ON `e`.`trans_no` = `b`.`trans_no` AND `e`.`trans_type` = 10 AND e.stock_id = a.stock_id
LEFT JOIN `0_stock_master` `d` ON `d`.`stock_id` = `a`.`stock_id` 
LEFT JOIN `0_debtors_master` `c` ON `c`.`debtor_no` = `b`.`debtor_no` 
LEFT JOIN `customer_discount_items` `f` ON `f`.`item_id` = `d`.`category_id` AND `c`.`debtor_no` = `f`.`customer_id` 
WHERE b.type=10 and a.debtor_trans_type=10 and b.tran_date='$date' $where 
";

    return $sql;

}


function get_sql_for_categorywise_sales_inquiry($date_from, $date_to, $cost_center = null)
{

    $customer_id = get_post('customer_id');

    $where = "";

    if (isset($customer_id) && !empty($customer_id)) {
        $where .= " AND b.debtor_no = $customer_id";
    }

    if (!empty($cost_center))
        $where .= " AND b.dimension_id=$cost_center ";

    // $sql = "SELECT g.description,
    // SUM(a.quantity) As total_service_count,
    // ROUND((SUM(a.unit_price*a.quantity)-SUM(a.pf_amount*a.quantity)),2) as total_service_charge,
    // ROUND(SUM(a.discount_amount*a.quantity),2) as total_pro_discount,
    // ROUND(SUM(e.reward_amount),2) total_reward_amount,

    // SUM(IFNULL((`f`.`customer_commission` * `a`.`quantity`),0)) as total_customer_commission,

    // ROUND(SUM(a.unit_tax*a.quantity),2) as total_tax,

    // ROUND(SUM(b.ov_amount+b.ov_gst),2) as total_invoice_amount,

    // ROUND(SUM(
    // CASE WHEN b.payment_flag in (2,3) THEN 0 ELSE
    // (a.govt_fee+a.bank_service_charge+a.bank_service_charge_vat+a.pf_amount)*a.quantity END),0) AS total_govt_fee,

    // ROUND((SUM(
    // (a.unit_price*a.quantity) -
    // (a.discount_amount*a.quantity)
    // ) -
    // SUM(IFNULL(e.reward_amount,0))-
    // SUM(IFNULL((`f`.`customer_commission` * `a`.`quantity`),0))  -
    // SUM(`a`.`pf_amount` * `a`.`quantity`)),2) AS net_service_charge

    // from 0_debtor_trans_details a
    // left join 0_debtor_trans b on b.trans_no = a.debtor_trans_no
    // LEFT JOIN `customer_rewards` `e` ON `e`.`trans_no` = `b`.`trans_no` AND `e`.`trans_type` = 10 AND e.stock_id = a.stock_id
    // LEFT JOIN `0_stock_master` `d` ON `d`.`stock_id` = `a`.`stock_id`
    // LEFT JOIN `0_stock_category` `g` ON `g`.`category_id` = `d`.`category_id`
    // LEFT JOIN `0_debtors_master` `c` ON `c`.`debtor_no` = `b`.`debtor_no`
    // LEFT JOIN `customer_discount_items` `f` ON `f`.`item_id` = `d`.`category_id` AND `c`.`debtor_no` = `f`.`customer_id`
    // WHERE b.type=10 and a.debtor_trans_type=10 and
    // (b.tran_date>='$date_from' and b.tran_date <= '$date_to') and a.quantity <> 0 $where  group by d.category_id";


    $reportSQL = (
    "SELECT 
                IFNULL(`e`.`reward_amount`,0) AS `reward_amount`,
                d.category_id,
                (((((`a`.`unit_price` * `a`.`quantity`) - (`a`.`discount_amount` * `a`.`quantity`)) - IFNULL(`e`.`reward_amount`,0)) - IFNULL((((`a`.`unit_price` * `f`.`customer_commission`) / 100) * `a`.`quantity`),0)) - (`a`.`pf_amount` * `a`.`quantity`)) AS `net_service_charge`,
                a.discount_amount * a.quantity AS total_pro_discount,
                a.quantity AS total_service_count,
                g.description AS description,
                SUM(IFNULL((`f`.`customer_commission` * `a`.`quantity`),0)) AS total_customer_commission,
                a.unit_tax*a.quantity AS total_tax,
                (a.unit_price - a.pf_amount) * a.quantity AS total_service_charge,  
                CASE 
                    WHEN b.payment_flag IN (2,3) THEN 0 
                    ELSE (a.govt_fee + a.bank_service_charge + a.bank_service_charge_vat + a.pf_amount) * a.quantity 
                END AS total_govt_fee,
                
                a.extra_service_charge*a.quantity AS total_extra_service_charge 
                 
            FROM `0_debtor_trans_details` `a`
                LEFT JOIN `0_debtor_trans` `b` ON `b`.`trans_no` = `a`.`debtor_trans_no`
                LEFT JOIN `0_debtors_master` `c` ON `c`.`debtor_no` = `b`.`debtor_no`
                LEFT JOIN `0_users` ON `0_users`.`id` = `a`.`created_by` 
                LEFT JOIN `0_stock_master` `d` ON `d`.`stock_id` = `a`.`stock_id`  
                LEFT JOIN `0_stock_category` `g` ON `g`.`category_id` = `d`.`category_id` 
                LEFT JOIN `customer_rewards` `e` 
                    ON `e`.`trans_no` = `b`.`trans_no` 
                        AND `e`.`trans_type` = 10 
                        AND  (`e`.`stock_id` = `a`.`stock_id`) 
                LEFT JOIN `customer_discount_items` `f` 
                    ON `f`.`item_id` = `d`.`category_id` 
                        AND `c`.`debtor_no` = `f`.`customer_id` 
            WHERE  
                `a`.`debtor_trans_type` = 10 
                AND `b`.`reference` <> 'auto' 
                AND `b`.`type` = 10 
                AND `a`.`quantity` <> 0  
                AND `b`.`ov_amount` <> 0 
                AND b.tran_date >= '$date_from' 
                AND b.tran_date <= '$date_to'  
                $where 
            GROUP BY `b`.`reference`,`a`.`stock_id`,`a`.`id`"
    );

    $sql = (
    "SELECT 
            description,
            ROUND(SUM(t1.net_service_charge), 2) net_service_charge, 
            category_id,
            ROUND(SUM(t1.total_pro_discount), 2) total_pro_discount,
            ROUND(SUM(t1.total_govt_fee), 2) AS total_govt_fee,
            SUM(t1.total_service_count) AS total_service_count,
            ROUND(SUM(t1.total_service_charge),2) AS total_service_charge,
            ROUND(SUM(t1.total_tax),2) AS total_tax,
            ROUND(SUM(t1.total_extra_service_charge),2) AS total_extra_service_charge
        FROM (
            $reportSQL
        ) AS t1
        GROUP BY category_id
        ORDER BY total_service_count DESC"
    );

    return $sql;
}

/**
 * Get service wise report
 *
 * @param array $filters An array of filters to be applied
 * @return array
 */
function get_service_wise_report($filters = [])
{
    $where = '';
    if (!empty($filters['cost_center']) && preg_match('/^[1-9][0-9]*$/', $filters['cost_center'])) {
        $where .= " AND dim.id = {$filters['cost_center']}";
    }

    $sql = (
    "SELECT
            stk_m.description item_name,
            stk_c.description category,
            SUM(dt_d.quantity) quantity,
            DATE_FORMAT(dt.tran_date, '%Y-%m') trans_month,
            dim.name cost_center
        FROM 
            0_debtor_trans_details dt_d
            LEFT JOIN 0_debtor_trans dt ON 
                dt_d.debtor_trans_no = dt.trans_no
                AND dt_d.debtor_trans_type = dt.type
            LEFT JOIN 0_stock_master stk_m ON stk_m.stock_id = dt_d.stock_id  
            LEFT JOIN 0_stock_category stk_c ON stk_c.category_id = stk_m.category_id
            LEFT JOIN 0_dimensions dim ON dim.id = dt.dimension_id
        WHERE 
            dt_d.debtor_trans_type = 10
            AND dt_d.quantity <> 0  
            AND dt.ov_amount <> 0
            $where
        GROUP BY dt_d.stock_id, dt.dimension_id, trans_month"
    );

    return db_query($sql)->fetch_all(MYSQLI_ASSOC);
}


function get_sql_for_invoice_payment_inquiry($customer_id,$user_id,$data_after,$date_to,$bank)
{


    $sql = "select a.tran_date as date_alloc,a.reference As payment_ref, 

(SELECT GROUP_CONCAT(0_debtor_trans.reference SEPARATOR ', ') from 0_cust_allocations  
left join 0_debtor_trans on 0_debtor_trans.trans_no = 0_cust_allocations.trans_no_to 
where trans_no_from = a.trans_no and 0_debtor_trans.type=10 AND trans_type_from=12 
 ) as invoice_numbers,

ROUND(a.alloc,2) as gross_payment,
ROUND((IFNULL(a.ov_discount,0)),2) as reward_amount,
ROUND((a.alloc-(IFNULL(a.ov_discount,0))),2) As net_payment,
d.bank_account_name,
e.name as customer,
g.user_id,
a.payment_method 

      

 from 0_debtor_trans a 
 
 LEFT JOIN `customer_rewards` b ON `b`.`trans_no` = `a`.`trans_no` and b.trans_type = 12  
 LEFT JOIN 0_bank_trans c on c.trans_no=a.trans_no and c.`type`=12  
 left join 0_bank_accounts d on d.id = c.bank_act 
 left join 0_debtors_master e on e.debtor_no = a.debtor_no 
 left join (SELECT * from 0_audit_trail where type=12 group by type,trans_no) f on f.trans_no = a.trans_no and f.type=12 
 left join 0_users g on g.id = f.user 
 
 where a.type=12 and a.alloc <> 0  and a.tran_date >= '$data_after' and a.tran_date  <= '$date_to '";


    if (!empty($customer_id)) {
        $sql .= " AND `a`.`debtor_no`=" . db_escape($customer_id);
    }

    if (!empty($user_id)) {
        $sql .= " AND `g`.`user_id`=" . db_escape($user_id);
    }

    if (!empty($bank)) {
        $sql .= " AND `d`.`id`=" . db_escape($bank);
    }

    return $sql;

}


//function get_sql_for_invoice_payment_inquiry(
//    $customer_id,
//    $user_id,
//    $data_after,
//    $date_to,
//    $bank,
//    $payment_method = null,
//    $pmt_cost_center = null,
//    $user_cost_center = null
//)
//{
//
//    $where = '';
//    if (!empty($customer_id)) {
//        $where .= " AND dt.`debtor_no` = " . db_escape($customer_id);
//    }
//
//    if (!empty($user_id)) {
//        $where .= " AND u.`user_id` = " . db_escape($user_id);
//    }
//
//    if (!empty($user_cost_center)) {
//        $where .= " AND u.id IN (SELECT u2.id FROM 0_users AS u2 WHERE u2.dflt_dimension_id = " . db_escape($user_cost_center) . ")";
//    }
//
//    if (!empty($bank)) {
//        $where .= " AND ba.`id` = " . db_escape($bank);
//    }
//
//    if (!empty($payment_method)) {
//        $where .= " AND dt.`payment_method` = " . db_escape($payment_method);
//    }
//
//    if (!empty($pmt_cost_center)) {
//        $where .= " AND dt.`dimension_id` = " . db_escape($pmt_cost_center);
//    }
//
//    $sql = (
//        "SELECT
//            dt.tran_date AS date_alloc,
//            dt.reference AS payment_ref,
//            (
//                SELECT
//                    GROUP_CONCAT(dt2.reference SEPARATOR ', ')
//                FROM 0_cust_allocations AS ca
//                    LEFT JOIN 0_debtor_trans AS dt2
//                        ON dt2.trans_no = ca.trans_no_to AND dt2.type = ca.trans_type_to
//                WHERE
//                    ca.trans_no_from = dt.trans_no
//                    AND ca.trans_type_from = dt.`type`
//                    AND dt2.type = 10
//            ) AS invoice_numbers,
//            ROUND(dt.ov_amount,2) AS gross_payment,
//            ROUND((IFNULL(dt.ov_discount,0)),2) AS reward_amount,
//            ROUND(dt.credit_card_charge,2) AS credit_card_charge,
//            dt.round_of_amount,
//            ROUND((dt.alloc-(IFNULL(dt.ov_discount,0)))+dt.credit_card_charge+dt.round_of_amount,2) AS net_payment,
//            ba.bank_account_name,
//            dm.name as customer,
//            u.user_id,
//            dt.payment_method
//        FROM 0_debtor_trans AS dt
//            LEFT JOIN 0_bank_trans AS bt ON bt.trans_no = dt.trans_no AND bt.`type`= dt.`type`
//            LEFT JOIN 0_bank_accounts AS ba ON ba.id = bt.bank_act
//            LEFT JOIN 0_debtors_master AS dm ON dm.debtor_no = dt.debtor_no
//            LEFT JOIN
//                (
//                    SELECT
//                        at1.`type`,
//                        at1.trans_no,
//                        at1.user
//                    FROM 0_audit_trail AS at1
//                    WHERE
//                        at1.id IN (
//                            SELECT
//                                min(`at`.id)
//                            FROM 0_audit_trail AS `at`
//                            GROUP BY
//                                `at`.`type`,
//                                `at`.trans_no
//                        )
//                ) AS at2
//                ON at2.trans_no = dt.trans_no and at2.`type` = dt.`type`
//            LEFT JOIN 0_users AS u ON u.id = at2.user
//            LEFT JOIN 0_gl_trans AS gt ON gt.`type` = dt.`type` AND gt.type_no = dt.trans_no
//        WHERE
//            dt.`type` = 12
//            AND gt.amount <> 0
//            AND dt.tran_date >= '$data_after'
//            AND dt.tran_date <= '$date_to' {$where}
//        GROUP BY dt.`type`, dt.trans_no"
//    );
//
//    return $sql;
//
//}


function get_sql_for_collection_summary($date = null, $cost_center = null)
{

    if (empty($date))
        $date = date2sql(Today());

    $where = "";
    if (!empty($cost_center))
        $where .= " and dimension_id=$cost_center";

    $sql = "SELECT 'Cash Collection' as description,IFNULL(ROUND(sum(ov_amount+credit_card_charge+round_of_amount),2),0) AS amount from 0_debtor_trans 
where payment_method='Cash' and type=12 and tran_date='$date' and ov_amount <> 0 $where UNION 

SELECT 'Credit Card Collection' as description,IFNULL(ROUND(sum(round(ov_amount+credit_card_charge+round_of_amount,2)),2),0) AS amount from 0_debtor_trans 
where payment_method='CreditCard' and type=12 and tran_date='$date' and ov_amount <> 0 $where UNION 

SELECT 'Bank Transfer Collection' as description,IFNULL(ROUND(sum(ov_amount+credit_card_charge+round_of_amount),2),0) AS amount from 0_debtor_trans 
where payment_method='BankTransfer' and type=12 and tran_date='$date' and ov_amount <> 0 $where UNION 

/* SELECT 'Total Collection' as description,IFNULL(ROUND(sum(alloc),2),0) AS amount from 0_debtor_trans 
where type=12 and tran_date='$date' UNION */

SELECT 'Total Payment Time Discount' as description, IFNULL(ROUND(IFNULL(sum(ov_discount),0),2),0) AS amount from 0_debtor_trans 
where type=12 and tran_date='$date' and ov_amount <> 0 $where UNION 


/* SELECT 'Customer Card Discount' as description, IFNULL(SUM(disc_amount),0) AS amount 
from 0_discount_trans where tran_date='$date' UNION */ 


SELECT 'Net Collection' as description,ROUND(IFNULL(sum(ov_amount+credit_card_charge+round_of_amount),0)-(
(SELECT IFNULL(sum(disc_amount),0) from 0_discount_trans where tran_date='$date' )),2) AS amount from 0_debtor_trans 
where type=12 and tran_date='$date' and ov_amount <> 0 $where ";

    return $sql;

}

/**
 * Retrieve the opening balance of the specified customer till a date
 *
 * @param integer $cust_id The customer's id
 * @param string $date upto which date the opening balance should be calculated
 * @return float
 */
function get_opening_bal($cust_id, $date = null)
{
    // customer id
    if (
        empty($cust_id)
        || !preg_match('/^[1-9][0-9]*$/', $cust_id)
    ) return false;

    // date format
    if (
        !empty($date)
        && ($dt = DateTime::createFromFormat('d/m/Y', $date))
        && $dt->format('d/m/Y') == $date
    ) {
        $date = $dt->format('Y-m-d');
    } else $date = date('Y-m-d');

    $bal = db_query(
        "SELECT SUM(amount) FROM 0_gl_trans WHERE person_id = $cust_id AND tran_date < '$date' AND account = 121001"
    )->fetch_array()[0];

    return round2(floatval($bal), 2);
}

/**
 * Get customers balance report
 *
 * @param integer $cust_id
 * @param string $dt_from format: dd/mm/yyyy
 * @param string $dt_to format: dd/mm/yyyy
 * @return array
 */
function get_cust_bal_rep($cust_id, $dt_from = null, $dt_to = null)
{
    // customer id
    if (
        empty($cust_id)
        || !preg_match('/^[1-9][0-9]*$/', $cust_id)
    ) return false;

    // date from
    if (
        !empty($dt_from)
        && ($dt = DateTime::createFromFormat('d/m/Y', $dt_from))
        && $dt->format('d/m/Y') == $dt_from
    ) {
        $dt_from = $dt->format('Y-m-d');
    } else $dt_from = date('Y-m-d');

    // date to
    if (
        !empty($dt_to)
        && ($dt = DateTime::createFromFormat('d/m/Y', $dt_to))
        && $dt->format('d/m/Y') == $dt_to
    ) {
        $dt_to = $dt->format('Y-m-d');
    } else $dt_to = date('Y-m-d');

    $sql = (
    "SELECT 
            tn.`type`,
            tn.type_no,
            tn.tran_date,
            tn.amount,
            tn.person_id,
            dm.name cust_name,
            rf.reference,
            cm.memo_,
            IF(tn.amount > 0, tn.amount,      NULL) debit,
            IF(tn.amount < 0, ABS(tn.amount), NULL) credit
        FROM 0_gl_trans tn
            LEFT JOIN 0_debtors_master dm ON dm.debtor_no = tn.person_id 
            LEFT JOIN 0_refs rf ON rf.id = tn.type_no AND rf.type = tn.type 
            LEFT JOIN 0_comments cm ON cm.id = tn.type_no AND cm.type = tn.type
        WHERE tn.account = 121001 
            AND tn.amount <> 0
            AND tn.person_id = $cust_id
            AND tn.person_type_id = 2
            AND tn.tran_date >= '$dt_from'
            AND tn.tran_date <= '$dt_to'"
    );

    return db_query($sql);
}

function get_customer_balance_report($customer_id = 0, $date_from, $date_to, $show_consolidated = 0)
{

    $date_from = date2sql($date_from);
    $date_to = date2sql($date_to);

    $where = "";
    $op_where = "person_id is null";
    if (!empty($customer_id)) {
        $where .= " AND b.debtor_no = $customer_id";
        $op_where = "person_id = $customer_id";
    }

    $where .= " AND a.tran_date >= " . db_escape($date_from);
    $where .= " AND a.tran_date <= " . db_escape($date_to);

    $sql_opbal_part = " SELECT '$date_from' as tran_date, 'OPENING BALANCE' type,'' reference,
            CASE WHEN SUM(amount) > 0 THEN ABS(SUM(amount)) ELSE 0 END as debit,
            CASE WHEN SUM(amount) < 0 THEN ABS(SUM(amount)) ELSE 0 END as credit,
            '' memo_ ,'' as type_no,'' as debtor_no ,'' AS customer 
            FROM 0_gl_trans
            WHERE $op_where AND tran_date < '$date_from' ";


    $sql_period_bal_part = "SELECT tran_date, type,reference,SUM(debit) debit, SUM(credit) credit,memo_,type_no,debtor_no,customer FROM 
            (
                SELECT a.tran_date,a.type,a.type_no,b.debtor_no, b.name as customer,c.reference,
                CASE WHEN a.amount > 0 THEN ABS(a.amount)/*+IFNULL((
                
                SELECT SUM(discount_amount) FROM 0_debtor_trans_details 
                WHERE debtor_trans_no=a.type_no AND debtor_trans_type=a.type
                
                ),0) */  ELSE 0 END AS debit, 
                CASE WHEN a.amount < 0 THEN ABS(a.amount) ELSE 0 END AS credit,
                d.memo_ 
                FROM 0_gl_trans a 
                
                LEFT JOIN 0_debtors_master b ON b.debtor_no=a.person_id 
                LEFT JOIN 0_refs c ON c.id=a.type_no AND c.type=a.type 
                LEFT JOIN 0_comments d ON d.id=a.type_no AND d.type=a.type  
                LEFT JOIN 0_debtor_trans e ON e.trans_no=a.type_no AND e.type=a.type  
                
                WHERE a.account = 121001 AND a.amount <> 0 $where ORDER BY a.tran_date 
            ) AS MyTable
            GROUP BY type,type_no ORDER BY tran_date";


    $sql = "$sql_opbal_part UNION $sql_period_bal_part";


    if ($show_consolidated) {

        $sql = "SELECT '$date_to' AS tran_date, '' AS type, '' AS reference,
		        SUM(debit) AS debit, SUM(credit) AS credit,'' AS memo_,'' AS type_no,debtor_no,customer 
		        FROM ($sql_period_bal_part) AS  consolidatedTable GROUP BY debtor_no";

    }

//    pp($sql);

    return $sql;

}


function get_customer_transaction_report($customer_id, $from_date, $to_date)
{

    $date_from = date2sql($from_date);
    $date_to = date2sql($to_date);

    $where = "";
    $op_where = "person_id is not null";
    if (!empty($customer_id)) {
        $where .= " AND b.debtor_no = $customer_id";
        $op_where = "person_id = $customer_id";
    }

    $where .= " AND a.tran_date >= " . db_escape($date_from);
    $where .= " AND a.tran_date <= " . db_escape($date_to);

    $sql = " SELECT '' reference,'$date_from' as tran_date, 'OPENING BALANCE' type,'' AS customer, '' AS debtor_no,

				CASE WHEN SUM(amount) > 0 THEN SUM(amount) ELSE 0 END AS debit,
				CASE WHEN SUM(amount) < 0 THEN SUM(amount) ELSE 0 END AS credit 
            FROM 0_gl_trans 
            WHERE $op_where AND tran_date < '$date_from' 

            UNION 
            
            SELECT a.reference,a.tran_date, a.type, b.name AS customer, a.debtor_no,
            
                CASE 
                    WHEN a.type IN (2,12) THEN a.alloc 
                    ELSE a.ov_amount+a.ov_gst 
                END AS debit, 
                
                CASE 
                    WHEN a.type IN (2,12) THEN a.ov_amount+a.ov_gst 
                    ELSE a.alloc
                END AS credit
                
            FROM 0_debtor_trans a
            LEFT JOIN 0_debtors_master b ON b.debtor_no=a.debtor_no
            
            WHERE a.ov_amount <> 0 AND a.type IN (0,1,2,10,12) $where ORDER BY customer,tran_date ";

    return $sql;

}


/**
 * @param $customer_id
 * @param $user_id
 * @param $rec_from
 * @param $rec_to
 * @param $inv_from
 * @param $inv_to
 * @param $bank
 * @return string
 * Invoice collection Report
 */
function get_invoice_payment_report($customer_id, $user_id, $rec_from, $rec_to, $inv_from, $inv_to, $bank, $show_consolidated = 0)
{


    $where = "";
    if (!empty($customer_id))
        $where .= " AND invoice.debtor_no = $customer_id";
    if (!empty($user_id))
        $where .= " AND usr.user_id = $user_id";
    if (!empty($rec_from))
        $where .= " AND payment.tran_date >= '$rec_from'";
    if (!empty($rec_to))
        $where .= " AND payment.tran_date <= '$rec_to'";
    if (!empty($inv_from))
        $where .= " AND invoice.tran_date >= '$inv_from'";
    if (!empty($inv_to))
        $where .= " AND invoice.tran_date <= '$inv_to'";
    if (!empty($bank))
        $where .= " AND bank_trans.bank_act = $bank";

    $selects = "SELECT invoice.tran_date AS invoice_date, invoice.reference AS invoice_ref, 
            customer.debtor_ref AS cust_id,customer.name AS cust_name,alloc.amt AS alloc_amount,
            payment.tran_date AS payment_date,payment.reference AS payment_ref, usr.user_id AS user_id, 
            bank_acc.bank_account_name AS bank ";

    $group_by = "";
    if ($show_consolidated) {

        $selects = "SELECT invoice.tran_date AS invoice_date, invoice.reference AS invoice_ref, 
            customer.debtor_ref AS cust_id,customer.name AS cust_name,SUM(alloc.amt) AS alloc_amount,
            payment.tran_date AS payment_date,payment.reference AS payment_ref, usr.user_id AS user_id, 
            bank_acc.bank_account_name AS bank ";

        $group_by = "GROUP BY cust_id";
    }

    $sql = "$selects FROM 0_cust_allocations alloc  
            LEFT JOIN 0_debtor_trans invoice ON invoice.trans_no=alloc.trans_no_to AND invoice.type=10 
            LEFT JOIN 0_debtor_trans payment ON payment.trans_no=alloc.trans_no_from AND payment.type=12 
            LEFT JOIN 0_debtors_master customer ON customer.debtor_no=invoice.debtor_no 
            LEFT JOIN 0_bank_trans bank_trans ON bank_trans.trans_no=payment.trans_no AND bank_trans.type=12  
            LEFT JOIN 0_bank_accounts bank_acc ON bank_acc.id = bank_trans.bank_act 
            LEFT JOIN (SELECT * FROM 0_audit_trail GROUP BY type,trans_no) audit 
                            ON audit.trans_no = payment.trans_no AND audit.type=12 
            LEFT JOIN 0_users usr on usr.id = audit.user WHERE 1=1 $where $group_by ORDER BY payment_date";

    return $sql;

}


function get_service_transactions_report($customer_id, $from_date, $to_date, $settled)
{

    $from_date = date2sql($from_date);
    $to_date = date2sql($to_date);
    $where = "";
    if (!empty($customer_id)) {
        $where .= " AND dt.debtor_no=$customer_id";
    }

    if (!empty($from_date)) {
        $where .= " AND dt.tran_date>='$from_date'";
    }

    if (!empty($to_date)) {
        $where .= " AND dt.tran_date<='$to_date'";
    }

    if ($settled == 'not_settled') {
        $where .= " AND dt_detail.transaction_id = '' AND dt_detail.ed_transaction_id = ''";
    }

    if ($settled == 'settled') {
        $where .= " AND (dt_detail.transaction_id <> '' OR dt_detail.ed_transaction_id <> '')";
    }

    $sql = "select dt.reference,dt.tran_date,debtor.name AS customer,dt_detail.description,

            dt_detail.transaction_id,dt_detail.ed_transaction_id,dt_detail.ref_name,
            dt_detail.transaction_id_updated_at AS transaction_at,
            
            ((dt_detail.quantity*dt_detail.unit_price) - (dt_detail.quantity*dt_detail.discount_amount))+
            (dt_detail.unit_tax * dt_detail.quantity)+
            ((dt_detail.govt_fee + dt_detail.bank_service_charge + dt_detail.bank_service_charge_vat) *
                                    dt_detail.quantity) AS service_total,
                                    (CASE WHEN (`dt`.`alloc` >= (`dt`.`ov_amount` + `dt`.`ov_gst`)) THEN '1' 
        WHEN (`dt`.`alloc` = 0) THEN '2' WHEN (`dt`.`alloc` < (`dt`.`ov_amount` + `dt`.`ov_gst`)) THEN '3' END) AS `payment_status`
            
             FROM 0_debtor_trans_details dt_detail 
            LEFT JOIN 0_debtor_trans dt ON dt.trans_no=dt_detail.debtor_trans_no AND dt.type=10
            LEFT JOIN 0_debtors_master debtor ON debtor.debtor_no=dt.debtor_no
            
            where dt_detail.debtor_trans_type=10 and dt_detail.quantity <> 0 $where ORDER BY dt.tran_date";

    return $sql;

}


function get_sql_for_customer_outstanding_list($customer_id, $show_pending)
{
    $where = "";
    if (!empty($customer_id)) {
        $where .= " AND dt.debtor_no=$customer_id";
    }

    $extra_select = "";
    if (!empty($show_pending)) {
        $extra_select .= ",IFNULL(SUM(((unsettled.quantity*unsettled.unit_price) - 
            (unsettled.quantity*unsettled.discount_amount))+
            ((unsettled.govt_fee + unsettled.bank_service_charge + unsettled.bank_service_charge_vat) *
             unsettled.quantity)),0) AS work_pending_total";
    }

    $sql = "SELECT debtor.name AS customer,SUM(dt.ov_amount+dt.ov_gst) AS total_invoice, 
            SUM(alloc) AS total_received,SUM(dt.ov_amount+dt.ov_gst)-SUM(alloc) AS balance_total  
            
            $extra_select 
             
            FROM 0_debtor_trans_details dt_detail 
            LEFT JOIN 0_debtor_trans dt ON dt.trans_no=dt_detail.debtor_trans_no AND dt.type=10 
            LEFT JOIN 0_debtors_master debtor ON debtor.debtor_no=dt.debtor_no 
            
            LEFT JOIN 0_debtor_trans_details unsettled ON unsettled.id=dt_detail.id 
                AND (unsettled.transaction_id = '' AND unsettled.ed_transaction_id = '' ) 
                AND unsettled.debtor_trans_type=10
            
            WHERE dt_detail.debtor_trans_type=10 AND dt_detail.quantity <> 0 $where 
            
            GROUP BY dt.debtor_no 
            ORDER BY dt.tran_date";

    return $sql;

}


function get_sub_customers($customer_id = null)
{
    $where = "";

    if (!empty($customer_id)) {
        $where .= "AND  customer_id=$customer_id";
    }

    $sql = "SELECT * FROM 0_sub_customers WHERE 1=1 $where";

    $result = db_query($sql, "an item could not be retreived");
    $return_array = [];
    $return_array[0] = "-Select-";
    while ($row = db_fetch($result)) {
        $return_array[$row['id']] = $row['name'];
    }

    return $return_array;


}


function get_next_invoice_ref($dimension_id)
{

//    $dimension_info = get_dimension($dimension_id);

    global $Refs;

    return $Refs->get_next(10);

//    $sql = "SELECT IFNULL(MAX(reference)+1,
//    CONCAT(1,LPAD(1, 6, 0))
//    ) ref FROM 0_debtor_trans
//    WHERE `type` = 10";
//
//    $result = db_fetch(db_query($sql));

    return $result['ref'];
}

function get_next_payment_ref($dimension_id)
{


    global $Refs;

    return $Refs->get_next(12);

    $dimension_info = get_dimension($dimension_id);

    $sql = "SELECT IFNULL(MAX(reference)+1,
    CONCAT(1,LPAD(1, 6, 0))
    ) ref FROM 0_debtor_trans 
    WHERE `type` = 12";

    $result = db_fetch(db_query($sql));

    return $result['ref'];
}


function duplicate_invoice_number($ref, $trans_no = 0)
{

    $sql = "select count(*) as cnt from 0_debtor_trans where reference=" . db_escape($ref) .
        " and ov_amount <> 0 and trans_no=$trans_no and type=10";

    $result = db_fetch(db_query($sql));

    if ($result['cnt'] > 0)
        return true;
    return false;

}


function get_sql_for_pending_customer_allocatables($customer_id) {

    $sql = (
        "SELECT 
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
            trans.branch_code,
            trans.payment_flag
        FROM 0_debtor_trans AS trans
        LEFT JOIN 0_cust_allocations AS alloc ON
            trans.trans_no = alloc.trans_no_to
            AND trans.type = alloc.trans_type_to 
            AND alloc.person_id = trans.debtor_no,
        INNER JOIN 0_debtors_master AS debtor ON trans.debtor_no=debtor.debtor_no
        WHERE 
            trans.debtor_no = '$customer_id'
            AND trans.payment_flag <> 2 
            AND (
                (trans.type='10' AND ROUND(IF(prep_amount, prep_amount, ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc,6) > 0)
                OR (trans.type='11' AND ROUND(-IF(prep_amount, prep_amount, ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc,6) > 0) 
                OR (trans.type = '0' AND ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount>0)
                OR (trans.type = '1' AND ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount>0)
            )
        GROUP BY `type`, trans_no
        ORDER BY trans.created_at"
    );

    return $sql;
}



function get_sql_for_pending_receipts($from,$to,$customer_id) {

    if(!empty($from)) {
        $data_after = date2sql($from);
    }
    if(!empty($to)){
        $date_to = date2sql($to);
    }

    $sql = (
        "SELECT 
            trans.type,
            trans.trans_no,
            trans.reference,
            trans.order_,
            trans.tran_date,
            trans.due_date,
            debtor.name,
            debtor.curr_code,
            (trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount)	AS TotalAmount,
            trans.alloc AS Allocated,
            trans.debtor_no
        FROM `0_debtor_trans` as trans
        INNER JOIN 0_debtors_master as debtor ON debtor.debtor_no = trans.debtor_no
        WHERE
            trans.`type` IN (0,2,11,12)
            AND IF(
                trans.`type` = 0,
                trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount < 0,
                trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount > 0
            )
            AND ROUND(
                    IF(
                        trans.prep_amount,
                        trans.prep_amount,
                        ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount)
                    ) - trans.alloc,
                    6
                ) != 0"
    );

    if(!empty($customer_id)){
        $sql .= " AND trans.debtor_no = "  . db_escape($customer_id);
    }
    if(!empty($data_after)){
        $sql .= " AND trans.tran_date >= '{$data_after}'";
    }
    if(!empty($date_to)){
        $sql .= " AND trans.tran_date <= '{$date_to}'";
    }

    return $sql;
}

/**
 * @param $customer_id
 * Run Automatic Allocation : AxisPro
 */

function runAutomaticAllocation($customer_id=null)
{

    return true;
    //Get all allocatable receipts
    $sql = get_sql_for_pending_receipts(begin_fiscalyear(),Today(),$customer_id);
    $result = db_query($sql);
    $receipts = [];
    while ($row = db_fetch($result)) {
        $receipts[] = $row;
    }

    $sql = get_sql_for_pending_customer_allocatables($customer_id);
    $result = db_query($sql);
    $outstandings = [];
    while ($row = db_fetch($result)) {
        $outstandings[] = $row;
    }

    for ($i = 0; $i < sizeof($receipts); $i++) { //Loop through all allocatable receipts

        $rcpt_amount = abs($receipts[$i]["TotalAmount"]);
        $rcpt_allocated = $receipts[$i]["Allocated"];
        $rcpt_trans_no = $receipts[$i]["trans_no"];
        $rcpt_trans_type = $receipts[$i]["type"];
        $rcpt_alloc_balance = $rcpt_amount-$rcpt_allocated;

        for ($j = 0; $j < sizeof($outstandings); $j++) { //eg : Loop through all outstanding trans

            $outst_allocated = $outstandings[$j]["alloc"];
            $outst_trans_no= $outstandings[$j]["trans_no"];
            $outst_trans_type= $outstandings[$j]["type"];
            $outst_amount = round((abs($outstandings[$j]["Total"]) - $outst_allocated), 6);

            if($rcpt_alloc_balance > 0 && $outst_amount > 0) {

                $allocating_amount = $outst_amount;
                if(($rcpt_alloc_balance-$outst_amount) < 0)
                    $allocating_amount = $rcpt_alloc_balance;

                $rcpt_alloc_balance =  $rcpt_alloc_balance-$allocating_amount;


                $sql = "select count(*) cnt from 0_cust_allocations where trans_no_from=$rcpt_trans_no 
                and trans_type_from=$rcpt_trans_type and 
                trans_no_to=$outst_trans_no and trans_type_to=$outst_trans_type and person_id=$customer_id";

                $existed_alloc = db_fetch(db_query($sql));

                if($existed_alloc['cnt'] > 0) {

                    $sql = "update 0_cust_allocations set amt=amt+$allocating_amount where trans_no_from=$rcpt_trans_no 
                and trans_type_from=$rcpt_trans_type and 
                trans_no_to=$outst_trans_no and trans_type_to=$outst_trans_type and person_id=$customer_id";

                    db_query($sql);

                }
                else {

                    add_cust_allocation($allocating_amount, $rcpt_trans_type, $rcpt_trans_no, $outst_trans_type,
                        $outst_trans_no, $customer_id, Today());

                }

                update_debtor_trans_allocation($outst_trans_type, $outst_trans_no, $customer_id);

            }

            if($rcpt_alloc_balance <= 0)
                break;

        }

        update_debtor_trans_allocation($rcpt_trans_type, $rcpt_trans_no, $customer_id);

    }

}

