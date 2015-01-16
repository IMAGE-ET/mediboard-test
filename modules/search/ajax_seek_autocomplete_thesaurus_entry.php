<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */


$object_class = CValue::get('object_class');
$field        = CValue::get('field');
$view_field   = CValue::get('view_field', $field);
$input_field  = CValue::get('input_field', $view_field);
$show_view    = CValue::get('show_view', 'false') == 'true';
$keywords     = CValue::get($input_field);
$limit        = CValue::get('limit', 30);
$contextes    = CValue::get('contextes');
$user_id      = CValue::get('user_id');

/** @var CMbObject $object */
$object = new $object_class;
$ds = $object->_spec->ds;

$user = new CMediusers();
$user->load($user_id);
$user->loadRefFunction();
$function_id = $user->_ref_function->_id;
$group_id = $user->_ref_function->group_id;

$where["contextes"] = $ds->prepareIn($contextes);

if ($keywords == "") {
  $keywords = "%";
}
$where["user_id"] = " = $user_id";
$where["function_id"] = " IS NULL";
$where["group_id"] = " IS NULL";
$matchesUser = $object->getAutocompleteList($keywords, $where, $limit, null);

unset($where["user_id"]);
$where["function_id"] = " = $function_id";
$matchesFunction = $object->getAutocompleteList($keywords, $where, $limit, null);

unset($where["function_id"]);
$where["group_id"] = " = $group_id";
$matchesGroup = $object->getAutocompleteList($keywords, $where, $limit, null);

$matches = $matchesUser + $matchesFunction + $matchesGroup;
$template = $object->getTypedTemplate("autocomplete");

// Création du template
$smarty = new CSmartyDP("modules/system/");

$smarty->assign('matches'   , $matches);
$smarty->assign('field'     , $field);
$smarty->assign('view_field', $view_field);
$smarty->assign('show_view' , $show_view);
$smarty->assign('template'  , $template);
$smarty->assign('nodebug'   , true);
$smarty->assign("input"     , "");
$smarty->assign("user_id"     , $user_id);
$smarty->assign("function_id" , $function_id);
$smarty->assign("group_id" , $group_id);

$smarty->display('inc_field_autocomplete.tpl');
