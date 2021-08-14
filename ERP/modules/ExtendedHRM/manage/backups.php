<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
$page_security = 'HR_EMPLOYEE_SETUP';
$path_to_root="../../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/gl/includes/gl_db.inc");
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/admin/db/maintenance_db.inc");
if (get_post('addupdate')) {	
	if (isset($_POST['Backups'])) {	 
    	header("Content-type: 'application/octet-stream'");
	    header('Content-Length: '.filesize(dirname(dirname(__FILE__)).'/backups/'.$_POST['Backups'].'.sql'));
    	header('Content-Disposition: attachment; filename='.$_POST['Backups'].'.sql');
    	readfile(dirname(dirname(__FILE__)).'/backups/'.$_POST['Backups'].'.sql'); 
    	exit; 
	}
}

if (get_post('deleteFile')) {	
	if (isset($_POST['Backups'])) {	     	
	    if(dirname(dirname(__FILE__)).'/backups/'.$_POST['Backups'].'.sql'){
	    	unlink(dirname(dirname(__FILE__)).'/backups/'.$_POST['Backups'].'.sql');
	    }	
	    display_notification("Selected Backup file removed from the system");        	
	}
}
if(get_post('ImportSQL')){
	global $db_connections; 

   // $prefix = $db_connections[$_SESSION["wa_current_user"]->company]["tbpref"];
	$conn = $db_connections[user_company()];
	
	if(isset($_FILES['filename'])){
		$path = $_FILES['filename']['name'];
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		if($ext == 'sql' && (strpos($path, 'extendedhrm-') === 0)){
			$tmpname = $_FILES['filename']['tmp_name'];

			$dir =  dirname(dirname(__FILE__)).'/backups/tmp';
			if (!file_exists($dir)) {
				mkdir ($dir,0777);
				$index_file = "<?php\nheader(\"Location: ../index.php\");\n?>";
				$fp = fopen($dir."/index.php", "w");
				fwrite($fp, $index_file);
				fclose($fp);
			}
			$filename = basename($_FILES['filename']['name']);
			
			move_uploaded_file($tmpname, $dir."/".$filename);

			if (db_import($dir.'/'.$filename, $conn)){
				unlink($dir.'/'.$filename);
				display_notification(trans("Backup Import completed."));
			}
		}else{
			display_error("Uploaded file is not Exported Database");
		}		
	}
}

if(get_post('RestoreSQL')){
	global $db_connections; 

   // $prefix = $db_connections[$_SESSION["wa_current_user"]->company]["tbpref"];
	$conn = $db_connections[user_company()];

	if (isset($_POST['Backups']) && file_exists(dirname(dirname(__FILE__)).'/backups/'.$_POST['Backups'].'.sql')) {	
		if (db_import(dirname(dirname(__FILE__)).'/backups/'.$_POST['Backups'].'.sql', $conn))
			display_notification(trans("Restore backup completed."));
	}
}

page(trans("Backup & Restore"), false, false, '', '');

if(isset($_POST['backuptables'])){
	backup_hrmtables();
}

start_form(true, true);
		start_outer_table(TABLESTYLE2);

	table_section(1);

		table_section_title(trans("Create Backup"));
			echo '<tr> <td style="height:90px;">';
			submit_center('backuptables', trans("Create Backup"));
			echo '</td> </tr>';

			file_row(trans("Upload Exported db") . ":", 'filename', 'filename');
			echo '<tr> <td style="height:30px;">';
			submit_center('ImportSQL', trans("Import Backup"));
			echo '</td> </tr>';

		table_section(2);
			table_section_title(trans("Backup Lists"));
			$kv = 1; 
			foreach(glob($path_to_root.'/modules/ExtendedHRM/backups/*.*') as $file) {	
				$this_filename = preg_replace('/\\.[^.\\s]{3,4}$/', '',basename($file));
				echo kv_radio_row($kv++.'. '.$this_filename, 'Backups', $this_filename);
			}
			echo '<tr> <td> &nbsp;</td> </tr>';
			submit_row('addupdate', trans("Download Backups"), '', '', true);
			echo '<tr> <td> &nbsp;</td> </tr>';
			submit_row('RestoreSQL', trans("Restore Backup"), '', '', true);

			echo '<tr> <td> &nbsp;</td> </tr>';
			submit_row('deleteFile', trans("Delete Backups"), '', '', true);
		end_outer_table();
		br();

	end_form();
	
end_page(); ?>