<?php

date_default_timezone_set('Asia/Dubai');
$current_username = $_SESSION['wa_current_user']->username;

function get_invoice_range($from, $to,$trans_no)
{
    global $SysPrefs;
    $ref = ($SysPrefs->print_invoice_no() == 1 ? "trans_no" : "reference");
    $sql = "SELECT trans.trans_no, trans.reference,trans.created_by  
		FROM " . TB_PREF . "debtor_trans trans 
			LEFT JOIN " . TB_PREF . "voided voided ON trans.type=voided.type AND trans.trans_no=voided.id
		WHERE trans.type=" . $trans_no
        . " AND ISNULL(voided.id)"
        . " AND trans.trans_no BETWEEN " . db_escape($from) . " AND " . db_escape($to)
        . " ORDER BY trans.tran_date, trans.$ref";
 //echo $sql;
    return db_query($sql, "Cant retrieve invoice range");
}

function get_do_number($from)
{
    $sqlr="select reference from 0_debtor_trans where trans_no='".$from."' and type='13'";
    $res=db_fetch(db_query($sqlr));
    return $res;
}

function get_payment_invoice_terms($from)
{
    $sqlr="select memo_ from 0_comments where id='".$from."' and type='10'";
    $res=db_fetch(db_query($sqlr));
    return $res;
}


function get_created_by($user_id)
{
    $sql="Select real_name FROM 0_users WHERE id='".$user_id."'";
    $res=db_query($sql);
    return db_fetch($res);
}

function get_delivery_numebrs($from)
{
    $get_del_sql="Select delivery_ids FROM 0_debtor_trans WHERE trans_no='".$from."' and type='10'";

    $delivery_data=db_fetch(db_query($get_del_sql));

    if($delivery_data[0]!='')
    {
        $sqlr=" 
               select d.reference,d.tran_date
FROM 0_debtor_trans_details as a 
INNER JOIN 0_debtor_trans AS d ON d.trans_no=a.debtor_trans_no
where a.src_id IN 
(select b.id
from 0_debtor_trans as a
INNER join 0_debtor_trans_details as b ON a.trans_no=b.debtor_trans_no
where a.trans_no in (".$delivery_data[0].")
and type='13'
) 
and a.quantity<>'0' AND d.`type`='13'
               
               
               
               
               ";
        $res=db_query($sqlr);
        $delvery_refernces='';
        $delivery_dates='';
        while ($myrow_d = db_fetch($res)) {
            $delvery_refernces.=$myrow_d[0].'+';
            $delivery_dates.=date('d-m-Y',strtotime($myrow_d[1])).'+';
        }

        $array=array(rtrim($delvery_refernces,"+"),rtrim($delivery_dates,"+"));
        return $array;
    }


}



$from = $_GET['PARAM_0'];
$to = $_GET['PARAM_1'];

 if (!$from || !$to) return;

$fno = explode("-", $from);
$tno = explode("-", $to);
$from = min($fno[0], $tno[0]);
$to = max($fno[0], $tno[0]);
//$dec = user_price_dec();
$range = get_invoice_range($from, $to,$fno[1]);
$row = db_fetch($range);
$row['trans_no'] = $row[0];
$myrow = get_customer_trans($row['trans_no'],$fno[1]);
$sign = 1;
$result = get_customer_invoice_details($fno[1], $row['trans_no']);
$SubTotal = 0;
$DiscountedAmountTotal = 0;
$get_num=get_do_number($from);
$payemnt_terms=get_payment_invoice_terms($from);

$delivery_references=get_delivery_numebrs($from);

if($row[2]!='')
{
    $create_by=get_created_by($row[2]);
}

$line_item_tr = "";
$total_tax = 0;
$total_before_tax = 0;
$i = 1;
$created_by = "";

$disc_amt = 0;
$total_qty_deliverynote='0';

$created_at_date_time = null;

$customer_info = get_customer($myrow['debtor_no']);

