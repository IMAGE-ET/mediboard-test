<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage sytem
* @version $Revision: $
* @author Thomas Despoix
*/

global $can;

$objects = array();

$object_class = mbGetValueFromGet("object_class");
$object_ids = mbGetValueFromGet("object_ids");

// Load compared Object
foreach ($object_ids as $object_id) {
  $object = new $object_class;
  $object->load($object_id);
  $object->loadAllBackRefs();
  $objects[$object_id] = $object;
}

// Empty object
$object = reset($objects);
unset($object->_back["logs"]);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("objects", $objects);
$smarty->assign("object", $object);

$smarty->display("view_back_refs.tpl");

?>