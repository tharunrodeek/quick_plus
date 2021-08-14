<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
            <div class="kt-subheader   kt-grid__item" id="kt_subheader">
                <div class="kt-container ">
                    <div class="kt-subheader__main">
                        <h3 class="kt-subheader__title"><?= trans('Dashboard') ?></h3>
                    </div>
                    <div class="kt-subheader__toolbar" style="display: none">
                        <div class="kt-subheader__wrapper">
                            <a href="#" class="btn kt-subheader__btn-daterange" id="kt_dashboard_daterangepicker" data-toggle="kt-tooltip" title="Select dashboard daterange" data-placement="left">
                                <span class="kt-subheader__btn-daterange-title" id="kt_dashboard_daterangepicker_title">Today</span>&nbsp;
                                <span class="kt-subheader__btn-daterange-date" id="kt_dashboard_daterangepicker_date">Aug 16</span>
                                <i class="flaticon2-calendar-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <?php if (canAccessAny([
                    'SA_DSH_LAST_10_DAYS', 
                    'SA_DSH_TOP_5_EMP', 
                    'SA_DSH_TOP_5'
                ])): ?>
                    <div class="row">
                        <?php if (user_check_access('SA_DSH_LAST_10_DAYS')) : ?>
                        <div class="col-xl-4 col-lg-4">
                            <div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid">
                                <div class="kt-portlet__head kt-portlet__space-x">
                                    <div class="kt-portlet__head-label">
                                        <h3 class="kt-portlet__head-title">
                                            <?= trans('Daily Sales - Last 10 Days') ?>
                                        </h3>
                                    </div>
                                </div>
                                <div class="kt-portlet__body kt-portlet--height-fluid">
                                    <div class="kt-widget20">
                                        <div class="kt-widget20__content kt-portlet__space-x">
                                            <span class="kt-widget20__number kt-font-brand" style="display: none;">670+</span>
                                            <span class="kt-widget20__desc" style="display: none;"><?= trans('Successful transactions') ?></span>
                                        </div>
                                        <div class="kt-widget20__chart" style="height:120px;">
                                            <canvas id="kt_chart_bandwidth1"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (user_check_access('SA_DSH_TOP_5_EMP')): ?>
                        <div class="col-xl-4 col-lg-4">
                            <!--begin:: Widgets/Profit Share-->
                            <div class="kt-portlet kt-portlet--height-fluid">
                                <div class="kt-widget14">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <input type="text"   name="ap-datepicker" class="form-control ap-datepicker fromdate"
                                                   placeholder="FROM DATE"  autocomplete="off" value="<?php echo Today(); ?>" >
                                        </div>

                                        <div class="col-sm-4">
                                            <input type="text"   name="ap-datepicker" class="form-control ap-datepicker todate"
                                                   placeholder="TO DATE"  autocomplete="off" value="<?php echo Today(); ?>" >
                                        </div>
                                        <div class="col-sm-4">

                                            <button type="button"  class="btn btn-success ClsTopEmployeeServce"><?= trans('View') ?></button>
                                        </div>
                                    </div>
                                    <div class="kt-widget14__header">
                                        <h3 class="kt-widget14__title">
                                            <?= trans('TOP 5 Employee Service Count') ?>
                                        </h3>
                                        <span class="kt-widget14__desc">
                                            <?= trans('No.of services done by employees - top 5') ?>
                                        </span>
                                    </div>
                                    <div class="kt-widget14__content">
                                        <div class="kt-widget14__chart">
                                            <div class="kt-widget14__stat employee_serv_count_avg" style="font-size: 14px">Avg : 45</div>
                                            <canvas id="kt_chart_profit_share" style="height: 140px; width: 140px;"></canvas>
                                        </div>
                                        <div class="kt-widget14__legends employee_serv_count_brief">

                                            <div class="kt-widget14__legend">
                                                <span class="kt-widget14__bullet kt-bg-warning"></span>
                                                <span class="kt-widget14__stats">47% Business Events</span>
                                            </div>
                                            <div class="kt-widget14__legend">
                                                <span class="kt-widget14__bullet kt-bg-brand"></span>
                                                <span class="kt-widget14__stats">19% Others</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end:: Widgets/Profit Share-->
                        </div>
                        <?php endif; ?>

                        <?php if (user_check_access('SA_DSH_TOP_5')): ?>
                        <div class="col-xl-4 col-lg-4">
                            <div class="kt-portlet kt-portlet--height-fluid">
                                <div class="kt-widget14">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <input type="text"   name="ap-datepicker" class="form-control ap-datepicker fdate"
                                                   placeholder="FROM DATE"  autocomplete="off" value="<?php echo Today(); ?>">
                                        </div>

                                        <div class="col-sm-4">
                                            <input type="text"   name="ap-datepicker" class="form-control ap-datepicker tdate"
                                                   placeholder="TO DATE"  autocomplete="off" value="<?php echo Today(); ?>">
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="button"   name="submit" class="btn btn-success ClsBtnCategoryCnt" value="View">
                                        </div>
                                    </div>
                                    <div class="kt-widget14__header">
                                        <h3 class="kt-widget14__title">
                                            <?= trans('TOP 5 Sales Category Count') ?>
                                        </h3>
                                        <span class="kt-widget14__desc">
                                            <?= trans('TOP 5 daily sales category count') ?>
                                        </span>
                                    </div>
                                    <div class="kt-widget14__content">
                                        <div class="kt-widget14__chart">
                                            <div id="kt_chart_revenue_change" style="height: 150px; width: 150px;"></div>
                                        </div>
                                        <div class="kt-widget14__legends category_sales_count_brief"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (user_check_access('SA_DSH_FIND_INV')): ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="kt-portlet kt-portlet--height-fluid ">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <?= trans('FIND INVOICE') ?>
                                    </h3>
                                </div>
                            </div>

                            <div class="kt-portlet__body kt-portlet__body--fluid kt-portlet__body--fit">
                                <div class="kt-widget4 kt-widget4--sticky">
                                    <div class="kt-widget4__items kt-portlet__space-x kt-margin-t-15">
                                        <div class="form-group">
                                            <input type="text" id="txt_print_invoice_number" class="form-control" placeholder="<?= trans("Enter invoice number") ?>">
                                        </div>
                                        <div class="kt-">
                                            <div class="kt-form__actions">
                                                <button type="button" data-method="print"  class="btn btn-primary btn-find-invoice"><?= trans('Print Invoice') ?></button>
                                                <button type="button" data-method="edit"  class="btn btn-success btn-find-invoice"><?= trans('Update Transaction ID') ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (user_check_access('SA_DSH_TODAYS_INV')): ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="kt-portlet kt-portlet--mobile ">
                            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <?= trans("TODAYS'S INVOICES") ?>
                                    </h3>
                                </div>

                            </div>
                            <div class="kt-portlet__body kt-portlet__body--fit">
                                <div class="" id="kt_datatable_todays_invoices"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (user_check_access('SA_DSH_HRM')) : include('hrm_dashboard.php'); endif; ?>

                <?php if (user_check_access('SA_DSH_CAT_REP')): ?>
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                            <div class="kt-widget4 kt-widget4--sticky">
                                <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                                    <div class="kt-portlet__head-label">
                                        <h3 class="kt-portlet__head-title">
                                            <?= trans('CATEGORY REPORT - TODAY') ?>
                                        </h3>
                                    </div>
                                </div>
                                <div class="kt-portlet__body kt-portlet__body--fit">
                                    <div class="" id="kt_datatable_category_sales_report"></div>
                                </div>
                                <!-- <div class="kt-widget4__chart kt-margin-t-15">
                                    <canvas id="kt_chart_category_wise_sales_count"></canvas>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (user_check_access('SA_DSH_TOP_10_CUST')): ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <?= trans("TOP 10 CUSTOMERS") ?>
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body kt-portlet__body--fit">
                                <div class="kt-widget4__items kt-portlet__space-x kt-margin-t-15">
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label"><?= trans('Category') ?>:</label>
                                        <div class="col-lg-2">
                                            <select 
                                                class="form-control kt-select2 ap-select2 tptc_filter"
                                                name="topf_cat_id" 
                                                id="topf_cat_id">
                                                <?= prepareSelectOptions(
                                                    $api->get_records_from_table(
                                                        '0_stock_category',
                                                        ['category_id', 'description']
                                                    ), 
                                                    'category_id', 
                                                    'description'
                                                ) ?>
                                            </select>
                                        </div>

                                        <label class="col-lg-2 col-form-label"><?= trans('Date From') ?>:</label>
                                        <div class="col-lg-2">
                                            <input 
                                                type="text" 
                                                name="topf_from_date" 
                                                id="topf_from_date"
                                                class="form-control ap-datepicker tptc_filter"
                                                readonly 
                                                placeholder="Select date"
                                                value="<?= add_days(Today(),-30) ?>">
                                        </div>

                                        <label class="col-lg-2 col-form-label"><?= trans('Date To') ?>:</label>
                                        <div class="col-lg-2">
                                            <input 
                                                type="text" 
                                                name="topf_to_date" 
                                                id="topf_to_date" 
                                                class="form-control ap-datepicker tptc_filter"
                                                readonly 
                                                placeholder="Select date" 
                                                value="<?= Today() ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="" id="tbl_dboard_top_ten_customers" style="padding-left: 12px">
                                    <table class="table table-responsive" style="display: table !important; text-align:  left">
                                        <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>Service Count</th>
                                        </tr>
                                        </thead>

                                        <tbody id="tbl_dboard_top_ten_customers_tbody">
                                            <!-- generated through javascript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row">
                <?php if(canAccessAny([
                    'SA_DSH_TRANS',
                    'SA_DSH_TRANS_ACC',
                    'SA_DSH_BNK_AC',
                    'SA_DSH_COLL_BD',
                    'SA_DHS_CUST_BAL'
                ])):
                    $filter_date = Today();
                    if(isset($_GET['filter_date']) && !empty($_GET['filter_date'])) {
                        $filter_date = $_GET['filter_date'];
                    }
                    $dec = user_price_dec();
                ?>
                    <div class="col-12">
                        <div class="row pt-5 pb-4 px-3">
                            <div class="col-lg-4">
                                <div class="form-group form-group-sm row">
                                    <label class="col col-form-label col-form-label-sm"><?= trans('Date') ?>:</label>
                                    <div class="col">
                                        <input 
                                            type="text" 
                                            name="inp_manager_report_date" 
                                            id="inp_manager_report_date" 
                                            class="form-control form-control-sm ap-datepicker tptc_filter"
                                            readonly 
                                            placeholder="Select date" 
                                            value="<?= $filter_date ?>" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-sm btn-primary" id="btn_load_manager_report">GET REPORT</button>
                            </div>
                        </div>
                    </div>

                    <?php if (user_check_access('SA_DSH_TRANS')): ?>
                    <div class="col-lg-12">
                        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <?= trans("Today Transactions معاملات اليوم") ?>
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body kt-portlet__body--fit">
                                <div class="" id="tbl_dboard_daily_trans" style="padding-left: 12px;overflow: auto;">
                                    <table class="table table-responsive" style="display: table !important; text-align:  left">
                                        <thead>
                                        <tr>
                                            <th>Department  <br>الادارة</th>
                                            <th>No. of Trans.<br> عدد المعاملات</th>
                                            <th>Gov. Fees <br>المصاريف الحكومية</th>
                                            <th>YBC Service Charge <br> قيمة خدمات المركز</th>
                                            <th>Credit Facility<br>  دفع أجل</th>
                                            <th>Discount<br> خصم</th>
                                            <th>VAT<br> الضريبة</th>
                                            <th>Total Collection<br> اجمالي المبلغ المتحصلة</th>
                                        </tr>
                                        </thead>
                                        <tbody id="tbl_dboard_daily_trans_tbody">
                                        <?php
                                            $report = $api->getDailySalesSummary(["START_DATE" => $filter_date,"END_DATE" => $filter_date],'array');

                                            $sum_total_service_count = 0;
                                            $sum_total_govt_fee = 0;
                                            $sum_total_service_charge = 0;
                                            $sum_total_credit_facility = 0;
                                            $sum_total_pro_discount = 0;
                                            $sum_total_tax = 0;
                                            $sum_total_collection_ = 0;

                                            foreach ($report as $row) {
                                                echo "<tr>";
                                                echo "<td>".$row['description']."</td>";
                                                echo "<td>".$row['total_service_count']."</td>";
                                                echo "<td>".$row['total_govt_fee']."</td>";
                                                echo "<td>".$row['total_service_charge']."</td>";
                                                echo "<td>".$row['total_credit_facility']."</td>";
                                                echo "<td>".$row['total_pro_discount']."</td>";
                                                echo "<td>".$row['total_tax']."</td>";
                                                echo "<td>".$row['total_collection']."</td>";
                                                echo  "</tr>";

                                                $sum_total_service_count += $row['total_service_count'];
                                                $sum_total_govt_fee += $row['total_govt_fee'];
                                                $sum_total_service_charge += $row['total_service_charge'];
                                                $sum_total_credit_facility += $row['total_credit_facility'];
                                                $sum_total_pro_discount += $row['total_pro_discount'];
                                                $sum_total_tax += $row['total_tax'];
                                                $sum_total_collection_ += $row['total_collection'];
                                            }

                                            echo "<tr style='font-weight: bold'>";
                                            echo "<td>TOTAL</td>";
                                            echo "<td>$sum_total_service_count</td>";
                                            echo "<td>$sum_total_govt_fee</td>";
                                            echo "<td>$sum_total_service_charge</td>";
                                            echo "<td>$sum_total_credit_facility</td>";
                                            echo "<td>$sum_total_pro_discount</td>";
                                            echo "<td>$sum_total_tax</td>";
                                            echo "<td>$sum_total_collection_</td>";
                                            echo "</tr>";
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (user_check_access('SA_DSH_TRANS_ACC')): ?>
                    <div class="col-lg-12">
                        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <?php
                                            $date = $filter_date;
                                            $phpdate = strtotime( date2sql($date) );
                                            $mysqldate = date( 'Y-m-d H:i:s', $phpdate );
                                            $month_name = date("F", strtotime($mysqldate));
                                        ?>
                                        <?= trans("Accumulated Transactions - $month_name - "." اجمالي المعاملات") ?>
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body kt-portlet__body--fit">
                                <div class="" id="tbl_dboard_accumulated_trans" style="padding-left: 12px;overflow: auto;">
                                    <table class="table table-responsive" style="display: table !important; text-align:  center">
                                        <thead>
                                        <tr>
                                            <th>Department  <br>الادارة</th>
                                            <th>No. of Trans.<br> عدد المعاملات</th>
                                            <th>YBC Service Charge <br> قيمة خدمات المركز</th>
                                            <th>Total Collection<br> اجمالي المبلغ المتحصلة</th>
                                            <th>Credit Facility<br>  دفع أجل</th>
                                        </tr>
                                        </thead>

                                        <tbody id="tbl_dboard_accumulated_trans_tbody">
                                        <?php
                                            $first_day_of_month =  date('Y-m-01', $phpdate);
                                            $last_day_of_month =  date('Y-m-t', $phpdate);

                                            $filters = [
                                                'START_DATE' => sql2date($first_day_of_month),
                                                'END_DATE' => sql2date($last_day_of_month)
                                            ];

                                            $report = $api->getDailySalesSummary($filters, 'array');

                                            $sum_total_service_count = 0;
                                            $sum_total_service_charge = 0;
                                            $sum_total_credit_facility = 0;
                                            $sum_total_collection = 0;

                                            foreach ($report as $row) {
                                                echo "<tr>";
                                                echo "<td>".$row['description']."</td>";
                                                echo "<td>".$row['total_service_count']."</td>";
                                                echo "<td>".$row['total_service_charge']."</td>";
                                                echo "<td>".$row['total_collection']."</td>";
                                                echo "<td>".$row['total_credit_facility']."</td>";
                                                echo  "</tr>";

                                                $sum_total_service_count += $row['total_service_count'];
                                                $sum_total_service_charge += $row['total_service_charge'];
                                                $sum_total_credit_facility += $row['total_credit_facility'];
                                                $sum_total_collection += $row['total_collection'];
                                            }

                                            echo "<tr style='font-weight: bold'>";
                                            echo "<td>TOTAL</td>";
                                            echo "<td>$sum_total_service_count</td>";
                                            echo "<td>$sum_total_service_charge</td>";
                                            echo "<td>$sum_total_collection</td>";
                                            echo "<td>$sum_total_credit_facility</td>";
                                            echo "</tr>";
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (user_check_access('SA_DSH_BNK_AC')): ?>
                    <div class="col-lg-12">
                        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <?= trans("Bank Accounts  حسابات البنوك") ?>
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body kt-portlet__body--fit">
                                <div class="" id="tbl_dboard_bank_report" style="padding-left: 12px;overflow: auto;">
                                    <table class="table table-responsive" style="display: table !important; text-align:  left">
                                        <thead>
                                            <tr>
                                                <th>Account Name<br> اسماء الحسابات   </th>
                                                <th>Today Opening Balance<br> الرصيد الافتتاحي اليوم</th>
                                                <th>Today Deposits<br>  الايداعات اليوم</th>
                                                <th>Today Transactions<br> معاملات اليوم</th>
                                                <th>Available Balance<br>  الرصيد المتوفر</th>
                                            </tr>
                                        </thead>

                                        <tbody id="tbl_dboard_bank_report_tbody">
                                        <?php
                                            $filters = [
                                                'START_DATE' => $filter_date,
                                                'END_DATE' => $filter_date
                                            ];

                                            $report = $api->getBankBalanceReport($filters, 'array');

                                            $sum_opening_bal = 0;
                                            $sum_deposits_total = 0;
                                            $sum_transaction_total = 0;
                                            $sum_balance_total = 0;

                                            foreach ($report as $row) {

                                                echo "<tr>";
                                                echo "<td>".$row['account_name']."</td>";
                                                echo "<td>".number_format2($row['opening_bal'], $dec)."</td>";
                                                echo "<td>".number_format2($row['debit'], $dec)."</td>";
                                                echo "<td>".number_format2($row['credit'], $dec)."</td>";
                                                echo "<td>".number_format2($row['balance'], $dec)."</td>";
                                                echo  "</tr>";

                                                $sum_opening_bal += $row['opening_bal'];
                                                $sum_deposits_total += $row['debit'];
                                                $sum_transaction_total += $row['credit'];
                                                $sum_balance_total += $row['balance'];

                                            }

                                            echo "<tr style='font-weight: bold'>";
                                            echo "<td>TOTAL</td>";
                                            echo "<td>".number_format2($sum_opening_bal, $dec)."</td>";
                                            echo "<td>".number_format2($sum_deposits_total, $dec)."</td>";
                                            echo "<td>".number_format2($sum_transaction_total, $dec)."</td>";
                                            echo "<td>".number_format2($sum_balance_total, $dec)."</td>";
                                            echo "</tr>";
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif ?> 

                    <?php if(user_check_access('SA_DHS_CUST_BAL')): 
                        if (isset($filter_date)) {
                            $userDateFormat = getDateFormatInNativeFormat();
                            $dt = DateTime::createFromFormat($userDateFormat, $filter_date);
                            $dt = ($dt && $dt->format($userDateFormat) === $filter_date) ? $dt->format(MYSQL_DATE_FORMAT) : null; 
                        }

                        $today = empty($dt) ? date(MYSQL_DATE_FORMAT) : $dt;

                        $data = [];

                        $sum = [
                            "opening_bal"   => 0.0,
                            "prepaid"       => 0.0,
                            "pending"       => 0.0,
                            "closing_bal"   => 0.0
                        ];

                        /** Read the opening balances of customers */
                        $sql_op_bal = get_sql_for_opening_balance_of_customer_balance_inquiry(null, $today);
                        $sql_op_bal .= " HAVING SUM(t2.pending) - SUM(t2.prepaid) > 0.0004 ORDER BY SUM(t2.pending) - SUM(t2.prepaid) DESC";
                        $mysqli_result = db_query($sql_op_bal);
                        while($row = $mysqli_result->fetch_assoc()) {
                            $_id = $row['debtor_no'];
                            $data[$_id] = array_merge($row, [
                                'prepaid' => 0.00,
                                'pending' => 0.00,
                                'closing_bal' => $row['opening_bal']
                            ]);
                        }

                        $mysqli_result = db_query(get_sql_for_customers_balance_inquiry(null, $today, $today));
                        while($row = $mysqli_result->fetch_assoc()){
                            $_id = $row['debtor_no'];
                            if(!isset($data[$_id])) {
                                /**
                                 * If inside here,
                                 * It means customer did'nt had any pending but they had transactions today.
                                 * If we want to show these transactions,
                                 * then we would have to handle also: advance payments from all customers
                                 * so skiping these type of customer transactions
                                 */
                                continue;
                            }
                            $row['closing_bal'] = $data[$_id]['opening_bal'] + $row['pending'] - $row['prepaid'];

                            $data[$_id] = array_merge($data[$_id], $row);
                        }

                        foreach($data as $row){
                            $sum['opening_bal'] += $row['opening_bal'];
                            $sum['prepaid']     += $row['prepaid'];
                            $sum['pending']     += $row['pending'];
                            $sum['closing_bal'] += $row['closing_bal'];
                        }
                    ?>
                        <div class="col-lg-12">
                        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <?= trans("Customer Balances") ?> <span lang="ar">أرصدة العملاء</span>
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body kt-portlet__body--fit">
                                <div class="" id="tbl_dboard_bank_report" style="padding-left: 12px;overflow: auto;">
                                    <table class="table table-responsive table-striped" style="display: table !important; text-align:  left">
                                        <thead>
                                            <tr>
                                                <th>Customer Name<br><span lang="ar">اسم العميل</span></th>
                                                <th>
                                                    Today's Opening Balance<br>
                                                    <span lang="ar">الرصيد الافتتاحي اليوم</span><br>
                                                    <span class="small text-muted"><?= number_format2($sum['opening_bal'], $dec) ?></span>
                                                </th>
                                                <th>
                                                    Today's Payment<br>
                                                    <span lang="ar">مدفوعات اليوم</span><br>
                                                    <span class="small text-muted"><?= number_format2($sum['prepaid'], $dec) ?></span>
                                                </th>
                                                <th>
                                                    Today's Transactions<br>
                                                    <span lang="ar">معاملات اليوم</span><br>
                                                    <span class="small text-muted"><?= number_format2($sum['pending'], $dec) ?></span>
                                                </th>
                                                <th>
                                                    Balance<br>
                                                    <span lang="ar">الرصيد</span><br>
                                                    <span class="small text-muted"><?= number_format2($sum['closing_bal'], $dec) ?></span>
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody id="tbl_dboard_bank_report_tbody">
                                        <?php foreach($data as $debtor): ?>
                                            <tr>
                                                <td><?= $debtor['name'] ?></td>
                                                <td><?= number_format2($debtor['opening_bal'], $dec) ?></td>
                                                <td><?= number_format2($debtor['prepaid'], $dec) ?></td>
                                                <td><?= number_format2($debtor['pending'], $dec) ?></td>
                                                <td><?= number_format2($debtor['closing_bal'], $dec) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (user_check_access('SA_DSH_COLL_BD')): ?>
                    <div class="col-lg-6">
                        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <?= trans("Today Collection Breakdown تفاصيل التحصيل اليوم") ?>
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body kt-portlet__body--fit">
                                <div class="" id="tbl_dboard_coll_breakdown" style="padding-left: 12px;overflow: auto;">
                                    <table class="table table-responsive" style="display: table !important; text-align:  left">
                                        <thead>
                                        <tr>
                                            <th>Actual Collection التحصيل الفعلي</th>
                                            <th></th>
                                        </tr>
                                        </thead>

                                        <tbody id="tbl_dboard_coll_breakdown_tbody">
                                            <?php
                                                $filters = [
                                                    'START_DATE' => $filter_date,
                                                ];
                                                $report = $api->getCollectionBreakDownReport($filters, 'array');

                                                $cash = $report[0];
                                                $credit_card = $report[1];
                                                $bank_transfer = $report[2];
                                                $advance_rcvd_today = $report[3];
                                                $total_actual_collection = $report[4];
                                                $credit_invoices_today = $report[5];
                                                $credit_invoices_till_date = $report[6];

                                                $total_cash_collection = $cash['amount']+$advance_rcvd_today['amount'];
                                                $net = (
                                                    $credit_card['amount']
                                                    + $bank_transfer['amount']
                                                    - $credit_invoices_today['amount']
                                                );
                                            ?>

                                            <tr>
                                                <td><?= $cash['description'] ?></td>
                                                <td><?= number_format2($cash['amount'], $dec) ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $advance_rcvd_today['description'] ?></td>
                                                <td><?= number_format2($advance_rcvd_today['amount'], $dec) ?></td>
                                            </tr>

                                            <tr>
                                                <td><b>TOTAL CASH COLLECTION</b></td>
                                                <td><b><?= number_format2($total_cash_collection, $dec) ?></b></td>
                                            </tr>

                                            <tr>
                                                <td><?= $credit_card['description'] ?></td>
                                                <td><?= number_format2($credit_card['amount'], $dec) ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $bank_transfer['description'] ?></td>
                                                <td><?= number_format2($bank_transfer['amount'], $dec) ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $credit_invoices_today['description'] ?></td>
                                                <td><?= number_format2($credit_invoices_today['amount'], $dec) ?></td>
                                            </tr>

                                            <?php
                                                $credit_invoices_rcvd_today = (
                                                    ($sum_total_collection_ - abs($report[4]["amount"])) 
                                                    - $credit_invoices_today['amount']
                                                );
                                                // print_r($credit_invoices_rcvd_today); die;
                                            ?>

                                            <tr>
                                                <td>Credit Invoices Received Today  ناقصا فواتير اجلة تم استلام قيمتها اليوم</td>
                                                <td><?= number_format2($credit_invoices_rcvd_today, $dec) ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $advance_rcvd_today['description'] ?></td>
                                                <td><?= number_format2(0-$advance_rcvd_today['amount'], $dec) ?></td>
                                            </tr>

                                            <?php
                                                // print_r($total_actual_collection);
                                                $actual_net = (
                                                    $total_cash_collection
                                                    + $credit_card['amount']
                                                    + $bank_transfer['amount']
                                                    + $credit_invoices_today['amount']
                                                    - abs($credit_invoices_rcvd_today)
                                                    - $advance_rcvd_today['amount']
                                                );
                                            ?>

                                            <tr>
                                                <td><b>NET  الصافي</b></td>
                                                <td><b><?= number_format2($actual_net, $dec) ?></b></td>
                                            </tr>

                                            <tr>
                                                <td><i><?= $credit_invoices_till_date['description'] ?></i></td>
                                                <td><i><?= number_format2($credit_invoices_till_date['amount'], $dec) ?></i></td>
                                            </tr>

                                            <?php
                                                // $net = $sum_total_collection_-abs($report[4]["amount"]);
                                                // $push_extra = [
                                                //     'description' => "Credit Invoices Received Today  ناقصا فواتير اجلة تم استلام قيمتها اليوم",
                                                //     'amount' => $net-$report[5]["amount"],
                                                //     'flag' => true
                                                // ];
                                                // array_push($report,$push_extra);

                                                // $push_extra = [
                                                //     'description' => "Advance Received Today",
                                                //     'amount' => 0-$report[3]["amount"],
                                                //     'flag' => true
                                                // ];
                                                // array_push($report,$push_extra);

                                                // $push_extra = [
                                                //     'description' => "Net  الصافي",
                                                //     'amount' => 0-$net
                                                // ];
                                                // array_push($report,$push_extra); 

                                                // foreach ($report as $row) {
                                                //     $d_amt = $row['amount'];
                                                //     $c_amt = 0;
            
                                                //     if($d_amt < 0) {
                                                //         $c_amt = abs($d_amt);
                                                //         $d_amt = 0;
                                                //     }
            
                                                //     if(isset($row["flag"]) && $row["flag"]) {
                                                //         $d_amt = $row['amount'];
                                                //         $c_amt = 0;
                                                //     }
            
                                                //     echo "<tr>";
                                                //     echo "<td>".$row['description']."</td>";
                                                //     echo "<td>".number_format2($d_amt,2)."</td>";
                                                //     echo "<td>".number_format($c_amt,2)."</td>";
                                                //     echo  "</tr>";
                                                // }
                                            ?>
                                        </tbody>
                                    </table>


                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif ?> 
                <?php endif; ?>

                    <?php if (user_check_access('SA_DSH_AC_CLOSING_BAL')): ?>
                    <div class="col-md-6">
                        <div class="kt-portlet kt-portlet--height-fluid ">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <?= trans('ACCOUNT CLOSING BALANCES') ?>
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body kt-portlet__body--fluid kt-portlet__body--fit">
                                <div class="kt-widget4 kt-widget4--sticky">
                                    <div class="kt-widget4__items kt-portlet__space-x kt-margin-t-15" id="bank_balance_brief">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="col-md-6" style="display: none">
                        <div class="kt-portlet kt-portlet--height-fluid ">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <?= trans('Top 10 SERVICES') ?>
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body kt-portlet__body--fluid kt-portlet__body--fit">
                                <div class="kt-widget4 kt-widget4--sticky">

                                    <div class="kt-portlet__body kt-portlet__body--fit">
                                        <div class="" id="kt_datatable_top_ten_services"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>