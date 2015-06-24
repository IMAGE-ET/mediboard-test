<?php
/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$user = CMediusers::get();

$date         = CValue::getOrSession("date", CMbDT::date());
$vue          = CValue::getOrSession("vue2", CAppUI::pref("AFFCONSULT", 0));
$prat_id      = CValue::getOrSession("chirSel", $user->_id);
$selConsult   = CValue::getOrSession("selConsult");
$dossier_anesth_id = CValue::getOrSession("dossier_anesth_id");

$today = CMbDT::date();
$hour  = CMbDT::time();
$now   = CMbDT::dateTime();

if (!isset($current_m)) {
  global $m;
  $current_m = CValue::get("current_m", $m);
}

if (isset($_GET["date"])) {
  $selConsult = null;
  CValue::setSession("selConsult", null);
}

// Test compliqué afin de savoir quelle consultation charger
$consult = new CConsultation();
$consult->load($selConsult);

if (isset($_GET["selConsult"])) {
  if ($consult->_id && $consult->patient_id) {
    $consult->loadRefPlageConsult();
    $prat_id = $consult->_ref_plageconsult->chir_id;
    CValue::setSession("chirSel", $prat_id);
  }
  else {
    $consult = new CConsultation();
    $selConsult = null;
    CValue::setSession("selConsult");
  }
}
else {
  if ($consult->_id && $consult->patient_id) {
    $consult->loadRefPlageConsult();
    if ($prat_id !== $consult->_ref_plageconsult->chir_id) {
      $consult = new CConsultation();
      $selConsult = null;
      CValue::setSession("selConsult");
    }
  }
}

// On charge le praticien
$userSel = new CMediusers();
$userSel->load($prat_id);
$userSel->loadRefFunction();
$canUserSel = $userSel->canDo();

if (!$consult->_id) {
  if ($current_m == "dPurgences") {
    CAppUI::setMsg("Vous devez selectionner une consultation", UI_MSG_ALERT);
    CAppUI::redirect("m=urgences&tab=0");
  }

  $smarty = new CSmartyDP();
  $smarty->assign("consult"  , $consult);
  $smarty->assign("current_m", $current_m);
  $smarty->assign("date"     , $date);
  $smarty->assign("vue"      , $vue);
  $smarty->assign("userSel"  , $userSel);
  $smarty->display("../../dPcabinet/templates/edit_consultation.tpl");
  CApp::rip();
}

$consult->canDo()->needsEdit(array("selConsult" => null));

switch ($current_m) {
  case "dPurgences":
    $group = CGroups::loadCurrent();
    $listPrats = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);
    break;
  default:
    $listPrats = CConsultation::loadPraticiens(PERM_EDIT);
}

if (!$userSel->isMedical() && $current_m != "dPurgences") {
  CAppUI::setMsg("Vous devez selectionner un professionnel de santé", UI_MSG_ALERT);
  CAppUI::redirect("m=dPcabinet&tab=0");
}

$list_etat_dents = array();
$consultAnesth = null;

// Consultation courante
$consult->_ref_chir = $userSel;

// Chargement de la consultation
$patient = $consult->loadRefPatient();

if ($patient->_vip) {
  CCanDo::redirect();
}

$consultAnesth = $consult->loadRefConsultAnesth($dossier_anesth_id);

// Chargement du patient
$patient->countBackRefs("consultations");
$patient->countBackRefs("sejours");
$patient->countINS();

$patient->loadRefPhotoIdentite();
$patient->loadRefsNotes();
$patient->loadRefsCorrespondants();

// Affecter la date de la consultation
$date = $consult->_ref_plageconsult->date;

// Tout utilisateur peut consulter en lecture seule une consultation de séjour
$consult->canDo();

if (CModule::getActive("fse")) {
  // Chargement des identifiants LogicMax
  $fse = CFseFactory::createFSE();
  if ($fse) {
    $fse->loadIdsFSE($consult);
    $fse->makeFSE($consult);

    $cps = CFseFactory::createCPS()->loadIdCPS($consult->_ref_chir);

    CFseFactory::createCV()->loadIdVitale($consult->_ref_patient);
  }
}

$consult->loadRefGrossesse();

$patient->loadRefDossierMedical();
$dossier_medical = $consult->_ref_patient->_ref_dossier_medical;
if ($dossier_medical->_id) {
  $etat_dents = $dossier_medical->loadRefsEtatsDents();
  foreach ($etat_dents as $etat) {
    $list_etat_dents[$etat->dent] = $etat->etat;
  }
  $dossier_medical->loadRefsAllergies();
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->countAntecedents(false);
  $dossier_medical->countAllergies();
}

