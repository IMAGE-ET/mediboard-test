<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$prop            = CValue::get("prop");
$spec_type       = CValue::get("_spec_type");

$form_name       = CValue::get("form_name");
$context_guid    = CValue::get("context_guid");

$context = CMbObject::loadFromGuid($context_guid);
$context->loadView();

$list_owner = $context->getRealListOwner();
$list_owner->loadView();

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

$spec = CExConcept::getConceptSpec($prop);

// UGLY hack because of the default value of the Boolspec
if ($spec instanceof CBoolSpec && strpos($prop, "default|") === false) {
	$spec->default = null;
}

$exclude = array(
  "confidential", "mask", "format", "reported", 
	"perm", "seekable", "pattern", "autocomplete", 
	"cascade", "delimiter", "canonical", "protected", 
	"class", "alphaAndNum"
);

$options = $spec->_options;

foreach($exclude as $_exclude) {
	unset($options[$_exclude]);
}

function order_specs($a, $b) {
  $options_order = array(
    "list",
    "notNull",
    "vertical",
    "typeEnum",
    "decimals",
    "length",
    "maxLength",
    "minLength",
    "min",
    "max",
    "pos",
    "progressive",
    
    "ccam",
    "cim10",
    "adeli",
    "insee",
    "rib",
    "siret",
    "order_number",
    
    "class",
    "cascade",
  );
  
  $key_a = array_search($a, $options_order);
  $key_b = array_search($b, $options_order);
  
  return ($key_a === false ? 1000 : $key_a) - ($key_b === false ? 1000 : $key_b);
}

function order_items($a, $b) {
  $order = $GLOBALS["items"];
  
  $key_a = array_search($a, $order);
  $key_b = array_search($b, $order);
  
  return ($key_a === false ? 1000 : $key_a) - ($key_b === false ? 1000 : $key_b);
}

uksort($options, "order_specs");

$items_sub = array();
$items_all = array();

if ($spec instanceof CEnumSpec) {
	if($list_owner->_id) {
	  $list_owner->updateEnumSpec($spec);
		$prop .= " ".implode("|", $spec->_list);
	}
	
  $items_sub = $spec->_list;
  $items_all = $spec->_list;
}

// to get the right locales
/*
if ($spec instanceof CEnumSpec) {
  $ex_field = new CExClassField;
  $ex_field->load($ex_field_id);
	
  $enum_trans = $ex_field->loadRefEnumTranslations();
  
  foreach($enum_trans as $_enum_trans) {
    $_enum_trans->updateLocales();
  }

  if ($ex_field->ex_class_id) {
    $ex_object = new CExObject;
    $ex_object->_ex_class_id = $ex_field->ex_class_id;
    $ex_object->setExClass();
    
    if ($ex_object->_specs[$field] instanceof CEnumSpec) {
      $spec = $ex_object->_specs[$field];
    }
  }

  else {
    // A second timpe to get the enum locales
    $spec = @CMbFieldSpecFact::getSpecWithClassName($class, $field, $prop);
  }
}*/

$triggerables = array();

if ($context instanceof CExClassField) {
  $context->loadTriggeredData();
  $ex_class = $context->loadRefExClass();
  if (!$ex_class->conditional) {
    $triggerable = new CExClass;
    //$triggerable->conditional = 1;
    $triggerable->host_class = $ex_class->host_class;
    $triggerable->event = $ex_class->event;
    $triggerables = $triggerable->loadMatchingList("conditional DESC");
  }
	
	if (!empty($context->concept_id) && !empty($context->_ref_concept->_ref_ex_list)) {
	  $items_all = array_keys($context->_ref_concept->_ref_ex_list->_ref_items);
	}
}

$GLOBALS["items"] = $items_sub;
usort($items_all, "order_items");

$classes = $spec instanceof CRefSpec ? CApp::getMbClasses() : array();

$smarty = new CSmartyDP();
$smarty->assign("prop", $prop);
$smarty->assign("spec", $spec);
$smarty->assign("options", $options);
$smarty->assign("items_all", $items_all);
$smarty->assign("items_sub", $items_sub);
$smarty->assign("form_name", $form_name);
$smarty->assign("classes", $classes);
$smarty->assign("list_owner", $list_owner);
$smarty->assign("context", $context);
$smarty->assign("triggerables", $triggerables);
$smarty->display("inc_edit_ex_field_spec2.tpl");
