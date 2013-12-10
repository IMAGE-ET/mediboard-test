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

$inSalle = CSQLDataSource::prepareIn($bloc->loadBackIds("salles"));
$op = new COperation();
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
$where = array();
$where[] = "operations.salle_id $inSalle OR plagesop.salle_id $inSalle";
$where[] = "'$date' IN (operations.date, plagesop.date)";
$where["labo"] = "= 1";
$order = "entree_salle, time_operation";
/** @var COperation[] $operations */

$operations = $op->loadList($where, $order, null, null, $ljoin);
CMbObject::massLoadFwdRef($operations, "plageop_id");
$chirs = CMbObject::massLoadFwdRef($operations, "chir_id");
CMbObject::massLoadFwdRef($chirs, "function_id");
$sejours = CMbObject::massLoadFwdRef($operations, "sejour_id");
CMbObject::massLoadFwdRef($sejours, "patient_id");

foreach ($operations as $_op) {
  $_op->loadRefSejour()->loadRefPatient();
  $_op->loadRefChir()->loadRefFunction();
  $_op->loadRefPlageOp();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"      , $date);
$smarty->assign("bloc"      , $bloc);
$smarty->assign("operations", $operations);

$smarty->display("print_bacterio.tpl");
