<?php
include_once("AxisPro.php");
include_once("PrepareQuery.php");

Class API_HRM_Document_Request
{
    public function request_document()
    {

        $doc_type=$_POST['doc_type'];
        $reason=$_POST['reason'];
        $doc_required_date=date('Y-m-d',strtotime($_POST['doc_required_date']));
        $hdn_edit_id=$_POST['edit_pk_id'];
        $saving_type=$_POST['saving_type'];

        if($saving_type=='0')
        {
            $get_emp_user="select employee_id as id from 0_users where id='".$_SESSION['wa_current_user']->user."'";
            $emp_id=db_fetch(db_query($get_emp_user));
            $_sql="Insert into 0_kv_empl_docu_request_details (empl_id,doc_type,reason,doc_req_date,status,created_date,active) 
             values('".$emp_id['id']."','".$doc_type."','".$reason."','".$doc_required_date."','0','".date('Y-m-d H:i:s')."','1')";

        }
        else if($saving_type=='1')
        {
            $_sql="Update 0_kv_empl_docu_request_details set doc_type='".$doc_type."',reason='".$reason."',
                   doc_req_date='".$doc_required_date."' where id='".$hdn_edit_id."'";
        }

        //echo $_sql;
        $msg='';
        if(db_query($_sql))
        {
            $msg=['status'=>'OK','msg'=>'Request Saved Successfully'];
        }
        else
        {
            $msg=['status'=>'Error','msg'=>'Error Occured While Saving Data'];
        }

        return AxisPro::SendResponse($msg);

    }



    public function list_request()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $root_url=str_replace("\ERP","",getcwd());
        $root_url=str_replace("\API","",$root_url);

        $sql="Select b.description,a.reason,a.doc_req_date,a.status,a.id,a.doc_type,a.document_name
                From 0_kv_empl_docu_request_details as a
                Inner join 0_kv_empl_doc_type as b on a.doc_type=b.id
                 WHERE active='1'";
        if($_SESSION['wa_current_user']->access!='2')
        {
            $get_emp_user="select employee_id as id from 0_users where id='".$_SESSION['wa_current_user']->user."'";
            $emp_id=db_fetch(db_query($get_emp_user));
            $sql .=" AND a.empl_id='".$emp_id['id']."'";
        }


        $sql .=" Order by a.id desc LIMIT ".$start.",".$length." ";

        $result = db_query($sql);
        $data=array();
        $status='';
        $root_url='';
        while ($myrow = db_fetch_assoc($result)) {
            $alt_attr="alt_pk_id='".$myrow['id']."' alt_doc_type='".$myrow['doc_type']."' alt_reason='".$myrow['reason']."'
                       alt_required_date='".date('d-m-Y',strtotime($myrow['doc_req_date']))."' ";

            if($myrow['status']=='0')
            {
                $status='<label style="color:red;font-weight:bold;">Pending</label>';
            }
            else if($myrow['status']=='1')
            {
                $status='<label style="color:green;font-weight:bold;">Approved</label>';
            }
            else if($myrow['status']=='2')
            {
                $status='<label style="color:black;font-weight:bold;">Rejected</label>';
            }

            $file=$root_url.'assets/uploads/'.$myrow['document_name'];

            $data[] = array(
                $myrow['description'],
                $myrow['reason'],
                date('d-m-Y',strtotime($myrow['doc_req_date'])),
                '<a href="'.$file.'" download ><label  style="cursor:pointer;">'.$myrow['document_name'].'</label></a>',
                $status,
                '<label class="ClsEdit" style="cursor: pointer;"  '.$alt_attr.' data-toggle="modal" data-target="#myModal"><i class=\'flaticon-edit\'></i></label>&nbsp;
                <label class="ClsRemove" style="cursor: pointer;"  '.$alt_attr.'><i class=\'flaticon-delete\'></i></label>'

            );
        }

        $sql_tot="SELECT * FROM 0_kv_empl_docu_request_details WHERE active='1' ";
        if($_SESSION['wa_current_user']->access!='2')
        {
            $get_emp_user="select employee_id as id from 0_users where id='".$_SESSION['wa_current_user']->user."'";
            $emp_id=db_fetch(db_query($get_emp_user));
            $sql_tot .=" AND empl_id='".$emp_id['id']."'";
        }


        $tot_result = db_query($sql_tot);

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($tot_result),
            "recordsFiltered" => db_num_rows($tot_result),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);
    }


    public function disapprove_status()
    {

        $_sql="Update 0_kv_empl_docu_request_details set status='2'
               ,approved_by='".$_SESSION['wa_current_user']->user."' where id='".$_POST['remove_id']."'
              ";
        if(db_query($_sql))
        {
            $msg=['status'=>'OK','msg'=>'Updated Successfully'];
        }
        else
        {
            $msg=['status'=>'Error','msg'=>'Error Occured While Saving Data'];
        }
        return AxisPro::SendResponse($msg);
    }



    public function remove_request()
    {
        $pk_id=$_POST['remove_id'];
        if($pk_id)
        {
            $_sql="Update 0_kv_empl_docu_request_details set active='0' where id='".$pk_id."'";


            if(db_query($_sql))
            {
                $msg=['status'=>'OK','msg'=>'Request Saved Successfully'];
            }
            else
            {
                $msg=['status'=>'Error','msg'=>'Error Occured While Saving Data'];
            }

            return AxisPro::SendResponse($msg);
        }

    }



    public function list_requests_for_employee()
    {


        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $root_url=str_replace("\ERP","",getcwd());
        $root_url=str_replace("\API","",$root_url);

        $get_emp_user="select employee_id as id from 0_users 
                   where id='".$_SESSION['wa_current_user']->user."'";
        $emp_id=db_fetch(db_query($get_emp_user));

        $sql="Select b.description,a.reason,a.doc_req_date,a.status,n.document_approve_empl_id
              ,a.empl_id,a.id,CONCAT(n.empl_id,' - ',n.empl_firstname,' ',n.empl_lastname) as empname
                From 0_kv_empl_docu_request_details as a
                Inner join 0_kv_empl_doc_type as b on a.doc_type=b.id
                Inner join 0_kv_empl_info as n on n.id=a.empl_id
                WHere a.active='1' 
                ";
        if($_SESSION['wa_current_user']->access!='2')
        {
            $sql .=" AND n.document_approve_empl_id='".$emp_id['id']."' 
                     ";
        }

        $sql .=" Order by a.id desc LIMIT ".$start.",".$length." ";
//echo $sql;
        $result = db_query($sql);
        $data=array();
        $status='';
        while ($myrow = db_fetch_assoc($result)) {
            $alt_attr="alt_pk_id='".$myrow['id']."'";

            if($myrow['status']=='0')
            {
                $status='<label style="color:red;font-weight:bold;">Pending</label>';
            }
            else if($myrow['status']=='1')
            {
                $status='<label style="color:green;font-weight:bold;">Approved</label>';
            }
            else if($myrow['status']=='2')
            {
                $status='<label style="color:black;font-weight:bold;">Rejected</label>';
            }

            $data[] = array(
                $myrow['empname'],
                $myrow['description'],
                $myrow['reason'],
                date('d-m-Y',strtotime($myrow['doc_req_date'])),
                $status,
                '<button class="approve" '.$alt_attr.' data-toggle="modal" data-target="#myModalAttach">Approve</button>&nbsp;
                 <button class="disapprove" '.$alt_attr.'>Cancel</button>'

            );
        }

        $sql_tot="SELECT * FROM 0_kv_empl_docu_request_details WHERE active='1' ";
        if($_SESSION['wa_current_user']->access!='2')
        {
            $sql_tot .=" AND a.empl_id='".$_SESSION['wa_current_user']->user."'";
        }
        $tot_result = db_query($sql_tot);

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($tot_result),
            "recordsFiltered" => db_num_rows($tot_result),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);


    }


    public function update_request_status()
    {
        $id=$_POST['remove_id'];
        $type=$_POST['type'];

        $dis_msg='';
        if($_FILES["fileToUpload"]["name"]!='')
        {
            $root_url=str_replace("\ERP","",getcwd());
            $root_url=str_replace("\API","",$root_url);
            $target_dir = $root_url."/assets/uploads/";
            $fname=explode(".",$_FILES["fileToUpload"]["name"]);
            $rand=rand(10,100);
            $filename=$fname[0].'_'.$rand.'.'.$fname[1];
            $target_file = $target_dir . basename($filename);
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            $dis_msg='';

            if ($_FILES["fileToUpload"]["size"] > 50000000) {
                $dis_msg='File size exceeded';
            }
            if($imageFileType != "pdf" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "jpg") {
                $dis_msg='File format is not allowed';
            }


            if($dis_msg!='')
            {
                $msg=['status'=>'OK','msg'=>'Request Saved Successfully'];
            }
            else
            {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

                }
            }

        }

        if($dis_msg=='')
        {
            $_sql="Update 0_kv_empl_docu_request_details set status='".$type."',approved_by='".$_SESSION['wa_current_user']->user."'
               ";
            if($_FILES["fileToUpload"]["name"]!='')
            {
                $_sql.=" ,document_name='".$filename."' ";
            }
            $_sql.=" ,approved_on='".date('Y-m-d H:i:s')."' where id='".$id."'";
            //echo $_sql;

            if(db_query($_sql))
            {
                $msg=['status'=>'OK','msg'=>'Request Saved Successfully'];
            }
            else
            {
                $msg=['status'=>'Error','msg'=>'Error occured saving data'];
            }
        }
        else
        {
            $msg=['status'=>'Error','msg'=>$dis_msg];
        }

        return AxisPro::SendResponse($msg);
    }

    public function getdashboardData_HRM()
    {
        /*************************BIRTH DAY DATA**************/

        $s_sql="SELECT a.department 
                    FROM 0_kv_empl_job AS a
                    INNER JOIN 0_users AS b ON b.employee_id=a.empl_id
                    WHERE b.id='".$_SESSION['wa_current_user']->user."'";
        $dept_id=db_fetch(db_query($s_sql));



        $b_sql="SELECT COUNT(a.id)
                    FROM  0_kv_empl_info AS a 
                    INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id 
                    WHERE  DATE_ADD(a.date_of_birth, 
                                    INTERVAL YEAR(CURDATE())-YEAR(a.date_of_birth)
                                             + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(a.date_of_birth),1,0)
                                    YEAR)  
                                BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 5 DAY)
                    ";
        if(!in_array($_SESSION['wa_current_user']->access,[2,17,16,24,25]))
        {
            $b_sql .=" AND b.department='".$dept_id['department']."' ";
        }
        $birth_cnt=db_fetch(db_query($b_sql));
        /*******************************END*********************/
        /*****************************Doc expiry checking**********/
        $d_sql="SELECT  COUNT(id) FROM 0_employee_docs 
                WHERE DATEDIFF(expiry_date,'".date('Y-m-d')."') <5  ";
        if(!in_array($_SESSION['wa_current_user']->access,[2,17,16,24,25]))
        {
            $d_sql .=" AND dept_id='".$dept_id[0]."' ";
        }
        $doc_expiry_cnt=db_fetch(db_query($d_sql));

        /*********************************END**********************/

        /********************************Total Employees***********/

        if(in_array($_SESSION['wa_current_user']->access,[2,17,16,24,25]))
        {
            $e_sql="SELECT COUNT(id) 
                    FROM 0_kv_empl_info ";
        }
        else
        {
            $e_sql="SELECT  COUNT(a.id) 
                    FROM 0_kv_empl_info AS a
                    INNER JOIN  0_kv_empl_job AS b ON a.id=b.empl_id
                    WHERE b.department='".$dept_id[0]."' ";
        }

        $tot_emp_cnt=db_fetch(db_query($e_sql));
        /**************************************END*****************/


        return array('birth_day'=>$birth_cnt[0],'doc_exp_cnt'=> $doc_expiry_cnt[0],'tot_emp_cnt'=>$tot_emp_cnt[0]);

    }

    public function get_birthday_details()
    {

         $s_sql="SELECT a.department 
                    FROM 0_kv_empl_job AS a
                    INNER JOIN 0_users AS b ON b.employee_id=a.empl_id
                    WHERE b.id='".$_SESSION['wa_current_user']->user."'";
        $dept_id=db_fetch(db_query($s_sql));



        $b_sql="SELECT a.empl_id,a.empl_firstname,a.date_of_birth
                    FROM  0_kv_empl_info AS a
                    INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                    WHERE  DATE_ADD(a.date_of_birth, 
                                    INTERVAL YEAR(CURDATE())-YEAR(a.date_of_birth)
                                             + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(a.date_of_birth),1,0)
                                    YEAR)  
                                BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 5 DAY)
                                           ";
        if(!in_array($_SESSION['wa_current_user']->access,[2,17,16,24,25,18]))
        {
            $b_sql .=" AND dept_id='".$dept_id[0]."' ";
        }
        $res=db_query($b_sql);
        $hmtl="<table border='1px solid #ccc;' style='width:100%;'>
                <tr>
                   <th style='padding: 8px;text-align: center;'>Emp. ID</th>
                   <th style='padding: 8px;text-align: center;'>Name</th>
                   <th style='padding: 8px;text-align: center;'>Birth Date</th>
                </tr>
             ";


        if(!empty($res))
        {
             while($birth_cnt=db_fetch($res))
            {
                $hmtl.="<tr>
                              <td style='text-align: center;'>".$birth_cnt['empl_id']."</td>
                              <td style='text-align: center;'>".$birth_cnt['empl_firstname']."</td>
                              <td style='text-align: center;'>".date('d-m-Y',strtotime($birth_cnt['date_of_birth']))."</td>
                            </tr>";
            }
        }


        $hmtl.=" </table>";
        return $hmtl;
    }

    public function get_expirying_doc_details()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);
        $doc_type_id=$_POST['doc_type_id'];

         $days_interval=0;
        if($doc_type_id!='0')
         {
            $sql="select days from 0_kv_empl_doc_type where id='".$doc_type_id."'  ";
            $doc_exp_days=db_fetch(db_query($sql));
            $days_interval=$doc_exp_days[0];
         }
         else
         {
            $days_interval='15';
         }

        $check_dept="SELECT d.id
                      from 0_kv_empl_info AS a
                      INNER JOIN 0_kv_empl_departments AS d ON a.id=d.head_of_empl_id
                      left join 0_users as c ON c.employee_id=a.id
                      WHERE c.id='".$_SESSION['wa_current_user']->user."' "; 
        $check_dept_res=db_query($check_dept); 
        $rows_chk_dept=db_num_rows($check_dept_res);

        $dept_ids='';
        while($data_dept=db_fetch($check_dept_res))
          {
            $dept_ids.=$data_dept['0'].',';
          } 



        // $s_sql="SELECT a.department 
        //             FROM 0_kv_empl_job AS a
        //             INNER JOIN 0_users AS b ON b.employee_id=a.empl_id
        //             WHERE b.id='".$_SESSION['wa_current_user']->user."'";
        // $dept_id=db_fetch(db_query($s_sql));

        $b_sql="SELECT CONCAT(b.empl_id,' - ',b.empl_firstname) as empname,a.description,a.expiry_date,DATEDIFF(a.expiry_date,CURDATE()) as expirein
               FROM 
                0_employee_docs AS a
                INNER JOIN 0_kv_empl_info AS b ON b.id=a.emp_id      
                WHERE DATEDIFF(a.expiry_date,CURDATE()) < ".$days_interval."  ";
            if($doc_type_id!='0')
            {
                $b_sql .=" AND a.type_id='".$doc_type_id."' ";
            }

            if(!in_array($_SESSION['wa_current_user']->access, [2,17,16,24]))
            {
                $b_sql .=" AND a.dept_id IN (".rtrim($dept_ids,',').") ";
            }

        $b_sql .=" ORDER BY expirein ASC LIMIT ".$start.",".$length." ";


       //echo $b_sql.' ---- ';
         
        $res=db_query($b_sql);

        $color='';
        $data=array();
        while($doc_exp_data=db_fetch($res))
        {
          if($doc_exp_data['expirein']<0)
            {
                $doc_exp_data['expirein']='0';
                $color='style="color:#f52929a8"';
            }
            else if($doc_exp_data['expirein']<=15 && $doc_exp_data['expirein']!=0)
            {
                $color='style="color:green"';
            }


            $data[] = array(
                '<div '.$color.'>'.$doc_exp_data['empname'].'</div>',
                '<div '.$color.'>'.$doc_exp_data['description'].'</div>',
                '<div '.$color.'>'.date('d-m-Y',strtotime($doc_exp_data['expiry_date'])).'</div>',
                '<div '.$color.'>'.$doc_exp_data['expirein'].'</div>'

            );     
        }


         $sql_tot="SELECT a.id
               FROM 
                0_employee_docs AS a
                INNER JOIN 0_kv_empl_info AS b ON b.id=a.emp_id      
                WHERE DATEDIFF(a.expiry_date,CURDATE()) < ".$days_interval." ";
         if($doc_type_id!='0')
            {
                $sql_tot .=" AND a.type_id='".$doc_type_id."' ";
            }

         if(!in_array($_SESSION['wa_current_user']->access, [2,17,16,24]))
            {
                $sql_tot .=" AND a.dept_id IN (".rtrim($dept_ids,',').") ";
            }         
        $tot_result = db_query($sql_tot);

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($tot_result),
            "recordsFiltered" => db_num_rows($tot_result),
            "data" => $data
        );



        return AxisPro::SendResponse($result_data);


    }

    // public function get_expirying_doc_details()
    // {

    //   $s_sql="SELECT a.department 
    //                 FROM 0_kv_empl_job AS a
    //                 INNER JOIN 0_users AS b ON b.employee_id=a.empl_id
    //                 WHERE b.id='".$_SESSION['wa_current_user']->user."'";
    //     $dept_id=db_fetch(db_query($s_sql));



    //     $b_sql="SELECT CONCAT(b.empl_id,' - ',b.empl_firstname) as empname,a.description,a.expiry_date,DATEDIFF(a.expiry_date,CURDATE()) as expirein
    //            FROM 
    //             0_employee_docs AS a
    //             INNER JOIN 0_kv_empl_info AS b ON b.id=a.emp_id		 
    //             WHERE DATEDIFF(a.expiry_date,CURDATE()) <5 ";
    //     if(!in_array($_SESSION['wa_current_user']->access,[2,17,16,24,25]))
    //     {
    //         $b_sql .=" AND a.dept_id='".$dept_id[0]."' ";
    //     }
    //     $res=db_query($b_sql);
    //     $hmtl="<table border='1px solid #ccc;' style='width:100%;'>
    //             <tr>
    //                <th style='padding: 8px;text-align: center;'>Employee</th>
    //                <th style='padding: 8px;text-align: center;'>Doc.</th>
    //                <th style='padding: 8px;text-align: center;'>Expire Date</th>
    //                <th style='padding: 8px;text-align: center;'>Expire In</th>
    //             </tr>
    //          ";
    //     $color='';
    //     while($birth_cnt=db_fetch($res))
    //     {
    //         if($birth_cnt['expirein']<0)
    //         {
    //             $birth_cnt['expirein']='0';
    //             $color='style="background-color:#f52929a8;color: #fff;"';
    //         }
    //         $hmtl.="<tr $color>
    //                   <td style='text-align: center;'>".$birth_cnt['empname']."</td>
    //                   <td style='text-align: center;'>".$birth_cnt['description']."</td>
    //                   <td style='text-align: center;'>".date('d-m-Y',strtotime($birth_cnt['expiry_date']))."</td>
    //                   <td style='text-align: center;'>".$birth_cnt['expirein']."</td>
    //                 </tr>";
    //     }

    //     $hmtl.=" </table>";
    //     return $hmtl;
    // }



    public function create_issue_warning()
    {
        $dept_id=$_POST['dept_id'];
        $empl_id=$_POST['empl_id'];
        $ded_amount=$_POST['ded_amount'];
        $desc=$_POST['desc'];
        $startdate=$_POST['startdate'];
        $id=$_POST['edit_id'];
        $msg='';
        $flag='';
        if($id=='0')
        {
            $sql="INSERT into 0_kv_empl_warings (dept_id,emp_id,ded_starting,ded_amount,`desc`,created_by,created_date,active)
                values ('".$dept_id."','".$empl_id."','".date('Y-m-d',strtotime($startdate))."','".$ded_amount."','".$desc."',
                '".$_SESSION['wa_current_user']->user."','".date('Y-m-d H:i:s')."','1')";

            if(db_query($sql))
            {
                $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                $flag='1';
            }
            else
            {
                $msg=['status'=>'Error','msg'=>'Error occured while saving'];
            }
        }
        else
        {
            $update="Update 0_kv_empl_warings set dept_id='".$dept_id."',emp_id='".$empl_id."',ded_starting='".date('Y-m-d',strtotime($startdate))."',
          ded_amount='".$ded_amount."',`desc`='".$desc."',updated_by='".$_SESSION['wa_current_user']->user."',updated_on='".date('Y-m-d H:i:s')."' where id='".$id."'";

            if(db_query($update))
            {
                $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
            }
            else
            {
                $msg=['status'=>'Error','msg'=>'Error occured while saving'];
            }
        }


        if($flag=='1')
        {
            $this->sending_mail($empl_id,$ded_amount);
        }
        return AxisPro::SendResponse($msg);


    }



    public function list_warnings()
    {


        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $root_url=str_replace("\ERP","",getcwd());
        $root_url=str_replace("\API","",$root_url);

        $get_emp_user="select employee as id from 
                       0_users where id='".$_SESSION['wa_current_user']->user."'";
        $emp_id=db_fetch(db_query($get_emp_user));

        $sql="SELECT  d.description,CONCAT(n.empl_firstname,' '+n.empl_lastname) AS EMpname
,w.ded_amount,w.ded_starting,w.deducted_amount,w.desc,w.id,w.dept_id,w.emp_id
From 0_kv_empl_warings as w
Inner join 0_kv_empl_info as n on n.id=w.emp_id
INNER JOIN 0_kv_empl_departments AS d ON d.id=w.dept_id
WHere w.active='1' and w.active='1'";
        if($_SESSION['wa_current_user']->access!='2')
        {
            $sql.=" and w.created_by='".$_SESSION['wa_current_user']->user."'";
        }


        $sql.=" LIMIT ".$start.",".$length." ";



        $result = db_query($sql);
        $data=array();
        $status='';
        while ($myrow = db_fetch_assoc($result)) {
            $alt_attr="alt='".$myrow['id']."' dept_id='".$myrow['dept_id']."' alt_ded_amount='".$myrow['ded_amount']."'
                       alt_desc='".$myrow['desc']."' alt_start_date='".date('m/d/Y',strtotime($myrow['ded_starting']))."'
                       alt_empl_id='".$myrow['emp_id']."' ";


            $data[] = array(
                $myrow['description'],
                $myrow['EMpname'],
                $myrow['ded_amount'],
                $myrow['desc'],
                date('d-m-Y',strtotime($myrow['ded_starting'])),
                '<label class="ClsEdit" style="cursor: pointer;"  '.$alt_attr.' data-toggle="modal" data-target="#myModal"><i class=\'flaticon-edit\'></i></label> 
                <label class="ClsRemove" style="cursor: pointer;"  '.$alt_attr.'><i class=\'flaticon-delete\'></i></label>'

            );
        }

        $sql_tot="SELECT * FROM 0_kv_empl_docu_request_details WHERE active='1' ";
        if($_SESSION['wa_current_user']->access!='2')
        {
            $sql_tot .=" AND a.empl_id='".$_SESSION['wa_current_user']->user."'";
        }
        $tot_result = db_query($sql_tot);

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($tot_result),
            "recordsFiltered" => db_num_rows($tot_result),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);


    }


    public function remove_warning()
    {
        if(!empty($_POST['remove_id']))
        {
            $sql="UPDATE 0_kv_empl_warings set active='0' where id='".$_POST['remove_id']."'";
            if(db_query($sql))
            {
                $msg=['status'=>'Success','msg'=>'Removed Successfully'];
            }
            else
            {
                $msg=['status'=>'Error','msg'=>'Error occured while saving'];
            }

            return AxisPro::SendResponse($msg);
        }

    }

    public function sending_mail($empl_id,$ded_amount)
    {
        $path_to_root = "..";
        include_once($path_to_root . "/API/HRM_Mail.php");
        $hrm_mail=new HRM_Mail();

        $emp_data_sql="select CONCAT(empl_firstname,' ',empl_lastname) as emp_name,email from 0_kv_empl_info where id='".$empl_id."'";
        $employee_data=db_fetch(db_query($emp_data_sql));



        $to=$employee_data[1];
        $sub="Warning Letter";
        $content= "<div>
                                   <label>Hi ".$employee_data[0].",</label></br>
                                   <div style='height: 39px;
                                    margin-top: 33px;'>There is one warning letter announced against to you. ".number_format($ded_amount,2)." amount will
                                                       be deducted form your salary.</div>
                                    <div style='margin-top: 1%;'>
                                     
                                    <div style='margin-top:2%;'>
                                    <div>Thanks,</div>
                                    <div>HR Department</div>
                                    </div>
                                    
                               </div>";

        $res=$hrm_mail->send_mail($to,$sub,$content);

        //print_r($res);
    }

    public function get_top_leave_request()
    {
        $b_sql="SELECT  m.description,a.reason,DATE_FORMAT(a.DATE,'%d-%m-%Y') AS fromdate,
                DATE_FORMAT(DATE(a.t_date),'%d-%m-%Y') AS todate,a.days,
                 a.id,a.leave_type,CONCAT(b.empl_id,' - ',b.empl_firstname) as EmpName,a.req_status
                FROM 0_kv_empl_leave_applied as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
				LEFT JOIN 0_kv_empl_leave_types AS m ON m.id=a.leave_type
                left join 0_users as c ON c.employee_id=b.id
				WHERE a.del_status='1' ";
        if($_SESSION['wa_current_user']->access!='2')
        {
            $b_sql.= " AND c.id='".$_SESSION['wa_current_user']->user."' " ;
        }
        $b_sql.=" ORDER BY a.id DESC LIMIT 10";
        $res=db_query($b_sql);
        $hmtl="<table class='table' style='width:100%;'>
<thead class=\"thead-dark\">
                <tr>
                   <th style='padding: 8px;text-align: center;'>Emp.Name</th>
                   <th style='padding: 8px;text-align: center;'>Leave Type</th>
                   <th style='padding: 8px;text-align: center;'>Reason</th>
                   <th style='padding: 8px;text-align: center;'>Start Date</th>
                   <th style='padding: 8px;text-align: center;'>End Date</th>
                   <th style='padding: 8px;text-align: center;'>Days Requested</th> 
                   <th style='padding: 8px;text-align: center;'>Current Status</th>
                   <th></th>
                </tr>
                </thead>
                 <tbody>
             ";
        $color='';
        $status='';
        while($leave_data=db_fetch($res))
        {
            $alt_attr="alt='".$leave_data['id']."' alt_type='1' ";

            if($leave_data['req_status']=='0' || $leave_data['req_status']=='')
            {
                $status='<label style="color:red;font-weight:bold;">Pending</label>';
            }
            else if($leave_data['req_status']=='1')
            {
                $status='<label style="color:green;font-weight:bold;">Approved</label>';
            }
            else if($leave_data['req_status']=='2')
            {
                $status='<label style="color:black;font-weight:bold;">Rejected</label>';
            }


            $hmtl.="<tr $color>
                      <td style='text-align: center;'>".$leave_data['EmpName']."</td>  
                      <td style='text-align: center;'>".$leave_data['description']."</td>
                      <td style='text-align: center;'>".$leave_data['reason']."</td>
                      <td style='text-align: center;'>".date('d-m-Y',strtotime($leave_data['fromdate']))."</td>
                      <td style='text-align: center;'>".date('d-m-Y',strtotime($leave_data['todate']))."</td>
                      <td style='text-align: center;'>".$leave_data['days']."</td>
                      <td style='text-align: center;'>".$status."</td>
                      <td><label style='text-decoration: underline;color:blue;cursor:pointer;' class='ClsviewHistory' ".$alt_attr.">View history</label></td>
                    </tr>";
        }

        $hmtl.="  </tbody></table>";
        return $hmtl;
    }


    public function get_attendences()
    {

        $emp_data_sql="select b.empl_id as empl_id 
                       from 0_users as a
                       INNER JOIN 0_kv_empl_info as b ON b.id=a.employee_id
                       where a.id='".$_SESSION['wa_current_user']->user."'";
        $employee_data=db_fetch(db_query($emp_data_sql));

$repl=(int)$employee_data[0];

        $b_sql="SELECT empl_id,a_date,in_time,out_time
                FROM 0_kv_empl_attendance
                WHERE empl_id='".$repl."'  ORDER BY a_date DESC LIMIT 10           
                       ";



        $res=db_query($b_sql);
        $hmtl="<table class='table' style='width:100%;'>
<thead class=\"thead-dark\">
                <tr>
                   <th style='padding: 8px;text-align: center;'>Emp ID</th>
                   <th style='padding: 8px;text-align: center;'>Shift Date</th>
                   <th style='padding: 8px;text-align: center;'>Punch In Time</th>
                   <th style='padding: 8px;text-align: center;'>Punch Out Time</th>
                    
                </tr>
                </thead>
                 <tbody>
             ";
        $color='';
        $status='';
        while($atten_data=db_fetch($res))
        {

            $hmtl.="<tr $color>
                      <td style='text-align: center;'>".$atten_data['empl_id']."</td>  
                      <td style='text-align: center;'>".date('d-m-Y',strtotime($atten_data['a_date']))."</td>
                      <td style='text-align: center;'>".$atten_data['in_time']."</td>
                      <td style='text-align: center;'>".$atten_data['out_time']."</td>
                    </tr>";
        }

        $hmtl.="  </tbody></table>";
        return $hmtl;
    }



    public function other_doc_requests()
    {

        $type=$_POST['type_id'];

        if($type=='3')
        {
            $b_sql="SELECT   a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,'' as language
                FROM 0_kv_empl_passport_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                left join 0_users as g ON g.employee_id=b.id
                WHERE a.status='1' ";
        }

        if($type=='2')
        {
            $b_sql="SELECT a.certifcate_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.language
                FROM 0_kv_empl_certificate_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                left join 0_users as g ON g.employee_id=b.id
                WHERE a.status='1' ";
        }

        if($type=='5')
        {
            $b_sql="SELECT a.noc_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,'' as language
                FROM 0_kv_empl_noc_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                left join 0_users as g ON g.employee_id=b.id
                WHERE a.status='1' ";
        }

        if($type=='6')
        {
            $b_sql="SELECT   CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname
                ,a.req_status,a.id,r.`name` AS cat_name,c.description AS type_name,a.comments,a.model,a.model_number,
                a.serial_number,a.request_ref_no,a.category_id,a.type_id
                FROM 0_kv_asset_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                INNER JOIN 0_kv_asset_category AS r ON r.id=a.category_id
                INNER JOIN 0_kv_company_asset AS c ON c.id=a.type_id
                left join 0_users as g ON g.employee_id=b.id
                WHERE a.status='1'";
        }

        if($type=='4')
        {
            $b_sql="SELECT   a.request_ref_no,a.`loan_required_date` as requestdate,a.description as comments,a.created_on as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level,a.installment_count
                , a.loan_amount
                FROM 0_kv_empl_loan_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                left join 0_users as g ON g.employee_id=b.id
                WHERE a.status='1' ";
        }



        if($_SESSION['wa_current_user']->access!='2')
        {
            $b_sql.= " AND g.id='".$_SESSION['wa_current_user']->user."' " ;
        }
        $b_sql.=" ORDER BY a.id DESC LIMIT 20";


        //echo $_SESSION['wa_current_user']->access;

       //echo $b_sql;

        $res=db_query($b_sql);

        if($type=='6') {
            $hmtl="<table class='table' style='width:100%;'>
<thead class=\"thead-dark\">
                <tr>
                   <th style='padding: 8px;text-align: center;'>Ref.No</th>
                   <th style='padding: 8px;text-align: center;'>Emp. Name</th>
                   <th style='padding: 8px;text-align: center;'>Category</th>
                   <th style='padding: 8px;text-align: center;'>Asset Type</th>
                   <th style='padding: 8px;text-align: center;'>Model</th>
                   <th style='padding: 8px;text-align: center;'>Model Number</th>
                   <th style='padding: 8px;text-align: center;'>Serial Number</th>
                    <th style='padding: 8px;text-align: center;'>Comment</th>
                    <th style='padding: 8px;text-align: center;'>Status</th>
                    
                    <th></th>
                </tr>
                </thead>
                 <tbody>
             ";
        }
        else if($type=='4')
        {
            $hmtl="<table class='table' style='width:100%;'>
<thead class=\"thead-dark\">
                <tr>
                     <th style='padding: 8px;text-align: center;'>Ref.No</th>
                     <th style='padding: 8px;text-align: center;'>Emp. Name</th>
                     <th style='padding: 8px;text-align: center;'>Loan Reason</th>
                     <th style='padding: 8px;text-align: center;'>Amount</th>
                     <th style='padding: 8px;text-align: center;'>Installment Count</th>
                     <th style='padding: 8px;text-align: center;'>Required Date</th>
                     <th style='padding: 8px;text-align: center;'>Status</th>
                     <th style='padding: 8px;text-align: center;'>Approval History</th>
                     <th></th>
                </tr>
                </thead>
                 <tbody>
             ";
        }
        else
        {
            $hmtl="<table class='table' style='width:100%;'>
<thead class=\"thead-dark\">
                <tr>
                   <th style='padding: 8px;text-align: center;'>Ref.No</th>
                   <th style='padding: 8px;text-align: center;'>Emp. Name</th>
                   <th style='padding: 8px;text-align: center;'>Request Date</th>
                   <th style='padding: 8px;text-align: center;'>Comment</th>
                   <th style='padding: 8px;text-align: center;'>Request Creation Date</th>
                   <th style='padding: 8px;text-align: center;'>Status</th>
                   <th style='padding: 8px;text-align: center;'>Approval History</th>
                   <th></th>
                </tr>
                </thead>
                 <tbody>
             ";
        }




        $color='';
        $status='';
        while($doc_data=db_fetch($res))
        {

            $alt_attr="alt='".$doc_data['id']."' alt_type='".$type."' ";

            if($doc_data['req_status']=='0' || $doc_data['req_status']=='')
            {
                $status='<label style="color:red;font-weight:bold;">Pending</label>';
            }
            else if($doc_data['req_status']=='1')
            {
                $status='<label style="color:green;font-weight:bold;">Approved</label>';
            }
            else if($doc_data['req_status']=='2')
            {
                $status='<label style="color:black;font-weight:bold;">Rejected</label>';
            }

            if($type=='6') {

                $hmtl.="<tr $color>
        <td style='text-align: center;color:blue;font-weight:bold;'>".$doc_data['request_ref_no']."</td>
                       <td style='text-align: center;'>".$doc_data['empname']."</td>
                      <td style='text-align: center;'>".$doc_data['cat_name']."</td>
                      <td style='text-align: center;'>".$doc_data['type_name']."</td>
                      <td style='text-align: center;'>".$doc_data['model']."</td>
                      <td style='text-align: center;'>".$doc_data['model_number']."</td>
                      <td style='text-align: center;'>".$doc_data['serial_number']."</td>
                      <td style='text-align: center;'>".$doc_data['comments']."</td>
                      <td style='text-align: center;'>".$status."</td>";
                if($_POST['from_page']==1)
                {
                    $hmtl.="  <td><button alt_action='1' class='btnupdate' ".$alt_attr." >Approve</button>&nbsp;
                 <button alt_action='2' class='btnupdate' ".$alt_attr.">Cancel</button></td>";
                }


                $hmtl.="</tr>";

            }
            else if($type=='4')
            {
                $hmtl.="<tr $color>
                        <td style='text-align: center;color:blue;font-weight:bold;'>".$doc_data['request_ref_no']."</td>
                       <td style='text-align: center;'>".$doc_data['empname']."</td>
                      <td style='text-align: center;'>".$doc_data['comments']."</td>
                      <td style='text-align: center;'>".$doc_data['loan_amount']."</td>
                      <td style='text-align: center;'>".$doc_data['installment_count']."</td>
                      <td style='text-align: center;'>".$doc_data['requestdate']."</td>
                      <td style='text-align: center;'>".$status."</td>
                      <td style='text-align: center;'><label style='text-decoration: underline;color:blue;cursor:pointer;' class='ClsviewHistory' ".$alt_attr.">View history</label></td>";
            }
            else
            {


                    $print_text='';
                    if($type=='2')
                    {

                        if($doc_data['req_status']==1)
                        {
                            $print_text='Print';
                        }

                    }

                $hmtl.="<tr $color>
                        <td style='text-align: center;color:blue;font-weight:bold;'>".$doc_data['request_ref_no']."</td>
                       <td style='text-align: center;'>".$doc_data['empname']."</td>
                      <td style='text-align: center;'>".$doc_data['requestdate']."</td>
                      <td style='text-align: center;'>".$doc_data['comments']."</td>
                      <td style='text-align: center;'>".date('d-m-Y',strtotime($doc_data['created']))."</td>
                      
                      
                      <td style='text-align: center;'>".$status."</td>
                      <td style='text-align: center;'><label style='text-decoration: underline;color:blue;cursor:pointer;' class='ClsviewHistory' ".$alt_attr.">View history</label></td>
                      <td style='text-align: center;'><label class='ClsDocPrint'  style='    cursor: pointer;
    color: blue;
    font-weight: bold;' alt_type='".$type."' alt_request_id='".$doc_data['id']."' alt_sub_form='".$doc_data['certifcate_name']."'
                      alt_language='".$doc_data['language']."'>".$print_text."</label></td>";

              /*  if($_POST['from_page']==1)
                {
                    $hmtl.="  <td><button alt_action='1' class='btnupdate' ".$alt_attr." >Approve</button>&nbsp;
                 <button alt_action='2' class='btnupdate' ".$alt_attr.">Cancel</button></td>";
                }*/

                $hmtl.=" </tr>";
            }


        }

        $hmtl.="  </tbody></table>";

        return AxisPro::SendResponse(['html'=>$hmtl]);
        //return $hmtl;
    }


    public function employee_documents()
    {
        $b_sql="SELECT b.description as Deptname,CONCAT(a.empl_id,' - ',a.empl_firstname) as EmpName,c.description as doc_title
              ,c.issue_date,c.expiry_date,c.filename,c.id,a.empl_id,c.dept_id,c.type_id,d.description as doc_type
                FROM 0_employee_docs AS c
                INNER JOIN 0_kv_empl_departments AS b ON b.id=c.dept_id
                INNER JOIN 0_kv_empl_info AS a ON a.id=c.emp_id
                INNER JOIN 0_kv_empl_doc_type AS d ON d.id=c.type_id
                left join 0_users as e ON e.employee_id=a.id
                WHERE c.status='1'";
        if($_SESSION['wa_current_user']->access!='2')
        {
            $b_sql.= " AND e.id='".$_SESSION['wa_current_user']->user."' " ;
        }
        $b_sql.=" ORDER BY c.id DESC";

        //echo $b_sql;
        $res=db_query($b_sql);
        $hmtl="<table class='table' style='width:100%;'>
<thead class=\"thead-dark\">
                <tr>
                   <th style='padding: 8px;text-align: center;'>Employee Name</th>
                   <th style='padding: 8px;text-align: center;'>Document Type</th>
                   <th style='padding: 8px;text-align: center;'>Title</th>
                   <th style='padding: 8px;text-align: center;'>File</th>
                   
                </tr>
                </thead>
                 <tbody>
             ";

        $color='';
        while($emp_doc_data=db_fetch($res))
        {

            $hmtl.="<tr $color>
                      <td style='text-align: center;'>".$emp_doc_data['EmpName']."</td>
                      <td style='text-align: center;'>".$emp_doc_data['doc_type']."</td>
                      <td style='text-align: center;'>".$emp_doc_data['doc_title']."</td>
                      
                      <td style='text-align: center;'><a href='assets/uploads/".$emp_doc_data['filename']."' target='_blank' >".$emp_doc_data['filename']."</a></td>
                       
         
                    </tr>";
        }

        $hmtl.="  </tbody></table>";
        return $hmtl;
    }

    public function warnings_issued()
    {
        $b_sql="SELECT b.description as Deptname,CONCAT(a.empl_id,' - ',a.empl_firstname) as EmpName,c.ded_starting,
                c.ded_amount,c.deducted_amount,c.`desc` as warningDesc
                FROM 0_kv_empl_warings AS c
                INNER JOIN 0_kv_empl_departments AS b ON b.id=c.dept_id
                INNER JOIN 0_kv_empl_info AS a ON a.id=c.emp_id
                left join 0_users as e ON e.employee_id=a.id
                WHERE c.active='1'";
        if($_SESSION['wa_current_user']->access!='2')
        {
            $b_sql.= " AND e.id='".$_SESSION['wa_current_user']->user."' " ;
        }
        $b_sql.=" ORDER BY c.id DESC";

        //echo $b_sql;
        $res=db_query($b_sql);
        $hmtl="<table class='table' style='width:100%;'>
<thead class=\"thead-dark\">
                <tr>
                   <th style='padding: 8px;text-align: center;'>Department Name</th>
                   <th style='padding: 8px;text-align: center;'>Employee Name</th>
                   <th style='padding: 8px;text-align: center;'>Description</th>
                   <th style='padding: 8px;text-align: center;'>Created Date</th>
                   <th style='padding: 8px;text-align: center;'>Deduction Amount</th>
                   <th style='padding: 8px;text-align: center;'>Total Deducted</th>
                </tr>
                </thead>
                 <tbody>
             ";

        $color='';
        while($warnings_data=db_fetch($res))
        {

            $hmtl.="<tr $color>
                      <td style='text-align: center;'>".$warnings_data['Deptname']."</td>
                      <td style='text-align: center;'>".$warnings_data['EmpName']."</td>
                      <td style='text-align: center;'>".$warnings_data['warningDesc']."</td>
                      <td style='text-align: center;'>".date('Y-m-d',strtotime($warnings_data['ded_starting']))."</td>
                      <td style='text-align: center;'>".number_format($warnings_data['ded_amount'],2)."</td>
                      <td style='text-align: center;'>".number_format($warnings_data['deducted_amount'],2)."</td>
                    </tr>";
        }

        $hmtl.="  </tbody></table>";
        return $hmtl;
    }


    public function get_policy_and_code_of_conduct()
    {

        /*$html.='<div class="container">
                 <div class="row">';*/
        $path_to_root='';
        $sql_policy="select `value` from 0_sys_prefs where name='privacy_policy'";
        $policy_name=db_fetch(db_query($sql_policy));

       /* if($policy_name[0]!='')
        {*/
            /*$url= $path_to_root."assets/uploads/policy_arabic.pdf";
            $html='  
                    <a  href="'.$url.'" target="_blank">
                                       <img src="'.$path_to_root.'assets/images/img_policy_ar.png" style="width: 19%;
    border: 1px solid #ccc;
    padding: 21px;
    border-radius: 21px;"/>
                                      </a>
                                
                  ';*/

               /*     $url= $path_to_root."assets/uploads/policy_eng.pdf";
            $html.='  
                    <a  href="'.$url.'" target="_blank">
                                       <img src="'.$path_to_root.'assets/images/img_policy.png" style="width: 19%;
    border: 1px solid #ccc;
    padding: 21px;
    border-radius: 21px;"/>
                                      </a>
                                
                  ';*/
       // }

       /* $sql_code="select `value` from 0_sys_prefs where name='code_of_conduct'";
        $code_name=db_fetch(db_query($sql_code));*/

        /*if($code_name[0]!='')
        {*/
         /* $sql_code="select `value` from 0_sys_prefs where name='code_of_conduct'";
        $code_name=db_fetch(db_query($sql_code));*/

        /*if($code_name[0]!='')
        {*/
            /*$url_code= $path_to_root."assets/uploads/Induction_Handouts.pdf";
            $html.='  
                  <a  href="'.$url_code.'" target="_blank"><img src="'.$path_to_root.'assets/images/induction.png" style="width: 16%;
    border: 1px solid #ccc;
    padding: 9px;
    border-radius: 21px;"/>
                  </a> ';

        $url_code= $path_to_root."assets/uploads/code_of_cndt_en.pdf";
        $html.='  
                  <a  href="'.$url_code.'" target="_blank"><img src="'.$path_to_root.'assets/images/Emp_code_conduct.png" style="width: 16%;
    border: 1px solid #ccc;
    padding: 9px;
    border-radius: 21px;"/>
                  </a> ';


        $url_code= $path_to_root."assets/uploads/code_of_cndt_ar.pdf";
        $html.='  
                  <a  href="'.$url_code.'" target="_blank"><img src="'.$path_to_root.'assets/images/Emp_code_conduct_ar.png" style="width: 16%;
    border: 1px solid #ccc;
    padding: 9px;
    border-radius: 21px;"/>
                  </a> ';*/

              /*   $url_code= $path_to_root."assets/uploads/annual_leaves.pdf";
            $html.='  
                  <a  href="'.$url_code.'" target="_blank"><img src="'.$path_to_root.'assets/images/annual_leave.png" style="width: 16%;
    border: 1px solid #ccc;
    padding: 9px;
    border-radius: 21px;"/>
                  </a>
                  
                ';*/


                /*$url_code= $path_to_root."assets/uploads/sick_leave.pdf";
            $html.='  
                  <a  href="'.$url_code.'" target="_blank"><img src="'.$path_to_root.'assets/images/sick_leave.png" style="width: 16%;
    border: 1px solid #ccc;
    padding: 9px;
    border-radius: 21px;"/>
                  </a>
                  
                ';*/
        //}

        /*$html.=' </div>
                 </div>';*/

       // return $html;
    }


    public function submit_passport_request()
    {
        $date=date('Y-m-d',strtotime($_POST['ap-datepicker']));
        $comment=str_replace("'","",$_POST['txttasks']);
        $hdn_id=$_POST['hdn_id'];
        $return_date=date('Y-m-d',strtotime($_POST['returndate']));
        $pfx='';
        $pfx_to_ref='';

        $now = strtotime($_POST['returndate']); // or your date as well
        $your_date = strtotime($_POST['ap-datepicker']);

        $datediff = $now - $your_date;

        $day_requested=round($datediff / (60 * 60 * 24));

        if($day_requested>0)
        {
            if(!empty($date))
            {
                $edmp_sql="SELECT a.id,CASE 
   WHEN a.report_to=''
  THEN 0 
  ELSE a.report_to 
 END AS report_to,a.leave_request_forward,a.email,a.empl_firstname,a.empl_id 
                    FROM  0_kv_empl_info AS a
                    INNER JOIN  0_users AS b ON a.id=b.employee_id
                    WHERE b.id=".$_SESSION['wa_current_user']->user." ";
                $result_d = db_query($edmp_sql, "Can't get your allowed user details");
                $rowData= db_fetch($result_d);



                if(!empty($hdn_id))
                {
                    $qry_up="Update 0_kv_empl_passport_request set `date`='".$date."',comments='".$comment."'
                        ,return_date='".$return_date."'  where id='".$hdn_id."'";
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
                    $sql="select `value` from 0_sys_prefs where name='passport_request_pfx'";
                    $data=db_fetch(db_query($sql));

                    if($data[0]!='')
                    {
                        $pfx=$data[0];
                    }

                    $emp_data_sql="select employee_id as id
                          from 0_users where id='".$_SESSION['wa_current_user']->user."'";
                    $employee_data=db_fetch(db_query($emp_data_sql));

                    /**************************GETTING NOC REQUEST FLOW******/
                   // include('API_HRM_Call.php');
                    $call_obj=new API_HRM_Call();
                    $user_dim_id=$call_obj->get_user_dim();

                    $qry="select * from 0_kv_empl_master_request_flow where type_id='3' 
                      and dim_id='".$user_dim_id."' and access_level='".$_SESSION['wa_current_user']->access."' ";
                    $qry_data=db_fetch(db_query($qry));
                    /***********************************END********************/


                    if($rowData[1]=='0')
                    {
                        $qry_data['level_1']='17';
                    }


                    $qry="INSERT INTO 0_kv_empl_passport_request (empl_id,`date`,comments,request_date,status,created_by,return_date,role_id,level)
                 VALUES ('".$employee_data['id']."','".$date."','".$comment."','".date('Y-m-d')."','1'
                        ,'".$_SESSION['wa_current_user']->user."','".$return_date."','".$qry_data['level_1']."','1')";
                    if(db_query($qry))
                    {
                        $insert_id=db_insert_id();
                        $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                        $pfx_to_ref=$pfx.''.$insert_id;
                        $update="update 0_kv_empl_passport_request set request_ref_no='".$pfx_to_ref."' 
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
        else
        {
            $msg=['status'=>'Error','msg'=>'Return date is lessthan the requested date'];
            return AxisPro::SendResponse($msg);
        }

    }


    public function get_passport_requests()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $sql=" SELECT   a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.return_date
                FROM 0_kv_empl_passport_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                left join 0_users as c ON c.employee_id=b.id
                WHERE a.status='1'";

        if($_SESSION['wa_current_user']->user<>'1')
        {
            $sql.=" AND c.id='".$_SESSION['wa_current_user']->user."'";
        }
        $sql.=" LIMIT ".$start.",".$length." ";


        $result = db_query($sql);
        $ref_no='';

        while ($myrow = db_fetch($result)) {

            $attributes='alt_id="'.$myrow['id'].'" alt_comment="'.$myrow['comments'].'" 
                         alt_date="'.date('d-m-Y',strtotime($myrow['requestdate'])).'" alt_return_date="'.date('d-m-Y',strtotime($myrow['return_date'])).'" ';

            $controls='<label class=\'btn btn-sm btn-primary ClsBtnEdit\' '.$attributes.' data-toggle="modal" data-target="#myModal"><i class=\'flaticon-edit\'></i></label>
                       <label class=\'btn btn-sm btn-primary ClsBtnRemove\' alt_id="'.$myrow['id'].'"><i class=\'flaticon-delete\'></i></label>';

            $ref_no='<label style="font-weight: bold;color:blue;">'.$myrow['request_ref_no'].'</label>';
            $data[] = array(
                $ref_no,
                $myrow['empname'],
                date('d-m-Y',strtotime($myrow['requestdate'])),
                date('d-m-Y',strtotime($myrow['return_date'])),
                $myrow['req_status'],
                date('d-m-Y',strtotime($myrow['created'])),
                $myrow['comments'],
                $controls,

            );
        }

        $tot_sql=" SELECT   a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id
                FROM 0_kv_empl_passport_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                left join 0_users as c ON c.employee_id=b.id
                WHERE a.status='1'";

        if($_SESSION['wa_current_user']->user<>'1')
        {
            $tot_sql.=" AND c.id='".$_SESSION['wa_current_user']->user."'";
        }
        $tot_sql=db_query($sql);

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($tot_sql),
            "recordsFiltered" => db_num_rows($tot_sql),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);


    }

    public function remove_requests()
    {
        $msg='';
        if(!empty($_POST['remove_id']))
        {
            if($_POST['type']=='1')
            {
                $sql="UPDATE 0_kv_empl_passport_request set status='0' where id='".$_POST['remove_id']."'";
                if(db_query($sql))
                {
                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                }
            }

            if($_POST['type']=='2')
            {
                $sql="UPDATE 0_kv_empl_certificate_request set status='0' where id='".$_POST['remove_id']."'";
                if(db_query($sql))
                {
                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                }
            }

            if($_POST['type']=='3')
            {
                $sql="UPDATE 0_kv_empl_noc_request set status='0' where id='".$_POST['remove_id']."'";
                if(db_query($sql))
                {
                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                }
            }

            if($_POST['type']=='4')
            {
                $sql="UPDATE 0_kv_asset_request set status='0' where id='".$_POST['remove_id']."'";

                if(db_query($sql))
                {
                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                }
                else
                {
                    $msg=['status'=>'Error','msg'=>'Data saved succesfully'];
                }
            }



        }

        return AxisPro::SendResponse($msg);
    }


    public function submit_certifcate_request()
    {
        $date=date('Y-m-d',strtotime($_POST['ap-datepicker']));
        $comment=str_replace("'","",$_POST['txtcomment']);
        $certifcate_name=str_replace("'","",$_POST['certifcate_name']);
        $bank=str_replace("'","",$_POST['bank']);
        $iban=str_replace("'","",$_POST['iban']);
        $branch=str_replace("'","",$_POST['branch']);
        $language=$_POST['ddl_doc_language'];


        $hdn_id=$_POST['hdn_id'];
        $address_to=$_POST['address_to'];
        $pfx='';
        $pfx_to_ref='';
        if(!empty($date))
        {


            $edmp_sql="SELECT a.id,CASE 
   WHEN a.report_to=''
  THEN 0 
  ELSE a.report_to 
 END AS report_to,a.leave_request_forward,a.email,a.empl_firstname,a.empl_id 
                    FROM  0_kv_empl_info AS a
                    INNER JOIN  0_users AS b ON a.id=b.employee_id
                    WHERE b.id=".$_SESSION['wa_current_user']->user." ";
            $result_d = db_query($edmp_sql, "Can't get your allowed user details");
            $rowData= db_fetch($result_d);

            if(!empty($hdn_id))
            {
                $qry_up="Update 0_kv_empl_certificate_request set `date`='".$date."',comments='".$comment."'
                        ,certifcate_name='".$certifcate_name."',address_to='".$address_to."',bank='".$bank."',
                        iban='".$iban."',branch='".$branch."'
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
                $sql="select `value` from 0_sys_prefs where name='certif_request_pfx'";
                $data=db_fetch(db_query($sql));

                if($data[0]!='')
                {

                    if($certifcate_name=='3')
                    {
                        $pfx=$data[0].'SC/';
                    }
                    else if($certifcate_name=='4')
                    {
                        $pfx=$data[0].'STC/';
                    }
                    
                }

                $emp_data_sql="select employee_id as id
                          from 0_users where id='".$_SESSION['wa_current_user']->user."'";
                $employee_data=db_fetch(db_query($emp_data_sql));

                /**************************GETTING CERTIFICATE REQUEST FLOW******/
                //include('API_HRM_Call.php');
                $call_obj=new API_HRM_Call();
                $user_dim_id=$call_obj->get_user_dim();

                $qry="select * from 0_kv_empl_master_request_flow where type_id='2' 
                      and dim_id='".$user_dim_id."' and access_level='".$_SESSION['wa_current_user']->access."' ";


                $qry_data=db_fetch(db_query($qry));
                /***********************************END********************/
               /* if($rowData[1]=='0')
                {
                    $qry_data['level_1']='17';
                }*/

                $qry="INSERT INTO 0_kv_empl_certificate_request (certifcate_name,empl_id,`date`,comments,request_date,status,created_by,address_to,role_id,level,bank,iban,branch,language)
                 VALUES ('".$certifcate_name."','".$employee_data['id']."','".$date."','".$comment."','".date('Y-m-d')."','1'
                 ,'".$_SESSION['wa_current_user']->user."','".$address_to."','".$qry_data['level_1']."','1',
                  '".$bank."','".$iban."','".$branch."','".$language."')";

                if(db_query($qry))
                {
                    $insert_id=db_insert_id();
                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                    $pfx_to_ref=$pfx.'0'.$insert_id.'/'.date('Y');
                    $update="update 0_kv_empl_certificate_request set request_ref_no='".$pfx_to_ref."' 
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

    public function get_certificate_requests()
    {

        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $sql=" SELECT c.name as certifcate_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.address_to,
                a.bank,a.iban,a.branch,c.id as certificate_id
                FROM 0_kv_empl_certificate_request as a
                inner join `0_kv_empl_certificates` as c ON c.id=a.certifcate_name
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                left join 0_users as g ON g.employee_id=b.id
                WHERE a.status='1'";

        if($_SESSION['wa_current_user']->user<>'1')
        {
            $sql.=" AND g.id='".$_SESSION['wa_current_user']->user."'";
        }
        $sql.=" LIMIT ".$start.",".$length." ";


        $result = db_query($sql);
        $ref_no='';
        $status='';

        while ($myrow = db_fetch($result)) {

            $attributes='alt_id="'.$myrow['id'].'" alt_comment="'.$myrow['comments'].'" 
                         alt_date="'.date('d-m-Y',strtotime($myrow['requestdate'])).'" 
                         cer_name="'.$myrow['certificate_id'].'" alt_address_to="'.$myrow['address_to'].'" 
                         alt_bank="'.$myrow['bank'].'" alt_iban="'.$myrow['iban'].'" alt_branch="'.$myrow['branch'].'" ';

            $controls='<label class=\'btn btn-sm btn-primary ClsBtnEdit\' '.$attributes.' data-toggle="modal" data-target="#myModal"><i class=\'flaticon-edit\'></i></label>
                       <label class=\'btn btn-sm btn-primary ClsBtnRemove\' alt_id="'.$myrow['id'].'"><i class=\'flaticon-delete\'></i></label>';

            $ref_no='<label style="font-weight: bold;color:blue;">'.$myrow['request_ref_no'].'</label>';

            if($myrow['req_status']=='0' || $myrow['req_status']=='')
            {
                $status='<label style="color:red;font-weight:bold;">Pending</label>';
            }
            else if($myrow['req_status']=='1')
            {
                $status='<label style="color:green;font-weight:bold;">Approved</label>';
            }
            else if($myrow['req_status']=='2')
            {
                $status='<label style="color:black;font-weight:bold;">Rejected</label>';
            }



            $data[] = array(
                $ref_no,
                $myrow['certifcate_name'],
                $myrow['address_to'],
                $myrow['empname'],
                /* date('d-m-Y',strtotime($myrow['requestdate'])),*/
                $status,
                date('d-m-Y',strtotime($myrow['created'])),
                $myrow['comments'],
                $controls,

            );
        }

        $total_sql="SELECT a.id
                FROM 0_kv_empl_certificate_request as a
                left JOIN 0_users as b ON a.empl_id=b.employee_id
                WHERE a.status='1'";
        //if($_SESSION['wa_current_user']->user<>'1')
        //{
            $total_sql.=" AND b.id='".$_SESSION['wa_current_user']->user."'";
       // }

        $result_tot = db_query($total_sql);

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($result_tot),
            "recordsFiltered" => db_num_rows($result_tot),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);

    }


    public function submit_noc_request()
    {
        $date=date('Y-m-d',strtotime($_POST['ap-datepicker']));
        $comment=str_replace("'","",$_POST['txtcomment']);
        $certifcate_name=str_replace("'","",$_POST['noc_name']);
        $hdn_id=$_POST['hdn_id'];
        $address_to=str_replace("'","",$_POST['address_to']);
        $pfx='';
        $pfx_to_ref='';
        if(!empty($date))
        {

            $edmp_sql="SELECT a.id,CASE 
   WHEN a.report_to=''
  THEN 0 
  ELSE a.report_to 
 END AS report_to,a.leave_request_forward,a.email,a.empl_firstname,a.empl_id 
                    FROM  0_kv_empl_info AS a
                    INNER JOIN  0_users AS b ON a.id=b.employee_id
                    WHERE b.id=".$_SESSION['wa_current_user']->user." ";
            $result_d = db_query($edmp_sql, "Can't get your allowed user details");
            $rowData= db_fetch($result_d);

            if(!empty($hdn_id))
            {
                $qry_up="Update 0_kv_empl_noc_request set `date`='".$date."',comments='".$comment."'
                        ,noc_name='".$certifcate_name."',address_to='".$address_to."' 
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
                $sql="select `value` from 0_sys_prefs where name='noc_request_pfx'";
                $data=db_fetch(db_query($sql));

                if($data[0]!='')
                {
                    $pfx=$data[0];
                }

                $emp_data_sql="select employee_id as id
                          from 0_users where id='".$_SESSION['wa_current_user']->user."'";
                $employee_data=db_fetch(db_query($emp_data_sql));

                /**************************GETTING NOC REQUEST FLOW******/
                //include('API_HRM_Call.php');
                $call_obj=new API_HRM_Call();
                $user_dim_id=$call_obj->get_user_dim();

                $qry_f="select * from 0_kv_empl_master_request_flow where type_id='5' 
                      and dim_id='".$user_dim_id."' and access_level='".$_SESSION['wa_current_user']->access."' ";
                $qry_data=db_fetch(db_query($qry_f));
                /***********************************END********************/

                if($rowData[1]=='0')
                {
                    $qry_data['level_1']='17';
                }


                $qry="INSERT INTO 0_kv_empl_noc_request (noc_name,empl_id,`date`,comments,request_date,status,created_by,address_to,role_id,level)
                 VALUES ('".$certifcate_name."','".$employee_data['id']."','".$date."','".$comment."'
                        ,'".date('Y-m-d')."','1','".$_SESSION['wa_current_user']->user."','".$address_to."','".$qry_data['level_1']."','1')";
                if(db_query($qry))
                {
                    $insert_id=db_insert_id();
                    $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                    $pfx_to_ref=$pfx.'0'.$insert_id.'/'.date('Y');
                    $update="update 0_kv_empl_noc_request set request_ref_no='".$pfx_to_ref."' 
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

    public function get_noc_requests()
    {

        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $sql=" SELECT a.noc_name as noc_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.address_to
                FROM 0_kv_empl_noc_request as a
 
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                left join 0_users as e ON e.employee_id=b.id 
                WHERE a.status='1'";

        if($_SESSION['wa_current_user']->access<>'2')
        {
            $sql.=" AND e.id='".$_SESSION['wa_current_user']->user."'";
        }
        $sql.=" LIMIT ".$start.",".$length." ";

 
        $result = db_query($sql);
        $ref_no='';
        $status='';

        while ($myrow = db_fetch($result)) {

            $attributes='alt_id="'.$myrow['id'].'" alt_comment="'.$myrow['comments'].'" 
                         alt_date="'.date('d-m-Y',strtotime($myrow['requestdate'])).'" 
                         cer_name="'.$myrow['noc_name'].'" alt_address_to="'.$myrow['address_to'].'" ';

            $controls='<label class=\'btn btn-sm btn-primary ClsBtnEdit\' '.$attributes.' data-toggle="modal" data-target="#myModal"><i class=\'flaticon-edit\'></i></label>
                       <label class=\'btn btn-sm btn-primary ClsBtnRemove\' alt_id="'.$myrow['id'].'"><i class=\'flaticon-delete\'></i></label>';

            $ref_no='<label style="font-weight: bold;color:blue;">'.$myrow['request_ref_no'].'</label>';

if($myrow['req_status']=='0' || $myrow['req_status']=='')
            {
                $status='<label style="color:red;font-weight:bold;">Pending</label>';
            }
            else if($myrow['req_status']=='1')
            {
                $status='<label style="color:green;font-weight:bold;">Approved</label>';
            }
            else if($myrow['req_status']=='2')
            {
                $status='<label style="color:black;font-weight:bold;">Rejected</label>';
            }

            $data[] = array(
                $ref_no,
                $myrow['noc_name'],
                $myrow['address_to'],
                $myrow['empname'],
                // date('d-m-Y',strtotime($myrow['requestdate'])),
                $status,
                date('d-m-Y',strtotime($myrow['created'])),
                $myrow['comments'],
                $controls,

            );
        }

        $total_sql="SELECT a.id
                FROM 0_kv_empl_noc_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                left join 0_users as c on c.employee_id=b.id 
                WHERE a.status='1'";
        if($_SESSION['wa_current_user']->user<>'2')
        {
            $total_sql.=" AND c.id='".$_SESSION['wa_current_user']->user."'";
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


    public function get_asset_category()
    {
        $sql="SELECT id,name
              FROM 0_kv_asset_category";
        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }
        return  $return_result;
    }


    public function get_asset_types()
    {
        $id=$_POST['id'];

        $sql="SELECT id,description
              FROM 0_kv_company_asset where category='".$id."'";
        $result = db_query($sql);

        $select='<select class="form-control"  id="ddl_asset_types" name="ddl_asset_types">
               <option value="0">---Select Asset Types---</option>';
        while ($myrow = db_fetch($result)) {
            $select.='<option value="'.$myrow['id'].'">'.$myrow['description'].'</option>';
        }
        $select.='</select>';



        return AxisPro::SendResponse(["html"=>$select]);

    }


    public function submit_asset_request()
    {
        $asset_cat=$_POST['ddl_asset_cate'];
        $asset_types=$_POST['ddl_asset_types'];
        $model=$_POST['model'];
        $model_number=$_POST['model_number'];
        $serial_number=$_POST['serial_number'];
        $txt_commnts=$_POST['txt_commnts'];
        $edit_id=$_POST['EDIT_id'];
        $request_type=$_POST['request_type'];
        $msg='';
        $pfx='';

        if(empty($edit_id))
        {
            /********************GET PREFIX****************/
            if($request_type=='1')
            {
                $sql="select `value` from 0_sys_prefs where name='asset_request_pfx'";
            }
            if($request_type=='2')
            {
                $sql="select `value` from 0_sys_prefs where name='asset_return_req_pfx'";
            }

            $data=db_fetch(db_query($sql));

            if($data[0]!='')
            {
                $pfx=$data[0];
            }


            $emp_data_sql="select employee_id as id
                          from 0_users where id='".$_SESSION['wa_current_user']->user."'";
            $employee_data=db_fetch(db_query($emp_data_sql));


            /**************************GETTING REQUEST FLOW******/

            //include('API_HRM_Call.php');
            $call_obj=new API_HRM_Call();
            $user_dim_id=$call_obj->get_user_dim();

            $qry="select * from 0_kv_empl_master_request_flow where type_id='6' 
                      and dim_id='".$user_dim_id."' and access_level='".$_SESSION['wa_current_user']->access."' ";
            $qry_data=db_fetch(db_query($qry));

            /***********************************END********************/


            $sql="INSERT INTO 0_kv_asset_request(category_id,empl_id,type_id,model,model_number,serial_number,comments,created_by,created_date,status,request_type,role_id,level)
          VALUES ('".$asset_cat."','".$employee_data['id']."','".$asset_types."','".$model."','".$model_number."','".$serial_number."',
          '".$txt_commnts."','".$_SESSION['wa_current_user']->user."','".date('Y-m-d')."','1','".$request_type."','".$qry_data['level_1']."','1')";
            if(db_query($sql))
            {
                $insert_id=db_insert_id();
                $msg=['status'=>'Success','msg'=>'Data saved succesfully'];
                $pfx_to_ref=$pfx.''.$insert_id;
                $update="update 0_kv_asset_request set request_ref_no='".$pfx_to_ref."' 
                        where id='".$insert_id."'";
                db_query($update);

            }
            else
            {
                $msg=['status'=>'Error','msg'=>'Error occured while saving data'];
            }
        }
        else
        {
            $sql="Update 0_kv_asset_request set category_id='".$asset_cat."',type_id='".$asset_types."',model='".$model."',
               model_number='".$model_number."',serial_number='".$serial_number."',comments='".$txt_commnts."',updated_by='".$_SESSION['wa_current_user']->user."'
               ,updated_on='".date('Y-m-d')."' where id='".$edit_id."'";
            if(db_query($sql))
            {
                $msg=['status'=>'Success','msg'=>'Data Saved Successfully'];
            }
            else
            {
                $msg=['status'=>'Error','msg'=>'Error occured while saving data'];
            }

        }

        return AxisPro::SendResponse($msg);

    }


    public function get_asset_requests()
    {

        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $request_type=$_POST['request_type'];

        $sql="SELECT   CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname
                ,a.req_status,a.id,r.`name` AS cat_name,c.description AS type_name,a.comments,a.model,a.model_number,
                a.serial_number,a.request_ref_no,a.category_id,a.type_id
                FROM 0_kv_asset_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                INNER JOIN 0_kv_asset_category AS r ON r.id=a.category_id
                INNER JOIN 0_kv_company_asset AS c ON c.id=a.type_id
                left join 0_users as g ON g.employee_id=b.id
                WHERE a.status='1'";

        if(!empty($request_type))
        {
            $sql.=" AND a.request_type='".$request_type."'";
        }

        if($_SESSION['wa_current_user']->user<>'1')
        {
            $sql.=" AND g.id='".$_SESSION['wa_current_user']->user."'";
        }
        $sql.=" LIMIT ".$start.",".$length." ";

//echo $sql;
        $result = db_query($sql);
        $ref_no='';

        while ($myrow = db_fetch($result)) {

            $attributes='alt_id="'.$myrow['id'].'" alt_comment="'.$myrow['comments'].'" alt_category="'.$myrow['category_id'].'"
                         alt_typname="'.$myrow['type_id'].'" alt_model="'.$myrow['model'].'" alt_serial_num="'.$myrow['serial_number'].'"
                          alt_model_num="'.$myrow['model_number'].'" ';

            $controls='<label class=\'btn btn-sm btn-primary ClsBtnEdit\' '.$attributes.' data-toggle="modal" data-target="#myModal"><i class=\'flaticon-edit\'></i></label>
                       <label class=\'btn btn-sm btn-primary ClsBtnRemove\' alt_id="'.$myrow['id'].'"><i class=\'flaticon-delete\'></i></label>';

            $ref_no='<label style="font-weight: bold;color:blue;">'.$myrow['request_ref_no'].'</label>';
            $data[] = array(
                $ref_no,
                $myrow['empname'],
                $myrow['cat_name'],
                $myrow['type_name'],
                $myrow['model'],
                $myrow['model_number'],
                $myrow['serial_number'],
                $myrow['comments'],
                $controls,

            );
        }

        $total_sql="SELECT a.id
                FROM 0_kv_asset_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                left join 0_users as c ON c.employee_id=b.id
                WHERE a.status='1'";
        if($_SESSION['wa_current_user']->user<>'1')
        {
            $sql.=" AND c.id='".$_SESSION['wa_current_user']->user."'";
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

    public function get_number_of_days()
    {
        $now = strtotime($_POST['toDate']); // or your date as well
        $your_date = strtotime($_POST['f_date']);

        $datediff = $now - $your_date;

        $day_requested=round($datediff / (60 * 60 * 24));

        return AxisPro::SendResponse(['days_requested'=>$day_requested+1]);
    }




    public function verify_approve_requests()
    {

        $type=$_POST['type_id'];
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);


        $ry="select employee_id as id from 0_users 
             where id='".$_SESSION['wa_current_user']->user."'";
        $get_user_id=db_fetch(db_query($ry));

       /* $check_dept="SELECT b.head_of_dept,b.department from 0_kv_empl_info AS a
                     INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                     WHERE b.head_of_dept='1' and user_id='".$_SESSION['wa_current_user']->user."'";*/

        $check_dept="SELECT d.id
                      from 0_kv_empl_info AS a
                      INNER JOIN 0_kv_empl_departments AS d ON a.id=d.head_of_empl_id
                      left join 0_users as c ON c.employee_id=a.id
                      WHERE c.id='".$_SESSION['wa_current_user']->user."' "; 
        $check_dept_res=db_query($check_dept); 
        $rows_chk_dept=db_num_rows($check_dept_res);


        if($type=='3')
        {

           /* if($_SESSION['wa_current_user']->access=='2')
            {
                $b_sqls="SELECT a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_passport_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                WHERE a.status='1' AND a.req_status IS NULL LIMIT ".$start.",".$length." ";
                $res=db_query($b_sqls);
            }
            else
            {*/
                $Q="SELECT count(a.id) as CntReq
                FROM 0_kv_empl_passport_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NULL and b.report_to='".$get_user_id[0]."'";
                $row_data=db_fetch(db_query($Q));
                if($row_data['CntReq']>0)
                {
                    $b_sql="SELECT   a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_passport_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' ";
                    $b_sql.=" and a.role_id='1111' ";
                    $b_sql.=" AND a.req_status IS NULL and b.report_to='".$get_user_id[0]."'
                LIMIT ".$start.",".$length." ";
                    $res=db_query($b_sql);
                }
                else if($rows_chk_dept>0)
                {
                    $dept_ids='';
                    while($data_dept=db_fetch($check_dept_res))
                      {
                        $dept_ids.=$data_dept['0'].',';
                      } 
 
            
                 $b_sqls="SELECT a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                           ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                           FROM 0_kv_empl_passport_request as a
                           left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                           WHERE a.status='1' and a.role_id='2222' AND a.req_status IS NULL 
                           and a.empl_id IN (SELECT g.empl_id 
                           FROM 0_kv_empl_job AS g
                           WHERE g.department IN (".rtrim($dept_ids,",").")) LIMIT ".$start.",".$length." ";
                     
                    $res=db_query($b_sqls);
                }
                else
                {
                    $b_sqls="SELECT a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_passport_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                WHERE a.status='1' AND a.req_status IS NULL and a.role_id='".$_SESSION['wa_current_user']->access."'
                LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
            //}


        }

        if($type=='4')
        {
            /*if($_SESSION['wa_current_user']->access=='2')
            {
                $b_sqls="SELECT   a.request_ref_no,a.`loan_required_date` as requestdate,a.description as comments,a.created_on as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level, a.loan_amount
                FROM 0_kv_empl_loan_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' AND a.req_status IS NULL LIMIT ".$start.",".$length." ";

                $res=db_query($b_sqls);
            }
            else
            {*/
                $Q="SELECT count(a.id) as CntReq
                FROM 0_kv_empl_loan_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NULL and b.report_to='".$get_user_id[0]."'";
                $row_data=db_fetch(db_query($Q));

                if($row_data['CntReq']>0)
                {
                    $b_sql="SELECT   a.request_ref_no,a.`loan_required_date` as requestdate,a.description as comments,a.created_on as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level,a.installment_count
                , a.loan_amount
                FROM 0_kv_empl_loan_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NULL and b.report_to='".$get_user_id[0]."' 
                LIMIT ".$start.",".$length." ";
                    $res=db_query($b_sql);

                }
                else if($rows_chk_dept>0)
                {
                    $dept_ids='';
                    while($data_dept=db_fetch($check_dept_res))
                      {
                        $dept_ids.=$data_dept['0'].',';
                      }


                    $b_sqls="SELECT   a.request_ref_no,a.`loan_required_date` as requestdate,a.description as comments,a.created_on as created
                        ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level, a.loan_amount
                         FROM 0_kv_empl_loan_request as a
                         left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                         WHERE a.status='1' and a.role_id='2222' AND a.req_status IS NULL and a.empl_id IN 
                         (SELECT g.empl_id 
                         FROM 0_kv_empl_job AS g
                         WHERE g.department IN (".rtrim($dept_ids,",").")) LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
                else
                {
                    $b_sqls="SELECT   a.request_ref_no,a.`loan_required_date` as requestdate,a.description as comments,a.created_on as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level, a.loan_amount
                FROM 0_kv_empl_loan_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' AND a.req_status IS NULL and a.role_id='".$_SESSION['wa_current_user']->access."'
                LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
           // }


        }

        if($type=='2')
        {

           /* if($_SESSION['wa_current_user']->access=='2')
            {
                $b_sqls="SELECT a.certifcate_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_certificate_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' AND a.req_status IS NULL LIMIT ".$start.",".$length." ";

                $res=db_query($b_sqls);
            }
            else
            {*/
                $Q="SELECT count(a.id) as CntReq
                FROM 0_kv_empl_certificate_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NULL and b.report_to='".$get_user_id[0]."'";

                $row_data=db_fetch(db_query($Q));
                if($row_data['CntReq']>0)
                {
                    $b_sql="SELECT a.certifcate_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_certificate_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NULL and b.report_to='".$get_user_id[0]."' 
                LIMIT ".$start.",".$length." ";
                    $res=db_query($b_sql);
                }
                else if($rows_chk_dept>0)
                {
                   $dept_ids='';
                    while($data_dept=db_fetch($check_dept_res))
                      {
                        $dept_ids.=$data_dept['0'].',';
                      }
                    

                    $b_sqls="SELECT a.certifcate_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                        ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                        FROM 0_kv_empl_certificate_request as a
                        left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                        WHERE a.status='1' and a.role_id='2222' AND a.req_status IS NULL and a.empl_id IN (SELECT g.empl_id 
                         FROM 0_kv_empl_job AS g
                         WHERE g.department IN (".rtrim($dept_ids,",").")) LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
                else
                {
                    $b_sqls="SELECT a.certifcate_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_certificate_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' AND a.req_status IS NULL and a.role_id='".$_SESSION['wa_current_user']->access."'
                LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
            //}

        }

        if($type=='5')
        {
            /*if($_SESSION['wa_current_user']->access=='2')
            {
                $b_sqls="SELECT a.noc_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_noc_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' AND a.req_status IS NULL 
                LIMIT ".$start.",".$length." ";

                $res=db_query($b_sqls);
            }
            else
            {*/
                $Q="SELECT count(a.id) as CntReq
                FROM 0_kv_empl_noc_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NULL and b.report_to='".$get_user_id[0]."'";
                $row_data=db_fetch(db_query($Q));
                if($row_data['CntReq']>0)
                {
                    $b_sql="SELECT a.noc_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_noc_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NULL and b.report_to='".$get_user_id[0]."' 
                LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sql);
                }
                else if($rows_chk_dept>0)
                {

                    $b_sqls="SELECT a.noc_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_noc_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='2222' AND a.req_status IS NULL and a.empl_id IN (SELECT g.empl_id 
                         FROM 0_kv_empl_job AS g
                         WHERE g.department IN (".rtrim($dept_ids,",").")) LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
                else
                {
                    $b_sqls="SELECT a.noc_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_noc_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' AND a.req_status IS NULL and a.role_id='".$_SESSION['wa_current_user']->access."' 
                LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
           // }


        }

        if($type=='6')
        {

           /* if($_SESSION['wa_current_user']->access=='2')
            {
                $b_sqls="SELECT   CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname
                ,a.req_status,a.id,r.`name` AS cat_name,c.description AS type_name,a.comments,a.model,a.model_number,
                a.serial_number,a.request_ref_no,a.category_id,a.type_id,a.level
                FROM 0_kv_asset_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                INNER JOIN 0_kv_asset_category AS r ON r.id=a.category_id
                INNER JOIN 0_kv_company_asset AS c ON c.id=a.type_id
                WHERE a.status='1' AND a.req_status IS NULL
                LIMIT ".$start.",".$length." ";

                $res=db_query($b_sqls);
            }
            else
            {*/
                $Q="SELECT count(a.id) as CntReq
                FROM 0_kv_asset_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NULL and b.report_to='".$get_user_id[0]."'";
                $row_data=db_fetch(db_query($Q));
                if($row_data['CntReq']>0) {
                    $b_sql="SELECT   CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname
                ,a.req_status,a.id,r.`name` AS cat_name,c.description AS type_name,a.comments,a.model,a.model_number,
                a.serial_number,a.request_ref_no,a.category_id,a.type_id,a.level
                FROM 0_kv_asset_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                INNER JOIN 0_kv_asset_category AS r ON r.id=a.category_id
                INNER JOIN 0_kv_company_asset AS c ON c.id=a.type_id
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NULL and b.report_to='".$get_user_id[0]."'
                LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sql);
                }
                else if($rows_chk_dept>0)
                {
                    $b_sqls="SELECT   CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname
                ,a.req_status,a.id,r.`name` AS cat_name,c.description AS type_name,a.comments,a.model,a.model_number,
                a.serial_number,a.request_ref_no,a.category_id,a.type_id,a.level
                FROM 0_kv_asset_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                INNER JOIN 0_kv_asset_category AS r ON r.id=a.category_id
                INNER JOIN 0_kv_company_asset AS c ON c.id=a.type_id
                WHERE a.status='1' and a.role_id='2222' AND a.req_status IS NULL and a.empl_id IN (SELECT g.empl_id 
                         FROM 0_kv_empl_job AS g
                         WHERE g.department IN (".rtrim($dept_ids,",").")) 
                     LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
                else
                {
                    $b_sqls="SELECT   CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname
                ,a.req_status,a.id,r.`name` AS cat_name,c.description AS type_name,a.comments,a.model,a.model_number,
                a.serial_number,a.request_ref_no,a.category_id,a.type_id,a.level
                FROM 0_kv_asset_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                INNER JOIN 0_kv_asset_category AS r ON r.id=a.category_id
                INNER JOIN 0_kv_company_asset AS c ON c.id=a.type_id
                WHERE a.status='1' AND a.req_status IS NULL and a.role_id='".$_SESSION['wa_current_user']->access."'
                LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
          //  }




        }


        if($type=='1')
        {
            /*if($_SESSION['wa_current_user']->access=='2')
            {
                $b_sqls="SELECT m.description,a.reason,DATE_FORMAT(a.DATE,'%d-%m-%Y') AS fromdate,
                        DATE_FORMAT(DATE(a.t_date),'%d-%m-%Y') AS todate,a.days,a.request_ref_no
                        ,a.request_date as created
                        ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level,
                        a.request_ref_no,a.reason,a.empl_id,a.half_full_day,a.sick_leave_doc
                        FROM 0_kv_empl_leave_applied as a
                        left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                        LEFT JOIN 0_kv_empl_leave_types AS m ON m.id=a.leave_type 
                        WHERE a.del_status='1' AND a.req_status='0'
                        LIMIT ".$start.",".$length." ";

                $res=db_query($b_sqls);
            }
            else
            {*/
                $role_flag='';
                $Q="SELECT count(a.id) as CntReq
                FROM 0_kv_empl_leave_applied as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.del_status='1' and a.role_id='1111' AND a.req_status='0' and b.report_to='".$get_user_id[0]."'";



                $row_data=db_fetch(db_query($Q));
                if($row_data['CntReq']>0)
                {
                    $b_sql="SELECT m.description,a.reason,DATE_FORMAT(a.DATE,'%d-%m-%Y') AS fromdate,
                        DATE_FORMAT(DATE(a.t_date),'%d-%m-%Y') AS todate,a.days,a.request_ref_no
                        ,a.request_date as created
                        ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level,
                        a.request_ref_no,a.reason,a.empl_id,a.half_full_day,a.sick_leave_doc
                        FROM 0_kv_empl_leave_applied as a
                        left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                        LEFT JOIN 0_kv_empl_leave_types AS m ON m.id=a.leave_type 
                        WHERE a.del_status='1' and a.role_id='1111' AND a.req_status='0' and b.report_to='".$get_user_id[0]."' 
                        LIMIT ".$start.",".$length." ";
                    $res=db_query($b_sql);
                    $role_flag='1111';
                }
                else if($rows_chk_dept>0)
                {

                    $b_sqls="SELECT m.description,a.reason,DATE_FORMAT(a.DATE,'%d-%m-%Y') AS fromdate,
                         DATE_FORMAT(DATE(a.t_date),'%d-%m-%Y') AS todate,a.days,a.request_ref_no
                        ,a.request_date as created
                        ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level,
                        a.request_ref_no,a.reason,a.empl_id,a.half_full_day,a.sick_leave_doc
                        FROM 0_kv_empl_leave_applied as a
                        left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                        LEFT JOIN 0_kv_empl_leave_types AS m ON m.id=a.leave_type 
                        WHERE a.del_status='1' and a.role_id='2222' AND a.req_status='0' and a.empl_id IN (SELECT g.empl_id 
                         FROM 0_kv_empl_job AS g
                         WHERE g.department IN (".rtrim($dept_ids,",").")) LIMIT ".$start.",".$length." ";
                    $role_flag='2222';

                    $res=db_query($b_sqls);
                }
                else
                {
                    $b_sqls="SELECT m.description,a.reason,DATE_FORMAT(a.DATE,'%d-%m-%Y') AS fromdate,
                        DATE_FORMAT(DATE(a.t_date),'%d-%m-%Y') AS todate,a.days,a.request_ref_no
                        ,a.request_date as created
                        ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level,
                        a.request_ref_no,a.reason,a.empl_id,a.half_full_day,a.sick_leave_doc
                        FROM 0_kv_empl_leave_applied as a
                        left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                        LEFT JOIN 0_kv_empl_leave_types AS m ON m.id=a.leave_type 
                        WHERE a.del_status='1' AND a.req_status='0' and a.role_id='".$_SESSION['wa_current_user']->access."'
                        LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);


                    $sql_flow="select level_1,level_2,level_3,level_4,level_5,level_6 from 0_kv_empl_master_request_flow where type_id='1'";
                    $res_flow_data=db_query($sql_flow);
                    $level_data=db_fetch($res_flow_data);

                     $level_array=[];
                     $i=0;
                       foreach($level_data as $key=>$value)
                       {
                           if (strpos($key, 'level') !== false) {

                             if($_SESSION['wa_current_user']->access==$value)
                               {
                                   array_push($level_array,["KEY"=>$key,"VALUE"=>$value]);
                               }

                           }
                       }
                    $explode_level=explode("_",$level_array[0]["KEY"]);
                    $level_next_incr=($explode_level[1])+1;
                    $level_next=$explode_level[0].'_'.$level_next_incr;


                    $sql="Select $level_next from 0_kv_empl_master_request_flow where type_id='1' ";
                    $res_nxt_lvl=db_query($sql);
                    $row_data=db_fetch($res_nxt_lvl);

                     if($row_data[0]!='0')
                     {
                         $role_flag='1';
                     }
                     else
                     {
                         $role_flag='';
                     }
                }
            //}

        }


     

        $color='';
        $status='';
        $data=array();
        $controls='';
        $leave_history='';
        $days='0';
        while($doc_data=db_fetch($res))
        {

            if($type=='1')
            {
                $days=$doc_data['days'];
            }
            else
            {
                $days=0;
            }

            $alt_attr="alt='".$doc_data['id']."' alt_type='".$type."' alt_level='".$doc_data['level']."' 
                       alt_days='".$days."' alt_role_flag='".$role_flag."' ";

            if($doc_data['req_status']=='0' || $doc_data['req_status']=='')
            {
                $status='<label style="color:red;font-weight:bold;">Pending</label>';
            }
            else if($doc_data['req_status']=='1')
            {
                $status='<label style="color:green;font-weight:bold;">Approved</label>';
            }
            else if($doc_data['req_status']=='2')
            {
                $status='<label style="color:black;font-weight:bold;">Rejected</label>';
            }


            $controls="<div style='display: inline-flex;'><button alt_action='1' class='btn-primary btnupdate' ".$alt_attr." >Approve</button>&nbsp;
                     <button alt_action='2' class='btn-warning btnupdate' ".$alt_attr.">Disapprove</button></div>";
            $ref_no="<span style='color:blue;font-weight:bold;'>".$doc_data['request_ref_no']."</span>";
            $leave_history="<a href='#' class='ViewEmpHistory'  alt_id='".$doc_data['empl_id']."' style='color:blue;font-weight:bold;cursor: pointer;text-decoration: underline;'>View</a>";

            if($type=='1') {


                if($doc_data['half_full_day']=='1')
                 {
                    $doc_data['description']=$doc_data['description'].' (Half Day)';
                 }
                 else if($doc_data['half_full_day']=='2')
                 {
                     $doc_data['description']=$doc_data['description'].' (Full Day)';
                 }     



                $data[] = array(
                    $ref_no,
                    $doc_data['empname'],
                    $doc_data['description'],
                    $days,
                    $doc_data['reason'],
                    date('d-m-Y',strtotime($doc_data['fromdate'])),
                    date('d-m-Y',strtotime($doc_data['todate'])),
                    $leave_history,
                    '<a href="assets/uploads/'.$doc_data['sick_leave_doc'].'" target="_blank">'.$doc_data['sick_leave_doc'].'</a>',
                    $status,
                    $controls

                );
            }
            else if($type=='6')
            {
                $data[] = array(
                    $ref_no,
                    $doc_data['empname'],
                    $doc_data['cat_name'],
                    $doc_data['type_name'],
                    $doc_data['model'],
                    $doc_data['model_number'],
                    $doc_data['serial_number'],
                    $doc_data['comments'],
                    $status,
                    $controls

                );
            }
            else if($type=='4')
            {
                $data[] = array(
                    $ref_no,
                    $doc_data['empname'],
                    $doc_data['comments'],
                    $doc_data['loan_amount'],
                    $doc_data['installment_count'],
                    date('d-m-Y',strtotime($doc_data['requestdate'])),

                    $status,
                    $controls

                );
            }
            else
            {
                $data[] = array(
                    $ref_no,
                    $doc_data['empname'],
                    $doc_data['requestdate'],
                    $doc_data['comments'],
                    date('d-m-Y',strtotime($doc_data['created'])),
                    $status,
                    $controls
                );
            }


        }



        /*----------------------------TOT ROWS-------------------------------*/

        if($type=='1')
        {
            $table='0_kv_empl_leave_applied';
        }
        if($type=='2')
        {
            $table='0_kv_empl_certificate_request';
        }

        if($type=='3')
        {
            $table='0_kv_empl_passport_request';
        }

        if($type=='4')
        {
            $table='0_kv_empl_loan_request';
        }

        if($type=='5')
        {
            $table='0_kv_empl_noc_request';
        }

        if($type=='6')
        {
            $table='0_kv_asset_request';
        }

        $tot_rows=$this->find_tot_rows($type,$table);

        /*-------------------------------END---------------------------------*/

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => $tot_rows,
            "recordsFiltered" => $tot_rows,
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);

    }



    public function find_tot_rows($type,$table)
    {

        $ry="select employee_id as id from 0_users 
             where id='".$_SESSION['wa_current_user']->user."'";
        $get_user_id=db_fetch(db_query($ry));

        $check_dept="SELECT b.head_of_dept,b.department from 0_kv_empl_info AS a
                     INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                     left join 0_users as c ON c.employee_id=b.id
                     WHERE b.head_of_dept='1' and c.id='".$_SESSION['wa_current_user']->user."'";
        $data_dept=db_fetch(db_query($check_dept));

        /*if($_SESSION['wa_current_user']->access=='2')
        {
            $b_sqls="SELECT a.id
                FROM ".$table." as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id";
            if($type=='1')
            {
                $b_sqls.=" WHERE a.del_status='1' AND a.req_status='0' ";
            }
            else
            {
                $b_sqls.=" WHERE a.status='1' AND a.req_status IS NULL ";
            }


            $res=db_query($b_sqls);
        }
        else
        {*/
            $Q="SELECT count(a.id) as CntReq
                FROM ".$table." as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id ";

            if($type=='1')
            {
                $Q.=" WHERE a.del_status='1' AND a.req_status='0' ";
            }
            else
            {
                $Q.=" WHERE a.status='1' AND a.req_status IS NULL ";
            }
            $Q.=" and a.role_id='1111'  and b.report_to='".$get_user_id[0]."'";
            $row_data=db_fetch(db_query($Q));
            if($row_data['CntReq']>0)
            {
                $b_sql="SELECT  a.id
                FROM ".$table." as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id ";

                if($type=='1')
                {
                    $b_sql.=" WHERE a.del_status='1' AND a.req_status='0'";
                }
                else
                {
                    $b_sql.=" WHERE a.status='1' AND a.req_status IS NULL ";
                }

                $b_sql.=" and a.role_id='1111' ";
                $b_sql.="  and b.report_to='".$get_user_id[0]."'
                ";
                $res=db_query($b_sql);
            }
            else if($data_dept[0]=='1')
            {

                $b_sqls="SELECT  a.id
                FROM ".$table." as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id ";
                if($type=='1')
                {
                    $b_sqls.=" WHERE a.del_status='1' AND a.req_status='0' ";
                }
                else
                {
                    $b_sqls.=" WHERE a.status='1'  AND a.req_status IS NULL";
                }

                $b_sqls.=" and a.role_id='2222' and a.empl_id IN (SELECT a.id from 0_kv_empl_info AS a
                     INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                     WHERE b.department='".$data_dept[1]."')";

                $res=db_query($b_sqls);
            }
            else
            {
                $b_sqls="SELECT a.id
                FROM ".$table." as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id ";

                if($type=='1')
                {
                    $b_sqls.=" WHERE a.del_status='1' AND a.req_status='0' ";
                }
                else
                {
                    $b_sqls.=" WHERE a.status='1' AND a.req_status IS NULL ";
                }

                $b_sqls.= "  and a.role_id='".$_SESSION['wa_current_user']->access."'
                ";

                $res=db_query($b_sqls);
            }
       // }

        //echo $b_sqls;

        return db_num_rows($res);

    }


    public function get_certificates()
    {
        $sql="SELECT id,name
              FROM `0_kv_empl_certificates`  where status='1'";
        $result = db_query($sql);
        return $result;
    }

    public function get_nocs()
    {
        $sql="SELECT id,name
              FROM `0_kv_empl_noc_master` where status='1'";
        $result = db_query($sql);
        return $result;
    }


    public function get_employee_leave_count()
    {
        $sql_tot="select count(a.id) from 
                  0_kv_empl_leave_applied as a
                  left join 0_kv_empl_info as b ON a.empl_id=b.id
                  left join 0_users as c ON c.employee_id=b.id ";
        if($_SESSION['wa_current_user']->access!='2')
        {
            $sql_tot.=" where c.id='".$_SESSION['wa_current_user']->user."' ";
        }

        $tot_leave=db_fetch(db_query($sql_tot));

        $sql_pend="select count(a.id) from 
                  0_kv_empl_leave_applied as a
                  left join 0_kv_empl_info as b ON a.empl_id=b.id
                  left join 0_users as c ON c.employee_id=b.id
                  where a.req_status='0' ";
        if($_SESSION['wa_current_user']->access!='2') {
            $sql_pend .= " and  c.id='" . $_SESSION['wa_current_user']->user . "'";
        }
        $tot_pend=db_fetch(db_query($sql_pend));


        $sql_approve="select count(a.id) from 
                  0_kv_empl_leave_applied as a
                  left join 0_kv_empl_info as b ON a.empl_id=b.id
                  left join 0_users as c ON c.employee_id=b.id
                  where a.req_status='1' ";
        if($_SESSION['wa_current_user']->access!='2') {
            $sql_approve .= " and  c.id='" . $_SESSION['wa_current_user']->user . "'  ";
        }
        $tot_approve=db_fetch(db_query($sql_approve));


        $sql_loan="select count(a.id) from 
                  0_kv_empl_loan_request as a
                  left join 0_kv_empl_info as b ON a.empl_id=b.id
                  left join 0_users as c ON c.employee_id=b.id";
        if($_SESSION['wa_current_user']->access!='2') {
            $sql_loan .= " where c.id='" . $_SESSION['wa_current_user']->user . "'";
        }
        $tot_loan=db_fetch(db_query($sql_loan));

        $sql_pass="select count(a.id) from 
                  0_kv_empl_passport_request as a
                  left join 0_kv_empl_info as b ON a.empl_id=b.id
                  left join 0_users as c ON c.employee_id=b.id";
        if($_SESSION['wa_current_user']->access!='2')
        {
            $sql_pass.=" where c.id='".$_SESSION['wa_current_user']->user."'";
        }
        $tot_pass=db_fetch(db_query($sql_pass));

        $sql_cert="select count(a.id) from 
                  0_kv_empl_certificate_request as a
                  left join 0_kv_empl_info as b ON a.empl_id=b.id
                  left join 0_users as c ON c.employee_id=b.id";
        if($_SESSION['wa_current_user']->access!='2')
        {
            $sql_cert.=" where c.id='".$_SESSION['wa_current_user']->user."'";
        }
        $tot_cert=db_fetch(db_query($sql_cert));


        return array('tot'=>$tot_leave[0],'pend'=>$tot_pend[0],'appve'=>$tot_approve[0],
                     'loan'=>$tot_loan[0],'pass'=>$tot_pass[0],'cert'=>$tot_cert[0]);


    }


    public function get_taken_leaves()
    {
        $sql="select leave_type from 0_kv_empl_leave_applied where id='".$_POST['id']."'";
        $leave_type_id=db_fetch(db_query($sql));
        $leaves_taken='0';
        if($leave_type_id[0]!='')
        {
            $sql_leave_cnt="select SUM(days) from 0_kv_empl_leave_applied where leave_type='".$leave_type_id[0]."'
                           AND req_status='1'";
            $leave_taken_data=db_fetch(db_query($sql_leave_cnt));

            if(empty($leave_taken_data[0]))
            {
                $leaves_taken='0';
            }
            else
            {
                $leaves_taken=$leave_taken_data[0];
            }
        }
        else
        {
            $leaves_taken='0';
        }

        return AxisPro::SendResponse(['leave_taken'=>$leaves_taken]);
    }



    public function group_requests()
    {
        $req_ids=array(1,2,3,4,5,6);
        $request_cnt=[];
        $request_types=[];

        $ry="select employee_id as id from 0_users where id='".$_SESSION['wa_current_user']->user."'";
        $get_user_id=db_fetch(db_query($ry));

        /*$check_dept="SELECT b.head_of_dept,b.department from 0_kv_empl_info AS a
                     INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                     left join 0_users as c ON c.employee_id=a.id
                     WHERE b.head_of_dept='1' and c.id='".$_SESSION['wa_current_user']->user."'";*/
        $check_dept="SELECT d.id
                      from 0_kv_empl_info AS a
                      INNER JOIN 0_kv_empl_departments AS d ON a.id=d.head_of_empl_id
                      left join 0_users as c ON c.employee_id=a.id
                      WHERE c.id='".$_SESSION['wa_current_user']->user."' ";

        $data_dept_res=db_query($check_dept);
        $rows_chk_dept=db_num_rows($data_dept_res);
        $data_dept=db_fetch($data_dept_res);

        $table='';
        $label='';
        for($k=0;$k<sizeof($req_ids);$k++)
        {
            if($req_ids[$k]=='1')
            {
                $table='0_kv_empl_leave_applied';
                $label='Leave Request';
            }
            if($req_ids[$k]=='2')
            {
                $table='0_kv_empl_certificate_request';
                $label='Certificate Request';
            }

            if($req_ids[$k]=='3')
            {
                $table='0_kv_empl_passport_request';
                $label='Passport Request';
            }

            if($req_ids[$k]=='4')
            {
                $table='0_kv_empl_loan_request';
                $label='Loan Request';
            }

            if($req_ids[$k]=='5')
            {
                $table='0_kv_empl_noc_request';
                $label='Noc Request';
            }

            if($req_ids[$k]=='6')
            {
                $table='0_kv_asset_request';
                $label='Asset Request';
            }


            /*if($_SESSION['wa_current_user']->access=='2')
            {
                $b_sqls="SELECT a.id
                FROM ".$table." as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id";
                if($req_ids[$k]=='1')
                {
                    $b_sqls.=" WHERE a.del_status='1' AND a.req_status='0' ";
                }
                else
                {
                    $b_sqls.=" WHERE a.status='1' AND a.req_status IS NULL ";
                }


                $res=db_query($b_sqls);

            }
            else
            {*/
                $Q="SELECT count(a.id) as CntReq
                FROM ".$table." as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id ";

                if($req_ids[$k]=='1')
                {
                    $Q.=" WHERE a.del_status='1' AND a.req_status='0' ";
                }
                else
                {
                    $Q.=" WHERE a.status='1' AND a.req_status IS NULL ";
                }
                $Q.=" and a.role_id='1111'  and b.report_to='".$get_user_id[0]."'";
                $row_data=db_fetch(db_query($Q));
                if($row_data['CntReq']>0)
                {
                    $b_sql="SELECT  a.id
                    FROM ".$table." as a
                    left JOIN 0_kv_empl_info as b ON a.empl_id=b.id ";

                    if($req_ids[$k]=='1')
                    {
                        $b_sql.=" WHERE a.del_status='1' AND a.req_status='0'";
                    }
                    else
                    {
                        $b_sql.=" WHERE a.status='1' AND a.req_status IS NULL ";
                    }

                    $b_sql.=" and a.role_id='1111' ";
                    $b_sql.="  and b.report_to='".$get_user_id[0]."'
                ";
                    $res=db_query($b_sql);
                }
                else if($rows_chk_dept>0)
                {

                    $b_sqls="SELECT  a.id
                    FROM ".$table." as a
                    left JOIN 0_kv_empl_info as b ON a.empl_id=b.id ";
                    if($req_ids[$k]=='1')
                    {
                        $b_sqls.=" WHERE a.del_status='1' AND a.req_status='0' ";
                    }
                    else
                    {
                        $b_sqls.=" WHERE a.status='1'  AND a.req_status IS NULL";
                    }

                    $b_sqls.=" and a.role_id='2222' and a.empl_id IN (SELECT a.id from 0_kv_empl_info AS a
                     INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                     WHERE b.department='".$data_dept[0]."')";



                    $res=db_query($b_sqls);
                }
                else
                {
                    $b_sqls="SELECT a.id
                FROM ".$table." as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id ";

                    if($req_ids[$k]=='1')
                    {
                        $b_sqls.=" WHERE a.del_status='1' AND a.req_status='0' ";
                    }
                    else
                    {
                        $b_sqls.=" WHERE a.status='1' AND a.req_status IS NULL ";
                    }

                    $b_sqls.= "  and a.role_id='".$_SESSION['wa_current_user']->access."' ";

                    $res=db_query($b_sqls);
                }
            //}


            array_push($request_cnt,db_num_rows($res));
            if(db_num_rows($res)>0)
            {
                array_push($request_types,array('name'=>$label,'rows'=>db_num_rows($res)));
            }
            else
            {
                array_push($request_types,array('name'=>null,'rows'=>0));
            }

        }



        return array('waiting_for_approve'=>array_sum($request_cnt),'details'=>$request_types);

    }

    public function group_requests_dept_summary()
    {
        $dept_id=$_POST['dept_id'];
        $req_id=$_POST['req_id'];
        $request_cnt=[];
        $request_types=[];

        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);



        $table='';
        $label='';

           /* if($req_id=='1')
            {
                $table='0_kv_empl_leave_applied';
                $label='Leave Request';
            }
            if($req_id=='2')
            {
                $table='0_kv_empl_certificate_request';
                $label='Certificate Request';
            }

            if($req_id=='3')
            {
                $table='0_kv_empl_passport_request';
                $label='Passport Request';
            }

            if($req_id=='4')
            {
                $table='0_kv_empl_loan_request';
                $label='Loan Request';
            }

            if($req_id=='5')
            {
                $table='0_kv_empl_noc_request';
                $label='Noc Request';
            }

            if($req_id=='6')
            {
                $table='0_kv_asset_request';
                $label='Asset Request';
            }*/

            /*if($dept_id!='0')
            {
                $b_sqls="SELECT  a.id
                        FROM ".$table." as a
                        left JOIN 0_kv_empl_info as b ON a.empl_id=b.id ";
                if($req_id=='1')
                {
                    $b_sqls.=" WHERE a.del_status='1' AND a.req_status='0' ";
                }
                else
                {
                    $b_sqls.=" WHERE a.status='1'  AND a.req_status IS NULL";
                }

                $b_sqls.=" and a.empl_id IN (SELECT a.id from 0_kv_empl_info AS a
                     INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                     WHERE b.department='".$dept_id."')";

                echo $b_sqls;
            }
            else
            {*/

                $req_ids=array(1,2,3,4,5,6);
                for($k=0;$k<sizeof($req_ids);$k++)
                {
                    if($req_ids[$k]=='1')
                    {
                        $table='0_kv_empl_leave_applied';
                        $label='Leave Request';
                    }
                    if($req_ids[$k]=='2')
                    {
                        $table='0_kv_empl_certificate_request';
                        $label='Certificate Request';
                    }

                    if($req_ids[$k]=='3')
                    {
                        $table='0_kv_empl_passport_request';
                        $label='Passport Request';
                    }

                    if($req_ids[$k]=='4')
                    {
                        $table='0_kv_empl_loan_request';
                        $label='Loan Request';
                    }

                    if($req_ids[$k]=='5')
                    {
                        $table='0_kv_empl_noc_request';
                        $label='Noc Request';
                    }

                    if($req_ids[$k]=='6')
                    {
                        $table='0_kv_asset_request';
                        $label='Asset Request';
                    }


                    $b_sqls="SELECT  a.id
                    FROM ".$table." as a
                    left JOIN 0_kv_empl_info as b ON a.empl_id=b.id ";
                    if($req_ids[$k]=='1')
                    {
                        $b_sqls.=" WHERE a.del_status='1' AND a.req_status='0' ";
                    }
                    else
                    {
                        $b_sqls.=" WHERE a.status='1'  AND a.req_status IS NULL";
                    }

                    if($dept_id!='0')
                    {
                        $b_sqls.=" and a.empl_id IN (SELECT a.id from 0_kv_empl_info AS a
                         INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                         WHERE b.department='".$dept_id."')";
                    }



                    $res=db_query($b_sqls);

                    $result_ids='';
                    $type_of_req_ids='';
                    while($rows=db_fetch($res))
                    {
                        $result_ids.=$rows['id'].',';
                        $type_of_req_ids.=$req_ids[$k].',';

                    }


                    if(db_num_rows($res)>0)
                    {
                        array_push($request_types,array('name'=>$label,'rows'=>db_num_rows($res),'ids'=>rtrim($result_ids,','),'request_ids'=>$req_ids[$k]));
                    }


                }



           // }






         $service_ids='';
          if(count($request_types)>0)
          {
              foreach($request_types as $req)
              {
                  if($req['rows']<>0)
                  {
                      $service_ids="<a href='#' class='clsTotalCnt' alt='".$req['ids']."' alt_request_id='".$req['request_ids']."' style='font-size: 12pt;
    font-weight: bold;'>".$req['rows']."</a>";
                      $data[] = array(
                          $req['name'],
                          $service_ids
                      );
                  }

              }
          }
          else
          {
              $data[] = array(
                  'No Data Found',
                  'No Data Found'
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



    public function get_request_details()
    {
        $req_pk_ids=$_POST['req_pk_ids'];
        $req_type_id=$_POST['req_type_id'];
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);


        if($req_type_id=='1')
        {
            $table='0_kv_empl_leave_applied';
            $label='Leave Request';
        }
        if($req_type_id=='2')
        {
            $table='0_kv_empl_certificate_request';
            $label='Certificate Request';
        }

        if($req_type_id=='3')
        {
            $table='0_kv_empl_passport_request';
            $label='Passport Request';
        }

        if($req_type_id=='4')
        {
            $table='0_kv_empl_loan_request';
            $label='Loan Request';
        }

        if($req_type_id=='5')
        {
            $table='0_kv_empl_noc_request';
            $label='Noc Request';
        }

        if($req_type_id=='6')
        {
            $table='0_kv_asset_request';
            $label='Asset Request';
        }


        if($req_type_id=='1')
        {
            $sql="SELECT  a.request_ref_no,a.reason as comments,a.`level`,a.role_id,a.request_date,
                CONCAT(b.empl_firstname,' ',b.empl_lastname) AS emp_name,a.id,b.id as emp_pk_id
                FROM ".$table." as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id  
                WHERE a.id IN (".$req_pk_ids.") ";
        }
        else
        {
            $sql="SELECT  a.request_ref_no,a.comments,a.`level`,a.role_id,a.request_date,
                CONCAT(b.empl_firstname,' ',b.empl_lastname) AS emp_name,a.id,b.id as emp_pk_id
                FROM ".$table." as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id  
                WHERE a.id IN (".$req_pk_ids.") ";
        }


        /******************************FIND REQUEST ASSIGNED LEVELS**********************/
         /* $l_sql="SELECT SUM(`one`+two+three+four+five+six+seven+eight+nine+ten) AS total_lvl
                    FROM (
                    SELECT CASE
                    when level_1='0' THEN 0
                    WHEN level_1<>0  THEN 1
                    END AS `one`,CASE when level_2='0' THEN 0 WHEN level_2<>0  THEN 1 END AS two, CASE when level_3='0' THEN 0 WHEN level_3<>0  THEN 1 END AS three,
                    CASE when level_4='0' THEN 0 WHEN level_4<>0  THEN 1 END AS four,CASE when level_5='0' THEN 0 WHEN level_5<>0  THEN 1 END AS five,
                    CASE when level_6='0' THEN 0 WHEN level_6<>0  THEN 1 END AS six,CASE when level_7='0' THEN 0 WHEN level_7<>0  THEN 1 END AS seven,
                    CASE when level_8='0' THEN 0 WHEN level_8<>0  THEN 1 END AS eight,CASE when level_9='0' THEN 0 WHEN level_9<>0  THEN 1 END AS nine,
                    CASE when level_10='0' THEN 0 WHEN level_10<>0  THEN 1 END AS ten
                    FROM 0_kv_empl_master_request_flow WHERE type_id='".$req_type_id."' ) AS tble";*/

         $l_sql=" SELECT CASE 
                    when level_1='0' THEN 0
                    WHEN level_1<>0  THEN 1
                    END AS `level_1`,CASE when level_2='0' THEN 0 WHEN level_2<>0  THEN 2 END AS `level_2`, CASE when level_3='0' THEN 0 WHEN level_3<>0  THEN 3 END AS `level_3`,
                    CASE when level_4='0' THEN 0 WHEN level_4<>0  THEN 4 END AS `level_4`,CASE when level_5='0' THEN 0 WHEN level_5<>0  THEN 5 END AS `level_5`,
                    CASE when level_6='0' THEN 0 WHEN level_6<>0  THEN 6 END AS `level_6`,CASE when level_7='0' THEN 0 WHEN level_7<>0  THEN 7 END AS `level_7`,
                    CASE when level_8='0' THEN 0 WHEN level_8<>0  THEN 8 END AS `level_8`,CASE when level_9='0' THEN 0 WHEN level_9<>0  THEN 9 END AS `level_9`,
                    CASE when level_10='0' THEN 0 WHEN level_10<>0  THEN 10 END AS `level_10`
                    FROM 0_kv_empl_master_request_flow WHERE type_id='".$req_type_id."'";

          $tot_levls=db_query($l_sql);
          $levels_row=db_fetch($tot_levls);
          $levels=array();
          if(isset($levels_row['level_1']) && $levels_row['level_1']!='0')
          {
              array_push($levels,$levels_row['level_1']);
          }
          if(isset($levels_row['level_2']) && $levels_row['level_2']!='0')
            {
                array_push($levels,$levels_row['level_2']);
            }
        if(isset($levels_row['level_3']) && $levels_row['level_3']!='0')
        {
            array_push($levels,$levels_row['level_3']);
        }
        if(isset($levels_row['level_4']) && $levels_row['level_4']!='0')
        {
            array_push($levels,$levels_row['level_4']);
        }
        if(isset($levels_row['level_5']) && $levels_row['level_5']!='0')
        {
            array_push($levels,$levels_row['level_5']);
        }
        if(isset($levels_row['level_6']) && $levels_row['level_6']!='0')
        {
            array_push($levels,$levels_row['level_6']);
        }
        if(isset($levels_row['level_7']) && $levels_row['level_7']!='0')
        {
            array_push($levels,$levels_row['level_7']);
        }
        if(isset($levels_row['level_8']) && $levels_row['level_8']!='0')
        {
            array_push($levels,$levels_row['level_8']);
        }
        if(isset($levels_row['level_9']) && $levels_row['level_9']!='0')
        {
            array_push($levels,$levels_row['level_9']);
        }
        if(isset($levels_row['level_10']) && $levels_row['level_10']!='0')
        {
            array_push($levels,$levels_row['level_10']);
        }





        /****************************************END*************************************/

//echo $sql;
        $res=db_query($sql);

        $data=array();
        $role_name='';


        while($row=db_fetch($res))
        {

            $qry="SELECT `level` as levels_done FROM  0_kv_empl_req_approve_history
                  WHERE request_id='".$row['id']."' ";
            $res_qry=db_query($qry);
            $levels_completed='';
            $completed_levels=array();
            while($r=db_fetch($res_qry))
            {
              array_push($completed_levels,$r['levels_done']);
              $levels_completed=$r['levels_done'].',';
            }

            $result_array_diff=array_diff($levels,$completed_levels); /****************FINDING UMCOMPLETED LEVELS***********/

            $level_lbl='';
            $need_to_complete_levels='';
            $level_number='1';
            foreach($result_array_diff as $d)
            {
               $level_lbl='level_'.$d;

               $sqlQry="select ".$level_lbl." from 0_kv_empl_master_request_flow where type_id='".$req_type_id."' ";
               $row_level=db_fetch(db_query($sqlQry));

               if($row_level[$level_lbl]=='1111')
               {
                  $sql_first="select CONCAT(b.empl_firstname,' ',b.empl_lastname) as emp_name
                                FROM 0_kv_empl_info AS a
                                LEFT JOIN 0_kv_empl_info AS b ON a.report_to=b.id
                                WHERE a.id='".$row['emp_pk_id']."'";
                  $line_manager=db_fetch(db_query($sql_first));


                  $need_to_complete_levels.=$level_number.' - '.$line_manager['emp_name'].'</br>';
               }
               else if($row_level[$level_lbl]=='2222')
               {
                  $sql_head="SELECT CONCAT(h.empl_firstname,' ',h.empl_lastname) as head_name
                                FROM 0_kv_empl_info AS a
                                LEFT JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                                LEFT JOIN 0_kv_empl_departments AS c ON c.id=b.department
                                LEFT JOIN 0_kv_empl_info AS h ON h.id=c.head_of_empl_id
                                WHERE a.id='".$row['emp_pk_id']."' ";
                  $head_name=db_fetch(db_query($sql_head));

                   $need_to_complete_levels.=$level_number.' - '.$head_name['head_name'].'</br>';
               }
               else
               {
                   $sql_role_user="SELECT CONCAT(b.empl_firstname,' ',b.empl_lastname) as emp_name
                                    FROM 0_users AS a
                                    LEFT JOIN 0_kv_empl_info AS b ON a.employee_id=b.id
                                    WHERE a.role_id='".$row_level[$level_lbl]."' ";
                   $role_user=db_query($sql_role_user);

                   while($res_role=db_fetch($role_user))
                   {
                       $need_to_complete_levels.=$level_number.' - '.$res_role['emp_name'].'</br>';
                   }
               }

               // print_r($sqlQry.' ---- '
                $level_number++;

            }





            if($row['role_id']=='1111')
            {
                $role_name='Counter Supervisor';
            }
            else if($row['role_id']=='2222')
            {
                $role_name='Head Of Department';
            }


            $data[] = array(
                '<label style="color:blue;font-weight:bold;">'.$row['request_ref_no'].'</label>',
                $row['emp_name'],
                date('d/m/Y',strtotime($row['request_date'])),
                $row['comments'],
                '<label style="color:green;font-weight:bold;">'.$row['level'].'</label>',
                '<label style="color:green;font-weight:bold;">'.$role_name.'</label>',
                '<label style="color:red;font-weight:bold;">'.rtrim($need_to_complete_levels,',').'</label>'
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




 public function list_verified_requests()
    {

        $type=$_POST['type_id'];
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);


        $ry="select employee_id as id from 0_users 
            where id='".$_SESSION['wa_current_user']->user."'";
        $get_user_id=db_fetch(db_query($ry));

       /* $check_dept="SELECT b.head_of_dept,b.department from 0_kv_empl_info AS a
                     INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                     WHERE b.head_of_dept='1' and user_id='".$_SESSION['wa_current_user']->user."'";*/

        $check_dept="SELECT d.id
                      from 0_kv_empl_info AS a
                      INNER JOIN 0_kv_empl_departments AS d ON a.id=d.head_of_empl_id
                      left join 0_users as c ON c.employee_id=a.id
                      WHERE c.id='".$_SESSION['wa_current_user']->user."' "; 
        $check_dept_res=db_query($check_dept); 
        $rows_chk_dept=db_num_rows($check_dept_res);


        if($type=='3')
        {

            if($_SESSION['wa_current_user']->access=='2')
            {
                $b_sqls="SELECT a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_passport_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                WHERE a.status='1' AND a.req_status IS NOT NULL LIMIT ".$start.",".$length." ";
                $res=db_query($b_sqls);
            }
            else
            {
                $Q="SELECT count(a.id) as CntReq
                FROM 0_kv_empl_passport_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NOT NULL and b.report_to='".$get_user_id[0]."'";
                $row_data=db_fetch(db_query($Q));
                if($row_data['CntReq']>0)
                {
                    $b_sql="SELECT   a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_passport_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' ";
                    $b_sql.=" and a.role_id='1111' ";
                    $b_sql.=" AND a.req_status IS NULL and b.report_to='".$get_user_id[0]."'
                LIMIT ".$start.",".$length." ";
                    $res=db_query($b_sql);
                }
                else if($rows_chk_dept>0)
                {
                    $dept_ids='';
                    while($data_dept=db_fetch($check_dept_res))
                      {
                        $dept_ids.=$data_dept['0'].',';
                      } 
 
            
                 $b_sqls="SELECT a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                           ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                           FROM 0_kv_empl_passport_request as a
                           left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                           WHERE a.status='1' and a.role_id='2222' AND a.req_status IS NOT NULL 
                           and a.empl_id IN (SELECT g.empl_id 
                           FROM 0_kv_empl_job AS g
                           WHERE g.department IN (".rtrim($dept_ids,",").")) LIMIT ".$start.",".$length." ";
                     
                    $res=db_query($b_sqls);
                }
                else
                {
                    $b_sqls="SELECT a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_passport_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                WHERE a.status='1' AND a.req_status IS NOT NULL and a.role_id='".$_SESSION['wa_current_user']->access."'
                LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
            }


        }

        if($type=='4')
        {
            if($_SESSION['wa_current_user']->access=='2')
            {
                $b_sqls="SELECT   a.request_ref_no,a.`loan_required_date` as requestdate,a.description as comments,a.created_on as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level, a.loan_amount
                FROM 0_kv_empl_loan_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' AND a.req_status IS NOT NULL LIMIT ".$start.",".$length." ";

                $res=db_query($b_sqls);
            }
            else
            {
                $Q="SELECT count(a.id) as CntReq
                FROM 0_kv_empl_loan_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NOT NULL and b.report_to='".$get_user_id[0]."'";
                $row_data=db_fetch(db_query($Q));

                if($row_data['CntReq']>0)
                {
                    $b_sql="SELECT   a.request_ref_no,a.`loan_required_date` as requestdate,a.description as comments,a.created_on as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level,a.installment_count
                , a.loan_amount
                FROM 0_kv_empl_loan_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NOT NULL and b.report_to='".$get_user_id[0]."' 
                LIMIT ".$start.",".$length." ";
                    $res=db_query($b_sql);

                }
                else if($rows_chk_dept>0)
                {
                    $dept_ids='';
                    while($data_dept=db_fetch($check_dept_res))
                      {
                        $dept_ids.=$data_dept['0'].',';
                      }


                    $b_sqls="SELECT   a.request_ref_no,a.`loan_required_date` as requestdate,a.description as comments,a.created_on as created
                        ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level, a.loan_amount
                         FROM 0_kv_empl_loan_request as a
                         left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                         WHERE a.status='1' and a.role_id='2222' AND a.req_status IS NOT NULL and a.empl_id IN 
                         (SELECT g.empl_id 
                         FROM 0_kv_empl_job AS g
                         WHERE g.department IN (".rtrim($dept_ids,",").")) LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
                else
                {
                    $b_sqls="SELECT   a.request_ref_no,a.`loan_required_date` as requestdate,a.description as comments,a.created_on as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level, a.loan_amount
                FROM 0_kv_empl_loan_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' AND a.req_status IS NOT NULL and a.role_id='".$_SESSION['wa_current_user']->access."'
                LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
            }


        }

        if($type=='2')
        {

            if($_SESSION['wa_current_user']->access=='2')
            {
                $b_sqls="SELECT a.certifcate_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_certificate_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' AND a.req_status IS NOT NULL LIMIT ".$start.",".$length." ";

                $res=db_query($b_sqls);
            }
            else
            {
                $Q="SELECT count(a.id) as CntReq
                FROM 0_kv_empl_certificate_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NOT NULL and b.report_to='".$get_user_id[0]."'";

                $row_data=db_fetch(db_query($Q));
                if($row_data['CntReq']>0)
                {
                    $b_sql="SELECT a.certifcate_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_certificate_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NOT NULL and b.report_to='".$get_user_id[0]."' 
                LIMIT ".$start.",".$length." ";
                    $res=db_query($b_sql);
                }
                else if($rows_chk_dept>0)
                {
                   $dept_ids='';
                    while($data_dept=db_fetch($check_dept_res))
                      {
                        $dept_ids.=$data_dept['0'].',';
                      }
                    

                    $b_sqls="SELECT a.certifcate_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                        ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                        FROM 0_kv_empl_certificate_request as a
                        left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                        WHERE a.status='1' and a.role_id='2222' AND a.req_status IS NOT NULL and a.empl_id IN (SELECT g.empl_id 
                         FROM 0_kv_empl_job AS g
                         WHERE g.department IN (".rtrim($dept_ids,",").")) LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
                else
                {
                    $b_sqls="SELECT a.certifcate_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_certificate_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' AND a.req_status IS NOT NULL and a.role_id='".$_SESSION['wa_current_user']->access."'
                LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
            }

        }

        if($type=='5')
        {
            if($_SESSION['wa_current_user']->access=='2')
            {
                $b_sqls="SELECT a.noc_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_noc_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' AND a.req_status IS NOT NULL 
                LIMIT ".$start.",".$length." ";

                $res=db_query($b_sqls);
            }
            else
            {
                $Q="SELECT count(a.id) as CntReq
                FROM 0_kv_empl_noc_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NOT NULL and b.report_to='".$get_user_id[0]."'";
                $row_data=db_fetch(db_query($Q));
                if($row_data['CntReq']>0)
                {
                    $b_sql="SELECT a.noc_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_noc_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NOT NULL and b.report_to='".$get_user_id[0]."' 
                LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sql);
                }
                else if($rows_chk_dept>0)
                {

                    $b_sqls="SELECT a.noc_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_noc_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='2222' AND a.req_status IS NOT NULL and a.empl_id IN (SELECT g.empl_id 
                         FROM 0_kv_empl_job AS g
                         WHERE g.department IN (".rtrim($dept_ids,",").")) LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
                else
                {
                    $b_sqls="SELECT a.noc_name,a.request_ref_no,a.`date` as requestdate,a.comments,a.request_date as created
                ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level
                FROM 0_kv_empl_noc_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' AND a.req_status IS NOT NULL and a.role_id='".$_SESSION['wa_current_user']->access."' 
                LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
            }





        }

        if($type=='6')
        {

            if($_SESSION['wa_current_user']->access=='2')
            {
                $b_sqls="SELECT   CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname
                ,a.req_status,a.id,r.`name` AS cat_name,c.description AS type_name,a.comments,a.model,a.model_number,
                a.serial_number,a.request_ref_no,a.category_id,a.type_id,a.level
                FROM 0_kv_asset_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                INNER JOIN 0_kv_asset_category AS r ON r.id=a.category_id
                INNER JOIN 0_kv_company_asset AS c ON c.id=a.type_id
                WHERE a.status='1' AND a.req_status IS NOT NULL
                LIMIT ".$start.",".$length." ";

                $res=db_query($b_sqls);
            }
            else
            {
                $Q="SELECT count(a.id) as CntReq
                FROM 0_kv_asset_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NOT NULL and b.report_to='".$get_user_id[0]."'";
                $row_data=db_fetch(db_query($Q));
                if($row_data['CntReq']>0) {
                    $b_sql="SELECT   CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname
                ,a.req_status,a.id,r.`name` AS cat_name,c.description AS type_name,a.comments,a.model,a.model_number,
                a.serial_number,a.request_ref_no,a.category_id,a.type_id,a.level
                FROM 0_kv_asset_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                INNER JOIN 0_kv_asset_category AS r ON r.id=a.category_id
                INNER JOIN 0_kv_company_asset AS c ON c.id=a.type_id
                WHERE a.status='1' and a.role_id='1111' AND a.req_status IS NOT NULL and b.report_to='".$get_user_id[0]."'
                LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sql);
                }
                else if($rows_chk_dept>0)
                {
                    $b_sqls="SELECT   CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname
                ,a.req_status,a.id,r.`name` AS cat_name,c.description AS type_name,a.comments,a.model,a.model_number,
                a.serial_number,a.request_ref_no,a.category_id,a.type_id,a.level
                FROM 0_kv_asset_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                INNER JOIN 0_kv_asset_category AS r ON r.id=a.category_id
                INNER JOIN 0_kv_company_asset AS c ON c.id=a.type_id
                WHERE a.status='1' and a.role_id='2222' AND a.req_status IS NOT NULL and a.empl_id IN (SELECT g.empl_id 
                         FROM 0_kv_empl_job AS g
                         WHERE g.department IN (".rtrim($dept_ids,",").")) 
                     LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
                else
                {
                    $b_sqls="SELECT   CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname
                ,a.req_status,a.id,r.`name` AS cat_name,c.description AS type_name,a.comments,a.model,a.model_number,
                a.serial_number,a.request_ref_no,a.category_id,a.type_id,a.level
                FROM 0_kv_asset_request as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                INNER JOIN 0_kv_asset_category AS r ON r.id=a.category_id
                INNER JOIN 0_kv_company_asset AS c ON c.id=a.type_id
                WHERE a.status='1' AND a.req_status IS NOT NULL and a.role_id='".$_SESSION['wa_current_user']->access."'
                LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
            }




        }


        if($type=='1')
        {
            if($_SESSION['wa_current_user']->access=='2')
            {
                $b_sqls="SELECT m.description,a.reason,DATE_FORMAT(a.DATE,'%d-%m-%Y') AS fromdate,
                        DATE_FORMAT(DATE(a.t_date),'%d-%m-%Y') AS todate,a.days,a.request_ref_no
                        ,a.request_date as created
                        ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level,
                        a.request_ref_no,a.reason,a.empl_id
                        FROM 0_kv_empl_leave_applied as a
                        left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                        LEFT JOIN 0_kv_empl_leave_types AS m ON m.id=a.leave_type 
                        WHERE a.del_status='1' AND a.req_status='0'
                        LIMIT ".$start.",".$length." ";

                $res=db_query($b_sqls);
            }
            else
            {
                $role_flag='';
                $Q="SELECT count(a.id) as CntReq
                FROM 0_kv_empl_leave_applied as a
                left JOIN 0_kv_empl_info as b ON a.empl_id=b.id 
                WHERE a.del_status='1' and a.role_id='1111' AND a.req_status='0' and b.report_to='".$get_user_id[0]."'";

                $row_data=db_fetch(db_query($Q));
                if($row_data['CntReq']>0)
                {
                    $b_sql="SELECT m.description,a.reason,DATE_FORMAT(a.DATE,'%d-%m-%Y') AS fromdate,
                        DATE_FORMAT(DATE(a.t_date),'%d-%m-%Y') AS todate,a.days,a.request_ref_no
                        ,a.request_date as created
                        ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level,
                        a.request_ref_no,a.reason,a.empl_id
                        FROM 0_kv_empl_leave_applied as a
                        left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                        LEFT JOIN 0_kv_empl_leave_types AS m ON m.id=a.leave_type 
                        WHERE a.del_status='1' and a.role_id='1111' AND a.req_status='0' and b.report_to='".$get_user_id[0]."' 
                        LIMIT ".$start.",".$length." ";
                    $res=db_query($b_sql);
                    $role_flag='1111';
                }
                else if($rows_chk_dept>0)
                {

                    $b_sqls="SELECT m.description,a.reason,DATE_FORMAT(a.DATE,'%d-%m-%Y') AS fromdate,
                         DATE_FORMAT(DATE(a.t_date),'%d-%m-%Y') AS todate,a.days,a.request_ref_no
                        ,a.request_date as created
                        ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level,
                        a.request_ref_no,a.reason,a.empl_id
                        FROM 0_kv_empl_leave_applied as a
                        left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                        LEFT JOIN 0_kv_empl_leave_types AS m ON m.id=a.leave_type 
                        WHERE a.del_status='1' and a.role_id='2222' AND a.req_status='0' and a.empl_id IN (SELECT g.empl_id 
                         FROM 0_kv_empl_job AS g
                         WHERE g.department IN (".rtrim($dept_ids,",").")) LIMIT ".$start.",".$length." ";
                    $role_flag='2222';

                    $res=db_query($b_sqls);
                }
                else
                {
                    $b_sqls="SELECT m.description,a.reason,DATE_FORMAT(a.DATE,'%d-%m-%Y') AS fromdate,
                        DATE_FORMAT(DATE(a.t_date),'%d-%m-%Y') AS todate,a.days,a.request_ref_no
                        ,a.request_date as created
                        ,CONCAT(b.empl_id,' ',b.empl_firstname,' ',b.empl_lastname) as empname,a.req_status,a.id,a.level,
                        a.request_ref_no,a.reason,a.empl_id
                        FROM 0_kv_empl_leave_applied as a
                        left JOIN 0_kv_empl_info as b ON a.empl_id=b.id
                        LEFT JOIN 0_kv_empl_leave_types AS m ON m.id=a.leave_type 
                        WHERE a.del_status='1' AND a.req_status='0' and a.role_id='".$_SESSION['wa_current_user']->access."'
                        LIMIT ".$start.",".$length." ";

                    $res=db_query($b_sqls);
                }
            }

        }

     

        $color='';
        $status='';
        $data=array();
        $controls='';
        $leave_history='';
        $days='0';
        while($doc_data=db_fetch($res))
        {

            if($type=='1')
            {
                $days=$doc_data['days'];
            }
            else
            {
                $days=0;
            }

            $alt_attr="alt='".$doc_data['id']."' alt_type='".$type."' alt_level='".$doc_data['level']."' 
                       alt_days='".$days."' alt_role_flag='".$role_flag."' ";

            if($doc_data['req_status']=='0' || $doc_data['req_status']=='')
            {
                $status='<label style="color:red;font-weight:bold;">Pending</label>';
            }
            else if($doc_data['req_status']=='1')
            {
                $status='<label style="color:green;font-weight:bold;">Approved</label>';
            }
            else if($doc_data['req_status']=='2')
            {
                $status='<label style="color:black;font-weight:bold;">Rejected</label>';
            }


            $controls="<div style='display: inline-flex;'><button alt_action='1' class='btn-primary btnprint' ".$alt_attr." >Print</button></div>";
            $ref_no="<span style='color:blue;font-weight:bold;'>".$doc_data['request_ref_no']."</span>";
            $leave_history="<a href='#' class='ViewEmpHistory'  alt_id='".$doc_data['empl_id']."' style='color:blue;font-weight:bold;cursor: pointer;text-decoration: underline;'>View</a>";

            if($type=='1') {

                $data[] = array(
                    $ref_no,
                    $doc_data['empname'],
                    $doc_data['description'],
                    $days,
                    $doc_data['reason'],
                    date('d-m-Y',strtotime($doc_data['fromdate'])),
                    date('d-m-Y',strtotime($doc_data['todate'])),
                    $leave_history,
                    $status,
                    $controls

                );
            }
            else if($type=='6')
            {
                $data[] = array(
                    $ref_no,
                    $doc_data['empname'],
                    $doc_data['cat_name'],
                    $doc_data['type_name'],
                    $doc_data['model'],
                    $doc_data['model_number'],
                    $doc_data['serial_number'],
                    $doc_data['comments'],
                    $status,
                   // $controls

                );
            }
            else if($type=='4')
            {
                $data[] = array(
                    $ref_no,
                    $doc_data['empname'],
                    $doc_data['comments'],
                    $doc_data['loan_amount'],
                    $doc_data['installment_count'],
                    date('d-m-Y',strtotime($doc_data['requestdate'])),

                    $status,
                    //$controls

                );
            }
            else
            {
                $data[] = array(
                    $ref_no,
                    $doc_data['empname'],
                    $doc_data['requestdate'],
                    $doc_data['comments'],
                    date('d-m-Y',strtotime($doc_data['created'])),
                    $status,
                    //$controls
                );
            }


        }



        /*----------------------------TOT ROWS-------------------------------*/

        if($type=='1')
        {
            $table='0_kv_empl_leave_applied';
        }
        if($type=='2')
        {
            $table='0_kv_empl_certificate_request';
        }

        if($type=='3')
        {
            $table='0_kv_empl_passport_request';
        }

        if($type=='4')
        {
            $table='0_kv_empl_loan_request';
        }

        if($type=='5')
        {
            $table='0_kv_empl_noc_request';
        }

        if($type=='6')
        {
            $table='0_kv_asset_request';
        }

        $tot_rows=$this->find_tot_rows($type,$table);

        /*-------------------------------END---------------------------------*/

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => $tot_rows,
            "recordsFiltered" => $tot_rows,
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);

    }    


     public function view_print()
    {
        $form_id=$_POST['type_id'];
        $request_id=$_POST['request_id'];
        $sub_frm_id=$_POST['sub_form_id'];
        $language=$_POST['language'];



        $sql="select html_content from 0_kv_hrm_form_masters where form_id='".$form_id."' 
              AND language='".$language."' ";
        if($sub_frm_id!='')
        {
            $sql.=" AND sub_frm_id='".$sub_frm_id."' ";
        }



        $content=db_fetch(db_query($sql));

        $frm_content='';

        if($form_id=='4')
        {
           $sql_d="SELECT a.request_ref_no,CONCAT(e.empl_id,' - ',e.empl_firstname,'',e.empl_lastname) AS EmpName,
                    e.empl_id,a.loan_amount,a.installment_count,a.loan_required_date
                    From 0_kv_empl_loan_request as a 
                    INNER JOIN 0_kv_empl_info AS e ON e.id=a.empl_id
                    WHERE a.id='".$request_id."'";

            $emp_data=db_fetch(db_query($sql_d));
           /* $loan_prnt_strng=array('employe_name','install_months','monthly_installment','loan_start_date','employe_code','curr_date');
            for($i=0;$i<=sizeof($loan_prnt_strng);$i++)
            {*/
               $monthly_installment=$emp_data['loan_amount']/$emp_data['installment_count'];

               $frm_content=str_replace('employe_name', $emp_data['EmpName'], $content['html_content']); 
               $frm_content=str_replace('install_months', $emp_data['installment_count'], $frm_content);
               $frm_content=str_replace('monthly_installment', $monthly_installment, $frm_content);
               $frm_content=str_replace('loan_start_date', $emp_data['loan_required_date'], $frm_content);
               $frm_content=str_replace('employe_code', $emp_data['empl_id'], $frm_content);
               $frm_content=str_replace('curr_date', date('Y-m-d'), $frm_content);  

           
       }
       elseif($form_id=='3')
       {
          $sql_d="SELECT a.request_ref_no,CONCAT(e.empl_id,' - ',e.empl_firstname,'',e.empl_lastname) AS EmpName,
                    e.empl_id,d.description AS desig,c.joining,w.description AS dept,n.description AS nation
                          ,CONCAT(g.empl_firstname,'',g.empl_lastname) AS linemanger,a.request_date,a.return_date,a.return_date,a.comments,e.id
                    From 0_kv_empl_passport_request as a 
                    INNER JOIN 0_kv_empl_info AS e ON e.id=a.empl_id
                    INNER JOIN 0_kv_empl_job AS c ON c.empl_id=e.id
                    INNER JOIN 0_kv_empl_designation AS d ON d.id=c.desig
                    INNER JOIN 0_kv_empl_departments AS w ON w.id=c.department
                    INNER JOIN 0_kv_empl_nationalities AS n ON n.id=c.nationality
                    INNER JOIN 0_kv_empl_info AS g ON g.id=e.report_to
                    WHERE a.id='".$request_id."'";
   
            $emp_data=db_fetch(db_query($sql_d)); 


            $passport_det="select issue_date,expiry_date,doc_no from 0_employee_docs where emp_id='".$emp_data['id']."'
            and type_id='1' ";
            $pass_data=db_fetch(db_query($passport_det));

            $visa_det="select issue_date,expiry_date,doc_no from 0_employee_docs where emp_id='".$emp_data['id']."'
            and type_id='4' ";
            $visa_data=db_fetch(db_query($visa_det));
 

           /* $loan_prnt_strng=array('employe_name','install_months','monthly_installment','loan_start_date','employe_code','curr_date');
            for($i=0;$i<=sizeof($loan_prnt_strng);$i++)
            {*/
               
 
               $frm_content=str_replace('CURRENT_DATE', date('d/m/Y'), $content['html_content']); 
               $frm_content=str_replace('EMP_FULL_NAME',  $emp_data['EmpName'], $frm_content);
               $frm_content=str_replace('EMP_DESIGNATION',  $emp_data['desig'], $frm_content);
               $frm_content=str_replace('EMP_DOJ', date('d/m/Y',strtotime($emp_data['joining'])) , $frm_content);
               $frm_content=str_replace('EMP_DEPT',  $emp_data['dept'], $frm_content);
               $frm_content=str_replace('EMP_NATION',  $emp_data['nation'], $frm_content);
               $frm_content=str_replace('EMP_VISA_EXP',  $visa_det['expiry_date'],$frm_content);
               $frm_content=str_replace('EMP_PASS_NO',  $pass_data['doc_no'], $frm_content);
               $frm_content=str_replace('EMP_VISA_NO',  $visa_det['doc_no'], $frm_content);
               $frm_content=str_replace('EMP_PASS_EXPIRY',  $pass_data['expiry_date'], $frm_content); 
               $frm_content=str_replace('EMP_LINE_MANAGER',  $emp_data['linemanger'], $frm_content);
               $frm_content=str_replace('PASS_WITHDRW_DATE', date('d/m/Y',strtotime($emp_data['request_date'])), $frm_content);
               $frm_content=str_replace('PASS_RETRN_DATE', date('d/m/Y',strtotime($emp_data['return_date'])), $frm_content);
               $frm_content=str_replace('PASS_RELESE_REASON', $emp_data['comments'], $frm_content); 
       }
       else if($form_id=='2')
       {



          $annotation='';
          $she_or_he='';
          $his_or_her='';

          $sql_d="SELECT a.request_ref_no,CONCAT(e.empl_firstname,'',e.empl_lastname) AS EmpName,
                    e.empl_id,d.description AS desig,c.joining,w.description AS dept,n.description AS nation
                          ,CONCAT(g.empl_firstname,'',g.empl_lastname) AS linemanger ,a.comments,e.id,e.gender,a.comments,a.address_to,a.bank,a.iban,a.branch
                    From 0_kv_empl_certificate_request as a 
                    INNER JOIN 0_kv_empl_info AS e ON e.id=a.empl_id
                    INNER JOIN 0_kv_empl_job AS c ON c.empl_id=e.id
                    INNER JOIN 0_kv_empl_designation AS d ON d.id=c.desig
                    INNER JOIN 0_kv_empl_departments AS w ON w.id=c.department
                    INNER JOIN 0_kv_empl_nationalities AS n ON n.id=c.nationality
                    LEFT JOIN 0_kv_empl_info AS g ON g.id=e.report_to
                    WHERE a.id='".$request_id."'";

            $emp_data=db_fetch(db_query($sql_d));

            $passport_det="select issue_date,expiry_date,doc_no from 0_employee_docs where emp_id='".$emp_data['id']."'
            and type_id='1' ";
            $pass_data=db_fetch(db_query($passport_det));

            $qry="select sum(pay_amount) as tot_salary from 0_kv_empl_salary_details
                  where emp_id='".$emp_data['id']."' and type='0'";
            $emp_tot_salary=db_fetch(db_query($qry));

            /*********************EMNPLOYEE HEADER LOGO****************/

             $query="select dflt_dimension_id from 0_users where employee_id='".$emp_data['id']."' ";
             $dimension_id=db_fetch(db_query($query));
             $img_url='';
             include('../../config.php');

           //$url='<img src="http://localhost/DXBB_TY/assets/images/tasheel_logo.jpg" style="width:10%;height:20%;"/>';

             if(!empty($dimension_id['dflt_dimension_id']))
             {
                 if($dimension_id['dflt_dimension_id']=='2')
                 {
                     $url= $base_url."assets/images/tasheel_logo.jpg";
                     $img_url='<img src="'.$url.'" style="width:20%;height:20%;" />';
                 }
                 if($dimension_id['dflt_dimension_id']=='3')
                 {
                     $url= $base_url."assets/images/tasheel_logo.jpg";
                     $img_url='<img src="'.$url.'" style="width:20%;height:20%;" />';
                 }

                 if($dimension_id['dflt_dimension_id']=='11')
                 {
                     $url= $base_url."assets/images/amer_logo.jpg";
                     $img_url='<img src="'.$url.'" style="width:20%;height:20%;" /> ';
                 }
             }


            /*******************EMPLOYEE SALARY DETAILS****************/

           $sql="SELECT a.pay_amount,b.element_name 
                     from 0_kv_empl_salary_details AS a
                     INNER JOIN 0_kv_empl_pay_elements AS b ON a.pay_rule_id=b.id
                    WHERE a.emp_id='".$emp_data['id']."' AND b.`type`='1' ORDER BY a.pay_amount DESC";
           $emp_split_slaary=db_query($sql);
           $employee_pay_elements='</br>';
           while($pay_element=db_fetch($emp_split_slaary))
           {
               $employee_pay_elements.=$pay_element['element_name'].' : '.number_format($pay_element['pay_amount'],2).'</br>';
           }

           /************************END********************************/
            if($emp_data['gender']=='1')
            {
                $annotation='Mr';
                $she_or_he='He';
                $his_or_her='His';
            }
            else if($emp_data['gender']=='2')
            {
               $annotation='Ms';
                $she_or_he='She';
                $his_or_her='Her';
            }


        /*if($sub_frm_id=='3')
               {*/
                   $frm_content=str_replace('KEY_REFERENCE_NUMBER', $emp_data['request_ref_no'], $content['html_content']);
                   $frm_content=str_replace('KEY_ADDRESS_OF', $emp_data['address_to'], $frm_content);
                   $frm_content=str_replace('KEY_EMPLOYEE_NAME', $emp_data['EmpName'], $frm_content);
                   $frm_content=str_replace('KEY_NATIONALITY', $emp_data['nation'], $frm_content);
                   $frm_content=str_replace('KEY_PASSPORT_NO', $pass_data['doc_no'], $frm_content);
                   $frm_content=str_replace('KEY_EMPLOYEE_POSITION',$emp_data['desig'], $frm_content);
                   $frm_content=str_replace('KEY_GENDER', $she_or_he, $frm_content);
                   $frm_content=str_replace('KEY_JOIN_DATE', date('d/m/Y',strtotime($emp_data['joining'])), $frm_content);
                   $frm_content=str_replace('KEY_ANNOTATION_PREFX', $his_or_her, $frm_content);
                    $frm_content=str_replace('KEY_SALARY',number_format($emp_tot_salary['tot_salary'],2), $frm_content);
                   $frm_content=str_replace('KEY_PAY_DETAILS',$employee_pay_elements, $frm_content);
                   $frm_content=str_replace('KEY_HEADER_LOGO',$img_url, $frm_content);
                   //$frm_content=str_replace('TO_ADDRESS',$emp_data['address_to'], $frm_content);
               // }
              /* else
                {
                    $frm_content=str_replace('EMPLOYE_NAME', $emp_data['EmpName'], $content['html_content']);
                    $frm_content=str_replace('SC_REF_NO', $emp_data['request_ref_no'], $frm_content);
                   $frm_content=str_replace('PRINT_DATE', date('d/m/Y'), $frm_content);
                   $frm_content=str_replace('EMPL_ID', $emp_data['empl_id'], $frm_content);
                   $frm_content=str_replace('EMP_PASSPORT_NO', $pass_data['doc_no'], $frm_content);
                   $frm_content=str_replace('EMP_JOIN_DATE', date('d/m/Y',strtotime($emp_data['joining'])), $frm_content);
                   $frm_content=str_replace('EMP_DESIG', $emp_data['desig'], $frm_content);
                   $frm_content=str_replace('EMP_TOT_SALARY', 'AED '.number_format($emp_tot_salary['tot_salary'],2), $frm_content);
                   $frm_content=str_replace('CONVERT_WRDS', $emp_data['desig'], $frm_content);
                   $frm_content=str_replace('EMP_ANNOTATION',$annotation, $frm_content);
                   $frm_content=str_replace('REQUST_REASON',$emp_data['comments'], $frm_content);
                   $frm_content=str_replace('EMP_COUNTRY',$emp_data['nation'], $frm_content);
                   $frm_content=str_replace('TO_ADDRESS',$emp_data['address_to'], $frm_content);
                   $frm_content=str_replace('EMP_BANK_NAME',$emp_data['bank'], $frm_content);
                   $frm_content=str_replace('BANK_IBAN',$emp_data['iban'], $frm_content);
                   $frm_content=str_replace('BANK_BRANCH',$emp_data['branch'], $frm_content);   
                }*/



         
       }
       else if($form_id=='5')
       {

              
          $sql_d="SELECT a.request_ref_no,CONCAT(e.empl_firstname,'',e.empl_lastname) AS EmpName,
                    e.empl_id,d.description AS desig,c.joining,w.description AS dept,n.description AS nation
                          ,CONCAT(g.empl_firstname,'',g.empl_lastname) AS linemanger ,a.comments,e.id,e.gender,a.comments,a.address_to,a.noc_name
                    From 0_kv_empl_noc_request as a 
                    INNER JOIN 0_kv_empl_info AS e ON e.id=a.empl_id
                    INNER JOIN 0_kv_empl_job AS c ON c.empl_id=e.id
                    INNER JOIN 0_kv_empl_designation AS d ON d.id=c.desig
                    INNER JOIN 0_kv_empl_departments AS w ON w.id=c.department
                    INNER JOIN 0_kv_empl_nationalities AS n ON n.id=c.nationality
                    INNER JOIN 0_kv_empl_info AS g ON g.id=e.report_to
                    WHERE a.id='".$request_id."'";

            $emp_data=db_fetch(db_query($sql_d));

            $passport_det="select issue_date,expiry_date,doc_no from 0_employee_docs where emp_id='".$emp_data['id']."'
            and type_id='1' ";
            $pass_data=db_fetch(db_query($passport_det));

$annotation='';
            if($emp_data['gender']=='1')
            {
                $annotation='Mr';
            }
            else if($emp_data['gender']=='2')
            {
               $annotation='Ms'; 
            }




                   $frm_content=str_replace('EMPLOYE_NAME', $emp_data['EmpName'], $content['html_content']);
                   $frm_content=str_replace('NOC_FOR', $emp_data['noc_name'], $frm_content);
                   $frm_content=str_replace('LETTER_TO', $emp_data['address_to'], $frm_content);
                   $frm_content=str_replace('EMP_NATION', $emp_data['nation'], $frm_content);
                   $frm_content=str_replace('EMP_DESIGNATION', $emp_data['desig'], $frm_content);
                   $frm_content=str_replace('EMP_PASS_NO', $pass_data['doc_no'], $frm_content);
                   $frm_content=str_replace('EMP_JOIN_DATE', date('d/m/Y',strtotime($emp_data['joining'])), $frm_content);
                   $frm_content=str_replace('REF_NO', $emp_data['request_ref_no'], $frm_content);
                   $frm_content=str_replace('EMP_ANNOTATION', $annotation, $frm_content);
                   $frm_content=str_replace('EMPL_ID', $emp_data['empl_id'], $frm_content);
                   $frm_content=str_replace('PRINT_DATE', date('d/m/Y'), $frm_content);

                   
       }


      
 /*echo $frm_content;

 die;*/
        

      $form=['status'=>'OK','msg'=>$frm_content];
      return AxisPro::SendResponse($form);
       // return $frm_content;

    } 




    public function list_all_approved_requests()
    {
        $type=$_POST['type_id'];
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

      
        $field_name='';
        $sub_frm_id='';
        $type_of_request='';

         if($type=='1')
        {
            $table='0_kv_empl_leave_applied';
        }
        if($type=='2')
        {
            $table='0_kv_empl_certificate_request';
            $field_name=',a.request_date,a.hr_status';
            $sub_frm_id=',a.certifcate_name';
        }

        if($type=='3')
        {
            $table='0_kv_empl_passport_request';
            $field_name=',a.request_date,a.hr_status';
            $type_of_request='<label style="color:black;font-weight:bold;">Passport Request</label>';
        }

        if($type=='4')
        {
            $table='0_kv_empl_loan_request';
            $field_name='loan_required_date,a.hr_status';
            $type_of_request='<label style="color:black;font-weight:bold;">Loan Request</label>';
        }

        if($type=='5')
        {
            $table='0_kv_empl_noc_request';
            $field_name=',a.request_date,a.hr_status';
            $type_of_request='<label style="color:black;font-weight:bold;">NOC Request<label>';
        }

        if($type=='6')
        {
            $table='0_kv_asset_request';
            $field_name=',a.request_date';
            $type_of_request='Asset Request';
        }

        if($type=='1')
        {
            $sql="SELECT a.request_ref_no,CONCAT(e.empl_id,' - ',e.empl_firstname,'',e.empl_lastname) AS EmpName
                 ".$field_name.",e.empl_id,a.id as req_id ".$sub_frm_id.",a.date as start_date,a.t_date as end_date
                 ,a.req_status as request_status,a.id as Req_pk_id,l.description as leave_name,a.sick_leave_doc
                 From ".$table." as a 
                 INNER JOIN 0_kv_empl_info AS e ON e.id=a.empl_id
                 LEFT JOIN 0_kv_empl_job AS h ON h.empl_id=e.id
                 LEFT JOIN 0_kv_empl_leave_types as l ON l.id=a.leave_type
                 WHERE a.empl_id IN (select empl_id from 0_kv_empl_job where department='".$_POST['dept']."' ) ";
        }
        else
        {
            $sql="SELECT a.comments,a.request_ref_no,CONCAT(e.empl_id,' - ',e.empl_firstname,'',e.empl_lastname) AS EmpName
                 ".$field_name.",e.empl_id,a.id as req_id ".$sub_frm_id.",a.id as Req_pk_id
                 From ".$table." as a 
                 INNER JOIN 0_kv_empl_info AS e ON e.id=a.empl_id
                 LEFT JOIN 0_kv_empl_job AS h ON h.empl_id=e.id
                 WHERE a.empl_id IN (select empl_id from 0_kv_empl_job where department='".$_POST['dept']."' ) ";
        }

        if($_POST['filter_status']!='')
        {
            $sql.=" AND a.req_status='".$_POST['filter_status']."' ";
        }



        $sql.=" Limit ".$start.",".$length."";


        $result=db_query($sql);
        $row_cnt=db_num_rows($result); 
        $data=array();
        $sub_from_id='';
        $hr_status_lbl='';
        $leave_request_date='';
        $request_status='';
        $request_approved_by='';

        $root_url= "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        while($req_data=db_fetch($result))
        {

            $request_approved_by=$this->getRequest_approved_by($req_data['Req_pk_id']);

            if($req_data['certifcate_name']!='')
            {
                $sub_from_id=$req_data['certifcate_name'];

                if($sub_from_id=='3')
                {
                    $type_of_request='<label style="color:black;font-weight:bold;">Salary certificate</label>';
                }
                else if($sub_from_id=='4')
                {
                    $type_of_request='<label style="color:black;font-weight:bold;">Salary transfer certificate</label>';
                }
            }


            if($req_data['hr_status']==1)
            {
                $hr_status_lbl='<label style="color:green;font-size:10pt;font-weight:bold;">Given</label>';
            }
            else if($req_data['hr_status']==2)
            {
                 $hr_status_lbl='<label style="color:blue;font-size:10pt;font-weight:bold;">Return back</label>';
            }
            else
            {
                $hr_status_lbl='';
            }

 

            $alt_attr='alt="'.$req_data['empl_id'].'" alt_type="'.$type.'" 
                       alt_req_id="'.$req_data['req_id'].'" alt_sub_frm_id="'.$sub_from_id.'" ';

            $controls="<div style='display: inline-flex;'><button alt_action='1' class='btn-primary btnprint' 
                      ".$alt_attr." style='width: 62px;'>Print</button>&nbsp;&nbsp;
                      <button  class='btn-info btnupdate' ".$alt_attr." style='width: 145px;'>Update Print Status</button></div>";

            if($type=='1')
            {
           
                if($req_data['request_status']=='0')
                {
                    $request_status='<label style="color:red;font-weight:bold;">Pending</label>';
                }
                else if($req_data['request_status']=='1')
                {
                    $request_status='<label style="color:green;font-weight:bold;">Approved</label>';
                }
                else if($req_data['request_status']=='2')
                {
                    $request_status='<label style="color:black;font-weight:bold;">Rejected</label>';
                }




                $leave_request_date= date('d/m/Y',strtotime($req_data['start_date'])).' - '. date('d/m/Y',strtotime($req_data['end_date']));

                $data[] = array(
                    $req_data['request_ref_no'],
                    $req_data['EmpName'],
                    $leave_request_date,
                    $request_status,
                    $req_data['leave_name'],
                    '<a href="assets/uploads/'.$req_data['sick_leave_doc'].'" target="_blank">'.$req_data['sick_leave_doc'].'</a>',
                    '<span style="color:blue;font-weight:bold;">'.$request_approved_by.'</span>'
                );
            }
            else
            {
                $data[] = array(
                    $req_data['request_ref_no'],
                    $req_data['EmpName'],
                    $type_of_request,
                    date('d/m/Y',strtotime($req_data['request_date'])),
                    $hr_status_lbl,
                    $req_data['comments'],
                    '<span style="color:blue;font-weight:bold;">'.$request_approved_by.'</span>',
                    $controls
                );
            }

        } 


        $tot_sql="SELECT *
                From ".$table." as a 
                INNER JOIN 0_kv_empl_info AS e ON e.id=a.empl_id
                 LEFT JOIN 0_kv_empl_job AS h ON h.empl_id=e.id
                 WHERE a.empl_id IN (select empl_id from 0_kv_empl_job where department='".$_POST['dept']."' ) ";
        if($_POST['filter_status']!='')
        {
            $tot_sql.=" AND a.req_status='".$_POST['filter_status']."' ";
        }
                $tot_res=db_query($tot_sql);
                $rows=db_num_rows($tot_res);


         $result_data = array(
            "draw" => $draw,
            "recordsTotal" => $rows,
            "recordsFiltered" => $rows,
            "data" => $data
        );


        return AxisPro::SendResponse($result_data);
    

    }


    public function getRequest_approved_by($request_id)
    {
      $sql="SELECT e.real_name
            From 0_kv_empl_req_approve_history as a 
            INNER JOIN 0_users AS e ON e.id=a.approved_by
            WHERE a.request_id='".$request_id."' ";

        $res=db_query($sql);
        $approved_by='';
        while($doc_data=db_fetch($res)) {
            $approved_by.=$doc_data['real_name'].',';
        }

        return rtrim($approved_by, ',');


    }


    public function update_hr_status()
    {
        $_type_id=$_POST['type'];
        $request_id=$_POST['req_id'];
        $hr_status=$_POST['status'];
        $hr_comment=$_POST['comment'];


        
        $sql="INSERT into 0_kv_empl_document_provide_log (request_id,type_id,hr_status,
              hr_comment,created_on,created_by)
              VALUES ('".$request_id."','".$_type_id."','".$hr_status."','".$hr_comment."','".date('Y-m-d')."',
              '".$_SESSION['wa_current_user']->user."')";

        if(db_query($sql))
        {
            $insert_id=db_insert_id();
                $path_to_root='../..';
                if(!empty($_FILES["documentFile"]))
                {
                    $target_dir = $path_to_root."/assets/uploads/";

                    $fname=explode(".",$_FILES["documentFile"]["name"]);
                    $rand=rand(10,100);
                    $filename=$fname[0].'_'.$rand.'.'.$fname[1];
                    $target_file = $target_dir . basename($filename);
                    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                    if ($_FILES["documentFile"]["size"] > 50000000) {
                        $upload_msg='File size exceeded';
                    }
                    if($imageFileType != "pdf" && $imageFileType != "docx") {
                        $upload_msg='File format is not allowed';
                    }

                    if (move_uploaded_file($_FILES["documentFile"]["tmp_name"], $target_file)) {
                        $file_update=" UPDATE 0_kv_empl_document_provide_log 
                                       SET signed_file='".$filename."' WHERE request_id='".$request_id."'
                                       and type_id='".$_type_id."' ";
                        db_query($file_update);
                    }

                }

                $msg=['status'=>'OK','msg'=>'Data Saved Successfully'];

                if($_type_id=='2')
                {
                    $table='0_kv_empl_certificate_request';
                }

                if($_type_id=='3')
                {
                    $table='0_kv_empl_passport_request';
                }

                if($_type_id=='4')
                {
                    $table='0_kv_empl_loan_request';
                }

                if($_type_id=='5')
                {
                    $table='0_kv_empl_noc_request';
                }

                if($_type_id=='6')
                {
                    $table='0_kv_asset_request';
                }  


           /**************************UPDATE status against request*********************/
             $sql_qry="Update ".$table." set hr_status='".$hr_status."'
                       where id='".$request_id."' ";
             db_query($sql_qry);
           /************************************END*************************************/
        }
        else
        {
            $msg=['status'=>'OK','msg'=>'Error Occured While Saving Data'];
        }


        return AxisPro::SendResponse($msg); 



    }

}