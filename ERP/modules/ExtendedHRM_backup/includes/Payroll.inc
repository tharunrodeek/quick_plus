<?php
/****************************************
/*  Author  : Kvvaradha
/*  Module  : Extended HRM
/*  E-mail  : admin@kvcodes.com
/*  Version : 1.0
/*  Http    : www.kvcodes.com
*****************************************/

// Necessary File hook
include_once($path_to_root . "/modules/ExtendedHRM/includes/kvcodes.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_db.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_loan_db.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_license_db.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/kv_empl_license_category.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/kv_empl_license_type.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_sal_db.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/employee.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/common.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/empl_degree.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/empl_experience.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/empl_license.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/empl_training.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/empl_skill.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/attendance.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_attendance_db.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/allowances.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/taxes.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");


$code = user_language();

$lang = array_search_value($code, $installed_languages, 'code');
$GetText->set_language($lang['code'], strtoupper($lang['encoding']));

if (file_exists($path_to_root.'/modules/ExtendedHRM/lang/'.$lang['code'].'/LC_MESSAGES/'.$lang['code'].'.po'))
    $GetText->add_domain($lang['code'], $path_to_root . "/modules/ExtendedHRM/lang");
else
    $GetText->add_domain($lang['code'], $path_to_root . "/lang", @$lang['version']);

function repay_button_cell($name, $value, $title=false)
{
	button_cell($name, $value, $title, ICON_MONEY);
}

function kv_simple_page_mode($numeric_id = true)
{
    global $Ajax, $Mode, $selected_id;

    $default = $numeric_id ? -1 : '';
    $selected_id = get_post('selected_id', $default);
    foreach (array('ADD_ITEM', 'UPDATE_ITEM', 'RESET', 'CLONE') as $m) {
        if (isset($_POST[$m])) {
            $Ajax->activate('_page_body');
            if ($m == 'RESET'  || $m == 'CLONE') 
                $selected_id = $default;
            unset($_POST['_focus']);
            $Mode = $m; return;
        }
    }
    foreach (array('Edit', 'Delete', 'Pay') as $m) {
        foreach ($_POST as $p => $pvar) {
            if (strpos($p, $m) === 0) {
//              $selected_id = strtr(substr($p, strlen($m)), array('%2E'=>'.'));
                unset($_POST['_focus']); // focus on first form entry
                $selected_id = quoted_printable_decode(substr($p, strlen($m)));
                $Ajax->activate('_page_body');
                $Mode = $m;
                return;
            }
        }
    }
    $Mode = '';
}
// PHP Execute Maths Expressions
function calculate_string( $mathString )    {
    $mathString = trim($mathString);     // trim white spaces
    $mathString = preg_replace ('[^0-9\+-\*\/\(\) ]', '', $mathString);    // remove any non-numbers chars; exception for math operators

    $compute = create_function("", "return (" . $mathString . ");" );
    return 0 + $compute();
}

if(!function_exists('html_specials_encode')){
    function html_specials_encode($str)  {
        return htmlspecialchars($str, ENT_QUOTES, $_SESSION['language']->encoding=='iso-8859-2' ?
             'ISO-8859-1' : $_SESSION['language']->encoding);
    }
}

//----------------------------------------------------------------------------------------
function kv_hrm_get_empl_list(){
    return 'SELECT job.empl_id, CONCAT(info.empl_firstname, " ", info.empl_lastname) AS empl_name, info.email, info.mobile_phone, dpt.description, job.desig,info.addr_line1,  job.joining , info.status FROM '.TB_PREF.'kv_empl_info info JOIN '.TB_PREF.'kv_empl_job job ON  job.empl_id = info.empl_id JOIN '.TB_PREF.'kv_empl_departments dpt ON job.department= dpt.id WHERE 1';
}

