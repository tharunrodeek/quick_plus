<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg14.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql14.php") ?>
<?php include_once "phpfn14.php" ?>
<?php include_once "daily_report_viewinfo.php" ?>
<?php include_once "_0_usersinfo.php" ?>
<?php include_once "userfn14.php" ?>
<?php

//
// Page class
//

$daily_report_view_list = NULL; // Initialize page object first

class cdaily_report_view_list extends cdaily_report_view {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = '{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}';

	// Table name
	var $TableName = 'daily_report_view';

	// Page object name
	var $PageObjName = 'daily_report_view_list';

	// Grid form hidden field names
	var $FormName = 'fdaily_report_viewlist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

	// Page headings
	var $Heading = '';
	var $Subheading = '';

	// Page heading
	function PageHeading() {
		global $Language;
		if ($this->Heading <> "")
			return $this->Heading;
		if (method_exists($this, "TableCaption"))
			return $this->TableCaption();
		return "";
	}

	// Page subheading
	function PageSubheading() {
		global $Language;
		if ($this->Subheading <> "")
			return $this->Subheading;
		if ($this->TableName)
			return $Language->Phrase($this->PageID);
		return "";
	}

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Custom export
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Methods to clear message
	function ClearMessage() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
	}

	function ClearFailureMessage() {
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
	}

	function ClearSuccessMessage() {
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
	}

	function ClearWarningMessage() {
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	function ClearMessages() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $TokenTimeout = 0;
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME], $this->TokenTimeout);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		global $UserTable, $UserTableConn;
		$GLOBALS["Page"] = &$this;
		$this->TokenTimeout = ew_SessionTimeoutTime();

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (daily_report_view)
		if (!isset($GLOBALS["daily_report_view"]) || get_class($GLOBALS["daily_report_view"]) == "cdaily_report_view") {
			$GLOBALS["daily_report_view"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["daily_report_view"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "daily_report_viewadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "daily_report_viewdelete.php";
		$this->MultiUpdateUrl = "daily_report_viewupdate.php";

		// Table object (_0_users)
		if (!isset($GLOBALS['_0_users'])) $GLOBALS['_0_users'] = new c_0_users();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'daily_report_view', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"]))
			$GLOBALS["gTimer"] = new cTimer();

		// Debug message
		ew_LoadDebugMsg();

		// Open connection
		if (!isset($conn))
			$conn = ew_Connect($this->DBID);

		// User table object (_0_users)
		if (!isset($UserTable)) {
			$UserTable = new c_0_users();
			$UserTableConn = Conn($UserTable->DBID);
		}

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "div";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";

		// Filter options
		$this->FilterOptions = new cListOptions();
		$this->FilterOptions->Tag = "div";
		$this->FilterOptions->TagClassName = "ewFilterOption fdaily_report_viewlistsrch";

		// List actions
		$this->ListActions = new cListActions();
	}

	//
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// User profile
		$UserProfile = new cUserProfile();

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if ($Security->IsLoggedIn()) $Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		if ($Security->IsLoggedIn()) $Security->TablePermission_Loaded();
		if (!$Security->CanList()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			$this->Page_Terminate(ew_GetUrl("index.php"));
		}

		// NOTE: Security object may be needed in other part of the script, skip set to Nothing
		// 
		// Security = null;
		// 
		// Get export parameters

		$custom = "";
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
			$custom = @$_GET["custom"];
		} elseif (@$_POST["export"] <> "") {
			$this->Export = $_POST["export"];
			$custom = @$_POST["custom"];
		} elseif (ew_IsPost()) {
			if (@$_POST["exporttype"] <> "")
				$this->Export = $_POST["exporttype"];
			$custom = @$_POST["custom"];
		} elseif (@$_GET["cmd"] == "json") {
			$this->Export = $_GET["cmd"];
		} else {
			$this->setExportReturnUrl(ew_CurrentUrl());
		}
		$gsExportFile = $this->TableVar; // Get export file, used in header

		// Get custom export parameters
		if ($this->Export <> "" && $custom <> "") {
			$this->CustomExport = $this->Export;
			$this->Export = "print";
		}
		$gsCustomExport = $this->CustomExport;
		$gsExport = $this->Export; // Get export parameter, used in header

		// Update Export URLs
		if (defined("EW_USE_PHPEXCEL"))
			$this->ExportExcelCustom = FALSE;
		if ($this->ExportExcelCustom)
			$this->ExportExcelUrl .= "&amp;custom=1";
		if (defined("EW_USE_PHPWORD"))
			$this->ExportWordCustom = FALSE;
		if ($this->ExportWordCustom)
			$this->ExportWordUrl .= "&amp;custom=1";
		if ($this->ExportPdfCustom)
			$this->ExportPdfUrl .= "&amp;custom=1";
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

		// Setup export options
		$this->SetupExportOptions();
		$this->invoice_count->SetVisibility();
		$this->tran_date->SetVisibility();
		$this->total_service_count->SetVisibility();
		$this->total_invoice_amount->SetVisibility();
		$this->total_amount_recieved->SetVisibility();
		$this->pending_amount->SetVisibility();
		$this->total_service_charge->SetVisibility();
		$this->total_commission->SetVisibility();
		$this->total_collection->SetVisibility();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
		}

		// Create Token
		$this->CreateToken();

		// Setup other options
		$this->SetupOtherOptions();

		// Set up custom action (compatible with old version)
		foreach ($this->CustomActions as $name => $action)
			$this->ListActions->Add($name, $action);

		// Show checkbox column if multiple action
		foreach ($this->ListActions->Items as $listaction) {
			if ($listaction->Select == EW_ACTION_MULTIPLE && $listaction->Allow) {
				$this->ListOptions->Items["checkbox"]->Visible = TRUE;
				break;
			}
		}
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $gsExportFile, $gTmpImages;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $daily_report_view;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($daily_report_view);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

		// Close connection
		ew_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			ew_SaveDebugMsg();
			header("Location: " . $url);
		}
		exit();
	}

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $OtherOptions = array(); // Other options
	var $FilterOptions; // Filter options
	var $ListActions; // List actions
	var $SelectedCount = 0;
	var $SelectedIndex = 0;
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $AutoHidePager = EW_AUTO_HIDE_PAGER;
	var $AutoHidePageSizeSelector = EW_AUTO_HIDE_PAGE_SIZE_SELECTOR;
	var $DefaultSearchWhere = ""; // Default search WHERE clause
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $MultiColumnClass;
	var $MultiColumnEditClass = "col-sm-12";
	var $MultiColumnCnt = 12;
	var $MultiColumnEditCnt = 12;
	var $GridCnt = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $DetailPages;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security, $EW_EXPORT;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process list action first
			if ($this->ProcessListAction()) // Ajax request
				$this->Page_Terminate();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			if ($this->Export == "")
				$this->SetupBreadcrumb();

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide options
			if ($this->Export <> "" || $this->CurrentAction <> "") {
				$this->ExportOptions->HideAllOptions();
				$this->FilterOptions->HideAllOptions();
			}

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Set up sorting order
			$this->SetupSortOrder();
		}

		// Restore display records
		if ($this->Command <> "json" && $this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		if ($this->Command <> "json")
			$this->LoadSortOrder();

		// Build filter
		$sFilter = "";
		if (!$Security->CanList())
			$sFilter = "(0=1)"; // Filter all records
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter
		if ($this->Command == "json") {
			$this->UseSessionForListSQL = FALSE; // Do not use session for ListSQL
			$this->CurrentFilter = $sFilter;
		} else {
			$this->setSessionWhere($sFilter);
			$this->CurrentFilter = "";
		}

		// Export data only
		if ($this->CustomExport == "" && in_array($this->Export, array_keys($EW_EXPORT))) {
			$this->ExportData();
			$this->Page_Terminate(); // Terminate response
			exit();
		}

		// Load record count first
		if (!$this->IsAddOrEdit()) {
			$bSelectLimit = $this->UseSelectLimit;
			if ($bSelectLimit) {
				$this->TotalRecs = $this->ListRecordCount();
			} else {
				if ($this->Recordset = $this->LoadRecordset())
					$this->TotalRecs = $this->Recordset->RecordCount();
			}
		}

		// Search options
		$this->SetupSearchOptions();
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 0) {
		}
		return TRUE;
	}

	// Set up sort parameters
	function SetupSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = @$_GET["order"];
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->invoice_count); // invoice_count
			$this->UpdateSort($this->tran_date); // tran_date
			$this->UpdateSort($this->total_service_count); // total_service_count
			$this->UpdateSort($this->total_invoice_amount); // total_invoice_amount
			$this->UpdateSort($this->total_amount_recieved); // total_amount_recieved
			$this->UpdateSort($this->pending_amount); // pending_amount
			$this->UpdateSort($this->total_service_charge); // total_service_charge
			$this->UpdateSort($this->total_commission); // total_commission
			$this->UpdateSort($this->total_collection); // total_collection
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->getSqlOrderBy() <> "") {
				$sOrderBy = $this->getSqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->invoice_count->setSort("");
				$this->tran_date->setSort("");
				$this->total_service_count->setSort("");
				$this->total_invoice_amount->setSort("");
				$this->total_amount_recieved->setSort("");
				$this->pending_amount->setSort("");
				$this->total_service_charge->setSort("");
				$this->total_commission->setSort("");
				$this->total_collection->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = FALSE;
		$item->Visible = FALSE;

		// List actions
		$item = &$this->ListOptions->Add("listactions");
		$item->CssClass = "text-nowrap";
		$item->OnLeft = FALSE;
		$item->Visible = FALSE;
		$item->ShowInButtonGroup = FALSE;
		$item->ShowInDropDown = FALSE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = FALSE;
		$item->OnLeft = FALSE;
		$item->Header = "<input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\">";
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseImageAndText = TRUE;
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		if ($this->ListOptions->UseButtonGroup && ew_IsMobile())
			$this->ListOptions->UseDropDownButton = TRUE;
		$this->ListOptions->ButtonClass = "btn-sm"; // Class for button group

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$this->SetupListOptionsExt();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// Call ListOptions_Rendering event
		$this->ListOptions_Rendering();

		// Set up list action buttons
		$oListOpt = &$this->ListOptions->GetItem("listactions");
		if ($oListOpt && $this->Export == "" && $this->CurrentAction == "") {
			$body = "";
			$links = array();
			foreach ($this->ListActions->Items as $listaction) {
				if ($listaction->Select == EW_ACTION_SINGLE && $listaction->Allow) {
					$action = $listaction->Action;
					$caption = $listaction->Caption;
					$icon = ($listaction->Icon <> "") ? "<span class=\"" . ew_HtmlEncode(str_replace(" ewIcon", "", $listaction->Icon)) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\"></span> " : "";
					$links[] = "<li><a class=\"ewAction ewListAction\" data-action=\"" . ew_HtmlEncode($action) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({key:" . $this->KeyToJson() . "}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . $listaction->Caption . "</a></li>";
					if (count($links) == 1) // Single button
						$body = "<a class=\"ewAction ewListAction\" data-action=\"" . ew_HtmlEncode($action) . "\" title=\"" . ew_HtmlTitle($caption) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({key:" . $this->KeyToJson() . "}," . $listaction->ToJson(TRUE) . "));return false;\">" . $Language->Phrase("ListActionButton") . "</a>";
				}
			}
			if (count($links) > 1) { // More than one buttons, use dropdown
				$body = "<button class=\"dropdown-toggle btn btn-default btn-sm ewActions\" title=\"" . ew_HtmlTitle($Language->Phrase("ListActionButton")) . "\" data-toggle=\"dropdown\">" . $Language->Phrase("ListActionButton") . "<b class=\"caret\"></b></button>";
				$content = "";
				foreach ($links as $link)
					$content .= "<li>" . $link . "</li>";
				$body .= "<ul class=\"dropdown-menu" . ($oListOpt->OnLeft ? "" : " dropdown-menu-right") . "\">". $content . "</ul>";
				$body = "<div class=\"btn-group\">" . $body . "</div>";
			}
			if (count($links) > 0) {
				$oListOpt->Body = $body;
				$oListOpt->Visible = TRUE;
			}
		}

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["action"];

		// Set up options default
		foreach ($options as &$option) {
			$option->UseImageAndText = TRUE;
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-sm"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fdaily_report_viewlistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = FALSE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fdaily_report_viewlistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
		$item->Visible = FALSE;
		$this->FilterOptions->UseDropDownButton = TRUE;
		$this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton;
		$this->FilterOptions->DropDownButtonPhrase = $Language->Phrase("Filters");

		// Add group option item
		$item = &$this->FilterOptions->Add($this->FilterOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
			$option = &$options["action"];

			// Set up list action buttons
			foreach ($this->ListActions->Items as $listaction) {
				if ($listaction->Select == EW_ACTION_MULTIPLE) {
					$item = &$option->Add("custom_" . $listaction->Action);
					$caption = $listaction->Caption;
					$icon = ($listaction->Icon <> "") ? "<span class=\"" . ew_HtmlEncode($listaction->Icon) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\"></span> " : $caption;
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fdaily_report_viewlist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
					$item->Visible = $listaction->Allow;
				}
			}

			// Hide grid edit and other options
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$option->HideAllOptions();
			}
	}

	// Process list action
	function ProcessListAction() {
		global $Language, $Security;
		$userlist = "";
		$user = "";
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {

			// Check permission first
			$ActionCaption = $UserAction;
			if (array_key_exists($UserAction, $this->ListActions->Items)) {
				$ActionCaption = $this->ListActions->Items[$UserAction]->Caption;
				if (!$this->ListActions->Items[$UserAction]->Allow) {
					$errmsg = str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionNotAllowed"));
					if (@$_POST["ajax"] == $UserAction) // Ajax
						echo "<p class=\"text-danger\">" . $errmsg . "</p>";
					else
						$this->setFailureMessage($errmsg);
					return FALSE;
				}
			}
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$this->CurrentAction = $UserAction;

			// Call row action event
			if ($rs && !$rs->EOF) {
				$conn->BeginTrans();
				$this->SelectedCount = $rs->RecordCount();
				$this->SelectedIndex = 0;
				while (!$rs->EOF) {
					$this->SelectedIndex++;
					$row = $rs->fields;
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
					$rs->MoveNext();
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionFailed")));
					}
				}
			}
			if ($rs)
				$rs->Close();
			$this->CurrentAction = ""; // Clear action
			if (@$_POST["ajax"] == $UserAction) { // Ajax
				if ($this->getSuccessMessage() <> "") {
					echo "<p class=\"text-success\">" . $this->getSuccessMessage() . "</p>";
					$this->ClearSuccessMessage(); // Clear message
				}
				if ($this->getFailureMessage() <> "") {
					echo "<p class=\"text-danger\">" . $this->getFailureMessage() . "</p>";
					$this->ClearFailureMessage(); // Clear message
				}
				return TRUE;
			}
		}
		return FALSE; // Not ajax request
	}

	// Set up search options
	function SetupSearchOptions() {
		global $Language;
		$this->SearchOptions = new cListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Button group for search
		$this->SearchOptions->UseDropDownButton = FALSE;
		$this->SearchOptions->UseImageAndText = TRUE;
		$this->SearchOptions->UseButtonGroup = TRUE;
		$this->SearchOptions->DropDownButtonPhrase = $Language->Phrase("ButtonSearch");

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide search options
		if ($this->Export <> "" || $this->CurrentAction <> "")
			$this->SearchOptions->HideAllOptions();
		global $Security;
		if (!$Security->CanSearch()) {
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}
	}

	function SetupListOptionsExt() {
		global $Security, $Language;
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
	}

	// Set up starting record parameters
	function SetupStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {

		// Load List page SQL
		$sSql = $this->ListSQL();
		$conn = &$this->Connection();

		// Load recordset
		$dbtype = ew_GetConnectionType($this->DBID);
		if ($this->UseSelectLimit) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			if ($dbtype == "MSSQL") {
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset, array("_hasOrderBy" => trim($this->getOrderBy()) || trim($this->getSessionOrderBy())));
			} else {
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset);
			}
			$conn->raiseErrorFn = '';
		} else {
			$rs = ew_LoadRecordset($sSql, $conn);
		}

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql, $conn);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues($rs = NULL) {
		if ($rs && !$rs->EOF)
			$row = $rs->fields;
		else
			$row = $this->NewRow(); 

		// Call Row Selected event
		$this->Row_Selected($row);
		if (!$rs || $rs->EOF)
			return;
		$this->invoice_count->setDbValue($row['invoice_count']);
		$this->tran_date->setDbValue($row['tran_date']);
		$this->total_service_count->setDbValue($row['total_service_count']);
		$this->total_invoice_amount->setDbValue($row['total_invoice_amount']);
		$this->total_amount_recieved->setDbValue($row['total_amount_recieved']);
		$this->pending_amount->setDbValue($row['pending_amount']);
		$this->total_service_charge->setDbValue($row['total_service_charge']);
		$this->total_commission->setDbValue($row['total_commission']);
		$this->total_collection->setDbValue($row['total_collection']);
	}

	// Return a row with default values
	function NewRow() {
		$row = array();
		$row['invoice_count'] = NULL;
		$row['tran_date'] = NULL;
		$row['total_service_count'] = NULL;
		$row['total_invoice_amount'] = NULL;
		$row['total_amount_recieved'] = NULL;
		$row['pending_amount'] = NULL;
		$row['total_service_charge'] = NULL;
		$row['total_commission'] = NULL;
		$row['total_collection'] = NULL;
		return $row;
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF)
			return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->invoice_count->DbValue = $row['invoice_count'];
		$this->tran_date->DbValue = $row['tran_date'];
		$this->total_service_count->DbValue = $row['total_service_count'];
		$this->total_invoice_amount->DbValue = $row['total_invoice_amount'];
		$this->total_amount_recieved->DbValue = $row['total_amount_recieved'];
		$this->pending_amount->DbValue = $row['pending_amount'];
		$this->total_service_charge->DbValue = $row['total_service_charge'];
		$this->total_commission->DbValue = $row['total_commission'];
		$this->total_collection->DbValue = $row['total_collection'];
	}

	// Load old record
	function LoadOldRecord() {
		return FALSE;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

		// Convert decimal values if posted back
		if ($this->total_invoice_amount->FormValue == $this->total_invoice_amount->CurrentValue && is_numeric(ew_StrToFloat($this->total_invoice_amount->CurrentValue)))
			$this->total_invoice_amount->CurrentValue = ew_StrToFloat($this->total_invoice_amount->CurrentValue);

		// Convert decimal values if posted back
		if ($this->total_amount_recieved->FormValue == $this->total_amount_recieved->CurrentValue && is_numeric(ew_StrToFloat($this->total_amount_recieved->CurrentValue)))
			$this->total_amount_recieved->CurrentValue = ew_StrToFloat($this->total_amount_recieved->CurrentValue);

		// Convert decimal values if posted back
		if ($this->pending_amount->FormValue == $this->pending_amount->CurrentValue && is_numeric(ew_StrToFloat($this->pending_amount->CurrentValue)))
			$this->pending_amount->CurrentValue = ew_StrToFloat($this->pending_amount->CurrentValue);

		// Convert decimal values if posted back
		if ($this->total_service_charge->FormValue == $this->total_service_charge->CurrentValue && is_numeric(ew_StrToFloat($this->total_service_charge->CurrentValue)))
			$this->total_service_charge->CurrentValue = ew_StrToFloat($this->total_service_charge->CurrentValue);

		// Convert decimal values if posted back
		if ($this->total_commission->FormValue == $this->total_commission->CurrentValue && is_numeric(ew_StrToFloat($this->total_commission->CurrentValue)))
			$this->total_commission->CurrentValue = ew_StrToFloat($this->total_commission->CurrentValue);

		// Convert decimal values if posted back
		if ($this->total_collection->FormValue == $this->total_collection->CurrentValue && is_numeric(ew_StrToFloat($this->total_collection->CurrentValue)))
			$this->total_collection->CurrentValue = ew_StrToFloat($this->total_collection->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// invoice_count
		// tran_date
		// total_service_count
		// total_invoice_amount
		// total_amount_recieved
		// pending_amount
		// total_service_charge
		// total_commission
		// total_collection

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// invoice_count
		$this->invoice_count->ViewValue = $this->invoice_count->CurrentValue;
		$this->invoice_count->ViewCustomAttributes = "";

		// tran_date
		$this->tran_date->ViewValue = $this->tran_date->CurrentValue;
		$this->tran_date->ViewValue = ew_FormatDateTime($this->tran_date->ViewValue, 0);
		$this->tran_date->ViewCustomAttributes = "";

		// total_service_count
		$this->total_service_count->ViewValue = $this->total_service_count->CurrentValue;
		$this->total_service_count->ViewCustomAttributes = "";

		// total_invoice_amount
		$this->total_invoice_amount->ViewValue = $this->total_invoice_amount->CurrentValue;
		$this->total_invoice_amount->ViewCustomAttributes = "";

		// total_amount_recieved
		$this->total_amount_recieved->ViewValue = $this->total_amount_recieved->CurrentValue;
		$this->total_amount_recieved->ViewCustomAttributes = "";

		// pending_amount
		$this->pending_amount->ViewValue = $this->pending_amount->CurrentValue;
		$this->pending_amount->ViewCustomAttributes = "";

		// total_service_charge
		$this->total_service_charge->ViewValue = $this->total_service_charge->CurrentValue;
		$this->total_service_charge->ViewCustomAttributes = "";

		// total_commission
		$this->total_commission->ViewValue = $this->total_commission->CurrentValue;
		$this->total_commission->ViewCustomAttributes = "";

		// total_collection
		$this->total_collection->ViewValue = $this->total_collection->CurrentValue;
		$this->total_collection->ViewCustomAttributes = "";

			// invoice_count
			$this->invoice_count->LinkCustomAttributes = "";
			$this->invoice_count->HrefValue = "";
			$this->invoice_count->TooltipValue = "";

			// tran_date
			$this->tran_date->LinkCustomAttributes = "";
			$this->tran_date->HrefValue = "";
			$this->tran_date->TooltipValue = "";

			// total_service_count
			$this->total_service_count->LinkCustomAttributes = "";
			$this->total_service_count->HrefValue = "";
			$this->total_service_count->TooltipValue = "";

			// total_invoice_amount
			$this->total_invoice_amount->LinkCustomAttributes = "";
			$this->total_invoice_amount->HrefValue = "";
			$this->total_invoice_amount->TooltipValue = "";

			// total_amount_recieved
			$this->total_amount_recieved->LinkCustomAttributes = "";
			$this->total_amount_recieved->HrefValue = "";
			$this->total_amount_recieved->TooltipValue = "";

			// pending_amount
			$this->pending_amount->LinkCustomAttributes = "";
			$this->pending_amount->HrefValue = "";
			$this->pending_amount->TooltipValue = "";

			// total_service_charge
			$this->total_service_charge->LinkCustomAttributes = "";
			$this->total_service_charge->HrefValue = "";
			$this->total_service_charge->TooltipValue = "";

			// total_commission
			$this->total_commission->LinkCustomAttributes = "";
			$this->total_commission->HrefValue = "";
			$this->total_commission->TooltipValue = "";

			// total_collection
			$this->total_collection->LinkCustomAttributes = "";
			$this->total_collection->HrefValue = "";
			$this->total_collection->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Set up export options
	function SetupExportOptions() {
		global $Language;

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a href=\"" . $this->ExportPrintUrl . "\" class=\"ewExportLink ewPrint\" title=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\">" . $Language->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\" class=\"ewExportLink ewExcel\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a href=\"" . $this->ExportWordUrl . "\" class=\"ewExportLink ewWord\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = FALSE;

		// Export to Html
		$item = &$this->ExportOptions->Add("html");
		$item->Body = "<a href=\"" . $this->ExportHtmlUrl . "\" class=\"ewExportLink ewHtml\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\">" . $Language->Phrase("ExportToHtml") . "</a>";
		$item->Visible = FALSE;

		// Export to Xml
		$item = &$this->ExportOptions->Add("xml");
		$item->Body = "<a href=\"" . $this->ExportXmlUrl . "\" class=\"ewExportLink ewXml\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\">" . $Language->Phrase("ExportToXml") . "</a>";
		$item->Visible = FALSE;

		// Export to Csv
		$item = &$this->ExportOptions->Add("csv");
		$item->Body = "<a href=\"" . $this->ExportCsvUrl . "\" class=\"ewExportLink ewCsv\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\">" . $Language->Phrase("ExportToCsv") . "</a>";
		$item->Visible = FALSE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a href=\"" . $this->ExportPdfUrl . "\" class=\"ewExportLink ewPdf\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\">" . $Language->Phrase("ExportToPDF") . "</a>";
		$item->Visible = TRUE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$url = "";
		$item->Body = "<button id=\"emf_daily_report_view\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_daily_report_view',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fdaily_report_viewlist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
		$item->Visible = TRUE;

		// Drop down button for export
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = TRUE;
		$this->ExportOptions->UseDropDownButton = FALSE;
		if ($this->ExportOptions->UseButtonGroup && ew_IsMobile())
			$this->ExportOptions->UseDropDownButton = TRUE;
		$this->ExportOptions->DropDownButtonPhrase = $Language->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = $this->UseSelectLimit;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->ListRecordCount();
		} else {
			if (!$this->Recordset)
				$this->Recordset = $this->LoadRecordset();
			$rs = &$this->Recordset;
			if ($rs)
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;

		// Export all
		if ($this->ExportAll) {
			set_time_limit(EW_EXPORT_ALL_TIME_LIMIT);
			$this->DisplayRecs = $this->TotalRecs;
			$this->StopRec = $this->TotalRecs;
		} else { // Export one page only
			$this->SetupStartRec(); // Set up start record position

			// Set the last record to display
			if ($this->DisplayRecs <= 0) {
				$this->StopRec = $this->TotalRecs;
			} else {
				$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
			}
		}
		if ($bSelectLimit)
			$rs = $this->LoadRecordset($this->StartRec-1, $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs);
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$this->ExportDoc = ew_ExportDocument($this, "h");
		$Doc = &$this->ExportDoc;
		if ($bSelectLimit) {
			$this->StartRec = 1;
			$this->StopRec = $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs;
		} else {

			//$this->StartRec = $this->StartRec;
			//$this->StopRec = $this->StopRec;

		}

		// Call Page Exporting server event
		$this->ExportDoc->ExportCustom = !$this->Page_Exporting();
		$ParentTable = "";
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		$Doc->Text .= $sHeader;
		$this->ExportDocument($Doc, $rs, $this->StartRec, $this->StopRec, "");
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		$Doc->Text .= $sFooter;

		// Close recordset
		$rs->Close();

		// Call Page Exported server event
		$this->Page_Exported();

		// Export header and footer
		$Doc->ExportHeaderAndFooter();

		// Clean output buffer
		if (!EW_DEBUG_ENABLED && ob_get_length())
			ob_end_clean();

		// Write debug message if enabled
		if (EW_DEBUG_ENABLED && $this->Export <> "pdf")
			echo ew_DebugMsg();

		// Output data
		if ($this->Export == "email") {
			echo $this->ExportEmail($Doc->Text);
		} else {
			$Doc->Export();
		}
	}

	// Export email
	function ExportEmail($EmailContent) {
		global $gTmpImages, $Language;
		$sSender = @$_POST["sender"];
		$sRecipient = @$_POST["recipient"];
		$sCc = @$_POST["cc"];
		$sBcc = @$_POST["bcc"];

		// Subject
		$sSubject = @$_POST["subject"];
		$sEmailSubject = $sSubject;

		// Message
		$sContent = @$_POST["message"];
		$sEmailMessage = $sContent;

		// Check sender
		if ($sSender == "") {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterSenderEmail") . "</p>";
		}
		if (!ew_CheckEmail($sSender)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperSenderEmail") . "</p>";
		}

		// Check recipient
		if (!ew_CheckEmailList($sRecipient, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperRecipientEmail") . "</p>";
		}

		// Check cc
		if (!ew_CheckEmailList($sCc, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperCcEmail") . "</p>";
		}

		// Check bcc
		if (!ew_CheckEmailList($sBcc, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperBccEmail") . "</p>";
		}

		// Check email sent count
		if (!isset($_SESSION[EW_EXPORT_EMAIL_COUNTER]))
			$_SESSION[EW_EXPORT_EMAIL_COUNTER] = 0;
		if (intval($_SESSION[EW_EXPORT_EMAIL_COUNTER]) > EW_MAX_EMAIL_SENT_COUNT) {
			return "<p class=\"text-danger\">" . $Language->Phrase("ExceedMaxEmailExport") . "</p>";
		}

		// Send email
		$Email = new cEmail();
		$Email->Sender = $sSender; // Sender
		$Email->Recipient = $sRecipient; // Recipient
		$Email->Cc = $sCc; // Cc
		$Email->Bcc = $sBcc; // Bcc
		$Email->Subject = $sEmailSubject; // Subject
		$Email->Format = "html";
		if ($sEmailMessage <> "")
			$sEmailMessage = ew_RemoveXSS($sEmailMessage) . "<br><br>";
		foreach ($gTmpImages as $tmpimage)
			$Email->AddEmbeddedImage($tmpimage);
		$Email->Content = $sEmailMessage . ew_CleanEmailContent($EmailContent); // Content
		$EventArgs = array();
		if ($this->Recordset) {
			$this->RecCnt = $this->StartRec - 1;
			$this->Recordset->MoveFirst();
			if ($this->StartRec > 1)
				$this->Recordset->Move($this->StartRec - 1);
			$EventArgs["rs"] = &$this->Recordset;
		}
		$bEmailSent = FALSE;
		if ($this->Email_Sending($Email, $EventArgs))
			$bEmailSent = $Email->Send();

		// Check email sent status
		if ($bEmailSent) {

			// Update email sent count
			$_SESSION[EW_EXPORT_EMAIL_COUNTER]++;

			// Sent email success
			return "<p class=\"text-success\">" . $Language->Phrase("SendEmailSuccess") . "</p>"; // Set up success message
		} else {

			// Sent email failure
			return "<p class=\"text-danger\">" . $Email->SendErrDescription . "</p>";
		}
	}

	// Export QueryString
	function ExportQueryString() {

		// Initialize
		$sQry = "export=html";

		// Build QueryString for search
		// Build QueryString for pager

		$sQry .= "&" . EW_TABLE_REC_PER_PAGE . "=" . urlencode($this->getRecordsPerPage()) . "&" . EW_TABLE_START_REC . "=" . urlencode($this->getStartRecordNumber());
		return $sQry;
	}

	// Add search QueryString
	function AddSearchQueryString(&$Qry, &$Fld) {
		$FldSearchValue = $Fld->AdvancedSearch->getValue("x");
		$FldParm = substr($Fld->FldVar,2);
		if (strval($FldSearchValue) <> "") {
			$Qry .= "&x_" . $FldParm . "=" . urlencode($FldSearchValue) .
				"&z_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("z"));
		}
		$FldSearchValue2 = $Fld->AdvancedSearch->getValue("y");
		if (strval($FldSearchValue2) <> "") {
			$Qry .= "&v_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("v")) .
				"&y_" . $FldParm . "=" . urlencode($FldSearchValue2) .
				"&w_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("w"));
		}
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", $this->TableVar, $url, "", $this->TableVar, TRUE);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		}
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendering event
	function ListOptions_Rendering() {

		//$GLOBALS["xxx_grid"]->DetailAdd = (...condition...); // Set to TRUE or FALSE conditionally
		//$GLOBALS["xxx_grid"]->DetailEdit = (...condition...); // Set to TRUE or FALSE conditionally
		//$GLOBALS["xxx_grid"]->DetailView = (...condition...); // Set to TRUE or FALSE conditionally

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example:
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
	}

	// Page Exporting event
	// $this->ExportDoc = export document object
	function Page_Exporting() {

		//$this->ExportDoc->Text = "my header"; // Export header
		//return FALSE; // Return FALSE to skip default export and use Row_Export event

		return TRUE; // Return TRUE to use default export and skip Row_Export event
	}

	// Row Export event
	// $this->ExportDoc = export document object
	function Row_Export($rs) {

		//$this->ExportDoc->Text .= "my content"; // Build HTML with field value: $rs["MyField"] or $this->MyField->ViewValue
	}

	// Page Exported event
	// $this->ExportDoc = export document object
	function Page_Exported() {

		//$this->ExportDoc->Text .= "my footer"; // Export footer
		//echo $this->ExportDoc->Text;

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($daily_report_view_list)) $daily_report_view_list = new cdaily_report_view_list();

// Page init
$daily_report_view_list->Page_Init();

// Page main
$daily_report_view_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$daily_report_view_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($daily_report_view->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fdaily_report_viewlist = new ew_Form("fdaily_report_viewlist", "list");
fdaily_report_viewlist.FormKeyCountName = '<?php echo $daily_report_view_list->FormKeyCountName ?>';

// Form_CustomValidate event
fdaily_report_viewlist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid.
 	return true;
 }

// Use JavaScript validation or not
fdaily_report_viewlist.ValidateRequired = <?php echo json_encode(EW_CLIENT_VALIDATE) ?>;

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($daily_report_view->Export == "") { ?>
<div class="ewToolbar">
<?php if ($daily_report_view_list->TotalRecs > 0 && $daily_report_view_list->ExportOptions->Visible()) { ?>
<?php $daily_report_view_list->ExportOptions->Render("body") ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
	$bSelectLimit = $daily_report_view_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($daily_report_view_list->TotalRecs <= 0)
			$daily_report_view_list->TotalRecs = $daily_report_view->ListRecordCount();
	} else {
		if (!$daily_report_view_list->Recordset && ($daily_report_view_list->Recordset = $daily_report_view_list->LoadRecordset()))
			$daily_report_view_list->TotalRecs = $daily_report_view_list->Recordset->RecordCount();
	}
	$daily_report_view_list->StartRec = 1;
	if ($daily_report_view_list->DisplayRecs <= 0 || ($daily_report_view->Export <> "" && $daily_report_view->ExportAll)) // Display all records
		$daily_report_view_list->DisplayRecs = $daily_report_view_list->TotalRecs;
	if (!($daily_report_view->Export <> "" && $daily_report_view->ExportAll))
		$daily_report_view_list->SetupStartRec(); // Set up start record position
	if ($bSelectLimit)
		$daily_report_view_list->Recordset = $daily_report_view_list->LoadRecordset($daily_report_view_list->StartRec-1, $daily_report_view_list->DisplayRecs);

	// Set no record found message
	if ($daily_report_view->CurrentAction == "" && $daily_report_view_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$daily_report_view_list->setWarningMessage(ew_DeniedMsg());
		if ($daily_report_view_list->SearchWhere == "0=101")
			$daily_report_view_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$daily_report_view_list->setWarningMessage($Language->Phrase("NoRecord"));
	}
$daily_report_view_list->RenderOtherOptions();
?>
<?php $daily_report_view_list->ShowPageHeader(); ?>
<?php
$daily_report_view_list->ShowMessage();
?>
<?php if ($daily_report_view_list->TotalRecs > 0 || $daily_report_view->CurrentAction <> "") { ?>
<div class="box ewBox ewGrid<?php if ($daily_report_view_list->IsAddOrEdit()) { ?> ewGridAddEdit<?php } ?> daily_report_view">
<form name="fdaily_report_viewlist" id="fdaily_report_viewlist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($daily_report_view_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $daily_report_view_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="daily_report_view">
<div id="gmp_daily_report_view" class="<?php if (ew_IsResponsiveLayout()) { ?>table-responsive <?php } ?>ewGridMiddlePanel">
<?php if ($daily_report_view_list->TotalRecs > 0 || $daily_report_view->CurrentAction == "gridedit") { ?>
<table id="tbl_daily_report_viewlist" class="table ewTable">
<thead>
	<tr class="ewTableHeader">
<?php

// Header row
$daily_report_view_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$daily_report_view_list->RenderListOptions();

// Render list options (header, left)
$daily_report_view_list->ListOptions->Render("header", "left");
?>
<?php if ($daily_report_view->invoice_count->Visible) { // invoice_count ?>
	<?php if ($daily_report_view->SortUrl($daily_report_view->invoice_count) == "") { ?>
		<th data-name="invoice_count" class="<?php echo $daily_report_view->invoice_count->HeaderCellClass() ?>"><div id="elh_daily_report_view_invoice_count" class="daily_report_view_invoice_count"><div class="ewTableHeaderCaption"><?php echo $daily_report_view->invoice_count->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="invoice_count" class="<?php echo $daily_report_view->invoice_count->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $daily_report_view->SortUrl($daily_report_view->invoice_count) ?>',1);"><div id="elh_daily_report_view_invoice_count" class="daily_report_view_invoice_count">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $daily_report_view->invoice_count->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($daily_report_view->invoice_count->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($daily_report_view->invoice_count->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($daily_report_view->tran_date->Visible) { // tran_date ?>
	<?php if ($daily_report_view->SortUrl($daily_report_view->tran_date) == "") { ?>
		<th data-name="tran_date" class="<?php echo $daily_report_view->tran_date->HeaderCellClass() ?>"><div id="elh_daily_report_view_tran_date" class="daily_report_view_tran_date"><div class="ewTableHeaderCaption"><?php echo $daily_report_view->tran_date->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="tran_date" class="<?php echo $daily_report_view->tran_date->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $daily_report_view->SortUrl($daily_report_view->tran_date) ?>',1);"><div id="elh_daily_report_view_tran_date" class="daily_report_view_tran_date">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $daily_report_view->tran_date->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($daily_report_view->tran_date->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($daily_report_view->tran_date->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($daily_report_view->total_service_count->Visible) { // total_service_count ?>
	<?php if ($daily_report_view->SortUrl($daily_report_view->total_service_count) == "") { ?>
		<th data-name="total_service_count" class="<?php echo $daily_report_view->total_service_count->HeaderCellClass() ?>"><div id="elh_daily_report_view_total_service_count" class="daily_report_view_total_service_count"><div class="ewTableHeaderCaption"><?php echo $daily_report_view->total_service_count->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="total_service_count" class="<?php echo $daily_report_view->total_service_count->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $daily_report_view->SortUrl($daily_report_view->total_service_count) ?>',1);"><div id="elh_daily_report_view_total_service_count" class="daily_report_view_total_service_count">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $daily_report_view->total_service_count->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($daily_report_view->total_service_count->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($daily_report_view->total_service_count->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($daily_report_view->total_invoice_amount->Visible) { // total_invoice_amount ?>
	<?php if ($daily_report_view->SortUrl($daily_report_view->total_invoice_amount) == "") { ?>
		<th data-name="total_invoice_amount" class="<?php echo $daily_report_view->total_invoice_amount->HeaderCellClass() ?>"><div id="elh_daily_report_view_total_invoice_amount" class="daily_report_view_total_invoice_amount"><div class="ewTableHeaderCaption"><?php echo $daily_report_view->total_invoice_amount->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="total_invoice_amount" class="<?php echo $daily_report_view->total_invoice_amount->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $daily_report_view->SortUrl($daily_report_view->total_invoice_amount) ?>',1);"><div id="elh_daily_report_view_total_invoice_amount" class="daily_report_view_total_invoice_amount">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $daily_report_view->total_invoice_amount->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($daily_report_view->total_invoice_amount->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($daily_report_view->total_invoice_amount->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($daily_report_view->total_amount_recieved->Visible) { // total_amount_recieved ?>
	<?php if ($daily_report_view->SortUrl($daily_report_view->total_amount_recieved) == "") { ?>
		<th data-name="total_amount_recieved" class="<?php echo $daily_report_view->total_amount_recieved->HeaderCellClass() ?>"><div id="elh_daily_report_view_total_amount_recieved" class="daily_report_view_total_amount_recieved"><div class="ewTableHeaderCaption"><?php echo $daily_report_view->total_amount_recieved->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="total_amount_recieved" class="<?php echo $daily_report_view->total_amount_recieved->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $daily_report_view->SortUrl($daily_report_view->total_amount_recieved) ?>',1);"><div id="elh_daily_report_view_total_amount_recieved" class="daily_report_view_total_amount_recieved">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $daily_report_view->total_amount_recieved->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($daily_report_view->total_amount_recieved->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($daily_report_view->total_amount_recieved->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($daily_report_view->pending_amount->Visible) { // pending_amount ?>
	<?php if ($daily_report_view->SortUrl($daily_report_view->pending_amount) == "") { ?>
		<th data-name="pending_amount" class="<?php echo $daily_report_view->pending_amount->HeaderCellClass() ?>"><div id="elh_daily_report_view_pending_amount" class="daily_report_view_pending_amount"><div class="ewTableHeaderCaption"><?php echo $daily_report_view->pending_amount->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="pending_amount" class="<?php echo $daily_report_view->pending_amount->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $daily_report_view->SortUrl($daily_report_view->pending_amount) ?>',1);"><div id="elh_daily_report_view_pending_amount" class="daily_report_view_pending_amount">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $daily_report_view->pending_amount->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($daily_report_view->pending_amount->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($daily_report_view->pending_amount->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($daily_report_view->total_service_charge->Visible) { // total_service_charge ?>
	<?php if ($daily_report_view->SortUrl($daily_report_view->total_service_charge) == "") { ?>
		<th data-name="total_service_charge" class="<?php echo $daily_report_view->total_service_charge->HeaderCellClass() ?>"><div id="elh_daily_report_view_total_service_charge" class="daily_report_view_total_service_charge"><div class="ewTableHeaderCaption"><?php echo $daily_report_view->total_service_charge->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="total_service_charge" class="<?php echo $daily_report_view->total_service_charge->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $daily_report_view->SortUrl($daily_report_view->total_service_charge) ?>',1);"><div id="elh_daily_report_view_total_service_charge" class="daily_report_view_total_service_charge">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $daily_report_view->total_service_charge->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($daily_report_view->total_service_charge->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($daily_report_view->total_service_charge->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($daily_report_view->total_commission->Visible) { // total_commission ?>
	<?php if ($daily_report_view->SortUrl($daily_report_view->total_commission) == "") { ?>
		<th data-name="total_commission" class="<?php echo $daily_report_view->total_commission->HeaderCellClass() ?>"><div id="elh_daily_report_view_total_commission" class="daily_report_view_total_commission"><div class="ewTableHeaderCaption"><?php echo $daily_report_view->total_commission->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="total_commission" class="<?php echo $daily_report_view->total_commission->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $daily_report_view->SortUrl($daily_report_view->total_commission) ?>',1);"><div id="elh_daily_report_view_total_commission" class="daily_report_view_total_commission">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $daily_report_view->total_commission->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($daily_report_view->total_commission->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($daily_report_view->total_commission->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($daily_report_view->total_collection->Visible) { // total_collection ?>
	<?php if ($daily_report_view->SortUrl($daily_report_view->total_collection) == "") { ?>
		<th data-name="total_collection" class="<?php echo $daily_report_view->total_collection->HeaderCellClass() ?>"><div id="elh_daily_report_view_total_collection" class="daily_report_view_total_collection"><div class="ewTableHeaderCaption"><?php echo $daily_report_view->total_collection->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="total_collection" class="<?php echo $daily_report_view->total_collection->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $daily_report_view->SortUrl($daily_report_view->total_collection) ?>',1);"><div id="elh_daily_report_view_total_collection" class="daily_report_view_total_collection">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $daily_report_view->total_collection->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($daily_report_view->total_collection->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($daily_report_view->total_collection->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php

// Render list options (header, right)
$daily_report_view_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($daily_report_view->ExportAll && $daily_report_view->Export <> "") {
	$daily_report_view_list->StopRec = $daily_report_view_list->TotalRecs;
} else {

	// Set the last record to display
	if ($daily_report_view_list->TotalRecs > $daily_report_view_list->StartRec + $daily_report_view_list->DisplayRecs - 1)
		$daily_report_view_list->StopRec = $daily_report_view_list->StartRec + $daily_report_view_list->DisplayRecs - 1;
	else
		$daily_report_view_list->StopRec = $daily_report_view_list->TotalRecs;
}
$daily_report_view_list->RecCnt = $daily_report_view_list->StartRec - 1;
if ($daily_report_view_list->Recordset && !$daily_report_view_list->Recordset->EOF) {
	$daily_report_view_list->Recordset->MoveFirst();
	$bSelectLimit = $daily_report_view_list->UseSelectLimit;
	if (!$bSelectLimit && $daily_report_view_list->StartRec > 1)
		$daily_report_view_list->Recordset->Move($daily_report_view_list->StartRec - 1);
} elseif (!$daily_report_view->AllowAddDeleteRow && $daily_report_view_list->StopRec == 0) {
	$daily_report_view_list->StopRec = $daily_report_view->GridAddRowCount;
}

// Initialize aggregate
$daily_report_view->RowType = EW_ROWTYPE_AGGREGATEINIT;
$daily_report_view->ResetAttrs();
$daily_report_view_list->RenderRow();
while ($daily_report_view_list->RecCnt < $daily_report_view_list->StopRec) {
	$daily_report_view_list->RecCnt++;
	if (intval($daily_report_view_list->RecCnt) >= intval($daily_report_view_list->StartRec)) {
		$daily_report_view_list->RowCnt++;

		// Set up key count
		$daily_report_view_list->KeyCount = $daily_report_view_list->RowIndex;

		// Init row class and style
		$daily_report_view->ResetAttrs();
		$daily_report_view->CssClass = "";
		if ($daily_report_view->CurrentAction == "gridadd") {
		} else {
			$daily_report_view_list->LoadRowValues($daily_report_view_list->Recordset); // Load row values
		}
		$daily_report_view->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$daily_report_view->RowAttrs = array_merge($daily_report_view->RowAttrs, array('data-rowindex'=>$daily_report_view_list->RowCnt, 'id'=>'r' . $daily_report_view_list->RowCnt . '_daily_report_view', 'data-rowtype'=>$daily_report_view->RowType));

		// Render row
		$daily_report_view_list->RenderRow();

		// Render list options
		$daily_report_view_list->RenderListOptions();
?>
	<tr<?php echo $daily_report_view->RowAttributes() ?>>
<?php

// Render list options (body, left)
$daily_report_view_list->ListOptions->Render("body", "left", $daily_report_view_list->RowCnt);
?>
	<?php if ($daily_report_view->invoice_count->Visible) { // invoice_count ?>
		<td data-name="invoice_count"<?php echo $daily_report_view->invoice_count->CellAttributes() ?>>
<span id="el<?php echo $daily_report_view_list->RowCnt ?>_daily_report_view_invoice_count" class="daily_report_view_invoice_count">
<span<?php echo $daily_report_view->invoice_count->ViewAttributes() ?>>
<?php echo $daily_report_view->invoice_count->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($daily_report_view->tran_date->Visible) { // tran_date ?>
		<td data-name="tran_date"<?php echo $daily_report_view->tran_date->CellAttributes() ?>>
<span id="el<?php echo $daily_report_view_list->RowCnt ?>_daily_report_view_tran_date" class="daily_report_view_tran_date">
<span<?php echo $daily_report_view->tran_date->ViewAttributes() ?>>
<?php echo $daily_report_view->tran_date->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($daily_report_view->total_service_count->Visible) { // total_service_count ?>
		<td data-name="total_service_count"<?php echo $daily_report_view->total_service_count->CellAttributes() ?>>
<span id="el<?php echo $daily_report_view_list->RowCnt ?>_daily_report_view_total_service_count" class="daily_report_view_total_service_count">
<span<?php echo $daily_report_view->total_service_count->ViewAttributes() ?>>
<?php echo $daily_report_view->total_service_count->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($daily_report_view->total_invoice_amount->Visible) { // total_invoice_amount ?>
		<td data-name="total_invoice_amount"<?php echo $daily_report_view->total_invoice_amount->CellAttributes() ?>>
<span id="el<?php echo $daily_report_view_list->RowCnt ?>_daily_report_view_total_invoice_amount" class="daily_report_view_total_invoice_amount">
<span<?php echo $daily_report_view->total_invoice_amount->ViewAttributes() ?>>
<?php echo $daily_report_view->total_invoice_amount->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($daily_report_view->total_amount_recieved->Visible) { // total_amount_recieved ?>
		<td data-name="total_amount_recieved"<?php echo $daily_report_view->total_amount_recieved->CellAttributes() ?>>
<span id="el<?php echo $daily_report_view_list->RowCnt ?>_daily_report_view_total_amount_recieved" class="daily_report_view_total_amount_recieved">
<span<?php echo $daily_report_view->total_amount_recieved->ViewAttributes() ?>>
<?php echo $daily_report_view->total_amount_recieved->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($daily_report_view->pending_amount->Visible) { // pending_amount ?>
		<td data-name="pending_amount"<?php echo $daily_report_view->pending_amount->CellAttributes() ?>>
<span id="el<?php echo $daily_report_view_list->RowCnt ?>_daily_report_view_pending_amount" class="daily_report_view_pending_amount">
<span<?php echo $daily_report_view->pending_amount->ViewAttributes() ?>>
<?php echo $daily_report_view->pending_amount->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($daily_report_view->total_service_charge->Visible) { // total_service_charge ?>
		<td data-name="total_service_charge"<?php echo $daily_report_view->total_service_charge->CellAttributes() ?>>
<span id="el<?php echo $daily_report_view_list->RowCnt ?>_daily_report_view_total_service_charge" class="daily_report_view_total_service_charge">
<span<?php echo $daily_report_view->total_service_charge->ViewAttributes() ?>>
<?php echo $daily_report_view->total_service_charge->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($daily_report_view->total_commission->Visible) { // total_commission ?>
		<td data-name="total_commission"<?php echo $daily_report_view->total_commission->CellAttributes() ?>>
<span id="el<?php echo $daily_report_view_list->RowCnt ?>_daily_report_view_total_commission" class="daily_report_view_total_commission">
<span<?php echo $daily_report_view->total_commission->ViewAttributes() ?>>
<?php echo $daily_report_view->total_commission->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($daily_report_view->total_collection->Visible) { // total_collection ?>
		<td data-name="total_collection"<?php echo $daily_report_view->total_collection->CellAttributes() ?>>
<span id="el<?php echo $daily_report_view_list->RowCnt ?>_daily_report_view_total_collection" class="daily_report_view_total_collection">
<span<?php echo $daily_report_view->total_collection->ViewAttributes() ?>>
<?php echo $daily_report_view->total_collection->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$daily_report_view_list->ListOptions->Render("body", "right", $daily_report_view_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($daily_report_view->CurrentAction <> "gridadd")
		$daily_report_view_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($daily_report_view->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($daily_report_view_list->Recordset)
	$daily_report_view_list->Recordset->Close();
?>
<?php if ($daily_report_view->Export == "") { ?>
<div class="box-footer ewGridLowerPanel">
<?php if ($daily_report_view->CurrentAction <> "gridadd" && $daily_report_view->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($daily_report_view_list->Pager)) $daily_report_view_list->Pager = new cPrevNextPager($daily_report_view_list->StartRec, $daily_report_view_list->DisplayRecs, $daily_report_view_list->TotalRecs, $daily_report_view_list->AutoHidePager) ?>
<?php if ($daily_report_view_list->Pager->RecordCount > 0 && $daily_report_view_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($daily_report_view_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $daily_report_view_list->PageUrl() ?>start=<?php echo $daily_report_view_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($daily_report_view_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $daily_report_view_list->PageUrl() ?>start=<?php echo $daily_report_view_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $daily_report_view_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($daily_report_view_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $daily_report_view_list->PageUrl() ?>start=<?php echo $daily_report_view_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($daily_report_view_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $daily_report_view_list->PageUrl() ?>start=<?php echo $daily_report_view_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $daily_report_view_list->Pager->PageCount ?></span>
</div>
<?php } ?>
<?php if ($daily_report_view_list->Pager->RecordCount > 0) { ?>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $daily_report_view_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $daily_report_view_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $daily_report_view_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($daily_report_view_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($daily_report_view_list->TotalRecs == 0 && $daily_report_view->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($daily_report_view_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($daily_report_view->Export == "") { ?>
<script type="text/javascript">
fdaily_report_viewlist.Init();
</script>
<?php } ?>
<?php
$daily_report_view_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($daily_report_view->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$daily_report_view_list->Page_Terminate();
?>
