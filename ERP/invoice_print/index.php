<?php
$path_to_root = "..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/prefs/sysprefs.inc");
include_once($path_to_root . "/includes/db/connect_db.inc");
include($path_to_root.'/BarcodeGenerator/BarcodeGenerator.php');
include($path_to_root.'/BarcodeGenerator/BarcodeGeneratorPNG.php');
ob_start();
include('content.php');
$content = ob_get_clean();

//echo $content; die;

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

    $mpdf->setFooter('<div style="font-weight: normal; font-size: 12px">Powered by - &copy; www.axisproerp.com</div>');


    $file_name = "invoice_".str_replace('/', '_', $myrow['reference']).".pdf";

    $file_path = getcwd();

    $is_email = $_GET['PARAM_3'] == 0 ? false : true;

    if($is_email) {

        $mpdf->Output($file_name, \Mpdf\Output\Destination::FILE);

        include_once "../API/API_Call.php";

        $email = $customer_info['debtor_email'];

        if(empty($email))
            display_error("Email not defined for this customer. Can't send email.!");

        $api = new API_Call();

        $company_name = $SysPrefs->prefs['coy_name'];

        $email_content = "<p>Dear ".$customer_info['name']. ",<br><br>";
        $email_content.= " Please find the attached invoice";
        $email_content.= "<br><br>";
        $email_content.= "Thanks & Regards<br>";
        $email_content.= "$company_name<br>";

        $send_mail = $api->Send_Email($email,"Invoice - ".$company_name,$email_content,[$file_path."/".$file_name]);

        if($send_mail){
            display_notification_centered("Invoice sent to $email");
        }

    }
    else {
        $mpdf->Output($file_name, \Mpdf\Output\Destination::INLINE);
    }



}
catch (ErrorException $e) {
    die("Error occurred while preparing PDF");
}


