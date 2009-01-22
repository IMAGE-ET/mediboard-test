<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:  $
* @author Alexis Granger
*/

global $AppUI;

$object_class   = mbGetValueFromGet("object_class");
$field          = mbGetValueFromGet("field");
$depend_value_1 = mbGetValueFromGet("depend_value_1");
$depend_value_2 = mbGetValueFromGet("depend_value_2");
$user_id        = mbGetValueFromGet("user_id");

// Chargement des aides
$object = new $object_class;
$object->loadAides($user_id, null, $depend_value_1, $depend_value_2);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $object);
$smarty->assign("field"   , $field);

$smarty->display("inc_vw_select_aides.tpl");

?>