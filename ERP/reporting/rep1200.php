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
$page_security = 'SA_SALESTRANSVIEW';
// ----------------------------------------------------------------
$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//------------------------------------------------------------------

print_report();

function get_report($from, $to,$dim=null)
{


    $where = "";

    if (!empty($from)) {
        $from = date2sql($from);

        $where .= " AND gl.tran_date >= " . db_escape($from);

    }

    if (!empty($to)) {
        $to = date2sql($to);
        $where .= " AND gl.tran_date <= " . db_escape($to);
    }

    if(!empty($dim)) {
        $where .= " AND gl.dimension_id =$dim";
    }


    $sql = "select chart.account_name, chart.account_code,

sum(if (gl.amount > 0,abs(amount),0)) debit_total,
sum(if (gl.amount < 0,abs(amount),0)) credit_total,

gl.account,chart_type.name group_name, chart_class.class_name  from 0_gl_trans gl 

left join 0_chart_master chart on chart.account_code=gl.account 

left join 0_chart_types chart_type on chart_type.id=chart.account_type 
left join 0_chart_class chart_class on chart_class.cid=chart_type.class_id


where chart.account_code<>0 and chart.account_code <> '' $where 
group by chart.account_code order by chart_type.class_id,chart_type.id";


    return db_query($sql);
}

//----------------------------------------------------------------------------------------------------

