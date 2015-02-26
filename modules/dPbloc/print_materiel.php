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

CCanDo::checkRead();
$praticien_id = CValue::getOrSession("praticien_id");
$function_id  = CValue::getOrSession("function_id");

$filter = new COperation;
$filter->_date_min = CValue::getOrSession("_date_min", CMbDT::date("-7 day"));
$filter->_date_max = CValue::getOrSession("_date_max", CMbDT::date());

$blocs = CGroups::loadCurrent()->loadBlocs(PERM_READ, null, "nom");
$bloc_id   = CValue::getOrSession("bloc_id", reset($blocs)->_id);
$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);

// Récupération des opérations
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";

$salles = $bloc->loadRefsSalles();
CStoredObject::filterByPerm($salles, PERM_READ);
$in_salles = CSQLDataSource::prepareIn(array_keys($salles));

$where = array();
$where[] = "plagesop.salle_id $in_salles OR operations.salle_id $in_salles";
$where["materiel"] = "!= ''";
$where["operations.date"] = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";

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
$ops = $operation->loadList($where, $order, null, null, $ljoin);

$operations = array(
  "commandee"   => array(),
  "a_commander" => array(),
);

foreach ($ops as $_op) {
  /** @var COperation $_op */
  $_op->loadRefPatient();
  $_op->loadRefChir()->loadRefFunction();
  $_op->loadRefPlageOp();
  $_op->loadExtCodesCCAM();
  $_op->loadRefCommande();
  $_commande = $_op->_ref_commande_mat;
  if (!$_commande->_id && !$_op->annulee) {
    $operations["a_commander"][$_op->_id] = $_op;
  }
  elseif ($_commande->_id && ($_commande->etat == "commandee" || $_commande->etat == "a_commander")) {
    $operations[$_commande->etat][$_op->_id] = $_op;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"    , $filter);
$smarty->assign("bloc"      , $bloc);
$smarty->assign("operations", $operations);

$smarty->display("print_materiel.tpl");
