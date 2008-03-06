<?php

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision: 2165 $
 * @author Sherpa
 * @abstract Retrieve complete properties for a Sherpa MbObject 
 * as an XML file
 */

global $can;
$can->needsAdmin();

$response = array();

$class = mbGetValueFromGet("class");
if (in_array($class, CSpObjectHandler::$queriable)) {
  trigger_error("Object of class 'class' is not queriable", E_USER_WARNING);
  return;
}

$id400 = mbGetValueFromGet("id400");
$object = CSpObjectHandler::getMbObjectFor($class, $id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);
$smarty->display("object_properties.tpl");
?>