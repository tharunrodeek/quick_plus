<?php

date_default_timezone_set('Asia/Dubai');
$current_username = $_SESSION['wa_current_user']->username;

function get_subledger_name($sub_acc_code)
{
    $sql = "Select * from 0_sub_ledgers where code = ".db_escape($sub_acc_code);
    $sub_lgr = db_fetch(db_query($sql));
    return $sub_lgr['name'];
}

function get_username($user_id)
{
    $sql="Select real_name FROM 0_users WHERE id='".$user_id."'";
    $res=db_query($sql);
    $data=db_fetch($res);
    return $data[0];
}

function get_voucher_from_acc_details($type, $trans_id) {

    $sql = "SELECT b.bank_account_name FROM 
            0_bank_trans AS a
            inner join 0_bank_accounts as b ON a.bank_act=b.id
            WHERE a.type='".$type."' and trans_no='".$trans_id."' ";

   return db_fetch(db_query($sql, "Cant retrieve voucher"));
}

function get_account_name($acc_code)
{
    $sql = "SELECT c.account_code,c.account_name FROM 
             0_chart_master AS c   
            WHERE c.account_code='".$acc_code."'";

    return db_fetch(db_query($sql, "Cant retrieve voucher"));
}


function get_being($type,$trans_id)
{
    $sql = " SELECT memo_ from 0_comments
            WHERE type='".$type."' and id='".$trans_id."' ";

    return db_fetch(db_query($sql, "Cant retrieve voucher"));
}

function get_gl_trans_on_page($type, $trans_id)
{
    $sql = "SELECT gl.*, cm.account_name, IFNULL(refs.reference, '') AS reference, user.real_name, 
			COALESCE(st.tran_date, dt.tran_date, bt.trans_date, grn.delivery_date, gl.tran_date) as doc_date,
			IF(ISNULL(st.supp_reference), '', st.supp_reference) AS supp_reference,bt.created_by as biii
	FROM ".TB_PREF."gl_trans as gl
		LEFT JOIN ".TB_PREF."chart_master as cm ON gl.account = cm.account_code
		LEFT JOIN ".TB_PREF."refs as refs ON (gl.type=refs.type AND gl.type_no=refs.id)
		LEFT JOIN ".TB_PREF."audit_trail as audit ON (gl.type=audit.type AND gl.type_no=audit.trans_no AND NOT ISNULL(gl_seq))
		LEFT JOIN ".TB_PREF."users as user ON (audit.user=user.id)
		LEFT JOIN ".TB_PREF."supp_trans st ON gl.type_no=st.trans_no AND st.type=gl.type AND (gl.type!=".ST_JOURNAL." OR gl.person_id=st.supplier_id)
		LEFT JOIN ".TB_PREF."grn_batch grn ON grn.id=gl.type_no AND gl.type=".ST_SUPPRECEIVE." AND gl.person_id=grn.supplier_id
		LEFT JOIN ".TB_PREF."debtor_trans dt ON gl.type_no=dt.trans_no AND dt.type=gl.type AND (gl.type!=".ST_JOURNAL." OR gl.person_id=dt.debtor_no)
		LEFT JOIN ".TB_PREF."bank_trans bt ON bt.type=gl.type AND bt.trans_no=gl.type_no AND bt.amount!=0
			 AND bt.person_type_id=gl.person_type_id AND bt.person_id=gl.person_id
		LEFT JOIN ".TB_PREF."journal j ON j.type=gl.type AND j.trans_no=gl.type_no"

        ." WHERE gl.type= ".db_escape($type)
        ." AND gl.type_no = ".db_escape($trans_id)
        ." AND gl.amount <> 0 ";
    if($type=='2')
    {
        $sql.=" and gl.amount<0 ";
    }
    else
    {
        $sql.=" and gl.amount>0 ";
    }
    $sql.="  
          ORDER BY tran_date, counter";
//return $sql;
 return db_query($sql, "The gl transactions could not be retrieved");
}

$from = $_GET['PARAM'];

