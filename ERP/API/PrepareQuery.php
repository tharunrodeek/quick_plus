<?php
/**
 * Class PrepareQuery
 * Created By : Bipin
 */
//$path_to_root = "..";
//include_once($path_to_root . "/sales/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");
include_once($path_to_root . "/admin/db/shipping_db.inc");
include_once($path_to_root . "/themes/daxis/kvcodes.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/admin/db/company_db.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
include_once($path_to_root . "/API/API_Call.php");
class PrepareQuery {
    public static function ServiceReport($filters) {
        $where = "";
        if(!empty($filters)) {
            if (!empty($filters['invoice_number'])) {
                $where .= " and  dt.reference = '{$filters['invoice_number']}'";
            }
            if (!empty($filters['date_from'])) {
                $date_from = date2sql($filters['date_from']);
                $where .= " and  dt.tran_date >= '$date_from'";
            }
            if (!empty($filters['date_to'])) {
                $date_to = date2sql($filters['date_to']);
                $where .= " and  dt.tran_date <= '$date_to'";
            }

            if(!isset($filters['customer'])) {
                $filters['customer'] = [];
            }

            if (!empty(array_filter($filters['customer']))) {

                $filters['customer'] = implode(",",array_filter($filters['customer']));

                $where .= " and  dt.debtor_no in (".$filters['customer'].")";
            }

            if(!user_check_access('SA_SRVREPORTALL'))
            {
                $where .= " and  dt_detail.created_by =".$_SESSION['wa_current_user']->user;
            }
            else
            {
                if (!empty($filters['employee'])) {
                    $where .= " and  dt_detail.created_by =".$filters['employee'];
                }
            }

            if (!empty($filters['salesman'])) {
                $where .= " and  i.salesman_code =".$filters['salesman'];
            }

            if (!empty($filters['display_customer'])) {
                $where .= " and  dt.display_customer LIKE '%" . $filters['display_customer']."%'";
            }

            if (!empty($filters['payment_status'])) {
                if ($filters['payment_status'] == 1) //Fully Paid
                    $where .= " and  dt.alloc >= (dt.ov_amount+dt.ov_gst+dt.ov_freight+dt.ov_freight_tax+dt.ov_discount)";
                if ($filters['payment_status'] == 2) //Not Paid
                    $where .= " and  dt.alloc = 0";
                else if ($filters['payment_status'] == 3) //Partially Paid
                    $where .= " and  (dt.alloc < (dt.ov_amount+dt.ov_gst+dt.ov_freight+dt.ov_freight_tax+dt.ov_discount) and dt.alloc <> 0)";
            }

            if (!empty($filters['category'])) {
                $where .= " and  stk.category_id =".$filters['category'];
            }

            if (!empty($filters['service'])) {
                $where .= " and  dt_detail.stock_id ='".$filters['service']."'";
            }

            if (!empty($filters['transaction_id'])) {
                $where .= " and  dt_detail.transaction_id LIKE '%" . $filters['transaction_id'] . "%'";
            }

            if (!empty($filters['customer_mobile'])) {
                $where .= " and  dt.customer_mobile like '%".trim($filters['customer_mobile'])."%'";
            }

            if (!empty($filters['customer_email'])) {
                $where .= " and  dt.customer_email like '%".trim($filters['customer_email'])."%'";
            }


            if(isset($filters['transaction_status'])) {

                if ($filters['transaction_status'] == 1) {
                    $where .= " and  dt_detail.transaction_id <> ''";
                }

                if ($filters['transaction_status'] == 2) {
                    $where .= " and  dt_detail.transaction_id = ''";
                }
            }

            if (!empty($filters['application_id'])) {
                $where .= " and dt_detail.application_id LIKE '%{$filters['application_id']}%'";
            }

            if (!empty($filters['invoice_type'])) {
                $where .= " and  dt.invoice_type ='".$filters['invoice_type']."'";
            }


        }

        $sql = (
            "SELECT 
                dt.trans_no,
                dt_detail.stock_id,
                dt_detail.description, 
                dt_detail.unit_price,
                dt.reference AS invoice_number,
                DATE_FORMAT(dt.tran_date,'%d-%m-%Y') AS tran_date,
                dt_detail.discount_amount,
                dt_detail.user_commission,
                dt_detail.created_by,
                IFNULL(cust_disc_items.customer_commission, 0) * dt_detail.quantity AS customer_commission,
                IFNULL(reward.reward_amount,0) AS reward_amount,
                CASE 
                    WHEN ROUND(dt.alloc) >= ROUND(dt.ov_amount + dt.ov_gst) THEN 'Fully Paid' 
                    WHEN dt.alloc = 0 THEN 'Not Paid' 
                    WHEN ROUND(dt.alloc) < ROUND(dt.ov_amount + dt.ov_gst) THEN 'Partially Paid' 
                END AS payment_status,
                ROUND(
                    (
                        dt_detail.govt_fee 
                        + dt_detail.bank_service_charge
                        + dt_detail.bank_service_charge_vat
                        + dt_detail.pf_amount
                    ) * (dt_detail.quantity),
                    2
                ) AS total_govt_fee,
                ROUND(
                    (
                        dt_detail.unit_price
                        + dt_detail.govt_fee
                        + dt_detail.bank_service_charge
                        + dt_detail.bank_service_charge_vat
                        + dt_detail.unit_tax
                        + dt_detail.extra_service_charge
                        - dt_detail.discount_amount
                    ) * dt_detail.quantity,
                    2
                ) AS line_total,
                ROUND(dt.ov_amount + dt.ov_gst, 2) AS invoice_total,
                ROUND(dt_detail.unit_price * dt_detail.quantity, 2) AS total_service_charge,
                dt.debtor_no,
                dt.display_customer,
                dt_detail.unit_tax,
                dt_detail.unit_tax * dt_detail.quantity AS total_tax,
                dt_detail.quantity,
                dt_detail.discount_amount * dt_detail.quantity AS line_discount_amount,
                dt_detail.govt_fee,
                dt_detail.govt_bank_account,
                dt_detail.bank_service_charge,
                dt_detail.bank_service_charge_vat,
                dt_detail.pf_amount,
                dt_detail.transaction_id,
                dt_detail.ed_transaction_id,
                dt_detail.application_id,
                dt_detail.user_commission * dt_detail.quantity AS employee_commission,
                dt_detail.ref_name,
                dt_detail.created_at,
                dt.customer_mobile,
                dt.invoice_type,
                dt.customer_email,
                i.salesman_name,
                dt_detail.extra_service_charge 
            FROM 0_debtor_trans_details dt_detail 
                LEFT JOIN 0_debtor_trans dt 
                    ON dt.trans_no = dt_detail.debtor_trans_no
                        AND dt.type=10
                LEFT JOIN 0_debtors_master dm ON dm.debtor_no = dt.debtor_no
                LEFT JOIN 0_salesman i ON dm.salesman_id = i.salesman_code
                LEFT JOIN 0_stock_master stk ON stk.stock_id = dt_detail.stock_id 
                LEFT JOIN 0_stock_category cat ON cat.category_id = stk.category_id 
                LEFT JOIN customer_discount_items cust_disc_items 
                    ON cust_disc_items.item_id = stk.category_id 
                        AND dt.`debtor_no` = cust_disc_items.customer_id 
                LEFT JOIN customer_rewards reward ON reward.detail_id = dt_detail.id 
            WHERE dt_detail.debtor_trans_type = 10 
                AND dt_detail.quantity <> 0 
                AND dt.ov_amount <> 0
                $where "
        );

        // dd($sql);
        return $sql;
    }
}