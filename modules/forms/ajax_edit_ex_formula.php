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

$field_names = CMbArray::pluck($ex_group->loadRefsFields(), "_locale");
$field_names = array_values($field_names);
$field_names = array_map("utf8_encode", $field_names);


$smarty = new CSmartyDP();
$smarty->assign("ex_group", $ex_group);
$smarty->assign("field_names", $field_names);
$smarty->display("inc_edit_ex_formula.tpl");