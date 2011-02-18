<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$object_class = CValue::get("object_class");

if (!$object_class || !is_subclass_of($object_class, "CMbObject")) {
	CAppUI::stepAjax("Nom de classe invalide <strong>$object_class</strong>", UI_MSG_ERROR);
}

$object_guid = CValue::session("object_guid", "$object_class-0");
$columns = CValue::get("col");

$smarty = new CSmartyDP();
$smarty->assign("object_class", $object_class);
$smarty->assign("object_guid", $object_guid);
$smarty->assign("columns", $columns);
$smarty->display("vw_object_tree_explorer.tpl");