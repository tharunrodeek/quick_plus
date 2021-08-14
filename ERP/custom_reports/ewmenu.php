<?php

// Menu
$RootMenu = new cMenu("RootMenu", TRUE);
$current_user_role_id = CurrentUserInfo('role_id');


$RootMenu->AddMenuItem(95, "mi_invoice_report_view", $Language->MenuPhrase("95", "MenuText"), "invoice_report_viewlist.php", -1, "", IsLoggedIn() || AllowListMenu('{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}invoice_report_view'), FALSE, FALSE, "");
$RootMenu->AddMenuItem(98, "mi_invoice_report_detail_view", $Language->MenuPhrase("98", "MenuText"), "invoice_report_detail_viewlist.php", -1, "", IsLoggedIn() || AllowListMenu('{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}invoice_report_detail_view'), FALSE, FALSE, "");
//$RootMenu->AddMenuItem(115, "mi_invoice_report_for_ref_view", $Language->MenuPhrase("115", "MenuText"), "invoice_report_for_ref_viewlist.php", -1, "", IsLoggedIn() || AllowListMenu('{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}invoice_report_for_ref_view'), FALSE, FALSE, "");


if(in_array($current_user_role_id,array(9,2))) {

    $RootMenu->AddMenuItem(99, "mi_discount_report_view", $Language->MenuPhrase("99", "MenuText"), "discount_report_viewlist.php", -1, "", IsLoggedIn() || AllowListMenu('{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}discount_report_view'), FALSE, FALSE, "");
    $RootMenu->AddMenuItem(100, "mi_commission_report_view", $Language->MenuPhrase("100", "MenuText"), "commission_report_viewlist.php", -1, "", IsLoggedIn() || AllowListMenu('{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}commission_report_view'), FALSE, FALSE, "");
//    $RootMenu->AddMenuItem(101, "mi_pf_report_view", $Language->MenuPhrase("101", "MenuText"), "pf_report_viewlist.php", -1, "", IsLoggedIn() || AllowListMenu('{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}pf_report_view'), FALSE, FALSE, "");
//    $RootMenu->AddMenuItem(103, "mi_periodical_report_view", $Language->MenuPhrase("103", "MenuText"), "periodical_report_viewlist.php", -1, "", IsLoggedIn() || AllowListMenu('{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}periodical_report_view'), FALSE, FALSE, "");
//    $RootMenu->AddMenuItem(104, "mi_daily_report_view", $Language->MenuPhrase("104", "MenuText"), "daily_report_viewlist.php", -1, "", IsLoggedIn() || AllowListMenu('{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}daily_report_view'), FALSE, FALSE, "");
    $RootMenu->AddMenuItem(106, "mi_customer_commission_report_view", $Language->MenuPhrase("106", "MenuText"), "customer_commission_report_viewlist.php", -1, "", IsLoggedIn() || AllowListMenu('{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}customer_commission_report_view'), FALSE, FALSE, "");
    $RootMenu->AddMenuItem(111, "mi_voided_trans_reprt_view", $Language->MenuPhrase("111", "MenuText"), "voided_trans_reprt_viewlist.php", -1, "", IsLoggedIn() || AllowListMenu('{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}voided_trans_reprt_view'), FALSE, FALSE, "");

    $RootMenu->AddMenuItem(112, "mi_items_report_view", $Language->MenuPhrase("112", "MenuText"), "items_report_viewlist.php", -1, "", IsLoggedIn() || AllowListMenu('{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}items_report_view'), FALSE, FALSE, "");


}
else {
//    $RootMenu->AddMenuItem(110, "mi_single_user_transaction_report_view", $Language->MenuPhrase("110", "MenuText"), "single_user_transaction_report_viewlist.php", -1, "", IsLoggedIn() || AllowListMenu('{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}single_user_transaction_report_view'), FALSE, FALSE, "");
}

$RootMenu->AddMenuItem(113, "mi_invoice_payment_report", $Language->MenuPhrase("113", "MenuText"), "invoice_payment_reportlist.php", -1, "", IsLoggedIn() || AllowListMenu('{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}invoice_payment_report'), FALSE, FALSE, "");


$RootMenu->AddMenuItem(109, "mi_transaction_report_view", $Language->MenuPhrase("109", "MenuText"), "transaction_report_viewlist.php", -1, "", IsLoggedIn() || AllowListMenu('{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}transaction_report_view'), FALSE, FALSE, "");



echo $RootMenu->ToScript();

?>

<div class="ewVertical" id="ewMenu"></div>
