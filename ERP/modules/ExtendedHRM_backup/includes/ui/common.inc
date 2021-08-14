<?php
// ----------------------------------------------------------------
// Creator: Kvvaradha
// email:   admin@kvcodes.com
// Title:   HRM Extention for Frontaccounting
// website: http://www.kvcodes.com
// ----------------------------------------------------------------
global $kv_empl_gender, $hrm_empl_status, $hrm_empl_salutation, $kv_empl_mop,$allowances_type_list, $hrm_empl_leave_types, $hrm_empl_relation; 

$hrm_empl_status = array(
    1 => trans("Active"),
    8 => trans("Inactive"),
    2 => trans("Resigned"),
    3 => trans("Absconded"),
    4 => trans("Terminated"),
    5 => trans("Suspened"),
    6 => trans("Deceased"),
    7 => trans("Retired"),
);

$hrm_empl_leave_types =  array(		
	'AL' => trans("Annual Leave"),
    'ML' => trans("Maternity Leave"),
    'SL' => trans("Sick Leave"),
    'HL' => trans("Hajj Leave"),
);

$hrm_empl_salutation = array(
    1 => trans("Mr."),
    2 => trans("Ms."),
    3 => trans("Mrs."),
    4 => trans("Dr."),
    5 => trans("Prof."),
    6 => trans("Other.")
);

$allowances_type_list = array(
	0 => " ",
    1 => trans("LMRA Fees"),
    2 => trans("Social Security"),
    3 => trans("Medical Allowance "),
    4 => trans("Visa and Immigartion Exp"),
    5 => trans("Leave Travel "),
    6 => trans("Leave Pay"),
    7 => trans("Indemnity "),
    8 => trans("Personal Accident Insurance")
);

$kv_empl_gender = array(
    1 => trans("Male"),
    2 => trans("Female"),
    3 => trans("Transgender")
);

$kv_empl_mop = array(
    1 => trans("Bank"),
    2 => trans("Cash"),
    3 => trans("Cheque")
);
$kv_cperiod = array(
    1 => trans("Month"),
    2 => trans("Year")
);

$hrm_empl_type = array(     
    1 => trans("Permanant"),
	2 => trans("Temporary"),
    3 => trans("Contract")

);

$hrm_empl_marital = array(
    1 => trans("Single"),
    2 => trans("Married"),
    3 => trans("Widow"),
    4 => trans("UnMarried"),
    5 => trans("Other")
);

$hrm_empl_grade_list = array(
    1 => trans("A - Individual Contributor"),
    3 => trans("B - Professional"),
	4 => trans("C - Manager"),
    5 => trans("D - Director"),
    6 => trans("E - Vice President")
);


$hrm_empl_bloog_group = array(
    1 => trans("A+"),
    2 => trans("O+"),
    3 => trans("B+"),
    4 => trans("AB+"),
    5 => trans("A-"),
    6 => trans("O-"),
    7 => trans("B-"),
    8 => trans("AB-")
);
$hrm_empl_relation = array(
	1 => trans("Wife"),
	6 => trans("Husband"),
	2 => trans("Parents"),
	3 => trans("Son"),
	4 => trans("Daughter"),
	5 => trans("Other")
	

);
//-------------------------------------------------------------------------------
function hrm_empl_medical_premium($name, $selected_id=null){
	$sql = "SELECT id, description FROM ".TB_PREF."kv_empl_medical_premium";
	return combo_input($name, $selected_id, $sql, 'id', 'description', array());
} 

function hrm_empl_medical_premium_cells($label, $name, $selected_id=null){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo hrm_empl_medical_premium($name, $selected_id);
	echo "</td>\n";
}

