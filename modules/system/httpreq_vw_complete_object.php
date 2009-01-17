<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Romain Ollivier
*/

global $can;

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");

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