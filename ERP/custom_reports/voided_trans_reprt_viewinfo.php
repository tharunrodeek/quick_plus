<?php

// Global variable for table object
$voided_trans_reprt_view = NULL;

//
// Table class for voided_trans_reprt_view
//
class cvoided_trans_reprt_view extends cTable {
	var $reference;
	var $voided_date;
	var $trans_date;
	var $voided_by;
	var $transaction_done_by;
	var $memo_;
	var $type;
	var $amount;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'voided_trans_reprt_view';
		$this->TableName = 'voided_trans_reprt_view';
		$this->TableType = 'VIEW';

		// Update Table
		$this->UpdateTable = "`voided_trans_reprt_view`";
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

		// reference
		$this->reference = new cField('voided_trans_reprt_view', 'voided_trans_reprt_view', 'x_reference', 'reference', '`reference`', '`reference`', 200, -1, FALSE, '`reference`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->reference->Sortable = TRUE; // Allow sort
		$this->fields['reference'] = &$this->reference;

		// voided_date
		$this->voided_date = new cField('voided_trans_reprt_view', 'voided_trans_reprt_view', 'x_voided_date', 'voided_date', '`voided_date`', ew_CastDateFieldForLike('`voided_date`', 0, "DB"), 133, 0, FALSE, '`voided_date`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->voided_date->Sortable = TRUE; // Allow sort
		$this->voided_date->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['voided_date'] = &$this->voided_date;

		// trans_date
		$this->trans_date = new cField('voided_trans_reprt_view', 'voided_trans_reprt_view', 'x_trans_date', 'trans_date', '`trans_date`', ew_CastDateFieldForLike('`trans_date`', 0, "DB"), 133, 0, FALSE, '`trans_date`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->trans_date->Sortable = TRUE; // Allow sort
		$this->trans_date->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['trans_date'] = &$this->trans_date;

		// voided_by
		$this->voided_by = new cField('voided_trans_reprt_view', 'voided_trans_reprt_view', 'x_voided_by', 'voided_by', '`voided_by`', '`voided_by`', 200, -1, FALSE, '`voided_by`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->voided_by->Sortable = TRUE; // Allow sort
		$this->fields['voided_by'] = &$this->voided_by;

		// transaction_done_by
		$this->transaction_done_by = new cField('voided_trans_reprt_view', 'voided_trans_reprt_view', 'x_transaction_done_by', 'transaction_done_by', '`transaction_done_by`', '`transaction_done_by`', 200, -1, FALSE, '`transaction_done_by`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->transaction_done_by->Sortable = TRUE; // Allow sort
		$this->transaction_done_by->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->transaction_done_by->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['transaction_done_by'] = &$this->transaction_done_by;

		// memo_
		$this->memo_ = new cField('voided_trans_reprt_view', 'voided_trans_reprt_view', 'x_memo_', 'memo_', '`memo_`', '`memo_`', 200, -1, FALSE, '`memo_`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->memo_->Sortable = TRUE; // Allow sort
		$this->fields['memo_'] = &$this->memo_;

		// type
		$this->type = new cField('voided_trans_reprt_view', 'voided_trans_reprt_view', 'x_type', 'type', '`type`', '`type`', 200, -1, FALSE, '`type`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->type->Sortable = TRUE; // Allow sort
		$this->fields['type'] = &$this->type;

		// amount
		$this->amount = new cField('voided_trans_reprt_view', 'voided_trans_reprt_view', 'x_amount', 'amount', '`amount`', '`amount`', 5, -1, FALSE, '`amount`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->amount->Sortable = TRUE; // Allow sort
		$this->amount->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['amount'] = &$this->amount;
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`voided_trans_reprt_view`";
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
			return "voided_trans_reprt_viewlist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// Get modal caption
	function GetModalCaption($pageName) {
		global $Language;
		if ($pageName == "voided_trans_reprt_viewview.php")
			return $Language->Phrase("View");
		elseif ($pageName == "voided_trans_reprt_viewedit.php")
			return $Language->Phrase("Edit");
		elseif ($pageName == "voided_trans_reprt_viewadd.php")
			return $Language->Phrase("Add");
		else
			return "";
	}

	// List URL
	function GetListUrl() {
		return "voided_trans_reprt_viewlist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("voided_trans_reprt_viewview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("voided_trans_reprt_viewview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "voided_trans_reprt_viewadd.php?" . $this->UrlParm($parm);
		else
			$url = "voided_trans_reprt_viewadd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("voided_trans_reprt_viewedit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("voided_trans_reprt_viewadd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("voided_trans_reprt_viewdelete.php", $this->UrlParm());
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
		$this->reference->setDbValue($rs->fields('reference'));
		$this->voided_date->setDbValue($rs->fields('voided_date'));
		$this->trans_date->setDbValue($rs->fields('trans_date'));
		$this->voided_by->setDbValue($rs->fields('voided_by'));
		$this->transaction_done_by->setDbValue($rs->fields('transaction_done_by'));
		$this->memo_->setDbValue($rs->fields('memo_'));
		$this->type->setDbValue($rs->fields('type'));
		$this->amount->setDbValue($rs->fields('amount'));
	}

	// Render list row values
	function RenderListRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

	// Common render codes
		// reference
		// voided_date
		// trans_date
		// voided_by
		// transaction_done_by
		// memo_
		// type
		// amount
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

		// reference
		$this->reference->EditAttrs["class"] = "form-control";
		$this->reference->EditCustomAttributes = "";
		$this->reference->EditValue = $this->reference->CurrentValue;
		$this->reference->PlaceHolder = ew_RemoveHtml($this->reference->FldCaption());

		// voided_date
		$this->voided_date->EditAttrs["class"] = "form-control";
		$this->voided_date->EditCustomAttributes = "";
		$this->voided_date->EditValue = ew_FormatDateTime($this->voided_date->CurrentValue, 8);
		$this->voided_date->PlaceHolder = ew_RemoveHtml($this->voided_date->FldCaption());

		// trans_date
		$this->trans_date->EditAttrs["class"] = "form-control";
		$this->trans_date->EditCustomAttributes = "";
		$this->trans_date->EditValue = ew_FormatDateTime($this->trans_date->CurrentValue, 8);
		$this->trans_date->PlaceHolder = ew_RemoveHtml($this->trans_date->FldCaption());

		// voided_by
		$this->voided_by->EditAttrs["class"] = "form-control";
		$this->voided_by->EditCustomAttributes = "";
		$this->voided_by->EditValue = $this->voided_by->CurrentValue;
		$this->voided_by->PlaceHolder = ew_RemoveHtml($this->voided_by->FldCaption());

		// transaction_done_by
		$this->transaction_done_by->EditAttrs["class"] = "form-control";
		$this->transaction_done_by->EditCustomAttributes = "";

		// memo_
		$this->memo_->EditAttrs["class"] = "form-control";
		$this->memo_->EditCustomAttributes = "";
		$this->memo_->EditValue = $this->memo_->CurrentValue;
		$this->memo_->PlaceHolder = ew_RemoveHtml($this->memo_->FldCaption());

		// type
		$this->type->EditAttrs["class"] = "form-control";
		$this->type->EditCustomAttributes = "";
		$this->type->EditValue = $this->type->CurrentValue;
		$this->type->PlaceHolder = ew_RemoveHtml($this->type->FldCaption());

		// amount
		$this->amount->EditAttrs["class"] = "form-control";
		$this->amount->EditCustomAttributes = "";
		$this->amount->EditValue = $this->amount->CurrentValue;
		$this->amount->PlaceHolder = ew_RemoveHtml($this->amount->FldCaption());
		if (strval($this->amount->EditValue) <> "" && is_numeric($this->amount->EditValue)) $this->amount->EditValue = ew_FormatNumber($this->amount->EditValue, -2, -1, -2, 0);

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
			$this->reference->Count++; // Increment count
			if (is_numeric($this->amount->CurrentValue))
				$this->amount->Total += $this->amount->CurrentValue; // Accumulate total
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {
			$this->reference->CurrentValue = $this->reference->Count;
			$this->reference->ViewValue = $this->reference->CurrentValue;
			$this->reference->ViewCustomAttributes = "";
			$this->reference->HrefValue = ""; // Clear href value
			$this->amount->CurrentValue = $this->amount->Total;
			$this->amount->ViewValue = $this->amount->CurrentValue;
			$this->amount->ViewCustomAttributes = "";
			$this->amount->HrefValue = ""; // Clear href value

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
					if ($this->reference->Exportable) $Doc->ExportCaption($this->reference);
					if ($this->voided_date->Exportable) $Doc->ExportCaption($this->voided_date);
					if ($this->trans_date->Exportable) $Doc->ExportCaption($this->trans_date);
					if ($this->voided_by->Exportable) $Doc->ExportCaption($this->voided_by);
					if ($this->transaction_done_by->Exportable) $Doc->ExportCaption($this->transaction_done_by);
					if ($this->memo_->Exportable) $Doc->ExportCaption($this->memo_);
					if ($this->type->Exportable) $Doc->ExportCaption($this->type);
					if ($this->amount->Exportable) $Doc->ExportCaption($this->amount);
				} else {
					if ($this->reference->Exportable) $Doc->ExportCaption($this->reference);
					if ($this->voided_date->Exportable) $Doc->ExportCaption($this->voided_date);
					if ($this->trans_date->Exportable) $Doc->ExportCaption($this->trans_date);
					if ($this->voided_by->Exportable) $Doc->ExportCaption($this->voided_by);
					if ($this->transaction_done_by->Exportable) $Doc->ExportCaption($this->transaction_done_by);
					if ($this->memo_->Exportable) $Doc->ExportCaption($this->memo_);
					if ($this->type->Exportable) $Doc->ExportCaption($this->type);
					if ($this->amount->Exportable) $Doc->ExportCaption($this->amount);
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
						if ($this->reference->Exportable) $Doc->ExportField($this->reference);
						if ($this->voided_date->Exportable) $Doc->ExportField($this->voided_date);
						if ($this->trans_date->Exportable) $Doc->ExportField($this->trans_date);
						if ($this->voided_by->Exportable) $Doc->ExportField($this->voided_by);
						if ($this->transaction_done_by->Exportable) $Doc->ExportField($this->transaction_done_by);
						if ($this->memo_->Exportable) $Doc->ExportField($this->memo_);
						if ($this->type->Exportable) $Doc->ExportField($this->type);
						if ($this->amount->Exportable) $Doc->ExportField($this->amount);
					} else {
						if ($this->reference->Exportable) $Doc->ExportField($this->reference);
						if ($this->voided_date->Exportable) $Doc->ExportField($this->voided_date);
						if ($this->trans_date->Exportable) $Doc->ExportField($this->trans_date);
						if ($this->voided_by->Exportable) $Doc->ExportField($this->voided_by);
						if ($this->transaction_done_by->Exportable) $Doc->ExportField($this->transaction_done_by);
						if ($this->memo_->Exportable) $Doc->ExportField($this->memo_);
						if ($this->type->Exportable) $Doc->ExportField($this->type);
						if ($this->amount->Exportable) $Doc->ExportField($this->amount);
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
				if ($this->reference->Exportable) $Doc->ExportAggregate($this->reference, 'COUNT');
				if ($this->voided_date->Exportable) $Doc->ExportAggregate($this->voided_date, '');
				if ($this->trans_date->Exportable) $Doc->ExportAggregate($this->trans_date, '');
				if ($this->voided_by->Exportable) $Doc->ExportAggregate($this->voided_by, '');
				if ($this->transaction_done_by->Exportable) $Doc->ExportAggregate($this->transaction_done_by, '');
				if ($this->memo_->Exportable) $Doc->ExportAggregate($this->memo_, '');
				if ($this->type->Exportable) $Doc->ExportAggregate($this->type, '');
				if ($this->amount->Exportable) $Doc->ExportAggregate($this->amount, 'TOTAL');
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
