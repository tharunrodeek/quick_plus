<?php

// Global variable for table object
$items_report_view = NULL;

//
// Table class for items_report_view
//
class citems_report_view extends cTable {
	var $stock_id;
	var $item_description;
	var $long_description;
	var $category_id;
	var $service_charge;
	var $govt_fee;
	var $pf_amount;
	var $bank_service_charge;
	var $bank_service_charge_vat;
	var $commission_loc_user;
	var $commission_non_loc_user;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'items_report_view';
		$this->TableName = 'items_report_view';
		$this->TableType = 'VIEW';

		// Update Table
		$this->UpdateTable = "`items_report_view`";
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

		// stock_id
		$this->stock_id = new cField('items_report_view', 'items_report_view', 'x_stock_id', 'stock_id', '`stock_id`', '`stock_id`', 200, -1, FALSE, '`stock_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->stock_id->Sortable = TRUE; // Allow sort
		$this->fields['stock_id'] = &$this->stock_id;

		// item_description
		$this->item_description = new cField('items_report_view', 'items_report_view', 'x_item_description', 'item_description', '`item_description`', '`item_description`', 200, -1, FALSE, '`item_description`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->item_description->Sortable = TRUE; // Allow sort
		$this->fields['item_description'] = &$this->item_description;

		// long_description
		$this->long_description = new cField('items_report_view', 'items_report_view', 'x_long_description', 'long_description', '`long_description`', '`long_description`', 200, -1, FALSE, '`long_description`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->long_description->Sortable = TRUE; // Allow sort
		$this->fields['long_description'] = &$this->long_description;

		// category_id
		$this->category_id = new cField('items_report_view', 'items_report_view', 'x_category_id', 'category_id', '`category_id`', '`category_id`', 3, -1, FALSE, '`category_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->category_id->Sortable = TRUE; // Allow sort
		$this->category_id->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->category_id->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['category_id'] = &$this->category_id;

		// service_charge
		$this->service_charge = new cField('items_report_view', 'items_report_view', 'x_service_charge', 'service_charge', '`service_charge`', '`service_charge`', 5, -1, FALSE, '`service_charge`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->service_charge->Sortable = TRUE; // Allow sort
		$this->service_charge->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['service_charge'] = &$this->service_charge;

		// govt_fee
		$this->govt_fee = new cField('items_report_view', 'items_report_view', 'x_govt_fee', 'govt_fee', '`govt_fee`', '`govt_fee`', 5, -1, FALSE, '`govt_fee`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->govt_fee->Sortable = TRUE; // Allow sort
		$this->govt_fee->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['govt_fee'] = &$this->govt_fee;

		// pf_amount
		$this->pf_amount = new cField('items_report_view', 'items_report_view', 'x_pf_amount', 'pf_amount', '`pf_amount`', '`pf_amount`', 5, -1, FALSE, '`pf_amount`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->pf_amount->Sortable = TRUE; // Allow sort
		$this->pf_amount->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['pf_amount'] = &$this->pf_amount;

		// bank_service_charge
		$this->bank_service_charge = new cField('items_report_view', 'items_report_view', 'x_bank_service_charge', 'bank_service_charge', '`bank_service_charge`', '`bank_service_charge`', 5, -1, FALSE, '`bank_service_charge`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->bank_service_charge->Sortable = TRUE; // Allow sort
		$this->bank_service_charge->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['bank_service_charge'] = &$this->bank_service_charge;

		// bank_service_charge_vat
		$this->bank_service_charge_vat = new cField('items_report_view', 'items_report_view', 'x_bank_service_charge_vat', 'bank_service_charge_vat', '`bank_service_charge_vat`', '`bank_service_charge_vat`', 5, -1, FALSE, '`bank_service_charge_vat`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->bank_service_charge_vat->Sortable = TRUE; // Allow sort
		$this->bank_service_charge_vat->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['bank_service_charge_vat'] = &$this->bank_service_charge_vat;

		// commission_loc_user
		$this->commission_loc_user = new cField('items_report_view', 'items_report_view', 'x_commission_loc_user', 'commission_loc_user', '`commission_loc_user`', '`commission_loc_user`', 5, -1, FALSE, '`commission_loc_user`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->commission_loc_user->Sortable = TRUE; // Allow sort
		$this->commission_loc_user->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['commission_loc_user'] = &$this->commission_loc_user;

		// commission_non_loc_user
		$this->commission_non_loc_user = new cField('items_report_view', 'items_report_view', 'x_commission_non_loc_user', 'commission_non_loc_user', '`commission_non_loc_user`', '`commission_non_loc_user`', 5, -1, FALSE, '`commission_non_loc_user`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->commission_non_loc_user->Sortable = TRUE; // Allow sort
		$this->commission_non_loc_user->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['commission_non_loc_user'] = &$this->commission_non_loc_user;
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`items_report_view`";
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
			if (array_key_exists('stock_id', $rs))
				ew_AddFilter($where, ew_QuotedName('stock_id', $this->DBID) . '=' . ew_QuotedValue($rs['stock_id'], $this->stock_id->FldDataType, $this->DBID));
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
		return "`stock_id` = '@stock_id@'";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (is_null($this->stock_id->CurrentValue))
			return "0=1"; // Invalid key
		else
			$sKeyFilter = str_replace("@stock_id@", ew_AdjustSql($this->stock_id->CurrentValue, $this->DBID), $sKeyFilter); // Replace key value
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
			return "items_report_viewlist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// Get modal caption
	function GetModalCaption($pageName) {
		global $Language;
		if ($pageName == "items_report_viewview.php")
			return $Language->Phrase("View");
		elseif ($pageName == "items_report_viewedit.php")
			return $Language->Phrase("Edit");
		elseif ($pageName == "items_report_viewadd.php")
			return $Language->Phrase("Add");
		else
			return "";
	}

	// List URL
	function GetListUrl() {
		return "items_report_viewlist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("items_report_viewview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("items_report_viewview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "items_report_viewadd.php?" . $this->UrlParm($parm);
		else
			$url = "items_report_viewadd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("items_report_viewedit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("items_report_viewadd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("items_report_viewdelete.php", $this->UrlParm());
	}

	// Add master url
	function AddMasterUrl($url) {
		return $url;
	}

	function KeyToJson() {
		$json = "";
		$json .= "stock_id:" . ew_VarToJson($this->stock_id->CurrentValue, "string", "'");
		return "{" . $json . "}";
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->stock_id->CurrentValue)) {
			$sUrl .= "stock_id=" . urlencode($this->stock_id->CurrentValue);
		} else {
			return "javascript:ew_Alert(ewLanguage.Phrase('InvalidRecord'));";
		}
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
			if ($isPost && isset($_POST["stock_id"]))
				$arKeys[] = $_POST["stock_id"];
			elseif (isset($_GET["stock_id"]))
				$arKeys[] = $_GET["stock_id"];
			else
				$arKeys = NULL; // Do not setup

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
			$this->stock_id->CurrentValue = $key;
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
		$this->stock_id->setDbValue($rs->fields('stock_id'));
		$this->item_description->setDbValue($rs->fields('item_description'));
		$this->long_description->setDbValue($rs->fields('long_description'));
		$this->category_id->setDbValue($rs->fields('category_id'));
		$this->service_charge->setDbValue($rs->fields('service_charge'));
		$this->govt_fee->setDbValue($rs->fields('govt_fee'));
		$this->pf_amount->setDbValue($rs->fields('pf_amount'));
		$this->bank_service_charge->setDbValue($rs->fields('bank_service_charge'));
		$this->bank_service_charge_vat->setDbValue($rs->fields('bank_service_charge_vat'));
		$this->commission_loc_user->setDbValue($rs->fields('commission_loc_user'));
		$this->commission_non_loc_user->setDbValue($rs->fields('commission_non_loc_user'));
	}

	// Render list row values
	function RenderListRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

	// Common render codes
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

		// stock_id
		$this->stock_id->EditAttrs["class"] = "form-control";
		$this->stock_id->EditCustomAttributes = "";
		$this->stock_id->EditValue = $this->stock_id->CurrentValue;
		$this->stock_id->ViewCustomAttributes = "";

		// item_description
		$this->item_description->EditAttrs["class"] = "form-control";
		$this->item_description->EditCustomAttributes = "";
		$this->item_description->EditValue = $this->item_description->CurrentValue;
		$this->item_description->PlaceHolder = ew_RemoveHtml($this->item_description->FldCaption());

		// long_description
		$this->long_description->EditAttrs["class"] = "form-control";
		$this->long_description->EditCustomAttributes = "";
		$this->long_description->EditValue = $this->long_description->CurrentValue;
		$this->long_description->PlaceHolder = ew_RemoveHtml($this->long_description->FldCaption());

		// category_id
		$this->category_id->EditAttrs["class"] = "form-control";
		$this->category_id->EditCustomAttributes = "";

		// service_charge
		$this->service_charge->EditAttrs["class"] = "form-control";
		$this->service_charge->EditCustomAttributes = "";
		$this->service_charge->EditValue = $this->service_charge->CurrentValue;
		$this->service_charge->PlaceHolder = ew_RemoveHtml($this->service_charge->FldCaption());
		if (strval($this->service_charge->EditValue) <> "" && is_numeric($this->service_charge->EditValue)) $this->service_charge->EditValue = ew_FormatNumber($this->service_charge->EditValue, -2, -1, -2, 0);

		// govt_fee
		$this->govt_fee->EditAttrs["class"] = "form-control";
		$this->govt_fee->EditCustomAttributes = "";
		$this->govt_fee->EditValue = $this->govt_fee->CurrentValue;
		$this->govt_fee->PlaceHolder = ew_RemoveHtml($this->govt_fee->FldCaption());
		if (strval($this->govt_fee->EditValue) <> "" && is_numeric($this->govt_fee->EditValue)) $this->govt_fee->EditValue = ew_FormatNumber($this->govt_fee->EditValue, -2, -1, -2, 0);

		// pf_amount
		$this->pf_amount->EditAttrs["class"] = "form-control";
		$this->pf_amount->EditCustomAttributes = "";
		$this->pf_amount->EditValue = $this->pf_amount->CurrentValue;
		$this->pf_amount->PlaceHolder = ew_RemoveHtml($this->pf_amount->FldCaption());
		if (strval($this->pf_amount->EditValue) <> "" && is_numeric($this->pf_amount->EditValue)) $this->pf_amount->EditValue = ew_FormatNumber($this->pf_amount->EditValue, -2, -1, -2, 0);

		// bank_service_charge
		$this->bank_service_charge->EditAttrs["class"] = "form-control";
		$this->bank_service_charge->EditCustomAttributes = "";
		$this->bank_service_charge->EditValue = $this->bank_service_charge->CurrentValue;
		$this->bank_service_charge->PlaceHolder = ew_RemoveHtml($this->bank_service_charge->FldCaption());
		if (strval($this->bank_service_charge->EditValue) <> "" && is_numeric($this->bank_service_charge->EditValue)) $this->bank_service_charge->EditValue = ew_FormatNumber($this->bank_service_charge->EditValue, -2, -1, -2, 0);

		// bank_service_charge_vat
		$this->bank_service_charge_vat->EditAttrs["class"] = "form-control";
		$this->bank_service_charge_vat->EditCustomAttributes = "";
		$this->bank_service_charge_vat->EditValue = $this->bank_service_charge_vat->CurrentValue;
		$this->bank_service_charge_vat->PlaceHolder = ew_RemoveHtml($this->bank_service_charge_vat->FldCaption());
		if (strval($this->bank_service_charge_vat->EditValue) <> "" && is_numeric($this->bank_service_charge_vat->EditValue)) $this->bank_service_charge_vat->EditValue = ew_FormatNumber($this->bank_service_charge_vat->EditValue, -2, -1, -2, 0);

		// commission_loc_user
		$this->commission_loc_user->EditAttrs["class"] = "form-control";
		$this->commission_loc_user->EditCustomAttributes = "";
		$this->commission_loc_user->EditValue = $this->commission_loc_user->CurrentValue;
		$this->commission_loc_user->PlaceHolder = ew_RemoveHtml($this->commission_loc_user->FldCaption());
		if (strval($this->commission_loc_user->EditValue) <> "" && is_numeric($this->commission_loc_user->EditValue)) $this->commission_loc_user->EditValue = ew_FormatNumber($this->commission_loc_user->EditValue, -2, -1, -2, 0);

		// commission_non_loc_user
		$this->commission_non_loc_user->EditAttrs["class"] = "form-control";
		$this->commission_non_loc_user->EditCustomAttributes = "";
		$this->commission_non_loc_user->EditValue = $this->commission_non_loc_user->CurrentValue;
		$this->commission_non_loc_user->PlaceHolder = ew_RemoveHtml($this->commission_non_loc_user->FldCaption());
		if (strval($this->commission_non_loc_user->EditValue) <> "" && is_numeric($this->commission_non_loc_user->EditValue)) $this->commission_non_loc_user->EditValue = ew_FormatNumber($this->commission_non_loc_user->EditValue, -2, -1, -2, 0);

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {

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
					if ($this->stock_id->Exportable) $Doc->ExportCaption($this->stock_id);
					if ($this->item_description->Exportable) $Doc->ExportCaption($this->item_description);
					if ($this->long_description->Exportable) $Doc->ExportCaption($this->long_description);
					if ($this->category_id->Exportable) $Doc->ExportCaption($this->category_id);
					if ($this->service_charge->Exportable) $Doc->ExportCaption($this->service_charge);
					if ($this->govt_fee->Exportable) $Doc->ExportCaption($this->govt_fee);
					if ($this->pf_amount->Exportable) $Doc->ExportCaption($this->pf_amount);
					if ($this->bank_service_charge->Exportable) $Doc->ExportCaption($this->bank_service_charge);
					if ($this->bank_service_charge_vat->Exportable) $Doc->ExportCaption($this->bank_service_charge_vat);
					if ($this->commission_loc_user->Exportable) $Doc->ExportCaption($this->commission_loc_user);
					if ($this->commission_non_loc_user->Exportable) $Doc->ExportCaption($this->commission_non_loc_user);
				} else {
					if ($this->stock_id->Exportable) $Doc->ExportCaption($this->stock_id);
					if ($this->item_description->Exportable) $Doc->ExportCaption($this->item_description);
					if ($this->category_id->Exportable) $Doc->ExportCaption($this->category_id);
					if ($this->service_charge->Exportable) $Doc->ExportCaption($this->service_charge);
					if ($this->govt_fee->Exportable) $Doc->ExportCaption($this->govt_fee);
					if ($this->pf_amount->Exportable) $Doc->ExportCaption($this->pf_amount);
					if ($this->bank_service_charge->Exportable) $Doc->ExportCaption($this->bank_service_charge);
					if ($this->bank_service_charge_vat->Exportable) $Doc->ExportCaption($this->bank_service_charge_vat);
					if ($this->commission_loc_user->Exportable) $Doc->ExportCaption($this->commission_loc_user);
					if ($this->commission_non_loc_user->Exportable) $Doc->ExportCaption($this->commission_non_loc_user);
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

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				if (!$Doc->ExportCustom) {
					$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
					if ($ExportPageType == "view") {
						if ($this->stock_id->Exportable) $Doc->ExportField($this->stock_id);
						if ($this->item_description->Exportable) $Doc->ExportField($this->item_description);
						if ($this->long_description->Exportable) $Doc->ExportField($this->long_description);
						if ($this->category_id->Exportable) $Doc->ExportField($this->category_id);
						if ($this->service_charge->Exportable) $Doc->ExportField($this->service_charge);
						if ($this->govt_fee->Exportable) $Doc->ExportField($this->govt_fee);
						if ($this->pf_amount->Exportable) $Doc->ExportField($this->pf_amount);
						if ($this->bank_service_charge->Exportable) $Doc->ExportField($this->bank_service_charge);
						if ($this->bank_service_charge_vat->Exportable) $Doc->ExportField($this->bank_service_charge_vat);
						if ($this->commission_loc_user->Exportable) $Doc->ExportField($this->commission_loc_user);
						if ($this->commission_non_loc_user->Exportable) $Doc->ExportField($this->commission_non_loc_user);
					} else {
						if ($this->stock_id->Exportable) $Doc->ExportField($this->stock_id);
						if ($this->item_description->Exportable) $Doc->ExportField($this->item_description);
						if ($this->category_id->Exportable) $Doc->ExportField($this->category_id);
						if ($this->service_charge->Exportable) $Doc->ExportField($this->service_charge);
						if ($this->govt_fee->Exportable) $Doc->ExportField($this->govt_fee);
						if ($this->pf_amount->Exportable) $Doc->ExportField($this->pf_amount);
						if ($this->bank_service_charge->Exportable) $Doc->ExportField($this->bank_service_charge);
						if ($this->bank_service_charge_vat->Exportable) $Doc->ExportField($this->bank_service_charge_vat);
						if ($this->commission_loc_user->Exportable) $Doc->ExportField($this->commission_loc_user);
						if ($this->commission_non_loc_user->Exportable) $Doc->ExportField($this->commission_non_loc_user);
					}
					$Doc->EndExportRow($RowCnt);
				}
			}

			// Call Row Export server event
			if ($Doc->ExportCustom)
				$this->Row_Export($Recordset->fields);
			$Recordset->MoveNext();
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
