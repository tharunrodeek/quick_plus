<?php
include "header.php";

global $SysPrefs;
$personal_time_mapped_leave=$SysPrefs->prefs['payroll_personal_assigned_leave'];
?>
<style>
    .input-disabled
    {
        pointer-events: none;
        background-color: #ccc;
    }
</style>

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
                                    <div class="ClsSick_leave_doc" style="display: none;">
                                        <label><?= trans('SICK LEAVE DOCUMENT') ?>:
                                        </label>
                                        <input type="file" class="form-control" name="sick_leave_doc" id="sick_leave_doc">
                                         <span class="error_note form-text text-muted kt-hidden">Please enter a valid file</span>
                                    </div>
                                    <div class="ClsSick_leave_doc_uploaded" style="display: none;">
                                         <span class="form-text text-muted" id="uploaded_doc"></span>
                                    </div>

                                    <!-- <div class="ClsHalf_day_Full" style="display: none;">
                                        <label><?/*= trans('HALF DAY/FULL DAY') */?>:
                                        </label>
                                        <select class="form-control ap_half_full"
                                                name="half_full" >
                                            <option value="0">SELECT</option>
                                            <option value="1">Half Day</option>
                                            <option value="2">Full Day</option>
                                        </select>
                                        
                                    </div>-->

                                    <div>
                                        <label class=""><?= trans('FROM DATE') ?>:</label>
                                        <input type="text"   name="ap-datepicker" class="form-control ap-datepicker fromdate"
                                               placeholder=""  autocomplete="off">
                                    </div>
                                    <div class="toDate_div">
                                        <label><?= trans('TO DATE') ?>:</label>
                                        <div class="input-group">
                                            <input type="text"   name="todate" class="form-control ap-datepicker"
                                                   placeholder="" id="toDate" autocomplete="off">
                                        </div>
                                    </div>

                                        <div class="row ClsPersonalTime" style="display: none;">
                                            <div class="col-sm">
                                                <label><?= trans('START TIME') ?>:</label>
                                                <div class="input-group">
                                                    <input type="time" id="start_time" name="start_time"
                                                           min="09:00" max="18:00" required>
                                                </div>
                                            </div>
                                            <div class="col-sm">
                                                <label><?= trans('END TIME') ?>:</label>
                                                <div class="input-group">
                                                    <input type="time" id="end_time" name="end_time"
                                                           min="09:00" max="18:00" required>
                                                </div>
                                            </div>

                                        </div>



                                    <div>
                                        <label><?= trans('NUMBER OF DAYS REQUESTED') ?>: <span id="days_requested" style="font-weight: bold;font-size: 15pt;color: blue;"></span></label>
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
                                            <th><?= trans('Document') ?></th>
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
<input type="hidden" id="hdn_leave_count_ml" name="hdn_leave_count_ml" />
<input type="hidden" id="hdn_leave_count_bl" name="hdn_leave_count_bl" />
<input type="text" id="hdn_personal_time_mapped" name="hdn_personal_time_mapped" value="<?php echo $personal_time_mapped_leave; ?>"/>


