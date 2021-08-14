<?php
include "header.php";
?>
<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">

                <div class="row" style="width: 100%;">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <div class="row">

                                        <div class="col-md-5">
                                            <div style="width: 212px;margin-top: 20%;">
                                                <?= trans('END OF SERVICE') ?>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div style="float: right;">
                                                <img src="assets/images/esb.jpg" style="width: 119px;"/>
                                            </div>
                                        </div>
                                    </div>


                                </h3>
                            </div>

                        </div>

                        <div class="kt-portlet__body" style="padding: 5px !important;">
                            <div class="row">

                                <div class="col-md-4">

                                        <label class="ClsBold"><?= trans('DEPARTMENT') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_department"
                                                    name="ddl_department" >
                                                <option value="">SELECT</option>
                                            </select>
                                        </div>


                                        <label class="ClsBold"><?= trans('EMPLOYEE') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_employees"
                                                    name="leave_type" >
                                                <option value="">All Employees</option>
                                            </select>
                                        </div>

                                </div>

                                <div class="col-md-8 ClsshowESB" style="display: none;">
                                    <div align="center"><label style="font-size: 13pt;font-weight: bold;color: black;text-decoration: underline;">View Employee ESB details</label></div>
                                    <div class="card card-custom example example-compact">
                                        <div class="form-group row fv-plugins-icon-container">
                                            <label class="col-form-label text-right col-lg-3 col-sm-12" style="font-weight:bold;">Employee Join Date</label>
                                            <div class="col-lg-9 col-md-9 col-sm-12" style="    margin-top: 10px;">
                                                <span id="emp_join_date" ></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card card-custom example example-compact">
                                        <div class="form-group row fv-plugins-icon-container">
                                            <label class="col-form-label text-right col-lg-3 col-sm-12" style="font-weight:bold;">Years Completed</label>
                                            <div class="col-lg-9 col-md-9 col-sm-12"  style="    margin-top: 10px;">
                                                <span id="year_complete"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card card-custom example example-compact">
                                        <div class="form-group row fv-plugins-icon-container">
                                            <label class="col-form-label text-right col-lg-3 col-sm-12" style="font-weight:bold;">Loan pending amount</label>
                                            <div class="col-lg-3 col-md-9 col-sm-12" style="    margin-top: 1%;">
                                                <input type="text" id="txt_loan_amount" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card card-custom example example-compact">
                                        <div class="form-group row fv-plugins-icon-container">
                                            <label class="col-form-label text-right col-lg-3 col-sm-12" style="font-weight:bold;">Warning deduction pending amount</label>
                                            <div class="col-lg-3 col-md-9 col-sm-12" style="    margin-top: 1%;">
                                                <input type="text" id="txt_warning_amount" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card card-custom example example-compact">
                                        <div class="form-group row fv-plugins-icon-container">
                                            <label class="col-form-label text-right col-lg-3 col-sm-12" style="font-weight:bold;">ESB Calculated</label>
                                            <div class="col-lg-3 col-md-9 col-sm-12" style="    margin-top: 1%;">
                                                <input type="text" id="txt_esb_amount" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="card card-custom example example-compact">
                                        <div class="form-group row fv-plugins-icon-container">
                                            <label class="col-form-label text-right col-lg-3 col-sm-12" style="font-weight:bold;">ESB Processing From Account</label>
                                            <div class="col-sm-8" style="margin-top: 1%;">
                                                <select class="form-control kt-select2 ap-select2 ClsESBFromAccount"
                                                        name="ClsESBFromAccount" >
                                                    <option value="">SELECT</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="card card-custom example example-compact">
                                        <div class="form-group row fv-plugins-icon-container">
                                            <div class="col-sm-12 ClsESBbutton" align="center" style="margin-top: 3%;">
                                                <a href="#" class="btn btn-success font-weight-bold btn-pill">Process ESB</a>
                                                <img src="<?= $base_url ?>assets/media/img/ajax-loader.gif" id="loader"
                                                     style="height: 16px;margin-top: 4%;display: none;"/>
                                            </div>

                                            <div id="esb_label" class="col-sm-12 " align="center" style="display:none;margin-top: 3%;">
                                                <label  style="color: red;
    font-weight: bold;
    margin-top: 3%;
    font-size: 13pt;">ESB Processed</label>
                                            </div>

                                        </div>
                                    </div>



                                </div>

                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="hdn_esb_amnt" />
