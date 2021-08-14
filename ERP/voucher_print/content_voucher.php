<?php

date_default_timezone_set('Asia/Dubai');
$current_username = $_SESSION['wa_current_user']->username;
function get_voucher($id)
{
    $sql = "SELECT * FROM 0_vouchers where id = $id";
    return db_fetch(db_query($sql, "Cant retrieve voucher"));
}

function get_voucher_transactions($voucher_id) {
    $sql = "SELECT * FROM 0_voucher_transactions where voucher_id = $voucher_id";
    return db_query($sql, "Cant retrieve voucher");
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
//$voucher_type = $_GET['voucher_type'];
//$voucher_id = $voucher_id-1;
//$voucher_id = 973;


$myrow = get_voucher($voucher_id);


$trans_type = $myrow['voucher_type'] == "PV" ? 1 : 2;
$trans_no = $myrow['trans_no'];

$person_id = get_counterparty_name($trans_type,$trans_no);
if(empty($person_id)) $person_id = $myrow['person_id'];
//display_error($person_id); die;


$voucher_title = $myrow['voucher_type'] == "PV" ? "PAYMENT VOUCHER" : "RECEIPT VOUCHER";
$result = get_voucher_transactions($voucher_id);
$i=1;
while ($myrow2 = db_fetch($result)) {
    $line_item_tr .=
        "<tr class='item'>
            <td class='center'>" . $i . "</td>
            <td class='center arabic'>" . $myrow2['account_code']."-".get_gl_account_name($myrow2['account_code'])."</td>
            <td class='center arabic'>" . $myrow2['description']. "</td>
            <td class='right'>" . number_format2($myrow2['amount'], 2) . "</td>
        </tr>";

    $i++;
}

$invoice_created_time = date("H:i:s", strtotime($myrow['created_at']));
$invoice_created_date = sql2date($myrow['tran_date'])

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

    <table class="info-table">
        <caption style="text-align: center; border: 1px solid black"><?= $voucher_title ?></caption>
        <tr>
            <td>Date:</td>
            <td class="center"
                style="width: 400px"><?= $invoice_created_date . " " . $invoice_created_time ?></td>
            <td class="right arabic">: التاريخ والوقت</td>
        </tr>
        <tr>
            <td>Voucher Number:</td>
            <td class="center"><?= $myrow['reference'] ?></td>
            <td class="right arabic">: رقم الفاتورة</td>
        </tr>
        <tr>
            <td>Counter Party</td>
            <td class="center"><?= $person_id ?></td>
            <td class="right arabic">: المتعامل</td>
        </tr>

        <?php

        $acc_title = $myrow['voucher_type'] == "PV" ? "Paid From" : "Received To";

        ?>

        <tr>
            <td><?= $acc_title ?></td>
            <td class="center"><?= get_gl_account_name($myrow['account_code']) ?></td>
            <td class="right arabic">: المتعامل</td>
        </tr>

        <tr>
            <td>Description</td>
            <td class="center"><?= $myrow['description'] ?></td>
            <td class="right arabic">: المتعامل</td>
        </tr>





    </table>

    <div style="height: 525px; border: 1px solid black !important;">
        <table class="invoice-items">
            <caption style="margin: 5px">Particulars <span class="arabic">تفاصيل</span></caption>
            <tr class="heading">
                <td>
                    Sl. No<br><span class="arabic">الرقم</span>
                </td>

                <td>
                    Account<br><span class="arabic">الخدمات</span>
                </td>

                <td>
                    Description<br><span class="arabic">الخدمات</span>
                </td>
                <td>
                    Amount<br><span class="arabic">الكمية</span>
                </td>
            </tr>

            <?= $line_item_tr ?>


            <tr class="total">
                <td colspan="3" class="right"><b>Net Amount الاجمالي </b></td>

                <td class="right">
                    <?= number_format2($myrow['amount'],2) ?>
                </td>
            </tr>
        </table>
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


    <div><img src="images/footer-banner.jpg"></div>
</div>
</body>
</html>
