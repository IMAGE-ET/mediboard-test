<?php 

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$log_id = CValue::get("log_id");
$log = new CLongRequestLog();
$log->load($log_id);
$log->getLink();

$smarty = new CSmartyDP();
$smarty->assign("log", $log);

$smarty->display("edit_long_request_log.tpl");