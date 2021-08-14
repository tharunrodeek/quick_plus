<?php
include "header.php";
?>
<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">


                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('TIMESHEET') ?>

                                </h3>
                            </div>
                        </div>


                        <!--begin::Form-->
                        <form class="kt-form kt-form--label-right" id="leave_form">
                            <div class="kt-portlet__body" style="padding: 20px !important;">
                                <div class="form-group row">

                                    <div class="col-lg-3">
                                        <label><?= trans('DEPARTMENT') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_department"
                                                    name="ddl_department" >
                                                <option value="">SELECT</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label><?= trans('EMPLOYEE') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_employees"
                                                    name="leave_type" >
                                                <option value="">All Employees</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <label><?= trans('FROM') ?>:</label>
                                        <div class="input-group">
                                            <input type="text" name="ap-datepicker" id="frmDate" autocomplete="off" class="form-control ap-datepicker" placeholder="">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <label><?= trans('TO') ?>:</label>
                                        <div class="input-group">
                                            <input type="text" name="ap-datepicker"  id="toDate" autocomplete="off" class="form-control ap-datepicker" placeholder="">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <label style="height: 13px;"></label>
                                        <div class="input-group">
                                            <button type="button" onclick="PrepareTimesheet();" class="btn btn-primary">
                                                <?= trans('View TimeSheet') ?>
                                            </button>&nbsp;&nbsp;
                                            <button type="button" onclick="Reset();" style="display:none;" class="btn btn-primary btnResetSlips">
                                                <?= trans('Cancel') ?>
                                            </button>
                                            <img src="<?= $base_url ?>assets/media/img/ajax-loader.gif" id="loader" style="height: 16px;
    margin-top: 4%;display: none;"/>
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
                                    <?= trans('TIMESHEET') ?>
                                </h3>
                            </div>

                        </div>

                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;width: 1248px;overflow: auto;">

                        </div>
                        <!--end::Form-->
                        <input type="hidden" id="hdnPageCnt" name="hdnPageCnt" />
                    </div>

                </div>
            </div>
            <!-- end:: Content -->
        </div>
    </div>
