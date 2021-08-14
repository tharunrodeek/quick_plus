<?php
include_once("AxisPro.php");
include_once("PrepareQuery.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

Class API_HRM_Call
{
    public function save_leave()
    {
        $leave_type =$_POST["leave_type"];
        $f_date =$_POST["ap-datepicker"];
        $t_date = ($_POST["half_full"] == 1 && ($leave_type==1 || $leave_type==3)) ? $f_date : $_POST["todate"];
        $leave_reason =$_POST["txt_reason_leave"];
        $txttasks =$_POST["txttasks"];

        $now = strtotime($t_date); // or your date as well
        $your_date = strtotime($_POST['ap-datepicker']);

        $datediff = $now - $your_date;

        $day_requested=round($datediff / (60 * 60 * 24));
        $day_requested=$day_requested+1;

        /************************GET LEAVE CHAR CODE*****************/

        $qry="select char_code,description,id from 0_kv_empl_leave_types where id='".$leave_type."'";
        $leave_char_code=db_fetch(db_query($qry));

        /*****************************END****************************/


        $emp_id='';
        $edmp_sql="SELECT a.id,CASE 
                       WHEN a.report_to=''
                      THEN 0 
                      ELSE a.report_to 
                     END AS report_to,a.leave_request_forward,a.email,a.empl_firstname,a.empl_id,m.joining
                    FROM  0_kv_empl_info AS a
                    INNER JOIN  0_users AS b ON a.id=b.employee_id
                    INNER JOIN 0_kv_empl_job as m ON a.id=m.empl_id
                    WHERE b.id=".$_SESSION['wa_current_user']->user." ";
        $result_d = db_query($edmp_sql, "Can't get your allowed user details");
        $rowData= db_fetch($result_d);

        if($leave_char_code=='sl')
        {
            $sick_leave_validation=$this->validate_sick_leave($leave_type,$rowData['joining'],$rowData['id'],$day_requested);

             if($sick_leave_validation['status']!='BEGIN')
            {
                echo json_encode(['status' => 'ERROR', 'msg' => $sick_leave_validation['status']]);
                exit;
            }
        }




        $root_url=str_replace("/ERP","",getcwd());
        $root_url=str_replace("/API","",$root_url);
        $target_dir = $root_url."/assets/uploads/";
        $filename = null;
        if($leave_char_code=='sl'){
            if(isset($_FILES["sick_leave_doc"]["name"]) && $_FILES["sick_leave_doc"]["name"]!='')
            {
                $fname=explode(".",$_FILES["sick_leave_doc"]["name"]);
                $rand=rand(10,100);
                $filename=$fname[0].'_'.$rand.'.'.$fname[1];
                $target_file = $target_dir . basename($filename);
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                $dis_msg='';

                if ($_FILES["sick_leave_doc"]["size"] > 3000000) {
                    echo json_encode(['status' => 'ERROR', 'msg' => 'File size exceeded']);
                    exit;
                }
                if($imageFileType != "pdf" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "jpg") {
                    echo json_encode(['status' => 'ERROR', 'msg' => 'File format is not allowed']);
                    exit;
                }
            }

        }


        $now = strtotime($t_date); // or your date as well
        $your_date = strtotime($_POST['ap-datepicker']);

        $datediff = $now - $your_date;

        $day_requested=round($datediff / (60 * 60 * 24));
        $day_requested=$day_requested+1;
        $pfx='';


        if($_SESSION['wa_current_user']->access=='2')
        {
            $emp_id=$_SESSION['wa_current_user']->user;
        }
        else
        {
            $emp_id=$rowData[0];
        }
        /****************CHECK LEAVE ALREADY APPLIED ON THE SAME DATE RANGE********/
        $chk_data_exist="SELECT count(a.id) as existsCnt 
                          FROM 0_kv_empl_leave_applied AS a
                          INNER JOIN 0_kv_empl_info AS b ON a.empl_id=b.id
                          WHERE b.id='".$emp_id."' AND a.date<='".date('Y-m-d',strtotime($f_date))."' 
                          AND a.t_date>='".date('Y-m-d',strtotime($t_date))."' and a.req_status='0'
                           AND a.leave_type='".$leave_type."' AND a.del_status='1' ";
        if($_POST["hdnAction"]){
            $chk_data_exist.=" AND id!='".$_POST["hdnAction"]."' ";
        }
        $leave_applied=db_fetch(db_query($chk_data_exist));
        if($leave_applied['existsCnt']=='' || $leave_applied['existsCnt']=='0')
        {
            $check_exists="SELECT id,sick_leave_doc from 0_kv_empl_leave_applied WHERE id='".$_POST["hdnAction"]."'";
            $result_data = db_query($check_exists, "Can't get your allowed user details");
            $existCnt= db_fetch_assoc($result_data);
            if(db_num_rows($result_data)==0)
            {
                $sql="select `value` from 0_sys_prefs where name='leave_request_pfx'";
                $data=db_fetch(db_query($sql));

                if($data[0]!='')
                {
                    $pfx=$data[0];
                }

                /**************************GETTING LEAVE REQUEST FLOW******/

                $user_dim_id=$this->get_user_dim();

                $qry="select * from 0_kv_empl_master_request_flow where type_id='1' 
                      and dim_id='".$user_dim_id."' and access_level='".$_SESSION['wa_current_user']->access."' ";
                $qry_data=db_fetch(db_query($qry));
                /***********************************END********************/
                if($rowData[1]=='0')
                {
                    $qry_data['level_1']='17';
                }

                $sql="INSERT into 0_kv_empl_leave_applied (reason,leave_type,days,DATE,empl_id,year,del_status,t_date,task_assigned,level,role_id,request_date,half_full_day,sick_leave_doc)
                  VALUES ('".$leave_reason."','".$leave_type."','".$day_requested."',
                     '".date('Y-m-d',strtotime($f_date))."','".$emp_id."','".date('Y')."','1'
                     ,'".date('Y-m-d',strtotime($t_date))."','".str_replace("'",'',$txttasks)."','1','".$qry_data['level_1']."','".date('Y-m-d')."','".$_POST["half_full"]."','".$filename."')";
            }
            else
            {
                $sql="UPDATE 0_kv_empl_leave_applied SET reason='".$leave_reason."',leave_type='".$leave_type."',days='".$day_requested."',
                  `DATE`='".date('Y-m-d',strtotime($f_date))."',t_date='".date('Y-m-d',strtotime($t_date))."',task_assigned='".str_replace("'",'',$txttasks)."'
                  ,half_full_day='".$_POST["half_full"]."' ";
                if(isset($_FILES["sick_leave_doc"]["name"]) && $_FILES["sick_leave_doc"]["name"]!='')
                {
                    $sql.=" ,sick_leave_doc='".$filename."' ";
                }
                $sql.=" WHERE id='".$_POST["hdnAction"]."' ";
            }

            $flag='';
            if(db_query($sql, "Can't get your allowed user details"))
            {
                $insert_id=db_insert_id();
                $pfx_to_ref=$pfx.''.$insert_id;
                $update="update 0_kv_empl_leave_applied set request_ref_no='".$pfx_to_ref."' 
                        where id='".$insert_id."'";
                db_query($update);
                $flag='1';
                if(isset($_FILES["sick_leave_doc"]["name"]) && $_FILES["sick_leave_doc"]["name"]!='')
                {
                    if (move_uploaded_file($_FILES["sick_leave_doc"]["tmp_name"], $target_file)) {
                        if($existCnt['sick_leave_doc']!=''){
                            $filename = $target_dir.$existCnt['sick_leave_doc'];
                            if (file_exists($filename) && !is_dir($filename))
                                unlink($filename);

                        }
                    }else{
                        echo json_encode(['status' => 'ERROR','msg'=>'file upload error']);
                    }
                }
                // echo json_encode(['data'=>$insert_id]);
                // exit;
                echo json_encode(['status' => 'OK', 'msg' => 'Leave applied successfully']);
                // exit;

            }
            else
            {
                echo json_encode(['status' => 'ERROR', 'msg' => 'Error occured while saving leave.']);
                exit;
            }
        }
        else
        {
            echo json_encode(['status' => 'ERROR', 'msg' => 'There is leave request applied for the same date range.']);
            exit;
        }



        if($flag=='1')
        {
            $this->sendMailToConcernPerson($rowData);
        }
    }

    public function get_applied_leaves()
    {
//3000/31*(31-0)
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);


        $sql=" SELECT  m.description,a.reason,DATE_FORMAT(a.DATE,'%d-%m-%Y') AS fromdate,
                DATE_FORMAT(DATE(a.t_date),'%d-%m-%Y') AS todate,a.days,
                CASE 
                WHEN a.req_status = 0 THEN \"Pending\"
                WHEN a.req_status = 1 THEN \"Accepted\"
                WHEN a.req_status = 2 THEN \"Rejected\"
                END AS statustext,a.id,a.leave_type,a.half_full_day,a.sick_leave_doc,a.task_assigned
                FROM 0_kv_empl_leave_applied as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                LEFT JOIN 0_kv_empl_leave_types AS m ON m.id=a.leave_type
                left join 0_users as c ON c.employee_id=b.id
                WHERE a.del_status='1'";

        if($_SESSION['wa_current_user']->access<>'2')
        {
            $sql.=" AND c.id='".$_SESSION['wa_current_user']->user."'";
        }

        $sql.= " LIMIT ".$start.",".$length." ";


        $result = db_query($sql);

        $controls_edit='';
        $controls_remove='';
        $data=[];
        while ($myrow = db_fetch_assoc($result)) {
            // $return_result[] = $myrow;

            $controls_edit='<label alt="'.$myrow['id'].'" alt_val="'.$myrow['leave_type'].'"  
                   alt_date="'.$myrow['fromdate'].'" alt_todate="'.$myrow['todate'].'" alt_days="'.$myrow['days'].'" alt_reason="'.$myrow['reason'].'" alt_task="'.$myrow['task_assigned'].'" alt_half_full="'.$myrow['half_full_day'].'" alt_sick_leave_doc="'.$myrow['sick_leave_doc'].'" class="btn btn-sm btn-primary ClsBtnEdit"><i class="flaticon-edit"></i>';

            $controls_remove=' <label alt='.$myrow['id'].' alt_val='.$myrow['leave_type'].' class=\'btn btn-sm btn-danger ClsBtnRemove\'><i class=\'flaticon-delete\'></i>
                   ';

            if($myrow['half_full_day']=='1')
            {
                $myrow['description']=$myrow['description'].' (Half Day)';
            }
            else if($myrow['half_full_day']=='2')
            {
                $myrow['description']=$myrow['description'].' (Full Day)';
            }
            switch ($myrow['statustext']) {
                case 'Pending':
                    $statusClass = "primary";
                    break;
                case 'Accepted':
                    $statusClass = "success";
                    break;
                case 'Rejected':
                    $statusClass = "danger";
                    break;
            }
            $data[] = array(
                $myrow['description'],
                $myrow['reason'],
                $myrow['fromdate'],
                $myrow['todate'],
                $myrow['days'],
                ($myrow['sick_leave_doc']!=null && $myrow['leave_type']==3) ? '<a href="assets/uploads/'.$myrow['sick_leave_doc'].'" target="_blank" >'.$myrow['sick_leave_doc'].'</a>' : '-',
                "<span class='badge badge-pill badge-".$statusClass."'>".$myrow['statustext']."</span>",
                $controls_edit,
                $controls_remove
            );


        }

        $sql_tot="SELECT  a.id
                FROM 0_kv_empl_leave_applied as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                left join 0_users as c ON c.employee_id=b.id
                WHERE a.del_status='1' ";

        if($_SESSION['wa_current_user']->access<>'2')
        {
            $sql_tot.=" AND c.id='".$_SESSION['wa_current_user']->user."'";
        }

        $result_tot=db_query($sql_tot);

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($result_tot),
            "recordsFiltered" => db_num_rows($result_tot),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);

    }

    /**
     * @return array
     * List all LeaveTypes
     */
    public function get_all_leave_types()
    {

        $sql = "SELECT * FROM 0_kv_empl_leave_types";
        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch($result)) {

            $return_result[] = $myrow;

        }

        return AxisPro::SendResponse($return_result);
    }

    function delete_sickleave_upload($id=0){
        if($id){
            $root_url=str_replace("\ERP","",getcwd());
            $root_url=str_replace("\API","",$root_url);
            $target_dir = $root_url."/assets/uploads/";
            $check_exists="SELECT id,sick_leave_doc from 0_kv_empl_leave_applied WHERE id='".$id."'";
            $result_data = db_query($check_exists, "Can't get your allowed user details");
            $existCnt= db_fetch_assoc($result_data);
            if($existCnt['sick_leave_doc']!=''){
                $filename = $target_dir.$existCnt['sick_leave_doc'];
                if (file_exists($filename) && !is_dir($filename)){
                    unlink($filename);
                }

            }
        }

    }

    public function remove_leave()
    {
        if(isset($_POST['remove_id']))
        {
            $sql = "UPDATE 0_kv_empl_leave_applied set del_status='0' WHERE id='".$_POST['remove_id']."' ";
            db_query($sql);
            $this->delete_sickleave_upload($_POST['remove_id']);
            return AxisPro::SendResponse(['status' => 'OK', 'msg' => 'Leave removed successfully']);
        }
    }

    public function get_all_emp_leaves()
    {
        $avail_leave=0;
        global $SysPrefs;
        $msg='';
        $leave_type_id=$_POST['leave_type'];

        $sql="SELECT a.id,b.joining,a.annual_leave_balance,a.skipped_annual_days
                FROM 0_kv_empl_info AS a
                INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                left join 0_users as c on c.employee_id=b.empl_id
                where c.id='".$_SESSION['wa_current_user']->user."'";

        $result_d = db_query($sql, "Can't get your allowed user details");
        $rowData= db_fetch($result_d);
        $eligible_leave='0';
        $prob_flag='';
        $prob_msg='';
        $annual_count='0';

        $qry="select char_code,description,id from 0_kv_empl_leave_types where id='".$leave_type_id."'";
        $leave_char_code=db_fetch(db_query($qry));



        $date_of_join=$rowData[1];
        //$date_of_join="2017-01-02";
        $curr_date=date('Y-m-d');
        $annual_leave_calculation_start_date='2021-01-01';


        $diff = abs(strtotime($curr_date) - strtotime($date_of_join));
        $years = floor($diff / (365*60*60*24));

        /*if($years>2)
        {
            $cnvert_join=date('m-d',strtotime($date_of_join));
            $date_of_join=date('Y').'-'.$cnvert_join;
        }*/

        /*-------------------------CHECK PROBATION PERIOD OVER---------*/

        $ts1_prob = strtotime($date_of_join);
        $ts2_prob = strtotime($curr_date);
        $year1_prob = date('Y', $ts1_prob);
        $year2_prob = date('Y', $ts2_prob);
        $month1_prob = date('m', $ts1_prob); $month2_prob = date('m', $ts2_prob);

        $diff_prob = (($year2_prob - $year1_prob) * 12) + ($month2_prob - $month1_prob)+1;

        $diff_for_leave_enchase = (($year2_prob - $year1_prob) * 12) + ($month2_prob - $month1_prob);


        /*echo $years;

        die;*/

        $maternity_flag='';
        $bereavement_flag='';
        $maternity_leave='0';
        $bereavement_leave='0';
        $sick_leave_validation_flg='';

        /*---------------------------------END--------------------------*/

        if($leave_char_code[0]=='al')
        {
            if($rowData[0]!='')
            {
                /*$last_day_month=date("t", strtotime($curr_date));

                if($last_day_month==date("d", strtotime($curr_date)))
                {
                    $ts1 = strtotime($date_of_join);
                    $ts2 = strtotime($curr_date);
                    $year1 = date('Y', $ts1);
                    $year2 = date('Y', $ts2);
                    $month1 = date('m', $ts1); $month2 = date('m', $ts2);

                    $diff = (($year2 - $year1) * 12) + ($month2 - $month1)+1;
                    $eligible_leave=$diff*2.55;
                }
                else
                {
                    $ts1 = strtotime($date_of_join); // or your date as well
                    $ts2 = strtotime($curr_date);
                    $year1 = date('Y', $ts1);
                    $year2 = date('Y', $ts2);
                    $month1 = date('m', $ts1); $month2 = date('m', $ts2);

                    $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
                    $eligible_leave=$diff*2.55;
                }*/

                /*$ts1 = strtotime($date_of_join); // or your date as well
                $ts2 = strtotime($curr_date);
                $year1 = date('Y', $ts1);
                $year2 = date('Y', $ts2);*/

                $split_annual_start_date= strtotime($annual_leave_calculation_start_date);
                $annual_start_yr = date('Y', $split_annual_start_date);

                /*if($annual_start_yr!='2022')
                {
                    $sql_year="select SUM(days) as SUM_ANU from 0_kv_empl_leave_applied
                          where leave_type='".$leave_type_id."' and req_status='1'
                           AND year between '".date('Y')."' and '".date('Y',strtotime($annual_leave_calculation_start_date))."'
                            AND empl_id='".$rowData['id']."' ";
                    $year_anu_leave=db_fetch(db_query($sql_year));

                    if($year_anu_leave['SUM_ANU']<30)
                    {
                        $year_anu_leave['SUM_ANU']=30-$year_anu_leave['SUM_ANU'];
                    }
                }*/




                if($rowData['annual_leave_balance']!=0)
                {
                    $date_of_join = strtotime($annual_leave_calculation_start_date); // or your date as well
                    $current_date = strtotime($curr_date);
                    $datediff = $current_date - $date_of_join;

                    $calculated_annual_leave=$datediff / (60 * 60 * 24)+1;

                    $eligible_leave=($calculated_annual_leave/365*30)+$rowData['annual_leave_balance'];

                }
                else
                {
                    $date_of_join = strtotime($date_of_join); // or your date as well
                    $current_date = strtotime($curr_date);
                    $datediff = $current_date - $date_of_join;

                    $calculated_annual_leave=$datediff / (60 * 60 * 24)+1;

                    $eligible_leave=$calculated_annual_leave/365*30;
                }

                if($rowData['skipped_annual_days']!=0)
                {
                    $eligible_leave=$eligible_leave+$rowData['skipped_annual_days'];
                }




                $year_anu_leave[0]=0;

                $sql_anu="select SUM(days) as SUMANU from 0_kv_empl_leave_applied
                          where leave_type='".$leave_type_id."' and req_status='1' AND empl_id='".$rowData['id']."'
                           and del_status='1' ";
                $taken_anu_leave=db_fetch(db_query($sql_anu));

                if($taken_anu_leave[0]!='')
                {
                    $reduce_leave=$taken_anu_leave[0];
                }

                if($rowData['skipped_annual_days']!=0)
                {
                    $reduce_leave=$reduce_leave+$rowData['skipped_annual_days'];
                }

                //$reduce_leave=30;
                //$reduce_leave=0;

                /*  $diff = abs(strtotime($curr_date) - strtotime($date_of_join));
                  $years = floor($diff / (365*60*60*24));*/




                /*if($rowData['annual_leave_balance']!='0')
                {
                    $eligible_leave=$eligible_leave+$rowData['annual_leave_balance'];
                }*/


                $msg=$leave_char_code[1].' Availabe :'.substr(round($eligible_leave)-$year_anu_leave[0]-$reduce_leave, 0, 2);

                $annual_count=abs(round($eligible_leave)-$reduce_leave);




                if($annual_count>60)
                {
                    $annual_count=60;
                }

                /**************************************END***********************/
            }
        }
        else if($leave_char_code[0]=='sl')
        {


            if($diff_prob<=6 && $years < 2)
            {
                $eligible_leave='0';
                $prob_flag='1';
                $prob_msg='(Kindly note that you are in probationary period. After the period you are eligible for 
                                Annual and sick leave)';
            }

            $yearEnd = date('Y') . '-12-31';

            $diff = abs(strtotime($yearEnd) - strtotime($rowData['joining']));

            $year_days=round( $diff / (60 * 60 * 24)+1);
            $year_of_join_date=date('Y',strtotime($rowData['joining']));


            if($year_days<365 &&  $year_of_join_date!=date('Y'))
            {
                $sql_sick="select SUM(days) as SUMSick from 0_kv_empl_leave_applied 
                       where leave_type='".$leave_type_id."' and req_status='1'
                       AND YEAR(DATE) between '".$year_of_join_date."' and '".date('Y')."'
                       AND empl_id='".$rowData['id']."'";
            }
            else
            {
                $sql_sick="select SUM(days) as SUMSick from 0_kv_empl_leave_applied 
                       where leave_type='".$leave_type_id."' and req_status='1' and YEAR(DATE)='".date('Y')."'

                       AND empl_id='".$rowData['id']."'";
            }

            $taken_sick_leave=db_fetch(db_query($sql_sick));

            if(empty($taken_sick_leave[0]))
            {
                $taken_sick_leave[0]='0';
            }

            if($taken_sick_leave[0]==15)
            {
                $sick_leave_validation_flg='USED_ALL_ELIGIBLE_SICK_LEAVE';
            }
            if($taken_sick_leave[0]>15 && $taken_sick_leave[0]<=30)
            {
                $sick_leave_validation_flg='VALID_FOR_HALF_DAY';
            }
            else if($taken_sick_leave[0]>30)
            {
                $sick_leave_validation_flg='VALID_FOR_UNPAID';
            }



            $msg=$leave_char_code[1].' Taken :'.$taken_sick_leave[0];

            $annual_count='0';
        }
        /*else if($leave_char_code[0]=='co')
        {
            $sql_combooff="select count(id) as Combo_Off from 0_kv_empl_earned_combo_off 
                       where empl_id='".$rowData[0]."' and YEAR(created_on)='".date('Y')."' ";
            $combo_off_sum=db_fetch(db_query($sql_combooff));


            $QUERY="select SUM(days) as taken_combof from 0_kv_empl_leave_applied 
                       where leave_type='".$leave_type_id."' and req_status='1' and year='".date('Y')."'
                       AND empl_id='".$rowData['id']."'";
            $taken_comb_leave=db_fetch(db_query($QUERY));

            $combo_off_available='0';

            if(empty($taken_comb_leave[0]))
            {
                $taken_comb_leave[0]='0';
            }

            $combo_off_available=$combo_off_sum[0]-$taken_comb_leave[0];


            $msg=$leave_char_code[1].' Availabe :'.$combo_off_available;
        }*/
        else if($leave_char_code[0]=='ml')
        {
            $QUERY="select SUM(days) as taken_combof from 0_kv_empl_leave_applied 
                       where leave_type='".$leave_type_id."' and req_status='1' and year='".date('Y')."'
                       AND empl_id='".$rowData['id']."' ";
            $taken_maternity_leave=db_fetch(db_query($QUERY));

            if($taken_maternity_leave[0]=='45')
            {
                $maternity_flag='NOT_ALLOWED';
            }
            else if($years<1)
            {
                $maternity_flag='YEAR_NOT_REACH';
            }
            else
            {
                $taken_maternity_leave[0]=45-$taken_maternity_leave[0];
                $maternity_flag='ALLOWED';
            }

            $msg=$leave_char_code[1].' Availabe :'.$taken_maternity_leave[0];
            $maternity_leave=$taken_maternity_leave[0];

        }
        else if($leave_char_code[0]=='bl')
        {
            $QUERY="select SUM(days) as taken_combof from 0_kv_empl_leave_applied 
                       where leave_type='".$leave_type_id."' and req_status='1' and year='".date('Y')."'
                       AND empl_id='".$rowData['id']."'";
            $taken_bereavement_leave=db_fetch(db_query($QUERY));

            if($taken_bereavement_leave[0]=='3')
            {
                $bereavement_flag='NOT_ALLOWED';
            }
            else
            {
                $taken_bereavement_leave[0]=3-$taken_bereavement_leave[0];
                $bereavement_flag='ALLOWED';
            }

            $msg=$leave_char_code[1].' Availabe :'.$taken_bereavement_leave[0];

            $bereavement_leave=$taken_bereavement_leave[0];
        }
        else
        {
            $eligible_leave=0;
            $msg=$leave_char_code[1].' Taken :'.$eligible_leave;
        }


        return AxisPro::SendResponse(['status' => 'OK','avail_leave' => $msg,'prob_flag'=>$prob_flag,'prob_msg'=>$prob_msg,'annual_leave_cnt'=>substr($annual_count, 0, 2),'combo_off'=>substr($combo_off_sum[0], 0, 2)
            ,'maternity_flg'=>$maternity_flag,'bereavement'=>$bereavement_flag,'ml_leave_available'=>substr($maternity_leave, 0, 2),
            'bl_leave_available'=>substr($bereavement_leave, 0, 2),'sick_leave_validation_flg'=>$sick_leave_validation_flg,'leave_code'=>$leave_char_code[0]]);
    }

    public function sendMailToConcernPerson($emp_data)
    {
        /*-------------------SEND EMAIL------------*/
        $path_to_root = "..";
        include_once($path_to_root . "/API/HRM_Mail.php");
        $hrm_mail=new HRM_Mail();
//id,report_to,leave_request_forward,email,empl_firstname,empl_id

        if($emp_data[0]!='')
        {
            if($emp_data[1]!='')
            {
                $edmp_sql="SELECT email,empl_firstname FROM  0_kv_empl_info 
                           where id=".$emp_data[1]." ";
                $result_d = db_query($edmp_sql, "Can't get your allowed user details");
                $row_res= db_fetch($result_d);


                $hr_sql="SELECT a.email,a.empl_firstname 
                         FROM 0_kv_empl_info as a
                         INNER join 0_users as b ON a.id=b.employee_id
                         where b.role_id='17' ";
                $res_hr = db_query($hr_sql, "Can't get your allowed user details");
                $row_data_hr= db_fetch($res_hr);




                $to_head=$row_res[0];
                $emp_name=$emp_data[4].'-'.$emp_data[5];
                $sub_head="Leave request for verification";
                $content= "<div>
                                   <label>Dear $row_res[1],</label></br>
                                   <div style='height: 39px;
                                    margin-top: 33px;'>There is one new leave request.</div>
                                    <div style='margin-top: 1%;'>
                                    <div style='    margin-left: 3px;'>Details</div>                                 
                                                                       <table>
                                                                       <tr>
                                                                        <td>Employee Name :</td><td>".$emp_name."</td>
                                                                       </tr>
                                                                        
                                                                       </table>
                                                                   </div>
                                    <div style='margin-top:2%;'>
                                    <div>Thanks,</div>
                                    <div>HRMS</div>
                                    </div>
                                    
                               </div>";

                /*$content_hr= "<div>
                                   <label>Dear $row_data_hr[1],</label></br>
                                   <div style='height: 39px;
                                    margin-top: 33px;'>You have one new leave request for verification.</div>
                                    <div style='margin-top: 1%;'>
                                    <div style='    margin-left: 3px;'>Details</div>
                                                                       <table>
                                                                       <tr>
                                                                        <td>Employee Name :</td><td>".$emp_name."</td>
                                                                       </tr>

                                                                       </table>
                                                                   </div>
                                    <div style='margin-top:2%;'>
                                    <div>Thanks,</div>
                                    <div>HRMS</div>
                                    </div>

                               </div>";
*/



                $hrm_mail->send_mail($to_head,$sub_head,$content);
                //$hrm_mail->send_mail($row_data_hr[0],$sub_head,$content_hr);


            }

            if($emp_data[3]!='')
            {

                $edmp_sql="SELECT email,empl_firstname FROM  0_kv_empl_info 
                           where id=".$emp_data[0]." ";
                $result_d = db_query($edmp_sql, "Can't get your allowed user details");
                $row_res= db_fetch($result_d);




                $to=$row_res[0];
                $sub="Leave request submitted";
                $content= "<div>
                                   <label>Dear $row_res[1],</label></br>
                                   <div style='height: 39px;
                                    margin-top: 33px;'>You have successfully submitted leave request. You can check the HRMS for your leave status.</div>
                                  
                                    <div style='margin-top:2%;'>
                                    <div>Thanks,</div>
                                    <div>HRMS</div>
                                    </div>
                                    
                               </div>";

                $hrm_mail->send_mail($to,$sub,$content);



            }
        }


        /*----------------------END----------------*/
    }

    /*------------------------GET ALL Employees---------------*/
    public function get_all_employees()
    {
        $sql = "SELECT a.empl_id as Empid,CONCAT(a.empl_id, \" - \", a.empl_firstname) AS Emp_name,a.id
                FROM 0_kv_empl_info AS a
                INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                WHERE b.department='".$_POST['dept_id']."' and a.status='1' ";
        $result = db_query($sql);
        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result);
    }



    public function get_all_shift_employees()
    {
        $sql_head="SELECT a.head_of_dept,a.department  
                FROM 0_kv_empl_job AS a
                INNER JOIN  0_kv_empl_info AS b ON b.id=a.empl_id
                left join 0_users as c On c.employee_id=b.id
                WHERE c.id='".$_SESSION['wa_current_user']->user."'";
        $dept_head_data=db_fetch(db_query($sql_head));


        $sql = "SELECT a.id as Empid,CONCAT(a.empl_id, \" - \", a.empl_firstname) AS Emp_name,a.id
                FROM 0_kv_empl_info AS a
                INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                left join 0_users as c ON c.employee_id=a.id
                WHERE b.department='".$_POST['dept_id']."'";
        if($dept_head_data[0]=='0')
        {
            $sql.=" AND c.id='".$_SESSION['wa_current_user']->user."'";
        }

        $result = db_query($sql);
        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result);
    }

    /*-------------------------------------GET ALL DEPARTMENTS-------------------*/
    public function get_all_department()
    {

        /*--------------CHECK THE LOGGED USER IS HEAD OF DEPT-------*/
        $sql_head="SELECT a.id  
                FROM 0_kv_empl_departments  AS a
                INNER JOIN  0_kv_empl_info AS b ON a.head_of_empl_id=b.id
                left join 0_users as c ON c.employee_id=b.id
                WHERE c.id='".$_SESSION['wa_current_user']->user."'";

        $dept_head_data=db_fetch(db_query($sql_head));


        $sql = "SELECT id,description 
                FROM 0_kv_empl_departments ";
        if($dept_head_data[0]<>'')
        {
            $sql.= " where id='".$dept_head_data[0]."' ";
        }
        $sql.= " ORDER BY description asc";
        $result = db_query($sql);


        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result);
    }






    public function get_all_shift_department()
    {

        /*--------------CHECK THE LOGGED USER IS HEAD OF DEPT-------*/
        $sql_head="SELECT a.head_of_dept,a.department  
                FROM 0_kv_empl_job AS a
                INNER JOIN  0_kv_empl_info AS b ON b.id=a.empl_id
                left join 0_users as c ON c.employee_id=b.id
                WHERE c.id='".$_SESSION['wa_current_user']->user."'";
        $dept_head_data=db_fetch(db_query($sql_head));


        $sql = "SELECT id,description 
                FROM 0_kv_empl_departments where inactive='0' ";
        if($dept_head_data[0]=='1')
        {
            $sql.= " AND  id='".$dept_head_data[1]."' ";
        }
        else if($dept_head_data[1]!='0' && $_SESSION['wa_current_user']->access!='2')
        {
            $sql.= " AND  id='".$dept_head_data[1]."' ";
        }


        $sql.= " ORDER BY description asc";
        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result);
    }





    /*----------------------LIST EMPLOYEES IN THE DEPARTMENT---------------*/

    public function list_employees_in_dept()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $sql = "SELECT a.id,a.empl_id,CONCAT(a.empl_firstname,\" \",a.empl_lastname) AS Emp_name,a.mobile_phone,a.email
                FROM 0_kv_empl_info AS a
                INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                WHERE b.department='".$_POST['dept_id']."' AND a.status='1'";

        if($_POST['emp_id']<>'' && $_POST['emp_id']!='0')
        {
            $sql.= " AND a.empl_id='".$_POST['emp_id']."' ";
        }

        $sql.= " LIMIT ".$start.",".$length." ";
