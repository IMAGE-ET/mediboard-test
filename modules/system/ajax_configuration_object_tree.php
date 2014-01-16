<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$inherit = CValue::get("inherit");
$uid     = CValue::get("uid");

$object_tree = CConfiguration::getObjectTree($inherit);

$smarty = new CSmartyDP();
$smarty->assign("object_tree", $object_tree);
$smarty->assign("inherit",     $inherit);
$smarty->assign("uid",         $uid);
$smarty->display("inc_select_configuration_object.tpl");