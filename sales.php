<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <!-- begin:: Content Head -->


            <style>

            </style>


<!--            --><?php //var_dump($_SESSION['wa_current_user']->role_set); die; ?>


            <!-- end:: Content Head -->

            <!-- begin:: Content -->
            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <!--Begin::Dashboard 2-->

                <!--Begin::Row-->


                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('INVOICE') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="row" style="border: 1px solid #ccc;
    padding: 18px;background: #fff9ec">

<!--                    --><?//= createMenuTile('SA_SALESORDER',trans('Direct Invoice'),
//                        trans('Immigration and other invoices'),'ERP/sales/sales_order_entry.php?NewInvoice=0','fa-print') ?>

                    <?php

                    $curr_user = get_user($_SESSION["wa_current_user"]->user);

                    $user_dim = $curr_user['dflt_dimension_id'];

                    $dim_info = get_dimension($user_dim);
					
					
                    if(in_array($_SESSION['wa_current_user']->access,[44,11]))
                    {
                        $user_dim=DT_TYPING;
                    }


                    ?>


                    <?php if(!empty($dim_info['has_service_request'])): ?>

                        <?= createMenuTile('SA_SERVICE_REQUEST',trans('NEW SERVICE REQUEST'),
                            trans('Pre-Invoice / Service Request'),'new_service_request.php','fa-print','') ?>

                    <?php endif?>

<!--                    --><?//= createMenuTile(
//                        'SA_SALESINVOICE',
//                        trans('TYPING'),
//                        trans('Invoice'),
//                        'ERP/sales/sales_order_entry.php?NewInvoice=0&dim_id=' . DT_TYPING,
//                        'fa-print',
//                        '',
//                        'ybc_logo.png',
//                        $user_dim!=DT_TYPING
//                    ) ?>
<!---->
<!--                    --><?//= createMenuTile(
//                        'SA_SALESINVOICE',
//                        trans('AMER - CBD'),
//                        trans('Invoice'),
//                        'ERP/sales/sales_order_entry.php?NewInvoice=0&dim_id=' . DT_AMER_CBD,
//                        'fa-print',
//                        '',
//                        'amer_logo.jpg',
//                        $user_dim!=DT_AMER_CBD
//                    ) ?>

<!--                    --><?//= createMenuTile(
//                        'SA_SALESINVOICE',
//                        trans('YBC'),
//                        trans('Invoice'),
//                        'ERP/sales/sales_order_entry.php?NewInvoice=0&dim_id=' . DT_YBC,
//                        'fa-print',
//                        '',
//                        'ybc_logo.png',
//                        $user_dim!= DT_YBC
//                    ) ?>
<!---->
<!--                    --><?//= createMenuTile(
//                        'SA_SALESINVOICE',
//                        trans('RTA'),
//                        trans('Invoice'),
//                        'ERP/sales/sales_order_entry.php?NewInvoice=0&dim_id=' . DT_RTA,
//                        'fa-print',
//                        '',
//                        'rta_logo.png',
//                        $user_dim!=DT_RTA
//                    ) ?>

                    <?= createMenuTile(
                        'SA_SALESINVOICE',
                        trans('AMER'),
                        trans('Invoice'),
                        'ERP/sales/sales_order_entry.php?NewInvoice=0&dim_id=' . DT_AMER,
                        'fa-print',
                        '',
                        'amer_logo.jpg',
                        $user_dim!= DT_AMER
                    ) ?>

<!--                    --><?//= createMenuTile(
//                        'SA_SALESINVOICE',
//                        trans('DHA'),
//                        trans('Invoice'),
//                        'ERP/sales/sales_order_entry.php?NewInvoice=0&dim_id=' . DT_DHA,
//                        'fa-print',
//                        '',
//                        'dha_logo.png',
//                        $user_dim!= DT_DHA
//                    ) ?>
<!---->
<!--                    --><?//= createMenuTile(
//                        'SA_SALESINVOICE',
//                        trans('DUBAI COURT'),
//                        trans('Invoice'),
//                        'ERP/sales/sales_order_entry.php?NewInvoice=0&dim_id=' . DT_DUBAI_COURT,
//                        'fa-print',
//                        '',
//                        'dubai_court_logo.jpg',
//                        $user_dim!= DT_DUBAI_COURT
//                    ) ?>

                    <?= createMenuTile(
                        'SA_SALESINVOICE',
                        trans('TAS-HEEL'),
                        trans('Invoice'),
                        'ERP/sales-tasheel/sales_order_entry.php?NewInvoice=0&is_tadbeer=1&show_items=ts&dim_id=' . DT_TASHEEL,
                        'fa-print',
                        '',
                        'tasheel_logo.JPG',
                        $user_dim!=DT_TASHEEL
                    ) ?>

<!--                    --><?//= createMenuTile(
//                        'SA_SALESINVOICE',
//                        trans('Economic Department'),
//                        trans('Invoice'),
//                        'ERP/sales/sales_order_entry.php?NewInvoice=0&dim_id=' . DT_DED,
//                        'fa-print',
//                        '',
//                        'ded_logo.png',
//                        $user_dim!=DT_DED
//                    ) ?>

