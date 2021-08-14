<?php
/**********************************************************************
 * Copyright (C) FrontAccounting, LLC.
 * Released under the terms of the GNU General Public License, GPL,
 * as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 ***********************************************************************/
$page_security = 'SA_TAXREP';
// ----------------------------------------------------------------
$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//------------------------------------------------------------------

print_report();

function get_report()
{
    $sql = get_the_sql();
    return db_query($sql);
}


function get_the_sql()
{
    $customer_id = get_post('PARAM_0');
    $date_from = date2sql(get_post('PARAM_1'));
    $date_to = date2sql(get_post('PARAM_2'));

    $where = "";

    if(!empty($customer_id))
        $where .= " AND dt.debtor_no=$customer_id";

    if(!empty($date_from))
        $where .= " AND dt.tran_date >= ".db_escape($date_from);

    if(!empty($date_to))
        $where .= " AND dt.tran_date <= ".db_escape($date_to);

    $sql = "SELECT dm.name AS customer, ROUND(SUM(ov_amount+ov_gst),2) AS invoice_total, 
            ROUND(SUM(dt.alloc),2) AS total_allocated,
            ROUND(SUM(ov_amount+ov_gst)- SUM(dt.alloc),2) AS balance
            FROM 0_debtor_trans dt
            LEFT JOIN 0_debtors_master dm ON dm.debtor_no=dt.debtor_no
            WHERE dt.`type`=10 $where 
            GROUP BY dt.debtor_no order by balance desc";

    return $sql;

}


//----------------------------------------------------------------------------------------------------

function print_report()
{
    global $path_to_root, $systypes_array;

    $debtor_no = $_POST['PARAM_0'];
    $comments = "";
    $destination = $_POST['DESTINATION'];

    $date_from = get_post('PARAM_1');
    $date_to = get_post('PARAM_2');

    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = "P";
    $dec = user_price_dec();

    $rep = new FrontReport(_('Customer Balance Summary'), "Customer_Balance_Summary", "A4", 9, $orientation);
    $summary = _('Summary Report');

    $params = array(0 => $comments,
        1 => array('text' => _('Period'), 'from' => $date_from, 'to' => $date_to),
        2 => array('text' => _('Type'), 'from' => $summary, 'to' => '')
    );

    $cols = array(0, 200, 300, 420, 510, 610);
    $headers = array(_('Customer'), "Total Invoice Amount", "Total Invoice Payment", _('Balance'));
    $aligns = array('left', 'center', 'center', 'center', 'center');
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);

    $rep->NewPage();
    $transactions = get_report();

    $total_invoice_sum = 0;
    $total_allocated_sum = 0;
    $total_balance_sum = 0;

    while ($trans = db_fetch($transactions)) {


        $total_invoice_sum += $trans['invoice_total'];
        $total_allocated_sum+= $trans['total_allocated'];
        $total_balance_sum+= $trans['balance'];

        $rep->TextCol(0, 1, $trans['customer'],0,0,0,0,0,1);
        $rep->AmountCol(1, 2, $trans['invoice_total'], $dec);
        $rep->AmountCol(2, 3, $trans['total_allocated'], $dec);
        $rep->AmountCol(3, 4, $trans['balance'], $dec);
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
    $rep->TextCol(0, 1, _("Total"));
    $rep->AmountCol(1, 2, $total_invoice_sum, $dec);
    $rep->AmountCol(2, 3, $total_allocated_sum, $dec);
    $rep->AmountCol(3, 4, $total_balance_sum, $dec);
    $rep->Line($rep->row - 5);
    $rep->Font();


    hook_tax_report_done();

    $rep->End();
}


