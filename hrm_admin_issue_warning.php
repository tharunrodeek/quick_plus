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
                                    <?= trans('WARNING LETTER') ?>
                                </h3>
                            </div>
                        </div>
                        <!--  <form class="kt-form kt-form--label-right" id="leave_form">-->
                        <div class="kt-portlet__body" style="padding: 5px !important;">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="card card-custom example example-compact">
                                        <!--begin::Form-->
                                        <form class="form fv-plugins-bootstrap fv-plugins-framework" id="kt_form_1" novalidate="novalidate">
                                            <div class="card-body">
                                                <div class="alert alert-custom alert-light-danger d-none" role="alert" id="kt_form_1_msg">
                                                    <div class="alert-icon"><i class="flaticon2-information"></i></div>
                                                    <div class="alert-text  font-weight-bold">
                                                        Oh snap! Change a few things up and try submitting again.
                                                    </div>
                                                    <div class="alert-close">
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                            <span><i class="ki ki-close "></i></span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-group row fv-plugins-icon-container">
                                                    <label class="col-form-label text-right col-lg-3 col-sm-12">Department *</label>
                                                    <div class="col-lg-9 col-md-9 col-sm-12">
                                                        <select class="form-control kt-select2 ap-select2 ap_department"
                                                                name="ddl_department" >
                                                            <option value="">SELECT</option>
                                                        </select>
                                                        <span class="form-text text-muted">Select Department</span>
                                                        <div class="fv-plugins-message-container"></div>
                                                    </div>
                                                </div>
                                                <div class="form-group row fv-plugins-icon-container">
                                                    <label class="col-form-label text-right col-lg-3 col-sm-12">Employee *</label>
                                                    <div class="col-lg-9 col-md-9 col-sm-12">
                                                        <div class="input-group">
                                                            <select class="form-control kt-select2 ap-select2 ap_employees"
                                                                    name="leave_type" >
                                                                <option value="">Select Employees</option>
                                                            </select>
                                                        </div>
                                                        <span class="form-text text-muted">Please enter your website URL.</span>
                                                        <div class="fv-plugins-message-container"></div>
                                                    </div>
                                                </div>
                                                <div class="form-group row fv-plugins-icon-container">
                                                    <label class="col-form-label text-right col-lg-3 col-sm-12">Deduction Amount</label>
                                                    <div class="col-lg-9 col-md-9 col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="flaticon2-browser"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control" name="ded_amount" id="ded_amount" placeholder="Enter digits">
                                                        </div>
                                                        <span class="form-text text-muted">Please enter only digits</span>
                                                        <div class="fv-plugins-message-container"></div>
                                                    </div>
                                                </div>
                                                <div class="form-group row fv-plugins-icon-container">
                                                    <label class="col-form-label text-right col-lg-3 col-sm-12">Description</label>
                                                    <div class="col-lg-9 col-md-9 col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="flaticon-price-tag"></i></span>
                                                            </div>
                                                            <textarea class="form-control" name="description" id="description" placeholder="Enter waring description" rows="3"></textarea>
                                                        </div>
                                                        <span class="form-text text-muted">Please enter your credit card number</span>
                                                        <div class="fv-plugins-message-container"></div>
                                                    </div>
                                                </div>
                                                <div class="form-group row fv-plugins-icon-container">
                                                    <label class="col-form-label text-right col-lg-3 col-sm-12">Deduction Start From</label>
                                                    <div class="col-lg-9 col-md-9 col-sm-12">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control ap-datepicker" name="startdate" id="startdate" placeholder="start date">
                                                        </div>
                                                        <span class="form-text text-muted">Warning Issue Date</span>
                                                        <div class="fv-plugins-message-container"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="row">
                                                    <div class="col-lg-9 ml-lg-auto">
                                                        <button type="submit" class="btn btn-primary font-weight-bold mr-2 submitButton" name="submitButton">Submit</button>
                                                        <button type="reset" class="btn btn-light-primary font-weight-bold">Cancel</button>
                                                        <img src="<?= $base_url ?>assets/media/img/ajax-loader.gif" id="" style="height: 16px;
                                            margin-top: 4%;display: none;"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div></div>
                                            <input type="hidden">
                                        </form>
                                        <!--end::Form-->
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="list_employees">
                                            <thead>

                                            <th><?= trans('Department') ?></th>
                                            <th><?= trans('Employee') ?></th>
                                            <th><?= trans('Deduction Amount') ?></th>
                                            <th><?= trans('Description') ?></th>
                                            <th><?= trans('Start Date') ?></th>
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
                <!-- end:: Content -->
            </div>
        </div>
    </div>
    <iframe
            id="iframe1"
            frameborder="0" style="display:none;">
    </iframe>
    <style>
        .highlighted{
            background-color:lightblue;
        }
        #list_employees tbody tr{
            cursor:pointer;
        }
    </style>
    <input type="hidden" id="hdnedit_id" name="hdnedit_id" value="0"/>
    <?php include "footer.php"; ?>
    <script>
        $(document).ready(function () {

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
                AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_department',null,function () {
                    $('.ap_department').prepend('<option value="0">Select Department</option>');
                    $('.ap_department').val('0');
                });
            });

            fetch_data();
        });

        $(".ap_department").change(function() {
            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+$(".ap_department").val(), function (data) {
                AxisPro.PrepareSelectOptions(data, 'id', 'Emp_name', 'ap_employees',null,function () {
                    $('.ap_employees').prepend('<option value="0">Select Employee</option>');
                    $('.ap_employees').val('0');
                });
            });
        });

        $('.ap-datepicker').datepicker({
            format: 'mm/dd/yyyy'
        });
        /**************************PAST Days disabled ,because can't assign shift for past days************/

        /**************************************************END*********************************************/

        $('.submitButton').click(function(e)
        {
            $("#loader").css('display','block');
            e.preventDefault();

            if($('.ap_department').val()=='')
            {
                toastr.error('Select Department');
            }
            else if($('.ap_employees ').val()=='')
            {
                toastr.error('Select Employee   ');
            }
            else if($('#ded_amount').val()=='0')
            {
                toastr.error('Enter Deduction Amount');
            }
            else if($('#description').val()=='0')
            {
                toastr.error('Enter Description');
            }
            else if($('#startdate').val()=='')
            {
                toastr.error('Choose start date');
            }
            else
            {
                issue_Warning();

            }
        });


        function issue_Warning()
        {

            AxisPro.APICall('POST',ERP_FUNCTION_API_END_POINT + "?method=create_issue_warning"
                ,'dept_id='+$(".ap_department").val()+"&empl_id="+$(".ap_employees ").val()
                +"&ded_amount="+$("#ded_amount").val()+"&desc="+$("#description").val()+"&startdate="+$("#startdate").val()
                +'&edit_id='+$('#hdnedit_id').val(),
                function (response) {

                    if(response.status=='Success')
                    {
                        toastr.success(response.msg);
                        fetch_data();
                        $("#loader").css('display','none');
                    }

                    if(response.status=='Error')
                    {
                        toastr.error(response.msg);
                        $("#loader").css('display','none');
                    }


                });

        }


        function fetch_data()
        {
            $('#list_employees').dataTable().fnDestroy();
            $('#list_employees').DataTable({
                "bProcessing": true,
                "serverSide": true,
                "ajax":{
                    url :ERP_FUNCTION_API_END_POINT + "?method=list_warnings", // json datasource
                    type: "post",  // type of method  ,GET/POST/DELETE
                    //data:{'dept_id':$(".ap_department").val(),'emp_id':$(".ap_employees").val(),'year':$(".ap-year").val(),'month':$(".ClsMonths ").val()},
                    error: function(){
                    }
                }
            });

        }


        $('#list_employees tbody').on('click', 'td label.ClsEdit', function (){
            var dept_id=$(this).attr('dept_id');
            var alt_ded_amount=$(this).attr('alt_ded_amount');
            var alt_desc=$(this).attr('alt_desc');
            var alt_start_date=$(this).attr('alt_start_date');
            var alt_id=$(this).attr('alt');
            var alt_empl_id=$(this).attr('alt_empl_id');


            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
                AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_department',null,function () {
                         $('.ap_department').val(dept_id);
                });
            });

            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+dept_id, function (data) {
                AxisPro.PrepareSelectOptions(data, 'id', 'Emp_name', 'ap_employees',null,function () {
                        $('.ap_employees').val(alt_empl_id);
                });
            });

            $('#ded_amount').val(alt_ded_amount);
            $('#description').val(alt_desc);
            $('#startdate').val(alt_start_date);
            $('#hdnedit_id').val(alt_id);





        });

        $('#list_employees tbody').on('click', 'td label.ClsRemove', function (){

            var edit_id=$(this).attr('alt');
            var confrmRes=confirm("Are you really want to remove the issued warning?");
            if(confrmRes==true)
            {
                AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=remove_warning", 'remove_id='+edit_id, function (data) {

                    if (data.status === 'Success') {
                        toastr.success('SUCCESS !. Data Removed Successfully.');
                        fetch_data();
                    }
                    else
                    {
                        toastr.success('ERROR !. Error occured while removing.');

                    }

                });
            }
        });




    </script>