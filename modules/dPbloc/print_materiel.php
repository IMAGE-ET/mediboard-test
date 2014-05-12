<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$now = CMbDT::date();

$filter = new COperation;
$filter->_date_min = CValue::get("_date_min", $now);
$filter->_date_max = CValue::get("_date_max", $now);

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

$order = "operations.date, rank";

$operation = new COperation();

$where["commande_mat"] = "!= '0'";
$operations["1"] = $operation->loadList($where, $order, null, null, $ljoin);

$where["commande_mat"] = "!= '1'";
$where["annulee"]      = "!= '1'";
$operations["0"] = $operation->loadList($where, $order, null, null, $ljoin);

foreach ($operations as $_operations) {
  /** @var COperation[] $_operations */
  foreach ($_operations as $_operation) {
    $_operation->loadRefPatient();
    $_operation->loadRefChir()->loadRefFunction();
    $_operation->loadRefPlageOp();
    $_operation->loadExtCodesCCAM();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"    , $filter);
$smarty->assign("bloc"      , $bloc);
$smarty->assign("operations", $operations);

$smarty->display("print_materiel.tpl");
