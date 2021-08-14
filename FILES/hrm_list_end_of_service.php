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
                                    <?= trans('LIST END OF SERVICES') ?>

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
                                                <option value="">All Employees</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-lg-3">

                                        <div class="input-group">
                                            <button type="button"  class="btn btn-primary btnView" style="margin-top: 8%;">
                                                <?= trans('View') ?>
                                            </button>&nbsp;&nbsp;
                                        </div>
                                    </div>


                                </div>

                        </form>

                        <!--end::Form-->
                    </div>


                </div>



                <div class="row" style="width: 100%;">

                    <div class="kt-portlet">

                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <table class="table table-bordered" id="list_employees">
                                <thead>
                                    <th><?= trans('Emp-Code') ?></th>
                                    <th><?= trans('Emp.Name') ?></th>
                                    <th><?= trans('ESB Processed Date') ?></th>
                                    <th><?= trans('Loan Amount') ?></th>
                                    <th><?= trans('Warning Deduction') ?></th>
                                    <th style="font-weight: bold;"><?= trans('ESB Amount') ?></th>
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


<?php include "footer.php"; ?>

<script>
    //fetchDataTotable();
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



    });




  $(".ap_department").change(function() {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+$(".ap_department").val(), function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'Emp_name', 'ap_employees',null,function () {
                $('.ap_employees').prepend('<option value="0">All Employees</option>');
                $('.ap_employees').val('0');
            });
        });
    });



    $('.btnView').click(function()
    {
        if($('.ap_department ').val()=='0')
        {
            toastr.error('Please select department');
        }
        else
        {

            fetchDataTotable();
        }

    });



    function fetchDataTotable()
    {
        $('#list_employees').dataTable().fnDestroy();
        $('#list_employees').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :ERP_FUNCTION_API_END_POINT + "?method=list_esb_entries", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                data:{'emp_id':$('.ap_employees ').val(),'dept_id':$('.ap_department').val()},
                error: function(){
                }
            }
        });
    }

    $('#list_employees tbody').on('click', 'td label.ClsBtnDelete', function (e)
    {
       var remove_id=$(this).attr('alt_id');

       var confrm=confirm('Are you really want to delete?');
       if(confrm==true)
       {
           AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=remove_esb",
               'remove_id='+remove_id , function (data)
               {
                   if(data.status=='OK')
                   {
                       toastr.success(data.msg);
                       fetchDataTotable();
                   }

                   if(data.status=='ERROR')
                   {
                       toastr.error(data.msg);
                   }


               });
       }
    });


</script>
