<?php
include_once($path_to_root . "/API/API_HRM_Document_Request.php");
$call_obj=new API_HRM_Document_Request();
$data_array=$call_obj->getdashboardData_HRM();
$data=$call_obj->get_employee_leave_count();

?>
<style>
.ClsBold
{
    font-weight:bold;
    font-size: 11pt;
    width: 45%;
    background-color: #b5bafe;
    color: white;
    padding: 5px;
}
.ClsBold-Second
{
    font-weight:bold;
    font-size: 11pt;
    width: 65%;
    background-color: #b5bafe;
    color: white;
    padding: 5px;
}
.clsEmpbasicDetails
{
    border: 1px solid #ccc;
    border-radius: 29px;
    margin-top: 1%;
    padding: 2%;
}

.nav-pills .nav-item .nav-link.active, .nav-pills .nav-item .nav-link:active, .nav-pills .nav-item .nav-link:hover, .nav-tabs .nav-item .nav-link.active, .nav-tabs .nav-item .nav-link:active, .nav-tabs .nav-item .nav-link:hover
{
    font-weight: bold;
}
/*#codeof_conduct img
{
    width: 13% !important;
    border: 1px solid #ccc;
    padding: 11px !important;
    border-radius: 21px;
}*/
.svg-icon
{
       color: #fff;
       font-size: 24pt;
       padding-left: 48%;
    font-weight: bold;
}

</style>

<!--<div class="row classic-tabs clsEmpbasicDetails">

    <div class="card card-custom bg-primary gutter-b" style="    height: 119px;
    border-radius: 15px;
    cursor: pointer;
    width: 15%;margin: 0px auto;" data-toggle="modal" data-target="#myModalDialog">
        <div class="card-body">
                                        <span class="svg-icon svg-icon-3x svg-icon-white ml-n2"
                                        >
                                         <?php /*echo $data['tot']; */?>
                                        </span>
            <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3" style="color:#fff;font-size: 16pt;">Total Leaves</div>


        </div>
    </div>

    <div class="card card-custom bg-secondary gutter-b" style="    height: 119px;
    border-radius: 15px;
    cursor: pointer;
    width: 15%;margin: 0px auto;" data-toggle="modal" data-target="#myModalDialog">
    <div class="card-body">
                                        <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">
                                            <?php /*echo $data['pend']; */?>
                                        </span>
        <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3" style="color:#fff;font-size: 14pt;">Pending Leaves</div>


    </div>
</div>


    <div class="card card-custom bg-success gutter-b" style="    height: 119px;
    border-radius: 15px;
    cursor: pointer;
    width: 15%;margin: 0px auto;" data-toggle="modal" data-target="#myModalDialog">
        <div class="card-body">
                                        <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">
                                         <?php /*echo $data['appve']; */?>
                                        </span>
            <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3" style="color:#fff;font-size: 14pt;">Approved</div>


        </div>
    </div>
    <div class="card card-custom bg-warning gutter-b" style="    height: 119px;
    border-radius: 15px;
    cursor: pointer;
    width: 15%;margin: 0px auto;" data-toggle="modal" data-target="#myModalDialog">
        <div class="card-body">
                                        <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">
                                         <?php /*echo $data['loan']; */?>
                                        </span>
            <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3" style="color:#fff;font-size: 14pt;">Loan Requests</div>


        </div>
    </div>
    <div class="card card-custom bg-dark gutter-b" style="    height: 119px;
    border-radius: 15px;
    cursor: pointer;
    width: 15%;margin: 0px auto;" data-toggle="modal" data-target="#myModalDialog">
        <div class="card-body">
                                        <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">
                                         <?php /*echo $data['pass']; */?>
                                        </span>
            <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3" style="color:#fff;font-size: 14pt;">Passport Req.</div>


        </div>
    </div>

    <div class="card card-custom bg-info gutter-b" style="    height: 119px;
    border-radius: 15px;
    cursor: pointer;
    width: 15%;margin: 0px auto;" data-toggle="modal" data-target="#myModalDialog">
        <div class="card-body">
                                        <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">
                                         <?php /*echo $data['cert']; */?>
                                        </span>
            <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3" style="color:#fff;font-size: 14pt;">Cert. Request</div>


        </div>
    </div>




