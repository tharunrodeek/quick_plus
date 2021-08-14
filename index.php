<?php include "header.php" ?>


<?php

error_reporting(E_ALL);

$application = isset($_GET['application']) ? $_GET['application'] : "dashboard";

//var_dump($application); die;


switch ($application) {

    case "dashboard" :

        $type = isset($_GET['dashboard']) ? $_GET['dashboard'] : '';

//        if($_SESSION["wa_current_user"]->access == 3 || $type == 'counter_staff'){
//            //Counter staff
//            include_once "counter_staff_dashboard.php";
//            break;
//        }
        if($_SESSION["wa_current_user"]->access == 13 || $type == 'cashier'){
            //Cashier
            include_once "cashier_window.php";
            break;
        }
        elseif($_SESSION["wa_current_user"]->access == 13 || $type == 'cashier_new'){
            //Cashier
            include_once "cashier_window_new.php";
            break;
        }
        else {
        include_once "dashboard.php";
        }

        break;

    case "sales" :
        include_once "sales.php";
        break;
    case "reports" :
        include_once "reports.php";
        break;
    case "services" :
        include_once "services.php";
        break;

    case "finance" :
        include_once "finance.php";
        break;
    case "purchase" :
        include_once "purchase.php";
        break;

    case "settings" :
        include_once "settings.php";
        break;

    case "hrm" :
        include_once "hrm.php";
        break;
		
	case "hr_admin" :
        include_once "hr_admin.php";
        break;	
		
    // case "fixed_assets" :
    //     include_once "fixed_assets.php";
    //     break;

    default:
        include_once "dashboard.php";
        break;
}


?>

<?php ?>
<?php include "footer.php"; ?>


