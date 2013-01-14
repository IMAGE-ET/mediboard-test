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

$ex_class_id         = CValue::get("ex_class_id");
$keywords            = CValue::get("_ex_field_view");
$exclude_ex_field_id = CValue::get("exclude_ex_field_id");

$ex_class = new CExClass;
$ex_class->load($ex_class_id);

$where = array(
  "ex_class_field_group.ex_class_id" => "= '$ex_class_id'",
);
$ljoin = array(
  "ex_class_field_group" => "ex_class_field_group.ex_class_field_group_id = ex_class_field.ex_group_id",
);

if ($exclude_ex_field_id) {
  $where["ex_class_field.ex_class_field_id"] = "!= $exclude_ex_field_id";
}

$ex_field = new CExClassField;

if ($keywords == "") {
  $keywords = "%";
}

$matches = $ex_field->getAutocompleteList($keywords, $where, 200, $ljoin);

$template = $ex_field->getTypedTemplate("autocomplete");

$smarty = new CSmartyDP("modules/system");
$smarty->assign('matches',    $matches);
$smarty->assign('field',      "ex_class_id");
$smarty->assign('view_field', "_ex_field_view");
$smarty->assign('show_view',  1);
$smarty->assign('template',   $template);
$smarty->assign('nodebug',    true);

$smarty->display('inc_field_autocomplete.tpl');
