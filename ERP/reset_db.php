<?php

// Clear out all frontaccounting transactions
// Leaves customers/bank accounts alone, but removes all transaction items
// BE CAREFUL YOU WILL LOSE YOUR TRANSACTION DATA IF YOU RUN THIS SCRIPT
// BACKUP BEFORE YOU RUN IT!!!
// IF YOU DON'T KNOW EXACTLY WHAT YOUR ARE DOING, DON'T RUN THIS SCRIPT


// ask for input
fwrite(STDOUT, "Enter your MySQL FrontAccounting database name: ");
// get input
$db = trim(fgets(STDIN));

fwrite(STDOUT, "Enter your Company Number eg. 1, 2 etc: ");
// get input
$company_number = trim(fgets(STDIN));

// ask for input
fwrite(STDOUT, "Enter your MySQL host (usually localhost): ");
// get input
$host = trim(fgets(STDIN));

fwrite(STDOUT, "Enter your MySQL user id: ");
// get input
$userid = trim(fgets(STDIN));

fwrite(STDOUT, "Enter your MySQL password: ");
// get input
$pword = trim(fgets(STDIN));

// Confirmation - must be Y in capitals, or I stop right here.
fwrite(STDOUT, "You are going to clear the FrontAccounting transactions for database : $db company number : $company_number\n" . "Are you absolutely sure you want to do this? (Y/N)");
$confirm = trim(fgets(STDIN));
if ($confirm!="Y") {
    echo "OK...aborting\n";
    exit();
}

//$conn = mysql_connect($host,$userid,$pword); //<---enter your host, user id and password for MySQL here
$conn = mysqli_connect($host, $userid, $pword, $db);

if ($conn==null) {
    echo "Could not connect to MySQL with the host/username/password you provided. Try again.\n";
    exit();
}
$tbllist = array();
// Here's the magic - read the end of this file into an array that contains the table names
// you want to clear.
$tbllist = explode("\n",file_get_contents(__FILE__, NULL, NULL,  __COMPILER_HALT_OFFSET__));

// Remove first entry - it's just a carriage return.
unset($tbllist[0]);


$sql = "SET FOREIGN_KEY_CHECKS = 0;";
mysqli_query($conn, $sql ) ;

// Process each table clearing it.
foreach ($tbllist as $tbl) {
    if (substr($tbl,0,1)!="#") run_delete_qry($tbl);
}

$sql = "SET FOREIGN_KEY_CHECKS = 1;";
mysqli_query($conn, $sql ) ;

echo "Finished clearing transaction tables\n";
exit();
// A function to clear data from a table you specify
function run_delete_qry($tblname) {
    global $db;
    global $conn;
    global $company_number;

    $sql = "truncate table " . $company_number . "_" . $tblname ;

    $result = mysqli_query($conn, $sql ) ;

    if ($result!=1) {
        echo "Warning: SQL statement " . $sql . " failed\n";
        echo "with an error message of " .mysqli_connect_errno() . mysqli_error($conn);
//        mysql_close($conn);

        return;
    }
    echo "Cleared " . $company_number . "_" . $tblname . "\n";
}
__HALT_COMPILER();
# Tables you want to clear go here
# Comments start with the pound sign
gl_trans
bank_trans
debtor_trans
debtor_trans_details
trans_tax_details
purch_orders
purch_order_details
sales_orders
sales_order_details
wo_issues
wo_issue_items
wo_manufacture
wo_requirements
supp_invoice_items
trans_tax_details
supp_allocations
grn_batch
grn_items
audit_trail
voided
refs
comments
cust_allocations
stock_moves
journal
other_charges_trans_details
vouchers
voucher_transactions
credit_requests
kv_empl_leave_applied
kv_empl_attendance
purchase_requests
supp_trans
stock_moves
voided_bank_trans
voided_customer_rewards
voided_cust_allocations
voided_debtor_trans
voided_debtor_trans_details
voided_gl_trans
voided_journal
voided_purch_orders
voided_sales_orders
voided_sales_order_details
voided_stock_moves
voided_supp_allocations
voided_supp_trans
voided_trans_tax_details
voided_customer_rewards
voided_sales_order_details
voided_stock_moves
voided_supp_invoice_items
voided_purch_order_details
voided_grn_items