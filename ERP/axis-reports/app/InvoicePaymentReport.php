<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoicePaymentReport extends Model
{
    /**
     * @param array $filters
     * @return string
     * Generate SQL for Service List
     */
    public function getSQL($filters = [])
    {
        $where = "";

        if (!empty($filters['user'])) {
            $where .= " AND g.id = " . $filters['user'];
        }
        if (!empty($filters['customer'])) {
            $where .= " AND e.debtor_no = " . $filters['customer'];
        }
        if (!empty($filters['payment_method'])) {
            $where .= " AND a.payment_method = '" . $filters['payment_method'] . "'";
        }

        if (!empty($filters['tran_date_from'])) {
            $date_from = date2sq1($filters['tran_date_from']);
            $where .= " and  a.tran_date >= '$date_from'";
        }
        if (!empty($filters['tran_date_to'])) {
            $date_to = date2sq1($filters['tran_date_to']);
            $where .= " and  a.tran_date <= '$date_to'";
        }

        if (!empty($filters['bank'])) {
            $where .= " AND d.id = " . $filters['bank'];
        }

        if (!empty($filters['receipt_no'])) {
            $where .= " AND a.reference = '" . $filters['receipt_no']."'";
        }

        $extra_where = "";
        if (!empty($filters['invoice_no'])) {
            $extra_where .= " AND invoice_numbers IN ('" . $filters['invoice_no']."')";
        }


        $sql = "select * from (select a.tran_date as date_alloc,a.reference As payment_ref, 

(SELECT GROUP_CONCAT(0_debtor_trans.reference SEPARATOR ', ') from 0_cust_allocations  
left join 0_debtor_trans on 0_debtor_trans.trans_no = 0_cust_allocations.trans_no_to 
where trans_no_from = a.trans_no and 0_debtor_trans.type=10   
 ) as invoice_numbers,

ROUND(a.alloc,2) as gross_payment,
ROUND((IFNULL(a.ov_discount,0)),2) as reward_amount,
ROUND((a.alloc-(IFNULL(a.ov_discount,0))),2) As net_payment,
d.bank_account_name,
e.name as customer,
g.user_id,
a.payment_method 

 from 0_debtor_trans a 
 
 LEFT JOIN `customer_rewards` b ON `b`.`trans_no` = `a`.`trans_no` and b.trans_type = 12  
 LEFT JOIN 0_bank_trans c on c.trans_no=a.trans_no and c.`type`=12  
 left join 0_bank_accounts d on d.id = c.bank_act 
 left join 0_debtors_master e on e.debtor_no = a.debtor_no 
 left join (SELECT * from 0_audit_trail group by type,trans_no) f on f.trans_no = a.trans_no and f.type=12 
 left join 0_users g on g.id = f.user 
 
 where a.type=12 and a.alloc <> 0  $where  ) as MyTable WHERE 1=1 $extra_where";

        return $sql;

    }

}
