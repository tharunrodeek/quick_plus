<?php include_once "config.php"; ?>
<?php include_once "ERP/includes/date_functions.inc"; ?>
<?php
include_once "ERP/API/API_Call.php";
$api = new API_Call();
?>
<?php


/**
 * @param string $menu
 * @return string
 * Setting up menu active
 */
function setActiveMenu($menu = "dashboard")
{

    $application = isset($_GET['application']) ? $_GET['application'] : "dashboard";

    if (empty(trim($application)))
        $application = "dashboard";


    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    //Sales Menu
    if (strpos($actual_link, '/sales/') !== false) {
        $application = "sales";
    } else if (strpos($actual_link, '/sales-tasheel/') !== false) {
        $application = "sales";
    } else if (strpos($actual_link, '/purchasing/') !== false) {
        $application = "purchase";
    } else if (strpos($actual_link, '/gl/') !== false || strpos($actual_link, '_accounts') !== false) {
        $application = "finance";
    } else if (strpos($actual_link, '/admin/') !== false || strpos($actual_link, 'settings') !== false) {
        $application = "setup";
    } else if (strpos($actual_link, '/items/') !== false || strpos($actual_link, 'items.php') !== false) {
        $application = "services";
    } else if (strpos($actual_link, 'hrm_')) {
        $application = "hrm";
    } else if (strpos($actual_link, 'fixed_assets') || strpos($actual_link, 'fixed_asset')) {
        $application = "fixed_assets";
    } else if (strpos($actual_link, 'purchase') || strpos($actual_link, 'inventory')) {
        $application = "purchase";
    }

     if (strpos($actual_link, 'hrm_admin_') || strpos($actual_link, 'ExtendedHRM')) {
         $application = "hr_admin";
     }

    //Report Menu
    if (strpos($actual_link, '/rep_') !== false ||
        strpos($actual_link, '_report') || strpos($actual_link, 'profit_and_loss'))
        $application = "reports";


    $class = "";
    if ($application == $menu) {
        $class = " kt-menu__item--here";
    }

    return $class;


}

/**
 * @param string $name
 * @return mixed|string
 * Defining routes
 */
