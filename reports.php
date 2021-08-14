
<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <!-- begin:: Content Head -->


            <!-- end:: Content Head -->

            <!-- begin:: Content -->
            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <!--Begin::Dashboard 2-->

                <!--Begin::Row-->



                <div class="kt-subheader  kt-subheader-custom kt-grid__item" >
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('CUSTOM REPORTS') ?></h3>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <?= createMenuTile(
                        [
                            'SA_SRVREPORT',
                            'SA_SRVREPORTALL'
                        ],
                        trans('SERVICE REPORT'),
                        trans('Service Report'),
                        route('service_report'),
                        'fa-file'
                    ) ?>




               <?php if(canAccessAny([
                        'SA_SRVREPORT',
                        'SA_SRVREPORTALL'
                    ])): $custom_reports = $api->get_custom_reports(); ?>
                        <?php


                          $hide='';
                          foreach ($custom_reports as $row): ?>

                          <?php if(!in_array($_SESSION["wa_current_user"]->access,[3,2])){

                              if(in_array($row['id'],[12,13]))
                              {
                                 // $hide='style="display:none;"';
                              }

                          }


                          ?>

                            <div class="col-lg-3 <?= HideMenu('SA_SRVREPORT') ?>" >
                                <div class="kt-portlet kt-iconbox kt-iconbox--warning kt-iconbox--animate-fast">
                                    <i 
                                        class="flaticon2-rubbish-bin del-custom-rep"
                                        onclick="AxisPro.DeleteCustomReport(<?= $row['id'] ?>)"
                                        data-id="<?= $row['id'] ?>"
                                        style="position: absolute;right: 4px;top: 4px; cursor: pointer; z-index: 999;font-size: 20px">
                                    </i>
                                    <?php //endif; ?>
                                    <div class="kt-portlet__body">
                                        <div class="kt-iconbox__body">
                                            <div class="kt-iconbox__icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <path d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z" fill="#000000" opacity="0.3"/>
                                                        <path d="M12,11 C10.8954305,11 10,10.1045695 10,9 C10,7.8954305 10.8954305,7 12,7 C13.1045695,7 14,7.8954305 14,9 C14,10.1045695 13.1045695,11 12,11 Z" fill="#000000" opacity="0.3"/>
                                                        <path d="M7.00036205,16.4995035 C7.21569918,13.5165724 9.36772908,12 11.9907452,12 C14.6506758,12 16.8360465,13.4332455 16.9988413,16.5 C17.0053266,16.6221713 16.9988413,17 16.5815,17 C14.5228466,17 11.463736,17 7.4041679,17 C7.26484009,17 6.98863236,16.6619875 7.00036205,16.4995035 Z" fill="#000000" opacity="0.3"/>
                                                    </g>
                                                </svg>	</div>
                                            <div class="kt-iconbox__desc">
                                                <h3 class="kt-iconbox__title">
                                                    <a class="kt-link" href="<?= route('service_report')."&custom_report_id=".$row['id'] ?>"><?= $row['name'] ?></a>
                                                </h3>
                                                <div class="kt-iconbox__content">
                                                    <?= trans('Custom Report') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>

                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('SALES REPORTS') ?></h3>
                        </div>
                    </div>
                </div>


                 <div class="row">

                    <?= createMenuTile(
                        [
                            'SA_EMPANALYTIC',
                            'SA_EMPANALYTICDEP',
                            'SA_EMPANALYTICALL'
                        ],
                        trans('Employee Sales'),
                        trans('Employee category sales inquiry'),
                        route('employee_wise_sales'),
                        'fa-info'
                    ) ?>



                    <?= createMenuTile(
                        'SA_CUSTANALYTIC',
                        trans('Customer Sales'),
                        trans('Customer category sales inquiry'),
                        route('customer_wise_sales'),
                        'fa-info'
                    ) ?>    

                    <?= createMenuTile(
                        'SA_CUSTPAYMREP',
                        trans('Customer balance statement'),
                        trans('Customer\'s balance statement'),
                        route('cust_bal_statement'),
                        'fa-file-alt'
                    ) ?>

                    <?= createMenuTile(
                        'SA_SALESANALYTIC',
                        trans('Service wise report'),
                        trans('Service wise sales analysis'),
                        route('service_wise_sales'),
                        'fa-chart-bar')
                    ?>

