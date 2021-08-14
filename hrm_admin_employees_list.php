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
                                    <?= trans('EMPLOYEES LIST') ?>

                                </h3>
                            </div>
                        </div>

                        <form class="kt-form kt-form--label-right" id="leave_form" action="<?= $erp_url ?>API/hub.php?method=export_employees_list" method="post">
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
                                        <label><?= trans('DESIGNATION') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_designations"
                                                    name="ddl_designation" >
                                                <option value="">Select Designations</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label><?= trans('EMPLOYEE') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_employees"
                                                    name="employee" >
                                                <option value="">----All----</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <label><?= trans('COST CENTER') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_cost_center"
                                                    name="cost_center" >
                                                <option value="">----All----</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">

                                        <div class="input-group">
                                            <button type="button" onclick="Submit();" class="btn btn-primary" style="margin-top: 8%;">
                                                <?= trans('Submit') ?>
                                            </button>&nbsp;&nbsp;
                                  
                                        
                                        <input type="submit" name="btnSubmit" value="Export Employee List" class="btn btn-primary" style="height: 39px;margin-top: 8%;"/>
                                 
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


                    <div class="kt-portlet" style="overflow: auto;">
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
                                    <th><?= trans('Dept. Name') ?></th>
                                    <th><?= trans('Designation') ?></th>
                                    <th><?= trans('Joining Date') ?></th>
                                    <th><?= trans('Line Manager') ?></th>
                                    <th><?= trans('Head Of Dept.') ?></th>
                                    <th><?= trans('Country') ?></th>
                                    <th><?= trans('City') ?></th>
                                    <th><?= trans('State') ?></th>
                                    <th><?= trans('DOB') ?></th>
                                    <th><?= trans('Marital Status') ?></th>
                                    <th><?= trans('Gender') ?></th>
                                    <th><?= trans('Mobile') ?></th>
                                    <th><?= trans('Email') ?></th>
                                    <th><?= trans('Cost Center') ?></th>
                                  <!--  <th><?/*= trans('Total Salary') */?></th>-->
                                    <th><?= trans('UserID') ?></th>
                                     
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
    fetchDataTotable();
    $(document).ready(function () {

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_department',null,function () {
                $('.ap_department').prepend('<option value="0">Select Department</option>');
                $('.ap_department').val('0');
            });
        });

       


        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_cost_centers', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'name', 'ap_cost_center ',null,function () {
                $('.ap_cost_center ').prepend('<option value="0">----All----</option>');
                $('.ap_cost_center ').val('0');
            });
        });


        
    });




  $(".ap_department").change(function() {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+$(".ap_department").val(), function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'Emp_name', 'ap_employees',null,function () {
                $('.ap_employees').prepend('<option value="0">----All----</option>');
                $('.ap_employees').val('0');
            });
        });


         AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_designations",'dept_id='+$(".ap_department").val(), function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_designations',null,function () {
                $('.ap_designations').prepend('<option value="0">----All----</option>');
                $('.ap_designations').val('0');
            });
        });

    });

   

  



    function fetchDataTotable()
    {
        $('#list_employees').dataTable().fnDestroy();
        $('#list_employees').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :ERP_FUNCTION_API_END_POINT + "?method=list_all_employees", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                data:{'dept_id':$(".ap_department").val(),'emp_id':$(".ap_employees").val()
                ,'desig':$(".ap_designations ").val(),'cost_center':$(".ap_cost_center").val()},
                error: function(){
                }
            }
        });
    }

    function Submit()
    {
        var dept=$(".ap_department").val();
         
        var flag=true;


        if(dept=='0')
        {
            toastr.error('ERROR!! Please select department');
            flag=false;
        }

        if(flag==true)
        {
            fetchDataTotable();
        }
    }

   

</script>
