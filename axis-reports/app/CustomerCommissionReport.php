<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomerCommissionReport extends Model
{
    /**
     * @param array $filters
     * @return string
     * Generate SQL for Customer Commission report
     */
    public function getSQL($filters = [])
    {
        $where = "";
        if (!empty($filters)) {
            if (!empty($filters['invoice_no'])) {
                $where .= " and  b.reference = " . $filters['invoice_no'];
            }
            if (!empty($filters['tran_date_from'])) {
                $date_from = date2sq1($filters['tran_date_from']);
                $where .= " and  b.tran_date >= '$date_from'";
            }
            if (!empty($filters['tran_date_to'])) {
                $date_to = date2sq1($filters['tran_date_to']);
                $where .= " and  b.tran_date <= '$date_to'";
            }
            if (!empty($filters['customer'])) {
                $where .= " and  b.debtor_no =" . $filters['customer'];
            }
            if (!empty($filters['employee'])) {
                $where .= " and  a.created_by =" . $filters['employee'];
            }
            if (!empty($filters['service'])) {
                $where .= " and  a.stock_id ='" . $filters['service'] . "'";
            }

        }

        $sql = "SELECT IFNULL(((`a`.`unit_price` * `e`.`customer_commission`) / 100),0) AS `customer_commission`, 
IFNULL((((`a`.`unit_price` * `e`.`customer_commission`) / 100) * `a`.`quantity`),0) AS `total_customer_commission`,
`b`.`reference` AS `invoice_no`,`a`.`stock_id` AS `stock_id`,`a`.`description` AS `description`,`a`.`quantity` AS `quantity`,
`a`.`created_by` AS `created_by`,`a`.`updated_by` AS `updated_by`,`c`.`name` AS `customer_name`,
`b`.`display_customer` AS `reference_customer`,`0_users`.`user_id` AS `created_employee`,`b`.`tran_date` AS `transaction_date`
FROM (((((`0_debtor_trans_details` `a`
LEFT JOIN `0_debtor_trans` `b` ON((`b`.`trans_no` = `a`.`debtor_trans_no`)))
LEFT JOIN `0_debtors_master` `c` ON((`c`.`debtor_no` = `b`.`debtor_no`)))
LEFT JOIN `0_users` ON((`0_users`.`id` = `a`.`created_by`)))
LEFT JOIN `0_stock_master` `d` ON((`d`.`stock_id` = `a`.`stock_id`)))
JOIN `customer_discount_items` `e` ON(((`e`.`item_id` = `d`.`category_id`) AND (`c`.`debtor_no` = `e`.`customer_id`))))
WHERE ((`a`.`debtor_trans_type` = 10) AND (`b`.`reference` <> 'auto') AND (`b`.`type` = 10) AND (`a`.`quantity` <> 0)) 

  $where 

GROUP BY `b`.`reference`,`a`.`stock_id`
ORDER BY `a`.`id` DESC";

        return $sql;

    }
}
