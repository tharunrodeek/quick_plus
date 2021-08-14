<?php include "header.php" ?>


<?php

$application = isset($_GET['action']) ? $_GET['action'] : "list";


switch ($application) {

    case "new" :
        include_once "new_item.php";
        break;
    case "edit" :
        include_once "new_item.php";
        break;

    case "list":
        include_once "item_list.php";
        break;

    default:
        include_once "item_list.php";
        break;
}


?>

<?php ?>
<?php include "footer.php"; ?>


<style>

    div.dataTables_wrapper div.dataTables_filter {

        text-align: left !important;

    }

    .dt-buttons {
        float: right; !important;
    }

</style>


<?php if ($application == 'new' || $application == 'edit') { ?>

    <script>

        var edit_stock_id = $("#edit_stock_id").val();



        // AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
        //     method: 'get_all_gl_accounts',
        //     format: 'json'
        // }, function (data) {
        //     AxisPro.PrepareSelectOptions(data, 'account_code', 'account_name', 'ap_gl_account_select');
        // });


        // AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
        //     method: 'get_all_item_categories',
        //     format: 'json'
        // }, function (data) {
        //     AxisPro.PrepareSelectOptions(data, 'category_id', 'description', 'ap_item_category', null, function () {
        //
        //         $('.ap_item_category').trigger('change');
        //
        //     });
        // });


        // AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
        //     method: 'get_item_tax_types',
        //     format: 'json'
        // }, function (data) {
        //     AxisPro.PrepareSelectOptions(data, 'id', 'name', 'ap_item_tax_type');
        // });





        function setDefaultAccounts($this) {

            var id = $($this).val();

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
                method: 'get_category',
                category_id: id,
                format: 'json'
            }, function (data) {


                $("#sales_account").val(data.dflt_sales_act).trigger('change');
                $("#cogs_account").val(data.dflt_cogs_act).trigger('change');

                loadSubCategory($("#category_id"), 'sub_cat_1');

                $("#sub_cat_2").html("<option value=''>--</option>");



            });

        }

        AxisPro.BlockDiv("#kt_wrapper");

        $(window).on('load', function () {


            var edit_stock_id = $("#edit_stock_id").val();


            edit_stock_id = ''; //For testing

            if (edit_stock_id !== '' && edit_stock_id !== '0') {


                AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
                    method: 'get_item_info',
                    format: 'json',
                    stock_id: edit_stock_id
                }, function (data) {

                    var g = data.g;
                    var sub = data.sub;
                    var p = data.p;

                    $("#NewStockID").val(g.stock_id);

                    $("#description").val(g.description);
                    $("#long_description").val(g.long_description);
                    $("#category_id").select2("val", g.category_id)
                    setTimeout(function () {
                        $("#sub_cat_1").select2("val", sub.parent_sub_cat_id)
                    }, 2000);

                    setTimeout(function () {
                        $("#sub_cat_2").val(sub.id).trigger("change");

                        $("#sales_account").val(g.sales_account).trigger("change");
                        $("#cogs_account").val(g.cogs_account).trigger("change");
                        $("#tax_type_id").val(g.tax_type_id).trigger("change");
                        $("#editable").val(g.editable).trigger("change");
                        $("#inactive").val(g.inactive).trigger("change");


                        $("#price").val(p.price);
                        $("#govt_fee").val(g.govt_fee);
                        $("#govt_bank_account").val(g.govt_bank_account).trigger("change");
                        $("#bank_service_charge").val(g.bank_service_charge).trigger("change");
                        $("#bank_service_charge_vat").val(g.bank_service_charge_vat).trigger("change");
                        $("#pf_amount").val(g.pf_amount).trigger("change");
                        $("#commission_loc_user").val(g.commission_loc_user).trigger("change");
                        $("#commission_non_loc_user").val(g.commission_non_loc_user).trigger("change");
                        $("#use_own_govt_bank_account").val(g.use_own_govt_bank_account).trigger("change");


                        AxisPro.UnBlockDiv("#kt_wrapper");

                    }, 4000);


                });


            }
            else {
                AxisPro.UnBlockDiv("#kt_wrapper");
            }


        });


        function CreateNewItem() {

            var $form = $("#item_form");
            var params = AxisPro.getFormData($form);

            $(".error_note").hide();

            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=save_item", params, function (data) {

                if (data.status === 'FAIL' && data.msg === 'VALIDATION_FAILED') {


                    toastr.error('ERROR !. PLEASE CHECK THE FORM DATA.');
                    var errors = data.data;


                    $.each(errors, function (key, value) {

                        $("#" + key)
                            .after('<span class="error_note form-text text-muted">' + value + '</span>')

                    })


                }
                else {

                    swal.fire(
                        'Success!',
                        'Item Saved',
                        'success'
                    ).then(function () {
                        window.location.reload();
                    });

                }

            });

        }

        function generateItemCode() {

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT + "?method=generate_item_code", {}, function (data) {

                if (data.status === 'OK') {
                    $("#NewStockID").val(data.data);
                }

            });

        }


        function loadSubCategory($this, populate_result_to, callback) {

            var category_id = $("#category_id").val();

            var parent_id = 0;

            if ($($this).attr("name") !== "category_id") {
                parent_id = $($this).val();

                if (parent_id == 0)
                    parent_id = -1;

            }

            if (category_id === '') return false;

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
                method: 'get_subcategory',
                format: 'json',
                category_id: category_id,
                parent_id: parent_id
            }, function (data) {
                AxisPro.PrepareSelectOptions(data, 'id', 'value', populate_result_to, null, callback);

            });

        }


    </script>

<?php } ?>



<?php if ($application == 'list') { ?>

    <script>

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
            method: 'get_items',
            format: 'json'
        }, function (data) {

            if(data) {

                var tbody_html = "";

                $.each(data, function(key,value) {

                    tbody_html+="<tr>";
                    tbody_html+="<td>"+value.stock_id+"</td>";
                    tbody_html+="<td>"+value.category_name+"</td>";
                    tbody_html+="<td>"+value.item_description+"</td>";
                    tbody_html+="<td>"+value.long_description+"</td>";
                    tbody_html+="<td>"+value.service_charge+"</td>";
                    tbody_html+="<td>"+value.govt_fee+"</td>";
                    tbody_html+="<td>"+value.govt_account_name+"</td>";
                    tbody_html+="<td>"+value.bank_service_charge+"</td>";
                    tbody_html+="<td>"+value.bank_service_charge_vat+"</td>";
                    tbody_html+="<td>"+value.pf_amount+"</td>";
                    tbody_html+="<td>"+value.commission_loc_user+"</td>";
                    tbody_html+="<td>"+value.commission_non_loc_user+"</td>";
                    tbody_html+="<td><a href='"+BASE_URL+"items.php?action=edit&edit_stock_id="+value.stock_id+"' " +
                        "class='btn btn-sm btn-primary'><i class='flaticon-edit'></i></td>";
                    tbody_html+="</tr>";

                });

                $("#service_list_tbody").html(tbody_html);

                $("#service_list_table").DataTable( {
                    dom: 'Bfrtip',
                    buttons: [
                        'colvis'
                    ]
                } );

            }

        });

    </script>

<?php } ?>


