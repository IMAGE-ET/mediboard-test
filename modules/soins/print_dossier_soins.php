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

if (!$sejour_id){
  CAppUI::stepMessage(UI_MSG_WARNING, "Veuillez sélectionner un sejour pour visualiser le dossier complet");
  return;
}

$fiches_anesthesies = array();
$formulaires = null;

global $atc_classes;
$atc_classes = array();

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadNDA();
$sejour->loadRefsConsultAnesth();
$sejour->_ref_consult_anesth->loadRefConsultation();
$sejour->loadSuiviMedical();

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

  if (count($prescription->_ref_prescription_line_mixes)) {
    foreach ($prescription->_ref_prescription_line_mixes as $_prescription_line_mix) {
      $_prescription_line_mix->loadRefsLines();
      $_prescription_line_mix->calculQuantiteTotal();
      $_prescription_line_mix->loadRefPraticien();
      foreach ($_prescription_line_mix->_ref_lines as $_perf_line) {
        $list_lines["prescription_line_mix"][$_perf_line->_id] = $_perf_line;
        $_perf_line->loadRefsAdministrations();
        foreach ($_perf_line->_ref_administrations as $_administration_perf) {
          $_administration_perf->loadRefAdministrateur();
          if (!$_administration_perf->planification) {
            $dossier[CMbDT::date($_administration_perf->dateTime)]["prescription_line_mix"][$_perf_line->_id][$_administration_perf->quantite][$_administration_perf->_id] = $_administration_perf;
          }
        }
      }
    }
  }

  // Parcours des lignes de medicament et stockage du dossier cloturé
  if (count($prescription->_ref_lines_med_comments["med"])) {
    foreach ($prescription->_ref_lines_med_comments["med"] as $_atc => $lines_by_type) {
      if (!isset($atc_classes[$_atc])) {
        $classe_atc = new CBcbClasseATC();
        $atc_classes[$_atc] = $classe_atc->getLibelle($_atc);
      }
      foreach ($lines_by_type as $med_id => $_line_med) {
        $list_lines["medicament"][$_line_med->_id] = $_line_med;

        $_line_med->loadRefsAdministrations();
        foreach ($_line_med->_ref_administrations as $_administration_med) {
          $_administration_med->loadRefAdministrateur();
          if (!$_administration_med->planification) {
            $dossier[CMbDT::date($_administration_med->dateTime)]["medicament"][$_line_med->_id][$_administration_med->quantite][$_administration_med->_id] = $_administration_med;
          }
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
          $_line_elt->loadRefsAdministrations();
          foreach ($_line_elt->_ref_administrations as $_administration_elt) {
            $_administration_elt->loadRefAdministrateur();
            if (!$_administration_elt->planification) {
              $dossier[CMbDT::date($_administration_elt->dateTime)][$chap][$_line_elt->_id][$_administration_elt->quantite][$_administration_elt->_id] = $_administration_elt;
            }
          }
        }
      }
    }
  }
}

ksort($dossier);

// Constantes du séjour
$sejour->loadListConstantesMedicales();
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
$smarty->assign("in_modal"   , $in_modal);
$smarty->assign("fiches_anesthesies", $fiches_anesthesies);
$smarty->assign("atc_classes", $atc_classes);

$smarty->display("print_dossier_soins.tpl");
