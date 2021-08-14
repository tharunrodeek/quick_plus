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
                                    <?= trans('Invoice Mistake Entry') ?>
                                </h3>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <label><?= trans('Search invoice by number') ?>:
                                </label>
                            <input type="text" id="invoice_ref_Id"   class="form-control" />
                            </div>

                            <div class="col-lg-2">
                                <label style="height: 13px;"></label>
                                <div class="input-group">
                                    <button type="button" onclick="Fecthdata();" class="btn btn-primary">
                                        <?= trans('View Invoice') ?>
                                    </button>&nbsp;&nbsp;

                                </div>
                            </div>

                        </div>
                        <!--begin::Table-->
                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <table class="table table-bordered" id="list_invoice_details">
                                <thead>
                                    <th><?= trans('Invoice Number') ?></th>
                                    <th><?= trans('Customer Name') ?></th>
                                    <th><?= trans('Employee Name') ?></th>
                                    <th><?= trans('Created On') ?></th>
                                    <th><?= trans('Invoice Amount') ?></th>
                                    <th></th>
                                </thead>
                                <tbody id="list_invoice_details_tbody">
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

        if($('#invoice_ref_Id').val()=='')
        {
            toastr.error('Enter invoice reference number');
        }
        else
        {
            $('#list_invoice_details').dataTable().fnDestroy();
            $('#list_invoice_details').DataTable({
                "bProcessing": true,
                "serverSide": true,
                "iDisplayLength": 10,
                "ajax":{
                    url :ERP_FUNCTION_API_END_POINT + "?method=get_invoice_details", // json datasource
                    type: "post",  // type of method  ,GET/POST/DELETE
                    data:{'ref_id':$('#invoice_ref_Id').val()},
                    error: function(){

                    }

                }
            });
        }
    }

    $("#list_invoice_details").on("keyup", '#invoice_amnt', function() {

        var rgx = /^[0-9]*\.?[0-9]*$/;
        var invoice_amount=$('#invoice_amnt').val();
        if(!invoice_amount.match(rgx))
        {
            $('#invoice_amnt').val($('#hdn_invoice_amnt').val());
        }
    });

    $("#list_invoice_details").on("click", '.btn_submit', function() {

         var ref_no=$(this).attr('alt_ref_no');
         var empl_id=$(this).attr('alt_empl_id');
         var trans_no=$(this).attr('alt_trans_no');
         var index_id=$(this).attr('alt_index');
         var ded_amount=$('#invoice_amnt_'+index_id).val();

         if(ded_amount=='' || ded_amount=='0')
         {
             toastr.error('Invoice dedcution amount canot be zero or null');
         }
         else
         {
             AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=submit_invoice_ded_entry",{ref_no:ref_no,empl_id:empl_id
             ,trans_no:trans_no,ded_amount:ded_amount }, function (data) {

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


    });





</script>