</div>-->

<div class="row classic-tabs clsEmpbasicDetails">
    <ul class="nav nav-tabs md-tabs" id="myTabMD" role="tablist" style="width: 100%;">
        <li class="nav-item">
            <a class="nav-link active" id="home-tab-md" data-toggle="tab" href="#home-md" role="tab" aria-controls="home-md"
               aria-selected="true">My Leave Requests</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profile-tab-md" data-toggle="tab" href="#profile-md" role="tab" aria-controls="profile-md"
               aria-selected="false">Other Requests</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="contact-tab-md" data-toggle="tab" href="#contact-md" role="tab" aria-controls="contact-md"
               aria-selected="false">My Documents</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="codeof_conduct-tab-md" data-toggle="tab" href="#codeof_conduct" role="tab" aria-controls="codeof_conduct"
               aria-selected="false">Policy & Employee Code of Conduct</a>
        </li>

    </ul>

    <div class="tab-content card pt-9" id="myTabContentMD" style="width:100%;    height: 281px;    overflow: auto;">
        <div class="tab-pane fade show active" id="home-md" role="tabpanel" aria-labelledby="home-tab-md">
            <?php
            echo  $call_obj->get_top_leave_request();
            ?>
        </div>
        <div class="tab-pane fade" id="profile-md" role="tabpanel" aria-labelledby="profile-tab-md">
            <label>Choose Type:</label>
            <select id="ddl_type" name="ddl_type">
                <option value="0">--SELECT--</option>
                <option value="3">Passport Request</option>
                <option value="2">Certificate</option>
                <option value="4">Loan</option>
                <option value="5">NOC Request</option>
                <option value="6">Assest Request</option>
            </select>
            <div id="other_requests"></div>

            <?php
            //echo $call_obj->other_doc_requests();
            ?>
        </div>
        <div class="tab-pane fade" id="contact-md" role="tabpanel" aria-labelledby="contact-tab-md">
            <?php
            echo $call_obj->employee_documents();
            ?>
        </div>

        <div class="tab-pane fade" id="codeof_conduct" role="tabpanel" aria-labelledby="codeof_conduct-tab-md"
        style="text-align: center;width: 95%;
    margin-top: 4%;">
            <?php

            echo $call_obj->get_policy_and_code_of_conduct();

            ?>
        </div>
    </div>
</div>


<div class="row classic-tabs clsEmpbasicDetails">
    <ul class="nav nav-tabs md-tabs" id="myTabMD" role="tablist" style="width: 100%;">
        <li class="nav-item">
            <a class="nav-link active" id="home-tab-md" data-toggle="tab" href="#home-md" role="tab" aria-controls="home-md"
               aria-selected="true">My Attendences</a>
        </li>
    </ul>

    <div class="tab-content card pt-9" id="myTabContentMD" style="width:100%;    height: 281px;    overflow: auto;">
        <div class="tab-pane fade show active" id="home-md" role="tabpanel" aria-labelledby="home-tab-md">
            <?php
            echo  $call_obj->get_attendences();
            ?>
        </div>
    </div>
</div>







<div id="myModal_History" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 743px;left: -25%">
            <div class="modal-header">
                <h4 class="modal-title">View Apporval<nobr></nobr> History</h4>
                <div>

                </div>
                <button type="button" class="close" data-dismiss="modal"></button>

            </div>
            <div class="modal-body">
                <form class="kt-form kt-form--label-right" id="history_form">
                    <div class="ClsApporveAhistory"></div>
                </form>
            </div>

        </div>

    </div>
</div>

<script src="assets/plugins/general/jquery/dist/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    $('#ddl_type').change(function()
    {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=other_doc_requests"
            , 'type_id='+$('#ddl_type').val()+'&from_page=2', function (data) {
                $('#other_requests').html(data.html);
            });
    });
    $(document).on("click",".ClsviewHistory",function()
    {

        var id=$(this).attr('alt');
        var type_id=$(this).attr('alt_type');

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=view_approval_history"
            , 'type_id='+type_id+'&from_page=2'+'&alt_id='+id, function (data)
            {
                $('.ClsApporveAhistory').html(data.html);
                $('#myModal_History').modal('show');
            });
    });
</script>