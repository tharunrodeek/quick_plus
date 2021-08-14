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
/*
	Security sections groups various areas on both functionality and privilege levels.
	Often analytic inquires are available only for management, and configuration
	for administration or management staff. This is why we have those three
	section type inside near every FA module.

	Section codes 0-99 are reserved for core FA functionalities.
	Every security section can contain up to 256 different areas.
	External modules can extend security roles system by adding rows to 
	$security_sections and $security_areas using section codes >=100.
	Security areas and sections created by extension modules/plugins
	have dynamically assigned 3-byte integer codes. The highest byte is zero
	for sections/areas defined in this file, and extid+1 for those defined 
	by extensions 
*/
define('SS_SADMIN',	1<<8);	// site admin
define('SS_SETUP',	2<<8);	// company level setup
define('SS_SPEC',	3<<8);	// special administration

define('SS_SALES_C',11<<8); // configuration
define('SS_SALES',	12<<8);	// transactions
define('SS_SALES_A',13<<8); // analytic functions/reports/inquires

define('SS_PURCH_C',21<<8);
define('SS_PURCH',	22<<8);
define('SS_PURCH_A',23<<8);

define('SS_ITEMS_C',31<<8);
define('SS_ITEMS',	32<<8);
define('SS_ITEMS_A',33<<8);

define('SS_ASSETS_C',36<<8);
define('SS_ASSETS',	37<<8);
define('SS_ASSETS_A',38<<8);

define('SS_MANUF_C',41<<8);
define('SS_MANUF',	42<<8);
define('SS_MANUF_A',43<<8);

define('SS_DIM_C',	51<<8);
define('SS_DIM',	52<<8);
define('SS_DIM_A',	53<<8);

define('SS_GL_C',	61<<8);
define('SS_GL',		62<<8);
define('SS_GL_A',	63<<8);

define('SS_HRM_A',71<<8);

define('SS_HEAD_MENU',81<<8);

define('SS_FINANCE', 91<<8);

define('SS_CRM_A', 101<<8);

define('SS_DASHBOARD', 102<<8);

$security_sections = array(
 	SS_SADMIN => trans("System administration"),
 	SS_SETUP => trans("Company setup"),
 	SS_SPEC => trans("Special maintenance"),
 	SS_SALES_C => trans("Sales configuration"),
 	SS_SALES => trans("Sales transactions"),
 	SS_SALES_A => trans("Sales related reports"),
 	SS_PURCH_C => trans("Purchase configuration"),
 	SS_PURCH => trans("Purchase transactions"),
 	SS_PURCH_A => trans("Purchase analytics"),
 	SS_ITEMS_C => trans("Inventory configuration"),
 	SS_ITEMS => trans("Inventory operations"),
 	SS_ITEMS_A => trans("Inventory analytics"),
	SS_ASSETS_C => trans("Fixed Assets configuration"),
	SS_ASSETS => trans("Fixed Assets operations"),
	SS_ASSETS_A => trans("Fixed Assets analytics"),
 	SS_MANUF_C => trans("Manufacturing configuration"),
 	SS_MANUF => trans("Manufacturing transactions"),
 	SS_MANUF_A => trans("Manufacturing analytics"),
 	SS_DIM_C => trans("Dimensions configuration"),
 	SS_DIM => trans("Dimensions"),
 	SS_GL_C => trans("Banking & GL configuration"),
 	SS_GL => trans("Banking & GL transactions"),
 	SS_GL_A => trans("Banking & GL analytics"),
    SS_HRM_A => trans("HRMS PERMISSION"),
	SS_HEAD_MENU => trans("Header menu display permission"),
	SS_FINANCE => trans("Finance related"),
	SS_CRM_A => trans("Customer Relationship Analytics"),
	SS_DASHBOARD => trans("Dashboard Sections")
);

