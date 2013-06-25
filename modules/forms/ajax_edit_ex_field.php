<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage forms
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$ex_field_id = CValue::get("ex_field_id");
$ex_class_id = CValue::get("ex_class_id");
$ex_group_id = CValue::get("ex_group_id");

CExObject::$_locales_cache_enabled = false;
$ex_field = new CExClassField;

$spec_type = "enum";

if ($ex_field->load($ex_field_id)) {
  $spec_type = $ex_field->getSpecObject()->getSpecType();
  $ex_field->loadRefsNotes();
  $ex_field->updateTranslation();
  $ex_field->loadTriggeredData();
}
else {
  $ex_field->ex_group_id = $ex_group_id;
  $ex_field->disabled = 0;
}

$ex_field->loadRefExClass();
$ex_field->loadRefPredicate();
$ex_field->loadRefPredicates();
$ex_field->loadRefProperties();

if ($ex_class_id) {
  $ex_class = new CExClass;
  $ex_class->load($ex_class_id);
}
else {
  $ex_class = $ex_field->_ref_ex_class;
}

$ex_class->loadRefsGroups();

$other_fields = array();

/*$ex_field->_ref_ex_class->loadRefsFields();
foreach($ex_field->_ref_ex_class->_ref_fields as $_field){
  if ($_field->_id != $ex_field->_id)
    $other_fields[] = $_field->name;
}*/

$smarty = new CSmartyDP();
$smarty->assign("ex_field", $ex_field);
$smarty->assign("ex_class", $ex_class);
$smarty->assign("spec_type", $spec_type);
$smarty->assign("other_fields", $other_fields);
$smarty->display("inc_edit_ex_field.tpl");