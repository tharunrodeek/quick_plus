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
                                    <?= trans('MANAGE EMPLOYEE DOCUMENTS') ?>
                                </h3>
                            </div>
                        </div>

                        <!--begin::Form-->
                        <form class="kt-form kt-form--label-right" id="employee_form">
                            <div class="kt-portlet__body" style="padding: 20px !important;">
                                <div class="Msg"></div>
                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label><?= trans('Department') ?>:
                                        </label>
                                        <select class="form-control kt-select2 ap-select2 ap_department"
                                                name="ddl_department">
                                            <option value="">SELECT</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3">
                                        <label><?= trans('EMPLOYEE') ?>:</label>
                                        <div class="input-group">
                                            <select class="form-control kt-select2 ap-select2 ap_employees"
                                                    name="leave_type" >
                                                <option value="">SELECT Employees</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <label><?= trans('Document Type') ?>:
                                        </label>
                                        <?php
                                        include_once($path_to_root . "/API/API_HRM_Call.php");
                                        $call_obj=new API_HRM_Call();
                                        $leave_types=$call_obj->get_documents();
                                        ?>
                                         <select id="ddlDocTypes" class="form-control">
                                             <option value="0">--SELECT--</option>
                                             <?php foreach ($leave_types as $type): ?>
                                                 <option value="<?php echo $type['id']?>"><?php echo $type['description']; ?></option>
                                             <?php endforeach; ?>
                                         </select>
                                    </div>
                                    <div class="col-lg-2">
                                        <label class=""><?= trans('Document Title') ?>:</label>
                                        <input type="text" id="txtTitle" name="txtTitle" class="form-control" />
                                    </div>
                                    <div class="col-lg-2">
                                        <label class=""><?= trans('Issue Date') ?>:</label>
                                        <input type="text" id="txtIssueDate" name="txtIssueDate" autocomplete="off" class="form-control ap-datepicker" />
                                    </div>
                                    <div class="col-lg-2" style="padding: 1%;">
                                        <label class=""><?= trans('Expiry Date') ?>:</label>
                                        <input type="text" id="txtExpiry" name="txtExpiry"  autocomplete="off"class="form-control ap-datepicker" />
                                    </div>

                                    <div class="col-lg-2" style="padding: 1%;">
                                        <label class=""><?= trans('Attach File') ?>:</label>
                                        <input type="file" id="fileToUpload" name="fileToUpload">
                                    </div>


                                    <div class="col-lg-3">
                                        <label style="height: 13px;"></label>
                                        <div class="input-group">
                                            <button type="button" onclick="SaveDocument();" class="btn btn-primary">
                                                <?= trans('Save Data') ?>
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
                                    <?= trans('Employee Documents') ?>
                                </h3>
                            </div>

                        </div>
                        <!--begin::Table-->
                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <table class="table table-bordered" id="list_documntes">
                                <thead>
                                <th><?= trans('Department') ?></th>
                                <th><?= trans('Emp.name') ?></th>
                                <th><?= trans('Document Type') ?></th>
                                <th><?= trans('Document Title') ?></th>
                                <th><?= trans('Issue Date') ?></th>
                                <th><?= trans('Expiry Date') ?></th>
                                <th><?= trans('File Name') ?></th>
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
        </div>
    </div>
</div>
<input type="hidden" id="hdndept_id" />
<input type="hidden" id="hdnemp_id" />
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


    function SaveDocument() {

        if($('.ap_department').val()=='0')
        {
            toastr.error('SELECT Department');
        }
        else if($('.ap_employees').val()=='0')
        {
            toastr.error('SELECT Employee');
        }
        else if($('#ddlDocTypes').val()=='0')
        {
            toastr.error('SELECT Document type');
        }
        else if($("#txtTitle").val()=='')
        {
            toastr.error('Enter title');
        }
        else if($("#txtIssueDate").val()=='')
        {
            toastr.error('Enter issue Date');
        }
        else if($("#txtExpiry").val()=='')
        {
            toastr.error('Enter expiry date');
        }
        else
        {
            var form = $("#employee_form");
            var params = form.serializeArray();
            var files = $("#fileToUpload")[0].files;
            var formData = new FormData();
            for (var i = 0; i < files.length; i++) {
                formData.append('fileToUpload', files[i]);
            }
            $(params).each(function(index, element) {
                formData.append(element.name, element.value);
            });

            formData.append('dept_id',$('.ap_department').val());
            formData.append('empl_id',$('.ap_employees').val());
            formData.append('doc_type',$('#ddlDocTypes').val());
            formData.append('title',$("#txtTitle").val());
            formData.append('issuedate',$("#txtIssueDate").val());
            formData.append('expirydate',$("#txtExpiry").val());
            formData.append('hdnEdit',$("#hdnedit").val());

            $.ajax({
                type: "POST",
                url: ERP_FUNCTION_API_END_POINT+"?method=employee_docs_save",
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
                url :ERP_FUNCTION_API_END_POINT + "?method=list_employees_docs", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                error: function(){
                }
            }
        });
    }


    $('#list_documntes tbody').on('click', 'td label.ClsEdit', function (){
        $("html, body").animate({ scrollTop: 0 }, "slow");
        var id=$(this).attr('alt_pk_id');
        var dept_id=$(this).attr('alt_dept');
        var emp_id=$(this).attr('alt_emp');
        var alt_type=$(this).attr('alt_type');
        var alt_title=$(this).attr('alt_title');
        var issue_date=$(this).attr('alt_issue');
        var exp_date=$(this).attr('alt_exp_date');

        $("#ddlDocTypes").val(alt_type);
        $("#txtTitle").val(alt_title);
        $("#txtIssueDate").val(issue_date);
        $("#txtExpiry").val(exp_date);
        $("#hdndept_id").val(dept_id);
        $("#hdnemp_id").val(emp_id);
        $("#hdnedit").val(id);
        Loaddept();

    });

    $('#list_documntes tbody').on('click', 'td label.ClsRemove', function (){
        var id=$(this).attr('alt_pk_id');

        var confrim=confirm('Are you really want to delete the record?');
        if(confrim==true) {

            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=remove_doc", 'remove_id='+id, function (data) {
                if (data.status == 'FAIL') {
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