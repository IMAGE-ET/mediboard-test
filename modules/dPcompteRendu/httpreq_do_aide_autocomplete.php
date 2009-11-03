<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Thomas Despoix
*/

$object_class = CValue::get("object_class");
$user_id      = CValue::get("user_id");
$property     = CValue::get("property");

// In order to take the first value (the key may change, but there is always only one pair)
$needle       = reset($_POST); 

$object = new $object_class;
$object->loadAides($user_id, $needle);

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