function route($name = "")
{

    $route_array = [

        "" => "",

        //SALES
        "direct_invoice" => 'ERP/sales/sales_order_entry.php?NewInvoice=0',
        "edirham_invoice" => 'ERP/sales-tasheel/sales_order_entry.php?NewInvoice=0&is_tadbeer=1&show_items=ts',
        "customer_payment" => 'ERP/sales/customer_payments.php?',
        "manage_invoice" => 'ERP/sales/inquiry/customer_inquiry.php?',
        'allocate_cust_pmts_n_cr_notes' => 'ERP/sales/allocations/customer_allocation_main.php',
        "allocation_inquiry" => 'ERP/sales/inquiry/customer_allocation_inquiry.php?',
        "customers" => 'ERP/sales/manage/customers.php?',
        "view_print_trans" => 'ERP/admin/view_print_transaction.php?',
        "sales_person" => 'ERP/sales/manage/sales_people.php?',
        "reception_tkn" => 'ERP/themes/daxis/front-desk.php?',
        "cust_rcpt_vchr" => 'ERP/sales/customer_reciept_voucher.php',


        'suppliers' => 'ERP/purchasing/manage/suppliers.php?',
        'direct_supplier_invoice' => 'ERP/purchasing/po_entry_items.php?NewInvoice=Yes',
        'supplier_transactions' => 'ERP/purchasing/inquiry/supplier_inquiry.php?',
        'supplier_payment' => 'ERP/purchasing/supplier_payment.php?',
        'purchase_order' => 'ERP/purchasing/po_entry_items.php?NewOrder=Yes',
        'direct_grn' => 'ERP/purchasing/po_entry_items.php?NewGRN=Yes',
        'supplier_invoice' => 'ERP/purchasing/supplier_invoice.php?New=1',
        'supplier_creditnote' => 'ERP/purchasing/supplier_credit.php?New=1',
        'purchase_enquiry' => 'ERP/purchasing/inquiry/po_search_completed.php?',
        'supplier_enquiry' => 'ERP/purchasing/inquiry/supplier_inquiry.php?',
        'supplier_allocation_enquiry' => 'ERP/purchasing/inquiry/supplier_allocation_inquiry.php?',
        'supplier_purchase_report' => 'ERP/reporting/reports_main.php?Class=2',
        'projects' => 'ERP/dimensions/dimension_entry.php?',
        'list_projects' => 'ERP/dimensions/inquiry/search_dimensions.php?',
        'recevie_items' => 'ERP/purchasing/inquiry/po_search.php?',


        //SERVICES
        'services' => 'ERP/inventory/manage/items.php?',
        'manage_items' => 'ERP/inventory/manage/items.php?',
        'category' => 'ERP/inventory/manage/item_categories.php?',
        'service_list' => 'items.php?action=list',
        //'service_list' => 'ERP/inventory/manage/items.php?',
        'sales_price' => 'ERP/inventory/prices.php?',
        'purchase_price' => 'ERP/inventory/purchasing_data.php?',
        'standard_cost' => 'ERP/inventory/cost_update.php?',
        'inve_loc' => 'ERP/inventory/transfers.php?NewTransfer=1',
        'inven_adjust' => 'ERP/inventory/adjustments.php?NewAdjustment=1',
        'item_movement' => 'ERP/inventory/inquiry/stock_movements.php?',
        'item_status' => 'ERP/inventory/inquiry/stock_status.php?',
        'warehoue' => 'ERP/inventory/manage/locations.php?',
        'measure_unit' => 'ERP/inventory/manage/item_units.php?',
        'reorder_level' => 'ERP/inventory/reorder_level.php?',
        'purchase_order_enq' => 'ERP/purchasing/inquiry/po_search_completed.php?',
        'supplier_transaction' => 'ERP/purchasing/inquiry/supplier_inquiry.php?',
        'supplier_allocation_enqu' => 'ERP/purchasing/inquiry/supplier_allocation_inquiry.php?',


        //FINANCE
        'journal_entry' => 'ERP/gl/gl_journal.php?NewJournal=Yes',
        'journal_inquiry' => 'ERP/gl/inquiry/journal_inquiry.php?',
        'gl_inquiry' => 'ERP/gl/inquiry/gl_account_inquiry.php?',
        'gl_accounts' => 'ERP/gl/manage/gl_accounts.php?',
        'gl_groups' => 'ERP/gl/manage/gl_account_types.php?',
        'gl_classes' => 'ERP/gl/manage/gl_account_classes.php?',
        'payment_voucher' => 'payment_voucher.php?trans_type=1',
        'receipt_voucher' => 'payment_voucher.php?trans_type=2',
        'bank_transfer' => 'ERP/gl/bank_transfer.php?',
        'edirham_recharge' => 'ERP/gl/edirham_recharge.php?',
        'bank_accounts' => 'ERP/gl/manage/bank_accounts.php?',
        'manual_reconciliation' => 'ERP/gl/bank_account_reconcile.php?',
        'auto_reconciliation' => 'ERP/gl/reconciliation.php?',
        'chart_of_accounts' => 'chart_of_accounts.php',
        'drill_pl' => 'profit_and_loss_drill.php',


        //REPORTS
        'category_wise_sales' => 'ERP/sales/inquiry/categorywise_sales_inquiry.php?',
        'employee_wise_sales' => 'ERP/sales/inquiry/categorywise_employee_report.php?',
        'service_wise_sales' => 'ERP/sales/inquiry/service_wise_inquiry.php',
        'cust_bal_statement' => 'ERP/sales/inquiry/customer_balance_statement.php',
        'customer_bal_inquiry' => 'ERP/sales/inquiry/customer_balance_inquiry.php',
        'customer_wise_sales' => 'ERP/sales/inquiry/categorywise_customer_report.php?',
        'daily_collection' => 'ERP/sales/inquiry/daily_collection_inquiry.php?',
        'invoice_collection' => 'ERP/sales/inquiry/invoice_payment_inquiry.php?',
        'rep_customer_balance' => 'rep_customer_balances.php',
        'rep_tb' => 'rep_trial_balance.php',
        'rep_pl' => 'rep_profit_and_loss_statement.php',
        'rep_bs' => 'rep_balance_sheet.php',
        'rep_gl' => 'rep_gl_report.php',
        'overall_collection_report' => 'acc_balances_report.php',
        'overall_sales_report' => 'ERP/sales/inquiry/sales_collection_report.php?',
        'management_report' => 'ERP/axis-reports/public/',
        'service_report' => 'management_report.php?report=service_report',
        'invoice_report' => 'invoice_report.php?application=reports',
        'employee_commission_adheed' => 'ERP/sales/inquiry/employee_commission_adheed.php',


        //SETTINGS
        'company_setup' => 'ERP/admin/company_preferences.php?',
        'user_setup' => 'ERP/admin/users.php?',
        'tax_types_setup' => 'ERP/taxes/tax_types.php?',
        'gl_setup' => 'ERP/admin/gl_setup.php?',
        'fsy_setup' => 'ERP/admin/fiscalyears.php?',
        'item_tax_types_setup' => 'ERP/taxes/item_tax_types.php?',
        'void_trans' => 'ERP/admin/void_transaction.php?',
        'access_setup' => 'ERP/admin/security_roles.php?',
        'voided_trans' => 'ERP/admin/voided_transactions.php?',
        'attach_documents' => 'ERP/admin/attachments.php',

        //HRM Routes
        'leave_manage' => 'ERP/modules/ExtendedHRM/leave_approval.php?',
        'manage_employee' => 'ERP/modules/ExtendedHRM/manage/employees.php',
        'manage_department' => 'ERP/modules/ExtendedHRM/manage/department.php',
        'manage_designations' => 'ERP/modules/ExtendedHRM/manage/designation.php',
        'apply_leave' => 'hrm_leave.php',
        'attendance_entry' => 'ERP/modules/ExtendedHRM/attendance.php',
        'leave_types' => 'ERP/modules/ExtendedHRM/manage/leave_types.php',
        'payslip_entry' => 'hrm_admin_payroll.php',
        'pay_elements' => 'hrm_admin_pay_elements.php',
        'timesheet' => 'hrm_admin_timesheet.php',
        'process_slip' => 'hrm_admin_process_payroll.php',
        'default_settings' => 'hrm_admin_default.php',
        'manage_docs' => 'hrm_admin_document.php',
        'manage_emp_docs' => 'hrm_admin_employee_doc.php',
        'shifts' => 'ERP/modules/ExtendedHRM/manage/shifts.php',
        'upload_attendance' => 'hrm_admin_attendence_upload.php',
        'assign_shifts' => 'hrm_admin_shift_assign.php',
        'single_attendance_entry' => 'hrm_admin_single_click_attendence.php',

        'attendance_sett'=>'ERP/modules/ExtendedHRM/manage/hrm_attendance_sett.php',
        'manage_divisions'=>'ERP/modules/ExtendedHRM/manage/designation_group.php',
        'overtime_approve'=>'hrm_admin_overtime.php',
        'process_payslip'=> 'hrm_admin_payments.php',
        'shift_report'=> 'shift_report.php',
        'loan_entry'=> 'hrm_admin_loan.php',
        'request_doc'=> 'hrm_request_doc.php',
        'verify_request'=> 'hrm_admin_request_verify.php',
        'loan_master'=>'ERP/modules/ExtendedHRM/manage/loan_type.php',
        'payroll_report'=>'hrm_admin_payroll_report.php',
        'employee_profile'=>'hrm_employee_profile.php',
        'issue_warning'=>'hrm_admin_issue_warning.php',
        'request_passport'=>'hrm_passport.php',
        'hrm_certificate'=>'hrm_certificate.php',
        'req_noc'=>'hrm_noc.php',
        'asset_request'=>'hrm_asset_request.php',
        'assets'=>'ERP/modules/ExtendedHRM/manage/assets.php',
        'asset_return'=>'hrm_asset_return.php',
        'request_flow'=>'hrm_admin_request_flow.php',
        'leave_history'=>'hrm_employee_leave_history.php',
        'request_loan'=>'hrm_loan_request.php',
        'end_of_service'=>'hrm_admin_end_of_service.php',
        'list_end_of_service'=>'hrm_admin_list_end_of_service.php',
        'verify_gl_entries'=>'hrm_admin_verify_gl_entries.php',
        'invoice_mistake_entry'=>'hrm_admin_invoice_ded_entry.php',
        'holidays_entry'=>'hrm_admin_holidays_entry.php',
        'holiday_work_approve'=>'hrm_admin_holidays_approve.php',
        'list_approved_requests'=>'hrm_list_approved_requests.php',
        'employee_hierarchy'=>'assets/uploads/Hierarchy.xlsx',
        'employee_report'=>'hrm_admin_employees_list.php',
        'view_request_status'=>'hrm_admin_check_request_status.php',
        'employee_leave_report'=> 'hrm_employee_leave_report.php?application=hr_admin',
        'employee_gpssa_report'=> 'hrm_employee_gpssa_report.php',
        'emergency_report'=>'hrm_emergency_report.php',
        'esb_report'=>'hrm_esb_report.php',

        /* CRM */
        'reception_report' => 'reception_report.php'


    ];

    return isset($route_array[$name]) ? $route_array[$name] : "#";

}


