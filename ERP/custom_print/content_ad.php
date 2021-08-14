<?php
date_default_timezone_set('Asia/Dubai');
$current_username = $_SESSION['wa_current_user']->username;


$adjustment_items = get_stock_adjustment_items($_GET["PARAM"]);


$line_item_tr = "";
$line_total='';
$tot = 0;
$loc_name='';
$ref='';
$date='';
$adjusted_by='';
while ($adjustment = db_fetch($adjustment_items))
{
    $line_total=$adjustment['qty']*$adjustment['standard_cost'];
    $line_item_tr .="<tr>
                         <td style='float:left;border: 1px solid black;'>" .$adjustment['stock_id']. "</td>
                         <td style='float:left;border: 1px solid black;'>" .$adjustment['description']. "</td>
                         <td style='float:left;border: 1px solid black;'>" .$adjustment['qty']. "</td>
                         <td style='float:left;border: 1px solid black;'>" .$adjustment['units']. "</td>
                         <td style='float:left;border: 1px solid black;'>" .number_format($adjustment['standard_cost'],2). "</td>
                         <td style='float:left;border: 1px solid black;'>" .number_format($line_total,2). "</td>
                     </tr>";

    $tot += $line_total;

    $loc_name=$adjustment['location_name'];
    $ref=$adjustment['reference'];
    $date=sql2date($adjustment['tran_date']);
    $adjusted_by=$adjustment['adjusted_by'];

}


$created_by='';
if($adjusted_by!='' && $adjusted_by!='0')
{
    $qry="select real_name from 0_users where id='".$adjusted_by."'";
    $result_uname = db_query($qry, "Cannot retreive a supplier transaction");
    $user_data = db_fetch($result_uname);
    $created_by=$user_data['real_name'];
}
else
{
    $created_by='N/A';
}



$fno[1]='1';

function numberTowords($num)
{
    $ones = array(
        0 =>"ZERO",
        1 => "ONE",
        2 => "TWO",
        3 => "THREE",
        4 => "FOUR",
        5 => "FIVE",
        6 => "SIX",
        7 => "SEVEN",
        8 => "EIGHT",
        9 => "NINE",
        10 => "TEN",
        11 => "ELEVEN",
        12 => "TWELVE",
        13 => "THIRTEEN",
        14 => "FOURTEEN",
        15 => "FIFTEEN",
        16 => "SIXTEEN",
        17 => "SEVENTEEN",
        18 => "EIGHTEEN",
        19 => "NINETEEN",
        "014" => "FOURTEEN"
    );
    $tens = array(
        0 => "ZERO",
        1 => "TEN",
        2 => "TWENTY",
        3 => "THIRTY",
        4 => "FORTY",
        5 => "FIFTY",
        6 => "SIXTY",
        7 => "SEVENTY",
        8 => "EIGHTY",
        9 => "NINETY"
    );
    $hundreds = array(
        "HUNDRED",
        "THOUSAND",
        "MILLION",
        "BILLION",
        "TRILLION",
        "QUARDRILLION"
    );
    $num = number_format($num,2,".",",");
    $num_arr = explode(".",$num);
    $wholenum = $num_arr[0];
    $decnum = $num_arr[1];
    $whole_arr = array_reverse(explode(",",$wholenum));
    krsort($whole_arr,1);
    $rettxt = "";
    foreach($whole_arr as $key => $i){

        while(substr($i,0,1)=="0")
            $i=substr($i,1,5);
        if($i < 20){
            /* echo "getting:".$i; */
            $rettxt .= $ones[$i];
        }elseif($i < 100){
            if(substr($i,0,1)!="0")  $rettxt .= $tens[substr($i,0,1)];
            if(substr($i,1,1)!="0") $rettxt .= " ".$ones[substr($i,1,1)];
        }else{
            if(substr($i,0,1)!="0") $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0];
            if(substr($i,1,1)!="0")$rettxt .= " ".$tens[substr($i,1,1)];
            if(substr($i,2,1)!="0")$rettxt .= " ".$ones[substr($i,2,1)];
        }
        if($key > 0){
            $rettxt .= " ".$hundreds[$key]." ";
        }
    }
    if($decnum > 0){
        $rettxt .= " and ";
        if($decnum < 20){
            $rettxt .= $ones[$decnum];
        }elseif($decnum < 100){
            $rettxt .= $tens[substr($decnum,0,1)];
            $rettxt .= " ".$ones[substr($decnum,1,1)];
        }
    }
    return $rettxt;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Print</title>
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            /* padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            */
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
           /* padding: 5px;*/
            vertical-align: top;
        }

        /*.invoice-box table tr td:nth-child(2) {*/
        /*text-align: right;*/
        /*}*/

        .invoice-box table tr.top table td {
            /*padding-bottom: 20px;*/
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            /*line-height: 45px;*/
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            /*padding-bottom: 20px;*/
        }

        .invoice-box table tr.item td{
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }


        #footer {
            clear: both;
            position: fixed;
            height: 100%;
            margin-top: 8%;
            width: 90%;
            margin-left: 30px;

        }
