<?php include "header.php" ?>

<?php


$edit_id = REQUEST_INPUT('edit_id');

$data = [];

$req_info = [];
$item_info = [];

if (!empty($edit_id)) {
    $data = $api->getServiceRequest($edit_id, "array");
    $req_info = $data['req'];
    $item_info = $data['items'];
}

$dflt_bank_chrgs = $api->getDefaultBankChargesForServiceRequest($item_info);

$user_id = $_SESSION['wa_current_user']->user;
$user_info = get_user($user_id);

$dim_info = get_dimension($user_info['dflt_dimension_id']);


?>

<style>

    /*.item_info_section {*/
    /*display: none;*/
    /*}*/

    #frm_invoice_head .col-lg-3 {
        margin-top: -2px !important;
        margin-bottom: -13px !important;
    }

</style>

<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head" style=" border-bottom: 1px solid #ccc; padding: 10px">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title" style="
    font-weight: bold;
">
                                    <!--                                    --><? //= trans('NEW SERVICE REQUEST') ?>


                                    <?php

                                    if (!empty($edit_id))
                                        echo trans('MODIFY SERVICE REQUEST : <u style="color: #009688">' . $req_info['reference'] . '</u>');
                                    else
                                        echo trans('NEW SERVICE REQUEST');

                                    ?>

                                </h3>
                            </div>
                        </div>

                        <!--begin::Form-->


                        <form class="kt-form kt-form--label-right" style="padding: 8px" id="frm_invoice_head">

                            <input type="hidden" id="edit_id" name="edit_id" value="<?= $edit_id ?>">

                            <div class="kt-portlet__body">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="token_no">Token No:</label>
                                            <input type="text"
                                                   class="form-control"
                                                   value="<?= getArrayValue($req_info, 'token_number') ?>"
                                                   name="token_no" id="token_no" <?php if (!empty($edit_id)) {
                                                echo " readonly ";
                                            } ?>>
                                        </div>
                                    </div>

                                    <!-- <div class="col-lg-3 d-none">
                                        <div class="form-group">
                                            <label class="">Company No</label>
                                            <input type="text" class="form-control" name="company_no">
                                        </div>
                                    </div>

                                    <div class="col-lg-3 d-none">
                                        <div class="form-group">
                                            <label class="">Category</label>
                                            <input type="text" class="form-control" name="company_category" disabled>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 d-none">
                                        <div class="form-group">
                                            <label>Payment Method:</label>
                                            <select class="form-control kt-selectpicker" name="payment_method">
                                                <option value="CenterCard"><?= trans('CASH') ?></option>
                                                <option value="CustomerCard"><?= trans('CUSTOMER CARD') ?></option>
                                            </select>
                                        </div>
                                    </div> -->

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="customer" class="">Customer</label>
                                            <select class="custom-select bg-white" name="customer" id="customer"
                                                    >
                                                <?= prepareSelectOptions(
                                                    $api->get_records_from_table('0_debtors_master', ['debtor_no', 'name']),
                                                    'debtor_no', 'name', getArrayValue($req_info, 'customer_id'), "--"
                                                ) ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="mobile" class="">Mobile:</label>
                                            <input type="text" class="form-control"
                                                   value="<?= getArrayValue($req_info, 'mobile') ?>" name="mobile"
                                                   id="mobile">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="email" class="">Email:</label>
                                            <input type="text" class="form-control"
                                                   value="<?= getArrayValue($req_info, 'email') ?>" name="email"
                                                   id="email">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="display_customer" class="">Invoice For:</label>
                                            <input type="text" class="form-control"
                                                   value="<?= getArrayValue($req_info, 'display_customer') ?>"
                                                   name="display_customer" id="display_customer">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="iban_number" class="">IBAN:</label>
                                            <input type="text" class="form-control"
                                                   value="<?= getArrayValue($req_info, 'iban') ?>" name="iban_number"
                                                   id="iban_number">
                                        </div>
                                    </div>

                                    <div class="col-lg-3" style="display: none;">
                                        <div class="form-group">
                                            <label for="contact_person" class="">Contact Person:</label>
                                            <input type="text" class="form-control"
                                                   value="<?= getArrayValue($req_info, 'contact_person') ?>"
                                                   name="contact_person" id="contact_person">
                                        </div>
                                    </div>


                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="memo" class="">Ref:</label>

                                            <textarea name="memo" id="memo"
                                                      class="form-control"><?= getArrayValue($req_info, 'memo') ?></textarea>

                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Active Status</label>
                                            <select class="form-control kt-selectpicker" name="active_status">
                                                <option value="ACTIVE"><?= trans('Active') ?></option>
                                                <option value="INACTIVE"><?= trans('Inactive') ?></option>
                                            </select>
                                        </div>
                                    </div>


                                </div>
                            </div>

                        </form>


                        <!--                        <div class="kt-portlet__body" id="item_info_div" style="display: none">-->

                        <div class="text-center item_info_section">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title"
                                    style="font-size: 17px !important;font-weight: bold;">
                                    <?= trans('ITEM DETAILS') ?>
                                </h3>
                            </div>

                            <?php
                            if ($dim_info['has_autofetch'] == 1) {
                                ?>

                                <button onclick="getAutoFetchPopup()" type="button" class="btn btn-warning"
                                        id="btn-autofetch-popup">AUTO-FETCH
                                </button>
                            <?php } ?>

                        </div>

                        <h4 id="invalid_token_msg" style='text-align: center; display: none'>Please enter valid TOKEN
                            number to proceed</h4>


                        <div class="item_info_section kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit"
                             style="border: 1px solid #ccc;margin-top: 9px;margin-bottom: 15px;"></div>

                        <div class="item_info_section kt-portlet__body"
                             style="padding: 0 15px 0 15px !important;border-bottom: 1px solid #ccc;">

                            <div class="form-group row">
                                <div class="col-md-1">
                                    <label>ID:</label>
                                    <input type="text" class="form-control" id="search_service"
                                           onchange="OnSearchStockID(this)">
                                </div>
                                <div class="col-lg-4">
                                    <label>Service <i class="flaticon-search-magnifier-interface-symbol"
                                                      onclick="loadSearchPopup()"
                                                      style="color: white;
    background: #5867dd;
    font-weight: bolder;
    padding: 4px;
    border-radius: 4px;
    cursor: pointer;"></i></label>
                                    <select class="form-control kt-select2 ap-select2"
                                            name="service" id="ln_stock_id" onchange="OnChangeStockItem(this)">

                                        <?= prepareSelectOptions(
                                            $api->get_permitted_item_list(),
                                            'stock_id', 'full_name', false, "--") ?>

                                    </select>
                                </div>

                                <div class="col-lg-1">
                                    <label class="">Govt.Fee</label>
                                    <input type="text" class="form-control" id="ln_govt_fee">
                                </div>

                                <div class="col-lg-1">
                                    <label class="">Add Fee</label>
                                    <input type="text" class="form-control" id="ln_bank_charge_vat">
                                </div>

                                <input type="hidden" name="dflt_bank_chrg" value="0.00" id="ln_dflt_bank_chrg">

                                <div class="col-lg-1">
                                    <label class="">Qty</label>
                                    <input type="number" class="form-control" id="ln_qty" readonly>
                                </div>

                                <div class="col-lg-1">
                                    <label class="">Bank Ch</label>
                                    <input type="text" class="form-control" id="ln_bank_charge" readonly>
                                </div>

                                <div class="col-lg-1">
                                    <label class="">Service Fee</label>
                                    <input type="text" class="form-control" id="ln_service_fee">
                                </div>

                                <div class="col-lg-1">
                                    <label class="">Discount</label>
                                    <input type="text" class="form-control" id="ln_discount" readonly>
                                </div>


                                <div class="col-lg-1" style="display: none;">
                                    <label class="">Add Govt.Fee</label>
                                    <input type="text" class="form-control" id="ln_add_govt_fee">
                                </div>

                                <div class="col-lg-1" style="display: none">
                                    <label class="">Add Service Fee</label>
                                    <input type="text" class="form-control" id="ln_add_service_fee">
                                </div>

                                <div class="col-lg-2">
                                    <label class="">Application ID</label>
                                    <input type="text" class="form-control" id="ln_application_id">
                                </div>


                                <div class="col-lg-2">
                                    <label class="">Ref.Name</label>
                                    <input type="text" class="form-control" id="ln_ref_name">
                                </div>


                                <div class="col-lg-1">
                                    <label class="">&nbsp</label>
                                    <button type="button" id="add_item" class="form-control btn btn-sm btn-primary">Add
                                        Item
                                    </button>
                                </div>


                            </div>


                            <table class="table table-bordered" id="item_detail_table">
                                <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col" style="width: 30%">Service</th>
                                    <th scope="col">QTY</th>
                                    <th scope="col">Govt.Fee</th>
                                    <th scope="col">Add Fee</th>
                                    <th scope="col">Bank Charge</th>
                                    <th scope="col">Service Charge</th>
                                    <th scope="col">Discount</th>
                                    <th scope="col">Application ID</th>
                                    <th scope="col">Ref.Name</th>
                                    <th scope="col">Tax</th>
                                    <th scope="col">Total</th>
                                    <th scope="col"></th>

                                </tr>
                                </thead>
                                <tbody id="item_detail_table_tbody">


                                <?php

                                if (!empty($edit_id)) {


                                    foreach ($item_info as $row) {

                                        $line_total = ($row['price'] +
                                                $row['bank_service_charge'] +
                                                $row['unit_tax'] + $row['govt_fee']) * $row['qty'];

                                        echo(
                                        "<tr>
                                            <td class=\"td_stock_id\" data-val=\"{$row['stock_id']}\">{$row['stock_id']}</td>
                                            <td class=\"td_description\" data-val=\"{$row['description']}\">{$row['description']}</td>
                                            <td class=\"td_qty\" data-val=\"{$row['qty']}\">{$row['qty']}</td>
                                            <td class=\"td_govt_fee\" data-val=\"{$row['govt_fee']}\">
                                                {$row['govt_fee']}
                                                <input type=\"hidden\" data-dflt_bank_chrg=\"\" value=\"{$dflt_bank_chrgs[$row['stock_id']]}\"
                                            </td>
                                            <td class=\"td_bank_charge_vat\" data-val=\"{$row['bank_service_charge_vat']}\">{$row['bank_service_charge_vat']}</td>
                                            <td class=\"td_bank_charge\" data-val=\"{$row['bank_service_charge']}\">{$row['bank_service_charge']}</td>
                                            <td class=\"td_service_charge\" data-val=\"{$row['price']}\">{$row['price']}</td>
                                            <td class=\"td_discount\" data-val=\"{$row['discount']}\">{$row['discount']}</td>
                                            <td class=\"td_application_id\" data-val=\"{$row['application_id']}\">{$row['application_id']}</td>
                                            <td class=\"td_ref_name\" data-val=\"{$row['ref_name']}\">{$row['ref_name']}</td>
                                            <td class=\"td_tax\" data-val=\"{$row['unit_tax']}\">{$row['unit_tax']}</td>
                                            <td class=\"td_total\" data-val=\"{$line_total}\">{$line_total}</td>
                                            <td style=\"display: none\" class=\"td_add_govt_fee\" data-val=\"0\">0</td>
                                            <td style=\"display: none\" class=\"td_add_service_fee\" data-val=\"0\">0</td>
                                            <td class=\"td_actions\">
                                                <div class=\"btn-group btn-group-sm\" role=\"group\" aria-label=\"\">
                                                    <button type=\"button\" class=\"btn btn-sm btn-primary btn-edit-ln\"><i class=\"flaticon2-edit\"></i></button>
                                                    <button type=\"button\" class=\"btn btn-sm btn-warning btn-delete-ln\"><i class=\"flaticon-delete\"></i></button>
                                                </div>
                                            </td>
                                        </tr>"
                                        );

                                    }

                                }

                                ?>

                                </tbody>
                            </table>


                        </div>


                        <div class="item_info_section kt-portlet__foot">
                            <div class="kt-form__actions">
                                <div class="row">

                                    <div class="col-md-6" style="text-align: right; padding-top: 3%">
                                        <button type="button" class="btn btn-success" id="place_invoice"
                                                onclick="place_srv_request();">

                                            <?php

                                            if (!empty($edit_id)) {
                                                echo "Update Service Request";
                                            } else {
                                                echo "Place Service Request";
                                            }
                                            ?>

                                        </button>
                                        <button type="reset" class="btn btn-secondary">Cancel</button>
                                    </div>


                                    <div class="col-md-6">

                                        <table class="table table-bordered" style="float: right;width: 50% !important;">

                                            <tr>
                                                <td class="kt-font-bold" style="font-weight: bold !important;">Sub
                                                    Total
                                                </td>
                                                <td id="sub_total" class="right kt-font-bold">0.00</td>
                                            </tr>

                                            <tr>
                                                <td class="kt-font-bold" style="font-weight: bold !important;">(+) Total
                                                    Tax
                                                </td>
                                                <td id="tax_total" class="right kt-font-bold">0.00</td>
                                            </tr>

                                            <tr>
                                                <td class="kt-font-bold" style="font-weight: bold !important;">(-)
                                                    Discount
                                                </td>
                                                <td id="discount_total" class="right kt-font-bold">0.00</td>
                                            </tr>

                                            <tr>
                                                <td class="kt-font-bold" style="font-weight: bold !important;">Net
                                                    Total
                                                </td>
                                                <td id="net_total" class="right kt-font-bold">0.00</td>
                                            </tr>

                                        </table>

                                    </div>

                                </div>
                            </div>
                        </div>

                        <!--                        </div>-->


                        <!--end::Form-->
                    </div>
                </div>

            </div>

            <!-- end:: Content -->
        </div>
    </div>
