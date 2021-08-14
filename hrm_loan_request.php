<?php
include "header.php";?>

<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">
                <?php include('hrm_employee_comman_header.php'); ?>
                <div class="row">

                    <div class="kt-portlet">


                        <div class="kt-portlet__head">

                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('LOAN REQUEST') ?>

                                </h3>
                            </div>
                        </div>



                        <div class="kt-portlet__body" style="padding: 5px !important;">
                            <div class="row">

                                <div class="col-md-4">
                                    <form class="kt-form kt-form--label-right" id="Loan_form">
                                        <div>
                                            <label class=""><?= trans('LOAN AMOUNT') ?>:</label>
                                            <input type="text" id="loan_amount" name="loan_amount" class="form-control"/>
                                        </div>
                                    <div>
                                        <label><?= trans('INASTLLMENT REQUIRED') ?>:
                                        </label>
                                        <select id="ddl_instll_required" name="ddl_instll_required" class="form-control">
                                            <option value="0">--SELECT--</option>
                                        <?php
                                         for($i=1;$i<=24;$i++)
                                         {
                                           echo '<option value="'.$i.'">'.$i.'</option>';
                                         } ?>
                                        </select>
                                    </div>
                                        <div>
                                            <label class=""><?= trans('LOAN REQUIRED DATE') ?>:</label>
                                            <input type="text" id="txt_required_date" name="txt_required_date" class="form-control ap-datepicker" />
                                        </div>
                                    <div>
                                        <label class=""><?= trans('REASON FOR REQUEST') ?>:</label>
                                        <textarea id="txtcomment" name="txtcomment" class="form-control" ></textarea>
                                    </div>
                                    <div>
                                        <label style="height: 24px;"></label>
                                        <div class="input-group">
                                            <button type="button" onclick="SubmitLeave();" class="btn btn-primary">
                                                <?= trans('Submit Request') ?>
                                            </button>&nbsp;&nbsp;

                                            <button type="button" class="btn btn-secondary btnCancel"><?= trans('Cancel') ?></button>
                                            <img src="<?= $base_url ?>assets/media/img/ajax-loader.gif" id="loader" style="height: 16px;
                                            margin-top: 4%;display: none;"/>
                                            <input type="hidden" id="hdnCurrentDate" name="hdnCurrentDate"  value="<?php echo date('d-m-Y') ?>"/>
                                            <input type="hidden" id="hdnAction" name="hdnAction"/>
                                            <input type="hidden" id="hdn_id" name="hdn_id"/>
                                        </div>
                                    </div>
                                </form>

                                </div>
                                <div class="col-md-8">


                                        <div class="table-responsive" >

                                            <table class="table table-bordered" id="certifcate_reuqests">
                                                <thead>
                                                <th><?= trans('Ref.No') ?></th>
                                                <th><?= trans('Loan Reason') ?></th>
                                                <th><?= trans('Amount') ?></th>
                                                <th><?= trans('Installment Count') ?></th>
                                                 <th><?= trans('Required Date') ?></th>
                                                <th><?= trans('Req.Status') ?></th>
                                                <th><?= trans('Created On') ?></th>

                                                <th></th>


                                                </thead>
                                                <tbody id="certifcate_reuqests_tbody">

                                                </tbody>
                                            </table>

                                        </div>




                                </div>

                            </div>


                        </div>


                        <!--end::Form-->
                    </div>


                </div>


            </div>

            <!-- end:: Content -->
        </div>
    </div>
</div>


<?php include "footer.php"; ?>
<script>


    $('.ap-datepicker').datepicker({
        format: 'dd-mm-yyyy',
    });
    fetchDataTotable();
    function SubmitLeave() {
        var form = $("#Loan_form");
        var params = AxisPro.getFormData(form);
        $("#loader").css('display','block');




        if($(".address_to").val()=='')
        {
            toastr.error('ERROR !. Please enter address to');
            $("#loader").css('display','none');
            return false;

        }

        $(".error_note").hide();

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=submit_loan_request", params, function (data) {

            if(data.status =='Success')
            {
                toastr.success(data.msg);
                fetchDataTotable();
            }
            else
            {
                toastr.error(data.msg);
            }


            $("#loader").css('display','none');
        });
    }

    function fetchDataTotable()
    {
        $('#certifcate_reuqests').dataTable().fnDestroy();
        $('#certifcate_reuqests').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :ERP_FUNCTION_API_END_POINT + "?method=get_loan_requests", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                //data:{'dept_id':$(".ap_department").val(),'emp_id':$(".ap_employees").val(),'year':$(".ap-year").val(),'month':$(".ClsMonths ").val()},
                error: function(){
                }
            }
        });
    }



    $('#certifcate_reuqests tbody').on('click', 'td label.ClsBtnEdit', function (){
        var alt_id=$(this).attr('alt_id');
        var amount=$(this).attr('alt_amount');
        var instalment=$(this).attr('alt_installment');
        var required_date=$(this).attr('alt_loan_require_date');
        var description=$(this).attr('alt_description');


        $('#txtcomment').val(description);
        $('#hdn_id').val(alt_id);
        $('#loan_amount').val(amount);
        $('#ddl_instll_required').val(instalment);
        $('#txt_required_date').val(required_date);

    });

    $('#certifcate_reuqests tbody').on('click', 'td label.ClsBtnRemove', function (){
        var edit_id=$(this).attr('alt_id');
        var confrmRes=confirm("Are you really want to delete request?");
        if(confrmRes==true)
        {
            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=remove_requests"
                , 'remove_id='+edit_id+'&type=2', function (data) {

                if (data.status === 'Error') {
                    toastr.error(data.msg);
                    fetchDataTotable();
                }
                else
                {
                    toastr.success(data.msg);
                    fetchDataTotable();

                }

            });
        }
    });


    $(".btnCancel").click(function()
    {
       location.reload();
    });



</script>