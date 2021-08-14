<?php
include "header.php";
?>
<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">


                    <div class="kt-portlet" style="display:block;">


                        <!--begin::Form-->
                        <form class="kt-form kt-form--label-right" id="document_form">
                            <div class="kt-portlet__body" style="padding: 20px !important;">
                                <div class="Msg"></div>
                                <div class="form-group row">
                                    <label>Choose Type:</label>
                                    <select id="ddl_type" name="ddl_type">
                                        <option value="0">--SELECT--</option>
                                        <!--<option value="1">Passport Request</option>
                                        <option value="2">Certificate</option>
                                        <option value="3">NOC Request</option>
                                        <option value="4">Assest Request</option>-->

                                        <option value="1">Leave Request</option>
                                        <option value="2">Certificate</option>
                                        <option value="3">Passport</option>
                                        <option value="4">loan</option>
                                        <option value="5">NOC</option>
                                        <option value="6">Asset Request</option>
                                        <option value="7">Asset Return Request</option>


                                    </select>

                                    <div class="col-lg-2" style="display: none;">
                                        <label><?= trans('Document Type') ?>:
                                        </label>
                                        <?php
                                        include_once($path_to_root . "/API/API_HRM_Call.php");
                                        $call_obj=new API_HRM_Call();
                                        $leave_types=$call_obj->get_documents();
                                        ?>
                                         <select class="ddlDocTypes form-control" >
                                             <option value="0">--SELECT--</option>
                                             <?php foreach ($leave_types as $type): ?>
                                                 <option value="<?php echo $type['id']?>"><?php echo $type['description']; ?></option>
                                             <?php endforeach; ?>
                                         </select>
                                    </div>
                                    <div class="col-lg-4" style="display: none;">
                                        <label class=""><?= trans('Reason') ?>:</label>

                                        <textarea class="txt_reason form-control" name="txt_reason"   style="    height: 40px;"></textarea>
                                    </div>
                                    <div class="col-lg-2" style="display: none;">
                                        <label class=""><?= trans('Document Required Date') ?>:</label>
                                        <input type="text" class="txtIssueDate form-control ap-datepicker" name="txtIssueDate" autocomplete="off"  />
                                    </div>
                                    <!--<div class="col-lg-2" >
                                        <label class=""><?/*= trans('Expiry Date') */?>:</label>
                                        <input type="text" id="txtExpiry" name="txtExpiry"  autocomplete="off"class="form-control ap-datepicker" />
                                    </div>-->

                                    <!--<div class="col-lg-2" style="padding: 1%;">
                                        <label class=""><?/*= trans('Attach File') */?>:</label>
                                        <input type="file" id="fileToUpload" name="fileToUpload">
                                    </div>-->


                                    <div class="col-lg-3" style="display: none;">
                                        <label style="height: 13px;"></label>
                                        <div class="input-group">
                                            <button type="button" onclick="SaveDocument(0);" class="btn btn-primary">
                                                <?= trans('Request') ?>
                                            </button>&nbsp;&nbsp;
                                            <button type="button" class="btn btn-secondary btnCancel" onclick="Cancel();"><?= trans('Cancel') ?></button>
                                            <input type="hidden" id="hdnAction" name="hdnAction"/>
                                        </div>
                                    </div>
                                </div>

                        </form>
                        <!--end::Form-->
                    </div>
                </div>

                <div class="row" style="width: 100%;">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('List Requests') ?>
                                </h3>
                            </div>

                        </div>
                        <!--begin::Table-->
                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <div id="other_requests"></div>

                            <table class='table' style='width:100%;display:none;' id="list_documents" >
                                <thead class=\"thead-dark\">
                                <tr>
                                    <th style='padding: 8px;text-align: center;'>Ref.No</th>
                                    <th style='padding: 8px;text-align: center;'>Emp. Name</th>
                                    <th style='padding: 8px;text-align: center;'>Request Date</th>
                                    <th style='padding: 8px;text-align: center;'>Comment</th>
                                    <th style='padding: 8px;text-align: center;'>Request Creation Date</th>
                                    <th style='padding: 8px;text-align: center;'>Status</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody id="list_documents_tbody" style="text-align: center;">
                                </tbody>
                            </table>

