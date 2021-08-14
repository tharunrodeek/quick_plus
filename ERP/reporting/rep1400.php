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

function prepare_query($show_type,$salesmen_id,$sql_extra,$fdate,$fdate_to,$filter_cust=0)
{
    $where = "";
    $display_field = " sum(a.quantity) ";
    if($show_type == 'COMMISSION')
        $display_field = " SUM(IFNULL(g.customer_commission,0)*a.quantity) ";

    $where = "";
    if(!empty($filter_cust)) {
        $where .= " AND f.debtor_no = $filter_cust";
    }
    if($salesmen_id > 1) {
        $where .= " and f.salesman_id = $salesmen_id ";
    }
    $where .= " AND c.category_id NOT IN (54,59,62,65,66,63) ";
    $sql = "select user_id,debtor_no,customer_name,".$sql_extra.",displ_custid  from 
    (select e.tran_date,c.category_id,d.user_id,f.name as customer_name,f.debtor_no,c.description,
    $display_field as count,g.customer_id as displ_custid from 0_debtor_trans_details a 
    left join 0_stock_master b on b.stock_id=a.stock_id 
    left join 0_stock_category c on c.category_id=b.category_id 
    left join 0_users d on d.id=a.created_by 
    LEFT JOIN 0_debtor_trans e on e.trans_no=a.debtor_trans_no 
    LEFT JOIN customer_discount_items g ON (g.item_id = c.category_id) AND (e.debtor_no = g.customer_id)
    right join 0_debtors_master f on f.debtor_no=e.debtor_no 
    WHERE (a.debtor_trans_type=10 AND e.type=10 AND e.tran_date>='$fdate' and e.tran_date <= '$fdate_to') $where  
    group by e.tran_date,b.category_id,f.debtor_no order by c.category_id ) as mytable group by debtor_no ";


    return db_query($sql);
    //return $sql;
}

function prepare_query_sub($show_type,$salesmen_id,$sql_extra,$fdate,$fdate_to,$customer_id=null)
{
    $display_field = " sum(a.quantity) ";
    if($show_type == 'COMMISSION')
        $display_field = " SUM(IFNULL(g.customer_commission,0)*a.quantity) ";
    $where = "";
    if($salesmen_id > 1) {
        $where .= " and f.salesman_id = $salesmen_id ";
    }
    if(!empty($customer_id) && $customer_id!='')
    {
        $where .= " AND f.debtor_no= $customer_id";
    }
    $where .= " AND c.category_id NOT IN (54,59,62,65,66,63) ";
    $sql = "select user_id,debtor_no,customer_name,".$sql_extra.",displ_custid  from 
            (select e.tran_date,c.category_id,d.user_id,f.name as customer_name,f.debtor_no,c.description,
            $display_field as count,g.customer_id as displ_custid from 0_debtor_trans_details a 
            left join 0_stock_master b on b.stock_id=a.stock_id 
            left join 0_stock_category c on c.category_id=b.category_id 
            left join 0_users d on d.id=a.created_by 
            LEFT JOIN 0_debtor_trans e on e.trans_no=a.debtor_trans_no 
            LEFT JOIN customer_discount_items g ON (g.item_id = c.category_id) AND (e.debtor_no = g.customer_id)
            right join 0_debtors_master f on f.debtor_no=e.debtor_no 
            WHERE (a.debtor_trans_type=10 AND e.type=10 AND e.tran_date>='$fdate' and e.tran_date <= '$fdate_to') $where 
            group by e.tran_date,b.category_id,f.debtor_no order by c.category_id ) as mytable group by debtor_no ";

   return db_query($sql,"Error");
   //return $sql;

}

/*function Hide_Total_Zero_Rows($cat_id,$cutomer_id,$from,$to)
{
    $sql=" SELECT sum(a.quantity)  as count from 0_debtor_trans_details a 
            left join 0_stock_master b on b.stock_id=a.stock_id 
            left join 0_stock_category c on c.category_id=b.category_id 
            LEFT JOIN 0_debtor_trans e on e.trans_no=a.debtor_trans_no 
            right join 0_debtors_master f on f.debtor_no=e.debtor_no 
            WHERE (a.debtor_trans_type=10 AND e.type=10 AND e.tran_date>='".$from."' 
            and e.tran_date <= '".$to."')  
			AND f.debtor_no='".$cutomer_id."' AND c.category_id='".$cat_id."' ";

    return db_fetch(db_query($sql));
}*/

