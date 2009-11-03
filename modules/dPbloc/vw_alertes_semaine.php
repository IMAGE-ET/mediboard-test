<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;

$date    = CValue::get("date", mbDate());
$bloc_id = CValue::get("bloc_id");

$date = mbDate("last sunday", $date);
$fin  = mbDate("next sunday", $date);
$date = mbDate("+1 day", $date);

$salle = new CSalle();
$where = array();
$where["bloc_id"] = "= '$bloc_id'";
$listSalles = $salle->loadList($where);

$operation = new COperation();
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
$where = array();
$where["plagesop.date"] = "BETWEEN '$date' AND '$fin'";
$where["plagesop.salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
$where["operations.annulee"] = "= '0'";
$where["operations.rank"] = "= '0'";
$order = "plagesop.date, plagesop.chir_id";

$listOperations = $operation->loadList($where, $order, null, null, $ljoin);

foreach($listOperations as &$op) {
  $op->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listOperations", $listOperations);

$smarty->display("vw_alertes_semaine.tpl");

?>