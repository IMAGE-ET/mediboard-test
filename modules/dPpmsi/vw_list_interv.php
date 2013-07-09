<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$date = CValue::getOrSession("date", CMbDT::date());

// Selection des salles
$listSalles = new CSalle;
$listSalles = $listSalles->loadGroupList();

$totalOp = 0;

$counts = array (
  "operations" => array (
    "total" => 0,
    "facturees" => 0,
  ),
  "urgences" => array (
    "total" => 0,
    "facturees" => 0,
  ),
);

$plage = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
$order = "debut";

/** @var CPlageOp[] $plages */
$plages = $plage->loadList($where, $order);
foreach ($plages as $_plage) {
  foreach ($_plage->loadRefsOperations(false) as $_operation) {
    // Détails de l'opérations
    $_operation->loadRefChir()->loadRefFunction();
    $_operation->loadExtCodesCCAM();
    $_operation->countExchanges();
    $_operation->countDocItems();

    // Détails du séjour
    $sejour = $_operation->loadRefSejour();
    $sejour->loadNDA();
    $sejour->countDocItems();
    $sejour->loadRefPatient()->loadIPP();

    $counts["operations"]["total"]++; 
    if (count($_operation->_nb_exchanges)) {
      $counts["operations"]["facturees"]++; 
    }
  }

  $totalOp += count($_plage->_ref_operations);
}

$operation = new COperation;
$where = array();
$ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
$where["operations.date"]       = "= '$date'";
$where["operations.plageop_id"] = "IS NULL";
$where["operations.annulee"]    = "= '0'";
$where["sejour.group_id"]       = "= '".CGroups::loadCurrent()->_id."'";
$order = "operations.chir_id";

/** @var COperation[] $horsplages */
$horsplages = $operation->loadList($where, $order, null, null, $ljoin);
$totalOp += count($horsplages);
foreach ($horsplages as $_operation) {
  $_operation->loadRefChir()->loadRefFunction();
  $_operation->loadExtCodesCCAM();
  $_operation->countExchanges();
  $_operation->countDocItems();

  $sejour = $_operation->loadRefSejour();
  $sejour->loadNDA();
  $sejour->countDocItems();
  $sejour->loadRefPatient()->loadIPP();

  $counts["urgences"]["total"]++;
  if (count($_operation->_nb_exchanges)) {
    $counts["urgences"]["facturees"]++;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"     , $date);
$smarty->assign("operation", $operation);
$smarty->assign("plages"   , $plages);
$smarty->assign("urgences" , $horsplages);
$smarty->assign("counts"   , $counts);
$smarty->assign("totalOp"  , $totalOp);

$smarty->display("vw_list_interv.tpl");
