<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$bloc_id   = CValue::getOrSession("bloc_id");

// Récupération des salles
$salle = new CSalle();
$where["bloc_id"] = "= '$bloc_id'";
$salles = $salle->loadListWithPerms(PERM_READ, $where);

// Récupération des opérations
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";

$where = array();
$where["plagesop.salle_id"] = CSQLDataSource::prepareIn(array_keys($salles));

$where["materiel"] = "!= ''";
$where["operations.plageop_id"] = "IS NOT NULL";
$where["plagesop.date"] = ">= '".mbDate("-7 day")."'";

$order = "plagesop.date, rank";

$operation = new COperation;

$where["commande_mat"] = "!= '1'";
$where["annulee"]      = "!= '1'";
$operations["0"] = $operation->loadList($where, $order, null, null, $ljoin);

$where["commande_mat"] = "= '1'";
$where["annulee"]      = "= '1'";
$operations["1"] = $operation->loadList($where, $order, null, null, $ljoin);

foreach($operations as &$_operations) {
  foreach($_operations as $_operation) {
    $_operation->loadRefPatient(1);
    $_operation->loadRefChir(1);
    $_operation->_ref_chir->loadRefFunction();
    $_operation->loadRefPlageOp(1);
    $_operation->loadExtCodesCCAM();
  }
}

$smarty = new CSmartyDP;

$smarty->assign("operations", $operations);

$smarty->display("inc_vw_materiel.tpl");