//----------------------------------------------------------------------------------------
function kv_get_employees_list_based_on_dept($dept_id, $rep=false) {

    if($dept_id == 0){
        $sql = "SELECT info.*, job.*, CONCAT(info.empl_firstname, ' ', info.empl_lastname) AS empl_name, job.joining, job.al, job.sl, job.slh, job.ml, job.hl, info.status, info.date_of_status_change FROM ".TB_PREF."kv_empl_job job JOIN ".TB_PREF."kv_empl_info info ON info.id = job.empl_id";
       // echo $sql;
        return db_query($sql, "The employee table is inaccessible");

    } else{
        if($rep == true){
            $sql="SELECT info.*, job.*, CONCAT(info.empl_firstname, ' ', info.empl_lastname) AS empl_name, job.joining, job.al, job.sl, job.slh, job.ml, job.hl,info.status, info.date_of_status_change FROM ".TB_PREF."kv_empl_job job JOIN ".TB_PREF."kv_empl_info info ON info.id = job.empl_id WHERE job.department=".db_escape($dept_id);
        }else{  
            $sql="SELECT info.*, job.*,info.empl_firstname, job.empl_id, CONCAT(info.empl_firstname, ' ', info.empl_lastname) AS empl_name, job.al, job.sl, job.slh, job.ml, job.hl, job.joining, info.status, info.date_of_status_change FROM ".TB_PREF."kv_empl_job job JOIN ".TB_PREF."kv_empl_info info ON info.id = job.empl_id WHERE job.department=".db_escape($dept_id);
        }
		$sql.= " ORDER BY info.empl_id" ;

        //echo $sql;
        return db_query($sql, "could not get the selected Employees");
    }
}

//----------------------------------------------------------------------------------------
function kv_get_employees_list_based_on_dept_rep($dept_id) {

    if($dept_id == 0){
        $sql = "SELECT empl_id, empl_firstname FROM ".TB_PREF."kv_empl_info ORDER BY empl_id";
  
        return db_query($sql, "The employee table is inaccessible");

    } else{	
	$sql="SELECT info.*, job.* FROM ".TB_PREF."kv_empl_job job JOIN ".TB_PREF."kv_empl_info info ON info.empl_id = job.empl_id WHERE job.department=".db_escape($dept_id)." ORDER BY info.empl_id" ;

        return db_query($sql, "could not get the selected Employees");
    }
}

//---------------------------------------------------------------------------------------
function kv_get_months_in_fiscal_year(){   
    $Year_id = get_post('year');
	if($Year_id){
		$fiscal_year_get = get_fiscalyear($Year_id);
	} else {
        $Year_id = get_post('PARAM_0');
        if($Year_id)
            $fiscal_year_get = get_fiscalyear($Year_id);
        else
		    $fiscal_year_get = get_current_fiscalyear();
	}
    $array_months = array();
    $start    = (new DateTime($fiscal_year_get['begin']))->modify('first day of this month');
    $end      = (new DateTime($fiscal_year_get['end']))->modify('first day of next month');
    $interval = DateInterval::createFromDateString('1 month');
    $period   = new DatePeriod($start, $interval, $end);

    foreach ($period as $dt) {
        $array_months[$dt->format("m")] = date("m - F", strtotime($dt->format("Y")."-".$dt->format("m")."-01"));
    }
    return $array_months;
}


//---------------------------------------------------------------------------------------
function kv_get_months_with_years_in_fiscal_year($Year_id, $fiscal_year_get=false){   
   
    if(!$fiscal_year_get)
        $fiscal_year_get = get_fiscalyear($Year_id);
 
    $array_months = array();
    $start    = (new DateTime($fiscal_year_get['begin']))->modify('first day of this month');
    $end      = (new DateTime($fiscal_year_get['end']))->modify('first day of next month');
    $interval = DateInterval::createFromDateString('1 month');
    $period   = new DatePeriod($start, $interval, $end);

    foreach ($period as $dt) {
        $array_months[(int)$dt->format("m")] = date("Y-m-d", strtotime($dt->format("Y")."-".$dt->format("m")."-01"));
    }
    return $array_months;
}

