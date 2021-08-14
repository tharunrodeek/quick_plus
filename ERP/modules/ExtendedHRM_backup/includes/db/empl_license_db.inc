<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

function add_empl_license($empl_id, $license_category, $license_type, $license_number, $issuing_country, $issue_date, $expiry_date) {
	
	return Insert('kv_empl_license', array('empl_id' => $empl_id, 'license_category' => $license_category, 'license_type' => $license_type,'license_number' => $license_number,'issuing_country' => $issuing_country, 'issue_date' => array($issue_date, 'date'), 'expiry_date' => array($expiry_date, 'date')));
}

function update_empl_license($empl_id, $license_category, $license_type, $license_number, $issuing_country, $issue_date, $expiry_date, $id) {
	
	return Update('kv_empl_license', array('id' => $id), array('empl_id' => $empl_id, 'license_category' => $license_category,'license_type' => $license_type,'license_number' => $license_number,'issuing_country' => $issuing_country, 'issue_date' => array($issue_date, 'date'), 'expiry_date' => array($expiry_date, 'date')));
}

function delete_empl_license($person, $with_licenses=false){

	$unique_name =  GetSingleValue('kv_empl_license', 'unique_name', array('id' => $person));
	$dir = company_path().'/attachments/licenses' ; 
	if ($unique_name && file_exists($dir."/".$unique_name))
		unlink($dir."/".$unique_name);

	return Delete('kv_empl_license', array('id' => $person));
}

/*
	Retrieve full license data from database for selected type/action/entity or license
*/
function get_empl_license_single($id){
	$kv_id = (int)$id ;
	$sql = "SELECT * FROM "	.TB_PREF."kv_empl_license WHERE id=".db_escape($kv_id);

	$license = array();
	$ret = db_query($sql, "Can't get employee  licenses");
	
	while($cont = db_fetch($ret)) 
		$license[] = $cont;
	//echo json_encode($cont);
	return $license;
}

function get_empl_license($id){
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_license WHERE id=".db_escape($id);

	$res = db_query($sql, "Can't get employee licenses");

	$person = db_fetch($res);
	$person['licenses'] = get_person_licenses($id);

	return $person;
}

/*
	Returns all licenses for given person id
*/
function get_person_licenses($id){
	//$kv_id = (int)$id ;
	$sql = "SELECT license.id, license.empl_id, category.description, type.description, license.license_number, country.local_name, license.issue_date, license.expiry_date, license.filename, license.unique_name FROM ".TB_PREF."kv_empl_license AS license LEFT JOIN ".TB_PREF."kv_empl_picklist AS type ON license.license_type = type.id  LEFT JOIN ".TB_PREF."kv_empl_picklist AS category ON license.license_category = category.id, ".TB_PREF."kv_empl_country AS country
	 WHERE  license.issuing_country = country.id AND license.empl_id=".db_escape($id);


	$licenses = array();
	$ret = db_query($sql, "Can't get employee licenses");
	
	while($cont = db_fetch($ret))
		$licenses[] = $cont;
	
	return $licenses;
}
?>