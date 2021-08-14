<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2/28/2019
 * Time: 1:28 PM
 */


$page_security = 'SA_SALESINVOICE';
$path_to_root = "..";
include_once($path_to_root . "/sales/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");
include_once($path_to_root . "/admin/db/shipping_db.inc");

if ($_GET['add_new'] == 1) {

    add_sub_customers();
    exit;

}


function add_sub_customers()
{

    $customer_id = $_POST['customer_id'];
    $sub_customer_name = $_POST['sub_cust_name'];
    $created_by = $_SESSION['wa_current_user']->user;

    if (empty($customer_id)) {
        echo json_encode(["status" => "FAIL", "msg" => "No Customer Error"]);
        exit();
    }


    $sql = "INSERT into 0_sub_customers (customer_id,name,created_by) 
             VALUES ($customer_id," . db_escape($sub_customer_name) . ",$created_by)";
    db_query($sql);

    $id = db_insert_id();

    echo json_encode(["status" => "SUCCESS","id"=>$id, "msg" => "New Sub Customer Added."]);

    exit();

}




