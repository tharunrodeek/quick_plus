<?php

// Global variable for table object
$_0_users = NULL;

//
// Table class for 0_users
//
class c_0_users extends cTable {
	var $id;
	var $user_id;
	var $password;
	var $real_name;
	var $role_id;
	var $phone;
	var $_email;
	var $_language;
	var $date_format;
	var $date_sep;
	var $tho_sep;
	var $dec_sep;
	var $theme;
	var $page_size;
	var $prices_dec;
	var $qty_dec;
	var $rates_dec;
	var $percent_dec;
	var $show_gl;
	var $show_codes;
	var $show_hints;
	var $last_visit_date;
	var $query_size;
	var $graphic_links;
	var $pos;
	var $print_profile;
	var $rep_popup;
	var $sticky_doc_date;
	var $startup_tab;
	var $transaction_days;
	var $save_report_selections;
	var $use_date_picker;
	var $def_print_destination;
	var $def_print_orientation;
	var $inactive;
	var $is_local;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = '_0_users';
		$this->TableName = '0_users';
		$this->TableType = 'TABLE';

		// Update Table
		$this->UpdateTable = "`0_users`";
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

		// id
		$this->id = new cField('_0_users', '0_users', 'x_id', 'id', '`id`', '`id`', 2, -1, FALSE, '`id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'NO');
		$this->id->Sortable = TRUE; // Allow sort
		$this->id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['id'] = &$this->id;

		// user_id
		$this->user_id = new cField('_0_users', '0_users', 'x_user_id', 'user_id', '`user_id`', '`user_id`', 200, -1, FALSE, '`user_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->user_id->Sortable = TRUE; // Allow sort
		$this->fields['user_id'] = &$this->user_id;

		// password
		$this->password = new cField('_0_users', '0_users', 'x_password', 'password', '`password`', '`password`', 200, -1, FALSE, '`password`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->password->Sortable = TRUE; // Allow sort
		$this->fields['password'] = &$this->password;

		// real_name
		$this->real_name = new cField('_0_users', '0_users', 'x_real_name', 'real_name', '`real_name`', '`real_name`', 200, -1, FALSE, '`real_name`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->real_name->Sortable = TRUE; // Allow sort
		$this->fields['real_name'] = &$this->real_name;

		// role_id
		$this->role_id = new cField('_0_users', '0_users', 'x_role_id', 'role_id', '`role_id`', '`role_id`', 3, -1, FALSE, '`role_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->role_id->Sortable = TRUE; // Allow sort
		$this->role_id->UsePleaseSelect = TRUE; // Use PleaseSelect by default
		$this->role_id->PleaseSelectText = $Language->Phrase("PleaseSelect"); // PleaseSelect text
		$this->role_id->OptionCount = 5;
		$this->role_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['role_id'] = &$this->role_id;

		// phone
		$this->phone = new cField('_0_users', '0_users', 'x_phone', 'phone', '`phone`', '`phone`', 200, -1, FALSE, '`phone`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->phone->Sortable = TRUE; // Allow sort
		$this->fields['phone'] = &$this->phone;

		// email
		$this->_email = new cField('_0_users', '0_users', 'x__email', 'email', '`email`', '`email`', 200, -1, FALSE, '`email`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->_email->Sortable = TRUE; // Allow sort
		$this->fields['email'] = &$this->_email;

		// language
		$this->_language = new cField('_0_users', '0_users', 'x__language', 'language', '`language`', '`language`', 200, -1, FALSE, '`language`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->_language->Sortable = TRUE; // Allow sort
		$this->fields['language'] = &$this->_language;

		// date_format
		$this->date_format = new cField('_0_users', '0_users', 'x_date_format', 'date_format', '`date_format`', '`date_format`', 16, -1, FALSE, '`date_format`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->date_format->Sortable = TRUE; // Allow sort
		$this->date_format->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['date_format'] = &$this->date_format;

		// date_sep
		$this->date_sep = new cField('_0_users', '0_users', 'x_date_sep', 'date_sep', '`date_sep`', '`date_sep`', 16, -1, FALSE, '`date_sep`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->date_sep->Sortable = TRUE; // Allow sort
		$this->date_sep->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['date_sep'] = &$this->date_sep;

		// tho_sep
		$this->tho_sep = new cField('_0_users', '0_users', 'x_tho_sep', 'tho_sep', '`tho_sep`', '`tho_sep`', 16, -1, FALSE, '`tho_sep`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->tho_sep->Sortable = TRUE; // Allow sort
		$this->tho_sep->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['tho_sep'] = &$this->tho_sep;

		// dec_sep
		$this->dec_sep = new cField('_0_users', '0_users', 'x_dec_sep', 'dec_sep', '`dec_sep`', '`dec_sep`', 16, -1, FALSE, '`dec_sep`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->dec_sep->Sortable = TRUE; // Allow sort
		$this->dec_sep->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['dec_sep'] = &$this->dec_sep;

		// theme
		$this->theme = new cField('_0_users', '0_users', 'x_theme', 'theme', '`theme`', '`theme`', 200, -1, FALSE, '`theme`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->theme->Sortable = TRUE; // Allow sort
		$this->fields['theme'] = &$this->theme;

		// page_size
		$this->page_size = new cField('_0_users', '0_users', 'x_page_size', 'page_size', '`page_size`', '`page_size`', 200, -1, FALSE, '`page_size`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->page_size->Sortable = TRUE; // Allow sort
		$this->fields['page_size'] = &$this->page_size;

		// prices_dec
		$this->prices_dec = new cField('_0_users', '0_users', 'x_prices_dec', 'prices_dec', '`prices_dec`', '`prices_dec`', 2, -1, FALSE, '`prices_dec`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->prices_dec->Sortable = TRUE; // Allow sort
		$this->prices_dec->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['prices_dec'] = &$this->prices_dec;

		// qty_dec
		$this->qty_dec = new cField('_0_users', '0_users', 'x_qty_dec', 'qty_dec', '`qty_dec`', '`qty_dec`', 2, -1, FALSE, '`qty_dec`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->qty_dec->Sortable = TRUE; // Allow sort
		$this->qty_dec->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['qty_dec'] = &$this->qty_dec;

		// rates_dec
		$this->rates_dec = new cField('_0_users', '0_users', 'x_rates_dec', 'rates_dec', '`rates_dec`', '`rates_dec`', 2, -1, FALSE, '`rates_dec`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->rates_dec->Sortable = TRUE; // Allow sort
		$this->rates_dec->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['rates_dec'] = &$this->rates_dec;

		// percent_dec
		$this->percent_dec = new cField('_0_users', '0_users', 'x_percent_dec', 'percent_dec', '`percent_dec`', '`percent_dec`', 2, -1, FALSE, '`percent_dec`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->percent_dec->Sortable = TRUE; // Allow sort
		$this->percent_dec->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['percent_dec'] = &$this->percent_dec;

		// show_gl
		$this->show_gl = new cField('_0_users', '0_users', 'x_show_gl', 'show_gl', '`show_gl`', '`show_gl`', 16, -1, FALSE, '`show_gl`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->show_gl->Sortable = TRUE; // Allow sort
		$this->show_gl->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['show_gl'] = &$this->show_gl;

		// show_codes
		$this->show_codes = new cField('_0_users', '0_users', 'x_show_codes', 'show_codes', '`show_codes`', '`show_codes`', 16, -1, FALSE, '`show_codes`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->show_codes->Sortable = TRUE; // Allow sort
		$this->show_codes->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['show_codes'] = &$this->show_codes;

		// show_hints
		$this->show_hints = new cField('_0_users', '0_users', 'x_show_hints', 'show_hints', '`show_hints`', '`show_hints`', 16, -1, FALSE, '`show_hints`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->show_hints->Sortable = TRUE; // Allow sort
		$this->show_hints->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['show_hints'] = &$this->show_hints;

		// last_visit_date
		$this->last_visit_date = new cField('_0_users', '0_users', 'x_last_visit_date', 'last_visit_date', '`last_visit_date`', ew_CastDateFieldForLike('`last_visit_date`', 0, "DB"), 135, 0, FALSE, '`last_visit_date`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->last_visit_date->Sortable = TRUE; // Allow sort
		$this->last_visit_date->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['last_visit_date'] = &$this->last_visit_date;

		// query_size
		$this->query_size = new cField('_0_users', '0_users', 'x_query_size', 'query_size', '`query_size`', '`query_size`', 17, -1, FALSE, '`query_size`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->query_size->Sortable = TRUE; // Allow sort
		$this->query_size->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['query_size'] = &$this->query_size;

		// graphic_links
		$this->graphic_links = new cField('_0_users', '0_users', 'x_graphic_links', 'graphic_links', '`graphic_links`', '`graphic_links`', 16, -1, FALSE, '`graphic_links`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->graphic_links->Sortable = TRUE; // Allow sort
		$this->graphic_links->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['graphic_links'] = &$this->graphic_links;

		// pos
		$this->pos = new cField('_0_users', '0_users', 'x_pos', 'pos', '`pos`', '`pos`', 2, -1, FALSE, '`pos`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->pos->Sortable = TRUE; // Allow sort
		$this->pos->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['pos'] = &$this->pos;

		// print_profile
		$this->print_profile = new cField('_0_users', '0_users', 'x_print_profile', 'print_profile', '`print_profile`', '`print_profile`', 200, -1, FALSE, '`print_profile`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->print_profile->Sortable = TRUE; // Allow sort
		$this->fields['print_profile'] = &$this->print_profile;

		// rep_popup
		$this->rep_popup = new cField('_0_users', '0_users', 'x_rep_popup', 'rep_popup', '`rep_popup`', '`rep_popup`', 16, -1, FALSE, '`rep_popup`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->rep_popup->Sortable = TRUE; // Allow sort
		$this->rep_popup->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['rep_popup'] = &$this->rep_popup;

		// sticky_doc_date
		$this->sticky_doc_date = new cField('_0_users', '0_users', 'x_sticky_doc_date', 'sticky_doc_date', '`sticky_doc_date`', '`sticky_doc_date`', 16, -1, FALSE, '`sticky_doc_date`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->sticky_doc_date->Sortable = TRUE; // Allow sort
		$this->sticky_doc_date->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['sticky_doc_date'] = &$this->sticky_doc_date;

		// startup_tab
		$this->startup_tab = new cField('_0_users', '0_users', 'x_startup_tab', 'startup_tab', '`startup_tab`', '`startup_tab`', 200, -1, FALSE, '`startup_tab`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->startup_tab->Sortable = TRUE; // Allow sort
		$this->fields['startup_tab'] = &$this->startup_tab;

		// transaction_days
		$this->transaction_days = new cField('_0_users', '0_users', 'x_transaction_days', 'transaction_days', '`transaction_days`', '`transaction_days`', 2, -1, FALSE, '`transaction_days`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->transaction_days->Sortable = TRUE; // Allow sort
		$this->transaction_days->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['transaction_days'] = &$this->transaction_days;

		// save_report_selections
		$this->save_report_selections = new cField('_0_users', '0_users', 'x_save_report_selections', 'save_report_selections', '`save_report_selections`', '`save_report_selections`', 2, -1, FALSE, '`save_report_selections`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->save_report_selections->Sortable = TRUE; // Allow sort
		$this->save_report_selections->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['save_report_selections'] = &$this->save_report_selections;

		// use_date_picker
		$this->use_date_picker = new cField('_0_users', '0_users', 'x_use_date_picker', 'use_date_picker', '`use_date_picker`', '`use_date_picker`', 16, -1, FALSE, '`use_date_picker`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->use_date_picker->Sortable = TRUE; // Allow sort
		$this->use_date_picker->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['use_date_picker'] = &$this->use_date_picker;

		// def_print_destination
		$this->def_print_destination = new cField('_0_users', '0_users', 'x_def_print_destination', 'def_print_destination', '`def_print_destination`', '`def_print_destination`', 16, -1, FALSE, '`def_print_destination`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->def_print_destination->Sortable = TRUE; // Allow sort
		$this->def_print_destination->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['def_print_destination'] = &$this->def_print_destination;

		// def_print_orientation
		$this->def_print_orientation = new cField('_0_users', '0_users', 'x_def_print_orientation', 'def_print_orientation', '`def_print_orientation`', '`def_print_orientation`', 16, -1, FALSE, '`def_print_orientation`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->def_print_orientation->Sortable = TRUE; // Allow sort
		$this->def_print_orientation->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['def_print_orientation'] = &$this->def_print_orientation;

		// inactive
		$this->inactive = new cField('_0_users', '0_users', 'x_inactive', 'inactive', '`inactive`', '`inactive`', 16, -1, FALSE, '`inactive`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->inactive->Sortable = TRUE; // Allow sort
		$this->inactive->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['inactive'] = &$this->inactive;

		// is_local
		$this->is_local = new cField('_0_users', '0_users', 'x_is_local', 'is_local', '`is_local`', '`is_local`', 3, -1, FALSE, '`is_local`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->is_local->Sortable = TRUE; // Allow sort
		$this->is_local->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['is_local'] = &$this->is_local;
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`0_users`";
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
			if (EW_ENCRYPTED_PASSWORD && $name == 'password')
				$value = (EW_CASE_SENSITIVE_PASSWORD) ? ew_EncryptPassword($value) : ew_EncryptPassword(strtolower($value));
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

			// Get insert id if necessary
			$this->id->setDbValue($conn->Insert_ID());
			$rs['id'] = $this->id->DbValue;
		}
		return $bInsert;
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "", $curfilter = TRUE) {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]) || $this->fields[$name]->FldIsCustom)
				continue;
			if (EW_ENCRYPTED_PASSWORD && $name == 'password') {
				if ($value == $this->fields[$name]->OldValue) // No need to update hashed password if not changed
					continue;
				$value = (EW_CASE_SENSITIVE_PASSWORD) ? ew_EncryptPassword($value) : ew_EncryptPassword(strtolower($value));
			}
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
			if (array_key_exists('id', $rs))
				ew_AddFilter($where, ew_QuotedName('id', $this->DBID) . '=' . ew_QuotedValue($rs['id'], $this->id->FldDataType, $this->DBID));
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
		return "`id` = @id@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->id->CurrentValue))
			return "0=1"; // Invalid key
		if (is_null($this->id->CurrentValue))
			return "0=1"; // Invalid key
		else
			$sKeyFilter = str_replace("@id@", ew_AdjustSql($this->id->CurrentValue, $this->DBID), $sKeyFilter); // Replace key value
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
			return "_0_userslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// Get modal caption
	function GetModalCaption($pageName) {
		global $Language;
		if ($pageName == "_0_usersview.php")
			return $Language->Phrase("View");
		elseif ($pageName == "_0_usersedit.php")
			return $Language->Phrase("Edit");
		elseif ($pageName == "_0_usersadd.php")
			return $Language->Phrase("Add");
		else
			return "";
	}

	// List URL
	function GetListUrl() {
		return "_0_userslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("_0_usersview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("_0_usersview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "_0_usersadd.php?" . $this->UrlParm($parm);
		else
			$url = "_0_usersadd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("_0_usersedit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("_0_usersadd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("_0_usersdelete.php", $this->UrlParm());
	}

	// Add master url
	function AddMasterUrl($url) {
		return $url;
	}

	function KeyToJson() {
		$json = "";
		$json .= "id:" . ew_VarToJson($this->id->CurrentValue, "number", "'");
		return "{" . $json . "}";
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->id->CurrentValue)) {
			$sUrl .= "id=" . urlencode($this->id->CurrentValue);
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
			if ($isPost && isset($_POST["id"]))
				$arKeys[] = $_POST["id"];
			elseif (isset($_GET["id"]))
				$arKeys[] = $_GET["id"];
			else
				$arKeys = NULL; // Do not setup

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		if (is_array($arKeys)) {
			foreach ($arKeys as $key) {
				if (!is_numeric($key))
					continue;
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
			$this->id->CurrentValue = $key;
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
		$this->id->setDbValue($rs->fields('id'));
		$this->user_id->setDbValue($rs->fields('user_id'));
		$this->password->setDbValue($rs->fields('password'));
		$this->real_name->setDbValue($rs->fields('real_name'));
		$this->role_id->setDbValue($rs->fields('role_id'));
		$this->phone->setDbValue($rs->fields('phone'));
		$this->_email->setDbValue($rs->fields('email'));
		$this->_language->setDbValue($rs->fields('language'));
		$this->date_format->setDbValue($rs->fields('date_format'));
		$this->date_sep->setDbValue($rs->fields('date_sep'));
		$this->tho_sep->setDbValue($rs->fields('tho_sep'));
		$this->dec_sep->setDbValue($rs->fields('dec_sep'));
		$this->theme->setDbValue($rs->fields('theme'));
		$this->page_size->setDbValue($rs->fields('page_size'));
		$this->prices_dec->setDbValue($rs->fields('prices_dec'));
		$this->qty_dec->setDbValue($rs->fields('qty_dec'));
		$this->rates_dec->setDbValue($rs->fields('rates_dec'));
		$this->percent_dec->setDbValue($rs->fields('percent_dec'));
		$this->show_gl->setDbValue($rs->fields('show_gl'));
		$this->show_codes->setDbValue($rs->fields('show_codes'));
		$this->show_hints->setDbValue($rs->fields('show_hints'));
		$this->last_visit_date->setDbValue($rs->fields('last_visit_date'));
		$this->query_size->setDbValue($rs->fields('query_size'));
		$this->graphic_links->setDbValue($rs->fields('graphic_links'));
		$this->pos->setDbValue($rs->fields('pos'));
		$this->print_profile->setDbValue($rs->fields('print_profile'));
		$this->rep_popup->setDbValue($rs->fields('rep_popup'));
		$this->sticky_doc_date->setDbValue($rs->fields('sticky_doc_date'));
		$this->startup_tab->setDbValue($rs->fields('startup_tab'));
		$this->transaction_days->setDbValue($rs->fields('transaction_days'));
		$this->save_report_selections->setDbValue($rs->fields('save_report_selections'));
		$this->use_date_picker->setDbValue($rs->fields('use_date_picker'));
		$this->def_print_destination->setDbValue($rs->fields('def_print_destination'));
		$this->def_print_orientation->setDbValue($rs->fields('def_print_orientation'));
		$this->inactive->setDbValue($rs->fields('inactive'));
		$this->is_local->setDbValue($rs->fields('is_local'));
	}

	// Render list row values
	function RenderListRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

	// Common render codes
		// id
		// user_id
		// password
		// real_name
		// role_id
		// phone
		// email
		// language
		// date_format
		// date_sep
		// tho_sep
		// dec_sep
		// theme
		// page_size
		// prices_dec
		// qty_dec
		// rates_dec
		// percent_dec
		// show_gl
		// show_codes
		// show_hints
		// last_visit_date
		// query_size
		// graphic_links
		// pos
		// print_profile
		// rep_popup
		// sticky_doc_date
		// startup_tab
		// transaction_days
		// save_report_selections
		// use_date_picker
		// def_print_destination
		// def_print_orientation
		// inactive
		// is_local
		// id

		$this->id->ViewValue = $this->id->CurrentValue;
		$this->id->ViewCustomAttributes = "";

		// user_id
		$this->user_id->ViewValue = $this->user_id->CurrentValue;
		$this->user_id->ViewCustomAttributes = "";

		// password
		$this->password->ViewValue = $this->password->CurrentValue;
		$this->password->ViewCustomAttributes = "";

		// real_name
		$this->real_name->ViewValue = $this->real_name->CurrentValue;
		$this->real_name->ViewCustomAttributes = "";

		// role_id
		if (strval($this->role_id->CurrentValue) <> "") {
			$this->role_id->ViewValue = $this->role_id->OptionCaption($this->role_id->CurrentValue);
		} else {
			$this->role_id->ViewValue = NULL;
		}
		$this->role_id->ViewCustomAttributes = "";

		// phone
		$this->phone->ViewValue = $this->phone->CurrentValue;
		$this->phone->ViewCustomAttributes = "";

		// email
		$this->_email->ViewValue = $this->_email->CurrentValue;
		$this->_email->ViewCustomAttributes = "";

		// language
		$this->_language->ViewValue = $this->_language->CurrentValue;
		$this->_language->ViewCustomAttributes = "";

		// date_format
		$this->date_format->ViewValue = $this->date_format->CurrentValue;
		$this->date_format->ViewCustomAttributes = "";

		// date_sep
		$this->date_sep->ViewValue = $this->date_sep->CurrentValue;
		$this->date_sep->ViewCustomAttributes = "";

		// tho_sep
		$this->tho_sep->ViewValue = $this->tho_sep->CurrentValue;
		$this->tho_sep->ViewCustomAttributes = "";

		// dec_sep
		$this->dec_sep->ViewValue = $this->dec_sep->CurrentValue;
		$this->dec_sep->ViewCustomAttributes = "";

		// theme
		$this->theme->ViewValue = $this->theme->CurrentValue;
		$this->theme->ViewCustomAttributes = "";

		// page_size
		$this->page_size->ViewValue = $this->page_size->CurrentValue;
		$this->page_size->ViewCustomAttributes = "";

		// prices_dec
		$this->prices_dec->ViewValue = $this->prices_dec->CurrentValue;
		$this->prices_dec->ViewCustomAttributes = "";

		// qty_dec
		$this->qty_dec->ViewValue = $this->qty_dec->CurrentValue;
		$this->qty_dec->ViewCustomAttributes = "";

		// rates_dec
		$this->rates_dec->ViewValue = $this->rates_dec->CurrentValue;
		$this->rates_dec->ViewCustomAttributes = "";

		// percent_dec
		$this->percent_dec->ViewValue = $this->percent_dec->CurrentValue;
		$this->percent_dec->ViewCustomAttributes = "";

		// show_gl
		$this->show_gl->ViewValue = $this->show_gl->CurrentValue;
		$this->show_gl->ViewCustomAttributes = "";

		// show_codes
		$this->show_codes->ViewValue = $this->show_codes->CurrentValue;
		$this->show_codes->ViewCustomAttributes = "";

		// show_hints
		$this->show_hints->ViewValue = $this->show_hints->CurrentValue;
		$this->show_hints->ViewCustomAttributes = "";

		// last_visit_date
		$this->last_visit_date->ViewValue = $this->last_visit_date->CurrentValue;
		$this->last_visit_date->ViewValue = ew_FormatDateTime($this->last_visit_date->ViewValue, 0);
		$this->last_visit_date->ViewCustomAttributes = "";

		// query_size
		$this->query_size->ViewValue = $this->query_size->CurrentValue;
		$this->query_size->ViewCustomAttributes = "";

		// graphic_links
		$this->graphic_links->ViewValue = $this->graphic_links->CurrentValue;
		$this->graphic_links->ViewCustomAttributes = "";

		// pos
		$this->pos->ViewValue = $this->pos->CurrentValue;
		$this->pos->ViewCustomAttributes = "";

		// print_profile
		$this->print_profile->ViewValue = $this->print_profile->CurrentValue;
		$this->print_profile->ViewCustomAttributes = "";

		// rep_popup
		$this->rep_popup->ViewValue = $this->rep_popup->CurrentValue;
		$this->rep_popup->ViewCustomAttributes = "";

		// sticky_doc_date
		$this->sticky_doc_date->ViewValue = $this->sticky_doc_date->CurrentValue;
		$this->sticky_doc_date->ViewCustomAttributes = "";

		// startup_tab
		$this->startup_tab->ViewValue = $this->startup_tab->CurrentValue;
		$this->startup_tab->ViewCustomAttributes = "";

		// transaction_days
		$this->transaction_days->ViewValue = $this->transaction_days->CurrentValue;
		$this->transaction_days->ViewCustomAttributes = "";

		// save_report_selections
		$this->save_report_selections->ViewValue = $this->save_report_selections->CurrentValue;
		$this->save_report_selections->ViewCustomAttributes = "";

		// use_date_picker
		$this->use_date_picker->ViewValue = $this->use_date_picker->CurrentValue;
		$this->use_date_picker->ViewCustomAttributes = "";

		// def_print_destination
		$this->def_print_destination->ViewValue = $this->def_print_destination->CurrentValue;
		$this->def_print_destination->ViewCustomAttributes = "";

		// def_print_orientation
		$this->def_print_orientation->ViewValue = $this->def_print_orientation->CurrentValue;
		$this->def_print_orientation->ViewCustomAttributes = "";

		// inactive
		$this->inactive->ViewValue = $this->inactive->CurrentValue;
		$this->inactive->ViewCustomAttributes = "";

		// is_local
		$this->is_local->ViewValue = $this->is_local->CurrentValue;
		$this->is_local->ViewCustomAttributes = "";

		// id
		$this->id->LinkCustomAttributes = "";
		$this->id->HrefValue = "";
		$this->id->TooltipValue = "";

		// user_id
		$this->user_id->LinkCustomAttributes = "";
		$this->user_id->HrefValue = "";
		$this->user_id->TooltipValue = "";

		// password
		$this->password->LinkCustomAttributes = "";
		$this->password->HrefValue = "";
		$this->password->TooltipValue = "";

		// real_name
		$this->real_name->LinkCustomAttributes = "";
		$this->real_name->HrefValue = "";
		$this->real_name->TooltipValue = "";

		// role_id
		$this->role_id->LinkCustomAttributes = "";
		$this->role_id->HrefValue = "";
		$this->role_id->TooltipValue = "";

		// phone
		$this->phone->LinkCustomAttributes = "";
		$this->phone->HrefValue = "";
		$this->phone->TooltipValue = "";

		// email
		$this->_email->LinkCustomAttributes = "";
		$this->_email->HrefValue = "";
		$this->_email->TooltipValue = "";

		// language
		$this->_language->LinkCustomAttributes = "";
		$this->_language->HrefValue = "";
		$this->_language->TooltipValue = "";

		// date_format
		$this->date_format->LinkCustomAttributes = "";
		$this->date_format->HrefValue = "";
		$this->date_format->TooltipValue = "";

		// date_sep
		$this->date_sep->LinkCustomAttributes = "";
		$this->date_sep->HrefValue = "";
		$this->date_sep->TooltipValue = "";

		// tho_sep
		$this->tho_sep->LinkCustomAttributes = "";
		$this->tho_sep->HrefValue = "";
		$this->tho_sep->TooltipValue = "";

		// dec_sep
		$this->dec_sep->LinkCustomAttributes = "";
		$this->dec_sep->HrefValue = "";
		$this->dec_sep->TooltipValue = "";

		// theme
		$this->theme->LinkCustomAttributes = "";
		$this->theme->HrefValue = "";
		$this->theme->TooltipValue = "";

		// page_size
		$this->page_size->LinkCustomAttributes = "";
		$this->page_size->HrefValue = "";
		$this->page_size->TooltipValue = "";

		// prices_dec
		$this->prices_dec->LinkCustomAttributes = "";
		$this->prices_dec->HrefValue = "";
		$this->prices_dec->TooltipValue = "";

		// qty_dec
		$this->qty_dec->LinkCustomAttributes = "";
		$this->qty_dec->HrefValue = "";
		$this->qty_dec->TooltipValue = "";

		// rates_dec
		$this->rates_dec->LinkCustomAttributes = "";
		$this->rates_dec->HrefValue = "";
		$this->rates_dec->TooltipValue = "";

		// percent_dec
		$this->percent_dec->LinkCustomAttributes = "";
		$this->percent_dec->HrefValue = "";
		$this->percent_dec->TooltipValue = "";

		// show_gl
		$this->show_gl->LinkCustomAttributes = "";
		$this->show_gl->HrefValue = "";
		$this->show_gl->TooltipValue = "";

		// show_codes
		$this->show_codes->LinkCustomAttributes = "";
		$this->show_codes->HrefValue = "";
		$this->show_codes->TooltipValue = "";

		// show_hints
		$this->show_hints->LinkCustomAttributes = "";
		$this->show_hints->HrefValue = "";
		$this->show_hints->TooltipValue = "";

		// last_visit_date
		$this->last_visit_date->LinkCustomAttributes = "";
		$this->last_visit_date->HrefValue = "";
		$this->last_visit_date->TooltipValue = "";

		// query_size
		$this->query_size->LinkCustomAttributes = "";
		$this->query_size->HrefValue = "";
		$this->query_size->TooltipValue = "";

		// graphic_links
		$this->graphic_links->LinkCustomAttributes = "";
		$this->graphic_links->HrefValue = "";
		$this->graphic_links->TooltipValue = "";

		// pos
		$this->pos->LinkCustomAttributes = "";
		$this->pos->HrefValue = "";
		$this->pos->TooltipValue = "";

		// print_profile
		$this->print_profile->LinkCustomAttributes = "";
		$this->print_profile->HrefValue = "";
		$this->print_profile->TooltipValue = "";

		// rep_popup
		$this->rep_popup->LinkCustomAttributes = "";
		$this->rep_popup->HrefValue = "";
		$this->rep_popup->TooltipValue = "";

		// sticky_doc_date
		$this->sticky_doc_date->LinkCustomAttributes = "";
		$this->sticky_doc_date->HrefValue = "";
		$this->sticky_doc_date->TooltipValue = "";

		// startup_tab
		$this->startup_tab->LinkCustomAttributes = "";
		$this->startup_tab->HrefValue = "";
		$this->startup_tab->TooltipValue = "";

		// transaction_days
		$this->transaction_days->LinkCustomAttributes = "";
		$this->transaction_days->HrefValue = "";
		$this->transaction_days->TooltipValue = "";

		// save_report_selections
		$this->save_report_selections->LinkCustomAttributes = "";
		$this->save_report_selections->HrefValue = "";
		$this->save_report_selections->TooltipValue = "";

		// use_date_picker
		$this->use_date_picker->LinkCustomAttributes = "";
		$this->use_date_picker->HrefValue = "";
		$this->use_date_picker->TooltipValue = "";

		// def_print_destination
		$this->def_print_destination->LinkCustomAttributes = "";
		$this->def_print_destination->HrefValue = "";
		$this->def_print_destination->TooltipValue = "";

		// def_print_orientation
		$this->def_print_orientation->LinkCustomAttributes = "";
		$this->def_print_orientation->HrefValue = "";
		$this->def_print_orientation->TooltipValue = "";

		// inactive
		$this->inactive->LinkCustomAttributes = "";
		$this->inactive->HrefValue = "";
		$this->inactive->TooltipValue = "";

		// is_local
		$this->is_local->LinkCustomAttributes = "";
		$this->is_local->HrefValue = "";
		$this->is_local->TooltipValue = "";

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

		// id
		$this->id->EditAttrs["class"] = "form-control";
		$this->id->EditCustomAttributes = "";
		$this->id->EditValue = $this->id->CurrentValue;
		$this->id->ViewCustomAttributes = "";

		// user_id
		$this->user_id->EditAttrs["class"] = "form-control";
		$this->user_id->EditCustomAttributes = "";
		$this->user_id->EditValue = $this->user_id->CurrentValue;
		$this->user_id->PlaceHolder = ew_RemoveHtml($this->user_id->FldCaption());

		// password
		$this->password->EditAttrs["class"] = "form-control";
		$this->password->EditCustomAttributes = "";
		$this->password->EditValue = $this->password->CurrentValue;
		$this->password->PlaceHolder = ew_RemoveHtml($this->password->FldCaption());

		// real_name
		$this->real_name->EditAttrs["class"] = "form-control";
		$this->real_name->EditCustomAttributes = "";
		$this->real_name->EditValue = $this->real_name->CurrentValue;
		$this->real_name->PlaceHolder = ew_RemoveHtml($this->real_name->FldCaption());

		// role_id
		$this->role_id->EditAttrs["class"] = "form-control";
		$this->role_id->EditCustomAttributes = "";
		$this->role_id->EditValue = $this->role_id->Options(TRUE);

		// phone
		$this->phone->EditAttrs["class"] = "form-control";
		$this->phone->EditCustomAttributes = "";
		$this->phone->EditValue = $this->phone->CurrentValue;
		$this->phone->PlaceHolder = ew_RemoveHtml($this->phone->FldCaption());

		// email
		$this->_email->EditAttrs["class"] = "form-control";
		$this->_email->EditCustomAttributes = "";
		$this->_email->EditValue = $this->_email->CurrentValue;
		$this->_email->PlaceHolder = ew_RemoveHtml($this->_email->FldCaption());

		// language
		$this->_language->EditAttrs["class"] = "form-control";
		$this->_language->EditCustomAttributes = "";
		$this->_language->EditValue = $this->_language->CurrentValue;
		$this->_language->PlaceHolder = ew_RemoveHtml($this->_language->FldCaption());

		// date_format
		$this->date_format->EditAttrs["class"] = "form-control";
		$this->date_format->EditCustomAttributes = "";
		$this->date_format->EditValue = $this->date_format->CurrentValue;
		$this->date_format->PlaceHolder = ew_RemoveHtml($this->date_format->FldCaption());

		// date_sep
		$this->date_sep->EditAttrs["class"] = "form-control";
		$this->date_sep->EditCustomAttributes = "";
		$this->date_sep->EditValue = $this->date_sep->CurrentValue;
		$this->date_sep->PlaceHolder = ew_RemoveHtml($this->date_sep->FldCaption());

		// tho_sep
		$this->tho_sep->EditAttrs["class"] = "form-control";
		$this->tho_sep->EditCustomAttributes = "";
		$this->tho_sep->EditValue = $this->tho_sep->CurrentValue;
		$this->tho_sep->PlaceHolder = ew_RemoveHtml($this->tho_sep->FldCaption());

		// dec_sep
		$this->dec_sep->EditAttrs["class"] = "form-control";
		$this->dec_sep->EditCustomAttributes = "";
		$this->dec_sep->EditValue = $this->dec_sep->CurrentValue;
		$this->dec_sep->PlaceHolder = ew_RemoveHtml($this->dec_sep->FldCaption());

		// theme
		$this->theme->EditAttrs["class"] = "form-control";
		$this->theme->EditCustomAttributes = "";
		$this->theme->EditValue = $this->theme->CurrentValue;
		$this->theme->PlaceHolder = ew_RemoveHtml($this->theme->FldCaption());

		// page_size
		$this->page_size->EditAttrs["class"] = "form-control";
		$this->page_size->EditCustomAttributes = "";
		$this->page_size->EditValue = $this->page_size->CurrentValue;
		$this->page_size->PlaceHolder = ew_RemoveHtml($this->page_size->FldCaption());

		// prices_dec
		$this->prices_dec->EditAttrs["class"] = "form-control";
		$this->prices_dec->EditCustomAttributes = "";
		$this->prices_dec->EditValue = $this->prices_dec->CurrentValue;
		$this->prices_dec->PlaceHolder = ew_RemoveHtml($this->prices_dec->FldCaption());

		// qty_dec
		$this->qty_dec->EditAttrs["class"] = "form-control";
		$this->qty_dec->EditCustomAttributes = "";
		$this->qty_dec->EditValue = $this->qty_dec->CurrentValue;
		$this->qty_dec->PlaceHolder = ew_RemoveHtml($this->qty_dec->FldCaption());

		// rates_dec
		$this->rates_dec->EditAttrs["class"] = "form-control";
		$this->rates_dec->EditCustomAttributes = "";
		$this->rates_dec->EditValue = $this->rates_dec->CurrentValue;
		$this->rates_dec->PlaceHolder = ew_RemoveHtml($this->rates_dec->FldCaption());

		// percent_dec
		$this->percent_dec->EditAttrs["class"] = "form-control";
		$this->percent_dec->EditCustomAttributes = "";
		$this->percent_dec->EditValue = $this->percent_dec->CurrentValue;
		$this->percent_dec->PlaceHolder = ew_RemoveHtml($this->percent_dec->FldCaption());

		// show_gl
		$this->show_gl->EditAttrs["class"] = "form-control";
		$this->show_gl->EditCustomAttributes = "";
		$this->show_gl->EditValue = $this->show_gl->CurrentValue;
		$this->show_gl->PlaceHolder = ew_RemoveHtml($this->show_gl->FldCaption());

		// show_codes
		$this->show_codes->EditAttrs["class"] = "form-control";
		$this->show_codes->EditCustomAttributes = "";
		$this->show_codes->EditValue = $this->show_codes->CurrentValue;
		$this->show_codes->PlaceHolder = ew_RemoveHtml($this->show_codes->FldCaption());

		// show_hints
		$this->show_hints->EditAttrs["class"] = "form-control";
		$this->show_hints->EditCustomAttributes = "";
		$this->show_hints->EditValue = $this->show_hints->CurrentValue;
		$this->show_hints->PlaceHolder = ew_RemoveHtml($this->show_hints->FldCaption());

		// last_visit_date
		$this->last_visit_date->EditAttrs["class"] = "form-control";
		$this->last_visit_date->EditCustomAttributes = "";
		$this->last_visit_date->EditValue = ew_FormatDateTime($this->last_visit_date->CurrentValue, 8);
		$this->last_visit_date->PlaceHolder = ew_RemoveHtml($this->last_visit_date->FldCaption());

		// query_size
		$this->query_size->EditAttrs["class"] = "form-control";
		$this->query_size->EditCustomAttributes = "";
		$this->query_size->EditValue = $this->query_size->CurrentValue;
		$this->query_size->PlaceHolder = ew_RemoveHtml($this->query_size->FldCaption());

		// graphic_links
		$this->graphic_links->EditAttrs["class"] = "form-control";
		$this->graphic_links->EditCustomAttributes = "";
		$this->graphic_links->EditValue = $this->graphic_links->CurrentValue;
		$this->graphic_links->PlaceHolder = ew_RemoveHtml($this->graphic_links->FldCaption());

		// pos
		$this->pos->EditAttrs["class"] = "form-control";
		$this->pos->EditCustomAttributes = "";
		$this->pos->EditValue = $this->pos->CurrentValue;
		$this->pos->PlaceHolder = ew_RemoveHtml($this->pos->FldCaption());

		// print_profile
		$this->print_profile->EditAttrs["class"] = "form-control";
		$this->print_profile->EditCustomAttributes = "";
		$this->print_profile->EditValue = $this->print_profile->CurrentValue;
		$this->print_profile->PlaceHolder = ew_RemoveHtml($this->print_profile->FldCaption());

		// rep_popup
		$this->rep_popup->EditAttrs["class"] = "form-control";
		$this->rep_popup->EditCustomAttributes = "";
		$this->rep_popup->EditValue = $this->rep_popup->CurrentValue;
		$this->rep_popup->PlaceHolder = ew_RemoveHtml($this->rep_popup->FldCaption());

		// sticky_doc_date
		$this->sticky_doc_date->EditAttrs["class"] = "form-control";
		$this->sticky_doc_date->EditCustomAttributes = "";
		$this->sticky_doc_date->EditValue = $this->sticky_doc_date->CurrentValue;
		$this->sticky_doc_date->PlaceHolder = ew_RemoveHtml($this->sticky_doc_date->FldCaption());

		// startup_tab
		$this->startup_tab->EditAttrs["class"] = "form-control";
		$this->startup_tab->EditCustomAttributes = "";
		$this->startup_tab->EditValue = $this->startup_tab->CurrentValue;
		$this->startup_tab->PlaceHolder = ew_RemoveHtml($this->startup_tab->FldCaption());

		// transaction_days
		$this->transaction_days->EditAttrs["class"] = "form-control";
		$this->transaction_days->EditCustomAttributes = "";
		$this->transaction_days->EditValue = $this->transaction_days->CurrentValue;
		$this->transaction_days->PlaceHolder = ew_RemoveHtml($this->transaction_days->FldCaption());

		// save_report_selections
		$this->save_report_selections->EditAttrs["class"] = "form-control";
		$this->save_report_selections->EditCustomAttributes = "";
		$this->save_report_selections->EditValue = $this->save_report_selections->CurrentValue;
		$this->save_report_selections->PlaceHolder = ew_RemoveHtml($this->save_report_selections->FldCaption());

		// use_date_picker
		$this->use_date_picker->EditAttrs["class"] = "form-control";
		$this->use_date_picker->EditCustomAttributes = "";
		$this->use_date_picker->EditValue = $this->use_date_picker->CurrentValue;
		$this->use_date_picker->PlaceHolder = ew_RemoveHtml($this->use_date_picker->FldCaption());

		// def_print_destination
		$this->def_print_destination->EditAttrs["class"] = "form-control";
		$this->def_print_destination->EditCustomAttributes = "";
		$this->def_print_destination->EditValue = $this->def_print_destination->CurrentValue;
		$this->def_print_destination->PlaceHolder = ew_RemoveHtml($this->def_print_destination->FldCaption());

		// def_print_orientation
		$this->def_print_orientation->EditAttrs["class"] = "form-control";
		$this->def_print_orientation->EditCustomAttributes = "";
		$this->def_print_orientation->EditValue = $this->def_print_orientation->CurrentValue;
		$this->def_print_orientation->PlaceHolder = ew_RemoveHtml($this->def_print_orientation->FldCaption());

		// inactive
		$this->inactive->EditAttrs["class"] = "form-control";
		$this->inactive->EditCustomAttributes = "";
		$this->inactive->EditValue = $this->inactive->CurrentValue;
		$this->inactive->PlaceHolder = ew_RemoveHtml($this->inactive->FldCaption());

		// is_local
		$this->is_local->EditAttrs["class"] = "form-control";
		$this->is_local->EditCustomAttributes = "";
		$this->is_local->EditValue = $this->is_local->CurrentValue;
		$this->is_local->PlaceHolder = ew_RemoveHtml($this->is_local->FldCaption());

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
					if ($this->id->Exportable) $Doc->ExportCaption($this->id);
					if ($this->user_id->Exportable) $Doc->ExportCaption($this->user_id);
					if ($this->password->Exportable) $Doc->ExportCaption($this->password);
					if ($this->real_name->Exportable) $Doc->ExportCaption($this->real_name);
					if ($this->role_id->Exportable) $Doc->ExportCaption($this->role_id);
					if ($this->phone->Exportable) $Doc->ExportCaption($this->phone);
					if ($this->_email->Exportable) $Doc->ExportCaption($this->_email);
					if ($this->_language->Exportable) $Doc->ExportCaption($this->_language);
					if ($this->date_format->Exportable) $Doc->ExportCaption($this->date_format);
					if ($this->date_sep->Exportable) $Doc->ExportCaption($this->date_sep);
					if ($this->tho_sep->Exportable) $Doc->ExportCaption($this->tho_sep);
					if ($this->dec_sep->Exportable) $Doc->ExportCaption($this->dec_sep);
					if ($this->theme->Exportable) $Doc->ExportCaption($this->theme);
					if ($this->page_size->Exportable) $Doc->ExportCaption($this->page_size);
					if ($this->prices_dec->Exportable) $Doc->ExportCaption($this->prices_dec);
					if ($this->qty_dec->Exportable) $Doc->ExportCaption($this->qty_dec);
					if ($this->rates_dec->Exportable) $Doc->ExportCaption($this->rates_dec);
					if ($this->percent_dec->Exportable) $Doc->ExportCaption($this->percent_dec);
					if ($this->show_gl->Exportable) $Doc->ExportCaption($this->show_gl);
					if ($this->show_codes->Exportable) $Doc->ExportCaption($this->show_codes);
					if ($this->show_hints->Exportable) $Doc->ExportCaption($this->show_hints);
					if ($this->last_visit_date->Exportable) $Doc->ExportCaption($this->last_visit_date);
					if ($this->query_size->Exportable) $Doc->ExportCaption($this->query_size);
					if ($this->graphic_links->Exportable) $Doc->ExportCaption($this->graphic_links);
					if ($this->pos->Exportable) $Doc->ExportCaption($this->pos);
					if ($this->print_profile->Exportable) $Doc->ExportCaption($this->print_profile);
					if ($this->rep_popup->Exportable) $Doc->ExportCaption($this->rep_popup);
					if ($this->sticky_doc_date->Exportable) $Doc->ExportCaption($this->sticky_doc_date);
					if ($this->startup_tab->Exportable) $Doc->ExportCaption($this->startup_tab);
					if ($this->transaction_days->Exportable) $Doc->ExportCaption($this->transaction_days);
					if ($this->save_report_selections->Exportable) $Doc->ExportCaption($this->save_report_selections);
					if ($this->use_date_picker->Exportable) $Doc->ExportCaption($this->use_date_picker);
					if ($this->def_print_destination->Exportable) $Doc->ExportCaption($this->def_print_destination);
					if ($this->def_print_orientation->Exportable) $Doc->ExportCaption($this->def_print_orientation);
					if ($this->inactive->Exportable) $Doc->ExportCaption($this->inactive);
					if ($this->is_local->Exportable) $Doc->ExportCaption($this->is_local);
				} else {
					if ($this->id->Exportable) $Doc->ExportCaption($this->id);
					if ($this->user_id->Exportable) $Doc->ExportCaption($this->user_id);
					if ($this->password->Exportable) $Doc->ExportCaption($this->password);
					if ($this->real_name->Exportable) $Doc->ExportCaption($this->real_name);
					if ($this->role_id->Exportable) $Doc->ExportCaption($this->role_id);
					if ($this->phone->Exportable) $Doc->ExportCaption($this->phone);
					if ($this->_email->Exportable) $Doc->ExportCaption($this->_email);
					if ($this->_language->Exportable) $Doc->ExportCaption($this->_language);
					if ($this->date_format->Exportable) $Doc->ExportCaption($this->date_format);
					if ($this->date_sep->Exportable) $Doc->ExportCaption($this->date_sep);
					if ($this->tho_sep->Exportable) $Doc->ExportCaption($this->tho_sep);
					if ($this->dec_sep->Exportable) $Doc->ExportCaption($this->dec_sep);
					if ($this->theme->Exportable) $Doc->ExportCaption($this->theme);
					if ($this->page_size->Exportable) $Doc->ExportCaption($this->page_size);
					if ($this->prices_dec->Exportable) $Doc->ExportCaption($this->prices_dec);
					if ($this->qty_dec->Exportable) $Doc->ExportCaption($this->qty_dec);
					if ($this->rates_dec->Exportable) $Doc->ExportCaption($this->rates_dec);
					if ($this->percent_dec->Exportable) $Doc->ExportCaption($this->percent_dec);
					if ($this->show_gl->Exportable) $Doc->ExportCaption($this->show_gl);
					if ($this->show_codes->Exportable) $Doc->ExportCaption($this->show_codes);
					if ($this->show_hints->Exportable) $Doc->ExportCaption($this->show_hints);
					if ($this->last_visit_date->Exportable) $Doc->ExportCaption($this->last_visit_date);
					if ($this->query_size->Exportable) $Doc->ExportCaption($this->query_size);
					if ($this->graphic_links->Exportable) $Doc->ExportCaption($this->graphic_links);
					if ($this->pos->Exportable) $Doc->ExportCaption($this->pos);
					if ($this->print_profile->Exportable) $Doc->ExportCaption($this->print_profile);
					if ($this->rep_popup->Exportable) $Doc->ExportCaption($this->rep_popup);
					if ($this->sticky_doc_date->Exportable) $Doc->ExportCaption($this->sticky_doc_date);
					if ($this->startup_tab->Exportable) $Doc->ExportCaption($this->startup_tab);
					if ($this->transaction_days->Exportable) $Doc->ExportCaption($this->transaction_days);
					if ($this->save_report_selections->Exportable) $Doc->ExportCaption($this->save_report_selections);
					if ($this->use_date_picker->Exportable) $Doc->ExportCaption($this->use_date_picker);
					if ($this->def_print_destination->Exportable) $Doc->ExportCaption($this->def_print_destination);
					if ($this->def_print_orientation->Exportable) $Doc->ExportCaption($this->def_print_orientation);
					if ($this->inactive->Exportable) $Doc->ExportCaption($this->inactive);
					if ($this->is_local->Exportable) $Doc->ExportCaption($this->is_local);
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
						if ($this->id->Exportable) $Doc->ExportField($this->id);
						if ($this->user_id->Exportable) $Doc->ExportField($this->user_id);
						if ($this->password->Exportable) $Doc->ExportField($this->password);
						if ($this->real_name->Exportable) $Doc->ExportField($this->real_name);
						if ($this->role_id->Exportable) $Doc->ExportField($this->role_id);
						if ($this->phone->Exportable) $Doc->ExportField($this->phone);
						if ($this->_email->Exportable) $Doc->ExportField($this->_email);
						if ($this->_language->Exportable) $Doc->ExportField($this->_language);
						if ($this->date_format->Exportable) $Doc->ExportField($this->date_format);
						if ($this->date_sep->Exportable) $Doc->ExportField($this->date_sep);
						if ($this->tho_sep->Exportable) $Doc->ExportField($this->tho_sep);
						if ($this->dec_sep->Exportable) $Doc->ExportField($this->dec_sep);
						if ($this->theme->Exportable) $Doc->ExportField($this->theme);
						if ($this->page_size->Exportable) $Doc->ExportField($this->page_size);
						if ($this->prices_dec->Exportable) $Doc->ExportField($this->prices_dec);
						if ($this->qty_dec->Exportable) $Doc->ExportField($this->qty_dec);
						if ($this->rates_dec->Exportable) $Doc->ExportField($this->rates_dec);
						if ($this->percent_dec->Exportable) $Doc->ExportField($this->percent_dec);
						if ($this->show_gl->Exportable) $Doc->ExportField($this->show_gl);
						if ($this->show_codes->Exportable) $Doc->ExportField($this->show_codes);
						if ($this->show_hints->Exportable) $Doc->ExportField($this->show_hints);
						if ($this->last_visit_date->Exportable) $Doc->ExportField($this->last_visit_date);
						if ($this->query_size->Exportable) $Doc->ExportField($this->query_size);
						if ($this->graphic_links->Exportable) $Doc->ExportField($this->graphic_links);
						if ($this->pos->Exportable) $Doc->ExportField($this->pos);
						if ($this->print_profile->Exportable) $Doc->ExportField($this->print_profile);
						if ($this->rep_popup->Exportable) $Doc->ExportField($this->rep_popup);
						if ($this->sticky_doc_date->Exportable) $Doc->ExportField($this->sticky_doc_date);
						if ($this->startup_tab->Exportable) $Doc->ExportField($this->startup_tab);
						if ($this->transaction_days->Exportable) $Doc->ExportField($this->transaction_days);
						if ($this->save_report_selections->Exportable) $Doc->ExportField($this->save_report_selections);
						if ($this->use_date_picker->Exportable) $Doc->ExportField($this->use_date_picker);
						if ($this->def_print_destination->Exportable) $Doc->ExportField($this->def_print_destination);
						if ($this->def_print_orientation->Exportable) $Doc->ExportField($this->def_print_orientation);
						if ($this->inactive->Exportable) $Doc->ExportField($this->inactive);
						if ($this->is_local->Exportable) $Doc->ExportField($this->is_local);
					} else {
						if ($this->id->Exportable) $Doc->ExportField($this->id);
						if ($this->user_id->Exportable) $Doc->ExportField($this->user_id);
						if ($this->password->Exportable) $Doc->ExportField($this->password);
						if ($this->real_name->Exportable) $Doc->ExportField($this->real_name);
						if ($this->role_id->Exportable) $Doc->ExportField($this->role_id);
						if ($this->phone->Exportable) $Doc->ExportField($this->phone);
						if ($this->_email->Exportable) $Doc->ExportField($this->_email);
						if ($this->_language->Exportable) $Doc->ExportField($this->_language);
						if ($this->date_format->Exportable) $Doc->ExportField($this->date_format);
						if ($this->date_sep->Exportable) $Doc->ExportField($this->date_sep);
						if ($this->tho_sep->Exportable) $Doc->ExportField($this->tho_sep);
						if ($this->dec_sep->Exportable) $Doc->ExportField($this->dec_sep);
						if ($this->theme->Exportable) $Doc->ExportField($this->theme);
						if ($this->page_size->Exportable) $Doc->ExportField($this->page_size);
						if ($this->prices_dec->Exportable) $Doc->ExportField($this->prices_dec);
						if ($this->qty_dec->Exportable) $Doc->ExportField($this->qty_dec);
						if ($this->rates_dec->Exportable) $Doc->ExportField($this->rates_dec);
						if ($this->percent_dec->Exportable) $Doc->ExportField($this->percent_dec);
						if ($this->show_gl->Exportable) $Doc->ExportField($this->show_gl);
						if ($this->show_codes->Exportable) $Doc->ExportField($this->show_codes);
						if ($this->show_hints->Exportable) $Doc->ExportField($this->show_hints);
						if ($this->last_visit_date->Exportable) $Doc->ExportField($this->last_visit_date);
						if ($this->query_size->Exportable) $Doc->ExportField($this->query_size);
						if ($this->graphic_links->Exportable) $Doc->ExportField($this->graphic_links);
						if ($this->pos->Exportable) $Doc->ExportField($this->pos);
						if ($this->print_profile->Exportable) $Doc->ExportField($this->print_profile);
						if ($this->rep_popup->Exportable) $Doc->ExportField($this->rep_popup);
						if ($this->sticky_doc_date->Exportable) $Doc->ExportField($this->sticky_doc_date);
						if ($this->startup_tab->Exportable) $Doc->ExportField($this->startup_tab);
						if ($this->transaction_days->Exportable) $Doc->ExportField($this->transaction_days);
						if ($this->save_report_selections->Exportable) $Doc->ExportField($this->save_report_selections);
						if ($this->use_date_picker->Exportable) $Doc->ExportField($this->use_date_picker);
						if ($this->def_print_destination->Exportable) $Doc->ExportField($this->def_print_destination);
						if ($this->def_print_orientation->Exportable) $Doc->ExportField($this->def_print_orientation);
						if ($this->inactive->Exportable) $Doc->ExportField($this->inactive);
						if ($this->is_local->Exportable) $Doc->ExportField($this->is_local);
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
