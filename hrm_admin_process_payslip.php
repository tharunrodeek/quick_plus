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
                                    <?= trans('PROCESS PAYSLIP') ?>

                                </h3>
                            </div>
                        </div>

                        <!--begin::Form-->
                        <form class="kt-form kt-form--label-right" id="leave_form">
                            <div class="kt-portlet__body" style="padding: 20px !important;">
                                <div class="form-group row">

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
                                        <label><?= trans('PAYSLIPS') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_payslips"
                                                    name="payslips_list" >
                                                <option value="0">---SELECT---</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <label style="height: 13px;"></label>
                                        <div class="input-group">
                                            <button type="button" onclick="SubmitPayout();" class="btn btn-primary">
                                                <?= trans('View PaySlips') ?>
                                            </button>&nbsp;&nbsp;

                                            <input type="hidden" id="hdnCurrentDate" name="hdnCurrentDate"  value="<?php echo date('d-m-Y') ?>"/>

                                            <input type="hidden" id="hdnAction" name="hdnAction"/>
                                        </div>
                                    </div>
                                </div>

                        </form>
                    </div>

                </div>

                <div class="row" style="width: 100%;">

                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('LIST PAYSLIPS') ?>
                                    <div style="display: contents;color: blue;">
                                        <?= trans(' | SALARY PROCESSING AGAINST :') ?><span id="spanShowPayslipID" style="color: red;"></span>
                                    </div>
                                </h3>
                            </div>
                            <div style="padding: 2%;margin-left: 33%;">
                                <img src="<?= $base_url ?>assets/media/img/ajax-loader.gif" id="loader"
                                     style="height: 16px;margin-top: 4%;display: none;"/>
                            </div>
                            <div style="padding: 2%;">

                                <input type="button" id="btnProcessPay" value="Process Payout" class="btn btn-primary" style="background-color: green;display: none;"/>

                            </div>
                        </div>


                        <div class="table-responsive table_payout" style="padding: 7px 7px 7px 7px;overflow: auto;">

                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
<input type="hidden" id="hdnPageCnt" name="hdnPageCnt" />
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
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_dept_payslips",'dept_id='+$(".ap_department").val()+'&month='+$(".ClsMonths").val(), function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'payslip_id', 'ap_payslips',null,function () {
                $('.ap_payslips').prepend('<option value="0">---SELECT---</option>');
                $('.ap_payslips').val('0');
            });
        });


        if($(this).val()!='0')
        {
           $(".btnResetSlips").css('display','block');
        }
    });


    function SubmitPayout()
    {

        $("#spanShowPayslipID").html($(".ap_payslips option:selected").text());
        $("#btnProcessPay").css('display','block');

        $(".table_payout").empty();
        AxisPro.APICall('POST',ERP_FUNCTION_API_END_POINT + "?method=list_payslip_details"
            ,'payslip_id='+$(".ap_payslips").val()+"&pagecnt="+$("#hdnPageCnt").val(), function (resdata) {
             $(".table_payout").html(resdata);


                $('.ClsPayingAmnt').change(function(){
                    var altsalary=$(this).attr('altsalary');

                    if(Number(altsalary)<Number($(this).val()))
                    {
                        toastr.error('ERROR!!! The entered amount is greater than the salary need to pay.');
                        $(this).val(altsalary);
                    }
                });

                /*---------------------------------PAYOUT PROCEESS-----------------*/

                /*-----------------------------------END---------------------------*/

                $(".page-link").click(function()
                {
                    var alt=$(this).attr('alt');
                    $("#hdnPageCnt").val(alt);
                    SubmitPayout();
                });


        });
    }

    $("#btnProcessPay").click(function()
    {
        var cnfrm=confirm('You are going to process salary payouts. Please Confirm');
        if(cnfrm==true)
        {
            $('#loader').css('display','block');
            var flag='';
            var process_salary = [];
            /*-----------------------SAVING ENTERED VALUES TO AN ARRAY--------------*/
            $('.ClsPayingAmnt').each(function(){
                if($(this).val()!='' && $(this).val()!='0.00')
                {
                    var alt_id=$(this).attr('alt');
                    var altsalary=$(this).attr('altsalary');
                    var payslip_id=$(this).attr('alt_slip_id');
                    var memo=$(".ClsTxtMemo").val();
                    process_salary.push({amount:$(this).val(),  index: alt_id, actual_salary: altsalary, payslip_pk_id: payslip_id,memo: memo});
                }
            });

            if(process_salary.length=='0')
            {
                toastr.error('SORRY!! There is no values mentioned to process payout');
                flag='fail';
                $('#loader').css('display','none');
            }
            /*----------------------------------END----------------------------------*/
            //console.log(process_salary);
            if(flag=='')
            {
                AxisPro.APICall('POST',
                    ERP_FUNCTION_API_END_POINT + "?method=process_payouts",{'salaries':process_salary},
                    function (data) {
                        if(data.status=='OK')
                        {
                            toastr.success('Salary payout processed successfully');
                        }
                        else
                        {
                            toastr.error('ERROR!! There are some occured while processing payouts.');
                        }
                        $('#loader').css('display','none');
                    });
            }

        }

    });








</script>