<input type="hidden" id="hdn_warning_amount" />
<input type="hidden" id="hdn_loan_amnt" />

<?php include "footer.php"; ?>
    <style>
        .ClsBold
        {
            font-weight: bold;
        }
    </style>
<script>
    $(document).ready(function () {
        Loaddept();

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_accounts', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'account_code', 'accname', 'ClsESBFromAccount',null,function () {
                $('.ClsESBFromAccount').prepend('<option value="0">Choose Account</option>');

            });
        });



    });


    $(".ap_department").change(function() {
        loadEmployees();
    });


   function Loaddept()
    {
        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_department',null,function () {
                $('.ap_department').prepend('<option value="0">Select Department</option>');


                    $('.ap_department').val('0');


                    loadEmployees();


            });
        });
    }

    function loadEmployees()
    {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+$(".ap_department").val(), function (data) {
            AxisPro.PrepareSelectOptions(data, 'Empid', 'Emp_name', 'ap_employees',null,function () {
                $('.ap_employees').prepend('<option value="0">SELECT Employees</option>');
                $('.ap_employees').val('0');

            });
        });
    }


   $('.ap_employees').change(function()
   {
       $('.ClsshowESB').css('display','block');
       displ_end_ofService($('.ap_employees').val());
   });


   function displ_end_ofService(emplid)
   {
       AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_esb_details",
           'empl_id='+emplid, function (data)
           {

             $('#emp_join_date').html(data.join_date);
             $('#year_complete').html(data.years_complted);
             $('#txt_esb_amount').val(data.esb);
             $('#txt_loan_amount').val(data.loan_ded_amnt);
             $('#txt_warning_amount').val(data.warning_ded_amount);

             $('#hdn_esb_amnt').val(data.tot_esb_amount);
             $('#hdn_warning_amount').val(data.warning_ded_amount);
             $('#hdn_loan_amnt').val(data.loan_ded_amnt);



             if(data.years_complted<1)
             {

                 $('.ClsESBbutton').css('display','none');
                 $('#esb_label').css('display','block');
                 $('#esb_label').html("<label style='color:red;font-weight:bold;'>End Of Service Can't be Processed. The years completed less than one year.</label>");
             }
             else
             {
                 if(data.tot_esb_amount=='created')
                 {
                     $('.ClsESBbutton').css('display','none');
                     $('#esb_label').css('display','block');
                 }
                 else
                 {
                     $('.ClsESBbutton').css('display','block');
                     $('#esb_label').css('display','none');
                 }
             }


           });
   }


   $('#txt_warning_amount').change(function()
   {
       var recalc=parseFloat($('#hdn_esb_amnt').val())-(parseFloat($('#txt_loan_amount').val())+parseFloat($('#txt_warning_amount').val()));
       $('#txt_esb_amount').val(recalc);
   });

    $('#txt_loan_amount').change(function()
    {
        var recalc=parseFloat($('#hdn_esb_amnt').val())-(parseFloat($('#txt_loan_amount').val())+parseFloat($('#txt_warning_amount').val()));
        $('#txt_esb_amount').val(recalc);
    });

    $('#txt_esb_amount').change(function()
    {
        var recalc=parseFloat($('#hdn_esb_amnt').val())-(parseFloat($('#txt_loan_amount').val())+parseFloat($('#txt_warning_amount').val()));
        $('#txt_esb_amount').val(recalc);
    });


   $('.btn-pill').click(function(e)
   {
       e.preventDefault();
       var empl_id=$('.ap_employees').val();
       var years=$('#year_complete').html();
       var loan_amnt=$('#txt_loan_amount').val();
       var warn_amnt=$('#txt_warning_amount').val();
       var esb_amnt=$('#txt_esb_amount').val();

       if(esb_amnt=='')
       {
           toastr.error('ESB cant be empty');
           return false;
       }
       else
       {
           $('#loader').css('display','block');
           AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=create_esb",
               'empl_id='+empl_id+'&years='+years+'&loan_amnt='+loan_amnt+'&warn_amnt='+warn_amnt+'&esb_amnt='+esb_amnt
               +'&dept_id='+$('.ap_department').val()+'&esb_from_account='+$('.ClsESBFromAccount').val()
               , function (data)
               {
                  if(data.status=='OK')
                  {
                      toastr.success(data.msg);
                      $('#loader').css('display','none');
                  }
                  if(data.status=='ERROR')
                  {
                       toastr.error(data.msg);
                       $('#loader').css('display','none');
                  }

               });
       }

   });









</script>