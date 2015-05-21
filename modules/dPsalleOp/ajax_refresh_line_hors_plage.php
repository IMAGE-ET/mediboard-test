<?php 

/**
 * $Id$
 *  
 * @category salleOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$operation_id  = CValue::get("operation_id");
$ds = CSQLDataSource::get("std");
$toRemove = false;

// bloc & salles
$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ);
$salle = new CSalle();
$listSalles = $salle->loadListWithPerms(PERM_READ);

// anesths
$anesth = new CMediusers();
$anesths = $anesth->loadAnesthesistes(PERM_READ);

$operation = new COperation();
$operation->load($operation_id);
$date = $operation->date;
if ($operation->plageop_id) {
  $toRemove = true;
}

if (!$toRemove) {
  $reservation_installed = CModule::getActive("reservation");
  $diff_hour_urgence = CAppUI::conf("reservation diff_hour_urgence");

  $operation->loadRefsFwd();
  $operation->loadRefAnesth();
  $patient = $operation->_ref_sejour->loadRefPatient();
  $dossier_medical = $patient->loadRefDossierMedical();
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->countAntecedents();
  $dossier_medical->countAllergies();
  $operation->_ref_chir->loadRefsFwd();

  if ($reservation_installed) {
    $first_log = $operation->loadFirstLog();
    if (abs(CMbDT::hoursRelative($operation->_datetime_best, $first_log->date)) <= $diff_hour_urgence) {
      $operation->_is_urgence = true;
    }
  }

  // Chargement des plages disponibles pour cette intervention
  $operation->_ref_chir->loadBackRefs("secondary_functions");
  $secondary_functions = array();
  foreach ($operation->_ref_chir->_back["secondary_functions"] as $curr_sec_func) {
    $secondary_functions[$curr_sec_func->function_id] = $curr_sec_func;
  }
  $where = array();
  $selectPlages  = "(plagesop.chir_id = %1 OR plagesop.spec_id = %2
      OR plagesop.spec_id ".CSQLDataSource::prepareIn(array_keys($secondary_functions)).")";
  $where[]       = $ds->prepare($selectPlages, $operation->chir_id, $operation->_ref_chir->function_id);
  $where["date"] = "= '$date'";
  $where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
  $order = "salle_id, debut";
  $plage = new CPlageOp;
  $operation->_alternate_plages = $plage->loadList($where, $order);
  foreach ($operation->_alternate_plages as $curr_plage) {
    $curr_plage->loadRefsFwd();
  }
}

// Liste des types d'anesthésie
$listAnesthType = new CTypeAnesth();
$listAnesthType = $listAnesthType->loadGroupList();

$smarty = new CSmartyDP();
$smarty->assign("op"  , $operation);
$smarty->assign("anesths",    $anesths);
$smarty->assign("listSalles", $listSalles);
$smarty->assign("listBlocs",  $listBlocs);
$smarty->assign("to_remove",   $toRemove);
$smarty->assign("listAnesthType" , $listAnesthType);


$smarty->display("../../dPsalleOp/templates/inc_line_hors_plage.tpl");