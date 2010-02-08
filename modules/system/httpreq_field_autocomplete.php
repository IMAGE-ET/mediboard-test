<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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

foreach($where as $key => $value) {
  $where[$key] = "='$value'";
}

$input = str_replace("\\'", "'", $input);
$search = $wholeString ? "%$input%" : "$input%";

$object = new $class;
$spec = $object->_specs[$field];
$ds = $object->_spec->ds;
$matches = array();
$count = 0;
$template = null;

if ($spec instanceof CRefSpec) {
  $target_object = new $spec->class;
  $where[$view_field] = $ds->prepareLike($search);
  
  if ($spec->perm) {
    $permsTable = array(
      "deny" => PERM_DENY,
      "read" => PERM_READ,
      "edit" => PERM_EDIT,
    );
    $perm = $permsTable[$spec->perm] ? $permsTable[$spec->perm] : null;
    $matches = $target_object->loadListWithPerms($perm, $where, $view_field, $limit);
    $total = $target_object->_totalWithPerms;
  }
  else {
    $matches = $target_object->loadList($where, $view_field, $limit);
    $total = $target_object->countList($where);
  } 
  $template_file = "modules/{$target_object->_ref_module->mod_name}/templates/{$target_object->_class_name}_autocomplete.tpl";
  if (is_file($template_file)) {
    $template = "../../../$template_file";
  }
}
else {
  $where[$field] = $ds->prepareLike($search);
	$matches = $object->loadList($where, $field, $limit, $field);
  /*$counts = CMbArray::pluck($object->countMultipleList($where, $field, $limit, $field), "total");
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

$smarty->display('inc_field_autocomplete.tpl');

?>