
<?php include_once "helper.php"; ?>

<div class="kt-header__top">
    <div class="kt-container ">

        <!-- begin:: Brand -->
        <div class="kt-header__brand   kt-grid__item" id="kt_header_brand">
            <div class="kt-header__brand-logo">
                <a href="index.html">
                    <img alt="Logo" src="<?= $base_url ?>assets/media/logos/logo-10.png" class="kt-header__brand-logo-default"/>
                </a>
            </div>
            <div class="kt-header__brand-nav">

            </div>
        </div>


        <!--        AMC Expiry Notification-->
        <div style="width: 50%" class="kt-header__brand   kt-grid__item blink_3_times" id="common_notify_div">
<!--            <h4 style="color: red"><marquee>AWS Hosting expires soon. Please contact the service provider.</marquee></h4>-->

            <marquee id="common_notification"></marquee>

        </div>


        <?php

            $dim = $_GET['dim_id'];

            $logo = "ybc_logo.png";

            switch ($dim) {
                case 2:
                    $logo='amer_logo.jpg';
                    break;
                case 3:
                    $logo= 'tasheel_logo.JPG';
                    break;
                case 4:
                    $logo = 'rta_logo_long.png';
                    break;
                case 5:
                    $logo= 'dha_logo.png';
                    break;
                case 6:
                    $logo= 'ybc_logo.png';
                    break;
                case 7:
                    $logo= 'dubai_court_logo.jpg';
                    break;
                case 8:
                    $logo= 'ded_logo.png';
                    break;
                case 9:
                    $logo= 'al_adheed.png';
                    break;
                case 10:
                    $logo= 'amer_logo.jpg';
                    break;
                default:
                    $logo = "";
                    break;

            }

            if($_GET['show_items'] == 'ts')
                $logo= 'tasheel_logo.JPG';

        ?>

        <?php if(!empty($logo))  {?>

        <div class="kt-header__brand   kt-grid__item" id="kt_header_brand">
            <div class="kt-header__brand-logo">
                <a href="index.html">
                    <img style="width: auto !important;
    height: 92px;
    float: left;" alt="Logo" src="<?= $base_url ?>assets/images/<?= $logo ?>" class="kt-header__brand-logo-default"/>
                </a>
            </div>
            <div class="kt-header__brand-nav">

            </div>
        </div>

        <?php } ?>

<!--        <div class="kt-header__brand   kt-grid__item" id="kt_header_brand">-->
<!--            <div class="kt-header__brand-logo">-->
<!---->
<!--                    <img style="width: 25% !important;border-left: 1px solid #ccc;"-->
<!--                            alt="Logo" src="--><?//= $base_url ?><!--assets/images/--><?//= $logo ?><!--" class="kt-header__brand-logo-default"/>-->
<!---->
<!--            </div>-->
<!--        </div>-->

<!--        <div>-->
<!---->
<!--            Image-->
<!---->
<!--        </div>-->

        <!-- end:: Brand -->

        <!-- begin:: Header Topbar -->
        <div class="kt-header__topbar kt-grid__item kt-grid__item--fluid">

            <!--begin: Search -->

            <!--end: Search -->

            <!--end: Quick panel toggler -->

            <style>

                .notification {
                    /*background-color: #555;*/
                    color: white;
                    text-decoration: none;
                    /*padding: 15px 26px;*/
                    position: relative;
                    display: inline-block;
                    border-radius: 2px;
                }

                .notification:hover {
                    /*background: red;*/
                }

                .notification .badge {
                    position: absolute;
                    top: 7px;
                    right: -8px;
                    padding: 6px 9px;
                    border-radius: 76%;
                    background-color: red;
                    color: white;
                }

            </style>


            <div class="kt-header__topbar-item dropdown">
                <div id="notification_icon" class="notification kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px" aria-expanded="false">
                    <span class="kt-header__topbar-icon kt-header__topbar-icon--warning"><i class="flaticon2-bell-4"></i></span><span class="badge" id="notification_count">0</span>
                </div>


                <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-xl">
                    <form>

                        <!--begin: Head -->
                        <div class="kt-head kt-head--skin-light kt-head--fit-x kt-head--fit-b">
                            <h3 class="kt-head__title">
                                Notifications