<!----------------------LEAVE----------------------------------->
                            <table class='table' style='width:100%;display:none;' id="list_leave">
                            <thead class=\"thead-dark\">

                            <tr>
                                <th style='padding: 8px;text-align: center;'>Ref.No</th>
                                <th style='padding: 8px;text-align: center;'>Emp. Name</th>
                                <th style='padding: 8px;text-align: center;'>Leave type</th>
                                <th style='padding: 8px;text-align: center;'>Days Requested</th>
                                <th style='padding: 8px;text-align: center;'>Reason</th>
                                <th style='padding: 8px;text-align: center;'>Start Date</th>
                                <th style='padding: 8px;text-align: center;'>End Date</th>
                                <th style='padding: 8px;text-align: center;'>Leave History</th>
                                <th style='padding: 8px;text-align: center;'>Status</th>
                                <th></th>
                            </tr>

                            </thead>

                            <tbody id="list_leave_tbody" style="text-align: center;">
                            </tbody>
                            </table>
<!-------------------------------------ASSET REQUEST--------------------->

                            <table class='table' style='width:100%;display:none;' id="asset_request">
                                <thead class=\"thead-dark\">

                                <tr>
                                    <th style='padding: 8px;text-align: center;'>Ref.No</th>
                                    <th style='padding: 8px;text-align: center;'>Emp. Name</th>
                                    <th style='padding: 8px;text-align: center;'>Category</th>
                                    <th style='padding: 8px;text-align: center;'>Asset Type</th>
                                    <th style='padding: 8px;text-align: center;'>Model</th>
                                    <th style='padding: 8px;text-align: center;'>Model Number</th>
                                    <th style='padding: 8px;text-align: center;'>Serial Number</th>
                                    <th style='padding: 8px;text-align: center;'>Comments</th>
                                    <th style='padding: 8px;text-align: center;'>Status</th>
                                    <th></th>
                                </tr>

                                </thead>

                                <tbody id="asset_request_tbody" style="text-align: center;">
                                </tbody>
                            </table>

                            <!---------------------------LON--------------->

                            <table class='table' style='width:100%;display:none;' id="list_loan">
                                <thead class=\"thead-dark\">

                                <tr>
                                    <th style='padding: 8px;text-align: center;'>Ref.No</th>
                                    <th style='padding: 8px;text-align: center;'>Emp. Name</th>
                                    <th style='padding: 8px;text-align: center;'>Loan Reason</th>
                                    <th style='padding: 8px;text-align: center;'>Amount</th>
                                    <th style='padding: 8px;text-align: center;'>Installment Count</th>
                                    <th style='padding: 8px;text-align: center;'>Required Date</th>
                                    <th style='padding: 8px;text-align: center;'>Status</th>
                                    <th></th>
                                </tr>

                                </thead>

                                <tbody id="list_leave_tbody" style="text-align: center;">
                                </tbody>
                            </table>



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="font-weight: bold;color:red;">Add Disapprove Reason</h4>
                <button type="button" class="close" data-dismiss="modal"></button>

            </div>
            <div class="modal-body">
                <table>
                    <!--<tr>
                        <td>Doc.Type:</td><td> <select class="ddl_Edit_DocTypes form-control" >
                                <option value="0">--SELECT--</option>
                                <?php /*foreach ($leave_types as $type): */?>
                                    <option value="<?php /*echo $type['id']*/?>"><?php /*echo $type['description']; */?></option>
                                <?php /*endforeach; */?>
                            </select></td>
                    </tr>-->
                    <tr>
                        <td>Disapprove Reason:</td><td><textarea class="txt_Edit_reason form-control" name="txt_reason"   style="height: 116px;
    margin-top: 0px;
    margin-bottom: 0px;
    width: 316px;"></textarea></td>
                    </tr>
                    <!--<tr>
                        <td>Doc. Required Date :</td>
                        <td>
                            <input type="text" class="txt_edit_IssueDate form-control ap-datepicker" name="txtIssueDate" autocomplete="off"  />
                        </td>
                    </tr>-->

                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default ClsReq_update" data-dismiss="modal">Update </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="hidden" id="hdn_pk_id" />
            </div>
        </div>

    </div>
