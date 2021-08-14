<?php

// Global variable for table object
$periodical_report_view = NULL;

//
// Table class for periodical_report_view
//
class cperiodical_report_view extends cTable {
	var $tran_date;
	var $invoice_count;
	var $total_service_count;
	var $total_invoice_amount;
	var $total_amount_recieved;
	var $pending_amount;
	var $total_service_charge;
	var $total_commission;
	var $total_collection;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'periodical_report_view';
		$this->TableName = 'periodical_report_view';
		$this->TableType = 'VIEW';

		// Update Table
		$this->UpdateTable = "`periodical_report_view`";
		$this->DBID = 'DB';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->ExportExcelPageOrientation = ""; // Page orientation (PHPExcel only)
		$this->ExportExcelPageSize = ""; // Page size (PHPExcel only)
		$this->ExportWordPageOrientation = "portrait"; // Page orientation (PHPWord only)
		$this->ExportWordColumnWidth = NULL; // Cell width (PHPWord only)
		$this->DetailAdd = FALSE; // Allow detail add
		$this->DetailEdit = FALSE; // Allow detail edit
		$this->DetailView = FALSE; // Allow detail view
		$this->ShowMultipleDetails = FALSE; // Show multiple details
		$this->GridAddRowCount = 5;
		$this->AllowAddDeleteRow = TRUE; // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// tran_date
		$this->tran_date = new cField('periodical_report_view', 'periodical_report_view', 'x_tran_date', 'tran_date', '`tran_date`', ew_CastDateFieldForLike('`tran_date`', 2, "DB"), 133, 2, FALSE, '`tran_date`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->tran_date->Sortable = TRUE; // Allow sort
		$this->tran_date->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['tran_date'] = &$this->tran_date;

		// invoice_count
		$this->invoice_count = new cField('periodical_report_view', 'periodical_report_view', 'x_invoice_count', 'invoice_count', '`invoice_count`', '`invoice_count`', 20, -1, FALSE, '`invoice_count`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->invoice_count->Sortable = TRUE; // Allow sort
		$this->invoice_count->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['invoice_count'] = &$this->invoice_count;

		// total_service_count
		$this->total_service_count = new cField('periodical_report_view', 'periodical_report_view', 'x_total_service_count', 'total_service_count', '`total_service_count`', '`total_service_count`', 20, -1, FALSE, '`total_service_count`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->total_service_count->Sortable = TRUE; // Allow sort
		$this->total_service_count->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['total_service_count'] = &$this->total_service_count;

		// total_invoice_amount
		$this->total_invoice_amount = new cField('periodical_report_view', 'periodical_report_view', 'x_total_invoice_amount', 'total_invoice_amount', '`total_invoice_amount`', '`total_invoice_amount`', 5, -1, FALSE, '`total_invoice_amount`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->total_invoice_amount->Sortable = TRUE; // Allow sort
		$this->total_invoice_amount->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['total_invoice_amount'] = &$this->total_invoice_amount;

		// total_amount_recieved
		$this->total_amount_recieved = new cField('periodical_report_view', 'periodical_report_view', 'x_total_amount_recieved', 'total_amount_recieved', '`total_amount_recieved`', '`total_amount_recieved`', 5, -1, FALSE, '`total_amount_recieved`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->total_amount_recieved->Sortable = TRUE; // Allow sort
		$this->total_amount_recieved->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['total_amount_recieved'] = &$this->total_amount_recieved;

		// pending_amount
		$this->pending_amount = new cField('periodical_report_view', 'periodical_report_view', 'x_pending_amount', 'pending_amount', '`pending_amount`', '`pending_amount`', 5, -1, FALSE, '`pending_amount`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->pending_amount->Sortable = TRUE; // Allow sort
		$this->pending_amount->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['pending_amount'] = &$this->pending_amount;

		// total_service_charge
		$this->total_service_charge = new cField('periodical_report_view', 'periodical_report_view', 'x_total_service_charge', 'total_service_charge', '`total_service_charge`', '`total_service_charge`', 5, -1, FALSE, '`total_service_charge`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->total_service_charge->Sortable = TRUE; // Allow sort
		$this->total_service_charge->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['total_service_charge'] = &$this->total_service_charge;

		// total_commission
		$this->total_commission = new cField('periodical_report_view', 'periodical_report_view', 'x_total_commission', 'total_commission', '`total_commission`', '`total_commission`', 5, -1, FALSE, '`total_commission`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->total_commission->Sortable = TRUE; // Allow sort
		$this->total_commission->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['total_commission'] = &$this->total_commission;

		// total_collection
		$this->total_collection = new cField('periodical_report_view', 'periodical_report_view', 'x_total_collection', 'total_collection', '`total_collection`', '`total_collection`', 5, -1, FALSE, '`total_collection`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->total_collection->Sortable = TRUE; // Allow sort
		$this->total_collection->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['total_collection'] = &$this->total_collection;
	}

	// Field Visibility
	function GetFieldVisibility($fldparm) {
		global $Security;
		return $this->$fldparm->Visible; // Returns original value
	}

	// Column CSS classes
	var $LeftColumnClass = "col-sm-2 control-label ewLabel";
	var $RightColumnClass = "col-sm-10";
	var $OffsetColumnClass = "col-sm-10 col-sm-offset-2";

	// Set left column class (must be predefined col-*-* classes of Bootstrap grid system)
	function SetLeftColumnClass($class) {
		if (preg_match('/^col\-(\w+)\-(\d+)$/', $class, $match)) {
			$this->LeftColumnClass = $class . " control-label ewLabel";
			$this->RightColumnClass = "col-" . $match[1] . "-" . strval(12 - intval($match[2]));
			$this->OffsetColumnClass = $this->RightColumnClass . " " . str_replace($match[1], $match[1] + "-offset", $class);
		}
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
		} else {
			$ofld->setSort("");
		}
	}

	// Table level SQL
	var $_SqlFrom = "";

	function getSqlFrom() { // From
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`periodical_report_view`";
	}

	function SqlFrom() { // For backward compatibility
		return $this->getSqlFrom();
	}

	function setSqlFrom($v) {
		$this->_SqlFrom = $v;
	}
	var $_SqlSelect = "";

	function getSqlSelect() { // Select
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT * FROM " . $this->getSqlFrom();
	}

	function SqlSelect() { // For backward compatibility
		return $this->getSqlSelect();
	}

	function setSqlSelect($v) {
		$this->_SqlSelect = $v;
	}
	var $_SqlWhere = "";

	function getSqlWhere() { // Where
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "";
		$this->TableFilter = "";
		ew_AddFilter($sWhere, $this->TableFilter);
		return $sWhere;
	}

	function SqlWhere() { // For backward compatibility
		return $this->getSqlWhere();
	}

	function setSqlWhere($v) {
		$this->_SqlWhere = $v;
	}
	var $_SqlGroupBy = "";

	function getSqlGroupBy() { // Group By
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "";
	}

	function SqlGroupBy() { // For backward compatibility
		return $this->getSqlGroupBy();
	}

	function setSqlGroupBy($v) {
		$this->_SqlGroupBy = $v;
	}
	var $_SqlHaving = "";

	function getSqlHaving() { // Having
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "";
	}

	function SqlHaving() { // For backward compatibility
		return $this->getSqlHaving();
	}

	function setSqlHaving($v) {
		$this->_SqlHaving = $v;
	}
	var $_SqlOrderBy = "";

	function getSqlOrderBy() { // Order By
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "";
	}

	function SqlOrderBy() { // For backward compatibility
		return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
		$this->_SqlOrderBy = $v;
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = EW_USER_ID_ALLOW;
		switch ($id) {
			case "add":
			case "copy":
			case "gridadd":
			case "register":
			case "addopt":
				return (($allow & 1) == 1);
			case "edit":
			case "gridedit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return (($allow & 4) == 4);
			case "delete":
				return (($allow & 2) == 2);
			case "view":
				return (($allow & 32) == 32);
			case "search":
				return (($allow & 64) == 64);
			default:
				return (($allow & 8) == 8);
		}
	}

	// Get SQL
	function GetSQL($where, $orderby) {
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$where, $orderby);
	}

	// Table SQL
	function SQL() {
		$filter = $this->CurrentFilter;
		$filter = $this->ApplyUserIDFilters($filter);
		$sort = $this->getSessionOrderBy();
		return $this->GetSQL($filter, $sort);
	}

	// Table SQL with List page filter
	var $UseSessionForListSQL = TRUE;

	function ListSQL() {
		$sFilter = $this->UseSessionForListSQL ? $this->getSessionWhere() : "";
		ew_AddFilter($sFilter, $this->CurrentFilter);
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$this->Recordset_Selecting($sFilter);
		$sSelect = $this->getSqlSelect();
		$sSort = $this->UseSessionForListSQL ? $this->getSessionOrderBy() : "";
		return ew_BuildSelectSql($sSelect, $this->getSqlWhere(), $this->getSqlGroupBy(),
			$this->getSqlHaving(), $this->getSqlOrderBy(), $sFilter, $sSort);
	}

	// Get ORDER BY clause
	function GetOrderBy() {
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql("", "", "", "", $this->getSqlOrderBy(), "", $sSort);
	}

	// Try to get record count
	function TryGetRecordCount($sql) {
		$cnt = -1;
		$pattern = "/^SELECT \* FROM/i";
		if (($this->TableType == 'TABLE' || $this->TableType == 'VIEW' || $this->TableType == 'LINKTABLE') && preg_match($pattern, $sql)) {
			$sql = "SELECT COUNT(*) FROM" . preg_replace($pattern, "", $sql);
		} else {
			$sql = "SELECT COUNT(*) FROM (" . $sql . ") EW_COUNT_TABLE";
		}
		$conn = &$this->Connection();
		if ($rs = $conn->Execute($sql)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$cnt = $rs->fields[0];
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Get record count based on filter (for detail record count in master table pages)
	function LoadRecordCount($filter) {
		$origFilter = $this->CurrentFilter;
		$this->CurrentFilter = $filter;
		$this->Recordset_Selecting($this->CurrentFilter);
		$select = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlSelect() : "SELECT * FROM " . $this->getSqlFrom();
		$groupBy = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlGroupBy() : "";
		$having = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlHaving() : "";
		$sql = ew_BuildSelectSql($select, $this->getSqlWhere(), $groupBy, $having, "", $this->CurrentFilter, "");
		$cnt = $this->TryGetRecordCount($sql);
		if ($cnt == -1) {
			if ($rs = $this->LoadRs($this->CurrentFilter)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Get record count (for current List page)
	function ListRecordCount() {
		$filter = $this->getSessionWhere();
		ew_AddFilter($filter, $this->CurrentFilter);
		$filter = $this->ApplyUserIDFilters($filter);
		$this->Recordset_Selecting($filter);
		$select = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlSelect() : "SELECT * FROM " . $this->getSqlFrom();
		$groupBy = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlGroupBy() : "";
		$having = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlHaving() : "";
		$sql = ew_BuildSelectSql($select, $this->getSqlWhere(), $groupBy, $having, "", $filter, "");
		$cnt = $this->TryGetRecordCount($sql);
		if ($cnt == -1) {
			$conn = &$this->Connection();
			if ($rs = $conn->Execute($sql)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// INSERT statement
	function InsertSQL(&$rs) {
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]) || $this->fields[$name]->FldIsCustom)
				continue;
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= ew_QuotedValue($value, $this->fields[$name]->FldDataType, $this->DBID) . ",";
		}
		$names = preg_replace('/,+$/', "", $names);
		$values = preg_replace('/,+$/', "", $values);
		return "INSERT INTO " . $this->UpdateTable . " ($names) VALUES ($values)";
	}

	// Insert
	function Insert(&$rs) {
		$conn = &$this->Connection();
		$bInsert = $conn->Execute($this->InsertSQL($rs));
		if ($bInsert) {
		}
		return $bInsert;
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "", $curfilter = TRUE) {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]) || $this->fields[$name]->FldIsCustom)
				continue;
			$sql .= $this->fields[$name]->FldExpression . "=";
			$sql .= ew_QuotedValue($value, $this->fields[$name]->FldDataType, $this->DBID) . ",";
		}
		$sql = preg_replace('/,+$/', "", $sql);
		$filter = ($curfilter) ? $this->CurrentFilter : "";
		if (is_array($where))
			$where = $this->ArrayToFilter($where);
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " WHERE " . $filter;
		return $sql;
	}

	// Update
	function Update(&$rs, $where = "", $rsold = NULL, $curfilter = TRUE) {
		$conn = &$this->Connection();
		$bUpdate = $conn->Execute($this->UpdateSQL($rs, $where, $curfilter));
		return $bUpdate;
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "", $curfilter = TRUE) {
		$sql = "DELETE FROM " . $this->UpdateTable . " WHERE ";
		if (is_array($where))
			$where = $this->ArrayToFilter($where);
		if ($rs) {
		}
		$filter = ($curfilter) ? $this->CurrentFilter : "";
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// Delete
	function Delete(&$rs, $where = "", $curfilter = TRUE) {
		$bDelete = TRUE;
		$conn = &$this->Connection();
		if ($bDelete)
			$bDelete = $conn->Execute($this->DeleteSQL($rs, $where, $curfilter));
		return $bDelete;
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		return $sKeyFilter;
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "periodical_report_viewlist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// Get modal caption
	function GetModalCaption($pageName) {
		global $Language;
		if ($pageName == "periodical_report_viewview.php")
			return $Language->Phrase("View");
		elseif ($pageName == "periodical_report_viewedit.php")
			return $Language->Phrase("Edit");
		elseif ($pageName == "periodical_report_viewadd.php")
			return $Language->Phrase("Add");
		else
			return "";
	}

	// List URL
	function GetListUrl() {
		return "periodical_report_viewlist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("periodical_report_viewview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("periodical_report_viewview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "periodical_report_viewadd.php?" . $this->UrlParm($parm);
		else
			$url = "periodical_report_viewadd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("periodical_report_viewedit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("periodical_report_viewadd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("periodical_report_viewdelete.php", $this->UrlParm());
	}

	// Add master url
	function AddMasterUrl($url) {
		return $url;
	}

	function KeyToJson() {
		$json = "";
		return "{" . $json . "}";
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort());
			return $this->AddMasterUrl(ew_CurrentPage() . "?" . $sUrlParm);
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = $_POST["key_m"];
			$cnt = count($arKeys);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = $_GET["key_m"];
			$cnt = count($arKeys);
		} elseif (!empty($_GET) || !empty($_POST)) {
			$isPost = ew_IsPost();

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		if (is_array($arKeys)) {
			foreach ($arKeys as $key) {
				$ar[] = $key;
			}
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($filter) {

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $filter;
		//$sql = $this->SQL();

		$sql = $this->GetSQL($filter, "");
		$conn = &$this->Connection();
		$rs = $conn->Execute($sql);
		return $rs;
	}

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
		$this->tran_date->setDbValue($rs->fields('tran_date'));
		$this->invoice_count->setDbValue($rs->fields('invoice_count'));
		$this->total_service_count->setDbValue($rs->fields('total_service_count'));
		$this->total_invoice_amount->setDbValue($rs->fields('total_invoice_amount'));
		$this->total_amount_recieved->setDbValue($rs->fields('total_amount_recieved'));
		$this->pending_amount->setDbValue($rs->fields('pending_amount'));
		$this->total_service_charge->setDbValue($rs->fields('total_service_charge'));
		$this->total_commission->setDbValue($rs->fields('total_commission'));
		$this->total_collection->setDbValue($rs->fields('total_collection'));
	}

	// Render list row values
	function RenderListRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

	// Common render codes
		// tran_date
		// invoice_count
		// total_service_count
		// total_invoice_amount
		// total_amount_recieved
		// pending_amount
		// total_service_charge
		// total_commission
		// total_collection
		// tran_date

		$this->tran_date->ViewValue = $this->tran_date->CurrentValue;
		$this->tran_date->ViewValue = ew_FormatDateTime($this->tran_date->ViewValue, 2);
		$this->tran_date->ViewCustomAttributes = "";

		// invoice_count
		$this->invoice_count->ViewValue = $this->invoice_count->CurrentValue;
		$this->invoice_count->ViewCustomAttributes = "";

		// total_service_count
		$this->total_service_count->ViewValue = $this->total_service_count->CurrentValue;
		$this->total_service_count->ViewCustomAttributes = "";

		// total_invoice_amount
		$this->total_invoice_amount->ViewValue = $this->total_invoice_amount->CurrentValue;
		$this->total_invoice_amount->ViewValue = ew_FormatNumber($this->total_invoice_amount->ViewValue, 2, -2, -2, -2);
		$this->total_invoice_amount->ViewCustomAttributes = "";

		// total_amount_recieved
		$this->total_amount_recieved->ViewValue = $this->total_amount_recieved->CurrentValue;
		$this->total_amount_recieved->ViewValue = ew_FormatNumber($this->total_amount_recieved->ViewValue, 2, -2, -2, -2);
		$this->total_amount_recieved->ViewCustomAttributes = "";

		// pending_amount
		$this->pending_amount->ViewValue = $this->pending_amount->CurrentValue;
		$this->pending_amount->ViewValue = ew_FormatNumber($this->pending_amount->ViewValue, 2, -2, -2, -2);
		$this->pending_amount->ViewCustomAttributes = "";

		// total_service_charge
		$this->total_service_charge->ViewValue = $this->total_service_charge->CurrentValue;
		$this->total_service_charge->ViewValue = ew_FormatNumber($this->total_service_charge->ViewValue, 2, -2, -2, -2);
		$this->total_service_charge->ViewCustomAttributes = "";

		// total_commission
		$this->total_commission->ViewValue = $this->total_commission->CurrentValue;
		$this->total_commission->ViewValue = ew_FormatNumber($this->total_commission->ViewValue, 2, -2, -2, -2);
		$this->total_commission->ViewCustomAttributes = "";

		// total_collection
		$this->total_collection->ViewValue = $this->total_collection->CurrentValue;
		$this->total_collection->ViewValue = ew_FormatNumber($this->total_collection->ViewValue, 2, -2, -2, -2);
		$this->total_collection->ViewCustomAttributes = "";

		// tran_date
		$this->tran_date->LinkCustomAttributes = "";
		$this->tran_date->HrefValue = "";
		$this->tran_date->TooltipValue = "";

		// invoice_count
		$this->invoice_count->LinkCustomAttributes = "";
		$this->invoice_count->HrefValue = "";
		$this->invoice_count->TooltipValue = "";

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

		// Call Row Rendered event
		$this->Row_Rendered();

		// Save data for Custom Template
		$this->Rows[] = $this->CustomTemplateFieldValues();
	}

	// Render edit row values
	function RenderEditRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

		// tran_date
		$this->tran_date->EditAttrs["class"] = "form-control";
		$this->tran_date->EditCustomAttributes = "";
		$this->tran_date->EditValue = ew_FormatDateTime($this->tran_date->CurrentValue, 2);
		$this->tran_date->PlaceHolder = ew_RemoveHtml($this->tran_date->FldCaption());

		// invoice_count
		$this->invoice_count->EditAttrs["class"] = "form-control";
		$this->invoice_count->EditCustomAttributes = "";
		$this->invoice_count->EditValue = $this->invoice_count->CurrentValue;
		$this->invoice_count->PlaceHolder = ew_RemoveHtml($this->invoice_count->FldCaption());

		// total_service_count
		$this->total_service_count->EditAttrs["class"] = "form-control";
		$this->total_service_count->EditCustomAttributes = "";
		$this->total_service_count->EditValue = $this->total_service_count->CurrentValue;
		$this->total_service_count->PlaceHolder = ew_RemoveHtml($this->total_service_count->FldCaption());

		// total_invoice_amount
		$this->total_invoice_amount->EditAttrs["class"] = "form-control";
		$this->total_invoice_amount->EditCustomAttributes = "";
		$this->total_invoice_amount->EditValue = $this->total_invoice_amount->CurrentValue;
		$this->total_invoice_amount->PlaceHolder = ew_RemoveHtml($this->total_invoice_amount->FldCaption());
		if (strval($this->total_invoice_amount->EditValue) <> "" && is_numeric($this->total_invoice_amount->EditValue)) $this->total_invoice_amount->EditValue = ew_FormatNumber($this->total_invoice_amount->EditValue, -2, -2, -2, -2);

		// total_amount_recieved
		$this->total_amount_recieved->EditAttrs["class"] = "form-control";
		$this->total_amount_recieved->EditCustomAttributes = "";
		$this->total_amount_recieved->EditValue = $this->total_amount_recieved->CurrentValue;
		$this->total_amount_recieved->PlaceHolder = ew_RemoveHtml($this->total_amount_recieved->FldCaption());
		if (strval($this->total_amount_recieved->EditValue) <> "" && is_numeric($this->total_amount_recieved->EditValue)) $this->total_amount_recieved->EditValue = ew_FormatNumber($this->total_amount_recieved->EditValue, -2, -2, -2, -2);

		// pending_amount
		$this->pending_amount->EditAttrs["class"] = "form-control";
		$this->pending_amount->EditCustomAttributes = "";
		$this->pending_amount->EditValue = $this->pending_amount->CurrentValue;
		$this->pending_amount->PlaceHolder = ew_RemoveHtml($this->pending_amount->FldCaption());
		if (strval($this->pending_amount->EditValue) <> "" && is_numeric($this->pending_amount->EditValue)) $this->pending_amount->EditValue = ew_FormatNumber($this->pending_amount->EditValue, -2, -2, -2, -2);

		// total_service_charge
		$this->total_service_charge->EditAttrs["class"] = "form-control";
		$this->total_service_charge->EditCustomAttributes = "";
		$this->total_service_charge->EditValue = $this->total_service_charge->CurrentValue;
		$this->total_service_charge->PlaceHolder = ew_RemoveHtml($this->total_service_charge->FldCaption());
		if (strval($this->total_service_charge->EditValue) <> "" && is_numeric($this->total_service_charge->EditValue)) $this->total_service_charge->EditValue = ew_FormatNumber($this->total_service_charge->EditValue, -2, -2, -2, -2);

		// total_commission
		$this->total_commission->EditAttrs["class"] = "form-control";
		$this->total_commission->EditCustomAttributes = "";
		$this->total_commission->EditValue = $this->total_commission->CurrentValue;
		$this->total_commission->PlaceHolder = ew_RemoveHtml($this->total_commission->FldCaption());
		if (strval($this->total_commission->EditValue) <> "" && is_numeric($this->total_commission->EditValue)) $this->total_commission->EditValue = ew_FormatNumber($this->total_commission->EditValue, -2, -2, -2, -2);

		// total_collection
		$this->total_collection->EditAttrs["class"] = "form-control";
		$this->total_collection->EditCustomAttributes = "";
		$this->total_collection->EditValue = $this->total_collection->CurrentValue;
		$this->total_collection->PlaceHolder = ew_RemoveHtml($this->total_collection->FldCaption());
		if (strval($this->total_collection->EditValue) <> "" && is_numeric($this->total_collection->EditValue)) $this->total_collection->EditValue = ew_FormatNumber($this->total_collection->EditValue, -2, -2, -2, -2);

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
			if (is_numeric($this->invoice_count->CurrentValue))
				$this->invoice_count->Total += $this->invoice_count->CurrentValue; // Accumulate total
			if (is_numeric($this->total_service_count->CurrentValue))
				$this->total_service_count->Total += $this->total_service_count->CurrentValue; // Accumulate total
			if (is_numeric($this->total_invoice_amount->CurrentValue))
				$this->total_invoice_amount->Total += $this->total_invoice_amount->CurrentValue; // Accumulate total
			if (is_numeric($this->total_amount_recieved->CurrentValue))
				$this->total_amount_recieved->Total += $this->total_amount_recieved->CurrentValue; // Accumulate total
			if (is_numeric($this->pending_amount->CurrentValue))
				$this->pending_amount->Total += $this->pending_amount->CurrentValue; // Accumulate total
			if (is_numeric($this->total_service_charge->CurrentValue))
				$this->total_service_charge->Total += $this->total_service_charge->CurrentValue; // Accumulate total
			if (is_numeric($this->total_commission->CurrentValue))
				$this->total_commission->Total += $this->total_commission->CurrentValue; // Accumulate total
			if (is_numeric($this->total_collection->CurrentValue))
				$this->total_collection->Total += $this->total_collection->CurrentValue; // Accumulate total
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {
			$this->invoice_count->CurrentValue = $this->invoice_count->Total;
			$this->invoice_count->ViewValue = $this->invoice_count->CurrentValue;
			$this->invoice_count->ViewCustomAttributes = "";
			$this->invoice_count->HrefValue = ""; // Clear href value
			$this->total_service_count->CurrentValue = $this->total_service_count->Total;
			$this->total_service_count->ViewValue = $this->total_service_count->CurrentValue;
			$this->total_service_count->ViewCustomAttributes = "";
			$this->total_service_count->HrefValue = ""; // Clear href value
			$this->total_invoice_amount->CurrentValue = $this->total_invoice_amount->Total;
			$this->total_invoice_amount->ViewValue = $this->total_invoice_amount->CurrentValue;
			$this->total_invoice_amount->ViewValue = ew_FormatNumber($this->total_invoice_amount->ViewValue, 2, -2, -2, -2);
			$this->total_invoice_amount->ViewCustomAttributes = "";
			$this->total_invoice_amount->HrefValue = ""; // Clear href value
			$this->total_amount_recieved->CurrentValue = $this->total_amount_recieved->Total;
			$this->total_amount_recieved->ViewValue = $this->total_amount_recieved->CurrentValue;
			$this->total_amount_recieved->ViewValue = ew_FormatNumber($this->total_amount_recieved->ViewValue, 2, -2, -2, -2);
			$this->total_amount_recieved->ViewCustomAttributes = "";
			$this->total_amount_recieved->HrefValue = ""; // Clear href value
			$this->pending_amount->CurrentValue = $this->pending_amount->Total;
			$this->pending_amount->ViewValue = $this->pending_amount->CurrentValue;
			$this->pending_amount->ViewValue = ew_FormatNumber($this->pending_amount->ViewValue, 2, -2, -2, -2);
			$this->pending_amount->ViewCustomAttributes = "";
			$this->pending_amount->HrefValue = ""; // Clear href value
			$this->total_service_charge->CurrentValue = $this->total_service_charge->Total;
			$this->total_service_charge->ViewValue = $this->total_service_charge->CurrentValue;
			$this->total_service_charge->ViewValue = ew_FormatNumber($this->total_service_charge->ViewValue, 2, -2, -2, -2);
			$this->total_service_charge->ViewCustomAttributes = "";
			$this->total_service_charge->HrefValue = ""; // Clear href value
			$this->total_commission->CurrentValue = $this->total_commission->Total;
			$this->total_commission->ViewValue = $this->total_commission->CurrentValue;
			$this->total_commission->ViewValue = ew_FormatNumber($this->total_commission->ViewValue, 2, -2, -2, -2);
			$this->total_commission->ViewCustomAttributes = "";
			$this->total_commission->HrefValue = ""; // Clear href value
			$this->total_collection->CurrentValue = $this->total_collection->Total;
			$this->total_collection->ViewValue = $this->total_collection->CurrentValue;
			$this->total_collection->ViewValue = ew_FormatNumber($this->total_collection->ViewValue, 2, -2, -2, -2);
			$this->total_collection->ViewCustomAttributes = "";
			$this->total_collection->HrefValue = ""; // Clear href value

		// Call Row Rendered event
		$this->Row_Rendered();
	}
	var $ExportDoc;

	// Export data in HTML/CSV/Word/Excel/Email/PDF format
	function ExportDocument(&$Doc, &$Recordset, $StartRec, $StopRec, $ExportPageType = "") {
		if (!$Recordset || !$Doc)
			return;
		if (!$Doc->ExportCustom) {

			// Write header
			$Doc->ExportTableHeader();
			if ($Doc->Horizontal) { // Horizontal format, write header
				$Doc->BeginExportRow();
				if ($ExportPageType == "view") {
					if ($this->tran_date->Exportable) $Doc->ExportCaption($this->tran_date);
					if ($this->invoice_count->Exportable) $Doc->ExportCaption($this->invoice_count);
					if ($this->total_service_count->Exportable) $Doc->ExportCaption($this->total_service_count);
					if ($this->total_invoice_amount->Exportable) $Doc->ExportCaption($this->total_invoice_amount);
					if ($this->total_amount_recieved->Exportable) $Doc->ExportCaption($this->total_amount_recieved);
					if ($this->pending_amount->Exportable) $Doc->ExportCaption($this->pending_amount);
					if ($this->total_service_charge->Exportable) $Doc->ExportCaption($this->total_service_charge);
					if ($this->total_commission->Exportable) $Doc->ExportCaption($this->total_commission);
					if ($this->total_collection->Exportable) $Doc->ExportCaption($this->total_collection);
				} else {
					if ($this->tran_date->Exportable) $Doc->ExportCaption($this->tran_date);
					if ($this->invoice_count->Exportable) $Doc->ExportCaption($this->invoice_count);
					if ($this->total_service_count->Exportable) $Doc->ExportCaption($this->total_service_count);
					if ($this->total_invoice_amount->Exportable) $Doc->ExportCaption($this->total_invoice_amount);
					if ($this->total_amount_recieved->Exportable) $Doc->ExportCaption($this->total_amount_recieved);
					if ($this->pending_amount->Exportable) $Doc->ExportCaption($this->pending_amount);
					if ($this->total_service_charge->Exportable) $Doc->ExportCaption($this->total_service_charge);
					if ($this->total_commission->Exportable) $Doc->ExportCaption($this->total_commission);
					if ($this->total_collection->Exportable) $Doc->ExportCaption($this->total_collection);
				}
				$Doc->EndExportRow();
			}
		}

		// Move to first record
		$RecCnt = $StartRec - 1;
		if (!$Recordset->EOF) {
			$Recordset->MoveFirst();
			if ($StartRec > 1)
				$Recordset->Move($StartRec - 1);
		}
		while (!$Recordset->EOF && $RecCnt < $StopRec) {
			$RecCnt++;
			if (intval($RecCnt) >= intval($StartRec)) {
				$RowCnt = intval($RecCnt) - intval($StartRec) + 1;

				// Page break
				if ($this->ExportPageBreakCount > 0) {
					if ($RowCnt > 1 && ($RowCnt - 1) % $this->ExportPageBreakCount == 0)
						$Doc->ExportPageBreak();
				}
				$this->LoadListRowValues($Recordset);
				$this->AggregateListRowValues(); // Aggregate row values

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				if (!$Doc->ExportCustom) {
					$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
					if ($ExportPageType == "view") {
						if ($this->tran_date->Exportable) $Doc->ExportField($this->tran_date);
						if ($this->invoice_count->Exportable) $Doc->ExportField($this->invoice_count);
						if ($this->total_service_count->Exportable) $Doc->ExportField($this->total_service_count);
						if ($this->total_invoice_amount->Exportable) $Doc->ExportField($this->total_invoice_amount);
						if ($this->total_amount_recieved->Exportable) $Doc->ExportField($this->total_amount_recieved);
						if ($this->pending_amount->Exportable) $Doc->ExportField($this->pending_amount);
						if ($this->total_service_charge->Exportable) $Doc->ExportField($this->total_service_charge);
						if ($this->total_commission->Exportable) $Doc->ExportField($this->total_commission);
						if ($this->total_collection->Exportable) $Doc->ExportField($this->total_collection);
					} else {
						if ($this->tran_date->Exportable) $Doc->ExportField($this->tran_date);
						if ($this->invoice_count->Exportable) $Doc->ExportField($this->invoice_count);
						if ($this->total_service_count->Exportable) $Doc->ExportField($this->total_service_count);
						if ($this->total_invoice_amount->Exportable) $Doc->ExportField($this->total_invoice_amount);
						if ($this->total_amount_recieved->Exportable) $Doc->ExportField($this->total_amount_recieved);
						if ($this->pending_amount->Exportable) $Doc->ExportField($this->pending_amount);
						if ($this->total_service_charge->Exportable) $Doc->ExportField($this->total_service_charge);
						if ($this->total_commission->Exportable) $Doc->ExportField($this->total_commission);
						if ($this->total_collection->Exportable) $Doc->ExportField($this->total_collection);
					}
					$Doc->EndExportRow($RowCnt);
				}
			}

			// Call Row Export server event
			if ($Doc->ExportCustom)
				$this->Row_Export($Recordset->fields);
			$Recordset->MoveNext();
		}

		// Export aggregates (horizontal format only)
		if ($Doc->Horizontal) {
			$this->RowType = EW_ROWTYPE_AGGREGATE;
			$this->ResetAttrs();
			$this->AggregateListRow();
			if (!$Doc->ExportCustom) {
				$Doc->BeginExportRow(-1);
				if ($this->tran_date->Exportable) $Doc->ExportAggregate($this->tran_date, '');
				if ($this->invoice_count->Exportable) $Doc->ExportAggregate($this->invoice_count, 'TOTAL');
				if ($this->total_service_count->Exportable) $Doc->ExportAggregate($this->total_service_count, 'TOTAL');
				if ($this->total_invoice_amount->Exportable) $Doc->ExportAggregate($this->total_invoice_amount, 'TOTAL');
				if ($this->total_amount_recieved->Exportable) $Doc->ExportAggregate($this->total_amount_recieved, 'TOTAL');
				if ($this->pending_amount->Exportable) $Doc->ExportAggregate($this->pending_amount, 'TOTAL');
				if ($this->total_service_charge->Exportable) $Doc->ExportAggregate($this->total_service_charge, 'TOTAL');
				if ($this->total_commission->Exportable) $Doc->ExportAggregate($this->total_commission, 'TOTAL');
				if ($this->total_collection->Exportable) $Doc->ExportAggregate($this->total_collection, 'TOTAL');
				$Doc->EndExportRow();
			}
		}
		if (!$Doc->ExportCustom) {
			$Doc->ExportTableFooter();
		}
	}

	// Get auto fill value
	function GetAutoFill($id, $val) {
		$rsarr = array();
		$rowcnt = 0;

		// Output
		if (is_array($rsarr) && $rowcnt > 0) {
			$fldcnt = count($rsarr[0]);
			for ($i = 0; $i < $rowcnt; $i++) {
				for ($j = 0; $j < $fldcnt; $j++) {
					$str = strval($rsarr[$i][$j]);
					$str = ew_ConvertToUtf8($str);
					if (isset($post["keepCRLF"])) {
						$str = str_replace(array("\r", "\n"), array("\\r", "\\n"), $str);
					} else {
						$str = str_replace(array("\r", "\n"), array(" ", " "), $str);
					}
					$rsarr[$i][$j] = $str;
				}
			}
			return ew_ArrayToJson($rsarr);
		} else {
			return FALSE;
		}
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Recordset Search Validated event
	function Recordset_SearchValidated() {

		// Example:
		//$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value

	}

	// Recordset Searching event
	function Recordset_Searching(&$filter) {

		// Enter your code here
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Inserting event
	function Row_Inserting($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted($rsold, &$rsnew) {

		//echo "Row Inserted"
	}

	// Row Updating event
	function Row_Updating($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Updated event
	function Row_Updated($rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Update Conflict event
	function Row_UpdateConflict($rsold, &$rsnew) {

		// Enter your code here
		// To ignore conflict, set return value to FALSE

		return TRUE;
	}

	// Grid Inserting event
	function Grid_Inserting() {

		// Enter your code here
		// To reject grid insert, set return value to FALSE

		return TRUE;
	}

	// Grid Inserted event
	function Grid_Inserted($rsnew) {

		//echo "Grid Inserted";
	}

	// Grid Updating event
	function Grid_Updating($rsold) {

		// Enter your code here
		// To reject grid update, set return value to FALSE

		return TRUE;
	}

	// Grid Updated event
	function Grid_Updated($rsold, $rsnew) {

		//echo "Grid Updated";
	}

	// Row Deleting event
	function Row_Deleting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		//var_dump($fld->FldName, $fld->LookupFilters, $filter); // Uncomment to view the filter
		// Enter your code here

	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>);

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
