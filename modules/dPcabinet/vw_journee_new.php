<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$today = CMbDT::date();
$date = CValue::getOrSession("date", $today);
$function_id = CValue::getOrSession("function_id");

$function = new CFunctions();
$functions = $function->loadListWithPerms(PERM_READ, null, "text");

// smarty
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("function_id", $function_id);
$smarty->assign("functions", $functions);
$smarty->display("vw_journee_new.tpl");