


<style>

    tbody#invoice-pending-tbody td {
        width: 27%;
        font-size: 12px;
        font-weight: bold;
        color: black;
    }

    table#pending-invoice-table th {
        border: 1px solid #ccc;
    }

    .credit_cards {
        width: 20%;
        height: 50px;
    }

    .btn:focus {
        background: #d08221 !important;
        border: 0px solid black;
        color: white;
    }

    #invoices-table-div {
        max-height: 400px;
        overflow-y: auto;
        padding: 0 !important;
    }

    #invoice-list-div {
        max-height: 300px !important;
        overflow-y: auto !important;
    }


    #pending-invoice-table td {
        border: 1px solid #fff;
        font-weight: bolder;
        color: #644942;
    }

    .btn-secondary {
        background: #b2bac5 !important;
        color: #fff;
    }


</style>


<input type="hidden" id="credit_card_charge_percent" value="<?= $SysPrefs->prefs['default_credit_card_charge_percent']; ?>">

<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row" style="padding-top:10px;">
                    <div class="col-md-9">
                        <!-- form card cc payment -->
                        <div class="card">
                            <div class="card-body" id="cashier-form-div">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3>CASHIER WINDOW</h3>
                                    </div>
                                    <div class="col-md-6">
                                        <h3 onclick="go_to_dashboard()"
                                            style="float: right;margin-right: 0px; cursor: pointer; background: #ccc; border-radius: 5px; padding: 2px">
                                            <i class="fa fa-home"></i></h3>
                                    </div>
                                </div>


                                <?php

                                $curr_user = get_user($_SESSION["wa_current_user"]->user);

                                $dim = get_dimension($curr_user['dflt_dimension_id'])

                                ?>

<!--                                <input type="hidden" name="dim_id" id="dim_id" value="--><?//= $curr_user['dflt_dimension_id'] ?><!--">-->

                                <div class="row">
                                    <div class="col-md-5">
