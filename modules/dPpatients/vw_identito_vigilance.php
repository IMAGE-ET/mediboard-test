<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

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
  	$_sejour->loadNumDossier();
  }
}

// Chargement des identifiants standards
$patient->loadIPP();
$patient->loadIdVitale();

// Liste des siblings
$listSiblings = $patient->getSiblings();
foreach ($listSiblings as &$_sibling) {
  $_sibling->loadDossierComplet();
  $_sibling->loadIPP();
  $_sibling->loadIdVitale();
  foreach($_sibling->_ref_sejours as &$_sejour){
  	$_sejour->loadNumDossier();
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

$smarty->assign("count_matching_patients", $count_matching_patients);
$smarty->assign("count_conflicts", $count_conflicts);

$smarty->display("vw_identito_vigilance.tpl");

?>