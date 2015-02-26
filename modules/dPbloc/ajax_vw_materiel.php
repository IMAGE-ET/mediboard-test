<?php 
/**
 * $Id:$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkEdit();
$bloc_id      = CValue::getOrSession("bloc_id");
$date_min     = CValue::getOrSession("_date_min", CMbDT::date("-7 day"));
$date_max     = CValue::getOrSession("_date_max", CMbDT::date());
$praticien_id = CValue::getOrSession("praticien_id");
$function_id  = CValue::getOrSession("function_id");

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
$where[] = " operations.date BETWEEN '$date_min' AND '$date_max'";
if ($praticien_id) {
  $where["operations.chir_id"] = " = '$praticien_id'";
}
elseif ($function_id) {
  $mediuser = new CMediusers();
  $users = $mediuser->loadProfessionnelDeSante(PERM_READ, $function_id);
  $where["operations.chir_id"] = CSQLDataSource::prepareIn(array_keys($users));
}

$order = "operations.date, rank";

$operation = new COperation();
$ops = $operation->loadList($where, $order, null, "operation_id", $ljoin);

$operations = array();
$commande = new CCommandeMaterielOp();
foreach ($commande->_specs["etat"]->_list as $spec) {
  $operations[$spec] = array();
}

foreach ($ops as $_op) {
  /** @var COperation $_op */
  $_op->loadRefPatient();
  $_op->loadRefChir()->loadRefFunction();
  $_op->loadRefPlageOp();
  $_op->loadExtCodesCCAM();
  $_op->loadRefCommande();
  if (!$_op->_ref_commande_mat->_id && !$_op->annulee) {
    $operations["a_commander"][$_op->_id] = $_op;
  }
  elseif ($_op->_ref_commande_mat->_id) {
    $operations[$_op->_ref_commande_mat->etat][$_op->_id] = $_op;
  }
}

$smarty = new CSmartyDP;

$smarty->assign("operations", $operations);

$smarty->display("inc_vw_materiel.tpl");
