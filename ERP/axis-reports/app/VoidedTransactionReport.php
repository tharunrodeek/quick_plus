<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VoidedTransactionReport extends Model
{

    /**
     * @param array $filters
     * @return string
     * Generate SQL for Voided Transaction report
     */
    public function getSQL($filters = [])
    {
        $where = "";
        if (!empty($filters)) {
            if (!empty($filters['transaction_done_by'])) {
                $where .= " and  d.id = " . $filters['transaction_done_by'];
            }

            if(!empty($filters['void_type'])) {
                if($filters['void_type'] == 1) {
                    $where .= " and a.memo_ = 'EDITED_INVOICE'";
                }else{
                    $where .= " and a.memo_ <> 'EDITED_INVOICE'";
                }

            }

            if (!empty($filters['reference'])) {
                $where .= " and  c.reference = " . $filters['reference'];
            }


            if (!empty($filters['voided_by'])) {
                $where .= " and  b.id = " . $filters['voided_by'];
            }


        }

        $sql = "SELECT `c`.`reference` AS `reference`,`a`.`date_` AS `voided_date`,`a`.`trans_date` AS `trans_date`,
`a`.`amount` AS `amount`,
`a`.`memo_` AS `memo_`,`b`.`user_id` AS `voided_by`,`d`.`user_id` AS `transaction_done_by`,
(CASE WHEN (`a`.`type` = 10) THEN 'Sales Invoice' WHEN (`a`.`type` = 12) THEN 'Customer Payment' 
WHEN (`a`.`type` = 0) THEN 'Journal' END) AS `type`
FROM  `0_voided` `a`
LEFT JOIN `0_users` `b` ON `b`.`id` = `a`.`created_by` 
LEFT JOIN `0_refs` `c` ON `c`.`id` = `a`.`id` AND `c`.`type` = `a`.`type` 
LEFT JOIN `0_users` `d` ON `d`.`id` = `a`.`transaction_created_by` 
WHERE `a`.`type` IN (1,0,10,12) $where  ";


        return $sql;

    }

}
