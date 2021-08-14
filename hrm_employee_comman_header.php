<?php
include_once($path_to_root . "/API/API_HRM_Call.php");
$access_dta=new API_HRM_Call();
$employee_basic_data=$access_dta->get_basic_employee_details_for_header();

?>
<style>
.ClsBold
{
    font-weight:bold;
    font-size: 11pt;
    width: 45%;
    background-color: none;
    color: black;
    padding: 5px;
}
.ClsBold-Second
{
    font-weight:bold;
    font-size: 11pt;
    width: 65%;
    background-color: none;
    color: black;
    padding: 5px;
}
.clsEmpbasicDetails
{
    border: 1px solid #ccc;
    border-radius: 29px;
    margin-top: 1%;
    padding: 2%;
}
.boldcls{font-weight:bold;color: black;}

</style>

<div class="row clsEmpbasicDetails">
    <div class="col-sm-4">
        <table>
            <tr>
                <td class="ClsBold">Employee Name</td><td class="boldcls">:</td><td><?php echo $employee_basic_data['employee_name']; ?></td>
            </tr>
            <tr style="height: 7px;"></tr>
            <tr>
                <td class="ClsBold">Department</td><td class="boldcls">:</td><td><?php echo $employee_basic_data['department']; ?></td>
            </tr>

        </table>
    </div>
    <div class="col-sm-4">
        <table>
            <tr>
                <td class="ClsBold-Second">Designation</td><td class="boldcls">:</td><td><?php echo $employee_basic_data['designation']; ?></td>
            </tr>
            <tr style="height: 7px;"></tr>
            <tr>
                <td class="ClsBold-Second">Date of join</td><td class="boldcls">:</td><td><?php echo date('d-m-Y',strtotime($employee_basic_data['joindate'])); ?></td>
            </tr>
        </table>
    </div>
    <div class="col-sm-4">
        <table>
            <tr>
                <td class="ClsBold-Second">Line Manager</td><td class="boldcls">:</td><td><?php echo $employee_basic_data['linemanager']; ?></td>
            </tr>
            <tr style="height: 7px;">
                 <td class="ClsBold-Second">Head Of Dept/Supervisor</td><td class="boldcls">:</td><td><?php echo $employee_basic_data['head_of_dept']; ?></td>
            </tr>

        </table>
    </div>
</div>