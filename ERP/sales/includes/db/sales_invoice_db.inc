<?php
/**********************************************************************
 * Copyright (C) FrontAccounting, LLC.
 * Released under the terms of the GNU General Public License, GPL,
 * as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 ***********************************************************************/
//-----------------------------------------------------------------------------
//	Add or update Sales Invoice
//
function write_sales_invoice(&$invoice)
{


    global $Refs, $SysPrefs;


    $trans_no = $invoice->trans_no;
    if (is_array($trans_no))
        $trans_no = key($trans_no);

    $date_ = $invoice->document_date;
    $charge_shipping = $invoice->freight_cost;

//    empty($SysPrefs->prefs['dflt_csh_pmt_act'])
//        ? $_SESSION['wa_current_user']->cashier_account
//        : $SysPrefs->prefs['dflt_csh_pmt_act']
//
//
//
//

//    dd($_SESSION['wa_current_user']->cashier_account);
    $payment_accounts = [
        'PayNow' => !empty($_SESSION['wa_current_user']->cashier_account)
            ? $_SESSION['wa_current_user']->cashier_account :
            $SysPrefs->prefs['dflt_csh_pmt_act'],
        'PayNoWCC' => $SysPrefs->prefs['dflt_credit_card_pmt_act'],
        'PayByBankTransfer' => $SysPrefs->prefs['dflt_bnk_trnsfr_pmt_act'],
        'PayByCustomerCard' => $SysPrefs->prefs['customer_card_act']
    ];


    begin_transaction();

    hook_db_prewrite($invoice, ST_SALESINVOICE);
    $company_data = get_company_prefs();

    $branch_data = get_branch_accounts($invoice->Branch);

    $customer = get_customer($invoice->customer_id);

    add_new_exchange_rate($customer['curr_code'], $date_, $invoice->ex_rate);

    // offer price values without freight costs
    $items_total = $invoice->get_items_total_dispatch();
    $freight_tax = $invoice->get_shipping_tax();

    $inv_trans_total = $invoice->get_items_trans_total();

//    display_error($inv_trans_total); die;

    if (!$invoice->is_prepaid() && !empty($invoice->src_docs))
        update_customer_trans_version(get_parent_type(ST_SALESINVOICE), $invoice->src_docs);
    elseif (count($invoice->prepayments)) {    // partial invoice
        $last_payment = end($invoice->prepayments);
        $gl_date = sql2date($last_payment['tran_date']);
    } else {    // final invoice
        $gl_date = $invoice->document_date;
    }

    $ov_gst = 0;
    $taxes = $invoice->get_taxes(); // all taxes with freight_tax
    $dec = user_price_dec();

    foreach ($taxes as $taxitem) {
        $taxitem['Value'] = round2($taxitem['Value'], $dec);
        $ov_gst += $taxitem['Value'];
    }

    if ($invoice->tax_included == 0) {
        $items_added_tax = $ov_gst - $freight_tax;
        $freight_added_tax = $freight_tax;
    } else {
        $items_added_tax = 0;
        $freight_added_tax = 0;
    }

    /* Insert/update the debtor_trans */
    $sales_order = $invoice->order_no;
    if (is_array($sales_order))
        $sales_order = $sales_order[0]; // assume all crucial SO data are same for every delivery

    if ($trans_no) {
        $allocs = get_payments_for($trans_no, ST_SALESINVOICE, $invoice->customer_id);
        delete_comments(ST_SALESINVOICE, $trans_no);
        void_gl_trans(ST_SALESINVOICE, $trans_no, true);
        void_trans_tax_details(ST_SALESINVOICE, $trans_no);
    } else
        $allocs = get_payments_for($invoice->order_no, ST_SALESORDER, $invoice->customer_id);



//    dd($_SESSION['Items']);

    if (isset($invoice->editing_invoice_no) && !empty($invoice->editing_invoice_no)) {
        $allocs = get_payments_for($_SESSION['editing_invoice_no'], ST_SALESINVOICE, $invoice->customer_id);
        delete_sales_order($invoice->sales_order_no, ST_SALESORDER);
        delete_invoice($invoice->sales_order_no);
        unset($invoice->editing_invoice_no);
    }


    if ($invoice->is_prepaid()) // selected prepayment is already in cart
    {
        $allocs = $invoice->prepayments;
        // values posted are reduced by prepaid_factor
        $prepaid_factor = $invoice->prep_amount / $invoice->get_trans_total();
    } else {
        $prepaid_factor = 1;
    }


    // write_customer_trans have to be called after optional void_cust_allocations above
    $invoice_no = write_customer_trans(ST_SALESINVOICE, $trans_no, $invoice->customer_id,
        $invoice->Branch, $date_, $invoice->reference, $items_total, 0,
        $items_added_tax, $invoice->freight_cost, $freight_added_tax,
        $invoice->sales_type, $sales_order, $invoice->ship_via,
        $invoice->due_date, 0, 0, $invoice->dimension_id,
        $invoice->dimension2_id, $invoice->payment,
        $invoice->tax_included, $invoice->prep_amount, $inv_trans_total, $invoice->edit_invoice);

    $invoice->reference = $_SESSION['Items']->reference;

    /** Save Invoice Type and Update Credit Approval Table */

    if ($trans_no == 0) {

        global $global_pay_types_array;

        $pay_type_array = $global_pay_types_array;

        $pay_type = $pay_type_array[$invoice->pay_type];

        if (empty($invoice->pay_type)) {
            cancel_transaction();
            display_error("Payment type should not be empty");
            display_footer_exit();
        }

        $mistake_staff_id = 0;

        if (isset($_POST['invoice_payment']) &&
            $_POST['invoice_payment'] == "PayForStaffMistake" &&
            !empty($_POST['mistake_staff_id'])) {

            $mistake_staff_id = $_POST['mistake_staff_id'];
        }

        $sql = "UPDATE 0_debtor_trans SET 
      invoice_type=" . db_escape(get_post('invoice_type')) . ",
      payment_method=" . db_escape($pay_type) . ",
      mistake_staff_id=" . db_escape($mistake_staff_id) . ",
      contact_person=" . db_escape($_POST['contact_person']) . " WHERE trans_no=$invoice_no AND type=10";

        db_query($sql);

        if (get_post('invoice_type') == 'Credit') {
            $sql = "UPDATE 0_credit_requests SET invoiced=1 WHERE customer_id=$invoice->customer_id AND status='ACCEPTED'";
            db_query($sql);
        }

        /** END -- Save Invoice Type and Update Credit Approval Table */


        if (!$invoice->edit_invoice) {
            $sql = "update 0_service_requests set is_invoiced = 1,trans_no=$invoice_no where 
        id = " . $invoice->service_req_id;
            //dd($sql);

            db_query($sql);
        }
    }


//    die;

    if ($trans_no == 0) {
        $invoice->trans_no = array($invoice_no => 0);
    } else
        move_trans_attachments(ST_SALESINVOICE, $trans_no, $invoice_no);

    $total = 0;

    $acc_receivables = 0;

    $govt_fee_total = 0;
    $bank_charges_total = 0;
    $bank_charges_vat_total = 0;
    $pf_amount_total = 0;

    // for prepayments use deferred income account if set
    $sales_account = $invoice->is_prepaid() ? get_company_pref('deferred_income_act') : 0;

    foreach ($invoice->line_items as $line_no => $invoice_line) {
        $qty = $invoice_line->qty_dispatched;


//        $line_taxfree_price = get_tax_free_price_for_item($invoice_line->stock_id,
//            ($invoice_line->price * $qty), 0, $invoice->tax_included,
//            $invoice->tax_group_array);
//
//        $line_tax = get_full_price_for_item($invoice_line->stock_id,
//                ($invoice_line->price * $qty), 0, $invoice->tax_included,
//                $invoice->tax_group_array) - $line_taxfree_price;


        /**
         * If paying by customer's card,
         * override the govt. bank account to use customer's card.
         * Later we can make payment to tally the amount
         */
        if ($invoice->pay_type == 'PayByCustomerCard') {
            $invoice_line->govt_bank_account = get_bank_gl_account($payment_accounts['PayByCustomerCard']);
        }

        //TAX AFTER DISCOUNT
        $discount_total = ($invoice_line->qty_dispatched * $invoice_line->discount_amount);

        if (empty($invoice_line->price))
            $discount_total = 0;

        $line_taxfree_price = get_tax_free_price_for_item($invoice_line->stock_id,
            ($invoice_line->price * $qty) - $discount_total, 0, $invoice->tax_included,
            $invoice->tax_group_array);

        $line_tax = get_full_price_for_item($invoice_line->stock_id,
                ($invoice_line->price * $qty) - $discount_total, 0, $invoice->tax_included,
                $invoice->tax_group_array) - $line_taxfree_price;


        // FOR ARABIAN
        $sql = "UPDATE 0_debtor_trans SET 
        inv_total=inv_total+$line_tax WHERE trans_no=$invoice_no AND type=10";
        db_query($sql);

        $item_info = get_item($invoice_line->stock_id);
        $item_category = get_item_category($item_info['category_id']);


        if(!empty($item_info['extra_service_charge']))
            $invoice_line->bank_service_charge = $invoice_line->bank_service_charge - $item_info['extra_service_charge'];

        if(empty($item_info['extra_service_charge']))
            $item_info['extra_service_charge'] = 0;

        if($invoice_line->bank_service_charge < 0) {
            $invoice_line->bank_service_charge = 0;
            $item_info['extra_service_charge'] = 0;

        }


        write_customer_trans_detail_item(ST_SALESINVOICE, $invoice_no, $invoice_line->stock_id,
            $invoice_line->item_description, $invoice_line->qty_dispatched,
            $invoice_line->line_price(), $qty ? $line_tax / $qty : 0, $invoice_line->discount_percent,
            $invoice_line->standard_cost, $invoice_line->src_id,
            $trans_no ? $invoice_line->id : 0,
            $invoice_line->govt_fee, $invoice_line->bank_service_charge,
            $invoice_line->bank_service_charge_vat, $invoice_line->transaction_id, $invoice_line->discount_amount, $invoice_line);


        $detail_id = db_insert_id();


        $sql = "update 0_debtor_trans_details set extra_service_charge = ".$item_info['extra_service_charge']." WHERE id=$detail_id";

//        dd($sql);

        db_query($sql);

        add_customer_reward_points($invoice_no, $date_, $invoice->customer_id, $invoice_line->stock_id, $qty, $detail_id);





        /**
         * Update Customer card total
         */
        if (get_post('invoice_type') == 'CustomerCard') {
            $customer_card_total = get_govt_fee_total($invoice_line);
            update_customer_card_total($invoice_no, $customer_card_total);
        }


        // Update delivery items for the quantity invoiced
        if ($invoice_line->qty_old != $invoice_line->qty_dispatched) {
            if ($invoice->is_prepaid())
                update_prepaid_so_line($invoice_line->src_id, $invoice_line->qty_dispatched - $invoice_line->qty_old);
            else
                update_parent_line(ST_SALESINVOICE, $invoice_line->src_id, ($invoice_line->qty_dispatched - $invoice_line->qty_old));
        }

        if ($invoice_line->qty_dispatched != 0) {
            $stock_gl_code = get_stock_gl_code($invoice_line->stock_id);


            if ($invoice_line->line_price() != 0 || !empty($invoice_line->govt_fee) || !empty($invoice_line->bank_service_charge)) {
                //Post sales transaction to GL credit sales

                // If there is a Branch Sales Account, then override with this,
                // else take the Item Sales Account
                if (!$invoice->is_prepaid())
                    $sales_account = $stock_gl_code['sales_account'];

                // If there is a Customer Dimension, then override with this,
                // else take the Item Dimension (if any)


                $dim = ($invoice->dimension_id != $customer['dimension_id'] ? $invoice->dimension_id :
                    ($customer['dimension_id'] != 0 ? $customer["dimension_id"] : $stock_gl_code["dimension_id"]));
                $dim2 = ($invoice->dimension2_id != $customer['dimension2_id'] ? $invoice->dimension2_id :
                    ($customer['dimension2_id'] != 0 ? $customer["dimension2_id"] : $stock_gl_code["dimension2_id"]));


                /** Modifications for AMER, new GL Account entries */

                $disc_amt = 0;
                if ($invoice_line->discount_percent != 0) {
                    $disc_amt = ($invoice_line->discount_amount * $qty);
                }

                //FOOR ANSAR GALLERY
                if (get_post('invoice_type') == 'CustomerCard') {
                    $invoice_line->govt_bank_account = $branch_data["receivables_account"];
                }


                if ($invoice_line->govt_bank_account == $branch_data["receivables_account"]) {
                    $customer['always_use_customer_card'] = 1;
                }

                //for REF
                //IF Using Customer Card, Set Acc Receivables to Unit Price*Qty
                //No need to apply GL for Govt Charges. Coz That is paying with Customer Card
                if ((isset($customer['always_use_customer_card'])) && ($customer['always_use_customer_card'] == 1)) {
                    //Customer Card
                    $amount_paid_by_cust_card = get_govt_fee_total($invoice_line);
                    $invoice_line->govt_fee = 0;
                    $invoice_line->bank_service_charge = 0;
                    $invoice_line->bank_service_charge_vat = 0;
                    $invoice_line->pf_amount = 0;
                    $sql = "UPDATE 0_debtor_trans SET
                    alloc = alloc+$amount_paid_by_cust_card 
                    WHERE trans_no = $invoice_no and type = 10";
                    db_query($sql, 'Customer Card , Other invoice - Set alloc Failed');
                    add_cust_allocation(
                        $amount_paid_by_cust_card,
                        ST_CUSTPAYMENT, (rand(-1, -999999999) + $invoice_no),
                        ST_SALESINVOICE, $invoice_no, $invoice->customer_id, $date_);

                }
                //END - Customer Card Payment Modification

                /** Discount amount is already deducted when calculating $line_tax_freee_price */
                $total_invoice_amount = $line_taxfree_price + $disc_amt + (($invoice_line->govt_fee + $invoice_line->bank_service_charge
                            + $invoice_line->bank_service_charge_vat+$item_info['extra_service_charge']) * $qty);

                $acc_receivables += $total_invoice_amount - $disc_amt;

                $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $sales_account, $dim, $dim2,
                    -($total_invoice_amount) * $prepaid_factor,
                    $invoice->customer_id, "The sales price GL posting could not be inserted");


                $transaction_id = $invoice_line->transaction_id ? $invoice_line->transaction_id : "N/A";

                //Posting GL account entries for government charges
                if (!empty($invoice_line->govt_fee)) {
                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no,
                        $date_,
                        $invoice_line->govt_bank_account, $dim, $dim2,
                        -($invoice_line->govt_fee * $prepaid_factor) * $qty,
                        $invoice->customer_id, "The sales price GL posting could not be inserted", 0,
                        "Govt.Fee", $transaction_id);

                    $govt_fee_total += $invoice_line->govt_fee * $qty;
                }

                //Posting GL account entries for Bank service charge
                if (!empty($invoice_line->bank_service_charge)) {
                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no,
                        $date_,
                        $invoice_line->govt_bank_account, $dim, $dim2,
                        -($invoice_line->bank_service_charge * $prepaid_factor) * $qty,
                        $invoice->customer_id, "The sales price GL posting could not be inserted", 0,
                        "Bank service charge", $transaction_id);

                    $bank_charges_total += $invoice_line->bank_service_charge * $qty;
                }

                //Posting GL account entries for Bank service charge VAT
                if (!empty($invoice_line->bank_service_charge_vat)) {
                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no,
                        $date_,
                        $invoice_line->govt_bank_account, $dim, $dim2,
                        -($invoice_line->bank_service_charge_vat * $prepaid_factor) * $qty,
                        $invoice->customer_id, "The sales price GL posting could not be inserted", 0,
                        "Additional Fee", $transaction_id);

                    $bank_charges_vat_total += $invoice_line->bank_service_charge_vat * $qty;
                }

                //Posting GL account entries for PF Amount
                if (!empty($invoice_line->pf_amount)) {
                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no,
                        $date_,
                        $invoice_line->govt_bank_account, $dim, $dim2,
                        -($invoice_line->pf_amount * $prepaid_factor) * $qty,
                        $invoice->customer_id, "The sales price GL posting could not be inserted", 0,
                        "Service charge", $transaction_id);

                    $pf_amount_total += $invoice_line->pf_amount * $qty;
                }


                $cogs_amount = ($invoice_line->govt_fee + $invoice_line->bank_service_charge
                        + $invoice_line->bank_service_charge_vat + $invoice_line->pf_amount) * $qty;


                if (!empty($cogs_amount)) {
                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_,
                        $item_category['dflt_cogs_act'], $dim, $dim2,
                        ($cogs_amount) * $prepaid_factor,
                        $invoice->customer_id, "Cost of Goods Sold");


                    $sql = " select * from 0_bank_accounts  where account_code 
                        =  " . $invoice_line->govt_bank_account . " limit 1 ";

                    $res = db_query($sql);
                    $bank_info = db_fetch($res);
                    $bank_gl_acc = $bank_info['id'];

                    if (!empty($bank_gl_acc)) { //Add to Bank trans

                        add_bank_trans(10, $invoice_no, $bank_gl_acc, $invoice->reference,
                            $date_, $cogs_amount, null, null);
                    }

                }


                if ($invoice_line->discount_percent != 0) {

                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_,
                        $branch_data["sales_discount_account"], $dim, $dim2,
                        $disc_amt,
                        $invoice->customer_id, "The sales discount GL posting could not be inserted");
                } /*end of if discount !=0 */


            }
        } /*quantity dispatched is more than 0 */

    } /*end of delivery_line loop */


    /**
     * Update Customer card total
     */
    if ($invoice->pay_type == 'PayByCustomerCard') {
        update_customer_card_total($invoice_no, ($items_total + $charge_shipping + $items_added_tax + $freight_added_tax));
    }


    if (($items_total + $charge_shipping) != 0) {

        // Edited : Bipin
        // $receivables_amount = (
        //     $items_total +
        //     $charge_shipping +
        //     $items_added_tax +
        //     $freight_added_tax +
        //     $govt_fee_total +
        //     $bank_charges_total+$bank_charges_vat_total
        // ) * $prepaid_factor;


        $receivables_amount = (
                $acc_receivables +
                $charge_shipping +
                $items_added_tax
            ) * $prepaid_factor;

        // $receivables_amount = (
        //     $items_total +
        //     $charge_shipping +
        //     $items_added_tax
        // ) * $prepaid_factor;


        $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $branch_data["receivables_account"], 0, 0,
            $receivables_amount,
            $invoice->customer_id, "The total debtor GL posting could not be inserted");
    }
    $to_allocate = ($items_total + $charge_shipping + $items_added_tax + $freight_added_tax);

    if ($charge_shipping != 0) {
        $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $company_data["freight_act"], 0, 0,
            -$invoice->get_tax_free_shipping() * $prepaid_factor, $invoice->customer_id,
            "The freight GL posting could not be inserted");
    }

    /**
     * Note: When using customer's card for payment there might have some issues with tax.
     * However, For all services that a customer card is used as payment method we don't have tax for that item.
     * So if in the future it changes - we need to take that into consideration here.
     */
    // post all taxes
    foreach ($taxes as $taxitem) {
        if ($taxitem['Net'] != 0) {
            $ex_rate = get_exchange_rate_from_home_currency(get_customer_currency($invoice->customer_id), $date_);
            add_trans_tax_details(ST_SALESINVOICE, $invoice_no, $taxitem['tax_type_id'],
                $taxitem['rate'], $invoice->tax_included, $prepaid_factor * $taxitem['Value'],
                $taxitem['Net'], $ex_rate, $date_, $invoice->reference, TR_OUTPUT);
            if (isset($taxitem['sales_gl_code']))
                $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $taxitem['sales_gl_code'], 0, 0,
                    (-$taxitem['Value']) * $prepaid_factor, $invoice->customer_id,
                    "A tax GL posting could not be inserted");
        }
    }


    /*Post a balance post if $total != 0 */
    add_gl_balance(ST_SALESINVOICE, $invoice_no, $date_, -$total, PT_CUSTOMER, $invoice->customer_id);

    add_comments(ST_SALESINVOICE, $invoice_no, $date_, $invoice->Comments);

    if (isset($_SESSION["invoiced_by"]))
        unset($_SESSION["invoiced_by"]);

    if ($trans_no == 0) {
        $Refs->save(ST_SALESINVOICE, $invoice_no, $invoice->reference, null, $invoice->fixed_asset);

        if (
            in_array($invoice->pay_type, [
                "PayNow",
                "PayNoWCC",
                "PayByBankTransfer",
                "PayCashAndCard",
                "PayByCustomerCard"
            ])
            && !$invoice->edit_invoice
        ) {
            switch ($invoice->pay_type) {
                case 'PayNoWCC':
                    $payment_acc = $payment_accounts['PayNoWCC'];
                    $payment_method = "CreditCard";
                    break;
                case 'PayByBankTransfer':
                    $payment_acc = $payment_accounts['PayByBankTransfer'];
                    $payment_method = "BankTransfer";
                    break;
                case 'PayNow':
                    $payment_acc = $payment_accounts['PayNow'];
                    $payment_method = "Cash";
                    break;
                case 'PayByCustomerCard':
                    $payment_acc = $payment_accounts['PayByCustomerCard'];
                    $payment_method = "CustomerCard";
                    break;
            }

            $amount = $items_total + $items_added_tax + $invoice->freight_cost + $freight_added_tax;

            // to use debtors.pmt_discount on cash sale:
            // extend invoice entry page with final amount after discount
            // and change line below.

            $discount = 0; // $invoice->cash_discount*$amount;

            $round_off_amt = $_POST['round_of_amount'];
            $round_off_amt = str_replace(",", "", $round_off_amt);
            $credit_card_charge = $SysPrefs->prefs['default_credit_card_charge_percent'];

            if (empty($credit_card_charge)) $credit_card_charge = 0;

            if (empty($round_off_amt))
                $round_off_amt = 0;

            if ($invoice->pay_type == "PayCashAndCard") { //Pay By Split

                $cash_amount = $_POST['cash_amount'];
                $card_amount = $_POST['card_amount'];

                $cash_amount = str_replace(",", "", $cash_amount);
                $card_amount = str_replace(",", "", $card_amount);

                if (empty($cash_amount))
                    $cash_amount = 0;

                if (empty($card_amount))
                    $card_amount = 0;


                /** *******Cash Amount Receipt entry ****************************/

                $payment_ref = get_next_payment_ref($invoice->dimension_id);


                $pmtno = write_customer_payment(
                    0,
                    $invoice->customer_id,
                    $invoice->Branch,
                    $payment_accounts['PayNow'],
                    $date_,
                    $payment_ref,
                    $cash_amount,
                    $discount,
                    'Payment for Invoice No. #' . $invoice->reference,
                    0,
                    0,
                    0,
                    '',
                    $invoice->dimension_id,
                    $round_off_amt
                );

                add_cust_allocation($cash_amount, ST_CUSTPAYMENT, $pmtno, ST_SALESINVOICE, $invoice_no, $invoice->customer_id, $date_);

                update_debtor_trans_allocation(ST_SALESINVOICE, $invoice_no, $invoice->customer_id);
                update_debtor_trans_allocation(ST_CUSTPAYMENT, $pmtno, $invoice->customer_id);

                $sql = "update 0_debtor_trans  set  round_of_amount=$round_off_amt,payment_method = 'Cash' where type = 12 and trans_no=$pmtno";
                db_query($sql);
                /** ********END Cash Amount Receipt entry ****************************************/


                /** ******Card Amount Receipt entry ***********************************************/

                $payment_ref = get_next_payment_ref($invoice->dimension_id);

                $pmtno = write_customer_payment(
                    0,
                    $invoice->customer_id,
                    $invoice->Branch,
                    $payment_accounts['PayNoWCC'],
                    $date_,
                    $payment_ref,
                    $card_amount,
                    $discount,
                    'Payment for Invoice No. #' . $invoice->reference,
                    0,
                    $credit_card_charge,
                    0,
                    '',
                    $invoice->dimension_id
                );

                add_cust_allocation($card_amount, ST_CUSTPAYMENT, $pmtno, ST_SALESINVOICE, $invoice_no, $invoice->customer_id, $date_);

                update_debtor_trans_allocation(ST_SALESINVOICE, $invoice_no, $invoice->customer_id);
                update_debtor_trans_allocation(ST_CUSTPAYMENT, $pmtno, $invoice->customer_id);

                $sql = "update 0_debtor_trans  set  payment_method = 'CreditCard' where type = 12 and trans_no=$pmtno";
                db_query($sql);

                /** ******END Card Amount Receipt entry ***********************************************/

            } else if ($invoice->pay_type == "PayNoWCC") // For Credit Card
            {
                $card_amount = $amount;


                $payment_ref = get_next_payment_ref($invoice->dimension_id);

                $pmtno = write_customer_payment(0, $invoice->customer_id,
                    $invoice->Branch, $payment_accounts['PayNoWCC'], $date_,
                    $payment_ref,
                    $card_amount, $discount,
                    'Payment for Invoice No. #' . $invoice->reference, 0, $credit_card_charge, 0, '', $invoice->dimension_id, $round_off_amt);


                add_cust_allocation($card_amount, ST_CUSTPAYMENT, $pmtno, ST_SALESINVOICE, $invoice_no, $invoice->customer_id, $date_);

                update_debtor_trans_allocation(ST_SALESINVOICE, $invoice_no, $invoice->customer_id);
                update_debtor_trans_allocation(ST_CUSTPAYMENT, $pmtno, $invoice->customer_id);

                $sql = "update 0_debtor_trans  set  payment_method = 'CreditCard',round_of_amount=$round_off_amt where type = 12 and trans_no=$pmtno";
                db_query($sql);


            } else {

                $payment_ref = get_next_payment_ref($invoice->dimension_id);

                $pmtno = write_customer_payment(
                    0,
                    $invoice->customer_id,
                    $invoice->Branch,
                    $payment_accounts['PayNow'],
                    $date_,
                    $payment_ref,
                    $amount - $discount,
                    $discount,
                    'Payment for Invoice No. #' . $invoice->reference,
                    0,
                    0,
                    0,
                    '',
                    $invoice->dimension_id,
                    $round_off_amt
                );

                add_cust_allocation($amount, ST_CUSTPAYMENT, $pmtno, ST_SALESINVOICE, $invoice_no, $invoice->customer_id, $date_);

                update_debtor_trans_allocation(ST_SALESINVOICE, $invoice_no, $invoice->customer_id);
                update_debtor_trans_allocation(ST_CUSTPAYMENT, $pmtno, $invoice->customer_id);

                $sql = "update 0_debtor_trans  set  round_of_amount=$round_off_amt,payment_method = " . db_escape($payment_method) .
                    " where type = 12 and trans_no=$pmtno";

                db_query($sql);
            }


        }
    }


    reallocate_payments($invoice_no, ST_SALESINVOICE, $date_, $to_allocate, $allocs, $invoice->customer_id);
    hook_db_postwrite($invoice, ST_SALESINVOICE);

    runAutomaticAllocation($invoice->customer_id);

    commit_transaction();

    return $invoice_no;
}


