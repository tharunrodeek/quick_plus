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
$page_security = 'SA_GLANALYTIC';

// ----------------------------------------------------------------
$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/gl/includes/db/reconciliation_db.inc");

//------------------------------------------------------------------

print_report();

function get_transaction_report($filters = [])
{
    include_once "../../ERP/API/API_Call.php";

    $api = new API_Call();

    return $api->getDailySalesSummary($filters,'array');
}

function get_bank_report($filters = [])
{
    include_once "../../ERP/API/API_Call.php";

    $api = new API_Call();

    return $api->getBankBalanceReport($filters,'array');
}

function get_collection_breakdown_report($filters = [])
{
    include_once "../../ERP/API/API_Call.php";

    $api = new API_Call();

    return $api->getCollectionBreakDownReport($filters,'array');
}

function systype_name($type)
{
    global $systypes_array;

    return isset($systypes_array[$type]) ? $systypes_array[$type] : $type;
}



//----------------------------------------------------------------------------------------------------

function print_report()
{
    global $path_to_root, $systypes_array;

    $comments = "";
    $destination = $_REQUEST['EXPORT_TYPE'];

    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = "L";
    $dec = user_price_dec();


    /*** START - Daily Transaction report */

    $rep = new FrontReport(trans('DAILY REPORT'), "DailyReport", "A4", 9, $orientation);

    $params = array(0 => $comments,

        1 => array('text' => trans('Period'), 'from' => $_REQUEST['START_DATE'], 'to' => $_REQUEST['END_DATE']),
        2 => array('text' => trans('Type'), 'from' => "Daily Transaction", 'to' => ''),

    );

    $cols = array(0, 60, 200, 280, 340, 380,440,500,600);
    $headers = array(
        trans('Department  الادارة'),
        trans('No. of Trans. عدد المعاملات'),
        trans('Gov. Fees المصاريف الحكومية'),
        trans('YBC Service Charge قيمة خدمات المركز'),
        trans('Credit Facility  دفع أجل'),
        trans('Discount خصم'),
        trans('VAT  الضريبة'),
        trans('Total Collection اجمالي المبلغ المتحصلة'),
       );
    $aligns = array(
        'center',
        'right',
        'right',
        'right',
        'right',
        'right',
        'right',
        'right'
    );


    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);

    $rep->NewPage();

    $filters = [
        'START_DATE' => $_REQUEST['START_DATE'],
        'END_DATE' => $_REQUEST['START_DATE']
    ];

    $daily_summary = get_transaction_report($filters);

    $sum_total_service_count = 0;
    $sum_total_govt_fee = 0;
    $sum_total_service_charge = 0;
    $sum_total_credit_facility = 0;
    $sum_total_pro_discount = 0;
    $sum_total_tax = 0;
    $sum_total_collection_ = 0;

    foreach ($daily_summary as $trans) {

        $rep->TextCol(0, 1, $trans['description']);
        $rep->TextCol(1, 2, $trans['total_service_count']);
        $rep->AmountCol(2, 3, $trans['total_govt_fee'],$dec);
        $rep->AmountCol(3, 4, $trans['total_service_charge'],$dec);
        $rep->AmountCol(4, 5, $trans['total_credit_facility'],$dec);
        $rep->AmountCol(5, 6, $trans['total_pro_discount'],$dec);
        $rep->AmountCol(6, 7, $trans['total_tax'],$dec);
        $rep->AmountCol(7, 8, $trans['total_collection'],$dec);

        $sum_total_service_count += $trans['total_service_count'];
        $sum_total_govt_fee += $trans['total_govt_fee'];
        $sum_total_service_charge += $trans['total_service_charge'];
        $sum_total_credit_facility += $trans['total_credit_facility'];
        $sum_total_pro_discount += $trans['total_pro_discount'];
        $sum_total_tax += $trans['total_tax'];
        $sum_total_collection_ += $trans['total_collection'];

        $rep->NewLine();

        if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
            $rep->Line($rep->row - 2);
            $rep->NewPage();
        }
    }

    $rep->Font('bold');
    $rep->NewLine();
    $rep->Line($rep->row + $rep->lineHeight);
    $rep->TextCol(0, 1, "Total");
    $rep->TextCol(1, 2, $sum_total_service_count);
    $rep->AmountCol(2, 3, $sum_total_govt_fee, $dec);
    $rep->AmountCol(3, 4, $sum_total_service_charge, $dec);
    $rep->AmountCol(4, 5, $sum_total_credit_facility, $dec);
    $rep->AmountCol(5, 6, $sum_total_pro_discount, $dec);
    $rep->AmountCol(6, 7, $sum_total_tax, $dec);
    $rep->AmountCol(7, 8, $sum_total_collection_, $dec);
    $rep->Line($rep->row - 2);
    $rep->Font();
    $rep->NewLine();
    $rep->NewLine();
    $rep->NewLine();


    if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
        $rep->Line($rep->row - 2);
        $rep->NewPage();
    }

    /*** END - Daily Transaction report */


    /*** START - Accumulated Transaction report */

    $summary = trans('Accumulated Transactions');
    $date = $_REQUEST['START_DATE'];
    $phpdate = strtotime( date2sql($date) );
    $mysqldate = date( 'Y-m-d H:i:s', $phpdate );
    $month_name = date("F", strtotime($mysqldate));


    $first_day_of_month =  date('Y-m-01', $phpdate);
    $last_day_of_month =  date('Y-m-t', $phpdate);

    $params = array(0 => $comments,
        1 => array('text' => trans('Period'), 'from' => sql2date($first_day_of_month),
            'to' => sql2date($last_day_of_month)),
        2 => array('text' => trans('Month'), 'from' => $month_name, 'to' => ''),
        3 => array('text' => trans('Type'), 'from' => $summary." - ".$month_name, 'to' => '')
    );

    $cols = array(0, 200, 350, 500, 650,800,   850,900,950);
    $headers = array(
        trans('Department  الادارة'),
        trans('No. of Trans. عدد المعاملات'),
        trans('YBC Service Charge قيمة خدمات المركز'),
        trans('Total Collection اجمالي المبلغ المتحصلة'),
        trans('Total Credit Facility اجمالي المبالغ الاجلة'),
        trans(''),
        trans(''),
        trans(''),
    );
    $aligns = array(
        'center',
        'right',
        'right',
        'right',
        'right',

        'right',
        'right',
        'right',
    );


    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();


    $filters = [
        'START_DATE' => sql2date($first_day_of_month),
        'END_DATE' => sql2date($last_day_of_month)
    ];

    $accumulated_report = get_transaction_report($filters);

    $sum_total_service_count = 0;
    $sum_total_service_charge = 0;
    $sum_total_credit_facility = 0;
    $sum_total_collection = 0;

    foreach ($accumulated_report as $trans) {

        $rep->TextCol(0, 1, $trans['description']);
        $rep->TextCol(1, 2, $trans['total_service_count']);
        $rep->AmountCol(2, 3, $trans['total_service_charge'],$dec);
        $rep->AmountCol(3, 4, $trans['total_collection'],$dec);
        $rep->AmountCol(4, 5, $trans['total_credit_facility'],$dec);

        $sum_total_service_count += $trans['total_service_count'];
        $sum_total_service_charge += $trans['total_service_charge'];
        $sum_total_credit_facility += $trans['total_credit_facility'];
        $sum_total_collection += $trans['total_collection'];

        $rep->NewLine();

        if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
            $rep->Line($rep->row - 2);
            $rep->NewPage();
        }
    }

    $rep->Font('bold');
    $rep->NewLine();
    $rep->Line($rep->row + $rep->lineHeight);
    $rep->TextCol(0, 1, "Total");
    $rep->TextCol(1, 2, $sum_total_service_count);
    $rep->AmountCol(2, 3, $sum_total_service_charge, $dec);
    $rep->AmountCol(3, 4, $sum_total_collection, $dec);
    $rep->AmountCol(4, 5, $sum_total_credit_facility, $dec);
    $rep->Line($rep->row - 5);
    $rep->Font();
    $rep->NewLine();
    $rep->NewLine();
    $rep->NewLine();


    if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
        $rep->Line($rep->row - 2);
        $rep->NewPage();
    }



    /*** END - Accumulated Transaction report */



    /*** START - Bank report */

    $summary = trans('Bank Accounts');
    $date = $_REQUEST['START_DATE'];

    $params = array(0 => $comments,
        1 => array('text' => trans('Period'), 'from' => $date,
            'to' => ''),
        2 => array('text' => trans('Type'), 'from' => $summary, 'to' => '')
    );

    $cols = array(0, 200, 350, 500, 650,800, 850,900,1000);
    $headers = array(
        trans('Account Name اسماء الحسابات   '),
        trans('Today Opening Balance الرصيد الافتتاحي اليوم'),
        trans('Today Deposits  الايداعات اليوم'),
        trans('Today Transactions معاملات اليوم '),
        trans('Available Balance  الرصيد المتوفر '),

        trans(''),
        trans(''),
        trans(''),
    );
    $aligns = array(
        'center',
        'right',
        'right',
        'right',
        'right',


        'right',
        'right',
        'right',
    );


    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();


    $filters = [
        'START_DATE' => $date,
    ];

    $bank_report = get_bank_report($filters);

    $sum_opening_bal = 0;
    $sum_deposits_total = 0;
    $sum_transaction_total = 0;
    $sum_balance_total = 0;

    foreach ($bank_report as $trans) {

        $rep->TextCol(0, 1, $trans['account_name']);
        $rep->AmountCol(1, 2, $trans['opening_bal'],$dec);
        $rep->AmountCol(2, 3, $trans['debit'],$dec);
        $rep->AmountCol(3, 4, $trans['credit'],$dec);
        $rep->AmountCol(4, 5, $trans['balance'],$dec);

        $sum_opening_bal += $trans['opening_bal'];
        $sum_deposits_total += $trans['debit'];
        $sum_transaction_total += $trans['credit'];
        $sum_balance_total += $trans['balance'];

        $rep->NewLine();

        if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
            $rep->Line($rep->row - 2);
            $rep->NewPage();
        }
    }

    $rep->Font('bold');
    $rep->NewLine();
    $rep->Line($rep->row + $rep->lineHeight);
    $rep->TextCol(0, 1, "Total");
    $rep->TextCol(1, 2, $sum_opening_bal);
    $rep->AmountCol(2, 3, $sum_deposits_total, $dec);
    $rep->AmountCol(3, 4, $sum_transaction_total, $dec);
    $rep->AmountCol(4, 5, $sum_balance_total, $dec);
    $rep->Line($rep->row - 5);
    $rep->Font();
    $rep->NewLine();
    $rep->NewLine();
    $rep->NewLine();


    if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
        $rep->Line($rep->row - 2);
        $rep->NewPage();
    }



    /*** END - Bank Accounts */




    /*** START - Today Collection Breakdown */

    $summary = trans('Today Collection Breakdown');
    $date = $_REQUEST['START_DATE'];

    $params = array(0 => $comments,
        1 => array('text' => trans('Period'), 'from' => $date,
            'to' => ''),
        2 => array('text' => trans('Type'), 'from' => $summary, 'to' => '')
    );

    $cols = array(0, 200, 400, 500, 800,500, 600,700,800);
    $headers = array(
        trans('Actual Collection'),
        trans(''),
        trans(''),


        trans(''),
        trans(''),
        trans(''),
        trans(''),
        trans(''),
    );
    $aligns = array(
        'left',
        'right',
        'right',


        'right',
        'right',
        'right',
        'right',
        'right',
    );


    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();


    $filters = [
        'START_DATE' => $date,
    ];

    $collection_breakdown_report = get_collection_breakdown_report($filters);

    $net = $sum_total_collection_-abs($collection_breakdown_report[4]["amount"]);

    $push_extra = [
        'description' => "Credit Invoices Received Today  ناقصا فواتير اجلة تم استلام قيمتها اليوم",
        'amount' => $net-$collection_breakdown_report[5]["amount"],
        'flag' => true
    ];

    array_push($collection_breakdown_report,$push_extra);


    $push_extra = [
        'description' => "Advance Received Today",
        'amount' => 0-$collection_breakdown_report[3]["amount"],
        'flag' => true
    ];
    array_push($report,$push_extra);


    $push_extra = [
        'description' => "Net  الصافي",
        'amount' => 0-$net
    ];

    array_push($collection_breakdown_report,$push_extra);

    $row_cnt = 0;
    foreach ($collection_breakdown_report as $trans) {

        $d_amt = $trans['amount'];
        $c_amt = 0;

        if($d_amt < 0) {
            $c_amt = abs($d_amt);
            $d_amt = 0;
        }

        if(isset($trans["flag"]) && $trans["flag"]) {
            $d_amt = $trans['amount'];
            $c_amt = 0;
        }

        $rep->TextCol(0, 1, $trans['description']);
        $rep->AmountCol(1, 2, $d_amt,$dec);
        $rep->AmountCol(2, 3, $c_amt,$dec);
        $row_cnt++;

        $rep->NewLine();

        if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
            $rep->Line($rep->row - 2);
            $rep->NewPage();
        }
    }

    $rep->NewLine();


    if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
        $rep->Line($rep->row - 2);
        $rep->NewPage();
    }



    /*** END - Today Collection Breakdown */



    hook_tax_report_done();

    $rep->End();
}


