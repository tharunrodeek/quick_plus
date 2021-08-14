<?php
include "header.php";?>

<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">
                <?php include('hrm_employee_comman_header.php'); ?>
                <div class="row">

                    <div class="kt-portlet">


                        <div class="kt-portlet__head">

                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('PASSPORT WITHDRAWAL REQUEST') ?>

                                </h3>
                            </div>
                        </div>



                        <div class="kt-portlet__body" style="padding: 5px !important;">
                            <div class="row">

                                <div class="col-md-4">
                                    <form class="kt-form kt-form--label-right" id="leave_form">
                                    <div>
                                        <label><?= trans('REQUIRED DATE') ?>:
                                        </label>
                                        <input type="text" name="ap-datepicker" class="form-control ap-datepicker date"
                                               placeholder=""  autocomplete="off">
                                        <span class="error_note form-text text-muted kt-hidden">Please enter your full name</span>
                                    </div>
                                    <div>
                                            <label><?= trans('RETURN DATE') ?>:
                                            </label>
                                            <input type="text" name="returndate" class="form-control ap-datepicker returndate"
                                                   placeholder=""  autocomplete="off">
                                            <span class="error_note form-text text-muted kt-hidden">Please enter your full name</span>
                                    </div>

                                    <div>
                                            <label class=""><?= trans('REASON FOR REQUEST') ?>:</label>
                                            <textarea id="txttasks" name="txttasks" class="form-control" ></textarea>
                                    </div>
                                    <div>
                                        <label style="height: 24px;"></label>
                                        <div class="input-group">
                                            <button type="button" onclick="SubmitLeave();" class="btn btn-primary">
                                                <?= trans('Submit Request') ?>
                                            </button>&nbsp;&nbsp;

                                            <button type="button" class="btn btn-secondary btnCancel"><?= trans('Cancel') ?></button>
                                            <img src="<?= $base_url ?>assets/media/img/ajax-loader.gif" id="loader" style="height: 16px;
                                            margin-top: 4%;display: none;"/>
                                            <input type="hidden" id="hdnCurrentDate" name="hdnCurrentDate"  value="<?php echo date('d-m-Y') ?>"/>
                                            <input type="hidden" id="hdnAction" name="hdnAction"/>
                                            <input type="hidden" id="hdn_id" name="hdn_id"/>
                                        </div>
                                    </div>
                                </form>

                                </div>
                                <div class="col-md-8">


                                        <div class="table-responsive" >

                                            <table class="table table-bordered" id="passport_reuqests">
                                                <thead>
                                                <th><?= trans('Ref.No') ?></th>
                                                <th><?= trans('Emp.Name') ?></th>
                                                <th><?= trans('Required Date') ?></th>
                                                <th><?= trans('Return Date') ?></th>
                                                <th><?= trans('Req.Status') ?></th>
                                                <th><?= trans('Created On') ?></th>
                                                <th><?= trans('Comments') ?></th>
                                                <th></th>


                                                </thead>
                                                <tbody id="passport_reuqests_tbody">

                                                </tbody>
                                            </table>

                                        </div>




                                </div>

                            </div>


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


    $('.ap-datepicker').datepicker({
        format: 'dd-mm-yyyy',
    });
    fetchDataTotable();
    function SubmitLeave() {
        var form = $("#leave_form");
        var params = AxisPro.getFormData(form);
        $("#loader").css('display','block');




        if($(".date").val()=='')
        {
            toastr.error('ERROR !. Please choose passport required date');
            $("#loader").css('display','none');
            return false;

        }

        if($(".returndate").val()=='')
        {
            toastr.error('ERROR !. Please choose return date');
            $("#loader").css('display','none');
            return false;
        }

        if($("#txttasks").val()=='')
        {
            toastr.error('ERROR !. Please enter reason for request');
            $("#loader").css('display','none');
            return false;
        }

        $(".error_note").hide();

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=submit_passport_request", params, function (data) {

            if(data.status =='Success')
            {
                toastr.success(data.msg);
                fetchDataTotable();
            }
            else
            {
                toastr.error(data.msg);
            }


            $("#loader").css('display','none');
        });
    }

    function fetchDataTotable()
    {
        $('#passport_reuqests').dataTable().fnDestroy();
        $('#passport_reuqests').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :ERP_FUNCTION_API_END_POINT + "?method=get_passport_requests", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                //data:{'dept_id':$(".ap_department").val(),'emp_id':$(".ap_employees").val(),'year':$(".ap-year").val(),'month':$(".ClsMonths ").val()},
                error: function(){
                }
            }
        });
    }



    $('#passport_reuqests tbody').on('click', 'td label.ClsBtnEdit', function (){
        var alt_id=$(this).attr('alt_id');
        var comment=$(this).attr('alt_comment');
        var date=$(this).attr('alt_date');
        var return_date=$(this).attr('alt_return_date');

        $('.date').val(date);
        $('#txttasks').val(comment);
        $('#hdn_id').val(alt_id);
        $('.returndate').val(return_date);

    });

    $('#passport_reuqests tbody').on('click', 'td label.ClsBtnRemove', function (){
        var edit_id=$(this).attr('alt_id');
        var confrmRes=confirm("Are you really want to delete request?");
        if(confrmRes==true)
        {
            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=remove_requests"
                 , 'remove_id='+edit_id+'&type='+1, function (data) {

                if (data.status === 'Error') {
                    toastr.error(data.msg);
                    fetchDataTotable();
                }
                else
                {
                    toastr.success(data.msg);
                    fetchDataTotable();

                }

            });
        }
    });


    $(".btnCancel").click(function()
    {
       location.reload();
    });

    $("#leave_days").change(function()
    {

       if($(this).val()>$("#spanAvailableLeave").html())
       {
           toastr.error('ERROR! Entered leave days is greaterthan available.');
           $("#leave_days").val('');
       }
    });


</script>