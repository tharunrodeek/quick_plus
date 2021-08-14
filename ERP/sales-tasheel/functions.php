<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 6/6/2018
 * Time: 4:52 PM
 */
$page_security = 'SA_SALESINVOICE';
$path_to_root = "..";
include_once($path_to_root . "/sales-tasheel/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales-tasheel/includes/sales_db.inc");
include_once($path_to_root . "/sales-tasheel/includes/sales_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");
include_once($path_to_root . "/admin/db/shipping_db.inc");


if($_GET['method'] == 'getOtherFeeInfo') {

//    $sales_type

}



//$barcode = $_GET['barcode'];
//
////error_reporting(E_ALL);
//
//$sql = "SELECT debtor_no FROM ".TB_PREF."debtor_trans WHERE barcode=".db_escape($barcode)." LIMIT 1";
//$result = db_query($sql, "The debtor transaction could not be queried");
//$row = db_fetch($result);
//if(db_num_rows($result) > 0) {
//    echo $row['debtor_no'];
//    exit;
//}
//else {
//    echo "false";
//    exit;
//}