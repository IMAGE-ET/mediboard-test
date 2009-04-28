<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsRead();

$now = mbDate();

$filter = new CMateriel;
$filter->_date_min = mbGetValueFromGet("_date_min", $now);
$filter->_date_max = mbGetValueFromGet("_date_max", $now);

// Récupération des opérations
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";

$where = array();

$salle = new CSalle();
$whereSalle = array("bloc_id" => CSQLDataSource::prepareIn(array_keys(CGroups::loadCurrent()->loadBlocs(PERM_READ))));
$where["plagesop.salle_id"] = CSQLDataSource::prepareIn(array_keys($salle->loadListWithPerms(PERM_READ, $whereSalle)));

$where["materiel"] = "!= ''";
$where["operations.plageop_id"] = "IS NOT NULL";
$where[] = "plagesop.date BETWEEN '$filter->_date_min' AND '$filter->_date_max'";

$order = "plagesop.date, rank";

$operation = new COperation();

$where["commande_mat"] = "!= '1'";
$operations1 = $operation->loadList($where, $order, null, null, $ljoin);
foreach($operations1 as &$op1) {
  $op1->loadRefsFwd();
  $op1->_ref_sejour->loadRefsFwd();
}

$where["commande_mat"] = "!= '0'";
$operations2 = $operation->loadList($where, $order, null, null, $ljoin);
foreach($operations2 as &$op2) {
  $op2->loadRefsFwd();
  $op2->_ref_sejour->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("op1", $operations1);
$smarty->assign("op2", $operations2);

$smarty->display("print_materiel.tpl");

?>