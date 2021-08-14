<?php
/**********************************************************************
    Direct Axis Technology L.L.C.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
$path_to_root="..";
$page_security = 'SA_ATTACHDOCUMENT';

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/admin/db/attachments_db.inc");
include_once($path_to_root . "/admin/db/transactions_db.inc");

if (isset($_GET['vw']))
	$view_id = $_GET['vw'];
else
	$view_id = find_submit('view');
if ($view_id != -1)
{
	$row = get_attachment($view_id);
	if ($row['filename'] != "")
	{
		if(in_ajax()) {
			$Ajax->popup($_SERVER['PHP_SELF'].'?vw='.$view_id);
		} else {
			$type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';	
    		header("Content-type: ".$type);
    		header('Content-Length: '.$row['filesize']);
 			header("Content-Disposition: inline");
	    	echo file_get_contents(company_path(). "/attachments/".$row['unique_name']);
    		exit();
		}
	}	
}
if (isset($_GET['dl']))
	$download_id = $_GET['dl'];
else
	$download_id = find_submit('download');

if ($download_id != -1)
{
	$row = get_attachment($download_id);
	if ($row['filename'] != "")
	{
		if(in_ajax()) {
			$Ajax->redirect($_SERVER['PHP_SELF'].'?dl='.$download_id);
		} else {
			$type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';	
    		header("Content-type: ".$type);
	    	header('Content-Length: '.$row['filesize']);
    		header('Content-Disposition: attachment; filename='.$row['filename']);
    		echo file_get_contents(company_path()."/attachments/".$row['unique_name']);
	    	exit();
		}
	}	
}

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(800, 500);
page(trans($help_context = "Attach Documents"), false, false, "", $js);

simple_page_mode(true);

function get_transaction_by_reference($reference,$type) {

//        $sql = "SELECT * FROM "
    $x = "";
    $sql = get_sql_for_view_transactions($type,null,null,$x,$reference);
    display_error($sql);

}

//----------------------------------------------------------------------------------------
if (isset($_GET['filterType'])) // catch up external links
	$_POST['filterType'] = $_GET['filterType'];
if (isset($_GET['trans_no']))
	$_POST['trans_no'] = $_GET['trans_no'];

if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM')
{

    $reference = $_POST['reference'];

    $sql_for_trans = get_sql_for_view_transactions($_POST['filterType'],null,null,$x,$reference);
    $trans_info = db_fetch(db_query($sql_for_trans));

    $_POST['trans_no'] = $trans_info['trans_no'];

	if (!transaction_exists($_POST['filterType'], $_POST['trans_no']) || empty($_POST['trans_no']) )
		display_error(trans("Selected transaction does not exists."));
	elseif ($Mode == 'ADD_ITEM' && !isset($_FILES['filename']))
		display_error(trans("Select attachment file."));
	elseif ($Mode == 'ADD_ITEM' && ($_FILES['filename']['error'] > 0)) {
    	if ($_FILES['filename']['error'] == UPLOAD_ERR_INI_SIZE) 
		  	display_error(trans("The file size is over the maximum allowed."));
    	else
		  	display_error(trans("Select attachment file."));
  	}
	else {
		//$content = base64_encode(file_get_contents($_FILES['filename']['tmp_name']));
		$tmpname = $_FILES['filename']['tmp_name'];

		$dir =  company_path()."/attachments";
		if (!file_exists($dir))
		{
			mkdir ($dir,0777);
			$index_file = "<?php\nheader(\"Location: ../index.php\");\n";
			$fp = fopen($dir."/index.php", "w");
			fwrite($fp, $index_file);
			fclose($fp);
		}

		$filename = basename($_FILES['filename']['name']);
		$filesize = $_FILES['filename']['size'];
		$filetype = $_FILES['filename']['type'];

		// file name compatible with POSIX
		// protect against directory traversal
		if ($Mode == 'UPDATE_ITEM')
		{
		    $row = get_attachment($selected_id);
		    if ($row['filename'] == "")
        		exit();
			$unique_name = $row['unique_name'];
			if ($filename && file_exists($dir."/".$unique_name))
				unlink($dir."/".$unique_name);
		}
		else
			$unique_name = random_id();

		//save the file
		move_uploaded_file($tmpname, $dir."/".$unique_name);

		if ($Mode == 'ADD_ITEM')
		{
			add_attachment($_POST['filterType'], $_POST['trans_no'], $_POST['description'],
				$filename, $unique_name, $filesize, $filetype);
			display_notification(trans("Attachment has been inserted."));
		}
		else
		{
			update_attachment($selected_id, $_POST['filterType'], $_POST['trans_no'], $_POST['description'],
				$filename, $unique_name, $filesize, $filetype); 
			display_notification(trans("Attachment has been updated."));
		}
	}
	refresh_pager('trans_tbl');
	$Ajax->activate('_page_body');
	$Mode = 'RESET';
}

if ($Mode == 'Delete')
{
	$row = get_attachment($selected_id);
	$dir =  company_path()."/attachments";
	if (file_exists($dir."/".$row['unique_name']))
		unlink($dir."/".$row['unique_name']);
	delete_attachment($selected_id);	
	display_notification(trans("Attachment has been deleted."));
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	unset($_POST['trans_no']);
	unset($_POST['description']);
	unset($_POST['reference']);
	$selected_id = -1;
}

function viewing_controls()
{
	global $selected_id;
	
    start_table(TABLESTYLE_NOBORDER);

	start_row();
	systypes_list_cells(trans("Type:"), 'filterType', null, true);
    text_cells("Reference",'filterReference',null,80,80);

    if (list_updated('filterType'))
		$selected_id = -1;;

	end_row();
    end_table(1);

}

function trans_view($trans)
{
	return get_trans_view_str($trans["type_no"], $trans["trans_no"]);
}

function edit_link($row)
{
  	return button('Edit'.$row["id"], trans("Edit"), trans("Edit"), ICON_EDIT);
}

function view_link($row)
{
  	return button('view'.$row["id"], trans("View"), trans("View"), ICON_VIEW);
}

function download_link($row)
{
  	return button('download'.$row["id"], trans("Download"), trans("Download"), ICON_DOWN);
}

function delete_link($row)
{
  	return button('Delete'.$row["id"], trans("Delete"), trans("Delete"), ICON_DELETE);
}

function reference($row) {
    return get_reference($row['type_no'],$row["trans_no"]);
}

function display_rows($type)
{
	$sql = get_sql_for_attached_documents($type);

//	display_error($sql);

	$cols = array(
		trans("Reference") => array('fun'=>'reference'),
	    trans("Description") => array('name'=>'description'),
	    trans("Filename") => array('name'=>'filename'),
	    trans("Size") => array('name'=>'filesize'),
	    trans("Filetype") => array('name'=>'filetype'),
	    trans("Date Uploaded") => array('name'=>'tran_date', 'type'=>'date'),
	    	array('insert'=>true, 'fun'=>'edit_link'),
	    	array('insert'=>true, 'fun'=>'view_link'),
	    	array('insert'=>true, 'fun'=>'download_link'),
	    	array('insert'=>true, 'fun'=>'delete_link')
	    );	
		$table =& new_db_pager('trans_tbl', $sql, $cols);

		$table->width = "60%";

		display_db_pager($table);
}

//----------------------------------------------------------------------------------------

start_form(true);

start_table(TABLESTYLE2);

$reference = "";

if (transaction_exists($_GET['filterType'], $_GET['trans_no'])) {
	$reference = reference([
		'type_no' => $_GET['filterType'],
		'trans_no' => $_GET['trans_no']
	]);
}

if ($selected_id != -1)
{
	if ($Mode == 'Edit')
	{
		$row = get_attachment($selected_id);
		$_POST['trans_no']  = $row["trans_no"];
		$_POST['description']  = $row["description"];


		$reference = get_reference($row['type_no'],$row['trans_no']);

		// display_error($reference);

		hidden('trans_no', $row['trans_no']);
		hidden('unique_name', $row['unique_name']);
		// label_row(trans("Transaction #"), $row['trans_no']);
	}	
	hidden('selected_id', $selected_id);
}
else {
	hidden('trans_no');
}
	// text_row_ex(trans("Transaction #").':', 'trans_no', 10);

text_row("Reference",'reference',$reference,80,80);
text_row_ex(trans("Description").':', 'description', 40);
file_row(trans("Attached File") . ":", 'filename', 'filename');
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'process');

br(2);

viewing_controls();
display_rows($_POST['filterType']);

end_form();
end_page();

?>

<script>
    $(document).ready(function (e) {

        $(document).on("change", "input[name='filterReference']", function () {

            $("select[name='filterType']").trigger('change')

        });



    })
</script>