/**
 * @param $invoice_line
 * @return float|int
 * get govt_fee_total
 */

function get_govt_fee_total($invoice_line)
{

    return (
            $invoice_line->govt_fee +
            $invoice_line->bank_service_charge +
            $invoice_line->bank_service_charge_vat +
            $invoice_line->pf_amount) * $invoice_line->quantity;

}

/**
 * @param $trans_no
 * @param $total
 * @return bool|mysqli_result|resource
 * Update Customer card paid amount
 */
function update_customer_card_total($trans_no, $total)
{

    $sql = "UPDATE 0_debtor_trans set customer_card_amount = $total,alloc=$total WHERE trans_no=$trans_no";
    return db_query($sql);

}

function write_sales_invoice_tasheel(&$invoice)
{
    global $Refs, $SysPrefs;

    $trans_no = $invoice->trans_no;
    if (is_array($trans_no))
        $trans_no = key($trans_no);

    $date_ = $invoice->document_date;
    $charge_shipping = $invoice->freight_cost;


//    $payment_accounts = [
//        'PayNow' => empty($SysPrefs->prefs['dflt_csh_pmt_act'])
//            ? $_SESSION['wa_current_user']->cashier_account
//            : $SysPrefs->prefs['dflt_csh_pmt_act'],
//        'PayNoWCC' => $SysPrefs->prefs['dflt_credit_card_pmt_act'],
//        'PayByBankTransfer' => $SysPrefs->prefs['dflt_bnk_trnsfr_pmt_act'],
//        'PayByCustomerCard' => $SysPrefs->prefs['customer_card_act']
//    ];

    $payment_accounts = [
        'PayNow' => !empty($_SESSION['wa_current_user']->cashier_account)
            ? $_SESSION['wa_current_user']->cashier_account :
            $SysPrefs->prefs['dflt_csh_pmt_act'],
        'PayNoWCC' => $SysPrefs->prefs['dflt_credit_card_pmt_act'],
        'PayByBankTransfer' => $SysPrefs->prefs['dflt_bnk_trnsfr_pmt_act'],
        'PayByCustomerCard' => $SysPrefs->prefs['customer_card_act']
    ];


    begin_transaction();

    hook_db_prewrite($invoice, ST_SALESINVOICE);
    $company_data = get_company_prefs();

    $branch_data = get_branch_accounts($invoice->Branch);

    $customer = get_customer($invoice->customer_id);

    add_new_exchange_rate($customer['curr_code'], $date_, $invoice->ex_rate);


    // offer price values without freight costs
    $items_total = $invoice->get_items_total_dispatch();
    $freight_tax = $invoice->get_shipping_tax();

    $inv_trans_total = $invoice->get_items_trans_total();

    if (!$invoice->is_prepaid() && !empty($invoice->src_docs))
        update_customer_trans_version(get_parent_type(ST_SALESINVOICE), $invoice->src_docs);
    elseif (count($invoice->prepayments)) {    // partial invoice
        $last_payment = end($invoice->prepayments);
        $gl_date = sql2date($last_payment['tran_date']);
    } else {    // final invoice
        $gl_date = $invoice->document_date;
    }

    $ov_gst = 0;
    $taxes = $invoice->get_taxes(); // all taxes with freight_tax
    $dec = user_price_dec();

    foreach ($taxes as $taxitem) {
        $taxitem['Value'] = round2($taxitem['Value'], $dec);
        $ov_gst += $taxitem['Value'];
    }

    if ($invoice->tax_included == 0) {
        $items_added_tax = $ov_gst - $freight_tax;
        $freight_added_tax = $freight_tax;
    } else {
        $items_added_tax = 0;
        $freight_added_tax = 0;
    }

    /* Insert/update the debtor_trans */
    $sales_order = $invoice->order_no;
    if (is_array($sales_order))
        $sales_order = $sales_order[0]; // assume all crucial SO data are same for every delivery

    if ($trans_no) {
        $allocs = get_payments_for($trans_no, ST_SALESINVOICE, $invoice->customer_id);
        delete_comments(ST_SALESINVOICE, $trans_no);
        void_gl_trans(ST_SALESINVOICE, $trans_no, true);
        void_trans_tax_details(ST_SALESINVOICE, $trans_no);
    } else
        $allocs = get_payments_for($invoice->order_no, ST_SALESORDER, $invoice->customer_id);


    if (isset($invoice->editing_invoice_no) && !empty($invoice->editing_invoice_no)) {


        $sql = "SELECT * FROM " . TB_PREF . "debtor_trans WHERE trans_no = " . db_escape($invoice->editing_invoice_no) . " and type=10";
        $query = db_query($sql, "Failed getting debtor_trans row");
        $old_invoice = db_fetch($query);


        $allocs = get_payments_for($invoice->editing_invoice_no, ST_SALESINVOICE, $invoice->customer_id);
        delete_sales_order($invoice->sales_order_no, ST_SALESORDER);
        delete_invoice($invoice->sales_order_no);
        unset($invoice->editing_invoice_no);
    } else {
//        $invoice->reference = get_next_invoice_ref($invoice->dimension_id);
    }

    if ($invoice->is_prepaid()) // selected prepayment is already in cart
    {
        $allocs = $invoice->prepayments;
        // values posted are reduced by prepaid_factor
        $prepaid_factor = $invoice->prep_amount / $invoice->get_trans_total();
    } else {
        $prepaid_factor = 1;
    }


    // write_customer_trans have to be called after optional void_cust_allocations above
    $invoice_no = write_customer_trans(ST_SALESINVOICE, $trans_no, $invoice->customer_id,
        $invoice->Branch, $date_, $invoice->reference, $items_total, 0,
        $items_added_tax, $invoice->freight_cost, $freight_added_tax,
        $invoice->sales_type, $sales_order, $invoice->ship_via,
        $invoice->due_date, 0, 0, $invoice->dimension_id,
        $invoice->dimension2_id, $invoice->payment, $invoice->tax_included,
        $invoice->prep_amount, $inv_trans_total, $invoice->edit_invoice);


    if ($trans_no == 0) {

        if (check_value('staff_mistake_invoice') &&
            !empty($_POST['mistake_staff_id'])) {

            //STAFF MISTAKE INVOICE
            $mistake_staff_id = $_POST['mistake_staff_id'];


            $sql = "UPDATE 0_debtor_trans SET 
              payment_method=" . db_escape("StaffMistake") . ",
              mistake_staff_id=" . db_escape($mistake_staff_id) . ",
              contact_person=" . db_escape($_POST['contact_person']) . " WHERE trans_no=$invoice_no AND type=10";

            db_query($sql);
        }


        $invoice_type = get_post('invoice_type');

        if(empty($invoice->payment_card) || $invoice->payment_card == $branch_data["receivables_account"]) {
            $invoice_type = "CustomerCard";
        }


        /** Save Invoice Type and Update Credit Approval Table */

        $sql = "UPDATE 0_debtor_trans SET 
              invoice_type=" . db_escape($invoice_type) . ",token_number=" . db_escape(get_post('token_no')) . " 
              ,contact_person=" . db_escape($_POST['contact_person']) . ",sub_customer_id=".db_escape($_POST['sub_customer_list'])."  WHERE trans_no=$invoice_no AND type=10";

        db_query($sql);

        if (get_post('invoice_type') == 'Credit') {
            $sql = "UPDATE 0_credit_requests SET invoiced=1 WHERE customer_id=$invoice->customer_id AND status='ACCEPTED'";
            db_query($sql);
        }

        /** END -- Save Invoice Type and Update Credit Approval Table */

    }


    $payment_flag = INV_TASHEEL_CC;
    if (isset($invoice->payment_card) && !empty($invoice->payment_card) && $invoice->payment_card != $branch_data["receivables_account"]) {
        $payment_flag = INV_TASHEEL_ED;
    }

    $tadbeer_customer_card_amount = 0;
    if (isset($invoice->is_tadbeer) && ($invoice->is_tadbeer == 1) && ($invoice->show_items == "tb")) {

        $payment_flag = INV_TADBEER_CC;//TADBEER CUSTOMER CARD
        if (isset($invoice->payment_card) && !empty($invoice->payment_card) && $invoice->payment_card != $branch_data["receivables_account"]) {
            $payment_flag = INV_TADBEER_ED; //TADBEER E_DIRHAM
        }

    }

    $sql = "UPDATE 0_debtor_trans SET payment_flag=$payment_flag WHERE trans_no=$invoice_no AND type=10";
    db_query($sql, "Error Payment Flag for Tasheel");

    if ($trans_no == 0) {
        $invoice->trans_no = array($invoice_no => 0);
    } else
        move_trans_attachments(ST_SALESINVOICE, $trans_no, $invoice_no);

    $total = 0;
    $acc_receivables = 0;
    $total_disc_amount = 0;
    $customer_card_amount_total = 0;

    // for prepayments use deferred income account if set
    $sales_account = $invoice->is_prepaid() ? get_company_pref('deferred_income_act') : 0;

    foreach ($invoice->line_items as $line_no => $invoice_line) {


        /** IF Using customer card set discount to 0, Discount will pay with manual voucher entry*/

        if (!isset($invoice->payment_card) || empty($invoice->payment_card) || $invoice->payment_card == $branch_data["receivables_account"]) {
            $invoice_line->discount_percent = 0;
            $invoice_line->discount_amount = 0;
        }

        /** END */


        $qty = $invoice_line->qty_dispatched;
        $line_taxfree_price = get_tax_free_price_for_item($invoice_line->stock_id,
            $invoice_line->price * $qty, 0, $invoice->tax_included,
            $invoice->tax_group_array);

        $line_tax = get_full_price_for_item($invoice_line->stock_id,
                $invoice_line->price * $qty, 0, $invoice->tax_included,
                $invoice->tax_group_array) - $line_taxfree_price;


//        $invoice_line->transaction_id_updated_at = get_post('transaction_id_updated_at');

        $invoice_line->govt_bank_account = $invoice->payment_card;

        if (empty($invoice_line->govt_bank_account)) {
            $invoice_line->govt_bank_account = $branch_data["receivables_account"];
        }


        if (isset($invoice->is_tadbeer) && ($invoice->is_tadbeer == 1)) {

            $invoice_line->govt_fee = $invoice_line->govt_fee - ($invoice_line->line_price());

        }


        if ($invoice_line->line_price() == 80) {
            $invoice_line->stock_id = "TAS_AUTO";
        } else if ($invoice_line->line_price() == 240) {
            $invoice_line->stock_id = "TAS_AUTO240";
        } else if ($invoice_line->line_price() == 40) {
            $invoice_line->stock_id = "TAS_AUTO40";
        }

        write_customer_trans_detail_item(ST_SALESINVOICE, $invoice_no, $invoice_line->stock_id,
            $invoice_line->item_description, $invoice_line->qty_dispatched,
            $invoice_line->line_price(), $qty ? $line_tax / $qty : 0, $invoice_line->discount_percent,
            $invoice_line->standard_cost, $invoice_line->src_id,
            $trans_no ? $invoice_line->id : 0,
            $invoice_line->govt_fee, $invoice_line->bank_service_charge,
            $invoice_line->bank_service_charge_vat, $invoice_line->transaction_id,
            $invoice_line->discount_amount, $invoice_line);


        $detail_id = db_insert_id();
        add_customer_reward_points($invoice_no, $date_, $invoice->customer_id, $invoice_line->stock_id, $qty, $detail_id);


        //Insert other fee details to 0_other_charges_trans table

        $total_other_charges = 0;
        $debtor_trans_details_id = db_insert_id();

        $item_info = get_item($invoice_line->stock_id);
        $item_category = get_item_category($item_info['category_id']);


        /*** */
        if ($invoice->show_items == 'ts') {
            $total_other_charges = ($invoice_line->govt_fee + $invoice_line->line_price());
        }


        //Get total customer card amount
        if (empty($invoice->payment_card) || $invoice->payment_card == $branch_data["receivables_account"]) {
//            if ($fee_row->account_code != $SysPrefs->prefs['noqodi_account']) { //CUSTOMER_CARD
            $customer_card_amount_total += abs($total_other_charges * $qty);
//            }
        }

//        $total_other_charges = $total_other_charges;

        /**** */

        /*  if (!empty($invoice_line->other_fee_info_json)) {
             $json_string = base64_decode($invoice_line->other_fee_info_json);
             $fee_object = json_decode($json_string);
             if (!empty($fee_object)) {
                 foreach ($fee_object as $fee_row) {

                     if ($fee_row->amount) {


                         if ($invoice->show_items == 'ts') {
                             $fee_row->amount = ($invoice_line->govt_fee+$invoice_line->line_price());
                         }


                         //Get total customer card amount
                         if (empty($invoice->payment_card) || $invoice->payment_card == 1200) {
                             if ($fee_row->account_code != $SysPrefs->prefs['noqodi_account']) { //CUSTOMER_CARD
                                 $customer_card_amount_total += abs($fee_row->amount * $qty);
                             }
                         }

                         $total_other_charges = $total_other_charges + floatval($fee_row->amount);
                     }

                     $sql = "INSERT INTO 0_other_charges_trans_details
                     (debtor_trans_detail_id,acc_code,amount,description) VALUES
                     ($debtor_trans_details_id,'$fee_row->account_code',$fee_row->amount,'$fee_row->description')";
                     db_query($sql, "The other charges transaction detail could not be written");
                 }
             }
         } */


        $tadbeer_invoice = false;
        if ($invoice->show_items == 'tb') {
            $tadbeer_invoice = empty($invoice_line->ed_transaction_id);
        }

//        if (((!empty($invoice_line->transaction_id) || !$SysPrefs->prefs['gl_after_transaction_id_update']) && !$tadbeer_invoice) ||
//            empty(($invoice_line->govt_fee + $invoice_line->bank_service_charge +
//                    $invoice_line->bank_service_charge_vat) * $invoice_line->qty_dispatched)) {

        // Update delivery items for the quantity invoiced
        if ($invoice_line->qty_old != $invoice_line->qty_dispatched) {
            if ($invoice->is_prepaid())
                update_prepaid_so_line($invoice_line->src_id, $invoice_line->qty_dispatched - $invoice_line->qty_old);
            else
                update_parent_line(ST_SALESINVOICE, $invoice_line->src_id, ($invoice_line->qty_dispatched - $invoice_line->qty_old));
        }

        if ($invoice_line->qty_dispatched != 0) {
            $stock_gl_code = get_stock_gl_code($invoice_line->stock_id);

//            display_error(print_r($stock_gl_code,true));

            if ($invoice_line->line_price() != 0 || !empty($invoice_line->govt_fee) || !empty($invoice_line->bank_service_charge)) {
                //Post sales transaction to GL credit sales
                // If there is a Branch Sales Account, then override with this,
                // else take the Item Sales Account
                if (!$invoice->is_prepaid())
                    $sales_account = $stock_gl_code['sales_account'];
//                    $sales_account = ($branch_data['sales_account'] != "" ? $branch_data['sales_account'] : $stock_gl_code['sales_account']);
                // If there is a Customer Dimension, then override with this,
                // else take the Item Dimension (if any)
                $dim = ($invoice->dimension_id != $customer['dimension_id'] ? $invoice->dimension_id :
                    ($customer['dimension_id'] != 0 ? $customer["dimension_id"] : $stock_gl_code["dimension_id"]));
                $dim2 = ($invoice->dimension2_id != $customer['dimension2_id'] ? $invoice->dimension2_id :
                    ($customer['dimension2_id'] != 0 ? $customer["dimension2_id"] : $stock_gl_code["dimension2_id"]));


//                print_r($sales_account);

                /** Modifications for AMER, new GL Account entries */

                $disc_amt = 0;
                if ($invoice_line->discount_percent != 0) {
                    $disc_amt = ($invoice_line->discount_amount * $qty);
                }

                $total_disc_amount += $disc_amt;

                $total_invoice_amount = $line_taxfree_price + ($total_other_charges * $qty);

                if (isset($invoice->is_tadbeer) && ($invoice->is_tadbeer == 1)) {
                    $total_invoice_amount = ($total_other_charges * $qty);
                }

                $acc_receivables += $total_invoice_amount - $disc_amt;
                $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $sales_account, $dim, $dim2,
                    -($total_invoice_amount) * $prepaid_factor,
                    $invoice->customer_id, "The sales price GL posting could not be inserted");

                $transaction_id = $invoice_line->transaction_id ? $invoice_line->transaction_id : "N/A";

                $ed_transaction_id = $invoice_line->ed_transaction_id ? $invoice_line->ed_transaction_id : "N/A";


//                    display_error(print_r($invoice_line,true)); die;


                if (!empty($invoice_line->govt_fee)) {


                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $invoice_line->govt_bank_account, $dim, $dim2,
                        -($total_invoice_amount),
                        $invoice->customer_id, "The other charges - govt fee GL posting could not be inserted", 0,
                        "ED/GD Payment", $transaction_id);


                }


                if (isset($invoice->is_tadbeer) && ($invoice->is_tadbeer == 1)) {

                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_,
                        $item_category['dflt_cogs_act'], $dim, $dim2,
                        ($total_invoice_amount - $line_taxfree_price),
                        $invoice->customer_id, "Cost of Goods Sold");

                }


                // post all taxes
                foreach ($taxes as $taxitem) {
                    if ($taxitem['Net'] != 0) {
                        $ex_rate = get_exchange_rate_from_home_currency(get_customer_currency($invoice->customer_id), $date_);
                        add_trans_tax_details(ST_SALESINVOICE, $invoice_no, $taxitem['tax_type_id'],
                            $taxitem['rate'], $invoice->tax_included, $prepaid_factor * $taxitem['Value'],
                            $taxitem['Net'], $ex_rate, $date_, $invoice->reference, TR_OUTPUT);
                        if (isset($taxitem['sales_gl_code']))
                            $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $taxitem['sales_gl_code'], 0, 0,
                                (-$taxitem['Value']) * $prepaid_factor, $invoice->customer_id,
                                "A tax GL posting could not be inserted");
                    }
                }


                //Posting GL account entries for service charge
                if (!empty($line_taxfree_price)) {
//                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $SysPrefs->prefs['service_charge_return_account'], $dim, $dim2,
//                        ($line_taxfree_price) + $items_added_tax,
//                        $invoice->customer_id, "The sales price GL posting could not be inserted", 0,
//                        "Service Charge");


                    $full_srv_amt = ($line_taxfree_price) + $items_added_tax;

                    $tawseel_revenue = 0;
                    $memo = "Service Charge";

                    if($full_srv_amt == 80) {
                        $tawseel_revenue = 20;
                        $full_srv_amt = $full_srv_amt-$tawseel_revenue;
                        $memo = "Tawseel Cost";
                    }


                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, 5105, $dim, $dim2,
                        $full_srv_amt,
                        $invoice->customer_id, "The sales price GL posting could not be inserted", 0,
                        $memo);


                    if(!empty($tawseel_revenue)) {
                        $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, 5890, $dim, $dim2,
                            $tawseel_revenue,
                            $invoice->customer_id, "The sales price GL posting could not be inserted", 0,
                            "Tawseel Revenue");
                    }


                }

                if ($invoice_line->discount_percent != 0) {

                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_,
                        $branch_data["sales_discount_account"], $dim, $dim2,
                        $disc_amt,
                        $invoice->customer_id, "The sales discount GL posting could not be inserted");
                } /*end of if discount !=0 */

            }
        } /*quantity dispatched is more than 0 */

