<?php
include "header.php";
include_once($path_to_root . "/API/API_HRM_Request_Flow.php");
$return_obj=new API_HRM_Request_Flow();
$dimensions=$return_obj->get_dimensions_for_request_flow();
$access_roles=$return_obj->get_access_roles();

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
                                    <?= trans('MANAGE REQUEST FLOW') ?>
                                </h3>
                            </div>
                            <div class="Clscontrols" style="margin-top: 1%;display:none;">
                                <div class="input-group">
                                    <button type="button" onclick="SubmitFlow();" class="btn btn-primary">
                                        <?= trans('Save Request Flow') ?>
                                    </button>&nbsp;&nbsp;

                                    <button type="button" class="btn btn-secondary btnCancel"><?= trans('Cancel') ?></button>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body" style="padding: 5px !important;">
                            <div class="row">
                                <div class="col-md-4">

                                    <div>
                                        <label class=""><?= trans('COST CENTER') ?>:</label>
                                        <select id="dim_id" name="dim_id" class="form-control">
                                            <option value="">---SELECT---</option>
                                           <?php  foreach($dimensions as $dim):  ?>
                                           <option value="<?php echo $dim['id'] ?>"><?php echo $dim['name'] ?></option>
                                           <?php  endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class=""><?= trans('ACCESS LEVEL') ?>:</label>
                                        <select id="role_id" name="role_id" class="form-control">
                                            <option value="">---SELECT---</option>
                                            <?php  foreach($access_roles as $role):  ?>
                                                <option value="<?php echo $role['id'] ?>"><?php echo $role['role'] ?></option>
                                            <?php  endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class=""><?= trans('CHOOSE MODULE TYPE') ?>:</label>
                                        <select id="ddl_select_module" name="ddl_select_module" class="form-control">
                                            <option value="0">---SELECT---</option>
                                            <option value="1">Leave Request</option>
                                            <option value="2">Certificate</option>
                                            <option value="3">Passport</option>
                                            <option value="4">loan</option>
                                            <option value="5">NOC</option>
                                            <option value="6">Asset Request</option>
                                            <option value="7">Asset Return Request</option>
                                        </select>
                                    </div>

                                    <div style="margin-top: 12px;">
                                        <button type="button" id="btnListLevels" class="btn btn-primary">
                                            <?= trans('View') ?>
                                        </button>&nbsp;
                                    </div>

                                </div>

                                <div class="col-md-8">
                                    <form class="kt-form kt-form--label-right" id="flow_form">
                                        <span id="request_type_heading"></span>
                                        <div class="ClsbindLevels" style="height: 420px;overflow: auto;"></div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                 
            </div>
        </div>
    </div>
</div>
<style>
    .ClsLevel
    {
        font-size: 18px;
        font-weight: bold;
        font-color: blue;
        color: blue;
    }
</style>
<input type="hidden" id="hdndept_id" />
<input type="hidden" id="hdnedit" />
<?php include "footer.php"; ?>
<script>
  /*  $(document).ready(function () {


        var counter = 1;

        $("#addrow").on("click", function () {

            var newRow = $("<tr>");
            var cols = "";

            cols += '<td><input type="text" class="form-control" name="mail' + counter + '"/></td>';
            cols += '<td><span class="ClsLevel">Level ' + counter + '</span></td>';


            cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
            newRow.append(cols);
            $("table.order-list").append(newRow);
            counter++;
        });


        $("table.order-list").on("click", ".ibtnDel", function (event) {
            $(this).closest("tr").remove();
            counter -= 1
        });


    });*/

   function SubmitFlow()
   {
       var form = $("#flow_form");
       var params = form.serializeArray();
       var formData = new FormData();
       $(params).each(function(index, element) {
           formData.append(element.name, element.value);
       });
       formData.append('type_id',$('#ddl_select_module').val());
       formData.append('dim_id',$('#dim_id').val());
       formData.append('role_id',$('#role_id').val());

       $.ajax({
           type: "POST",
           url: ERP_FUNCTION_API_END_POINT+"?method=save_request_flow",
           data: formData,
           contentType: false,
           processData: false,
           success: function (data) {
                alert(data);
                location.reload();
           }
       });

   }




    $('#btnListLevels').click(function()
    {
        $('.Clscontrols').css('display','block');

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=list_levels",
            'type_id='+$('#ddl_select_module').val()+'&dim_id='+$('#dim_id').val()+'&role_id='+$('#role_id').val(), function (data) {
                 $('.ClsbindLevels').html(data.html);


                 $('#level_1').val(data.levels.lev_1);
                 $('#level_2').val(data.levels.lev_2);
                 $('#level_3').val(data.levels.lev_3);
                 $('#level_4').val(data.levels.lev_4);
                 $('#level_5').val(data.levels.lev_5);
                 $('#level_6').val(data.levels.lev_6);
                 $('#level_7').val(data.levels.lev_7);
                 $('#level_8').val(data.levels.lev_8);
                 $('#level_9').val(data.levels.lev_8);
                 $('#level_10').val(data.levels.lev_10);

            });
    });

</script>