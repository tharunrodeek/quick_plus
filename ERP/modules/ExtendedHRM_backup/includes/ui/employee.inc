<?php
// ----------------------------------------------------------------
// Creator: Kvvaradha
// email:   admin@kvcodes.com
// Title:   HRM Extention for Frontaccounting
// website: http://www.kvcodes.com
// ----------------------------------------------------------------
function employee_list($name, $empl_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false, $show_all=true, $search=true, $inactive = false,$disabled= false){
	global $all_items;

	$sql = "SELECT empl.id, CONCAT(empl.empl_id, ' - ', empl.empl_firstname, ' ', empl.empl_lastname) AS empl_name, dept.description FROM ".TB_PREF."kv_empl_info empl,".TB_PREF."kv_empl_job job, ".TB_PREF."kv_empl_departments dept WHERE empl.id=job.empl_id AND job.department=dept.id ";

	if(get_post('dept_id'))
		$sql .=" AND job.department=".get_post('dept_id');
	/*if($show_all)
		$sql .= " AND empl.status=1";
	elseif($inactive)
		$sql .= " AND empl.status != 1";*/
	if(get_post('show_inactive'))
		$sql .=" AND empl.status <> 1 ";
	else{
		//$sql .= " AND empl.status == 1 " ;
		if($show_all == 2 && $show_all != 1)
			$sql .= " AND empl.status != 1 ";
		elseif($show_all == true)
			$sql .= " AND empl.status=1";
	}
	if(get_post('dept_id'))
		$sql .=" AND job.department=".get_post('dept_id');
	if ($editkey)
		set_editor('employee', $name, $editkey);
	$mode = 0;
	$ret = combo_input($name, $empl_id, $sql, 'empl.empl_id', 'empl_name',
	array_merge(
	  array(
		'format' => '_format_empl',
			'spec_option' => $all_option===true ?  trans("All Employees") : $all_option,
			'spec_id' => ALL_TEXT,
			'search_box' => $search,
			'search' => array("empl.empl_id", "dept.description","empl_name"),
			'search_submit' => get_company_pref('no_item_list')!=0,
			'size'=>20,
			'select_submit'=> $submit_on_change,
			'category' => 2,
			'disabled' => $disabled,
			'order' => array( 'dept.description','empl_name'),
			'editable' => false,
			'max' => 255
		  )) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}

function _format_empl($row){
	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1];
}

function employee_list_cells($label, $name, $empl_id=null, $all_option=false, $submit_on_change=false, $all=false, $editkey = false, $show_all= true, $search=false, $inactive=false,$disabled= false ){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo employee_list($name, $empl_id, $all_option, $submit_on_change,	array('cells'=>true, 'show_inactive'=>$all), $editkey, $show_all,$search, $inactive,$disabled);
	echo "</td>\n";
}

function employee_list_row($label, $name, $empl_id=null, $all_option=false, $submit_on_change=false, $all=false, $editkey = false, $show_all= true,$search=false, $inactive=false,$disabled= false){
	echo "<tr><td class='label'>$label</td>";
	employee_list_cells(null, $name, $empl_id, $all_option, $submit_on_change, $all, $editkey, $show_all,$search, $inactive,$disabled);
	echo "</tr>\n";
}

function empl_img_name($empl_id){
	$empl_id = strtr($empl_id, "><\\/:|*?", '________');
	return clean_file_name($empl_id);
}


//-----------------------------------------------------------------------------------
function kv_image_cells($label, $name, $id=""){
	if ($id != "")
		$id = "id='$id'";
	label_cells($label, "<input type='file' name='$name' $id  'image/*'/>");
}		
function kv_image_row($label, $name, $id = ""){
	echo "<tr><td class='label'>$label</td>";
	kv_image_cells(null, $name, $id);
	echo "</tr>\n";
}	


//-----------------------------------------------------------------------------------
function kv_doc_cells($label, $name, $id=""){
	if ($id != "")
		$id = "id='$id'";
	label_cells($label, "<input type='file' name='$name' $id  accept='.doc,.docx, .rtf, .txt, .jpg, .png, .jpeg, .gif, .bmp, .pdf'/>");
}		
function kv_doc_row($label, $name, $id = ""){
	echo "<tr><td class='label'>$label</td>";
	kv_doc_cells(null, $name, $id);
	echo "</tr>\n";
}
?>
