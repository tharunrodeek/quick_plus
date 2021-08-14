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
$page_security = 'SA_BANKTRANSVIEW';
$path_to_root = "../..";

include($path_to_root . "/includes/session.inc");

page(trans($help_context = "View Bank Deposit"), true);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/gl/includes/gl_db.inc");

if (isset($_GET["trans_no"]))
{
	$trans_no = $_GET["trans_no"];
}

// get the pay-to bank payment info
$result = get_bank_trans(ST_BANKDEPOSIT, $trans_no);

if (db_num_rows($result) != 1)
	display_db_error("duplicate payment bank transaction found", "");

$to_trans = db_fetch($result);

$company_currency = get_company_currency();

$show_currencies = false;

if ($to_trans['bank_curr_code'] != $to_trans['settle_curr'])
{
	$show_currencies = true;
}

echo "<center>";

display_heading(trans("GL Deposit") . " #$trans_no");

echo "<br>";
start_table(TABLESTYLE, "width='80%'");

if ($show_currencies)
{
	$colspan1 = 1;
	$colspan2 = 7;
}
else
{
	$colspan1 = 3;
	$colspan2 = 5;
}
start_row();
label_cells(trans("To Bank Account"), $to_trans['bank_account_name'], "class='tableheader2'");
if ($show_currencies)
	label_cells(trans("Currency"), $to_trans['bank_curr_code'], "class='tableheader2'");
label_cells(trans("Amount"), number_format2($to_trans['amount'], user_price_dec()), "class='tableheader2'", "align=right");
label_cells(trans("Date"), sql2date($to_trans['trans_date']), "class='tableheader2'");
end_row();
start_row();
label_cells(trans("From"), get_counterparty_name(ST_BANKDEPOSIT, $to_trans['trans_no']), "class='tableheader2'", "colspan=$colspan1");
if ($show_currencies)
{
	label_cells(trans("Settle currency"), $to_trans['settle_curr'], "class='tableheader2'");
	label_cells(trans("Settled amount"),  number_format2($to_trans['settled_amount'], user_price_dec()), "class='tableheader2'");
}
label_cells(trans("Deposit Type"), $bank_transfer_types[$to_trans['account_type']], "class='tableheader2'");
end_row();
start_row();
label_cells(trans("Reference"), $to_trans['ref'], "class='tableheader2'", "colspan=$colspan2");
end_row();
comments_display_row(ST_BANKDEPOSIT, $trans_no);

end_table(1);

is_voided_display(ST_BANKDEPOSIT, $trans_no, trans("This deposit has been voided."));

$items = get_gl_trans(ST_BANKDEPOSIT, $trans_no);

if (db_num_rows($items) == 0)
{
	display_note(trans("There are no items for this deposit."));
}
else
{

	display_heading2(trans("Items for this Deposit"));
	if ($show_currencies)
		display_heading2(trans("Item Amounts are Shown in:") . " " . $company_currency);

    start_table(TABLESTYLE, "width='80%'");
    $dim = get_company_pref('use_dimension');
    if ($dim == 2)
        $th = array(trans("Account Code"), trans("Account Description"), trans("Dimension")." 1", trans("Dimension")." 2",
            trans("Amount"), trans("Memo"));
    elseif ($dim == 1)
        $th = array(trans("Account Code"), trans("Account Description"), trans("Dimension"),
            trans("Amount"), trans("Memo"));
    else
        $th = array(trans("Account Code"), trans("Account Description"),
            trans("Amount"), trans("Memo"));
    table_header($th);

    $k = 0; //row colour counter
	$total_amount = 0;

    while ($item = db_fetch($items))
    {

		if ($item["account"] != $to_trans["account_code"])
		{
    		alt_table_row_color($k);

        	label_cell($item["account"]);
    		label_cell($item["account_name"]);
            if ($dim >= 1)
                label_cell(get_dimension_string($item['dimension_id'], true));
            if ($dim > 1)
                label_cell(get_dimension_string($item['dimension2_id'], true));
            amount_cell(-$item["amount"]);
    		label_cell($item["memo_"]);
    		end_row();
    		$total_amount += $item["amount"];
		}
	}

	label_row(trans("Total"), number_format2(-$total_amount, user_price_dec()),"colspan=".(2+$dim)." align=right", "align=right");

	end_table(1);

	display_allocations_from($to_trans['person_type_id'], $to_trans['person_id'], 2, $trans_no, $to_trans['settled_amount']);
}

end_page(true, false, false, ST_BANKDEPOSIT, $trans_no);
