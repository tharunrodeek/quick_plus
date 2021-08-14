<?php
include "header.php";
include_once($path_to_root . "/API/API_HRM_Call.php");
$object=new API_HRM_Call();
$get_year_dropdown=$object->get_year_dropdown();
?>
<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch"
     xmlns="http://www.w3.org/1999/html">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">


                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('Process Payments') ?>

                                </h3>
                            </div>
                        </div>

                        <!--begin::Form-->
                        <form class="kt-form kt-form--label-right" id="leave_form" method="post"
                              action="<?= $erp_url ?>API/hub.php?method=export_payroll">
                            <div class="kt-portlet__body" style="padding: 20px !important;">
                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label><?= trans('YEAR') ?>:
                                        </label>
                                        <select class="form-control kt-select2 ap-year"
                                                name="ddl_year" id="ddl_year" >
                                            <option value="">SELECT</option>
                                            <?php echo $get_year_dropdown; ?>
                                        </select>
                                        <span class="error_note form-text text-muted kt-hidden">Please enter your full name</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <label class=""><?= trans('MONTH') ?>:</label>
                                        <select class="form-control kt-select2 ap-select2 ClsMonths"
                                                name="ddl_months" >
                                            <option value="">SELECT</option>
                                            <option value='1'>Janaury (01)</option>
                                            <option value='2'>February (02)</option>
                                            <option value='3'>March (03)</option>
                                            <option value='4'>April (04)</option>
                                            <option value='5'>May (05)</option>
                                            <option value='6'>June (06)</option>
                                            <option value='7'>July (07)</option>
                                            <option value='8'>August (08)</option>
                                            <option value='9'>September (09)</option>
                                            <option value='10'>October (10)</option>
                                            <option value='11'>November (11)</option>
                                            <option value='12'>December (12)</option>
                                        </select>
                                    </div>
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
                                        <label><?= trans('PAYROLLS') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_payrolls"
                                                    name="payslips_list" >
                                                <option value="0">---SELECT---</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <label style="height: 13px;"></label>
                                        <div class="input-group">
                                            <button type="button" onclick="Submit();" class="btn btn-primary">
                                                <?= trans('View') ?>
                                            </button>&nbsp;&nbsp;
                                            <!--input type="submit" id="btnDownload" name="btnDownload" value="Download Payroll" class="btn btn-primary" /-->
                                            <input type="hidden" id="hdnCurrentDate" name="hdnCurrentDate"  value="<?php echo date('d-m-Y') ?>"/>

                                            <input type="hidden" id="hdnAction" name="hdnAction"/>
                                        </div>
                                    </div>
                                </div>

                        </form>
                    </div>

                </div>

                <div class="row" style="width: 100%;">

                    <div class="kt-portlet" style="width:100%;">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('LIST PROCESSED PAYROLLS') ?>
                                </h3>
                            </div>
                            <div style="padding: 2%;/*margin-left: 24%;*/">
                                <img src="<?= $base_url ?>assets/media/img/ajax-loader.gif" id="loader"
                                     style="height: 16px;margin-top: 4%;display: none;"/>
                            </div>

                        </div>


                        <div class="table-responsive table_payout" style="padding: 7px 7px 7px 7px;overflow: auto;">
                            <table class="table table-bordered" id="list_employees">
                                <thead>
                                <th><?= trans('Emp-Code') ?></th>
                                <th><?= trans('Emp.Name') ?></th>
                                <th><?= trans('Net Salary Payable') ?></th>
                                <th><?= trans('Salary Processed') ?></th>
                                <th><?= trans('View GL') ?></th>
                                <th></th>
                                </thead>
                                <tbody id="list_employees_tbody">
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
<!--<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>-->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                 <table>
                     <tr>
                         <td>For Employee:</td><td><label id="lblEmpname"></label></td>
                     </tr>
                     <tr>
                         <td>Pay From :</td>
                         <td>
                             <select class="form-control kt-select2 ap-select2 ClsFrombank"
                                     name="bank_account" id="bank_account">
                                 <option value="">SELECT</option>
                             </select>
                         </td>
                     </tr>
                     <tr style="height:22px;"></tr>
                     <tr>
                         <td>Enter Amount :</td><td><input type="text" id="txtSalary_amount" name="txtSalary_amount" class="form-control"/></td>
                     </tr>
                 </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default ClsProcessPay" data-dismiss="modal">Process</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="hidden" id="hdnPayroll_id" />
                <input type="hidden" id="hdn_emp_id" />
                <input type="hidden" id="hdn_salary" />
            </div>
        </div>

    </div>
</div>
<iframe
        id="iframe1"
        frameborder="0" style="display:none;">
