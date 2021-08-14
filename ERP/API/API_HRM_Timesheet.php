<?php
include_once("AxisPro.php");
include_once("PrepareQuery.php");

Class API_HRM_Timesheet
{
    /*----------------------PREPARE TIMESHEET---------------*/

    public function prepare_timesheet()
    {
        global $SysPrefs;
        $dept_id=$_POST['dept_id'];
        $emp_id=$_POST['Emp_id'];
        $Fdate=$_POST['frmDate'];
        $Tdate=$_POST['toDate'];
        $show_in_out=$_POST['include_in_out'];
        $filter_time=$_POST['filter_time'];
        /* $leavetypes_arr = [
          'al' => 'Annual Leave',
          'sl' => 'Sick Leave',
          'ml' => 'Maternity Leave',
          'cp' => 'Official Public Holidays',
          'bl' => 'Bereavement Leave'
       ];*/



        $grace_time=$SysPrefs->prefs['payroll_grace_time'];

        $leavetypes_arr =$this->get_all_leave_types();






        $Date = $this->getDatesFromRange($Fdate,$Tdate);

        if(sizeof($Date)<=31)
        {
            $limit = 10;
            if ($_POST["pagecnt"]!='') {
                $page  = $_POST["pagecnt"];
            }
            else{
                $page=1;
            }

            $start_from = ($page-1) * $limit;

            $sql = "SELECT a.empl_id as employeeId,a.empl_firstname as Employeename,a.id as emp_pk_id
                FROM 0_kv_empl_info as a 
                INNER JOIN 0_kv_empl_job as b ON a.id=b.empl_id
                WHERE b.department='".$dept_id."' and a.status='1' ";
            if($emp_id!='0')
            {
                $sql.= " AND a.empl_id='".$emp_id."' ";
            }

            $sql.= " LIMIT $start_from, $limit";


            $result = db_query($sql);

            $table="<table   class=\"table table-bordered \" style='font-size:90%'>
                       <thead><tr>
                         <th class='border-dark' >EmpID</th>
                         <th class='border-dark'  >Empname</th>";

            for($i=0;$i<sizeof($Date);$i++)
            {
                $weakend=$this->isWeekend($Date[$i]);
                $color=$this->getColorCodes($weakend);
                if($weakend=='1')
                {
                    $disp_head='Fri.';
                }
                else
                {
                    $disp_head=date('d',strtotime($Date[$i]));
                }

                $table.="<th class='border-dark' $color>".$disp_head."</th>";
            }
            $table.=" </tr></thead>
                      <tbody>";

            $time_disp_lbl='';
            $seconds=0;
            $get_leave_name='';

            while ($myrow = db_fetch($result)) {
                $table.="<tr>
                      <td class='border-dark'>".$myrow['employeeId']."</td>
                      <td class='border-dark'>".$myrow['Employeename']."</td>";
                for($i=0;$i<sizeof($Date);$i++)
                {
                    $attendence=$this->getAttendenceCount($Date[$i],$myrow['employeeId']);





                    $shift_res_data=$this->get_shift_from_day($Date[$i],$myrow['emp_pk_id']);
                    $begin_time=$shift_res_data[0];
                    $shift_id=$shift_res_data[1];
                    if(empty($shift_id))
                    {
                        $shift_id=0;
                    }


                    $shift_time=explode(":",$begin_time);
                    $attendence_intime_exp=explode(":",$attendence['in_time']);



                    if($attendence['code']=='a')
                    {
                        $attendence['duration']='<span class="badge badge-danger" style="color:white;font-weight:bold;">ABSENT</span>';
                    }
                    else
                    {
                        if($attendence['duration']!='')
                        {
                            $attendence['duration']='<label style="margin-bottom: 0;">'.$attendence['duration'].'</label>';
                        }
                        /*else
                        {
                            $attendence['duration']='MISSING';
                        }*/
                        $color=$this->getColorCodes($attendence['code']);



                        if(in_array($attendence['code'],$leavetypes_arr))
                        {
                            $get_leave_name=$this->get_leave_name($attendence['code']);
                            $attendence['duration']='<span class="badge badge-dark" style="color:white;font-weight:bold;">'.$get_leave_name.'</span>';
                        }
                        else if($attendence['duration']=='')
                        {

                            $chck_shift_off=$this->getShiftOff($Date[$i],$myrow['employeeId'],$myrow['emp_pk_id']);
                             //echo $chck_shift_off.' ---- ';

                            /***********************777 Means shift Off**************/

                            if($chck_shift_off=='777')
                            {
                                $attendence['duration']='<span class="badge badge-warning" style="color:black;font-weight:bold;">OFF</span>';
                            }
                            else
                            {
                                $attendence['duration']='<span class="badge badge-danger" style="color:white;font-weight:bold;">ABSENTT</span>';
                            }


                        }
                        else
                        {


                            if($shift_time[0]!='' )
                            {
                                $begin_time_exp=$shift_time[0].':'.$shift_time[1].':00';
                                $att_in_time=$attendence_intime_exp[0].':'.$attendence_intime_exp[1].':00';
                                $diffe=strtotime($att_in_time)-strtotime($begin_time_exp);
                                $seconds=floor($diffe / 60);
                            }
                            else
                            {
                                $seconds=0;
                            }

                            if($attendence['duration']!='MISSING')
                            {

                                $attendence['duration']='<span style="color: white;
                                                    font-weight: bold;" class="badge badge-info btn-block my-1">DURATION : '.$attendence['duration'].' </span></br>';


                                if($show_in_out==1)
                                {
                                    $attendence['duration'].='<span style="color: white;
                                                             font-weight: bold;" class="badge btn-block badge-success mb-1">IN : '.date('h:i:s A',strtotime($attendence['in_time'])).' </span></br>
                                                                                           <span style="color: white;
                                                             font-weight: bold;" class="badge btn-block badge-primary">OUT : '.date('h:i:s A',strtotime($attendence[4])).' </span></br>
                                                          ';
                                }
                            }



                        }
                    }





                    $date = new DateTime( $attendence['a_date']." ".$attendence['in_time'] );
                    $date2 = new DateTime( $attendence['a_date']." ".$attendence['out_time'] );

                    $diff_in_sec = $date2->getTimestamp() - $date->getTimestamp();

                    $in_time_stamp=date('h:i:s A',strtotime($attendence['in_time']));
                    /*$in_time_minute=date('i',strtotime($attendence['in_time']));
                    $in_time_second=date('s',strtotime($attendence['in_time']));*/

                    $out_time_stamp=date('h:i:s A',strtotime($attendence[4]));
                    /*$out_time_minute=date('i',strtotime($attendence[4]));
                    $out_time_second=date('s',strtotime($attendence[4])); */

//echo $chck_shift_off.' --- ';
                    if($get_leave_name=='' && $chck_shift_off=='')
                    {
                        if(isset($shift_res_data[2]))
                        {
                            $attendence['duration'].='</br> <label style="color:green;font-weight:bold;">Shift : '.$shift_res_data[2].'</label>';
                        }
                        else
                        {

                            $attendence['duration'].='</br> <label style="color:red;font-weight:bold;">Shift : Not Assigned</label>';
                        }
                    }


                    $get_leave_name='';
                    $chck_shift_off='';



                    if($seconds <= $grace_time && $filter_time=='1')
                    {
                        $table.="<td class='border-dark' $color style='vertical-align:middle;'><label class='ClsAttendence' atten_date='".$Date[$i]."' altempl_id='".$myrow['employeeId']."' altid='".$attendence['attendece_pk_id']."' alt_code='".$attendence['code']."' alt_shift_id='".$shift_id."' alt_intime='".$in_time_stamp."' alt_outime='".$out_time_stamp."' style='cursor: pointer;'>".$attendence['duration']."</label></td>";
                    }
                    else if($seconds > $grace_time && $filter_time=='2')
                    {
                        $table.="<td class='border-dark' $color style='vertical-align:middle;'><label class='ClsAttendence' atten_date='".$Date[$i]."' altempl_id='".$myrow['employeeId']."' altid='".$attendence['attendece_pk_id']."' alt_code='".$attendence['code']."' alt_shift_id='".$shift_id."' alt_intime='".$in_time_stamp."' alt_outime='".$out_time_stamp."' style='cursor: pointer;'>".$attendence['duration']."</label></td>";
                    }
                    else if($diff_in_sec < 28800 && $filter_time=='3')
                    {
                        $table.="<td class='border-dark' $color style='vertical-align:middle;'><label class='ClsAttendence' atten_date='".$Date[$i]."' altempl_id='".$myrow['employeeId']."' altid='".$attendence['attendece_pk_id']."' alt_code='".$attendence['code']."' alt_shift_id='".$shift_id."' alt_intime='".$in_time_stamp."' alt_outime='".$out_time_stamp."' style='cursor: pointer;'>".$attendence['duration']."</label></td>";
                    }
                    else if($filter_time=='0')
                    {
                        $table.="<td class='border-dark' $color style='vertical-align:middle;'> <label class='ClsAttendence' atten_date='".$Date[$i]."' altempl_id='".$myrow['employeeId']."' altid='".$attendence['attendece_pk_id']."' alt_code='".$attendence['code']."' alt_shift_id='".$shift_id."' alt_intime='".$in_time_stamp."' alt_outime='".$out_time_stamp."' style='cursor: pointer;'>".$attendence['duration']."</label></td>";
                    }else{
                        $table .= "<td class='border-dark'></td>";
                    }








                }

                $get_leave_name='';
            }

            $table.="</tr>";
            //}
            $table.="</tbody></table>";



            /*********************PAGINATION STARTS**********************/
            $table.= "<ul class='pagination' style='float: right;'>";
            /*******GET TOTAL COUNT OF RECORDS***********/
            $totCnt="SELECT a.empl_id as employeeId,a.empl_firstname as Employeename
                    FROM 0_kv_empl_info as a 
                    INNER JOIN 0_kv_empl_job as b ON a.id=b.empl_id
                    WHERE b.department='".$dept_id."'";
            if($emp_id!='0')
            {
                $totCnt.= " AND a.empl_id='".$emp_id."' ";
            }
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
        else
        {
            return AxisPro::SendResponse(['status' => 'FAIL', 'msg' => "SORRY!! The date range can't greater than 31 Days "]);
        }

    }


    function getDatesFromRange($start, $end, $format = 'Y-m-d') {

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


    function get_all_leave_types()
    {
        $sql="select char_code,description from 0_kv_empl_leave_types where char_code<>'p' ";
        $res=db_query($sql);
        // $data=db_fetch($res);

        $char_codes=array();

        while($data=db_fetch($res))
        {
            array_push($char_codes,$data['char_code']);
        }


        return $char_codes;
    }


    function get_leave_name($code)
    {
        $sql="select  description from 0_kv_empl_leave_types where char_code='".$code."' ";
        $res=db_query($sql);
        $leave_name=db_fetch($res);
        return $leave_name['description'];
    }




    public function getShiftOff($shift_date,$emplid,$employee_pk_id)
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




    function getAttendenceCount($from,$emp_id)
    {
        global $SysPrefs;

        $employee_id=(int)$emp_id;

        $sql="SELECT `code`,time_format(SUM(abs(timediff(out_time, in_time))),'%H:%i:%s') as duration,id as attendece_pk_id,in_time,out_time,duration as duration_hours,a_date
                FROM 0_kv_empl_attendance 
                WHERE a_date='".$from."' and empl_id='".$employee_id."'";
        //echo $sql.' --- ';
        $result = db_query($sql);

        return db_fetch($result);
    }


    function get_shift_from_day($shift_date,$empl_id)
    {
        $sql="SELECT s.BeginTime,a.shift_id,CONCAT(s.BeginTime,' - ',s.EndTime) AS ShiftTime
              FROM 0_kv_empl_shiftdetails as a 
              LEFT JOIN 0_kv_empl_shifts as s ON s.id=a.shift_id
              where s_date='".$shift_date."' 
              AND empl_id='".$empl_id."' ";
        $result = db_query($sql);
        $result_data=db_fetch($result);

        return $result_data;

    }

    function getColorCodes($code)
    {
        $font_color='';
        if($code=='p')
        {
            $font_color='style="color:green;font-weight:bold;";';
        }

        if($code=='wl' || $code=='a' || $code=='sl')
        {
            $font_color='style="color:red;font-weight:bold;"';
        }

        if($code=='cl')
        {
            $font_color='style="color:blue;font-weight:bold;"';
        }

        if($code=='1')
        {
            $font_color='style="background-color: #a55353;color: white;"';
        }
        return $font_color;
    }

    function isWeekend($date) {
        return (date('N', strtotime($date)) ==5);
    }

    public function getLeaveTypes()
    {
        $sql="SELECT char_code,description
              FROM 0_kv_empl_leave_types";
        $result = db_query($sql);
        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }
        return $return_result;
    }

    /*******
     * UPDATE Employee Attendence
     */
    public function update_attendence()
    {
        $pk_id=$_POST['pk_id'];
        $leave_type=$_POST['leave_type'];
        $in_hour=$_POST['in_hour'];
        $in_min=$_POST['in_min'];
        $out_hr=$_POST['out_hr'];
        $out_min=$_POST['out_min'];
        $in_format=$_POST['in_format'];
        $out_format=$_POST['out_format'];
        $empl_id=$_POST['altempl_id'];
        $remarks=$_POST['remarks'];
        $shift_id=$_POST['shift_id'];
        $dept_id=$_POST['dept_id'];
        $from=date('Y-m-d',strtotime($_POST['from']));
        $to=date('Y-m-d',strtotime($_POST['to']));
        $s_date=date('Y-m-d',strtotime($_POST['s_date']));
        $assigned_shift_id=$_POST['assigned_shift_id'];



        $inhour_formatted='';
        $out_hr_formatted='';
        if($in_format=='PM')
        {
            if($in_hour!='12')
            {
                $inhour_formatted='12'+$in_hour;
            }
            else
            {
                $inhour_formatted=$in_hour;
            }

        }
        else
        {
            $inhour_formatted=$in_hour;
        }

        if($out_format=='PM')
        {
            $out_hr_formatted='12'+$out_hr;
        }
        else
        {
            $out_hr_formatted=$out_hr;
        }
        $in_time_merged=$inhour_formatted.':'.$in_min.':00';
        $out_time_merged=$out_hr_formatted.':'.$out_min.':00';

        $time1 = strtotime($in_time_merged);
        $time2 = strtotime($out_time_merged);
        $duration= round(abs($time2 - $time1) / 3600,2);


        /***
         * Checking payslip generated for the employee, If not then allow update attendence.
         */
        $qry="SELECT MONTH(a.a_date),b.id,a.a_date
                FROM  0_kv_empl_attendance AS a
                INNER JOIN  0_kv_empl_info AS b ON a.empl_id=b.empl_id
                WHERE a.id='".$pk_id."'";
        $atten_data=db_fetch(db_query($qry));

        //$payroll_cutoff_from_to=$this->get_payroll_cutoff_date($from,$to,$atten_data[0]);


        if(isset($atten_data))
        {
            $p_sql=" 
                        
                        SELECT a.id
                            FROM 0_kv_empl_payroll_details AS a
                            INNER JOIN 0_kv_empl_payroll_master AS b ON b.id=a.payslip_id
                            WHERE  '".$s_date."' BETWEEN b.payroll_from_date AND b.payroll_to_date 
                            AND a.empl_id='".$atten_data[1]."'  
        
                        ";
            $exst_check=db_fetch(db_query($p_sql));
        }

       /*if($exst_check[0]!='')
        {*/
            $choosen_day=explode('-',$s_date);

             if($exst_check[0]!='')
             {
                 $msg=['status' => 'FAIL', 'msg' =>"Error occured while updating attendence.
                 Payslip already processed for the employee for the Month."];
             }
             else
             {
                 if($pk_id=='' && $leave_type!='777')
                 {
                     $sql="INSERT INTO 0_kv_empl_attendance (empl_id,`code`,a_date,in_time,out_time,duration,remarks,updated_by) 
                     values('".$empl_id."','".$leave_type."','".$_POST['atten_date']."','".$in_time_merged."','".$out_time_merged."','".$duration."','".$remarks."',".$_SESSION['wa_current_user']->user.")" ;
                 }
                 else
                 {
                     if($leave_type=='p')
                     {
                         $sql="UPDATE 0_kv_empl_attendance SET code='".$leave_type."',in_time='".$in_time_merged."',out_time='".$out_time_merged."',duration='".$duration."',remarks='".$remarks."',updated_by='".$_SESSION['wa_current_user']->user."' 
                      WHERE id='".$pk_id."'";
                     }
                     else if($leave_type=='777' && $shift_id=='777')
                     {
                         $sql="delete from 0_kv_empl_attendance where id='".$pk_id."' ";
                     }
                     else
                     {
                         $sql="UPDATE 0_kv_empl_attendance SET code='".$leave_type."',in_time='00:00:00',out_time='00:00:00',duration='0',remarks='".$remarks."',updated_by='".$_SESSION['wa_current_user']->user."' 
                               WHERE id='".$pk_id."'";
                     }

                 }

                 if(db_query($sql))
                 {
                     $leavetypes=array('wl','p','777');
                     if(!in_array($leave_type,$leavetypes))
                     {
                         $chk="SELECT id FROM 0_kv_empl_leave_applied WHERE  empl_id='".$atten_data[1]."' 
                           AND leave_type='".$leave_type."' AND `date`='".$atten_data[2]."'";
                         $exist_or_not=db_fetch(db_query($chk));

                         $L_Sql="INSERT INTO 0_kv_empl_leave_applied (`year`,empl_id,leave_type,`date`,days,status,created_by,del_status)
                             VALUES ('".date('Y')."','".$atten_data[1]."','".$leave_type."'
                                    ,'".$atten_data[2]."','1','1','".$_SESSION['wa_current_user']->user."','1')";

                         db_query($L_Sql);
                     }

                     $emp_pk_sql="select id from 0_kv_empl_info where empl_id='".$empl_id."' ";
                     $empl_pk_id=db_fetch(db_query($emp_pk_sql));


                     $sql_s="select id from 0_kv_empl_shiftdetails where empl_id='".$empl_pk_id[0]."' and s_date='".$s_date."' ";
                     $res_shift_cnt=db_query($sql_s);
                     $assigned_shift_id=db_num_rows($res_shift_cnt);

                     if($assigned_shift_id==0)
                     {
                         $shift_sql="Insert into 0_kv_empl_shiftdetails (shift_id,dept_id,empl_id,`from`,`to`,s_date
                                 ,created_at,created_by )
                                 values ('".$shift_id."','".$dept_id."','".$empl_pk_id[0]."','".$from."','".$to."','".$s_date."','".date('Y-m-d H:i:s')."','".$_SESSION['wa_current_user']->user."')";
                     }
                     else
                     {
                         $shift_sql="Update 0_kv_empl_shiftdetails SET shift_id='".$shift_id."' where empl_id='".$empl_pk_id[0]."'
                                 AND s_date='".$s_date."' ";
                     }

                     db_query($shift_sql);


                     $msg=['status' => 'OK', 'msg' =>"Successfully Updated Attendence"];
                 }
                 else
                 {$msg=['status' => 'FAIL', 'msg' =>"Error occured while updating"];}
             }
       /* }*/

        return AxisPro::SendResponse($msg);

    }


    public function list_employees_for_shift()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);
        $shift_id=$_POST['shift_id'];

        $sql = "SELECT a.id,a.empl_id,CONCAT(a.empl_firstname,\" \",a.empl_lastname) AS Emp_name,a.mobile_phone,a.email,b.shift
                FROM 0_kv_empl_info AS a
                INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                WHERE b.department='".$_POST['dept_id']."'";

        if($_POST['emp_id']<>'' && $_POST['emp_id']!='0')
        {
            $sql.= " AND a.empl_id='".$_POST['emp_id']."' ";
        }

        /* if($shift_id!='' || $shift_id!='0')
         {
             $sql.= " AND (b.shift='".$shift_id."') OR (b.shift='0')";
         }*/




        $result = db_query($sql);
        $data = [];
        $payslip_label='';
        $checkbox='';
        $checked='';
        $disabled='';
        $i=0;
        $select='';
        $date_selected='';
        $cls_color='';
        while ($myrow = db_fetch($result)) {

            $sql_chk="Select id,s_date FROM 0_kv_empl_shiftdetails Where shift_id='".$shift_id."' 
                      And empl_id='".$myrow['id']."' AND `from`>='".date('Y-m-d',strtotime($_POST['f_date']))."'
                      AND `to`<='".date('Y-m-d',strtotime($_POST['t_date']))."' ";
            $res=db_query($sql_chk);
            $row_data = db_fetch($res);

            if($row_data['id']!='') {
                $checked='checked="checked"';
                ///$cls_color='selected_color';
            }
            else {$checked='';$cls_color='';}

            $checkbox='<input type="checkbox" class="chkEmp_select" name="chkEmp_select" value="'.$myrow['id'].'" alt='.$i.' '.$checked.' '.$disabled.'/>';

            $select_box='<select id="clsShiftDate_'.$myrow['id'].'" name="clsShiftDate_'.$myrow['id'].'" class="form-control">
                         <option value="0">---SELECT SHIFT DATE---</option>';
            $period = new DatePeriod(
                new DateTime(date('Y-m-d',strtotime($_POST['f_date']))),
                new DateInterval('P1D'),
                new DateTime(date('Y-m-d',strtotime($_POST['t_date'])))
            );

            $select='';
            foreach ($period as $key => $value) {
                if($value->format('Y-m-d')==$row_data['s_date'])
                {
                    $date_selected='selected="selected"';
                }
                else
                {
                    $date_selected='';
                }
                $select.='<option value="'.$value->format('d-m-Y').'" '.$date_selected.'>'.$value->format('d-m-Y').'</option>';
            }
            $select.='<option value="'.$_POST['t_date'].'">'.$_POST['t_date'].'</option>';
            $select_box.=$select;
            $select_box.='</select>';

            $data[] = array(
                $checkbox,
                $myrow['empl_id'],
                $myrow['Emp_name'],
                $select_box
            );

            $i++;
        }

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($result),
            "recordsFiltered" => db_num_rows($result),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);

    }





    public function unassign_emp_shift()
    {
        $un_assign_empids=$_POST['un_assign_empids'];

        $sql="UPDATE 0_kv_empl_job SET shift='0',shift_start='0000-00-00',shift_end='0000-00-00'
              WHERE empl_id in (".$un_assign_empids.")";
        if(db_query($sql))
        {
            $msg=['status' => 'OK', 'msg' =>"Successfully Updated Attendence"];

            /*---------------Unassign Employee From Shift-------------*/
            $remove_sql="UPDATE 0_kv_empl_shiftdetails set assign_status='0'
                          ,unassign_date='".date('Y-m-d h:i:s')."',unassign_by='".$_SESSION['wa_current_user']->user."' 
                           WHERE empl_id in (".$un_assign_empids.")";
            //echo $remove_sql;
            if(!db_query($remove_sql))
            {
                /*------------Log Insertion------------*/
                $desc_msg='Unassign of employee from shift failed :'.$un_assign_empids;
                $this->SaveLog($desc_msg,'Unassign employee from shift');

            }

            /*------------------------END-----------------------------*/

        }
        else
        {
            $msg=['status' => 'FAIL', 'msg' =>"Error occured while updating"];
        }

        return AxisPro::SendResponse($msg);
    }

    public function single_click_attendence()
    {
        $dept_id=$_POST['dept_id'];
        $month=$_POST['month'];
        $emp_id=$_POST['emp_id'];

        if(isset($_POST))
        {
            $number_of_days_in_month=cal_days_in_month(CAL_GREGORIAN, $month, date('Y'));
            $start_date=date('Y').'-'.$month.'-01';

            $sql="SELECT a.empl_id as Empid,a.id,b.shift,b.shift_start,b.shift_end,a.id,b.work_hours
                        FROM 0_kv_empl_info AS a
                        INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                        WHERE b.department='".$dept_id."'";
            if(!empty($emp_id))
            {
                $sql.=" AND a.empl_id='".$emp_id."'";
            }

            $result = db_query($sql);
            $leave_days=array();
            $push_date='';
            $desc='';
            $curre_emp_id='';
            $empl_attendence_status='';
            $emp_workHours='';
            $default_time='';

            $sql_defualt_from="SELECT value 
                  from 0_sys_prefs where `name` in ('payroll_work_hours')";
            $res_dft_time_from= db_query($sql_defualt_from);
            $defalut_time_from = db_fetch($res_dft_time_from);

            $sql_defualt_to="SELECT value 
                  from 0_sys_prefs where `name` in ('payroll_work_hours_to')";
            $res_dft_time_to= db_query($sql_defualt_to);
            $defalut_time_to = db_fetch($res_dft_time_to);

            $default_time=$defalut_time_to[0]-$defalut_time_from[0];

            while ($myrow = db_fetch($result))
            {
                $start_t='';
                $end_t='';
                $empl_week_day='';

                /*----------------------CHECK FOR APPROVED LEAVES---------*/
                /* $my_qry="SELECT `date` as leavedate,days
                                      FROM 0_kv_empl_leave_applied WHERE empl_id='".$myrow['id']."'
                                      AND status='1' AND del_status='1'  ";
                 $leave_res=db_query($my_qry);
                 if(db_num_rows($leave_res)>0)
                 {
                     $myrow_leave = db_fetch($leave_res);
                     for($i=1;$i<=$myrow_leave[1];$i++)
                     {
                         array_push($leave_days,$myrow_leave[0]); /*pushing into an array for attendence check purpose*/
                /*  $myrow_leave[0] = date('Y-m-d', strtotime($myrow_leave[0]. ' + 1 days'));
              }
          }*/



                /*--------------------------------END---------------------*/
                $j=0;
                for($i=1;$i<=$number_of_days_in_month;$i++)
                {

                    $start_t='00:00:00';
                    $end_t='00:00:00';

                    /*-----------------------GET OFF DAYS OF EMPLOYEE IF SHIFT IS NOT ASSIGNED-----*/
                    $sql="SELECT weekly_off,work_hours from 0_kv_empl_job where empl_id='".$myrow['id']."'";
                    $res_data= db_query($sql);
                    $empl_job_data = db_fetch($res_data);
                    $empl_week_day=unserialize(base64_decode($empl_job_data[0]));

                    /*$QRY="select id from 0_kv_empl_attendance where a_date='".$start_date."' ";
                    $leave_atte_data=db_fetch(db_query($QRY));*/

//echo $leave_atte_data[0].' ---- ';
                    //  if($leave_atte_data[0]!='')
                    //  {

                    $chk_sql="SELECT id FROM 0_kv_empl_attendance 
                                        WHERE empl_id='".$myrow['Empid']."' AND a_date='".$start_date."' ";
                    $row_attendnce=db_fetch(db_query($chk_sql));

                    if($row_attendnce[0]=='')
                    {
                        $dayname = date('D', strtotime($start_date));
                        if(in_array($dayname,$empl_week_day))
                        {
                            $empl_attendence_status='wl';
                        }
                        else
                        {
                            $empl_attendence_status='p';
                        }

                        if(!empty($empl_job_data[1]) || $empl_job_data[1]!='0')
                        {
                            $emp_workHours=$empl_job_data[1];
                        }
                        else
                        {

                            $emp_workHours=$default_time;
                        }


                        $qry="INSERT INTO 0_kv_empl_attendance (empl_id,code,shift,a_date,in_time,out_time,duration)
                                        VALUES ('".$myrow['Empid']."','".$empl_attendence_status."','".$myrow['shift']."','".$start_date."','".$start_t."','".$end_t."','".$emp_workHours."')";
                        //echo $qry.'-----';
                        if(!db_query($qry))
                        {
                            /*--------------------LOG Saving-----------*/
                            if($myrow['Empid']!=$curre_emp_id)
                            {
                                $desc.="Attendence Creation failed for Employee :".$myrow['Empid']."</br>"
                                    ." For Month :".$month." And year :".date('Y')."</br>";
                            }
                            /*----------------------END-----------------*/
                        }
                        $curre_emp_id=$myrow['Empid'];
                    }

                    //}



                    if($start_date==date('Y').'-'.$month.'-'.$number_of_days_in_month)
                    {
                        $start_date=date('Y').'-'.$month.'-01';
                        unset($leave_days);
                    }
                    else
                    {
                        $start_date=date('Y-m-d',strtotime($start_date. ' + 1 days'));
                    }
                    $j++;
                }
            }



            if(!empty($desc))
            {
                $msg=['status' => 'FAIL', 'msg' =>"There are some error occured while create attendance. 
                      You can check the log page for check attendance."];
                $this->SaveLog($desc,'Single Attendance');
            }
            else
            {
                $msg=['status' => 'SUCCESS', 'msg' =>"Attendance creation done for the employees.
                     Now you can verify to check with timesheet."];
            }


            return AxisPro::SendResponse($msg);
        }
    }

    public function SaveLog($log_message,$from_module)
    {
        $log="INSERT INTO 0_kv_empl_hrm_log (description,from_module,log_date)
                                          VALUES ('".$log_message."','".$from_module."','".date('Y-m-d h:i:s')."')";
        db_query($log);
    }

    public function upload_Attendence()
    {
        $root_url=str_replace("\ERP","",getcwd());
        $root_url=str_replace("\API","",$root_url);
        $filename=$_FILES["Filetoupload"]["tmp_name"];
        $fp = file($filename);
        $total_rows = count($fp);
        $month=$_POST['month'];
        $year=date('Y');
        $root_url=str_replace("\ERP","",getcwd());
        $root_url=str_replace("\API","",$root_url);
        $target_dir = $root_url."/assets/uploads/attendence/";
        $fname=explode(".",$_FILES["Filetoupload"]["name"]);
        $rand=rand(10,1000);
        $filename_up=$fname[0].'_'.$rand.'.'.$fname[1];

        if($_FILES["Filetoupload"]["type"]=='application/vnd.ms-excel')
        {
            $r_sql="Select id from 0_kv_empl_attendence_upload_master where `month`='".$month."' AND `year`='".$year."'
                    AND status='1'";
            $exist_data=db_fetch(db_query($r_sql));
            if($exist_data[0]!='')
            {
                $msg=['status' => 'FAIL', 'msg' =>"Attendence is already uploaded for the selected month"];
                return AxisPro::SendResponse($msg);
            }
            else
            {
                /*----------------------CREATE MASTER ENTRY FOR ATTENDECE UPLOAD---------------*/
                $sql="INSERT INTO 0_kv_empl_attendence_upload_master (file_name,`month`,`year`,`created_on`,`created_by`,status)
                    VALUES ('".$filename_up."','".$month."','".$year."','".date('Y-m-d')."','".$_SESSION['wa_current_user']->user."','1')";
                /*--------------------------------------END-------------------------------------*/
                $res=db_query($sql);
                if($res)
                {
                    $insert_master_id=db_insert_id();
                    $file = fopen($filename, "r");
                    $leave_days_empl=array();
                    $code='';
                    $k='0';
                    while (($empData = fgetcsv($file, 1000, ",")) !== FALSE)
                    {
                        $empl_id=$empData[0];
                        $shift_date=date('Y-m-d',strtotime($empData[2]));
                        $shift_name=$empData[3];
                        $work_hrs=$empData[4];
                        /*-----------------------GET SHIFT ID------------------*/
                        $s_sql="SELECT id,BeginTime,EndTime FROM 0_kv_empl_shifts WHERE description LIKE '%".$shift_name."%' ";
                        $shift_data=db_fetch(db_query($s_sql));
                        /*----------------------CHECK FOR APPROVED LEAVES---------*/
                        $e_sql="SELECT id FROM 0_kv_empl_info WHERE empl_id='".$empl_id."' ";
                        $empl_data=db_fetch(db_query($e_sql));

                        $my_qry="SELECT `date` as leavedate,days
                                     FROM 0_kv_empl_leave_applied WHERE empl_id='".$empl_data['id']."'
                                     AND status='1' AND del_status='1'";
                        $leave_res=db_query($my_qry);
                        if(db_num_rows($leave_res)>0)
                        {
                            $myrow_leave = db_fetch($leave_res);
                            for($i=1;$i<=$myrow_leave[1];$i++)
                            {
                                array_push($leave_days_empl,$myrow_leave[0]); /*pushing into an array for attendence check purpose*/
                                $myrow_leave[0] = date('Y-m-d', strtotime($myrow_leave[0]. ' + 1 days'));
                            }
                        }
                        /*--------------------------------END---------------------*/
                        if(!in_array($shift_date,$leave_days_empl)) {
                            if($shift_data[3]!='' || $work_hrs=='0:00')
                            {
                                $code='a';
                            }
                            else
                            {
                                $code='p';
                            }

                            $sql = "INSERT INTO 0_kv_empl_attendance (empl_id,code,shift,a_date,in_time,out_time,duration,upload_master_id)
                                 VALUES ('".$empl_id."','".$code."','" .$shift_data[0]. "','" .$shift_date."','".$shift_data[1]."'
                                ,'".$shift_data[2]."','".$work_hrs."','".$insert_master_id."')";
                            db_query($sql);

                            $k++;
                        }
                    }

                    $target_file = $target_dir .$filename_up;
                    move_uploaded_file($_FILES["Filetoupload"]["tmp_name"], $target_file);

                    $msg=['status' => 'OK', 'msg' =>"All attendence data imported successfully"];
                    return AxisPro::SendResponse($msg);
                }
            }

        }
        else
        {
            $msg=['status' => 'FAIL', 'msg' =>"File format is not matching, Valid format is CSV."];
            return AxisPro::SendResponse($msg);
        }

    }

    public function list_attendence_upload()
    {
        $sql="SELECT file_name,MONTHNAME(STR_TO_DATE(`month`, '%m')) as sel_month,`year` as sel_year,id
              FROM 0_kv_empl_attendence_upload_master WHERE status='1' ORDER BY ID ASC";
        $resu=db_query($sql);
        $return_result = [];
        while ($myrow = db_fetch($resu)) {
            $return_result[] = $myrow;
        }
        return AxisPro::SendResponse($return_result);
    }


    public function remove_attendnence_data()
    {
        $id=$_POST['remove_id'];
        if(isset($id))
        {
            $remo_sql="DELETE FROM 0_kv_empl_attendance WHERE upload_master_id='".$id."'";
            if(db_query($remo_sql))
            {
                $del_master="Update 0_kv_empl_attendence_upload_master SET status='0' where id='".$id."' ";
                db_query($del_master);

                $msg=['status' => 'OK', 'msg' =>"Attendence Removed Successfully"];
                return AxisPro::SendResponse($msg);
            }
            else
            {

                $msg=['status' => 'FAIL', 'msg' =>"Failed removing attendance data. Please try again after some time."];
                return AxisPro::SendResponse($msg);
            }
        }
    }



    public function list_employees_have_overtime()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);
        $month=$_POST['month'];
        $year=$_POST['year'];
        $empl_id=$_POST['emp_id'];
        $dept_id=$_POST['dept_id'];

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
        if($_POST['emp_id']<>'' && $_POST['emp_id']!='0')
        {
            $sql.= " AND b.empl_id='".$_POST['emp_id']."' ";
        }
        $sql.=" LIMIT ".$start.",".$length."";

        //
        //echo $sql;

        $result = db_query($sql);
        $data = [];
        $overtime_label='';
        $checkbox='';
        $disabled='';
        $i=0;
        while ($myrow = db_fetch($result)) {


            $qry="SELECT SUM(a.pay_amount) AS EmpTotalSalary
                    FROM 0_kv_empl_salary_details as a
                    INNER JOIN 0_kv_empl_pay_elements as b ON b.id=a.pay_rule_id
                    WHERE a.emp_id='".$myrow['id']."' AND b.type='1'";

            $em_gross_salary=db_fetch(db_query($qry));
            $get_one_hour_salary=($em_gross_salary[0]/$number_of_days_in_month)/$working_hours;
            $overtime_extra_hour=$myrow['duration']-$working_hours;

            // $overtime_rate=$get_one_hour_salary*$config_reslt['payroll_overtime_rate']*$overtime_extra_hour;  /******ONE Hour Salary * Overtime rate * Overtime hour **/

            $calc_salary=$em_gross_salary[0]/$number_of_days_in_month/$working_hours*$config_reslt['payroll_overtime_rate'];
            $overtime_rate=round($calc_salary,3)*$overtime_extra_hour;



            $overtime_app_status="SELECT id from 0_kv_empl_overtime WHERE emp_id='".$myrow['id']."'
                                  AND s_date='".$myrow['a_date']."'";
            $approve_status=db_fetch(db_query($overtime_app_status));
            if($approve_status[0]!='')
            {
                $overtime_label='<label style="color: green;font-weight: bold;    background-color: yellow;">Approved</label>';
                $disabled='disabled="disabled"';
            }
            else
            {
                $overtime_label='';
                $disabled='';
            }
            $checkbox='<input type="checkbox" class="ClsChk" alt_key_id="'.$i.'" alt="'.$myrow['id'].'"
            alt_date="'.$myrow['a_date'].'" alt_hour="'.$overtime_extra_hour.'" alt_assign_id="'.$approve_status[0].'"/>';


            $data[] = array(
                $checkbox,
                date('d-m-Y',strtotime($myrow['a_date'])),
                $myrow['empl_id'],
                $myrow['Emp_name'],
                $overtime_extra_hour,
                '<input type="text" id="txt_'.$i.'" value="'.$overtime_rate.'" '.$disabled.'>',
                $overtime_label
            );
            $i++;
        }

        /*---------------------------------GET Total Records--------*/
        /*$sql_total = "SELECT a.empl_id,CONCAT(a.empl_firstname,\" \",a.empl_lastname) AS Emp_name,b.a_date,b.duration,a.id
                FROM 0_kv_empl_attendance AS b
                INNER JOIN 0_kv_empl_info AS a ON a.empl_id=b.empl_id
                INNER JOIN 0_kv_empl_job AS c ON c.empl_id=a.id
                WHERE c.department='".$dept_id."' AND b.a_date>='".$from_date."' AND b.a_date<='".$to_date."'
                AND b.duration>'".$config_reslt[1][0]."'";*/

        $sql_total="SELECT a.empl_id,CONCAT(a.empl_firstname,\" \",a.empl_lastname) AS Emp_name
                ,b.a_date,b.duration,a.id,TIMESTAMPDIFF(HOUR, b.in_time, b.out_time) as duration
                FROM 0_kv_empl_attendance AS b
                INNER JOIN 0_kv_empl_info AS a ON a.empl_id=b.empl_id
                INNER JOIN 0_kv_empl_job AS c ON c.empl_id=a.id
                WHERE c.department='".$dept_id."' AND b.a_date>='".$from_date."' AND b.a_date<='".$to_date."'
                AND TIMESTAMPDIFF(HOUR, b.in_time, b.out_time) >'".$working_hours."' ";

        if($_POST['emp_id']<>'' && $_POST['emp_id']!='0')
        {
            $sql_total.= " AND b.empl_id='".$_POST['emp_id']."' ";
        }

        //echo $sql_total;
        $total_res=db_query($sql_total);
        /*-------------------------------------END-------------------*/

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($total_res),
            "recordsFiltered" => db_num_rows($total_res),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);

    }

    /******
     * @return mixed
     * function for saving over time against each employee
     */
    public function save_over_time()
    {
        $empids=$_POST['empids'];
        $shiftdate=$_POST['shiftdate'];
        $extrahour=$_POST['extrahour'];
        $overtimeanmt=$_POST['overtimeanmt'];
        $flag=$_POST['flag'];
        $month=$_POST['month'];
        $remove_id=$_POST['remove_id'];

        for($i=0;$i<count($empids);$i++)
        {

            if($flag=='1')
            {
                $sql_check="SELECT id FROM 0_kv_empl_overtime WHERE emp_id='".$empids[$i]."' 
                            AND s_date='".$shiftdate[$i]."' ";
                $exist_cnt=db_fetch(db_query($sql_check));
                if($exist_cnt[0]=='')
                {
                    $sql="INSERT into 0_kv_empl_overtime (emp_id,s_date,extra_hour,overtime_amnt,`month`,approved_by,approved_on,`year`)
                      values ('".$empids[$i]."','".$shiftdate[$i]."','".$extrahour[$i]."'
                      ,'".$overtimeanmt[$i]."','".$month."','".$_SESSION['wa_current_user']->user."'
                      ,'".date('Y-m-d h:i:s')."','".date('Y')."')";
                    db_query($sql);
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
            $d_sql="Delete from 0_kv_empl_overtime where id in (".rtrim($pk_ids, ',').")";
            //echo $d_sql;
            db_query($d_sql);

        }

        $msg=['status' => 'OK', 'msg' =>"Successfully Updated "];
        return AxisPro::SendResponse($msg);
    }

    public function get_gl_postings()
    {
        $empl_id=$_POST['empl_id'];
        $payroll_detail_id=$_POST['payroll_detail_id'];

        if(isset($empl_id))
        {
            $sql="SELECT a.account,b.account_name,a.is_gross
                    FROm 0_kv_empl_temp_gl as a
                    INNER JOIN 0_chart_master as b On a.account=b.account_code
                    where a.emp_id='38' AND a.payroll_ref_id='177'
                    ORDER BY a.is_gross DESC ";
            $res=db_query($sql);
            while($row_data=db_fetch($res))
            {

            }
        }

    }

    public function list_payrolls_processed()
    {

        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);
        $_payroll_id=$_POST['payroll_id'];

        $sql="SELECT b.empl_id,concat(b.empl_firstname,'',b.empl_lastname) AS empname,b.id,a.tot_salary_payable,a.processed_salary
                FROM 0_kv_empl_payroll_details AS a
                INNER JOIN 0_kv_empl_info AS b ON a.empl_id=b.id
                INNER JOIN 0_kv_empl_job AS c ON c.empl_id=b.id

                WHERE a.payslip_id='".$_payroll_id."' LIMIT ".$start.",".$length."";
        //echo $sql;
        $res=db_query($sql);
        $data = [];


        $payable_salary='';
        $links='';
        $gl_links='';
        while($row_data=db_fetch($res))
        {
            if($row_data['processed_salary']!='' || $row_data['processed_salary']!='0')
            {
                $payable_salary=$row_data['tot_salary_payable']-$row_data['processed_salary'];
            }
            else
            {
                $payable_salary=$row_data['tot_salary_payable'];
            }
            if($row_data['processed_salary']!='' && $row_data['processed_salary']!='0')
            {
                $links='<label  class="ClsProcessSalary" alt-empname="'.$row_data['empname'].'" data-toggle="modal" data-target="#myModal" alt="'.$row_data['id'].'" altpayroll_id="'.$_payroll_id.'" alt-salary="'.$payable_salary.'" style="cursor:pointer;text-decoration: underline;color:blue;">Process</label> | 
                        <label  class="ClsViewSlip" alt="'.$row_data['id'].'" altpayroll_id="'.$_payroll_id.'" style="cursor:pointer;text-decoration: underline;color:blue;">Download Payslip</label>';
            }
            else
            {
                $links='<label  class="ClsProcessSalary" alt-empname="'.$row_data['empname'].'" data-toggle="modal" data-target="#myModal" alt="'.$row_data['id'].'" altpayroll_id="'.$_payroll_id.'" alt-salary="'.$payable_salary.'" 
                        style="cursor:pointer;text-decoration: underline;color:blue;">Process</label>';
            }
            $qry="Select trans_id from 0_kv_empl_payout_details where payslip_ref_id='".$_payroll_id."'
                  and empl_id='".$row_data['id']."'";
            $res_d=db_query($qry);
            $comma_gl_ids=db_fetch($res_d);

            $trans_ids=explode(",",$comma_gl_ids[0]);

            for($i=0;$i<=sizeof($trans_ids);$i++)
            {
                if($trans_ids[$i]!=0)
                {
                    $gl_links.='<a target="_blank" href="ERP/gl/view/gl_trans_view.php?type_id=0&amp;trans_no='.$trans_ids[$i].'" 
                          onclick="javascript:openWindow(this.href,this.target); return false;" accesskey="V"><u>'.$trans_ids[$i].'</u></a>'.' , ';
                }

            }




            $data[] = array(
                $row_data['empl_id'],
                $row_data['empname'],
                $row_data['tot_salary_payable'],
                $row_data['processed_salary'],
                $gl_links,
                $links,

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


    public function view_payslip()
    {

        include('../../config.php');
        $alt_empl_id=$_POST['alt_empl_id'];
        $pay_roll_id=$_POST['payroll_id'];
        $url=$base_url."ERP/company/0/images/pdf-header-top.jpg";
        $url_footer=$base_url."ERP/company/0/images/payslip_footer.jpg";

        $qry="Select pay_year,pay_month From 0_kv_empl_payroll_master Where id='".$pay_roll_id."'";
        $result_d = db_query($qry, "Can't get your results");
        $payslip_data= db_fetch($result_d);

        $MonthName=date(F, mktime(0, 0, 0, $payslip_data[1], 10));

        /****************************GET Employee Basic Details************************/
        $sql="SELECT a.id,a.empl_id,CONCAT(a.empl_firstname,\" \",a.empl_lastname) AS Emp_name
            ,a.mobile_phone,a.email,b.empl_id,d.description,e.description,b.joining,b.department
            FROM 0_kv_empl_info AS a
            INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
            INNER JOIN 0_kv_empl_designation AS d ON d.id=b.desig
            INNER JOIN 0_kv_empl_departments AS e ON e.id=b.department
            WHERE a.id='".$alt_empl_id."'";

        $result = db_query($sql, "Can't get your results");
        $employe_Data= db_fetch($result);
        /**************************************END**************************************/
        /**************************************GET Employee Earnings********************/
        $qry="SELECT p.element_name,a.amount,p.`type` AS acc_type,m.created_on,m.payslip_id,b.days_worked
            ,b.net_commission,b.processed_salary,b.tot_salary_payable,b.commission,b.leave_absent_deduction
            ,b.late_coming_deduction_minutes
            FROM 0_kv_empl_payroll_elements AS a
            INNER JOIN 0_kv_empl_payroll_details AS b ON b.id=a.payslip_detail_id
            INNER JOIN 0_kv_empl_pay_elements AS p ON p.id=a.pay_element
            INNER JOIN 0_kv_empl_payroll_master AS m ON m.id=b.payslip_id
            WHERE b.empl_id='".$alt_empl_id."' AND m.pay_year='".$payslip_data[0]."' AND m.pay_month='".$payslip_data[1]."' and p.`type`='1'
            ORDER BY  p.element_name ASC";



        $result_sql = db_query($qry, "Can't get your results");
        $payslip_no=db_fetch(db_query($qry));


        $qry_ded="SELECT p.element_name,a.amount,p.`type` AS acc_type,m.created_on,m.payslip_id,b.days_worked,b.net_commission,b.processed_salary
            FROM 0_kv_empl_payroll_elements AS a
            INNER JOIN 0_kv_empl_payroll_details AS b ON b.id=a.payslip_detail_id
            INNER JOIN 0_kv_empl_pay_elements AS p ON p.id=a.pay_element
            INNER JOIN 0_kv_empl_payroll_master AS m ON m.id=b.payslip_id
            WHERE b.empl_id='".$alt_empl_id."' AND m.pay_year='".$payslip_data[0]."' AND m.pay_month='".$payslip_data[1]."' and p.`type`='2'
            ORDER BY  p.element_name ASC";
        $result_sql_ded = db_query($qry_ded, "Can't get your results");

        /**********************************************END******************************/
        $sql_report="SELECT empl_firstname FROM 
                    0_kv_empl_info
                    WHERE id IN (SELECT report_to FROM 0_kv_empl_info WHERE id='".$alt_empl_id."')";
        $report_name= db_fetch(db_query($sql_report, "Can't get your results"));

        $sql_head="SELECT CONCAT(a.empl_firstname,' ',a.empl_lastname) AS empname
                    FROM 0_kv_empl_info AS a
                    INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                    WHERE b.head_of_dept='1' AND b.department='".$employe_Data['department']."'";

        $head_name= db_fetch(db_query($sql_head, "Can't get your results"));


        /*$qry_resu="Select processed_salary From 0_kv_empl_payroll_details
                    Where payslip_id='".$pay_roll_id."' and empl_id='".$alt_empl_id."'";

        $result_des = db_query($qry_resu);
        $payslip_data_res= db_fetch($result_des);

        $net_pay=$payslip_data_res[0];*/



        $html="<div>
            <img src='".$url."' style='width:100%;'/>
               <div align=\"center\" style=\"border: 1px solid black;height: 22px;padding: 1%;\">SALARY SLIP OF THE MONTH ".strtoupper($MonthName).'-'.$year."</div>";
        $html.="<table width=\"944\" style=\"height: 566px;border:0px solid black;margin-top: 2%;\">
                    <tbody>
                  
                    <tr style=\"height: 21px;\">
                    <td style=\"height: 21px;\">Name</td>
                    <td style=\"height: 21px;\">:</td>
                    <td style=\"height: 21px;\">".$employe_Data['2']."</td>
                     <td style=\"height: 21px;\">Employee No.</td>
                    <td style=\"height: 21px;\">:</td>
                    <td style=\"height: 21px;\">".$employe_Data['1']."</td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    </tr>
                    <tr style=\"height: 21px;\">
                    <td style=\"height: 21px;\">Join Date</td>
                    <td style=\"height: 21px;\">:</td>
                    <td style=\"height: 21px;\">".$employe_Data['8']."</td>
                    <td style=\"height: 21px;\">Working Days</td>
                    <td style=\"height: 21px;\">:</td>
                    <td style=\"height: 21px;\">".$payslip_no['days_worked']."</td>
                    
                    </tr>
                    <tr style=\"height: 21px;\">
                    <td style=\"height: 21px;\">Designation</td>
                    <td style=\"height: 21px;\">:</td>
                    <td style=\"height: 21px;\">".$employe_Data['6']."</td>
                    <td style=\"height: 21px;\">Department</td>
                    <td style=\"height: 21px;\">:</td>
                    <td style=\"height: 21px;\">".$employe_Data['7']."</td>
                    <td style=\"height: 21px;\"></td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    </tr>

                    <tr style=\"height: 21px;\">
                    <td style=\"height: 21px;\">Line Manager</td>
                    <td style=\"height: 21px;\">:</td>
                    <td style=\"height: 21px;\">".$report_name['0']."</td>
                    <td style=\"height: 21px;\">Head Of Dept.</td>
                    <td style=\"height: 21px;\">:</td>
                    <td style=\"height: 21px;\">".$head_name['0']."</td>
                    <td style=\"height: 21px;\"></td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    </tr>
                 
                    <tr style=\"height:7px;\"></tr>
                    <tr style=\"height: 21px;\">
                    <td colspan=\"3\" style=\"height: 21px;border-bottom: 1px solid black;font-weight:bold;\">Salary Details</td>
                    <td colspan=\"3\" style=\"height: 21px;border-bottom: 1px solid black;font-weight:bold;\">Deductions</td>
                    
                    </tr>
                    ";

        $sum_earnings=array();
        $sum_deduction=array();
        $payslip_date='';
        $net_pay='';




        $html.=" <tr style=\"height: 21px;\">
                    <td colspan=\"3\"><div style='margin-top: -3%;'><table style=\"width:50%;\">";

        while ($emp_allownce_data = db_fetch_assoc($result_sql)) {
            if($emp_allownce_data['acc_type']=='1') {
                $html .= " 
                        <tr style=\"height: 21px;\">
                         <td style=\"height: 21px;\">" . $emp_allownce_data['element_name'] . "</td>
                    <td style=\"height: 21px;\">:</td>
              
                    <td style=\"height: 21px;\">" . $emp_allownce_data['amount'] . "</td>
                         </tr>
                         ";
                array_push($sum_earnings,$emp_allownce_data['amount']);
                $payslip_date=strtotime($emp_allownce_data['created_on']);
            }
        }
        $html.=" </table></div></td><td colspan=\"3\"> 
                        <div style='margin-top: -4%;'><table style=\"width:50%;\">";

        while ($emp_allownce_data_ded = db_fetch_assoc($result_sql_ded)) {
            if($emp_allownce_data_ded['acc_type']=='2') {
                $html .= "<tr style=\"height: 21px;\">
                         <td style=\"height: 21px;\">" . $emp_allownce_data_ded['element_name'] . "</td>
                    <td style=\"height: 21px;\">:</td>
                    <td style=\"height: 21px;\">" . $emp_allownce_data_ded['amount'] . "</td>
                         </tr>
                       ";
                array_push($sum_deduction,$emp_allownce_data_ded['amount']);
            }
        }
        $html.=" </table> </div>    </td>";

        $html.="</tr>";

        $net_pay=$payslip_no['tot_salary_payable'];
        $late_come_and_leave_deduction=$payslip_no['leave_absent_deduction']+$payslip_no['late_coming_deduction_minutes'];
        $total_deduction=array_sum($sum_deduction)+$late_come_and_leave_deduction;
        $total_salary=array_sum($sum_earnings)+$payslip_no['commission'];



        $html.="<tr style=\"height: 21px;\">
                    <td style=\"height: 21px;\" ><b>Commission</b></td><td style=\"height: 21px;\">:</td>
                    <td style=\"height: 21px;border: 0px solid black;\">".$payslip_no['commission']."</td>
                    
                    <td style=\"height: 21px;\" ><b>Late Coming </br>& Leave Deduction</b></td><td style=\"height: 21px;\">:</td>
                    <td style=\"height: 21px;border: 0px solid black;\">".$late_come_and_leave_deduction."</td>
                     
                     
                    </tr>";

        // $number_to_text=$this->numberTowords($net_pay);
        $html.="<tr style=\"height: 21px;\">
                    <td style=\"height: 21px;border-bottom: 1px solid black;\" ><b>Salary Total</b></td>
                    <td style=\"height: 21px;border: 0px solid black;\">&nbsp;</td>
                    <!--td style=\"height: 21px;border: 0px solid black;\">&nbsp;</td-->
                    <td style=\"height: 21px;border-bottom: 1px solid black;\"><b>".$total_salary."</b></td>
                    <!--<td style=\"height: 21px;border: 0px solid black;\">&nbsp;</td>-->
                    
                    
                    
                    
                    <td style=\"height: 21px;border-bottom: 1px solid black;\" ><b>Deduction Total</b></td>
                    <td style=\"height: 21px;border: 0px solid black;\">&nbsp;</td>
                    <td style=\"height: 21px;border-bottom: 1px solid black;\"><b>".$total_deduction."</b></td>
                     
                    </tr>
                    
                    <tr style=\"height: 21px;\">
                    <td style=\"height: 21px;\">Payslip Date</td>
                    <td style=\"height: 21px;\">:</td>
                    <td style=\"height: 21px;\">".date("jS F, Y", $payslip_date)."</td>
                    <td colspan=\"4\" style=\"height: 21px;\"><b>NET PAY-/</b></td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    </tr>
                     <tr style=\"height: 21px;\">
                    <td style=\"height: 21px;\"></td>
                    <td style=\"height: 21px;\"></td>
                    <td style=\"height: 21px;\"></td>
                    <td colspan=\"4\" style=\"height: 21px;\"><b>".number_format($net_pay,2)."</b></td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    </tr> 
                    <!--tr style=\"height: 21px;\">
                    <td style=\"height: 21px;\"></td>
                    <td style=\"height: 21px;\"></td>
                    <td style=\"height: 21px;\"></td>
                    <td colspan=\"4\" rowspan=\"2\" style=\"height: 42px;\"><b>".$number_to_text."</b></td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    </tr-->
                   <tr style=\"height: 21px;\">
                    <td style=\"height: 21px;\"></td>
                    <td style=\"height: 21px;\"></td>
                    <td style=\"height: 21px;\"></td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    </tr> 
                     <tr style=\"height: 21px;\">
                    <td style=\"height: 21px;\">&nbsp;</td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    <td style=\"height: 21px;\">&nbsp;</td>
                    </tr> 
                    
                    </tbody>
</table>

 <img src='".$url_footer."' style='width:100%;margin-top:120px;;'/>
</div>";


        return AxisPro::SendResponse(['status' => 'OK', 'msg' => $html]);
    }


    function get_from_subacc()
    {
        $ledger_id=$_POST['from_account'];
        $id=$_POST['id'];
        $sql="SELECT code,name
              FROM 0_sub_ledgers where ledger_id='".$ledger_id."' ";
        $result = db_query($sql);
        $return_result = [];
        $select='<select class="form-control" id="ddl_from_sub_'.$id.'" style="width: 188px;"><option value="0">---Select Sub Ledger---</option>';
        while ($myrow = db_fetch($result)) {
            $select.='<option value="'.$myrow['code'].'">'.$myrow['name'].'</option>';
        }
        $select.='</select>';

        return AxisPro::SendResponse($select);
    }


    function get_to_subacc()
    {
        $ledger_id=$_POST['to_account'];
        $id=$_POST['id'];
        $sql="SELECT code,name
              FROM 0_sub_ledgers where ledger_id='".$ledger_id."' ";
        $result = db_query($sql);
        $return_result = [];
        $select='<select class="form-control"  id="ddl_to_sub_'.$id.'" style="width: 188px;"><option value="0">---Select Sub Ledger---</option>';
        while ($myrow = db_fetch($result)) {
            $select.='<option value="'.$myrow['code'].'">'.$myrow['name'].'</option>';
        }
        $select.='</select>';

        return AxisPro::SendResponse($select);
    }


    public function post_multi_gl()
    {

        $gl_data=$_POST['gl_data'];
        $accounts_arra=$_POST['accounts'];
        $Refs = new references();

        $amount_to_gl='';
        $account='';
        $trans_id='';
        for($i=0;$i<sizeof($gl_data);$i++)
        {
            $ref = $Refs->get_next(ST_JOURNAL, null, Today());
            $trans_type = 0;
            $total_gl = 0;
            $trans_id = get_next_trans_no(0);

            //$jv_date=date('d-M-Y',strtotime($gl_data[$i]['jv_date']));
            $jv_date=date('d/m/Y',strtotime($gl_data[$i]['jv_date']));



            $amount_sum='0';
            foreach($gl_data[$i]['accounts'] as $value)
            {
                if(isset($value['jv_from']))
                {
                    $amount_to_gl_dbt=$value['amount'];
                    $account_dbt=$value['jv_from'];

                    add_gl_trans($trans_type, $trans_id,$jv_date,$account_dbt, 0, 0,
                        $value['from_memo'],$amount_to_gl_dbt, 'AED', "", 0, "", 0);

                    $amount_sum=$amount_sum+$amount_to_gl_dbt;

                    $gl_counter = db_insert_id();
                    $sql = "UPDATE 0_gl_trans SET axispro_subledger_code ='".$value['jv_from_sub']."' WHERE counter = $gl_counter";
                    db_query($sql);

                    if($value['tax_option']==1)
                    {
                        $tax_amount=$value['amount']*5/100;
                        add_gl_trans($trans_type, $trans_id,$jv_date,$gl_data[$i]['tax_account'], 0, 0,
                            '',$tax_amount, 'AED', "", 0, "", 0);
                    }
                }
                if(isset($value['jv_to']))
                {
                    if($value['tax_option']==1) {
                        $tax_amount = $value['amount'] * 5 / 100;
                        $t=$value['amount']+$tax_amount;
                        $amount_to_gl_crdt='-'.$t;
                    }
                    else
                    {
                        $amount_to_gl_crdt='-'.$value['amount'];
                    }

                    $account_crdt=$value['jv_to'];

                    add_gl_trans($trans_type, $trans_id,$jv_date,$account_crdt, 0, 0,
                        $value['to_memo'],$amount_to_gl_crdt, 'AED', "", 0, "", 0);
                    $gl_counter = db_insert_id();
                    $sql = "UPDATE 0_gl_trans SET axispro_subledger_code ='".$value['jv_from_to']."' WHERE counter = $gl_counter";
                    db_query($sql);
                }


            }



            $sql = "INSERT INTO ".TB_PREF."journal(`type`,`trans_no`, `amount`, `currency`, `rate`, `reference`, `source_ref`, `tran_date`,
                              `event_date`, `doc_date`)
                              VALUES("
                .db_escape($trans_type).","
                .db_escape($trans_id).","
                .db_escape(round($amount_sum)).",'AED',"
                .db_escape(1).","
                .db_escape($ref).",'',"
                ."'".date('Y-m-d')."',"
                ."'".date('Y-m-d')."','')";

            db_query($sql);

            $Refs->save($trans_type, $trans_id, $gl_data[$i]['jv_no']);
            add_comments($trans_type, $trans_id, $jv_date,$gl_data[$i]['memo']);
            add_audit_trail($trans_type, $trans_id, $jv_date);



        }

        return AxisPro::SendResponse('Success');
    }


    public function check_refencenumber()
    {
        $ref_number=$_POST['refn_number'];
        $sql = "SELECT reference FROM 0_refs WHERE reference='".$ref_number."' and type='0'";
        $data=db_fetch(db_query($sql));

        if($data['reference']=='')
        {
            return AxisPro::SendResponse(0);
        }
        else
        {
            return AxisPro::SendResponse(1);
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


    public function list_gl_entries()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $payroll_ref_id=$_POST['id'];
        if(isset($payroll_ref_id))
        {

            $sql_get="SELECT id from 0_kv_empl_payroll_master where payslip_id='".trim($payroll_ref_id)."'";
            $payroll_pk_id=db_fetch(db_query($sql_get));


            if($payroll_pk_id[0]!='')
            {
                $sql="SELECT DISTINCT g.empl_id,CONCAT(g.empl_firstname,'',g.empl_lastname) AS empname,a.emp_id
                    FROM 0_kv_empl_temp_gl AS a
                    INNER JOIN 0_kv_empl_info AS g ON a.emp_id=g.id
                    WHERE a.payroll_ref_id='".$payroll_pk_id[0]."' 
                    LIMIT  ".$start.",".$length."  ";


                $res=db_query($sql);
                while ($gl_data = db_fetch($res)) {

                    /* $qry="SELECT e.account_name AS receiva,b.amount,f.account_name
                             FROM 0_kv_empl_temp_gl AS b
                             left JOIN 0_sys_prefs AS c ON c.`name`=b.`account` AND b.from_account=''
                             left JOIN 0_chart_master AS e ON e.account_code=c.`value`
                             LEFT JOIN 0_chart_master AS f ON f.account_code=b.from_account
                             WHERE b.payroll_ref_id='".$payroll_ref_id."' and b.emp_id='".$gl_data['2']."'
                             AND b.entry_type='2'";*/

                    $qry="SELECT e.account_name AS receiva,b.amount,f.account_name,b.gl_flag
                        FROM 0_kv_empl_temp_gl AS b
                        left JOIN 0_sys_prefs AS c ON c.`name`=b.`account`  
                        left JOIN 0_chart_master AS e ON e.account_code=c.`value`
                        LEFT JOIN 0_chart_master AS f ON f.account_code=b.account
                        WHERE b.payroll_ref_id='".$payroll_pk_id[0]."' and b.emp_id='".$gl_data['2']."'
                         ";

                    //  echo $qry.'---';
                    $qry_data=db_query($qry);


                    $html='<table>
                          <tr>
                             <th>Account</th>
                             <th>Debit</th>
                             <th>Credit</th>
                          </tr>';
                    $debit='';
                    $credit='';
                    $gl_pass_flag='';
                    while($data_res=db_fetch($qry_data))
                    {

                        if($data_res['1'] < '0')
                        {
                            $credit=$data_res['1'];
                        }
                        else
                        {
                            $credit='';
                        }
                        if($data_res['1'] > '0')
                        {
                            $debit=$data_res['1'];
                        }
                        else
                        {
                            $debit='';
                        }
                        if($data_res['0']=='')
                        {
                            $data_res['0']=$data_res['2'];
                        }

                        $html.='<tr>
                            <td>'.$data_res['0'].'</td>
                            <td>'.$debit.'</td>
                            <td>'.$credit.'</td>
                          </tr>
                          ';
                        if($data_res['gl_flag']=='1')
                        {
                            $gl_pass_flag='<label style="color:green;font-weight:bold;">GL Entry Passed</label>';
                        }
                        else
                        {
                            $gl_pass_flag='';
                        }

                    }

                    $html.='</table>';

                    $data[] = array(
                        $gl_data['1'].' ('.$gl_data['0'].' )',
                        $html,
                        $gl_pass_flag
                    );


                }

                $result_data = array(
                    "draw" => $draw,
                    "recordsTotal" => 10,
                    "recordsFiltered" => 10,
                    "data" => $data
                );


            }
            else
            {
                $result_data = array(
                    "draw" => $draw,
                    "recordsTotal" => 10,
                    "recordsFiltered" => 10,
                    "data" => 0
                );
            }


            return AxisPro::SendResponse($result_data);

        }
    }


    public function post_gl_entries()
    {
        $payroll_ref_id=$_POST['payroll_ref_id'];
        if(isset($payroll_ref_id))
        {

            $sql_get="SELECT id from 0_kv_empl_payroll_master where payslip_id='".trim($payroll_ref_id)."'";
            $payroll_pk_id=db_fetch(db_query($sql_get));


            $qry="SELECT distinct emp_id FROM  0_kv_empl_temp_gl WHERE payroll_ref_id='".$payroll_pk_id[0]."' ";
            $qry_data=db_query($qry);

            $Refs = new references();
            $ref = $Refs->get_next(ST_JOURNAL, null, Today());
            $trans_type = 0;
            $total_gl = 0;
            $trans_id = get_next_trans_no(0);

            while($temp_rcrd_data=db_fetch($qry_data))
            {
                /*---------------PASSING GL BEFORE PAY------------*/

                $sql="SELECT b.amount,b.account,b.memo,b.sub_ledger,b.id
                    FROM 0_kv_empl_temp_gl AS b
                    WHERE b.payroll_ref_id='".$payroll_pk_id[0]."' and b.emp_id='".$temp_rcrd_data[0]."'
                    AND gl_flag=''";
                $data_res=db_query($sql);


                /***************************EMP COST CENTER***********************/
                $sql_cost="select cost_center 
                        from 0_kv_empl_job where empl_id='".$temp_rcrd_data[0]."'";
                $emp_cost_data=db_fetch(db_query($sql_cost));
                /*********************************END*****************************/

                while($data_set=db_fetch($data_res))
                {

                    $total_gl+= add_gl_trans($trans_type, $trans_id, Today(),$data_set['account'], $emp_cost_data[0], 0,
                        $data_set['memo'],$data_set['amount'], 'AED', "", 0, "", 0);

                    if($data_set['sub_ledger']!='0')
                    {
                        $gl_counter_advance = db_insert_id();
                        $sql = "UPDATE 0_gl_trans SET axispro_subledger_code ='".$data_set['sub_ledger']."'
                                ,created_by='" . $_SESSION['wa_current_user']->user . "' WHERE counter = $gl_counter_advance";
                        db_query($sql);
                    }

                    if($trans_id)
                    {
                        $update_gl_id="UPDATE 0_kv_empl_payroll_details
                                           SET gl_trans_id='".$trans_id."',trans_date='".Today()."' 
                                           WHERE  payslip_id='".$payroll_pk_id[0]."' AND empl_id='".$temp_rcrd_data['emp_id']."' ";
                        db_query($update_gl_id);

                    }


                    $up_qry="UPDATE 0_kv_empl_temp_gl SET gl_flag='1' WHERE id='".$data_set['id']."' ";
                    db_query($up_qry);


                }


                /************************************UPDATE deduction details of LOAN and Warnings******/

                $QRY="SELECT id FROM 0_kv_empl_payroll_details
                          WHERE payslip_id='".$payroll_pk_id[0]."' AND empl_id='".$temp_rcrd_data['emp_id']."'";
                $res_data=db_fetch(db_query($QRY));

                if($res_data[0])
                {
                    $sql_pay="select war_ded_desc FROM 0_kv_empl_payroll_elements
                                  where payslip_detail_id='".$res_data[0]."' and war_ded_desc!='0'";
                    $details_res=db_query($sql_pay);
                    $decode_war_ded='';
                    while($detail_data=db_fetch($details_res))
                    {
                        if(!empty($detail_data[0]))
                        {
                            $decode_war_ded=json_decode($detail_data[0]);

                            for($i=0;$i<sizeof($decode_war_ded);$i++)
                            {
                                $updae_sql="update 0_kv_empl_warings set deducted_amount=`deducted_amount`+'".$decode_war_ded[$i]->amount."'
                                                where id='".$decode_war_ded[$i]->pk_id."' ";
                                if(db_query($updae_sql))
                                {
                                    $sql_insert="Insert into 0_kv_empl_warning_ded_details (warning_id,amount,payroll_id,created_date,created_by)
                                             values ('".$decode_war_ded[$i]->pk_id."','".$decode_war_ded[$i]->amount."'
                                             ,'".$payroll_pk_id[0]."','".date('Y-m-d h:i:s')."','".$_SESSION['wa_current_user']->user."')";
                                    db_query($sql_insert);
                                }

                            }
                        }
                    }

                    /***********************LOAN DED SAVING**********/

                    $sql_loan_pay="select loan_ded_desc FROM 0_kv_empl_payroll_elements
                                  where payslip_detail_id='".$res_data[0]."' and loan_ded_desc!='0'";
                    $details_res_loan=db_query($sql_loan_pay);
                    $decode_loan_ded='';
                    while($detail_data_loan=db_fetch($details_res_loan))
                    {
                        if(!empty($detail_data_loan[0]))
                        {
                            $decode_loan_ded=json_decode($detail_data_loan[0]);

                            for($i=0;$i<sizeof($decode_loan_ded);$i++)
                            {
                                $updae_sql="update 0_kv_empl_loan set periods_paid=`periods_paid`+'1'
                                            where id='".$decode_loan_ded[$i]->pk_id."' ";
                                if(db_query($updae_sql))
                                {
                                    $sql_loan_details="Insert into 0_kv_empl_loan_deduction_details 
                                                       (loan_id,deducted_amount,payroll_id,created_date,created_by) values('".$decode_loan_ded[$i]->pk_id."','".$decode_loan_ded[$i]->loan_amnt."','".$payroll_pk_id[0]."',
                                                       '".date('Y-m-d')."','".$_SESSION['wa_current_user']->user."')";
                                    db_query($sql_loan_details);
                                }


                            }
                        }
                    }



                }

            }

            $sql = "INSERT INTO ".TB_PREF."journal(`type`,`trans_no`, `amount`, `currency`, `rate`, `reference`, `source_ref`, `tran_date`,
                              `event_date`, `doc_date`)
                              VALUES("
                .db_escape($trans_type).","
                .db_escape($trans_id).","
                .db_escape(round(str_replace(",","",0))).",'AED',"
                .db_escape(1).","
                .db_escape($ref).",'',"
                ."'".date('Y-m-d')."',"
                ."'".date('Y-m-d')."','')";
            db_query($sql);
            /*----END-------*/
            $memo= 'SALARY GL ENTRY AGAINST THE PAYROLL REF NO :'.trim($payroll_ref_id);
            $Refs->save($trans_type, $trans_id, $ref);

            add_comments($trans_type, $trans_id, Today(), $memo);
            add_audit_trail($trans_type, $trans_id, Today());


            $msg=['status' => 'OK', 'msg' =>"Successfully Saved"];
            return AxisPro::SendResponse($msg);
            /*-----------------------------------END----------------*/

        }
    }


    public function get_pay_bank_accounts()
    {
        $sql="SELECT  account_code as glcode,bank_account_name from 0_bank_accounts";
        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result);
    }



    public function export_time_sheet()
    {

        $Fdate=$_POST['frmDate'];
        $Tdate=$_POST['toDate'];
        $dept_id=$_POST['ddl_department'];
        $emp_id=$_POST['ddl_employees'];

        $filename = 'Timesheet_'.date('Ymd').'.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");


        $file = fopen('php://output', 'w');
        $header = array("Emp-Code",
            "Emp.Name",
            "Date",
            "Punch In",
            "Punch Out",
            "Shift"
        );

        fputcsv($file, $header);


        $Date = $this->getDatesFromRange($Fdate,$Tdate);



        for($i=0;$i<sizeof($Date);$i++)
        {

            $sql = "SELECT a.empl_id as employeeId,a.empl_firstname as Employeename,a.id as emp_pk_id
                          FROM 0_kv_empl_info as a 
                          INNER JOIN 0_kv_empl_job as b ON a.id=b.empl_id
                          WHERE b.department='".$dept_id."'";
            if($emp_id!='0')
            {
                $sql.= " AND a.empl_id='".$emp_id."' ";
            }

            $result = db_query($sql);



            $data=[];
            $k=0;
            $in_time='';
            $out_time='';
            $shift_desc='';

            while($data_res=db_fetch($result))
            {

                $attendence=$this->getAttendenceCount($Date[$i],$data_res['employeeId']);
                $shift_res_data=$this->get_shift_from_day($Date[$i],$data_res['emp_pk_id']);


                if(isset($attendence['in_time']))
                {
                    $in_time=date('h:i:s A',strtotime($attendence['in_time']));
                }

                if(isset($attendence[4]))
                {
                    $out_time=date('h:i:s A',strtotime($attendence[4]));
                }

                if(isset($shift_res_data[2]))
                {
                    $shift_desc='Shift : '.$shift_res_data[2];
                }
                else
                {
                    $shift_desc='Shift : Not Assigned';
                }

                if($in_time==$out_time)
                {
                    $in_time='Missed Punchin/Punch Out';
                    $out_time='Missed Punchin/Punch Out';
                }


                $data[] = array(
                    $data_res['employeeId'],
                    $data_res['Employeename'],
                    $Date[$i],
                    $in_time,
                    $out_time,
                    $shift_desc
                );


                fputcsv($file,$data[$k]);
                $k++;

            }

        }

        fclose($file);

    }

    public function get_payroll_cutoff_date($from_date,$to_date,$payroll_month)
    {
        $from=explode('-',$from_date);
        $to=explode('-',$to_date);
        $month=$payroll_month;
        $year=$from['Y'];


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


        if($cutoff_day!='' || $cutoff_day!='0')
        {
            $start_month=$from[1];
            $monthNum = $start_month;


            if($monthNum=='1')
            {
                $cutoff_month='12';
            }
            else
            {
                $cutoff_month=$monthNum-1;
            }


            if($month=='01')
            {
              //  $year=$year-1;
            }

            $from_date=$cutoff_day;

            if($month=='01')
            {
               // $year=$year+1;
            }

            if($cutoff_day=='1')
            {
                $cutoff_day=cal_days_in_month(CAL_GREGORIAN, $cutoff_month, $year);
                $to_date=$cutoff_day;
            }
            else
            {
                $cutoff_day=$cutoff_day-1;
                $to_date=$cutoff_day;
            }

        }

        return array("From"=>$from_date,"To"=>$to_date);


    }


}