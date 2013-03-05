<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

class CPhpSpec extends CMbFieldSpec {
  function getSpecType() {
    return "php";
  }
  
  function getDBSpec() {
    return "MEDIUMTEXT";
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $propValue = $object->{$this->fieldName};
    $propValue = (!empty($params['export']) ? var_export($propValue, true) : $propValue);
    
    return utf8_decode(CMbString::highlightCode("php", $propValue, false));
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }

  function sample(&$object, $consistent = true){
    $object->{$this->fieldName} =
'<?php
$file = fopen("welcome.txt", "r") or exit("Unable to open file!");
//Output a line of the file until the end is reached
while(!feof($file)) {
  echo fgets($file). "<br />";
}
fclose($file);
?>';
  }
}
