<?php
include_once("AxisPro.php");
include_once("PrepareQuery.php");

Class API_HRM_Request_Flow
{
    public function list_levels()
    {
        $type_id=$_POST['type_id'];

        $sql="SELECT id, role, inactive FROM 0_security_roles order by role asc";
        $result=db_query($sql);
        $select='';
        while($role_data=db_fetch($result))
        {
            $select.='<option value="'.$role_data['id'].'">'.$role_data['role'].'</option>';
        }
        $select.='<option value="1111">Line Manager</option>
                      <option value="2222">Head Of Department</option>';


        $rows="<table id=\"myTable\" class=\" table order-list shadow w-200\" style=\"display: inline-table;\">
               <tbody>";

        for($i=1;$i<=10;$i++)
        {
            $rows.="<tr>
                     <td>
                     <select id='level_".$i."' name='level_".$i."' class='form-control'>
                        <option value='0'>--- SELECT ROLES ---</option>
                        ".$select."
                     </select>
                     </td>
                     <td><span class=\"ClsLevel\">Level ".$i."</span></td>
                    </tr>";
        }

        $rows.="</tbody>
                </table>";


        $qry="select * from 0_kv_empl_master_request_flow where type_id='".$type_id."' 
              and dim_id='".$_POST['dim_id']."' AND access_level='".$_POST['role_id']."' ";

        $qry_data=db_fetch(db_query($qry));

        if($qry_data['level_1']==''){$qry_data['level_1']=0;}
        if($qry_data['level_2']==''){$qry_data['level_2']=0;}
        if($qry_data['level_3']==''){$qry_data['level_3']=0;}
        if($qry_data['level_4']==''){$qry_data['level_4']=0;}
        if($qry_data['level_5']==''){$qry_data['level_5']=0;}
        if($qry_data['level_6']==''){$qry_data['level_6']=0;}
        if($qry_data['level_7']==''){$qry_data['level_7']=0;}
        if($qry_data['level_8']==''){$qry_data['level_8']=0;}
        if($qry_data['level_9']==''){$qry_data['level_9']=0;}
        if($qry_data['level_10']==''){$qry_data['level_10']=0;}



        $levels=array('lev_1'=>$qry_data['level_1'],'lev_2'=>$qry_data['level_2'],'lev_3'=>$qry_data['level_3'],
                     'lev_4'=>$qry_data['level_4'],'lev_5'=>$qry_data['level_5'],'lev_6'=>$qry_data['level_6'],
                     'lev_7'=>$qry_data['level_7'],'lev_8'=>$qry_data['level_8'],'lev_9'=>$qry_data['level_9'],'lev_10'=>$qry_data['level_10']);

        return AxisPro::SendResponse(['html'=>$rows,'levels'=>$levels]);
    }

    public function save_request_flow()
    {

      $sql_chk="SELECT id from 0_kv_empl_master_request_flow where type_id='".$_POST['type_id']."' 
      and dim_id='".$_POST['dim_id']."' and access_level='".$_POST['role_id']."' ";
      $data_id=db_fetch(db_query($sql_chk));

      if($data_id[0]=='')
      {
          $sql="INSERT into 0_kv_empl_master_request_flow (type_id,level_1,level_2,level_3,level_4,level_5,level_6,
           level_7,level_8,level_9,level_10,created_by,created_on,dim_id,access_level) 
           VALUES ('".$_POST['type_id']."','".$_POST['level_1']."','".$_POST['level_2']."','".$_POST['level_3']."',
           '".$_POST['level_4']."','".$_POST['level_5']."','".$_POST['level_6']."','".$_POST['level_7']."','".$_POST['level_8']."',
           '".$_POST['level_9']."','".$_POST['level_10']."','".$_SESSION['wa_current_user']->user."'
           ,'".date('Y-m-d')."','".$_POST['dim_id']."','".$_POST['role_id']."')";

      }
      else
      {
          $sql="UPDATE 0_kv_empl_master_request_flow SET level_1='".$_POST['level_1']."',level_2='".$_POST['level_2']."',
                level_3='".$_POST['level_3']."',level_4='".$_POST['level_4']."',level_5='".$_POST['level_5']."',
                level_6='".$_POST['level_6']."',level_7='".$_POST['level_7']."',level_8='".$_POST['level_8']."',
                level_9='".$_POST['level_9']."',level_10='".$_POST['level_10']."' where id='".$data_id[0]."'";


      }
      $msg='';
      if(db_query($sql))
      {
          $msg='Data Saved successful';
      }
      else
      {
          $msg='Error occured while saving data';
      }

      echo $msg;

    }


    public function approve_request()
    {
        $msg='';
        $flag='';
        if(!empty($_POST['remove_id']))
        {
            if($_POST['type']=='3')
            {
                $level_incr=$_POST['level']+1;
                $level='level_'.$level_incr;

                $sql_cur_role="select role_id,empl_id 
                               from 0_kv_empl_passport_request where id='".$_POST['remove_id']."' ";
                $curr_role_id=db_fetch(db_query($sql_cur_role));

                $empl_access=$this->get_employee_access_from_user($curr_role_id['empl_id']);

                $qry="Select ".$level." from 0_kv_empl_master_request_flow where type_id='".$_POST['type']."'
                      AND access_level='".$empl_access['role_id']."' AND dim_id='".$empl_access['dflt_dimension_id']."' ";
                $next_lev_data=db_fetch(db_query($qry));




                if($curr_role_id[0]=='17')
                {
                    $next_lev_data[0]='0';
                }


                if($next_lev_data[0]=='0')
                {
                    $sql="UPDATE 0_kv_empl_passport_request set role_id='0'
                      ,level='0',req_status='1' where id='".$_POST['remove_id']."'";
                }
                else
                {
                    $sql="UPDATE 0_kv_empl_passport_request set role_id='".$next_lev_data[0]."'
                      ,level='".$level_incr."' where id='".$_POST['remove_id']."'";
                }

                if(db_query($sql))
                {
                    $sql_his="Insert into 0_kv_empl_req_approve_history (type_id,level,request_id,approved_by,
                              approved_on) values ('".$_POST['type']."','".$_POST['level']."','".$_POST['remove_id']."',
                              '".$_SESSION['wa_current_user']->user."','".date('Y-m-d')."')";

                    if(db_query($sql_his))
                    {
                        $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                        $flag='1';
                    }

                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Error occured while saving data'];
                    $flag='0';
                }
            }

            if($_POST['type']=='2')
            {

                $level_incr=$_POST['level']+1;
                $level='level_'.$level_incr;

                $sql_cur_role="select role_id,empl_id 
                               from 0_kv_empl_certificate_request where id='".$_POST['remove_id']."' ";
                $curr_role_id=db_fetch(db_query($sql_cur_role));

                $empl_access=$this->get_employee_access_from_user($curr_role_id['empl_id']);

                $qry="Select ".$level." from 0_kv_empl_master_request_flow where type_id='".$_POST['type']."'
                      AND access_level='".$empl_access['role_id']."' AND dim_id='".$empl_access['dflt_dimension_id']."' ";
                $next_lev_data=db_fetch(db_query($qry));



                if($curr_role_id[0]=='17')
                {
                    $next_lev_data[0]='0';
                }


                if($next_lev_data[0]=='0')
                {
                    $sql="UPDATE 0_kv_empl_certificate_request set role_id='0'
                      ,level='0',req_status='1' where id='".$_POST['remove_id']."'";
                }
                else
                {
                    $sql="UPDATE 0_kv_empl_certificate_request set role_id='".$next_lev_data[0]."'
                      ,level='".$level_incr."' where id='".$_POST['remove_id']."'";
                }


                if(db_query($sql))
                {
                    $sql_his="Insert into 0_kv_empl_req_approve_history (type_id,level,request_id,approved_by,
                              approved_on) values ('".$_POST['type']."','".$_POST['level']."','".$_POST['remove_id']."',
                              '".$_SESSION['wa_current_user']->user."','".date('Y-m-d')."')";
                    db_query($sql_his);

                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                    $flag='1';
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                    $flag='0';
                }
            }

            if($_POST['type']=='5')
            {
                $level_incr=$_POST['level']+1;
                $level='level_'.$level_incr;

                $sql_cur_role="select role_id,empl_id 
                               from 0_kv_empl_noc_request where id='".$_POST['remove_id']."' ";
                $curr_role_id=db_fetch(db_query($sql_cur_role));

                $empl_access=$this->get_employee_access_from_user($curr_role_id['empl_id']);

                $qry="Select ".$level." from 0_kv_empl_master_request_flow where type_id='".$_POST['type']."'
                      AND access_level='".$empl_access['role_id']."' AND dim_id='".$empl_access['dflt_dimension_id']."' ";
                $next_lev_data=db_fetch(db_query($qry));



                if($curr_role_id[0]=='17')
                {
                    $next_lev_data[0]='0';
                }

                if($next_lev_data[0]=='0')
                {
                    $sql="UPDATE 0_kv_empl_noc_request set role_id='0'
                      ,level='0',req_status='1' where id='".$_POST['remove_id']."'";
                }
                else
                {
                    $sql="UPDATE 0_kv_empl_noc_request set role_id='".$next_lev_data[0]."'
                      ,level='".$level_incr."' where id='".$_POST['remove_id']."'";
                }

                if(db_query($sql))
                {
                    $sql_his="Insert into 0_kv_empl_req_approve_history (type_id,level,request_id,approved_by,
                              approved_on) values ('".$_POST['type']."','".$_POST['level']."','".$_POST['remove_id']."',
                              '".$_SESSION['wa_current_user']->user."','".date('Y-m-d')."')";
                    db_query($sql_his);

                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                    $flag='1';
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                    $flag='0';
                }
            }

            if($_POST['type']=='6')
            {

                $level_incr=$_POST['level']+1;
                $level='level_'.$level_incr;

                $sql_cur_role="select role_id,empl_id 
                               from 0_kv_asset_request where id='".$_POST['remove_id']."' ";
                $curr_role_id=db_fetch(db_query($sql_cur_role));

                $empl_access=$this->get_employee_access_from_user($curr_role_id['empl_id']);

                $qry="Select ".$level." from 0_kv_empl_master_request_flow where type_id='".$_POST['type']."'
                      AND access_level='".$empl_access['role_id']."' AND dim_id='".$empl_access['dflt_dimension_id']."' ";
                $next_lev_data=db_fetch(db_query($qry));



                if($curr_role_id[0]=='17')
                {
                    $next_lev_data[0]='0';
                }

                if($next_lev_data[0]=='0')
                {
                    $sql="UPDATE 0_kv_asset_request set role_id='0'
                      ,level='0',req_status='1' where id='".$_POST['remove_id']."'";
                }
                else
                {
                    $sql="UPDATE 0_kv_asset_request set role_id='".$next_lev_data[0]."'
                      ,level='".$level_incr."' where id='".$_POST['remove_id']."'";
                }

                if(db_query($sql))
                {
                    $sql_his="Insert into 0_kv_empl_req_approve_history (type_id,level,request_id,approved_by,
                              approved_on) values ('".$_POST['type']."','".$_POST['level']."','".$_POST['remove_id']."',
                              '".$_SESSION['wa_current_user']->user."','".date('Y-m-d')."')";
                    db_query($sql_his);

                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                    $flag='1';
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                    $flag='0';
                }
            }

            if($_POST['type']=='4')
            {

                $level_incr=$_POST['level']+1;
                $level='level_'.$level_incr;

                $sql_cur_role="select role_id,empl_id 
                               from 0_kv_empl_loan_request where id='".$_POST['remove_id']."' ";
                $curr_role_id=db_fetch(db_query($sql_cur_role));

                $empl_access=$this->get_employee_access_from_user($curr_role_id['empl_id']);

                $qry="Select ".$level." from 0_kv_empl_master_request_flow where type_id='".$_POST['type']."'
                      AND access_level='".$empl_access['role_id']."' AND dim_id='".$empl_access['dflt_dimension_id']."' ";
                $next_lev_data=db_fetch(db_query($qry));


                if($curr_role_id[0]=='17')
                {
                    $next_lev_data[0]='0';
                }

                if($next_lev_data[0]=='0')
                {
                    $sql="UPDATE 0_kv_empl_loan_request set role_id='0'
                      ,level='0',req_status='1' where id='".$_POST['remove_id']."'";
                }
                else
                {
                    $sql="UPDATE 0_kv_empl_loan_request set role_id='".$next_lev_data[0]."'
                      ,level='".$level_incr."' where id='".$_POST['remove_id']."'";
                }

                if(db_query($sql))
                {
                    $sql_his="Insert into 0_kv_empl_req_approve_history (type_id,level,request_id,approved_by,
                              approved_on) values ('".$_POST['type']."','".$_POST['level']."','".$_POST['remove_id']."',
                              '".$_SESSION['wa_current_user']->user."','".date('Y-m-d')."')";
                    db_query($sql_his);

                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                    $flag='1';
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                    $flag='0';
                }
            }

            if($_POST['type']=='1')
            {

                $level_incr=$_POST['level']+1;
                $level='level_'.$level_incr;

                $sql_cur_role="select role_id,empl_id 
                               from 0_kv_empl_leave_applied where id='".$_POST['remove_id']."' ";
                $curr_role_id=db_fetch(db_query($sql_cur_role));

                $empl_access=$this->get_employee_access_from_user($curr_role_id['empl_id']);

                $qry="Select ".$level." from 0_kv_empl_master_request_flow where type_id='".$_POST['type']."'
                      AND access_level='".$empl_access['role_id']."' AND dim_id='".$empl_access['dflt_dimension_id']."' ";
                $next_lev_data=db_fetch(db_query($qry));



                if($curr_role_id[0]=='17')
                {
                    $next_lev_data[0]='0';
                }


                if($next_lev_data[0]=='0')
                {
                    $sql="UPDATE 0_kv_empl_leave_applied set role_id='0'
                      ,level='0',req_status='1' where id='".$_POST['remove_id']."'";
                }
                else
                {
                    $sql="UPDATE 0_kv_empl_leave_applied set role_id='".$next_lev_data[0]."'
                      ,level='".$level_incr."' where id='".$_POST['remove_id']."'";
                }

                if(db_query($sql))
                {
                    $sql_his="Insert into 0_kv_empl_req_approve_history (type_id,level,request_id,approved_by,
                              approved_on) values ('".$_POST['type']."','".$_POST['level']."','".$_POST['remove_id']."',
                              '".$_SESSION['wa_current_user']->user."','".date('Y-m-d')."')";
                    db_query($sql_his);

                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                    $flag='1';
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                    $flag='0';
                }
            }


        }

        echo  json_encode($msg);

        if($flag=='1')
        {
            if($next_lev_data[0]!=0)
            {
                $this->send_mail_to_next_levl($_POST['type'],$_POST['remove_id'],$next_lev_data[0]);
            }
            $this->send_request_approve_mail($_POST['level'],$_POST['type'],$_POST['remove_id']);
        }

    }

    public function approve_leave_request_with_split_date()
    {
       $update_id=$_POST['update_id'];
       $full_paid_leave=$_POST['full_paid_leave'];
       $half_paid_leave=$_POST['half_paid_leave'];
       $unpaid_leave=$_POST['unpaid_leave'];
       $req_date=$_POST['req_date'];
       $days_reduce_cmnt=$_POST['days_reduce_cmnt'];
       $salary_deduction_option=$_POST['salary_deduction_option'];


        $level_incr=$_POST['level']+1;
        $level='level_'.$level_incr;

        $qry="Select ".$level." from 0_kv_empl_master_request_flow where type_id='1'";
        $next_lev_data=db_fetch(db_query($qry));

        if($next_lev_data[0]=='0')
        {
            $QUREY="select `date` as start_date,empl_id,leave_type,days from 0_kv_empl_leave_applied where id='".$update_id."'";
            $start_date_data=db_fetch(db_query($QUREY));

            if($req_date!=$start_date_data[3])
            {
                $end_date=date('Y-m-d', strtotime($start_date_data[0]. ' + '.$req_date.' days'));



                $sql="UPDATE 0_kv_empl_leave_applied set role_id='0' 
                     ,level='0',req_status='1',t_date='".$end_date."',days='".$req_date."',leave_days_reduce_cmnt='".$days_reduce_cmnt."' ";

                if($salary_deduction_option=='1')
                {
                    $sql.=" ,allowed_paid_leaves='".$req_date."'  ";
                }
                if($salary_deduction_option=='2')
                {
                   $sql.=" ,full_day_salary_cut='".$req_date."'  ";
                }
                if($salary_deduction_option=='3')
                {
                    $sql.=" ,half_day_salary_cut='".$req_date."'  ";
                }

                $sql.="  where id='".$update_id."'";
            }
            else
            {
                $sql="UPDATE 0_kv_empl_leave_applied set role_id='0' 
                     ,level='0',req_status='1'";

                if($salary_deduction_option=='1')
                {
                    $sql.=" ,allowed_paid_leaves='".$req_date."'  ";
                }
                if($salary_deduction_option=='2')
                {
                    $sql.=" ,full_day_salary_cut='".$req_date."'  ";
                }
                if($salary_deduction_option=='3')
                {
                    $sql.=" ,half_day_salary_cut='".$req_date."'  ";
                }

                $sql.=" where id='".$update_id."'";
            }

            /*--------------------INSERT LEAVE DAYS OF EMPLOYEE INTO ATTENDENCE-----------*/
              $QUREY="select `date` as start_date,empl_id,leave_type from 0_kv_empl_leave_applied where id='".$update_id."'";
              $start_date_data=db_fetch(db_query($QUREY));

              $qry="select empl_id from 0_kv_empl_info where id='".$start_date_data[1]."'";
              $empl_id_data=db_fetch(db_query($qry));

            $qry_leave="select char_code from 0_kv_empl_leave_types where id='".$start_date_data[2]."'";
            $leave_data=db_fetch(db_query($qry_leave));

               $a_date='';
              for($i=0;$i<$req_date;$i++)
              {
                  $a_date=date('Y-m-d', strtotime($start_date_data[0]. ' + '.$i.' days'));

                  $atten_sql="INSERT INTO 0_kv_empl_attendance (empl_id,code,a_date,in_time,out_time,leave_id)
                              VALUES ('".$empl_id_data[0]."','".$leave_data[0]."','".$a_date."','00:00:00','00:00:00','".$update_id."')";
                  db_query($atten_sql);


              }

            /*----------------------------------END----------------------------------------*/
        }
        else
        {
            $QUREY="select `date` as start_date,empl_id,leave_type,days from 0_kv_empl_leave_applied where id='".$update_id."'";
            $start_date_data=db_fetch(db_query($QUREY));

            if($req_date!=$start_date_data[3])
            {
                $end_date=date('Y-m-d', strtotime($start_date_data[0]. ' + '.$req_date.' days'));

                $sql="UPDATE 0_kv_empl_leave_applied set role_id='".$next_lev_data[0]."',allowed_paid_leaves='".$allow_leave."',
                      full_day_salary_cut='".$full_day_cut."',half_day_salary_cut='".$half_day_cut."'
                      ,level='".$level_incr."',t_date='".$end_date."',days='".$req_date."',leave_days_reduce_cmnt='".$days_reduce_cmnt."' where id='".$update_id."'";
            }
            else
            {
                $sql="UPDATE 0_kv_empl_leave_applied set role_id='".$next_lev_data[0]."',allowed_paid_leaves='".$allow_leave."',
                      full_day_salary_cut='".$full_day_cut."',half_day_salary_cut='".$half_day_cut."'
                      ,level='".$level_incr."' where id='".$update_id."'";
            }



        }


       //echo $sql;

        if(db_query($sql))
        {
            $sql_his="Insert into 0_kv_empl_req_approve_history (type_id,level,request_id,approved_by,
                              approved_on) values ('1','".$_POST['level']."','".$update_id."',
                              '".$_SESSION['wa_current_user']->user."','".date('Y-m-d')."')";
            db_query($sql_his);

            $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
        }
        else
        {
            $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
        }


        echo json_encode($msg);

    }





    public function dispprove_request()
    {
        $msg='';
        if(!empty($_POST['remove_id']))
        {
            if($_POST['type']=='3')
            {
                /*$level_incr=$_POST['level'];
                $level='level_'.$level_incr;*/

                $sql="UPDATE 0_kv_empl_passport_request set req_status='2' where id='".$_POST['remove_id']."'";

                if(db_query($sql))
                {
                    $sql_his="Insert into 0_kv_empl_req_approve_history (type_id,level,request_id,approved_by,
                              approved_on,comment) values ('".$_POST['type']."','".$_POST['level']."','".$_POST['remove_id']."',
                              '".$_SESSION['wa_current_user']->user."','".date('Y-m-d')."','".$_POST['comment']."')";
                    db_query($sql_his);


                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                }
            }

            if($_POST['type']=='2')
            {

                $sql="UPDATE 0_kv_empl_certificate_request set req_status='2' where id='".$_POST['remove_id']."'";
                if(db_query($sql))
                {
                    $sql_his="Insert into 0_kv_empl_req_approve_history (type_id,level,request_id,approved_by,
                              approved_on,comment) values ('".$_POST['type']."','".$_POST['level']."','".$_POST['remove_id']."',
                              '".$_SESSION['wa_current_user']->user."','".date('Y-m-d')."','".$_POST['comment']."')";
                    db_query($sql_his);

                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                }
            }

            if($_POST['type']=='5')
            {
                $sql="UPDATE 0_kv_empl_noc_request set req_status='2' where id='".$_POST['remove_id']."'";
                if(db_query($sql))
                {
                    $sql_his="Insert into 0_kv_empl_req_approve_history (type_id,level,request_id,approved_by,
                              approved_on,comment) values ('".$_POST['type']."','".$_POST['level']."','".$_POST['remove_id']."',
                              '".$_SESSION['wa_current_user']->user."','".date('Y-m-d')."','".$_POST['comment']."')";
                    db_query($sql_his);

                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                }
            }

            if($_POST['type']=='6')
            {
                $sql="UPDATE 0_kv_asset_request set req_status='2' where id='".$_POST['remove_id']."'";

                if(db_query($sql))
                {
                    $sql_his="Insert into 0_kv_empl_req_approve_history (type_id,level,request_id,approved_by,
                              approved_on,comment) values ('".$_POST['type']."','".$_POST['level']."','".$_POST['remove_id']."',
                              '".$_SESSION['wa_current_user']->user."','".date('Y-m-d')."','".$_POST['comment']."')";
                    db_query($sql_his);
                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                }
            }


            if($_POST['type']=='4')
            {
                $sql="UPDATE 0_kv_empl_loan_request set req_status='2' where id='".$_POST['remove_id']."'";

                if(db_query($sql))
                {
                    $sql_his="Insert into 0_kv_empl_req_approve_history (type_id,level,request_id,approved_by,
                              approved_on,comment) values ('".$_POST['type']."','".$_POST['level']."','".$_POST['remove_id']."',
                              '".$_SESSION['wa_current_user']->user."','".date('Y-m-d')."','".$_POST['comment']."')";
                    db_query($sql_his);
                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                }
            }



            if($_POST['type']=='1')
            {
                $sql="UPDATE 0_kv_empl_leave_applied set req_status='2' where id='".$_POST['remove_id']."'";

                if(db_query($sql))
                {
                    $sql_his="Insert into 0_kv_empl_req_approve_history (type_id,level,request_id,approved_by,
                              approved_on,comment) values ('".$_POST['type']."','".$_POST['level']."','".$_POST['remove_id']."',
                              '".$_SESSION['wa_current_user']->user."','".date('Y-m-d')."','".$_POST['comment']."')";
                    db_query($sql_his);
                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                }
            }

        }

        echo json_encode($msg);


        $this->send_request_disapprove_mail($_POST['level'],$_POST['type'],$_POST['remove_id']);

    }


    public function view_approval_history()
    {

        $sql="SELECT a.level,CONCAT(b.empl_firstname,' ',b.empl_lastname) AS emp_name
              ,a.type_id,a.comment,a.approved_on
                FROM 0_kv_empl_req_approve_history AS a 
                INNER JOIN 0_users AS u ON u.id=a.approved_by
               INNER JOIN 0_kv_empl_info AS b ON u.employee_id=b.id
                WHERE a.type_id='".$_POST['type_id']."' AND a.request_id='".$_POST['alt_id']."'";

        //echo $sql;
        $res=db_query($sql);

        $html='<html>
<table class="table table-hover"  >
<thead class="thead-light">
               <tr>
               <th style="padding: 8px;text-align: center;color: black;">Level</th>
               <th style="padding: 8px;text-align: center;color: black;">Role</th>
               <th style="padding: 8px;text-align: center;color: black;">Comment</th>
               <th style="padding: 8px;text-align: center;color: black;">Verified by</th>
               <th style="padding: 8px;text-align: center;color: black;">Verified on</th>
                </tr></thead><tbody>';
        $leve_lbl='';
        $role_label='';
        while($data=db_fetch($res))
        {
            $leve_lbl='level_'.$data['level'];
            $qry="select ".$leve_lbl." from 0_kv_empl_master_request_flow where type_id='".$data['type_id']."'";
            $role_id=db_fetch(db_query($qry));

            if(!empty($role_id[0]))
            {
                 if($role_id[0]=='1111')
                 {
                     $role_label='Line Manager';
                 }
                 else if($role_id[0]=='2222')
                 {
                     $role_label='Head Of Department';
                 }
                 else
                 {
                      $sql_qry="select role from 0_security_roles where id='".$role_id[0]."' ";
                      $data_set=db_fetch(db_query($sql_qry));
                      $role_label=$data_set[0];
                 }

            }


            $html.='<tr>
                      <td style="text-align: center;">Level '.$data['level'].'</td>
                      <td style="text-align: center;">'.$role_label.'</td>
                      <td style="text-align: center;">'.$data['comment'].'</td>
                      <td style="text-align: center;">'.$data['emp_name'].'</td>
                      <td style="text-align: center;">'.date('d-m-Y',strtotime($data['approved_on'])).'</td>
                   </tr>';
        }

        $html.='</tbody></table></html>';

        $msg=['html'=>$html];


        return AxisPro::SendResponse($msg);


    }


   public function get_employee_leave_history()
   {
       $eml_id=$_POST['empl_id'];
       $sql="select description,id from 0_kv_empl_leave_types WHERE char_code<>'p' order by id desc";
       $res=db_query($sql);
       $leave_array=[];
       $leave_days=[];
       while($data=db_fetch($res))
       {
           $sql_q="SELECT SUM(days) as days
                    FROM 0_kv_empl_leave_applied 
                    WHERE leave_type='".$data['id']."' AND empl_id='".$eml_id."'
                    AND req_status='1'";
           $days_data=db_fetch(db_query($sql_q));


           array_push($leave_array, $data[0]);
           array_push($leave_days,$days_data[0]);
       }

       $msg=['levae_names'=>$leave_array,'leave_days'=>$leave_days];


       return AxisPro::SendResponse($msg);


   }

   public function submit_loan_request()
   {
       $comment=str_replace("'","",$_POST['txtcomment']);
       $hdn_id=$_POST['hdn_id'];
       $loan_amount=$_POST['loan_amount'];
       $install_count=$_POST['ddl_instll_required'];
       $loan_required_date=date('Y-m-d',strtotime($_POST['txt_required_date']));
       $pfx='';
       $pfx_to_ref='';
       if(!empty($loan_required_date))
       {

           if(!empty($hdn_id))
           {
               $qry_up="Update 0_kv_empl_loan_request set `loan_required_date`='".$loan_required_date."',description='".$comment."'
                        ,loan_amount='".$loan_amount."',installment_count='".$install_count."'
                         where id='".$hdn_id."'";
               if(db_query($qry_up))
               {
                   $msg=['status'=>'Success','msg'=>'Data updated succesfully'];
               }
               else
               {
                   $msg=['status'=>'Error','msg'=>'Error occured while saving'];
               }

           }
           else
           {
               /********************GET PREFIX****************/
               $sql="select `value` from 0_sys_prefs where name='loan_request_pfx'";
               $data=db_fetch(db_query($sql));

               if($data[0]!='')
               {
                   $pfx=$data[0];
               }

               $emp_data_sql="select id
                          from 0_kv_empl_info where user_id='".$_SESSION['wa_current_user']->user."'";
               $employee_data=db_fetch(db_query($emp_data_sql));

               /**************************GETTING NOC REQUEST FLOW******/
               include('API_HRM_Call.php');
               $call_obj=new API_HRM_Call();
               $user_dim_id=$call_obj->get_user_dim();

               $qry="select * from 0_kv_empl_master_request_flow where type_id='4' 
                      and dim_id='".$user_dim_id."' and access_level='".$_SESSION['wa_current_user']->access."' ";
               $qry_data=db_fetch(db_query($qry));
               /***********************************END********************/


               $qry="INSERT INTO 0_kv_empl_loan_request (description,empl_id,`loan_required_date`,status,created_by,created_on,role_id,level,loan_amount,installment_count)
                 VALUES ('".$comment."','".$employee_data['id']."','".$loan_required_date."','1','".$_SESSION['wa_current_user']->user."'
                        ,'".date('Y-m-d')."','".$qry_data['level_1']."','1','".$loan_amount."','".$install_count."')";
               if(db_query($qry))
               {
                   $insert_id=db_insert_id();
                   $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                   $pfx_to_ref=$pfx.''.$insert_id;
                   $update="update 0_kv_empl_loan_request set request_ref_no='".$pfx_to_ref."' 
                        where id='".$insert_id."'";
                   db_query($update);
               }
               else
               {
                   $msg=['status'=>'Error','msg'=>'Error occured while saving'];
               }
           }

           return AxisPro::SendResponse($msg);

       }
   }

    public function get_loan_requests()
    {

        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $sql=" SELECT a.description,a.request_ref_no,a.`loan_required_date` as requestdate,a.created_on as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.loan_amount
                ,a.installment_count
                FROM 0_kv_empl_loan_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1'";

        if($_SESSION['wa_current_user']->user<>'1')
        {
            $sql.=" AND b.user_id='".$_SESSION['wa_current_user']->user."'";
        }
        $sql.=" LIMIT ".$start.",".$length." ";


        $result = db_query($sql);
        $ref_no='';

        while ($myrow = db_fetch($result)) {

            $attributes='alt_id="'.$myrow['id'].'" alt_amount="'.$myrow['loan_amount'].'" alt_installment="'.$myrow['installment_count'].'"
                         alt_loan_require_date="'.date('d-m-Y',strtotime($myrow['requestdate'])).'" alt_description="'.$myrow['description'].'" ';

            $controls='<label class=\'btn btn-sm btn-primary ClsBtnEdit\' '.$attributes.' data-toggle="modal" data-target="#myModal"><i class=\'flaticon-edit\'></i></label>
                       <label class=\'btn btn-sm btn-primary ClsBtnRemove\' alt_id="'.$myrow['id'].'"><i class=\'flaticon-delete\'></i></label>';

            $ref_no='<label style="font-weight: bold;color:blue;">'.$myrow['request_ref_no'].'</label>';
            $data[] = array(
                $ref_no,
                $myrow['description'],
                $myrow['loan_amount'],
                $myrow['installment_count'],
                date('d-m-Y',strtotime($myrow['requestdate'])),
                $myrow['req_status'],
                date('d-m-Y',strtotime($myrow['created'])),
                $controls,

            );
        }

        $total_sql="SELECT a.id
                FROM 0_kv_empl_certificate_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1'";
        if($_SESSION['wa_current_user']->user<>'1')
        {
            $sql.=" AND b.user_id='".$_SESSION['wa_current_user']->user."'";
        }

        $result_tot = db_query($total_sql);

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($result_tot),
            "recordsFiltered" => db_num_rows($result_tot),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);

    }


    public function send_request_approve_mail($level,$type,$req_id)
    {
        /*-------------------SEND EMAIL------------*/
        $path_to_root = "..";
        include_once($path_to_root . "/API/HRM_Mail.php");
        $hrm_mail=new HRM_Mail();

        $request_name='';
        $table_name='';

        if($type=='1'){$request_name='Leave Request'; $table_name='0_kv_empl_leave_applied'; }

        if($type=='2'){$request_name='Certificate Request'; $table_name='0_kv_empl_certificate_request';}

        if($type=='3'){$request_name='Passport Request'; $table_name='0_kv_empl_passport_request';}

        if($type=='4'){$request_name='Loan Request'; $table_name='0_kv_empl_loan_request';}

        if($type=='5'){$request_name='NOC Request'; $table_name='0_kv_empl_noc_request';}

        if($type=='6'){$request_name='Asset Request'; $table_name='0_kv_asset_request';}

        if($type=='7'){$request_name='Asset Return Request'; $table_name='0_kv_asset_request';}

        $sql_qry="select empl_id from ".$table_name." where id='".$req_id."' ";
        $fetch_data=db_fetch(db_query($sql_qry));


        $edmp_sql="SELECT email,empl_firstname,email FROM  0_kv_empl_info 
                           where id=".$fetch_data[0]." ";
        $result_d = db_query($edmp_sql, "Can't get your allowed user details");
        $row_res= db_fetch($result_d);



        $to=$row_res[2];
        $sub=$request_name.' - level_'.$level." Approved";
        $content= "<div>
                              <label>Dear $row_res[1],</label></br>
                                <div style='height: 39px;margin-top: 33px;'>
                                    Your ".$request_name." request ".$level." approved.
                                   </div>
                                  
                                    <div style='margin-top:2%;'>
                                    <div>Thanks,</div>
                                    <div>HRMS</div>
                                </div>
                                    
                  </div>";

        $hrm_mail->send_mail($to,$sub,$content);
    }


     public function send_mail_to_next_levl($type,$req_id,$role_id)
     {

         /*-------------------SEND EMAIL------------*/
         $path_to_root = "..";
         include_once($path_to_root . "/API/HRM_Mail.php");
         $hrm_mail=new HRM_Mail();

         $request_name='';
         $table_name='';
         $to_email='';
         $emp_name='';

         if($type=='1'){$request_name='Leave Request'; $table_name='0_kv_empl_leave_applied'; }

         if($type=='2'){$request_name='Certificate Request'; $table_name='0_kv_empl_certificate_request';}

         if($type=='3'){$request_name='Passport Request'; $table_name='0_kv_empl_passport_request';}

         if($type=='4'){$request_name='Loan Request'; $table_name='0_kv_empl_loan_request';}

         if($type=='5'){$request_name='NOC Request'; $table_name='0_kv_empl_noc_request';}

         if($type=='6'){$request_name='Asset Request'; $table_name='0_kv_asset_request';}

         if($type=='7'){$request_name='Asset Return Request'; $table_name='0_kv_asset_request';}

         $sql_qry="select empl_id from ".$table_name." where id='".$req_id."' ";
         $fetch_data=db_fetch(db_query($sql_qry));

         if($role_id=='1111')
         {

             /*-----------------GET Line manager of Employee department-----*/
               $q=" SELECT a.report_to 
                     FROM 0_kv_empl_info AS a 
                     WHERE a.id='".$fetch_data[0]."' ";
               $report_to_data=db_fetch(db_query($q));

               if($report_to_data[0]!='')
               {
                    $sql_emp="select email,empl_firstname from 0_kv_empl_info where id='".$report_to_data[0]."' ";
                    $line_manger_data=db_fetch(db_query($sql_emp));
                    $to_email=$line_manger_data[0];
                    $emp_name=$line_manger_data[1];
               }
             /*-------------------------END----------------------------------*/

         }
         else if($role_id=='2222')
         {
             /*-----------------GET head of dept of employee-----*/
             $q=" SELECT a.department 
                     FROM 0_kv_empl_job AS a 
                     WHERE a.empl_id='".$fetch_data[0]."' ";
             $report_to_data=db_fetch(db_query($q));

             if($report_to_data[0]!='')
             {
                 $ry="select empl_id from 0_kv_empl_job where department='".$report_to_data[0]."'
                      and head_of_dept='1'";

                 $head_dept_data=db_fetch(db_query($ry));
                 if($head_dept_data[0]!='')
                 {
                     $sql_emp="select email,empl_firstname from 0_kv_empl_info where id='".$head_dept_data[0]."' ";
                     $line_manger_data=db_fetch(db_query($sql_emp));
                     $to_email=$line_manger_data[0];
                     $emp_name=$line_manger_data[1];
                 }
             }
             /*-------------------------END----------------------------------*/
         }
         else
         {
             $edmp_sql="SELECT empl_firstname,email FROM 0_kv_empl_info 
                           where id=".$fetch_data[0]." ";
             $result_d = db_query($edmp_sql, "Can't get your allowed user details");
             $row_res= db_fetch($result_d);
             $emp_name=$row_res[0];

            $ss="SELECT a.email 
                    FROM 0_kv_empl_info AS a 
                    INNER JOIN 0_users AS b ON a.user_id=b.id
                    WHERE b.role_id='".$role_id."'";
            $email_data=db_query($ss);

            while($emil_ids=db_fetch($email_data))
            {
                $to_email.=$emil_ids[0].',';
            }

             $to_email=rtrim($to_email,',');
         }



         $to=$to_email;
         $sub='New Request To Verify';
         $content= "<div>
                                   <label>Dear $emp_name,</label></br>
                                   <div style='height: 39px;
                                    margin-top: 33px;'>You have one new ".$request_name." request to verify.</div>
                                  
                                    <div style='margin-top:2%;'>
                                    <div>Thanks,</div>
                                    <div>HRMS</div>
                                    </div>
                                    
                  </div>";

       $hrm_mail->send_mail($to,$sub,$content);

     }


    public function send_request_disapprove_mail($level,$type,$req_id)
    {
        /*-------------------SEND EMAIL------------*/
        $path_to_root = "..";
        include_once($path_to_root . "/API/HRM_Mail.php");
        $hrm_mail=new HRM_Mail();

        $request_name='';
        $table_name='';

        if($type=='1'){$request_name='Leave Request'; $table_name='0_kv_empl_leave_applied'; }

        if($type=='2'){$request_name='Certificate Request'; $table_name='0_kv_empl_certificate_request';}

        if($type=='3'){$request_name='Passport Request'; $table_name='0_kv_empl_passport_request';}

        if($type=='4'){$request_name='Loan Request'; $table_name='0_kv_empl_loan_request';}

        if($type=='5'){$request_name='NOC Request'; $table_name='0_kv_empl_noc_request';}

        if($type=='6'){$request_name='Asset Request'; $table_name='0_kv_asset_request';}

        if($type=='7'){$request_name='Asset Return Request'; $table_name='0_kv_asset_request';}

        $sql_qry="select empl_id from ".$table_name." where id='".$req_id."' ";
        $fetch_data=db_fetch(db_query($sql_qry));


        $edmp_sql="SELECT email,empl_firstname,email FROM  0_kv_empl_info 
                           where id=".$fetch_data[0]." ";
        $result_d = db_query($edmp_sql, "Can't get your allowed user details");
        $row_res= db_fetch($result_d);



        $to=$row_res[2];
        $sub=$request_name.' - level_'.$level." Approved";
        $content= "<div>
                              <label>Dear $row_res[1],</label></br>
                                   <div style='height: 39px;margin-top: 33px;'>
                                    Your ".$request_name." request has disapproved.
                                   </div>
                                  
                                    <div style='margin-top:2%;'>
                                    <div>Thanks,</div>
                                    <div>HRMS</div>
                                </div>
                                    
                  </div>";

        $hrm_mail->send_mail($to,$sub,$content);
    }


     public function get_esb_details()
     {
         $empl_id=$_POST['empl_id'];

         $sql="SELECT b.id,a.joining 
                FROM 0_kv_empl_job AS a
                INNER JOIN 0_kv_empl_info AS b ON a.empl_id=b.id
                WHERE b.empl_id='".$empl_id."' ";
         $join_date=db_fetch(db_query($sql));



         $sql_bsic="select pay_amount from 0_kv_empl_salary_details where emp_id='".$join_date[0]."' 
                    and is_basic='1'";
         $basic_salary=db_fetch(db_query($sql_bsic));


         $ch_sql="select count(id) from 0_kv_empl_esb where empl_id='".$join_date[0]."' and status='1' ";
         $esb_done=db_fetch(db_query($ch_sql));

         $esb_amount='';
         $daliy_wage='';
         $esg_net_amount='';



         if($esb_done[0]=='0')
         {
             if(!empty($join_date[1]))
             {

                 $firstDate = $join_date[1];
                 $secondDate = date('Y-m-d');

                 $dateDifference = abs(strtotime($secondDate) - strtotime($firstDate));

                 $years  = floor($dateDifference / (365 * 60 * 60 * 24));

                 if($years>=1 && $years<5)
                 {
                     $daliy_wage=$basic_salary[0]/30;
                     $esb_amount=$daliy_wage*21;

                 }
                 else if($years>5)
                 {
                     $daliy_wage=$basic_salary[0]/30;
                     $esb_amount=$daliy_wage*30;
                 }

                 /*********checking for loan and other deduction************/
                 $sql_ry="SELECT (periods-periods_paid) AS pending_periods,monthly_pay
                        FROM 0_kv_empl_loan WHERE empl_id='".$join_date[0]."'";
                 $loan_data=db_query($sql_ry);
                 $loan_ded_amount='0';
                 while($loan_row=db_fetch($loan_data))
                 {
                     $loan_ded_amount+=$loan_row[1]*$loan_row[0];
                 }


                 $sql_war="SELECT (ded_amount-deducted_amount) AS ded_amount_pend
                         FROM 0_kv_empl_warings WHERE emp_id='".$join_date[0]."'";
                 $ded_war_pend_amnt=db_query($sql_war);
                 $ded_waring_pending_amount='0';
                 while($ded_row=db_fetch($ded_war_pend_amnt))
                 {
                     $ded_waring_pending_amount+=$ded_row[0];
                 }

                 $esg_net_amount=($esb_amount*$years)-($loan_ded_amount+$ded_waring_pending_amount);

                 /*************************END******************************/
                 $msg=['join_date'=>date('m-d-Y',strtotime($join_date[1]))
                     ,'esb'=>$esg_net_amount,'years_complted'=>$years,'loan_ded_amnt'=>$loan_ded_amount
                     ,'warning_ded_amount'=>$ded_waring_pending_amount,'tot_esb_amount'=>$esb_amount*$years];


             }
         }
         else
         {

             $ch_sql="select years_worked,loan_amount,ded_amount,amount 
                      from 0_kv_empl_esb where empl_id='".$join_date[0]."'  and status='1' ";
             $esb_done=db_fetch(db_query($ch_sql));
             //echo $ch_sql;

             $msg=['join_date'=>date('m-d-Y',strtotime($join_date[1]))
                 ,'esb'=>$esb_done[3],'years_complted'=>$esb_done[0],'loan_ded_amnt'=>$esb_done[1]
                 ,'warning_ded_amount'=>$esb_done[2],'tot_esb_amount'=>'created'];
         }


         return AxisPro::SendResponse($msg);

     }

     public function create_esb()
     {
         $sql="SELECT b.id
                FROM 0_kv_empl_info AS b
                WHERE b.empl_id='".$_POST['empl_id']."' ";
         $emp_data=db_fetch(db_query($sql));



         $sql="INSERT INTO 0_kv_empl_esb (empl_id,date,years_worked,amount,loan_amount,ded_amount,dept_id,status)
               values ('".$emp_data[0]."','".date('Y-m-d')."','".$_POST['years']."'
               ,'".$_POST['esb_amnt']."','".$_POST['loan_amnt']."','".$_POST['warn_amnt']."','".$_POST['dept_id']."','1')";

         $msg='';
         if(db_query($sql))
         {
             $msg=['status'=>'OK','msg'=>'Saved Successfully'];
         }
         else
         {
             $msg=['status'=>'ERROR','msg'=>'Error Occured saving data'];
         }


         return AxisPro::SendResponse($msg);

     }


    public function list_esb_entries()
    {

        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $sql = "SELECT a.id,b.empl_id,CONCAT(b.empl_firstname,\" \",b.empl_lastname) AS Emp_name
                ,a.`date` AS created_date,a.loan_amount,a.ded_amount,a.amount
                FROM 0_kv_empl_esb AS a
                INNER JOIN 0_kv_empl_info AS b ON a.empl_id=b.id
                WHERE a.dept_id='".$_POST['dept_id']."'";


        if($_POST['emp_id']!='0')
        {
            $sql .= " AND a.empl_id='".$_POST['emp_id']."' ";
        }


        $sql .= " AND a.status='1' ORDER BY id asc LIMIT ".$start.",".$length." ";


        $result = db_query($sql);

      //  echo $sql;
        $data = [];
        $esb_amount='';
        $controls='';
        $attributes='';
        while ($myrow = db_fetch($result)) {

            $attributes='alt_id="'.$myrow['id'].'"   ';


            $controls='<label class=\'btn btn-sm btn-primary ClsBtnDelete\' alt_id="'.$myrow['id'].'"><i class=\'flaticon-delete\'></i></label>';

            $esb_amount="<label style='font-weight: bold;color:blue;'>".$myrow['amount']."</label>";


            //'<a target="_blank" href="ERP/gl/view/gl_trans_view.php?type_id=0&amp;trans_no='.$myrow['trans_id'].'" onclick="javascript:openWindow(this.href,this.target); return false;" accesskey="V"><u>V</u>iew GL</a>'

            $data[] = array(
                $myrow['empl_id'],
                $myrow['Emp_name'],
                date('d/m/Y',strtotime($myrow['created_date'])),
                $myrow['loan_amount'],
                $myrow['ded_amount'],
                $esb_amount,
                '',
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


    public function remove_esb()
    {
        $msg='';
        $sql="update 0_kv_empl_esb set status='0' where id='".$_POST['remove_id']."' ";
        if(db_query($sql))
        {
            $msg=['status'=>'OK','msg'=>'Saved Successfully'];
        }
        else
        {
            $msg=['status'=>'OK','msg'=>'Error occured while saving'];
        }
        return AxisPro::SendResponse($msg);
    }

    public function get_dimensions_for_request_flow()
    {
        $sql = "SELECT id,name FROM 0_dimensions";
        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return $return_result;
    }

    public function get_access_roles()
    {
        $sql = "SELECT id,role FROM 0_security_roles";
        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return $return_result;
    }


    public function get_employee_access_from_user($empl_id)
    {
        $sql="SELECT role_id,dflt_dimension_id from 0_users where employee_id='".$empl_id."' ";
        $role_id=db_fetch(db_query($sql));

        return $role_id;
    }


}