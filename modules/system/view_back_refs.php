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
$object_ids = split(",", mbGetValueFromGet("object_ids"));

// Load compared Object
foreach ($object_ids as $object_id) {
  $object = new $object_class;
  $object->load($object_id);
  $object->loadAllBackRefs();
  $objects[$object_id] = $object;
}

// Empty object
$object = new $object_class;
$backSpecs = $object->_backSpecs;
unset($backSpecs["logs"]);
$object->_backSpecs = array_reverse($backSpecs);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("objects", $objects);
$smarty->assign("object", $object);

$smarty->display("view_back_refs.tpl");

?>