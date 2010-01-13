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

$filter = new COperation;
$filter->_date_min = CValue::get("_date_min", $now);
$filter->_date_max = CValue::get("_date_max", $now);

$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ, null, "nom");
$bloc_id   = CValue::getOrSession("bloc_id", reset($listBlocs)->_id);

// Récupération des opérations
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";

$where = array();

$salle = new CSalle();
$whereSalle = array("bloc_id" => "= '$bloc_id'");
$where["plagesop.salle_id"] = CSQLDataSource::prepareIn(array_keys($salle->loadListWithPerms(PERM_READ, $whereSalle)));

$where["materiel"] = "!= ''";
$where["operations.plageop_id"] = "IS NOT NULL";
$where[] = "plagesop.date BETWEEN '$filter->_date_min' AND '$filter->_date_max'";

$order = "plagesop.date, rank";

$operation = new COperation();

$where["commande_mat"] = "!= '0'";
$operations["1"] = $operation->loadList($where, $order, null, null, $ljoin);

$where["commande_mat"] = "!= '1'";
$where["annulee"]      = "!= '1'";
$operations["0"] = $operation->loadList($where, $order, null, null, $ljoin);

foreach($operations as &$_operations) {
	foreach($_operations as $_operation) {
	  $_operation->loadRefPatient(1);
	  $_operation->loadRefChir(1);
	  $_operation->_ref_chir->loadRefFunction();
	  $_operation->loadRefPlageOp(1);
	  $_operation->loadExtCodesCCAM();
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter", $filter);
$smarty->assign("operations", $operations);

$smarty->display("print_materiel.tpl");

?>