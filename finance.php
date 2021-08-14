<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <!-- begin:: Content Head -->


            <style>
                .kt-iconbox {
                    padding: 8px !important;
                }
            </style>

            <!-- end:: Content Head -->

            <!-- begin:: Content -->
            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <!--Begin::Dashboard 2-->

                <!--Begin::Row-->


                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('JOURNALS') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="row">


                    <?= createMenuTile('SA_JOURNALENTRY',trans('Journal Entry'),
                        trans('Normal Journal Vouchers'),route('journal_entry'),'fa-print') ?>


                    <?= createMenuTile('SA_GLANALYTIC',trans('Journal Inquiry'),
                        trans('Manage Journals'),route('journal_inquiry'),'fa-info') ?>



                    <?= createMenuTile('SA_GLTRANSVIEW',trans('Ledger Inquiry'),
                        trans('Ledger Inquiry'),route('gl_inquiry'),'fa-info') ?>



                    <?= createMenuTile('SA_REFUND_TO_CUSTOMER',trans('REFUND TO CUSTOMER'),
                        trans('Refund to Customer'),'customer_refund.php','fa-money-bill-alt') ?>


                    <?= createMenuTile('SA_GLACCOUNT',trans('Chart Of Accounts'),
                        trans('Manage COA'),route('chart_of_accounts'),'fa-swatchbook') ?>


<!--                    --><?//= createMenuTile('SA_GLACCOUNT',trans('Manage Cost Centers'),
//                        trans('Manage Cost Center'),'ERP/dimensions/dimension_entry.php?','fa-swatchbook') ?>
<!---->
<!--                    --><?//= createMenuTile('SA_GLACCOUNT',trans('Cost Center List'),
//                        trans('Cost Center List'),'ERP/dimensions/inquiry/search_dimensions.php?','fa-swatchbook') ?>
<!---->
<!---->
<!---->
<!--                    --><?//= createMenuTile('SA_GLACCOUNT',trans('RTA Import'),
//                        trans('Import RTA Transactions'),'#','fas fa-file-upload','','rta_logo.png') ?>
<!---->
<!--                    --><?//= createMenuTile('SA_GLACCOUNT',trans('DHA Import'),
//                        trans('Import DHA Transactions'),'#','fas fa-file-upload','','dha_logo.png') ?>



                </div>




                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('VOUCHERS') ?></h3>
                        </div>
                    </div>
                </div>


                <div class="row">

                    <?= createMenuTile('SA_PAYMENT',trans('Payment Voucher'),
                        trans('Payment Voucher Entry'),'ERP/gl/gl_bank.php?NewPayment=Yes','fa-money-bill-alt') ?>

                    <?= createMenuTile('SA_DEPOSIT',trans('Receipts Voucher'),
                        trans('Receipt Voucher Entry'),'ERP/gl/gl_bank.php?NewDeposit=Yes','fa-money-bill-alt') ?>

                    <?= createMenuTile('SA_BANKTRANSFER',trans('Bank Transfer'),
                        trans('Bank to Bank Transfer'),route('bank_transfer'),'fa-money-bill-alt') ?>

                    <?= createMenuTile('SA_PRINT_REFUNDS',trans('Print Customer Refunds'),
                        trans('Customer Refunds'),'refund_list.php','fa-money-bill-alt') ?>

<!--                    --><?//= createMenuTile('SA_BANKTRANSFER',trans('E DIRHAM Recharge'),
//                        trans('E Dirham Recharge Entry'),route('edirham_recharge'),'fa-plug') ?>



                </div>





                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('RECONCILIATION') ?></h3>
                        </div>
                    </div>
                </div>



                <div class="row">


                    <?= createMenuTile('SA_BANKACCOUNT',trans('Bank Accounts'),
                        trans('Add and Manage Bank Accounts'),route('bank_accounts'),'fa-lock') ?>

                    <?= createMenuTile('SA_RECONCILE',trans('Reconciliation'),
                        trans('Manually Reconcile Bank A/C'),route('manual_reconciliation'),'fa-check-double') ?>


                    <?= createMenuTile('SA_DENIED'/*'SA_RECONCILE'*/,trans('Auto Reconciliation'),
                        trans('Auto Bank Reconciliation by CSV'),route('auto_reconciliation'),'fa-check-double') ?>



                </div>





                <!--End::Row-->



                <!--End::Row-->

                <!--End::Dashboard 2-->
            </div>

            <!-- end:: Content -->
        </div>
    </div>
</div>