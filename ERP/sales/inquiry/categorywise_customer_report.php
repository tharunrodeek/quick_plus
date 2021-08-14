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
/**********************************************************************
 * Page for searching item list and select it to item selection
 * in pages that have the item dropdown lists.
 * Author: bogeyman2007 from Discussion Forum. Modified by Joe Hunt
 ***********************************************************************/
//$page_security = "SA_ITEM";
$page_security = "SA_SALESORDER";
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/inventory/includes/db/items_db.inc");

?>

<?php

$js="";

if (user_use_date_picker()) {
    $js .= get_js_date_picker();
}

page(trans($help_context = "Customer-Category-Sales"), false, false, "", $js);

if (get_post("search")) {
    $Ajax->activate("item_tbl");
}

start_form(false, false, $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);

start_table(TABLESTYLE_NOBORDER);

start_row();

//date_cells("Date", 'filter_date', trans('Filter by Date'),
//    true, 0, 0, 0, null, true);


date_cells(trans("Date FROM"), 'filter_date', trans('Filter by Date'),
    true, 0, 0, 0, null, true);

date_cells(trans("Date TO"), 'filter_date_to', trans('Filter by Date'),
    true, 0, 0, 0, null, true);


sales_persons_list_cells("Sales Men",'salesmen_id',$_POST['salesmen_id'],'--All--'); ?>
<?php
$categories = db_query("SELECT category_id, description FROM 0_stock_category")->fetch_all(MYSQLI_ASSOC); ?>
<td><?= trans("Category") ?> </td>
<td>
    <select
            name="filter_category"
            id="filter_category">
        <option value="">-- all categories --</option>
        <?php foreach($categories as $c): ?>
            <option value="<?= $c["category_id"] ?>"><?= $c["description"] ?></option>
        <?php endforeach; ?>
    </select>
</td>
<?php
$cutomers = db_query("SELECT debtor_no, name FROM 0_debtors_master")->fetch_all(MYSQLI_ASSOC); ?>
<td><?= trans("Customer") ?> </td>
<td>
    <select
            name="filter_cust"
            id="filter_cust">
        <option value="">-- all customers --</option>
        <?php foreach($cutomers as $cust): ?>
            <option value="<?= $cust["debtor_no"] ?>"><?= $cust["name"] ?></option>
        <?php endforeach; ?>
    </select>
</td>
<td><?= trans("Total Greaterthan 10") ?></td>
<td><input type="checkbox" id="chk_total_limit" name="chk_total_limit"/></td>

<?php


//show_only_cells("Show Only", 'show_type', $_POST['show_type']);


submit_cells("search", trans("Search"), "", trans("Search items"), "default");

end_row();

end_table();

end_form();

div_start("item_tbl");
start_table(TABLESTYLE);

$cat_id = $_POST['filter_category'];
$where = "";






$sql = "select * from 0_stock_category where category_id NOT IN (54,59,62,65,66,63) ";
if(!empty($cat_id))
{
    $sql .= " AND category_id=$cat_id ";
}
$sql.=" order by category_id";

$result = db_query($sql,"Error");

$th_array = ["Customer Name"];

$sql_extra= [];



$fdate = date2sql($_POST['filter_date']);
$fdate_to = date2sql($_POST['filter_date_to']);
$datediff = strtotime($fdate) - strtotime($fdate_to);
$datediff =  abs(round($datediff / (60 * 60 * 24)));

$salesmen_id = $_POST['salesmen_id'];

if($datediff > 31) {
    display_error("Date period should not exceed by 31 days");
    return false;
}

//display_error(print_r($_POST['filter_date'],true)); die;

//$fdate = date2sql($_POST['filter_date']);
$show_type = $_POST['show_type'];

$cat_ids = [];
while ($myrow = db_fetch_assoc($result)) {
    array_push($th_array,$myrow['description']);
    $sql_extra[] = "SUM( CASE when category_id=".$myrow['category_id']." and tran_date>='$fdate' and tran_date <='$fdate_to' then count ELSE 0 END) as '".$myrow['category_id']."'";
    array_push($cat_ids,$myrow['category_id']);
}

$sql_extra = implode(",",$sql_extra);

array_push($th_array,trans("TOTAL"));
array_push($th_array,trans("COMMISSION TOTAL"));
//table_header($th_array);

$th = array("", trans("Item Code"), trans("Description"), trans("Category"));
table_header($th_array);

$k = 0;
//$name = $_GET["client_id"];





 //display_error(print_r($sql,true)); die;


