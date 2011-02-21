<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$ex_field_id = CValue::get("ex_field_id");
$ex_class_id = CValue::get("ex_class_id");

$ex_field = new CExClassField;

$spec_type = "str";

if ($ex_field->load($ex_field_id)) {
  $spec_type = $ex_field->getSpecObject()->getSpecType();
  $ex_field->loadRefsNotes();
  $ex_field->updateTranslation();
}
else {
  $ex_field->ex_class_id = $ex_class_id;
}

$ex_field->loadRefExClass();
$ex_field->_ref_ex_class->loadRefsFields();

$other_fields = array();

foreach($ex_field->_ref_ex_class->_ref_fields as $_field){
  if ($_field->_id != $ex_field->_id)
    $other_fields[] = $_field->name;
}

$ex_concepts = new CExClassField;
$where = array("ex_class_id" => "IS NULL");
$list_concepts = $ex_concepts->loadList($where, "name");

$smarty = new CSmartyDP();
$smarty->assign("ex_field", $ex_field);
$smarty->assign("spec_type", $spec_type);
$smarty->assign("other_fields", $other_fields);
$smarty->assign("list_concepts", $list_concepts);
$smarty->display("inc_edit_ex_field.tpl");