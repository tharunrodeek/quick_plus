<?php

/**
 * Class API_Call
 * Created By : Bipin
 */

include_once("AxisPro.php");
include_once("PrepareQuery.php");
include_once("AxisNotification.php");

Class API_Call
{

    /**
     * @param $data
     * @param string $format
     * @return mixed
     * Return HTTP Response
     */
    public function HttpResponse($data, $format = 'json')
    {
        if ($format == 'json') {
            echo json_encode($data);
            exit();
        }
        return $data;

    }

    /**
     * @return array
     * Get sales report
     */
    public function daily_sales()
    {
        try {

            $sql = "select tran_date,ROUND(sum(ov_amount+ov_gst),2) amount from 0_debtor_trans 
            where type=10 group by tran_date order by tran_date ASC limit 10";

            $result = db_query($sql);

            $daily_sales = [];
            while ($row = db_fetch($result)) {
                $daily_sales[$row['tran_date']] = $row['amount'];
            }

            return AxisPro::SendResponse($daily_sales);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @return array
     * Get employee wise service count
     */
    public function employee_service_count()
    {
        try {
            $sql = "select users.real_name,sum(dt_detail.quantity) qty from 0_debtor_trans_details dt_detail 
            left join 0_users users on users.id=dt_detail.created_by
            INNER JOIN 0_stock_master AS s ON s.stock_id=dt_detail.stock_id
            INNER JOIN 0_stock_category AS c ON c.category_id=s.category_id
            WHERE c.category_id IN (60,6,2,3,4,5)";

            if (!empty($_GET['fromdate']) || !empty($_GET['to_date'])) {
                $sql .= " AND DATE(dt_detail.created_at)>='" . date2sql($_GET['fromdate']) . "' AND  DATE(dt_detail.created_at)<='" . date2sql($_GET['to_date']) . "'";
            } else {
                $sql .= " AND DATE(dt_detail.created_at)>='" . date('Y-m-d') . "' AND  DATE(dt_detail.created_at)<='" . date('Y-m-d') . "'";
            }


            $sql .= "
            group by dt_detail.created_by order by qty desc limit 5";
            // echo $sql;

            $result = db_query($sql);

            $employee_service_count = [];
            while ($row = db_fetch($result)) {
                $first_name = explode(" ", $row['real_name']);
                $employee_service_count[$first_name[0]] = $row['qty'];
            }


            return AxisPro::SendResponse($employee_service_count);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @return array
     * Get top5 selling category
     */
    public function top_five_category()
    {

        try {
            $sql = "select sc.description, SUM(dt_detail.quantity) qty from 0_debtor_trans_details dt_detail
            left join 0_stock_master sm on sm.stock_id=dt_detail.stock_id
            left join 0_stock_category sc on sc.category_id=sm.category_id
            where sc.category_id IN (60,6,2,3,4,5) ";

            if (!empty($_GET['f_date']) || !empty($_GET['t_date'])) {
                $sql .= " AND DATE(dt_detail.created_at)>='" . date2sql($_GET['f_date']) . "' AND  DATE(dt_detail.created_at)<='" . date2sql($_GET['t_date']) . "'";
            } else {
                $sql .= " AND DATE(dt_detail.created_at)>='" . date('Y-m-d') . "' AND  DATE(dt_detail.created_at)<='" . date('Y-m-d') . "'";
            }
            $sql .= " group by sc.category_id order by qty desc limit 5";
//echo $sql;
            $result = db_query($sql);

            $top_five_category = [];
            while ($row = db_fetch($result)) {
                $top_five_category[$row['description']] = $row['qty'];
            }

            return AxisPro::SendResponse($top_five_category);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    /**
     * @return array
     * List out todays invoices
     */
    public function todays_invoices()
    {

        try {
            $status = isset($_GET['status']) ? $_GET['status'] : '';

            $show_only_pending = isset($_GET['show_only_pending']) ? $_GET['show_only_pending'] : '';

            $today = Today();
            $today = date2sql($today);


            $dim_id = $_GET['dim_id'];


            $sql = "SELECT * FROM (SELECT `b`.`reference` AS `invoice_no`,
                ROUND((`b`.`ov_amount` + `b`.`ov_gst`),2) AS `invoice_amount`, b.order_,b.trans_no,
                `a`.`created_by` AS `created_by`,b.payment_flag,b.display_customer,c.name as customer_name, 
                `0_users`.`user_id` AS `created_employee`,`b`.`tran_date` AS `transaction_date`, b.dimension_id, 
                (CASE WHEN (ROUND(`b`.`alloc`) >= ROUND(`b`.`ov_amount` + `b`.`ov_gst`)) THEN '1' 
                    WHEN (`b`.`alloc` = 0) THEN '2' WHEN (ROUND(`b`.`alloc`) < ROUND(`b`.`ov_amount` + `b`.`ov_gst`)) THEN '3' END) AS `payment_status`
FROM (((`0_debtor_trans_details` `a`
    LEFT JOIN `0_debtor_trans` `b` ON((`b`.`trans_no` = `a`.`debtor_trans_no`)))
LEFT JOIN `0_debtors_master` `c` ON((`c`.`debtor_no` = `b`.`debtor_no`)))
LEFT JOIN `0_users` ON((`0_users`.`id` = `a`.`created_by`)))
WHERE ((`a`.`debtor_trans_type` = 10) AND (`b`.`reference` <> 'auto') AND (`b`.`type` = 10) AND (`a`.`quantity` <> 0))  

AND b.tran_date = '$today' 

GROUP BY `b`.`reference`
ORDER BY `b`.`trans_no` DESC) As MyTable WHERE 1=1 ";


            if (!empty($status)) {
                $sql .= " AND payment_status = $status";
            }

            if (!empty($dim_id))
                $sql .= " AND dimension_id = $dim_id ";

            if ($show_only_pending == '1') {

                $sql .= " AND payment_status <> 1 ";

            }

            /** IF Admin / Accountant : Shows only not paid and partially paid invoices */
            if (in_array($_SESSION['wa_current_user']->access, [9, 2, 13])) {
//                $sql .= " AND payment_status <> 1 ";
            } else {
                $sql .= " AND created_by = " . $_SESSION['wa_current_user']->user;
            }

            $sql .= " ORDER BY payment_status ASC, trans_no ASC";

            $result = db_query($sql, "Transactions could not be calculated");

//            pp($sql);

            $return_result = [];
            $i = 0;
            while ($row = db_fetch_assoc($result)) {

                $class = 'class="oddrow"';
                if ($i % 2 == 0)
                    $class = 'class="evenrow"';

                $payment_status = "Not Paid";
                if ($row['payment_status'] == '1') {
                    $payment_status = 'Fully Paid';
                }
                if ($row['payment_status'] == '3') {
                    $payment_status = 'Partially Paid';
                }

                $update_transaction_id_link = "sales/customer_invoice.php?ModifyInvoice=" . $row['trans_no'];

                if ($row['payment_flag'] != 0 && $row['payment_flag'] != 3) {//TASHEEL
                    $update_transaction_id_link .= "&is_tadbeer=1&show_items=ts";
                }

                if ($row['payment_flag'] == 4 || $row['payment_flag'] == 5) {//TADBEER
                    $update_transaction_id_link .= "&is_tadbeer=1&show_items=tb";
                }

                $row['payment_status'] = $payment_status;
                $row['edit_trans_id_link'] = $update_transaction_id_link;

                array_push($return_result, $row);

            }

            return AxisPro::SendResponse($return_result);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @return array
     * Get category wise sales count
     */
    public function category_sales_count()
    {

        try {
            $sql = "select sc.description, SUM(dt_detail.quantity) qty from 0_debtor_trans_details dt_detail
            left join 0_stock_master sm on sm.stock_id=dt_detail.stock_id
            left join 0_stock_category sc on sc.category_id=sm.category_id
              group by sc.category_id";

            $result = db_query($sql);

            $category_sales_count = [];
            while ($row = db_fetch($result)) {
                $category_sales_count[$row['description']] = $row['qty'];
            }

            return AxisPro::SendResponse($category_sales_count);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    /**
     * @return array
     * Get category wise sales report
     */
    public function category_sales_report()
    {

        try {

            $date = null;

            $sql = "select c.description,sum(a.quantity) as inv_count,ROUND(sum(a.quantity*a.unit_price),2) as service_charge from 0_debtor_trans_details a 
            left join 0_stock_master b on b.stock_id=a.stock_id 
            left join 0_stock_category c on c.category_id=b.category_id 
            left join 0_debtor_trans d on d.trans_no = a.debtor_trans_no and d.`type`=10 where a.debtor_trans_type=10";

            if ($date) {
                $sql .= " and d.tran_date='$date' ";
            }

            $sql .= " group by b.category_id";
            $result = db_query($sql, "Transactions could not be calculated");

            $category_sales_report = [];
            while ($row = db_fetch($result)) {
                $category_sales_report[] = $row;
            }

            return AxisPro::SendResponse($category_sales_report);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @return array
     * Get the bank balances
     */
    public function bank_balances()
    {

        try {
            $today = Today();
            $today = date2sql($today);


            $sql = "select ROUND(ifnull(sum(gl.amount),0),2) amount from 0_gl_trans gl 
            inner join 0_chart_master chart on chart.account_code=gl.account and chart.account_type in (19,191)
            where gl.tran_date<='$today'";


            $result = db_fetch(db_query($sql));
            $acc_rcvbl_total = $result['amount'];


            $sql = "select b.account_name as bank_account_name,ROUND(sum(amount),2) balance,
 b.account_code 
 from 0_gl_trans a 
            left join 0_chart_master b on b.account_code = a.account 
            inner join 0_bank_accounts c on c.account_code=b.account_code 
            where a.tran_date <= '$today' 
            group by account order by b.account_name";

            $result = db_query($sql);

            $bank_balances = [];


            while ($row = db_fetch($result)) {

                if (in_array($_SESSION["wa_current_user"]->access, [3])) {

                    if (in_array($row['account_code'], [113002, 113004, 1130001])) {
                        $bank_balances[$row['bank_account_name']] = $row['balance'];
                    }

                } else {

                    $bank_balances[$row['bank_account_name']] = $row['balance'];
                    $bank_balances['ACCOUNTS RECEIVABLES TOTAL'] = $acc_rcvbl_total;
                }
            }

            return AxisPro::SendResponse($bank_balances);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    /**
     * @return array
     * For expense chart
     */
    public function expenses()
    {

        try {
            $options = null;

            if ($options == 'Last Month') {
                $today1 = date('Y-m-d', strtotime('last day of previous month'));
                $begin1 = date('Y-m-d', strtotime('first day of previous month'));
            } elseif ($options == 'This Month') {
                $begin1 = date("Y-m-d", strtotime("first day of this month"));
                $today1 = date("Y-m-d", strtotime("last day of this month"));
            } elseif ($options == 'Last Quarter Year') {

            } elseif ($options == 'Last Week') {
                $begin1 = date("Y-m-d", strtotime("last week monday"));
                $today1 = date("Y-m-d", strtotime("last week sunday"));
            } elseif ($options == 'Today') {
                $begin1 = date("Y-m-d", strtotime("now"));
                $today1 = date("Y-m-d", strtotime("now"));
            } else {
                $f_year = kv_get_current_fiscalyear();
                $begin1 = $f_year['begin'];
                $today1 = date('Y-m-d');
            }

            $f_year = kv_get_current_fiscalyear();
            $begin1 = $f_year['begin'];
            $today1 = date('Y-m-d');


            $charts_list = kv_get_expenses_chartlists();
            $final = array();
            foreach ($charts_list as $single_char) {
                $sql = "SELECT ROUND(SUM(IF(amount >= 0, amount, 0)),2) as debit, 
                ROUND(SUM(IF(amount < 0, -amount, 0)),2) as credit, ROUND(SUM(amount),2) as balance 
                FROM " . TB_PREF . "gl_trans," . TB_PREF . "chart_master," . TB_PREF . "chart_types, " . TB_PREF . "chart_class 
                WHERE " . TB_PREF . "gl_trans.account=" . TB_PREF . "chart_master.account_code AND " . TB_PREF . "chart_master.account_type=" . TB_PREF . "chart_types.id 
                AND " . TB_PREF . "chart_types.class_id=" . TB_PREF . "chart_class.cid AND account='" . $single_char[0] . "' AND tran_date > IF(ctype>0 AND ctype<4, '0000-00-00', '" . $begin1 . "') AND tran_date <= '" . $today1 . "' ";
                $result = db_query($sql, "could not get Company Details");

                while ($row = db_fetch_assoc($result)) {
                    if ($row['balance'] > 0) {
                        $row['code'] = $single_char[0];
                        $row['name'] = $single_char[1];
                        $final[] = $row;
                    }
                }
            }
            return AxisPro::SendResponse($final);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @return array
     * Get top 10 customers
     */
    public function top_ten_customers()
    {
        try {
            $category_sales_count = get_top_customers($options = null);
            return AxisPro::SendResponse($category_sales_count);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @return array
     * Get top10 selling services
     */
    public function top_ten_services()
    {

        try {
            $today = Today();

            $begin = begin_fiscalyear();
            $begin1 = date2sql($begin);
            $today1 = date2sql($today);


            $sql = $sql = "SELECT ROUND(SUM((trans.unit_price * trans.quantity) * d.rate),2) AS total, s.stock_id, s.description, 
            SUM(trans.quantity) AS qty, ROUND(SUM((trans.govt_fee) * trans.quantity),2) AS costs FROM
            " . TB_PREF . "debtor_trans_details AS trans, " . TB_PREF . "stock_master AS s, " . TB_PREF . "debtor_trans AS d 
            WHERE trans.stock_id=s.stock_id AND trans.debtor_trans_type=d.type AND trans.debtor_trans_no=d.trans_no
            AND (d.type = " . ST_SALESINVOICE . " OR d.type = " . ST_CUSTCREDIT . ") ";

            $sql .= "AND tran_date >= '$begin1' ";

            $sql .= "AND tran_date <= '$today1' GROUP by s.stock_id ORDER BY total DESC, s.stock_id 
            LIMIT 10";
            $result = db_query($sql);

            $top_ten_services = [];
            while ($myrow = db_fetch($result)) {

                $top_ten_services[] = $myrow;

            }

            return AxisPro::SendResponse($top_ten_services);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @return array
     * List all customers
     */
    public function get_all_customers()
    {

        try {
            $sql = "SELECT * FROM 0_debtors_master";
            $result = db_query($sql);

            $all_customers = [];
            while ($myrow = db_fetch($result)) {

                $all_customers[] = $myrow;

            }

            return AxisPro::SendResponse($all_customers);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    /**
     * @param string $format
     * @return array
     * List all GL Accounts
     */
    public function get_all_gl_accounts($format = 'json')
    {
        try {
            $sql = "SELECT * FROM 0_chart_master";
            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch($result)) {
                $return_result[] = $myrow;
            }

            return AxisPro::SendResponse($return_result, $format);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    /**
     * @return array
     * Get all COA groups
     */
    public function get_all_coa_groups()
    {

        try {
            $sql = "SELECT * FROM 0_chart_types WHERE 1=1 ";

            if (isset($_GET['class_id']) && !empty($_GET['class_id'])) {
                $sql .= " AND class_id=" . $_GET['class_id'];
            }

            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch($result)) {

                $myrow['name'] = $myrow['id'] . " - " . $myrow['name'];

                $return_result[] = $myrow;

            }

            return AxisPro::SendResponse($return_result);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }

    /**
     * @return array
     * Get all COA classes
     */
    public function get_all_coa_classes()
    {
        try {
            $sql = "SELECT * FROM 0_chart_class";
            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch($result)) {

                $myrow['class_name'] = $myrow['cid'] . " - " . $myrow['class_name'];

                $return_result[] = $myrow;

            }

            return AxisPro::SendResponse($return_result);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @return array
     * Get common Application Settings
     */
    public function common_settings()
    {
        try {
            $settings = [];
            $curr_fs_yr = get_current_fiscalyear();
            $settings['curr_fiscal_year']['begin'] = sql2date($curr_fs_yr['begin']);
            $settings['curr_fiscal_year']['end'] = sql2date($curr_fs_yr['end']);

            return AxisPro::SendResponse($settings);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @return array
     * Get the chart of accounts
     */
    public function chart_of_accounts()
    {

        try {
            $sql = "SELECT CONCAT('CLS_',cid) id, CONCAT(class.cid,' - ',class.class_name) text,0 parent_id, 'class' AS type, 
            cid real_id, 0 as p_id_one,0 as p_id_two  
            FROM 0_chart_class class UNION
            
            SELECT CONCAT('GRP_',id), CONCAT(id,' - ',name) text, 
            CASE WHEN (parent='' OR parent=0 ) THEN CONCAT('CLS_',class_id) ELSE CONCAT('GRP_',parent) END AS parent_id, 
            'group' AS type, id real_id, class_id as p_id_one,parent as p_id_two  
            FROM 0_chart_types UNION 
            
            SELECT CONCAT('LGR_',account_code) id, CONCAT(account_code,' - ',account_name) text,
            CONCAT('GRP_',account_type) parent_id, 'ledger' AS type, account_code real_id,0 as p_id_one,0 as p_id_two  
            FROM 0_chart_master UNION 
            
            
            SELECT CONCAT('SLR_',code) id, CONCAT(code,' - ',name) text,CONCAT('LGR_',ledger_id) parent_id, 'sub_ledger' AS type, 
            code real_id, 0 as p_id_one,0 as p_id_two  
            FROM 0_sub_ledgers ";


            $result = db_query($sql);
            $return_result = [];
            while ($myrow = db_fetch_assoc($result)) {
                $return_result[] = $myrow;
            }

            // Build array of item references:
            foreach ($return_result as $key => &$item) {
                $itemsByReference[$item['id']] = &$item;
                // Children array:
                $itemsByReference[$item['id']]['children'] = array();
                // Empty data class (so that json_encode adds "data: {}" )
                $itemsByReference[$item['id']]['data'] = new StdClass();
            }

            foreach ($return_result as $key => &$item)
                if ($item['parent_id'] && isset($itemsByReference[$item['parent_id']]))
                    $itemsByReference [$item['parent_id']]['children'][] = &$item;

            // Remove items that were added to parents elsewhere:
            foreach ($return_result as $key => &$item) {
                if ($item['parent_id'] && isset($itemsByReference[$item['parent_id']]))
                    unset($return_result[$key]);
            }

            return AxisPro::SendResponse($return_result);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * Create new node in chart of accounts tree
     */
    public function create_coa_node()
    {
        try {
            $type = $_POST['node_type'];
            $text = $_POST['text'];
            $node_id = $_POST['node_id'];
            $purpose = $_POST['purpose'];

            $table = '';
            $values = [];
            $primary_key = 0;

            $error = false;
            $msg = "";

            switch ($type) {

                case 'group' :

                    $parent_node_id = $_POST['parent_id'];
                    $parent_node_info = get_account_type($parent_node_id);

                    $class_id = $parent_node_info['class_id'];
                    $parent_node_type = $_POST['parent_node_type'];

                    if ($parent_node_type == 'CLS') {
                        $parent_node_id = 0;
                        $class_id = $_POST['parent_id'];
                    }

                    if (strlen(trim($_POST['node_id'])) == 0) {
                        echo json_encode(['msg' => 'The account group id cannot be empty.', 'status' => 'FAIL']);
                        exit();
                    }

                    if (strlen(trim($_POST['text'])) == 0) {
                        echo json_encode(['msg' => 'The account group name cannot be empty.', 'status' => 'FAIL']);
                        exit();
                    }

                    if ($_POST['node_id'] === $parent_node_id) {
                        echo json_encode(['msg' => 'You cannot set an account group to be a subgroup of itself.', 'status' => 'FAIL']);
                        exit();
                    }

                    $check = get_account_type(trim($_POST['node_id']));
                    if ($check && ($purpose != 'update')) {
                        echo json_encode(['msg' => 'This account group id is already in use', 'status' => 'FAIL']);
                        exit();
                    }


                    $values = [
                        'id' => $_POST['node_id'],
                        'parent' => $parent_node_id,
                        'class_id' => $class_id,
                        'name' => db_escape($_POST['text'])

                    ];

                    $primary_key = 'id';
                    $table = '0_chart_types';


                    break;

                case 'ledger' :

                    $values = [
                        'account_code' => db_escape($_POST['node_id']),
                        'account_name' => db_escape($_POST['text']),
                        'account_type' => $_POST['parent_id'],
                    ];

                    $primary_key = 'account_code';
                    $table = '0_chart_master';

                    break;
                case 'sub_ledger' :

                    $values = [
                        'code' => $_POST['node_id'],
                        'name' => db_escape($_POST['text']),
                        'ledger_id' => $_POST['parent_id'],
                    ];

                    $primary_key = 'code';
                    $table = '0_sub_ledgers';

                    break;
            }


            if (!empty($table)) {

                if ($purpose == 'create') {
                    db_insert($table, $values);
                }
                if ($purpose == 'update') {
                    $id = $values[$primary_key];
                    unset($values[$primary_key]);
                    db_update($table, $values, [$primary_key . "=" . $id]);
                }

            }


            $msg_text = "Node updated";
            if ($purpose == 'create')
                $msg_text = "New node created";
            if ($purpose == 'update')
                $msg_text = "Node updated";

            return AxisPro::SendResponse(['msg' => $msg_text, 'status' => 'OK']);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @param $id
     * @return mixed|string|\Symfony\Component\Translation\TranslatorInterface
     * Check before deleting COA group
     */
    public function node_group_delete_check($id)
    {
        try {
            if (key_in_foreign_table($id, 'chart_master', 'account_type')) {

                return trans("Cannot delete this account group because GL accounts have been created referring to it.");
            }

            if (key_in_foreign_table($id, 'chart_types', 'parent')) {
                return trans("Cannot delete this account group because GL account groups have been created referring to it.");
            }

            return 'DELETABLE';
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    /**
     * @param $selected_account
     * @return bool|mixed|string|\Symfony\Component\Translation\TranslatorInterface
     * Check before deleting COA GL
     */
    public function node_ledger_delete_check($selected_account)
    {
        try {
            global $SysPrefs;
            if ($selected_account == "")
                return false;

            if ($selected_account == $SysPrefs->prefs['opening_bal_equity_account']) {
                return (trans("Cannot delete this system default account."));
            }

            if (key_in_foreign_table($selected_account, 'gl_trans', 'account')) {
                return (trans("Cannot delete this account because transactions have been created using this account."));
            }

            if (gl_account_in_company_defaults($selected_account)) {
                return (trans("Cannot delete this account because it is used as one of the company default GL accounts."));
            }

            if (key_in_foreign_table($selected_account, 'bank_accounts', 'account_code')) {
                return (trans("Cannot delete this account because it is used by a bank account."));
            }

            if (gl_account_in_stock_category($selected_account)) {
                return (trans("Cannot delete this account because it is used by one or more Item Categories."));
            }

            if (gl_account_in_stock_master($selected_account)) {
                return (trans("Cannot delete this account because it is used by one or more Items."));
            }

            if (gl_account_in_tax_types($selected_account)) {
                return (trans("Cannot delete this account because it is used by one or more Taxes."));
            }

            if (gl_account_in_cust_branch($selected_account)) {
                return (trans("Cannot delete this account because it is used by one or more Customer Branches."));
            }
            if (gl_account_in_suppliers($selected_account)) {
                return (trans("Cannot delete this account because it is used by one or more suppliers."));
            }

            if (gl_account_in_quick_entry_lines($selected_account)) {
                return (trans("Cannot delete this account because it is used by one or more Quick Entry Lines."));
            }

            return "DELETABLE";
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * Delete a COA Node
     */
    public function delete_coa_node()
    {

        try {
            $type = $_POST['node_type'];
            $node_id = $_POST['node_id'];

            $error = false;
            $msg = "";
            $status = "OK";


            switch ($type) {

                case 'group' :

                    $check = $this->node_group_delete_check($node_id);

                    if ($check == 'DELETABLE') {
                        delete_account_type($node_id);
                        $msg = "Node deleted";
                    } else {
                        $error = true;
                        $status = "FAIL";
                        $msg = $check;
                    }


                    break;

                case 'ledger' :

                    $check = $this->node_ledger_delete_check($node_id);

                    if ($check == 'DELETABLE') {
                        delete_gl_account($node_id);
                        $msg = "Node deleted";
                    } else {
                        $error = true;
                        $status = "FAIL";
                        $msg = $check;
                    }

                    break;

                case 'sub_ledger' :

                    //$check = $this->node_ledger_delete_check($node_id);

                    //if ($check == 'DELETABLE') {
                    $this->delete_subledger($node_id);
                    $msg = "Node deleted";
                    // } else {
                    //    $error = true;
                    //     $status = "FAIL";
                    //    $msg = $check;
                    //  }


                    break;


            }


            echo json_encode(['status' => $status, 'msg' => $msg]);
            exit();

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    function delete_subledger($node_id)
    {
        $sql = "DELETE FROM 0_sub_ledgers WHERE code='" . $node_id . "'";
        db_query($sql);
    }


    /**
     * Change COA node parent
     */
    public function change_coa_parent()
    {

        try {
            $type = $_POST['node_type'];
            $node_id = $_POST['node_id'];
            $parent_id = $_POST['parent_id'];
            $class_id = $_POST['class_id'];

            if (empty($parent_id)) $parent_id = 0;


            $error = false;
            $msg = "";

            switch ($type) {
                case 'group' :

                    if (empty($class_id)) $class_id = 0;
                    if (empty($parent_id)) $parent_id = 0;

                    db_update('0_chart_types', ['parent' => $parent_id, 'class_id' => $class_id], ["id=$node_id"]);

                    break;
                case 'ledger' :

                    db_update('0_chart_master', ['account_type' => $parent_id], ["account_code=$node_id"]);

                    break;
            }

            return AxisPro::SendResponse(['status' => "OK", 'msg' => "Parent changed"]);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    /**
     * @return array
     * Get the COA Class balances
     */
    public function get_class_balances()
    {

        try {
            $from = date2sql($_GET['from']);
            $to = date2sql($_GET['to']);

            $sql = "SELECT cls.class_name name, SUM(gl.amount) amount, cls.cid as id, 'class' as coa_type FROM 0_gl_trans gl

            LEFT JOIN 0_chart_master chart ON chart.account_code=gl.account 

            LEFT JOIN 0_chart_types grp on grp.id=chart.account_type

            LEFT JOIN 0_chart_class cls on cls.cid=grp.class_id 

            WHERE  cls.ctype IN (4,5,6) AND gl.tran_date >= '$from' AND gl.tran_date <= '$to' 

            GROUP BY cls.cid ORDER BY cls.ctype ";


            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch_assoc($result)) {

                $return_result[] = $myrow;
                $myrow['amount'] = round($myrow["amount"], 2);

            }

            return AxisPro::SendResponse($return_result);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    /**
     * @return array
     * Get Top level COA group balances
     */
    public function get_top_level_group_balances()
    {

        try {
            $from = date2sql($_GET['from']);
            $to = date2sql($_GET['to']);

            $class_id = $_GET['parent_id'];

            $sql = "SELECT grp.name, SUM(gl.amount) amount, grp.id,'group' as coa_type  FROM 0_gl_trans gl
            LEFT JOIN 0_chart_master chart ON chart.account_code=gl.account 
            LEFT JOIN 0_chart_types grp on grp.id=chart.account_type
            LEFT JOIN 0_chart_class cls on cls.cid=grp.class_id 
            WHERE  cls.ctype IN (4,5,6) AND grp.class_id=$class_id 

            AND gl.tran_date >= '$from' AND gl.tran_date <= '$to' 

            GROUP BY chart.account_type";


            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch_assoc($result)) {

                $myrow['amount'] = round2($myrow["amount"], 2);
                $return_result[] = $myrow;


            }

            return AxisPro::SendResponse($return_result);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @return array
     * Get COA group balances
     */
    public function get_group_balances()
    {

        try {

            $from = date2sql($_GET['from']);
            $to = date2sql($_GET['to']);

            $group_id = $_GET['parent_id'];

            $sql = "SELECT grp.name, IFNULL(SUM(gl.amount),0) amount, grp.id,'group' AS coa_type  
            FROM 0_gl_trans gl 

            left join 0_chart_master chart on chart.account_code=gl.account
            right join 0_chart_types grp on grp.id=chart.account_type

            where grp.parent=$group_id AND gl.tran_date >= '$from' AND gl.tran_date <= '$to' 

            group by grp.id";


            $sql .= " UNION ";

            $sql .= "SELECT chart.account_name name, SUM(gl.amount) amount, chart.account_code id,'ledger' AS coa_type  
            FROM 0_gl_trans gl 

            left join 0_chart_master chart on chart.account_code=gl.account

            where chart.account_type=$group_id AND gl.tran_date >= '$from' AND gl.tran_date <= '$to' 

            group by gl.account";


            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch_assoc($result)) {

                $myrow['amount'] = round($myrow["amount"], 2);
                $return_result[] = $myrow;

            }

            return AxisPro::SendResponse($return_result);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    /**
     * @return array
     * Get the Ledger transactions
     */
    public function get_ledger_transactions()
    {

        try {
            global $systypes_array;

            $account = $_GET['parent_id'];

            $from = date2sql($_GET['from']);
            $to = date2sql($_GET['to']);

            $start = isset($_GET['start']) ? $_GET['start'] : 0;


            $sql = "SELECT gl.*,sub_ledger.name as sub_ledger_name, j.event_date, j.doc_date, a.gl_seq, u.user_id, st.supp_reference, gl.person_id subcode,
            IFNULL(IFNULL(sup.supp_name, debt.name), bt.person_id) as person_name, 
            IFNULL(gl.person_id, IFNULL(sup.supplier_id, debt.debtor_no)) as person_id,
            IF(gl.person_id, gl.person_type_id, IF(sup.supplier_id," . PT_SUPPLIER . "," . "IF(debt.debtor_no," . PT_CUSTOMER . ", -1))) as person_type_id,
            IFNULL(st.tran_date, IFNULL(dt.tran_date, IFNULL(bt.trans_date, IFNULL(grn.delivery_date, gl.tran_date)))) as doc_date,
            coa.account_name, ref.reference,voucher.chq_date,voucher.chq_no 
            FROM "
                . TB_PREF . "gl_trans gl
            LEFT JOIN " . TB_PREF . "voided v ON gl.type_no=v.id AND v.type=gl.type

            LEFT JOIN " . TB_PREF . "supp_trans st ON gl.type_no=st.trans_no AND st.type=gl.type AND (gl.type!=" . ST_JOURNAL . " OR gl.person_id=st.supplier_id)
            LEFT JOIN " . TB_PREF . "grn_batch grn ON grn.id=gl.type_no AND gl.type=" . ST_SUPPRECEIVE . " AND gl.person_id=grn.supplier_id
            LEFT JOIN " . TB_PREF . "debtor_trans dt ON gl.type_no=dt.trans_no AND dt.type=gl.type AND (gl.type!=" . ST_JOURNAL . " OR gl.person_id=dt.debtor_no)

            LEFT JOIN " . TB_PREF . "suppliers sup ON st.supplier_id=sup.supplier_id OR grn.supplier_id=sup.supplier_id
            LEFT JOIN " . TB_PREF . "cust_branch branch ON dt.branch_code=branch.branch_code
            LEFT JOIN " . TB_PREF . "debtors_master debt ON dt.debtor_no=debt.debtor_no

            LEFT JOIN " . TB_PREF . "bank_trans bt ON bt.type=gl.type AND bt.trans_no=gl.type_no AND bt.amount!=0
            AND bt.person_type_id=gl.person_type_id AND bt.person_id=gl.person_id

            LEFT JOIN " . TB_PREF . "journal j ON j.type=gl.type AND j.trans_no=gl.type_no
            LEFT JOIN " . TB_PREF . "audit_trail a ON a.type=gl.type AND a.trans_no=gl.type_no AND NOT ISNULL(gl_seq)
            LEFT JOIN " . TB_PREF . "users u ON a.user=u.id 

            LEFT JOIN " . TB_PREF . "vouchers AS voucher ON voucher.trans_no=gl.type_no 
            AND gl.type=IF(voucher.voucher_type='PV',1,2) 

            LEFT JOIN 0_sub_ledgers sub_ledger ON sub_ledger.code = gl.axispro_subledger_code 

            LEFT JOIN " . TB_PREF . "refs ref ON ref.type=gl.type AND ref.id=gl.type_no,"
                . TB_PREF . "chart_master coa
            WHERE coa.account_code=gl.account
            AND ISNULL(v.date_)

            AND gl.tran_date >= '$from' AND gl.tran_date <= '$to' 


            AND gl.amount <> 0";

            if ($account != null)
                $sql .= " AND gl.account = " . db_escape($account);


            $sql .= " LIMIT $start,10 ";


            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch_assoc($result)) {


                if (empty($myrow['person_name']))
                    $myrow['person_name'] = "";

                if (empty($myrow['sub_ledger_name']))
                    $myrow['sub_ledger_name'] = "";

                $myrow['tran_date'] = sql2date($myrow['tran_date']);
                $myrow['type'] = $systypes_array[$myrow["type"]];
                $myrow['amount'] = round($myrow["amount"], 2);

                $return_result[] = $myrow;

            }


            $op_bal = 0;

            if ($start == 0) {
                $op_bal = get_gl_balance_from_to(null, null, $account);
            }


            return AxisPro::SendResponse(['data' => $return_result, 'ob_bal' => $op_bal]);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    /**
     * @return array
     * Get all item categories
     */
    public function get_all_item_categories($format = 'json')
    {

        try {
            $sql = "SELECT * FROM 0_stock_category WHERE inactive = 0";
            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch($result)) {

                $return_result[] = $myrow;

            }
            return AxisPro::SendResponse($return_result, $format);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    /**
     * @return mixed
     * Get a category info
     */
    public function get_category()
    {

        try {
            $id = $_GET['category_id'];

            $sql = "SELECT * FROM 0_stock_category WHERE category_id = $id";
            $result = db_query($sql);

            $return_result = db_fetch_assoc($result);

            return AxisPro::SendResponse($return_result);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    /**
     * @param bool $new_item
     * @param string $Mode
     * @return array
     * Validation check fot item create
     */
    public function validate_before_item_create($new_item = true, $Mode = "ADD_ITEM")
    {

        try {
            $errors = [];

            if (strlen($_POST['description']) == 0) {
                $errors['description'] = trans('The item name must be entered.');
            }

            if (strlen($_POST['tax_type_id']) == 0) {
                $errors['tax_type_id'] = trans('The Tax type cannot be empty.');
            }

            if (strlen($_POST['category_id']) == 0) {
                $errors['category_id'] = trans('Please select item category.');
            }

            if (strlen($_POST['price']) == 0) {
                $errors['price'] = trans('The Center fee cannot be empty (Put atleast 0).');
            }
            if (strlen($_POST['govt_fee']) == 0) {
                $errors['govt_fee'] = trans('The Govt. fee cannot be empty (Put atleast 0).');
            }
            if (strlen($_POST['bank_service_charge']) == 0) {
                $errors['bank_service_charge'] = trans('The Bank Service charge cannot be empty (Put atleast 0).');
            }
            if (strlen($_POST['pf_amount']) == 0) {
                $errors['pf_amount'] = trans('The Other charge cannot be empty (Put atleast 0).');
            }
            if (strlen($_POST['commission_loc_user']) == 0) {
                $errors['commission_loc_user'] = trans('The Local Commission cannot be empty (Put atleast 0).');
            }
            if (strlen($_POST['commission_non_loc_user']) == 0) {
                $errors['commission_non_loc_user'] = trans('The Non Local Commission cannot be empty (Put atleast 0).');
            }
            if (strlen($_POST['bank_service_charge_vat']) == 0) {
                $errors['bank_service_charge_vat'] = trans('The VAT for Bank charge cannot be empty (Put atleast 0).');
            }


            if (strlen($_POST['NewStockID']) == 0) {
                $errors['NewStockID'] = trans('The item code cannot be empty.');
            }
            if (strstr($_POST['NewStockID'], " ") || strstr($_POST['NewStockID'], "'") ||
                strstr($_POST['NewStockID'], "+") || strstr($_POST['NewStockID'], "\"") ||
                strstr($_POST['NewStockID'], "&") || strstr($_POST['NewStockID'], "\t")) {

                $errors['NewStockID'] = trans('The item code cannot contain any of the following characters -  & + OR a space OR quotes.');

            }
            if ($new_item && db_num_rows(get_item_kit($_POST['NewStockID']))) {
                $errors['NewStockID'] = trans('This item code is already assigned to stock item or sale kit.');

            }

            if (get_post('fixed_asset')) {
                if ($_POST['depreciation_rate'] > 100) {
                    $_POST['depreciation_rate'] = 100;
                } elseif ($_POST['depreciation_rate'] < 0) {
                    $_POST['depreciation_rate'] = 0;
                }
                $move_row = get_fixed_asset_move($_POST['NewStockID'], ST_SUPPRECEIVE);
                if (isset($_POST['depreciation_start']) && strtotime($_POST['depreciation_start']) < strtotime($move_row['tran_date'])) {
                    $errors['depreciation_start'] = trans('The depreciation cannot start before the fixed asset purchase date.');
                }
            }


            if (!check_num('price', 0)) {
                $errors['price'] = trans("The service charge entered must be numeric.");
            } elseif ($Mode == 'ADD_ITEM' && get_stock_price_type_currency($_POST['NewStockID'], $_POST['sales_type_id'], $_POST['curr_abrev'])) {
                $errors['price'] = trans("The sales pricing for this item, sales type and currency has already been added.");
            }


            return $errors;

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    /**
     * @return mixed
     * Save an item
     */
    public function save_item()
    {

        try {
            $mode = 'ADD_ITEM';
            $new_item = true;
            if (!empty($_POST['edit_stock_id'])) {
                $mode = 'EDIT_ITEM';
                $new_item = false;
            }

            $errors = $this->validate_before_item_create($new_item, $mode);

            if (!empty($errors)) {
                return AxisPro::SendResponse(['status' => 'FAIL', 'msg' => 'VALIDATION_FAILED', 'data' => $errors]);
            }

            $_POST['sub_category_id'] = 0;
            if (isset($_POST['sub_cat_1']) && !empty($_POST['sub_cat_1']))
                $_POST['sub_category_id'] = $_POST['sub_cat_1'];

            if (isset($_POST['sub_cat_2']) && !empty($_POST['sub_cat_2']))
                $_POST['sub_category_id'] = $_POST['sub_cat_2'];

            if ($new_item) {

                add_item($_POST['NewStockID'], $_POST['description'],
                    $_POST['long_description'], $_POST['category_id'], $_POST['tax_type_id'],
                    $_POST['units'], get_post('fixed_asset') ? 'F' : get_post('mb_flag'), $_POST['sales_account'],
                    $_POST['inventory_account'], $_POST['cogs_account'],
                    $_POST['adjustment_account'], $_POST['wip_account'],
                    $_POST['dimension_id'], $_POST['dimension2_id'],
                    check_value('no_sale'), check_value('editable'), check_value('no_purchase'));


                /** Add Item Price */

                add_item_price($_POST['NewStockID'], $_POST['sales_type_id'],
                    $_POST['curr_abrev'], input_num('price'));
            } else {

                $update_params = [
                    'description' => db_escape($_POST['description']),
                    'long_description' => db_escape($_POST['long_description']),
                    'category_id' => db_escape($_POST['category_id']),
                    'tax_type_id' => db_escape($_POST['tax_type_id']),
                    'sales_account' => db_escape($_POST['sales_account']),
                    'cogs_account' => db_escape($_POST['cogs_account']),
                    'editable' => db_escape($_POST['editable']),
                ];

                db_update('0_stock_master', $update_params, ['stock_id = ' . db_escape($_POST['NewStockID'])]);
                db_update('0_prices', ['price' => input_num('price')], ['stock_id = ' . db_escape($_POST['NewStockID'])]);

                update_item_code(-1, $_POST['NewStockID'], $_POST['NewStockID'], db_escape($_POST['description']), $_POST['category_id'], 1, 0);

                update_record_status($_POST['NewStockID'], $_POST['inactive'],
                    'stock_master', 'stock_id');
                update_record_status($_POST['NewStockID'], $_POST['inactive'],
                    'item_codes', 'item_code');

            }

            /** Update additional pricing fields */
            update_item_additional_charges_info($_POST['NewStockID'], input_num('govt_fee'), get_post('govt_bank_account'),
                input_num('bank_service_charge'), input_num('bank_service_charge_vat'),
                input_num('commission_loc_user'), input_num('commission_non_loc_user'), input_num('pf_amount'));


            if (empty($_POST['extra_service_charge']))
                $_POST['extra_service_charge'] = 0;

            db_update('0_stock_master', [
                'extra_service_charge' => $_POST['extra_service_charge']
            ], ['stock_id = ' . db_escape($_POST['NewStockID'])]);


            return AxisPro::SendResponse(['status' => 'OK', 'msg' => 'Item Saved']);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @return mixed
     * Generate item code
     */
    public function generate_item_code()
    {
        try {
            $code = $this->generateBarcode();
            return AxisPro::SendResponse(['status' => 'OK', 'data' => $code]);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @return array|string
     *  Generate auto item code
     */
    public function generateBarcode()
    {
        try {
            $tmpBarcodeID = "";
            $tmpCountTrys = 0;
            while ($tmpBarcodeID == "") {
                srand((double)microtime() * 1000000);
                $random_1 = rand(1, 9);
                $random_2 = rand(0, 9);
                $random_3 = rand(0, 9);
                $random_4 = rand(0, 9);
                $random_5 = rand(0, 9);
                $random_6 = rand(0, 9);
                $random_7 = rand(0, 9);
                //$random_8  = rand(0,9);

                // http://stackoverflow.com/questions/1136642/ean-8-how-to-calculate-checksum-digit
                $sum1 = $random_2 + $random_4 + $random_6;
                $sum2 = 3 * ($random_1 + $random_3 + $random_5 + $random_7);
                $checksum_value = $sum1 + $sum2;

                $checksum_digit = 10 - ($checksum_value % 10);
                if ($checksum_digit == 10)
                    $checksum_digit = 0;

                $random_8 = $checksum_digit;

                $tmpBarcodeID = $random_1 . $random_2 . $random_3 . $random_4 . $random_5 . $random_6 . $random_7 . $random_8;

                // LETS CHECK TO SEE IF THIS NUMBER HAS EVER BEEN USED
                $query = "SELECT stock_id FROM " . TB_PREF . "stock_master WHERE stock_id='" . $tmpBarcodeID . "'";
                $arr_stock = db_fetch(db_query($query));

                if (!$arr_stock['stock_id']) {
                    return $tmpBarcodeID;
                }
                $tmpBarcodeID = "";
            }
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }

    /**
     * @return mixed
     * Get subcategory
     */
    public function get_subcategory($cat_id, $p_id = 0, $format = 'json')
    {

        try {
            $category_id = $_GET['category_id'];
            $parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : 0;

            if (!empty($cat_id))
                $category_id = $cat_id;

            if (!empty($p_id))
                $parent_id = $p_id;

            $result = get_subcategory($parent_id, $category_id);
            $return_result = [];
            foreach ($result as $key => $value) {
                array_push($return_result, ['id' => $key, 'value' => $value]);
            }

            return AxisPro::SendResponse($return_result, $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    /**
     * @return mixed
     * Get all Item Tax Types
     */
    public function get_item_tax_types($format = 'json')
    {
        try {
            $sql = "SELECT id, name FROM " . TB_PREF . "item_tax_types WHERE inactive = 0";
            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch($result)) {

                $return_result[] = $myrow;

            }
            return AxisPro::SendResponse($return_result, $format);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @param bool $stock_id
     * @param string $format
     * @return mixed
     * Get single Item data
     */
    public function get_item_info($stock_id = false, $format = 'json')
    {

        if (isset($_GET['stock_id']) && !empty($_GET['stock_id']))
            $stock_id = $_GET['stock_id'];

        try {
            $stock_id = $stock_id ? $stock_id : $_REQUEST['stock_id'];
            $sql = (
                "SELECT
                    sm.*,
                    ba.dflt_bank_chrg
                FROM 
                    0_stock_master sm
                LEFT JOIN 0_bank_accounts ba ON 
                    ba.account_code = sm.govt_bank_account
                WHERE sm.stock_id = " . db_escape($stock_id)
            );
            $result = db_query($sql);
            $general_info = db_fetch_assoc($result);

            $sql = "SELECT * FROM 0_subcategories WHERE id=" . $general_info['sub_category_id'];
            $result = db_query($sql);
            $subcat_row = db_fetch($result);

            $sql = "SELECT price FROM 0_prices WHERE stock_id=" . db_escape($stock_id) . " AND sales_type_id = 1 LIMIT 1";
            $result = db_query($sql);
            $price = db_fetch($result);

            $sql = "SELECT tax_types.* FROM 0_tax_types tax_types 
            LEFT JOIN 0_tax_group_items tax_grp_items ON tax_grp_items.tax_type_id = tax_types.id
            LEFT JOIN 0_tax_groups tax_grp on tax_grp.id=tax_grp_items.tax_group_id
            LEFT JOIN 0_item_tax_types item_tax_type ON item_tax_type.id=tax_grp.id 
            WHERE item_tax_type.id=" . $general_info['tax_type_id'];
            $result = db_query($sql);
            $tax_info = db_fetch($result);


            $sql = "SELECT * FROM " . TB_PREF . "stock_category WHERE category_id = " . db_escape($general_info['category_id']);
            $result = db_query($sql);
            $category_info = db_fetch_assoc($result);


            $discount_info = [];
            if (isset($_GET['customer_id'])) {

                $sql = "SELECT * FROM customer_discount_items WHERE 
                item_id= " . $general_info['category_id'] . " AND customer_id = " . $_GET['customer_id'];

                $result = db_query($sql);
                $discount_info = db_fetch($result);

            }


            $return_result = [
                'g' => $general_info,
                'c' => $category_info,
                'sub' => $subcat_row,
                'p' => $price,
                'd' => $discount_info,
                't' => $tax_info
            ];

            return AxisPro::SendResponse($return_result, $format);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    /**
     * @return mixed
     * Get all items
     */
    public function get_items()
    {

        try {
            $sql = "SELECT `a`.`stock_id` AS `stock_id`,`a`.`description` AS `item_description`,

            IFNULL(govt_acc.account_name,'') as govt_account_name,
            cog_acc.account_name as cog_account_name,
            sales_acc.account_name as sales_account_name,
            CASE WHEN a.inactive=0 THEN 'ACTIVE' ELSE 'INACTIVE' END as active_status,

            `a`.`long_description` AS `long_description`,`b`.`description` AS `category_name`,`c`.`price` AS `service_charge`,


            `a`.`govt_fee`,

            `a`.`pf_amount` AS `pf_amount`,
            `a`.`bank_service_charge` AS `bank_service_charge`,`a`.`bank_service_charge_vat` AS `bank_service_charge_vat`,
            `a`.`commission_loc_user` AS `commission_loc_user`,`a`.`commission_non_loc_user` AS `commission_non_loc_user`
            FROM `0_stock_master` `a`
            LEFT JOIN `0_stock_category` `b` ON `b`.`category_id` = `a`.`category_id`
            LEFT JOIN `0_prices` `c` ON `c`.`stock_id` = `a`.`stock_id` AND `c`.`sales_type_id` = 1 

            LEFT JOIN 0_chart_master govt_acc on govt_acc.account_code=a.govt_bank_account 
            LEFT JOIN 0_chart_master cog_acc on cog_acc.account_code=a.cogs_account 
            LEFT JOIN 0_chart_master sales_acc on sales_acc.account_code=a.sales_account 

            WHERE 1=1   GROUP BY stock_id ";

            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch_assoc($result)) {

                $return_result[] = $myrow;

            }

            return AxisPro::SendResponse($return_result);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    /**
     * @param $table
     * @param $key
     * @param $val
     * @return array
     * Get records as key value array
     */
    function get_key_value_records($table, $key, $val)
    {

        try {
            $sql = "SELECT $key,$val FROM $table";
            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch_assoc($result))
                $return_result[$myrow[$key]] = $myrow[$val];

            return $return_result;
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    function get_permitted_item_list($show_inactive = false)
    {

        try {

            $user_id = $_SESSION['wa_current_user']->user;
            $user_info = get_user($user_id);

            $user_dimension = $user_info['dflt_dimension_id'];

            $permitted_cats = $user_info['permitted_categories'];

            $sql = "SELECT item.stock_id,item.description,item.long_description, 
            
            CONCAT(item.description,'-',IFNULL(item.long_description,'')) full_name FROM 0_stock_master item

            LEFT JOIN 0_stock_category cat ON cat.category_id = item.category_id

            where item.inactive=0 AND cat.dflt_dim1=$user_dimension";

            if (!empty($permitted_cats))
                $sql .= " and item.category_id in ($permitted_cats)";

            if ($show_inactive)
                $sql .= " and inactive=1";


            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch_assoc($result))
                $return_result[] = $myrow;

            return $return_result;


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }


    /**
     * @return mixed
     * Generate service report
     */
    public function service_report()
    {

        try {
            $sql = PrepareQuery::ServiceReport($_GET);

            $total_count_sql = "select count(*) as cnt,
            SUM(line_total) sum_line_total,
            SUM(invoice_total) sum_invoice_total,
            SUM(line_discount_amount) sum_discount,
            SUM(reward_amount) sum_reward, 
            SUM(quantity) sum_quantity,
            SUM(employee_commission) sum_employee_commission,  
            SUM(total_service_charge) sum_total_service_charge,
            SUM(total_govt_fee) sum_total_govt_fee,
            (SUM(line_total)-SUM(reward_amount)-SUM(customer_commission)-SUM(employee_commission)) sum_net_service_charge,
            SUM(govt_fee) sum_govt_fee, 
            SUM(bank_service_charge) sum_bank_service_charge, 
            SUM(bank_service_charge_vat) sum_bank_service_charge_vat,
            SUM(customer_commission) sum_customer_commission 
            from ($sql) as tmpTable";
            $total_count_exec = db_fetch_assoc(db_query($total_count_sql));
            $total_count = $total_count_exec['cnt'];

            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $perPage = 200;
            $offset = ($page * $perPage) - $perPage;


            $sql = $sql . " LIMIT $perPage OFFSET $offset";

            $result = db_query($sql);
            $report = [];
            while ($myrow = db_fetch_assoc($result))
                $report[] = $myrow;


            $customers = $this->get_key_value_records('0_debtors_master', 'debtor_no', 'name');
            $gl_accounts = $this->get_key_value_records('0_chart_master', 'account_code', 'account_name');
            $categories = $this->get_key_value_records('0_stock_category', 'category_id', 'description');
            $service_category_map = $this->get_key_value_records('0_stock_master', 'stock_id', 'category_id');
            $users = $this->get_key_value_records('0_users', 'id', 'user_id');

            $real_name = $this->get_key_value_records('0_users', 'id', 'real_name');


            $filters = $this->getServiceReportFilters();


            $custom_report = [];
            if (isset($_GET['custom_rep_id']) && !empty(trim($_GET['custom_rep_id']))) {

                $sql = "SELECT * FROM 0_custom_reports WHERE id=" . $_GET['custom_rep_id'];

                $custom_report = db_fetch_assoc(db_query($sql));

                $custom_report['params'] = htmlspecialchars_decode($custom_report['params']);

            }


            return AxisPro::SendResponse(
                [
                    'rep' => $report,
                    'total_rows' => $total_count,
                    'pagination_link' => AxisPro::paginate($total_count),
                    'customers' => $customers,
                    'gl_accounts' => $gl_accounts,
                    'categories' => $categories,
                    'service_category_map' => $service_category_map,
                    'custom_report' => $custom_report,
                    'filters' => $filters,
                    'aggregates' => $total_count_exec,
                    'users' => $users,
                    'user_name' => $real_name
                ]
            );
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    /**
     * @return mixed
     * Load service report page
     */
    public function load_service_report_page()
    {

        try {
            $filters = $this->getServiceReportFilters();
            return AxisPro::SendResponse(['filters' => $filters]);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    /**
     * @return array
     * Get enabled filters of report
     */
    public function getServiceReportFilters()
    {

        try {
            $sql = "SELECT * FROM 0_debtors_master";
            $result = db_query($sql);

            $all_customers = [];
            while ($myrow = db_fetch($result)) {

                $all_customers[] = $myrow;

            }

            $sql = "SELECT * FROM 0_salesman";
            $result = db_query($sql);

            $salesman = [];
            while ($myrow = db_fetch($result)) {

                $salesman[] = $myrow;

            }

            $sql = "SELECT stock_id,description FROM 0_stock_master";
            $result = db_query($sql);

            $services = [];
            while ($myrow = db_fetch($result)) {

                $services[] = $myrow;

            }

            $sql = "SELECT category_id,description FROM 0_stock_category";
            if (in_array($_SESSION['wa_current_user']->user, [216])) {
                $sql .= " AND category_id IN (43,44)";
            }

            $result = db_query($sql);

            $categories = [];
            while ($myrow = db_fetch($result)) {

                $categories[] = $myrow;

            }

            $sql = "SELECT id, CONCAT(real_name, ' (', user_id, ')') AS real_name FROM 0_users";
            $result = db_query($sql);

            $users = [];
            while ($myrow = db_fetch($result)) {

                $users[] = $myrow;

            }

            return [
                'customers' => $all_customers,
                'salesman' => $salesman,
                'services' => $services,
                'categories' => $categories,
                'users' => $users,
            ];

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    /**
     * @return mixed
     * Save custom generated report
     */
    public function save_custom_report()
    {

        try {
            if (!isset($_POST['custom_report_name']) || trim($_POST['custom_report_name']) == '')
                return AxisPro::SendResponse(['status' => 'FAIL', 'msg' => 'Please Provide A Report Name']);


            $report_name = $_POST['custom_report_name'];
            $params = json_encode($_POST);

            $array = [
                'name' => db_escape($report_name),
                'params' => db_escape($params),
            ];

            if (isset($_GET['custom_rep_id']) && !empty(trim($_GET['custom_rep_id']))) {
                db_update('0_custom_reports', $array, ["id=" . $_GET['custom_rep_id']]);
            } else {
                db_insert('0_custom_reports', $array);

            }

            return AxisPro::SendResponse(['status' => 'OK', 'msg' => 'Report Saved']);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    /**
     * @param string $format
     * @return array
     * Get all custom reports
     */
    public function get_custom_reports($format = 'json')
    {

        try {
            $sql = "SELECT * FROM 0_custom_reports";

            $result = db_query($sql);
            $return_result = [];
            while ($myrow = db_fetch_assoc($result))
                $return_result[] = $myrow;

            return $return_result;
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    /**
     * @return mixed
     * Delete custom report
     */
    public function delete_custom_report()
    {
        try {
            $id = $_POST['id'];

            $sql = "DELETE FROM 0_custom_reports WHERE id=$id";
            db_query($sql);

            return AxisPro::SendResponse(['status' => 'OK', 'msg' => 'Your report has been deleted.']);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @return mixed
     * Change system language
     */
    public function change_language()
    {

        try {
            $language = $_POST["lang"];

            $_SESSION['wa_current_user']->prefs->user_language = $language;

            return AxisPro::SendResponse(['status' => 'OK', 'msg' => 'Language Changed.']);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @param null $date
     * @return array
     * Get account closing balance report
     */
    function get_acc_bal_report($date = null)
    {

        try {
            $date = date2sql($date);

            if (empty($date))
                $date = date2sql(Today());


            $sql = "select ROUND(ifnull(sum(gl.amount),0),2) total_cash_in_hand from 0_gl_trans gl 
            inner join 0_bank_accounts bank on bank.account_code=gl.account and bank.account_type=3 
            where gl.tran_date<='$date'";
            $result = db_fetch(db_query($sql));
            $total_cash_in_hand = $result['total_cash_in_hand'];

            $sql = "select ROUND(ifnull(sum(gl.amount),0),2) payment_cards_total from 0_gl_trans gl 
            inner join 0_chart_master chart on chart.account_code=gl.account and chart.account_type=15
            where gl.tran_date<='$date'";
            $result = db_fetch(db_query($sql));
            $payment_cards_total = $result['payment_cards_total'];


            $sql = "select chart.account_name,ROUND(ifnull(sum(gl.amount),0),2) amount from 0_gl_trans gl 
            inner join 0_chart_master chart on chart.account_code=gl.account and chart.account_type=15
            where gl.tran_date<='$date' group by chart.account_code";
            $result = db_query($sql);

            $e_dirhams = [];
            while ($row = db_fetch($result)) {
                $e_dirhams[] = $row;
            }


            $sql = "select chart.account_name,ROUND(ifnull(sum(gl.amount),0),2) amount from 0_gl_trans gl 
            inner join 0_chart_master chart on chart.account_code=gl.account and chart.account_type in (19,191)
            where gl.tran_date<='$date' group by chart.account_code";

            $result = db_query($sql);

            $acc_rcv_groups = [];
            while ($row = db_fetch($result)) {
                $acc_rcv_groups[] = $row;
            }

            $sql = "select ROUND(ifnull(sum(gl.amount),0),2) amount from 0_gl_trans gl 
            inner join 0_chart_master chart on chart.account_code=gl.account and chart.account_type in (19,191)
            where gl.tran_date<='$date'";


            $result = db_fetch(db_query($sql));
            $acc_rcvbl_total = $result['amount'];


            $sql = "select ROUND(ifnull(sum(gl.amount),0),2) cbd_total from 0_gl_trans gl 
            where gl.account=1112 and gl.tran_date<='$date'";
            $result = db_fetch(db_query($sql));
            $cbd_total = $result['cbd_total'];

            $sql = "select ROUND(ifnull(sum(gl.amount),0),2) fab_total from 0_gl_trans gl 
            where gl.account=1117 and gl.tran_date<='$date'";
            $result = db_fetch(db_query($sql));
            $fab_total = $result['fab_total'];

            $sql = "select ROUND(ifnull(sum(gl.amount),0),2) acc_rcvbl_total from 0_gl_trans gl 
            where gl.account=1200 and gl.tran_date<='$date'";


            return [
                'cash_in_hand' => $total_cash_in_hand ?: 0,
                'payment_cards' => $payment_cards_total ?: 0,
                'cbd' => $cbd_total ?: 0,
                'fab' => $fab_total ?: 0,
                'acc_rcvbl' => $acc_rcv_groups,
                'e_dirhams' => $e_dirhams,
                'rcvbl_total' => $acc_rcvbl_total
            ];

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    /**
     * @return bool|mysqli_result|resource
     * Import TAWSEEL CSV File | For TANZEEL
     */
    public function import_tawseel_csv()
    {

        try {
            $result = false;

            if (isset($_POST["submit"])) {

                $date_format_excel = $_POST['date_format'];

                begin_transaction();

                $insert = [
                    'created_by' => $_SESSION['wa_current_user']->user
                ];

                db_insert('0_tawseel_report', $insert);
                $report_master_id = db_insert_id();

                $cols = [
                    'reference',
                    ['field' => 'invoice_date', 'type' => 'date', 'format' => $date_format_excel],
                    'category',
                    'employee',
                    'customer',
                    'company',
                    ['field' => 'center_fee', 'type' => 'amount'],
                    ['field' => 'employee_fee', 'type' => 'amount'],
                    ['field' => 'typing_fee', 'type' => 'amount'],
                    ['field' => 'service_fee', 'type' => 'amount'],
                    ['field' => 'discount', 'type' => 'amount'],
                    'transaction_id',
                    'rcpt_no',
                    ['field' => 'tax_amount', 'type' => 'amount'],
                    'payment_method',
                    ['field' => 'total_fee', 'type' => 'amount'],
                    'status',
                ];


                $result = AxisPro::import_csv('0_tawseel_report_detail', $cols, $_FILES["csv_file"],
                    ['report_id' => $report_master_id]);


                commit_transaction();
            }

            return $result;
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    function get_tawseel_report()
    {

        try {
            $filter_from_date = db_escape(date2sql($_GET['filter_from_date']));
            $filter_to_date = db_escape(date2sql($_GET['filter_to_date']));

            $where = "";

            if (!empty($filter_from_date))
                $where .= " AND invoice_date>=$filter_from_date";

            if (!empty($filter_to_date))
                $where .= " AND invoice_date<=$filter_to_date";


            $sql = "SELECT * FROM 0_tawseel_report_detail WHERE 1=1 $where ";

            $total_count_sql = "select count(*) as cnt from ($sql) as tmpTable";

            $total_count_exec = db_fetch_assoc(db_query($total_count_sql));
            $total_count = $total_count_exec['cnt'];

            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $perPage = 200;
            $offset = ($page * $perPage) - $perPage;


            $sql = $sql . " LIMIT $perPage OFFSET $offset";

            $result = db_query($sql);
            $report = [];
            while ($myrow = db_fetch_assoc($result))
                $report[] = $myrow;


            return AxisPro::SendResponse(
                [
                    'rep' => $report,
                    'total_rows' => $total_count,
                    'pagination_link' => AxisPro::paginate($total_count, $perPage),
                ]
            );
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    function CreatesalesPerson()
    {
        if ($_POST['edit_id'] != '') {
            $sql = "UPDATE 0_salesman SET salesman_name='" . $_POST['sales_person'] . "',salesman_phone='" . $_POST['telephone'] . "',
            salesman_email='" . $_POST['email'] . "' where salesman_code='" . $_POST['edit_id'] . "'";
        } else {

            $sql = "INSERT INTO 0_salesman (salesman_name,salesman_phone,salesman_email)
            values ('" . $_POST['sales_person'] . "','" . $_POST['telephone'] . "','" . $_POST['email'] . "')";
        }
        // echo $sql;
        if (db_query($sql)) {
            return AxisPro::SendResponse(['status' => 'OK', 'msg' => 'Salesman Creation Done']);
        } else {
            return AxisPro::SendResponse(['status' => 'Fail', 'msg' => 'Error']);
        }


    }

    public function list_salesman()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $sql = "select * from 0_salesman where inactive='0'  ";
        $result = db_query($sql);
        $data = [];
        $payslip_label = '';
        $checkbox = '';
        while ($myrow = db_fetch($result)) {


            $data[] = array(
                $myrow['salesman_name'],
                $myrow['salesman_phone'],
                /* $myrow['salesman_fax'],*/
                $myrow['salesman_email'],
                '<label class="ClsCommison" style="cursor: pointer;color: blue;
                text-decoration: underline;" alt="' . $myrow['salesman_code'] . '" >Add Product Commison</label>',
                '<label class="ClsEdit" style="cursor: pointer;" alt_id="' . $myrow['salesman_code'] . '" alt_salesman="' . $myrow['salesman_name'] . '" alt_phone="' . $myrow['salesman_phone'] . '" alt_fax="' . $myrow['salesman_fax'] . '" alt_email="' . $myrow['salesman_email'] . '"><i class=\'flaticon-edit\'></i></label>',
                '<label class="ClsRemove" style="cursor: pointer;"   alt_id="' . $myrow['salesman_code'] . '" ><i class=\'flaticon-delete\'></i></label>'
            );
        }

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($result),
            "recordsFiltered" => db_num_rows($result),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);

    }

    function get_all_items()
    {
        $sql = "SELECT i.item_code, i.description
        FROM
        0_stock_master s,
        0_item_codes i
        LEFT JOIN
        0_stock_category c
        ON i.category_id=c.category_id
        WHERE i.stock_id=s.stock_id
        AND mb_flag != 'F'";

        /* if ($type == 'local')	{ // exclude foreign codes
              $sql .=	" AND !i.is_foreign";
          } elseif ($type == 'kits') { // sales kits
              $sql .=	" AND !i.is_foreign AND i.item_code!=i.stock_id";
          }*/
        $sql .= " AND !i.inactive AND !s.inactive AND !s.no_sale";
        $sql .= " GROUP BY i.item_code";

        $result = db_query($sql);
        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result);

    }

    function save_product_commission()
    {
        $sql_check = "select * from 0_salesman_product_percent where product_id='" . $_POST['ap_items'] . "' and salesman_id='" . $_POST['salesman_id'] . "'";
        $re = db_query($sql_check);
        if (db_num_rows($re) > 0) {
            $sql = "Update 0_salesman_product_percent set commission='" . $_POST['sales_commison'] . "' 
            where product_id='" . $_POST['ap_items'] . "' and salesman_id='" . $_POST['salesman_id'] . "'";
        } else {
            $sql = "INSERT into 0_salesman_product_percent (product_id,salesman_id,commission,status)
            VALUES('" . $_POST['ap_items'] . "','" . $_POST['salesman_id'] . "','" . $_POST['sales_commison'] . "','1')";
        }
        db_query($sql);
        return AxisPro::SendResponse(['status' => 'OK', 'Status' => 'Success']);
    }

    /*function list_salesman_commission()
    {
        $draw = intval($_POST["draw"]);
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);

        $sql = "select * from 
                0_salesman_product_percent 
                where status='1' and salesman_id='".$_POST['salesman_id']."'
                LIMIT ".$start.",".$length." ";
        $result = db_query($sql);
        $data = [];
        $payslip_label='';
        $checkbox='';
        while ($myrow = db_fetch($result)) {
            $get_name="Select description from 0_item_codes where item_code='".$myrow['product_id']."'";
            $item_name=db_fetch(db_query($get_name));
            $data[] = array(
                $myrow['product_id'],
                $item_name[0],
                $myrow['commission'],
                '<label class="ClsCommisonEdit" style="cursor: pointer;"  alt="'.$myrow['product_id'].'" alt_commison="'.$myrow['commission'].'"><i class=\'flaticon-edit\'></i></label>',
                '<label class="ClsCommisonRemove" style="cursor: pointer;"  alt="'.$myrow['id'].'"><i class=\'flaticon-delete\'></i></label>'
            );
        }

        $result_data = array(
            "draw" => $draw,
            "recordsTotal" => db_num_rows($result),
            "recordsFiltered" => db_num_rows($result),
            "data" => $data
        );

        return AxisPro::SendResponse($result_data);
    }

    function remove_salesman()
    {
        $sql_salesman="update 0_salesman set inactive='1' where salesman_code='".$_POST['salesman_id']."' ";
        db_query($sql_salesman);

        $sql_update_perce="Update 0_salesman_product_percent set status='0' where salesman_id='".$_POST['salesman_id']."'";
        db_query($sql_update_perce);

        return AxisPro::SendResponse(['status'=>'OK','msg'=>'Success']);
    }

    function remove_product_cmison()
    {
        $sql_update_perce="Update 0_salesman_product_percent set status='0' where id='".$_POST['product_commsion_id']."' ";
        if(db_query($sql_update_perce))
        {
            return AxisPro::SendResponse(['status'=>'OK','msg'=>'Product removed from salesman']);
        }

    }*/

    function get_Sales_man_sales_cnt()
    {
        $qry = "SELECT a.salesman_name,
        (SELECT COUNT(*) FROM 
            0_debtor_trans_details AS b 
            WHERE a.salesman_code=b.sales_man_id) AS sales_cnt
FROM 0_salesman AS a
WHERE a.inactive='0' LIMIT 5";

        $result = db_query($qry);
        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return $return_result;

    }

    public function get_all_accounts()
    {
        $sql = "SELECT chart.account_code,CONCAT(chart.account_code,' - ',chart.account_name) AS accname, chart.inactive, type.id
    FROM 0_chart_master chart,0_chart_types type
    WHERE chart.account_type=type.id";
        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result);
    }

    function get_from_subacc()
    {
        $ledger_id = $_POST['from_account'];
        $id = $_POST['id'];
        $sub_type = is_subledger_account($ledger_id);
        if ($sub_type == '') {
            $sql = "SELECT code,name
        FROM 0_sub_ledgers where ledger_id='" . $ledger_id . "' ";

            $result = db_query($sql);
            $return_result = [];
            $select = '<select class="form-control" id="ddl_from_sub_' . $id . '" style="width: 188px;"><option value="0">---Select Sub Ledger---</option>';
            while ($myrow = db_fetch($result)) {
                $select .= '<option value="' . $myrow['code'] . '">' . $myrow['name'] . '</option>';
            }
            $select .= '</select>';
        } else {
            if ($sub_type > 0) {
                $sql = "SELECT DISTINCT d.debtor_no as id, debtor_ref as name 
            FROM  
            0_debtors_master d,
            0_cust_branch c
            WHERE d.debtor_no=c.debtor_no AND NOT d.inactive AND c.receivables_account='" . $ledger_id . "'";
            } else {
                $sql = "SELECT supplier_id as id, supp_ref as name 
            FROM  0_suppliers s
            WHERE NOT s.inactive AND s.payable_account='" . $ledger_id . "' ";
            }

            $result = db_query($sql);
            $return_result = [];
            $select = '<select class="form-control" id="ddl_person_' . $id . '" style="width: 188px;"><option value="0">---Select Counter Party---</option>';
            while ($myrow = db_fetch($result)) {
                $select .= '<option value="' . $myrow['id'] . '">' . $myrow['name'] . '</option>';
            }
            $select .= '</select>';


        }

        return AxisPro::SendResponse($select);
    }


    function get_to_subacc()
    {
        $ledger_id = $_POST['to_account'];
        $id = $_POST['id'];
        $sql = "SELECT code,name
    FROM 0_sub_ledgers where ledger_id='" . $ledger_id . "' ";
        $result = db_query($sql);
        $return_result = [];
        $select = '<select class="form-control"  id="ddl_to_sub_' . $id . '" style="width: 188px;"><option value="0">---Select Sub Ledger---</option>';
        while ($myrow = db_fetch($result)) {
            $select .= '<option value="' . $myrow['code'] . '">' . $myrow['name'] . '</option>';
        }
        $select .= '</select>';

        return AxisPro::SendResponse($select);
    }

    public function post_multi_gl()
    {

        $gl_data = $_POST['gl_data'];
        $accounts_arra = $_POST['accounts'];
        $Refs = new references();

        $amount_to_gl = '';
        $account = '';
        $trans_id = '';
        for ($i = 0; $i < sizeof($gl_data); $i++) {
            $ref = $Refs->get_next(ST_JOURNAL, null, Today());
            $trans_type = 0;
            $total_gl = 0;
            $trans_id = get_next_trans_no(0);

            //$jv_date=date('d-M-Y',strtotime($gl_data[$i]['jv_date']));
            $jv_date = date('d/m/Y', strtotime($gl_data[$i]['jv_date']));


            $amount_sum = '0';
            foreach ($gl_data[$i]['accounts'] as $value) {
                if (isset($value['jv_from'])) {
                    $amount_to_gl_dbt = $value['amount'];
                    $account_dbt = $value['jv_from'];

                    add_gl_trans($trans_type, $trans_id, $jv_date, $account_dbt, 0, 0,
                        $value['from_memo'], $amount_to_gl_dbt, 'AED', "", 0, "", 0);

                    $amount_sum = $amount_sum + $amount_to_gl_dbt;

                    $gl_counter = db_insert_id();
                    $sql = "UPDATE 0_gl_trans SET axispro_subledger_code ='" . $value['jv_from_sub'] . "' WHERE counter = $gl_counter";
                    db_query($sql);

                    if ($value['tax_option'] == 1) {
                        $tax_amount = $value['amount'] * 5 / 100;
                        add_gl_trans($trans_type, $trans_id, $jv_date, $gl_data[$i]['tax_account'], 0, 0,
                            '', $tax_amount, 'AED', "", 0, "", 0);
                    }
                }
                if (isset($value['jv_to'])) {
                    if ($value['tax_option'] == 1) {
                        $tax_amount = $value['amount'] * 5 / 100;
                        $t = $value['amount'] + $tax_amount;
                        $amount_to_gl_crdt = '-' . $t;
                    } else {
                        $amount_to_gl_crdt = '-' . $value['amount'];
                    }

                    $account_crdt = $value['jv_to'];

                    add_gl_trans($trans_type, $trans_id, $jv_date, $account_crdt, 0, 0,
                        $value['to_memo'], $amount_to_gl_crdt, 'AED', "", 0, "", 0);
                    $gl_counter = db_insert_id();
                    $sql = "UPDATE 0_gl_trans SET axispro_subledger_code ='" . $value['jv_from_to'] . "' WHERE counter = $gl_counter";
                    db_query($sql);
                }


            }


            $sql = "INSERT INTO " . TB_PREF . "journal(`type`,`trans_no`, `amount`, `currency`, `rate`, `reference`, `source_ref`, `tran_date`,
           `event_date`)
VALUES("
                . db_escape($trans_type) . ","
                . db_escape($trans_id) . ","
                . db_escape(round($amount_sum)) . ",'AED',"
                . db_escape(1) . ","
                . db_escape($ref) . ",'',"
                . "'" . date('Y-m-d') . "',"
                . "'" . date('Y-m-d') . "')";

            db_query($sql);

            $Refs->save($trans_type, $trans_id, $gl_data[$i]['jv_no']);
            add_comments($trans_type, $trans_id, $jv_date, $gl_data[$i]['memo']);
            add_audit_trail($trans_type, $trans_id, $jv_date);


        }

        return AxisPro::SendResponse('Success');
    }


    public function check_refencenumber()
    {
        $ref_number = $_POST['refn_number'];
        $sql = "SELECT reference FROM 0_refs WHERE reference='" . $ref_number . "' and type='0'";
        $data = db_fetch(db_query($sql));

        if ($data['reference'] == '') {
            return AxisPro::SendResponse(0);
        } else {
            return AxisPro::SendResponse(1);
        }
    }

    public function get_reference_number()
    {
        $type = '';
        if ($_GET['type'] == '1') {
            $type = '1';
        }

        if ($_GET['type'] == '2') {
            $type = '2';
        }

        $Refs = new references();
        $ref_name = $Refs->get_next($type, 2, date('d/m/Y'));
        return AxisPro::SendResponse($ref_name);
    }

//    public function get_bank_accounts()
//    {
//        $sql = "SELECT  id,bank_account_name from 0_bank_accounts";
//        $result = db_query($sql);
//
//        $return_result = [];
//        while ($myrow = db_fetch($result)) {
//            $return_result[] = $myrow;
//        }
//
//        return AxisPro::SendResponse($return_result);
//    }

    public function get_customers($format = 'json')
    {
        $sql = "SELECT debtor_no, debtor_ref, name, curr_code, inactive,concat(debtor_ref,' - ',name) as custname FROM 0_debtors_master 
            where debtor_no <>1  ORDER BY debtor_ref ASC";
        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result, $format);
    }


    public function get_customer($format = "json")
    {

        try {
            $id = $_GET['id'];

            $sql = "SELECT * FROM 0_debtors_master WHERE debtor_no = $id";
            $result = db_query($sql);

            $return_result = db_fetch_assoc($result);

            return AxisPro::SendResponse(["data" => $return_result], $format);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    public function save_reception()
    {
        try {

            $new_reception = true;

            if ($new_reception) {

                $customer_id = $_POST['customer_id'];
                $token = $_POST['token'];
                $display_customer = $_POST['display_customer'];
                $contact_person = $_POST['contact_person'];

                $customer_email = $_POST['customer_email'];
                $customer_trn = $_POST['customer_trn'];
                $customer_ref = $_POST['customer_ref'];
                $customer_iban = $_POST['customer_iban'];

                $radio_type = $_POST['GroupCompany'];


                if ($radio_type == '1') {
                    $customer_id = '1';
                }

                if ($radio_type == '2') {
                    $customer_mobile = $_POST['customer_mobile_company'];
                    $customer_email = $_POST['customer_company_email'];
                } else if ($radio_type == '1') {
                    $customer_mobile = $_POST['customer_eid_number'];
                    $customer_email = $_POST['customer_email'];

                }


                $errors = [];
                if (empty($display_customer))
                    $errors['display_cust_err'] = "Please choose a sub customer";

                if (empty($contact_person))
                    $errors['contact_person'] = "Please enter contact person name";

                if (empty($customer_id))
                    $errors['customer_id'] = "Please choose a customer";

                if (empty($customer_mobile))
                    $errors['customer_mobile'] = "Please enter customer mobile";
                // else if (!preg_match('/^[0-9]{10}$/', $customer_mobile))
                //     $errors['customer_mobile'] = "Mobile number must be valid (10 digits). Eg: 0512345678";

                if (empty($token))
                    $errors['token'] = "Please enter token number";

                // if (empty($customer_iban))
                //     $errors['customer_iban'] = "IBAN is mandatory";


                if (!empty($errors))
                    return AxisPro::SendResponse(['status' => 'FAIL', 'msg' => 'VALIDATION_FAILED', 'data' => $errors]);


                $sql = "INSERT INTO 0_axis_front_desk 
            (token,customer_id,display_customer,customer_mobile,customer_email,customer_trn,customer_iban,customer_ref,contact_person) 
            VALUES (
                " . db_escape($token) . ",$customer_id,
                " . db_escape($display_customer) . ",
                " . db_escape($customer_mobile) . ",
                " . db_escape($customer_email) . ",
                " . db_escape($customer_trn) . ",
                " . db_escape($customer_iban) . ",
                " . db_escape($customer_ref) . ",
                " . db_escape($contact_person) . ")";

                if (db_query($sql, "could not insert to front desk")) {
                    $sql_select = "select iban_no,mobile,debtor_email from 0_debtors_master where debtor_no ='" . $customer_id . "' ";
                    $debtor_data = db_fetch(db_query($sql_select));


                    $update_customer = "Update 0_debtors_master set ";
                    if ($debtor_data['iban_no'] == '') {
                        $update_customer .= " iban_no=" . db_escape($customer_iban) . " ";
                    }
                    if ($debtor_data['mobile'] == '') {
                        $update_customer .= " ,mobile=" . db_escape($customer_mobile) . " ";
                    }

                    if ($debtor_data['debtor_email'] == '') {
                        $update_customer .= " ,debtor_email=" . db_escape($customer_email) . " ";
                    }
                    $update_customer .= " where debtor_no='" . $customer_id . "'  AND debtor_no <> 1";
                    // echo $update_customer;

                    db_query($update_customer);
                }

            }
            return AxisPro::SendResponse(['status' => 'OK', 'msg' => 'Reception info saved']);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function get_suppliers()
    {
        $sql = "SELECT supplier_id, supp_ref, supp_name, curr_code, inactive,concat(supp_ref,' - ',supp_name) as supplier_name FROM 0_suppliers";
        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result);
    }


    public function process_voucher()
    {
        global $systypes_array;
        $type = $_POST['page_type'];
        $error_msg = '';
        if ($type == '1') {
            $limit = get_bank_account_limit($_POST['bnk_account'], $_POST['date_']);

            $amnt_chg = array_sum($_POST['tot_amount']);


            if ($limit !== null && floatcmp($limit, $amnt_chg) < 0) {
                $error_msg = sprintf(_("The total bank amount exceeds allowed limit (%s)."), price_format($limit));

            }
            if ($trans = check_bank_account_history($amnt_chg, $_POST['bank_account'], $_POST['date_'])) {

                if (isset($trans['trans_no'])) {
                    $error_msg = sprintf(_("The bank transaction would result in exceed of authorized overdraft limit for transaction: %s #%s on %s."),
                        $systypes_array[$trans['type']], $trans['trans_no'], sql2date($trans['trans_date']));
                }
            }
            if (!is_date($_POST['date_'])) {
                $error_msg = "The entered date for the payment is invalid.";
            } elseif (!is_date_in_fiscalyear($_POST['date_'])) {
                $error_msg = "The entered date is out of fiscal year or is closed for further data entry.";

            }
        } else {
            $error_msg = '';
        }


        if ($error_msg == '') {
            $type = $_POST['page_type'];
            $object = new items_cart($type, $trans_no = 0);

            $_payment_data = $_POST['payment_data'];

            $voucher_type = '';

            if ($type == '1') {
                $voucher_type = '1';
            }

            if ($type == '2') {
                $voucher_type = '2';
            }
            begin_transaction();
            $amount = '';
            foreach ($_payment_data as $d) {
                $_SESSION['journal_items']->axispro_subledger_code = $d['jv_from_sub'];

                if ($type == '1') {
                    $amount = $d['amount'];
                } else {
                    $amount = '-' . $d['amount'];
                }

                if ($d['dimension'] == '') {
                    $d['dimension'] = '0';
                }

                $gl_items[] = new gl_item($d['jv_from'], $d['dimension'], 0, $amount, '', '', $d['person_id'], '');

                $object->trans_type = $voucher_type;
                $object->line_items = '';
                $object->gl_items = $gl_items;
                $object->order_id = '';
                $object->from_loc = '';
                $object->to_loc = '';
                $object->tran_date = $_POST['v_date'];
                $object->doc_date = '';
                $object->event_date = '';
                $object->transfer_type = '';
                $object->increase = '';
                $object->memo_ = '';
                $object->branch_id = '';
                $object->reference = $_POST['v_refer'];
                $object->original_amount = '';
                $object->currency = '';
                $object->rate = '1';
                $object->source_ref = " ";
                $object->vat_category = " ";
                $object->tax_info = " ";
                $object->fixed_asset = " ";

            }

            $post_trans_no = '';
            if ($_POST['modify_voucher'] == '1') {
                $post_trans_no = $_POST['modify_trans_no'];
            } else {
                $post_trans_no = 0;
            }


            $trans = write_bank_transaction(
                $voucher_type, $post_trans_no, $_POST['v_from_bank_acc'],
                $object, Today(),
                $_POST['pay_to'], $_POST['head_person_id'], '0',
                $_POST['v_refer'], $_POST['being'], true, '', $_POST['pay_type'], $_POST['chq_no'], $_POST['cheq_date']);

            $trans_type = $trans[0];
            $trans_no = $trans[1];
            new_doc_date($_POST['v_date']);

            commit_transaction();

            $_SESSION['journal_items']->axispro_subledger_code = [];


            return AxisPro::SendResponse(['trans_no' => $trans_no, 'trans_type' => $trans_type]);
        } else {
            return AxisPro::SendResponse(['trans_no' => $error_msg, 'trans_type' => 'error']);
        }

    }


    public function get_edit_voucher_data()
    {
        $sql = "SELECT * FROM 0_gl_trans WHERE type_no='" . $_POST['trans_no'] . "' ";
        if ($_POST['type'] == '1') {
            $sql .= "and amount > 0 and type='1'";
        }
        if ($_POST['type'] == '2') {
            $sql .= "and amount < 0 and type='2'";
        }


        $result_d = db_query($sql);


        $select = '';
        $html = '';
        $i = 0;

        while ($myrow_data = db_fetch($result_d)) {

            //print_r($myrow_data['account'].'----');

            $ledger_id = $myrow_data['account'];
            /*---------------------Sub_ledger_or_customer_names--------------------*/
            $sub_type = is_subledger_account($ledger_id);
            if ($sub_type == '') {
                $sql_sub = "SELECT code,name
            FROM 0_sub_ledgers where ledger_id='" . $ledger_id . "' ";

                $result_sub = db_query($sql_sub);

                $select = '<select class="form-control" id="ddl_from_sub_' . $i . '" style="width: 188px;"><option value="0">---Select Sub Ledger---</option>';
                $selection = '';
                while ($myrow_sub = db_fetch($result_sub)) {
                    if ($myrow_sub['code'] == $myrow_data['axispro_subledger_code']) {
                        $selection = 'selected="selected"';
                    } else {
                        $selection = '';
                    }
                    $select .= '<option value="' . $myrow_sub['code'] . '" ' . $selection . '>' . $myrow_sub['name'] . '</option>';
                }
                $select .= '</select>';
            } else {
                if ($sub_type > 0) {
                    $sql_sub = "SELECT DISTINCT d.debtor_no as id, debtor_ref as name 
                FROM  
                0_debtors_master d,
                0_cust_branch c
                WHERE d.debtor_no=c.debtor_no AND NOT d.inactive AND c.receivables_account='" . $ledger_id . "'";
                } else {
                    $sql_sub = "SELECT supplier_id as id, supp_ref as name 
                FROM  0_suppliers s
                WHERE NOT s.inactive AND s.payable_account='" . $ledger_id . "' ";
                }

                $result_sub = db_query($sql_sub);

                $select = '<select class="form-control" id="ddl_person_' . $i . '" style="width: 188px;"><option value="0">---Select Counter Party---</option>';
                $selection = '';
                while ($myrow_sub = db_fetch($result_sub)) {
                    if ($myrow_sub['code'] == $myrow_data['axispro_subledger_code']) {
                        $selection = 'selected="selected"';
                    } else {
                        $selection = '';
                    }
                    $select .= '<option value="' . $myrow_sub['id'] . '" ' . $selection . '>' . $myrow_sub['name'] . '</option>';
                }
                $select .= '</select>';


            }

            /*-----------------------------END-------------------------------------*/

            $html .= '<tr class="calc" id="tr_' . $i . '">
        <td> <select id="ddl_from_acc_' . $i . '" class="form-control kt-select2 ap-select2 ap_service_select_from_' . $i . ' ClsDispalyOrHide" onchange="getFromSubAccounts(this,' . $i . ');" style="width: 230px;" name="service" id="service"> 
        <option value="">--</option>';
            $selection = '';
            $sql = "SELECT chart.account_code,CONCAT(chart.account_code,' - ',chart.account_name) AS accname, chart.inactive, type.id
        FROM 0_chart_master chart,0_chart_types type
        WHERE chart.account_type=type.id";
            $result = db_query($sql);
            while ($myrow = db_fetch($result)) {
                if ($myrow['account_code'] == $myrow_data['account']) {
                    $selection = 'selected="selected"';
                } else {
                    $selection = '';
                }
                $html .= '<option value="' . $myrow['account_code'] . '" ' . $selection . '>' . $myrow['accname'] . '</option>';
            }
            $html .= ' </select>
        </td>
        <td><div class="From_sub_acc_' . $i . '" id="ddl_sub_from_account_' . $i . '">';

            $html .= $select;
            $html .= ' </div></td>';

            $html .= '<td>';
            $html .= ' <select id="ddl_dimen_acc_' . $i . '" class="form-control kt-select2 ap-select2 dimension_select_from_' . $i . '"  style="width: 230px;">
        <option value="">--</option>';
            $sql_dim = "SELECT id,name FROM 0_dimensions";
            $result_dimen = db_query($sql_dim);
            $selection_dimen = '';
            while ($myrow_dimen = db_fetch($result_dimen)) {
                if ($myrow_dimen['id'] == $myrow_data['dimension_id']) {
                    $selection_dimen = 'selected="selected"';
                } else {
                    $selection_dimen = '';
                }
                $html .= '<option value="' . $myrow_dimen['id'] . '" ' . $selection_dimen . '>' . $myrow_dimen['name'] . '</option>';
            }
            $html .= '</td>';

            $html .= ' <td><input type="text" id="txtAmount_' . $i . '" style="width: 69px;" class="ClsDispalyOrHide clstaxAmount" onkeyup="this.value=this.value.replace(/[^0-9.]/g,\'\');" onchange="display_price(' . $i . ')" value="' . abs($myrow_data['amount']) . '"/></td>
        <td><textarea id="txt_comment_' . $i . '" class="ClsDispalyOrHide" alt="' . $i . '" >' . $myrow_data['memo_'] . '</textarea></td>
        <td><input type="submit" value="Remove" class="btn btn-info btnSubmit" alt="' . $i . '"/>

        </td>

        </tr>';


            $i++;
        }

        return AxisPro::SendResponse(['trans_no' => $html, 'next_index' => $i]);

    }


    public function get_headings_data($trans_no, $type)
    {
        $sql = "SELECT a.ref,a.payment_type,b.account AS payto,a.bank_act,a.person_type_id,a.cheq_no,a.cheq_date,a.person_id
    FROM 0_bank_trans AS a
    INNER JOIN 0_gl_trans AS b ON a.trans_no=b.type_no
    WHERE b.amount < 0 AND b.type_no='$trans_no' and a.type='$type'";
        // echo $sql;
        $result = db_query($sql);
        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return $return_result;
    }

    function get_records_from_table($table, $cols)
    {

        try {

            $cols = implode(',', $cols);

            $sql = "SELECT " . $cols . " FROM $table";
            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch_assoc($result))
                $return_result[] = $myrow;

            return $return_result;
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    public function get_bnk_balance()
    {
        $to = add_days(Today(), 1);
        $bal = get_balance_before_for_bank_account($_POST['bank_id'], $to);

        return AxisPro::SendResponse($bal);
    }

    public function get_dimensions()
    {
        $sql = "SELECT id,name FROM 0_dimensions";
        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch($result)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result);
    }


    public function get_dimen_id_againstuser()
    {
        $dimension_id = '';
        if ($_POST['hdn_modify'] == '') {
            $sql = "SELECT dflt_dimension_id FROM 0_users where id='" . $_SESSION['wa_current_user']->user . "'";
            $result = db_query($sql);
            $dflt_dimension_id = db_fetch_row($result);
            $dimension_id = $dflt_dimension_id[0];

        } else {
            $dimension_id = '0';
        }

        return AxisPro::SendResponse(['dim_id' => $dimension_id]);

    }


    public function get_purchase_items($format = 'json')
    {
//        $sql = "select stock_id,description from 0_stock_master where NOT no_purchase";
        $sql = "SELECT s.stock_id,s.description,c.description AS category from 0_stock_master s 
    LEFT JOIN 0_stock_category c ON c.category_id=s.category_id 
    where NOT s.no_purchase";
        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch($result)) {

            $myrow['description'] = $myrow['description'] . " [" . $myrow['category'] . "]";

            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result, $format);
    }


    public function getPurchaseRequests()
    {

        $sql = "select * from 0_purchase_requests WHERE 1=1 ";


        $user_id = $_SESSION['wa_current_user']->user;
        $user_info = get_user($user_id);

        $sql .= " AND (created_by = $user_id OR 
        staff_mgr_id = " . $user_id . " OR 
        purch_mgr_id=" . $user_id . ")";


        if (!empty($_POST['fl_ref']))
            $sql .= " AND reference = " . db_escape($_POST['fl_ref']);

        if (!empty($_POST['fl_start_date']))
            $sql .= " AND DATE(created_at) >= " . db_escape(date2sql($_POST['fl_start_date']));

        if (!empty($_POST['fl_end_date']))
            $sql .= " AND DATE(created_at) <= " . db_escape(date2sql($_POST['fl_end_date']));

        if (!empty($_POST['fl_requested_by']))
            $sql .= " AND created_by = " . $_POST['fl_requested_by'];

        if (!empty($_POST['fl_status'])) {

            $fl_status = $_POST['fl_status'];

            if ($fl_status == 'WFSMA')//Waiting for staff manager approval
                $sql .= " AND staff_mgr_action=0";

            if ($fl_status == 'WFPMA')//Waiting for Purchase manager approval
                $sql .= " AND staff_mgr_action=1 and purch_manager_action=0";

            if ($fl_status == 'RBSM')//Rejected by staff manager
                $sql .= " AND staff_mgr_action=2";

            if ($fl_status == 'ABPM')//Approved by Purchase manager
                $sql .= " AND purch_mgr_action=1";

            if ($fl_status == 'RBPM')//Rejected by Purchase manager
                $sql .= " AND purch_mgr_action=2";

            if ($fl_status == 'POC')//Purchase Order Created
                $sql .= " AND po_id<>0";


        }

        $sql .= "ORDER BY staff_mgr_action ASC";


        $total_count_sql = "select count(*) as cnt from ($sql) as tmpTable";
        $total_count_exec = db_fetch_assoc(db_query($total_count_sql));
        $total_count = $total_count_exec['cnt'];

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = 200;
        $offset = ($page * $perPage) - $perPage;


        $sql = $sql . " LIMIT $perPage OFFSET $offset";

        $result = db_query($sql);
        $report = [];
        while ($myrow = db_fetch_assoc($result))
            $report[] = $myrow;


        return AxisPro::SendResponse(
            [
                'rep' => $report,
                'total_rows' => $total_count,
                'pagination_link' => AxisPro::paginate($total_count),
                'users' => $this->get_key_value_records('0_users', 'id', 'user_id'),
                'aggregates' => $total_count_exec,]
        );

    }


    public function getPurchaseRequestStatus()
    {

    }

    public function storePurchReqLog($req_id, $desc)
    {

        try {

            $user_id = $_SESSION['wa_current_user']->user;

            db_insert('0_purch_request_log', [
                'user_id' => $user_id,
                'req_id' => $req_id,
                'description' => db_escape($desc)
            ]);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    public function handleNewPurchaseRequest()
    {

        try {

            begin_transaction();

            $edit_id = isset($_POST['edit_id']) ? $_POST['edit_id'] : null;

            $memo = $_POST['memo'];
            $user_id = $_SESSION['wa_current_user']->user;

            $user_info = get_user($user_id);

            if (empty($user_info['purch_req_send_to_level_one']))
                return AxisPro::SendResponse(['status' => 'FAIL', 'msg' => 'Purchase Request Send to Level One Not Set.']);


            $return_msg = "";

            if (!empty($edit_id)) {

                $editing_request = $this->getPurchaseRequest($edit_id, 'array');

                $revision_count = intval($editing_request['req']['revision_count']) + 1;
                $old_ref = $editing_request['req']['reference'];
                $explode_ref = explode('-', $old_ref);

                $rev_part = 0;
                if (isset($explode_ref[1]))
                    $rev_part = intval($explode_ref[1]) + 1;


                $ref = $explode_ref[0] . "-" . $rev_part;


                $array = [
                    'memo' => db_escape($memo),
                    'last_revision_by' => db_escape($user_id),
                    'revision_count' => $revision_count,
                    'reference' => db_escape($ref),
                ];


                if ($editing_request['req']['staff_mgr_id'] == $user_id) {
                    //Staff manager is editing this request

                } else if ($editing_request['req']['purch_mgr_id'] == $user_id) {
                    //purchase manager is editing this request
                } else if ($editing_request['req']['created_by'] == $user_id) {
                    //Created user is editing this request
                }

                $purchase_req_id = $edit_id;

                db_update('0_purchase_requests', $array, ["id=$purchase_req_id"]);

                $this->storePurchReqLog($purchase_req_id, "Edited / Revised");

                $sql = "DELETE FROM 0_purchase_request_items WHERE req_id=$purchase_req_id";
                db_query($sql);


                $return_msg = "Purchase Request Revised";


            } else {

                $array = [
                    'memo' => db_escape($memo),
                    'created_by' => db_escape($user_id),
                    'staff_mgr_id' => $user_info['purch_req_send_to_level_one']
                ];

                db_insert('0_purchase_requests', $array);

                $purchase_req_id = db_insert_id();
                $this->storePurchReqLog($purchase_req_id, "Purchase Request Created");

                //Add Notification
                AxisNotification::insert([
                    'description' => 'New purchase request from - ' . $_SESSION['wa_current_user']->loginname,
                    'link' => 'purchase_request_list.php',
                    'users' => [
                        $user_info['purch_req_send_to_level_one']
                    ]
                ]);


                $new_ref_int = intval($purchase_req_id);
                $new_ref_numeric_part = str_pad($new_ref_int, 3, '0', STR_PAD_LEFT);
                $ref = "PR/" . $new_ref_numeric_part;

                $sql = "update 0_purchase_requests set reference = " . db_escape($ref) . " WHERE id=$purchase_req_id";
                db_query($sql);

                $return_msg = "New Purchase Request Placed";


            }


            //insert items

            $items = $_POST['items'];

            $insert_items_array = [];

            foreach ($items as $row) {

                $temp_array = [
                    'req_id' => $purchase_req_id,
                    'stock_id' => db_escape($row['stock_id']),
                    'description' => db_escape($row['description']),
                    'qty' => $row['qty']

                ];

                array_push($insert_items_array, $temp_array);

            }

            if (!empty($insert_items_array))
                db_insert_batch('0_purchase_request_items', $insert_items_array);


            commit_transaction();

            return AxisPro::SendResponse(['status' => 'SUCCESS', 'msg' => $return_msg]);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    public function getPurchaseRequest($id = null, $format = 'json')
    {

        try {

            if (empty($id))
                $id = $_GET['id'];

            $sql = "SELECT * FROM 0_purchase_requests where id = $id";
            $result = db_fetch_assoc(db_query($sql));
            $mr = $result;

            $sql = "SELECT items.*,stk.description item_name,stk.purchase_cost 
            FROM 0_purchase_request_items items 
            LEFT JOIN 0_stock_master stk ON stk.stock_id = items.stock_id
            where items.req_id = $id ";


            $result = db_query($sql);
            $items = [];
            while ($myrow = db_fetch_assoc($result)) {
                $myrow['qty_in_stock'] = get_qoh_on_date($myrow['stock_id']);

                $qty_to_be_ordered = 0;

                if ($myrow['qty'] > $myrow['qty_in_stock'])
                    $qty_to_be_ordered = $myrow['qty'] - $myrow['qty_in_stock'];

                if ($qty_to_be_ordered < 0)
                    $qty_to_be_ordered = 0;

                $myrow['qty_to_be_ordered'] = $qty_to_be_ordered;

                $items[] = $myrow;
            }


            $sql = "SELECT log.req_id,log.description,log.created_at,usr.user_id FROM 0_purch_request_log log 
            LEFT JOIN 0_users usr ON usr.id = log.user_id
            WHERE log.req_id = $id ORDER BY log.created_at DESC";

//            pp($sql);

            $result = db_query($sql);
            $log = [];
            while ($myrow = db_fetch_assoc($result)) {
                $log[] = $myrow;
            }


            return AxisPro::SendResponse(['req' => $mr, 'items' => $items, 'log' => $log], $format);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    public function purchaseRequestActionHandler()
    {

        try {

            $req_id = $_POST['req_id'];
            $actionToUpdate = $_POST['action'];

            $req_info = $this->getPurchaseRequest($req_id, 'array');
            $req_row = $req_info['req'];

            $msg = "Request Approved";

            if ($actionToUpdate == 2)
                $msg = "Request Rejected";

            if (!empty($req_row['staff_mgr_action'])) {
                //Staff Manager is already approved, so the action is now taken by purchase manager
                $update_set = [
                    'purch_mgr_action' => $actionToUpdate,
                    'purch_mgr_actioned_at' => db_escape(date("Y-m-d H:i:s"))
                ];
            } else {
                //Staff Manager not approved, so the action is now taken by staff manager


                $user_id = $_SESSION['wa_current_user']->user;

                $user_info = get_user($user_id);

                if (empty($user_info['purch_req_send_to_level_two']))
                    return AxisPro::SendResponse(['status' => 'FAIL', 'msg' => 'Level 2 Send To Not Set']);


                $update_set = [
                    'staff_mgr_action' => $actionToUpdate,
                    'staff_mgr_actioned_at' => db_escape(date("Y-m-d H:i:s")),
                    'purch_mgr_id' => $user_info['purch_req_send_to_level_two']
                ];

            }


            $notification_desc = 'A new Purchase Request needs your attention';
            $notify_user = $user_info['purch_req_send_to_level_two'];

            if ($actionToUpdate == 2) {
                $notification_desc = "Your purchase request (" . $req_info['req']['reference'] . ") is rejected";
                $notify_user = $req_info['req']['created_by'];
            }


            //Add Notification
            AxisNotification::insert([
                'description' => $notification_desc,
                'link' => 'purchase_request_list.php',
                'users' => [
                    $notify_user
                ]
            ]);

            db_update('0_purchase_requests', $update_set, ["id=$req_id"]);

            $this->storePurchReqLog($req_id, $msg);


            return AxisPro::SendResponse(['status' => 'SUCCESS', 'msg' => $msg]);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function handleNewMaterialIssue()
    {

        try {

            $user_id = $_SESSION['wa_current_user']->user;

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function getCountAwaitingPurchaseRequests()
    {
        try {

            $user_id = $_SESSION['wa_current_user']->user;

            $sql = "select count(*) as cnt from 0_purchase_requests WHERE 1=1 ";
            $sql .= " AND ( ";
            $sql .= " ( staff_mgr_id = " . $user_id . " and staff_mgr_action=0) OR 
                ( purch_mgr_id = " . $user_id . " and purch_mgr_action=0)";

            $sql .= " )";


            $result = db_fetch(db_query($sql));

            return $result['cnt'];


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    public function handleNewPOTermsAndCondition()
    {

        try {


            $title = $_POST['title'];
            $desc = $_POST['desc'];

            $array = [
                "title" => db_escape($title),
                "description" => db_escape($desc)
            ];

            if (empty(trim($title)))
                return AxisPro::SendResponse(['status' => 'FAIL', 'msg' => 'Please enter title']);

            if (empty(trim($desc)))
                return AxisPro::SendResponse(['status' => 'FAIL', 'msg' => 'Please enter description']);


            db_insert('0_po_terms_and_conditions', $array);

            return AxisPro::SendResponse(['status' => 'SUCCESS', 'msg' => 'PO - Terms and Conditions Added']);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    public function handleDeletePOTermsAndCondition()
    {

        try {

            $id = $_POST['id'];
            $sql = "delete from 0_po_terms_and_conditions where id=$id";
            db_query($sql);
            return AxisPro::SendResponse(['status' => 'SUCCESS', 'msg' => 'PO - Terms and Conditions Deleted']);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function pay_invoice($format = 'json')
    {

        try {

            global $Refs;

            begin_transaction();

            $curr_user = get_user($_SESSION["wa_current_user"]->user);

            $dim_id = $_POST['dim_id'];

//        $invoice_no = $_POST['trans_no'];
            $customer_id = $_POST['customer_id'];
            $date_ = $_POST['tran_date'];
//            $date_ = Today();
            $amount = input_num('amount');
            $discount = input_num('discount');
            $bank_charge = input_num('bank_charge');
            $payment_method = $_POST['payment_method'];
            $bank_account = $_POST['bank_acc'];//IF CREDIT CARD

            $round_off = input_num('rounded_difference');


            if ($payment_method == "Cash") {

                $curr_user = get_user($_SESSION["wa_current_user"]->user);
                $bank_account = $curr_user['cashier_account'];

                if (empty($bank_account))
                    return AxisPro::SendResponse(["status" => "FAIL",
                        "msg" => "No Cashier-Account set for This User"], $format);

            }
            else {

                if (empty($bank_account))
                    return AxisPro::SendResponse(["status" => "FAIL",
                        "msg" => "No Payment Account Selected"], $format);

            }

            if (empty($customer_id))
                return AxisPro::SendResponse(["status" => "FAIL",
                    "msg" => "No Customer Selected"], $format);

            if (empty($dim_id))
                return AxisPro::SendResponse(["status" => "FAIL",
                    "msg" => "No Cost-Center set for This User"], $format);


//            $invoice_info = get_customer_trans($invoice_no, ST_SALESINVOICE);
//            $max_allocatable_amount = round($invoice_info['ov_amount'] + $invoice_info['ov_gst'] - $invoice_info['alloc']);
//
//
//            if (round($amount) > $max_allocatable_amount) {
//                return AxisPro::SendResponse(["status" => "FAIL",
//                    "msg" => "Maximum allocatable amount Exceeded. Please check the amount"], $format);
//            }

            $branch = db_fetch(db_query(get_sql_for_customer_branches($customer_id)));
            $customer_branch = $branch['branch_code'];

            $alloc_invoices = $_POST['alloc_invoices'];


            $is_advance_rcpt = true;
            $invoice_info = [];
            foreach ($alloc_invoices as $alloc) {
                if ($alloc['amount'] > 0) {
                    $is_advance_rcpt = false;
                    $invoice_info = get_customer_trans($alloc['trans_no'], ST_SALESINVOICE);

                    if (!empty($invoice_info)) {
                        $dim_id = $invoice_info["dimension_id"];
                    }
                }
            }


            $memo = "";
            if ($is_advance_rcpt)
                $memo = "ADVANCE RECEIPT";


            $ref = get_next_payment_ref($dim_id);

            $pmtno = write_customer_payment(0, $customer_id,
                $customer_branch, $bank_account, $date_,
                $ref,
                $amount, $discount, $memo, 0, $bank_charge, $amount, '', $dim_id, $round_off);


            foreach ($alloc_invoices as $alloc) {

                if ($alloc['amount'] > 0) {

                    add_cust_allocation($alloc['amount'], ST_CUSTPAYMENT, $pmtno, ST_SALESINVOICE, $alloc['trans_no'], $customer_id, $date_);
                    update_debtor_trans_allocation(ST_SALESINVOICE, $alloc['trans_no'], $customer_id);

                }

            }


            //TODO: Loop
//            add_cust_allocation($amount, ST_CUSTPAYMENT, $pmtno, ST_SALESINVOICE, $invoice_no, $customer_id, $date_);
//            update_debtor_trans_allocation(ST_SALESINVOICE, $invoice_no, $customer_id);
            //TODO: loop


            update_debtor_trans_allocation(ST_CUSTPAYMENT, $pmtno, $customer_id);


            $sql = "update 0_debtor_trans  set round_of_amount=$round_off, payment_method = " . db_escape($payment_method) . " where type = 12 and trans_no=$pmtno";
            db_query($sql);


            runAutomaticAllocation($customer_id);

            commit_transaction();

            return AxisPro::SendResponse(["status" => "OK", "payment_no" => $pmtno, "msg" => "Payment Done"], $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    public function find_invoice($format = 'json')
    {

        try {


            $ref = $_GET['ref'];
            $dim_id = $_GET['dim_id'];

            $sql = "SELECT trans.*, trans.ov_amount+trans.ov_gst as total_amount,  

            (trans.ov_amount+ov_gst-trans.alloc) as remaining_amount,
            cust.name from 0_debtor_trans trans 
            left join 0_debtors_master cust on cust.debtor_no  = trans.debtor_no 
            WHERE barcode = " . db_escape(trim($ref)) . " OR reference = " . db_escape($ref) . " and trans.type=10 AND trans.ov_amount!=0";

            if (!empty($dim_id))
                $sql .= " AND trans.dimension_id = $dim_id ";


            $result = db_fetch_assoc(db_query($sql));

            $result['total_amount'] = round2($result['total_amount'], user_price_dec());
            $result['remaining_amount'] = round2($result['remaining_amount'], user_price_dec());

            $result['tran_date'] = sql2date($result['tran_date']);

            return AxisPro::SendResponse($result, $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    public function get_bank_accounts($format = "json")
    {

        try {

            $account_type = $_GET['acc_type'];
            $sql = "SELECT * FROM 0_bank_accounts";

            if (!empty($account_type))
                $sql .= " WHERE account_type in ($account_type)";

            $result = db_query($sql);
            $return_result = [];
            while ($myrow = db_fetch_assoc($result)) {

                $return_result[] = $myrow;

            }


            if ($account_type == "0,3") {

                $return_result = [];
                $curr_user = get_user($_SESSION["wa_current_user"]->user);
                $bank_account_id = $curr_user['cashier_account'];
                $array = [];
                if (!empty($bank_account_id)) {
                    $bank_info = get_bank_account($bank_account_id);
                    $array = [
                        'id' => $bank_account_id,
                        'bank_account_name' => $bank_info['bank_account_name']
                    ];

                    $return_result[] = $array;

                }
            }


            return AxisPro::SendResponse($return_result, $format);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


//    public function pay_invoice($format = 'json')
//    {
//
//        try {
//
//            global $Refs;
//
//            begin_transaction();
//
////        $invoice_no = $_POST['trans_no'];
//            $customer_id = $_POST['customer_id'];
//            $date_ = $_POST['tran_date'];
////            $date_ = Today();
//            $amount = input_num('amount');
//            $discount = input_num('discount');
//            $bank_charge = input_num('bank_charge');
//            $payment_method = $_POST['payment_method'];
//            $bank_account = $_POST['bank_acc'];//IF CREDIT CARD
//
//            if ($payment_method == "Cash") {
//
//                $curr_user = get_user($_SESSION["wa_current_user"]->user);
//                $bank_account = $curr_user['cashier_account'];
//
//                if (empty($bank_account))
//                    return AxisPro::SendResponse(["status" => "FAIL",
//                        "msg" => "No Cashier-Account set for This User"], $format);
//
//            }
//
//
////            $invoice_info = get_customer_trans($invoice_no, ST_SALESINVOICE);
////            $max_allocatable_amount = round($invoice_info['ov_amount'] + $invoice_info['ov_gst'] - $invoice_info['alloc']);
////
////
////            if (round($amount) > $max_allocatable_amount) {
////                return AxisPro::SendResponse(["status" => "FAIL",
////                    "msg" => "Maximum allocatable amount Exceeded. Please check the amount"], $format);
////            }
//
//            $branch = db_fetch(db_query(get_sql_for_customer_branches($customer_id)));
//            $customer_branch = $branch['branch_code'];
//
//            $pmtno = write_customer_payment(0, $customer_id,
//                $customer_branch, $bank_account, $date_,
//                $Refs->get_next(ST_CUSTPAYMENT, null, array('customer' => $customer_id,
//                    'branch' => $customer_branch, 'date' => $date_)),
//                $amount, $discount, "", 0, $bank_charge);
//
//
//            $alloc_invoices = $_POST['alloc_invoices'];
//
//            foreach ($alloc_invoices as $alloc) {
//
//                if ($alloc['amount'] > 0) {
//
//                    add_cust_allocation($alloc['amount'], ST_CUSTPAYMENT, $pmtno, ST_SALESINVOICE, $alloc['trans_no'], $customer_id, $date_);
//                    update_debtor_trans_allocation(ST_SALESINVOICE, $alloc['trans_no'], $customer_id);
//
//                }
//
//            }
//
//
//            //TODO: Loop
////            add_cust_allocation($amount, ST_CUSTPAYMENT, $pmtno, ST_SALESINVOICE, $invoice_no, $customer_id, $date_);
////            update_debtor_trans_allocation(ST_SALESINVOICE, $invoice_no, $customer_id);
//            //TODO: loop
//
//
//            update_debtor_trans_allocation(ST_CUSTPAYMENT, $pmtno, $customer_id);
//
//
//            $sql = "update 0_debtor_trans set payment_method = " . db_escape($payment_method) . " where type = 12 and trans_no=$pmtno";
//            db_query($sql);
//
//            commit_transaction();
//
//            return AxisPro::SendResponse(["status" => "OK", "payment_no" => $pmtno, "msg" => "Payment Done"], $format);
//
//        } catch (Exception $e) {
//            return AxisPro::catchException($e);
//        }
//
//
//    }
//
//
//    public function get_bank_accounts($format = "json")
//    {
//
//        try {
//
//            $account_type = $_GET['acc_type'];
//            $sql = "SELECT * FROM 0_bank_accounts WHERE 1=1";
//
//            if (!empty($account_type))
//                $sql .= " AND account_type in ($account_type)";
//
//            $result = db_query($sql);
//            $return_result = [];
//            while ($myrow = db_fetch_assoc($result)) {
//
//                $return_result[] = $myrow;
//
//            }
//
//
//            if ($account_type == "0,3") {
//
//                $return_result = [];
//                $curr_user = get_user($_SESSION["wa_current_user"]->user);
//                $bank_account_id = $curr_user['cashier_account'];
//                $array = [];
//                if (!empty($bank_account_id)) {
//                    $bank_info = get_bank_account($bank_account_id);
//                    $array = [
//                        'id' => $bank_account_id,
//                        'bank_account_name' => $bank_info['bank_account_name']
//                    ];
//
//                    $return_result[] = $array;
//
//                }
//            }
//
//
//            return AxisPro::SendResponse($return_result, $format);
//
//
//        } catch (Exception $e) {
//            return AxisPro::catchException($e);
//        }
//
//    }

    public function get_unpaid_invoices($format = "json")
    {

        try {

            $debtor_no = $_GET['debtor_no'];
            $except_trans_no = $_GET['except_trans_no'];

            $dim_id = $_REQUEST['dim_id'];

            $sql = "SELECT *,ROUND((ov_amount+ov_gst),2) as total_amount,ROUND((ov_amount+ov_gst-alloc),2) as remaining_amount
            FROM 0_debtor_trans trans 
            WHERE debtor_no=$debtor_no AND (ov_amount+ov_gst)> alloc 
            AND ov_amount <>0 AND type=10 AND ROUND(ov_amount+ov_gst) <> ROUND(alloc) ";

            if (!empty($except_trans_no))
                $sql .= " AND trans_no <>$except_trans_no";

            if (!empty($dim_id))
                $sql .= " AND trans.dimension_id = $dim_id ";

            $result = db_query($sql);
            $return_result = [];
            while ($myrow = db_fetch_assoc($result)) {

                $myrow['total_amount'] = round2($myrow['total_amount'], user_price_dec());
                $myrow['remaining_amount'] = round2($myrow['remaining_amount'], user_price_dec());

                $return_result[] = $myrow;

            }


            return AxisPro::SendResponse($return_result, $format);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    function get_customer_balance($cust_id = null, $format = "json")
    {
        try {

            $customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : 0;

            if (empty($customer_id))
                $customer_id = $cust_id;

            $sql = get_sql_for_customer_allocation_inquiry(begin_fiscalyear(), Today(),
                $customer_id, null, null);


            // $result = db_fetch_assoc(db_query($sql));
            $result = db_query($sql, "could not get customer");

            $prepaid_bal = 0;
            $out_standing_bal = 0;
            while ($row = db_fetch($result)) {
                $balance = ($row["type"] == ST_JOURNAL && $row["TotalAmount"] < 0 ? -$row["TotalAmount"] :
                        $row["TotalAmount"]) - $row["Allocated"];
                if ($row["type"] == ST_CUSTCREDIT && $row['TotalAmount'] > 0) {
                    /*its a credit note which could have an allocation */
                    $prepaid_bal += $balance;
                } elseif ($row["type"] == ST_JOURNAL && $row['TotalAmount'] < 0) {
                    $prepaid_bal += $balance;
                } elseif (($row["type"] == ST_CUSTPAYMENT || $row["type"] == ST_BANKDEPOSIT) &&
                    (floatcmp($row['TotalAmount'], $row['Allocated']) >= 0)) {
                    /*its a receipt  which could have an allocation*/
                    $prepaid_bal += $balance;
                } elseif ($row["type"] == ST_CUSTPAYMENT && $row['TotalAmount'] <= 0) {
                    /*its a negative receipt */
                    $prepaid_bal += 0;
//        return '';
                } elseif (($row["type"] == ST_SALESINVOICE && ($row['TotalAmount'] - $row['Allocated']) > 0) || $row["type"] == ST_BANKPAYMENT) {
                    $out_standing_bal += $balance;
                } else {
                    $out_standing_bal += $balance;
                }

            }

            $result = [
                'customer_id' => $customer_id,
                'customer_balance' => $prepaid_bal - $out_standing_bal
            ];

            // $result['customer_balance'] = $prepaid_bal-$out_standing_bal;
            // $result['customer_id'] = $customer_id;

            return AxisPro::SendResponse($result, $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    function check_admin_password($format = "json")
    {
        try {

            $password = md5($_POST['password']);
            $discount = $_POST['discount'];
            $user = 'admin';

            $sql = "select id from 0_users where user_id='$user' and password='$password'";


            // $result = db_fetch_assoc(db_query($sql));
            $result = db_query($sql, "could not get admin user");

            $status = false;

            while ($row = db_fetch($result)) {
                if ($row["id"]) {
                    $status = true;
                }
            }

            $result = [
                'discount' => $discount,
                'status' => $status
            ];

            // $result['customer_balance'] = $prepaid_bal-$out_standing_bal;
            // $result['customer_id'] = $customer_id;

            return AxisPro::SendResponse($result, $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    public function decide()
    {
        if ($_POST['btnClick'] == 'csv') {
            $this->export_csv($_POST);
        }

        if ($_POST['btnClick'] == 'pdf') {
            $this->export_pdf($_POST);
        }
    }

    public function export_csv($data)
    {

        $sql = PrepareQuery::ServiceReport($data);

        $result = db_query($sql);


        $filename = 'SERVICE_REPORT_' . date('Ymd') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");

        $file = fopen('php://output', 'w');

        $mapped_array = array(
            'col_invoice_number' => "INVOICE NUMBER",
            'col_tran_date' => "INVOICE DATE",
            'col_invoice_type' => "CARD TYPE",
            'col_stock_id' => "STOCK ID",
            'col_service' => "SERVICE NAME",
            'col_category' => "CATEGORY",
            'col_customer' => "CUSTOMER",
            'col_sales_man' => "SALES MAN",
            'col_display_customer' => "DISPLAY CUSTOMER",
            'col_customer_mobile' => "CUSTOMER MOBILE",
            'col_customer_email' => "CUSTOMER EMAIL",
            'col_quantity' => "QUANTITY",
            'col_unit_price' => "SERVICE CHARGE",
            'col_total_price' => "TOTAL SERVICE CHARGE",
            'col_extra_service_charge' => "Extra/Round Off Charge",
            'col_total_tax' => "TOTAL VAT",
            'col_govt_fee' => "GOVT.FEE",
            'col_govt_bank' => "GOVT.BANK",
            'col_bank_service_charge' => "BANK SERVICE CHARGE",
            'col_bank_service_charge_vat' => "BANK SERVICE CHARGE VAT",
            'col_pf_amount' => "OTHER CHARGE",
            'col_total_govt_fee' => "TOTAL GOVT.FEE",
            'col_transaction_id' => "BANK REFERENCE NUMBER",
            'col_ed_transaction_id' => "MB/ST/DW-ID",
            'col_application_id' => "APPLICATION ID / RECEIPT ID",
            'col_ref_name' => "REF.NAME",
            'col_employee_commission' => "EMPLOYEE COMMISSION",
            'col_customer_commission' => "CUSTOMER COMMISSION",
            'col_line_discount_amount' => "DISCOUNT AMOUNT",
            'col_reward_amount' => "REWARD AMOUNT",
            'col_payment_status' => "PAYMENT STATUS",
            'col_created_by' => "EMPLOYEE",
            'col_employee_name' => "EMPLOYEE NAME",
            'col_line_total' => "LINE TOTAL",
            'col_invoice_total' => "INVOICE TOTAL",
            'col_net_service_charge' => "NET SERVICE CHARGE"
        );

        $cusrtom_data = '';
        $header_one = [];


        if (isset($_POST['custom_report_hdn_id']) && !empty(trim($_POST['custom_report_hdn_id']))) {

            $sql = "SELECT * FROM 0_custom_reports WHERE id=" . $_POST['custom_report_hdn_id'];
            $custom_report = db_fetch_assoc(db_query($sql));
            $custom_report['params'] = htmlspecialchars_decode($custom_report['params']);
            $cusrtom_data = json_decode($custom_report['params']);

            $before_header = [];

            $push_data = [];
            $header_one[] = "Report Name :" . $cusrtom_data->custom_report_name;
            fputcsv($file, $header_one);
            foreach ($cusrtom_data as $key => $val) {
                if (array_key_exists($key, $mapped_array)) {
                    array_push($before_header, $key);
                    array_push($push_data, str_replace("col_", "", $key));


                }
            }


            if (in_array('col_total_price', $before_header)) {

                array_push($before_header, 'col_total_tax');
                array_push($push_data, str_replace("col_", "", 'col_total_tax'));
            }


            $header = [];
            foreach ($mapped_array as $index => $key_vals) {
                if (in_array($index, $before_header)) {
                    array_push($header, $key_vals);

                }

            }


        } else {
            $header = array("INVOICE NUMBER", "INVOICE DATE", "CARD TYPE", "STOCK ID", "SERVICE NAME", "CATEGORY", "CUSTOMER", "SALES MAN", "DISPLAY CUSTOMER", "CUSTOMER MOBILE", "CUSTOMER EMAIL", "QUANTITY"
            , "SERVICE CHARGE", "TOTAL SERVICE CHARGE", "Extra/Round Off Charge", "TOTAL VAT", "GOVT.FEE", "GOVT.BANK", "BANK SERVICE CHARGE", "BANK SERVICE CHARGE VAT", "OTHER CHARGE",
                "TOTAL GOVT.FEE", "BANK REFERENCE NUMBER", "MB/ST/DW-ID", "APPLICATION ID / RECEIPT ID", "REF.NAME", "EMPLOYEE COMMISSION", "CUSTOMER COMMISSION", "DISCOUNT AMOUNT",
                "REWARD AMOUNT", "PAYMENT STATUS", "EMPLOYEE", "EMPLOYEE NAME", "LINE TOTAL", "INVOICE TOTAL", "NET SERVICE CHARGE");
        }

//print_r($push_data);
        fputcsv($file, $header);

        $i = 0;
        $data = [];

        while ($myrow = db_fetch_assoc($result)) {

            $sql_catname = "SELECT a.description
    FROM 0_stock_category AS a
    INNER JOIN 0_stock_master AS b ON a.category_id=b.category_id
    WHERE b.stock_id='" . $myrow['stock_id'] . "'";
            $cat_name_data = db_fetch_assoc(db_query($sql_catname));
            $cate_name = $cat_name_data['description'];

            $cust_name = "select name FROM 0_debtors_master where debtor_no='" . $myrow['debtor_no'] . "'";
            $cust_data = db_fetch_assoc(db_query($cust_name));
            $customer_name = $cust_data['name'];

            $acc_name = "select account_name FROM 0_chart_master where account_code='" . $myrow['govt_bank_account'] . "'";
            $account_name = db_fetch_assoc(db_query($acc_name));
            $account = $account_name['account_name'];


            $user = "select user_id,real_name FROM 0_users where id='" . $myrow['created_by'] . "'";
            $user_data = db_fetch_assoc(db_query($user));
            $user_name = $user_data['user_id'];


            $net_service_charge = $myrow['line_total'] - $myrow['reward_amount'] - $myrow['customer_commission'] - $myrow['employee_commission'];
            $data_to_fecth = [];
            if (isset($_POST['custom_report_hdn_id']) && !empty(trim($_POST['custom_report_hdn_id']))) {


                $data_to_fecth = array(
                    'invoice_number' => $myrow['invoice_number'],
                    'tran_date' => $myrow['tran_date'],
                    'invoice_type' => $myrow['invoice_type'],
                    'stock_id' => $myrow['stock_id'],
                    'service' => $myrow['description'],
                    'category' => $cate_name,
                    'customer' => $customer_name,
                    'salesman' => $myrow['salesman_name'],
                    'display_customer' => $myrow['display_customer'],
                    'customer_mobile' => $myrow['customer_mobile'],
                    'customer_email' => $myrow['customer_email'],
                    'quantity' => $myrow['quantity'],
                    'unit_price' => $myrow['unit_price'],
                    'total_price' => $myrow['total_service_charge'],
                    'extra_service_charge' => $myrow['extra_service_charge'],
                    'total_tax' => $myrow['total_tax'],
                    'govt_fee' => $myrow['govt_fee'],
                    'govt_bank' => $account,
                    'bank_service_charge' => $myrow['bank_service_charge'],
                    'bank_service_charge_vat' => $myrow['bank_service_charge_vat'],
                    'pf_amount' => $myrow['pf_amount'],
                    'total_govt_fee' => $myrow['total_govt_fee'],
                    'transaction_id' => $myrow['transaction_id'],
                    'ed_transaction_id' => "'" . $myrow['ed_transaction_id'],
                    'application_id' => "'" . $myrow['application_id'],
                    'ref_name' => $myrow['ref_name'],
                    'employee_commission' => $myrow['employee_commission'],
                    'customer_commission' => $myrow['customer_commission'],
                    'line_discount_amount' => $myrow['line_discount_amount'],
                    'reward_amount' => $myrow['reward_amount'],
                    'payment_status' => $myrow['payment_status'],
                    'created_by' => $user_name,
                    'employee_name' => $user_data['real_name'],
                    'line_total' => $myrow['line_total'],
                    'invoice_total' => $myrow['invoice_total'],
                    'net_service_charge' => $net_service_charge
                );
                $test = [];
                foreach ($data_to_fecth as $keys => $vals) {
                    if (in_array($keys, $push_data)) {
                        array_push($test, $vals);

                    }

                }

                $data[] = $test;
            } else {

                /*if()*/
                $data[] = array(
                    $myrow['invoice_number'],
                    $myrow['tran_date'],
                    $myrow['invoice_type'],
                    $myrow['stock_id'],
                    $myrow['description'],
                    $cate_name,
                    $customer_name,
                    $myrow['salesman_name'],
                    $myrow['display_customer'],
                    $myrow['customer_mobile'],
                    $myrow['customer_email'],
                    $myrow['quantity'],
                    $myrow['unit_price'],
                    $myrow['total_service_charge'],
                    $myrow['extra_service_charge'],
                    $myrow['total_tax'],
                    $myrow['govt_fee'],
                    $account,
                    $myrow['bank_service_charge'],
                    $myrow['bank_service_charge_vat'],
                    $myrow['pf_amount'],
                    $myrow['total_govt_fee'],
                    $myrow['transaction_id'],
                    "'" . $myrow['ed_transaction_id'],
                    "'" . $myrow['application_id'],
                    $myrow['ref_name'],
                    $myrow['employee_commission'],
                    $myrow['customer_commission'],
                    $myrow['line_discount_amount'],
                    $myrow['reward_amount'],
                    $myrow['payment_status'],
                    $user_name,
                    $user_data['real_name'],
                    $myrow['line_total'],
                    $myrow['invoice_total'],
                    $net_service_charge
                );


            }


            fputcsv($file, $data[$i]);
            $i++;
            //unset($data_to_fecth);
        }
        fclose($file);
        exit;
    }


    public function export_pdf($data)
    {

        $path = "";
        require_once $path . '../vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf(['setAutoTopMargin' => 'stretch', 'default_font_size' => 7, 'default_font' => 'dejavusans']);
        $mpdf->SetDisplayMode('fullpage');


        $mpdf->list_indent_first_level = 0; // 1 or 0 - whether to indent the first level of a list
        $stylesheet = file_get_contents('style.css');
        $mpdf->WriteHTML($stylesheet, 1);
        //$mpdf->SetColumns(1, 'J', 9);
        $mpdf->list_align_style = 'L';
        $mpdf->falseBoldWeight = 2;

        /*-------------------------GET CAT NAME---------------*/
        $cat_data = "select description FROM 0_stock_category where category_id='" . $data['category'] . "'";
        $reult_data = db_fetch_assoc(db_query($cat_data));
        if ($data['category'] == '') {
            $reult_data['description'] = 'All';
        }

        $f_year = kv_get_current_fiscalyear();
        $begin1 = $f_year['begin'];
        $today1 = $f_year['end'];
        /*----------------------------END----------------------*/

        $mpdf->setHeader('
            <div>
            <div align="right">
            <span style="font-size: 9pt !important;font-weight: normal !important;">DXBBMS</span><br/>
            </div>
            <div align="left" >
            <span style="font-size:12pt;">Category Report</span><br/>
            <span>Print Out Date : ' . date('d-m-Y h:i:s') . '</span><br/>
            <label style="font-weight: normal;">Fiscal Year : ' . date('d-m-Y', strtotime($begin1)) . ' - ' . date('d-m-Y', strtotime($today1)) . '</label><br/>
            <label style="font-weight: normal;">Period : ' . $data['date_from'] . ' - ' . $data['date_to'] . '</label><br/>
            <label style="font-weight: normal;">Category : ' . $reult_data['description'] . '</label><br/>
            <label></label><br/>
            </div>




            </div>

            <table style="border-top: 1px solid black;">
            <tr>
            <td>Sl.no</td>
            <td style="width:15%;">INVOICE No.</td>
            <td style="width:15%;">SERVICE NAME</td>
            <td style="width:10%;">CATEGORY</td>
            <td style="width:10%;">TOTAL SERVICE CHARGE</td>
            <td>TOTAL<br/> GOVT. FEE</td>
            <td>BANK <br/> REFERENCE No.</td>
            <td>EMP. NAME</td>
            <td>LINE TOTAL<td>
            </tr> </table>
            ');

        // $mpdf->SetHeader($arr);

        $content = "<table>
";

        $sql = PrepareQuery::ServiceReport($data);
        $result = db_query($sql);
        $i = 1;
        $tot_service_chrge = '0';
        $tot_govt_fee_disp = '0';
        $line_tot = '0';
        while ($myrow = db_fetch_assoc($result)) {

            $sql_catname = "SELECT a.description
    FROM 0_stock_category AS a
    INNER JOIN 0_stock_master AS b ON a.category_id=b.category_id
    WHERE b.stock_id='" . $myrow['stock_id'] . "'";
            $cat_name_data = db_fetch_assoc(db_query($sql_catname));
            $cate_name = $cat_name_data['description'];

            $cust_name = "select name FROM 0_debtors_master where debtor_no='" . $myrow['debtor_no'] . "'";
            $cust_data = db_fetch_assoc(db_query($cust_name));
            $customer_name = $cust_data['name'];

            $acc_name = "select account_name FROM 0_chart_master where account_code='" . $myrow['govt_bank_account'] . "'";
            $account_name = db_fetch_assoc(db_query($acc_name));
            $account = $account_name['account_name'];


            $user = "select user_id,real_name FROM 0_users where id='" . $myrow['created_by'] . "'";
            $user_data = db_fetch_assoc(db_query($user));
            $emp_name = explode(" ", $user_data['real_name']);


            $net_service_charge = $myrow['line_total'] - $myrow['reward_amount'] - $myrow['customer_commission'] - $myrow['employee_commission'];

            $content .= '<tr>
    <td>' . $i . '</td>
    <td>' . $myrow['invoice_number'] . '</td>
    <td style="width:22%;">' . $myrow['description'] . '</td>
    <td>' . $cate_name . '</td>
    <td>' . $myrow['total_service_charge'] . '</td>
    <td>' . $myrow['total_govt_fee'] . '</td>
    <td style="width:17;">' . $myrow['transaction_id'] . '</td>
    <td style="width:17%;" align="center">' . $emp_name[0] . ' ' . $emp_name[1] . '</td>
    <td>' . round($myrow['line_total'], 2) . '</td>
    </tr>';

            $i++;

            $tot_service_chrge += $myrow['total_service_charge'];
            $tot_govt_fee_disp += $myrow['total_govt_fee'];
            $line_tot += round($myrow['line_total'], 2);

        }


        $content .= ' </table>';

        $content .= '<table style="border-top: 1px solid black;width:100%;"><tr >

<td style="font-weight: bold;">TOTAL :</td>
<td style="width: 39%;"></td>
<td style="font-weight: bold;">' . number_format($tot_service_chrge, 2) . '</td>
<td style="font-weight: bold;">' . number_format($tot_govt_fee_disp, 2) . '</td>
<td style="width: 25%;"></td>
<td style="font-weight: bold;">' . number_format($line_tot, 2) . '</td>



</tr></table>';

        $mpdf->WriteHTML($content);

        $mpdf->setFooter('<div style="font-weight: normal; font-size: 12px">Powered by - &copy; www.axisproerp.com</div>');


        $mpdf->Output("Category_Report.pdf", \Mpdf\Output\Destination::INLINE);
    }

    public function upload_purchase_req_doc($format = "json")
    {

        try {

            $root_url = str_replace("\ERP", "", getcwd());
            $root_url = str_replace("\API", "", $root_url);
            $root_url = getcwd();

            $pr_id = $_POST['upload_doc_pr_id'];

            $target_file = '';
            $filename = '';
            if ($_FILES["upload_doc"]["name"] != '') {
                $target_dir = $root_url . "/../../assets/uploads/";
                $fname = explode(".", $_FILES["upload_doc"]["name"]);
                $rand = rand(1000, 10000);
                $filename = $fname[0] . '_' . $rand . '.' . $fname[1];
                $target_file = $target_dir . basename($filename);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                if ($_FILES["upload_doc"]["size"] > 50000000) {
                    return AxisPro::SendResponse(["status" => "FAIL", "msg" => "File size exceeded"], $format);
                }
                if ($imageFileType != "pdf" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "jpg") {
                    return AxisPro::SendResponse(["status" => "FAIL", "msg" => "File format is not allowed"], $format);
                }

            }


            if (move_uploaded_file($_FILES["upload_doc"]["tmp_name"], $target_file)) {

                db_update('0_purchase_requests', [
                    'upload_file' => db_escape($filename)
                ], ["id=$pr_id"]);

                return AxisPro::SendResponse(["status" => "OK", "msg" => "Document saved successfully"], $format);
            }

            return AxisPro::SendResponse(["status" => "FAIL", "msg" => "Something went wrong. Please try again"], $format);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    public function get_issuing_items($format = 'json')
    {
        try {

            $purchase_req_id = 1;

            $sql = "SELECT * FROM 0_purchase_requests where id = $purchase_req_id";
            $result = db_fetch_assoc(db_query($sql));
            $mr = $result;

            $sql = "SELECT items.*,stk.description item_name,stk.purchase_cost 
        FROM 0_purchase_request_items items 
        LEFT JOIN 0_stock_master stk ON stk.stock_id = items.stock_id
        where items.req_id = $purchase_req_id ";


            $result = db_query($sql);
            $items = [];
            $line = 1;
            $return_result = [];
            while ($myrow = db_fetch_assoc($result)) {
                $myrow['qty_in_stock'] = get_qoh_on_date($myrow['stock_id']);

                $qty_to_be_ordered = 0;

                if ($myrow['qty'] > $myrow['qty_in_stock'])
                    $qty_to_be_ordered = $myrow['qty'] - $myrow['qty_in_stock'];

                if ($qty_to_be_ordered < 0)
                    $qty_to_be_ordered = 0;

                $qty_issuable = $myrow['qty_in_stock'];

                if ($qty_issuable > 0) {

                    // pp($myrow);

                    if ($qty_issuable > $myrow['qty'])
                        $qty_issuable = $myrow['qty'];

//                    $_SESSION['adj_items']->add_to_cart($line, $myrow['stock_id'], -$qty_issuable, 0, $description = null);
//                    $line++;

                    $return_result[] = [
                        'item_code' => $myrow['stock_id'],
                        'item_name' => $myrow['item_name'],
                        'issue_qty' => $qty_issuable,
                        'unit_cost' => $myrow['purchase_cost'],
                        'total_cost' => $myrow['purchase_cost'] * $qty_issuable

                    ];

                }

            }

            return AxisPro::SendResponse($return_result, $format);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }


    public function issueStockItems($format = 'json')
    {

        try {

            global $Refs;

            $issueItemReqID = $_POST['issueItemReqID'];

            $items = $_POST['items'];

            $items_array = [];

            foreach ($items as $row) {

                $temp_array = [
                    'stock_id' => db_escape($row['stock_id']),
                    'quantity' => -$row['qty'],
                    'standard_cost' => $row['standard_cost']

                ];

                array_push($items_array, $temp_array);

            }


            $ref = $Refs->get_next(17);
            $memo = 'Stock Issuing From Purchase Request';
            $date = Today();
            $loc = 'DEF';

            $items_obj = new stdClass();
            $items_obj->stock_id =

            $trans_no = $this->AddStockAdjustment($items_array,
                $loc, $date, $ref, $memo);
            new_doc_date($date);


            if (isset($issueItemReqID) && !empty($issueItemReqID)) {
                db_update('0_purchase_requests',
                    ['issued_from_stock' => 1], //issued from stock
                    ['id=' . $issueItemReqID]
                );

                $user_id = $_SESSION['wa_current_user']->user;

                $msg = "Items issued from stock";
                db_insert('0_purch_request_log', [
                    'user_id' => $user_id,
                    'req_id' => $issueItemReqID,
                    'description' => db_escape($msg)
                ]);

            }

            return AxisPro::SendResponse(['msg' => 'Success', 'data' => $trans_no, 'status' => 'OK'], $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    function AddStockAdjustment($items, $location, $date_, $reference, $memo_)
    {

        try {

            global $SysPrefs, $path_to_root, $Refs;

            begin_transaction();
            $args = func_get_args();
            $args = (object)array_combine(array('items', 'location', 'date_', 'reference', 'memo_'), $args);
            $args->trans_no = 0;
            hook_db_prewrite($args, ST_INVADJUST);

            $adj_id = get_next_trans_no(ST_INVADJUST);

            foreach ($items as $line_item) {


                //dd($line_item);

                $stk_id = trim($line_item['stock_id'], "'");

                add_stock_adjustment_item($adj_id, $stk_id, $location, $date_, $reference,
                    $line_item['quantity'], $line_item['standard_cost'], $memo_);
            }

            add_comments(ST_INVADJUST, $adj_id, $date_, $memo_);

            $Refs->save(ST_INVADJUST, $adj_id, $reference);
            add_audit_trail(ST_INVADJUST, $adj_id, $date_);

            $args->trans_no = $adj_id;
            hook_db_postwrite($args, ST_INVADJUST);
            commit_transaction();

            return $adj_id;

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }


    public function getNotifications($format = 'json')
    {
        try {

            $return_result = AxisNotification::getAll(0);

            //Set all notification status to read.
            $this->readNotifications('array');

            return AxisPro::SendResponse($return_result, $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }

    public function readNotifications($format = 'json')
    {
        try {

            $user_id = $_SESSION['wa_current_user']->user;
            AxisNotification::makeReadAll($user_id); //TODO

            return AxisPro::SendResponse(["status" => "OK", "msg" => "All Unread Notifications is set to read"], $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }

    public function getUnreadNotificationCount($format = 'json')
    {
        try {

            $user_id = $_SESSION['wa_current_user']->user;
            $count = AxisNotification::getUnreadCount($user_id); //TODO

            $common_alerts = $this->getCommonAlerts();

            return AxisPro::SendResponse(["status" => "OK", "data" => $count,
                'common_alerts' => $common_alerts], $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }

    public function bankBalanceBelowLimitCheck()
    {

        try {

            if (!in_array($_SESSION['wa_current_user']->access, [9, 2])) {
                return [];
            }

            $acc_to_check = ["113002", "113003"];//Test purpose

            $acc_bal_limits = [
                "113002" => 5000,
                "113003" => 5000
            ];

            $gone_below_limits = [];
            foreach ($acc_to_check as $acc) {

                $from_date = begin_fiscalyear();
                $to_date = Today();
                $account = $acc;

                $from_date = add_days($from_date, -1);
                $to_date = add_days($to_date, 1);

                $balance = get_gl_balance_from_to($from_date, $to_date, $account, $dimension = 0, $dimension2 = 0);

                if (empty($balance))
                    $balance = 0;

                if ($acc_bal_limits[$acc] > $balance) {

                    $acc_name = get_gl_account_name($acc);

                    $gone_below_limits[] = [
                        'account' => $acc,
                        'account_name' => $acc_name,
                        'curr_bal' => $balance,
                        'limit' => $acc_bal_limits[$acc],
                        'type' => 'danger'
                    ];
                }

            }

            return $gone_below_limits;


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function getCommonAlerts()
    {

        try {

            $common_alerts = [];

            $bank_bal_limit_check = $this->bankBalanceBelowLimitCheck();

            if (!empty($bank_bal_limit_check)) {

                foreach ($bank_bal_limit_check as $row) {

                    $acc_name = strtoupper($row['account_name']);
                    $curr_bal = $row['curr_bal'];
                    $limit = $row['limit'];
                    $type = $row['type'];

                    $style = "font-weight:bold;";
                    if ($type == 'danger')
                        $style .= "color:red;";
                    $alert_text = "<span style='$style'>$acc_name balance is gone below the set limit AED $limit.</span>";
                    $common_alerts[] = $alert_text;

                }

            }

//            return [];
            return $common_alerts;


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function get_gl_acc_balance_from_to($format = 'json')
    {
        try {

            $from_date = begin_fiscalyear();
            $to_date = $_GET['trans_date'];
            $account = $_GET['cash_acc'];
            $account = get_bank_gl_account($account);

            $from_date = add_days($from_date, -1);
            $to_date = add_days($to_date, 1);

            if (empty($account))
                return AxisPro::SendResponse(["status" => "FAIL", "data" => "Required Fields Not Validated"], $format);

            $balance = get_gl_balance_from_to($from_date, $to_date, $account, $dimension = 0, $dimension2 = 0);

            if (empty($balance))
                $balance = 0;

            return AxisPro::SendResponse(["status" => "OK", "data" => $balance], $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }

    public function getBalanceCashCollection($format = 'json')
    {
        try {
            $user_date_format = getDateFormatInNativeFormat();
            if (
                !isset($_GET['trans_date'])
                || !($dt = DateTime::createFromFormat($user_date_format, $_GET['trans_date']))
                || $dt->format($user_date_format) != $_GET['trans_date']
            ) {
                return AxisPro::SendResponse(["status" => "FAIL", "msg" => "The date is not valid"], $format);
            } else {
                $trans_date = $dt->format(MYSQL_DATE_FORMAT);
            }

            if (!isset($_GET['user_id']) || !preg_match('/^[1-9][0-9]{0,15}$/', $_GET['user_id'])) {
                return AxisPro::SendResponse(["status" => "FAIL", "msg" => "The user id is not valid"], $format);
            }

            if (!isset($_GET['cash_acc']) || !preg_match('/^[0-9]{1,15}$/', $_GET['cash_acc'])) {
                return AxisPro::SendResponse(["status" => "FAIL", "msg" => "account code is not valid"], $format);
            }

            $account = get_bank_gl_account($_GET['cash_acc']);
            if (empty($account)) {
                return AxisPro::SendResponse(["status" => "FAIL", "msg" => "account is not a bank account"], $format);
            }

            $balance = db_fetch_row(
                db_query(
                    "SELECT SUM(gl.amount) 
                FROM 0_gl_trans gl
                WHERE gl.account = '$account'
                    AND gl.tran_date = '$trans_date'
                    AND gl.created_by = {$_GET['user_id']}"
                )
            )[0];

            if (empty($balance)) {
                $balance = 0.00;
            } else {
                $balance = (float)$balance;
            }

            /** Check if there any amount that is already handovered and miinus that amount*/
            $mysqli_result = db_query(
                "SELECT cash_in_hand 
            FROM 0_cash_handover_requests 
            WHERE trans_date = '$trans_date'
                AND cashier_id = {$_GET['user_id']}
                AND `status` = 'APPROVED'"
            );
            while ($row = $mysqli_result->fetch_assoc()) {
                $balance -= (float)$row['cash_in_hand'];
            }

            return AxisPro::SendResponse(["status" => "OK", "data" => round2($balance, 2)], $format);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }

    public function getHandoveredCash($format = 'json')
    {
        $user_date_format = getDateFormatInNativeFormat();
        if (
            !($dt = DateTime::createFromFormat($user_date_format, $_GET['trans_date']))
            || $dt->format($user_date_format) != $_GET['trans_date']
        ) {
            return AxisPro::SendResponse(["status" => "FAIL", "msg" => "The date is not valid"], $format);
        } else {
            $trans_date = $dt->format(MYSQL_DATE_FORMAT);
        }

        if (!preg_match('/^[1-9][0-9]{0,15}$/', $_GET['user_id'])) {
            return AxisPro::SendResponse(["status" => "FAIL", "msg" => "The user id is not valid"], $format);
        }

        $cash = db_query(
            "SELECT SUM(cash_in_hand) 
        FROM 0_cash_handover_requests 
        WHERE trans_date = '$trans_date'
            AND cashier_id = {$_GET['user_id']}
            AND `status` = 'APPROVED'
        GROUP BY cashier_id"
        )->fetch_row()[0];

        $cash = round2((float)$cash, 2);

        return AxisPro::SendResponse(["status" => "OK", "data" => $cash], $format);
    }

    public function get_cashiers($format = 'json')
    {
        try {
            $sql = "select id,user_id,real_name,cashier_account from 0_users where role_id in (3,9,13,15)";
            $get = db_query($sql);
            $return_result = [];

            while ($myrow = db_fetch($get))
                $return_result[] = $myrow;

            return AxisPro::SendResponse($return_result, $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function get_user_info($format = 'json')
    {
        try {

            $user_id = $_GET['user_id'];

            $sql = "select id,user_id,real_name,cashier_account, role_id from 0_users where id=$user_id";
            $get = db_query($sql);
            $data = db_fetch_assoc($get);

            return AxisPro::SendResponse(["status" => "OK", "data" => $data], $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * Return security roles which have access to the specified security areas
     *
     * Note: Does not consider whether the security section that encapsulate the area is enabled for the role or not
     *
     * @param string|string[] $sec_areas The secutity area or areas that needs to be searched
     * @return array Array of role ids which have access to the provided security area or empty array if none exists
     */
    public function getRoleIdsWithAccess($sec_areas)
    {
        if (!is_array($sec_areas)) {
            $sec_areas = [$sec_areas];
        }
        /** Index the security areas */
        $sec_areas = array_intersect_key($GLOBALS['security_areas'], array_flip($sec_areas));
        $sec_areas = array_column($sec_areas, 0, 0);

        $roles = [];
        $mysqli_result = db_query("SELECT id, areas FROM 0_security_roles WHERE NOT inactive");
        while ($row = $mysqli_result->fetch_assoc()) {
            $roles[$row['id']] = array_flip(explode(';', $row['areas']));
        }

        $role_ids = [];
        foreach ($roles as $id => $permissions) {
            if (!empty(array_intersect_key($sec_areas, $permissions))) {
                $role_ids[] = $id;
            }
        }
        return $role_ids;
    }


    public function saveCashHandOverRequest($format = 'json')
    {
        try {
            // validate cashier id
            $cashier_id = $_GET['user_id'] = $_POST['user_id'];
            if (empty($cashier_id)) {
                return AxisPro::SendResponse([
                    "status" => "FAIL",
                    "msg" => "Please choose a Cashier"
                ], $format);
            }
            if (!preg_match('/^[1-9][0-9]{0,15}$/', $cashier_id)) {
                return AxisPro::SendResponse(["status" => "FAIL",
                    "msg" => "The cashier id is invalid"
                ], $format);
            }

            // validate cashier's cash account.
            $res = db_fetch_assoc(
                db_query(
                    "select id,user_id,real_name,cashier_account from 0_users where id = $cashier_id"
                )
            );
            if ($res) {
                $cash_acc = $res['cashier_account'];
            }
            if (empty($cash_acc)) {
                return AxisPro::SendResponse(["status" => "FAIL",
                    "msg" => "No Cashier A/C set for this cashier."
                ], $format);
            }

            // validate date
            $trans_date = $_POST['trans_date'];
            $user_date_format = getDateFormatInNativeFormat();
            if (
                !($dt = DateTime::createFromFormat($user_date_format, $trans_date))
                || $dt->format($user_date_format) !== $trans_date
            ) {
                return AxisPro::SendResponse(["status" => "FAIL",
                    "msg" => "Transaction date is invalid"
                ], $format);
            }
            $tdate = date2sql($trans_date);
            $today = date2sql(Today());
            $future_date = strtotime($today) < strtotime($tdate);
            if ($future_date) {
                return AxisPro::SendResponse(["status" => "FAIL",
                    "msg" => "Transaction Date must not be a Future Date"
                ], $format);
            }

            $cash_acc_gl_code = get_bank_gl_account($cash_acc);
            if (empty($cash_acc_gl_code)) {
                return AxisPro::SendResponse(["status" => "FAIL",
                    "msg" => "No bank account is configured for this cashier A/C."
                ], $format);
            }

            $created_by = $_SESSION['wa_current_user']->user;

            $denom1000 = !empty($_POST['denom1000_pcs']) ? $_POST['denom1000_pcs'] : 0;
            $denom500 = !empty($_POST['denom500_pcs']) ? $_POST['denom500_pcs'] : 0;
            $denom200 = !empty($_POST['denom200_pcs']) ? $_POST['denom200_pcs'] : 0;
            $denom100 = !empty($_POST['denom100_pcs']) ? $_POST['denom100_pcs'] : 0;
            $denom50 = !empty($_POST['denom50_pcs']) ? $_POST['denom50_pcs'] : 0;
            $denom20 = !empty($_POST['denom20_pcs']) ? $_POST['denom20_pcs'] : 0;
            $denom10 = !empty($_POST['denom10_pcs']) ? $_POST['denom10_pcs'] : 0;
            $denom5 = !empty($_POST['denom5_pcs']) ? $_POST['denom5_pcs'] : 0;
            $denom1 = !empty($_POST['denom1_pcs']) ? $_POST['denom1_pcs'] : 0;
            $denom0_5 = !empty($_POST['denom0_5_pcs']) ? $_POST['denom0_5_pcs'] : 0;
            $denom0_25 = !empty($_POST['denom0_25_pcs']) ? $_POST['denom0_25_pcs'] : 0;

            $denom_total = 0;
            $denom_total += ($denom1000 * 1000);
            $denom_total += ($denom500 * 500);
            $denom_total += ($denom200 * 200);
            $denom_total += ($denom100 * 100);
            $denom_total += ($denom50 * 50);
            $denom_total += ($denom20 * 20);
            $denom_total += ($denom10 * 10);
            $denom_total += ($denom5 * 5);
            $denom_total += ($denom1 * 1);
            $denom_total += ($denom0_5 * 0.5);
            $denom_total += ($denom0_25 * 0.25);

            if (empty($denom_total) || $denom_total <= 0)
                return AxisPro::SendResponse(["status" => "FAIL",
                    "msg" => "Please enter valid denominations"
                ], $format);

            $_GET['trans_date'] = $trans_date;
            $_GET['cash_acc'] = $cash_acc;
            $tot_cash_in_hand = round2((float)$this->getBalanceCashCollection('array')['data'], 2);

            if (empty($tot_cash_in_hand))
                $tot_cash_in_hand = 0;

            $tot_cash_in_hand = round2($tot_cash_in_hand, 2);
            $total_to_pay = ceil($tot_cash_in_hand / 0.25) * 0.25;
            $adjustments = round2($total_to_pay - $tot_cash_in_hand, 2);
            $balance = round2($denom_total - $total_to_pay, 2);

            if ($denom_total < $total_to_pay)
                return AxisPro::SendResponse([
                    "status" => "FAIL",
                    "msg" => "Entered amount($denom_total) is less than the cash in hand($total_to_pay)"
                ], $format);


            $insert_data = [
                'amount' => $denom_total,
                'cashier_id' => $cashier_id,
                'cash_acc_code' => $cash_acc_gl_code,
                'cash_in_hand' => $tot_cash_in_hand,
                'total_to_pay' => $total_to_pay,
                'adj' => $adjustments,
                'balance' => $balance,
                'trans_date' => db_escape(date2sql($trans_date)),
                'created_by' => $created_by,
                'denom1000' => $denom1000,
                'denom500' => $denom500,
                'denom200' => $denom200,
                'denom100' => $denom100,
                'denom50' => $denom50,
                'denom20' => $denom20,
                'denom10' => $denom10,
                'denom5' => $denom5,
                'denom1' => $denom1,
                'denom0_5' => $denom0_5,
                'denom0_25' => $denom0_25,
            ];

            db_insert('0_cash_handover_requests', $insert_data);

            $insert_id = db_insert_id();
            $ref = 'CH/' . $insert_id;

            $sql = "update 0_cash_handover_requests set reference = '$ref' WHERE id=$insert_id";
            db_query($sql);

            return AxisPro::SendResponse([
                "status" => "OK",
                "msg" => "New Cash Handover Request Placed",
                'data' => $ref
            ], $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function getAllCashHandoverRequests($format = 'json')
    {
        try {
            if (!$_SESSION['wa_current_user']->can_access('SA_CASH_HANDOVER_LIST')) {
                return AxisPro::SendResponse([
                    'rep' => [],
                    'total_rows' => 0,
                    'pagination_link' => '',
                    'users' => [],
                    'bl' => [],
                    'aggregates' => [],
                ]);
            }

            $sql = "SELECT * FROM 0_cash_handover_requests WHERE 1=1 ORDER BY created_at DESC";

            $total_count_sql = "select count(*) as cnt from ($sql) as tmpTable";
            $total_count_exec = db_fetch_assoc(db_query($total_count_sql));
            $total_count = $total_count_exec['cnt'];

            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $perPage = 200;
            $offset = ($page * $perPage) - $perPage;


            $sql = $sql . " LIMIT $perPage OFFSET $offset";

            $result = db_query($sql);
            $report = [];
            while ($myrow = db_fetch_assoc($result)) {

                $myrow['trans_date'] = sql2date($myrow['trans_date']);

                $denoms = [];

                if (!empty($myrow['denom1000']))
                    $denoms[] = [
                        "key" => '1000',
                        "val" => $myrow['denom1000']
                    ];

                if (!empty($myrow['denom500']))
                    $denoms[] = [
                        "key" => '500',
                        "val" => $myrow['denom500']
                    ];

                if (!empty($myrow['denom200']))
                    $denoms[] = [
                        "key" => '200',
                        "val" => $myrow['denom200']
                    ];

                if (!empty($myrow['denom100']))
                    $denoms[] = [
                        "key" => '100',
                        "val" => $myrow['denom100']
                    ];

                if (!empty($myrow['denom50']))
                    $denoms[] = [
                        "key" => '50',
                        "val" => $myrow['denom50']
                    ];

                if (!empty($myrow['denom20']))
                    $denoms[] = [
                        "key" => '20',
                        "val" => $myrow['denom20']
                    ];

                if (!empty($myrow['denom10']))
                    $denoms[] = [
                        "key" => '10',
                        "val" => $myrow['denom10']
                    ];

                if (!empty($myrow['denom5']))
                    $denoms[] = [
                        "key" => '5',
                        "val" => $myrow['denom5']
                    ];

                if (!empty($myrow['denom1']))
                    $denoms[] = [
                        "key" => '1',
                        "val" => $myrow['denom1']
                    ];

                if (!empty($myrow['denom0_5']))
                    $denoms[] = [
                        "key" => '0.5',
                        "val" => $myrow['denom0_5']
                    ];

                if (!empty($myrow['denom0_25']))
                    $denoms[] = [
                        "key" => '0.25',
                        "val" => $myrow['denom0_25']
                    ];

                $myrow['denoms'] = $denoms;

                $report[] = $myrow;
            }


            $sql = "SELECT coa.account_name, coa.account_code, bank.id AS bank_acc_id FROM 0_chart_master coa 
        INNER JOIN 0_bank_accounts bank ON bank.account_code = coa.account_code";
            $result = db_query($sql);
            $bank_ledgers = [];
            while ($myrow = db_fetch_assoc($result))
                $bank_ledgers[$myrow['account_code']] = $myrow;

            return AxisPro::SendResponse(
                [
                    'rep' => $report,
                    'total_rows' => $total_count,
                    'pagination_link' => AxisPro::paginate($total_count),
                    'users' => $this->get_key_value_records('0_users', 'id', 'user_id'),
                    'bl' => $bank_ledgers,
                    'aggregates' => $total_count_exec,]
            );


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    public function cashHandoverRequestActionHandler($format = 'json')
    {
        if (!$_SESSION['wa_current_user']->can_access('SA_CASH_HANDOVER_LIST')) {
            return AxisPro::SendResponse([
                'status' => 'FAIL',
                'msg' => 'The security settings on your account do not permit you to access this function'
            ]);
        }

        try {

            global $Refs;

            $req_id = $_POST['req_id'];
            $actionToUpdate = $_POST['action'];
            $dateTime = date(MYSQL_DATE_TIME_FORMAT);

            if ($actionToUpdate == 'APPROVED') {
                //Post gl after approval
                //Todo

                $sql = "select * from 0_cash_handover_requests where id=$req_id";
                $get = db_query($sql);
                $req_info = db_fetch($get);

                $user_handing_over_cash = $req_info['cashier_id'];
                $cash_handover_requested_on = $req_info['trans_date'];
                $cash_in_hand = $req_info['cash_in_hand'];
                $adjustments = $req_info['adj'];
                $totalToPay = $req_info['total_to_pay'];
                $credit_account = $req_info['cash_acc_code'];
                $credit_account2 = get_company_pref('cash_handover_round_off_adj_act');
                if (empty(trim($credit_account2))) {
                    return AxisPro::SendResponse([
                        'status' => 'FAIL',
                        'msg' => "Cash handover round off adjustment account is not set"
                    ]);
                }

                $cash_acc_id = db_query(
                    "select cashier_account from 0_users where id = {$_SESSION['wa_current_user']->user}"
                )->fetch_row()[0];
                if (empty($cash_acc_id)) {
                    return AxisPro::SendResponse(["status" => "FAIL",
                        "msg" => "No Cashier A/C is set for this user."
                    ], $format);
                }

                // $hasMultipleUsersWithSameAccount = db_query(
                //     "SELECT COUNT(1) FROM 0_users WHERE cashier_account = $cash_acc_id AND id != {$_SESSION['wa_current_user']->user}"
                // )->fetch_row()[0];
                // if ($hasMultipleUsersWithSameAccount) {
                //     return AxisPro::SendResponse(["status" => "FAIL",
                //         "msg" => "This user must have a seperate cashier A/C"
                //     ]);
                // }

                $debit_account = get_bank_gl_account($cash_acc_id);
                if (empty($debit_account)) {
                    return AxisPro::SendResponse(["status" => "FAIL",
                        "msg" => "No bank account is configured for this A/C."
                    ], $format);
                }

                /** Check if the user transfering the cash and the user approving the request use the same cashier account */
                if ($credit_account == $debit_account) {
                    return AxisPro::SendResponse(["status" => "FAIL",
                        "msg" => "Cannot transfer cash between users: having the same cash account"
                    ], $format);
                }

                /** Verify the balance in the cashier's account */
                $_GET['user_id'] = $user_handing_over_cash;
                $_GET['trans_date'] = DateTime::createFromFormat(MYSQL_DATE_FORMAT, $cash_handover_requested_on)->format(getDateFormatInNativeFormat());
                $_GET['cash_acc'] = db_query("SELECT id from 0_bank_accounts where account_code = '{$credit_account}'")->fetch_assoc()['id'];
                $balance_in_cashier_account = round2((float)$this->getBalanceCashCollection('array')['data'], 2);
                if (floatcmp($balance_in_cashier_account, $cash_in_hand) != 0) {
                    return AxisPro::SendResponse(["status" => "FAIL",
                        "msg" => "Amount does not match the balance in their account!"
                    ], $format);
                }

                //Pass Journal Entry
                $ref = $Refs->get_next(ST_JOURNAL, null, Today());
                $trans_type = 0;

                $trans_id = get_next_trans_no(ST_JOURNAL);

                $memo = "Cash Handover Request";

                begin_transaction();
                db_update('0_cash_handover_requests', [
                    'trans_no' => $trans_id,
                    'status' => db_escape($actionToUpdate),
                    'approve_rejected_by' => $_SESSION['wa_current_user']->user,
                    'approve_rejected_at' => "'{$dateTime}'"
                ], ["id=$req_id"]);
                add_gl_trans($trans_type, $trans_id, Today(), $debit_account, 0, 0,
                    $memo, $totalToPay, 'AED', null, null, "", 0);
                add_gl_trans($trans_type, $trans_id, Today(), $credit_account, 0, 0,
                    $memo, -$cash_in_hand, 'AED', null, null, "", 0);
                add_gl_trans($trans_type, $trans_id, Today(), $credit_account2, 0, 0,
                    $memo, -$adjustments, 'AED', null, null, "", 0);

                add_journal($trans_type, $trans_id, $totalToPay, Today(), 'AED', $ref,
                    '', 1, Today(), Today());

                $Refs->save($trans_type, $trans_id, $ref);
                add_comments($trans_type, $trans_id, Today(), $memo);
                add_audit_trail($trans_type, $trans_id, Today());
                commit_transaction();
            } else {
                begin_transaction();
                db_update('0_cash_handover_requests', [
                    'status' => db_escape($actionToUpdate),
                    'approve_rejected_by' => $_SESSION['wa_current_user']->user,
                    'approve_rejected_at' => "'{$dateTime}'"
                ], ["id=$req_id"]);
                commit_transaction();
            }

            $msg = "Cash Handover Request is APPROVED";

            if ($actionToUpdate == 'REJECTED')
                $msg = "Cash Handover Request is REJECTED";

            return AxisPro::SendResponse(["status" => "SUCCESS",
                "msg" => $msg
            ], $format);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    function place_srv_request($format = 'json')
    {

        try {

            begin_transaction();
            $inputs = $_POST;

            $errors = [];
            if (empty($_POST['display_customer']))
                $errors['display_customer'] = "Please enter display customer";

            if (empty($_POST['customer']))
                $errors['customer'] = "Please choose a customer";

            if (empty($_POST['mobile']))
                $errors['mobile'] = "Please enter customer mobile";
//            else if (strlen($_POST['mobile']) <> 10)
//                $errors['mobile'] = "Mobile number must have 10 digits Eg: 0512345678";

            if (empty($_POST['token_no']))
                $errors['token_no'] = "Please enter token number";

//            if (empty($_POST['contact_person']))
//                $errors['contact_person'] = "Please enter a contact person";

//            if (empty($_POST['iban_number']))
//                $errors['iban_number'] = "Please enter an iban number";


            if (!empty($errors))
                return AxisPro::SendResponse(['status' => 'FAIL', 'msg' => 'VALIDATION_FAILED', 'data' => $errors]);


            $edit_id = isset($_POST['edit_id']) ? $_POST['edit_id'] : null;


            $curr_user_id = $_SESSION['wa_current_user']->user;
            $user_info = get_user($curr_user_id);

            $array = [
                'customer_id' => $inputs['customer'],
                'payment_method' => db_escape($inputs['payment_method']),
                'cost_center_id' => $user_info['dflt_dimension_id'],
                'mobile' => db_escape($inputs['mobile']),
                'email' => db_escape($inputs['email']),
                'iban' => db_escape($inputs['iban_number']),
                'display_customer' => db_escape($inputs['display_customer']),
                'contact_person' => db_escape($inputs['contact_person']),
                'memo' => db_escape($inputs['memo']),
                'active_status' => db_escape($inputs['active_status'])
            ];


            if (empty($edit_id)) {

                $return_msg = "Service Request added";

                $sql = "select count(*) cnt from 0_service_requests 
                where token_number=" . db_escape($inputs['token_no']) . " and date(created_at) = " . db_escape(date2sql(Today()));
                $get = db_query($sql);
                $data = db_fetch($get);
                // if ($data['cnt'] > 0)
                //     return AxisPro::SendResponse(['status' => 'FAIL', 'msg' => 'This TOKEN NUMBER is already used today', 'data' => $errors]);

                $barcode = AxisPro::GenerateBarCode(12, '0_service_requests', 'barcode');

                $array['barcode'] = $barcode;
                $array['token_number'] = db_escape($inputs['token_no']);
                $array['created_by'] = $curr_user_id;

                db_insert('0_service_requests', $array);
                $service_request_id = db_insert_id();
            } else {//EDIT FUNCTIONALITY

                $return_msg = "Service Request updated";

                $service_request_id = $edit_id;

                $sql = "DELETE FROM 0_service_request_items WHERE req_id=$service_request_id";
                db_query($sql);

                $array['updated_by'] = $curr_user_id;

                db_update('0_service_requests', $array, ["id=$service_request_id"]);

            }


            if (empty($edit_id)) {

                $year = date("Y");
                $month = date("m");
                $day = date("d");

                $date_part = $year . $month . $day;

                $sql = "select COUNT(*) as cnt from 0_service_requests where
                token_number = " . db_escape($inputs['token_no']) . " and date(created_at) = " . db_escape(date2sql(Today()));

                $get = db_query($sql);
                $res = db_fetch($get);

                $next_cnt = $res['cnt'];

                $cost_center = get_dimension($user_info['dflt_dimension_id']);
                $reference = "SRQ/" . $cost_center['invoice_prefix'] . "/$date_part/" . $inputs['token_no'] . "/" . $next_cnt;

                db_update('0_service_requests', ['reference' => db_escape($reference)], ["id=$service_request_id"]);

            }


            // db_insert('0_service_requests', $insert_array);

            $dflt_bank_chrgs = $this->getDefaultBankChargesForServiceRequest($inputs['items']);

            // $service_request_id = db_insert_id();
            $items_batch = [];
            foreach ($inputs['items'] as $row) {

                if (empty($row['discount']))
                    $row['discount'] = 0;

                $tmp_array = [
                    'req_id' => $service_request_id,
                    'stock_id' => db_escape($row['stock_id']),
                    'description' => db_escape($row['description']),
                    'qty' => $row['qty'],
                    'govt_fee' => $row['govt_fee'],
                    'bank_service_charge' => $row['bank_charge'],
                    'bank_service_charge_vat' => $row['bank_charge_vat'],
                    'price' => $row['service_charge'],
                    'discount' => $row['discount'],
                    'unit_tax' => $row['tax'],
                    'application_id' => db_escape($row['application_id']),
                    'ref_name' => db_escape($row['ref_name'])
                ];

                if ($tmp_array['govt_fee'] <= 0 && $tmp_array['bank_service_charge_vat'] <= 0) {
                    $tmp_array['bank_service_charge'] = 0;
                }//DXBBMS

//                if ($tmp_array['govt_fee'] > 0) {
//                    if ($tmp_array['bank_service_charge'] <= 0 && $tmp_array['bank_service_charge_vat'] <= 0 ) {
//                        $tmp_array['bank_service_charge'] = $dflt_bank_chrgs[$row['stock_id']];
//                    }
//                } else {
//                    $tmp_array['bank_service_charge'] = 0;
//                }

                array_push($items_batch, $tmp_array);
            }

            db_insert_batch('0_service_request_items', $items_batch);

            commit_transaction();;

            return AxisPro::SendResponse(['status' => 'OK', 'msg' => $return_msg, 'print_url' => $this->generateUrlForServiceRequestPrint($service_request_id)]);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    public function getDefaultBankChargesForServiceRequest($requestItems)
    {
        if (empty($requestItems)) {
            return [];
        }

        $db_escape = function ($value) {
            return db_escape($value);
        };

        $stock_ids = implode(
            ',',
            array_map(
                $db_escape,
                array_column(
                    $requestItems,
                    'stock_id'
                )
            )
        );

        $dflt_bank_chrgs = db_query(
            "SELECT 
                sm.stock_id,
                ba.dflt_bank_chrg
            FROM 
                0_stock_master sm
            LEFT JOIN 0_bank_accounts ba on ba.account_code = sm.govt_bank_account
            WHERE sm.stock_id in ({$stock_ids})"
        )->fetch_all(MYSQLI_ASSOC);

        $dflt_bank_chrgs = array_column($dflt_bank_chrgs, 'dflt_bank_chrg', 'stock_id');

        return $dflt_bank_chrgs;
    }

    public function get_token_info($format = 'json')
    {

        try {

            $token = $_GET['token'];

            $token = db_escape($token);

            $sql = "select * from 0_axis_front_desk where 
            token=$token and date(created_at) = " . db_escape(date2sql(Today()) . " order by id desc");
            $get = db_query($sql);
            $return_result = db_fetch($get);

            return AxisPro::SendResponse(['status' => 'OK', 'data' => $return_result], $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    public function getTopTenCustomerTransaction($format = 'json')
    {

        try {


            $cat_id = $_GET['cat_id'];
            $from_date = $_GET['from_date'];
            $to_date = $_GET['to_date'];

            $where = "";

            if (!empty($from_date) && !empty($to_date)) {
                $from_date = date2sql($from_date);
                $to_date = date2sql($to_date);

                $where .= " and trans.tran_date >= " . db_escape($from_date);
                $where .= " and trans.tran_date <= " . db_escape($to_date);
            }

            if (!empty($cat_id))
                $where .= " and cat.category_id=$cat_id";

            $sql = "SELECT 

            cust.name AS customer_name, SUM(detail.quantity) AS qty 
            
            FROM 0_debtor_trans_details detail 
            
            LEFT JOIN 0_debtor_trans trans ON trans.trans_no = detail.debtor_trans_no AND trans.`type` = detail.debtor_trans_type 
            
            LEFT JOIN 0_stock_master stock ON stock.stock_id = detail.stock_id 
            
            LEFT JOIN 0_stock_category cat ON cat.category_id = stock.category_id 
            
            LEFT JOIN 0_debtors_master cust ON cust.debtor_no=trans.debtor_no
            
            WHERE detail.debtor_trans_type = 10 AND detail.quantity <> 0 $where GROUP BY cust.debtor_no ORDER BY qty DESC LIMIT 20";


            $return_result = [];
            $get = db_query($sql);
            while ($myrow = db_fetch($get))
                $return_result[] = $myrow;

            return AxisPro::SendResponse($return_result, $format);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * Process refund
     * Creates a payment voucher and allocates automatically
     * @param string $format
     * @return array|mixed
     */
    public function process_refund($format = 'json')
    {

        try {

            global $Refs;

            $allocs = $_POST['allocs'];
            $acc_rcvbl_account = 121001;
            $total_amount = 0;
            $customer_id = 1;
            foreach ($allocs as $alloc) {

                if ($alloc['amount'] > 0) {
                    $trans_info = get_customer_trans($alloc['trans_no'], ST_CUSTPAYMENT);
                    $customer_id = $trans_info['debtor_no'];
                    $total_amount += $alloc['amount'];
                }

            }

            $from_bank = 2;//Main Cash
            $amount = $total_amount;

            $date = Today();

            $ref = $Refs->get_next(ST_BANKPAYMENT, null, Today());
            $object = new items_cart(ST_BANKPAYMENT, $trans_no = 0);
            $gl_items = [];
            $gl_items[] = new gl_item($acc_rcvbl_account, 0, 0, $amount, '', '', $customer_id, '');

            $object->trans_type = ST_BANKPAYMENT;
            $object->line_items = '';
            $object->gl_items = $gl_items;
            $object->order_id = '';
            $object->from_loc = '';
            $object->to_loc = '';
            $object->tran_date = $date;
            $object->doc_date = $date;
            $object->event_date = $date;
            $object->transfer_type = '';
            $object->increase = '';
            $object->memo_ = 'REFUND TRANSACTION';
            $object->branch_id = '';
            $object->reference = $ref;
            $object->original_amount = '';
            $object->currency = '';
            $object->rate = '1';
            $object->source_ref = " ";
            $object->vat_category = " ";
            $object->tax_info = " ";
            $object->fixed_asset = " ";

            $trans = write_bank_transaction(
                1, 0, $from_bank,
                $object, $date,
                2, $customer_id, '0',
                $ref, 'REFUND TRANSACTION', true, null, 0, 0, '', 1);

            $trans_type = $trans[0];
            $trans_no = $trans[1];
            new_doc_date($date);

            foreach ($allocs as $alloc) {

                if ($alloc['amount'] > 0) {

                    add_cust_allocation($amount,
                        ST_CUSTPAYMENT, $alloc['trans_no'],
                        ST_BANKPAYMENT, $trans_no, $customer_id, $date);

                    update_debtor_trans_allocation(ST_BANKPAYMENT, $trans_no, $customer_id);
                    update_debtor_trans_allocation(ST_CUSTPAYMENT, $alloc['trans_no'], $customer_id);

                }

            }

            commit_transaction();
            return AxisPro::SendResponse(["status" => "OK", "msg" => "Refund Processed", 'pv_no' => $trans_no, 'print_url' => $this->generateUrlForRefundPrint($trans_no)], $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function generateUrlForRefundPrint($id)
    {
        return $GLOBALS['SysPrefs']->project_url . "/ERP/voucher_print/?voucher_id=$id-1";
    }

    public function get_customer_advances($format = 'json')
    {

        try {

            $from_date = begin_fiscalyear();
            $to_date = end_fiscalyear();
            $customer_id = $_GET['customer_id'];
            $rcpt_no = trim($_GET['rcpt_no']);
            $inv_no = trim($_GET['inv_no']);

            if (empty($customer_id) && empty($rcpt_no) && empty($inv_no))
                return AxisPro::SendResponse([], $format);

            $sql = get_sql_for_customer_allocation_inquiry($from_date, $to_date, $customer_id, 3, false);


            if (!empty($rcpt_no))
                $sql .= " AND trans.reference = " . db_escape($rcpt_no) . " ";

            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch_assoc($result)) {

                $allocated_invoices = $myrow['invoice_numbers'];
                $allocated_invoices = explode(", ", $allocated_invoices);

                if (!empty($inv_no)) {

                    if (!in_array($inv_no, $allocated_invoices))
                        continue;

                }

                $return_result[] = $myrow;
            }

            return AxisPro::SendResponse($return_result, $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }

    public function getAllCompanies($format = 'json')
    {

        try {

            $return_array = [
                [
                    'value' => 'Direct Axis Tech',
                    'id' => 1,
                ],
                [
                    'value' => 'Daxis',
                    'id' => 2,
                ]
            ];

            return AxisPro::SendResponse($return_array, $format);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }

    public function get_next_customer_id()
    {

        global $SysPrefs;

        $customer_id_prefix = $SysPrefs->prefs['customer_id_prefix'];

        if (empty($customer_id_prefix)) $customer_id_prefix = "";

        $sql = "select 
        LPAD(debtor_ref+1, 4, '0') as cust_id 
        from 0_debtors_master order by debtor_no desc limit 1 ";


        // display_error($sql);
        $res = db_fetch(db_query($sql));
        return $res['cust_id'] ?: $customer_id_prefix . '0001';

    }

    public function addCustomerBasicInfo($format = 'json')
    {

        try {

            begin_transaction();
            $cust_ref = $this->get_next_customer_id();

            $CustName = $_POST['cust_name'];
            $cust_mobile = $_POST['cust_mobile'];
            $cust_email = $_POST['cust_email'];


            $sql = "select count(*) as cnt from 0_debtors_master where mobile=" . db_escape($cust_mobile);
            $get = db_query($sql);
            $mobile_duplicate = db_fetch($get);

            $errors = [];
            if (empty($CustName))
                $errors['cust_name'] = "Please enter customer name";

            if (empty($cust_mobile))
                $errors['cust_mobile'] = "Please enter customer mobile";
            else if (strlen($cust_mobile) <> 10)
                $errors['cust_mobile'] = "Mobile number must have 10 digits Eg: 0512345678";
            else if ($mobile_duplicate['cnt'] > 0)
                $errors['cust_mobile'] = "This mobile number is already exists";

            if (!empty($errors))
                return AxisPro::SendResponse(['status' => 'FAIL', 'msg' => 'VALIDATION_FAILED', 'data' => $errors]);

            add_customer($CustName, $cust_ref, $address = "", $tax_id = 1, $curr_code = "AED",
                $dimension_id = 0, $dimension2_id = 0, $credit_status = 1, $payment_terms = 4, $discount = 0, $pymt_discount = 0,
                $credit_limit = 1000, $sales_type = 1, $notes = '', $cust_mobile, $cust_email);

            $selected_id = db_insert_id();

            add_branch($selected_id, $CustName, $cust_ref,
                $address = "", 0, 2, 1, '',
                get_company_pref('default_sales_discount_act'), get_company_pref('debtors_act'), get_company_pref('default_prompt_payment_act'),
                'DEF', '', 0, 1, '', null);

            $selected_branch = db_insert_id();

            add_crm_person($cust_ref, $CustName, '', '',
                $cust_mobile, '', '', $cust_email, '', '');

            $pers_id = db_insert_id();
            add_crm_contact('cust_branch', 'general', $selected_branch, $pers_id);

            add_crm_contact('customer', 'general', $selected_id, $pers_id);

            commit_transaction();

            return AxisPro::SendResponse(["status" => "OK", "msg" => "New Customer Added"], $format);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }


    public function addSubCustomer($format = 'json')
    {
        try {

            begin_transaction();
            //$cust_id = $_POST['cust_id'];
            $comp_name = $_POST['comp_name'];
            $created_by = $_SESSION['wa_current_user']->user;


            if ($_POST['radio_check_val'] == '1') {
                $cust_id = '1';
            } else if ($_POST['radio_check_val'] == '2') {
                $cust_id = $_POST['cust_id'];
            }

            $errors = [];
            if (empty($cust_id))
                $errors['cust_id'] = "Please select a customer";

            if (empty(trim($comp_name)))
                $errors['comp_name'] = "Please select a company name";

            if (!empty($errors))
                return AxisPro::SendResponse(['status' => 'FAIL', 'msg' => 'VALIDATION_FAILED', 'data' => $errors]);


            $sql = "INSERT into 0_sub_customers (customer_id,name,created_by,mobile) 
            VALUES ($cust_id," . db_escape($comp_name) . ",$created_by,'" . $_POST['cust_hdn_mobile'] . "')";
            db_query($sql);
            commit_transaction();

            return AxisPro::SendResponse(["status" => "OK", "msg" => "New Customer Company Added"], $format);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }

    public function get_sub_customers($format = 'json')
    {
        try {
            $customer_id = $_GET['customer_id'];
            $type = $_GET['radio_type'];
            $mobile = $_GET['mobile'];

            $sql = "SELECT id, name FROM " . TB_PREF . "sub_customers ";

            if ($type == '1') {
                $sql .= " WHERE customer_id =1 and mobile='" . $mobile . "' ";
            } else if ($type == '2') {
                $sql .= " WHERE customer_id ='" . $customer_id . "' and mobile='" . $mobile . "' ";
            }

            //echo $sql;
            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch($result)) {

                $return_result[] = $myrow;

            }
            return AxisPro::SendResponse(["data" => $return_result], $format);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    public function get_sub_ledgers($format = 'json')
    {
        if (empty($_GET['ledger'])) {
            return AxisPro::SendResponse([
                "status" => "FAIL",
                "msg" => "Please provide a ledger code"
            ]);
        }

        $ledgerId = db_escape($_GET['ledger']);
        $subledgers = db_query("SELECT * from 0_sub_ledgers WHERE ledger_id = {$ledgerId}")->fetch_all(MYSQLI_ASSOC);

        AxisPro::SendResponse($subledgers, $format);
    }

    public function getCustomerByMobile($format = 'json')
    {
        try {

            $mobile = db_escape($_GET['mobile']);

            $sql = "SELECT * FROM 0_axis_front_desk WHERE customer_mobile = $mobile ORDER BY id DESC LIMIT 1";
            if ($row = db_fetch(db_query($sql))) {
                return AxisPro::SendResponse(["data" => $row], $format);
            } else {
                $sql = (
                "SELECT 
                    debtor_no customer_id,
                    display_customer,
                    customer_trn,
                    customer_email,
                    customer_mobile,
                    contact_person,
                    NULL as customer_iban
                    FROM 0_debtor_trans
                    WHERE customer_mobile = $mobile
                    AND debtor_no = 1
                    LIMIT 1"
                );
                if ($row = db_fetch(db_query($sql))) {
                    return AxisPro::SendResponse(["data" => $row], $format);
                }
            }

            $sql = (
            "SELECT 
                debtor_no customer_id,
                `name` display_customer,
                tax_id customer_trn,
                debtor_email customer_email,
                mobile customer_mobile,
                contact_person,
                iban_no customer_iban
                FROM 0_debtors_master
                WHERE mobile = $mobile
                LIMIT 1"
            );

            $result = db_query($sql);
            $row = db_fetch($result);

            return AxisPro::SendResponse(["data" => $row], $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }

    public function getPendingServiceRequests($format = "json")
    {

        try {

            $cost_center_id = $_GET['cost_center_id'];

            $where = "";

            if (!empty($cost_center_id))
                $where .= " AND cost_center_id=$cost_center_id";

            $sql = " select req.id, req.display_customer,req.mobile, req.token_number, ifnull(req.reference,'') reference,
             SUM((item.unit_tax+item.price+item.govt_fee+item.bank_service_charge+item.bank_service_charge_vat-item.discount)*item.qty) as amount,usr.real_name as staff_name  
             from 0_service_requests req 
             left join 0_service_request_items item on item.req_id = req.id 
             left join 0_users usr on usr.id=req.created_by 
             where date(created_at) = " . db_escape(date2sql(Today())) . " and is_invoiced = 0 
             and active_status='ACTIVE' $where group by  item.req_id";

            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch($result)) {
                $return_result[] = $myrow;
            }

            return AxisPro::SendResponse($return_result, $format);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    public function getPaymentSummaryByMethod($format = 'json')
    {
        $user_date_format = getDateFormatInNativeFormat();
        if (
            !($dt = DateTime::createFromFormat($user_date_format, $_GET['trans_date']))
            || $dt->format($user_date_format) != $_GET['trans_date']
        ) {
            return AxisPro::SendResponse(["status" => "FAIL", "msg" => "The date is not valid"], $format);
        } else {
            $trans_date = $dt->format(MYSQL_DATE_FORMAT);
        }

        if (!preg_match('/^[1-9][0-9]{0,15}$/', $_GET['user_id'])) {
            return AxisPro::SendResponse(["status" => "FAIL", "msg" => "The user id is not valid"], $format);
        }

        $sql = (
        "SELECT 
                SUM(
                    ROUND( 
                        (dt.alloc - IFNULL(dt.ov_discount, 0)) + dt.credit_card_charge + dt.round_of_amount, 
                        2
                    )
                ) AS total,
                dt.payment_method 
            FROM 0_debtor_trans dt
            WHERE dt.type = 12 
                AND dt.ov_amount <> 0 
                AND dt.alloc <> 0  
                AND dt.tran_date = '$trans_date'
                AND dt.created_by = {$_GET['user_id']}
            GROUP BY dt.payment_method"
        );

        $summary = db_query($sql)->fetch_all(MYSQLI_ASSOC);
        if (!empty($summary)) {
            $summary = array_column($summary, 'total', 'payment_method');
        }

        if (empty($summary['Cash'])) {
            $summary['Cash'] = 0.00;
        }
        if (empty($summary['CreditCard'])) {
            $summary['CreditCard'] = 0.00;
        }
        if (empty($summary['BankTransfer'])) {
            $summary['BankTransfer'] = 0.00;
        }

        return AxisPro::SendResponse([
            "status" => "OK",
            "data" => $summary
        ], $format);
    }

    public function getServiceRequests()
    {

        $user_id = $_SESSION['wa_current_user']->user;
        $user_info = get_user($user_id);

        $sql = "SELECT req.id, req.barcode,req.token_number,req.is_invoiced,req.customer_id,req.display_customer,
    req.contact_person,req.iban,req.mobile,req.email,req.cost_center_id,req.created_by,
    req.created_at,cust.name customer_name,usr.user_id, usr.real_name user_real_name
    FROM 0_service_requests req
    LEFT JOIN 0_debtors_master cust ON cust.debtor_no=req.customer_id
    LEFT JOIN 0_users usr ON usr.id = req.created_by WHERE 1=1 ";


        $sql = "SELECT req.id, ifnull(req.reference,'') reference,ifnull(req.memo,'') memo,req.barcode,req.token_number,
req.is_invoiced,req.customer_id,req.display_customer,ifnull(trans.reference,'') invoice_number,
ifnull(GROUP_CONCAT(detail.transaction_id),'') transaction_ids,
req.contact_person,req.iban,req.mobile,req.email,req.cost_center_id,req.created_by,
req.created_at,cust.name customer_name,usr.user_id, usr.real_name user_real_name
FROM 0_service_requests req
LEFT JOIN 0_debtors_master cust ON cust.debtor_no=req.customer_id
LEFT JOIN 0_debtor_trans trans ON trans.trans_no=req.trans_no AND trans.`type`=10 

LEFT JOIN 0_debtor_trans_details detail 
ON detail.debtor_trans_no=trans.trans_no AND detail.debtor_trans_type=trans.`type`
LEFT JOIN 0_users usr ON usr.id = req.created_by WHERE 1=1 ";
        if (!empty($_POST['fl_start_date'])) {
            $sql .= " AND DATE(req.created_at) >= " . db_escape(date2sql($_POST['fl_start_date']));
        }

        if (!empty($_POST['fl_end_date'])) {
            $sql .= " AND DATE(req.created_at) <= " . db_escape(date2sql($_POST['fl_end_date']));
        }

        $having_clause = "";
        if (!empty($_POST['fl_status'])) {

            $status = 0;
            if ($_POST['fl_status'] == 'COMPLETED') {
                $status = 1;
            }

            if ($_POST['fl_status'] == 'TRANS_COMPLETED') {
                $having_clause .= " HAVING transaction_ids <> '' ";
                $status = 1;
            }

            $sql .= " AND req.is_invoiced = $status";
        }

//        if (!in_array($_SESSION['wa_current_user']->access, [2, 3])) {
//            $sql .= " AND req.cost_center_id=" . $user_info['dflt_dimension_id'];
//            $sql .= " AND req.created_by=" . $user_id;
//        }

        $sql .= " GROUP BY req.id $having_clause ORDER BY req.created_at DESC ";

        $total_count_sql = "select count(*) as cnt from ($sql) as tmpTable";
        $total_count_exec = db_fetch_assoc(db_query($total_count_sql));
        $total_count = $total_count_exec['cnt'];

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = 50;
        $offset = ($page * $perPage) - $perPage;


        $sql = $sql . " LIMIT $perPage OFFSET $offset";

        $result = db_query($sql);
        $report = [];
        while ($myrow = db_fetch_assoc($result))
            $report[] = $myrow;


        return AxisPro::SendResponse(
            [
                'rep' => $report,
                'total_rows' => $total_count,
                'pagination_link' => AxisPro::paginate($total_count),
                'users' => $this->get_key_value_records('0_users', 'id', 'user_id'),
                'aggregates' => $total_count_exec,]
        );

    }


    public function getServiceRequest($id = null, $format = 'json')
    {

        try {

            if (empty($id))
                $id = $_GET['id'];

            $sql = "SELECT * FROM 0_service_requests where id = $id";
            $result = db_fetch_assoc(db_query($sql));
            $req = $result;

            $sql = "SELECT items.* 
        FROM 0_service_request_items items 
        where items.req_id = $id ";


            $result = db_query($sql);
            $items = [];
            while ($myrow = db_fetch_assoc($result)) {

                $items[] = $myrow;
            }

            return AxisPro::SendResponse(['req' => $req, 'items' => $items], $format);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    public function get_customers_list()
    {
        $sql = "SELECT debtor.*,concat(debtor.debtor_ref,' - ',debtor.name) as custname,
    IFNULL(pro.salesman_name,'') AS pro_name FROM 0_debtors_master debtor 
    LEFT JOIN 0_salesman pro ON pro.salesman_code = debtor.salesman_id ";

        $result = db_query($sql);
        $sql3 = "SELECT category_id, description FROM 0_stock_category";
        $result3 = db_query($sql3);

        while ($myrow3 = db_fetch($result3)) {
            $catarray[] = $myrow3;
        }


        $return_result = [];

        while ($myrow = db_fetch($result)) {
            $return_result[$myrow['debtor_no']]['line'] = [];
            $return_result[$myrow['debtor_no']]['discdata'] = [];
            $return_result[$myrow['debtor_no']]['cust'] = $myrow;
            $return_result[$myrow['debtor_no']]['line'] = $catarray;

            $sql2 = "SELECT item_id,discount,customer_commission FROM customer_discount_items WHERE customer_id = " . $myrow['debtor_no'];
            $result2 = db_query($sql2);
            while ($myrow2 = db_fetch($result2)) {
                $return_result[$myrow['debtor_no']]['discdata'][$myrow2['item_id']]['discount'] = number_format2($myrow2['discount'], 2);
                $return_result[$myrow['debtor_no']]['discdata'][$myrow2['item_id']]['customer_commission'] = $myrow2['customer_commission'];
            }


        }

        return AxisPro::SendResponse($return_result);
    }


    public function getSearchItemsList($format = "json")
    {

        try {

            $user_id = $_SESSION['wa_current_user']->user;
            $user_info = get_user($user_id);
            $main_cat_id = isset($_GET['main_cat_id']) ? $_GET['main_cat_id'] : 0;
            $sub_cat_id = isset($_GET['sub_cat_id']) ? $_GET['sub_cat_id'] : 0;

            $user_dimension = $user_info['dflt_dimension_id'];

            $sql = "SELECT items.stock_id,cat.description category_name,items.description,items.long_description,
        IFNULL(price.price,0) service_fee,
        (items.govt_fee+items.bank_service_charge+items.bank_service_charge_vat) total_govt_fee,(IFNULL(price.price,0)+items.govt_fee) total_display_fee FROM 0_stock_master items 
        LEFT JOIN 0_prices price ON price.stock_id=items.stock_id 
        LEFT JOIN 0_stock_category cat ON cat.category_id=items.category_id 
        LEFT JOIN 0_subcategories subcat ON subcat.id = items.sub_category_id
        WHERE items.inactive=0 ";

            $sql .= " AND cat.dflt_dim1=$user_dimension";


            if (!empty($main_cat_id)) {
                $sql .= " AND items.category_id=$main_cat_id";
            }

            if (!empty($sub_cat_id)) {
                $sql .= " AND items.sub_category_id=$sub_cat_id";
            }

//            dd($sql);

            $get = db_query($sql);

            $items = [];
            while ($myrow = db_fetch_assoc($get)) {

                $items[] = $myrow;
            }


            return AxisPro::SendResponse($items, $format);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }


    }


    public function getCategoriesOfUserCostCenter($format = 'json')
    {
        try {

            global $path_to_root;

            $user_id = $_SESSION['wa_current_user']->user;
            $user_info = get_user($user_id);

            $user_dimension = $user_info['dflt_dimension_id'];

            $sql = " select * from 0_stock_category where inactive = 0 ";

            $sql .= " and dflt_dim1=$user_dimension";


            $get = db_query($sql);

            $return_result = [];
            $logo_dir = "ERP/themes/daxis/images/";

            while ($myrow = db_fetch_assoc($get)) {

                $logo = $logo_dir . "cat_logo_" . $myrow["description"] . ".png";

                if (!file_exists($logo)) {
                    $logo = "ERP/inventory/inquiry/default_category_image.png";
                }

                $myrow['category_logo'] = $logo;

                $return_result[] = $myrow;
            }

            return AxisPro::SendResponse($return_result, $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }


    public function getTopLevelSubcategories($format = 'json')
    {

        try {

            $category_id = $_GET['cat_id'];

            $sql = "select sub.*,cat.description category_name from 0_subcategories sub 
        LEFT JOIN 0_stock_category cat ON cat.category_id=sub.main_cat_id where main_cat_id=$category_id and parent_sub_cat_id=0";

            $get = db_query($sql);

            $return_result = [];
            $logo_dir = "ERP/themes/daxis/images/";

            while ($myrow = db_fetch_assoc($get)) {

                $logo = $logo_dir . "cat_logo_" . $myrow["category_name"] . ".png";

                if (!file_exists($logo)) {
                    $logo = "ERP/inventory/inquiry/default_category_image.png";
                }

                $myrow['category_logo'] = $logo;

                $return_result[] = $myrow;
            }

            return AxisPro::SendResponse($return_result, $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function getChildLevelSubcategories($format = 'json')
    {

        try {

            $id = $_GET['id'];

            $sql = "select sub.*,cat.description category_name from 0_subcategories sub 
        LEFT JOIN 0_stock_category cat ON cat.category_id=sub.main_cat_id where parent_sub_cat_id=$id ";

//            dd($sql);

            $get = db_query($sql);

            $return_result = [];
            $logo_dir = "ERP/themes/daxis/images/";

            while ($myrow = db_fetch_assoc($get)) {

                $logo = $logo_dir . "cat_logo_" . $myrow["category_name"] . ".png";

                if (!file_exists($logo)) {
                    $logo = "ERP/inventory/inquiry/default_category_image.png";
                }

                $myrow['category_logo'] = $logo;

                $return_result[] = $myrow;
            }

            return AxisPro::SendResponse($return_result, $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    // function get_invoices(){
    //     $sql = "SELECT t.trans_no as trans_no ,t.reference as ref ,t.tran_date as trans_date, t.type as type FROM `0_debtor_trans` t LEFT JOIN `0_voided` v ON t.trans_no=v.id AND v.type=10 WHERE ISNULL(v.`memo_`) AND t.trans_no >= '1' AND t.trans_no <= '999999' AND t.`type` = '10' GROUP BY t.type, t.trans_no ORDER BY t.trans_no DESC";
    // }
    private function get_invoice_records($params)
    {
        // $columns = array(
        //     0 => 't.trans_no',
        //     1 => 't.reference',
        //     2 => 't.tran_date',
        //     3 => 'cust.name',
        //     4 => 't.ov_amount+t.ov_gst+t.ov_freight+t.ov_freight_tax+t.ov_discount',
        //     5 => 'total_received',
        //     6 => 'cust_pay_reference_and_method',
        //     // 7 => 'pdc_list.from_type',
        //     // 8 => 'pdc_list.is_processed',
        //     // 9 => 'created_by_table.user_id',
        //     // 10 => 'processed_by_table.user_id',
        //     );
        $where_condition = $sqlTot = $sqlRec = "";
        // if(!empty($params['search']['value']) || $params['status'] || $params['type'] ){
        //     $where_condition .= " WHERE ";
        // }
        if (!empty($params['search']['value'])) {
            $where_condition .= " AND ( t1.trans_no LIKE '%" . $params['search']['value'] . "%' ";
            $where_condition .= " OR t1.reference LIKE '%" . $params['search']['value'] . "%' ";
            $where_condition .= " OR t1.tran_date LIKE '%" . $params['search']['value'] . "%' ";
            $where_condition .= " OR cust.name LIKE '%" . $params['search']['value'] . "%' )";
            // $where_condition .= " OR cust_pay_reference_and_method LIKE '%" . $params['search']['value'] . "%' )";
        }
        //add custom filter here
        if ($params['reference_no']) {
            $where_condition .= " AND t1.reference = " . db_escape($params['reference_no']) . " ";
        }
// if ($params['trans_date']) {
//     $where_condition .= " AND t.tran_date = " . db_escape(date2sql($params['trans_date'])) . " ";
// }
        if ($params['trans_date_from'] && $params['trans_date_to']) {
            $trans_date_to = date2sql($params['trans_date_to']);
            $trans_date_from = date2sql($params['trans_date_from']);
            if ($trans_date_from > $trans_date_to) {
                $temp = $trans_date_to;
                $trans_date_to = $trans_date_from;
                $trans_date_from = $temp;
            }
            $trans_date_from .= " 00:00:00";
            $trans_date_to .= " 23:59:59";
            $where_condition .= " AND t1.created_at >= " . db_escape($trans_date_from) . " AND t1.created_at <= " . db_escape($trans_date_to) . " ";
        }
        if ($params['customer_id']) {
            $where_condition .= " AND t1.debtor_no = " . db_escape($params['customer_id']) . " ";
        }

        if ($params['trans_no_from'] && $params['trans_no_to']) {
            $trans_no_from = min($params['trans_no_from'], $params['trans_no_to']);
            $trans_no_to = max($params['trans_no_from'], $params['trans_no_to']);
            $where_condition .= " AND t1.trans_no >= " . db_escape($trans_no_from) . " AND t1.trans_no <= " . db_escape($trans_no_to) . " ";
        }

        $sql_query = "SELECT cust.`name` AS DebtorName,t1.trans_no as trans_no ,t1.reference as ref ,t1.tran_date as trans_date,t1.created_at, t1.`type` AS `type`,
ROUND(t1.ov_amount+t1.ov_gst+t1.ov_freight+t1.ov_freight_tax+t1.ov_discount,2) AS Total,
ROUND(SUM(IF(t2.payment_method = 'Cash', t2.alloc, 0)),2) AS Cash,
ROUND(SUM(IF(t2.payment_method = 'CreditCard', t2.alloc, 0)),2) AS CreditCard,
ROUND(SUM(IF(t2.payment_method = 'BankTransfer', t2.alloc, 0)),2) AS BankTransfer,
ROUND(t1.alloc,2) AS total_received,
ROUND(t1.ov_amount+t1.ov_gst+t1.ov_freight+t1.ov_freight_tax+t1.ov_discount - t1.alloc,2) AS balance
FROM 0_debtor_trans t1 
LEFT JOIN 0_cust_allocations alloc ON  t1.trans_no = alloc.trans_no_to AND t1.`type` = alloc.trans_type_to
LEFT JOIN 0_debtor_trans t2 ON  t2.trans_no = alloc.trans_no_from AND t2.`type` = alloc.trans_type_from
LEFT JOIN 0_debtors_master cust ON t1.debtor_no = cust.debtor_no
WHERE t1.`type` = 10 ";

        $sqlTot .= $sql_query;
        $sqlRec .= $sql_query;
        if (isset($where_condition) && $where_condition != '') {
            $sqlTot .= $where_condition;
            $sqlRec .= $where_condition;
        }
        $sqlTot .= " GROUP BY alloc.trans_type_to,alloc.trans_no_to";
        $sqlRec .= " GROUP BY alloc.trans_type_to,alloc.trans_no_to ORDER BY ref DESC ";

        if (isset($params['start']) && isset($params['length'])) {
            $sqlRec .= " LIMIT " . $params['start'] . " ," . $params['length'];
        }
// if(isset($columns[$params['order'][0]['column']]) && isset($params['order'][0]['dir']) && isset($params['start']) && isset($params['length'])){
//     $sqlRec .=  " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . $params['order'][0]['dir'] . "  LIMIT " . $params['start'] . " ," . $params['length'] . " ";    
// }
        $sum_query = "Select sum(s.Total) as total_sum,sum(s.Cash) as pay_cash_sum,sum(s.CreditCard) as pay_creditcard_sum,sum(s.BankTransfer) as pay_bank_sum,sum(s.others) as pay_other_sum,sum(s.total_received) as total_received_sum,sum(s.balance) as total_balance_sum FROM ($sqlTot) as s";
        $sums_data = db_fetch_assoc(db_query($sum_query));
        $queryTot = db_query($sqlTot);
        $totalRecords = db_num_rows($queryTot);
        $queryRecords = db_query($sqlRec, "Error to Get the Post details.");
        return [
            'data' => $queryRecords,
            'total_records' => $totalRecords,
// 'sql' => $sum_query,
            'sums_data' => $sums_data,
        ];

    }

    //datatable listing method for pdc
    public function get_invoice_list_for_datatable()
    {
        $params = $columns = $totalRecords = $data = array();
        $params = $_REQUEST;

        $queryRecords = $this->get_invoice_records($params);
        $today = Today();
        $erp_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $erp_link = substr($erp_link, 0, strpos($erp_link, "ERP")) . "ERP/";
        $button_style = 'vertical-align:middle;width:18px;height:18px;border:0;';
        while ($row = db_fetch_assoc($queryRecords['data'])) {
            $actions = '';
            // $total = round($row['Total'],2);
            // $received = round($row['total_received'],2);
            // $balance = $total - $received;
            $created_at = $row['created_at'];
            //doing this way because sql2date function cannot return time and changing in helper page doesnt change here
            $created_date = sql2date($row['created_at']);
            $created_time = date('h:i:s A', strtotime($row['created_at']));
            $created_at = $created_date . " " . $created_time;
            $print_params = "PARAM_0=" . $row['trans_no'] . "-10&PARAM_1=" . $row['trans_no'] . "-10&PARAM_2=&PARAM_3=0&PARAM_4=&PARAM_5=&PARAM_6=&PARAM_7=0&REP_ID=107";
            $print_link = $erp_link . "invoice_print/index.php?" . $print_params;
            $print_invoice = "<a id='inv_print' target='_blank' href='" . $print_link . "'><img src='" . $erp_link . "themes/daxis/images/print.png' style='" . $button_style . "' title='Print Invoice'></a>";
            // $actions .= "<a class='btn btn-primary btn-sm' target='_blank' href='".$print_link."' title='Print Invoice'><i class='fa fa-print'></i></a>";
            $gl_link = "<a target='_blank' href='" . $erp_link . "gl/view/gl_trans_view.php?type_id=10&amp;trans_no=" . $row['trans_no'] . "' onclick='javascript:openWindow(this.href,this.target); return false;'><img src='" . $erp_link . "themes/daxis/images/gl.png' style='" . $button_style . "' title='GL'></a>";
            $data[] = array(
                // $row['trans_no'],
                $row['ref'],
                $created_date,
                $created_time,
                $row['DebtorName'],
                $row['Total'] ? $row['Total'] : '0.00',
                $row['Cash'] ? $row['Cash'] : '0.00',
                $row['CreditCard'] ? $row['CreditCard'] : '0.00',
                $row['BankTransfer'] ? $row['BankTransfer'] : '0.00',
                $row['others'] ? $row['others'] : '0.00',
                $row['total_received'] ? $row['total_received'] : '0.00',
                // $received,
                $row['balance'] ? $row['balance'] : '0.00',
                // $this->get_cust_payment_receipts_and_method_for_invoice_report($row['cust_pay_reference_and_method'],$return_gl_and_print_link=true),
                $gl_link,
                $print_invoice
            );
        }
        $json_data = array(
            "draw" => intval($params['draw']),
            "recordsTotal" => intval($queryRecords['total_records']),
            "recordsFiltered" => intval($queryRecords['total_records']),
            "data" => $data,
            "params" => $params,
            // "sql"             => $queryRecords['sql'],
            "total_sum" => $queryRecords['sums_data']['total_sum'] ? round($queryRecords['sums_data']['total_sum'], 2) : '0.00',
            "pay_cash_sum" => $queryRecords['sums_data']['pay_cash_sum'] ? round($queryRecords['sums_data']['pay_cash_sum'], 2) : '0.00',
            "pay_creditcard_sum" => $queryRecords['sums_data']['pay_creditcard_sum'] ? round($queryRecords['sums_data']['pay_creditcard_sum'], 2) : '0.00',
            "pay_bank_sum" => $queryRecords['sums_data']['pay_bank_sum'] ? round($queryRecords['sums_data']['pay_bank_sum'], 2) : '0.00',
            "pay_other_sum" => $queryRecords['sums_data']['pay_other_sum'] ? round($queryRecords['sums_data']['pay_other_sum'], 2) : '0.00',
            "total_balance_sum" => $queryRecords['sums_data']['total_balance_sum'] ? round($queryRecords['sums_data']['total_balance_sum'], 2) : '0.00',
            "total_received_sum" => $queryRecords['sums_data']['total_received_sum'] ? round($queryRecords['sums_data']['total_received_sum'], 2) : '0.00',
        );
        echo json_encode($json_data);
    }

    public function export_invoice_report()
    {
        try {


            $trans_date_from = date2sql($_GET['trans_date_from']);
            $trans_date_to = date2sql($_GET['trans_date_to']);
            $reference_no = (isset($_GET['reference_no']) && $_GET['reference_no'] != 'null') ? $_GET['reference_no'] : '';
            $customer_id = (isset($_GET['customer_id']) && $_GET['customer_id'] != 'null') ? $_GET['customer_id'] : '';
            $trans_no_from = (isset($_GET['trans_no_from']) && $_GET['trans_no_from'] != 'null') ? $_GET['trans_no_from'] : '';
            $trans_no_to = (isset($_GET['trans_no_to']) && $_GET['trans_no_to'] != 'null') ? $_GET['trans_no_to'] : '';
            $export_type = (isset($_GET['export_type'])) ? $_GET['export_type'] : 'excel';
            $params = [
                'reference_no' => $reference_no,
                'trans_date_from' => $_GET['trans_date_from'],
                'trans_date_to' => $_GET['trans_date_to'],
                'customer_id' => $customer_id,
                'trans_no_from' => $trans_no_from,
                'trans_no_to' => $trans_no_to,
            ];

            $reference_no = $reference_no == '' ? 'All' : $reference_no;
            $trans_no_from = $trans_no_from == '' ? 'All' : $trans_no_from;
            $trans_no_to = $trans_no_to == '' ? 'All' : $trans_no_to;
            if ($trans_date_from > $trans_date_to) {
                $temp = $trans_date_to;
                $trans_date_to = $trans_date_from;
                $trans_date_from = $temp;
            }


            if ((abs(round((strtotime($trans_date_to) - strtotime($trans_date_from)) / 86400)) <= 31) && $_GET['trans_date_from'] != '' && $_GET['trans_date_to'] != '') {

                $customer_name = '';
                if ($customer_id) {
                    $customer = "SELECT name,debtor_no FROM 0_debtors_master where debtor_no='" . $customer_id . "'";
                    $customer_data = db_fetch_assoc(db_query($customer));
                    $customer_name = $customer_data['name'];
                }


                set_time_limit(0);
                $queryRecords = $this->get_invoice_records($params);
                $invoice_report_data = $queryRecords['data']->fetch_all(MYSQLI_ASSOC);
                $filename = "Invoices_Report";
                global $path_to_root;
                $page = 'A4';
                $orientation = 'L';
                if ($export_type == 'pdf') {
                    include_once($path_to_root . "/reporting/includes/pdf_report.inc");
                } else {
                    include_once($path_to_root . "/reporting/includes/excel_report.inc");
                    // In excel columns are too much congested
                    $page = 'A3';
                    $orientation = 'L';
                }
                if (!empty($invoice_report_data)) {

                    $columns = [
                        // [
                        // "key"   => "trans_no",
                        // "title" => _('#'),
                        // "align" => "left",
                        // "width" => 30
                        // ],
                        [
                            "key" => "si_no",
                            "title" => _('SI'),
                            "align" => "left",
                            "width" => 20
                        ],
                        [
                            "key" => "reference_no",
                            "title" => _('Reference'),
                            "align" => "center",
                            "width" => 40
                        ],
                        [
                            "key" => "tran_date",
                            "title" => _('Date'),
                            "align" => "left",
                            "width" => 50
                        ],
                        [
                            "key" => "tran_time",
                            "title" => _('Time'),
                            "align" => "left",
                            "width" => 50
                        ],
                        [
                            "key" => "customer",
                            "title" => _('Customer'),
                            "align" => "left",
                            "width" => 60
                        ],
                        [
                            "key" => "amount",
                            "title" => _('Total Amount'),
                            "align" => "center",
                            "width" => 40
                        ],
                        [
                            "key" => "Cash",
                            "title" => _('Cash'),
                            "align" => "center",
                            "width" => 35
                        ],
                        [
                            "key" => "CreditCard",
                            "title" => _('Debit/Credit Card'),
                            "align" => "center",
                            "width" => 50
                        ],
                        [
                            "key" => "BankTransfer",
                            "title" => _('Bank Transfer'),
                            "align" => "center",
                            "width" => 50
                        ],
                        [
                            "key" => "others",
                            "title" => _('Others'),
                            "align" => "center",
                            "width" => 30
                        ],
                        [
                            "key" => "total_received",
                            "title" => _('Amount Received'),
                            "align" => "center",
                            "width" => 50
                        ],
                        [
                            "key" => "balance_amount",
                            "title" => _('Balance Amount'),
                            "align" => "center",
                            "width" => 40
                        ],
                        // [
                        // "key"   => "receipts_and_payment_methods",
                        // "title" => _('Receipts & Payment Methods'),
                        // "align" => "left",
                        // "width" => 80
                        // ],

                    ];

                    $colDef = $this->calculateColumnInfo($columns, $page, $orientation);

                    /**
                     * 0th parameter is Comment.
                     * Can pass any comment if needed and it will show up in the header of the report
                     */
                    $param[] = "";
                    /**
                     * additional parameters are provided in a two column
                     * format with seperater '-'
                     * [
                     *    "text" => the attribute name,
                     *    "from" => first column
                     *    "to"   => second column
                     * ]
                     */
                    $param[] = [
                        "text" => _("Reference No."),
                        "from" => $reference_no,
                        "to" => ''
                    ];
                    $param[] = [
                        "text" => _("#"),
                        "from" => $trans_no_from,
                        "to" => $trans_no_to
                    ];
                    $param[] = [
                        "text" => _("Date"),
                        "from" => sql2date($trans_date_from),
                        "to" => sql2date($trans_date_to)
                    ];
                    $param[] = [
                        "text" => _("Customer"),
                        "from" => $customer_name,
                        "to" => ''
                    ];

                    $rep = new FrontReport(_("Invoices Report"), $filename, $page, 9, $orientation);
                    $rep->Font();
                    $rep->Info(
                        $param,
                        $colDef->points,
                        array_column($columns, 'title'),
                        array_column($columns, 'align')
                    );
                    $rep->NewPage();
                    $count = 0;
                    foreach ($invoice_report_data as $row) {
                        $count++;
                        // $total = round($row['Total'],2);
                        // $received = round($row['total_received'],2);
                        // $balance = $total - $received;     
                        $created_date = sql2date($row['created_at']);
                        $created_time = date('h:i:s A', strtotime($row['created_at']));
                        $created_at = $created_date . " " . $created_time;

                        // $rep->TextCol(
                        //     $colDef->index['trans_no'][0],
                        //     $colDef->index['trans_no'][1],
                        //     $row['trans_no']
                        //     );

                        $rep->TextCol(
                            $colDef->index['si_no'][0],
                            $colDef->index['si_no'][1],
                            $count
                        );
                        $rep->TextCol(
                            $colDef->index['reference_no'][0],
                            $colDef->index['reference_no'][1],
                            $row['ref']
                        );
                        $rep->DateCol(
                            $colDef->index['tran_date'][0],
                            $colDef->index['tran_date'][1],
                            $created_date
                        );
                        $rep->TextCol(
                            $colDef->index['tran_time'][0],
                            $colDef->index['tran_time'][1],
                            $created_time
                        );
                        $rep->TextCol(
                            $colDef->index['customer'][0],
                            $colDef->index['customer'][1],
                            $row['DebtorName']
                        );
                        $rep->TextCol(
                            $colDef->index['amount'][0],
                            $colDef->index['amount'][1],
                            $row['Total'] ? $row['Total'] : 0
                        );
                        $rep->TextCol(
                            $colDef->index['Cash'][0],
                            $colDef->index['Cash'][1],
                            $row['Cash'] ? $row['Cash'] : 0
                        );
                        $rep->TextCol(
                            $colDef->index['CreditCard'][0],
                            $colDef->index['CreditCard'][1],
                            $row['CreditCard'] ? $row['CreditCard'] : 0
                        );
                        $rep->TextCol(
                            $colDef->index['BankTransfer'][0],
                            $colDef->index['BankTransfer'][1],
                            $row['BankTransfer'] ? $row['BankTransfer'] : 0
                        );
                        $rep->TextCol(
                            $colDef->index['others'][0],
                            $colDef->index['others'][1],
                            $row['others'] ? $row['others'] : 0
                        );
                        $rep->TextCol(
                            $colDef->index['total_received'][0],
                            $colDef->index['total_received'][1],
                            $row['total_received'] ? $row['total_received'] : 0
                        );
                        $rep->TextCol(
                            $colDef->index['balance_amount'][0],
                            $colDef->index['balance_amount'][1],
                            $row['balance'] ? $row['balance'] : 0
                        );
                        // $rep->TextCol(
                        //     $colDef->index['receipts_and_payment_methods'][0],
                        //     $colDef->index['receipts_and_payment_methods'][1],
                        //     $this->get_cust_payment_receipts_and_method_for_invoice_report($row['cust_pay_reference_and_method'],$return_gl_and_print_link=false)
                        //     );

                        $rep->NewLine();

                        if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
                            $rep->Line($rep->row - 2);
                            $rep->NewPage();
                        }
                    }
                    $rep->End();
                }
            } else {
                display_error("Wrong Date Range Given for Invoice Report Export!!!");
                // echo "Wrong Date Range Given for Invoice Report Export!!!";
            }
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }

    private function get_cust_payment_receipts_and_method_for_invoice_report($cust_pay_reference_and_method, $return_gl_and_print_link = false)
    {
        $erp_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $erp_link = substr($erp_link, 0, strpos($erp_link, "ERP")) . "ERP/";
        $button_style = 'vertical-align:middle;width:18px;height:18px;border:0;';
        $cust_pay_reference_and_method_arr = explode(' , ', $cust_pay_reference_and_method);
        $cust_ref_payment_method = [];
        foreach ($cust_pay_reference_and_method_arr as $val) {
            $var2 = explode(' - ', $val);
            if ($var2[0] != '' && $var2[1] != '') {
                $receipts_and_method_string = $var2[0] . "(" . $var2[1] . ")";
                $print_params = "PARAM_0=" . $var2[2] . "-" . $var2[3] . "&PARAM_1=" . $var2[2] . "-" . $var2[3] . "&PARAM_2=&PARAM_3=&PARAM_4=0&PARAM_5=0&REP_ID=112";
                $print_receipt = "<a id='inv_print' target='_blank' href='" . $erp_link . "reporting/prn_redirect.php?" . $print_params . "'><img src='" . $erp_link . "themes/daxis/images/print.png' style='" . $button_style . "' title='Print Receipt'></a>";
                $gl_link = "<a target='_blank' href='" . $erp_link . "gl/view/gl_trans_view.php?type_id=" . $var2[3] . "&amp;trans_no=" . $var2[2] . "' onclick='javascript:openWindow(this.href,this.target); return false;'><img src='" . $erp_link . "themes/daxis/images/gl.png' style='" . $button_style . "' title='GL'></a>";
                $cust_ref_payment_method[] = $return_gl_and_print_link == false ? $receipts_and_method_string : "<span>" . $gl_link . " " . $print_receipt . " " . $receipts_and_method_string . "</span>";
            } else {
                $cust_ref_payment_method[] = '-';
            }
        }
        return implode($return_gl_and_print_link == false ? ' , ' : '<br>', $cust_ref_payment_method);

    }

// function get_customers_for_select2(){

// $sql = "SELECT debtor_no, concat(debtor_ref,' - ',name) as custname FROM `0_debtors_master` 
//         WHERE name LIKE '%".$_GET['q']."%' OR debtor_ref LIKE '%" . $_GET['q'] . "%' 
//         LIMIT 10";
//     $result = db_query($sql);

//     $return_result = [];
//     while ($myrow = db_fetch($result)) {
//         $return_result[] = ['id'=>$myrow['debtor_no'], 'text'=>$myrow['custname']];
//     }

//     return AxisPro::SendResponse($return_result, 'json');
// }

    public function calculateColumnInfo(
        $columns,
        $page = 'A4',
        $orientation = 'P',
        $margin = null
    )
    {
        $points = [0,];
        $index = [];
        $width = 0;

        // All mesurements are in 72 ppi and taken from reporting/includes/pdf_report.inc
        // -----------------------------
        // P => width in Portrait mode,
        // L => width in Landscape mode
        $pages = [
            "A4" => [
                "P" => 595,
                "L" => 842,
                "margin" => [
                    "left" => 40,
                    "right" => 30
                ]
            ],
            "A3" => [
                "P" => 842,
                "L" => 1198,
                "margin" => [
                    "left" => 50,
                    "right" => 40
                ]
            ],
        ];
        $marginL = empty($margin["left"]) ? $pages[$page]['margin']['left'] : $margin["left"];
        $marginR = empty($margin["right"]) ? $pages[$page]['margin']['right'] : $margin["right"];
        $maxWidth = $pages[$page][$orientation] - $marginL - $marginR;

        foreach ($columns as $key => $col) {
            $width += $col["width"];
            $points[] = $width;
            $index[$col['key']] = [$key, $key + 1];
        }

        $scale = $maxWidth / $width;
        $points = array_map(function ($val) use ($scale) {
            return floor($val * $scale);
        }, $points);

        return (object)[
            "points" => $points,
            "index" => $index
        ];
    }

    public function fetchTodaysServiceRequestsByToken($format = 'json')
    {

        try {

            $token = $_GET['token'];

            $sql = "select req.id, req.display_customer,req.mobile, req.token_number, ifnull(req.reference,'') reference,
             SUM((item.unit_tax+item.price+item.govt_fee+item.bank_service_charge+item.bank_service_charge_vat-item.discount)*item.qty) as amount 
             from 0_service_requests req left join 0_service_request_items item on item.req_id = req.id 
             where date(created_at) = " . db_escape(date2sql(Today())) . " and is_invoiced = 0 
             and token_number=" . db_escape($token) . " group by  item.req_id";

            $get = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch_assoc($get)) {
                $return_result[] = $myrow;
            }

            return AxisPro::SendResponse($return_result, $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function getInvoicedApplicationIDs($format = 'json')
    {

        try {

            $tasheel_only = isset($_GET['tasheel_only']) ?: 0;

            $sql = "SELECT TRIM(detail.application_id) application_id 
                    FROM 0_debtor_trans_details detail
                    LEFT JOIN 0_debtor_trans trans ON trans.trans_no=detail.debtor_trans_no 
                    AND trans.`type`=detail.debtor_trans_type 
                    LEFT JOIN 0_stock_master item ON item.stock_id=detail.stock_id
                    LEFT JOIN 0_stock_category cat ON cat.category_id = item.category_id
                    WHERE detail.application_id <> '' AND trans.`type`=10 and detail.quantity <> 0 ";


            if ($tasheel_only)
                $sql .= " and cat.is_tasheel=1";

            $sql .= " ORDER BY detail.id DESC LIMIT 800";


            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch($result)) {
                $return_result[] = trim($myrow['application_id'], " ");
            }

            return AxisPro::SendResponse($return_result, $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function getServiceRequestApplicationIDs($format = 'json')
    {

        try {

            $sql = "SELECT TRIM(application_id) application_id 
                    FROM 0_service_request_items
                   ";

            $sql .= " ORDER BY id DESC LIMIT 800";


            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch($result)) {
                $return_result[] = trim($myrow['application_id'], " ");
            }

            return AxisPro::SendResponse($return_result, $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * Returns the reception report
     *
     * @param string $format
     * @return void
     */
    public function getReceptionReport($format = 'json')
    {
        $validate = function () {
            $errors = [];
            // validate customer id
            if (
                !empty($_GET['cust_id'])
                && !preg_match('/^[1-9][0-9]*$/', $_GET['cust_id'])
            ) {
                $errors['cust_id'] = 'Customer id is invalid!';
            }

            // validate customer filter
            if (
                !empty($_GET['cust_filter'])
                && !preg_match('/[a-zA-Z_ 0-9]*$/', $_GET['cust_filter'])
            ) {
                $errors['cust_filter'] = 'Filter can only contain letters (a-z, A-Z), numbers (0-9), underscore (_) and space (<space>)';
            }

            // validate date from
            if (
                !empty($_GET['dt_from'])
                && (
                    !($dt_from = DateTime::createFromFormat('d/m/Y', $_GET['dt_from']))
                    || $dt_from->format('d/m/Y') != $_GET['dt_from']
                )
            ) {
                $errors['dt_from'] = 'From date is invalid';
            } else {
                $dt_from && $_GET['_dt_from'] = $dt_from->format('Y-m-d');
            }

            // validate date to
            if (
                !empty($_GET['dt_to'])
                && (
                    !($dt_to = DateTime::createFromFormat('d/m/Y', $_GET['dt_to']))
                    || $dt_to->format('d/m/Y') != $_GET['dt_to']
                )
            ) {
                $errors['dt_to'] = 'To date is invalid';
            } else {
                $dt_to && $_GET['_dt_to'] = $dt_to->format('Y-m-d');
            }

            return $errors;
        };

        if (!empty($errors = $validate())) {
            return AxisPro::SendResponse(['status' => 'FAIL', 'errors' => $errors], $format);
        }

        $buildWhere = function () {
            $where = '';
            !empty($_GET['cust_id']) && $where .= " AND r.customer_id = {$_GET['cust_id']}";
            !empty($_GET['cust_filter']) && $where .= " AND (r.display_customer LIKE '%{$_GET['cust_filter']}%' OR r.customer_mobile LIKE '%{$_GET['cust_filter']}%')";
            !empty($_GET['_dt_from']) && $where .= " AND DATE_FORMAT(r.created_at, '%Y-%m-%d') >= '{$_GET['_dt_from']}'";
            !empty($_GET['_dt_to']) && $where .= " AND DATE_FORMAT(r.created_at, '%Y-%m-%d') <= '{$_GET['_dt_to']}'";

            return $where;
        };
        $where = $buildWhere();

        $sql = (
        "SELECT 
                r.token,
                r.customer_id cust_id,
                r.display_customer `display_name`,
                d.name `real_name`,
                r.customer_mobile mobile_no,
                r.customer_email email,
                r.created_at `date`
            FROM 0_axis_front_desk r 
                LEFT JOIN 0_debtors_master d ON d.debtor_no = r.customer_id
            WHERE 1=1 $where
            ORDER BY r.created_at DESC"
        );

        $total_count = db_query("SELECT COUNT(1) cnt FROM ($sql) t1")->fetch_row()[0];
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = 50;
        $offset = ($page * $perPage) - $perPage;
        $sql .= " LIMIT $perPage OFFSET $offset";

        $report = [];
        $utcTimeZone = new DateTimeZone('UTC');
        $localTimeZone = (new DateTime())->getTimezone();
        $dateTimeFormat = getDateFormatInNativeFormat() . ' h:i:s A';
        $mysqli_result = db_query($sql);
        while ($row = $mysqli_result->fetch_assoc()) {
            $dt = DateTime::createFromFormat(MYSQL_DATE_TIME_FORMAT, $row['date'], $utcTimeZone);
            $dt->setTimezone($localTimeZone);
            $row['date'] = $dt->format($dateTimeFormat);
            $report[] = $row;
        }

        return AxisPro::SendResponse([
            'data' => $report,
            'pagination_links' => AxisPro::paginate($total_count, $perPage)
        ], $format);
    }


    public function getDailySalesSummary($filters = [], $format = "json")
    {

        try {


            $start_date = $filters['START_DATE'];
            $end_date = $filters['END_DATE'];

            if (!empty($start_date)) {
                $start_date = date2sql($start_date);
            }
            if (!empty($end_date)) {
                $end_date = date2sql($end_date);
            }

            $reportSql = "(SELECT description,  category_id, 
            ROUND(SUM(t1.total_pro_discount), 2) total_pro_discount, 
            ROUND(SUM(t1.total_govt_fee), 2) AS total_govt_fee, 
            SUM(t1.total_service_count) AS total_service_count, 
            ROUND(SUM(t1.total_service_charge),2) AS total_service_charge,
            ROUND(SUM(t1.total_tax),2) AS total_tax, 
            ROUND(SUM(t1.line_total),2) AS total_collection,
            0 AS total_credit_facility
            FROM (
                SELECT  d.category_id, 
                ((`a`.`unit_price` * `a`.`quantity`) - 
                (`a`.`discount_amount` * `a`.`quantity`) - 
                (`a`.`pf_amount` * `a`.`quantity`)) AS `net_service_charge`, 
                a.discount_amount * a.quantity AS total_pro_discount,
                 a.quantity AS total_service_count, g.description AS description, 
                 ROUND(a.unit_tax*a.quantity,2) AS total_tax, 
                 (a.unit_price - a.pf_amount) * a.quantity AS total_service_charge, 
                 CASE WHEN b.payment_flag IN (2,3) THEN 0 ELSE 
                 (a.govt_fee + a.bank_service_charge + a.bank_service_charge_vat + a.pf_amount) * a.quantity END 
                    AS total_govt_fee,
 
                 ((a.govt_fee + a.bank_service_charge + a.bank_service_charge_vat + 
                 a.pf_amount+a.unit_price-a.discount_amount+a.unit_tax)*a.quantity) line_total
                
                FROM `0_debtor_trans_details` `a`
                LEFT JOIN `0_debtor_trans` `b` ON `b`.`trans_no` = `a`.`debtor_trans_no`
                LEFT JOIN `0_debtors_master` `c` ON `c`.`debtor_no` = `b`.`debtor_no`
                LEFT JOIN `0_users` ON `0_users`.`id` = `a`.`created_by`
                LEFT JOIN `0_stock_master` `d` ON `d`.`stock_id` = `a`.`stock_id`
                LEFT JOIN `0_stock_category` `g` ON `g`.`category_id` = `d`.`category_id`
                LEFT JOIN `customer_rewards` `e` ON `e`.`trans_no` = `b`.`trans_no` AND `e`.`trans_type` = 10 
                AND (`e`.`stock_id` = `a`.`stock_id`)
                LEFT JOIN `customer_discount_items` `f` ON 
                    `f`.`item_id` = `d`.`category_id` AND `c`.`debtor_no` = `f`.`customer_id`
                WHERE `a`.`debtor_trans_type` = 10 AND `b`.`reference` <> 'auto' AND `b`.`type` = 10 
                AND `a`.`quantity` <> 0 AND `b`.`ov_amount` <> 0 AND b.tran_date >= '$start_date' 
                 AND b.tran_date <= '$end_date' 

                GROUP BY `b`.`reference`,`a`.`stock_id`,`a`.`id`
                ) AS t1
                GROUP BY category_id)


                UNION 
                
                (SELECT description,  category_id, 
                ROUND(SUM(t1.total_pro_discount), 2) total_pro_discount, ROUND(SUM(t1.total_govt_fee), 2) AS total_govt_fee, 
                SUM(t1.total_service_count) AS total_service_count, ROUND(SUM(t1.total_service_charge),2) AS total_service_charge,
                ROUND(SUM(t1.total_tax),2) AS total_tax, 
                ROUND(SUM(t1.line_total),2) AS total_collection,ROUND(SUM(t1.total_credit_facility),2) AS total_credit_facility
                FROM (
                
                SELECT  d.category_id, 
                0 AS `net_service_charge`, 0 AS total_pro_discount,
                0 AS total_service_count, g.description AS description, 
                0 AS total_tax, 
                0 AS total_service_charge, 
                0 AS total_govt_fee, 
                0 AS line_total,
 
                ((a.govt_fee + a.bank_service_charge + a.bank_service_charge_vat + 
                a.pf_amount+a.unit_price-a.discount_amount+a.unit_tax)*a.quantity) total_credit_facility

                FROM `0_debtor_trans_details` `a`
                LEFT JOIN `0_debtor_trans` `b` ON `b`.`trans_no` = `a`.`debtor_trans_no`
                LEFT JOIN `0_debtors_master` `c` ON `c`.`debtor_no` = `b`.`debtor_no`
                LEFT JOIN `0_users` ON `0_users`.`id` = `a`.`created_by`
                LEFT JOIN `0_stock_master` `d` ON `d`.`stock_id` = `a`.`stock_id`
                LEFT JOIN `0_stock_category` `g` ON `g`.`category_id` = `d`.`category_id`
                LEFT JOIN `customer_rewards` `e` ON `e`.`trans_no` = `b`.`trans_no` AND `e`.`trans_type` = 10 
                AND (`e`.`stock_id` = `a`.`stock_id`)
                LEFT JOIN `customer_discount_items` `f` ON `f`.`item_id` = `d`.`category_id` AND `c`.`debtor_no` = `f`.`customer_id`
                WHERE `a`.`debtor_trans_type` = 10 AND `b`.`reference` <> 'auto' AND `b`.`type` = 10 
                AND `a`.`quantity` <> 0 AND `b`.`ov_amount` <> 0 AND b.tran_date >= '$start_date' AND b.tran_date <= '$end_date' 

                AND b.payment_method='CreditCustomer'
                
                GROUP BY `b`.`reference`,`a`.`stock_id`,`a`.`id`
                
                ) AS t1
            GROUP BY category_id)";

            $sql = "SELECT description,category_id,SUM(total_pro_discount) total_pro_discount,
            SUM(total_govt_fee) total_govt_fee, SUM(total_service_count) total_service_count, 
            SUM(total_service_charge) total_service_charge,
            SUM(total_tax) total_tax, SUM(total_collection) total_collection, 
            SUM(total_credit_facility) total_credit_facility 

            FROM (
            
            $reportSql
            
            ) AS MyTable
            GROUP BY category_id
            ORDER BY total_service_count DESC";


            $get = db_query($sql);
            $return_result = [];
            while ($myrow = db_fetch($get)) {

                $myrow['total_collection'] = $myrow['total_govt_fee'] +
                    $myrow['total_service_charge'] - $myrow['total_pro_discount'] + $myrow['total_tax'];

                $return_result[] = $myrow;
            }
            return AxisPro::SendResponse($return_result, $format);

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    public function getBankBalanceReport($filters = [], $format = "json")
    {

        try {

            $account_codes = "112001,112002,113002,113005,113001,113004,113007,113006,113003";

            $start_date = $filters['START_DATE'];

            if (!empty($start_date)) {
                $start_date = date2sql($start_date);
            }

            $sql = (
            "SELECT 
                    *, 
                    (
                        SELECT 
                            IFNULL(ROUND(SUM(amount),2),0)
                        FROM 
                            0_gl_trans 
                        WHERE `account` = account_code 
                            AND tran_date < '$start_date'
                    ) AS opening_bal,
                    (
                        SELECT
                            IFNULL(ROUND(SUM(ABS(amount)),2),0)
                        FROM 
                            0_gl_trans
                        WHERE `account` = account_code 
                            AND tran_date = '$start_date'
                            AND amount > 0
                    ) AS debit,
                    (
                        SELECT 
                            IFNULL(ROUND(SUM(ABS(amount)),2),0)
                        FROM
                            0_gl_trans
                        WHERE `account` = account_code 
                            AND tran_date = '$start_date'
                            AND amount < 0
                    ) AS credit
                FROM (
                    SELECT 
                        coa.account_code,
                        coa.account_name 
                    FROM 
                        0_chart_master coa
                    LEFT JOIN 0_gl_trans trans ON trans.account=coa.account_code
                    WHERE coa.account_code IN ($account_codes) 
                        AND if(trans.tran_date <> NULL, trans.tran_date='$start_date', 1=1)
                    GROUP BY coa.account_code
                        
                ) AS MyTable"
            );

            $get = db_query($sql);
            $return_result = [];
            while ($myrow = db_fetch($get)) {

                /** If AL Masraf Do the credit calculation for displaying */
                if ($myrow['account_code'] == '112001') {
                    $myrow['opening_bal'] = 14800000 + $myrow['opening_bal'];
                }

                $myrow['balance'] = $myrow['opening_bal'] +
                    $myrow['debit'] - $myrow['credit'];

                $return_result[] = $myrow;
            }
            return AxisPro::SendResponse($return_result, $format);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function getCollectionBreakDownReport($filters = [], $format = "json")
    {

        try {

            $start_date = $filters['START_DATE'];

            if (!empty($start_date)) {
                $start_date = date2sql($start_date);
            }

            $sql = "SELECT 'Cash  نقدا' as description,
                IFNULL(ROUND(sum(ov_amount+credit_card_charge+round_of_amount),2),0) AS amount 
                from 0_debtor_trans 
                where payment_method='Cash' and type=12 and tran_date='$start_date' and ov_amount <> 0 UNION 
                
                SELECT 'Credit Card  بطاقة ائتمان' as description,
                IFNULL(ROUND(sum(round(ov_amount+credit_card_charge+round_of_amount,2)),2),0) AS amount 
                from 0_debtor_trans 
                where payment_method='CreditCard' and type=12 and tran_date='$start_date' and ov_amount <> 0 UNION 
                
                SELECT 'Bank Transfer  تحويل الحساب البنكي' as description,
                IFNULL(ROUND(sum(ov_amount+credit_card_charge+round_of_amount),2),0) AS amount 
                from 0_debtor_trans 
                where payment_method='BankTransfer' and type=12 and tran_date='$start_date' and ov_amount <> 0  UNION 
                              
                SELECT 'Advance Received Today' as description,IFNULL(ROUND(sum(ov_amount+credit_card_charge-alloc),2),0) AS amount 
                from 0_debtor_trans WHERE `type` in (12,2) and tran_date='$start_date' and ov_amount <> 0  UNION

                SELECT 'Total Actual Collection اجمالي التحصيل الفعلي' as description,
                0-IFNULL(ROUND(sum(ov_amount+credit_card_charge+round_of_amount),2),0) AS amount from 0_debtor_trans 
                where type=12 and tran_date='$start_date' UNION
                
                SELECT 'Credit Invoices  فواتير دفع اجل اليوم' AS description,
                IFNULL(ROUND(sum(ov_amount+ov_gst),2),0) AS amount 
                FROM 0_debtor_trans WHERE `type` = 10 and tran_date='$start_date' and ov_amount <> 0 
                AND payment_method = 'CreditCustomer'";

//            print_r($sql); die;

            $get = db_query($sql);
            $return_result = [];
            while ($myrow = db_fetch($get)) {
                $return_result[] = $myrow;
            }

            $tomorrow = DateTime::createFromFormat(MYSQL_DATE_FORMAT, $start_date)
                ->add(new DateInterval('P1D'))
                ->format(MYSQL_DATE_FORMAT);
            $customer_bal_sql = get_sql_for_opening_balance_of_customer_balance_inquiry(null, $tomorrow);
            $amt_receivable = db_query(
                "SELECT 
                    'Credit Customer Balance Till Date' AS `description`,
                    SUM(t3.opening_bal) AS amount
                FROM ({$customer_bal_sql} HAVING SUM(t2.pending) - SUM(t2.prepaid) > 0.0004) AS t3"
            )->fetch_assoc();;

            if (!empty($amt_receivable)) {
                $return_result[] = $amt_receivable;
            }

            return AxisPro::SendResponse($return_result, $format);


        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function addAutoBatchItems($format = "json")
    {

        try {

            $items = $_POST['items'];


            foreach ($items as $index => $myrow) {

                $_SESSION['Items']->line_items[$index] = new line_details(
                    $myrow["stock_id"], 1,
                    $myrow["srv_amt"], 0,
                    1, 0,
                    $myrow["description"], 0,
                    0,
                    0,
                    $myrow['tot'],
                    0,
                    0,
                    $myrow['transaction_id'],//TR ID
                    0,//DISC AMT
                    null,
                    $myrow["application_id"],
                    1,//govt_bank
                    null,
                    $myrow['transaction_id']

                );
            }

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * Genereate a download link to print/download service request link
     *
     * @param int $id
     * @return string
     */
    public function generateUrlForServiceRequestPrint(int $id)
    {
        return $GLOBALS['SysPrefs']->project_url . "/ERP/service_request/print.php?id=$id";
    }


    public function getCustomerByEID($format = "json")
    {


        $eid_number = $_GET['eid'];

        $sql = "select * from 0_debtors_master where eid=" . db_escape($eid_number);

        $get = db_query($sql);

        $return_result = db_fetch($get);


        return AxisPro::SendResponse($return_result, $format);

    }


    public function Send_Email($to, $subject, $content, $attachments = array())
    {

        try {
            require('../includes/class.phpmailer.php');
            $mail = new PHPMailer();
            $ret = '';
            $mail->IsSMTP();
            $mail->SMTPDebug = 0;
            $mail->SMTPAuth = TRUE;
            $mail->SMTPSecure = "ssl";
            $mail->Port = 465;
            $mail->Username = "ereceipt@dxbbms.ae";
            $mail->Password = "ereceipt1234**!!";
            $mail->Host = "mail.dxbbms.ae";
            $mail->Mailer = "smtp";
            $mail->SetFrom("ereceipt@dxbbms.ae", "AxisPro-ERP");
            $mail->AddReplyTo($to, "AxisPro-ERP");
            $mail->AddAddress($to);
            $mail->Subject = $subject;
            $mail->WordWrap = 80;
            $mail->MsgHTML($content);
            $mail->IsHTML(true);


            if (!empty($attachments)) {
                foreach ($attachments as $filename => $file) {
                    $mail->addAttachment($file, $filename);
                }
            }


            if (!$mail->Send()) {

                dd($mail->ErrorInfo);
                dd(1111);
                return false;
                $result = ['status' => 'FAIL', 'msg' => 'Mail sending failed'];
            } else {
                //dd($mail->ErrorInfo);


                return true;
                $result = ['status' => 'OK', 'msg' => 'Mail sending successfull'];
            }
            return true;

        } catch (phpmailerException $e) {
            echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
            echo $e->getMessage(); //Boring error messages from anything else!
        }

//        return $result;
    }


    /**
     * Delete Service requeset identified by the specified id if it is not invoiced already
     */
    public function del_srv_request($format = 'json')
    {

//        $canDeleteSrvRequest = user_check_access('SA_DEL_SRV_REQ');
//        $canDeleteAllSrvRequest = user_check_access('SA_DEL_SRV_REQ_ALL');

//        if (!$canDeleteSrvRequest && !$canDeleteAllSrvRequest) {
//            return AxisPro::SendResponse([
//                "status" => 'FAIL',
//                "code"   => 403,
//                "msg"    => 'The security settings on your account do not permit you to access this function'
//            ]);
//        }

        if (empty($_POST['id']) || !preg_match('/^[1-9][0-9]{1,15}$/', $_POST['id'])) {
            return AxisPro::SendResponse([
                "status" => 'FAIL',
                "code" => 422,
                "msg" => 'Service request ID is missing or invalid'
            ]);
        }

        $srvRequest = db_query("SELECT * FROM 0_service_requests WHERE id = {$_POST['id']}")->fetch_assoc();

        if (!$srvRequest) {
            return AxisPro::SendResponse([
                "status" => 'FAIL',
                "code" => 404,
                "msg" => 'Resource does not exist'
            ]);
        }

//        if (!$canDeleteAllSrvRequest && ($_SESSION['wa_current_user']->user != $srvRequest['created_by'])) {
//            return AxisPro::SendResponse([
//                "status" => 'FAIL',
//                "code"   => 403,
//                "msg"    => 'The security settings on your account do not permit you to access this function'
//            ]);
//        }

        /* $type = ST_SRV_REQ;
        $now = date(MYSQL_DATE_FORMAT);
        $memo = json_encode([
            "memo" => 'Delete',
            "data" => $srvRequest
        ]);
        $trans_date = DateTime::createFromFormat(MYSQL_DATE_TIME_FORMAT, $srvRequest['created_at'])->format(MYSQL_DATE_FORMAT);
        $backedUp = db_query(
            "INSERT INTO 0_voided (
                `type`,
                id,
                date_,
                memo_,
                trans_date,
                amount,
                customer,
                created_by,
                transaction_created_by
            )
            VALUES (
                $type,
                {$srvRequest['id']},
                '$now',
                '$memo',
                '$trans_date',
                0.00,
                {$srvRequest['customer_id']},
                {$_SESSION['wa_current_user']->user},
                {$srvRequest['created_by']}
            )"
        );
        if (!$backedUp) {
            return AxisPro::SendResponse([
                "status" => 'FAIL',
                "code"   => 500,
                "msg"    => 'Something went wrong! Please try again later'
            ]);
        }
        */

        $deleted = db_query("DELETE FROM 0_service_requests WHERE id = {$_POST['id']}");
        if (!$deleted) {
            return AxisPro::SendResponse([
                "status" => 'FAIL',
                "code" => 500,
                "msg" => 'Something went wrong! Please try again later'
            ]);
        } else {
            return AxisPro::SendResponse([
                "status" => 'Success',
                "code" => 204,
                "msg" => 'Resource deleted successfully'
            ]);
        }

    }

    public function getCashFlowReport($format = "json")
    {

        $start_date = date2sql($_POST['start_date']);
        $end_date = date2sql($_POST['end_date']);

        $sql = "SELECT bank.bank_account_name, bank.account_code, 

            IFNULL(ROUND(
                (SELECT SUM(amount) FROM 0_gl_trans WHERE `account`=bank.account_code AND tran_date < '$start_date')
            ,2),0.00) opening_balance,
            
            IFNULL(ROUND(SUM(CASE WHEN amount>=0 THEN amount ELSE 0 END),2),0.00) as debit_total, 
            IFNULL(ROUND(SUM(CASE WHEN amount<0 THEN amount ELSE 0 END),2),0.00) AS credit_total
            
             FROM 0_gl_trans gl RIGHT JOIN 0_bank_accounts bank ON bank.account_code=gl.account 
             
             WHERE gl.tran_date>=" . db_escape($start_date) . " AND gl.tran_date <= " . db_escape($end_date) . "
             
             GROUP BY bank.id";

//        dd($sql);

        $get = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch_assoc($get)) {
            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result, $format);


    }


    public function list_refunds($format = 'json')
    {
        $customer_id = $_GET['customer_id'];
        $inv_no = $_GET['inv_no'];

        $sql = "SELECT a.ref,abs(a.amount) as amount,a.trans_date,a.person_id,b.trans_no,d.name
                FROM 0_bank_trans AS a
                INNER JOIN 0_debtor_trans AS b ON a.trans_no=b.trans_no
                INNER JOIN 0_debtors_master AS d ON d.debtor_no=a.person_id
                WHERE a.refund_process='1' AND a.person_id='" . $customer_id . "' ";
        if ($inv_no != '') {
            $sql .= " AND b.reference='" . $inv_no . "'";
        }
        $sql .= " GROUP BY a.trans_no";

        $result = db_query($sql);

        $return_result = [];
        while ($myrow = db_fetch_assoc($result)) {

            /*  $qry="select display_customer,contact_person from 0_debtor_trans
                    where trans_no='".$myrow['trans_no']."' and `type`='10'";
              $customer_data=db_fetch(db_query($qry));*/


            $myrow['print_url'] = $this->generateUrlForRefundPrint($myrow['trans_no']);

            $return_result[] = $myrow;
        }

        return AxisPro::SendResponse($return_result, $format);


    }


    public function getActivityLog()
    {

        $user_id = $_SESSION['wa_current_user']->user;
        $user_info = get_user($user_id);


        $sql = "select log.* from 0_activity_log log WHERE 1=1 ";

        if (!empty($_POST['start_date'])) {
            $sql .= " AND DATE(log.created_at) >= " . db_escape(date2sql($_POST['start_date']));
        }

        if (!empty($_POST['end_date'])) {
            $sql .= " AND DATE(log.created_at) <= " . db_escape(date2sql($_POST['end_date']));
        }

        $sql .= " ORDER BY log.id DESC";

        $total_count_sql = "select count(*) as cnt from ($sql) as tmpTable";
        $total_count_exec = db_fetch_assoc(db_query($total_count_sql));
        $total_count = $total_count_exec['cnt'];

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = 50;
        $offset = ($page * $perPage) - $perPage;


        $sql = $sql . " LIMIT $perPage OFFSET $offset";

        $result = db_query($sql);
        $report = [];
        while ($myrow = db_fetch_assoc($result))
            $report[] = $myrow;


        return AxisPro::SendResponse(
            [
                'rep' => $report,
                'total_rows' => $total_count,
                'pagination_link' => AxisPro::paginate($total_count),
                'users' => $this->get_key_value_records('0_users', 'id', 'user_id'),
                'aggregates' => $total_count_exec,]
        );

    }


    public function saveEmployeeCustomDeductions($format = "json")
    {

        $insert_info = [];

        for ($i = 0; $i < sizeof($_POST['user_id']); $i++) {

            if (empty($_POST['deduct_amt'][$i]))
                $_POST['deduct_amt'][$i] = 0;

            $temp_array = [
                'date' => db_escape(date2sql($_POST['date_'])),
                'user_id' => $_POST['user_id'][$i],
                'amt' => $_POST['deduct_amt'][$i],
                'description' => db_escape($_POST['description'][$i]),
            ];

            array_push($insert_info, $temp_array);

        }

        $sql = "DELETE from 0_employee_custom_deductions where date=" . db_escape(date2sql($_POST['date_']));
        db_query($sql);

        db_insert_batch('0_employee_custom_deductions', $insert_info);


        return AxisPro::SendResponse([
            "status" => "Success",
            "msg" => "Employee deductions added"
        ], $format);


    }


    public function getEmployeeCustomDeductionSummary()
    {
        try {

            $date_from = $_POST['start_date'];
            $date_to = $_POST['end_date'];


            $sql = "SELECT ded.date date_, ded.user_id, ROUND(SUM(ded.amt),2) amt, 

            (SELECT ROUND(IFNULL(SUM(dt.user_commission),0),2) from 0_debtor_trans_details dt  
                WHERE dt.created_by = 1 
                AND dt.debtor_trans_type=10 
                AND DATE(dt.created_at) >= " . db_escape(date2sql($date_from)) . "
                AND DATE(dt.created_at) <= " . db_escape(date2sql($date_to)) . "
                AND dt.quantity <> 0 AND dt.created_by=usr.id) AS usr_comm, 
                    
            usr.user_id emp_user, usr.real_name AS emp_name 
            FROM 0_employee_custom_deductions ded
            LEFT JOIN 0_users usr ON usr.id=ded.user_id 
            
            WHERE 1=1 ";


            if (!empty($date_from))
                $sql .= " AND ded.date >= " . db_escape(date2sql($date_from));

            if (!empty($date_to))
                $sql .= " AND ded.date <= " . db_escape(date2sql($date_to));

            $sql .= " GROUP BY ded.user_id";

//            dd($sql);

            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch($result)) {

                $myrow['net'] = number_format2($myrow['usr_comm'] - $myrow["amt"], 2);

                $return_result[] = $myrow;
            }

            return AxisPro::SendResponse($return_result);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


    public function getEmployeeCustomDeductions()
    {
        try {

            $date = $_GET['date_'];

            $sql = "SELECT * FROM (SELECT ded.date, ded.user_id,ded.amt,ded.description, usr.user_id emp_user, 
usr.real_name AS emp_name FROM 0_employee_custom_deductions ded 
left JOIN
0_users usr ON usr.id=ded.user_id WHERE `date` = " . db_escape(date2sql($date)) . " 

UNION ALL

SELECT CURDATE() AS DATE, id, 0 AS amt, '' description, user_id ,real_name emp_name 
FROM 0_users) AS mmm 


GROUP BY user_id";

//            if(!empty($date_from))
//                $sql .= " AND ded.date_ = ".db_escape(date2sql($date));


            $result = db_query($sql);

            $return_result = [];
            while ($myrow = db_fetch($result)) {
                $return_result[] = $myrow;
            }

            return AxisPro::SendResponse($return_result);
        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }


}
