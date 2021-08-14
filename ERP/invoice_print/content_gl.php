<?php

date_default_timezone_set('Asia/Dubai');
$current_username = $_SESSION['wa_current_user']->username;

function get_subledger_name($sub_acc_code)
{
    $sql = "Select * from 0_sub_ledgers where code = ".db_escape($sub_acc_code);
    $sub_lgr = db_fetch(db_query($sql));
    return $sub_lgr['name'];
}

function get_username($user_id)
{
    $sql="Select real_name FROM 0_users WHERE id='".$user_id."'";
    $res=db_query($sql);
    $data=db_fetch($res);
    return $data[0];
}

$from = $_GET['PARAM'];

$fno = explode("-", $from);
$from = $fno[1];
$to = $fno[0];
//$dec = user_price_dec();
$result = get_gl_trans($from,$to);

$sign = 1;




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
$result = get_gl_trans($from, $to);
$attach='';
$refernce_no='';
$voucher_date='';
$created_by='';
$memo='';
$dr_total = 0;
$cr_total = 0;
while ($myrow2 = db_fetch($result)) {

    $refernce_no=$myrow2['reference'];
    $voucher_date=$myrow2["doc_date"];
    // if($fno[1]=='10')
    // {
    $counterpartyname = get_subaccount_name($myrow2["account"], $myrow2["person_id"]);
    $sub_ledger_name = get_subledger_name($myrow2['axispro_subledger_code']);

    $dr_amount = abs($myrow2['amount']>0?$myrow2['amount']:0.00);
    $cr_amount = abs($myrow2['amount']<0?$myrow2['amount']:0.00);




    if(!empty($sub_ledger_name))
    {
        $attach=' - '.$sub_ledger_name;
    }
    else
    {
        $attach='';
    }

    if ($myrow2['amount'] > 0 )
    {
        $debit = $myrow2['amount'];
        $credit='';
    }
    else
    {
        $credit = -$myrow2['amount'];
        $debit='';
    }

    $accoun= $myrow2['account'].' - '.$myrow2['account_name'] . ($counterpartyname ? ': '.$counterpartyname : '').$attach;
    $line_item_tr .=
        "<tr class='item'>
         
            <td class='arabic' style='float:left;'>" .$accoun. "</td>
           <td class='right' >".$myrow2['memo_']."</td>";

    $line_item_tr .=
        "
             
            <td class='right'>".$debit."</td>
            <td class='right'>".$credit."</td>

        </tr>";

    $i++;

    $dr_total += $dr_amount;
    $cr_total += $cr_amount;

    //}

}

 $line_item_tr .="<tr class='item' style=\"border: 1px solid black;\">
                
                <td></td>
                <td></td>
                <td style=\"border-top: 2px solid black !important;font-weight:bold;\" class=\"right\">".number_format2($dr_total,2)."</td>
<td style=\"border-top: 2px solid black !important;font-weight:bold;\" class=\"right\">".number_format2($cr_total,2)."</td>
</tr>";


$paidto_rcvd_to_label = $myrow['voucher_type'] == "PV" ? "Paid To:" : "Received From:";
if($fno[1]=='0') {
  $pv_rv_label = "J.V.No:";
}
if($fno[1]=='1') {
$pv_rv_label = "P.V.No:";
$paidto_rcvd_to_label="Paid To:";
}
if($fno[1]=='2') {
  $pv_rv_label = "R.V.No:";
  $paidto_rcvd_to_label="Received From:";
}

$person_types=array('0'=>'Miscellaneous','2'=>'Customer','3'=>'Supplier');
$payment_types=array('1'=>'Cash','2'=>'Cheque','3'=>'Transfer');

$person_type='';
$recived_from='';
$remarks='';
$chck_no='';
$chk_date='';
$amount='';
$payment_type='';

$person_id = get_counterparty_name($from,$to);

if($from=='2')
{
    $sql_voucher_details = "Select * from 0_vouchers where voucher_type='RV' and trans_no='".$to."'  ";
    $res_data = db_fetch(db_query($sql_voucher_details));

    $person_type=$person_types[$res_data['person_type_id']];
    $recived_from='';
    $remarks='';
    $chck_no=$res_data['chq_no'];

    if($res_data['chq_date']=='1970-01-01')
    {
        $chk_date='0000-00-00';
    }

    if($res_data['chq_date']!='0000-00-00')
    {
        $chk_date=date('m-d-Y',strtotime($res_data['chq_date']));
    }



    $amount=abs($res_data['amount']);
    $payment_type=$payment_types[$res_data['payment_type']];

    if(empty($person_id))
    {
        $person_id=$res_data['person_id'];
    }

    $created_by=get_username($res_data["created_by"]);
}
else
{
    $sql_journal = "Select * from 0_journal where `type`='".$from."' and trans_no='".$to."' ";
    $jour_data = db_fetch(db_query($sql_journal));

    $sql_jl = "Select * from 0_gl_trans where `type`='".$from."' and type_no='".$to."' ";
    $gl_data = db_fetch(db_query($sql_jl));

    $created_by=get_username($gl_data["created_by"]);
}






    $comment_sql="Select memo_ FROm 0_comments where type='".$from."' and id='".$to."' ";
