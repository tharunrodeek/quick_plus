<?php
$path_to_root = "..";
include_once( $path_to_root ."/includes/session.inc");
include_once( $path_to_root ."/includes/prefs/sysprefs.inc");
include_once($path_to_root ."/includes/db/connect_db.inc");

//echo 'haii22333';
$path = "";
 require_once $path . '../vendor/autoload.php';
 ob_start();
include('content_gl.php');
$content = ob_get_clean();

 
try {
	  $stylesheet = file_get_contents('style.css');
$mpdf = new \Mpdf\Mpdf();
$mpdf->SetDisplayMode('fullpage');
$stylesheet = file_get_contents('style.css');
$mpdf->list_indent_first_level = 0; 
$mpdf->WriteHTML($stylesheet, 1); 
$mpdf->WriteHTML($content);
$mpdf->setFooter('<div style="font-weight: normal; font-size: 12px">Powered by - &copy; www.axisproerp.com</div>');

$mpdf->Output("invoice.pdf", \Mpdf\Output\Destination::INLINE);

  }
  catch (ErrorException $e) {
    die("Error occurred while preparing PDF");
}


