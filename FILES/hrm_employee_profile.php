<?php
include "header.php";

include_once($path_to_root . "/API/API_HRM_Call.php");
$object=new API_HRM_Call();
$emp_data=$object->get_employee_info();

$leave_details=$object->leave_details();

$path='ERP/company/0/images/empl/';
if($emp_data[0]['gender']=='1')
{
    if($leave_details['img_pth']=='')
    {
        $img_path=$path.'avatar_male.png';
    }
    else
    {
        $img_path=$path.$leave_details['img_pth'];
    }
}
else if($emp_data[0]['gender']=='2')
{
    if($leave_details['img_pth']=='')
    {
        $img_path=$path.'avatar_girl.png';
    }
    else
    {
        $img_path=$path.$leave_details['img_pth'];
    }
}
else
{
    $img_path=$path.'avatar_male.png';
}




?>
<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
            <div class="kt-container  kt-grid__item kt-grid__item--fluid">
                <div class="row">
                    <div class="kt-portlet">
                        <div class="kt-portlet__body" style="padding: 5px !important;">
                            <div class="row">

                                    <!--begin::Aside-->
                                    <div class="flex-row-auto offcanvas-mobile w-250px w-xxl-350px" id="kt_profile_aside">
                                        <!--begin::Profile Card-->
                                        <div class="card card-custom card-stretch">
                                            <!--begin::Body-->
                                            <div class="card-body pt-4" style="height: 479px;">
                                                <!--begin::Toolbar-->
                                                <div class="d-flex justify-content-end">
                                                    <div class="dropdown dropdown-inline">
                                                        <a href="#" class="btn btn-clean btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="ki ki-bold-more-hor"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                            <!--begin::Navigation-->
                                                            <ul class="navi navi-hover py-5">
                                                                <li class="navi-item">
                                                                    <a href="#" class="navi-link">
                                                                        <span class="navi-icon"><i class="flaticon2-drop"></i></span>
                                                                        <span class="navi-text">New Group</span>
                                                                    </a>
                                                                </li>
                                                                <li class="navi-item">
                                                                    <a href="#" class="navi-link">
                                                                        <span class="navi-icon"><i class="flaticon2-list-3"></i></span>
                                                                        <span class="navi-text">Contacts</span>
                                                                    </a>
                                                                </li>
                                                                <li class="navi-item">
                                                                    <a href="#" class="navi-link">
                                                                        <span class="navi-icon"><i class="flaticon2-rocket-1"></i></span>
                                                                        <span class="navi-text">Groups</span>
                                                                        <span class="navi-link-badge">
                                                   <span class="label label-light-primary label-inline font-weight-bold">new</span>
                                                   </span>
                                                                    </a>
                                                                </li>
                                                                <li class="navi-item">
                                                                    <a href="#" class="navi-link">
                                                                        <span class="navi-icon"><i class="flaticon2-bell-2"></i></span>
                                                                        <span class="navi-text">Calls</span>
                                                                    </a>
                                                                </li>
                                                                <li class="navi-item">
                                                                    <a href="#" class="navi-link">
                                                                        <span class="navi-icon"><i class="flaticon2-gear"></i></span>
                                                                        <span class="navi-text">Settings</span>
                                                                    </a>
                                                                </li>
                                                                <li class="navi-separator my-3"></li>
                                                                <li class="navi-item">
                                                                    <a href="#" class="navi-link">
                                                                        <span class="navi-icon"><i class="flaticon2-magnifier-tool"></i></span>
                                                                        <span class="navi-text">Help</span>
                                                                    </a>
                                                                </li>
                                                                <li class="navi-item">
                                                                    <a href="#" class="navi-link">
                                                                        <span class="navi-icon"><i class="flaticon2-bell-2"></i></span>
                                                                        <span class="navi-text">Privacy</span>
                                                                        <span class="navi-link-badge">
                                                   <span class="label label-light-danger label-rounded font-weight-bold">5</span>
                                                   </span>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                            <!--end::Navigation-->
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end::Toolbar-->
                                                <!--begin::User-->
                                                <div class="d-flex align-items-center" style="margin-top: -13%;">
                                                    <div class="symbol symbol-60 symbol-xxl-100 mr-5 align-self-start align-self-xxl-center">
                                                        <div class="symbol-label" style="background-image:url('<?php echo $img_path; ?>')"></div>
                                                        <i class="symbol-badge bg-success"></i>
                                                    </div>
                                                    <div>
                                                        <a href="#" class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary">
                                                            <?php echo $emp_data[0]['EmpName'] ?>
                                                        </a>
                                                        <div class="text-muted">
                                                            <?php echo $emp_data[0]['description'] ?>
                                                        </div>
                                                        <div class="mt-2">
                                                            <!--<a href="#" class="btn btn-sm btn-primary font-weight-bold mr-2 py-2 px-3 px-xxl-5 my-1">Chat</a>
                                                               <a href="#" class="btn btn-sm btn-success font-weight-bold py-2 px-3 px-xxl-5 my-1">Follow</a>-->
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end::User-->
                                                <!--begin::Contact-->
                                                <div class="py-9" style="margin-top:8%;">
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <span class="font-weight-bold mr-2">Email:</span>
                                                        <a href="#" class="text-muted text-hover-primary"><?php echo $emp_data[0]['email'] ?></a>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <span class="font-weight-bold mr-2">Phone:</span>
                                                        <span class="text-muted"><?php echo $emp_data[0]['mobile_phone'] ?></span>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="font-weight-bold mr-2">Location:</span>
                                                        <span class="text-muted"><?php echo $emp_data[0]['local_name'] ?></span>
                                                    </div>
                                                </div>
                                                <!--end::Contact-->
                                                <!--begin::Nav-->
                                                <div class="navi navi-bold navi-hover navi-active navi-link-rounded" style="margin-top: 8%;">
                                                    <div class="navi-item mb-2">

                                             <span class="navi-icon mr-2">
                                                <span class="svg-icon">
                                                   <!--begin::Svg Icon | path:/metronic/themes/metronic/theme/html/demo1/dist/assets/media/svg/icons/General/User.svg-->
                                                   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                         <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                         <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                         <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                                                      </g>
                                                   </svg>
                                                    <!--end::Svg Icon-->
                                                </span>
                                             </span>
                                                            <span class="navi-text font-size-lg">
                                             Personal Informationnn
                                             </span>

                                                    </div>
                                                    <div class="navi-item mb-2">

                                             <span class="navi-icon mr-2">
                                                <span class="svg-icon">
                                                   <!--begin::Svg Icon | path:/metronic/themes/metronic/theme/html/demo1/dist/assets/media/svg/icons/Code/Compiling.svg-->
                                                   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                         <rect x="0" y="0" width="24" height="24"></rect>
                                                         <path d="M2.56066017,10.6819805 L4.68198052,8.56066017 C5.26776695,7.97487373 6.21751442,7.97487373 6.80330086,8.56066017 L8.9246212,10.6819805 C9.51040764,11.267767 9.51040764,12.2175144 8.9246212,12.8033009 L6.80330086,14.9246212 C6.21751442,15.5104076 5.26776695,15.5104076 4.68198052,14.9246212 L2.56066017,12.8033009 C1.97487373,12.2175144 1.97487373,11.267767 2.56066017,10.6819805 Z M14.5606602,10.6819805 L16.6819805,8.56066017 C17.267767,7.97487373 18.2175144,7.97487373 18.8033009,8.56066017 L20.9246212,10.6819805 C21.5104076,11.267767 21.5104076,12.2175144 20.9246212,12.8033009 L18.8033009,14.9246212 C18.2175144,15.5104076 17.267767,15.5104076 16.6819805,14.9246212 L14.5606602,12.8033009 C13.9748737,12.2175144 13.9748737,11.267767 14.5606602,10.6819805 Z" fill="#000000" opacity="0.3"></path>
                                                         <path d="M8.56066017,16.6819805 L10.6819805,14.5606602 C11.267767,13.9748737 12.2175144,13.9748737 12.8033009,14.5606602 L14.9246212,16.6819805 C15.5104076,17.267767 15.5104076,18.2175144 14.9246212,18.8033009 L12.8033009,20.9246212 C12.2175144,21.5104076 11.267767,21.5104076 10.6819805,20.9246212 L8.56066017,18.8033009 C7.97487373,18.2175144 7.97487373,17.267767 8.56066017,16.6819805 Z M8.56066017,4.68198052 L10.6819805,2.56066017 C11.267767,1.97487373 12.2175144,1.97487373 12.8033009,2.56066017 L14.9246212,4.68198052 C15.5104076,5.26776695 15.5104076,6.21751442 14.9246212,6.80330086 L12.8033009,8.9246212 C12.2175144,9.51040764 11.267767,9.51040764 10.6819805,8.9246212 L8.56066017,6.80330086 C7.97487373,6.21751442 7.97487373,5.26776695 8.56066017,4.68198052 Z" fill="#000000"></path>
                                                      </g>
                                                   </svg>
                                                    <!--end::Svg Icon-->
                                                </span>
                                             </span>
                                                            <span class="navi-text font-size-lg">
                                             Account Information
                                             </span>

                                                    </div>
                                                    <div class="navi-item mb-2">

                                             <span class="navi-icon mr-2">
                                                <span class="svg-icon">
                                                   <!--begin::Svg Icon | path:/metronic/themes/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Shield-user.svg-->
                                                   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                         <rect x="0" y="0" width="24" height="24"></rect>
                                                         <path d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z" fill="#000000" opacity="0.3"></path>
                                                         <path d="M12,11 C10.8954305,11 10,10.1045695 10,9 C10,7.8954305 10.8954305,7 12,7 C13.1045695,7 14,7.8954305 14,9 C14,10.1045695 13.1045695,11 12,11 Z" fill="#000000" opacity="0.3"></path>
                                                         <path d="M7.00036205,16.4995035 C7.21569918,13.5165724 9.36772908,12 11.9907452,12 C14.6506758,12 16.8360465,13.4332455 16.9988413,16.5 C17.0053266,16.6221713 16.9988413,17 16.5815,17 C14.5228466,17 11.463736,17 7.4041679,17 C7.26484009,17 6.98863236,16.6619875 7.00036205,16.4995035 Z" fill="#000000" opacity="0.3"></path>
                                                      </g>
                                                   </svg>
                                                    <!--end::Svg Icon-->
                                                </span>
                                             </span>
                                                            <span class="navi-text font-size-lg">
                                             Leave Details
                                             </span>
                                                            <span class="navi-label">
                                             </span>

                                                    </div>
                                                </div>
                                                <!--end::Nav-->
                                            </div>
                                            <!--end::Body-->
                                        </div>
                                        <!--end::Profile Card-->
                                    </div>
                                    <!--end::Aside-->
                                    <!--begin::Content-->
                                    <div class="col-md-9">
                                        <!--begin::Card-->
                                        <div class="card card-custom" data-toggle="collapse" href="#personalInfo" style="cursor:pointer;">
                                            <!--begin::Header-->
                                            <div class="card-header py-3">
                                                <div class="card-title align-items-start flex-column">
                                                    <h3 class="card-label font-weight-bolder text-dark">Personal Information</h3>
                                                    <span class="text-muted font-weight-bold font-size-sm mt-1">Find your details</span>
                                                </div>

                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Form-->
                                            <form class="form">
                                                <div class="card-body collapse" id="personalInfo">
                                                    <!--begin::Alert-->
                                                    <div class="alert alert-custom alert-light-danger fade show mb-10" role="alert" style="    display: none;">


                                                        <div class="alert-close">
                                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true"><i class="ki ki-close"></i></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <!--end::Alert-->
                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">First Name</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label>  <?php echo $emp_data[0]['empl_firstname'] ?></label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Last Name</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label>  <?php  if(empty($emp_data[0]['empl_lastname'])) {echo 'N/A';}else { echo $emp_data[0]['empl_lastname'];} ?></label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">DOB</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label>  <?php echo sql2date($emp_data[0]['date_of_birth']) ?></label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Email</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label>  <?php echo $emp_data[0]['email'] ?></label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Date Of Join</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label>  <?php echo sql2date($emp_data[0]['joining']) ?></label>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Probationary Period</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label>  <?php echo $emp_data[0]['joining'] ?></label>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Contract Period</label>
                                                        <div class="col-lg-9 col-xl-6">

                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <!--end::Form-->
                                        </div>




                                        <div class="card card-custom"  data-toggle="collapse" href="#accountDetails" style="cursor:pointer;">
                                            <!--begin::Header-->
                                            <div class="card-header py-3">
                                                <div class="card-title align-items-start flex-column">
                                                    <h3 class="card-label font-weight-bolder text-dark">Account Details</h3>
                                                    <span class="text-muted font-weight-bold font-size-sm mt-1">View Account Info</span>
                                                </div>

                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Form-->
                                            <form class="form">
                                                <div class="card-body collapse" id="accountDetails">
                                                    <!--begin::Alert-->

                                                    <!--end::Alert-->
                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Mode of Pay</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                           <label><?php echo $emp_data[0]['email']; ?></label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Bank Name</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label><?php echo $emp_data[0]['bank_name']; ?></label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Bank Account No</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label><?php echo $emp_data[0]['acc_no']; ?></label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Branch</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label><?php if(empty($emp_data[0]['branch_detail'])){ echo 'N/A';} else {echo $emp_data[0]['branch_detail'];} ?></label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">SWIFT Code</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label><?php if(empty($emp_data[0]['ifsc'])){echo 'N/A';}else {echo $emp_data[0]['ifsc'];}  ?></label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">IBAN</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label><?php if(empty($emp_data[0]['iban'])){echo 'N/A';}else {echo $emp_data[0]['iban'];}  ?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <!--end::Form-->
                                        </div>




                                        <div class="card card-custom"  data-toggle="collapse" href="#leaveDetails" style="cursor:pointer;">
                                            <!--begin::Header-->
                                            <div class="card-header py-3">
                                                <div class="card-title align-items-start flex-column">
                                                    <h3 class="card-label font-weight-bolder text-dark">Leave Details</h3>
                                                    <span class="text-muted font-weight-bold font-size-sm mt-1">View Leave Info</span>
                                                </div>

                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Form-->
                                            <form class="form">
                                                <div class="card-body collapse" id="leaveDetails">
                                                    <!--begin::Alert-->

                                                    <!--end::Alert-->
                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Earned Leaves</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label><?php echo $leave_details['Elg_leave']; ?></label>
                                                        </div>
                                                    </div>
                                                   <!-- <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Casual Leaves Taken</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label><?php /*echo $emp_data[0]['bank_name']; */?></label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Annuval Leave Taken</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label><?php /*echo $emp_data[0]['acc_no']; */?></label>
                                                        </div>
                                                    </div>-->
                                                    <div class="form-group row">
                                                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Total Leaves Taken</label>
                                                        <div class="col-lg-9 col-xl-6">
                                                            <label><?php echo $leave_details['Tot_leave']; ?></label>
                                                        </div>
                                                    </div>

                                                </div>
                                            </form>
                                            <!--end::Form-->
                                        </div>

                                        <?php
                                        include_once($path_to_root . "/API/API_HRM_Document_Request.php");
                                        $call_obj=new API_HRM_Document_Request();
                                        ?>

                                        <div class="card card-custom"  data-toggle="collapse" href="#warningDetails" style="cursor:pointer;">

                                        <div class="card-header py-3">
                                            <div class="card-title align-items-start flex-column">
                                                <h3 class="card-label font-weight-bolder text-dark">Warings Issued</h3>
                                                <span class="text-muted font-weight-bold font-size-sm mt-1">Issued warnings against employee</span>
                                            </div>

                                        </div>

                                        <form class="form">
                                            <div class="card-body collapse" id="warningDetails" style="height: 330px;
    overflow: auto;">
                                                <?php

                                                   echo $call_obj->warnings_issued();

                                                ?>

                                            </div>
                                        </form>

                                    </div>


                                        <div class="card card-custom"  data-toggle="collapse" href="#PrivacyDetails" style="cursor:pointer;">

                                            <div class="card-header py-3">
                                                <div class="card-title align-items-start flex-column">
                                                    <h3 class="card-label font-weight-bolder text-dark">Privacy Policy & Code Of Conduct</h3>
                                                    <span class="text-muted font-weight-bold font-size-sm mt-1">View Company policy and code of conduct</span>
                                                </div>

                                            </div>

                                            <form class="form">
                                                <div class="card-body collapse" id="PrivacyDetails" style="height: 330px;
    overflow: auto;">
                                                    <?php

                                                    echo $call_obj->get_policy_and_code_of_conduct();

                                                    ?>

                                                </div>
                                            </form>

                                        </div>




                                    </div>
                                    <!--end::Content-->

                            </div>
                        </div>
                        <!--end::Form-->
                    </div>
                </div>
            </div>
            <!-- end:: Content -->
        </div>
    </div>
</div>
<style>
    .symbol-label
    {
        width: 60px;
        height: 60px;
        background-color: #f3f6f9;
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        border-radius: .42rem;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .text-alert
    {
        font-weight:bold !important;
    }


    .card-custom .accordion-toggle:after {
        font-family: 'Material Icons';
        content: "expand_less";
        float: right;
        color: grey;
    }

    .card-custom .accordion-toggle.collapsed:after {
        font-family: 'Material Icons';
        content: "expand_more";
    }
</style>
<?php include "footer.php"; ?>
