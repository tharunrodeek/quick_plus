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
                                    <?= trans('PROCESS PAYROLL') ?>

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
                                            <button type="button" onclick="SubmitPayout();" class="btn btn-primary">
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
                                    <?= trans('LIST EMPLOYEE') ?>
                                    <div style="display: contents;color: blue;">
                                        <?= trans(' | SALARY PROCESSING AGAINST :') ?><span id="spanShowPayslipID" style="color: red;"></span>
                                    </div>

                                </h3>
                            </div>
                            <div style="padding: 2%;/*margin-left: 24%;*/">
                                <img src="<?= $base_url ?>assets/media/img/ajax-loader.gif" id="loader"
                                     style="height: 16px;margin-top: 4%;display: none;"/>
                            </div>
                            <!--<div >
                                <label style="width: 244px;"><?/*= trans('CHOOSE FROM ACCOUNT') */?>:</label>
                                <div class="input-group">
                                    <select class="form-control kt-select2 ap-select2 ddl_salaryFrmAccount"
                                            name="ddl_fromaccount">
                                        <option value="">----Choose From Account----</option>
                                    </select>
                                </div>
                            </div>-->
                            <div style="/*padding: 2%;*/">
                                <!-- <input type="button" id="btnProcessPay" value="Process PayRoll" class="btn btn-primary" style="background-color: green;display: none;"/>
                                <form action="<?/*= $erp_url */?>API/hub.php?method=export_payroll" method="post">
                                    <input type="submit" name="btnSubmit" value="Download Payroll" class="btn btn-warning"></input>
                                    <input type="hidden" id="txt_hdn_payroll_id" name="txt_hdn_payroll_id" />
                                </form>-->


                                <div class="col-sm-12 text-center" style="display: flex;table-responsive table_payout">
                                    <input type="button" id="btnProcessPay" value="Process PayRoll" class="btn btn-primary" style="background-color: green;display: none;"/>&nbsp;&nbsp;
                                    <form action="<?= $erp_url ?>API/hub.php?method=export_payroll" method="post">
                                        <input type="submit" name="btnSubmit" value="Download Payroll" class="btn btn-warning clsExtraBtns" style="display:none;"/>
                                        <input type="hidden" id="txt_hdn_payroll_id" name="txt_hdn_payroll_id" />
                                    </form>
                                    &nbsp;&nbsp;<input type="submit" name="btnReset" value="Reset Payroll Entry" class="btn btn-warning btn-reset clsExtraBtns" style="display:none;"/>
                                </div>



                            </div>
                            <div style="/*padding: 2%;*/">
                                <!--input type="button" id="btnPostGL" value="Verify GL Entries" class="btn btn-primary"
                                style="background-color: green;display: none;"/-->
                                <!--<a href="#" class="btn btn-primary" id="btnVerify" target="_blank" alt="<?/*= $base_url */?>">Verify GL Entries</a>-->
                            </div>
                        </div>


                        <div class="table-responsive table_payout" style="padding: 7px 7px 7px 7px;overflow: auto;height: 450px;">

                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!---------------------------------POPUP MODEL-------------->
<div class="modal fade" id="modalPopupPostGL" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="padding: 1% !important;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h4 class="modal-title w-100 font-weight-bold">Verify GL</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">
                <div   style="display: inline-flex;">
                    <label style="width: 244px;"><?= trans('CHOOSE EMPLOYEE') ?>:</label>
                    <div class="input-group">
                        <select class="form-control kt-select2 ap-select2 ddl_employee"
                                name="ddl_employee">
                            <option value="">----Choose Employee From List----</option>
                        </select>
                    </div>
                </div>
                <div id="bndPopupBdy">

                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button class="btn btn-default" id="btnUpdate">Submit</button>
                <button class="btn btn-default" id="btnCancel">Cancel</button>
            </div>
        </div>
    </div>
</div>
<style>
    .modal-dialog {
        max-width: none !important;
        width: 80%;
    }
