<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <p><?= trans("Fee Details") ?></p>

        </div>
        <div class="modal-body">
            <table class="tablestyle" cellpadding="2" cellspacing="0">
                <thead>
                <tbody>
                <tr>
                    <td><?= trans("Total Amount") ?></td>
                    <td><input type="number" id="tot_amount"></td>
                </tr>
                <tr>
                    <td><?= trans("Govt. Fee") ?></td>
                    <td><input type="number" id="supp_amount"></td>
                </tr>

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


<style>
    #batchModel.modal .modal-content .modal-header .close:before {
        display: none !important;
    }
</style>

<!--Batch Model-->
<div id="batchModel" class="modal">
    <!-- Modal content -->

    <div class="" role="document" style="width: 100% !important;">

        <div class="modal-content">
            <div class="modal-header">
                <span class="close batchModelClose">&times;</span>
                <h4 class="modal-title" style="position: absolute;">Auto Batch</h4>
            </div>
            <div class="modal-body">

                <div style="    height: 400px;
    overflow-y: scroll;">
                    <table class="tablestyle" cellpadding="2" cellspacing="0">
                        <thead>
                        <th>*</th>
                        <th><?= trans("Service Name") ?></th>
                        <th><?= trans("Amount") ?></th>
                        <th><?= trans("Transaction ID") ?></th>
                        <th><?= trans("Application ID") ?></th>
                        <th><?= trans("Ref. Name") ?></th>
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


</div>


<div class="modal fade" role="dialog" id="batchModel--">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i
                            class="fa fa-close"></i>
                </button>
                <h4 class="modal-title" style="position: absolute;">Admin Approval</h4>
            </div>


            <div class="modal-body">
                <div class="form-group">
                    <label for="discount_for_admin_approval" class="col-form-label">Discount</label>
                    <input type="number" step="any" class="form-control numpad" placeholder="Enter Discount"
                           id="discount_for_admin_approval">
                </div>
                <div class="form-group">
                    <label for="admin_password" class="col-form-label">Password for user: <b>'admin'</b></label>
                    <input type="password" class="form-control" id="admin_password" placeholder="Password">
                </div>
                <button type="submit" class="btn btn-primary mb-2 pull-right" onclick="check_password();"
                        id="admin_password_confirm">Confirm
                </button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


<div id="pendingSrqModel" class="modal">
    <!-- Modal content -->

    <div class="" role="document" style="width: 100% !important;">

        <div class="modal-content">
            <div class="modal-header">
                <span class="close srqModelClose">&times;</span>
                <h4 class="modal-title" style="position: absolute; color: white" id="srv_pop_title">Pending Service
                    Request List</h4>
            </div>
            <div class="modal-body">

                <div style="    height: 400px;
    overflow-y: scroll;">
                    <table class="tablestyle" cellpadding="2" cellspacing="0">
                        <thead>
                        <th><?= trans("REFERENCE") ?></th>
                        <th><?= trans("TOKEN") ?></th>
                        <th><?= trans("CUSTOMER NAME") ?></th>
                        <th><?= trans("MOBILE") ?></th>
                        <th><?= trans("COUNTER STAFF NAME") ?></th>
                        <th><?= trans("AMOUNT") ?></th>
                        <th><?= trans("") ?></th>
                        </thead>
                        <tbody id="srq_list_tbody">


                        </tbody>
                    </table>
                </div>


            </div>

        </div>
    </div>


</div>


<div id="add_sub_cust_modal" class="modal modal-sm" style="max-width: 100%">
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
            <button type="button" id="btn_add_sub_cust" style="background: #1d1d1d">Add</button>
        </div>
    </div>
</div>


<?php if (!user_check_access('SA_SALESINVOICEDATEMOD')): ?>
    <style>
        input[name="OrderDate"] {
            display: none;
        }

    </style>
<?php endif; ?>

<style>

    input[name="ref"] {
        pointer-events: none;
        background: #ccc;
    }

    textarea[name='memo_'], textarea[name='Comments'] {
        height: 5em !important;
        width: 18em !important;
        max-width: 1000px !important;

    }

    input[name="eid_number"] {
        pointer-events: none;
        background: #ccc;
    }

    .modal .modal-content .modal-header .close:before {
        content: none !important;
    }

    #pendingSrqModel .modal-content {
        /*width: 900px*/
        width: 65% !important;
    }

    #pendingSrqModel table td, #pendingSrqModel table th {
        border: 1px solid #ccc !important;
    }

    input[name="OrderDate"], input[name="ref"], input[name="token_no"] {
        width: 230px !important;
    }

    select[name="dimension_id"] {
        pointer-events: none;
        background: #ccc;
    }

    #_stock_id_sel .select2-container {
        width: 430px !important;

    }

    #batchModel .modal-content {
        /*width: 900px*/
        width: 65% !important;
    }

    #batchModel table td, #batchModel table th {
        border: 1px solid #ccc !important;
    }

    #items_table table.tablestyle td:nth-child(4) {
        display: none;
    }

    /*#items_table table.tablestyle td:nth-child(9) {*/
    /*display: none;*/
    /*}*/

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
        background-color: #009688;
        color: white;
    }

    .modal-body {
        padding: 2px 16px;
    }

    .modal-footer {
        padding: 2px 16px;
        background-color: #009688;
        color: white;
        text-align: right;
    }

    #btn_update_fee_info, #btn_req_for_approval {
        background: #585858 !important;
        box-shadow: none !important;
        padding: 0px 20px !important;
        margin: 2px;
    }

    /*#header_table .customer-credit-row,*/
    /*!*#header_table .customer-branch-list-row,*!*/
    /*#header_table .sales-type-list-row, #delivery {*/
    /*display: none;*/
    /*}*/

    #delivery, .sales-type-list-row, .customer-branch-list-row {
        display: none;
    }

    #items_table select {
        width: 85% !important;
    }

    /*#_payment_sel < tr {*/
    /*display: none;*/
    /*}*/

    input[name="Disc"], input[name="total_bank_charge"],
    input[name="discount_amount"], input[name="bank_service_charge"] {
        pointer-events: none;
        background: #ccc;
    }

    .select2-results__option {
        font-size: 12px;
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

    table.tablestyle td {
        padding: 2px !important;
    }

    #_stock_id_edit {
        width: 53px !important;
    }

    #batch_auto_add {
        background: #585858 !important;
        box-shadow: none !important;
        padding: 0px 20px !important;
        margin: 2px;
    }


