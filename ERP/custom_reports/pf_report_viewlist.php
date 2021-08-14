<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg14.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql14.php") ?>
<?php include_once "phpfn14.php" ?>
<?php include_once "pf_report_viewinfo.php" ?>
<?php include_once "_0_usersinfo.php" ?>
<?php include_once "userfn14.php" ?>
<?php

//
// Page class
//

$pf_report_view_list = NULL; // Initialize page object first

class cpf_report_view_list extends cpf_report_view {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = '{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}';

	// Table name
	var $TableName = 'pf_report_view';

	// Page object name
	var $PageObjName = 'pf_report_view_list';

	// Grid form hidden field names
	var $FormName = 'fpf_report_viewlist';
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

		// Table object (pf_report_view)
		if (!isset($GLOBALS["pf_report_view"]) || get_class($GLOBALS["pf_report_view"]) == "cpf_report_view") {
			$GLOBALS["pf_report_view"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["pf_report_view"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "pf_report_viewadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "pf_report_viewdelete.php";
		$this->MultiUpdateUrl = "pf_report_viewupdate.php";

		// Table object (_0_users)
		if (!isset($GLOBALS['_0_users'])) $GLOBALS['_0_users'] = new c_0_users();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'pf_report_view', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fpf_report_viewlistsrch";

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
		$this->invoice_no->SetVisibility();
		$this->transaction_date->SetVisibility();
		$this->description->SetVisibility();
		$this->customer_name->SetVisibility();
		$this->reference_customer->SetVisibility();
		$this->created_employee->SetVisibility();
		$this->pf_amount->SetVisibility();
		$this->quantity->SetVisibility();
		$this->total_pf_amount->SetVisibility();
		$this->stock_id->SetVisibility();

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
		global $EW_EXPORT, $pf_report_view;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($pf_report_view);
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
			ew_AddFilter($this->DefaultSearchWhere, $this->BasicSearchWhere(TRUE));
			ew_AddFilter($this->DefaultSearchWhere, $this->AdvancedSearchWhere(TRUE));

			// Get basic search values
			$this->LoadBasicSearchValues();

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

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();

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

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();

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
		$sFilterList = ew_Concat($sFilterList, $this->invoice_no->AdvancedSearch->ToJson(), ","); // Field invoice_no
		$sFilterList = ew_Concat($sFilterList, $this->transaction_date->AdvancedSearch->ToJson(), ","); // Field transaction_date
		$sFilterList = ew_Concat($sFilterList, $this->description->AdvancedSearch->ToJson(), ","); // Field description
		$sFilterList = ew_Concat($sFilterList, $this->customer_name->AdvancedSearch->ToJson(), ","); // Field customer_name
		$sFilterList = ew_Concat($sFilterList, $this->reference_customer->AdvancedSearch->ToJson(), ","); // Field reference_customer
		$sFilterList = ew_Concat($sFilterList, $this->created_employee->AdvancedSearch->ToJson(), ","); // Field created_employee
		$sFilterList = ew_Concat($sFilterList, $this->pf_amount->AdvancedSearch->ToJson(), ","); // Field pf_amount
		$sFilterList = ew_Concat($sFilterList, $this->quantity->AdvancedSearch->ToJson(), ","); // Field quantity
		$sFilterList = ew_Concat($sFilterList, $this->total_pf_amount->AdvancedSearch->ToJson(), ","); // Field total_pf_amount
		$sFilterList = ew_Concat($sFilterList, $this->invoice_amount->AdvancedSearch->ToJson(), ","); // Field invoice_amount
		$sFilterList = ew_Concat($sFilterList, $this->stock_id->AdvancedSearch->ToJson(), ","); // Field stock_id
		$sFilterList = ew_Concat($sFilterList, $this->unit_price->AdvancedSearch->ToJson(), ","); // Field unit_price
		$sFilterList = ew_Concat($sFilterList, $this->unit_tax->AdvancedSearch->ToJson(), ","); // Field unit_tax
		$sFilterList = ew_Concat($sFilterList, $this->discount_percent->AdvancedSearch->ToJson(), ","); // Field discount_percent
		$sFilterList = ew_Concat($sFilterList, $this->discount_amount->AdvancedSearch->ToJson(), ","); // Field discount_amount
		$sFilterList = ew_Concat($sFilterList, $this->govt_fee->AdvancedSearch->ToJson(), ","); // Field govt_fee
		$sFilterList = ew_Concat($sFilterList, $this->bank_service_charge->AdvancedSearch->ToJson(), ","); // Field bank_service_charge
		$sFilterList = ew_Concat($sFilterList, $this->bank_service_charge_vat->AdvancedSearch->ToJson(), ","); // Field bank_service_charge_vat
		$sFilterList = ew_Concat($sFilterList, $this->transaction_id->AdvancedSearch->ToJson(), ","); // Field transaction_id
		$sFilterList = ew_Concat($sFilterList, $this->user_commission->AdvancedSearch->ToJson(), ","); // Field user_commission
		$sFilterList = ew_Concat($sFilterList, $this->created_by->AdvancedSearch->ToJson(), ","); // Field created_by
		$sFilterList = ew_Concat($sFilterList, $this->updated_by->AdvancedSearch->ToJson(), ","); // Field updated_by
		if ($this->BasicSearch->Keyword <> "") {
			$sWrk = "\"" . EW_TABLE_BASIC_SEARCH . "\":\"" . ew_JsEncode2($this->BasicSearch->Keyword) . "\",\"" . EW_TABLE_BASIC_SEARCH_TYPE . "\":\"" . ew_JsEncode2($this->BasicSearch->Type) . "\"";
			$sFilterList = ew_Concat($sFilterList, $sWrk, ",");
		}
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
			$UserProfile->SetSearchFilters(CurrentUserName(), "fpf_report_viewlistsrch", $filters);

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

		// Field invoice_no
		$this->invoice_no->AdvancedSearch->SearchValue = @$filter["x_invoice_no"];
		$this->invoice_no->AdvancedSearch->SearchOperator = @$filter["z_invoice_no"];
		$this->invoice_no->AdvancedSearch->SearchCondition = @$filter["v_invoice_no"];
		$this->invoice_no->AdvancedSearch->SearchValue2 = @$filter["y_invoice_no"];
		$this->invoice_no->AdvancedSearch->SearchOperator2 = @$filter["w_invoice_no"];
		$this->invoice_no->AdvancedSearch->Save();

		// Field transaction_date
		$this->transaction_date->AdvancedSearch->SearchValue = @$filter["x_transaction_date"];
		$this->transaction_date->AdvancedSearch->SearchOperator = @$filter["z_transaction_date"];
		$this->transaction_date->AdvancedSearch->SearchCondition = @$filter["v_transaction_date"];
		$this->transaction_date->AdvancedSearch->SearchValue2 = @$filter["y_transaction_date"];
		$this->transaction_date->AdvancedSearch->SearchOperator2 = @$filter["w_transaction_date"];
		$this->transaction_date->AdvancedSearch->Save();

		// Field description
		$this->description->AdvancedSearch->SearchValue = @$filter["x_description"];
		$this->description->AdvancedSearch->SearchOperator = @$filter["z_description"];
		$this->description->AdvancedSearch->SearchCondition = @$filter["v_description"];
		$this->description->AdvancedSearch->SearchValue2 = @$filter["y_description"];
		$this->description->AdvancedSearch->SearchOperator2 = @$filter["w_description"];
		$this->description->AdvancedSearch->Save();

		// Field customer_name
		$this->customer_name->AdvancedSearch->SearchValue = @$filter["x_customer_name"];
		$this->customer_name->AdvancedSearch->SearchOperator = @$filter["z_customer_name"];
		$this->customer_name->AdvancedSearch->SearchCondition = @$filter["v_customer_name"];
		$this->customer_name->AdvancedSearch->SearchValue2 = @$filter["y_customer_name"];
		$this->customer_name->AdvancedSearch->SearchOperator2 = @$filter["w_customer_name"];
		$this->customer_name->AdvancedSearch->Save();

		// Field reference_customer
		$this->reference_customer->AdvancedSearch->SearchValue = @$filter["x_reference_customer"];
		$this->reference_customer->AdvancedSearch->SearchOperator = @$filter["z_reference_customer"];
		$this->reference_customer->AdvancedSearch->SearchCondition = @$filter["v_reference_customer"];
		$this->reference_customer->AdvancedSearch->SearchValue2 = @$filter["y_reference_customer"];
		$this->reference_customer->AdvancedSearch->SearchOperator2 = @$filter["w_reference_customer"];
		$this->reference_customer->AdvancedSearch->Save();

		// Field created_employee
		$this->created_employee->AdvancedSearch->SearchValue = @$filter["x_created_employee"];
		$this->created_employee->AdvancedSearch->SearchOperator = @$filter["z_created_employee"];
		$this->created_employee->AdvancedSearch->SearchCondition = @$filter["v_created_employee"];
		$this->created_employee->AdvancedSearch->SearchValue2 = @$filter["y_created_employee"];
		$this->created_employee->AdvancedSearch->SearchOperator2 = @$filter["w_created_employee"];
		$this->created_employee->AdvancedSearch->Save();

		// Field pf_amount
		$this->pf_amount->AdvancedSearch->SearchValue = @$filter["x_pf_amount"];
		$this->pf_amount->AdvancedSearch->SearchOperator = @$filter["z_pf_amount"];
		$this->pf_amount->AdvancedSearch->SearchCondition = @$filter["v_pf_amount"];
		$this->pf_amount->AdvancedSearch->SearchValue2 = @$filter["y_pf_amount"];
		$this->pf_amount->AdvancedSearch->SearchOperator2 = @$filter["w_pf_amount"];
		$this->pf_amount->AdvancedSearch->Save();

		// Field quantity
		$this->quantity->AdvancedSearch->SearchValue = @$filter["x_quantity"];
		$this->quantity->AdvancedSearch->SearchOperator = @$filter["z_quantity"];
		$this->quantity->AdvancedSearch->SearchCondition = @$filter["v_quantity"];
		$this->quantity->AdvancedSearch->SearchValue2 = @$filter["y_quantity"];
		$this->quantity->AdvancedSearch->SearchOperator2 = @$filter["w_quantity"];
		$this->quantity->AdvancedSearch->Save();

		// Field total_pf_amount
		$this->total_pf_amount->AdvancedSearch->SearchValue = @$filter["x_total_pf_amount"];
		$this->total_pf_amount->AdvancedSearch->SearchOperator = @$filter["z_total_pf_amount"];
		$this->total_pf_amount->AdvancedSearch->SearchCondition = @$filter["v_total_pf_amount"];
		$this->total_pf_amount->AdvancedSearch->SearchValue2 = @$filter["y_total_pf_amount"];
		$this->total_pf_amount->AdvancedSearch->SearchOperator2 = @$filter["w_total_pf_amount"];
		$this->total_pf_amount->AdvancedSearch->Save();

		// Field invoice_amount
		$this->invoice_amount->AdvancedSearch->SearchValue = @$filter["x_invoice_amount"];
		$this->invoice_amount->AdvancedSearch->SearchOperator = @$filter["z_invoice_amount"];
		$this->invoice_amount->AdvancedSearch->SearchCondition = @$filter["v_invoice_amount"];
		$this->invoice_amount->AdvancedSearch->SearchValue2 = @$filter["y_invoice_amount"];
		$this->invoice_amount->AdvancedSearch->SearchOperator2 = @$filter["w_invoice_amount"];
		$this->invoice_amount->AdvancedSearch->Save();

		// Field stock_id
		$this->stock_id->AdvancedSearch->SearchValue = @$filter["x_stock_id"];
		$this->stock_id->AdvancedSearch->SearchOperator = @$filter["z_stock_id"];
		$this->stock_id->AdvancedSearch->SearchCondition = @$filter["v_stock_id"];
		$this->stock_id->AdvancedSearch->SearchValue2 = @$filter["y_stock_id"];
		$this->stock_id->AdvancedSearch->SearchOperator2 = @$filter["w_stock_id"];
		$this->stock_id->AdvancedSearch->Save();

		// Field unit_price
		$this->unit_price->AdvancedSearch->SearchValue = @$filter["x_unit_price"];
		$this->unit_price->AdvancedSearch->SearchOperator = @$filter["z_unit_price"];
		$this->unit_price->AdvancedSearch->SearchCondition = @$filter["v_unit_price"];
		$this->unit_price->AdvancedSearch->SearchValue2 = @$filter["y_unit_price"];
		$this->unit_price->AdvancedSearch->SearchOperator2 = @$filter["w_unit_price"];
		$this->unit_price->AdvancedSearch->Save();

		// Field unit_tax
		$this->unit_tax->AdvancedSearch->SearchValue = @$filter["x_unit_tax"];
		$this->unit_tax->AdvancedSearch->SearchOperator = @$filter["z_unit_tax"];
		$this->unit_tax->AdvancedSearch->SearchCondition = @$filter["v_unit_tax"];
		$this->unit_tax->AdvancedSearch->SearchValue2 = @$filter["y_unit_tax"];
		$this->unit_tax->AdvancedSearch->SearchOperator2 = @$filter["w_unit_tax"];
		$this->unit_tax->AdvancedSearch->Save();

		// Field discount_percent
		$this->discount_percent->AdvancedSearch->SearchValue = @$filter["x_discount_percent"];
		$this->discount_percent->AdvancedSearch->SearchOperator = @$filter["z_discount_percent"];
		$this->discount_percent->AdvancedSearch->SearchCondition = @$filter["v_discount_percent"];
		$this->discount_percent->AdvancedSearch->SearchValue2 = @$filter["y_discount_percent"];
		$this->discount_percent->AdvancedSearch->SearchOperator2 = @$filter["w_discount_percent"];
		$this->discount_percent->AdvancedSearch->Save();

		// Field discount_amount
		$this->discount_amount->AdvancedSearch->SearchValue = @$filter["x_discount_amount"];
		$this->discount_amount->AdvancedSearch->SearchOperator = @$filter["z_discount_amount"];
		$this->discount_amount->AdvancedSearch->SearchCondition = @$filter["v_discount_amount"];
		$this->discount_amount->AdvancedSearch->SearchValue2 = @$filter["y_discount_amount"];
		$this->discount_amount->AdvancedSearch->SearchOperator2 = @$filter["w_discount_amount"];
		$this->discount_amount->AdvancedSearch->Save();

		// Field govt_fee
		$this->govt_fee->AdvancedSearch->SearchValue = @$filter["x_govt_fee"];
		$this->govt_fee->AdvancedSearch->SearchOperator = @$filter["z_govt_fee"];
		$this->govt_fee->AdvancedSearch->SearchCondition = @$filter["v_govt_fee"];
		$this->govt_fee->AdvancedSearch->SearchValue2 = @$filter["y_govt_fee"];
		$this->govt_fee->AdvancedSearch->SearchOperator2 = @$filter["w_govt_fee"];
		$this->govt_fee->AdvancedSearch->Save();

		// Field bank_service_charge
		$this->bank_service_charge->AdvancedSearch->SearchValue = @$filter["x_bank_service_charge"];
		$this->bank_service_charge->AdvancedSearch->SearchOperator = @$filter["z_bank_service_charge"];
		$this->bank_service_charge->AdvancedSearch->SearchCondition = @$filter["v_bank_service_charge"];
		$this->bank_service_charge->AdvancedSearch->SearchValue2 = @$filter["y_bank_service_charge"];
		$this->bank_service_charge->AdvancedSearch->SearchOperator2 = @$filter["w_bank_service_charge"];
		$this->bank_service_charge->AdvancedSearch->Save();

		// Field bank_service_charge_vat
		$this->bank_service_charge_vat->AdvancedSearch->SearchValue = @$filter["x_bank_service_charge_vat"];
		$this->bank_service_charge_vat->AdvancedSearch->SearchOperator = @$filter["z_bank_service_charge_vat"];
		$this->bank_service_charge_vat->AdvancedSearch->SearchCondition = @$filter["v_bank_service_charge_vat"];
		$this->bank_service_charge_vat->AdvancedSearch->SearchValue2 = @$filter["y_bank_service_charge_vat"];
		$this->bank_service_charge_vat->AdvancedSearch->SearchOperator2 = @$filter["w_bank_service_charge_vat"];
		$this->bank_service_charge_vat->AdvancedSearch->Save();

		// Field transaction_id
		$this->transaction_id->AdvancedSearch->SearchValue = @$filter["x_transaction_id"];
		$this->transaction_id->AdvancedSearch->SearchOperator = @$filter["z_transaction_id"];
		$this->transaction_id->AdvancedSearch->SearchCondition = @$filter["v_transaction_id"];
		$this->transaction_id->AdvancedSearch->SearchValue2 = @$filter["y_transaction_id"];
		$this->transaction_id->AdvancedSearch->SearchOperator2 = @$filter["w_transaction_id"];
		$this->transaction_id->AdvancedSearch->Save();

		// Field user_commission
		$this->user_commission->AdvancedSearch->SearchValue = @$filter["x_user_commission"];
		$this->user_commission->AdvancedSearch->SearchOperator = @$filter["z_user_commission"];
		$this->user_commission->AdvancedSearch->SearchCondition = @$filter["v_user_commission"];
		$this->user_commission->AdvancedSearch->SearchValue2 = @$filter["y_user_commission"];
		$this->user_commission->AdvancedSearch->SearchOperator2 = @$filter["w_user_commission"];
		$this->user_commission->AdvancedSearch->Save();

		// Field created_by
		$this->created_by->AdvancedSearch->SearchValue = @$filter["x_created_by"];
		$this->created_by->AdvancedSearch->SearchOperator = @$filter["z_created_by"];
		$this->created_by->AdvancedSearch->SearchCondition = @$filter["v_created_by"];
		$this->created_by->AdvancedSearch->SearchValue2 = @$filter["y_created_by"];
		$this->created_by->AdvancedSearch->SearchOperator2 = @$filter["w_created_by"];
		$this->created_by->AdvancedSearch->Save();

		// Field updated_by
		$this->updated_by->AdvancedSearch->SearchValue = @$filter["x_updated_by"];
		$this->updated_by->AdvancedSearch->SearchOperator = @$filter["z_updated_by"];
		$this->updated_by->AdvancedSearch->SearchCondition = @$filter["v_updated_by"];
		$this->updated_by->AdvancedSearch->SearchValue2 = @$filter["y_updated_by"];
		$this->updated_by->AdvancedSearch->SearchOperator2 = @$filter["w_updated_by"];
		$this->updated_by->AdvancedSearch->Save();
		$this->BasicSearch->setKeyword(@$filter[EW_TABLE_BASIC_SEARCH]);
		$this->BasicSearch->setType(@$filter[EW_TABLE_BASIC_SEARCH_TYPE]);
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere($Default = FALSE) {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->invoice_no, $Default, FALSE); // invoice_no
		$this->BuildSearchSql($sWhere, $this->transaction_date, $Default, FALSE); // transaction_date
		$this->BuildSearchSql($sWhere, $this->description, $Default, FALSE); // description
		$this->BuildSearchSql($sWhere, $this->customer_name, $Default, TRUE); // customer_name
		$this->BuildSearchSql($sWhere, $this->reference_customer, $Default, FALSE); // reference_customer
		$this->BuildSearchSql($sWhere, $this->created_employee, $Default, TRUE); // created_employee
		$this->BuildSearchSql($sWhere, $this->pf_amount, $Default, FALSE); // pf_amount
		$this->BuildSearchSql($sWhere, $this->quantity, $Default, FALSE); // quantity
		$this->BuildSearchSql($sWhere, $this->total_pf_amount, $Default, FALSE); // total_pf_amount
		$this->BuildSearchSql($sWhere, $this->invoice_amount, $Default, FALSE); // invoice_amount
		$this->BuildSearchSql($sWhere, $this->stock_id, $Default, FALSE); // stock_id
		$this->BuildSearchSql($sWhere, $this->unit_price, $Default, FALSE); // unit_price
		$this->BuildSearchSql($sWhere, $this->unit_tax, $Default, FALSE); // unit_tax
		$this->BuildSearchSql($sWhere, $this->discount_percent, $Default, FALSE); // discount_percent
		$this->BuildSearchSql($sWhere, $this->discount_amount, $Default, FALSE); // discount_amount
		$this->BuildSearchSql($sWhere, $this->govt_fee, $Default, FALSE); // govt_fee
		$this->BuildSearchSql($sWhere, $this->bank_service_charge, $Default, FALSE); // bank_service_charge
		$this->BuildSearchSql($sWhere, $this->bank_service_charge_vat, $Default, FALSE); // bank_service_charge_vat
		$this->BuildSearchSql($sWhere, $this->transaction_id, $Default, FALSE); // transaction_id
		$this->BuildSearchSql($sWhere, $this->user_commission, $Default, FALSE); // user_commission
		$this->BuildSearchSql($sWhere, $this->created_by, $Default, FALSE); // created_by
		$this->BuildSearchSql($sWhere, $this->updated_by, $Default, FALSE); // updated_by

		// Set up search parm
		if (!$Default && $sWhere <> "" && in_array($this->Command, array("", "reset", "resetall"))) {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->invoice_no->AdvancedSearch->Save(); // invoice_no
			$this->transaction_date->AdvancedSearch->Save(); // transaction_date
			$this->description->AdvancedSearch->Save(); // description
			$this->customer_name->AdvancedSearch->Save(); // customer_name
			$this->reference_customer->AdvancedSearch->Save(); // reference_customer
			$this->created_employee->AdvancedSearch->Save(); // created_employee
			$this->pf_amount->AdvancedSearch->Save(); // pf_amount
			$this->quantity->AdvancedSearch->Save(); // quantity
			$this->total_pf_amount->AdvancedSearch->Save(); // total_pf_amount
			$this->invoice_amount->AdvancedSearch->Save(); // invoice_amount
			$this->stock_id->AdvancedSearch->Save(); // stock_id
			$this->unit_price->AdvancedSearch->Save(); // unit_price
			$this->unit_tax->AdvancedSearch->Save(); // unit_tax
			$this->discount_percent->AdvancedSearch->Save(); // discount_percent
			$this->discount_amount->AdvancedSearch->Save(); // discount_amount
			$this->govt_fee->AdvancedSearch->Save(); // govt_fee
			$this->bank_service_charge->AdvancedSearch->Save(); // bank_service_charge
			$this->bank_service_charge_vat->AdvancedSearch->Save(); // bank_service_charge_vat
			$this->transaction_id->AdvancedSearch->Save(); // transaction_id
			$this->user_commission->AdvancedSearch->Save(); // user_commission
			$this->created_by->AdvancedSearch->Save(); // created_by
			$this->updated_by->AdvancedSearch->Save(); // updated_by
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

	// Return basic search SQL
	function BasicSearchSQL($arKeywords, $type) {
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->invoice_no, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->description, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->customer_name, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->reference_customer, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->created_employee, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->stock_id, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->transaction_id, $arKeywords, $type);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSQL(&$Where, &$Fld, $arKeywords, $type) {
		global $EW_BASIC_SEARCH_IGNORE_PATTERN;
		$sDefCond = ($type == "OR") ? "OR" : "AND";
		$arSQL = array(); // Array for SQL parts
		$arCond = array(); // Array for search conditions
		$cnt = count($arKeywords);
		$j = 0; // Number of SQL parts
		for ($i = 0; $i < $cnt; $i++) {
			$Keyword = $arKeywords[$i];
			$Keyword = trim($Keyword);
			if ($EW_BASIC_SEARCH_IGNORE_PATTERN <> "") {
				$Keyword = preg_replace($EW_BASIC_SEARCH_IGNORE_PATTERN, "\\", $Keyword);
				$ar = explode("\\", $Keyword);
			} else {
				$ar = array($Keyword);
			}
			foreach ($ar as $Keyword) {
				if ($Keyword <> "") {
					$sWrk = "";
					if ($Keyword == "OR" && $type == "") {
						if ($j > 0)
							$arCond[$j-1] = "OR";
					} elseif ($Keyword == EW_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NULL";
					} elseif ($Keyword == EW_NOT_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NOT NULL";
					} elseif ($Fld->FldIsVirtual) {
						$sWrk = $Fld->FldVirtualExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING, $this->DBID), $this->DBID);
					} elseif ($Fld->FldDataType != EW_DATATYPE_NUMBER || is_numeric($Keyword)) {
						$sWrk = $Fld->FldBasicSearchExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING, $this->DBID), $this->DBID);
					}
					if ($sWrk <> "") {
						$arSQL[$j] = $sWrk;
						$arCond[$j] = $sDefCond;
						$j += 1;
					}
				}
			}
		}
		$cnt = count($arSQL);
		$bQuoted = FALSE;
		$sSql = "";
		if ($cnt > 0) {
			for ($i = 0; $i < $cnt-1; $i++) {
				if ($arCond[$i] == "OR") {
					if (!$bQuoted) $sSql .= "(";
					$bQuoted = TRUE;
				}
				$sSql .= $arSQL[$i];
				if ($bQuoted && $arCond[$i] <> "OR") {
					$sSql .= ")";
					$bQuoted = FALSE;
				}
				$sSql .= " " . $arCond[$i] . " ";
			}
			$sSql .= $arSQL[$cnt-1];
			if ($bQuoted)
				$sSql .= ")";
		}
		if ($sSql <> "") {
			if ($Where <> "") $Where .= " OR ";
			$Where .= "(" . $sSql . ")";
		}
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere($Default = FALSE) {
		global $Security;
		$sSearchStr = "";
		if (!$Security->CanSearch()) return "";
		$sSearchKeyword = ($Default) ? $this->BasicSearch->KeywordDefault : $this->BasicSearch->Keyword;
		$sSearchType = ($Default) ? $this->BasicSearch->TypeDefault : $this->BasicSearch->Type;

		// Get search SQL
		if ($sSearchKeyword <> "") {
			$ar = $this->BasicSearch->KeywordList($Default);

			// Search keyword in any fields
			if (($sSearchType == "OR" || $sSearchType == "AND") && $this->BasicSearch->BasicSearchAnyFields) {
				foreach ($ar as $sKeyword) {
					if ($sKeyword <> "") {
						if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
						$sSearchStr .= "(" . $this->BasicSearchSQL(array($sKeyword), $sSearchType) . ")";
					}
				}
			} else {
				$sSearchStr = $this->BasicSearchSQL($ar, $sSearchType);
			}
			if (!$Default && in_array($this->Command, array("", "reset", "resetall"))) $this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		if ($this->invoice_no->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->transaction_date->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->description->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->customer_name->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->reference_customer->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->created_employee->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->pf_amount->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->quantity->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->total_pf_amount->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->invoice_amount->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->stock_id->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->unit_price->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->unit_tax->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->discount_percent->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->discount_amount->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->govt_fee->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->bank_service_charge->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->bank_service_charge_vat->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->transaction_id->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->user_commission->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->created_by->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->updated_by->AdvancedSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();

		// Clear advanced search parameters
		$this->ResetAdvancedSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Clear all advanced search parameters
	function ResetAdvancedSearchParms() {
		$this->invoice_no->AdvancedSearch->UnsetSession();
		$this->transaction_date->AdvancedSearch->UnsetSession();
		$this->description->AdvancedSearch->UnsetSession();
		$this->customer_name->AdvancedSearch->UnsetSession();
		$this->reference_customer->AdvancedSearch->UnsetSession();
		$this->created_employee->AdvancedSearch->UnsetSession();
		$this->pf_amount->AdvancedSearch->UnsetSession();
		$this->quantity->AdvancedSearch->UnsetSession();
		$this->total_pf_amount->AdvancedSearch->UnsetSession();
		$this->invoice_amount->AdvancedSearch->UnsetSession();
		$this->stock_id->AdvancedSearch->UnsetSession();
		$this->unit_price->AdvancedSearch->UnsetSession();
		$this->unit_tax->AdvancedSearch->UnsetSession();
		$this->discount_percent->AdvancedSearch->UnsetSession();
		$this->discount_amount->AdvancedSearch->UnsetSession();
		$this->govt_fee->AdvancedSearch->UnsetSession();
		$this->bank_service_charge->AdvancedSearch->UnsetSession();
		$this->bank_service_charge_vat->AdvancedSearch->UnsetSession();
		$this->transaction_id->AdvancedSearch->UnsetSession();
		$this->user_commission->AdvancedSearch->UnsetSession();
		$this->created_by->AdvancedSearch->UnsetSession();
		$this->updated_by->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->invoice_no->AdvancedSearch->Load();
		$this->transaction_date->AdvancedSearch->Load();
		$this->description->AdvancedSearch->Load();
		$this->customer_name->AdvancedSearch->Load();
		$this->reference_customer->AdvancedSearch->Load();
		$this->created_employee->AdvancedSearch->Load();
		$this->pf_amount->AdvancedSearch->Load();
		$this->quantity->AdvancedSearch->Load();
		$this->total_pf_amount->AdvancedSearch->Load();
		$this->invoice_amount->AdvancedSearch->Load();
		$this->stock_id->AdvancedSearch->Load();
		$this->unit_price->AdvancedSearch->Load();
		$this->unit_tax->AdvancedSearch->Load();
		$this->discount_percent->AdvancedSearch->Load();
		$this->discount_amount->AdvancedSearch->Load();
		$this->govt_fee->AdvancedSearch->Load();
		$this->bank_service_charge->AdvancedSearch->Load();
		$this->bank_service_charge_vat->AdvancedSearch->Load();
		$this->transaction_id->AdvancedSearch->Load();
		$this->user_commission->AdvancedSearch->Load();
		$this->created_by->AdvancedSearch->Load();
		$this->updated_by->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetupSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = @$_GET["order"];
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->invoice_no); // invoice_no
			$this->UpdateSort($this->transaction_date); // transaction_date
			$this->UpdateSort($this->description); // description
			$this->UpdateSort($this->customer_name); // customer_name
			$this->UpdateSort($this->reference_customer); // reference_customer
			$this->UpdateSort($this->created_employee); // created_employee
			$this->UpdateSort($this->pf_amount); // pf_amount
			$this->UpdateSort($this->quantity); // quantity
			$this->UpdateSort($this->total_pf_amount); // total_pf_amount
			$this->UpdateSort($this->stock_id); // stock_id
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
				$this->invoice_no->setSort("");
				$this->transaction_date->setSort("");
				$this->description->setSort("");
				$this->customer_name->setSort("");
				$this->reference_customer->setSort("");
				$this->created_employee->setSort("");
				$this->pf_amount->setSort("");
				$this->quantity->setSort("");
				$this->total_pf_amount->setSort("");
				$this->stock_id->setSort("");
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fpf_report_viewlistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fpf_report_viewlistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
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
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fpf_report_viewlist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fpf_report_viewlistsrch\">" . $Language->Phrase("SearchLink") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
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

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "" && $this->Command == "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

	// Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// invoice_no

		$this->invoice_no->AdvancedSearch->SearchValue = @$_GET["x_invoice_no"];
		if ($this->invoice_no->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->invoice_no->AdvancedSearch->SearchOperator = @$_GET["z_invoice_no"];

		// transaction_date
		$this->transaction_date->AdvancedSearch->SearchValue = @$_GET["x_transaction_date"];
		if ($this->transaction_date->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->transaction_date->AdvancedSearch->SearchOperator = @$_GET["z_transaction_date"];
		$this->transaction_date->AdvancedSearch->SearchCondition = @$_GET["v_transaction_date"];
		$this->transaction_date->AdvancedSearch->SearchValue2 = @$_GET["y_transaction_date"];
		if ($this->transaction_date->AdvancedSearch->SearchValue2 <> "" && $this->Command == "") $this->Command = "search";
		$this->transaction_date->AdvancedSearch->SearchOperator2 = @$_GET["w_transaction_date"];

		// description
		$this->description->AdvancedSearch->SearchValue = @$_GET["x_description"];
		if ($this->description->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->description->AdvancedSearch->SearchOperator = @$_GET["z_description"];

		// customer_name
		$this->customer_name->AdvancedSearch->SearchValue = @$_GET["x_customer_name"];
		if ($this->customer_name->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->customer_name->AdvancedSearch->SearchOperator = @$_GET["z_customer_name"];
		if (is_array($this->customer_name->AdvancedSearch->SearchValue)) $this->customer_name->AdvancedSearch->SearchValue = implode(",", $this->customer_name->AdvancedSearch->SearchValue);
		if (is_array($this->customer_name->AdvancedSearch->SearchValue2)) $this->customer_name->AdvancedSearch->SearchValue2 = implode(",", $this->customer_name->AdvancedSearch->SearchValue2);

		// reference_customer
		$this->reference_customer->AdvancedSearch->SearchValue = @$_GET["x_reference_customer"];
		if ($this->reference_customer->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->reference_customer->AdvancedSearch->SearchOperator = @$_GET["z_reference_customer"];

		// created_employee
		$this->created_employee->AdvancedSearch->SearchValue = @$_GET["x_created_employee"];
		if ($this->created_employee->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->created_employee->AdvancedSearch->SearchOperator = @$_GET["z_created_employee"];
		if (is_array($this->created_employee->AdvancedSearch->SearchValue)) $this->created_employee->AdvancedSearch->SearchValue = implode(",", $this->created_employee->AdvancedSearch->SearchValue);
		if (is_array($this->created_employee->AdvancedSearch->SearchValue2)) $this->created_employee->AdvancedSearch->SearchValue2 = implode(",", $this->created_employee->AdvancedSearch->SearchValue2);

		// pf_amount
		$this->pf_amount->AdvancedSearch->SearchValue = @$_GET["x_pf_amount"];
		if ($this->pf_amount->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->pf_amount->AdvancedSearch->SearchOperator = @$_GET["z_pf_amount"];

		// quantity
		$this->quantity->AdvancedSearch->SearchValue = @$_GET["x_quantity"];
		if ($this->quantity->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->quantity->AdvancedSearch->SearchOperator = @$_GET["z_quantity"];

		// total_pf_amount
		$this->total_pf_amount->AdvancedSearch->SearchValue = @$_GET["x_total_pf_amount"];
		if ($this->total_pf_amount->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->total_pf_amount->AdvancedSearch->SearchOperator = @$_GET["z_total_pf_amount"];

		// invoice_amount
		$this->invoice_amount->AdvancedSearch->SearchValue = @$_GET["x_invoice_amount"];
		if ($this->invoice_amount->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->invoice_amount->AdvancedSearch->SearchOperator = @$_GET["z_invoice_amount"];

		// stock_id
		$this->stock_id->AdvancedSearch->SearchValue = @$_GET["x_stock_id"];
		if ($this->stock_id->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->stock_id->AdvancedSearch->SearchOperator = @$_GET["z_stock_id"];

		// unit_price
		$this->unit_price->AdvancedSearch->SearchValue = @$_GET["x_unit_price"];
		if ($this->unit_price->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->unit_price->AdvancedSearch->SearchOperator = @$_GET["z_unit_price"];

		// unit_tax
		$this->unit_tax->AdvancedSearch->SearchValue = @$_GET["x_unit_tax"];
		if ($this->unit_tax->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->unit_tax->AdvancedSearch->SearchOperator = @$_GET["z_unit_tax"];

		// discount_percent
		$this->discount_percent->AdvancedSearch->SearchValue = @$_GET["x_discount_percent"];
		if ($this->discount_percent->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->discount_percent->AdvancedSearch->SearchOperator = @$_GET["z_discount_percent"];

		// discount_amount
		$this->discount_amount->AdvancedSearch->SearchValue = @$_GET["x_discount_amount"];
		if ($this->discount_amount->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->discount_amount->AdvancedSearch->SearchOperator = @$_GET["z_discount_amount"];

		// govt_fee
		$this->govt_fee->AdvancedSearch->SearchValue = @$_GET["x_govt_fee"];
		if ($this->govt_fee->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->govt_fee->AdvancedSearch->SearchOperator = @$_GET["z_govt_fee"];

		// bank_service_charge
		$this->bank_service_charge->AdvancedSearch->SearchValue = @$_GET["x_bank_service_charge"];
		if ($this->bank_service_charge->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->bank_service_charge->AdvancedSearch->SearchOperator = @$_GET["z_bank_service_charge"];

		// bank_service_charge_vat
		$this->bank_service_charge_vat->AdvancedSearch->SearchValue = @$_GET["x_bank_service_charge_vat"];
		if ($this->bank_service_charge_vat->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->bank_service_charge_vat->AdvancedSearch->SearchOperator = @$_GET["z_bank_service_charge_vat"];

		// transaction_id
		$this->transaction_id->AdvancedSearch->SearchValue = @$_GET["x_transaction_id"];
		if ($this->transaction_id->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->transaction_id->AdvancedSearch->SearchOperator = @$_GET["z_transaction_id"];

		// user_commission
		$this->user_commission->AdvancedSearch->SearchValue = @$_GET["x_user_commission"];
		if ($this->user_commission->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->user_commission->AdvancedSearch->SearchOperator = @$_GET["z_user_commission"];

		// created_by
		$this->created_by->AdvancedSearch->SearchValue = @$_GET["x_created_by"];
		if ($this->created_by->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->created_by->AdvancedSearch->SearchOperator = @$_GET["z_created_by"];

		// updated_by
		$this->updated_by->AdvancedSearch->SearchValue = @$_GET["x_updated_by"];
		if ($this->updated_by->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->updated_by->AdvancedSearch->SearchOperator = @$_GET["z_updated_by"];
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
		$this->invoice_no->setDbValue($row['invoice_no']);
		$this->transaction_date->setDbValue($row['transaction_date']);
		$this->description->setDbValue($row['description']);
		$this->customer_name->setDbValue($row['customer_name']);
		$this->reference_customer->setDbValue($row['reference_customer']);
		$this->created_employee->setDbValue($row['created_employee']);
		$this->pf_amount->setDbValue($row['pf_amount']);
		$this->quantity->setDbValue($row['quantity']);
		$this->total_pf_amount->setDbValue($row['total_pf_amount']);
		$this->invoice_amount->setDbValue($row['invoice_amount']);
		$this->stock_id->setDbValue($row['stock_id']);
		$this->unit_price->setDbValue($row['unit_price']);
		$this->unit_tax->setDbValue($row['unit_tax']);
		$this->discount_percent->setDbValue($row['discount_percent']);
		$this->discount_amount->setDbValue($row['discount_amount']);
		$this->govt_fee->setDbValue($row['govt_fee']);
		$this->bank_service_charge->setDbValue($row['bank_service_charge']);
		$this->bank_service_charge_vat->setDbValue($row['bank_service_charge_vat']);
		$this->transaction_id->setDbValue($row['transaction_id']);
		$this->user_commission->setDbValue($row['user_commission']);
		$this->created_by->setDbValue($row['created_by']);
		$this->updated_by->setDbValue($row['updated_by']);
	}

	// Return a row with default values
	function NewRow() {
		$row = array();
		$row['invoice_no'] = NULL;
		$row['transaction_date'] = NULL;
		$row['description'] = NULL;
		$row['customer_name'] = NULL;
		$row['reference_customer'] = NULL;
		$row['created_employee'] = NULL;
		$row['pf_amount'] = NULL;
		$row['quantity'] = NULL;
		$row['total_pf_amount'] = NULL;
		$row['invoice_amount'] = NULL;
		$row['stock_id'] = NULL;
		$row['unit_price'] = NULL;
		$row['unit_tax'] = NULL;
		$row['discount_percent'] = NULL;
		$row['discount_amount'] = NULL;
		$row['govt_fee'] = NULL;
		$row['bank_service_charge'] = NULL;
		$row['bank_service_charge_vat'] = NULL;
		$row['transaction_id'] = NULL;
		$row['user_commission'] = NULL;
		$row['created_by'] = NULL;
		$row['updated_by'] = NULL;
		return $row;
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF)
			return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->invoice_no->DbValue = $row['invoice_no'];
		$this->transaction_date->DbValue = $row['transaction_date'];
		$this->description->DbValue = $row['description'];
		$this->customer_name->DbValue = $row['customer_name'];
		$this->reference_customer->DbValue = $row['reference_customer'];
		$this->created_employee->DbValue = $row['created_employee'];
		$this->pf_amount->DbValue = $row['pf_amount'];
		$this->quantity->DbValue = $row['quantity'];
		$this->total_pf_amount->DbValue = $row['total_pf_amount'];
		$this->invoice_amount->DbValue = $row['invoice_amount'];
		$this->stock_id->DbValue = $row['stock_id'];
		$this->unit_price->DbValue = $row['unit_price'];
		$this->unit_tax->DbValue = $row['unit_tax'];
		$this->discount_percent->DbValue = $row['discount_percent'];
		$this->discount_amount->DbValue = $row['discount_amount'];
		$this->govt_fee->DbValue = $row['govt_fee'];
		$this->bank_service_charge->DbValue = $row['bank_service_charge'];
		$this->bank_service_charge_vat->DbValue = $row['bank_service_charge_vat'];
		$this->transaction_id->DbValue = $row['transaction_id'];
		$this->user_commission->DbValue = $row['user_commission'];
		$this->created_by->DbValue = $row['created_by'];
		$this->updated_by->DbValue = $row['updated_by'];
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
		if ($this->pf_amount->FormValue == $this->pf_amount->CurrentValue && is_numeric(ew_StrToFloat($this->pf_amount->CurrentValue)))
			$this->pf_amount->CurrentValue = ew_StrToFloat($this->pf_amount->CurrentValue);

		// Convert decimal values if posted back
		if ($this->quantity->FormValue == $this->quantity->CurrentValue && is_numeric(ew_StrToFloat($this->quantity->CurrentValue)))
			$this->quantity->CurrentValue = ew_StrToFloat($this->quantity->CurrentValue);

		// Convert decimal values if posted back
		if ($this->total_pf_amount->FormValue == $this->total_pf_amount->CurrentValue && is_numeric(ew_StrToFloat($this->total_pf_amount->CurrentValue)))
			$this->total_pf_amount->CurrentValue = ew_StrToFloat($this->total_pf_amount->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// invoice_no
		// transaction_date
		// description
		// customer_name
		// reference_customer
		// created_employee
		// pf_amount
		// quantity
		// total_pf_amount
		// invoice_amount
		// stock_id
		// unit_price
		// unit_tax
		// discount_percent
		// discount_amount
		// govt_fee
		// bank_service_charge
		// bank_service_charge_vat
		// transaction_id
		// user_commission
		// created_by
		// updated_by
		// Accumulate aggregate value

		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT && $this->RowType <> EW_ROWTYPE_AGGREGATE) {
			if (is_numeric($this->total_pf_amount->CurrentValue))
				$this->total_pf_amount->Total += $this->total_pf_amount->CurrentValue; // Accumulate total
		}
		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// invoice_no
		$this->invoice_no->ViewValue = $this->invoice_no->CurrentValue;
		$this->invoice_no->ViewCustomAttributes = "";

		// transaction_date
		$this->transaction_date->ViewValue = $this->transaction_date->CurrentValue;
		$this->transaction_date->ViewValue = ew_FormatDateTime($this->transaction_date->ViewValue, 2);
		$this->transaction_date->ViewCustomAttributes = "";

		// description
		$this->description->ViewValue = $this->description->CurrentValue;
		$this->description->ViewCustomAttributes = "";

		// customer_name
		if (strval($this->customer_name->CurrentValue) <> "") {
			$arwrk = explode(",", $this->customer_name->CurrentValue);
			$sFilterWrk = "";
			foreach ($arwrk as $wrk) {
				if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
				$sFilterWrk .= "`name`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_STRING, "");
			}
		switch (@$gsLanguage) {
			case "ar":
				$sSqlWrk = "SELECT `name`, `name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_debtors_master`";
				$sWhereWrk = "";
				$this->customer_name->LookupFilters = array("dx1" => '`name`');
				break;
			case "en":
				$sSqlWrk = "SELECT `name`, `name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_debtors_master`";
				$sWhereWrk = "";
				$this->customer_name->LookupFilters = array("dx1" => '`name`');
				break;
			default:
				$sSqlWrk = "SELECT `name`, `name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_debtors_master`";
				$sWhereWrk = "";
				$this->customer_name->LookupFilters = array("dx1" => '`name`');
				break;
		}
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->customer_name, $sWhereWrk); // Call Lookup Selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->customer_name->ViewValue = "";
				$ari = 0;
				while (!$rswrk->EOF) {
					$arwrk = array();
					$arwrk[1] = $rswrk->fields('DispFld');
					$this->customer_name->ViewValue .= $this->customer_name->DisplayValue($arwrk);
					$rswrk->MoveNext();
					if (!$rswrk->EOF) $this->customer_name->ViewValue .= ew_ViewOptionSeparator($ari); // Separate Options
					$ari++;
				}
				$rswrk->Close();
			} else {
				$this->customer_name->ViewValue = $this->customer_name->CurrentValue;
			}
		} else {
			$this->customer_name->ViewValue = NULL;
		}
		$this->customer_name->ViewCustomAttributes = "";

		// reference_customer
		$this->reference_customer->ViewValue = $this->reference_customer->CurrentValue;
		$this->reference_customer->ViewCustomAttributes = "";

		// created_employee
		if (strval($this->created_employee->CurrentValue) <> "") {
			$arwrk = explode(",", $this->created_employee->CurrentValue);
			$sFilterWrk = "";
			foreach ($arwrk as $wrk) {
				if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
				$sFilterWrk .= "`user_id`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_STRING, "");
			}
		switch (@$gsLanguage) {
			case "ar":
				$sSqlWrk = "SELECT `user_id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
				$sWhereWrk = "";
				$this->created_employee->LookupFilters = array("dx1" => '`user_id`');
				break;
			case "en":
				$sSqlWrk = "SELECT `user_id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
				$sWhereWrk = "";
				$this->created_employee->LookupFilters = array("dx1" => '`user_id`');
				break;
			default:
				$sSqlWrk = "SELECT `user_id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
				$sWhereWrk = "";
				$this->created_employee->LookupFilters = array("dx1" => '`user_id`');
				break;
		}
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->created_employee, $sWhereWrk); // Call Lookup Selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->created_employee->ViewValue = "";
				$ari = 0;
				while (!$rswrk->EOF) {
					$arwrk = array();
					$arwrk[1] = $rswrk->fields('DispFld');
					$this->created_employee->ViewValue .= $this->created_employee->DisplayValue($arwrk);
					$rswrk->MoveNext();
					if (!$rswrk->EOF) $this->created_employee->ViewValue .= ew_ViewOptionSeparator($ari); // Separate Options
					$ari++;
				}
				$rswrk->Close();
			} else {
				$this->created_employee->ViewValue = $this->created_employee->CurrentValue;
			}
		} else {
			$this->created_employee->ViewValue = NULL;
		}
		$this->created_employee->ViewCustomAttributes = "";

		// pf_amount
		$this->pf_amount->ViewValue = $this->pf_amount->CurrentValue;
		$this->pf_amount->ViewCustomAttributes = "";

		// quantity
		$this->quantity->ViewValue = $this->quantity->CurrentValue;
		$this->quantity->ViewCustomAttributes = "";

		// total_pf_amount
		$this->total_pf_amount->ViewValue = $this->total_pf_amount->CurrentValue;
		$this->total_pf_amount->ViewCustomAttributes = "";

		// stock_id
		if (strval($this->stock_id->CurrentValue) <> "") {
			$sFilterWrk = "`stock_id`" . ew_SearchString("=", $this->stock_id->CurrentValue, EW_DATATYPE_STRING, "");
		switch (@$gsLanguage) {
			case "ar":
				$sSqlWrk = "SELECT `stock_id`, `description` AS `DispFld`, `long_description` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_stock_master`";
				$sWhereWrk = "";
				$this->stock_id->LookupFilters = array("dx1" => '`description`', "dx2" => '`long_description`');
				break;
			case "en":
				$sSqlWrk = "SELECT `stock_id`, `description` AS `DispFld`, `long_description` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_stock_master`";
				$sWhereWrk = "";
				$this->stock_id->LookupFilters = array("dx1" => '`description`', "dx2" => '`long_description`');
				break;
			default:
				$sSqlWrk = "SELECT `stock_id`, `description` AS `DispFld`, `long_description` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_stock_master`";
				$sWhereWrk = "";
				$this->stock_id->LookupFilters = array("dx1" => '`description`', "dx2" => '`long_description`');
				break;
		}
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->stock_id, $sWhereWrk); // Call Lookup Selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$this->stock_id->ViewValue = $this->stock_id->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->stock_id->ViewValue = $this->stock_id->CurrentValue;
			}
		} else {
			$this->stock_id->ViewValue = NULL;
		}
		$this->stock_id->ViewCustomAttributes = "";

			// invoice_no
			$this->invoice_no->LinkCustomAttributes = "";
			$this->invoice_no->HrefValue = "";
			$this->invoice_no->TooltipValue = "";

			// transaction_date
			$this->transaction_date->LinkCustomAttributes = "";
			$this->transaction_date->HrefValue = "";
			$this->transaction_date->TooltipValue = "";

			// description
			$this->description->LinkCustomAttributes = "";
			$this->description->HrefValue = "";
			$this->description->TooltipValue = "";

			// customer_name
			$this->customer_name->LinkCustomAttributes = "";
			$this->customer_name->HrefValue = "";
			$this->customer_name->TooltipValue = "";

			// reference_customer
			$this->reference_customer->LinkCustomAttributes = "";
			$this->reference_customer->HrefValue = "";
			$this->reference_customer->TooltipValue = "";

			// created_employee
			$this->created_employee->LinkCustomAttributes = "";
			$this->created_employee->HrefValue = "";
			$this->created_employee->TooltipValue = "";

			// pf_amount
			$this->pf_amount->LinkCustomAttributes = "";
			$this->pf_amount->HrefValue = "";
			$this->pf_amount->TooltipValue = "";

			// quantity
			$this->quantity->LinkCustomAttributes = "";
			$this->quantity->HrefValue = "";
			$this->quantity->TooltipValue = "";

			// total_pf_amount
			$this->total_pf_amount->LinkCustomAttributes = "";
			$this->total_pf_amount->HrefValue = "";
			$this->total_pf_amount->TooltipValue = "";

			// stock_id
			$this->stock_id->LinkCustomAttributes = "";
			$this->stock_id->HrefValue = "";
			$this->stock_id->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// invoice_no
			$this->invoice_no->EditAttrs["class"] = "form-control";
			$this->invoice_no->EditCustomAttributes = "";
			$this->invoice_no->EditValue = ew_HtmlEncode($this->invoice_no->AdvancedSearch->SearchValue);
			$this->invoice_no->PlaceHolder = ew_RemoveHtml($this->invoice_no->FldCaption());

			// transaction_date
			$this->transaction_date->EditAttrs["class"] = "form-control";
			$this->transaction_date->EditCustomAttributes = "";
			$this->transaction_date->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->transaction_date->AdvancedSearch->SearchValue, 2), 2));
			$this->transaction_date->PlaceHolder = ew_RemoveHtml($this->transaction_date->FldCaption());
			$this->transaction_date->EditAttrs["class"] = "form-control";
			$this->transaction_date->EditCustomAttributes = "";
			$this->transaction_date->EditValue2 = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->transaction_date->AdvancedSearch->SearchValue2, 2), 2));
			$this->transaction_date->PlaceHolder = ew_RemoveHtml($this->transaction_date->FldCaption());

			// description
			$this->description->EditAttrs["class"] = "form-control";
			$this->description->EditCustomAttributes = "";
			$this->description->EditValue = ew_HtmlEncode($this->description->AdvancedSearch->SearchValue);
			$this->description->PlaceHolder = ew_RemoveHtml($this->description->FldCaption());

			// customer_name
			$this->customer_name->EditCustomAttributes = "";
			if (trim(strval($this->customer_name->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$arwrk = explode(",", $this->customer_name->AdvancedSearch->SearchValue);
				$sFilterWrk = "";
				foreach ($arwrk as $wrk) {
					if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
					$sFilterWrk .= "`name`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_STRING, "");
				}
			}
			switch (@$gsLanguage) {
				case "ar":
					$sSqlWrk = "SELECT `name`, `name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_debtors_master`";
					$sWhereWrk = "";
					$this->customer_name->LookupFilters = array("dx1" => '`name`');
					break;
				case "en":
					$sSqlWrk = "SELECT `name`, `name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_debtors_master`";
					$sWhereWrk = "";
					$this->customer_name->LookupFilters = array("dx1" => '`name`');
					break;
				default:
					$sSqlWrk = "SELECT `name`, `name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_debtors_master`";
					$sWhereWrk = "";
					$this->customer_name->LookupFilters = array("dx1" => '`name`');
					break;
			}
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->customer_name, $sWhereWrk); // Call Lookup Selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->customer_name->AdvancedSearch->ViewValue = "";
				$ari = 0;
				while (!$rswrk->EOF) {
					$arwrk = array();
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$this->customer_name->AdvancedSearch->ViewValue .= $this->customer_name->DisplayValue($arwrk);
					$rswrk->MoveNext();
					if (!$rswrk->EOF) $this->customer_name->AdvancedSearch->ViewValue .= ew_ViewOptionSeparator($ari); // Separate Options
					$ari++;
				}
				$rswrk->MoveFirst();
			} else {
				$this->customer_name->AdvancedSearch->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->customer_name->EditValue = $arwrk;

			// reference_customer
			$this->reference_customer->EditAttrs["class"] = "form-control";
			$this->reference_customer->EditCustomAttributes = "";
			$this->reference_customer->EditValue = ew_HtmlEncode($this->reference_customer->AdvancedSearch->SearchValue);
			$this->reference_customer->PlaceHolder = ew_RemoveHtml($this->reference_customer->FldCaption());

			// created_employee
			$this->created_employee->EditCustomAttributes = "";
			if (trim(strval($this->created_employee->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$arwrk = explode(",", $this->created_employee->AdvancedSearch->SearchValue);
				$sFilterWrk = "";
				foreach ($arwrk as $wrk) {
					if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
					$sFilterWrk .= "`user_id`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_STRING, "");
				}
			}
			switch (@$gsLanguage) {
				case "ar":
					$sSqlWrk = "SELECT `user_id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_users`";
					$sWhereWrk = "";
					$this->created_employee->LookupFilters = array("dx1" => '`user_id`');
					break;
				case "en":
					$sSqlWrk = "SELECT `user_id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_users`";
					$sWhereWrk = "";
					$this->created_employee->LookupFilters = array("dx1" => '`user_id`');
					break;
				default:
					$sSqlWrk = "SELECT `user_id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_users`";
					$sWhereWrk = "";
					$this->created_employee->LookupFilters = array("dx1" => '`user_id`');
					break;
			}
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->created_employee, $sWhereWrk); // Call Lookup Selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->created_employee->AdvancedSearch->ViewValue = "";
				$ari = 0;
				while (!$rswrk->EOF) {
					$arwrk = array();
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$this->created_employee->AdvancedSearch->ViewValue .= $this->created_employee->DisplayValue($arwrk);
					$rswrk->MoveNext();
					if (!$rswrk->EOF) $this->created_employee->AdvancedSearch->ViewValue .= ew_ViewOptionSeparator($ari); // Separate Options
					$ari++;
				}
				$rswrk->MoveFirst();
			} else {
				$this->created_employee->AdvancedSearch->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->created_employee->EditValue = $arwrk;

			// pf_amount
			$this->pf_amount->EditAttrs["class"] = "form-control";
			$this->pf_amount->EditCustomAttributes = "";
			$this->pf_amount->EditValue = ew_HtmlEncode($this->pf_amount->AdvancedSearch->SearchValue);
			$this->pf_amount->PlaceHolder = ew_RemoveHtml($this->pf_amount->FldCaption());

			// quantity
			$this->quantity->EditAttrs["class"] = "form-control";
			$this->quantity->EditCustomAttributes = "";
			$this->quantity->EditValue = ew_HtmlEncode($this->quantity->AdvancedSearch->SearchValue);
			$this->quantity->PlaceHolder = ew_RemoveHtml($this->quantity->FldCaption());

			// total_pf_amount
			$this->total_pf_amount->EditAttrs["class"] = "form-control";
			$this->total_pf_amount->EditCustomAttributes = "";
			$this->total_pf_amount->EditValue = ew_HtmlEncode($this->total_pf_amount->AdvancedSearch->SearchValue);
			$this->total_pf_amount->PlaceHolder = ew_RemoveHtml($this->total_pf_amount->FldCaption());

			// stock_id
			$this->stock_id->EditCustomAttributes = "";
			if (trim(strval($this->stock_id->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`stock_id`" . ew_SearchString("=", $this->stock_id->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			switch (@$gsLanguage) {
				case "ar":
					$sSqlWrk = "SELECT `stock_id`, `description` AS `DispFld`, `long_description` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_stock_master`";
					$sWhereWrk = "";
					$this->stock_id->LookupFilters = array("dx1" => '`description`', "dx2" => '`long_description`');
					break;
				case "en":
					$sSqlWrk = "SELECT `stock_id`, `description` AS `DispFld`, `long_description` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_stock_master`";
					$sWhereWrk = "";
					$this->stock_id->LookupFilters = array("dx1" => '`description`', "dx2" => '`long_description`');
					break;
				default:
					$sSqlWrk = "SELECT `stock_id`, `description` AS `DispFld`, `long_description` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_stock_master`";
					$sWhereWrk = "";
					$this->stock_id->LookupFilters = array("dx1" => '`description`', "dx2" => '`long_description`');
					break;
			}
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->stock_id, $sWhereWrk); // Call Lookup Selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
				$this->stock_id->AdvancedSearch->ViewValue = $this->stock_id->DisplayValue($arwrk);
			} else {
				$this->stock_id->AdvancedSearch->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->stock_id->EditValue = $arwrk;
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATEINIT) { // Initialize aggregate row
			$this->total_pf_amount->Total = 0; // Initialize total
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATE) { // Aggregate row
			$this->total_pf_amount->CurrentValue = $this->total_pf_amount->Total;
			$this->total_pf_amount->ViewValue = $this->total_pf_amount->CurrentValue;
			$this->total_pf_amount->ViewCustomAttributes = "";
			$this->total_pf_amount->HrefValue = ""; // Clear href value
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
		if (!ew_CheckDateDef($this->transaction_date->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->transaction_date->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->transaction_date->AdvancedSearch->SearchValue2)) {
			ew_AddMessage($gsSearchError, $this->transaction_date->FldErrMsg());
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
		$this->invoice_no->AdvancedSearch->Load();
		$this->transaction_date->AdvancedSearch->Load();
		$this->description->AdvancedSearch->Load();
		$this->customer_name->AdvancedSearch->Load();
		$this->reference_customer->AdvancedSearch->Load();
		$this->created_employee->AdvancedSearch->Load();
		$this->pf_amount->AdvancedSearch->Load();
		$this->quantity->AdvancedSearch->Load();
		$this->total_pf_amount->AdvancedSearch->Load();
		$this->invoice_amount->AdvancedSearch->Load();
		$this->stock_id->AdvancedSearch->Load();
		$this->unit_price->AdvancedSearch->Load();
		$this->unit_tax->AdvancedSearch->Load();
		$this->discount_percent->AdvancedSearch->Load();
		$this->discount_amount->AdvancedSearch->Load();
		$this->govt_fee->AdvancedSearch->Load();
		$this->bank_service_charge->AdvancedSearch->Load();
		$this->bank_service_charge_vat->AdvancedSearch->Load();
		$this->transaction_id->AdvancedSearch->Load();
		$this->user_commission->AdvancedSearch->Load();
		$this->created_by->AdvancedSearch->Load();
		$this->updated_by->AdvancedSearch->Load();
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
		$item->Body = "<button id=\"emf_pf_report_view\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_pf_report_view',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fpf_report_viewlist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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
		if ($this->BasicSearch->getKeyword() <> "") {
			$sQry .= "&" . EW_TABLE_BASIC_SEARCH . "=" . urlencode($this->BasicSearch->getKeyword()) . "&" . EW_TABLE_BASIC_SEARCH_TYPE . "=" . urlencode($this->BasicSearch->getType());
		}
		$this->AddSearchQueryString($sQry, $this->invoice_no); // invoice_no
		$this->AddSearchQueryString($sQry, $this->transaction_date); // transaction_date
		$this->AddSearchQueryString($sQry, $this->description); // description
		$this->AddSearchQueryString($sQry, $this->customer_name); // customer_name
		$this->AddSearchQueryString($sQry, $this->reference_customer); // reference_customer
		$this->AddSearchQueryString($sQry, $this->created_employee); // created_employee
		$this->AddSearchQueryString($sQry, $this->pf_amount); // pf_amount
		$this->AddSearchQueryString($sQry, $this->quantity); // quantity
		$this->AddSearchQueryString($sQry, $this->total_pf_amount); // total_pf_amount
		$this->AddSearchQueryString($sQry, $this->invoice_amount); // invoice_amount
		$this->AddSearchQueryString($sQry, $this->stock_id); // stock_id
		$this->AddSearchQueryString($sQry, $this->unit_price); // unit_price
		$this->AddSearchQueryString($sQry, $this->unit_tax); // unit_tax
		$this->AddSearchQueryString($sQry, $this->discount_percent); // discount_percent
		$this->AddSearchQueryString($sQry, $this->discount_amount); // discount_amount
		$this->AddSearchQueryString($sQry, $this->govt_fee); // govt_fee
		$this->AddSearchQueryString($sQry, $this->bank_service_charge); // bank_service_charge
		$this->AddSearchQueryString($sQry, $this->bank_service_charge_vat); // bank_service_charge_vat
		$this->AddSearchQueryString($sQry, $this->transaction_id); // transaction_id
		$this->AddSearchQueryString($sQry, $this->user_commission); // user_commission
		$this->AddSearchQueryString($sQry, $this->created_by); // created_by
		$this->AddSearchQueryString($sQry, $this->updated_by); // updated_by

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
		case "x_customer_name":
			$sSqlWrk = "";
				switch (@$gsLanguage) {
					case "ar":
						$sSqlWrk = "SELECT `name` AS `LinkFld`, `name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_debtors_master`";
						$sWhereWrk = "{filter}";
						$fld->LookupFilters = array("dx1" => '`name`');
						break;
					case "en":
						$sSqlWrk = "SELECT `name` AS `LinkFld`, `name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_debtors_master`";
						$sWhereWrk = "{filter}";
						$fld->LookupFilters = array("dx1" => '`name`');
						break;
					default:
						$sSqlWrk = "SELECT `name` AS `LinkFld`, `name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_debtors_master`";
						$sWhereWrk = "{filter}";
						$fld->LookupFilters = array("dx1" => '`name`');
						break;
				}
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`name` IN ({filter_value})', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
				$this->Lookup_Selecting($this->customer_name, $sWhereWrk); // Call Lookup Selecting
				if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_created_employee":
			$sSqlWrk = "";
				switch (@$gsLanguage) {
					case "ar":
						$sSqlWrk = "SELECT `user_id` AS `LinkFld`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
						$sWhereWrk = "{filter}";
						$fld->LookupFilters = array("dx1" => '`user_id`');
						break;
					case "en":
						$sSqlWrk = "SELECT `user_id` AS `LinkFld`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
						$sWhereWrk = "{filter}";
						$fld->LookupFilters = array("dx1" => '`user_id`');
						break;
					default:
						$sSqlWrk = "SELECT `user_id` AS `LinkFld`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
						$sWhereWrk = "{filter}";
						$fld->LookupFilters = array("dx1" => '`user_id`');
						break;
				}
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`user_id` IN ({filter_value})', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
				$this->Lookup_Selecting($this->created_employee, $sWhereWrk); // Call Lookup Selecting
				if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_stock_id":
			$sSqlWrk = "";
				switch (@$gsLanguage) {
					case "ar":
						$sSqlWrk = "SELECT `stock_id` AS `LinkFld`, `description` AS `DispFld`, `long_description` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_stock_master`";
						$sWhereWrk = "{filter}";
						$fld->LookupFilters = array("dx1" => '`description`', "dx2" => '`long_description`');
						break;
					case "en":
						$sSqlWrk = "SELECT `stock_id` AS `LinkFld`, `description` AS `DispFld`, `long_description` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_stock_master`";
						$sWhereWrk = "{filter}";
						$fld->LookupFilters = array("dx1" => '`description`', "dx2" => '`long_description`');
						break;
					default:
						$sSqlWrk = "SELECT `stock_id` AS `LinkFld`, `description` AS `DispFld`, `long_description` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_stock_master`";
						$sWhereWrk = "{filter}";
						$fld->LookupFilters = array("dx1" => '`description`', "dx2" => '`long_description`');
						break;
				}
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`stock_id` IN ({filter_value})', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
				$this->Lookup_Selecting($this->stock_id, $sWhereWrk); // Call Lookup Selecting
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

		$this->stock_id->Visible = FALSE;
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
if (!isset($pf_report_view_list)) $pf_report_view_list = new cpf_report_view_list();

// Page init
$pf_report_view_list->Page_Init();

// Page main
$pf_report_view_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$pf_report_view_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($pf_report_view->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fpf_report_viewlist = new ew_Form("fpf_report_viewlist", "list");
fpf_report_viewlist.FormKeyCountName = '<?php echo $pf_report_view_list->FormKeyCountName ?>';

// Form_CustomValidate event
fpf_report_viewlist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid.
 	return true;
 }

// Use JavaScript validation or not
fpf_report_viewlist.ValidateRequired = <?php echo json_encode(EW_CLIENT_VALIDATE) ?>;

// Dynamic selection lists
fpf_report_viewlist.Lists["x_customer_name[]"] = {"LinkField":"x_name","Ajax":true,"AutoFill":false,"DisplayFields":["x_name","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"_0_debtors_master"};
fpf_report_viewlist.Lists["x_customer_name[]"].Data = "<?php echo $pf_report_view_list->customer_name->LookupFilterQuery(FALSE, "list") ?>";
fpf_report_viewlist.Lists["x_created_employee[]"] = {"LinkField":"x_user_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_user_id","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"_0_users"};
fpf_report_viewlist.Lists["x_created_employee[]"].Data = "<?php echo $pf_report_view_list->created_employee->LookupFilterQuery(FALSE, "list") ?>";
fpf_report_viewlist.Lists["x_stock_id"] = {"LinkField":"x_stock_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","x_long_description","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"_0_stock_master"};
fpf_report_viewlist.Lists["x_stock_id"].Data = "<?php echo $pf_report_view_list->stock_id->LookupFilterQuery(FALSE, "list") ?>";

// Form object for search
var CurrentSearchForm = fpf_report_viewlistsrch = new ew_Form("fpf_report_viewlistsrch");

// Validate function for search
fpf_report_viewlistsrch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_transaction_date");
	if (elm && !ew_CheckDateDef(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($pf_report_view->transaction_date->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate event
fpf_report_viewlistsrch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid.
 	return true;
 }

// Use JavaScript validation or not
fpf_report_viewlistsrch.ValidateRequired = <?php echo json_encode(EW_CLIENT_VALIDATE) ?>;

// Dynamic selection lists
fpf_report_viewlistsrch.Lists["x_customer_name[]"] = {"LinkField":"x_name","Ajax":true,"AutoFill":false,"DisplayFields":["x_name","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"_0_debtors_master"};
fpf_report_viewlistsrch.Lists["x_customer_name[]"].Data = "<?php echo $pf_report_view_list->customer_name->LookupFilterQuery(FALSE, "extbs") ?>";
fpf_report_viewlistsrch.Lists["x_created_employee[]"] = {"LinkField":"x_user_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_user_id","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"_0_users"};
fpf_report_viewlistsrch.Lists["x_created_employee[]"].Data = "<?php echo $pf_report_view_list->created_employee->LookupFilterQuery(FALSE, "extbs") ?>";
fpf_report_viewlistsrch.Lists["x_stock_id"] = {"LinkField":"x_stock_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","x_long_description","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"_0_stock_master"};
fpf_report_viewlistsrch.Lists["x_stock_id"].Data = "<?php echo $pf_report_view_list->stock_id->LookupFilterQuery(FALSE, "extbs") ?>";
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($pf_report_view->Export == "") { ?>
<div class="ewToolbar">
<?php if ($pf_report_view_list->TotalRecs > 0 && $pf_report_view_list->ExportOptions->Visible()) { ?>
<?php $pf_report_view_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($pf_report_view_list->SearchOptions->Visible()) { ?>
<?php $pf_report_view_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($pf_report_view_list->FilterOptions->Visible()) { ?>
<?php $pf_report_view_list->FilterOptions->Render("body") ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
	$bSelectLimit = $pf_report_view_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($pf_report_view_list->TotalRecs <= 0)
			$pf_report_view_list->TotalRecs = $pf_report_view->ListRecordCount();
	} else {
		if (!$pf_report_view_list->Recordset && ($pf_report_view_list->Recordset = $pf_report_view_list->LoadRecordset()))
			$pf_report_view_list->TotalRecs = $pf_report_view_list->Recordset->RecordCount();
	}
	$pf_report_view_list->StartRec = 1;
	if ($pf_report_view_list->DisplayRecs <= 0 || ($pf_report_view->Export <> "" && $pf_report_view->ExportAll)) // Display all records
		$pf_report_view_list->DisplayRecs = $pf_report_view_list->TotalRecs;
	if (!($pf_report_view->Export <> "" && $pf_report_view->ExportAll))
		$pf_report_view_list->SetupStartRec(); // Set up start record position
	if ($bSelectLimit)
		$pf_report_view_list->Recordset = $pf_report_view_list->LoadRecordset($pf_report_view_list->StartRec-1, $pf_report_view_list->DisplayRecs);

	// Set no record found message
	if ($pf_report_view->CurrentAction == "" && $pf_report_view_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$pf_report_view_list->setWarningMessage(ew_DeniedMsg());
		if ($pf_report_view_list->SearchWhere == "0=101")
			$pf_report_view_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$pf_report_view_list->setWarningMessage($Language->Phrase("NoRecord"));
	}
$pf_report_view_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($pf_report_view->Export == "" && $pf_report_view->CurrentAction == "") { ?>
<form name="fpf_report_viewlistsrch" id="fpf_report_viewlistsrch" class="form-inline ewForm ewExtSearchForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($pf_report_view_list->SearchWhere <> "") ? " in" : " in"; ?>
<div id="fpf_report_viewlistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="pf_report_view">
	<div class="ewBasicSearch">
<?php
if ($gsSearchError == "")
	$pf_report_view_list->LoadAdvancedSearch(); // Load advanced search

// Render for search
$pf_report_view->RowType = EW_ROWTYPE_SEARCH;

// Render row
$pf_report_view->ResetAttrs();
$pf_report_view_list->RenderRow();
?>
<div id="xsr_1" class="ewRow">
<?php if ($pf_report_view->invoice_no->Visible) { // invoice_no ?>
	<div id="xsc_invoice_no" class="ewCell form-group">
		<label for="x_invoice_no" class="ewSearchCaption ewLabel"><?php echo $pf_report_view->invoice_no->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_invoice_no" id="z_invoice_no" value="LIKE"></span>
		<span class="ewSearchField">
<input type="text" data-table="pf_report_view" data-field="x_invoice_no" name="x_invoice_no" id="x_invoice_no" size="30" maxlength="60" placeholder="<?php echo ew_HtmlEncode($pf_report_view->invoice_no->getPlaceHolder()) ?>" value="<?php echo $pf_report_view->invoice_no->EditValue ?>"<?php echo $pf_report_view->invoice_no->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_2" class="ewRow">
<?php if ($pf_report_view->transaction_date->Visible) { // transaction_date ?>
	<div id="xsc_transaction_date" class="ewCell form-group">
		<label for="x_transaction_date" class="ewSearchCaption ewLabel"><?php echo $pf_report_view->transaction_date->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("BETWEEN") ?><input type="hidden" name="z_transaction_date" id="z_transaction_date" value="BETWEEN"></span>
		<span class="ewSearchField">
<input type="text" data-table="pf_report_view" data-field="x_transaction_date" data-format="2" name="x_transaction_date" id="x_transaction_date" placeholder="<?php echo ew_HtmlEncode($pf_report_view->transaction_date->getPlaceHolder()) ?>" value="<?php echo $pf_report_view->transaction_date->EditValue ?>"<?php echo $pf_report_view->transaction_date->EditAttributes() ?>>
<?php if (!$pf_report_view->transaction_date->ReadOnly && !$pf_report_view->transaction_date->Disabled && !isset($pf_report_view->transaction_date->EditAttrs["readonly"]) && !isset($pf_report_view->transaction_date->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateDateTimePicker("fpf_report_viewlistsrch", "x_transaction_date", {"ignoreReadonly":true,"useCurrent":false,"format":2});
</script>
<?php } ?>
</span>
		<span class="ewSearchCond btw1_transaction_date">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
		<span class="ewSearchField btw1_transaction_date">
<input type="text" data-table="pf_report_view" data-field="x_transaction_date" data-format="2" name="y_transaction_date" id="y_transaction_date" placeholder="<?php echo ew_HtmlEncode($pf_report_view->transaction_date->getPlaceHolder()) ?>" value="<?php echo $pf_report_view->transaction_date->EditValue2 ?>"<?php echo $pf_report_view->transaction_date->EditAttributes() ?>>
<?php if (!$pf_report_view->transaction_date->ReadOnly && !$pf_report_view->transaction_date->Disabled && !isset($pf_report_view->transaction_date->EditAttrs["readonly"]) && !isset($pf_report_view->transaction_date->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateDateTimePicker("fpf_report_viewlistsrch", "y_transaction_date", {"ignoreReadonly":true,"useCurrent":false,"format":2});
</script>
<?php } ?>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_3" class="ewRow">
<?php if ($pf_report_view->customer_name->Visible) { // customer_name ?>
	<div id="xsc_customer_name" class="ewCell form-group">
		<label for="x_customer_name" class="ewSearchCaption ewLabel"><?php echo $pf_report_view->customer_name->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_customer_name" id="z_customer_name" value="LIKE"></span>
		<span class="ewSearchField">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_customer_name"><?php echo (strval($pf_report_view->customer_name->AdvancedSearch->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $pf_report_view->customer_name->AdvancedSearch->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($pf_report_view->customer_name->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_customer_name[]',m:1,n:10});" class="ewLookupBtn btn btn-default btn-sm"<?php echo (($pf_report_view->customer_name->ReadOnly || $pf_report_view->customer_name->Disabled) ? " disabled" : "")?>><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="pf_report_view" data-field="x_customer_name" data-multiple="1" data-lookup="1" data-value-separator="<?php echo $pf_report_view->customer_name->DisplayValueSeparatorAttribute() ?>" name="x_customer_name[]" id="x_customer_name[]" value="<?php echo $pf_report_view->customer_name->AdvancedSearch->SearchValue ?>"<?php echo $pf_report_view->customer_name->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_4" class="ewRow">
<?php if ($pf_report_view->created_employee->Visible) { // created_employee ?>
	<div id="xsc_created_employee" class="ewCell form-group">
		<label for="x_created_employee" class="ewSearchCaption ewLabel"><?php echo $pf_report_view->created_employee->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_created_employee" id="z_created_employee" value="LIKE"></span>
		<span class="ewSearchField">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_created_employee"><?php echo (strval($pf_report_view->created_employee->AdvancedSearch->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $pf_report_view->created_employee->AdvancedSearch->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($pf_report_view->created_employee->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_created_employee[]',m:1,n:10});" class="ewLookupBtn btn btn-default btn-sm"<?php echo (($pf_report_view->created_employee->ReadOnly || $pf_report_view->created_employee->Disabled) ? " disabled" : "")?>><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="pf_report_view" data-field="x_created_employee" data-multiple="1" data-lookup="1" data-value-separator="<?php echo $pf_report_view->created_employee->DisplayValueSeparatorAttribute() ?>" name="x_created_employee[]" id="x_created_employee[]" value="<?php echo $pf_report_view->created_employee->AdvancedSearch->SearchValue ?>"<?php echo $pf_report_view->created_employee->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_5" class="ewRow">
<?php if ($pf_report_view->stock_id->Visible) { // stock_id ?>
	<div id="xsc_stock_id" class="ewCell form-group">
		<label for="x_stock_id" class="ewSearchCaption ewLabel"><?php echo $pf_report_view->stock_id->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_stock_id" id="z_stock_id" value="LIKE"></span>
		<span class="ewSearchField">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_stock_id"><?php echo (strval($pf_report_view->stock_id->AdvancedSearch->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $pf_report_view->stock_id->AdvancedSearch->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($pf_report_view->stock_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_stock_id',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"<?php echo (($pf_report_view->stock_id->ReadOnly || $pf_report_view->stock_id->Disabled) ? " disabled" : "")?>><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="pf_report_view" data-field="x_stock_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $pf_report_view->stock_id->DisplayValueSeparatorAttribute() ?>" name="x_stock_id" id="x_stock_id" value="<?php echo $pf_report_view->stock_id->AdvancedSearch->SearchValue ?>"<?php echo $pf_report_view->stock_id->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_6" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($pf_report_view_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($pf_report_view_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $pf_report_view_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($pf_report_view_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($pf_report_view_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($pf_report_view_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($pf_report_view_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
		</ul>
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("SearchBtn") ?></button>
	</div>
	</div>
</div>
	</div>
</div>
</form>
<?php } ?>
<?php } ?>
<?php $pf_report_view_list->ShowPageHeader(); ?>
<?php
$pf_report_view_list->ShowMessage();
?>
<?php if ($pf_report_view_list->TotalRecs > 0 || $pf_report_view->CurrentAction <> "") { ?>
<div class="box ewBox ewGrid<?php if ($pf_report_view_list->IsAddOrEdit()) { ?> ewGridAddEdit<?php } ?> pf_report_view">
<form name="fpf_report_viewlist" id="fpf_report_viewlist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($pf_report_view_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $pf_report_view_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="pf_report_view">
<div id="gmp_pf_report_view" class="<?php if (ew_IsResponsiveLayout()) { ?>table-responsive <?php } ?>ewGridMiddlePanel">
<?php if ($pf_report_view_list->TotalRecs > 0 || $pf_report_view->CurrentAction == "gridedit") { ?>
<table id="tbl_pf_report_viewlist" class="table ewTable">
<thead>
	<tr class="ewTableHeader">
<?php

// Header row
$pf_report_view_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$pf_report_view_list->RenderListOptions();

// Render list options (header, left)
$pf_report_view_list->ListOptions->Render("header", "left");
?>
<?php if ($pf_report_view->invoice_no->Visible) { // invoice_no ?>
	<?php if ($pf_report_view->SortUrl($pf_report_view->invoice_no) == "") { ?>
		<th data-name="invoice_no" class="<?php echo $pf_report_view->invoice_no->HeaderCellClass() ?>"><div id="elh_pf_report_view_invoice_no" class="pf_report_view_invoice_no"><div class="ewTableHeaderCaption"><?php echo $pf_report_view->invoice_no->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="invoice_no" class="<?php echo $pf_report_view->invoice_no->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pf_report_view->SortUrl($pf_report_view->invoice_no) ?>',1);"><div id="elh_pf_report_view_invoice_no" class="pf_report_view_invoice_no">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pf_report_view->invoice_no->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($pf_report_view->invoice_no->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pf_report_view->invoice_no->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($pf_report_view->transaction_date->Visible) { // transaction_date ?>
	<?php if ($pf_report_view->SortUrl($pf_report_view->transaction_date) == "") { ?>
		<th data-name="transaction_date" class="<?php echo $pf_report_view->transaction_date->HeaderCellClass() ?>"><div id="elh_pf_report_view_transaction_date" class="pf_report_view_transaction_date"><div class="ewTableHeaderCaption"><?php echo $pf_report_view->transaction_date->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="transaction_date" class="<?php echo $pf_report_view->transaction_date->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pf_report_view->SortUrl($pf_report_view->transaction_date) ?>',1);"><div id="elh_pf_report_view_transaction_date" class="pf_report_view_transaction_date">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pf_report_view->transaction_date->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pf_report_view->transaction_date->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pf_report_view->transaction_date->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($pf_report_view->description->Visible) { // description ?>
	<?php if ($pf_report_view->SortUrl($pf_report_view->description) == "") { ?>
		<th data-name="description" class="<?php echo $pf_report_view->description->HeaderCellClass() ?>"><div id="elh_pf_report_view_description" class="pf_report_view_description"><div class="ewTableHeaderCaption"><?php echo $pf_report_view->description->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="description" class="<?php echo $pf_report_view->description->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pf_report_view->SortUrl($pf_report_view->description) ?>',1);"><div id="elh_pf_report_view_description" class="pf_report_view_description">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pf_report_view->description->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($pf_report_view->description->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pf_report_view->description->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($pf_report_view->customer_name->Visible) { // customer_name ?>
	<?php if ($pf_report_view->SortUrl($pf_report_view->customer_name) == "") { ?>
		<th data-name="customer_name" class="<?php echo $pf_report_view->customer_name->HeaderCellClass() ?>"><div id="elh_pf_report_view_customer_name" class="pf_report_view_customer_name"><div class="ewTableHeaderCaption"><?php echo $pf_report_view->customer_name->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="customer_name" class="<?php echo $pf_report_view->customer_name->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pf_report_view->SortUrl($pf_report_view->customer_name) ?>',1);"><div id="elh_pf_report_view_customer_name" class="pf_report_view_customer_name">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pf_report_view->customer_name->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pf_report_view->customer_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pf_report_view->customer_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($pf_report_view->reference_customer->Visible) { // reference_customer ?>
	<?php if ($pf_report_view->SortUrl($pf_report_view->reference_customer) == "") { ?>
		<th data-name="reference_customer" class="<?php echo $pf_report_view->reference_customer->HeaderCellClass() ?>"><div id="elh_pf_report_view_reference_customer" class="pf_report_view_reference_customer"><div class="ewTableHeaderCaption"><?php echo $pf_report_view->reference_customer->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="reference_customer" class="<?php echo $pf_report_view->reference_customer->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pf_report_view->SortUrl($pf_report_view->reference_customer) ?>',1);"><div id="elh_pf_report_view_reference_customer" class="pf_report_view_reference_customer">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pf_report_view->reference_customer->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($pf_report_view->reference_customer->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pf_report_view->reference_customer->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($pf_report_view->created_employee->Visible) { // created_employee ?>
	<?php if ($pf_report_view->SortUrl($pf_report_view->created_employee) == "") { ?>
		<th data-name="created_employee" class="<?php echo $pf_report_view->created_employee->HeaderCellClass() ?>"><div id="elh_pf_report_view_created_employee" class="pf_report_view_created_employee"><div class="ewTableHeaderCaption"><?php echo $pf_report_view->created_employee->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="created_employee" class="<?php echo $pf_report_view->created_employee->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pf_report_view->SortUrl($pf_report_view->created_employee) ?>',1);"><div id="elh_pf_report_view_created_employee" class="pf_report_view_created_employee">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pf_report_view->created_employee->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pf_report_view->created_employee->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pf_report_view->created_employee->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($pf_report_view->pf_amount->Visible) { // pf_amount ?>
	<?php if ($pf_report_view->SortUrl($pf_report_view->pf_amount) == "") { ?>
		<th data-name="pf_amount" class="<?php echo $pf_report_view->pf_amount->HeaderCellClass() ?>"><div id="elh_pf_report_view_pf_amount" class="pf_report_view_pf_amount"><div class="ewTableHeaderCaption"><?php echo $pf_report_view->pf_amount->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="pf_amount" class="<?php echo $pf_report_view->pf_amount->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pf_report_view->SortUrl($pf_report_view->pf_amount) ?>',1);"><div id="elh_pf_report_view_pf_amount" class="pf_report_view_pf_amount">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pf_report_view->pf_amount->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pf_report_view->pf_amount->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pf_report_view->pf_amount->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($pf_report_view->quantity->Visible) { // quantity ?>
	<?php if ($pf_report_view->SortUrl($pf_report_view->quantity) == "") { ?>
		<th data-name="quantity" class="<?php echo $pf_report_view->quantity->HeaderCellClass() ?>"><div id="elh_pf_report_view_quantity" class="pf_report_view_quantity"><div class="ewTableHeaderCaption"><?php echo $pf_report_view->quantity->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="quantity" class="<?php echo $pf_report_view->quantity->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pf_report_view->SortUrl($pf_report_view->quantity) ?>',1);"><div id="elh_pf_report_view_quantity" class="pf_report_view_quantity">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pf_report_view->quantity->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pf_report_view->quantity->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pf_report_view->quantity->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($pf_report_view->total_pf_amount->Visible) { // total_pf_amount ?>
	<?php if ($pf_report_view->SortUrl($pf_report_view->total_pf_amount) == "") { ?>
		<th data-name="total_pf_amount" class="<?php echo $pf_report_view->total_pf_amount->HeaderCellClass() ?>"><div id="elh_pf_report_view_total_pf_amount" class="pf_report_view_total_pf_amount"><div class="ewTableHeaderCaption"><?php echo $pf_report_view->total_pf_amount->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="total_pf_amount" class="<?php echo $pf_report_view->total_pf_amount->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pf_report_view->SortUrl($pf_report_view->total_pf_amount) ?>',1);"><div id="elh_pf_report_view_total_pf_amount" class="pf_report_view_total_pf_amount">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pf_report_view->total_pf_amount->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pf_report_view->total_pf_amount->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pf_report_view->total_pf_amount->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($pf_report_view->stock_id->Visible) { // stock_id ?>
	<?php if ($pf_report_view->SortUrl($pf_report_view->stock_id) == "") { ?>
		<th data-name="stock_id" class="<?php echo $pf_report_view->stock_id->HeaderCellClass() ?>"><div id="elh_pf_report_view_stock_id" class="pf_report_view_stock_id"><div class="ewTableHeaderCaption"><?php echo $pf_report_view->stock_id->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="stock_id" class="<?php echo $pf_report_view->stock_id->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pf_report_view->SortUrl($pf_report_view->stock_id) ?>',1);"><div id="elh_pf_report_view_stock_id" class="pf_report_view_stock_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pf_report_view->stock_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pf_report_view->stock_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pf_report_view->stock_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php

// Render list options (header, right)
$pf_report_view_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($pf_report_view->ExportAll && $pf_report_view->Export <> "") {
	$pf_report_view_list->StopRec = $pf_report_view_list->TotalRecs;
} else {

	// Set the last record to display
	if ($pf_report_view_list->TotalRecs > $pf_report_view_list->StartRec + $pf_report_view_list->DisplayRecs - 1)
		$pf_report_view_list->StopRec = $pf_report_view_list->StartRec + $pf_report_view_list->DisplayRecs - 1;
	else
		$pf_report_view_list->StopRec = $pf_report_view_list->TotalRecs;
}
$pf_report_view_list->RecCnt = $pf_report_view_list->StartRec - 1;
if ($pf_report_view_list->Recordset && !$pf_report_view_list->Recordset->EOF) {
	$pf_report_view_list->Recordset->MoveFirst();
	$bSelectLimit = $pf_report_view_list->UseSelectLimit;
	if (!$bSelectLimit && $pf_report_view_list->StartRec > 1)
		$pf_report_view_list->Recordset->Move($pf_report_view_list->StartRec - 1);
} elseif (!$pf_report_view->AllowAddDeleteRow && $pf_report_view_list->StopRec == 0) {
	$pf_report_view_list->StopRec = $pf_report_view->GridAddRowCount;
}

// Initialize aggregate
$pf_report_view->RowType = EW_ROWTYPE_AGGREGATEINIT;
$pf_report_view->ResetAttrs();
$pf_report_view_list->RenderRow();
while ($pf_report_view_list->RecCnt < $pf_report_view_list->StopRec) {
	$pf_report_view_list->RecCnt++;
	if (intval($pf_report_view_list->RecCnt) >= intval($pf_report_view_list->StartRec)) {
		$pf_report_view_list->RowCnt++;

		// Set up key count
		$pf_report_view_list->KeyCount = $pf_report_view_list->RowIndex;

		// Init row class and style
		$pf_report_view->ResetAttrs();
		$pf_report_view->CssClass = "";
		if ($pf_report_view->CurrentAction == "gridadd") {
		} else {
			$pf_report_view_list->LoadRowValues($pf_report_view_list->Recordset); // Load row values
		}
		$pf_report_view->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$pf_report_view->RowAttrs = array_merge($pf_report_view->RowAttrs, array('data-rowindex'=>$pf_report_view_list->RowCnt, 'id'=>'r' . $pf_report_view_list->RowCnt . '_pf_report_view', 'data-rowtype'=>$pf_report_view->RowType));

		// Render row
		$pf_report_view_list->RenderRow();

		// Render list options
		$pf_report_view_list->RenderListOptions();
?>
	<tr<?php echo $pf_report_view->RowAttributes() ?>>
<?php

// Render list options (body, left)
$pf_report_view_list->ListOptions->Render("body", "left", $pf_report_view_list->RowCnt);
?>
	<?php if ($pf_report_view->invoice_no->Visible) { // invoice_no ?>
		<td data-name="invoice_no"<?php echo $pf_report_view->invoice_no->CellAttributes() ?>>
<span id="el<?php echo $pf_report_view_list->RowCnt ?>_pf_report_view_invoice_no" class="pf_report_view_invoice_no">
<span<?php echo $pf_report_view->invoice_no->ViewAttributes() ?>>
<?php echo $pf_report_view->invoice_no->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($pf_report_view->transaction_date->Visible) { // transaction_date ?>
		<td data-name="transaction_date"<?php echo $pf_report_view->transaction_date->CellAttributes() ?>>
<span id="el<?php echo $pf_report_view_list->RowCnt ?>_pf_report_view_transaction_date" class="pf_report_view_transaction_date">
<span<?php echo $pf_report_view->transaction_date->ViewAttributes() ?>>
<?php echo $pf_report_view->transaction_date->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($pf_report_view->description->Visible) { // description ?>
		<td data-name="description"<?php echo $pf_report_view->description->CellAttributes() ?>>
<span id="el<?php echo $pf_report_view_list->RowCnt ?>_pf_report_view_description" class="pf_report_view_description">
<span<?php echo $pf_report_view->description->ViewAttributes() ?>>
<?php echo $pf_report_view->description->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($pf_report_view->customer_name->Visible) { // customer_name ?>
		<td data-name="customer_name"<?php echo $pf_report_view->customer_name->CellAttributes() ?>>
<span id="el<?php echo $pf_report_view_list->RowCnt ?>_pf_report_view_customer_name" class="pf_report_view_customer_name">
<span<?php echo $pf_report_view->customer_name->ViewAttributes() ?>>
<?php echo $pf_report_view->customer_name->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($pf_report_view->reference_customer->Visible) { // reference_customer ?>
		<td data-name="reference_customer"<?php echo $pf_report_view->reference_customer->CellAttributes() ?>>
<span id="el<?php echo $pf_report_view_list->RowCnt ?>_pf_report_view_reference_customer" class="pf_report_view_reference_customer">
<span<?php echo $pf_report_view->reference_customer->ViewAttributes() ?>>
<?php echo $pf_report_view->reference_customer->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($pf_report_view->created_employee->Visible) { // created_employee ?>
		<td data-name="created_employee"<?php echo $pf_report_view->created_employee->CellAttributes() ?>>
<span id="el<?php echo $pf_report_view_list->RowCnt ?>_pf_report_view_created_employee" class="pf_report_view_created_employee">
<span<?php echo $pf_report_view->created_employee->ViewAttributes() ?>>
<?php echo $pf_report_view->created_employee->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($pf_report_view->pf_amount->Visible) { // pf_amount ?>
		<td data-name="pf_amount"<?php echo $pf_report_view->pf_amount->CellAttributes() ?>>
<span id="el<?php echo $pf_report_view_list->RowCnt ?>_pf_report_view_pf_amount" class="pf_report_view_pf_amount">
<span<?php echo $pf_report_view->pf_amount->ViewAttributes() ?>>
<?php echo $pf_report_view->pf_amount->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($pf_report_view->quantity->Visible) { // quantity ?>
		<td data-name="quantity"<?php echo $pf_report_view->quantity->CellAttributes() ?>>
<span id="el<?php echo $pf_report_view_list->RowCnt ?>_pf_report_view_quantity" class="pf_report_view_quantity">
<span<?php echo $pf_report_view->quantity->ViewAttributes() ?>>
<?php echo $pf_report_view->quantity->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($pf_report_view->total_pf_amount->Visible) { // total_pf_amount ?>
		<td data-name="total_pf_amount"<?php echo $pf_report_view->total_pf_amount->CellAttributes() ?>>
<span id="el<?php echo $pf_report_view_list->RowCnt ?>_pf_report_view_total_pf_amount" class="pf_report_view_total_pf_amount">
<span<?php echo $pf_report_view->total_pf_amount->ViewAttributes() ?>>
<?php echo $pf_report_view->total_pf_amount->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($pf_report_view->stock_id->Visible) { // stock_id ?>
		<td data-name="stock_id"<?php echo $pf_report_view->stock_id->CellAttributes() ?>>
<span id="el<?php echo $pf_report_view_list->RowCnt ?>_pf_report_view_stock_id" class="pf_report_view_stock_id">
<span<?php echo $pf_report_view->stock_id->ViewAttributes() ?>>
<?php echo $pf_report_view->stock_id->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$pf_report_view_list->ListOptions->Render("body", "right", $pf_report_view_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($pf_report_view->CurrentAction <> "gridadd")
		$pf_report_view_list->Recordset->MoveNext();
}
?>
</tbody>
<?php

// Render aggregate row
$pf_report_view->RowType = EW_ROWTYPE_AGGREGATE;
$pf_report_view->ResetAttrs();
$pf_report_view_list->RenderRow();
?>
<?php if ($pf_report_view_list->TotalRecs > 0 && ($pf_report_view->CurrentAction <> "gridadd" && $pf_report_view->CurrentAction <> "gridedit")) { ?>
<tfoot><!-- Table footer -->
	<tr class="ewTableFooter">
<?php

// Render list options
$pf_report_view_list->RenderListOptions();

// Render list options (footer, left)
$pf_report_view_list->ListOptions->Render("footer", "left");
?>
	<?php if ($pf_report_view->invoice_no->Visible) { // invoice_no ?>
		<td data-name="invoice_no" class="<?php echo $pf_report_view->invoice_no->FooterCellClass() ?>"><span id="elf_pf_report_view_invoice_no" class="pf_report_view_invoice_no">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($pf_report_view->transaction_date->Visible) { // transaction_date ?>
		<td data-name="transaction_date" class="<?php echo $pf_report_view->transaction_date->FooterCellClass() ?>"><span id="elf_pf_report_view_transaction_date" class="pf_report_view_transaction_date">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($pf_report_view->description->Visible) { // description ?>
		<td data-name="description" class="<?php echo $pf_report_view->description->FooterCellClass() ?>"><span id="elf_pf_report_view_description" class="pf_report_view_description">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($pf_report_view->customer_name->Visible) { // customer_name ?>
		<td data-name="customer_name" class="<?php echo $pf_report_view->customer_name->FooterCellClass() ?>"><span id="elf_pf_report_view_customer_name" class="pf_report_view_customer_name">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($pf_report_view->reference_customer->Visible) { // reference_customer ?>
		<td data-name="reference_customer" class="<?php echo $pf_report_view->reference_customer->FooterCellClass() ?>"><span id="elf_pf_report_view_reference_customer" class="pf_report_view_reference_customer">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($pf_report_view->created_employee->Visible) { // created_employee ?>
		<td data-name="created_employee" class="<?php echo $pf_report_view->created_employee->FooterCellClass() ?>"><span id="elf_pf_report_view_created_employee" class="pf_report_view_created_employee">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($pf_report_view->pf_amount->Visible) { // pf_amount ?>
		<td data-name="pf_amount" class="<?php echo $pf_report_view->pf_amount->FooterCellClass() ?>"><span id="elf_pf_report_view_pf_amount" class="pf_report_view_pf_amount">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($pf_report_view->quantity->Visible) { // quantity ?>
		<td data-name="quantity" class="<?php echo $pf_report_view->quantity->FooterCellClass() ?>"><span id="elf_pf_report_view_quantity" class="pf_report_view_quantity">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($pf_report_view->total_pf_amount->Visible) { // total_pf_amount ?>
		<td data-name="total_pf_amount" class="<?php echo $pf_report_view->total_pf_amount->FooterCellClass() ?>"><span id="elf_pf_report_view_total_pf_amount" class="pf_report_view_total_pf_amount">
<span class="ewAggregate"><?php echo $Language->Phrase("TOTAL") ?></span><span class="ewAggregateValue">
<?php echo $pf_report_view->total_pf_amount->ViewValue ?></span>
		</span></td>
	<?php } ?>
	<?php if ($pf_report_view->stock_id->Visible) { // stock_id ?>
		<td data-name="stock_id" class="<?php echo $pf_report_view->stock_id->FooterCellClass() ?>"><span id="elf_pf_report_view_stock_id" class="pf_report_view_stock_id">
		&nbsp;
		</span></td>
	<?php } ?>
<?php

// Render list options (footer, right)
$pf_report_view_list->ListOptions->Render("footer", "right");
?>
	</tr>
</tfoot>
<?php } ?>
</table>
<?php } ?>
<?php if ($pf_report_view->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($pf_report_view_list->Recordset)
	$pf_report_view_list->Recordset->Close();
?>
<?php if ($pf_report_view->Export == "") { ?>
<div class="box-footer ewGridLowerPanel">
<?php if ($pf_report_view->CurrentAction <> "gridadd" && $pf_report_view->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($pf_report_view_list->Pager)) $pf_report_view_list->Pager = new cPrevNextPager($pf_report_view_list->StartRec, $pf_report_view_list->DisplayRecs, $pf_report_view_list->TotalRecs, $pf_report_view_list->AutoHidePager) ?>
<?php if ($pf_report_view_list->Pager->RecordCount > 0 && $pf_report_view_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($pf_report_view_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $pf_report_view_list->PageUrl() ?>start=<?php echo $pf_report_view_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($pf_report_view_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $pf_report_view_list->PageUrl() ?>start=<?php echo $pf_report_view_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $pf_report_view_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($pf_report_view_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $pf_report_view_list->PageUrl() ?>start=<?php echo $pf_report_view_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($pf_report_view_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $pf_report_view_list->PageUrl() ?>start=<?php echo $pf_report_view_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $pf_report_view_list->Pager->PageCount ?></span>
</div>
<?php } ?>
<?php if ($pf_report_view_list->Pager->RecordCount > 0) { ?>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $pf_report_view_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $pf_report_view_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $pf_report_view_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($pf_report_view_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($pf_report_view_list->TotalRecs == 0 && $pf_report_view->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($pf_report_view_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($pf_report_view->Export == "") { ?>
<script type="text/javascript">
fpf_report_viewlistsrch.FilterList = <?php echo $pf_report_view_list->GetFilterList() ?>;
fpf_report_viewlistsrch.Init();
fpf_report_viewlist.Init();
</script>
<?php } ?>
<?php
$pf_report_view_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($pf_report_view->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$pf_report_view_list->Page_Terminate();
?>
