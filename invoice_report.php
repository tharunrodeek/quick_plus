<?php include "header.php" ?>
<?php //$isPDCWindow = true;
$today = Today();
$one_month_before = date('d-M-Y', strtotime("-1 month", strtotime($today))); ?>

<div class="kt-container  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch">
    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

            <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                <div class="row">
                    <div class="col-md-12">
                        <!-- form card cc payment -->
                        <div class="card">
                            <div class="card-body" id="cashier-form-div">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3>Invoice Report</h3>
                                    </div>
                                </div>

                                <div class="row">
                                 <div class="col-lg-12">
                                    <form id="form-filter" class="form-horizontal" style="width:100%;">
                                       <table style="width:100%;margin-bottom:10px;">
                                           <tr>
                                            <td width="20%">
                                                <input type="text" class="form-control" placeholder="Reference No." id="reference_no_filter" onchange="reloadDatatable();" value="<?php echo ($_GET['reference_no']!='') ? $_GET['reference_no']: '';?>">
                                            </td>
                                            <td width="10%">
                                                <input type="text" class="form-control" placeholder="# from" id="trans_no_from_filter" onchange="reloadDatatable();" value="<?php echo ($_GET['trans_no_from']!='') ? $_GET['trans_no_from']: '1';?>">
                                            </td>
                                            <td width="10%">
                                                <input type="text" class="form-control" placeholder="# to" id="trans_no_to_filter" onchange="reloadDatatable();" value="<?php echo ($_GET['trans_no_to']!='') ? $_GET['trans_no_to']: '999999';?>">
                                            </td>
                                            <td width="15%">
                                                <input type="text" class="form-control ap-datepicker" placeholder="Date from" id="trans_date_from_filter" readonly onchange="reloadDatatable();" value="<?php echo ($_GET['trans_date_from']!='') ? sql2date($_GET['trans_date_from']): $one_month_before;?>">
                                            </td>
                                            <td width="15%">
                                                <input type="text" class="form-control ap-datepicker" placeholder="Date to" id="trans_date_to_filter" readonly onchange="reloadDatatable();" value="<?php echo ($_GET['trans_date_to']!='') ? sql2date($_GET['trans_date_to']): $today;?>">
                                            </td>
                                            <td id="customerSelect" width="30%">
                                                <select class="select2" style="width:100%;" id="customer_id_for_filter" onchange="reloadDatatable();">
                                                </select>
                                            </td>
                                            <td>
                                                <div class="dropdown show" id="main_export_div" data-toggle-second="tooltip">
                                                  <a class="btn btn-primary dropdown-toggle btn-block" href="#" role="button" id="dropdownMenuLinkExportReport" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Export Report to
                                                </a>

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLinkExportReport">
                                                    <a class="dropdown-item" target="_blank" href="#" id="export_to_excel_btn"><i class="fa fa-file-excel"></i>Excel</a>
                                                    <a class="dropdown-item" onclick="return confirm('Are you sure to export to PDF? We recommend you to choose EXCEL because PDF report may be congested due to large data and columns')" target="_blank" href="#" id="export_to_pdf_btn"><i class="fa fa-file-pdf"></i>Pdf</a>
                                                </div>
                                            </div>
                                          <!--   <a class="btn btn-success export_btn text-white" target="_blank" href="#" id="export_to_excel_btn">EXPORT TO EXCEL</a>
                                          <a class="btn btn-primary export_btn text-white" onclick="return confirm('Are you sure to export to PDF? We recommend you to choose EXCEL because PDF report may be congested due to large data and columns')" target="_blank" href="#" id="export_to_pdf_btn">EXPORT TO PDF</a> -->
                                      </td>
                                  </tr>
                              </table>
                          </form>
                          <table id="invoice_report" class="table dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <!-- <th>#</th> -->
                                    <th>Reference</th>
                                    <th width="10%">Date</th>
                                    <th width="8%">Time</th>
                                    <th>Customer</th>
                                    <th>Total Amount</th>
                                    <th>Cash</th>
                                    <th>Debit/Credit Card</th>
                                    <th>Bank Transfer</th>
                                    <th>Others</th>
                                    <th>Amount Received</th>
                                    <th>Balance Amount</th>
                                    <!-- <th width="20%">Receipts & Payment Methods</th> -->
                                    <th>GL</th>
                                    <th>Print</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="4" style="text-align:right;">Total:</th>
                                    <th id="total_sum"></th>
                                    <th id="pay_cash_sum"></th>
                                    <th id="pay_creditcard_sum"></th>
                                    <th id="pay_bank_sum"></th>
                                    <th id="pay_other_sum"></th>
                                    <th id="total_received_sum"></th>
                                    <th id="total_balance_sum"></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                </div>

            </div>
        </div>
        <!-- /card  -->

    </div>
</div>









</div>

<!-- end:: Content -->
</div>
</div>
</div>