<!--                                        <label for="cc" style="font-weight: bold">COST CENTER : --><?//= $dim['name'] ?><!--</label>-->
                                        <label for="cc" style="font-weight: bold">COST CENTER :
                                        </label>

                                        <select class="form-control kt-select2"
                                                name="dim_id" id="dim_id" onchange="loadPendingInvoices();">

                                            <?= prepareSelectOptions($api->get_records_from_table('0_dimensions',['id','name']), 'id', 'name',$curr_user['dflt_dimension_id'],false) ?>

                                        </select>
                                        <p></p>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-5">
                                        <label for="cc_name">BARCODE</label>
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-5">
                                        <label for="cc_name">Customer Name</label><span id="customer_balance" class=" pull-right badge badge-warning"></span>
                                    </div>
                                    <div class="col-md-5">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5 input-group">
                                        <input type="text" autofocus="autofocus" class="form-control" id="barcode">
                                        <div class="input-group-append">
                                            <button onclick="fetchBarcode();" class="btn btn-info"><i
                                                    style="color:#fff !important;"
                                                    class="menu-icon flaticon-refresh"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-5" id="customerSelect">
                                    </div>
                                    <div class="col-md-5" style="display:none;" id="customer-name-div">
                                        <input type="text" class="form-control" id="customer_name"
                                               autocomplete="off"
                                               maxlength="3" readonly value="">

                                    </div>


                                    <input type="hidden" id="date_format" value="<?= getDateFormat(); ?>">
                                    <input type="hidden" id="invoice_selected_type" value="">

                                    <input type="hidden" name="customer_id" id="customer_id">
                                    <input type="hidden" name="trans_no" id="trans_no">
                                    <input type="hidden" name="payment_method" id="payment_method">
                                    <input type="hidden" name="bank_acc" id="bank_acc">
                                </div>
                                <div class="row" id="invoice-list-div">

                                    <div class="col-md-12">
                                        <div class="form-group row" style="margin-top:20px;">
                                            <div class="col-md-2"><label>Invoice #</label></div>
                                            <div class="col-md-2"><label>Date</label></div>
                                            <div class="col-md-2"><label>Amount</label></div>
                                            <div class="col-md-2"><label>Paid Amount</label></div>
                                            <div class="col-md-2"><label>Pay Balance</label></div>
                                            <div class="col-md-2"><label>This Alloc</label></div>
                                        </div>
                                        <div class="form-group row alloc_inv_table" style="margin-top:-25px;"
                                             id="barcode-invoice">
                                            <div class="col-md-2">
                                                <input style="font-size:80%;" type="text" data-trans_no=""
                                                       class="form-control aInvNumber" id="invoice_number"
                                                       autocomplete="off" maxlength="3" readonly="" value="">
                                            </div>
                                            <div class="col-md-2">
                                                <input style="font-size:80%;" type="text" name="tran_date"
                                                       id="tran_date" class="form-control"
                                                       placeholder="Select date" value="<?= Today() ?>"/>
                                            </div>
                                            <div class="col-md-2">
                                                <input style="font-size:80%;" type="text" class="form-control"
                                                       id="invoice_amount" autocomplete="off" maxlength="3"
                                                       readonly="" value="">
                                            </div>
                                            <div class="col-md-2">
                                                <input style="font-size:80%;" type="text" class="form-control"
                                                       id="paid_amount" readonly
                                                       value="">
                                            </div>
                                            <div class="col-md-2">
                                                <input style="font-size:80%;" type="text" class="form-control"
                                                       id="display_amount" readonly value="">
                                                <input type="hidden" class="form-control" id="max_alloc_for_barcode"
                                                       value="">
                                            </div>
                                            <div class="col-md-2">
                                                <input style="font-size:80%;" type="number" step="any" min="0"
                                                       name="this_alloc_amount" class="form-control numpad aAllocAmount"
                                                       id="this_alloc_amount" value="">
                                            </div>
                                        </div>
                                        <div style="margin-top:-25px;" id="invoice-list" class="alloc_inv_table">
                                        </div>


                                    </div>
                                </div>


                                <hr>
                                <div class="form-group row">
                                    <div class="col-md-2">

                                        <button type="button" class="btn btn-primary" id="alloc_all">Allocate All</button>

                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-2">

                                        <label></label>

                                        <input type="text" class="form-control" placeholder="0.00"
                                               id="invoice_amount_final" autocomplete="off" readonly="" value="">
                                    </div>
                                    <div class="col-md-2">

                                        <label></label>

                                        <input type="text" class="form-control" placeholder="0.00"
                                               id="paid_amount_final" autocomplete="off" readonly="" value="">
                                    </div>
                                    <div class="col-md-2">

                                        <label></label>

                                        <input type="text" class="form-control" placeholder="0.00"
                                               id="pay_balance_final" autocomplete="off" readonly="" value="">
                                    </div>
                                    <div class="col-md-2">
                                        <label style="margin-bottom:0!important;">Amount Total</label>
                                        <input type="number" min="0" step="any" class="form-control numpad"
                                               placeholder="0.00" id="this_alloc_final" autocomplete="off" value=""
                                               style="border: 2px solid #2786fb; font-weight: bold">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <button type="button" style="width:50%;height: 50px; font-size: 25px;"
                                                class="btn btn-secondary pull-left btn-block"
                                                onclick="cancel();">CANCEL
                                        </button>
                                    </div>

                                    <div class="col-md-6">
                                        <button id="paynow_btn_single" type="button" style="height: 50px; font-size: 25px;
                                    right: 10px;" class="btn btn-primary pull-right"
                                                onclick="paydata();">PAY NOW
                                        </button>

                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- /form card cc payment -->

                    </div>
                    <div class="col-md-3">
                        <!-- form card cc payment -->
                        <div class="card">
                            <div class="card-body" id="invoices-table-div">
                                <table class="table table-responsive" id="pending-invoice-table" style="display: table !important;">
                                    <thead style="background:#eeeeee;">

                                    <tr>
                                        <th>Invoice #</th>
                                        <!--                                    <th>Token</th>-->
                                        <th>Date</th>
                                    </tr>

                                    </thead>
                                    <tbody id="invoice-pending-tbody">


                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <!-- /form card cc payment -->

                    </div>
                </div>




                <hr>



                <div class="row">

                    <div class="col-lg-12">

                        <div class="kt-portlet kt-portlet--height-fluid ">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <?= trans('FIND INVOICE') ?>
                                    </h3>
                                </div>
                            </div>

                            <div class="kt-portlet__body kt-portlet__body--fluid kt-portlet__body--fit">
                                <div class="kt-widget4 kt-widget4--sticky">
                                    <div class="kt-widget4__items kt-portlet__space-x kt-margin-t-15">
                                        <div class="form-group">
                                            <input type="text" id="txt_print_invoice_number" class="form-control" placeholder="<?= trans("Enter invoice number") ?>">
                                        </div>

                                        <div class="kt-">
                                            <div class="kt-form__actions">
                                                <button type="button" data-method="print"  class="btn btn-primary btn-find-invoice"><?= trans('Print Invoice') ?></button>
                                                <button type="button" data-method="edit"  class="btn btn-success btn-find-invoice"><?= trans('Update Transaction ID') ?></button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="col-lg-12">
                        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <?= trans("TODAYS'S INVOICES") ?>
                                    </h3>
                                </div>

                            </div>
                            <div class="kt-portlet__body kt-portlet__body--fit">
                                <div class="" id="kt_datatable_todays_invoices"></div>
                            </div>
                        </div>
                    </div>



                </div>







            </div>

            <!-- end:: Content -->
        </div>
    </div>
</div>