function print_report()
{


    global $path_to_root;

    $comments = "";
    $destination = $_POST['DESTINATION'];

    $show_op_cl = isset($_POST['SHOW_OP_CL']) ? $_POST['SHOW_OP_CL'] : 'no';

//    pp($show_op_cl);

    $from = $_POST['FromDate'];
    $to = $_POST['ToDate'];
    $dim = $_POST['dim'];


    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = "P";
    $dec = user_price_dec();


    $rep = new FrontReport(trans('Trial Balance'), "Trial_Balance_New", "A3", 9, $orientation);

    $params = array(0 => $comments,
        1 => array('text' => trans('Customer'), 'from' => "", 'to' => ""),
    );

    $cols = array(0, 200, 350, 450, 550, 650, 720);
    $headers = array(trans('Particulars'), trans('Opening Balance'), trans('Debit Total (Period)'),
        trans('Credit Total(Period)'),
        trans('Period Difference'),
        trans('Balance'));
    $aligns = array('left', 'right', 'right', 'right', 'right', 'right');


    if($show_op_cl == 'no') {

        $cols = array(0, 200, 350, 450, 600);
        $headers = array(trans('Particulars'), trans('Debit'),
            trans('Credit'),
            trans('Difference'));
        $aligns = array('left', 'right', 'right', 'right');

    }


    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);

    $rep->NewPage();
    $transactions = get_report($from, $to,$dim);


    $op_sum = 0;
    $debit_sum = 0;
    $credit_sum = 0;
    $period_diff_sum = 0;
    $closing_sum = 0;


    $current_group = "";
    $group_op_sum = 0;
    $group_dr_sum = 0;
    $group_cr_sum = 0;
    $group_bal_sum = 0;
    $group_period_diff_sum = 0;

    $total_rows = db_num_rows($transactions);

    $loop_cnt = 0;

    while ($trans = db_fetch($transactions)) {

        $loop_cnt += 1;

        $where = "";
        if(!empty($dim)){
            $where .= " AND dimension_id=$dim";
        }

        $sql = "SELECT SUM(amount) amount FROM 0_gl_trans
            WHERE account = " . $trans['account'] . " AND tran_date < " . db_escape(date2sql($from)) ." $where";
        $op_query = db_query($sql);
        $opening_bal = db_fetch($op_query);
        $opening_bal = $opening_bal['amount'];

        $closing_bal = $trans['debit_total'] - $trans['credit_total'] + $opening_bal;

        if ($current_group != $trans['group_name']) {


            if ($current_group != "") {

                display_group_total($rep, $current_group, $group_op_sum, $group_dr_sum, $group_cr_sum, $group_period_diff_sum, $group_bal_sum,$show_op_cl);

            }
            $current_group = $trans['group_name'];

            $rep->row -= 4;

            $rep->Font('bold');
            $rep->fontSize += 2;


            if($show_op_cl == 'no') {
                $rep->TextCol(0, 4, $current_group);
            }
            else{
                $rep->TextCol(0, 6, $current_group);
            }


            $rep->row -= 4;
            $rep->Font();
            $rep->Line($rep->row);
            $rep->NewLine();

            $rep->fontSize -= 2;


            $group_op_sum = 0;
            $group_dr_sum = 0;
            $group_cr_sum = 0;
            $group_bal_sum = 0;
            $group_period_diff_sum = 0;

        }


        $period_diff = $trans['debit_total'] - $trans['credit_total'];



        if($show_op_cl == 'no') {

            $rep->TextCol(0, 1, $trans['account_code']." - ".$trans['account_name']);
            $rep->AmountCol(1, 2, $trans['debit_total'], $dec);
            $rep->AmountCol(2, 3, $trans['credit_total'], $dec);
            $rep->AmountCol(3, 4, $period_diff, $dec);

        }
        else {

            $rep->TextCol(0, 1, $trans['account_code']." - ".$trans['account_name']);
            $rep->TextCol(1, 2, number_format2($opening_bal, 2));
            $rep->AmountCol(2, 3, $trans['debit_total'], $dec);
            $rep->AmountCol(3, 4, $trans['credit_total'], $dec);
            $rep->AmountCol(4, 5, $period_diff, $dec);
            $rep->AmountCol(5, 6, $closing_bal, $dec);
        }






        $op_sum += $opening_bal;
        $debit_sum += $trans['debit_total'];
        $credit_sum += $trans['credit_total'];
        $period_diff_sum += $period_diff;
        $closing_sum += $closing_bal;

        $group_op_sum += $opening_bal;
        $group_dr_sum += $trans['debit_total'];
        $group_cr_sum += $trans['credit_total'];
        $group_period_diff_sum += $period_diff;
        $group_bal_sum += $closing_bal;


        $rep->NewLine();


        if ($loop_cnt == $total_rows) {

            display_group_total($rep, $current_group, $group_op_sum, $group_dr_sum, $group_cr_sum, $group_period_diff_sum, $group_bal_sum,$show_op_cl);

        }


        if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
            $rep->Line($rep->row - 2);
            $rep->NewPage();
        }
    }


    $rep->Font('bold');
    $rep->NewLine();
    $rep->Line($rep->row + $rep->lineHeight);
    $rep->TextCol(0, 1, 'Grand Total');


    if($show_op_cl == 'no') {

        $rep->AmountCol(1, 2, $debit_sum, $dec);
        $rep->AmountCol(2, 3, $credit_sum, $dec);
        $rep->AmountCol(3, 4, $period_diff_sum, $dec);

    }
    else {
        $rep->AmountCol(1, 2, $op_sum, 2);
        $rep->AmountCol(2, 3, $debit_sum, $dec);
        $rep->AmountCol(3, 4, $credit_sum, $dec);
        $rep->AmountCol(4, 5, $period_diff_sum, $dec);
        $rep->AmountCol(5, 6, $closing_sum, $dec);
    }




    $rep->Line($rep->row - 5);
    $rep->Font();


    hook_tax_report_done();

    $rep->End();
}


function display_group_total($rep, $current_group, $group_op_sum, $group_dr_sum, $group_cr_sum, $group_period_diff, $group_bal_sum,$show_op_cl)
{

    $rep->Line($rep->row);
    $rep->NewLine();

    $rep->row += 2;


//                $rep->fontSize += 2;

    $rep->TextCol(0, 1, 'Total - ' . $current_group);

    if($show_op_cl == 'no') {

        $rep->AmountCol(1, 2, $group_dr_sum, 2);
        $rep->AmountCol(2, 3, $group_cr_sum, 2);
        $rep->AmountCol(3, 4, $group_period_diff, 2);

    }
    else {
        $rep->AmountCol(1, 2, $group_op_sum, 2);
        $rep->AmountCol(2, 3, $group_dr_sum, 2);
        $rep->AmountCol(3, 4, $group_cr_sum, 2);
        $rep->AmountCol(4, 5, $group_period_diff, 2);
        $rep->AmountCol(5, 6, $group_bal_sum, 2);
    }


    $rep->row -= 4;
    $rep->Font();
    $rep->Line($rep->row);
    $rep->NewLine();
    $rep->NewLine();

}


