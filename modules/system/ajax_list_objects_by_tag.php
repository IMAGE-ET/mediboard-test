<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$tag_id  = CValue::get("tag_id");
$columns = CValue::get("col");

$tag = new CTag;
$tag->load($tag_id);

$objects = $tag->getObjects();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("objects", $objects);
$smarty->assign("columns", $columns);
$smarty->assign("tag", $tag);
$smarty->display("inc_list_objects_by_tag.tpl");