<!--                                <span class="btn btn-label-primary btn-sm btn-bold btn-font-md">23 new</span>-->
                            </h3>
                            <ul style="display: none" class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand  kt-notification-item-padding-x" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active show" data-toggle="tab" href="#topbar_notifications_notifications" role="tab" aria-selected="true">Alerts</a>
                                </li>

                            </ul>
                        </div>

                        <!--end: Head -->
                        <div class="tab-content">
                            <div class="tab-pane active show" id="topbar_notifications_notifications" role="tabpanel">
                                <div id="notification_popup" class="kt-notification kt-margin-t-10 kt-margin-b-10 kt-scroll" data-scroll="true" data-height="300" data-mobile-height="200">

                                    <div id="no_new_notification_div" class="kt-grid kt-grid--ver" style="min-height: 200px;">
                                        <div class="kt-grid kt-grid--hor kt-grid__item kt-grid__item--fluid kt-grid__item--middle">
                                            <div class="kt-grid__item kt-grid__item--middle kt-align-center">
                                                All caught up!
                                                <br>No new notifications.
                                            </div>
                                        </div>
                                    </div>

                                </div>



                            </div>

                        </div>
                    </form>
                </div>


            </div>


<!--            Notification List Here-->








            <?php

            $language_flag = '012-uk.svg';

            $language = $_SESSION['wa_current_user']->prefs->user_language;
            if ($_SESSION['wa_current_user']->prefs->user_language == 'AR') {

                $language_flag = 'united-arab-emirates.svg';

             } ?>

            <!--begin: Language bar -->
            <div class="kt-header__topbar-item kt-header__topbar-item--langs">
                <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">
											<span class="kt-header__topbar-icon kt-header__topbar-icon--info">
												<img class="" src="<?= $base_url ?>assets/media/flags/<?= $language_flag ?>" alt=""/>
											</span>
                </div>
                <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim">
                    <ul class="kt-nav kt-margin-t-10 kt-margin-b-10">
                        <li class="kt-nav__item  <?php if($language == 'EN') echo 'kt-nav__item--active' ?> ">
                            <a href="#"  data-lang="EN" class="kt-nav__link axispro-lang-btn">
                                <span class="kt-nav__link-icon"><img src="<?= $base_url ?>assets/media/flags/012-uk.svg" alt=""/></span>
                                <span class="kt-nav__link-text"><?= trans('English') ?></span>
                            </a>
                        </li>
                        <li class="kt-nav__item <?php if($language == 'AR') echo 'kt-nav__item--active' ?>">
                            <a href="#"  data-lang="AR" class="kt-nav__link axispro-lang-btn">
                                <span class="kt-nav__link-icon"><img src="<?= $base_url ?>assets/media/flags/united-arab-emirates.svg" alt=""/></span>
                                <span class="kt-nav__link-text"><?= trans('Arabic') ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>


            <!--end: Language bar -->

            <!--begin: User bar -->
            <div class="kt-header__topbar-item kt-header__topbar-item--user">

                <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">

                    <img class="kt-hidden-" alt="Pic" src="<?= $base_url ?>assets/media/users/300_21.jpg"/>
                    <span class="kt-header__topbar-icon kt-header__topbar-icon--brand kt-hidden"><b>S</b></span>
                </div>
                <?php
                $style='';
                if(isset($_GET['application']))
                {
                    $style='style="cursor: pointer;font-weight: 500 !important;padding: 27px;"';
                }
                else
                {
                    $style='style="cursor: pointer;padding: 33px;"';
                }
 
                $display_name='';
                if($_SESSION['wa_current_user']->name!='')
                {
                    $display_name=$_SESSION['wa_current_user']->name;
                }
                else
                {
                    $display_name=$_SESSION['wa_current_user']->username;
                }
                ?>
                <div data-toggle="dropdown" >
                    <label <?php echo $style; ?>>Hi,  <?= $display_name; ?></label>
                </div>

                <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-xl">

                    <!--begin: Head -->
                    <div class="kt-user-card kt-user-card--skin-light kt-notification-item-padding-x">
                        <div class="kt-user-card__avatar">
                            <img class="kt-hidden-" alt="Pic" src="<?= $base_url ?>assets/media/users/300_25.jpg"/>

                            <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                            <span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold kt-hidden">S</span>
                        </div>
                        <div class="kt-user-card__name">

                            <?= $_SESSION['wa_current_user']->name ?>
                            <h5 style="padding: 2px;
    /* border: 1px solid #ccc; */
    border-top: 1px solid #ccc;
    font-size: 13px;
    font-weight: bold;"><?= $_SESSION['wa_current_user']->role_name ?></h5>
                            <span class="btn btn-label-primary btn-sm btn-bold btn-font-md"><a href="<?= $base_url ?>/ERP/admin/change_current_user_password.php"><?= trans('Reset Password') ?></a></span>

                        </div>



                        <div class="kt-user-card__badge">
                            <a style="cursor: pointer !important;" href="<?= $base_url ?>/ERP/access/logout.php?"><span class="btn btn-label-primary btn-sm btn-bold btn-font-md"><?= trans('Log Out') ?></span></a>
                        </div>
                    </div>


                    <!--end: Head -->

                    <!--begin: Navigation -->
