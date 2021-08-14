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
$page_security = 'SA_EXCHANGERATE';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/banking.inc");

$js = "";
if (user_use_date_picker())
	$js .= get_js_date_picker();
page(trans($help_context = "Revaluation of Currency Accounts"), false, false, "", $js);

if (isset($_GET['AddedID'])) 
{
	$trans_no = $_GET['AddedID'];
	$JE = $_GET['JE'];
	$trans_type = ST_JOURNAL;

	if ($trans_no == 0)
   		display_notification_centered( trans("No Revaluation was needed"));
	else
	{
   		display_notification_centered( trans("Transfer has been entered"));

		display_note(get_gl_view_str($trans_type, $trans_no, trans("&View the GL Journal Entries for this Transfer")));
	}
	if ($JE > 0)
   		display_notification_centered(sprintf(trans("%d Journal Entries for AR/AP accounts have been added"), $JE));

	//display_footer_exit();
}


//---------------------------------------------------------------------------------------------
function check_data()
{
	if (!is_date($_POST['date']))
	{
		display_error( trans("The entered date is invalid."));
		set_focus('date');
		return false;
	}
	if (!is_date_in_fiscalyear($_POST['date']))
	{
		display_error(trans("The entered date is out of fiscal year or is closed for further data entry."));
		set_focus('date');
		return false;
	}
	if (!check_reference($_POST['ref'], ST_JOURNAL))
	{
		set_focus('ref');
		return false;
	}

	return true;
}

//---------------------------------------------------------------------------------------------

function handle_submit()
{
	if (!check_data())
		return;

	$trans = add_exchange_variation_all($_POST['date'], $_POST['ref'], $_POST['memo_']);

	meta_forward($_SERVER['PHP_SELF'], "AddedID=".$trans[0]."&JE=".$trans[1]);
	//clear_data();
}


//---------------------------------------------------------------------------------------------

function display_reval()
{
	global $Refs;
	start_form();
	start_table(TABLESTYLE2);

	if (!isset($_POST['date']))
		$_POST['date'] = Today();
    date_row(trans("Date for Revaluation:"), 'date', '', null, 0, 0, 0, null, true);
    ref_row(trans("Reference:"), 'ref', '', $Refs->get_next(ST_JOURNAL, null, $_POST['date']), false, ST_JOURNAL);
    textarea_row(trans("Memo:"), 'memo_', null, 40,4);
	end_table(1);

	submit_center('submit', trans("Revaluate Currencies"), true, false);
	end_form();
}

//---------------------------------------------------------------------------------------------

function clear_data()
{
	unset($_POST['date_']);
	unset($_POST['memo_']);
}

//---------------------------------------------------------------------------------------------

if (get_post('submit'))
	handle_submit();

//---------------------------------------------------------------------------------------------

display_reval();

end_page();

