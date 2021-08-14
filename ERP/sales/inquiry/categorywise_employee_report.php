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

$user_role = $_SESSION['wa_current_user']->access;
$user_id = $_SESSION["wa_current_user"]->user;
$login_name = $_SESSION["wa_current_user"]->loginname;

$canManage = in_array($_SESSION['wa_current_user']->access, [2,18,37,49]);

/** If not a counter staff */
if ($canManage) {
    /** check if a user filter is applied and validate it */
    if (
        !isset($_POST['filter_user'])
        || !preg_match('/^[1-9][0-9]{0,15}$/', $_POST['filter_user'])
    ) {
        $_POST['filter_user'] = null;
    }
} else {
    $_POST['filter_user'] = $user_id;
}


$js="";

if (user_use_date_picker()) {
    $js .= get_js_date_picker();
}

page(trans($help_context = trans("Employee-Category-Sales")), false, false, "", $js);

if (get_post("search")) {
    $Ajax->activate("item_tbl");
}

start_form(false, false, $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);

start_table(TABLESTYLE_NOBORDER);

start_row();

date_cells(trans("Date FROM"), 'filter_date', trans('Filter by Date'),
    true, 0, 0, 0, null, false);

date_cells(trans("Date TO"), 'filter_date_to', trans('Filter by Date'),
    true, 0, 0, 0, null, false);

show_only_cells("Show Only", 'show_type', $_POST['show_type']);


end_row();
start_row();
if(in_array($_SESSION['wa_current_user']->access, [2,18,37]))
{
    dimensions_list_cells("Cost Center",'dimension_id',$_POST['dimension_id'],true,'All');
}
/** If not a counter staff */
// if ($canManage) {
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
<?php // }

/** If not a counter staff */
if ($canManage) {
    $users = db_query("SELECT id, user_id FROM 0_users")->fetch_all(MYSQLI_ASSOC); ?>
    <td><?= trans("User") ?> </td>
    <td>
        <select
                name="filter_user"
                id="filter_user">
            <option value="">-- all users --</option>
            <?php foreach($users as $u): ?>
                <option value="<?= $u["id"] ?>"><?= $u["user_id"] ?></option>
            <?php endforeach; ?>
        </select>
    </td>
<?php }

check_cells(trans("Show only Locals"),'show_locals');

submit_cells("search", trans("Search"), "", trans("Search items"), "default");
end_row();


end_table();

end_form();

div_start("item_tbl");
start_table(TABLESTYLE);

$dim_id = $_POST['dimension_id'];
$cat_id = $_POST['filter_category'];



// if($_SESSION['wa_current_user']->access == 18){
//     //If logged in user is counter supervisor, show only their cost center categories
//     //

//     $username = $_SESSION['wa_current_user']->username;

//     $user = get_user_by_login($username);

//     $dim_id = $user['dflt_dimension_id'];

// }



$where = "";

if(!empty($dim_id))
    $where .= " AND dflt_dim1=$dim_id ";

if(!empty($cat_id))
    $where .= " AND category_id=$cat_id ";

$sql = "select * from 0_stock_category WHERE 1=1 $where order by category_id";
$result = db_query($sql,"Error");
$th_array = [trans("Employee Name")];
$sql_extra= [];
$fdate = date2sql($_POST['filter_date']);
$fdate_to = date2sql($_POST['filter_date_to']);
$datediff = strtotime($fdate) - strtotime($fdate_to);
$datediff =  abs(round($datediff / (60 * 60 * 24)));

if($datediff > 31) {
    display_error("Date period should not exceed by 31 days");
    return false;
}

$cat_ids = [];
while ($myrow = db_fetch_assoc($result)) {
    array_push($th_array,$myrow['description']);
    $sql_extra[] = "SUM( CASE when category_id=".$myrow['category_id']." and tran_date>='$fdate' and tran_date <='$fdate_to'  then count ELSE 0 END) as '".$myrow['category_id']."'";
    array_push($cat_ids,$myrow['category_id']);
}

$sql_extra = implode(",",$sql_extra);
array_push($th_array,trans("TOTAL"));
table_header($th_array);

$k = 0;
$where = "";

$show_locals = $_POST['show_locals'];

if($show_locals == 1) {
    $where .= " AND d.is_local=1 ";
}

if(!empty($_POST['filter_user'])) {
    $where .= " AND d.id = {$_POST['filter_user']}";
}

$show_type = $_POST['show_type'];
$display_field = " sum(a.quantity) ";

if($show_type == 'COMMISSION') {
    $display_field = " SUM(IFNULL(a.user_commission,0)*a.quantity) ";
}

if(!empty($dim_id)) {
    $where .= " AND d.dflt_dimension_id = $dim_id";
}


$sql = (
"SELECT 
        user_id,
        $sql_extra
    FROM (
        SELECT 
            e.tran_date,
            c.category_id,
            d.real_name user_id,
            c.description,
            $display_field AS count
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
            AND a.unit_price <> 0
            $where 
        GROUP BY e.tran_date, b.category_id, a.created_by 
        ORDER BY c.category_id
    ) AS mytable 
    GROUP BY user_id "
);









//echo $sql;
$result = db_query($sql,"Error");

$i = 0;

$col_total = [];
$col_total[0] =  trans("TOTAL");
$user_total_sum = 0;
while ($myrow = db_fetch_assoc($result)) {
    alt_table_row_color($k);
    label_cell($myrow["user_id"],"style='text-align:center'");
    $user_total = 0;
    for ($i = 0; $i < count($cat_ids); $i++) {

        if(!isset($col_total[$i+1])) $col_total[$i+1] = 0;

        $cat_id = $cat_ids[$i];
        label_cell($myrow[$cat_id],"style='text-align:center'");

        $user_total += $myrow[$cat_id];
        $col_total[$i+1] += $myrow[$cat_id];
    }
    $user_total_sum += $user_total;
    label_cell($user_total,"style='text-align:center; font-weight:bold'");
    end_row();
}
array_push($col_total,$user_total_sum);


alt_table_row_color($k);
for ($i = 0; $i < count($col_total); $i++) {
    label_cell($col_total[$i],"style='text-align:center; font-weight:bold'");
}

end_row();





set_focus('description');

end_table(1);

div_end();


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
hidden("REP_ID", "1005");
hidden("PARAM_0", $_POST['filter_date']);
hidden("PARAM_1", $_POST['filter_date_to']);
hidden("PARAM_2", $_POST['show_locals']);
hidden("PARAM_3", $_POST['show_type']);
hidden("PARAM_4", $_POST['dimension_id']);
hidden("PARAM_5", $_POST['filter_user']);
hidden("PARAM_6", $_POST['filter_category']);


echo array_selector("DESTINATION", null, [ trans("Export to EXCEL") ,trans("Export to PDF")]);
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