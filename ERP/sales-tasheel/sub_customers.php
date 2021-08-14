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


if($_GET['reception_data']=='1')
{
get_reception_data();
}


if($_GET['PAYMENT_CARD']=='1')
{
  get_payment_card();
}

if($_GET['edit_payment_type']=='1')
{
    get_invoice_payment_edit_data();
}



function get_reception_data()
{
    global $SysPrefs;
 $token_id = $_POST['token_id'];

 $sql="select * from 0_axis_front_desk where token=".db_escape($token_id)." and DATE(created_at)='" . date2sql(Today()) . "'
       ORDER BY id DESC LIMIT 1 ";
 
       $result = db_query($sql);
        $row = db_fetch($result);
        if (db_num_rows($result) > 0)
        {
            $payment_type='';
            $payment_card='';
           if($row['payment_type']=='1')
           {

            
               $payment_type='CenterCard';
               $payment_card=$SysPrefs->prefs['default_e_dirham_account'];

           }

           if($row['payment_type']=='2')
           {
               $payment_type='CustomerCard';
               $payment_card='';
           }

            echo json_encode(["status" => "SUCCESS","payment_type"=>$payment_type,"invoice_payment_card"=>$payment_card]);
        }

}


function get_payment_card()
{
    global $SysPrefs;
  $invoice_type=$_POST['invoice_type_id'];
  $payment_card='';

   if($invoice_type=='CustomerCard')
   {
      $payment_card='';
   }
   else if($invoice_type=='CenterCard')
   {
      $payment_card=$SysPrefs->prefs['default_e_dirham_account'];
   }

   echo json_encode(["status" => "SUCCESS","displ_payment_card"=>$payment_card,"displ_invoice_type"=>$invoice_type]);
}


function get_invoice_payment_edit_data()
{
    $invoice_number=$_POST['edit_invoice_number'];

    $sql="select invoice_type from 0_debtor_trans where trans_no='".$invoice_number."' and type='10' ";
    $result = db_query($sql);
        $row = db_fetch($result);

        if (db_num_rows($result) > 0)
        {
            $payment_card='';
            if($row['invoice_type']=='CenterCard')
            {
                $qry="select govt_bank_account from 0_debtor_trans_details where debtor_trans_no='".$invoice_number."' and   debtor_trans_type='10' ";
                $result_qry = db_query($qry);
                $row_data = db_fetch($result_qry);
                if(db_num_rows($result)>0)
                {
                    $payment_card=$row_data['govt_bank_account'];
                }
            }


            echo json_encode(["status" => "SUCCESS","edit_invoice_card"=>$row['invoice_type']
                            ,"edit_pay_type"=>$payment_card]);

        }


}


