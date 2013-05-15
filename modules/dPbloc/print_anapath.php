<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$date    = CValue::get("date");
$bloc_id = CValue::get("bloc_id");

$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);
$bloc->loadRefsSalles();
$inSalle = CSQLDataSource::prepareIn(array_keys($bloc->_ref_salles));

$op = new COperation();
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
$where = array();
$where[] = "operations.salle_id $inSalle OR plagesop.salle_id $inSalle";
$where[] = "operations.date = '$date' OR plagesop.date = '$date'";
$where["anapath"] = "= 1";
$order = "entree_salle, time_operation";
/** @var COperation[] $operations */
$operations = $op->loadList($where, $order, null, null, $ljoin);
foreach ($operations as $_op) {
  $_op->loadRefsFwd();
  $_op->updateSalle();
  $_op->_ref_sejour->loadRefPatient();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("date", $date);
$smarty->assign("bloc", $bloc);
$smarty->assign("operations", $operations);

$smarty->display("print_anapath.tpl");
