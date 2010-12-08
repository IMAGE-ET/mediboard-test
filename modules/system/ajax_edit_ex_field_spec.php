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
    $prop = $spec_type." ".substr($prop, strpos($prop, " ")+1);
  }
}

$spec = @CMbFieldSpecFact::getSpec($object, $field, $prop);
$options = $spec->getOptions();

$classes = $spec instanceof CRefSpec ? CApp::getMbClasses() : array();

$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->assign("field", $field);
$smarty->assign("prop", $prop);
$smarty->assign("spec", $spec);
$smarty->assign("options", $options);
$smarty->assign("other_fields", $other_fields);
$smarty->assign("classes", $classes);
$smarty->display("inc_edit_ex_field_spec.tpl");