<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">


                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('ADD NEW SERVICE/ITEM') ?>
                                </h3>
                            </div>
                        </div>


                        <?php


                        $gl_accounts = $api->get_all_gl_accounts('array');
                        $all_categories = $api->get_all_item_categories('array');
                        $item_tax_types = $api->get_item_tax_types('array');


                        $edit_stock_id = REQUEST_INPUT('edit_stock_id');

                        $general_info = [];
                        $price_info = [];
                        $sub_info = [];

                        $sub_category_list1 = [];
                        $sub_category_list2 = [];
                        if (!empty($edit_stock_id)) {
                            $item_info = $api->get_item_info($edit_stock_id, 'array');

                            $general_info = $item_info['g'];
                            $price_info = $item_info['p'];
                            $sub_info = $item_info['sub'];

                            $sub_category_list1 = $api->get_subcategory($general_info['category_id'], false, 'array');

                            $sub_category_list2 = $api->get_subcategory($general_info['category_id'], $sub_info['parent_sub_cat_id'], 'array');

                        }


                        ?>


                        <!--begin::Form-->
                        <form class="kt-form kt-form--label-right" id="item_form">


                            <input type="hidden" name="edit_stock_id" id="edit_stock_id"
                                   value="<?= REQUEST_INPUT('edit_stock_id') ?>">


                            <h6 class="kt-portlet__head-title" style="padding: 11px 1px 0px 24px;">
                                <?= trans('GENERAL DETAILS') ?>:
                            </h6>
                            <hr>

                            <!-- Hidden Values-->
                            <input type="hidden" name="units" value="each">
                            <input type="hidden" name="no_purchase" value="1">
                            <input type="hidden" name="no_sale" value="0">
                            <input type="hidden" name="mb_flag" value="D">
                            <input type="hidden" name="dimension_id" value="">
                            <input type="hidden" name="dimension2_id" value="">
                            <input type="hidden" name="inventory_account" value="1510">
                            <input type="hidden" name="adjustment_account" value="5040">
                            <input type="hidden" name="wip_account" value="1530">


                            <form class="kt-form">

                                <div class="kt-portlet__body" style="padding: 20px !important;">


                                    <div class="kt-portlet__body">


                                        <div class="form-group row form-group-marginless kt-margin-t-20">
                                            <label class="col-lg-2 col-form-label"><?= trans('ITEM CODE') ?>:
                                                <span>
                                                <a href="#" onclick="generateItemCode();" style="    background: #009487;
                                                                    color: #fff;
                                                                    padding: 4px;
                                                                    font-size: 12px;"><?= trans('Generate') ?></a>
                                            </span>
                                            </label>
                                            <div class="col-lg-6">
                                                <input type="text" id="NewStockID" name="NewStockID"
                                                       class="form-control"
                                                       placeholder=""
                                                       value="<?= getArrayValue($general_info, 'stock_id') ?>">
                                            </div>


                                            <label class="col-lg-2 col-form-label"><?= trans('SERVICE CHARGE') ?>:
                                            </label>
                                            <div class="col-lg-2">
                                                <input type="number" id="price" name="price" class="form-control"
                                                       placeholder=""
                                                       value="<?= getArrayValue($price_info, 'price') ?>">
                                            </div>


                                        </div>


                                        <div class="form-group row form-group-marginless kt-margin-t-20">

                                            <label class="col-lg-2 col-form-label"><?= trans('ITEM NAME') ?>:</label>
                                            <div class="col-lg-6">
                                                <input type="text" id="description" name="description"
                                                       class="form-control"
                                                       placeholder=""
                                                       value="<?= getArrayValue($general_info, 'description') ?>">
                                            </div>



                                            <label class="col-lg-2 col-form-label"><?= trans('ADDITIONAL SERVICE CHARGE') ?>:
                                                <small>Added with service charge in background, but shows as govt fee. Not included with tax calculations</small>
                                            </label>

                                            <div class="col-lg-2">
                                                <input type="number" id="extra_service_charge" name="extra_service_charge" class="form-control"
                                                       placeholder=""
                                                       value="<?= getArrayValue($general_info, 'extra_service_charge') ?>">
                                            </div>


                                        </div>


                                        <div class="form-group row form-group-marginless kt-margin-t-20">

                                            <label class="col-lg-2 col-form-label"><?= trans('ARABIC NAME') ?>
                                                :</label>

                                            <div class="col-lg-6">
                                                <div class="input-group">
                                                    <input type="text" id="long_description" name="long_description"
                                                           class="form-control"
                                                           placeholder=""
                                                           value="<?= getArrayValue($general_info, 'long_description') ?>">
                                                </div>
                                            </div>

                                            <label class="col-lg-2 col-form-label"><?= trans('GOVT CHARGE') ?>:
                                            </label>

                                            <div class="col-lg-2">
                                                <input type="number" id="govt_fee" name="govt_fee" class="form-control"
                                                       placeholder=""
                                                       value="<?= getArrayValue($general_info, 'govt_fee') ?>">

                                                <span class="error_note form-text text-muted kt-hidden">Please enter govt charge</span>

                                            </div>
                                        </div>


                                        <div class="form-group row form-group-marginless kt-margin-t-20">

                                            <label class="col-lg-2 col-form-label"><?= trans('CATEGORY') ?>:</label>
                                            <div class="col-lg-6">
                                                <select class="form-control kt-select2 ap-select2 ap_item_category"
                                                        name="category_id" id="category_id"
                                                        onchange="setDefaultAccounts(this)">

                                                    <?php echo prepareSelectOptions($all_categories, 'category_id', 'description',
                                                        getArrayValue($general_info, 'category_id')) ?>

                                                </select>
                                            </div>

                                            <label class="col-lg-2 col-form-label"><?= trans('BANK SERVICE CHARGE') ?>
                                                :
                                            </label>
                                            <div class="col-lg-2">
                                                <input type="number" id="bank_service_charge" name="bank_service_charge"
                                                       class="form-control"
                                                       placeholder=""
                                                       value="<?= getArrayValue($general_info, 'bank_service_charge') ?>">

                                                <span class="error_note form-text text-muted kt-hidden">Please enter bank service charge</span>

                                            </div>
                                        </div>


                                        <div class="form-group row form-group-marginless kt-margin-t-20">

                                            <label class="col-lg-2 col-form-label"><?= trans('SUB CATEGORY') ?>
                                                1:</label>
                                            <div class="col-lg-6">
                                                <div class="kt-input-icon kt-input-icon--right">
                                                    <select id="sub_cat_1"
                                                            class="form-control kt-select2 ap-select2 sub_cat_1"
                                                            name="sub_cat_1" onchange="loadSubCategory(this,'sub_cat_2',function(data) {

                                                    })">

                                                        <?= prepareSelectOptions($sub_category_list1, 'id', 'value', getArrayValue($sub_info, 'parent_sub_cat_id')) ?>

                                                    </select>
                                                </div>
                                            </div>




                                            <label class="col-lg-2 col-form-label"><?= trans('GOVT BANK ACCOUNT') ?>
                                                :</label>
                                            <div class="col-lg-2">
                                                <select class="form-control kt-select2 ap-select2 ap_gl_account_select"
                                                        name="govt_bank_account" id="govt_bank_account">

                                                    <?= prepareSelectOptions($gl_accounts, 'account_code', 'account_name',
                                                        getArrayValue($general_info, 'govt_bank_account')) ?>

                                                </select>
                                            </div>







                                        </div>


                                        <div class="form-group row form-group-marginless kt-margin-t-20">

                                            <label class="col-lg-2 col-form-label"><?= trans('SUB CATEGORY') ?>
                                                2:</label>
                                            <div class="col-lg-6">
                                                <div class="kt-input-icon kt-input-icon--right">
                                                    <select id="sub_cat_2"
                                                            class="form-control kt-select2 ap-select2 sub_cat_2"
                                                            name="sub_cat_2">

                                                        <?= prepareSelectOptions($sub_category_list2, 'id', 'value', getArrayValue($sub_info, 'id')) ?>

                                                    </select>
                                                </div>
                                            </div>

                                            <label class="col-lg-2 col-form-label"><?= trans('LOCAL COMMISSION') ?>:
                                            </label>

                                            <div class="col-lg-2">
                                                <input type="number" id="commission_loc_user" name="commission_loc_user"
                                                       class="form-control"
                                                       placeholder=""
                                                       value="<?= getArrayValue($general_info, 'commission_loc_user') ?>">

                                            </div>
                                        </div>





                                        <div class="form-group row form-group-marginless kt-margin-t-20">


                                            <label class="col-lg-2 col-form-label"><?= trans('ITEM TAX TYPE') ?>:</label>
                                            <div class="col-lg-2">
                                                <select id="tax_type_id"
                                                        class="form-control kt-select2 ap-select2 ap_item_tax_type"
                                                        name="tax_type_id">

                                                    <?= prepareSelectOptions($item_tax_types, 'id', 'name',
                                                        getArrayValue($general_info, 'tax_type_id')) ?>


                                                </select>


                                            </div>


                                            <label class="col-lg-2 col-form-label"><?= trans('ADDITIONAL GOVT FEE') ?>
                                                :
                                            </label>
                                            <div class="col-lg-2">
                                                <input type="number" id="bank_service_charge_vat"
                                                       name="bank_service_charge_vat" class="form-control"
                                                       placeholder=""
                                                       value="<?= getArrayValue($general_info, 'bank_service_charge_vat') ?>">

                                                <span class="error_note form-text text-muted kt-hidden">Please enter bank charge VAT</span>

                                            </div>


                                            <label class="col-lg-2 col-form-label"><?= trans('OTHER CHARGE') ?>:
                                            </label>

                                            <div class="col-lg-2">
                                                <input type="number" id="pf_amount" name="pf_amount"
                                                       class="form-control"
                                                       placeholder=""
                                                       value="<?= getArrayValue($general_info, 'pf_amount') ?>">


                                            </div>


                                        </div>



                                    </div>


                                </div>


                                <h6 class="kt-portlet__head-title" style="padding: 11px 1px 0px 20px;">
                                    <?= trans('ADVANCED DETAILS') ?>:
                                </h6>

                                <input type="hidden" name="sales_type_id" value="1">
                                <input type="hidden" name="curr_abrev" value="AED">


                                <div class="kt-portlet__body" style="padding: 20px !important;">


                                    <div class="form-group row form-group-marginless kt-margin-t-20">





                                        <label class="col-lg-2 col-form-label"><?= trans('NON-LOCAL COMMISSION:') ?>
                                        </label>
                                        <div class="col-lg-2">
                                            <input type="number" id="commission_non_loc_user"
                                                   name="commission_non_loc_user" class="form-control"
                                                   placeholder=""
                                                   value="<?= getArrayValue($general_info, 'commission_non_loc_user') ?>">


                                        </div>

                                        <label class="col-lg-2 col-form-label"><?= trans('EDITABLE DESCRIPTION') ?>
                                            :</label>
                                        <div class="col-lg-2">
                                            <select id="editable" class="form-control kt-selectpicker" name="editable">
                                                <option value="0" <?= getArrayValue($general_info, 'editable') == 0 ? 'selected' : '' ?>><?= trans('NO') ?></option>
                                                <option value="1" <?= getArrayValue($general_info, 'editable') == 1 ? 'selected' : '' ?>><?= trans('YES') ?></option>
                                            </select>
                                        </div>

                                        <label class="col-lg-2 col-form-label"><?= trans('USE OWN GOVT.BANK') ?>
                                            :</label>

                                        <div class="col-lg-2">
                                            <select id="use_own_govt_bank_account" class="form-control kt-selectpicker"
                                                    name="use_own_govt_bank_account">
                                                <option value="0" <?= getArrayValue($general_info, 'use_own_govt_bank_account') == 0 ? 'selected' : '' ?>><?= trans('NO') ?></option>
                                                <option value="1" <?= getArrayValue($general_info, 'use_own_govt_bank_account') == 1 ? 'selected' : '' ?>><?= trans('YES') ?></option>
                                            </select>
                                        </div>


                                    </div>


                                    <div class="form-group row form-group-marginless kt-margin-t-20">

                                            <label class="col-lg-2 col-form-label"><?= trans('SALES ACCOUNT') ?>
                                                :</label>

                                        <div class="col-lg-2">
                                            <select class="form-control kt-select2 ap-select2 ap_gl_account_select"
                                                    name="sales_account" id="sales_account">

                                                <?= prepareSelectOptions($gl_accounts, 'account_code', 'account_name',
                                                    getArrayValue($general_info, 'sales_account')) ?>

                                            </select>
                                        </div>

                                            <label class="col-lg-2 col-form-label"><?= trans('COGS ACCOUNT') ?>:</label>

                                        <div class="col-lg-2">
                                            <select class="form-control kt-select2 ap-select2 ap_gl_account_select"
                                                    name="cogs_account" id="cogs_account">

                                                <?= prepareSelectOptions($gl_accounts, 'account_code', 'account_name',
                                                    getArrayValue($general_info, 'cogs_account')) ?>

                                            </select>
                                        </div>

                                        <label class="col-lg-2 col-form-label"><?= trans('ITEM STATUS') ?>:</label>
                                        <div class="col-lg-2">
                                            <select id="inactive" class="form-control kt-selectpicker" name="inactive">
                                                <option value="0" <?= getArrayValue($general_info, 'inactive') == 0 ? 'selected' : '' ?>><?= trans('ACTIVE') ?></option>
                                                <option value="1" <?= getArrayValue($general_info, 'inactive') == 1 ? 'selected' : '' ?>><?= trans('INACTIVE') ?></option>
                                            </select>
                                        </div>

                                    </div>


                                    <div class="form-group row form-group-marginless kt-margin-t-20">






                                    </div>


                                </div>


                                <div class="kt-portlet__foot">
                                    <div class="kt-form__actions">
                                        <div class="row">
                                            <div class="col-lg-4"></div>
                                            <div class="col-lg-8">
                                                <button type="button" onclick="CreateNewItem();"
                                                        class="btn btn-primary">
                                                    <?= trans('Submit') ?>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-secondary"><?= trans('Cancel') ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <!--end::Form-->
                    </div>


                </div>

            </div>

            <!-- end:: Content -->
        </div>
    </div>
</div>