<script src="assets/plugins/general/jquery/dist/jquery.js" type="text/javascript"></script>
<!-- <script src="assets/numpad/jquery.numpad.js"></script> -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<script src="assets/plugins/general/popper.js/dist/umd/popper.js" type="text/javascript"></script>
<script src="assets/js/config.js" type="text/javascript"></script>

<script src="assets/plugins/general/sticky-js/dist/sticky.min.js" type="text/javascript"></script>

<script src="assets/js/scripts.bundle.js" type="text/javascript"></script>
<script src="assets/plugins/general/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"
type="text/javascript"></script>

<script src="assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>


<script src="assets/js/axispro.js" type="text/javascript"></script>
<script src="assets/js/jquery-dateformat.min.js" type="text/javascript"></script>
<script src="assets/js/jquery.doubleScroll.js" type="text/javascript"></script>


<!--    <script src="assets/plugins/general/toastr/build/toastr.min.js" type="text/javascript"></script>-->
<script src="assets/plugins/general/sweetalert2/dist/sweetalert2.min.js" type="text/javascript"></script>
<script src="assets/plugins/general/js/global/integration/plugins/sweetalert2.init.js"
type="text/javascript"></script>
<!-- jQuery.NumPad -->
<script type="text/javascript">
assign_export_link = () => {
 let dropdownMenuLinkExportReport = document.getElementById("dropdownMenuLinkExportReport");
 let exportMainDiv = document.getElementById("main_export_div");
 let pdf_btn = document.getElementById("export_to_pdf_btn");
 let excel_btn = document.getElementById("export_to_excel_btn");
 excel_btn.href = pdf_btn.href = "javascript:;";
 let 
 export_title,
 export_link = '<?= $erp_url ?>API/hub.php?method=export_invoice_report',
 reference_no = $('#reference_no_filter').val(),
 trans_date_from = $('#trans_date_from_filter').val(),
 trans_date_to = $('#trans_date_to_filter').val(),
 trans_no_from = $('#trans_no_from_filter').val(),
 customer_id = $('#customer_id_for_filter').val(),
 trans_no_to = $('#trans_no_to_filter').val();

 let d1 = new Date(trans_date_from), d2 = new Date(trans_date_to);
 if(d1 > d2){
    let temp = d2;
    d2 = d1;
    d1 = temp;
 }

 if(Math.round((d2 - d1)/(1000*60*60*24))<=31){
    export_link += `&&reference_no=${reference_no}&&trans_no_to=${trans_no_to}&&trans_date_from=${trans_date_from}&&trans_date_to=${trans_date_to}&&trans_no_from=${trans_no_from}&&customer_id=${customer_id}`;
    pdf_btn.href = export_link+'&&export_type=pdf'; 
    excel_btn.href = export_link+'&&export_type=excel';   
    dropdownMenuLinkExportReport.classList.remove("disabled","export_disabled");
    pdf_btn.classList.remove("disabled");
    excel_btn.classList.remove("disabled");
    export_title='Export Report to Excel / PDF';
}else{
   dropdownMenuLinkExportReport.classList.add("disabled","export_disabled");
   pdf_btn.classList.add("disabled");
   excel_btn.classList.add("disabled");
   export_title = 'Sorry !!! You can export maximum 31 Days\' data';

   }

   // exportMainDiv.setAttribute('title', export_title);
   exportMainDiv.setAttribute('data-original-title', export_title);
}


