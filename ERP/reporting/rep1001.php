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
$page_security = 'SA_SALESANALYTIC';
// ----------------------------------------------------------------
$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//------------------------------------------------------------------

print_report();

function get_report($from, $to,$cost_center)
{

    if (empty($from) || empty($to)) {
        display_error(trans('Please provide a date'));
        exit;
    }

    $date_from = date2sql($from);
    $date_to = date2sql($to);

    $sql = get_sql_for_categorywise_sales_inquiry($date_from,$date_to,$cost_center);

    return db_query($sql);
}

//----------------------------------------------------------------------------------------------------

function print_report()
{
    global $path_to_root, $systypes_array;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $summaryOnly = $_POST['PARAM_2'];
    $comments = $_POST['PARAM_3'];
    $orientation = $_POST['PARAM_4'];
    $destination = $_POST['PARAM_5'];
    $cost_center = $_POST['PARAM_6'];

    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

//    $orientation = ($orientation ? 'L' : 'P');
    $orientation = "L";
    $dec = user_price_dec();

    $rep = new FrontReport(trans('Category Wise Sales Inquiry - Report'), "CategoryWiseSalesInqReport", "A4", 9, $orientation);
    $summary = trans('Detailed Report');


    $params = array(0 => $comments,
        1 => array('text' => trans('Period'), 'from' => $from, 'to' => $to),
        2 => array('text' => trans('Type'), 'from' => $summary, 'to' => '')
    );

    $cols = array(0,70,110,170,240,320,360,410,480,580,660);
    $headers = array(trans('Category'), trans('Count'),
        trans("Total Govt.Fee"),
        trans("Total Service Charge"),
        trans("Round Off/Extra Charge"),
        trans("Tax"),("Total"),
        trans("P.R.O Discount"), trans('Net Service Charge'));
    $aligns = array('left', 'left', 'left','left','left','left','left','left');
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);

    $rep->NewPage();
    $transactions = get_report($from, $to,$cost_center);
    $total_service_qty = 0;
    $total_net_service_charge = 0;
    $total_service_charge = 0;
    $total_pro_discount = 0;
    $total_govt_fee = 0;
    $total_tax = 0;
    $total_invoice_amount = 0;
    $total_extra_service_charge = 0;

    $sl_no=1;

    $total_sum = 0;

    while ($trans = db_fetch($transactions)) {

        $total_service_qty += $trans["total_service_count"];
        $total_service_charge += $trans["total_service_charge"];
        $total_net_service_charge += $trans["net_service_charge"];
        $total_pro_discount += $trans["total_pro_discount"];
        $total_govt_fee += $trans["total_govt_fee"];
        $total_tax += $trans["total_tax"];
        $total_invoice_amount += $trans["total_invoice_amount"];
        $total_extra_service_charge += $trans["total_extra_service_charge"];


//        $total = $total_service_charge+$total_govt_fee;
        $total = $trans["total_service_charge"]
            +$trans["total_govt_fee"]+$trans["total_tax"]
            +$trans['total_extra_service_charge'];

        $total_sum +=$total;

        $rep->TextCol(0, 1, $trans['description']);
        $rep->TextCol(1, 2, $trans['total_service_count']);
        $rep->TextCol(2, 3, $trans['total_govt_fee']);
        $rep->AmountCol(3, 4, $trans['total_service_charge'], 2);
        $rep->AmountCol(4, 5, $trans['total_extra_service_charge'], 2);
        $rep->AmountCol(5, 6, $trans['total_tax'], 2);
        $rep->AmountCol(6, 7, $total, 2);
        $rep->AmountCol(7, 8, $trans['total_pro_discount'], 2);
        $rep->AmountCol(8, 9, $trans['net_service_charge'], 2);
        $rep->NewLine();
        $sl_no++;

        if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
            $rep->Line($rep->row - 2);
            $rep->NewPage();
        }
    }

    $rep->Font('bold');
    $rep->NewLine();
    $rep->Line($rep->row + $rep->lineHeight);
    $rep->TextCol(0, 1, trans("Total"));
    $rep->TextCol(1, 2, $total_service_qty);
    $rep->TextCol(2, 3, $total_govt_fee);
    $rep->AmountCol(3, 4, $total_service_charge, $dec);
    $rep->AmountCol(4, 4, $total_extra_service_charge, $dec);
    $rep->AmountCol(5, 6, $total_tax, $dec);
    $rep->AmountCol(6, 7, $total_sum, $dec);
    $rep->AmountCol(7, 8, $total_pro_discount, $dec);
    $rep->AmountCol(8, 9, $total_net_service_charge, $dec);
    $rep->Line($rep->row - 5);
    $rep->Font();

    hook_tax_report_done();

    $rep->End();
}


