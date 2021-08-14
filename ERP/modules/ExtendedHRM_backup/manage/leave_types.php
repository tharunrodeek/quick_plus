<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Enhanced HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'SA_OPEN';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
$version_id = get_company_prefs('version_id');

$js = '';
if($version_id['version_id'] == '2.4.1'){
	if ($SysPrefs->use_popup_windows) 
		$js .= get_js_open_window(900, 500);	

	if (user_use_date_picker()) 
		$js .= get_js_date_picker();
	
}else{
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
}
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

page(trans("Leave Types"), @$_REQUEST['popup'], false, "", $js);
 
simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){

	$input_error = 0;

	if (strlen($_POST['description']) == 0) {
		$input_error = 1;
		display_error(trans("The Leave Type description cannot be empty."));
		set_focus('description');
	}
	if (strlen($_POST['char_code']) == 0) {
		$input_error = 1;
		display_error(trans("The Leave char code should not be empty and it must be 2 letters."));
		set_focus('char_code');
	}elseif(GetSingleValue('kv_empl_leave_types', 'COUNT(*)', array('char_code' => $_POST['char_code'])) && $Mode=='ADD_ITEM'){
		$input_error = 1;
		display_error(trans("The Leave char code already exists."));
		set_focus('char_code');
	}

	if ($input_error != 1)	{
    	if ($selected_id != -1)     	{
    		Update('kv_empl_leave_types', array('id' =>  $selected_id), array('description' => $_POST['description'],'frequency' => $_POST['frequency'],'char_code' =>$_POST['char_code'],'type'=>$_POST['type'],'salary_cut'=>$_POST['salary_cut']));
			$note = trans('Selected Leave Type has been updated');
    	}   else  	{
    		Insert('kv_empl_leave_types', array('description' => $_POST['description'], 'frequency' => $_POST['frequency'], 'char_code' =>$_POST['char_code'], 'deletable' => $_POST['deletable'],'type'=>$_POST['type'],'salary_cut'=>$_POST['salary_cut']));
    		$Input_id =$_POST['char_code'];

    		$rslt = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_salary` LIKE '{$Input_id}'", "Can't Query this Table");
			$exists = (db_num_rows($rslt))?TRUE:FALSE;
			if(!$exists) {
				$sql0 ="ALTER TABLE `".TB_PREF."kv_empl_salary` ADD `{$Input_id}` double NOT NULL DEFAULT '0' ";
				db_query($sql0, "Db Table creation failed, Kv empl_salary table");
			}
			$result = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_job` LIKE '{$Input_id}'", "Can't Query this Table");
			$existss = (db_num_rows($result))?TRUE:FALSE;
			if(!$existss) {
				$sql1 ="ALTER TABLE `".TB_PREF."kv_empl_job` ADD `{$Input_id}` double NOT NULL DEFAULT '0' ";
				db_query($sql1, "Db Table creation failed, Kv empl_job table");
			}	

			$result = db_query("SHOW COLUMNS FROM `".TB_PREF."kv_empl_grade` LIKE '{$Input_id}'", "Can't Query this Table");
			$existss = (db_num_rows($result))?TRUE:FALSE;
			if(!$existss) {
				$sql1 ="ALTER TABLE `".TB_PREF."kv_empl_grade` ADD `{$Input_id}` double NOT NULL DEFAULT '0' ";
				db_query($sql1, "Db Table creation failed, Kv empl_job table");
			}
			$note = trans('New Leave Type has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete'){
	$cancel_delete = 0;
	$type = GetRow('kv_empl_job', array('id' => $selected_id));
	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'kv_empl_job', $type['char_code']))	{
		$cancel_delete = 1;
		display_error(trans("Cannot delete this Leave Type because it has been used."));
	} 
	if ($cancel_delete == 0) {
		Delete('kv_empl_leave_types', array('id' => $selected_id));
		display_notification(trans('Selected Leave Type has been deleted'));
	} //end if Delete department
	$Mode = 'RESET';
} 

if ($Mode == 'RESET'){
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}
//-------------------------------------------------------------------------------------------------

$filter= array('inactive' => 0);
if(get_post('show_inactive'))
	$filter = null;
$result = GetAll('kv_empl_leave_types', $filter);

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(trans("Char Code"), trans("Description"), trans("Frequency"), trans("Leave Type"), trans("Salary Deduction"), "","");
inactive_control_column($th);

table_header($th);
$k = 0; 
 $items = array('12' => trans("Year"), '1' => trans("Month"), '0' => trans("Maternity"), '-1' => trans("All Time"), '-2' => trans("Each Event"), '60' => trans("5 Year"));
$leave_type_str='';
$cutting='';
 foreach($result as $myrow) {
	if($myrow["type"]=='0')
    {
        $leave_type_str='Unpaid Leave';
    }
    else if($myrow["type"]=='1')
    {
        $leave_type_str='Paid Leave';
    }

    if($myrow["salary_cut"]=='1')
    {
        $cutting='Half Day';
    }
    else if($myrow["salary_cut"]=='2')
    {
        $cutting='Full Day';
    }
    else
    {
        $cutting='';
    }

	alt_table_row_color($k);

	label_cell($myrow["char_code"]);
	label_cell($myrow["description"]);
	label_cell( $items[$myrow["frequency"]]);
    label_cell( $leave_type_str);
    label_cell($cutting);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'kv_empl_leave_types', 'id');
 	edit_button_cell("Edit".$myrow["id"], trans("Edit"));
 	if($myrow['deletable'] == 1)
 		delete_button_cell("Delete".$myrow["id"], trans("Delete"));
 	else
 		label_cell('');
 	//echo '<td>'.submit_js_confirm('go', trans("Are you sure you want to post accruals?"));
 	//echo '</td>';
	end_row();
}

