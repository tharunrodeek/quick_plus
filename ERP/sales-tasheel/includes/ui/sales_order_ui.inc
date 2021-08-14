<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <p><?= trans("Fee Details")?></p>


            <?php

            if (isset($_GET['is_tadbeer']) && ($_GET['is_tadbeer'] == 1)) {

            } else {

                ?>

                <p><span><?= trans("Total Amount") ?> : <input type="number" id="total_amount"> <?= trans("Service Charge") ?> : <input type="number"
                                                                                                        id="service_charge"</span>
                </p>
            <?php } ?>

        </div>
        <div class="modal-body">
            <table class="tablestyle" cellpadding="2" cellspacing="0">
                <thead>
                <tr>
                    <th><?= trans("GL ACCOUNT") ?></th>
                    <th><?= trans("AMOUNT") ?></th>
                    <th><?= trans("DESCRIPTION") ?></th>
                </tr>
                </thead>
                <tbody id="other_fee_edit_tbl_body">
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_update_fee_info"><?= trans("Update") ?></button>
        </div>
    </div>
</div>


<!--Request for aditional credit  - Modal Form-->
<div id="req_cr_modal" class="modal modal-sm">
    <!-- Modal content -->
    <div class="modal-content" style="width: 30%">
        <div class="modal-header">
            <span class="close close_rq_model">&times;</span>
            <p>Request for additional credit</p>

        </div>
        <div class="modal-body">
            <input type="hidden" id="req_model_cust_id">
            <table class="tablestyle" cellpadding="2" cellspacing="0">
                <thead>
                <tbody>
                <tr>
                    <td>Customer</td>
                    <td id="req_model_cust_name"></td>
                </tr>
                <tr style="display: none">
                    <td>Requesting Amount</td>
                    <td><input type="number" id="req_amount"></td>
                </tr>

                <tr>
                    <td>Note</td>
                    <td><textarea id="req_note" rows="6" cols="80"></textarea></td>
                </tr>

                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_req_for_approval">Send Request</button>
        </div>
    </div>
</div>


<!--Batch Model-->
<div id="batchModel" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="close batchModelClose">&times;</span>
            <p><?= trans("Auto Batch") ?></p>

        </div>
        <div class="modal-body">
            
            <div style="    height: 400px;
    overflow-y: scroll;">
            <table class="tablestyle" cellpadding="2" cellspacing="0">
                <thead>
                <th><input type="checkbox" class="af_select_all"></th>
                <th><?= trans("Service Name") ?></th>
                <th><?= trans("Amount") ?></th>
                <th><?= trans("Service Charge") ?></th>
                <th><?= trans("Transaction ID") ?></th>
                <th><?= trans("Application ID") ?></th>
                </thead>
                <tbody id="batch_auto_tbody">


                </tbody>
            </table>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="batch_auto_add"><?= trans("Add Selected Items") ?></button>
        </div>
    </div>
</div>

<div id="add_sub_cust_modal" class="modal">
    <!-- Modal content -->
    <div class="modal-content" style="width: 30%">
        <div class="modal-header">
            <span class="close close_add_sub_cust_modal">&times;</span>
            <p>Add Sub Customer</p>
        </div>
        <div class="modal-body">
            <input type="hidden" id="add_sub_cust_modal">
            <table class="tablestyle" cellpadding="2" cellspacing="0">
                <thead>
                <tbody>
                <tr>
                    <td>Sub Customer</td>
                    <td><input type="text" id="sub_cust_name"></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_add_sub_cust" style="background: #100000 !important">Add</button>
        </div>
    </div>
</div>


<?php
if (!in_array($_SESSION['wa_current_user']->access, [9, 2])) {   ?>

    <style>

        input[name="ref"]
        {
            pointer-events: none;
            background: #ccc;
        }

    </style>

<?php } ?>


<?php
if ($_SESSION['wa_current_user']->access!=2) {   ?>

    <style>

        .clsClander
        {
            display:none;
        }

    </style>

<?php } ?>



<?php
if ($_GET['show_items'] == 'tb') {   ?>

    <style>
        input[name="govt_fee"]
        {
            pointer-events: none;
            background: #ccc;
        }

    </style>

<?php } ?>

<?php
if ($_GET['show_items'] == 'ts') {   ?>

    <style>
        #myBtn
        {
            display: none;
        }

    </style>

<?php } ?>

 
<?php if($_GET['EditFlag']==true){  ?>
<input type="hidden" id="hdn_edit_flag" value='1' />
<input type="hidden" id="hdn_invoice_number" value='<?php echo $_GET['NewInvoice']; ?>' />
<?php } ?>



<style>

    input[name="eid_number"] {
        pointer-events: none;
        background: #ccc;
    }


    #company_labour_table {
        /*display: none !important;*/
    }

    input[name="govt_fee"]
    {
        /*pointer-events: none;*/
        /*background: #ccc;*/
    }


    input[name="OrderDate"],input[name="ref"],input[name="token_no"] {
        width: 230px !important;
    }

    select[name="dimension_id"] {
        pointer-events: none;
        background: #ccc;
    }


    #batchModel .modal-content {
        /*width: 900px*/
        width: 65% !important;
    }

    #batchModel table td,#batchModel table th {
        border: 1px solid #ccc !important;
    }

    #btn_update_fee_info,#batch_auto_add {
        background: #151311 !important;
        box-shadow: none !important;
        padding: 0px 20px !important;
        margin: 2px;
    }


    .select2-dropdown, .select2-search__field {
        width: 600px !important;
    }

    .select2-search__field {
        max-width: 580px !important;
    }

    /*body {font-family: Arial, Helvetica, sans-serif;}*/

    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 9999; /* Sit on top */
        padding-top: 100px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0, 0, 0); /* Fallback color */
        background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        position: relative;
        background-color: #fefefe;
        margin: auto;
        padding: 0;
        border: 1px solid #888;
        width: 50%;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        -webkit-animation-name: animatetop;
        -webkit-animation-duration: 0.4s;
        animation-name: animatetop;
        animation-duration: 0.4s
    }

    /* Add Animation */
    @-webkit-keyframes animatetop {
        from {
            top: -300px;
            opacity: 0
        }
        to {
            top: 0;
            opacity: 1
        }
    }

    @keyframes animatetop {
        from {
            top: -300px;
            opacity: 0
        }
        to {
            top: 0;
            opacity: 1
        }
    }

    /* The Close Button */
    .close {
        color: white;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    .modal-header {
        padding: 1px 14px;
        background-color: #ad833b;
        color: white;
    }

    .modal-body {
        padding: 2px 16px;
    }

    .modal-footer {
        padding: 2px 16px;
        background-color: #ad833b;
        color: white;
        text-align: right;
    }

    #btn_update_fee_info, #btn_req_for_approval {
        background: #151311 !important;
        box-shadow: none !important;
        padding: 0px 20px !important;
        margin: 2px;
    }

    /*#header_table .customer-credit-row,*/
    /*!*#header_table*! .customer-branch-list-row,*/
    /*#delivery {*/
    /*display: none;*/
    /*}*/

    #delivery, .sales-type-list-row, .customer-branch-list-row {
        display: none;
    }

    table.tablestyle2 td:nth-child(2) {
        width: 26% !important;
    }
    
    
    #company_labour_table
    {
        display:none;
    }
    
    #payment_card
    {
        /*opacity: 0.6;*/
        /*pointer-events: none;*/
    }
    
    input[name="customer_mobile"],input[name="customer_email"],input[name="customer_trn"],
    input[name="customer_ref"]
    /*,input[name="display_customer"] */
    {
       /*pointer-events: none;*/
       /*background: #ccc;*/
    }
    
    

    /*#_payment_sel < tr {*/
    /*display: none;*/
    /*}*/

    input[name="Disc"], input[name="total_bank_charge"],
    input[name="discount_amount"],
        /*input[name="govt_fee"],
            input[name="qty"], */
    input[name="company_status"]
    {
        pointer-events: none;
        background: #ccc;
    }

    input[name="price"]
    {
        pointer-events: none;
        background: #ccc;
    }

    table.tablestyle td.tableheader {
        color: #fff;
        background: #ad833b !important;
    }

    button {
        background-color: #ad833b !important;
    }

    .navbar .container-fluid {
        background-color: #ad833b;
        color: white;
    }

    /*#stock_id {*/
    /*max-width: 172px !important;*/
    /*}*/

    /*#items_table td[align="right"] {*/
    /*text-align: center !important;*/
    /*}*/

    body {
        /*-moz-transform: scale(0.9);*/
    }

    #items_table table {
        text-align: center !important;
    }

    #_stock_id_edit {
        width: 80px !important;
    }


   /* #items_table table.tablestyle td:nth-child(7) {
        display: none;
    }*/

