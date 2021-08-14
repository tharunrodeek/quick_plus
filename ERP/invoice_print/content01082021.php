<?php

date_default_timezone_set('Asia/Dubai');
$current_username = $_SESSION['wa_current_user']->username;
function get_invoice_range($from, $to)
{
    global $SysPrefs;
    $ref = ($SysPrefs->print_invoice_no() == 1 ? "trans_no" : "reference");
    $sql = "SELECT trans.trans_no, trans.reference,trans.payment_flag,dim.name as dim_name,trans.dimension_id    
		FROM " . TB_PREF . "debtor_trans trans 
			LEFT JOIN " . TB_PREF . "voided voided ON trans.type=voided.type AND trans.trans_no=voided.id 
			LEFT JOIN 0_dimensions dim ON dim.id=trans.dimension_id 
		WHERE trans.type=" . ST_SALESINVOICE
        . " AND ISNULL(voided.id)"
        . " AND trans.trans_no BETWEEN " . db_escape($from) . " AND " . db_escape($to)
        . " ORDER BY trans.tran_date, trans.$ref";


    return db_query($sql, "Cant retrieve invoice range");
}


function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}


$from = $_GET['PARAM_0'];
$to = $_GET['PARAM_1'];

if (!$from || !$to) return;

$fno = explode("-", $from);
$tno = explode("-", $to);
$from = min($fno[0], $tno[0]);
$to = max($fno[0], $tno[0]);
$dec = user_price_dec();
$row['trans_no'] = $fno[0];
$myrow = get_customer_trans($row['trans_no'], ST_SALESINVOICE, null,null,$voided);

$voided = false;
if($myrow['Total'] == 0){
    if(!empty(get_voided_entry(ST_SALESINVOICE, $fno[0]))){
        $voided = true;
        $myrow = get_customer_trans($fno[0], ST_SALESINVOICE, null,null,$voided);
    }
}

$sign = 1;
$result = get_customer_trans_details(ST_SALESINVOICE,$voided == false ? $row['trans_no']  : $fno[0],$voided);
$SubTotal = 0;
$DiscountedAmountTotal = 0;
$line_item_tr = "";
$total_tax = 0;
$i = 1;
$created_by = "";

$disc_amt = 0;

$created_at_date_time = null;

$customer_info = get_customer($myrow['debtor_no']);

$total_govt_fee = 0;

$has_transaction_id = false;

