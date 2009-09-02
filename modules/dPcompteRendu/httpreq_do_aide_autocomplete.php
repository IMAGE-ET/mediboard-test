<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Thomas Despoix
*/

$object_class = mbGetValueFromGet("object_class");
$user_id = mbGetValueFromGet("user_id");
$needle = mbGetValueFromPost("_search");
$property = mbGetValueFromGet("property");

$object = new $object_class;
$object->loadAides($user_id, $needle);

// Tableau de depend value
$helped = $object->_specs[$property]->helped;
$depend_field_1 = @$helped[0];
$depend_field_2 = @$helped[1];

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $object);
$smarty->assign("property", $property);
$smarty->assign("needle", $needle);
$smarty->assign("nodebug", true);
$smarty->assign("depend_field_1", $depend_field_1);
$smarty->assign("depend_field_2", $depend_field_2);

$smarty->display("httpreq_do_aide_autocomplete.tpl");
