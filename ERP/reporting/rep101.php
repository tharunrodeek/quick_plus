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
$page_security = 'SA_CUSTPAYMREP';

// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Balances
// ----------------------------------------------------------------
$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/sales/includes/db/customers_db.inc");
include_once($path_to_root . "/API/API_Call.php");

//----------------------------------------------------------------------------------------------------

print_customer_balances();

function get_open_balance($debtorno, $to)
{
    if ($to)
        $to = date2sql($to);
    $sql = "SELECT SUM(IF(t.type = " . ST_SALESINVOICE . " OR (t.type IN (" . ST_JOURNAL . " , " . ST_BANKPAYMENT . ") AND t.ov_amount>0),
             -abs(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount), 0)) AS charges,";

    $sql .= "SUM(IF(t.type != " . ST_SALESINVOICE . " AND NOT(t.type IN (" . ST_JOURNAL . " , " . ST_BANKPAYMENT . ") AND t.ov_amount>0),
             abs(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount) * -1, 0)) AS credits,";

    $sql .= "SUM(IF(t.type != " . ST_SALESINVOICE . " AND NOT(t.type IN (" . ST_JOURNAL . " , " . ST_BANKPAYMENT . ")), t.alloc * -1, t.alloc)) AS Allocated,";

    $sql .= "SUM(IF(t.type = " . ST_SALESINVOICE . " OR (t.type IN (" . ST_JOURNAL . " , " . ST_BANKPAYMENT . ") AND t.ov_amount>0), 1, -1) *
 			(abs(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount) - abs(t.alloc))) AS OutStanding
		FROM " . TB_PREF . "debtor_trans t
    	WHERE t.debtor_no = " . db_escape($debtorno)
        . " AND t.type <> " . ST_CUSTDELIVERY;
    if ($to)
        $sql .= " AND t.tran_date < '$to'";
    $sql .= " GROUP BY debtor_no";

    $result = db_query($sql, "No transactions were returned");
    return db_fetch($result);
}

function get_transactions($debtorno, $from, $to)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $allocated_from =
        "(SELECT trans_type_from as trans_type, trans_no_from as trans_no, date_alloc, sum(amt) amount
 			FROM " . TB_PREF . "cust_allocations alloc
 				WHERE person_id=" . db_escape($debtorno) . "
 					AND date_alloc <= '$to'
 				GROUP BY trans_type_from, trans_no_from) alloc_from";
    $allocated_to =
        "(SELECT trans_type_to as trans_type, trans_no_to as trans_no, date_alloc, sum(amt) amount
 			FROM " . TB_PREF . "cust_allocations alloc
 				WHERE person_id=" . db_escape($debtorno) . "
 					AND date_alloc <= '$to'
 				GROUP BY trans_type_to, trans_no_to) alloc_to";

    $sql = "SELECT trans.*,
     
     CASE WHEN trans.type = 10 THEN group_concat(dt_detail.ref_name) ELSE 
(select group_concat(memo_) from 0_comments where id=trans.trans_no and type = trans.type) 
	END as ref_comments,
     
 		(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount) AS TotalAmount,
 		IFNULL(alloc_from.amount, alloc_to.amount) AS Allocated,
 		((trans.type = " . ST_SALESINVOICE . ")	AND trans.due_date < '$to') AS OverDue
     	FROM " . TB_PREF . "debtor_trans trans
 			LEFT JOIN " . TB_PREF . "voided voided ON trans.type=voided.type AND trans.trans_no=voided.id
 			LEFT JOIN $allocated_from ON alloc_from.trans_type = trans.type AND alloc_from.trans_no = trans.trans_no
 			LEFT JOIN $allocated_to ON alloc_to.trans_type = trans.type AND alloc_to.trans_no = trans.trans_no


LEFT JOIN 0_debtor_trans_details dt_detail on dt_detail.debtor_trans_no=trans.trans_no 
	        and dt_detail.debtor_trans_type=10 and trans.type=10 

     	WHERE trans.tran_date >= '$from'
 			AND trans.tran_date <= '$to'
 			AND trans.debtor_no = " . db_escape($debtorno) . "
 			AND trans.type <> " . ST_CUSTDELIVERY . "
 			AND ISNULL(voided.id) 
 			
 			
 			GROUP BY trans.type,trans.trans_no 	
     	ORDER BY trans.tran_date";
    return db_query($sql, "No transactions were returned");
}