//        }

    } /*end of delivery_line loop */

    if (isset($_SESSION["invoiced_by"]))
        unset($_SESSION["invoiced_by"]);

    if (($items_total + $charge_shipping) != 0) {

        $receivables_amount = (
                $acc_receivables +
                $charge_shipping +
                $items_added_tax
            ) * $prepaid_factor;

        $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $branch_data["receivables_account"],
            $invoice->dimension_id, 0,
            $receivables_amount,
            $invoice->customer_id, "The total debtor GL posting could not be inserted");

        //Posting GL account entries for E-Dirham card
        if (!empty($receivables_amount)) {


            if (isset($invoice->is_tadbeer) && ($invoice->is_tadbeer == 1)) {


            } else {


                if (isset($invoice->payment_card) && !empty($invoice->payment_card) && $invoice->payment_card != $branch_data["receivables_account"]) {
                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $invoice->payment_card,
                        $invoice->dimension_id, 0,
                        -($receivables_amount + $total_disc_amount),
                        $invoice->customer_id, "The E-Dirham card payment GL posting could not be inserted", 0,
                        "E-Dirham Card Payment");
                } else {
                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $branch_data["receivables_account"],
                        $invoice->dimension_id, $dim2,
                        -($receivables_amount + $total_disc_amount),
                        $invoice->customer_id, "The customer card GL posting could not be inserted", 0,
                        "Customer Card Payment");


                    //SET ALLOC=(OV_AMOUNT+OV_GST) to avoid invoice for customer payment
                    $sql = "UPDATE 0_debtor_trans SET alloc=$receivables_amount 
                        WHERE trans_no=$invoice_no AND type=" . ST_SALESINVOICE;
                    db_query($sql, "Error Updating ov_amount+gst=alloc");

                    //Insert an entry in customer allocation for fixing balanced in customer balance report
//                add_cust_allocation($receivables_amount, ST_CUSTPAYMENT, 0, ST_SALESINVOICE, $invoice_no, $invoice->customer_id, $date_);


                }


            }


        }

    }
    $to_allocate = ($items_total + $charge_shipping + $items_added_tax + $freight_added_tax);

