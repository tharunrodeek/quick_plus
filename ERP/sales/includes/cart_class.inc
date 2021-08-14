<?php
/**********************************************************************
 * Direct Axis Technology L.L.C.
 * Released under the terms of the GNU General Public License, GPL,
 * as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 ***********************************************************************/
/* Definition of the cart class
this class can hold all the information for:

i)   a sales order
ii)  an invoice
iii) a credit note
iv)  a delivery note
*/

include_once($path_to_root . "/inventory/includes/inventory_db.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");
include($path_to_root . '/BarcodeGenerator/BarcodeGenerator.php');
include($path_to_root . '/BarcodeGenerator/BarcodeGeneratorPNG.php');
include($path_to_root . '/BarcodeGenerator/BarcodeGeneratorSVG.php');
include($path_to_root . '/BarcodeGenerator/BarcodeGeneratorJPG.php');
include($path_to_root . '/BarcodeGenerator/BarcodeGeneratorHTML.php');


class Cart
{
    var $trans_type; // invoice, order, quotation, delivery note ...
    var $trans_no = array();// array (num1=>ver1,..) or 0 for new
    var $so_type = 0;        // for sales order: simple=0 template=1
    var $cart_id;        // used to detect multi-tab edition conflits
    var $line_items;  //array of objects of class line_details

    var $src_docs = array();    // array of arrays(num1=>ver1,...) or 0 for no src
    var $src_date;            // src document date (for info only)

    var $document_date;
    var $due_date;
    var $sales_type; // set to the customer's sales type
    var $sales_type_name; // set to customer's sales type name
    var $tax_included;

    var $customer_currency; // set to the customer's currency
    var $default_discount; // set to the customer's discount %
    var $customer_name;
    var $customer_id;
    var $Branch;
    var $email;

    var $deliver_to;
    var $delivery_address;
    var $phone;

    var $cust_ref;
    var $reference;
    var $Comments;
    var $Location;
    var $location_name;
    var $order_no;        // the original order number

    var $ship_via;
    var $freight_cost = 0;

    var $tax_group_id;
    var $tax_group_name;
    var $tax_group_array = null; // saves db queries
    var $price_factor;     // ditto for price calculations

    var $pos;            // user assigned POS
    var $cash_account;
    var $account_name;
    var $cash_discount;    // not used as of FA 2.1
    var $dimension_id;
    var $dimension2_id;
    var $payment;
    var $payment_terms; // cached payment terms
    var $credit;
    // prepayment mode:
    var $prepaid;        // true for documents issued in prepayment mode
    var $prep_amount;    // prepayment required for SO, invoiced amount for prepaiament invoice
    var $sum_paid;        // sum of all allocated prepayments both to order and related invoices
    var $alloc;        // sum of payments allocated to this document
    var $prepayments = array(); // allocation records for this document
    var $ex_rate;

    var $fixed_asset = false;


    //Added For AMER
    var $display_customer;
    var $customer_trn;
    var $customer_email;
    var $customer_mobile;
    var $customer_ref;
    var $barcode;
    var $edit_invoice;
    var $sales_order_no;


    //-------------------------------------------------------------------------
    //
    //  $trans_no==0 => open new/direct document
    //  $trans_no!=0 && $prepare_child==false => update with parent constarints for reedition
    //  $trans_no!=0 && $prepare_child==true => prepare for child doc entry
    //	$prepare_child is set to ST_SALESINVOICE for prepayment invoices
    //
    function __construct($type, $trans_no = 0, $prepare_child = false)
    {
        /*Constructor function initialises a new shopping cart */
        $this->line_items = array();
        $this->sales_type = "";
        if ($type == ST_SALESQUOTE)
            $this->trans_type = $type;
        else
            $this->trans_type = ST_SALESORDER;
        $this->dimension_id = 0;
        $this->dimension2_id = 0;
        $this->pos = get_sales_point(user_pos());
        $this->read($type, $trans_no, $prepare_child);
        $this->cart_id = uniqid('');

        $this->barcode = $this->randomNumber();
    }


    /** Random number for barcode */
    function randomNumber($length = 12)
    {
        $randomNumber = '';

        for ($i = 0; $i < $length; $i++) {
            $randomNumber .= mt_rand(0, 9);
        }

        $sql = "SELECT * FROM " . TB_PREF . "debtor_trans WHERE barcode = " . $randomNumber;
        $result = db_query($sql, "can't retrieve child trans");

        if (db_num_rows($result) > 1) {
            $this->randomNumber(12);
        } else {
            return $randomNumber;
        }
    }


    /*
        Optional sorting items by stock_id.
    */
    function _cmp_lines($a, $b)
    {
        return strcmp($a->stock_id, $b->stock_id);
    }