inactive_control_row($th);
end_table(1);

//-------------------------------------------------------------------------------------------------
start_table(TABLESTYLE2);

if ($selected_id != -1) {
 	if ($Mode == 'Edit') { //editing an existing department
		$myrow = GetRow('kv_empl_leave_types', array('id' =>$selected_id));
		$_POST['description']  = $myrow["description"];
		$_POST['char_code']  = $myrow["char_code"];
		$_POST['frequency']  = $myrow["frequency"];
        $_POST['type']  = $myrow["type"];
	}
	hidden("selected_id", $selected_id);
	hidden("char_code", $myrow["char_code"]);
	label_row(trans("Leave Char Code :"), $myrow["char_code"]);
} else
	text_row_ex(trans("Leave Char Code :"),'char_code', 3);
text_row_ex(trans("Leave Type Description:"),'description', 60);
leave_frequency_list_row(trans("Leave Frequency:"),'frequency', null);
leave_type(trans("Leave Type:"),'type',null);
//salary_cut_ddl(trans("Salary Cut:"),'salary_cut',null);
echo '<tr class="ClsTRCut" >
<td class="label">Salary Cut:</td>
<td ><span id="_salary_cut_sel">
<select autocomplete="off" name="salary_cut" class="combo" title="" _last="0">
    <option value="1">Half Day</option>
    <option value="2">Full Day</option>
</select>
</span>
</td>
</tr>';

if ($selected_id != -1) 
	check_row(trans("Non-Deletable Leave"), 'deletable', null);
else
	hidden('deletable', 0);
end_table(1);
submit_add_or_update_center($selected_id == -1,'','both');
end_form();
end_page();
?>

<script type="text/javascript">
    $(".ClsTRCut").css('display','none');
    $('select[name="type"]').on('change', function(){
         if($(this).val()=='1')
         {
            $(".ClsTRCut").css('display','table-row');
         }
         else
         {
             $(".ClsTRCut").css('display','none');
         }
    });

</script>
