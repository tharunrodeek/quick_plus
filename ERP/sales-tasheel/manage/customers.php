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
$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();
	
page(trans($help_context = "Customers"), false, false, "", $js);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");
include_once($path_to_root . "/includes/ui/customer_item_discounts_view.inc");

if (isset($_GET['debtor_no'])) 
{
	$_POST['customer_id'] = $_GET['debtor_no'];
}

$selected_id = get_post('customer_id','');
//--------------------------------------------------------------------------------------------

function can_process()
{
	if (strlen($_POST['CustName']) == 0) 
	{
		display_error(trans("The customer name cannot be empty."));
		set_focus('CustName');
		return false;
	} 

	if (strlen($_POST['cust_ref']) == 0) 
	{
		display_error(trans("The customer short name cannot be empty."));
		set_focus('cust_ref');
		return false;
	} 
	
	if (!check_num('credit_limit', 0))
	{
		display_error(trans("The credit limit must be numeric and not less than zero."));
		set_focus('credit_limit');
		return false;		
	} 
	
	if (!check_num('pymt_discount', 0, 100)) 
	{
		display_error(trans("The payment discount must be numeric and is expected to be less than 100% and greater than or equal to 0."));
		set_focus('pymt_discount');
		return false;		
	} 
	
	if (!check_num('discount', 0, 100)) 
	{
		display_error(trans("The discount percentage must be numeric and is expected to be less than 100% and greater than or equal to 0."));
		set_focus('discount');
		return false;		
	} 

	return true;
}

//--------------------------------------------------------------------------------------------

function handle_submit(&$selected_id)
{
	global $path_to_root, $Ajax, $SysPrefs;

	//For Walk-in Customer
	if($selected_id == 1) {
	    display_error("Walk-in customer cannot be edited");
        return;
    }

	if (!can_process())
		return;
		
	if ($selected_id) 
	{

        $minutes_to_add = $_POST['activation_expiry'];

        $activated_till = null;

        if(!empty($minutes_to_add)) {
            $time = new DateTime();
            $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));

            $activated_till = $time->format('Y-m-d H:i:s');
        }


        update_customer($_POST['customer_id'], $_POST['CustName'], $_POST['cust_ref'], $_POST['address'],
			$_POST['tax_id'], $_POST['curr_code'], $_POST['dimension_id'], $_POST['dimension2_id'],
			$_POST['credit_status'], $_POST['payment_terms'], input_num('discount') / 100, input_num('pymt_discount') / 100,
			input_num('credit_limit'), $_POST['sales_type'], $_POST['notes'],$activated_till,$_POST['mobile'],$_POST['debtor_email']);

		update_record_status($_POST['customer_id'], $_POST['inactive'],
			'debtors_master', 'debtor_no');

		$Ajax->activate('customer_id'); // in case of status change
		display_notification(trans("Customer has been updated."));
	} 
	else 
	{ 	//it is a new customer

		begin_transaction();
		add_customer($_POST['CustName'], $_POST['cust_ref'], $_POST['address'],
			$_POST['tax_id'], $_POST['curr_code'], $_POST['dimension_id'], $_POST['dimension2_id'],
			$_POST['credit_status'], $_POST['payment_terms'], input_num('discount') / 100, input_num('pymt_discount') / 100,
			input_num('credit_limit'), $_POST['sales_type'], $_POST['notes'],$_POST['mobile'],$_POST['debtor_email']);

		$selected_id = $_POST['customer_id'] = db_insert_id();
         
		if (isset($SysPrefs->auto_create_branch) && $SysPrefs->auto_create_branch == 1)
		{
        	add_branch($selected_id, $_POST['CustName'], $_POST['cust_ref'],
                $_POST['address'], $_POST['salesman'], $_POST['area'], $_POST['tax_group_id'], '',
                get_company_pref('default_sales_discount_act'), get_company_pref('debtors_act'), get_company_pref('default_prompt_payment_act'),
                $_POST['location'], $_POST['address'], 0, $_POST['ship_via'], $_POST['notes'], $_POST['bank_account']);
                
        	$selected_branch = db_insert_id();
        
			add_crm_person($_POST['cust_ref'], $_POST['CustName'], '', $_POST['address'], 
				$_POST['phone'], $_POST['phone2'], $_POST['fax'], $_POST['email'], '', '');

			$pers_id = db_insert_id();
			add_crm_contact('cust_branch', 'general', $selected_branch, $pers_id);

			add_crm_contact('customer', 'general', $selected_id, $pers_id);
		}
		commit_transaction();

		display_notification(trans("A new customer has been added."));

		if (isset($SysPrefs->auto_create_branch) && $SysPrefs->auto_create_branch == 1)
			display_notification(trans("A default Branch has been automatically created, please check default Branch values by using link below."));
		
		$Ajax->activate('_page_body');
	}
}
//--------------------------------------------------------------------------------------------

