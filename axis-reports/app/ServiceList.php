<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ServiceList extends Model
{
    /**
     * @param array $filters
     * @return string
     * Generate SQL for Service List
     */
    public function getSQL($filters = [])
    {
        $where = "";
        if (!empty($filters)) {
            if (!empty($filters['category_id'])) {
                $where .= " and  b.category_id = " . $filters['category_id'];
            }
        }

        $sql = "SELECT `a`.`stock_id` AS `stock_id`,`a`.`description` AS `item_description`,
 `a`.`long_description` AS `long_description`,`b`.`description` AS `category_name`,`c`.`price` AS `service_charge`,
 `a`.`govt_fee` AS `govt_fee`,`a`.`pf_amount` AS `pf_amount`,
 `a`.`bank_service_charge` AS `bank_service_charge`,`a`.`bank_service_charge_vat` AS `bank_service_charge_vat`,
 `a`.`commission_loc_user` AS `commission_loc_user`,`a`.`commission_non_loc_user` AS `commission_non_loc_user`, 
 
govt_acc.account_name as govt_account_name,
      cog_acc.account_name as cog_account_name,
      sales_acc.account_name as sales_account_name,
       
 CASE WHEN a.tax_type_id = 1 THEN (`c`.`price`+`a`.`pf_amount`)*.05 ELSE '0' END as tax, 
 
 (IFNULL(`c`.`price`,0)+ IFNULL(`a`.`govt_fee`,0)+ IFNULL(`a`.`pf_amount`,0)+ IFNULL(a.bank_service_charge,0)+ IFNULL(a.bank_service_charge_vat,0)) AS total_amount
FROM `0_stock_master` `a`
LEFT JOIN `0_stock_category` `b` ON `b`.`category_id` = `a`.`category_id` 

LEFT JOIN `0_prices` `c` ON `c`.`stock_id` = `a`.`stock_id` AND `c`.`sales_type_id` = 1 

LEFT JOIN 0_chart_master govt_acc on govt_acc.account_code=a.govt_bank_account 
        LEFT JOIN 0_chart_master cog_acc on cog_acc.account_code=a.cogs_account 
        LEFT JOIN 0_chart_master sales_acc on sales_acc.account_code=a.sales_account 

INNER JOIN 0_item_codes d on d.stock_id = a.stock_id 

WHERE 1=1 AND a.inactive <> 1 $where  ";

        return $sql;

    }


}
