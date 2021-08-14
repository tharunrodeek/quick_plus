<?php

$path_to_root = "..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/prefs/sysprefs.inc");
include_once($path_to_root . "/includes/db/connect_db.inc");
include($path_to_root.'/BarcodeGenerator/BarcodeGenerator.php');
include($path_to_root.'/BarcodeGenerator/BarcodeGeneratorPNG.php');
ob_start();




date_default_timezone_set('Asia/Dubai');
function get_token($id)
{
    $sql = "SELECT * FROM 0_axis_front_desk where id = $id";
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

$token_id = $_GET['token_id'];
//$voucher_type = $_GET['voucher_type'];


$myrow = get_token($token_id);

$title = "TOKEN NUMBER : ".$myrow["token"];

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Token Print</title>

</head>

<body>

<div class="invoice-box arabic" style="border-bottom: none !important;">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="4">
                <table>
                    <tr>
                        <td class="title" style="text-align: center">
                            <img src="images/header-top.jpg" style="width:100%;">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <caption style="text-align: center;"><?= $title ?></caption>

        <tr>
            <td>CUSTOMER NAME:</td>
            <td class="center"><?= $myrow['display_customer'] ?></td>
            <td class="right arabic">: اسم الزبون</td>
        </tr>
        <tr>
            <td>MOBILE NUMBER:</td>
            <td class="center"><?= $myrow['customer_mobile'] ?></td>
            <td class="right arabic">: رقم الهاتف المحمول</td>
        </tr>

        <tr>
            <td>E-MAIL:</td>
            <td class="center"><?= $myrow['customer_email'] ?></td>
            <td class="right arabic">: البريد الإلكتروني</td>
        </tr>

        <tr>
            <td>TRN:</td>
            <td class="center"><?= $myrow['customer_trn'] ?></td>
            <td class="right arabic">: الرقم الضريبي</td>
        </tr>

    </table>




    <div><img src="images/footer-banner.jpg"></div>
</div>
</body>
</html>



<?php


$content = ob_get_clean();


//echo htmlspecialchars($content); die;

$path = "";
require_once $path . '../vendor/autoload.php';

try {
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->SetDisplayMode('fullpage');
    $stylesheet = file_get_contents('style.css');

//    $mpdf->SetWatermarkImage('images/water_mark.png',0.10,[120,170]);
//    $mpdf->showWatermarkImage = true;

    $mpdf->list_indent_first_level = 0; // 1 or 0 - whether to indent the first level of a list
    $mpdf->WriteHTML($stylesheet, 1); // The parameter 1 tells that this is css/style only and no body/html/text
    $mpdf->WriteHTML($content);
    $mpdf->Output("invoice-".$myrow['reference'].".pdf", \Mpdf\Output\Destination::INLINE);
}
catch (ErrorException $e) {
    die("Error occurred while preparing PDF");
}



