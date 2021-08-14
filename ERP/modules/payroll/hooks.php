<?php
define('SS_PAYROLL', 71 << 8);

class payroll_app extends application
{
    function payroll_app() {
        global $path_to_root;

        $this->application("payroll", trans($this->help_context = "P&ayroll"));

        $this->add_module(trans("Transactions"));
        $this->add_lapp_function(0, trans("&Create Paychecks"), $path_to_root . '/modules/payroll/paycheck.php', 'SA_PAYROLL', MENU_TRANSACTION);
        $this->add_lapp_function(0, trans("Pay Payroll &Liabilities"), $path_to_root . '/modules/payroll/payroll_liabilities.php', 'SA_PAYROLL', MENU_TRANSACTION);

        $this->add_module(trans("Inquiries and Reports"));
        $this->add_lapp_function(1, trans("Form 941"), $path_to_root . '/modules/payroll/form_941.php', 'SA_PAYROLL', MENU_INQUIRY);
        $this->add_lapp_function(1, trans("Form W2"), $path_to_root . '/modules/payroll/form_w2.php', 'SA_PAYROLL', MENU_INQUIRY);

        $this->add_module(trans("Maintenance"));
        $this->add_lapp_function(2, trans("&Employees"), $path_to_root . '/modules/payroll/employees.php', 'SA_PAYROLL', MENU_MAINTENANCE);
        $this->add_rapp_function(2, trans("Deprecated - Payroll Taxes"), $path_to_root . '/modules/payroll/payroll_taxes.php', 'SA_PAYROLL', MENU_MAINTENANCE);
        //$this->add_rapp_function(2, trans("Payroll Tax Groups"),
        //        $path_to_root.'/modules/payroll/payroll_tax_groups.php', 'SA_PAYROLL', MENU_MAINTENANCE);
        //$this->add_rapp_function(2, trans("File Template"),
        //        $path_to_root.'/modules/payroll/template.php', 'SA_PAYROLL', MENU_MAINTENANCE);

        $this->add_lapp_function(2, trans("Pay Types"), $path_to_root . '/modules/payroll/managePayType.php', 'SA_PAYROLL', MENU_MAINTENANCE);
        $this->add_rapp_function(2, trans("&Taxes & Deductions"), $path_to_root . '/modules/payroll/manageTaxes.php', 'SA_PAYROLL', MENU_MAINTENANCE);

        $this->add_extensions();
    }
}

class hooks_payroll extends hooks
{
    var $module_name = 'payroll'; // extension module name.

    function install_tabs($app) {
        set_ext_domain('modules/payroll'); // set text domain for gettext
        $app->add_application(new payroll_app); // add menu tab defined by example_class
        set_ext_domain();
    }

    function install_access() {
        $security_sections[SS_PAYROLL] = trans("Payroll");
        $security_areas['SA_PAYROLL']  = array(
            SS_PAYROLL | 1,
            trans("Process Payroll and Reports ")
        );
        return array(
            $security_areas,
            $security_sections
        );
    }

    function activate_extension($company, $check_only = true) {
        global $db_connections;
        
        $updates = array(
            'payroll.sql' => array(
                'payroll'
            )
        );
        return $this->update_databases($company, $updates, $check_only);
    }

}

?>