$data=db_fetch(db_query($comment_sql));
$commnet='';
if($data['memo_']!='')
{
    $commnet=$data['memo_'];
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
            padding: 5px;
            vertical-align: top;
        }

        /*.invoice-box table tr td:nth-child(2) {*/
        /*text-align: right;*/
        /*}*/

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
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
            padding-bottom: 20px;
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
                            <img src="<?= $path_to_root ?>/company/0/images/pdf-header-top.jpg" style="width:100%;">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table cellpadding="0" cellspacing="0" >
        <tr class="top">
            <td colspan="4">
                <table>
                    <tr>
                        <td class="title" style="text-align: center;">
                            <?php if($fno[1]=='0') { ?>
                                <img src="images/journal-voucher.jpg" style="width:100%; max-width: 250px;">
                            <?php } ?>
                            <?php if($fno[1]=='1') { ?>
                                <img src="images/payment-voucher.jpg" style="width:100%; max-width: 250px; ">
                            <?php } ?>
                            <?php if($fno[1]=='2') { ?>
                                <img src="images/receipt-voucher.jpg" style="width:100%; max-width: 250px;">
                            <?php } ?>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>


    <!--    <div style="text-align: center !important;">Payment Voucher</div>-->
    <div class="head-info" style=" width: 100%; margin-left: 0px; border: 1px solid black">

        <table>

            <?php if($fno[1]!='0')
            { ?>

            <tr>
                <td><?=$pv_rv_label?></td>
                <td style="text-align: left"><?= $refernce_no; ?></td>
                <td></td>
                <?php if($chck_no!=''): ?>

                <td>Cheque#:</td>
                <td style="text-align: left"><?= $chck_no; ?></td>
                <?php endif; ?>
            </tr>
            <tr>
                <td>Date:</td>
                <td style="text-align: left"><?= $voucher_date; ?></td>
                <td></td>
        <?php if($chck_no!=''): ?>
                <td>Cheque Date:</td>
                <td><?= $chk_date; ?></td>
         <?php endif; ?>
            </tr>

            <tr>
                <td>Type:</td>
                <td style="text-align: left"><?=  $person_type; ?></td>
                <td></td>
                <td>Amount:</td>
                <td><?= number_format($amount,2); ?></td>
            </tr>

            <tr>
                <td><?=$paidto_rcvd_to_label?></td>
                <td style="text-align: left"><?= $person_id; ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: left"></td>
                <td></td>
                <td>Payment Type:</td>
                <td><?= $payment_type; ?></td>
            </tr>

            <tr>
                <td>Remarks:</td>
                <td style="text-align: left"><?= $commnet; ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
         <?php } else { ?>
                <tr>
                    <td><?=$pv_rv_label?></td>
                    <td style="text-align: left"><?= $refernce_no; ?></td>
                    <td></td>
                    <td>Date#:</td>
                    <td style="text-align: left"><?= date('d-m-Y',strtotime($voucher_date)); ?></td>
                </tr>

                <tr>
                    <td>Amount:</td>
                    <td style="text-align: left"><?= number_format($jour_data['amount'],2); ?></td>
                    <td></td>
                    <td>Remarks:</td>
                    <td style="text-align: left"><?= $commnet; ?></td>
                </tr>
         <?php } ?>

        </table>

    </div>

    <div style="height:10px;"></div>
    <div style="height: 375px;">
        <table class="invoice-items"  style="width:100%;">
            <tr class="heading">
                <td>
                    Account<br><span class="arabic">الرقم</span>
                </td>
                <td >
                    Description<br><span class="arabic">وصف</span>
                </td>
               <!-- <td>
                    Project
                </td>-->
                <td >
                    Debit<br><span class="arabic">وحدة</span>
                </td>
                <td >
                    Credit<br><span class="arabic">الكمية</span>
                </td>

            </tr>
            <?= $line_item_tr ?>
            <tr >
                <td style="height:50px;"></td>
            </tr>

        </table>
    </div>

    <div style="padding:2px;font-size: 9pt;margin-top:5%;">
        <table>
            <tr>
                <td><label style="font-weight:500;">Prepared By : <label style="font-size:5pt;text-decoration:underline;"><?= $current_username; ?></label></label></td>
                <td><label style="font-weight:500;">Approved By : <label style="font-size:5pt;">_______________________</label></label></td>
                <td>&nbsp;</td>

                <td><label style="font-weight:500;">Verified By : <label style="font-size:5pt;">________________________</label></label></td>
            </tr>
        </table>
    </div>


    <!--<div style="padding:2px;font-size: 9pt;margin-top:9%;">
        <table>
            <tr>
                <td><label style="font-weight:500;">Printed By : <label style="font-size:5pt;"><?/*= $current_username; */?></label></label></td>
            </tr>
            <tr>
                <td><label style="font-weight:500;">Printed On : <label style="font-size:5pt;"><?/*= date('d-m-Y h:i:s'); */?></label></label></td>
            </tr>
        </table>
    </div>-->


</div>
</body>
</html>