</style>

<script src="../js/jquery3.3.1.min.js"></script>

<script src="../js/autoFetch_TASHEEL.js"></script>

<script type="text/javascript">

    if (performance.navigation.type == 2) {
        window.location.href = "<?php echo $path_to_root ?>";
    }
    $(document).on("change", "input[name='discount_amount']", function () {

        var discount_amount = $(this).val();
        var price = $("input[name='price']").val();
        var discount_percentage = (discount_amount / price) * 100;

        $("input[name='Disc']").val(discount_percentage.toFixed(2))

    });

    $(document).on("change", "input[name='Disc']", function () {
        // alert(1)
        var discount_percentage = $(this).val();
        var price = $("input[name='price']").val();

        var discount_amount = (price * discount_percentage) / 100;

        $("input[name='discount_amount']").val(discount_amount.toFixed(2));
    });


    $(document).ready(function()
    {
        
        $("select[name='invoice_type']").val('CustomerCard');
         
        
        
        if($('#hdn_edit_flag').val()=='1')
        {
            $.ajax({
                url: "sub_customers.php?edit_payment_type=1",
                type: "post",
                dataType: 'json',
                data: {
                    edit_invoice_number: $('#hdn_invoice_number').val()
                },
                success: function (response) {
                    
                    if(response.payment_type=='CenterCard')
                    {
                        $("#payment_card option:first").attr("disabled", "true");
                    }
                     
                    
                        
                          $("select[name='invoice_type']").val(response.edit_invoice_card);
                          $("#payment_card").val(response.edit_pay_type);
                          
                        
                },
                error: function (xhr) {
                }
            });
        
        }           
         
         
        
    });






    // $(document).on("change", "input[name='token_no']", function () {
    //
    //
    //
    //     //$("input[name='invoice_type']").val('CustomerCard');
    //
    //     $.ajax({
    //         url: "sub_customers.php?reception_data=1",
    //         type: "post",
    //         dataType: 'json',
    //         data: {
    //             token_id: $(this).val()
    //         },
    //         success: function (response) {
    //
    //
    //            /* if(response.payment_type=='CenterCard')
    //             {
    //                 $("#payment_card option:first").attr("disabled", "true");
    //             }*/
    //
    //              if(response.status=='SUCCESS')
    //              {
    //                   $("select[name='invoice_type']").val(response.payment_type.trim());
    //                   $("#payment_card").val(response.invoice_payment_card.trim());
    //              }
    //
    //
    //
    //                $("select[name='customer_id']").trigger('change');
    //
    //
    //
    //         },
    //         error: function (xhr) {
    //         }
    //     });
    //
    //
    //
    //
    // });

    $(document).on("change", "input[name='token_no']", function () {

        $("select[name='customer_id']").trigger('change')

    });
    
    $(document).on("change", "select[name='invoice_type']", function () {
        
        
        
        $.ajax({
            url: "sub_customers.php?PAYMENT_CARD=1",
            type: "post",
            dataType: 'json',
            data: {
                invoice_type_id: $(this).val()
            },
            success: function (response) {
                
                 if(response.displ_invoice_type=='CenterCard')
                 {
                    //$( "#payment_card").children("option")[0].remove();\
                    $("#payment_card option:first").attr("disabled", "true");
                 }
                 
                
                    
                $("#payment_card").val(response.displ_payment_card);
                      
                 
            },
            error: function (xhr) {
            }
        });
        
        
        
        
    });
    
    
    
    


    $(document).on("change", "select[name='customer_type']", function () {

        $("select[name='customer_id']").trigger('change')

    });


    document.addEventListener("keydown", function (event) {

        if (event.keyCode == 117)
            $(".item_list_options").trigger('click');
    });

    $(document).on("change", "input[name='labour_contract_number']", function () {

        var labour_contract_number = $(this).val();

        if (labour_contract_number.length <= 0)
            return false;

        $.ajax({
            url: "labour_contract_info.php",
            type: "get", //send it through get method
            dataType: "json", //send it through get method
            data: {
                lcno: labour_contract_number
            },
            success: function (response) {

                if (response.d == null) {
                    alert("Please provide valid labour contract number");
                    $("input[name='labour_contract_number']").val("").focus();
                    $("#mol_company_name").html("");
                    $("#mol_company_status").html("");
                    $("#sales_type").val(null);
                    $("#mol_company_category").val(null);
                } else {

                    var value = response.d.Category;
                    $("#mol_company_name").html(response.d.CompanyName);
                    $("#mol_company_status").html(response.d.CompanyStatus);

                    $("input[name='display_customer']").val(response.d.CompanyName);

                    $('#mol_company_category option:contains(' + value + ')').each(function () {
                        if ($(this).text() == value) {
                            $(this).attr('selected', 'selected');
                            // $(this).trigger("change");
                            $('#mol_company_category').trigger("change");
                            // return false;
                        }
                        return true;
                    });
                }
            },
            error: function (xhr) {
            }
        });
    });


    $(document).on("change", "#company_code", function () {

        var labour_contract_number = $(this).val();

        if (labour_contract_number.length <= 0)
            return false;

        $.ajax({
            url: "labour_contract_info.php",
            type: "get", //send it through get method
            dataType: "json", //send it through get method
            data: {
                lcno: labour_contract_number
            },
            success: function (response) {

                if (response.d == null) {
                    $("#sub_cust_name").val(null);
                } else {

                    var value = response.d.Category;
                    $("#sub_cust_name").val(response.d.CompanyName);
                }
            },
            error: function (xhr) {
            }
        });
    });


    //FOR MODAL
    var modal = document.getElementById('myModal');

    // Get the button that opens the modal
    var btn = document.getElementById("myBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];


    // When the user clicks on <span> (x), close the modal
    span.onclick = function () {
        modal.style.display = "none";
    };

    //FOR REQUESY MODAL
    var req_modal = document.getElementById('req_cr_modal');


    // Get the button that opens the modal
    // var req_btn = document.getElementById("req_credit_button");

    // Get the <span> element that closes the modal
    var req_span = document.getElementsByClassName("close_rq_model")[0];


    // When the user clicks on <span> (x), close the modal
    req_span.onclick = function () {
        req_modal.style.display = "none";
    };


    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == modal || event.target == req_modal) {
            modal.style.display = "none";
            req_modal.style.display = "none";
        }
    };


    $(document).ready(function (event) {
        $(document).on("change", "#mol_company_category", function () {
            $("#sales_type option").hide();
            var value = $(this).val();
            $('#sales_type option:contains(' + value + ')').each(function () {
                $(this).show();
                return true;
            });
        });


        var current_fee_json_input;
        $(document).on("click", ".other_fee_detailed", function () {

            current_fee_json_input = $(this);
            modal.style.display = "block";
            var fee_info = $(this).parents("tr").find("input[name='other_fee_info_json']").val();

            var fee_info_json = $.parseJSON(atob(fee_info));
            var tbody = "";
            $.each(fee_info_json, function (key, value) {


                console.log(value);

                var row_class = "oddrow"
                if (((key + 1) % 2) == 0) {
                    row_class = "evenrow";
                }
                tbody += "<tr class='fee_tr " + row_class + "'>";
                tbody += "<td>" +
                    "<input type='hidden' class='edit_other_acc' value='" + value.account_code + "'>" +
                    "<input type='hidden' class='edit_other_acc_name' value='" + value.account_name + "'>" +
                    "<label class=''>" + value.account_code + "-" + value.account_name + "</label></td>";
                tbody += "<td><input class='edit_other_amount' type='number' value='" + value.amount + "'></td>";
                tbody += "<td><input class='edit_other_desc' type='text' value='" + value.description + "'></td>";
                tbody += "</tr>";

            });
            $("#other_fee_edit_tbl_body").html(tbody);
        });

        $(document).on("click", "#btn_update_fee_info", function (e) {

            var fee_object = [];
            var total_govt_fee = 0;

            var total_amount = $("#total_amount").val();
            var service_charge = $("#service_charge").val();


            if (total_amount == '' || !total_amount) total_amount = 0;
            if (service_charge == '' || !service_charge) service_charge = 0;

            var index = 0;
            $(".fee_tr").each(function (x) {
                var acc_code = $(this).find(".edit_other_acc").val();
                var acc_desc = $(this).find(".edit_other_desc").val();
                var acc_amount = $(this).find(".edit_other_amount").val();
                var acc_name = $(this).find(".edit_other_acc_name").val();


                if ((index == 0) && (total_amount != 0) && (service_charge != 0)) {
                    acc_amount = parseFloat(total_amount) - parseFloat(service_charge);
                }


                var fee_array = {
                    'account_code': acc_code,
                    'amount': acc_amount,
                    'description': acc_desc,
                    'account_name': acc_name
                };


                total_govt_fee += parseFloat(acc_amount);
                fee_object.push(fee_array);

                index++;
            });

            var stringyfy_json_encoded = btoa(JSON.stringify(fee_object));
            current_fee_json_input.parents("tr").find("input[name='other_fee_info_json']").val(stringyfy_json_encoded)
            modal.style.display = "none";


            $("input[name='govt_fee']").val(total_govt_fee.toFixed(2))


            if (service_charge != '' && service_charge != 0) {
                $("input[name='price']").val(parseFloat(service_charge).toFixed(2));
            }

        });


    });
    // Get the modal

    var tasheel_popup;
    $(document).on("click", "#tasheel-link", function () {
        // alert(1)
        // tasheel_popup=lookupWindow("", "");
        tasheel_popup = window.open("http://www.mohre.gov.ae/en/services/enquiry-services.aspx", "Popup", "width=300,height=100");

        setTimeout(function () {
            console.log(tasheel_popup.getElementById('keywords'))
        }, 30000)
    });


    $(document).on("click", ".req_credit_button", function () {

        req_modal.style.display = "block";

        var customer_id = $('#customer_id').val();
        var customer_name = $('#customer_id option:selected').text();

        $("#req_model_cust_name ").html(customer_name);
        $("#req_model_cust_id").val(customer_id);

        $("#req_note").val("");
        $("#req_amount").val(0);


    });

    $(document).on("click", "#btn_req_for_approval", function () {

        var customer_id = $("#req_model_cust_id").val();
        var req_amount = $("#req_amount").val();
        var req_desc = $("#req_note").val();


        $("#ajaxmark").attr("visibility", "visible");
        $.ajax({
            url: "../sales/credit_requests_ajax.php?cr_request=1",
            type: "post",
            dataType: 'json',
            data: {
                customer_id: customer_id,
                req_amount: req_amount,
                req_desc: req_desc
            },
            success: function (response) {
                $("#ajaxmark").attr("visibility", "hidden");
                alert(response.msg);
                req_modal.style.display = "none";
            },
            error: function (xhr) {
            }
        });


    });



    $( document ).ready(function() {
        // $('input[name="price"]').attr('disabled', 'disabled');
    });


    var add_sub_cust_modal = document.getElementById('add_sub_cust_modal');
    var add_sub_cust_close = document.getElementsByClassName("close_add_sub_cust_modal")[0];
    add_sub_cust_close.onclick = function () {
        add_sub_cust_modal.style.display = "none";
    };

    window.onclick = function (event) {
        if (event.target == add_sub_cust_modal) {
            add_sub_cust_modal.style.display = "none";
        }
    };

    $(document).on("click", ".add_sub_customer_button", function () {

        add_sub_cust_modal.style.display = "block";

    });

    $(document).on("click", "#btn_add_sub_cust", function () {

        var customer_id = $('#customer_id').val();
        var customer_name = $('#customer_id option:selected').text();

        var sub_cust_name = $("#sub_cust_name").val();


        $("#ajaxmark").attr("visibility", "visible");
        $.ajax({
            url: "sub_customers.php?add_new=1",
            type: "post",
            dataType: 'json',
            data: {
                customer_id: customer_id,
                sub_cust_name: sub_cust_name
            },
            success: function (response) {
                $("#ajaxmark").attr("visibility", "hidden");
                alert(response.msg);
                add_sub_cust_modal.style.display = "none";

                $('#customer_id').trigger("change");

                setTimeout(function () {
                    $("select[name='sub_customer_list']").val(response.id);
                    $("input[name='display_customer']").val(sub_cust_name);
                },2000);


            },
            error: function (xhr) {
            }
        });



    });



    // $(document).on("change", "select[name='sub_customer_list']", function () {
    //
    //     var sub_customer_name = $("select[name='sub_customer_list'] option:selected").text();
    //
    //     $("input[name='display_customer']").val(sub_customer_name);
    //
    //     // $("input[name='contact_person']").val(sub_customer_name);
    //
    // });

    $(document).on("change", "select[name='sub_customer_list']", function () {

        var sub_customer_name = $("select[name='sub_customer_list'] option:selected").text();

        if($(this).val() === '0') {
            sub_customer_name = "";
        }

        $("input[name='display_customer']").val(sub_customer_name);
        // $("input[name='contact_person']").val(sub_customer_name);

    });


    $(document).on("click", ".create_cust_button", function () {

        var e = jQuery.Event("keydown");
        e.which = 113;
        $("input").trigger(e);

    });





