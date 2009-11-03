<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;

$can->needsRead();

$class = CValue::get('class');
$id    = CValue::get('id');
$field = CValue::get('field');

// Loads the expected Object
if (class_exists($class)) {
  $object = new $class;
  $object->load($id);
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('object', $object);
$smarty->assign('field',  $field);

$smarty->display('inc_object_value.tpl');
?>
