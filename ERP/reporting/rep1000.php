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
// ----------------------------------------------------------------
$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

$canAccess = [
    "OWN" => user_check_access('SA_CSHCOLLECTREP'),
    "DEP" => user_check_access('SA_CSHCOLLECTREPDEP'),
    "ALL" => user_check_access('SA_CSHCOLLECTREPALL')
];

$page_security = in_array(true, $canAccess, true) ? 'SA_ALLOW' : 'SA_DENIED';
//------------------------------------------------------------------

print_report($canAccess);

function get_invoice_payment_inquiry($from, $to, $customer_id, $bank, $user_id, $pay_method, $cost_center, $user_cost_center, $canAccess)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $data_after = $from;
    $date_to = $to;

    if(!$canAccess['ALL']) {
        $user_cost_center = $_SESSION['wa_current_user']->default_cost_center;

        if(!$canAccess['DEP']) {
            $user_id = $_SESSION['wa_current_user']->loginname;
        }
    }

    $sql = get_sql_for_invoice_payment_inquiry(
        $customer_id,
        $user_id,
        $data_after,
        $date_to,
        $bank,
        $pay_method,
        $cost_center,
        $user_cost_center
    );

    return db_query($sql);
}

//----------------------------------------------------------------------------------------------------

function print_report($canAccess)
{
    global $path_to_root, $systypes_array;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $summaryOnly = $_POST['PARAM_2'];
    $comments = $_POST['PARAM_3'];
    $orientation = $_POST['PARAM_4'];
    $destination = $_POST['PARAM_5'];
    $customer_id = $_POST['PARAM_6'];
    $bank = $_POST['PARAM_7'];
    $user_id = $_POST['PARAM_8'];
    $pay_method = $_POST['PARAM_9'];
    $cost_center = $_POST['PARAM_10'];
    $user_cost_center = $_POST['PARAM_11'];

    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

//    $orientation = ($orientation ? 'L' : 'P');
    $orientation = "L";
    $dec = user_price_dec();

    $rep = new FrontReport(trans('Payment Collection Report'), "PaymentCollectionReport", "A3", 9, $orientation);
    $summary = trans('Detailed Report');

    if($customer_id == ALL_TEXT) {
        $cust = trans("All");
    }
    else {
        $cust = get_customer_name($customer_id);
    }
    if($bank == ALL_TEXT) {
        $bank_name = trans("All");
    }
    else {
        $bank_info=get_bank_account($bank);
        $bank_name = $bank_info["bank_account_name"];
    }
    if($user_id == ALL_TEXT) {
        $user = trans("All");
    }
    else {
        $user_info = get_user_by_login($user_id);
        $user = $user_info["user_id"];
    }

    $params = array(0 => $comments,
        1 => array('text' => trans('Period'), 'from' => $from, 'to' => $to),
        2 => array('text' => trans('Type'), 'from' => $summary, 'to' => ''),
        3 => array('text' => trans('Customer'), 'from' => $cust, 'to' => ''),
        4 => array('text' => trans('Bank'), 'from' => $bank_name, 'to' => ''),
        5 => array('text' => trans('User'), 'from' => $user, 'to' => '')
    );

    $cols = array(0, 70, 130, 250, 300, 350, 400, 450, 500, 570,620,680,720);
    $headers = array(trans('Date'), trans('Receipt No'), trans('Invoices'), trans('Gross'), trans('Discount'),
        trans('Bank Comm.'),
        trans('Round of Amt'),
        trans('Total Receipt'), trans('Bank'), trans('Customer'),  trans('User'),trans('Pay.Method'));
    $aligns = array('left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left','left','left','left');
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);

    $rep->NewPage();
    $transactions = get_invoice_payment_inquiry(
        $from,
        $to,
        $customer_id,
        $bank,
        $user_id,
        $pay_method,
        $cost_center,
        $user_cost_center,
        $canAccess
    );
    $total_gross = 0;
    $total_rew_disc = 0;
    $total_net = 0;
    $total_bank_charge = 0;
    $total_round_of_amount = 0;

    $total_cash_collection = 0;
    $total_credit_card_collection = 0;

    while ($trans = db_fetch($transactions)) {

        $total_gross += $trans['gross_payment'];
        $total_rew_disc += $trans['reward_amount'];
        $total_net += $trans['net_payment'];
        $total_bank_charge += $trans['credit_card_charge'];
        $total_round_of_amount += $trans['round_of_amount'];


        if($trans['payment_method'] == 'Cash')
            $total_cash_collection += $trans['net_payment'];

        if($trans['payment_method'] == 'CreditCard')
            $total_credit_card_collection += $trans['net_payment'];

        $rep->DateCol(0, 1, $trans['date_alloc'], true);
        $rep->TextCol(1, 2, $trans['payment_ref']);
//        $rep->TextCol(2, 3, $trans['invoice_numbers']);

        $rep->TextCol(2, 3, $trans['invoice_numbers']);


        $rep->AmountCol(3, 4, $trans['gross_payment'], 2);
        $rep->AmountCol(4, 5, $trans['reward_amount'], 2);
        $rep->AmountCol(5, 6, $trans['credit_card_charge'], 2);
        $rep->AmountCol(6, 7, $trans['round_of_amount'], 2);

        $rep->AmountCol(7, 8, $trans['net_payment'], $dec);
        $rep->TextCol(8, 9, $trans['bank_account_name']);
        $rep->TextCol(9, 10, $trans['customer']);

        $rep->TextCol(10, 11, $trans['user_id']);
        $rep->TextCol(11, 12, $trans['payment_method']);
        $rep->NewLine();

        if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
            $rep->Line($rep->row - 2);
            $rep->NewPage();
        }
    }

    $rep->Font('bold');
    $rep->NewLine();
    $rep->Line($rep->row + $rep->lineHeight);
    $rep->TextCol(0, 2, trans("Total"));
    $rep->AmountCol(3, 4, $total_gross, $dec);
    $rep->AmountCol(4, 5, $total_rew_disc, $dec);
    $rep->AmountCol(5, 6, $total_bank_charge, $dec);
    $rep->AmountCol(6, 7, $total_round_of_amount, $dec);
    $rep->AmountCol(7, 8, $total_net, $dec);
    $rep->Line($rep->row - 5);
    $rep->Font();

    $rep->NewLine();
    $rep->NewLine(2);

    $rep->TextCol(0, 2, trans("Total Cash Collection"));
    $rep->AmountCol(2, 3, $total_cash_collection, $dec);
    $rep->NewLine();

    $rep->TextCol(0, 2, trans("Total Credit Card Collection"));
    $rep->AmountCol(2, 3, $total_credit_card_collection, $dec);



    hook_tax_report_done();

    $rep->End();
}


