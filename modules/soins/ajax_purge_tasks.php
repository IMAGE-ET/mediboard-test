<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkAdmin();
$type = CValue::get("type", "check");

$where = array();
$where["realise"] = " = '1'";
$where[] = "date_realise IS NULL";
$where[] = "author_realise_id IS NULL";
$where[] = "author_id IS NOT NULL";

$task = new CSejourTask();
if ($type == "repair") {
  $tasks = $task->loadList($where, null, 100);
}
else {
  $tasks = $task->loadIds($where);
}

CAppUI::stepAjax("Taches à corriger: ".count($tasks), UI_MSG_OK);

if ($type == "repair") {
  $correction = 0;
  foreach ($tasks as $_task) {
    /* @var CSejourTask $_task*/
    $_task->date_realise = $_task->date;
    $_task->author_realise_id = $_task->author_id;
    if ($msg = $_task->store()) {
      mbTrace($msg);
    }
    else {
      $correction++;
    }
  }
  CAppUI::stepAjax("Taches corrigés: $correction", UI_MSG_OK);
}
