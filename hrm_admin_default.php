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
                                    <?= trans('DEFAULT SETTINGS') ?>
                                </h3>
                            </div>
                        </div>


                            <div class="kt-portlet__body" style="padding: 20px !important;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                            <?= trans('GL SETTINGS') ?>:
                                        </label>

                                        <div class="form-group row">
                                            <div class="col-sm-2" >
                                                <label class=""><?= trans('Gross Salary account ') ?>:</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <select class="form-control kt-select2 ap-select2 ClsGrossSalary"
                                                        name="GrossSalary" >
                                                    <option value="">SELECT</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-2" >
                                                <label class=""><?= trans('Payroll Payable Account') ?>:</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <select class="form-control kt-select2 ap-select2 ClsPayrollAccounts"
                                                        name="ClsPayrollAccounts" >
                                                    <option value="">SELECT</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-2" >
                                                <label class=""><?= trans('Deductible account ') ?>:</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <select class="form-control kt-select2 ap-select2 ClsAccounts"
                                                        name="ClsAccounts" >
                                                    <option value="">SELECT</option>
                                                </select>
                                            </div>
                                        </div>

                                        <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                            <?= trans('ESB Head Account') ?>:
                                        </label>
                                        <div class="form-group row">
                                            <div class="col-sm-2" >
                                                <label class=""><?= trans('ESB Head Account ') ?>:</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <select class="form-control kt-select2 ap-select2 ClsEsbAccounts"
                                                        name="ClsEsbAccounts" >
                                                    <option value="">SELECT</option>
                                                </select>
                                            </div>
                                        </div>



                                        <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                            <?= trans('Working Time') ?>:
                                        </label>
                                        <div class="form-group row">
                                            <div class="col-sm-2" >
                                                <label class=""><?= trans('Start Time ') ?>:</label>
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="number" id="TxtWrkStartTime" value="8" min="1" max="12" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-2" >
                                                <label class=""><?= trans('End Time ') ?>:</label>
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="number" id="TxtWrkEndTime" value="8" min="1" max="24" class="form-control"/>
                                            </div>
                                        </div>
                                        <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                            <?= trans('Salary Deduction Settings') ?>:
                                        </label>
                                        <div class="form-group row">
                                            <div class="col-sm-2" >
                                                <label class=""><?= trans('Choose Salary Deduction ') ?>:</label>
                                            </div>
                                            <div class="col-sm-4">
                                                <select id="ddl_salary_ded" class="form-control">
                                                    <option value="0">--SELECT--</option>
                                                    <option value="1">From Basic</option>
                                                    <option value="2">From Total Salary</option>
                                                </select>
                                            </div>
                                        </div>
                                        <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                            <?= trans('Over Time Calculation Rate') ?>:
                                        </label>
                                        <div class="form-group row">
                                            <div class="col-sm-2" >
                                                <label class=""><?= trans('Over Time Calculation Rate ') ?>:</label>
                                            </div>
                                            <div class="col-sm-4">
                                                <input type="text" id="txtOvertimeRate" name="txtOvertimeRate" class="form-control"/>
                                            </div>
                                        </div>


                                        <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                            <?= trans('Grace Time In Minutes') ?>:
                                        </label>
                                        <div class="form-group row">
                                            <div class="col-sm-2" >
                                                <label class=""><?= trans('Grace Time In Minutes ') ?>:</label>
                                            </div>
                                            <div class="col-sm-4">
                                                <select id="ddl_GraceTime" class="form-control">
                                                     <option value="0">---Select--</option>
                                                    <?php for($i=0;$i<=60;$i++){ ?>
                                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-4">


                                        <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                            <?= trans('PF Settings') ?>:
                                        </label>
                                        <div class="form-group row">
                                            <div class="col-sm-6" >
                                                <label class=""><?= trans('PF Percentage Ded. From Employee (%)') ?>:</label>
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="text" id="txt_pf_ded_from_emp" name="txt_pf_ded_from_emp" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6" >
                                                <label class=""><?= trans('PF Percentage From Company (%)') ?>:</label>
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="text" id="txt_pf_per_comp" name="txt_pf_per_comp" class="form-control"/>
                                            </div>
                                        </div>


                                        <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                            <?= trans('Cutoff Date') ?>:
                                        </label>
                                        <div class="form-group row">
                                            <div class="col-sm-6" >
                                                <label class=""><?= trans('Each Month Of') ?>:</label>
                                            </div>
                                            <div class="col-sm-5">
                                                <select id="cutoff_date" name="cutoff_date" class="form-control">
                                                    <option value="0">--SELECT--</option>
                                                    <?php for($i=1;$i<=31;$i++){ ?>
                                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <form class="kt-form kt-form--label-right" id="Frmprivacy">
                                            <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                                <?= trans('Manage Policy & Code Of Conduct') ?>:
                                            </label>
                                            <div class="form-group row">
                                                <div class="col-sm-6" >
                                                    <label class=""><?= trans('Policy') ?>:</label>
                                                </div>
                                                <div class="col-sm-2">
                                                    <input type="file" id="policy" name="policy"/>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-6" >
                                                    <label class=""><?= trans('Code Of Conduct') ?>:</label>
                                                </div>
                                                <div class="col-sm-2">
                                                    <input type="file" id="code_of_condct" name="code_of_condct"/>
                                                </div>
                                            </div>
                                        </form>



                                        <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                            <?= trans('Manage Prefixs') ?>:
                                        </label>
                                        <div class="col-xl" style="height:250px;overflow: auto;">
                                            <div class="form-group row">
                                                <div class="col-sm-6" >
                                                    <label class=""><?= trans('Leave Request Prefix') ?>:</label>
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="text" id="prefix_leave" name="prefix_leave" class="form-control"/>
                                                </div>

                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-6" >
                                                    <label class=""><?= trans('Loan Request Prefix') ?>:</label>
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="text" id="prefix_loan" name="prefix_loan" class="form-control"/>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-6" >
                                                    <label class=""><?= trans('Passport Request Prefix') ?>:</label>
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="text" id="prefix_passport" name="prefix_passport" class="form-control"/>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-6" >
                                                    <label class=""><?= trans('Certificate Request Prefix') ?>:</label>
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="text" id="prefix_certi" name="prefix_certi" class="form-control"/>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-6" >
                                                    <label class=""><?= trans('NOC Request Prefix') ?>:</label>
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="text" id="prefix_noc" name="prefix_noc" class="form-control"/>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-6" >
                                                    <label class=""><?= trans('Asset Request Prefix') ?>:</label>
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="text" id="prefix_asset" name="prefix_asset" class="form-control"/>
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <div class="col-sm-6" >
                                                    <label class=""><?= trans('Asset Return Request Prefix') ?>:</label>
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="text" id="prefix_asset_return" name="prefix_asset_return" class="form-control"/>
                                                </div>
                                            </div>
                                    </div>


                                        <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                            <?= trans('Personal Time Incorporation') ?>:
                                        </label>
                                        <div class="form-group row">
                                            <div class="col-sm-6" >
                                                <label class=""><?= trans('In Hours') ?>:</label>
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="text" id="txt_personal_ded_hrs" name="txt_personal_ded_hrs" class="form-control"/>
                                            </div>
                                        </div>


                                        <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                            <?= trans('Personal Time Deduction Period') ?>:
                                        </label>
                                        <div class="form-group row">
                                            <div class="col-sm-3" >
                                                <label class=""><?= trans('Choose Deduction Peroid') ?>:</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <select class="form-control" id="ddl_personal_ded_opt" name="ddl_personal_ded_opt">
                                                    <option value="0">------SELECT------</option>
                                                    <option value="1">Month Period</option>
                                                    <option value="2">Payroll Period</option>
                                                </select>
                                            </div>
                                        </div>

                                        <label style="height: 23px;text-decoration: underline;font-weight: bold;color: black;">
                                            <?= trans('Personal Time Assigned Leave') ?>:
                                        </label>
                                        <div class="form-group row">
                                            <div class="col-sm-3" >
                                                <label class=""><?= trans('Choose Leave Type') ?>:</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <select class="form-control kt-select2 ap-select2 ap_leave_types"
                                                        name="leave_type" >
                                                    <option value="">SELECT</option>
                                                </select>
                                            </div>
                                        </div>







                                </div>

                                </div>

                                <div class="col-lg-12"  align="center" style=" padding: 16px;
    border-top: 1px solid #ccc;
    margin-top: 2%;">
                                    <div style="margin-left: 524px;">

                                        <div class="input-group">
                                            <button type="button" onclick="UpdateSettings();" class="btn btn-primary">
                                                <?= trans('Update') ?>
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
</div>
 <input type="hidden" id="hdnTxtpay" value="0"/>
 <input type="hidden" id="hdnTxtDed" value="0"/>
 <input type="hidden" id="hdnPayableAcc" value="0"/>
 <input type="hidden" id="hdnDedctionAcc" value="0"/>
 <input type="hidden" id="hdnOvertimeAcc" value="0"/>
 <input type="hidden" id="hdnGrossSalaryAcc" value="0"/>
 <input type="hidden" id="hdnEsbAccount" value="0"/>
    <input type="hidden" id="hdn_PersonalTime_AssignedLeave" value="0"/>
    <input type="hidden" id="hdn_hr_access_mapping" value="0"/>
<?php include "footer.php"; ?>
<script>
    $(document).ready(function () {


        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_saved_settings', format: 'json'}, function (data)
        {
            //alert(data['payroll_gorss_salary_act']);
            $('#ddl_salary_ded').val(data['payroll_salary_deduction']);
            $('#TxtWrkStartTime').val(data['payroll_work_hours']);
            $('#cutoff_date').val(data['payroll_cutoff_date']);
            $('#txtOvertimeRate').val(data['payroll_overtime_rate']);
            $('#hdnPayableAcc').val(data['payroll_payable_act']);
            $('#hdnOvertimeAcc').val(data['payroll_overtime_act']);
            $('#hdnDedctionAcc').val(data['payroll_deductleave_act']);
            $('#hdnGrossSalaryAcc').val(data['payroll_gorss_salary_act']);
            $('#TxtWrkEndTime').val(data['payroll_work_hours_to']);
            $('#txt_pf_ded_from_emp').val(data['payroll_emp_pf_percent']);
            $('#txt_pf_per_comp').val(data['payroll_pf_comp_percent']);
            $('#hdnEsbAccount').val(data['payroll_esb_account']);
            $('#ddl_GraceTime').val(data['payroll_grace_time']);
                $('#txt_personal_ded_hrs').val(data['payroll_personal_time_hrs']);
                $('#ddl_personal_ded_opt').val(data['payroll_personal_selection']);
                //$('.ap_leave_types').val(data['payroll_personal_assigned_leave']);


            $('#prefix_leave').val(data['leave_request_pfx']);
            $('#prefix_loan').val(data['loan_request_pfx']);
            $('#prefix_passport').val(data['passport_request_pfx']);
            $('#prefix_certi').val(data['certif_request_pfx']);
            $('#prefix_noc').val(data['noc_request_pfx']);
            $('#prefix_asset').val(data['asset_request_pfx']);
            $('#prefix_asset_return').val(data['asset_return_req_pfx']);
                $('#hdn_PersonalTime_AssignedLeave').val(data['payroll_personal_assigned_leave']);
                $('#hdn_hr_access_mapping').val(data['payroll_hr_access_level_mapping']);

        });

        
        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_accounts', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'account_code', 'accname', 'ClsAccounts',null,function () {
                $('.ClsAccounts').prepend('<option value="0">Choose Account</option>');
                $('.ClsAccounts').val($('#hdnDedctionAcc').val());
            });

           AxisPro.PrepareSelectOptions(data, 'account_code', 'accname', 'ClsPayrollAccounts',null,function () {
                $('.ClsPayrollAccounts').prepend('<option value="0">Choose Account</option>');
                $('.ClsPayrollAccounts').val($('#hdnPayableAcc').val());
               //$('.ClsPayrollAccounts').val('6001');
            });

           AxisPro.PrepareSelectOptions(data, 'account_code', 'accname', 'ClsOvertime',null,function () {
                $('.ClsOvertime').prepend('<option value="0">Choose Account</option>');
                $('.ClsOvertime').val( $('#hdnOvertimeAcc').val());
            });

            AxisPro.PrepareSelectOptions(data, 'account_code', 'accname', 'ClsGrossSalary',null,function () {
                $('.ClsGrossSalary').prepend('<option value="0">Choose Account</option>');
                $('.ClsGrossSalary').val( $('#hdnGrossSalaryAcc').val());
            });

            AxisPro.PrepareSelectOptions(data, 'account_code', 'accname', 'ClsEsbAccounts',null,function () {
                $('.ClsEsbAccounts').prepend('<option value="0">Choose Account</option>');
                $('.ClsEsbAccounts').val( $('#hdnEsbAccount').val());
            });


        });


            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_leave_types', format: 'json'}, function (data) {
                AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_leave_types',null,function () {
                    $('.ap_leave_types').prepend('<option value="0">Choose leave type</option>');
                    $('.ap_leave_types').val( $('#hdn_PersonalTime_AssignedLeave').val());
                });
            });

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_security_roles', format: 'json'}, function (data) {
                AxisPro.PrepareSelectOptions(data, 'id', 'role', 'cls_security_roles',null,function () {
                    $('.cls_security_roles').prepend('<option value="0">Choose security role</option>');
                    $('.cls_security_roles').val($('#hdn_hr_access_mapping').val());
                });
            });


    });


    $('.ap-datepicker').datepicker({
        format: 'dd-mm-yyyy'
    });

   function UpdateSettings()
    {
        var flag='';

        if($('#TxtWrkStartTime').val()=='0' || $('#TxtWrkEndTime').val()=='')
        {
            toastr.error('Working hours cannot be blank or empty');
            flag='fail';
        }
        if($('#ddl_salary_ded').val()=='0')
        {
            toastr.error('Choose Salary Deduction');
            flag='fail';
        }

        if(flag=='')
        {
            var payrol_pay_acc=$('.ClsPayrollAccounts').val();
            var ded_acc=$('.ClsAccounts').val();
            var overtime_acc=$('.ClsOvertime').val();

            var form = $("#Frmprivacy");
            var params = form.serializeArray();
            var files = $("#policy")[0].files;
            var formData = new FormData();
            for (var i = 0; i < files.length; i++) {
                formData.append('policy', files[i]);
            }
            var files_code = $("#code_of_condct")[0].files;
            for (var i = 0; i < files_code.length; i++) {
                formData.append('code_of_condct', files_code[i]);
            }

            $(params).each(function(index, element) {
                formData.append(element.name, element.value);
            });

            formData.append('payroll_payable_act',payrol_pay_acc);
            formData.append('payroll_deductleave_act',ded_acc);
            formData.append('payroll_work_hours',$('#TxtWrkStartTime').val());
            formData.append('payroll_salary_deduction',$('#ddl_salary_ded').val());
            formData.append('payroll_cutoff_date',$('#cutoff_date').val());
            formData.append('payroll_overtime_rate',$('#txtOvertimeRate').val());
            formData.append('payroll_overtime_act',overtime_acc);
            formData.append('payroll_gorss_salary_act',$('.ClsGrossSalary').val());
            formData.append('payroll_work_hours_to',$('#TxtWrkEndTime').val());
            formData.append('payroll_emp_pf_percent',$('#txt_pf_ded_from_emp').val());
            formData.append('payroll_pf_comp_percent',$('#txt_pf_per_comp').val());
            formData.append('payroll_esb_account',$('.ClsEsbAccounts').val());
            formData.append('payroll_grace_time',$('#ddl_GraceTime').val());
                formData.append('payroll_personal_time_hrs',$('#txt_personal_ded_hrs').val());
                formData.append('payroll_personal_selection',$('#ddl_personal_ded_opt').val());
                formData.append('payroll_personal_assigned_leave',$('.ap_leave_types').val());
                formData.append('payroll_hr_access_level_mapping',$('.cls_security_roles').val());


            formData.append('leave_request_pfx',$('#prefix_leave').val());
            formData.append('loan_request_pfx',$('#prefix_loan').val());
            formData.append('passport_request_pfx',$('#prefix_passport').val());
            formData.append('certif_request_pfx',$('#prefix_certi').val());
            formData.append('noc_request_pfx',$('#prefix_noc').val());
            formData.append('asset_request_pfx',$('#prefix_asset').val());
            formData.append('asset_return_req_pfx',$('#prefix_asset_return').val());


            $.ajax({
                type: "POST",
                url: ERP_FUNCTION_API_END_POINT+"?method=update_hrm_settings",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    var res = JSON.parse(data);
                    if(res.status=='OK')
                    {
                        toastr.success('Successfully Updated Settings');
                    }
                    else
                    {
                        toastr.error('There are some error occured , during update');
                    }
                }
            });


        }

    }


</script>
