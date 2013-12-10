<?php

/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkAdmin();

$limit = 1000;

$date = null;
//$date = "2013-09-23";

$queryCount = "SELECT consultation_anesth.operation_id,
    COUNT( consultation_anesth.operation_id ) AS total,
    GROUP_CONCAT( consultation_anesth.`consultation_anesth_id` ) as doublons
  FROM consultation_anesth\n";
if ($date) {
  $queryCount .= "LEFT JOIN consultation
    ON consultation.consultation_id = consultation_anesth.consultation_id
  LEFT JOIN plageconsult
    ON consultation.plageconsult_id = plageconsult.plageconsult_id
  WHERE plageconsult.date = '$date'\n";
}

$queryCount .= "GROUP BY consultation_anesth.operation_id
  HAVING total > 1
  ORDER BY consultation_anesth.operation_id DESC\n";

$query = $queryCount . "LIMIT $limit";

$dossier = new CConsultAnesth();
$ds = $dossier->_spec->ds;

$count  = $ds->countRows($queryCount);
$result = $ds->loadHashAssoc($query);

CAppUI::setMsg("$count dossier(s) restants en doublon avant traitement", UI_MSG_OK);

$log = new CUserLog();
foreach ($result as $_doublon) {
  $_doublon_ids = explode(",", $_doublon["doublons"]);
  foreach ($_doublon_ids as $_doublon_id) {
    $infoAnesth = false;
    $consultAnesth = new CConsultAnesth();
    $consultAnesth->load($_doublon_id);
    $consultAnesth->loadLogs();
    foreach ($consultAnesth->_ref_logs as $_log) {
      if ($_log->type != "create" && $_log->fields != "operation_id" && $_log->fields != "operation_id sejour_id") {
        $infoAnesth = $_log->fields;
      }
    }
    if (count($consultAnesth->_ref_logs) < 3 && !$infoAnesth) {
      if($msg = $consultAnesth->delete()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
      else {
        CAppUI::setMsg("Dossier supprimés", UI_MSG_OK);
      }
      break;
    }
  }
}

echo CAppUI::getMsg();