.dotted
{
    border-bottom: 1px dotted;
}

        .font-style
        {
            font-size:9pt;
        }

    </style>

</head>

<body>

<div class="invoice-box arabic">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="4">
                <table>
                    <tr>
                        <td class="title" style="text-align: center">
                            <img src="images/header-top_orange.jpg" style="width:100%;">
                        </td>
                    </tr>
                    <tr class="top">
                        <td colspan="4">
                            <table>
                                <tr>
                                    <td class="title" style="text-align: center;">
                                        <?php if($_GET['type']=='1') { ?>
                                            <img src="images/inventory_adj.jpg" style="width:100%; max-width: 250px;">
                                        <?php } ?>
                                        <?php if($_GET['type']=='0') { ?>
                                            <img src="images/payment-voucher.jpg" style="width:100%; max-width: 250px; ">
                                        <?php } ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


    <div class="head-info" style=" width: 100%; margin-left: 0px; ">

      <table>
            <tr>
                <td style="font-weight: bold;">At Location :</td>
                <td style="text-align: left"><?= $loc_name; ?></td>
                <td></td>
                <td style="font-weight: bold;">Reference :</td>
                <td style="text-align: left"><?= $ref; ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Date :</td>
                <td style="text-align: left"><?= $date; ?></td>
            </tr>
        </table>
    </div>

    <div style="height:10px;"></div>
    <div style="height: 375px;">
        <table class="invoice-items"  style="width:100%;border-collapse: collapse;">
            <tr class="heading">
                <td style="border: 1px solid black;width:20%;">
                    Itemcode<br>
                </td>
                <td style="border: 1px solid black;">
                    Description<br>
                </td>
                <td style="border: 1px solid black;">
                    Qunatity<br>
                </td>
                <td style="border: 1px solid black;">
                    Unit<br>
                </td>
                <td style="border: 1px solid black;">
                    Unitcost<br>
                </td>
                <td style="border: 1px solid black;">
                    LineTotal<br>
                </td>
            </tr>
           <?= $line_item_tr ?>

            <tr >
                <td colspan="5" style="border: 1px solid black;"><?php echo numberTowords(str_replace("-","",$tot)); ?></td>

                <td style="border: 1px solid black;text-align: left;">AED <?php echo number_format(str_replace("-","",$tot),2); ?></td>
            </tr>



        </table>
        <div style="margin-left:75%;margin-top:2%;">
            <!--<table>
                <tr>
                    <td>Sub Total : <?/*= $display_sub_tot; */?></td>
                </tr>
                <tr>
                    <td>Tax(5%) : <?/*= $tax_total_display; */?></td>
                </tr>
                <tr>
                    <td>Amount Total : <?/*= $display_total; */?></td>
                </tr>
            </table>-->
        </div>

<div style="height:70px;"></div>
       <table>
            <tr>
                <td><label style="font-weight:500;">Prepared by : <label style="font-size:5pt;text-decoration:underline;"><?= $created_by; ?></label></label></td>
                    <td><label style="font-weight:500;">Reviewed By : <label style="font-size:5pt;">_______________________</label></label></td>
                    <td>&nbsp;</td>
                    <td><label style="font-weight:500;">Approved By : <label style="font-size:5pt;">________________________</label></label></td>
            </tr>
        </table>
        <?php if($fno[1]=='1'): ?>
        <div style="height:50px;border-bottom:1px solid #87CEFA;"></div>

       <!-- <table>
            <tr ><td style="height:20px;"></td></tr>
            <tr>
                <td><label style="font-weight:500;">Received by &nbsp;&nbsp; :<label style="font-size:5pt;">_______________________</label></label></td>
            </tr>
            <tr><td style="height:20px;"></td></tr>
            <tr>
                <td><label style="font-weight:500;">Received Date :<label style="font-size:5pt;">_______________________</label></label></td>
            </tr>
            <tr><td style="height:20px;"></td></tr>
            <tr>
                <td><label style="font-weight:500;">Signature &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:<label style="font-size:5pt;">_______________________</label></label></td>
            </tr>
        </table>-->
        <?php endif; ?>
    </div>

    <div style="padding:2px;font-size: 9pt;margin-top:15%;">
         <table style="border-collapse: collapse;">
            <tr>
                <td style="background-color: #b0e0e6;width:12%;border: 1px solid #87CEFA;"><label style="font-weight:500;">Printed By : <td><label style="font-size:5pt;"><?= $current_username; ?></label></td></label></td>
                <td style="background-color: #b0e0e6;width:12%;border: 1px solid #87CEFA;"><label style="font-weight:500;">Printed On : <td><label style="font-size:5pt;"><?= date('d-m-Y h:i:s'); ?></label></td></label></td>
            </tr>

        </table>
    </div>


</div>
</body>
</html>
