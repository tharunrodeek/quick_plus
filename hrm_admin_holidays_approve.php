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
                                    <?= trans('HOLIDAY WORK APPROVE') ?>

                                </h3>
                            </div>
                        </div>

                        <form class="kt-form kt-form--label-right" id="leave_form" action="<?= $erp_url ?>API/hub.php?method=download_overtime" method="post">
                            <div class="kt-portlet__body" style="padding: 20px !important;">
                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label><?= trans('YEAR') ?>:
                                        </label>
                                        <select class="form-control kt-select2 ap-year"
                                                name="ap-year">
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
                                        <label><?= trans('EMPLOYEE') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_employees"
                                                    name="ap_employees" >
                                                <option value="">Select Employees</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label style="height: 13px;"></label>
                                        <div class="input-group">
                                            <button type="button" onclick="Submitbtn();" class="btn btn-primary">
                                                <?= trans('Submit') ?>
                                            </button>&nbsp;&nbsp;
                                            <button type="button" onclick="ResetPaySlip();"   class="btn btn-primary btnResetSlips">
                                                <?= trans('Reset') ?>
                                            </button>

                                            <input type="hidden" id="hdnCurrentDate" name="hdnCurrentDate"  value="<?php echo date('d-m-Y') ?>"/>
                                            <img src="<?= $base_url ?>assets/media/img/ajax-loader.gif" id="loader" style="height: 16px;margin-top: 4%;display: none;"/>
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
                            <div class="btn_alignment" style="margin: 10px;display: none;">
                               <button type="button" onclick="Approve();" style="height: 40px;position: absolute;
                                                   right: 221px;    background-color: green;"
                                        class="btn btn-primary btnResetSlips">
                                    <?= trans('Approve Holiday Pay') ?>
                                </button>

                                 <button type="button" onclick="Disapprove();" style="height: 40px;position: absolute;
                                                   right: 2%;background-color: green;"
                                        class="btn btn-primary btnResetSlips">
                                    <?= trans('Remove From Approval') ?>
                                </button>
                            </div>
                        </div>

                        <!--begin::Table-->


                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <table class="table table-bordered" id="list_holiday_works">
                                <thead>
                                    <th></th>
                                    <th><?= trans('Emp.Name') ?></th>
                                    <th><?= trans('Holiday Name') ?></th>
                                    <th><?= trans('Date') ?></th>
                                    <th></th>
                                    <th>Status</th>
                                </thead>
                                <tbody id="list_holiday_works_tbody">
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
                $('.ap_employees').prepend('<option value="0">Select Employees</option>');
                $('.ap_employees').val('0');
            });
        });
    });

    function Submitbtn()
    {
        if($('.ap-year').val()=='')
        {
            toastr.error('Select year');
        }
        else if($('.ClsMonths').val()=='')
        {
            toastr.error('Select month');
        }
        else if($('.ap_department').val()=='0')
        {
            toastr.error('Select department');
        }
        else if($('.ap_employees').val()=='0')
        {
            toastr.error('Select employee');
        }
        else
        {
            fetchDataTotable();
        }

    }

    function fetchDataTotable()
    {
        $('.btn_alignment').css('display','block');
        $('#list_holiday_works').dataTable().fnDestroy();
        $('#list_holiday_works').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :ERP_FUNCTION_API_END_POINT + "?method=list_empl_holidaywrked", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                data:{'dept_id':$(".ap_department").val(),'emp_id':$(".ap_employees").val(),'year':$(".ap-year").val()
                      ,'month':$(".ClsMonths ").val()},
                error: function(){
                }
            }
        });
    }

    function Approve() {
        var empids = [];
        var date = [];
        var extrahour = [];
        var ids = [];
        var pay_option =[];
        jQuery(".chk_select:checked").each(function () {
            var index_id = $(this).attr('alt_index');
            var pk_id = $(this).attr('alt_holi_pk_id');
            var holiday_date = $(this).attr('alt_date');
            var empl_id = $(this).attr('alt_emp_id');
            
            empids.push(empl_id);
            date.push(holiday_date);
            ids.push(pk_id);
            pay_option.push($('#ddl_payOption_'+index_id).val());
        }); 
        var flag = '';
        if (empids.length == '0') {
            toastr.error('ERROR!!! Please select atlast one entry for approval');
            flag = '1';
        }

        if (flag == '')
        {
            ApproveOrDisapprove(empids,date,ids,pay_option,'1','');
        }
    }

    function Disapprove()
    {
        var remove_ids = [];
        jQuery(".chk_select:checked").each(function () {
            var assign_id = $(this).attr('alt_un_assign_id');
            remove_ids.push(assign_id);
        });
        ApproveOrDisapprove('','','','','2',remove_ids);

    }


    function ApproveOrDisapprove(empids,date,ids,pay_option,approve_or_disapprove,remove_id)
    {

        AxisPro.APICall('POST',
            ERP_FUNCTION_API_END_POINT + "?method=save_apporved_holidays_for_employee",{'empids':empids,'date':date
                ,'flag':approve_or_disapprove,'remove_id':remove_id,'pay_option':pay_option,'holiday_id':ids},
            function (data) {
                if(data.status=='OK')
                {
                    toastr.success(data.msg);
                    fetchDataTotable();
                }
                else if(data.status=='FAIL')
                {
                    toastr.error(data.msg);
                }

            }
        );
    }




</script>
