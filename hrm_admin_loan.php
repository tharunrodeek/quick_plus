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
                                    <?= trans('LOAN ENTRY') ?>

                                </h3>
                            </div>
                        </div>

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
                                                <option value="">Select Employees</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label><?= trans('LOAN TYPE') ?>:</label>
                                        <div class="input-group">
                                            <?php
                                            include_once($path_to_root . "/API/API_HRM_Call.php");
                                            $object=new API_HRM_Call();
                                            $loan_types=$object->get_loan_types();

                                            ?>

                                            <select class="form-control kt-select2 ap-select2"
                                                    name="loan_type" id="loan_type" >
                                                <option value="">Select loan type</option>
                                                <?php while($types=db_fetch($loan_types)){ ?>
                                                    <option value="<?php echo $types['id']; ?>"><?php echo $types['loan_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <label><?= trans('LOAN AMOUNT') ?>:</label>
                                        <div class="input-group">
                                           <input type="text" id="txt_loan_amount" name="txt_loan_amount" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <label><?= trans('No.of Installment(Months)') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 installment"
                                                    name="leave_type" >
                                                <option value="">Select Installment</option>
                                               <?php for($i=1;$i<=24;$i++){ ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                               <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <label><?= trans('LOAN EFFECT FROM') ?>:</label>
                                        <div class="input-group">
                                            <input type="text"  id="txt_loaneffect" name="txt_loaneffect" class="form-control ap-datepicker"
                                                   placeholder=""/>
                                        </div>
                                    </div>

                                     <div class="col-sm-3">
                                            <label class=""><?= trans('LOAN FROM ACCOUNT') ?>:</label>
                                            <select class="form-control kt-select2 ap-select2 ClsLoanAccount"
                                                    name="ClsLoanAccount" >
                                                <option value="">SELECT</option>
                                            </select>
                                     </div>

                                    <div class="col-lg-2">
                                        <label><?= trans('Installment Amount') ?>:</label>
                                        <div class="input-group">
                                            <input type="text"  id="txt_install_amount" name="txt_install_amount" class="form-control"
                                                   placeholder=""/>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <label><?= trans('COMMENT') ?>:</label>
                                        <div class="input-group">
                                            <textarea id="txtComment" name="txtComment"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">

                                        <div class="input-group">
                                            <button type="button" onclick="Submit();" class="btn btn-primary" style="margin-top: 8%;">
                                                <?= trans('Submit') ?>
                                            </button>&nbsp;&nbsp;
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label id="lblShowMonthlyPay" style="color: red;font-size: 13pt;margin-top: 6%;margin-left: -38%;
                                        font-weight: bold;"></label>

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
                                    <?= trans('LIST OF EMPLOYEES') ?>
                                </h3>
                            </div>



                        </div>

                        <!--begin::Table-->


                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <table class="table table-bordered" id="list_employees">
                                <thead>

                                    <th><?= trans('Emp-Code') ?></th>
                                    <th><?= trans('Emp.Name') ?></th>
                                    <th><?= trans('Loan Start Date.') ?></th>
                                    <th><?= trans('Loan Amount') ?></th>
                                    <th><?= trans('Tenure') ?></th>
                                    <th><?= trans('Periods Paids') ?></th>
                                    <th><?= trans('Monthly Installment') ?></th>
                                    <th><?= trans('View GL') ?></th>
                                    <th></th>
                                </thead>
                                <tbody id="list_employees_tbody">
                                </tbody>
                            </table>

                        </div>


                        <!--end::Form-->
                    </div>


                </div>

            </div>

            <!-- end:: Content -->
        </div>
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td>Department:</td><td> <select class="form-control kt-select2 ap-select2 edit_ap_department"
                                                         name="ddl_edit_dept" id="ddl_edit_dept">
                                <option value="">SELECT</option>
                            </select></td>
                    </tr>
                    <tr>
                        <td>Employee :</td>
                        <td>
                            <select class="form-control kt-select2 ap-select2 edit_ap_employees"
                                    name="ddl_edit_employee" id="ddl_edit_employee">
                                <option value="">Select Employees</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>LOAN TYPE :</td>
                        <td>
                            <select class="form-control kt-select2"
                                    name="edit_loan_type" id="edit_loan_type" >
                                <option value="">Select loan type</option>
                                <?php
                                include_once($path_to_root . "/API/API_HRM_Call.php");
                                $object=new API_HRM_Call();
                                $loan_types=$object->get_loan_types();

                                ?>
                                <?php while($types=db_fetch($loan_types)){ ?>
                                    <option value="<?php echo $types['id']; ?>"><?php echo $types['loan_name']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>LOAN AMOUNT :</td>
                        <td>
                            <input type="text" id="txt_edit_loan_amount" name="txt_edit_loan_amount" class="form-control"/>
                        </td>
                    </tr>



                    <tr>
                        <td>No.of Installemnt :</td>
                        <td>
                            <select class="form-control kt-select2"
                                    name="ddl_edit_inastallemnt" id="ddl_edit_inastallemnt" >
                                <option value="">Select Installment</option>
                                <?php for($i=1;$i<=24;$i++){ ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>LOAN START DATE :</td>
                        <td>
                            <input type="text"  id="edit_loaneffect" name="edit_loaneffect" class="form-control ap-datepicker"
                                   placeholder=""/>
                        </td>
                    </tr>
                    <tr>
                        <td>LOAN FROM ACCOUNT :</td>
                        <td>
                            <select class="form-control kt-select2 ap-select2 ClsLoanAccount"
                                   id="ddl_edit_loan_fromacc" name="ddl_edit_loan_fromacc" >
                                <option value="">SELECT</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Installment Amount :</td>
                        <td>
                            <input type="text"  id="txt_edit_install_amount" name="txt_edit_install_amount" class="form-control"
                                   placeholder=""/>
                        </td>
                    </tr>
                    <tr>
                        <td>Comment :</td>
                        <td>
                            <textarea id="edit_txtComment" name="edit_txtComment"></textarea>
                        </td>
                    </tr>

                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default ClsSaveLoanEntry" data-dismiss="modal">Save Loan Data</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="hidden" id="hdnPayroll_id" />
                <input type="hidden" id="hdn_emp_id" />
                <input type="hidden" id="hdn_salary" />
                <input type="hidden" id="hdn_id" />
            </div>
        </div>

    </div>
</div>
<iframe
         id="iframe1"
        frameborder="0" style="display:none;">
</iframe>
<?php include "footer.php"; ?>

<script>
    fetchDataTotable();
    $(document).ready(function () {

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_department',null,function () {
                $('.ap_department').prepend('<option value="0">Select Department</option>');
                $('.ap_department').val('0');
            });
        });

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'edit_ap_department',null,function () {
                $('.edit_ap_department').prepend('<option value="0">Select Department</option>');
                $('.edit_ap_department').val('0');
            });
        });

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_accounts', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'account_code', 'accname', 'ClsLoanAccount', null, function () {
                $('.ClsOvertime').prepend('<option value="0">Choose Account</option>');
                $('.ClsOvertime').val($('#hdnOvertimeAcc').val());
            });
        });

    });




  $(".ap_department").change(function() {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+$(".ap_department").val(), function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'Emp_name', 'ap_employees',null,function () {
                $('.ap_employees').prepend('<option value="0">Select Employees</option>');
                $('.ap_employees').val('0');
            });
        });
    });

    $(".edit_ap_department ").change(function() {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+$(".edit_ap_department").val(), function (data) {
            AxisPro.PrepareSelectOptions(data, 'Empid', 'Emp_name', 'edit_ap_employees',null,function () {
                $('.edit_ap_employees').prepend('<option value="0">Select Employees</option>');
                $('.edit_ap_employees').val('0');
            });
        });
    });

    $(".installment").change(function()
    {
         var loan_amount=$("#txt_loan_amount").val();
         var install_count=$(".installment ").val();

         if(loan_amount!='')
         {
             var monthlypay=loan_amount/install_count;
              $('#txt_install_amount').val(monthlypay.toFixed(2));
         }
         else
         {
             alert("Loan amount can't be empty");
             (".installment ").val('');
         }
    });




    function fetchDataTotable()
    {
        $('#list_employees').dataTable().fnDestroy();
        $('#list_employees').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :ERP_FUNCTION_API_END_POINT + "?method=list_loan_entries", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                data:{'dept_id':$(".ap_department").val(),'emp_id':$(".ap_employees").val(),'year':$(".ap-year").val(),'month':$(".ClsMonths ").val()},
                error: function(){
                }
            }
        });
    }

    function Submit()
    {
        var dept=$(".ap_department ").val();
        var Empl_id=$(".ap_employees ").val();
        var loan_amount=$("#txt_loan_amount").val();
        var install_count=$(".installment ").val();
        var effect_date=$("#txt_loaneffect ").val();
        var LoanAccount=$('.ClsLoanAccount ').val();
        var flag=true;


        if(dept=='')
        {
            toastr.error('ERROR!! Please select department');
            flag=false;
        }

        if(Empl_id=='')
        {
            toastr.error('ERROR!! Please select employee');
            flag=false;
        }
        if(loan_amount=='' || loan_amount=='0')
        {
            toastr.error('ERROR!! Please enter loan amount');
            flag=false;
        }

        if(install_count=='')
        {
            toastr.error('ERROR!! Please select no.of installments');
            flag=false;
        }

        if(effect_date=='')
        {
            toastr.error('ERROR!! Please choose loan effect date');
            flag=false;
        }

        if(effect_date=='')
        {
            toastr.error('ERROR!! Please choose loan effect date');
            flag=false;
        }

        if(LoanAccount=='')
        {
            toastr.error('ERROR!! Please choose loan effect date');
            flag=false;
        }

        if($('#loan_type_id').val()=='')
        {
            toastr.error('ERROR!! Please select loan type');
            flag=false;
        }

         if(flag==true)
         {
             $("#loader").css('display','block');
             AxisPro.APICall('POST',
                 ERP_FUNCTION_API_END_POINT + "?method=create_loan_entry", /****************Function located in API_HRM_CAll.php******/
                 'dept_id='+dept+'&Empl_id='+Empl_id+'&loan_amount='+loan_amount+'&install_count='+install_count
                 +'&memo='+$("#txtComment").val()+'&effect_date='+effect_date+'&LoanAccount='+LoanAccount+'&loan_type_id='+$('#loan_type').val(),
                 function (data) {
                     $("#loader").css('display','none');
                     if(data.status=='success')
                     {
                         toastr.success(data.msg);
                     }
                     else
                     {
                         toastr.error(data.msg);
                     }

                         fetchDataTotable();
                 }
             );
         }
    }

    $('#list_employees tbody').on('click', 'td label.ClsBtnEdit', function (e){

        var dept_id=$(this).attr('alt_dept_id');
        var empl_id=$(this).attr('alt_emp_id');
        var loan_amount=$(this).attr('alt_loan_amount');
        var insta_cnt=$(this).attr('alt_install_cont');
        var loan_start_date=$(this).attr('alt_start_date');
        var from_acc=$(this).attr('alt_from_acc');
        var install_amount=$(this).attr('alt_installment_amount');
        var loan_type_id=$(this).attr('alt_loan_type_id');
        var periods_paid=$(this).attr('alt_paid_peroid');

        if(periods_paid > 0)
        {
            toastr.error('Cannot edit or remove loan entry.Because there is already paryoll processed');
            return false;
        }

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'edit_ap_department',null,function () {
                $('.edit_ap_department').val(dept_id);
            });


        });

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+dept_id, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'Emp_name', 'edit_ap_employees',null,function () {
                $('.edit_ap_employees').val(empl_id);
            });
        });

        $('#txt_edit_loan_amount').val(loan_amount);
        $('#ddl_edit_inastallemnt').val(insta_cnt);
        $('#ddl_edit_loan_fromacc').val(from_acc);
        $('#txt_edit_install_amount').val(install_amount);
       // $('#edit_txtComment').val(loan_amount);
        $('#edit_loaneffect').val(loan_start_date);
        $('#edit_loan_type').val(loan_type_id);
        $('#hdn_id').val($(this).attr('alt_id'));

    });


    $('.ClsSaveLoanEntry').click(function()
    {
       var edit_dept_id=$('#ddl_edit_dept ').val();
       var edit_emp_id=$('#ddl_edit_employee').val();
       var loan_type=$('#edit_loan_type').val();
       var loan_amnt=$('#txt_edit_loan_amount').val();
       var edit_install=$('#ddl_edit_inastallemnt').val();
       var loan_start_d=$('#edit_loaneffect').val();
       var loan_from=$('#ddl_edit_loan_fromacc').val();
       var insta_amount=$('#txt_edit_install_amount').val();
       var flag='';

       if(edit_dept_id=='')
       {
           toastr.error('ERROR!! Select department');
           flag=false;
       }

       if(edit_emp_id=='')
       {
           toastr.error('ERROR!! Select employee');
           flag=false;
       }

        if(loan_amnt=='')
        {
            toastr.error('ERROR!! Enter loan amount');
            flag=false;
        }

        if(loan_type=='')
        {
            toastr.error('ERROR!! Select loan type');
            flag=false;
        }

        if(edit_install=='')
        {
            toastr.error('ERROR!! Select installment count');
            flag=false;
        }

        if(loan_start_d=='')
        {
            toastr.error('ERROR!! Select loan start date');
            flag=false;
        }

        if(loan_from=='')
        {
            toastr.error('ERROR!! Select loan give account');
            flag=false;
        }

        if(insta_amount=='')
        {
            toastr.error('ERROR!! Select installment amount');
            flag=false;
        }

        if(flag=='')
        {
            AxisPro.APICall('POST',
                ERP_FUNCTION_API_END_POINT + "?method=update_loan_entry", /****************Function located in API_HRM_CAll.php******/
                'dept_id='+edit_dept_id+'&Empl_id='+edit_emp_id+'&loan_amount='+loan_amnt+'&install_count='+edit_install
                +'&memo='+$("#edit_txtComment").val()+'&effect_date='+loan_start_d+'&LoanAccount='+loan_from
                +'&loan_type_id='+loan_type+'&install_amount='+insta_amount+'&hdn_id='+$('#hdn_id').val(),
                function (data) {
                    $("#loader").css('display','none');
                    if(data.status=='success')
                    {
                        toastr.success(data.msg);
                        fetchDataTotable();
                    }
                    else
                    {
                        toastr.error(data.msg);
                    }


                }
            );
        }

    });


    $('#txt_edit_loan_amount').change(function()
    {
        var monthlypay=$('#txt_edit_loan_amount').val()/$('#ddl_edit_inastallemnt').val();
        $('#txt_edit_install_amount').val(monthlypay.toFixed(2));
    });

    $('#ddl_edit_inastallemnt').change(function()
    {
        var monthlypay=$('#txt_edit_loan_amount').val()/$('#ddl_edit_inastallemnt').val();
        $('#txt_edit_install_amount').val(monthlypay.toFixed(2));
    });


</script>
