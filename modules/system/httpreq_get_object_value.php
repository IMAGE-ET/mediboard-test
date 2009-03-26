<?php 

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 5037 $
 * @author Yohann Poiron
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;

$can->needsRead();

$class           = mbGetValueFromGet('class');
$id              = mbGetValueFromGet('id');
$field           = mbGetValueFromGet('field');
$content_type    = mbGetValueFromGet('content_type');
$formatted_value = mbGetValueFromGet('formatted_value');

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
