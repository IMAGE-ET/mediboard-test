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
  $smarty->display("../../dPcabinet/templates/vw_consultation.tpl");
  CApp::rip();
}

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

// Count dans les tabs
$tabs_count = array(
  "AntTrait"            => 0,
  "Constantes"          => 0,
  "prescription_sejour" => 0,
  "facteursRisque"      => 0,
  "Examens"             => 0,
  "Exams"               => 0,
  "ExamsComp"           => 0,
  "Intub"               => 0,
  "InfoAnesth"          => 0,
  "dossier_traitement"  => 0,
  "dossier_suivi"       => 0,
  "Actes"               => 0,
  "fdrConsult"          => 0,
  "reglement"           => 0
);

// Consultation courante
$consult->_ref_chir = $userSel;

// Chargement de la consultation
$patient = $consult->loadRefPatient();
$consult->loadRefConsultAnesth();

$consultAnesth = $consult->_ref_consult_anesth;

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

if (CModule::getActive("maternite")) {
  $consult->loadRefGrossesse();
}

$patient->loadRefDossierMedical();
$dossier_medical = $consult->_ref_patient->_ref_dossier_medical;
if ($dossier_medical->_id) {
  $etat_dents = $dossier_medical->loadRefsEtatsDents();
  foreach ($etat_dents as $etat) {
    $list_etat_dents[$etat->dent] = $etat->etat;
  }
}

$sejour = $consult->loadRefSejour();

// Chargement du sejour
if ($sejour->_id) {
  // Cas des urgences
  $rpu = $sejour->loadRefRPU();
}

CPrescription::$_load_lite = true;
foreach ($tabs_count as $_tab => $_count) {
  $count = 0;
  switch ($_tab) {
    case "AntTrait":
      $prescription = $dossier_medical->loadRefPrescription();
      $prescription->countLinesMedsElements();
      $dossier_medical->countTraitements();
      $dossier_medical->countAntecedents();
      $tabs_count[$_tab] = $dossier_medical->_count_antecedents + $dossier_medical->_count_traitements + $prescription->_counts_by_chapitre["med"];
      break;
    case "Constantes":
      $tabs_count[$_tab] = $patient->countBackRefs("constantes");
      break;
    case "prescription_sejour":
      if (!$consultAnesth->_id) {
        break;
      }
      $sejour = $consultAnesth->loadRefSejour();
      if ($sejour->_id) {
        $sejour->loadRefsPrescriptions();
        foreach ($sejour->_ref_prescriptions as $key => $_prescription) {
          if (!$_prescription->_id) {
            unset($sejour->_ref_prescriptions[$key]);
            continue;
          }

          $sejour->_ref_prescriptions[$_prescription->_id] = $_prescription;
          unset($sejour->_ref_prescriptions[$key]);
        }

        $prescription = new CPrescription();

        $prescription->massCountMedsElements($sejour->_ref_prescriptions);
        foreach ($sejour->_ref_prescriptions as $_prescription) {
          $count += array_sum($_prescription->_counts_by_chapitre);
        }
      }

      $tabs_count[$_tab] = $count;
      break;
    case "facteursRisque":
      if (!$consultAnesth) {
        break;
      }
      $fields = array(
        "risque_antibioprophylaxie", "risque_MCJ_chirurgie", "risque_MCJ_patient",
        "risque_prophylaxie", "risque_thrombo_chirurgie", "risque_thrombo_patient"
      );

      foreach ($fields as $_field) {
        if ($dossier_medical->$_field != "NR") {
          $count++;
        }
      }

      if ($dossier_medical->facteurs_risque) {
        $count++;
      }

      $tabs_count[$_tab] = $count;
      break;
    case "Examens":
      if ($consultAnesth->_id) {
        break;
      }
      $fields = array("motif", "rques", "examen", "traitement");
      foreach ($fields as $_field) {
        if ($consult->$_field) {
          $count++;
        }
      }
      $count += $consult->countBackRefs("examaudio");
      $count += $consult->countBackRefs("examnyha");
      $count += $consult->countBackRefs("exampossum");
      $tabs_count[$_tab] = $count;
      break;
    case "Exams":
      if (!$consultAnesth->_id) {
        break;
      }
      $fields = array("examenCardio", "examenPulmo", "examenDigest", "examenAutre");
      foreach ($fields as $_field) {
        if ($consultAnesth->$_field) {
          $count++;
        }
      }
      if ($consult->examen != "") {
        $count++;
      }
      $count += $consult->countBackRefs("examaudio");
      $count += $consult->countBackRefs("examnyha");
      $count += $consult->countBackRefs("exampossum");
      $tabs_count[$_tab] = $count;
      break;
    case "ExamsComp":
      if (!$consultAnesth->_id) {
        break;
      }
      $count += $consult->countBackRefs("examcomp");
      if ($consultAnesth->result_ecg) {
        $count++;
      }
      if ($consultAnesth->result_rp) {
        $count++;
      }
      $tabs_count[$_tab] = $count;
      break;
    case "Intub":
      if (!$consultAnesth->_id) {
        break;
      }
      $fields = array("mallampati", "bouche", "distThyro");
      foreach ($fields as $_field) {
        if ($consultAnesth->$_field) {
          $count++;
        }
      }
      $tabs_count[$_tab] = $count;
      break;
    case "InfoAnesth":
      if (!$consultAnesth->_id) {
        break;
      }
      $op = $consultAnesth->loadRefOperation();

      if (!$op->_id) {
        break;
      }

      $fields_anesth = array("prepa_preop", "premedication");
      $fields_op = array("passage_uscpo", "type_anesth", "ASA", "position");

      foreach ($fields_anesth as $_field) {
        if ($consultAnesth->$_field) {
          $count++;
        }
      }
      foreach ($fields_op as $_field) {
        if ($op->$_field) {
          $count++;
        }
      }

      if ($consult->rques) {
        $count++;
      }

      $count += $consultAnesth->countBackRefs("techniques");

      $tabs_count[$_tab] = $count;
      break;
    case "dossier_traitement":
      break;
    case "dossier_suivi":
      break;
    case "Actes":
      $consult->countActes();
      $tabs_count[$_tab] = $consult->_count_actes;
      break;
    case "fdrConsult":
      $consult->countDocs();
      $consult->countFiles();
      $tabs_count[$_tab] = $consult->_nb_docs + $consult->_nb_files;
      break;
    case "reglement":
      $consult->loadRefFacture()->loadRefsReglements();
      $tabs_count[$_tab] = count($consult->_ref_facture->_ref_reglements);
  }
}
CPrescription::$_load_lite = false;

$isPrescriptionInstalled = CModule::getActive("dPprescription");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult"        , $consult);

$smarty->assign("isPrescriptionInstalled", $isPrescriptionInstalled);

$smarty->assign("listPrats"      , $listPrats);

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
$smarty->assign("tabs_count"     , $tabs_count);
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