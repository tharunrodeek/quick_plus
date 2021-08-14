<?php
// ----------------------------------------------------------------
// Creator: Kvvaradha
// email:   admin@kvcodes.com
// Title:   HRM Extention for Frontaccounting
// website: http://www.kvcodes.com
// ----------------------------------------------------------------
include_once($path_to_root. '/includes/ui/simple_crud_class.inc');
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_training_db.inc");

/*
	View/Edit class for training
*/
class training extends simple_crud {
	var $selected_id;
	var $entity;
	var $sub_class;
	var $class;
	
	function __construct($name, $id, $class, $subclass=null) {
		$fields = array('training_desc','course','cost', 'institute', 'start_date','end_date', 'notes', //'lang','notess', 
		'assgn' =>array('fld'=>'training')
						);

		parent::__construct($name, $fields);
		$this->class = $class;
		$this->subclass = $subclass;
		$this->entity = $id;
	}

	function list_view() {
		br();
			$training = get_person_trainings( $this->entity);
			start_table(TABLESTYLE, "width=80%");
			$th = array( trans("Training Description"), trans("Course"), trans("Cost"), trans("Institute"), trans("Start Date"),	trans("End Date"), 	trans("Notes"), '',	"&nbsp;");

			table_header($th);
			foreach($training as $de) {
				label_cell($de[2]);					
				label_cell($de[3]);
				label_cell($de[4]);
				label_cell($de[5]);				
				label_cell(sql2date($de['s_date']));
				label_cell(sql2date($de['e_date']));	
				label_cell($de['notes']);
				edit_button_cell("{$this->name}Edit[{$de[0]}]", trans("Edit"));
				// delete button removes only category unless this is the last training for this training
				delete_button_cell("{$this->name}Delete[{$de[0]}]", trans("Delete"));
				end_row();
			}
		end_table(1);
	}
	
	function editor_view(){
		$this->list_view();
		br();
		
		if($this->selected_id != null || $this->selected_id != "") {
			$de = GetRow('kv_empl_training', array('id' => $this->selected_id));
		
			if($de) {
				start_outer_table(TABLESTYLE);
				$_POST['start_date'] = 	sql2date($de['s_date']);
				$_POST['end_date'] = 	sql2date($de['e_date']);
				$_POST['training_desc'] = $de['training_desc'];
				$_POST['course'] = $de['course'];
				$_POST['cost'] = $de['cost'];
				$_POST['institute'] = $de['institute'];
				$_POST['notes'] = $de['notes'];
			}
		}
		start_outer_table(TABLESTYLE);
			table_section(1);
			table_section_title(trans("Training"));
			textarea_row(trans("Training Description:"), 'training_desc', null, 30, 4);
			text_row(trans("Course :"), 'course',null, 35, 40);
			text_row(trans("Cost:"), 'cost',null, 35, 40);
			text_row(trans("Institute:"), 'institute', null, 35, 40);
			date_row(trans("Start Date:"), 'start_date',null);
			date_row(trans("End Date:"), 'end_date', null);
			textarea_row(trans("Notes:"), 'notes', null, 30, 4);
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
			return add_empl_training($this->entity, $dat['training_desc'], $dat['course'], $dat['cost'], $dat['institute'], $dat['start_date'], $dat['end_date'], $dat['notes']);
	}

	function db_update() {
			return update_empl_training($this->entity, $_POST['training_desc'], $_POST['course'], $_POST['cost'], $_POST['institute'], $_POST['start_date'], $_POST['end_date'], $_POST['notes'], $this->selected_id, $this->class);
	}

	function insert_check() {
		if (strlen($_POST['training_desc']) == 0) {
			display_error(trans("The training Description cannot be empty."));
			set_focus('training_desc');
			return false;
		}
		if (strlen($_POST['course']) == 0) {
			display_error(trans("Course Name cannot be empty."));
			set_focus('course');
			return false;
		}
		
		return true; 
	}

	function db_read() {
		return GetRow('kv_empl_training', array('id' => $this->selected_id));
	}

	function delete_check() {
		return true;
	}
	
	function db_delete() {
		if (delete_empl_training($this->selected_id, $this->class, $this->entity)) {
			
		}
		return false;
	}
}


class empl_training extends simple_crud {
	var $selected_id;
	var $entity;
	var $sub_class;
	var $class;
	
	function __construct($name, $id, $class, $subclass=null) {
		$fields = array('training_desc','course','cost', 'institute', 'start_date','end_date', 'notes', //'lang','notess', 
		'assgn' =>array('fld'=>'training')
						);

		parent::__construct($name, $fields);
		$this->class = $class;
		$this->subclass = $subclass;
		$this->entity = $id;
	}

	function list_view() {
		br();
			$training = get_person_trainings( $this->entity);
			start_table(TABLESTYLE, "width=80%");
			$th = array( trans("Training Description"), trans("Course"), trans("Cost"), trans("Institute"), trans("Start Date"),	trans("End Date"), 	trans("Notes"));

			table_header($th);
			foreach($training as $de) {
				label_cell($de[2]);					
				label_cell($de[3]);
				label_cell($de[4]);
				label_cell($de[5]);				
				label_cell(sql2date($de['s_date']));
				label_cell(sql2date($de['e_date']));	
				label_cell($de['notes']);				
				end_row();
			}
		end_table(1);
	}
	
	function _bottom_controls()	{

	}

}

?>
