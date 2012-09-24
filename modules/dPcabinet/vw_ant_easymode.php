<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Fabien Ménager
*/

// @todo à transférer dans  dPpatient
// En l'état on ne peut pas vérifier les droits sur dPcabinet
// CCanDo::checkRead();

$patient_id = CValue::get("patient_id");
$consult_id = CValue::get("consult_id");

// On charge le praticien
$user = CAppUI::$user;
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

$dossier_medical = $patient->_ref_dossier_medical;
$dossier_medical->loadRefsAntecedents();
$dossier_medical->loadRefsTraitements();

$applied_antecedents = array();
foreach ($dossier_medical->_ref_antecedents_by_type as $list) {
  foreach($list as $a) {
    if (!isset($applied_antecedents[$a->type])) $applied_antecedents[$a->type] = array();
    
    $applied_antecedents[$a->type][] = $a->rques;
  }
}

foreach ($aides_antecedent as $_depend_1 => $_aides_by_depend_1) {
  foreach ($_aides_by_depend_1 as $_depend_2 => $_aides_by_depend_2) {
    foreach ($_aides_by_depend_2 as $_aide) {
      if (isset($applied_antecedents[$_depend_1])) {
        foreach ($applied_antecedents[$_depend_1] as $_atcd) {
          if ($_atcd == $_aide->text || strpos($_atcd, $_aide->text) == 0) {
            $_aide->_applied = true;
          }
        }
      }
    }
  }
}

$applied_traitements = array();
foreach ($dossier_medical->_ref_traitements as $a) {
  $applied_traitements[$a->traitement] = true;
}

$traitement = new CTraitement();
$traitement->loadAides($user->_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("aides_antecedent", $aides_antecedent);
$smarty->assign("antecedent", $antecedent);
$smarty->assign("traitement", $traitement);
$smarty->assign("applied_antecedents", $applied_antecedents);
$smarty->assign("applied_traitements", $applied_traitements);
$smarty->assign("patient", $patient);
$smarty->assign("consult", $consult);

$smarty->display("vw_ant_easymode.tpl");
?>