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
                                    <?= trans('SINGLE CLICK ATTENDENCE') ?>
                                </h3>
                            </div>
                        </div>


                            <div class="kt-portlet__body" style="padding: 20px !important;">
                                <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                    <?= trans('CHOOSE FILTERS') ?>:
                                </label>

                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label><?= trans('DEPARTMENT') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_department"
                                                    name="ddl_department" >
                                                <option value="">SELECT</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <label><?= trans('EMPLOYEE') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_employees"
                                                    name="leave_type" >
                                                <option value="">All Employees</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <label><?= trans('MONTH') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ClsMonths"
                                                    name="ddl_months" >
                                                <option value="">SELECT</option>
                                                <option value='01'>Janaury (01)</option>
                                                <option value='02'>February (02)</option>
                                                <option value='03'>March (03)</option>
                                                <option value='04'>April (04)</option>
                                                <option value='05'>May (05)</option>
                                                <option value='06'>June (06)</option>
                                                <option value='07'>July (07)</option>
                                                <option value='08'>August (08)</option>
                                                <option value='09'>September (09)</option>
                                                <option value='10'>October (10)</option>
                                                <option value='11'>November (11)</option>
                                                <option value='12'>December (12)</option>
                                            </select>
                                        </div>
                                    </div>

                                   <!-- <div class="col-lg-2">
                                        <label><?/*= trans('START TIME') */?>:</label>
                                        <div class="input-group">
                                            <input type="time" id="strtT" name="appt" class="form-control"
                                                   min="09:00" max="18:00" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <label><?/*= trans('END TIME') */?>:</label>
                                        <div class="input-group">
                                            <input type="time" id="endT" name="appt" class="form-control"
                                                   min="09:00" max="18:00" required>
                                        </div>
                                    </div>-->
                                </div>

                                <div class="col-lg-3">
                                    <label style="height: 13px;"></label>
                                    <div class="input-group">
                                        <button type="button" onclick="GenerateAttendance();" class="btn btn-primary">
                                            <?= trans('Generate Attendence') ?>
                                        </button>&nbsp;&nbsp;


                                        <img src="<?= $base_url ?>assets/media/img/ajax-loader.gif" id="loader" style="height: 16px;
    margin-top: 4%;display: none;"/>
                                    </div>
                                </div>

                            </div>


                </div>
            </div>
        </div>
    </div>
</div>

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

    $(".ap_department").change(function() {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+$(".ap_department").val(), function (data) {
            AxisPro.PrepareSelectOptions(data, 'Empid', 'Emp_name', 'ap_employees',null,function () {
                $('.ap_employees').prepend('<option value="0">All Employees</option>');
                $('.ap_employees').val('0');
            });
        });
    });

    $('.ap-datepicker').datepicker({
        format: 'dd-mm-yyyy',
        todayHighlight: true
    });

   function GenerateAttendance()
    {

        var flag='';
        if($('.ap_department').val()=='0')
        {
            toastr.error('Please select department');
            flag='fail';
        }


        if($('.ClsMonths').val()=='0')
        {
            toastr.error('Please select month');
            flag='fail';
        }

        if($('#strtT').val()=='')
        {
            toastr.error('Please select start time');
            flag='fail';
        }

        if($('#endT').val()=='')
        {
            toastr.error('Please select end time');
            flag='fail';
        }

        if(flag=='')
        {
            var dept_id=$('.ap_department').val();
            var month=$('.ClsMonths').val();
            var emp_id=$('.ap_employees').val();

            $('#loader').css('display','block');
            AxisPro.APICall('POST',
                ERP_FUNCTION_API_END_POINT + "?method=single_click_attendence",
                {'dept_id':dept_id,'month':month,'emp_id':emp_id,'starttime':$('#strtT').val(),'endtime':$('#endT').val()},
                function (data) {

                    if(data.status=='FAIL')
                    {
                        toastr.error(data.msg);
                    }

                    if(data.status=='SUCCESS')
                    {
                        toastr.success(data.msg);
                    }

                    $('#loader').css('display','none');
                });
        }

    }


</script>
