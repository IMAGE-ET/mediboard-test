<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

$sejour_id  = CValue::get("sejour_id");
$sejour = new CSejour();
$sejour->load($sejour_id);

$entree = $sejour->entree;
$debut  = CValue::getOrSession("debut", $entree);
$debut  = CMbDT::date("last sunday", $debut);
$fin    = CMbDT::date("next sunday", $debut);
$debut  = CMbDT::date("+1 day", $debut);
$nbjours = 7;

$patient = $sejour->loadRefPatient();
// Chargement des caracteristiques du patient
$patient =& $sejour->_ref_patient;
$patient->loadRefPhotoIdentite();
$patient->loadRefLatestConstantes(null, array("poids", "taille"));

$dossier_medical = $patient->loadRefDossierMedical();
if ($dossier_medical->_id) {
  $dossier_medical->loadRefsAllergies();
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->countAntecedents();
  $dossier_medical->countAllergies();
}

$sejour->loadRefCurrAffectation($debut);
if (!$sejour->_ref_curr_affectation->_id) {
  $sejour->loadRefsAffectations();
  $sejour->_ref_curr_affectation = $sejour->_ref_last_affectation;
}

//Instanciation du planning
$planning = new CPlanningWeek($debut, $debut, $fin, $nbjours, false, 450, null, true);
$planning->title = "";
$planning->guid = $sejour->_guid;
$planning->hour_min = "07";
$planning->hour_max = "20";
$planning->pauses = array("12");

$ljoin = array();
$ljoin['plageconsult'] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
$where = array();
$where[] = "plageconsult.date BETWEEN '$debut' AND '$fin'";
$where["patient_id"] = " = '$patient->_id'";
$consultation = new CConsultation();
$consultations = $consultation->loadList($where, null, null, "consultation_id", $ljoin);

foreach ($consultations as $_consult) {
  /* @var CConsultation $_consult*/
  $_consult->loadRefPlageConsult()->loadRefChir();
  $color = "#cfc";
  $libelle = $_consult->_view." - ".$_consult->_ref_plageconsult->_ref_chir->_view;
  $event = new CPlanningEvent($_consult->_guid, $_consult->_datetime, CMbDT::minutesRelative($_consult->_datetime, $_consult->_date_fin), $libelle, $color, true);
  $event->onmousover = true;
  $planning->addEvent($event);
}

$ljoin = array();
$ljoin['plagesop'] = "plagesop.plageop_id = operations.plageop_id";
$where = array();
$where[] = "(plagesop.date BETWEEN '$debut' AND '$fin') OR (operations.date BETWEEN '$debut' AND '$fin')";
$where["operations.sejour_id"] = " = '$sejour_id'";
$operation = new COperation();
$operations = $operation->loadList($where, null, null, "operation_id", $ljoin);

foreach ($operations as $_operation) {
  /* @var COperation $_operation*/
  $_operation->loadRefChir();
  $_operation->loadRefPlageOp();
  $color = "#fcc";
  $class = null;
  if ($_operation->_acte_execution == $_operation->_datetime) {
    $_operation->_acte_execution = CMbDT::addDateTime($_operation->temp_operation, $_operation->_datetime);
  }

  $libelle = "Intervention par le Dr ". $_operation->_ref_chir->_view." - $_operation->libelle";
  $event = new CPlanningEvent($_operation->_guid, $_operation->_datetime, CMbDT::minutesRelative($_operation->_datetime, $_operation->_acte_execution), $libelle, $color, true);
  $event->onmousover = true;
  $planning->addEvent($event);
}

$dates = array($debut, $fin);
$prescription = $sejour->loadRefPrescriptionSejour();
$lines["imagerie"] = $prescription->loadRefsLinesElement(null, "imagerie");
$lines["kine"]     = $prescription->loadRefsLinesElement(null, "kine");
foreach ($lines as $category => $cat) {
  $color = $category == "kine" ? "#ccf" : "aaa";
  foreach ($cat as $_line) {
    /* @var CPrescriptionLineElement $_line*/
    // Chargement des planifications pour la date courante
    $planif = new CPlanificationSysteme();
    $where = array();
    $where["object_id"] = " = '$_line->_id'";
    $where["object_class"] = " = '$_line->_class'";
    $where["dateTime"] = " BETWEEN '$debut 00:00:00' AND '$fin 23:59:59'";
    $planifs = $planif->loadList($where, "dateTime");
    foreach ($planifs as $_planif) {
      /* @var CPlanificationSysteme $_planif*/

      $_planif->loadRefPrise();
      $libelle = $_planif->_ref_prise->quantite." ".$_line->_unite_prise." - ".$_line->_view;
      $event = new CPlanningEvent($_line->_guid, $_planif->dateTime, 60, $libelle, $color, true);
      $event->onmousover = true;
      $planning->addEvent($event);
    }

    $_line->loadRefsAdministrations($dates);
    foreach($_line->_ref_administrations as $_admin) {
      /* @var CAdministration $_admin*/
      $libelle = $_admin->quantite." ".$_line->_unite_prise." - ".$_line->_view;
      $event = new CPlanningEvent($_admin->_guid, $_admin->dateTime, 60, $libelle, $color, true);
      $event->onmousover = true;
      $planning->addEvent($event);
    }
  }
}

$smarty = new CSmartyDP();

$smarty->assign("sejour"   , $sejour);
$smarty->assign("planning" , $planning);
$smarty->assign("debut"    , $debut);
$smarty->assign("fin"      , $fin);
$smarty->assign("precedent", CMbDT::date("-1 week", $debut));
$smarty->assign("suivant"  , CMbDT::date("+1 week", $debut));

$smarty->display("vw_planning_sejour.tpl");