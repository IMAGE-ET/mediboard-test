<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Fabien Ménager
*/


global $can;
// @todo à transférer dans  dPpatient
// En l'état on ne peut pas vérifier les droits sur dPcabinet
//$can->needsRead();

$user_id = CValue::getOrSession("user_id");
$patient_id = CValue::get("patient_id");
$consult_id = CValue::get("consult_id");

// On charge le praticien
$user = new CMediusers;
$user->load($user_id);
$user->loadRefs();
$canUser = $user->canDo();

$consult = new CConsultation;
if ($consult_id) {
  $consult->load($consult_id);
  $consult->loadRefsFwd();
}

// Chargement des aides à la saisie
$antecedent = new CAntecedent();
$antecedent->loadAides($user->_id);
$aides_antecedent = $antecedent->_aides_all_depends["rques"];

// On charge le patient pour connaitre ses antécedents et traitements actuels
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefDossierMedical();

$dossier_medical = &$patient->_ref_dossier_medical;
$patient->_ref_dossier_medical->loadRefsAntecedents();
$patient->_ref_dossier_medical->loadRefsTraitements();

$applied_antecedents = array();
foreach($dossier_medical->_ref_antecedents as $list) {
  foreach($list as $a) {
    if (!isset($applied_antecedents[$a->type])) $applied_antecedents[$a->type] = array();
    $applied_antecedents[$a->type][$a->rques] = true;
  }
}

$applied_traitements = array();
foreach($dossier_medical->_ref_traitements as $a) {
  $applied_traitements[$a->traitement] = true;
}

//mbTrace($aides_antecedent);
$traitement = new CTraitement();
$traitement->loadAides($user->_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("aides_antecedent", $aides_antecedent);
$smarty->assign("antecedent", $antecedent);
$smarty->assign("traitement", $traitement);
$smarty->assign("applied_antecedents", $applied_antecedents);
$smarty->assign("applied_traitements", $applied_traitements);
$smarty->assign("user", $user);
$smarty->assign("patient", $patient);
$smarty->assign("consult", $consult);

$smarty->display("vw_ant_easymode.tpl");
?>