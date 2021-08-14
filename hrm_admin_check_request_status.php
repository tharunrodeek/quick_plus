<?php
include "header.php";
?>
<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">


                    <div class="kt-portlet" style="display:block;">

                         <div class="kt-portlet__head" style="display: none;">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('LIST APPROVED REQUESTS') ?>
                                </h3>
                            </div>
                        </div>
                        <!--begin::Form-->
                        <form class="kt-form kt-form--label-right" id="document_form">
                            <div class="kt-portlet__body" style="padding: 20px !important;">
                                <div class="Msg"></div>
                                <div class="form-group row">


                                    <div class="col-sm">

                                        <div class="row">
                                            <div class="col-sm">
                                                <label><?= trans('DEPARTMENT') ?>:</label>
                                                <div class="input-group">
                                                    <select class="form-control kt-select2 ap-select2 ap_department"
                                                            name="ddl_department" >
                                                        <option value="">SELECT</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm">
                                                <label style="height: 13px;"></label>
                                                <div class="input-group">
                                                    <button type="button" onclick="ListSummaryRequest();" class="btn btn-primary">
                                                        <?= trans('Submit') ?>
                                                    </button>&nbsp;&nbsp;

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-sm">
                                        <table border='1px solid #ccc;' style='width:100%;' id="list_summary">
                                            <thead>
                                            <tr>
                                                <th style='padding: 8px;text-align: center;'>Request Name</th>
                                                <th style='padding: 8px;text-align: center;'>Pending Count</th>
                                            </tr>
                                            </thead>
                                            <tbody id="list_summary_tbody" style="text-align: center;">
                                            </tbody>
                                        </table>
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
                                    <?= trans('View Request Details') ?>
                                </h3>
                            </div>

                        </div>
                        <!--begin::Table-->
                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <div id="other_requests"></div>

                            <table class='table' style='width:100%;display:none;' id="view_request_details" >
                                <thead class=\"thead-dark\">
                                <tr>
                                    <th style='padding: 8px;text-align: center;'>RequestID</th>
                                    <th style='padding: 8px;text-align: center;'>Employee Name</th>
                                    <th style='padding: 8px;text-align: center;'>Request Date</th>
                                    <th style='padding: 8px;text-align: center;'>Comments</th>
                                    <th style='padding: 8px;text-align: center;'>Current Level</th>
                                    <th style='padding: 8px;text-align: center;'>Role</th>
                                    <th style='padding: 8px;text-align: center;'>Pending Levels</th>
                                </tr>
                                </thead>

                                <tbody id="view_request_details_tbody" style="text-align: center;">
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<input type="hidden" id="hdndept_id" />
<input type="hidden" id="hdnedit" />
<input type="hidden" id="hdntype_id" />
<input type="hidden" id="hdn_id" />
<input type="hidden" id="hdn_alt_level" />
<input type="hidden" id="hdn_changed_type_val" />

<div id='divToPrint' style='display:none;'></div>
<?php include "footer.php"; ?>
<script>

    $(document).ready(function () {

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_department',null,function () {
                $('.ap_department').prepend('<option value="0">------All Department-------</option>');
                $('.ap_department').val('0');
            });
        });

    });


   function ListSummaryRequest()
   {
       $('#view_request_details').css('display','none');

          $('#list_summary').css('display','table');
           $('#list_summary').dataTable().fnDestroy();

           $('#list_summary').DataTable({
               "bProcessing": true,
               "serverSide": true,
               "ajax":{
                   url :ERP_FUNCTION_API_END_POINT + "?method=group_requests_dept_summary", // json datasource
                   type: "post",  // type of method  ,GET/POST/DELETE
                   data:{'dept_id':$('.ap_department ').val(),'req_id':0},
                   error: function(){
                   }
               }
           });

      /* AxisPro.APICall('POST',
           ERP_FUNCTION_API_END_POINT + "?method=group_requests_dept_summary",{'dept_id':$('.ap_department ').val(),'req_id':0},
           function (data) {
               if(data.status=='OK')
               {
                   toastr.success(data.msg);
                   fetchDataTotable();
               }
               else if(data.status=='FAIL')
               {
                   toastr.error(data.msg);
               }

           }
       );*/


        
   }


    $('#list_summary tbody').on('click', 'td .clsTotalCnt', function ()
    {
        $('html,body').animate({ scrollTop: 9999 }, 'slow');
        var request_ids=$(this).attr('alt');
        var request_type_id=$(this).attr('alt_request_id');

        $('#view_request_details').css('display','table');
        $('#view_request_details').dataTable().fnDestroy();

        $('#view_request_details').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :ERP_FUNCTION_API_END_POINT + "?method=get_request_details", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                data:{'req_pk_ids':request_ids,'req_type_id':request_type_id},
                error: function(){
                }
            }
        });

    });


</script>