while ($myrow2 = db_fetch($result)) {

    if($fno[1]=='10')
    {
        $item_description = $myrow2['description'];
        $total_price=$myrow2['quantity']*$myrow2['unit_price'];
        $total_with_tax=$total_price+$myrow2['unit_tax']*$myrow2['quantity'];

        $line_item_tr .=
            "<tr class='item'>
            <td class='center'>" . $i . "</td>
            <td class='arabic' style='float:left;'>" . $item_description . "</td>";
        if($myrow['display_weight_or_not']=='1')
        {
            $line_item_tr .= "<td class='center'>KG</td>
                              <td class='right'>".$myrow2['kg_per']."</td>
                              <td class='right'>".$myrow2['tot_kg_available']."</td>";
        }
        else
        {
            $line_item_tr .= "<td class='center'>".$myrow2['units']."</td>
                              <td class='right'>".$myrow2['quantity']."</td>
                              <td class='right'>".number_format($myrow2['unit_price'],2)."</td>";
        }
        $line_item_tr .=
            "
            <td class='right' >". number_format($total_price,2)."</td>
            <td class='right'>".$myrow2['unit_tax']*$myrow2['quantity']."</td>
            <!--<td class='right'></td>-->
            <td class='right'>".number_format($total_with_tax,2)."</td>

        </tr>";

        $i++;
        $total_tax=$myrow2['unit_tax']*$myrow2['quantity']+$total_tax;
        $total_before_tax =$total_price+$total_before_tax;
    }
    else if($fno[1]=='13')
    {
        $item_description = $myrow2['description'];
        $line_item_tr .=
            "<tr class='item'>
            <td class='center'>" . $i . "</td>
            <td class='arabic' style='float:left;'>" . $item_description . "</td>
            <td class='center'>".$myrow2['units']."</td>
            <td class='right'>".$myrow2['quantity']."</td>
           

        </tr>";

        $i++;
        $total_qty_deliverynote=$myrow2['quantity']+$total_qty_deliverynote;

    }





}
$size=db_fetch($result);

if(sizeof($size)<15)
{
    $number_to_loop = 15-sizeof($size);
}

if($fno[1]=='10')
{
    for($i=0;$i<$number_to_loop;$i++)
    {
        $line_item_tr .=
            "<tr class='item' >
            <td class='center' style='height:20px;'></td>
            <td class='arabic' style='float:left;'></td>
            <td class='center'></td>
            <td class='right'></td>
            <td class='right'></td>
            <td class='right' ></td>
            <td class='right'></td>
            <!--<td class='right'></td>-->
            <td class='right'></td>

        </tr>";
    }

}
else if($fno[1]=='13')
{
    for($i=0;$i<$number_to_loop;$i++)
    {
        $line_item_tr .=
            "<tr class='item' >
            <td class='center' style='height:20px;'></td>
            <td class='arabic' style='float:left;'></td>
            <td class='center'></td>
            <td class='right'></td>
        </tr>";
    }
}