//---------------------------------------------------------------------------------------
function  kv_current_fiscal_months_list_row($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false,$negative_mnth=0) {  
    
    echo "<tr><td class='label'>$label</td>";
    kv_current_fiscal_months_list_cell(null, $name, $selected_id, $submit_on_change, $disabled,$negative_mnth);
    echo "</tr>\n";
}
//---------------------------------------------------------------------------------------
function  kv_current_fiscal_months_list_cell($label=null, $name, $selected_id=null, $submit_on_change=false, $disabled=false, $negative_mnth=0) {  
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>". kv_current_fiscal_months($name, $selected_id, $submit_on_change, $disabled, $negative_mnth)."</td>\n";
}
function kv_current_fiscal_months($name, $selected_id=null, $submit_on_change=false, $disabled=false,$negative_mnth=0) {
   
   $Year_id = get_post('year');
   $month_id = (int)get_post($name); 
  
   if(!empty($month_id) && $month_id>0 ){} else { $selected_id = str_pad((date("m")-$negative_mnth), 2, '0', STR_PAD_LEFT);}
    $hrm_months_list = kv_get_months_in_fiscal_year();
    $options = array(
        'select_submit'=> $submit_on_change,
        'disabled' => $disabled
    );
    return  array_selector($name, $selected_id, $hrm_months_list, $options);
}

function kv_month_name_by_id($month){
    $month= abs($month);
    $months =  array(
                1 => trans('January'),
                2 => trans('February'),
                3 => trans('March'),
                4 => trans('April'),
                5 => trans('May'),
                6 => trans('June'),
                7 => trans('July'),
                8 => trans('August'),
                9 => trans('September'),
                10 => trans('October'),
                11 => trans('November'),
                12 => trans('December')
                );
    return $months[(int)$month];
}

//---------------------------------------------------------------------------------------------------

function kv_fiscalyears_list($name, $selected_id=null, $submit_on_change=false){

	$sql = "SELECT * FROM ".TB_PREF."fiscal_year";

    // default to the company current fiscal year
	return combo_input($name, $selected_id, $sql, 'id', '',
	array(
		'order' => 'begin',
		'default' => get_company_pref('f_year'),
		'format' => '_format_fiscalyears',
		'select_submit'=> $submit_on_change,
		'async' => false
	) );
}


function kv_fiscalyears_list_cells($label, $name, $selected_id=null, $submit_on_change=false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo kv_fiscalyears_list($name, $selected_id, $submit_on_change);
	echo "</td>\n";
}

function kv_fiscalyears_list_row($label, $name, $selected_id=null, $submit_on_change=false){
	echo "<tr><td class='label'>$label</td>";
	kv_fiscalyears_list_cells(null, $name, $selected_id, $submit_on_change);
	echo "</tr>\n";
}


function kv_truncate_Complete_HRM(){
    display_notification("submitted catch");
    $featured_hrm_tables = array('kv_empl_departments', 'kv_empl_attendancee', 'kv_empl_cv', 'kv_empl_degree', 'kv_empl_experience', 'kv_empl_info', 'kv_empl_job', 'kv_empl_loan', 'kv_empl_salary', 'kv_empl_salary_advance','kv_empl_cv',  'kv_empl_leave_encashment',  'kv_empl_training', 'kv_empl_loan_types', 'kv_empl_option'); 

    foreach ($featured_hrm_tables as $tbl) {
        $sql_first =" TRUNCATE TABLE ".TB_PREF.$tbl ;
        $result = db_query($sql_first, "could not clean {$tbl} it");
    }

    $sql = "DELETE FROM ".TB_PREF."gl_trans WHERE type=99"; 
    db_query($sql, "Can't delete the GL Transactions");            
}


//---------------------------------------------------------------------------------------------------
function kv_bank_accounts_list($name, $selected_id=null, $submit_on_change=false, $spec_option =false){
    $today = date('Y-m-d');
    $sql ="SELECT bank_acc.id, CONCAT(bank_acc.bank_account_name,' ( ',SUM(amount),' )') as name 
        FROM ".TB_PREF."bank_accounts AS bank_acc LEFT JOIN ".TB_PREF."bank_trans AS trans ON bank_acc.id = trans.bank_act WHERE trans.trans_date <= '$today' GROUP BY trans.bank_act" ;

// default to the company current fiscal year

    return combo_input($name, $selected_id, $sql, 'id', 'name',
    array(
       // 'order' => 'begin',
        //'default' => get_company_pref('f_year'),
        //'format' => '_format_fiscalyears',
        'spec_option' => $spec_option === true ? trans("All Bank Accounts") : $spec_option,
        'select_submit'=> $submit_on_change,
        'async' => false
    ) );
}
        
function kv_bank_accounts_list_cells($label, $name, $selected_id=null, $submit_on_change=false, $spec_option=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo kv_bank_accounts_list($name, $selected_id, $submit_on_change, $spec_option);
    echo "</td>\n";
}