$fno = explode("-", $from);
$from = $fno[1];
$to = $fno[0];
//$dec = user_price_dec();
$result = get_gl_trans($from,$to);

$sign = 1;




if($row[2]!='')
{
    $create_by=get_created_by($row[2]);
}

$line_item_tr = "";
$total_tax = 0;
$total_before_tax = 0;
$i = 1;
$created_by = "";

$disc_amt = 0;
$total_qty_deliverynote='0';

$created_at_date_time = null;
$result = get_gl_trans_on_page($from, $to);

//echo $result;
$attach='';
$refernce_no='';
$voucher_date='';
$created_by='';
$memo='';
$tot='';
while ($myrow2 = db_fetch($result)) {
    $refernce_no=$myrow2['reference'];
    $voucher_date=date('m-d-Y',strtotime($myrow2["doc_date"]));
    // if($fno[1]=='10')
    // {
    $counterpartyname = get_subaccount_name($myrow2["account"], $myrow2["person_id"]);
    $sub_ledger_name = get_subledger_name($myrow2['axispro_subledger_code']);
    $account_name=get_account_name($myrow2['account_code']);

    if(!empty($sub_ledger_name))
    {
        $attach= $sub_ledger_name;
    }
    else
    {
        $attach='';
    }

    if ($myrow2['amount'] > 0 )
    {
        $debit = $myrow2['amount'];
        $credit='';
        $tot=$tot+$debit;
    }
   if($myrow2['amount'] < 0)
    {
        $credit =  $myrow2['amount'];
        $debit='';
        $tot=$tot+$credit;
    }

    $accoun= $myrow2['account'].' - '.$myrow2['account_name'] . ($counterpartyname ? ': '.$counterpartyname : '').'<br/>'.$attach;
    $line_item_tr .=
        "<tr class='item'>
         
            <td class='arabic' style='float:left;width:20%;border: 1px solid black;'>" .$accoun. "</td>
           <td class='right' style='width:60%;text-align:left;border: 1px solid black;' >".$myrow2['memo_']."</td>";
    if($fno[1]=='1')
    {
        $line_item_tr .=
            " <td class='right' style='text-align:right;border: 1px solid black;'>".number_format($debit,2)."</td>
           </tr>";
    }

    if($fno[1]=='2')
    {
        $line_item_tr .=
            " <td class='right' style='text-align:right;border: 1px solid black;'>".number_format(str_replace("-","",$credit),2)."</td>
           </tr>";
    }

    if($fno[1]=='0')
    {
        $line_item_tr .=
            " <td class='right' style='text-align:right;border: 1px solid black;'>".number_format($debit,2)."</td>
           </tr>";
    }


    $i++;

    //}

}


$paidto_rcvd_to_label = $myrow['voucher_type'] == "PV" ? "Paid To:" : "Received From:";
if($fno[1]=='0') {
  $pv_rv_label = "J.V.No:";
}
if($fno[1]=='1') {
$pv_rv_label = "Voucher No.:";
$paidto_rcvd_to_label="Paid To:";
}
if($fno[1]=='2') {
  $pv_rv_label = "Voucher.No:";
  $paidto_rcvd_to_label="Received From:";
}

$person_types=array('0'=>'Miscellaneous','2'=>'Customer','3'=>'Supplier');
$payment_types=array('1'=>'Cash','2'=>'Cheque','3'=>'Transfer');

$person_type='';
$recived_from='';
$remarks='';
$chck_no='';
$chk_date='';
$amount='';
$payment_type='';


