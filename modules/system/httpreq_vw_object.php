<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Romain Ollivier
*/

global $can;

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");

// Look for view options
$options = CMbArray::filterPrefix($_GET, "view_");

$object->loadView();

$can->read = $object->canRead();
$can->edit = $object->canEdit();
$can->needsRead();

// If no template is defined, use generic
$template = is_file("modules/$object->_view_template") ?
  $object->_view_template : 
  "system/templates/CMbObject_view.tpl";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->assign("props", $object->getDBFields());

// Options
foreach ($options as $key => $value) {
  $smarty->assign($key, $value);
}

$smarty->display("../../$template");
?>