function hrm_empl_medical_premium_row($label, $name, $selected_id=null){
	echo "<tr><td class='label'>$label</td>";
	hrm_empl_medical_premium_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//-------------------------------------------------------------------------------
function hrm_empl_nationality($name, $selected_id=null, $all_option=false){
	$sql = "SELECT id, description FROM ".TB_PREF."kv_empl_nationalities";
	return combo_input($name, $selected_id, $sql, 'id', 'description', array('spec_option' => $all_option===true ?  trans("All Nationalities") : $all_option));
}

function hrm_empl_nationality_cells($label, $name, $selected_id=null, $all_option=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo hrm_empl_nationality($name, $selected_id, $all_option);
	echo "</td>\n";
}

function hrm_empl_nationality_row($label, $name, $selected_id=null, $all_option=false){
	echo "<tr><td class='label'>$label</td>";
	hrm_empl_nationality_cells(null, $name, $selected_id, $all_option);
	echo "</tr>\n";
}
//------------------------------------------------------------------------------------
function department_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false, $all_dept=false,$disabled= false){
	global $all_items;
	if($all_dept)
		$sql = "SELECT id, description FROM ".TB_PREF."kv_empl_departments";
	else
		$sql = "SELECT DISTINCT dept.id, dept.description FROM ".TB_PREF."kv_empl_info empl,".TB_PREF."kv_empl_job job, ".TB_PREF."kv_empl_departments dept WHERE empl.id=job.empl_id AND job.department=dept.id AND empl.status=1";
    //echo $sql;
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
		'spec_option' => $spec_option === true ? trans("All Departments") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'disabled' => $disabled,
		'sel_hint' => $mode ? trans("Press Space tab to filter by name fragment; F2 - entry new department") :	trans("Select department"),
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('department');
	return $ret;
}

function department_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false, $all_dept=false,$disabled= false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo department_list($name, $selected_id, $all_option, $submit_on_change, $show_inactive, $editkey, $all_dept,$disabled);
	echo "</td>\n";
}

function department_list_row($label, $name, $selected_id=null, $all_option = false, $submit_on_change=false, $show_inactive=false, $editkey = false, $all_dept=false,$disabled= false){
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo department_list($name, $selected_id, $all_option, $submit_on_change, $show_inactive, $editkey, $all_dept,$disabled);
	echo "</td>\n</tr>\n";
}


function  hrm_empl_blood_list($label, $name, $selected_id=null, $submit_on_change=false,$disabled= false) {
	global $hrm_empl_bloog_group;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
	    'disabled' => $disabled
	);
	echo kv_array_selector($name, $selected_id, $hrm_empl_bloog_group, $options,$disabled);
	echo "</td></tr>\n";
}

function  hrm_empl_performance_list($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_empl_performance_list;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $hrm_empl_performance_list, $options);
	echo "</td></tr>\n";
}

function  hrm_empl_type_row($label, $name, $selected_id=null, $submit_on_change=false,$disabled= false) {
	global $hrm_empl_type;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
	    'disabled' => $disabled
	);
	echo kv_array_selector($name, $selected_id, $hrm_empl_type, $options,$disabled);
	echo "</td></tr>\n";
}

function  hrm_empl_contract_type_row($label, $name, $selected_id=null, $submit_on_change=false,$disabled= false) {
	global $hrm_empl_type;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
	    'disabled' => $disabled
	);
	echo kv_array_selector($name, $selected_id, $hrm_empl_type, $options,$disabled);
	echo "</td></tr>\n";
}

function hrm_empl_marital_list_row($label, $name, $selected_id=null, $submit_on_change=false,$disabled= false) {
	global $hrm_empl_marital;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
	    'disabled' => $disabled
	);
	echo kv_array_selector($name, $selected_id, $hrm_empl_marital, $options,$disabled);
	echo "</td></tr>\n";
}

function kv_empl_gender_list_row($label, $name, $selected_id=null, $submit_on_change=false,$disabled= false) {
	global $kv_empl_gender;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
	    'disabled' => $disabled
	);
	echo kv_array_selector($name, $selected_id, $kv_empl_gender, $options);
	echo "</td></tr>\n";
}

function kv_empl_leave_approve_permission($label, $name, $selected_id=null, $submit_on_change=false,$disabled= false) {
    global $kv_empl_gender;

   /* echo "<tr><td class='label'>$label</td><td>";
     $options = array(
        //'select_submit'=> $submit_on_change,
        'disabled' => $disabled
    );
    echo kv_leave_permission_settings($name, $selected_id, $kv_empl_gender, $options);
    echo "</td></tr>\n";*/

	echo "<tr><td class='label'>$label</td>";
	kv_leave_permission_settings(null, $name, $selected_id, $submit_on_change, $disabled);
	echo "</tr>\n";
}

