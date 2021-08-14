<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">




            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">





                    <div class="kt-portlet">
                        <!--begin::Table-->


                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <table class="table table-bordered" id="service_list_table">
                                <thead>
                                <th><?= trans('Ref ID') ?></th>
                                <th><?= trans('Name') ?></th>
                                <th><?= trans('P.R.O Name') ?></th>
                                <th><?= trans('Address') ?></th>
                                <th><?= trans('Mobile') ?></th>
                                <th><?= trans('Email') ?></th>
                                <th><?= trans('TRN') ?></th>
                                <?php
                                $sql = "SELECT category_id, description FROM 0_stock_category";
                                $result = db_query($sql);
                                while ($myrow = db_fetch($result)) {
                                    ?>
                                    <th><?= trans($myrow['description']) ?></th>
                                    <?php

                                }
                                ?>
                                </thead>
                                <tbody id="service_list_tbody">

                                </tbody>
                            </table>

                        </div>


                        <!--end::Form-->
                    </div>


                </div>

            </div>

            <!-- end:: Content -->
        </div>
    </div>
</div>