//    $to_allocate = $inv_trans_total;


    if ($charge_shipping != 0) {
        $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $company_data["freight_act"], 0, 0,
            -$invoice->get_tax_free_shipping() * $prepaid_factor, $invoice->customer_id,
            "The freight GL posting could not be inserted");
    }

    /*Post a balance post if $total != 0 */
    add_gl_balance(ST_SALESINVOICE, $invoice_no, $date_, -$total, PT_CUSTOMER, $invoice->customer_id);

    add_comments(ST_SALESINVOICE, $invoice_no, $date_, $invoice->Comments);

    if ($trans_no == 0 && !$invoice->edit_invoice) {

        $Refs->save(ST_SALESINVOICE, $invoice_no, $invoice->reference, null, $invoice->fixed_asset);

        if ($payment_flag == INV_TASHEEL_CC) {


            $round_off_amt = 0;
            $amount = $items_total + $items_added_tax + $invoice->freight_cost + $freight_added_tax;

            // to use debtors.pmt_discount on cash sale:
            // extend invoice entry page with final amount after discount
            // and change line below.

            $discount = 0; // $invoice->cash_discount*$amount;

            $payment_acc = $payment_accounts['PayByCustomerCard'];
            $payment_method = "CustomerCard";

            $payment_ref = get_next_payment_ref($invoice->dimension_id);

            $pmtno = write_customer_payment(
                0,
                $invoice->customer_id,
                $invoice->Branch,
                $payment_acc,
                $date_,
                $payment_ref,
                $amount - $discount,
                $discount,
                'Payment for Invoice No. #' . $invoice->reference,
                0,
                0,
                0,
                '',
                $invoice->dimension_id,
                $round_off_amt
            );

            add_cust_allocation($amount, ST_CUSTPAYMENT, $pmtno, ST_SALESINVOICE, $invoice_no, $invoice->customer_id, $date_);

            update_debtor_trans_allocation(ST_SALESINVOICE, $invoice_no, $invoice->customer_id);
            update_debtor_trans_allocation(ST_CUSTPAYMENT, $pmtno, $invoice->customer_id);

            $sql = "update 0_debtor_trans  set  round_of_amount=$round_off_amt,payment_method = " . db_escape($payment_method) .
                " where type = 12 and trans_no=$pmtno";

            db_query($sql);
        }
    }