function kv_leave_permission_settings($label, $name, $selected_id=null,  $submit_on_change=false, $disabled=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo leave_approve_options_list($name, $selected_id, $submit_on_change, $disabled);
	echo "</td>\n";
}

function leave_approve_options_list($name, $selected_id=null,  $submit_on_change=false, $disabled=false){
	$items = array('0' => trans("No"), '1' => trans("Yes"));

	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false,
			'disabled' => $disabled ) ); // FIX?
}



function kv_cperiod_row($label, $name, $selected_id=null, $submit_on_change=false,$disabled= false) {
	global $kv_cperiod;

	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
	    'disabled' => $disabled
	);
	echo kv_array_selector($name, $selected_id, $kv_cperiod, $options);
	echo "</td></tr>\n";
}

function  kv_empl_salutation_list_row($label, $name, $selected_id=null, $submit_on_change=false,$disabled= false) {
	global $hrm_empl_salutation;

	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
	    'disabled' => $disabled
	);
	echo kv_array_selector($name, $selected_id, $hrm_empl_salutation, $options);
	echo "</td></tr>\n";
}


function  hrm_empl_status_list($label, $name, $selected_id=null, $submit_on_change=false,$disabled= false) {
	global $hrm_empl_status;

	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
	    'disabled' => $disabled
	);
	echo kv_array_selector($name, $selected_id, $hrm_empl_status, $options);
	echo "</td></tr>\n";
}

function  hrm_empl_mop_list($label, $name, $selected_id=null, $submit_on_change=false,$disabled= false) {
	global $kv_empl_mop;

	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
	    'disabled' => $disabled
	);
	echo kv_array_selector($name, $selected_id, $kv_empl_mop, $options);
	echo "</td></tr>\n";
}

//-------------------------------------------------------------------------------

function country_list($name, $selected_id=null){

	$sql = "SELECT id, local_name FROM ".TB_PREF."kv_empl_country";
	return combo_input($name, $selected_id, $sql, 'id', 'local_name', array());
}

function country_list_cells($label, $name, $selected_id=null){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo country_list($name, $selected_id);
	echo "</td>\n";
}

function country_list_row($label, $name, $selected_id=null){
	echo "<tr><td class='label'>$label</td>";
	country_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

function  kv_allowances_type_list_row($label, $name, $selected_id=null, $submit_on_change=false) {
	global $allowances_type_list;

	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $allowances_type_list, $options);
	echo "</td></tr>\n";
}

//-------------------------------------------------------------------------------
function doc_type_list($name, $selected_id=null, $spec_option= false){

	$sql = "SELECT id, description FROM ".TB_PREF."kv_empl_doc_type";
	return combo_input($name, $selected_id, $sql, 'id', 'description', array( 'spec_option' => $spec_option === true ? trans("All Allowances") : $spec_option,));
}

function doc_type_list_cells($label, $name, $selected_id=null, $spec_option= false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo doc_type_list($name, $selected_id, $spec_option);
	echo "</td>\n";
}

function doc_type_list_row($label, $name, $selected_id=null, $spec_option= false){
	echo "<tr><td class='label'>$label</td>";
	doc_type_list_cells(null, $name, $selected_id, $spec_option);
	echo "</tr>\n";
}

//-------------------------------------------------------------------------------
function empl_allowances_list($name, $selected_id=null, $spec_option = false, $submit_on_change =false){
	$sql = "SELECT id, description FROM ".TB_PREF."kv_empl_allowances";
	return combo_input($name, $selected_id, $sql, 'id', 'description', array('select_submit'=> $submit_on_change, 'spec_option' => $spec_option === true ? trans("All Allowances") : $spec_option,));
}

function empl_allowances_list_cells($label, $name, $selected_id=null, $spec_option =false, $submit_on_change =false, $textbox=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo empl_allowances_list($name, $selected_id, $spec_option, $submit_on_change).($textbox? ' or '.text_input($name.'_', null ) : '');
	echo "</td>\n";
}

function empl_allowances_list_row($label, $name, $selected_id=null, $spec_option = false, $submit_on_change= false, $textbox=false){
	echo "<tr><td class='label'>$label</td>";
	empl_allowances_list_cells(null, $name, $selected_id, $spec_option, $submit_on_change, $textbox);
	echo "</tr>\n";
}