function kv_bank_accounts_list_row($label, $name, $selected_id=null, $submit_on_change=false, $spec_option=false){
    echo "<tr><td class='label'>$label</td>";
    kv_bank_accounts_list_cells(null, $name, $selected_id, $submit_on_change,$spec_option);
    echo "</tr>\n";
}


function backup_hrmtables(){
    
    global $db_connections; 

    $prefix = $db_connections[$_SESSION["wa_current_user"]->company]["tbpref"]; 

    $tables = array();
    $result = db_query('SHOW TABLES');
    $end_char_count = strlen($prefix);
    while($row = db_fetch_row($result)) {
        if(strpos($row[0], $prefix.'kv_empl') !== false)
            $tables[] = substr($row[0], $end_char_count);
    }

    //print_r($tables);
    //exit;
    $return = "# MySQL dump of database '".$db_connections[$_SESSION["wa_current_user"]->company]["name"]."' on host '".$db_connections[$_SESSION["wa_current_user"]->company]["host"]."'
# Backup Date and Time: ".date('Y-m-d h:i a')."
# Module name : Extended HRM
# Module Author : Kvvaradha
# http://www.kvcodes.com
# Company: ".$db_connections[$_SESSION["wa_current_user"]->company]["name"]."
# User : ".$_SESSION["wa_current_user"]->name."
# Compatibility: ".get_company_pref('version_id')."

SET SQL_MODE='';

# Table Backups ".$db_connections[$_SESSION["wa_current_user"]->company]["dbname"]."

#------------------------------------------------------------------------------------
    ";

    //cycle through
    foreach($tables as $table)  {

        $table_exist = db_query("SHOW TABLES LIKE '".$prefix.$table."'");
        $tbl_ext = db_num_rows($table_exist) > 0;
        
        if($tbl_ext){
            $result = db_query('SELECT * FROM '.$prefix.$table);
            $num_fields = db_num_fields($result);
            
            $return.= "### Structure of table `".$table."` ### \n\n DROP TABLE IF EXISTS 0_".$table.";";
            $row2 = db_fetch_row(db_query('SHOW CREATE TABLE '.$prefix.$table));
            $return.= "\n\n".$row2[1].";\n\n";
            $kv = 0; 
            for ($i = 0; $i < $num_fields; $i++) {
                while($row = db_fetch_row($result)){
                    if($kv == 0)
                        $return .= 'INSERT INTO 0_'.$table.' VALUES(';
                    else
                        $return .= "\n (";
                    for($j=0; $j < $num_fields; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = preg_replace("#\n#", "\\n", $row[$j]);
                        if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                        if ($j < ($num_fields-1)) { $return.= ','; }
                    }
                    if($kv == 100){
                        $return .= "); \n";
                        $kv = 0;
                    }
                    else{
                        $return .= "),";
                        $kv++; 
                    }                    
                }
            }
            if(substr($return , -1) == ',')
                $return = substr($return, 0,-1).';';
            $return.="\n\n # -------------------------------------------------------------------------------------\n\n";
        }        
    }

    $return = str_replace($prefix, '0_', $return);

    //save file
    $handle = fopen(dirname(dirname(__FILE__)).'/backups/extendedhrm-'.time().'.sql','w+');
    fwrite($handle,$return);
    fclose($handle);
}

function kv_loan_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo kv_loan_list($name, $selected_id, $all_option, $submit_on_change,  $show_inactive, $editkey);
    echo "</td>\n";
}

function kv_loan_list_row($label, $name, $selected_id=null, $all_option = false, $submit_on_change=false, $show_inactive=false, $editkey = false){
    global $path_to_root;

    echo "<tr><td class='label'>$label</td><td nowrap>";
    echo kv_loan_list_cells($name, $selected_id, $all_option, $submit_on_change,    $show_inactive, $editkey);
    echo "</td>\n</tr>\n";
}