$(document).ready(function(){
    assign_export_link();
    initialiseDatepicker();
    $('[data-toggle-second="tooltip"]').tooltip();
    // $('.export_disabled').click(function(){
    //     alert('Sorry!! You can export only 3 months (90 Days) data');
    // });
 //     $("#customer_id_for_filter").select2({
 //        placeholder: 'Select a Customer',
 //  ajax: { 
 //   url: ERP_FUNCTION_API_END_POINT+"?method=get_customers_for_select2",
 //   type: "post",
 //   dataType: 'json',
 //   delay: 250,
 //   data: function (params) {
 //    return {
 //      searchTerm: params.term // search term
 //    };
 //   },
 //   processResults: function (response) {
 //     return {
 //        results: response
 //     };
 //   },
 //   cache: true
 //  }
 // });
// $('#customer_id_for_filter').select2({
//         placeholder: 'Select a Customer',
//         minimumInputLength: 3,
//         ajax: {
//           url: ERP_FUNCTION_API_END_POINT+"?method=get_customers_for_select2",
//           dataType: 'json',
//           type: "GET",
//           delay: 250,
//           processResults: function (data) {
//             return {
//               results: data
//             };
//           },
//           cache: true
//         }
//       });
var params = {
    method: 'get_all_customers'
};
AxisPro.APICall("GET", ERP_FUNCTION_API_END_POINT, params, function (data) {
    var customerSelectData = '';
    customerSelectData += '<option value="" selected disabled>Select a Customer</option>';
    data.forEach(function (item) {
        customerSelectData += '<option value="' + item.debtor_no + '">' + item.name + '</option>';
    });
                // alert(customerSelectData);
                $("#customer_id_for_filter").append(customerSelectData);
                $("#customer_id_for_editing").append(customerSelectData);
                $(".select2").select2();
            });

invoice_report_table = $('#invoice_report').dataTable({
    dom: 'Bfrtip',
    stateSave: true,
    "bLengthChange": false,
    "ordering": false,
    buttons: [
    {
        text: '<i class="menu-icon flaticon-refresh"></i>',
            // className:'btn btn-secondary',
            action: function ( e, dt, node, config ) {
                // dt.ajax.reload();
                $('#form-filter')[0].reset();
                // $('#customer_id_for_filter').trigger('change');
                // $('#customer_id_for_filter').val(null).trigger('change');
                $('.select2').trigger("change");
                // $('#supplier_id_for_filter').trigger("change");
                invoice_report_table.api().search( '' ).columns().search( '' ).draw();
                invoice_report_table.api().ajax.reload();
            }
        },
        {
            extend:'colvis',
            text:'<i class="fa fa-eye"></i>'
            
        }
        ],
        // "order": [[ 5, "desc" ]],
        // "columnDefs": [
        // { 
        //     "orderable": false, 
        //     "targets": [6,12] 
        // }
        // ],
        "bProcessing": true,
        "serverSide": true,
        // 'searching': false,
        "ajax":{
            url :ERP_FUNCTION_API_END_POINT+"?method=get_invoice_list_for_datatable",
            type: "POST",
            "data": function ( data ) {
                data.reference_no = $('#reference_no_filter').val();
                data.trans_date_from = $('#trans_date_from_filter').val();
                data.trans_date_to = $('#trans_date_to_filter').val();
                data.trans_no_from = $('#trans_no_from_filter').val();
                data.trans_no_to = $('#trans_no_to_filter').val();
                data.customer_id = $('#customer_id_for_filter').val();

            // data.date = $('#date').val();
                // data.address = $('#address').val();
            },
            dataSrc: function ( data ) {
           total_sum = data.total_sum;
           pay_cash_sum = data.pay_cash_sum;
           pay_creditcard_sum = data.pay_creditcard_sum;
           pay_bank_sum = data.pay_bank_sum;
           pay_other_sum = data.pay_other_sum;
           total_balance_sum = data.total_balance_sum;
           total_received_sum = data.total_received_sum;

           return data.data;
         },    
            error: function(){
              $("#invoice_report_processing").css("display","none");
          }
      },
      drawCallback: function( settings ) {
        var api = this.api();
        // alert(total_sum);
        $('#total_sum').html(total_sum);
        $('#pay_cash_sum').html(pay_cash_sum);
        $('#pay_creditcard_sum').html(pay_creditcard_sum);
        $('#pay_bank_sum').html(pay_bank_sum);
        $('#pay_other_sum').html(pay_other_sum);
        $('#total_received_sum').html(total_received_sum);
        $('#total_balance_sum').html(total_balance_sum);

        }
  });

// $(".select2").select2();

$('#trans_no_from_filter,#reference_no_filter,#trans_date_from_filter,#trans_date_to_filter,#trans_no_to_filter').on("keyup change",function(){
    // invoice_report_table.ajax.reload();
    reloadDatatable();
});
});
function reloadDatatable(){
    assign_export_link();
    invoice_report_table.api().ajax.reload();
}

function monthsDiff(d1, d2) {
  let date1 = new Date(d1);
  let date2 = new Date(d2);
  let years = yearsDiff(d1, d2);
  let months =(years * 12) + (date2.getMonth() - date1.getMonth()) ;
  return months;
}
function yearsDiff(d1, d2) {
    let date1 = new Date(d1);
    let date2 = new Date(d2);
    let yearsDiff =  date2.getFullYear() - date1.getFullYear();
    return yearsDiff;
}
function initialiseDatepicker(){

    $('.ap-datepicker').datepicker({format: $('#date_format').val()});
}

var KTAppOptions = {
    "colors": {
        "state": {
            "brand": "#5d78ff",
            "light": "#ffffff",
            "dark": "#282a3c",
            "primary": "#5867dd",
            "success": "#34bfa3",
            "info": "#36a3f7",
            "warning": "#ffb822",
            "danger": "#fd3995"
        },
        "base": {
            "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
            "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
        }
    }
};




$(document).ajaxComplete(function (event, xhr, settings) {

    var responseText = xhr.responseText;
    var responseJson = $.parseJSON(responseText);

    if (responseJson.reference_no === 'LOGIN_TIME_OUT') {

        swal.fire(
            'Login TimeOut !',
            responseJson.msg,
            'error'
            ).then(function () {
                window.location.reload();
            });

        }

    });

</script>


<?php include "footer.php"; ?>