//-------------------------------------------------------------------------------
function designation_list($name, $selected_id=null){

	$sql = "SELECT id, description FROM ".TB_PREF."kv_empl_designation";
	return combo_input($name, $selected_id, $sql, 'id', 'description', array());
}

function designation_list_cells($label, $name, $selected_id=null){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo designation_list($name, $selected_id);
	echo "</td>\n";
}



function designation_list_row($label, $name, $selected_id=null){
	echo "<tr><td class='label'>$label</td>";
	designation_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

function users_list_cells_display($label, $name, $selected_id=null){
	echo "<tr><td class='label'>$label</td>";
	users_list_cells_rows(null, $name, $selected_id);
	echo "</tr>\n";
}

function get_leave_types($label, $name, $selected_id=null){
	echo "<tr><td class='label'>$label</td>";
	added_leaves_row(null, $name, $selected_id);
	echo "</tr>\n";
}

function added_leaves_row($label, $name, $selected_id=null, $submit_on_change=false, $spec_opt=true)
{
	$where = false;
	$sql = " SELECT char_code,description FROM 0_kv_empl_leave_types";

	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";

	echo combo_input($name, $selected_id, $sql,'user_id','description',
		array(
			'spec_option' => $spec_opt===true ?trans("Select Leave Type") : $spec_opt,
			'spec_id' => '',
			'order' => 'description',
			'select_submit'=> $submit_on_change,
			'async' => false
		) );
	echo "</td>";

}

function users_list_cells_rows($label, $name, $selected_id=null, $submit_on_change=false, $spec_opt=true)
{
	$where = false;
	$sql = " SELECT id,concat(empl_firstname,'',empl_lastname,' - ',empl_id) as real_name FROM 0_kv_empl_info";

	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";

	echo combo_input($name, $selected_id, $sql, 'user_id', 'real_name',
		array(
			'spec_option' => $spec_opt===true ?trans("Select Employee") : $spec_opt,
			'spec_id' => '',
			'order' => 'real_name',
			'select_submit'=> $submit_on_change,
			'async' => false
		     ) );
	echo "</td>";

}

//-------------------------------------------------------------------------------

function kv_empl_desig_group($name, $selected_id=null){
	$sql = "SELECT id, description FROM ".TB_PREF."kv_empl_designation_group";
	return combo_input($name, $selected_id, $sql, 'id', 'description', array());
}

function kv_empl_desig_group_cells($label, $name, $selected_id=null){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo kv_empl_desig_group($name, $selected_id);
	echo "</td>\n";
}

function hrm_empl_desig_group($label, $name, $selected_id=null){
	echo "<tr><td class='label'>$label</td>";
	kv_empl_desig_group_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

function kv_empl_grade_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false, $all_dept=false){
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."kv_empl_grade";

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
		'spec_option' => $spec_option === true ? trans("All Grades") : $spec_option,
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

function kv_empl_grade_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false, $all_dept=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo kv_empl_grade_list($name, $selected_id, $all_option, $submit_on_change, $show_inactive, $editkey, $all_dept);
	echo "</td>\n";
}

function kv_empl_grade_list_row($label, $name, $selected_id=null, $all_option = false, $submit_on_change=false, $show_inactive=false, $editkey = false, $all_dept=false){
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo kv_empl_grade_list($name, $selected_id, $all_option, $submit_on_change, $show_inactive, $editkey, $all_dept);
	echo "</td>\n</tr>\n";
}

//-------------------------------------------------------------------------------

function  hrm_empl_grade_list($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false) {
	global $hrm_empl_grade_list;

	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
		'disabled' => $disabled
	);
	echo array_selector($name, $selected_id, $hrm_empl_grade_list, $options);
	echo "</td></tr>\n";
}


//-----------------------------------------------------------------------------
$hrm_empl_workings_days = array(
    'Sun' => trans("Sunday"),
    'Mon' => trans("Monday"),
    'Tue' => trans("Tuesday"),
    'Wed' => trans("Wednesday"),
    'Thu' => trans("Thursday"),
    'Fri' => trans("Friday"),
    'Sat' => trans("Saturday")
);

