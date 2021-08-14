<?php
// ----------------------------------------------------------------
// Creator: Kvvaradha
// email:   admin@kvcodes.com
// Title:   HRM Extention for Frontaccounting
// website: http://www.kvcodes.com
// ----------------------------------------------------------------
include_once($path_to_root. '/includes/ui/simple_crud_class.inc');
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_license_db.inc");

class license extends simple_crud {
	var $selected_id;
	var $entity;
	var $sub_class;
	var $class; 
	
	function __construct($name, $id, $class, $subclass=null) {
		$fields = array('license_category','license_type','license_number', 'issuing_country','issue_date','expiry_date');

		parent::__construct($name, $fields);
		$this->class = $class;
		$this->subclass = $subclass;
		$this->entity = $id;
		
	}

	function list_view() {
		br();

		$license = get_person_licenses($this->entity);
			
		start_table(TABLESTYLE, "width=80%");

		$th = array( trans("License Category"), trans("License Type"), trans("License Number"), trans("Issuing Country"), trans("Issue Date"), trans("Expiry Date"), trans("Filename"), "", "", "");

		table_header($th);
		
		foreach($license as $de) {
			//display_error(json_encode($de));
			label_cell($de[2]);					
			label_cell($de[3]);
			label_cell($de[4]);
			label_cell($de[5]);			
			label_cell(sql2date($de[6]));		
			label_cell(sql2date($de[7]));			
			label_cell($de[8]);								
			edit_button_cell("{$this->name}Edit[{$de[0]}]", trans("Edit"));
				
			label_cell($de['unique_name'] != '' ? viewer_link(trans("View/Download"), 'modules/ExtendedHRM/manage/employees.php?vw='.$de[0]) : '' );

			// delete button removes only category unless this is the last license for this license
			delete_button_cell("{$this->name}Delete[{$de[0]}]", trans("Delete"));
			end_row();
					
		}
		end_table(1);
	}
	
	function editor_view(){

		$this->list_view();
		br();
		
		if($this->selected_id != null || $this->selected_id != "") {
			$de = GetRow('kv_empl_license', array('id' => $this->selected_id));
			
			//foreach($degre as $de) {
				start_outer_table(TABLESTYLE);
				//display_error(json_encode($de));
				$_POST['license_category'] = $de['license_category'];
				$_POST['license_type'] = $de['license_type'];
				$_POST['license_number'] = $de['license_number'];
				$_POST['issuing_country'] = $de['issuing_country'];
				$_POST['issue_date'] = sql2date($de['issue_date']);
				$_POST['expiry_date'] = sql2date($de['expiry_date']);
				table_section(1);
				table_section_title(trans("Work license"));
				empl_picklist_row(trans("License Category :"), 'license_category', null, false, false, 6);
				empl_picklist_row(trans("License Type :"), 'license_type', null, false, false, 5);
				text_row(trans("License Number:"), 'license_number', null, 35, 40);
				country_list_row(trans("Country:"), 'issuing_country', null);
				date_row(trans("Issue Date:"), 'issue_date', null);
				date_row(trans("Expiry Date:"), 'expiry_date', null);
				file_row(trans("Attached File") . ":", 'kv_attach_name', 'kv_attach_name');
					
				end_outer_table(1);	
			
			//}
		}
		else {
			start_outer_table(TABLESTYLE);

			table_section(1);
			table_section_title(trans("Work license"));
				empl_picklist_row(trans("License Category :"), 'license_category', null, false, false, 6);
				empl_picklist_row(trans("License Type :"), 'license_type', null, false, false, 5);
				text_row(trans("License Number:"), 'license_number', null, 35, 40);
				country_list_row(trans("Country:"), 'issuing_country', null);
				date_row(trans("Issue Date:"), 'issue_date', null);
				date_row(trans("Expiry Date:"), 'expiry_date', null);
				file_row(trans("Attached File") . ":", 'kv_attach_name', 'kv_attach_name');
			end_outer_table(1);
		}
	}

	function _bottom_controls()	{
		$title=false;
		$async='both';
		$base=$this->name;

		$cancel = $async;

		if ($async === 'both') {
			$async = 'default'; $cancel = 'cancel';
		} 
		else if ($async === 'default')
			$cancel = true;
		else if ($async === 'cancel')
			$async = true;
		echo "<center>";

		if ($this->Mode == '' || $this->Mode == 'RESET')
			submit("{$base}NEW", trans("Add new"), true, $title, $async);
		else {
			if ($this->Mode == 'NEW' || $this->selected_id==$this->_none)
				
				submit("{$base}ADD", trans("Add"), true, $title, $async);
			else {
				submit("{$base}UPDATE[{$this->selected_id}]", trans("Update"), true, trans("Submit changes"), $async);
			}
			submit("{$base}RESET", trans("Cancel"), true, trans("Cancel edition"), $cancel);
		}
		echo "</center>";
	}
	
