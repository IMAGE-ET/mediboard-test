<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$ds = CSQLDataSource::get("std");

$date  = CValue::getOrSession("date", CMbDT::date());

// Toutes les salles des blocs
$group =  CGroups::loadCurrent();
$listBlocs = $group->loadBlocs(PERM_READ);

// Les salles autorisées
$salle = new CSalle();
$listSalles = $salle->loadListWithPerms(PERM_READ);

// Chargement des Chirurgiens
$chir      = new CMediusers();
$listChirs = $chir->loadPraticiens(PERM_READ);

// Listes des interventions hors plage
$operation = new COperation();
$where = array ();
$where["date"] = "= '$date'";
$where["plageop_id"] = "IS NULL";
$where["operations.chir_id"] = CSQLDataSource::prepareIn(array_keys($listChirs));

/** @var COperation[] $urgences */
$urgences = $operation->loadGroupList($where, "salle_id, date, time_operation, chir_id");

$reservation_installed = CModule::getActive("reservation");
$diff_hour_urgence = CAppUI::conf("reservation diff_hour_urgence");

foreach ($urgences as &$urgence) {
  $urgence->loadRefsFwd();
  $urgence->loadRefAnesth();
  $patient = $urgence->_ref_sejour->loadRefPatient();
  $dossier_medical = $patient->loadRefDossierMedical();
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->countAntecedents();
  $dossier_medical->countAllergies();
  $urgence->_ref_chir->loadRefsFwd();
  
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
  $plage = new CPlageOp;
  $urgence->_alternate_plages = $plage->loadList($where, $order);
  foreach ($urgence->_alternate_plages as $curr_plage) {
    $curr_plage->loadRefsFwd();
  }
}

$anesth = new CMediusers();
$anesths = $anesth->loadAnesthesistes(PERM_READ);

// Liste des types d'anesthésie
$listAnesthType = new CTypeAnesth();
$listAnesthType = $listAnesthType->loadGroupList();

// Création du template
$smarty = new CSmartyDP();

$smarty->debugging = false;

$smarty->assign("urgences"       , $urgences);
$smarty->assign("listBlocs"      ,  $listBlocs);
$smarty->assign("listSalles"     , $listSalles);
$smarty->assign("anesths"        ,    $anesths);
$smarty->assign("date"           , $date);
$smarty->assign("group"          , $group);
$smarty->assign("listAnesthType" , $listAnesthType);

$smarty->display("../../dPsalleOp/templates/vw_urgences.tpl");