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
$object_class = CValue::get("object_class");
$insertion = CValue::get("insertion");

$tag = new CTag;

/*if ($keywords){
  $object = new $object_class;
  $objects = $object->seek($keywords, null, 10000, true);
  $count_children = $object->_totalSeek;
}

else {*/
  if (strpos($tag_id, "all") === 0) {
    $parts = explode("-", $tag_id);
    $object_class = $parts[1];
    
		/**
		 * @var CMbObject
		 */
    $object = new $object_class;
    
    if (!$keywords) {
      $keywords = "%";
    }
    
    $objects = $object->seek($keywords, null, 10000, true);
		foreach($objects as $_object)  {
			$_object->loadRefsTagItems();
		}
		
    $count_children = $object->_totalSeek;
  }
	elseif (strpos($tag_id, "none") === 0) {
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
//}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("objects", $objects);
$smarty->assign("columns", $columns);
$smarty->assign("insertion", $insertion);
$smarty->assign("count_children", $count_children);
$smarty->assign("tag", $tag);
$smarty->display("inc_list_objects_by_tag.tpl");
