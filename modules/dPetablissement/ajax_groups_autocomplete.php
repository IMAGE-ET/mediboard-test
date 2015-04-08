<?php 

/**
 * $Id$
 *  
 * @category Etablissements
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$field        = CValue::get('field');
$view_field   = CValue::get('view_field', $field);
$input_field  = CValue::get('input_field', $view_field);
$show_view    = CValue::get('show_view', 'false') == 'true';
$keywords     = CValue::get($input_field);
$edit         = CValue::get('edit', 0);

CSessionHandler::writeClose();

$groups = CGroups::loadGroups($edit ? PERM_EDIT : PERM_READ);

if ($keywords) {
  foreach ($groups as $_group) {
    if (!preg_match("/^$keywords/i", $_group->text)) {
      unset($groups[$_group->_id]);
    }
  }
}

$group = new CGroups();
$template = $group->getTypedTemplate("autocomplete");

$smarty = new CSmartyDP();

$smarty->assign("matches"   , $groups);
$smarty->assign("field"     , $field);
$smarty->assign('view_field', $view_field);
$smarty->assign('show_view' , $show_view);
$smarty->assign("template"  , $template);
$smarty->assign('input'     , "");
$smarty->assign('nodebug'   , true);

$smarty->display("../../system/templates/inc_field_autocomplete.tpl");