<!--                    --><?//= createMenuTile(
//                        'SA_GLANALYTIC',
//                        trans('Overall collection report'),
//                        trans('Overall collection report'),
//                        route('overall_collection_report'),
//                        'fa-chart-line')
//                    ?>

                    <?= createMenuTile(
                        'SA_SALESANALYTIC',
                        trans('Category Sales'),
                        trans('Category-wise sales inquiry'),
                        route('category_wise_sales'),
                        'fa-info')
                    ?>

                    <?= createMenuTile(
                        'SA_SALESANALYTIC',
                        trans('Daily Report'),
                        trans('Daily report'),
                        route('daily_collection'),
                        'fa-chart-area'
                    ) ?>

                    <?= createMenuTile(
                        [
                            'SA_CSHCOLLECTREP',
                            'SA_CSHCOLLECTREPALL'
                        ],
                        trans('Collection Report'),
                        trans('Collection Report'),
                        route('invoice_collection'),
                        'fa-chart-bar'
                    ) ?>

<!--                    --><?//= createMenuTile(
//                        'SA_GLANALYTIC',
//                        trans('YBC Daily Sales Report'),
//                        trans('Daily sales report'),
//                        'daily_report.php',
//                        'fa-balance-scale'
//                    ) ?>

                    <?= createMenuTile(
                        'SA_CUSTPAYMREP',
                        trans('Customer Balance'),
                        trans('Customer Balance Report'),
                        route('rep_customer_balance'),
                        'fa-balance-scale')
                    ?>

                    <?= createMenuTile(
                        'SA_REP',
                        trans('Reports and Analysis'),
                        trans('Reports and Analysis'),
                        'ERP/reporting/reports_main.php?Class=6',
                        'fa-balance-scale'
                    ) ?>

                    <?= createMenuTile(
                        'SA_MGMTREP',
                        trans('Management Report'),
                        trans('Management Report'),
                        route('management_report'),
                        'fa-chart-bar',
                        "_blank"
                    ) ?>

                    <?= createMenuTile(
                        'SA_CUSTBULKREP',
                        trans('Customer Information Report'),
                        trans('Customer Information Report'),
                        'customers.php?action=list',
                        'fa-info'
                    ) ?>

                    <?= createMenuTile(
                        'SA_DENIED',
                        trans('Invoice Report'),
                        trans('Invoice Report'),
                        route('invoice_report'),
                        'fa-money-bill'
                    ) ?>

                    <?= createMenuTile(
                        'SA_CUSTANALYTIC',
                        trans('Customer Balance Inquiry'),
                        trans('Customer Balance Inquiry'),
                        route('customer_bal_inquiry'),
                        'fa-info'
                    ) ?>

                     <?= createMenuTile(
                         'SA_CUSTANALYTIC',
                         trans('Customer balance summary'),
                         trans('Customer\'s balance summary'),
                         'ERP/sales/inquiry/customer_balance_summary.php',
                         'fa-file-alt'
                     ) ?>

                </div>

                <div class="kt-subheader  kt-subheader-custom kt-grid__item" >
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('FINANCE REPORTS') ?></h3>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <?= createMenuTile(
                        'SA_GLANALYTIC',
                        trans('Profit & Loss - DrillDown'),
                        trans('Drill Down Report'),
                        route('drill_pl'),
                        'fa-balance-scale-left'
                    ) ?>

                    <?= createMenuTile(
                        'SA_GLREP',
                        trans('Ledger Report'),
                        trans('Ledger Transaction Report'),
                        route('rep_gl'),
                        'fa-list')
                    ?>

                    <?= createMenuTile(
                        'SA_GLANALYTIC',
                        trans('Trial Balance'),
                        trans('Trial Balance Report'),
                        route('rep_tb'),
                        'fa-balance-scale'
                    ) ?>

                    <?= createMenuTile(
                        'SA_GLANALYTIC',
                        trans('Profit & Loss'),
                        trans('Profit and loss report'),
                        route('rep_pl'),
                        'fa-wave-square'
                    ) ?>

                    <?= createMenuTile(
                        'SA_GLANALYTIC',
                        trans('Balance Sheet'),
                        trans('Balance Sheet'),
                        route('rep_bs'),
                        'fa-wave-square'
                    ) ?>
                </div>
            </div>

            <!-- end:: Content -->
        </div>
    </div>
</div>