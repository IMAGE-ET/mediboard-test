<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPprescription
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();
$sejour_id = CValue::get("sejour_id");
$group = CGroups::loadCurrent();

$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefCurrAffectation();
$sejour->_ref_curr_affectation->loadView();
$sejour->canRead();
$patient = $sejour->loadRelPatient();
$patient->loadRefsCorrespondantsPatient();
$patient->loadRefPhotoIdentite();
$patient->loadRefsNotes();
$patient->loadRefConstantesMedicales(null, array("poids", "taille"));
$dossier_medical = $patient->loadRefDossierMedical();

if ($dossier_medical->_id) {
  $dossier_medical->loadRefsAllergies();
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->countAntecedents();
  $dossier_medical->countAllergies();
}

$sejour->loadRefPraticien();
$sejour->loadRefsOperations();
$sejour->loadRefConfirmeUser()->loadRefFunction();

$prescription_active = CModule::getInstalled("dPprescription");

// Gestion des macro-cible seulement si prescription disponible
$cible_importante = $prescription_active;
$date_transmission = CAppUI::conf("soins synthese transmission_date_limit", $group->_guid) ? CMbDT::dateTime() : null;
$sejour->loadRefsTransmissions($cible_importante, true, false, null, $date_transmission);

$sejour->loadRefsObservations(true);
$sejour->loadRefsTasks();
$sejour->loadRefsNotes();

foreach ($sejour->_ref_tasks as $key=>$_task) {
  if ($_task->realise) {
    unset($sejour->_ref_tasks[$key]);
    continue;
  }

  $_task->loadRefPrescriptionLineElement();
  $_task->setDateAndAuthor();
  $_task->loadRefAuthor();
  $_task->loadRefAuthorRealise();
}

CSejourTask::sortByDate($sejour->_ref_tasks);

// Tri des transmissions par catégorie
$transmissions = array();

foreach ($sejour->_ref_transmissions as $_trans) {
  $_trans->loadRefUser()->loadRefFunction();
  $_trans->loadTargetObject();
  $_trans->calculCibles();
  $sort_key_pattern = "$_trans->_class $_trans->user_id $_trans->object_id $_trans->object_class $_trans->libelle_ATC";

  $sort_key = "$_trans->date $sort_key_pattern";

  $date_before = CMbDT::dateTime("-1 SECOND", $_trans->date);
  $sort_key_before = "$date_before $sort_key_pattern";

  $date_after  = CMbDT::dateTime("+1 SECOND", $_trans->date);
  $sort_key_after = "$date_after $sort_key_pattern";

  // Aggrégation à -1 sec
  if (array_key_exists($sort_key_before, $transmissions)) {
    $sort_key = $sort_key_before;
  }
  // à +1 sec
  else if (array_key_exists($sort_key_after, $transmissions)) {
    $sort_key = $sort_key_after;
  }

  if (!isset($transmissions[$sort_key])) {
    $transmissions[$sort_key] = array("data" => array(), "action" => array(), "result" => array());
  }
  if (!isset($transmissions[$sort_key][0])) {
    $transmissions[$sort_key][0] = $_trans;
  }
  $transmissions[$sort_key][$_trans->type][] = $_trans;
}
krsort($transmissions);

$sejour->_ref_transmissions = $transmissions;

$sejour->loadRefsConsultAnesth();
$sejour->_ref_consult_anesth->loadRefConsultation();

if ($prescription_active) {
  $prescription_sejour = $sejour->loadRefPrescriptionSejour();
  $prescription_sejour->loadJourOp(CMbDt::date());
  // Chargement des lignes de prescriptions
  $prescription_sejour->loadRefsLinesMedComments();
  foreach ($prescription_sejour->_ref_lines_med_comments["med"] as $_line_med) {
    /**@var CPrescriptionLineMedicament $_line_med*/
    $_line_med->updateAlerteAntibio();
  }
  $prescription_sejour->loadRefsLinesElementsComments();

  // Chargement des prescription_line_mixes
  $prescription_sejour->loadRefsPrescriptionLineMixes();

  foreach ($prescription_sejour->_ref_prescription_line_mixes as $curr_prescription_line_mix) {
    $curr_prescription_line_mix->loadRefsLines();

    $curr_prescription_line_mix->updateAlerteAntibio();

    $curr_prescription_line_mix->_compact_view = array();
    foreach ($curr_prescription_line_mix->_ref_lines as $_line) {
      if (!$_line->solvant) {
        $curr_prescription_line_mix->_compact_view[] = $_line->_ref_produit->libelle_abrege;
      }
    }
    if (count($curr_prescription_line_mix->_compact_view)) {
      $curr_prescription_line_mix->_compact_view = implode(", ", $curr_prescription_line_mix->_compact_view);
    }
    else {
      $curr_prescription_line_mix->_compact_view = "";
    }
  }
}

if ($prescription_active) {
  $date = CMbDT::dateTime();
  $days_config = CAppUI::conf("dPprescription general nb_days_prescription_current", $group->_guid);
  $date_before = CMbDT::dateTime("-$days_config DAY", $date);
  $date_after  = CMbDT::dateTime("+$days_config DAY", $date);
}

foreach ($sejour->_ref_operations as $_operation) {
  $_operation->loadRefsFwd();
  $_operation->_ref_chir->loadRefFunction();
  $_operation->loadBrancardage();
  $_operation->countAlertsNotHandled();
}

$smarty = new CSmartyDP();

$smarty->assign("sejour"   , $sejour);

if ($prescription_active) {
  $smarty->assign("date"        , $date);
  $smarty->assign("days_config" , $days_config);
  $smarty->assign("date_before" , $date_before);
  $smarty->assign("date_after"  , $date_after);
}

$smarty->display("inc_vw_suivi_clinique.tpl");
