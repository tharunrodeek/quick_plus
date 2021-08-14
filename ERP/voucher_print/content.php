<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Voucher</title>

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


<?php

date_default_timezone_set('Asia/Dubai');
$current_username = $_SESSION['wa_current_user']->username;
function get_voucher($id,$type)
{
    // $type=1;
    $table = "0_bank_trans";
    if($type == 0)
        $table = "0_journal";

    $sql = "SELECT * FROM $table where trans_no = $id and type=$type";
    return db_fetch(db_query($sql, "Cant retrieve voucher"));
}


function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

$voucher_id = $_GET['voucher_id'];

$exploded = explode("-",$voucher_id);

$voucher_id = $exploded[0];
$type = $exploded[1];

$myrow = get_voucher($voucher_id,$type);

if($type == 0) {
    $myrow['ref'] = $myrow['reference'];
    $myrow['trans_date'] = $myrow['tran_date'];
}

$trans_type = $myrow['type'];
$trans_no = $myrow['trans_no'];


$gl_trans = get_gl_trans($trans_type,$trans_no);

$person_id = get_counterparty_name($trans_type,$trans_no);
if(empty($person_id)) $person_id = $myrow['person_id'];
//display_error($person_id); die;

$voucher_title = "JOURNAL VOUCHER";
$pv_rv_label = "J.V.No:";

if($trans_type == 1) {
    $voucher_title = "PAYMENT VOUCHER";
    $pv_rv_label = "P.V.No:";
}

if($trans_type == 2) {
    $voucher_title = "RECEIPT VOUCHER";
    $pv_rv_label = "R.V.No:";
}


$pv_rv_label = "J.V.No:";

//$voucher_title = $trans_type == "1" ? "PAYMENT VOUCHER" : "RECEIPT VOUCHER";
//$pv_rv_label = $trans_type == "1" ? "P.V.No:" : "R.V.No:";
$paidto_rcvd_to_label = $trans_type == "1" ? "Paid To:" : "Received From:";

?>



<div class="invoice-box">
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


        <tr>
            <td colspan="4">

                <table style="padding: 0; margin-top: -30px">
                    <tr>
                        <td class="title" style="text-align: center; font-size: 15px">
                            <u><?= $voucher_title?></u>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>

    </table>


    <!--    <div style="text-align: center !important;">Payment Voucher</div>-->
    <div class="head-info" style=" width: 90%; margin-left: 30px; border: 1px solid black">

        <table>

            <tr>
                <td><?=$pv_rv_label?></td>
                <td style="text-align: left"><?=$myrow['ref']?></td>
                <td></td>
<!--                <td>Cheque#:</td>-->
<!--                <td style="text-align: left">--><?//=$myrow['chq_no']?><!--</td>-->

                <td>Date:</td>
                <td style="text-align: left"><?=sql2date($myrow['trans_date'])?></td>

            </tr>
<!--            <tr>-->
<!---->
<!--                <td>Amount:</td>-->
<!--                <td>--><?//= number_format2(abs($myrow['amount']),2) ?><!--</td>-->
<!---->
<!--                <td></td>-->
<!---->
<!--            </tr>-->
            <tr>
<!--                <td>Type:</td>-->
<!--                <td style="text-align: left">--><?//=$payment_person_types[$myrow['person_type_id']]?><!--</td>-->
<!--                <td></td>-->

            </tr>
<!--            <tr>-->
<!--                <td>--><?//=$paidto_rcvd_to_label?><!--</td>-->
<!--                <td style="text-align: left">--><?//= $person_id ?><!--</td>-->
<!--                <td></td>-->
<!--                <td></td>-->
<!--                <td></td>-->
<!--            </tr>-->

            <tr>
<!--                <td>Remarks:</td>-->
<!--                <td style="text-align: left">--><?//= $myrow['description'] ?><!--</td>-->
<!--                <td></td>-->
<!--                <td></td>-->
<!--                <td></td>-->
            </tr>

        </table>

    </div>

    <br>

    <div class="trans-table" style="width: 90%; margin-left: 30px;">

        <table style="border: 1px solid black">
            <tr>
                <td style="background: #eee;">S.No</td>
                <td style="background: #eee;">A/c #</td>
                <td style="background: #eee;">Account Name</td>
                <td style="background: #eee;">Name & Description</td>
                <td style="background: #eee;">Dr.Amount</td>
                <td style="background: #eee;">Cr.Amount</td>
            </tr>


            <?php

            $sl_no = 1;
            $dr_total = 0;
            $cr_total = 0;
            while ($myrow2 = db_fetch($gl_trans)) {

                $dr_amount = abs($myrow2['amount']>0?$myrow2['amount']:0.00);
                $cr_amount = abs($myrow2['amount']<0?$myrow2['amount']:0.00);

                $dr_total += $dr_amount;
                $cr_total += $cr_amount;

                if($myrow['account_code'] == $myrow2['account'])
                    $myrow2['memo_'] = $myrow['description'];

                echo "<tr>
                        <td>".$sl_no."</td>
                        <td>".$myrow2['account']."</td>
                        <td>".$myrow2['account_name']."</td>
                        <td>".$myrow2['memo_']."</td>
                        <td class='right'>".number_format2($dr_amount,2)."</td>
                        <td class='right'>".number_format2($cr_amount,2)."</td>
                </tr>";

                $sl_no++;

            }

            ?>


            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="border-top: 1px solid black" class="right"><?=number_format2($dr_total,2)?></td>
                <td style="border-top: 1px solid black" class="right"><?=number_format2($cr_total,2)?></td>
            </tr>


        </table>

<!--        <small style="font-size: 12px">AL Yalayis</small>-->

    </div>


    <div class="footer-table" id="footer">

        <table>
            <tr>
                <td class="center" style="font-size: 13px">Prepared By:</td>
                <td class="center" style="font-size: 13px">Approved By:</td>
                <td class="center" style="font-size: 13px">Received By:</td>
            </tr>
        </table>

    </div>


</div>
</body>
</html>