<div class="modal fade" role="dialog" id="PaymentModel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i
                        class="fa fa-close"></i>
                </button>
                <h4 class="modal-title" style="position: absolute;">PAYMENT</h4>
            </div>


            <div class="modal-body">
                <div class="col-md-12">

                    <button type="button" style="width:32%; height: 100px; font-size: 25px;right: 10px;"
                            class="btn btn-primary paymentchooser" data-acc_type="0,3" data-chooser="1">CASH
                    </button>
                <button type="button" style="width:32%; height: 100px; font-size: 25px;right: 10px;"
                            class="btn btn-primary paymentchooser" data-acc_type="2,4" data-chooser="2">CARD
                    </button>

                    <button type="button" style="width:32%; height: 100px; font-size: 25px;right: 10px;"
                            class="btn btn-primary paymentchooser" data-acc_type="1,4" data-chooser="3">BANK TRANSFER
                    </button>

                </div>
                <div class="col-md-12" id="card_data" style="margin-top: 8px;display:none">

                </div>


                <div class="row col-md-12">


                    <div class="col-md-3" style="margin-top: 8px;">
                <span>TRANSACTION DATE : <input type="text" step="any" id="pay_date"
                                                class="form-control ap-datepicker" readonly
                                                placeholder="" value="<?= Today() ?>"></span>
                    </div>

                    <div class="col-md-3" style="margin-top: 8px;">
                <span>Paying Amount : <input type="number" step="any" id="paying_amount"
                                             class="form-control"
                                             placeholder="Amount" value="0" readonly></span>
                    </div>

                    <div class="col-md-3" style="margin-top: 8px;">
                <span>Discount : <input type="number" step="any" id="discount"
                                        class="form-control"
                                        placeholder="Discount" value="0" min="0" readonly onclick="admin_approval_modal();"></span>
                    </div>

                    <div class="col-md-3" style="margin-top: 8px;">
                    <span>Bank Charge (%) : <input type="number" step="any" id="bank_charge"
                                                   class="form-control numpad"
                                                   placeholder="Bank Charge (%)" value="0" min="0"></span>
                    </div>


                    <div class="col-md-3" style="margin-top: 8px;">
                    <span>Round of Amount : <input type="number" step="any" id="rounded_difference" name="rounded_difference"
                                                   class="form-control numpad"
                                                   placeholder="Round of Amount" value="0"></span>
                    </div>

                </div>


                <hr>


                <hr>
                <div class="row col-md-12">
                    <div class="col-md-4" style="margin-top: 8px;">
                        <span>Given Amount : <input type="number" step="any" id="given_amount" class="form-control numpad"
                                                    value="0"></span>
                    </div>

                    <div class="col-md-4" style="margin-top: 8px;">
                            <span>Change : <input type="number" step="any" id="change_amount" class="form-control"
                                                  disabled></span>
                    </div>
                </div>

                <!--                       <h3 style="margin: 12px;color: #000000; text-align: center;-->
                <!--                      border: 1px solid #CCC;">Amount Before Rounding : <span id="amount_before_rounding" style="padding: 4px;-->
                <!--                      font-weight: bold;"></span> AED </h3>-->
                <!--                      <h3 style="margin: 12px;color: #5867dd; text-align: center;-->
                <!--                      border: 1px solid #CCC;">Rounded Amount : <span id="amount_rounded" style="padding: 4px;-->
                <!--                      font-weight: bold;"></span> AED </h3>-->
                <h3 style="margin: 12px;color: #063f08; text-align: center;
                      border: 1px solid #CCC;">
                    Amount to be Collected : <span id="amount_to_be_collected" style="padding: 4px;
                      font-weight: bold;"></span> AED </h3>


                <button type="button" style="float: right; margin: 8px 9px 3px 9px;" class="btn btn-primary"
                        id="btn_make_payment">Proceed To Pay
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Admin Password Modal -->
<div class="modal fade" role="dialog" id="AdminModal">
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
                    <input type="number" step="any" class="form-control numpad" placeholder="Enter Discount" id="discount_for_admin_approval">
                </div>
                <div class="form-group">
                    <label for="admin_password" class="col-form-label">Password for user: <b>'admin'</b></label>
                    <input type="password" class="form-control" id="admin_password" placeholder="Password">
                </div>
                <button type="submit" class="btn btn-primary mb-2 pull-right" onclick="check_password();" id="admin_password_confirm">Confirm</button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--
 -->
<script src="assets/plugins/general/jquery/dist/jquery.js" type="text/javascript"></script>
<script src="assets/numpad/jquery.numpad.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<script src="assets/js/config.js" type="text/javascript"></script>

<script src="assets/plugins/general/sticky-js/dist/sticky.min.js" type="text/javascript"></script>

<script src="assets/js/scripts.bundle.js" type="text/javascript"></script>
<script src="assets/plugins/general/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"
        type="text/javascript"></script>

<script src="assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>


<script src="assets/js/axispro.js" type="text/javascript"></script>
<script src="assets/js/jquery-dateformat.min.js" type="text/javascript"></script>
<script src="assets/js/jquery.doubleScroll.js" type="text/javascript"></script>


<!--    <script src="assets/plugins/general/toastr/build/toastr.min.js" type="text/javascript"></script>-->
<script src="assets/plugins/general/sweetalert2/dist/sweetalert2.min.js" type="text/javascript"></script>
<script src="assets/plugins/general/js/global/integration/plugins/sweetalert2.init.js"
        type="text/javascript"></script>
