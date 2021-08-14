<?php

namespace App;

use App\Http\Requests\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class InvoiceReportModel extends Model
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
//            if (!empty($filters['customer'])) {
//                $where .= " and  b.debtor_no =".$filters['customer'];
//            }
            if (!empty($filters['employee'])) {
                $where .= " and  a.created_by =".$filters['employee'];
            }
            if (!empty($filters['payment_status'])) {
                if ($filters['payment_status'] == 1)
                    $where .= " and  ROUND(b.alloc,2) >= (ROUND(ov_amount+ov_gst,2))";
                if ($filters['payment_status'] == 2)
                    $where .= " and  b.alloc = 0";
                else if ($filters['payment_status'] == 3)
                    $where .= " and  (ROUND(b.alloc,2) < (ROUND(b.ov_amount+b.ov_gst,2)) and ROUND(b.alloc,2) <> 0)";

                else if ($filters['payment_status'] == 4)
                    $where .= " and  ROUND(b.alloc,2) < ROUND(b.ov_amount+b.ov_gst,2) ";

            }


            if (!empty($filters['customer_ref'])) {
                $where .= " and  b.customer_ref LIKE '%".$filters['customer_ref']."%'";
            }


        }

        $sql = "SELECT trans_no,
invoice_no,
stock_id,
description,
customer_ref,
unit_price,
unit_tax,
(quantity-descQty) AS quantity,descQty,creditNoteAmntSum,(invoice_amount-creditNoteAmntSum) AS invoice_amount
,discount_percent,discount_amount,govt_fee,bank_service_charge
,bank_service_charge_vat,pf_amount,transaction_id,user_commission,created_by,updated_by,customer_name,debtor_no,
reference_customer,created_employee,transaction_date,(alloc-creditNoteAmntSum) AS alloc,

(CASE WHEN (ROUND((alloc-creditNoteAmntSum),2) >= (ROUND((invoice_amount-creditNoteAmntSum),2))) THEN '1' 
         WHEN ((alloc-creditNoteAmntSum) = 0) THEN '2' WHEN ((alloc-creditNoteAmntSum) < (invoice_amount-creditNoteAmntSum)) THEN '3' END)
         AS payment_status

FROM 


(SELECT b.trans_no,`b`.`reference` AS `invoice_no`,`a`.`stock_id` AS `stock_id`,`a`.`description` AS `description`,
        b.customer_ref,
        `a`.`unit_price` AS `unit_price`,`a`.`unit_tax` AS `unit_tax`,a.quantity AS `quantity`,

        (`b`.`ov_amount` + `b`.`ov_gst`) AS `invoice_amount`,
			`a`.`discount_percent` AS `discount_percent`,
        `a`.`discount_amount` AS `discount_amount`,`a`.`govt_fee` AS `govt_fee`,`a`.`bank_service_charge` AS `bank_service_charge`,
        `a`.`bank_service_charge_vat` AS `bank_service_charge_vat`,`a`.`pf_amount` AS `pf_amount`,`a`.`transaction_id` AS `transaction_id`,
        `a`.`user_commission` AS `user_commission`,`a`.`created_by` AS `created_by`,`a`.`updated_by` AS `updated_by`,
        `c`.`name` AS `customer_name`,`c`.`debtor_no` AS `debtor_no`,`b`.`display_customer` AS `reference_customer`,
        `0_users`.`user_id` AS `created_employee`,`b`.`tran_date` AS `transaction_date`, b.alloc,
         
         /*(CASE WHEN (ROUND(`b`.`alloc`,2) >= (ROUND(`b`.`ov_amount` + `b`.`ov_gst`,2))) THEN '1' 
         WHEN (`b`.`alloc` = 0) THEN '2' WHEN (`b`.`alloc` < (`b`.`ov_amount` + `b`.`ov_gst`)) THEN '3' END) AS `payment_status`,*/
         
			(SELECT IFNULL(SUM(p.quantity),0)
			FROM 0_debtor_trans_details p
			WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11) AS descQty,

			(SELECT IFNULL(SUM(`b`.`ov_amount` + `b`.`ov_gst`),0)
			FROM 0_debtor_trans_details p
			LEFT JOIN `0_debtor_trans` `b` ON `b`.`trans_no` = `p`.`debtor_trans_no` AND p.debtor_trans_type=b.`type` 
			WHERE p.src_id=a.id AND `b`.`type` = 11) AS creditNoteAmntSum
      
        
        FROM `0_debtor_trans_details` `a`
        LEFT JOIN `0_debtor_trans` `b` ON `b`.`trans_no` = `a`.`debtor_trans_no`
        LEFT JOIN `0_debtors_master` `c` ON `c`.`debtor_no` = `b`.`debtor_no` 
        LEFT JOIN `0_users` ON `0_users`.`id` = `a`.`created_by`
        WHERE  `a`.`debtor_trans_type` IN (10) AND  `b`.`reference` <> 'auto'  AND `b`.`type` IN (10)  AND  `a`.`quantity` <> 0 
        AND (a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
		  FROM 0_debtor_trans_details p
		  WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11)) <>0
        
         $where
        
        GROUP BY `b`.`reference`
        ORDER BY `b`.`trans_no` ASC) AS MyTable";