    /*
        Returns items array optionally sorted by item code.
    */
    function get_items()
    {
        global $SysPrefs;

        $items = $this->line_items;
        if (@$SysPrefs->sort_sales_items)
            uasort($items, array($this, '_cmp_lines'));

        return $items;
    }
    //
    //	Prepare cart to new child document entry, just after initial parent doc read.
    //
    function prepare_child($type)
    {
        global $Refs;

        if ($type === true)
            $type = get_child_type($this->trans_type);

        $this->trans_type = $type;
        $this->reference = $Refs->get_next($this->trans_type, null, array('date' => $this->document_date,
            'customer' => $this->customer_id, 'branch' => $this->Branch));
        if ($type == ST_CUSTCREDIT)
            $this->src_date = $this->document_date;

        $this->document_date = new_doc_date();

        for ($line_no = 0; $line_no < count($this->line_items); $line_no++) {
            $line = &$this->line_items[$line_no];
            $line->src_id = $line->id; // save src line ids for update
            $line->qty_dispatched = $type == ST_CUSTCREDIT ? '0' :
                (($this->prepaid && $type == ST_SALESINVOICE) ? $line->quantity : $line->quantity - $line->qty_done);
            $line->qty_old = 0;
        }
        unset($line);

        if ($type == ST_CUSTDELIVERY) {
            $this->order_no = key($this->trans_no);
            $cust = get_customer($this->customer_id);
            $this->dimension_id = $cust['dimension_id'];
            $this->dimension2_id = $cust['dimension2_id'];
        }
        if ($type == ST_SALESINVOICE) {
            $this->due_date = get_invoice_duedate($this->payment, $this->document_date);
        }

        $this->src_docs = $this->trans_no;
        $this->trans_no = 0;
    }

    //
    //	Prepares transaction for reedition updating with parent transaction data
    //
    function set_parent_constraints($sodata, $src_no)
    {

        $srcdetails = get_sales_parent_lines($this->trans_type, $src_no);
        $src_type = get_parent_type($this->trans_type);

        // calculate & save: qtys on other docs and free qtys on src doc
        $line_no = 0;
        $src_docs = array();
        // Loop speed optimisation below depends on fact
        // that child line_items contains subset of parent lines in _the_same_ order !
        while (($line_no < count($this->line_items)) && ($srcline = db_fetch($srcdetails))) {
            $line = &$this->line_items[$line_no];
            $src_docs[] = $src_type == ST_SALESORDER ? $srcline['order_no'] : $srcline['debtor_trans_no'];
            while ($srcline['id'] != $line->src_id) // Logic : This will increment the line_items array till sales_order line is matched.
            {    // Fixes Delivery note bug : Parent constraints not working if sales order line deleted after delivery
                $line_no++;
                $line = &$this->line_items[$line_no];
            }
            if ($srcline['id'] == $line->src_id) {
                if ($this->trans_type == ST_SALESINVOICE)
                    $line->src_no = $srcline['debtor_trans_no'];
                $line->qty_old = $line->qty_dispatched = $line->quantity;
                $line->quantity += $srcline['quantity'] -
                    ($src_type == ST_SALESORDER ? $srcline['qty_sent'] : $srcline['qty_done']); // add free qty on src doc
                $line_no++;
            }
        }

        if ($src_type == ST_SALESORDER || $src_type == 0) {
            $this->src_docs = array($sodata['order_no'] => $sodata['version']);
        } else {
            // get src_data from debtor_trans
            if (!empty($src_docs))
                $this->src_docs = get_customer_trans_version($src_type, array_unique($src_docs));
        }
    }
    //-------------------------------------------------------------------------
    // Reading document into cart
    //
    function read($type, $trans_no = 0, $prepare_child = false)
    {
        global $SysPrefs, $Refs;

        if (!is_array($trans_no)) $trans_no = array($trans_no);

        if ($trans_no[0]) { // read old transaction


            if ($type == ST_SALESORDER || $type == ST_SALESQUOTE) { // sales order || sales quotation
                read_sales_order($trans_no[0], $this, $type);

            } else {

                // other type of sales transaction

                read_sales_trans($type, $trans_no, $this);


                $this->prepayments = get_payments_for($trans_no[0], $type, $this->customer_id);
                $this->update_payments();
                if ($this->order_no) { // free hand credit notes have no order_no
                    $sodata = get_sales_order_header($this->order_no, ST_SALESORDER);
                    $this->cust_ref = $sodata["customer_ref"];
                    // currently currency is hard linked to debtor account
                    $this->delivery_to = $sodata["deliver_to"];
                    $this->delivery_address = $sodata["delivery_address"];
                    // child transaction reedition - update with parent info unless it is freehand
                    if (!$this->is_prepaid() && !$prepare_child) // this is read for view/reedition
                        $this->set_parent_constraints($sodata, $trans_no[0]);
                }
            }
            // convert document into child and prepare qtys for entry
            if ($prepare_child)
                $this->prepare_child($prepare_child);

        } else { // new document
            $this->trans_type = $type;
            $this->trans_no = 0;
            $this->customer_currency = get_company_currency();
            // set new sales document defaults here
            if (get_global_customer() != ALL_TEXT)
                $this->customer_id = get_global_customer();
            else
                $this->customer_id = '';
            $this->document_date = new_doc_date();
            if (!is_date_in_fiscalyear($this->document_date))
                $this->document_date = end_fiscalyear();
            $this->reference = $Refs->get_next($this->trans_type, null, array('date' => Today(),
                'customer' => $this->customer_id));



//            //YBC Reference for each dimension
//            $sql = 'SELECT MAX(reference) as max_ref from 0_debtor_trans where type=10
//                and dimension_id=0 and ov_amount <> 0 ORDER BY reference DESC LIMIT 1';
//            $result = db_fetch(db_query($sql));
//            $max_ref = $result['max_ref'];
//            $max_ref_count = strlen($max_ref);
//            $new_ref_int = intval($max_ref)+1;
//            $ref = str_pad($new_ref_int, $max_ref_count, '0', STR_PAD_LEFT);
//            $this->reference = $ref;

//            pp(get_post('dimension_id'));




            if ($type != ST_SALESORDER && $type != ST_SALESQUOTE) // Added 2.1 Joe Hunt 2008-11-12
            {
                $dim = get_company_pref('use_dimension');
                if ($dim > 0) {
                    if ($this->customer_id == '')
                        $this->dimension_id = 0;
                    else {
                        $cust = get_customer($this->customer_id);
                        $this->dimension_id = $cust['dimension_id'];
                    }
                    if ($dim > 1) {
                        if ($this->customer_id == '')
                            $this->dimension2_id = 0;
                        else
                            $this->dimension2_id = $cust['dimension2_id'];
                    }
                }
            }
            if ($type == ST_SALESINVOICE) {
                $this->due_date =
                    get_invoice_duedate($this->payment, $this->document_date);
            } else
                $this->due_date =
                    add_days($this->document_date, $SysPrefs->default_delivery_required_by());
        }
        $this->credit = get_current_cust_credit($this->customer_id);
    }