	function db_insert() {
		$dat = $this->data;
		$license_id = add_empl_license($this->entity, $dat['license_category'], $dat['license_type'], $dat['license_number'], $dat['issuing_country'], $dat['issue_date'], $dat['expiry_date']);

		if (isset($_FILES['kv_attach_name']) && $_FILES['kv_attach_name']['name'] != '') {
			$max_image_size = 5000;
			$result = $_FILES['kv_attach_name']['error'];
			$upload_file = 'Yes'; 
			$attr_dir = company_path().'/attachments' ; 
			if (!file_exists($attr_dir)){				
				mkdir($attr_dir);
			}
			$dir = $attr_dir.'/licenses';
			if (!file_exists($dir)){
				mkdir($dir);
			}	
			/*$doc_ext = substr(trim($_FILES['kv_attach_name']['name']), strlen($_FILES['kv_attach_name']['name'])-3); 
			if($doc_ext == 'ocx' ) {
				$doc_ext = substr(trim($_FILES['kv_attach_name']['name']),strlen($_FILES['kv_attach_name']['name'])-4); 
			}*/
			$filename = basename($_FILES['kv_attach_name']['name']);
			$tmp = explode('.', $filename);
			$ext = strtolower(end($tmp));
			
			if(in_array($ext, array('docx','doc', 'pdf', 'jpg', 'jpeg', 'gif', 'png', 'bmp', 'rtf', 'txt'))){	
				
				$filesize = $_FILES['kv_attach_name']['size'];
				$filetype = $_FILES['kv_attach_name']['type'];
				
				$unique_name = uniqid();

				if ( $filesize > ($max_image_size * 1024)) { //File Size Check
					display_warning(trans('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $max_image_size);
					$upload_file ='No';
				}elseif (file_exists($dir."/".$unique_name)){
					$result = unlink($dir."/".$unique_name);
					if (!$result) 	{
						display_error(trans('The existing Bill could not be removed'));
						$upload_file ='No';
					}
				}
					
				if ($upload_file == 'Yes'){
					$result = move_uploaded_file($_FILES['kv_attach_name']['tmp_name'], $dir."/".$unique_name);			
				}
				Update('kv_empl_license', array('id' => $license_id), array( 'filename' => $filename, 'unique_name' => $unique_name, 'filetype' => $filetype, 'filesize' => $filesize));
			} else 
				display_error(trans("The Selected File format is not supported, try files within this format (.jpg, png, doc,docx, rtf,pdf)"));
		}
		return $license_id;
	}

	function db_update() {	
		$license_id = $this->selected_id; 		 

		if (isset($_FILES['kv_attach_name']) && $_FILES['kv_attach_name']['name'] != '') {
			$max_image_size = 5000;
			$result = $_FILES['kv_attach_name']['error'];
			$upload_file = 'Yes'; 
			$attr_dir = company_path().'/attachments' ; 
			if (!file_exists($attr_dir)){				
				mkdir($attr_dir);
			}
			$dir = $attr_dir.'/licenses';
			if (!file_exists($dir)){
				mkdir($dir);
			}	
			$doc_ext = substr(trim($_FILES['kv_attach_name']['name']), strlen($_FILES['kv_attach_name']['name'])-3); 
			if($doc_ext == 'ocx' ) {
				$doc_ext = substr(trim($_FILES['kv_attach_name']['name']),strlen($_FILES['kv_attach_name']['name'])-4); 
			}
			
			$filename = basename($_FILES['kv_attach_name']['name']);
			$tmp = explode('.', $filename);
			$ext = strtolower(end($tmp));
			
			if(in_array($ext, array('docx','doc', 'pdf', 'jpg', 'jpeg', 'gif', 'bmp', 'png', 'rtf', 'txt'))){					
				$filesize = $_FILES['kv_attach_name']['size'];
				$filetype = $_FILES['kv_attach_name']['type'];			
				
				$row = GetRow('kv_empl_license', array('id' => $license_id));
				
				//if ($row['filename'] == "")
					//exit();
				if ($row['unique_name'] != "")
					$unique_name = $row['unique_name'];
				else
					$unique_name = uniqid();
				
				if ($filename && file_exists($dir."/".$unique_name))
					unlink($dir."/".$unique_name);			
				
				if ( $filesize > ($max_image_size * 1024)) { //File Size Check
					display_warning(trans('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $max_image_size);
					$upload_file ='No';
				}elseif (file_exists($dir."/".$unique_name)){
					$result = unlink($dir."/".$unique_name);
					if (!$result) 	{
						display_error(trans('The existing Bill could not be removed'));
						$upload_file ='No';
					}
				}
				
				if ($upload_file == 'Yes'){
					
					$result = move_uploaded_file($_FILES['kv_attach_name']['tmp_name'], $dir."/".$unique_name);			
				}
				Update('kv_empl_license', array('id' => $license_id), array( 'filename' => $filename, 'unique_name' => $unique_name, 'filetype' => $filetype, 'filesize' => $filesize));
			} else 
				display_error(trans("The Selected File format is not supported, try files within this format (.jpg, png, doc,docx, rtf,pdf)"));
		}
		$job = GetRow('kv_empl_license', array('empl_id' => $this->entity));
		$return= update_empl_license($this->entity, $_POST['license_category'], $_POST['license_type'], $_POST['license_number'], $_POST['issuing_country'], $_POST['issue_date'], $_POST['expiry_date'], $this->selected_id);
		$array_info = GetRow('kv_empl_license', array('empl_id' => $this->entity));
		$result=array_diff($array_info,$job);
			$last_change=Today();
			foreach ($result as $key => $value) {
				Insert('kv_empl_history', array('empl_id' => $_POST['empl_id'], 'option_name' => $key, 'option_value' => $value,'last_change' =>array($last_change,'date')));
			}
			return $return;
	}

	function insert_check() {
		if (strlen($_POST['license_category']) == 0) {
			display_error(trans("The license category cannot be empty."));
			set_focus('license_category');
			return false;
		}
		if (strlen($_POST['license_type']) == 0) {
			display_error(trans("license type cannot be empty."));
			set_focus('license_type');
			return false;
		}
		if (strlen($_POST['license_number']) == 0) {
			display_error(trans("Employee license number cannot be empty."));
			set_focus('license_number');
			return false;
		}
		if (strlen($_POST['issuing_country']) == 0) {
			display_error(trans("license issuing country cannot be empty."));
			set_focus('issuing_country');
			return false;
		}
		if (count(@$_POST['issue_date']) == 0) {
			display_error(trans("Select Employee license issue date."));
			set_focus('issue_date');
			return false;
		}
		if (count(@$_POST['expiry_date']) == 0) {
			display_error(trans("Select Employee license expiry date."));
			set_focus('expiry_date');
			return false;
		} 
		return true; 
	}

	function db_read() {
		return get_empl_license($this->entity);
	}

	function delete_check() {
		return true;
	}
	
	function db_delete() {
		if (delete_empl_license($this->selected_id, $this->class, $this->entity)) {
			return true; 
		}
		return false;
	}
}

class empl_license extends simple_crud {
	var $selected_id;
	var $entity;
	var $sub_class;
	var $class; 
	
	function __construct($name, $id, $class, $subclass=null) {
		$fields = array('license_category','license_type','license_number', 'issuing_country','issue_date','expiry_date');

		parent::__construct($name, $fields);
		$this->class = $class;
		$this->subclass = $subclass;
		$this->entity = $id;
		
	}

	function list_view() {
		br();

		$license = get_person_licenses($this->entity);
			
		start_table(TABLESTYLE, "width=80%");

		$th = array( trans("License Category"), trans("License Type"), trans("License Number"), trans("Issuing Country"), trans("Issue Date"), trans("Expiry Date"), trans("Filename"),'');

		table_header($th);
		
		foreach($license as $de) {
			//display_error(json_encode($de));
			label_cell($de[2]);					
			label_cell($de[3]);
			label_cell($de[4]);
			label_cell($de[5]);			
			label_cell(sql2date($de[6]));		
			label_cell(sql2date($de[7]));			
			label_cell($de[8]);								
			label_cell($de['unique_name'] != '' ? viewer_link(trans("View/Download"), 'modules/ExtendedHRM/manage/employees.php?vw='.$de[0]) : '' );
			end_row();
					
		}
		end_table(1);
	}

	function _bottom_controls()	{
	}
}
?>