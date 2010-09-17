<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7V2 {  
  static $versions = array(
    "1",
    "2",
    "3",
    "3_1",
    "4",
    "5"
  );
  
  var $version = null;
  
  function isDate($value) {
    return preg_match("/^\d{8}$/", $value);
  }
  
  function isTime($value) {
    return preg_match("/^\d{6}$/", $value);
  }
  
  function isDateTime($value) {
    return preg_match("/^\d{14}$/", $value);
  }
  
  function isDouble($value) {
    return is_numeric($value) && is_double(floatval($value));
  }
  
  function isInteger($value) {
    return preg_match("/^\d+$/", $value);
  }
  
  function isString($value) {
    return is_string($value);
  }
}

?>