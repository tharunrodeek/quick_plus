<?php
include "header.php";
?>
<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">


                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('CREATE PAY ELEMENTS') ?>

                                </h3>
                            </div>
                        </div>


                        <!--begin::Form-->
                        <form class="kt-form kt-form--label-right" id="element_form">
                            <div class="kt-portlet__body" style="padding: 20px !important;">
                                <div class="Msg"></div>
                                <div class="form-group row">
                                    <div class="col-lg-2">
                                        <label><?= trans('ELEMENT NAME') ?>:
                                        </label>
                                         <input type="text" id="txtElement_Name" name="txtElement_Name" class="form-control" />
                                        <span class="error_note form-text text-muted kt-hidden">Please enter your full name</span>
                                    </div>
                                  <div class="col-lg-4">
                                        <label class=""><?= trans('SELECT ACCOUNT') ?>:</label>
                                        <select class="form-control kt-select2 ap-select2 ap_account"
                                                name="ddl_accounts" >
                                            <option value="">SELECT</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 ClsSubledger" style="display:none;">
                                        <label class=""><?= trans('SELECT Sub-Ledger') ?>:</label>
                                        <div class="bindSubledger"></div>
                                    </div>
                                    <div class="col-lg-2">
                                        <label class=""><?= trans('TYPE') ?>:</label>
                                        <select class="form-control kt-select2 ap-select2 ap_type"
                                                name="ddl_type">
                                            <option value="">SELECT</option>
                                            <option value="1">Earnings</option>
                                            <option value="2">Deductions</option>
                                        </select>
                                    </div>
                                   <!-- <div class="col-lg-2">
                                        <label class=""><?/*= trans('TYPE') */?>:</label>
                                        <select class="form-control kt-select2 ap-select2 ap_type"
                                                name="ddl_type" >
                                            <option value="">SELECT</option>
                                            <option value="1">Earnings</option
                                            <option value="2">Deductions</option
                                        </select>
                                    </div>-->
                                    <div class="col-lg-3">
                                        <label style="height: 13px;"></label>
                                        <div class="input-group">
                                            <button type="button" onclick="SavePayElement();" class="btn btn-primary">
                                                <?= trans('Save Pay Elements') ?>
                                            </button>&nbsp;&nbsp;
                                            <button type="button" class="btn btn-secondary btnCancel" onclick="Cancel();"><?= trans('Cancel') ?></button>
                                            <input type="hidden" id="hdnAction" name="hdnAction"/>
                                        </div>
                                    </div>
                                </div>


                        </form>
                        <!--end::Form-->
                    </div>
                </div>

                <div class="row" style="width: 100%;">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <?= trans('LIST OF PAY ELEMENTS') ?>
                                </h3>
                            </div>

                        </div>
                        <!--begin::Table-->
                        <div class="table-responsive" style="padding: 7px 7px 7px 7px;">

                            <table class="table table-bordered" id="list_pay_elements">
                                <thead>
                                <th><?= trans('Element') ?></th>
                                <th><?= trans('Account Code') ?></th>
                                <th><?= trans('Account Name') ?></th>
                                <!--<th><?/*= trans('Subledger') */?></th>-->
                                <th><?= trans('Type') ?></th>
                                <th></th>
                                <th></th>
                                </thead>
                                <tbody id="list_pay_elements_tbody">
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "footer.php"; ?>
<script>
    $(document).ready(function () {

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_accounts', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data, 'account_code', 'accname', 'ap_account',null,function () {
                $('.ap_account').prepend('<option value="0">Select Account</option>');
                $('.ap_account').val('0');
            });
        });

        Fecthdata();
    });


    /*$('.ap_account').change(function()
    {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_to_subacc", 'acc_id='+$('.ap_account').val(), function (data) {
             $('.ClsSubledger').css('display','block');
             $('.bindSubledger').html(data);
        });
    });*/




    function SavePayElement() {
        var $form = $("#element_form");
        var params = AxisPro.getFormData($form);
        var type=$(".ap_type").val();

        if($("#txtElement_Name").val()=='')
        {
            toastr.error('ERROR !. Please enter pay element name');
            return false;
        }

        if($(".ap_account").val()=='0')
        {
            toastr.error('ERROR !. Please select account');
            return false;
        }

        if(type=='')
        {
            toastr.error('ERROR!! Please select pay element type');
            return false;
        }


        $(".error_note").hide();

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=save_element", params, function (data) {
            if (data.status === 'ERROR') {
                 $(".Msg").html('<label style="color: red;font-size: 12pt;font-weight: bold;">The account is already assigned to other element</label>');
                $('.Msg').delay(5000).fadeOut('slow');
            }
            else
            {
                toastr.success('SUCCESS !. PAY ELEMENTS CREATED SUCCESSFULLY.');
                Fecthdata();
            }
        });
    }


    function Fecthdata()
    {
        $("#txtElement_Name").val('');
        $("#hdnAction").val('');
        //$('.ap_account').prepend('<option value="0">Select Account</option>');
        //$('.ap_account').val('0');
        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
            method: 'get_pay_elements',
            format: 'json'
        }, function (data) {

            if(data) {

                var tbody_html = "";

                $.each(data, function(key,value) {

                    tbody_html+="<tr>";
                    tbody_html+="<td>"+value.element_name+"</td>";
                   tbody_html+="<td>"+value.account_code+"</td>";
                    tbody_html+="<td>"+value.accname+"</td>";
                    //tbody_html+="<td>"+value.sub_ledger+"</td>";
                    tbody_html+="<td>"+value.acc_type+"</td>";
                    tbody_html+="<td><label alt='"+value.id+"' alt_val='"+value.element_name+"' " +
                        "        alt_code='"+value.account_code+"'  alt_type='"+value.type_id+"' class='btn btn-sm btn-primary ClsBtnEdit'><i class='flaticon-edit'></i></td>";
                    if(value.deletable=='0')
                    {
                        tbody_html+="<td><label alt='"+value.id+"' class='btn btn-sm btn-primary ClsBtnRemove'><i class='flaticon-delete'></i></td>";
                    }
                    else
                    {
                        tbody_html+="<td></td>";
                    }
                    tbody_html+="</tr>";

                });

                $("#list_pay_elements_tbody").html(tbody_html);

            }

        });
    }
    $("#list_pay_elements").DataTable( {
        //dom: 'Bfrtip'
    });

    $('#list_pay_elements tbody').on('click', 'td label.ClsBtnEdit', function (){
        $("html, body").animate({ scrollTop: 0 }, "slow");
        var id=$(this).attr('alt');
        var element_name=$(this).attr('alt_val');
        var account_code=$(this).attr('alt_code');
        var alt_type=$(this).attr('alt_type');

        AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_accounts', format: 'json'}, function (data) {
            AxisPro.PrepareSelectOptions(data,'account_code','accname','ap_account',null,function () {
                $(".ap_account").val(account_code);
                $("#txtElement_Name").val(element_name);
                $("#hdnAction").val(id);
                $(".ap_type").val(alt_type).change();
            });
        });
    });

    $('#list_pay_elements tbody').on('click', 'td label.ClsBtnRemove', function (){
        var id=$(this).attr('alt');

        var confrim=confirm('Are you really want to delete the pay element?');
        if(confrim==true) {

            AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=remove_element", 'remove_id='+id, function (data) {
                if (data.status === 'FAIL' && data.msg === 'VALIDATION_FAILED') {
                }
                else
                {
                    toastr.success('SUCCESS !. Pay Element Removed Successfully.');
                    Fecthdata();
                }
            });

        }
    });

    function Cancel()
    {
        location.reload();
    }

</script>