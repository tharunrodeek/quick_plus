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
                                    <?= trans('EMPLOYEE LEAVE HISTORY') ?>
                                </h3>
                            </div>
                        </div>

                        <!--begin::Form-->
                        <form class="kt-form kt-form--label-right" id="employee_form">

                        </form>
                </div>

                <div class="row" style="width: 100%;">
                    <div class="col-md-8">
                        <canvas id="barChart" ></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="hdndept_id" />
<input type="hidden" id="hdnemp_id" />
<input type="hidden" id="hdnedit" />
<?php include "footer.php"; ?>
<script>

    data();

    function data()
    {
        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT + "?method=get_employee_leave_history", 'empl_id='+'<?php echo $_GET['id']; ?>', function (data) {


//console.log(data.levae_names);

            var ctx = document.getElementById("barChart").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels:data.levae_names,
                    datasets: [{
                        label: '# of Employee Leaves in ',
                        data: data.leave_days,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255,99,71, 0.2)',
                            'rgba(240,128,128, 0.2)',
                            'rgba(184,134,11, 0.2)',
                            'rgba(238,232,170, 0.2)',
                            'rgba(124,252,0, 0.2)',
                            'rgba(0,100,0, 0.2)',
                            'rgba(50,205,50, 0.2)',
                            'rgba(0,250,154, 0.2)'


                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(255,99,71, 1)',
                            'rgba(240,128,128, 1)',
                            'rgba(184,134,11, 1)',
                            'rgba(238,232,170, 1)',
                            'rgba(124,252,0, 1)',
                            'rgba(0,100,0, 1)',
                            'rgba(50,205,50,1)',
                            'rgba(0,250,154, 1)'

                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });



        });
    }











</script>