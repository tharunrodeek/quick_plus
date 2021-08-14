<?php
/**********************************************************************
 AuthoR : Kvvaradha 
 Module : HRM  
 Developed to kvcodes.com 
***********************************************************************/
include_once($path_to_root. '/includes/ui/simple_crud_class.inc');

class empl_leave extends simple_crud {
	var $selected_id;
	var $entity;
	var $sub_class;
	var $class;
	
	function __construct($name, $id, $class, $subclass=null) {
		$fields = array('#','date','leave_type','days', 'reason', 'status', 'empl_id', 'year');
		parent::__construct($name, $fields);
		$this->class = $class;
		$this->subclass = $subclass;
		$this->entity = $id;
		
	}

	function list_view() {
	global $hrm_empl_leave_types;
		br();
		start_table();
			kv_fiscalyears_list_row(trans("Fiscal Year:"), 'year', null, true);
		end_table();
		br();
		$degree = GetAll('kv_empl_leave_applied', array('empl_id' => $this->entity, 'year' => get_post('year')));	
		start_table(TABLESTYLE, "width=80%");
		$th = array(trans("Leave Type"), trans("Reason"), trans("Date"), trans("Days"), trans("Status"), "", "");
		table_header($th);		
		foreach($degree as $de) {				
			label_cell($hrm_empl_leave_types[$de['leave_type']]);					
			label_cell($de['reason']);
			label_cell(sql2date($de['date']));
			label_cell($de['days']);
			label_cell($de["status"]== 1 ? 'Accepted' :  ($de["status"]== 0 ? 'Pending' : 'Rejected'));		
			if($de['status'] == 1 ) {
				label_cell('');
				label_cell('');
			} else {
				edit_button_cell("{$this->name}Edit[{$de['id']}]", trans("Edit"));
				delete_button_cell("{$this->name}Delete[{$de['id']}]", trans("Delete"));
			}
			end_row();		
		}
		end_table(1);
	}
	
	function editor_view(){
		$this->list_view();
		br();	
		
		start_outer_table(TABLESTYLE);				
		table_section(1);
		table_section_title(trans("Degree data"));

		if($this->selected_id != null || $this->selected_id != "") {
			$de = GetRow('kv_empl_leave_applied', array( 'id' => $this->selected_id));	
			$_POST['leave_type'] = 	$de['leave_type'];
			$_POST['date'] = 	sql2date($de['date']);
			$_POST['days'] = 	$de['days'];
			$_POST['reason'] = 	$de['reason'];
			$_POST['status'] = 	$de['status'];
		}else {	
			$_POST['leave_type'] = $_POST['days'] = $_POST['reason'] = '';
			unset($_POST['date']);
		}
		hrm_empl_leave_type_row(trans("Leave Type"), "leave_type");
		date_row(trans("Date") . ":", 'date');
		text_row(trans("Days:"), 'days', null, 5, 10);
		textarea_row(trans("Reason:"), 'reason', null, 35, 5);
		if(isset($de['filename']) && $de['filename'] != null){
			label_row(trans("Attachment"), viewer_link($de["filename"], 'modules/ExtendedHRM/employee/?vw='.$de["id"]));
		}
		kv_doc_row(trans("Select Docs") . ":", 'kv_attach_name', 'kv_attach_name');
		$isset_report_to = GetSingleValue('kv_empl_info', 'report_to', array('empl_id' => $this->entity));

		label_row(trans('Status'), (isset($_POST['status']) ? ( $_POST['status'] == 1 ? 'Accepted' : ($_POST['status'] == 0 ? 'Pending' : ($_POST['status'] == 2 ? 'Rejected' : ('-')))  )  :   'Pending' ) );
		hidden('status', isset($_POST['status']) ? $_POST['status'] : 0);
		
		label_row(trans("Report To:"), GetSingleValue('kv_empl_info', 'CONCAT(`empl_firstname`, " ", `empl_lastname`)', array('empl_id' => $isset_report_to)));
		end_outer_table(1);	
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
		$return = Insert('kv_empl_leave_applied', array( 'reason' => $_POST['reason'], 'leave_type' => $_POST['leave_type'], 'days' => $_POST['days'], 'date' => array($_POST['date'], 'date'), 'empl_id' => $this->entity, 'year' => $_POST['year'], 'status' => $_POST['status']));
		$this->UploadHandle($return, $this->entity);
		return $return;
	}	

	function db_update() {
		$dat = $this->data;
		$this->UploadHandle($this->selected_id, $this->entity);
		return Update('kv_empl_leave_applied', array( 'id' => $this->selected_id), array( 'reason' => $_POST['reason'], 'leave_type' => $_POST['leave_type'], 'days' => $_POST['days'], 'year' => $_POST['year']));
	}

	function insert_check() {
		if (strlen($_POST['reason']) == 0) {
			display_error(trans("The reason cannot be empty."));
			set_focus('degree');
			return false;
		}
		
		return true; 
	}

	function db_read() {
		return GetRow('kv_empl_leave_applied', array('id' => $this->entity));
	}
	
	//	Delete all degree for degree in current class/entity
	function db_delete() {
		$attach = GetRow('kv_empl_leave_applied', array('id' => $this->selected_id));
		$attr_dir = company_path().'/attachments/empldocs/'.$attach['empl_id'].'/'.$attach['filename']; 
		if($attach['filename'] && file_exists($attr_dir) && !is_dir($attr_dir))
			unlink($attr_dir);
		if (Delete('kv_empl_leave_applied', array('id' => $this->selected_id))) {	}
		return false;
	}

	function UploadHandle($id, $empl_id){
		if (isset($_FILES['kv_attach_name']) && $_FILES['kv_attach_name']['name'] != '') {
			$max_image_size = 5000;
			$result = $_FILES['kv_attach_name']['error'];
			$upload_file = 'Yes'; 
			$attr_dir = company_path().'/attachments' ; 
			if (!file_exists($attr_dir)){				
				mkdir($attr_dir);
			}
			$dir = $attr_dir.'/empldocs/'.$empl_id.'/';
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
				
				$unique_name = $id.'-'.$filename;

				if ( $filesize > ($max_image_size * 1024)) { //File Size Check
					display_warning(trans('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $max_image_size);
					$upload_file ='No';
				}elseif (file_exists($dir."/".$unique_name)){
					$result = unlink($dir."/".$unique_name);
					if (!$result) 	{
						display_error(trans('The existing Bill could not be removed'));
						$upload_file ='No';
					}
				}else {
					$attach = GetRow('kv_empl_leave_applied', array('id' => $id));
					$attr_dir = company_path().'/attachments/empldocs/'.$attach['empl_id'].'/'.$attach['filename']; 
					if($attach['filename'] && file_exists($attr_dir) && !is_dir($attr_dir))
						unlink($attr_dir);
				}
					
				if ($upload_file == 'Yes'){
					$result = move_uploaded_file($_FILES['kv_attach_name']['tmp_name'], $dir."/".$unique_name);			
				}
				Update('kv_empl_leave_applied', array('id' => $id), array( 'filename' => $unique_name));
			} else 
				display_error(trans("The Selected File format is not supported, try files within this format (.jpg, png, doc,docx, rtf,pdf)"));
		}
	}
}
?>