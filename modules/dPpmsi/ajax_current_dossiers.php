<?php 

/**
 * $Id$
 *  
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkRead();
$date = CValue::getOrSession("date", CMbDT::date());
// Selection des salles
$listSalles = new CSalle;
$listSalles = $listSalles->loadGroupList();

$totalOp = 0;
$counts = array (
  "sejours" => array (
    "total" => 0,
    "facturees" => 0,
  ),
  "operations" => array (
    "total" => 0,
    "facturees" => 0,
  ),
  "urgences" => array (
    "total" => 0,
    "facturees" => 0,
  ),

);

/**
 * Comptage des Interventions planifiées
 */
$plage = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));


/** @var CPlageOp[] $plages */
$plages = $plage->loadList($where);
/** @var COperation[] $operations */
$operations = CPlageOp::massLoadBackRefs($plages, "operations", null, array("annulee" => "= '0'"));

foreach ($plages as $_plage) {
  $_plage->_ref_operations = $_plage->_back["operations"];
  foreach ($_plage->_ref_operations as $_operation) {
    $counts["operations"]["total"]++;
    if ($_operation->facture) {
      $counts["operations"]["facturees"]++;
    }
  }
  $totalOp += count($_plage->_ref_operations);
}

/**
 * Comptage des Interventions hors plages
 */
$operation = new COperation;
$where = array();
$ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
$where["operations.date"]       = "= '$date'";
$where["operations.plageop_id"] = "IS NULL";
$where["operations.annulee"]    = "= '0'";
$where["sejour.group_id"]       = "= '".CGroups::loadCurrent()->_id."'";


/** @var COperation[] $horsplages */
$horsplages = $operation->loadList($where, null, null, null, $ljoin);
$totalOp += count($horsplages);
foreach ($horsplages as $_operation) {
  $counts["urgences"]["total"]++;
  if ($_operation->facture) {
    $counts["urgences"]["facturees"]++;
  }
}

/**
 * Comptage des séjours
 */

$group = CGroups::loadCurrent();
$next = CMbDT::date("+1 day", $date);
$sejour = new CSejour;
$where = array();
$where["entree"] = "< '$next'";
$where["sortie"] = "> '$date'";
$where["group_id"]      = "= '$group->_id'";
$where["annule"]        = "= '0'";
$order = array();
$order[] = "sortie";
$order[] = "entree";

/** @var CSejour[] $listSejours */
$count = $sejour->countList($where);
$listSejours = $sejour->loadList($where, $order);
foreach ($listSejours as $_sejour) {
  $counts["sejours"]["total"]++;
  if ($_sejour->facture) {
    $counts["sejours"]["facturees"]++;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"     , $date);
$smarty->assign("counts"   , $counts);
$smarty->assign("totalOp"  , $totalOp);
$smarty->display("current_dossiers/inc_current_dossiers.tpl");
