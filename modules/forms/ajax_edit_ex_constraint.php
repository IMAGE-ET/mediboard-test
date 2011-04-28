<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$ex_constraint_id = CValue::get("ex_constraint_id");
$ex_class_id      = CValue::get("ex_class_id");

$ex_constraint = new CExClassConstraint;
$ex_constraint->_ref_object = null;
  
if (!$ex_constraint->load($ex_constraint_id)) {
  $ex_constraint->ex_class_id = $ex_class_id;
}
else {
  $ex_constraint->loadRefsNotes();
}

$ex_constraint->loadRefExClass();
$ex_constraint->loadTargetObject();

$smarty = new CSmartyDP();
$smarty->assign("ex_constraint", $ex_constraint);
$smarty->display("inc_edit_ex_constraint.tpl");