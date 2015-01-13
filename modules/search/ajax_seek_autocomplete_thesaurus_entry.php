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

$where["contextes"] = $ds->prepareIn($contextes);
$where["user_id"] = " = $user_id";

if ($keywords == "") {
  $keywords = "%";
}

$matches = $object->getAutocompleteList($keywords, $where, $limit, null);

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

$smarty->display('inc_field_autocomplete.tpl');