/**
 * @return string
 * Get date format from ERP
 */
function getDateFormat()
{

    $dateFormat = $_SESSION['wa_current_user']->prefs->date_format;
    $sep = $GLOBALS['date_seps'][$_SESSION['wa_current_user']->prefs->date_sep];

//    $dateFormat = 3;

    switch ($dateFormat) {
        case 0:
            $fmt = "m" . $sep . "d" . $sep . "yyyy";
            break;
        case 1:
            $fmt = "d" . $sep . "m" . $sep . "yyyy";
            break;
        case 2:
            $fmt = "Y" . $sep . "m" . $sep . "d";
            break;
        case 3:
            $fmt = "M" . $sep . "dd" . $sep . "yyyy";
            break;
        case 4:
            $fmt = "d" . $sep . "M" . $sep . "yyyy";
            break;
        default:
            $fmt = "Y" . $sep . "M" . $sep . "dd";
    }

    return $fmt;


}

/**
 * @param $cfg_obj
 * @param $cfg_key
 * @return bool
 * Get default configs
 */
function APConfig($cfg_obj, $cfg_key)
{
    if (empty($cfg_obj) || empty($_SESSION['wa_current_user']))
        return false;

    $result = $_SESSION['wa_current_user']->axispro_config[$cfg_obj];

    if (!empty($cfg_key))
        $result = $result[$cfg_key];

    return $result;

}

