<?php
$path_to_root = "..";
include_once( $path_to_root ."/includes/session.inc");
include_once( $path_to_root ."/includes/prefs/sysprefs.inc");
include_once($path_to_root ."/includes/db/connect_db.inc");
include($path_to_root . "/purchasing/includes/po_class.inc");
$path = "";
include($path_to_root .'/custom_print/mpdf/vendor/autoload.php');
ob_start();
include('content_pr.php');
$content = ob_get_clean();

//print_r($content);


//die;

$stylesheet = file_get_contents('style.css');
$mpdf = new \Mpdf\Mpdf();
$mpdf->SetDisplayMode('fullpage');
$stylesheet = file_get_contents('style.css');

//    $mpdf->SetWatermarkImage('images/water_mark.png',0.10,[120,170]);
//    $mpdf->showWatermarkImage = true;

$mpdf->list_indent_first_level = 0; // 1 or 0 - whether to indent the first level of a list
$mpdf->WriteHTML($stylesheet, 1); // The parameter 1 tells that this is css/style only and no body/html/text
$mpdf->WriteHTML($content);
$mpdf->setFooter('<div style="font-weight: normal; font-size: 12px">Powered by - &copy; www.axisproerp.com</div>');

$mpdf->Output("invoice.pdf", \Mpdf\Output\Destination::INLINE);

