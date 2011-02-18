<?php /* $Id: httpreq_field_autocomplete.php 8303 2010-03-10 17:05:12Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 8303 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$object_class = CValue::get('object_class');
$field        = CValue::get('field');
$view_field   = CValue::get('view_field', $field);
$input_field  = CValue::get('input_field', $view_field);

$keywords     = CValue::get($input_field);
$limit        = CValue::get('limit', 30);
$where        = CValue::get('where', array());

$object = new $object_class;
$ds = $object->_spec->ds;

foreach($where as $key => $value) {
  $where[$key] = $ds->prepare("= %", $value);
}

if ($keywords == "") {
  $keywords = "%";
}

$matches = $object->seek($keywords, $where, $limit);
$template = null;

$template_file = "modules/{$object->_ref_module->mod_name}/templates/{$object->_class_name}_autocomplete.tpl";
if (is_file($template_file)) {
  $template = "../../../$template_file";
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('matches',    $matches);
$smarty->assign('field',      $field);
$smarty->assign('view_field', $view_field);
$smarty->assign('show_view',  1);
$smarty->assign('template',   $template);
$smarty->assign('nodebug',    true);

$smarty->display('inc_field_autocomplete.tpl');
