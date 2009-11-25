<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;

$can->needsRead();

// Initialisation de variables

// Type d'admission
$type = CValue::getOrSession("type");

$selAdmis  = CValue::getOrSession("selAdmis", "0");
$selSaisis = CValue::getOrSession("selSaisis", "0");
$order_col = CValue::getOrSession("order_col", "_nomPatient");
$order_way = CValue::getOrSession("order_way", "ASC");
$date      = CValue::getOrSession("date", mbDate());
$next      = mbDate("+1 DAY", $date);

$date_actuelle = mbDateTime("00:00:00");
$date_demain   = mbDateTime("00:00:00","+ 1 day");

$hier   = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

$date_min = mbDateTime("00:00:00", $date);
$date_max = mbDateTime("23:59:00", $date);

// Chargement des prestations
$prestations = CPrestation::loadCurrentList();

// Operations de la journée
$today = new CSejour;

$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$ljoin["users"] = "sejour.praticien_id = users.user_id";

if($type){ 
  $where["type"] = " = '$type'";
} else {
  $where["type"] = "!= 'urg'";
}
$where["group_id"] = "= '$g'";
$where["entree_prevue"] = "BETWEEN '$date' AND '$next'";
if($selAdmis != "0") {
  $where[] = "(entree_reelle IS NULL OR entree_reelle = '0000-00-00 00:00:00')";
  $where["annule"] = "= '0'";
}
if($selSaisis != "0") {
  $where["saisi_SHS"] = "= '0'";
  $where["annule"] = "= '0'";
}

if($order_col != "_nomPatient" && $order_col != "entree_prevue" && $order_col != "_nomPraticien"){
	$order_col = "_nomPatient";	
}

if($order_col == "_nomPatient"){
  $order = "patients.nom $order_way, patients.prenom, sejour.entree_prevue";
}
if($order_col == "entree_prevue"){
  $order = "sejour.entree_prevue $order_way, patients.nom, patients.prenom";
}
if($order_col == "_nomPraticien"){
  $order = "users.user_last_name $order_way, users.user_first_name";
}

  
$today = $today->loadGroupList($where, $order, null, null, $ljoin);

foreach ($today as $keySejour => $valueSejour) {
  $sejour =& $today[$keySejour];
//  $sejour->loadRefs();
  $sejour->loadRefPatient();
  $sejour->_ref_patient->loadIPP();
  $sejour->loadRefPraticien();
  $sejour->loadRefsOperations();
  $sejour->loadRefsAffectations();
  $sejour->loadNumDossier();
  foreach($sejour->_ref_operations as $key_op => $curr_op) {
    $sejour->_ref_operations[$key_op]->loadRefsConsultAnesth();
    //$sejour->_ref_operations[$key_op]->_ref_consult_anesth->loadRefsFwd();
    $sejour->_ref_operations[$key_op]->_ref_consult_anesth->loadRefConsultation();
    $sejour->_ref_operations[$key_op]->_ref_consult_anesth->_ref_consultation->loadRefPlageConsult();
    $sejour->_ref_operations[$key_op]->_ref_consult_anesth->_date_consult =& $sejour->_ref_operations[$key_op]->_ref_consult_anesth->_ref_consultation->_date;
  }
  $affectation =& $sejour->_ref_first_affectation;
 
  if ($affectation->affectation_id) {
    $affectation->loadRefLit();
    $affectation->_ref_lit->loadCompleteView();
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);
$smarty->assign("date_min"     , $date_min);
$smarty->assign("date_max"     , $date_max);
$smarty->assign("date_demain"  , $date_demain);
$smarty->assign("date_actuelle", $date_actuelle);
$smarty->assign("date"         , $date        );
$smarty->assign("selAdmis"     , $selAdmis    );
$smarty->assign("selSaisis"    , $selSaisis   );
$smarty->assign("order_col"    , $order_col   );
$smarty->assign("order_way"    , $order_way   );
$smarty->assign("today"        , $today       );
$smarty->assign("prestations"  , $prestations );
$smarty->assign("canAdmissions", CModule::getCanDo("dPadmissions"));
$smarty->assign("canPatients"  , CModule::getCanDo("dPpatients"));
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));

$smarty->display("inc_vw_admissions.tpl");

?>