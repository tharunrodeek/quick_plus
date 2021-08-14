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
                                    <?= trans('CASH FLOW STATEMENT') ?>
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
                                        <button type="button" id="search_btn" onclick="GetReport();"
                                                class="form-control btn btn-sm btn-primary">
                                            Search
                                        </button>
                                    </div>


                                </div>

                            </form>

                        </div>


                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <table class="table table-bordered" id="cashflow_table">
                                <thead>
                                <th><?= trans('Account') ?></th>
                                <th><?= trans('Opening Balance') ?></th>
                                <th><?= trans('IN FLOW') ?></th>
                                <th><?= trans('OUT FLOW') ?></th>
                                <th><?= trans('BALANCE') ?></th>
                                <th><?= trans('Closing Balance') ?></th>

                                <th></th>
                                </thead>
                                <tbody id="cashflow_tbody">


<!--                                --><?php
//
//                                $filters = [
//                                    'start_date' => $_GET['fl_start_date'],
//                                    'end_date' => $_GET['fl_end_date'],
//                                ];
//
//                                $report = $api->getCashFlowReport($filters,'array');
//
//                                foreach ($report as $row) {
//
//                                    $closing_balance = $row['opening_balance']+$row['debit_total']+$row['credit_total'];
//
//                                    echo "<tr>";
//                                    echo "<td>".$row['bank_account_name']."</td>";
//                                    echo "<td>".abs($row['opening_balance'])."</td>";
//                                    echo "<td>".abs($row['debit_total'])."</td>";
//                                    echo "<td>".abs($row['credit_total'])."</td>";
//                                    echo "<td>".abs($closing_balance)."</td>";
//                                    echo "</tr>";
//
//                                }
//
//
//                                ?>



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



    function GetReport() {

        // AxisPro.BlockDiv("#kt_content");

        var $form = $("#filter_form");
        var params = AxisPro.getFormData($form);

        $(".error_note").hide();

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=getCashFlowReport", params, function (data) {

            // AxisPro.UnBlockDiv("#kt_content");

            var tbody_html = "";

            $.each(data, function(key,value) {

                var closing_balance = parseFloat(value.opening_balance)
                                        +parseFloat(value.debit_total)
                                        +parseFloat(value.credit_total);

                var balance = parseFloat(value.debit_total)+parseFloat(value.credit_total);

                tbody_html+="<tr>";
                tbody_html+="<td>"+value.bank_account_name+"</td>";
                tbody_html+="<td>"+Math.abs(value.opening_balance).toFixed(2)+"</td>";
                tbody_html+="<td>"+Math.abs(value.debit_total).toFixed(2)+"</td>";
                tbody_html+="<td>"+Math.abs(value.credit_total).toFixed(2)+"</td>";
                tbody_html+="<td>"+Math.abs(balance).toFixed(2)+"</td>";
                tbody_html+="<td>"+Math.abs(closing_balance).toFixed(2)+"</td>";

                tbody_html+="</tr>";

            });

            $("#cashflow_tbody").html(tbody_html);



        });

    }




</script>
