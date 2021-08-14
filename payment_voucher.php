My Drive
<?php
include "header.php";
?>

<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">

                    <div class="row" style="width: 100%;">

                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <?php
                                        if($_GET['trans_type']=='1')
                                        { ?>
                                            <?= trans('PAYMENT VOUCHER') ?>
                                        <?php }
                                        if($_GET['trans_type']=='2')
                                        { ?>
                                            <?= trans('RECEIPT VOUCHER') ?>
                                            <?php
                                        }

                                        include_once($path_to_root . "/API/API_Call.php");
                                        $api_obj=new API_Call();
                                        $head_data=$api_obj->get_headings_data($_GET['trans_no'],$_GET['trans_type']);

                                        $ref_no=$head_data[0]['ref'];
                                        $from=$head_data[0]['payto'];
                                        $pay_type=$head_data[0]['payment_type'];
                                        $bank_act=$head_data[0]['bank_act'];
                                        $person_typ_id=$head_data[0]['person_type_id'];
                                        $cheq_no=$head_data[0]['cheq_no'];
                                        $cheq_date=$head_data[0]['cheq_date'];
                                        $person_id=$head_data[0]['person_id'];



                                        ?>

                                    </h3>
                                </div>

                            </div>


                            <form class="kt-form kt-form--label-right" id="report_filter_form"
                                  method="post" style="padding: 2px 0 9px 0;
    border-radius: 6px; border: 1px solid #ccc">


                                <div class="kt-portlet__body" style="padding: 5px !important;">
                                    <div class="form-group row">

                                        <div class="col-lg-2">
                                            <label><?= trans('Date') ?>:
                                            </label>
                                            <input type="text" id="txt_date" name="txt_date" class="form-control ap-datepicker" value="<?php echo Today(); ?>"/>
                                        </div>
                                        <div class="col-lg-2">
                                            <label><?= trans('Reference') ?>:
                                            </label>
                                            <input type="text" id="txt_reference" name="txt_reference" class="form-control" value="<?php echo $ref_no; ?>"/>
                                        </div>

                                        <div class="col-lg-2">
                                            <label class="Cls_bnk_txt">
                                                <?php
                                                if($_GET['trans_type']==1)
                                                {
                                                    echo trans('From');
                                                }
                                                else if($_GET['trans_type']==2)
                                                {
                                                    echo  trans('Into');
                                                }

                                                ?>:
                                            </label>
                                            <select class="form-control kt-select2 ap-select2 ClsFrombank"
                                                    name="bank_account" id="bank_account" >
                                                <option value="">SELECT</option>

                                            </select>
                                        </div>



                                        <div class="col-lg-2">
                                            <label><?= trans('Payment Type') ?>:
                                            </label>
                                            <select class="form-control ClsPayType"
                                                    name="paytype" >
                                                <option value="">SELECT</option>
                                                <option value="1">Cash</option>
                                                <option value="2">Cheque</option>
                                                <option value="3">Transfer</option>
                                            </select>
                                        </div>


                                        <div class="col-lg-2 clsShowhide" style="display:none;">
                                            <label><?= trans('Cheque No') ?>:
                                            </label>
                                            <input type="text" id="txt_chq_no" name="txt_chq_no" class="form-control"/>
                                        </div>
                                        <div class="col-lg-2 clsShowhide" style="display:none;">
                                            <label><?= trans('Cheque Date') ?>:
                                            </label>
                                            <input type="text" id="txt_chq_date" name="txt_chq_date" class="form-control ap-datepicker"/>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="lbl_txt"><?php
                                                if($_GET['trans_type']==1)
                                                {
                                                    echo trans('Pay To');
                                                }
                                                else if($_GET['trans_type']==2)
                                                {
                                                    echo  trans('From');
                                                }

                                                ?>:
                                            </label>
                                            <select class="form-control ClsPayTo"
                                                    name="payto" >
                                                <option value="">SELECT</option>
                                                <option value="0">Miscellaneous</option>
                                                <option value="2">Customer</option>
                                                <option value="3">Supplier</option>
                                            </select>
                                        </div>

                                        <div class="col-lg-3 clsShowCustomer" style="display:none;">
                                            <label><?= trans('Customer') ?>:
                                            </label>
                                            <select class="form-control kt-select2 ap-select2 ClsCustomer"
                                                    name="customer" id="customer" >
                                                <option value="">SELECT</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 clsShowSuplier" style="display:none;">
                                            <label><?= trans('Supplier') ?>:
                                            </label>
                                            <select class="form-control kt-select2 ap-select2 ClsSupplier"
                                                    name="supplier" id="supplier" >
                                                <option value="">SELECT</option>

                                            </select>
                                        </div>
                                        <div class="col-lg-2">
                                            <label><?= trans('Being') ?>:
                                            </label>
                                            <textarea id="txt_being" class="form-control" width="450px"></textarea>
                                        </div>
                                        <div class="col-lg-2 clsShowBnkBlnce" style="display:none;">
                                            <label style="font-size: 12pt;font-weight: bold;"><?= trans('Bank Balance') ?>:
                                            </label>
                                            <label id="Dispbankbalnce" style="font-size: 12pt;font-weight: bold;"></label>
                                        </div>



                                    </div>

                                </div>




                            </form>

                            <div class="table-responsive" style="padding: 7px 7px 7px 7px;width: 1249px;">
                                <button type="button" class="btn btn-info add-new"><i class="fa fa-plus"></i> Add New</button>
                                &nbsp;&nbsp;<button type="button" class="btn btn-info payment-submit">
                                    <?php
                                    if($_GET['trans_type']=='1')
                                    { ?>
                                        Process Payment Voucher
                                    <?php }
                                    if($_GET['trans_type']=='2')
                                    { ?>
                                        Process Receipt Voucher
                                        <?php
                                    }
                                    ?>
                                </button>
                                <table class="table table-bordered" id="invoice_report">
                                    <thead>
                                    <th><?= trans('Account') ?></th>
                                    <th><?= trans('Counter Party /Sub-ledger') ?></th>
                                    <th><?= trans('Cost Center') ?></th>
                                    <th><?= trans('Amount') ?></th>
                                    <th><?= trans('Memo') ?></th>
                                    <th></th>
                                    </thead>

                                    <tbody id="invoice_report_tbody">

                                    </tbody>
                                </table>

                            </div>

                        </div>


                    </div>

                </div>

                <!-- end:: Content -->
            </div>
        </div>
    </div>
    <?php
    $url="http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $exp=explode("/",$url);
    $baseurl=$exp[0].'//'.$exp[2].'/'.$exp[3].'/';

    $modify_pay='';
    if(isset($_GET['ModifyPayment']) || isset($_GET['ModifyDeposit']))
    {
        $modify_pay='1';
    }


    ?>
    <input type="hidden" id="hdn_page_type" value="<?php echo $_GET['trans_type']; ?>"/>
    <input type="hidden" id="hdn_total_Rows" />
    <input type="hidden" id="hdn_update_Rows" />
    <input type="hidden" id="hdn_url" value="<?php echo $baseurl; ?>" />
    <input type="hidden" id="hdn_modify_voucher" value="<?php echo $modify_pay; ?>" />
    <input type="hidden" id="hdn_trans_no" value="<?php echo $_GET['trans_no']; ?>" />
    <input type="hidden" id="hdn_person_type_id" value="<?php echo $person_typ_id; ?>" />
    <input type="hidden" id="hdn_last_index" value="0"/>
    <?php include "footer.php"; ?>
    <!--link rel="stylesheet" href="https://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">

    <script src="https://code.jquery.com/ui/1.11.2/jquery-ui.js"></script-->

    <script>
        $(document).ready(function ()
        {
            //alert($('#hdn_modify_voucher').val());
            if($('#hdn_modify_voucher').val()=='1')
            {
                $('.ClsPayType').val('<?php echo $pay_type; ?>');
                $('.ClsPayTo').val('<?php echo $person_typ_id; ?>');
            }



            if($('.ClsPayTo').val()=='2')
            {
                $('.clsShowCustomer').show();
            }

            if($('.ClsPayTo').val()=='3')
            {
                $('.clsShowSuplier').show();
            }


            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_accounts', format: 'json'}, function (data) {
                AxisPro.PrepareSelectOptions(data, 'account_code', 'accname', 'ap_service_select', '--------CHOOSE VAT ACCOUNT---------');
            });

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_accounts', format: 'json'}, function (data) {
                AxisPro.PrepareSelectOptions(data,  'account_code', 'accname', 'ap_service_select_from', '--------CHOOSE FROM ACCOUNT---------',function()
                {

                });

            });

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_accounts', format: 'json'}, function (data) {
                AxisPro.PrepareSelectOptions(data,  'account_code', 'accname', 'ap_service_select_to', '--------CHOOSE TO ACCOUNT---------');
            });

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_reference_number','type':$('#hdn_page_type').val() ,format: 'json'}, function (data) {
                if($('#hdn_modify_voucher').val()=='')
                {
                    $('#txt_reference').val(data);
                }

            });

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_bank_accounts', format: 'json'}, function (data) {
                AxisPro.PrepareSelectOptions(data, 'id', 'bank_account_name', 'ClsFrombank', '--------CHOOSE BANK ACCOUNT---------',function()
                {
                    $('.ClsFrombank').val('<?php echo $bank_act ?>');
                });
            });

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_customers', format: 'json'}, function (data) {
                AxisPro.PrepareSelectOptions(data, 'debtor_no', 'custname', 'ClsCustomer', '--------CHOOSE CUSTOMERS---------',function()
                {
                    if($('#hdn_modify_voucher').val()=='1')
                    {
                        $('.ClsCustomer').val('<?php echo $person_id; ?>');
                    }
                });
            });

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_suppliers', format: 'json'}, function (data) {
                AxisPro.PrepareSelectOptions(data, 'supplier_id', 'supplier_name', 'ClsSupplier', '--------CHOOSE SUPPLIERS---------');
            });




            if($('#hdn_modify_voucher').val()=='1')
            {

                AxisPro.APICall('POST',
                    ERP_FUNCTION_API_END_POINT + "?method=get_edit_voucher_data",'trans_no='+$('#hdn_trans_no').val()+'&type='+$('#hdn_page_type').val(),
                    function (data) {


                        $(".table-responsive table").append(data.trans_no);
                        $('#hdn_last_index').val(data.next_index);


                    }
                );

            }
            else
            {
                $(".add-new").trigger("click");
            }

            if($('.ClsPayType').val()=='2')
            {
                $('.clsShowhide').show();
                $('#txt_chq_no').val('<?php echo $cheq_no; ?>');
                $('#txt_chq_date').val('<?php echo date('m/d/Y',strtotime($cheq_date)); ?>');
            }

        });

        var i=0;
        var row='';
        $(".add-new").click(function() {

            if($('#hdn_last_index').val()!=0)
            {
                i=$('#hdn_last_index').val();
            }

            var index = $(".container table tbody tr:last-child").index();
            row = '<tr class="calc" id="tr_'+i+'">' +

                '<td> <select id="ddl_from_acc_'+i+'" class="form-control kt-select2 ap-select2 ap_service_select_from_'+i+' ClsDispalyOrHide" onchange="getFromSubAccounts(this,'+i+');" style="width: 230px;" name="service" id="service"> <option value="">--</option></select></td>' +
                '<td><div class="From_sub_acc_'+i+'" id="ddl_sub_from_account_'+i+'"></div></td>' +
                '<td><select id="ddl_dimen_acc_'+i+'" class="form-control kt-select2 ap-select2 dimension_select_from_'+i+'"  style="width: 230px;"><option value="">--</option></select></td>' +
                '<td><input type="text" id="txtAmount_'+i+'" style="width: 69px;" class="ClsDispalyOrHide clstaxAmount" onkeyup="this.value=this.value.replace(/[^0-9.]/g,\'\');" onchange="display_price(i)" /></td>' +
                '<td><textarea id="txt_comment_'+i+'" class="ClsDispalyOrHide" alt="'+i+'"></textarea></td>' +
                '<td><input type="submit" value="Remove" class="btn btn-info btnSubmit" alt="'+i+'"/></td>'+
                '</tr>';

            $class='ap_service_select_from_'+i;

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_accounts', format: 'json'}, function (data) {
                AxisPro.PrepareSelectOptions(data,  'account_code', 'accname',$class, '--------CHOOSE FROM ACCOUNT---------');
            });

            $cls_to='ap_service_select_to_'+i;

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_accounts', format: 'json'}, function (data) {
                AxisPro.PrepareSelectOptions(data,  'account_code', 'accname', $cls_to, '--------CHOOSE TO ACCOUNT---------');
            });

            $class_dim='dimension_select_from_'+i;

            AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_dimensions', format: 'json'}, function (data) {
                AxisPro.PrepareSelectOptions(data, 'id', 'name', $class_dim, '--------CHOOSE Dimension---------',function()
                {

                    /*------------------ASSIGNED DIMENSION AGAINST USER------*/
                    AxisPro.APICall('POST',
                        ERP_FUNCTION_API_END_POINT + "?method=get_dimen_id_againstuser",'hdn_modify='+$('#hdn_modify_voucher').val(),
                        function (data) {
                            $('.'+$class_dim).val(data.dim_id);
                        }
                    );

                    /*------------------------------END-------------------------*/


                });
            });


            $(".table-responsive table").append(row);

            $('#hdn_total_Rows').val(i);

            $('.clsFromDate').datepicker();



            if($('#hdn_last_index').val()!='0')
            {
                $('#hdn_last_index').val(parseInt($('#hdn_last_index').val())+1);
            }
            else
            {
                i++;
            }





        });


        function getFromSubAccounts(event,id)
        {
            var from_accnt=event.value;

            AxisPro.APICall('POST',
                ERP_FUNCTION_API_END_POINT + "?method=get_from_subacc",'from_account='+from_accnt+'&id='+id,
                function (data) {
                    $('.From_sub_acc_'+id).html(data);
                    $('#ddl_sub_from_account_'+id).show();
                }
            );
        }



        function getToSubAccounts(event,id)
        {
            var to_accnt=event.value;


            AxisPro.APICall('POST',
                ERP_FUNCTION_API_END_POINT + "?method=get_to_subacc",'to_account='+to_accnt+'&id='+id,
                function (data) {
                    $('.To_sub_acc_'+id).html(data);
                    $('#ddl_sub_to_account_'+id).show();
                }
            );
        }

        function display_price(alt)
        {
            var id=alt-1;
            var amount=$('#txtAmount_'+id).val();
            $('#lbl_total_'+id).html(amount);

        }


        function Chk_ref_exist(event,id)
        {
            var refn_number=$('#txt_voucher_no_'+id).val();
            AxisPro.APICall('POST',
                ERP_FUNCTION_API_END_POINT + "?method=check_refencenumber",'refn_number='+refn_number,
                function (data) {
                    if(data=='1')
                    {
                        alert('Refenece already exists');
                        $('#txt_voucher_no_'+id).val('');
                    }

                }
            );
        }

        function calcluate_tax(event,id)
        {
            var alt=id;
            var tax_ddl_val=$('#ddl_tax_'+alt).val();
            if(tax_ddl_val=='1')
            {
                if($('#service').val()=='')
                {
                    alert('Select vat account first');
                    $('#ddl_tax_'+alt).val(0);

                }
                else
                {
                    var amount_wh_tax=$('#txtAmount_'+alt).val()*5/100;
                    var summ=parseInt($('#txtAmount_'+id).val())+parseInt(amount_wh_tax);
                    $('#lbl_total_'+alt).html(summ);
                }

            }
            else
            {
                $('#lbl_total_'+alt).html($('#txtAmount_'+id).val());
            }
        }


        $(document).on("click",".btnSubmit", function() {
            var alt_id=$(this).attr('alt');
            $('#tr_'+alt_id).remove();
        });
        var chkflag='';
        $(".payment-submit").click(function() {


            if($('.ClsFrombank').val()=='')
            {
                alert('Select From');
            }
            else if($('.ClsPayType ').val()=='')
            {
                alert('Select payment type');
            }
            else if($('.ClsPayTo ').val()=='')
            {
                alert('Select '+$('.lbl_txt').html());
            }
            else
            {
                if($('#hdn_modify_voucher').val()=='1')
                {
                    var tot_rows= $('#hdn_last_index').val()-1;
                }
                else
                {
                    var tot_rows= $('#hdn_total_Rows').val();
                }

                var tax_account=$('#service').val();

                var i;
                var payment_data = [];
                var payment_array = [];

                var v_date=$('#txt_date').val();
                var v_refer=$('#txt_reference').val();
                var v_from_bank_acc=$('#bank_account').val();
                var pay_type=$('.ClsPayType ').val();
                var pay_to=$('.ClsPayTo').val();
                var being=$('#txt_being').val();
                var cheq_no=$('#txt_chq_no').val();
                var cheq_date=$('#txt_chq_date').val();
                var supplier=$('.ClsSupplier').val();
                var customer=$('.ClsCustomer ').val();
                var modify_voucher=$('#hdn_modify_voucher').val();
                var modify_trans_no=$('#hdn_trans_no').val();

                var cntr='0';
                var tot_amount=[];
                for(i=0;i<=tot_rows;i++)
                {

                    var jv_from=$('#ddl_from_acc_'+i).val();
                    var jv_from_sub=$('#ddl_from_sub_'+i).val();
                    var dimension=$('#ddl_dimen_acc_'+i).val();
                    var amount=$('#txtAmount_'+i).val();
                    var remarks=$('#txt_comment_'+i).val();
                    var person_id=$('#ddl_person_'+i).val();

                    if(jv_from!=undefined)
                    {
                        payment_data.push({jv_from:jv_from,jv_from_sub:jv_from_sub,
                            amount:amount,memo:remarks,person_id:person_id,dimension:dimension});
                        tot_amount.push(amount);
                    }

                    if(amount=='')
                    {
                        cntr++;
                    }
                    //payment_data = [];
                }


                //console.log(tot_amount);

                var confrm=confirm('Please check and confirm ? Ready to create?')
                if(confrm==true)
                {
                    if(cntr-1==tot_rows)
                    {
                        alert('Some of the fields values are missing , please check and update');
                    }
                    else
                    {

                        var head_person_id='';

                        if(supplier!='' || supplier!=undefined)
                        {
                            head_person_id=supplier;
                        }

                        if(customer!='' || customer!=undefined)
                        {
                            head_person_id=customer;
                        }

                        AxisPro.APICall('POST',
                            ERP_FUNCTION_API_END_POINT + "?method=process_voucher",{'payment_data':payment_data,
                                'v_date':v_date,'v_refer':v_refer,'v_from_bank_acc':v_from_bank_acc,'pay_type':pay_type,'pay_to':pay_to,'being':being,
                                'chq_no':cheq_no,'cheq_date':cheq_date,'page_type':$('#hdn_page_type').val(),'head_person_id':head_person_id
                                ,'modify_voucher':modify_voucher,'modify_trans_no':modify_trans_no,'bnk_account':$('#bank_account').val(),
                                'date_':$('#txt_date').val(),'tot_amount':tot_amount},
                            function (data) {
//alert(data);
                                if(data.trans_no!='')
                                {
                                    if(data.trans_type=='1')
                                    {
                                        window.location.href =$('#hdn_url').val()+ "ERP/gl/gl_bank.php?AddedID="+data.trans_no;
                                    }

                                    if(data.trans_type=='2')
                                    {
                                        window.location.href =$('#hdn_url').val()+ "ERP/gl/gl_bank.php?AddedDep="+data.trans_no;
                                    }

                                    if(data.trans_type=='error')
                                    {
                                        alert(data.trans_no);
                                    }


                                }
                                else
                                {
                                    alert('Error occured during the voucher creation');
                                }
                            }
                        );
                    }


                }
            }


        });

        $('.ClsPayType').change(function()
        {
            if($('.ClsPayType').val()=='2')
            {
                $('.clsShowhide').css('display','block');
            }
            else
            {
                $('.clsShowhide').css('display','none');
            }
        });

        $('.ClsPayTo').change(function()
        {
            if($('.ClsPayTo').val()=='2')
            {
                $('.clsShowCustomer').css('display','block');
                $('.clsShowSuplier').css('display','none');
            }
            else if($('.ClsPayTo').val()=='3')
            {
                $('.clsShowSuplier').css('display','block');
                $('.clsShowCustomer').css('display','none');
            }
            else
            {
                $('.clsShowCustomer').css('display','none');
                $('.clsShowSuplier').css('display','none');
            }
        });


        $('.ClsFrombank').change(function()
        {
            if($('#hdn_page_type').val()=='1')
            {
                AxisPro.APICall('POST',
                    ERP_FUNCTION_API_END_POINT + "?method=get_bnk_balance",'bank_id='+$('.ClsFrombank').val(),
                    function (data) {
                        $('.clsShowBnkBlnce').css('display','block');
                        $('#Dispbankbalnce').html(data);
                    }
                );
            }

        });

    </script>