//----------------------------------------------------------------------------------------------------

function print_customer_balances()
{
    global $path_to_root, $systypes_array;


    $show_alloc_col = false;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $fromcust = $_POST['PARAM_2'];
    $show_balance = $_POST['PARAM_3'];
    $currency = $_POST['PARAM_4'];
    $no_zeros = $_POST['PARAM_5'];
    $comments = $_POST['PARAM_6'];
    $orientation = $_POST['PARAM_7'];
    $destination = $_POST['PARAM_8'];
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ($orientation ? 'L' : 'P');
    if ($fromcust == ALL_TEXT)
        $cust = trans('All');
    else
        $cust = get_customer_name($fromcust);
    $dec = user_price_dec();

    if ($show_balance) $sb = trans('Yes');
    else $sb = trans('No');

    if ($currency == ALL_TEXT) {
        $convert = true;
        $currency = trans('Balances in Home Currency');
    } else
        $convert = false;

    if ($no_zeros) $nozeros = trans('Yes');
    else $nozeros = trans('No');

    $cols = [
        ["key" => 1, "width" => 90],
        ["key" => 2, "width" => 45],
        ["key" => 3, "width" => 60],
        ["key" => 4, "width" => 50],
        ["key" => 5, "width" => 70],
        ["key" => 6, "width" => 65],
        ["key" => 7, "width" => 65],
        ["key" => 8, "width" => 65]
    ];

    $headers = array(trans('Trans Type'), trans('#'), trans('Date'), trans('Remarks'), trans('Debit'), trans('Credits'),
        trans('Allocated'), trans('Outstanding'));

    if ($show_balance)
        $headers[7] = trans('Balance');
    $aligns = array('left', 'left', 'left', 'left', 'right', 'right', 'right', 'right');


    if ($show_alloc_col == false) {

        array_pop($cols);
        $headers = array(trans('Trans Type'), trans('#'), trans('Date'), trans('Remarks'), trans('Debit'), trans('Credits'),
            trans('Outstanding'));
        if ($show_balance)
            $headers[6] = trans('Balance');
        $aligns = array('left', 'left', 'left', 'left', 'right', 'right', 'right');

    }

    $api = new API_Call();
    $colDefs = $api->calculateColumnInfo($cols, user_pagesize(), $orientation);
    $cols       = $colDefs->index;
    $col_points = $colDefs->points;

    $params = array(0 => $comments,
        1 => array('text' => trans('Period'), 'from' => $from, 'to' => $to),
        2 => array('text' => trans('Customer'), 'from' => $cust, 'to' => ''),
        3 => array('text' => trans('Show Balance'), 'from' => $sb, 'to' => ''),
        4 => array('text' => trans('Currency'), 'from' => $currency, 'to' => ''),
        5 => array('text' => trans('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));

    $rep = new FrontReport(trans('Customer Statement'), "CustomerBalances", user_pagesize(), 9, $orientation);
    $rep->Font();
    $rep->Info($params, $col_points, $headers, $aligns);
    $rep->NewPage();

    $grandtotal = array(0, 0, 0, 0);

    $sql = "SELECT debtor_no, name, curr_code FROM " . TB_PREF . "debtors_master ";
    if ($fromcust != ALL_TEXT)
        $sql .= "WHERE debtor_no=" . db_escape($fromcust);
    $sql .= " ORDER BY name";
    $result = db_query($sql, "The customers could not be retrieved");

    $op_bal_total = 0;

    while ($myrow = db_fetch($result)) {
        if (!$convert && $currency != $myrow['curr_code']) continue;


        $accumulate = 0;
        $rate = $convert ? get_exchange_rate_from_home_currency($myrow['curr_code'], Today()) : 1;
        $bal = get_open_balance($myrow['debtor_no'], $from);
        $init[0] = $init[1] = 0.0;
        $init[0] = round2(abs($bal['charges'] * $rate), $dec);
        $init[1] = round2(Abs($bal['credits'] * $rate), $dec);
        $init[2] = round2($bal['Allocated'] * $rate, $dec);
        if ($show_balance) {
            $init[3] = $init[0] - $init[1];
            $accumulate += $init[3];
        } else
            $init[3] = round2($bal['OutStanding'] * $rate, $dec);
//
        $res = get_transactions($myrow['debtor_no'], $from, $to);
        if ($no_zeros && db_num_rows($res) == 0) continue;
//
//        $rep->fontSize += 2;
//        $rep->TextCol(0, 2, $myrow['name']);
//        if ($convert)
//            $rep->TextCol(2, 3, $myrow['curr_code']);
//        $rep->fontSize -= 2;
        $rep->TextCol($cols[4][0], $cols[4][1], trans("Open Balance"));
//        $rep->AmountCol(4, 5, $init[0], $dec);
//        $rep->AmountCol(5, 6, $init[1], $dec);
//
//
        if ($show_alloc_col == true) {
//            $rep->AmountCol(6, 7, $init[2], $dec);
            $rep->AmountCol($cols[8][0], $cols[8][1], $init[3], $dec);
        } else {

            $rep->AmountCol($cols[7][0], $cols[7][1], $init[3], $dec);
        }

        $op_bal_total = $init[3];


        $total = array(0, 0, 0, 0);
        // for ($i = 0; $i < 4; $i++) {
        //    $total[$i] += $init[$i];
        //    $grandtotal[$i] += $init[$i];
        // }
//        $rep->NewLine(1, 2);
//        $rep->Line($rep->row + 4);
        if (db_num_rows($res) == 0) {
            $rep->NewLine(1, 2);
            continue;
        }
        while ($trans = db_fetch($res)) {
            if ($no_zeros) {
                if ($show_balance) {
                    if ($trans['TotalAmount'] == 0) continue;
                } else {
                    if (floatcmp($trans['TotalAmount'], $trans['Allocated']) == 0) continue;
                }
            }
            $rep->NewLine(1, 2);
            $rep->TextCol($cols[1][0], $cols[1][1], $systypes_array[$trans['type']]);
            $rep->TextCol($cols[2][0], $cols[2][1], $trans['reference']);
            $rep->DateCol($cols[3][0], $cols[3][1], $trans['tran_date'], true);
//			if ($trans['type'] == ST_SALESINVOICE)
            $rep->TextCol($cols[4][0], $cols[4][1], $trans['ref_comments'], true);
            $item[0] = $item[1] = 0.0;
            if ($trans['type'] == ST_CUSTCREDIT || $trans['type'] == ST_CUSTPAYMENT || $trans['type'] == ST_BANKDEPOSIT)
                $trans['TotalAmount'] *= -1;
            if ($trans['TotalAmount'] > 0.0) {
                $item[0] = round2(abs($trans['TotalAmount']) * $rate, $dec);
                $rep->AmountCol($cols[5][0], $cols[5][1], $item[0], $dec);
                $accumulate += $item[0];
                $item[2] = round2($trans['Allocated'] * $rate, $dec);
            } else {
                $item[1] = round2(Abs($trans['TotalAmount']) * $rate, $dec);
                $rep->AmountCol($cols[6][0], $cols[6][1], $item[1], $dec);
                $accumulate -= $item[1];
                $item[2] = round2($trans['Allocated'] * $rate, $dec) * -1;
            }


            if ($show_alloc_col == true) {


                $rep->AmountCol($cols[7][0], $cols[7][1], $item[2], $dec);
                if (($trans['type'] == ST_JOURNAL && $item[0]) || $trans['type'] == ST_SALESINVOICE || $trans['type'] == ST_BANKPAYMENT)
                    $item[3] = $item[0] - $item[2];
                else
                    $item[3] = -$item[1] - $item[2];
                if ($show_balance)
                    $rep->AmountCol($cols[8][0], $cols[8][1], $accumulate, $dec);
                else
                    $rep->AmountCol($cols[8][0], $cols[8][1], $item[3], $dec);

            } else {


                if (($trans['type'] == ST_JOURNAL && $item[0]) || $trans['type'] == ST_SALESINVOICE || $trans['type'] == ST_BANKPAYMENT)
                    $item[3] = $item[0] - $item[2];
                else
                    $item[3] = -$item[1] - $item[2];
                if ($show_balance)
                    $rep->AmountCol($cols[7][0], $cols[7][1], $accumulate, $dec);
                else
                    $rep->AmountCol($cols[7][0], $cols[7][1], $item[0]-$item[1], $dec);

            }


            for ($i = 0; $i < 4; $i++) {
                $total[$i] += $item[$i];
                $grandtotal[$i] += $item[$i];
            }
            if ($show_balance)
                $total[3] = $total[0] - $total[1];


            //FOR TASHEEL CUSTOMER CARD
            if ($trans['payment_flag'] == 2 && $trans['type'] == 10) {

                $rep->NewLine(1, 2);
                $rep->TextCol($cols[1][0], $cols[1][1], "Customer Card Payment");
                $rep->TextCol($cols[2][0], $cols[2][1], '');
                $rep->DateCol($cols[3][0], $cols[3][1], $trans['tran_date'], true);
                $rep->TextCol($cols[4][0], $cols[4][1], 'Payment entry for customer card', true);


                if (isset($trans['special_invoice']) && $trans['special_invoice'] == 1)
                    $item[0] = round2(abs(53) * $rate, $dec);
                else
                    $item[0] = round2(abs($trans['customer_card_amount']) * $rate, $dec);

                $rep->AmountCol($cols[6][0], $cols[6][1], $item[0], $dec);

                $accumulate -= $item[0];

                if ($show_alloc_col == true) {
                    $rep->AmountCol($cols[7][0], $cols[7][1], $item[0] * -1, $dec);

                    if (($trans['type'] == ST_JOURNAL && $item[0]) || $trans['type'] == ST_SALESINVOICE || $trans['type'] == ST_BANKPAYMENT)
                        $item[3] = $item[0] - $item[2];
                    else
                        $item[3] = -$item[1] - $item[2];
                    if ($show_balance)
                        $rep->AmountCol($cols[8][0], $cols[8][1], $accumulate, $dec);
                    else
                        $rep->AmountCol($cols[8][0], $cols[8][1], 0, $dec);
                } else {


                    if (($trans['type'] == ST_JOURNAL && $item[0]) || $trans['type'] == ST_SALESINVOICE || $trans['type'] == ST_BANKPAYMENT)
                        $item[3] = $item[0] - $item[2];
                    else
                        $item[3] = -$item[1] - $item[2];
                    if ($show_balance)
                        $rep->AmountCol($cols[7][0], $cols[7][1], $accumulate, $dec);
                    else
                        $rep->AmountCol($cols[7][0], $cols[7][1], -$item[0], $dec);

                }


                $total[1] += $item[0];
                $total[2] += $item[0] * -1;
                $grandtotal[1] += $item[0];
                $grandtotal[2] += $item[0] * -1;

                if ($show_balance)
                    $total[3] = $total[0] - $total[1];


            }

        }


        $rep->Line($rep->row - 8);
        $rep->NewLine(2);
        $rep->TextCol($cols[1][0], $cols[3][1], trans('Total'));

//        if ($show_balance)
            $total[2] = $total[0] - $total[1]+$op_bal_total;

        $cnt = 4;

        if (!$show_alloc_col) $cnt = 3;

        for ($i = 0; $i < $cnt; $i++)
            $rep->AmountCol($cols[$i + 5][0], $cols[$i + 5][1], $total[$i], $dec);
        $rep->Line($rep->row - 4);
        $rep->NewLine(2);
    }


    $rep->fontSize += 2;
//    $rep->TextCol(0, 3, trans('Grand Total'));
    $rep->fontSize -= 2;
//    if ($show_balance)
        $grandtotal[2] = $grandtotal[0] - $grandtotal[1];

    $cnt = 4;
    if (!$show_alloc_col) $cnt = 3;

//    for ($i = 0; $i < $cnt; $i++)
//        $rep->AmountCol($i + 4, $i + 5, $grandtotal[$i], $dec);
//    $rep->Line($rep->row - 4);
    $rep->NewLine();
    $rep->End();
}

