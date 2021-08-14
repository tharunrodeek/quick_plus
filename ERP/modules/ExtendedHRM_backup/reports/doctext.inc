<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
	
	if ($this->formData['doctype'] == 'payslip'){
		$this->title = trans("Payslip");
		$addr_title = trans("Payable To");
		$this->formData['document_name'] =trans("Payroll ID.");
	}elseif ($this->formData['doctype'] == 'loan'){
		$this->title = trans("Loan Details");
		$addr_title = trans("Employee Info");
		$this->formData['document_name'] =trans("Employee ID.");
	}elseif($this->formData['doctype'] == 'esb'){
		$this->title = trans("End of Service Benefit");
		$addr_title = trans("End of Service Benefit");
		$this->formData['document_name'] =trans(" ");
	}elseif($this->formData['doctype'] == 'paysummary'){
		$this->title = trans("Payroll Summary");
		$addr_title = trans("Pay Details");
		$this->formData['document_name'] =trans(" ");
	}elseif($this->formData['doctype'] == 'annual_encashment'){
		$this->title = trans("Encashment Report");
		$addr_title = trans("Encashment Details");
		$this->formData['document_name'] =trans(" ");
	}
	
	if($this->formData['doctype'] == 'paysummary'){
		$Addr1 = array(
			'title' => $addr_title,
			'id' => (isset($this->formData['empl_id']) ? $this->formData['empl_id']: ''),
			'year' => $this->formData['year']
			);
	}elseif($this->formData['doctype'] == 'esb'){
		$Addr1 = array(
			'title' => $addr_title,
			'id' => $this->formData['empl_id'],
			'name' => @$this->formData['empl_name'] ? $this->formData['empl_name'] : @$this->formData['empl_name'],
			'dept' => $this->formData['department'], 
			'desig' => $this->formData['desig'], 
			'resignation' => $this->formData['resignation'], 
			'status' => $this->formData['status'], 			
			'joining' => $this->formData['joining']
		);
	}else{
		$Addr1 = array(
			'title' => $addr_title,
			'id' => $this->formData['empl_id'],
			'name' => @$this->formData['empl_name'] ? $this->formData['empl_name'] : @$this->formData['empl_name'],
			'dept' => $this->formData['department'], 
			'desig' => $this->formData['desig'], 
			'joining' => $this->formData['joining']
		);
		
	
		
	}
	
	$this->formData['domicile'] = $this->company['domicile'];

	$Footer[0] = trans("All amounts stated in Company  Currency") ;

		// default values 
	if (!isset($this->formData['document_date']))
		$this->formData['document_date'] = Today(); 
		
	if (!isset($this->formData['document_number']))
		$this->formData['document_number'] = $print_invoice_no == 0 && isset($this->formData['reference']) ? $this->formData['reference'] : @$this->formData['trans_no'];

	// footer generic content
	if (@$this->formData['bank_name'])
		$Footer[] = trans("Bank"). ": ".$this->formData['bank_name']. ", " . trans("Bank Account") . ": " . $this->formData['acc_no'];

	if ($this->formData['doctype'] == 'payslip')
 		$Footer[] = trans("* Subject to Realisation of the Cheque.");

	if ($this->params['comments'] != '')
		$Footer[] = $this->params['comments'];

	if(isset($Addr1['name']))
		$this->formData['recipient_name'] = $Addr1['name'];
?>
