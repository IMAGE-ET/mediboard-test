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

$log_cron = new CCronJobLog();
$log_cron->_date_min = CMbDT::dateTime("-7 DAY");
$log_cron->_date_max = CMbDT::dateTime("+1 DAY");

$smarty = new CSmartyDP();
$smarty->assign("log_cron", $log_cron);
$smarty->display("vw_cronjob.tpl");