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

$listPrats   = CConsultation::loadPraticiens(PERM_EDIT);
$listChirs   = $user->loadPraticiens(PERM_READ);
$listAnesths = $user->loadAnesthesistes();

$consult = new CConsultation();
if ($current_m == "dPurgences") {
  if (!$selConsult) {
    CAppUI::setMsg("Vous devez selectionner une consultation", UI_MSG_ALERT);
    CAppUI::redirect("m=urgences&tab=0");
  }

  $group = CGroups::loadCurrent();
  $listPrats = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);
}

if (isset($_GET["date"])) {
  $selConsult = null;
  CValue::setSession("selConsult", null);
}

// Test compliqué afin de savoir quelle consultation charger
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

if (!$userSel->isMedical() && $current_m != "dPurgences") {
  CAppUI::setMsg("Vous devez selectionner un professionnel de santé", UI_MSG_ALERT);
  CAppUI::redirect("m=dPcabinet&tab=0");
}

$consultAnesth =& $consult->_ref_consult_anesth;

// Consultation courante
$consult->_ref_chir =& $userSel;

// Chargement de la consultation
if ($consult->_id) {
  $patient = $consult->loadRefPatient();
  $consult->loadRefConsultAnesth();

  if ($patient->_vip) {
    CCanDo::redirect();
  }

  // Si on a passé un id de dossier d'anesth
  if ($dossier_anesth_id && isset($consult->_refs_dossiers_anesth[$dossier_anesth_id])) {
    $consultAnesth = $consult->_refs_dossiers_anesth[$dossier_anesth_id];
  }

  // Chargement du patient
  $patient->countBackRefs("consultations");
  $patient->countBackRefs("sejours");

  $patient->loadRefPhotoIdentite();
  $patient->loadRefsNotes();
  $patient->loadRefsCorrespondants();

  // Affecter la date de la consultation
  $date = $consult->_ref_plageconsult->date;
}
else {
  $consultAnesth->_id = 0;
}

if ($consult->_id) {
  $consult->canDo();
}

if ($consult->_id && CModule::getActive("fse")) {
  // Chargement des identifiants LogicMax
  $fse = CFseFactory::createFSE();
  if ($fse) {
    $fse->loadIdsFSE($consult);
    $fse->makeFSE($consult);

    $cps = CFseFactory::createCPS()->loadIdCPS($consult->_ref_chir);

    CFseFactory::createCV()->loadIdVitale($consult->_ref_patient);
  }
}

if (CModule::getActive("maternite")) {
  $consult->loadRefGrossesse();
}

// Tout utilisateur peut consulter en lecture seule une consultation de séjour
$consult->canEdit();

$list_etat_dents = array();
if ($consult->_id) {
  $patient->loadRefDossierMedical();
  $dossier_medical = $consult->_ref_patient->_ref_dossier_medical;
  if ($dossier_medical->_id) {
    $etat_dents = $dossier_medical->loadRefsEtatsDents();
    foreach ($etat_dents as $etat) {
      $list_etat_dents[$etat->dent] = $etat->etat;
    }
  }
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
$smarty->assign("listChirs"      , $listChirs);
$smarty->assign("listAnesths"    , $listAnesths);

if ($isPrescriptionInstalled) {
  $smarty->assign("line"         , new CPrescriptionLineMedicament());
}

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

  $smarty->assign("secs"    , $secs);
  $smarty->assign("mins"    , $mins);
  $smarty->assign("examComp", new CExamComp());
  $smarty->assign("techniquesComp", new CTechniqueComp());
  $smarty->display("../../dPcabinet/templates/vw_consultation.tpl");
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

    $smarty->display("../../dPcabinet/templates/vw_consultation.tpl");
  }
  else {
    $smarty->display("../../dPcabinet/templates/edit_consultation_classique.tpl");
  }
}