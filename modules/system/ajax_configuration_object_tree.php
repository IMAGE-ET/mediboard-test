<?php 
/**
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id$
 */

$inherit = CValue::get("inherit");

$object_tree = CConfiguration::getObjectTree($inherit);

$smarty = new CSmartyDP();
$smarty->assign("object_tree", $object_tree);
$smarty->assign("inherit",     $inherit);
$smarty->display("inc_select_configuration_object.tpl");