function  hrm_empl_workings_days($label, $name, $selected_id=null, $submit_on_change=false, $multi = false) {
	global $hrm_empl_workings_days;

	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change,
	    'multi' => $multi
	);
	echo array_selector($name, $selected_id, $hrm_empl_workings_days, $options);
	echo "</td></tr>\n";
}

//------------------------------------------------------------
$hrm_empl_ref_type = array(
    0 => trans("Manual"),
    1 => trans("Automatic")
);

function  hrm_empl_ref_type($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_empl_ref_type;

	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $hrm_empl_ref_type, $options);
	echo "</td></tr>\n";
}

function kv_text_rows($label, $name, $size, $max=null, $submit_on_change=false, $title=null, $init=null, $labparams=null, $post_label=null){
  	global $Ajax;

	default_focus($name);
	if (!isset($_POST[$name]) || $_POST[$name] == "")	{
		if ($init)
			$_POST[$name] = $init;
		else
			$_POST[$name] = "";
	}
	if ($label != null){
		echo '<tr>';
		label_cell($label, $labparams);
	}

	if (!isset($max))
		$max = $size;

	echo "<td>";
	$class = $submit_on_change ? 'class="searchbox"' : '';
	echo "<input $class type=\"text\" name=\"$name\" size=\"$size\" maxlength=\"$max\" value=\"" . $_POST[$name]. "\""
	 .($title ? " title='$title'": '')." >";

	if ($post_label)
		echo " " . $post_label;

	echo "</td>\n";
	if ($label != null)
	echo '</tr>';
	$Ajax->activate('_page_body');
}

//-----------------------------------------------------------------------------------
function kv_radio($label, $name, $value, $selected=null, $submit_on_change=false, $disabled=''){
	if (!isset($selected))
		$selected = get_post($name) == $value;

	if ($submit_on_change === true)
		$submit_on_change =
			"JsHttpRequest.request(\"_{$name}_update\", this.form);";

	return "<input type='radio' {$disabled} name=$name value='$value' ".($selected ? "checked":'')
	    .($submit_on_change ? " onclick='$submit_on_change'" : '')
		.">".($label ? $label : '');
}

function kv_radio_row($label, $name, $value, $selected=null, $submit_on_change=false, $disabled=''){

	echo '<tr> <td>'.kv_radio($label, $name, $value, $selected, $submit_on_change, $disabled).'</td></tr>';
}

