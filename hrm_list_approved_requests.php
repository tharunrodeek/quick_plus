<?php
include "header.php";
?>
<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">


                    <div class="kt-portlet" style="display:block;">

                         <div class="kt-portlet__head">
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
                                    <div class="col-lg-4">
                                          <label>Choose Type:</label>
                                            <select id="ddl_type" name="ddl_type" class="form-control">
                                                <option value="0">--SELECT--</option>
                                                <!--<option value="1">Passport Request</option>
                                                <option value="2">Certificate</option>
                                                <option value="3">NOC Request</option>
                                                <option value="4">Assest Request</option>-->

                                              <option value="1">Leave Request</option> 
                                                <option value="2">Certificate</option>
                                                <option value="3">Passport</option>
                                                <option value="4">loan</option>
                                                <option value="5">NOC</option>
                                                <!-- <option value="6">Asset Request</option>
                                                <option value="7">Asset Return Request</option> -->


                                            </select>

                                    </div>

                                    <div class="col-lg-2">
                                        <label><?= trans('Department') ?>:
                                        </label>
                                        <select class="form-control kt-select2 ap-select2 ap_department"
                                                name="ddl_department">
                                            <option value="">SELECT</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-2">
                                        <label><?= trans('Status') ?>:
                                        </label>
                                        <select class="form-control kt-select2 ap-select2 ap_request_status"
                                                name="ddl_request_status">
                                            <option value="">---ALL--</option>
                                            <option value="1">Approved</option>
                                            <option value="0">Pending</option>
                                            <option value="2">Rejected</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-2">
                                       <input type="button" class="btn-success btnSearch" value="SEARCH" style="    padding: 7px;
    margin-top: 14%;"/>
                                    </div>
                                    

                                    <div class="col-lg-2" style="display: none;">
                                        <label><?= trans('Document Type') ?>:
                                        </label>
                                        <?php
                                        include_once($path_to_root . "/API/API_HRM_Call.php");
                                        $call_obj=new API_HRM_Call();
                                        $leave_types=$call_obj->get_documents();
                                        ?>
                                         <select class="ddlDocTypes form-control" >
                                             <option value="0">--SELECT--</option>
                                             <?php foreach ($leave_types as $type): ?>
                                                 <option value="<?php echo $type['id']?>"><?php echo $type['description']; ?></option>
                                             <?php endforeach; ?>
                                         </select>
                                    </div>
                                    <div class="col-lg-4" style="display: none;">
                                        <label class=""><?= trans('Reason') ?>:</label>

                                        <textarea class="txt_reason form-control" name="txt_reason"   style="    height: 40px;"></textarea>
                                    </div>
                                    <div class="col-lg-2" style="display: none;">
                                        <label class=""><?= trans('Document Required Date') ?>:</label>
                                        <input type="text" class="txtIssueDate form-control ap-datepicker" name="txtIssueDate" autocomplete="off"  />
                                    </div>
                                    <!--<div class="col-lg-2" >
                                        <label class=""><?/*= trans('Expiry Date') */?>:</label>
                                        <input type="text" id="txtExpiry" name="txtExpiry"  autocomplete="off"class="form-control ap-datepicker" />
                                    </div>-->

                                    <!--<div class="col-lg-2" style="padding: 1%;">
                                        <label class=""><?/*= trans('Attach File') */?>:</label>
                                        <input type="file" id="fileToUpload" name="fileToUpload">
                                    </div>-->


                                    <div class="col-lg-3" style="display: none;">
                                        <label style="height: 13px;"></label>
                                        <div class="input-group">
                                            <button type="button" onclick="SaveDocument(0);" class="btn btn-primary">
                                                <?= trans('Request') ?>
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
                                    <?= trans('List Requests') ?>
                                </h3>
                            </div>

                        </div>
                        <!--begin::Table-->
                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <div id="other_requests"></div>

                            <table class='table' style='width:100%;display:none;' id="list_leave" >
                                <thead class=\"thead-dark\">
                                <tr>
                                    <th style='padding: 8px;text-align: center;'>Ref.No</th>
                                    <th style='padding: 8px;text-align: center;'>Emp. Name</th>
                                    <th style='padding: 8px;text-align: center;'>Request Date (Start Date - End Date)</th>
                                    <th style='padding: 8px;text-align: center;'>Request Status</th>
                                    <th style='padding: 8px;text-align: center;'>Leave Name</th>
                                    <th style='padding: 8px;text-align: center;'>view Doc.</th>
                                    <th style='padding: 8px;text-align: center;'>Approved By</th>
                                </tr>
                                </thead>

                                <tbody id="list_leave_tbody" style="text-align: center;">
                                </tbody>
                            </table>