// echo $sql;
        $result = db_query($sql);
        $data = [];
        $payslip_label='';
        $checkbox='';
        while ($myrow = db_fetch($result)) {



            $pay_staus=" SELECT (case when (c.id > 0) 
                         THEN
                             'Payslip Generated'
                         ELSE
                             'Payslip Not Generated'
                         END)
                         as state,c.payroll_porcessed
                        FROM 0_kv_empl_payroll_details AS c
                        INNER JOIN 0_kv_empl_payroll_master AS d ON c.payslip_id=d.id
                        WHERE c.empl_id='".$myrow['id']."' 
                        AND pay_year='".$_POST['year']."' 
                        AND pay_month='".$_POST['month']."'";

            $pay_status=db_fetch(db_query($pay_staus));

            if(!empty($pay_status[0]))
            {
                $payslip_label='<label style="color:green;"><b>Generated</b></label> ';

                if($pay_status[1]=='')
                {
                    $checkbox='<input type="checkbox" class="chkEmp_select" name="chkEmp_select" value="'.$myrow['id'].'"/>';
                }
                else{ $payslip_label.='| 
               <label  alt="'.$myrow['id'].'"  style="color: blue;text-decoration: underline;font-weight: bold;">Payroll Processed</label>';}


            }
            else
            {
                $payslip_label='<label style="color:red;"><b>Not Generated</b></label>';
                $checkbox='<input type="checkbox" class="chkEmp_select" name="chkEmp_select" value="'.$myrow['id'].'"/>';
            }

            $data[] = array(
                $checkbox,
                $myrow['empl_id'],
                $myrow['Emp_name'],
                $myrow['mobile_phone'],
                $myrow['email'],
                $payslip_label
            );
        }

        $sql_qry = "SELECT a.id,a.empl_id,CONCAT(a.empl_firstname,\" \",a.empl_lastname) AS Emp_name,a.mobile_phone,a.email
                FROM 0_kv_empl_info AS a
                INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                WHERE b.department='".$_POST['dept_id']."'";

        if($_POST['emp_id']<>'' && $_POST['emp_id']!='0')
        {
            $sql_qry.= " AND a.empl_id='".$_POST['emp_id']."' ";
        }
        $result_tot = db_query($sql_qry);

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($result_tot),
            "recordsFiltered" => db_num_rows($result_tot),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);

    }

    public function get_all_accounts()
    {
        $sql="SELECT chart.account_code,CONCAT(chart.account_code,' - ',chart.account_name) AS accname, chart.inactive, type.id
            FROM 0_chart_master chart,0_chart_types type
            WHERE chart.account_type=type.id";
        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result);
    }

    public function save_element()
    {
        $elementName =$_POST["txtElement_Name"];
        $account_code =$_POST["ddl_accounts"];
        $subaccount_code ='0';

        if($_POST["hdnAction"]!='')
        {
            $sql="UPDATE 0_kv_empl_pay_elements set account_code='".$account_code."',element_name='".$elementName."',`type`='".$_POST['ddl_type']."'
                     ,sub_ledger='".$subaccount_code."' WHERE id='".$_POST["hdnAction"]."'";
        }
        else
        {
            $sql="INSERT into 0_kv_empl_pay_elements (account_code,element_name,status,created_by,`type`,sub_ledger)
              VALUES ('".$account_code."','".$elementName."','1','".$_SESSION['wa_current_user']->user."','".$_POST['ddl_type']."','".$subaccount_code."')";
        }

        db_query($sql, "Can't get your allowed user details");
        return AxisPro::SendResponse(['status' => 'OK', 'msg' => 'Created successfully']);

    }

    public function get_pay_elements()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $sql="SELECT a.element_name,CONCAT(b.account_code,' - ',b.account_name) AS accname,a.id,a.account_code,CASE
                WHEN a.`type` =1 THEN 'Earnings'
                WHEN a.`type` = 2 THEN 'Deduction'
                ELSE ''
                END AS acc_type,a.`type` as type_id,a.is_basic,a.salary_ded_flag,a.deletable 
                FROM 0_kv_empl_pay_elements as a
                left JOIN 0_chart_master as b ON a.account_code=b.account_code
                -- LEFT JOIN 0_sub_ledgers AS s ON s.code=a.sub_ledger
                
               
                WHERE status='1'
                ORDER BY a.id asc";
        $result = db_query($sql);
        $return_result = [];
        $data=array();
        while ($myrow = db_fetch_assoc($result)) {


            $return_result[]=$myrow;


        }


        return AxisPro::SendResponse($return_result);

    }

    public function remove_element()
    {
        if(isset($_POST['remove_id']))
        {
            $sql = "UPDATE 0_kv_empl_pay_elements set status='0' WHERE id='".$_POST['remove_id']."' ";
            db_query($sql);
            return AxisPro::SendResponse(['status' => 'OK', 'msg' => 'Leave removed successfully']);
        }
    }

    /****
     * Payslip creation starts
     */
    public function create_pay_roll()
    {
        //3000/30*(30-1)
        $dept_id=$_POST['dept_id'];
        $year=$_POST['year'];
        $month=$_POST['month'];
        $emp_id=$_POST['emp_id'];
        $check_empids=$_POST['check_empids'];

        /*************Check Payslip status****************/
        $check_exists="SELECT id,payslip_id from 0_kv_empl_payroll_master WHERE pay_year='".$year."' AND pay_month='".$month."'
                           AND dept_id='".$dept_id."'";
        $result_data = db_query($check_exists);
        $payslip_auto_id= db_fetch($result_data);

        $pay_slip_pk_id='';
        if(db_num_rows($result_data)==0) {
            $payslip_refID='0'.$_POST['month'].'/'.$dept_id.'/'.$_POST['year'];
            $sql_master = "INSERT Into 0_kv_empl_payroll_master (payslip_id,pay_year,pay_month,dept_id,created_on,created_by)
                       VALUES ('" . $payslip_refID . "','" . $year . "','" . $month . "','" . $dept_id . "','" . date('Y-m-d h:i:s') . "','" . $_SESSION['wa_current_user']->user . "')";

            if(db_query($sql_master))
            {
                $query="SELECT id FROM 0_kv_empl_payroll_master ORDER BY ID DESC LIMIT 1";
                $result_data = db_query($query, "Can't get your allowed user details");
                $latest_insert_id= db_fetch($result_data);
                $pay_slip_pk_id=$latest_insert_id[0];
                if($pay_slip_pk_id!='' || $pay_slip_pk_id!='0')
                {
                    $number_of_days_in_month=cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    $this->pay_roll_generation($pay_slip_pk_id,$emp_id,$dept_id,$number_of_days_in_month,$year,$month,$check_empids,$payslip_refID);
                }
            }
        }
        else
        {
            $number_of_days_in_month=cal_days_in_month(CAL_GREGORIAN, $month, $year);

            $this->pay_roll_generation($payslip_auto_id[0],$emp_id,$dept_id,$number_of_days_in_month,$year,$month,$check_empids,$payslip_auto_id[1]);
        }



        return AxisPro::SendResponse(['status' => 'OK', 'msg' => 'success']);
        // }
        /*else
        {
            //return AxisPro::SendResponse(['status' => 'Exists', 'msg' => 'Payslip already generated for the selected filters.']);
        }*/



    }
    /*****Generate payroll for one employee or all Employees****/
    public function pay_roll_generation($last_insert_id,$empl_id,$dept_id,$days_in_mnth,$year,$month,$check_empids,$payslip_ref_id)
    {
        $Refs = new references();
        $cutoff_day = '';
        $days_in_mnth = $days_in_mnth;
        global $SysPrefs;

        $cut_off_day_number_of_days = 0;

        $sql = "SELECT a.empl_id,b.empl_id as employee_id_string,a.calculate_commission,a.calculate_pf,b.id
                FROM 0_kv_empl_job AS a
                INNER JOIN 0_kv_empl_info AS b ON a.empl_id=b.id
                WHERE a.department='" . $dept_id . "' AND b.status='1' ";
        if ($empl_id != '0') {
            $sql .= " AND b.empl_id='" . $empl_id . "' ";
        }
        if ($check_empids != '' && $empl_id == '0') {
            $sql .= " AND b.id in (" . $check_empids . ") ";
        }


        /*-------------------------------------GET CUT OFF DATE--------------------------*/
        $sql_cut = "SELECT `value` as cutdate FROM 0_sys_prefs WHERE `name`='payroll_cutoff_date'";
        $cutof_date = db_fetch(db_query($sql_cut));
        if ($cutof_date[0] == '' || $cutof_date[0] == 'null') {
            $cutoff_day = '0';
        } else {
            $cutoff_day = $cutof_date[0];
        }
        /*-------------------------------------------END----------------------------------*/
        //echo $sql;
        $empl_id_resu = db_query($sql);

        /*-----------------------------GET Salary Cutting Settings---------*/
        $salary_sql = "select value
                               from 0_sys_prefs
                               where name='payroll_salary_deduction'";
        $salary_ded_res = db_query($salary_sql, "Query execution failed");
        $salary_res = db_fetch($salary_ded_res);
        /*-----------------------------------END----------------------------*/

        if ($cutoff_day == '' || $cutoff_day == '0') {

            $from_date = $year . '-' . $month . '-' . '01';
            $to_date = $year . '-' . $month . '-' . $days_in_mnth;
        } else {
            $start_month = $month;
            $monthNum = $start_month;
            //$cutoff_month=Date('n', strtotime(date("F", mktime(0, 0, 0, $monthNum, 10)) . " last month"));

            if ($monthNum == '1') {
                $cutoff_month = '12';
            } else {
                $cutoff_month = $monthNum - 1;
            }


            if ($month == '01') {
                $year = $year - 1;
            }
            //$cutoff_month=$cutoff_month-1;
            $from_date = $year . '-' . $cutoff_month . '-' . $cutoff_day;

            if ($month == '01') {
                $year = $year + 1;
            }

            if ($cutoff_day == '1') {
                $cutoff_day = cal_days_in_month(CAL_GREGORIAN, $cutoff_month, $year);
                $to_date = $year . '-' . $cutoff_month . '-' . $cutoff_day;
            } else {
                $cutoff_day = $cutoff_day - 1;
                $to_date = $year . '-' . $month . '-' . $cutoff_day;
            }
        }
        /**************************UPDATE PAYROLL FROM DATE AND TODATE**********/
        db_query("update 0_kv_empl_payroll_master set payroll_from_date='" . $from_date . "',payroll_to_date='" . $to_date . "' 
         where id='" . $last_insert_id . "' ");
        /*************************************END*******************************/


        $half_day_salary_ded = '0';
        $full_day_salary_ded = '0';
        $total_salary_ded = '0';
        $pay_elemnt_tot_salary_ded = '0';

        $emp_net_commission = '0';
        $advance_and_loan_tot = '';
        $emp_pf = 0;
        $company_share_pf = 0;
        $gpssa = 0;
        $start_month = '';
        $cutoff_month = '';
        $get_percentage = '';
        $ded_warning_amount = '';
        $tot_ded_warning = '';
        $absent_days_salry_ded = '';
        $advance_and_loan_tot = 0;
        $anual_salary_amount = 0;
        $absence_have_no_attendnce = 0;
        $late_coming_deduction = 0;
        $absence_date_push = array();
        $memo = '';
        $missed_employee_ids = '';
        while ($emp_data = db_fetch_assoc($empl_id_resu)) {

//echo '1';

            $emp_id = $emp_data['empl_id'];

            $emp_data['employee_id_string'] = (int)$emp_data['employee_id_string'];


            $from_date_display = $from_date;
            $to_date_display = $to_date;
            /*********************************CUT OFF DAYS BETWWEN DATES*****************/

            /*   $days = (strtotime($to_date) - strtotime($from_date)) / (60 * 60 * 24);
               $days_in_mnth=$days+1;*/
            /************************************END*************************************/


            $sql = "SELECT weekly_off,work_hours from 0_kv_empl_job where empl_id='" . $emp_data['id'] . "'";
            $res_data = db_query($sql);
            $empl_job_data = db_fetch($res_data);
            $empl_week_day = unserialize(base64_decode($empl_job_data[0]));

            //$employee_weakend_off_days=array();


            $start = $month_num = strtotime($from_date_display);
            $end = strtotime($to_date_display);
            $weakend_cnt = 0;
            while ($month_num < $end) {
                $day_name = date('D', strtotime(date('Y-m-d', $month_num)));
                if (in_array($day_name, $empl_week_day)) {
                    $weakend_cnt++;
                }

                $month_num = strtotime("+1 day", $month_num);
            }


            /*********************FIND ABSENCE IN THE MONTH**************************/

            $shift_check = "SELECT s_date FROM 0_kv_empl_shiftdetails 
                          WHERE s_date >='" . $from_date . "' AND s_date <='" . $to_date . "' AND empl_id='" . $emp_data['id'] . "' ";


            $shift_res = db_query($shift_check);
            $shift_rows = db_num_rows($shift_res);


            if ($shift_rows > 0) {
                $sql_absence = " SELECT s_date FROM 0_kv_empl_shiftdetails WHERE s_date NOT IN (SELECT p.a_date
                             FROM 0_kv_empl_attendance AS p
                             WHERE p.empl_id='" . $emp_data['employee_id_string'] . "'
                             AND p.a_date>='" . $from_date . "'  
                                     AND p.a_date<='" . $to_date . "') AND s_date >='" . $from_date . "' AND s_date <='" . $to_date . "' AND empl_id='" . $emp_data['id'] . "' AND shift_id!='777' ";
                $res_absence_data = db_query($sql_absence, "Can't get your allowed user details");

                $absence_rows_shift = db_num_rows($res_absence_data);


                /* $absence_date=array();
                 if($absence_rows_shift>0)
                 {
                     while($ab_data=db_fetch($res_absence_data))
                     {
                         array_push($absence_date,$ab_data['s_date']);
                     }
                 }*/


            }

            $absence_rows_shift = 0;


            /*---------------------GENERATE FROM DATE AND TODATE BASED ON SELECTED MONTH-----------*/

            $Date = $this->getDatesFromRange($from_date, $to_date);


            for ($i = 0; $i <= sizeof($Date); $i++) {
                $sql = "SELECT count(a_date) 
                FROM 0_kv_empl_attendance 
                WHERE a_date='" . $Date[$i] . "' and empl_id='" . $emp_data['employee_id_string'] . "'";

                $chck_shift_off = $this->getShiftOff_EMployee($Date[$i], $emp_data['employee_id_string'], $emp_data['id']);

                //print_r($sql.' --- ');
                $result_not_attendence = db_query($sql);

                while ($have_no_attendece = db_fetch($result_not_attendence)) {

                    if ($have_no_attendece[0] == '0' && $chck_shift_off != '777') {
                        if ($Date[$i] != '') {
                            array_push($absence_date_push, $Date[$i]);
                        }

                    }

                }
            }
            $absence_have_no_attendnce = count($absence_date_push);

            /****************************************END****************************/

            $qry_full_day = "SELECT count(a.leave_id) 
                            from 0_kv_empl_attendance AS a
                            INNER JOIN  0_kv_empl_leave_applied AS b ON a.leave_id=b.id
                            WHERE a.a_date>='" . $from_date . "'
                            AND a.a_date<='" . $to_date . "' AND a.empl_id='" . $emp_data['employee_id_string'] . "' AND a.leave_id<>'' 
                            AND a.`code`!='al' AND b.full_day_salary_cut!='' ";


            $res_full_day = db_query($qry_full_day, "Can't get your allowed user details");
            $data_set_full = db_fetch($res_full_day);

            $qry_half_day = "SELECT count(a.leave_id) 
                            from 0_kv_empl_attendance AS a
                            INNER JOIN  0_kv_empl_leave_applied AS b ON a.leave_id=b.id
                            WHERE a.a_date>='" . $from_date . "'
                            AND a.a_date<='" . $to_date . "' AND a.empl_id='" . $emp_data['employee_id_string'] . "' AND a.leave_id<>'' 
                            AND a.`code`!='al' AND b.half_day_salary_cut!='' ";

            $res_half_day = db_query($qry_half_day, "Can't get your allowed user details");
            $data_set_half = db_fetch($res_half_day);


            $al_sql = " SELECT SUM(full_day_salary_cut) AS full_day,SUM(half_day_salary_cut) AS hal_day,days
                       from 0_kv_empl_leave_applied
                       WHERE id IN (SELECT leave_id from 0_kv_empl_attendance where a_date>='" . $from_date . "' 
                       and a_date<='" . $to_date . "' AND empl_id='" . $emp_data['employee_id_string'] . "' ) 
                       AND leave_type IN (SELECT id FROM 0_kv_empl_leave_types WHERE char_code='al')";
            $al_data = db_fetch(db_query($al_sql));


            $check_atten = "SELECT count(id) as presntCnt from 0_kv_empl_attendance where a_date>='" . $from_date . "' 
                    and a_date<='" . $to_date . "' AND empl_id='" . $emp_data['employee_id_string'] . "'
                    and code IN ('p','') ";
            $present_data = db_fetch(db_query($check_atten));

            $sql_ab = "SELECT count(id) as presntCnt from 0_kv_empl_attendance where a_date>='" . $from_date . "' 
                    and a_date<='" . $to_date . "' AND empl_id='" . $emp_data['employee_id_string'] . "'
                    and code IN ('a') ";
            $row_absent_data = db_fetch(db_query($sql_ab));

            if (isset($row_absent_data[0])) {
                $absence_rows_shift = $absence_rows_shift + $row_absent_data[0];
            }


            if ($shift_rows > 0) {
                $present_data[0] = $days_in_mnth - $absence_rows_shift;
                $absence_rows = $absence_rows_shift;
            } else {
                $present_data[0] = $present_data[0] + $weakend_cnt;
                $absence_rows = $days_in_mnth - $present_data[0];
            }

            $absence_rows = $absence_rows + $absence_have_no_attendnce + $data_set_full[0] + $data_set_half[0];


            /*--------------Salary Calculation----------------*/


            if ($present_data[0] != '0') {

                $emp_salary_structure = "select b.pay_amount,a.id 
                                            from 0_kv_empl_salary_details as b
                                            INNER JOIN 0_kv_empl_info as a ON a.id=b.emp_id 
                                            where a.id='" . $emp_id . "' and is_basic='1'";


                $res_pay_amount = db_query($emp_salary_structure, "Query execution failed");
                $pay_result = db_fetch($res_pay_amount);

                if (db_num_rows($res_pay_amount) > 0) {
                    /******GET TOTAL SALARY OF EMPLOYEE*********/
                    $query = "SELECT SUM(a.pay_amount) AS total_slary 
                                    from 0_kv_empl_salary_details AS a
                                    INNER JOIN 0_kv_empl_pay_elements AS b ON a.pay_rule_id=b.id
                                    WHERE a.emp_id='" . $pay_result[1] . "' AND b.`type`='1' AND b.`calculate_percentage`='0'
                                 ";
                    if ($salary_res[0] == '1') {
                        $query .= " AND a.is_basic='1'";
                    }
                    $result_qry = db_query($query, "Query execution failed");
                    $total_empl_salary = db_fetch($result_qry);
                    /***************END************************/


                    /********************************Anual Leave salary Calculation*****************/

                    if (isset($al_data[2])) {


                        $emp_hr_amount = "select b.pay_amount,a.id 
                                            from 0_kv_empl_salary_details as b
                                            INNER JOIN 0_kv_empl_info as a ON a.id=b.emp_id 
                                            where a.id='" . $emp_id . "' 
                                            AND b.pay_rule_id IN (select id from 0_kv_empl_pay_elements where is_hr='1') ";
                        $res_hr_amount = db_query($emp_hr_amount, "Query execution failed");
                        $hr_amount_res = db_fetch($res_hr_amount);

                        /***********************CHK EMPLYEE IS LOCAL*****************/

                        $sql_local = "select is_local from 0_users where employee_id='" . $emp_id . "' ";
                        $local_empl = db_fetch(db_query($sql_local));

                        /****************************END*****************************/


                        if (!isset($hr_amount_res[0])) {
                            $hr_amount_res[0] = 0;
                        }


                        if ($local_empl['is_local'] == '1') {
                            $get_oneday_salary_anual = $total_empl_salary[0] / $days_in_mnth;
                        } else {
                            $total_base_hr = $hr_amount_res[0];
                            $get_oneday_salary_anual = $total_base_hr / $days_in_mnth;
                        }


                        $get_oneday_salary_anual = round($get_oneday_salary_anual, 2);

                        if ($al_data[1] != '0') {
                            $half_day_calc_al = $get_oneday_salary_anual;
                            $half_day_calc_al = $half_day_calc_al / 2;
                            $half_day_salary_add_al = $half_day_calc_al * $al_data[1];
                            $anual_salary_amount += $half_day_salary_add_al;
                        } else if ($al_data[0] != '0') {
                            $full_day_calc_al = $get_oneday_salary_anual;
                            $full_day_salary_al_ded = $full_day_calc_al * $al_data[0];
                            $anual_salary_amount += $full_day_salary_al_ded;
                        } else {
                            $full_day_anual_salary = $get_oneday_salary_anual * $al_data[2];
                            $anual_salary_amount += $full_day_anual_salary;
                        }

                        $present_data[0] = $present_data[0] - $al_data[2];
                    } else {
                        $al_data[2] = 0;
                    }

                    if ($anual_salary_amount > 0) {
                        $memo .= ' AED' . round($anual_salary_amount) . ' = Annual leave ' . $al_data[2] . ' day';
                    }


                    $get_oneday_salary = $total_empl_salary['0'] / $days_in_mnth; /*-----------------One day Salary------*/
                    $get_oneday_salary = round($get_oneday_salary, 2);

                    if ($data_set_full[0] != '') {
                        $half_day_calc = $get_oneday_salary;
                        $half_day_calc = $half_day_calc / 2;
                        $half_day_salary_ded = $half_day_calc * $data_set_full[0];
                    }

                    if ($data_set_half[0] != '') {
                        $full_day_calc = $get_oneday_salary;
                        $full_day_salary_ded = $full_day_calc * $data_set_half[0];
                    }

                    if ($absence_rows > 0) {
                        $absent_days_salry_ded = $get_oneday_salary * ($absence_rows);
                    }

                    $total_salary_ded = $half_day_salary_ded + $full_day_salary_ded + $absent_days_salry_ded;

                    $mepl_salary = '';
                    $absent_days = $absence_rows;


                    $sqlQuery = "SELECT SUM(a.pay_amount) as extraAllowance,
                                    (SELECT SUM(aa.pay_amount)
                                    FROM 0_kv_empl_salary_details AS aa
                                    INNER JOIN 0_kv_empl_pay_elements AS bb ON aa.pay_rule_id=bb.id
                                    WHERE aa.emp_id='" . $pay_result[1] . "' AND aa.is_basic!='1' AND bb.`type`='2' AND bb.`calculate_percentage`='0') AS Deduction
                                    FROM 0_kv_empl_salary_details AS a
                                    INNER JOIN 0_kv_empl_pay_elements AS b ON a.pay_rule_id=b.id
                                    WHERE a.emp_id='" . $pay_result[1] . "' AND a.is_basic!='1' AND b.`type`!='2' AND b.`calculate_percentage`='0'";
                    $result_salary_elements = db_fetch(db_query($sqlQuery));
                    if ($salary_res[0] == '1') {
                        $mepl_salary_added_extra = ($total_empl_salary[0] + $result_salary_elements[0]) - ($result_salary_elements['Deduction'] - $total_salary_ded);
                    } else {
                        $mepl_salary_added_extra = ($total_empl_salary[0] - $total_salary_ded) - $result_salary_elements['Deduction'];
                    }


                    /********************************Late Coming Deduction****************/

                    $grace_time = $SysPrefs->prefs['payroll_grace_time'];
                    $Date = $this->getDatesFromRange($from_date, $to_date);
                    $total_daily_deduction = array();
                    $total_timeout_elaps = 0;
                    $tot_latecoming_ded = 0;
                    $laps_time = 0;

                    for ($i = 0; $i < sizeof($Date); $i++) {

                        $sql_shift = " SELECT b.BeginTime,b.EndTime 
                             FROM 0_kv_empl_shiftdetails AS a
                             INNER JOIN 0_kv_empl_shifts AS b ON a.shift_id=b.id
                             WHERE s_date='" . $Date[$i] . "' AND empl_id='" . $emp_id . "' ";
                        $shift_begin_time = db_fetch(db_query($sql_shift));


                        if ($shift_begin_time[0] != '') {

                            $shift_end = $shift_begin_time['EndTime'];

                            $sql_latecoming = "SELECT a_date,in_time,ROUND((TIME_TO_SEC(TIMEDIFF(in_time,  '" . $shift_begin_time[0] . "'))/60)-" . $grace_time . ") AS timelaps,
                                        ROUND((TIME_TO_SEC(TIMEDIFF(out_time,in_time ))/60)) AS total_spend,
                                        ROUND((TIME_TO_SEC(TIMEDIFF( '" . $shift_begin_time[0] . "','" . $shift_end . "'))/60)) AS shift_dur
                                        ,CASE WHEN out_time > '" . $shift_end . "' THEN \"0\"
                                              ELSE ROUND((TIME_TO_SEC(TIMEDIFF('" . $shift_end . "',out_time))/60))
                                         END AS    out_time_elaps
                                         FROM 0_kv_empl_attendance
                                         WHERE empl_id='" . $emp_data['employee_id_string'] . "'
                                       -- AND ROUND(TIME_TO_SEC(TIMEDIFF(in_time,  '" . $shift_begin_time[0] . "'))/60) >" . $grace_time . "
                                        AND a_date ='" . $Date[$i] . "' AND `code` IN ('','p') ";


                            $late_data = db_fetch(db_query($sql_latecoming));

                            if ($late_data['in_time'] != '') {
                                if ($late_data['timelaps'] < 0) {
                                    $late_data['timelaps'] = 0;
                                }

                                if ($late_data['out_time_elaps'] < 0) {
                                    $late_data['out_time_elaps'] = 0;
                                }


                                $total_timeout_elaps = $late_data['out_time_elaps'] + $late_data['timelaps'];

                                if ($total_timeout_elaps > 0) {
                                    $get_one_hour_salary = ($total_empl_salary['0'] / $days_in_mnth) / 8;
                                    $missed_salary_minutes = ($get_one_hour_salary / 60) * $total_timeout_elaps;

                                    array_push($total_daily_deduction, $missed_salary_minutes);

                                    $tot_latecoming_ded = $total_timeout_elaps;

                                    $del = "delete from 0_kv_empl_late_coming_days where `date`='" . $Date[$i] . "' AND empl_id='" . $emp_id . "' ";
                                    db_query($del);

                                    if ($total_timeout_elaps > 0) {
                                        $sql_insert_late_coming = "INSERT INTO 0_kv_empl_late_coming_days (`date`,laps_time,empl_id,year)
                                                      values ('" . $Date[$i] . "','" . $total_timeout_elaps . "','" . $emp_id . "','" . $year . "')";
                                        db_query($sql_insert_late_coming);
                                    }
                                }


                            }

                        } else {
                            $sql_no_shift = "SELECT a_date,in_time,out_time,ROUND((TIME_TO_SEC(TIMEDIFF(out_time, in_time))/60)) AS timelaps,
                                ROUND((TIME_TO_SEC(TIMEDIFF(out_time,in_time ))/60)) AS total_spend
                                ,CASE WHEN ROUND((TIME_TO_SEC(TIMEDIFF(out_time, in_time))/60))/60 >=8 THEN \"0\"
                                ELSE 8-(ROUND((TIME_TO_SEC(TIMEDIFF(out_time, in_time))/60)))
                                END AS  out_time_elaps
                                FROM 0_kv_empl_attendance
                                WHERE empl_id='" . $emp_data['employee_id_string'] . "'
                                AND a_date ='" . $Date[$i] . "' AND `code` IN ('','p') ";


                            $result_data_set = db_fetch(db_query($sql_no_shift));

                            if (abs($result_data_set['out_time_elaps']) > 0) {
                                $get_one_hour_salary = ($total_empl_salary['0'] / $days_in_mnth) / 8;
                                $missed_salary_minutes_no_shift = ($get_one_hour_salary / 60) * abs($result_data_set['out_time_elaps']);

                                array_push($total_daily_deduction, $missed_salary_minutes_no_shift);

                                $tot_latecoming_ded = abs($result_data_set['out_time_elaps']);

                                $del = "delete from 0_kv_empl_late_coming_days where `date`='" . $Date[$i] . "' AND empl_id='" . $emp_id . "' ";
                                db_query($del);

                                if ($tot_latecoming_ded > 0) {
                                    $sql_insert_late_coming = "INSERT INTO 0_kv_empl_late_coming_days (`date`,laps_time,empl_id,year)
                                                     values ('" . $Date[$i] . "','" . $tot_latecoming_ded . "','" . $emp_id . "','" . $year . "')";
                                    db_query($sql_insert_late_coming);
                                }

                            }


                        }

                    }
                    /*********************GET EMP PF & COMP PF Percentage*************/

                    if ($emp_data['calculate_pf'] == '1') {
                        $sql_pf = "select value From 0_sys_prefs where `name`='payroll_emp_pf_percent' ";
                        $pf_per_value = db_fetch_row(db_query($sql_pf));
                        if ($pf_per_value[0] != '') {
                            $emp_pf = $total_empl_salary[0] * $pf_per_value[0] / 100;
                        }
                        $sql_com_pf = "select value From 0_sys_prefs where `name`='payroll_pf_comp_percent' ";
                        $com_pf_per_value = db_fetch_row(db_query($sql_com_pf));
                        if ($com_pf_per_value[0] != '') {
                            $company_share_pf = $total_empl_salary[0] * $com_pf_per_value[0] / 100;
                        }

                        $gpssa = round($emp_pf + $company_share_pf);
                    }


                    /*****************************END****************************/

                    /*********ADDIng Overtime Amount To the total Salary*****/
                    $over_sql = "SELECT SUM(overtime_amnt) as totalOvertime
                                    FROM 0_kv_empl_overtime WHERE emp_id='" . $emp_id . "' and month='" . $month . "'";
                    $overtime_data = db_fetch(db_query($over_sql));

                    if ($overtime_data[0] != '') {
                        $mepl_salary_added_extra = $mepl_salary_added_extra + $overtime_data[0];

                    }
                    /***********************END******************************/
                    $check_data_exist = "Select id FROM 0_kv_empl_payroll_details WHERE payslip_id='" . $last_insert_id . "' 
                                        AND empl_id='" . $emp_id . "'";
                    $result_cnt = db_fetch(db_query($check_data_exist));

                    if ($result_cnt[0] == '') {

                        /*---------------- ------GL JOURNAL ENTRY START--------------*/
                        $total_days_wrked = $days_in_mnth - $absence_rows - ($leave_data[0] + ($leave_data[1] / 2));

                        $absent_days = ($absence_rows + ($leave_data[0] + ($leave_data[1] / 2)));


                        if ($absent_days > 0) {
                            $sql_leave_days = "SELECT date
                                    from 0_kv_empl_leave_applied
                                    WHERE id IN (SELECT leave_id from 0_kv_empl_attendance where a_date>='" . $from_date . "' 
                                    and a_date<='" . $to_date . "' AND empl_id='" . $emp_data['employee_id_string'] . "')
                                    AND leave_type IN (SELECT id FROM 0_kv_empl_leave_types WHERE char_code!='al') ";

                            $res_leave_data = db_query($sql_leave_days, "Can't get your allowed user details");
                            $data_set_leave = db_fetch($res_leave_data);
                            $leave_days = '';
                            foreach ($data_set_leave as $d) {
                                $leave_days .= $d . ',';
                            }

                            if (!empty($leave_days)) {
                                $memo .= $absent_days . ' day(s) absent' . ',';
                            } else {
                                $memo .= $absent_days . ' day(s) absent ' . ',';
                            }

                        }

                        /*-----------------------------BEGIN--------------------------*/

                        /**************************GET EMPLOYEE TOT SALARY*************/
                        $emp_sql = "SELECT SUM(a.pay_amount) AS total_slary 
                                    from 0_kv_empl_salary_details AS a
                                    INNER JOIN 0_kv_empl_pay_elements AS b ON a.pay_rule_id=b.id
                                    WHERE a.emp_id='" . $pay_result[1] . "' AND b.`type`='1' AND b.`calculate_percentage`='0'
                                 ";
                        $emp_tot_salary_data = db_fetch(db_query($emp_sql));
                        /**********************************END*************************/

                        $sqlQuery_percent = "SELECT a.pay_rule_id,a.pay_amount,b.`calculate_percentage`
                                        ,b.calculate_percentage
                                        FROM 0_kv_empl_salary_details AS a
                                        INNER JOIN 0_kv_empl_pay_elements AS b ON a.pay_rule_id=b.id 
                                        WHERE a.emp_id='" . $pay_result[1] . "' AND b.`calculate_percentage`='1' ";
                        $percent_data = db_fetch(db_query($sqlQuery_percent));
                        if ($percent_data['calculate_percentage'] == '1') {
                            $pay_elemnt_tot_salary_ded = $result_salary_elements['Deduction'] + ($emp_tot_salary_data[0] * $percent_data['pay_amount'] / 100);
                            $mepl_salary_added_extra += -($emp_tot_salary_data[0] * $percent_data['pay_amount'] / 100);

                        } else {
                            $pay_elemnt_tot_salary_ded = $result_salary_elements['Deduction'];
                        }

                        if ($emp_data['calculate_commission'] == '1') {
                            $sql_ry = "SELECT a.id,b.empl_id
                                        FROM 0_kv_empl_info AS b
                                        INNER JOIN 0_users AS a ON a.employee_id=b.id
                                        WHERE b.id='" . $pay_result[1] . "'   ";
                            $r_data = db_query($sql_ry);
                            $empl_user_id = db_fetch($r_data);


                            if ($emp_data['employee_id_string'] == '1035') {

                                $sql_tasheel = " SELECT COUNT(a.id)*6 as Two_Fourty_sum
                                    FROM (((`0_debtor_trans_details` `a`
                                    LEFT JOIN `0_debtor_trans` `b` ON((`b`.`trans_no` = `a`.`debtor_trans_no`)))
												)
                                     )
                                    WHERE ((`a`.`debtor_trans_type` = 10) AND (`b`.`reference` != 'auto') AND (`b`.`type` = 10) AND (`a`.`quantity` != 0)) 
                                    and  b.tran_date >= '" . $from_date . "' and  b.tran_date <= '" . $to_date . "'
                                    AND  unit_price='240' ";
                                $two_fourty_commison = db_fetch(db_query($sql_tasheel));


                                $sql_tasheel_eighty = "SELECT COUNT(a.id)*2 as Eighty_sum
                                    FROM (((`0_debtor_trans_details` `a`
                                    LEFT JOIN `0_debtor_trans` `b` ON((`b`.`trans_no` = `a`.`debtor_trans_no`)))
												)
                                     )
                                    WHERE ((`a`.`debtor_trans_type` = 10) AND (`b`.`reference` != 'auto') AND (`b`.`type` = 10) AND (`a`.`quantity` != 0)) 
                                    and  b.tran_date >= '" . $from_date . "' and  b.tran_date <= '" . $to_date . "'
                                    AND   unit_price='80'";

                                $eight_cateory_commission = db_fetch(db_query($sql_tasheel_eighty));

                                $employee_commison = $two_fourty_commison['Two_Fourty_sum'] + $eight_cateory_commission['Eighty_sum'];


                                if ($emp_tot_salary_data[0] < $employee_commison) {
                                    $emp_net_commission = abs($employee_commison - $emp_tot_salary_data[0]);
                                } else {
                                    if ($absence_rows != '0') {
                                        $emp_net_commission = $employee_commison;
                                    }

                                }


                            } else if ($empl_user_id[0] != '') {
                                $com_sql = "SELECT SUM((`a`.`user_commission` * `a`.`quantity`)) AS `total_commission`
                                    FROM (((`0_debtor_trans_details` `a`
                                    LEFT JOIN `0_debtor_trans` `b` ON((`b`.`trans_no` = `a`.`debtor_trans_no`)))
                                    LEFT JOIN `0_debtors_master` `c` ON((`c`.`debtor_no` = `b`.`debtor_no`)))
                                    LEFT JOIN `0_users` ON((`0_users`.`id` = `a`.`created_by`)))
                                    WHERE ((`a`.`debtor_trans_type` = 10) AND (`b`.`reference` != 'auto') AND (`b`.`type` = 10) AND (`a`.`quantity` != 0)) 
                                    and  b.tran_date >= '" . $from_date . "' and  b.tran_date <= '" . $to_date . "'  
                                    and `a`.`created_by`='" . $empl_user_id[0] . "'
                                    /*GROUP BY `b`.`reference`,`a`.`stock_id`*/
                                    ";

                                //echo $com_sql;
                                $com_sql_res = db_query($com_sql);
                                $commd_data = db_fetch($com_sql_res);
                                /* $commion_return_res=[];
                                 while ($commison_data = db_fetch($com_sql_res)) {
                                     $commion_return_res[] = $commison_data['total_commission'];
                                 }*/

                                $employee_commison = $commd_data[0];
                                //echo $commd_data[0].' --- ';


                                if ($emp_tot_salary_data[0] < $employee_commison) {
                                    $emp_net_commission = abs($employee_commison - $emp_tot_salary_data[0]);
                                } else {
                                    if ($absence_rows != '0') {
                                        $emp_net_commission = $employee_commison;
                                    }

                                }

                            }
                        }
                        //echo '6'.'-----';

                        /************************************DEDUCT loan amount if emp has loan & Advance paid amount ******************/
                        $loan_sql = "select monthly_pay as loan_amount,id
                                      from 0_kv_empl_loan where start_date >= '" . $from_date . "' and start_date<='" . $to_date . "'
                                      AND  (periods-periods_paid)>0 and empl_id='" . $emp_id . "'";
                        $lon_result = db_query($loan_sql);

                        $sql_accountmapp_chk = "select id,advance_salary_account_base,advance_emp_sub_ledger 
                                                 from 0_kv_empl_account_mapping where emp_id='" . $emp_id . "'";
                        $account_mapp_res = db_fetch_row(db_query($sql_accountmapp_chk));

                        $adv_amount = '0';

                        if ($account_mapp_res[0] != '') {
                            $sql_adv = " SELECT  SUM(gl.amount) AS Advance
                                        FROM " . TB_PREF . "gl_trans gl 
                                        LEFT JOIN " . TB_PREF . "voided v ON gl.type_no=v.id AND v.type=gl.type 
                                        LEFT JOIN " . TB_PREF . "vouchers AS voucher ON voucher.trans_no=gl.type_no 
                                        AND gl.type=IF(voucher.voucher_type='PV',1,2) 
                                        LEFT JOIN " . TB_PREF . "refs ref ON ref.type=gl.type AND ref.id=gl.type_no,0_chart_master coa 
                                        WHERE coa.account_code=gl.account AND ISNULL(v.date_) AND gl.tran_date >= '" . $from_date . "' 
                                        AND gl.tran_date <='" . $to_date . "' AND gl.amount <> 0 AND gl.amount>0
                                        AND gl.account = '" . $account_mapp_res[1] . "'";
                            if (!empty($account_mapp_res[2])) {
                                $sql_adv .= " AND axispro_subledger_code='" . $account_mapp_res[2] . "'";
                            }
                            $sql_adv .= " ORDER BY axispro_subledger_code,voucher.voucher_type";
                            $advance_amount = db_fetch_row(db_query($sql_adv));
                            $advance_and_loan_tot += $advance_amount[0];
                            $adv_amount = $advance_amount[0];

                        }
                        //  echo '7'.'-----';
                        /* if($lon_res[0]!='')
                         {
                             $advance_and_loan_tot+= $lon_res[0];
                         }*/
                        $loan_ded_desc = array();
                        $tot_loan_amount = '0';
                        while ($loan_entry_data = db_fetch($lon_result)) {
                            $advance_and_loan_tot += $loan_entry_data[0];
                            $tot_loan_amount += $loan_entry_data[0];
                            array_push($loan_ded_desc, array('pk_id' => $loan_entry_data[1], 'loan_amnt' => $loan_entry_data[0]));
                        }

                        /************************************************END**************************************/
                        $sql_in = "select sum(ded_amount) as invoice_mist_ded
                                      from 0_kv_empl_invoice_mistake_entry 
                                      where created_on >='" . $from_date . "' and created_on <='" . $to_date . "' 
                                      and empl_id='" . $pay_result[1] . "' ";
                        $invoice_mis_ded_amount = db_fetch(db_query($sql_in));

                        /********************************************INVOICE MISTAKE DEDUTION END*****************/
                        $net_payable = round($mepl_salary_added_extra, 2) - $advance_and_loan_tot - $gpssa;
                        $pay_elemnt_tot_salary_ded += $advance_and_loan_tot + $gpssa + $invoice_mis_ded_amount[0];

                        if ($total_salary_ded) {
                            $pay_elemnt_tot_salary_ded += $total_salary_ded;
                            /***********Deducting the absent days Amount************/
                        }

                        /* if($emp_tot_salary_data[0]>$employee_commison)
                         {
                              $employee_commison=0;
                              $emp_net_commission=0;
                         }*/


                        $array_sum = round(array_sum($total_daily_deduction), 2);


                        $memo .= 'AED ' . $array_sum . ' = late time deduction' . ',';

                        $pay_elemnt_tot_salary_ded += $array_sum;


                        $total_salary_ded = $total_salary_ded + round($anual_salary_amount);


                        $sql = "INSERT INTO 0_kv_empl_payroll_details (payslip_id,empl_id,leave_days,salary_amount,created_on
                               ,created_by,days_worked,weakend,gl_trans_id,tot_salary_deduction,commission,net_commission,absent_hours,
                               absent_ded_amount_hrs,leave_absent_deduction,loan_amount,advance_amount,pf_amount,anual_leave_salary,late_coming_deduction_minutes,memo)
                               values ('" . $last_insert_id . "','" . $pay_result[1] . "','" . $absent_days . "','" . round($emp_tot_salary_data[0], 2) . "','" . date('Y-m-d') . "',
                               '" . $_SESSION['wa_current_user']->user . "','" . $total_days_wrked . "','4','0','" . $pay_elemnt_tot_salary_ded . "',
                               '" . $employee_commison . "','" . $emp_net_commission . "','0','0','" . $total_salary_ded . "','" . $tot_loan_amount . "'
                               ,'" . $adv_amount . "','" . $gpssa . "','" . round($anual_salary_amount) . "','" . $array_sum . "','" . $memo . "')";

                        /*echo   $sql;

                                                 die;*/
                        if (db_query($sql)) {

                            $SQL = "SELECT id FROM 0_kv_empl_payroll_details ORDER BY ID DESC LIMIT 1";
                            $data_res = db_query($SQL, "Can't get your results");
                            $last_id = db_fetch($data_res);
                            /*****************INSERT EMPLOYEE PAY ELEMENTS**************/
                            $sqlQuery = "SELECT a.pay_rule_id,a.pay_amount,a.is_basic,b.account_code,b.`type` AS ded_ear
                                        ,b.calculate_percentage
                                        FROM 0_kv_empl_salary_details AS a
                                        INNER JOIN 0_kv_empl_pay_elements AS b ON a.pay_rule_id=b.id 
                                        WHERE a.emp_id='" . $pay_result[1] . "' ";
                            $result_salary_elements = db_query($sqlQuery);


                            $pay_amnt = '';
                            while ($myrow_payslip = db_fetch_assoc($result_salary_elements)) {

                                if ($salary_res[0] == '1' && $myrow_payslip['is_basic'] == '1') {
                                    $pay_amnt = $mepl_salary;
                                } else if ($myrow_payslip['calculate_percentage'] == '1') {
                                    $pay_amnt = $emp_tot_salary_data[0] * $myrow_payslip['pay_amount'] / 100;
                                } else {
                                    $pay_amnt = $myrow_payslip['pay_amount'];
                                }

                                $pay_elemnts = "INSERT INTO 0_kv_empl_payroll_elements (payslip_detail_id,pay_element,amount)
                                          VALUES ('" . $last_id[0] . "','" . $myrow_payslip['pay_rule_id'] . "','" . round($pay_amnt) . "')";
                                db_query($pay_elemnts);
                            }


                            if ($overtime_data[0] != '') {
                                $get_sqls = "SELECT id,account_code 
                                      FROM 0_kv_empl_pay_elements WHERE is_over_time_accnt='1'";
                                $over_time_accs = db_fetch(db_query($get_sqls));

                                $overtime_insert = "INSERT INTO 0_kv_empl_payroll_elements (payslip_detail_id,pay_element,amount)
                                          VALUES ('" . $last_id[0] . "','" . $over_time_accs[0] . "','" . $overtime_data[0] . "')";
                                db_query($overtime_insert);
                            }

                            if ($tot_loan_amount != '') {
                                $get_sqls = "SELECT id,account_code 
                                      FROM 0_kv_empl_pay_elements WHERE is_loan_account='1'";
                                $loan_acc = db_fetch(db_query($get_sqls));

                                $loan_insert = "INSERT INTO 0_kv_empl_payroll_elements (payslip_detail_id,pay_element,amount,loan_ded_desc)
                                          VALUES ('" . $last_id[0] . "','" . $loan_acc[0] . "','" . $tot_loan_amount . "','" . json_encode($loan_ded_desc) . "')";
                                db_query($loan_insert);
                            }


                            if ($gpssa != '') {
                                $get_sqls = "SELECT id,account_code 
                                      FROM 0_kv_empl_pay_elements WHERE is_pf_account='1'";
                                $gpssa_data = db_fetch(db_query($get_sqls));

                                $loan_insert = "INSERT INTO 0_kv_empl_payroll_elements (payslip_detail_id,pay_element,amount)
                                          VALUES ('" . $last_id[0] . "','" . $gpssa_data[0] . "','" . $gpssa . "')";
                                db_query($loan_insert);
                            }

                            /*******************************INVOICE  MISTAKE DEDUCTION*************/

                            if ($invoice_mis_ded_amount[0] > 0) {
                                $Qry_in = "SELECT id,account_code 
                                      FROM 0_kv_empl_pay_elements WHERE mistake_ded_flag='1'";
                                $res_data_inv = db_fetch(db_query($Qry_in));

                                $invoice_mistake_sql = "INSERT INTO 0_kv_empl_payroll_elements (payslip_detail_id,pay_element,amount)
                                          VALUES ('" . $last_id[0] . "','" . $res_data_inv[0] . "','" . $invoice_mis_ded_amount[0] . "')";
                                db_query($invoice_mistake_sql);
                            }

                            /*****************************WARNING DEDUCTION************************/
                            $sql_war = "select id from 0_kv_empl_warings where emp_id='" . $pay_result[1] . "'
                              AND ded_starting >='" . $from_date . "' AND ded_starting <='" . $to_date . "' AND completed IS NULL";

                            $war_data_res = db_query($sql_war);
                            $deduction_amount_array = array();
                            while ($war_data = db_fetch($war_data_res)) {
                                if ($war_data['id'] != '') {

                                    $sql_sub = "Select ded_starting,completed,ded_amount - CASE WHEN deducted_amount IS NULL THEN 0 
                                               ELSE 0 END AS ded_amount 
                                               from 0_kv_empl_warings where id='" . $war_data['id'] . "'
                                      ";
                                    $war_data_res_sub = db_fetch(db_query($sql_sub));

                                    $get_percentage = $emp_tot_salary_data[0] * 10 / 100;

                                    if ($war_data_res_sub['ded_amount'] != '') {
                                        if ($get_percentage < $war_data_res_sub['ded_amount']) {
                                            $ded_warning_amount = $get_percentage;
                                        } else {
                                            $ded_warning_amount = $war_data_res_sub['ded_amount'];
                                        }

                                        $tot_ded_warning += $ded_warning_amount;
                                    }


                                    array_push($deduction_amount_array, array('pk_id' => $war_data['id'], 'amount' => $ded_warning_amount));

                                }
                            }


                            /********************************END*************************/

                            if ($tot_ded_warning != '') {
                                $qry_w = "SELECT id,account_code 
                                      FROM 0_kv_empl_pay_elements WHERE is_warning_ded='1'";
                                $data_war = db_fetch(db_query($qry_w));

                                $sql_war = "INSERT INTO 0_kv_empl_payroll_elements (payslip_detail_id,pay_element,amount,war_ded_desc)
                                          VALUES ('" . $last_id[0] . "','" . $data_war[0] . "','" . $tot_ded_warning . "','" . json_encode($deduction_amount_array) . "')";
                                db_query($sql_war);

                                /***********************WARNING AMOUNT DEDUCTIONS SAVING******************/
                            }

                            /*****************Public Holidays DED amount adding in OTHER ALLOWNANCES******/

                            $sql_hol = "select count(id)
                                            From 0_kv_empl_holiday_approved
                                            where date>='" . $from_date . "' AND date<='" . $to_date . "'";
                            $res_holi_cnt = db_num_rows(db_query($sql_hol));

                            $tot_holiday_sum = '0';
                            $fifty_per_value = '0';
                            if ($res_holi_cnt) {

                                $query_tot_emp_salary = "SELECT SUM(a.pay_amount) AS total_slary 
                                    from 0_kv_empl_salary_details AS a
                                    INNER JOIN 0_kv_empl_pay_elements AS b ON a.pay_rule_id=b.id
                                    WHERE a.emp_id='" . $pay_result[1] . "' AND b.`type`='1' AND b.`calculate_percentage`='0'";
                                /* if($salary_res[0]=='1')
                                 {
                                   $query.=" AND a.is_basic='1'";
                                 }*/
                                $res_tot_salary = db_query($query_tot_emp_salary, "Query execution failed");
                                $data_emp_tot_salary = db_fetch($res_tot_salary);
                                /***************END************************/
                                $get_oneday_salary_calc = $data_emp_tot_salary['0'] / $days_in_mnth; /*-----------------One day Salary------*/
                                $get_oneday_salary_rounded = round($get_oneday_salary_calc, 2);


                                /*********50% OFF Allowances + 1 Day off Calculating***************/

                                $sql_f = "select id
                                            From 0_kv_empl_holiday_approved
                                            where date>='" . $from_date . "' AND date<='" . $to_date . "'
                                            AND pay_option='1'";
                                $percen_fifty_res = db_query($sql_f);
                                $option_one_cnt = '0';
                                $options_ids_saving = [];
                                while ($opt_data_one = db_fetch($percen_fifty_res)) {
                                    array_push($options_ids_saving, array('id' => $opt_data_one['id']));
                                    $option_one_cnt++;
                                }


                                if ($option_one_cnt > 0) {
                                    $fifty_per_value = ($get_oneday_salary_rounded * 50 / 100) * $option_one_cnt;
                                }

                                /************************END***************************/

                                /*********150% OFF Allowances Calculating***************/

                                /*  $sqlRy="select count(id)
                                             From 0_kv_empl_holiday_approved
                                             where date>='".$from_date."' AND date<='".$to_date."'
                                             AND pay_option='2'";
                                    $percen_one_fifty=db_query($sqlRy);
                                    $option_two_cnt='0';

                                    while($opt_data_two=db_fetch($percen_one_fifty))
                                    {
                                      array_push($options_ids_saving,array('id'=>$opt_data_two['id']));
                                      $option_two_cnt++;
                                    }


                                    if($option_two_cnt>0)
                                    {
                                       $one_fifty_per_value=($get_oneday_salary_rounded*150/100)*$option_two_cnt;
                                    } */
                                /************************END***************************/
                                /*
                                                                    $tot_holiday_sum=$fifty_per_value+$one_fifty_per_value;
                                                                    if($tot_holiday_sum>0)
                                                                    {
                                                                           $qry_h="SELECT id,account_code
                                                                                   FROM 0_kv_empl_pay_elements WHERE holiday_allowance_flag='1'";
                                                                           $data_holiday_data=db_fetch(db_query($qry_h));

                                                                           $R="INSERT INTO 0_kv_empl_payroll_elements (payslip_detail_id,pay_element,amount,holiday_ids)
                                                                               VALUES ('".$last_id[0]."','".$data_holiday_data[0]."'
                                                                               ,'".$tot_holiday_sum."','".json_encode($options_ids_saving)."')";
                                                                           db_query($R);

                                                                    }

                                */
                            }

                            $pay_elemnt_tot_salary_ded = 0;
                            $absence_have_no_attendnce = 0;
                            $anual_salary_amount = 0;
                            $absent_days = 0;
                            $absence_have_no_attendnce = 0;
                            $absence_date_push = [];
                            $laps_time = 0;
                            $emp_net_commission = 0;
                            $employee_commison = 0;
                            $commion_return_res = [];
                            $total_salary_ded = 0;
                            $half_day_salary_ded = 0;
                            $full_day_salary_ded = 0;
                            $absent_days_salry_ded = 0;
                            $gpssa = 0;
                            $memo = '';


                            /**************************************END************************************/

                        }
                    }
                    /*echo  json_encode(['status' => 'SUCCESS',
                        'msg' => 'Donee']);*/
                } else {

                    $missed_employee_ids .= $emp_data['employee_id_string'] . ',';
                }
            }

            $return = '';
            if (!empty($missed_employee_ids)) {
                $return = ['status' => 'FAIL',
                    'msg' => 'There is no base salary defined for the Employee(s) : ' . rtrim($missed_employee_ids, ',')];
            } else {
                $return = ['status' => 'SUCCESS',
                    'msg' => 'Success'];
            }


            return AxisPro::SendResponse($return);

        }
    }

    public function get_shift_elaps_time()
    {

    }




    public function reset_payslip()
    {

        $empids=explode(",",$_POST['empl_id']);
        $dept_id=$_POST['dept_id'];
        $month=$_POST['month'];
        $year=$_POST['year'];

        $sql="SELECT id FROM 0_kv_empl_payroll_master WHERE `pay_year`='".$year."' AND `pay_month`='".$month."' 
              AND dept_id='".$dept_id."' ";
        $result_qry= db_query($sql, "Query execution failed");

        if(db_num_rows($result_qry)>0)
        {
            $payslip_id=db_fetch($result_qry);




            foreach($empids as $emp_id)
            {
                $qry="SELECT id 
                        FROM 0_kv_empl_payroll_details WHERE payslip_id='".$payslip_id[0]."'";
                $qry.=" and empl_id='".$emp_id."' ";



                $res_qry= db_query($qry, "Query execution failed");
                if(db_num_rows($res_qry)>0)
                {

                    $d_sql=" DELETE
                                            FROM 0_kv_empl_payroll_elements 
                                            WHERE payslip_detail_id IN (SELECT id 
                                            FROM 0_kv_empl_payroll_details WHERE payslip_id='".$payslip_id[0]."' and empl_id='".$emp_id."')";


                    if(db_query($d_sql, "Query execution failed"))
                    {
                        $del_sql="DELETE FROM 0_kv_empl_payroll_details WHERE payslip_id='".$payslip_id[0]."' and empl_id='".$emp_id."' ";
                        db_query($del_sql, "Query execution failed");
                        /*--------------DELETE MASTER DATA FOR THAT MONTH-----------*/
                        if(db_query($del_sql, "Query execution failed"))
                        {
                            // $sql_m="DELETE FROM 0_kv_empl_payroll_master WHERE id='".$payslip_id[0]."'";
                            // db_query($sql_m, "Query execution failed");
                        }
                        /*----------------------END---------------------------------*/
                    }

                }
            }


            return AxisPro::SendResponse(['status' => 'OK', 'msg' => "Success"]);
        }
        else
        {
            return AxisPro::SendResponse(['status' => 'FAIL', 'msg' => "There is no payroll exists for the selected period."]);
        }


    }

    public function numberTowords($num)
    {
        $ones = array(
            0 =>"ZERO",
            1 => "ONE",
            2 => "TWO",
            3 => "THREE",
            4 => "FOUR",
            5 => "FIVE",
            6 => "SIX",
            7 => "SEVEN",
            8 => "EIGHT",
            9 => "NINE",
            10 => "TEN",
            11 => "ELEVEN",
            12 => "TWELVE",
            13 => "THIRTEEN",
            14 => "FOURTEEN",
            15 => "FIFTEEN",
            16 => "SIXTEEN",
            17 => "SEVENTEEN",
            18 => "EIGHTEEN",
            19 => "NINETEEN",
            "014" => "FOURTEEN"
        );
        $tens = array(
            0 => "ZERO",
            1 => "TEN",
            2 => "TWENTY",
            3 => "THIRTY",
            4 => "FORTY",
            5 => "FIFTY",
            6 => "SIXTY",
            7 => "SEVENTY",
            8 => "EIGHTY",
            9 => "NINETY"
        );
        $hundreds = array(
            "HUNDRED",
            "THOUSAND",
            "MILLION",
            "BILLION",
            "TRILLION",
            "QUARDRILLION"
        );
        $num = number_format($num,2,".",",");
        $num_arr = explode(".",$num);
        $wholenum = $num_arr[0];
        $decnum = $num_arr[1];
        $whole_arr = array_reverse(explode(",",$wholenum));
        krsort($whole_arr,1);
        $rettxt = "";
        foreach($whole_arr as $key => $i){

            while(substr($i,0,1)=="0")
                $i=substr($i,1,5);
            if($i < 20){
                /* echo "getting:".$i; */
                $rettxt .= $ones[$i];
            }elseif($i < 100){
                if(substr($i,0,1)!="0")  $rettxt .= $tens[substr($i,0,1)];
                if(substr($i,1,1)!="0") $rettxt .= " ".$ones[substr($i,1,1)];
            }else{
                if(substr($i,0,1)!="0") $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0];
                if(substr($i,1,1)!="0")$rettxt .= " ".$tens[substr($i,1,1)];
                if(substr($i,2,1)!="0")$rettxt .= " ".$ones[substr($i,2,1)];
            }
            if($key > 0){
                $rettxt .= " ".$hundreds[$key]." ";
            }
        }
        if($decnum > 0){
            $rettxt .= " and ";
            if($decnum < 20){
                $rettxt .= $ones[$decnum];
            }elseif($decnum < 100){
                $rettxt .= $tens[substr($decnum,0,1)];
                $rettxt .= " ".$ones[substr($decnum,1,1)];
            }
        }
        return $rettxt;
    }


    public function getPayslips()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);
        $month=$_POST['month'];
        $dept_id=$_POST['dept_id'];
        $empl_id=$_POST['emp_id'];

        $sql="SELECT a.payslip_id,a.pay_year,b.description as dept_name,MONTHNAME(STR_TO_DATE(a.pay_month, '%m')) AS MonthName
                FROM 0_kv_empl_payroll_master as a
                INNER join 0_kv_empl_departments as b ON a.dept_id=b.id
                where a.pay_year='".date('Y')."' AND a.pay_month='".$month."'";
        $result = db_query($sql);
        $data=array();
        while ($myrow = db_fetch_assoc($result)) {
            $data[] = array(
                $myrow['payslip_id'],
                $myrow['pay_year'],
                $myrow['MonthName'],
                $myrow['dept_name'],
                ''
            );
        }

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($result),
            "recordsFiltered" => db_num_rows($result),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);
    }

    public function get_dept_payrolls()
    {
        $month=$_POST['month'];
        $dept_id=$_POST['dept_id'];

        $sql = "SELECT a.payslip_id,a.pay_year,a.pay_month,a.id,a.payslip_status
                FROM 0_kv_empl_payroll_master as a
                where a.pay_year='".date('Y')."' 
                AND a.pay_month='".$month."' AND a.dept_id='".$dept_id."'";
        $result = db_query($sql);
        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result);
    }

    public function getPayElementsASHeading()
    {
        $sql="SELECT element_name,id,account_code,`type` as element_type
              FROM 0_kv_empl_pay_elements where status='1' and type='2'";
        $result = db_query($sql);
        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }
        return $return_result;
    }

    public function getEarningspayelemnts()
    {
        $sql="SELECT element_name,id,account_code,`type` as element_type,deletable,is_basic,non_fixed_ear_flag
              FROM 0_kv_empl_pay_elements where status='1' and type='1' and hide_from_empl='0'";
        $result = db_query($sql);
        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }
        return $return_result;
    }

    public function list_payroll_details()
    {
        $payslip_id=$_POST['payslip_id'];
        $year=$_POST['year'];
        $month=$_POST['month'];

        if($payslip_id!='0' || $payslip_id!='')
        {
            $limit = 100;
            if ($_POST["pagecnt"]!='') {
                $page  = $_POST["pagecnt"];
            }
            else{
                $page=1;
            }

            $start_from = ($page-1) * $limit;

            $sql_payslip_details="SELECT a.empl_id,b.empl_id AS EmployeeID,CONCAT(b.empl_firstname,\" \",b.empl_lastname) as emp_name
                                ,b.mobile_phone,a.salary_amount,a.id,a.payslip_id,a.processed_salary,a.memo,b.id as empPKID,a.days_worked,a.leave_days,
                                a.commission,a.net_commission,a.leave_days,a.leave_absent_deduction,a.tot_salary_deduction,a.id as payslip_pk_id,a.loan_amount,a.advance_amount,a.pf_amount
                                ,a.payroll_porcessed,a.late_coming_deduction_minutes,j.calculate_commission
                                ,a.anual_leave_salary
                                FROM 0_kv_empl_payroll_details as a
                                INNER JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                                inner join 0_kv_empl_job as j ON j.empl_id=b.id
                                WHERE a.payslip_id='".$payslip_id."'";
            $sql_payslip_details.= " LIMIT $start_from, $limit";

            $result = db_query($sql_payslip_details);
            $data=array();

            $headings=$this->getPayElementsASHeading();

            $table="<table class=\"table\">
                       <thead><tr>
                         <th style='position: sticky;top: 0;background-color: #C1C1C1;'></th>
                         <th style='position: sticky;top: 0;background-color: #C1C1C1;'>". trans('Empl. ID') ."</th>
                         <th style='position: sticky;top: 0;background-color: #C1C1C1;'>". trans('Empl. Name') ."</th>";
            $type='';
            $pay_headings=$this->getEarningspayelemnts();
            for($i=0;$i<sizeof($pay_headings);$i++) {
                if($pay_headings[$i]['element_type']=='1')
                {
                    $table.="<th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans($pay_headings[$i]['element_name'].$type)."</th>";
                }


            }


            $table.="  
                      <th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('Total Salary')."</th>
                      <!--th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('OT HOURS')."</th>
                      <th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('OT AMOUNT')."</th-->
                      ";
            $type='';
            for($i=0;$i<sizeof($headings);$i++) {
                if($headings[$i]['element_type']=='2')
                {
                    $type='<span style="color:red;">(Deduction)</span>';
                }
                else
                {
                    $type='';
                }
                $table.="<th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans($headings[$i]['element_name'].$type)."</th>";
            }
            $table.=" 

 <th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('ADDITION')."</th>
                      <!--th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('NET COMMISSION')."</th-->
                      <!--th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('ABSNT HRS')."</th-->
                      <th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('LATE COMING DEDUCTION AMT (MINS)')."</th>
                      <th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('PRESENT DAYS')."</th>
                      <th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('LEAVE / ABSENT DAYS')."</th>
                      <th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('LEAVE / ABSENT DED')."</th>
                    
                      <th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('Total salary Ded')."</th>
                      <th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('Total Salary') ."</th>";

            $table.="  <th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('NET PAYABLE SALARY')."</th>
                       <th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('Transaction Mode')."</th>
                       <th style='position: sticky;top: 0;background-color: #C1C1C1;'>".trans('Memo')."</th>
                     </tr></thead>
                   <tbody>";


            $number_of_days_in_month=cal_days_in_month(CAL_GREGORIAN, $month, $year);

            $sql_cut="SELECT `value` as cutdate FROM 0_sys_prefs WHERE `name`='payroll_cutoff_date'";
            $cutof_date=db_fetch(db_query($sql_cut));
            if($cutof_date[0]=='')
            {
                $cutoff_day='0';
            }
            else
            {
                $cutoff_day=$cutof_date[0];
            }


            if($cutoff_day=='' || $cutoff_day=='0')
            {
                $from_date=$year.'-'.$month.'-'.'01';
                $to_date=$year.'-'.$month.'-'.$number_of_days_in_month;
            }
            else
            {
                $start_month=$month;
                $monthNum = $start_month;
                $cutoff_month=Date('n', strtotime(date("F", mktime(0, 0, 0, $monthNum, 10)) . " last month"));

                $from_date=$year.'-'.$cutoff_month.'-'.$cutoff_day;
                $to_date=$year.'-'.$month.'-'.($cutoff_day-1);

            }


            $m=0;
            $gpssa=0;
            $days_worked=0;
            $absent_days=0;
            $tot_salary_dedn=0;
            $tot_salary_employee=0;
            $paying_salary=0;
            $employee_commison='0';
            $salary_amount='0';
            $emp_pf=0;
            $company_share=0;
            $disp_item='';
            $flag_process='';
            $ded_warning_amount=0;
            $tot_ded_warning=0;
            while ($myrow = db_fetch_assoc($result)) {

                /*----------------------------END---------------------------*/
                $qry="SELECT SUM(a.pay_amount) AS pay_incr,

                    (SELECT SUM(a.pay_amount)
                    FROM 0_kv_empl_salary_details AS a
                    INNER JOIN 0_kv_empl_pay_elements AS b ON a.pay_rule_id=b.id 
                    WHERE a.emp_id='".$myrow['empPKID']."' AND b.`type`=2) AS pay_decr
                    
                    FROM 0_kv_empl_salary_details AS a
                    INNER JOIN 0_kv_empl_pay_elements AS b ON a.pay_rule_id=b.id 
                    WHERE a.emp_id='".$myrow['empPKID']."' AND b.`type`=1";
                $res_data = db_query($qry);
                $ded_tot_salary= db_fetch($res_data);
                $empl_total_salary_result=$ded_tot_salary[0];
                /*-------------------PF Calculation-------------*/

                /*************************EMPLOYEE TOTAL PAYELEMNT DED**********/
                $sql_tot_pay_ele_ded="SELECT SUM(a.amount)
                                        FROM 0_kv_empl_payroll_elements AS a
                                        inner join 0_kv_empl_pay_elements AS b  ON a.pay_element=b.id
                                        WHERE a.payslip_detail_id='".$myrow['id']."' AND b.`type`=2";
                $pay_element_tot_ded=db_fetch(db_query($sql_tot_pay_ele_ded));

                /*********************GET EMP PF & COMP PF Percentage*************/
                $sql_pf="select value From 0_sys_prefs where `name` in ('payroll_pf_comp_percent','payroll_emp_pf_percent') ";
                $pf_per_value=db_fetch(db_query($sql_pf));
                if($pf_per_value[0]!='')
                {
                    $emp_pf=$empl_total_salary_result*$pf_per_value[0]/100;
                }
                if($pf_per_value[1]!='')
                {
                    $company_share=$empl_total_salary_result*$pf_per_value[1]/100;
                }



                $gpssa=$emp_pf+$company_share;

                /*****************************END****************************/


                $qry_pay="SELECT  mod_of_pay,department from 0_kv_empl_job 
                      where empl_id='".$myrow['empPKID']."'";
                $res_data_pay = db_query($qry_pay);
                $pay_data_set= db_fetch($res_data_pay);


                if($myrow['payroll_porcessed']=='1')
                {
                    $disp_item='<label style="color:green;font-weight:bold;">Processed</label>';
                    $flag_process='1';
                }
                else
                {
                    $disp_item="<input type='checkbox' name='chkEmployee' alt_id='".$myrow['payslip_pk_id']."' alt_incr='".$m."' 
                                alt_empl_id='".$myrow['empPKID']."' checked='checked' />";
                }


                $table.="<tr>
<td>".$disp_item."</td>
                           <td>".$myrow['EmployeeID']."</td>
                           <td>".$myrow['emp_name']."</td>";
                $disabled='';
                for($k=0;$k<sizeof($pay_headings);$k++)
                {
                    $sql_Pay="SELECT amount,pay_element,id
                              FROM 0_kv_empl_payroll_elements 
                              WHERE payslip_detail_id='".$myrow['id']."' and pay_element='".$pay_headings[$k]['id']."' ";
                    $results = db_query($sql_Pay);
                    if(sizeof($results)>0)
                    {
                        $empl_result= db_fetch($results);
                        $amnt=$empl_result[0];
                    }
                    else
                    {
                        $amnt='0';
                    }

                    if($empl_result[1]!='')
                    {
                        $txt_box_class='txt_amt';
                    }

                    if($pay_headings[$k]['non_fixed_ear_flag']=='0' || $pay_headings[$k]['is_basic']=='1')
                    {
                        $disabled='disabled="disabled"';
                    }
                    else
                    {
                        $disabled='';
                    }


                    if($amnt=='')
                    {
                        $amnt=0;
                    }

                    $table.="<td><input type='text' ".$disabled." id='id_ear_".$m."_".$k."' size='4' alt_non_fixed_ear='".$pay_headings[$k]['non_fixed_ear_flag']."' alt_incr='".$m."' alt='".$pay_headings[$k]['id']."' alt_ded_ear='".$pay_headings[$k]['element_type']."' class='".$txt_box_class."' value='".$amnt."'/></td>";
                }

                $salary_payable='';
                if($myrow['processed_salary']=='' || $myrow['processed_salary']=='0')
                {
                    $salary_payable=$myrow['salary_amount']-$gpssa;
                }
                else
                {
                    $salary_payable= $myrow['salary_amount']- $myrow['processed_salary'];
                }

                /*-----------------------GET OVER TIME--------------*/
                $qry_db="SELECT SUM(overtime_amnt),SUM(extra_hour) from 0_kv_empl_overtime
                      where emp_id='".$myrow['empPKID']."' and `month`='".$month."' and `year`='".$year."' ";
                $data_res_over = db_query($qry_db);
                $data_overtime= db_fetch($data_res_over);



                $qry_db_other="SELECT amount
                              FROM 0_kv_empl_payroll_elements as a
                              INNER JOIN 0_kv_empl_pay_elements as b ON a.pay_element=b.id
                              WHERE a.payslip_detail_id='".$myrow['id']."' and holiday_allowance_flag='1'
                              ";
                $data_other_allwance = db_query($qry_db_other);
                $data_allownace= db_fetch($data_other_allwance);

                $employee_tot_salary=$ded_tot_salary[0]+$data_overtime[0];


                /*-------------------------END-----------------------*/
                //$paying_salary=$myrow['salary_amount']+$myrow['net_commission']-$myrow['tot_salary_deduction'];

                $pay_element_tot_ded[0]=$pay_element_tot_ded[0]+$myrow['leave_absent_deduction']+$myrow['late_coming_deduction_minutes'];

                if($myrow['calculate_commission']=='1')
                {

                    if($pay_data_set['department']=='1')
                    {
                        $paying_salary=$myrow['salary_amount']+$myrow['commission']-$pay_element_tot_ded[0];
                    }
                    else
                    {
                        if($empl_total_salary_result<$myrow['commission'])
                        {
                            $paying_salary=$myrow['salary_amount']+($myrow['commission']-$pay_element_tot_ded[0]-$myrow['salary_amount']);

                        }
                        else
                        {
                            if($myrow['commission']=='0')
                            {
                                $paying_salary=$myrow['salary_amount']-($pay_element_tot_ded[0]);
                            }
                            else
                            {
                                $paying_salary=$myrow['salary_amount']+($myrow['commission']-$pay_element_tot_ded[0]-$myrow['salary_amount']);
                            }

                        }
                    }

                }
                else
                {

                    $paying_salary=$myrow['salary_amount']-($pay_element_tot_ded[0]);

                }





                $salary_amount=$myrow['salary_amount'];



                $table.=" 
                         <td><input type='text' disabled='disabled' class='Cls_Employee_Tot_Sala' id='txtTotal_salary_".$m."' alt='".$m."' size='5' value='".$empl_total_salary_result."'/></td>    
     <!--td><input type='text' class='ClsOT' id='txt_ot_".$m."' alt='".$m."' size='5' value='".$data_overtime[1]."'/></td> 
                          <td><input type='text' class='ClsOtAmount' id='txt_otAmount_".$m."' alt='".$m."' size='5' value='".$data_overtime[0]."'/></td--> 
                          ";
                $amnt_ded='';
                for($k=0;$k<sizeof($headings);$k++)
                {
                    $sql_Pay="SELECT amount,pay_element,id
                              FROM 0_kv_empl_payroll_elements 
                              WHERE payslip_detail_id='".$myrow['id']."' and pay_element='".$headings[$k]['id']."' ";
                    $results = db_query($sql_Pay);
                    if(sizeof($results)>0)
                    {
                        $empl_result= db_fetch($results);
                        $amnt_ded=$empl_result[0];
                    }
                    else
                    {
                        $amnt_ded='0';
                    }

                    if($empl_result[1]!='')
                    {
                        $txt_box_class='txt_amt';
                    }



                    $table.="<td><input type='text' id='id_txt_".$m."_".$k."' size='4' alt_incr='".$m."' alt='".$headings[$k]['id']."' alt_ded_ear='".$headings[$k]['element_type']."' class='".$txt_box_class."' value='".$amnt_ded."'/></td>";
                }






                $table.="
<!--td><input type='text' class='ClsWarning' id='txt_Warning_".$m."' alt='".$m."' size='5' value='".$tot_ded_warning."'/></td-->
<!--td><input type='text' class='ClsPf' id='txt_pf_".$m."' alt='".$m."' size='5' value='".$myrow['pf_amount']."'/></td-->
<td><input type='text' class='ClsCommisn' id='txt_commission_".$m."' alt='".$m."' size='5' value='".$myrow['commission']."'/></td>
                          <!--td><input type='text' class='ClsNetComsion' id='txt_net_commision_".$m."' alt='".$m."' size='5' value='".$myrow['net_commission']."'/></td-->
                          <!--td><input type='text' class='ClsLoan' id='txt_loan_amnt_".$m."' alt='".$m."' size='5'   value='".$myrow['loan_amount']."'/></td>
                          <td><input type='text' class='ClsAdvance' id='txt_adv_amnt_".$m."' alt='".$m."' size='5'   value='".$myrow['advance_amount']."'/></td-->
                          <!--td><input type='text' class='ClsAbsntHrs' id='txt_absent_hrs_".$m."' alt='".$m."' size='5' value='".$myrow['late_coming_deduction_minutes']."'/></td-->
                          <td><input type='text' class='ClsDed_amnt' id='txt_deduction_amnt_".$m."' alt='".$m."' size='5'   value='".$myrow['late_coming_deduction_minutes']."' /></td>
                          <td><input type='text' class='ClsPresnt_dys' id='txt_present_days_".$m."' alt='".$m."' size='5' value='".$myrow['days_worked']."'/></td>
                          <td><input type='text' class='ClsAbsnt_days' id='txt_absent_days_".$m."' alt='".$m."' size='5' value='".$myrow['leave_days']."'/></td>
                          <td><input type='text' class='ClsAbsnt_dedn' id='txt_absent_dedn_".$m."' alt='".$m."' size='5' value='".$myrow['leave_absent_deduction']."'/></td>
                          <td><input type='text' id='txt_tot_salary_dedn_".$m."' value='".$pay_element_tot_ded[0]."'/></td>";
                $table.=" <td><input type='text' id='txt_empl_tot_salary_".$m."' alt='".$m."' value='".$salary_amount."'/></td>
                          <td><input type='textbox' class='ClsPayingAmnt' id='txtTotal_salary_paying_".$m."' size='4' alt=".$myrow['id']." alt_slip_id=".$myrow['payslip_id']."  altsalary=".number_format($paying_salary,2)." value='".$paying_salary."'/>
                          <input type='hidden' id='hdn_emp_tot_salary_".$m."' value='".$paying_salary."' /></td>
                          <!--<td><label style='color:red;font-weight:bold;'></label></td>-->
                          <td><input type='text' id='txt_trn_mode_".$m."' value=''/></td>
                          <td><textarea class='ClsTxtMemo' id='txt_memo_".$m."' style='height: 25px;'>".$myrow['memo']."</textarea>
                          <input type='hidden' id='hdnEmployeeTot_slary_".$m."' value='".$empl_result[0]."' />
                          <input type='hidden' id='hdnPresent_days_".$m."' value='".$days_worked."' />
                          <input type='hidden' id='hdn_tot_salary_include_overtime_".$m."' value='".$employee_tot_salary."' />
                          <input type='hidden' id='hdn_tot_anual_salary_amount_".$m."' value='".$myrow['anual_leave_salary']."' />
                          <input type='hidden' id='hdn_empl_deptid_".$m."' value='".$pay_data_set['department']."' />
                          </td>
                         </tr>";

                $m++;
            }
            $table.="</tbody></table> <input type='hidden' id='hdnTotalHeadings' value='".sizeof($headings)."' />
            <input type='hidden' id='hdnTotal_Earnings_Headings' value='".sizeof($pay_headings)."' />
            <input type='hidden' id='hdnMonth_days' value='".$number_of_days_in_month."' />
            <input type='hidden' id='hdn_payroll_process' value='".$flag_process."' />
            ";


            /*********************PAGINATION STARTS**********************/
            $table.= " <ul class='pagination' style='float: right;'><li style='padding: 7px;'>PAGES :</li>";
            /*******GET TOTAL COUNT OF RECORDS***********/
            $totCnt="SELECT *
                                FROM 0_kv_empl_payroll_details as a
                                INNER JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                                WHERE a.payslip_id='".$payslip_id."'";

            $result_tot=db_query($totCnt);
            /**************END***************************/
            $total_records =db_num_rows($result_tot);
            $total_pages = ceil($total_records / $limit);
            $background='';
            for ($i=1; $i<=$total_pages; $i++) {
                if($i==$_POST["pagecnt"])
                {
                    $background='style="background-color:#ebedf2 !important;cursor: pointer;"';
                }
                else
                {
                    $background='style="background-color:#fff !important;cursor: pointer;"';
                }
                $table .= "<li class='page-item' ><span class='page-link' $background alt='".$i."'>".$i."</span></li>";
            }
            $table .="</ul>";
            /********************************ENDS*********************/

            return AxisPro::SendResponse($table);

        }

    }


    public function process_payroll()
    {
        $salary_data=$_POST['salaries'];
        $tot_salary=$salary_data[0]['emp_tot_slaary'];


        $flag='1';
        $Refs = new references();
        $ref = $Refs->get_next(ST_JOURNAL, null, Today());
        $trans_type = 0;
        $total_gl = 0;
        $trans_id = get_next_trans_no(0);
        $i=0;

        foreach($salary_data as $p_salary)
        {


            /*----------------------------UPDATE Processed salary in the details table-----------*/

            $sql="UPDATE 0_kv_empl_payroll_details set leave_days='".$p_salary['absent_days']."',days_worked='".$p_salary['present_days']."',salary_amount='".$p_salary['tot_salary']."',
                  memo='".$p_salary['memo']."',tot_salary_deduction='".$p_salary['tot_salary_ded']."',commission='".$p_salary['commison']."',net_commission='".$p_salary['net_commison']."',absent_hours='".$p_salary['absent_hrs']."',
                  absent_ded_amount_hrs='".$p_salary['absent_ded_amount']."',leave_absent_deduction='".$p_salary['absent_ded']."'
                  ,payroll_porcessed='1',tot_salary_payable='".$p_salary['tot_salary_payble']."',pf_amount='".$p_salary['pf_amount']."' 
                  ,transaction_type='".$p_salary['trans_mode']."' where id='".$p_salary['payslip_pk_id']."'";
            if(db_query($sql))
            {




                foreach($p_salary['payelemnts_push'] as $pay_elem)
                {
                    $sql_chk="select id from 0_kv_empl_payroll_elements where pay_element='".$pay_elem['element_id']."' 
                              and payslip_detail_id='".$p_salary['payslip_pk_id']."'";
                    $rows_cnt=db_fetch(db_query($sql_chk));

                    if($rows_cnt['id']!='')
                    {
                        $update_elemnts="Update 0_kv_empl_payroll_elements set amount='".$pay_elem['element_val']."' 
                                         where pay_element='".$pay_elem['element_id']."' 
                                         and payslip_detail_id='".$p_salary['payslip_pk_id']."'";
                    }
                    else
                    {
                        $update_elemnts=" INSERT Into 0_kv_empl_payroll_elements (payslip_detail_id,pay_element,amount)
                                          values ('".$p_salary['payslip_pk_id']."','".$pay_elem['element_id']."'
                                          ,'".$pay_elem['element_val']."')";
                    }

                    db_query($update_elemnts);

                }

                /*
                                if($p_salary['overtime_amount']!='')
                                {
                                    $get_sqls="SELECT id,account_code
                                                      FROM 0_kv_empl_pay_elements WHERE is_over_time_accnt='1'";
                                    $over_time_accs=db_fetch(db_query($get_sqls));

                                    $overtime_insert="INSERT INTO 0_kv_empl_payroll_elements (payslip_detail_id,pay_element,amount)
                                                          VALUES ('".$p_salary['payslip_pk_id']."','".$over_time_accs[0]."','".$p_salary['overtime_amount']."')";
                                    db_query($overtime_insert);

                                    $sql="INSERT into 0_kv_empl_overtime (emp_id,year,extra_hour,overtime_amnt,`month`,approved_by,approved_on)
                                          values ('".$p_salary['alt_emp_id']."','".$p_salary['year']."','".$p_salary['overtime_hours']."'
                                          ,'".$p_salary['overtime_amount']."','".$p_salary['month']."','".$_SESSION['wa_current_user']->user."','".date('Y-m-d h:i:s')."')";
                                    db_query($sql);
                                }*/

                /******************************PUBLIC HOLIDAY AMOUNT ADDING**************************/




                /*******************************END**************************************************/



            }

            /******************************CHK If employee has personal accounts*****************/


            $sql_emp_data="select empl_firstname from 0_kv_empl_info where id='".$p_salary['alt_emp_id']."'";
            $emp_per_data=db_fetch(db_query($sql_emp_data));

            /****************************GL Entry For Process Payout*****************************/




            $sql_chk_pay="SELECT b.account_code,a.amount,b.element_name,b.type,b.id
                            from 0_kv_empl_payroll_elements AS a
                            INNER JOIN 0_kv_empl_pay_elements AS b ON b.id=a.pay_element
                            where  a.payslip_detail_id='".$p_salary['payslip_pk_id']."'";
            $res_pay_elemnt=db_query($sql_chk_pay);
            $sign_and_amnt='';
            $pay_memo='';
            while($pay_res=db_fetch($res_pay_elemnt))
            {
                if($pay_res['type']=='1')
                {
                    $sign_and_amnt=$pay_res['amount'];
                }
                else
                {
                    $sign_and_amnt='-'.$pay_res['amount'];
                }

                $pay_memo=$pay_res['element_name'].' For Employee :'.$emp_per_data['empl_firstname'];


                $sql_qery="select sub_account_code from 0_kv_empl_account_mapping 
                    where emp_id='".$p_salary['alt_emp_id']."' and element_id='".$pay_res['id']."'";
                $sub_led_data=db_fetch(db_query($sql_qery));

                $sql_temp_gl="INSERT INTO 0_kv_empl_temp_gl (payroll_ref_id,emp_id,account,amount,created_on,pay_element_id,memo,sub_ledger,gl_flag)
                                  VALUES ('".$_POST['payroll_id']."','".$p_salary['alt_emp_id']."','".$pay_res['account_code']."'
                                  ,'".$sign_and_amnt."','".date('Y-m-d')."','".$pay_res['id']."','".$pay_memo."','".$sub_led_data[0]."','0')";
                db_query($sql_temp_gl);



            }


            $SQLQRY_Commision="SELECT b.account_code,b.id
                            from 0_kv_empl_pay_elements AS b
                            where b.is_commison_account='1'";

            $commsion_data=db_fetch(db_query($SQLQRY_Commision));


            if($commsion_data['account_code']!='')
            {
                $sql_qery_commison="select sub_account_code from 0_kv_empl_account_mapping
                                    where emp_id='".$p_salary['alt_emp_id']."' and element_id='".$commsion_data['id']."'";
                $sub_led_data=db_fetch(db_query($sql_qery_commison));

                $sql_temp_gl="INSERT INTO 0_kv_empl_temp_gl (payroll_ref_id,emp_id,account,amount,created_on,pay_element_id,memo,sub_ledger,gl_flag)
                                  VALUES ('".$_POST['payroll_id']."','".$p_salary['alt_emp_id']."','".$commsion_data['account_code']."'
                                  ,'".$p_salary['net_commison']."','".date('Y-m-d')."','".$commsion_data['id']."',
                                  '".'Commission For Employee :'.$emp_per_data['empl_firstname']."','".$sub_led_data['sub_account_code']."','0')";
                db_query($sql_temp_gl);
            }




            $Qry_payable="SELECT `value` FROM 0_sys_prefs 
                                         WHERE `name`='payroll_payable_act'";
            $net_payabale_acc=db_fetch(db_query($Qry_payable));


            if($net_payabale_acc['value']!='')
            {


                $SQLQRY="select payable_empl_subledger 
                                     from 0_kv_empl_job where empl_id='".$p_salary['alt_emp_id']."'"    ;

                $sub_payable_data=db_fetch(db_query($SQLQRY));

                $sql_temp_gl="INSERT INTO 0_kv_empl_temp_gl (payroll_ref_id,emp_id,account,amount,created_on,pay_element_id,memo,sub_ledger,gl_flag)
                                  VALUES ('".$_POST['payroll_id']."','".$p_salary['alt_emp_id']."','".$net_payabale_acc['value']."'
                                  ,'".'-'.$p_salary['tot_salary_payble']."','".date('Y-m-d')."','5555',
                                  '". 'Employee net salary payable :'.$p_salary['tot_salary_payble']."','".$sub_led_data['sub_account_code']."','0')";
                db_query($sql_temp_gl);

            }






            /******************************END******************************************************/

            $i++;
        }



        //return AxisPro::SendResponse(['status'=>'OK','msg'=>'Success']);
        echo json_encode(['status' => 'OK', 'msg' => 'Success']);

        $this->sendMailToFinanceDept($_POST['payroll_id']);
    }


    /******
     * UPDATE SETTINGS
     *
     ***/
    public function update_hrm_settings()
    {
        $dis_msg='';
        $upload_msg='';

        $path_to_root='../..';
        if(!empty($_FILES["policy"]))
        {
            $target_dir = $path_to_root."/assets/uploads/";

            $fname=explode(".",$_FILES["policy"]["name"]);
            $rand=rand(10,100);
            $filename=$fname[0].'_'.$rand.'.'.$fname[1];
            $target_file = $target_dir . basename($filename);
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

            if ($_FILES["policy"]["size"] > 50000000) {
                $upload_msg='File size exceeded';
            }
            if($imageFileType != "pdf" && $imageFileType != "docx") {
                $upload_msg='File format is not allowed';
            }

            if (move_uploaded_file($_FILES["policy"]["tmp_name"], $target_file)) {
                $upload_msg='Employee document saved successfully';

                $sql_policy=" UPDATE 0_sys_prefs SET value='".$filename."' WHERE name='privacy_policy'";
                db_query($sql_policy);
            }

        }

        if(!empty($_FILES["code_of_condct"]))
        {
            $target_dir = $path_to_root."/assets/uploads/";

            $fname=explode(".",$_FILES["code_of_condct"]["name"]);
            $rand=rand(10,100);
            $filename=$fname[0].'_'.$rand.'.'.$fname[1];
            $target_file = $target_dir . basename($filename);
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

            if ($_FILES["code_of_condct"]["size"] > 50000000) {
                $upload_msg='File size exceeded';
            }
            if($imageFileType != "pdf" && $imageFileType != "docx") {
                $upload_msg='File format is not allowed';
            }

            if (move_uploaded_file($_FILES["code_of_condct"]["tmp_name"], $target_file)) {
                $upload_msg='Employee document saved successfully';
                $sql_code=" UPDATE 0_sys_prefs SET value='".$filename."' WHERE name='code_of_conduct'";
                db_query($sql_code);
            }

        }

        if($upload_msg=='')
        {
            $array=array('payroll_payable_act','payroll_deductleave_act','payroll_work_hours','payroll_salary_deduction','payroll_cutoff_date',
                'payroll_overtime_rate','payroll_overtime_act','payroll_gorss_salary_act','payroll_work_hours_to'
            ,'payroll_emp_pf_percent','payroll_pf_comp_percent','leave_request_pfx','loan_request_pfx','passport_request_pfx'
            ,'certif_request_pfx','noc_request_pfx','asset_request_pfx','asset_return_req_pfx','payroll_esb_account',
                'payroll_latecoming_deduction','payroll_grace_time','payroll_personal_time_hrs','payroll_personal_selection',
                'payroll_personal_assigned_leave');
            for($i=0;$i<sizeof($array);$i++)
            {
                $sql=" UPDATE 0_sys_prefs SET value='".$_POST[$array[$i]]."' WHERE name='".$array[$i]."'";
                //echo $sql.'---';
                if(db_query($sql))
                {
                    $dis_msg='Data Saved Successfully';
                }
                else
                {
                    $dis_msg='Failed to save Data';
                }
            }

            return AxisPro::SendResponse(['status'=>'OK','msg'=>$dis_msg]);
        }
        else
        {
            return AxisPro::SendResponse(['status'=>'OK','msg'=>$upload_msg]);
        }





    }

    /****GET SAVED SETTINGS
     *
     * */
    public function get_saved_settings()
    {
        $sql="SELECT `name`,`value` 
              FROM 0_sys_prefs WHERE `name` in  ('payroll_work_hours','payroll_salary_deduction','payroll_cutoff_date'
                ,'payroll_overtime_rate','payroll_overtime_act','payroll_deductleave_act','payroll_payable_act','payroll_gorss_salary_act','payroll_work_hours_to'
                ,'payroll_emp_pf_percent','payroll_pf_comp_percent','leave_request_pfx','loan_request_pfx','passport_request_pfx',
                'certif_request_pfx','noc_request_pfx','asset_request_pfx','asset_return_req_pfx','payroll_esb_account','payroll_latecoming_deduction','payroll_grace_time','payroll_personal_time_hrs','payroll_personal_selection','payroll_personal_assigned_leave')
              ORDER BY `name` desc";

        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[$myrow['name']] = $myrow['value'];
        }
        return AxisPro::SendResponse($return_result);
    }


    public function get_document_types()
    {
        $sql="SELECT description,days,CASE WHEN inactive='0' THEN 'Inactive'
              WHEN inactive='1' THEN 'Active' END AS `status`,id,inactive
              FROM 0_kv_empl_doc_type ORDER BY id";

        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }
        return AxisPro::SendResponse($return_result);
    }

    public function Save_doc_type()
    {
        $doc_type=$_POST['type'];
        $expiry_d=$_POST['notify'];
        $status=$_POST['status'];
        $hdn_id=$_POST['hdn_id'];

        if(isset($_POST))
        {
            if($hdn_id)
            {
                $sql="UPDATE 0_kv_empl_doc_type set description='".$doc_type."',days='".$expiry_d."',inactive='".$status."'
                     WHERE id='".$hdn_id."'";
            }
            else
            {
                $sql="INSERT into 0_kv_empl_doc_type (description,days,inactive) 
                VALUES ('".$doc_type."','".$expiry_d."','".$status."')";
            }

            if(db_query($sql))
            {
                return AxisPro::SendResponse(['status'=>'OK','msg'=>'Data Saved Successfully']);
            }
            else
            {
                return AxisPro::SendResponse(['status'=>'FAIL','msg'=>'Error occured while saving.']);
            }
        }
    }

    public function get_documents()
    {
        $sql="SELECT description,id
              FROM 0_kv_empl_doc_type ORDER BY id";
        $result = db_query($sql);
        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }
        return $return_result;
    }

    public function employee_docs_save()
    {
        $dept_id=$_POST['dept_id'];
        $mpl_id=$_POST['empl_id'];
        $doc_type=$_POST['doc_type'];
        $title=$_POST['title'];
        $hdn_edit=$_POST['hdnEdit'];

        $issuedate=date("Y-m-d",strtotime($_POST['issuedate']));
        $expirydate=date("Y-m-d",strtotime($_POST['expirydate']));
        $root_url=str_replace("/ERP","",getcwd());
        $root_url=str_replace("/API","",$root_url);

        if($_FILES["fileToUpload"]["name"]!='')
        {
            $target_dir = $root_url."/assets/uploads/";
            $fname=explode(".",$_FILES["fileToUpload"]["name"]);
            $rand=rand(10,100);
            $filename=$fname[0].'_'.$rand.'.'.$fname[1];
            $target_file = $target_dir . basename($filename);
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            $dis_msg='';

            if ($_FILES["fileToUpload"]["size"] > 50000000) {
                $dis_msg=['status'=>'FAIL','msg'=>'File size exceeded'];
            }
            if($imageFileType != "pdf" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "jpg") {
                $dis_msg=['status'=>'FAIL','msg'=>'File format is not allowed'];
            }
        }

        if($dis_msg=='')
        {
            $sel_sql="SELECT id FROM 0_kv_empl_info where empl_id='".$mpl_id."' ";
            $sel_emp_id= db_fetch(db_query($sel_sql, "Query execution failed"));

            if($hdn_edit=='')
            {
                $sql="INSERT INTO 0_employee_docs (dept_id,emp_id,type_id,description,issue_date,expiry_date,filename,
                      filesize,filetype,status) VALUES ('".$dept_id."','".$sel_emp_id[0]."','".$doc_type."','".$title."','".$issuedate."'
                      ,'".$expirydate."','".$filename."','".$_FILES["fileToUpload"]["size"]."','".$imageFileType."','1')";
            }
            else
            {
                $sql="UPDATE 0_employee_docs SET dept_id='".$dept_id."',emp_id='".$sel_emp_id[0]."',type_id='".$doc_type."'
                       ,description='".$title."',issue_date='".$issuedate."',expiry_date='".$expirydate."'  ";

                if($_FILES["fileToUpload"]["name"]!='')
                {
                    $sql.=" ,filename='".$filename."',filesize='".$_FILES["fileToUpload"]["size"]."',filetype='".$imageFileType."'";
                }

                $sql.=" WHERE id='".$hdn_edit."' ";
            }


            if(!db_query($sql))
            {
                $dis_msg=['status'=>'FAIL','msg'=>'Failed to save employee document'];
            }
            else
            {

                if($_FILES["fileToUpload"]["name"]!='')
                {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        $dis_msg=['status'=>'OK','msg'=>'Employee document saved successfully'];
                    }
                }
                else
                {
                    $dis_msg=['status'=>'OK','msg'=>'Employee document saved successfully'];
                }

            }
        }
        return AxisPro::SendResponse( $dis_msg);
    }

    public function list_employees_docs()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $root_url=str_replace("\ERP","",getcwd());
        $root_url=str_replace("\API","",$root_url);

        $sql_for_total='';

        $sql="SELECT b.description as Deptname,CONCAT(a.empl_id,' - ',a.empl_firstname) as EmpName,c.description as doc_title
              ,c.issue_date,c.expiry_date,c.filename,c.id,a.empl_id,c.dept_id,c.type_id,d.description as doc_type
                FROM 0_employee_docs AS c
                INNER JOIN 0_kv_empl_departments AS b ON b.id=c.dept_id
                INNER JOIN 0_kv_empl_info AS a ON a.id=c.emp_id
                INNER JOIN 0_kv_empl_doc_type AS d ON d.id=c.type_id";

        if(!empty($_POST['search']['value']))
        {
            $sql .=" WHERE ";
            $sql .=" ( a.empl_id LIKE '%".$_POST['search']['value']."%' ";
            $sql .=" OR a.empl_firstname LIKE '%".$_POST['search']['value']."%' )";
            //$sql .=" OR track_no LIKE '%".$_POST['search']['value']."%' )";
        }

        $sql .=" AND c.status='1' ";
        $sql_for_total=$sql;

        $sql .=" ORDER BY c.id LIMIT ".$start.",".$length." ";

        $result = db_query($sql);
        $data=array();
        while ($myrow = db_fetch_assoc($result)) {
            $alt_attr="alt_Dept='".$myrow['dept_id']."' alt_pk_id='".$myrow['id']."' 
                       alt_emp='".$myrow['empl_id']."' alt_type='".$myrow['type_id']."' alt_title='".$myrow['doc_title']."' 
                       alt_issue='".date("d-m-Y",strtotime($myrow['issue_date']))."' alt_exp_date='".date("d-m-Y",strtotime($myrow['expiry_date']))."' ";

            $data[] = array(
                $myrow['Deptname'],
                $myrow['EmpName'],
                $myrow['doc_type'],
                $myrow['doc_title'],
                date("d-m-Y",strtotime($myrow['issue_date'])),
                date("d-m-Y",strtotime($myrow['expiry_date'])),
                '<a href="assets/uploads/'.$myrow['filename'].'" target="_blank" >'.$myrow['filename'].'</a>',
                '<label class="ClsEdit" style="cursor: pointer;"  '.$alt_attr.'><i class=\'flaticon-edit\'></i></label>',
                '<label class="ClsRemove" style="cursor: pointer;"  '.$alt_attr.'><i class=\'flaticon-delete\'></i></label>'
            );
        }

        $sql_tot=$sql_for_total;
        $tot_result = db_query($sql_tot);

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($tot_result),
            "recordsFiltered" => db_num_rows($tot_result),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);
    }

    public function remove_doc()
    {
        $dis_msg='';
        $sql="UPDATE 0_employee_docs SET status='0' WHERE id='".$_POST['remove_id']."' ";
        if(db_query($sql))
        {
            $dis_msg=['status'=>'OK','msg'=>'Data Removed Successfully'];
        }
        else
        {
            $dis_msg=['status'=>'FAIL','msg'=>'Failed to remove data'];
        }

        return AxisPro::SendResponse($dis_msg);
    }

    public function getShifts()
    {
        $sql="SELECT  id,CONCAT('(',TIME_FORMAT(BeginTime, '%r'),' - ',TIME_FORMAT(EndTime, '%r'),')') AS description,shift_color
              FROM 0_kv_empl_shifts ORDER BY id";
        $result = db_query($sql);
        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }
        return $return_result;
    }




    public function export_payroll()
    {
        require '../../spreadsheet/vendor/autoload.php';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Payroll.xlsx"');
        header('Cache-Control: max-age=0');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];


        //$sheet->getStyle('A1:I1')->applyFromArray($styleArray);

        $sql_qry="SELECT pay_month,pay_year FROM 0_kv_empl_payroll_master WHERE id='".$_POST['txt_hdn_payroll_id']."'";
        $result_qry = db_query($sql_qry);
        $payroll_data=db_fetch($result_qry);

        $payslip_id="SELECT id FROM 0_kv_empl_payroll_details WHERE payslip_id='".$_POST['txt_hdn_payroll_id']."'";
        $payslip_id_data=db_fetch(db_query($payslip_id));
        if($payslip_id_data['id']!='')
        {
            $payelemnts_ear="select a.element_name,b.amount,a.type
                    FROM 0_kv_empl_payroll_elements as b 
                    INNER join 0_kv_empl_pay_elements as a ON a.id=b.pay_element
                    Where a.is_over_time_accnt='0' and b.payslip_detail_id='".$payslip_id_data['id']."'
                    AND a.type='1' ORDER BY b.pay_element";

            $pay_head_data_ear = db_query($payelemnts_ear);


            $payelemnts="select a.element_name,b.amount,a.type
                    FROM 0_kv_empl_payroll_elements as b 
                    INNER join 0_kv_empl_pay_elements as a ON a.id=b.pay_element
                    Where a.is_over_time_accnt='0' and b.payslip_detail_id='".$payslip_id_data['id']."'
                    AND a.type='2' ORDER BY b.pay_element";

            $pay_head_data = db_query($payelemnts);
        }



        $number_of_days_in_month=cal_days_in_month(CAL_GREGORIAN, $payroll_data['pay_month'], $payroll_data['pay_year']);

        $month_name = date("F", mktime(0, 0, 0, $payroll_data['pay_month'], 10));





        //$spreadsheet->getDefaultStyle()->getFont()->setSize(15);
        $sheet->setCellValue('A1', ' '.$month_name.' '.$payroll_data['pay_year'].' - Premium Businessmen Services');

        $spreadsheet->getActiveSheet()->mergeCells('A1:I1');

        // $sheet->setCellValue('D2', 'Month : '.$payroll_data['pay_month']);
        // $sheet->setCellValue('F2', 'Year : '.$payroll_data['pay_year']);   // For Boulevard

        //$spreadsheet->getDefaultStyle()->getFont()->setSize(8);

        $sheet->getStyle('A4:AB4')->applyFromArray($styleArray);

        $sheet->setCellValue('A4', 'S NO.');
        $sheet->setCellValue('B4', 'EMP. NO.');
        $sheet->setCellValue('C4', 'EMP. NAME');
        $sheet->setCellValue('D4', 'Employee Designation');
        $sheet->setCellValue('E4', 'Joining Date');
        $sheet->setCellValue('F4', 'duty hours in a day');

        $current_col_ear = 4;
        $current_row_ear = 6;

        while ($myrow_res_ear = db_fetch($pay_head_data_ear))  {

            $sheet->setCellValueByColumnAndRow($current_row_ear, $current_col_ear,$myrow_res_ear['element_name']);
            $current_row_ear++;

        }

        $sub_one=array('TOTAL SALARY','ROUTING CODE','SLARY IBAN','WORK PERMIT','DIVISION','BRANCH');
        $current_row_one=$current_row_ear;
        for($k=0;$k<=sizeof($sub_one);$k++)
        {

            $sheet->setCellValueByColumnAndRow($current_row_one, $current_col_ear,$sub_one[$k]);

            $current_row_one++;
        }

        $current_col = 4;
        $current_row = $current_row_one-1;
        while ($myrow_res = db_fetch($pay_head_data))  {

            $sheet->setCellValueByColumnAndRow($current_row, $current_col,$myrow_res['element_name']);
            $current_row++;
        }
        $sub_headings=array('COMMISSION','PRESENT DAYS','LEAVE / ABSENT DAYS','LATE COMING DEDUCTION','LEAVE ABSENT DEDUCTION','Total salary Ded','NET PAYABLE SALARY', 'TRANSACTION MODE','REMARKS');

        $current_row_sub=$current_row;
        for($k=0;$k<=sizeof($sub_headings);$k++)
        {
            $sheet->setCellValueByColumnAndRow($current_row_sub, $current_col,$sub_headings[$k]);
            $current_row_sub++;
        }


        $sql="SELECT a.empl_id,a.empl_firstname,c.description,b.joining,d.commission,d.net_commission,d.absent_hours,
                d.absent_ded_amount_hrs,d.days_worked,d.leave_days,d.leave_absent_deduction,d.tot_salary_deduction,
                d.tot_salary_payable,d.ot_hours,d.ot_amount,d.id,d.salary_amount,d.memo,d.transaction_type
                ,b.work_hours,a.id as emp_pk_id,d.late_coming_deduction_minutes
                FROM 0_kv_empl_payroll_details AS d 
                INNER JOIN 0_kv_empl_info AS a ON a.id=d.empl_id
                INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                INNER JOIN 0_kv_empl_designation AS c ON c.id=b.desig
                Where d.payroll_porcessed='1' AND d.payslip_id='".$_POST['txt_hdn_payroll_id']."' 
                Group by d.empl_id
                 ";


        $result = db_query($sql);
        $return_result = [];
        $i=5;
        $counter=1;
        $tot_salary_include_pay_sum=0;
        $ot_amnt_sum=0;
        $addntion_amnt=0;
        $gpssa_sum=0;
        $net_commision=0;
        $asbemt_hrs_amount=0;
        $absent_ded=0;
        $tot_salary=0;
        $net_slaary=0;
        $loan_adv_dedn=0;
        $misatek_dedn=0;
        $salary_released=0;
        $salary_tot_ded=0;
        $p=0;
        $last_row_id=0;
        $index=array();
        $get_pay_elem_column=array();
        $commission=0;
        while ($myrow = db_fetch($result)) {

            /**************************GET EMPLOYEE TOT SALARY*************/
            $emp_sql="SELECT SUM(a.pay_amount) AS total_slary 
                                    from 0_kv_empl_salary_details AS a
                                    INNER JOIN 0_kv_empl_pay_elements AS b ON a.pay_rule_id=b.id
                                    WHERE a.emp_id='".$myrow['emp_pk_id']."' AND b.`type`='1' AND b.`calculate_percentage`='0'
                                 ";
            $emp_tot_salary_data = db_fetch(db_query($emp_sql));
            /**********************************END*************************/


            $sheet->setCellValue('A' . $i, $counter);
            $sheet->setCellValue('B' . $i, $myrow['empl_id']);
            $sheet->setCellValue('C' . $i, $myrow['empl_firstname']);
            $sheet->setCellValue('D' . $i, $myrow['description']);
            $sheet->setCellValue('E' . $i, $myrow['joining']);
            $sheet->setCellValue('F' . $i,  $myrow['work_hours']);

            $i++;


            $payelemnts_ear_sql="select a.element_name,b.amount
                    FROM 0_kv_empl_payroll_elements as b 
                    INNER join 0_kv_empl_pay_elements as a ON a.id=b.pay_element
                    Where a.is_over_time_accnt='0' and b.payslip_detail_id='".$myrow['id']."' AND a.type='1' ORDER BY b.pay_element";
            $pay_earnins_value= db_query($payelemnts_ear_sql);
            if($p==0)
            {
                $current_col = 5;
            }
            else
            {
                $current_col = 5+$p;
            }

            $current_row = 6;
            while ($ear_row = db_fetch($pay_earnins_value))   {

                $sheet->setCellValueByColumnAndRow($current_row,$current_col,$ear_row['amount']);

                $get_pay_elem_column[$current_row]=$ear_row['amount'];
                $current_row++;
            }



            for($k=0;$k<=sizeof($sub_one);$k++)
            {
                /* $payelemnts_bind="select SUM(b.amount) as overtimesum
                      FROM 0_kv_empl_payroll_elements as b
                      INNER join 0_kv_empl_pay_elements as a ON a.id=b.pay_element
                      Where a.is_over_time_accnt='1' and b.payslip_detail_id='".$myrow['id']."'
                       ";
                 $overtime_sum=db_fetch(db_query($payelemnts_bind));*/

                $bank_sql="SELECT a.ifsc,a.iban,a.card_no_salary,b.description as branch,c.description as division
                          from 0_kv_empl_job AS a
                          INNER JOIN 0_kv_empl_departments AS b ON a.department=b.id
                          INNER JOIN 0_kv_empl_designation_group AS c ON c.depti_id=a.department 
                          where a.empl_id='".$myrow['emp_pk_id']."' ";
                $bank_data=db_fetch(db_query($bank_sql));





                $sub_one_tot=array($emp_tot_salary_data[0],$bank_data['card_no_salary']
                ,$bank_data['iban'],$bank_data['ifsc'],$bank_data['branch'],$bank_data['division']);
                $sheet->setCellValueByColumnAndRow($current_row, $current_col,$sub_one_tot[$k]);
                $current_row++;
            }



            $payelemnts_bind="select a.element_name,b.amount
                    FROM 0_kv_empl_payroll_elements as b 
                    INNER join 0_kv_empl_pay_elements as a ON a.id=b.pay_element
                    Where a.is_over_time_accnt='0' and b.payslip_detail_id='".$myrow['id']."' AND a.type='2' ORDER BY b.pay_element";
            $pay_head_data_bind = db_query($payelemnts_bind);
            if($p==0)
            {
                $current_col = 5;
            }
            else
            {
                $current_col = 5+$p;
            }

            $current_row = $current_row-1;
            while ($rosmma = db_fetch($pay_head_data_bind))   {

                $sheet->setCellValueByColumnAndRow($current_row,$current_col,$rosmma['amount']);

                $get_pay_elem_column[$current_row]=$rosmma['amount'];
                //array_push($get_pay_elem_column,$current_row);
                $current_row++;
            }

            for($r=0;$r<sizeof($sub_headings);$r++)
            {
                array_push($index,$current_row+$r);
            }

            $sheet->setCellValueByColumnAndRow($index[0],$current_col,$myrow['commission']);
            /*$sheet->setCellValueByColumnAndRow($index[1],$current_col,$myrow['net_commission']);*/
            /*$sheet->setCellValueByColumnAndRow($index[2],$current_col,$myrow['absent_hours']);*/
            /*$sheet->setCellValueByColumnAndRow($index[3],$current_col,$myrow['absent_ded_amount_hrs']);*/
            $sheet->setCellValueByColumnAndRow($index[1],$current_col,$myrow['days_worked']);
            $sheet->setCellValueByColumnAndRow($index[2],$current_col,$myrow['leave_days']);
            $sheet->setCellValueByColumnAndRow($index[3],$current_col,$myrow['late_coming_deduction_minutes']);
            $sheet->setCellValueByColumnAndRow($index[4],$current_col,$myrow['leave_absent_deduction']);
            /*$sheet->setCellValueByColumnAndRow($index[6],$current_col,$myrow['leave_absent_deduction']);*/
            $sheet->setCellValueByColumnAndRow($index[5],$current_col,$myrow['tot_salary_deduction']);
            $sheet->setCellValueByColumnAndRow($index[6],$current_col,$myrow['tot_salary_payable']);
            $sheet->setCellValueByColumnAndRow($index[7],$current_col,$myrow['transaction_type']);
            $sheet->setCellValueByColumnAndRow($index[8],$current_col,$myrow['memo']);

            $p++;

            $tot_salary_include_pay_sum=$tot_salary_include_pay_sum+$emp_tot_salary_data[0];
            //  $ot_amnt_sum=$ot_amnt_sum+$overtime_sum['overtimesum'];
            $addntion_amnt=$addntion_amnt+$myrow['additonal_payment'];
            $gpssa_sum=$gpssa_sum+$myrow['gpssa'];
            $net_commision=$net_commision+$myrow['net_commission'];
            $asbemt_hrs_amount=$asbemt_hrs_amount+$myrow['absent_ded_amnt'];
            $absent_ded=$absent_ded+$myrow['leave_absent_deduction'];
            // $tot_salary=$tot_salary+$myrow['tot_salary_payable'];
            $net_slaary=$net_slaary+$myrow['tot_salary_payable'];
            $loan_adv_dedn=$loan_adv_dedn+$myrow['loan_adv_ded'];
            $misatek_dedn=$misatek_dedn+$myrow['ded_mistake'];
            $salary_released=$salary_released+$myrow['salary_released'];
            $salary_tot_ded=$salary_tot_ded+$myrow['tot_salary_deduction'];
            $commission=$commission+$myrow['commission'];

            $counter++;

        }





        $colspan_one=$i+1;
        $sheet->setCellValue('C'.$colspan_one, 'TOTAL ');
        $sheet->setCellValue('G'.$colspan_one,  $tot_salary_include_pay_sum);

        foreach($get_pay_elem_column as $key=>$val)
        {
            $sheet->setCellValueByColumnAndRow($key,$colspan_one,$val);
        }



        $sheet->setCellValueByColumnAndRow($index[0],$colspan_one,$commission);
        //$sheet->setCellValueByColumnAndRow($index[1],$colspan_one,$net_commision);
        //$sheet->setCellValueByColumnAndRow($index[2],$colspan_one,$asbemt_hrs_amount);
        //$sheet->setCellValueByColumnAndRow($index[3],$colspan_one,$absent_ded);
        $sheet->setCellValueByColumnAndRow($index[1],$colspan_one,0);
        $sheet->setCellValueByColumnAndRow($index[2],$colspan_one,0);
        //$sheet->setCellValueByColumnAndRow($index[6],$colspan_one,$absent_ded);
        $sheet->setCellValueByColumnAndRow($index[5],$colspan_one,$salary_tot_ded);
        $sheet->setCellValueByColumnAndRow($index[6],$colspan_one,$net_slaary);
        $sheet->setCellValueByColumnAndRow($index[7],$colspan_one,0);
        $sheet->setCellValueByColumnAndRow($index[8],$colspan_one,0);


        $colspan=$i+3;
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        /* $payelemnts_bind="select SUM(b.amount) as overtimesum
                     FROM 0_kv_empl_payroll_elements as b
                     INNER join 0_kv_empl_pay_elements as a ON a.id=b.pay_element
                     Where a.is_over_time_accnt='1' and b.payslip_detail_id='".$myrow['id']."'
                      ";
         $overtime_sum=db_fetch(db_query($payelemnts_bind));*/

        /*$sheet->getStyle('C'.$colspan.':'.'I'.$colspan)->applyFromArray($styleArray);
        $sheet->setCellValue('C'.$colspan, 'Salary Released,Hold/Deductions & GPSSA :0 ');
        $spreadsheet->getActiveSheet()->mergeCells('C'.$colspan.':'.'I'.$colspan);*/

        /*   $sheet->getStyle('M'.$colspan.':'.'Q'.$colspan)->applyFromArray($styleArray);
           $sheet->setCellValue('M'.$colspan, 'Salary Payable Amt : '.$net_slaary);
           $spreadsheet->getActiveSheet()->mergeCells('M'.$colspan.':'.'Q'.$colspan);
   */

        /* $sheet->getStyle('C'.$colspan.':'.'I'.$colspan)->applyFromArray($styleArray);
         $sheet->setCellValue('C'.$colspan, 'Overtime & Add. Payment : '.$ot_amnt_sum);
         $spreadsheet->getActiveSheet()->mergeCells('C'.$colspan.':'.'I'.$colspan);*/


        $colspan=$colspan+2;
        $overt_add=$ot_amnt_sum+$addntion_amnt;


        /*  $sheet->getStyle('C'.$colspan.':'.'G'.$colspan)->applyFromArray($styleArray);
          $sheet->setCellValue('C'.$colspan, 'Commission Payable Amt : '.$net_commision);
          $spreadsheet->getActiveSheet()->mergeCells('C'.$colspan.':'.'G'.$colspan);*/

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

    }



    public function download_overtime()
    {
        require '../../spreadsheet/vendor/autoload.php';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Overtime.xlsx"');
        header('Cache-Control: max-age=0');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        $sheet->setCellValue('A1', 'Shift Date.');
        $sheet->setCellValue('B1', 'EMP. Code.');
        $sheet->setCellValue('C1', 'EMP. Name');
        $sheet->setCellValue('D1', 'Extra Hour Worked ');
        $sheet->setCellValue('E1', 'Overtime Amount Calculated');

        $month=$_POST['ddl_months'];
        $year=$_POST['ap-year'];
        $empl_id=$_POST['ap_employees'];
        $dept_id=$_POST['ddl_department'];


        /*******************GET WORKING HOUR********************************/
        $w_sql="SELECT `value`,`name` FROM 0_sys_prefs 
                 WHERE `name` IN ('payroll_work_hours','payroll_work_hours_to','payroll_overtime_rate')
                 ORDER BY NAME";
        $pay_roll_config=db_query($w_sql);
        while ($confg_data = db_fetch($pay_roll_config)) {
            $config_reslt[$confg_data['name']] = $confg_data['value'];
        }

        /*****************CHECK Employeee Have Work Hours************/
        $qry_sql="SELECT a.work_hours FROM 
                  0_kv_empl_info as b
                  Inner Join 0_kv_empl_job as a ON a.empl_id=b.id
                  where b.empl_id='".$empl_id."'";

        $empl_work_hrs=db_fetch(db_query($qry_sql));
        /************************EBND*********************************/

        if($empl_work_hrs['work_hours']!='0')
        {
            $working_hours=$empl_work_hrs['work_hours'];
        }
        else
        {
            $working_hours = $config_reslt['payroll_work_hours_to'] - $config_reslt['payroll_work_hours'];
        }



        $number_of_days_in_month=cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $from_date=$year.'-'.$month.'-01';
        $to_date=$year.'-'.$month.'-'.$number_of_days_in_month;
        /**********************END******************************************/

        $sql = "SELECT a.empl_id,CONCAT(a.empl_firstname,\" \",a.empl_lastname) AS Emp_name
                ,b.a_date,b.duration,a.id,TIMESTAMPDIFF(HOUR, b.in_time, b.out_time) as duration
                FROM 0_kv_empl_attendance AS b
                INNER JOIN 0_kv_empl_info AS a ON a.empl_id=b.empl_id
                INNER JOIN 0_kv_empl_job AS c ON c.empl_id=a.id
                WHERE c.department='".$dept_id."' AND b.a_date>='".$from_date."' AND b.a_date<='".$to_date."'
                AND TIMESTAMPDIFF(HOUR, b.in_time, b.out_time) >'".$working_hours."'";
        $result = db_query($sql);
        // echo $sql;

        $i=2;
        while ($myrow = db_fetch($result)) {

            $qry="SELECT SUM(a.pay_amount) AS EmpTotalSalary
                    FROM 0_kv_empl_salary_details as a
                    INNER JOIN 0_kv_empl_pay_elements as b ON b.id=a.pay_rule_id
                    WHERE a.emp_id='".$myrow['id']."' AND b.type='1'";

            $em_gross_salary=db_fetch(db_query($qry));
            $get_one_hour_salary=($em_gross_salary[0]/$number_of_days_in_month)/$working_hours;
            $overtime_extra_hour=$myrow['duration']-$working_hours;

            $calc_salary=$em_gross_salary[0]/$number_of_days_in_month/$working_hours*$config_reslt['payroll_overtime_rate'];
            $overtime_rate=round($calc_salary,3)*$overtime_extra_hour;


            $sheet->setCellValue('A' . $i, date('d-m-Y',strtotime($myrow['a_date'])));
            $sheet->setCellValue('B' . $i, $myrow['empl_id']);
            $sheet->setCellValue('C' . $i, $myrow['Emp_name']);
            $sheet->setCellValue('D' . $i, $overtime_extra_hour);
            $sheet->setCellValue('E' . $i, $overtime_rate);


            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');


    }




    public function reset_payroll()
    {
        /*$reset_ids=$_POST['reset_ids'];
        foreach($reset_ids as $ids)
        {*/


        /*-------------------------VOID GL ENTRY--------------*/
        $pay_out_data="select * from 0_kv_empl_payroll_details where payslip_id='".$_POST['payroll_id']."'
                           and payroll_porcessed='1'";
        $pay_res=db_query($pay_out_data);
        // echo $pay_out_data.'----';
        $msg='';
        while ($myrow_void = db_fetch($pay_res)) {
            /*$memo='Voiding Transaction from HRMS aginst the payroll id : '.$_POST['payroll_id'].' and employee id'.$myrow_void['empl_id'];
            void_transaction(0, $myrow_void['gl_trans_id'],
            $myrow_void['trans_date'], $memo);*/

            if($myrow_void['gl_trans_id']=='' || $myrow_void['gl_trans_id']=='0')
            {
                $reset_payroll="Update 0_kv_empl_payroll_details set payroll_porcessed='0',tot_salary_payable='0' where id='".$myrow_void['id']."'
                            ";
                db_query($reset_payroll);

                $sql_dele="DELETE FROM 0_kv_empl_temp_gl where payroll_ref_id='".$_POST['payroll_id']."'";
                db_query($sql_dele);
                $msg='1';
            }
            else
            {
                $msg='';
            }

        }


        return AxisPro::SendResponse(['resp'=>"OK",'msg'=>$msg]);
    }

    public function replace_comma($param)
    {
        $value=str_replace(",","",$param);
        return $value;
    }


    public function create_loan_entry()
    {
        $msg='';
        $status='';

        $start_date=date2sql($_POST['effect_date']);
        $loan_amount=str_replace(",","",$_POST['loan_amount']);

        /****************************EMP Primary Key******************/
        /*$sql_emp_id="select id from 0_kv_empl_info where empl_id='".$_POST['Empl_id']."'";
        $data_emp_pk=db_fetch(db_query($sql_emp_id));
        $empl_id=$data_emp_pk['id'];*/
        $empl_id=$_POST['Empl_id'];
        /*******************************END***************************/


        $SQLQRY_loan="SELECT b.account_code,b.id
                            from 0_kv_empl_pay_elements AS b
                            where b.is_loan_account='1'";

        $commsion_data=db_fetch(db_query($SQLQRY_loan));

        $sql_sub_loan_check="select sub_account_code from 0_kv_empl_account_mapping
                                    where emp_id='".$_POST['Empl_id']."' and element_id='".$commsion_data['id']."'";
        $sub_led_data=db_fetch(db_query($sql_sub_loan_check));



        if($sub_led_data['sub_account_code']=='' || $sub_led_data['sub_account_code']=='0')
        {
            $msg="There is no Loan Account specified for the employee. Kindly update the Add/Manage Employee section.";
            $status='error';
        }
        else
        {
            $sql_chk="select id from 0_kv_empl_loan where empl_id='".$_POST['Empl_id']."' and start_date='".$start_date."'
                  And loan_amount='".$loan_amount."'";
            $row=db_fetch_row(db_query($sql_chk));

            if($row['id']=='')
            {
                $monthly_amnt=$_POST['loan_amount']/$_POST['install_count'];

                $sql="Insert into 0_kv_empl_loan (dept_id,empl_id,date,loan_date,start_date,loan_amount,periods,monthly_pay,status,memo,loan_from_account,loan_type_id)
              values ('".$_POST['dept_id']."','".$_POST['Empl_id']."','".date('Y-m-d')."','".date('Y-m-d')."','".$start_date."'
              ,'".$loan_amount."','".$_POST['install_count']."','".round($monthly_amnt,2)."','1','".$_POST['memo']."','".$_POST['LoanAccount']."','".$_POST['loan_type_id']."')";

                if(db_query($sql))
                {
                    $loan_pk_id=db_insert_id();
                    /*-------------------------LOAN GL PASSING--------------*/

                    $Refs = new references();
                    $ref = $Refs->get_next(ST_JOURNAL, null, Today());
                    $trans_type = 0;
                    $total_gl = 0;
                    $trans_id = get_next_trans_no(0);

                    if($_POST['LoanAccount']!='')
                    {
                        $sql_emp_data="select empl_firstname from 0_kv_empl_info where id='".$empl_id."'";
                        $emp_per_data=db_fetch(db_query($sql_emp_data));

                        $total_gl= add_gl_trans($trans_type, $trans_id, Today(),$_POST['LoanAccount'], 0, 0,
                            'Loan Given for Emp :'.$emp_per_data['empl_firstname'],$loan_amount, 'AED', "", 0, "", 0);
                    }



                    $SQLQRY_loan_chk="SELECT b.account_code,b.id
                                            from 0_kv_empl_pay_elements AS b
                                            where b.is_loan_account='1'";

                    $loan_base_acc=db_fetch(db_query($SQLQRY_loan_chk));


                    if($loan_base_acc['account_code']!='')
                    {

                        $sql_qery_loan_sub="select sub_account_code from 0_kv_empl_account_mapping
                                        where emp_id='".$_POST['Empl_id']."' and element_id='".$loan_base_acc['id']."'";
                        $sub_led_data=db_fetch(db_query($sql_qery_loan_sub));

                        $total_gl = add_gl_trans($trans_type, $trans_id, Today(),$loan_base_acc['account_code'], 0, 0,
                            'Loan Processed For Emp :'.$emp_per_data['empl_firstname'],'-'.$loan_amount, 'AED', "", 0, "", 0);
                        if($sub_led_data['sub_account_code']!='')
                        {
                            $gl_counter_loan = db_insert_id();
                            $sql = "UPDATE 0_gl_trans SET axispro_subledger_code ='".$sub_led_data['sub_account_code']."'
                                ,created_by='" . $_SESSION['wa_current_user']->user . "' WHERE counter = $gl_counter_loan";
                            db_query($sql);
                        }
                    }




                    $sql = "INSERT INTO ".TB_PREF."journal(`type`,`trans_no`, `amount`, `currency`, `rate`, `reference`, `source_ref`, `tran_date`,
                                     `event_date`, `doc_date`)
                                     VALUES("
                        .db_escape($trans_type).","
                        .db_escape($trans_id).","
                        .db_escape($loan_amount).",'AED',"
                        .db_escape(1).","
                        .db_escape($ref).",'',"
                        ."'".date('Y-m-d')."',"
                        ."'".date('Y-m-d')."','')";
                    db_query($sql);
                    /*----END-------*/
                    $memo = '';
                    $Refs->save($trans_type, $trans_id, $ref);



                    add_comments($trans_type, $trans_id, Today(), $memo);
                    add_audit_trail($trans_type, $trans_id, Today());


                    if($trans_id)
                    {
                        $trans_id_update="update 0_kv_empl_loan set trans_id='".$trans_id."',tran_date='".Today()."' where id='".$loan_pk_id."'";
                        db_query($trans_id_update);
                    }

                    /*-------------------------------END----------------------*/
                    $msg='Successfully Saved Data';
                    $status='success';
                }
                else
                {
                    $msg='Error occured while saving data';
                    $status='error';
                }


            }
            else
            {
                $msg='Loan entry already exists for the employee for the same date and same amount';
                $status='error';
            }
        }


        return AxisPro::SendResponse(['status'=>$status,'msg'=>$msg]);
    }



    public function list_loan_entries()
    {

        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $sql = "SELECT a.id,b.empl_id,CONCAT(b.empl_firstname,\" \",b.empl_lastname) AS Emp_name,a.start_date,a.loan_amount,
                a.monthly_pay,a.periods,a.dept_id,a.empl_id as emp_pk_id,a.loan_from_account,a.loan_type_id,a.trans_id,a.periods_paid
                FROM 0_kv_empl_loan AS a
                INNER JOIN 0_kv_empl_info AS b ON a.empl_id=b.id
                ORDER BY id asc LIMIT ".$start.",".$length." ";


        $result = db_query($sql);
        $data = [];
        $payslip_label='';
        $controls='';
        $attributes='';
        while ($myrow = db_fetch($result)) {

            $attributes='alt_id="'.$myrow['id'].'" alt_dept_id="'.$myrow['dept_id'].'" alt_emp_id="'.$myrow['emp_pk_id'].'" alt_loan_amount="'.$myrow['loan_amount'].'"
                         alt_install_cont="'.$myrow['periods'].'" alt_start_date="'.$myrow['start_date'].'" alt_from_acc="'.$myrow['loan_from_account'].'"
                         alt_installment_amount="'.$myrow['monthly_pay'].'" alt_loan_type_id="'.$myrow['loan_type_id'].'" alt_paid_peroid="'.$myrow['periods_paid'].'" ';

            if($myrow['periods_paid']>'0')
            {
                $controls='<label class=\'btn btn-sm btn-primary ClsBtnEdit\' '.$attributes.' ><i class=\'flaticon-edit\'></i></label>
                 <label class=\'btn btn-sm btn-primary ClsBtnEdit\' alt_id="'.$myrow['id'].'"><i class=\'flaticon-delete\'></i></label>';
            }
            else
            {
                $controls='<label class=\'btn btn-sm btn-primary ClsBtnEdit\' '.$attributes.' data-toggle="modal" data-target="#myModal"><i class=\'flaticon-edit\'></i></label>
                 <label class=\'btn btn-sm btn-primary ClsBtnEdit\' alt_id="'.$myrow['id'].'"><i class=\'flaticon-delete\'></i></label>';
            }



            $data[] = array(
                $myrow['empl_id'],
                $myrow['Emp_name'],
                date('d/m/Y',strtotime($myrow['start_date'])),
                $myrow['loan_amount'],
                $myrow['periods'],
                $myrow['periods_paid'],
                $myrow['monthly_pay'],
                '<a target="_blank" href="ERP/gl/view/gl_trans_view.php?type_id=0&amp;trans_no='.$myrow['trans_id'].'" onclick="javascript:openWindow(this.href,this.target); return false;" accesskey="V"><u>V</u>iew GL</a>',
                $controls,

            );
        }

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($result),
            "recordsFiltered" => db_num_rows($result),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);

    }


    public function process_payment()
    {
        $payroll_id=$_POST['payroll_id'];
        $emp_id=$_POST['emp_id'];
        $acount=$_POST['account'];
        $salary=$_POST['salary'];
        $from=$_POST['from'];
        $msg='';
        $payout_insert_id='';

        $qry="select id from 0_kv_empl_payout_details where payslip_ref_id='".$payroll_id."' and empl_id='".$emp_id."'";
        $check_exists=db_fetch(db_query($qry));
        if($check_exists['id']=='' || $check_exists['id']=='0')
        {
            $save_payout="Insert into 0_kv_empl_payout_details (payslip_ref_id,salary_released,empl_id,from_account,created_by,created_on)
                        values ('".$payroll_id."','".$salary."','".$emp_id."','".$acount."','".$_SESSION['wa_current_user']->user."',
                     '".date('Y-m-d h:i:s')."')";
        }
        else
        {
            $save_payout="Update 0_kv_empl_payout_details set salary_released=`salary_released`+'".$salary."' 
                         where id='".$check_exists['id']."'";
        }

        if(db_query($save_payout))
        {
            if($check_exists['id']=='')
            {
                $payout_insert_id=db_insert_id();
            }
            else
            {
                $payout_insert_id=$check_exists['id'];
            }

            $update="update 0_kv_empl_payroll_details set processed_salary=`processed_salary`+'".$salary."' Where payslip_id='".$payroll_id."'
           and empl_id='".$emp_id."'";
            if(db_query($update))

            { /*


              $Refs = new references();
               $ref = $Refs->get_next(ST_JOURNAL, null, Today());
               $trans_type = 0;
               $total_gl = 0;
               $trans_id = get_next_trans_no(0);*/

                $sql_emp_data="select empl_firstname from 0_kv_empl_info where id='".$emp_id."'";
                $emp_per_data=db_fetch(db_query($sql_emp_data));


                //$sql_personal_base="select * from 0_kv_empl_account_mapping where emp_id='".$emp_id."'
                //and payable_empl_base_acc!=''";
                //$per_acc_data_res_base=db_fetch(db_query($sql_personal_base));





                /*if($net_payabale_acc['value']!='')
                        {


                             $SQLQRY="select payable_empl_subledger
                                         from 0_kv_empl_job where empl_id='".$p_salary['alt_emp_id']."'"    ;

                              $sub_payable_data=db_fetch(db_query($SQLQRY));

                                $total_gl += add_gl_trans($trans_type, $trans_id, Today(),$net_payabale_acc['value'], 0, 0,
                                'Employee net salary payable','-'.$p_salary['tot_salary_payble'], 'AED', "", 0, "", 0);
                            if($sub_payable_data[0]!='')
                            {
                                $gl_counter_pay= db_insert_id();
                                $sql = "UPDATE 0_gl_trans SET axispro_subledger_code ='".$sub_payable_data[0]."',created_by='" . $_SESSION['wa_current_user']->user . "' WHERE counter = $gl_counter_pay";


                               db_query($sql);
                            }
                        }*/

                $Qry_payable="SELECT `value` FROM 0_sys_prefs 
                                         WHERE `name`='payroll_payable_act'";
                $net_payabale_acc=db_fetch(db_query($Qry_payable));


                if($net_payabale_acc['value']!='') {


                    /* $SQLQRY="select payable_empl_subledger,esb_empl_subledger

                     $SQLQRY="select payable_empl_subledger,esb_empl_subledger
                                   from 0_kv_empl_job where empl_id='".$emp_id."'"    ;
                      $sub_payable_data=db_fetch(db_query($SQLQRY));

                     $total_gl = add_gl_trans($trans_type, $trans_id, Today(),$net_payabale_acc['value'],$sub_payable_data['esb_empl_subledger'], 0,
                         'Salary Given To :' . $emp_per_data['empl_firstname'], $salary, 'AED', "", 0, "", 0);

                     if($sub_payable_data['payable_empl_subledger']!='')
                     {
                         $gl_counter_base = db_insert_id();
                         $sql = "UPDATE 0_gl_trans SET axispro_subledger_code ='".$sub_payable_data['payable_empl_subledger']."',created_by='" . $_SESSION['wa_current_user']->user . "' WHERE counter = $gl_counter_base";
                         db_query($sql);
                     }*/

                }
                else
                {
                    /*$payroll_pref=array('payroll_payable_act');
                    for($i=0;$i<sizeof($payroll_pref);$i++)
                    {
                        $get_GL_sql="SELECT `value`,`name` FROM 0_sys_prefs
                                          WHERE `name`='".$payroll_pref[$i]."' ";
                        $gl_setings=db_fetch(db_query($get_GL_sql));

                        $total_gl= add_gl_trans($trans_type, $trans_id, Today(),$gl_setings[0], 0, 0,
                                'Salary Given To :'.$emp_per_data['empl_firstname'],'-'.$salary, 'AED', "", 0, "", 0);

                    }*/

                    //}

                }

                if($acount!='')
                {

                    /* $total_gl= add_gl_trans($trans_type, $trans_id, Today(),$acount, 0, 0,
                         'Salary Give from Account ','-'.$salary, 'AED', "", 0, "", 0);*/
                }

                /*$sql = "INSERT INTO ".TB_PREF."journal(`type`,`trans_no`, `amount`, `currency`, `rate`, `reference`, `source_ref`, `tran_date`,

                     $total_gl= add_gl_trans($trans_type, $trans_id, Today(),$acount, 0, 0,
                         'Salary Give from Account ','-'.$salary, 'AED', "", 0, "", 0);
                 }

                $sql = "INSERT INTO ".TB_PREF."journal(`type`,`trans_no`, `amount`, `currency`, `rate`, `reference`, `source_ref`, `tran_date`,
                    `event_date`, `doc_date`)
                                      VALUES("
                    .db_escape($trans_type).","
                    .db_escape($trans_id).","
                    .db_escape($salary).",'AED',"
                    .db_escape(1).","
                    .db_escape($ref).",'',"
                    ."'".date('Y-m-d')."',"
                    ."'".date('Y-m-d')."','')";
                db_query($sql);*/

                $memo = 'Salary Processed GL Entry For Employee :'.$emp_per_data['empl_firstname'];

                //$Refs->save($trans_type, $trans_id, $ref);



                //add_comments($trans_type, $trans_id, Today(), $memo);
                // add_audit_trail($trans_type, $trans_id, Today());

                //$Refs->save($trans_type, $trans_id, $ref);



                // add_comments($trans_type, $trans_id, Today(), $memo);
                // add_audit_trail($trans_type, $trans_id, Today());


                if($trans_id)
                {
                    $update_trans_id="Update 0_kv_empl_payout_details set trans_id=CONCAT(trans_id,',','".$trans_id."') 
                         where id='".$payout_insert_id."'";

                    // echo $update_trans_id;
                    //db_query($update_trans_id);
                }

                $msg='Data Saved Successfully';
            }
        }
        else
        {
            $msg='Some error occured while saving data';
        }

        return AxisPro::SendResponse(['msg'=>$msg]);

    }


    function get_to_subacc()
    {
        $ledger_id=$_POST['acc_id'];

        $sql="SELECT code,name
              FROM 0_sub_ledgers where ledger_id='".$ledger_id."' ";
        $result = db_query($sql);
        $return_result = [];
        $select='<select class="form-control"  id="ddl_to_sub" name="ddl_to_sub" style="width: 188px;"><option value="0">---Select Sub Ledger---</option>';
        while ($myrow = db_fetch($result)) {
            $select.='<option value="'.$myrow['code'].'">'.$myrow['name'].'</option>';
        }
        $select.='</select>';

        return AxisPro::SendResponse($select);
    }

    public function get_loan_types()
    {
        $sql="SELECT  * from 0_kv_empl_loan_types ";
        $result = db_query($sql);


        return $result;

    }

    public function update_loan_entry()
    {



        $sql="update 0_kv_empl_loan set dept_id='".$_POST['dept_id']."',empl_id='".$_POST['Empl_id']."',loan_date='".date('Y-m-d')."',
               loan_from_account='".$_POST['LoanAccount']."',start_date='".date('Y-m-d',strtotime($_POST['effect_date']))."',loan_amount='".$_POST['loan_amount']."',loan_type_id='".$_POST['loan_type_id']."',
               periods='".$_POST['install_count']."',monthly_pay='".$_POST['install_amount']."',memo='".$_POST['memo']."',status='1',updated_by='".$_SESSION['wa_current_user']->user."',updated_date='".date('Y-m-d H:i:s')."'
               where id='".$_POST['hdn_id']."'";

        if(db_query($sql))
        {
            /**************************GET EMP NAME*****************/
            $emp_name_sql="select CONCAT(empl_firstname,' ',empl_lastname) as EMPNAME from 0_kv_empl_info where id='".$_POST['Empl_id']."'";
            $emp_name=db_fetch_row(db_query($emp_name_sql));


            if($emp_name)
            {
                $get_sql="select trans_id,tran_date from 0_kv_empl_loan where id='".$_POST['hdn_id']."'";
                $loan_trans_data=db_fetch_row(db_query($get_sql));

                $memo='Voiding Loan Entry for employee'.$emp_name[0];
                void_transaction(0, $loan_trans_data['trans_id'],
                    $loan_trans_data['tran_date'], $memo);

                /*----------------------UPDATE LOAN GL-------------*/

                $Refs = new references();
                $ref = $Refs->get_next(ST_JOURNAL, null, Today());
                $trans_type = 0;
                $total_gl = 0;
                $trans_id = get_next_trans_no(0);
                $empl_id=$_POST['Empl_id'];

                if($_POST['LoanAccount']!='')
                {
                    $sql_emp_data="select empl_firstname from 0_kv_empl_info where id='".$empl_id."'";
                    $emp_per_data=db_fetch(db_query($sql_emp_data));

                    $total_gl= add_gl_trans($trans_type, $trans_id, Today(),$_POST['LoanAccount'], 0, 0,
                        'Loan Given for Emp :'.$emp_name[0],$_POST['loan_amount'], 'AED', "", 0, "", 0);
                }

                $sql_personal="select * from 0_kv_empl_account_mapping where emp_id='".$empl_id."'";
                $per_acc_data_res=db_fetch(db_query($sql_personal));

                if($per_acc_data_res['emp_loan_base_account']!='')
                {
                    $total_gl = add_gl_trans($trans_type, $trans_id, Today(),$per_acc_data_res['emp_loan_base_account'], 0, 0,
                        'Loan Processed For Emp :'.$emp_name[0],'-'.$_POST['loan_amount'], 'AED', "", 0, "", 0);
                    if($per_acc_data_res['emp_loan_subledger_account']!='')
                    {
                        $gl_counter_loan = db_insert_id();
                        $sql = "UPDATE 0_gl_trans SET axispro_subledger_code ='".$per_acc_data_res['emp_loan_subledger_account']."'
                                ,created_by='" . $_SESSION['wa_current_user']->user . "' WHERE counter = $gl_counter_loan";
                        db_query($sql);
                    }
                }




                $sql = "INSERT INTO ".TB_PREF."journal(`type`,`trans_no`, `amount`, `currency`, `rate`, `reference`, `source_ref`, `tran_date`,
                                     `event_date`, `doc_date`)
                                     VALUES("
                    .db_escape($trans_type).","
                    .db_escape($trans_id).","
                    .db_escape($_POST['loan_amount']).",'AED',"
                    .db_escape(1).","
                    .db_escape($ref).",'',"
                    ."'".date('Y-m-d')."',"
                    ."'".date('Y-m-d')."','')";
                db_query($sql);
                /*----END-------*/
                $memo = '';
                $Refs->save($trans_type, $trans_id, $ref);



                add_comments($trans_type, $trans_id, Today(), $memo);
                add_audit_trail($trans_type, $trans_id, Today());


                if($trans_id)
                {
                    $trans_id_update="update 0_kv_empl_loan set trans_id='".$trans_id."',tran_date='".Today()."' where id='".$_POST['hdn_id']."'";
                    db_query($trans_id_update);
                }



            }
            $msg='success';
            /******************************END***********************/
        }
        else
        {
            $msg='fail';
        }

        return AxisPro::SendResponse(['status'=>$msg]);

    }


    public function list_salary_reports()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $sql="SELECT p.payslip_id,SUM(a.tot_salary_payable) AS tot_payable,SUM(o.salary_released) AS salaryProcesed
                FROM 0_kv_empl_payroll_details AS a
                INNER JOIN 0_kv_empl_info AS b ON a.empl_id=b.id
                INNER JOIN 0_kv_empl_job AS c ON c.empl_id=b.id
                INNER JOIN 0_kv_empl_payroll_master AS p ON p.id=a.payslip_id
                left JOIN 0_kv_empl_payout_details AS o ON o.payslip_ref_id=a.payslip_id
                WHERE p.pay_year='".$_POST['year']."' AND p.pay_month='".$_POST['month']."' AND dept_id='".$_POST['dept_id']."'
                AND a.payroll_porcessed='1' LIMIT ".$start.",".$length." ";
        //echo $sql;
        $res=db_query($sql);
        $data = [];


        $payable_salary='';
        $links='';
        $gl_links='';
        while($row_data=db_fetch($res))
        {

            $data[] = array(
                $row_data['payslip_id'],
                $row_data['tot_payable'],
                $row_data['salaryProcesed'],
            );
        }

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($res),
            "recordsFiltered" => db_num_rows($res),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);
    }


    public function get_employee_info()
    {
        $sql="SELECT a.empl_id,CONCAT(a.empl_firstname,' ',a.empl_lastname) AS EmpName,d.description,a.email,a.mobile_phone,
                a.empl_firstname,a.empl_lastname,a.date_of_birth,b.joining,b.mod_of_pay,b.bank_name,b.acc_no,b.branch_detail,b.ifsc,b.iban
                ,c.local_name,a.gender
                FROM 0_kv_empl_info AS a
                LEFT JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                INNER JOIN 0_kv_empl_designation AS d ON d.id=b.desig
                INNER JOIN 0_kv_empl_country AS c ON c.id=a.country
                left join 0_users as e ON e.employee_id=a.id
                WHERE e.id='".$_SESSION['wa_current_user']->user."'";


        //echo $sql;

        $result=db_query($sql);
        $res=[];
        while ($myrow = db_fetch($result)) {
            $res[] = $myrow;
        }

        return $res;

    }

    public function leave_details()
    {
        $get_empl_id="select employee_id from 0_users where id='".$_SESSION['wa_current_user']->user."'";
        $emp_res= db_query($get_empl_id, "Can't get your allowed user details");
        $empl_id_data= db_fetch($emp_res);

        if(sizeof($empl_id_data)>1)
        {
            $sql="SELECT a.id,b.joining,a.empl_pic
                FROM 0_kv_empl_info AS a
                INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                left join 0_users as c ON c.employee_id=a.id
                where c.id='".$_SESSION['wa_current_user']->user."'";
            //echo $sql;
            $result_d = db_query($sql, "Can't get your allowed user details");
            $rowData= db_fetch($result_d);
            $eligible_leave='0';
            if($rowData[0]!='')
            {

                $date_of_join=$rowData[1];
                $oneday_eligible_leave=30/365;

                $now = time();
                $your_date = strtotime($date_of_join);
                $datediff = $now - $your_date;
                $no_of_wrk_days=round($datediff / (60 * 60 * 24));

                if($no_of_wrk_days)
                {
                    $eligible_leave=$no_of_wrk_days*$oneday_eligible_leave;
                }

            }

            $sql_tot="Select count(id) as tot from 0_kv_empl_attendance where code NOT IN ('p','wl')
            and empl_id='".$empl_id_data['empl_id']."'";
            $emp_tot_leave= db_query($sql_tot, "Can't get your allowed user details");
            $tot_leave_data= db_fetch($emp_tot_leave);

            /*-----------------GET EMP IMAGE----------------*/
            $img=$rowData[2];
            /*-------------------------END------------------*/


            return array('Elg_leave'=>round($eligible_leave),'Tot_leave'=>$tot_leave_data['tot'],'img_pth'=>$img);
        }
        return array('Elg_leave'=>0,'Tot_leave'=>0,'img_pth'=>'');


    }

    public function check_access_for_dashbord()
    {
        $sql_head="SELECT a.head_of_dept,a.department  
                FROM 0_kv_empl_job AS a
                INNER JOIN  0_kv_empl_info AS b ON b.id=a.empl_id
                left join 0_users as c ON c.employee_id=b.id
                WHERE c.id='".$_SESSION['wa_current_user']->user."'";
        $dept_head_data=db_fetch(db_query($sql_head));
        $hrm_access='';
        if($dept_head_data[0]=='1')
        {
            $hrm_access='1';
        }
        else if(in_array($_SESSION['wa_current_user']->access,[2,17,16,24,25,18]))
        {
            $hrm_access='2';
        }
        else
        {
            $hrm_access='3';
        }

        return $hrm_access;
    }

    public function get_basic_employee_details_for_header()
    {
        $sql="SELECT d.description as department,a.description as designation,e.empl_id
                ,CONCAT(e.empl_id,' - ',e.empl_firstname,' ',e.empl_lastname) as employee_name
                ,CONCAT(p.empl_id,' - ',p.empl_firstname,' ',p.empl_lastname) AS linemanager,b.joining as joindate
                ,CONCAT(c.empl_id,' - ',c.empl_firstname,' ',c.empl_lastname) as head_of_dept
                FROM 0_kv_empl_info as e
                INNER JOIN 0_kv_empl_job as b ON e.id=b.empl_id
                INNER JOIN 0_kv_empl_departments AS d ON d.id=b.department
                INNER JOIN 0_kv_empl_designation AS a ON a.id=b.desig
                LEFT JOIN 0_kv_empl_info AS p ON p.id=e.report_to
                left join 0_kv_empl_info as c On c.id=d.head_of_empl_id
                left join 0_users as g ON g.employee_id=e.id
                WHERE g.id='".$_SESSION['wa_current_user']->user."'
              ";

        $data=db_fetch(db_query($sql));

        return $data;

    }


    public function display_hr_admin_menu()
    {
        if($_SESSION['wa_current_user']->access=='2')
        {
            return true;
        }
        else if($_SESSION['wa_current_user']->access!='2')
        {
            $sql_head="SELECT a.head_of_dept
                FROM 0_kv_empl_job AS a
                INNER JOIN  0_users AS b ON b.employee_id=a.empl_id
                WHERE b.id='".$_SESSION['wa_current_user']->user."'";
            $dept_head_data=db_fetch(db_query($sql_head));

            if($dept_head_data[0]=='1')
            {
                return true;
            }

            if($_SESSION['wa_current_user']->access=='9')
            {
                return true;
            }
        }
        else
        {
            return false;
        }


    }



    public function sendMailToFinanceDept($pay_roll_id)
    {
        /*-------------------SEND EMAIL------------*/
        $path_to_root = "..";
        include_once($path_to_root . "/API/HRM_Mail.php");
        $hrm_mail=new HRM_Mail();


        $pay_sql="SELECT payslip_id from 0_kv_empl_payroll_master where id='".$pay_roll_id."'";
        $paysli_ref_id=db_fetch(db_query($pay_sql));



        $ss="SELECT a.email 
                    FROM 0_kv_empl_info AS a 
                    INNER JOIN 0_users AS b ON a.user_id=b.id
                    WHERE b.role_id='9'";
        $email_data=db_query($ss);
        $to_email='';
        while($emil_ids=db_fetch($email_data))
        {
            $to_email.=$emil_ids[0].',';
        }

        $to_emails=rtrim($to_email,',');




        $to=$to_emails;
        $sub='New Payroll Generated';
        $content= "<div>
                                   <label>Dear Finance Dept,</label></br>
                                   <div style='height: 39px;
                                    margin-top: 33px;'>There is one payroll generted against payroll ID :.'".$paysli_ref_id[0]."' </div>
                                  
                                    <div style='margin-top:2%;'>
                                    <div>Thanks,</div>
                                    <div>HRMS</div>
                                    </div>
                                    
                  </div>";

        $hrm_mail->send_mail($to,$sub,$content);



        /*----------------------END----------------*/
    }

    public function get_invoice_details()
    {
        $invoice_ref_number=$_POST['ref_id'];

        /************************GET INVOICE DETAILS*******************/
        $sql="SELECT (dt.ov_amount+dt.ov_gst)as invoice_total,dt.display_customer,dt.tran_date
                   ,us.real_name,dt.reference,dt_detail.created_by,dt.trans_no,dt.mistaken_invoice
                   FROM 0_debtor_trans_details dt_detail
                   left join 0_debtor_trans dt on dt.trans_no=dt_detail.debtor_trans_no and dt.type=10
                   LEFT JOIN 0_users us ON us.id=dt_detail.created_by
                   where dt_detail.debtor_trans_type=10 
                   and dt_detail.quantity <> 0 and dt.ov_amount <> 0
                   AND  dt.reference LIKE '%".trim($invoice_ref_number)."%'
                   GROUP BY dt.reference ORDER BY dt.tran_date DESC";
        $search_res=db_query($sql);


        /******************get user_department & loggined user dept**********************/
        /*$sql_dept="select b.department
                    from 0_kv_empl_job as b
                    inner join o_kv_empl_info as a ON a.id=b.empl_id
                    where a.user_id='".$invoice_data['created_by']."'";
         $user_dept=db_fetch(db_query($sql_dept));*/

        /* $sql_logged_user="select b.department
                    from 0_kv_empl_job as b
                    inner join o_kv_empl_info as a ON a.id=b.empl_id
                    where a.user_id='".$_SESSION['wa_current_user']->user."'";
        $dept_of_logged=db_fetch(db_query($sql_logged_user));*/

        /* if($user_dept['department']!=$dept_of_logged['department'])
         {
             $msg=['status'=>'OK'];
             return AxisPro::SendResponse($msg);
         }
         else
         {
           */
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $data=[];
        $i=0;
        $controls='';
        while($invoice_data=db_fetch($search_res))
        {
            $sql_dept="select b.department,a.id
                        from 0_kv_empl_job as b
                        inner join 0_kv_empl_info as a ON a.id=b.empl_id
                        where a.user_id='".$invoice_data['created_by']."'";
            $empl_data=db_fetch(db_query($sql_dept));


            $attribuites=" alt_trans_no='".$invoice_data['trans_no']."' alt_ref_no='".$invoice_data['reference']."'
                                   alt_empl_id='".$empl_data['id']."' alt_index='".$i."'  ";

            if($invoice_data['mistaken_invoice']=='1')
            {
                $controls='<label style="color:green;background-color: yellow;">Added</label>';
            }
            else
            {
                $controls='<input type="submit" class="btn_submit btn btn-success" value="Add invoice" '.$attribuites.' /> 
                         <input type="hidden" class="hdn_invoice_amnt" value="'.number_format($invoice_data['invoice_total'],2).'"/>';
            }

            $data[] = array(
                $invoice_data['reference'],
                $invoice_data['display_customer'],
                $invoice_data['real_name'],
                $invoice_data['tran_date'],
                '<input type="text" id="invoice_amnt_'.$i.'" value="'.number_format($invoice_data['invoice_total'],2).'" />',
                $controls
            );

            $i++;
        }

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($search_res),
            "recordsFiltered" => db_num_rows($search_res),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);
        //  }

        /*************************END*******************************/
    }

    public function submit_invoice_ded_entry()
    {
        $ref_no=$_POST['ref_no'];
        $empl_id=$_POST['empl_id'];
        $trans_no=$_POST['trans_no'];
        $ded_amount=$_POST['ded_amount'];


        $sql="INSERT INTO 0_kv_empl_invoice_mistake_entry (empl_id,invoice_ref_no,invoice_trans_no,ded_amount,created_by,created_on)
              VALUES ('".$empl_id."','".$ref_no."','".$trans_no."','".$ded_amount."','".$_SESSION['wa_current_user']->user."',
              '".date('Y-m-d H:i:s')."')";
        if(db_query($sql))
        {
            $msg=['status'=>'OK','msg'=>'Data Saved successfully'];
            $up_sql="UPDATE 0_debtor_trans set mistaken_invoice='1' where trans_no='".$trans_no."' and `type`='10'";
            db_query($up_sql);
        }
        else
        {
            $msg=['status'=>'ERROR','msg'=>'Error Occures while saving the data'];
        }

        return AxisPro::SendResponse($msg);
    }


    public function Save_holidays()
    {
        $year=$_POST['year'];
        $date=$_POST['date'];
        $name=$_POST['holi_day_name'];
        $date_format=date('Y-m-d',strtotime($date));
        $edit_id=$_POST['edit_id'];

        if(empty($edit_id))
        {
            $sql="INSERT INTO 0_kv_empl_public_holidays (year, `date`,holiday,status)
              VALUES ('".$year."','".$date_format."','".$name."','1')";
        }
        else
        {
            $sql="Update 0_kv_empl_public_holidays 
             set `year`='".$year."',`date`='".$date."',holiday='".$name."'
             where id='".$edit_id."' ";
        }


        if(db_query($sql))
        {
            $msg=['status'=>'OK','msg'=>'Saved Successfully'];
        }
        else
        {
            $msg=['status'=>'ERROR','msg'=>'Error Occures while saving the data'];
        }

        return AxisPro::SendResponse($msg);
    }


    public function get_saved_holidays()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);


        $sql="Select year,date,holiday,id from 0_kv_empl_public_holidays where status='1'";
        $res=db_query($sql);
        $i=1;
        $data=[];
        $controls='';
        while($data_set=db_fetch($res))
        {

            $controls='<label alt='.$data_set['id'].'  
                     alt_date='.date('m-d-Y',strtotime($data_set['date'])).'
                     alt_holiday_name='.$data_set['holiday'].' alt_year='.$data_set['year'].' class="btn btn-sm btn-primary ClsBtnEdit"><i class="flaticon-edit"></i></label> &nbsp;&nbsp;

                    <label alt='.$data_set['id'].' class="btn btn-sm btn-primary ClsBtnRemove"><i class="flaticon-delete"></i></label>';



            $data[] = array(
                $i,
                $data_set['year'],
                date('m-d-Y',strtotime($data_set['date'])),
                $data_set['holiday'],
                $controls

            );

            $i++;
        }

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($res),
            "recordsFiltered" => db_num_rows($res),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);



    }

    public function delete_holiday()
    {
        $sql="Update 0_kv_empl_public_holidays 
             set `status`='0' where id='".$_POST['edit_id']."' ";

        if(db_query($sql))
        {
            $msg=['status'=>'OK','msg'=>'Saved Successfully'];
        }
        else
        {
            $msg=['status'=>'ERROR','msg'=>'Error Occures while saving the data'];
        }

        return AxisPro::SendResponse($msg);
    }




    public function list_empl_holidaywrked()
    {
        $from_date='';
        $to_date='';
        $month=$_POST['month'];
        $year=$_POST['year'];
        $days_in_mnth=cal_days_in_month(CAL_GREGORIAN, $month, $year);
        /*-------------------------------------GET CUT OFF DATE--------------------------*/
        $sql_cut="SELECT `value` as cutdate FROM 0_sys_prefs WHERE `name`='payroll_cutoff_date'";
        $cutof_date=db_fetch(db_query($sql_cut));
        if($cutof_date[0]=='' || $cutof_date[0]=='null')
        {
            $cutoff_day='0';
        }
        else
        {
            $cutoff_day=$cutof_date[0];
        }

        if($cutoff_day=='' || $cutoff_day=='0')
        {
            $from_date=$year.'-'.$month.'-'.'01';
            $to_date=$year.'-'.$month.'-'.$days_in_mnth;
        }
        else
        {
            $start_month=$month;
            $monthNum = $start_month;
            $cutoff_month=Date('n', strtotime(date("F", mktime(0, 0, 0, $monthNum, 10)) . " last month"));

            $from_date=$year.'-'.$cutoff_month.'-'.$cutoff_day;

            if($cutoff_day=='1')
            {
                $cutoff_day=cal_days_in_month(CAL_GREGORIAN, $cutoff_month, $year);
                $to_date=$year.'-'.$cutoff_month.'-'.$cutoff_day;
            }
            else
            {
                $cutoff_day=$cutoff_day-1;
                $to_date=$year.'-'.$month.'-'.$cutoff_day;
            }
        }
        /*-------------------------------------------END----------------------------------*/

        $draw  = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length= intval($_POST["length"]);


        $sql=" SELECT a.`date`,a.holiday,CONCAT(c.empl_id,' - ',c.empl_firstname,' ',c.empl_lastname) AS Empname,c.id as empl_id,a.id as holidy_pk_id  FROM 
                 0_kv_empl_attendance AS b
                 INNER JOIN 0_kv_empl_public_holidays AS a ON a.`date`=b.a_date
                 INNER JOIN 0_kv_empl_info AS c ON c.empl_id=b.empl_id
                 WHERE a.`status`='1' ";

        if(!empty($_POST['emp_id']))
        {
            $sql.=" AND b.empl_id='".$_POST['emp_id']."' ";
        }
        $sql.=" AND b.a_date>='".$from_date."' AND b.a_date<='".$to_date."' ";
        //echo $sql;

        $res=db_query($sql);
        $i=1;
        $data=[];
        $controls='';
        $label='';
        $selected='';
        while($data_set=db_fetch($res))
        {

            $exists="select id,pay_option 
                             from 0_kv_empl_holiday_approved where holiday_id='".$data_set['holidy_pk_id']."'
                             and empl_id='".$data_set['empl_id']."' ";

            $exist_data=db_fetch(db_query($exists));

            if($exist_data['id']=='')
            {
                $label='';
            }
            else
            {
                $label='<label style="color:green;background-color:yellow;font-weight:bold;">Approved</label>';
            }




            $controls='<select id="ddl_payOption_'.$i.'">
                            <option value="0">--Select--</option>';
            if($exist_data['pay_option']=='1')
            {
                $controls.='<option value="1" selected>50% from the GROSS SALARY + a day off</option>
                         <option value="2" >150% of the Basic SALARY</option>';
            }
            else if($exist_data['pay_option']=='2')
            {
                $controls.='<option value="1" >50% from the GROSS SALARY + a day off</option>
                         <option value="2" selected>150% of the Basic SALARY</option>';
            }
            else
            {
                $controls.='<option value="1" >50% from the GROSS SALARY + a day off</option>
                         <option value="2" >150% of the Basic SALARY</option>';
            }




            $controls.='</select>';




            $data[] = array(
                '<input type="checkbox" name="chk_select" class="chk_select" alt_index="'.$i.'" 
                         alt_emp_id="'.$data_set['empl_id'].'" alt_holi_pk_id="'.$data_set['holidy_pk_id'].'" 
                         alt_date="'.$data_set['date'].'" alt_un_assign_id="'.$exist_data['id'].'" />',
                $data_set['date'],
                $data_set['holiday'],
                $data_set['Empname'],
                $controls,
                $label

            );

            $i++;
        }


        $tot=" SELECT * FROM 
                 0_kv_empl_attendance AS b
                 INNER JOIN 0_kv_empl_public_holidays AS a ON a.`date`=b.a_date
                 INNER JOIN 0_kv_empl_info AS c ON c.empl_id=b.empl_id
                 WHERE a.`status`='1' ";

        if(!empty($_POST['emp_id']))
        {
            $sql.=" AND b.empl_id='".$_POST['emp_id']."' ";
        }
        $tot_res=db_query($tot);

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($tot_res),
            "recordsFiltered" => db_num_rows($tot_res),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);
    }


    public function save_apporved_holidays_for_employee()
    {
        $empids=$_POST['empids'];
        $date=$_POST['date'];
        $holiday_ids=$_POST['holiday_id'];
        $flag=$_POST['flag'];
        $remove_id=$_POST['remove_id'];
        $pay_option=$_POST['pay_option'];

        for($i=0;$i<count($empids);$i++)
        {

            if($flag=='1')
            {
                $sql_check="SELECT id FROM 0_kv_empl_holiday_approved WHERE emp_id='".$empids[$i]."' 
                            AND date='".$shiftdate[$i]."' ";
                $exist_cnt=db_fetch(db_query($sql_check));
                if($exist_cnt[0]=='')
                {
                    $sql="INSERT into 0_kv_empl_holiday_approved (holiday_id,empl_id,`date`,pay_option,created_on,created_by)
                      values ('".$holiday_ids[$i]."','".$empids[$i]."','".$date[$i]."'
                      ,'".$pay_option[$i]."','".date('Y-m-d h:i:s')."','".$_SESSION['wa_current_user']->user."')";
                    if(db_query($sql))
                    {
                        if($pay_option[$i]=='1')
                        {
                            $holiday_approve_pkid=db_insert_id($sql);
                            $insert_combo_off="Insert into 0_kv_empl_earned_combo_off 
                                         (empl_id,holiday_id,created_by) values ('".$empids[$i]."','".$holiday_approve_pkid."','".$_SESSION['wa_current_user']->user."')";
                            db_query($insert_combo_off);
                        }

                    }




                }

            }

        }

        if($flag=='2')
        {
            $pk_ids='';
            for($k=0;$k<count($remove_id);$k++)
            {
                $pk_ids.=$remove_id[$k].',';
            }
            $d_sql="Delete from 0_kv_empl_holiday_approved where id in (".rtrim($pk_ids, ',').")";
            if(db_query($d_sql))
            {
                $rem_comoff="Delete from 0_kv_empl_earned_combo_off where holiday_id in (".rtrim($pk_ids, ',').") ";
                //echo $rem_comoff;
                db_query($rem_comoff);
            }

        }

        $msg=['status' => 'OK', 'msg' =>"Successfully Updated "];
        return AxisPro::SendResponse($msg);
    }




    public function get_missed_punchin_pucnhout()
    {
        $sql="SELECT * FROM 0_kv_empl_attendance WHERE duration='0' AND a_date='2021-01-17' limit 2";
        $result=db_query($sql);

        return $result;
    }

    public function check_birthday(){
        $return_data = ['status' =>false];
        $sql = "SELECT emp.date_of_birth FROM 0_kv_empl_info emp
        LEFT JOIN 0_users users ON  emp.id = users.employee_id
        WHERE users.id = '".$_SESSION["wa_current_user"]->user."' AND emp.date_of_birth + INTERVAL (YEAR(CURDATE()) - YEAR(emp.date_of_birth)) YEAR = CURDATE() ";
        $result=db_query($sql);
        if(db_num_rows($result)>0){
            $return_data = ['status' =>true, 'sql'=>$sql];
        }
        echo json_encode($return_data);
    }

    public function getDatesFromRange($start, $end, $format = 'Y-m-d') {

        // Declare an empty array
        $array = array();
        // Variable that store the date interval
        // of period 1 day
        $interval = new DateInterval('P1D');
        $realEnd = new DateTime($end);
        $realEnd->add($interval);
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
        // Use loop to store date into array
        foreach($period as $date) {
            $array[] = $date->format($format);
        }
        // Return the array elements
        return $array;
    }



    public function getShiftOff_EMployee($shift_date,$emplid,$employee_pk_id)
    {
        $sql_absence=" SELECT shift_id FROM 0_kv_empl_shiftdetails WHERE s_date NOT IN (SELECT p.a_date
                             FROM 0_kv_empl_attendance AS p
                             WHERE p.empl_id='".$emplid."'
                             AND p.a_date='".$shift_date."'  
                             ) AND s_date='".$shift_date."' AND empl_id='".$employee_pk_id."' ";



        $res = db_query($sql_absence, "Can't get your allowed user details");

        $shift_date=db_fetch($res);
        return $shift_date['shift_id'];
    }


    private function get_emp_leave_records($params){

        $where_condition = $sqlTot = $sqlRec = "";


        /*if ($params['year']) {
            $where_condition .= " AND leave_appl.year = " . db_escape($params['year']) . " ";
        }else{
            $where_condition .= " AND leave_appl.year = " . db_escape(date('Y')) . " ";
        }*/
        if ($params['dept_id']) {
            $where_condition .= " AND emp_job.department = " . db_escape($params['dept_id']) . " ";
        }
        if ($params['emp_id']) {
            $where_condition .= " AND emp.id = " . db_escape($params['emp_id']) . " ";
        }
        /*if ($params['leave_type']) {
            $where_condition .= " AND leave_appl.leave_type = " . db_escape($params['leave_type']) . " ";
        }*/

        /*$sql_query="SELECT 
    emp.empl_id AS emp_id,
    d.description AS dept, 
    emp.emp_code, 
    emp.annual_leave_balance,
    emp.empl_firstname, 
    emp.empl_lastname,
    emp_job.joining,
    DATE(NOW()) AS today,
    leave_appl.year,
    FLOOR(DATEDIFF(NOW(),emp_job.joining)/(365)) AS years_diff,
    IFNULL(leave_types.description, '-') AS leave_type,
    IFNULL(sum(DATEDIFF(leave_appl.t_date,leave_appl.date))+1, 0) AS leave_days,
    CASE 
             WHEN leave_appl.leave_type = '1' THEN (emp.annual_leave_balance - IFNULL((sum(DATEDIFF(leave_appl.t_date,leave_appl.date))+1), 0))
             WHEN leave_appl.leave_type = '3' THEN '-'
             WHEN leave_appl.leave_type = '5' THEN (45 - IFNULL((sum(DATEDIFF(leave_appl.t_date,leave_appl.date))+1), 0))
             WHEN leave_appl.leave_type = '6' THEN '-'
             WHEN leave_appl.leave_type = '7' THEN '-'
             WHEN leave_appl.leave_type = '8' THEN (3 - IFNULL((sum(DATEDIFF(leave_appl.t_date,leave_appl.date))+1), 0))
             WHEN leave_appl.leave_type = '9' THEN '-'
             WHEN leave_appl.leave_type = '10' THEN '-'
             ELSE '-'
             END AS leave_balance,leave_appl.leave_type as leave_type_id,emp.id as emp_pk_id
             FROM 0_kv_empl_leave_applied AS leave_appl
             LEFT JOIN 0_kv_empl_info AS emp ON emp.id = leave_appl.empl_id
             LEFT JOIN 0_kv_empl_job AS emp_job ON emp.id = emp_job.empl_id
             LEFT JOIN 0_kv_empl_departments AS d ON d.id = emp_job.department
             LEFT JOIN 0_kv_empl_leave_types AS leave_types ON leave_types.id = leave_appl.leave_type
             WHERE leave_appl.req_status='1' AND leave_appl.del_status='1' AND emp.id IS NOT NULL ";*/

        $sql_query="SELECT  emp.empl_id AS emp_id,
              d.description AS dept,emp.id as emp_pk_id,
       emp.empl_firstname, 
    emp.empl_lastname
             FROM 0_kv_empl_info AS emp
             LEFT JOIN 0_kv_empl_job AS emp_job ON emp.id = emp_job.empl_id
             LEFT JOIN 0_kv_empl_departments AS d ON d.id = emp_job.department
             WHERE  emp.id IS NOT NULL";

        $sqlRec .= $sql_query;
        if (isset($where_condition) && $where_condition != '') {
            //$sqlTot .= $where_condition;
            $sqlRec .= $where_condition;
        }

        //$sqlRec .= " GROUP BY emp.id,leave_appl.leave_type,leave_appl.year ";

        if(isset($params['start']) && isset($params['length'])){
            $sqlRec .=  " LIMIT ". $params['start'] . " ," . $params['length'];
        }


        $sqlTot ="SELECT emp.id FROM 0_kv_empl_info AS emp
             LEFT JOIN 0_kv_empl_job AS emp_job ON emp.id = emp_job.empl_id
             LEFT JOIN 0_kv_empl_departments AS d ON d.id = emp_job.department
             WHERE  emp.id IS NOT NULL ";
        if (isset($where_condition) && $where_condition != '') {
            $sqlTot .= $where_condition;
        }

        $queryTot = db_query($sqlTot);
        $totalRecords = db_num_rows($queryTot);
        $queryRecords = db_query($sqlRec, "Error to Get the Post details.");
        return [
            'data' => $queryRecords,
            'total_records' => $totalRecords,
            //'sql'=>$sqlRec
        ];

    }
    //datatable listing method for emp_leave_list
    public function get_emp_leave_list_for_datatable()
    {
        $params = $columns = $totalRecords = $data = array();
        $params = $_REQUEST;

        $queryRecords = $this->get_emp_leave_records($params);
        $leave_taken=0;
        $leave_balance=0;

        while ($row = db_fetch_assoc($queryRecords['data'])) {

            $leave_available=$this->get_employee_avail_leaves($params['leave_type'],$row['emp_pk_id']);
            $get_leave_taken=$this->get_emp_leave_tkn($row['emp_pk_id'],$params['year'],$params['leave_type']);
            $get_leave_name=$this->get_leave_name($params['leave_type']);

            if($params['leave_type']=='1')
            {
                $leave_balance=round($leave_available['annual_leave_cnt']);
            }
            else if($params['leave_type']=='3')
            {
                $leave_balance=round($leave_available['sick_leave_avilable']);
            }
            else if($params['leave_type']=='5')
            {
                $leave_balance=round($leave_available['ml_leave_available']);
            }
            else if($params['leave_type']=='8')
            {
                $leave_balance=round($leave_available['bl_leave_available']);
            }


            $data[] = array(
                $row['dept'],
                '('.$row['emp_id'].') '.$row['empl_firstname']." ".$row['empl_lastname'],
                $get_leave_name,
                $get_leave_taken,
                $leave_balance,
            );
        }
        $json_data = array(
            "draw"            => intval($params['draw']),
            "recordsTotal"    => intval($queryRecords['total_records']),
            "recordsFiltered" => intval($queryRecords['total_records']),
            "data"            => $data,
            "params"          => $params,
            // 'sql'             => $queryRecords['sql'],
        );
        echo json_encode($json_data);
    }

    function get_years_for_filter(){
        $yearsArr = ['2020','2021','2022','2023','2024','2025','2026','2027','2028','2029','2030'];
        // return $yearsArr;
        echo json_encode($yearsArr);
    }

    public function get_employee_avail_leaves($leave_type_id,$empl_id)
    {
        $avail_leave=0;
        $msg='';

        $sql="SELECT a.id,b.joining,a.annual_leave_balance,a.skipped_annual_days
                FROM 0_kv_empl_info AS a
                INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                where a.id='".$empl_id."'";

        $result_d = db_query($sql, "Can't get your allowed user details");
        $rowData= db_fetch($result_d);
        $eligible_leave='0';
        $prob_flag='';
        $prob_msg='';
        $annual_count='0';

        $qry="select char_code,description,id from 0_kv_empl_leave_types where id='".$leave_type_id."'";
        $leave_char_code=db_fetch(db_query($qry));

        $date_of_join=$rowData[1];
        //$date_of_join="2017-01-02";
        $curr_date=date('Y-m-d');
        $annual_leave_calculation_start_date='2021-01-01';

        //$curr_date='2021-03-31';

        $diff = abs(strtotime($curr_date) - strtotime($date_of_join));
        $years = floor($diff / (365*60*60*24));

        /*  if($years>2)
        {
            $cnvert_join=date('m-d',strtotime($date_of_join));
            $date_of_join=date('Y').'-'.$cnvert_join;
          }*/
        /*-------------------------CHECK PROBATION PERIOD OVER---------*/
        $ts1_prob = strtotime($date_of_join);
        $ts2_prob = strtotime($curr_date);
        $year1_prob = date('Y', $ts1_prob);
        $year2_prob = date('Y', $ts2_prob);
        $month1_prob = date('m', $ts1_prob); $month2_prob = date('m', $ts2_prob);

        $diff_prob = (($year2_prob - $year1_prob) * 12) + ($month2_prob - $month1_prob)+1;

        $diff_for_leave_enchase = (($year2_prob - $year1_prob) * 12) + ($month2_prob - $month1_prob);

        $maternity_flag='';
        $bereavement_flag='';
        $maternity_leave='0';
        $bereavement_leave='0';
        /*---------------------------------END--------------------------*/
        if($leave_char_code[0]=='al')
        {
            if($rowData[0]!='')
            {
                /*$last_day_month=date("t", strtotime($curr_date));

                if($last_day_month==date("d", strtotime($curr_date)))
                {
                    $ts1 = strtotime($date_of_join);
                    $ts2 = strtotime($curr_date);
                    $year1 = date('Y', $ts1);
                    $year2 = date('Y', $ts2);
                    $month1 = date('m', $ts1); $month2 = date('m', $ts2);

                    $diff = (($year2 - $year1) * 12) + ($month2 - $month1)+1;
                    $eligible_leave=$diff*2.55;
                }
                else
                {
                    $ts1 = strtotime($date_of_join); // or your date as well
                    $ts2 = strtotime($curr_date);
                    $year1 = date('Y', $ts1);
                    $year2 = date('Y', $ts2);
                    $month1 = date('m', $ts1); $month2 = date('m', $ts2);

                    $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
                    $eligible_leave=$diff*2.55;
                }*/

                /*$ts1 = strtotime($date_of_join); // or your date as well
                $ts2 = strtotime($curr_date);
                $year1 = date('Y', $ts1);
                $year2 = date('Y', $ts2);*/

                $split_annual_start_date= strtotime($annual_leave_calculation_start_date);
                $annual_start_yr = date('Y', $split_annual_start_date);

                /*if($annual_start_yr!='2022')
                {
                    $sql_year="select SUM(days) as SUM_ANU from 0_kv_empl_leave_applied
                          where leave_type='".$leave_type_id."' and req_status='1'
                           AND year between '".date('Y')."' and '".date('Y',strtotime($annual_leave_calculation_start_date))."'
                            AND empl_id='".$rowData['id']."' ";
                    $year_anu_leave=db_fetch(db_query($sql_year));

                    if($year_anu_leave['SUM_ANU']<30)
                {
                        $year_anu_leave['SUM_ANU']=30-$year_anu_leave['SUM_ANU'];
                    }
                }*/




                if($rowData['annual_leave_balance']!=0)
                {
                    $date_of_join = strtotime($annual_leave_calculation_start_date); // or your date as well
                    $current_date = strtotime($curr_date);
                    $datediff = $current_date - $date_of_join;

                    $calculated_annual_leave=$datediff / (60 * 60 * 24)+1;

                    $eligible_leave=($calculated_annual_leave/365*30)+$rowData['annual_leave_balance'];

                }
                else
                {
                    $date_of_join = strtotime($date_of_join); // or your date as well
                    $current_date = strtotime($curr_date);
                    $datediff = $current_date - $date_of_join;

                    $calculated_annual_leave=$datediff / (60 * 60 * 24)+1;

                    $eligible_leave=$calculated_annual_leave/365*30;
                }




                $year_anu_leave[0]=0;

                $sql_anu="select SUM(days) as SUMANU from 0_kv_empl_leave_applied 
                          where leave_type='".$leave_type_id."' and req_status='1' AND empl_id='".$rowData['id']."' and del_status='1' ";
                $taken_anu_leave=db_fetch(db_query($sql_anu));

                if($taken_anu_leave[0]!='')
                {
                    $reduce_leave=$taken_anu_leave[0];
                }

                if($rowData['skipped_annual_days']!=0)
                {
                    $reduce_leave=$reduce_leave+$rowData['skipped_annual_days'];
                }

                //$reduce_leave=30;
                //$reduce_leave=0;

                /*  $diff = abs(strtotime($curr_date) - strtotime($date_of_join));
                  $years = floor($diff / (365*60*60*24));*/




                /*if($rowData['annual_leave_balance']!='0')
                {
                    $eligible_leave=$eligible_leave+$rowData['annual_leave_balance'];
                }*/


                $msg=$leave_char_code[1].' Availabe :'.round(round($eligible_leave)-$year_anu_leave[0]-$reduce_leave);

                $annual_count=abs(round($eligible_leave)-$reduce_leave);




                if($annual_count>60)
                {
                    $annual_count=60;
                }

                /**************************************END***********************/
            }
        }
        else if($leave_char_code[0]=='sl')
        {
            $yearEnd = date('Y') . '-12-31';

            $diff = abs(strtotime($yearEnd) - strtotime($rowData['joining']));

            $year_days=round( $diff / (60 * 60 * 24)+1);
            $year_of_join_date=date('Y',strtotime($rowData['joining']));


            if($year_days<365 &&  $year_of_join_date!=date('Y'))
            {
                $sql_sick="select SUM(days) as SUMSick from 0_kv_empl_leave_applied 
                       where leave_type='".$leave_type_id."' and req_status='1'
                       AND YEAR(DATE) between '".$year_of_join_date."' and '".date('Y')."'
                       AND empl_id='".$rowData['id']."'";
            }
            else
            {
            $sql_sick="select SUM(days) as SUMSick from 0_kv_empl_leave_applied 
                       where leave_type='".$leave_type_id."' and req_status='1' and YEAR(DATE)='".date('Y')."'

                       AND empl_id='".$rowData['id']."'";
            }




            $taken_sick_leave=db_fetch(db_query($sql_sick));

            $reduce_leave='0';
            if(empty($taken_sick_leave[0]))
            {
                $taken_sick_leave[0]='0';
            }



            if($taken_sick_leave[0]>15 && $taken_sick_leave[0]<=30)
            {
                $sick_leave_validation_flg='VALID_FOR_HALF_DAY';
            }
            else if($taken_sick_leave[0]>30)
            {
                $sick_leave_validation_flg='VALID_FOR_UNPAID';
            }


            if($year_days<365)
            {
                $taken_sick_leave[0]=($year_days/(365/15))-$taken_sick_leave[0];
            }
            else
            {
                $taken_sick_leave[0]=15-$taken_sick_leave[0];
            }


            //$msg=$leave_char_code[1].' Taken :'.$taken_sick_leave[0];
            //$annual_count='0';
        }
        else if($leave_char_code[0]=='co')
        {
            $sql_combooff="select count(id) as Combo_Off from 0_kv_empl_earned_combo_off 
                       where empl_id='".$rowData[0]."' and YEAR(created_on)='".date('Y')."' ";
            $combo_off_sum=db_fetch(db_query($sql_combooff));


            $QUERY="select SUM(days) as taken_combof from 0_kv_empl_leave_applied 
                       where leave_type='".$leave_type_id."' and req_status='1' and year='".date('Y')."'
                       AND empl_id='".$rowData['id']."'";
            $taken_comb_leave=db_fetch(db_query($QUERY));

            $combo_off_available='0';

            if(empty($taken_comb_leave[0]))
            {
                $taken_comb_leave[0]='0';
            }

            $combo_off_available=$combo_off_sum[0]-$taken_comb_leave[0];


            $msg=$leave_char_code[1].' Availabe :'.$combo_off_available;
        }
        else if($leave_char_code[0]=='ml')
        {
            $QUERY="select SUM(days) as taken_combof from 0_kv_empl_leave_applied 
                       where leave_type='".$leave_type_id."' and req_status='1' and year='".date('Y')."'
                       AND empl_id='".$rowData['id']."' ";
            $taken_maternity_leave=db_fetch(db_query($QUERY));

            if($taken_maternity_leave[0]=='45')
            {
                $maternity_flag='NOT_ALLOWED';
            }
            else if($years<1)
            {
                $maternity_flag='YEAR_NOT_REACH';
            }
            else
            {
                $taken_maternity_leave[0]=45-$taken_maternity_leave[0];
                $maternity_flag='ALLOWED';
            }

            $msg=$leave_char_code[1].' Availabe :'.$taken_maternity_leave[0];
            $maternity_leave=$taken_maternity_leave[0];

        }
        else if($leave_char_code[0]=='bl')
        {
            $QUERY="select SUM(days) as taken_combof from 0_kv_empl_leave_applied 
                       where leave_type='".$leave_type_id."' and req_status='1' and year='".date('Y')."'
                       AND empl_id='".$rowData['id']."'";
            $taken_bereavement_leave=db_fetch(db_query($QUERY));

            if($taken_bereavement_leave[0]=='3')
            {
                $bereavement_flag='NOT_ALLOWED';
            }
            else
            {
                $taken_bereavement_leave[0]=3-$taken_bereavement_leave[0];
                $bereavement_flag='ALLOWED';
            }

            $msg=$leave_char_code[1].' Availabe :'.$taken_bereavement_leave[0];

            $bereavement_leave=$taken_bereavement_leave[0];
        }
        else
        {
            $eligible_leave=0;
            $msg=$leave_char_code[1].' Taken :'.$eligible_leave;
        }


        return ['annual_leave_cnt'=>substr($annual_count, 0, 2),'combo_off'=>substr($combo_off_sum[0], 0, 2)
            ,'maternity_flg'=>$maternity_flag,'bereavement'=>$bereavement_flag,'ml_leave_available'=>substr($maternity_leave, 0, 2),
            'bl_leave_available'=>substr($bereavement_leave, 0, 2),'sick_leave_avilable'=>substr($taken_sick_leave[0], 0, 2)];
    }

    public function get_emp_leave_tkn($empl_id,$year,$leave_type)
    {

        $sql_emp="SELECT a.id,b.joining 
                FROM 0_kv_empl_info AS a
                INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                where a.id='".$empl_id."'";
        $data_empl = db_fetch(db_query($sql_emp));

        if($leave_type=='3')
        {
            $yearEnd = date('Y') . '-12-31';

            $diff = abs(strtotime($yearEnd) - strtotime($data_empl['joining']));
                 
            $year_days=round( $diff / (60 * 60 * 24)+1);
            $year_of_join_date=date('Y',strtotime($data_empl['joining']));


            if($year_days<365 &&  $year_of_join_date!=date('Y'))
            {
                $sql="select SUM(days) as leave_days from 0_kv_empl_leave_applied 
                       where leave_type='".$leave_type."' and req_status='1'
                       AND YEAR(DATE) between '".$year_of_join_date."' and '".date('Y')."'
                       AND empl_id='".$empl_id."'";
            }
            else
            {
                $sql="select SUM(days) as leave_days from 0_kv_empl_leave_applied 
                       where leave_type='".$leave_type."' and req_status='1' and YEAR(DATE)='".date('Y')."'

                       AND empl_id='".$empl_id."'";
            }
        }
        else
        {
            $sql="select SUM(days) as leave_days from 0_kv_empl_leave_applied
                          where leave_type='".$leave_type."' and req_status='1' 
                          AND YEAR(DATE) = '".$year."' AND empl_id='".$empl_id."' and del_status='1' ";
        }


        $data=db_fetch(db_query($sql));

        if($leave_type=='1')
        {
            /*********************SKIPPED LEAVE***************/
            $sql_skipped="select skipped_annual_days  from 0_kv_empl_info
                          where id='".$empl_id."' ";
            $skipped=db_fetch(db_query($sql_skipped));
            /*************************END*********************/
        }
        else
        {
            $skipped['skipped_annual_days']=0;
        }

        $total_days_taken=$data['leave_days']+$skipped['skipped_annual_days'];

        return $total_days_taken;

    }

    public function get_leave_name($leave_type_id)
    {
        $sql="select description from 0_kv_empl_leave_types where  id = '".$leave_type_id."'  ";
        $data=db_fetch(db_query($sql));

        return $data['description'];
    }


    public function export_leave_balances()
    {
        $filename = 'Leave_Report_'.date('Ymd').'.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");

        $file = fopen('php://output', 'w');
        $header = array("Department",
            "Emp.Name",
            "Leave Type",
            "Taken Leave",
            "Available Leaves"
        );

        fputcsv($file, $header);
        $data=[];
        $k=0;

        $queryRecords = $this->get_emp_leave_records($_POST);
        while ($row = db_fetch_assoc($queryRecords['data'])) {

            $leave_available=$this->get_employee_avail_leaves($_POST['leave_type'],$row['emp_pk_id']);
            $get_leave_taken=$this->get_emp_leave_tkn($row['emp_pk_id'],$_POST['year'],$_POST['leave_type']);
            $get_leave_name=$this->get_leave_name($_POST['leave_type']);

            if($_POST['leave_type']=='1')
            {
                $leave_balance=round($leave_available['annual_leave_cnt']);
            }
            else if($_POST['leave_type']=='3')
            {
                $leave_balance=round($leave_available['sick_leave_avilable']);
            }
            else if($params['leave_type']=='5')
            {
                $leave_balance=round($leave_available['ml_leave_available']);
            }
            else if($_POST['leave_type']=='8')
            {
                $leave_balance=round($leave_available['bl_leave_available']);
            }


            $data[] = array(
                $row['dept'],
                '('.$row['emp_id'].') '.$row['empl_firstname']." ".$row['empl_lastname'],
                $get_leave_name,
                $get_leave_taken,
                $leave_balance,
            );

            fputcsv($file,$data[$k]);
            $k++;


        }

        fclose($file);







    }


    public function get_year_dropdown()
    {
        $html='';
        $selected='';
        for($i=2019;$i<=2040;$i++){
            if(date('Y')==$i)
            {
                $selected='selected="selected"';
            }
            else
            {
                $selected='';
            }
            $html.="<option value=".$i." ".$selected.">".$i."</option>";
        }


        return $html;
    }




    public function download_payroll()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);
        $year=$_POST['year'];
        $month=$_POST['month'];


        $sql="SELECT a.payslip_id,a.pay_year,a.pay_month,b.empl_id,a.id,b.processed_salary
              FROM 0_kv_empl_payroll_master AS a
              INNER JOIN 0_kv_empl_payroll_details AS b ON a.id=b.payslip_id
              WHERE a.pay_year='".$year."' AND a.pay_month='".$month."'  ";
        /*if(!in_array($_SESSION['wa_current_user']->access,[2,16,17]))
        {*/
        $sql_emp="select employee_id from 0_users where id='".$_SESSION['wa_current_user']->user."' ";
        $data_set=db_fetch(db_query($sql_emp));

        if(empty($data_set[0]))
        {
            $data_set[0]='0';
        }
        $sql.=" AND b.empl_id='".$data_set[0]."' ";
        /*}*/
        $sql.="  GROUP BY a.payslip_id LIMIT ".$start.",".$length." ";

        $result=db_query($sql);

        $data=array();

        while($doc_data=db_fetch($result))
        {
            $data[] = array(
                $doc_data['payslip_id'],
                $doc_data['pay_year'],
                $doc_data['pay_month'],
                "<label class='Cls_payslip_download' style='color: blue;
                 cursor: pointer;
                 text-decoration: underline;' alt_empl_id='".$doc_data['empl_id']."' alt_payroll_id='".$doc_data['id']."'>Download</label>"
            );
        }

        $sql_tot="SELECT a.payslip_id 
              FROM 0_kv_empl_payroll_master AS a
              INNER JOIN 0_kv_empl_payroll_details AS b ON a.id=b.payslip_id
              WHERE a.pay_year='".$year."' AND a.pay_month='".$month."' AND b.empl_id='".$data_set[0]."'  GROUP BY a.payslip_id";
        $res=db_query($sql_tot);
        $rows=db_num_rows($res);


        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => $rows,
            "recordsFiltered" => $rows,
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);
    }



    public function validate_sick_leave($leave_type_id,$empl_joining,$empl_id,$requested_days)
    {
        $yearEnd = date('Y') . '-12-31';

        $diff = abs(strtotime($yearEnd) - strtotime($empl_joining));

        $year_days=round( $diff / (60 * 60 * 24)+1);
        $year_of_join_date=date('Y',strtotime($empl_joining));


        if($year_days<365 &&  $year_of_join_date!=date('Y'))
        {
            $sql_sick="select SUM(days) as SUMSick from 0_kv_empl_leave_applied 
                       where leave_type='".$leave_type_id."' and req_status='1'
                       AND YEAR(DATE) between '".$year_of_join_date."' and '".date('Y')."'
                       AND empl_id='".$empl_id."'";
        }
        else
        {
            $sql_sick="select SUM(days) as SUMSick from 0_kv_empl_leave_applied 
                       where leave_type='".$leave_type_id."' and req_status='1' and YEAR(DATE)='".date('Y')."'
    
                       AND empl_id='".$empl_id."'";
        }

        $taken_sick_leave=db_fetch(db_query($sql_sick));

        if($taken_sick_leave[0]>=15)
        {
            $taken_sick_leave[0]=15;
        }

        $balance_leave='';
        $balance_leave=15-$taken_sick_leave[0];

        if($taken_sick_leave[0]<15 && $taken_sick_leave[0]==0)
        {
            $sick_leave_validation_flg="BEGIN";
        }
        else if(($taken_sick_leave[0]+$requested_days)>15 && $balance_leave!=0)
        {

            $sick_leave_validation_flg="YOU HAVE ALREADY CONSUMED / CHOOSING MORE THAN THE ELIGIBLE SICK LEAVE $taken_sick_leave[0] OUT OF 15
                                         .NOW YOU CAN APPLY THE BALANCE/AVILABLE LEAVE OF $balance_leave .THE REST LEAVE YOU CAN APPLY AS SPERATE LEAVE. ";
        }
        /*else if(($taken_sick_leave[0]+$requested_days)>15 && ($taken_sick_leave[0]+$requested_days)<=30)
        {
            $sick_leave_validation_flg='YOU HAVE CONSUMED ALL THE 15 DAYS SICK LEAVE , NOW YOU CAN APPLY FOR HALF DAY LEAVE ONLY';
        }*/
        else if($taken_sick_leave[0]==90)
        {
            $sick_leave_validation_flg='ALL THE SICK LEAVES AVAILABLE IS USED. ';
        }
        if(empty($sick_leave_validation_flg))
        {
            $sick_leave_validation_flg="BEGIN";
        }

        return array("status"=> $sick_leave_validation_flg);
    }


    public function get_user_dim()
    {
        $sql="select dflt_dimension_id from 0_users where id='".$_SESSION['wa_current_user']->user."' ";
        $dim_id=db_fetch(db_query($sql));
        return $dim_id['dflt_dimension_id'];
    }



}