</div>
<!---------------------------------POPUP MODEL-------------->
<div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h4 class="modal-title w-100 font-weight-bold">Update Attendence</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">

                <div class="md-form mb-5">
                    <label data-error="wrong" data-success="right" for="defaultForm-email">Select Leave Type :</label>
                    <?php
                    include_once($path_to_root . "/API/API_HRM_Timesheet.php");
                    $timesheet_obj=new API_HRM_Timesheet();
                    $leave_types=$timesheet_obj->getLeaveTypes();
                    ?>
                    <select class="form-control" id="ddlLeaveType">
                            <option value="">---SELECT--</option>
                        <?php
                          foreach($leave_types as $types):
                        ?>
                           <option value="<?php echo $types['char_code']; ?>"><?php echo $types['description']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="md-form mb-5 ClsTimeDispl" style="display: none;">
                    <label data-error="wrong" data-success="right" for="defaultForm-email">In Time :</label>

                    <div class="ClsDivAlignDdl">

                        <select class="form-control ClsTime" id="ddlInTmeHour">
                            <?php
                            for($i=0;$i<=11;$i++){
                                ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                        <select class="form-control ClsTime" id="ddlInTmeMin">
                            <?php
                            for($i=0;$i<=59;$i++){
                                ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php } ?>
                        </select>

                        <select class="form-control ClsTime" id="ddlInTmeFrmt">
                                <option value="AM">AM</option>
                                <option value="PM">PM</option>
                        </select>

                    </div>
                </div>
                <div class="md-form mb-5 ClsTimeDispl" style="display: none;">
                    <label data-error="wrong" data-success="right" for="defaultForm-email">Out Time :</label>
                    <div class="ClsDivAlignDdl">
                        <select class="form-control ClsTime" id="ddlOutHr" >
                            <?php
                            for($i=0;$i<=11;$i++){
                                ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                        <select class="form-control ClsTime" id="ddlOutMin">
                            <?php
                            for($i=0;$i<=59;$i++){
                                ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                        <select class="form-control ClsTime" id="ddlOutTmeFrmt">
                            <option value="AM">AM</option>
                            <option value="PM">PM</option>
                        </select>
                        <input type="hidden" id="HdnTxtId" />
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button class="btn btn-default" id="btnUpdate">Update</button>
                <button class="btn btn-default" id="btnCancel">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-----------------------------------END-------------------->
<style>
    .ClsTime
    {
        width: 21%;
    }
    .ClsDivAlignDdl
    {
        display: flex;
    }
</style>

<?php include "footer.php"; ?>
<script>
    $(document).ready(function () {

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_department',null,function () {
                $('.ap_department').prepend('<option value="0">Select Department</option>');
                $('.ap_department').val('0');
            });
        });
    });
    $('.ap-datepicker').datepicker({
        format: 'dd-mm-yyyy',
    });

    $(".ap_department").change(function() {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+$(".ap_department").val(), function (data) {
            AxisPro.PrepareSelectOptions(data, 'Empid', 'Emp_name', 'ap_employees',null,function () {
                $('.ap_employees').prepend('<option value="0">All Employees</option>');
                $('.ap_employees').val('0');
            });
        });
    });

    $("#ddlLeaveType").change(function() {

        if($(this).val()=='p')
        {
           $('.ClsTimeDispl').css('display','block');
        }
        else
        {
            $('.ClsTimeDispl').css('display','none');
        }

    });

    function PrepareTimesheet()
    {
        var dept_id=$(".ap_department").val();
        var f_Date=$("#frmDate").val();
        var t_Date=$("#toDate").val();


        if(dept_id=='0')
        {
            toastr.error('Please select department');
            return false;
        }

        if(f_Date=='')
        {
            toastr.error('Please select fromdate');
            return false;
        }

        if(t_Date=='')
        {
            toastr.error('Please select todate');
            return false;
        }

        AxisPro.APICall('POST',ERP_FUNCTION_API_END_POINT + "?method=prepare_timesheet"
                       ,'dept_id='+$(".ap_department").val()+'&Emp_id='+$(".ap_employees").val()
                        +'&frmDate='+$("#frmDate").val()+'&toDate='+$("#toDate").val()+"&pagecnt="+$("#hdnPageCnt").val(), function (resdata) {

                       if(resdata.status=='FAIL')
                       {
                           alert(resdata.msg);
                       }
                       else
                       {
                           $(".table-responsive").html(resdata);

                           $(".page-link").click(function()
                           {
                               var alt=$(this).attr('alt');
                               $("#hdnPageCnt").val(alt);
                               PrepareTimesheet();
                           });
                       }

                       $(".ClsAttendence").click(function()
                       {
                           var alt_id=$(this).attr('altid');
                           $("#HdnTxtId").val(alt_id);
                           $("#ddlLeaveType").val($(this).attr('alt_code'));
                           $('#modalLoginForm').modal('show');
                       });

        });

    }


    $("#btnUpdate").click(function()
    {
       var pk_id=$("#HdnTxtId").val();
       var leave_type=$("#ddlLeaveType").val();
       var in_hr=$("#ddlInTmeHour").val();
       var in_min=$("#ddlInTmeMin").val();
       var out_hr=$("#ddlOutHr").val();
       var out_min=$("#ddlOutMin").val();
       var in_format=$("#ddlInTmeFrmt").val();
       var out_format=$("#ddlOutTmeFrmt").val();

       var flag='';
       if(leave_type=='0')
       {
           toastr.error('ERROR!! Select leave type');
           flag=false;
       }

      if(flag=='')
      {
          AxisPro.APICall('POST',ERP_FUNCTION_API_END_POINT + "?method=update_attendence"
              ,'pk_id='+pk_id+'&leave_type='+leave_type+'&in_hour='+in_hr+'&in_min='+in_min+'&out_hr='+out_hr
              +'&out_min='+out_min+'&in_format='+in_format+'&out_format='+out_format, function (resdata)
              {
                  if(resdata.status=='OK')
                  {
                      toastr.success(resdata.msg);
                      $('#modalLoginForm').modal('hide');
                      PrepareTimesheet();
                  }

                  if(resdata.status=='FAIL')
                  {
                      toastr.error(resdata.msg);
                  }
              });
      }

    });

    $("#btnCancel").click(function()
    {
        $('#modalLoginForm').modal('hide');
    });

</script>