</div>


<div class="modal fade" id="searchItemListPopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 85% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"
                ><?= trans('ITEM LIST') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form>


                    <input type="hidden" id="loaded_main_cat_id">
                    <input type="hidden" id="loaded_sub_cat_id">

                    <div class="row cat-image-div">


                        <div class="col"><img src="ERP/themes/daxis/images/cat_logo_IMMIGRATION.png"></div>
                        <div class="col">col</div>
                        <div class="col">col</div>
                        <div class="col">col</div>
                    </div>

                    <table class="table table-bordered table-sm" id="search_items_table">
                        <thead>
                        <tr>
                            <th scope="col" style="10%">Item Code</th>
                            <th scope="col" style="">Item Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Service Fee</th>
                            <th scope="col">Govt.Fee+ServiceFee</th>
                        </tr>
                        </thead>
                        <tbody id="search_items_tbody">

                        </tbody>

                    </table>

                </form>

            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="autoFetchPopUp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 85% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"
                ><?= trans('AUTO FETCH - PENDING APPLICATIONS') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form>

                    <table class="table table-bordered table-sm" id="autofetch_items_table">
                        <thead>
                        <tr>
                            <th scope="col" style="10%"><p>SL.No</p><input type="checkbox" class="af_select_all"></th>
                            <th scope="col" style="">Service</th>
                            <th scope="col">Govt. Fee</th>
                            <th scope="col">Center Fee</th>
                            <th scope="col">Application ID</th>
                            <th scope="col">Ref. Name</th>

                        </tr>
                        </thead>
                        <tbody id="autofetch_items_tbody">

                        </tbody>

                    </table>

                    <button type="button" id="batch_auto_add" class="btn btn-md btn-primary">Load Items</button>

                </form>

            </div>

        </div>
    </div>