</iframe>
<div id="htmTpprint"></div>
<input type="hidden" id="hdnPageCnt" name="hdnPageCnt" />
<?php include "footer.php"; ?>
<script>
    var totalRows='0';
    $(document).ready(function () {

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_department',null,function () {
                $('.ap_department').prepend('<option value="0">Select Department</option>');
                $('.ap_department').val('0');
            });
        });

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_pay_bank_accounts', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'glcode', 'bank_account_name', 'ClsFrombank',null,function () {
                $('.ddl_salaryFrmAccount').prepend('<option value="0">Select From Account</option>');
                $('.ddl_salaryFrmAccount').val('0');
            });
        });

    });


    $(".ap_department").change(function() {

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_dept_payrolls",
            'dept_id='+$(".ap_department").val()+'&month='+$(".ClsMonths").val()+'&year='+$('#ddl_year').val(), function (data) {
                AxisPro.PrepareSelectOptions(data, 'id', 'payslip_id', 'ap_payrolls',null,function () {
                    $('.ap_payslips').prepend('<option value="0">---SELECT---</option>');
                    $('.ap_payslips').val('0');
                });
            });


        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+$(".ap_department").val(), function (data) {
            AxisPro.PrepareSelectOptions(data, 'Empid', 'Emp_name', 'ddl_employee',null,function () {
                $('.ddl_employee').prepend('<option value="0">----Choose Employee From List----</option>');
                $('.ddl_employee').val('0');
            });
        });


        if($(this).val()!='0')
        {
            $(".btnResetSlips").css('display','block');
        }
    });

    $('#btnVerify').click(function()
    {
        var alt=$(this).attr('alt');
        window.open(alt+'hrm_verify_gl_entries.php?id='+$('.ap_payrolls').val(),'_blank')
    });

    function Submit()
    {

        $("#spanShowPayslipID").html($(".ap_payrolls option:selected").text());
        $("#btnProcessPay").css('display','block');
        $("#btnPostGL").css('display','block');

        $('#txt_hdn_payroll_id').val($('.ap_payrolls').val());


        $('#list_employees').dataTable().fnDestroy();
        $('#list_employees').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :ERP_FUNCTION_API_END_POINT + "?method=list_payrolls_processed", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                data:{'payroll_id':$(".ap_payrolls").val()},
                error: function(){
                }
            }
        });

    }


    $('#list_employees tbody').on('click', 'td label.ClsProcessSalary', function (e){
        var alt_empl_id=$(this).attr('alt');
        var payroll_id=$(this).attr('altpayroll_id');
        $('#txtSalary_amount').val($(this).attr('alt-salary'));
        $('#lblEmpname').html($(this).attr('alt-empname'));
        $('#hdnPayroll_id').val(payroll_id);
        $('#hdn_emp_id').val(alt_empl_id);
        $('#hdn_salary').val($(this).attr('alt-salary'));

        AxisPro.APICall('POST',
            ERP_FUNCTION_API_END_POINT + "?method=view_payslip",'alt_empl_id='+alt_empl_id+'&payroll_id='+$('.ap_payrolls').val(),
            function (data) {
                if (data.status === 'OK') {
                   // $('#htmTpprint').html(data.msg);
                   // objFra.contentWindow.document.write(data.msg);
                    //objFra.contentWindow.focus();
                   // objFra.contentWindow.print();


                }
            }
        );
    });


    $('#list_employees tbody').on('click', 'td label.ClsViewSlip', function (e){
        var alt_empl_id=$(this).attr('alt');
        var objFra = document.getElementById('iframe1');
        $("#iframe1").contents().find("body").html('');
        AxisPro.APICall('POST',
            ERP_FUNCTION_API_END_POINT + "?method=view_payslip",'alt_empl_id='+alt_empl_id+'&payroll_id='+$('.ap_payrolls').val(),
            function (data) {
                if (data.status === 'OK') {
                    objFra.contentWindow.document.write(data.msg);
                    objFra.contentWindow.focus();
                    setTimeout(function(){  
                    
                    objFra.contentWindow.print();
                    }, 1000);
                    //$('#htmTpprint').html(data.msg);
                }
            }
        );
    });

    $('.ClsProcessPay').click(function()
    {

        var salary=$('#txtSalary_amount').val();
        var account=$('.ClsFrombank').val();
        var salary_amnt=$('#hdn_salary').val();
        var from=$('#ClsFrombank ').val();

        if(parseInt(salary_amnt)<parseInt($(this).val()))
        {
            alert('You cant enter more than '+salary_amnt);
            $('#txtSalary_amount').val(salary_amnt);
        }
        else
        {
            AxisPro.APICall('POST',
                ERP_FUNCTION_API_END_POINT + "?method=process_payment", /****************Function located in API_HRM_CAll.php******/
                'salary='+salary+'&account='+account+'&payroll_id='+$('#hdnPayroll_id').val()
                +'&emp_id='+$('#hdn_emp_id').val()+'&from='+from,
                function (data) {
                    $("#loader").css('display','none');
                    toastr.success(data.msg);
                    Submit();
                }
            );
        }


    });


    $('#txtSalary_amount').change(function()
    {
        var salary_amnt=$('#hdn_salary').val();

        if(parseInt(salary_amnt)<parseInt($(this).val()))
        {
            alert('You cant enter more than '+salary_amnt);
            $('#txtSalary_amount').val(salary_amnt);
        }

    });


</script>
