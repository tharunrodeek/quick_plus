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
$page_security = 'SA_CUSTANALYTIC';
// ----------------------------------------------------------------
$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//------------------------------------------------------------------

print_report();

function get_report($customer_id = null)
{
    $sql = get_sql_for_customers_balance_inquiry($customer_id);

    return db_query($sql);
}

//----------------------------------------------------------------------------------------------------

function print_report()
{
    global $path_to_root, $systypes_array;

    $debtor_no = $_POST['PARAM_0'];
    $comments = "";
    $destination = $_POST['DESTINATION'];


    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = "P";
    $dec = user_price_dec();

    $rep = new FrontReport(trans('Customer Balance Inquiry'), "Customer_Balace_Inquiry", "A4", 9, $orientation);
    $summary = trans('Summary Report');

    $params = array(0 => $comments,
        1 => array('text' => trans('Period'), 'from' => begin_fiscalyear(), 'to' => Today()),
        2 => array('text' => trans('Type'), 'from' => $summary, 'to' => '')
    );

    $cols = array(0,150,250,350,450,550);
    $headers = array(trans('Customer'),trans('Prepaid'), trans('Pending Payment'), trans('Balance'),trans('Outstanding'));
    $aligns = array('left', 'center', 'center','center','center');
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);

    $rep->NewPage();
    $transactions = get_report($debtor_no);

    $prepaid_total = 0;
    $pending_total = 0;
    $balance_total = 0;
    $out_standing_total = 0;

    while ($trans = db_fetch($transactions)) {

        $prepaid = $trans['prepaid'];
        $pending = $trans['pending'];
        $balance = abs($trans['advance']);
        $out_standing = abs($trans['due']);

        $prepaid_total += $prepaid;
        $pending_total += $pending;
        $balance_total += $balance;
        $out_standing_total += $out_standing;

        $rep->TextCol(0, 1, $trans['name']);
        $rep->AmountCol(1, 2, $prepaid,$dec);
        $rep->AmountCol(2, 3, $pending,$dec);
        $rep->AmountCol(3, 4, $balance,$dec);
        $rep->AmountCol(4, 5, $out_standing,$dec);
        $rep->NewLine();

        if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
            $rep->Line($rep->row - 2);
            $rep->NewPage();
        }
    }
    unset($_SESSION['tmp_bal_info']);


    $rep->Font('bold');
    $rep->NewLine();
    $rep->Line($rep->row + $rep->lineHeight);
    $rep->TextCol(0, 1, trans("Total"));
    $rep->AmountCol(1, 2, $prepaid_total,$dec);
    $rep->AmountCol(2, 3, $pending_total,$dec);
    $rep->AmountCol(3, 4, $balance_total,$dec);
    $rep->AmountCol(4, 5, $out_standing_total,$dec);
    $rep->Line($rep->row - 5);
    $rep->Font();


    hook_tax_report_done();

    $rep->End();
}


