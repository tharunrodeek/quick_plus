<?php
include "header.php";

include_once($path_to_root . "/API/API_HRM_Call.php");
$object=new API_HRM_Call();
$get_year_dropdown=$object->get_year_dropdown();
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
                                    <?= trans('GENERATE PAYROLL') ?>

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
                                                    name="year" >
                                                <option value="">SELECT</option>
                                                 <?php echo $get_year_dropdown;  ?>
                                            </select>
                                            <span class="error_note form-text text-muted kt-hidden">Please enter your full name</span>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class=""><?= trans('MONTH') ?>:</label>
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


                                    </div>

                        </form>

                        <!--end::Form-->
                    </div>


                </div>



                <div class="row" style="width: 100%;">

<div id="Notification" style="background-color: antiquewhite;
    color: red;
    height: 40px;
    width: 100%;
    font-size: 14pt;
    text-align: center;
    padding: 6px;display: none;"></div>
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('LIST OF EMPLOYEES') ?>
                                </h3>
                            </div>

                            <div class="col-lg-3">
                                <label style="height: 13px;"></label>
                                <div class="input-group">
                                    <button type="button" onclick="generatePayslip();" style="display:none;" class="btn btn-primary btnResetSlips">
                                        <?= trans('Generate Payroll') ?>
                                    </button>&nbsp;&nbsp;
                                    <button type="button" onclick="ResetPaySlip();" style="display:none;" class="btn btn-primary btnResetSlips">
                                        <?= trans('Reset Payroll') ?>
                                    </button>

                                    <input type="hidden" id="hdnCurrentDate" name="hdnCurrentDate"  value="<?php echo date('d-m-Y') ?>"/>
                                    <img src="<?= $base_url ?>assets/media/img/ajax-loader.gif" id="loader" style="height: 16px;
    margin-top: 4%;display: none;"/>
                                    <input type="hidden" id="hdnAction" name="hdnAction"/>
                                </div>
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
                                    <th><?= trans('PayRoll Status') ?></th>
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

    function generatePayslip()
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
                 ERP_FUNCTION_API_END_POINT + "?method=create_pay_roll",
                 'dept_id='+$(".ap_department").val()+'&year='+year+'&month='+month+'&emp_id='+emp_id+'&check_empids='+checked_empids,
                 function (data) {
                     //alert(data.status);
                     $("#loader").css('display','none');
                     if(data.status=='Exists')
                     {
                         toastr.error(data.msg);
                     }

                     if(data.status=='FAIL')
                     {
                        $('#Notification').css('display','block');
                        $('#Notification').html(data.msg);
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

                   /* var myWindow = window.open("", "MsgWindow", "width=200, height=500");
                    myWindow.document.write(data.msg);*/
                }
            }
        );
    });

    function ResetPaySlip()
    {
        var emp_id=$(".ap_employees").val();
var msg='';
        var checked_empids=[];


            jQuery(".chkEmp_select:checked").each(function(){
                checked_empids.push($(this).val());
            });

        if(checked_empids.length!='0')
        {
            msg='Are you really want to delete the payroll for selected employees?'
        }
        else
        {
            toastr.error('Select Employee for  reset payroll generation');
            return false;
        }


        var confrm=confirm(msg);
        if(confrm==true)
        {
            AxisPro.APICall('POST',
                ERP_FUNCTION_API_END_POINT + "?method=reset_payslip",'empl_id='+checked_empids+'&dept_id='+$(".ap_department ").val()
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
