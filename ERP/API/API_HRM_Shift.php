<?php
include_once("AxisPro.php");
include_once("PrepareQuery.php");

Class API_HRM_Shift
{
    /*----------------------PREPARE TIMESHEET---------------*/

    public function list_shift_prepare()
    {
        $dept_id=$_POST['dept_id'];
        $filter_shift_id=$_POST['shift_id'];
        $Fdate=$_POST['frmDate'];
        $Tdate=$_POST['toDate'];

        include_once($path_to_root . "/API/API_HRM_Call.php");
        $return=new API_HRM_Call();
        $shift_data=$return->getShifts();



          
                                          

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

            $sql = "SELECT a.id,a.empl_id,CONCAT(a.empl_firstname,\" \",a.empl_lastname) AS Employeename,a.mobile_phone,a.email,b.shift
                FROM 0_kv_empl_info AS a
                INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                WHERE b.department='".$_POST['dept_id']."' and a.status='1' ";

            if($_POST['emp_id']!='0')
            {
                $sql.= " AND a.empl_id='".$_POST['emp_id']."' ";
            }
  // $sql.= " LIMIT $start_from, $limit";
            $result = db_query($sql);
 
            $table="<table   class=\"table\" id='tblShift'>
                       <thead>
                       <tr>
                        <!--<th colspan='".sizeof($Date)."'style='text-align: center;font-size: 17px;background-color: #d0d085;'>Morning Shift ( 09:00:00 - 18:00:00 )</th>-->
                       </tr>
                       <tr style=' background: #1C6EA4;
  background: -moz-linear-gradient(top, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
  background: -webkit-linear-gradient(top, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
  background: linear-gradient(to bottom, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
  border-bottom: 2px solid #444444;color:#fff;'>
                      <th ></th>
                      <th >Empname</th>
                     ";

            for($i=0;$i<sizeof($Date);$i++)
            {
                $weakend=$this->isWeekend($Date[$i]);
                $color=$this->getColorCodes($weakend);
                /*if($weakend=='1')
                {
                    $disp_head='Fri.';
                }
                else
                {*/
                    $disp_head=date('d/m/Y',strtotime($Date[$i]));
                //}

                $table.="<th $color >".$disp_head."</th>";
            }
            $table.=" </tr></thead>
                      <tbody>";


            /*------------------------------SHIFT LIST-------------------*/
              $option='';
              /*foreach($shift_data as $s){
                 $option.='
                 <option value="'.$s['id'].'">'.$s['description'].'</option>';
              }*/
                                            
            /*-------------------------------END--------------------------*/

            $i=0;
            $checked='';
            while ($myrow = db_fetch($result)) {

                $sql_chk="Select id,s_date,shift_id FROM 0_kv_empl_shiftdetails  Where 
                      empl_id='".$myrow['id']."' AND `from`>='".date('Y-m-d',strtotime($Fdate))."'
                      AND `to`<='".date('Y-m-d',strtotime($Tdate))."' ";

                      if($filter_shift_id<>0)
                      {
                        $sql_chk.=" AND shift_id='".$filter_shift_id."' ";
                      }
                    //echo $sql_chk.' -----';
                $res=db_query($sql_chk);
                $row_data = db_fetch($res);

                if($row_data['id']!='') {
                    $checked='checked="checked"';
                    ///$cls_color='selected_color';
                }
                else {$checked='';}



                $table.="<tr>";
                $checkbox='<input type="checkbox" class="chkEmp_select" name="chkEmp_select" al_tot_cnt="'.sizeof($Date).'" 
                value="'.$myrow['id'].'" alt='.$i.' '.$checked.'/>';

                    $table.="<td style='border: 1px solid #ccc;    width:3%;vertical-align : middle;text-align:center;font-weight: bold;'>".$checkbox."</td>
                       ";

                    $table.="<td style='border: 1px solid #ccc;'>".$myrow['empl_id'].' - '.$myrow['Employeename']."</td>
                       ";

                for($i=0;$i<sizeof($Date);$i++)
                {
                    $assigned_color=$this->checkShiftIsAssigned($Date[$i],$row_data['shift_id'],$myrow['id'],$dept_id,$filter_shift_id);
                      
                    if($filter_shift_id<>0)
                    {
                      $shift_validation=$this->validateShift($Date[$i],$filter_shift_id,$myrow['id']);

                      $disable_shift='';
                      if($shift_validation[0]!='')
                      {
                        $disable_shift=' opacity: 0.6;pointer-events: none;';
                      }  
                    }

                    
 
                    
                    $table.="<td style='".$disable_shift."border: 1px solid #ccc;".$assigned_color[0]."' class='ClsShiftRow' 
                     id='td_sift_id_".$myrow['id']."_".$i."' alt='".$Date[$i]."'> ";
                       
                    $table.="<select id='ddl_sift_id_".$myrow['id'].'_'.$i."'> <option value=''>---CHOOSE SHIFT---</option>";
                    $selected='';
                    foreach($shift_data as $s){
                      if($s['id']==$assigned_color[1])
                      {
                        $selected='selected="selected"';
                      }
                      else
                      {
                        $selected='';
                      }
                       $table.='<option value="'.$s['id'].'"  '.$selected.'>'.$s['description'].'</option>';
                     
                       
                    }
                     $off_day='';
                    if($assigned_color[1]=='777')
                    {
                      $off_day='selected="selected"';
                    } 
                    $table.=" <option value='777' ".$off_day." >OFF DAY</option></select>";     
                                
                    $table.=" </td>
                       ";


                }

                $table.="</tr>";
                $i++;
            }
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

    function validateShift($shift_date,$filter_shift_id,$empl_id)
    {
       $sql="select id from 0_kv_empl_shiftdetails 
       where empl_id='".$empl_id."' and s_date='".$shift_date."' AND shift_id !='".$filter_shift_id."' ";
       $result = db_query($sql);
        return db_fetch($result);
    }


    function getIsInShift($from,$emp_id,$shift_id,$dept_id)
    {
        $sql="select id from 0_kv_empl_shiftdetails 
              where dept_id='".$dept_id."' and empl_id='".$emp_id."' and shift_id='".$shift_id."' and DATE(s_date)='".date('Y-m-d',strtotime($from))."'";
       //echo $sql.'----';
        $result = db_query($sql);
        return db_fetch($result);
    }

    function getColorCodes($code)
    {
        $font_color='';
       if($code=='p')
       {
           $font_color='style="color:green;font-weight:bold;border: 1px solid #ccc;"';
       }

       if($code=='wl' || $code=='a' || $code=='sl')
       {
           $font_color='style="color:red;font-weight:bold;border: 1px solid #ccc;"';
       }

       if($code=='cl')
       {
           $font_color='style="color:blue;font-weight:bold;border: 1px solid #ccc;"';
       }

       if($code=='1')
       {
           $font_color='style="background-color: #a55353;color: white;border: 1px solid #ccc;"';
       }

       if(empty($font_color))
       {
           $font_color='style="border: 1px solid #ccc;"';
       }
       return $font_color;
    }

    function isWeekend($date) {
        return (date('N', strtotime($date)) ==5);
    }

    public function checkShiftIsAssigned($shift_date,$shift_id,$empl_id,$dept_id,$filter_shift_id)
    {
        $sql="SELECT id,shift_id
              FROM 0_kv_empl_shiftdetails where dept_id='".$dept_id."' 
              AND s_date='".$shift_date."'
              AND empl_id='".$empl_id."'";
         if($filter_shift_id<>0)
         {
          $sql.=" AND shift_id='".$filter_shift_id."' ";
         }     
     // echo $sql.'------';
        $result = db_query($sql);
        $myrow = db_fetch($result);
        $class='';
        if($myrow['id']!='')
        {
            $qry="select shift_color from 0_kv_empl_shifts where id='".$myrow['shift_id']."' ";
            $result_data = db_query($qry);
            $myrow_data = db_fetch($result_data);

           //echo $qry.'------';
            

            $class='background-color:'.$myrow_data['shift_color'].'';
        }
        else
        {
            $class='';
        }
        //echo $myrow['id'].'-------';
        return array($class,$myrow['shift_id']);
    }


    public function assign_shift_to_employee()
    {
      
        $dept_id=$_POST['dept_id'];
        $selected_shift=$_POST['shift_id'];
        $shift_start_date=date('Y-m-d',strtotime($_POST['shift_start_date']));
        $shift_end_date=date('Y-m-d',strtotime($_POST['shift_end_date']));
        $cnter='0';

      /*$sql_save="Insert Into 0_kv_empl_shift_master (shift_id,dept_id,start_date,end_date)
                   values ('".$selected_shift."','".$dept_id."','".$shift_start_date."','".$shift_end_date."')";
      if(db_query($sql_save))
      {*/
          foreach($_POST['check_empids'] as $asign_data)
          {


              $sql="UPDATE 0_kv_empl_job set shift='".$selected_shift."',shift_start='".$shift_start_date."',shift_end='".$shift_end_date."'
                  WHERE empl_id='".$asign_data['Empl_id']."' ";


              db_query($sql);

              /*---------------------INSERT SHIFT DETAILS -----------------------*/

              foreach($asign_data['s_date'] as $_data)
              {

                  $ql_sel="SELECT id FROM 0_kv_empl_shiftdetails 
                     WHERE  /* shift_id='".$_data['shift_ids']."' AND */ dept_id='".$dept_id."' AND empl_id='".$asign_data['Empl_id']."'
                       AND s_date='".date('Y-m-d',strtotime($_data['dates']))."' ";

                     //echo $ql_sel.' --- ';

                  $chckRow_res=db_query($ql_sel);

               
                  if(db_num_rows($chckRow_res)>0)
                  {

                   
                      $pk_id=db_fetch($chckRow_res);
                      /*$sql_details="UPDATE 0_kv_empl_shiftdetails set unassign_date='".date('Y-m-d')."'
                                    ,unassign_by='".$_SESSION['wa_current_user']->user."',assign_status='1'
                                    WHERE id='".$pk_id[0]."'";*/
                     /* $qry="Delete From 0_kv_empl_shiftdetails  where  /*shift_id='".$_data['shift_ids']."' AND*/ /*dept_id='".$dept_id."' AND empl_id='".$asign_data['Empl_id']."'
                     AND `from`='".$shift_start_date."' AND `to`='".$shift_end_date."'";*/
                      $qry="Delete From 0_kv_empl_shiftdetails  where id='".$pk_id['id']."' ";
                      db_query($qry);


                 $sql_details="INSERT INTO 0_kv_empl_shiftdetails (shift_id,empl_id,`from`,`to`,created_at,created_by,assign_status,dept_id,s_date)
                                 VALUES ('".$_data['shift_ids']."','".$asign_data['Empl_id']."','".$shift_start_date."','".$shift_end_date."',
                                 '".date('Y-m-d h:i:s')."','".$_SESSION['wa_current_user']->user."','1','".$dept_id."','".date('Y-m-d',strtotime($_data['dates']))."')"; 

                                  //echo $sql_details.' ---'.$qry;
                  }
                  else
                  {


                      $sql_details="INSERT INTO 0_kv_empl_shiftdetails (shift_id,empl_id,`from`,`to`,created_at,created_by,assign_status,dept_id,s_date)
                                 VALUES ('".$_data['shift_ids']."','".$asign_data['Empl_id']."','".$shift_start_date."','".$shift_end_date."',
                                 '".date('Y-m-d h:i:s')."','".$_SESSION['wa_current_user']->user."','1','".$dept_id."','".date('Y-m-d',strtotime($_data['dates']))."')";
                  }

                 
                db_query($sql_details);
                
               /*if(db_query($sql_details))
                  {
                      $cnter++;
                  } */


              }

          }

          //if($cnter==$_POST['total_rows'])
          //{
              $msg=['status' => 'OK', 'msg' =>"Shift Successfully Updated"];
          //}
         // else
         // {
             // $msg=['status' => 'FAIL', 'msg' =>"Assign Shift Failed For Some Employees"];
         // }

          return AxisPro::SendResponse($msg);
     /* }*/



    }


}