</script>


<?php
/**********************************************************************
 * Direct Axis Technology L.L.C.
 * Released under the terms of the GNU General Public License, GPL,
 * as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 ***********************************************************************/
include_once($path_to_root . "/sales-tasheel/includes/cart_class.inc");


//--------------------------------------------------------------------------------
function add_to_order(&$order, $new_item, $new_item_qty, $price, $discount, $description = '',
                      $govt_fee = 0, $bank_service_charge = 0, $bank_service_charge_vat = 0,
                      $transaction_id = null, $discount_amount = null,
                      $other = null, $application_id = null, $govt_bank_account = null, $ref_name = null, $ed_transaction_id = null)
{

    // calculate item price to sum of kit element prices factor for
    // value distribution over all exploded kit items
    $std_price = get_kit_price($new_item, $order->customer_currency,
        $order->sales_type, $order->price_factor, get_post('OrderDate'), true);

    if ($std_price == 0)
        $price_factor = 0;
    else
        $price_factor = $price / $std_price;

    $kit = get_item_kit($new_item);
    $item_num = db_num_rows($kit);

    while ($item = db_fetch($kit)) {
        $std_price = get_kit_price($item['stock_id'], $order->customer_currency,
            $order->sales_type, $order->price_factor, get_post('OrderDate'), true);

        // rounding differences are included in last price item in kit
        $item_num--;
        if ($item_num) {
            $price -= $item['quantity'] * $std_price * $price_factor;
            $item_price = $std_price * $price_factor;
        } else {
            if ($item['quantity'])
                $price = $price / $item['quantity'];
            $item_price = $price;
        }
        $item_price = round($item_price, user_price_dec());


//        $item_price = isset($_POST['price']) ? round($_POST['price'], user_price_dec()): $item_price;

        if (!$item['is_foreign'] && $item['item_code'] != $item['stock_id']) {    // this is sales kit - recurse


            add_to_order($order, $item['stock_id'], $new_item_qty * $item['quantity'],
                $item_price, $discount, $govt_fee, $bank_service_charge, $bank_service_charge_vat, $transaction_id);


        } else {    // stock item record eventually with foreign code


            // check duplicate stock item
//            foreach ($order->line_items as $order_item) {
//                if (strcasecmp($order_item->stock_id, $item['stock_id']) == 0) {
//                    display_warning(trans("For Part :") . $item['stock_id'] . " "
//                        . trans("This item is already on this document. You have been warned."));
//                    break;
//                }
//            }

            $order->add_to_cart(count($order->line_items), $item['stock_id'],
                $new_item_qty * $item['quantity'], $item_price, $discount, 0, 0, $description, null, null, null,
                $govt_fee, $bank_service_charge, $bank_service_charge_vat,
                $transaction_id, $discount_amount, $other, $application_id, $govt_bank_account, $ref_name, $ed_transaction_id);
        }
    }


}

//---------------------------------------------------------------------------------

