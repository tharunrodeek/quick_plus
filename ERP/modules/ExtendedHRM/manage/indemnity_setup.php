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
include_once($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
page(trans("Indemnity Settings"));
if (isset($_POST['addupdate'])) {		
		$allowances = kv_get_allowances('Earnings');
		$all_arr = [];
		foreach($allowances as $single){
			if(isset($_POST[$single['id']]) && $_POST[$single['id']] == 1)
				$all_arr[] = $single['id'];
		}
		if(!empty($all_arr)){
			$_POST['allowances'] = base64_encode(serialize($all_arr));
		}else 
			$_POST['allowances'] = '';
    	
    	Update('kv_empl_option', array('option_name' => 'indemnity'), array('option_value' =>  $_POST['allowances']));   
		display_notification(trans("Selected Indemnity Settings has been updated"));
} 
//-------------------------------------------------------------------------------------------------
start_form();
start_table(TABLESTYLE2);
table_section_title(trans("Allowances"));
$myrow = unserialize(base64_decode(GetSingleValue('kv_empl_option','option_value',array('option_name' => 'indemnity'))));

$allowances = kv_get_allowances('Earnings');
foreach($allowances as $single){
	if( !empty($myrow) && in_array($single['id'], $myrow))
		$_POST[$single['id']] = 1;
	kv_check_row(trans($single['description']), $single['id'], null);
}
end_table(1);
submit_center('addupdate', trans("Submit"), true, '', 'default');
end_form();
end_page();
?>
<style>
	table { width: auto; }
</style>