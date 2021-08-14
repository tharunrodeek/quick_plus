<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">


            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

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
                    //$hrm_access_data=$access_dta->check_access_for_dashbord();

                    $leave_req_data=$call_obj->group_requests();

                    if($_SESSION["wa_current_user"]->can_access('HRM_REQUEST_SECTION'))
                    {
                        ?>



                        <div class="col-xl-6">
                            <div class="row" style="background-color: #dff3f3;
        /*height: 93px;*/
    padding: 0px;
    /*border-radius: 18px;*/">

                                <div class="col-md-4">

                                    <div class="card card-custom bg-primary gutter-b" style="height: 71px;
                                    /*border-radius: 15px;*/cursor:pointer;background-color: #34bfa3b5 !important;margin: 4%;" data-toggle="modal" data-target="#myModalDialog"  >
                                        <div class="card-body">
                                        <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">
                                        <img src="https://cdn3.vectorstock.com/i/1000x1000/32/22/gift-box-red-3d-present-box-with-silver-ribbon-vector-11533222.jpg" style="
    width: 13%;
">
                                        </span>
                                             

                                            <a href="#" class="text-inverse-primary font-weight-bold font-size-lg mt-1"  style="color:#fff;">BirthDays(<?php echo $data_array['birth_day']; ?>)</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card card-custom gutter-b" data-toggle="modal" data-target="#myModalDialogDocumnet" style="height: 71px;    /*border-radius: 17px;  */  background-color: #36a3f7b5;margin: 4%;">
                                        <div class="card-body">
                                        <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">
                                        <img src="https://static.thenounproject.com/png/1241401-200.png" style="width: 13%;"></span>
                                          

                                            <a href="#" class="text-inverse-primary font-weight-bold font-size-lg mt-1" style="color:#fff;">Document Expires(<?php echo $data_array['doc_exp_cnt']; ?>)</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card card-custom gutter-b" style="height: 71px;    /*border-radius: 17px;  */  background-color: #fd3995bf;margin: 4%;">
                                        <div class="card-body">
                                        <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">
                                        <img src="https://static.thenounproject.com/png/2170321-200.png" style="width: 13%;"></span>
                                          

                                            <a href="#" class="text-inverse-primary font-weight-bold font-size-lg mt-1" style="color:#fff;">Total Employees(<?php echo $data_array['tot_emp_cnt']; ?>)</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card card-custom gutter-b" style="height: 71px;    /*border-radius: 17px;  */  background-color: #36a3f7cc;margin: 4%;" data-toggle="modal" data-target="#RequestListSummaryDialog">
                                        <div class="card-body">
                                        <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">
                                            <img src="https://static.thenounproject.com/png/3266167-200.png" style="width: 13%;">
                                             
                                        </span>
                                          

                                            <a href="#" class="text-inverse-primary font-weight-bold font-size-lg mt-1" style="color:#fff;">Pending Requests To Verify(<?php  echo $leave_req_data['waiting_for_approve']; ?>)</a>
                                        </div>
                                    </div>
                                </div>
                                <?php if(user_check_access('HRM_SYNC_ATTENDANCE')): ?>

                                <!--<div class="col-md-4">
                                    <div class="card card-custom gutter-b" style="height: 71px;    background-color: grey;
    /*border-radius: 17px;*/margin: 4%;" data-toggle="modal" data-target="#sync_attendance_modal" >
                                        <div class="card-body">
                                        <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">
                                            <img src="https://static.thenounproject.com/png/9005-200.png" style="width: 13%;">
                                             
                                        </span>


                                            <a href="#" class="text-inverse-primary font-weight-bold font-size-lg mt-1" style="color:#fff;"> Sync Attendance</a>
                                        </div>
                                    </div>
                                </div>-->
                            <?php endif; ?>



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
                   <!-- <ul class="nav nav-tabs md-tabs" id="myTabMD" role="tablist" style="width: 100%;">
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

                        <li>
                            <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo">Show/Hide</button>
                        </li>
                    </ul>-->

                   <!-- <div id="demo" class="collapse" style="    width: 100%;">
                            <div class="tab-content card pt-9" id="myTabContentMD" style="width:100%;">
                                <div class="tab-pane fade show active" id="home-md" role="tabpanel" aria-labelledby="home-tab-md">
                                    <?php
/*                                    echo  $call_obj->get_top_leave_request();
                                    */?>
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
                                </div>
                                <div class="tab-pane fade" id="contact-md" role="tabpanel" aria-labelledby="contact-tab-md">
                                    <?php
/*                                    echo $call_obj->employee_documents();
                                    */?>
                                </div>
                            </div>

                    </div>  -->


                    
                    <?php //} ?>

                </div>

                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title" ><?= trans('Employee Management') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="row" id="collapseModules">

                    <?= createMenuTile('HRM_EMPLOYEE_MANAGE',trans('Add/Manage Employee'),
                        trans('Add/Manage Employee'),route('manage_employee'),'fa fa-user-tie') ?>

                    <?= createMenuTile('HRM_EMP_DOCS',trans('Manage Employee Documents'),
                        trans('Manage Employee Documents'),route('manage_emp_docs'),'fa fa-file') ?>

                    <?= createMenuTile('HRM_ASSIGN_SHIFT_EMPLOYEE',trans('Assign Shifts To Employees'),
                        trans('Assign Shifts To Employees'),route('assign_shifts'),'fa fa-users') ?>
                </div>








                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title" ><?= trans('Attendence & Payroll') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="row" id="collapseModules">
                    <?/*= createMenuTile('HRM_MANUVAL_ATTENDENCE',trans('Manual Attendance Entry'),
                        trans('Manual Attendance Entry'),route('attendance_entry'),'fa fa-book') */?><!--

                    <?/*= createMenuTile('HRM_SINGLE_ATTENDENCE',trans('Single Click Attendance Entry'),
                        trans('Single Click Attendance Entry'),route('single_attendance_entry'),'fa fa-book') */?>

                    --><?/*= createMenuTile('HRM_UPLOAD_ATTENDENCE',trans('Upload Attendence'),
                        trans('Upload Attendence'),route('upload_attendance'),'fa fa-upload') */?>


                    <?= createMenuTile('HRM_TIMESHEET',trans('TimeSheet'),
                        trans('Upload Attendence'),route('timesheet'),'fa fa-calendar-alt') ?>


                    <?= createMenuTile('HRM_PAYROLL_GENERATION',trans('Generate Payroll'),
                        trans('Generate Payroll'),route('payslip_entry'),'fa fa-file-invoice') ?>

                    <?= createMenuTile('HRM_PAYROLL_PROCESS',trans('Process Payroll'),
                        trans('Process Payroll'),route('process_slip'),'fa fa-file-invoice') ?>

                    <?= createMenuTile('HRM_PAYMENT',trans('Payment'),
                        trans('Process Payment'),route('process_payslip'),'fa fa-file-invoice-dollar') ?>



                    <?/*= createMenuTile('HRM_LOAN',trans('Loan Entry'),
                        trans('Loan Entry'),route('loan_entry'),'fa fa-file-invoice') */?>





                    <?/*= createMenuTile('HRM_LEAVE_APPROVE',trans('Leave Request & Approval'),
                        trans('Leave Request & Approval'),route('leave_manage'),'fa fa-file-invoice') */?>





                    <?= createMenuTile('HRM_VERIFY_GL_ENTRIES',trans('Verify & Pass GL Payroll Entries'),
                        trans('Verify & Pass GL Payroll Entries'),route('verify_gl_entries'),'fa fa-file') ?>

                    <?= createMenuTile('HRM_INVOICE_DEDUCTION',trans('Invoice Mistake Entry'),
                        trans('Invoice Mistake Entry'),route('invoice_mistake_entry'),'fa fa-file') ?>

                    <?= createMenuTile('HRM_HOLIDAY_WRK_APPRVE',trans('Public Holiday Work Approve'),
                        trans('Public Holiday Work Approve'),route('holiday_work_approve'),'fa fa-file') ?>





                

                </div>


                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title" ><?= trans('Request Management & ESB And Others') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="row" id="collapseModules">

                    <?= createMenuTile('HRM_REQUEST_VERIFY',trans('Verify Requests ('.$leave_req_data['waiting_for_approve'].')'),
                        trans('Verify Requests'),route('verify_request'),'fa fa-paper-plane') ?>

                    <?= createMenuTile('HRM_SUBMITTED_REQUESTS',trans('View Request Status'),
                        trans('View Request Status'),route('view_request_status'),'fa fa-paper-plane') ?>

                    <?= createMenuTile('HRM_APPROVED_REQUESTS',trans('View Approved Requests'),
                        trans('View Approved Requests'),route('list_approved_requests'),'far fa-thumbs-up') ?>

                    <?= createMenuTile('HRM_END_OF_SERVICE',trans('END of service'),
                        trans('END of service'),route('end_of_service'),'fa fa-hourglass-end') ?>

                    <?= createMenuTile('HRM_LIST_END_OF_SERVICE',trans('List END of services'),
                        trans('List END of services'),route('list_end_of_service'),'fa fa-file') ?>

                    <?= createMenuTile('HRM_APPROVE_OVERTIME',trans('Approve OverTime'),
                        trans('Approve OverTime'),route('overtime_approve'),'fa fa-user-clock') ?>

                    <?= createMenuTile('HRM_ISSUE_WARNING',trans('Issue Warning Letter'),
                        trans('Issue Warning Letter'),route('issue_warning'),'fa fa-exclamation-triangle') ?>


                </div>


                <!----END ROW--------->
                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title" ><?= trans('Master Modules') ?></h3>
                        </div>
                    </div>
                </div>
                <!-----------------------START ROW-------------------->
                <div class="row" id="collapseModules">



                    <?= createMenuTile('HRM_DEPARTMENT',trans('Department'),
                        trans('Department'),route('manage_department'),'fa fa-list') ?>

                    <?= createMenuTile('HRM_DIVISION',trans('Divisions'),
                        trans('Divisions'),route('manage_divisions'),'fa fa-project-diagram') ?>

                    <?= createMenuTile('HRM_DESIGNATION',trans('Designations'),
                        trans('Designations'),route('manage_designations'),'fa fa-user-graduate') ?>

                    <?= createMenuTile('HRM_DOC_MASTER',trans('Document'),
                        trans('Document'),route('manage_docs'),'fa fa-file-alt') ?>

                    <?= createMenuTile('HRM_MANUVAL_ATTENDENCE',trans('Leave Types'),
                        trans('Leave Types'),route('leave_types'),'fa fa-list') ?>

                    <?= createMenuTile('HRM_ASSETS',trans('Assets'),
                        trans('Assets'),route('assets'),'fa fa-list') ?>


                    <?= createMenuTile('HRM_PAY_ELEMENTS',trans('Pay Elements'),
                        trans('Pay Elements'),route('pay_elements'),'fa fa-list') ?>

                    <?= createMenuTile('HRM_SHIFT',trans('Shifts'),
                        trans('Shifts'),route('shifts'),'fa fa-list') ?>



                    <?= createMenuTile('HRM_LOAN_MASTER',trans('Loan'),
                        trans('Loan'),route('loan_master'),'fa fa-business-time') ?>

                    <?= createMenuTile('HRM_SETTINGS',trans('Default Settings'),
                        trans('Default Settings'),route('default_settings'),'fa fa-cog') ?>

                    <?= createMenuTile('HRM_REQUEST_FLOW',trans('Define Request Flow'),
                        trans('Define Request Flow'),route('request_flow'),'fa fas fa-code-branch') ?>

                     <?= createMenuTile('HRM_HOLIDAYS_MANAGE',trans('Manage Holidays'),
                        trans('Manage Holidays'),route('holidays_entry'),'fa fa-file') ?>   

                    <?php //endif; ?>

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

                    <?= createMenuTile('HRM_PAYROLL_REPORT',trans('Payroll Report'),
                        trans('Payroll Report'),route('payroll_report'),'fa fa-file') ?>

                    <?= createMenuTile('HRM_PAYROLL_REPORT',trans('Employee Report'),
                        trans('Employee Report'),route('employee_report'),'fa fa-file') ?>

                    <?= createMenuTile('HRM_PAYROLL_REPORT',trans('Employee Leave Report'),
                        trans('Employee Leave Report'),route('employee_leave_report'),'fa fa-sign-out-alt') ?>

                    <?= createMenuTile('HRM_GPSSA_REPORT',trans('GPSSA Report'),
                        trans('Employee Gpssa Report'),route('employee_gpssa_report'),'fa fa-sign-out-alt') ?>

                    <?= createMenuTile('HRM_EMERGENCY_REPORT',trans('Emergency Contact Report'),
                        trans('Employee Contact Report'),route('emergency_report'),'fa fa-sign-out-alt') ?>

                    <?= createMenuTile('HRM_ESB_REPORT',trans('Gratuity Report'),
                        trans('Employee Gratuity Report'),route('esb_report'),'fa fa-sign-out-alt') ?>

                </div>







                <!-------------------------END ROW--------------------->
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



    <div id="RequestListSummaryDialog" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">List<nobr></nobr> Summary Requests</h4>
                    <div>
                        <!--<img src="https://cdn3.vectorstock.com/i/1000x1000/32/22/gift-box-red-3d-present-box-with-silver-ribbon-vector-11533222.jpg"
                             style="width: 24%;">-->
                    </div>
                    <button type="button" class="close" data-dismiss="modal"></button>

                </div>
                <div class="modal-body">
                    <form class="kt-form kt-form--label-right" id="document_form">

                        <table border='1px solid #ccc;' style='width:100%;'>
                            <tr>
                                <th style='padding: 8px;text-align: center;'>Request Name</th>
                                <th style='padding: 8px;text-align: center;'>Pending Count</th>
                            </tr>
                       <?php $i=0;

					     foreach($leave_req_data['details'] as $summary):

                            if($summary['rows']>0){

                        ?>
                                 <tr>
                                     <td style='text-align: center;'><?php echo $summary['name']; ?></td>
                                     <td style='text-align: center;'><?php echo $summary['rows']; ?></td>

                                 </tr>
                       <?php $i++;

                           }
                   endforeach; ?>

                        </table>
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

                    <form id="form_export_expiry" method="POST" 
                    action="<?= $erp_url ?>API/hub.php?method=export_expiry_to_csv">
                         <div class="col-lg-6" style="    display: flex;">
                                        <label><?= trans('Document Type') ?>:
                                        </label>
                                        <?php
                                        include_once($path_to_root . "/API/API_HRM_Call.php");
                                        $call_obj=new API_HRM_Call();
                                        $leave_types=$call_obj->get_documents();
                                        ?>
                                         <select id="ddlDocTypes_Expiry" class="form-control">
                                             <option value="0">--All--</option>
                                             <?php foreach ($leave_types as $type): ?>
                                                 <option value="<?php echo $type['id']?>"><?php echo $type['description']; ?></option>
                                             <?php endforeach; ?>
                                         </select>
                         </div>

                         <!-- <div class="col-lg-6" style="    display: flex;">
                             <input type="submit" id="btn_export" name="btn_export" value="Export To CSV" />
                         </div> -->

                    </form>

                   


                                <table class="table table-bordered" id="document_exp_list">
                                            <thead>
                                                <th><?= trans('Employee') ?></th>
                                                <th><?= trans('Doc.') ?></th>
                                                <th><?= trans('Expire Date') ?></th>
                                                <th><?= trans('Expire In') ?></th>
                                            </thead>
                                            <tbody id="document_exp_list_tbody">
                                            </tbody>
                                </table>


                    <!-- <div class="kt-form kt-form--label-right" id="document_form">
                        <?php

                       // $call_obj=new API_HRM_Document_Request();
                       // $doc_data=$call_obj->get_expirying_doc_details();
                        ?>
                        <?php /*echo $doc_data;*/ ?>
                    </div> -->
                </div>

            </div>

        </div>
    </div>

<!--     <div id="myModalDialogDocumnet" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">List<nobr></nobr> Documents</h4>
                    <div>
                        <img src="https://cdn3.vectorstock.com/i/1000x1000/32/22/gift-box-red-3d-present-box-with-silver-ribbon-vector-11533222.jpg"
                             style="width: 24%;">
                    </div>
                    <button type="button" class="close" data-dismiss="modal"></button>

                </div>
                <div class="modal-body">
                    <form class="kt-form kt-form--label-right" id="document_form">
                        <?php

                        //$call_obj=new API_HRM_Document_Request();
                        //$doc_data=$call_obj->get_expirying_doc_details();
                        ?>
                        <?php //echo $doc_data; ?>
                    </form>
                </div>

            </div>

        </div>
    </div> -->

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
<?php if(user_check_access('HRM_SYNC_ATTENDANCE')): ?>
    <div class="modal fade" id="sync_attendance_modal">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Sync Attendance</h4>
            <button type="button" class="close" data-dismiss="modal"></button>
          </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="sync_from_date">From Date</label>
                <input type="text" class="form-control ap-datepicker" id="sync_from_date" placeholder="From Date" value="<?=Today();?>">
              </div>
              <div class="form-group">
                <label for="sync_to_date">To Date</label>
                <input type="text" class="form-control ap-datepicker" id="sync_to_date" placeholder="To Date" value="<?=Today();?>">
              </div>
            </div>
            <div class="modal-footer border-top-0 d-flex justify-content-center">
              <button type="submit" id="sync_btn" onclick="sync_attendance_now();" class="btn btn-success btn-block"><i class="fa fa-sync"></i> Sync Attendance Now</button>
            </div>

        </div>
      </div>
    </div>

<?php endif; ?>

</div>

<style>
    .card-body
    {
        padding: 1.5rem !important;
    }
</style>

<script src="assets/plugins/general/jquery/dist/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function(){
        load_exp_data();
        $('.ap-datepicker').datepicker({
        format: 'dd-mm-yyyy',
    });
    });
    $('#ddl_type').change(function()
    {
        //alert('sdasd');
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=other_doc_requests"
            , 'type_id='+$('#ddl_type').val(), function (data) {

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
   $('#ddlDocTypes_Expiry').change(function()
    {
       load_exp_data();
    });

   function load_exp_data()
   {
     $('#document_exp_list').dataTable().fnDestroy();
            $('#document_exp_list').DataTable({
                "bProcessing": true,
                "serverSide": true,
                "searching":false,
                "ajax":{
                    url :ERP_FUNCTION_API_END_POINT + "?method=get_expirying_doc_details", // json datasource
                    type: "post",  // type of method  ,GET/POST/DELETE
                     data:{'doc_type_id':$("#ddlDocTypes_Expiry").val()},
                    error: function(){
                    }
                }
            });
   }
<?php if(user_check_access('HRM_SYNC_ATTENDANCE')): ?>
    sync_attendance_now = () => {
      let from_date = $('#sync_from_date').val();
      let to_date = $('#sync_to_date').val();
      let sync_btn = $('#sync_btn');
      sync_btn.attr('disabled',true);
      sync_btn.html('<i class="fa fa-sync fa-spin"></i> Syncing Attendance ...');
      if(from_date!='' && to_date!=''){
          // alert(`${from_date} ${to_date}`);
          $.ajax({
            // url: 'http://192.168.1.52/shajeer/AxisAttendce/AxisAttendce/sync_data_access.php',
            url: 'http://localhost/AxisAttendce/sync_data_access.php',
            dataType: 'JSON',
            type: 'post',
            data: {from_date:from_date,to_date:to_date},
            success: function( data, textStatus, jQxhr ){
                // data = JSON.parse(data);
                if(data.status == 'OK'){
                  swal.fire(
                    'Success!',
                    data.success_msg,
                    'success'
                    );

              }else{
                swal.fire(
                    'Error!',
                    data.error_msg,
                    'error'
                    );
            }
        },
        error: function( jqXhr, textStatus, errorThrown ){
            console.log( errorThrown );
        }
    });

      }else{
        swal.fire(
            'Warning!',
            'Please select a valid date range!!!',
            'warning'
            );
    }
    $('#sync_attendance_modal').modal('hide');
    sync_btn.attr('disabled',false);
    sync_btn.html('<i class="fa fa-sync"></i> Sync Attendance Now');
}

<?php endif; ?>
</script>
