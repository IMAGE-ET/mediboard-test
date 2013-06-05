<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

class CHprim21DataType extends CHDataType {
  /**
   * Get the spec object of a data type
   *
   * @param        $message
   * @param string $type      The 2 or 3 letters type
   * @param string $version   The version number of the spec
   * @param string $extension The extension
   *
   * @return CHL7v2DataType The data type spec
   */
  static function load($message, $type, $version, $extension) {
    static $cache = array();
    
    if ($type == "TS") {
      $type = "DTM";
    }
    
    $class_type = self::mapToBaseType($type);
    
    if (isset($cache[$version][$type])) {
      return $cache[$version][$type];
    }
    
    if (in_array($class_type, self::$typesBase)) {
      $class = "CHL7v2DataType$class_type";
      $instance = new $class($message, $class_type, $version, $extension);
      //$instance->getSpecs();
    }
    else {
      $instance = new CHL7v2DataTypeComposite($message, $type, $version, $extension);
    }
    
    return $cache[$version][$type] = $instance;
  }
}