    //-------------------------------------------------------------------------
    // Writing new/modified sales document to database.
    // Makes parent documents for direct delivery/invoice by recurent call.
    // $policy - 0 or 1:  writeoff/return for IV, back order/cancel for DN
    function write($policy = 0)
    {


        begin_transaction(); // prevents partial database changes in case of direct delivery/invoice
        if ($this->reference != 'auto' && $this->trans_no == 0
//            && !is_new_reference($this->reference, $this->trans_type)
            && !$this->edit_invoice) {
//            commit_transaction();
//            return -1;//FOR YBC


//            if($this->trans_type==10 &&
//                duplicate_invoice_number($this->reference, $this->trans_type)){

//                return -1;//FOR YBC
//            }

        }




        if (count($this->src_docs) == 0 && ($this->trans_type == ST_SALESINVOICE || $this->trans_type == ST_CUSTDELIVERY) && !$this->is_prepaid()) {
            // this is direct document - first add parent
            $ref = $this->reference;
            $date = $this->document_date;
            $due_date = $this->due_date;
            $dimension_id = $this->dimension_id;
            $dimension2_id = $this->dimension2_id;
            $this->trans_type = get_parent_type($this->trans_type);

            $this->reference = 'auto';


            $trans_no = $this->write(1);

            // re-read parent document converting it to child
            $this->read($this->trans_type, $trans_no, true);

            $this->document_date = $date;
            $this->reference = $ref;
            $this->due_date = $due_date;
            $this->dimension_id = $dimension_id;
            $this->dimension2_id = $dimension2_id;
        }


        $this->reference = @html_entity_decode($this->reference, ENT_QUOTES);
        $this->Comments = @html_entity_decode($this->Comments, ENT_QUOTES);
        foreach ($this->line_items as $lineno => $line) {
            $this->line_items[$lineno]->stock_id = @html_entity_decode($line->stock_id, ENT_QUOTES);
            $this->line_items[$lineno]->item_description = @html_entity_decode($line->item_description, ENT_QUOTES);

        }


        switch ($this->trans_type) {
            case ST_SALESINVOICE:


//                display_error($_POST['payment_flag']); die;

                if (isset($this->edit_invoice) && $this->edit_invoice == true) {
//                    delete_sales_order($this->sales_order_no, ST_SALESORDER);
//                    delete_invoice($this->sales_order_no);
                    $ret = write_sales_invoice($this);
                } else {


                    if(tasheel_invoice($_POST['payment_flag']) || tadbeer_invoice($_POST['payment_flag'])) {
                        $ret = write_sales_invoice_tasheel($this);
                    }

//                    if ($_POST['payment_flag'] == 4 || $_POST['payment_flag'] == 5) {
//                        $ret = write_sales_invoice_tasheel($this);
//                    } else if ($_POST['payment_flag'] != 0 && $_POST['payment_flag'] != 3) {
//                        $ret = write_sales_invoice_tasheel($this);
//                    }

                    else {

                        $ret = write_sales_invoice($this);
                    }


                }

                break;
            case ST_CUSTCREDIT:
                $ret = write_credit_note($this, $policy);
                break;
            case ST_CUSTDELIVERY:
                $ret = write_sales_delivery($this, $policy);
                break;
            case ST_SALESORDER:
            case ST_SALESQUOTE:
                if ($this->trans_no == 0)    // new document
                    $ret = add_sales_order($this);
                else {
                    $ret = update_sales_order($this);
                }
        }

        commit_transaction();

        return $ret;
    }