if($fno[1]!='0')
{
    $sql_voucher_details = "Select * from 0_bank_trans where `type`='".$from."' and trans_no='".$to."' ";
    $res_data = db_fetch(db_query($sql_voucher_details));


    $person_type=$person_types[$res_data['person_type_id']];
    $recived_from='';
    $remarks='';
    $chck_no=$res_data['cheq_no'];
    if($res_data['cheq_date']!='0000-00-00')
    {
        $chk_date=date('m-d-Y',strtotime($res_data['cheq_date']));
    }
    $amount=abs($res_data['amount']);
    $payment_type=$payment_types[$res_data['payment_type']];


        if($res_data['person_type_id']=='0')
        {
            $person_id='Miscellaneous';
        }

        if($res_data['person_type_id']=='2')
        {
            $sql_qry = "Select * from 0_debtors_master where debtor_no='".$res_data['name']."'  ";
            $res_qry = db_fetch(db_query($sql_qry));
            $person_id=$res_qry['name'];
        }

        if($res_data['person_type_id']=='3')
        {
            $sql_qry = "Select * from 0_suppliers where supplier_id='".$res_data['name']."'  ";
            $res_qry = db_fetch(db_query($sql_qry));
            $person_id=$res_qry['supp_name'];

        }

    $created_by=get_username($res_data["created_by"]);
}
else
{
    $sql_journal = "Select * from 0_journal where `type`='".$from."' and trans_no='".$to."' ";
    $jour_data = db_fetch(db_query($sql_journal));

    $sql_jl = "Select * from 0_gl_trans where `type`='".$from."' and type_no='".$to."' ";
    $gl_data = db_fetch(db_query($sql_jl));

    $created_by=get_username($gl_data["created_by"]);
}






$comment_sql="Select memo_ FROm 0_comments where type='".$from."' and id='".$to."' ";
$data=db_fetch(db_query($comment_sql));
$commnet='';
if($data['memo_']!='')
{
    $commnet=$data['memo_'];
}

$from_account=get_voucher_from_acc_details($from, $to);

$get_being=get_being($from, $to);

 //print_r($get_being);

$pay_label='';
if($fno[1]=='2')
{
    $get_pay_type="select payment_type from 0_bank_trans where trans_no='".$fno[0]."' and type='".$fno[1]."'";
    $data_paytype=db_fetch(db_query($get_pay_type));

    if($data_paytype['payment_type']=='1')
    {
        $pay_label='Cash';
    }

    if($data_paytype['payment_type']=='2')
    {
        $pay_label='Cheque';
    }

    if($data_paytype['payment_type']=='3')
    {
        $pay_label='Transfer';
    }
}



 function numberTowords($num)
    {
        $ones = array(
            0 =>"ZERO",
            1 => "ONE",
            2 => "TWO",
            3 => "THREE",
            4 => "FOUR",
            5 => "FIVE",
            6 => "SIX",
            7 => "SEVEN",
            8 => "EIGHT",
            9 => "NINE",
            10 => "TEN",
            11 => "ELEVEN",
            12 => "TWELVE",
            13 => "THIRTEEN",
            14 => "FOURTEEN",
            15 => "FIFTEEN",
            16 => "SIXTEEN",
            17 => "SEVENTEEN",
            18 => "EIGHTEEN",
            19 => "NINETEEN",
            "014" => "FOURTEEN"
        );
        $tens = array(
            0 => "ZERO",
            1 => "TEN",
            2 => "TWENTY",
            3 => "THIRTY",
            4 => "FORTY",
            5 => "FIFTY",
            6 => "SIXTY",
            7 => "SEVENTY",
            8 => "EIGHTY",
            9 => "NINETY"
        );
        $hundreds = array(
            "HUNDRED",
            "THOUSAND",
            "MILLION",
            "BILLION",
            "TRILLION",
            "QUARDRILLION"
        );
        $num = number_format($num,2,".",",");
        $num_arr = explode(".",$num);
        $wholenum = $num_arr[0];
        $decnum = $num_arr[1];
        $whole_arr = array_reverse(explode(",",$wholenum));
        krsort($whole_arr,1);
        $rettxt = "";
        foreach($whole_arr as $key => $i){

            while(substr($i,0,1)=="0")
                $i=substr($i,1,5);
            if($i < 20){
                /* echo "getting:".$i; */
               $rettxt .= $ones[$i];
            }elseif($i < 100){
                if(substr($i,0,1)!="0")  $rettxt .= $tens[substr($i,0,1)];
                if(substr($i,1,1)!="0") $rettxt .= " ".$ones[substr($i,1,1)];
            }else{
                if(substr($i,0,1)!="0") $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0];
                if(substr($i,1,1)!="0")$rettxt .= " ".$tens[substr($i,1,1)];
                if(substr($i,2,1)!="0")$rettxt .= " ".$ones[substr($i,2,1)];
            }
            if($key > 0){
                $rettxt .= " ".$hundreds[$key]." ";
            }
        }
        if($decnum > 0){
            $rettxt .= " and ";
            if($decnum < 20){
                $rettxt .= $ones[$decnum];
            }elseif($decnum < 100){
                $rettxt .= $tens[substr($decnum,0,1)];
                $rettxt .= " ".$ones[substr($decnum,1,1)];
            }
        }
        return $rettxt;
    }

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Print</title>
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            /* padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            */
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
           /* padding: 5px;*/
            vertical-align: top;
        }

        /*.invoice-box table tr td:nth-child(2) {*/
        /*text-align: right;*/
        /*}*/

        .invoice-box table tr.top table td {
            /*padding-bottom: 20px;*/
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            /*line-height: 45px;*/
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            /*padding-bottom: 20px;*/
        }

        .invoice-box table tr.item td{
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }


        #footer {
            clear: both;
            position: fixed;
            height: 100%;
            margin-top: 8%;
            width: 90%;
            margin-left: 30px;

        }
