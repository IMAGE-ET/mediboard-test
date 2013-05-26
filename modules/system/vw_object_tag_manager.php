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

CCanDo::checkRead();

$object_class = CValue::get("object_class");

$tree = CTag::getTree($object_class);

$smarty = new CSmartyDP("modules/system");
$smarty->assign("object_class", $object_class);
$smarty->assign("tree", $tree);
$smarty->assign("root", true);
$smarty->display("vw_object_tag_manager.tpl");