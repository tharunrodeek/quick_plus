<?php include "header.php"; ?>

<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('LIST OF SERVICE REQUESTS') ?>
                                </h3>
                            </div>

                            <div class="kt-portlet__head-label">
                                <a href="new_service_request.php" class="btn btn-sm btn-primary">Add New Service
                                    Request</a>
                            </div>


                        </div>

                        <!--begin::Form-->


                        <div style="padding: 14px">

                            <form id="filter_form">

                                <div class="form-group row">


                                    <div class="col-lg-2">
                                        <label class=""><?= trans('From Date') ?>:</label>
                                        <input type="text" name="fl_start_date"
                                               class="form-control ap-datepicker config_begin_fy"
                                               readonly placeholder="Select date"
                                               value="<?= Today() ?>"/>
                                    </div>

                                    <div class="col-lg-2">
                                        <label class=""><?= trans('To Date') ?>:</label>
                                        <input type="text" name="fl_end_date"
                                               class="form-control ap-datepicker config_begin_fy"
                                               readonly placeholder="Select date"
                                               value="<?= Today() ?>"/>
                                    </div>


                                    <div class="col-lg-3">
                                        <label class=""><?= trans('Status') ?>:</label>

                                        <select class="form-control kt-selectpicker" name="fl_status">

                                            <option value="PEDNING"><?= trans('Pending') ?></option>
                                            <option value="COMPLETED"><?= trans('Completed') ?></option>
                                            <option value="TRANS_COMPLETED"><?= trans('Completed with Transaction') ?></option>
                                            <option value=""><?= trans('All') ?></option>
                                        </select>


                                    </div>


                                    <div class="col-lg-1">
                                        <label class="">&nbsp</label>
                                        <button type="button" id="search_btn" onclick="GetReport()"
                                                class="form-control btn btn-sm btn-primary">
                                            Search
                                        </button>
                                    </div>


                                </div>

                            </form>

                        </div>


                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <table class="table table-bordered" id="service_req_list_table">
                                <thead>
                                <th><?= trans('Date') ?></th>
                                <th><?= trans('Reference') ?></th>
                                <th><?= trans('Token') ?></th>
                                <th><?= trans('Customer') ?></th>
                                <th><?= trans('Memo') ?></th>
                                <th><?= trans('Invoice Number') ?></th>
                                <th><?= trans('Transaction IDs') ?></th>
                                <th><?= trans('Status') ?></th>

                                <th></th>
                                </thead>
                                <tbody id="tbody">

                                </tbody>
                            </table>

                            <div id="pg-link"></div>


                        </div>


                        <!--end::Form-->
                    </div>
                </div>

            </div>

            <!-- end:: Content -->
        </div>
    </div>
</div>


<?php include "footer.php"; ?>


<script>

    $.date = function (dateObject) {
        var d = new Date(dateObject);
        var day = d.getDate();
        var month = d.getMonth() + 1;
        var year = d.getFullYear();
        if (day < 10) {
            day = "0" + day;
        }
        if (month < 10) {
            month = "0" + month;
        }
        var date = day + "-" + month + "-" + year;

        return date;
    };


    var curr_user_id = '<?php echo $_SESSION['wa_current_user']->user ?>';


    $(document).ready(function () {

        GetReport();


        $('#service_req_list_table').on("click", '[data-action="delete_srv_req"]', function (evnt) {
            AxisPro.BlockDiv("#kt_content");
            var data = {
                method: 'del_srv_request',
                id: this.dataset.id
            };

            var handleResponse = function (resp) {
                AxisPro.UnBlockDiv("#kt_content");
                if (resp.code !== 204) {
                    swal.fire('FAILED', resp.msg, 'warning');
                } else {
                    swal.fire('Success', resp.msg, 'success')
                        .then(function () {
                            GetReport();
                        });
                }
            };

            AxisPro.APICall(
                'POST',
                ERP_FUNCTION_API_END_POINT,
                data,
                handleResponse
            );
        });


    });


    $(document).on("click", ".pg-link", function (e) {

        e.preventDefault();

        var req_url = $(this).attr("href");

        AxisPro.BlockDiv("#kt_content");


        $(".error_note").hide();

        AxisPro.APICall('GET', req_url, {}, function (data) {

            DisplayReport(data);


        });


    });


    function GetReport() {

        AxisPro.BlockDiv("#kt_content");

        var $form = $("#filter_form");
        var params = AxisPro.getFormData($form);

        $(".error_note").hide();

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=getServiceRequests", params, function (data) {

            DisplayReport(data);


        });

    }


    function DisplayReport(data) {

        var rep = data.rep;
        var users = data.users;

        var tbody_html = "";

        $.each(rep, function (key, value) {

            tbody_html += "<tr>";


            var status = 'Pending';

            if (value.is_invoiced === '1') {
                status = "Completed";
            }


            tbody_html += "<td>" + $.date(new Date(value.created_at));
            +"</td>";
            tbody_html += "<td>" + value.reference + "</td>";
            tbody_html += "<td>" + value.token_number + "</td>";
            tbody_html += "<td><u>" + value.customer_name + "</u><br>" + value.display_customer + "</td>";
            tbody_html += "<td>" + value.memo + "</td>";
            tbody_html += "<td>" + value.invoice_number + "</td>";
            tbody_html += "<td>" + value.transaction_ids + "</td>";
            tbody_html += "<td>" + status + "</td>";

            tbody_html += "<td class='action_td'>";

            if (value.is_invoiced === '0') {

                var url = ERP_ROOT_URL + "sales/sales_order_entry.php?NewInvoice=0&" +
                    "dim_id=" + value.cost_center_id + "&SRQ_TOKEN=" + value.token_number + "&req_id=" + value.id;

                var edit_url = "new_service_request.php?edit_id=" + value.id;

                tbody_html += "<a href = '" + url + "' class='btn btn-sm btn-primary'>Make Invoice</a>";
                tbody_html += "<a href = '" + edit_url + "' class='btn btn-sm btn-warning'>Edit</a>";


                if (value.is_invoiced == 0) {
                    tbody_html += '<button data-action="delete_srv_req" data-id="' + value.id + '" class="btn btn-sm btn-danger">Delete</button>'
                }
            }

            tbody_html += "</td>";


            tbody_html += "</tr>";

        });

        $("#tbody").html(tbody_html);


        $("#pg-link").html(data.pagination_link);

        // $('.double-scroll-table').doubleScroll();

        AxisPro.UnBlockDiv("#kt_content");

    }


</script>
