<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg14.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql14.php") ?>
<?php include_once "phpfn14.php" ?>
<?php include_once "invoice_payment_reportinfo.php" ?>
<?php include_once "_0_usersinfo.php" ?>
<?php include_once "userfn14.php" ?>
<?php

//
// Page class
//

$invoice_payment_report_list = NULL; // Initialize page object first

class cinvoice_payment_report_list extends cinvoice_payment_report {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = '{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}';

	// Table name
	var $TableName = 'invoice_payment_report';

	// Page object name
	var $PageObjName = 'invoice_payment_report_list';

	// Grid form hidden field names
	var $FormName = 'finvoice_payment_reportlist';
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

		// Table object (invoice_payment_report)
		if (!isset($GLOBALS["invoice_payment_report"]) || get_class($GLOBALS["invoice_payment_report"]) == "cinvoice_payment_report") {
			$GLOBALS["invoice_payment_report"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["invoice_payment_report"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "invoice_payment_reportadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "invoice_payment_reportdelete.php";
		$this->MultiUpdateUrl = "invoice_payment_reportupdate.php";

		// Table object (_0_users)
		if (!isset($GLOBALS['_0_users'])) $GLOBALS['_0_users'] = new c_0_users();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'invoice_payment_report', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption finvoice_payment_reportlistsrch";

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
		$this->invoice_number->SetVisibility();
		$this->payment_ref->SetVisibility();
		$this->date_alloc->SetVisibility();
		$this->customer->SetVisibility();
		$this->user->SetVisibility();
		$this->amt->SetVisibility();

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
		global $EW_EXPORT, $invoice_payment_report;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($invoice_payment_report);
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

			// Get default search criteria
			ew_AddFilter($this->DefaultSearchWhere, $this->AdvancedSearchWhere(TRUE));

			// Get and validate search values for advanced search
			$this->LoadSearchValues(); // Get search values

			// Process filter list
			$this->ProcessFilterList();
			if (!$this->ValidateSearch())
				$this->setFailureMessage($gsSearchError);

			// Restore search parms from Session if not searching / reset / export
			if (($this->Export <> "" || $this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall") && $this->Command <> "json" && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetupSortOrder();

			// Get search criteria for advanced search
			if ($gsSearchError == "")
				$sSrchAdvanced = $this->AdvancedSearchWhere();
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

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load advanced search from default
			if ($this->LoadAdvancedSearchDefault()) {
				$sSrchAdvanced = $this->AdvancedSearchWhere();
			}
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif ($this->Command <> "json") {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		if (!$Security->CanList())
			$sFilter = "(0=1)"; // Filter all records
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);
		if ($sFilter == "") {
			$sFilter = "0=101";
			$this->SearchWhere = $sFilter;
		}

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

	// Get list of filters
	function GetFilterList() {
		global $UserProfile;

		// Initialize
		$sFilterList = "";
		$sSavedFilterList = "";
		$sFilterList = ew_Concat($sFilterList, $this->invoice_number->AdvancedSearch->ToJson(), ","); // Field invoice_number
		$sFilterList = ew_Concat($sFilterList, $this->payment_ref->AdvancedSearch->ToJson(), ","); // Field payment_ref
		$sFilterList = ew_Concat($sFilterList, $this->date_alloc->AdvancedSearch->ToJson(), ","); // Field date_alloc
		$sFilterList = ew_Concat($sFilterList, $this->person_id->AdvancedSearch->ToJson(), ","); // Field person_id
		$sFilterList = ew_Concat($sFilterList, $this->customer->AdvancedSearch->ToJson(), ","); // Field customer
		$sFilterList = ew_Concat($sFilterList, $this->user->AdvancedSearch->ToJson(), ","); // Field user
		$sFilterList = ew_Concat($sFilterList, $this->amt->AdvancedSearch->ToJson(), ","); // Field amt
		$sFilterList = preg_replace('/,$/', "", $sFilterList);

		// Return filter list in json
		if ($sFilterList <> "")
			$sFilterList = "\"data\":{" . $sFilterList . "}";
		if ($sSavedFilterList <> "") {
			if ($sFilterList <> "")
				$sFilterList .= ",";
			$sFilterList .= "\"filters\":" . $sSavedFilterList;
		}
		return ($sFilterList <> "") ? "{" . $sFilterList . "}" : "null";
	}

	// Process filter list
	function ProcessFilterList() {
		global $UserProfile;
		if (@$_POST["ajax"] == "savefilters") { // Save filter request (Ajax)
			$filters = @$_POST["filters"];
			$UserProfile->SetSearchFilters(CurrentUserName(), "finvoice_payment_reportlistsrch", $filters);

			// Clean output buffer
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			echo ew_ArrayToJson(array(array("success" => TRUE))); // Success
			$this->Page_Terminate();
			exit();
		} elseif (@$_POST["cmd"] == "resetfilter") {
			$this->RestoreFilterList();
		}
	}

	// Restore list of filters
	function RestoreFilterList() {

		// Return if not reset filter
		if (@$_POST["cmd"] <> "resetfilter")
			return FALSE;
		$filter = json_decode(@$_POST["filter"], TRUE);
		$this->Command = "search";

		// Field invoice_number
		$this->invoice_number->AdvancedSearch->SearchValue = @$filter["x_invoice_number"];
		$this->invoice_number->AdvancedSearch->SearchOperator = @$filter["z_invoice_number"];
		$this->invoice_number->AdvancedSearch->SearchCondition = @$filter["v_invoice_number"];
		$this->invoice_number->AdvancedSearch->SearchValue2 = @$filter["y_invoice_number"];
		$this->invoice_number->AdvancedSearch->SearchOperator2 = @$filter["w_invoice_number"];
		$this->invoice_number->AdvancedSearch->Save();

		// Field payment_ref
		$this->payment_ref->AdvancedSearch->SearchValue = @$filter["x_payment_ref"];
		$this->payment_ref->AdvancedSearch->SearchOperator = @$filter["z_payment_ref"];
		$this->payment_ref->AdvancedSearch->SearchCondition = @$filter["v_payment_ref"];
		$this->payment_ref->AdvancedSearch->SearchValue2 = @$filter["y_payment_ref"];
		$this->payment_ref->AdvancedSearch->SearchOperator2 = @$filter["w_payment_ref"];
		$this->payment_ref->AdvancedSearch->Save();

		// Field date_alloc
		$this->date_alloc->AdvancedSearch->SearchValue = @$filter["x_date_alloc"];
		$this->date_alloc->AdvancedSearch->SearchOperator = @$filter["z_date_alloc"];
		$this->date_alloc->AdvancedSearch->SearchCondition = @$filter["v_date_alloc"];
		$this->date_alloc->AdvancedSearch->SearchValue2 = @$filter["y_date_alloc"];
		$this->date_alloc->AdvancedSearch->SearchOperator2 = @$filter["w_date_alloc"];
		$this->date_alloc->AdvancedSearch->Save();

		// Field person_id
		$this->person_id->AdvancedSearch->SearchValue = @$filter["x_person_id"];
		$this->person_id->AdvancedSearch->SearchOperator = @$filter["z_person_id"];
		$this->person_id->AdvancedSearch->SearchCondition = @$filter["v_person_id"];
		$this->person_id->AdvancedSearch->SearchValue2 = @$filter["y_person_id"];
		$this->person_id->AdvancedSearch->SearchOperator2 = @$filter["w_person_id"];
		$this->person_id->AdvancedSearch->Save();

		// Field customer
		$this->customer->AdvancedSearch->SearchValue = @$filter["x_customer"];
		$this->customer->AdvancedSearch->SearchOperator = @$filter["z_customer"];
		$this->customer->AdvancedSearch->SearchCondition = @$filter["v_customer"];
		$this->customer->AdvancedSearch->SearchValue2 = @$filter["y_customer"];
		$this->customer->AdvancedSearch->SearchOperator2 = @$filter["w_customer"];
		$this->customer->AdvancedSearch->Save();

		// Field user
		$this->user->AdvancedSearch->SearchValue = @$filter["x_user"];
		$this->user->AdvancedSearch->SearchOperator = @$filter["z_user"];
		$this->user->AdvancedSearch->SearchCondition = @$filter["v_user"];
		$this->user->AdvancedSearch->SearchValue2 = @$filter["y_user"];
		$this->user->AdvancedSearch->SearchOperator2 = @$filter["w_user"];
		$this->user->AdvancedSearch->Save();

		// Field amt
		$this->amt->AdvancedSearch->SearchValue = @$filter["x_amt"];
		$this->amt->AdvancedSearch->SearchOperator = @$filter["z_amt"];
		$this->amt->AdvancedSearch->SearchCondition = @$filter["v_amt"];
		$this->amt->AdvancedSearch->SearchValue2 = @$filter["y_amt"];
		$this->amt->AdvancedSearch->SearchOperator2 = @$filter["w_amt"];
		$this->amt->AdvancedSearch->Save();
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere($Default = FALSE) {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->invoice_number, $Default, FALSE); // invoice_number
		$this->BuildSearchSql($sWhere, $this->payment_ref, $Default, FALSE); // payment_ref
		$this->BuildSearchSql($sWhere, $this->date_alloc, $Default, FALSE); // date_alloc
		$this->BuildSearchSql($sWhere, $this->person_id, $Default, FALSE); // person_id
		$this->BuildSearchSql($sWhere, $this->customer, $Default, FALSE); // customer
		$this->BuildSearchSql($sWhere, $this->user, $Default, FALSE); // user
		$this->BuildSearchSql($sWhere, $this->amt, $Default, FALSE); // amt

		// Set up search parm
		if (!$Default && $sWhere <> "" && in_array($this->Command, array("", "reset", "resetall"))) {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->invoice_number->AdvancedSearch->Save(); // invoice_number
			$this->payment_ref->AdvancedSearch->Save(); // payment_ref
			$this->date_alloc->AdvancedSearch->Save(); // date_alloc
			$this->person_id->AdvancedSearch->Save(); // person_id
			$this->customer->AdvancedSearch->Save(); // customer
			$this->user->AdvancedSearch->Save(); // user
			$this->amt->AdvancedSearch->Save(); // amt
		}
		return $sWhere;
	}

	// Build search SQL
	function BuildSearchSql(&$Where, &$Fld, $Default, $MultiValue) {
		$FldParm = $Fld->FldParm();
		$FldVal = ($Default) ? $Fld->AdvancedSearch->SearchValueDefault : $Fld->AdvancedSearch->SearchValue; // @$_GET["x_$FldParm"]
		$FldOpr = ($Default) ? $Fld->AdvancedSearch->SearchOperatorDefault : $Fld->AdvancedSearch->SearchOperator; // @$_GET["z_$FldParm"]
		$FldCond = ($Default) ? $Fld->AdvancedSearch->SearchConditionDefault : $Fld->AdvancedSearch->SearchCondition; // @$_GET["v_$FldParm"]
		$FldVal2 = ($Default) ? $Fld->AdvancedSearch->SearchValue2Default : $Fld->AdvancedSearch->SearchValue2; // @$_GET["y_$FldParm"]
		$FldOpr2 = ($Default) ? $Fld->AdvancedSearch->SearchOperator2Default : $Fld->AdvancedSearch->SearchOperator2; // @$_GET["w_$FldParm"]
		$sWrk = "";
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		if ($FldOpr == "") $FldOpr = "=";
		$FldOpr2 = strtoupper(trim($FldOpr2));
		if ($FldOpr2 == "") $FldOpr2 = "=";
		if (EW_SEARCH_MULTI_VALUE_OPTION == 1)
			$MultiValue = FALSE;
		if ($MultiValue) {
			$sWrk1 = ($FldVal <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr, $FldVal, $this->DBID) : ""; // Field value 1
			$sWrk2 = ($FldVal2 <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr2, $FldVal2, $this->DBID) : ""; // Field value 2
			$sWrk = $sWrk1; // Build final SQL
			if ($sWrk2 <> "")
				$sWrk = ($sWrk <> "") ? "($sWrk) $FldCond ($sWrk2)" : $sWrk2;
		} else {
			$FldVal = $this->ConvertSearchValue($Fld, $FldVal);
			$FldVal2 = $this->ConvertSearchValue($Fld, $FldVal2);
			$sWrk = ew_GetSearchSql($Fld, $FldVal, $FldOpr, $FldCond, $FldVal2, $FldOpr2, $this->DBID);
		}
		ew_AddFilter($Where, $sWrk);
	}

	// Convert search value
	function ConvertSearchValue(&$Fld, $FldVal) {
		if ($FldVal == EW_NULL_VALUE || $FldVal == EW_NOT_NULL_VALUE)
			return $FldVal;
		$Value = $FldVal;
		if ($Fld->FldDataType == EW_DATATYPE_BOOLEAN) {
			if ($FldVal <> "") $Value = ($FldVal == "1" || strtolower(strval($FldVal)) == "y" || strtolower(strval($FldVal)) == "t") ? $Fld->TrueValue : $Fld->FalseValue;
		} elseif ($Fld->FldDataType == EW_DATATYPE_DATE || $Fld->FldDataType == EW_DATATYPE_TIME) {
			if ($FldVal <> "") $Value = ew_UnFormatDateTime($FldVal, $Fld->FldDateTimeFormat);
		}
		return $Value;
	}

	// Check if search parm exists
	function CheckSearchParms() {
		if ($this->invoice_number->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->payment_ref->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->date_alloc->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->person_id->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->customer->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->user->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->amt->AdvancedSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear advanced search parameters
		$this->ResetAdvancedSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all advanced search parameters
	function ResetAdvancedSearchParms() {
		$this->invoice_number->AdvancedSearch->UnsetSession();
		$this->payment_ref->AdvancedSearch->UnsetSession();
		$this->date_alloc->AdvancedSearch->UnsetSession();
		$this->person_id->AdvancedSearch->UnsetSession();
		$this->customer->AdvancedSearch->UnsetSession();
		$this->user->AdvancedSearch->UnsetSession();
		$this->amt->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore advanced search values
		$this->invoice_number->AdvancedSearch->Load();
		$this->payment_ref->AdvancedSearch->Load();
		$this->date_alloc->AdvancedSearch->Load();
		$this->person_id->AdvancedSearch->Load();
		$this->customer->AdvancedSearch->Load();
		$this->user->AdvancedSearch->Load();
		$this->amt->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetupSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = @$_GET["order"];
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->invoice_number); // invoice_number
			$this->UpdateSort($this->payment_ref); // payment_ref
			$this->UpdateSort($this->date_alloc); // date_alloc
			$this->UpdateSort($this->customer); // customer
			$this->UpdateSort($this->user); // user
			$this->UpdateSort($this->amt); // amt
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

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->invoice_number->setSort("");
				$this->payment_ref->setSort("");
				$this->date_alloc->setSort("");
				$this->customer->setSort("");
				$this->user->setSort("");
				$this->amt->setSort("");
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

		// "sequence"
		$item = &$this->ListOptions->Add("sequence");
		$item->CssClass = "text-nowrap";
		$item->Visible = TRUE;
		$item->OnLeft = TRUE; // Always on left
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

		// "sequence"
		$oListOpt = &$this->ListOptions->Items["sequence"];
		$oListOpt->Body = ew_FormatSeqNo($this->RecCnt);

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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"finvoice_payment_reportlistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"finvoice_payment_reportlistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
		$item->Visible = TRUE;
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
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.finvoice_payment_reportlist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
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

		// Search button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = ($this->SearchWhere <> "") ? " active" : " active";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"finvoice_payment_reportlistsrch\">" . $Language->Phrase("SearchLink") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ResetSearch") . "\" data-caption=\"" . $Language->Phrase("ResetSearch") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ResetSearchBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

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

	// Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// invoice_number

		$this->invoice_number->AdvancedSearch->SearchValue = @$_GET["x_invoice_number"];
		if ($this->invoice_number->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->invoice_number->AdvancedSearch->SearchOperator = @$_GET["z_invoice_number"];

		// payment_ref
		$this->payment_ref->AdvancedSearch->SearchValue = @$_GET["x_payment_ref"];
		if ($this->payment_ref->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->payment_ref->AdvancedSearch->SearchOperator = @$_GET["z_payment_ref"];

		// date_alloc
		$this->date_alloc->AdvancedSearch->SearchValue = @$_GET["x_date_alloc"];
		if ($this->date_alloc->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->date_alloc->AdvancedSearch->SearchOperator = @$_GET["z_date_alloc"];
		$this->date_alloc->AdvancedSearch->SearchCondition = @$_GET["v_date_alloc"];
		$this->date_alloc->AdvancedSearch->SearchValue2 = @$_GET["y_date_alloc"];
		if ($this->date_alloc->AdvancedSearch->SearchValue2 <> "" && $this->Command == "") $this->Command = "search";
		$this->date_alloc->AdvancedSearch->SearchOperator2 = @$_GET["w_date_alloc"];

		// person_id
		$this->person_id->AdvancedSearch->SearchValue = @$_GET["x_person_id"];
		if ($this->person_id->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->person_id->AdvancedSearch->SearchOperator = @$_GET["z_person_id"];

		// customer
		$this->customer->AdvancedSearch->SearchValue = @$_GET["x_customer"];
		if ($this->customer->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->customer->AdvancedSearch->SearchOperator = @$_GET["z_customer"];

		// user
		$this->user->AdvancedSearch->SearchValue = @$_GET["x_user"];
		if ($this->user->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->user->AdvancedSearch->SearchOperator = @$_GET["z_user"];

		// amt
		$this->amt->AdvancedSearch->SearchValue = @$_GET["x_amt"];
		if ($this->amt->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->amt->AdvancedSearch->SearchOperator = @$_GET["z_amt"];
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
		$this->invoice_number->setDbValue($row['invoice_number']);
		$this->payment_ref->setDbValue($row['payment_ref']);
		$this->date_alloc->setDbValue($row['date_alloc']);
		$this->person_id->setDbValue($row['person_id']);
		$this->customer->setDbValue($row['customer']);
		$this->user->setDbValue($row['user']);
		$this->amt->setDbValue($row['amt']);
	}

	// Return a row with default values
	function NewRow() {
		$row = array();
		$row['invoice_number'] = NULL;
		$row['payment_ref'] = NULL;
		$row['date_alloc'] = NULL;
		$row['person_id'] = NULL;
		$row['customer'] = NULL;
		$row['user'] = NULL;
		$row['amt'] = NULL;
		return $row;
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF)
			return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->invoice_number->DbValue = $row['invoice_number'];
		$this->payment_ref->DbValue = $row['payment_ref'];
		$this->date_alloc->DbValue = $row['date_alloc'];
		$this->person_id->DbValue = $row['person_id'];
		$this->customer->DbValue = $row['customer'];
		$this->user->DbValue = $row['user'];
		$this->amt->DbValue = $row['amt'];
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
		if ($this->amt->FormValue == $this->amt->CurrentValue && is_numeric(ew_StrToFloat($this->amt->CurrentValue)))
			$this->amt->CurrentValue = ew_StrToFloat($this->amt->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// invoice_number
		// payment_ref
		// date_alloc
		// person_id
		// customer
		// user
		// amt
		// Accumulate aggregate value

		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT && $this->RowType <> EW_ROWTYPE_AGGREGATE) {
			if (is_numeric($this->amt->CurrentValue))
				$this->amt->Total += $this->amt->CurrentValue; // Accumulate total
		}
		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// invoice_number
		$this->invoice_number->ViewValue = $this->invoice_number->CurrentValue;
		$this->invoice_number->ViewCustomAttributes = "";

		// payment_ref
		$this->payment_ref->ViewValue = $this->payment_ref->CurrentValue;
		$this->payment_ref->ViewCustomAttributes = "";

		// date_alloc
		$this->date_alloc->ViewValue = $this->date_alloc->CurrentValue;
		$this->date_alloc->ViewValue = ew_FormatDateTime($this->date_alloc->ViewValue, 2);
		$this->date_alloc->ViewCustomAttributes = "";

		// customer
		$this->customer->ViewValue = $this->customer->CurrentValue;
		$this->customer->ViewCustomAttributes = "";

		// user
		if (strval($this->user->CurrentValue) <> "") {
			$sFilterWrk = "`id`" . ew_SearchString("=", $this->user->CurrentValue, EW_DATATYPE_NUMBER, "");
		switch (@$gsLanguage) {
			case "ar":
				$sSqlWrk = "SELECT `id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
				$sWhereWrk = "";
				$this->user->LookupFilters = array();
				break;
			case "en":
				$sSqlWrk = "SELECT `id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
				$sWhereWrk = "";
				$this->user->LookupFilters = array();
				break;
			default:
				$sSqlWrk = "SELECT `id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
				$sWhereWrk = "";
				$this->user->LookupFilters = array();
				break;
		}
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->user, $sWhereWrk); // Call Lookup Selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->user->ViewValue = $this->user->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->user->ViewValue = $this->user->CurrentValue;
			}
		} else {
			$this->user->ViewValue = NULL;
		}
		$this->user->ViewCustomAttributes = "";

		// amt
		$this->amt->ViewValue = $this->amt->CurrentValue;
		$this->amt->ViewCustomAttributes = "";

			// invoice_number
			$this->invoice_number->LinkCustomAttributes = "";
			$this->invoice_number->HrefValue = "";
			$this->invoice_number->TooltipValue = "";

			// payment_ref
			$this->payment_ref->LinkCustomAttributes = "";
			$this->payment_ref->HrefValue = "";
			$this->payment_ref->TooltipValue = "";

			// date_alloc
			$this->date_alloc->LinkCustomAttributes = "";
			$this->date_alloc->HrefValue = "";
			$this->date_alloc->TooltipValue = "";

			// customer
			$this->customer->LinkCustomAttributes = "";
			$this->customer->HrefValue = "";
			$this->customer->TooltipValue = "";

			// user
			$this->user->LinkCustomAttributes = "";
			$this->user->HrefValue = "";
			$this->user->TooltipValue = "";

			// amt
			$this->amt->LinkCustomAttributes = "";
			$this->amt->HrefValue = "";
			$this->amt->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// invoice_number
			$this->invoice_number->EditAttrs["class"] = "form-control";
			$this->invoice_number->EditCustomAttributes = "";
			$this->invoice_number->EditValue = ew_HtmlEncode($this->invoice_number->AdvancedSearch->SearchValue);
			$this->invoice_number->PlaceHolder = ew_RemoveHtml($this->invoice_number->FldCaption());

			// payment_ref
			$this->payment_ref->EditAttrs["class"] = "form-control";
			$this->payment_ref->EditCustomAttributes = "";
			$this->payment_ref->EditValue = ew_HtmlEncode($this->payment_ref->AdvancedSearch->SearchValue);
			$this->payment_ref->PlaceHolder = ew_RemoveHtml($this->payment_ref->FldCaption());

			// date_alloc
			$this->date_alloc->EditAttrs["class"] = "form-control";
			$this->date_alloc->EditCustomAttributes = "";
			$this->date_alloc->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->date_alloc->AdvancedSearch->SearchValue, 2), 2));
			$this->date_alloc->PlaceHolder = ew_RemoveHtml($this->date_alloc->FldCaption());
			$this->date_alloc->EditAttrs["class"] = "form-control";
			$this->date_alloc->EditCustomAttributes = "";
			$this->date_alloc->EditValue2 = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->date_alloc->AdvancedSearch->SearchValue2, 2), 2));
			$this->date_alloc->PlaceHolder = ew_RemoveHtml($this->date_alloc->FldCaption());

			// customer
			$this->customer->EditAttrs["class"] = "form-control";
			$this->customer->EditCustomAttributes = "";
			$this->customer->EditValue = ew_HtmlEncode($this->customer->AdvancedSearch->SearchValue);
			$this->customer->PlaceHolder = ew_RemoveHtml($this->customer->FldCaption());

			// user
			$this->user->EditAttrs["class"] = "form-control";
			$this->user->EditCustomAttributes = "";
			if (trim(strval($this->user->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`id`" . ew_SearchString("=", $this->user->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER, "");
			}
			switch (@$gsLanguage) {
				case "ar":
					$sSqlWrk = "SELECT `id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_users`";
					$sWhereWrk = "";
					$this->user->LookupFilters = array();
					break;
				case "en":
					$sSqlWrk = "SELECT `id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_users`";
					$sWhereWrk = "";
					$this->user->LookupFilters = array();
					break;
				default:
					$sSqlWrk = "SELECT `id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_users`";
					$sWhereWrk = "";
					$this->user->LookupFilters = array();
					break;
			}
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->user, $sWhereWrk); // Call Lookup Selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->user->EditValue = $arwrk;

			// amt
			$this->amt->EditAttrs["class"] = "form-control";
			$this->amt->EditCustomAttributes = "";
			$this->amt->EditValue = ew_HtmlEncode($this->amt->AdvancedSearch->SearchValue);
			$this->amt->PlaceHolder = ew_RemoveHtml($this->amt->FldCaption());
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATEINIT) { // Initialize aggregate row
			$this->amt->Total = 0; // Initialize total
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATE) { // Aggregate row
			$this->amt->CurrentValue = $this->amt->Total;
			$this->amt->ViewValue = $this->amt->CurrentValue;
			$this->amt->ViewCustomAttributes = "";
			$this->amt->HrefValue = ""; // Clear href value
		}
		if ($this->RowType == EW_ROWTYPE_ADD || $this->RowType == EW_ROWTYPE_EDIT || $this->RowType == EW_ROWTYPE_SEARCH) // Add/Edit/Search row
			$this->SetupFieldTitles();

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate search
	function ValidateSearch() {
		global $gsSearchError;

		// Initialize
		$gsSearchError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;
		if (!ew_CheckDateDef($this->date_alloc->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->date_alloc->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->date_alloc->AdvancedSearch->SearchValue2)) {
			ew_AddMessage($gsSearchError, $this->date_alloc->FldErrMsg());
		}

		// Return validate result
		$ValidateSearch = ($gsSearchError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateSearch = $ValidateSearch && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsSearchError, $sFormCustomError);
		}
		return $ValidateSearch;
	}

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->invoice_number->AdvancedSearch->Load();
		$this->payment_ref->AdvancedSearch->Load();
		$this->date_alloc->AdvancedSearch->Load();
		$this->person_id->AdvancedSearch->Load();
		$this->customer->AdvancedSearch->Load();
		$this->user->AdvancedSearch->Load();
		$this->amt->AdvancedSearch->Load();
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
		$item->Body = "<button id=\"emf_invoice_payment_report\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_invoice_payment_report',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.finvoice_payment_reportlist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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
		$this->AddSearchQueryString($sQry, $this->invoice_number); // invoice_number
		$this->AddSearchQueryString($sQry, $this->payment_ref); // payment_ref
		$this->AddSearchQueryString($sQry, $this->date_alloc); // date_alloc
		$this->AddSearchQueryString($sQry, $this->person_id); // person_id
		$this->AddSearchQueryString($sQry, $this->customer); // customer
		$this->AddSearchQueryString($sQry, $this->user); // user
		$this->AddSearchQueryString($sQry, $this->amt); // amt

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
		if ($pageId == "list") {
			switch ($fld->FldVar) {
			}
		} elseif ($pageId == "extbs") {
			switch ($fld->FldVar) {
		case "x_user":
			$sSqlWrk = "";
				switch (@$gsLanguage) {
					case "ar":
						$sSqlWrk = "SELECT `id` AS `LinkFld`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
						$sWhereWrk = "";
						$fld->LookupFilters = array();
						break;
					case "en":
						$sSqlWrk = "SELECT `id` AS `LinkFld`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
						$sWhereWrk = "";
						$fld->LookupFilters = array();
						break;
					default:
						$sSqlWrk = "SELECT `id` AS `LinkFld`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
						$sWhereWrk = "";
						$fld->LookupFilters = array();
						break;
				}
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`id` IN ({filter_value})', "t0" => "2", "fn0" => "");
			$sSqlWrk = "";
				$this->Lookup_Selecting($this->user, $sWhereWrk); // Call Lookup Selecting
				if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
			}
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		if ($pageId == "list") {
			switch ($fld->FldVar) {
			}
		} elseif ($pageId == "extbs") {
			switch ($fld->FldVar) {
			}
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
if (!isset($invoice_payment_report_list)) $invoice_payment_report_list = new cinvoice_payment_report_list();

// Page init
$invoice_payment_report_list->Page_Init();

// Page main
$invoice_payment_report_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$invoice_payment_report_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($invoice_payment_report->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = finvoice_payment_reportlist = new ew_Form("finvoice_payment_reportlist", "list");
finvoice_payment_reportlist.FormKeyCountName = '<?php echo $invoice_payment_report_list->FormKeyCountName ?>';

// Form_CustomValidate event
finvoice_payment_reportlist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid.
 	return true;
 }

// Use JavaScript validation or not
finvoice_payment_reportlist.ValidateRequired = <?php echo json_encode(EW_CLIENT_VALIDATE) ?>;

// Dynamic selection lists
finvoice_payment_reportlist.Lists["x_user"] = {"LinkField":"x_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_user_id","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"_0_users"};
finvoice_payment_reportlist.Lists["x_user"].Data = "<?php echo $invoice_payment_report_list->user->LookupFilterQuery(FALSE, "list") ?>";

// Form object for search
var CurrentSearchForm = finvoice_payment_reportlistsrch = new ew_Form("finvoice_payment_reportlistsrch");

// Validate function for search
finvoice_payment_reportlistsrch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_date_alloc");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($invoice_payment_report->date_alloc->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate event
finvoice_payment_reportlistsrch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid.
 	return true;
 }

// Use JavaScript validation or not
finvoice_payment_reportlistsrch.ValidateRequired = <?php echo json_encode(EW_CLIENT_VALIDATE) ?>;

// Dynamic selection lists
finvoice_payment_reportlistsrch.Lists["x_user"] = {"LinkField":"x_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_user_id","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"_0_users"};
finvoice_payment_reportlistsrch.Lists["x_user"].Data = "<?php echo $invoice_payment_report_list->user->LookupFilterQuery(FALSE, "extbs") ?>";
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($invoice_payment_report->Export == "") { ?>
<div class="ewToolbar">
<?php if ($invoice_payment_report_list->TotalRecs > 0 && $invoice_payment_report_list->ExportOptions->Visible()) { ?>
<?php $invoice_payment_report_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($invoice_payment_report_list->SearchOptions->Visible()) { ?>
<?php $invoice_payment_report_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($invoice_payment_report_list->FilterOptions->Visible()) { ?>
<?php $invoice_payment_report_list->FilterOptions->Render("body") ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
	$bSelectLimit = $invoice_payment_report_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($invoice_payment_report_list->TotalRecs <= 0)
			$invoice_payment_report_list->TotalRecs = $invoice_payment_report->ListRecordCount();
	} else {
		if (!$invoice_payment_report_list->Recordset && ($invoice_payment_report_list->Recordset = $invoice_payment_report_list->LoadRecordset()))
			$invoice_payment_report_list->TotalRecs = $invoice_payment_report_list->Recordset->RecordCount();
	}
	$invoice_payment_report_list->StartRec = 1;
	if ($invoice_payment_report_list->DisplayRecs <= 0 || ($invoice_payment_report->Export <> "" && $invoice_payment_report->ExportAll)) // Display all records
		$invoice_payment_report_list->DisplayRecs = $invoice_payment_report_list->TotalRecs;
	if (!($invoice_payment_report->Export <> "" && $invoice_payment_report->ExportAll))
		$invoice_payment_report_list->SetupStartRec(); // Set up start record position
	if ($bSelectLimit)
		$invoice_payment_report_list->Recordset = $invoice_payment_report_list->LoadRecordset($invoice_payment_report_list->StartRec-1, $invoice_payment_report_list->DisplayRecs);

	// Set no record found message
	if ($invoice_payment_report->CurrentAction == "" && $invoice_payment_report_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$invoice_payment_report_list->setWarningMessage(ew_DeniedMsg());
		if ($invoice_payment_report_list->SearchWhere == "0=101")
			$invoice_payment_report_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$invoice_payment_report_list->setWarningMessage($Language->Phrase("NoRecord"));
	}
$invoice_payment_report_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($invoice_payment_report->Export == "" && $invoice_payment_report->CurrentAction == "") { ?>
<form name="finvoice_payment_reportlistsrch" id="finvoice_payment_reportlistsrch" class="form-inline ewForm ewExtSearchForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($invoice_payment_report_list->SearchWhere <> "") ? " in" : " in"; ?>
<div id="finvoice_payment_reportlistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="invoice_payment_report">
	<div class="ewBasicSearch">
<?php
if ($gsSearchError == "")
	$invoice_payment_report_list->LoadAdvancedSearch(); // Load advanced search

// Render for search
$invoice_payment_report->RowType = EW_ROWTYPE_SEARCH;

// Render row
$invoice_payment_report->ResetAttrs();
$invoice_payment_report_list->RenderRow();
?>
<div id="xsr_1" class="ewRow">
<?php if ($invoice_payment_report->invoice_number->Visible) { // invoice_number ?>
	<div id="xsc_invoice_number" class="ewCell form-group">
		<label for="x_invoice_number" class="ewSearchCaption ewLabel"><?php echo $invoice_payment_report->invoice_number->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_invoice_number" id="z_invoice_number" value="="></span>
		<span class="ewSearchField">
<input type="text" data-table="invoice_payment_report" data-field="x_invoice_number" name="x_invoice_number" id="x_invoice_number" size="30" maxlength="60" placeholder="<?php echo ew_HtmlEncode($invoice_payment_report->invoice_number->getPlaceHolder()) ?>" value="<?php echo $invoice_payment_report->invoice_number->EditValue ?>"<?php echo $invoice_payment_report->invoice_number->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_2" class="ewRow">
<?php if ($invoice_payment_report->payment_ref->Visible) { // payment_ref ?>
	<div id="xsc_payment_ref" class="ewCell form-group">
		<label for="x_payment_ref" class="ewSearchCaption ewLabel"><?php echo $invoice_payment_report->payment_ref->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_payment_ref" id="z_payment_ref" value="="></span>
		<span class="ewSearchField">
<input type="text" data-table="invoice_payment_report" data-field="x_payment_ref" name="x_payment_ref" id="x_payment_ref" size="30" maxlength="60" placeholder="<?php echo ew_HtmlEncode($invoice_payment_report->payment_ref->getPlaceHolder()) ?>" value="<?php echo $invoice_payment_report->payment_ref->EditValue ?>"<?php echo $invoice_payment_report->payment_ref->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_3" class="ewRow">
<?php if ($invoice_payment_report->date_alloc->Visible) { // date_alloc ?>
	<div id="xsc_date_alloc" class="ewCell form-group">
		<label for="x_date_alloc" class="ewSearchCaption ewLabel"><?php echo $invoice_payment_report->date_alloc->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("BETWEEN") ?><input type="hidden" name="z_date_alloc" id="z_date_alloc" value="BETWEEN"></span>
		<span class="ewSearchField">
<input type="text" data-table="invoice_payment_report" data-field="x_date_alloc" data-format="2" name="x_date_alloc" id="x_date_alloc" placeholder="<?php echo ew_HtmlEncode($invoice_payment_report->date_alloc->getPlaceHolder()) ?>" value="<?php echo $invoice_payment_report->date_alloc->EditValue ?>"<?php echo $invoice_payment_report->date_alloc->EditAttributes() ?>>
<?php if (!$invoice_payment_report->date_alloc->ReadOnly && !$invoice_payment_report->date_alloc->Disabled && !isset($invoice_payment_report->date_alloc->EditAttrs["readonly"]) && !isset($invoice_payment_report->date_alloc->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateDateTimePicker("finvoice_payment_reportlistsrch", "x_date_alloc", {"ignoreReadonly":true,"useCurrent":false,"format":2});
</script>
<?php } ?>
</span>
		<span class="ewSearchCond btw1_date_alloc">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
		<span class="ewSearchField btw1_date_alloc">
<input type="text" data-table="invoice_payment_report" data-field="x_date_alloc" data-format="2" name="y_date_alloc" id="y_date_alloc" placeholder="<?php echo ew_HtmlEncode($invoice_payment_report->date_alloc->getPlaceHolder()) ?>" value="<?php echo $invoice_payment_report->date_alloc->EditValue2 ?>"<?php echo $invoice_payment_report->date_alloc->EditAttributes() ?>>
<?php if (!$invoice_payment_report->date_alloc->ReadOnly && !$invoice_payment_report->date_alloc->Disabled && !isset($invoice_payment_report->date_alloc->EditAttrs["readonly"]) && !isset($invoice_payment_report->date_alloc->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateDateTimePicker("finvoice_payment_reportlistsrch", "y_date_alloc", {"ignoreReadonly":true,"useCurrent":false,"format":2});
</script>
<?php } ?>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_4" class="ewRow">
<?php if ($invoice_payment_report->user->Visible) { // user ?>
	<div id="xsc_user" class="ewCell form-group">
		<label for="x_user" class="ewSearchCaption ewLabel"><?php echo $invoice_payment_report->user->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_user" id="z_user" value="="></span>
		<span class="ewSearchField">
<select data-table="invoice_payment_report" data-field="x_user" data-value-separator="<?php echo $invoice_payment_report->user->DisplayValueSeparatorAttribute() ?>" id="x_user" name="x_user"<?php echo $invoice_payment_report->user->EditAttributes() ?>>
<?php echo $invoice_payment_report->user->SelectOptionListHtml("x_user") ?>
</select>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_5" class="ewRow">
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("SearchBtn") ?></button>
</div>
	</div>
</div>
</form>
<?php } ?>
<?php } ?>
<?php $invoice_payment_report_list->ShowPageHeader(); ?>
<?php
$invoice_payment_report_list->ShowMessage();
?>
<?php if ($invoice_payment_report_list->TotalRecs > 0 || $invoice_payment_report->CurrentAction <> "") { ?>
<div class="box ewBox ewGrid<?php if ($invoice_payment_report_list->IsAddOrEdit()) { ?> ewGridAddEdit<?php } ?> invoice_payment_report">
<form name="finvoice_payment_reportlist" id="finvoice_payment_reportlist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($invoice_payment_report_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $invoice_payment_report_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="invoice_payment_report">
<div id="gmp_invoice_payment_report" class="<?php if (ew_IsResponsiveLayout()) { ?>table-responsive <?php } ?>ewGridMiddlePanel">
<?php if ($invoice_payment_report_list->TotalRecs > 0 || $invoice_payment_report->CurrentAction == "gridedit") { ?>
<table id="tbl_invoice_payment_reportlist" class="table ewTable">
<thead>
	<tr class="ewTableHeader">
<?php

// Header row
$invoice_payment_report_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$invoice_payment_report_list->RenderListOptions();

// Render list options (header, left)
$invoice_payment_report_list->ListOptions->Render("header", "left");
?>
<?php if ($invoice_payment_report->invoice_number->Visible) { // invoice_number ?>
	<?php if ($invoice_payment_report->SortUrl($invoice_payment_report->invoice_number) == "") { ?>
		<th data-name="invoice_number" class="<?php echo $invoice_payment_report->invoice_number->HeaderCellClass() ?>"><div id="elh_invoice_payment_report_invoice_number" class="invoice_payment_report_invoice_number"><div class="ewTableHeaderCaption"><?php echo $invoice_payment_report->invoice_number->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="invoice_number" class="<?php echo $invoice_payment_report->invoice_number->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $invoice_payment_report->SortUrl($invoice_payment_report->invoice_number) ?>',1);"><div id="elh_invoice_payment_report_invoice_number" class="invoice_payment_report_invoice_number">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $invoice_payment_report->invoice_number->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($invoice_payment_report->invoice_number->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($invoice_payment_report->invoice_number->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($invoice_payment_report->payment_ref->Visible) { // payment_ref ?>
	<?php if ($invoice_payment_report->SortUrl($invoice_payment_report->payment_ref) == "") { ?>
		<th data-name="payment_ref" class="<?php echo $invoice_payment_report->payment_ref->HeaderCellClass() ?>"><div id="elh_invoice_payment_report_payment_ref" class="invoice_payment_report_payment_ref"><div class="ewTableHeaderCaption"><?php echo $invoice_payment_report->payment_ref->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="payment_ref" class="<?php echo $invoice_payment_report->payment_ref->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $invoice_payment_report->SortUrl($invoice_payment_report->payment_ref) ?>',1);"><div id="elh_invoice_payment_report_payment_ref" class="invoice_payment_report_payment_ref">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $invoice_payment_report->payment_ref->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($invoice_payment_report->payment_ref->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($invoice_payment_report->payment_ref->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($invoice_payment_report->date_alloc->Visible) { // date_alloc ?>
	<?php if ($invoice_payment_report->SortUrl($invoice_payment_report->date_alloc) == "") { ?>
		<th data-name="date_alloc" class="<?php echo $invoice_payment_report->date_alloc->HeaderCellClass() ?>"><div id="elh_invoice_payment_report_date_alloc" class="invoice_payment_report_date_alloc"><div class="ewTableHeaderCaption"><?php echo $invoice_payment_report->date_alloc->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="date_alloc" class="<?php echo $invoice_payment_report->date_alloc->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $invoice_payment_report->SortUrl($invoice_payment_report->date_alloc) ?>',1);"><div id="elh_invoice_payment_report_date_alloc" class="invoice_payment_report_date_alloc">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $invoice_payment_report->date_alloc->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($invoice_payment_report->date_alloc->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($invoice_payment_report->date_alloc->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($invoice_payment_report->customer->Visible) { // customer ?>
	<?php if ($invoice_payment_report->SortUrl($invoice_payment_report->customer) == "") { ?>
		<th data-name="customer" class="<?php echo $invoice_payment_report->customer->HeaderCellClass() ?>"><div id="elh_invoice_payment_report_customer" class="invoice_payment_report_customer"><div class="ewTableHeaderCaption"><?php echo $invoice_payment_report->customer->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="customer" class="<?php echo $invoice_payment_report->customer->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $invoice_payment_report->SortUrl($invoice_payment_report->customer) ?>',1);"><div id="elh_invoice_payment_report_customer" class="invoice_payment_report_customer">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $invoice_payment_report->customer->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($invoice_payment_report->customer->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($invoice_payment_report->customer->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($invoice_payment_report->user->Visible) { // user ?>
	<?php if ($invoice_payment_report->SortUrl($invoice_payment_report->user) == "") { ?>
		<th data-name="user" class="<?php echo $invoice_payment_report->user->HeaderCellClass() ?>"><div id="elh_invoice_payment_report_user" class="invoice_payment_report_user"><div class="ewTableHeaderCaption"><?php echo $invoice_payment_report->user->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="user" class="<?php echo $invoice_payment_report->user->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $invoice_payment_report->SortUrl($invoice_payment_report->user) ?>',1);"><div id="elh_invoice_payment_report_user" class="invoice_payment_report_user">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $invoice_payment_report->user->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($invoice_payment_report->user->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($invoice_payment_report->user->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($invoice_payment_report->amt->Visible) { // amt ?>
	<?php if ($invoice_payment_report->SortUrl($invoice_payment_report->amt) == "") { ?>
		<th data-name="amt" class="<?php echo $invoice_payment_report->amt->HeaderCellClass() ?>"><div id="elh_invoice_payment_report_amt" class="invoice_payment_report_amt"><div class="ewTableHeaderCaption"><?php echo $invoice_payment_report->amt->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="amt" class="<?php echo $invoice_payment_report->amt->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $invoice_payment_report->SortUrl($invoice_payment_report->amt) ?>',1);"><div id="elh_invoice_payment_report_amt" class="invoice_payment_report_amt">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $invoice_payment_report->amt->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($invoice_payment_report->amt->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($invoice_payment_report->amt->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php

// Render list options (header, right)
$invoice_payment_report_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($invoice_payment_report->ExportAll && $invoice_payment_report->Export <> "") {
	$invoice_payment_report_list->StopRec = $invoice_payment_report_list->TotalRecs;
} else {

	// Set the last record to display
	if ($invoice_payment_report_list->TotalRecs > $invoice_payment_report_list->StartRec + $invoice_payment_report_list->DisplayRecs - 1)
		$invoice_payment_report_list->StopRec = $invoice_payment_report_list->StartRec + $invoice_payment_report_list->DisplayRecs - 1;
	else
		$invoice_payment_report_list->StopRec = $invoice_payment_report_list->TotalRecs;
}
$invoice_payment_report_list->RecCnt = $invoice_payment_report_list->StartRec - 1;
if ($invoice_payment_report_list->Recordset && !$invoice_payment_report_list->Recordset->EOF) {
	$invoice_payment_report_list->Recordset->MoveFirst();
	$bSelectLimit = $invoice_payment_report_list->UseSelectLimit;
	if (!$bSelectLimit && $invoice_payment_report_list->StartRec > 1)
		$invoice_payment_report_list->Recordset->Move($invoice_payment_report_list->StartRec - 1);
} elseif (!$invoice_payment_report->AllowAddDeleteRow && $invoice_payment_report_list->StopRec == 0) {
	$invoice_payment_report_list->StopRec = $invoice_payment_report->GridAddRowCount;
}

// Initialize aggregate
$invoice_payment_report->RowType = EW_ROWTYPE_AGGREGATEINIT;
$invoice_payment_report->ResetAttrs();
$invoice_payment_report_list->RenderRow();
while ($invoice_payment_report_list->RecCnt < $invoice_payment_report_list->StopRec) {
	$invoice_payment_report_list->RecCnt++;
	if (intval($invoice_payment_report_list->RecCnt) >= intval($invoice_payment_report_list->StartRec)) {
		$invoice_payment_report_list->RowCnt++;

		// Set up key count
		$invoice_payment_report_list->KeyCount = $invoice_payment_report_list->RowIndex;

		// Init row class and style
		$invoice_payment_report->ResetAttrs();
		$invoice_payment_report->CssClass = "";
		if ($invoice_payment_report->CurrentAction == "gridadd") {
		} else {
			$invoice_payment_report_list->LoadRowValues($invoice_payment_report_list->Recordset); // Load row values
		}
		$invoice_payment_report->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$invoice_payment_report->RowAttrs = array_merge($invoice_payment_report->RowAttrs, array('data-rowindex'=>$invoice_payment_report_list->RowCnt, 'id'=>'r' . $invoice_payment_report_list->RowCnt . '_invoice_payment_report', 'data-rowtype'=>$invoice_payment_report->RowType));

		// Render row
		$invoice_payment_report_list->RenderRow();

		// Render list options
		$invoice_payment_report_list->RenderListOptions();
?>
	<tr<?php echo $invoice_payment_report->RowAttributes() ?>>
<?php

// Render list options (body, left)
$invoice_payment_report_list->ListOptions->Render("body", "left", $invoice_payment_report_list->RowCnt);
?>
	<?php if ($invoice_payment_report->invoice_number->Visible) { // invoice_number ?>
		<td data-name="invoice_number"<?php echo $invoice_payment_report->invoice_number->CellAttributes() ?>>
<span id="el<?php echo $invoice_payment_report_list->RowCnt ?>_invoice_payment_report_invoice_number" class="invoice_payment_report_invoice_number">
<span<?php echo $invoice_payment_report->invoice_number->ViewAttributes() ?>>
<?php echo $invoice_payment_report->invoice_number->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($invoice_payment_report->payment_ref->Visible) { // payment_ref ?>
		<td data-name="payment_ref"<?php echo $invoice_payment_report->payment_ref->CellAttributes() ?>>
<span id="el<?php echo $invoice_payment_report_list->RowCnt ?>_invoice_payment_report_payment_ref" class="invoice_payment_report_payment_ref">
<span<?php echo $invoice_payment_report->payment_ref->ViewAttributes() ?>>
<?php echo $invoice_payment_report->payment_ref->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($invoice_payment_report->date_alloc->Visible) { // date_alloc ?>
		<td data-name="date_alloc"<?php echo $invoice_payment_report->date_alloc->CellAttributes() ?>>
<span id="el<?php echo $invoice_payment_report_list->RowCnt ?>_invoice_payment_report_date_alloc" class="invoice_payment_report_date_alloc">
<span<?php echo $invoice_payment_report->date_alloc->ViewAttributes() ?>>
<?php echo $invoice_payment_report->date_alloc->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($invoice_payment_report->customer->Visible) { // customer ?>
		<td data-name="customer"<?php echo $invoice_payment_report->customer->CellAttributes() ?>>
<span id="el<?php echo $invoice_payment_report_list->RowCnt ?>_invoice_payment_report_customer" class="invoice_payment_report_customer">
<span<?php echo $invoice_payment_report->customer->ViewAttributes() ?>>
<?php echo $invoice_payment_report->customer->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($invoice_payment_report->user->Visible) { // user ?>
		<td data-name="user"<?php echo $invoice_payment_report->user->CellAttributes() ?>>
<span id="el<?php echo $invoice_payment_report_list->RowCnt ?>_invoice_payment_report_user" class="invoice_payment_report_user">
<span<?php echo $invoice_payment_report->user->ViewAttributes() ?>>
<?php echo $invoice_payment_report->user->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($invoice_payment_report->amt->Visible) { // amt ?>
		<td data-name="amt"<?php echo $invoice_payment_report->amt->CellAttributes() ?>>
<span id="el<?php echo $invoice_payment_report_list->RowCnt ?>_invoice_payment_report_amt" class="invoice_payment_report_amt">
<span<?php echo $invoice_payment_report->amt->ViewAttributes() ?>>
<?php echo $invoice_payment_report->amt->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$invoice_payment_report_list->ListOptions->Render("body", "right", $invoice_payment_report_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($invoice_payment_report->CurrentAction <> "gridadd")
		$invoice_payment_report_list->Recordset->MoveNext();
}
?>
</tbody>
<?php

// Render aggregate row
$invoice_payment_report->RowType = EW_ROWTYPE_AGGREGATE;
$invoice_payment_report->ResetAttrs();
$invoice_payment_report_list->RenderRow();
?>
<?php if ($invoice_payment_report_list->TotalRecs > 0 && ($invoice_payment_report->CurrentAction <> "gridadd" && $invoice_payment_report->CurrentAction <> "gridedit")) { ?>
<tfoot><!-- Table footer -->
	<tr class="ewTableFooter">
<?php

// Render list options
$invoice_payment_report_list->RenderListOptions();

// Render list options (footer, left)
$invoice_payment_report_list->ListOptions->Render("footer", "left");
?>
	<?php if ($invoice_payment_report->invoice_number->Visible) { // invoice_number ?>
		<td data-name="invoice_number" class="<?php echo $invoice_payment_report->invoice_number->FooterCellClass() ?>"><span id="elf_invoice_payment_report_invoice_number" class="invoice_payment_report_invoice_number">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($invoice_payment_report->payment_ref->Visible) { // payment_ref ?>
		<td data-name="payment_ref" class="<?php echo $invoice_payment_report->payment_ref->FooterCellClass() ?>"><span id="elf_invoice_payment_report_payment_ref" class="invoice_payment_report_payment_ref">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($invoice_payment_report->date_alloc->Visible) { // date_alloc ?>
		<td data-name="date_alloc" class="<?php echo $invoice_payment_report->date_alloc->FooterCellClass() ?>"><span id="elf_invoice_payment_report_date_alloc" class="invoice_payment_report_date_alloc">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($invoice_payment_report->customer->Visible) { // customer ?>
		<td data-name="customer" class="<?php echo $invoice_payment_report->customer->FooterCellClass() ?>"><span id="elf_invoice_payment_report_customer" class="invoice_payment_report_customer">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($invoice_payment_report->user->Visible) { // user ?>
		<td data-name="user" class="<?php echo $invoice_payment_report->user->FooterCellClass() ?>"><span id="elf_invoice_payment_report_user" class="invoice_payment_report_user">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($invoice_payment_report->amt->Visible) { // amt ?>
		<td data-name="amt" class="<?php echo $invoice_payment_report->amt->FooterCellClass() ?>"><span id="elf_invoice_payment_report_amt" class="invoice_payment_report_amt">
<span class="ewAggregate"><?php echo $Language->Phrase("TOTAL") ?></span><span class="ewAggregateValue">
<?php echo $invoice_payment_report->amt->ViewValue ?></span>
		</span></td>
	<?php } ?>
<?php

// Render list options (footer, right)
$invoice_payment_report_list->ListOptions->Render("footer", "right");
?>
	</tr>
</tfoot>
<?php } ?>
</table>
<?php } ?>
<?php if ($invoice_payment_report->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($invoice_payment_report_list->Recordset)
	$invoice_payment_report_list->Recordset->Close();
?>
<?php if ($invoice_payment_report->Export == "") { ?>
<div class="box-footer ewGridLowerPanel">
<?php if ($invoice_payment_report->CurrentAction <> "gridadd" && $invoice_payment_report->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($invoice_payment_report_list->Pager)) $invoice_payment_report_list->Pager = new cPrevNextPager($invoice_payment_report_list->StartRec, $invoice_payment_report_list->DisplayRecs, $invoice_payment_report_list->TotalRecs, $invoice_payment_report_list->AutoHidePager) ?>
<?php if ($invoice_payment_report_list->Pager->RecordCount > 0 && $invoice_payment_report_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($invoice_payment_report_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $invoice_payment_report_list->PageUrl() ?>start=<?php echo $invoice_payment_report_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($invoice_payment_report_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $invoice_payment_report_list->PageUrl() ?>start=<?php echo $invoice_payment_report_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $invoice_payment_report_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($invoice_payment_report_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $invoice_payment_report_list->PageUrl() ?>start=<?php echo $invoice_payment_report_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($invoice_payment_report_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $invoice_payment_report_list->PageUrl() ?>start=<?php echo $invoice_payment_report_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $invoice_payment_report_list->Pager->PageCount ?></span>
</div>
<?php } ?>
<?php if ($invoice_payment_report_list->Pager->RecordCount > 0) { ?>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $invoice_payment_report_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $invoice_payment_report_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $invoice_payment_report_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($invoice_payment_report_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($invoice_payment_report_list->TotalRecs == 0 && $invoice_payment_report->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($invoice_payment_report_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($invoice_payment_report->Export == "") { ?>
<script type="text/javascript">
finvoice_payment_reportlistsrch.FilterList = <?php echo $invoice_payment_report_list->GetFilterList() ?>;
finvoice_payment_reportlistsrch.Init();
finvoice_payment_reportlist.Init();
</script>
<?php } ?>
<?php
$invoice_payment_report_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($invoice_payment_report->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$invoice_payment_report_list->Page_Terminate();
?>