while ($myrow2 = db_fetch($result)) {

    if ($myrow2["quantity"] == 0)
        continue;


//    $created_at_date_time = $myrow2['created_at'];
//    $created_by = $myrow2['created_by'];
//    $Net = round2($sign * (((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"])
//            + ($myrow2['govt_fee'] + $myrow2['bank_service_charge'] + $myrow2['bank_service_charge_vat']) * $myrow2["quantity"]),
//        user_price_dec());


    $created_at_date_time = $myrow2['created_at'];
    $created_by = $myrow2['created_by'];
    $Net = round2($sign * (
//            (((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"]) * $myrow2["quantity"])
            ($myrow2["unit_price"] + $myrow2['govt_fee'] +
                $myrow2['bank_service_charge'] + $myrow2['bank_service_charge_vat']+ + $myrow2['extra_service_charge']
            ) * $myrow2["quantity"]),
        user_price_dec());


//    $Net = round2($sign * ( ($myrow2['discount_amount'] * $myrow2['quantity'])
//            + ($myrow2["unit_price"]+$myrow2['govt_fee'] + $myrow2['bank_service_charge'] +
//                $myrow2['bank_service_charge_vat']) * $myrow2["quantity"]));


//    echo $Net; die;

    $DisplayPrice = $myrow2["unit_price"];
    $DisplayQty = number_format2($sign * $myrow2["quantity"], get_qty_dec($myrow2['stock_id']));
    $DisplayNet = $Net;
    $TaxAmount = $myrow2['unit_tax'] * $myrow2["quantity"];


    //Changed to NOTAX after savi's request

//    $TaxAmount = 0;
    $total_tax += floatval($TaxAmount);

    if ($myrow2["discount_amount"] == 0) {
        $DisplayDiscount = 0;
    } else {
        $DisplayDiscount = $myrow2["discount_amount"];
        $disc_amt += $myrow2['discount_amount'] * $myrow2['quantity'];
    }

    //Modified for AMER
    $DiscountedAmountTotal += $DisplayDiscount * $myrow2["quantity"];

//    echo $dec; die;

//    echo number_format2(floatval($DisplayNet)+$DiscountedAmountTotal+floatval($TaxAmount),$dec); die;
//    echo number_format2(floatval($DisplayNet) + $DiscountedAmountTotal + floatval($TaxAmount), $dec); die;
//


    $DisplayNet = floatval($DisplayNet) + floatval($TaxAmount);

//    if($customer_info['show_discount'] == 1) {
//        $DisplayNet = $DisplayNet-round($DiscountedAmountTotal);
//    }

//    echo $DisplayNet; die;

    $SubTotal += $DisplayNet;

//    echo $SubTotal; die;

    $item_description = $myrow2['description'];

    $item_description = str_lreplace('-', '<br />', $item_description);
    $item_description = $myrow2['transaction_id'] ? $item_description . "<br>" . $myrow2['transaction_id'] : $item_description;
    $item_description = $myrow2['application_id'] ? $item_description . "<br>" . $myrow2['application_id'] : $item_description;
    $item_description = $myrow2['ed_transaction_id'] ? $item_description . "<br>" . $myrow2['ed_transaction_id'] : $item_description;
    $item_description = $myrow2['ref_name'] ? $item_description . "<br>" . $myrow2['ref_name'] : $item_description;


    if(!empty(trim($myrow2['transaction_id'])))
        $has_transaction_id = true;

    $total_charge = $DisplayPrice;
    $tasheel_invoice = false;
    if (tasheel_invoice($row['payment_flag']) || tadbeer_invoice($row['payment_flag'])) {
        $tasheel_invoice = true;
        $total_charge = $myrow2['govt_fee'] + $myrow2['bank_service_charge'] +
            $myrow2['bank_service_charge_vat'] + $DisplayPrice;
    }

    $line_item_tr .=
        "<tr class='item'>
            <td class='center'>" . $i . "</td>
            <td class='center arabic'>" . $item_description . "</td>
            <td class='center'>" . $DisplayQty . "</td>";

    if (!$tasheel_invoice) {
        $line_item_tr .= "<td class='right'>" .
            number_format2($myrow2['govt_fee'] + $myrow2['bank_service_charge'] +
                $myrow2['bank_service_charge_vat']+$myrow2['extra_service_charge'], $dec) .
            "</td>
            <td class='right'>" . number_format2($total_charge, $dec) . "</td>";
    } else {
        $line_item_tr .= "<td class='right'>" . number_format2($total_charge, $dec) . "</td>";
    }


    //Changed to NOTAX after savi's request

    $line_item_tr .= " <td class='right' >" . number_format2($TaxAmount, $dec) . "</td>";

     $line_item_tr.= "<td class='right'>" . number_format2($DisplayNet, $dec) . "</td>";

       $line_item_tr.=" </tr>";

    $i++;


    $total_govt_fee += $myrow2['govt_fee'] + $myrow2['bank_service_charge'] + $myrow2['bank_service_charge_vat'];


}


$DisplaySubTot = number_format2($SubTotal, $dec);

//echo $DisplaySubTot; die;

$tax_items = get_trans_tax_details(ST_SALESINVOICE, $voided == false ? $row['trans_no']  : $fno[0],$voided);
$first = true;

while ($tax_item = db_fetch($tax_items)) {

    if ($tax_item['amount'] == 0)
        continue;

    $DisplayTax = number_format2($sign * $tax_item['amount'], $dec);

    if ($SysPrefs->suppress_tax_rates() == 1)
        $tax_type_name = $tax_item['tax_type_name'];
    else
        $tax_type_name = $tax_item['tax_type_name'] . " (" . $tax_item['rate'] . "%) ";

}

//$DisplayTotal = number_format2($myrow["ov_amount"] + $myrow['ov_gst']+$disc_amt, $dec);
$DisplayTotal = number_format2($SubTotal, $dec);

if ($customer_info['show_discount'] == 1) {
//    $DisplayTotal = number_format2($sign * ($myrow["ov_freight"] + $myrow["ov_gst"] +
//            $myrow["ov_amount"] + $myrow["ov_freight_tax"]), $dec);

    $DisplayTotal = number_format2($SubTotal-$DiscountedAmountTotal, $dec);

}

