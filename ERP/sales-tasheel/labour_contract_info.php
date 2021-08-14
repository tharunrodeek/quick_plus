<?php


//phpinfo();
//die;

$url = 'https://www.mohre.gov.ae/services/AjaxHandler.asmx/LoadServiceResult';
//$data = array('keywords' => '773265', 'languageCode' => 'en-GB','languageId' => 1,'method' => 'CI');
//
//// use key 'http' even if you send the request to https://...
//$options = array(
//    'http' => array(
//        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
//        'method'  => 'POST',
//        'content' => http_build_query($data)
//    )
//);
//$context  = stream_context_create($options);
//$result = file_get_contents($url, false, $context);
//if ($result === FALSE) { /* Handle error */ }




$labour_contract_number = $_GET['lcno'];




$data = array('keywords' => $labour_contract_number, 'languageCode' => 'en-GB','languageId' => 1,'method' => 'CI');
//$data = array("name" => "Hagrid", "age" => "36");
$data_string = json_encode($data);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
);

$result = curl_exec($ch);

$result = json_decode($result);


//$response = curl_exec($ch);

echo json_encode($result);
exit;

//var_export($result);

?>




