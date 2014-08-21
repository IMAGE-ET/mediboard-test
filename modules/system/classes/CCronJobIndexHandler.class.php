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
 
/**
 * CronJob handler
 */
class CCronJobIndexHandler extends CMbIndexHandler {
  /**
   * @see parent::onAfterMain()
   */
  function onAfterMain() {
    $cron_log_id = CValue::get("execute_cron_log_id");
    if (!$cron_log_id) {
      return;
    }
    //Mise à jour du statut du log suite à l'appel au script
    $cron_log = new CCronJobLog();
    $cron_log->load($cron_log_id);
    if (CCronJobLog::$log) {
      $cron_log->status = "error";
      $cron_log->error  = CCronJobLog::$log;
      $cron_log->end_datetime = CMbDT::dateTime();
    }
    else {
      $cron_log->status = "finished";
      $cron_log->end_datetime = CMbDT::dateTime();
    }
    $cron_log->store();
  }
}