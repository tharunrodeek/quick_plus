<?php

date_default_timezone_set('Asia/Dubai');
$current_username = $_SESSION['wa_current_user']->username;
function get_invoice_range($from, $to)
{
    global $SysPrefs;
    $ref = ($SysPrefs->print_invoice_no() == 1 ? "trans_no" : "reference");
    $sql = "SELECT trans.trans_no, trans.reference,trans.payment_flag  
		FROM " . TB_PREF . "debtor_trans trans 
			LEFT JOIN " . TB_PREF . "voided voided ON trans.type=voided.type AND trans.trans_no=voided.id
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


$title_html = '<!doctype html>
    <html>
    <head>
        <meta charset="utf-8">

    </head>

    <body>';


echo $title_html;


$end_html = '</body></html>';


if (isset($_GET['_']) && $_GET['_'] == 'bulk_invoice') {

    $from_date = $_GET['from_date'];
    $to_date = $_GET['to_date'];
    $inv_range_from = $_GET['r_from'];
    $inv_range_to = $_GET['r_to'];
    $customer = $_GET['customer'];

    $from_date_ = db_escape(date2sql($from_date));
    $to_date_ = db_escape(date2sql($to_date));
    $inv_range_from_ = db_escape($inv_range_from);
    $inv_range_to_ = db_escape($inv_range_to);


    $range = get_print_bulk_invoices($from_date_, $to_date_, $inv_range_from_, $inv_range_to_, $customer);


} else {

    $from = $_GET['PARAM_0'];
    $to = $_GET['PARAM_1'];

    if (!$from || !$to) return;

    $fno = explode("-", $from);
    $tno = explode("-", $to);
    $from = min($fno[0], $tno[0]);
    $to = max($fno[0], $tno[0]);
    $dec = user_price_dec();
    $range = get_invoice_range($from, $to);

}


$total_count = db_num_rows($range);

$count = 0;

while ($row = db_fetch($range)) {

    $count++;


    $row['trans_no'] = $row[0];
    $myrow = get_customer_trans($row['trans_no'], ST_SALESINVOICE);
    $sign = 1;
    $result = get_customer_trans_details(ST_SALESINVOICE, $row['trans_no']);
    $SubTotal = 0;
    $DiscountedAmountTotal = 0;
    $line_item_tr = "";
    $total_tax = 0;
    $i = 1;
    $created_by = "";

    $disc_amt = 0;

    $created_at_date_time = null;

    $customer_info = get_customer($myrow['debtor_no']);

    if($myrow['debtor_no'] != 1)
        $myrow['customer_trn'] = $customer_info['tax_id'];

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
                ($myrow2["unit_price"]+$myrow2['govt_fee'] + $myrow2['bank_service_charge'] + $myrow2['bank_service_charge_vat']) * $myrow2["quantity"]),
            user_price_dec());


//    $Net = round2($sign * ( ($myrow2['discount_amount'] * $myrow2['quantity'])
//            + ($myrow2["unit_price"]+$myrow2['govt_fee'] + $myrow2['bank_service_charge'] +
//                $myrow2['bank_service_charge_vat']) * $myrow2["quantity"]));


//    echo $Net; die;

        $DisplayPrice = $myrow2["unit_price"];
        $DisplayQty = number_format2($sign * $myrow2["quantity"], get_qty_dec($myrow2['stock_id']));
        $DisplayNet = $Net;
        $TaxAmount = $myrow2['unit_tax'] * $myrow2["quantity"];
        $total_tax += floatval($TaxAmount);

        if ($myrow2["discount_amount"] == 0) {
            $DisplayDiscount = 0;
        } else {
            $DisplayDiscount = $myrow2["discount_amount"];
            $disc_amt += $myrow2['discount_amount']* $myrow2['quantity'];
        }

        //Modified for AMER
        $DiscountedAmount =  $DisplayDiscount * $myrow2["quantity"];
        $DiscountedAmountTotal += round($DiscountedAmount);

//    echo $dec; die;

//    echo number_format2(floatval($DisplayNet)+$DiscountedAmount+floatval($TaxAmount),$dec); die;
//    echo number_format2(floatval($DisplayNet) + $DiscountedAmount + floatval($TaxAmount), $dec); die;
//


        $DisplayNet = floatval($DisplayNet)  + floatval($TaxAmount);

//    if($customer_info['show_discount'] == 1) {
//        $DisplayNet = $DisplayNet-round($DiscountedAmount);
//    }

//    echo $DisplayNet; die;

        $SubTotal += $DisplayNet;

//    echo $SubTotal; die;

        $item_description = $myrow2['description'];

        $item_description = str_lreplace('-', '<br />', $item_description);
        $item_description = $myrow2['transaction_id'] ? $item_description . "<br>" . $myrow2['transaction_id'] : $item_description;
        
        if(empty($myrow2['transaction_id']))
            $item_description = $myrow2['ed_transaction_id'] ? $item_description . "<br>" . $myrow2['ed_transaction_id'] : $item_description;

        $item_description = $myrow2['application_id'] ? $item_description . "<br>" . $myrow2['application_id'] : $item_description;

        $item_description = $myrow2['ref_id'] ? $item_description . "<br>" . $myrow2['ref_id'] : $item_description;
        $item_description = $myrow2['ref_name'] ? $item_description . "<br>" . $myrow2['ref_name'] : $item_description;

        $line_item_tr .=
            "<tr class='item'>
            <td class='center'>" . $i . "</td>
            <td style='width: 300px' class='center arabic'>" . $item_description . "</td>
            <td class='center'>" . $DisplayQty . "</td>
            <td class='right'>" . number_format2($myrow2['govt_fee'] + $myrow2['bank_service_charge'] + $myrow2['bank_service_charge_vat'], $dec) . "</td>
            <td class='right'>" . number_format2($DisplayPrice, $dec) . "</td>
            <td class='right' >" . number_format2($TaxAmount, $dec) . "</td>
            <td class='right'>" . number_format2($DisplayNet, $dec) . "</td>

        </tr>";

        $i++;
    }


    $DisplaySubTot = number_format2($SubTotal, $dec);