/*
	This table stores security areas available in FA. 
	Key is area identifier used to check user rights, values are
	codes stored for each role in security_roles table and description used
	in roles editor.

	Set of allowed access areas codes is retrieved during user login from
	security_roles table, and cached in user profile.

	Special value 'SA_OPEN' is used for publicly available pages like login/logout.
*/
$security_areas =array(
	//
	//	Site administration
	//
	'SA_CREATECOMPANY' =>array(SS_SADMIN|1, trans("Install/update companies")),
	'SA_CREATELANGUAGE' => array(SS_SADMIN|2, trans("Install/update languages")),
	'SA_CREATEMODULES' => array(SS_SADMIN|3, trans("Install/upgrade modules")),
	'SA_SOFTWAREUPGRADE' => array(SS_SADMIN|4, trans("Software upgrades")),
	//
	//	Company setup
	//
	'SA_SETUPCOMPANY' => array(SS_SETUP|1, trans("Company parameters")),
	'SA_SECROLES' => array(SS_SETUP|2, trans("Access levels edition")),
	'SA_USERS' => array(SS_SETUP|3, trans("Users setup")),
	'SA_POSSETUP' => array(SS_SETUP|4, trans("Point of sales definitions")),
	'SA_PRINTERS' => array(SS_SETUP|5, trans("Printers configuration")),
	'SA_PRINTPROFILE' => array(SS_SETUP|6, trans("Print profiles")),
	'SA_PAYTERMS' => array(SS_SETUP|7, trans("Payment terms")),
	'SA_SHIPPING' => array(SS_SETUP|8, trans("Shipping ways")),
	'SA_CRSTATUS' => array(SS_SETUP|9, trans("Credit status definitions changes")),
	'SA_INVENTORYLOCATION' => array(SS_SETUP|10, trans("Inventory locations changes")),
	'SA_INVENTORYMOVETYPE'  => array(SS_SETUP|11, trans("Inventory movement types")),
	'SA_WORKCENTRES' => array(SS_SETUP|12, trans("Manufacture work centres")),
	'SA_FORMSETUP' => array(SS_SETUP|13, trans("Forms setup")),
	'SA_CRMCATEGORY' => array(SS_SETUP|14, trans("Contact categories")),
	//
	// Special and common functions
	//
	'SA_VOIDTRANSACTION' 		=> array(SS_SPEC|1, trans("Voiding transactions")),
	'SA_BACKUP' 				=> array(SS_SPEC|2, trans("Database backup/restore")),
	'SA_VIEWPRINTTRANSACTION' 	=> array(SS_SPEC|3, trans("Common view/print transactions interface")),
	'SA_ATTACHDOCUMENT' 		=> array(SS_SPEC|4, trans("Attaching documents")),
	'SA_SETUPDISPLAY' 			=> array(SS_SPEC|5, trans("Display preferences")),
	'SA_CHGPASSWD' 				=> array(SS_SPEC|6, trans("Password changes")),
	'SA_EDITOTHERSTRANS' 		=> array(SS_SPEC|7, trans("Edit other users transactions")),
	'SA_VOIDEDTRANSACTIONS' => [SS_SPEC|8, 	trans("Voided Transaction Report")],
	/** 
	 * Note: This Access Level (SA_REP) is only for the UI.
	 * The actual permission cannot be set because,
	 * every pdf/excel report goes through the reports_main.php 
	 */
	'SA_REP' 				=> [SS_SPEC|9, 	trans("Reports and Analysis")],
	'SA_MGMTREP'			=> [SS_SPEC|10, trans("Management Report")],
	//
	// Sales related functionality
	//
	'SA_SALESTYPES' => array(SS_SALES_C|1, trans("Sales types")),
	'SA_SALESPRICE' => array(SS_SALES_C|2, trans("Sales prices edition")),
	'SA_SALESMAN' => array(SS_SALES_C|3, trans("Sales staff maintenance")),
	'SA_SALESAREA' => array(SS_SALES_C|4, trans("Sales areas maintenance")),
	'SA_SALESGROUP' => array(SS_SALES_C|5, trans("Sales groups changes")),
	'SA_STEMPLATE' => array(SS_SALES_C|6, trans("Sales templates")),
	'SA_SRECURRENT' => array(SS_SALES_C|7, trans("Recurrent invoices definitions")),
	'SA_CUSTDISCOUNT'	 => [SS_SALES_C|8, trans("Items and Discount settings for customer")],
	'SA_UPDATECRLMT'	 => [SS_SALES_C|9, trans("Credit Limit settings for customer")],

	'SA_SALESTRANSVIEW' => array(SS_SALES|1,  trans("Sales transactions view")),
	'SA_CUSTOMER' => array(SS_SALES|2,  trans("Sales customer and branches changes")),
	'SA_SALESORDER' => array(SS_SALES|3, trans("Sales orders edition")),
	'SA_SALESDELIVERY' => array(SS_SALES|4, trans("Sales deliveries edition")),
	'SA_SALESINVOICE' => array(SS_SALES|5, trans("Sales invoices edition")),
	'SA_SALESCREDITINV' => array(SS_SALES|6, trans("Sales credit notes against invoice")),
	'SA_SALESCREDIT' => array(SS_SALES|7, trans("Sales freehand credit notes")),
	'SA_SALESPAYMNT' => array(SS_SALES|8, trans("Customer payments entry")),
	'SA_SALESALLOC' => array(SS_SALES|9, trans("Customer payments allocation")),
	'SA_SALESQUOTE' => array(SS_SALES|10, trans("Sales quotations")),
	'SA_CUSTRCPTVCHR' => array(SS_SALES|11, trans("Customer reciept voucher")),
    'SA_SERVICE_REQUEST' => array(SS_SALES|12,  trans("Service Request")),
	'SA_SALESINVOICEDATEMOD' => [SS_SALES|13, trans("Transaction date modification when invoicing")],
	'SA_MANAGEINV'			 => [SS_SALES|14, trans("Manage Invoices")],
	'SA_EDITSALESINVOICE'	 => [SS_SALES|15, trans("Edit Sales Invoices")],
	'SA_MANAGEINVDEP'	 	 => [SS_SALES|16, trans("Manage Invoices (Department)")],
	'SA_MANAGEINVALL'	 	 =>	[SS_SALES|17, trans("Manage Invoices (All)")],

	'SA_SALESANALYTIC' 	=> array(SS_SALES_A|1, trans("Sales analytical reports")),
	'SA_SALESBULKREP' 	=> array(SS_SALES_A|2, trans("Sales document bulk reports")),
	'SA_PRICEREP' 		=> array(SS_SALES_A|3, trans("Sales prices listing")),
	'SA_SALESMANREP' 	=> array(SS_SALES_A|4, trans("Sales staff listing")),
	'SA_CUSTBULKREP' 	=> array(SS_SALES_A|5, trans("Customer bulk listing")),
	'SA_CUSTSTATREP' 	=> array(SS_SALES_A|6, trans("Customer status report")),
	'SA_CUSTPAYMREP' 	=> array(SS_SALES_A|7, trans("Customer payments report")),
	'SA_SRVREPORT'			=> [SS_SALES_A|8, trans("Service Report")],
	'SA_SRVREPORTALL' 		=> [SS_SALES_A|9, trans("Service Report of everybody"),],
	'SA_SRVREQLI' 			=> [SS_SALES_A|10, trans("Service Request List"),],
	'SA_CSHCOLLECTREP' 		=> [SS_SALES_A|11, trans("Cash Collection (Self Only)")],
	'SA_CSHCOLLECTREPALL' 	=> [SS_SALES_A|12, trans("Cash Collection (All)")],
	'SA_EMPANALYTIC'		=> [SS_SALES_A|13, trans("Employee Analytical Reports (Self Only)")],
	'SA_EMPANALYTICDEP'		=> [SS_SALES_A|14, trans("Employee Analytical Reports (Department)")],
	'SA_EMPANALYTICALL'		=> [SS_SALES_A|15, trans("Employee Analytical Reports (All)")],
	'SA_CUSTANALYTIC'		=> [SS_SALES_A|16, trans("Customer Analytical Reports")],
	'SA_CSHCOLLECTREPDEP'	=> [SS_SALES_A|17, trans("Cash Collection (Department)")],
	'SA_EMPCOMMAAD'			=> [SS_SALES_A|18, trans("Employee Commission Adheed (Self Only)")],
	'SA_EMPCOMMAADDEP'		=> [SS_SALES_A|19, trans("Employee Commission Adheed (Department)")],
	'SA_EMPCOMMAADALL'		=> [SS_SALES_A|20, trans("Employee Commission Adheed (All)")],
	
	//
	// Purchase related functions
	//
	'SA_PURCHASEPRICING' => array(SS_PURCH_C|1, trans("Purchase price changes")),

	'SA_SUPPTRANSVIEW' => array(SS_PURCH|1, trans("Supplier transactions view")),
	'SA_SUPPLIER' => array(SS_PURCH|2, trans("Suppliers changes")),
	'SA_PURCHASEORDER' => array(SS_PURCH|3, trans("Purchase order entry")),
	'SA_GRN' => array(SS_PURCH|4, trans("Purchase receive")),
	'SA_SUPPLIERINVOICE' => array(SS_PURCH|5, trans("Supplier invoices")),
	'SA_GRNDELETE' => array(SS_PURCH|9, trans("Deleting GRN items during invoice entry")),
	'SA_SUPPLIERCREDIT' => array(SS_PURCH|6, trans("Supplier credit notes")),
	'SA_SUPPLIERPAYMNT' => array(SS_PURCH|7, trans("Supplier payments")),
	'SA_SUPPLIERALLOC' => array(SS_PURCH|8, trans("Supplier payments allocations")),

	'SA_SUPPLIERANALYTIC' => array(SS_PURCH_A|1, trans("Supplier analytical reports")),
	'SA_SUPPBULKREP' => array(SS_PURCH_A|2, trans("Supplier document bulk reports")),
	'SA_SUPPPAYMREP' => array(SS_PURCH_A|3, trans("Supplier payments report")),

	//
	// Inventory 
	//
	'SA_ITEM' => array(SS_ITEMS_C|1, trans("Stock items add/edit")),
	'SA_SALESKIT' => array(SS_ITEMS_C|2, trans("Sales kits")),
	'SA_ITEMCATEGORY' => array(SS_ITEMS_C|3, trans("Item categories")),
	'SA_UOM' => array(SS_ITEMS_C|4, trans("Units of measure")),

	'SA_ITEMSSTATVIEW' => array(SS_ITEMS|1, trans("Stock status view")),
	'SA_ITEMSTRANSVIEW' => array(SS_ITEMS|2, trans("Stock transactions view")),
	'SA_FORITEMCODE' => array(SS_ITEMS|3, trans("Foreign item codes entry")),
	'SA_LOCATIONTRANSFER' => array(SS_ITEMS|4, trans("Inventory location transfers")),
	'SA_INVENTORYADJUSTMENT' => array(SS_ITEMS|5, trans("Inventory adjustments")),

	'SA_REORDER' => array(SS_ITEMS_A|1, trans("Reorder levels")),
	'SA_ITEMSANALYTIC' => array(SS_ITEMS_A|2, trans("Items analytical reports and inquiries")),
	'SA_ITEMSVALREP' => array(SS_ITEMS_A|3, trans("Inventory valuation report")),

	//
	// Fixed Assets
	//
	'SA_ASSET' => array(SS_ASSETS_C|1, trans("Fixed Asset items add/edit")),
	'SA_ASSETCATEGORY' => array(SS_ASSETS_C|2, trans("Fixed Asset categories")),
	'SA_ASSETCLASS' => array(SS_ASSETS_C|4, trans("Fixed Asset classes")),

	'SA_ASSETSTRANSVIEW' => array(SS_ASSETS|1, trans("Fixed Asset transactions view")),
	'SA_ASSETTRANSFER' => array(SS_ASSETS|2, trans("Fixed Asset location transfers")),
	'SA_ASSETDISPOSAL' => array(SS_ASSETS|3, trans("Fixed Asset disposals")),
    'SA_DEPRECIATION' => array(SS_ASSETS|4, trans("Depreciation")),

	'SA_ASSETSANALYTIC' => array(SS_ASSETS_A|1, trans("Fixed Asset analytical reports and inquiries")),

	//
	// Manufacturing module 
	//
	'SA_BOM' => array(SS_MANUF_C|1, trans("Bill of Materials")),

	'SA_MANUFTRANSVIEW' => array(SS_MANUF|1, trans("Manufacturing operations view")),
	'SA_WORKORDERENTRY' => array(SS_MANUF|2, trans("Work order entry")),
	'SA_MANUFISSUE' => array(SS_MANUF|3, trans("Material issues entry")),
	'SA_MANUFRECEIVE' => array(SS_MANUF|4, trans("Final product receive")),
	'SA_MANUFRELEASE' => array(SS_MANUF|5, trans("Work order releases")),

	'SA_WORKORDERANALYTIC' => array(SS_MANUF_A|1, trans("Work order analytical reports and inquiries")),
	'SA_WORKORDERCOST' => array(SS_MANUF_A|2, trans("Manufacturing cost inquiry")),
	'SA_MANUFBULKREP' => array(SS_MANUF_A|3, trans("Work order bulk reports")),
	'SA_BOMREP' => array(SS_MANUF_A|4, trans("Bill of materials reports")),
	//
	// Dimensions
	//
	'SA_DIMTAGS' => array(SS_DIM_C|1, trans("Dimension tags")),

	'SA_DIMTRANSVIEW' => array(SS_DIM|1, trans("Dimension view")),

	'SA_DIMENSION' => array(SS_DIM|2, trans("Dimension entry")),

	'SA_DIMENSIONREP' => array(SS_DIM|3, trans("Dimension reports")),
	//
	// Banking and General Ledger
	//
	'SA_ITEMTAXTYPE' => array(SS_GL_C|1, trans("Item tax type definitions")),
	'SA_GLACCOUNT' => array(SS_GL_C|2, trans("GL accounts edition")),
	'SA_GLACCOUNTGROUP' => array(SS_GL_C|3, trans("GL account groups")),
	'SA_GLACCOUNTCLASS' => array(SS_GL_C|4, trans("GL account classes")),
	'SA_QUICKENTRY' => array(SS_GL_C|5, trans("Quick GL entry definitions")),
	'SA_CURRENCY' => array(SS_GL_C|6, trans("Currencies")),
	'SA_BANKACCOUNT' => array(SS_GL_C|7, trans("Bank accounts")),
	'SA_TAXRATES' => array(SS_GL_C|8, trans("Tax rates")),
	'SA_TAXGROUPS' => array(SS_GL_C|12, trans("Tax groups")),
	'SA_FISCALYEARS' => array(SS_GL_C|9, trans("Fiscal years maintenance")),
	'SA_GLSETUP' => array(SS_GL_C|10, trans("Company GL setup")),
	'SA_GLACCOUNTTAGS' => array(SS_GL_C|11, trans("GL Account tags")),
	'SA_GLCLOSE' => array(SS_GL_C|14, trans("Closing GL transactions")),
	'SA_GLREOPEN' => array(SS_GL_C|15, trans("Reopening GL transactions")), // see below
	'SA_MULTIFISCALYEARS' => array(SS_GL_C|13, trans("Allow entry on non closed Fiscal years")),

	'SA_BANKTRANSVIEW' => array(SS_GL|1, trans("Bank transactions view")),
	'SA_GLTRANSVIEW' => array(SS_GL|2, trans("GL postings view")),
	'SA_EXCHANGERATE' => array(SS_GL|3, trans("Exchange rate table changes")),
	'SA_PAYMENT' => array(SS_GL|4, trans("Bank payments")),
	'SA_DEPOSIT' => array(SS_GL|5, trans("Bank deposits")),
	'SA_BANKTRANSFER' => array(SS_GL|6, trans("Bank account transfers")),
	'SA_RECONCILE' => array(SS_GL|7, trans("Bank reconciliation")),
	'SA_JOURNALENTRY' => array(SS_GL|8, trans("Manual journal entries")),
	'SA_BANKJOURNAL' => array(SS_GL|11, trans("Journal entries to bank related accounts")),
	'SA_BUDGETENTRY' => array(SS_GL|9, trans("Budget edition")),
	'SA_STANDARDCOST' => array(SS_GL|10, trans("Item standard costs")),
	'SA_ACCRUALS' => array(SS_GL|12, trans("Revenue / Cost Accruals")),

	'SA_GLANALYTIC' => array(SS_GL_A|1, trans("GL analytical reports and inquiries")),
	'SA_TAXREP' => array(SS_GL_A|2, trans("Tax reports and inquiries")),
	'SA_BANKREP' => array(SS_GL_A|3, trans("Bank reports and inquiries")),
	'SA_GLREP' => array(SS_GL_A|4, trans("GL reports and inquiries")),


    'HRM_CUSTOM_REQUEST_LEAVE' =>array(SS_HRM_A|1, trans("Apply Employee Leave")),
    'HRM_LEAVE_APPROVE' =>array(SS_HRM_A|2, trans("Approve Leave")),
    'HRM_MANUVAL_ATTENDENCE' =>array(SS_HRM_A|3, trans("Manuval Attendence")),
    'HRM_SINGLE_ATTENDENCE' =>array(SS_HRM_A|4, trans("Single Click Attendence")),
    'HRM_UPLOAD_ATTENDENCE' =>array(SS_HRM_A|5, trans("Upload Attendence")),
    'HRM_TIMESHEET' =>array(SS_HRM_A|6, trans("Time Sheet")),
    'HRM_APPROVE_OVERTIME' =>array(SS_HRM_A|7, trans("Approve Overtime")),
    'HRM_LOAN' =>array(SS_HRM_A|1, trans("Loan Entry")),
    'HRM_PAYROLL_GENERATION' =>array(SS_HRM_A|8, trans("Payroll Generation")),
    'HRM_PAYROLL_PROCESS' =>array(SS_HRM_A|9, trans("Process Payroll")),
    'HRM_PAYMENT' =>array(SS_HRM_A|10, trans("Payment")),
    'HRM_EMPLOYEE_MANAGE' =>array(SS_HRM_A|11, trans("Add/Manage Employee")),
    'HRM_SETTINGS' =>array(SS_HRM_A|12, trans("Default Settings")),
    'HRM_EMP_DOCS' =>array(SS_HRM_A|13, trans("Manage Employee Documents")),
    'HRM_REQUEST_VERIFY' =>array(SS_HRM_A|14, trans("Verify Request")),
    'HRM_SHIFT_REPORT' =>array(SS_HRM_A|15, trans("Shift Report")),
    'HRM_PAYROLL_REPORT' =>array(SS_HRM_A|16, trans("Payroll Report")),
    'HRM_DEPARTMENT' =>array(SS_HRM_A|17, trans("DEPARTMENT MASTER")),
    'HRM_DIVISION' =>array(SS_HRM_A|18, trans("DIVISION MASTER")),
    'HRM_DESIGNATION' =>array(SS_HRM_A|19, trans("DESIGNATION MASTER")),
    'HRM_DOC_MASTER' =>array(SS_HRM_A|20, trans("DOCUMENT MASTER")),
    'HRM_LEAVE' =>array(SS_HRM_A|21, trans("LEAVE MASTER")),
    'HRM_PAY_ELEMENTS' =>array(SS_HRM_A|22, trans("Pay Elements")),
    'HRM_SHIFT' =>array(SS_HRM_A|23, trans("Shift Master")),
    'HRM_ASSIGN_SHIFT_EMPLOYEE' =>array(SS_HRM_A|24, trans("Asign Shift To Employee")),
    'HRM_LOAN_MASTER' =>array(SS_HRM_A|25, trans("Loan Master")),
    //'HRM_REQ_DOC' =>array(SS_HRM_A|26, trans("Request Document")),
    'HRM_EMPLOYEE_PROFILE' =>array(SS_HRM_A|27, trans("Employee Profile")),
    'HRM_ISSUE_WARNING' =>array(SS_HRM_A|28, trans("Issue Warning Letter")),
    'HRM_REQ_PASSPORT' =>array(SS_HRM_A|29, trans("Request Passport")),
    'HRM_REQ_CERTIFICATE' =>array(SS_HRM_A|30, trans("Request Certificate")),
    'HRM_REQ_NOC' =>array(SS_HRM_A|31, trans("Request NOC")),
    'HRM_EMP_REQUEST_LOAN'=>array(SS_HRM_A|32, trans("Request LOAN")),
    'HRM_REQ_ASSET'=>array(SS_HRM_A|33, trans("Request Asset")),
    'HRM_ASSETS'=>array(SS_HRM_A|34, trans("Manage Assets")),
    'HRM_ASSET_RETURN'=>array(SS_HRM_A|35, trans("Assets Return Request")),
    'HRM_REQUEST_FLOW'=>array(SS_HRM_A|36, trans("Request Flow Manage")),
    'HRM_END_OF_SERVICE'=>array(SS_HRM_A|37, trans("END Of Services")),
    'HRM_LIST_END_OF_SERVICE'=>array(SS_HRM_A|38, trans("List END Of Services")),
    'HRM_VERIFY_GL_ENTRIES'=>array(SS_HRM_A|39, trans("Verify & Pass GL Entries")),
    'HRM_INVOICE_DEDUCTION'=>array(SS_HRM_A|40, trans("Invoice Mistake Entry")),
    'HRM_HOLIDAYS_MANAGE'=>array(SS_HRM_A|41, trans("Manage Holidays")),
    'HRM_HOLIDAY_WRK_APPRVE'=>array(SS_HRM_A|42, trans("Holidays Work Approve")),
    'HRM_APPROVED_REQUESTS'=>array(SS_HRM_A|43, trans("List Approved Requests")),
    'HRM_SUBMITTED_REQUESTS'=>array(SS_HRM_A|44, trans("View Submitted Requests")),
    'HRM_SYNC_ATTENDANCE'=>array(SS_HRM_A|45, trans("Manually Sync Attendance")),
    'HRM_GPSSA_REPORT'=>array(SS_HRM_A|46, trans("GPSSA Report")),
    'HRM_EMERGENCY_REPORT'=>array(SS_HRM_A|47, trans("Emergency Contact Report")),
    'HRM_ESB_REPORT'=>array(SS_HRM_A|48, trans("ESB Report")),
    'HRM_REQUEST_SECTION'=>array(SS_HRM_A|49, trans("Display Request Tiles")),

	'SA_RECEPTION_REPORT' => array(SS_CRM_A|1, trans("View Customer reception report")),
	'SA_RECEPTION'	=> [SS_CRM_A|2, trans("Recieve Customer")],

	
     'HEAD_MENU_SALES'=>array(SS_HEAD_MENU|1, trans("Sales Menu")),
     'HEAD_MENU_PURCHASE'=>array(SS_HEAD_MENU|2, trans("Purchase Menu")),
     'HEAD_MENU_ASSET'=>array(SS_HEAD_MENU|3, trans("Fixed Asset Menu")),
     'HEAD_MENU_FINANCE'=>array(SS_HEAD_MENU|4, trans("Finance Menu")),
     'HEAD_MENU_HR_ADMIN'=>array(SS_HEAD_MENU|5, trans("Admin Menu")),
     'HEAD_MENU_REPORT'=>array(SS_HEAD_MENU|6, trans("Report Menu")),
     'HEAD_MENU_SETTINGS'=>array(SS_HEAD_MENU|7, trans("Settings Menu")),

	// Finance related
	'SA_CASH_HANDOVER_ALL' => array(SS_FINANCE|1, trans("Place cash handover request for all")),
	'SA_CASH_HANDOVER' => array(SS_FINANCE|2, trans("Place cash handover request")),
	'SA_CASH_HANDOVER_LIST' => array(SS_FINANCE|3, trans("See cash handover request list")),
	'SA_PRINT_REFUNDS' => array(SS_FINANCE|4, trans("Print customer refunds")),
    'SA_REFUND_TO_CUSTOMER' => array(SS_FINANCE|5, trans("Refund to customer")),

	/** Dashboard Sections */
	'SA_DSH_LAST_10_DAYS' 	=> [SS_DASHBOARD|1,		trans("Sales - Last 10 days")],
	'SA_DSH_TOP_5_EMP'		=> [SS_DASHBOARD|2,		trans("Top 5 Employees Service Count")],
	'SA_DSH_TOP_5'			=> [SS_DASHBOARD|3, 	trans("Top 5 Sales Category Count")],
	'SA_DSH_FIND_INV'		=> [SS_DASHBOARD|4, 	trans("Find Invoices")],
	'SA_DSH_TODAYS_INV'		=> [SS_DASHBOARD|5, 	trans("Today's Invoices")],
	'SA_DSH_CAT_REP'		=> [SS_DASHBOARD|6, 	trans("Today's Category Report")],
	'SA_DSH_TOP_10_CUST'	=> [SS_DASHBOARD|7, 	trans("Top 10 Customers")],
	'SA_DSH_TRANS'			=> [SS_DASHBOARD|8, 	trans("Todays Transaction")],
	'SA_DSH_TRANS_ACC'		=> [SS_DASHBOARD|9, 	trans("Accumulated Transaction")],
	'SA_DSH_BNK_AC'			=> [SS_DASHBOARD|10, 	trans("Bank Accounts")],
	'SA_DSH_COLL_BD'		=> [SS_DASHBOARD|11, 	trans("Total Collection Breakdown")],
	'SA_DSH_AC_CLOSING_BAL'	=> [SS_DASHBOARD|12, 	trans("Account Closing Balance")],
	'SA_DSH_HRM'			=> [SS_DASHBOARD|13,	trans("HRM Dashboard")],
	'SA_DHS_CUST_BAL'		=> [SS_DASHBOARD|14,	trans("Customer Balances")]
);

