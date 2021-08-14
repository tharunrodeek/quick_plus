<?php include "header.php" ?>


<?php

$application = isset($_GET['rep']) ? $_GET['rep'] : "service_report";


switch ($application) {

    case "service_report" :
        include_once "management_reports/service_report.php";
        break;

    default:
        include_once "management_reports/service_report.php";
        break;
}


?>

<?php ?>
<?php include "footer.php"; ?>


<style>

    .sum_text {
        font-size: 12px;
        font-weight: normal;
        /* text-align: center; */
        display: block;
    }

    .pg-link, .pagination b, .pagination span {
        padding: 5px;
    }

    #pg-link {
        margin-top: 8px;
    }

    .pagination span {
        padding-top: 10px;
    }

    .pagination a,.pagination b {
        /*border: 1px solid #ddd; !* Gray *!*/

        color: black;
        float: left;
        padding: 8px 16px;
        text-decoration: none;
        transition: background-color .3s;
        border: 1px solid #ddd;

    }

    .pagination b {
        background-color: #009588;
        color: white;
        border: 1px solid #009588;
    }
</style>


<?php if (isset($_GET['custom_report_id']) && !empty(trim($_GET['custom_report_id']))) { ?>

    <style>

        .rep-filter {
            display: none;
        }

    </style>

<?php } ?>

 <style>
     #service_report_thead th{
         position: sticky;
         top: 0;
         background-color: #ccc;
     }

     .disable{
         opacity: 0.6;

     }
 </style>


<input type="hidden" id="custom_report_id" value="<?= $_GET['custom_report_id'] ?>">
<?php if(!user_check_access('SA_SRVREPORTALL')): ?>
<input type="hidden" id="hdn_user_id" value="<?= $_SESSION['wa_current_user']->user; ?>">
<?php endif; ?>