function get_customer_details_to_order(&$order, $customer_id, $branch_id)
{

    global $SysPrefs;
    $ret_error = "";
    $myrow = get_customer_to_order($customer_id);


    $customer_email = $myrow['debtor_email'] ? $myrow['debtor_email'] : '';
    $customer_mobile = $myrow['mobile'] ? $myrow['mobile'] : '';
    $order->tax_id = $myrow["tax_id"];
    $name = $myrow['name'];

    if ($myrow['dissallow_invoices'] == 1)
        $ret_error = trans("The selected customer account is currently on hold. Please contact the credit control personnel to discuss.");

    $deliver = $myrow['address']; // in case no branch address use company address

    $order->set_customer($customer_id, $name, $myrow['curr_code'],
        $myrow['discount'], $myrow['payment_terms'], $myrow['pymt_discount']);

    // the sales type determines the price list to be used by default
    $order->set_sales_type($myrow['salestype'], $myrow['sales_type'], $myrow['tax_included'],
        $myrow['factor']);

//    display_error(print_r($myrow['salestype'],true));

//    $order->set_invoice_display_details($_POST['display_customer'],$_POST['customer_trn'],
//        $_POST['mobile'],$_POST['email'],$_POST['customer_ref']);

    $order->credit = $myrow['cur_credit'];

    if ($order->trans_type != ST_SALESORDER && $order->trans_type != ST_SALESQUOTE) {
        $order->dimension_id = $myrow['dimension_id'];
        $order->dimension2_id = $myrow['dimension2_id'];
    }
    $result = get_branch_to_order($customer_id, $branch_id);

    if (db_num_rows($result) == 0) {
        return trans("The selected customer and branch are not valid, or the customer does not have any branches.");
    }

    $myrow = db_fetch($result);

    // FIX - implement editable contact selector in sales order
    $contact = get_branch_contacts($branch_id, 'order', $customer_id);

    $order->set_branch($contact, $myrow["tax_group_id"],
        $myrow["tax_group_name"], @$customer_mobile, @$customer_email);

    $address = trim($myrow["br_post_address"]) != '' ? $myrow["br_post_address"]
        : (trim($myrow["br_address"]) != '' ? $myrow["br_address"] : $deliver);

    $order->set_delivery($myrow["default_ship_via"], $myrow["br_name"],
        $address);
    if ($order->trans_type == ST_SALESINVOICE) {
        $order->due_date = get_invoice_duedate($order->payment, $order->document_date);
    } elseif ($order->trans_type == ST_SALESORDER)
        $order->due_date = add_days($order->document_date, $SysPrefs->default_delivery_required_by());
    elseif ($order->trans_type == ST_SALESQUOTE)
        $order->due_date = add_days($order->document_date, $SysPrefs->default_quote_valid_days());

    if ($order->payment_terms['cash_sale']) {
        $order->set_location($order->pos["pos_location"], $order->pos["location_name"]);
    } else
        $order->set_location($myrow["default_location"], $myrow["location_name"]);


//    $order->phone = $customer_mobile;
//    $order->email = $customer_email;

    $order->set_token_data();

    return $ret_error;
}

//---------------------------------------------------------------------------------

function display_order_summary($title, &$order, $editable_items = false)
{

    display_heading($title);

    div_start('items_table');
    start_table(TABLESTYLE, "width='90%'");


    $trans_id_one_text = trans("NOQODI - ID");
    $trans_id_two_text = trans("MB NO");

    if ($order->show_items == 'ts')
        $trans_id_one_text = trans("MB NO");

    $th = array(trans("Item Code"),
        trans("Item Description"),
        trans("QTY"),
        trans("Delivered"),
//        trans("Unit"),
        trans("Total Charge"),
        //$order->tax_included ? trans("Price after Tax") : trans("Service Charge"),
        trans("$trans_id_one_text"),
       // trans("$trans_id_two_text"),
        trans("Application ID"),
         trans("Narration"),
        trans("Total"), "");

    if ($order->trans_no == 0) {
        unset($th[3]);
    }

    //IF TASHEEL, HIDE EXTRA TRANSACTION ID INPUT
    if ($order->show_items == 'ts') {
        //unset($th[7]);
    }

    if (count($order->line_items))
        $th[] = '';

    table_header($th);

    $total = 0;
    $k = 0;  //row colour counter

    $id = find_submit('Edit');

    $low_stock = $order->check_qoh($_POST['OrderDate'], $_POST['Location']);
    foreach ($order->get_items() as $line_no => $stock_item) {

//      $line_total = round($stock_item->qty_dispatched *
//            ($stock_item->price+$stock_item->govt_fee+$stock_item->bank_service_charge+$stock_item->bank_service_charge_vat) *
//            (1 - $stock_item->discount_percent),
//         user_price_dec());


//        $stock_item->price= isset($_POST['price']) ? $_POST['price'] : $stock_item->price;

        $line_total = round($stock_item->qty_dispatched *
            ($stock_item->price + $stock_item->govt_fee + $stock_item->bank_service_charge +
                $stock_item->bank_service_charge_vat) -
            ($stock_item->discount_amount * $stock_item->qty_dispatched) + $stock_item->discount_amount,
            user_price_dec());

        if (isset($order->is_tadbeer) && ($order->is_tadbeer == 1)) {

            $line_total = $line_total - ($stock_item->price * $stock_item->qty_dispatched);

        }


//        $line_total = round($stock_item->qty_dispatched * $stock_item->price * (1 - $stock_item->discount_percent) +
//            ($stock_item->govt_fee + $stock_item->bank_service_charge + $stock_item->bank_service_charge_vat),
//            user_price_dec());


//        /** Modifications for AMER */
//        $item_price+=$govt_fee;
//        $item_price+=$bank_service_charge;
//        $item_price+=$bank_service_charge_vat;

        $qoh_msg = '';
        if (!$editable_items || $id != $line_no) {

//            display_error(print_r($stock_item ,true));

            if (in_array($stock_item->stock_id, $low_stock))
                start_row("class='stockmankobg'");    // notice low stock status
            else
                alt_table_row_color($k);

            view_stock_status_cell($stock_item->stock_id);

            label_cell($stock_item->item_description);
            $dec = get_qty_dec($stock_item->stock_id);
            qty_cell($stock_item->qty_dispatched, false, $dec);

            if ($order->trans_no != 0)
                qty_cell($stock_item->qty_done, false, $dec);

            amount_cell($stock_item->govt_fee);


//            display_error(print_r($stock_item->price,true));

           // amount_cell($stock_item->price);

            /** Modifications for AMER */

//            amount_cell($stock_item->bank_service_charge + $stock_item->bank_service_charge_vat);
//            amount_cell($stock_item->bank_service_charge_vat);

//            amount_cell($stock_item->discount_amount);
//            percent_cell($stock_item->discount_percent * 100);

            label_cell($stock_item->ed_transaction_id);

          /*  if ($order->show_items == 'tb') {
                label_cell($stock_item->ed_transaction_id);
            }*/


 label_cell($stock_item->application_id);
          label_cell($stock_item->ref_name);


            amount_cell($line_total);

            if ($editable_items) {
                edit_button_cell("Edit$line_no", trans("Edit"),
                    trans('Edit document line'));
                delete_button_cell("Delete$line_no", trans("Delete"),
                    trans('Remove line from document'));
            }
            end_row();
        } else {
            sales_order_item_controls($order, $k, $line_no);
        }

        $total += $line_total;
    }

//    $total = $total-$stock_item->price * (1 - $stock_item->discount_percent);

    if ($id == -1 && $editable_items)
        sales_order_item_controls($order, $k);

    $colspan = 7;
    if ($order->trans_no != 0)
        ++$colspan;


    //IF TASHEEL, Reduce Cols Span, coz extra transaction id input is hidden already
    if ($order->show_items == 'ts') {
        $colspan = 6;
    }

    start_row("style='display:none'");
    label_cell(trans("Shipping Charge"), "colspan=$colspan align=right");
    small_amount_cells(null, 'freight_cost', price_format(get_post('freight_cost', 0)));
    label_cell('', 'colspan=2');
    end_row();
    $display_sub_total = price_format($total + input_num('freight_cost'));
    label_row(trans("Sub-total"), $display_sub_total, "colspan=$colspan align=right", "align=right", 2);
    $taxes = $order->get_taxes(input_num('freight_cost'));

    $tax_total = display_edit_tax_items($taxes, $colspan, $order->tax_included, 2);


    $display_total = price_format(($total + input_num('freight_cost') + $tax_total));


    start_row();
    label_cells(trans("Amount Total"), $display_total, "colspan=$colspan align=right", "align=right");
    //submit_cells('update', trans("Update"), "colspan=2 align='center'", trans("Refresh"), true);
    end_row();

    end_table();
    if ($low_stock)
        display_note(trans("Marked items have insufficient quantities in stock as on day of delivery."), 0, 1, "class='stockmankofg'");

    div_end();


    set_focus('ed_transaction_id');

}

// ------------------------------------------------------------------------------

