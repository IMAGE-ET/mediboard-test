<?php 

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$object_guid = CValue::get("object_guid");
$object_class = CValue::get("object_class");

$tag = new CTag();
if ($object_guid) {
  $tag = CStoredObject::loadFromGuid($object_guid);
}
else {
  if ($object_class) {
    $tag->object_class = $object_class;
  }
}

if ($tag->_id) {
  $tag->countRefItems();
  $tag->loadRefParent();
}

// smarty
$smarty = new CSmartyDP();
$smarty->assign("tag", $tag);
$smarty->display("inc_edit_tag.tpl");