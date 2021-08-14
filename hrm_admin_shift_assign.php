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

                                <div class="col-lg-3">
                                    <label><?= trans('EMPLOYEE') ?>:</label>
                                    <div class="input-group">
                                        <select class="form-control kt-select2 ap-select2 ap_employees"
                                                name="leave_type" >
                                            <option value="">All Employees</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- <div class="col-lg-3"> -->
                                    <?php
                                     include_once($path_to_root . "/API/API_HRM_Call.php");
                                     $return=new API_HRM_Call();
                                     $shift_data=$return->getShifts(); 
                                    ?>
                                    
                             <!--    </div> -->

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
                                        <button id="btnView" name="btnView" class="btn btn-primary">View</button>
                                    </div>
                                </div>



                            </div>

                            <!-- </form>-->

                        </div>



                    </div>
                    <style>
                        .square {
     height: 26px;
    width: 26px;
 
}
                    </style>
                   
                    <div class="row clsAvilableShifts" style="width: 100%;">
                        <div class="kt-portlet" style="display: contents;">

                           <?php
                           $icon_color=''; 
                           foreach($shift_data as $s):
          
                           $icon_color='style="background-color:'.$s['shift_color'].' "';
                           ?>
                            <div class="square" <?php echo $icon_color; ?>></div><?php echo $s['description']; ?>&nbsp;
                           <?php endforeach; ?>
                        </div>

                    </div>

                    <div class="row" style="width: 100%;overflow: scroll;height: 450px;">

                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <div class="col-md-4">
                                    <h3 class="kt-portlet__head-title">
                                        <?= trans('LIST OF EMPLOYEES') ?>
                                    </h3>
                                </div>
                                <div class="col-md-5">
                                    <label><?= trans('SHIFT') ?>:</label>  
                                      <div class="input-group">
                                        <select class="form-control kt-select2 ap-select2 ap_shift"
                                                name="shift_type" >
                                            <option value="0">---All Shift---</option>
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

                                 <div class="col-md-5" style="margin-top:5%;">
                                    <button type="button" onclick="fetchDataTotable();" "
                                            class="btn btn-primary  ">
                                        <?= trans('Filter') ?>
                                    </button>
                                 </div>

                                    <div class="col-md-5" style="margin-top:5%;margin-left: -26%;">
                                        <button type="button" onclick="AssignShift();" style="height: 40px;display: none;"
                                                class="btn btn-primary btnResetSlips">
                                            <?= trans('Assign Employee') ?>
                                        </button>

                                        </div>







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
        /*.highlighted{
            background-color:lightblue;
        }*/

        #list_employees tbody tr{
            cursor:pointer;
        }


    </style>
    <input type="hidden" id="hdnPageCnt" name="hdnPageCnt" />
    <script
            src="https://code.jquery.com/jquery-1.3.2.min.js"
    ></script>
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
        /**************************PAST Days disabled ,because can't assign shift for past days************/
        /*  var nowDate = new Date();
          var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);
          $('.ap-datepicker').datepicker({
              format: 'dd-mm-yyyy',
              startDate: today,
              todayHighlight: true
          });*/
        /**************************************************END*********************************************/
        $(".ap_department").change(function() {

            if($(this).val()!='0')
            {
                $(".btnResetSlips").css('display','block');
               // $(".clsAvilableShifts").css('display','contents');
            }
            else
            {
                $(".btnResetSlips").css('display','none');
               // $(".clsAvilableShifts").css('display','none');
            }

            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+$(".ap_department").val(), function (data) {
                AxisPro.PrepareSelectOptions(data, 'Empid', 'Emp_name', 'ap_employees',null,function () {
                    $('.ap_employees').prepend('<option value="0">All Employees</option>');
                    $('.ap_employees').val('0');
                });
            });
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

            AxisPro.APICall('POST',ERP_FUNCTION_API_END_POINT + "?method=list_shift_prepare"
                ,'dept_id='+$(".ap_department").val()+"&pagecnt="+$("#hdnPageCnt").val()
                +"&shift_id="+$(".ap_shift").val()+"&frmDate="+$("#ShiftStartDate").val()+"&toDate="+$("#ShiftEndDate").val()+"&emp_id="+$('.ap_employees').val(),
                function (resdata) {
                //alert(resdata);
                    $(".table_payout").html(resdata);

                    $(function () {

                        var isMouseDown = false,
                            isHighlighted;
                        $("#tblShift .ClsShiftRow");

                            /*.mousedown(function () {
                                isMouseDown = true;
                                $(this).toggleClass("highlighted");
                                isHighlighted = $(this).hasClass("highlighted");
                                return false; // prevent text selection
                            })*/
                           /* .mouseover(function () {
                                if (isMouseDown) {
                                    $(this).toggleClass("highlighted", isHighlighted);
                                }
                            })*/
                            /*.bind("selectstart", function () {
                                return false;
                            });*/



                        /*$(document)
                            .mouseup(function () {
                                isMouseDown = false;
                            });*/
                    });


                    $(".page-link").click(function()
                    {
                        var alt=$(this).attr('alt');
                        $("#hdnPageCnt").val(alt);

                        fetchDataTotable();
                    });
                });

        }

        function AssignShift()
        {
            var dept_id=$(".ap_department").val();
            //var shift_id=$(".ap_shift").val();
            var shift_start_date=$("#ShiftStartDate").val();
            var shift_end_date=$("#ShiftEndDate").val();
            var flag=true;
            var checked_empids=[];


            if(dept_id=='0')
            {
                toastr.error('ERROR!! Please select department');
                flag=false;
            }

           /* if(shift_id=='0')
            {
                toastr.error('ERROR!! Please select shift.');
                flag=false;
            }*/
            if(shift_start_date=='')
            {
                toastr.error('ERROR!! Please select shift start date.');
                flag=false;
            }
            if(shift_end_date=='')
            {
                toastr.error('ERROR!! Please select shift end date.');
                flag=false;
            }

            if(shift_start_date==shift_end_date)
            {
                toastr.error('ERROR!! Shift start date and end date cant be same.');
                flag=false;
            }

            /*************SAVING checked empIds to and array and passing it************/
            var k=0;
            var date_range=[];
            var n=0;
            var h=0;
            var shift_id='';
            jQuery(".chkEmp_select:checked").each(function(){

                var tot_date_cnt=$(this).attr('al_tot_cnt');

                for(n=0;n<=tot_date_cnt;n++)
                {

                    if($('#ddl_sift_id_'+$(this).val()+'_'+n).val()!='')
                    {
                        var shift_date=$('#td_sift_id_'+$(this).val()+'_'+n).attr('alt');
                        var shift_id=$('#ddl_sift_id_'+$(this).val()+'_'+n).val();

                        date_range.push({dates:shift_date,shift_ids:shift_id});
                        h++;
                    }

                }

               
 
                checked_empids.push({Empl_id:$(this).val(),s_date:date_range});
                k++;
                date_range=[];
            });

           

            if(checked_empids.length=='0')
            {
                toastr.error('ERROR!! Select employee before clicking assign button.');
                flag=false;
            }


            if(flag==true)
            {
                $("#loader").css('display','block');
                AxisPro.APICall('POST',
                    ERP_FUNCTION_API_END_POINT + "?method=assign_shift_to_employee",{'dept_id':$(".ap_department").val(),'shift_id':shift_id,'shift_start_date':shift_start_date,
                        'shift_end_date':shift_end_date,'check_empids':checked_empids,'total_rows':h},
                    function (data) {
                        $("#loader").css('display','none');
                        if(data.status=='OK')
                        {
                            toastr.success(data.msg);
                             fetchDataTotable();
                        }
                        if(data.status=='FAIL')
                        {
                            toastr.error(data.msg);
                        }

                    }
                );
            }
        }











    </script>