//echo $DisplaySubTot; die;

    $tax_items = get_trans_tax_details(ST_SALESINVOICE, $row['trans_no']);
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

    $DisplayTotal = number_format2($myrow["ov_amount"] + $myrow['ov_gst']+$disc_amt, $dec);

    if($customer_info['show_discount'] == 1) {
        $DisplayTotal = number_format2($sign * ($myrow["ov_freight"] + $myrow["ov_gst"] +
                $myrow["ov_amount"] + $myrow["ov_freight_tax"]), $dec);

    }

    $total_tax = number_format2($total_tax, $dec);

    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    $barcodeImage = base64_encode($generator->getBarcode($myrow['barcode'], $generator::TYPE_CODE_128));


    $invoice_created_time = date("H:i:s", strtotime($created_at_date_time));

    ?>


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


        <table class="tbl-heading">

            <tr>
                <td style="text-align: center">&nbsp;&nbsp;&nbsp;<img height="40" width="80"
                                                                      src="data:image/png;base64,<?= $barcodeImage ?>">
                    &nbsp;&nbsp;<p style="text-align: center;"><?= $myrow['barcode'] ?></p></td>
                <td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php if(in_array($row['payment_flag'],[1,2])) {
                        echo '<img src="images/notax-invoice-title.jpg" style="width:100%; max-width: 250px; visibility: visible">';
                    } else {
                        echo '<img src="images/tax-invoice-title.jpg" style="width:100%; max-width: 250px; visibility: visible">';
                    }?>

                    <!--                <h3 style="float: right">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;-->
                    <!--                    &emsp;&emsp;&emsp;INVOICE</h3>-->
                </td>
                <td></td>
            </tr>

        </table>


        <table class="info-table">
            <caption style="text-align: center; color: #1a2226; font-weight: bold; font-size: 13px; border: 1px solid black; font-family: 'Times New Roman'">Customer Information <span class="arabic">معلومات المتعاملين </span>
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
                <td style="font-weight: bold">Customer:</td>
                <td class="center"><?= $myrow['display_customer'] ?></td>
                <td class="right arabic">: المتعامل</td>
            </tr>
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
            <tr>
                <td style="font-weight: bold">Customer Ref:</td>
                <td class="center"><?= $myrow['customer_ref'] ?></td>
                <td class="right arabic">: مرجع</td>
            </tr>
        </table>

        <div style="height: 397px">
            <table class="invoice-items">
                <caption style="margin: 5px; font-weight: bold;font-size: 14px; color: #1a2226; font-family: 'Times New Roman'">Particulars <span class="arabic">تفاصيل</span></caption>
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
                    <td>
                        Govt.Fee & Bank Charge<br><span class="arabic">الرسوم الحكومية</span>
                    </td>
                    <td>
                        Transaction Charge<br><span class="arabic">تكلفة المعاملة</span>
                    </td>
                    <td>
                        Tax Amount<br><span class="arabic">قيمة المضافة</span>
                    </td>
                    <td>
                        Total<br><span class="arabic">الاجمالى بالدرهم</span>
                    </td>
                </tr>

                <?= $line_item_tr ?>

                <tr class="total item">
                    <!--                <td colspan="4" style="visibility: hidden !important;"></td>-->
                    <!--                <td></td>-->
                    <td colspan="6" style="text-align: right"><b>Total VAT اجمالى القيمة المضافة </b></td>

                    <td class="right">
                        <?= $total_tax ?>
                    </td>
                </tr>

                <?php if($customer_info['show_discount'] == 1) { ?>

                    <tr class="total item">
                        <!--                <td></td>-->
                        <td colspan="6" style="text-align: right"><b>Total Discount Given </b></td>

                        <td class="right">
                            <?= number_format2(round($DiscountedAmountTotal),2) ?>
                        </td>
                    </tr>

                <?php } ?>

                <tr class="total item">
                    <!--                <td></td>-->

                    <!--                <td colspan="4"></td>-->
                    <td colspan="6" style="text-align: right; border-top: none;"><b>Net Amount الاجمالي </b></td>

                    <td class="right">
                        <?= $DisplayTotal ?>
                    </td>
                </tr>
            </table>
        </div>

        <table class="footer1">

            <tr>
                <td style="text-align: center"><?= $created_by ?></td>
                <td class="right">Note:<span class="arabic">ملاحظات</span></td>
            </tr>
            <tr>
                <td style="text-align: center">Authorized Signatory <br> <span class="arabic">المخول بالتوقيع</span></td>
                <td class="right arabic">الرجاء التأكد من الفاتورة والمستندات قبل مغادرة الكاونتر</td>
            </tr>
            <tr>
                <?php
                $pay_info="";
                if($row['payment_flag'] == 2) {
                    $pay_info = "Paid Using Customer Card";
                } ?>


                <?php


                $text = "";
                if(!isset($_GET['reprint'])) {
                    $text = " (REPRINT) ";
                } ?>

                <td style="text-align: center"><b><?= $text ?><em><u><?= $pay_info ?></u><em></b></td>
                <td class="right">Kindly check the invoice and documents before leaving the counter</td>
            </tr>
        </table>
        <div><img src="<?= $path_to_root ?>/company/0/images/pdf-footer-image.jpg"></div>
    </div>
    <?php

    if($total_count != $count)
        echo "<pagebreak />";


    ?>




<?php  }

echo $end_html;