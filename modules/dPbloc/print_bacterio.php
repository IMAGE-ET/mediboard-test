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

$in_salles = CSQLDataSource::prepareIn($bloc->loadBackIds("salles"));
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
$where[] = "operations.salle_id $in_salles OR plagesop.salle_id $in_salles";
$where[] = "operations.date = '$date'";
$where["labo"] = "= 1";
$order = "entree_salle, time_operation";

$operation = new COperation();
/** @var COperation[] $operations */
$operations = $operation->loadList($where, $order, null, null, $ljoin);
CMbObject::massLoadFwdRef($operations, "plageop_id");
$chirs = CMbObject::massLoadFwdRef($operations, "chir_id");
CMbObject::massLoadFwdRef($chirs, "function_id");
$sejours = CMbObject::massLoadFwdRef($operations, "sejour_id");
CMbObject::massLoadFwdRef($sejours, "patient_id");
foreach ($operations as $_operation) {
  $_operation->loadRefPatient();
  $_operation->loadRefPlageOp();
  $_operation->updateSalle();
  $_operation->loadRefChir()->loadRefFunction();
  $_operation->loadExtCodesCCAM();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"      , $date);
$smarty->assign("bloc"      , $bloc);
$smarty->assign("operations", $operations);

$smarty->display("print_bacterio.tpl");