if (!@$SysPrefs->allow_gl_reopen)
	unset($security_areas['SA_GLREOPEN']);
/*
	This function should be called whenever we want to extend core access level system
	with new security areas and/or sections i.e.: 
	. on any page with non-standard security areas
	. in security roles editor
	The call should be placed between session.inc inclusion and page() call.
	Up to 155 security sections and 155 security areas for any extension can be installed.
*/
function add_access_extensions()
{
	global $security_areas, $security_sections, $installed_extensions;

	foreach($installed_extensions as $extid => $ext) {
		$accext = hook_invoke($ext['package'], 'install_access', $dummy);
		if ($accext == null) continue;

		$scode = 100;
		$acode = 100;
		$extsections = $accext[1];
		$extareas = $accext[0];
		$extcode = $extid<<16;
		
		$trans = array();
		foreach($extsections as $code =>$name) {
			$trans[$code] = $scode<<8;
			// reassign section codes
			$security_sections[$trans[$code]|$extcode] = $name;
			$scode++;
		}
		foreach($extareas as $code => $area) {
			$section = $area[0]&0xff00;
			// extension modules:
			// if area belongs to nonstandard section
			// use translated section codes and
			// preserve lower part of area code
			if (isset($trans[$section])) {
				$section = $trans[$section];
			} 
				// otherwise assign next available
				// area code >99

//            display_error(print_r($section ,true));

			$area[0] = $extcode | $section | ($acode++);
			$security_areas[$code] = $area;
		}
	}
}

function check_edit_access($name)
{
	global $input_security;

	$access = @$input_security[$name];

	if (!$access)
		$access = @$input_security['']; // default access level

	if (!$access)
		return true; // if constraint is not defined edit access is allowed

	return  user_check_access($access);
}
/*
	Returns POST value or null if edit access to $name control is forbidden.
*/
function access_post($name, $dflt=null)
{
	if (!check_edit_access($name))
		return $dflt;
	else
		return get_post($name, $dflt);
}

/*
	Returns numeric input value or null if edit access to $name control is forbidden.
*/
function access_num($name, $dflt=null)
{
	if (!check_edit_access($name))
		return $dflt;
	else
		return input_num($name, $dflt);
}
