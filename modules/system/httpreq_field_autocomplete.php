<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision$
* @author Fabien Ménager
*/

$class       = mbGetValueFromGet('class');
$field       = mbGetValueFromGet('field');
$view_field  = mbGetValueFromGet('view_field', $field);
$show_view   = mbGetValueFromGet('show_view', 'false') == 'true';
$input_field = mbGetValueFromGet('input_field', $view_field);
$input       = mbGetValueFromGet($input_field);
$limit       = mbGetValueFromGet('limit', 15);
$wholeString = mbGetValueFromGet('wholeString', 'false') == 'true';

$search = $wholeString ? "%$input%" : "$input%";

$object = new $class;
$spec = $object->_specs[$field];
$ds = $object->_spec->ds;
if ($spec instanceof CRefSpec) {
	$target_object = new $spec->class;
	$where = "`$view_field` ".$ds->prepareLike($search);
	$matches = $target_object->loadList($where, $view_field, $limit);
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