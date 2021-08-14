<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

//--------------------------------------------------------------------------------------
function kv_get_tax_value($gross_pay, $year){	
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_taxes WHERE min_sal <= ".db_escape($gross_pay*12)." AND max_sal <= ".db_escape($gross_pay*12)." AND year=".db_escape($year)." LIMIT 1" ;
	$result = db_query($sql, "could not get Tax type");	
	return db_fetch($result);
}

function kv_get_tax_for_an_employee($empl_id, $year, $gross=null, $month=null){	
	
	if($gross)		
		$gross_pay_month = $gross;	
	else
		$gross_pay_month =  get_empl_gross_pay($empl_id);
	
	$monthsLis = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'monthsList'));
	$monthsList = unserialize(base64_decode($monthsLis));
	
	if(is_array($monthsList) && in_array((int)$month, $monthsList)){
		$frequency = GetSingleValue('kv_empl_option', 'option_value', array('option_name' => 'frequency'));
		$frequency--;
		
		if($frequency >= 0){			
			for($i=1; $i<= $frequency; $i++){
				$month_to_query = $month-$i;
				if($month_to_query < 1)
					$month_to_query_final = 12 + $month_to_query;
				else
					$month_to_query_final = $month_to_query;
					
				$gross_pay_month += GetSingleValue('kv_empl_salary', 'gross', array('month' => $month_to_query_final, 'year' => $year));				
			}
			$tax_row = kv_get_tax_value($gross_pay_month, $year);
			
			if($tax_row['percentage'] > 0){
				if($tax_row['taxable_salary'] > 0)
					$tax_val = ($tax_row['taxable_salary'] /*- $tax_row['min_sal']*/)*($tax_row['percentage']/100)+$tax_row['offset'];
				else
					$tax_val = ($gross_pay_month /*- $tax_row['min_sal']*/)*($tax_row['percentage']/100)+$tax_row['offset'];
			}elseif($tax_row['offset'] > 0 )
				$tax_val = $tax_row['offset'];
			else
				$tax_val = 0;
		} else {
			$tax_val = 0; 
		}
	} else 
		$tax_val = 0;
	return round($tax_val);
}
?>