<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ServiceReport extends Model
{
    protected $table = "0_debtor_trans";

    /**
     * @param array $filters
     * @return string
     * Generate SQL for Invoice report
     */
    public function getSQL($filters = []) {

        $where = "";
        if(!empty($filters)) {
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

            if(!isset($filters['customer'])) {
                $filters['customer'] = [];
            }

            if (!empty(array_filter($filters['customer']))) {

                $filters['customer'] = implode(",",array_filter($filters['customer']));

                $where .= " and  b.debtor_no in (".$filters['customer'].")";
            }
            if (!empty($filters['employee'])) {
                $where .= " and  a.created_by =".$filters['employee'];
            }

            if (!empty($filters['sales_man_id'])) {
                $where .= " and  i.salesman_code =".$filters['sales_man_id'];
            }

            if (!empty($filters['display_customer'])) {
                $where .= " and  b.display_customer LIKE '%" . $filters['display_customer']."%'";
            }

            if (!empty($filters['payment_status'])) {
                if ($filters['payment_status'] == 1)
                    $where .= " and  ROUND(b.alloc,2) >= (ROUND(b.ov_amount+b.ov_gst,2))";
                if ($filters['payment_status'] == 2)
                    $where .= " and  b.alloc = 0";
                else if ($filters['payment_status'] == 3)
                    $where .= " and  (ROUND(b.alloc,2) < (ROUND(b.ov_amount+b.ov_gst,2)) and b.alloc <> 0)";
            }

            if (!empty($filters['category'])) {
                $where .= " and  d.category_id =".$filters['category'];
            }

            if (!empty($filters['service'])) {
                $where .= " and  a.stock_id ='".$filters['service']."'";
            }

            if (!empty($filters['transaction_id'])) {
                $where .= " and  a.transaction_id ='".$filters['transaction_id']."'";
            }

            if (!empty($filters['work_location'])) {
                $where .= " and  a.work_location ='".$filters['work_location']."'";
            }

        }


        $sql = "SELECT IFNULL(`e`.`reward_amount`,0) AS `reward_amount`, g.description AS category_name,

i.salesman_name as salesman_name,

IFNULL((((`a`.`unit_price` * `f`.`customer_commission`) / 100) * (a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
FROM 0_debtor_trans_details p
WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11))),0) AS `total_customer_commission`,

`b`.`reference` AS `invoice_no`,`a`.`stock_id` AS `stock_id`,`d`.`category_id` AS `category_id`,
LEFT(`a`.`description`,60) AS `description`,`d`.`description` AS `service_eng_name`,

`a`.`unit_price` AS `unit_price`,(`a`.`unit_price` * (a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
FROM 0_debtor_trans_details p
WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11))) AS `total_price`,

(`a`.`unit_tax` * (a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
FROM 0_debtor_trans_details p
WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11))) AS `total_tax`,(`a`.`govt_fee` * (a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
FROM 0_debtor_trans_details p
WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11))) AS `total_govt_fee`,

`a`.`unit_tax` AS `unit_tax`,(a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
FROM 0_debtor_trans_details p
WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11))  AS `quantity`,

((((((`a`.`unit_price` + `a`.`govt_fee`) + `a`.`bank_service_charge`) + `a`.`bank_service_charge_vat`) + 
`a`.`unit_tax`) * (a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
FROM 0_debtor_trans_details p
WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11))) - (`a`.`discount_amount` * (a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
FROM 0_debtor_trans_details p
WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11)))) AS `invoice_amount`,
(((((`a`.`unit_price` * (a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
FROM 0_debtor_trans_details p
WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11))) - (`a`.`discount_amount` * (a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
FROM 0_debtor_trans_details p
WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11)))) - 
IFNULL(`e`.`reward_amount`,0)) - 
IFNULL((((`a`.`unit_price` * `f`.`customer_commission`) / 100) * (a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
FROM 0_debtor_trans_details p
WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11))),0)) - 
(`a`.`pf_amount` * (a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
FROM 0_debtor_trans_details p
WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11)))) AS `net_service_charge`,

(`a`.`discount_percent` * 100) AS `discount_percent`,`a`.`discount_amount` AS `discount_amount`,`a`.`govt_fee` AS `govt_fee`,
`a`.`bank_service_charge` AS `bank_service_charge`,`a`.`bank_service_charge_vat` AS `bank_service_charge_vat`,
`a`.`pf_amount` AS `pf_amount`, IF((`a`.`transaction_id` <> ''), CONCAT('\"',`a`.`transaction_id`,'\"'), NULL) AS `transaction_id`,
a.ed_transaction_id,

a.application_id,b.customer_ref,j.account_name as govt_bank_account,
k.account_name as stock_cogs_account,l.account_name as stock_sales_account,


`a`.`user_commission` AS `user_commission`,`a`.`created_by` AS `created_by`,`a`.`updated_by` AS `updated_by`,a.work_location,
`c`.`name` AS `customer_name`,`b`.`display_customer` AS `reference_customer`,`0_users`.`user_id` AS `created_employee`,
`b`.`tran_date` AS `transaction_date`,
(CASE WHEN (ROUND(`b`.`alloc`,2) >= (ROUND(`b`.`ov_amount` + `b`.`ov_gst`,2))) THEN '1' 
WHEN (`b`.`alloc` = 0) THEN '2' WHEN (`b`.`alloc` < (`b`.`ov_amount` + `b`.`ov_gst`)) THEN '3' END) AS `payment_status`,
a.ref_id,a.ref_name 
FROM `0_debtor_trans_details` `a`
LEFT JOIN `0_debtor_trans` `b` ON `b`.`trans_no` = `a`.`debtor_trans_no` 
LEFT JOIN `0_debtors_master` `c` ON `c`.`debtor_no` = `b`.`debtor_no` 
LEFT JOIN `0_users` ON `0_users`.`id` = `a`.`created_by` 
LEFT JOIN `0_stock_master` `d` ON `d`.`stock_id` = `a`.`stock_id` 
LEFT JOIN `0_stock_category` `g` ON `g`.`category_id` = `d`.`category_id` 
LEFT JOIN `customer_rewards` `e` ON (`e`.`trans_no` = `b`.`trans_no`) AND (`e`.`trans_type` = 10) AND (e.stock_id = a.stock_id) 
LEFT JOIN `customer_discount_items` `f` ON (`f`.`item_id` = `d`.`category_id`) AND (`c`.`debtor_no` = `f`.`customer_id`)

LEFT JOIN 0_cust_branch h on h.branch_code=b.branch_code 
LEFT JOIN 0_salesman i on i.salesman_code=h.salesman  


LEFT JOIN 0_chart_master j on j.account_code = a.govt_bank_account 
LEFT JOIN 0_chart_master k on k.account_code = d.cogs_account 
LEFT JOIN 0_chart_master l on l.account_code = d.sales_account
WHERE (`a`.`debtor_trans_type` = 10) AND (`b`.`reference` <> 'auto') AND (`b`.`type` = 10) AND (`a`.`quantity` <> 0) 
AND (a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
FROM 0_debtor_trans_details p
WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11)) <>0

$where 

GROUP BY `b`.`reference`,`a`.`stock_id`,`a`.`id`
ORDER BY `a`.`id` ASC ";




        return $sql;

    }

}