//    if ($invoice->payment_terms['cash_sale'] && $invoice->pos['pos_account']) {
//            $amount = $items_total + $items_added_tax + $invoice->freight_cost
//                + $freight_added_tax;
//
//            // to use debtors.pmt_discount on cash sale:
//            // extend invoice entry page with final amount after discount
//            // and change line below.
//            $discount = 0; // $invoice->cash_discount*$amount;
//            $pmtno = write_customer_payment(0, $invoice->customer_id,
//                $invoice->Branch, $invoice->pos['pos_account'], $date_,
//                $Refs->get_next(ST_CUSTPAYMENT, null, array('customer' => $invoice->customer_id,
//                    'branch' => $invoice->Branch, 'date' => $date_)),
//                $amount - $discount, $discount,
//                $invoice->pos['pos_name'] . ' #' . $invoice_no);
//            add_cust_allocation($amount, ST_CUSTPAYMENT, $pmtno, ST_SALESINVOICE, $invoice_no, $invoice->customer_id, $date_);
//
//            update_debtor_trans_allocation(ST_SALESINVOICE, $invoice_no, $invoice->customer_id);
//            update_debtor_trans_allocation(ST_CUSTPAYMENT, $pmtno, $invoice->customer_id);
//        }
//    }
    reallocate_payments($invoice_no, ST_SALESINVOICE, $date_, $to_allocate, $allocs, $invoice->customer_id);
    hook_db_postwrite($invoice, ST_SALESINVOICE);

    //commit_transaction();


    if (isset($old_invoice) && !empty($old_invoice) && $old_invoice['payment_flag'] == 2) {

        $sql = "UPDATE 0_debtor_trans SET alloc=0  
                        WHERE trans_no=$invoice_no AND type=" . ST_SALESINVOICE;
//        db_query($sql, "Error Updating ov_amount+gst=alloc");

    }

    if ($payment_flag == 2) {
        //SET ALLOC=(OV_AMOUNT+OV_GST) to avoid invoice for customer payment
//        $sql = "UPDATE 0_debtor_trans SET alloc=ov_amount,customer_card_amount=$inv_trans_total
//                        WHERE trans_no=$invoice_no AND type=" . ST_SALESINVOICE;
//        db_query($sql, "Error Updating ov_amount+gst=alloc");
//        add_cust_allocation(isset($receivables_amount) ? $receivables_amount : 0, ST_CUSTPAYMENT, (rand(-1, -999999999) + $invoice_no), ST_SALESINVOICE, $invoice_no, $invoice->customer_id, $date_);
    }

    $sql = "UPDATE 0_debtor_trans SET inv_total = " . ($inv_trans_total - $total_disc_amount) . " WHERE trans_no=$invoice_no AND type=" . ST_SALESINVOICE;
    db_query($sql);

    //is_tadbeer
    if ($payment_flag == INV_TADBEER_CC) {
        //SET ALLOC=(OV_AMOUNT+OV_GST) to avoid invoice for customer payment
        $sql = "UPDATE 0_debtor_trans SET alloc=$tadbeer_customer_card_amount,customer_card_amount=$customer_card_amount_total
                        WHERE trans_no=$invoice_no AND type=" . ST_SALESINVOICE;
        db_query($sql, "Error Updating ov_amount+gst=alloc");
//        add_cust_allocation(isset($tadbeer_customer_card_amount) ? $tadbeer_customer_card_amount : 0, ST_CUSTPAYMENT, (rand(-1, -999999999) + $invoice_no), ST_SALESINVOICE, $invoice_no, $invoice->customer_id, $date_);
    }

    runAutomaticAllocation($invoice->customer_id);

    commit_transaction();

    return $invoice_no;
}

