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
                                    <?= trans('REQUEST DOCUMENT') ?>
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body" style="padding: 5px !important;">
                            <div class="row">
                                <div class="col-md-4">
                                    <form class="kt-form kt-form--label-right" id="document_form">


                                                <div >
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
                                                <div>
                                                    <label class=""><?= trans('Reason') ?>:</label>

                                                    <textarea class="txt_reason form-control" name="txt_reason"   style="    height: 40px;"></textarea>
                                                </div>
                                                <div>
                                                    <label class=""><?= trans('Document Required Date') ?>:</label>
                                                    <input type="text" class="txtIssueDate form-control ap-datepicker" name="txtIssueDate" autocomplete="off"  />
                                                </div>


                                                <div>
                                                    <label style="height: 13px;"></label>
                                                    <div class="input-group">
                                                        <button type="button" onclick="SaveDocument(0);" class="btn btn-primary">
                                                            <?= trans('Request') ?>
                                                        </button>&nbsp;&nbsp;
                                                        <button type="button" class="btn btn-secondary btnCancel" onclick="Cancel();"><?= trans('Cancel') ?></button>
                                                        <input type="hidden" id="hdnAction" name="hdnAction"/>
                                                    </div>
                                                </div>


                                    </form>
                                </div>
                                <div class="col-md-8">
                                    <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                                        <table class="table table-bordered" id="list_documntes">
                                            <thead>

                                            <th><?= trans('Document Type') ?></th>
                                            <th><?= trans('Reason') ?></th>
                                            <th><?= trans('Doc. Required Date') ?></th>
                                            <th><?= trans('File') ?></th>
                                            <th></th>
                                            <th></th>
                                            </thead>
                                            <tbody id="list_documntes_tbody">
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>






                        <!--begin::Form-->


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
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td>Doc.Type:</td><td> <select class="ddl_Edit_DocTypes form-control" >
                                <option value="0">--SELECT--</option>
                                <?php foreach ($leave_types as $type): ?>
                                    <option value="<?php echo $type['id']?>"><?php echo $type['description']; ?></option>
                                <?php endforeach; ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td>Reason:</td><td><textarea class="txt_Edit_reason form-control" name="txt_reason"   style="height: 40px;"></textarea></td>
                    </tr>
                    <tr>
                        <td>Doc. Required Date :</td>
                        <td>
                            <input type="text" class="txt_edit_IssueDate form-control ap-datepicker" name="txtIssueDate" autocomplete="off"  />
                        </td>
                    </tr>

                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default ClsReq_update" data-dismiss="modal">Update </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="hidden" id="hdn_pk_id" />
            </div>
        </div>

    </div>
</div>


<input type="hidden" id="hdndept_id" />
<input type="hidden" id="hdnedit" />
<?php include "footer.php"; ?>
<script>
    $(document).ready(function () {
        Loaddept();
        Fecthdata();
    });

    $('.ap-datepicker').datepicker({
        format: 'dd-mm-yyyy',
    });

    $(".ap_department").change(function() {
        loadEmployees();
    });


    function SaveDocument(type) {
        var flag='';

        if(type=='0')
        {
            if($(".ddlDocTypes").val()=='0')
            {
                toastr.error('Select document type');
                flag='1';
            }
            else if($("#txt_reason").val()=='')
            {
                toastr.error('Enter the reason');
                flag='1';
            }
            else if($("#txtIssueDate").val()=='')
            {
                toastr.error('Pick a date for when u required the document');
                flag='1';
            }
        }



        if(type=='1')
        {
            if($(".ddl_Edit_DocTypes").val()=='0')
            {
                toastr.error('Select document type');
                flag='1';
            }
            else if($("#txt_Edit_reason").val()=='')
            {
                toastr.error('Enter the reason');
                flag='1';
            }
            else if($("#txt_edit_IssueDate").val()=='')
            {
                toastr.error('Pick a date for when u required the document');
                flag='1';
            }
        }





        if(flag=='')
        {
            var form = $("#document_form");
            var params = form.serializeArray();
            var formData = new FormData();

            if(type=='0')
            {
                formData.append('doc_type',$(".ddlDocTypes").val());
                formData.append('reason',$(".txt_reason").val());
                formData.append('doc_required_date',$(".txtIssueDate").val());
                formData.append('saving_type',type);
                formData.append('edit_pk_id',$('#hdn_pk_id').val());
            }
            else
            {
                formData.append('doc_type',$(".ddl_Edit_DocTypes").val());
                formData.append('reason',$(".txt_Edit_reason").val());
                formData.append('doc_required_date',$(".txt_edit_IssueDate").val());
                formData.append('saving_type',type);
                formData.append('edit_pk_id',$('#hdn_pk_id').val());
            }


            $.ajax({
                type: "POST",
                url: ERP_FUNCTION_API_END_POINT+"?method=request_document",
                data: formData,
                contentType: false,
                processData: false,
                success: function (result) {
                    var res = JSON.parse(result);
                    if(res.status=='OK')
                    {
                        toastr.success(res.msg);
                        location.reload();
                    }
                    if(res.status=='FAIL')
                    {
                        toastr.error(res.msg);
                    }
                }
            });
        }

    }


    function Fecthdata()
    {
        $('#list_documntes').dataTable().fnDestroy();
        $('#list_documntes').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :ERP_FUNCTION_API_END_POINT + "?method=list_request", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                error: function(){
                }
            }
        });
    }


    $('#list_documntes tbody').on('click', 'td label.ClsEdit', function (){
        var id=$(this).attr('alt_pk_id');
        var doc_id=$(this).attr('alt_doc_type');
        var alt_reason=$(this).attr('alt_reason');
        var alt_required_date=$(this).attr('alt_required_date');
        $('#hdn_pk_id').val(id);

        $(".ddl_Edit_DocTypes").val(doc_id);
        $(".txt_Edit_reason").val(alt_reason);
        $(".txt_edit_IssueDate").val(alt_required_date);



    });

    $('.ClsReq_update').click(function()
    {
        SaveDocument(1);
    });


    $('#list_documntes tbody').on('click', 'td label.ClsRemove', function (){
        var id=$(this).attr('alt_pk_id');

        var confrim=confirm('Are you really want to delete the record?');
        if(confrim==true) {

            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=remove_request", 'remove_id='+id, function (data) {
                if (data.status == 'Error') {
                    toastr.error(data.msg);
                }
                else
                {
                    toastr.success(data.msg);
                    Fecthdata();
                }
            });

        }
    });

    function Cancel()
    {
        if($("#hdnedit").val()!='')
        {
            var confrm=confirm('Exit form edit mode??');
            if(confrm==true)
            {
                location.reload();
            }
        }
        else
        {
            location.reload();
        }


    }

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

</script>