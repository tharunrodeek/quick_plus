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
                                    <?= trans('Manage Holidays') ?>
                                </h3>
                            </div>
                        </div>


                        <!--begin::Form-->
                        <form class="kt-form kt-form--label-right" id="element_form">
                            <div class="kt-portlet__body" style="padding: 20px !important;">
                                <div class="Msg"></div>
                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label><?= trans('Year') ?>:
                                        </label>
                                        <select id="ddl_year" class="form-control">
                                            <option option='0'>----Choose Year----</option>
                                            <?php for($i='2019';$i<='2030';$i++): ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-4">
                                        <label class=""><?= trans('Holiday Date') ?>:</label>
                                        <input type="text"   name="holi_date" class="form-control ap-datepicker"
                                                   placeholder="" id="holi_date" autocomplete="off">
                                    </div>
                                    <div class="col-lg-4">
                                        <label class=""><?= trans('Holiday Name') ?>:</label>
                                        <input type="text"   name="holi_day_name" class="form-control"
                                                   placeholder="" id="holi_day_name" autocomplete="off">
                                    </div>


                                    <div class="col-lg-3">
                                        <label style="height: 13px;"></label>
                                        <div class="input-group">
                                            <button type="button" onclick="SaveHoliday();" class="btn btn-primary">
                                                <?= trans('Create') ?>
                                            </button>&nbsp;&nbsp;
                                            <button type="button" class="btn btn-secondary btnCancel" onclick="Cancel();"><?= trans('Cancel') ?></button>
                                            <input type="hidden" id="hdnAction" name="hdnAction"/>
                                        </div>
                                    </div>
                                </div>


                        </form>
                        <!--end::Form-->
                    </div>
                </div>

                <div class="row" style="width: 100%;">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('Document Types') ?>
                                </h3>
                            </div>

                        </div>
                        <!--begin::Table-->
                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <table class="table table-bordered" id="list_documntes">
                                <thead>
                                <th><?= trans('Sl.no') ?></th>
                                <th><?= trans('Year') ?></th>
                                <th><?= trans('Date') ?></th>
                                <th><?= trans('Holiday') ?></th>
                                <th></th>
                                </thead>
                                <tbody id="list_documntes_tbody">
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="hdnEdit_id" />
<?php include "footer.php"; ?>
<script>
    $(document).ready(function () {
        Fecthdata();
    });

    function SaveHoliday() {

        var year=$("#ddl_year").val();
        var date=$("#holi_date").val();
        var holi_day_name=$("#holi_day_name").val();

        if(year=='')
        {
            toastr.error('ERROR !. Please select year');
            return false;
        }

        if(date=='')
        {
            toastr.error('ERROR !. Select holiday date');
            return false;
        }

         if(holi_day_name=='')
        {
            toastr.error('ERROR !. Enter holiday name');
            return false;
        }

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=Save_holidays",
            {'year':year,'date':date,'holi_day_name':holi_day_name,'edit_id':$("#hdnEdit_id").val()}, function (data) {
            if (data.status === 'ERROR') {
                  toastr.error(data.msg);
            }
            else
            {
                toastr.success(data.msg);
                Fecthdata();

                    $("#ddl_year").val();
                    $("#holi_date").val();
                    $("#holi_day_name").val();
                   
            }
        });
    }


    function Fecthdata()
    {
       
       $('#list_documntes').dataTable().fnDestroy();
       $('#list_documntes').DataTable({
                "bProcessing": true,
                "serverSide": true,
                "ajax":{
                    url :ERP_FUNCTION_API_END_POINT + "?method=get_saved_holidays", // json datasource
                    type: "post",  // type of method  ,GET/POST/DELETE
                    //data:{'dept_id':$(".ap_department").val(),'emp_id':$(".ap_employees").val(),'year':$(".ap-year").val(),'month':$(".ClsMonths ").val()},
                    error: function(){
                    }
                }
        });
 
    }


   $('.ap-datepicker').datepicker({
        format: 'dd-mm-yyyy',
    });


   $('#list_documntes tbody').on('click', 'td label.ClsBtnEdit', function (){
    $("html, body").animate({ scrollTop: 0 }, "slow");
        var alt_date=$(this).attr('alt_date');
        var alt_year=$(this).attr('alt_year');
        var alt_holiday_name=$(this).attr('alt_holiday_name');
        var edit_id=$(this).attr('alt');

        $("#ddl_year").val(alt_year);
        $("#holi_date").val(alt_date);
        $("#holi_day_name").val(alt_holiday_name);
        $("#hdnEdit_id").val(edit_id);


        /* AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=edit_holidays",
            {'year':$("#ddl_year").val(),'date':$("#holi_date").val(),'holi_day_name':$("#holi_day_name").val(),'edit_id':edit_id}, function (data) {
            if (data.status === 'ERROR') {
                  toastr.error(data.msg);
            }
            else
            {
                toastr.success(data.msg);
                Fecthdata();
            }
        });*/

    });


    $('#list_documntes tbody').on('click', 'td label.ClsBtnRemove', function (){
        
        var edit_id=$(this).attr('alt');

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=delete_holiday",
            {'edit_id':edit_id}, function (data) {
            if (data.status === 'ERROR') {
                  toastr.error(data.msg);
            }
            else
            {
                toastr.success(data.msg);
                Fecthdata();
            } 
        }); 

    });


   function Cancel()
   {
    location.reload();
   }

  
  

</script>