<!----------------------LEAVE----------------------------------->
                            <table class='table' style='width:100%;' id="list_documents">
                            <thead class=\"thead-dark\">

                            <tr>
                                <th style='padding: 8px;text-align: center;'>Ref.No</th>
                                    <th style='padding: 8px;text-align: center;'>Emp. Name</th>
                                    <th style='padding: 8px;text-align: center;'>Type Of Request</th>
                                    <th style='padding: 8px;text-align: center;'>Request Date</th>
                                    <th style='padding: 8px;text-align: center;'>Status</th>
                                    <th style='padding: 8px;text-align: center;'>Comment</th>
                                    <th style='padding: 8px;text-align: center;'>Approved By</th>
                                    <th style='padding: 8px;text-align: center;'></th> 
                            </tr>

                            </thead>

                            <tbody id="list_documents_tbody" style="text-align: center;">
                            </tbody>
                            </table>

                            



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="font-weight: bold;color:black;">HR Status Update</h4>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
        <form class="kt-form kt-form--label-right" id="FrmUpdate">
            <div class="modal-body">

                <table>
                    <tr>
                        <td>Choose Status</td>
                            <td>:</td>
                        <td><select id="ddl_out_status" name="ddl_out_status" class="form-control">
                            <option value="0">---Select---</opion>
                            <option value='1'>Given</option>
                            <option value='2'>Return Back</option>
                         </select></td>
                    </tr>
                    <tr>
                        <td>Attach Document</td>
                        <td>:</td>
                        <td>
                           <input type="file" name="documentFile" id="documentFile" /> 
                        </td>
                    </tr>

                    <tr>
                        <td>Comment</td>
                        <td>:</td>
                        <td>
                            <textarea name="hr_comment" id="hr_comment" class="form-control"></textarea>
                        </td>
                    </tr>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default ClsReq_update" data-dismiss="modal">Update </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="hidden" id="hdn_request_id" />
            </div>
        </form>


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
        Loaddept();
    });

    $('.ap-datepicker').datepicker({
        format: 'dd-mm-yyyy',
    });

    $(".ap_department").change(function() {
        loadEmployees();
    });


   function Loaddept()
    {
        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'id', 'description', 'ap_department',null,function () {
                $('.ap_department').prepend('<option value="0">Select Department</option>');

                if($("#hdndept_id").val()=='')
                {
                    $('.ap_department').val('0');
                }
                else
                {
                    $('.ap_department').val($("#hdndept_id").val());
                    loadEmployees();
                }

            });
        });
    }

    function loadEmployees()
    {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+$(".ap_department").val(), function (data) {
            AxisPro.PrepareSelectOptions(data, 'Empid', 'Emp_name', 'ap_employees',null,function () {
                $('.ap_employees').prepend('<option value="0">SELECT Employees</option>');

                if($("#hdnemp_id").val()=='')
                {
                    $('.ap_employees').val('0');
                }
                else
                {
                    $('.ap_employees').val($("#hdnemp_id").val());
                }
            });
        });
    }

    $('#ddl_type').change(function()
    {

        $('#hdn_changed_type_val').val($('#ddl_type').val());
        //databind($('#hdn_changed_type_val').val());

    });

    $('.btnSearch').click(function()
    {
        databind();
    });




   function databind()
   {
       var type_id=$('#ddl_type').val();
       var dept=$('.ap_department').val();
       var filter_status=$('.ap_request_status').val();


       if(dept=='0' || type_id=='0')
       {
           toastr.error('Choose Department and Request type');
       }
       else
       {
           $('#list_leave').css('display','none');
           $('#list_documents').css('display','none');
           $('#list_leave').dataTable().fnDestroy();
           $('#list_documents').dataTable().fnDestroy();

           if(type_id=='1')
           {
               $('#list_leave').css('display','table');


               $('#list_leave').DataTable({
                   "bProcessing": true,
                   "serverSide": true,
                   "ajax":{
                       url :ERP_FUNCTION_API_END_POINT + "?method=list_all_approved_requests", // json datasource
                       type: "post",  // type of method  ,GET/POST/DELETE
                       data:{'type_id':type_id,'dept':dept,'filter_status':filter_status},
                       error: function(){
                       }
                   }
               });
           }
           else
           {
               $('#list_documents').css('display','table');


               $('#list_documents').DataTable({
                   "bProcessing": true,
                   "serverSide": true,
                   "ajax":{
                       url :ERP_FUNCTION_API_END_POINT + "?method=list_all_approved_requests", // json datasource
                       type: "post",  // type of method  ,GET/POST/DELETE
                       data:{'type_id':type_id,'dept':dept,'filter_status':filter_status},
                       error: function(){
                       }
                   }
               });
           }
       }





     

        
   }


    $('#list_leave tbody').on('click', 'td .btnupdate', function ()
    {
        var type_id=$(this).attr('alt_type');
        $('#hdn_request_id').val($(this).attr('alt_req_id'));
       $('#myModal').modal('show');
    });

    $('.ClsReq_update').click(function()
    {
        
       var hr_status=$('#ddl_out_status').val();
       if(hr_status=='0')
       {
         toastr.error('Choose Status');
       }
       else
       {


           var form = $("#FrmUpdate");
           var params = form.serializeArray();
           var files = $("#documentFile")[0].files;
           var formData = new FormData();


           for (var i = 0; i < files.length; i++) {
                formData.append('documentFile', files[i]);
            }

         formData.append('type',$("#ddl_type").val());
         formData.append('req_id',$('#hdn_request_id').val());
         formData.append('status',hr_status);
         formData.append('comment',$('#hr_comment').val());

        

            $.ajax({
                type: "POST",
                url: ERP_FUNCTION_API_END_POINT+"?method=update_hr_status",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    var res = JSON.parse(data);
                    if(res.status=='OK')
                    {
                        toastr.success(res.msg);
                        $('#hdn_changed_type_val').val($('#ddl_type').val());
                         databind($('#hdn_changed_type_val').val());
                    }
                    else
                    {
                        toastr.error(res.msg);
                    }
                }
            });



       }


    });



    $('#list_leave tbody').on('click', 'td .btnprint', function ()
    {

        var id=$(this).attr('alt');
        var action=$(this).attr('alt_action');
        var alt_type=$(this).attr('alt_type');
        var alt_req_id=$(this).attr('alt_req_id');
        var alt_sub_frm_id=$(this).attr('alt_sub_frm_id');
         

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=view_print",
                    {type:alt_type,req_id:alt_req_id,sub_frm_id:alt_sub_frm_id}, function (data) 
        {

                   $("#divToPrint").html(data.msg);
                     var prtContent = document.getElementById('divToPrint');
                     var WinPrint = window.open('', '', 'letf=100,top=100,width=600,height=80');

                     WinPrint.document.write(prtContent.innerHTML);
                     setTimeout(function(){ WinPrint.focus();
                       WinPrint.print();
                     WinPrint.close();
                         }, 1000);        
        }); 

    });

</script>