if (isset($_POST['submit'])) 
{
	handle_submit($selected_id);
}



//-------------------------------------------------------------------------------------------- 

if (isset($_POST['delete'])) 
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtor_trans'

	if (key_in_foreign_table($selected_id, 'debtor_trans', 'debtor_no'))
	{
		$cancel_delete = 1;
		display_error(trans("This customer cannot be deleted because there are transactions that refer to it."));
	} 
	else 
	{
		if (key_in_foreign_table($selected_id, 'sales_orders', 'debtor_no'))
		{
			$cancel_delete = 1;
			display_error(trans("Cannot delete the customer record because orders have been created against it."));
		} 
		else 
		{
//			if (key_in_foreign_table($selected_id, 'cust_branch', 'debtor_no'))
//			{
//				$cancel_delete = 1;
//				display_error(trans("Cannot delete this customer because there are branch records set up against it."));
////				echo "<br> There are " . $myrow[0] . " branch records relating to this customer";
//			}
		}
	}
	
	if ($cancel_delete == 0) 
	{ 	//ie not cancelled the delete as a result of above tests
	
		delete_customer($selected_id);

		display_notification(trans("Selected customer has been deleted."));
		unset($_POST['customer_id']);
		$selected_id = '';
		$Ajax->activate('_page_body');
	} //end if Delete Customer
}

