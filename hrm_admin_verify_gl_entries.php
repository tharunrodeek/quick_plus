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
                                    <?= trans('Verify GL Entries') ?>
                                </h3>

                            </div>

                            <div style="margin: 1%;">
                                <input type="button" id="submitGL" value="Post GL Entries" class="btn btn-primary" />
                            </div>

                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <label><?= trans('Search by payroll reference number ') ?>:
                                </label>
                            <input type="text" id="payslip_Id"   class="form-control" />
                            </div>

                            <div class="col-lg-2">
                                <label style="height: 13px;"></label>
                                <div class="input-group">
                                    <button type="button" onclick="Fecthdata();" class="btn btn-primary">
                                        <?= trans('View GL Entries') ?>
                                    </button>&nbsp;&nbsp;

                                </div>
                            </div>

                        </div>
                        <!--begin::Table-->
                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <table class="table table-bordered" id="list_gl_entries">
                                <thead>
                                <th><?= trans('Employee') ?></th>
                                <th><?= trans('GL Entry') ?></th>
                                <th></th>
                                </thead>
                                <tbody id="list_gl_entries_tbody">
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "footer.php"; ?>
<script>


    function Fecthdata()
    {

        if($('#payslip_Id').val()=='')
        {
            toastr.error('Enter payslip refernce number');
        }
        else
        {
            $('#list_gl_entries').dataTable().fnDestroy();
            $('#list_gl_entries').DataTable({
                "bProcessing": true,
                "serverSide": true,
                "iDisplayLength": 100,
                "ajax":{
                    url :ERP_FUNCTION_API_END_POINT + "?method=list_gl_entries", // json datasource
                    type: "post",  // type of method  ,GET/POST/DELETE
                    data:{'id':$('#payslip_Id').val()},
                    error: function(){

                    }
                }
            });
        }


    }



    $('#list_gl_entries tbody').on('click', 'td label.ClsRemove', function (){
        var refrence_id=$(this).attr('alt_ref_id');
        var emp_pk_id=$(this).attr('alt_empl_id');

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=delete_gl_entries",
            'refrence_id='+refrence_id+'&emp_pk_id='+emp_pk_id, function (data)
            {
                 if(data.status=='OK')
                 {
                     toastr.success(data.msg);
                 }
                 if(data.status=='FAIL')
                 {
                     toastr.error(data.msg);
                 }
            });
    });


    $('#submitGL').click(function()
    {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=post_gl_entries",
            'payroll_ref_id='+$('#payslip_Id').val(), function (data)
            {
                toastr.success(data.msg);
            });
    });


</script>