//--------------------------------------------------------------------------------------------------

function void_sales_invoice($type, $type_no)
{


    begin_transaction();

    hook_db_prevoid($type, $type_no);
    void_bank_trans($type, $type_no, true);
    void_gl_trans($type, $type_no, true);

    // reverse all the changes in parent document(s)
    $factor = get_cust_prepayment_invoice_factor($type_no);
    if ($factor != 0) {
        $lines = get_customer_trans_details($type, $type_no);
        while ($line = db_fetch($lines)) {
            before_update_prepaid_so_line($line['src_id']);
            update_prepaid_so_line($line['src_id'], -$factor * $line['quantity']);
        }
    } else {
        $deliveries = get_sales_parent_numbers($type, $type_no);

        if ($deliveries !== 0) {
            if ($type == ST_SALESINVOICE && count($deliveries) == 1 && get_reference(ST_CUSTDELIVERY, $deliveries[0]) == "auto") {
                void_sales_delivery(ST_CUSTDELIVERY, $deliveries[0], false);
                $date_ = Today();
                add_audit_trail(ST_CUSTDELIVERY, $deliveries[0], $date_, trans("Voided."));
                add_voided_entry(ST_CUSTDELIVERY, $deliveries[0], $date_, "");
            } else {
                $srcdetails = get_sales_parent_lines($type, $type_no);
                while ($row = db_fetch($srcdetails)) {
                    before_update_parent_line($row['id']);
                    update_parent_line($type, $row['id'], -$row['quantity']);
                }
            }
        }
    }
    // clear details after they've been reversed in the sales order
    void_customer_trans_details($type, $type_no);

    void_stock_move($type, $type_no); // in case of credit note with return

    void_trans_tax_details($type, $type_no);

    void_cust_allocations($type, $type_no);

    // do this last because other voidings can depend on it - especially voiding
    // DO NOT MOVE THIS ABOVE VOIDING or we can end up with trans with alloc < 0
    void_customer_trans($type, $type_no);

    commit_transaction();
}

