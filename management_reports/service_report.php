<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <!-- begin:: Content Head -->


            <!-- end:: Content Head -->

            <!-- begin:: Content -->
            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <!--Begin::Dashboard 2-->

                <!--Begin::Row-->


                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title" id="report_title">SERVICE REPORT</h3>
                        </div>
                    </div>
                </div>


                <form class="kt-form kt-form--label-right" id="report_filter_form" style="padding: 2px 0 9px 0;
    border-radius: 6px; border: 1px solid #ccc"
                      method="post" action="<?= $erp_url ?>API/hub.php?method=decide">


                    <div class="kt-portlet__body" style="padding: 5px !important;">
                        <div class="form-group row">
                            <div class="col-lg-2 rep-filter">
                                <label for="invoice_number">Invoice No:
                                </label>

                                <input type="text" id="invoice_number" name="invoice_number" class="form-control"
                                       placeholder="">


                            </div>
                            <div class="col-lg-2 rep-filter">
                                <label>SalesMan:
                                </label>

                                <select style="width: 350px !important;" class="form-control kt-select2 ap-select2 ap_salesman_select"
                                        name="salesman" id="salesman">

                                    <option value="">--</option>

                                </select>


                            </div>
                            <div class="col-lg-4 rep-filter">
                                <label class="">Service:</label>
                                <select class="form-control kt-select2 ap-select2 ap_service_select"
                                        name="service" id="service">

                                    <option value="">--</option>

                                </select>
                            </div>


                            <div class="col-lg-2 rep-filter">
                                <label>Payment Status:
                                </label>

                                <select id="payment_status" class="form-control kt-selectpicker" name="payment_status">
                                    <option value="">All</option>
                                    <option value="2">Not Paid</option>
                                    <option value="3">Partially Paid</option>
                                    <option value="1">Fully Paid</option>
                                </select>


                            </div>
                            <div class="col-lg-2 rep-filter">
                                <label>Customer:
                                </label>

                                <select style="width: 180px !important;"
                                        class="form-control kt-select2 ap-select2 ap_customer_select"
                                        name="customer[]" id="customer" multiple="multiple">

                                    <option value="">--</option>

                                </select>
                            </div>


                            <div class="col-lg-2 rep-filter">
                                <label>Date From:
                                </label>

                                <input type="text" id="date_from" name="date_from" class="form-control ap-datepicker config_begin_fy"
                                       readonly placeholder="Select date" value="<?= Today() ?>"/>


                            </div>


                            <div class="col-lg-2 rep-filter">
                                <label>Date To:
                                </label>

                                <input type="text" id="date_to" name="date_to" class="form-control ap-datepicker config_begin_fy"
                                       readonly placeholder="Select date" value="<?= Today() ?>"/>


                            </div>
                            <div class="col-lg-2 rep-filter">
                                <label>Display Customer:
                                </label>

                                <input type="text" id="display_customer" name="display_customer" class="form-control"
                                       placeholder="">


                            </div>
                            <div class="col-lg-2 rep-filter">
                                <label class="">Transaction ID:</label>
                                <input type="text" id="transaction_id" name="transaction_id" class="form-control"
                                       placeholder="">
                            </div>


                            <div class="col-lg-2 rep-filter">
                                <label class="">Category:</label>
                                <select class="form-control kt-select2 ap-select2 ap_item_category_select"
                                        name="category" id="category">

                                    <option value="">--</option>

                                </select>
                            </div>


                            <div class="col-lg-2 rep-filter">
                                <label class="">Employee:</label>
                                <select class="form-control kt-select2 ap-select2 ap_user_select"
                                        name="employee" id="employee">

                                    <option value="">--</option>

                                </select>
                            </div>

                            <div class="col-lg-2 rep-filter">
                                <label class="">Transaction Status:</label>
                                <select id="transaction_status" class="form-control kt-selectpicker"
                                        name="transaction_status">
                                    <option value="">All</option>
                                    <option value="0">Completed</option>
                                    <option value="1">Not Completed</option>
                                </select>
                            </div>

                            <div class="col-lg-2 rep-filter">
                                <label class="">Card Type:</label>
                                <select id="invoice_type" class="form-control kt-selectpicker" name="invoice_type">
                                    <option value="">All</option>
                                    <option value="Cash">Cash</option>
                                    <option value="CenterCard">Center Card</option>
                                    <option value="CustomerCard">Customer Card</option>
                                </select>
                            </div>

                            <div class="col-lg-2 rep-filter">
                                <label class="">Mobile Number:</label>
                                <input type="text" id="customer_mobile" name="customer_mobile" class="form-control"
                                       placeholder="">
                            </div>

                            <div class="col-lg-2 rep-filter">
                                <label class="">Customer Email:</label>
                                <input type="email" id="customer_email" name="customer_email" class="form-control"
                                       placeholder="" >
                            </div>
                            
                            <div class="col-lg-2 rep-filter">
                                <label class="">Application ID:</label>
                                <input type="text" id="application_id" name="application_id" class="form-control"
                                       placeholder="" >
                            </div>

                        </div>


                    </div>


                    <div class="kt-portlet__foot" style="padding-left: 5px !important;">
                        <div class="kt-form__actions" style="margin-top: -17px !important;">
                            <div class="row">
                                <div class="col-lg-8">
                                    <button type="button"  onclick="GetReport();" class="btn btn-success btn-sm">
                                        Submit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary">Reset</button>

                                    <button type="button" data-toggle="modal" data-target="#CustomerReportFilterPopup"
                                            class="btn btn-primary btn-sm">CUSTOM REPORT MENU
                                    </button>

                                    <button type="submit" name="btnClick" data-toggle="modal" value="csv"
                                            class="btn btn-primary btn-sm">EXPORT TO CSV
                                    </button>
                                    <input type="hidden" name="custom_report_hdn_id" value="<?= $_GET['custom_report_id'] ?>">
                               <?php if($_GET['custom_report_id']=='8'): ?>
                                    <button type="submit" name="btnClick"  data-toggle="modal" value="pdf"
                                            class="btn btn-primary btn-sm">EXPORT TO PDF
                                    </button>
                               <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>


                <div class="double-scroll-table" style="padding: 7px 7px 7px 7px; overflow: scroll;height:520px;">

                    <table class="table-bordered scroll_table table-sm table-head-bg-brand" id="service_report_table">
                        <thead id="service_report_thead">

                        </thead>
                        <tbody id="service_report_tbody">

                        </tbody>
                    </table>




                </div>

                <div id="pg-link"></div>


                <!--End::Row-->


                <!--End::Row-->

                <!--End::Dashboard 2-->
            </div>

            <!-- end:: Content -->
        </div>
    </div>
