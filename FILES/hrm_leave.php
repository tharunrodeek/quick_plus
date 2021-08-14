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
                                    <?= trans('APPLY LEAVE') ?> | <span id="spanAvailableLeave" style="color:green;    font-size: 14pt;    font-weight: 600;"></span>
                                    <label id="lblWarnings"></label>
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
                                        <label><?= trans('NUMBER OF DAYS REQUESTED') ?>: <span id="days_requested" style="    font-weight: bold;
    font-size: 15pt;
    color: blue;"></span></label>
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
                                    <div>
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
                              <!-- <div class="col-md-8">
                                   <canvas id="barChart" ></canvas>
                               </div>-->

                                <div class="col-md-8">

                                    <div class="table-responsive" style="padding: 1%;">

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
<input type="hidden" id="hdn_leavecount_annual" name="hdn_leavecount_annual" />
<input type="hidden" id="hdn_days_requested" name="hdn_days_requested" />

<?php include "footer.php"; ?>
<script>

    $(document).ready(function () {
        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_leave_types', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_leave_types',null,function () {
                $('.ap_leave_types').prepend('<option value="0">Choose leave type</option>');
                $('.ap_leave_types').val(0);
            });
        });



        Fecthdata();

    });

    $('#toDate').change(function()
    {

            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_number_of_days",
                'f_date='+$('.fromdate').val()+'&toDate='+$('#toDate').val(), function (data)
                {


                    if(data.days_requested <0)
                    {
                        toastr.error('ERROR !. Please ensure that the To Date is greater than or equal to the From Date.');
                        $("#loader").css('display','none');
                        $('#toDate').val('');
                        return false;
                    }
                    else
                    {
                        $('#days_requested').html(data.days_requested);
                        $('#hdn_days_requested').val(data.days_requested);
                    }


                });

    });

    $('.ap_leave_types ').change(function()
    {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_emp_leaves",
            'leave_type='+$('.ap_leave_types').val(), function (data)
            {
                $("#spanAvailableLeave").html(data.avail_leave);
                $("#hdn_leavecount_annual").val(data.annual_leave_cnt);


                if(data.prob_flag=='1')
                {
                    $('#lblWarnings').html(data.prob_msg);
                    $('.fromdate').attr('disabled','disabled');
                    $('#toDate').attr('disabled','disabled');
                    $('#txt_reason_leave').attr('disabled','disabled');
                    $('#txttasks').attr('disabled','disabled');
                }
                else
                {
                    $('#lblWarnings').html('');
                    $('.fromdate').removeAttr('disabled');
                    $('#toDate').removeAttr('disabled');
                    $('#txt_reason_leave').removeAttr('disabled');
                    $('#txttasks').removeAttr('disabled');
                }

            });

    });



    $('.ap-datepicker').datepicker({
        format: 'dd-mm-yyyy',
    });

    function SubmitLeave() {
        var $form = $("#leave_form");
        var params = AxisPro.getFormData($form);
        $("#loader").css('display','block');

        var eDate = new Date($("#toDate").val());
        var sDate = new Date($(".fromdate").val());

        if(sDate> eDate)
        {
            toastr.error('ERROR !. Please ensure that the End Date is greater than or equal to the Start Date.');
            $("#loader").css('display','none');
            return false;
        }


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

        if($("#txt_reason_leave").val()=='')
        {
            toastr.error('ERROR !. Please enter the reason for leave');
            $("#loader").css('display','none');
            return false;

        }

        if($('#hdn_leavecount_annual').val()!=0)
        {
            if($('#hdn_days_requested').val()>parseFloat($('#hdn_leavecount_annual').val())+3)
            {
                toastr.error('You cant take annual leave more than available. you can take max 3 days extra.');
                $("#loader").css('display','none');
                return false;
            }
        }



        $(".error_note").hide();

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=save_leave", params, function (data) {

            if (data.status === 'ERROR') {
                toastr.error(data.msg);
            }
            else
            {
                toastr.success(data.msg);
                Fecthdata();

                $("#toDate").val('');
                $(".fromdate").val('');
                $("#txt_reason_leave").val('');
                $("#txttasks").val('');
                $("#days_requested").val('0');
            }
            $("#loader").css('display','none');
        });
    }

function Fecthdata()
{

    $('#applied_leaves').dataTable().fnDestroy();
    $('#applied_leaves').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax":{
            url :ERP_FUNCTION_API_END_POINT + "?method=get_applied_leaves", // json datasource
            type: "post",  // type of method  ,GET/POST/DELETE
            //data:{'dept_id':$(".ap_department").val(),'emp_id':$(".ap_employees").val(),'year':$(".ap-year").val(),'month':$(".ClsMonths ").val()},
            error: function(){
            }
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


    /*var ctxB = document.getElementById("barChart").getContext('2d');
    var myBarChart = new Chart(ctxB, {
        type: 'bar',
        data: {
            labels: ["Red"],
            datasets: [{
                label: '# of Votes',
                data: [70],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)'
                   /!* 'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'*!/
                ],
                borderColor: [
                    'rgba(255,99,132,1)'/!*,
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'*!/
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,

                    }
                }]
            }
        }
    });*/

</script>