function get_transaction_each_customer($show_type,$salesmen_id,$fdate,$fdate_to,$customer_id,$cat_ids)
{
    $fdate = date2sql($fdate);
    $fdate_to = date2sql($fdate_to);
    if($customer_id!='')
    {
        $sql = "select * from 0_stock_category where category_id NOT IN (54,59,62,65,66,63)";
        if(!empty($filter_category))
        {
            $sql .= " AND category_id=$filter_category ";
        }
        $sql.=" order by category_id";
        $result = db_query($sql,"Error");
        $sql_extra= [];

        while ($myrow = db_fetch_assoc($result)) {
            $sql_extra[] = "SUM( CASE when category_id=".$myrow['category_id']." and tran_date>='$fdate' and tran_date <='$fdate_to' then count ELSE 0 END) as '".$myrow['category_id']."'";
           // array_push($cat_ids,$myrow['category_id']);
        }
        $sql_extra = implode(",",$sql_extra);


        $result_sub=prepare_query_sub($show_type,$salesmen_id,$sql_extra,$fdate,$fdate_to,$customer_id);
        $customer_each_tran=[];
        $customer_commission_array=[];
        while ($myrow_res = db_fetch_assoc($result_sub)) {
            $user_total = 0;
            $user_commission=0;
            for ($i = 0; $i < count($cat_ids); $i++) {
                $cat_id = $cat_ids[$i];
                $user_total += $myrow_res[$cat_id];

                $get_customer_commsion="SELECT customer_commission FROM customer_discount_items WHERE item_id='".$cat_id."' 
                                and customer_id='".$myrow_res['displ_custid']."'  ";
                $customer_commission=db_fetch(db_query($get_customer_commsion));

                $user_commission+=$myrow_res[$cat_id]*$customer_commission['customer_commission'];

                array_push($customer_each_tran,$myrow_res[$cat_id]);
                array_push($customer_commission_array,$user_commission);
            }
        }
      return array("CUST_TRAN_TOT"=>array_sum($customer_each_tran),"CUSTOMER_COMMISSION_TOT"=>array_sum($customer_commission_array));
 //return $result_sub;
    }
}


function get_report($from, $to,$show_type,$salesmen_id=null,$filter_cust=null,$filter_category=null)
{
    if (empty($from) || empty($to)) {
        display_error(_('Please provide a date'));
        exit;
    }

    $sql = "select * from 0_stock_category where category_id NOT IN (54,59,62,65,66,63)";
    if(!empty($filter_category))
    {
        $sql .= " AND category_id=$filter_category ";
    }
    $sql.=" order by category_id";
    $result = db_query($sql,"Error");
    $th_array = ["Customer Name"];
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
        //$zero_or_not=Hide_Total_Zero_Rows($myrow['category_id'],);
        array_push($th_array,$myrow['description']);
        $sql_extra[] = "SUM( CASE when category_id=".$myrow['category_id']." and tran_date>='$fdate' and tran_date <='$fdate_to' then count ELSE 0 END) as '".$myrow['category_id']."'";
        array_push($cat_ids,$myrow['category_id']);

    }

    $sql_extra = implode(",",$sql_extra);
    array_push($th_array,"TOTAL");
    table_header($th_array);


    $k = 0;
    $result=prepare_query($show_type,$salesmen_id,$sql_extra,$fdate,$fdate_to,$filter_cust);
    return $result;
}


function fmt_payment_status($status)
{
    return [1 => 'Fully Paid', 2 => 'Not Paid', 3 => 'Partially Paid'][$status];
}

//----------------------------------------------------------------------------------------------------

