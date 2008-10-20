<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;

$can->needsRead();

$class = mbGetValueFromGet('class');
$id    = mbGetValueFromGet('id');
$field = mbGetValueFromGet('field');

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
