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
                            <h3 class="kt-subheader__title"><?= trans('TRANSACTIONS') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <?php

                        $awaiting_purchase_req_count = $api->getCountAwaitingPurchaseRequests();

                    ?>


                    <?= createMenuTile('SA_SALESORDER',trans('New Purchase Request'),
                        trans('New Purchase Request'),'purchase_request_new.php','far fa-paper-plane',1) ?>

                    <?= createMenuTile('SA_SALESORDER',trans('Purchase Requests')." <span class='cnt-pr' style='color: red'>($awaiting_purchase_req_count)</span>",
                        trans('Purchase Request List'),'purchase_request_list.php','far fa-file-alt',1) ?>


                    <?= createMenuTile('SA_PURCHASEORDER',trans('Purchase Order Entry'),
                        trans('Purchase Order Entry'),route('purchase_order'),'fa-print',1) ?>
                    <?= createMenuTile('SA_GRN',trans('Receive Items'),
                        trans('Receive Items'),route('recevie_items'),'fa-arrow-alt-circle-left',1) ?>
                    <?= createMenuTile('SA_SUPPLIERINVOICE',trans('Supplier Invoices'),
                        trans('Supplier Invoices'),route('supplier_invoice'),'fa-file-alt',1) ?>

                    <?= createMenuTile('SA_SUPPLIERINVOICE', trans('Direct Supplier Invoice'),
                        trans('Create supplier invoice'), route('direct_supplier_invoice'), 'fa-print', 1) ?>

                    <?= createMenuTile('SA_SUPPLIERPAYMNT', trans('Payment'),
                        trans('Payment to Supplier'), route('supplier_payment'), 'fa-money-bill-wave', 1) ?>

                </div>


                <div class="kt-subheader kt-subheader-custom   kt-grid__item" style="">
                    <div class="kt-container ">
                        <div class="kt-subheader__main" style=" ">
                            <h3 class="kt-subheader__title"><?= trans('REPORTS & INQUIRIES') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <?= createMenuTile('SA_SUPPLIERCREDIT', trans('Supplier Credit Notes'),
                        trans('Supplier Credit Notes'), route('supplier_creditnote'), 'fa-receipt', 1) ?>
                    <?= createMenuTile('SA_SUPPTRANSVIEW', trans('Purchase Order '),
                        trans('Purchase order Inquiry'), route('purchase_enquiry'), 'fa-headset', 1) ?>
                    <?= createMenuTile('SA_SUPPTRANSVIEW', trans('Supplier Transaction'),
                        trans('Supplier Transaction Inquiry'), route('supplier_enquiry'), 'fa-info', 1) ?>

                    <!--  --><? /*= createMenuTile('SA_SUPPTRANSVIEW',trans('Inquiry'),
                                   trans('Supplier Transaction Inquiry'),route('supplier_transactions'),'fa-info',2) */ ?>
                    <?= createMenuTile('SA_SUPPTRANSVIEW', trans('Supplier Allocation '),
                        trans('Supplier Allocation Inquiry'), route('supplier_allocation_enquiry'), 'fa-headset', 1) ?>

                    <?= createMenuTile('SA_SUPPLIERANALYTIC', trans('Supplier Statement'),
                        trans('Supplier Statement'), 'rep_supplier_balances.php', 'fa-boxes') ?>

                    <?= createMenuTile('SA_ITEMCATEGORY', trans('Aged Supplier Analysis'),
                        trans('Aged Supplier Analysis'), 'rep_supplier_aged.php', 'fa-boxes') ?>

                </div>


                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('') ?></h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <? /*= createMenuTile('SA_SUPPTRANSVIEW',trans('Purchase Orders Inquiry'),
                        trans('Purchase order Inquiry'),route('purchase_enquiry'),'fa-headset') */ ?><!--

                    <? /*= createMenuTile('SA_SUPPTRANSVIEW',trans('Supplier Transaction Inquiry'),
                        trans('Supplier Transaction Inquiry'),route('supplier_enquiry'),'fa-info') */ ?>

                    --><? /*= createMenuTile('SA_SUPPTRANSVIEW',trans('Supplier Allocation Inquiry'),
                        trans('Supplier Allocation Inquiry'),route('supplier_allocation_enquiry'),'fa-headset') */ ?>


                </div>


                <!--End::Row-->


                <!--End::Row-->

                <!--End::Dashboard 2-->


                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('ITEMS/INVENTORY') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="row">


                    <? /*= createMenuTile('SA_ITEM',trans('Sales Pricing'),
                        trans('Sales Pricing'),route('sales_price'),'fa-money-bill-wave') */ ?>
                    <?= createMenuTile('SA_SUPPLIER', trans('Suppliers'),
                        trans('Suppliers'), route('suppliers'), 'fa-people-carry') ?>

                    <?= createMenuTile('SA_PURCHASEPRICING', trans('Purchase Pricing'),
                        trans(' Purchase Pricing'), route('purchase_price'), 'fa-shopping-cart') ?>

                    <!-- --><? /*= createMenuTile('SA_ITEM',trans('Standard Costs'),
                        trans('Standard Costs'),route('standard_cost'),'fa-list-ul') */ ?>

                    <?= createMenuTile('SA_ITEMCATEGORY', trans('PO Terms & Conditions'),
                        trans('PO Terms & Conditions'), 'purchase_terms_and_conditions.php', 'fa-boxes') ?>



<!--                    --><?//= createMenuTile('SA_ITEMCATEGORY', trans(' Stock Transfer'),
//                        trans('Stock Transfer'), route('inve_loc'), 'fa-boxes') ?>

                    <?= createMenuTile('SA_INVENTORYADJUSTMENT', trans('Stock Adjustments'),
                        trans('Stock Adjustments'), route('inven_adjust'), 'fa-boxes') ?>

                    <?= createMenuTile('SA_ITEMSTRANSVIEW', trans('Stock Report'),
                        trans('Stock Report'), route('item_movement'), 'fa-boxes') ?>

                    <?= createMenuTile('SA_ITEMSSTATVIEW', trans('Stock Availability'),
                        trans('Inventory Item Status'), route('item_status'), 'fa-boxes') ?>

                    <?= createMenuTile('SA_REORDER', trans('Reorder Levels'),
                        trans('Reorder Levels'), route('reorder_level'), 'fa-boxes') ?>

                    <?= createMenuTile('SA_ITEM',trans('Items'),
                        trans('Add and Manage Items'),'ERP/inventory/manage/items.php?','fa-people-carry') ?>


                    <? /*= createMenuTile('SA_VIEWPRINTTRANSACTION',trans('Project Inquiry'),
                        trans('Project Inquiry'),route('list_projects'),'fa-th-list') */ ?>

                </div>

            </div>

            <!-- end:: Content -->
        </div>
    </div>
</div>
<style>
    .col-lg-3 {
        max-width: 95% !important;
    }
</style>