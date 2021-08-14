<?php
include "header.php";?>

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
                                    <?= trans('ASSET REQUEST') ?>
                                </h3>
                            </div>
                        </div>
                        <?php
                        include_once($path_to_root . "/API/API_HRM_Document_Request.php");
                        $call_obj=new API_HRM_Document_Request();
                        $asset_data=$call_obj->get_asset_category();
                        ?>


                        <div class="kt-portlet__body" style="padding: 5px !important;">
                            <form class="kt-form kt-form--label-right" id="leave_form">
                            <div class="row">

                                <div class="col-sm">
                                    <div>
                                        <label class=""><?= trans('ASSET CATEGORY') ?>:</label>
                                        <select id="ddl_asset_cate" name="ddl_asset_cate" class="form-control">
                                            <option value="0">---Select Asset Category---</option>
                                        <?php
                                        foreach($asset_data as $d):
                                        ?>
                                        <option value="<?php echo $d['id']; ?>"><?php echo $d['name']; ?></option>
                                        <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div>
                                        <label><?= trans('ASSET TYPE') ?>:
                                        </label>
                                         <div class="bind_asset_types"></div>
                                    </div>
                                    <div>
                                        <label class=""><?= trans('MODEL') ?>:</label>
                                        <input type="text" id="model" name="model" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div>
                                        <label class=""><?= trans('MODEL NUMBER') ?>:</label>
                                        <input type="text" id="model_number" name="model_number" class="form-control"/>
                                    </div>
                                    <div>
                                        <label class=""><?= trans('SERIAL NUMBER') ?>:</label>
                                        <input type="text" id="serial_number" name="serial_number" class="form-control"/>
                                    </div>
                                    <div>
                                        <label class=""><?= trans('COMMENTS') ?>:</label>
                                        <textarea id="txt_commnts" name="txt_commnts" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div style="margin-top: 42%;">
                                    <button type="button" onclick="SubmitLeave();" class="btn btn-primary">
                                        <?= trans('Submit Request') ?>
                                    </button>&nbsp;&nbsp;
                                    <button type="button" class="btn btn-secondary btnCancel"><?= trans('Cancel') ?></button>
                                    </div>
                                    <input type="hidden" id="EDIT_id" name="EDIT_id"/>
                                    <input type="hidden" id="request_type" name="request_type" value="1"/>
                                </div>

                            </div>
                            </form>

                            <div class="col-md-16" style="margin-top: 5%;">


                                <div class="table-responsive" >

                                    <table class="table table-bordered" id="asset_reuqests">
                                        <thead>
                                        <th><?= trans('Ref.No') ?></th>
                                        <th><?= trans('Employee') ?></th>
                                        <th><?= trans('Category') ?></th>
                                        <th><?= trans('Asset Type') ?></th>
                                        <th><?= trans('Model') ?></th>
                                        <th><?= trans('Model Number') ?></th>
                                        <th><?= trans('Serial Number') ?></th>
                                        <th><?= trans('Comments') ?></th>
                                        <th></th>


                                        </thead>
                                        <tbody id="asset_reuqests_tbody">

                                        </tbody>
                                    </table>

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


<?php include "footer.php"; ?>

<script>


   /* $('.ap-datepicker').datepicker({
        format: 'dd-mm-yyyy',
    });*/
    fetchDataTotable();
    function SubmitLeave() {
         var flag='';
         if($('#ddl_asset_cate').val()=='0')
         {
             toastr.error('Select Asset Category');
             flag='false';
         }

         if($('#ddl_asset_types').val()=='0')
         {
             toastr.error('Select Asset Type');
             flag='false';
         }

         if(flag=='')
         {
             var form = $("#leave_form");
             var params = AxisPro.getFormData(form);
             $("#loader").css('display','block');




             if($(".date").val()=='')
             {
                 toastr.error('ERROR !. Please choose noc required date');
                 $("#loader").css('display','none');
                 return false;

             }

             $(".error_note").hide();

             AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=submit_asset_request", params, function (data) {

                 if(data.status =='Success')
                 {
                     toastr.success(data.msg);
                     fetchDataTotable();
                 }
                 else
                 {
                     toastr.error(data.msg);
                 }

                 $("#loader").css('display','none');
             });
         }

    }

    function fetchDataTotable()
    {
        $('#asset_reuqests').dataTable().fnDestroy();
        $('#asset_reuqests').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :ERP_FUNCTION_API_END_POINT + "?method=get_asset_requests", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                 data:{'request_type':1},
                error: function(){
                }
            }
        });
    }



    $('#asset_reuqests tbody').on('click', 'td label.ClsBtnEdit', function (){
        var alt_id=$(this).attr('alt_id');
        var category_id=$(this).attr('alt_category');
        var asset_type_id=$(this).attr('alt_typname');
        var model=$(this).attr('alt_model');
        var mode_num=$(this).attr('alt_model_num');
        var serail_num=$(this).attr('alt_serial_num');
        var comment=$(this).attr('alt_comment');


        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_asset_types"
            , 'id='+category_id, function (data) {

                $('.bind_asset_types').html(data.html);


            });
        $('#EDIT_id').val(alt_id);
        $('#ddl_asset_types').val(asset_type_id);
        $('#ddl_asset_cate').val(category_id);
        $('#model').val(model);
        $('#model_number').val(mode_num);
        $('#serial_number').val(serail_num);
        $('#txt_commnts').val(comment);

    });

    $('#asset_reuqests tbody').on('click', 'td label.ClsBtnRemove', function (){
        var edit_id=$(this).attr('alt_id');
        var confrmRes=confirm("Are you really want to delete request?");
        if(confrmRes==true)
        {
            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=remove_requests"
                , 'remove_id='+edit_id+'&type=4', function (data) {

                if (data.status === 'Error') {
                    toastr.error(data.msg);
                }
                else
                {
                    toastr.success(data.msg);
                }

                    fetchDataTotable();

            });
        }
    });


    $("#ddl_asset_cate").change(function()
    {

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_asset_types"
            , 'id='+$('#ddl_asset_cate').val(), function (data) {

                $('.bind_asset_types').html(data.html);

            });
    });



    $(".btnCancel").click(function()
    {
       location.reload();
    });



</script>