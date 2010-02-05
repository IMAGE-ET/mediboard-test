<?php /* $Id: vw_aed_rpu.php 7346 2009-11-16 22:51:04Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7346 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m, $tab;

$fiche_autonomie_id = CValue::getOrSession("fiche_autonomie_id");

$group = CGroups::loadCurrent();
$user = new CMediusers();
$listPrats = $user->loadPraticiens(PERM_READ);

$fiche_autonomie = new CFicheAutonomie;
$fiche_autonomie->load($fiche_autonomie_id);

if ($fiche_autonomie_id && !$fiche_autonomie->load($fiche_autonomie_id)) {
  CAppUI::setMsg(CAppUI::tr("CFicheAutonomie-unavailable"), UI_MSG_WARNING);
  CAppUI::redirect("m=$m&tab=$tab&fiche_autonomie_id=0");
}

// Chargement des aides a la saisie
$fiche_autonomie->loadAides($AppUI->user_id);

if ($fiche_autonomie->_id || $fiche_autonomie->sejour_id) {
  $sejour  = $fiche_autonomie->_ref_sejour;
  $patient = $sejour->_ref_patient;
  $patient->loadStaticCIM10($AppUI->user_id);
  
  // Chargement de l'IPP 
  $patient->loadIPP();
  // Chargement du numero de dossier
  $sejour->loadNumDossier();
  
} else {
  $fiche_autonomie->_praticien_id = $AppUI->user_id;
  $fiche_autonomie->_entree = mbDate()." 08:00:00";
  $fiche_autonomie->_sortie = mbDate()." 18:00:00";
  $sejour  = new CSejour;
  $patient = new CPatient;
}

// Gestion des traitements, antecedents, diagnostics
$traitement = new CTraitement();
$traitement->loadAides($AppUI->user_id);

$antecedent = new CAntecedent();
$antecedent->loadAides($AppUI->user_id);

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

$can_view_dossier_medical = 
  CModule::getCanDo('dPcabinet')->edit ||
  CModule::getCanDo('dPbloc')->edit ||
  CModule::getCanDo('dPplanningOp')->edit || 
  $AppUI->_ref_user->isFromType(array("Infirmière"));
  
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("can_view_dossier_medical", $can_view_dossier_medical);
$smarty->assign("today"               , mbDate());
$smarty->assign("traitement"          , $traitement);
$smarty->assign("antecedent"          , $antecedent);
$smarty->assign("fiche_autonomie"     , $fiche_autonomie);
$smarty->assign("sejour"              , $sejour);
$smarty->assign("patient"             , $patient);
$smarty->assign("listPrats"           , $listPrats);
$smarty->assign("etablissements"      , $etablissements);

$smarty->display("vw_aed_fiche_autonomie.tpl");
?>