function kv_basic_row($label, $name, $size, $max=null, $init=null, $submit_on_change=false){
  	global $Ajax;

  	echo "<tr>";
	default_focus($name);
	if (!isset($_POST[$name]) || $_POST[$name] == ""){
		if ($init)
			$_POST[$name] = $init;
		else
			$_POST[$name] = "";
	}
	if ($label != null)
		label_cell($label, null);

	if (!isset($max))
		$max = $size;

	echo "<td>";
	$class = $submit_on_change ? 'class="searchbox"' : '';

		echo "<input $class type=\"text\"  name=\"$name\" size=\"$size\" maxlength=\"$max\" value=\"" . $_POST[$name]. "\""
	 .($label ? " title='$label'": '')."  > &nbsp;&nbsp;";
	submit('RefreshInquiry', trans("Calculate"),trans("Show Results"),trans("Update Calculation & Gross Pay"), true);
	echo "</td>\n";
	$Ajax->addUpdate($name, $name, $_POST[$name]);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------------------

function kv_text_row_ex($label, $name, $size, $max=null, $title=null, $value=null, $params=null, $post_label=null, $submit_on_change=false,$disabled=false){
	echo "<tr><td class='label'>$label</td>";
	kv_text_cells_ex(null, $name, $size, $max, $value, $title, $params, $post_label, false, $disabled);
	echo "</tr>\n";
}

function kv_text_cells_ex($label, $name, $size, $max=null, $init=null, $title=null,
	$labparams=null, $post_label=null, $submit_on_change=false, $disabled= false){
  	global $Ajax;

	default_focus($name);
	if (!isset($_POST[$name]) || $_POST[$name] == "")
	{
		if ($init)
			$_POST[$name] = $init;
		else
			$_POST[$name] = "";
	}
	if ($label != null)
		label_cell($label, $labparams);

	if (!isset($max))
		$max = $size;

	echo "<td>";
	$class = $submit_on_change ? 'class="searchbox"' : '';
	if($disabled){
		echo "<input disabled $class type=\"text\"  name=\"$name\" size=\"$size\" maxlength=\"$max\" value=\"" . $_POST[$name]. "\""
	 .($title ? " title='$title'": '')."  >";
	}else{
		echo "<input $class type=\"text\"  name=\"$name\" size=\"$size\" maxlength=\"$max\" value=\"" . $_POST[$name]. "\""
	 .($title ? " title='$title'": '')."  >";
	}

	if ($post_label)
		echo " " . $post_label;

	echo "</td>\n";
	$Ajax->addUpdate($name, $name, $_POST[$name]);
}

function kv_checkbox($label, $name, $value=null, $submit_on_change=false, $title=false, $disabled =false)
{
  	global $Ajax;

	$str = '';

	if ($label)
		$str .= $label . "  ";
	if ($submit_on_change !== false) {
		if ($submit_on_change === true)
			$submit_on_change =
				"JsHttpRequest.request(\"_{$name}_update\", this.form);";
	}
	if ($value === null)
		$value = get_post($name,0);

	$str .= "<input"
	    .($value == 1 ? ' checked':'')
	    ." type='checkbox' name='$name' value='1'"
	    .($submit_on_change ? " onclick='$submit_on_change'" : '')
	    .($title ? " title='$title'" : '')
	    .($disabled ? "disabled" : '')
	    ." >\n";

	$Ajax->addUpdate($name, $name, $value);
	return $str;
}

function kv_check($label, $name, $value=null, $submit_on_change=false, $title=false, $disabled=false)
{
	echo kv_checkbox($label, $name, $value, $submit_on_change, $title, $disabled);
}

function kv_check_cells($label, $name, $value=null, $submit_on_change=false, $title=false,
	$params='', $disabled =false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td $params>";
	echo kv_check(null, $name, $value, $submit_on_change, $title, $disabled);
	echo "</td>";
}

function kv_check_row($label, $name, $value=null, $submit_on_change=false, $title=false, $disabled=false)
{
	echo "<tr><td class='label'>$label</td>";
	echo kv_check_cells(NULL, $name, $value, $submit_on_change, $title,'', $disabled);
	echo "</tr>\n";
}

function kv_button($name, $value, $title=false, $icon=false,  $aspect='', $disabled=false)
{
	// php silently changes dots,spaces,'[' and characters 128-159
	// to underscore in POST names, to maintain compatibility with register_globals
	$rel = '';
	if ($aspect == 'selector') {
		$rel = " rel='$value'";
		$value = trans("Select");
	}
	if($disabled != false)
		$disable = 'disabled';
	else
		$disable = '';
	if (user_graphic_links() && $icon)
	{
		if ($value == trans("Delete")) // Helper during implementation
			$icon = ICON_DELETE;
		return "<button type='submit' $disable class='editbutton' name='"
			.htmlentities(strtr($name, array('.'=>'=2E', '='=>'=3D',// ' '=>'=20','['=>'=5B'
			)))
			."' value='1'" . ($title ? " title='$title'":" title='$value'")
			. ($aspect ? " aspect='$aspect'" : '')
			. $rel
			." >".set_icon($icon)."</button>\n";
	}
	else
		return "<input type='submit' $disable class='editbutton' name='"
			.htmlentities(strtr($name, array('.'=>'=2E', '='=>'=3D',// ' '=>'=20','['=>'=5B'
			)))
			."' value='$value'"
			.($title ? " title='$title'":'')
			. ($aspect ? " aspect='$aspect'" : '')
			. $rel
			." >\n";
}

function kv_button_cell($name, $value, $title=false, $icon=false, $aspect='', $disabled=false)
{
	echo "<td align='center'>";
	echo kv_button($name, $value, $title, $icon, $aspect, $disabled);
	echo "</td>";
}

function kv_delete_button_cell($name, $value, $title=false, $disabled=false)
{
	kv_button_cell($name, $value, $title, ICON_DELETE,'', $disabled);
}

function  hrm_empl_leave_type_row($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_empl_leave_types;

	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $hrm_empl_leave_types, $options);
	echo "</td></tr>\n";
}

