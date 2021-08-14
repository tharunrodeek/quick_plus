<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg14.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql14.php") ?>
<?php include_once "phpfn14.php" ?>
<?php include_once "voided_trans_reprt_viewinfo.php" ?>
<?php include_once "_0_usersinfo.php" ?>
<?php include_once "userfn14.php" ?>
<?php

//
// Page class
//

$voided_trans_reprt_view_list = NULL; // Initialize page object first

class cvoided_trans_reprt_view_list extends cvoided_trans_reprt_view {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = '{46DD0EDD-E9C9-44DF-9E61-DEAB99FDD850}';

	// Table name
	var $TableName = 'voided_trans_reprt_view';

	// Page object name
	var $PageObjName = 'voided_trans_reprt_view_list';

	// Grid form hidden field names
	var $FormName = 'fvoided_trans_reprt_viewlist';
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

		// Table object (voided_trans_reprt_view)
		if (!isset($GLOBALS["voided_trans_reprt_view"]) || get_class($GLOBALS["voided_trans_reprt_view"]) == "cvoided_trans_reprt_view") {
			$GLOBALS["voided_trans_reprt_view"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["voided_trans_reprt_view"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "voided_trans_reprt_viewadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "voided_trans_reprt_viewdelete.php";
		$this->MultiUpdateUrl = "voided_trans_reprt_viewupdate.php";

		// Table object (_0_users)
		if (!isset($GLOBALS['_0_users'])) $GLOBALS['_0_users'] = new c_0_users();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'voided_trans_reprt_view', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fvoided_trans_reprt_viewlistsrch";

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
		$this->reference->SetVisibility();
		$this->voided_date->SetVisibility();
		$this->trans_date->SetVisibility();
		$this->voided_by->SetVisibility();
		$this->transaction_done_by->SetVisibility();
		$this->memo_->SetVisibility();
		$this->type->SetVisibility();
		$this->amount->SetVisibility();

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
		global $EW_EXPORT, $voided_trans_reprt_view;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($voided_trans_reprt_view);
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
		$sFilterList = ew_Concat($sFilterList, $this->reference->AdvancedSearch->ToJson(), ","); // Field reference
		$sFilterList = ew_Concat($sFilterList, $this->voided_date->AdvancedSearch->ToJson(), ","); // Field voided_date
		$sFilterList = ew_Concat($sFilterList, $this->trans_date->AdvancedSearch->ToJson(), ","); // Field trans_date
		$sFilterList = ew_Concat($sFilterList, $this->voided_by->AdvancedSearch->ToJson(), ","); // Field voided_by
		$sFilterList = ew_Concat($sFilterList, $this->transaction_done_by->AdvancedSearch->ToJson(), ","); // Field transaction_done_by
		$sFilterList = ew_Concat($sFilterList, $this->memo_->AdvancedSearch->ToJson(), ","); // Field memo_
		$sFilterList = ew_Concat($sFilterList, $this->type->AdvancedSearch->ToJson(), ","); // Field type
		$sFilterList = ew_Concat($sFilterList, $this->amount->AdvancedSearch->ToJson(), ","); // Field amount
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
			$UserProfile->SetSearchFilters(CurrentUserName(), "fvoided_trans_reprt_viewlistsrch", $filters);

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

		// Field reference
		$this->reference->AdvancedSearch->SearchValue = @$filter["x_reference"];
		$this->reference->AdvancedSearch->SearchOperator = @$filter["z_reference"];
		$this->reference->AdvancedSearch->SearchCondition = @$filter["v_reference"];
		$this->reference->AdvancedSearch->SearchValue2 = @$filter["y_reference"];
		$this->reference->AdvancedSearch->SearchOperator2 = @$filter["w_reference"];
		$this->reference->AdvancedSearch->Save();

		// Field voided_date
		$this->voided_date->AdvancedSearch->SearchValue = @$filter["x_voided_date"];
		$this->voided_date->AdvancedSearch->SearchOperator = @$filter["z_voided_date"];
		$this->voided_date->AdvancedSearch->SearchCondition = @$filter["v_voided_date"];
		$this->voided_date->AdvancedSearch->SearchValue2 = @$filter["y_voided_date"];
		$this->voided_date->AdvancedSearch->SearchOperator2 = @$filter["w_voided_date"];
		$this->voided_date->AdvancedSearch->Save();

		// Field trans_date
		$this->trans_date->AdvancedSearch->SearchValue = @$filter["x_trans_date"];
		$this->trans_date->AdvancedSearch->SearchOperator = @$filter["z_trans_date"];
		$this->trans_date->AdvancedSearch->SearchCondition = @$filter["v_trans_date"];
		$this->trans_date->AdvancedSearch->SearchValue2 = @$filter["y_trans_date"];
		$this->trans_date->AdvancedSearch->SearchOperator2 = @$filter["w_trans_date"];
		$this->trans_date->AdvancedSearch->Save();

		// Field voided_by
		$this->voided_by->AdvancedSearch->SearchValue = @$filter["x_voided_by"];
		$this->voided_by->AdvancedSearch->SearchOperator = @$filter["z_voided_by"];
		$this->voided_by->AdvancedSearch->SearchCondition = @$filter["v_voided_by"];
		$this->voided_by->AdvancedSearch->SearchValue2 = @$filter["y_voided_by"];
		$this->voided_by->AdvancedSearch->SearchOperator2 = @$filter["w_voided_by"];
		$this->voided_by->AdvancedSearch->Save();

		// Field transaction_done_by
		$this->transaction_done_by->AdvancedSearch->SearchValue = @$filter["x_transaction_done_by"];
		$this->transaction_done_by->AdvancedSearch->SearchOperator = @$filter["z_transaction_done_by"];
		$this->transaction_done_by->AdvancedSearch->SearchCondition = @$filter["v_transaction_done_by"];
		$this->transaction_done_by->AdvancedSearch->SearchValue2 = @$filter["y_transaction_done_by"];
		$this->transaction_done_by->AdvancedSearch->SearchOperator2 = @$filter["w_transaction_done_by"];
		$this->transaction_done_by->AdvancedSearch->Save();

		// Field memo_
		$this->memo_->AdvancedSearch->SearchValue = @$filter["x_memo_"];
		$this->memo_->AdvancedSearch->SearchOperator = @$filter["z_memo_"];
		$this->memo_->AdvancedSearch->SearchCondition = @$filter["v_memo_"];
		$this->memo_->AdvancedSearch->SearchValue2 = @$filter["y_memo_"];
		$this->memo_->AdvancedSearch->SearchOperator2 = @$filter["w_memo_"];
		$this->memo_->AdvancedSearch->Save();

		// Field type
		$this->type->AdvancedSearch->SearchValue = @$filter["x_type"];
		$this->type->AdvancedSearch->SearchOperator = @$filter["z_type"];
		$this->type->AdvancedSearch->SearchCondition = @$filter["v_type"];
		$this->type->AdvancedSearch->SearchValue2 = @$filter["y_type"];
		$this->type->AdvancedSearch->SearchOperator2 = @$filter["w_type"];
		$this->type->AdvancedSearch->Save();

		// Field amount
		$this->amount->AdvancedSearch->SearchValue = @$filter["x_amount"];
		$this->amount->AdvancedSearch->SearchOperator = @$filter["z_amount"];
		$this->amount->AdvancedSearch->SearchCondition = @$filter["v_amount"];
		$this->amount->AdvancedSearch->SearchValue2 = @$filter["y_amount"];
		$this->amount->AdvancedSearch->SearchOperator2 = @$filter["w_amount"];
		$this->amount->AdvancedSearch->Save();
		$this->BasicSearch->setKeyword(@$filter[EW_TABLE_BASIC_SEARCH]);
		$this->BasicSearch->setType(@$filter[EW_TABLE_BASIC_SEARCH_TYPE]);
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere($Default = FALSE) {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->reference, $Default, FALSE); // reference
		$this->BuildSearchSql($sWhere, $this->voided_date, $Default, FALSE); // voided_date
		$this->BuildSearchSql($sWhere, $this->trans_date, $Default, FALSE); // trans_date
		$this->BuildSearchSql($sWhere, $this->voided_by, $Default, FALSE); // voided_by
		$this->BuildSearchSql($sWhere, $this->transaction_done_by, $Default, FALSE); // transaction_done_by
		$this->BuildSearchSql($sWhere, $this->memo_, $Default, FALSE); // memo_
		$this->BuildSearchSql($sWhere, $this->type, $Default, FALSE); // type
		$this->BuildSearchSql($sWhere, $this->amount, $Default, FALSE); // amount

		// Set up search parm
		if (!$Default && $sWhere <> "" && in_array($this->Command, array("", "reset", "resetall"))) {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->reference->AdvancedSearch->Save(); // reference
			$this->voided_date->AdvancedSearch->Save(); // voided_date
			$this->trans_date->AdvancedSearch->Save(); // trans_date
			$this->voided_by->AdvancedSearch->Save(); // voided_by
			$this->transaction_done_by->AdvancedSearch->Save(); // transaction_done_by
			$this->memo_->AdvancedSearch->Save(); // memo_
			$this->type->AdvancedSearch->Save(); // type
			$this->amount->AdvancedSearch->Save(); // amount
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
		$this->BuildBasicSearchSQL($sWhere, $this->reference, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->voided_by, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->transaction_done_by, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->memo_, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->type, $arKeywords, $type);
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
		if ($this->reference->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->voided_date->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->trans_date->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->voided_by->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->transaction_done_by->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->memo_->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->type->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->amount->AdvancedSearch->IssetSession())
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
		$this->reference->AdvancedSearch->UnsetSession();
		$this->voided_date->AdvancedSearch->UnsetSession();
		$this->trans_date->AdvancedSearch->UnsetSession();
		$this->voided_by->AdvancedSearch->UnsetSession();
		$this->transaction_done_by->AdvancedSearch->UnsetSession();
		$this->memo_->AdvancedSearch->UnsetSession();
		$this->type->AdvancedSearch->UnsetSession();
		$this->amount->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->reference->AdvancedSearch->Load();
		$this->voided_date->AdvancedSearch->Load();
		$this->trans_date->AdvancedSearch->Load();
		$this->voided_by->AdvancedSearch->Load();
		$this->transaction_done_by->AdvancedSearch->Load();
		$this->memo_->AdvancedSearch->Load();
		$this->type->AdvancedSearch->Load();
		$this->amount->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetupSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = @$_GET["order"];
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->reference); // reference
			$this->UpdateSort($this->voided_date); // voided_date
			$this->UpdateSort($this->trans_date); // trans_date
			$this->UpdateSort($this->voided_by); // voided_by
			$this->UpdateSort($this->transaction_done_by); // transaction_done_by
			$this->UpdateSort($this->memo_); // memo_
			$this->UpdateSort($this->type); // type
			$this->UpdateSort($this->amount); // amount
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
				$this->reference->setSort("");
				$this->voided_date->setSort("");
				$this->trans_date->setSort("");
				$this->voided_by->setSort("");
				$this->transaction_done_by->setSort("");
				$this->memo_->setSort("");
				$this->type->setSort("");
				$this->amount->setSort("");
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fvoided_trans_reprt_viewlistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fvoided_trans_reprt_viewlistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
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
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fvoided_trans_reprt_viewlist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fvoided_trans_reprt_viewlistsrch\">" . $Language->Phrase("SearchLink") . "</button>";
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
		// reference

		$this->reference->AdvancedSearch->SearchValue = @$_GET["x_reference"];
		if ($this->reference->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->reference->AdvancedSearch->SearchOperator = @$_GET["z_reference"];

		// voided_date
		$this->voided_date->AdvancedSearch->SearchValue = @$_GET["x_voided_date"];
		if ($this->voided_date->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->voided_date->AdvancedSearch->SearchOperator = @$_GET["z_voided_date"];

		// trans_date
		$this->trans_date->AdvancedSearch->SearchValue = @$_GET["x_trans_date"];
		if ($this->trans_date->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->trans_date->AdvancedSearch->SearchOperator = @$_GET["z_trans_date"];

		// voided_by
		$this->voided_by->AdvancedSearch->SearchValue = @$_GET["x_voided_by"];
		if ($this->voided_by->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->voided_by->AdvancedSearch->SearchOperator = @$_GET["z_voided_by"];

		// transaction_done_by
		$this->transaction_done_by->AdvancedSearch->SearchValue = @$_GET["x_transaction_done_by"];
		if ($this->transaction_done_by->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->transaction_done_by->AdvancedSearch->SearchOperator = @$_GET["z_transaction_done_by"];

		// memo_
		$this->memo_->AdvancedSearch->SearchValue = @$_GET["x_memo_"];
		if ($this->memo_->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->memo_->AdvancedSearch->SearchOperator = @$_GET["z_memo_"];

		// type
		$this->type->AdvancedSearch->SearchValue = @$_GET["x_type"];
		if ($this->type->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->type->AdvancedSearch->SearchOperator = @$_GET["z_type"];

		// amount
		$this->amount->AdvancedSearch->SearchValue = @$_GET["x_amount"];
		if ($this->amount->AdvancedSearch->SearchValue <> "" && $this->Command == "") $this->Command = "search";
		$this->amount->AdvancedSearch->SearchOperator = @$_GET["z_amount"];
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
		$this->reference->setDbValue($row['reference']);
		$this->voided_date->setDbValue($row['voided_date']);
		$this->trans_date->setDbValue($row['trans_date']);
		$this->voided_by->setDbValue($row['voided_by']);
		$this->transaction_done_by->setDbValue($row['transaction_done_by']);
		$this->memo_->setDbValue($row['memo_']);
		$this->type->setDbValue($row['type']);
		$this->amount->setDbValue($row['amount']);
	}

	// Return a row with default values
	function NewRow() {
		$row = array();
		$row['reference'] = NULL;
		$row['voided_date'] = NULL;
		$row['trans_date'] = NULL;
		$row['voided_by'] = NULL;
		$row['transaction_done_by'] = NULL;
		$row['memo_'] = NULL;
		$row['type'] = NULL;
		$row['amount'] = NULL;
		return $row;
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF)
			return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->reference->DbValue = $row['reference'];
		$this->voided_date->DbValue = $row['voided_date'];
		$this->trans_date->DbValue = $row['trans_date'];
		$this->voided_by->DbValue = $row['voided_by'];
		$this->transaction_done_by->DbValue = $row['transaction_done_by'];
		$this->memo_->DbValue = $row['memo_'];
		$this->type->DbValue = $row['type'];
		$this->amount->DbValue = $row['amount'];
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
		if ($this->amount->FormValue == $this->amount->CurrentValue && is_numeric(ew_StrToFloat($this->amount->CurrentValue)))
			$this->amount->CurrentValue = ew_StrToFloat($this->amount->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// reference
		// voided_date
		// trans_date
		// voided_by
		// transaction_done_by
		// memo_
		// type
		// amount
		// Accumulate aggregate value

		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT && $this->RowType <> EW_ROWTYPE_AGGREGATE) {
			$this->reference->Count++; // Increment count
			if (is_numeric($this->amount->CurrentValue))
				$this->amount->Total += $this->amount->CurrentValue; // Accumulate total
		}
		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// reference
		$this->reference->ViewValue = $this->reference->CurrentValue;
		$this->reference->ViewCustomAttributes = "";

		// voided_date
		$this->voided_date->ViewValue = $this->voided_date->CurrentValue;
		$this->voided_date->ViewValue = ew_FormatDateTime($this->voided_date->ViewValue, 0);
		$this->voided_date->ViewCustomAttributes = "";

		// trans_date
		$this->trans_date->ViewValue = $this->trans_date->CurrentValue;
		$this->trans_date->ViewValue = ew_FormatDateTime($this->trans_date->ViewValue, 0);
		$this->trans_date->ViewCustomAttributes = "";

		// voided_by
		$this->voided_by->ViewValue = $this->voided_by->CurrentValue;
		$this->voided_by->ViewCustomAttributes = "";

		// transaction_done_by
		if (strval($this->transaction_done_by->CurrentValue) <> "") {
			$sFilterWrk = "`user_id`" . ew_SearchString("=", $this->transaction_done_by->CurrentValue, EW_DATATYPE_STRING, "");
		switch (@$gsLanguage) {
			case "ar":
				$sSqlWrk = "SELECT `user_id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
				$sWhereWrk = "";
				$this->transaction_done_by->LookupFilters = array("dx1" => '`user_id`');
				break;
			case "en":
				$sSqlWrk = "SELECT `user_id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
				$sWhereWrk = "";
				$this->transaction_done_by->LookupFilters = array("dx1" => '`user_id`');
				break;
			default:
				$sSqlWrk = "SELECT `user_id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `0_users`";
				$sWhereWrk = "";
				$this->transaction_done_by->LookupFilters = array("dx1" => '`user_id`');
				break;
		}
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->transaction_done_by, $sWhereWrk); // Call Lookup Selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->transaction_done_by->ViewValue = $this->transaction_done_by->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->transaction_done_by->ViewValue = $this->transaction_done_by->CurrentValue;
			}
		} else {
			$this->transaction_done_by->ViewValue = NULL;
		}
		$this->transaction_done_by->ViewCustomAttributes = "";

		// memo_
		$this->memo_->ViewValue = $this->memo_->CurrentValue;
		$this->memo_->ViewCustomAttributes = "";

		// type
		$this->type->ViewValue = $this->type->CurrentValue;
		$this->type->ViewCustomAttributes = "";

		// amount
		$this->amount->ViewValue = $this->amount->CurrentValue;
		$this->amount->ViewCustomAttributes = "";

			// reference
			$this->reference->LinkCustomAttributes = "";
			$this->reference->HrefValue = "";
			$this->reference->TooltipValue = "";

			// voided_date
			$this->voided_date->LinkCustomAttributes = "";
			$this->voided_date->HrefValue = "";
			$this->voided_date->TooltipValue = "";

			// trans_date
			$this->trans_date->LinkCustomAttributes = "";
			$this->trans_date->HrefValue = "";
			$this->trans_date->TooltipValue = "";

			// voided_by
			$this->voided_by->LinkCustomAttributes = "";
			$this->voided_by->HrefValue = "";
			$this->voided_by->TooltipValue = "";

			// transaction_done_by
			$this->transaction_done_by->LinkCustomAttributes = "";
			$this->transaction_done_by->HrefValue = "";
			$this->transaction_done_by->TooltipValue = "";

			// memo_
			$this->memo_->LinkCustomAttributes = "";
			$this->memo_->HrefValue = "";
			$this->memo_->TooltipValue = "";

			// type
			$this->type->LinkCustomAttributes = "";
			$this->type->HrefValue = "";
			$this->type->TooltipValue = "";

			// amount
			$this->amount->LinkCustomAttributes = "";
			$this->amount->HrefValue = "";
			$this->amount->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// reference
			$this->reference->EditAttrs["class"] = "form-control";
			$this->reference->EditCustomAttributes = "";
			$this->reference->EditValue = ew_HtmlEncode($this->reference->AdvancedSearch->SearchValue);
			$this->reference->PlaceHolder = ew_RemoveHtml($this->reference->FldCaption());

			// voided_date
			$this->voided_date->EditAttrs["class"] = "form-control";
			$this->voided_date->EditCustomAttributes = "";
			$this->voided_date->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->voided_date->AdvancedSearch->SearchValue, 0), 8));
			$this->voided_date->PlaceHolder = ew_RemoveHtml($this->voided_date->FldCaption());

			// trans_date
			$this->trans_date->EditAttrs["class"] = "form-control";
			$this->trans_date->EditCustomAttributes = "";
			$this->trans_date->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->trans_date->AdvancedSearch->SearchValue, 0), 8));
			$this->trans_date->PlaceHolder = ew_RemoveHtml($this->trans_date->FldCaption());

			// voided_by
			$this->voided_by->EditAttrs["class"] = "form-control";
			$this->voided_by->EditCustomAttributes = "";
			$this->voided_by->EditValue = ew_HtmlEncode($this->voided_by->AdvancedSearch->SearchValue);
			$this->voided_by->PlaceHolder = ew_RemoveHtml($this->voided_by->FldCaption());

			// transaction_done_by
			$this->transaction_done_by->EditCustomAttributes = "";
			if (trim(strval($this->transaction_done_by->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`user_id`" . ew_SearchString("=", $this->transaction_done_by->AdvancedSearch->SearchValue, EW_DATATYPE_STRING, "");
			}
			switch (@$gsLanguage) {
				case "ar":
					$sSqlWrk = "SELECT `user_id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_users`";
					$sWhereWrk = "";
					$this->transaction_done_by->LookupFilters = array("dx1" => '`user_id`');
					break;
				case "en":
					$sSqlWrk = "SELECT `user_id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_users`";
					$sWhereWrk = "";
					$this->transaction_done_by->LookupFilters = array("dx1" => '`user_id`');
					break;
				default:
					$sSqlWrk = "SELECT `user_id`, `user_id` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `0_users`";
					$sWhereWrk = "";
					$this->transaction_done_by->LookupFilters = array("dx1" => '`user_id`');
					break;
			}
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->transaction_done_by, $sWhereWrk); // Call Lookup Selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$this->transaction_done_by->AdvancedSearch->ViewValue = $this->transaction_done_by->DisplayValue($arwrk);
			} else {
				$this->transaction_done_by->AdvancedSearch->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->transaction_done_by->EditValue = $arwrk;

			// memo_
			$this->memo_->EditAttrs["class"] = "form-control";
			$this->memo_->EditCustomAttributes = "";
			$this->memo_->EditValue = ew_HtmlEncode($this->memo_->AdvancedSearch->SearchValue);
			$this->memo_->PlaceHolder = ew_RemoveHtml($this->memo_->FldCaption());

			// type
			$this->type->EditAttrs["class"] = "form-control";
			$this->type->EditCustomAttributes = "";
			$this->type->EditValue = ew_HtmlEncode($this->type->AdvancedSearch->SearchValue);
			$this->type->PlaceHolder = ew_RemoveHtml($this->type->FldCaption());

			// amount
			$this->amount->EditAttrs["class"] = "form-control";
			$this->amount->EditCustomAttributes = "";
			$this->amount->EditValue = ew_HtmlEncode($this->amount->AdvancedSearch->SearchValue);
			$this->amount->PlaceHolder = ew_RemoveHtml($this->amount->FldCaption());
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATEINIT) { // Initialize aggregate row
			$this->reference->Count = 0; // Initialize count
			$this->amount->Total = 0; // Initialize total
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATE) { // Aggregate row
			$this->reference->CurrentValue = $this->reference->Count;
			$this->reference->ViewValue = $this->reference->CurrentValue;
			$this->reference->ViewCustomAttributes = "";
			$this->reference->HrefValue = ""; // Clear href value
			$this->amount->CurrentValue = $this->amount->Total;
			$this->amount->ViewValue = $this->amount->CurrentValue;
			$this->amount->ViewCustomAttributes = "";
			$this->amount->HrefValue = ""; // Clear href value
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
		$this->reference->AdvancedSearch->Load();
		$this->voided_date->AdvancedSearch->Load();
		$this->trans_date->AdvancedSearch->Load();
		$this->voided_by->AdvancedSearch->Load();
		$this->transaction_done_by->AdvancedSearch->Load();
		$this->memo_->AdvancedSearch->Load();
		$this->type->AdvancedSearch->Load();
		$this->amount->AdvancedSearch->Load();
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
		$item->Body = "<button id=\"emf_voided_trans_reprt_view\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_voided_trans_reprt_view',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fvoided_trans_reprt_viewlist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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
		$this->AddSearchQueryString($sQry, $this->reference); // reference
		$this->AddSearchQueryString($sQry, $this->voided_date); // voided_date
		$this->AddSearchQueryString($sQry, $this->trans_date); // trans_date
		$this->AddSearchQueryString($sQry, $this->voided_by); // voided_by
		$this->AddSearchQueryString($sQry, $this->transaction_done_by); // transaction_done_by
		$this->AddSearchQueryString($sQry, $this->memo_); // memo_
		$this->AddSearchQueryString($sQry, $this->type); // type
		$this->AddSearchQueryString($sQry, $this->amount); // amount

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
		case "x_transaction_done_by":
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
				$this->Lookup_Selecting($this->transaction_done_by, $sWhereWrk); // Call Lookup Selecting
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
if (!isset($voided_trans_reprt_view_list)) $voided_trans_reprt_view_list = new cvoided_trans_reprt_view_list();

// Page init
$voided_trans_reprt_view_list->Page_Init();

// Page main
$voided_trans_reprt_view_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$voided_trans_reprt_view_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($voided_trans_reprt_view->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fvoided_trans_reprt_viewlist = new ew_Form("fvoided_trans_reprt_viewlist", "list");
fvoided_trans_reprt_viewlist.FormKeyCountName = '<?php echo $voided_trans_reprt_view_list->FormKeyCountName ?>';

// Form_CustomValidate event
fvoided_trans_reprt_viewlist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid.
 	return true;
 }

// Use JavaScript validation or not
fvoided_trans_reprt_viewlist.ValidateRequired = <?php echo json_encode(EW_CLIENT_VALIDATE) ?>;

// Dynamic selection lists
fvoided_trans_reprt_viewlist.Lists["x_transaction_done_by"] = {"LinkField":"x_user_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_user_id","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"_0_users"};
fvoided_trans_reprt_viewlist.Lists["x_transaction_done_by"].Data = "<?php echo $voided_trans_reprt_view_list->transaction_done_by->LookupFilterQuery(FALSE, "list") ?>";

// Form object for search
var CurrentSearchForm = fvoided_trans_reprt_viewlistsrch = new ew_Form("fvoided_trans_reprt_viewlistsrch");

// Validate function for search
fvoided_trans_reprt_viewlistsrch.Validate = function(fobj) {
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
fvoided_trans_reprt_viewlistsrch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid.
 	return true;
 }

// Use JavaScript validation or not
fvoided_trans_reprt_viewlistsrch.ValidateRequired = <?php echo json_encode(EW_CLIENT_VALIDATE) ?>;

// Dynamic selection lists
fvoided_trans_reprt_viewlistsrch.Lists["x_transaction_done_by"] = {"LinkField":"x_user_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_user_id","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"_0_users"};
fvoided_trans_reprt_viewlistsrch.Lists["x_transaction_done_by"].Data = "<?php echo $voided_trans_reprt_view_list->transaction_done_by->LookupFilterQuery(FALSE, "extbs") ?>";
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($voided_trans_reprt_view->Export == "") { ?>
<div class="ewToolbar">
<?php if ($voided_trans_reprt_view_list->TotalRecs > 0 && $voided_trans_reprt_view_list->ExportOptions->Visible()) { ?>
<?php $voided_trans_reprt_view_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($voided_trans_reprt_view_list->SearchOptions->Visible()) { ?>
<?php $voided_trans_reprt_view_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($voided_trans_reprt_view_list->FilterOptions->Visible()) { ?>
<?php $voided_trans_reprt_view_list->FilterOptions->Render("body") ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
	$bSelectLimit = $voided_trans_reprt_view_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($voided_trans_reprt_view_list->TotalRecs <= 0)
			$voided_trans_reprt_view_list->TotalRecs = $voided_trans_reprt_view->ListRecordCount();
	} else {
		if (!$voided_trans_reprt_view_list->Recordset && ($voided_trans_reprt_view_list->Recordset = $voided_trans_reprt_view_list->LoadRecordset()))
			$voided_trans_reprt_view_list->TotalRecs = $voided_trans_reprt_view_list->Recordset->RecordCount();
	}
	$voided_trans_reprt_view_list->StartRec = 1;
	if ($voided_trans_reprt_view_list->DisplayRecs <= 0 || ($voided_trans_reprt_view->Export <> "" && $voided_trans_reprt_view->ExportAll)) // Display all records
		$voided_trans_reprt_view_list->DisplayRecs = $voided_trans_reprt_view_list->TotalRecs;
	if (!($voided_trans_reprt_view->Export <> "" && $voided_trans_reprt_view->ExportAll))
		$voided_trans_reprt_view_list->SetupStartRec(); // Set up start record position
	if ($bSelectLimit)
		$voided_trans_reprt_view_list->Recordset = $voided_trans_reprt_view_list->LoadRecordset($voided_trans_reprt_view_list->StartRec-1, $voided_trans_reprt_view_list->DisplayRecs);

	// Set no record found message
	if ($voided_trans_reprt_view->CurrentAction == "" && $voided_trans_reprt_view_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$voided_trans_reprt_view_list->setWarningMessage(ew_DeniedMsg());
		if ($voided_trans_reprt_view_list->SearchWhere == "0=101")
			$voided_trans_reprt_view_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$voided_trans_reprt_view_list->setWarningMessage($Language->Phrase("NoRecord"));
	}
$voided_trans_reprt_view_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($voided_trans_reprt_view->Export == "" && $voided_trans_reprt_view->CurrentAction == "") { ?>
<form name="fvoided_trans_reprt_viewlistsrch" id="fvoided_trans_reprt_viewlistsrch" class="form-inline ewForm ewExtSearchForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($voided_trans_reprt_view_list->SearchWhere <> "") ? " in" : " in"; ?>
<div id="fvoided_trans_reprt_viewlistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="voided_trans_reprt_view">
	<div class="ewBasicSearch">
<?php
if ($gsSearchError == "")
	$voided_trans_reprt_view_list->LoadAdvancedSearch(); // Load advanced search

// Render for search
$voided_trans_reprt_view->RowType = EW_ROWTYPE_SEARCH;

// Render row
$voided_trans_reprt_view->ResetAttrs();
$voided_trans_reprt_view_list->RenderRow();
?>
<div id="xsr_1" class="ewRow">
<?php if ($voided_trans_reprt_view->transaction_done_by->Visible) { // transaction_done_by ?>
	<div id="xsc_transaction_done_by" class="ewCell form-group">
		<label for="x_transaction_done_by" class="ewSearchCaption ewLabel"><?php echo $voided_trans_reprt_view->transaction_done_by->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_transaction_done_by" id="z_transaction_done_by" value="="></span>
		<span class="ewSearchField">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_transaction_done_by"><?php echo (strval($voided_trans_reprt_view->transaction_done_by->AdvancedSearch->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $voided_trans_reprt_view->transaction_done_by->AdvancedSearch->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($voided_trans_reprt_view->transaction_done_by->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_transaction_done_by',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"<?php echo (($voided_trans_reprt_view->transaction_done_by->ReadOnly || $voided_trans_reprt_view->transaction_done_by->Disabled) ? " disabled" : "")?>><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="voided_trans_reprt_view" data-field="x_transaction_done_by" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $voided_trans_reprt_view->transaction_done_by->DisplayValueSeparatorAttribute() ?>" name="x_transaction_done_by" id="x_transaction_done_by" value="<?php echo $voided_trans_reprt_view->transaction_done_by->AdvancedSearch->SearchValue ?>"<?php echo $voided_trans_reprt_view->transaction_done_by->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_2" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($voided_trans_reprt_view_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($voided_trans_reprt_view_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $voided_trans_reprt_view_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($voided_trans_reprt_view_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($voided_trans_reprt_view_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($voided_trans_reprt_view_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($voided_trans_reprt_view_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
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
<?php $voided_trans_reprt_view_list->ShowPageHeader(); ?>
<?php
$voided_trans_reprt_view_list->ShowMessage();
?>
<?php if ($voided_trans_reprt_view_list->TotalRecs > 0 || $voided_trans_reprt_view->CurrentAction <> "") { ?>
<div class="box ewBox ewGrid<?php if ($voided_trans_reprt_view_list->IsAddOrEdit()) { ?> ewGridAddEdit<?php } ?> voided_trans_reprt_view">
<form name="fvoided_trans_reprt_viewlist" id="fvoided_trans_reprt_viewlist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($voided_trans_reprt_view_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $voided_trans_reprt_view_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="voided_trans_reprt_view">
<div id="gmp_voided_trans_reprt_view" class="<?php if (ew_IsResponsiveLayout()) { ?>table-responsive <?php } ?>ewGridMiddlePanel">
<?php if ($voided_trans_reprt_view_list->TotalRecs > 0 || $voided_trans_reprt_view->CurrentAction == "gridedit") { ?>
<table id="tbl_voided_trans_reprt_viewlist" class="table ewTable">
<thead>
	<tr class="ewTableHeader">
<?php

// Header row
$voided_trans_reprt_view_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$voided_trans_reprt_view_list->RenderListOptions();

// Render list options (header, left)
$voided_trans_reprt_view_list->ListOptions->Render("header", "left");
?>
<?php if ($voided_trans_reprt_view->reference->Visible) { // reference ?>
	<?php if ($voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->reference) == "") { ?>
		<th data-name="reference" class="<?php echo $voided_trans_reprt_view->reference->HeaderCellClass() ?>"><div id="elh_voided_trans_reprt_view_reference" class="voided_trans_reprt_view_reference"><div class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->reference->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="reference" class="<?php echo $voided_trans_reprt_view->reference->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->reference) ?>',1);"><div id="elh_voided_trans_reprt_view_reference" class="voided_trans_reprt_view_reference">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->reference->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($voided_trans_reprt_view->reference->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($voided_trans_reprt_view->reference->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($voided_trans_reprt_view->voided_date->Visible) { // voided_date ?>
	<?php if ($voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->voided_date) == "") { ?>
		<th data-name="voided_date" class="<?php echo $voided_trans_reprt_view->voided_date->HeaderCellClass() ?>"><div id="elh_voided_trans_reprt_view_voided_date" class="voided_trans_reprt_view_voided_date"><div class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->voided_date->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="voided_date" class="<?php echo $voided_trans_reprt_view->voided_date->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->voided_date) ?>',1);"><div id="elh_voided_trans_reprt_view_voided_date" class="voided_trans_reprt_view_voided_date">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->voided_date->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($voided_trans_reprt_view->voided_date->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($voided_trans_reprt_view->voided_date->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($voided_trans_reprt_view->trans_date->Visible) { // trans_date ?>
	<?php if ($voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->trans_date) == "") { ?>
		<th data-name="trans_date" class="<?php echo $voided_trans_reprt_view->trans_date->HeaderCellClass() ?>"><div id="elh_voided_trans_reprt_view_trans_date" class="voided_trans_reprt_view_trans_date"><div class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->trans_date->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="trans_date" class="<?php echo $voided_trans_reprt_view->trans_date->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->trans_date) ?>',1);"><div id="elh_voided_trans_reprt_view_trans_date" class="voided_trans_reprt_view_trans_date">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->trans_date->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($voided_trans_reprt_view->trans_date->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($voided_trans_reprt_view->trans_date->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($voided_trans_reprt_view->voided_by->Visible) { // voided_by ?>
	<?php if ($voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->voided_by) == "") { ?>
		<th data-name="voided_by" class="<?php echo $voided_trans_reprt_view->voided_by->HeaderCellClass() ?>"><div id="elh_voided_trans_reprt_view_voided_by" class="voided_trans_reprt_view_voided_by"><div class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->voided_by->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="voided_by" class="<?php echo $voided_trans_reprt_view->voided_by->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->voided_by) ?>',1);"><div id="elh_voided_trans_reprt_view_voided_by" class="voided_trans_reprt_view_voided_by">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->voided_by->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($voided_trans_reprt_view->voided_by->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($voided_trans_reprt_view->voided_by->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($voided_trans_reprt_view->transaction_done_by->Visible) { // transaction_done_by ?>
	<?php if ($voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->transaction_done_by) == "") { ?>
		<th data-name="transaction_done_by" class="<?php echo $voided_trans_reprt_view->transaction_done_by->HeaderCellClass() ?>"><div id="elh_voided_trans_reprt_view_transaction_done_by" class="voided_trans_reprt_view_transaction_done_by"><div class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->transaction_done_by->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="transaction_done_by" class="<?php echo $voided_trans_reprt_view->transaction_done_by->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->transaction_done_by) ?>',1);"><div id="elh_voided_trans_reprt_view_transaction_done_by" class="voided_trans_reprt_view_transaction_done_by">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->transaction_done_by->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($voided_trans_reprt_view->transaction_done_by->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($voided_trans_reprt_view->transaction_done_by->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($voided_trans_reprt_view->memo_->Visible) { // memo_ ?>
	<?php if ($voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->memo_) == "") { ?>
		<th data-name="memo_" class="<?php echo $voided_trans_reprt_view->memo_->HeaderCellClass() ?>"><div id="elh_voided_trans_reprt_view_memo_" class="voided_trans_reprt_view_memo_"><div class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->memo_->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="memo_" class="<?php echo $voided_trans_reprt_view->memo_->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->memo_) ?>',1);"><div id="elh_voided_trans_reprt_view_memo_" class="voided_trans_reprt_view_memo_">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->memo_->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($voided_trans_reprt_view->memo_->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($voided_trans_reprt_view->memo_->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($voided_trans_reprt_view->type->Visible) { // type ?>
	<?php if ($voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->type) == "") { ?>
		<th data-name="type" class="<?php echo $voided_trans_reprt_view->type->HeaderCellClass() ?>"><div id="elh_voided_trans_reprt_view_type" class="voided_trans_reprt_view_type"><div class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->type->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="type" class="<?php echo $voided_trans_reprt_view->type->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->type) ?>',1);"><div id="elh_voided_trans_reprt_view_type" class="voided_trans_reprt_view_type">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->type->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($voided_trans_reprt_view->type->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($voided_trans_reprt_view->type->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php if ($voided_trans_reprt_view->amount->Visible) { // amount ?>
	<?php if ($voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->amount) == "") { ?>
		<th data-name="amount" class="<?php echo $voided_trans_reprt_view->amount->HeaderCellClass() ?>"><div id="elh_voided_trans_reprt_view_amount" class="voided_trans_reprt_view_amount"><div class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->amount->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="amount" class="<?php echo $voided_trans_reprt_view->amount->HeaderCellClass() ?>"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $voided_trans_reprt_view->SortUrl($voided_trans_reprt_view->amount) ?>',1);"><div id="elh_voided_trans_reprt_view_amount" class="voided_trans_reprt_view_amount">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $voided_trans_reprt_view->amount->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($voided_trans_reprt_view->amount->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($voided_trans_reprt_view->amount->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
		</div></div></th>
	<?php } ?>
<?php } ?>
<?php

// Render list options (header, right)
$voided_trans_reprt_view_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($voided_trans_reprt_view->ExportAll && $voided_trans_reprt_view->Export <> "") {
	$voided_trans_reprt_view_list->StopRec = $voided_trans_reprt_view_list->TotalRecs;
} else {

	// Set the last record to display
	if ($voided_trans_reprt_view_list->TotalRecs > $voided_trans_reprt_view_list->StartRec + $voided_trans_reprt_view_list->DisplayRecs - 1)
		$voided_trans_reprt_view_list->StopRec = $voided_trans_reprt_view_list->StartRec + $voided_trans_reprt_view_list->DisplayRecs - 1;
	else
		$voided_trans_reprt_view_list->StopRec = $voided_trans_reprt_view_list->TotalRecs;
}
$voided_trans_reprt_view_list->RecCnt = $voided_trans_reprt_view_list->StartRec - 1;
if ($voided_trans_reprt_view_list->Recordset && !$voided_trans_reprt_view_list->Recordset->EOF) {
	$voided_trans_reprt_view_list->Recordset->MoveFirst();
	$bSelectLimit = $voided_trans_reprt_view_list->UseSelectLimit;
	if (!$bSelectLimit && $voided_trans_reprt_view_list->StartRec > 1)
		$voided_trans_reprt_view_list->Recordset->Move($voided_trans_reprt_view_list->StartRec - 1);
} elseif (!$voided_trans_reprt_view->AllowAddDeleteRow && $voided_trans_reprt_view_list->StopRec == 0) {
	$voided_trans_reprt_view_list->StopRec = $voided_trans_reprt_view->GridAddRowCount;
}

// Initialize aggregate
$voided_trans_reprt_view->RowType = EW_ROWTYPE_AGGREGATEINIT;
$voided_trans_reprt_view->ResetAttrs();
$voided_trans_reprt_view_list->RenderRow();
while ($voided_trans_reprt_view_list->RecCnt < $voided_trans_reprt_view_list->StopRec) {
	$voided_trans_reprt_view_list->RecCnt++;
	if (intval($voided_trans_reprt_view_list->RecCnt) >= intval($voided_trans_reprt_view_list->StartRec)) {
		$voided_trans_reprt_view_list->RowCnt++;

		// Set up key count
		$voided_trans_reprt_view_list->KeyCount = $voided_trans_reprt_view_list->RowIndex;

		// Init row class and style
		$voided_trans_reprt_view->ResetAttrs();
		$voided_trans_reprt_view->CssClass = "";
		if ($voided_trans_reprt_view->CurrentAction == "gridadd") {
		} else {
			$voided_trans_reprt_view_list->LoadRowValues($voided_trans_reprt_view_list->Recordset); // Load row values
		}
		$voided_trans_reprt_view->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$voided_trans_reprt_view->RowAttrs = array_merge($voided_trans_reprt_view->RowAttrs, array('data-rowindex'=>$voided_trans_reprt_view_list->RowCnt, 'id'=>'r' . $voided_trans_reprt_view_list->RowCnt . '_voided_trans_reprt_view', 'data-rowtype'=>$voided_trans_reprt_view->RowType));

		// Render row
		$voided_trans_reprt_view_list->RenderRow();

		// Render list options
		$voided_trans_reprt_view_list->RenderListOptions();
?>
	<tr<?php echo $voided_trans_reprt_view->RowAttributes() ?>>
<?php

// Render list options (body, left)
$voided_trans_reprt_view_list->ListOptions->Render("body", "left", $voided_trans_reprt_view_list->RowCnt);
?>
	<?php if ($voided_trans_reprt_view->reference->Visible) { // reference ?>
		<td data-name="reference"<?php echo $voided_trans_reprt_view->reference->CellAttributes() ?>>
<span id="el<?php echo $voided_trans_reprt_view_list->RowCnt ?>_voided_trans_reprt_view_reference" class="voided_trans_reprt_view_reference">
<span<?php echo $voided_trans_reprt_view->reference->ViewAttributes() ?>>
<?php echo $voided_trans_reprt_view->reference->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($voided_trans_reprt_view->voided_date->Visible) { // voided_date ?>
		<td data-name="voided_date"<?php echo $voided_trans_reprt_view->voided_date->CellAttributes() ?>>
<span id="el<?php echo $voided_trans_reprt_view_list->RowCnt ?>_voided_trans_reprt_view_voided_date" class="voided_trans_reprt_view_voided_date">
<span<?php echo $voided_trans_reprt_view->voided_date->ViewAttributes() ?>>
<?php echo $voided_trans_reprt_view->voided_date->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($voided_trans_reprt_view->trans_date->Visible) { // trans_date ?>
		<td data-name="trans_date"<?php echo $voided_trans_reprt_view->trans_date->CellAttributes() ?>>
<span id="el<?php echo $voided_trans_reprt_view_list->RowCnt ?>_voided_trans_reprt_view_trans_date" class="voided_trans_reprt_view_trans_date">
<span<?php echo $voided_trans_reprt_view->trans_date->ViewAttributes() ?>>
<?php echo $voided_trans_reprt_view->trans_date->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($voided_trans_reprt_view->voided_by->Visible) { // voided_by ?>
		<td data-name="voided_by"<?php echo $voided_trans_reprt_view->voided_by->CellAttributes() ?>>
<span id="el<?php echo $voided_trans_reprt_view_list->RowCnt ?>_voided_trans_reprt_view_voided_by" class="voided_trans_reprt_view_voided_by">
<span<?php echo $voided_trans_reprt_view->voided_by->ViewAttributes() ?>>
<?php echo $voided_trans_reprt_view->voided_by->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($voided_trans_reprt_view->transaction_done_by->Visible) { // transaction_done_by ?>
		<td data-name="transaction_done_by"<?php echo $voided_trans_reprt_view->transaction_done_by->CellAttributes() ?>>
<span id="el<?php echo $voided_trans_reprt_view_list->RowCnt ?>_voided_trans_reprt_view_transaction_done_by" class="voided_trans_reprt_view_transaction_done_by">
<span<?php echo $voided_trans_reprt_view->transaction_done_by->ViewAttributes() ?>>
<?php echo $voided_trans_reprt_view->transaction_done_by->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($voided_trans_reprt_view->memo_->Visible) { // memo_ ?>
		<td data-name="memo_"<?php echo $voided_trans_reprt_view->memo_->CellAttributes() ?>>
<span id="el<?php echo $voided_trans_reprt_view_list->RowCnt ?>_voided_trans_reprt_view_memo_" class="voided_trans_reprt_view_memo_">
<span<?php echo $voided_trans_reprt_view->memo_->ViewAttributes() ?>>
<?php echo $voided_trans_reprt_view->memo_->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($voided_trans_reprt_view->type->Visible) { // type ?>
		<td data-name="type"<?php echo $voided_trans_reprt_view->type->CellAttributes() ?>>
<span id="el<?php echo $voided_trans_reprt_view_list->RowCnt ?>_voided_trans_reprt_view_type" class="voided_trans_reprt_view_type">
<span<?php echo $voided_trans_reprt_view->type->ViewAttributes() ?>>
<?php echo $voided_trans_reprt_view->type->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($voided_trans_reprt_view->amount->Visible) { // amount ?>
		<td data-name="amount"<?php echo $voided_trans_reprt_view->amount->CellAttributes() ?>>
<span id="el<?php echo $voided_trans_reprt_view_list->RowCnt ?>_voided_trans_reprt_view_amount" class="voided_trans_reprt_view_amount">
<span<?php echo $voided_trans_reprt_view->amount->ViewAttributes() ?>>
<?php echo $voided_trans_reprt_view->amount->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$voided_trans_reprt_view_list->ListOptions->Render("body", "right", $voided_trans_reprt_view_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($voided_trans_reprt_view->CurrentAction <> "gridadd")
		$voided_trans_reprt_view_list->Recordset->MoveNext();
}
?>
</tbody>
<?php

// Render aggregate row
$voided_trans_reprt_view->RowType = EW_ROWTYPE_AGGREGATE;
$voided_trans_reprt_view->ResetAttrs();
$voided_trans_reprt_view_list->RenderRow();
?>
<?php if ($voided_trans_reprt_view_list->TotalRecs > 0 && ($voided_trans_reprt_view->CurrentAction <> "gridadd" && $voided_trans_reprt_view->CurrentAction <> "gridedit")) { ?>
<tfoot><!-- Table footer -->
	<tr class="ewTableFooter">
<?php

// Render list options
$voided_trans_reprt_view_list->RenderListOptions();

// Render list options (footer, left)
$voided_trans_reprt_view_list->ListOptions->Render("footer", "left");
?>
	<?php if ($voided_trans_reprt_view->reference->Visible) { // reference ?>
		<td data-name="reference" class="<?php echo $voided_trans_reprt_view->reference->FooterCellClass() ?>"><span id="elf_voided_trans_reprt_view_reference" class="voided_trans_reprt_view_reference">
<span class="ewAggregate"><?php echo $Language->Phrase("COUNT") ?></span><span class="ewAggregateValue">
<?php echo $voided_trans_reprt_view->reference->ViewValue ?></span>
		</span></td>
	<?php } ?>
	<?php if ($voided_trans_reprt_view->voided_date->Visible) { // voided_date ?>
		<td data-name="voided_date" class="<?php echo $voided_trans_reprt_view->voided_date->FooterCellClass() ?>"><span id="elf_voided_trans_reprt_view_voided_date" class="voided_trans_reprt_view_voided_date">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($voided_trans_reprt_view->trans_date->Visible) { // trans_date ?>
		<td data-name="trans_date" class="<?php echo $voided_trans_reprt_view->trans_date->FooterCellClass() ?>"><span id="elf_voided_trans_reprt_view_trans_date" class="voided_trans_reprt_view_trans_date">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($voided_trans_reprt_view->voided_by->Visible) { // voided_by ?>
		<td data-name="voided_by" class="<?php echo $voided_trans_reprt_view->voided_by->FooterCellClass() ?>"><span id="elf_voided_trans_reprt_view_voided_by" class="voided_trans_reprt_view_voided_by">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($voided_trans_reprt_view->transaction_done_by->Visible) { // transaction_done_by ?>
		<td data-name="transaction_done_by" class="<?php echo $voided_trans_reprt_view->transaction_done_by->FooterCellClass() ?>"><span id="elf_voided_trans_reprt_view_transaction_done_by" class="voided_trans_reprt_view_transaction_done_by">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($voided_trans_reprt_view->memo_->Visible) { // memo_ ?>
		<td data-name="memo_" class="<?php echo $voided_trans_reprt_view->memo_->FooterCellClass() ?>"><span id="elf_voided_trans_reprt_view_memo_" class="voided_trans_reprt_view_memo_">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($voided_trans_reprt_view->type->Visible) { // type ?>
		<td data-name="type" class="<?php echo $voided_trans_reprt_view->type->FooterCellClass() ?>"><span id="elf_voided_trans_reprt_view_type" class="voided_trans_reprt_view_type">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($voided_trans_reprt_view->amount->Visible) { // amount ?>
		<td data-name="amount" class="<?php echo $voided_trans_reprt_view->amount->FooterCellClass() ?>"><span id="elf_voided_trans_reprt_view_amount" class="voided_trans_reprt_view_amount">
<span class="ewAggregate"><?php echo $Language->Phrase("TOTAL") ?></span><span class="ewAggregateValue">
<?php echo $voided_trans_reprt_view->amount->ViewValue ?></span>
		</span></td>
	<?php } ?>
<?php

// Render list options (footer, right)
$voided_trans_reprt_view_list->ListOptions->Render("footer", "right");
?>
	</tr>
</tfoot>
<?php } ?>
</table>
<?php } ?>
<?php if ($voided_trans_reprt_view->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($voided_trans_reprt_view_list->Recordset)
	$voided_trans_reprt_view_list->Recordset->Close();
?>
<?php if ($voided_trans_reprt_view->Export == "") { ?>
<div class="box-footer ewGridLowerPanel">
<?php if ($voided_trans_reprt_view->CurrentAction <> "gridadd" && $voided_trans_reprt_view->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($voided_trans_reprt_view_list->Pager)) $voided_trans_reprt_view_list->Pager = new cPrevNextPager($voided_trans_reprt_view_list->StartRec, $voided_trans_reprt_view_list->DisplayRecs, $voided_trans_reprt_view_list->TotalRecs, $voided_trans_reprt_view_list->AutoHidePager) ?>
<?php if ($voided_trans_reprt_view_list->Pager->RecordCount > 0 && $voided_trans_reprt_view_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($voided_trans_reprt_view_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $voided_trans_reprt_view_list->PageUrl() ?>start=<?php echo $voided_trans_reprt_view_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($voided_trans_reprt_view_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $voided_trans_reprt_view_list->PageUrl() ?>start=<?php echo $voided_trans_reprt_view_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $voided_trans_reprt_view_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($voided_trans_reprt_view_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $voided_trans_reprt_view_list->PageUrl() ?>start=<?php echo $voided_trans_reprt_view_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($voided_trans_reprt_view_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $voided_trans_reprt_view_list->PageUrl() ?>start=<?php echo $voided_trans_reprt_view_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $voided_trans_reprt_view_list->Pager->PageCount ?></span>
</div>
<?php } ?>
<?php if ($voided_trans_reprt_view_list->Pager->RecordCount > 0) { ?>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $voided_trans_reprt_view_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $voided_trans_reprt_view_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $voided_trans_reprt_view_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($voided_trans_reprt_view_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($voided_trans_reprt_view_list->TotalRecs == 0 && $voided_trans_reprt_view->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($voided_trans_reprt_view_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($voided_trans_reprt_view->Export == "") { ?>
<script type="text/javascript">
fvoided_trans_reprt_viewlistsrch.FilterList = <?php echo $voided_trans_reprt_view_list->GetFilterList() ?>;
fvoided_trans_reprt_viewlistsrch.Init();
fvoided_trans_reprt_viewlist.Init();
</script>
<?php } ?>
<?php
$voided_trans_reprt_view_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($voided_trans_reprt_view->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$voided_trans_reprt_view_list->Page_Terminate();
?>
