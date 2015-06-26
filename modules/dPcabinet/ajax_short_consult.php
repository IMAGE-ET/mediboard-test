<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();
$consult_id = CValue::get("consult_id");
$sejour_id  = CValue::get("sejour_id");

$consult = new CConsultation();
$consult->load($consult_id);
$consult->canDo();

if (!$consult->_id) {
  CAppUI::stepAjax(CAppUI::tr("CConsultation.none"));
  CApp::rip();
}

$patient = $consult->loadRefPatient();
$dossier_medical = $patient->loadRefDossierMedical();
$consult_anesth = $consult->loadRefConsultAnesth();

$consult->loadExtCodesCCAM();
$consult->getAssociationCodesActes();
$consult->loadPossibleActes();
$consult->_ref_chir->loadRefFunction();

$list_etat_dents = array();

if ($dossier_medical->_id) {
  $dossier_medical->canDo();

  $etat_dents = $dossier_medical->loadRefsEtatsDents();
  foreach ($etat_dents as $etat) {
    $list_etat_dents[$etat->dent] = $etat->etat;
  }
}

$user = CMediusers::get();
$user->isAnesth();
$user->isPraticien();
$user->canDo();

// Chargement des listes de praticiens
$user = new CMediusers();
$listAnesths = $user->loadAnesthesistes(PERM_DENY);
$listChirs   = $user->loadPraticiens(PERM_DENY);

// Liste des dents CCAM
$liste_dents = reset(CDentCCAM::loadList());

// Chargement des boxes
$services = array();
$list_mode_sortie = array();

$sejour = $consult->loadRefSejour();

// Chargement du sejour
if ($sejour && $sejour->_id) {
  $sejour->loadExtDiagnostics();
  $sejour->loadRefDossierMedical();
  $sejour->loadNDA();

  // Cas des urgences
  $rpu = $sejour->loadRefRPU();
  if ($rpu && $rpu->_id) {
    $rpu->loadRefSejourMutation();
    $sejour->loadRefCurrAffectation()->loadRefService();

    // Urgences pour un séjour "urg"
    if ($sejour->type == "urg") {
      $services = CService::loadServicesUrgence();
    }

    if ($sejour->_ref_curr_affectation->_ref_service->radiologie == "1") {
      $services = array_merge($services, CService::loadServicesImagerie());
    }

    // UHCD pour un séjour "comp" et en UHCD
    if ($sejour->type == "comp" && $sejour->UHCD) {
      $services = CService::loadServicesUHCD();
    }

    if (CAppUI::conf("dPplanningOp CSejour use_custom_mode_sortie")) {
      $mode_sortie = new CModeSortieSejour();
      $where = array(
        "actif" => "= '1'",
      );
      $list_mode_sortie = $mode_sortie->loadGroupList($where);
    }
  }
}

$smarty = new CSmartyDP();

$smarty->assign("listAnesths"    , $listAnesths);
$smarty->assign("listChirs"      , $listChirs);
$smarty->assign("services"       , $services);
$smarty->assign("list_mode_sortie", $list_mode_sortie);
$smarty->assign("consult"        , $consult);
$smarty->assign("consult_anesth" , $consult_anesth);
$smarty->assign("patient"        , $patient);
$smarty->assign("_is_anesth"     , $user->isAnesth());
$smarty->assign("antecedent"     , new CAntecedent());
$smarty->assign("traitement"     , new CTraitement);
$smarty->assign("acte_ngap"      , CActeNGAP::createEmptyFor($consult));
$smarty->assign("liste_dents"    , $liste_dents);
if (CModule::getActive("dPprescription")) {
  $smarty->assign("line"           , new CPrescriptionLineMedicament());
}
$smarty->assign("userSel"        , $user);
$smarty->assign("user"           , $user);
$smarty->assign("sejour_id"      , $sejour_id);
$smarty->assign("today"          , CMbDT::date());
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));

if ($consult_anesth->_id) {
  $consult_anesth->loadRefOperation();
  $consult_anesth->loadRefsTechniques();
  $anesth = new CTypeAnesth();
  $anesth = $anesth->loadGroupList();
  
  $smarty->assign("list_etat_dents", $list_etat_dents);
  $smarty->assign("mins"           , range(0, 15-1, 1));
  $smarty->assign("secs"           , range(0, 60-1, 1));
  $smarty->assign("examComp"       , new CExamComp());
  $smarty->assign("techniquesComp" , new CTechniqueComp());
  $smarty->assign("anesth"         , $anesth);
  $smarty->assign("view_prescription", 0);
  
  if (CAppUI::conf("dPcabinet CConsultAnesth show_facteurs_risque")) {
    $sejour = new CSejour();
    $sejour->load($sejour_id);
    $sejour->loadRefDossierMedical();
    $smarty->assign("sejour"       , $sejour);
  }
}

$smarty->display("inc_short_consult.tpl");
