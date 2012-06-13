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

$ex_field_predicate = new CExClassFieldPredicate;

if (!$ex_field_predicate->load($ex_field_predicate_id)) {
  $ex_field_predicate->ex_class_field_id = $ex_field_id;
}

$ex_field = $ex_field_predicate->loadRefExClassField();
$ex_class = $ex_field->loadRefExClass();

$smarty = new CSmartyDP();
$smarty->assign("ex_field_predicate", $ex_field_predicate);
$smarty->assign("ex_class", $ex_class);
$smarty->display("inc_edit_ex_field_predicate.tpl");