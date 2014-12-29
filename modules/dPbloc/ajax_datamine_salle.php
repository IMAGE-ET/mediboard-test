<?php 

/**
 * $Id$
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$limit        = CValue::get("limit", CAppUI::conf("dataminer_limit"));
$miner_class  = CValue::get("miner_class");
$phase        = CValue::get("phase");
$auto         = CValue::get("auto");

$miner = new $miner_class();
if (!$miner instanceof CDailySalleMiner) {
  trigger_error("Wrong miner class", E_USER_ERROR);
  return;
}

$report = $miner->mineSome($limit, $phase);

CAppUI::stepAjax("Miner: %s. Success mining count is '%s'", UI_MSG_OK, $miner_class, $report["success"]);

if ($report["failure"]) {
  CAppUI::stepAjax("Miner: %s. Failure mining counts is '%s'", UI_MSG_ERROR, $miner_class, $report["failure"]);
}

if ($auto) {
  CAppUI::js('submitMine();');
}