</style>
<!-----------------------------------END-------------------->
<input type="hidden" id="hdnPageCnt" name="hdnPageCnt" />
<?php include "footer.php"; ?>
<script>
    var totalRows='0',earnings_headings='0';
    $(document).ready(function () {

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_department',null,function () {
                $('.ap_department').prepend('<option value="0">Select Department</option>');
                $('.ap_department').val('0');
            });
        });

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_bankaccounts', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'account_code', 'bank_account_name', 'ddl_salaryFrmAccount',null,function () {
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

    function SubmitPayout()
    {

        $("#spanShowPayslipID").html($(".ap_payrolls option:selected").text());
        $("#btnProcessPay").css('display','block');
        $("#btnPostGL").css('display','block');

        $('#txt_hdn_payroll_id').val($('.ap_payrolls').val());

        $(".table_payout").empty();
        AxisPro.APICall('POST',ERP_FUNCTION_API_END_POINT + "?method=list_payroll_details"
            ,'payslip_id='+$(".ap_payrolls").val()+"&pagecnt="+$("#hdnPageCnt").val()
            +"&year="+$("#ddl_year").val()+"&month="+$(".ClsMonths").val(), function (resdata) {
                $(".table_payout").html(resdata);

                if($('#hdn_payroll_process').val()=='1')
                {
                    $('.clsExtraBtns').css('display','block');
                }


                $('.ClsPayingAmnt').change(function(){
                    var altsalary=$(this).attr('altsalary');

                    if(Number(altsalary)<Number($(this).val()))
                    {
                        toastr.error('ERROR!!! The entered amount is greater than the salary need to pay.');
                        $(this).val(altsalary);
                    }
                });

                totalRows=$("#hdnTotalHeadings").val();
                earnings_headings=$("#hdnTotal_Earnings_Headings").val();

                $('.txt_amt').change(function()
                {

                    var alt_row_id=$(this).attr('alt_incr');
                    var alt_element_id=$(this).attr('alt');
                    var alt_element_type=$(this).attr('alt_ded_ear');

                    var tot = [];
                    var tot_ear = [];
                    var arrayTotalWei='';
                    var arrayTotal_earnings='';
                    var sign='';

                    for(var k=0;k<=totalRows;k++)
                    {
                        if($.isNumeric($('#id_txt_'+alt_row_id+'_'+k).val()))
                        {
                            if(alt_element_type=='2')
                            {
                                tot.push(parseFloat(sign+$('#id_txt_'+alt_row_id+'_'+k).val()));
                            }

                        }

                        if($.isNumeric($('#id_ear_'+alt_row_id+'_'+k).val()))
                        {
                              var alt_non_fixed_ear=$('#id_ear_'+alt_row_id+'_'+k).attr('alt_non_fixed_ear');

                              if(alt_non_fixed_ear=='1')
                              {
                                  tot_ear.push(parseFloat(sign+$('#id_ear_'+alt_row_id+'_'+k).val()));
                              }

                        }

                    }


                    arrayTotalWei = SumArray(tot);

                    arrayTotal_earnings = SumArray(tot_ear);
                    var replace_tot=Math.abs(arrayTotalWei);
                    var replace_tot_ear=Math.abs(arrayTotal_earnings);






                    var empl_leave_absent_dedn=$('#txt_absent_dedn_'+alt_row_id).val();
                

                   /* if(replace_tot_ear>0)
                    {
                        $('#txtTotal_salary_'+alt_row_id).val(replace_tot_ear);
                    }*/
                    //var tot_dedn=replace_tot+parseFloat($('#txt_absent_dedn_'+alt_row_id).val());

                     $('#txt_tot_salary_dedn_'+alt_row_id).val(parseFloat(empl_leave_absent_dedn)+parseFloat(arrayTotalWei));
                     var hdn_emp_dept=$('#hdn_empl_deptid_'+alt_row_id).val();


                    if($('#txt_commission_'+alt_row_id).val()!='' && $('#txt_commission_'+alt_row_id).val()!='0')
                    {
                        if(hdn_emp_dept=='1')
                        {
                            var tot_emp_salary=parseFloat($('#txtTotal_salary_'+alt_row_id).val())+parseFloat($('#txt_commission_'+alt_row_id).val())-(parseFloat($('#txt_tot_salary_dedn_'+alt_row_id).val())-parseFloat($('#txt_deduction_amnt_'+alt_row_id).val()));
                        }
                        else
                        {
                            var tot_emp_salary=parseFloat($('#txtTotal_salary_'+alt_row_id).val())+(parseFloat($('#txt_commission_'+alt_row_id).val())-parseFloat($('#txt_tot_salary_dedn_'+alt_row_id).val())-parseFloat($('#txt_deduction_amnt_'+alt_row_id).val())-parseFloat($('#txtTotal_salary_'+alt_row_id).val()) );
                        }


                    }
                    else
                    {
                        if(hdn_emp_dept=='1') {
                            var tot_emp_salary=parseFloat($('#txtTotal_salary_'+alt_row_id).val())-(parseFloat($('#txt_tot_salary_dedn_'+alt_row_id).val())-parseFloat($('#txt_deduction_amnt_'+alt_row_id).val()));
                        }
                        else
                        {
                            var tot_emp_salary=parseFloat($('#txtTotal_salary_'+alt_row_id).val())-(parseFloat($('#txt_tot_salary_dedn_'+alt_row_id).val())-parseFloat($('#txt_deduction_amnt_'+alt_row_id).val()));
                        }

                    }


                    $('#txtTotal_salary_paying_'+alt_row_id).val(tot_emp_salary);
                });


                $('.cls_earnings_amt').change(function()
                {
                    var alt_row_id=$(this).attr('alt_incr');
                    var alt_element_id=$(this).attr('alt');
                    var alt_element_type=$(this).attr('alt_ded_ear');

                    var tot_ear = [];
                    var arrayTotal_earnings='';
                     var tot_ded = [];

                    for(var k=0;k<=earnings_headings;k++)
                    {

                        if($.isNumeric($('#id_ear_'+alt_row_id+'_'+k).val()))
                        {
                            var alt_non_fixed_ear=$('#id_ear_'+alt_row_id+'_'+k).attr('alt_non_fixed_ear');
                            if(alt_non_fixed_ear=='1')
                            {
                                tot_ear.push(parseFloat($('#id_ear_'+alt_row_id+'_'+k).val()));
                            }
                        }



                        if($.isNumeric($('#id_txt_'+alt_row_id+'_'+k).val()))
                        {
                            if(alt_element_type=='2')
                            {
                                tot_ded.push(parseFloat($('#id_txt_'+alt_row_id+'_'+k).val()));
                            }

                        }
                    }

                    arrayTotal_earnings = SumArray(tot_ear);
                    var replace_tot_ear=Math.abs(arrayTotal_earnings);

                    var arrayTotal_deduction = SumArray(tot_ded);
                    var replace_tot_deduction=Math.abs(arrayTotal_deduction);

                    var tot_salary_ded=parseFloat(replace_tot_deduction)+parseFloat($('#txt_tot_salary_dedn_'+alt_row_id).val());

                    $('#txt_tot_salary_dedn_'+alt_row_id).val(tot_salary_ded);



                    var tot_salary= $('#txt_empl_tot_salary_'+alt_row_id).val();

                    $('#txtTotal_salary_paying_'+alt_row_id).val(parseFloat(tot_salary)+parseFloat(replace_tot_ear)-parseFloat($('#txt_tot_salary_dedn_'+alt_row_id).val()));
                });






                $('.ClsCommisn').change(function()
                {
                        var alt=$(this).attr('alt');
                    
                        var tot_salary_ded_emp=$('#txt_tot_salary_dedn_'+alt).val();
                        var emp_tot_salary=$('#txt_empl_tot_salary_'+alt).val();
                        var empl_total_salary=parseFloat(emp_tot_salary)-parseFloat(tot_salary_ded_emp);
 
 
                        if(parseInt($('#txt_commission_'+alt).val())>parseInt($('#txtTotal_salary_'+alt).val()))
                        {
                            $('#txt_net_commision_'+alt).val($('#txt_commission_'+alt).val()-$('#txtTotal_salary_'+alt).val());

                            //var tot=parseFloat($('#txtTotal_salary_paying_'+alt).val())+parseFloat($('#txt_net_commision_'+alt).val());

                            $('#txtTotal_salary_paying_'+alt).val(parseFloat(empl_total_salary)+parseFloat($('#txt_net_commision_'+alt).val()));
                        }
                        else
                        {
                            $('#txt_net_commision_'+alt).val($('#txt_commission_'+alt).val());
                            //var tot=parseFloat($('#txtTotal_salary_paying_'+alt).val())+parseFloat($('#txt_net_commision_'+alt).val());

                            $('#txtTotal_salary_paying_'+alt).val(parseFloat(empl_total_salary)+parseFloat($('#txt_net_commision_'+alt).val()));
                        }

                });

                $('.ClsDed_Mistake_Error').change(function()
                {
                    var alt=$(this).attr('alt');
                    // $('#txt_net_commision_'+alt).val($('#txt_commission_'+alt).val()-$('#txt_ded_mistake_err_'+alt).val()-$('#txtTotal_salary_'+alt).val());
                    calculate_tot_salry_ded(alt);
                });

                $('.Cls_Employee_Tot_Sala').change(function()
                {
                    var alt=$(this).attr('alt');
                    $('#txt_net_commision_'+alt).val($('#txt_commission_'+alt).val()-$('#txt_ded_mistake_err_'+alt).val()-$('#txtTotal_salary_'+alt).val());
                });

                $('.ClsDed_Error').change(function()
                {
                    var alt=$(this).attr('alt');
                    calculate_tot_salry_ded(alt);
                });

                $('.ClsAdv_ded').change(function()
                {
                    var alt=$(this).attr('alt');
                    calculate_tot_salry_ded(alt);
                });

                $('.ClsDed_Mistake').change(function()
                {
                    var alt=$(this).attr('alt');
                    calculate_tot_salry_ded(alt);
                });

                $('.ClsSalaryRelse').change(function()
                {
                    var alt=$(this).attr('alt');
                    calculate_tot_salry_ded(alt);
                });

                $('.ClsHoldSalary').change(function()
                {
                    var alt=$(this).attr('alt');
                    calculate_tot_salry_ded(alt);
                });

                $('.ClsDed_amnt').change(function()
                {
                    var alt=$(this).attr('alt');
                    calculate_tot_salry_ded(alt);
                });

                $('.ClsAdd_Amounts').change(function()
                {
                    var alt=$(this).attr('alt');
                    calculate_tot_salry_ded(alt);
                });

                $('.ClsAbsnt_dedn').change(function()
                {
                    var alt=$(this).attr('alt');
                    calculate_tot_salry_ded(alt);
                });

                $('.ClsOtAmount').change(function()
                {
                    var alt=$(this).attr('alt');
                    calculate_tot_salry_adding(alt);
                });

                $('.ClsPf').change(function()
                {
                    var alt=$(this).attr('alt');
                    var tot_dedn=$('#txt_tot_salary_dedn_'+alt).val();
                    $('#txt_tot_salary_dedn_'+alt).val(parseFloat(tot_dedn)+parseFloat($(this).val()));
                    var tot_emp_salary=$('#hdn_tot_salary_include_overtime_'+alt).val();
                    $('#txt_empl_tot_salary_'+alt).val(parseFloat(tot_emp_salary)-parseFloat($('#txt_tot_salary_dedn_'+alt).val()));
                    $('#txtTotal_salary_paying_'+alt).val(parseFloat($('#txt_net_commision_'+alt).val())+parseFloat($('#txt_empl_tot_salary_'+alt).val()));
                });




                function calculate_tot_salry_ded(alt) {
                    var tot=parseFloat($('#txt_absent_dedn_'+alt).val());


                    var tot_pay_ele_ded = [];
                    var merge_sum='';
                    for(var k=0;k<=totalRows;k++)
                    {
                        if($.isNumeric($('#id_txt_'+alt+'_'+k).val()))
                        {
                                tot_pay_ele_ded.push(parseFloat($('#id_txt_'+alt+'_'+k).val()));
                        }
                    }


                    var arrayTotalWei = SumArray(tot_pay_ele_ded);
                    merge_sum=parseFloat(arrayTotalWei)+parseFloat(tot);


                    $('#txt_tot_salary_dedn_'+alt).val(merge_sum);
                    var emp_tot_salry=$('#hdn_tot_salary_include_overtime_'+alt).val();

                     var additonal_amount=$('#txt_addAmount_'+alt).val();
                    if(additonal_amount==undefined || additonal_amount=='')
                    {
                        additonal_amount=0;
                    }


                    $('#txt_empl_tot_salary_'+alt).val(merge_sum);

                    var commsion=$('#txt_commission_'+alt).val();

                    if(commsion==undefined || commsion=='')
                    {
                        commsion=0;
                    }


                   

                    if(commsion!=0)
                    {
                       $('#txtTotal_salary_paying_'+alt).val(parseFloat(commsion)-parseFloat(merge_sum)); 
                    }
                    else
                    {
                       $('#txtTotal_salary_paying_'+alt).val(parseFloat($('#txt_empl_tot_salary_'+alt).val())+parseFloat(merge_sum));
                    }
                   




                }


                function calculate_tot_salry_adding(alt) {
                    var tot=parseFloat($('#txt_absent_dedn_'+alt).val());


                    var tot_pay_ele_ded = [];
                    var merge_sum='';
                    for(var k=0;k<=totalRows;k++)
                    {
                        if($.isNumeric($('#id_txt_'+alt+'_'+k).val()))
                        {
                            tot_pay_ele_ded.push(parseFloat($('#id_txt_'+alt+'_'+k).val()));
                        }
                    }


                    var arrayTotalWei = SumArray(tot_pay_ele_ded);
                    merge_sum=parseFloat(arrayTotalWei)+parseFloat(tot);


                    $('#txt_tot_salary_dedn_'+alt).val(merge_sum);
                    var emp_tot_salry=$('#hdn_tot_salary_include_overtime_'+alt).val();
                    var additonal_amount=$('#txt_addAmount_'+alt).val();
                    if(additonal_amount==undefined || additonal_amount=='')
                    {
                        additonal_amount=0;
                    }

                    var ot_amount=$('#txt_otAmount_'+alt).val();

                    //$('#txt_empl_tot_salary_'+alt).val(emp_tot_salry-merge_sum+parseFloat(ot_amount));

                    var commsion=$('#txt_net_commision_'+alt).val();

                    if(commsion==undefined || commsion=='')
                    {
                        commsion=0;
                    }

                    //alert($('#txt_empl_tot_salary_'+alt).val());
                    //console.log(emp_tot_salry-merge_sum+parseFloat(ot_amount));

                    $('#txtTotal_salary_paying_'+alt).val(emp_tot_salry-merge_sum+parseFloat(ot_amount)+parseFloat(commsion));
                }



                function SumArray(numArray){
                    var sum=0;
                    for(var i=0;i<numArray.length;i++){
                        if (!isNaN(numArray[i])) {
                            sum += numArray[i];
                        }
                    }
                    return sum;
                }


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
            var payelemnts_push=[];
            var totoal_salary_sum=[];
            /*-----------------------SAVING ENTERED VALUES TO AN ARRAY--------------*/
            var total_payelemnt_cnt=$("#hdnTotalHeadings").val();;
            var p;
            var t;
            $.each($("input[name='chkEmployee']:checked"), function(){
                if($(this).val()!='' && $(this).val()!='0.00')
                {
                    var alt_id=$(this).attr('alt_incr');
                    var payslip_id=$(this).attr('alt_id');
                    var commison=$('#txt_commission_'+alt_id).val();
                    var net_commison=$('#txt_net_commision_'+alt_id).val();
                    var absent_hrs=$('#txt_absent_hrs_'+alt_id).val();
                    var absent_ded_amount=$('#txt_deduction_amnt_'+alt_id).val();
                    var present_days=$('#txt_present_days_'+alt_id).val();
                    var absent_days=$('#txt_absent_days_'+alt_id).val();
                    var absent_ded=$('#txt_absent_dedn_'+alt_id).val();
                    var tot_salary_ded=$('#txt_tot_salary_dedn_'+alt_id).val();
                    var tot_salary=$('#txt_empl_tot_salary_'+alt_id).val();
                    var tot_salary_payble=$('#txtTotal_salary_paying_'+alt_id).val();
                    var trans_mode=$('#txt_trn_mode_'+alt_id).val();
                    var memo=$('#txt_memo_'+alt_id).val();
                    var overtime_amount=$('#txt_otAmount_'+alt_id).val();
                    var overtime_hours=$('#txt_ot_'+alt_id).val();
                    var loan_amount=$('#txt_loan_amnt_'+alt_id).val();
                    var adv_amount=$('#txt_adv_amnt_'+alt_id).val();
                    var alt_emp_id=$(this).attr('alt_empl_id');
                    var emp_tot_slaary=$('#txtTotal_salary_'+alt_id).val();
                    var pf_amount=$('#txt_pf_'+alt_id).val();
                    var month=$('.ClsMonths').val();
                    var year=$('#ddl_year').val();

                    for(p=0;p<=total_payelemnt_cnt;p++)
                    {
                        var element_val=$('#id_txt_'+alt_id+'_'+p).val();
                        var element_id=$('#id_txt_'+alt_id+'_'+p).attr('alt');
                        if(element_val!='')
                        {
                            payelemnts_push.push({element_id:element_id,element_val:element_val});
                        }

                    }

                    for(t=0;t<=earnings_headings;t++)
                    {
                        var element_val=$('#id_ear_'+alt_id+'_'+t).val();
                        var element_id=$('#id_ear_'+alt_id+'_'+t).attr('alt');
                        if(element_val!='')
                        {
                            payelemnts_push.push({element_id:element_id,element_val:element_val});
                        }

                    }

                    //console.log(payelemnts_push);


                    totoal_salary_sum.push(emp_tot_slaary);

                    process_salary.push({commison:commison,net_commison:net_commison,absent_hrs:absent_hrs,absent_ded_amount:absent_ded_amount,
                        present_days:present_days,absent_days:absent_days,absent_ded:absent_ded,tot_salary_ded:tot_salary_ded,tot_salary:tot_salary,
                        tot_salary_payble:tot_salary_payble,trans_mode:trans_mode,memo:memo,alt_incr:alt_id,payslip_pk_id:payslip_id,payelemnts_push:payelemnts_push,alt_emp_id:alt_emp_id,loan_amount:loan_amount
                        ,adv_amount:adv_amount,emp_tot_slaary:totoal_salary_sum,pf_amount:pf_amount,overtime_hours:overtime_hours,overtime_amount:overtime_amount,month:month,year:year
                    });
                }
                payelemnts_push=[];
            });

            //console.log(process_salary);

           /* if(process_salary.length=='0')
            {
                toastr.error('SORRY!! There is no values mentioned to process payout');
                flag='fail';
                $('#loader').css('display','none');
            }*/
            /*----------------------------------END----------------------------------*/
            /*if($('.ddl_salaryFrmAccount').val()=='0')
            {
                toastr.error('SORRY!!! Please select from account for salary processing.');
                flag='fail';
                $('#loader').css('display','none');
            }*/


            //console.log(process_salary);
            if(flag=='')
            {
                AxisPro.APICall('POST',
                    ERP_FUNCTION_API_END_POINT + "?method=process_payroll",{'salaries':process_salary,'from_account':$('.ddl_salaryFrmAccount').val(),'payroll_id':$('.ap_payrolls').val()},
                    function (data) {
                        if(data.status=='OK')
                        {
                            toastr.success('Salary payout processed successfully');
                            SubmitPayout();
                            $('.clsExtraBtns').css('display','block');
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





    $(".ddl_employee").change(function()
    {
        AxisPro.APICall('POST',
            ERP_FUNCTION_API_END_POINT + "?method=get_gl_postings",
            {'empl_id':$(".ddl_employee").val(),'payroll_detail_id':$('.ap_payslips').val()},
            function (data) {

            });
    });

    $(".btn-reset").click(function()
    {
        /*var reset_ids=[];
        $.each($("input[name='chkEmployee']:checked"), function(){
            var payroll_id=$(this).attr('alt_id');
            var empl_id=$(this).attr('alt_empl_id');
            reset_ids.push({payroll_id:payroll_id,empl_id:empl_id});
        });*/
        var payroll_id=$('.ap_payrolls ').val();
        //var empl_id=$(this).attr('alt_empl_id');


        AxisPro.APICall('POST',
            ERP_FUNCTION_API_END_POINT + "?method=reset_payroll",
            {'payroll_id':payroll_id,'empl_id':0},
            function (data) {

                if(data.msg!='')
                {
                    SubmitPayout();
                }
                else
                {
                    toastr.error('Unable to reset payrolls , because GL entreies passed against it.')
                }
            });
    });



</script>
