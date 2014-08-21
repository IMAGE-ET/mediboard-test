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

$identifiant = CValue::get("identifiant");

$cronjob = new CCronJob();
$cronjob->load($identifiant);

$smarty = new CSmartyDP();
$smarty->assign("cronjob", $cronjob);
$smarty->display("inc_edit_cronjob.tpl");