<!-- jQuery.NumPad -->
<script type="text/javascript">
    // alert('footer');
    // Set NumPad defaults for jQuery mobile.
    // These defaults will be applied to all NumPads within this document!
    $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="display:block;"></table>';
    // $.fn.numpad.defaults.position = 'absolute';
    // $.fn.numpad.defaults.rowTpl = '<tr style="width:100%;"></tr>';
    $.fn.numpad.defaults.cellTpl = '<td style="border:none!important;"></td>';
    $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
    $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" />';
    $.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-block btn-outline-primary"></button>';
    $.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn btn-block btn-outline-primary"></button>';
    $.fn.numpad.defaults.decimalSeparator = '.';
    $.fn.numpad.defaults.hidePlusMinusButton = true;
    $.fn.numpad.defaults.textDelete = 'Delete';
    // $.fn.numpad.defaults.onChange = function(event, value){alert(value);};

    // $.fn.numpad.defaults.positionX = 'center';
    // $.fn.numpad.defaults.positionY = 'middle';

    // $.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

    // Instantiate NumPad once the page is ready to be shown
    //     $(document).ready(function(){
    //         // alert('ready');
    //         $( ".numpad" ).numpad();

    //         // $('.pinpad').numpad({
    //         //     decimalSeparator: '.',
    //         //     hidePlusMinusButton: true
    //         // });

    // });
    $(document).ready(function(){
        // initialiseNumpad();
        resize_invoices_table();
    });
    function initialiseNumpad(){
        $( ".numpad" ).numpad();
        console.log('numpad initialised');
    }
    // $(function () {
    //             // $('#previousTokensDiv').css({'min-height': $('#currentTokenDiv').height()+'px'});
    //             // $( ".numpad" ).numpad();
    //             resize_invoices_table();
    //         });
    function admin_approval_modal(){
        // alert('clicked');
        $('#AdminModal').modal('show');
        $("#AdminModal").on('shown.bs.modal', function(){
            // $("#discount_for_admin_approval").numpad();

            // $(this).find('#discount_for_admin_approval').focus();
            // $(this).find('#admin_password').focus();

        });
// return false;
    }
    // $('#admin_password_confirm').on("click",function (e) {
    function check_password(){
        var pass = $('#admin_password').val();
        var disc = $('#discount_for_admin_approval').val();
        var discount_is_numeric = $.isNumeric(disc);
        // alert(disc);
        if(pass.length==0){
            swal.fire(
                'Warning!',
                'No Password Entered!!!',
                'warning'
            );
        }else{
            // if(!discount_is_numeric || parseFloat(disc)<0){
            //     swal.fire(
            //         'Warning!',
            //         'Please Enter a Discount greater than or equal to zero!!!',
            //         'warning'
            //         );
            // }else{


            var params = {
                password: pass,
                discount:parseFloat(disc),
                method: 'check_admin_password'
            };

            AxisPro.APICall("POST", ERP_FUNCTION_API_END_POINT, params, function (data) {

                if(data.status==true){
                    // alert('success');
                    $("#discount").val(data.discount);
                    $("#discount").trigger("change");
                    $('#admin_password').val('');
                    $('#discount_for_admin_approval').val('');
                    $('#AdminModal').modal('hide');
                    // alert("discount is :"+data.discount);
                }else{
                    swal.fire(
                        'Error!',
                        'Something went wrong!!!',
                        'error'
                    );
                }



            });

            // }

        }
    }

    // });
    function resize_invoices_table() {
        $('#invoices-table-div').css({'max-height': $('#cashier-form-div').height() + 33 + 'px'});
    }


    var allocation_objects = [];

    var KTAppOptions = {
        "colors": {
            "state": {
                "brand": "#5d78ff",
                "light": "#ffffff",
                "dark": "#282a3c",
                "primary": "#5867dd",
                "success": "#34bfa3",
                "info": "#36a3f7",
                "warning": "#ffb822",
                "danger": "#fd3995"
            },
            "base": {
                "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
            }
        }
    };

    function cancel() {

        window.location.href = "<?=route('cashier_touch_screen')?>";

    }

    function update_this_alloc(rowid) {
        var max_alloc = '#hidden_display_amount_' + rowid;
        var this_alloc = '#this_alloc_amount_' + rowid;

        var max_alloc_val = parseFloat($(max_alloc).val());

        var existing_val = $(this_alloc).val();



        if(existing_val != 0 && existing_val!='' && existing_val != '0.00') {

            // console.log(234234234);
            // console.log(existing_val);
            max_alloc_val = '0.00';

        }


        $(this_alloc).val(max_alloc_val);
        update_max_alloc();

        // alert(max_alloc_val);
    }

    function update_max_alloc() {
        var sum = 0.0;
        $('#invoice-list > .row  > .alloc_div').each(function () {
            var alloc_val = $(this).find('.alloc_val').val();
            sum += parseFloat(alloc_val);
        });
        //just update the total to sum
        $('#this_alloc_final').val(parseFloat(sum));
    }

    function paydata() {
        var sum = 0.0;
        var alloc_invoice_select_flag = false; //flag to check if atleast one invoice value is selected based on alloc value
        var open_payment_modal_flag = true;
        var invoice_alloc_error_flag = false;
        $('#invoice-list > .row  > .alloc_div').each(function () {
            var alloc_val = parseFloat($(this).find('.alloc_val').val());
            var max_alloc_val = parseFloat($(this).find('.hidden_display_amount').val());
            if (alloc_val > max_alloc_val) {
                open_payment_modal_flag = false;
                invoice_alloc_error_flag = true;
            }

            if (alloc_val > 0.00) {
                alloc_invoice_select_flag = true;
            }
            sum += parseFloat(alloc_val);
        });

        if (invoice_alloc_error_flag == true) {

            swal.fire(
                'Warning!',
                'Alloc Amount should be less than or equal to Pay Balance!',
                'warning'
            );
        }

        var alloc_final_val = parseFloat($('#this_alloc_final').val());

        if ((alloc_final_val != sum) && (alloc_invoice_select_flag === true)) {
            open_payment_modal_flag = false;
            swal.fire(
                'Warning!',
                'Maximum Alloc Amount should be equal to Sum of all Alloc Amount!',
                'warning'
            );
        }

        var barcode_alloc_val = parseFloat($("#this_alloc_amount").val()); //under barcode
        var max_alloc_for_barcode = parseFloat($("#max_alloc_for_barcode").val()); //max alloc under barcode

        if (barcode_alloc_val > max_alloc_for_barcode) {
            open_payment_modal_flag = false;
            swal.fire(
                'Warning!',
                'Alloc Amount should be less than or equal to Pay Balance!',
                'warning'
            );
        }
        if ((alloc_final_val != barcode_alloc_val) && (barcode_alloc_val > 0.00)) {
            open_payment_modal_flag = false;
            swal.fire(
                'Warning!',
                'Maximum Alloc Amount should be equal to Sum of all Alloc Amount!',
                'warning'
            );
        }
        if (alloc_final_val > 0) {
            if (open_payment_modal_flag === true) {
                // jQuery.noConflict();

                $("#paying_amount").val(alloc_final_val);

                allocation_objects = [];

                var barcode_inv_num = $("#invoice_number").val();
                var barcode_trans_no = $("#invoice_number").data('trans_no');
                var barcode_amount = $("#this_alloc_amount").val();

                if (parseFloat(barcode_amount) !== 0) {
                    allocation_objects.push({
                        inv_no: barcode_inv_num,
                        trans_no: barcode_trans_no,
                        amount: barcode_amount
                    })
                }

                var loop = 1;
                $('#invoice-list .form-group').each(function () {

                    var inv_num;
                    var trans_no;
                    var amount;

                    inv_num = $(this).find("#invoice_number_" + loop).val();
                    trans_no = $(this).find("#invoice_number_" + loop).data('trans_no');
                    amount = $(this).find("#this_alloc_amount_" + loop).val();

                    allocation_objects.push({
                        inv_no: inv_num,
                        trans_no: trans_no,
                        amount: amount
                    });

                    loop++;

                });

                $('#PaymentModel').modal('show');

            }

        } else {
            swal.fire(
                'Warning!',
                'No Invoice Selected or No Amount entered!',
                'warning'
            );
        }

    }

    $("#btn_make_payment").click(function () {

        var this_btn = $(this);

        this_btn.html("Please Wait ....");

        this_btn.attr("disabled", "disabled");

        var tran_date = $("#pay_date").val();

        var customer_id = $("#customer_id").val();
        var amount = $("#paying_amount").val();
        var discount = $("#discount").val();
        var bank_acc = $("#bank_acc").val();
        var bank_charge = $("#bank_charge").val();

//used for round off
        discount = parseFloat(discount);

        if(!discount) discount = 0;

        if(bank_charge) {
            bank_charge = parseFloat(bank_charge);
            amount = parseFloat(amount);
            amount = amount-discount;
            coll_amount = amount+((amount*bank_charge)/100)
        }
        var rounded_amount = $("#rounded_difference").val();
        // var difference = (rounded_amount-coll_amount).toFixed(2); //rounded difference in value; if positive, then profit for company else loss for company
        //end round off

        var payment_method = $("#payment_method").val();

        if (payment_method === "") {

            swal.fire(
                'Warning!',
                'Please select a Payment Method (CASH or CARD)',
                'warning'
            );

            this_btn.html("Proceed To Pay");
            this_btn.removeAttr('disabled');

            return false;
        }

        var dim_id = $("#dim_id").val();

        var params = {
            method: "pay_invoice",
            format: "json",
            payment_method: payment_method,
            // trans_no: trans_no,
            amount: amount,
            rounded_difference:rounded_amount,//rounded difference in value; if positive, then profit for company else loss for company
            customer_id: customer_id,
            discount: discount,
            bank_acc: bank_acc,
            bank_charge: bank_charge,
            tran_date: tran_date,

            alloc_invoices: allocation_objects,
            dim_id : dim_id

        };

        // alert(JSON.stringify(params));


        AxisPro.APICall("POST", ERP_FUNCTION_API_END_POINT, params, function (data) {

            this_btn.removeAttr('disabled');

            if (data.status === "OK") {
                toastr.success(data.msg);

                // var print_params = "PARAM_0=" + data.payment_no + "-12&PARAM_1=" +
                //     data.payment_no + "-12&PARAM_2=&PARAM_3=0&PARAM_4=&PARAM_5=&PARAM_6=&PARAM_7=0&REP_ID=112";
                //
                // var print_link = ERP_ROOT_URL + "reporting/prn_redirect.php?" + print_params;


                var print_link = ERP_ROOT_URL+"rcpt_print/index.php?trans_no="+ data.payment_no;

                window.open(
                    print_link,
                    '_blank'
                );

                setTimeout(function () {
                    window.location.href = window.location.href;
                }, 1000);
            } else {

                swal.fire(
                    'Error!',
                    data.msg,
                    'error'
                ).then(function () {
                    window.location.reload();
                });

            }

        });

    });

    function loadPendingInvoices() {

        var params = {method: 'todays_invoices',show_only_pending:'1', format: 'json', dim_id : $("#dim_id").val()};

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, params, function (data) {

            var tbody_html = "";

            $.each(data, function (key, value) {

                var recall_style = "style=display:none";
                var tr_style = "style=background:#ff4b55";
                // if (value.payment_status_id === "1" && value.qms_token_done === '0') {
                if (value.payment_status_id === "1") {
                    recall_style = "style=display:block";
                    tr_style = "style=background:#9deabf";
                }

                tbody_html += "<tr " + tr_style + ">";
                tbody_html += "<td>" + value.invoice_no + "</td>";
                // tbody_html += "<td>" + value.qms_token + "</td>";
                tbody_html += "<td>" + value.transaction_date + "" +
                    "<button data-ref='"+value.invoice_no+"' " +
                    "class='btn btn-block btn-sm btn-primary btn-call btn-cashier-call'>Call</button></td>";


                tbody_html += "</tr>";


            });


            $("#invoice-pending-tbody").html(tbody_html);


        });


    }


    $(document).on("click",".btn-cashier-call",function () {

        $("#barcode").val("");

        var this_ref = $(this).data("ref");

        $("#barcode").val(this_ref);
        $("#barcode").change();

    });


    $(document).ready(function () {




        //load pending invoices
        loadPendingInvoices();






        $("#customer-name-div").hide();
        var params = {
            method: 'get_all_customers'
        };
        AxisPro.APICall("GET", ERP_FUNCTION_API_END_POINT, params, function (data) {
            var customerSelectData = '<select class="select2" style="width:100%;" id="customer_id_in_select" onchange="get_unpaid_invoices();">';
            customerSelectData += '<option value="" selected disabled>Select a Customer</option>';
            data.forEach(function (item) {
                customerSelectData += '<option value="' + item.debtor_no + '">' + item.name + '</option>';
            });
            customerSelectData += '</select>';
            // alert(customerSelectData);
            $("#customerSelect").append(customerSelectData);
            $(".select2").select2();
        });


        $(document).on('click', '.paymentchooser', function () {
            var type = $(this).data('chooser');

            var acc_type = $(this).data("acc_type");

            var credit_card_charge_percent = $("#credit_card_charge_percent").val();

                if (type === 2) {
                $('#card_data').show();
                $('#payment_method').val("CreditCard");
                    $("#bank_charge").val(credit_card_charge_percent);

                    loadBanks(acc_type);

                    $('.paymentchooser').css('background-color', '#384ad7');
                    $(this).css('background-color', '#D08221');


                }
                else if(type === 3){
                    $('#card_data').show();
                    $('#payment_method').val("BankTransfer");

                $("#bank_charge").val(0);

                loadBanks(acc_type);

                    $('.paymentchooser').css('background-color', '#384ad7');
                    $(this).css('background-color', '#D08221');
                }

                else {
                $('#card_data').show();
                $('#payment_method').val("Cash");

                $("#bank_charge").val(0);

                loadBanks(acc_type);

                    $('.paymentchooser').css('background-color', '#384ad7');
                    $(this).css('background-color', '#D08221');

            }
        });


        $(document).on('click', '.btn', function () {

        });
        $('#customer_name').on('click', function () {
            // alert('clicked');
            $("#customer-name-div").hide();
            $("#customerSelect").show();

        });

        function loadBanks(acc_type) {

            AxisPro.BlockDiv("#kt_content");

            AxisPro.APICall("GET", ERP_FUNCTION_API_END_POINT, {
                method: 'get_bank_accounts',
                acc_type: acc_type,
                format: "json"
            }, function (data) {

                AxisPro.UnBlockDiv("#kt_content");


                if (data) {

                    var html = "";

                    $.each(data, function (key, val) {

                        html += '<button type="button" data-id="' + val.id + '" class="btn btn-primary credit_cards">' + val.bank_account_name + '</button>';

                    });

                    $("#card_data").html(html);


                    //Set First listed bank selected first
                    var first_button = $("#card_data button").eq(0);
                    $("#bank_acc").val($(first_button).data("id"));

                    $(first_button).css({background: "#d08221"});
                    $(first_button).css({border: "0px solid black"});
                    $(first_button).css({color: "white"});


                }

            });

        }




        $("#barcode").change(function (e) {

            var ref = $(this).val();
            var dim_id = $("#dim_id").val();
            var params = {
                ref: ref,
                method: 'find_invoice',
                dim_id: dim_id
            };

            AxisPro.APICall("GET", ERP_FUNCTION_API_END_POINT, params, function (data) {

                $("#customer_id").val(data.debtor_no);
                $("#customer_name").val(data.name);
                $("#invoice_number").val(data.reference);

                $("#invoice_number").attr('data-trans_no', data.trans_no);

                $("#trans_no").val(data.trans_no);
                $("#tran_date").val(data.tran_date);
                $("#invoice_amount").val(data.total_amount);
                $("#paid_amount").val(data.alloc);
                $("#display_amount").val(data.remaining_amount);
                $("#max_alloc_for_barcode").val(data.remaining_amount);
                $("#paying_amount").val(data.remaining_amount);
                $("#this_alloc_amount").val(data.remaining_amount);
                $("#this_alloc_amount").attr({"max": data.remaining_amount});

                $("#invoice-list").hide();
                $("#invoice_amount_final").val(data.total_amount);
                $("#paid_amount_final").val(data.alloc);
                $("#pay_balance_final").val(data.remaining_amount); //display amount final
                $("#this_alloc_final").val(data.remaining_amount); //alloc amount final
                $("#barcode-invoice").show();
                $("#invoice_selected_type").val('barcode');
                $("#customer-name-div").show();
                $("#customerSelect").hide();
                $('#customer_id_in_select').val([]);//previous select value cleared

                // $('#customer_id_in_select').select2("val", data.debtor_no);

                resize_invoices_table();

                // var customer_id = data.debtor_no;
                var params = {
                    customer_id: data.debtor_no,
                    method: 'get_customer_balance'
                };
                AxisPro.APICall("GET", ERP_FUNCTION_API_END_POINT, params, function (data) {
                    // alert(data.customer_id);
                    // $("#customer_balance").html("Balance : <b>"+parseFloat(data.customer_balance)+"</b>");
                });


            });

        });


    });
    $("#this_alloc_amount").change(function (e) {
        var alloc_amount = $("#this_alloc_amount").val(); //under barcode
        var max_alloc_amount = $("#max_alloc_for_barcode").val();
        $("#this_alloc_final").val(alloc_amount); //alloc amount final
    });



    function get_unpaid_invoices() {
        $("#barcode").val('');
        $("#customer_id").val($('#customer_id_in_select').val());
        $("#customer_name").val('');
        $("#invoice_number").val('');
        $("#trans_no").val('');
        $("#invoice_amount").val('');
        $("#paid_amount").val('');
        $("#display_amount").val('');
        $("#paying_amount").val('');
        $("#invoice_amount_final").val('');
        $("#paid_amount_final").val('');
        $("#pay_balance_final").val(''); //display amount final
        $("#max_alloc_for_barcode").val(''); //pay balance amount final
        $("#this_alloc_amount").val('');

        var debtor_no = $('#customer_id_in_select').val();
        var dim_id = $("#dim_id").val();
        var params = {
            debtor_no: debtor_no,
            // except_trans_no: data.trans_no,
            method: 'get_unpaid_invoices',
            dim_id : dim_id
        };
        AxisPro.APICall("GET", ERP_FUNCTION_API_END_POINT, params, function (data) {
            // alert('unpaid invoices');
            var invoiceAppendData = '';
            var i = 0;
            var invoice_amount_final = 0;
            var paid_amount_final = 0;
            var pay_balance_final = 0;
            data.forEach(function (item) {
                i++;
                var invoice_number = item.reference;
                var tran_date = item.tran_date;
                var invoice_amount = (item.total_amount);
                var paid_amount = (item.alloc);
                var display_amount = (item.remaining_amount);
                invoice_amount_final += parseFloat(invoice_amount);
                paid_amount_final += parseFloat(paid_amount);
                pay_balance_final += parseFloat(display_amount);

                invoiceAppendData += '<div class="form-group row" id="row_' + i + '" data-invoice_id="' + invoice_number + '">' +
                    '<div class="col-md-2" style="margin-top:5px;">' +
                    '<input style="font-size:80%;" disabled placeholder="0.00" type="text" data-trans_no="' + item.trans_no + '" class="form-control" id="invoice_number_' + i + '" autocomplete="off" disabled="" value="' + invoice_number + '">' +
                    '</div>' +
                    '<div class="col-md-2" style="margin-top:5px;">' +
                    '<input style="font-size:80%;" disabled placeholder="0.00" type="text" id="tran_date_' + i + '" class="form-control" placeholder="Select date" value="' + tran_date + '"/>' +
                    '</div>' +
                    '<div class="col-md-2" style="margin-top:5px;">' +
                    '<input style="font-size:80%;" disabled placeholder="0.00" type="text" class="form-control" id="invoice_amount_' + i + '" autocomplete="off" disabled="" value="' + invoice_amount + '">' +
                    '</div>' +
                    '<div class="col-md-2" style="margin-top:5px;">' +
                    '<input style="font-size:80%;" disabled placeholder="0.00" type="text" class="form-control" id="paid_amount_' + i + '" disabled value="' + paid_amount + '">' +
                    '</div>' +
                    '<div class="col-md-2" style="margin-top:5px;">' +
                    '<input style="font-size:80%;" disabled placeholder="0.00" type="text" class="form-control" id="display_amount_' + i + '" disabled value="' + display_amount + '">' +
                    '</div>' +
                    '<div class="col-md-2 input-group alloc_div" style="margin-top:5px;">' +
                    '<input style="font-size:80%;" type="number" step="any" min="0" name="this_alloc_amount_' + i + '" class="form-control numpad alloc_val" id="this_alloc_amount_' + i + '" value="0.00" max="' + display_amount + '" onchange="update_max_alloc();" onchange="update_max_alloc();">' +
                    '<input type="hidden" class="hidden_display_amount" id="hidden_display_amount_' + i + '" value="' + display_amount + '">' +
                    '<div class="input-group-append">' +
                    '<button style="font-size:80%;" class="btn btn-success alloc-btn" onclick="update_this_alloc(' + i + ')" type="button">all</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            })
            $("#invoice-list").html(invoiceAppendData);
// initialiseNumpad();
            $("#invoice_amount_final").val((invoice_amount_final));
            $("#paid_amount_final").val((paid_amount_final));
            $("#pay_balance_final").val((pay_balance_final)); //display amount final
            $("#invoice-list").show();
            $("#barcode-invoice").hide();
            $("#invoice_selected_type").val('select_dropdown');
            $("#customer-name-div").hide();
            $("#customerSelect").show();
            update_max_alloc();

            resize_invoices_table();

            var params = {
                customer_id: $('#customer_id_in_select').val(),
                method: 'get_customer_balance'
            };
            AxisPro.APICall("GET", ERP_FUNCTION_API_END_POINT, params, function (data) {
                // alert(data.customer_id);
                // $("#customer_balance").html("Balance : <b>"+parseFloat(data.customer_balance)+"</b>");
            });

        });
    }

    $(document).on('click', '.credit_cards', function () {


        $('.credit_cards').css('background-color', '#384ad7');
        $(this).css('background-color', '#D08221');

        var bank_acc = $(this).data("id");
        $("#bank_acc").val(bank_acc);

    });

    function go_to_dashboard() {

        window.location.href = "index.php";

    }

    function fetchBarcode() {
        $("#barcode").trigger("change");
    }


    function calculateChange() {
        var paying_amount = parseFloat($("#paying_amount").val());
        var given_amount = parseFloat($('#given_amount').val());

        if (paying_amount <= 0 || given_amount <= 0)
            return false;

        // var discount = parseFloat($("#discount").val());
        // var tot_payable = paying_amount - discount;
        // var change_amount = given_amount - tot_payable;
        var coll_amount = paying_amount;
        var charge = $("#bank_charge").val();
        var discount = $("#discount").val();
        discount = parseFloat(discount);

        if(!discount) discount = 0;

        if(charge) {
            charge = parseFloat(charge);
            paying_amount = parseFloat(paying_amount);
            paying_amount = paying_amount-discount;
            coll_amount = paying_amount+((paying_amount*charge)/100)
        }
        var rounded_amount = Math.round(coll_amount);
        var change_amount = given_amount - rounded_amount;

        $("#change_amount").val(change_amount.toFixed(2));




    }

    $("#given_amount").change(function (e) {
        calculateChange();
    });

    $("#discount").change(function () {
        calculateChange();
    });
    $("#paying_amount").change(function () {
        calculateChange();
    });

    $("#alloc_all").click(function() {

        $(".alloc-btn").trigger('click');

    });


    $(document).ajaxComplete(function (event, xhr, settings) {

        var responseText = xhr.responseText;
        var responseJson = $.parseJSON(responseText);

        if (responseJson.status === 'LOGIN_TIME_OUT') {

            swal.fire(
                'Login TimeOut !',
                responseJson.msg,
                'error'
            ).then(function () {
                window.location.reload();
            });

        }

    });



    setInterval(function() {

        var payment_amount = $("#paying_amount").val();
        var coll_amount = payment_amount;
        var charge = $("#bank_charge").val();
        var discount = $("#discount").val();
        discount = parseFloat(discount);

        if(!discount) discount = 0;

        if(charge) {
            charge = parseFloat(charge);
            payment_amount = parseFloat(payment_amount);
            payment_amount = payment_amount-discount;
            coll_amount = payment_amount+((payment_amount*charge)/100)
        }
        // var rounded_amount = Math.round(coll_amount);
        var rounded_amount = $("#rounded_difference").val();
        if(rounded_amount)
            rounded_amount = parseFloat(rounded_amount);

        var difference = (rounded_amount-coll_amount);
        $("#amount_before_rounding").html(parseFloat(coll_amount).toFixed(2));
        // $("#amount_to_be_collected").html(parseFloat(Math.round(coll_amount)).toFixed(2));

        var rnd_amt = $("#rounded_difference").val();

        if(rnd_amt)
            rnd_amt = parseFloat(rnd_amt);
        else
            rnd_amt = 0;

        $("#amount_to_be_collected").html(parseFloat(coll_amount+rnd_amt).toFixed(2));
        $("#amount_rounded").html(difference.toFixed(2));



    },500)



</script>


<?php //include('footer.php'); ?>
