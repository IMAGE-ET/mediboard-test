<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Alexis Granger
*/

global $AppUI;

$object_class   = CValue::get("object_class");
$field          = CValue::get("field");
$depend_value_1 = CValue::get("depend_value_1");
$depend_value_2 = CValue::get("depend_value_2");
$user_id        = CValue::get("user_id");
$no_enum        = CValue::get("no_enum");

// Chargement des aides
$object = new $object_class;
$object->loadAides($user_id, null, $depend_value_1, $depend_value_2);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object",  $object);
$smarty->assign("field",   $field);
$smarty->assign("no_enum", $no_enum);

$smarty->display("inc_vw_select_aides.tpl");

?>