$sejour = $consult->loadRefSejour();

// Chargement du sejour
if ($sejour->_id) {
  // Cas des urgences
  $rpu = $sejour->loadRefRPU();
}

$isPrescriptionInstalled = CModule::getActive("dPprescription");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult"        , $consult);

$smarty->assign("isPrescriptionInstalled", $isPrescriptionInstalled);

$smarty->assign("listPrats"      , $listPrats);

if ($isPrescriptionInstalled) {
  $smarty->assign("line"         , new CPrescriptionLineMedicament());

  CPrescription::$_load_lite = true;
  $consult->_ref_sejour->loadRefPrescriptionSejour();
  $consultAnesth->loadRefSejour()->loadRefPrescriptionSejour();
  CPrescription::$_load_lite = false;
}

$smarty->assign("represcription" , CValue::get("represcription", 0));
$smarty->assign("date"           , $date);
$smarty->assign("hour"           , $hour);
$smarty->assign("vue"            , $vue);
$smarty->assign("today"          , $today);
$smarty->assign("now"            , $now);
$smarty->assign("_is_anesth"     , $consult->_is_anesth);
$smarty->assign("consult_anesth" , $consultAnesth);
$smarty->assign("_is_dentiste"   , $consult->_is_dentiste);
$smarty->assign("current_m"      , $current_m);
$smarty->assign("userSel"        , $userSel);
$smarty->assign("dossier_medical", $dossier_medical);
$smarty->assign("antecedents"    , $dossier_medical->_ref_antecedents_by_type);
$smarty->assign("tabs_count"     , CConsultation::makeTabsCount($consult, $dossier_medical, $consultAnesth, $sejour, $list_etat_dents));
$smarty->assign("list_etat_dents", $list_etat_dents);

if ($consult->_is_dentiste) {
  $devenirs_dentaires = $consult->_ref_patient->loadRefsDevenirDentaire();

  foreach ($devenirs_dentaires as &$devenir_dentaire) {
    $etudiant = $devenir_dentaire->loadRefEtudiant();
    $etudiant->loadRefFunction();
    $actes_dentaires  = $devenir_dentaire->countRefsActesDentaires();
  }

  $smarty->assign("devenirs_dentaires", $devenirs_dentaires);
}

if (count($consult->_refs_dossiers_anesth)) {
  $secs = range(0, 60-1, 1);
  $mins = range(0, 15-1, 1);

  $patient->loadRefLatestConstantes();

  $smarty->assign("secs"    , $secs);
  $smarty->assign("mins"    , $mins);
  $smarty->assign("examComp", new CExamComp());
  $smarty->assign("techniquesComp", new CTechniqueComp());
  $smarty->display("../../dPcabinet/templates/edit_consultation.tpl");
}
else {
  if (CAppUI::pref("MODCONSULT")) {
    $where = array();
    $where["entree"] = "<= '".CMbDT::dateTime()."'";
    $where["sortie"] = ">= '".CMbDT::dateTime()."'";
    $where["function_id"] = "IS NOT NULL";

    $affectation = new CAffectation();
    /** @var CAffectation[] $blocages_lit */
    $blocages_lit = $affectation->loadList($where);

    $where["function_id"] = "IS NULL";

    foreach ($blocages_lit as $blocage) {
      $blocage->loadRefLit()->loadRefChambre()->loadRefService();
      $where["lit_id"] = "= '$blocage->lit_id'";

      if ($affectation->loadObject($where)) {
        $sejour = $affectation->loadRefSejour();
        $patient = $sejour->loadRefPatient();
        $blocage->_ref_lit->_view .= " indisponible jusqu'à ".CMbDT::transform($affectation->sortie, null, "%Hh%Mmin %d-%m-%Y");
        $blocage->_ref_lit->_view .= " (".$patient->_view." (".strtoupper($patient->sexe).") ";
        $blocage->_ref_lit->_view .= CAppUI::conf("dPurgences age_patient_rpu_view") ? $patient->_age.")" : ")" ;
      }
    }
    $smarty->assign("blocages_lit" , $blocages_lit);
    $smarty->assign("consult_anesth", null);

    $smarty->display("../../dPcabinet/templates/edit_consultation.tpl");
  }
  else {
    $smarty->display("../../dPcabinet/templates/edit_consultation_classique.tpl");
  }
}