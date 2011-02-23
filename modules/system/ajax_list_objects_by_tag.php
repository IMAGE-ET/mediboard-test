<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$tag_id   = CValue::get("tag_id");
$columns  = CValue::get("col");
$keywords = CValue::get("keywords");

$tag = new CTag;

if (strpos($tag_id, "none") === 0) {
	$parts = explode("-", $tag_id);
	$object_class = $parts[1];
	
	$tag->object_class = $object_class;
	$object = new $object_class;
	
	$where = array(
	  "tag_item_id" => "IS NULL",
	);
	
	$ljoin = array(
	  "tag_item" => "tag_item.object_id = {$object->_spec->table}.{$object->_spec->key} AND tag_item.object_class = '$object_class'",
	);
	
	if (!$keywords) {
		$keywords = "%";
	}
	
  $objects = $object->seek($keywords, $where, 10000, true, $ljoin);
	$count_children = $object->_totalSeek;
}
else {
	$tag->load($tag_id);
	$count_children = $tag->countChildren();
	$objects = $tag->getObjects($keywords);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("objects", $objects);
$smarty->assign("columns", $columns);
$smarty->assign("count_children", $count_children);
$smarty->assign("tag", $tag);
$smarty->display("inc_list_objects_by_tag.tpl");
