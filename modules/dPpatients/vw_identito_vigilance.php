<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

global $can;
if (CAppUI::$user->_user_type =! 1) {
  $can->redirect();
}

$patient_id = CValue::getOrSession("patient_id");

// Patient à analyser
$patient = new CPatient();

$count_matching_patients = $patient->countMatchingPatients();

$id400 = new CIdSante400();
$id400->object_class = "CPatient";
$id400->tag = CAppUI::conf("dPpatients CPatient tag_conflict_ipp").CAppUI::conf("dPpatients CPatient tag_ipp");
$count_conflicts = $id400->countMatchingList();

$patient->load($patient_id);

// Liste des praticiens disponibles
$listPrat = array();
if ($patient->_id) {
  $listPrat = new CMediusers();
  $listPrat = $listPrat->loadPraticiens(PERM_READ);
  $patient->loadDossierComplet();
  
}

if ($patient->_id) {
  foreach($patient->_ref_sejours as &$_sejour){
  	$_sejour->loadNDA();
  }
}

// Chargement des identifiants standards
$patient->loadIPP();
if (CModule::getActive("fse")) {
  CFseFactory::createCV()->loadIdVitale($patient);
}


$vip = 0;
if($patient->vip && !CCanDo::admin()) {
  $user_in_list_prat = false;
  $user_in_logs      = false;
  foreach($patient->_ref_praticiens as $_prat) {
    if($user->_id == $_prat->user_id) {
      $user_in_list_prat = true;
    }
  }
  $patient->loadLogs();
  foreach($patient->_ref_logs as $_log) {
    if($user->_id == $_log->user_id) {
      $user_in_logs = true;
    }
  }
  $vip = !$user_in_list_prat && !$user_in_logs;
}

if($vip) {
  CValue::setSession("patient_id", 0);
}

// Liste des siblings
$listSiblings = $patient->getSiblings();
foreach ($listSiblings as &$_sibling) {
  $_sibling->loadDossierComplet();
  $_sibling->loadIPP();
  if (CModule::getActive("fse")) {
    CFseFactory::createCV()->loadIdVitale($_sibling);
  }
  foreach($_sibling->_ref_sejours as &$_sejour){
  	$_sejour->loadNDA();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canPatients"  , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions", CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"   , CModule::getCanDo("dPcabinet"));

$smarty->assign("patient"     , $patient);
$smarty->assign("listPrat"    , $listPrat);
$smarty->assign("listSiblings", $listSiblings);
$smarty->assign("vip"         , $vip);

$smarty->assign("count_matching_patients", $count_matching_patients);
$smarty->assign("count_conflicts", $count_conflicts);

$smarty->display("vw_identito_vigilance.tpl");

?>