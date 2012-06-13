<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$ex_class_field_id = CValue::get("ex_class_field_id");
$form_name = CValue::get("form_name");
$value = CValue::get("value");

$ex_class_field = new CExClassField;
$ex_class_field->load($ex_class_field_id);

$ex_object = new CExObject;
$ex_object->_ex_class_id = $ex_class_field->loadRefExGroup()->ex_class_id;
$ex_object->setExClass();

$ex_object->{$ex_class_field->name} = $value;

$spec = CExConcept::getConceptSpec($ex_class_field->prop);
if ($spec instanceof CEnumSpec) {
  $ex_class_field->updateEnumSpec($spec);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("ex_field", $ex_class_field);
$smarty->assign("ex_object", $ex_object);
$smarty->assign("form", $form_name);
$smarty->display("inc_ex_object_field.tpl");
