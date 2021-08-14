<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <!-- begin:: Content -->
            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="kt-subheader kt-subheader-custom   kt-grid__item">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?= trans('SETTINGS') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <?= createMenuTile(
                        'SA_SETUPCOMPANY',
                        trans('Company Setup'),
                        trans('Setup company information'),
                        route('company_setup'),
                        'fa-cogs'
                    ) ?>
                    <?= createMenuTile(
                        'SA_USERS',
                        trans('Users'),
                        trans('Manage Users'),
                        route('user_setup'),
                        'fa-users-cog'
                    ) ?>
                    <?= createMenuTile(
                        'SA_SECROLES',
                        trans('Access Controls'),
                        trans('Manage Users'),
                        route('access_setup'),
                        'fa-users-cog'
                    ) ?>
                    <?= createMenuTile(
                        'SA_POSSETUP',
                        trans('Sales Point Settings'),
                        trans('POS settings'),
                        'ERP/sales/manage/sales_points.php?',
                        'fa-users-cog'
                    ) ?>
                    <?= createMenuTile(
                        'SA_TAXRATES',
                        trans('Tax Types'),
                        trans('Manage Tax Types'),
                        route('tax_types_setup'),
                        'fa-cog'
                    ) ?>
                    <?= createMenuTile(
                        'SA_GLSETUP',
                        trans('System and General GL Setup'),
                        trans('Accounting Setup'),
                        route('gl_setup'),
                        'fa-cog'
                    ) ?>     
                    <?= createMenuTile(
                        'SA_FISCALYEARS',
                        trans('Fiscal Years'),
                        trans('Financial Year Setup'),
                        route('fsy_setup'),
                        'fa-cog'
                    ) ?>
                    <?= createMenuTile(
                        'SA_ITEMTAXTYPE',
                        trans('Item Tax Types'),
                        trans('Manage Item Tax Types'),
                        route('item_tax_types_setup'),
                        'fa-cog'
                    ) ?>
                    <?= createMenuTile(
                        'SA_VOIDTRANSACTION',
                        trans('Void Transaction'),
                        trans('Void Transaction'),
                        route('void_trans'),
                        'fa-cog'
                    ) ?>
                    <?= createMenuTile(
                        'SA_VOIDEDTRANSACTIONS',
                        trans('Voided & Edited Report'),
                        trans('Voided & Edited Report'),
                        route('voided_trans'),
                        'fa-eraser'
                    ) ?>
                    <?= createMenuTile(
                        'SA_ITEMCATEGORY',
                        trans('Item Categories'),
                        trans('Add and Manage Category'),
                        route('category'),
                        'fa-boxes'
                    ) ?>
                    <?= createMenuTile(
                        'SA_ATTACHDOCUMENT',
                        trans('Attach Documents'),
                        trans('Attach documents'),
                        route('attach_documents'),
                        'fa-paperclip'
                    ) ?>
                </div>
            </div>
        </div>
    </div>
</div>