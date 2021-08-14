<?php
include('ERP/config_db.php');
$conn = mysqli_connect($db_connections[0]['host'],$db_connections[0]['dbuser'],$db_connections[0]['dbpassword']);
mysqli_select_db($conn,$db_connections[0]['dbname']);


$get_all_customers="select debtor_no from 0_debtors_master where debtor_no <>1 ";
$result=mysqli_query($conn,$get_all_customers);

$array_item=array("2"=>10,"3"=>30,"4"=>4,"6"=>10,"7"=>3,"42"=>3,"55"=>4,"60"=>10,"67"=>10);


while($debtor_no = mysqli_fetch_array($result))
{

    foreach($array_item as $key=>$value)
    {
        $s="select id from customer_discount_items where customer_id='".$debtor_no['debtor_no']."'
            AND item_id='".$key."' ";
        $res=mysqli_query($conn,$s);
        $data=mysqli_fetch_row($res);

            if(empty($data['id']))
            {
                 $q="INSERT INTO customer_discount_items (customer_id,item_id,customer_commission)
                     VALUES ('".$debtor_no['debtor_no']."','".$key."','".$value."')";

            }
            else
            {
                 $q="UPDATE customer_discount_items set customer_commission='".$value."' 
                     where customer_id='".$debtor_no['debtor_no']."' AND item_id='".$key."' ";

            }
            mysqli_query($conn,$q);

    }
}

?>