    //Added for AMER
    function set_invoice_display_details($display_customer, $customer_trn, $customer_mobile, $customer_email, $customer_ref)
    {

        $this->display_customer = $display_customer;
        $this->customer_trn = $customer_trn;
        $this->customer_mobile = $customer_mobile;
        $this->customer_email = $customer_email;
        $this->customer_ref = $customer_ref;

    }


    //FILL INFO BY TOKEN
    function get_customer_by_token($token)
    {
        $sql = "SELECT * FROM 0_axis_front_desk WHERE DATE(created_at)='" . date2sql(Today()) . "' AND
        token=" . db_escape($token) . " ORDER BY id DESC LIMIT 1";
        $result = db_query($sql);
        $row = db_fetch($result);
        if (db_num_rows($result) > 0)
            return $row;
//        else {
//            $sql = "SELECT debtor_no customer_id,name display_customer,tax_id customer_trn,
//            debtor_email as customer_email,mobile as customer_mobile,contact_person
//            FROM 0_debtors_master where mobile=" . db_escape($token) . " LIMIT 1";
//            $result = db_query($sql);
//            $row = db_fetch($result);
//            if (db_num_rows($result) > 0)
//                return $row;
//        }


//        $sql = "SELECT debtor_no customer_id,display_customer,customer_trn,
//             customer_email,customer_mobile,contact_person
//            FROM 0_debtor_trans where customer_mobile=" . db_escape($token) . " and debtor_no=1
//              AND display_customer <> 'Walk-in Customer' LIMIT 1";


        $sql = "SELECT debtor_no customer_id,name display_customer,tax_id customer_trn,
            debtor_email as customer_email,mobile as customer_mobile,contact_person  
            FROM 0_debtors_master where customer_type='COMPANY' and mobile=" . db_escape($token) . " LIMIT 1";

//        pp($sql);

        $result = db_query($sql);
        $row = db_fetch($result);
        if (db_num_rows($result) > 0)
            return $row;


//        $result = db_query($sql);
//        $row = db_fetch($result);
//        if (db_num_rows($result) > 0)
//            return $row;


        return false;
    }

    //FILL INFO BY TOKEN
    function set_token_data()
    {

//        pp('111111');

        if (isset($_POST['token_no']) && !empty($_POST['token_no'])) {
            $row = $this->get_customer_by_token($_POST['token_no']);
            if ($row) {
                $this->customer_id = $row['customer_id'];
                if (!empty($row['display_customer']))
                    $this->customer_name = $row['display_customer'];
                if (!empty($row['customer_trn']))
                    $this->tax_id = $row['customer_trn'];
                if (!empty($row['customer_mobile']))
                    $this->phone = $row['customer_mobile'];
                if (!empty($row['customer_email']))
                    $this->email = $row['customer_email'];
                if (!empty($row['customer_ref']))
                    $this->customer_ref = $row['customer_ref'];
                if (!empty($row['display_customer']))
                    $this->contact_person = $row['display_customer'];

            }

        }
    }


    function set_info_from_service_request($order)
    {

        $this->customer_id = $order->customer_id;
        $this->customer_name = $order->customer_name;
        $this->tax_id = $order->tax_id;
        $this->customer_mobile = $order->phone;
        $this->customer_email = $order->email;
        $this->customer_ref = $order->customer_ref;

    }

    function set_customer($customer_id, $customer_name, $currency, $discount, $payment, $cdiscount = 0)
    {
        $this->customer_name = $customer_name;

        if($customer_id == 1 && !$this->service_req_id) {
            $this->customer_name = "";
            $this->contact_person = "";
        }

        $this->customer_id = $customer_id;
        $this->default_discount = $discount;
        $this->customer_currency = $currency;
        $this->payment = $payment;
        $this->payment_terms = get_payment_terms($payment);
        $this->cash_discount = $cdiscount;

        if ($this->payment_terms['cash_sale']) {
            $this->Location = $this->pos['pos_location'];
            $this->location_name = $this->pos['location_name'];
        }
        $this->credit = get_current_cust_credit($customer_id);
    }