/**
 * @param $elements
 * @param int $parentId
 * @return array
 * Build tree, parent child relationship
 */
function buildTree($elements, $parentId = 0)
{

    $tree = array();

    foreach ($elements as $element) {
        if ($element->parent_id == $parentId) {
            $children = buildTree($elements, $element->id);
            if ($children) {
                $element->children = $children;
            }
            $tree[] = $element;
        }
    }

    return $tree;
}

/**
 * @param $value
 * @param string $default
 * @return string
 * get value from http request
 */
function REQUEST_INPUT($value, $default = "")
{
    if (isset($_REQUEST[$value]) && !empty($_REQUEST[$value]))
        return $_REQUEST[$value];
    return $default;

}


/**
 * @param $array
 * @param string $value
 * @return string
 * get array value by key
 */
function getArrayValue($array, $value = "")
{
    if (isset($array[$value]) && !empty($array[$value]))
        return $array[$value];
    return '';

}

/**
 * @param $data
 * @param $value
 * @param $text
 * @param bool $selected_id
 * @param string $place_holder
 * @return string
 * Preparing selection option html dynamically
 */
function prepareSelectOptions($data, $value, $text, $selected_id = false, $place_holder = "Select")
{

    $options = "";


    if ($place_holder !== false)
        $options .= "<option value=''>$place_holder</option>";

    foreach ($data as $row) {

        $opt_text = $row[$text];
        $opt_value = $row[$value];

        $selected = "";

        if ($opt_value == $selected_id)
            $selected = 'selected';

        $options .= "<option value='$opt_value' $selected>$opt_text</option>";

    }

    return $options;

}

