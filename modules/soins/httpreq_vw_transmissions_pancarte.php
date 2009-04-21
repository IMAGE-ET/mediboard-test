<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$service_id = mbGetValueFromGet("service_id");
$date       = mbGetValueFromGet("date");
$date_min   = mbGetValueFromGet("date_min");
$user_id    = mbGetValueFromGet("user_id");
$degre      = mbGetValueFromGet("degre");
$load_transmissions = mbGetValueFromGet("transmissions");
$load_observations = mbGetValueFromGet("observations");
$refresh = mbGetValueFromGet("refresh");

// Chargement du service
$service = new CService();
$service->load($service_id);

// Chargement des prescriptions qui sont dans le service selectionné
$prescription = new CPrescription();
$prescriptions = array();
$ljoin = array();
$ljoin["sejour"] = "prescription.object_id = sejour.sejour_id";
$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
$ljoin["lit"]      = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"]  = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"]  = "service.service_id = chambre.service_id";
$where["prescription.object_class"] = " = 'CSejour'";
$where["prescription.type"] = " = 'sejour'";
$where["service.service_id"]  = " = '$service_id'";
$where["sejour.entree_prevue"] = " < '$date 23:59:59'";
$where["sejour.sortie_prevue"] = " > '$date 00:00:00'";
$prescriptions = $prescription->loadList($where, null, null, null, $ljoin);

$transmissions = array();
$observations = array();
$users = array();

// Calcul du plan de soin pour chaque prescription
foreach($prescriptions as $_prescription){
  $patient =& $_prescription->_ref_patient;
  $patients[$patient->_id] = $patient;
  
  $where = array();
  $where["sejour_id"] = " = '$_prescription->object_id'";
	$where["date"] = " >= '$date_min'";
	if($user_id){
	  $where["user_id"] = " = '$user_id'";
	}
	if($degre){
	  if($degre == "urg_normal"){
	    $where[] = "degre = 'low' OR degre = 'high'";
	  }
	  if($degre == "urg"){
	    $where[] = "degre = 'high'";
	  }
	}
	if($load_transmissions == "1"){
	  $transmission = new CTransmissionMedicale();
	  @$transmissions[$_prescription->_id] = $transmission->loadList($where);
	}
	if($load_observations == "1"){
	  $observation = new CObservationMedicale();
	  @$observations[$_prescription->_id] = $observation->loadList($where);
	}
}

$trans_and_obs = array();
if($transmissions){
	foreach($transmissions as $_transmissions_by_prescription){
	  foreach($_transmissions_by_prescription as $_transmission){
	    $_transmission->loadRefsFwd();
	    $_transmission->_ref_sejour->loadRefPatient();
	    $trans_and_obs[$_transmission->date][$_transmission->_id] = $_transmission;
	    $_transmission->_ref_user->loadRefFunction();
	    $users[$_transmission->user_id] = $_transmission->_ref_user; 
	  }
	}
}
if($observations){
	foreach($observations as $_observations_by_prescription){
	  foreach($_observations_by_prescription as $_observation){
	    $_observation->loadRefsFwd();
	    $_observation->_ref_sejour->loadRefPatient();
	    $trans_and_obs[$_observation->date][$_observation->_id] = $_observation;
	    $_observation->_ref_user->loadRefFunction();
	    $users[$_observation->user_id] = $_observation->_ref_user; 
	  }
	}
}
$filter_obs = new CObservationMedicale();
$filter_obs->degre = $degre;
$filter_obs->user_id = $user_id;

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("service", $service);
$smarty->assign("transmissions", $transmissions);
$smarty->assign("observations", $observations);
$smarty->assign("trans_and_obs", $trans_and_obs);
$smarty->assign("filter_obs", $filter_obs);
$smarty->assign("users", $users);

if($user_id || $degre || $refresh){
  $smarty->display('../../dPprescription/templates/inc_vw_transmissions.tpl'); 
} else {
  $smarty->display('inc_vw_transmissions_pancarte.tpl'); 
}

?>