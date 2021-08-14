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
                            <h3 class="kt-subheader__title"><?= trans('FIXED ASSET') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="row">


                    <?= createMenuTile('SA_SUPPLIERINVOICE',trans('Fixed Assets Purchase'),
                        trans('Fixed Assets Purchase'),'ERP/purchasing/po_entry_items.php?NewInvoice=Yes&FixedAsset=1',
                        'fa-people-carry') ?>


                    <?= createMenuTile('SA_ASSETTRANSFER',trans('Fixed Assets Location Transfers'),
                        trans('Fixed Assets Location Transfers'),'ERP/inventory/transfers.php?NewTransfer=1&FixedAsset=1','fa-people-carry') ?>


                    <?= createMenuTile('SA_ASSETDISPOSAL',trans('Fixed Assets Disposal'),
                        trans('Fixed Assets Disposal'),'ERP/inventory/adjustments.php?NewAdjustment=1&FixedAsset=1','fa-people-carry') ?>


                    <?= createMenuTile('SA_SALESINVOICE',trans('Fixed Assets Sale'),
                        trans('Fixed Assets Sale'),'ERP/sales/sales_order_entry.php?NewInvoice=0&FixedAsset=1','fa-boxes') ?>


                    <?= createMenuTile('SA_DEPRECIATION',trans('Process &Depreciation'),
                        trans('Process &Depreciation'),'ERP/sales/sales_order_entry.php?NewInvoice=0&FixedAsset=1','fa-boxes') ?>



                </div>


                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('MASTERS') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="row">


                    <?= createMenuTile('SA_ASSET',trans('Fixed Assets'),
                        trans('Fixed Assets'),'ERP/inventory/manage/items.php?FixedAsset=1',
                        'fa-people-carry') ?>


                    <?= createMenuTile('SA_INVENTORYLOCATION',trans('Fixed Assets &Locations'),
                        trans('Fixed Assets &Locations'),'ERP/inventory/manage/locations.php?FixedAsset=1','fa-people-carry') ?>


                    <?= createMenuTile('SA_ASSETCATEGORY',trans('Fixed Assets Categories'),
                        trans('Fixed Assets Categories'),'ERP/inventory/manage/item_categories.php?FixedAsset=1','fa-people-carry') ?>


                    <?= createMenuTile('SA_ASSETCLASS',trans('Fixed Assets Classes'),
                        trans('Fixed Assets Classes'),'ERP/fixed_assets/fixed_asset_classes.php','fa-boxes') ?>



                </div>







                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('INQUIRIES AND REPORTS') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="row">


                    <?= createMenuTile('SA_ASSETSTRANSVIEW',trans('Fixed Assets Movements'),
                        trans('Fixed Assets Movements'),'ERP/inventory/inquiry/stock_movements.php?FixedAsset=1',
                        'fa-people-carry') ?>


                    <?= createMenuTile('SA_ASSETSANALYTIC',trans('Fixed Assets Inquiry'),
                        trans('Fixed Assets Inquiry'),'ERP/fixed_assets/inquiry/stock_inquiry.php?','fa-people-carry') ?>


                    <?= createMenuTile('SA_ASSETSANALYTIC',trans('Fixed Assets Reports'),
                        trans('Fixed Assets Reports'),'ERP/reporting/reports_main.php?Class=7','fa-people-carry') ?>





                </div>







                <!--End::Row-->



                <!--End::Row-->

                <!--End::Dashboard 2-->
            </div>

            <!-- end:: Content -->
        </div>
    </div>
</div>