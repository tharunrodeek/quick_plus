<?php 
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$site_url = "http://localhost/fa/244";
 //Employee Attendance API to connect and insert into the system
$post = [
    'email' => 'hruser',  // FA Admin Login
    'pwd' => '123456',  // FA Admin Password
    'company' => '0',   //the company installed,you can get this number from the config_db. Just get the array number
    'empl_id' => 101,  //Employee ID
    'data' => array('date' => "2018-02-03", 'in' => '09:05:00', 'out' => '06:35:00')
    //'data' => array('date' => "2018-02-13", 'in' => '2018-02-13 09:00:00', 'out' => '2018-02-13 06:30:00')
];
$module = 'UpdateSingleEmplAttendance'; 


$url = $site_url.'/modules/ExtendedHRM/api/'.$module;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
$response = curl_exec($ch); 

var_export($response); 

//$res = json_decode($response); 

//.print_r($res); 

//echo $res[1]->id;?>