<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

$object_class = mbGetValueFromGet("object_class");
$object_id    = mbGetValueFromGet("object_id");

if ($object_class === null || $object_id === null) {
  return;
}

$object = new $object_class;
$object->load($object_id);
$object->loadView();

if(!$object->canRead()){
  include("access_denied.php");
}else{
  // Création du template
  $smarty = new CSmartyDP();

  $smarty->assign("object", $object);

  $smarty->display($object->_view_template);
}
?>