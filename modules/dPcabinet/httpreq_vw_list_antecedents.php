<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;
  
$can->needsEdit();

$patient_id  = mbGetValueFromGetOrSession("patient_id", 0);
$_is_anesth  = mbGetValueFromGetOrSession("_is_anesth", null);
$sejour_id = mbGetValueFromGetOrSession("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$patient = new CPatient;
$patient->load($patient_id);

// Chargement du dossier medical du patient
$patient->loadRefDossierMedical();

// Chargements des antecedents, traitements et addictions du dossier_medical
if($patient->_ref_dossier_medical->_id){
  $patient->_ref_dossier_medical->loadRefsAntecedents();
  $patient->_ref_dossier_medical->loadRefsTraitements();
  $patient->_ref_dossier_medical->loadRefsAddictions();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour" , $sejour);
$smarty->assign("patient"    , $patient);
$smarty->assign("_is_anesth" , $_is_anesth);

$smarty->display("inc_list_ant.tpl");

?>