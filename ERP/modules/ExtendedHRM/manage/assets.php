<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HRM_ASSETS';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
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

page(trans("Departments"), @$_REQUEST['popup'], false, "", $js);
 
simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM'){

	$input_error = 0;

	if (strlen($_POST['description']) == 0) {
		$input_error = 1;
		display_error(trans("The  asset name cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)	{
    	if ($selected_id != -1)     	{
    		Update('kv_company_asset', array('id' => $selected_id), array('description'=>$_POST['description'],'category'=>$_POST['asset_category_id']));
			$note = trans("Selected asset has been updated");
    	}     	else     	{
    		Insert('kv_company_asset', array('description' => $_POST['description'],'category'=>$_POST['asset_category_id']));
			$note = trans("New Asset has been added");
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete'){

	$cancel_delete = 0;


	if ($cancel_delete == 0) {
		Delete('kv_company_asset', array('id' => $selected_id));
		display_notification(trans("Selected asset has been deleted"));
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


if(check_value('show_inactive'))
	$result = GetAll('kv_company_asset', array('inactive' => check_value('show_inactive')));
else
	$result = GetAll('kv_company_asset');

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(trans("ID"), trans("Asset Name"),trans("Category"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0; 

 foreach($result as $myrow) {
	 $result = GetAll('kv_asset_category', array('id' => $myrow["category"]));

	alt_table_row_color($k);
		
	label_cell($myrow["id"]);
	label_cell($myrow["description"]);
	label_cell($result[0]['name']);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'kv_company_asset', 'id');
 	edit_button_cell("Edit".$myrow["id"], trans("Edit"));
 	delete_button_cell("Delete".$myrow["id"], trans("Delete"));
	end_row();
}

inactive_control_row($th);
end_table(1);

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) {
 	if ($Mode == 'Edit') { //editing an existing department
		$myrow = GetRow('kv_company_asset', array('id' => $selected_id));

		$_POST['description']  = $myrow["description"];
		$_POST['asset_category_id']  = $myrow["category"];
	}
	hidden("selected_id", $selected_id);
	//label_row(trans("ID"), $myrow["id"]);
}

asset_category_list_row(trans("Asset Category"), 'asset_category_id',null,true," ",false,1);
text_row_ex(trans("Asset Name")." :", 'description', 30);


end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