<script>

    $(document).ready(function () {

        GetReport();

        setTimeout(function () {

            $("#CustomerReportFilterPopup").hide();


        },1000);


        $(document).on("click", ".pg-link", function (e) {

            e.preventDefault();

            var req_url = $(this).attr("href");

            AxisPro.BlockDiv("#kt_content");


            $(".error_note").hide();

            AxisPro.APICall('GET', req_url, {}, function (data) {

                DisplayReport(data);


            });


        });


    });



    $("#customer_email" ).change(function() {
        if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($("#customer_email" ).val()))
        {
            alert('Enter a valid email address');
            $("#customer_email" ).val('');
        }

    });


    function SaveCustomReport($this) {

        var $form = $("#CustomerReportFilterPopup-Form");
        var params = AxisPro.getFormData($form);

        var additional_params = "";

        if ($($this).data("action") === 'save') {
            additional_params += "&custom_rep_id=" + $("#custom_report_id").val();
        }

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=save_custom_report" + additional_params, params, function (data) {

            if (data && data.status === 'FAIL') {
                toastr.error(data.msg);
            }
            else {

                Swal.fire({
                    position: 'top',
                    icon: 'success',
                    title: 'Report Saved.',
                    showConfirmButton: false,
                    timer: 1500
                });

                window.location.reload(true);

            }

        });
    }


    function display_sum_text(sum) {

        return '<span class="sum_text">'+amount(sum)+'</span>';

    }


    function DisplayReport(data) {
       

        var custom_rep_id = '<?php echo $_GET['custom_report_id'] ?>';

        var rep = data.rep;
        var total_rows = data.total_rows;
        var customers = data.customers;
        var gl_accounts = data.gl_accounts;
        var categories = data.categories;
        var service_category_map = data.service_category_map;
        var aggregates = data.aggregates;
        var users = data.users;
        var user_name=data.user_name;

        var this_is_custom_report = false;
        var custom_report = {};

        if (!custom_rep_id || custom_report_id.length <= 0 || custom_rep_id === '0') {

            $("#CustomerReportFilterPopup-Form input[type='checkbox']").prop('checked', true);
            $("#custom_report_update_btn").hide();

        }
        else {

            this_is_custom_report = true;
            custom_report = JSON.parse(data.custom_report.params);

            var custom_rep_name = data.custom_report.name;

            $("input[name='custom_report_name']").val(custom_rep_name);

            $("#report_title").html(custom_rep_name);

            //Show Hide Filters/Cols
            $.each(custom_report, function (key, val) {

                var filter_element_id = key.replace("filter_", "");
                $("#" + filter_element_id).parents("div").show();
                $("input[name='" + key + "']").prop("checked", true);

            });
        }


        var thead_html = "";

        if (isset_empty(custom_report.col_invoice_number) || !this_is_custom_report)
            thead_html += '<th style="min-width: 150px" data-field="stock_id" >INVOICE NUMBER</th>';

        if (isset_empty(custom_report.col_tran_date) || !this_is_custom_report)
            thead_html += '<th style="min-width: 100px" data-field="invoice_date" >INVOICE DATE</th>';

        if (isset_empty(custom_report.col_invoice_type) || !this_is_custom_report) {
            thead_html += '<th style="min-width: 100px" data-field="invoice_type" >CARD TYPE</th>';
        }

        if (isset_empty(custom_report.col_stock_id) || !this_is_custom_report)
            thead_html += '<th style="min-width: 100px" data-field="stock_id" >STOCK ID</th>';

        if (isset_empty(custom_report.col_service) || !this_is_custom_report)
            thead_html += '<th style="min-width: 300px" data-field="service">SERVICE NAME</th>';

        if (isset_empty(custom_report.col_category) || !this_is_custom_report)
            thead_html += '<th style="min-width: 100px" data-field="category">CATEGORY</th>';

        if (isset_empty(custom_report.col_customer) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="customer">CUSTOMER</th>';

        if (isset_empty(custom_report.col_sales_man) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="salesman">Sales Man</th>';

        if (isset_empty(custom_report.col_display_customer) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="display_customer">DISPLAY CUSTOMER</th>';

        if (isset_empty(custom_report.col_customer_mobile) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="customer_mobile">CUSTOMER MOBILE</th>';

        if (isset_empty(custom_report.col_customer_email) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="customer_email">CUSTOMER EMAIL</th>';

        if (isset_empty(custom_report.col_quantity) || !this_is_custom_report)
            thead_html += '<th style="min-width: 100px" data-field="quantity">' +
                'QUANTITY '+display_sum_text(aggregates.sum_quantity)+'</th>';

        if (isset_empty(custom_report.col_unit_price) || !this_is_custom_report)
            thead_html += '<th style="min-width: 100px" data-field="unit_price">SERVICE CHARGE</th>';

        if (isset_empty(custom_report.col_total_price) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="total_price">TOTAL SERVICE CHARGE'+
                display_sum_text(aggregates.sum_total_service_charge)+'</th>';

        if (isset_empty(custom_report.col_extra_service_charge) || !this_is_custom_report)
            thead_html += '<th style="min-width: 100px" data-field="extra_service_charge">Extra/Round Off Charge</th>';

        if (isset_empty(custom_report.col_total_price) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="total_tax">TOTAL VAT</th>';

        if (isset_empty(custom_report.col_govt_fee) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="govt_fee">GOVT.FEE</th>';

        if (isset_empty(custom_report.col_govt_bank) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="govt_bank">GOVT.BANK</th>';

        if (isset_empty(custom_report.col_bank_service_charge) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="bank_service_charge">BANK SERVICE CHARGE</th>';

        if (isset_empty(custom_report.col_bank_service_charge_vat) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="bank_service_charge_vat">BANK SERVICE CHARGE VAT</th>';


        if (isset_empty(custom_report.col_pf_amount) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="transaction_id">OTHER CHARGE</th>';

        if (isset_empty(custom_report.col_total_govt_fee) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="total_govt_fee">TOTAL GOVT.FEE'+display_sum_text(aggregates.sum_total_govt_fee)+'</th>';

        if (isset_empty(custom_report.col_transaction_id) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="pf_amount">BANK REFERENCE NUMBER</th>';

        if (isset_empty(custom_report.col_ed_transaction_id) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="pf_amount">MB/ST/DW-ID</th>';

        if (isset_empty(custom_report.col_application_id) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="pf_amount">APPLICATION ID / RECEIPT ID</th>';

        if (isset_empty(custom_report.col_ref_name) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="pf_amount">REF.NAME</th>';

        if (isset_empty(custom_report.col_employee_commission) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="employee_commission">EMPLOYEE COMMISSION'+
                display_sum_text(aggregates.sum_employee_commission)+'</th>';

        if (isset_empty(custom_report.col_customer_commission) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="customer_commission">CUSTOMER COMMISSION'+
                display_sum_text(aggregates.sum_customer_commission)+'</th>';


        if (isset_empty(custom_report.col_line_discount_amount) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="line_discount_amount">DISCOUNT AMOUNT'+
                display_sum_text(aggregates.sum_discount)+'</th>';


        if (isset_empty(custom_report.col_reward_amount) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="reward_amount">REWARD AMOUNT'+
                display_sum_text(aggregates.sum_reward)+'</th>';

        if (isset_empty(custom_report.col_payment_status) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="payment_status">PAYMENT STATUS</th>';


        if (isset_empty(custom_report.col_created_by) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="created_by">EMPLOYEE</th>';

        if (isset_empty(custom_report.col_employee_name) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="created_by">EMPLOYEE NAME</th>';


        if (isset_empty(custom_report.col_line_total) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="line_total">LINE TOTAL'+
                display_sum_text(aggregates.sum_line_total)+'</th>';

        if (isset_empty(custom_report.col_invoice_total) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="invoice_total">INVOICE TOTAL'+
                display_sum_text(aggregates.sum_invoice_total)+'</th>';


        if (isset_empty(custom_report.col_net_service_charge) || !this_is_custom_report)
            thead_html += '<th style="min-width: 200px" data-field="net_service_charge">NET SERVICE CHARGE'+
                display_sum_text(aggregates.sum_net_service_charge)+'</th>';

        $("#service_report_thead").html(thead_html);


        var tbody_html = "";

        $.each(rep, function (key, value) {

            tbody_html += "<tr>";


            if (isset_empty(custom_report.col_invoice_number) || !this_is_custom_report)
                tbody_html += "<td data-field='stock_id'>" + value.invoice_number + "</td>";

            if (isset_empty(custom_report.col_tran_date) || !this_is_custom_report)
                tbody_html += "<td data-field='stock_id'>" + value.tran_date + "</td>";

            if (isset_empty(custom_report.col_invoice_type) || !this_is_custom_report) {
                tbody_html += "<td data-field='invoice_type'>" + value.invoice_type + "</td>";
            }

            if (isset_empty(custom_report.col_stock_id) || !this_is_custom_report)
                tbody_html += "<td data-field='stock_id'>" + value.stock_id + "</td>";

            if (isset_empty(custom_report.col_service) || !this_is_custom_report)
                tbody_html += "<td data-field='service'>" + value.description + "</td>";

            if (isset_empty(custom_report.col_category) || !this_is_custom_report)
                tbody_html += "<td data-field='category'>" + categories[service_category_map[value.stock_id]] + "</td>";

            if (isset_empty(custom_report.col_customer) || !this_is_custom_report)
                tbody_html += "<td data-field='customer'>" + clean(customers[value.debtor_no]) + "</td>";
            
            if (isset_empty(custom_report.col_sales_man) || !this_is_custom_report)
                tbody_html += "<td data-field='salesman'>" + clean(value.salesman_name) + "</td>";

            if (isset_empty(custom_report.col_display_customer) || !this_is_custom_report)
                tbody_html += "<td data-field='display_customer'>" + value.display_customer + "</td>";

            if (isset_empty(custom_report.col_customer_mobile) || !this_is_custom_report)
                tbody_html += "<td data-field='customer_mobile'>" + value.customer_mobile + "</td>";

            if (isset_empty(custom_report.col_customer_email) || !this_is_custom_report)
                tbody_html += "<td data-field='customer_email'>" + value.customer_email + "</td>";

            if (isset_empty(custom_report.col_quantity) || !this_is_custom_report)
                tbody_html += "<td data-field='quantity'>" + value.quantity + "</td>";

            if (isset_empty(custom_report.col_unit_price) || !this_is_custom_report)
                tbody_html += "<td data-field='unit_price'>" + amount(value.unit_price) + "</td>";

            if (isset_empty(custom_report.col_total_price) || !this_is_custom_report)
                tbody_html += "<td data-field='total_price'>" + amount(value.total_service_charge) + "</td>";


            if (isset_empty(custom_report.col_extra_service_charge) || !this_is_custom_report)
                tbody_html += "<td data-field='extra_service_charge'>" + amount(value.extra_service_charge) + "</td>";


            if (isset_empty(custom_report.col_total_price) || !this_is_custom_report)
                tbody_html += "<td data-field='total_tax'>" + amount(value.total_tax) + "</td>";

            if (isset_empty(custom_report.col_govt_fee) || !this_is_custom_report)
                tbody_html += "<td data-field='govt_fee'>" + amount(value.govt_fee) + "</td>";

            if (isset_empty(custom_report.col_govt_bank) || !this_is_custom_report)
                tbody_html += "<td data-field='govt_bank'>" + clean(gl_accounts[value.govt_bank_account]) + "</td>";

            if (isset_empty(custom_report.col_bank_service_charge) || !this_is_custom_report)
                tbody_html += "<td data-field='bank_service_charge'>" + amount(value.bank_service_charge) + "</td>";

            if (isset_empty(custom_report.col_bank_service_charge_vat) || !this_is_custom_report)
                tbody_html += "<td data-field='bank_service_charge_vat'>" + amount(value.bank_service_charge_vat) + "</td>";

            if (isset_empty(custom_report.col_pf_amount) || !this_is_custom_report)
                tbody_html += "<td data-field='pf_amount'>" + amount(value.pf_amount) + "</td>";


            if (isset_empty(custom_report.col_total_govt_fee) || !this_is_custom_report)
                tbody_html += "<td data-field='total_govt_fee'>" + amount(value.total_govt_fee) + "</td>";

            if (isset_empty(custom_report.col_transaction_id) || !this_is_custom_report)
                tbody_html += "<td data-field='transaction_id'>" + clean(value.transaction_id) + "</td>";

            if (isset_empty(custom_report.col_ed_transaction_id) || !this_is_custom_report)
                tbody_html += "<td data-field='ed_transaction_id'>" + clean(value.ed_transaction_id) + "</td>";

            if (isset_empty(custom_report.col_application_id) || !this_is_custom_report)
                tbody_html += "<td data-field='application_id'>" + clean(value.application_id) + "</td>";

            if (isset_empty(custom_report.col_ref_name) || !this_is_custom_report)
                tbody_html += "<td data-field='ref_name'>" + clean(value.ref_name) + "</td>";



            if (isset_empty(custom_report.col_employee_commission) || !this_is_custom_report)
                tbody_html += "<td data-field='employee_commission'>" + amount(value.employee_commission) + "</td>";

            if (isset_empty(custom_report.col_customer_commission) || !this_is_custom_report)
                tbody_html += "<td data-field='customer_commission'>" + amount(value.customer_commission) + "</td>";


            if (isset_empty(custom_report.col_line_discount_amount) || !this_is_custom_report)
                tbody_html += "<td data-field='line_discount_amount'>" + amount(value.line_discount_amount) + "</td>";


            if (isset_empty(custom_report.col_reward_amount) || !this_is_custom_report)
                tbody_html += "<td data-field='reward_amount'>" + amount(value.reward_amount) + "</td>";

            if (isset_empty(custom_report.col_payment_status) || !this_is_custom_report)
                tbody_html += "<td data-field='payment_status'>" + clean(value.payment_status) + "</td>";


            if (isset_empty(custom_report.col_created_by) || !this_is_custom_report)
                tbody_html += "<td data-field='created_by'>" + clean(users[value.created_by]) + "</td>";

            if (isset_empty(custom_report.col_employee_name) || !this_is_custom_report)
                tbody_html += "<td data-field='created_by'>" + clean(user_name[value.created_by]) + "</td>";

            if (isset_empty(custom_report.col_line_total) || !this_is_custom_report)
                tbody_html += "<td data-field='line_total'>" + amount(value.line_total) + "</td>";

            if (isset_empty(custom_report.col_invoice_total) || !this_is_custom_report)
                tbody_html += "<td data-field='invoice_total'>" + amount(value.invoice_total) + "</td>";


            var net_service_charge = value.line_total-value.reward_amount-value.customer_commission-value.employee_commission;

            if (isset_empty(custom_report.col_net_service_charge) || !this_is_custom_report)
                tbody_html += "<td data-field='net_service_charge'>" + amount(net_service_charge) + "</td>";

            tbody_html += "</tr>";

        });

        $("#service_report_tbody").html(tbody_html);

        if (!window.loadedOnce){
            loadFilters(data.filters);
            window.loadedOnce = true;
        }

        $("#pg-link").html(data.pagination_link);

        //$('.double-scroll-table').doubleScroll();

        AxisPro.UnBlockDiv("#kt_content");

        setTimeout(function () {

            $("#CustomerReportFilterPopup").hide();


        },1000);


    }

    function loadFilters(filters) { 
        AxisPro.PrepareSelectOptions(filters.customers, 'debtor_no', 'name', 'ap_customer_select', 'All');
        AxisPro.PrepareSelectOptions(filters.salesman, 'salesman_code', 'salesman_name', 'ap_salesman_select', 'All');
        AxisPro.PrepareSelectOptions(filters.categories, 'category_id', 'description', 'ap_item_category_select', 'All');
        AxisPro.PrepareSelectOptions(filters.users, 'id', 'real_name', 'ap_user_select',null,function()
        {


            var user_id=$('#hdn_user_id').val();

            if(user_id!=undefined)
            {
                $('#employee').val(user_id);
            }
            else
            {
                $('#employee').prepend('<option value="0">All</option>');
                $('#employee').val('0');
            }
        });
        AxisPro.PrepareSelectOptions(filters.services, 'stock_id', 'description', 'ap_service_select', 'All');
    }

    function GetReport() {

        // alert(ERP_FUNCTION_API_END_POINT);
        AxisPro.BlockDiv("#kt_content");

        var $form = $("#report_filter_form");
        var params = AxisPro.getFormData($form);

        var custom_rep_id = '<?php echo $_GET['custom_report_id'] ?>';

        $(".error_note").hide();

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT + "?method=service_report&custom_rep_id=" + custom_rep_id, params, function (data) {

            DisplayReport(data);


        });

    }

    function ExportToCSV() {


        AxisPro.BlockDiv("#kt_content");

        var $form = $("#report_filter_form");
        var params = AxisPro.getFormData($form);

        var custom_rep_id = '<?php echo $_GET['custom_report_id'] ?>';

        $(".error_note").hide();

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT + "?method=export_csv&custom_rep_id=" + custom_rep_id, params, function (data) {



        });

    }

</script>

