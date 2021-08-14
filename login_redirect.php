<?php include "header.php";


$user_id = $_SESSION["wa_current_user"]->user;

$user_info = get_user($user_id);

$startup_tab = $user_info['startup_tab'];

if(empty($startup_tab))
    $startup_tab = "dashboard";


/** Store Login Log */

$user_ip = $_SERVER['REMOTE_ADDR'];
db_insert("0_activity_log",[
    'log_type' => ACT_LOG_LOGIN,
    'user_id' => $user_id,
    'description' => db_escape("User is logged in"),
    'user_ip' => db_escape($user_ip),
]);


header("Location: $base_url?application=$startup_tab");

exit();