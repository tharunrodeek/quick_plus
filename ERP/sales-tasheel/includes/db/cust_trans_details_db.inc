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
//----------------------------------------------------------------------------------------

function get_customer_trans_details($debtor_trans_type, $debtor_trans_no)
{
    if (!is_array($debtor_trans_no))
        $debtor_trans_no = array(0 => $debtor_trans_no);

    $sql = "SELECT line.*,item.long_description,users.real_name as created_by,users.user_id,line.created_by as created_by_id,
		line.unit_price+line.unit_tax AS FullUnitPrice,
		line.description As StockDescription,
		item.units, item.mb_flag 
		FROM "
        . TB_PREF . "debtor_trans_details line,"
        . TB_PREF . "stock_master item,"
        . TB_PREF . "users users
		WHERE (";

    $tr = array();
    foreach ($debtor_trans_no as $trans_no)
        $tr[] = 'debtor_trans_no=' . db_escape($trans_no);

    $sql .= implode(' OR ', $tr);


    $sql .= ") AND debtor_trans_type=" . db_escape($debtor_trans_type) . "
		AND item.stock_id=line.stock_id AND users.id=line.created_by 
		ORDER BY id";

    return db_query($sql, "The debtor transaction detail could not be queried");
}

//----------------------------------------------------------------------------------------

function void_customer_trans_details($type, $type_no)
{

    $sql = "select created_by from " . TB_PREF . "debtor_trans_details
WHERE debtor_trans_no=" . db_escape($type_no) . " AND debtor_trans_type=" . db_escape($type);;

    $result = db_query($sql, "Cannot retreive a debtor transaction");
    $row = db_fetch($result);

    if ($row && ($row['created_by'] != '')) {
        $_SESSION['voided_trans_created_by'] = $row['created_by'];
    }

    $sql = "UPDATE " . TB_PREF . "debtor_trans_details SET quantity=0, unit_price=0,
		unit_tax=0, discount_percent=0, standard_cost=0, src_id=0
		WHERE debtor_trans_no=" . db_escape($type_no) . "
		AND debtor_trans_type=" . db_escape($type);


    db_query($sql, "The debtor transaction details could not be voided");

    // clear the stock move items
    void_stock_move($type, $type_no);
}

//----------------------------------------------------------------------------------------

function write_customer_trans_detail_item($debtor_trans_type, $debtor_trans_no, $stock_id, $description,
                                          $quantity, $unit_price, $unit_tax, $discount_percent, $std_cost, $src_id, $line_id = 0,
                                          $govt_fee = 0, $bank_service_charge = 0, $bank_service_charge_vat = 0,
                                          $transaction_id = null, $discount_amount = 0)
{




    //Modified for AMER
    $item_row = get_item($stock_id);
    $logged_in_user = get_user($_SESSION['wa_current_user']->user);
    $logged_in_user_id = $logged_in_user["id"];
    $pf_amount = $item_row['pf_amount'] ? $item_row['pf_amount'] : 0;

    $updated_by = $logged_in_user_id;

    if (isset($_SESSION["invoiced_by"]) && !empty($_SESSION["invoiced_by"]))
        $logged_in_user_id = $_SESSION["invoiced_by"];


    if ($logged_in_user['is_local'] == 1)
        $user_commission = $item_row['commission_loc_user'];
    else
        $user_commission = $item_row['commission_non_loc_user'];

    $govt_fee = $govt_fee ? $govt_fee : 0;
    $bank_service_charge = $bank_service_charge ? $bank_service_charge : 0;
    $bank_service_charge_vat = $bank_service_charge_vat ? $bank_service_charge_vat : 0;
    $transaction_id = $transaction_id ? $transaction_id : '';

    if ($line_id != 0)
        $sql = "UPDATE " . TB_PREF . "debtor_trans_details SET
			stock_id=" . db_escape($stock_id) . ",
			description=" . db_escape($description) . ",
			quantity=$quantity,
			unit_price=$unit_price,
			unit_tax=$unit_tax,
			discount_percent=$discount_percent,
			standard_cost=$std_cost,
			
			govt_fee=$govt_fee,
			bank_service_charge=$bank_service_charge,
			bank_service_charge_vat=$bank_service_charge_vat,
			transaction_id='$transaction_id',
			discount_amount=$discount_amount,
			pf_amount=$pf_amount,
			
			updated_by='$updated_by',
			
			src_id=" . db_escape($src_id) . " WHERE
			id=" . db_escape($line_id);
    else
        $sql = "INSERT INTO " . TB_PREF . "debtor_trans_details (debtor_trans_no,
				debtor_trans_type, stock_id, description, quantity, unit_price,
				unit_tax, discount_percent, standard_cost, src_id,
				govt_fee,bank_service_charge,bank_service_charge_vat,transaction_id,
				discount_amount,created_by,user_commission,pf_amount,updated_by)
			VALUES (" . db_escape($debtor_trans_no) . ", " . db_escape($debtor_trans_type) . ", " . db_escape($stock_id) .
            ", " . db_escape($description) . ",
				$quantity, $unit_price, $unit_tax, 
				$discount_percent, $std_cost," . db_escape($src_id) . ",$govt_fee,
				$bank_service_charge,$bank_service_charge_vat,'$transaction_id',
				$discount_amount,$logged_in_user_id,$user_commission,$pf_amount,$updated_by)";

    db_query($sql, "The debtor transaction detail could not be written");
}