//        dd($sql);


       /* $sql = "SELECT b.trans_no,`b`.`reference` AS `invoice_no`,`a`.`stock_id` AS `stock_id`,`a`.`description` AS `description`,
        b.customer_ref,
        `a`.`unit_price` AS `unit_price`,`a`.`unit_tax` AS `unit_tax`,(a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
FROM 0_debtor_trans_details p
WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11)) AS `quantity`,

        (`b`.`ov_amount` + `b`.`ov_gst`)-(SELECT IFNULL(SUM(`b`.`ov_amount` + `b`.`ov_gst`),0)
FROM 0_debtor_trans_details p
LEFT JOIN `0_debtor_trans` `b` ON `b`.`trans_no` = `p`.`debtor_trans_no`
WHERE p.src_id=a.id AND `b`.`type` = 11) AS `invoice_amount`,
`a`.`discount_percent` AS `discount_percent`,
        `a`.`discount_amount` AS `discount_amount`,`a`.`govt_fee` AS `govt_fee`,`a`.`bank_service_charge` AS `bank_service_charge`,
        `a`.`bank_service_charge_vat` AS `bank_service_charge_vat`,`a`.`pf_amount` AS `pf_amount`,`a`.`transaction_id` AS `transaction_id`,
        `a`.`user_commission` AS `user_commission`,`a`.`created_by` AS `created_by`,`a`.`updated_by` AS `updated_by`,
        `c`.`name` AS `customer_name`,`c`.`debtor_no` AS `debtor_no`,`b`.`display_customer` AS `reference_customer`,
        `0_users`.`user_id` AS `created_employee`,`b`.`tran_date` AS `transaction_date`, b.alloc,
         
        (CASE WHEN (ROUND(`b`.`alloc`,2) >= (ROUND(`b`.`ov_amount` + `b`.`ov_gst`,2))) THEN '1' 
        WHEN (`b`.`alloc` = 0) THEN '2' WHEN (`b`.`alloc` < (`b`.`ov_amount` + `b`.`ov_gst`)) THEN '3' END) AS `payment_status`
      
        
        FROM `0_debtor_trans_details` `a`
        LEFT JOIN `0_debtor_trans` `b` ON `b`.`trans_no` = `a`.`debtor_trans_no`
        LEFT JOIN `0_debtors_master` `c` ON `c`.`debtor_no` = `b`.`debtor_no` 
        LEFT JOIN `0_users` ON `0_users`.`id` = `a`.`created_by`
        WHERE  `a`.`debtor_trans_type` IN (10) AND  `b`.`reference` <> 'auto'  AND `b`.`type` IN (10)  AND  `a`.`quantity` <> 0 
        AND (a.quantity-(SELECT IFNULL(SUM(p.quantity),0)
FROM 0_debtor_trans_details p
WHERE p.src_id=a.id AND `p`.`debtor_trans_type` = 11)) <>0
        
        $where 
        
        GROUP BY `b`.`reference`
        ORDER BY `b`.`trans_no` ASC";*/
 //print_r($sql);
        return $sql;

    }



}