    function set_branch($branch_id, $tax_group_id, $tax_group_name, $phone = '', $email = '')
    {
        $this->Branch = $branch_id;
        $this->phone = $phone;
        $this->email = $email;
        $this->tax_group_id = $tax_group_id;
        $this->tax_group_array = get_tax_group_items_as_array($tax_group_id);
    }

    function set_sales_type($sales_type, $sales_name, $tax_included = 0, $factor = 0)
    {
        $this->sales_type = $sales_type;
        $this->sales_type_name = $sales_name;
        $this->tax_included = $tax_included;
        $this->price_factor = $factor;
    }

    function set_location($id, $name)
    {
        $this->Location = $id;
        $this->location_name = $name;
    }

    function set_delivery($shipper, $destination, $address, $freight_cost = null)
    {
        $this->ship_via = $shipper;
        $this->deliver_to = $destination;
        $this->delivery_address = $address;
        if (isset($freight_cost))
            $this->freight_cost = $freight_cost;
    }

    function add_to_cart($line_no, $stock_id, $qty, $price, $disc, $qty_done = 0, $standard_cost = 0, $description = null, $id = 0, $src_no = 0,
                         $src_id = 0, $govt_fee = 0, $bank_service_charge = 0, $bank_service_charge_vat = 0,
                         $transaction_id = null, $discount_amount = null, $other = null, $application_id = null,
                         $govt_bank_account = null, $ref_name = null, $ed_transaction_id = null)
    {


        $line = new line_details($stock_id, $qty, $price, $disc,
            $qty_done, $standard_cost, $description, $id, $src_no, $src_id, $govt_fee, $bank_service_charge, $bank_service_charge_vat,
            $transaction_id, $discount_amount, $other, $application_id, $govt_bank_account, $ref_name, $ed_transaction_id);

        if ($line->valid) {
            $this->line_items[$line_no] = $line;

            return 1;
        } else
            display_error(trans("You have to enter valid stock code or nonempty description"));
        return 0;
    }

    function update_cart_item($line_no, $qty, $price, $disc, $description = "", $govt_fee = 0,
                              $bank_charge = 0, $bank_charge_vat = 0, $discount_amount, $transaction_id = "", $other = "",
                              $application_id = "", $govt_bank_account = "", $ref_name = "", $ed_transaction_id = "")
    {

        if ($description != "")
            $this->line_items[$line_no]->item_description = $description;
        $this->line_items[$line_no]->quantity = $qty;
        $this->line_items[$line_no]->qty_dispatched = $qty;
        $this->line_items[$line_no]->price = $price;
        $this->line_items[$line_no]->discount_percent = $disc;

        //Modifications for AMER
        $this->line_items[$line_no]->govt_fee = $govt_fee;
        $this->line_items[$line_no]->bank_service_charge = $bank_charge;
        $this->line_items[$line_no]->bank_service_charge_vat = $bank_charge_vat;
        $this->line_items[$line_no]->discount_amount = $discount_amount;

        if ($transaction_id != "")
            $this->line_items[$line_no]->transaction_id = $transaction_id;

        if ($application_id != "")
            $this->line_items[$line_no]->application_id = $application_id;

        if ($ref_name != "")
            $this->line_items[$line_no]->ref_name = $ref_name;

        if ($ed_transaction_id != "")
            $this->line_items[$line_no]->ed_transaction_id = $ed_transaction_id;

        if ($govt_bank_account != "")
            $this->line_items[$line_no]->govt_bank_account = $govt_bank_account;
    }

    function update_add_cart_item_qty($line_no, $qty)
    {
        $this->line_items[$line_no]->quantity += $qty;
    }

    function remove_from_cart($line_no)
    {
        array_splice($this->line_items, $line_no, 1);
    }

    function clear_items()
    {
        unset($this->line_items);
        $this->line_items = array();
        $this->sales_type = "";
        $this->trans_no = 0;
        $this->customer_id = $this->order_no = 0;
    }

    function count_items()
    {
        $counter = 0;
        foreach ($this->line_items as $line) {
            if ($line->quantity != $line->qty_done) $counter++;
        }
        //$counter = 1;
        return $counter;
    }

    function get_items_total()
    {
        $total = 0;

        foreach ($this->line_items as $ln_itm) {
            $price = $ln_itm->line_price();
            $total += round($ln_itm->quantity * $price * (1 - $ln_itm->discount_percent),
                user_price_dec());
        }
        return $total;
    }


