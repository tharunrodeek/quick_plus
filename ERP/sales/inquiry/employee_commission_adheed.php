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

$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/inventory/includes/db/items_db.inc");

$js="";

if (user_use_date_picker()) {
    $js .= get_js_date_picker();
}

$canAccess = [
    'OWN' => user_check_access('SA_EMPCOMMAAD'),
    'DEP' => user_check_access('SA_EMPCOMMAADDEP'),
    'ALL' => user_check_access('SA_EMPCOMMAADALL')
];

$page_security = in_array(true, $canAccess, true) 
    ? (
        !$canAccess['ALL'] && !in_array($_SESSION['wa_current_user']->default_cost_center, [DT_ADHEED, DT_ADHEED_OTH])
            ? 'SA_DENIED'
            : 'SA_ALLOW'
    ) : 'SA_DENIED';


page(trans($help_context = trans("Employee-Category-Sales")), false, false, "", $js);

if (list_updated('month')) {
    $Ajax->activate("item_tbl");
}

if (!isset($_POST['month']) || (int)$_POST['month'] < 1 || (int)$_POST['month'] > 12){
    $_POST['month'] = date('j') > 25 ? date('n') + 1 : date('n');
}

$months = [
    '1' => trans("January"),
    '2' => trans("February"),
    '3' => trans("March"),
    '4' => trans("April"),
    '5' => trans("May"),
    '6' => trans("June"),
    '7' => trans("July"),
    '8' => trans("August"),
    '9' => trans("September"),
    '10' => trans("October"),
    '11' => trans("November"),
    '12' => trans("December")
];

start_form(false, false, $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);

start_table(TABLESTYLE_NOBORDER, "", '2', '0', 'w-50 mb-3');

start_row();

array_selector_cells('Month', 'month', $_POST['month'], $months, ["select_submit" => true]);

label_cell('&nbsp;');
label_cell('&nbsp;');

check_cells(trans("Show only Locals"),'show_locals');

submit_cells("search", trans("Search"), "", trans("Search items"), "default");

end_row();

end_table();

end_form();

$dim_ids = [DT_ADHEED, DT_ADHEED_OTH];
$dim_ids = implode(",", $dim_ids);
$user_price_dec = user_price_dec();
$fdate = new DateTime();
$fdate = $fdate->setDate(date('Y'), $_POST['month'] - 1, 26)->format(MYSQL_DATE_FORMAT);
$fdate_to = new DateTime();
$fdate_to = $fdate_to->setDate(date('Y'), $_POST['month'], 25)->format(MYSQL_DATE_FORMAT);

$categories = db_query(
    "SELECT
        category_id,
        `description`
    FROM 
        0_stock_category
    WHERE dflt_dim1 IN ({$dim_ids})
    ORDER BY category_id"
)->fetch_all(MYSQLI_ASSOC);
$categories = array_column($categories, 'description', 'category_id');
$category_ids = array_keys($categories);

$getAddittionalFilters = function ($category_ids, $canAccess) {
    $where = "";

    if($_POST['show_locals'] == 1) {
        $where .= " AND user.is_local = 1 ";
    }
    
    if(!$canAccess['DEP'] && !$canAccess['ALL'])
    {
        $where .= " AND user.id = '{$_SESSION['wa_current_user']->user}'";
    }
    
    if(!empty($category_ids)){
        $where .= " AND item.category_id IN (" . implode(",", $category_ids) . ")";
    }

    return $where;
};

$selects  = [];
foreach($category_ids as $category_id){
    $selects[]  = "SUM( IF(item.category_id = {$category_id}, ROUND(details.unit_price * details.quantity, {$user_price_dec}), 0) ) AS '{$category_id}'";
}
$selects = implode(", ", $selects);

$sql = (
    "SELECT
        GROUP_CONCAT(DISTINCT(user.user_id)) AS user_id,
        MAX(user.real_name) AS real_name,
        {$selects}
    FROM `0_debtor_trans_details` details
    LEFT JOIN `0_stock_master` item ON item.stock_id = details.stock_id
    LEFT JOIN `0_users` user ON user.id = details.created_by 
    LEFT JOIN `0_debtor_trans` trans ON trans.trans_no = details.debtor_trans_no AND trans.type = details.debtor_trans_type
    LEFT JOIN `0_voided` voided ON voided.id = details.debtor_trans_no AND voided.type = details.debtor_trans_type
    WHERE
        details.debtor_trans_type = 10
        AND trans.tran_date >= '{$fdate}'
        AND trans.tran_date <= '{$fdate_to}'
        AND ISNULL(voided.date_)
        AND user.dflt_dimension_id IN ({$dim_ids})
        {$getAddittionalFilters($category_ids, $canAccess)}
    GROUP BY user.employee_id"
);

$result = db_query($sql,"Error");

/**
 * 8% Commission if total of two categories(Legal Services[43], AL Adheed[79]) exceeds 15000
 * 10% on Any transaction of category Legal Agreements[44] 
 */
$legal_services = '43';
$al_adheed  = '79';
$legal_agreements = '44';

$totals = [
    '8_percent'         => 0,
    '10_percent'        => 0,
    'total_comm'        => 0
];
foreach($category_ids as $category_id){
    $totals[$category_id] = 0;
}

$headers = [
    trans("User ID"),
    trans("Employee Name")
];
if($canAccess['DEP'] || $canAccess['ALL']){
    foreach($categories as $category_name){
        $headers[] = $category_name;
    }
    $headers[] = trans("Commission (8%)");
    $headers[] = trans("Commission (10%)");
}
$headers[] = trans("Total Commission");


div_start("item_tbl");
start_table(TABLESTYLE);
table_header($headers);

$k = 0;
while ($row = db_fetch_assoc($result)) {
    $comm_8_percent = $row[$legal_services] + $row[$al_adheed] >= 15000 
        ? round2(0.08 * ($row[$legal_services] + $row[$al_adheed]), $user_price_dec)
        : 0;
    $totals['8_percent'] += $comm_8_percent;

    $comm_10_percent = $row[$legal_agreements] > 0
        ? round2(0.1 * $row[$legal_agreements], $user_price_dec)
        : 0;
    $totals['10_percent'] += $comm_10_percent;

    $total_commission = $comm_8_percent + $comm_10_percent;
    $totals['total_comm'] += $total_commission;

    alt_table_row_color($k);
    label_cell($row["user_id"]);
    label_cell($row["real_name"]);
    if($canAccess['DEP'] || $canAccess['ALL']) {
        foreach($category_ids as $category_id){
            $totals[$category_id] += $row[$category_id];
            label_cell(price_format($row[$category_id]), 'class="text-right"');
        }
        label_cell(price_format($comm_8_percent), 'class="text-right"');
        label_cell(price_format($comm_10_percent), 'class="text-right"');
    }
    label_cell(price_format($total_commission), 'class="text-right"');
    end_row();
}

if($canAccess['DEP'] || $canAccess['ALL']){
    alt_table_row_color($k);
    echo '<td colspan="2" class="text-center">Total</td>';
    foreach($category_ids as $category_id){
        label_cell(price_format($totals[$category_id]), 'class="text-right"');
    }
    label_cell(price_format($totals['8_percent']), 'class="text-right"');
    label_cell(price_format($totals['10_percent']), 'class="text-right"');
    label_cell(price_format($totals['total_comm']), 'class="text-right"');
    end_row();
}

end_table(1);
div_end();

end_form();
/** END -- EXPORT */
end_page();

?>