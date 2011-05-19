<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$object_class = CValue::get("object_class");

if (!$object_class || !is_subclass_of($object_class, "CMbObject")) {
//	CAppUI::stepAjax("Nom de classe invalide <strong>$object_class</strong>", UI_MSG_ERROR);
}

$tree = CTag::getTree($object_class);

$smarty = new CSmartyDP("modules/system");
$smarty->assign("object_class", $object_class);
$smarty->assign("tree", $tree);
$smarty->assign("root", true);
$smarty->display("vw_object_tag_manager.tpl");