function customer_settings($selected_id) 
{
	global $SysPrefs, $path_to_root, $page_nested;
	
	if (!$selected_id) 
	{
	 	if (list_updated('customer_id') || !isset($_POST['CustName'])) {
			$_POST['CustName'] = $_POST['cust_ref'] = $_POST['address'] = $_POST['tax_id']  = '';
			$_POST['dimension_id'] = 0;
			$_POST['dimension2_id'] = 0;
			$_POST['sales_type'] = -1;
			$_POST['curr_code']  = get_company_currency();
			$_POST['credit_status']  = -1;
			$_POST['payment_terms']  = $_POST['notes']  = '';

			$_POST['discount']  = $_POST['pymt_discount'] = percent_format(0);
			$_POST['credit_limit']	= price_format($SysPrefs->default_credit_limit());
		}
	}
	else 
	{
		$myrow = get_customer($selected_id);

		$_POST['CustName'] = $myrow["name"];
		$_POST['cust_ref'] = $myrow["debtor_ref"];
		$_POST['address']  = $myrow["address"];
		$_POST['tax_id']  = $myrow["tax_id"];
		$_POST['dimension_id']  = $myrow["dimension_id"];
		$_POST['dimension2_id']  = $myrow["dimension2_id"];
		$_POST['sales_type'] = $myrow["sales_type"];
		$_POST['curr_code']  = $myrow["curr_code"];
		$_POST['credit_status']  = $myrow["credit_status"];
		$_POST['payment_terms']  = $myrow["payment_terms"];
		$_POST['discount']  = percent_format($myrow["discount"] * 100);
		$_POST['pymt_discount']  = percent_format($myrow["pymt_discount"] * 100);
		$_POST['credit_limit']	= price_format($myrow["credit_limit"]);
		$_POST['notes']  = $myrow["notes"];
		$_POST['inactive'] = $myrow["inactive"];
		$_POST['activated_till'] = $myrow["activated_till"];
		$_POST['mobile'] = $myrow["mobile"];
		$_POST['debtor_email'] = $myrow["debtor_email"];
	}

	start_outer_table(TABLESTYLE2);
	table_section(1);
	table_section_title(trans("Name and Address"));

	text_row(trans("Customer Name:"), 'CustName', $_POST['CustName'], 40, 80);
	text_row(trans("Customer Short Name:"), 'cust_ref', null, 30, 30);
	textarea_row(trans("Address:"), 'address', $_POST['address'], 35, 5);

	text_row(trans("Mobile No:"), 'mobile', null, 40, 40);
	text_row(trans("Email:"), 'debtor_email', null, 40, 40);
	text_row(trans("TRN No:"), 'tax_id', null, 40, 40);


	if (!$selected_id || is_new_customer($selected_id) || (!key_in_foreign_table($selected_id, 'debtor_trans', 'debtor_no') &&
		!key_in_foreign_table($selected_id, 'sales_orders', 'debtor_no'))) 
	{

//	    display_error($_POST['curr_code']);

        hidden('curr_code', $_POST['curr_code']);
//        currencies_list_row(trans("Customer's Currency:"), 'curr_code', $_POST['curr_code']);
	} 
	else 
	{
		//label_row(trans("Customer's Currency:"), $_POST['curr_code']);
		hidden('curr_code', $_POST['curr_code']);
	}
	sales_types_list_row(trans("Sales Type/Price List:"), 'sales_type', $_POST['sales_type']);

	if($selected_id)
		record_status_list_row(trans("Customer status:"), 'inactive');
	elseif (isset($SysPrefs->auto_create_branch) && $SysPrefs->auto_create_branch == 1)
	{
		table_section_title(trans("Branch"));
		text_row(trans("Phone:"), 'phone', null, 32, 30);
		text_row(trans("Secondary Phone Number:"), 'phone2', null, 32, 30);
		text_row(trans("Fax Number:"), 'fax', null, 32, 30);
		email_row(trans("E-mail:"), 'email', null, 35, 55);
		text_row(trans("Bank Account Number:"), 'bank_account', null, 30, 60);
		sales_persons_list_row( trans("Sales Person:"), 'salesman', null);
	}


	if($selected_id) {
        table_section(1);
        table_section_title(trans("Issue Activation Token"));

        text_row(trans("Activate up to (minutes):"), 'activation_expiry',null, 40, 80);
//        select_button_cell('generate_activation_expiry',trans('Generate'));
//        echo "<tr><td></td><td><button type='button' id='activate_button'>Generate</button></td></tr>";


    }



	table_section(2);

	table_section_title(trans("Sales"));

	hidden('discount', $_POST['discount']);
	hidden('pymt_discount',$_POST['pymt_discount']);
	hidden('credit_limit',$_POST['credit_limit']);
	hidden('payment_terms',$_POST['payment_terms']);
//	hidden('credit_status',$_POST['credit_status']);

//	percent_row(trans("Discount Percent:"), 'discount', $_POST['discount']);
//	percent_row(trans("Prompt Payment Discount Percent:"), 'pymt_discount', $_POST['pymt_discount']);
//	amount_row(trans("Credit Limit:"), 'credit_limit', $_POST['credit_limit']);

//	payment_terms_list_row(trans("Payment Terms:"), 'payment_terms', $_POST['payment_terms']);
	credit_status_list_row(trans("Credit Status:"), 'credit_status', $_POST['credit_status']);
	$dim = get_company_pref('use_dimension');
	if ($dim >= 1)
		dimensions_list_row(trans("Dimension")." 1:", 'dimension_id', $_POST['dimension_id'], true, " ", false, 1);
	if ($dim > 1)
		dimensions_list_row(trans("Dimension")." 2:", 'dimension2_id', $_POST['dimension2_id'], true, " ", false, 2);
	if ($dim < 1)
		hidden('dimension_id', 0);
	if ($dim < 2)
		hidden('dimension2_id', 0);

	if ($selected_id)  {
		start_row();
		echo '<td class="label">'.trans('Customer branches').':</td>';
	  	hyperlink_params_td($path_to_root . "/sales-tasheel/manage/customer_branches.php",
			'<b>'. ($page_nested ?  trans("Select or &Add") : trans("&Add or Edit ")).'</b>',
			"debtor_no=".$selected_id.($page_nested ? '&popup=1':''));
		end_row();
	}

	textarea_row(trans("General Notes:"), 'notes', null, 35, 5);
	if (!$selected_id && isset($SysPrefs->auto_create_branch) && $SysPrefs->auto_create_branch == 1)
	{
		table_section_title(trans("Branch"));
		locations_list_row(trans("Default Inventory Location:"), 'location');
		shippers_list_row(trans("Default Shipping Company:"), 'ship_via');
		sales_areas_list_row( trans("Sales Area:"), 'area', null);
		tax_groups_list_row(trans("Tax Group:"), 'tax_group_id', null);
	}
	end_outer_table(1);

	div_start('controls');
	if (@$_REQUEST['popup']) hidden('popup', 1);
	if (!$selected_id)
	{
		submit_center('submit', trans("Add New Customer"), true, '', 'default');
	} 
	else 
	{
		submit_center_first('submit', trans("Update Customer"),
		  trans('Update customer data'), $page_nested ? true : 'default');
		submit_return('select', $selected_id, trans("Select this customer and return to document entry."));
		submit_center_last('delete', trans("Delete Customer"),
		  trans('Delete customer data if have been never used'), true);
	}
	div_end();
}

