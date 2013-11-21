<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$sejour_id = CValue::get("sejour_id");
$offline   = CValue::get("offline");
$in_modal  = CValue::get("in_modal");
$embed     = CValue::get("embed");
$period    = CValue::get("period");

if (!$sejour_id) {
  CAppUI::stepMessage(UI_MSG_WARNING, "Veuillez sélectionner un sejour pour visualiser le dossier complet");
  return;
}

$fiches_anesthesies = array();
$formulaires = null;

global $atc_classes;
$atc_classes = array();

$datetime_min = "";
if ($period) {
  $datetime_min = CMbDT::dateTime("- $period HOURS");
}

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadNDA();
$sejour->loadExtDiagnostics();
$sejour->loadRefsConsultAnesth();
$sejour->_ref_consult_anesth->loadRefConsultation();
$sejour->loadSuiviMedical($datetime_min);

$sejour->canRead();

// Chargement des affectations
$sejour->loadRefCurrAffectation()->loadRefLit();
foreach ($sejour->loadRefsAffectations() as $_affectation) {
  $_affectation->loadRefLit();
}

// Chargement des tâches
foreach ($sejour->loadRefsTasks() as $_task) {
  $_task->loadRefPrescriptionLineElement();
}

// Chargement des opérations
$sejour->loadRefsOperations();
foreach ($sejour->_ref_operations as $_interv) {
  $_interv->loadRefChir();
  $_interv->_ref_chir->loadRefFunction();
  $_interv->loadRefsConsultAnesth();
  $_interv->loadBrancardage();

  /** @var CDailyCheckList[] $check_lists  */
  $check_lists = $_interv->loadBackRefs("check_lists", "date");

  foreach ($check_lists as $_check_list_id => $_check_list) {
    // Remove check lists not signed
    if (!$_check_list->validator_id) {
      unset($_interv->_back["check_lists"][$_check_list_id]);
      unset($check_lists[$_check_list_id]);
      continue;
    }

    $_check_list->loadItemTypes();
    $_check_list->loadBackRefs('items');
    foreach ($_check_list->_back['items'] as $_item) {
      $_item->loadRefsFwd();
    }
  }

  $params = array(
    "dossier_anesth_id" => $_interv->_ref_consult_anesth->_id,
    "operation_id"      => $_interv->_id,
    "offline"           => 1,
    "print"             => 1,
    "pdf"               => 0
  );

  $fiches_anesthesies[$_interv->_id] = CApp::fetch("dPcabinet", "print_fiche", $params);
}

if ($offline && CModule::getActive("forms")) {
  $params = array(
    "detail"          => 3,
    "reference_id"    => $sejour->_id,
    "reference_class" => $sejour->_class,
    "target_element"  => "ex-objects-$sejour->_id",
    "print"           => 1,
  );

  $formulaires = CApp::fetch("forms", "ajax_list_ex_object", $params);
}

if ($embed) {
  // Fichiers et documents du sejour
  $sejour->loadRefsDocItems(false);

  // Fichiers et documents des interventions
  $interventions = $sejour->_ref_operations;
  foreach ($interventions as $_interv) {
    $_interv->loadRefPlageOp();
    $_interv->loadRefsDocItems(false);
  }

  // Fichiers et documents des consultations
  $consultations = $sejour->loadRefsConsultations();
  foreach ($consultations as $_consult) {
    $_consult->loadRefsDocItems(false);
  }

  $sejour->_ref_consult_anesth->_ref_consultation->loadRefsDocItems(false);
  $sejour->_ref_consult_anesth->loadRefsDocItems(false);
}

// Chargement du patient
$patient = $sejour->loadRefPatient();
$patient->loadComplete();
$patient->loadIPP();

// Chargement du dossier medical
$dossier_medical = $patient->_ref_dossier_medical;
$dossier_medical->canRead();
$dossier_medical->countAntecedents();

$dossier = array();
$list_lines = array();