</div>


<?php include "footer.php"; ?>


<?php

if (!empty($edit_id)) {

    echo "<script>

setTimeout(function() {
    
    CalculateSummary();
  
},1000)


</script>";

}

?>


<script>


    $(document).ready(function () {

        var edit_id = $("#edit_id").val();

        if (edit_id === "" || edit_id.length <= 0) {
            $(".item_info_section").hide();
            $("#invalid_token_msg").show();
        }

        $("#token_no").change(function () {


            $("#customer").val(1);
            $("#email").val("");
            $("#mobile").val("");
            $("#display_customer").val("");

            $(".item_info_section").show();
            $("#invalid_token_msg").hide();

            // var token = this.value;
            //
            // AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
            //     method: 'get_token_info',
            //     token: token,
            //     format: 'json'
            // }, function (data) {
            //
            //     if (data.data === false) {
            //
            //         $(".item_info_section").hide();
            //
            //         $("#invalid_token_msg").show();
            //
            //     }
            //     else {
            //
            //         var info = data.data;
            //
            //         $("#customer").val(info.customer_id);
            //         $("#email").val(info.customer_email);
            //         $("#mobile").val(info.customer_mobile);
            //         $("#display_customer").val(info.display_customer);
            //         $("#iban_number").val(info.customer_iban);
            //         $("#contact_person").val(info.contact_person);
            //
            //         $(".item_info_section").show();
            //         $("#invalid_token_msg").hide();
            //     }
            //
            // });

        });


        $("#add_item").click(function (e) {
            var stock_id = $("#ln_stock_id").val();
            var item_desc = $("#ln_stock_id option:selected").text();
            var govt_fee = $("#ln_govt_fee").val();
            var qty = $("#ln_qty").val();
            var tax = $("#ln_tax").val();
            var bank_charge = $("#ln_bank_charge").val();
            var bank_charge_vat = $("#ln_bank_charge_vat").val();
            var service_fee = $("#ln_service_fee").val();
            var discount = $("#ln_discount").val();
            var dflt_bank_chrg = $('#ln_dflt_bank_chrg').val();
            var application_id = $("#ln_application_id").val();
            var ref_name = $("#ln_ref_name").val();


            var app_id_exists = false;
            $("#item_detail_table_tbody tr").each(function (i, row) {

                var $row = $(row);

                var app_id = $row.find(".td_application_id").data('val');

                if((String(app_id).length > 0) && (application_id == app_id)) {
                    app_id_exists = true;
                }

            });

            if(app_id_exists) {
                alert("Application ID already exists in this context");
                return false;
            }


            addRow(
                stock_id,
                item_desc,
                govt_fee,
                service_fee,
                application_id,
                ref_name,
                qty,
                bank_charge,
                dflt_bank_chrg,
                bank_charge_vat
            );


        });


        $(document).on('click', '.btn-delete-ln', function () {
            $(this).parents('tr').remove();
            CalculateSummary();
        });


        $(document).on('click', '.btn-edit-ln', function () {

            var $tr = $(this).parents('tr');

            var qty = $tr.find("td.td_qty").data('val');
            var stock_id = $tr.find("td.td_stock_id").data('val');
            var govt_fee = $tr.find("td.td_govt_fee").data('val');
            var bank_charge = $tr.find("td.td_bank_charge").data('val');
            var bank_charge_vat = $tr.find("td.td_bank_charge_vat").data('val');
            var service_charge = $tr.find("td.td_service_charge").data('val');
            var discount = $tr.find("td.td_discount").data('val');
            var add_govt_fee = $tr.find("td.td_add_govt_fee").data('val');
            var add_service_fee = $tr.find("td.td_add_service_fee").data('val');
            var application_id = $tr.find("td.td_application_id").data('val');
            var ref_name = $tr.find("td.td_ref_name").data('val');
            var dflt_bank_charge = $tr.find("[data-dflt_bank_chrg]").val();


            // $("#ln_stock_id").val(stock_id).trigger('change');

            $('#ln_stock_id').select2('destroy');
            $('#ln_stock_id').val(stock_id).select2();


            $("#ln_qty").val(qty);
            $("#ln_govt_fee").val(govt_fee);
            $("#ln_bank_charge").val(bank_charge);
            $("#ln_bank_charge_vat").val(bank_charge_vat);
            $("#ln_service_fee").val(service_charge);
            $("#ln_discount").val(discount);
            $("#ln_add_govt_fee").val(add_govt_fee);
            $("#ln_add_service_fee").val(add_service_fee);
            $("#ln_application_id").val(application_id);
            $("#ln_ref_name").val(ref_name);
            $('#ln_dflt_bank_chrg').val(dflt_bank_charge);

            $tr.remove();


        })
    });

    // $('#ln_govt_fee').on('change', function (ev) {
    //     govt_fee = this.value;
    //     if (govt_fee > 0) {
    //         bankCharge = parseFloat($('#ln_bank_charge').val());
    //         if (bankCharge == 0) {
    //             $('#ln_bank_charge').val($('#ln_dflt_bank_chrg').val());
    //         }
    //     } else {
    //         $('#ln_bank_charge').val('0.00');
    //     }
    // });

    function addRow(
        stock_id,
        item_desc,
        govt_fee,
        service_fee,
        application_id,
        ref_name,
        qty,
        bank_charge,
        dflt_bank_chrg,
        bank_charge_vat
    ) {

        var customer_id = $("#customer").val();

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
            method: 'get_item_info',
            stock_id: stock_id,
            customer_id: customer_id,
            format: 'json'
        }, function (data) {


            var item_info = data.g;
            var price_info = data.p;
            var discount_info = data.d;
            var tax_info = data.t;
            var category_info = data.c;

            if (!item_info) {
                toastr.error("AutoFetch Item not defined for the item code : " + stock_id);
                return false;
            }

            var discount = discount_info.discount;
            if (!discount_info)
                discount = 0;

            var tax_percent = 0;
            if (!tax_info)
                tax_percent = 0;
            else
                tax_percent = parseFloat(tax_info['rate']);

            // var tax_percent = 5;
            // if (item_info.tax_type_id === "2") {
            //     tax_percent = 0;
            // }

            var is_application_id_required = category_info.srq_app_id_required;


            if (service_fee === '' || isNaN(service_fee))
                service_fee = 0;

            if (discount === '' || isNaN(discount))
                discount = 0;

            var tax_amount = (parseFloat(service_fee - discount) * tax_percent) / 100;
            var total_govt_fee = parseFloat(govt_fee) * parseFloat(qty);

            var total = parseFloat(service_fee) +
                parseFloat(bank_charge) +
                parseFloat(bank_charge_vat) +
                tax_amount +
                /* parseFloat(add_govt_fee) +
                 parseFloat(add_service_fee) +*/
                total_govt_fee;


            var add_row = ` <tr><td class="td_stock_id" data-val="${stock_id}">${stock_id}</td>
                                        <td class="td_description" data-val="${item_desc}">${item_desc}</td>
                                        <td class="td_qty" data-val="${qty}">${qty}</td>
                                        <td class="td_govt_fee" data-val="${govt_fee}">${amount(govt_fee)}</td>
                                        <td class="td_bank_charge_vat" data-val="${bank_charge_vat}">${amount(bank_charge_vat)}</td>
                                        <td class="td_bank_charge" data-val="${bank_charge}">
                                            ${amount(bank_charge)}
                                            <input type="hidden" data-dflt_bank_chrg="" value="${dflt_bank_chrg}"
                                        </td>
                                        <td class="td_service_charge" data-val="${service_fee}">${amount(service_fee)}</td>
                                        <td class="td_discount" data-val="${discount}">${amount(discount)}</td>
                                        <td class="td_application_id" data-val="${application_id}">${application_id}</td>
                                        <td class="td_ref_name" data-val="${ref_name}">${ref_name}</td>
                                        <td class="td_tax" data-val="${tax_amount}">${amount(tax_amount)}</td>
                                        <td class="td_total" data-val="${total}">${amount(total)}</td>

                                        <td style="display: none" class="td_add_govt_fee" data-val="0">${amount(0)}</td>
                                        <td style="display: none" class="td_add_service_fee" data-val="0">${amount(0)}</td>


                                        <td class="td_actions">
                                            <div class="btn-group btn-group-sm" role="group" aria-label="">
                                                <button type="button" class="btn btn-sm btn-primary btn-edit-ln"><i class="flaticon2-edit"></i></button>
                                                <button type="button" class="btn btn-sm btn-warning btn-delete-ln"><i class="flaticon-delete"></i></button>
                                            </div>
                                        </td></tr>`;



            if (is_application_id_required === "1" && application_id === "") {
                toastr.error("Please enter the application ID");
            }
            else {

                if (total <= 0 || isNaN(total))
                    return false;

                $("#item_detail_table_tbody").append(add_row);

                CalculateSummary();

            }


        });
    }

    function popupCenter(url, title, w, h) {
        // Fixes dual-screen position                             Most browsers      Firefox
        var dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
        var dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;

        var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        var systemZoom = width / window.screen.availWidth;
        var left = (width - w) / 2 / systemZoom + dualScreenLeft
        var top = (height - h) / 2 / systemZoom + dualScreenTop
        var newWindow = window.open(
            url,
            title,
            'scrollbars=yes'
            + ',width=' + w / systemZoom
            + ',height=' + h / systemZoom
            + ',top=' + top
            + ',left=' + left
        )

        if (window.focus) newWindow.focus();
    }

    function CalculateSummary() {


        var total_qty = 0;
        var total_govt_fee = 0;
        var total_bank_charge = 0;
        var total_service_charge = 0;
        var total_discount = 0;
        var total_tax = 0;
        var total_net = 0;

        $("#item_detail_table_tbody tr").each(function (i, row) {

            var $row = $(row);

            // if(i===0) return true;

            var qty = $row.find(".td_qty").data('val');
            var govt_fee = $row.find(".td_govt_fee").data('val');
            var bank_charge = $row.find(".td_bank_charge").data('val');
            var bank_charge_vat = $row.find(".td_bank_charge_vat").data('val');
            var service_charge = $row.find(".td_service_charge").data('val');
            var discount = $row.find(".td_discount").data('val');
            var tax = $row.find(".td_tax").data('val');
            var total = $row.find(".td_total").data('val');

            total_qty += parseFloat(qty);
            total_govt_fee += parseFloat(govt_fee);
            total_bank_charge += parseFloat(bank_charge);
            total_service_charge += parseFloat(service_charge);
            total_discount += parseFloat(discount);
            total_tax += parseFloat(tax);
            total_net += parseFloat(total);


            console.log(total)


        });


        $("#sub_total").text(amount(total_net - total_tax));
        $("#net_total").html(amount(total_net - total_discount));
        $("#tax_total").html(amount(total_tax));
        $("#discount_total").html(amount(total_discount));

    }

    function place_srv_request() {


        if ($("#item_detail_table_tbody tr").length <= 0) {
            toastr.error("Please enter at least one line item");
            return false;
        }


        var $form = $("#frm_invoice_head");
        document.getElementById('customer').disabled = false;
        var params = AxisPro.getFormData($form);
        document.getElementById('customer').disabled = true;
        var items_array = [];

        $("#item_detail_table_tbody tr").each(function (i, row) {

            var $row = $(row);

            var obj = {
                stock_id: $row.find(".td_stock_id").data('val'),
                description: $row.find(".td_description").data('val'),
                qty: $row.find(".td_qty").data('val'),
                govt_fee: $row.find(".td_govt_fee").data('val'),
                bank_charge: $row.find(".td_bank_charge").data('val'),
                bank_charge_vat: $row.find(".td_bank_charge_vat").data('val'),
                service_charge: $row.find(".td_service_charge").data('val'),
                discount: $row.find(".td_discount").data('val'),
                application_id: $row.find(".td_application_id").data('val'),
                ref_name: $row.find(".td_ref_name").data('val'),
                tax: $row.find(".td_tax").data('val'),
                total: $row.find(".td_total").data('val')
            };

            items_array.push(obj);


        });

        params.items = items_array;


        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=place_srv_request", params, function (data) {

            if (data && data.status === 'FAIL') {
                toastr.error(data.msg);


                var errors = data.data;


                $.each(errors, function (key, value) {

                    $("#" + key)
                        .after('<span class="error_note form-text text-muted">' + value + '</span>')

                })

            }

            else if (data && data.status === 'OK') {
                swal.fire({
                    title: 'Success!',
                    html: 'Service Request added.',
                    type: 'success',
                    timerProgressBar: true,
                }).then(function () {
                    popupCenter(data.print_url, '_blank', 900, 500);
                    window.location.reload();
                });
            } else {
                swal.fire('Oops', 'Something went wrong!', 'error');
            }

            // else {
            //
            //     swal.fire(
            //         'Success!',
            //         'Service Request added.',
            //         'success'
            //     ).then(function () {
            //         window.location.reload();
            //     });
            //
            // }

        });

    }

    function OnSearchStockID($this) {

        var stock_id = $($this).val();

        $("#ln_stock_id").val(stock_id).trigger('change');

    }

    function OnChangeStockItem($this) {

        var customer_id = $("#customer").val();

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
            method: 'get_item_info',
            stock_id: $($this).val(),
            customer_id: customer_id,
            format: 'json'
        }, function (data) {


            var g = data.g;
            var p = data.p;
            var d = data.d;


            var discount = d.discount;
            if (!d)
                discount = 0;

            var extra_service_charge = g.extra_service_charge;

            $("#ln_govt_fee").val(g.govt_fee);
            $("#ln_qty").val(1);
            $('#ln_dflt_bank_chrg').val(g.dflt_bank_chrg);
            $("#ln_bank_charge").val(parseFloat(g.bank_service_charge)+parseFloat(extra_service_charge));
            $("#ln_bank_charge_vat").val(parseFloat(g.bank_service_charge_vat));

            var price = parseFloat(p.price)+parseFloat(g.pf_amount);
            
            $("#ln_service_fee").val(price);
            $("#ln_add_govt_fee").val(0);
            $("#ln_add_service_fee").val(0);
            $("#ln_discount").val(discount);


        });

    }

    function getSearchItemsList(main_cat_id, sub_cat_id) {

        AxisPro.BlockDiv("#kt_content");

        $('#search_items_table').DataTable().destroy();

        if (!sub_cat_id)
            sub_cat_id = 0;

        if (!main_cat_id)
            main_cat_id = 0;


        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
            method: 'getSearchItemsList',
            format: 'json',
            main_cat_id: main_cat_id,
            sub_cat_id: sub_cat_id,
        }, function (data) {

            if (data) {

                var tbody_html = "";

                $.each(data, function (key, val) {

                    var item_name = val.description + " " + val.long_description;

                    tbody_html += "<tr>";

                    tbody_html += "<td><a class='select-item' href='javascript:void(0)' data-value='" + val.stock_id + "'>" + val.stock_id + "</a></td>";
                    tbody_html += "<td>" + item_name + "</td>";
                    tbody_html += "<td>" + val.category_name + "</td>";
                    tbody_html += "<td>" + val.service_fee + "</td>";
                    tbody_html += "<td>" + val.total_display_fee + "</td>";

                    tbody_html += "</tr>";

                });

                $("#search_items_tbody").html(tbody_html);

                $('#search_items_table').DataTable({
                    destroy: true,
                    retrieve: true,
                    // searching: false,
                    dom: 'Bfrtip',
                    buttons: [
                        'colvis'
                    ]
                });


                AxisPro.UnBlockDiv("#kt_content");

            }


        });

    }

    function loadSearchPopup() {

        $("#searchItemListPopup").modal("show");
        getSearchItemsList();
        getCategoriesOfUserCostCenter();

    }

    function getCategoriesOfUserCostCenter() {


        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
            method: 'getCategoriesOfUserCostCenter',
            format: 'json'
        }, function (data) {

            if (data) {

                var div_html = "";

                $.each(data, function (key, val) {

                    div_html += '<div class="col cat_filter main_cats" onclick="getTopLevelSubcategories(' + val.category_id + ')" data-id="' + val.category_id + '"><img src="' + val.category_logo + '"><p>' + val.description + '</p></div>';

                });

                $(".cat-image-div").html(div_html);

            }

        })

    }

    $(document).on("click", ".select-item", function () {
        var selected_stock_id = $(this).data('value');

        $("#ln_stock_id").val(selected_stock_id);
        $("#ln_stock_id").trigger('change');
        $("#searchItemListPopup").modal("hide");

    });


    function getTopLevelSubcategories(id) {

        var selected_val = id;

        $("#loaded_main_cat_id").val(id);

        getSearchItemsList(selected_val);

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
            method: 'getTopLevelSubcategories',
            cat_id: selected_val,
            format: 'json'
        }, function (data) {


            if (data) {

                var div_html = "";

                div_html += "<button type='button' onclick='getCategoriesOfUserCostCenter(); getSearchItemsList(); ' class='btn btn-sm btn-primary'><i class='flaticon2-left-arrow'></i></button>";

                $.each(data, function (key, val) {

                    div_html += '<div class="col cat_filter sub_cats" onclick="getChildLevelSubcategories(' + val.id + ')" data-id="' + val.id + '"><img src="' + val.category_logo + '"><p>' + val.description + '</p></div>';

                });

                $(".cat-image-div").html(div_html);


            }


        });

    }


    function getChildLevelSubcategories(id) {

        var selected_val = id;
        var loaded_main_cat_id = $("#loaded_main_cat_id").val();

        getSearchItemsList(loaded_main_cat_id, selected_val);

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
            method: 'getChildLevelSubcategories',
            id: selected_val,
            format: 'json'
        }, function (data) {


            if (data) {

                var div_html = "";


                div_html += "<button type='button' onclick='getTopLevelSubcategories(" + loaded_main_cat_id + "); " +
                    "getSearchItemsList(" + loaded_main_cat_id + ");' class='btn btn-sm btn-primary'><i class='flaticon2-left-arrow'></i></button>";


                $.each(data, function (key, val) {

                    div_html += '<div class="col cat_filter sub_cats" onclick="getSearchItemsList(' + loaded_main_cat_id + ',' + val.id + ')" data-id="' + val.id + '"><img src="' + val.category_logo + '"><p>' + val.description + '</p></div>';

                });

                $(".cat-image-div").html(div_html);

            }

        });

    }


    var itemsJSON;

    $.getJSON(ERP_ROOT_URL + "/js/immServices.json", function (data) {

        itemsJSON = data.items;

    });

    $(document).on("click", ".af_select_all", function () {

        $('input:checkbox').not(this).prop('checked', this.checked);

    });

    function getAutoFetchPopup() {

        navigator.clipboard.readText().then(clipText =>
            displayFetchedData(clipText)
        );
    }

    function displayFetchedData(clipText) {

        var each_service = clipText.split("#");
        var tbody_html = "";
        var application_ids = [];

        var clipboard_check = each_service[0].split("|")[0];

        if (clipboard_check === "TASHEEL" || clipboard_check === "IMMIGRATION") {


            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
                method: 'getServiceRequestApplicationIDs',
                format: 'json'
            }, function (data) {

                var invoiced_application_id_array = data;

                $.each(each_service, function (key, value) {

                    var this_service = value.split("|");
                    if (this_service[0] === "IMMIGRATION") {//For testing


                        var service_name_en = this_service[1];
                        var service_name_ar = this_service[2];

                        var lang = 'en';
                        if (service_name_en.length <= 0) {
                            lang = 'ar';
                        }

                        var check_var = service_name_en;

                        if (lang === 'ar')
                            check_var = service_name_ar;

                        var item = itemsJSON.find(el => (el[lang]).toLowerCase() === check_var.toLowerCase().trim());

                        if (item) {
                            if (lang === 'en') {
                                var ar_name = "";

                                if (item["ar"] !== undefined)
                                    ar_name = item["ar"];

                                service_name_ar = ar_name;
                            }
                            else {
                                var en_name = "";
                                if (item["en"] !== undefined)
                                    en_name = item["en"];
                                service_name_en = en_name;
                            }
                        }


                        var total_fee = this_service[6];
                        var application_id = this_service[4];
                        var transaction_id = this_service[3];
                        var bank_ref_number = this_service[5];
                        var service_charge = this_service[7];
                        var ref_name = this_service[9];


                        application_id = application_id.replace(/<br>\s*$/, "");
                        application_id = application_id.replace(/URN\s*$/, "");
                        application_id = application_id.replace(/<br>\s*$/, "");

                        application_id = $.trim(application_id);


                        if (jQuery.inArray(application_id, invoiced_application_id_array) !== -1)
                            return true;

                        tbody_html += "<tr>";
                        tbody_html += "<td><input type='checkbox' class='auto_batch_checked'/></td>";
                        tbody_html += "<td class='af_srv_name'>" + service_name_en + ' ' + service_name_ar + "</td>";
                        tbody_html += "<td class='af_tot'>" + parseFloat(total_fee).toFixed(2) + "</td>";

                        var actual_center_fee = parseFloat(service_charge);
                        if (clipboard_check === "TASHEEL") {
                            actual_center_fee = parseFloat(total_fee) - parseFloat(service_charge);
                        }
                        tbody_html += "<td class='af_srv_amt'>" + actual_center_fee.toFixed(2) + "</td>";

                        tbody_html += "<td class='af_app_id'>" + application_id + "</td>";
                        tbody_html += "<td class='af_ref_name'>" + ref_name + "</td>";
                        tbody_html += "</tr>";

                        application_ids.push(application_id);

                    }

                });

                if (tbody_html.length > 0) {
                    $("#autoFetchPopUp").modal("show");
                    $("#autofetch_items_tbody").html(tbody_html);
                }
                else {
                    swal.fire(
                        'No Pending Applications!',
                        'Please click the B button on <br>DX AutoFetch and try again..',
                        'warning'
                    )
                }

            });

        }
        else {
            swal.fire(
                'No Data!',
                'Please click the B button on <br>DX AutoFetch and try again..',
                'warning'
            )
        }

    }


    $(document).on("click", "#batch_auto_add", function () {


        $("#autofetch_items_table").find('tr').each(function () {

            var row = $(this);
            if (row.find('input[type="checkbox"]').is(':checked')) {

                var srv_name = row.find(".af_srv_name").html();
                var tot = row.find(".af_tot").html();
                var srv_amt = row.find(".af_srv_amt").html();
                var tr_id = row.find(".af_tr_id").html();
                var app_id = row.find(".af_app_id").html();
                var bank_ref = row.find(".af_bank_ref").html();
                var ref_name = row.find(".af_ref_name").html();

                var auto_stock_id = 'IMM_AUTO';

                if (!srv_amt)
                    return true;

                AxisPro.BlockDiv("#kt_content");

                var dflt_qty = 1;
                var dflt_bank_charge = 3.15;

                addRow(
                    auto_stock_id,
                    srv_name,
                    tot,
                    srv_amt,
                    app_id,
                    ref_name,
                    dflt_qty,
                    dflt_bank_charge,
                    0.00
                );

            }
        });

        $('html, body').animate({
            scrollTop: $("#item_detail_table").offset().top
        }, 2000);

        $("#autoFetchPopUp").modal("hide");
        AxisPro.UnBlockDiv("#kt_content");

        toastr.success("Selected services added from AutoFetch");

    });


</script>

<style>

    /*.item_info_section{*/
    /*display: none !important;*/
    /*}*/

    .dt-buttons.btn-group {
        display: none;
    }

    .select-item {
        text-decoration: underline;
    }

    .cat-image-div {

    }

    .cat-image-div img {
        width: 75px;
    }

    .cat-image-div .col {
        border: 1px solid #ccc;
        border-radius: 3px;
        margin: 2px;
        text-align: center;
    }

    .cat_filter {
        cursor: pointer;
    }

</style>