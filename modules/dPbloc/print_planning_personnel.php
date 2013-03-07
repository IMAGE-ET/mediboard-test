<?php

/**
 * dPbloc
 *  
 * @category dPbloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$date_min = CValue::get("_date_min");
$date_max = CValue::get("_date_max");
$salle_id = CValue::get("salle_id");
$bloc_id  = CValue::get("_bloc_id");
$prat_id  = CValue::get("_prat_id");
$specialite = CValue::get("_specialite");

if (is_array($bloc_id)) {
  CMbArray::removeValue("0", $bloc_id);
}

$dates = array();
$date_temp = $date_min;

while ($date_temp < $date_max) {
  $dates[] = $date_temp;
  //$personnel[$date_temp] = array();
  $date_temp = CMbDT::date("+ 1 day", $date_temp);
}

$hours = array();
$hour_temp = CAppUI::conf("dPbloc CPlageOp hours_start");
$hour_max = CAppUI::conf("dPbloc CPlageOp hours_stop");

while ($hour_temp < $hour_max) {
  $hours[] = $hour_temp;
  $hour_temp = CMbDT::time("+1 hour", $hour_temp);
}

$plage = new CPlageOp;
$where = array();

$where["date"] = "BETWEEN '$date_min' AND '$date_max'";

if (!$prat_id && !$specialite) {
  $function = new CFunctions;
  $user = CMediusers::get();
  if (!$user->isFromType(array("Anesthésiste"))) {
    $functions  = $function->loadListWithPerms(PERM_READ);
    $praticiens = $user->loadPraticiens();
  } else {
    $functions = $function->loadList();
    $praticiens = $praticien->loadList();
  }
  $where[] = "plagesop.chir_id ".CSQLDataSource::prepareIn(array_keys($praticiens)) .
             " OR plagesop.spec_id ".CSQLDataSource::prepareIn(array_keys($functions));
}

if ($prat_id) {
  $where["chir_id"] = "= '$prat_id'";
}

if ($specialite) {
  $where["spec_id"] = "= '$specialite'";
}

$salle = new CSalle();
$whereSalle = array();
$whereSalle["bloc_id"] =
  CSQLDataSource::prepareIn(count($bloc_id) ?
    $bloc_id :
    array_keys(CGroups::loadCurrent()->loadBlocs(PERM_READ)));

if ($salle_id) {
  $whereSalle["sallesbloc.salle_id"] = "= '$salle_id'";
}
$listSalles = $salle->loadListWithPerms(PERM_READ, $whereSalle);

$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
$order = "date, salle_id, debut";

$plages = $plage->loadList($where, $order);

$planning   = array();
$personnels = array();

// @todo : gérer les hors plages

foreach ($plages as $_plage) {
  $affectations = $_plage->loadAffectationsPersonnel();
   
  $operations = $_plage->loadRefsOperations(0);
  
  CMbObject::massLoadFwdRef($operations, "plageop_id");
  $praticiens = CMbObject::massLoadFwdRef($operations, "chir_id");
  CMbObject::massLoadFwdRef($praticiens, "function_id");
  
  foreach ($operations as $_operation) {
    $_operation->loadRefPlageOp(1);
    $_operation->loadRefPatient(1);
    $_operation->loadRefChir(1)->loadRefFunction();
    $_operation->updateSalle();
  }
  
  if (count($affectations)) {
    foreach ($affectations as $_affectations_by_type) {
      foreach ($_affectations_by_type as $_affectation) {
        $_affectation->loadRefPersonnel()->loadRefUser();
        
        if (!isset($planning[$_affectation->personnel_id])) {
          $planning[$_affectation->personnel_id] = array();
          $personnels[$_affectation->personnel_id] = $_affectation->_ref_personnel;
        }
        if (!isset($planning[$_affectation->personnel_id][$_plage->date]) && count($operations)) {
          $planning[$_affectation->personnel_id][$_plage->date] = array();
        }
        if (count($operations)) {
          $planning[$_affectation->personnel_id][$_plage->date] = $operations;
        }
      }
    }
  }
  foreach ($operations as $_operation) {
    
    // Personnels ajoutés
    $affectations = $_operation->loadAffectationsPersonnel();
    
    if (count($affectations)) {
      foreach ($affectations as $_affectations_by_type) {
        foreach ($_affectations_by_type as $_affectation) {
          $_affectation->loadRefPersonnel()->loadRefUser();
          
          if (!isset($planning[$_affectation->personnel_id])) {
            $planning[$_affectation->personnel_id] = array();
            $personnels[$_affectation->personnel_id] = $_affectation->_ref_personnel;
          }
        }
      }
    }
  }
}

// Trier le planning par personnel
$sorter = CMbArray::pluck($personnels, "_ref_user", "_view");
array_multisort($sorter, SORT_ASC, $personnels);

array_multisort(array_keys($personnels), SORT_ASC, $planning);

$smarty = new CSmartyDP;

$smarty->assign("date_min"  , $date_min);
$smarty->assign("date_max"  , $date_max);
$smarty->assign("hours"     , $hours);
$smarty->assign("planning"  , $planning);
$smarty->assign("personnels", $personnels);

$smarty->display("print_planning_personnel.tpl");

?>