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
                                    <?= trans('DOCUMENT CATEGORIES') ?>
                                </h3>
                            </div>
                        </div>


                        <!--begin::Form-->
                        <form class="kt-form kt-form--label-right" id="element_form">
                            <div class="kt-portlet__body" style="padding: 20px !important;">
                                <div class="Msg"></div>
                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label><?= trans('Document Type') ?>:
                                        </label>
                                         <input type="text" id="txtDoc_type" name="txtDoc_type" class="form-control" />
                                    </div>
                                    <div class="col-lg-4">
                                        <label class=""><?= trans('Notify Before(Days)') ?>:</label>
                                        <input type="number" id="txtNotify" name="txtNotify" class="form-control" />
                                    </div>
                                    <div class="col-lg-4">
                                        <label class=""><?= trans('Status') ?>:</label>
                                         <select id="ddlStatus" class="form-control" >
                                             <option value="1">Active</option>
                                             <option value="0">Inactive</option>
                                         </select>
                                    </div>


                                    <div class="col-lg-3">
                                        <label style="height: 13px;"></label>
                                        <div class="input-group">
                                            <button type="button" onclick="SavePayElement();" class="btn btn-primary">
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
                                <th><?= trans('Document Type') ?></th>
                                <th><?= trans('Notify Before (Days)') ?></th>
                                <th>Status</th>
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
<?php include "footer.php"; ?>
<script>
    $(document).ready(function () {
        Fecthdata();
    });

    function SavePayElement() {

        var type=$("#txtDoc_type").val();
        var notify=$("#txtNotify").val();
        var status=$("#ddlStatus").val();

        if(type=='')
        {
            toastr.error('ERROR !. Please enter document type');
            return false;
        }

        if(notify=='' || notify<=0)
        {
            toastr.error('ERROR !. Expiration date canot be blank or zero');
            return false;
        }

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=Save_doc_type",
            {'type':type,'notify':notify,'status':status,'hdn_id':$("#hdnAction").val()}, function (data) {
            if (data.status === 'FAIL') {
                  toastr.error(data.msg);
            }
            else
            {
                toastr.success(data.msg);
                Fecthdata();
            }
        });
    }


    function Fecthdata()
    {
        $("#txtElement_Name").val('');
        $("#hdnAction").val('');

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
            method: 'get_document_types',
            format: 'json'
        }, function (data) {

            if(data) {

                var tbody_html = "";

                $.each(data, function(key,value) {

                    tbody_html+="<tr>";
                    tbody_html+="<td>"+value.description+"</td>";
                    tbody_html+="<td>"+value.days+"</td>";
                    tbody_html+="<td>"+value.status+"</td>";

                    tbody_html+="<td><label alt='"+value.id+"' alt_val='"+value.description+"' " +
                        "        alt_days='"+value.days+"' alt_satus='"+value.inactive+"'  class='btn btn-sm btn-primary ClsBtnEdit'><i class='flaticon-edit'></i></td>";

                        tbody_html+="<td><label alt='"+value.id+"' class='btn btn-sm btn-primary ClsBtnRemove'><i class='flaticon-delete'></i></td>";

                    tbody_html+="</tr>";

                });

                $("#list_documntes_tbody").html(tbody_html);

            }

        });
    }


    $('#list_documntes tbody').on('click', 'td label.ClsBtnEdit', function (){
        $("html, body").animate({ scrollTop: 0 }, "slow");
        var id=$(this).attr('alt');
        var element_name=$(this).attr('alt_val');
        var days=$(this).attr('alt_days');
        var status=$(this).attr('alt_satus');


                $("#txtDoc_type").val(element_name);
                $("#txtNotify").val(days);
                $("#ddlStatus").val(status);
                $("#hdnAction").val(id);


    });

    $('#list_pay_elements tbody').on('click', 'td label.ClsBtnRemove', function (){
        var id=$(this).attr('alt');

        var confrim=confirm('Are you really want to delete the pay element?');
        if(confrim==true) {

            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=remove_element", 'remove_id='+id, function (data) {
                if (data.status === 'FAIL' && data.msg === 'VALIDATION_FAILED') {
                }
                else
                {
                    toastr.success('SUCCESS !. Pay Element Removed Successfully.');
                    Fecthdata();
                }
            });

        }
    });

    function Cancel()
    {
        var confrm=confirm('Exit form edit mode??');
        if(confrm==true)
        {
            location.reload();
        }

    }

</script>