/**
 * @param string|string[] $access The security area which this UI falls under
 * @return string
 * Hiding tiles, unless user has access permission.
 */
function HideMenu($access)
{
    $hidden = true;
    if (!is_array($access)) {
        $access = [$access];
    }

    foreach($access as $sec_area) {
        if ($_SESSION["wa_current_user"]->can_access($sec_area)) {
            $hidden = false;
            break;
        }
    }

    return $hidden ? "hidden_elem" : "";
}


/**
 * @param $access
 * @return string
 * Hiding tiles, unless user has access permission.
 */
function HideApplication($access)
{

    if (!$_SESSION["wa_current_user"]->can_access($access))
        return "hidden_elem";

    return "";

}


function bt_random()
{

    $items = [
        'success',
//        'danger',
        'warning',
//        'brand',
        'info'
        //'primary',
    ];

    return $items[array_rand($items)];

}

function createMenuTile($permission, $main_title, $sub_title, $route, $fa_icon_class, $target = "", $icon_image = "",$hidden=false)
{
    if (in_array($_SESSION['wa_current_user']->access, [2])) {
        $hidden = false;
    }


    if (in_array($_SESSION['wa_current_user']->access, [48])) {
        $hidden = false;
    }

    if (in_array($_SESSION['wa_current_user']->user, [131])) {
        $hidden = false;
    }

    if($hidden) { return ''; }

    /**
     * Check if the user has access to the tile
     */
    $has_access = false;
    $sec_areas = $permission;
    if (!is_array($permission)) {
        $sec_areas = [$permission];
    }

    foreach($sec_areas as $sec_area) {
        if (user_check_access($sec_area)) {
            $has_access = true;
            break;
        }
    }

    if(!$has_access) { return ''; }

    $random_bt = bt_random();

    $icon_dom = ' <i class="fa ' . $fa_icon_class . ' fa-4x kt-font-' . $random_bt . ' "></i>';
    if (!empty($icon_image)) {
        $icon_dom = '<img src="assets/images/' . $icon_image . '" width="50" />';
    }

    return '
    
                    <div class="col-lg-3">
                        <div class="kt-portlet kt-iconbox kt-iconbox--' . $random_bt . ' kt-iconbox--animate-fast">
                            <div class="kt-portlet__body">
                                <div class="kt-iconbox__body">
                                    <div class="kt-iconbox__icon">
                                    
                                    
                                    ' . $icon_dom . '

                                        	</div>
                                    <div class="kt-iconbox__desc">
                                        <h3 class="kt-iconbox__title">
                                            <a class="kt-link" target="' . $target . '" href="' . $route . '">' . $main_title . '</a>
                                        </h3>
                                        <div class="kt-iconbox__content">
                                            ' . $sub_title . '
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    
    ';

}












