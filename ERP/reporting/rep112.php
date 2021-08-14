<?php
/**********************************************************************
    Direct Axis Technology L.L.C.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Receipts
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

//----------------------------------------------------------------------------------------------------

print_receipts();

//----------------------------------------------------------------------------------------------------
function get_receipt($type, $trans_no,$voided=false)
{

//    display_error(print_r($trans_no ,true));
	if($voided){
		$sql = "SELECT trans.*,
				(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax) AS Total,
				trans.ov_discount, 
				debtor.name AS DebtorName,
				debtor.debtor_ref, 
				(select display_customer from 0_voided_debtor_trans where trans_no=(select trans_no_to from 0_voided_cust_allocations where trans_no_from = $trans_no and trans_type_from=12 and trans_type_to = 10 limit 1) and type=10) As display_customer,
  (select customer_mobile from 0_voided_debtor_trans where trans_no=(select trans_no_to from 0_voided_cust_allocations where trans_no_from = $trans_no and trans_type_from=12 limit 1) and type=10) As customer_mobile,
  (select customer_trn from 0_voided_debtor_trans where trans_no=(select trans_no_to from 0_voided_cust_allocations where trans_no_from = $trans_no and trans_type_from=12 limit 1) and type=10) As customer_trn,
   				debtor.curr_code,
   				debtor.payment_terms,
   				debtor.tax_id AS tax_id,
   				debtor.address
    			FROM ".TB_PREF."voided_debtor_trans trans,"
    				.TB_PREF."debtors_master debtor
				WHERE trans.debtor_no = debtor.debtor_no
				AND trans.type = ".db_escape($type)."
				AND trans.trans_no = ".db_escape($trans_no);
	}else{

    $sql = "SELECT trans.*,
				(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax) AS Total,
				trans.ov_discount, 
				debtor.name AS DebtorName,
				debtor.debtor_ref, 
				(select display_customer from 0_debtor_trans where trans_no=(select trans_no_to from 0_cust_allocations where trans_no_from = $trans_no and trans_type_from=12 and trans_type_to = 10 limit 1) and type=10) As display_customer,
  (select customer_mobile from 0_debtor_trans where trans_no=(select trans_no_to from 0_cust_allocations where trans_no_from = $trans_no and trans_type_from=12 limit 1) and type=10) As customer_mobile,
  (select customer_trn from 0_debtor_trans where trans_no=(select trans_no_to from 0_cust_allocations where trans_no_from = $trans_no and trans_type_from=12 limit 1) and type=10) As customer_trn,
   				debtor.curr_code,
   				debtor.payment_terms,
   				debtor.tax_id AS tax_id,
   				debtor.address
    			FROM ".TB_PREF."debtor_trans trans,"
    				.TB_PREF."debtors_master debtor
				WHERE trans.debtor_no = debtor.debtor_no
				AND trans.type = ".db_escape($type)."
				AND trans.trans_no = ".db_escape($trans_no);
	}
   	$result = db_query($sql, "The remittance cannot be retrieved");
   	if (db_num_rows($result) == 0)
   		return false;
    return db_fetch($result);
}

function print_receipts()
{



    global $path_to_root, $systypes_array;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$comments = $_POST['PARAM_3'];
	$orientation = $_POST['PARAM_4'];
	$voided = $_POST['PARAM_5'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

 	$fno = explode("-", $from);
	$tno = explode("-", $to);
	$from = min($fno[0], $tno[0]);
	$to = max($fno[0], $tno[0]);

	$cols = array(4, 85, 150, 225, 275, 360, 450, 515);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'left', 'right', 'right', 'right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	$rep = new FrontReport($voided ? trans("VOIDED RECEIPT") : trans("RECEIPT"), "ReceiptBulk", user_pagesize(), 9, $orientation);
   	if ($orientation == 'L')
    	recalculate_cols($cols);
	$rep->currency = $cur;
	$rep->Font();
	$rep->Info($params, $cols, null, $aligns);
	// $rep->title = $voided ? trans("VOIDED RECEIPT") : trans("RECEIPT");

	for ($i = $from; $i <= $to; $i++)
	{
		if ($fno[0] == $tno[0])
			$types = array($fno[1]);
		else
			$types = array(ST_BANKDEPOSIT, ST_CUSTPAYMENT);
		foreach ($types as $j)
		{
			$myrow = get_receipt($j, $i,$voided);



			if (!$myrow)
				continue;
			if ($currency != ALL_TEXT && $myrow['curr_code'] != $currency) {
				continue;
			}
			$res = get_bank_trans($j, $i, null, null, $voided);




			$baccount = db_fetch($res);


			$params['bankaccount'] = $baccount['bank_act'];




            $contacts = get_branch_contacts($myrow['branch_code'], 'invoice', $myrow['debtor_no']);




            $rep->SetCommonData($myrow, null, $myrow, $baccount, ST_CUSTPAYMENT, $contacts);



 			$rep->SetHeaderType('Header2');



			$rep->NewPage();
//



             // get_allocatable_to_cust_transactions($customer_id = null, $trans_no=null, $type=null,$barcode=null,$invoice_from_date=null,$invoice_to_date=null,$dimension_id = null,$voided=false)
            // $result = get_allocatable_to_cust_transactions($myrow['debtor_no'], $myrow['trans_no'], $myrow['type'],null,null,null,null);
            $result = get_allocatable_to_cust_transactions($myrow['debtor_no'], $myrow['trans_no'], $myrow['type'],null,null,null,null,$voided);

			$doctype = ST_CUSTPAYMENT;

			$total_allocated = 0;
//			$rep->TextCol(0, 4,	trans("As advance / full / part / payment towards:"), -2);
//			$rep->NewLine(2);

            $sum_outstanding=0;

			while ($myrow2=db_fetch($result))
			{
				$rep->TextCol(0, 1,	ltrim($systypes_array[$myrow2['type']],'Sales'), -2);
				$rep->TextCol(1, 2,	$myrow2['reference'], -2);
				$rep->TextCol(2, 3,	sql2date($myrow2['tran_date']), -2);
				$rep->TextCol(3, 4,	sql2date($myrow2['due_date']), -2);
				$rep->AmountCol(4, 5, $myrow2['Total'], $dec, -2);
				$rep->AmountCol(5, 6, $myrow2['Total'] - $myrow2['alloc'], $dec, -2);
				$rep->AmountCol(6, 7, $myrow2['amt'], $dec, -2);

				$total_allocated += $myrow2['amt'];
				$rep->NewLine(1);
				if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
					$rep->NewPage();

                $sum_outstanding += $myrow2['Total'] - $myrow2['alloc'];

			}

			$memo = get_comments_string($j, $i);
			if ($memo != "")
			{
				$rep->NewLine();
				$rep->TextColLines(1, 5, $memo, -2);
			}
			is_voided_display_in_report($j,$i, trans("This transaction has been voided."),$rep,1,5);
			$rep->row = $rep->bottomMargin + (20 * $rep->lineHeight);



            $myrow['Total'] = $myrow['Total']+$myrow['credit_card_charge'];

            $rep->NewLine();
            $rep->Font('bold');

            if($myrow['payment_method']) {
                $pay_method = $myrow['payment_method'];
                $rep->TextCol(3, 6, trans("TOTAL RECEIPT AMOUNT( Pay.Method:$pay_method )"), - 2);
            }else {
                $rep->TextCol(3, 6, trans("TOTAL RECEIPT AMOUNT"), - 2);
            }
            $rep->AmountCol(6, 7, $myrow['Total']+$myrow['round_of_amount'], $dec, -2);

            $rep->NewLine();
            $rep->Font('bold');

            $rep->TextCol(3, 6, trans("Allocated Amount"), -2);
            $rep->AmountCol(6, 7, $total_allocated, $dec, -2);




//            if ($myrow['credit_card_charge']) {
//                $rep->NewLine();
//                $rep->TextCol(3, 6, trans("Payment Method"), -2);
//                $rep->TextCol(6, 7, $myrow['payment_method']);
//            }


            $myrow['show_bank_charge'] = 1;

            if( $myrow['show_bank_charge'] == 0)
                $myrow['credit_card_charge'] = 0; //IF DO NOT WANT TO SHOW BANK CHARGE

            if ($myrow['credit_card_charge']) {

                $rep->NewLine();
                $rep->TextCol(3, 6, trans("Card Charge"), -2);
                $rep->AmountCol(6, 7, $myrow['credit_card_charge'], $dec, -2);
            }

            if ($myrow['round_of_amount']) {

                $rep->NewLine();
                $rep->TextCol(3, 6, trans("Round of Amount"), -2);
                $rep->AmountCol(6, 7, $myrow['round_of_amount'], $dec, -2);
            }


			$rep->NewLine();
			$rep->TextCol(3, 6, trans("Balance"), -2);
			$rep->AmountCol(6, 7, $myrow['Total'] + $myrow['ov_discount'] - $total_allocated-$myrow['credit_card_charge'], $dec, -2);
			if (floatcmp($myrow['ov_discount'], 0))
			{
				$rep->NewLine();
				$rep->TextCol(3, 6, trans("Discount"), - 2);
				$rep->AmountCol(6, 7, -$myrow['ov_discount'], $dec, -2);
			}

            $rep->NewLine();
            $rep->NewLine();
            $rep->TextCol(3, 6, trans("Total Invoice Outstanding"), -2);
            $rep->AmountCol(6, 7, $sum_outstanding, $dec, -2);





			$words = price_in_words($myrow['Total'], ST_CUSTPAYMENT);
            $rep->NewLine();

			if ($words != "")
			{
				$rep->NewLine(2);
				$rep->TextCol(0, 7, $myrow['curr_code'] . ": " . $words, - 2);
			}
			$rep->Font();


//            $rep->NewLine();
			$rep->TextCol(6, 7, trans($_SESSION['wa_current_user']->name), - 2);
            $rep->NewLine();
			$rep->TextCol(6, 7, trans("Received/Sign"), - 2);
			$rep->NewLine();
//			$rep->TextCol(0, 2, trans("By Cash / Cheque* / Draft No."), - 2);
//			$rep->TextCol(2, 4, "______________________________", - 2);
//			$rep->TextCol(4, 5, trans("Dated"), - 2);
//			$rep->TextCol(5, 6, "__________________", - 2);
//			$rep->NewLine(1);
//			$rep->TextCol(0, 2, trans("Drawn on Bank"), - 2);
//			$rep->TextCol(2, 4, "______________________________", - 2);
//			$rep->TextCol(4, 5, trans("Branch"), - 2);
//			$rep->TextCol(5, 6, "__________________", - 2);
//			$rep->TextCol(6, 7, "__________________");
		}
	}
	$rep->End();
}

