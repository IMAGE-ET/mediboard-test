<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$ex_group_id = CValue::get("ex_group_id");

$ex_group = new CExClassFieldGroup;
$ex_group->load($ex_group_id);

$fields = $ex_group->loadRefsFields();

$field_names = CMbArray::pluck($fields, "_locale");
$field_names = array_values($field_names);
$field_names = array_map("utf8_encode", $field_names);

$result_fields = array();
$good = array("str", "num", "float");
foreach($fields as $_k => $_field) {
  $prop = reset(explode(" ", $_field->prop));
  if (in_array($prop, $good)) {
    $result_fields[] = $_field;
  }
}

$smarty = new CSmartyDP();
$smarty->assign("ex_group", $ex_group);
$smarty->assign("field_names", $field_names);
$smarty->assign("result_fields", $result_fields);
$smarty->display("inc_edit_ex_formula.tpl");