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

page(trans($help_context = "Employee-Category-Sales"), false, false, "", $js);

if (get_post("search")) {
    $Ajax->activate("item_tbl");
}

start_form(false, false, $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);

start_table(TABLESTYLE_NOBORDER);

start_row();

date_cells("Date", 'filter_date', trans('Filter by Date'),
    true, 0, 0, 0, null, true);

submit_cells("search", trans("Search"), "", trans("Search items"), "default");

end_row();

end_table();

end_form();

div_start("item_tbl");
start_table(TABLESTYLE);


$sql = "select * from 0_stock_category order by category_id";

$result = db_query($sql,"Error");

$th_array = ["Employee Name"];

$sql_extra= [];

//display_error(print_r($_POST['filter_date'],true)); die;

$fdate = date2sql($_POST['filter_date']);

$cat_ids = [];
while ($myrow = db_fetch_assoc($result)) {
    array_push($th_array,$myrow['description']);
    $sql_extra[] = "SUM( CASE when category_id=".$myrow['category_id']." and tran_date='$fdate' then count ELSE 0 END) as '".$myrow['category_id']."'";
    array_push($cat_ids,$myrow['category_id']);
}

$sql_extra = implode(",",$sql_extra);

$th = array("", trans("Item Code"), trans("Description"), trans("Category"));
table_header($th_array);

$k = 0;
$name = $_GET["client_id"];


$where = "";


$sql = "select user_id,".$sql_extra."  from 

(select e.tran_date,c.category_id,d.user_id,c.description,sum(a.quantity) as count from 0_debtor_trans_details a 
left join 0_stock_master b on b.stock_id=a.stock_id 
left join 0_stock_category c on c.category_id=b.category_id 

left join 0_users d on d.id=a.created_by 


LEFT JOIN 0_debtor_trans e on e.trans_no=a.debtor_trans_no


where a.debtor_trans_type=10 and e.type=10
 group by e.tran_date,b.category_id,a.created_by order by c.category_id) as mytable group by user_id ";



//display_error(print_r($sql,true)); die;


$result = db_query($sql,"Error");

$i = 0;
while ($myrow = db_fetch_assoc($result)) {
    alt_table_row_color($k);
    label_cell($myrow["user_id"],"style='text-align:center'");
    for ($i = 0; $i < count($cat_ids); $i++) {
        $cat_id = $cat_ids[$i];
        label_cell($myrow[$cat_id],"style='text-align:center'");
    }
    end_row();

}




set_focus('description');

end_table(1);

div_end();
end_page(true);