</style>

<script src="../js/jquery3.3.1.min.js"></script>

<script src="../js/autoFetch.js"></script>


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


    $(document).on("change", "input[name='token_no']", function () {

        $("select[name='customer_id']").trigger('change')

    });


    $(document).on("click", "#mobileSearch", function () {

        var token_number = $("input[name='token_no']").val();

        var curr_url = $(this).attr('data-href');

        $.ajax("../API/hub.php", {
            method: 'get',
            data: {
                method: 'fetchTodaysServiceRequestsByToken',
                token: token_number
            },
        }).done(function (r) {

            var data = JSON.parse(r);


            if (data.length > 1) {

                $("#srv_pop_title").html("PENDING SERVICE REQUESTS OF TOKEN NUMBER : " + token_number);

                srq_list_modal.style.display = "block";

                var tbody_html = "";

                $.each(data, function (key, value) {

                    tbody_html += "<tr>";
                    tbody_html += "<td>" + value.reference + "</td>";
                    tbody_html += "<td>" + value.token_number + "</td>";
                    tbody_html += "<td>" + value.display_customer + "</td>";
                    tbody_html += "<td>" + value.mobile + "</td>";
                    tbody_html += "<td>" + value.staff_name + "</td>";
                    tbody_html += "<td>" + value.amount + "</td>";
                    tbody_html += "<td style='text-align: center'>" +
                        "<button type='button' " +
                        "class='call_token_btn btn btn-sm btn-primary' data-req_id='" + value.id + "' data-token='" + value.token_number + "'>Call</button></td>";
                    tbody_html += "</tr>";

                });

                if (data.length === 0) {
                    var tr_len = $("#srq_list_tbody").closest("table").find("th").length;
                    tbody_html = "<tr><td colspan='" + tr_len + "' style='text-align: center'>You have no pending service request.!</td></tr>";
                }

                $("#srq_list_tbody").html(tbody_html);

            }
            else {

                var req_id = 0;

                if (data.length === 1) {
                    req_id = data[0].id;
                }

                var check_url = curr_url + "&SRQ_TOKEN=" + token_number + "&req_id=" + req_id;

                window.location.href = check_url;

            }


        });


    });


    $(document).on("change", "select[name='customer_type']", function () {

        $("select[name='customer_id']").trigger('change')

    });


    $(document).on("click", ".create_cust_button", function () {

        var e = jQuery.Event("keydown");
        e.which = 113;
        $("input").trigger(e);

    });

    $(document).ready(function () {
        $(".credit_card_no").hide();

        setInterval(function () {

            var payment_method = $("select[name='invoice_payment']").val();

            if (payment_method !== "PayNoWCC") {
                $(".credit_card_no").hide();
            }

        }, 50)
    });


    document.addEventListener("keydown", function (event) {

        if (event.keyCode == 117)
            $(".item_list_options").trigger('click');
    });


    //FOR MODAL
    var modal = document.getElementById('myModal');

    // Get the button that opens the modal
    var btn = document.getElementById("myBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];
    $(document).on("click", "#myBtn", function () {

        modal.style.display = "block";


        var service_charge = $("input[name='price']").val();
        var supp_amount = $("input[name='govt_fee']").val();

        service_charge = service_charge.replace(/,/g, '');
        supp_amount = supp_amount.replace(/,/g, '');

        if (!service_charge) service_charge = 0;
        if (!supp_amount) supp_amount = 0;

        var tot_amount = parseFloat(service_charge) + parseFloat(supp_amount);

        $("#tot_amount").val(tot_amount.toFixed(2));
        $("#supp_amount").val(parseFloat(supp_amount).toFixed(2))


    });

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


    $(document).on("click", "#btn_update_fee_info", function () {

        var total_amount = $("#tot_amount").val();
        var supp_amount = $("#supp_amount").val();

        if (!total_amount) total_amount = 0;
        if (!supp_amount) supp_amount = 0;

        var service_charge = parseFloat(total_amount) - parseFloat(supp_amount);

        $("input[name='price']").val(parseFloat(service_charge).toFixed(2));

        $("input[name='govt_fee']").val(parseFloat(supp_amount).toFixed(2));

        modal.style.display = "none";
    });

    $("input[name='govt_fee']").bind('keypress', function (e) {

        console.log(4234234234);
        if (e.keyCode == 13) {
            // Enter pressed... do anything here...
        }
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
            url: "credit_requests_ajax.php?cr_request=1",
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

        if ($.trim(sub_cust_name) === "") {
            alert("Sub customer name is empty.");
            return false;
        }


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
                }, 2000);


            },
            error: function (xhr) {
            }
        });


    });


    $(document).on("change", "select[name='sub_customer_list']", function () {

        var sub_customer_name = $("select[name='sub_customer_list'] option:selected").text();

        if ($(this).val() === '0') {
            sub_customer_name = "";
        }

        $("input[name='display_customer']").val(sub_customer_name);
        // $("input[name='contact_person']").val(sub_customer_name);

    });


    var srq_list_modal = document.getElementById('pendingSrqModel');
    var add_sub_cust_close = document.getElementsByClassName("srqModelClose")[0];
    add_sub_cust_close.onclick = function () {
        srq_list_modal.style.display = "none";
    };

    window.onclick = function (event) {
        if (event.target == srq_list_modal) {
            srq_list_modal.style.display = "none";
        }
    };

    $(document).on("click", "#srv_req_list_btn", function () {

        srq_list_modal.style.display = "block";

        var dim_id = $("input[name='dimension_id']").val();

        $.ajax("../API/hub.php", {
            method: 'get',
            data: {
                method: 'getPendingServiceRequests',
                cost_center_id: dim_id
            },
        }).done(function (r) {

            var data = JSON.parse(r);

            $("#srv_pop_title").html("PENDING SERVICE REQUESTS");

            var tbody_html = "";

            $.each(data, function (key, value) {

                tbody_html += "<tr>";
                tbody_html += "<td>" + value.reference + "</td>";
                tbody_html += "<td>" + value.token_number + "</td>";
                tbody_html += "<td>" + value.display_customer + "</td>";
                tbody_html += "<td>" + value.mobile + "</td>";
                tbody_html += "<td>" + value.staff_name + "</td>";
                tbody_html += "<td>" + value.amount + "</td>";
                tbody_html += "<td style='text-align: center'>" +
                    "<button type='button' " +
                    "class='call_token_btn btn btn-sm btn-primary' data-req_id='" + value.id + "' data-token='" + value.token_number + "'>Call</button></td>";
                tbody_html += "</tr>";

            });

            if (data.length === 0) {
                var tr_len = $("#srq_list_tbody").closest("table").find("th").length;
                tbody_html = "<tr><td colspan='" + tr_len + "' style='text-align: center'>You have no pending service request.!</td></tr>";
            }

            $("#srq_list_tbody").html(tbody_html);

        });

    });


    $(document).on("click", ".call_token_btn", function () {

        var selected_token = $(this).data('token');
        var req_id = $(this).data('req_id');


        $("input[name='token_no']").val(selected_token);


        var curr_url = $("#mobileSearch").attr('data-href');

        var check_url = curr_url + "&SRQ_TOKEN=" + selected_token + "&req_id=" + req_id;

        window.location.href = check_url;

        // srq_list_modal.style.display = "none";

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
include_once($path_to_root . "/sales/includes/cart_class.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/common.inc");


//--------------------------------------------------------------------------------
function add_to_order(&$order, $new_item, $new_item_qty, $price, $discount, $description = '',
                      $govt_fee = 0, $bank_service_charge = 0, $bank_service_charge_vat = 0,
                      $transaction_id = null, $discount_amount = null, $other = "",
                      $application_id = null, $govt_bank_account = null, $ref_name = null, $ed_transaction_id = null)
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

        if (!$item['is_foreign'] && $item['item_code'] != $item['stock_id']) {    // this is sales kit - recurse

            add_to_order($order, $item['stock_id'], $new_item_qty * $item['quantity'],
                $item_price, $discount, $govt_fee, $bank_service_charge, $bank_service_charge_vat, $transaction_id);


        } else {    // stock item record eventually with foreign code

            // check duplicate stock item
            foreach ($order->line_items as $order_item) {
                if (strcasecmp($order_item->stock_id, $item['stock_id']) == 0) {
                    display_warning(trans("For Part :") . $item['stock_id'] . " "
                        . trans("This item is already on this document. You have been warned."));
                    break;
                }
            }
            $order->add_to_cart(count($order->line_items), $item['stock_id'],
                $new_item_qty * $item['quantity'], $item_price, $discount, 0, 0, $description, null, null, null,
                $govt_fee, $bank_service_charge, $bank_service_charge_vat, $transaction_id, $discount_amount, null,
                $application_id, $govt_bank_account, $ref_name, $ed_transaction_id);
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
//    $order->contact_person = $myrow["contact_person"];

    if (empty($order->contact_person))
        $order->contact_person = $myrow["name"];

    $name = $myrow['name'];


    if (isset($order->token_number) && !empty($order->token_number)) {
        $customer_email = $order->email;
        $customer_mobile = $order->phone;
        $name = $order->customer_name;

    } else {
        $order->customer_ref = $name;
    }

    $dimension_info = get_dimension($order->dim_id);

    if (in_array($_SESSION["wa_current_user"]->access, explode(",", $dimension_info['direct_invoicing_permitted_to']))) {
        $dimension_info['has_service_request'] = 0;
    }

    if ($dimension_info['has_service_request'] == 0) {
        $order->customer_ref = "";
    }


    if ($myrow['dissallow_invoices'] == 1)
        $ret_error = trans("The selected customer account is currently on hold. Please contact the credit control personnel to discuss.");

    $deliver = $myrow['address']; // in case no branch address use company address

    $order->set_customer($customer_id, $name, $myrow['curr_code'],
        $myrow['discount'], $myrow['payment_terms'], $myrow['pymt_discount']);

    // the sales type determines the price list to be used by default
    $order->set_sales_type($myrow['salestype'], $myrow['sales_type'], $myrow['tax_included'],
        $myrow['factor']);


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

    $order->set_token_data();


    return $ret_error;
}

//---------------------------------------------------------------------------------

function display_order_summary($title, &$order, $editable_items = false)
{

    display_heading($title);

    div_start('items_table');
    start_table(TABLESTYLE, "width='90%'");

    $th = array(trans("Item Code"),
        trans("Item Description"),
        trans("QTY"),
        trans("Delivered"),
        trans("Govt. A/C"),
        trans("Govt. Fee"),
        trans("Add Fee"),
        $order->tax_included ? trans("Price after Tax") : trans("Service Charge"),

//        trans("Bank Charge"),

        trans("Bank Charge"),

//        trans("Discount"),
//        trans("Discount %"),
        trans("Transaction ID"),
        trans("Application ID"),
        trans("Ref. Name"),
        trans("Total"), "");

    if ($order->trans_no == 0) {
        unset($th[3]);
    }

    if (count($order->line_items))
        $th[] = '';

    table_header($th);

    $total = 0;
    $k = 0;  //row colour counter

    $discount_total = 0;

    $id = find_submit('Edit');

    $low_stock = $order->check_qoh($_POST['OrderDate'], $_POST['Location']);
    foreach ($order->get_items() as $line_no => $stock_item) {


//        pp($stock_item);

        $line_total = round($stock_item->qty_dispatched *
            ($stock_item->price + $stock_item->govt_fee + $stock_item->bank_service_charge +
                $stock_item->bank_service_charge_vat) -
            ($stock_item->discount_amount * $stock_item->qty_dispatched) + $stock_item->discount_amount,
            user_price_dec());

        $discount_total += $stock_item->discount_amount;

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

            label_cell(get_gl_account_name($stock_item->govt_bank_account));

            amount_cell($stock_item->govt_fee);
            amount_cell($stock_item->bank_service_charge_vat);

            amount_cell($stock_item->price);

            /** Modifications for AMER */

            amount_cell($stock_item->bank_service_charge);


            label_cell($stock_item->transaction_id);
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


    if ($id == -1 && $editable_items)
        sales_order_item_controls($order, $k);

    $colspan = 10;
    if ($order->trans_no != 0)
        ++$colspan;
    start_row("style='display:none'");
    label_cell(trans("Shipping Charge"), "colspan=$colspan align=right");
    small_amount_cells(null, 'freight_cost', price_format(get_post('freight_cost', 0)));
    label_cell('', 'colspan=2');
    end_row();
    $display_sub_total = price_format($total + input_num('freight_cost'));
    label_row(trans("Sub-total"), $display_sub_total, "colspan=$colspan align=right", "align=right", 2);
    $taxes = $order->get_taxes(input_num('freight_cost'));

    $tax_total = display_edit_tax_items($taxes, $colspan, $order->tax_included, 2);


    label_row(trans("Total Discount"), price_format($discount_total), "colspan=$colspan align=right", "align=right", 2);

    $display_total = price_format(($total + input_num('freight_cost') + $tax_total) - $discount_total);

    start_row();
    label_cells(trans("Amount Total"), $display_total, "colspan=$colspan align=right", "align=right");
    //submit_cells('update', trans("Update"), "colspan=2 align='center'", trans("Refresh"), true);
    end_row();

    end_table();
    if ($low_stock)
        display_note(trans("Marked items have insufficient quantities in stock as on day of delivery."), 0, 1, "class='stockmankofg'");

    div_end();


    set_focus('transaction_id');

}


function validate_token($req_id, $dim_id)
{

    if (empty($dim_id))
        return false;

    $sql = "select count(*) as cnt from 0_service_requests 
    where id = '$req_id' and cost_center_id=$dim_id";
    $get = db_query($sql);

    $data = db_fetch($get);

    if ($data['cnt'] > 0)
        return true;

    return false;

}

// ------------------------------------------------------------------------------

function display_order_header(&$order, $editable, $date_text)
{
    global $Ajax, $SysPrefs, $path_to_root;

    start_outer_table(TABLESTYLE2, "width='80%' id='header_table'");

    table_section(1);

    $customer_error = "";
    $change_prices = 0;
    $token_error = "";

    if (empty($order->dim_id))
        display_error("Cost Center Not Selected. Please go back and try again");

    ////SERVICE_REQUEST_CHANGES

    $dimension_info = get_dimension($order->dim_id);

    if (in_array($_SESSION["wa_current_user"]->access, explode(",", $dimension_info['direct_invoicing_permitted_to']))) {
        $dimension_info['has_service_request'] = 0;
    }

//    $dimension_info['has_service_request'] = 0;

    if ($dimension_info['has_service_request'] == 1) {//Service Request Flow is Only Enabled for Selected Cost Centers

        if (!isset($order->token_number) || empty($order->token_number)) {
            $token_error = "Please enter TOKEN NUMBER to proceed with invoice";
        } else if (!validate_token($order->service_req_id, $order->dim_id)) {
            $token_error = "Token number is not valid. Please check";
        } else {
            $_POST['SRQ_TOKEN'] = $order->token_number;
        }
    }


    if (get_post('invoice_payment') == 'PayLater') {

        if (!check_credit_limit_validation()) {
            $token_error = "Credit Limit Exceeded";
        }

    }


    $token_check_url = "$path_to_root/sales/sales_order_entry.php?NewInvoice=0&dim_id=" . $order->dim_id;

    $token_after_html = "";
    $token_params = "";

    $has_full_invoice_access = in_array($_SESSION["wa_current_user"]->access,[48]);

    if ($dimension_info['has_service_request'] == 1 || $has_full_invoice_access) {
    $token_after_html = "<a id='mobileSearch' type='button' 
class='btn btn-sm btn-primary' style='color: white' href='#' data-href='$token_check_url'>GET DATA</a>
<a id='srv_req_list_btn' type='button' 
class='btn btn-sm btn-success' style='color: white' href='#' >FIND</a>";
    }

//    pp($_POST['token_no']);

    if ($dimension_info['has_service_request'] == 1 || $dimension_info['has_token_filter'] == 1) {

//        pp(222);
        text_row(trans("Token/Mobile:"), "token_no", $order->token_number,
            30, 30, null, "", $token_after_html, "", "autocomplete=AxisproDaxis");
    } else {
        hidden('token_no');
    }


    text_row(trans("Invoice No") . ":", 'ref', null, 28, 100, 'Invoice Number');

//    ref_row(trans("Invoice No") . ':', 'ref', trans('Reference number unique for this document type'), null, '', $order->trans_type, array('date' => @$_POST['OrderDate']));

    if ($order->sales_type != $_POST['sales_type']) {
        $myrow = get_sales_type($_POST['sales_type']);
        $order->set_sales_type($myrow['id'], $myrow['sales_type'],
            $myrow['tax_included'], $myrow['factor']);
        $Ajax->activate('sales_type');
        $change_prices = 1;
    }


    if ($editable) {
        if (!isset($_POST['OrderDate']) || $_POST['OrderDate'] == "")
            $_POST['OrderDate'] = $order->document_date;


        $_POST['OrderDate'] = (isset($order->edit_document_date) && !empty($order->edit_document_date)) ?
            $order->edit_document_date : $_POST['OrderDate'];

        //FOR NYPD
        if (user_check_access('SA_SALESINVOICEDATEMOD')) {
            date_row($date_text, 'OrderDate', trans('Date of order receive'),
                $order->trans_no == 0, 0, 0, 0, null, true);
        } else {
            start_row();
            label_cell($date_text, "class='label'");
            label_cell($_POST['OrderDate']);
            end_row();
            hidden('OrderDate', $_POST['OrderDate']);
        }


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


//        if ($dimension_info['has_service_request'] == 0) {

        //SET DATA BY TOKEN

//        pp($_POST['token_no']);

        if (isset($_POST['token_no']) && !empty($_POST['token_no'])) {
            $token_customer = $order->get_customer_by_token($_POST['token_no']);
            $_POST['customer_id'] = !empty($token_customer) ? $token_customer['customer_id'] : 1;

//                pp( $_POST['customer_id']);

            if (empty($token_customer)) {
                display_warning("Cant find this Token/Mobile, Please continue...");
            }
        }

//        }


//        text_row(trans("EID Number:"), "eid_number", null,
//            30, 30, null, "", "<a id='eidNumFilter' type='button'
//class='btn btn-sm btn-primary' style='color: white' href='#'>FETCH EID</a>", "", "autocomplete=AxisproDaxis readonly");

        hidden("eid_number");

        text_cells("", "emptyInput", " ", "", "",
            "", "", "", "id='emptyInput' style='display:none'");

        if ($order->trans_type != ST_SALESORDER && $order->trans_type != ST_SALESQUOTE) {    // 2008-11-12 Joe Hunt added dimensions


            //YBC
            if ((!isset($_GET['EditFlag']) || $_GET['EditFlag'] == 'false')
                && !list_updated('dimension_id') && !list_updated('customer_id')) {
                $_POST['dimension_id'] = $_GET['dim_id'];
            }

            if ($order->edit_invoice) {
                $_POST['dimension_id'] = $order->dim_id;
                $order->dimension_id = $order->dim_id;
            }

            $dim = get_company_pref('use_dimension');
            if ($dim > 0) {

                hidden('dimension_id', $_POST['dimension_id']);
            } else
                hidden('dimension_id', 0);
            if ($dim > 1)
                dimensions_list_row(trans("Dimension") . " 2:", 'dimension2_id',
                    null, true, ' ', false, 2, false);
            else
                hidden('dimension2_id', 0);

        }

        $customer_info = get_customer($_POST['customer_id']);
//        pp($customer_info);


        $options = array('select_submit' => true, 'disabled' => null, 'id' => 'customer_type');
        $select_opt = array(
            "" => trans("--ALL--"),
            "PRO" => trans("PRO CUSTOMER"),
            "COMPANY" => trans("COMPANY CUSTOMER")
        );

        if (empty($_POST['customer_type']))
            $_POST['customer_type'] = $customer_info['customer_type'];

        echo '<tr style="display: none !important;"><td class="label">Customer Type </td><td>' . array_selector('customer_type', null, $select_opt, $options) . '</td> </tr>';


        $customer_type = $_POST['customer_type'];
        $where_cond = '1=1';
        $where_cond = '';
        if (!empty($customer_type)) {
            //$where_cond = 'AND customer_type=' . db_escape($customer_type);
            $Ajax->activate('customer_id');
            $Ajax->activate('branch_id');
        }

        $show_hide_customer_select = "";

//        if ($dimension_info['has_service_request'] == 1) {
//            $show_hide_customer_select = "style='display:none'";
//        }

        if (in_array($_SESSION["wa_current_user"]->access, explode(",", $dimension_info['direct_invoicing_permitted_to']))) {
//            $where_cond .= " AND debtor_no != 1 ";
        }

//        $sql = "SELECT debtor_no, debtor_ref, name, curr_code, inactive FROM 0_debtors_master
//          WHERE 1=1 $where_cond  ORDER BY debtor_ref";
//
//        $result = db_query($sql, "an item could not be retreived");
//        $customers_array = [];
//        while ($row = db_fetch($result)) {
//            $customers_array[] = $row;
//        }
//        $select_opt =$customers_array;
//        if (count($select_opt) > 1) {
//
//            $html = "<span id='_customer_id_sel'>";
//            $html.="<select autocomplete='off' id='customer_id' name='customer_id' class='combo2' title='' _last=''>";
//
//            foreach ($customers_array as $row) {
//                $html.= "<option value='".$row['debtor_no']."'>".$row["name"]."</option>";
//            }
//
//            $html.="</select>";
//            $html.="</span>";
//
//            $html.="<input type='submit' class='combo_select' style='border: 0px; background: url(&quot;../themes/daxis/images/button_ok.png&quot;) no-repeat; display: none;' aspect='fallback' name='_customer_id_update' value='' title='Select'>";
//
//            echo '<tr><td class="label">Customer</td><td>' . $html .
//                '</td> </tr>';
//
//        }

//        pp($_POST['customer_id']);


        customer_list_row(trans("Customer:"), 'customer_id', null, '-Select a Customer-',
            true, false, true, $where_cond, $show_hide_customer_select);

        echo '<tr style="display: none"><td colspan="2" align="center">
            <button type="button" class="create_cust_button">+</button></td> </tr>';

        if ($order->customer_id != get_post('customer_id', -1)) {
            // customer has changed
            $Ajax->activate('branch_id');

        }

        //For Arabian
        hidden('invoice_type', 'Cash');

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

        if (isset($_SESSION['Items']->edit_invoice) && $_SESSION['Items']->edit_invoice == true) {

        } else {

            //Temp Disable CORONA
            $customer_error = $token_error;

        }

    }

    table_section(2);


    $options = array('select_submit' => true, 'disabled' => null, 'id' => 'sub_customer_list');
    $select_opt = get_sub_customers($_POST['customer_id']);


    if (count($select_opt) > 1) {

//        pp($select_opt);

        echo '<tr><td class="label">Sub Customers</td><td>' . array_selector('sub_customer_list', null, $select_opt, $options) .
            '<button type="button" class="add_sub_customer_button btn btn-primary btn-sm">Add Sub Customer</button></td> </tr>';

    }

    $Ajax->activate('sub_customer_list');

//    pp($order->customer_name);

//    text_row(trans("Contact Person:"), "contact_person", $order->contact_person, 30, 30);
//    text_row(trans("Contact Person:"), "contact_person", "", 30, 30);

    hidden("contact_person", "");

    text_row(trans("Company/Customer Name:"), "display_customer", $order->customer_name, 30, 30);
    text_row(trans("Mobile:"), "customer_mobile", $order->phone, 30, 30);
    text_row(trans("Email:"), "customer_email", $order->email, 30, 30);

    text_row(trans("Customer TRN") . ":", 'customer_trn', $order->tax_id, 30, 30);

//    $Ajax->activate('display_customer');
//    $Ajax->activate('contact_person');
//    $Ajax->activate('customer_mobile');
//    $Ajax->activate('customer_email');


    $options = array('select_submit' => true, 'disabled' => null, 'id' => 'invoice_type');
    $select_opt = array(
//            "" => "--Select--",
        "Cash" => "Cash",
        "Credit" => "Credit",
//            "G2" => "G2"
    );
    echo '<tr style="display: none"><td class="label">Payment Type </td><td>' . array_selector('invoice_type', $_POST['invoice_type'], $select_opt, $options) . '</td> </tr>';


    $options = array('select_submit' => true, 'disabled' => null, 'id' => 'invoice_payment');

    global $global_pay_types_array;

    $select_opt = $global_pay_types_array;

//    pp(123123);
//    pp($_POST['customer_id']);

    if ($_POST['customer_id'] == 1) {
        unset($select_opt['PayLater']);
//        unset($select_opt['PayForStaffMistake']);
    }

    if ($order->edit_invoice) {
        unset($select_opt['PayNow']);
        unset($select_opt['PayNoWCC']);
        unset($select_opt['PayLater']);
        unset($select_opt['PayCashAndCard']);
        unset($select_opt['PayByBankTransfer']);
        unset($select_opt['PayByCustomerCard']);
    }

    if ($order->dim_id == 4) { //FOR RTA

        $select_opt["PayByCustomerCard"] = "Pay By Customer Card";
        //unset($select_opt['PayNoWCC']);
        unset($select_opt['PayCashAndCard']);
        unset($select_opt['PayByBankTransfer']);

    } else {
        unset($select_opt['PayByCustomerCard']);
    }


    $pt_style = "";

    if (in_array($_SESSION["wa_current_user"]->access,
        explode(",", $dimension_info['direct_invoicing_permitted_to']))) {

//        $has_full_invoice_access = true;
        $has_full_invoice_access = in_array($_SESSION["wa_current_user"]->access,[48]);

        if (!$has_full_invoice_access) {
            $pt_style = "style='display:none'";
//        else {

            unset($select_opt['PayNow']);
            unset($select_opt['PayNoWCC']);
            unset($select_opt['PayCashAndCard']);
            unset($select_opt['PayByBankTransfer']);
//        unset($select_opt['PayForStaffMistake']);

        }



    }


    if (!isset($_POST['invoice_payment']) || empty($_POST['invoice_payment']))
        $_POST['invoice_payment'] = "PayLater";

//    pp($_POST['invoice_payment']);


    echo '<tr ' . $pt_style . ' ><td class="label">Payment</td><td>' . array_selector('invoice_payment', null, $select_opt, $options) . '</td> </tr>';


//    text_row("Credit Card No.(Last 4 Digit)",'credit_card_no','',4,4,
//        '',null,null,'credit_card_no');

    echo '<tr class="credit_card_no">
<td class="label" style="width: 100% !important;">Credit Card No.(Last 4 Digit)</td><td>
<input type="text" name="credit_card_no" size="4" maxlength="4" value="" style="display: block;"></td> 
</tr>';

//    $Ajax->activate('credit_card_no');


    $sql = "SELECT emp.id,empl_id emp_code, empl_firstname fname, empl_lastname lname
    FROM 0_kv_empl_info emp
    INNER JOIN 0_users usr ON usr.employee_id=emp.id";

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


    $style = "display:none";
    if ($_POST['invoice_payment'] == "PayForStaffMistake") {
        $style = "";
    }

    echo '<tr style="' . $style . '" data-staff_mistake_cust_id="' . $staff_mistake_cust_id . '" id="mistake_staff_id_tr"><td class="label">Staff</td><td>' . array_selector('mistake_staff_id', $order->mistake_staff_id, $select_opt, $options) . '</td> </tr>';

    if (!is_company_currency($order->customer_currency) && in_array($order->trans_type, array(ST_SALESINVOICE, ST_CUSTDELIVERY))) {
        label_row(trans("Customer Currency:"), $order->customer_currency);
        exchange_rate_display(get_company_currency(), $order->customer_currency,
            ($editable ? $_POST['OrderDate'] : $order->document_date));
    }

    if ($_POST['customer_id'] != 1 && $_POST['customer_type'] != "PRO") {
//        customer_credit_row($_POST['customer_id'], $order->credit);
    }


    if ($_SESSION['wa_current_user']->loginname == 'admin') {
        echo '<tr><td colspan="2" align="center"><button type="button" class="add_sub_customer_button">Add Sub Customer</button></td> </tr>';

    }


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
        $str = sales_types_list_row(trans("Price List:"), 'sales_type', null, true);
    } else {
        label_row(trans("Price List:"), $order->sales_type_name);
    }

//    hidden('customer_trn', $order->tax_id);

    $ov_gst = 0;
    $freight_tax = $order->get_shipping_tax();
    $taxes = $order->get_taxes(); // all taxes with freight_tax
    $dec = user_price_dec();

    foreach ($taxes as $taxitem) {
        $taxitem['Value'] = round2($taxitem['Value'], $dec);
        $ov_gst += $taxitem['Value'];
    }

    if ($order->tax_included == 0) {
        $items_added_tax = $ov_gst - $freight_tax;
        $freight_added_tax = $freight_tax;
    } else {
        $items_added_tax = 0;
        $freight_added_tax = 0;
    }

    $invoice_total = $order->get_items_total_dispatch() + $items_added_tax;

    if ($_POST['invoice_payment'] == 'PayNoWCC') {

        $cc_charge_amount = $invoice_total * $SysPrefs->prefs['default_credit_card_charge_percent'] / 100;

        $invoice_total = $invoice_total + $cc_charge_amount;
    }


    $charge_txt = " ";

    if (!empty($cc_charge_amount)) {
        $charge_txt = " CC Charge : " . $cc_charge_amount;
    }

    small_amount_row(trans("Cash Amount") . ":", "cash_amount", null, "class='cash_amount label'");
    small_amount_row(trans("Card Amount") . ":", "card_amount", null, "class='cash_amount label'");

    hidden("hdn_credit_card_charge", $SysPrefs->prefs['default_credit_card_charge_percent']);

    $_POST['total_amount'] = $Ajax->activate('total_amount');


    small_amount_row(trans("Total Amount") . ":", "total_amount", number_format2($invoice_total, 2), null, $charge_txt);
    small_amount_row(trans("Round of Amount") . ":", "round_of_amount", null);

    small_amount_row(trans("Given Amount") . ":", "given_amount", null);
    small_amount_row(trans("Balance") . ":", "balance_amount", null);

//    textarea_row(trans("Comments:"), "Comments", $order->Comments, 2, 2);
//    text_row(trans("Reference") . ":", "customer_ref", $order->customer_ref, 30, 30);
    textarea_row(trans("Ref:"), "customer_ref", $order->Comments, 30, 2);


    hidden('hdn_inv_total_amnt', number_format2($invoice_total, 2));


    end_outer_table(1); // outer table

    $Ajax->activate('items_table');
    if ($change_prices != 0) {
//        foreach ($order->line_items as $line_no => $item) {
//            $line = &$order->line_items[$line_no];
//            $line->price = get_kit_price($line->stock_id, $order->customer_currency,
//                $order->sales_type, $order->price_factor, get_post('OrderDate'));
//        }
        $Ajax->activate('items_table');
    }

    return $customer_error;
}

//--------------------------------------------------------------------------------

function sales_order_item_controls(&$order, &$rowcounter, $line_no = -1)
{
    global $Ajax, $SysPrefs;


    $dimension_info = get_dimension($order->dim_id);

    if (in_array($_SESSION["wa_current_user"]->access, explode(",", $dimension_info['direct_invoicing_permitted_to']))) {
        $dimension_info['has_service_request'] = 0;
    }

//    $dimension_info['has_service_request'] = 0;
    //IF SERVICE REQUEST FLOW IS ENABLED IN THIS COST CENTER, LINE ITEM CONTROLS ARE ENABLED ELSE HIDDEN
    if ($dimension_info['has_service_request'] == 1 && $order->edit_invoice == false)
        return false;

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

        $item_info = get_item($_POST['stock_id']);

        $_POST['bank_service_charge'] = $order->line_items[$id]->bank_service_charge;
        $_POST['bank_service_charge_vat'] = $order->line_items[$id]->bank_service_charge_vat;
        $_POST['discount_amount'] = $order->line_items[$id]->discount_amount;
        $_POST['transaction_id'] = $order->line_items[$id]->transaction_id;
        $_POST['application_id'] = $order->line_items[$id]->application_id;
        $_POST['ref_name'] = $order->line_items[$id]->ref_name;
        $_POST['govt_bank_account'] = $order->line_items[$id]->govt_bank_account;


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

        //Category Permission
        $user = get_user($_SESSION["wa_current_user"]->user);
        $permitted_categorries = "0";
        if (!empty($user["permitted_categories"]))
            $permitted_categorries = $user["permitted_categories"];

        if ($order->fixed_asset)
            stock_disposable_fa_list_cells(null, 'stock_id', null, trans('[Select item]'), true, $order->line_items);
        else
            if (list_updated('stock_id')) {

                sales_items_list_cells(null, 'stock_id', null, false, true, true,
                    ["where" => "c.is_tasheel=0 AND c.category_id in ($permitted_categorries) and c.dflt_dim1=" . $_SESSION['Items']->dim_id]);

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
                $Ajax->activate('govt_bank_account');
            } else {
                $last_service = last_sales_order_detail($order, 'stk_code');
//                $last_service['stock_id'] = "";
                sales_items_list_cells(null, 'stock_id', $last_service['stock_id'], "--", true, true,
                    ["where" => "c.is_tasheel=0 AND c.category_id in ($permitted_categorries) and c.dflt_dim1=" . $_SESSION['Items']->dim_id]);
            }


        $last_added_item = last_sales_order_detail($order, 'stk_code');


        $last_stock_id = $last_added_item ? $last_added_item['stock_id'] : $_POST['stock_id'];

        if (list_updated('stock_id')) {
            $_POST['stock_id'] = $_POST['_stock_id_edit'];
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


        //Modified for AMER
        //Adding PF Amount with Service charge

        $_POST['price'] = price_format($price);
        // default to the customer's discount %

        $_POST['Disc'] = percent_format($order->default_discount * 100);


        /** Modifications for AMER  */

        $_POST['govt_fee'] = $item_info['govt_fee'];

        $_POST['bank_service_charge'] = $item_info['bank_service_charge'];
        $_POST['bank_service_charge_vat'] = $item_info['bank_service_charge_vat'];
        $_POST['extra_service_charge'] = $item_info['extra_service_charge'];

        $_POST['discount_amount'] = "0.0";
        $_POST['transaction_id'] = "";
        $_POST['application_id'] = "";
        $_POST['ref_name'] = "";
        $_POST['govt_bank_account'] = $item_info['govt_bank_account'];

        if (!empty($item_info['discount'])) {

            $_POST['discount_amount'] = $item_info['discount'];
            $_POST['Disc'] = ($_POST['discount_amount'] * 100) / $_POST['price'];

        }

    }

    if ($order->fixed_asset) {
        label_cell(1, '', 'qty');
        hidden('qty', 1);
    } else
        qty_cells(null, 'qty', $_POST['qty'], null, null, $dec);

    if ($order->trans_no != 0) {
        qty_cell($line_no == -1 ? 0 : $order->line_items[$line_no]->qty_done, false, $dec);

    }

    gl_all_accounts_list_cells("", 'govt_bank_account', $_POST['govt_bank_account'], false, false, false, false, false, false);


//    if (in_array($_SESSION["wa_current_user"]->access, $SysPrefs->invoice_edit_permitted_roles)) {
//        small_amount_cells(null, 'govt_fee', null, null, "<a id='myBtn' class='gvt_btn' href='#'></a>");
//
//        small_amount_cells(null, 'price');
//    } else {
//
//
//    }

    small_amount_cells(null, 'govt_fee', null, null);

    small_amount_cells(null, 'bank_service_charge_vat');

    small_amount_cells(null, 'price');


    $_POST['total_bank_charge'] = $_POST['bank_service_charge'] + $_POST['bank_service_charge_vat'];
    $_POST['total_bank_charge'] = $_POST['total_bank_charge'] ? $_POST['total_bank_charge'] : '0.0';


    $_POST['bank_service_charge'] = $_POST['bank_service_charge'] + $_POST['extra_service_charge'];

//    small_amount_cells(null, 'total_bank_charge');


    small_amount_cells(null, 'bank_service_charge');


//    hidden('bank_service_charge', $_POST['bank_service_charge']);
//    hidden('bank_service_charge_vat', $_POST['bank_service_charge_vat']);

    hidden('discount_amount', $_POST['discount_amount']);

    hidden('Disc', percent_format($_POST['Disc']));


    if (in_array($_SESSION['Items']->dim_id, [DT_ADHEED, DT_AMER, DT_ADHEED_OTH])) {

        text_cells(null, 'transaction_id', null, 5, 50,
            false, "", "",
            "class='live_popover' autocomplete='off' data-placement='top'
        data-content='" . $_POST['transaction_id'] . "'  rel='next-popover'");

    } else {
        text_cells(null, 'transaction_id', null, 5, 50,
            false, "", "",
            "class='live_popover' autocomplete='off' data-placement='top'
        data-content='" . $_POST['transaction_id'] . "'  rel='next-popover'");
    }


    text_cells(null, 'application_id', null, 5, 50);

    text_cells(null, 'ref_name', null, 5, 50, false, "", "",
        "class='live_popover' autocomplete='off' data-placement='top' 
        data-content='" . $_POST['ref_name'] . "'  rel='next-popover'");


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


    amount_cell('', false, '', 'line_total');

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

    set_focus('transaction_id');
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
                : ($order->trans_type == ST_SALESQUOTE ? trans('Enter Valid until Date') : ''));
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

    input[name="balance_amount"] {
        pointer-events: none;
        background: #ccc;
    }

    input[name="total_amount"] {
        pointer-events: none;
        background: #ccc;
    }

    input[name="card_amount"] {
        display: none;
    }

    .cash_amount {
        display: none;
    }

    input[name="cash_amount"] {
        display: none;
    }

    /*input[name="credit_card_no"] {*/
    /*display: none;*/
    /**/
    /*}*/
    /*.credit_card_no{*/
    /*display: none !important;*/
    /*}*/

    .card_amount {
        display: none;
    }

    /*span[aria-labelledby="select2-customer_id-container"] {*/
    /*background-color: #ccc;*/
    /*}*/

    /*#_customer_id_sel .select2-selection--single {*/
    /*background: #ccc !important;*/
    /*}*/

    <?php

//     dd($_SESSION['Items']);

     if( !isset($_GET['EditFlag']) || $_GET['EditFlag'] != 'true') { ?>

    #_customer_id_sel {
        /*pointer-events: none;*/
    }

    img[title="Search customers"] {
        display: none;
    }

    <?php } ?>

</style>

<script>


    $(document).ready(function () {

        // $('#_customer_id_sel').parent().css('pointer-events', 'none');
        // $('#_customer_id_sel').parent().find('img').hide();

    });


    $(document).on("change", "select[name='mistake_staff_id']", function () {

        var staff_id = $(this).val();

        var staff_mistake_customer_id = $("#mistake_staff_id_tr").data("staff_mistake_cust_id")

        // alert(staff_mistake_customer_id);

        if (staff_id !== "") {
            $("select[name='customer_id']").val(staff_mistake_customer_id);
            $("select[name='customer_id']").trigger('change');
        }

    });


    $(document).on("change", "select[name='invoice_payment']", function () {

        var payment_method = $(this).val();


        if (payment_method === "PayNoWCC") {
            // $(".credit_card_no").css('display', 'table-row');
        }
        else {
            //$(".credit_card_no").hide();
        }


        if (payment_method === "PayCashAndCard") {

            $("input[name='cash_amount']").show();
            $("input[name='card_amount']").show();

            $(".cash_amount").show();
            $(".card_amount").show();

        }
        else {
            $("input[name='cash_amount']").hide();
            $("input[name='card_amount']").hide();

            $(".cash_amount").hide();
            $(".card_amount").hide();
        }


        if (payment_method === "PayForStaffMistake") {

            $("#mistake_staff_id_tr").show();

        }
        else {
            $("#mistake_staff_id_tr").hide();
        }

    });


    $(document).on("keyup", "input[name='given_amount']", function () {
        // alert(1)

        calculateBalance();

    });

    $(document).on("keyup", "input[name='cash_amount']", function () {

        var payment_amount = $("input[name='hdn_inv_total_amnt']").val();
        var cash_amount = $("input[name='cash_amount']").val();


        var noCommas = payment_amount.replace(/,/g, ''),
            asANumber = +noCommas;
        payment_amount = noCommas;


        var noCommas = cash_amount.replace(/,/g, ''),
            asANumber = +noCommas;
        cash_amount = noCommas;

        var card_amount = 0;
        if (payment_amount) {
            card_amount = (payment_amount - cash_amount).toFixed(2)
        }

        if (card_amount > 0) {

            var credit_card_charge_percent = $("input[name='hdn_credit_card_charge']").val();

            if (credit_card_charge_percent === "") credit_card_charge_percent = 0;

            var credit_card_charge = card_amount * credit_card_charge_percent / 100;
            $("input[name='card_amount']").val(parseFloat(card_amount) + parseFloat(credit_card_charge.toFixed(2)));


            var sum_cash_amount = $("input[name='cash_amount']").val();
            var sum_card_amount = $("input[name='card_amount']").val();
            var repl_total_amnt = parseFloat(sum_cash_amount) + parseFloat(sum_card_amount);

            $("input[name='total_amount']").val(repl_total_amnt);

        }
        else {
            alert("Amount not valid");
            $("input[name='cash_amount']").val(0);
            $("input[name='card_amount']").val(0);
        }


    });


    $(document).on("keyup", "input[name='card_amount']", function () {

        var payment_amount = $("input[name='total_amount']").val();
        var card_amount = $("input[name='card_amount']").val();


        var noCommas = payment_amount.replace(/,/g, ''),
            asANumber = +noCommas;
        payment_amount = noCommas;


        var noCommas = card_amount.replace(/,/g, ''),
            asANumber = +noCommas;
        card_amount = noCommas;

        var cash_amount = 0;
        if (payment_amount) {
            cash_amount = (payment_amount - card_amount).toFixed(2)
        }

        if (cash_amount > 0) {
            $("input[name='cash_amount']").val(cash_amount);
        }
        else {
            alert("Amount not valid");
            $("input[name='cash_amount']").val(0);
            $("input[name='card_amount']").val(0);
        }


    });


    $(document).on("keyup", "input[name='total_amount']", function () {

        calculateBalance();
    });

    $(document).on("keyup", "input[name='round_of_amount']", function () {

        calculateBalance();
    });

    function calculateBalance() {

        var given_amount = $("input[name='given_amount']").val();
        var payment_amount = $("input[name='total_amount']").val();
        var round_of_amount = $("input[name='round_of_amount']").val();

        var noCommas = given_amount.replace(/,/g, ''),
            asANumber = +noCommas;
        given_amount = noCommas;

        var noCommas = payment_amount.replace(/,/g, ''),
            asANumber = +noCommas;
        payment_amount = noCommas;

        var noCommas = round_of_amount.replace(/,/g, ''),
            asANumber = +noCommas;
        round_of_amount = noCommas;

        // debugger;

        if (!round_of_amount)
            round_of_amount = 0;

        given_amount = parseFloat(given_amount) - parseFloat(round_of_amount);

        var balance = 0;
        if (payment_amount) {
            balance = (given_amount) - payment_amount
        }

        if (isNaN(balance))
            balance = 0;

        $("input[name='balance_amount']").val(balance.toFixed(2));

    }

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

            // setTimeout(function () {
            //     $('#_customer_id_sel').parent().css('pointer-events','none');
            //     $('#_customer_id_sel').parent().css('background','#ccc');
            //     $('#_customer_id_sel').find('.select2-selection--single').css('background','#ccc');
            // },2000)


        });

    }

    function fetchEIDNumber(clipText) {

        if (isJson(clipText)) {

            var jsonData = JSON.parse(clipText);

            if (jsonData.HasData) {

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


</script>