</div>


<div class="modal fade" id="CustomerReportFilterPopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="COA_modal_title">Custom Report - Choose Columns and Filters</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form id="CustomerReportFilterPopup-Form">
                    <div class="kt-portlet__body">


                        <label>Report Name:</label>
                        <input name="custom_report_name"  type="text" class="form-control">


                        <ul class="nav nav-tabs  nav-tabs-line" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#kt_tabs_1_1" role="tab">Columns</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#kt_tabs_1_3" role="tab">Filters</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="kt_tabs_1_1" role="tabpanel" style="margin-left: 10px">


                                <div class="form-group row">
                                    <div class="kt-checkbox-list" style="padding-right: 14px">
                                        <label class="kt-checkbox">
                                            <input name="col_stock_id" type="checkbox"> Stock ID
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="col_service" type="checkbox"> Service Name
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="col_category" type="checkbox"> Category
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_line_total" type="checkbox"> Line Total
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_transaction_id" type="checkbox"> Bank Reference no.
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_line_discount_amount" type="checkbox"> Discount Amount
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_reward_amount" type="checkbox"> Reward Amount
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="col_ed_transaction_id" type="checkbox">MB/ST/DW-ID
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="col_sales_man" type="checkbox">Sales Man
                                            <span></span>
                                        </label>
                                    </div>

                                    <div class="kt-checkbox-list" style="padding-right: 14px">
                                        <label class="kt-checkbox">
                                            <input name="col_customer" type="checkbox"> Customer
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="col_display_customer" type="checkbox"> Display Customer
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="col_quantity" type="checkbox"> Quantity
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="col_unit_price" type="checkbox"> Unit Price
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="col_application_id" type="checkbox"> Application ID
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_employee_commission" type="checkbox"> Employee Commission
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_extra_service_charge" type="checkbox"> Extra/Round Off Charge
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_net_service_charge" type="checkbox"> Net Service Charge
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_customer_mobile" type="checkbox"> Customer Mobile
                                            <span></span>
                                        </label>
                                    </div>


                                    <div class="kt-checkbox-list" style="padding-right: 14px">

                                        <label class="kt-checkbox">
                                            <input name="col_govt_fee" type="checkbox"> Govt. Fee
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="col_govt_bank" type="checkbox"> Govt. Bank
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="col_bank_service_charge" type="checkbox"> Bank Service Charge
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_bank_service_charge_vat" type="checkbox"> Bank Service
                                            Charge VAT
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_total_govt_fee" type="checkbox"> Total Govt Charge
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_ref_name" type="checkbox"> Ref.Name
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_customer_commission" type="checkbox"> Customer Commission
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="col_customer_email" type="checkbox"> Customer Email
                                            <span></span>
                                        </label>
                                        <label for="col_invoice_type" class="kt-checkbox">
                                            <input type="checkbox" name="col_invoice_type" id="col_invoice_type">Card Type
                                            <span></span>
                                        </label>
                                    </div>

                                    <div class="kt-checkbox-list" style="padding-right: 14px">
                                        <label class="kt-checkbox">
                                            <input name="col_total_price" type="checkbox"> Total Service Charge
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_invoice_number" type="checkbox"> Invoice Number
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_tran_date" type="checkbox"> Invoice Date
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_pf_amount" type="checkbox"> Other Charge
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_invoice_total" type="checkbox"> Invoice Total
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_payment_status" type="checkbox"> Payment Status
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_created_by" type="checkbox"> Employee
                                            <span></span>
                                        </label>

                                        <label class="kt-checkbox">
                                            <input name="col_employee_name" type="checkbox"> Employee Name
                                            <span></span>
                                        </label>


                                    </div>

