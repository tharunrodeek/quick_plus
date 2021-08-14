<?php include "header.php" ?>
<?php //$isPDCWindow = true;
$today = Today();
?>
<style type="text/css">

        </style>
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
                                                <h3>Employee Gratuity Report </h3>
                                            </div>
                                        </div>

                                        <div class="row">
                                           <div class="col-lg-12">
                                            <form id="form-filter" class="form-horizontal" method="POST" action="<?= $erp_url ?>API/hub.php?method=export_esb" style="width:100%;">
                                             <table id="filter_table" style="width:100%;margin-bottom:10px;">
                                                 <tr>

                                           <!-- <td width="15%">
                                                <select class="form-control Clsyear"
                                                        name="year"  >
                                                    <option value="">--SELECT--</option>
                                                    <?php /*echo $get_year_dropdown; */?>
                                                </select>
                                            </td>-->
                                            <td id="deptSelect" width="30%">
                                                <select class="select2" style="width:100%;" id="department_for_filter" name="dept_id">
                                                </select>
                                            </td>
                                            <td id="empSelect" width="30%">
                                                <select class="select2" style="width:100%;" id="emp_for_filter" name="emp_id"  >
                                                    <option value="">Choose Department First</option>
                                                </select>
                                            </td>



                                    </tr>

                                </table>
                                        <input type="hidden" name="export_excel" value="1" />
                                        <input type="submit" name="exportCSV" value="Export CSV" class="btn btn-success"/>
                                        <input type="button" name="ViewData" value="View Data" class="btn btn-success ClsViewData"/>
                            </form>
                            <table id="esb_datatable" class="table dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>DEPARTMENT</th>
                                        <th>EMPLOYEE NAME</th>
                                        <th>JOIN DATE</th>
                                        <th>YEARS COMPLETED</th>
                                        <th>LOAN PENDING AMOUNT</th>
                                        <th>WARNING DEDUCTION PENDING AMOUNT</th>
                                        <th>ESB Calculated</th>
                                    </tr>
                                </thead>
                                
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
<script src="assets/plugins/general/sweetalert2/dist/sweetalert2.min.js" type="text/javascript"></script>
<script src="assets/plugins/general/js/global/integration/plugins/sweetalert2.init.js" type="text/javascript"></script>

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
       export_link = '<?= $erp_url ?>API/hub.php?method=export_service_commission_report',
       // reference_no = $('#reference_no_filter').val(),
       trans_date_from = $('#trans_date_from_filter').val(),
       trans_date_to = $('#trans_date_to_filter').val(),
       // trans_no_from = $('#trans_no_from_filter').val(),
       customer_id   = $('#leave_type_for_filter').val(),
       paid_status   = $('#department_for_filter').val();

       let d1 = new Date(trans_date_from), d2 = new Date(trans_date_to);
       if(d1 > d2){
        let temp = d2;
        d2 = d1;
        d1 = temp;
    }

    if(Math.round((d2 - d1)/(1000*60*60*24))<=31){
        export_link += `&&trans_date_from=${trans_date_from}&&trans_date_to=${trans_date_to}&&paid_status=${paid_status}&&customer_id=${customer_id}`;
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

getSelectedUsernames = () =>{
    let data = $('#user_id_for_filter').select2('data'), 
    selected_users = [];
    data.forEach(function (item) { 
    // alert(item.text); 
    selected_users.push(item.text);
})
// console.log(selected_users);
return selected_users;
}

$(document).ready(function(){
    // assign_export_link();
    $('[data-toggle-second="tooltip"]').tooltip();




AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT,{method: 'get_all_department', format: 'json'}, function (data) {
    var deptSelectData = '';
    deptSelectData += '<option value="" selected disabled>Choose Department</option>';
    data.forEach(function (item) {
        deptSelectData += '<option value="' + item.id + '">' + item.description + '</option>';
    });
                // alert(deptSelectData);
                $("#department_for_filter").append(deptSelectData);
                // $("#customer_id_for_editing").append(deptSelectData);
                $(".select2").select2();
            });

$("#department_for_filter").change(function() {
    AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_all_employees",'dept_id='+$("#department_for_filter").val(), function (data) {
        var empSelectData = '';
        empSelectData += '<option value="">All Employee</option>';
        data.forEach(function (item) {
            empSelectData += '<option value="' + item.id + '">' + item.Emp_name + '</option>';
        });
                $("#emp_for_filter").html(empSelectData);
                $(".select2").select2();

            });




    });

$('.ClsViewData').click(function()
{
    $('#esb_datatable').dataTable().fnDestroy();

    emp_gpssa_table = $('#esb_datatable').dataTable({
        dom: 'Bfrtip',
        // stateSave: true,
        "bLengthChange": false,
        // "pageLength": 50,
        // "paging": false,
        // "bInfo": false,
        "ordering": false,
        buttons: [
            {
                text: '<i class="menu-icon flaticon-refresh"></i>',
                // className:'btn btn-secondary',
                action: function ( e, dt, node, config ) {
                    $('#form-filter')[0].reset();

                    $('.select2').trigger("change");
                    emp_gpssa_table.api().search( '' ).columns().search( '' ).draw();
                    emp_gpssa_table.api().ajax.reload();
                }
            },
            {
                extend:'colvis',
                text:'<i class="fa fa-eye"></i>'

            }
        ],
        "bProcessing": true,
        "serverSide": true,
        "searching": false,
        "ajax":{
            url :ERP_FUNCTION_API_END_POINT+"?method=get_emp_esb_list_for_datatable",
            type: "POST",
            "data": function ( data ) {
               // data.year = $('.Clsyear').val();
                data.dept_id = $('#department_for_filter').val();
                data.emp_id = $('#emp_for_filter').val();

            },
            error: function(){
                $("#emp_leave_processing").css("display","none");
            }
        }
    });
});







/*$('#year_for_filter,#department_for_filter,#emp_for_filter,#leave_type_for_filter').on("keyup change",function(){
    // emp_leave_table.ajax.reload();
    reloadDatatable();
});*/
});
/*function reloadDatatable(){
    // assign_export_link();
    emp_leave_table.api().ajax.reload();
}*/


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