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
                                    <?= trans('UPLOAD ZK ATTENDENCE') ?>
                                </h3>
                            </div>
                        </div>


                            <div class="kt-portlet__body" style="padding: 20px !important;">
                                <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                    <?= trans('ZK ATTENDENCE') ?>:
                                </label>

                                <div class="form-group row">
                                    <div class="col-sm-2" >
                                        <label class=""><?= trans('Choose Your CSV File To Upload') ?>:</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="file" name="Filetoupload" id="Filetoupload">
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <label style="height: 13px;"></label>
                                    <div class="input-group">
                                        <button type="button" onclick="UpdateSettings();" class="btn btn-primary">
                                            <?= trans('Upload Attendence') ?>
                                        </button>&nbsp;&nbsp;
                                        <button type="button" onclick="ResetPaySlip();"   class="btn btn-primary btnResetSlips">
                                            <?= trans('Cancel') ?>
                                        </button>

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

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_saved_settings', format: 'json'}, function (data)
        {
            $('#hdnTxtpay').val(data.one);
            $('#hdnTxtDed').val(data.two);
            $('#TxtWrkTime').val(data.three);
        });


        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_accounts', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'account_code', 'accname', 'ClsAccounts',null,function () {
                $('.ClsAccounts').prepend('<option value="0">Select Account</option>');
                $('.ClsAccounts').val($('#hdnTxtpay').val());
            });

       AxisPro.PrepareSelectOptions(data, 'account_code', 'accname', 'ClsPayrollAccounts',null,function () {
                $('.ClsPayrollAccounts').prepend('<option value="0">Select Account</option>');
                $('.ClsPayrollAccounts').val($('#hdnTxtDed').val());
            });
        });
    });

   function UpdateSettings()
    {
        var flag='';
        if($('.ClsPayrollAccounts').val()=='0')
        {
            toastr.error('Please select payroll payable account');
            flag='fail';
        }

        if($('.ClsAccounts').val()=='0')
        {
            toastr.error('Please select deductible account');
            flag='fail';
        }
        if($('#TxtWrkTime').val()=='0' || $('#TxtWrkTime').val()=='')
        {
            toastr.error('Working hours cannot be blank or empty');
            flag='fail';
        }

        if(flag=='')
        {
            var payrol_pay_acc=$('.ClsPayrollAccounts').val();
            var ded_acc=$('.ClsAccounts').val();

            $('#loader').css('display','true');
            AxisPro.APICall('POST',
                ERP_FUNCTION_API_END_POINT + "?method=update_hrm_settings",{'payroll_payable_act':payrol_pay_acc,'payroll_deductleave_act':ded_acc,'payroll_work_hours':$('#TxtWrkTime').val()},
                function (data) {
                    $('#loader').css('display','true');
                });
        }

    }


</script>
