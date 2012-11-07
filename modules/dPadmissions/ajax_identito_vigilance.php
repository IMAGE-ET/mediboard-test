<?php /* $Id: httpreq_vw_main_courante.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkEdit();

// Filtres
$see_mergeable = CValue::getOrSession("see_mergeable", "1");
$see_yesterday = CValue::getOrSession("see_yesterday", "1");
$see_cancelled = CValue::getOrSession("see_cancelled", "1");
$module        = CValue::get("module", "dPadmissions");

// Selection de la date
$date = CValue::get("date", mbDate());
$date_min = $see_yesterday ? mbDate("-1 day", $date) : $date;
$date_max = mbDate("+1 day", $date);

// Chargement des séjours concernés
$sejour = new CSejour;
$where = array();
$where["sejour.entree"] = "BETWEEN '$date_min' AND '$date_max'";
if ($module == "dPurgences") {
  $where["sejour.type"] = "= 'urg'";
}
if ($see_cancelled == 0) {
  $where["sejour.annule"] = "= '0'";
}
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$order = "entree";
$sejours = $sejour->loadList($where, $order);
$guesses = array();
$patients = array();
foreach ($sejours as &$_sejour) {
  if ($module == "dPurgences") {
    // Look for multiple RPU
    $_sejour->loadBackRefs("rpu");
  }
  
  // Chargement du numero de dossier
  $_sejour->loadNDA();

  // Chargement de l'IPP
  $_sejour->loadRefPatient();
  
  // Classement par patient
  if (!isset($patients[$_sejour->patient_id])) {
    $patients["$_sejour->patient_id"] = $_sejour->_ref_patient;
  }
  
  $patients["$_sejour->patient_id"]->_ref_sejours[$_sejour->_id] = $_sejour;
}

// Chargement des détails sur les patients
$mergeables_count = 0;
foreach ($patients as $patient_id => $patient) {
  $patient->loadIPP();
  
  $guess = array();
  $nicer = array();

  $guess["mergeable"] = isset($guesses[$patient_id]) ? true : false;
  
  // Sibling patients
  $siblings = $patient->getSiblings();
  foreach ($guess["siblings"] = array_keys($siblings) as $sibling_id) {
    if (array_key_exists($sibling_id, $patients)) {
      $guesses[$sibling_id]["mergeable"] = true;
      $guess["mergeable"] = true;
    }
  }

  // Phoning patients
  $phonings = $patient->getPhoning($_sejour->entree);
  foreach ($guess["phonings"] = array_keys($phonings) as $phoning_id) {
    if (array_key_exists($phoning_id, $patients)) {
      $guesses[$phoning_id]["mergeable"] = true;
      $guess["mergeable"] = true;
    }
  }
  
  // Multiple séjours 
  if (count($patient->_ref_sejours) > 1) {
    $guess["mergeable"] = true;
  }
  
  // Multiple Interventions
  foreach ($patient->_ref_sejours as $_sejour) {
    $operations = $_sejour->loadRefsOperations();
    foreach ($operations as $_operation) {
      $_operation->loadView();
    }
    
    if (count($operations) > 1) {
      $guess["mergeable"] = true;
    }
    
    // Multiple RPU 
    if ($module == "dPurgences") {
      if (count($_sejour->_back["rpu"]) > 1) {
        $guess["mergeable"] = true;
      }
    }
  }  
  
  if ($guess["mergeable"]) {
    $mergeables_count++;
  }

  $guesses[$patient->_id] = $guess;
}

// Tri sur la vue a posteriori : détruit les clés !
array_multisort(CMbArray::pluck($patients, "nom"), SORT_ASC, $patients);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("mergeables_count", $mergeables_count);
$smarty->assign("see_mergeable"   , $see_mergeable);
$smarty->assign("see_yesterday"   , $see_yesterday);
$smarty->assign("see_cancelled"   , $see_cancelled);
$smarty->assign("date"            , $date);
$smarty->assign("patients"        , $patients );
$smarty->assign("guesses"         , $guesses );
$smarty->assign("module"          , $module );

$smarty->display("inc_identito_vigilance.tpl");
?>