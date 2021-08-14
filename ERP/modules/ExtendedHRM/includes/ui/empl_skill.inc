<?php
// ----------------------------------------------------------------
// Creator: Kvvaradha
// email:   admin@kvcodes.com
// Title:   HRM Extention for Frontaccounting
// website: http://www.kvcodes.com
// ----------------------------------------------------------------
include_once($path_to_root. '/includes/ui/simple_crud_class.inc');

class skill extends simple_crud {
	var $selected_id;
	var $entity;
	var $sub_class;
	var $class; 
	
	function __construct($name, $id, $class, $subclass=null) {
		$fields = array('language','proficiency');

		parent::__construct($name, $fields);
		$this->class = $class;
		$this->subclass = $subclass;
		$this->entity = $id;
		
	}

	function list_view() {
		br();

		//$skill = GetAll('kv_empl_skills', array('empl_id' => $this->entity));
			$skill = GetDataJoin('kv_empl_skills AS skill', array( 
								0 => array('join' => 'INNER', 'table_name' => 'kv_empl_picklist AS pick', 'conditions' => '`pick`.`id` = `skill`.`language`'),
								1 => array('join' => 'INNER', 'table_name' => 'kv_empl_picklist AS pick2', 'conditions' => '`pick2`.`id` = `skill`.`proficiency`'), 						
							), 
							array('`pick`.`description` AS Lang, `pick2`.`description` AS Prof, `skill`.`id`'), array('`skill`.`empl_id`' => $this->entity));
		start_table(TABLESTYLE, "width=40%");
		$th = array( trans("Language"), trans("Proficiency"), "", "");
		table_header($th);
		
		foreach($skill as $de) {		
			label_cell($de['Lang']);					
			label_cell($de['Prof']);										
			edit_button_cell("{$this->name}Edit[{$de['id']}]", trans("Edit"));
			delete_button_cell("{$this->name}Delete[{$de['id']}]", trans("Delete"));
			end_row();
					
		}
		end_table(1);
	}
	
	function editor_view(){
		$this->list_view();
		br();
		
		if($this->selected_id != null || $this->selected_id != "") {
			$de = GetRow('kv_empl_skills', array('id' => $this->selected_id));
				
			$_POST['language'] = $de['language'];
			$_POST['proficiency'] = $de['proficiency'];
		}		

		start_table(TABLESTYLE);
			table_section_title(trans("Skill Set"));
			empl_picklist_row(trans("Language :"), 'language', null, false, false, 1);
			empl_picklist_row(trans("Proficiency:"), 'proficiency', null, false, false, 2);
		end_table();
		br(2);
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
		return Insert('kv_empl_skills', array( 'empl_id' => $this->entity, 'language' => $_POST['language'], 'proficiency' => $_POST['proficiency']));
	}

	function db_update() {	
		$skill_id = $this->selected_id; 		 
		
		return Update('kv_empl_skills', array( 'empl_id' => $this->selected_id), array('language' => $_POST['language'], 'proficiency' => $_POST['proficiency']));
	}

	function insert_check() {
		
		return true; 
	}

	function db_read() {
		return GetRow('kv_empl_skills', array('id' => $this->entity));
	}

	function delete_check() {
		return true;
	}
	
	function db_delete() {
		if (Delete('kv_empl_skills', array('id' => $this->selected_id))) {
			return true; 
		}
		return false;
	}
}

class empl_skill extends simple_crud {
	var $selected_id;
	var $entity;
	var $sub_class;
	var $class; 
	
	function __construct($name, $id, $class, $subclass=null) {
		$fields = array('language','proficiency');

		parent::__construct($name, $fields);
		$this->class = $class;
		$this->subclass = $subclass;
		$this->entity = $id;
		
	}

	function list_view() {
		br();

		//$skill = GetAll('kv_empl_skills', array('empl_id' => $this->entity));
			$skill = GetDataJoin('kv_empl_skills AS skill', array( 
								0 => array('join' => 'INNER', 'table_name' => 'kv_empl_picklist AS pick', 'conditions' => '`pick`.`id` = `skill`.`language`'),
								1 => array('join' => 'INNER', 'table_name' => 'kv_empl_picklist AS pick2', 'conditions' => '`pick2`.`id` = `skill`.`proficiency`'), 						
							), 
							array('`pick`.`description` AS Lang, `pick2`.`description` AS Prof, `skill`.`id`'), array('`skill`.`empl_id`' => $this->entity));
		start_table(TABLESTYLE, "width=40%");
		$th = array( trans("Language"), trans("Proficiency"));
		table_header($th);
		
		foreach($skill as $de) {		
			label_cell($de['Lang']);					
			label_cell($de['Prof']);										
			end_row();
					
		}
		end_table(1);
	}
	function _bottom_controls()	{
}
}

?>