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
                                    <?= trans('APPLY LEAVE') ?> | <label><?= trans('Earned Leaves :')?> </label><span id="spanAvailableLeave" style="color:green;    font-size: 18pt;    font-weight: 600;"></span>

                                </h3>
                            </div>
                        </div>



                        <div class="kt-portlet__body" style="padding: 5px !important;">
                            <div class="row">

                                <div class="col-md-4">
                                    <form class="kt-form kt-form--label-right" id="leave_form">
                                    <div>
                                        <label><?= trans('LEAVE TYPE') ?>:
                                        </label>
                                        <select class="form-control kt-select2 ap-select2 ap_leave_types"
                                                name="leave_type" >
                                            <option value="">SELECT</option>
                                        </select>
                                        <span class="error_note form-text text-muted kt-hidden">Please enter your full name</span>
                                    </div>
                                    <div>
                                        <label class=""><?= trans('FROM DATE') ?>:</label>
                                        <input type="text"   name="ap-datepicker" class="form-control ap-datepicker fromdate"
                                               placeholder=""  autocomplete="off">
                                    </div>
                                    <div>
                                        <label><?= trans('TO DATE') ?>:</label>
                                        <div class="input-group">
                                            <input type="text"   name="todate" class="form-control ap-datepicker"
                                                   placeholder="" id="toDate" autocomplete="off">
                                        </div>
                                    </div>
                                    <div>
                                        <label class=""><?= trans('REASON') ?>:</label>
                                        <input type="text" id="txt_reason_leave" name="txt_reason_leave"
                                               class="form-control" placeholder="">
                                    </div>
                                    <div>
                                            <label class=""><?= trans('TASK ASSIGNED') ?>:</label>
                                            <textarea id="txttasks" name="txttasks" class="form-control" ></textarea>
                                    </div>
                                    <div >
                                        <label style="height: 24px;"></label>
                                        <div class="input-group">
                                            <button type="button" onclick="SubmitLeave();" class="btn btn-primary">
                                                <?= trans('Apply Leave') ?>
                                            </button>&nbsp;&nbsp;

                                            <button type="button" class="btn btn-secondary btnCancel"><?= trans('Cancel') ?></button>
                                            <img src="<?= $base_url ?>assets/media/img/ajax-loader.gif" id="loader" style="height: 16px;
                                            margin-top: 4%;display: none;"/>
                                            <input type="hidden" id="hdnCurrentDate" name="hdnCurrentDate"  value="<?php echo date('d-m-Y') ?>"/>
                                            <input type="hidden" id="hdnAction" name="hdnAction"/>
                                        </div>
                                    </div>
                                </form>

                                </div>
                                <div class="col-md-8">


                                        <div class="table-responsive" >

                                            <table class="table table-bordered" id="applied_leaves">
                                                <thead>
                                                <th><?= trans('Leave Type') ?></th>
                                                <th><?= trans('Reason') ?></th>
                                                <th><?= trans('Start Date') ?></th>
                                                <th><?= trans('End Date') ?></th>
                                                <th><?= trans('Days Requested') ?></th>
                                                <th><?= trans('Status') ?></th>
                                                <th></th>
                                                <th></th>

                                                </thead>
                                                <tbody id="applied_leaves_tbody">

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

    $(document).ready(function () {
        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_leave_types', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_leave_types',null,function () {

            });
        });

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_emp_leaves', format: 'json'}, function (data) {

            $("#spanAvailableLeave").html(data.avail_leave);

        });


        Fecthdata();

    });

    $('.ap-datepicker').datepicker({
        format: 'dd-mm-yyyy',
    });

    function SubmitLeave() {
        var $form = $("#leave_form");
        var params = AxisPro.getFormData($form);
        $("#loader").css('display','block');
//alert($("#toDate").val());

        if($(".fromdate").val()=='')
        {
            toastr.error('ERROR !. Please select from date');
            $("#loader").css('display','none');
            return false;

        }
        if($("#toDate").val()=='')
        {
            toastr.error('ERROR !. Please select to date');
            $("#loader").css('display','none');
            return false;

        }

       /* if($("#leave_days").val()=='')
        {
            toastr.error('ERROR !. Please enter number of days leave required');
            return false;
        }*/

        /*if($("#hdnCurrentDate").val() > $(".ap-datepicker").val())
        {
            toastr.error('ERROR !. Selected leave date less than current date.');
            return false;
        }*/

        $(".error_note").hide();

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=save_leave", params, function (data) {

            if (data.status === 'FAIL' && data.msg === 'VALIDATION_FAILED') {


                toastr.error('ERROR !. PLEASE CHECK THE FORM DATA.');
                var errors = data.data;

                $.each(errors, function (key, value) {
                    $("#" + key)
                        .after('<span class="error_note form-text text-muted">' + value + '</span>')
                })


            }
            else
            {
                toastr.success('SUCCESS !. LEAVE APPLIED SUCCESSFULLY.');

                Fecthdata();
            }
            $("#loader").css('display','none');
        });
    }

function Fecthdata()
{
    AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
        method: 'get_applied_leaves',
        format: 'json'
    }, function (data) {

        if(data) {

            var tbody_html = "";

            $.each(data, function(key,value) {

                tbody_html+="<tr>";
                tbody_html+="<td>"+value.description+"</td>";
                tbody_html+="<td>"+value.reason+"</td>";
                tbody_html+="<td>"+value.fromdate+"</td>";
                tbody_html+="<td>"+value.todate+"</td>";
                tbody_html+="<td>"+value.days+"</td>";
                tbody_html+="<td>"+value.statustext+"</td>";
                tbody_html+="<td><label alt='"+value.id+"' alt_val='"+value.leave_type+"' " +
                    "        alt_date='"+value.fromdate+"' alt_days='"+value.days+"' alt_reason='"+value.reason+"' class='btn btn-sm btn-primary ClsBtnEdit'><i class='flaticon-edit'></i></td>";
                tbody_html+="<td><label alt='"+value.id+"' alt_val='"+value.leave_type+"' class='btn btn-sm btn-primary ClsBtnRemove'><i class='flaticon-delete'></i></td>";

                tbody_html+="</tr>";

            });

            $("#applied_leaves_tbody").html(tbody_html);

        }

    });
}
    $("#applied_leaves").DataTable( {
        //dom: 'Bfrtip'
    });



    $('#applied_leaves tbody').on('click', 'td label.ClsBtnEdit', function (){
        var leave_type=$(this).attr('alt_val');
        var leave_date=$(this).attr('alt_date');
        var days=$(this).attr('alt_days');
        var reason=$(this).attr('alt_reason');
        var edit_id=$(this).attr('alt');

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_leave_types', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_leave_types',null,function () {
                $(".ap_leave_types").val(leave_type).trigger('change');
                $(".ap-datepicker").val(leave_date);
                $("#leave_days").val(days);
                $("#txt_reason_leave").val(reason);
                $("#hdnAction").val(edit_id);
            });
        });
    });

    $('#applied_leaves tbody').on('click', 'td label.ClsBtnRemove', function (){
        var edit_id=$(this).attr('alt');
        var confrmRes=confirm("Are you really want to remove the leave request?");
        if(confrmRes==true)
        {
            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=remove_leave", 'remove_id='+edit_id, function (data) {

                if (data.status === 'FAIL' && data.msg === 'VALIDATION_FAILED') {

                }
                else
                {
                    toastr.success('SUCCESS !. LEAVE Removed Successfully.');
                    Fecthdata();
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