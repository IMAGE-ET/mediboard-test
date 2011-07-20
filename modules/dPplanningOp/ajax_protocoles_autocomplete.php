<?php /* $Id: httpreq_field_autocomplete.php 8303 2010-03-10 17:05:12Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 8303 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$field        = CValue::get('field');
$view_field   = CValue::get('view_field', $field);
$input_field  = CValue::get('input_field', $view_field);

$keywords     = CValue::get($input_field);
$limit        = CValue::get('limit', 30);
$chir_id      = CValue::get('chir_id');
$for_sejour   = CValue::get('for_sejour');

$object = new CProtocole();
$ds = $object->_spec->ds;

$where = array();
if($chir_id) {
  $chir = new CMediusers();
  $chir->load($chir_id);
  $where[] = "(protocole.chir_id = '$chir->_id' OR protocole.function_id = '$chir->function_id')";
}

if($for_sejour !== null) {
  $where["for_sejour"] = "= '$for_sejour'";
}

if ($keywords == "") {
  $keywords = "%";
}

$matches = $object->getAutocompleteList($keywords, $where, $limit);

$template = $object->getTypedTemplate("autocomplete");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('matches',    $matches);
$smarty->assign('field',      $field);
$smarty->assign('view_field', $view_field);
$smarty->assign('show_view',  1);
$smarty->assign('template',   $template);
$smarty->assign('nodebug',    true);

$smarty->display('../../system/templates/inc_field_autocomplete.tpl');
