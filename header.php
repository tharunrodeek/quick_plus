<?php
$path_to_root = "ERP";
include_once("./ERP/includes/session.inc"); 
include_once("./ERP/includes/date_functions.inc");?>
<!DOCTYPE html>

<html lang="en">

<!-- begin::Head -->

<?php include "head.php";?>


<?php if ($_SESSION['wa_current_user']->prefs->user_language == 'AR') { ?>


<script>
    document.getElementsByTagName("html")[0].setAttribute("dir", "rtl");
</script>

<?php } ?>


<!-- end::Head -->

<!-- begin::Body -->
<body class="kt-page-content-white kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--transparent kt-page--loading">

<!-- begin:: Page -->

<!-- begin:: Header Mobile -->
<?php include "logo-top.php"; ?>

<!-- end:: Header Mobile -->
<div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper " id="kt_wrapper">

            <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed " data-ktheader-minimize="on">
                <?php include "header-top.php"; ?>
                <?php include "header-menu.php"; ?>

            </div>
