<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPurgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkRead();
$group = CGroups::loadCurrent();
$user = CAppUI::$user;
$listResponsables = CAppUI::conf("dPurgences only_prat_responsable") ?
  $user->loadPraticiens(PERM_READ, $group->service_urgences_id) :
  $user->loadUsers(PERM_READ, $group->service_urgences_id);

$listPrats = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);

$rpu    = new CRPU;
$rpu_id = CValue::getOrSession("rpu_id");
if ($rpu_id && !$rpu->load($rpu_id)) {
  global $m, $tab;
  CAppUI::setMsg("Ce RPU n'est pas ou plus disponible", UI_MSG_WARNING);
  CAppUI::redirect("m=$m&tab=$tab&rpu_id=0");
}

// Création d'un RPU pour un séjour existant
if ($sejour_id = CValue::get("sejour_id")) {
  $rpu = new CRPU;
  $rpu->sejour_id = $sejour_id;
  $rpu->loadMatchingObject();
  $rpu->updateFormFields();
}

if ($rpu->_id || $rpu->sejour_id) {
  // Mise en session de l'id de la consultation, si elle existe.
  $rpu->loadRefConsult();
  if ($rpu->_ref_consult->_id) {
    CValue::setSession("selConsult", $rpu->_ref_consult->_id);
  }
  
  $sejour  = $rpu->_ref_sejour;
  $patient = $sejour->_ref_patient;
  
  // Chargement de l'IPP ($_IPP)
  $patient->loadIPP();
  
  // Chargement du numero de dossier ($_NDA)
  $sejour->loadNDA();
  $sejour->loadRefPraticien(1);
  $sejour->loadRefsNotes();
  $praticien = $sejour->_ref_praticien;
  $listResponsables[$praticien->_id] = $praticien;
}
else {
  $rpu->_responsable_id = $user->_id;
  $rpu->_entree         = mbDateTime();
  $sejour               = new CSejour;
  $patient              = new CPatient;
  $praticien            = new CMediusers;
}

// Gestion des traitements, antecedents, diagnostics
$traitement = new CTraitement();
$traitement->loadAides($user->_id);

$antecedent = new CAntecedent();
$antecedent->loadAides($user->_id);

// Contraintes sur le mode d'entree / provenance
$contrainteProvenance[6] = array("", 1, 2, 3, 4);
$contrainteProvenance[7] = array("", 1, 2, 3, 4);
$contrainteProvenance[8] = array("", 5, 8);

// Chargement des boxes 
$services = array();
// Urgences pour un séjour "urg"
$services = CService::loadServicesUrgence();

// UHCD pour un séjour "comp" et en UHCD
if ($sejour->type == "comp" && $sejour->UHCD) {
  $services = CService::loadServicesUHCD();
}

$module_orumip = CModule::getActive("orumip");
$orumip_active = $module_orumip && $module_orumip->mod_active;

$nb_printers = 0;

if (CModule::getActive("printing")) {
  // Chargement des imprimantes pour l'impression d'étiquettes 
  $user_printers = CMediusers::get();
  $function      = $user_printers->loadRefFunction();
  $nb_printers   = $function->countBackRefs("printers");
}

$list_mode_entree = array();
if (CAppUI::conf("dPplanningOp CSejour use_custom_mode_entree")) {
  $mode_entree = new CModeEntreeSejour();
  $where = array(
    "actif" => "= '1'",
  );
  $list_mode_entree = $mode_entree->loadGroupList($where);
}

if (CAppUI::conf("ref_pays") == 2) {
  $rpu->loadRefMotif();
  $chapitre = new CChapitreMotif();
  $chapitres = $chapitre->loadList();
  $motif    = new CMotif();
  if ($rpu->code_diag) {
    $motif->chapitre_id = $rpu->_ref_motif->chapitre_id;
  }
  $motifs = $motif->loadMatchingList();
}

// Création du template
$smarty = new CSmartyDP();

if (CAppUI::conf("ref_pays") == 2) {
  $smarty->assign("chapitre_id"         , 0);
  $smarty->assign("chapitres"           , $chapitres);
  $smarty->assign("motif_id"            , 0);
  $smarty->assign("motifs"              , $motifs);
}

$smarty->assign("group"               , $group);
if (CModule::getActive("dPprescription")) {
  $smarty->assign("line"              , new CPrescriptionLineMedicament());
}

$smarty->assign("services"            , $services);
$smarty->assign("contrainteProvenance", $contrainteProvenance);
$smarty->assign("userSel"             , $user);
$smarty->assign("today"               , mbDate());
$smarty->assign("traitement"          , $traitement);
$smarty->assign("antecedent"          , $antecedent);
$smarty->assign("rpu"                 , $rpu);
$smarty->assign("sejour"              , $sejour);
$smarty->assign("patient"             , $patient);
$smarty->assign("listResponsables"    , $listResponsables);
$smarty->assign("listPrats"           , $listPrats);
$smarty->assign("praticien"           , $praticien);
$smarty->assign("orumip_active"       , $orumip_active);
$smarty->assign("nb_printers"         , $nb_printers);
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("isImedsInstalled"    , (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->assign("list_mode_entree"    , $list_mode_entree);

$smarty->display("vw_aed_rpu.tpl");
