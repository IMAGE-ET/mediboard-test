<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Romain Ollivier
*/

global $can, $m, $ajax;

$object_class = mbGetValueFromGet("object_class");
$object_id    = mbGetValueFromGet("object_id");

if (!$object_class || !$object_id) {
  return;
}

$object = new $object_class;
$object->load($object_id);
if (!$object->_id) {
  $AppUI->redirect("?ajax=$ajax&suppressHeaders=1&m=$m&a=object_not_found&object_classname=$object_class");
}

$object->loadComplete();

$can->read = $object->canRead();
$can->edit = $object->canEdit();
$can->needsRead();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canSante400", CModule::getCanDo("dPsante400"));

$smarty->assign("object", $object);

$smarty->display("../../$object->_complete_view_template");
?>