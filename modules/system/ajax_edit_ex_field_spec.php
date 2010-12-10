<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$spec_type    = CValue::get("spec_type");
$prop         = CValue::get("prop");
$class        = CValue::get("class");
$field        = CValue::get("field", "field");
$ex_field_id  = CValue::get("ex_field_id");
$other_fields = CValue::get("other_fields", array());

if (!$field) {
  $field = "field";
}

$object = new $class;
$object->$field = null;

$prop_type = explode(" ", $prop);
$prop_type = reset($prop_type);

if ($spec_type) {
  if (!array_key_exists($prop_type, CMbFieldSpecFact::$classes)) {
    $prop = "$spec_type $prop";
  }
  else {
    if (strpos($prop, " ") !== false) {
      $prop = $spec_type." ".substr($prop, strpos($prop, " ")+1);
    }
    else {
      $prop = $spec_type;
    }
  }
}

$spec = @CMbFieldSpecFact::getSpec($object, $field, $prop);
$options = $spec->getOptions();

// to get the right locales
if ($spec instanceof CEnumSpec && $ex_field_id) {
  $ex_field = new CExClassField;
  $ex_field->load($ex_field_id);
  $enum_trans = $ex_field->loadRefEnumTranslations();
  
  foreach($enum_trans as $_enum_trans) {
    $_enum_trans->updateLocales();
  }
  
  $ex_object = new CExObject;
  $ex_object->_ex_class_id = $ex_field->ex_class_id;
  $ex_object->setExClass();
  
  if ($ex_object->_specs[$field] instanceof CEnumSpec) {
    $spec = $ex_object->_specs[$field];
  }
}

$classes = $spec instanceof CRefSpec ? CApp::getMbClasses() : array();

$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->assign("field", $field);
$smarty->assign("prop", $prop);
$smarty->assign("spec", $spec);
$smarty->assign("options", $options);
$smarty->assign("ex_field_id", $ex_field_id);
$smarty->assign("other_fields", $other_fields);
$smarty->assign("classes", $classes);
$smarty->display("inc_edit_ex_field_spec.tpl");