<!--                    <div class="kt-notification">-->
<!--                        <a href="custom/apps/user/profile-1/personal-information.html"-->
<!--                           class="kt-notification__item">-->
<!--                            <div class="kt-notification__item-icon">-->
<!--                                <i class="flaticon2-calendar-3 kt-font-success"></i>-->
<!--                            </div>-->
<!--                            <div class="kt-notification__item-details">-->
<!--                                <div class="kt-notification__item-title kt-font-bold">-->
<!--                                    My Profile-->
<!--                                </div>-->
<!--                                <div class="kt-notification__item-time">-->
<!--                                    Account settings and more-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </a>-->
<!--                        <a href="custom/apps/user/profile-3.html" class="kt-notification__item">-->
<!--                            <div class="kt-notification__item-icon">-->
<!--                                <i class="flaticon2-mail kt-font-warning"></i>-->
<!--                            </div>-->
<!--                            <div class="kt-notification__item-details">-->
<!--                                <div class="kt-notification__item-title kt-font-bold">-->
<!--                                    My Messages-->
<!--                                </div>-->
<!--                                <div class="kt-notification__item-time">-->
<!--                                    Inbox and tasks-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </a>-->
<!--                        <a href="custom/apps/user/profile-2.html" class="kt-notification__item">-->
<!--                            <div class="kt-notification__item-icon">-->
<!--                                <i class="flaticon2-rocket-1 kt-font-danger"></i>-->
<!--                            </div>-->
<!--                            <div class="kt-notification__item-details">-->
<!--                                <div class="kt-notification__item-title kt-font-bold">-->
<!--                                    My Activities-->
<!--                                </div>-->
<!--                                <div class="kt-notification__item-time">-->
<!--                                    Logs and notifications-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </a>-->
<!--                        <a href="custom/apps/user/profile-3.html" class="kt-notification__item">-->
<!--                            <div class="kt-notification__item-icon">-->
<!--                                <i class="flaticon2-hourglass kt-font-brand"></i>-->
<!--                            </div>-->
<!--                            <div class="kt-notification__item-details">-->
<!--                                <div class="kt-notification__item-title kt-font-bold">-->
<!--                                    My Tasks-->
<!--                                </div>-->
<!--                                <div class="kt-notification__item-time">-->
<!--                                    latest tasks and projects-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </a>-->
<!--                        <a href="custom/apps/user/profile-1/overview.html" class="kt-notification__item">-->
<!--                            <div class="kt-notification__item-icon">-->
<!--                                <i class="flaticon2-cardiogram kt-font-warning"></i>-->
<!--                            </div>-->
<!--                            <div class="kt-notification__item-details">-->
<!--                                <div class="kt-notification__item-title kt-font-bold">-->
<!--                                    Billing-->
<!--                                </div>-->
<!--                                <div class="kt-notification__item-time">-->
<!--                                    billing & statements <span-->
<!--                                        class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill kt-badge--rounded">2 pending</span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </a>-->
<!--                        <div class="kt-notification__custom kt-space-between">-->
<!--                            <a href="custom/user/login-v2.html" target="_blank"-->
<!--                               class="btn btn-label btn-label-brand btn-sm btn-bold">Sign Out</a>-->
<!--                            <a href="custom/user/login-v2.html" target="_blank"-->
<!--                               class="btn btn-clean btn-sm btn-bold">Upgrade Plan</a>-->
<!--                        </div>-->
<!--                    </div>-->

                    <!--end: Navigation -->
                </div>
            </div>


            <!--end: User bar -->






        </div>

        <!-- end:: Header Topbar -->
    </div>
</div>