function kv_loan_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, $show_inactive=false, $editkey = false){
    global $all_items;

    $sql = "SELECT id, loan_name, interest_rate FROM ".TB_PREF."kv_empl_loan_types";

    $mode = 0;

    if ($editkey)
        set_editor('employee', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'id', 'loan_name',
    array(
       'format' => '_format_empl_loan',
        'order' => array('loan_name'),
        'search_box' => $mode!=0,
        'type' => 1,
        'size' => 20,
        'spec_option' => $spec_option === true ? trans("All Employee") : $spec_option,
        'spec_id' => $all_items,
        'select_submit'=> $submit_on_change,
        'async' => false,
        'sel_hint' => $mode ? trans("Press Space tab to filter by name fragment; F2 - entry new employee") :  trans("Select employee"),
        'show_inactive' => $show_inactive
    ) );
    if ($editkey)
        $ret .= add_edit_combo('employee');
    return $ret;
}


function _format_empl_loan($row){
    return (user_show_codes() ?  ($row[2] . "% &nbsp;-&nbsp;") : "") . $row[1];
}


//--------------------------------------------------------------------------------
/*   Earnings Deductions Drop-down   */ 

function earning_deductions_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false, $disabled= false){
    $items = array();
    $items['Earnings'] =  trans("Earnings");
    $items['Deductions'] =  trans("Deductions");
    $items['Employer Contribution'] =  trans("Employer Contribution");
    $items['Reimbursement'] =  trans("Reimbursement");

    return array_selector($name, $selected_id, $items, 
        array( 
            'select_submit'=> $submit_on_change,
            'async' => false,
            'disabled' => $disabled ) ); // FIX?
}

function earning_deductions_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false, $disabled=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo earning_deductions_list($name, $selected_id, $name_yes, $name_no, $submit_on_change, $disabled);
    echo "</td>\n";
}

function earning_deductions_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false, $disabled=false){
    echo "<tr><td class='label'>$label</td>";
    earning_deductions_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change, $disabled);
    echo "</tr>\n";
}

//--------------------------------------------------------------------------------
/*   Amount Percentage Drop-down   */ 

function percentage_amount_list($name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    $items = array('Profile Input' => trans("Profile Input"), 'Percentage' => trans("Percentage of Basic"), 'Gross Percentage' => trans("Percentage of Gross"), 'Formula' =>  trans("Formula & Amount"), 'Payroll Input' => trans("Payroll Input"), 'Calculation' => trans("Calculation") );

    return array_selector($name, $selected_id, $items, 
        array( 
            'select_submit'=> $submit_on_change,
            'async' => false,
            'disabled' => $disabled ) ); // FIX?
}

function percentage_amount_list_cells($label, $name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo percentage_amount_list($name, $selected_id, $submit_on_change, $disabled);
    echo "</td>\n";
}

function percentage_amount_list_row($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false){
    echo "<tr><td class='label'>$label</td>";
    percentage_amount_list_cells(null, $name, $selected_id, $submit_on_change, $disabled);
    echo "</tr>\n";
}
//--------------------------------------------------------------------------------
/*   Tax Frequency Drop-down   */ 

function TaxFrequency_List($name, $selected_id=null, $submit_on_change=false, $disabled= false){
    $items = array();
    $items['1'] =  trans("Monthly");
    $items['3'] =  trans("Quaterly(3 Months)");
    $items['6'] =  trans("Half(6 Months)");
    $items['12'] =  trans("Annual (12 Months)");

    return array_selector($name, $selected_id, $items, 
        array( 
            'select_submit'=> $submit_on_change,
            'async' => false,
            'disabled' => $disabled ) ); // FIX?
}

function TaxFrequency_List_cells($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo TaxFrequency_List($name, $selected_id,  $submit_on_change, $disabled);
    echo "</td>\n";
}

function TaxFrequency_List_row($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false){
    echo "<tr><td class='label'>$label</td>";
    TaxFrequency_List_cells(null, $name, $selected_id, $submit_on_change, $disabled);
    echo "</tr>\n";
}

function kv_empl_number_list_row($label, $name, $selected_id=null, $from = 0, $to = 10, $submit_on_change=false) {
    $kv_empl_number = array();
    for($from; $from <= $to; $from++){
        $kv_empl_number[$from] = $from;
    }
    echo "<tr><td class='label'>$label</td><td>";
    $options = array(
        'select_submit'=> $submit_on_change
    );
    echo array_selector($name, $selected_id, $kv_empl_number, $options);
    echo "</td></tr>\n";
}

