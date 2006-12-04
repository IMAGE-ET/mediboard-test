<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$patient_id = mbGetValueFromGetOrSession("patient_id", 0);

if(!$patient_id) {
  $AppUI->setMsg("Vous devez selectionner un patient", UI_MSG_ALERT);
  $AppUI->redirect("m=dPpatients&tab=0");
}

// Récuperation du patient sélectionné
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadDossierComplet();
$patient->loadRefsAntecedents();
$patient->loadRefsTraitements();

$diagnosticsInstall = CModule::getActive("dPImeds") && CModule::getActive("dPsante400");

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("patient"            , $patient);
$smarty->assign("diagnosticsInstall" , $diagnosticsInstall);

$smarty->display("inc_vw_full_patients.tpl");
?>