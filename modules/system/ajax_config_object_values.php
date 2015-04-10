<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$object_id           = CValue::get("object_id");
$object_configs_guid = CValue::get("object_configs_guid");

list($object_configs_class, $object_configs_id) = explode('-', $object_configs_guid);

$default_config = new $object_configs_class;
$default_config->valueDefaults();
$default_config->_default_specs_values = true;

$object = new $object_configs_class;
$object->object_id = $object_id;
$object->loadMatchingObject();
  
// Recherche s'il existe des valeurs par défauts 
$where = array();
$where["object_id"]    = " IS NULL";
$default = new $object_configs_class;
$default->loadObject($where);
$default->_default_specs_values = true;

$object->_default_specs_values = ($object->_id && $default->_id) ? false : true;

if (!$object->_id && !$object_id) {
  $object = $default;
}

$fields = $object->getPlainFields();
unset($fields[$object->_spec->key]);
unset($fields["object_id"]);

if (!isset($object->_categories)) {
  $name = $object_id ? "$object_configs_class-$object_id" : $object_configs_class;
  $categories = array("$name" => array_keys($fields));
}
else {
  $categories = $object->_categories;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object"        , $object);
$smarty->assign("default"       , $default);
$smarty->assign("default_config", $default_config);
$smarty->assign("fields"        , $fields);
$smarty->assign("categories"    , $categories);
$smarty->display("inc_config_object_values.tpl");