function kv_empl_contract_list_row($label, $name1,$name2,$selected_id1=null,$selected_id2=null, $from = 0, $to = 10,$from1 = 0, $to1 = 10, $submit_on_change=false) {
    $kv_empl_number = array();
    for($from; $from <= $to; $from++){
        $kv_empl_number[$from] = $from;
    }
    $kv_empl_number1 = array();
    for($from1; $from1 <= $to1; $from1++){
        $kv_empl_number1[$from1] = $from1;
    }
    echo "<tr><td class='label'>$label</td><td>";
    $options = array(
        'select_submit'=> $submit_on_change
    );
    echo array_selector($name1, $selected_id1, $kv_empl_number, $options).'/'.array_selector($name2, $selected_id2, $kv_empl_number1, $options);
    echo "</td></tr>\n";
}


//---------------------------------------------------------------------------------------------------
function kv_allowances_list($name, $selected_id=null, $submit_on_change=false, $all_option =false, $esic= false, $pf=false){
    $today = date('Y-m-d');
    $sql ="SELECT id, description  FROM ".TB_PREF."kv_empl_allowances WHERE 1=1" ;

    if($esic && $pf == false)
        $sql .= " AND esic=1";

    elseif($pf  && $esic == false)
        $sql .= " AND pf =1" ;

    elseif($esic == true && $pf == true)
        $sql .= " AND ( esic=1 OR pf =1 )";

    return combo_input($name, $selected_id, $sql, 'id', 'description',
    array(
        'select_submit'=> $submit_on_change,
        'spec_option' => $all_option===true ?  trans("Select Allowances") : $all_option,
        'async' => false
    ) );
}
        
function kv_allowances_list_cells($label, $name, $selected_id=null, $submit_on_change=false, $all_option = false, $esic=false, $pf =false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo kv_allowances_list($name, $selected_id, $submit_on_change, $all_option, $esic, $pf);
    echo "</td>\n";
}

function kv_allowances_list_row($label, $name, $selected_id=null, $submit_on_change=false, $all_option=false, $esic=false, $pf=false){
    echo "<tr><td class='label'>$label</td>";
    kv_allowances_list_cells(null, $name, $selected_id, $submit_on_change, $all_option, $esic,$pf);
    echo "</tr>\n";
}

//--------------------------------------------------------------------------------
/*   ESIC PF Drop-down   */ 

function esic_pf_list($name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    $items = array('esic' => trans("ESIC"), 'pf' => trans("PF"),/* 'Formula' =>  trans("Formula")*/);

    return array_selector($name, $selected_id, $items, 
        array( 
            'select_submit'=> $submit_on_change,
            'async' => false,
            'disabled' => $disabled ) ); // FIX?
}

function esic_pf_list_cells($label, $name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo esic_pf_list($name, $selected_id, $submit_on_change, $disabled);
    echo "</td>\n";
}

function esic_pf_list_row($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false){
    echo "<tr><td class='label'>$label</td>";
    esic_pf_list_cells(null, $name, $selected_id, $submit_on_change, $disabled);
    echo "</tr>\n";
}

//--------------------------------------------------------------------------------
/*   ESIC PF Drop-down   */ 

function kv_rate_per_hour($name, $selected_id=null,  $submit_on_change=false, $disabled=false){

    $car_rate = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'car_rate'));
    $bike_rate = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'bike_rate'));
    $items = array($car_rate  => trans("Car"), $bike_rate  => trans("Bike"),/* 'Formula' =>  trans("Formula")*/);

    return array_selector($name, $selected_id, $items, 
        array( 
            'select_submit'=> $submit_on_change,
            'async' => false,
            'disabled' => $disabled ) ); // FIX?
}

function kv_rate_per_hour_cells($label, $name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo kv_rate_per_hour($name, $selected_id, $submit_on_change, $disabled);
    echo "</td>\n";
}

function kv_rate_per_hour_row($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false){
    echo "<tr><td class='label'>$label</td>";
    kv_rate_per_hour_cells(null, $name, $selected_id, $submit_on_change, $disabled);
    echo "</tr>\n";
}

//--------------------------------------------------------------------------------
/*  Bank statement ESIC PF Drop-down   */ 

function banksmt_esic_pf_list($name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    $items = array('bank' =>  trans("Bank Statement"), 'esic' => trans("ESIC"), 'pf' => trans("PF") );

    return array_selector($name, $selected_id, $items, 
        array( 
            'select_submit'=> $submit_on_change,
            'async' => false,
            'disabled' => $disabled ) ); // FIX?
}