<?php include "footer.php"; ?>
<script>
const toJSDate = (dateStr) => {
  const [day, month, year] = dateStr.split("-");
  return new Date(year, month - 1, day);
}
    $(document).ready(function () {
        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_leave_types', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_leave_types',null,function () {
                $('.ap_leave_types').prepend('<option value="0">Choose leave type</option>');
                $('.ap_leave_types').val(0);
            });
        });

        Fecthdata();

    });


    /*$('.ap_leave_types').change(function()
    { 

        document.getElementById('sick_leave_doc').value= null;
        var leave_code=$("#hdn_current_leave_code").val();
        alert(leave_code);

        if(leave_code=='al' || leave_code=='sl')
        {
             $('.ClsHalf_day_Full').css('display','block');
        }
        else
        {
            $('.ClsHalf_day_Full').css('display','none');
        }
             if(leave_code=='sl'){
                 $('.ClsSick_leave_doc,.ClsSick_leave_doc_uploaded').css('display','block');
             }else{
                $('.ClsSick_leave_doc,.ClsSick_leave_doc_uploaded').css('display','none');
             }
    });*/


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
    
    $(".fromdate").change(function(){ 
        $('#toDate').val($('.fromdate').val()).trigger('change');
        });
    $(".ap_half_full,.ap_leave_types").change(function(){

        var leave_code=$("#hdn_current_leave_code").val();
        if(leave_code=='al' || leave_code=='sl'){
        if($(".ap_half_full").val()=='1'){
        $( ".toDate_div" ).css('display','none');
        }else{
        $( ".toDate_div" ).css('display','block');

        }   
        }else{
            $( ".toDate_div" ).css('display','block');
        }
    });
    $('.ap_leave_types ').change(function()
    {
        if($(this).val()==$("#hdn_personal_time_mapped").val())
        {
             $('.ClsPersonalTime').css('display','flex');
        }
        else
        {
            $('.ClsPersonalTime').css('display','none');
        }


        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_emp_leaves",
            'leave_type='+$('.ap_leave_types').val(), function (data)
            {

                $("#spanAvailableLeave").html(data.avail_leave);
                $("#hdn_leavecount_annual").val(data.annual_leave_cnt);
                $("#hdn_leave_count_ml").val(data.ml_leave_available);
                $("#hdn_leave_count_bl").val(data.bl_leave_available);
                $("#hdn_current_leave_code").val(data.leave_code);


                document.getElementById('sick_leave_doc').value= null;
                 var leave_code=data.leave_code;

                if(leave_code=='al' || leave_code=='sl')
                {
                    $('.ClsHalf_day_Full').css('display','block');
                }
                else
                {
                    $('.ClsHalf_day_Full').css('display','none');
                }
                if(leave_code=='sl'){
                    $('.ClsSick_leave_doc,.ClsSick_leave_doc_uploaded').css('display','block');
                }else{
                    $('.ClsSick_leave_doc,.ClsSick_leave_doc_uploaded').css('display','none');
                }





                if(data.prob_flag=='1')
                {
                    $('#lblWarnings').html(data.prob_msg);
                    $('.fromdate').attr('disabled','disabled');
                    $('#toDate').attr('disabled','disabled');
                    $('#txt_reason_leave').attr('disabled','disabled');
                    $('#txttasks').attr('disabled','disabled');
                }
               /* else if(data.combo_off=='0')
                {
                    $('.fromdate').attr('disabled','disabled');
                    $('#toDate').attr('disabled','disabled');
                    $('#txt_reason_leave').attr('disabled','disabled');
                    $('#txttasks').attr('disabled','disabled');
                }*/
                else if(data.maternity_flg=='NOT_ALLOWED')
                {
                    $('#lblWarnings').html("You can't apply for Maternity Leave.You already taken the leave.");
                    $('.fromdate').attr('disabled','disabled');
                    $('#toDate').attr('disabled','disabled');
                    $('#txt_reason_leave').attr('disabled','disabled');
                    $('#txttasks').attr('disabled','disabled');
                }
                else if(data.maternity_flg=='YEAR_NOT_REACH')
                {
                     $('#lblWarnings').html("You are not eligible for Maternity Leave.");
                    $('.fromdate').attr('disabled','disabled');
                    $('#toDate').attr('disabled','disabled');
                    $('#txt_reason_leave').attr('disabled','disabled');
                    $('#txttasks').attr('disabled','disabled');
                }
                else if(data.bereavement=='NOT_ALLOWED')
                {
                    $('#lblWarnings').html("You are not eligible for Bereavement Leave,You are already used 3 days in a year.");
                    $('.fromdate').attr('disabled','disabled');
                    $('#toDate').attr('disabled','disabled');
                    $('#txt_reason_leave').attr('disabled','disabled');
                    $('#txttasks').attr('disabled','disabled');
                }
                else if(data.sick_leave_validation_flg=='VALID_FOR_HALF_DAY')
                {
                    $('#lblWarnings').html("You are now eligible for took half for sick leave");
                    $('.ap_half_full').val('2');
                    $('.ap_half_full').attr('disabled','disabled');
                }
                else if(data.sick_leave_validation_flg=='VALID_FOR_UNPAID')
                {
                    $('#lblWarnings').html("You are now eligible for took unpaid for sick leave");
                   // $('.ap_half_full').val('2');
                   // $('.ap_half_full').attr('disabled','disabled');
                }
                else
                {
                    $('#lblWarnings').html('');
                    $('.fromdate').removeAttr('disabled');
                    $('#toDate').removeAttr('disabled');
                    $('#txt_reason_leave').removeAttr('disabled');
                    $('#txttasks').removeAttr('disabled');
                }

                //alert($('.ap_leave_types').val()+' / '+$('#hdn_personal_time_mapped').val());
                if($('.ap_leave_types').val()==$('#hdn_personal_time_mapped').val())
                {
                    $('#toDate').addClass('input-disabled');
                }



            });


    });



    $('.ap-datepicker').datepicker({
        format: 'dd-mm-yyyy',
    });

    function SubmitLeave() {

        var $form = $("#leave_form");
        var params = AxisPro.getFormData($form);

        // alert(JSON.stringify(params));
        // return;
        $("#loader").css('display','block');

        var eDate = toJSDate($("#toDate").val());
        // new Date($("#toDate").val());
        var sDate = toJSDate($(".fromdate").val()); 
        // new Date($(".fromdate").val());
        // alert(`sDate: ${sDate} eDate:${eDate}`);
        // return;

        if(sDate > eDate)
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


     /*   if($(".ap_leave_types ").val()=='1' || $(".ap_leave_types ").val()=='3')
        {
            if($(".ap_half_full").val()=='0')
            {
                toastr.error('ERROR !. Please choose half day or full day');
                $("#loader").css('display','none');
                return false;
            }
        }*/


        if($(".ap_leave_types ").val()=='5')
        {
             if($('#hdn_days_requested').val()>parseFloat($('#hdn_leave_count_ml').val()))
            {
                toastr.error('You cant take maternity leave more than available.');
                $("#loader").css('display','none');
                return false;
            }
        }

         if($(".ap_leave_types ").val()=='8')
        {
             if($('#hdn_days_requested').val()>parseFloat($('#hdn_leave_count_bl').val()))
            {
                toastr.error('You cant take bereavement leave more than available.');
                $("#loader").css('display','none');
                return false;
            }
        }



        $(".error_note").hide();

            var form = $("#leave_form");
            var params = form.serializeArray();
            var files = $("#sick_leave_doc")[0].files;
            var formData = new FormData();
            for (var i = 0; i < files.length; i++) {
                formData.append('sick_leave_doc', files[i]);
            }
            $(params).each(function(index, element) {
                formData.append(element.name, element.value);
            });


            if($(".ap_leave_types ").val()=='3')
            {
                if(files.length==0)
                {
                    toastr.error('Kindly attach sickleave document.');
                    $("#loader").css('display','none');
                    return false;
                }
            }

            

            $.ajax({
                type: "POST",
                url: ERP_FUNCTION_API_END_POINT+"?method=save_leave",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    data = JSON.parse(data);
                    if (data.status == 'ERROR') {
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
                $("#hdnAction").val('');
                $(".ap_leave_types").val(1).trigger('change');
            }
            $("#loader").css('display','none');
                }
            });

    }

    // function SubmitLeaveOld() {
    //     var $form = $("#leave_form");
    //     var params = AxisPro.getFormData($form);

    //     // alert(JSON.stringify(params));
    //     // return;
    //     $("#loader").css('display','block');

    //     var eDate = new Date($("#toDate").val());
    //     var sDate = new Date($(".fromdate").val());

    //     if(sDate> eDate)
    //     {
    //         toastr.error('ERROR !. Please ensure that the End Date is greater than or equal to the Start Date.');
    //         $("#loader").css('display','none');
    //         return false;
    //     }


    //     if($(".fromdate").val()=='')
    //     {
    //         toastr.error('ERROR !. Please select from date');
    //         $("#loader").css('display','none');
    //         return false;

    //     }
    //     if($("#toDate").val()=='')
    //     {
    //         toastr.error('ERROR !. Please select to date');
    //         $("#loader").css('display','none');
    //         return false;

    //     }

    //     if($("#txt_reason_leave").val()=='')
    //     {
    //         toastr.error('ERROR !. Please enter the reason for leave');
    //         $("#loader").css('display','none');
    //         return false;

    //     }

    //     if($('#hdn_leavecount_annual').val()!=0)
    //     {
    //         if($('#hdn_days_requested').val()>parseFloat($('#hdn_leavecount_annual').val())+3)
    //         {
    //             toastr.error('You cant take annual leave more than available. you can take max 3 days extra.');
    //             $("#loader").css('display','none');
    //             return false;
    //         }
    //     }


    //     if($(".ap_leave_types ").val()=='1' || $(".ap_leave_types ").val()=='3')
    //     {
    //         if($(".ap_half_full").val()=='0')
    //         {
    //             toastr.error('ERROR !. Please choose half day or full day');
    //             $("#loader").css('display','none');
    //             return false;
    //         }
    //     }


    //     if($(".ap_leave_types ").val()=='5')
    //     {
    //          if($('#hdn_days_requested').val()>parseFloat($('#hdn_leave_count_ml').val()))
    //         {
    //             toastr.error('You cant take maternity leave more than available.');
    //             $("#loader").css('display','none');
    //             return false;
    //         }
    //     }

    //      if($(".ap_leave_types ").val()=='8')
    //     {
    //          if($('#hdn_days_requested').val()>parseFloat($('#hdn_leave_count_bl').val()))
    //         {
    //             toastr.error('You cant take bereavement leave more than available.');
    //             $("#loader").css('display','none');
    //             return false;
    //         }
    //     }



    //     $(".error_note").hide();

    //     AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=save_leave", params, function (data) {
    //         console.log(data);
    //         return;
    //         if (data.status === 'ERROR') {
    //             toastr.error(data.msg);
    //         }
    //         else
    //         {
    //             toastr.success(data.msg);
    //             Fecthdata();

    //             $("#toDate").val('');
    //             $(".fromdate").val('');
    //             $("#txt_reason_leave").val('');
    //             $("#txttasks").val('');
    //             $("#days_requested").val('0');
    //         }
    //         $("#loader").css('display','none');
    //     });
    // }

