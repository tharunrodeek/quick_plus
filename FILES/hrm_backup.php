<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <!-- begin:: Content Head -->


            <!-- end:: Content Head -->

            <!-- begin:: Content -->
            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('Transactions') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--warning kt-iconbox--animate-fast">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-file-invoice fa-4x kt-font-warning"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('apply_leave')?>"><?= trans('Apply Leave') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Apply Leave') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--success kt-iconbox--animate-slow">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-check-square fa-4x kt-font-success"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('leave_manage') ?>"><?= trans('Leave Request & Approval') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Leave Request & Approval') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3"  >
                        <div class="kt-portlet kt-iconbox kt-iconbox--info kt-iconbox--animate-fast">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-book fa-4x kt-font-info"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('attendance_entry') ?>"><?= trans('Manuval Attendance Entry') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Manuval Attendance Entry') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3"  >
                        <div class="kt-portlet kt-iconbox kt-iconbox--warning kt-iconbox--animate-fast">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-book fa-4x kt-font-warning"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('single_attendance_entry') ?>"><?= trans('Single Click Attendance Entry') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Single Click Attendance Entry') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3"  >
                        <div class="kt-portlet kt-iconbox kt-iconbox--success kt-iconbox--animate-slow">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-upload  fa-4x kt-font-success"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('upload_attendance') ?>"><?= trans('Upload Attendence') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Upload Attendence') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--info kt-iconbox--animate-fast">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">

                                        <i class="fa fa-calendar-alt fa-4x kt-font-info"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('timesheet') ?>"><?= trans('TimeSheet') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('TimeSheet' )?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--success kt-iconbox--animate-slow">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">

                                        <i class="fa fa-user-clock fa-4x kt-font-success"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('overtime_approve') ?>"><?= trans('Approve OverTime') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Approve OverTime' )?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--warning kt-iconbox--animate-fast">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-file-invoice fa-4x kt-font-warning"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('payslip_entry') ?>"><?= trans('Generate Payroll') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Payroll' )?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!--<div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--success kt-iconbox--animate-slow">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-file-invoice-dollar fa-4x kt-font-success"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?/*= route('process_slip') */?>"><?/*= trans('Process Payroll') */?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?/*= trans('Process PayRoll')*/?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>-->

                    <div class="col-lg-3" style="display: none">
                        <div class="kt-portlet kt-iconbox kt-iconbox--success kt-iconbox--animate-slow">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">

                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"/>
                                                <path d="M16,17 L16,21 C16,21.5522847 15.5522847,22 15,22 L9,22 C8.44771525,22 8,21.5522847 8,21 L8,17 L5,17 C3.8954305,17 3,16.1045695 3,15 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,15 C21,16.1045695 20.1045695,17 19,17 L16,17 Z M17.5,11 C18.3284271,11 19,10.3284271 19,9.5 C19,8.67157288 18.3284271,8 17.5,8 C16.6715729,8 16,8.67157288 16,9.5 C16,10.3284271 16.6715729,11 17.5,11 Z M10,14 L10,20 L14,20 L14,14 L10,14 Z" fill="#000000"/>
                                                <rect fill="#000000" opacity="0.3" x="8" y="2" width="8" height="2" rx="1"/>
                                            </g>
                                        </svg>								</div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('attendance_entry') ?>"><?= trans('Attendance Entry') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Attendance Entry') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!--End::Row-->
                <!--End::Row-->
                <!--End::Dashboard 2-->
                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('Maintainance') ?></h3>
                        </div>
                    </div>
                </div>
               <!------------Start Row------------>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--warning kt-iconbox--animate-slow">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-user-tie fa-4x kt-font-warning"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('manage_employee') ?>"><?= trans('Add/Manage Employee') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Add/Manage Employee') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--info kt-iconbox--animate-fast">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-cog fa-4x kt-font-info"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('default_settings') ?>"><?= trans('Default Settings') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Deafult Settings') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--success kt-iconbox--animate-slow">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-file fa-4x kt-font-info"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('manage_emp_docs') ?>"><?= trans('Manage Employee Documents') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Manage Employee Documents') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--<div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--warning kt-iconbox--animate-slow">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-cog fa-4x kt-font-warning"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?/*= route('attendance_sett') */?>"><?/*= trans('Attendance Settings') */?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?/*= trans('Attendance Settings') */?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>-->

                </div>

                <!--<div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?/*= trans('Reports') */?></h3>
                        </div>
                    </div>
                </div>-->

                <!--<div class="row">
                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--info kt-iconbox--animate-fast">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-user-shield fa-4x kt-font-info"></i>								</div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="#"><?/*= trans('Employee Reports') */?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?/*= trans('Add/Manage Employee') */?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>-->



                <!----END ROW--------->
                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('Master Modules') ?></h3>
                        </div>
                    </div>
                </div>
          <!-----------------------START ROW-------------------->
                <div class="row">
                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--success kt-iconbox--animate-slow">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-list fa-4x fa fa-chart-bar fa-4x kt-font-success"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('manage_department') ?>"><?= trans('Department') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Department Master') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--info kt-iconbox--animate-fast">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-project-diagram fa-4x kt-font-info"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('manage_divisions') ?>"><?= trans('Divisions') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Manage Divisions') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--warning kt-iconbox--animate-slow">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-user-graduate fa-4x kt-font-warning"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('manage_designations') ?>"><?= trans('Designations') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Manage Designations') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--success kt-iconbox--animate-slow">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-file-alt fa-4x kt-font-success"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('manage_docs') ?>"><?= trans('Document') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Document') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--info kt-iconbox--animate-fast">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-list fa-4x fa fa-chart-bar fa-4x kt-font-info"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('leave_types') ?>"><?= trans('Leave') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Leave') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--warning kt-iconbox--animate-slow">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-list fa-4x fa fa-chart-bar fa-4x kt-font-warning"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('pay_elements') ?>"><?= trans('Pay Elements') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Pay Elements') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--success kt-iconbox--animate-slow">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-business-time fa-4x  kt-font-success"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('shifts') ?>"><?= trans('Shifts') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Shifts') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--warning kt-iconbox--animate-slow">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                        <i class="fa fa-users fa-4x  kt-font-warning"></i>
                                    </div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" href="<?= route('assign_shifts') ?>"><?= trans('Assign Shifts To Employees') ?></a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            <?= trans('Assign Shifts To Employees') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>




                </div>
          <!-------------------------END ROW--------------------->
            </div>
            <!-- end:: Content -->
        </div>
    </div>
</div>