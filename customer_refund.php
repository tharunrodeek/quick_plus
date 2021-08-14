<?php include "header.php" ?>


<style>

    .refund_amt {
        width: 100px;
        pointer-events: none;
    }

</style>


<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <!-- begin:: Content Head -->


            <!-- end:: Content Head -->

            <!-- begin:: Content -->
            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <!--Begin::Dashboard 2-->

                <!--Begin::Row-->

                <div class="row">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('REFUND PROCESS') ?>
                                </h3>
                            </div>
                        </div>

                        <!--begin::Form-->
                        <form method="post" action="<?= $erp_url ?>reporting/prn_redirect.php" id="rep-form"
                              onsubmit="AxisPro.ShowPopUpReport(this)"
                              class=" kt-form kt-form--fit kt-form--label-right">

                            <div class="kt-portlet__body">


                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label"><?= trans('Customer') ?>:</label>
                                    <div class="col-lg-3">
                                        <select class="form-control kt-select2 ap-select2 ap-customer-select ev-input"
                                                name="customer_id" id="customer_id">

                                        </select>
                                    </div>

                                    <label class="col-lg-2 col-form-label"><?= trans('RCPT NO') ?>:</label>
                                    <div class="col-lg-3">
                                        <input type="text" name="rcpt_no" id="rcpt_no" class="form-control ev-input">
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label"><?= trans('INV NO') ?>:</label>
                                    <div class="col-lg-3">
                                        <input type="text" name="inv_no" id="inv_no" class="form-control ev-input">
                                    </div>

                                    <label class="col-lg-2 col-form-label"></label>

                                    <div class="col-lg-3">
                                        <button type="button" class="form-control btn btn-primary" id="btn-find">
                                            SEARCH
                                        </button>
                                    </div>

                                </div>


                            </div>

                            <div class="kt-portlet__body">


                                <table class="table table-bordered">

                                    <thead>
                                    <th>#</th>
                                    <th>RCPT NO</th>
                                    <th>DATE</th>
                                    <th>INVOICES</th>
                                    <th>TOTAL RCPT AMT</th>
                                    <th>TOTAL ALLOC AMOUNT</th>
                                    <th>BALANCE TO REFUND</th>
                                    <th>REFUND AMOUNT</th>
                                    <th></th>
                                    </thead>

                                    <tbody id="tbody-alloc">
                                    <tr>
                                        <td style='text-align: center' colspan='9'>No Data</td>
                                    </tr>
                                    </tbody>


                                </table>


                            </div>

                            <div class="" style="text-align: center">
                                <button type="button" id="btn-process-refund"
                                        class="btn btn-success"><?= trans('PROCESS REFUND') ?></button>
                            </div>
                        </form>

                        <!--end::Form-->
                    </div>
                </div>


                <!--End::Row-->


                <!--End::Row-->

                <!--End::Dashboard 2-->
            </div>

            <!-- end:: Content -->
        </div>
    </div>
</div>

<?php include "footer.php"; ?>

<script>


    $(document).ready(function () {

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
            method: 'get_all_customers',
            format: 'json'
        }, function (data) {
            AxisPro.PrepareSelectOptions(data, 'debtor_no', 'name', 'ap-customer-select', 'Select a customer');
        });


        $(".ev-input").change(function () {

            var customer_id = $("#customer_id").val();

            // if(!customer_id)
            //     return false;

            var rcpt_no = $("#rcpt_no").val();
            var inv_no = $("#inv_no").val();

            AxisPro.BlockDiv("#kt_content");

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
                method: 'get_customer_advances',
                format: 'json',
                customer_id: customer_id,
                rcpt_no: rcpt_no,
                inv_no: inv_no
            }, function (data) {

                AxisPro.UnBlockDiv("#kt_content");
                var tbody_html = "";
                if (data.length > 0) {

                    var cnt = 1;

                    $.each(data, function (key, val) {

                        console.log(val);

                        var left_to_alloc = parseFloat(val.TotalAmount) - parseFloat(val.Allocated);


                        if(val.invoice_numbers == null)
                            val.invoice_numbers = "";

                        tbody_html += "<tr>";
                        tbody_html += "<td>" + cnt + "</td>";
                        tbody_html += "<td>" + val.reference + "</td>";
                        tbody_html += "<td>" + val.tran_date + "</td>";
                        tbody_html += "<td>" + val.invoice_numbers + "</td>";
                        tbody_html += "<td>" + val.TotalAmount + "</td>";
                        tbody_html += "<td>" + val.Allocated + "</td>";
                        tbody_html += "<td>" + left_to_alloc + "</td>";
                        tbody_html += "<td><input type='number' value='0'  class='form-control refund_amt' data-amt='" + left_to_alloc + "' data-trans_no='" + val.trans_no + "'></td>";
                        tbody_html += "<td><a href='javascript:void(0)' class='btn btn-sm btn-primary line-all'>All</a></td>";
                        tbody_html += "</tr>";
                        cnt++;

                    });

                    $("#tbody-alloc").html(tbody_html);


                } else {

                    $("#tbody-alloc").html("<tr><td style='text-align: center' colspan='9'>No Data</td></tr>");

                }

            });

        });

        $('body').on('click', '.line-all', function () {

            var input_elem = $(this).parents("tr").find('.refund_amt');
            var line_left_alloc = input_elem.data('amt');
            input_elem.val(line_left_alloc)

        });

        $('body').on('change', '.refund_amt', function () {

            var elem = $(this);
            var left_to_alloc = parseFloat(elem.data('amt'));
            var this_alloc = parseFloat(elem.val());

            if (this_alloc > left_to_alloc)
                elem.val(left_to_alloc)

        });

        $("#btn-find").click(function () {

            $(".ev-input").eq(0).trigger('change');

        });


        $("#btn-process-refund").click(function () {

            AxisPro.BlockDiv("#kt_content","Processing Refund. Please wait....");

            var values = [];

            var has_alloc = false;
            $('.refund_amt').each(function () {

                if (this.value !== '' && this.value !== "0" && this.value !== 0  ) {
                    has_alloc = true;
                    values.push({trans_no: $(this).data('trans_no'), amount: this.value});
                }
            });

            if (!has_alloc) {
                swal.fire(
                    'ERROR',
                    'Please choose at least one transaction to refund',
                    'warning'
                ).then(function () {
                    window.location.reload();
                });
                return false;
            }

            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT, {
                method: 'process_refund',
                format: 'json',
                allocs: values
            }, function (data) {

                AxisPro.UnBlockDiv("#kt_content");

                swal.fire(
                    data.status === 'OK' ? 'Success!' : 'FAILED',
                    data.msg,
                    data.status === 'OK' ? 'success' : 'warning'
                ).then(function () {
                    popupCenter(data.print_url, '_blank', 900, 500);
                    window.location.reload();
                });


            });

        })




        function popupCenter (url, title, w, h) {
            // Fixes dual-screen position                             Most browsers      Firefox
            var dualScreenLeft = window.screenLeft !==  undefined ? window.screenLeft : window.screenX;
            var dualScreenTop = window.screenTop !==  undefined   ? window.screenTop  : window.screenY;

            var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            var systemZoom = width / window.screen.availWidth;
            var left = (width - w) / 2 / systemZoom + dualScreenLeft
            var top = (height - h) / 2 / systemZoom + dualScreenTop
            var newWindow = window.open(
                url,
                title,
                'scrollbars=yes'
                + ',width='  + w / systemZoom
                + ',height=' + h / systemZoom
                + ',top='    + top
                + ',left='   + left
            )

            if (window.focus) newWindow.focus();
        }







    });


</script>