.dotted
{
    border-bottom: 1px dotted;
}

        .font-style
        {
            font-size:9pt;
        }

    </style>

</head>

<body>

<div class="invoice-box arabic">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="4">
                <table>
                    <tr>
                        <td class="title" style="text-align: center">
 <img src="<?= $path_to_root ?>/company/0/images/pdf-header-top.jpg" style="width:100%;">
                        </td>
                    </tr>
                    <tr class="top">
                        <td colspan="4">
                            <table>
                                <tr>
                                    <td class="title" style="text-align: center;">
                                        <?php if($fno[1]=='0') { ?>
                                            <img src="images/journal-voucher.jpg" style="width:100%; max-width: 250px;">
                                        <?php } ?>
                                        <?php if($fno[1]=='1') { ?>
                                            <img src="images/payment-voucher.jpg" style="width:100%; max-width: 250px; ">

                                        <?php } ?>
                                        <?php if($fno[1]=='2') { ?>
                                            <img src="images/receipt-voucher.jpg" style="width:100%; max-width: 250px;"><br/>
                                            <span style="font-size:12pt;"><?php echo $pay_label; ?></span>
                                        <?php } ?>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

   <!-- <table cellpadding="0" cellspacing="0" >
        <tr class="top">
            <td colspan="4">
                <table>
                    <tr>
                        <td class="title" style="text-align: center;">
                            <?php /*if($fno[1]=='0') { */?>
                                <img src="images/journal-voucher.jpg" style="width:100%; max-width: 250px;">
                            <?php /*} */?>
                            <?php /*if($fno[1]=='1') { */?>
                                <img src="images/payment-voucher.jpg" style="width:100%; max-width: 250px; ">
                            <?php /*} */?>
                            <?php /*if($fno[1]=='2') { */?>
                                <img src="images/receipt-voucher.jpg" style="width:100%; max-width: 250px;">
                            <?php /*} */?>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>-->


    <!--    <div style="text-align: center !important;">Payment Voucher</div>-->
    <div class="head-info" style=" width: 100%; margin-left: 0px; ">

        <!--<table>

            <?php /*if($fno[1]!='0')
            { */?>

            <tr>
                <td><?/*=$pv_rv_label*/?></td>
                <td style="text-align: left"><?/*= $refernce_no; */?></td>
                <td></td>
                <td>Cheque#:</td>
                <td style="text-align: left"><?/*= $chck_no; */?></td>
            </tr>
            <tr>
                <td>Date:</td>
                <td style="text-align: left"><?/*= $voucher_date; */?></td>
                <td></td>
                <td>Cheque Date:</td>
                <td><?/*= $chk_date; */?></td>
            </tr>

            <tr>
                <td>Type:</td>
                <td style="text-align: left"><?/*=  $person_type; */?></td>
                <td></td>
                <td>Amount:</td>
                <td><?/*= number_format($amount,2); */?></td>
            </tr>

            <tr>
                <td><?/*=$paidto_rcvd_to_label*/?></td>
                <td style="text-align: left"><?/*= $person_id; */?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: left"></td>
                <td></td>
                <td>Payment Type:</td>
                <td><?/*= $payment_type; */?></td>
            </tr>

            <tr>
                <td>Remarks:</td>
                <td style="text-align: left"><?/*= $commnet; */?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
         <?php /*} else { */?>
                <tr>
                    <td><?/*=$pv_rv_label*/?></td>
                    <td style="text-align: left"><?/*= $refernce_no; */?></td>
                    <td></td>
                    <td>Date#:</td>
                    <td style="text-align: left"><?/*= $voucher_date; */?></td>
                </tr>

                <tr>
                    <td>Amount:</td>
                    <td style="text-align: left"><?/*= number_format($jour_data['amount'],2); */?></td>
                    <td></td>
                    <td>Remarks:</td>
                    <td style="text-align: left"><?/*= $commnet; */?></td>
                </tr>
         <?php /*} */?>

        </table>-->

        <table>
            <?php if($fno[1]!='0')
            { ?>
            <tr>
                <td style="width: 13%;font-weight:bold;" class="font-style"><?= $pv_rv_label ?></td>
                <td style="text-align: left;    font-size: 10pt;" class="dotted font-style" ><?= $refernce_no; ?></td>
                <td></td>
                <td style="font-weight:bold;width: 23%;" class="font-style">Amount AED :</td>
                <td style="text-align: left; font-size: 10pt;" class="dotted font-style"><?= number_format(str_replace("-","",$tot),2); ?></td>
            </tr>
                <tr><td style="height:10px;"></td></tr>
            <tr>
                    <td style="font-weight:bold;width: 15%;" class="font-style">Voucher Date:</td>
                    <td style="text-align: left; font-size: 10pt;" class="dotted font-style"><?= $voucher_date; ?></td>
            </tr>
                <tr><td style="height:10px;"></td></tr>
                <tr>
                    <td style="font-weight:bold;width: 15%;" class="font-style">
                        <?php if($fno[1]=='1'): ?>
                            Payee Name:
                        <?php elseif($fno[1]=='2'): ?>
                            Received From Mr.M/s
                        <?php endif; ?>
                    </td>
                    <td style="text-align: left; font-size: 10pt;" class="dotted font-style"><?= $person_id; ?></td>
                </tr>
                <tr><td style="height:10px;"></td></tr>
                <tr>
                    <td style="font-weight:bold;width: 15%;" class="font-style">Being:</td>
                    <td style="text-align: left; font-size: 10pt;" class="dotted font-style"><?= $get_being['0']; ?></td>
                </tr>
                <tr><td style="height:10px;"></td></tr>
                <tr>
                    <td style="font-weight:bold;width: 16%;" class="font-style">
                <?php if($fno[1]=='1'): ?>
                    Credit Account:
                <?php else: ?>
                    Debit Account:
                <?php endif; ?>
                   </td>
                    <td style="text-align: left; font-size: 10pt;" class="dotted font-style"><?= $from_account[0] ?></td>
                </tr>
                <tr><td style="height:10px;"></td></tr>
                <tr>
                    <?php if($chck_no!=''): ?>
                        <td style="font-weight:bold;width: 16%;" class="font-style">Cheque No.:</td>
                        <td style="text-align: left; font-size: 10pt;" class="dotted font-style"><?= $chck_no; ?></td>
                    <?php endif; ?>
                </tr>
                <tr><td style="height:10px;"></td></tr>
                <tr>
                <?php if($chk_date!=''): ?>
                    <td style="font-weight:bold;width: 18%;" class="font-style">Cheque Due Date:</td>
                    <td style="text-align: left; font-size: 10pt;" class="dotted font-style"><?= $chk_date; ?></td>
                <?php endif; ?>
                </tr>

            <?php } else { ?>


            <?php } ?>
        </table>

    </div>

    <div style="height:10px;"></div>
    <div style="height: 375px;">
        <table class="invoice-items"  style="width:100%;border-collapse: collapse;">
            <tr class="heading">
                <td style="border: 1px solid black;">
                    Account<br>
                </td>
                <td style="border: 1px solid black;">
                    Description<br>
                </td>

                <td style="border: 1px solid black;">
                   Amount<br>
                </td>
               <!-- <td >
                    Credit<br><span class="arabic">الكمية</span>
                </td>-->

            </tr>
            <?= $line_item_tr ?>
            <tr >
                <td colspan="2" style="border: 1px solid black;"><?php echo numberTowords(str_replace("-","",$tot)); ?></td>

                <td style="border: 1px solid black;text-align: right;">AED <?php echo number_format(str_replace("-","",$tot),2); ?></td>
            </tr>



        </table>
