<?php
/**********************************************************************
 AuthoR : Kvvaradha 
 Module : HRM  
 Developed to kvcodes.com 
***********************************************************************/
include_once($path_to_root. '/includes/ui/simple_crud_class.inc');

class degree extends simple_crud {
	var $selected_id;
	var $entity;
	var $sub_class;
	var $class;
	
	function __construct($name, $id, $class, $subclass=null) {
		$fields = array('empl_id','degree','major','university','grade', 'year_of_passing');

		parent::__construct($name, $fields);
		$this->class = $class;
		$this->subclass = $subclass;
		$this->entity = $id;
		
	}

	function list_view() {
		br();

		$degree = GetAll('kv_empl_degree', array('empl_id' => $this->entity));	
		start_table(TABLESTYLE, "width=80%");
		$th = array(trans("Degree"), trans("Major"), trans("University"), trans("Grade/ Percentage"), trans("Year of Passing"), "", "");
		table_header($th);		
		foreach($degree as $de) {				
			label_cell($de['degree']);					
			label_cell($de['major']);
			label_cell($de['university']);
			label_cell($de['grade']);
			label_cell(sql2date($de['year']));
					
			edit_button_cell("{$this->name}Edit[{$de['id']}]", trans("Edit"));
			delete_button_cell("{$this->name}Delete[{$de['id']}]", trans("Delete"));
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
			$de = GetRow('kv_empl_degree', array( 'id' => $this->selected_id));
			$_POST['year_of_passing']=sql2date($de[6]);	
			text_row(trans("Degree :"), 'degree', $de[2], 35, 40);
			text_row(trans("Major:"), 'major', $de[3], 35, 40);
			text_row(trans("University:"), 'university', $de[4], 35, 40);
			text_row(trans("Grade/ Percentage:"), 'grade', $de[5], 32, 30);
			date_row(trans("Year of Passing:"), 'year_of_passing');
			
		}else {	
			text_row(trans("Degree :"), 'degree', null, 35, 40);
			text_row(trans("Major:"), 'major', null, 35, 40);
			text_row(trans("University:"), 'university', @$this->data['university'], 35, 40);
			text_row(trans("Grade/ Percentage:"), 'grade', @$this->data['grade'], 32, 30);
			date_row(trans("Year of Passing:"), 'year_of_passing', @$this->data['year_of_passing']);
		}
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
			
		return Insert('kv_empl_degree', array('empl_id' => $this->entity, 'degree' => $dat['degree'], 'major' => $dat['major'], 'university' => $dat['university'], 'grade' => $dat['grade'], 'year' => array($dat['year_of_passing'], 'date')));
	}

	function db_update() {
		$dat = $this->data;
		return Update('kv_empl_degree', array( 'id' => $this->selected_id), array('empl_id' => $this->entity, 'degree' => $dat['degree'], 'major' => $dat['major'], 'university' => $dat['university'], 'grade' => $dat['grade'], 'year' => array($dat['year_of_passing'], 'date')) );
	}

	function insert_check() {
		if (strlen($_POST['degree']) == 0) {
			display_error(trans("The degree name cannot be empty."));
			set_focus('degree');
			return false;
		}
		if (strlen($_POST['university']) == 0) {
			display_error(trans("University cannot be empty."));
			set_focus('university');
			return false;
		}
		if (count(@$_POST['grade']) == 0) {
			display_error(trans("You have to enter either Grade or Percentage here."));
			set_focus('grade');
			return false;
		}
		return true; 
	}

	function db_read() {
		return GetRow('kv_empl_degree', array('id' => $this->entity));
	}
	
	//	Delete all degree for degree in current class/entity
	function db_delete() {
		if (Delete('kv_empl_degree', array('id' => $this->selected_id))) {	}
		return false;
	}
}

class empl_degree extends simple_crud {
	var $selected_id;
	var $entity;
	var $sub_class;
	var $class;	
	function __construct($name, $id, $class, $subclass=null) {
		$fields = array('empl_id','degree','major','university','grade', 'year_of_passing');
		parent::__construct($name, $fields);
		$this->class = $class;
		$this->subclass = $subclass;
		$this->entity = $id;		
	}

	function list_view() {
		br();
		$degree = GetAll('kv_empl_degree', array('empl_id' => $this->entity));		
		start_table(TABLESTYLE, "width=80%");
		$th = array(trans("Degree"), trans("Major"), trans("University"), trans("Grade/ Percentage"), trans("Year of Passing"));
		table_header($th);		
		foreach($degree as $de) {				
			label_cell($de['degree']);					
			label_cell($de['major']);
			label_cell($de['university']);
			label_cell($de['grade']);
			label_cell(sql2date($de['year']));
			end_row();
					
		}
		end_table(1);
	}
	function _bottom_controls()	{}
}
?>