function display_order_header(&$order, $editable, $date_text)
{
    global $Ajax, $SysPrefs;


    /** FOR TASHEEL */
    start_outer_table(TABLESTYLE2, "width='80%' id='company_labour_table'");
    table_section(1);
//    $_POST['sales_type']=null;
    text_row(trans("LABOUR")."#:", "labour_contract_number", null, 30, 30);
    table_section(2);

    $company_categories = [
        "2-A" => "2-A",
        "2-B" => "2-B",
        "2-C" => "2-C",
        "2-D" => "2-D"
    ];

    $sequences = array(
        'Week End' => trans("Week End Date"),
        'Gross Sales' => trans("Gross Sales")
    );

    label_cell(trans("CATEGORY").":");

    echo '</td><td><select id="mol_company_category" autocomplete="off" name="mol_company_category" class="combo" title="" _last="0">
<option selected="" value="0">select</option>
<option value="2-A">2-A</option>
<option value="2-B">2-B</option>
<option value="2-C">2-C</option>
<option value="D">D</option>

</select></td>';


    if (!isset($order->sales_type) || empty($order->sales_type)) {
        $order->sales_type_id = 1;
    } else if ($order->sales_type_id != $_POST['sales_type']) {
        $order->sales_type_id = $_POST['sales_type'];
    }


    sales_types_list_row(trans("POSITION:"), 'sales_type', $order->sales_type_id, true, 'select');

    table_section(3);
    label_cell(trans("COMPANY").":");
    label_cell(null, "id=mol_company_name");

    table_section(4);
    label_cell(trans("STATUS").":");
    label_cell(null, "id=mol_company_status");
    end_outer_table(2); // outer table


    start_outer_table(TABLESTYLE2, "width='80%' id='header_table'");

    table_section(1);

    $customer_error = "";
    $change_prices = 0;


    text_row(trans("Token/Mobile"), 'token_no', null, 28, 100, 'Token Number');
     $Ajax->activate('invoice_type');

    ref_row(trans("Invoice No") . ':', 'ref', trans('Reference number unique for this document type'), null, '');


    if ($order->sales_type != $_POST['sales_type']) {


        $myrow = get_sales_type($_POST['sales_type']);
        $order->set_sales_type($myrow['id'], $myrow['sales_type'],
            $myrow['tax_included'], $myrow['factor']);
        $Ajax->activate('sales_type');
        $change_prices = 1;

//        display_error(print_r($order->sales_type,true));


    }


    if ($editable) {
        if (!isset($_POST['OrderDate']) || $_POST['OrderDate'] == "")
            $_POST['OrderDate'] = $order->document_date;


        $_POST['OrderDate'] = (isset($order->edit_document_date) && !empty($order->edit_document_date)) ?
            $order->edit_document_date : $_POST['OrderDate'];

        date_row($date_text, 'OrderDate', trans('Date of order receive'),
            $order->trans_no == 0, 0, 0, 0, null, true);
        if (isset($_POST['_OrderDate_changed']) || list_updated('payment')) {
            if (!is_company_currency($order->customer_currency)
                && (get_base_sales_type() > 0)) {
                $change_prices = 1;


            }
            $Ajax->activate('_ex_rate');
            if ($order->trans_type == ST_SALESINVOICE) {
                $_POST['delivery_date'] = get_invoice_duedate(get_post('payment'), get_post('OrderDate'));
            } else
                $_POST['delivery_date'] = add_days(get_post('OrderDate'), $SysPrefs->default_delivery_required_by());
            $Ajax->activate('items_table');
            $Ajax->activate('delivery_date');
        }
        if ($order->trans_type != ST_SALESORDER && $order->trans_type != ST_SALESQUOTE) {    // 2008-11-12 Joe Hunt added dimensions
            $dim = get_company_pref('use_dimension');
//          if ($dim > 0)
//              dimensions_list_row(trans("Dimension").":", 'dimension_id',
//                  null, true, ' ', false, 1, false);
//          else
//              hidden('dimension_id', 0);
//          if ($dim > 1)
//              dimensions_list_row(trans("Dimension")." 2:", 'dimension2_id',
//                  null, true, ' ', false, 2, false);
//          else
//              hidden('dimension2_id', 0);
        }
    } else {
        label_row($date_text, $order->document_date);
        hidden('OrderDate', $order->document_date);
    }

    if (isset($order) && !$editable) {

        label_row(null, $order->customer_name . " - " . $order->deliver_to);
        hidden('customer_id', $order->customer_id);
        hidden('branch_id', $order->Branch);
        hidden('sales_type', $order->sales_type);
        if ($order->trans_type != ST_SALESORDER && $order->trans_type != ST_SALESQUOTE) {
            hidden('dimension_id', $order->dimension_id); // 2008-11-12 Joe Hunt
            hidden('dimension2_id', $order->dimension2_id);
        }
    } else {

          
//print_r($_POST);
        //SET DATA BY TOKEN
        if (isset($_POST['token_no']) && !empty($_POST['token_no'])) {
            
            $token_customer = $order->get_customer_by_token($_POST['token_no']);


//            pp($token_customer);
           
           // print_r($_POST);
              
            if(!empty($token_customer))
            {
                $_POST['customer_id'] = !empty($token_customer) ? $token_customer['customer_id'] : 1;
                
                $label_payment_type='';
                 
              /* if($token_customer['payment_type']=='1')
                  {
                       $label_payment_type='CenterCard';
                  }
                  else if($token_customer['payment_type']=='2')
                  {
                      $label_payment_type='CustomerCard'; 
                  }*/
                  
                  //$_POST['invoice_type']=$label_payment_type;
                
               
            }

            if (empty($token_customer)) {
                   display_warning("Cant find this Token/Mobile, Please continue...");
            }
        }
 


        $options = array('select_submit' => true, 'disabled' => null, 'id' => 'customer_type');
        $select_opt = array(
            "" => trans("--Select--"),
            "1" => trans("Company / P.R.O"),
            "2" => trans("Staff")
        );
        echo '<tr style="display: none"><td class="label">'.trans("Customer Type"). '</td><td>' . array_selector('customer_type', null, $select_opt, $options) . '</td> </tr>';


        $customer_type = $_POST['customer_type'];
        $where_cond = '';
        if(!empty($customer_type)) {
            if($customer_type == 2)
                $where_cond = 'is_employee=1';

            if($customer_type == 1)
                $where_cond = 'is_employee=0';

            $Ajax->activate('customer_id');
            $Ajax->activate('branch_id');
        }



        hidden("eid_number",null);

//        text_row(trans("EID Number:"), "eid_number", $order->token_number,
//            30, 30, null, "", "<a id='eidNumFilter' type='button'
//class='btn btn-sm btn-primary' style='color: white' href='#'>FETCH EID</a>","","autocomplete=AxisproDaxis readonly");


        text_cells("","emptyInput"," ","","",
            "","","","id='emptyInput' style='display:none'");



        customer_list_row(trans("Customer:"), 'customer_id', null, false, true, false, true,$where_cond);

        if ($order->customer_id != get_post('customer_id', -1)) {
            // customer has changed
            $Ajax->activate('branch_id');
        }





//        hidden('invoice_type','Cash');

        customer_branches_list_row(trans("Branch:"),
            $_POST['customer_id'], 'branch_id', null, false, true, true, true);

        if (($order->customer_id != get_post('customer_id', -1)) ||
            ($order->Branch != get_post('branch_id', -1)) ||
            list_updated('customer_id')) {

            if (!isset($_POST['branch_id']) || $_POST['branch_id'] == "") {
                // ignore errors on customer search box call
                if ($_POST['customer_id'] == '')
                    $customer_error = trans("No customer found for entered text.");
                else
                    $customer_error = trans("The selected customer does not have any branches. Please create at least one branch.");
                unset($_POST['branch_id']);
                $order->Branch = 0;
            } else {

                $old_order = (PHP_VERSION < 5) ? $order : clone $order;


                $customer_error = get_customer_details_to_order($order, $_POST['customer_id'], $_POST['branch_id']);

//                display_error(print_r($order->sales_type,true));

                $order->sales_type = $_POST['sales_type'];

                $_POST['Location'] = $order->Location;
                $_POST['deliver_to'] = $order->deliver_to;
                $_POST['delivery_address'] = $order->delivery_address;
                $_POST['phone'] = $order->phone;
                $_POST['delivery_date'] = $order->due_date;

                if (!in_array($order->trans_type, array(ST_SALESQUOTE, ST_SALESORDER))
                    && ($order->pos['cash_sale'] != $order->pos['credit_sale'])
                    && (($order->payment_terms['cash_sale'] && !$order->pos['cash_sale']) ||
                        (!$order->payment_terms['cash_sale'] && !$order->pos['credit_sale']))) {
                    // force payment terms refresh if terms are editable
                    // and pos have no permitions for terms selected in customer record.
                    // Terms are set to first terms in allowed category below.
                    display_warning(
                        sprintf(trans("Customer's payment terms '%s' cannot be selected on this POS"),
                            $order->payment_terms['terms']));
                    $order->payment = '';
                } elseif (get_post('payment') !== $order->payment) {
                    $_POST['payment'] = $order->payment;
                    $Ajax->activate('delivery');
                    $Ajax->activate('payment');
                } else {
                    if ($order->trans_type == ST_SALESINVOICE) {
                        $_POST['delivery_date'] = $order->due_date;
                        $Ajax->activate('delivery_date');
                    }
                    $Ajax->activate('Location');
                    $Ajax->activate('deliver_to');
                    $Ajax->activate('phone');
                    $Ajax->activate('delivery_address');
                }
                // change prices if necessary
                // what about discount in template case?
                if ($old_order->customer_currency != $order->customer_currency) {
                    $change_prices = 1;

                }
                if ($old_order->sales_type != $order->sales_type) {
                    //  || $old_order->default_discount!=$order->default_discount
                    $_POST['sales_type'] = $order->sales_type;
                    $Ajax->activate('sales_type');


                    $change_prices = 1;
                }
                if ($old_order->dimension_id != $order->dimension_id) {
                    $_POST['dimension_id'] = $order->dimension_id;
                    $Ajax->activate('dimension_id');
                }
                if ($old_order->dimension2_id != $order->dimension2_id) {
                    $_POST['dimension2_id'] = $order->dimension2_id;
                    $Ajax->activate('dimension2_id');
                }
                unset($old_order);
            }
            set_global_customer($_POST['customer_id']);
        } // changed branch
        else {
            $row = get_customer_to_order($_POST['customer_id']);
            if ($row['dissallow_invoices'] == 1)
                $customer_error = trans("The selected customer account is currently on hold. Please contact the credit control personnel to discuss.");
        }

        //DISABLED_TEMPORARLY
//        $customer_error = validate_original_customer($_POST['customer_id']);

    }

    table_section(2);


    $options = array('select_submit' => true, 'disabled' => null, 'id' => 'sub_customer_list');
    $select_opt = get_sub_customers($_POST['customer_id']);

    $sub_customer_label='';
    if($_POST['customer_id']=='1')
    {
        $sub_customer_label='Sub Customers';
    }
    else
    {
        $sub_customer_label='Company/Customer Name:';
    }

    if(get_post('sub_customer_list')!='')
    {
         $order->sub_customer_id=get_post('sub_customer_list');
    }
    else if($order->sub_customer_id==0)
    {
         $order->sub_customer_id=null;
    }





    //if(count($select_opt) > 1) {
        echo '<tr style="display: none">
               <td class="label">'.$sub_customer_label.' </td><td>' . array_selector('sub_customer_list', $order->sub_customer_id, $select_opt, $options) .
             ' <button type="button" class="add_sub_customer_button btn btn-primary btn-sm">Add Sub Customer</button> </td> </tr>';

  //  }

    $display_contact_name='';

    if($_GET['EditFlag']==true)
    {
        $display_contact_name= $order->contact_person;
    }
    else
    {
        $display_contact_name= $order->customer_name;
    }


//    text_row(trans("Contact Person:"), "contact_person",$display_contact_name, 30, 30);
    
    hidden("contact_person","");
    
    text_row(trans("Company/Customer Name:"), "display_customer", $order->customer_name, 30, 30);


//    if($_POST['customer_id']=='1')
//    {
//        text_row(trans("Company/Customer Name:"), "display_customer", $order->customer_name, 30, 30);
//    }
//    else
//    {
//        hidden("display_customer",$order->customer_name);
//    }


    text_row(trans("Mobile:"), "customer_mobile", $order->phone, 30, 30);
    text_row(trans("Email:"), "customer_email", $order->email, 30, 30);

    hidden("staff_mistake_invoice",null);

//    check_row(trans("Staff Mistake Invoice:"),'staff_mistake_invoice',null,true);

    //hidden("contact_person");


    $get_users = get_users();

    $select_opt = [];

    while ($row = db_fetch($get_users)) {
        $select_opt[$row['id']] = $row['user_id'] . "-" . $row['real_name'];
    }

    $options = array('select_submit' => true, 'disabled' => null,
        'id' => 'mistake_staff_id', 'spec_option' => 'Select a staff');

    $Ajax->activate('mistake_staff_id');

    $staff_mistake_cust_id = $SysPrefs->prefs['staff_mistake_customer_id'];

    if (empty($staff_mistake_cust_id))
        $customer_error = "Staff Mistake Default Customer is not set. Please change the settings in System & GL Setup";


//    pp(check_value('staff_mistake_invoice'));

    $style = "display:none";
    if (check_value('staff_mistake_invoice') == 1) {
        $style = "";
    }

    echo '<tr style="' . $style . '" data-staff_mistake_cust_id="' . $staff_mistake_cust_id . '" id="mistake_staff_id_tr"><td class="label">Staff</td><td>' . array_selector('mistake_staff_id', $order->mistake_staff_id, $select_opt, $options) . '</td> </tr>';


    $options = array('select_submit' => false, 'disabled' => null, 'id' => 'invoice_type');
    $select_opt = array(
        //"" => "--Select--",
        "CustomerCard" => "Customer Card",
        "CenterCard" => "Center Card"

    );
    
   
   
 // print_r($_POST['invoice_type'].' ---- '.$label_payment_type );
   
   
   
  
   /*else
   {
       if(!isset($_POST['invoice_type']))
       {
           $_POST['invoice_type'] = "CustomerCard";
       }
   }*/

     //if(!isset($_POST['invoice_type']))
        //$_POST['invoice_type'] = "CustomerCard";
    
     
    echo '<tr style="display: none"><td class="label">'.trans("Payment Type").' </td><td>' . array_selector('invoice_type', $_POST['invoice_type'], $select_opt, $options) . '</td> </tr>';



    if (!is_company_currency($order->customer_currency) && in_array($order->trans_type, array(ST_SALESINVOICE, ST_CUSTDELIVERY))) {
        label_row(trans("Customer Currency:"), $order->customer_currency);
        exchange_rate_display(get_company_currency(), $order->customer_currency,
            ($editable ? $_POST['OrderDate'] : $order->document_date));
    }

//    customer_credit_row($_POST['customer_id'], $order->credit);
//
//    if ($order->credit <= 0) {
//        echo '<tr><td colspan="2" align="center"><button type="button" class="req_credit_button">Request</button></td> </tr>';
//    }

//    label_row(trans("Customer Discount:"), ($order->default_discount * 100) . "%");


    echo '<tr style="display:none;"><td colspan="2" align="center"><button type="button" class="add_sub_customer_button">Add Company</button></td> </tr>';


    table_section(3);
    start_row("style=display:none");
    if (($order->pos['cash_sale'] || $order->pos['credit_sale'])
        && !$order->is_started()) {
        // editable payment type
        if (get_post('payment') !== $order->payment) {
            $order->payment = get_post('payment');
            $order->payment_terms = get_payment_terms($order->payment);
            $order->due_date = get_invoice_duedate($order->payment, $order->document_date);
            if ($order->payment_terms['cash_sale']) {
                $_POST['Location'] = $order->Location = $order->pos['pos_location'];
                $order->location_name = $order->pos['location_name'];
            }
            $Ajax->activate('items_table');
            $Ajax->activate('delivery');
        }
        $paymcat = !$order->pos['cash_sale'] ? PM_CREDIT :
            (!$order->pos['credit_sale'] ? PM_CASH : PM_ANY);
        // all terms are available for SO
        $is_order = in_array($order->trans_type, array(ST_SALESQUOTE, ST_SALESORDER));
        sale_payment_list_cells(trans('Payment:'), 'payment', $is_order ? PM_ANY : $paymcat, null, true, $is_order);
    } else {
        label_cells(trans('Payment:'), $order->payment_terms['terms'], "class='label'");
    }
    end_row();

    if ($editable) {
//        $str = sales_types_list_row(trans("Price List:"), 'sales_type', null, true);
    } else {
        label_row(trans("Price List:"), $order->sales_type_name);
    }



    text_row(trans("TRN").":", "customer_trn", $order->tax_id, 30, 30);

    text_row(trans("Ref").":", "customer_ref", null, 30, 30);


    $user = get_user($_SESSION["wa_current_user"]->user);
    if (isset($user['govt_credit_account']) && !empty($user['govt_credit_account'])) {
        $_POST['payment_card'] = "";
    }


    /*if ($_POST['invoice_type'] == 'CenterCard') {
        $_POST['payment_card'] = !empty($_POST['payment_card']) ?
            $_POST['payment_card']:$SysPrefs->prefs['default_e_dirham_account'];
    } else {
        $_POST['payment_card'] = "";
    }*/

    //$Ajax->activate('payment_card');

//    if($_POST['invoice_type'] == 'CustomerCard') {}


//    hidden('payment_card',null);
    gl_all_accounts_list_cells(trans("Payment Card"), 'payment_card', $_POST['payment_card'], false, false, trans("Choose payment Card"), false, false, $SysPrefs->prefs['gl_payment_card_group']);


    if ($order->trans_type != ST_SALESORDER && $order->trans_type != ST_SALESQUOTE) {    // 2008-11-12 Joe Hunt added dimensions

        //YBC
        if ((!isset($_GET['EditFlag']) || $_GET['EditFlag'] == 'false') && !list_updated('dimension_id')) {
            $user_id = $_SESSION['wa_current_user']->user;
            $user = get_user($user_id);
//            $_POST['dimension_id'] = !empty($order->dimension_id) ? $order->dimension_id : $user['dflt_dimension_id'];
            $_POST['dimension_id'] = 3; //TASHEEL;
//            $order->dimension_id = $user['dflt_dimension_id'];
        }


        $dim = get_company_pref('use_dimension');
//        if ($dim > 0)
//            dimensions_list_row(trans("Dimension") . ":", 'dimension_id',
//                $_POST['dimension_id'], false, ' ', false, 1, true);
//        else
//            hidden('dimension_id', 0);
//        if ($dim > 1)
//            dimensions_list_row(trans("Dimension") . " 2:", 'dimension2_id',
//                null, true, ' ', false, 2, false);
//        else
//            hidden('dimension2_id', 0);

        hidden('dimension_id',$_POST['dimension_id']);

    }

//    textarea_row(trans("Comments:"), "customer_ref", $order->Comments, 30, 2);


    end_outer_table(1); // outer table


//    if ($change_prices != 0) {
//        foreach ($order->line_items as $line_no => $item) {
//            $line = &$order->line_items[$line_no];
//            $line->price = get_kit_price($line->stock_id, $order->customer_currency,
//                $order->sales_type, $order->price_factor, get_post('OrderDate'));
//        }
//        $Ajax->activate('items_table');
//    }


    return $customer_error;
}

