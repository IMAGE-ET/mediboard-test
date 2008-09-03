<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: $
* @author Thomas Despoix
*/

$object_class = mbGetValueFromGet("object_class");
$user_id = mbGetValueFromGet("user_id");
$needle = mbGetValueFromPost("_search");
$property = mbGetValueFromGet("property");
$object = new $object_class;
$object->loadAides($user_id, $needle);

$dependField = $object->_helped_fields[$property];

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $object);
$smarty->assign("property", $property);
$smarty->assign("dependField", $dependField);
$smarty->assign("needle", $needle);
$smarty->assign("nodebug", true);

$smarty->display("httpreq_do_aide_autocomplete.tpl");

?>