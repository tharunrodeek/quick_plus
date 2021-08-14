<?php

$path_to_root = "..";
include_once($path_to_root . "/includes/session.inc");
include($path_to_root.'/BarcodeGenerator/BarcodeGenerator.php');
include($path_to_root.'/BarcodeGenerator/BarcodeGeneratorPNG.php');
require_once __DIR__ . '/../vendor/autoload.php';

check_page_security('SA_SALESINVOICE');

try {
    $contents = getContents();
    $mpdf = new \Mpdf\Mpdf([
        "margin_left"     => 10,
        "margin_right"    => 10,
        "margin_top"      => 10,
        "margin_bottom"   => 10,
        "margin_header"   => 5,
        "margin_footer"   => 5,
    ]);
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->WriteHTML(file_get_contents('../../assets/css/mpdf_default.css'), 1);
    $mpdf->WriteHTML($contents);
    $mpdf->SetTitle('Service Request - Axispro ERP');
    $footer_html = '<div class="text-right w-100">Powered by - &copy; www.axisproerp.com</div>';
    $mpdf->SetHTMLFooter($footer_html, 'O');
    $mpdf->SetHTMLFooter($footer_html, 'E');
    $mpdf->Output("service-request.pdf", \Mpdf\Output\Destination::INLINE);
}
catch (Exception $e) {
    // die("Error occurred while preparing PDF");
    throw $e;
}

/**
 * Returns the HTML content of the service request.
 *
 * @return string
 */
function getContents() {
    /** Validate id */
    if (empty($_GET['id']) || !preg_match('/^[1-9][0-9]*$/', $_GET['id'])){
        http_response_code(404);
        echo "The requested resource could not be found";
        exit;
    }
    $srv_req = db_query(
        "SELECT 
            srv_req.token_number,
            srv_req.reference,
            srv_req.created_at,
            srv_req.display_customer,
            srv_req.mobile,
            IF(user.real_name = ' ', user.user_id, user.real_name) employee,srv_req.barcode
        FROM 
            0_service_requests srv_req
            LEFT JOIN 0_users user ON srv_req.created_by = user.id
        WHERE srv_req.id = {$_GET['id']}"
    )->fetch_assoc();
    if (!$srv_req) {
        http_response_code(404);
        echo "The requested resource could not be found";
        exit;
    }

    $srv_req['created_at'] = DateTime::createFromFormat(MYSQL_DATE_TIME_FORMAT, $srv_req['created_at'])->format(getDateFormatInNativeFormat() . ' h:i A');
    
    $srv_req['_items'] = [];
    $srv_req['_gross_amt'] = 0.00;
    $srv_req['_discount_amt'] = 0.00;
    $result = db_query(
        "SELECT item.* FROM 0_service_request_items item where item.req_id = {$_GET['id']}"
    );
    $_delimeter = '&nbsp;&nbsp;&nbsp;&nbsp;';
    while($item = $result->fetch_assoc()){
        $_extra = [];
            !empty($item['application_id'])     && $_extra[] = $item['application_id'];
            !empty($item['ref_name'])           && $_extra[] = $item['ref_name'];
        $_fee = (float)$item['bank_service_charge'] +
            (float)$item['govt_fee']+
            (float)$item['bank_service_charge_vat']+(float)$item['extra_service_charge'];
        $_total = (int)$item['qty'] * (
                $_fee
            +   (float)$item['price']
            +   (float)$item['unit_tax']
        );
        
        $item['_extra'] = implode($_delimeter, $_extra);
        $item['_fee']   = $_fee;
        $item['_total'] = round2($_total, 2);

        $srv_req['_items'][] = $item;
        $srv_req['_gross_amt'] += $item['_total'];
        $srv_req['_discount_amt'] += round2((int)$item['qty'] * (float)$item['discount'], 2);
    }
    $srv_req['_net_amt'] = $srv_req['_gross_amt'] - $srv_req['_discount_amt'];
    return html(compact('srv_req'));
}

/**
 * Generate the html in an isolated env
 *
 * @param array $__GLOBALS__ The array of variables which are globally available in the content.php
 * @return string
 */
function html($__GLOBALS__) {
    extract($__GLOBALS__);
    ob_start();
    include __DIR__ . '/content.php';
    return ob_get_clean();
}