<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Thomas Despoix
*/

$object_class  = CValue::get("object_class");
$user_id       = CValue::get("user_id");
$property      = CValue::get("property");

$depend_value_1 = CValue::post("depend_value_1", null);
$depend_value_2 = CValue::post("depend_value_2", null);
$needle         = CValue::post("_search");

$object = new $object_class;
$object->loadAides($user_id, $needle, $depend_value_1, $depend_value_2);

// Tableau de depend value
@list($depend_field_1, $depend_field_2) = $object->_specs[$property]->helped;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $object);
$smarty->assign("property", $property);
$smarty->assign("needle", $needle);
$smarty->assign("nodebug", true);
$smarty->assign("depend_field_1", $depend_field_1);
$smarty->assign("depend_field_2", $depend_field_2);

$smarty->display("httpreq_do_aide_autocomplete.tpl");