function Fecthdata()
{

    $('#applied_leaves').dataTable().fnDestroy();
    $('#applied_leaves').DataTable({
        "order": [[ 2, "desc" ]],
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
        var half_full=$(this).attr('alt_half_full');
        var leave_date=$(this).attr('alt_date');
        var leave_todate=$(this).attr('alt_todate');
        var days=$(this).attr('alt_days');
        var reason=$(this).attr('alt_reason');
        var task_assigned =$(this).attr('alt_task');
        var sick_leave_doc_uploaded =$(this).attr('alt_sick_leave_doc');
        
        var edit_id=$(this).attr('alt');

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_leave_types', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_leave_types',null,function () {
                $(".ap-datepicker").val(leave_date);
                $("#toDate").val(leave_todate);
                $("#txttasks").val(task_assigned);
                $("#days_requested").html(days);
                $("#txt_reason_leave").val(reason);
                $(".ap_half_full").val(half_full);
                if(sick_leave_doc_uploaded!=null){
                $("#uploaded_doc").html(`UPLOADED DOCUMENT: <a class="badge badge-pill badge-primary" href="assets/uploads/${sick_leave_doc_uploaded}" target="_blank" >${sick_leave_doc_uploaded}</a>`);
                 $('.ClsSick_leave_doc_uploaded').css('display','block');
                }else{

                 $('.ClsSick_leave_doc_uploaded').css('display','none');
                }
                $(".ap_leave_types").val(leave_type).trigger('change');
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