<!--                    --><?//= createMenuTile(
//                        'SA_SALESINVOICE',
//                        trans('AL ADHEED'),
//                        trans('Invoice'),
//                        'ERP/sales/sales_order_entry.php?NewInvoice=0&dim_id=' . DT_ADHEED,
//                        'fa-print',
//                        '',
//                        'al_adheed.png',
//                        $user_dim!=DT_ADHEED
//                    ) ?>
<!--                    -->
<!--                    --><?//= createMenuTile(
//                        'SA_SALESINVOICE',
//                        trans('AL ADHEED OTHERS'),
//                        trans('Invoice'),
//                        'ERP/sales/sales_order_entry.php?NewInvoice=0&dim_id=' . DT_ADHEED_OTH,
//                        'fa-print',
//                        '',
//                        'al_adheed.png',
//                        $user_dim!=DT_ADHEED_OTH
//                    ) ?>

                </div>



                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('MANAGE') ?></h3>
                        </div>
                    </div>
                </div>



                <div class="row">

                    <?= createMenuTile(
                        'SA_RECEPTION_REPORT',
                        'Reception report',
                        'Customer reception list',
                        route('reception_report'),
                        'fa-clipboard-list'
                    ) ?>

                    <?= createMenuTile(
                        'SA_RECEPTION',
                        trans('RECEPTION'),
                        trans('Reception'),
                        'reception.php',
                        'fa-money-bill'
                    ) ?>


                    <?= createMenuTile(
                        'SA_SRVREQLI',
                        trans('SERVICE REQUEST LIST'),
                        trans('SERVICE REQUEST LIST'),
                        'service_request_list.php',
                        'fa-money-bill'
                    ) ?>

                    <?= createMenuTile(
                        'SA_SALESPAYMNT',
                        trans('Cashier Dashboard'),
                        trans('Cashier Dashboard'),
                        'index.php?dashboard=cashier',
                        'fa-money-bill'
                    ) ?>

<!--                    --><?//= createMenuTile(
//                        'SA_SALESALLOC',
//                        trans('Allocate Customer Payments'),
//                        trans('Allocate Customer Payments or Credit Notes'),
//                        route('allocate_cust_pmts_n_cr_notes'),
//                        'fa-map-signs'
//                    ); ?>

                    <?= createMenuTile(
                        'SA_MANAGEINV',
                        trans('Manage Invoices'),
                        trans('Edit and Manage Invoices'),
                        route('manage_invoice'),
                        'fa-info'
                    ) ?>

                    <?= createMenuTile(
                        [
                            'SA_CASH_HANDOVER',
                            'SA_CASH_HANDOVER_ALL'
                        ], 
                        trans('Cash Handover Request'),
                        trans('Cash Handover Request'),
                        'cash_handover_request.php',
                        'fa-money-bill'
                    ) ?>

                    <?= createMenuTile(
                        'SA_CASH_HANDOVER_LIST',
                        trans('Cash Handover Request List'),
                        trans('Cash Handover Request List'), 
                        'cash_handover_request_list.php', 
                        'fa-clipboard-list'
                    ) ?>

                    <?= createMenuTile(
                        'SA_CUSTRCPTVCHR', 
                        trans('Cust. reciept voucher'), 
                        trans('Reciept voucher for customer'),
                        route('cust_rcpt_vchr'),
                        'fa-receipt'
                    ) ?>

                    <?= createMenuTile(
                        'SA_SALESALLOC',
                        trans('Advance Allocation'),
                        trans('Payment allocation Enquiry'),
                        route('allocation_inquiry'),
                        'fa-wallet'
                    ) ?>

<!--                    --><?//= createMenuTile(
//                        'SA_SALESPAYMNT',
//                        trans('Receipts'),
//                        trans('Customer Receipts'),
//                        route('customer_payment'),
//                        'fa-money-bill'
//                    ) ?>

                    <?= createMenuTile(
                        'SA_CUSTOMER',
                        trans('Customers'),
                        trans('Manage Customers'),
                        route('customers'),
                        'fa-users-cog'
                    ) ?>

                    <?= createMenuTile(
                        'SA_SALESMAN',
                        trans('SalesMan'),
                        trans('Manage SalesMan'),
                        route('sales_person'),
                        'fa-user-tie'
                    ) ?>


                    <?= createMenuTile(
                        'SA_VIEWPRINTTRANSACTION',
                        trans('Print or View'),
                        trans('View or Print Transactions'),
                        route('view_print_trans'),
                        'fa-print'
                    ) ?>

<!--                    --><?//= createMenuTile(
//                        'SA_CUSTPAYMREP',
//                        trans('Customer Balance'),
//                        trans('Customer Balance Report'),
//                        route('rep_customer_balance'),
//                        'fa-balance-scale'
//                    ) ?>
<!---->
<!--                    --><?//= createMenuTile(
//                        'SA_CUSTPAYMREP',
//                        trans('Aged Customer Analysis'),
//                        trans('Aged Customer Analysis'),
//                        'rep_customer_aged.php',
//                        'fa-balance-scale'
//                    ) ?>
                  
<!--                    --><?//= createMenuTile(
//                        [
//                            'SA_EMPANALYTIC',
//                            'SA_EMPANALYTICDEP',
//                            'SA_EMPANALYTICALL'
//                        ],
//                        trans('Sales Report'),
//                        trans('Employee wise sales report'),
//                        route('employee_wise_sales'),
//                        'fa-info'
//                    ) ?>

                    <?= createMenuTile(
                        'SA_ITEM',
                        trans('Service List'),
                        trans('Add and Manage Services'),
                        route('service_list'),
                        'fa-people-carry'
                    ) ?>

                    <?= createMenuTile(
                        'SA_ITEM',
                        trans('Add New Service'),
                        trans('Add and Manage Items'),
                        'items.php?action=new',
                        'fa-people-carry'
                    ) ?>
                </div>
            </div>

        </div>
    </div>
</div>
