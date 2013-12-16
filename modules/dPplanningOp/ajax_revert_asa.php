<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage salleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */
CCanDo::checkEdit();

$ds   = CSQLDataSource::get("std");
$view = CValue::get("view", 1);
$date = CMbDT::date();

$request = "SELECT `operations`.*
            FROM `operations`
            LEFT JOIN `plagesop` ON `operations`.`plageop_id` = `plagesop`.`plageop_id`
            WHERE `operations`.`ASA` = '1'
            AND (`plagesop`.`date` >= '".$date."'
            OR `operations`.`date` >= '".$date."')
            AND NOT EXISTS (
              SELECT * FROM `consultation_anesth`
              WHERE `consultation_anesth`.`operation_id` = `operations`.`operation_id`
            );";
$resultats = $ds->loadList($request);

if ($view == false) {
  $request = "UPDATE `operations`
            LEFT JOIN `plagesop` ON `operations`.`plageop_id` = `plagesop`.`plageop_id`
            SET `operations`.`ASA` = NULL
            WHERE `operations`.`ASA` = '1'
            AND (`plagesop`.`date` >= '".$date."'
            OR `operations`.`date` >= '".$date."')
            AND NOT EXISTS (
              SELECT * FROM `consultation_anesth`
              WHERE `consultation_anesth`.`operation_id` = `operations`.`operation_id`
            );";
  $ds->query($request);
  $result = $ds->affectedRows();

  CAppUI::stepAjax(count($resultats)." intervention(s) modifiée(s)", UI_MSG_OK);
}
else {
  $where = array();
  $where["operation_id"] = CSQLDataSource::prepareIn(CMbArray::pluck($resultats, "operation_id"));
  /* @var COperation[] $operations*/
  $operation = new COperation();
  $operations = $operation->loadList($where);

  foreach ($operations as $op) {
    $op->loadRefPraticien();
    $op->loadRelPatient();
    $op->loadRefPlageOp();
  }

  // Creation du template
  $smarty = new CSmartyDP();
  $smarty->assign("operations" , $operations);
  $smarty->display("check_score_asa.tpl");
}