</div>


<div id="myModalLeaveVerify" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Approve leave Request</h4>
                <button type="button" class="close" data-dismiss="modal"></button>

            </div>
            <div class="modal-body">
                <form class="kt-form kt-form--label-right" id="document_form">
                <table>
                    <tr>
                        <td style="font-size: 12pt;
    color: green;
    font-weight: bold;">Taken Leave In Days</td><td>:</td><td>
                            <label id="lbl_taken_leave" style="font-size: 12pt;
    font-weight: bold;
    color: green;
    margin-top: 5%;"></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12pt;color: blue;
    font-weight: bold;">Number of days requested</td><td>:</td><td>
                            <input type="text" id="lblLeaveReqDays" name="lblLeaveReqDays"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Fully Paid (in days)</td><td>:</td><td>
                           <input type="text" id="txtpaidleaves" name="txtpaidleaves"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Half Paid (in days)</td><td>:</td><td>
                            <input type="text" id="txt_full_day_salary_cut" name="txt_full_day_salary_cut"/>
                        </td>
                    </tr>

                    <tr>
                        <td>UnPaid (in days)</td><td>:</td><td>
                            <input type="text" id="txt_half_day_salary_cut" name="txt_half_day_salary_cut"/>
                        </td>
                    </tr>

                    <tr>
                        <td>Leave days reducing comment</td><td>:</td><td>
                             <textarea id="leave_day_reduce_cmnt" name="leave_day_reduce_cmnt" style="height: 120px;
                             width: 181px;"></textarea>
                        </td>
                    </tr>
                </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default ClsApporveReq" data-dismiss="modal">Continue To Approve</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="hidden" id="hdn_leave_level" />
                <input type="hidden" id="hdn_leave_edit_id" />
            </div>
        </div>

    </div>
</div>


