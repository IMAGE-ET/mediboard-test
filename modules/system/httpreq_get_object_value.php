<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;

$can->needsRead();

$class           = CValue::get('class');
$id              = CValue::get('id');
$field           = CValue::get('field');
$content_type    = CValue::get('content_type');
$formatted_value = CValue::get('formatted_value');

// Loads the expected Object
if (class_exists($class)) {
  $object = new $class;
  $object->load($id);
}

if($content_type) {
  header("Content-Type: $content_type");	
}

if($formatted_value) {
  echo $object->getFormattedValue($field);	
} else {
	echo $object->$field;
}

?>
