<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m;


$object_class = mbGetValueFromGet("object_class");
$object_id    = mbGetValueFromGet("object_id");

if (!$object_class || !$object_id) {
  return;
}


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("canSante400", CModule::getCanDo("dPsante400"));
$smarty->assign("object_class",$object_class);
$smarty->assign("object_id",$object_id);
$smarty->display("vw_object_idsante400.tpl");
?>