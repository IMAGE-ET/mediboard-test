<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/
global $AppUI, $can, $m;

$can->needsAdmin();

$patient_id = CValue::getOrSession("patient_id");

// Patient à analyser
$patient = new CPatient();
$patient->load($patient_id);

// Liste des praticiens disponibles
$listPrat = array();
if ($patient->_id) {
  $listPrat = new CMediusers();
  $listPrat = $listPrat->loadPraticiens(PERM_READ);
  $patient->loadDossierComplet();
  
}

if ($patient->_id) {
  foreach($patient->_ref_sejours as &$sejour){
  	$sejour->loadNumDossier();
  }
}

// Chargement des identifiants standards
$patient->loadIPP();
$patient->loadIdVitale();

// Liste des siblings
$listSiblings = $patient->getSiblings();
foreach ($listSiblings as &$curr_sib) {
  $curr_sib->loadDossierComplet();
  $curr_sib->loadIPP();
  $curr_sib->loadIdVitale();
  foreach($curr_sib->_ref_sejours as &$sejour){
  	$sejour->loadNumDossier();
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

$smarty->display("vw_siblings.tpl");

?>