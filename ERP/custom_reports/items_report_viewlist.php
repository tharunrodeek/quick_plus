<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg14.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql14.php") ?>
<?php include_once "phpfn14.php" ?>
<?php include_once "items_report_viewinfo.php" ?>
<?php include_once "_0_usersinfo.php" ?>
<?php include_once "userfn14.php" ?>
<?php

//
// Page class
//

$items_report_view_list = NULL; // Initialize page object first

class citems_report_view_list extends citems_report_view {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = '{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}';

	// Table name
	var $TableName = 'items_report_view';

	// Page object name
	var $PageObjName = 'items_report_view_list';

	// Grid form hidden field names
	var $FormName = 'fitems_report_viewlist';
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

		// Table object (items_report_view)
		if (!isset($GLOBALS["items_report_view"]) || get_class($GLOBALS["items_report_view"]) == "citems_report_view") {
			$GLOBALS["items_report_view"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["items_report_view"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "items_report_viewadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "items_report_viewdelete.php";
		$this->MultiUpdateUrl = "items_report_viewupdate.php";

		// Table object (_0_users)
		if (!isset($GLOBALS['_0_users'])) $GLOBALS['_0_users'] = new c_0_users();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'items_report_view', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fitems_report_viewlistsrch";

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
		$this->stock_id->SetVisibility();
		$this->item_description->SetVisibility();
		$this->long_description->SetVisibility();
		$this->category_id->SetVisibility();
		$this->service_charge->SetVisibility();
		$this->govt_fee->SetVisibility();
		$this->pf_amount->SetVisibility();
		$this->bank_service_charge->SetVisibility();
		$this->bank_service_charge_vat->SetVisibility();
		$this->commission_loc_user->SetVisibility();
		$this->commission_non_loc_user->SetVisibility();

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
		global $EW_EXPORT, $items_report_view;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($items_report_view);
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
		if (count($arrKeyFlds) >= 1) {
			$this->stock_id->setFormValue($arrKeyFlds[0]);
		}
		return TRUE;
	}

	// Get list of filters
	function GetFilterList() {
		global $UserProfile;

		// Initialize
		$sFilterList = "";
		$sSavedFilterList = "";
		$sFilterList = ew_Concat($sFilterList, $this->stock_id->AdvancedSearch->ToJson(), ","); // Field stock_id
		$sFilterList = ew_Concat($sFilterList, $this->item_description->AdvancedSearch->ToJson(), ","); // Field item_description
		$sFilterList = ew_Concat($sFilterList, $this->long_description->AdvancedSearch->ToJson(), ","); // Field long_description
		$sFilterList = ew_Concat($sFilterList, $this->category_id->AdvancedSearch->ToJson(), ","); // Field category_id
		$sFilterList = ew_Concat($sFilterList, $this->service_charge->AdvancedSearch->ToJson(), ","); // Field service_charge
		$sFilterList = ew_Concat($sFilterList, $this->govt_fee->AdvancedSearch->ToJson(), ","); // Field govt_fee
		$sFilterList = ew_Concat($sFilterList, $this->pf_amount->AdvancedSearch->ToJson(), ","); // Field pf_amount
		$sFilterList = ew_Concat($sFilterList, $this->bank_service_charge->AdvancedSearch->ToJson(), ","); // Field bank_service_charge
		$sFilterList = ew_Concat($sFilterList, $this->bank_service_charge_vat->AdvancedSearch->ToJson(), ","); // Field bank_service_charge_vat
		$sFilterList = ew_Concat($sFilterList, $this->commission_loc_user->AdvancedSearch->ToJson(), ","); // Field commission_loc_user
		$sFilterList = ew_Concat($sFilterList, $this->commission_non_loc_user->AdvancedSearch->ToJson(), ","); // Field commission_non_loc_user
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
			$UserProfile->SetSearchFilters(CurrentUserName(), "fitems_report_viewlistsrch", $filters);

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

		// Field stock_id
		$this->stock_id->AdvancedSearch->SearchValue = @$filter["x_stock_id"];
		$this->stock_id->AdvancedSearch->SearchOperator = @$filter["z_stock_id"];
		$this->stock_id->AdvancedSearch->SearchCondition = @$filter["v_stock_id"];
		$this->stock_id->AdvancedSearch->SearchValue2 = @$filter["y_stock_id"];
		$this->stock_id->AdvancedSearch->SearchOperator2 = @$filter["w_stock_id"];
		$this->stock_id->AdvancedSearch->Save();

		// Field item_description
		$this->item_description->AdvancedSearch->SearchValue = @$filter["x_item_description"];
		$this->item_description->AdvancedSearch->SearchOperator = @$filter["z_item_description"];
		$this->item_description->AdvancedSearch->SearchCondition = @$filter["v_item_description"];
		$this->item_description->AdvancedSearch->SearchValue2 = @$filter["y_item_description"];
		$this->item_description->AdvancedSearch->SearchOperator2 = @$filter["w_item_description"];
		$this->item_description->AdvancedSearch->Save();

		// Field long_description
		$this->long_description->AdvancedSearch->SearchValue = @$filter["x_long_description"];
		$this->long_description->AdvancedSearch->SearchOperator = @$filter["z_long_description"];
		$this->long_description->AdvancedSearch->SearchCondition = @$filter["v_long_description"];
		$this->long_description->AdvancedSearch->SearchValue2 = @$filter["y_long_description"];
		$this->long_description->AdvancedSearch->SearchOperator2 = @$filter["w_long_description"];
		$this->long_description->AdvancedSearch->Save();

		// Field category_id
		$this->category_id->AdvancedSearch->SearchValue = @$filter["x_category_id"];
		$this->category_id->AdvancedSearch->SearchOperator = @$filter["z_category_id"];
		$this->category_id->AdvancedSearch->SearchCondition = @$filter["v_category_id"];
		$this->category_id->AdvancedSearch->SearchValue2 = @$filter["y_category_id"];
		$this->category_id->AdvancedSearch->SearchOperator2 = @$filter["w_category_id"];
		$this->category_id->AdvancedSearch->Save();

		// Field service_charge
		$this->service_charge->AdvancedSearch->SearchValue = @$filter["x_service_charge"];
		$this->service_charge->AdvancedSearch->SearchOperator = @$filter["z_service_charge"];
		$this->service_charge->AdvancedSearch->SearchCondition = @$filter["v_service_charge"];
		$this->service_charge->AdvancedSearch->SearchValue2 = @$filter["y_service_charge"];
		$this->service_charge->AdvancedSearch->SearchOperator2 = @$filter["w_service_charge"];
		$this->service_charge->AdvancedSearch->Save();

		// Field govt_fee
		$this->govt_fee->AdvancedSearch->SearchValue = @$filter["x_govt_fee"];
		$this->govt_fee->AdvancedSearch->SearchOperator = @$filter["z_govt_fee"];
		$this->govt_fee->AdvancedSearch->SearchCondition = @$filter["v_govt_fee"];
		$this->govt_fee->AdvancedSearch->SearchValue2 = @$filter["y_govt_fee"];
		$this->govt_fee->AdvancedSearch->SearchOperator2 = @$filter["w_govt_fee"];
		$this->govt_fee->AdvancedSearch->Save();

		// Field pf_amount
		$this->pf_amount->AdvancedSearch->SearchValue = @$filter["x_pf_amount"];
		$this->pf_amount->AdvancedSearch->SearchOperator = @$filter["z_pf_amount"];
		$this->pf_amount->AdvancedSearch->SearchCondition = @$filter["v_pf_amount"];
		$this->pf_amount->AdvancedSearch->SearchValue2 = @$filter["y_pf_amount"];
		$this->pf_amount->AdvancedSearch->SearchOperator2 = @$filter["w_pf_amount"];
		$this->pf_amount->AdvancedSearch->Save();

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

		// Field commission_loc_user
		$this->commission_loc_user->AdvancedSearch->SearchValue = @$filter["x_commission_loc_user"];
		$this->commission_loc_user->AdvancedSearch->SearchOperator = @$filter["z_commission_loc_user"];
		$this->commission_loc_user->AdvancedSearch->SearchCondition = @$filter["v_commission_loc_user"];
		$this->commission_loc_user->AdvancedSearch->SearchValue2 = @$filter["y_commission_loc_user"];
		$this->commission_loc_user->AdvancedSearch->SearchOperator2 = @$filter["w_commission_loc_user"];
		$this->commission_loc_user->AdvancedSearch->Save();

		// Field commission_non_loc_user
		$this->commission_non_loc_user->AdvancedSearch->SearchValue = @$filter["x_commission_non_loc_user"];
		$this->commission_non_loc_user->AdvancedSearch->SearchOperator = @$filter["z_commission_non_loc_user"];
		$this->commission_non_loc_user->AdvancedSearch->SearchCondition = @$filter["v_commission_non_loc_user"];
		$this->commission_non_loc_user->AdvancedSearch->SearchValue2 = @$filter["y_commission_non_loc_user"];
		$this->commission_non_loc_user->AdvancedSearch->SearchOperator2 = @$filter["w_commission_non_loc_user"];
		$this->commission_non_loc_user->AdvancedSearch->Save();
		$this->BasicSearch->setKeyword(@$filter[EW_TABLE_BASIC_SEARCH]);
		$this->BasicSearch->setType(@$filter[EW_TABLE_BASIC_SEARCH_TYPE]);
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere($Default = FALSE) {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->stock_id, $Default, FALSE); // stock_id
		$this->BuildSearchSql($sWhere, $this->item_description, $Default, FALSE); // item_description
		$this->BuildSearchSql($sWhere, $this->long_description, $Default, FALSE); // long_description
		$this->BuildSearchSql($sWhere, $this->category_id, $Default, FALSE); // category_id
		$this->BuildSearchSql($sWhere, $this->service_charge, $Default, FALSE); // service_charge
		$this->BuildSearchSql($sWhere, $this->govt_fee, $Default, FALSE); // govt_fee
		$this->BuildSearchSql($sWhere, $this->pf_amount, $Default, FALSE); // pf_amount
		$this->BuildSearchSql($sWhere, $this->bank_service_charge, $Default, FALSE); // bank_service_charge
		$this->BuildSearchSql($sWhere, $this->bank_service_charge_vat, $Default, FALSE); // bank_service_charge_vat
		$this->BuildSearchSql($sWhere, $this->commission_loc_user, $Default, FALSE); // commission_loc_user
		$this->BuildSearchSql($sWhere, $this->commission_non_loc_user, $Default, FALSE); // commission_non_loc_user

		// Set up search parm
		if (!$Default && $sWhere <> "" && in_array($this->Command, array("", "reset", "resetall"))) {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->stock_id->AdvancedSearch->Save(); // stock_id
			$this->item_description->AdvancedSearch->Save(); // item_description
			$this->long_description->AdvancedSearch->Save(); // long_description
			$this->category_id->AdvancedSearch->Save(); // category_id
			$this->service_charge->AdvancedSearch->Save(); // service_charge
			$this->govt_fee->AdvancedSearch->Save(); // govt_fee
			$this->pf_amount->AdvancedSearch->Save(); // pf_amount
			$this->bank_service_charge->AdvancedSearch->Save(); // bank_service_charge
			$this->bank_service_charge_vat->AdvancedSearch->Save(); // bank_service_charge_vat
			$this->commission_loc_user->AdvancedSearch->Save(); // commission_loc_user
			$this->commission_non_loc_user->AdvancedSearch->Save(); // commission_non_loc_user
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
		$this->BuildBasicSearchSQL($sWhere, $this->stock_id, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->item_description, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->long_description, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->category_id, $arKeywords, $type);
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
		if ($this->stock_id->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->item_description->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->long_description->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->category_id->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->service_charge->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->govt_fee->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->pf_amount->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->bank_service_charge->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->bank_service_charge_vat->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->commission_loc_user->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->commission_non_loc_user->AdvancedSearch->IssetSession())
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
		$this->stock_id->AdvancedSearch->UnsetSession();
		$this->item_description->AdvancedSearch->UnsetSession();
		$this->long_description->AdvancedSearch->UnsetSession();
		$this->category_id->AdvancedSearch->UnsetSession();
		$this->service_charge->AdvancedSearch->UnsetSession();
		$this->govt_fee->AdvancedSearch->UnsetSession();
		$this->pf_amount->AdvancedSearch->UnsetSession();
		$this->bank_service_charge->AdvancedSearch->UnsetSession();
		$this->bank_service_charge_vat->AdvancedSearch->UnsetSession();
		$this->commission_loc_user->AdvancedSearch->UnsetSession();
		$this->commission_non_loc_user->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->stock_id->AdvancedSearch->Load();
		$this->item_description->AdvancedSearch->Load();
		$this->long_description->AdvancedSearch->Load();
		$this->category_id->AdvancedSearch->Load();
		$this->service_charge->AdvancedSearch->Load();
		$this->govt_fee->AdvancedSearch->Load();
		$this->pf_amount->AdvancedSearch->Load();
		$this->bank_service_charge->AdvancedSearch->Load();
		$this->bank_service_charge_vat->AdvancedSearch->Load();
		$this->commission_loc_user->AdvancedSearch->Load();
		$this->commission_non_loc_user->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetupSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = @$_GET["order"];
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->stock_id); // stock_id
			$this->UpdateSort($this->item_description); // item_description
			$this->UpdateSort($this->long_description); // long_description
			$this->UpdateSort($this->category_id); // category_id
			$this->UpdateSort($this->service_charge); // service_charge
			$this->UpdateSort($this->govt_fee); // govt_fee
			$this->UpdateSort($this->pf_amount); // pf_amount
			$this->UpdateSort($this->bank_service_charge); // bank_service_charge
			$this->UpdateSort($this->bank_service_charge_vat); // bank_service_charge_vat
			$this->UpdateSort($this->commission_loc_user); // commission_loc_user
			$this->UpdateSort($this->commission_non_loc_user); // commission_non_loc_user
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
				$this->stock_id->setSort("");
				$this->item_description->setSort("");
				$this->long_description->setSort("");
				$this->category_id->setSort("");
				$this->service_charge->setSort("");
				$this->govt_fee->setSort("");
				$this->pf_amount->setSort("");
				$this->bank_service_charge->setSort("");
				$this->bank_service_charge_vat->setSort("");
				$this->commission_loc_user->setSort("");
				$this->commission_non_loc_user->setSort("");
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
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" class=\"ewMultiSelect\" value=\"" . ew_HtmlEncode($this->stock_id->CurrentValue) . "\" onclick=\"ew_ClickMultiCheckbox(event);\">";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fitems_report_viewlistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fitems_report_viewlistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
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
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fitems_report_viewlist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fitems_report_viewlistsrch\">" . $Language->Phrase("SearchLink") . "</button>";
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
		// stock_id

		$this->stock_id->AdvancedSearch->SearchValue = @$_GET["x_stock_id"];
		if ($this->stock_id->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->stock_id->AdvancedSearch->SearchOperator = @$_GET["z_stock_id"];

		// item_description
		$this->item_description->AdvancedSearch->SearchValue = @$_GET["x_item_description"];
		if ($this->item_description->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->item_description->AdvancedSearch->SearchOperator = @$_GET["z_item_description"];

		// long_description
		$this->long_description->AdvancedSearch->SearchValue = @$_GET["x_long_description"];
		if ($this->long_description->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->long_description->AdvancedSearch->SearchOperator = @$_GET["z_long_description"];

		// category_id
		$this->category_id->AdvancedSearch->SearchValue = @$_GET["x_category_id"];
		if ($this->category_id->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->category_id->AdvancedSearch->SearchOperator = @$_GET["z_category_id"];

		// service_charge
		$this->service_charge->AdvancedSearch->SearchValue = @$_GET["x_service_charge"];
		if ($this->service_charge->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->service_charge->AdvancedSearch->SearchOperator = @$_GET["z_service_charge"];

		// govt_fee
		$this->govt_fee->AdvancedSearch->SearchValue = @$_GET["x_govt_fee"];
		if ($this->govt_fee->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->govt_fee->AdvancedSearch->SearchOperator = @$_GET["z_govt_fee"];

		// pf_amount
		$this->pf_amount->AdvancedSearch->SearchValue = @$_GET["x_pf_amount"];
		if ($this->pf_amount->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->pf_amount->AdvancedSearch->SearchOperator = @$_GET["z_pf_amount"];

		// bank_service_charge
		$this->bank_service_charge->AdvancedSearch->SearchValue = @$_GET["x_bank_service_charge"];
		if ($this->bank_service_charge->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->bank_service_charge->AdvancedSearch->SearchOperator = @$_GET["z_bank_service_charge"];

		// bank_service_charge_vat
		$this->bank_service_charge_vat->AdvancedSearch->SearchValue = @$_GET["x_bank_service_charge_vat"];
		if ($this->bank_service_charge_vat->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->bank_service_charge_vat->AdvancedSearch->SearchOperator = @$_GET["z_bank_service_charge_vat"];

		// commission_loc_user
		$this->commission_loc_user->AdvancedSearch->SearchValue = @$_GET["x_commission_loc_user"];
		if ($this->commission_loc_user->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->commission_loc_user->AdvancedSearch->SearchOperator = @$_GET["z_commission_loc_user"];

		// commission_non_loc_user
		$this->commission_non_loc_user->AdvancedSearch->SearchValue = @$_GET["x_commission_non_loc_user"];
		if ($this->commission_non_loc_user->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->commission_non_loc_user->AdvancedSearch->SearchOperator = @$_GET["z_commission_non_loc_user"];
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
		$this->stock_id->setDbValue($row['stock_id']);
		$this->item_description->setDbValue($row['item_description']);
		$this->long_description->setDbValue($row['long_description']);
		$this->category_id->setDbValue($row['category_id']);
		$this->service_charge->setDbValue($row['service_charge']);
		$this->govt_fee->setDbValue($row['govt_fee']);
		$this->pf_amount->setDbValue($row['pf_amount']);
		$this->bank_service_charge->setDbValue($row['bank_service_charge']);
		$this->bank_service_charge_vat->setDbValue($row['bank_service_charge_vat']);
		$this->commission_loc_user->setDbValue($row['commission_loc_user']);
		$this->commission_non_loc_user->setDbValue($row['commission_non_loc_user']);
	}

	// Return a row with default values
	function NewRow() {
		$row = array();
		$row['stock_id'] = NULL;
		$row['item_description'] = NULL;
		$row['long_description'] = NULL;
		$row['category_id'] = NULL;
		$row['service_charge'] = NULL;
		$row['govt_fee'] = NULL;
		$row['pf_amount'] = NULL;
		$row['bank_service_charge'] = NULL;
		$row['bank_service_charge_vat'] = NULL;
		$row['commission_loc_user'] = NULL;
		$row['commission_non_loc_user'] = NULL;
		return $row;
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF)
			return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->stock_id->DbValue = $row['stock_id'];
		$this->item_description->DbValue = $row['item_description'];
		$this->long_description->DbValue = $row['long_description'];
		$this->category_id->DbValue = $row['category_id'];
		$this->service_charge->DbValue = $row['service_charge'];
		$this->govt_fee->DbValue = $row['govt_fee'];
		$this->pf_amount->DbValue = $row['pf_amount'];
		$this->bank_service_charge->DbValue = $row['bank_service_charge'];
		$this->bank_service_charge_vat->DbValue = $row['bank_service_charge_vat'];
		$this->commission_loc_user->DbValue = $row['commission_loc_user'];
		$this->commission_non_loc_user->DbValue = $row['commission_non_loc_user'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("stock_id")) <> "")
			$this->stock_id->CurrentValue = $this->getKey("stock_id"); // stock_id
		else
			$bValidKey = FALSE;

		// Load old record
		$this->OldRecordset = NULL;
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$this->OldRecordset = ew_LoadRecordset($sSql, $conn);
		}
		$this->LoadRowValues($this->OldRecordset); // Load row values
		return $bValidKey;
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
		if ($this->service_charge->FormValue == $this->service_charge->CurrentValue && is_numeric(ew_StrToFloat($this->service_charge->CurrentValue)))
			$this->service_charge->CurrentValue = ew_StrToFloat($this->service_charge->CurrentValue);

		// Convert decimal values if posted back
		if ($this->govt_fee->FormValue == $this->govt_fee->CurrentValue && is_numeric(ew_StrToFloat($this->govt_fee->CurrentValue)))
			$this->govt_fee->CurrentValue = ew_StrToFloat($this->govt_fee->CurrentValue);

		// Convert decimal values if posted back
		if ($this->pf_amount->FormValue == $this->pf_amount->CurrentValue && is_numeric(ew_StrToFloat($this->pf_amount->CurrentValue)))
			$this->pf_amount->CurrentValue = ew_StrToFloat($this->pf_amount->CurrentValue);

		// Convert decimal values if posted back
		if ($this->bank_service_charge->FormValue == $this->bank_service_charge->CurrentValue && is_numeric(ew_StrToFloat($this->bank_service_charge->CurrentValue)))
			$this->bank_service_charge->CurrentValue = ew_StrToFloat($this->bank_service_charge->CurrentValue);

		// Convert decimal values if posted back
		if ($this->bank_service_charge_vat->FormValue == $this->bank_service_charge_vat->CurrentValue && is_numeric(ew_StrToFloat($this->bank_service_charge_vat->CurrentValue)))
			$this->bank_service_charge_vat->CurrentValue = ew_StrToFloat($this->bank_service_charge_vat->CurrentValue);

		// Convert decimal values if posted back
		if ($this->commission_loc_user->FormValue == $this->commission_loc_user->CurrentValue && is_numeric(ew_StrToFloat($this->commission_loc_user->CurrentValue)))
			$this->commission_loc_user->CurrentValue = ew_StrToFloat($this->commission_loc_user->CurrentValue);

		// Convert decimal values if posted back
		if ($this->commission_non_loc_user->FormValue == $this->commission_non_loc_user->CurrentValue && is_numeric(ew_StrToFloat($this->commission_non_loc_user->CurrentValue)))
			$this->commission_non_loc_user->CurrentValue = ew_StrToFloat($this->commission_non_loc_user->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// stock_id
		// item_description
		// long_description
		// category_id
		// service_charge
		// govt_fee
		// pf_amount
		// bank_service_charge
		// bank_service_charge_vat
		// commission_loc_user
		// commission_non_loc_user

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// stock_id
		$this->stock_id->ViewValue = $this->stock_id->CurrentValue;
		$this->stock_id->ViewCustomAttributes = "";

		// item_description
		$this->item_description->ViewValue = $this->item_description->CurrentValue;
		$this->item_description->ViewCustomAttributes = "";

		// long_description
		$this->long_description->ViewValue = $this->long_description->CurrentValue;
		$this->long_description->ViewCustomAttributes = "";

		// category_id
		if (strval($this->category_id->CurrentValue) <> "") {
			$sFilterWrk = "`category_id`" . ew_SearchString("=", $this->category_id->CurrentValue, EW_DATATYPE_NUMBER, "");
		switch (@$gsLanguage) {
			case "ar":
				$sSqlWrk = "SELECT `category_id`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_stock_category`";
				$sWhereWrk = "";
				$this->category_id->LookupFilters = array();
				break;
			case "en":
				$sSqlWrk = "SELECT `category_id`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_stock_category`";
				$sWhereWrk = "";
				$this->category_id->LookupFilters = array();
				break;
			default:
				$sSqlWrk = "SELECT `category_id`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_stock_category`";
				$sWhereWrk = "";
				$this->category_id->LookupFilters = array();
				break;
		}
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->category_id, $sWhereWrk); // Call Lookup Selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->category_id->ViewValue = $this->category_id->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->category_id->ViewValue = $this->category_id->CurrentValue;
			}
		} else {
			$this->category_id->ViewValue = NULL;
		}
		$this->category_id->ViewCustomAttributes = "";

		// service_charge
		$this->service_charge->ViewValue = $this->service_charge->CurrentValue;
		$this->service_charge->ViewCustomAttributes = "";

		// govt_fee
		$this->govt_fee->ViewValue = $this->govt_fee->CurrentValue;
		$this->govt_fee->ViewCustomAttributes = "";

		// pf_amount
		$this->pf_amount->ViewValue = $this->pf_amount->CurrentValue;
		$this->pf_amount->ViewCustomAttributes = "";

		// bank_service_charge
		$this->bank_service_charge->ViewValue = $this->bank_service_charge->CurrentValue;
		$this->bank_service_charge->ViewCustomAttributes = "";

		// bank_service_charge_vat
		$this->bank_service_charge_vat->ViewValue = $this->bank_service_charge_vat->CurrentValue;
		$this->bank_service_charge_vat->ViewCustomAttributes = "";

		// commission_loc_user
		$this->commission_loc_user->ViewValue = $this->commission_loc_user->CurrentValue;
		$this->commission_loc_user->ViewCustomAttributes = "";

		// commission_non_loc_user
		$this->commission_non_loc_user->ViewValue = $this->commission_non_loc_user->CurrentValue;
		$this->commission_non_loc_user->ViewCustomAttributes = "";

			// stock_id
			$this->stock_id->LinkCustomAttributes = "";
			$this->stock_id->HrefValue = "";
			$this->stock_id->TooltipValue = "";

			// item_description
			$this->item_description->LinkCustomAttributes = "";
			$this->item_description->HrefValue = "";
			$this->item_description->TooltipValue = "";

			// long_description
			$this->long_description->LinkCustomAttributes = "";
			$this->long_description->HrefValue = "";
			$this->long_description->TooltipValue = "";

			// category_id
			$this->category_id->LinkCustomAttributes = "";
			$this->category_id->HrefValue = "";
			$this->category_id->TooltipValue = "";

			// service_charge
			$this->service_charge->LinkCustomAttributes = "";
			$this->service_charge->HrefValue = "";
			$this->service_charge->TooltipValue = "";

			// govt_fee
			$this->govt_fee->LinkCustomAttributes = "";
			$this->govt_fee->HrefValue = "";
			$this->govt_fee->TooltipValue = "";

			// pf_amount
			$this->pf_amount->LinkCustomAttributes = "";
			$this->pf_amount->HrefValue = "";
			$this->pf_amount->TooltipValue = "";

			// bank_service_charge
			$this->bank_service_charge->LinkCustomAttributes = "";
			$this->bank_service_charge->HrefValue = "";
			$this->bank_service_charge->TooltipValue = "";

			// bank_service_charge_vat
			$this->bank_service_charge_vat->LinkCustomAttributes = "";
			$this->bank_service_charge_vat->HrefValue = "";
			$this->bank_service_charge_vat->TooltipValue = "";

			// commission_loc_user
			$this->commission_loc_user->LinkCustomAttributes = "";
			$this->commission_loc_user->HrefValue = "";
			$this->commission_loc_user->TooltipValue = "";

			// commission_non_loc_user
			$this->commission_non_loc_user->LinkCustomAttributes = "";
			$this->commission_non_loc_user->HrefValue = "";
			$this->commission_non_loc_user->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// stock_id
			$this->stock_id->EditAttrs["class"] = "form-control";
			$this->stock_id->EditCustomAttributes = "";
			$this->stock_id->EditValue = ew_HtmlEncode($this->stock_id->AdvancedSearch->SearchValue);
			$this->stock_id->PlaceHolder = ew_RemoveHtml($this->stock_id->FldCaption());

			// item_description
			$this->item_description->EditAttrs["class"] = "form-control";
			$this->item_description->EditCustomAttributes = "";
			$this->item_description->EditValue = ew_HtmlEncode($this->item_description->AdvancedSearch->SearchValue);
			$this->item_description->PlaceHolder = ew_RemoveHtml($this->item_description->FldCaption());

			// long_description
			$this->long_description->EditAttrs["class"] = "form-control";
			$this->long_description->EditCustomAttributes = "";
			$this->long_description->EditValue = ew_HtmlEncode($this->long_description->AdvancedSearch->SearchValue);
			$this->long_description->PlaceHolder = ew_RemoveHtml($this->long_description->FldCaption());

			// category_id
			$this->category_id->EditAttrs["class"] = "form-control";
			$this->category_id->EditCustomAttributes = "";
			if (trim(strval($this->category_id->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`category_id`" . ew_SearchString("=", $this->category_id->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER, "");
			}
			switch (@$gsLanguage) {
				case "ar":
					$sSqlWrk = "SELECT `category_id`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_stock_category`";
					$sWhereWrk = "";
					$this->category_id->LookupFilters = array();
					break;
				case "en":
					$sSqlWrk = "SELECT `category_id`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_stock_category`";
					$sWhereWrk = "";
					$this->category_id->LookupFilters = array();
					break;
				default:
					$sSqlWrk = "SELECT `category_id`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_stock_category`";
					$sWhereWrk = "";
					$this->category_id->LookupFilters = array();
					break;
			}
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->category_id, $sWhereWrk); // Call Lookup Selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->category_id->EditValue = $arwrk;

			// service_charge
			$this->service_charge->EditAttrs["class"] = "form-control";
			$this->service_charge->EditCustomAttributes = "";
			$this->service_charge->EditValue = ew_HtmlEncode($this->service_charge->AdvancedSearch->SearchValue);
			$this->service_charge->PlaceHolder = ew_RemoveHtml($this->service_charge->FldCaption());

			// govt_fee
			$this->govt_fee->EditAttrs["class"] = "form-control";
			$this->govt_fee->EditCustomAttributes = "";
			$this->govt_fee->EditValue = ew_HtmlEncode($this->govt_fee->AdvancedSearch->SearchValue);
			$this->govt_fee->PlaceHolder = ew_RemoveHtml($this->govt_fee->FldCaption());

			// pf_amount
			$this->pf_amount->EditAttrs["class"] = "form-control";
			$this->pf_amount->EditCustomAttributes = "";
			$this->pf_amount->EditValue = ew_HtmlEncode($this->pf_amount->AdvancedSearch->SearchValue);
			$this->pf_amount->PlaceHolder = ew_RemoveHtml($this->pf_amount->FldCaption());

			// bank_service_charge
			$this->bank_service_charge->EditAttrs["class"] = "form-control";
			$this->bank_service_charge->EditCustomAttributes = "";
			$this->bank_service_charge->EditValue = ew_HtmlEncode($this->bank_service_charge->AdvancedSearch->SearchValue);
			$this->bank_service_charge->PlaceHolder = ew_RemoveHtml($this->bank_service_charge->FldCaption());

			// bank_service_charge_vat
			$this->bank_service_charge_vat->EditAttrs["class"] = "form-control";
			$this->bank_service_charge_vat->EditCustomAttributes = "";
			$this->bank_service_charge_vat->EditValue = ew_HtmlEncode($this->bank_service_charge_vat->AdvancedSearch->SearchValue);
			$this->bank_service_charge_vat->PlaceHolder = ew_RemoveHtml($this->bank_service_charge_vat->FldCaption());

			// commission_loc_user
			$this->commission_loc_user->EditAttrs["class"] = "form-control";
			$this->commission_loc_user->EditCustomAttributes = "";
			$this->commission_loc_user->EditValue = ew_HtmlEncode($this->commission_loc_user->AdvancedSearch->SearchValue);
			$this->commission_loc_user->PlaceHolder = ew_RemoveHtml($this->commission_loc_user->FldCaption());

			// commission_non_loc_user
			$this->commission_non_loc_user->EditAttrs["class"] = "form-control";
			$this->commission_non_loc_user->EditCustomAttributes = "";
			$this->commission_non_loc_user->EditValue = ew_HtmlEncode($this->commission_non_loc_user->AdvancedSearch->SearchValue);
			$this->commission_non_loc_user->PlaceHolder = ew_RemoveHtml($this->commission_non_loc_user->FldCaption());
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
		$this->stock_id->AdvancedSearch->Load();
		$this->item_description->AdvancedSearch->Load();
		$this->long_description->AdvancedSearch->Load();
		$this->category_id->AdvancedSearch->Load();
		$this->service_charge->AdvancedSearch->Load();
		$this->govt_fee->AdvancedSearch->Load();
		$this->pf_amount->AdvancedSearch->Load();
		$this->bank_service_charge->AdvancedSearch->Load();
		$this->bank_service_charge_vat->AdvancedSearch->Load();
		$this->commission_loc_user->AdvancedSearch->Load();
		$this->commission_non_loc_user->AdvancedSearch->Load();
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
		$item->Body = "<button id=\"emf_items_report_view\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_items_report_view',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fitems_report_viewlist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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
		$this->AddSearchQueryString($sQry, $this->stock_id); // stock_id
		$this->AddSearchQueryString($sQry, $this->item_description); // item_description
		$this->AddSearchQueryString($sQry, $this->long_description); // long_description
		$this->AddSearchQueryString($sQry, $this->category_id); // category_id
		$this->AddSearchQueryString($sQry, $this->service_charge); // service_charge
		$this->AddSearchQueryString($sQry, $this->govt_fee); // govt_fee
		$this->AddSearchQueryString($sQry, $this->pf_amount); // pf_amount
		$this->AddSearchQueryString($sQry, $this->bank_service_charge); // bank_service_charge
		$this->AddSearchQueryString($sQry, $this->bank_service_charge_vat); // bank_service_charge_vat
		$this->AddSearchQueryString($sQry, $this->commission_loc_user); // commission_loc_user
		$this->AddSearchQueryString($sQry, $this->commission_non_loc_user); // commission_non_loc_user

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
		case "x_category_id":
			$sSqlWrk = "";
				switch (@$gsLanguage) {
					case "ar":
						$sSqlWrk = "SELECT `category_id` AS `LinkFld`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_stock_category`";
						$sWhereWrk = "";
						$fld->LookupFilters = array();
						break;
					case "en":
						$sSqlWrk = "SELECT `category_id` AS `LinkFld`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_stock_category`";
						$sWhereWrk = "";
						$fld->LookupFilters = array();
						break;
					default:
						$sSqlWrk = "SELECT `category_id` AS `LinkFld`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_stock_category`";
						$sWhereWrk = "";
						$fld->LookupFilters = array();
						break;
				}
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`category_id` IN ({filter_value})', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
				$this->Lookup_Selecting($this->category_id, $sWhereWrk); // Call Lookup Selecting
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
if (!isset($items_report_view_list)) $items_report_view_list = new citems_report_view_list();

// Page init
$items_report_view_list->Page_Init();

// Page main
$items_report_view_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$items_report_view_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($items_report_view->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fitems_report_viewlist = new ew_Form("fitems_report_viewlist", "list");
fitems_report_viewlist.FormKeyCountName = '<?php echo $items_report_view_list->FormKeyCountName ?>';

// Form_CustomValidate event
fitems_report_viewlist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid.
 	return true;
 }

// Use JavaScript validation or not
fitems_report_viewlist.ValidateRequired = <?php echo json_encode(EW_CLIENT_VALIDATE) ?>;

// Dynamic selection lists
fitems_report_viewlist.Lists["x_category_id"] = {"LinkField":"x_category_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"_0_stock_category"};
fitems_report_viewlist.Lists["x_category_id"].Data = "<?php echo $items_report_view_list->category_id->LookupFilterQuery(FALSE, "list") ?>";

// Form object for search
var CurrentSearchForm = fitems_report_viewlistsrch = new ew_Form("fitems_report_viewlistsrch");

// Validate function for search
fitems_report_viewlistsrch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate event
fitems_report_viewlistsrch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid.
 	return true;
 }

// Use JavaScript validation or not
fitems_report_viewlistsrch.ValidateRequired = <?php echo json_encode(EW_CLIENT_VALIDATE) ?>;

// Dynamic selection lists
fitems_report_viewlistsrch.Lists["x_category_id"] = {"LinkField":"x_category_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"_0_stock_category"};
fitems_report_viewlistsrch.Lists["x_category_id"].Data = "<?php echo $items_report_view_list->category_id->LookupFilterQuery(FALSE, "extbs") ?>";
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($items_report_view->Export == "") { ?>
<div class="ewToolbar">
<?php if ($items_report_view_list->TotalRecs > 0 && $items_report_view_list->ExportOptions->Visible()) { ?>
<?php $items_report_view_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($items_report_view_list->SearchOptions->Visible()) { ?>
<?php $items_report_view_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($items_report_view_list->FilterOptions->Visible()) { ?>
<?php $items_report_view_list->FilterOptions->Render("body") ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
	$bSelectLimit = $items_report_view_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($items_report_view_list->TotalRecs <= 0)
			$items_report_view_list->TotalRecs = $items_report_view->ListRecordCount();
	} else {
		if (!$items_report_view_list->Recordset && ($items_report_view_list->Recordset = $items_report_view_list->LoadRecordset()))
			$items_report_view_list->TotalRecs = $items_report_view_list->Recordset->RecordCount();
	}
	$items_report_view_list->StartRec = 1;
	if ($items_report_view_list->DisplayRecs <= 0 || ($items_report_view->Export <> "" && $items_report_view->ExportAll)) // Display all records
		$items_report_view_list->DisplayRecs = $items_report_view_list->TotalRecs;
	if (!($items_report_view->Export <> "" && $items_report_view->ExportAll))
		$items_report_view_list->SetupStartRec(); // Set up start record position
	if ($bSelectLimit)
		$items_report_view_list->Recordset = $items_report_view_list->LoadRecordset($items_report_view_list->StartRec-1, $items_report_view_list->DisplayRecs);

	// Set no record found message
	if ($items_report_view->CurrentAction == "" && $items_report_view_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$items_report_view_list->setWarningMessage(ew_DeniedMsg());
		if ($items_report_view_list->SearchWhere == "0=101")
			$items_report_view_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$items_report_view_list->setWarningMessage($Language->Phrase("NoRecord"));
	}
$items_report_view_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($items_report_view->Export == "" && $items_report_view->CurrentAction == "") { ?>
<form name="fitems_report_viewlistsrch" id="fitems_report_viewlistsrch" class="form-inline ewForm ewExtSearchForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($items_report_view_list->SearchWhere <> "") ? " in" : " in"; ?>
<div id="fitems_report_viewlistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="items_report_view">
	<div class="ewBasicSearch">
<?php
if ($gsSearchError == "")
	$items_report_view_list->LoadAdvancedSearch(); // Load advanced search

// Render for search
$items_report_view->RowType = EW_ROWTYPE_SEARCH;

// Render row
$items_report_view->ResetAttrs();
$items_report_view_list->RenderRow();
?>
<div id="xsr_1" class="ewRow">
<?php if ($items_report_view->category_id->Visible) { // category_id ?>
	<div id="xsc_category_id" class="ewCell form-group">
		<label for="x_category_id" class="ewSearchCaption ewLabel"><?php echo $items_report_view->category_id->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_category_id" id="z_category_id" value="="></span>
		<span class="ewSearchField">
<select data-table="items_report_view" data-field="x_category_id" data-value-separator="<?php echo $items_report_view->category_id->DisplayValueSeparatorAttribute() ?>" id="x_category_id" name="x_category_id"<?php echo $items_report_view->category_id->EditAttributes() ?>>
<?php echo $items_report_view->category_id->SelectOptionListHtml("x_category_id") ?>
</select>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_2" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($items_report_view_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($items_report_view_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $items_report_view_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($items_report_view_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($items_report_view_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($items_report_view_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($items_report_view_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
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
<?php $items_report_view_list->ShowPageHeader(); ?>
<?php
$items_report_view_list->ShowMessage();
?>
<?php if ($items_report_view_list->TotalRecs > 0 || $items_report_view->CurrentAction <> "") { ?>
<div class="box ewBox ewGrid<?php if ($items_report_view_list->IsAddOrEdit()) { ?> ewGridAddEdit<?php } ?> items_report_view">
<form name="fitems_report_viewlist" id="fitems_report_viewlist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($items_report_view_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $items_report_view_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="items_report_view">
<div id="gmp_items_report_view" class="<?php if (ew_IsResponsiveLayout()) { ?>table-responsive <?php } ?>ewGridMiddlePanel">
<?php if ($items_report_view_list->TotalRecs > 0 || $items_report_view->CurrentAction == "gridedit") { ?>
<table id="tbl_items_report_viewlist" class="table ewTable">
<thead>
	<tr class="ewTableHeader">
<?php

// Header row
$items_report_view_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$items_report_view_list->RenderListOptions();

// Render list options (header, left)
$items_report_view_list->ListOptions->Render("header", "left");
?>
<?php if ($items_report_view->stock_id->Visible) { // stock_id ?>
	<?php if ($items_report_view->SortUrl($items_report_view->stock_id) == "") { ?>
		<th data-name="stock_id" class="<?php echo $items_report_view->stock_id->HeaderCellClass() ?>"><div id="elh_items_report_view_stock_id" class="items_report_view_stock_id"><div class="ewTableHeaderCaption"><?php echo $items_report_view->stock_id->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="stock_id" class="<?php echo $items_report_view->stock_id->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $items_report_view->SortUrl($items_report_view->stock_id) ?>',1);"><div id="elh_items_report_view_stock_id" class="items_report_view_stock_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $items_report_view->stock_id->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($items_report_view->stock_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($items_report_view->stock_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($items_report_view->item_description->Visible) { // item_description ?>
	<?php if ($items_report_view->SortUrl($items_report_view->item_description) == "") { ?>
		<th data-name="item_description" class="<?php echo $items_report_view->item_description->HeaderCellClass() ?>"><div id="elh_items_report_view_item_description" class="items_report_view_item_description"><div class="ewTableHeaderCaption"><?php echo $items_report_view->item_description->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="item_description" class="<?php echo $items_report_view->item_description->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $items_report_view->SortUrl($items_report_view->item_description) ?>',1);"><div id="elh_items_report_view_item_description" class="items_report_view_item_description">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $items_report_view->item_description->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($items_report_view->item_description->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($items_report_view->item_description->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($items_report_view->long_description->Visible) { // long_description ?>
	<?php if ($items_report_view->SortUrl($items_report_view->long_description) == "") { ?>
		<th data-name="long_description" class="<?php echo $items_report_view->long_description->HeaderCellClass() ?>"><div id="elh_items_report_view_long_description" class="items_report_view_long_description"><div class="ewTableHeaderCaption"><?php echo $items_report_view->long_description->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="long_description" class="<?php echo $items_report_view->long_description->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $items_report_view->SortUrl($items_report_view->long_description) ?>',1);"><div id="elh_items_report_view_long_description" class="items_report_view_long_description">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $items_report_view->long_description->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($items_report_view->long_description->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($items_report_view->long_description->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($items_report_view->category_id->Visible) { // category_id ?>
	<?php if ($items_report_view->SortUrl($items_report_view->category_id) == "") { ?>
		<th data-name="category_id" class="<?php echo $items_report_view->category_id->HeaderCellClass() ?>"><div id="elh_items_report_view_category_id" class="items_report_view_category_id"><div class="ewTableHeaderCaption"><?php echo $items_report_view->category_id->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="category_id" class="<?php echo $items_report_view->category_id->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $items_report_view->SortUrl($items_report_view->category_id) ?>',1);"><div id="elh_items_report_view_category_id" class="items_report_view_category_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $items_report_view->category_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($items_report_view->category_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($items_report_view->category_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($items_report_view->service_charge->Visible) { // service_charge ?>
	<?php if ($items_report_view->SortUrl($items_report_view->service_charge) == "") { ?>
		<th data-name="service_charge" class="<?php echo $items_report_view->service_charge->HeaderCellClass() ?>"><div id="elh_items_report_view_service_charge" class="items_report_view_service_charge"><div class="ewTableHeaderCaption"><?php echo $items_report_view->service_charge->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="service_charge" class="<?php echo $items_report_view->service_charge->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $items_report_view->SortUrl($items_report_view->service_charge) ?>',1);"><div id="elh_items_report_view_service_charge" class="items_report_view_service_charge">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $items_report_view->service_charge->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($items_report_view->service_charge->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($items_report_view->service_charge->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($items_report_view->govt_fee->Visible) { // govt_fee ?>
	<?php if ($items_report_view->SortUrl($items_report_view->govt_fee) == "") { ?>
		<th data-name="govt_fee" class="<?php echo $items_report_view->govt_fee->HeaderCellClass() ?>"><div id="elh_items_report_view_govt_fee" class="items_report_view_govt_fee"><div class="ewTableHeaderCaption"><?php echo $items_report_view->govt_fee->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="govt_fee" class="<?php echo $items_report_view->govt_fee->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $items_report_view->SortUrl($items_report_view->govt_fee) ?>',1);"><div id="elh_items_report_view_govt_fee" class="items_report_view_govt_fee">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $items_report_view->govt_fee->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($items_report_view->govt_fee->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($items_report_view->govt_fee->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($items_report_view->pf_amount->Visible) { // pf_amount ?>
	<?php if ($items_report_view->SortUrl($items_report_view->pf_amount) == "") { ?>
		<th data-name="pf_amount" class="<?php echo $items_report_view->pf_amount->HeaderCellClass() ?>"><div id="elh_items_report_view_pf_amount" class="items_report_view_pf_amount"><div class="ewTableHeaderCaption"><?php echo $items_report_view->pf_amount->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="pf_amount" class="<?php echo $items_report_view->pf_amount->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $items_report_view->SortUrl($items_report_view->pf_amount) ?>',1);"><div id="elh_items_report_view_pf_amount" class="items_report_view_pf_amount">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $items_report_view->pf_amount->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($items_report_view->pf_amount->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($items_report_view->pf_amount->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($items_report_view->bank_service_charge->Visible) { // bank_service_charge ?>
	<?php if ($items_report_view->SortUrl($items_report_view->bank_service_charge) == "") { ?>
		<th data-name="bank_service_charge" class="<?php echo $items_report_view->bank_service_charge->HeaderCellClass() ?>"><div id="elh_items_report_view_bank_service_charge" class="items_report_view_bank_service_charge"><div class="ewTableHeaderCaption"><?php echo $items_report_view->bank_service_charge->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="bank_service_charge" class="<?php echo $items_report_view->bank_service_charge->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $items_report_view->SortUrl($items_report_view->bank_service_charge) ?>',1);"><div id="elh_items_report_view_bank_service_charge" class="items_report_view_bank_service_charge">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $items_report_view->bank_service_charge->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($items_report_view->bank_service_charge->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($items_report_view->bank_service_charge->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($items_report_view->bank_service_charge_vat->Visible) { // bank_service_charge_vat ?>
	<?php if ($items_report_view->SortUrl($items_report_view->bank_service_charge_vat) == "") { ?>
		<th data-name="bank_service_charge_vat" class="<?php echo $items_report_view->bank_service_charge_vat->HeaderCellClass() ?>"><div id="elh_items_report_view_bank_service_charge_vat" class="items_report_view_bank_service_charge_vat"><div class="ewTableHeaderCaption"><?php echo $items_report_view->bank_service_charge_vat->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="bank_service_charge_vat" class="<?php echo $items_report_view->bank_service_charge_vat->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $items_report_view->SortUrl($items_report_view->bank_service_charge_vat) ?>',1);"><div id="elh_items_report_view_bank_service_charge_vat" class="items_report_view_bank_service_charge_vat">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $items_report_view->bank_service_charge_vat->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($items_report_view->bank_service_charge_vat->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($items_report_view->bank_service_charge_vat->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($items_report_view->commission_loc_user->Visible) { // commission_loc_user ?>
	<?php if ($items_report_view->SortUrl($items_report_view->commission_loc_user) == "") { ?>
		<th data-name="commission_loc_user" class="<?php echo $items_report_view->commission_loc_user->HeaderCellClass() ?>"><div id="elh_items_report_view_commission_loc_user" class="items_report_view_commission_loc_user"><div class="ewTableHeaderCaption"><?php echo $items_report_view->commission_loc_user->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="commission_loc_user" class="<?php echo $items_report_view->commission_loc_user->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $items_report_view->SortUrl($items_report_view->commission_loc_user) ?>',1);"><div id="elh_items_report_view_commission_loc_user" class="items_report_view_commission_loc_user">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $items_report_view->commission_loc_user->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($items_report_view->commission_loc_user->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($items_report_view->commission_loc_user->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($items_report_view->commission_non_loc_user->Visible) { // commission_non_loc_user ?>
	<?php if ($items_report_view->SortUrl($items_report_view->commission_non_loc_user) == "") { ?>
		<th data-name="commission_non_loc_user" class="<?php echo $items_report_view->commission_non_loc_user->HeaderCellClass() ?>"><div id="elh_items_report_view_commission_non_loc_user" class="items_report_view_commission_non_loc_user"><div class="ewTableHeaderCaption"><?php echo $items_report_view->commission_non_loc_user->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="commission_non_loc_user" class="<?php echo $items_report_view->commission_non_loc_user->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $items_report_view->SortUrl($items_report_view->commission_non_loc_user) ?>',1);"><div id="elh_items_report_view_commission_non_loc_user" class="items_report_view_commission_non_loc_user">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $items_report_view->commission_non_loc_user->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($items_report_view->commission_non_loc_user->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($items_report_view->commission_non_loc_user->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php

// Render list options (header, right)
$items_report_view_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($items_report_view->ExportAll && $items_report_view->Export <> "") {
	$items_report_view_list->StopRec = $items_report_view_list->TotalRecs;
} else {

	// Set the last record to display
	if ($items_report_view_list->TotalRecs > $items_report_view_list->StartRec + $items_report_view_list->DisplayRecs - 1)
		$items_report_view_list->StopRec = $items_report_view_list->StartRec + $items_report_view_list->DisplayRecs - 1;
	else
		$items_report_view_list->StopRec = $items_report_view_list->TotalRecs;
}
$items_report_view_list->RecCnt = $items_report_view_list->StartRec - 1;
if ($items_report_view_list->Recordset && !$items_report_view_list->Recordset->EOF) {
	$items_report_view_list->Recordset->MoveFirst();
	$bSelectLimit = $items_report_view_list->UseSelectLimit;
	if (!$bSelectLimit && $items_report_view_list->StartRec > 1)
		$items_report_view_list->Recordset->Move($items_report_view_list->StartRec - 1);
} elseif (!$items_report_view->AllowAddDeleteRow && $items_report_view_list->StopRec == 0) {
	$items_report_view_list->StopRec = $items_report_view->GridAddRowCount;
}

// Initialize aggregate
$items_report_view->RowType = EW_ROWTYPE_AGGREGATEINIT;
$items_report_view->ResetAttrs();
$items_report_view_list->RenderRow();
while ($items_report_view_list->RecCnt < $items_report_view_list->StopRec) {
	$items_report_view_list->RecCnt++;
	if (intval($items_report_view_list->RecCnt) >= intval($items_report_view_list->StartRec)) {
		$items_report_view_list->RowCnt++;

		// Set up key count
		$items_report_view_list->KeyCount = $items_report_view_list->RowIndex;

		// Init row class and style
		$items_report_view->ResetAttrs();
		$items_report_view->CssClass = "";
		if ($items_report_view->CurrentAction == "gridadd") {
		} else {
			$items_report_view_list->LoadRowValues($items_report_view_list->Recordset); // Load row values
		}
		$items_report_view->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$items_report_view->RowAttrs = array_merge($items_report_view->RowAttrs, array('data-rowindex'=>$items_report_view_list->RowCnt, 'id'=>'r' . $items_report_view_list->RowCnt . '_items_report_view', 'data-rowtype'=>$items_report_view->RowType));

		// Render row
		$items_report_view_list->RenderRow();

		// Render list options
		$items_report_view_list->RenderListOptions();
?>
	<tr<?php echo $items_report_view->RowAttributes() ?>>
<?php

// Render list options (body, left)
$items_report_view_list->ListOptions->Render("body", "left", $items_report_view_list->RowCnt);
?>
	<?php if ($items_report_view->stock_id->Visible) { // stock_id ?>
		<td data-name="stock_id"<?php echo $items_report_view->stock_id->CellAttributes() ?>>
<span id="el<?php echo $items_report_view_list->RowCnt ?>_items_report_view_stock_id" class="items_report_view_stock_id">
<span<?php echo $items_report_view->stock_id->ViewAttributes() ?>>
<?php echo $items_report_view->stock_id->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($items_report_view->item_description->Visible) { // item_description ?>
		<td data-name="item_description"<?php echo $items_report_view->item_description->CellAttributes() ?>>
<span id="el<?php echo $items_report_view_list->RowCnt ?>_items_report_view_item_description" class="items_report_view_item_description">
<span<?php echo $items_report_view->item_description->ViewAttributes() ?>>
<?php echo $items_report_view->item_description->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($items_report_view->long_description->Visible) { // long_description ?>
		<td data-name="long_description"<?php echo $items_report_view->long_description->CellAttributes() ?>>
<span id="el<?php echo $items_report_view_list->RowCnt ?>_items_report_view_long_description" class="items_report_view_long_description">
<span<?php echo $items_report_view->long_description->ViewAttributes() ?>>
<?php echo $items_report_view->long_description->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($items_report_view->category_id->Visible) { // category_id ?>
		<td data-name="category_id"<?php echo $items_report_view->category_id->CellAttributes() ?>>
<span id="el<?php echo $items_report_view_list->RowCnt ?>_items_report_view_category_id" class="items_report_view_category_id">
<span<?php echo $items_report_view->category_id->ViewAttributes() ?>>
<?php echo $items_report_view->category_id->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($items_report_view->service_charge->Visible) { // service_charge ?>
		<td data-name="service_charge"<?php echo $items_report_view->service_charge->CellAttributes() ?>>
<span id="el<?php echo $items_report_view_list->RowCnt ?>_items_report_view_service_charge" class="items_report_view_service_charge">
<span<?php echo $items_report_view->service_charge->ViewAttributes() ?>>
<?php echo $items_report_view->service_charge->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($items_report_view->govt_fee->Visible) { // govt_fee ?>
		<td data-name="govt_fee"<?php echo $items_report_view->govt_fee->CellAttributes() ?>>
<span id="el<?php echo $items_report_view_list->RowCnt ?>_items_report_view_govt_fee" class="items_report_view_govt_fee">
<span<?php echo $items_report_view->govt_fee->ViewAttributes() ?>>
<?php echo $items_report_view->govt_fee->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($items_report_view->pf_amount->Visible) { // pf_amount ?>
		<td data-name="pf_amount"<?php echo $items_report_view->pf_amount->CellAttributes() ?>>
<span id="el<?php echo $items_report_view_list->RowCnt ?>_items_report_view_pf_amount" class="items_report_view_pf_amount">
<span<?php echo $items_report_view->pf_amount->ViewAttributes() ?>>
<?php echo $items_report_view->pf_amount->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($items_report_view->bank_service_charge->Visible) { // bank_service_charge ?>
		<td data-name="bank_service_charge"<?php echo $items_report_view->bank_service_charge->CellAttributes() ?>>
<span id="el<?php echo $items_report_view_list->RowCnt ?>_items_report_view_bank_service_charge" class="items_report_view_bank_service_charge">
<span<?php echo $items_report_view->bank_service_charge->ViewAttributes() ?>>
<?php echo $items_report_view->bank_service_charge->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($items_report_view->bank_service_charge_vat->Visible) { // bank_service_charge_vat ?>
		<td data-name="bank_service_charge_vat"<?php echo $items_report_view->bank_service_charge_vat->CellAttributes() ?>>
<span id="el<?php echo $items_report_view_list->RowCnt ?>_items_report_view_bank_service_charge_vat" class="items_report_view_bank_service_charge_vat">
<span<?php echo $items_report_view->bank_service_charge_vat->ViewAttributes() ?>>
<?php echo $items_report_view->bank_service_charge_vat->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($items_report_view->commission_loc_user->Visible) { // commission_loc_user ?>
		<td data-name="commission_loc_user"<?php echo $items_report_view->commission_loc_user->CellAttributes() ?>>
<span id="el<?php echo $items_report_view_list->RowCnt ?>_items_report_view_commission_loc_user" class="items_report_view_commission_loc_user">
<span<?php echo $items_report_view->commission_loc_user->ViewAttributes() ?>>
<?php echo $items_report_view->commission_loc_user->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($items_report_view->commission_non_loc_user->Visible) { // commission_non_loc_user ?>
		<td data-name="commission_non_loc_user"<?php echo $items_report_view->commission_non_loc_user->CellAttributes() ?>>
<span id="el<?php echo $items_report_view_list->RowCnt ?>_items_report_view_commission_non_loc_user" class="items_report_view_commission_non_loc_user">
<span<?php echo $items_report_view->commission_non_loc_user->ViewAttributes() ?>>
<?php echo $items_report_view->commission_non_loc_user->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$items_report_view_list->ListOptions->Render("body", "right", $items_report_view_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($items_report_view->CurrentAction <> "gridadd")
		$items_report_view_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($items_report_view->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($items_report_view_list->Recordset)
	$items_report_view_list->Recordset->Close();
?>
<?php if ($items_report_view->Export == "") { ?>
<div class="box-footer ewGridLowerPanel">
<?php if ($items_report_view->CurrentAction <> "gridadd" && $items_report_view->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($items_report_view_list->Pager)) $items_report_view_list->Pager = new cPrevNextPager($items_report_view_list->StartRec, $items_report_view_list->DisplayRecs, $items_report_view_list->TotalRecs, $items_report_view_list->AutoHidePager) ?>
<?php if ($items_report_view_list->Pager->RecordCount > 0 && $items_report_view_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($items_report_view_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $items_report_view_list->PageUrl() ?>start=<?php echo $items_report_view_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($items_report_view_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $items_report_view_list->PageUrl() ?>start=<?php echo $items_report_view_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $items_report_view_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($items_report_view_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $items_report_view_list->PageUrl() ?>start=<?php echo $items_report_view_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($items_report_view_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $items_report_view_list->PageUrl() ?>start=<?php echo $items_report_view_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $items_report_view_list->Pager->PageCount ?></span>
</div>
<?php } ?>
<?php if ($items_report_view_list->Pager->RecordCount > 0) { ?>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $items_report_view_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $items_report_view_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $items_report_view_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($items_report_view_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($items_report_view_list->TotalRecs == 0 && $items_report_view->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($items_report_view_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($items_report_view->Export == "") { ?>
<script type="text/javascript">
fitems_report_viewlistsrch.FilterList = <?php echo $items_report_view_list->GetFilterList() ?>;
fitems_report_viewlistsrch.Init();
fitems_report_viewlist.Init();
</script>
<?php } ?>
<?php
$items_report_view_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($items_report_view->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$items_report_view_list->Page_Terminate();
?>
