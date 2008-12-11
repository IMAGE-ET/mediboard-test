<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $can;

$canPatient = CModule::getCanDo("dPpatients");
$canPatient->needsEdit();

$patient_id  = mbGetValueFromGetOrSession("patient_id", 0);
$_is_anesth  = mbGetValueFromGetOrSession("_is_anesth", null);
$sejour_id = mbGetValueFromGetOrSession("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$patient = new CPatient;
$patient->load($patient_id);

// Chargement du dossier medical du patient
$patient->loadRefDossierMedical();
$dossier_medical =& $patient->_ref_dossier_medical;

// Chargements des antecedents et traitements du dossier_medical
if ($dossier_medical->_id) {
	$dossier_medical->loadRefsAntecedents();
  foreach ($dossier_medical->_ref_antecedents as $type) {
    foreach ($type as &$ant) {
      $ant->loadLogs();
    }
  }
  
	$dossier_medical->loadRefsTraitements();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour" , $sejour);
$smarty->assign("patient"    , $patient);
$smarty->assign("_is_anesth" , $_is_anesth);

$smarty->display("inc_list_ant.tpl");

?>