<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m, $tab;

$can->needsRead();

$group = CGroups::loadCurrent();
$user = new CMediusers();
if (CAppUI::conf("dPurgences only_prat_reponsable")) {
  $listResponsables = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);
} else {
  $listResponsables = $user->loadUsers(PERM_READ, $group->service_urgences_id);
}
$listPrats = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);

$rpu    = new CRPU;
$rpu_id = mbGetValueFromGetOrSession("rpu_id");

// Création d'un RPU pour un séjour existant
if ($sejour_id = mbGetValueFromGet("sejour_id")) {
  $rpu_id = null;
  $rpu->sejour_id = $sejour_id;
  $rpu->updateFormFields();
}

if ($rpu_id && !$rpu->load($rpu_id)) {
  $AppUI->setMsg("Ce RPU n'est pas ou plus disponible", UI_MSG_WARNING);
  $AppUI->redirect("m=$m&tab=$tab&rpu_id=0");
}


// Chargement des aides a la saisie
$rpu->loadAides($AppUI->user_id);

if ($rpu->_id || $rpu->sejour_id) {
  $sejour  = $rpu->_ref_sejour;
  $patient = $sejour->_ref_patient;
  $patient->loadStaticCIM10($AppUI->user_id);
  
  // Chargement de l'IPP ($_IPP)
  $patient->loadIPP();
  // Chargement du numero de dossier ($_num_dossier)
  $sejour->loadNumDossier();
  
} 
else {
  $rpu->_responsable_id = $AppUI->user_id;
  $rpu->_entree         = mbDateTime();
  $sejour               = new CSejour;
  $patient              = new CPatient;
}

// Gestion des traitements, antecedents, diagnostics
$traitement = new CTraitement();
$traitement->loadAides($AppUI->user_id);

$antecedent = new CAntecedent();
$antecedent->loadAides($AppUI->user_id);

// Chargement du praticien courant
$userSel = new CMediusers();
$userSel->load($AppUI->user_id);

// Contraintes sur le mode d'entree / provenance
$contrainteProvenance[6] = array("", 1, 2, 3, 4);
$contrainteProvenance[7] = array("", 1, 2, 3, 4);
$contrainteProvenance[8] = array("", 5, 8);

// Chargement des boxes d'urgences
$listServicesUrgence = CService::loadServicesUrgence();

// Chargement des etablissements externes
$order = "nom";
$etab = new CEtabExterne();
$listEtab = $etab->loadList(null, $order);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("line", new CPrescriptionLineMedicament());
$smarty->assign("listServicesUrgence" , $listServicesUrgence);
$smarty->assign("contrainteProvenance", $contrainteProvenance);
$smarty->assign("userSel"             , $userSel);
$smarty->assign("today"               , mbDate());
$smarty->assign("traitement"          , $traitement);
$smarty->assign("antecedent"          , $antecedent);
$smarty->assign("rpu"                 , $rpu);
$smarty->assign("sejour"              , $sejour);
$smarty->assign("patient"             , $patient);
$smarty->assign("listResponsables"    , $listResponsables);
$smarty->assign("listPrats"           , $listPrats);
$smarty->assign("listEtab", $listEtab);
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->display("vw_aed_rpu.tpl");
?>