<input type="hidden" id="hdndept_id" />
<input type="hidden" id="hdnedit" />
<input type="hidden" id="hdntype_id" />
<input type="hidden" id="hdn_id" />
<input type="hidden" id="hdn_alt_level" />
<input type="hidden" id="hdn_changed_type_val" />
<?php include "footer.php"; ?>
<script>
    $(document).ready(function () {
        Loaddept();
        //Fecthdata();
    });

    $('.ap-datepicker').datepicker({
        format: 'dd-mm-yyyy',
    });

    $(".ap_department").change(function() {
        loadEmployees();
    });


   function Loaddept()
    {
        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_department',null,function () {
                $('.ap_department').prepend('<option value="0">Select Department</option>');

                if($("#hdndept_id").val()=='')
                {
                    $('.ap_department').val('0');
                }
                else
                {
                    $('.ap_department').val($("#hdndept_id").val());
                    loadEmployees();
                }

            });
        });
    }

    function loadEmployees()
    {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+$(".ap_department").val(), function (data) {
            AxisPro.PrepareSelectOptions(data, 'Empid', 'Emp_name', 'ap_employees',null,function () {
                $('.ap_employees').prepend('<option value="0">SELECT Employees</option>');

                if($("#hdnemp_id").val()=='')
                {
                    $('.ap_employees').val('0');
                }
                else
                {
                    $('.ap_employees').val($("#hdnemp_id").val());
                }
            });
        });
    }

    $('#ddl_type').change(function()
    {

      if($('#ddl_type').val()=='1')
        {
            $('#list_documents').css('display','none');
            $('#list_leave').css('display','table');
            $('#asset_request').css('display','none');
            $('#list_loan').css('display','none');


        }
        else if($('#ddl_type').val()=='6' || $('#ddl_type').val()=='7')
        {
            $('#list_documents').css('display','none');
            $('#list_leave').css('display','none');
            $('#asset_request').css('display','table');
            $('#list_loan').css('display','none');

        }
        else if($('#ddl_type').val()=='4')
        {
            $('#list_documents').css('display','none');
            $('#list_leave').css('display','none');
            $('#asset_request').css('display','none');
            $('#list_loan').css('display','table');
        }
        else
        {
            $('#list_documents').css('display','table');
            $('#list_leave').css('display','none');
            $('#asset_request').css('display','none');
            $('#list_loan').css('display','none');
        }

        $('#hdn_changed_type_val').val($('#ddl_type').val());

        databind($('#hdn_changed_type_val').val());

    });


   function databind(type_id)
   {
       if(type_id==1)
       {
           $('#asset_request').dataTable().fnDestroy();
           $('#list_documents').dataTable().fnDestroy();
           $('#list_leave').dataTable().fnDestroy();
           $('#list_loan').dataTable().fnDestroy();

           $('#list_leave').DataTable({
               "bProcessing": true,
               "serverSide": true,
               "ajax":{
                   url :ERP_FUNCTION_API_END_POINT + "?method=verify_approve_requests", // json datasource
                   type: "post",  // type of method  ,GET/POST/DELETE
                   data:{'type_id':type_id},
                   error: function(){
                   }
               }
           });
       }
       else if($('#ddl_type').val()=='6' || $('#ddl_type').val()=='7')
       {
           $('#asset_request').dataTable().fnDestroy();
           $('#list_documents').dataTable().fnDestroy();
           $('#list_leave').dataTable().fnDestroy();
           $('#list_loan').dataTable().fnDestroy();
           $('#asset_request').DataTable({
               "bProcessing": true,
               "serverSide": true,
               "ajax":{
                   url :ERP_FUNCTION_API_END_POINT + "?method=verify_approve_requests", // json datasource
                   type: "post",  // type of method  ,GET/POST/DELETE
                   data:{'type_id':type_id},
                   error: function(){
                   }
               }
           });
       }
       else if($('#ddl_type').val()=='4')
       {
           $('#asset_request').dataTable().fnDestroy();
           $('#list_documents').dataTable().fnDestroy();
           $('#list_leave').dataTable().fnDestroy();
           $('#list_loan').dataTable().fnDestroy();
           $('#list_loan').DataTable({
               "bProcessing": true,
               "serverSide": true,
               "ajax":{
                   url :ERP_FUNCTION_API_END_POINT + "?method=verify_approve_requests", // json datasource
                   type: "post",  // type of method  ,GET/POST/DELETE
                   data:{'type_id':type_id},
                   error: function(){
                   }
               }
           });
       }
       else
       {
           $('#asset_request').dataTable().fnDestroy();
           $('#list_documents').dataTable().fnDestroy();
           $('#list_leave').dataTable().fnDestroy();
           $('#list_loan').dataTable().fnDestroy();
           $('#list_documents').DataTable({
               "bProcessing": true,
               "serverSide": true,
               "ajax":{
                   url :ERP_FUNCTION_API_END_POINT + "?method=verify_approve_requests", // json datasource
                   type: "post",  // type of method  ,GET/POST/DELETE
                   data:{'type_id':type_id},
                   error: function(){
                   }
               }
           });
       }




   }


    $('#list_documents tbody').on('click', 'td .btnupdate', function ()
    {

        var id=$(this).attr('alt');
        var action=$(this).attr('alt_action');
        var alt_type=$(this).attr('alt_type');
        var alt_level=$(this).attr('alt_level');

        if(action=='1')
        {
            approveStatus(alt_type,id,action,alt_level);
        }
        else if(action=='2')
        {
            DisapproveStatus(alt_type,id,action,alt_level);
        }

    });


    $('#asset_request tbody').on('click', 'td .btnupdate', function ()
    {

        var id=$(this).attr('alt');
        var action=$(this).attr('alt_action');
        var alt_type=$(this).attr('alt_type');
        var alt_level=$(this).attr('alt_level');

        if(action=='1')
        {
            approveStatus(alt_type,id,action,alt_level);
        }
        else if(action=='2')
        {
            DisapproveStatus(alt_type,id,action,alt_level);
        }

    });

    $('#list_leave tbody').on('click', 'td .btnupdate', function ()
    {

        var id=$(this).attr('alt');
        var action=$(this).attr('alt_action');
        var alt_type=$(this).attr('alt_type');
        var alt_level=$(this).attr('alt_level');
        var alt_role_flag=$(this).attr('alt_role_flag');
        var days_req=$(this).attr('alt_days');

        if(action=='1')
        {
            approveStatus(alt_type,id,action,alt_level,alt_role_flag,days_req);
        }
        else if(action=='2')
        {
            DisapproveStatus(alt_type,id,action,alt_level);
        }

    });



    $('#list_leave tbody').on('click', 'td .ViewEmpHistory', function ()
    {
        var id=$(this).attr('alt_id');

        window.open(BASE_URL+'hrm_employee_leave_history.php?id='+id);
    });




    function  approveStatus(type,id,action,alt_level,alt_role_flag,days_req)
    {

        if(type=='1' && alt_role_flag=='')
        {
           $('#lblLeaveReqDays').val(days_req);
           $('#myModalLeaveVerify').modal('show');
           $('#hdn_leave_edit_id').val(id);
           $('#hdn_leave_level').val(alt_level);

            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_taken_leaves",
                'id='+id, function (data) {
                    $('#lbl_taken_leave').html(data.leave_taken);
                });



        }
        else
        {
            var confrim=confirm('Please confirm');
            if(confrim==true) {

                AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=approve_request",
                    'remove_id='+id+'&action='+action+'&type='+type+'&level='+alt_level, function (data) {
                        if (data.status == 'Error') {
                            toastr.error(data.msg);
                        }
                        else
                        {
                            toastr.success(data.msg);
                            databind($('#hdn_changed_type_val').val());
                        }
                    });
            }
        }

    }



    $('.ClsApporveReq').click(function()
    {
        var full_paid_leave=$('#txtpaidleaves').val();
        var half_paid_leave=$('#txt_full_day_salary_cut').val();
        var unpaid_leave=$('#txt_half_day_salary_cut').val();
        var id=$('#hdn_leave_edit_id').val();
        var alt_level=$('#hdn_leave_level').val();
        var days_req=$('#lblLeaveReqDays').val();
        var days_reduce_cmnt=$('#leave_day_reduce_cmnt').val();

        if(full_paid_leave==''){full_paid_leave=0;}
        if(half_paid_leave==''){half_paid_leave=0;}
        if(unpaid_leave==''){unpaid_leave=0};

        var sum_tot=parseFloat(full_paid_leave)+parseFloat(half_paid_leave)+parseFloat(unpaid_leave);

        if(sum_tot==days_req)
        {
            var confrim=confirm('Please confirm');
            if(confrim==true) {

                AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=approve_leave_request_with_split_date",
                    'update_id='+id+'&level='+alt_level+'&full_paid_leave='+full_paid_leave
                    +'&half_paid_leave='+half_paid_leave+'&unpaid_leave='+unpaid_leave
                    +'&req_date='+days_req+'&days_reduce_cmnt='+days_reduce_cmnt, function (data) {
                        if (data.status == 'Error') {
                            toastr.error(data.msg);
                        }
                        else
                        {
                            toastr.success(data.msg);
                            databind($('#hdn_changed_type_val').val());
                        }
                    });
            }
        }
        else
        {
            toastr.error('Entered days and total days not matching');
        }
    });

    function DisapproveStatus(type,id,action,alt_level)
    {
        $('#myModal').modal('show');
        $('#hdntype_id').val(type);
        $('#hdn_id').val(id);
        $('#hdn_alt_level').val(alt_level);

    }

    $('.ClsReq_update').click(function()
    {
        if($('.txt_Edit_reason').val()=='')
        {
            toastr.error('Enter request disapprove reason');
        }
        else
        {
            var confrim=confirm('Please confirm');
            if(confrim==true) {

                AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=dispprove_request",
                    'remove_id='+$('#hdn_id').val()+'&action='+2+'&type='+$('#hdntype_id').val()+'&level='+$('#hdn_alt_level').val()+'&comment='+$('.txt_Edit_reason').val(), function (data) {
                        if (data.status == 'Error') {
                            toastr.error(data.msg);
                        }
                        else
                        {
                            toastr.success(data.msg);
                            databind($('#hdn_changed_type_val').val());
                        }
                    });
            }
        }

    });





</script>