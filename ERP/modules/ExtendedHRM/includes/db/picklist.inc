<?php 
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
/*  Pick List for Employment Type, Location work, Mode of Pay
*****************************************/

function add_pick_list($type, $pick_name, $inactive){
	return Insert('kv_empl_picklist', array('type' => $type, 'description' => $pick_name, 'inactive' => $inactive));
}

function update_pick_list($id, $type, $pick_name, $inactive){
	//display_error($id.'--'. $type.'--'.$pick_name.'--'. $inactive);
	return Update('kv_empl_picklist', array('id' =>  $id), array('type' => $type, 'description' => $pick_name, 'inactive' =>$inactive));
}

function get_pick_name($id){
	return GetSingleValue('kv_empl_picklist', 'description', array('id' => $id));
}
function get_pick($id){
	return GetRow('kv_empl_picklist', array('id' => $id));
}

function get_all_picks($type=0){
	if($type > 0)
		return GetAll('kv_empl_picklist', array('type' => $type));
	else
		return GetAll('kv_empl_picklist');
}
function delete_pick($id){
	return Delete('kv_empl_picklist', array('id' => $id));
}

function db_has_this_pick($dept_name_or_id, $type){
	
	$sql = "SELECT id FROM ".TB_PREF."kv_empl_picklist WHERE type=".db_escape($type); 
	if(is_numeric($dept_name_or_id)){
		$sql .= " AND id=".db_escape($dept_name_or_id);
	}else{
		$sql .= " AND description=".db_escape($dept_name_or_id);
	}
	$result = db_query($sql, "Can't Select department table");
	if(!$result) {		
		return false; 
	} else{
		$final = db_fetch($result);
		return $final['id'];
	} 
}
function Insert_pick_of_Get_existing_id($input, $type){
	$result = db_has_this_pick($input, $type);
	if($result)
		return $result;
	else{
		Insert('kv_empl_picklist', array('description' => $input, 'type' => $type));
		return db_insert_id();
	}
}

//------------------------------------------------------------------------------------
function hrm_pick_list_type($name, $selected_id=null, $spec_option=false, $type=1, $submit_on_change=false, $show_inactive=false, $editkey = false){
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."kv_empl_pick_type";

	//if($type)
	//	$sql .= " WHERE type=".$type;
	$mode = 0;

	if ($editkey)
		set_editor('department', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
	    'format' => null,
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? trans("None") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? trans("Press Space tab to filter by name fragment; F2 - entry new department") :	trans("Select department"),
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('department');
	return $ret;
}

function hrm_pick_list_type_row($label, $name, $selected_id=null, $pick_type=1, $all_option = false, $submit_on_change=false, $show_inactive=false, $editkey = false){
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo hrm_pick_list_type($name, $selected_id, $all_option, $pick_type, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n</tr>\n";
}

?>
