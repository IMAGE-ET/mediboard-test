<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$prop          = CValue::get("prop");
$spec_type     = CValue::get("_spec_type");
$form_name     = CValue::get("form_name");
$ex_list_id    = CValue::get("ex_list_id");
$ex_concept_id = CValue::get("concept_id");
$owner_guid    = CValue::get("owner_guid");

$owner = null;

$ex_list = new CExList;
if($ex_list_id) {
  $ex_list->load($ex_list_id);
  $owner = $ex_list->getRealListOwner();
}

$ex_concept = new CExConcept;
if($ex_concept_id) {
  $ex_concept->load($ex_concept_id);
	$prop = $ex_concept->prop;
	$spec_type = CExConcept::getConceptSpec($prop)->getSpecType();
	$owner = $ex_concept->getRealListOwner();
}

if (!$owner) {
	$owner = CMbObject::loadFromGuid($owner_guid);
}

$owner->loadView();

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
$options = $spec->_options;

if ($spec instanceof CEnumSpec) {
	unset($spec->_locales[""]);
	if (reset($spec->_list) === "") {
		unset($spec->_list[0]);
	}
	
	if($ex_list->_id) {
	  $ex_list->updateEnumSpec($spec);
		$prop .= " ".implode("|", $spec->_list);
	}
	elseif($ex_concept->_id) {
		$ex_list = $ex_concept->loadRefExList();
	}
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

$classes = $spec instanceof CRefSpec ? CApp::getMbClasses() : array();

$smarty = new CSmartyDP();
$smarty->assign("prop", $prop);
$smarty->assign("spec", $spec);
$smarty->assign("options", $options);
$smarty->assign("form_name", $form_name);
$smarty->assign("classes", $classes);
$smarty->assign("ex_list", $ex_list);
$smarty->assign("owner", $owner);
$smarty->display("inc_edit_ex_field_spec2.tpl");