//--------------------------------------------------------------------------------

function sales_order_item_controls(&$order, &$rowcounter, $line_no = -1)
{
    global $Ajax,$SysPrefs;


    alt_table_row_color($rowcounter);

    $id = find_submit('Edit');
    if ($line_no != -1 && $line_no == $id) // edit old line
    {

        $_POST['stock_id'] = $order->line_items[$id]->stock_id;
        $dec = get_qty_dec($_POST['stock_id']);
        $_POST['qty'] = number_format2($order->line_items[$id]->qty_dispatched, $dec);
        $_POST['price'] = price_format($order->line_items[$id]->price);

        $_POST['Disc'] = percent_format($order->line_items[$id]->discount_percent * 100);
        $units = $order->line_items[$id]->units;
        $_POST['item_description'] = $order->line_items[$id]->item_description;

        //Modifications for AMER
        $_POST['govt_fee'] = $order->line_items[$id]->govt_fee;
        $_POST['bank_service_charge'] = $order->line_items[$id]->bank_service_charge;
        $_POST['bank_service_charge_vat'] = $order->line_items[$id]->bank_service_charge_vat;
        $_POST['discount_amount'] = $order->line_items[$id]->discount_amount;
        $_POST['transaction_id'] = $order->line_items[$id]->transaction_id;
        $_POST['application_id'] = $order->line_items[$id]->application_id;
        $_POST['ref_name'] = $order->line_items[$id]->ref_name;
        $_POST['ed_transaction_id'] = $order->line_items[$id]->ed_transaction_id;


        $_POST['other_fee_info'] = $order->line_items[$id]->other_fee_info_json;


        hidden('stock_id', $_POST['stock_id']);
        label_cell($_POST['stock_id']);
        if ($order->line_items[$id]->descr_editable)
            text_cells(null, 'item_description', null, 45, 150);
        else {

            hidden('item_description', $_POST['item_description']);
            label_cell($_POST['item_description']);
        }
        $Ajax->activate('items_table');
    } else    // prepare new line
    {

        $stock_filter = ["where" => "c.is_tasheel=1 AND c.category_id <> " . $SysPrefs->prefs['tadbeer_category']];
        if (isset($order->show_items) && ($order->show_items == 'tb')) {

            $stock_filter = ["where" => "c.category_id = " . $SysPrefs->prefs['tadbeer_category'],];
        }

        if ($order->fixed_asset)
            stock_disposable_fa_list_cells(null, 'stock_id', null, trans('[Select item]'), true, $order->line_items);
        else
            if (list_updated('stock_id')) {


                sales_items_list_cells(null, 'stock_id', null, false, true, true, $stock_filter);
                $Ajax->activate('price');
                $Ajax->activate('units');
                $Ajax->activate('qty');
                $Ajax->activate('line_total');
                $Ajax->activate('govt_fee');
                $Ajax->activate('bank_service_charge');
                $Ajax->activate('bank_service_charge_vat');
                $Ajax->activate('Disc');
                $Ajax->activate('discount_amount');
                $Ajax->activate('total_bank_charge');
                $Ajax->activate('sales_type');
                $Ajax->activate('other_fee_info_json');
//                $Ajax->activate('customer_id');
            } else
                sales_items_list_cells(null, 'stock_id', last_sales_order_detail($order, 'stk_code'), false, true, true, $stock_filter);


        $last_added_item = last_sales_order_detail($order, 'stk_code');
        $last_stock_id = $last_added_item ? $last_added_item['stock_id'] : $_POST['stock_id'];

        if (list_updated('stock_id')) {
            $item_info = get_item_edit_info($_POST['stock_id'], $_POST['customer_id']);
        } else {
            $item_info = get_item_edit_info($last_stock_id, $order->customer_id);
        }

        $units = $item_info["units"];
        $dec = $item_info['decimals'];
        $_POST['qty'] = number_format2(1, $dec);


        $price = get_kit_price($_POST['stock_id'] ? $_POST['stock_id'] : $last_stock_id,
            $order->customer_currency, $_POST['sales_type'],
            $order->price_factor, get_post('OrderDate'));


//        echo "<script>$('#stock_id').trigger('change')</script>";


        //Modified for AMER
        //Adding PF Amount with Service charge

//        $price = $price + ($item_info['pf_amount'] ? $item_info['pf_amount'] : 0);

        $_POST['price'] = price_format($price);
        // default to the customer's discount %

        $_POST['Disc'] = percent_format($order->default_discount * 100);


        /** Modifications for TASHEEL  */


//        $other_fee_total = get_other_fee_total($_POST['stock_id'] ? $_POST['stock_id'] : $last_stock_id, $_POST['sales_type']);

//        if(isset($order->is_tadbeer) && ($order->is_tadbeer == 1)) {
//
//            $other_fee_total = $other_fee_total- $_POST['price'];
//
//        }


        $price_info = get_sales_type_price($_POST['stock_id'] ? $_POST['stock_id'] : $last_stock_id,
            $_POST['sales_type']);

//        $_POST['Disc'] = $item_info['discount'];

//        display_error($other_fee_total);

        $_POST['govt_fee'] = $item_info["govt_fee"];
        $_POST['bank_service_charge'] = $price_info['bank_service_charge'];
        $_POST['bank_service_charge_vat'] = $price_info['bank_service_charge_vat'];

        $_POST['discount_amount'] = "0.0";
        $_POST['transaction_id'] = "";
        $_POST['ed_transaction_id'] = "";
        $_POST['application_id'] = "";
        $_POST['ref_name'] = "";

        if (!empty($item_info['discount'])) {

            $_POST['discount_amount'] = $item_info['discount'];
//            (discount_amount / price) * 100;
            $_POST['Disc'] = ($_POST['discount_amount'] * 100) / $_POST['price'];
        }


        $other_fee_info = get_other_fee_info($_POST['stock_id'] ? $_POST['stock_id'] : $last_stock_id, $_POST['sales_type']);
        $other_fee_info_json = json_encode($other_fee_info);
        $_POST['other_fee_info'] = base64_encode($other_fee_info_json);

    }


    if ($order->fixed_asset) {
        label_cell(1, '', 'qty');
        hidden('qty', 1);
    } else
        qty_cells(null, 'qty', $_POST['qty'], null, null, $dec);

    if ($order->trans_no != 0) {
        qty_cell($line_no == -1 ? 0 : $order->line_items[$line_no]->qty_done, false, $dec);

    }

//    label_cell($units, '', 'units');

    small_amount_cells(null, 'govt_fee', null, null, "<a id='myBtn' class='other_fee_detailed' href='#'>?</a>");


//    display_error(print_r($_POST['other_fee_info'],true));


    hidden("other_fee_info_json", $_POST['other_fee_info']);
    hidden("price", $_POST['price']);
//  amount_cells(null, 'price');
    //small_amount_cells(null, 'price');

//    small_amount_cells(null, 'bank_service_charge');
//    small_amount_cells(null, 'bank_service_charge_vat');

    $_POST['total_bank_charge'] = $_POST['bank_service_charge'] + $_POST['bank_service_charge_vat'];
    $_POST['total_bank_charge'] = $_POST['total_bank_charge'] ? $_POST['total_bank_charge'] : '0.0';

//        small_amount_cells(null, 'bank_service_charge');
    hidden('total_bank_charge', $_POST['total_bank_charge']);

    hidden('bank_service_charge', $_POST['bank_service_charge']);
    hidden('bank_service_charge_vat', $_POST['bank_service_charge_vat']);

//    small_amount_cells(null, 'discount_amount', null, "");

    hidden('discount_amount', percent_format($_POST['discount_amount']));
    hidden('Disc', percent_format($_POST['Disc']));
//    small_amount_cells(null, 'Disc', percent_format($_POST['Disc']), null, null, user_percent_dec());



//    if($_POST['customer_id'] == 1) {
//
//        hidden('transaction_id', $_POST['transaction_id']);
//        label_cell($_POST['transaction_id']);
//
//        if ($order->show_items == 'tb') {
//            hidden('ed_transaction_id', $_POST['ed_transaction_id']);
//            label_cell($_POST['ed_transaction_id']);
//        } else {
//            hidden('ed_transaction_id');
//        }
//
//    }
//    else {

//        text_cells(null, 'transaction_id', null, 5, 50);

    text_cells(null, 'ed_transaction_id', null, 5, 50,
        false, "", "",
        "class='live_popover' autocomplete='off' data-placement='top' 
        data-content='" . $_POST['ed_transaction_id'] . "'  rel='next-popover'");

        /*if ($order->show_items == 'tb') {
            text_cells(null, 'ed_transaction_id', null, 10, 50);
        } else {
            hidden('ed_transaction_id');
        }*/
//    }



//    text_cells(null, 'transaction_id', null, 10, 50);
//
//
//    if ($order->show_items == 'tb') {
//        text_cells(null, 'ed_transaction_id', null, 10, 50);
//    } else {
//        hidden('ed_transaction_id');
//    }


 text_cells(null, 'application_id', null, 10, 50);

    //hidden('application_id', $_POST['application_id']);

    text_cells(null, 'ref_name', null, 10, 50);

//    $line_total = (
//            input_num('qty') * (
//            input_num('price') ) * (1 - input_num('Disc') / 100))+
//            input_num('govt_fee') +
//            input_num('bank_service_charge') +
//            input_num('bank_service_charge_vat');


//Modification for AMER
    $line_total = (
        (
            input_num('qty') * (
                input_num('price') +
                input_num('govt_fee') +
                input_num('bank_service_charge') +
                input_num('bank_service_charge_vat')
            )
        ) - (input_num('qty') * input_num('discount_amount'))
    );

    if (isset($order->is_tadbeer) && ($order->is_tadbeer == 1)) {

        $line_total = (
            (
                input_num('qty') * (
                    input_num('govt_fee') +
                    input_num('bank_service_charge') +
                    input_num('bank_service_charge_vat')
                )
            ) - (input_num('qty') * input_num('discount_amount'))
        );

    }


    amount_cell($line_total, false, '', 'line_total');

    if ($id != -1) {
        button_cell('UpdateItem', trans("Update"),
            trans('Confirm changes'), ICON_UPDATE);
        button_cell('CancelItemChanges', trans("Cancel"),
            trans('Cancel changes'), ICON_CANCEL);
        hidden('LineNo', $line_no);
        set_focus('qty');
    } else {
        submit_cells('AddItem', trans("Add Item"), "colspan=2 align='center'",
            trans('Add new item to document'), true);
    }

    end_row();

    set_focus('ed_transaction_id');
}

//--------------------------------------------------------------------------------

function display_delivery_details(&$order)
{
    global $Ajax;

    div_start('delivery');

    if ($order->payment_terms['cash_sale']) {    // Direct payment sale
        $Ajax->activate('items_table');
        display_heading(trans('Cash payment'));
        start_table(TABLESTYLE2, "width='60%'");

        locations_list_row(trans("Deliver from Location:"), 'Location', null, false, true);
        if (list_updated('Location'))
            $Ajax->activate('items_table');
        label_row(trans("Cash account:"), $order->pos['bank_account_name']);
        textarea_row(trans("Comments:"), "Comments", $order->Comments, 31, 5);
        end_table();
        hidden('delivery_date', $order->due_date);
    } else {
        if ($order->trans_type == ST_SALESINVOICE) {
            $title = trans("Delivery Details");
            $delname = trans("Due Date") . ':';
        } elseif ($order->trans_type == ST_CUSTDELIVERY) {
            $title = trans("Invoice Delivery Details");
            $delname = trans("Invoice before") . ':';
        } elseif ($order->trans_type == ST_SALESQUOTE) {
            $title = trans("Quotation Delivery Details");
            $delname = trans("Valid until") . ':';
        } else {
            $title = trans("Order Delivery Details");
            $delname = trans("Required Delivery Date") . ':';
        }
        display_heading($title);
        start_outer_table(TABLESTYLE2, "width='90%'");
        table_section(1);

        locations_list_row(trans("Deliver from Location:"), 'Location', null, false, true, $order->fixed_asset);
        if ($order->payment_terms['days_before_due'] == -1) {
            $Ajax->addUpdate('items_table', 'prep_amount', price_format($order->get_trans_total())); // bind to items_table update
            if (!$order->is_started())
                amount_row(trans("Pre-Payment Required:"), 'prep_amount');
            else
                label_row(trans("Pre-Payment Required:"), price_format($order->prep_amount), "class='label'");
        }
        if (list_updated('Location'))
            $Ajax->activate('items_table');

        date_row($delname, 'delivery_date',
            $order->trans_type == ST_SALESORDER ? trans('Enter requested day of delivery')
                : $order->trans_type == ST_SALESQUOTE ? trans('Enter Valid until Date') : '');
        text_row(trans("Deliver To:"), 'deliver_to', $order->deliver_to, 50, 60,
            trans('Additional identifier for delivery e.g. name of receiving person'));

        textarea_row(trans("Address:"), 'delivery_address', $order->delivery_address, 35, 5,
            trans('Delivery address. Default is address of customer branch'));

        table_section(2);

        text_row(trans("Contact Phone Number:"), 'phone', $order->phone, 25, 25,
            trans('Phone number of ordering person. Defaults to branch phone number'));
        text_row(trans("Customer Reference:"), 'cust_ref', $order->cust_ref, 25, 25,
            trans('Customer reference number for this order (if any)'));
        textarea_row(trans("Comments:"), "Comments", $order->Comments, 31, 5);

        shippers_list_row(trans("Shipping Company:"), 'ship_via', $order->ship_via);

        end_outer_table(1);
    }
    div_end();
}

?>


<style>


    <?php

    if( !isset($_GET['EditFlag']) || $_GET['EditFlag'] != 'true') { ?>

    #_customer_id_sel {
         /*pointer-events: none;*/
    }
    img[title="Search customers"] {
        display: none;
    }

    <?php } ?>

    /*#_customer_id_sel {*/
        /*pointer-events: none;*/
    /*}*/
    /*img[title="Search customers"] {*/
        /*display: none;*/
    /*}*/

