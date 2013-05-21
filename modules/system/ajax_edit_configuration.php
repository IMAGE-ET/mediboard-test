<?php 
/**
 * $Id$
 *
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id$
 */

$inherit = CValue::get("inherit");
$module  = CValue::get("module");

if (!is_array($inherit)) {
  $inherit = array($inherit);
}

$all_inherits = array_keys(CConfiguration::getModel($inherit));

$smarty = new CSmartyDP();
$smarty->assign("module",       $module);
$smarty->assign("inherit",      $inherit);
$smarty->assign("all_inherits", $all_inherits);
$smarty->display("inc_edit_configuration.tpl");