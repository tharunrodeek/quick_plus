<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">


            <div class="kt-container  kt-grid__item kt-grid__item--fluid">


             <?php include('hrm_employee_comman_header.php'); ?>


                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title" style="font-size: 24pt;"><?= trans('HRMS') ?></h3>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <?php
                    include_once($path_to_root . "/API/API_HRM_Document_Request.php");
                    $call_obj=new API_HRM_Document_Request();
                    $data_array=$call_obj->getdashboardData_HRM();

                    include_once($path_to_root . "/API/API_HRM_Call.php");
                    $access_dta=new API_HRM_Call();
                    $hrm_access_data=$access_dta->check_access_for_dashbord();

                    if($hrm_access_data!='3')
                    {
                    ?>
                    <div class="col-xl-6">
                        <div class="row" style="background-color: #dff3f3;
    height: 166px;
    padding: 9px;
    border-radius: 18px;">

                            <div class="col-md-2">

                                <div class="card card-custom bg-primary gutter-b" style="height: 150px;border-radius: 15px;cursor:pointer;" data-toggle="modal" data-target="#myModalDialog"  >
                                    <div class="card-body">
                                        <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">
                                        <img src="https://cdn3.vectorstock.com/i/1000x1000/32/22/gift-box-red-3d-present-box-with-silver-ribbon-vector-11533222.jpg" style="
    width: 24%;
">
                                        </span>
                                        <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3" style="color:#fff;font-size: 27pt;"><?php echo $data_array['birth_day']; ?></div>

                                        <a href="#" class="text-inverse-primary font-weight-bold font-size-lg mt-1"  style="color:#fff;">BirthDay(s)</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="card card-custom gutter-b" data-toggle="modal" data-target="#myModalDialogDocumnet" style="height: 150px;    background-color: #f97f8c;    border-radius: 17px;">
                                    <div class="card-body">
                                        <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">
                                        <img src="https://static.thenounproject.com/png/1241401-200.png" style="width: 25%;"></span>
                                        <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3" style="color:#fff;font-size: 27pt;"><?php echo $data_array['doc_exp_cnt']; ?></div>

                                        <a href="#" class="text-inverse-primary font-weight-bold font-size-lg mt-1" style="color:#fff;">Document Expire Soon</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="card card-custom gutter-b" style="height: 150px;    background-color: #663159;
    border-radius: 17px;">
                                    <div class="card-body">
                                        <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">
                                            <img src="https://keenthemes.com/metronic/themes/metronic/theme/html/demo4/dist/assets/media/svg/avatars/001-boy.svg" style="width: 25%;">
                                        </span>
                                        <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3" style="color:#fff;font-size: 27pt;"><?php echo $data_array['tot_emp_cnt']; ?></div>

                                        <a href="#" class="text-inverse-primary font-weight-bold font-size-lg mt-1" style="color:#fff;">Total Employees</a>
                                    </div>
                                </div>
                            </div>




                        </div>
                    </div>
                    <?php } ?>

                    <?php //if($hrm_access_data=='3') { ?>
                        <style>
                            .tab-pane
                            {
                                height: 263px;
                                overflow: auto;
                            }
                        </style>
                        <ul class="nav nav-tabs md-tabs" id="myTabMD" role="tablist" style="width: 100%;">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab-md" data-toggle="tab" href="#home-md" role="tab" aria-controls="home-md"
                                   aria-selected="true">My Leave Requests</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab-md" data-toggle="tab" href="#profile-md" role="tab" aria-controls="profile-md"
                                   aria-selected="false">Other Requests</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="contact-tab-md" data-toggle="tab" href="#contact-md" role="tab" aria-controls="contact-md"
                                   aria-selected="false">My Documents</a>
                            </li>
                        </ul>
                        <div class="tab-content card pt-9" id="myTabContentMD" style="width:100%;">
                            <div class="tab-pane fade show active" id="home-md" role="tabpanel" aria-labelledby="home-tab-md">
                                 <?php
                                 echo  $call_obj->get_top_leave_request();
                                 ?>
                            </div>
                            <div class="tab-pane fade" id="profile-md" role="tabpanel" aria-labelledby="profile-tab-md">
                                <label>Choose Type:</label>
                                <select id="ddl_type" name="ddl_type">
                                    <option value="0">--SELECT--</option>
                                    <option value="3">Passport Request</option>
                                    <option value="2">Certificate</option>
                                    <option value="4">Loan</option>
                                    <option value="5">NOC Request</option>
                                    <option value="6">Assest Request</option>
                                </select>
                                <div id="other_requests"></div>

                                <?php
                                  //echo $call_obj->other_doc_requests();
                                ?>
                            </div>
                            <div class="tab-pane fade" id="contact-md" role="tabpanel" aria-labelledby="contact-tab-md">
                                <?php
                                echo $call_obj->employee_documents();
                                ?>
                            </div>
                        </div>
                    <?php //} ?>









                </div>



                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('Transactions') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="row "  id="collapseExample">

                    <?= createMenuTile('HRM_CUSTOM_REQUEST_LEAVE',trans('Apply Leave'),
                        trans('Apply Leave'),route('apply_leave'),'fa fa-file-invoice') ?>

                    <?/*= createMenuTile('HRM_REQ_DOC',trans('Request Document'),
                        trans('Request Document'),route('request_doc'),'fa fa-file-invoice') */?>
						
					<?= createMenuTile('HRM_REQ_PASSPORT',trans('Passport Withdrawal Request '),
                        trans('Request Passport'),route('request_passport'),'fas fa-passport') ?>
						
					<?= createMenuTile('HRM_REQ_CERTIFICATE',trans('Request Certificate'),
                        trans('Request Certificate'),route('hrm_certificate'),'fa fa-certificate') ?>
						
					<?= createMenuTile('HRM_REQ_NOC',trans('Request NOC'),
                        trans('Request NOC'),route('req_noc'),'fa fa-certificate') ?>
                    <!-- HRM_REQ_ASSET-->
                    <?= createMenuTile('HRM_REQ_NOC',trans('Asset Request'),
                        trans('Asset Request'),route('asset_request'),'fa fa-certificate') ?>

                    <?= createMenuTile('HRM_ASSET_RETURN',trans('Asset Return Request'),
                        trans('Asset Return Request'),route('asset_return'),'fa fa-certificate') ?>






                    <?= createMenuTile('HRM_EMP_REQUEST_LOAN',trans('Request Loan'),
                        trans('Request Loan'),route('request_loan'),'fa fa-file-invoice') ?>





                </div>
                <!--End::Row-->
                <!--End::Row-->
                <!--End::Dashboard 2-->
                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title" ><?= trans('Maintainance') ?></h3>
                        </div>
                    </div>
                </div>
               <!------------Start Row------------>
                <div class="row " id="collapseMaintence">

                    <?/*= createMenuTile('HRM_EMPLOYEE_MANAGE',trans('Add/Manage Employee'),
                        trans('Add/Manage Employee'),route('manage_employee'),'fa fa-user-tie') */?>

                    <?= createMenuTile('HRM_EMPLOYEE_PROFILE',trans('Employee Profile'),
                        trans('Employee Profile'),route('employee_profile'),'fa fa-user-tie') ?>
						
						<?= createMenuTile('HRM_EMPLOYEE_PROFILE',trans('Company Hierarchy'),
                        trans('Company Hierarchy'),route('employee_hierarchy'),'fad fa-sitemap') ?>

                  





                </div>

                 <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('Reports') ?></h3>
                        </div>
                    </div>
                </div>

                 <div class="row " id="collapseReports">

                     <?= createMenuTile('HRM_SHIFT_REPORT',trans('Shift Reports'),
                         trans('Shift Reports'),route('shift_report'),'fa fa-file') ?>

                     <?/*= createMenuTile('HRM_PAYROLL_REPORT',trans('Payroll Report'),
                         trans('Payroll Report'),route('payroll_report'),'fa fa-file') */?>

                </div>


            </div>
            <!-- end:: Content -->
        </div>
    </div>

    <div id="myModalDialog" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">List<nobr></nobr> BirthDays</h4>
                    <div>
                        <!--<img src="https://cdn3.vectorstock.com/i/1000x1000/32/22/gift-box-red-3d-present-box-with-silver-ribbon-vector-11533222.jpg"
                             style="width: 24%;">-->
                    </div>
                    <button type="button" class="close" data-dismiss="modal"></button>

                </div>
                <div class="modal-body">
                    <form class="kt-form kt-form--label-right" id="document_form">
                        <?php

                        $call_obj=new API_HRM_Document_Request();
                        $birthday_data=$call_obj->get_birthday_details();
                        ?>
                          <?php echo $birthday_data; ?>
                    </form>
                </div>

            </div>

        </div>
    </div>


    <div id="myModalDialogDocumnet" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">List<nobr></nobr> Documents</h4>
                    <div>
                        <!--<img src="https://cdn3.vectorstock.com/i/1000x1000/32/22/gift-box-red-3d-present-box-with-silver-ribbon-vector-11533222.jpg"
                             style="width: 24%;">-->
                    </div>
                    <button type="button" class="close" data-dismiss="modal"></button>

                </div>
                <div class="modal-body">
                    <form class="kt-form kt-form--label-right" id="document_form">
                        <?php

                        $call_obj=new API_HRM_Document_Request();
                        $doc_data=$call_obj->get_expirying_doc_details();
                        ?>
                        <?php echo $doc_data; ?>
                    </form>
                </div>

            </div>

        </div>
    </div>


    <div id="myModal_History" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" style="width: 743px;left: -25%">
                <div class="modal-header">
                    <h4 class="modal-title">View Apporval<nobr></nobr> History</h4>
                    <div>

                    </div>
                    <button type="button" class="close" data-dismiss="modal"></button>

                </div>
                <div class="modal-body">
                    <form class="kt-form kt-form--label-right" id="history_form">
                         <div class="ClsApporveAhistory"></div>
                    </form>
                </div>

            </div>

        </div>
    </div>




</div>
<script src="assets/plugins/general/jquery/dist/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    $('#ddl_type').change(function()
    {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=other_doc_requests"
            , 'type_id='+$('#ddl_type').val()+'&from_page=2', function (data) {
                $('#other_requests').html(data.html);
            });
    });
    $(document).on("click",".ClsviewHistory",function()
    {

        var id=$(this).attr('alt');
        var type_id=$(this).attr('alt_type');

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=view_approval_history"
            , 'type_id='+type_id+'&from_page=2'+'&alt_id='+id, function (data)
            {
                 $('.ClsApporveAhistory').html(data.html);
                 $('#myModal_History').modal('show');
            });
    });
</script>