//--------------------------------------------------------------------------------
/*  Attendance Drop-down   */ 

function attendance_mark_cells($label, $name, $selected_id=null,  $submit_on_change=false, $disabled=false, $style=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td ".$style.">";
    echo attendance_mark($name, $selected_id, $submit_on_change, $disabled);
    echo "</td>\n";
}

function attendance_mark_row($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false, $style=false){
    echo "<tr><td class='label'>$label</td>";
    attendance_mark_cells(null, $name, $selected_id, $submit_on_change, $disabled, $style);
    echo "</tr>\n";
}
function attendance_mark($name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    $items = array('P' =>  trans("P"), 'A' => trans("A"), 'HD' => trans("HD"), 'L' => trans("L"), 'AL' => trans("AL"), 'CL' => trans("CL"), 'ML' => trans("ML") );

    return array_selector($name, $selected_id, $items, 
        array( 
            'select_submit'=> $submit_on_change,
            'async' => false,
            'disabled' => $disabled ) ); // FIX?
}

//-------------------------------------------------------------------------------
function empl_shifts_list($name, $selected_id=null, $spec_option = false, $submit_on_change =false){
    $sql = "SELECT id, description FROM ".TB_PREF."kv_empl_shifts WHERE !inactive";
    return combo_input($name, $selected_id, $sql, 'id', 'description', array('select_submit'=> $submit_on_change, 'spec_option' => $spec_option === true ? trans("No Shift") : $spec_option,));
}

function empl_shifts_list_cells($label, $name, $selected_id=null, $spec_option =false, $submit_on_change =false, $textbox=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo empl_shifts_list($name, $selected_id, $spec_option, $submit_on_change).($textbox? ' or '.text_input($name.'_', null ) : '');
    echo "</td>\n";
}

function empl_shifts_list_row($label, $name, $selected_id=null, $spec_option = false, $submit_on_change= false, $textbox=false){
    echo "<tr><td class='label'>$label</td>";
    empl_shifts_list_cells(null, $name, $selected_id, $spec_option, $submit_on_change, $textbox);
    echo "</tr>\n";
}

function get_employee_balance_loan($empl_id){
    $loan_details =  GetAll('kv_empl_loan', array('status' => 'Active', 'empl_id' => $empl_id));
    if($loan_details){       
        $return_amt = 0;
        foreach($loan_details as $key => $single){
            $return_amt += ($single['loan_amount']/$single['periods']) *( $single['periods']-$single['periods_paid']);
        }
        return $return_amt;
    } else
        return 0;
}

function kv_loan_balance_dropdown($name, $loan_table_id, $spec_option = false, $submit_on_change= false, $textbox=false) {
	$myrow= GetRow('kv_empl_loan', array('id' => $loan_table_id));
	$repay_ar = array(0 => '0');
	for($vj =1; $vj<= ($myrow["periods"]-$myrow['periods_paid']); $vj++)
		$repay_ar[$vj] = $myrow['monthly_pay']*$vj;
	$options = array('select_submit'=> $submit_on_change,  'spec_option' => $spec_option === true ? trans("None") : $spec_option,);
	return array_selector($name, null, $repay_ar, $options);
	
}
function kv_loan_balance_dropdown_cells($label, $name, $selected_id=null, $spec_option =false, $submit_on_change =false, $textbox=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo kv_loan_balance_dropdown($name, $selected_id, $spec_option, $submit_on_change, $textbox);
    echo "</td>\n";
}

function kv_loan_balance_dropdown_row($label, $name, $selected_id=null, $spec_option = false, $submit_on_change= false, $textbox=false){
    echo "<tr><td class='label'>$label</td>";
    kv_loan_balance_dropdown_cells(null, $name, $selected_id, $spec_option, $submit_on_change, $textbox);
    echo "</tr>\n";
}

function  hrm_empl_relation_list_row($label, $name, $selected_id=null, $submit_on_change=false) {
	global $hrm_empl_relation;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(    'select_submit'=> $submit_on_change);
	echo array_selector($name, $selected_id, $hrm_empl_relation, $options);
	echo "</td></tr>\n";
}