</style>

<script>

    $(document).ready(function () {


        // $('#_customer_id_sel').parent().css('pointer-events', 'none');
        // $('#_customer_id_sel').parent().find('img').hide();


    });

    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }


    function getCustomerByEID(eidNumber) {

        $.ajax("../API/hub.php", {
            method: 'get',
            data: {
                method: 'getCustomerByEID',
                eid: eidNumber
            },
        }).done(function (r) {

            var data = JSON.parse(r);

            var customer_id = data.debtor_no;

            $("#customer_id").val(customer_id);

            $("#customer_id").trigger("change");


            setTimeout(function () {
                $('#_customer_id_sel').parent().css('pointer-events','none');
                $('#_customer_id_sel').parent().css('background','#ccc');
                $('#_customer_id_sel').find('.select2-selection--single').css('background','#ccc');
            },2000)


        });

    }

    function fetchEIDNumber(clipText) {

        if(isJson(clipText)) {

            var jsonData = JSON.parse(clipText);

            if(jsonData.HasData) {

                var EID_Number = jsonData.EIDNumber;

                getCustomerByEID(jsonData.EIDNumber);

                $("input[name='eid_number']").val(EID_Number);

                var emptyInp = document.getElementById('emptyInput');
                emptyInp.select();
                emptyInp.focus();
                document.execCommand('copy');

            }
            else {

                alert("Please insert a valid EID and get data.")

            }

        }
        else {

            alert("Please insert a valid EID and get data.")

        }

    }

    $(document).on("click", "#eidNumFilter", function () {

        navigator.clipboard.readText().then(clipText =>
            fetchEIDNumber(clipText)
        );

    });


    $(document).on("change", "input[name='staff_mistake_invoice']", function () {

        var check_value =$("input[name='staff_mistake_invoice']").is(":checked");

        if (check_value) {

            $("#mistake_staff_id_tr").show();

        }
        else {
            $("#mistake_staff_id_tr").hide();
        }

    });

</script>

