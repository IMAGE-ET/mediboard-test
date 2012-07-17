<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$ex_field_id           = CValue::get("ex_field_id");
$ex_field_predicate_id = CValue::get("ex_field_predicate_id");
$exclude_ex_field_id   = CValue::get("exclude_ex_field_id");
$opener_field_value    = CValue::get("opener_field_value");
$opener_field_view     = CValue::get("opener_field_view");

$ex_field_predicate = new CExClassFieldPredicate;
$ex_field_predicate->load($ex_field_predicate_id);

if (!$ex_field_predicate->_id && $ex_field_id != $exclude_ex_field_id) {
  $ex_field_predicate->ex_class_field_id = $ex_field_id;
}

$ex_field_predicate->loadRefExClassField();

$ex_field = new CExClassField;
$ex_field->load($ex_field_id);
$ex_class = $ex_field->loadRefExClass();

$smarty = new CSmartyDP();
$smarty->assign("ex_field_predicate",  $ex_field_predicate);
$smarty->assign("ex_class",            $ex_class);
$smarty->assign("exclude_ex_field_id", $exclude_ex_field_id);
$smarty->assign("opener_field_value",  $opener_field_value);
$smarty->assign("opener_field_view",   $opener_field_view);
$smarty->display("inc_edit_ex_field_predicate.tpl");
