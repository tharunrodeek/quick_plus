<?php    
	
	define('DB_DUPLICATE_ERROR', 1062);
	define('SQL_MODE', '');
	if (!defined('TB_PREF')) {
		define('TB_PREF', '&TB_PREF&');
	}
	global $security_areas, $security_groups, $security_headings, $path_to_root, $db, $db_connections, $dbpref;

	$path_to_root = "../../..";
	//include ($path_to_root . "/api/includes/sales_types_db.inc");

	require_once("Rest.inc.php");	
		
	class API extends REST {		
	
		public $data = "";
						
		private $db ;

		public $dbpref;

		public $companyy;

		public $login_id = NULL;
	
		public function __construct(){
			parent::__construct();	
		}

		function db_num_rows($result){	return mysqli_num_rows($result); }

		function db_fetch_row ($result){	return mysqli_fetch_row($result);	}

		function db_fetch_assoc ($result){	return mysqli_fetch_assoc($result);	}

		function db_fetch ($result){	return mysqli_fetch_array($result, MYSQLI_ASSOC);	}

		function db_insert_id()	{ global $db; return mysqli_insert_id($db); }

		function db_num_affected_rows(){ global $db;return mysqli_affected_rows($db);}
		
		function set_global_connection($company=-1) {
			global $db, $path_to_root, $db_connections;
			include ($path_to_root . "/config_db.php");
			$this->cancel_transaction(); // cancel all aborted transactions (if any) //$_SESSION["wa_current_user"]->cur_con = $company;
			$connection = $db_connections[$company];	//$this->companyy = $db_connections[$company];
			$this->dbpref = $connection['tbpref'];		//const DEF_CONNECTION = $connection['tbpref'];

			$db = mysqli_connect($connection["host"], $connection["dbuser"], $connection["dbpassword"]);
				mysqli_select_db($db, $connection["dbname"]);
			
			if (strncmp(mysqli_get_server_info($db), "5.6", 3) >= 0) 
				$this->db_query("SET sql_mode = ''");

			return $db;
		}

		function cancel_transaction(){
			global $transaction_level;

			if ($transaction_level) {
				db_query("ROLLBACK", "could not cancel a transaction");	
			}
			$transaction_level = 0;
		}
		
		function db_query($sql, $err_msg=null, $testing=false){
			global $db, $dbpref;
			$cur_prefix = $this->dbpref;
		//	$sql = str_replace('\\', '', $sql);  //stripslashes($sql);
			$sql = str_replace(TB_PREF, $cur_prefix, $sql);

			$sql = str_replace('TB_PREF', $cur_prefix, $sql);			

			if($testing){				
				$error = array('status' => 'query', "msg" => $sql);
				$this->response($this->json($error), 400);
			}			
			$result = mysqli_query($db, $sql);
			if(!$result && $err_msg != null ){       
		        return $err_msg;
		    }		    
		    return $result;
		}

		public	function db_escape($value = "", $nullify = false){	
			global $db;		
			$value = @html_entity_decode($value);
			$value = @htmlspecialchars($value);

		  	//reset default if second parameter is skipped
			$nullify = ($nullify === null) ? (false) : ($nullify);

		  	//check for null/unset/empty strings
			if ((!isset($value)) || (is_null($value)) || ($value === "")) {
				$value = ($nullify) ? ("NULL") : ("''");
			} else {
				if (is_string($value)) {
		      		//value is a string and should be quoted; determine best method based on available extensions
					if (function_exists('mysqli_real_escape_string')) {
				  		$value = "'" . mysqli_real_escape_string($db, $value) . "'";
					} else {
					  $value = "'" . mysqli_escape_string($db, $value) . "'";
					}
				} else if (!is_numeric($value)) {
					//value is not a string nor numeric
					display_error("ERROR: incorrect data type send to sql query");
					echo '<br><br>';
					exit();
				}
			}
			return $value;
		}
				
		/*
		 * Public method for access api.
		 * This method dynmically call the method based on the query string
		 */
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
		}
		
		/* 
		 *	Simple login API
		 *  Login must be POST method
		 *  email : <USER EMAIL>
		 *  pwd : <USER PASSWORD>
		 */
		public function login(){ // Cross validation if the request method is POST else it will return "Not Acceptable" status
			global $db_connections, $db, $dbpref;

			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			$email = $this->_request['email'];		
			$password = $this->_request['pwd'];
			$company = $this->_request['company'];

			$db = $this->set_global_connection($company);
			
			if($db){
				$Auth_Result = $this->get_user_auth($email, md5($password));

				//$error = array('status' => 'final', "msg" => $Auth_Result['id']);
				//$this->response($this->json($error), 400);			
			}
			if(isset($Auth_Result['id'])){
				return $Auth_Result;
			}
			return null;
		}

		function db_Has_Data_on_Table($table_name, $primary_key =false){

		    $sql = "SELECT COUNT(*) FROM ".TB_PREF.$table_name." WHERE 1=1";
		    if($primary_key){
		        foreach($primary_key as $key=>$value){
		            if(is_array($value)) { 
		                if($value[1] == 'date')             
		                    $sql .= " AND ".$key." = ". $this->db_escape($value[0]).",";
		                if($value[1] == 'float')
		                    $sql .= " AND ".$key." = ". $value.",";
		            }else{
		                if(is_numeric($value)){
		                    $sql .=" AND ". $key." = ".$value;
		                }else
		                   $sql .= " AND ".$key." = ".$this->db_escape($value);
		            }
		        }
		    }
		    return $this->kv_check_empty_result($sql);
		}

		function kv_check_empty_result($sql){
			$result = $this->db_query($sql, "could not do check empty query");	
			
			$myrow = $this->db_fetch_row($result);
			return $myrow[0] > 0;
		}

		//-----------------------------------------------------------------------------------------------
		function get_user_auth($user_id, $password){
			global $db, $dbpref;
			$sql = "SELECT * FROM ".TB_PREF."users WHERE user_id = ".$this->db_escape($user_id)." AND"
				." password=".$this->db_escape($password);
			//return $this->db_query($sql, "could not get validate user login for $user_id");

			$cur_prefix = $this->dbpref;
			$sql = str_replace(TB_PREF, $cur_prefix, $sql);

			$result = mysqli_query($db, $sql);
				
			if(!$result && $err_msg != null ){       
		        return $err_msg;
		    }
			return $this->db_fetch($result) ;
		}
	
		/*
		 *	Inserting...
		 */
		function Insert($loginskip =false, $table_name= false, $data = false, $empl_id=false ){
			if($loginskip || $this->login() > 0){
				if(!$table_name)
					$table_name = $this->_request['table'];	
				if(!$data)
					$data = $this->_request['data'];

			    $sql0 = "INSERT INTO ".TB_PREF.$table_name." (";
			    $sql1 = " VALUES (";
			    foreach($data as $key=>$value){
			        $sql0 .= "`".$key."`,";
					if(is_array($value)) { 
						if($value[1] == 'date')				
							$sql1 .=  $this->db_escape($value[0]).",";
						if($value[1] == 'float')
							$sql1 .= $value.",";
					}else 
						$sql1 .= $this->db_escape($value).",";
			    }
			    $sql0 = substr($sql0, 0, -1).")";
			    $sql1 = substr($sql1, 0, -1).")";
			    //$string =  str_replace('\"', '',$sql0.$sql1);
			    $string = stripslashes($sql0.$sql1);
			   	
				$sql = $this->db_query($sql0.$sql1, 'Unable to insert your Data. Please Contact Adminstrator');
				
				if($this->db_insert_id() > 0){
					$final_status = "Employee ID#".$empl_id." Attendance Updated";
					$status_code = 200;
				} else{
					$status_code = 400;
					$final_status = "Employee ID#".$empl_id." Failed to Update Attendance";
				}
				$status = array('status' => "Success", "msg" => $final_status);
				$this->response($this->json($status), $status_code);

			}else{
				$error = array('status' => "Failed", "msg" => "Sorry You are not authenticated.");
				$this->response($this->json($error), 400);
			}
		}

		function is_date( $date, $format = 'Y-m-d' ) {
		    $d = DateTime::createFromFormat($format, $date);
    		return $d && $d->format($format) == $date;	    
		}

		/*
		 *	Update Employee Attendance...
		 */
		function UpdateSingleEmplAttendance(){

			if($this->login() > 0){
				$table_name = 'kv_empl_attendancee';	
				$data = $this->_request['data'];
				$empl_id = $this->_request['empl_id'];
				$empl_dept = false;
				if(isset($empl_id) && $empl_id != ''){
					$sql="SELECT department FROM ".TB_PREF."kv_empl_job WHERE empl_id=".$this->db_escape($empl_id); 
					$result = $this->db_query($sql, "could not do check empty query");	
			
					$myrow = $this->db_fetch_row($result);
					$empl_dept = $myrow[0];
				}

				if($this->is_date($data['date']) && $empl_dept && $this->is_date($data['in'], 'H:i:s') && $this->is_date($data['out'], 'H:i:s')){
					$month = (int)date('m', strtotime($data['date']));
					$year = 0;
					$sql = "SELECT id FROM ".TB_PREF."fiscal_year WHERE begin <".$this->db_escape($data['date'])." AND end > ".$this->db_escape($data['date'])." LIMIT 1";
					$res = $this->db_query($sql, "Cant get fiscal year");
					if($yr = $this->db_fetch_row($res))
						$year = $yr[0];
					if($year != 0 ){
						
						$primary_key = array('empl_id' => $empl_id, 'month' => $month, 'year' => $year);
						$dayy = (int)date('d', strtotime($data['date']));
					    $in =  date('H:i:s', strtotime($data['in']));
					    $out =  date('H:i:s', strtotime($data['out']));
						$comment = 'P';
						$set_sql = "SELECT settings.* FROM ".TB_PREF."kv_empl_attendance_settings AS settings LEFT JOIN ".TB_PREF."kv_empl_job AS job ON job.department=settings.dept_id WHERE job.empl_id=1";
						$set_res = $this->db_query($set_sql, "Cant get fiscal year");
						$hrm_settings= array();
						if($this->db_num_rows($set_res)) {
							while($settings = $this->db_fetch($set_res)){
								$data_offdays = @unserialize(base64_decode($settings['option_value']));
								if ($data_offdays !== false) {
									$hrm_settings[$settings['option_name']] = unserialize(base64_decode($settings['option_value']));
								} else {
									$hrm_settings[$settings['option_name']] = $settings['option_value']; 
								}
							}
						}
						
						$attendance_set_sql = "SELECT settings.* FROM ".TB_PREF."kv_empl_attendance_settings AS settings LEFT JOIN ".TB_PREF."kv_empl_job AS job ON job.department=settings.dept_id WHERE job.empl_id=1";
						$attendance_set_res = $this->db_query($attendance_set_sql, "Cant get fiscal year");
						if($this->db_num_rows($attendance_set_res)) {
							$attendance_settings = array();
							while($row = $this->db_fetch($attendance_set_res)){
								$attendance_settings[$row['option_name']] = $row['option_value'];
							}
							
							//Consider Early In
							if($attendance_settings['early_coming_punch'] != 1 && strtotime($hrm_settings['BeginTime']) >= strtotime($in)  ){
								$in = $hrm_settings['BeginTime']; 
							}

							//Consider Early Going Punch
							if($attendance_settings['late_going_punch'] != 1 && strtotime($hrm_settings['EndTime']) <= strtotime($out)  ){
								$out = $hrm_settings['EndTime']; 
							}
							
							// Grace time for Late Punch in 
							if(strtotime($hrm_settings['BeginTime']) <= strtotime($in)){

								$secs = strtotime($attendance_settings['grace_in_time'])-strtotime("00:00:00");
								$office_time_grace_time = strtotime($hrm_settings['BeginTime'])+$secs;

								if( $office_time_grace_time < strtotime($in)){  
									if($attendance_settings['mark_half_day_late'] == 1) { // Mark Half day, if late coming by 
										$morning_late_time = strtotime($attendance_settings['mark_half_day_late_min'])-strtotime("00:00:00");
										$office_time_late_grace_time = strtotime($hrm_settings['BeginTime'])+$morning_late_time;

										if( $office_time_late_grace_time < strtotime($in)){
											$comment = 'HD';
										}
									}
								}				
							}	
							
							// Grace Time for Early Punch Go out
							if( strtotime($hrm_settings['EndTime']) > strtotime($out)  ){ 

								$secs = strtotime($attendance_settings['grace_out_time'])-strtotime("00:00:00");
								$office_time_grace_time = strtotime($hrm_settings['EndTime'])-$secs;

								if( $office_time_grace_time > strtotime($out)){  // Mark Half day, if early going by 
									if($attendance_settings['mark_half_day_early_go'] == 1){
										$evening_early_time = strtotime($attendance_settings['mark_half_day_early_go_min'])-strtotime("00:00:00");
										$office_time_early_grace_time = strtotime($hrm_settings['EndTime'])-$evening_early_time;

										if( $office_time_early_grace_time > strtotime($out)){											
											$comment = 'HD';
										}
									}
								}
							}

							// Mark half day if work duration less than ...
							if($attendance_settings['Halfday_workduration'] == 1){
								$secs = strtotime($attendance_settings['Halfday_workduration_min'])-strtotime("00:00:00");
								$worked_time =  strtotime($out) - strtotime($in);
								if($worked_time < $secs){
									$comment = 'HD';
								}
							} 

							// Mark Absent if work duration less than ...
							if($attendance_settings['absent_workduration'] == 1) {
								$secs = strtotime($attendance_settings['absent_workduration_min'])-strtotime("00:00:00");
								$worked_time =  strtotime($out) - strtotime($in);
								if($worked_time < $secs){
									$comment = 'A';
								}
							}			
						}

					    if($this->db_Has_Data_on_Table($table_name, $primary_key)){
					    	
					        $sql0 = "UPDATE ".TB_PREF.$table_name." SET `{$dayy}` ='".$comment."', `{$dayy}vj_in` =".$this->db_escape($in).", `{$dayy}vj_out` =".$this->db_escape($out).", dept_id = ".$this->db_escape($empl_dept). " WHERE empl_id =".$this->db_escape($empl_id)." AND month=".$month." AND year=".$year;
					       
					        $final_status = $this->db_query($sql0, "Could not update data on table {$table_name}");
					        if($final_status == true){
					        	$final_status = "Employee ID#".$empl_id." Attendance Updated";
					        	$status_code = 200;
					        } else{
					        	$status_code = 400;
								$final_status = "Employee ID#".$empl_id." Failed to Update Attendance";
							}
						    $status = array('status' => "Success", "msg" => $final_status);
							$this->response($this->json($status), $status_code);

					    }else{
					        foreach($primary_key as $key => $value){
					            if($key != 'id')
					                $data[$key] = $value;
					        }
					        unset($data['date']);
					        unset($data['in']);
					        unset($data['out']);
					        $data['dept_id'] = $empl_dept;
					        $data[$dayy.'vj_in'] = $in;
					        $data[$dayy.'vj_out'] = $out;
					        $data[$dayy] = 'P';
					        $this->Insert(true, $table_name, $data, $empl_id);
					    } 
					}
					
				} else {
					if($empl_dept == false){
						$error = array('status' => "Failed", "msg" => "Given Employee ID is invalid");
						$this->response($this->json($error), 400);
					}else{
						$error = array('status' => "Failed", "msg" => "Given Date or Time is invalid");
						$this->response($this->json($error), 400);
					}					
				}				 
		    } else{
				$error = array('status' => "Failed", "msg" => "Sorry You are not authenticated.");
				$this->response($this->json($error), 400);
			}   
		}	

		/*
		 *	Encode array into JSON
		 */
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}	

	}
	
	// Initiiate Library
	
	$api = new API;
	$api->processApi();
	
?>