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
$in_salles = CSQLDataSource::prepareIn(array_keys($salles));
$where[] = "plagesop.salle_id $in_salles  OR operations.salle_id $in_salles";

$where["materiel"] = "!= ''";
$date_min = CMbDT::date("-7 day");
$where["operations.date"] = ">= '$date_min'";

$order = "operations.date, rank";

$operation = new COperation;

$where["commande_mat"] = "!= '1'";
$where["annulee"]      = "!= '1'";
$operations["0"] = $operation->loadList($where, $order, null, null, $ljoin);

$where["commande_mat"] = "= '1'";
$where["annulee"]      = "= '1'";
$operations["1"] = $operation->loadList($where, $order, null, null, $ljoin);

foreach ($operations as &$_operations) {
  /** @var COperation[] $_operations */
  foreach ($_operations as $_operation) {
    $_operation->loadRefPatient();
    $_operation->loadRefChir();
    $_operation->_ref_chir->loadRefFunction();
    $_operation->loadRefPlageOp();
    $_operation->loadExtCodesCCAM();
  }
}

$smarty = new CSmartyDP;

$smarty->assign("operations", $operations);

$smarty->display("inc_vw_materiel.tpl");