    //Added for AMER
    function get_amer_items_total()
    {
        $total = 0;

        foreach ($this->line_items as $ln_itm) {
            $price = $ln_itm->line_price();
            $total += round($ln_itm->quantity * $price * (1 - $ln_itm->discount_percent) +
                ($ln_itm->govt_fee + $ln_itm->bank_service_charge + $ln_itm->bank_service_charge_vat),
                user_price_dec());
        }
        return $total;
    }


    //Modified for AMER
    function get_items_total_dispatch()
    {
        global $SysPrefs;
        $total = 0;
        $service_charge_total = 0;

        foreach ($this->line_items as $ln_itm) {


            if ((!empty($ln_itm->transaction_id) || !$SysPrefs->prefs['gl_after_transaction_id_update']) ||
                empty(($ln_itm->govt_fee + $ln_itm->bank_service_charge +
                        $ln_itm->bank_service_charge_vat) * $ln_itm->qty_dispatched)) {

                $price = $ln_itm->line_price();

//                if(!isset($this->payment_card) || empty($this->payment_card) || $this->payment_card==1200) {
//                    $ln_itm->discount_percent = 0;
//                    $ln_itm->discount_amount = 0;
//                }

                $pf_amount = $ln_itm->pf_amount ? $ln_itm->pf_amount : 0;

//            $total += round(($ln_itm->qty_dispatched * $price * (1 - $ln_itm->discount_percent))
//                + (($ln_itm->govt_fee + $ln_itm->bank_service_charge + $ln_itm->bank_service_charge_vat) * $ln_itm->qty_dispatched),
//                user_price_dec());

                $total += round(($ln_itm->qty_dispatched * $price)
                    - ($ln_itm->discount_amount * $ln_itm->qty_dispatched)
                    + (($ln_itm->govt_fee + $ln_itm->bank_service_charge + $ln_itm->bank_service_charge_vat) * $ln_itm->qty_dispatched),
                    user_price_dec());

                $service_charge_total += ($ln_itm->qty_dispatched * $price);

            }

        }

        if(isset($this->is_tadbeer) && ($this->is_tadbeer == 1)) {
            $total = $total - $service_charge_total;
        }



        return $total;
    }


    //FOR ARABIAN
    function get_items_trans_total()
    {
        $total = 0;
        foreach ($this->line_items as $ln_itm) {
            $price = $ln_itm->line_price();


            if (isset($_SESSION['Items']->is_tadbeer) && $_SESSION['Items']->is_tadbeer==1) {

                $total += round((($ln_itm->govt_fee + $ln_itm->bank_service_charge + $ln_itm->bank_service_charge_vat) *
                    $ln_itm->qty_dispatched),
                    user_price_dec());

            }
            else {
                $total += round(($ln_itm->qty_dispatched * $price)
                    - ($ln_itm->discount_amount * $ln_itm->qty_dispatched)
                    + (($ln_itm->govt_fee + $ln_itm->bank_service_charge + $ln_itm->bank_service_charge_vat) *
                        $ln_itm->qty_dispatched),
                    user_price_dec());
            }


        }
        return $total;
    }


    function has_items_dispatch()
    {
        foreach ($this->line_items as $ln_itm) {
            if ($ln_itm->qty_dispatched > 0)
                return true;
        }
        return false;
    }

    function any_already_delivered()
    {
        /* Checks if there have been any line item processed */
        foreach ($this->line_items as $stock_item) {
            if ($stock_item->qty_done != 0) {
                return 1;
            }
        }
        return 0;
    }

    function some_already_delivered($line_no)
    {
        /* Checks if there have been deliveries of a specific line item */
        if (isset($this->line_items[$line_no]) &&
            $this->line_items[$line_no]->qty_done != 0) {
            return 1;
        }
        return 0;
    }

    function get_taxes($shipping_cost = null)
    {
        global $SysPrefs;
        $items = array();
        $prices = array();
        if ($shipping_cost == null)
            $shipping_cost = $this->freight_cost;

//		foreach ($this->line_items as $ln_itm) {
//			$items[] = $ln_itm->stock_id;
//			$prices[] = round(((
//				$this->trans_type==ST_SALESORDER ? 	$ln_itm->quantity : $ln_itm->qty_dispatched) *
//				$ln_itm->line_price()* (1 - $ln_itm->discount_percent)),  user_price_dec());
//		}


        //Modified for AMER
        foreach ($this->line_items as $ln_itm) {


            if ((!empty($ln_itm->transaction_id) || !$SysPrefs->prefs['gl_after_transaction_id_update']) ||
                empty(($ln_itm->govt_fee + $ln_itm->bank_service_charge +
                        $ln_itm->bank_service_charge_vat) * $ln_itm->qty_dispatched)) {

//                $items[] = $ln_itm->stock_id;
//                $prices[] = round(((
//                    $this->trans_type == ST_SALESORDER ? $ln_itm->quantity : $ln_itm->qty_dispatched) *
//                    $ln_itm->line_price()), user_price_dec());

                if(empty($ln_itm->line_price()))
                    $ln_itm->discount_amount = 0;

                $items[] = $ln_itm->stock_id;
                $prices[] = round(((
                        $this->trans_type == ST_SALESORDER ? $ln_itm->quantity : $ln_itm->qty_dispatched) *
                        $ln_itm->line_price()) -
                    ($ln_itm->discount_amount*$ln_itm->qty_dispatched),
                    user_price_dec());

            }
        }

        $taxes = get_tax_for_items($items, $prices, $shipping_cost,
            $this->tax_group_id, $this->tax_included, $this->tax_group_array);

        // Adjustment for swiss franken, we always have 5 rappen = 1/20 franken
        if ($this->customer_currency == 'CHF') {
            $val = $taxes['1']['Value'];
            $val1 = (floatval((intval(round(($val * 20), 0))) / 20));
            $taxes['1']['Value'] = $val1;
        }
        return $taxes;
    }


