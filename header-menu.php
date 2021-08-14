
<?php include_once "helper.php";

include_once($path_to_root . "/API/API_HRM_Call.php");
$object=new API_HRM_Call();
$is_display_hr_admin_menu=$object->display_hr_admin_menu();

/*public function HideMenu($access)
{

    if (!$_SESSION["wa_current_user"]->can_access($access))
        {
            return "hidden_elem";
        }
    else
        {
             return "";
        }

}*/
?>



<input type="hidden" id="date_format" value="<?= getDateFormat(); ?>">

<div class="kt-header__bottom">
    <div class="kt-container flexbox" style="width: 100% !important;">

        <!-- begin: Header Menu -->
        <button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i
                    class="la la-close"></i></button>
        <div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
            <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile ">
                <?php
                $flag=true;
                if($_SESSION['wa_current_user']->loginname=='9999')
                {
                    $flag=false;
                }

                ?>

                <ul class="kt-menu__nav ">
                    <li class="kt-menu__item  kt-menu__item--open   <?= setActiveMenu('dashboard') ?>  kt-menu__item--rel">
                        <a href="<?= $base_url ?>?application=dashboard" class="kt-menu__link"><span
                                    class="kt-menu__link-text"><i class="menu-icon flaticon-dashboard"></i><?= trans('Dashboard') ?></span><i
                                    class="kt-menu__ver-arrow la la-angle-right"></i></a>

                    </li>
                <?php if($_SESSION["wa_current_user"]->can_access('HEAD_MENU_SALES')): ?>
                    <li class="kt-menu__item <?= setActiveMenu('sales') ?> kt-menu__item--rel">
                        <a href="<?= $base_url ?>?application=sales" class="kt-menu__link"><span
                                    class="kt-menu__link-text"><i class="menu-icon flaticon-shopping-basket"></i><?= trans('SALES') ?></span><i
                                    class="kt-menu__ver-arrow la la-angle-right"></i></a>

                    </li>
                <?php endif; ?>
<!--                --><?php //if($_SESSION["wa_current_user"]->can_access('HEAD_MENU_PURCHASE')): ?>
<!--                    <li class="kt-menu__item --><?//= setActiveMenu('purchase') ?><!-- kt-menu__item--rel">-->
<!--                        <a href="--><?//= $base_url ?><!--?application=purchase" class="kt-menu__link"><span-->
<!--                                    class="kt-menu__link-text"><i class="menu-icon flaticon-shopping-basket"></i>--><?//= trans('PURCHASE') ?><!--</span><i-->
<!--                                    class="kt-menu__ver-arrow la la-angle-right"></i></a>-->
<!---->
<!--                    </li>-->
<!--                --><?php //endif; ?>

                 <?php /* if($_SESSION["wa_current_user"]->can_access('HEAD_MENU_ASSET')): ?>
                     <li class="kt-menu__item <?= setActiveMenu('fixed_assets') ?> kt-menu__item--rel">
                         <a href="<?= $base_url ?>?application=fixed_assets" class="kt-menu__link"><span
                                     class="kt-menu__link-text"><i class="menu-icon flaticon-diagram"></i><?= trans('FIXED ASSET') ?></span><i
                                     class="kt-menu__ver-arrow la la-angle-right"></i></a>
                     </li>
                 <?php endif;*/ ?>

                 <?php if($_SESSION["wa_current_user"]->can_access('HEAD_MENU_FINANCE')): ?>
                    <li class="kt-menu__item <?= setActiveMenu('finance') ?> kt-menu__item--rel">
                        <a href="<?= $base_url ?>?application=finance" class="kt-menu__link"><span
                                    class="kt-menu__link-text"><i class="menu-icon flaticon-diagram"></i><?= trans('FINANCE') ?></span><i
                                    class="kt-menu__ver-arrow la la-angle-right"></i></a>
                    </li>
                 <?php endif; ?>

                    <?php if(!in_array($_SESSION['wa_current_user']->access,[25,27,28])):  ?>

<!--                    <li class="kt-menu__item --><?//= setActiveMenu('hrm') ?><!-- kt-menu__item--rel">-->
<!--                        <a href="--><?//= $base_url ?><!--?application=hrm" class="kt-menu__link"><span-->
<!--                                    class="kt-menu__link-text"><i class="menu-icon flaticon-diagram"></i>--><?//= trans("ESS") ?><!--</span><i-->
<!--                                    class="kt-menu__ver-arrow la la-angle-right"></i></a>-->
<!--                    </li>-->

                    <?php endif; ?>

                     <?php //if($is_display_hr_admin_menu): ?>
                    <?php if($_SESSION["wa_current_user"]->can_access('HEAD_MENU_HR_ADMIN')): ?>
<!--					<li class="kt-menu__item --><?//= setActiveMenu('hr_admin') ?><!-- kt-menu__item--rel">-->
<!--                        <a href="--><?//= $base_url ?><!--?application=hr_admin" class="kt-menu__link"><span-->
<!--                                    class="kt-menu__link-text"><i class="fa fa-user"></i> --><?//= trans(" Admin") ?><!--</span><i-->
<!--                                    class="kt-menu__ver-arrow la la-angle-right"></i></a>-->
<!--                    </li>-->
					
                    <?php endif; ?>



                  <?php if($_SESSION["wa_current_user"]->can_access('HEAD_MENU_REPORT')): ?>
                    <li class="kt-menu__item <?= setActiveMenu('reports') ?> kt-menu__item--rel">
                        <a href="<?= $base_url ?>?application=reports" class="kt-menu__link"><span
                                    class="kt-menu__link-text"><i class="menu-icon flaticon2-files-and-folders"></i><?= trans('REPORT') ?></span><i
                                    class="kt-menu__ver-arrow la la-angle-right"></i></a>
                    </li>
                 <?php endif; ?>

                <?php if($_SESSION["wa_current_user"]->can_access('HEAD_MENU_SETTINGS')): ?>
                    <li class="kt-menu__item <?= setActiveMenu('setup') ?>  kt-menu__item--rel">
                        <a href="<?= $base_url ?>?application=settings" class="kt-menu__link">
                            <span class="kt-menu__link-text"><i class="menu-icon flaticon-settings-1"></i><?= trans('SETTINGS') ?></span><i
                                    class="kt-menu__ver-arrow la la-angle-right"></i></a>

                    </li>
                <?php endif; ?>



                </ul>
            </div>
        </div>

        <!-- end: Header Menu -->
    </div>
</div>

<style>

    .menu-icon {
        margin: 10px;
        font-size: 15px;
    }

    /*.kt-container.kt-grid__item.kt-grid__item--fluid
    {
        margin-top: 2%;
    }

    .kt-header__bottom
    {
        position: fixed;
        top: 60px;
        right: 0;
        left: 0;
        !*z-index: 97;*!
    }

    .kt-header .kt-header__top {
        height: 61px;
    }*/

</style>



