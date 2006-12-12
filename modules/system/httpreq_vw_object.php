<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

$object_class = mbGetValueFromGet("object_class", null);
$object_id    = mbGetValueFromGet("object_id", null);

if($object_class === null || $object_id === null) {
  return;
}

$object = new $object_class;
$object->load($object_id);
$object->loadCompleteView();

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("object", $object);

$smarty->display($object->_view_template);

?>