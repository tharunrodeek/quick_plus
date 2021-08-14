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
                                    <?= trans('User Log') ?>
                                </h3>
                            </div>

                        </div>

                        <!--begin::Form-->


                        <div style="padding: 14px">

                            <form id="filter_form">

                                <div class="form-group row">


                                    <div class="col-lg-2">
                                        <label class=""><?= trans('From Date') ?>:</label>
                                        <input type="text" name="start_date"
                                               class="form-control ap-datepicker config_begin_fy"
                                               readonly placeholder="Select date"
                                               value="<?= add_days(Today(),-30) ?>"/>
                                    </div>

                                    <div class="col-lg-2">
                                        <label class=""><?= trans('To Date') ?>:</label>
                                        <input type="text" name="end_date"
                                               class="form-control ap-datepicker config_begin_fy"
                                               readonly placeholder="Select date"
                                               value="<?= Today() ?>"/>
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
                                <th><?= trans('Date Time') ?></th>
                                <th><?= trans('Description') ?></th>
                                <th><?= trans('User') ?></th>
                                <th><?= trans('IP Address') ?></th>

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

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=getActivityLog", params, function (data) {

            DisplayReport(data);


        });

    }


    function DisplayReport(data) {

        var rep = data.rep;
        var users = data.users;

        var tbody_html = "";

        $.each(rep, function (key, value) {

            tbody_html += "<tr>";


            tbody_html += "<td>" + value.created_at + "</td>";
            tbody_html += "<td>" + value.description + "</td>";
            tbody_html += "<td>" + users[value.user_id] + "</td>";
            tbody_html += "<td>" + value.user_ip + "</td>";

            tbody_html += "</tr>";

        });

        $("#tbody").html(tbody_html);


        $("#pg-link").html(data.pagination_link);

        // $('.double-scroll-table').doubleScroll();

        AxisPro.UnBlockDiv("#kt_content");

    }


</script>