function Void_Payroll($id){

	begin_transaction();
	hook_db_prevoid(99, $id);

	if (get_voided_entry(99, $id))
		return;

	$Payslip = GetRow('kv_empl_salary', array('id' => $id));
	$loan_ar = unserialize(base64_decode($Payslip['loans']));
	if(!empty($loan_ar)){	
		foreach($loan_ar as $single){
			$row = GetRow('kv_empl_loan', array('id' => $single[0]));
			$paid_row  = $row['periods_paid']-$single[2];
			$update_row = array('periods_paid' => ($paid_row >=0 ? $paid_row : 0) );
			if($row['status'] == 'Inactive')
				$update_row['status'] = 'Active' ;			
			Update('kv_empl_loan', array('id' => 	$single[0]	), $update_row);			
		}
	}
	Delete('kv_empl_salary', array('id' => $id));

	void_gl_trans(99, $id);
	commit_transaction();
}

function attendance_month_selection($name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    $items = array(1 =>  trans("Current Month Only"), 2 => trans("Current + Next Month"), 3 => trans("Previous + Current Month") );

    return array_selector($name, $selected_id, $items, 
        array( 
            'select_submit'=> $submit_on_change,
            'async' => false,
            'disabled' => $disabled ) ); // FIX?
}

//--------------------------------------------------------------------------------
/*   Leave frequency Drop-down   */ 

function leave_frequency_list($name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    $items = array('12' => trans("Year"), '1' => trans("Month"), '0' => trans("Maternity"), '-1' => trans("All Time"), '-2' => trans("Each Event"), '60' => trans("5 Year"));

    return array_selector($name, $selected_id, $items, 
        array( 
            'select_submit'=> $submit_on_change,
            'async' => false,
            'disabled' => $disabled ) ); // FIX?
}

function leave_type_list($name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    $items = array('0' => trans("Unpaid Leave"), '1' => trans("Paid Leave"));

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false,
            'disabled' => $disabled ) ); // FIX?
}

function cut_salry_list($name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    $items = array('0' => trans("Half Day"), '1' => trans("Full Day"));

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false,
            'disabled' => $disabled ) ); // FIX?
}

function leave_frequency_list_cells($label, $name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo leave_frequency_list($name, $selected_id, $submit_on_change, $disabled);
    echo "</td>\n";
}

function leave_type_list_cells($label, $name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo leave_type_list($name, $selected_id, $submit_on_change, $disabled);
    echo "</td>\n";
}

function salary_list_cells($label, $name, $selected_id=null,  $submit_on_change=false, $disabled=false){
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo cut_salry_list($name, $selected_id, $submit_on_change, $disabled);
    echo "</td>\n";
}

function leave_frequency_list_row($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false){
    echo "<tr><td class='label'>$label</td>";
    leave_frequency_list_cells(null, $name, $selected_id, $submit_on_change, $disabled);
    echo "</tr>\n";
}

function leave_type($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false){
    echo "<tr><td class='label'>$label</td>";
    leave_type_list_cells(null, $name, $selected_id, $submit_on_change, $disabled);
    echo "</tr>\n";
}


function salary_cut_ddl($label, $name, $selected_id=null, $submit_on_change=false, $disabled=false){
    echo "<tr><td class='label'>$label</td>";
    salary_list_cells(null, $name, $selected_id, $submit_on_change, $disabled);
    echo "</tr>\n";
}

function kv_hrm_textarea_cells($label, $name, $value, $cols, $rows, $title = null, $params="")
{
    global $Ajax;

    default_focus($name);
    if ($label != null)
        echo "<td $params>$label</td>\n";
    if ($value == null)
        $value = (!isset($_POST[$name]) ? "" : $_POST[$name]);
    echo "<td><textarea name='$name' id='$name' style='width: 500px; height: 100px;' cols='$cols' rows='$rows'"
    .($title ? " title='$title'" : '')
    .">$value</textarea></td>\n";
    $Ajax->addUpdate($name, $name, $value);
}

function kv_hrm_textarea_row($label, $name, $value, $cols, $rows, $title=null, $params="")
{
    echo "<tr><td class='label'>$label</td>";
    kv_hrm_textarea_cells(null, $name, $value, $cols, $rows, $title, $params);
    echo "</tr>\n";
}

?>