function prepare_query($show_type,$salesmen_id,$sql_extra,$fdate,$fdate_to,$customer_id=0)
{
    $display_field = " sum(a.quantity) ";
    if($show_type == 'COMMISSION')
        $display_field = " SUM(IFNULL(g.customer_commission,0)*a.quantity) ";
    $where = "";
    if(!empty($_POST['filter_cust'])) {
        $where .= " AND f.debtor_no = {$_POST['filter_cust']}";
    }
    if($salesmen_id > 1) {
        $where .= " and f.salesman_id = $salesmen_id ";
    }
    if($customer_id!=0)
    {
        $where .= " and g.customer_id= $customer_id";
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



$result=prepare_query($show_type,$salesmen_id,$sql_extra,$fdate,$fdate_to);




$col_total = [];
$commission_total=[];
$col_total[0] =  trans("TOTAL");

$user_total_sum = 0;
$user_total_commission=0;
$filter_condition_flg='';
$filter_customer_tran_total='';
$filter_customer_comm_total=0;
$i = 0;
while ($myrow = db_fetch_assoc($result)) {
    alt_table_row_color($k);

    $return_result=get_transaction_each_customer($show_type,$salesmen_id,$sql_extra,$fdate,$fdate_to,$myrow['displ_custid'],$cat_ids);
    if($_POST['chk_total_limit']=='1')
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


    if($filter_customer_tran_total>=$filter_condition_flg && $filter_customer_comm_total>0)
    {
        label_cell($myrow["customer_name"],"style='text-align:center'");
        $user_total = 0;
        $user_commission=0;
        $user_total_commission=0;
        for ($i = 0; $i < count($cat_ids); $i++) {
            $cat_id = $cat_ids[$i];
            label_cell($myrow[$cat_id],"style='text-align:center'");
            $get_customer_commsion="SELECT customer_commission FROM customer_discount_items WHERE item_id='".$cat_id."' 
                                and customer_id='".$myrow['displ_custid']."'  ";
            $customer_commission=db_fetch(db_query($get_customer_commsion));

            $user_total += $myrow[$cat_id];
            $user_commission+=$myrow[$cat_id]*$customer_commission['customer_commission'];
            $col_total[$i+1] += $myrow[$cat_id];

        }

        $user_total_sum += $user_total;
        $user_total_commission+=$user_commission;

        label_cell($user_total,"style='text-align:center; font-weight:bold'");
        label_cell($user_total_commission,"style='text-align:center; font-weight:bold'");
        array_push($commission_total,$user_total_commission);
    }

    end_row();

}

array_push($col_total,$user_total_sum);


 //display_error(print_r(array_sum($commission_total),true));

alt_table_row_color($k);
for ($i = 0; $i < count($col_total); $i++) {
    label_cell($col_total[$i],"style='text-align:center; font-weight:bold'");

  //  label_cell($commission_total[$i],"style='text-align:center; font-weight:bold'");
}

label_cell(array_sum($commission_total),"style='text-align:center; font-weight:bold'");


end_row();


function get_transaction_each_customer($show_type,$salesmen_id,$sql_extra,$fdate,$fdate_to,$customer_id,$cat_ids)
{
    if($customer_id!='')
    {
        $result=prepare_query_sub($show_type,$salesmen_id,$sql_extra,$fdate,$fdate_to,$customer_id);
        $customer_each_tran=[];
        $customer_commission_array=[];
        while ($myrow = db_fetch_assoc($result)) {
            $user_total = 0;
            $user_commission=0;
            for ($i = 0; $i < count($cat_ids); $i++) {
                $cat_id = $cat_ids[$i];
                $user_total += $myrow[$cat_id];

                $get_customer_commsion="SELECT customer_commission FROM customer_discount_items WHERE item_id='".$cat_id."' 
                                and customer_id='".$myrow['displ_custid']."'  ";
                $customer_commission=db_fetch(db_query($get_customer_commsion));

                $user_commission+=$myrow[$cat_id]*$customer_commission['customer_commission'];

                array_push($customer_each_tran,$myrow[$cat_id]);
                array_push($customer_commission_array,$user_commission);
            }
        }
        return array("CUST_TRAN_TOT"=>array_sum($customer_each_tran),"CUSTOMER_COMMISSION_TOT"=>array_sum($customer_commission_array));
    }
}




function show_only_cells($label,$name,$selected_id=null)
{
    echo "<td>$label</td>
            <td>" . array_selector(
            $name, $selected_id,
            [
                'COUNT' => 'COUNT',
                'COMMISSION' => 'COMMISSION'
            ]
        ) . "</td>";

}



set_focus('description');

end_table(1);

div_end();




/** EXPORT */
$Ajax->activate("PARAM_0");
$Ajax->activate("PARAM_1");
$Ajax->activate("PARAM_2");
$Ajax->activate("PARAM_3");
$Ajax->activate("PARAM_4");
$Ajax->activate("PARAM_5");
$Ajax->activate("PARAM_6");


start_form(false, false, $path_to_root . "/reporting/reports_main.php", "export_from");
hidden("Class", "6");
hidden("REP_ID", "1400");
hidden("PARAM_0", $_POST['filter_date']);
hidden("PARAM_1", $_POST['filter_date_to']);
hidden("PARAM_2", $_POST['show_type']);
hidden("PARAM_3", $_POST['salesmen_id']);
hidden("PARAM_4", $_POST['filter_category']);
hidden("PARAM_5", $_POST['filter_cust']);
hidden("PARAM_6", $_POST['chk_total_limit']);


echo array_selector("DESTINATION", null, [trans("Export to EXCEL")]);
br(2);


submit_cells('Rep1005', trans("EXPORT"), '', "Export to PDF or EXCEL", 'default');

end_form();

/** END -- EXPORT */

end_page();

?>

<style>
    form[name="export_from"] {
        text-align: center;
    }
</style>

<script type="text/javascript">
    const hideCol = () => {
        const columns = $(".tablestyle > tbody > tr:first > td").length;
        for(let i=0;i<columns;i++){
            let list = $(".tablestyle tr td:nth-child("+i+")");
            let sum = 0;
            for(let l = 1; l<list.length; l++){
                sum += parseInt($(list[l]).text());
            }
            if(sum == 0)
            {
                list.hide();
            }

        }


    }

    $(document).ready(function(){
        setInterval(function(){
            hideCol();
        }, 100);
    });

</script>