function is_cust_invoice_credited($trans_no)
{
    return db_num_rows(get_sales_child_lines(ST_SALESINVOICE, $trans_no));
}

function get_cust_prepayment_invoice_factor($trans_no)
{
    $sql = "SELECT IF(dt.prep_amount>0, dt.prep_amount/so.total ,0) 
		FROM " . TB_PREF . "debtor_trans dt
		LEFT JOIN " . TB_PREF . "sales_orders so ON so.trans_type=" . ST_SALESORDER . " AND so.order_no=dt.order_
		 WHERE dt.type=" . ST_SALESINVOICE . " AND trans_no=" . db_escape($trans_no);
    $row = db_fetch(db_query($sql, 'cannot retrieve prepaid invoice factor'));
    return $row[0];
}

/*
    Return yet not invoiced amount of prepayment invoice (or 0 if normal invoice)
*/
function prepaid_invoice_remainder($order)
{
    $sql = "SELECT so.total - IFNULL(SUM(inv.prep_amount),0) FROM "
        . TB_PREF . "sales_orders so,
		" . TB_PREF . "debtor_trans inv,
		" . TB_PREF . "payment_terms pt
		WHERE  so.order_no=" . db_escape($order)
        . " AND so.trans_type=" . ST_SALESORDER
        . " AND inv.type=" . ST_SALESINVOICE
        . " AND inv.order_=so.order_no"
        . " AND so.payment_terms=pt.terms_indicator"
        . " AND inv.payment_terms=pt.terms_indicator"
        . " AND pt.days_before_due = -1";

    $result = db_fetch(db_query($sql, "cannot find final invoice value"));
    return $result[0] ? $result[0] : 0;
}


