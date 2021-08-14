//For Batch Auto Model
//FOR MODAL
var batch_modal = document.getElementById('batchModel');

// Get the button that opens the modal

// Get the <span> element that closes the modal
var batch_close_span = document.getElementsByClassName("batchModelClose")[0];
$(document).on("click", "#auto_fetch_batch_open", function () {

    batch_modal.style.display = "block";


    navigator.clipboard.readText().then(clipText =>
        loadAutoBatchData(clipText)
    );


});


$(document).on("click", ".af_select_all", function () {

    $('input:checkbox').not(this).prop('checked', this.checked);


});

var application_ids_in_cart = [];


function loadAutoBatchData(clipText) {


    $.ajax("../API/hub.php", {
        method: 'get',
        data: {
            tasheel_only: 1,
            method: 'getInvoicedApplicationIDs',
        },
    }).done(function (r) {

        var invoiced_application_id_array = JSON.parse(r);

        var each_service = clipText.split("#");

        var tbody_html = "";

        $.each(each_service, function (key, value) {

            var this_service = value.split("|");
            if (this_service[0] == "TASHEEL") {


                var service_name_en = this_service[1];
                var service_name_ar = this_service[2];
                var total_fee = this_service[6];
                var service_charge = this_service[7];
                var application_id = this_service[4];
                var transaction_id = this_service[3];
                // var bank_ref_number = this_service[5];

                application_id = application_id.replace(/<br>\s*$/, "");
                application_id = application_id.replace(/URN\s*$/, "");
                application_id = application_id.replace(/<br>\s*$/, "");

                application_id = $.trim(application_id);


                if (jQuery.inArray(application_id, invoiced_application_id_array) !== -1)
                    return true;


                // if (total_fee >= 243 && total_fee < 250) {
                //     service_charge = 240;
                // }
                // else if (total_fee > 80) {
                //     service_charge = 80;
                // }
                // else if (total_fee > 42) {
                //     service_charge = 40;
                // }
                // else if (total_fee >= 22) {
                //     service_charge = 19;
                // }


                tbody_html += "<tr>";
                tbody_html += "<td><input type='checkbox' class='auto_batch_checked'/></td>";
                tbody_html += "<td class='af_srv_name'>" + service_name_en + ' ' + service_name_ar + "</td>";
                tbody_html += "<td class='af_tot'>" + total_fee + "</td>";
                tbody_html += "<td class='af_srv_amt'>" + service_charge + "</td>";
                tbody_html += "<td class='af_tr_id'>" + transaction_id + "</td>";
                tbody_html += "<td class='af_app_id'>" + application_id + "</td>";

                tbody_html += "</tr>";

            }

        });

        $("#batch_auto_tbody").html(tbody_html);


    });

}

// When the user clicks on <span> (x), close the modal
batch_close_span.onclick = function () {
    batch_modal.style.display = "none";
};

window.onclick = function (event) {

    if (event.target == batch_modal) {
        batch_modal.style.display = "none";
    }
};


$(document).on("click", "#batch_auto_add", function () {


    var batch_add_items = [];

    $('#batchModel table').find('tr').each(function () {
        var row = $(this);
        if (row.find('input[type="checkbox"]').is(':checked')) {


            var srv_name = row.find(".af_srv_name").html();
            var tot = row.find(".af_tot").html();
            var srv_amt = row.find(".af_srv_amt").html();
            var tr_id = row.find(".af_tr_id").html();
            var app_id = row.find(".af_app_id").html();
            var bank_ref = row.find(".af_bank_ref").html();


            var auto_stock_id = 'TAS_AUTO';

            if(!srv_amt)
                return true;

            if (srv_amt !== "80")
                auto_stock_id = auto_stock_id + "" + srv_amt;

            var items = {
                stock_id : auto_stock_id,
                description : srv_name,
                tot : tot,
                srv_amt : srv_amt,
                transaction_id : tr_id,
                application_id : app_id,
            };



            batch_add_items.push(items);



        }
    });



    $.ajax("../API/hub.php", {
        method: 'post',
        data: {
            items : batch_add_items,
            method: 'addAutoBatchItems',
        },
    }).done(function (r) {

        var current_token = $('input[name="token_no"]').val();

        $('#customer_id').trigger("change");

        setTimeout(function () {

            $('input[name="token_no"]').val(current_token);
            $('input[name="token_no"]').trigger("change");

        },3000);

    });


    batch_modal.style.display = "none";


});


$(document).on("click", "#auto_fetch_button", function () {

    navigator.clipboard.readText().then(clipText =>
        getAutoFetchContent(clipText)
    );

});

function getAutoFetchContent(clipText) {


    var each_service = clipText.split("#");

    var latest_service = "";
    $.each(each_service.reverse(), function (key, value) {

        var this_service = value.split("|");
        if (this_service[0] == "TASHEEL") {
            latest_service = this_service;
        }

    });

    var service_info = latest_service;

    var service_name_en = service_info[1];
    var service_name_ar = service_info[2];
    var total_fee = service_info[6];
    var srv_amt = service_info[7];
    var application_id = service_info[4];
    var transaction_id = service_info[3];
    var bank_ref_number = service_info[5];


    application_id = application_id.replace(/<br>\s*$/, "");
    application_id = application_id.replace(/URN\s*$/, "");
    application_id = application_id.replace(/<br>\s*$/, "");

    var service_full_name = service_name_en + " - " + service_name_ar;

    var service_charge = srv_amt;

    var auto_stock_id = 'TAS_AUTO';

    // var auto_stock_id = 'TAS_AUTO';

    if (srv_amt != "80")
        auto_stock_id = auto_stock_id + "" + srv_amt



    // if (total_fee >= 243 && total_fee < 250) {
    //     srv_amt = total_fee - 240;
    //     auto_stock_id = 'TAS_AUTO240';
    // }
    // else if (total_fee > 80) {
    //     srv_amt = total_fee - 80;
    //     auto_stock_id = 'TAS_AUTO';
    // }
    // else if (total_fee > 42) {
    //     srv_amt = total_fee - 40;
    //     auto_stock_id = 'TAS_AUTO40';
    // }
    // else if (total_fee >= 22) {
    //     srv_amt = total_fee - 19;
    //     auto_stock_id = 'TAS_AUTO';
    // }

    // srv_amt = total_fee - srv_amt;


    $("#stock_id").val(auto_stock_id).trigger('change');

    // setTimeout(function () {
    //     $("#stock_id_text").val(service_full_name);
    //     $("input[name='govt_fee']").val(total_fee);
    //     $("input[name='transaction_id']").val(transaction_id);
    // }, 1000);


    setTimeout(function () {
        $("#stock_id_text").val(service_full_name);
        $("input[name='govt_fee']").val(total_fee);
        $("input[name='price']").val(service_charge);
        $("input[name='ed_transaction_id']").val(transaction_id);
        $("input[name='application_id']").val(application_id);


        var fee_info = $("input[name='govt_fee']").parents("tr").find("input[name='other_fee_info_json']").val();

        var fee_info_json = $.parseJSON(atob(fee_info));

        var loop = 0;
        $.each(fee_info_json, function (key, value) {
            fee_info_json[loop].amount = total_fee;
            loop++;
        });

        var stringyfy_json_encoded = btoa(JSON.stringify(fee_info_json));
        $("input[name='govt_fee']").parents("tr").find("input[name='other_fee_info_json']").val(stringyfy_json_encoded)

    }, 1000);


}