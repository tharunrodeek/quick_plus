<?php
/****************************************
/*  Author  : Kvvaradha
/*  Module  : Extended HRM
/*  E-mail  : admin@kvcodes.com
/*  Version : 1.0
/*  Http    : www.kvcodes.com
*****************************************/
define ('SS_EXHRM', 250<<8);
define ('SS_EXHRM_SETTINGS', 251<<8);
define ('SS_EXHRM_PAYROLL', 252<<8);
define ('SS_EXHRM_EMPLOYEE', 253<<8);

class ExtendedHRM_app extends application{
    var $apps;
   
    function __construct()  {     
      parent::__construct("extendedhrm", trans($this->help_context = "&HRM"));
	  
      $this->add_module(trans("Transactions"));
        
      $this->add_lapp_function(0, trans("PaySlip"),'modules/ExtendedHRM/payslip.php', 'HR_PAYSLIP', MENU_TRANSACTION);
      $this->add_lapp_function(0, trans("Payroll"), 'modules/ExtendedHRM/manage/payroll.php', 'HR_PAYSLIP', MENU_TRANSACTION);
      $this->add_lapp_function(0, trans("Advance Salary"), 'modules/ExtendedHRM/advance_salary.php', 'HR_PAYSLIP', MENU_TRANSACTION);
      $this->add_rapp_function(0, trans("Attendance Entry"), 'modules/ExtendedHRM/attendance.php', 'HR_ATTENDANCE', MENU_TRANSACTION);
     // $this->add_rapp_function(0, trans("Month Attendance"), 'modules/ExtendedHRM/monthly_attendance.php', 'HR_ATTENDANCE', MENU_TRANSACTION);
     // $this->add_rapp_function(0, trans("Edit Attendance"), 'modules/ExtendedHRM/single_empl_attend.php', 'HR_ATTENDANCE', MENU_TRANSACTION);
      $this->add_rapp_function(0, trans("ZK Time Attendance"), 'modules/ExtendedHRM/zkteco_attendance.php', 'HR_ATTENDANCE', MENU_TRANSACTION);
      $this->add_rapp_function(0, trans("Leave Request & Approval"), 'modules/ExtendedHRM/leave_approval.php', 'HR_ATTENDANCE', MENU_TRANSACTION);
	  $this->add_lapp_function(0, trans("Leave Encashment"),'modules/ExtendedHRM/leave_encashment.php','HR_LEAVE_ENCASHMENT', MENU_TRANSACTION);
      $this->add_lapp_function(0, trans("End of Service Benefit"), 'modules/ExtendedHRM/esb.php','HR_PAYSLIP', MENU_TRANSACTION);
      $this->add_rapp_function(0, trans("Loan Entry"), 'modules/ExtendedHRM/manage/loan_form.php', 'HR_LOANFORM', MENU_TRANSACTION);
      $this->add_lapp_function(0, trans("Employee Attachments"), 'modules/ExtendedHRM/manage/attachments.php', 'HR_EMPL_INFO', MENU_TRANSACTION);
      $this->add_rapp_function(0, trans("Family Data"), 'modules/ExtendedHRM/manage/family_data.php', 'HR_EMPL_INFO', MENU_TRANSACTION);
      $this->add_rapp_function(0, trans("Email"), 'modules/ExtendedHRM/manage/email.php', 'HR_EMPL_INFO', MENU_TRANSACTION);

      if($this->GetHRMOption('enable_employee_access')){  // Employee login follow up
        $this->add_lapp_function(0, trans('Profile'), 'modules/ExtendedHRM/employee', 'HR_EML_PROFILE', MENU_TRANSACTION);
      }     

      $this->add_module(trans("Inquires"));
      $this->add_lapp_function(1, trans("Payroll Inquiry"), 'modules/ExtendedHRM/inquires/payroll_history_inquiry.php', 'HR_PAYROLL_INQ', MENU_INQUIRY);
      $this->add_lapp_function(1, trans("Attendance Inquiry"), 'modules/ExtendedHRM/inquires/attendance_inquiry.php', 'HR_ATTENDANCE', MENU_INQUIRY);
      $this->add_lapp_function(1, trans("Daily Attendance Inquiry"), 'modules/ExtendedHRM/inquires/daily_attendance_inquiry.php', 'HR_SELATTENDANCE', MENU_INQUIRY);
      $this->add_rapp_function(1, trans("Loan Inquiry"), 'modules/ExtendedHRM/inquires/loan_inquiry.php', 'HR_LOAN_INQ', MENU_INQUIRY);
      $this->add_rapp_function(1, trans("Advance Salary Inquiry"), 'modules/ExtendedHRM/inquires/adv_sal_inquiry.php', 'HR_PAYROLL_INQ', MENU_INQUIRY);
      $this->add_lapp_function(1, trans("Employees Inquiry"), 'modules/ExtendedHRM/inquires/employees_inquiry.php', 'HR_EMPLOYEE_INQ', MENU_INQUIRY);
      $this->add_rapp_function(1, trans("Document Inquiry"), 'modules/ExtendedHRM/inquires/document_inquiry.php', 'HR_EMPLOYEE_INQ', MENU_INQUIRY);
	  $this->add_rapp_function(1, trans("Leave Encashment Inquiry"), 'modules/ExtendedHRM/inquires/encashment_inquiry.php', 'HR_LEAVE_ENCASHMENT', MENU_INQUIRY);
	  $this->add_rapp_function(1, trans("HRM Reports"), 'modules/ExtendedHRM/reports/hrm_reports.php?Class=8&REP_ID=801', 'HR_REPORTS', MENU_INQUIRY);
      
      $this->add_module(trans("Maintainance"));

      $this->add_lapp_function(2, trans("Add And Manage Employees"), 'modules/ExtendedHRM/manage/employees.php', 'HR_EMPL_INFO', MENU_ENTRY);
      $this->add_lapp_function(2, trans("Department"), 'modules/ExtendedHRM/manage/department.php', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      $this->add_rapp_function(2, trans("Document Type"), 'modules/ExtendedHRM/manage/doc_type.php', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      //$this->add_rapp_function(2, trans("Notifications"), 'modules/ExtendedHRM/manage/notifications.php', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      $this->add_lapp_function(2, trans("Designations"), 'modules/ExtendedHRM/manage/designation.php', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      $this->add_lapp_function(2, trans("Designation Group"), 'modules/ExtendedHRM/manage/designation_group.php', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      $this->add_rapp_function(2, trans("Allowance Setup"), 'modules/ExtendedHRM/manage/allowances.php', 'HR_PAYROLL_SETUP', MENU_MAINTENANCE);
	  $this->add_rapp_function(2, trans("Company Shifts"), 'modules/ExtendedHRM/manage/shifts.php', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      $this->add_lapp_function(2, trans("Grade Setup"), 'modules/ExtendedHRM/grade.php', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      if($this->GetHRMOption('tax_used'))
			$this->add_rapp_function(2, trans("Taxes"), 'modules/ExtendedHRM/tax/', 'HR_EMPL_TAX', MENU_MAINTENANCE);
	  if($this->GetHRMOption('enable_esic') || $this->GetHRMOption('enable_pf'))
		  $this->add_rapp_function(2, trans("ESIC & PF Settings"), 'modules/ExtendedHRM/esic_pf_settings.php', 'HR_EMPL_TAX', MENU_MAINTENANCE);

      $this->add_lapp_function(2, trans("Loan Types"), 'modules/ExtendedHRM/manage/loan_type.php', 'HR_LOANTYPE', MENU_MAINTENANCE);
      $this->add_rapp_function(2, trans("Backup & Restore"), 'modules/ExtendedHRM/manage/backups.php?', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      $this->add_rapp_function(2, trans("Clear Demo and Data's"), 'modules/ExtendedHRM/docs/', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      $this->add_rapp_function(2, trans("Documentation"), 'modules/ExtendedHRM/docs/index.php?tut=text', 'SA_OPEN', MENU_MAINTENANCE);
      $this->add_lapp_function(2, trans("Gazetted Off Days"), 'modules/ExtendedHRM/manage/gazetted_holidays.php', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
	  if(isset($_SESSION['wa_current_user']) && ($_SESSION['wa_current_user']->username == 'kvcodes' ||$_SESSION['wa_current_user']->username == 'kvvaradha14' ) ){
			$this->add_rapp_function(2, trans("Pick List Types"), 'modules/ExtendedHRM/manage/picklist.php', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
			$this->add_rapp_function(2, trans("Pick Type"), 'modules/ExtendedHRM/manage/PickType.php', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
	  }
      $this->add_rapp_function(2, trans("Attendance Settings"), 'modules/ExtendedHRM/manage/attendance_settings.php', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
	  
	  $this->add_lapp_function(2, trans("Leave Pay Settings"), 'modules/ExtendedHRM/manage/leave_pay_allowance_settings.php','HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      $this->add_rapp_function(2, trans("Medical Preimum"), 'modules/ExtendedHRM/manage/medical_premium.php','HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      $this->add_rapp_function(2, trans("Visa & Immigration Types"), 'modules/ExtendedHRM/manage/visa_types.php','HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      $this->add_lapp_function(2, trans("Leave Travel/Passage"), 'modules/ExtendedHRM/manage/leave_travel.php','HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      $this->add_lapp_function(2, trans("LMRA Fees"), 'modules/ExtendedHRM/manage/lmra.php','HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      $this->add_lapp_function(2, trans("GOSI Settings"), 'modules/ExtendedHRM/manage/gosi_settings.php','HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      $this->add_rapp_function(2, trans("Indemnity Settings"), 'modules/ExtendedHRM/manage/indemnity_setup.php','HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
	  $this->add_lapp_function(2, trans("Nationalities"), 'modules/ExtendedHRM/manage/nationalities.php', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
	  $this->add_rapp_function(2, trans("Languages"), 'modules/ExtendedHRM/manage/picklist.php?type=1', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
	  $this->add_rapp_function(2, trans("Proficiency"), 'modules/ExtendedHRM/manage/picklist.php?type=2', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      $this->add_rapp_function(2, trans("Settings"), 'modules/ExtendedHRM/manage/hrm_settings.php', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      if($this->GetHRMOption('license_mgr')){
        $this->add_lapp_function(2, trans("License  Category"), 'modules/ExtendedHRM/manage/picklist.php?type=6', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
        $this->add_lapp_function(2, trans("License Type "), 'modules/ExtendedHRM/manage/picklist.php?type=5', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
      }
      $this->add_extensions();        
    }      
    function GetHRMOption($option){
      $sql = "SELECT option_value FROM ".TB_PREF."kv_empl_option WHERE option_name=".db_escape($option);
      $res = db_query($sql, "Can't get option value for settings" );
      if($row =db_fetch($res))
        return $row[0];
      else
        return false;
    }
}

class hooks_ExtendedHRM extends hooks {
	var $module_name = 'ExtendedHRM';

	/*
		Install additonal menu options provided by module
	*/
    function install_tabs($app) {
        $app->add_application(new ExtendedHRM_app);
    }
  
    function install_access()	{
        $security_sections[SS_EXHRM]               = trans("HRM");
        $security_sections[SS_EXHRM_SETTINGS]       = trans("HRM Settings");
        $security_sections[SS_EXHRM_PAYROLL]        = trans("HRM Payroll");
        $security_sections[SS_EXHRM_EMPLOYEE]        = trans("HRM Employee Access");
        // ############################################################################################
        // HRM related functionality
        //
        // Employee Information
         $security_areas['HR_EMPL_INFO'] = array(SS_EXHRM|1, trans("HRM Employee info"));
		 $security_areas['HR_ATTENDANCE'] = array(SS_EXHRM|2, trans("Employee Attendence"));
         $security_areas['HR_LOANFORM'] = array(SS_EXHRM|3, trans("Loan Application Form"));
		 $security_areas['HR_EMPLOYEE_INQ'] = array(SS_EXHRM|4, trans("Employee Inquiry"));
         $security_areas['HR_SELATTENDANCE'] = array(SS_EXHRM|5, trans("Selective Attendance List Show"));
         $security_areas['HR_LOAN_INQ'] = array(SS_EXHRM|6, trans("Loan Approve Inquiry"));
         $security_areas['HR_LEAVEFORM'] = array(SS_EXHRM|7, trans("Leave Application Form"));
		 $security_areas['HR_DOCUMENT_MGT'] = array(SS_EXHRM|8, trans("Document"));
		 
         $security_areas['HR_PAYSLIP'] = array(SS_EXHRM_PAYROLL|1, trans("Pay Slip Generation"));
		 $security_areas['HR_LEAVE_ENCASHMENT'] = array(SS_EXHRM_PAYROLL|2, trans("Leave Encashment"));
		 $security_areas['HR_PAYROLL_SETUP'] = array(SS_EXHRM_PAYROLL|3, trans("Payroll Setup"));
		 $security_areas['HR_PAYROLL_INQ'] = array(SS_EXHRM_PAYROLL|4, trans("Payroll Inquriy"));
         
         $security_areas['HR_REPORTS'] = array(SS_EXHRM_SETTINGS|1, trans("HRM Reports"));
         $security_areas['HR_EMPL_TAX'] = array(SS_EXHRM_SETTINGS|2, trans("Tax Setup"));
         $security_areas['HR_LOANTYPE'] = array(SS_EXHRM_SETTINGS|3, trans("Loan Type Setup"));
         $security_areas['HR_EMPLOYEE_SETUP'] = array(SS_EXHRM_SETTINGS|4, trans("Setup"));
         
         // Employees Access
         $security_areas['HR_EML_PROFILE'] = array(SS_EXHRM_EMPLOYEE|1, trans("Profile"));
         $security_areas['HR_EML_LEAVE'] = array(SS_EXHRM_EMPLOYEE|2, trans("Leave Form"));
         $security_areas['HR_EML_PAYROLL'] = array(SS_EXHRM_EMPLOYEE|3, trans("Payroll History"));
         $security_areas['HR_EML_LEAVE_INQUIRY'] = array(SS_EXHRM_EMPLOYEE|4, trans("Leave Inquiry"));
         $security_areas['HR_EML_LOAN'] = array(SS_EXHRM_EMPLOYEE|5, trans("Employee Loan Apply"));

		return array($security_areas, $security_sections);
	}

    /* This method is called on extension activation for company.   */
    function activate_extension($company, $check_only=true) {
        global $db_connections;

        $updates = array(   'update.sql' => array('ExtendedHRM')    );

        $result = db_query("SHOW COLUMNS FROM `".TB_PREF."users` LIKE 'employee_id'", "can't get mysql result");
        $exists = (db_num_rows($result))?TRUE:FALSE;
        if(!$exists)
          db_query("ALTER TABLE `".TB_PREF."users` ADD `employee_id` INT(11) NOT NULL AFTER `user_id`", "can't alter suppliers Table");
            return $this->update_databases($company, $updates, $check_only);
        }

    function deactivate_extension($company, $check_only=true) {
        global $db_connections;

        $updates = array(     'drop.sql' => array('ugly_hack') );

        return $this->update_databases($company, $updates, $check_only);
    }
}
?>