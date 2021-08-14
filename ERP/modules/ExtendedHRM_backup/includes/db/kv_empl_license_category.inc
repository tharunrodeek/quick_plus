<?php 


function  db_has_license_category_exist(){
	$result = db_query("SELECT COUNT(*) FROM ".TB_PREF."kv_empl_license_category", "Can't Select license_category table");
	if(!$result) {		
		return  false; 
	} else 
	return true ; 	
}

function kv_create_license_category_db() {
	
	$sql= "CREATE TABLE IF NOT EXISTS `".TB_PREF."kv_empl_license_category` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(60) NOT NULL DEFAULT '',
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`) 
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;" ; 

	return db_query($sql, "Db Table creation failed, Kv Manufacturer table");
} 
function add_license_category($description){
	$sql = "INSERT INTO ".TB_PREF."kv_empl_license_category (description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The sales license_category could not be added");
}

function update_license_category($selected_id, $description){
	$sql = "UPDATE ".TB_PREF."kv_empl_license_category SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The sales license_category could not be updated");
}

function delete_license_category($id){
	return Delete('kv_empl_license_category', array('id' => $id));
}

function get_license_categories($show_inactive){
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_license_category";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get kv_empl_license_category");
}

function get_license_category($selected_id){
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_license_category WHERE id=".db_escape($selected_id);
	$result = db_query($sql,"could not get license_category");
	return db_fetch($result);
}

function get_license_category_name($group_no){
	$sql = "SELECT description FROM ".TB_PREF."kv_empl_license_category WHERE id = ".db_escape($group_no);
	$result = db_query($sql, "could not get license_category");
	$row = db_fetch($result);
	return $row[0];
}


//------------------------------------------------------------------------------------
function license_category_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false, $all_dept=false){
	global $all_items;
	$sql = "SELECT id, description FROM ".TB_PREF."kv_empl_license_category";
	
	$mode = 0;

	if ($editkey)
		set_editor('license_category', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
	    'format' => null,
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? trans("All license category") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? trans('Press Space tab to filter by name fragment; F2 - entry new license_category') :	trans('Select license_category'),
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('license_category');
	return $ret;
}

function license_category_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false, $all_dept=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo license_category_list($name, $selected_id, $all_option, $submit_on_change, $show_inactive, $editkey, $all_dept);
	echo "</td>\n";
}

function license_category_list_row($label, $name, $selected_id=null, $all_option = false, $submit_on_change=false, $show_inactive=false, $editkey = false, $all_dept=false){
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo license_category_list($name, $selected_id, $all_option, $submit_on_change, $show_inactive, $editkey, $all_dept);
	echo "</td>\n</tr>\n";
}


?>