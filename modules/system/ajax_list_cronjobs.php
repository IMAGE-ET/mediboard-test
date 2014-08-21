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


$cronjob  = new CCronJob();
/** @var CCronJob[] $cronjobs */
$cronjobs = $cronjob->loadList();

$cronjob->loadLibrary();
foreach ($cronjobs as $_cronjob) {
  $_cronjob->getNextDate();
}

$smarty = new CSmartyDP();
$smarty->assign("cronjobs", $cronjobs);
$smarty->display("inc_list_cronjobs.tpl");