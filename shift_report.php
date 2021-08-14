<?php
include "header.php";
?>
<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">


                  <?php
                      
                      $flag='';

                      if(in_array($_SESSION['wa_current_user']->access,[2]))
                      {
                        $flag=1;
                      }

                   if($flag==1){

                  ?>




                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('ASSIGN EMPLOYEE TO SHIFT') ?>

                                </h3>
                            </div>
                        </div>

                        <!--  <form class="kt-form kt-form--label-right" id="leave_form">-->
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

                                <!--  <div class="col-lg-3">
                                        <label><?= trans('EMPLOYEE') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_employees"
                                                    name="leave_type" >
                                                <option value="">All Employees</option>
                                            </select>
                                        </div>
                                    </div> -->
                                <div class="col-lg-3">
                                    <?php
                                    include_once($path_to_root . "/API/API_HRM_Call.php");
                                    $return=new API_HRM_Call();
                                    $shift_data=$return->getShifts();
                                    ?>
                                    <label><?= trans('SHIFT') ?>:</label>
                                    <div class="input-group">
                                        <select class="form-control kt-select2 ap-select2 ap_shift"
                                                name="shift_type" >
                                            <option value="0">Select Shift</option>
                                            <?php
                                            foreach($shift_data as $s):
                                                ?>
                                                <option value="<?php echo $s['id']; ?>"><?php echo $s['description']; ?></option>
                                            <?php
                                            endforeach;
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <label><?= trans('START DATE') ?>:</label>
                                    <div class="input-group">
                                        <input type="text" id="ShiftStartDate" name="ShiftStartDate" autocomplete="off"  class="form-control ap-datepicker"/>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <label><?= trans('END DATE') ?>:</label>
                                    <div class="input-group">
                                        <input type="text" id="ShiftEndDate" name="ShiftEndDate" autocomplete="off" class="form-control ap-datepicker"/>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <div class="input-group" style="margin-top: 13%;">
                                        <button id="btnView" name="btnView" class="btn btn-primary">Generate Report</button>
                                    </div>
                                </div>

                            </div>

                            <!-- </form>-->

                        </div>

                    </div>

                <?php } ?>

                    <div class="row" style="width: 100%;overflow: scroll;">

                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <?= trans('SHIFT DETAILS FOR NEXT WEEK') ?>
                                    </h3>
                                </div>

                            </div>

                            <div class="table-responsive table_payout" style="padding: 7px 7px 7px 7px;">

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
        .selected_color{
            background-color:lightblue;
        }

        #list_employees tbody tr{
            cursor:pointer;
        }
    </style>
    <input type="hidden" id="hdnPageCnt" name="hdnPageCnt" />
    <?php include "footer.php"; ?>
    <script>
        $(document).ready(function () {

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
                AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_department',null,function () {
                    $('.ap_department').prepend('<option value="0">Select Department</option>');
                    $('.ap_department').val('0');
                });
            });

              fetchDataTotable();


        });
        $('.ap-datepicker').datepicker({
            format: 'dd-mm-yyyy'
        });

       
         
        $(".ap_department").change(function() {

            if($(this).val()!='0')
            {
                $(".btnResetSlips").css('display','block');
            }
            else
            {
                $(".btnResetSlips").css('display','none');
            }
        });

        /*$(".ap_shift").change(function()
        {
            fetchDataTotable();
        });
    */

        $('#btnView').click(function()
        {
            if($('#ShiftStartDate').val()=='')
            {
                toastr.error('Select START DATE');
            }
            else if($('#ShiftEndDate').val()=='')
            {
                toastr.error('Select END DATE');
            }
            else if($('.ap-select2').val()=='0')
            {
                toastr.error('Select SHIFT');
            }
            else if($('.ap_department').val()=='0')
            {
                toastr.error('Select Department');
            }
            else
            {
                fetchDataTotable();
            }
        });


        function fetchDataTotable()
        {
           /* //alert('Enter');
            $('#list_employees').dataTable().fnDestroy();

            $('#list_employees').DataTable({
                "bProcessing": true,
                "serverSide": true,
                "iDisplayLength": 100,
                "ajax":{
                    url :ERP_FUNCTION_API_END_POINT + "?method=list_employees_for_shift", // json datasource
                    type: "post",  // type of method  ,GET/POST/DELETE
                    data:{'dept_id':$(".ap_department").val(),'shift_id':$(".ap_shift").val(),'f_date':$("#ShiftStartDate").val(),'t_date':$("#ShiftEndDate").val()},
                    error: function(){
                    }
                }
            });*/


            AxisPro.APICall('POST',ERP_FUNCTION_API_END_POINT + "?method=prepare_shift_report"
                ,'dept_id='+$(".ap_department").val()+"&pagecnt="+$("#hdnPageCnt").val()
                +"&shift_id="+$(".ap_shift").val()+"&frmDate="+$("#ShiftStartDate").val()+"&toDate="+$("#ShiftEndDate").val(),
                function (resdata) {
                    $(".table_payout").html(resdata);


                    $(".page-link").click(function()
                    {
                        var alt=$(this).attr('alt');
                        $("#hdnPageCnt").val(alt);

                        fetchDataTotable();
                    });
                });

        }







    </script>
