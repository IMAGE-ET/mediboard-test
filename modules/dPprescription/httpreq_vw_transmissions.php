<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$ds = CSQLDataSource::get("std");
// Charge toutes les transmissions liées a un sejour si un sejour_id est passé
// Sinon, charge les transmissions des dernieres 24 heures pour le praticien_id
$datetime = mbDateTime();
$date_max = $datetime;
$date_min = mbDateTime("-1 DAY", $date_max);
$addTrans = mbGetValueFromGet("addTrans", false);

$sejour_id = mbGetValueFromGetOrSession("sejour_id");
$praticien_id = mbGetValueFromGet("praticien_id");
$order_col = mbGetValueFromGet("order_col", "date");
$order_way = mbGetValueFromGet("order_way", "DESC");

$with_filter = mbGetValueFromGet("with_filter", '1');

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

$cibles = array();

$key = "";

foreach($transmissions as $_transmission){
	$_transmission->loadRefsFwd();
	$sejour =& $_transmission->_ref_sejour;
  $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  $sejour->loadRefsAffectations();
  $sejour->_ref_last_affectation->loadRefLit();
        
  $patient = $_transmission->_ref_sejour->_ref_patient;
  $lit = $sejour->_ref_last_affectation->_ref_lit;
  
  if($order_col == "patient_id"){
	  $key = $patient->nom.$patient->prenom.$patient->_id.$_transmission->date;
  }
  if($order_col == "date") {
	  $key= $_transmission->date;
	}
	if($order_col == "lit_id"){
	  $key = $lit->_view.$lit->_id.$_transmission->date;
	}
  $_transmission->calculCibles($cibles);
  $trans_and_obs[$key][$_transmission->_id] = $_transmission;
}

foreach($observations as $_observation){
  $_observation->loadRefsFwd();
  $sejour =& $_observation->_ref_sejour;
  $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  $sejour->loadRefsAffectations();
  $sejour->_ref_last_affectation->loadRefLit();
  
  $patient = $_observation->_ref_sejour->_ref_patient;
	$lit = $sejour->_ref_last_affectation->_ref_lit;
	
  if($order_col == "patient_id"){
	  $key = $patient->nom.$patient->prenom.$patient->_id.$_observation->date;
	}
	if($order_col == "date") {
	  $key= $_observation->date;
	}
	if($order_col == "lit_id"){
	  $key = $lit->_view.$lit->_id.$_observation->date;
	}
	
  $trans_and_obs[$key][$_observation->_id] = $_observation;
}

// Tri du tableau
if($order_way == "ASC"){
  ksort($trans_and_obs);
} else {
  krsort($trans_and_obs);
}

// Variables de templates
$smarty = new CSmartyDP();
$smarty->assign("with_filter", $with_filter);
$smarty->assign("order_way", $order_way);
$smarty->assign("order_col", $order_col);

$smarty->assign("trans_and_obs", $trans_and_obs);
$smarty->assign("addTrans", $addTrans);
$smarty->assign("sejour_id", $sejour_id);
$smarty->assign("transmission", new CTransmissionMedicale());
$smarty->assign("cibles", $cibles);
$smarty->display("inc_vw_transmissions.tpl");

?>