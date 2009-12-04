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
$limit       = CValue::get('limit', 15);
$wholeString = CValue::get('wholeString', 'false') == 'true';

$search = $wholeString ? "%$input%" : "$input%";

$object = new $class;
$spec = $object->_specs[$field];
$ds = $object->_spec->ds;
if ($spec instanceof CRefSpec) {
	$target_object = new $spec->class;
	$where = "`$view_field` ".$ds->prepareLike($search);
  
  if ($spec->perm) {
    $permsTable = array(
      "deny" => PERM_DENY,
      "read" => PERM_READ,
      "edit" => PERM_EDIT,
    );
    $perm = $permsTable[$spec->perm] ? $permsTable[$spec->perm] : null;
    $matches = $target_object->loadListWithPerms($perm, $where, $view_field, $limit);
  }
  else {
    $matches = $target_object->loadList($where, $view_field, $limit);
  }
}
else {
	$where = "`$field` ".$ds->prepareLike($search);
	$matches = $object->loadList($where, $field, $limit, $field);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('matches', $matches);
$smarty->assign('input',   $input);
$smarty->assign('field',   $field);
$smarty->assign('view_field', $view_field);
$smarty->assign('show_view',  $show_view);
$smarty->assign('nodebug', true);

$smarty->display('inc_field_autocomplete.tpl');

?>