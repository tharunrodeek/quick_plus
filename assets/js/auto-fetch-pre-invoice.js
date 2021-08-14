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


//Handling Other Immigration Category Items
var other_items = [
    "DEPOSIT",
    "VIOLATION",
    "SPONSOR REGISTRATION"
];

var other_item_full_string = other_items.join("|").toLowerCase();
//END -- Handling Other Immigration Category Items

var itemsJSON;

$.getJSON("../js/immServices.json", function (data) {

    itemsJSON = data.items;

});


function loadAutoBatchData(clipText) {

    var each_service = clipText.split("#");

    var tbody_html = "";

    var transaction_ids = [];
    var application_ids = [];

    $.each(each_service, function (key, value) {

        var this_service = value.split("|");
        if (this_service[0] == "IMMIGRATION") {


            var service_name_en = this_service[1];
            var service_name_ar = this_service[2];

            var srv_first_part = service_name_en;
            var srv_second_part = service_name_ar;

            var lang = 'en';
            if (service_name_en.length <= 0) {
                lang = 'ar';
            }

            var check_var = service_name_en;

            if (lang == 'ar')
                check_var = service_name_ar;

            // var service_name_ar = this_service[2];
            var item = itemsJSON.find(el => (el[lang]).toLowerCase() === check_var.toLowerCase().trim());

            // var service_name_ar = item["ar"];

            if (lang == 'en')
                service_name_ar = item["ar"];
            else
                service_name_en = item["en"];

            // console.log(country["name"]);


            var total_fee = this_service[6];
            var application_id = this_service[4];
            var transaction_id = this_service[3];
            var bank_ref_number = this_service[5];
            var ref_name = this_service[9];


            // if($.inArray(transaction_id, transaction_ids) !== -1)
            //     return true;

            if ($.inArray(application_id, application_ids) !== -1)
                return true;


            tbody_html += "<tr>";
            tbody_html += "<td><input type='checkbox' class='auto_batch_checked'/></td>";
            tbody_html += "<td class='af_srv_name'>" + service_name_en + ' ' + service_name_ar + "</td>";
            tbody_html += "<td class='af_tot'>" + total_fee + "</td>";

            tbody_html += "<td class='af_tr_id'>" + transaction_id + "</td>";
            tbody_html += "<td class='af_app_id'>" + application_id + "</td>";
            // tbody_html += "<td class='af_bank_ref'>" + bank_ref_number + "</td>";
            tbody_html += "<td class='af_ref_name'>" + ref_name + "</td>";
            tbody_html += "</tr>";

            // transaction_ids.push(transaction_id);
            application_ids.push(application_id);

        }

    });

    $("#batch_auto_tbody").html(tbody_html);

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


    var batch_add_items = "";

    $('#batchModel table').find('tr').each(function () {
        var row = $(this);
        if (row.find('input[type="checkbox"]').is(':checked')) {

            var srv_name = row.find(".af_srv_name").html();
            var tot = row.find(".af_tot").html();
            var tr_id = row.find(".af_tr_id").html();
            var app_id = row.find(".af_app_id").html();
            var bank_ref = row.find(".af_bank_ref").html();
            var ref_name = row.find(".af_ref_name").html();

            batch_add_items += "|" + srv_name + "|" + tot + "|" + tr_id + "|" + app_id + "|" + bank_ref + "#"


            // Handling Other Immigration Category Items
            if (new RegExp(other_item_full_string).test(srv_name.toLowerCase())) {

                alert("OK");


                if (new RegExp("violation").test(srv_name.toLowerCase())) {
                    $("#stock_id").val("IM0002").trigger('change');
                }
                else {
                    $("#stock_id").val("IM0003").trigger('change');
                }


                console.log(srv_name);


                // setTimeout(function () {
                $("input[name='govt_fee']").val(tot);
                $("input[name='price']").val(30);
                $("input[name='transaction_id']").val(tr_id);
                $("input[name='application_id']").val(app_id);
                $("input[name='ref_name']").val(ref_name);
                $("input[name='total_bank_charge']").val(3.15);
                $("input[name='bank_service_charge_vat']").val(0.15);
                $("input[name='bank_service_charge']").val(3.00);
                $("#stock_id_text").val(srv_name);


                $("#AddItem").trigger('click');
                // alert('OK');
                // },2000)
                alert("OK.");
            }
            else {
                console.log(srv_name);
                $("#stock_id").val("IM0001").trigger('change');
                // alert("OK");


                setTimeout(function () {
                    $("input[name='govt_fee']").val(tot);
                    //$("input[name='price']").val(83);
                    $("input[name='transaction_id']").val(tr_id);
                    $("input[name='application_id']").val(app_id);
                    $("input[name='ref_name']").val(ref_name);
                    $("#stock_id_text").val(srv_name);
                    $("#AddItem").trigger('click');
                    // alert('OK');
                }, 1500)


            } // END -- Handling Other Immigration Category Items


            console.log(srv_name);
            //$("#stock_id").val("IM0001").trigger('change');


        }
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
    var total_len = latest_service.length;
    $.each(each_service.reverse(), function (key, value) {

        var this_service = value.split("|");
        if (this_service[0] == "IMMIGRATION") {
            latest_service = this_service;
        }

    });

    var service_info = latest_service;

    var service_name_en = service_info[1];
    var service_name_ar = service_info[2];


    var srv_first_part = service_name_en;
    var srv_second_part = service_name_ar;

    var lang = 'en';
    if (service_name_en.length <= 0) {
        lang = 'ar';
    }


    $.each(itemsJSON, function (i, v) {

        var check_var = service_name_en;

        if (lang == 'ar')
            check_var = service_name_ar;

        if ((v[lang]).toLowerCase() === check_var.toLowerCase().trim()) {

            var item = v;

            if (lang == 'en')
                service_name_ar = item["ar"];
            else
                service_name_en = item["en"]

            var total_fee = service_info[6];
            var application_id = service_info[4];
            var transaction_id = service_info[3];
            var bank_ref_number = service_info[5];
            var ref_name = service_info[9];

            var service_full_name = service_name_en + " - " + service_name_ar;


            // Handling Other Immigration Category Items
            if (new RegExp(other_item_full_string).test(service_name_en.toLowerCase())) {
                $("#stock_id").val("IM0002").trigger('change');
            }
            else {
                $("#stock_id").val("IM0001").trigger('change');
            } // END -- Handling Other Immigration Category Items


            setTimeout(function () {


                $("#stock_id_text").val(service_full_name);
                $("input[name='govt_fee']").val(total_fee);
                $("input[name='transaction_id']").val(transaction_id);
                $("input[name='application_id']").val(application_id);
                $("input[name='ref_name']").val(ref_name);


            }, 1500);


            return false; // stops the loop
        }
    });


}