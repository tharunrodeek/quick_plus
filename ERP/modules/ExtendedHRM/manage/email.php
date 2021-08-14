<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_EMPL_INFO';
$path_to_root="../../..";
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();
include_once($path_to_root . "/admin/db/attachments_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/reporting/includes/class.mail.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
global $hrm_empl_relation;

$version_id = get_company_prefs('version_id');

$js = '';

echo '<script language="javascript" type="text/javascript" src="'.$path_to_root.'/modules/ExtendedHRM/js/nicEdit-latest.js"></script>';
page(trans($help_context = "Employee E-mail"), false, false, "", $js);

simple_page_mode(true);

check_db_has_employees(sprintf(trans("There is no employee in this system. Kindly Open '%s' to update it"), "<a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>".trans("Add And Manage Employees")."</a>"));

//----------------------------------------------------------------------------------------
if (isset($_GET['empl_id']))
	 $empl_id = $_POST['empl_id'] = $_GET['empl_id'];

if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM'){
		
		$sent = $try = 0;
        $emails = "";
        $company = get_company_prefs();
        $user = $_SESSION["wa_current_user"]->name;
        $mail = new email(str_replace(",", "", $company['coy_name']), $company['email']);
        
        $sender = "<br><br><span>" .$user . "<br>" . $company['coy_name'] . "<br>" . $company['postal_address'] . "<br>" . $company['email'] . "<br></span>" . $company['phone'];
        
        $to = $_POST['empl_name'].' <'. $_POST['email_to'].' > ';
        $tomail = $to;
        $cc[] = array($_POST['cc'], $_POST['n_name']);
       
        $mail->to($to);
        
        
            $mail->subject($_POST['subject']);
            $mail->html($_POST['description'] . $sender);
       
        if(!empty($attachments)){
            foreach ($attachments as $key => $value) {
                $mail->attachment($value, $key);
            }           
        }
         //display_error($message);
        $emails .= " " . $tomail;
        if ($mail->send()) 
            $sent++;	
		
		Insert('kv_empl_emails', array('empl_id' => $_POST['empl_id'], 'empl_name' => $_POST['empl_name'],'email_to' => $_POST['email_to'],'cc' => $_POST['cc'],'subject' => $_POST['subject'],'description' => $_POST['description'],'send_info' => $sent));
	//	Update('kv_empl_family', array('id' => $_POST['selected_id']), array('empl_id' => $_POST['empl_id'], 'full_name' => $_POST['full_name'],'relation' => $_POST['relation'] )); 
	
}



if ($Mode == 'RESET'){
	unset($_POST['trans_no']);
	unset($_POST['description']);
	$selected_id = -1;
	$Ajax->activate('Attachments');
}



function edit_link($row){
  	return '<td>'.button('Edit'.$row["id"], trans("Edit"), trans("Edit"), ICON_EDIT).'</td>';
}

function display_rows(){
	$all_attachments =  GetAll('kv_empl_emails', array('empl_id' => get_post('empl_id')));
	if(get_post('empl_id') > 0 ){
		$all_attachments = GetAll('kv_empl_emails', array('`empl_id`' => get_post('empl_id')));
	}else
		$all_attachments = '';

	start_table(TABLESTYLE, "width=60%");
    $th = array(trans("ID"),trans("Empl_id"),trans("Name"), trans("E-mail"),  trans("Subject"),'');
    if(basename($_SERVER['PHP_SELF']) == 'employees.php')
    	unset($th[6]);
    table_header($th);
    foreach($all_attachments as $attach){
    	start_row();
    	label_cell($attach['id']);
    	label_cell($attach['empl_id']);
    	label_cell($attach['empl_name']);    	
    	label_cell($attach['email_to']);
    	label_cell($attach['subject']);
    	if(basename($_SERVER['PHP_SELF']) != 'employees.php')
    		echo edit_link($attach);
    	
    	
    	end_row();
    }
    end_table();	
}

//----------------------------------------------------------------------------------------
$action = $_SERVER['PHP_SELF'];

if ($page_nested)
	$action .= "?empl_id=".get_post('empl_id');
start_form(true, false, $action);

	if (db_has_employees()) {
		if (!$page_nested){
			start_table(TABLESTYLE_NOBORDER);
			start_row();
			department_list_cells(trans("Select a Department")." :", 'dept_id', null,	trans("No Department"), true, check_value('show_inactive'));
			employee_list_cells(trans("Select an Employee")." :", 'empl_id', null,	trans("Select an Employee"), true, check_value('show_inactive'),false, false,true);
			$new_item = get_post('empl_id')=='';
			
			end_row();
			end_table();

			if (get_post('_show_inactive_update')) {
				$Ajax->activate('empl_id');
				set_focus('empl_id');
			}
		}
	}
	else{
		hidden('empl_id', get_post('empl_id'));
	}

	if(list_updated('empl_id'))
		$Ajax->activate('Attachments');

	div_start('Attachments');
	if(get_post('empl_id') > 0 ){
		br();
		display_rows();	
		br();
		if ($selected_id != -1 ){	
			if($Mode == 'Edit')	{
				$row = GetRow('kv_empl_emails', array('id' => $selected_id));
				$_POST['empl_id'] = $row["empl_id"];
				$_POST['empl_name']  = $row["empl_name"];	
				$_POST['email_to']  = $row["email_to"];			
				$_POST['subject']  = $row["subject"];				
				$_POST['nominee_email']  = $row["cc"];				
				$_POST['description']  = $row["description"];				
			} 					
		} 
		$empl_details = GetRow('kv_empl_info', array('empl_id' => get_post('empl_id')));
		$nominee_email = GetSingleValue('kv_empl_job', 'nominee_email', array('empl_id' => get_post('empl_id')));
		$nominee_name = GetSingleValue('kv_empl_job', 'nominee_name', array('empl_id' => get_post('empl_id')));
		//display_error($nominee_email);
		start_table(TABLESTYLE2);
		label_row(trans("Employee Name"), $empl_details['empl_id'].'-'.$empl_details['empl_firstname'].$empl_details['empl_lastname']);
		hidden('empl_name', $empl_details['empl_firstname'].$empl_details['empl_lastname']);
		label_row(trans("Employee E-mail"), $empl_details['email']);
		hidden('email_to', $empl_details['email']);
		if($nominee_email){
			check_row(trans("Send email to Nominee "), 'email',null,true);
			if((list_updated('email')|| get_post('email')) && check_value('email')==1){
				label_row(trans("Nominee E-mail"), $nominee_email);
				hidden('cc',$nominee_email);
				hidden('n_name',$nominee_name);
				$Ajax->activate('Attachments');
			}
		}	
		text_row_ex(trans("Subject").':','subject', 50);
		kv_hrm_textarea_row(null,'description', null, 70, 10);
		
		
		hidden('selected_id', $selected_id);
		//text_row_ex(trans("Full Name").':', 'full_name', 40);
		//hrm_empl_relation_list_row(trans("Relation").':', 'relation', null);
		//kv_doc_row(trans("Select Docs") . ":", 'kv_attach_name', 'kv_attach_name');
		
		end_table(1); 
		if($selected_id == -1)
		submit_add_or_update_center($selected_id == -1, '', '');
	}
	 
	div_end();		

end_form();
echo "<script type='text/javascript'> kvcodes_crm_nicEditor();  </script> \n";
end_page();
?>
