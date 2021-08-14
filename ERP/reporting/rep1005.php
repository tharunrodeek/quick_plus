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
// ----------------------------------------------------------------
$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

$canAccess = [
    'OWN' => user_check_access('SA_EMPANALYTIC'),
    'DEP' => user_check_access('SA_EMPANALYTICDEP'),
    'ALL' => user_check_access('SA_EMPANALYTICALL')
];

$page_security = in_array(true, array_values($canAccess), true) ? 'SA_ALLOW' : 'SA_DENIED';

//------------------------------------------------------------------

print_report($canAccess);

function get_report($from, $to, $show_locals, $show_type, $dim_id, $canAccess, $is_typing_commission)
{
    if (empty($from) || empty($to)) {
        display_error(_('Please provide a date'));
        exit;
    }

    $where = "";

    if($canAccess['DEP'] && !$canAccess['ALL']){
        $username = $_SESSION['wa_current_user']->username;
        $user = get_user_by_login($username);
        $dim_id = $user['dflt_dimension_id'];
    }

    if(!empty($dim_id))
        $where .= " AND dflt_dim1=$dim_id ";

    $sql = "select * from 0_stock_category WHERE 1=1 $where order by category_id";
    $result = db_query($sql,"Error");
    $th_array = ["Employee Name"];
    $sql_extra= [];

    $fdate = date2sql($from);
    $fdate_to = date2sql($to);
    $datediff = strtotime($fdate) - strtotime($fdate_to);
    $datediff =  abs(round($datediff / (60 * 60 * 24)));

    if($datediff > 31) {
        display_error("Date period should not exceed by 31 days");
        return false;
    }

    while ($myrow = db_fetch_assoc($result)) {
        array_push($th_array,$myrow['description']);
        $sql_extra[] = "SUM(IF(category_id = {$myrow['category_id']}, `count`, 0)) AS '{$myrow['category_id']}'";
    }

    $sql_extra = implode(",",$sql_extra);
    array_push($th_array,"TOTAL");
    table_header($th_array);


    $k = 0;
    $where = "";


    if ($show_locals == 1) {
        $where .= " AND d.is_local=1 ";
    }

    if (!$canAccess['ALL'] && !$canAccess['DEP']) {
        $where.=" and d.user_id='".$_SESSION['wa_current_user']->username."' ";
    }

    if (!empty($dim_id)) {
        $where .= " and d.dflt_dimension_id = $dim_id ";
    }

    $display_field = " sum(a.quantity) ";
    if($show_type == 'COMMISSION'){
        if($is_typing_commission) {
            $display_field = " 0 ";
        } else {
            $display_field = " SUM(IFNULL(a.user_commission,0)*a.quantity) ";
        }
    }

    $sql = (
    "SELECT 
            user_id,
            {$sql_extra}
        FROM 
            (
                SELECT 
                    e.tran_date,
                    c.category_id,
                    d.user_id,
                    c.description,
                    $display_field AS `count`
                FROM 0_debtor_trans_details a 
                    LEFT JOIN 0_stock_master b ON b.stock_id = a.stock_id 
                    LEFT JOIN 0_stock_category c ON c.category_id = b.category_id 
                    LEFT JOIN 0_users d ON d.id = a.created_by
                    LEFT JOIN 0_debtor_trans e ON e.trans_no = a.debtor_trans_no
                WHERE 
                    a.debtor_trans_type = 10
                    AND e.type = 10
                    AND e.tran_date >= '$fdate'
                    AND e.tran_date <= '$fdate_to'
                    AND (e.ov_amount + e.ov_gst + e.ov_freight + e.ov_freight_tax + e.ov_discount) <> 0
                    $where
                GROUP BY 
                    e.tran_date,
                    b.category_id,
                    a.created_by
                ORDER BY c.category_id
            ) AS mytable
        GROUP BY user_id"
    );




    return db_query($sql);
}


function fmt_payment_status($status)
{
    return [1 => 'Fully Paid', 2 => 'Not Paid', 3 => 'Partially Paid'][$status];
}


function get_category_for_print($cat_id,$fdate,$fdate_to)
{

    $from= date2sql($fdate);
    $to = date2sql($fdate_to);
    $sql_query="SELECT distinct c.category_id
                    FROM 0_debtor_trans_details a
                    LEFT JOIN 0_stock_master b ON b.stock_id = a.stock_id
                    LEFT JOIN 0_stock_category c ON c.category_id = b.category_id
                    LEFT JOIN 0_debtor_trans e ON e.trans_no = a.debtor_trans_no
                    WHERE a.debtor_trans_type = 10 AND e.type = 10 AND e.tran_date >= '$from' AND e.tran_date <= '$to' 
                    AND a.unit_price <> 0 AND c.category_id='".$cat_id."'
                    GROUP BY e.tran_date, b.category_id, a.created_by
                    ORDER BY c.category_id";
    $result_set=db_fetch(db_query($sql_query));

    return $result_set['category_id'];
}

