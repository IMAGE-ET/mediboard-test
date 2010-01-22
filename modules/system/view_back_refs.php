<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$objects = array();

$object_class = CValue::get("object_class");
$object_ids = CValue::get("object_ids");

// Load compared Object
$max = 30;
$counts = array();
foreach ($object_ids as $object_id) {
  $object = new $object_class;
  $object->load($object_id);
  $object->loadAllBackRefs("0, $max");
  $objects[$object_id] = $object;
	foreach ($object->_back as $backName => $backRefs) {
		$counts[$backName] = @$counts[$backName] + $object->_count[$backName];
	}
}

// Empty object
$object = reset($objects);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("objects", $objects);
$smarty->assign("counts", $counts);
$smarty->assign("object", $object);

$smarty->display("view_back_refs.tpl");

?>