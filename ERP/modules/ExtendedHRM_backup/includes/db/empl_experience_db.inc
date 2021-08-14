<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

function add_empl_experience($empl_id, $company_name, $company_location, $department, $designation, $from, $to) {
	
	return Insert('kv_empl_experience', array('empl_id' => $empl_id, 'company_name' => $company_name, 'company_location' => $company_location,'department' => $department,'designation' => $designation, 's_date' => array($from, 'date'), 'e_date' => array($to, 'date')));
}

function update_empl_experience($empl_id, $company_name, $company_location, $department, $designation, $from, $to,  	$id) {
	
	return Update('kv_empl_experience', array('id' => $id), array('empl_id' => $empl_id, 'company_name' => $company_name,'company_location' => $company_location,'department' => $department,'designation' => $designation, 's_date' => array($from, 'date'), 'e_date' => array($to, 'date')));
}

function delete_empl_experience($person, $with_experiences=false){
	return Delete('kv_empl_experience', array('id' => $person));
}

/*
	Retrieve full experience data from database for selected type/action/entity or experience
*/
function get_empl_experience_single($id){
	$kv_id = (int)$id ;
	$sql = "SELECT * FROM "	.TB_PREF."kv_empl_experience WHERE id=".db_escape($kv_id);

	$experience = array();
	$ret = db_query($sql, "Can't get employee  experiences");
	
	while($cont = db_fetch_row($ret)) 
		$experience[] = $cont;
	//echo json_encode($cont);
	return $experience;
}

function get_empl_experience($id){
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_experience WHERE id=".db_escape($id);

	$res = db_query($sql, "Can't get employee experiences");

	$person = db_fetch($res);
	$person['experiences'] = get_person_experiences($id);

	return $person;
}

/*
	Returns all experiences for given person id
*/
function get_person_experiences($id){
	//$kv_id = (int)$id ;
	$sql = "SELECT * FROM "	.TB_PREF."kv_empl_experience WHERE empl_id=".db_escape($id);


	$experiences = array();
	$ret = db_query($sql, "Can't get employee experiences");
	
	while($cont = db_fetch_row($ret))
		$experiences[] = $cont;
	
	return $experiences;
}

function delete_entity_experiences($class, $entity){
	delete_empl_experiences(null, $class, $entity);
	// cleanup
	$res = get_empl_experience($class, null, $entity, null, true);
	while($person = db_fetch($res)) {
		$rels = get_person_experiences($person['id']);
		if (count($rels) == 0) {
			delete_empl_person($person['id']);
		}
	}
}

?>