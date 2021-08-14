<?php

date_default_timezone_set('Asia/Dubai');
$current_username = $_SESSION['wa_current_user']->username;


function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}





?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt Print</title>

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


//        if(empty($trn))
            $trn_html = "";


    $rcpt_trans_no = $_GET['trans_no'];

    $myrow = get_customer_trans($rcpt_trans_no, ST_CUSTPAYMENT);

    ?>

    <h3 style="text-align: center; color: black; font-size: 15px">RECEIPT  الإيصال</h3>


    <table class="w-100 table-sm mt-4">
        <tr>
            <td class="border w-30">
                <table class="w-100">
                    <tbody>

                    <tr>
                        <td><b>Receipt No.</b></td>
                        <td><?= $myrow['reference'] ?></td>
                    </tr>

                    <?php

                    $customer_name = $myrow['display_customer'];

                    if(empty($customer_name)) {
                        $customer_info = get_customer($myrow['debtor_no']);
                        $customer_name = $customer_info['name'];
                    }

                    ?>

                    <tr>
                        <td><b>Customer/<span lang="ar">المتعامل</span></b></td>
                        <td><?= $customer_name ?></td>
                    </tr>

                    <tr>
                        <td><b>Payment Method/<span lang="ar">المتعامل</span></b></td>
                        <td><?= $myrow['payment_method'] ?></td>
                    </tr>

                    </tbody>
                </table>
            </td>
            <td class="border w-<?=$w_cust_det?> align-bottom">
                <table class="w-100">
                    <tbody>
                    <tr>
                        <td><b>Date/<span lang="ar">التاريخ والوقت</span></b></td>
                        <td><?= sql2date($myrow['tran_date']) . " " . $invoice_created_time ?></td>
                    </tr>

                    <tr>
                        <td><b>Mobile No./<span lang="ar">رقم الهاتف المتحرك</span></b></td>
                        <td><?= $myrow['customer_mobile'] ?></td>
                    </tr>

                    <tr>
                        <td><b>Remarks/<span lang="ar">رقم الهاتف المتحرك</span></b></td>
                        <td><?= get_comments_string(ST_CUSTPAYMENT, $myrow['trans_no']); ?></td>
                    </tr>


                    </tbody>
                </table>
            </td>

        </tr>
    </table>

    <div style="height: 500px">
        <table class="invoice-items">
            <caption
                    style="margin: 5px; font-weight: bold;font-size: 14px; color: #1a2226; font-family: 'Times New Roman'">
                Particulars <span class="arabic">تفاصيل</span></caption>

            <tbody>
            <tr class="heading">
                <td>
                    Sl. No<br><span class="arabic">الرقم</span>
                </td>
                <td>
                    Barcode<br><span class="arabic">الخدمات</span>
                </td>
                <td>
                    Invoice No<br><span class="arabic">الكمية</span>
                </td>

                <td>
                    Invoice Amount<br><span class="arabic">تكلفة المعاملة</span>
                </td>

                <td>
                    Total<br><span class="arabic">الاجمالى بالدرهم</span>
                </td>
            </tr>

            <?php



            $sign = 1;
            $i = 1;
            $customer_info = get_customer($myrow['debtor_no']);


            $result = get_allocatable_to_cust_transactions($myrow['debtor_no'], $myrow['trans_no'], $myrow['type'],null,null,null,null,$voided);

            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();


            if(db_num_rows($result) > 0) {

                while ($myrow2 = db_fetch($result)) {


                    $barcodeImage = base64_encode($generator->getBarcode($myrow['barcode'], $generator::TYPE_CODE_128));

                    $barcode = '';

                    echo "<tr class='item'>";
                    echo "<td class='center arabic'>" . $i . "</td>";
                    echo "<td class='center arabic'><img height='25' width='80'
                                                                  src='data:image/png;base64,$barcodeImage'>
                &nbsp;&nbsp;<p style='text-align: center;'>" . $myrow2['barcode'] . "</p></td>";
                    echo "<td class='center arabic'>" . $myrow2['reference'] . "</td>";
                    echo "<td class='center arabic'>" . number_format2($myrow2['Total'], 2) . "</td>";
                    echo "<td class='center arabic'>" . number_format2($myrow2['amt'], 2) . "</td>";
                    echo "</tr>";


                    $i++;


                }
            }
            else {

                echo "<tr class='item'>";
                echo "<td colspan='5' class='center arabic'>ADVANCE RECEIPT</td>";
                echo "</tr>";

            }


            ?>

            </tbody>


            <tfoot>

            <?php

            $total_rcpt_amount = $myrow['Total']+$myrow['credit_card_charge']-$myrow['ov_discount'];

            ?>

            <tr class='item'>
                <td colspan="4" class='center arabic right'>
                    SUB TOTAL
                </td>
                <td style="text-align: center"><?= number_format2($myrow['Total'],2) ?></td>
            </tr>

            <tr class='item'>
                <td colspan="4" class='center arabic right'>
                    DISCOUNT
                </td>
                <td style="text-align: center"><?= number_format2($myrow['ov_discount'],2) ?></td>
            </tr>

            <tr class='item'>
                <td colspan="4" class='center arabic right'>
                    CREDIT CARD CHARGE
                </td>
                <td style="text-align: center"><?= number_format2($myrow['credit_card_charge'],2) ?></td>
            </tr>

            <tr class='item'>
                <td colspan="4" class='center arabic right'>
                    TOTAL RECEIPT AMOUNT
                </td>
                <td style="text-align: center"><?= number_format2($total_rcpt_amount,2) ?></td>
            </tr>

            </tfoot>


        </table>
    </div>

    <table class="footer1">


        <?php


        $rcpt_created_info = get_entered_by_user($rcpt_trans_no, ST_CUSTPAYMENT);

        $created_by = $rcpt_created_info['real_name'];

        if(empty($created_by))
            $created_by = $rcpt_created_info['user_id'];



        ?>


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
            $pay_info = "";

            $text = "";
            // if(!isset($_GET['reprint'])) {
            //     $text = " (REPRINT) ";
            // }
             ?>

            <td style="text-align: center"><b> <?= $text ?>  <em><u><?= $pay_info ?></u><em></b></td>
            <td class="right">Kindly check the invoice and documents before leaving the counter</td>
        </tr>
    </table>









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
