<?php
/**
 * Field spec factory
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Id$
 */


/**
 * CFieldSpec factory for prop serialized definitions
 * 
 * @todo Memory caching
 */
class CMbFieldSpecFact {
  
  static $classes = array(
    "enum"         => "CEnumSpec",
    "set"          => "CSetSpec",
    "str"          => "CStrSpec",
    "text"         => "CTextSpec",
    "num"          => "CNumSpec",
    "float"        => "CFloatSpec",
    "date"         => "CDateSpec",
    "time"         => "CTimeSpec",
    "dateTime"     => "CDateTimeSpec",
    "bool"         => "CBoolSpec",
    "code"         => "CCodeSpec",
    "pct"          => "CPctSpec",
    "birthDate"    => "CBirthDateSpec",
    "ref"          => "CRefSpec",
    "numchar"      => "CNumcharSpec",
    "currency"     => "CCurrencySpec",
    "email"        => "CEmailSpec",
    "password"     => "CPasswordSpec",
    "html"         => "CHtmlSpec",
    "xml"          => "CXmlSpec",
    "php"          => "CPhpSpec",       // @todo: Make a sourceCode spec/
    "er7"          => "CER7Spec",
    "ipAddress"    => "CIpAddressSpec",
  );
   
  /**
   * Returns a spec object for an object field's prop
   * 
   * @param CMbObject $object The object
   * @param string    $field  The field name
   * @param string    $prop   The prop string serializing the spec object options
   * 
   * @return CMbFieldSpec Corresponding spec instance
   */
  static function getSpec(CModelObject $object, $field, $prop) {
  	return self::getSpecWithClassName($object->_class, $field, $prop);
  }
  
  /**
   * Returns a spec object for an object's field from a class name
   * 
   * @param string $class The class name
   * @param string $field The field name
   * @param string $prop  The prop string serializing the spec object options
   * 
   * @return CMbFieldSpec
   */
  static function getSpecWithClassName($class, $field, $prop) {
    $parts = explode(" ", $prop);
    $specClass = "CMbFieldSpec";
    
    // Get spec class
    if ($specType = array_shift($parts)) {
      if (null == $specClass = CMbArray::get(self::$classes, $specType)) {
        trigger_error("No spec class name for '$class'::'$field' = '$prop'", E_USER_ERROR);
      }
    }
    
    // Get spec options
    $specOptions = array();
    foreach ($parts as $_part) {
      $options = explode("|", $_part);
      $specOptions[array_shift($options)] = count($options) ? implode("|", $options) : true;
    }

    return new $specClass($class, $field, $prop, $specOptions);
  }
}
