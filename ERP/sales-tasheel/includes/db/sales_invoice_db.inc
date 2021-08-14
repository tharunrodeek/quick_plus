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
//-----------------------------------------------------------------------------
//	Add or update Sales Invoice
//
function write_sales_invoice(&$invoice)
{

    global $Refs;


    $trans_no = $invoice->trans_no;
    if (is_array($trans_no))
        $trans_no = key($trans_no);

    $date_ = $invoice->document_date;
    $charge_shipping = $invoice->freight_cost;

    begin_transaction();

    hook_db_prewrite($invoice, ST_SALESINVOICE);
    $company_data = get_company_prefs();

    $branch_data = get_branch_accounts($invoice->Branch);

    $customer = get_customer($invoice->customer_id);

    add_new_exchange_rate($customer['curr_code'], $date_, $invoice->ex_rate);

    // offer price values without freight costs
    $items_total = $invoice->get_items_total_dispatch();

    $inv_trans_total = $invoice->get_items_trans_total();

    $freight_tax = $invoice->get_shipping_tax();

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
        $invoice->prep_amount,$inv_trans_total);

    if ($trans_no == 0) {
        $invoice->trans_no = array($invoice_no => 0);
    } else
        move_trans_attachments(ST_SALESINVOICE, $trans_no, $invoice_no);

    $total = 0;

    $govt_fee_total = 0;
    $bank_charges_total = 0;
    $bank_charges_vat_total = 0;
    $pf_amount_total = 0;

    // for prepayments use deferred income account if set
    $sales_account = $invoice->is_prepaid() ? get_company_pref('deferred_income_act') : 0;

    foreach ($invoice->line_items as $line_no => $invoice_line) {
        $qty = $invoice_line->qty_dispatched;
        $line_taxfree_price = get_tax_free_price_for_item($invoice_line->stock_id,
            $invoice_line->price * $qty, 0, $invoice->tax_included,
            $invoice->tax_group_array);

        $line_tax = get_full_price_for_item($invoice_line->stock_id,
                $invoice_line->price * $qty, 0, $invoice->tax_included,
                $invoice->tax_group_array) - $line_taxfree_price;




        add_customer_reward_points($invoice_no,$date_,$invoice->customer_id, $invoice_line->stock_id,$qty);



        write_customer_trans_detail_item(ST_SALESINVOICE, $invoice_no, $invoice_line->stock_id,
            $invoice_line->item_description, $invoice_line->qty_dispatched,
            $invoice_line->line_price(), $qty ? $line_tax / $qty : 0, $invoice_line->discount_percent,
            $invoice_line->standard_cost, $invoice_line->src_id,
            $trans_no ? $invoice_line->id : 0,
            $invoice_line->govt_fee,$invoice_line->bank_service_charge,
            $invoice_line->bank_service_charge_vat,$invoice_line->transaction_id,$invoice_line->discount_amount);


        if(isset($_SESSION["invoiced_by"]))
            unset($_SESSION["invoiced_by"]);

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
                    $sales_account = ($branch_data['sales_account'] != "" ? $branch_data['sales_account'] : $stock_gl_code['sales_account']);
                // If there is a Customer Dimension, then override with this,
                // else take the Item Dimension (if any)
                $dim = ($invoice->dimension_id != $customer['dimension_id'] ? $invoice->dimension_id :
                    ($customer['dimension_id'] != 0 ? $customer["dimension_id"] : $stock_gl_code["dimension_id"]));
                $dim2 = ($invoice->dimension2_id != $customer['dimension2_id'] ? $invoice->dimension2_id :
                    ($customer['dimension2_id'] != 0 ? $customer["dimension2_id"] : $stock_gl_code["dimension2_id"]));


                /** Modifications for AMER, new GL Account entries */

                $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $sales_account, $dim, $dim2,
                    -($line_taxfree_price - ($invoice_line->pf_amount*$qty)) * $prepaid_factor,
                    $invoice->customer_id, "The sales price GL posting could not be inserted");

                $transaction_id = $invoice_line->transaction_id ? $invoice_line->transaction_id : "N/A";

                //Posting GL account entries for government charges
                if (!empty($invoice_line->govt_fee)) {
                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $invoice_line->govt_bank_account, $dim, $dim2,
                        -($invoice_line->govt_fee * $prepaid_factor)*$qty,
                        $invoice->customer_id, "The sales price GL posting could not be i