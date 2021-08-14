<?php include "header.php"; ?>

<style>

    .form-control {
        border: 1px solid #9e9e9e;
    }

</style>

<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('Employee Deduction Report') ?>
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
                                               value="<?= Today() ?>"/>
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
                                <th><?= trans('Employee') ?></th>
                                <th><?= trans('Commission Amount') ?></th>
                                <th><?= trans('Deduction Amount') ?></th>
                                <th><?= trans('Net') ?></th>

                                </thead>
                                <tbody id="tbody">

                                </tbody>
                            </table>



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



    function GetReport() {

        AxisPro.BlockDiv("#kt_content");

        var $form = $("#filter_form");
        var params = AxisPro.getFormData($form);

        $(".error_note").hide();

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=getEmployeeCustomDeductionSummary", params, function (data) {

            AxisPro.UnBlockDiv("#kt_content");


            var tbody_html = "";

            $.each(data, function (key, value) {

                tbody_html += "<tr>";

                tbody_html += "<td>" + value.emp_user + "</td>";
                tbody_html += "<td>"+value.usr_comm+"</td>";
                tbody_html += "<td>"+value.amt+"</td>";
                tbody_html += "<td>"+value.net+"</td>";
                tbody_html += "</tr>";

            });


            $("#tbody").html(tbody_html);


        });

    }




</script>
