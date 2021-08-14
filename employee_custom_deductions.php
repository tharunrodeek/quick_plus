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
                                <h2 class="kt-portlet__head-title">
                                    <?= trans('Manage Employee Deductions') ?>
                                </h2>
                            </div>

                        </div>

                        <!--begin::Form-->

                        <form id="emp_form">


                            <div style="padding: 14px">


                                <div class="form-group row">


                                    <div class="col-lg-2">
                                        <label class=""><?= trans('Date') ?>:</label>
                                        <input type="text" name="date_" id="date_"
                                               class="form-control ap-datepicker config_begin_fy"
                                               readonly placeholder="Select date"
                                               value="<?= Today() ?>"/>
                                    </div>

                                </div>


                            </div>


                            <div class="table-responsive" style="padding: 7px 7px 7px 7px;">


                                <table class="table table-bordered" id="service_req_list_table">
                                    <thead>
                                    <th><?= trans('Employee Name') ?></th>
                                    <th style=""><?= trans('Deduction Amount') ?></th>
                                    <th><?= trans('Description') ?></th>
                                    <th></th>
                                    </thead>
                                    <tbody id="tbody">

                                    <?php

                                    $users = $api->get_key_value_records('0_users', 'id', 'user_id');

                                    foreach ($users as $key => $val) {

                                        echo "<tr>";
                                        echo "<td>" . $val . "</td>";
                                        echo "<td>
                                    <input type='hidden' name='user_id[]' value='" . $key . "'>
                                    <input type='number' name='deduct_amt[]'
                                               class='form-control col-sm-3'></td>";

                                        echo "<td><textarea name='description[]' class='form-control'></textarea></td>";
                                        echo "<td></td>";
                                        echo "</tr>";
                                    }

                                    ?>

<!--                                    <tr>-->
<!--                                        <td>-->
<!--                                            <button class="btn btn-sm btn-primary form-control" type="button"-->
<!--                                                    id="btn-submit">SUBMIT-->
<!--                                            </button>-->
<!---->
<!--                                        </td>-->
<!--                                    </tr>-->

                                    </tbody>
                                </table>

                                <div style="text-align: center;">
                                    <button class="btn btn-sm btn-primary form-control col-md-2" type="button"
                                            id="btn-submit">SUBMIT
                                    </button>
                                </div>



                                <div id="pg-link"></div>


                            </div>


                        </form>

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

        getReport();


    });


    $("#date_").change(function (e) {

        getReport();

    });


    function getReport() {

        AxisPro.BlockDiv("#kt_content");

        var date_ = $("#date_").val();

        var params = {
            date_ : date_
        };

        $(".error_note").hide();

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT + "?method=getEmployeeCustomDeductions", params, function (data) {

            AxisPro.UnBlockDiv("#kt_content");


            var tbody_html = "";

            $.each(data, function (key, value) {

                tbody_html += "<tr>";

                tbody_html += "<td>" + value.emp_user + "</td>";
                tbody_html += "<td><input type='hidden' name='user_id[]' value='"+value.user_id+"'> " +
                    "<input type='number' name='deduct_amt[]' class='form-control col-sm-3' value='"+value.amt+"'></td>";

                tbody_html += "<td><textarea name='description[]' class='form-control'>"+value.description+"</textarea></td>";

                tbody_html += "<td></td>";
                tbody_html += "</tr>";

            });


            $("#tbody").html(tbody_html);

        });

    }

    $("#btn-submit").click(function (e) {

        var $form = $("#emp_form");
        var params = $form.serializeArray();

        // console.log(params);

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=saveEmployeeCustomDeductions", params, function (data) {

            if (data.status === "FAIL") {

                swal.fire(
                    'Warning!',
                    data.msg,
                    'warning'
                );

            }
            else {

                swal.fire(
                    'Success!',
                    'Employee Deductions Updated.',
                    'success'
                ).then(function () {
                    window.location.reload();
                });

            }

        });

    })


</script>
