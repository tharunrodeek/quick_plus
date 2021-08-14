<?php
// ----------------------------------------------------------------
// Creator: Kvvaradha
// email:   admin@kvcodes.com
// Title:   HRM Extention for Frontaccounting
// website: http://www.kvcodes.com
// ----------------------------------------------------------------
include_once($path_to_root. '/includes/ui/simple_crud_class.inc');
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_experience_db.inc");

class experience extends simple_crud {
	var $selected_id;
	var $entity;
	var $sub_class;
	var $class;
	
	function __construct($name, $id, $class, $subclass=null) {
		$fields = array('company_name','company_location','department', 'designation','from', 'to');

		parent::__construct($name, $fields);
		$this->class = $class;
		$this->subclass = $subclass;
		$this->entity = $id;
		
	}

	function list_view() {		
		br();
		$experience = get_person_experiences($this->entity);			
		start_table(TABLESTYLE, "width=80%");
		$th = array( trans("Company Name"), trans("Company Location"), trans("Department"), trans("Designation"),trans("Start Date"), trans("End Date"), "", "");
		table_header($th);		
		foreach($experience as $de) {
			label_cell($de[2]);					
			label_cell($de[3]);
			label_cell($de[4]);
			label_cell($de[5]);			
			label_cell(sql2date($de[6]));		
			label_cell(sql2date($de[7]));					
			edit_button_cell("{$this->name}Edit[{$de[0]}]", trans("Edit"));

			// delete button removes only category unless this is the last experience for this experience
			delete_button_cell("{$this->name}Delete[{$de[0]}]", trans("Delete"));
			end_row();					
		}
		end_table(1);
	}
	
	function editor_view(){
		br();
		$this->list_view();		
		br(2);
		
		if($this->selected_id != null || $this->selected_id != "") {
			$de = GetRow('kv_empl_experience', array('id' => $this->selected_id));
		
			if($de){
				$_POST['from'] = sql2date($de['s_date']);
				$_POST['to'] = sql2date($de['e_date']);
				$_POST['company_name'] = $de['company_name'];
				$_POST['company_location'] = $de['company_location'];
				$_POST['designation'] = $de['designation'];
				$_POST['department'] = $de['department'];			
			}
		}	
		
		start_outer_table(TABLESTYLE);

			table_section(1);
			table_section_title(trans("Work Experience"));
				text_row(trans("Company Name :"), 'company_name', null, 35, 40);
				text_row(trans("Company Location:"), 'company_location', null, 35, 40);
				text_row(trans("Department:"), 'department', null, 35, 40);
				text_row(trans("Designation:"), 'designation', null, 35, 40);
				date_row(trans("From:"), 'from', null);
				date_row(trans("To:"), 'to', null);
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
			
		return add_empl_experience($this->entity, $dat['company_name'], $dat['company_location'], $dat['department'], $dat['designation'], $dat['from'], $dat['to']);
	}

	function db_update() {	
		return update_empl_experience($this->entity, $_POST['company_name'], $_POST['company_location'], $_POST['department'], $_POST['designation'], $_POST['from'], $_POST['to'], $this->selected_id);
	}

	function insert_check() {
		if (strlen($_POST['company_name']) == 0) {
			display_error(trans("The Company name cannot be empty."));
			set_focus('company_name');
			return false;
		}
		if (strlen($_POST['company_location']) == 0) {
			display_error(trans("Company location cannot be empty."));
			set_focus('company_location');
			return false;
		}
		if (strlen($_POST['department']) == 0) {
			display_error(trans("Employee department cannot be empty."));
			set_focus('department');
			return false;
		}
		if (strlen($_POST['designation']) == 0) {
			display_error(trans("Employee designation cannot be empty."));
			set_focus('designation');
			return false;
		}
		if (!is_date(@$_POST['from'])) {
			display_error(trans("Select Employee career start up date."));
			set_focus('from');
			return false;
		}
		return true; 
	}

	function db_read() {
		return GetRow('kv_empl_experience', array('id' => $this->entity));
	}

	function delete_check() {
		return true;
	}
	
	function db_delete() {
		if (delete_empl_experience($this->selected_id, $this->class, $this->entity)) {
			
		}
		return false;
	}
}


class empl_experience extends simple_crud {
	var $selected_id;
	var $entity;
	var $sub_class;
	var $class;
	
	function __construct($name, $id, $class, $subclass=null) {
		$fields = array('company_name','company_location','department', 'designation','from', 'to');

		parent::__construct($name, $fields);
		$this->class = $class;
		$this->subclass = $subclass;
		$this->entity = $id;		
	}

	function list_view() {		
		br();
		$experience = get_person_experiences($this->entity);			
		start_table(TABLESTYLE, "width=80%");
		$th = array( trans("Company Name"), trans("Company Location"), trans("Department"), trans("Designation"),trans("Start Date"), trans("End Date"));
		table_header($th);		
		foreach($experience as $de) {				
			label_cell($de[2]);					
			label_cell($de[3]);
			label_cell($de[4]);
			label_cell($de[5]);			
			label_cell(sql2date($de[6]));		
			label_cell(sql2date($de[7]));			
			end_row();					
		}
		end_table(1);
	}		
	function _bottom_controls()	{}	
}
?>