/*
    Find oldest delivery date for sales invoice
*/
function get_oldest_delivery_date($invoice_no)
{
    $sql = "SELECT MIN(trans.tran_date)
			FROM
				" . TB_PREF . "debtor_trans_details del
			LEFT JOIN " . TB_PREF . "debtor_trans_details inv
				ON inv.src_id=del.id
			LEFT JOIN " . TB_PREF . "debtor_trans trans 
				ON trans.type=" . ST_CUSTDELIVERY . " AND trans.trans_no = del.debtor_trans_no
			WHERE
				inv.debtor_trans_type=" . ST_SALESINVOICE
        . " AND inv.debtor_trans_no=" . db_escape($invoice_no);
    $res = db_query($sql, 'cannot find oldest delivery date');
    $date = db_fetch($res);
    return $date[0];
}

/*
    Find oldest payment date for sales invoice
*/
function get_oldest_payment_date($invoice_no)
{
    $sql = "SELECT MIN(payment.tran_date)
			FROM
			" . TB_PREF . "cust_allocations alloc,
			" . TB_PREF . "debtor_trans payment
			WHERE
				alloc.trans_type_to=" . ST_SALESINVOICE . " AND alloc.trans_no_to=" . db_escape($invoice_no)
        . " AND alloc.trans_type_from=payment.type AND alloc.trans_no_from=payment.trans_no";
    $res = db_query($sql, 'cannot find oldest delivery date');
    $date = db_fetch($res);
    return $date[0];
}