/**
 * Add reward points to customer_rewards table,
 * if customer has assigned reward for the given item's category
 * @param $trans_no
 * @param $date_
 * @param $customer_id
 * @param $stock_id
 * @param $quantity
 * @return bool
 */
function add_customer_reward_points($invoice_no,$date_,$customer_id, $stock_id,$quantity)
{
    global $SysPrefs;

    $sql = "select * from customer_discount_items where item_id=
            (select category_id from 0_stock_master where stock_id=" . db_escape($stock_id) . " limit 1) 
            and customer_id=" . db_escape($customer_id);

    $result = db_query($sql, "could not get customer");
    $result = db_fetch($result);
    if(!$result)
        return false;
    $reward_type=1;
    $trans_type=ST_SALESINVOICE;
    $conversion_rate = $SysPrefs->reward_point_conversion_rate;
    $SQLDate = date2sql($date_);
    $reward_point = $result['reward_point'];
    $reward_amount = $reward_point*$quantity*$conversion_rate;
    $user_data = get_user($_SESSION['wa_current_user']->user);
    $created_by = $user_data["id"];

    $sql = "INSERT INTO customer_rewards 
            (trans_no,trans_type,stock_id,qty,reward_type,tran_date,customer_id,conversion_rate,reward_point,reward_amount,created_by) 
            VALUES(".db_escape($invoice_no).",".db_escape($trans_type).",
            ".db_escape($stock_id).",".$quantity.",".$reward_type.",'$SQLDate',
            ".$customer_id.",".$conversion_rate.",".$reward_point.",".$reward_amount.",".$created_by.")";

    db_query($sql, "Reward point insertion failed");

}

/**
 * Redeem reward amount
 * @param $customer_id
 * @param $trans_no
 * @param $trans_type
 * @param $date_
 * @param $reward_amount
 */
function redeem_reward_amount($customer_id,$trans_no,$trans_type,$date_,$reward_amount) {

    $SQLDate = date2sql($date_);
    $reward_type=2;
    $user_data = get_user($_SESSION['wa_current_user']->user);
    $created_by = $user_data["id"];

    $sql = "INSERT INTO customer_rewards (trans_no,trans_type,reward_type,tran_date,customer_id,reward_amount,created_by) 
            VALUES(".db_escape($trans_no).",".db_escape($trans_type).",
            ".$reward_type.",'$SQLDate',".$customer_id.",".$reward_amount.",".$created_by.")";

    db_query($sql, "Reward Redeem insertion failed");

}

