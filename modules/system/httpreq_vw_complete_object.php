<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$object_class = mbGetValueFromGet("object_class");
$object_id    = mbGetValueFromGet("object_id");

if (!$object_class || !$object_id) {
  return;
}

$object = new $object_class;
$object->load($object_id);
$object->loadComplete();

$can->read = $object->canRead();
$can->needsRead();

// If no template is defined, use generic
$template = is_file($object->_view_template) ?
  $object->_view_template : 
  "system/templates/CMbObject_view.tpl";

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->display("../../$object->_complete_template");
?>