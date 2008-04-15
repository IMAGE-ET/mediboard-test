<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$group = new CGroups();
$group->load($g);
$user = new CMediusers();
$listResponsables = $user->loadUsers(PERM_READ, $group->service_urgences_id);

$rpu_id = mbGetValueFromGetOrSession("rpu_id");
$rpu    = new CRPU;
$rpu->load($rpu_id);

// Chargement des aides a la saisie
$rpu->loadAides($AppUI->user_id);

if($rpu->_id) {
  $sejour  = $rpu->_ref_sejour;
  $patient = $sejour->_ref_patient;
  $patient->loadStaticCIM10($AppUI->user_id);
  
  // Chargement de l'IPP ($_IPP)
  $patient->loadIPP();
  // Chargement du numero de dossier ($_num_dossier)
  $sejour->loadNumDossier();
  
} else {
  $rpu->_responsable_id = $AppUI->user_id;
  $rpu->_entree         = mbDateTime();
  $sejour               = new CSejour;
  $patient              = new CPatient;
}


// Gestion des addictions, traitements, antecedents, diagnostics
$addiction = new CAddiction();
$addiction->loadAides($AppUI->user_id);

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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listServicesUrgence", $listServicesUrgence);
$smarty->assign("contrainteProvenance", $contrainteProvenance);
$smarty->assign("userSel", $userSel);
$smarty->assign("today", mbDate());
$smarty->assign("addiction", $addiction);
$smarty->assign("traitement", $traitement);
$smarty->assign("antecedent", $antecedent);
$smarty->assign("rpu"             , $rpu);
$smarty->assign("sejour"          , $sejour);
$smarty->assign("patient"         , $patient);
$smarty->assign("listResponsables", $listResponsables);

$smarty->display("vw_aed_rpu.tpl");
?>