//--------------------------------------------------------------------------------
/*   Accept Reject Statuses Drop-down   */

function accept_reject_pending_list($name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    $items = array('0' => trans("Pending"), '1' => trans("Accepted"), '2' => trans("Rejected")  /*, 'Formula' =>  trans("Formula") */ );

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false,
            'disabled' => $disabled ) ); // FIX?
}
function filter_leave_types_list($name, $selected_id=null,  $submit_on_change=false, $disabled=false){
	$items = array('0' => trans("My Leaves"), '1' => trans("Reported To Me")  , '2' =>  trans("Forward From Others") );

	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false,
			'disabled' => $disabled ) ); // FIX?
}

function accept_reject_pending_list_cells($label, $name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo accept_reject_pending_list($name, $selected_id, $submit_on_change, $disabled);
    echo "</td>\n";
}

function accept_reject_pending_list_row($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false){
    echo "<tr><td class='label'>$label</td>";
    accept_reject_pending_list_cells(null, $name, $selected_id, $submit_on_change, $disabled);
    echo "</tr>\n";
}

function filter_leave_types_list_cells($label, $name, $selected_id=null,  $submit_on_change=false, $disabled=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo filter_leave_types_list($name, $selected_id, $submit_on_change, $disabled);
	echo "</td>\n";
}

function filter_leave_types($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false){
	echo "<tr><td class='label'>$label</td>";
	filter_leave_types_list_cells(null, $name, $selected_id, $submit_on_change, $disabled);
	echo "</tr>\n";
}

//--------------------------------------------------------------------------------
/*   Travel Mode Drop-down   */ 

function travel_mode_list($name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    $items = array('Bus' => trans("Bus"), 'Car' => trans("Car"), 'Bike' => trans("Bike"), 'Custom' =>  trans("Custom") );

    return array_selector($name, $selected_id, $items, 
        array( 
            'select_submit'=> $submit_on_change,
            'async' => false,
            'disabled' => $disabled ) ); // FIX?
}

function travel_mode_list_cells($label, $name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo travel_mode_list($name, $selected_id, $submit_on_change, $disabled);
    echo "</td>\n";
}

function travel_mode_list_row($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false){
    echo "<tr><td class='label'>$label</td>";
    travel_mode_list_cells(null, $name, $selected_id, $submit_on_change, $disabled);
    echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------

function empl_workcenter_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{

	$sql = "SELECT id, name, inactive FROM ".TB_PREF."workcentres";

	return combo_input($name, $selected_id, $sql, 'id', 'name',
		array(
			'spec_option' =>$all_option===true ? trans("All Work Centres") : $all_option,
			'select_submit'=> $submit_on_change,
			'spec_id' => ALL_TEXT,
		) );
}

function empl_workcenter_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	//default_focus($name);
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo empl_workcenter_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n";
}

//-------------------------------------------------------------------------------
function hrm_empl_allowances_list($name, $selected_id=null){
	$sql = "SELECT id, description FROM ".TB_PREF."kv_empl_allowances WHERE loan=1";
	return combo_input($name, $selected_id, $sql, 'id', 'description', array());
}

function hrm_empl_allowances_list_cells($label, $name, $selected_id=null){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo hrm_empl_allowances_list($name, $selected_id);
	echo "</td>\n";
}

