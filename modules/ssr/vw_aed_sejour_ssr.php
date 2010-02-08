<?php /* $Id: vw_aed_rpu.php 7346 2009-11-16 22:51:04Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7346 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m, $tab;

$sejour_id = CValue::getOrSession("sejour_id");

$group = CGroups::loadCurrent();
$user = new CMediusers();
$listPrats = $user->loadPraticiens(PERM_READ);

$sejour = new CSejour;
$sejour->load($sejour_id);

if ($sejour_id && !$sejour->_id) {
  CAppUI::setMsg(CAppUI::tr("CSejour-unavailable"), UI_MSG_WARNING);
  CAppUI::redirect("m=$m&tab=$tab&sejour_id=0");
}

$fiche_autonomie = new CFicheAutonomie();
if ($sejour->_id) {
  $sejour->loadRefPatient();
  $patient = $sejour->_ref_patient;
  $patient->loadStaticCIM10($AppUI->user_id);
  
  // Chargement de l'IPP 
  $patient->loadIPP();
  // Chargement du numero de dossier
  $sejour->loadNumDossier();
  
  $fiche_autonomie->sejour_id = $sejour->_id;
  $fiche_autonomie->loadMatchingObject();
} else {
  $sejour->praticien_id = $AppUI->user_id;
  $sejour->entree_prevue = mbDate()." 08:00:00";
  $sejour->sortie_prevue = mbDate()." 18:00:00";
  
  $patient = new CPatient;
}

// Aides à la saisie
$sejour->loadAides($AppUI->user_id);

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

$smarty->display("vw_aed_sejour_ssr.tpl");
?>