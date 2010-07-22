<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$object_class = CValue::get("object_class", "CGroups");
$id400 = new CIdSante400();
$id400->object_class = $object_class;

// Find groups
$objects = array();
$object_ids = array();
$id400->tag = "eCap";
foreach ($id400->loadMatchingList() as $_id400) {
  $_id400->loadTargetObject();
	$object = $_id400->_ref_object;
	$objects[$object->_id] = $object;
	$object_ids[$object->_id] = array(
	 "eCap" => $_id400,
	);
}

// Object tags (true meaning editable)
$object_tags = array (
  "CGroups" => array (
    "eCap" => false,
    "eCap SHS" => false,
    "eCap URGSER" => true,
  ),
  "CService" => array (
    "eCap" => false,
  ),
  "CSalle" => array (
    "eCap" => false,
  ),
  "CBlocOperatoire" => array (
    "eCap" => false,
  ),
);

// Find other ids for groups
foreach ($objects as $_object) {
	foreach ($object_tags[$object_class] as $_tag => $_editable) {
	  $id400 = new CIdSante400();
	  $id400->loadLatestFor($_object, $_tag);
    $object_ids[$_object->_id][$_tag] = $id400;
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("objects"     , $objects);
$smarty->assign("object_class", $object_class);
$smarty->assign("object_ids"  , $object_ids);
$smarty->assign("object_tags" , $object_tags);

$smarty->display("vw_identifiants.tpl");
?>