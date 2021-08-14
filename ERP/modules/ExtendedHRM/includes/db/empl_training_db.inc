<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

function add_empl_training($empl_id, $training_desc, $course, $cost, $institute, $s_date, $e_date, $notes) {

	return Insert('kv_empl_training', array('empl_id' => $empl_id, 'training_desc' => $training_desc, 'course' => $course, 'cost' => $cost, 'institute' => $institute, 's_date' => array($s_date, 'date'), 'e_date' => array($e_date,'date'), 'notes' => $notes));
}

function update_empl_training($empl_id, $training_desc, $course, $cost, $institute, $s_date, $e_date, $notes, $id) {
	
	return Update('kv_empl_training', array('id' => $id), array('empl_id' => $empl_id, 'training_desc' => $training_desc, 'course' => $course, 'cost' => $cost, 'institute' => $institute, 's_date' => array($s_date, 'date'), 'e_date' => array($e_date,'date'), 'notes' => $notes));
}

function delete_empl_training($person, $with_trainings=false){
	return Delete('kv_empl_training', array('id' => $person));
}

/*
	Retrieve full training data from database for selected type/action/entity or training
*/
function get_empl_training_single($id){
	$kv_id = (int)$id ;
	$sql = "SELECT * FROM "	.TB_PREF."kv_empl_training WHERE id=".db_escape($kv_id);

	$training = array();
	$ret = db_query($sql, "Can't get employee  trainings");
	
	while($cont = db_fetch_row($ret)) 
		$training[] = $cont;
	//echo json_encode($cont);
	return $training;
}

function get_empl_training($id){
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_training WHERE id=".db_escape($id);

	$res = db_query($sql, "Can't get employee trainings");

	$person = db_fetch($res);
	$person['trainings'] = get_person_trainings($id);

	return $person;
}

/*
	Returns all trainings for given person id
*/
function get_person_trainings($id){
	//$kv_id = (int)$id ;
	$sql = "SELECT * FROM "	.TB_PREF."kv_empl_training WHERE empl_id=".db_escape($id);

	$trainings = array();
	$ret = db_query($sql, "Can't get employee trainings");
	
	while($cont = db_fetch($ret))
		$trainings[] = $cont;
	
	return $trainings;
}

function delete_entity_trainings($class, $entity){
	delete_empl_trainings(null, $class, $entity);
	// cleanup
	$res = get_empl_training($class, null, $entity, null, true);
	while($person = db_fetch($res)) {
		$rels = get_person_trainings($person['id']);
		if (count($rels) == 0) {
			delete_empl_person($person['id']);
		}
	}
}

?>
