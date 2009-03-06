<?php

/**
* @package Mediboard
* @subpackage dPboard
* @version $Revision:
* @author Alexis Granger
*/

$ds = CSQLDataSource::get("std");
// Charge toutes les transmissions liées a un sejour si un sejour_id est passé
// Sinon, charge les transmissions des dernieres 24 heures pour le praticien_id
$datetime = mbDateTime();
$date_max = $datetime;
$date_min = mbDateTime("-1 DAY", $date_max);

$sejour_id = mbGetValueFromGet("sejour_id");
$praticien_id = mbGetValueFromGet("praticien_id", "14");

// Initialisation
$transmissions = array();
$observations = array();
$where = array();
$whereTrans = array();
$trans_and_obs = array();

if($praticien_id && !$sejour_id){
	$sejour = new CSejour(); 
	$sejours = array();
	$where = array();
	$ljoin["transmission_medicale"] = "transmission_medicale.sejour_id = sejour.sejour_id";
	$ljoin["observation_medicale"] = "observation_medicale.sejour_id = sejour.sejour_id";
	$where[] = "(transmission_medicale.date BETWEEN '$date_min' and '$date_max') OR
							(observation_medicale.date BETWEEN '$date_min' and '$date_max')";
	$where["sejour.praticien_id"] = " = '$praticien_id'";
	$sejours = $sejour->loadList($where, null, null, null, $ljoin);

	$whereTrans["sejour_id"] = $ds->prepareIn(array_keys($sejours));
	$whereTrans["date"] = "BETWEEN '$date_min' and '$date_max'";
} elseif ($sejour_id) {
  $whereTrans["sejour_id"] = " = '$sejour_id'";
}

$transmission = new CTransmissionMedicale();
$transmissions = $transmission->loadList($whereTrans);

$observation = new CObservationMedicale();
$observations = $observation->loadList($whereTrans);

foreach($transmissions as $_transmission){
  $_transmission->loadRefsFwd();
  $_transmission->_ref_sejour->loadRefPatient();
  $_transmission->_ref_sejour->loadRefPraticien();
  $trans_and_obs[$_transmission->date][$_transmission->_id] = $_transmission;
}

foreach($observations as $_observation){
  $_observation->loadRefsFwd();
  $_observation->_ref_sejour->loadRefPatient();
  $_observation->_ref_sejour->loadRefPraticien();
  $trans_and_obs[$_observation->date][$_observation->_id] = $_observation;
}

krsort($trans_and_obs);
// Variables de templates
$smarty = new CSmartyDP();
$smarty->assign("trans_and_obs", $trans_and_obs);
$smarty->display("inc_vw_transmissions.tpl");

?>