$DisplayTotalTax = number_format2($total_tax, $dec);

$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
$barcodeImage = base64_encode($generator->getBarcode($myrow['barcode'], $generator::TYPE_CODE_128));


$invoice_created_time = date("H:i:s", strtotime($created_at_date_time) + 14440);

?>

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
                            <img src="<?= $path_to_root ?>/company/0/images/pdf-header-top.jpg" style="width:100%;">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


    <?php

    $dimension_info = get_dimension($myrow['dimension_id']);

    $trn = get_company_prefs()['gst_no'];

    $trn = $dimension_info['gst_no'];

        $trn_html =  '<br> <p style="text-align: center; float: right">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<b style="font-size: 13px">TRN : '.$trn.'</b></p>';

        if(empty($trn))
            $trn_html = "";

    ?>

    <table class="tbl-heading">

        <tr>
            <td style="text-align: center">&nbsp;&nbsp;&nbsp;<img height="40" width="80"
                                                                  src="data:image/png;base64,<?= $barcodeImage ?>">
                &nbsp;&nbsp;<p style="text-align: center;"><?= $myrow['barcode'] ?></p></td>
            <td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php if (tasheel_invoice($row['payment_flag']) || tadbeer_invoice($row['payment_flag'])) {
                    echo '<img src="images/notax-invoice-title.jpg" style="width:100%; max-width: 250px; visibility: visible">';

                    echo $trn_html;

                } else {



                    if(in_array($myrow['dimension_id'],[9,2])) {

                        $credit_invoice = false;

                        if($customer_info['customer_type'] == 'CREDIT')
                            $credit_invoice = true;

                        if(($myrow['alloc'] >= ($myrow['ov_amount']+$myrow['ov_gst']))
                            || $credit_invoice) {

                            if(true) {
                                //Changed to NOTAX after savi's request
                                echo '<img src="images/tax-invoice-title.jpg" style="width:100%; max-width: 250px; visibility: visible">';
                                echo $trn_html;
                            }
                            else
                                echo '<h3>PRE - SALES INVOICE</h3>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        }
                        else {
                            echo '<h3>PRE - SALES INVOICE</h3>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        }

                    }
                    else {

                        //Changed to NOTAX after savi's request
                        echo '<img src="images/tax-invoice-title.jpg" style="width:100%; max-width: 250px; visibility: visible">';
                        echo $trn_html;
                    }
//                    echo $trn_html;

                } ?>

            </td>
            <td></td>
        </tr>




    </table>


    <table class="info-table">
        <caption
                style="text-align: center; color: #1a2226; font-weight: bold; font-size: 13px; border: 1px solid black; font-family: 'Times New Roman'">
            Customer Information <span class="arabic">معلومات المتعاملين </span>
        </caption>
        <tr>
            <td style="font-weight: bold">Date:</td>
            <td class="center"
                style="width: 400px"><?= sql2date($myrow['tran_date']) . " " . $invoice_created_time ?></td>
            <td class="right arabic">: التاريخ والوقت</td>
        </tr>
        <tr>
            <td style="font-weight: bold">Invoice Number:</td>
            <td class="center"><?= $myrow['reference'] ?></td>
            <td class="right arabic">: رقم الفاتورة</td>
        </tr>
        <tr>
            <td style="font-weight: bold">Customer/Company:</td>
            <td class="center"><?= $myrow['display_customer'] ?></td>
            <td class="right arabic">:  الشركة</td>
        </tr>
        <tr>
            <td style="font-weight: bold">Contact Person:</td>
            <td class="center"><?= $myrow['contact_person'] ?></td>
            <td class="right arabic">: المتعامل</td>
        </tr>

<!--        <tr>-->
<!--            <td style="font-weight: bold">Cost Center:</td>-->
<!--            <td class="center">--><?//= $row['dim_name'] ?><!--</td>-->
<!--            <td class="right arabic">: المتعامل</td>-->
<!--        </tr>-->

        <tr>
            <td style="font-weight: bold">Customer TRN Number:</td>
            <td class="center"><?= $myrow['customer_trn'] ?></td>
            <td class="right arabic">: رقم تسجيل الضريبة</td>
        </tr>
        <tr>
            <td style="font-weight: bold">Mobile Number</td>
            <td class="center"><?= $myrow['customer_mobile'] ?></td>
            <td class="right arabic">: رقم الهاتف المتحرك</td>
        </tr>
<!--        <tr>-->
<!--            <td style="font-weight: bold">Payment Type:</td>-->
<!--            <td class="center">--><?//= $myrow['invoice_type'] ?><!--</td>-->
<!--            <td class="right arabic">: مرجع</td>-->
<!--        </tr>-->
    </table>

    <div style="height: 310px">
        <table class="invoice-items">
            <caption
                    style="margin: 5px; font-weight: bold;font-size: 14px; color: #1a2226; font-family: 'Times New Roman'">
                Particulars <span class="arabic">تفاصيل</span></caption>
            <tr class="heading">
                <td>
                    Sl. No<br><span class="arabic">الرقم</span>
                </td>
                <td>
                    Service<br><span class="arabic">الخدمات</span>
                </td>
                <td>
                    Quantity<br><span class="arabic">الكمية</span>
                </td>

                <?php

                $colspan = 5; //5 //Changed to NOTAX after savi's request, it was 5
                if (!$tasheel_invoice) {
                    $colspan = 6; //6 //Changed to NOTAX after savi's request, it was 6
                    ?>

                    <td>
                        Govt.Fee & Bank Charge<br><span class="arabic">الرسوم الحكومية</span>
                    </td>

                <?php } ?>

                <td>
                    Transaction Charge<br><span class="arabic">تكلفة المعاملة</span>
                </td>

<!--                Changed to NOTAX after savi's request-->

                <td>
                    Tax Amount<br><span class="arabic">قيمة المضافة</span>
                </td>
                <td>
                    Total<br><span class="arabic">الاجمالى بالدرهم</span>
                </td>
            </tr>

            <?= $line_item_tr ?>



            <tr class="total item">

                <?php

                if ($customer_info['show_discount'] != 1) {
                    $DiscountedAmountTotal = 0;
                }

                $gross_total = number_format2(round($SubTotal - $total_tax + $DiscountedAmountTotal,2),2);

                ?>

                <td colspan="<?= $colspan ?>" style="text-align: right"><b>Net Amount اجمالى القيمة </b></td>

                <td class="right">
                    <?= $gross_total ?>
                </td>
            </tr>


            <?php if ($customer_info['show_discount'] == 1 && $DiscountedAmountTotal > 0.001) { ?>

                <tr class="total item">
                    <!--                <td></td>-->
                    <td colspan="<?= $colspan ?>" style="text-align: right"><b>Discount </b></td>

                    <td class="right">
                        <?= price_format($DiscountedAmountTotal) ?>
                    </td>
                </tr>

            <?php } ?>


<!--            Changed to NOTAX after savi's request-->

            <tr class="total item">
                <td colspan="<?= $colspan ?>" style="text-align: right"><b>VAT اجمالى القيمة المضافة </b></td>

                <td class="right">
                    <?= $DisplayTotalTax ?>
                </td>
            </tr>



            <tr class="total item">

                <td colspan="<?= $colspan ?>" style="text-align: right; border-top: none;"><b>Total الاجمالي </b>
                </td>

                <td class="right">
                    <?= $DisplayTotal ?>
                </td>
            </tr>


            <?php

            $total_amount = (float)str_replace(',', '', $DisplayTotal);
            $customer_card_payment_amount = 0;
            if ($myrow['invoice_type'] == 'G2' || $myrow['invoice_type'] == 'CustomerCard') {
                $customer_card_payment_amount = $total_govt_fee;

                //IF TASHEEL CUSTOMER CARD
                if (tasheel_cc_invoice($myrow['payment_flag']) || tadbeer_cc_invoice($myrow['payment_flag']) ) {
                    $customer_card_payment_amount = $myrow['customer_card_amount'];
                }

                $customer_card_payment_amount = $myrow['customer_card_amount'];


            }

            $total_payable = $total_amount - $customer_card_payment_amount;


            ?>


<!--            <tr class="total item" style="display: none">-->
<!---->
<!--                <td colspan="--><?//= $colspan ?><!--" style="text-align: right; border-top: none;"><b>Customer Card Payment-->
<!--                        الاجمالي </b></td>-->
<!---->
<!--                <td class="right">-->
<!--                    --><?//= number_format2($customer_card_payment_amount, 2) ?>
<!--                </td>-->
<!--            </tr>-->
<!---->
<!---->
<!--            <tr class="total item" style="display: none">-->
<!---->
<!--                <td colspan="--><?//= $colspan ?><!--" style="text-align: right; border-top: none;"><b>Total Payable-->
<!--                        الاجمالي </b></td>-->
<!---->
<!--                <td class="right">-->
<!--                    --><?//= number_format2($total_payable, 2) ?>
<!--                </td>-->
<!--            </tr>-->


        </table>
        <?php $voided && is_voided_display(ST_SALESINVOICE, $from, trans("This transaction has been voided.")); ?>
    </div>

    <table class="footer1">

        <tr>
            <?php if(!in_array($myrow['dimension_id'], [DT_ADHEED, DT_ADHEED_OTH])): ?>
            <td style="text-align: center"><?= $created_by ?></td>
            <?php else: ?>
            <td style="text-align: center">&nbsp;</td>
            <?php endif; ?>
            <td class="right">Note:<span class="arabic">ملاحظات</span></td>
        </tr>
        <tr>
            <td style="text-align: center">Authorized Signatory <br> <span class="arabic">المخول بالتوقيع</span></td>
            <td class="right arabic">الرجاء التأكد من الفاتورة والمستندات قبل مغادرة الكاونتر</td>
        </tr>
        <tr>
            <?php
            $pay_info = "";
            if (tasheel_cc_invoice($row['payment_flag']) || tadbeer_cc_invoice($row['payment_flag'])) {
                $pay_info = "Paid Using Customer Card";
            } ?>

            <?php


            $text = "";
            if(!isset($_GET['reprint'])) {
                //$text = " (REPRINT) ";
            } ?>

            <td style="text-align: center"><b> <?= $text ?>  <em><u><?= $pay_info ?></u><em></b></td>
            <td class="right">Kindly check the invoice and documents before leaving the counter</td>
        </tr>
    </table>



    <?php


    $alloc_result = get_allocatable_from_cust_transactions($myrow['debtor_no'], $voided == false ? $row['trans_no']  : $fno[0], 10,$voided);

    // exit;
    if (db_num_rows($alloc_result) > 0) {
        $receipt_title = $voided == false ? 'RECEIPT INFO' : 'VOIDED RECEIPT INFO' ;
        echo ' <p style="font-size: 15px">
        '.$receipt_title.'
    </p>';

        echo '<table  class="info-table" style="margin-bottom: 2px">
        <tr>
            <td style="text-align: center">Receipt Number</td>
            <td style="text-align: center"> Amount</td>
            <td style="text-align: center">Collected By</td>
            <td style="text-align: center">Payment Mode</td>
        </tr>';

    while ($alloc_row = db_fetch($alloc_result))
    {

        $rcpt_created_info = get_entered_by_user($alloc_row['trans_no'],$alloc_row['type']);

        if(empty($rcpt_created_info['real_name']))
            $rcpt_created_info['real_name'] = $rcpt_created_info['user_id'];

        echo "<tr>";
        echo "<td style='text-align: center'>".$alloc_row['reference']."</td>";
        echo "<td style='text-align: center'>".number_format2($alloc_row['amt']
                +$alloc_row['round_of_amount']+$alloc_row['credit_card_charge']
                , 2)
            ."</td>";
//        echo "<td style='text-align: center'>".number_format2($alloc_row['round_of_amount'], 2)."</td>";
        echo "<td style='text-align: center'>".$rcpt_created_info['real_name']."</td>";
        echo "<td style='text-align: center'>".$alloc_row['payment_method']."</td>";
        echo "</tr>";

    }


    }
    echo '</table>';
    ?>





<!--    <table  class="info-table" style="margin-bottom: 2px">-->
<!--        <tr>-->
<!--            <td style="text-align: center">Receipt Number</td>-->
<!--            <td>Amount</td>-->
<!--            <td>Collected By</td>-->
<!--        </tr>-->
<!---->
<!--        <tr>-->
<!--            <td style="text-align: center">12654654/2020</td>-->
<!--            <td>5000.00</td>-->
<!--            <td>Bipin</td>-->
<!--        </tr>-->
<!--    </table>-->


    <div><img src="<?= $path_to_root ?>/company/0/images/pdf-footer-image.jpg"></div>
</div>
</body>
</html>
