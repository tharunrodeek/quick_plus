<?php include "header.php" ?>


<?php

$application = isset($_GET['action']) ? $_GET['action'] : "list";

//$get_customers = $api->get_customers('array');

?>
<style>
    .ClsForCompany {
        display: none;
    }
/*
    #customer_id {
        display: none;
    }*/
</style>

<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">


                    <div class="kt-portlet">
                        <div class="kt-portlet__head border-bottom-0">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('RECEPTION') ?>
                                </h3>
                            </div>
                        </div>

                        <OBJECT id="EIDAWebComponent" style="border:solid 1px gray; display: none"
                                CLASSID="CLSID:A4B3BB86-4A99-3BAE-B211-DB93E8BA008B"
                                width="130" height="154">
                        </OBJECT>
                        <span id="loading_data" style="visibility:hidden" ><font color="red">Loading public data ...</font></span>

                        <form class="kt-form kt-form--label-right" id="item_form">

                            <input type="hidden" name="display_customer" id="display_customer">
                            <input type="hidden" id="customer_ref" name="customer_ref">

                            <div class="text-center" style="font-size: 13pt;display: none;">
                                <input form="item_form" type="radio" value='1' name="GroupCompany" id="individual" checked /> Individual &nbsp;&nbsp;
                                 <input form="item_form" type="radio" value='2' name="GroupCompany" id="company" /> Company

                            </div>

                            <hr>

                            <div class="kt-portlet__body" style="padding:20px !important">
                                <div class="kt-portlet__body kt-margin-t-20">
                                    <div class="row">




                                        <div class="col-lg-6 ClsForIndividual">
                                            <div class="form-group row">
                                                <label class="col-4 col-form-label">
                                                    <?= trans('EID Mobile Number') ?> :
                                                </label>
                                                <div class="col-8">
                                                    <input type="text" id="customer_mobile" name="customer_mobile" class="form-control" placeholder="" value="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 ClsForCompany">
                                            <div class="form-group row">
                                                <label class="col-4 col-form-label">
                                                    <?= trans('CUSTOMER') ?> :
                                                </label>
                                                <div class="col-8">
                                                    <select style="width:100%" class="form-control ap-select2 " name="customer_id" id="customer_id" >
                                                        <option value="0">--------SELECT A VALUE------</option>
                                                        <?php foreach($api->get_customers('array') as $c): ?>
                                                        <option value="<?= $c['debtor_no'] ?>"><?= $c['debtor_ref'] ?>&nbsp;-&nbsp;<?= $c['name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <!-- <div style="margin-top: 5px">
                                                        <button type="button" id="btn_pop_add_new_customer"
                                                                class="btn btn-sm btn-primary">Add New Customer
                                                        </button>
                                                    </div>-->
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group row">
                                                <label class="col-4 col-form-label">
                                                    <?= trans('TOKEN NUMBER') ?> :
                                                </label>
                                                <div class="col-8">
                                                    <input type="text" id="token" name="token" class="form-control" placeholder="" value="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group row">
                                                <label class="col-4 col-form-label" for="contact_person">
                                                    <?= trans('CONTACT PERSON') ?> :
                                                </label>
                                                <div class="col-8">
                                                    <input type="text" id="contact_person" name="contact_person" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 ClsForIndividual">
                                            <div class="form-group row">
                                                <label class="col-4 col-form-label">
                                                    <?= trans('E-MAIL') ?> :
                                                </label>
                                                <div class="col-8">
                                                    <input type="text" id="customer_email" name="customer_email" class="form-control" placeholder="" value="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 ClsForCompany">
                                            <div class="form-group row">
                                                <label class="col-4 col-form-label">
                                                    <?= trans('MOBILE') ?> :
                                                </label>
                                                <div class="col-8">
                                                    <input type="text" id="customer_mobile_company" name="customer_mobile_company" class="form-control" placeholder="" value="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group row">
                                                <label class="col-4 col-form-label">
                                                    <?= trans('SUB CUSTOMER') ?> :
                                                </label>
                                                <div class="col-8">
                                                    <select class="form-control kt-select2 ap-select2" name="sub_customer_id" id="sub_customer_id">
                                                        <option>Choose a Customer</option>
                                                    </select>
                                                    <span id="display_cust_err"></span>
                                                    <small>
                                                        <button type="button" id="btn_pop_add_new_company" class="btn btn-sm btn-link py-0">
                                                            Add New Company
                                                        </button>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6" >
                                            <div class="form-group row">
                                                <label class="col-4 col-form-label">
                                                    <?= trans('IBAN') ?> :
                                                </label>
                                                <div class="col-8">
                                                    <input
                                                            type="text"
                                                            id="customer_iban"
                                                            name="customer_iban"
                                                            class="form-control"
                                                            placeholder=""
                                                            value=""
                                                            maxlength="23"
                                                            >
                                                    <small
                                                            id="iban-help"
                                                            class="form-text text-muted">
                                                        characters: <span id="iban-count">0</span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 ClsForIndividual" >
                                            <div class="form-group row">
                                                <label class="col-4 col-form-label">
                                                    <?= trans('Customer Mobile') ?> :
                                                </label>
                                                <div class="col-8">
                                                    <input type="text" id="customer_eid_number" name="customer_eid_number" class="form-control" placeholder="" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">

                                        </div>
                                        <div class="col-lg-6 ClsForCompany">
                                            <div class="form-group row">
                                                <label class="col-4 col-form-label">
                                                    <?= trans('EMAIL') ?> :
                                                </label>
                                                <div class="col-8">
                                                    <input type="text" id="customer_company_email" name="customer_company_email" class="form-control" placeholder="" value="">
                                                </div>
                                            </div>
                                        </div>




                                    </div>
                                </div>
                            </div>

                            <div class="kt-portlet__foot">
                                <div class="kt-form__actions text-center">
                                    <button type="submit" class="btn btn-primary">
                                        <?= trans('Submit') ?>
                                    </button>
                                    <button type="button" class="btn btn-secondary">
                                        <?= trans('Cancel') ?>
                                    </button>
                                    <button type="button" class="btn btn-success clsBtn" style="font-size: 10pt;">READ DATA FROM EID
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>


                </div>

            </div>

            <!-- end:: Content -->
        </div>
    </div>
</div>


<div class="modal fade" id="AddNewCustomerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= trans('Add New Customer') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">

                <form id="addCustomerForm" method="post">
                    <div class="form-group">
                        <label for="cust_name">Name</label>
                        <input type="text" class="form-control" name="cust_name" id="cust_name" placeholder="Enter customer name">
                    </div>
                    <div class="form-group">
                        <label for="cust_mobile">Mobile Number</label>
                        <input type="text" class="form-control" name="cust_mobile" id="cust_mobile" placeholder="Enter customer mobile">
                    </div>
                    <div class="form-group">
                        <label for="cust_email">Email</label>
                        <input type="text" class="form-control" name="cust_email" id="cust_email" placeholder="Enter customer email">
                    </div>

                </form>

            </div>
            <div class="modal-footer" id="modal_footer">

                <button type="button" class="btn btn-success" id="btn-add-customer" onclick="AddNewCustomer()">Add
                </button>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="AddNewSubCustomerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= trans('Add New Customer Company') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">

                <form id="addSubCustomerForm" method="post">

                    <input type="hidden" id="cust_id" name="cust_id">
                    <input type="hidden" id="cust_hdn_mobile" name="cust_hdn_mobile">
                    <input type="hidden" id="radio_check_val" name="radio_check_val">

                    <div class="form-group">
                        <label for="comp_code">Company Code</label>
                        <input type="text" class="form-control" name="comp_code" id="comp_code" placeholder="Enter company code">
                    </div>
                    <div class="form-group">
                        <label for="comp_name">Company Name</label>
                        <input type="text" class="form-control" name="comp_name" id="comp_name" placeholder="Enter company name">
                    </div>


                </form>

            </div>
            <div class="modal-footer" id="modal_footer">

                <button type="button" class="btn btn-success" onclick="AddNewSubCustomer()">Add</button>

            </div>
        </div>
    </div>
</div>


<?php include "footer.php"; ?>

<script type="text/javascript" language="javascript" src="EID_READER/errors.js"></script>
<script type="text/javascript" language="javascript" src="EID_READER/occupations.js"></script>
<script type="text/javascript" language="javascript" src="EID_READER/eida_webcomponents.js"></script>
<script type="text/javascript" language="javascript" src="EID_READER/fingers.js"></script>


<style>
    div.dataTables_wrapper div.dataTables_filter {

        text-align: left !important;

    }

    .dt-buttons {
        float: right !important;
    }
</style>


<script>

    $(document).ready(function() {

        setTimeout(function() {
            Initialize();
        },3000);

    });


    $('.clsBtn').click(function()
    {
        DisplayPublicData();
    });




    $(document).ready(function() {

        //loadSubCustomer(1);
        $('#ForOnlyCompany').css('display', 'none');
        //$('#display_customer').val('Walk-in Customer');
        loadSubCustomer("1");
    });


    $('input[type=radio][name=GroupCompany]').on('change', function() {
        $('#customer_mobile').val('');
        $('#display_customer').val('');
        $('#customer_id').val("0");
        $('#customer_id').trigger("change");
        $('#token').val('');
        $('#customer_email').val('');
        $('#customer_mobile_company').val('');
        $('#sub_customer_id').html('<option value="">Choose a Customer</option>');
        $('#sub_customer_id').trigger("change");
        $('#contact_person').val('');
        $('#customer_iban').val('');
        $('#customer_iban').trigger('keyup');


        if ($(this).val() == '1') {
            $('.ClsForIndividual').css('display', 'block');
            $('.ClsForCompany').css('display', 'none');
            $('.clsBtn').css('display','inline-block');
        } else if ($(this).val() == '2') {
            $('.ClsForIndividual').css('display', 'none');
            $('.ClsForCompany').css('display', 'block');
            $('.clsBtn').css('display','none');
        }
    });


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


    $("#btn_pop_add_new_customer").click(function() {

        $("#AddNewCustomerModal").modal("show");

    });

    $("#btn_pop_add_new_company").click(function() {

        var mobile_no = $("#customer_mobile").val();
        if ($("input[name='GroupCompany']:checked").val() == 1 && !(/^[0-9]{10}$/).test(mobile_no)) {
            return Swal.fire({
                title: 'Please enter a valid mobile number first',
                type: 'warning',
                confirmButtonText: 'OK'
            })
        }

        var selected_customer = $("#customer_id").val();
        $("#cust_id").val(selected_customer);
        $("#cust_hdn_mobile").val();
        $('#radio_check_val').val($("input[name='GroupCompany']:checked").val());

        $("#AddNewSubCustomerModal").modal("show");

    });

    $("#customer_id").change(function() {
    /*var callBack = function (resp) {
            var data = resp.data
            if (data) {
                $("#customer_mobile_company").val(data.mobile);
                $("#contact_person").val(data.contact_person);
                $('#customer_iban').val(data.iban_no);
            } else {
                $("#customer_mobile_company").val('');
                $("#contact_person").val('');
                $('#customer_iban').val('');
            }
            $('#customer_iban').trigger('keyup');
            loadSubCustomer(this.value);
        }
        AxisPro.APICall(
            'GET',
            ERP_FUNCTION_API_END_POINT,
            {
                method: 'get_customer',
                id: this.value,
                format: 'json'
            },
            callBack
        );*/

        $.ajax({
            type: "GET",
            url: ERP_FUNCTION_API_END_POINT+"?method=get_customer",
            data: 'id='+this.value,
            success: function (result) {
                var data = JSON.parse(result);
                if(data)
                {
                    $("#customer_mobile_company").val(data['data']['mobile']);
                    $("#contact_person").val(data['data']['contact_person']);
                    $('#customer_iban').val(data['data']['iban_no']);
                    $('#customer_company_email').val(data['data']['debtor_email']);
                }
                else {
                    $("#customer_mobile_company").val('');
                    $("#contact_person").val('');
                    $('#customer_iban').val('');
                    $('#customer_company_email').val();
                }
                $('#customer_iban').trigger('keyup');
                loadSubCustomer($("#customer_id").val());
            }
        });

    });

    $("#sub_customer_id").change(function() {
        var selected_company_name;
        var selected_id = $(this).val();
        var $dispCustomer = $("#display_customer");

        // we will handle this later when submitting
        if (selected_id == "-1") {
            return $dispCustomer.val('');
        }

        if (selected_id.length > 0) {
            selected_company_name = $("#sub_customer_id option:selected").text();
            return $dispCustomer.val(selected_company_name);
        }

        $dispCustomer.val("");
    });


    $("#customer_mobile").change(function() {

       /* var callBack = function (resp) {
            var data = resp.data;
            if (data) {
                $("#customer_email").val(data.customer_email);
                $("#contact_person").val(data.contact_person);
                $('#customer_iban').val(data.customer_iban);
            } else {
                $("#customer_email").val('');
                $("#contact_person").val('');
                $('#customer_iban').val('')
            }
            $('#customer_iban').trigger('keyup');
            loadSubCustomer("1");
        }
        if (this.value.length === 10) {
            AxisPro.APICall(
                'GET',
                ERP_FUNCTION_API_END_POINT,
                {
                    method: 'getCustomerByMobile',
                    mobile: this.value,
                    format: 'json'
                },
                callBack
            );
        }*/

        if (this.value.length === 10) {

            $("#cust_hdn_mobile").val($('#customer_mobile').val());

            $.ajax({
                type: "GET",
                url: ERP_FUNCTION_API_END_POINT+"?method=getCustomerByMobile",
                data: 'mobile='+this.value,
                success: function (result) {
                    var data = JSON.parse(result);
                    if (data) {
                        $("#customer_email").val(data['data']['customer_email']);
                        $("#contact_person").val(data['data']['contact_person']);
                        $('#customer_iban').val(data['data']['customer_iban']);
                        $('#customer_eid_number').val(data['data']['customer_mobile']);
                    } else {
                        $("#customer_email").val('');
                        $("#contact_person").val('');
                        $('#customer_iban').val('');
                        $('#customer_eid_number').val();
                    }
                    $('#customer_iban').trigger('keyup');
                    loadSubCustomer("1");
                }
            });
        }

    });

    $('#customer_iban').on('keyup', function(){
        $('#iban-count').text(this.value.length);
    });

    $("#item_form").on("submit", function(ev) {
        ev.preventDefault();

        if ($("#sub_customer_id").val() == "-1") {
            var indivOrComp = $("input[name='GroupCompany']:checked").val();

            if (indivOrComp == "1") {
                $("#display_customer").val($('#contact_person').val());
            }

            if (indivOrComp == "2") {
                $("#display_customer").val($("#customer_id option:selected").text());
            }
        }
        var form = $(this);
       // var params = AxisPro.getFormData($form);
       // var form = $("#employee_form");
        var params = form.serializeArray();


        $(".error_note").hide();
        /*AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=save_reception", params, function(resp) {

            if (resp.status === 'FAIL' && resp.msg === 'VALIDATION_FAILED') {
                toastr.error('ERROR !. PLEASE CHECK THE FORM DATA.');
                var errors = resp.data;
                $.each(errors, function(key, value) {
                    $("#" + key)
                        .after('<span class="error_note form-text text-muted">' + value + '</span>')
                })
            } else {
                swal.fire(
                    'Success!',
                    'Reception Info Saved',
                    'success'
                ).then(function() {
                    window.location.reload();
                });
            }
        });*/

        $.ajax({
            type: "POST",
            url: ERP_FUNCTION_API_END_POINT+"?method=save_reception",
            data: params,
            //contentType: false,
            //processData: false
            success: function (result) {
                var data = JSON.parse(result);

                if (data.status === 'FAIL' && data.msg === 'VALIDATION_FAILED') {

                    var errors = data.data;
                    $.each(errors, function(key, value) {
                        $("#" + key)
                            .after('<span class="error_note form-text text-muted">' + value + '</span>')
                    });
                }
                else
                {
                    swal.fire(
                        'Success!',
                        'Reception Info Saved',
                        'success'
                    ).then(function() {
                        window.location.reload();
                    });
                }

            }
        });

    });


    function loadSubCustomer(cust_id) {
        //$('#display_customer').val('');
        //ar param='customer_id='+cust_id+'&radio_type='+$("input[name='GroupCompany']:checked").val()+'&mobile='+$('#customer_mobile').val();

        var indivOrComp = $("input[name='GroupCompany']:checked").val();
      /* AxisPro.APICall(
            'GET',
            ERP_FUNCTION_API_END_POINT, {
                method: 'get_sub_customers',
                customer_id: cust_id,
                radio_type: indivOrComp,
                mobile: $('#customer_mobile').val(),
                format: 'json'
            },
            function(resp) {
                var $selectEl = $('#sub_customer_id');
                var opts;
                // if individual
                if (indivOrComp == "1") {
                    opts = (
                        '<option value="" selected>------Select-----</option>'
                        +   '<option value="-1">Personal</option>'
                    );
                }
                // if company
                if (indivOrComp == "2") {
                    opts = (
                        '<option value="">------Select-----</option>'
                        +   '<option value="-1" selected>' + $("#customer_id option:selected").text() + '</option>'
                    );
                }
                $.each(resp.data, function(key, row) {
                    opts += '<option value="' + row['id'] + '">' + row['name'] + '</option>';
                });
                $selectEl.html(opts);
                $selectEl[0].dataset.custId = cust_id;
                $selectEl.trigger("change");
            }
        ); */


        $.ajax({
            type: "GET",
            url: ERP_FUNCTION_API_END_POINT+"?method=get_sub_customers",
            data: 'customer_id='+cust_id+'&radio_type='+indivOrComp+'&mobile='+$('#customer_mobile').val(),
            success: function (result) {

                var $selectEl = $('#sub_customer_id');
                var opts;
                // if individual
                if (indivOrComp == "1") {
                    opts = (
                        '<option value="" >------Select-----</option>'
                        +   '<option value="-1" selected>Personal</option>'
                    );
                }
                // if company
                if (indivOrComp == "2") {
                    opts = (
                        '<option value="">------Select-----</option>'
                        +   '<option value="-1" selected>' + $("#customer_id option:selected").text() + '</option>'
                    );
                }

                var resp = JSON.parse(result);


                $.each(resp.data, function(key, row) {
                    opts += '<option value="' + row['id'] + '">' + row['name'] + '</option>';
                });
                $selectEl.html(opts);
                $selectEl[0].dataset.custId = cust_id;
                $selectEl.trigger("change");

            }
        });






    }


    function AddNewCustomer() {

        //var $form = $("#addCustomerForm");
       // var params = AxisPro.getFormData($form);


        $(".error_note").hide();

        /*AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=addCustomerBasicInfo", params, function(data) {

            if (data.status === 'FAIL' && data.msg === 'VALIDATION_FAILED') {


                toastr.error('ERROR !. PLEASE CHECK THE FORM DATA.');
                var errors = data.data;


                $.each(errors, function(key, value) {

                    $("#" + key)
                        .after('<span class="error_note form-text text-muted">' + value + '</span>')

                })


            } else {

                swal.fire(
                    'Success!',
                    'Customer saved',
                    'success'
                ).then(function() {
                    window.location.reload();
                });

            }

        });*/

    }

    function AddNewSubCustomer() {

       // var $form = $("#addSubCustomerForm");
       // var params = AxisPro.getFormData($form);

        var form = $("#addSubCustomerForm");
        var params = form.serializeArray();



        $(".error_note").hide();

       /* AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=addSubCustomer", params, function(data) {

            if (data.status === 'FAIL' && data.msg === 'VALIDATION_FAILED') {


                toastr.error('ERROR !. PLEASE CHECK THE FORM DATA.');
                var errors = data.data;


                $.each(errors, function(key, value) {

                    $("#" + key)
                        .after('<span class="error_note form-text text-muted">' + value + '</span>')

                })


            } else {

                swal.fire(
                    'Success!',
                    'Customer Company saved',
                    'success'
                ).then(function() {
                    loadSubCustomer(document.getElementById('sub_customer_id').dataset.custId)
                    $("#AddNewSubCustomerModal").modal("hide");
                });

            }

        });*/



        $.ajax({
            type: "POST",
            url: ERP_FUNCTION_API_END_POINT+"?method=addSubCustomer",
            data: params,
            success: function (result) {
                var data = JSON.parse(result);

                if (data.status === 'FAIL' && data.msg === 'VALIDATION_FAILED') {


                    toastr.error('ERROR !. PLEASE CHECK THE FORM DATA.');
                    var errors = data.data;


                    $.each(errors, function(key, value) {

                        $("#" + key)
                            .after('<span class="error_note form-text text-muted">' + value + '</span>')

                    })


                } else {

                    swal.fire(
                        'Success!',
                        'Customer Company saved',
                        'success'
                    ).then(function() {
                        loadSubCustomer(document.getElementById('sub_customer_id').dataset.custId)
                        $("#AddNewSubCustomerModal").modal("hide");
                    });

                }

            }
        });


    }


</script>