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
$page_security = 'SA_SETUPCOMPANY';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");

page(trans($help_context = "Company Setup"));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/admin/db/company_db.inc");
//-------------------------------------------------------------------------------------------------

if (isset($_POST['update']) && $_POST['update'] != "")
{
	$input_error = 0;
	if (!check_num('login_tout', 10))
	{
		display_error(trans("Login timeout must be positive number not less than 10."));
		set_focus('login_tout');
		$input_error = 1;
	}
	if (strlen($_POST['coy_name'])==0)
	{
		$input_error = 1;
		display_error(trans("The company name must be entered."));
		set_focus('coy_name');
	}
	if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '')
	{
    if ($_FILES['pic']['error'] == UPLOAD_ERR_INI_SIZE) {
			display_error(trans('The file size is over the maximum allowed.'));
			$input_error = 1;
    }
    elseif ($_FILES['pic']['error'] > 0) {
			display_error(trans('Error uploading logo file.'));
			$input_error = 1;
    }
		$result = $_FILES['pic']['error'];
		$filename = company_path()."/images";
		if (!file_exists($filename))
		{
			mkdir($filename);
		}
		$filename .= "/".clean_file_name($_FILES['pic']['name']);

		 //But check for the worst
		if (!in_array( substr($filename,-4), array('.jpg','.JPG','.png','.PNG')))
		{
			display_error(trans('Only jpg and png files are supported - a file extension of .jpg or .png is expected'));
			$input_error = 1;
		}
		elseif ( $_FILES['pic']['size'] > ($SysPrefs->max_image_size * 1024))
		{ //File Size Check
			display_error(trans('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $SysPrefs->max_image_size);
			$input_error = 1;
		}
		elseif ( $_FILES['pic']['type'] == "text/plain" )
		{  //File type Check
			display_error( trans('Only graphics files can be uploaded'));
			$input_error = 1;
		}
		elseif (file_exists($filename))
		{
			$result = unlink($filename);
			if (!$result)
			{
				display_error(trans('The existing image could not be removed'));
				$input_error = 1;
			}
		}

		if ($input_error != 1)
		{
			$result  =  move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
			$_POST['coy_logo'] = clean_file_name($_FILES['pic']['name']);
			if(!$result) 
				display_error(trans('Error uploading logo file'));
		}
	}
	if (check_value('del_coy_logo'))
	{
		$filename = company_path()."/images/".clean_file_name($_POST['coy_logo']);
		if (file_exists($filename))
		{
			$result = unlink($filename);
			if (!$result)
			{
				display_error(trans('The existing image could not be removed'));
				$input_error = 1;
			}
		}
		$_POST['coy_logo'] = "";
	}
	if ($_POST['add_pct'] == "")
		$_POST['add_pct'] = -1;
	if ($_POST['round_to'] <= 0)
		$_POST['round_to'] = 1;

	$updates = get_post([
		'coy_name',
		'coy_no',
		'gst_no',
		'tax_prd',
		'tax_last',
		'postal_address',
		'phone',
		'fax',
		'email',
		'coy_logo',
		'domicile',
		'use_dimension',
		'curr_default',
		'f_year',
		'shortname_name_in_list',
		'no_item_list' => 0,
		'no_customer_list' => 0,
		'no_supplier_list' =>0,
		'base_sales',
		'time_zone' => 0,
		'company_logo_report' => 0,
		'barcodes_on_stock' => 0,
		'add_pct',
		'round_to',
		'login_tout',
		'auto_curr_reval',
		'bcc_email',
		'alternative_tax_include_on_docs',
		'suppress_tax_rates',
		'use_manufacturing',
		'use_fixed_assets',
		'enable_invoice_editing',
		'refresh_permissions',
		'service_charge_return_account',
		'gl_after_transaction_id_update',
		'noqodi_account',
		'immigration_category',
		'tadbeer_category',
		'tasheel_category',
		'gl_bank_account_group',
		'gl_payment_card_group',
		'default_e_dirham_account',
		'opening_bal_equity_account',
		'customer_id_prefix',
		'org_ip',
        'ip_restriction'
	]);

	$_valid = true;
	foreach($_POST['excluded_customers'] as $cust_id) {
		if(!preg_match('/^[1-9][0-9]{0,15}$/', $cust_id)){
			$_valid = false;
			break;
		}
	}
	if($_valid) {
		$updates['excluded_customers'] = implode(',', $_POST['excluded_customers']);
	}

	if ($input_error != 1)
	{
		update_company_prefs($updates);

		$_SESSION['wa_current_user']->timeout = $_POST['login_tout'];
		display_notification_centered(trans("Company setup has been updated."));
	}
	set_focus('coy_name');
	$Ajax->activate('_page_body');
} /* end of if submit */

start_form(true);

$myrow = get_company_prefs();

$_POST['coy_name'] = $myrow["coy_name"];
$_POST['gst_no'] = $myrow["gst_no"];
$_POST['tax_prd'] = $myrow["tax_prd"];
$_POST['tax_last'] = $myrow["tax_last"];
$_POST['coy_no']  = $myrow["coy_no"];
$_POST['postal_address']  = $myrow["postal_address"];
$_POST['phone']  = $myrow["phone"];
$_POST['fax']  = $myrow["fax"];
$_POST['email']  = $myrow["email"];
$_POST['coy_logo']  = $myrow["coy_logo"];
$_POST['domicile']  = $myrow["domicile"];
$_POST['use_dimension']  = $myrow["use_dimension"];
$_POST['base_sales']  = $myrow["base_sales"];
if (!isset($myrow["shortname_name_in_list"]))
{
	set_company_pref("shortname_name_in_list", "setup.company", "tinyint", 1, '0');
	$myrow["shortname_name_in_list"] = get_company_pref("shortname_name_in_list");
}
$_POST['shortname_name_in_list']  = $myrow["shortname_name_in_list"];
$_POST['no_item_list']  = $myrow["no_item_list"];
$_POST['no_customer_list']  = $myrow["no_customer_list"];
$_POST['no_supplier_list']  = $myrow["no_supplier_list"];
$_POST['curr_default']  = $myrow["curr_default"];
$_POST['f_year']  = $myrow["f_year"];
$_POST['time_zone']  = $myrow["time_zone"];
if (!isset($myrow["company_logo_report"]))
{
	set_company_pref("company_logo_report", "setup.company", "tinyint", 1, '0');
	$myrow["company_logo_report"] = get_company_pref("company_logo_report");
}
$_POST['company_logo_report']  = $myrow["company_logo_report"];
if (!isset($myrow["barcodes_on_stock"]))
{
	set_company_pref("barcodes_on_stock", "setup.company", "tinyint", 1, '0');
	$myrow["barcodes_on_stock"] = get_company_pref("barcodes_on_stock");
}
$_POST['barcodes_on_stock']  = $myrow["barcodes_on_stock"];
$_POST['version_id']  = $myrow["version_id"];
$_POST['add_pct'] = $myrow['add_pct'];
$_POST['login_tout'] = $myrow['login_tout'];
if ($_POST['add_pct'] == -1)
	$_POST['add_pct'] = "";
$_POST['round_to'] = $myrow['round_to'];	
$_POST['auto_curr_reval'] = $myrow['auto_curr_reval'];	
$_POST['del_coy_logo']  = 0;
$_POST['bcc_email']  = $myrow["bcc_email"];
$_POST['alternative_tax_include_on_docs']  = $myrow["alternative_tax_include_on_docs"];
$_POST['suppress_tax_rates']  = $myrow["suppress_tax_rates"];
$_POST['use_manufacturing']  = $myrow["use_manufacturing"];
$_POST['use_fixed_assets']  = $myrow["use_fixed_assets"];


$_POST['enable_invoice_editing'] = $myrow['enable_invoice_editing'];
$_POST['refresh_permissions'] = $myrow['refresh_permissions'];
$_POST['gl_after_transaction_id_update'] = $myrow['gl_after_transaction_id_update'];
$_POST['service_charge_return_account'] = $myrow['service_charge_return_account'];

$_POST['tasheel_category'] = $myrow['tasheel_category'];
$_POST['tadbeer_category'] = $myrow['tadbeer_category'];
$_POST['immigration_category'] = $myrow['immigration_category'];
$_POST['noqodi_account'] = $myrow['noqodi_account'];
$_POST['gl_bank_account_group'] = $myrow['gl_bank_account_group'];
$_POST['gl_payment_card_group'] = $myrow['gl_payment_card_group'];
$_POST['default_e_dirham_account'] = $myrow['default_e_dirham_account'];
$_POST['opening_bal_equity_account'] = $myrow['opening_bal_equity_account'];
$_POST['customer_id_prefix'] = $myrow['customer_id_prefix'];
$_POST['excluded_customers'] = explode(',', $myrow['excluded_customers']);

if (!isset($myrow["org_ip"]))
{
    set_company_pref("org_ip", "setup.axispro", "varchar", 200, '');
    $myrow["org_ip"] = get_company_pref("org_ip");
}
$_POST['org_ip'] = $myrow['org_ip'];

if (!isset($myrow["ip_restriction"]))
{
    set_company_pref("ip_restriction", "setup.axispro", "tinyint", 1, '0');
    $myrow["ip_restriction"] = get_company_pref("ip_restriction");
}
$_POST['ip_restriction'] = $myrow['ip_restriction'];


start_outer_table(TABLESTYLE2);

table_section(1);
table_section_title(trans("General settings"));

text_row_ex(trans("Name (to appear on reports):"), 'coy_name', 50, 50);
textarea_row(trans("Address:"), 'postal_address', $_POST['postal_address'], 34, 5);
text_row_ex(trans("Domicile:"), 'domicile', 25, 55);

text_row_ex(trans("Phone Number:"), 'phone', 25, 55);
text_row_ex(trans("Fax Number:"), 'fax', 25);
email_row_ex(trans("Email Address:"), 'email', 50, 55);

email_row_ex(trans("BCC Address for all outgoing mails:"), 'bcc_email', 50, 55);

text_row_ex(trans("Official Company Number:"), 'coy_no', 25);
text_row_ex(trans("TRN No:"), 'gst_no', 25);
currencies_list_row(trans("Home Currency:"), 'curr_default', $_POST['curr_default']);

label_row(trans("Company Logo:"), $_POST['coy_logo']);
file_row(trans("New Company Logo (.jpg)") . ":", 'pic', 'pic');
check_row(trans("Delete Company Logo:"), 'del_coy_logo', $_POST['del_coy_logo']);

check_row(trans("Automatic Revaluation Currency Accounts"), 'auto_curr_reval', $_POST['auto_curr_reval']);
check_row(trans("Time Zone on Reports"), 'time_zone', $_POST['time_zone']);
check_row(trans("Company Logo on Reports"), 'company_logo_report', $_POST['company_logo_report']);
check_row(trans("Use Barcodes on Stocks"), 'barcodes_on_stock', $_POST['barcodes_on_stock']);
label_row(trans("Database Scheme Version"), $_POST['version_id']);

table_section(2);
table_section_title(trans("AXISPRO CONFIGURATIONS"));


//check_row(trans("ENABLE IP RESTRICTION"), 'ip_restriction', $_POST['ip_restriction']);

hidden("ip_restriction",0);

text_row_ex(trans("ORGANIZATION IP:"), 'org_ip', 50, 50);

check_row(trans("Enable Invoice Editing:"),'enable_invoice_editing',$_POST['enable_invoice_editing']);
check_row(trans("Refresh Permissions on each request:"),'refresh_permissions',$_POST['refresh_permissions']);
check_row(trans("Post GL Entries Only After Updating Transaction ID:"),'gl_after_transaction_id_update',$_POST['gl_after_transaction_id_update']);

stock_categories_list_row(trans("TASHEEL Category"),'tasheel_category',$_POST['tasheel_category']);
stock_categories_list_row(trans("TADBEER Category"),'tadbeer_category',$_POST['tadbeer_category']);
stock_categories_list_row(trans("IMMIGRATION Category"),'immigration_category',$_POST['immigration_category']);

gl_all_accounts_list_row(trans("Service Charge Return Account:"),'service_charge_return_account',$_POST['service_charge_return_account']);
gl_all_accounts_list_row(trans("NOQODI ACCOUNT:"),'noqodi_account',$_POST['noqodi_account']);
gl_all_accounts_list_row(trans("Default E-Dirham Account:"),'default_e_dirham_account',$_POST['default_e_dirham_account']);
gl_all_accounts_list_row(trans("Opening Balance Equity Account:"), 'opening_bal_equity_account', $_POST['opening_bal_equity_account']);

gl_account_types_list_row(trans("GL - Bank Account Group:"), 'gl_bank_account_group', $_POST['gl_bank_account_group']);
gl_account_types_list_row(trans("GL - Payment Card Account Group:"), 'gl_payment_card_group', $_POST['gl_payment_card_group']);


text_row(trans("Customer ID Prefix:"),'customer_id_prefix',$_POST['customer_id_prefix'],28,28);

customer_list_row(trans("Exclude Customers From Report:"), 'excluded_customers', null,
	'--select customers--', true, false, true, '', '', true);

//echo array_selector('sdfsdf',null,[0,1,2],["multi" => true]);

table_section_title(trans("General Ledger Settings"));
fiscalyears_list_row(trans("Fiscal Year:"), 'f_year', $_POST['f_year']);
text_row_ex(trans("Tax Periods:"), 'tax_prd', 10, 10, '', null, null, trans('Months.'));
text_row_ex(trans("Tax Last Period:"), 'tax_last', 10, 10, '', null, null, trans('Months back.'));
check_row(trans("Put alternative Tax Include on Docs"), 'alternative_tax_include_on_docs', null);
check_row(trans("Suppress Tax Rates on Docs"), 'suppress_tax_rates', null);





table_section_title(trans("Sales Pricing"));
sales_types_list_row(trans("Base for auto price calculations:"), 'base_sales', $_POST['base_sales'], false,
    trans('No base price list') );

text_row_ex(trans("Add Price from Std Cost:"), 'add_pct', 10, 10, '', null, null, "%");
$curr = get_currency($_POST['curr_default']);
text_row_ex(trans("Round calculated prices to nearest:"), 'round_to', 10, 10, '', null, null, $curr['hundreds_name']);
label_row("", "&nbsp;");


table_section_title(trans("Optional Modules"));
check_row(trans("Manufacturing"), 'use_manufacturing', null);
check_row(trans("Fixed Assets"), 'use_fixed_assets', null);
number_list_row(trans("Use Dimensions:"), 'use_dimension', null, 0, 2);

table_section_title(trans("User Interface Options"));

check_row(trans("Short Name and Name in List"), 'shortname_name_in_list', $_POST['shortname_name_in_list']);
check_row(trans("Search Item List"), 'no_item_list', null);
check_row(trans("Search Customer List"), 'no_customer_list', null);
check_row(trans("Search Supplier List"), 'no_supplier_list', null);
text_row_ex(trans("Login Timeout:"), 'login_tout', 10, 10, '', null, null, trans('seconds'));

end_outer_table(1);

hidden('coy_logo', $_POST['coy_logo']);
submit_center('update', trans("Update"), true, '',  'default');

end_form(2);
//-------------------------------------------------------------------------------------------------

end_page();