<script>


    $(document).ready(function (e) {

        getTopTenCustomerTransaction();

        // AxisPro.get_current_QMS_token();

        // get_current_QMS_token();

        var dim_id = $("#dim_id").val();

        var params = {method: 'todays_invoices', format: 'json', dim_id : dim_id};

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
                tbody_html += "<td>" + value.transaction_date + "</td>";
                tbody_html += "<td>" + value.customer_name + "</td>";
                tbody_html += "<td>" + value.display_customer + "</td>";
                tbody_html += "<td>" + amount(value.invoice_amount) + "</td>";


                var token = value.qms_token;

                tbody_html += "<td>" +
                    "<div class='btn-group' role='group'>" +
                    "  <button type='button' " +
                    "class='btn  btn-warning btn-sm qms-recall' data-token='" + token + "' " + recall_style + " >" +
                    "<i class='flaticon2-fast-back'></i>Recall</button>" +

                    "  <button type='button' data-ref='"+value.invoice_no+"' data-type='edit' class='btn btn-brand edit_or_print'>" +
                    "<i class='flaticon-edit'></i>Edit</button>" +


                    "</div>" +
                    "" +
                    "</td>";

                tbody_html += "</td>";


            });


            $("#sales-pending-tbody").html(tbody_html);


        });

    });


    $(".qms-stop-token").click(function() {

        var this_btn = $(this);
        var curr_token_id = $("#curr_token_id").val();
        var params = {method: 'end_token', token_id: curr_token_id};

        AxisPro.APICall('POST', QMS_API_ROOT + "deftoken/TOKEN_API.php", params, function (data) {

            this_btn.attr('disabled', 'disabled');
            this_btn.css('cursor', 'default');

            get_current_QMS_token();

            setTimeout(function () {

                this_btn.removeAttr('disabled');
                this_btn.css('cursor', 'pointer');

            }, 10000)


        });

    });


    $(".qms-call-next").click(function (e) {

        var this_btn = $(this);
        var qms_user = $("#qms_user").val();
        var params = {method: 'call_next', user_id: qms_user};

        AxisPro.APICall('POST', QMS_API_ROOT + "deftoken/TOKEN_API.php", params, function (data) {

            this_btn.attr('disabled', 'disabled');
            this_btn.css('cursor', 'default');

            get_current_QMS_token(function (data) {

                if(data.msg === "NOT_FOUND" || data.msg === "NO_TOKENS") {
                    swal.fire(
                        'Warning!',
                        'No Tokens found!',
                        'warning'
                    );

                }
                else {
                    toastr.success("Token Called");
                    window.location.href = ERP_ROOT_URL+"sales/sales_order_entry.php?NewInvoice=0"
                }

            });


            setTimeout(function () {

                this_btn.removeAttr('disabled');
                this_btn.css('cursor', 'pointer');

            }, 10000)


        });


    });


    $(document).on("click", ".qms-recall", function () {

        var this_btn = $(this);
        var qms_user = $("#qms_user").val();
        var token = this_btn.data("token");
        var params = {method: 're_call', token_id: token, user_id: qms_user};

        AxisPro.APICall('POST', QMS_API_ROOT + "deftoken/TOKEN_API.php", params, function (data) {

            if (data.msg === "SUCCESS") {
                this_btn.attr("disabled","disabled");
                toastr.success("Token Recalled");
                this_btn.css('cursor', 'default');
            }

            get_current_QMS_token();
            setTimeout(function () {

                this_btn.removeAttr('disabled');
                this_btn.css('cursor', 'default');

            },5000)


        });


    });




    $(document).on("click", ".edit_or_print", function () {

        var this_btn = $(this);
        var invoice_number = this_btn.data('ref');
        var type = this_btn.data('type');

        $.ajax({
            url: ERP_ROOT_URL+"sales/read_sales_invoice.php",
            type: "post",
            dataType: 'JSON',
            data: {
                invoice_ref: invoice_number
            },
            success: function(response) {
                KTApp.unblockPage();

                if(response != 'false' && response.trans_no) {

                    toastr.success("Invoice found");

                    var edit_url = ERP_ROOT_URL+"sales/customer_invoice.php?ModifyInvoice="+response.trans_no;

                    if(response.payment_flag != "0" && response.payment_flag != "3") {
                        edit_url += "&is_tadbeer=1&show_items=ts";
                    }

                    if(response.payment_flag == "4" || response.payment_flag == "5") {
                        edit_url += "&is_tadbeer=1&show_items=tb";
                    }

                    if(type == 'edit') {
                        window.location.href = edit_url;
                    }
                    else{
                        var print_params = "PARAM_0="+response.trans_no+"-10&PARAM_1="+
                            response.trans_no+"-10&PARAM_2=&PARAM_3=0&PARAM_4=&PARAM_5=&PARAM_6=&PARAM_7=0&REP_ID=107";

                        var print_link = ERP_ROOT_URL+"invoice_print?"+print_params;

                        window.open(
                            print_link,
                            '_blank'
                        );
                    }


                }
                else {
                    // alert("No invoice found");
                    toastr.error("No invoice found!");
                }
            },
            error: function(xhr) {
            }
        });


    });





    function getTopTenCustomerTransaction() {


        var cat_id = $("#topf_cat_id").val();
        var from_date = $("#topf_from_date").val();
        var to_date = $("#topf_to_date").val();


        var params = {
            method: 'getTopTenCustomerTransaction',
            format: 'json',
            cat_id:cat_id,
            from_date:from_date,
            to_date:to_date,
        };

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, params, function (data) {

            var tbody_html = "";


            $.each(data, function (key, value) {

                tbody_html += "<tr>";
                tbody_html += "<td>"+value.customer_name+"</td>";
                tbody_html += "<td>"+value.qty+"</td>";
                tbody_html += "</tr>";

            });

            if(tbody_html.length === 0)
                tbody_html+="<tr><td colspan='2'>No Data Found</td></tr>";

            $("#tbl_dboard_top_ten_customers_tbody").html(tbody_html);
        });


    }


    $(".tptc_filter").change(function () {

        getTopTenCustomerTransaction();

    });


    $("#btn_load_manager_report").click(function() {

       var date = $("#inp_manager_report_date").val();

       window.location.href = BASE_URL+"?application=dashboard&filter_date="+date

    });



</script>