function hrm_empl_allowances_list_row($label, $name, $selected_id=null){
	echo "<tr><td class='label'>$label</td>";
	hrm_empl_allowances_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//--------------------------------------------------------------------------------
/*   Payout Duration Drop-down   */ 

function payout_duration_list($name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    $items = array('1' => trans("Monthly"), '2' => trans("Semi-Monthly"), '3' => trans("Bi-weekly"), '4' => trans("Weekly"));

    return array_selector($name, $selected_id, $items, 
        array( 
            'select_submit'=> $submit_on_change,
            'async' => false,
            'disabled' => $disabled ) ); // FIX?
}

function payout_duration_list_cells($label, $name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo payout_duration_list($name, $selected_id, $submit_on_change, $disabled);
    echo "</td>\n";
}

function payout_duration_list_row($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false){
    echo "<tr><td class='label'>$label</td>";
    payout_duration_list_cells(null, $name, $selected_id, $submit_on_change, $disabled);
    echo "</tr>\n";
}


//-----------------------------------------------------------------------------------------------

function empl_picklist($name, $selected_id=null, $all_option=false, $submit_on_change=false, $type=1){

	$sql = "SELECT id, description, inactive FROM ".TB_PREF."kv_empl_picklist WHERE type=".db_escape($type);

	return combo_input($name, $selected_id, $sql, 'id', 'description',
		array(
			'spec_option' =>$all_option===true ? trans("No Type Selected") : $all_option,
			'select_submit'=> $submit_on_change,
			'spec_id' => ALL_TEXT,
		) );
}

function empl_picklist_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $type=1){
	//default_focus($name);
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo empl_picklist($name, $selected_id, $all_option, $submit_on_change, $type);
	echo "</td>\n";
}

function empl_picklist_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $type=1){
    echo "<tr><td class='label'>$label</td>";
    empl_picklist_cells(null, $name, $selected_id, $all_option, $submit_on_change, $type);
    echo "</tr>\n";
}

function kv_array_selector($name, $selected_id, $items, $options=null)
{
	global $Ajax;

$opts = array(		// default options
	'spec_option'=>false, 	// option text or false
	'spec_id' => 0,		// option id
	'select_submit' => false, //submit on select: true/false
	'async' => true,	// select update via ajax (true) vs _page_body reload
	'default' => '', // default value when $_POST is not set
	'multi'=>false,	// multiple select
		// search box parameters
	'height' => false,	// number of lines in select box
	'sel_hint' => null,
	'disabled' => ''
);
// ------ merge options with defaults ----------
	if($options != null)
		$opts = array_merge($opts, $options);
	$select_submit =  $opts['select_submit'];
	$spec_id = $opts['spec_id'];
	$spec_option = $opts['spec_option'];
	$disabled = $opts['disabled'] ? "disabled" : '';
	$multi = $opts['multi'];

	if ($selected_id == null) {
		$selected_id = get_post($name, $opts['default']);
	}
	if(!is_array($selected_id))
		$selected_id = array((string)$selected_id); // code is generalized for multiple selection support

	if (isset($_POST[ '_'.$name.'_update'])) {
		if (!$opts['async'])
			$Ajax->activate('_page_body');
		else
			$Ajax->activate($name);
	}

	// ------ make selector ----------
	$selector = $first_opt = '';
	$first_id = false;
	$found = false;
	foreach($items as $value=>$descr) {
		$sel = '';
		if (in_array((string)$value, $selected_id, true)) {
			$sel = 'selected';
			$found = $value;
		}
		if ($first_id === false) {
			$first_id = $value;
			$first_opt = $descr;
		}
		$selector .= "<option $sel value='$value'>$descr</option>\n";
	}

	if ($first_id!==false) {
		$sel = ($found===$first_id) || ($found===false && ($spec_option===false)) ? "selected='selected'" : '';
	}
	// Prepend special option.
	if ($spec_option !== false) { // if special option used - add it
		$first_id = $spec_id;
		$first_opt = $spec_option;
		$sel = $found===false ? 'selected' : '';
		$selector = "<option $sel value='$spec_id'>$spec_option</option>\n"
			. $selector;
	}

	if ($found===false) {
		$selected_id = array($first_id);
	}
	$_POST[$name] = $multi ? $selected_id : $selected_id[0];

	$selector = "<select autocomplete='off' ".($multi  ? "multiple" : '')
		. ($opts['height']!==false ? ' size="'.$opts['height'].'"' : '')
		. "$disabled name='$name".($multi ? '[]' : '')."' class='combo' title='"
		. $opts['sel_hint']."'>".$selector."</select>\n";

	$Ajax->addUpdate($name, "_{$name}_sel", $selector);

	$selector = "<span id='_{$name}_sel'>".$selector."</span>\n";

	if ($select_submit != false) { // if submit on change is used - add select button
		$selector .= sprintf(SELECT_BUTTON, $disabled, user_theme(),
			(fallback_mode() ? '' : 'display:none;'),
			 '_'.$name.'_update')."\n";
	}
	default_focus($name);

	return $selector;
}
?>
