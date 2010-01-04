<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$service_id = CValue::get("service_id");
$date       = CValue::get("date");
$date_min   = CValue::get("date_min");
$user_id    = CValue::get("user_id");
$degre      = CValue::get("degre");
$load_transmissions = CValue::get("transmissions");
$load_observations = CValue::get("observations");
$refresh = CValue::get("refresh");

$order_col = CValue::get("order_col", "date");
$order_way = CValue::get("order_way", "DESC");

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
	$where[] = "date >= '$date_min' AND date <= '$date 23:59:59'";
	
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

$cibles = array();
$trans_and_obs = array();
if($transmissions){
	foreach($transmissions as $_transmissions_by_prescription){
	  foreach($_transmissions_by_prescription as $_transmission){
	    $_transmission->loadRefsFwd();
	    $_transmission->_ref_sejour->loadRefPatient();
	    $_transmission->_ref_sejour->loadRefsAffectations();
		  $_transmission->_ref_sejour->_ref_last_affectation->loadRefLit();
		        
		  $patient = $_transmission->_ref_sejour->_ref_patient;
		  $lit = $_transmission->_ref_sejour->_ref_last_affectation->_ref_lit;
		  
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
			$_observation->_ref_sejour->loadRefsAffectations();
		  $_observation->_ref_sejour->_ref_last_affectation->loadRefLit();
		  
		  $patient = $_observation->_ref_sejour->_ref_patient;
			$lit = $_observation->_ref_sejour->_ref_last_affectation->_ref_lit;
			
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
	    $_observation->_ref_user->loadRefFunction();
	    $users[$_observation->user_id] = $_observation->_ref_user; 
	  }
	}
}

// Tri du tableau
if($order_way == "ASC"){
  ksort($trans_and_obs);
} else {
  krsort($trans_and_obs);
}

$filter_obs = new CObservationMedicale();
$filter_obs->degre = $degre;
$filter_obs->user_id = $user_id;

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("order_way", $order_way);
$smarty->assign("order_col", $order_col);
$smarty->assign("cibles", $cibles);
$smarty->assign("service", $service);
$smarty->assign("transmissions", $transmissions);
$smarty->assign("observations", $observations);
$smarty->assign("trans_and_obs", $trans_and_obs);
$smarty->assign("filter_obs", $filter_obs);
$smarty->assign("users", $users);
$smarty->assign("with_filter", "1");
$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", "$date 23:59:59");

if($user_id || $degre || $refresh){
  $smarty->display('../../dPprescription/templates/inc_vw_transmissions.tpl'); 
} else {
  $smarty->display('inc_vw_transmissions_pancarte.tpl'); 
}

?>