    function get_tax_free_shipping()
    {

        if ($this->tax_included == 0)
            return $this->freight_cost;
        else
            return ($this->freight_cost - $this->get_shipping_tax());
    }

    function get_shipping_tax()
    {

        $tax_items = get_shipping_tax_as_array($this->tax_group_id);
        $tax_rate = 0;
        if ($tax_items != null) {
            foreach ($tax_items as $item_tax) {
                $index = $item_tax['tax_type_id'];
                if (isset($this->tax_group_array[$index]['rate'])) {
                    $tax_rate += $item_tax['rate'];
                }
            }
        }
        if ($this->tax_included)
            return round($this->freight_cost * $tax_rate / ($tax_rate + 100), user_price_dec());
        else
            return round($this->freight_cost * $tax_rate / 100, user_price_dec());
    }

    /*
        Returns transaction value including all taxes
    */
    function get_trans_total()
    {

        $total = $this->get_items_total() + $this->freight_cost;
        $dec = user_price_dec();
        if (!$this->tax_included) {
            $total += $this->get_shipping_tax();
            $taxes = $this->get_taxes();
            foreach ($taxes as $tax)
                $total += round($tax['Value'], $dec);
        }

        return $total;
    }

    function get_amer_trans_total()
    {
        $total = $this->get_amer_items_total() + $this->freight_cost;
        $dec = user_price_dec();
        if (!$this->tax_included) {
            $total += $this->get_shipping_tax();
            $taxes = $this->get_taxes();
            foreach ($taxes as $tax)
                $total += round($tax['Value'], $dec);
        }

        return $total;
    }

    /*
        Checks cart quantities on document_date.
        Returns array of stock_ids which stock quantities would go negative on some day.
    */
    function check_qoh($date = null, $location = null)
    {
        $low_stock = array();
        // check only for customer delivery and direct sales invoice
        if (!($this->trans_type == ST_CUSTDELIVERY || ($this->trans_type == ST_SALESINVOICE && $this->trans_no == 0)))
            return $low_stock;

        // collect quantities by stock_id
        $qtys = array();
        foreach ($this->line_items as $line_no => $line_item) {
            if (has_stock_holding($line_item->mb_flag)) {
                if (!$this->trans_no) // new delivery
                    $qtys[$line_item->stock_id]['qty'] = $line_item->qty_dispatched + @$qtys[$line_item->stock_id]['qty'];
                else    // DN modification: check change in quantity
                    $qtys[$line_item->stock_id]['qty'] = ($line_item->qty_dispatched - $line_item->qty_old) + @$qtys[$line_item->stock_id]['qty'];
                $qtys[$line_item->stock_id]['line'] = $line_no;
            }
        }

        foreach ($qtys as $stock_id => $sum) {
            if (check_negative_stock($stock_id, -$sum['qty'], $location ? $location : $this->Location, $date ? $date : $this->document_date))
                $low_stock[] = $stock_id;
        }

        return $low_stock;
    }

    /*
        Returns true for documents issued in prepayment cycle.
    */
    function is_prepaid()
    {
        return $this->prepaid;
    }

    /*
        Order is ready for delivery in prepament mode.
    */
    function is_released()
    {
        return floatcmp($this->sum_paid, $this->prep_amount) >= 0;
    }

    /*
        Check whether order has been already invoiced/send or not.
    */
    function is_started()
    {
        if ($this->trans_no == 0)
            return false;
        $order_no = array_keys($this->trans_no);

        return is_sales_order_started(reset($order_no));
    }

