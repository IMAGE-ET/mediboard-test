<?php 
/**
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id$
 */

CCanDo::checkRead();

$inherit = CValue::get("inherit");
$module = CValue::get("module");

$all_inherits = array_keys(CConfiguration::getModel($module));

$smarty = new CSmartyDP();
$smarty->assign("module",       $module);
$smarty->assign("inherit",      $inherit);
$smarty->assign("all_inherits", $all_inherits);
$smarty->display("inc_edit_configuration.tpl");