?>
<?php  if($fno[1]=='10'){ ?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Print</title>

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
                </table>
            </td>
        </tr>
    </table>


    <table class="tbl-heading" >

        <tr>
             <td style="text-align: center;width:30%;font-size: 9pt;"><p>Invoice No. : <?= $myrow['reference'] ?></p></td>
            <td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <img src="images/tax-invoice-title.jpg" style="width:100%; max-width: 250px;">
            </td>
            <td style="text-align: center;width:30%;font-size: 9pt;">TRN. 100201440300003</td>
        </tr>

    </table>
   <table>
       <tr>
           <td style="width: 20%;">L.P.O No.</td><td>:</td><td><?= $myrow['lpo_no'] ?></td>
           <td style="width: 30%;"></td>
           <td style="width: 20%;">L.P.O Date.</td><td>:</td><td><?= date('d-m-Y',strtotime($myrow['lpo_date'])) ?></td>
       </tr>
       <tr>
           <td style="width: 20%;">Customer TRN</td><td>:</td><td><?= $customer_info['tax_id']; ?></td>
           <td style="width: 30%;"></td>
           <td style="width: 20%;">D.O No.</td><td>:</td><td><?= $delivery_references[0]; ?></td>
       </tr>
       <tr>
           <td style="width: 20%;">Customer Address</td><td>:</td><td><?= $customer_info['address']; ?></td>
           <td style="width: 30%;"></td>
           <td style="width: 20%;">Delivery Date</td><td>:</td><td><?= $delivery_references[1]; ?></td>
       </tr>
<?php if($myrow['payment_type']!='')
      {
          $pay_types=array('1'=>'Cash','2'=>'Cheque','3'=>'Transfer');
          ?>
        <!--  <tr>
              <td style="width: 20%;">Payment Type</td><td>:</td><td><?/*= $pay_types[$myrow['payment_type']]; */?></td>
          </tr>-->
        <!--  <tr>
               <td style="width: 20%;">Cheque No</td><td>:</td><td><?/*= $myrow['cheq_no']; */?></td>
               <td style="width: 30%;"></td>
               <td style="width: 20%;">Cheque Date</td><td>:</td><td><?/*= $myrow['cheq_date']; */?></td>
          </tr>-->
 <?php } ?>

 <tr><td></td></tr>
   </table>

    <div style="border: 1px solid black;width:100%;padding:2px;font-size: 12px;border-radius: 4px;">Customer Name :  <?= $customer_info['name']; ?> </div>
    <div style="height:10px;"></div>
    <div style="height: 375px">
        <table class="invoice-items">
            <tr class="heading">
                <td>
                    Sl. No<br><span class="arabic">الرقم</span>
                </td>
                <td>
                    Description<br><span class="arabic">وصف</span>
                </td>
                <?php
                    if($myrow['display_weight_or_not']=='1')
                    {
                ?>
                <td>
                    Unit<br><span class="arabic">وحدة</span>
                </td>
                <td>
                  Price Per KG<br><span class="arabic">الكمية</span>
                </td>
                <td>
                   Total KG<br><span class="arabic">سعر الوحده</span>
                </td>
               <?php }
               else
                { ?>
                    <td>
                        Unit<br><span class="arabic">وحدة</span>
                    </td>
                    <td>
                        Qty<br><span class="arabic">الكمية</span>
                    </td>
                    <td>
                        Unit price<br><span class="arabic">سعر الوحده</span>
                    </td>
               <?php
               }
               ?>
                <td>
                    Total before TAX<br><span class="arabic">المجموع دون الضريبة</span>
                </td>
                <td>
                    Tax<br><span class="arabic">ضريبة</span>
                </td>
              <!--  <td>
                    VAT Amount<br><span class="arabic">قيمة الضريبة</span>
                </td>-->
                <td>
                    Total With TAX<br><span class="arabic">المجموع مع الضرائب</span>
                </td>
            </tr>
            <?= $line_item_tr ?>
            <tr >
                <td style="height:50px;"></td>
            </tr>
            <tr class="total">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="3"><b>Total Before TAX المجموع دون الضريبة</b></td>
                <td class="right">
                      <?= number_format($total_before_tax,2); ?>
                </td>
            </tr>



            <tr class="total">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="3"><b>Total TAX مجموع الضريبة</b></td>
                <td class="right">
                  <?=  number_format($total_tax,2) ?>
                </td>
            </tr>

            <tr class="total">
                <td colspan="2">
                </td>
                <td></td>
                <td></td>
                <td colspan="3"><b>Grand Total المبلغ الإجمالي</b></td>
                <td class="right">
                   <?= number_format($total_before_tax+$total_tax,2); ?>
                </td>
            </tr>
        </table>
    </div>
<div style="padding:2px;font-size: 9pt;">
    <label style="font-weight:500;">Terms & Conditions : <label style="font-size:5pt;"><?= $payemnt_terms[0]; ?></label></label>
</div>
    <div style="padding:2px;font-size: 9pt;margin-top:10%;">
         <table>
             <tr>
                 <td><label style="font-weight:500;">Prepared By : <label style="font-size:5pt;text-decoration:underline;"><?= $create_by[0]; ?></label></label></td>
                 <td><label style="font-weight:500;">Reviewed By : <label style="font-size:5pt;">___________________________</label></label></td>
                 <td>&nbsp;</td>
                 <td><label style="font-weight:500;">Approved By : <label style="font-size:5pt;">_____________________________</label></label></td>
             </tr>
         </table>
    </div>


    <div style="padding:2px;font-size: 9pt;margin-top:3%;">
        <table>
            <tr>
                <td><label style="font-weight:500;">Printed By : <label style="font-size:5pt;text-decoration:underline;"><?= $current_username; ?></label></label></td>
            </tr>
            <tr>
                <td><label style="font-weight:500;">Printed On : <label style="font-size:5pt;"><?= date('m-d-Y h:i:s'); ?></label></label></td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>

<?php } else if($fno[1]=='13') {?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Print</title>

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
                </table>
            </td>
        </tr>
    </table>


    <table class="tbl-heading" >

        <tr>
            <td style="text-align: center;width:30%;font-size: 9pt;"><p></p></td>
            <td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <img src="images/Deliver-Note.jpg" style="width:100%; max-width: 250px;">
            </td>
            <td style="text-align: center;width:30%;font-size: 9pt;"></td>
        </tr>

    </table>
    <table>
        <tr>
            <td style="width: 20%;"></td><td></td><td></td>
            <td style="width: 30%;"></td>
            <td style="width: 20%;">Date</td><td>:</td><td><?= date('d-m-Y',strtotime($myrow['due_date'])) ?></td>
        </tr>
        <tr>
            <td style="width: 20%;"></td><td></td><td></td>
            <td style="width: 30%;"></td>
            <td style="width: 20%;">L.P.O No.</td><td>:</td><td><?= $myrow['lpo_no']; ?></td>
        </tr>

        <tr><td></td></tr>
    </table>

    <div style="border: 1px solid black;width:100%;padding:2px;font-size: 12px;border-radius: 4px;">Mr/Mrs :  <?= $customer_info['name']; ?> </div>
    <div style="height:10px;"></div>
    <div style="height: 375px;">
        <table class="invoice-items"  style="width:100%;">
            <tr class="heading">
                <td>
                    Sl. No<br><span class="arabic">الرقم</span>
                </td>
                <td >
                    Description<br><span class="arabic">وصف</span>
                </td>
                <td >
                    Unit<br><span class="arabic">وحدة</span>
                </td>
                <td >
                    Qty<br><span class="arabic">الكمية</span>
                </td>

            </tr>
            <?= $line_item_tr ?>
            <tr >
                <td style="height:50px;"></td>
            </tr>
            <tr class="total">
                <td></td>
                <td></td>
                <td ><b>Total Qty إجمالي الكمية</b></td>
                <td class="right">
                    <?= number_format($total_qty_deliverynote,2); ?>
                </td>
            </tr>


        </table>
    </div>

    <div style="padding:2px;font-size: 9pt;margin-top:10%;">
        <table>
            <tr>
                <td><label style="font-weight:500;">Prepared By : <label style="font-size:5pt;text-decoration:underline;"><?= $create_by[0]; ?></label></label></td>
                <td><label style="font-weight:500;">Delivered By : <label style="font-size:5pt;">_______________________</label></label></td>
                <td>&nbsp;</td>

                <td><label style="font-weight:500;">Received By : <label style="font-size:5pt;">________________________</label></label></td>
            </tr>
        </table>
    </div>
    <div style="padding:2px;font-size: 9pt;margin-top:3%;">
        <table>
            <tr>
                <td><label style="font-weight:500;">Printed By : <label style="font-size:5pt;text-decoration:underline;"><?= $current_username; ?></label></label></td>
            </tr>
            <tr>
                <td><label style="font-weight:500;">Printed On : <label style="font-size:5pt;"><?= date('m-d-Y h:i:s'); ?></label></label></td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>

<?php } ?>