<div style="height:30px;"></div>
        <table>
            <tr>
                <td><label style="font-weight:500;">Prepared by : <label style="font-size:5pt;text-decoration:underline;"><?= $created_by; ?></label></label></td>
                <?php if($fno[1]=='0') { ?>

                    <td><label style="font-weight:500;">Reviewed By : <label style="font-size:5pt;">_______________________</label></label></td>
                    <td>&nbsp;</td>

                    <td><label style="font-weight:500;">Approved By : <label style="font-size:5pt;">________________________</label></label></td>

                <?php } ?>

                <?php if($fno[1]=='1') { ?>

                    <td><label style="font-weight:500;">Reviewed by : <label style="font-size:5pt;">_______________________</label></label></td>
                    <td>&nbsp;</td>

                    <td><label style="font-weight:500;">Approved by : <label style="font-size:5pt;">________________________</label></label></td>
                <?php } ?>

                <?php if($fno[1]=='2') { ?>

                    <td><label style="font-weight:500;">Reviewed by : <label style="font-size:5pt;">_______________________</label></label></td>
                    <td>&nbsp;</td>

                    <td><label style="font-weight:500;">Approved by : <label style="font-size:5pt;">________________________</label></label></td>
                <?php } ?>
            </tr>
        </table>
        <?php if($fno[1]=='1'): ?>
        <div style="height:50px;border-bottom:1px solid #87CEFA;"></div>

        <table>
            <tr ><td style="height:20px;"></td></tr>
            <tr>
                <td><label style="font-weight:500;">Received by &nbsp;&nbsp; :<label style="font-size:5pt;">_______________________</label></label></td>
            </tr>
            <tr><td style="height:20px;"></td></tr>
            <tr>
                <td><label style="font-weight:500;">Received Date :<label style="font-size:5pt;">_______________________</label></label></td>
            </tr>
            <tr><td style="height:20px;"></td></tr>
            <tr>
                <td><label style="font-weight:500;">Signature &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:<label style="font-size:5pt;">_______________________</label></label></td>
            </tr>
        </table>
        <?php endif; ?>
    </div>

    <div style="padding:2px;font-size: 9pt;margin-top:15%;">
        <table style="border-collapse: collapse;">
            <tr>
                <td style="background-color: #b0e0e6;width:12%;border: 1px solid #87CEFA;"><label style="font-weight:500;">Printed By : <td><label style="font-size:5pt;"><?= $current_username; ?></label></td></label></td>
                <td style="background-color: #b0e0e6;width:12%;border: 1px solid #87CEFA;"><label style="font-weight:500;">Printed On : <td><label style="font-size:5pt;"><?= date('d-m-Y h:i:s'); ?></label></td></label></td>
            </tr>

        </table>
    </div>


</div>
</body>
</html>
