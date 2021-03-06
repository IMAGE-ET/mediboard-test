<?php
/**
 * Liste des accouchements � placer en salle de naissance
 *
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();
$ds   = CSQLDataSource::get("std");
$date = CValue::getOrSession("date", CMbDT::date());

// Toutes les salles des blocs
$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ, true, "nom", array("type" => "= 'obst'"));

// Les salles autoris�es
$salle = new CSalle();
$ljoin = array("bloc_operatoire" => "sallesbloc.bloc_id = bloc_operatoire.bloc_operatoire_id");
$where = array("bloc_operatoire.type" => "= 'obst'");
/** @var CSalle[] $listSalles */
$listSalles = $salle->loadListWithPerms(PERM_READ, $where, null, null, null, $ljoin);

// Chargement des Chirurgiens
$chir      = new CMediusers();
$listChirs = $chir->loadPraticiens(PERM_READ);

// Listes des interventions hors plage
$operation = new COperation();

$ljoin = array();
$ljoin["sejour"]    = "operations.sejour_id = sejour.sejour_id";
$ljoin["grossesse"] = "sejour.grossesse_id = grossesse.grossesse_id";

$where = array();
// Interv ou travail qui commence le jour choisi et n'a pas termin� d'accoucher
$where[] = "operations.date = '$date' OR (
  grossesse.datetime_debut_travail IS NOT NULL AND
  DATE(grossesse.datetime_debut_travail) < '$date' AND
  grossesse.datetime_accouchement IS NULL
)";
$where["operations.chir_id"]  = CSQLDataSource::prepareIn(array_keys($listChirs));
$where["sejour.grossesse_id"] = "IS NOT NULL";

/** @var CStoredObject[] $urgences */
$urgences = $operation->loadGroupList($where, "salle_id, chir_id", null, null, $ljoin);

$reservation_installed = CModule::getActive("reservation");
$diff_hour_urgence = CAppUI::conf("reservation diff_hour_urgence");

$sejours  = CStoredObject::massLoadFwdRef($urgences, "sejour_id");
$patients = CStoredObject::massLoadFwdRef($sejours, "patient_id");
CStoredObject::massLoadFwdRef($sejours, "grossesse_id");

$plage = new CPlageOp();

/** @var COperation[] $urgences */
foreach ($urgences as &$urgence) {
  $urgence->loadRefsFwd();
  $urgence->loadRefAnesth();
  $urgence->_ref_chir->loadRefsFwd();

  $sejour = $urgence->_ref_sejour;
  $patient = $sejour->loadRefPatient();
  $sejour->loadRefGrossesse();

  $dossier_medical = $patient->loadRefDossierMedical();
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->countAntecedents();
  $dossier_medical->countAllergies();

  if ($reservation_installed) {
    $first_log = $urgence->loadFirstLog();
    if (abs(CMbDT::hoursRelative($urgence->_datetime_best, $first_log->date)) <= $diff_hour_urgence) {
      $urgence->_is_urgence = true;
    }
  }

  // Chargement des plages disponibles pour cette intervention
  $urgence->_ref_chir->loadBackRefs("secondary_functions");
  $secondary_functions = array();
  foreach ($urgence->_ref_chir->_back["secondary_functions"] as $curr_sec_func) {
    $secondary_functions[$curr_sec_func->function_id] = $curr_sec_func;
  }
  $where = array();
  $selectPlages  = "(plagesop.chir_id = %1 OR plagesop.spec_id = %2
    OR plagesop.spec_id ".CSQLDataSource::prepareIn(array_keys($secondary_functions)).")";
  $where[]       = $ds->prepare($selectPlages, $urgence->chir_id, $urgence->_ref_chir->function_id);
  $where["date"] = "= '$date'";
  $where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
  $order = "salle_id, debut";

  $urgence->_alternate_plages = $plage->loadList($where, $order);
  foreach ($urgence->_alternate_plages as $curr_plage) {
    $curr_plage->loadRefsFwd();
  }
}

$anesth = new CMediusers();
$anesths = $anesth->loadAnesthesistes(PERM_READ);

$date_last_checklist = array();
foreach ($listSalles as $salle) {
  if ($salle->cheklist_man) {
    $checklist = new CDailyCheckList();
    $checklist->object_class = $salle->_class;
    $checklist->object_id = $salle->_id;
    $checklist->loadMatchingObject("date DESC");
    if ($checklist->_id) {
      $log = new CUserLog();
      $log->object_id     = $checklist->_id;
      $log->object_class  = $checklist->_class;
      $log->loadMatchingObject("date DESC");
      $date_last_checklist[$salle->_id] = $log->date;
    }
    elseif ($checklist->date) {
      $date_last_checklist[$salle->_id] = $checklist->date;
    }
    else {
      $date_last_checklist[$salle->_id] = "";
    }
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->debugging = false;

$smarty->assign("urgences"  , $urgences);
$smarty->assign("listBlocs" , $listBlocs);
$smarty->assign("listSalles", $listSalles);
$smarty->assign("anesths"   , $anesths);
$smarty->assign("date"      , $date);
$smarty->assign("date_last_checklist", $date_last_checklist);

$smarty->display("vw_placement.tpl");