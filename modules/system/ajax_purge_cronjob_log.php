<?php 

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$do_purge = CValue::get("do_purge");
$date_max = CValue::get("_date_max");
$months   = CValue::get("months");
$max      = CValue::get("max", 1);
$delete   = CValue::get("delete");

if ($months) {
  $date_max = CMbDT::date("- $months MONTHS");
}

if (!$date_max) {
  CAppUI::stepAjax("Merci d'indiquer une date fin de recherche.", UI_MSG_ERROR);
}

$cronjob_log = new CCronJobLog();
$ds          = $cronjob_log->_spec->ds;

// comptage des echanges à supprimer
$count_delete    = 0;
$date_max_delete = $delete ? CMbDT::date("-6 MONTHS", $date_max) : $date_max;

$where                   = array();
$where["start_datetime"] = "< '$date_max_delete'";
$count_to_delete         = $cronjob_log->countList($where);

CAppUI::stepAjax("{$cronjob_log->_class}-msg-delete_count", UI_MSG_OK, $count_to_delete);

if (!$do_purge) {
  return;
}

$query = "DELETE FROM `{$cronjob_log->_spec->table}`
  WHERE `start_datetime` < '$date_max_delete'
  LIMIT $max";

$ds->exec($query);
$count_delete = $ds->affectedRows();
CAppUI::stepAjax("{$cronjob_log->_class}-msg-deleted_count", UI_MSG_OK, $count_delete);

// on continue si on est en auto
if ($count_to_delete + $count_delete) {
  echo "<script type='text/javascript'>CronJob.purge();</script>";
}