<!--                                    <div class="kt-checkbox-list" style="padding-right: 14px">-->
<!---->
<!--                                        <label class="kt-checkbox">-->
<!--                                            <input name="col_pf_amount" type="checkbox"> Other Charge-->
<!--                                            <span></span>-->
<!--                                        </label>-->
<!---->
<!--                                    </div>-->

                                </div>


                            </div>
                            <div class="tab-pane" id="kt_tabs_1_3" role="tabpanel" style="margin-left: 10px">


                                <div class="form-group row">
                                    <div class="kt-checkbox-list" style="padding-right: 14px">
                                        <label class="kt-checkbox">
                                            <input name="filter_invoice_number" type="checkbox"> Invoice No
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="filter_salesman" type="checkbox"> SalesMan
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="filter_service" type="checkbox"> Service Name
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="filter_customer_mobile" type="checkbox"> Customer Mobile
                                            <span></span>
                                        </label>
                                    </div>


                                    <div class="kt-checkbox-list" style="padding-right: 14px">
                                        <label class="kt-checkbox">
                                            <input name="filter_payment_status" type="checkbox"> Payment Status
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="filter_customer" type="checkbox"> Customer
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="filter_display_customer" type="checkbox"> Display Customer
                                            <span></span>
                                        </label>
                                    </div>


                                    <div class="kt-checkbox-list" style="padding-right: 14px">
                                        <label class="kt-checkbox">
                                            <input name="filter_date_from" type="checkbox"> Date From
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="filter_date_to" type="checkbox"> Date To
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="filter_transaction_id" type="checkbox"> Transaction ID
                                            <span></span>
                                        </label>
                                    </div>


                                    <div class="kt-checkbox-list" style="padding-right: 14px">
                                        <label class="kt-checkbox">
                                            <input name="filter_category" type="checkbox"> Category
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="filter_employee" type="checkbox"> Employee
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="filter_transaction_status" type="checkbox"> Transaction Status
                                            <span></span>
                                        </label>
                                    </div>


                                    <div class="kt-checkbox-list" style="padding-right: 14px">
                                        <label class="kt-checkbox">
                                            <input name="filter_invoice_type" type="checkbox"> Card Type
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="filter_payment_status" type="checkbox"> Payment Status
                                            <span></span>
                                        </label>
                                        <label class="kt-checkbox">
                                            <input name="filter_customer_email" type="checkbox"> Customer Email
                                            <span></span>
                                        </label>
                                    </div>


                                </div>


                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="custom_report_new_btn" class="btn btn-success" data-action="save_as" onclick="SaveCustomReport(this)">Save As New Report
                    <button type="button" id="custom_report_update_btn" class="btn btn-warning" data-action="save" onclick="SaveCustomReport(this)">Update Current Report
                    </button>
            </div>
        </div>
    </div>
</div>


<style>
    .select2.select2-container {
        width: 100% !important;
    }

</style>