//--------------------------------------------------------------------------------------------

check_db_has_sales_types(trans("There are no sales types defined. Please define at least one sales type before adding a customer."));
 
start_form();

if (db_has_customers()) 
{
	start_table(TABLESTYLE_NOBORDER);
	start_row();
	customer_list_cells(trans("Select a customer: "), 'customer_id', null,
		trans('New customer'), true, check_value('show_inactive'));
	check_cells(trans("Show inactive:"), 'show_inactive', null, true);
	end_row();
	end_table();

	if (get_post('_show_inactive_update')) {
		$Ajax->activate('customer_id');
		set_focus('customer_id');
	}
} 
else 
{
	hidden('customer_id');
}

//if (!$selected_id || list_updated('customer_id'))
if (!$selected_id)
	unset($_POST['_tabs_sel']); // force settings tab for new customer

tabbed_content_start('tabs', array(
		'settings' => array(trans('&General settings'), $selected_id),
		'contacts' => array(trans('&Contacts'), $selected_id),
		'transactions' => array(trans('&Transactions'), (user_check_access('SA_SALESTRANSVIEW') ? $selected_id : null)),
		'orders' => array(trans('Sales &Orders'), (user_check_access('SA_SALESTRANSVIEW') ? $selected_id : null)),

        'item_discounts' => array(trans('Items and Discounts'), $selected_id),

	));
	
	switch (get_post('_tabs_sel')) {
		default:
		case 'settings':
			customer_settings($selected_id); 
			break;
		case 'contacts':
			$contacts = new contacts('contacts', $selected_id, 'customer');
			$contacts->show();
			break;

        case 'item_discounts':

            $contacts = new customer_item_discounts('contacts', $selected_id, 'customer');
            $contacts->show();
            break;


		case 'transactions':
			$_GET['customer_id'] = $selected_id;
			include_once($path_to_root."/sales-tasheel/inquiry/customer_inquiry.php");
			break;
		case 'orders':
			$_GET['customer_id'] = $selected_id;
			include_once($path_to_root."/sales-tasheel/inquiry/sales_orders_view.php");
			break;
	};
br();
tabbed_content_end();

end_form();
end_page();

?>
<script src="../../js/jquery3.3.1.min.js"></script>
<script>

    $("#activate_button").click(function (e) {
        // alert(1);
    });

    $(document).on('change', "input[name='CustName']", function() {
        $("input[name='cust_ref']").val($(this).val())
    })


</script>