    /*
        Check payment terms and prepayments selected for this invoice,
        and updates	partial/final invoice value respectively.
    */
    function update_payments()
    {
        $remainder = prepaid_invoice_remainder($this->order_no);

        // recalculate prepaid part from payments
        if ($this->payment_terms['days_before_due'] == -1) {    // this is partial invoice for selected prepayments made.
            $paid = 0;
            foreach ($this->prepayments as $payment)
                $paid += $payment['amt'];
            $this->prep_amount = $this->trans_no ? $paid : min($remainder, $paid);
        } else    // this is final invoice
            $this->prep_amount = $remainder;
    }
} /* end of class defintion */

class line_details
{
    var $id;
    var $stock_id;
    var $item_description;
    var $units;
    var $mb_flag;
    var $tax_type;
    var $tax_type_name;
    var $src_no;    // number of src doc for this line
    var $src_id;
    var $price;
    var $discount_percent;


    var $standard_cost;
    var $descr_editable;

    var $valid; // validation in constructor
    /*
        Line quantity properties in various cart create modes:

        view:
        $quantity - quantity on current document
        $qty_done - quantity processed on all child documents
        $qty_dispatched - not used
        $qty_old - not used

        edit:
        $quantity - free parent quantity including this doc (= max allowed quantity)
        $qty_done - qty processed on child documents (= min allowed qty)
        $qty_dispatched - quantity currently selected to process
        $qty_old - quantity processed on this document before reedition

        new child entry (view parent followed by prepare_child() call):
        $quantity - max allowed quantity (get from parent)
        $qty_done - qty processed on other child documents
        $qty_dispatched - quantity currently selected to process
        $qty_old - 0; not used
    */
    var $quantity;
    var $qty_done;
    var $qty_dispatched;
    var $qty_old = 0;


    //Additional changes

    var $pf_amount;
    var $govt_fee;
    var $govt_bank_account;
    var $bank_service_charge;
    var $bank_service_charge_vat;
    var $commission_loc_user;
    var $commission_non_loc_user;
    var $transaction_id;
    var $application_id;
    var $ref_name;
    var $ed_transaction_id;
    var $discount_amount;


    function __construct($stock_id, $qty, $prc, $disc_percent,
                         $qty_done, $standard_cost, $description, $id = 0, $src_no = 0,
                         $src_id = 0, $govt_fee = 0, $bank_service_charge = 0, $bank_service_charge_vat = 0,
                         $transaction_id = null, $discount_amount = 0, $other = null, $application_id = null,
                         $govt_bank_account = null, $ref_name = null, $ed_transaction_id = null)
    {
        /* Constructor function to add a new LineDetail object with passed params */

        $this->id = $id;
        $this->src_no = $src_no;
        $this->src_id = $src_id;
        $item_row = get_item($stock_id);

        if (!$item_row)
            return;

        // Changes for AMER

        $this->govt_fee = $govt_fee;
        $this->bank_service_charge = $bank_service_charge;
        $this->bank_service_charge_vat = $bank_service_charge_vat;
        $this->transaction_id = $transaction_id;
        $this->application_id = $application_id;
        $this->ref_name = $ref_name;
        $this->ed_transaction_id = $ed_transaction_id;
        $this->discount_amount = $discount_amount ? $discount_amount : 0;

//		$this->govt_fee = $item_row['govt_fee'];
//        $this->govt_bank_account = $govt_bank_account;

        $this->govt_bank_account = !empty($govt_bank_account) ? $govt_bank_account : 1200;


//		$this->bank_service_charge = $item_row['bank_service_charge'];
//		$this->bank_service_charge_vat = $item_row['bank_service_charge_vat'];
        $this->commission_loc_user = $item_row['commission_loc_user'];
        $this->commission_non_loc_user = $item_row['commission_non_loc_user'];
        $this->pf_amount = $item_row['pf_amount'] ? $item_row['pf_amount'] : 0;


        //20-10-2018
//        if(isset($item_row['use_own_govt_bank_account']) &&
//            !empty($item_row['use_own_govt_bank_account']) && ($item_row['use_own_govt_bank_account'] == 1)) {
//            $this->govt_bank_account = $_POST['govt_credit_account'];
//        }

        $this->mb_flag = $item_row["mb_flag"];
        $this->units = $item_row["units"];
        $this->descr_editable = $item_row["editable"];
        if ($description == null || !$this->descr_editable)
            $this->item_description = $item_row['long_description'] ?
                $item_row["description"] . " - " . $item_row['long_description'] : $item_row['description'];
        else
            $this->item_description = $description;
        $this->tax_type = $item_row["tax_type_id"];
        $this->tax_type_name = $item_row["tax_type_name"];
        $this->stock_id = $stock_id;
        $this->quantity = $qty;
        $this->qty_dispatched = $qty;
        $this->price = $prc;
        $this->discount_percent = $disc_percent;
        $this->qty_done = $qty_done;
        $this->standard_cost = $standard_cost;
        $this->valid = true;
    }

    // get unit price as stated on document
    function line_price()
    {
        return $this->price;
    }
}

