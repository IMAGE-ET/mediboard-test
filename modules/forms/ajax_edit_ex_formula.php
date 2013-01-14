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

$ex_field = new CExClassField;
$ex_field->load($ex_field_id);

$formula_possible = true;
$field_names = array();

$spec_type = $ex_field->getSpecObject()->getSpecType();

if (!CExClassField::formulaCanResult($spec_type)) {
  $formula_possible = false;
}
else {
  $field_names = $ex_field->getFieldNames(true, true);
  $field_names = array_values($field_names);
  $field_names = array_map("utf8_encode", $field_names);
}

$smarty = new CSmartyDP();
$smarty->assign("ex_field", $ex_field);
$smarty->assign("field_names", $field_names);
$smarty->assign("formula_possible", $formula_possible);
$smarty->display("inc_edit_ex_formula.tpl");