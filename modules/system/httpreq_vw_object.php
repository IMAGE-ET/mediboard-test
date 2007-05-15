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
$object->loadView();

$canModule = CModule::getCanDo($object->_ref_module->mod_name);
$can->read = $canModule->read && $object->canRead();
$can->needsRead();

// If no template is defined, use generic
$template = is_file("modules/$object->_view_template") ?
  $object->_view_template : 
  "system/templates/CMbObject_view.tpl";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->assign("props", $object->getProps());

$smarty->display("../../$template");
?>