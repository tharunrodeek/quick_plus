<?php

// Global variable for table object
$discount_report_view = NULL;

//
// Table class for discount_report_view
//
class cdiscount_report_view extends cTable {
	var $invoice_no;
	var $transaction_date;
	var $description;
	var $customer_name;
	var $reference_customer;
	var $created_employee;
	var $discount_amount;
	var $quantity;
	var $total_discount;
	var $invoice_amount;
	var $stock_id;
	var $unit_price;
	var $unit_tax;
	var $discount_percent;
	var $govt_fee;
	var $bank_service_charge;
	var $bank_service_charge_vat;
	var $pf_amount;
	var $transaction_id;
	var $user_commission;
	var $created_by;
	var $updated_by;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'discount_report_view';
		$this->TableName = 'discount_report_view';
		$this->TableType = 'VIEW';

		// Update Table
		$this->UpdateTable = "`discount_report_view`";
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

		// invoice_no
		$this->invoice_no = new cField('discount_report_view', 'discount_report_view', 'x_invoice_no', 'invoice_no', '`invoice_no`', '`invoice_no`', 200, -1, FALSE, '`invoice_no`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->invoice_no->Sortable = TRUE; // Allow sort
		$this->fields['invoice_no'] = &$this->invoice_no;

		// transaction_date
		$this->transaction_date = new cField('discount_report_view', 'discount_report_view', 'x_transaction_date', 'transaction_date', '`transaction_date`', ew_CastDateFieldForLike('`transaction_date`', 2, "DB"), 133, 2, FALSE, '`transaction_date`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->transaction_date->Sortable = TRUE; // Allow sort
		$this->transaction_date->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['transaction_date'] = &$this->transaction_date;

		// description
		$this->description = new cField('discount_report_view', 'discount_report_view', 'x_description', 'description', '`description`', '`description`', 200, -1, FALSE, '`description`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->description->Sortable = TRUE; // Allow sort
		$this->fields['description'] = &$this->description;

		// customer_name
		$this->customer_name = new cField('discount_report_view', 'discount_report_view', 'x_customer_name', 'customer_name', '`customer_name`', '`customer_name`', 200, -1, FALSE, '`customer_name`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->customer_name->Sortable = TRUE; // Allow sort
		$this->customer_name->FldSelectMultiple = TRUE; // Multiple select
		$this->fields['customer_name'] = &$this->customer_name;

		// reference_customer
		$this->reference_customer = new cField('discount_report_view', 'discount_report_view', 'x_reference_customer', 'reference_customer', '`reference_customer`', '`reference_customer`', 200, -1, FALSE, '`reference_customer`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->reference_customer->Sortable = TRUE; // Allow sort
		$this->fields['reference_customer'] = &$this->reference_customer;

		// created_employee
		$this->created_employee = new cField('discount_report_view', 'discount_report_view', 'x_created_employee', 'created_employee', '`created_employee`', '`created_employee`', 200, -1, FALSE, '`created_employee`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->created_employee->Sortable = TRUE; // Allow sort
		$this->created_employee->FldSelectMultiple = TRUE; // Multiple select
		$this->fields['created_employee'] = &$this->created_employee;

		// discount_amount
		$this->discount_amount = new cField('discount_report_view', 'discount_report_view', 'x_discount_amount', 'discount_amount', '`discount_amount`', '`discount_amount`', 5, -1, FALSE, '`discount_amount`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->discount_amount->Sortable = TRUE; // Allow sort
		$this->discount_amount->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['discount_amount'] = &$this->discount_amount;

		// quantity
		$this->quantity = new cField('discount_report_view', 'discount_report_view', 'x_quantity', 'quantity', '`quantity`', '`quantity`', 5, -1, FALSE, '`quantity`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->quantity->Sortable = TRUE; // Allow sort
		$this->quantity->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['quantity'] = &$this->quantity;

		// total_discount
		$this->total_discount = new cField('discount_report_view', 'discount_report_view', 'x_total_discount', 'total_discount', '`total_discount`', '`total_discount`', 5, -1, FALSE, '`total_discount`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->total_discount->Sortable = TRUE; // Allow sort
		$this->total_discount->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['total_discount'] = &$this->total_discount;

		// invoice_amount
		$this->invoice_amount = new cField('discount_report_view', 'discount_report_view', 'x_invoice_amount', 'invoice_amount', '`invoice_amount`', '`invoice_amount`', 5, -1, FALSE, '`invoice_amount`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->invoice_amount->Sortable = FALSE; // Allow sort
		$this->invoice_amount->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['invoice_amount'] = &$this->invoice_amount;

		// stock_id
		$this->stock_id = new cField('discount_report_view', 'discount_report_view', 'x_stock_id', 'stock_id', '`stock_id`', '`stock_id`', 200, -1, FALSE, '`stock_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->stock_id->Sortable = TRUE; // Allow sort
		$this->stock_id->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->stock_id->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->fields['stock_id'] = &$this->stock_id;

		// unit_price
		$this->unit_price = new cField('discount_report_view', 'discount_report_view', 'x_unit_price', 'unit_price', '`unit_price`', '`unit_price`', 5, -1, FALSE, '`unit_price`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->unit_price->Sortable = TRUE; // Allow sort
		$this->unit_price->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['unit_price'] = &$this->unit_price;

		// unit_tax
		$this->unit_tax = new cField('discount_report_view', 'discount_report_view', 'x_unit_tax', 'unit_tax', '`unit_tax`', '`unit_tax`', 5, -1, FALSE, '`unit_tax`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->unit_tax->Sortable = TRUE; // Allow sort
		$this->unit_tax->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['unit_tax'] = &$this->unit_tax;

		// discount_percent
		$this->discount_percent = new cField('discount_report_view', 'discount_report_view', 'x_discount_percent', 'discount_percent', '`discount_percent`', '`discount_percent`', 5, -1, FALSE, '`discount_percent`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->discount_percent->Sortable = TRUE; // Allow sort
		$this->discount_percent->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['discount_percent'] = &$this->discount_percent;

		// govt_fee
		$this->govt_fee = new cField('discount_report_view', 'discount_report_view', 'x_govt_fee', 'govt_fee', '`govt_fee`', '`govt_fee`', 5, -1, FALSE, '`govt_fee`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->govt_fee->Sortable = TRUE; // Allow sort
		$this->govt_fee->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['govt_fee'] = &$this->govt_fee;

		// bank_service_charge
		$this->bank_service_charge = new cField('discount_report_view', 'discount_report_view', 'x_bank_service_charge', 'bank_service_charge', '`bank_service_charge`', '`bank_service_charge`', 5, -1, FALSE, '`bank_service_charge`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->bank_service_charge->Sortable = TRUE; // Allow sort
		$this->bank_service_charge->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['bank_service_charge'] = &$this->bank_service_charge;

		// bank_service_charge_vat
		$this->bank_service_charge_vat = new cField('discount_report_view', 'discount_report_view', 'x_bank_service_charge_vat', 'bank_service_charge_vat', '`bank_service_charge_vat`', '`bank_service_charge_vat`', 5, -1, FALSE, '`bank_service_charge_vat`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->bank_service_charge_vat->Sortable = TRUE; // Allow sort
		$this->bank_service_charge_vat->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['bank_service_charge_vat'] = &$this->bank_service_charge_vat;

		// pf_amount
		$this->pf_amount = new cField('discount_report_view', 'discount_report_view', 'x_pf_amount', 'pf_amount', '`pf_amount`', '`pf_amount`', 5, -1, FALSE, '`pf_amount`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->pf_amount->Sortable = TRUE; // Allow sort
		$this->pf_amount->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['pf_amount'] = &$this->pf_amount;

		// transaction_id
		$this->transaction_id = new cField('discount_report_view', 'discount_report_view', 'x_transaction_id', 'transaction_id', '`transaction_id`', '`transaction_id`', 200, -1, FALSE, '`transaction_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->transaction_id->Sortable = TRUE; // Allow sort
		$this->fields['transaction_id'] = &$this->transaction_id;

		// user_commission
		$this->user_commission = new cField('discount_report_view', 'discount_report_view', 'x_user_commission', 'user_commission', '`user_commission`', '`user_commission`', 5, -1, FALSE, '`user_commission`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->user_commission->Sortable = TRUE; // Allow sort
		$this->user_commission->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['user_commission'] = &$this->user_commission;

		// created_by
		$this->created_by = new cField('discount_report_view', 'discount_report_view', 'x_created_by', 'created_by', '`created_by`', '`created_by`', 3, -1, FALSE, '`created_by`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->created_by->Sortable = TRUE; // Allow sort
		$this->created_by->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['created_by'] = &$this->created_by;

		// updated_by
		$this->updated_by = new cField('discount_report_view', 'discount_report_view', 'x_updated_by', 'updated_by', '`updated_by`', '`updated_by`', 3, -1, FALSE, '`updated_by`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->updated_by->Sortable = TRUE; // Allow sort
		$this->updated_by->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['updated_by'] = &$this->updated_by;
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`discount_report_view`";
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
			return "discount_report_viewlist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// Get modal caption
	function GetModalCaption($pageName) {
		global $Language;
		if ($pageName == "discount_report_viewview.php")
			return $Language->Phrase("View");
		elseif ($pageName == "discount_report_viewedit.php")
			return $Language->Phrase("Edit");
		elseif ($pageName == "discount_report_viewadd.php")
			return $Language->Phrase("Add");
		else
			return "";
	}

	// List URL
	function GetListUrl() {
		return "discount_report_viewlist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("discount_report_viewview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("discount_report_viewview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "discount_report_viewadd.php?" . $this->UrlParm($parm);
		else
			$url = "discount_report_viewadd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("discount_report_viewedit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("discount_report_viewadd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("discount_report_viewdelete.php", $this->UrlParm());
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
		$this->invoice_no->setDbValue($rs->fields('invoice_no'));
		$this->transaction_date->setDbValue($rs->fields('transaction_date'));
		$this->description->setDbValue($rs->fields('description'));
		$this->customer_name->setDbValue($rs->fields('customer_name'));
		$this->reference_customer->setDbValue($rs->fields('reference_customer'));
		$this->created_employee->setDbValue($rs->fields('created_employee'));
		$this->discount_amount->setDbValue($rs->fields('discount_amount'));
		$this->quantity->setDbValue($rs->fields('quantity'));
		$this->total_discount->setDbValue($rs->fields('total_discount'));
		$this->invoice_amount->setDbValue($rs->fields('invoice_amount'));
		$this->stock_id->setDbValue($rs->fields('stock_id'));
		$this->unit_price->setDbValue($rs->fields('unit_price'));
		$this->unit_tax->setDbValue($rs->fields('unit_tax'));
		$this->discount_percent->setDbValue($rs->fields('discount_percent'));
		$this->govt_fee->setDbValue($rs->fields('govt_fee'));
		$this->bank_service_charge->setDbValue($rs->fields('bank_service_charge'));
		$this->bank_service_charge_vat->setDbValue($rs->fields('bank_service_charge_vat'));
		$this->pf_amount->setDbValue($rs->fields('pf_amount'));
		$this->transaction_id->setDbValue($rs->fields('transaction_id'));
		$this->user_commission->setDbValue($rs->fields('user_commission'));
		$this->created_by->setDbValue($rs->fields('created_by'));
		$this->updated_by->setDbValue($rs->fields('updated_by'));
	}

	// Render list row values
	function RenderListRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

	// Common render codes
		// invoice_no
		// transaction_date
		// description
		// customer_name
		// reference_customer
		// created_employee
		// discount_amount
		// quantity
		// total_discount
		// invoice_amount
		// stock_id
		// unit_price
		// unit_tax
		// discount_percent
		// govt_fee
		// bank_service_charge
		// bank_service_charge_vat
		// pf_amount
		// transaction_id
		// user_commission
		// created_by
		// updated_by
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

		// discount_amount
		$this->discount_amount->ViewValue = $this->discount_amount->CurrentValue;
		$this->discount_amount->ViewCustomAttributes = "";

		// quantity
		$this->quantity->ViewValue = $this->quantity->CurrentValue;
		$this->quantity->ViewCustomAttributes = "";

		// total_discount
		$this->total_discount->ViewValue = $this->total_discount->CurrentValue;
		$this->total_discount->ViewCustomAttributes = "";

		// invoice_amount
		$this->invoice_amount->ViewValue = $this->invoice_amount->CurrentValue;
		$this->invoice_amount->ViewCustomAttributes = "";

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

		// unit_price
		$this->unit_price->ViewValue = $this->unit_price->CurrentValue;
		$this->unit_price->ViewCustomAttributes = "";

		// unit_tax
		$this->unit_tax->ViewValue = $this->unit_tax->CurrentValue;
		$this->unit_tax->ViewCustomAttributes = "";

		// discount_percent
		$this->discount_percent->ViewValue = $this->discount_percent->CurrentValue;
		$this->discount_percent->ViewCustomAttributes = "";

		// govt_fee
		$this->govt_fee->ViewValue = $this->govt_fee->CurrentValue;
		$this->govt_fee->ViewCustomAttributes = "";

		// bank_service_charge
		$this->bank_service_charge->ViewValue = $this->bank_service_charge->CurrentValue;
		$this->bank_service_charge->ViewCustomAttributes = "";

		// bank_service_charge_vat
		$this->bank_service_charge_vat->ViewValue = $this->bank_service_charge_vat->CurrentValue;
		$this->bank_service_charge_vat->ViewCustomAttributes = "";

		// pf_amount
		$this->pf_amount->ViewValue = $this->pf_amount->CurrentValue;
		$this->pf_amount->ViewCustomAttributes = "";

		// transaction_id
		$this->transaction_id->ViewValue = $this->transaction_id->CurrentValue;
		$this->transaction_id->ViewCustomAttributes = "";

		// user_commission
		$this->user_commission->ViewValue = $this->user_commission->CurrentValue;
		$this->user_commission->ViewCustomAttributes = "";

		// created_by
		$this->created_by->ViewValue = $this->created_by->CurrentValue;
		$this->created_by->ViewCustomAttributes = "";

		// updated_by
		$this->updated_by->ViewValue = $this->updated_by->CurrentValue;
		$this->updated_by->ViewCustomAttributes = "";

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

		// discount_amount
		$this->discount_amount->LinkCustomAttributes = "";
		$this->discount_amount->HrefValue = "";
		$this->discount_amount->TooltipValue = "";

		// quantity
		$this->quantity->LinkCustomAttributes = "";
		$this->quantity->HrefValue = "";
		$this->quantity->TooltipValue = "";

		// total_discount
		$this->total_discount->LinkCustomAttributes = "";
		$this->total_discount->HrefValue = "";
		$this->total_discount->TooltipValue = "";

		// invoice_amount
		$this->invoice_amount->LinkCustomAttributes = "";
		$this->invoice_amount->HrefValue = "";
		$this->invoice_amount->TooltipValue = "";

		// stock_id
		$this->stock_id->LinkCustomAttributes = "";
		$this->stock_id->HrefValue = "";
		$this->stock_id->TooltipValue = "";

		// unit_price
		$this->unit_price->LinkCustomAttributes = "";
		$this->unit_price->HrefValue = "";
		$this->unit_price->TooltipValue = "";

		// unit_tax
		$this->unit_tax->LinkCustomAttributes = "";
		$this->unit_tax->HrefValue = "";
		$this->unit_tax->TooltipValue = "";

		// discount_percent
		$this->discount_percent->LinkCustomAttributes = "";
		$this->discount_percent->HrefValue = "";
		$this->discount_percent->TooltipValue = "";

		// govt_fee
		$this->govt_fee->LinkCustomAttributes = "";
		$this->govt_fee->HrefValue = "";
		$this->govt_fee->TooltipValue = "";

		// bank_service_charge
		$this->bank_service_charge->LinkCustomAttributes = "";
		$this->bank_service_charge->HrefValue = "";
		$this->bank_service_charge->TooltipValue = "";

		// bank_service_charge_vat
		$this->bank_service_charge_vat->LinkCustomAttributes = "";
		$this->bank_service_charge_vat->HrefValue = "";
		$this->bank_service_charge_vat->TooltipValue = "";

		// pf_amount
		$this->pf_amount->LinkCustomAttributes = "";
		$this->pf_amount->HrefValue = "";
		$this->pf_amount->TooltipValue = "";

		// transaction_id
		$this->transaction_id->LinkCustomAttributes = "";
		$this->transaction_id->HrefValue = "";
		$this->transaction_id->TooltipValue = "";

		// user_commission
		$this->user_commission->LinkCustomAttributes = "";
		$this->user_commission->HrefValue = "";
		$this->user_commission->TooltipValue = "";

		// created_by
		$this->created_by->LinkCustomAttributes = "";
		$this->created_by->HrefValue = "";
		$this->created_by->TooltipValue = "";

		// updated_by
		$this->updated_by->LinkCustomAttributes = "";
		$this->updated_by->HrefValue = "";
		$this->updated_by->TooltipValue = "";

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

		// invoice_no
		$this->invoice_no->EditAttrs["class"] = "form-control";
		$this->invoice_no->EditCustomAttributes = "";
		$this->invoice_no->EditValue = $this->invoice_no->CurrentValue;
		$this->invoice_no->PlaceHolder = ew_RemoveHtml($this->invoice_no->FldCaption());

		// transaction_date
		$this->transaction_date->EditAttrs["class"] = "form-control";
		$this->transaction_date->EditCustomAttributes = "";
		$this->transaction_date->EditValue = ew_FormatDateTime($this->transaction_date->CurrentValue, 2);
		$this->transaction_date->PlaceHolder = ew_RemoveHtml($this->transaction_date->FldCaption());

		// description
		$this->description->EditAttrs["class"] = "form-control";
		$this->description->EditCustomAttributes = "";
		$this->description->EditValue = $this->description->CurrentValue;
		$this->description->PlaceHolder = ew_RemoveHtml($this->description->FldCaption());

		// customer_name
		$this->customer_name->EditAttrs["class"] = "form-control";
		$this->customer_name->EditCustomAttributes = "";

		// reference_customer
		$this->reference_customer->EditAttrs["class"] = "form-control";
		$this->reference_customer->EditCustomAttributes = "";
		$this->reference_customer->EditValue = $this->reference_customer->CurrentValue;
		$this->reference_customer->PlaceHolder = ew_RemoveHtml($this->reference_customer->FldCaption());

		// created_employee
		$this->created_employee->EditAttrs["class"] = "form-control";
		$this->created_employee->EditCustomAttributes = "";

		// discount_amount
		$this->discount_amount->EditAttrs["class"] = "form-control";
		$this->discount_amount->EditCustomAttributes = "";
		$this->discount_amount->EditValue = $this->discount_amount->CurrentValue;
		$this->discount_amount->PlaceHolder = ew_RemoveHtml($this->discount_amount->FldCaption());
		if (strval($this->discount_amount->EditValue) <> "" && is_numeric($this->discount_amount->EditValue)) $this->discount_amount->EditValue = ew_FormatNumber($this->discount_amount->EditValue, -2, -1, -2, 0);

		// quantity
		$this->quantity->EditAttrs["class"] = "form-control";
		$this->quantity->EditCustomAttributes = "";
		$this->quantity->EditValue = $this->quantity->CurrentValue;
		$this->quantity->PlaceHolder = ew_RemoveHtml($this->quantity->FldCaption());
		if (strval($this->quantity->EditValue) <> "" && is_numeric($this->quantity->EditValue)) $this->quantity->EditValue = ew_FormatNumber($this->quantity->EditValue, -2, -1, -2, 0);

		// total_discount
		$this->total_discount->EditAttrs["class"] = "form-control";
		$this->total_discount->EditCustomAttributes = "";
		$this->total_discount->EditValue = $this->total_discount->CurrentValue;
		$this->total_discount->PlaceHolder = ew_RemoveHtml($this->total_discount->FldCaption());
		if (strval($this->total_discount->EditValue) <> "" && is_numeric($this->total_discount->EditValue)) $this->total_discount->EditValue = ew_FormatNumber($this->total_discount->EditValue, -2, -1, -2, 0);

		// invoice_amount
		$this->invoice_amount->EditAttrs["class"] = "form-control";
		$this->invoice_amount->EditCustomAttributes = "";
		$this->invoice_amount->EditValue = $this->invoice_amount->CurrentValue;
		$this->invoice_amount->PlaceHolder = ew_RemoveHtml($this->invoice_amount->FldCaption());
		if (strval($this->invoice_amount->EditValue) <> "" && is_numeric($this->invoice_amount->EditValue)) $this->invoice_amount->EditValue = ew_FormatNumber($this->invoice_amount->EditValue, -2, -1, -2, 0);

		// stock_id
		$this->stock_id->EditAttrs["class"] = "form-control";
		$this->stock_id->EditCustomAttributes = "";

		// unit_price
		$this->unit_price->EditAttrs["class"] = "form-control";
		$this->unit_price->EditCustomAttributes = "";
		$this->unit_price->EditValue = $this->unit_price->CurrentValue;
		$this->unit_price->PlaceHolder = ew_RemoveHtml($this->unit_price->FldCaption());
		if (strval($this->unit_price->EditValue) <> "" && is_numeric($this->unit_price->EditValue)) $this->unit_price->EditValue = ew_FormatNumber($this->unit_price->EditValue, -2, -1, -2, 0);

		// unit_tax
		$this->unit_tax->EditAttrs["class"] = "form-control";
		$this->unit_tax->EditCustomAttributes = "";
		$this->unit_tax->EditValue = $this->unit_tax->CurrentValue;
		$this->unit_tax->PlaceHolder = ew_RemoveHtml($this->unit_tax->FldCaption());
		if (strval($this->unit_tax->EditValue) <> "" && is_numeric($this->unit_tax->EditValue)) $this->unit_tax->EditValue = ew_FormatNumber($this->unit_tax->EditValue, -2, -1, -2, 0);

		// discount_percent
		$this->discount_percent->EditAttrs["class"] = "form-control";
		$this->discount_percent->EditCustomAttributes = "";
		$this->discount_percent->EditValue = $this->discount_percent->CurrentValue;
		$this->discount_percent->PlaceHolder = ew_RemoveHtml($this->discount_percent->FldCaption());
		if (strval($this->discount_percent->EditValue) <> "" && is_numeric($this->discount_percent->EditValue)) $this->discount_percent->EditValue = ew_FormatNumber($this->discount_percent->EditValue, -2, -1, -2, 0);

		// govt_fee
		$this->govt_fee->EditAttrs["class"] = "form-control";
		$this->govt_fee->EditCustomAttributes = "";
		$this->govt_fee->EditValue = $this->govt_fee->CurrentValue;
		$this->govt_fee->PlaceHolder = ew_RemoveHtml($this->govt_fee->FldCaption());
		if (strval($this->govt_fee->EditValue) <> "" && is_numeric($this->govt_fee->EditValue)) $this->govt_fee->EditValue = ew_FormatNumber($this->govt_fee->EditValue, -2, -1, -2, 0);

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

		// pf_amount
		$this->pf_amount->EditAttrs["class"] = "form-control";
		$this->pf_amount->EditCustomAttributes = "";
		$this->pf_amount->EditValue = $this->pf_amount->CurrentValue;
		$this->pf_amount->PlaceHolder = ew_RemoveHtml($this->pf_amount->FldCaption());
		if (strval($this->pf_amount->EditValue) <> "" && is_numeric($this->pf_amount->EditValue)) $this->pf_amount->EditValue = ew_FormatNumber($this->pf_amount->EditValue, -2, -1, -2, 0);

		// transaction_id
		$this->transaction_id->EditAttrs["class"] = "form-control";
		$this->transaction_id->EditCustomAttributes = "";
		$this->transaction_id->EditValue = $this->transaction_id->CurrentValue;
		$this->transaction_id->PlaceHolder = ew_RemoveHtml($this->transaction_id->FldCaption());

		// user_commission
		$this->user_commission->EditAttrs["class"] = "form-control";
		$this->user_commission->EditCustomAttributes = "";
		$this->user_commission->EditValue = $this->user_commission->CurrentValue;
		$this->user_commission->PlaceHolder = ew_RemoveHtml($this->user_commission->FldCaption());
		if (strval($this->user_commission->EditValue) <> "" && is_numeric($this->user_commission->EditValue)) $this->user_commission->EditValue = ew_FormatNumber($this->user_commission->EditValue, -2, -1, -2, 0);

		// created_by
		$this->created_by->EditAttrs["class"] = "form-control";
		$this->created_by->EditCustomAttributes = "";
		$this->created_by->EditValue = $this->created_by->CurrentValue;
		$this->created_by->PlaceHolder = ew_RemoveHtml($this->created_by->FldCaption());

		// updated_by
		$this->updated_by->EditAttrs["class"] = "form-control";
		$this->updated_by->EditCustomAttributes = "";
		$this->updated_by->EditValue = $this->updated_by->CurrentValue;
		$this->updated_by->PlaceHolder = ew_RemoveHtml($this->updated_by->FldCaption());

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
			if (is_numeric($this->total_discount->CurrentValue))
				$this->total_discount->Total += $this->total_discount->CurrentValue; // Accumulate total
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {
			$this->total_discount->CurrentValue = $this->total_discount->Total;
			$this->total_discount->ViewValue = $this->total_discount->CurrentValue;
			$this->total_discount->ViewCustomAttributes = "";
			$this->total_discount->HrefValue = ""; // Clear href value

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
					if ($this->invoice_no->Exportable) $Doc->ExportCaption($this->invoice_no);
					if ($this->transaction_date->Exportable) $Doc->ExportCaption($this->transaction_date);
					if ($this->description->Exportable) $Doc->ExportCaption($this->description);
					if ($this->customer_name->Exportable) $Doc->ExportCaption($this->customer_name);
					if ($this->reference_customer->Exportable) $Doc->ExportCaption($this->reference_customer);
					if ($this->created_employee->Exportable) $Doc->ExportCaption($this->created_employee);
					if ($this->discount_amount->Exportable) $Doc->ExportCaption($this->discount_amount);
					if ($this->quantity->Exportable) $Doc->ExportCaption($this->quantity);
					if ($this->total_discount->Exportable) $Doc->ExportCaption($this->total_discount);
					if ($this->invoice_amount->Exportable) $Doc->ExportCaption($this->invoice_amount);
					if ($this->stock_id->Exportable) $Doc->ExportCaption($this->stock_id);
					if ($this->unit_price->Exportable) $Doc->ExportCaption($this->unit_price);
					if ($this->unit_tax->Exportable) $Doc->ExportCaption($this->unit_tax);
					if ($this->discount_percent->Exportable) $Doc->ExportCaption($this->discount_percent);
					if ($this->govt_fee->Exportable) $Doc->ExportCaption($this->govt_fee);
					if ($this->bank_service_charge->Exportable) $Doc->ExportCaption($this->bank_service_charge);
					if ($this->bank_service_charge_vat->Exportable) $Doc->ExportCaption($this->bank_service_charge_vat);
					if ($this->pf_amount->Exportable) $Doc->ExportCaption($this->pf_amount);
					if ($this->transaction_id->Exportable) $Doc->ExportCaption($this->transaction_id);
					if ($this->user_commission->Exportable) $Doc->ExportCaption($this->user_commission);
					if ($this->created_by->Exportable) $Doc->ExportCaption($this->created_by);
					if ($this->updated_by->Exportable) $Doc->ExportCaption($this->updated_by);
				} else {
					if ($this->invoice_no->Exportable) $Doc->ExportCaption($this->invoice_no);
					if ($this->transaction_date->Exportable) $Doc->ExportCaption($this->transaction_date);
					if ($this->description->Exportable) $Doc->ExportCaption($this->description);
					if ($this->customer_name->Exportable) $Doc->ExportCaption($this->customer_name);
					if ($this->reference_customer->Exportable) $Doc->ExportCaption($this->reference_customer);
					if ($this->created_employee->Exportable) $Doc->ExportCaption($this->created_employee);
					if ($this->discount_amount->Exportable) $Doc->ExportCaption($this->discount_amount);
					if ($this->quantity->Exportable) $Doc->ExportCaption($this->quantity);
					if ($this->total_discount->Exportable) $Doc->ExportCaption($this->total_discount);
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
						if ($this->invoice_no->Exportable) $Doc->ExportField($this->invoice_no);
						if ($this->transaction_date->Exportable) $Doc->ExportField($this->transaction_date);
						if ($this->description->Exportable) $Doc->ExportField($this->description);
						if ($this->customer_name->Exportable) $Doc->ExportField($this->customer_name);
						if ($this->reference_customer->Exportable) $Doc->ExportField($this->reference_customer);
						if ($this->created_employee->Exportable) $Doc->ExportField($this->created_employee);
						if ($this->discount_amount->Exportable) $Doc->ExportField($this->discount_amount);
						if ($this->quantity->Exportable) $Doc->ExportField($this->quantity);
						if ($this->total_discount->Exportable) $Doc->ExportField($this->total_discount);
						if ($this->invoice_amount->Exportable) $Doc->ExportField($this->invoice_amount);
						if ($this->stock_id->Exportable) $Doc->ExportField($this->stock_id);
						if ($this->unit_price->Exportable) $Doc->ExportField($this->unit_price);
						if ($this->unit_tax->Exportable) $Doc->ExportField($this->unit_tax);
						if ($this->discount_percent->Exportable) $Doc->ExportField($this->discount_percent);
						if ($this->govt_fee->Exportable) $Doc->ExportField($this->govt_fee);
						if ($this->bank_service_charge->Exportable) $Doc->ExportField($this->bank_service_charge);
						if ($this->bank_service_charge_vat->Exportable) $Doc->ExportField($this->bank_service_charge_vat);
						if ($this->pf_amount->Exportable) $Doc->ExportField($this->pf_amount);
						if ($this->transaction_id->Exportable) $Doc->ExportField($this->transaction_id);
						if ($this->user_commission->Exportable) $Doc->ExportField($this->user_commission);
						if ($this->created_by->Exportable) $Doc->ExportField($this->created_by);
						if ($this->updated_by->Exportable) $Doc->ExportField($this->updated_by);
					} else {
						if ($this->invoice_no->Exportable) $Doc->ExportField($this->invoice_no);
						if ($this->transaction_date->Exportable) $Doc->ExportField($this->transaction_date);
						if ($this->description->Exportable) $Doc->ExportField($this->description);
						if ($this->customer_name->Exportable) $Doc->ExportField($this->customer_name);
						if ($this->reference_customer->Exportable) $Doc->ExportField($this->reference_customer);
						if ($this->created_employee->Exportable) $Doc->ExportField($this->created_employee);
						if ($this->discount_amount->Exportable) $Doc->ExportField($this->discount_amount);
						if ($this->quantity->Exportable) $Doc->ExportField($this->quantity);
						if ($this->total_discount->Exportable) $Doc->ExportField($this->total_discount);
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
				if ($this->invoice_no->Exportable) $Doc->ExportAggregate($this->invoice_no, '');
				if ($this->transaction_date->Exportable) $Doc->ExportAggregate($this->transaction_date, '');
				if ($this->description->Exportable) $Doc->ExportAggregate($this->description, '');
				if ($this->customer_name->Exportable) $Doc->ExportAggregate($this->customer_name, '');
				if ($this->reference_customer->Exportable) $Doc->ExportAggregate($this->reference_customer, '');
				if ($this->created_employee->Exportable) $Doc->ExportAggregate($this->created_employee, '');
				if ($this->discount_amount->Exportable) $Doc->ExportAggregate($this->discount_amount, '');
				if ($this->quantity->Exportable) $Doc->ExportAggregate($this->quantity, '');
				if ($this->total_discount->Exportable) $Doc->ExportAggregate($this->total_discount, 'TOTAL');
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