function print_report()
{
    global $path_to_root, $systypes_array;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $show_type = $_POST['PARAM_2'];
    $salesmen_id = $_POST['PARAM_3'];
    $filter_category = $_POST['PARAM_4'];
    $filter_cust = $_POST['PARAM_5'];
    $comments = "";
    $destination = $_POST['DESTINATION'];


    if (!$destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = "L";
    $dec = user_price_dec();

    $rep = new FrontReport(_('Customer Category Sales Report'), "CustomerCategorySalesReport", "A4", 9, $orientation);
    $summary = _('Detailed Report');

    $params = array(0 => $comments,
        1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
        2 => array('text' => _('Type'), 'from' => $summary, 'to' => '')
    );

    $sql = "select * from 0_stock_category where category_id NOT IN (54,59,62,65,66,63) ";
    if(!empty($filter_category))
    {
        $sql .= " AND category_id=$filter_category ";
    }
    $sql.=" order by category_id";
    $result = db_query($sql,"Error");



    $headers = ["Customer Name"];
    $cols = [];
    $cols[0] = 0;
    $cat_ids = [];
    $aligns = [];


    $cols_last = 0;

    while ($myrow = db_fetch_assoc($result)) {
        array_push($cols,$cols_last+75);
        $cols_last = $cols_last+75;
        array_push($headers,$myrow['description']);
        array_push($aligns,'left');
        array_push($cat_ids,$myrow['category_id']);
    }
    array_push($headers,'TOTAL');
    array_push($headers,trans("COMMISSION TOTAL"));
    array_push($aligns,'left');
    array_push($cols,$cols_last+75);



    $transactions = get_report($from, $to,$show_type,$salesmen_id,$filter_cust,$filter_category);


    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);

    $rep->NewPage();


    $i = 0;
    $col_total = [];
    $commission_total=[];
    $col_total[0] =  "TOTAL";
    $user_total_sum = 0;
    $user_total_commission=0;
    $filter_condition_flg='';
    $filter_customer_tran_total='';
    $filter_customer_comm_total=0;
    while ($myrow = db_fetch_assoc($transactions)) {


      $return_result=get_transaction_each_customer($show_type,$salesmen_id,$from,$to,$myrow['displ_custid'],$cat_ids);
        if($_POST['PARAM_6']=='1')
        {
            $filter_condition_flg=10;
            $filter_customer_tran_total=$return_result['CUST_TRAN_TOT'];
            $filter_customer_comm_total=$return_result['CUSTOMER_COMMISSION_TOT'];
        }
        else
        {
            $filter_condition_flg=0;
            $filter_customer_comm_total=$return_result['CUSTOMER_COMMISSION_TOT'];
        }

    if($filter_customer_tran_total>=$filter_condition_flg  && $filter_customer_comm_total>0)
     {
            $rep->TextCol(0, 1,  $myrow["customer_name"] );
            $user_total = 0;
            $user_commission=0;
            $user_total_commission=0;
            for ($i = 0; $i < count($cat_ids); $i++) {
                if(!isset($col_total[$i+1])) $col_total[$i+1] = 0;
                $cat_id = $cat_ids[$i];
                $rep->TextCol($i+1, $i+1+1, $myrow[$cat_id]);

                $get_customer_commsion="SELECT customer_commission FROM customer_discount_items WHERE item_id='".$cat_id."' 
                                and customer_id='".$myrow['displ_custid']."'  ";
                $customer_commission=db_fetch(db_query($get_customer_commsion));


                $user_total += $myrow[$cat_id];
                $col_total[$i+1] += $myrow[$cat_id];
                $user_commission+=$myrow[$cat_id]*$customer_commission['customer_commission'];
            }
            $user_total_sum += $user_total;
            $user_total_commission+=$user_commission;
            $rep->TextCol(count($cat_ids)+1, count($cat_ids)+2,$user_total);
            $rep->TextCol(count($cat_ids)+2, count($cat_ids)+2,$user_total_commission);
            $rep->NewLine();
            array_push($commission_total,$user_total_commission);
   }





    }
    array_push($col_total,$user_total_sum);

    $rep->NewLine();
    $rep->Line($rep->row + $rep->lineHeight);

    for ($i = 0; $i < count($col_total); $i++) {
        $rep->TextCol($i, $i+1, $col_total[$i]);
    }
    $rep->TextCol($i, $i+1, array_sum($commission_total));

    hook_tax_report_done();

    $rep->End();
}


