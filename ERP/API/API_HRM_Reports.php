<?php
include_once("AxisPro.php");
include_once("PrepareQuery.php");

Class API_HRM_Reports
{
    /*----------------------PREPARE TIMESHEET---------------*/

    public function prepare_shift_report()
    {
       

          if($_SESSION['wa_current_user']->access=='2')
          {
               $Fdate=$_POST['frmDate'];
               $Tdate=$_POST['toDate'];

                $dept_id=$_POST['dept_id'];
                $shift_id=$_POST['shift_id'];
          }
          else
          {
            
               $current_date=date('Y-m-d');
               $Fdate= isset($_POST['frmDate']) ? $_POST['frmDate'] : $current_date;
               $Tdate= isset($_POST['toDate']) ? $_POST['toDate'] : date('Y-m-d', strtotime('+7 days'));

               $empl_info=$this->getEmployeeInfo();
               $dept_id=$empl_info['dept_id'];
               $shift_id=0;
          }



      
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

            $sql = "  SELECT a.empl_id,CONCAT(i.empl_id,' - ',i.empl_firstname) as Employeename,a.shift_id
                       FROM 0_kv_empl_shiftdetails AS a
                       INNER JOIN 0_kv_empl_info AS i ON i.id=a.empl_id
                       INNER JOIN 0_kv_empl_job AS b ON b.empl_id=a.empl_id
                       WHERE a.dept_id='".$dept_id."'";

            if(!in_array($_SESSION['wa_current_user']->access, [2,25, 17, 16,18]))
                {
                  $sql.=" AND b.empl_id='".$empl_info['empl_id']."' ";
                }

            if (!in_array($_SESSION['wa_current_user']->access, [2,25, 17, 16,18])) {

               $sql.=" AND shift_id<>'777' GROUP BY a.empl_id,a.shift_id ";

            }
            else
            {
               $sql.=" AND shift_id<>'777' GROUP BY a.empl_id ";
            }         

           


            $sql.= " LIMIT $start_from, $limit";

           //echo $sql.' --- ';
            $result = db_query($sql);

            $table="<table   class=\"table\">
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
               // }

                $table.="<th $color >".$disp_head."</th>";
            }
            $table.=" </tr></thead>
                      <tbody>";
            $i=0;
            while ($myrow = db_fetch($result)) {

              if($_SESSION['wa_current_user']->access=='2')
              {
                 $shift_id=$shift_id;
              }
              else
              {
                $shift_id=$myrow['shift_id'];
              }

               
                $shift_name=$this->getShiftName($shift_id);

                $table.="<tr>";
                /*if($i=='0')
                {*/
                    $table.="<td style='border: 1px solid #ccc;    width: 23%;vertical-align : middle;text-align:center;font-weight: bold;'>".$shift_name."</td>
                       ";
                //}
                $table.="<td style='border: 1px solid #ccc;'>".$myrow['Employeename']."</td>
                       ";

                for($i=0;$i<sizeof($Date);$i++)
                {
                    $attendence=$this->getIsInShift($Date[$i],$myrow['empl_id'],$shift_id,$dept_id);
                    $get_off_days=$this->getShiftOffDays($Date[$i],$myrow['empl_id']);
                    $shift_attendence='';
                    if($attendence[0]!='')
                    {
                        $path='..';
                        $shift_attendence="<img src='assets/media/img/tick.png' style='width:14%;'/>";
                    }
                    else if($get_off_days['shift_id']=='777')
                    {
                      $shift_attendence="<label style='color: black;
    font-weight: bold;
    margin-top: 24%;'>OFF Day</label>";
                    }
                    $color=$this->getColorCodes($attendence[0]);
                    $table.="<td $color><label class='ClsAttendence' style='margin-top: 24%;'> ".$shift_attendence." </label></td>";
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
               if($_SESSION['wa_current_user']->access!='2')
                       {
                          $totCnt.=" AND b.empl_id='".$empl_info['empl_id']."' ";
                       }

            $totCnt.=" AND shift_id<>'777' GROUP BY a.empl_id,a.shift_id ";

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

   function getEmployeeInfo()
    {
      $user_id=$_SESSION['wa_current_user']->user;

        $sql="SELECT a.empl_id,a.department as dept_id
              FROM 0_kv_empl_job AS a
              INNER JOIN 0_users AS b ON a.empl_id=b.employee_id
              WHERE b.id='".$user_id."' ";

     
        $result = db_query($sql);
        return db_fetch($result);
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


    function getIsInShift($from,$emp_id,$shift_id,$dept_id)
    {
        $sql="select id from 0_kv_empl_shiftdetails 
              where dept_id='".$dept_id."' and empl_id='".$emp_id."' and shift_id='".$shift_id."' and DATE(s_date)='".date('Y-m-d',strtotime($from))."'";
       //echo $sql.'----';
        $result = db_query($sql);
        return db_fetch($result);
    }

    function getShiftOffDays($from,$emp_id)
    {
        $sql="select shift_id from 0_kv_empl_shiftdetails 
              where   empl_id='".$emp_id."' and DATE(s_date)='".date('Y-m-d',strtotime($from))."'";
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
           $font_color='style="color: white;border: 1px solid #ccc;"';
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

    public function getShiftName($shift_id)
    {
        $sql="SELECT id,description,BeginTime,EndTime
              FROM 0_kv_empl_shifts where id='".$shift_id."'";
        $result = db_query($sql);
        $myrow = db_fetch($result);

        return $myrow['description'].'  ('.$myrow['BeginTime'].' - '.$myrow['EndTime'].')';
    }



    public function list_all_employees()
    {

            $draw = intval($_POST["draw"]);
            $start = intval($_POST["start"]);
            $length = intval($_POST["length"]);


            $result=$this->sql_for_get_employees($_POST['dept_id'],$_POST['emp_id'],$_POST['desig']
              ,$_POST['cost_center'],$start,$length,1);
      
            $data=array();
             
            while($data_res=db_fetch($result))
            {

                $data[] = array(
                    $data_res['empl_id'],
                    $data_res['emp_name'],
                    $data_res['dept_name'],
                    $data_res['designation'],
                   date('d/m/Y',strtotime($data_res['joining'])),
                    $data_res['line_manager'],
                    $data_res['head_of_dept'],
                    $data_res['emp_cntry'],
                    $data_res['empl_city'],
                    $data_res['empl_state'],
                    date('d/m/Y',strtotime($data_res['date_of_birth'])),
                    $data_res['marital_status'],
                    $data_res['gender'],
                    $data_res['mobile_phone'],
                    $data_res['email'],
                    $data_res['cost_center'],
                    $data_res['user_id'],
                );
            }


          $tot_sql="SELECT a.id
                    FROM 0_kv_empl_info AS a
                    INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                    WHERE b.department='".$_POST['dept_id']."'";
          if($_POST['emp_id']!='0')
            {
              $tot_sql.=" AND a.id='".$_POST['emp_id']."' ";
            }
          if($_POST['desig']!='0')
            {
              $tot_sql.=" AND b.desig='".$_POST['desig']."' ";
            }

          if($_POST['cost_center']!='0')
            {
              $tot_sql.=" AND b.cost_center='".$_POST['cost_center']."' ";
            }
          $tot_rws=db_num_rows(db_query($tot_sql));          


          $result_data = array(
            "draw" => $draw,
            "recordsTotal" => $tot_rws,
            "recordsFiltered" => $tot_rws,
            "data" => $data
          );

        return AxisPro::SendResponse($result_data);


    }



     public function get_all_designations()
    {

        $sql = "SELECT a.id,a.description 
                FROM 0_kv_empl_designation AS a
                INNER JOIN 0_kv_empl_designation_group AS b ON b.id=a.division
                WHERE b.depti_id='".$_POST['dept_id']."' ";

        
        $result = db_query($sql);

        
        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result);
    }

     public function get_all_cost_centers()
    {

        $sql = "SELECT id,name 
                FROM 0_dimensions ";
        
        $result = db_query($sql);

        
        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result);
    }


   public function export_employees_list()
   {
       $filename = 'Employees_List'.date('Ymd').'.csv'; 
       header("Content-Description: File Transfer"); 
       header("Content-Disposition: attachment; filename=$filename"); 
       header("Content-Type: application/csv; ");

       $file = fopen('php://output', 'w');
       $header = array("Emp-Code",
                       "Emp.Name",
                       "Dept. Name",
                       "Designation",
                       "Joining Date",
                       "Line Manager",
                       "Head Of Dept.",
                       "Country",
                       "City",
                       "State",
                       "DOB",
                       "Marital Status",
                       "Gender",
                       "Mobile",
                       "Email",
                       "Cost Center",
                  );

       fputcsv($file, $header);

      $result=$this->sql_for_get_employees($_POST['ddl_department'],$_POST['employee'],$_POST['ddl_designation']
              ,$_POST['cost_center'],0,0,2);




            $data=[];
            $i=0;
             
            while($data_res=db_fetch($result))
            {

                $data[] = array(
                    $data_res['empl_id'],
                    $data_res['emp_name'],
                    $data_res['dept_name'],
                    $data_res['designation'],
                   date('d/m/Y',strtotime($data_res['joining'])),
                    $data_res['line_manager'],
                    $data_res['head_of_dept'],
                    $data_res['emp_cntry'],
                    $data_res['empl_city'],
                    $data_res['empl_state'],
                    date('d/m/Y',strtotime($data_res['date_of_birth'])),
                    $data_res['marital_status'],
                    $data_res['gender'],
                    $data_res['mobile_phone'],
                    $data_res['email'],
                    $data_res['cost_center'],
                    $data_res['user_id'],
                );


              fputcsv($file,$data[$i]); 
             $i++;

            }

            fclose($file); 
      exit; 


   }


   public function sql_for_get_employees($dept_id,$empl_id,$desig_id,$cost_center,$start,$end,$print_type)
   {
       $sql="SELECT a.empl_id,CONCAT(a.empl_firstname,' ',a.empl_lastname) AS emp_name,a.empl_city,a.empl_state,
                a.date_of_birth,CASE WHEN a.marital_status='1' THEN 'Single' WHEN a.marital_status='2' THEN 'Married' END AS marital_status
                ,CASE WHEN a.gender='1' THEN 'Male' WHEN a.gender='2' THEN 'Female' END AS gender,a.mobile_phone,a.email,b.joining
                ,CONCAT(e.empl_id,' - ',e.empl_firstname,' ',e.empl_lastname) AS line_manager
                ,CONCAT(f.empl_id,' - ',f.empl_firstname,' ',f.empl_lastname) AS head_of_dept,b.department
                ,g.description as designation,h.`name` AS cost_center,c.description AS dept_name,d.local_name AS emp_cntry,s.user_id
              FROM 0_kv_empl_info AS a
              INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
              left JOIN 0_kv_empl_departments AS c ON c.id=b.department
              left JOIN 0_kv_empl_country AS d ON d.id=a.country
              left JOIN 0_kv_empl_info AS e ON e.id=a.report_to
              left JOIN 0_kv_empl_info AS f ON f.id=c.head_of_empl_id
              LEFT JOIN 0_kv_empl_designation AS g ON g.id=b.desig
              LEFT JOIN 0_dimensions AS h ON h.id=b.cost_center
              LEFT JOIN 0_users AS s ON s.employee_id=a.id
              WHERE b.department='".$dept_id."'  ";

            if($empl_id!='0')
            {
              $sql.=" AND a.id='".$empl_id."' ";
            }

            if($desig_id!='0')
            {
              $sql.=" AND b.desig='".$desig_id."' ";
            }

            if($cost_center!='0')
            {
              $sql.=" AND b.cost_center='".$cost_center."' ";
            }

            if($print_type=='1')
            {
              $sql.=" LIMIT ".$start.",".$end."  ";
            } 



            $result=db_query($sql);

            return $result;
   }

    public function get_emp_gpssa_list_for_datatable()
    {
        $params = $_REQUEST;

        $queryRecords = $this->get_gpssa_records($params);


        while ($row = db_fetch_assoc($queryRecords['data'])) {

            $gpssa_amount=$this->get_gpssa_amount($row['emp_pk_id'],$params['year']);



            $data[] = array(
                $row['description'],
                '('.$row['empl_id'].') '.$row['EmployeeName'],
                $gpssa_amount[0][1],$gpssa_amount[1][2],$gpssa_amount[2][3],$gpssa_amount[3][4],$gpssa_amount[4][5],
                $gpssa_amount[5][6],$gpssa_amount[6][7],$gpssa_amount[7][8],$gpssa_amount[8][9],$gpssa_amount[9][10],
                $gpssa_amount[10][11],$gpssa_amount[11][12]
            );
        }
        $json_data = array(
            "draw"            => intval($params['draw']),
            "recordsTotal"    => intval($queryRecords['total_records']),
            "recordsFiltered" => intval($queryRecords['total_records']),
            "data"            => $data,
            "params"          => $params,
        );
        echo json_encode($json_data);
    }


    public function get_gpssa_records($param)
    {
        $start = intval($param["start"]);
        $length = intval($param["length"]);
        $dept_id=$param['dept_id'];
        $empl_id=$param['emp_id'];

            $sql=" SELECT a.empl_id,CONCAT(a.empl_firstname,' ',a.empl_lastname) as EmployeeName,d.description,
                a.id as emp_pk_id
                 FROM 0_kv_empl_info AS a 
                 INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                 INNER JOIN 0_kv_empl_departments AS d ON d.id=b.department
                 WHERE d.id='".$dept_id."' AND a.status=1 AND b.calculate_pf=1";
        if($empl_id>0)
        {
            $sql.=" AND a.id='".$empl_id."' ";
        }

        $sqlRec=$sql;
        if(isset($start) && isset($length) && $_POST['export_excel']!='1'){
            $sqlRec .=  " LIMIT ". $start. " ," . $length;
        }

        $result=db_query($sqlRec);
        $tot_result=db_query($sql);
        $total_rows=db_num_rows($tot_result);

        return [
            'data'=>$result,
            'total_records'=>$total_rows
        ];

    }


    public function get_gpssa_amount($empl_id,$year)
    {

       $monthly_pf=[];
       for($i=1;$i<=12;$i++)
       {
           $sql="SELECT c.amount,d.empl_id
                    FROM 0_kv_empl_payroll_details AS d
                    INNER JOIN 0_kv_empl_payroll_elements AS c ON c.payslip_detail_id=d.id
                    INNER JOIN 0_kv_empl_pay_elements AS p ON p.id=c.pay_element
                    INNER JOIN 0_kv_empl_payroll_master AS m ON m.id=d.payslip_id
                    WHERE p.is_pf_account='1' AND m.pay_month='".$i."' 
                    AND m.pay_year='".$year."' AND d.empl_id='".$empl_id."' ";
           $result=db_query($sql);
           $pf_data=db_fetch($result);
           if(empty($pf_data['amount']))
           {
               $pf_data['amount']=0;
           }

           array_push($monthly_pf,[$i=>$pf_data['amount']]);
       }

       return $monthly_pf;
    }



    public function export_gpssa()
    {
        $filename = 'GPSSA_Report_'.date('Ymd').'.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");

        $file = fopen('php://output', 'w');
        $header = array("Department",
            "Emp.Name",
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        );

        fputcsv($file, $header);
        $data=[];
        $k=0;

        $queryRecords = $this->get_gpssa_records($_POST);


        while ($row = db_fetch_assoc($queryRecords['data'])) {

            $gpssa_amount=$this->get_gpssa_amount($row['emp_pk_id'],$_POST['year']);

            $data[] = array(
                $row['description'],
                '('.$row['empl_id'].') '.$row['EmployeeName'],
                $gpssa_amount[0][1],$gpssa_amount[1][2],$gpssa_amount[2][3],$gpssa_amount[3][4],$gpssa_amount[4][5],
                $gpssa_amount[5][6],$gpssa_amount[6][7],$gpssa_amount[7][8],$gpssa_amount[8][9],$gpssa_amount[9][10],
                $gpssa_amount[10][11],$gpssa_amount[11][12]
            );

            fputcsv($file,$data[$k]);
            $k++;


        }

        fclose($file);


    }


    public function get_emp_emergency_list_for_datatable()
    {
        $params = $_REQUEST;

        $queryRecords = $this->get_employee_records($params);


        while ($row = db_fetch_assoc($queryRecords['data'])) {

            $data[] = array(
                $row['description'],
                '('.$row['empl_id'].') '.$row['EmployeeName'],
                $row['ice_name'],
                $row['ice_phone_no']
            );
        }
        $json_data = array(
            "draw"            => intval($params['draw']),
            "recordsTotal"    => intval($queryRecords['total_records']),
            "recordsFiltered" => intval($queryRecords['total_records']),
            "data"            => $data,
            "params"          => $params,
        );
        echo json_encode($json_data);
    }


   public function get_employee_records($params)
   {
       $start = intval($params["start"]);
       $length = intval($params["length"]);

       $sql="SELECT a.ice_name,a.ice_phone_no, a.empl_id,CONCAT(a.empl_firstname,' ',a.empl_lastname) as EmployeeName,
                d.description,a.id as empl_pk_id
                FROM 0_kv_empl_info AS a
                INNER JOIN 0_kv_empl_job AS b ON a.id=b.empl_id
                INNER JOIN 0_kv_empl_departments AS d ON d.id=b.department
                WHERE a.status='1' AND b.department='".$params['dept_id']."' ";
       if(!empty($params['emp_id']))
       {
           $sql.=" and a.id='".$params['emp_id']."' ";
       }


       $sqlRec=$sql;
       if(isset($start) && isset($length) && $_POST['export_excel']!='1'){
           $sqlRec .=  " LIMIT ". $start. " ," . $length;
       }

       $result=db_query($sqlRec);
       $tot_result=db_query($sql);
       $total_rows=db_num_rows($tot_result);

       return [
           'data'=>$result,
           'total_records'=>$total_rows
       ];



   }

   public function export_emergency()
   {
       $filename = 'Emergency_Report_'.date('Ymd').'.csv';
       header("Content-Description: File Transfer");
       header("Content-Disposition: attachment; filename=$filename");
       header("Content-Type: application/csv; ");

       $file = fopen('php://output', 'w');
       $header = array("Department",
           "Emp.Name",
           "Emergency Contact Person",
           "Emergency Contact Number"
       );

       fputcsv($file, $header);
       $data=[];
       $k=0;

       $queryRecords = $this->get_employee_records($_POST);


       while ($row = db_fetch_assoc($queryRecords['data'])) {

           $data[] = array(
               $row['description'],
               '('.$row['empl_id'].') '.$row['EmployeeName'],
               $row['ice_name'],
               $row['ice_phone_no']
           );

           fputcsv($file,$data[$k]);
           $k++;


       }

       fclose($file);
   }


   public function get_emp_esb_list_for_datatable()
   {
       $params = $_REQUEST;

       $queryRecords = $this->get_employee_records($params);

       $esb_amount='';
       while ($row = db_fetch_assoc($queryRecords['data'])) {

           $esb_details=$this->get_each_emp_esb_details($row['empl_pk_id']);

           /*if($esb_details['tot_esb_amount']=='created')
           {
               $esb_amount='<label style="color:green;font-weight: bold;">Processed</label>';
           }
           else
           {
               $esb_amount="<label style='color:blue;font-weight:bold;'>".$esb_details['tot_esb_amount']."</label>";
           }*/


           $data[] = array(
               $row['description'],
               '('.$row['empl_id'].') '.$row['EmployeeName'],
               $esb_details['join_date'],
               $esb_details['years_complted'],
               $esb_details['loan_ded_amnt'],
               $esb_details['warning_ded_amount'],
               $esb_details['tot_esb_amount']
           );
       }
       $json_data = array(
           "draw"            => intval($params['draw']),
           "recordsTotal"    => intval($queryRecords['total_records']),
           "recordsFiltered" => intval($queryRecords['total_records']),
           "data"            => $data,
           "params"          => $params,
       );
       echo json_encode($json_data);
   }


    public function get_each_emp_esb_details($empl_id)
    {

        $sql="SELECT b.id,a.joining 
                FROM 0_kv_empl_job AS a
                INNER JOIN 0_kv_empl_info AS b ON a.empl_id=b.id
                WHERE b.id='".$empl_id."' ";

        $join_date=db_fetch(db_query($sql));


        $sql_bsic="select pay_amount from 0_kv_empl_salary_details where emp_id='".$join_date[0]."' 
                    and is_basic='1'";
        $basic_salary=db_fetch(db_query($sql_bsic));



        $ch_sql="select count(id) from 0_kv_empl_esb where empl_id='".$join_date[0]."' and status='1' ";
        $esb_done=db_fetch(db_query($ch_sql));

        $esb_amount='';
        $daliy_wage='';
        $esg_net_amount='';
        $msg='';

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
                ,'warning_ded_amount'=>$esb_done[2],'tot_esb_amount'=>'Processed'];
        }

        return $msg;

    }


    public function export_esb()
    {
        $filename = 'ESB_Report_'.date('Ymd').'.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");

        $file = fopen('php://output', 'w');
        $header = array("Department",
            "Emp.Name",
            "Join Date",
            "Years Completed",
            "Loan Pending Amount",
            "Warning Deduction Pending Amount",
            "ESB Calculated"
        );

        fputcsv($file, $header);
        $data=[];
        $k=0;

        $queryRecords = $this->get_employee_records($_POST);


        while ($row = db_fetch_assoc($queryRecords['data'])) {

            $esb_details=$this->get_each_emp_esb_details($row['empl_pk_id']);

            $data[] = array(
                $row['description'],
                '('.$row['empl_id'].') '.$row['EmployeeName'],
                $esb_details['join_date'],
                $esb_details['years_complted'],
                $esb_details['loan_ded_amnt'],
                $esb_details['warning_ded_amount'],
                $esb_details['tot_esb_amount']
            );

            fputcsv($file,$data[$k]);
            $k++;

        }

        fclose($file);
    }





}