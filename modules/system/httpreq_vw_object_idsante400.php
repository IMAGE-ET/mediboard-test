<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Alexis Granger
*/

global $can;

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("canSante400", CModule::getCanDo("dPsante400"));
$smarty->assign("object", $object);
$smarty->display("vw_object_idsante400.tpl");
?>