if (CModule::getActive("dPprescription")) {
  // Chargement du dossier de soins cloturé
  $prescription = $sejour->loadRefPrescriptionSejour();

  // Chargement des lignes
  $prescription->loadRefsLinesMedComments("1", "1", "1", "", "", "0", "1");
  $prescription->loadRefsLinesElementsComments();
  $prescription->loadRefsPrescriptionLineMixes();
  $prescription->loadRefsLinesInscriptions();

  $where = array();
  $where["planification"] = " = '0'";

  if ($datetime_min) {
    $where["dateTime"] = " >= '$datetime_min'";
  }

  if (count($prescription->_ref_prescription_line_mixes)) {
    foreach ($prescription->_ref_prescription_line_mixes as $_prescription_line_mix) {
      $_prescription_line_mix->loadRefsLines();
      $_prescription_line_mix->calculQuantiteTotal();
      $_prescription_line_mix->loadRefPraticien();
      $_prescription_line_mix->loadRefsVariations();
      foreach ($_prescription_line_mix->_ref_lines as $_perf_line) {
        $list_lines["prescription_line_mix"][$_perf_line->_id] = $_perf_line;
        $_perf_line->loadRefsAdministrations($where);
        foreach ($_perf_line->_ref_administrations as $_administration_perf) {
          $_administration_perf->loadRefAdministrateur();
          $dossier[CMbDT::date($_administration_perf->dateTime)]["prescription_line_mix"][$_perf_line->_id][$_administration_perf->quantite][$_administration_perf->_id] = $_administration_perf;
        }
      }
    }
  }

  // Parcours des lignes de medicament et stockage du dossier cloturé
  if (count($prescription->_ref_lines_med_comments["med"])) {
    foreach ($prescription->_ref_lines_med_comments["med"] as $_atc => $lines_by_type) {
      if (!isset($atc_classes[$_atc])) {
        $medicament_produit = new CMedicamentProduit();
        $atc_classes[$_atc] = $medicament_produit->getLibelleATC($_atc);
      }
      foreach ($lines_by_type as $med_id => $_line_med) {
        $list_lines["medicament"][$_line_med->_id] = $_line_med;

        $_line_med->loadRefsAdministrations(null, $where);
        foreach ($_line_med->_ref_administrations as $_administration_med) {
          $_administration_med->loadRefAdministrateur();
          $dossier[CMbDT::date($_administration_med->dateTime)]["medicament"][$_line_med->_id][$_administration_med->quantite][$_administration_med->_id] = $_administration_med;
        }
      }
    }
  }

  // Parcours des lignes d'elements
  if (count($prescription->_ref_lines_elements_comments)) {
    foreach ($prescription->_ref_lines_elements_comments as $chap => $_lines_by_chap) {
      foreach ($_lines_by_chap as $_lines_by_cat) {
        foreach ($_lines_by_cat["comment"] as $_line_elt_comment) {
          $_line_elt_comment->loadRefPraticien();
        }
        foreach ($_lines_by_cat["element"] as $_line_elt) {
          $list_lines[$chap][$_line_elt->_id] = $_line_elt;
          $_line_elt->loadRefsAdministrations(null, $where);
          foreach ($_line_elt->_ref_administrations as $_administration_elt) {
            $_administration_elt->loadRefAdministrateur();
            $dossier[CMbDT::date($_administration_elt->dateTime)][$chap][$_line_elt->_id][$_administration_elt->quantite][$_administration_elt->_id] = $_administration_elt;
          }
        }
      }
    }
  }

  foreach ($prescription->_ref_lines_inscriptions as $inscriptions_by_type) {
    foreach ($inscriptions_by_type as $_inscription) {
      $_inscription->loadRefsAdministrations(null, $where);
      foreach ($_inscription->_ref_administrations as $_adm_inscription) {
        $_adm_inscription->loadRefAdministrateur();
        if ($_inscription instanceof CPrescriptionLineMedicament) {
          $chapitre = "medicament";
        }
        else {
          $chapitre = $_inscription->_chapitre;
        }
        $list_lines[$chapitre][$_inscription->_id] = $_inscription;
        $dossier[CMbDT::date($_adm_inscription->dateTime)][$chapitre][$_inscription->_id][$_adm_inscription->quantite][$_adm_inscription->_id] = $_adm_inscription;
      }
    }
  }
}

ksort($dossier);

// Constantes du séjour
$where = array();
if ($datetime_min) {
  $where["datetime"] = " >= '$datetime_min'";
}
$sejour->loadListConstantesMedicales($where);

$constantes_grid = CConstantesMedicales::buildGrid($sejour->_list_constantes_medicales, false);

$praticien = new CMediusers();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour"    , $sejour);
$smarty->assign("dossier"   , $dossier);
$smarty->assign("list_lines", $list_lines);
$smarty->assign("constantes_medicales_grid", $constantes_grid);

if (CModule::getActive("dPprescription")) {
  $smarty->assign("prescription", $prescription);
}

$smarty->assign("formulaires", $formulaires);
$smarty->assign("praticien"  , $praticien);
$smarty->assign("offline"    , $offline);
$smarty->assign("embed"      , $embed);
$smarty->assign("in_modal"   , $in_modal);
$smarty->assign("fiches_anesthesies", $fiches_anesthesies);
$smarty->assign("atc_classes", $atc_classes);

$smarty->display("print_dossier_soins.tpl");