//----------------------------------------------------------------------------------------------------

function print_report($canAccess)
{
    global $path_to_root, $systypes_array;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $show_locals = $_POST['PARAM_2'];
    $show_type = $_POST['PARAM_3'];
    $dim_id = $_POST['PARAM_4'];
    $comments = "";
    $destination = $_POST['DESTINATION'];

    $is_typing_commission = false;
    if($show_type == 'COMMISSION' && isset($dim_id) && $dim_id == DT_TYPING){
        $is_typing_commission = true;
    }

    if ($is_typing_commission) {
        $fdate = date2sql($from);
        $fdate_to = date2sql($to);

        $commissions = db_query(
            "SELECT 
                t1.user_id,
                SUM(IF(t1.commission >= 60, t1.commission * 2, t1.commission)) AS commission
            FROM
                (
                    SELECT
                        user.user_id,
                        trans.tran_date,
                        SUM(details.quantity) AS commission
                    FROM 
                        `0_debtor_trans_details` AS details
                        LEFT JOIN `0_debtor_trans` AS trans ON
                            trans.type = details.debtor_trans_type
                            AND trans.trans_no = details.debtor_trans_no
                        LEFT JOIN `0_users` AS user ON
                             user.id = details.created_by
                    WHERE
                        trans.type = 10
                        AND trans.tran_date >= '$fdate'
                        AND trans.tran_date <= '$fdate_to'
                        AND trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount <> 0
                    GROUP BY user.id, trans.tran_date
                ) AS t1
            GROUP BY t1.user_id"
        )->fetch_all(MYSQLI_ASSOC);

        $commissions = array_column($commissions, 'commission', 'user_id');
    }

    if (!$destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = "L";
    $dec = user_price_dec();

    $rep = new FrontReport(_('Employee Category Sales Report'), "EmployeeCategorySalesReport", "A4", 9, $orientation);
    $summary = _('Detailed Report');

    $params = array(0 => $comments,
        1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
        2 => array('text' => _('Type'), 'from' => $summary, 'to' => '')
    );

    $where = "";

    if(!empty($dim_id))
        $where .= " AND dflt_dim1=$dim_id ";

    $sql = "select * from 0_stock_category WHERE 1=1 $where order by category_id";
    $result = db_query($sql,"Error");
    $headers = ["Employee Name"];
    $cols = [];
    $cols[0] = 0;
    $cat_ids = [];
    $aligns = [];

    $cols_last = 0;


    while ($myrow = db_fetch_assoc($result)) {

        $res_allowed_category=get_category_for_print($myrow['category_id'],$from,$to);

        if($res_allowed_category!='')
        {
            array_push($cols,$cols_last+75);
            $cols_last = $cols_last+75;
            array_push($headers,$myrow['description']);
            array_push($aligns,'left');
            array_push($cat_ids,$myrow['category_id']);
        }

    }
    array_push($headers,'TOTAL');
    array_push($aligns,'left');
    array_push($cols,$cols_last+75);

    $transactions = get_report($from, $to, $show_locals, $show_type, $dim_id, $canAccess, $is_typing_commission);


    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);

    $rep->NewPage();


    $i = 0;
    $col_total = [];
    $col_total[0] =  "TOTAL";
    $user_total_sum = 0;
    $typing_commission_sum = 0;

    $total = "user_total";
    if ($is_typing_commission){
        $total = "typing_commission";
    }
    $total_sum = "{$total}_sum";

    while ($myrow = db_fetch_assoc($transactions)) {

        $rep->TextCol(0, 1, $myrow["user_id"]);
        $user_total = 0;
        for ($i = 0; $i < count($cat_ids); $i++) {
            if(!isset($col_total[$i+1])) $col_total[$i+1] = 0;
            $cat_id = $cat_ids[$i];
            $rep->TextCol($i+1, $i+1+1, $myrow[$cat_id]);
            $user_total += $myrow[$cat_id];
            $col_total[$i+1] += $myrow[$cat_id];
        }
        $typing_commission = isset($commissions[$myrow["user_id"]]) ? $commissions[$myrow["user_id"]] : 0;

        $rep->TextCol(count($cat_ids)+1, count($cat_ids)+2, $$total);

        $user_total_sum += $user_total;
        $typing_commission_sum += $typing_commission;

        $rep->NewLine();

    }
    array_push($col_total,$$total_sum);

    $rep->NewLine();
    $rep->Line($rep->row + $rep->lineHeight);

    for ($i = 0; $i < count($col_total); $i++) {
        $rep->TextCol($i, $i+1, $col_total[$i]);
    }

    hook_tax_report_done();

    $rep->End();
}


