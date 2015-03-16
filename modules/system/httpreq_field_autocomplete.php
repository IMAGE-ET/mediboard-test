<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$class       = CValue::get('class');
$field       = CValue::get('field');
$view_field  = CValue::get('view_field', $field);
$show_view   = CValue::get('show_view', 'false') == 'true';
$input_field = CValue::get('input_field', $view_field);
$input       = CValue::get($input_field);
$limit       = CValue::get('limit', 30);
$wholeString = CValue::get('wholeString', 'false') == 'true';
$where       = CValue::get('where', array());
$whereComplex = CValue::get('whereComplex', array());
$min_occurences = CValue::get('min_occurences', 1);

/** @var CMbObject $object */
$object = new $class;
$ds = $object->_spec->ds;

foreach ($where as $key => $value) {
  $where[$key] = $ds->prepare("= %", $value);
}

foreach ($whereComplex as $key => $value) {
  $where[$key] = stripslashes($value);
}

$input = str_replace("\\'", "'", $input);
$search = $wholeString ? "%$input%" : "$input%";

$spec = $object->_specs[$field];
$matches = array();
$count = 0;
$template = null;

if ($spec instanceof CRefSpec) {
  /** @var CMbObject $target_object */
  $target_object = new $spec->class;
  
  if ($view_field == "_view") {
    $matches = $target_object->getAutocompleteList($input, $where, $limit);
  }
  else {
    $where[$view_field] = $ds->prepareLike($search);
    
    if ($spec->perm) {
      $permsTable = array(
        "deny" => PERM_DENY,
        "read" => PERM_READ,
        "edit" => PERM_EDIT,
      );
      $perm = $permsTable[$spec->perm] ? $permsTable[$spec->perm] : null;
      $matches = $target_object->loadListWithPerms($perm, $where, $view_field, $limit, $view_field);
    }
    else {
      $matches = $target_object->loadList($where, $view_field, $limit, $view_field);
      $total = $target_object->countList($where, $view_field);
    }
  }
  
  $template = $target_object->getTypedTemplate("autocomplete");
}
else {
  $where[$field] = $ds->prepareLike($search);
  $group_by = $field;
  
  if ($min_occurences > 1) {
    $group_by .= " HAVING COUNT(*) > $min_occurences";
  }
  
  $matches = $object->loadList($where, $field, $limit, $group_by);
  /*$counts = CMbArray::pluck($object->countMultipleList($where, $field, $field), "total");
  $count = count($counts);
  
  if ($limit)
    $counts = array_slice($counts, 0, $limit, true);
  
  $total = array_sum($counts) / 100;
  $matches_keys = array_keys($matches);
  $percents = array();
  foreach($counts as $key => $v) {
    $matches[$matches_keys[$key]]->_percent = $v / $total;
  }
  
  function percent_sort($match) {
    
  }*/
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('matches',    $matches);
$smarty->assign('count',      $count);
$smarty->assign('input',      $input);
$smarty->assign('field',      $field);
$smarty->assign('view_field', $view_field);
$smarty->assign('show_view',  $show_view);
$smarty->assign('template',   $template);
$smarty->assign('nodebug',    true);
$smarty->assign('ref_spec',   $spec instanceof CRefSpec);

$smarty->display('inc_field_autocomplete.tpl');
