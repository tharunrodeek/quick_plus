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
                                    <?= trans('GENERATE PAYSLIP') ?>

                                </h3>
                            </div>
                        </div>

                        <form class="kt-form kt-form--label-right" id="leave_form">
                            <div class="kt-portlet__body" style="padding: 20px !important;">
                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label><?= trans('YEAR') ?>:
                                        </label>
                                        <select class="form-control kt-select2 ap-year"
                                                name="leave_type" >
                                            <option value="">SELECT</option>
                                            <option value="2018">2018</option>
                                            <option value="2019" selected>2019</option>
                                            <option value="2020">2020</option>
                                            <option value="2021">2021</option>
                                            <option value="2022">2022</option>
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
                                        <label><?= trans('EMPLOYEE') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_employees"
                                                    name="leave_type" >
                                                <option value="">All Employees</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <label style="height: 13px;"></label>
                                        <div class="input-group">
                                            <button type="button" onclick="SubmitLeave();" class="btn btn-primary">
                                                <?= trans('Generate PaySlip') ?>
                                            </button>&nbsp;&nbsp;
                                            <button type="button" onclick="ResetPaySlip();" style="display:none;" class="btn btn-primary btnResetSlips">
                                                <?= trans('Reset Payslips') ?>
                                            </button>

                                            <input type="hidden" id="hdnCurrentDate" name="hdnCurrentDate"  value="<?php echo date('d-m-Y') ?>"/>
                                            <img src="<?= $base_url ?>assets/media/img/ajax-loader.gif" id="loader" style="height: 16px;
    margin-top: 4%;display: none;"/>
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
                                    <?= trans('LIST OF EMPLOYEES') ?>
                                </h3>
                            </div>

                        </div>

                        <!--begin::Table-->


                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <table class="table table-bordered" id="list_employees">
                                <thead>
                                    <th></th>
                                    <th><?= trans('Emp-Code') ?></th>
                                    <th><?= trans('Emp.Name') ?></th>
                                    <th><?= trans('Contact No.') ?></th>
                                    <th><?= trans('Email') ?></th>
                                    <th><?= trans('PaySlip Status') ?></th>
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
<iframe
         id="iframe1"
        frameborder="0" style="display:none;">
</iframe>
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

        fetchDataTotable();
        if($(this).val()!='0')
        {
           $(".btnResetSlips").css('display','block');
        }
    });

    $(".ap_employees").change(function()
    {
        fetchDataTotable();
    });

    function fetchDataTotable()
    {
        $('#list_employees').dataTable().fnDestroy();
        $('#list_employees').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :ERP_FUNCTION_API_END_POINT + "?method=list_employees_in_dept", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                data:{'dept_id':$(".ap_department").val(),'emp_id':$(".ap_employees").val(),'year':$(".ap-year").val(),'month':$(".ClsMonths ").val()},
                error: function(){
                }
            }
        });
    }

    function SubmitLeave()
    {
        var year=$(".ap-year").val();
        var month=$(".ClsMonths ").val();
        var dept_id=$(".ap_department").val();
        var emp_id=$(".ap_employees").val();
        var flag=true;
        var checked_empids=[];

        if(emp_id=='0')
        {
            jQuery(".chkEmp_select:checked").each(function(){
                checked_empids.push($(this).val());
            });
        }



        if(year=='')
        {
            toastr.error('ERROR!! Please select year');
            flag=false;
        }

        if(month=='')
        {
            toastr.error('ERROR!! Please select Month');
            flag=false;
        }
        if(dept_id=='0')
        {
            toastr.error('ERROR!! Please select department');
            flag=false;
        }

         if(flag==true)
         {
             $("#loader").css('display','block');
             AxisPro.APICall('POST',
                 ERP_FUNCTION_API_END_POINT + "?method=create_pay_slip",
                 'dept_id='+$(".ap_department").val()+'&year='+year+'&month='+month+'&emp_id='+emp_id+'&check_empids='+checked_empids,
                 function (data) {
                     $("#loader").css('display','none');
                     if(data.status=='Exists')
                     {
                         toastr.error(data.msg);
                     }
                     fetchDataTotable();
                 }
             );
         }
    }

    $('#list_employees tbody').on('click', 'td label.ClsViewSlip', function (e){
        var alt_empl_id=$(this).attr('alt');
        var objFra = document.getElementById('iframe1');
        $("#iframe1").contents().find("body").html('');
        AxisPro.APICall('POST',
            ERP_FUNCTION_API_END_POINT + "?method=view_payslip",'alt_empl_id='+alt_empl_id+'&month='+$(".ClsMonths ").val()+'&year='+$(".ap-year").val(),
            function (data) {
                if (data.status === 'OK') {
                    objFra.contentWindow.document.write(data.msg);
                    objFra.contentWindow.focus();
                    objFra.contentWindow.print();
                }
            }
        );
    });

    function ResetPaySlip()
    {
        var emp_id=$(".ap_employees").val();
        if(emp_id=='0')
        {
            $msg='Are you really want to delete payslip for all employees?'
        }
        else
        {
            $msg='You really want to delete the payslip for the employee :'+ $(".ap_employees option:selected").text();
        }

        var confrm=confirm($msg);
        if(confrm==true)
        {
            AxisPro.APICall('POST',
                ERP_FUNCTION_API_END_POINT + "?method=reset_payslip",'empl_id='+emp_id+'&dept_id='+$(".ap_department ").val()
                +'&month='+$(".ClsMonths").val()+'&year='+$(".ap-year").val(),
                function (data) {
                    if (data.status === 'OK') {
                       toastr.success('Payslip sucessfully deleted');
                        fetchDataTotable();
                    }
                    else
                    {
                        toastr.error("There are some issue occur while deleting payslip");
                    }
                }
            );
        }
    }
</script>
