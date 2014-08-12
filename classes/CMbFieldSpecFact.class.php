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
    "phone"        => "CPhoneSpec",
    "ref"          => "CRefSpec",
    "numchar"      => "CNumcharSpec",
    "currency"     => "CCurrencySpec",
    "email"        => "CEmailSpec",
    "password"     => "CPasswordSpec",
    "html"         => "CHtmlSpec",
    "xml"          => "CXmlSpec",
    "php"          => "CPhpSpec",       // @todo: Make a sourceCode spec/
    "er7"          => "CER7Spec",
    "hpr"          => "CHPRSpec",
    "ipAddress"    => "CIpAddressSpec",
    "url"          => "CURLSpec",
    "uri"          => "CURISpec",
    "color"        => "CColorSpec"
  );
   
  /**
   * Returns a spec object for an object field's prop
   * 
   * @param CModelObject $object The object
   * @param string       $field  The field name
   * @param string       $prop   The prop string serializing the spec object options
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
   * @throws Exception
   * @return CMbFieldSpec
   */
  static function getSpecWithClassName($class, $field, $prop) {
    $spec_class  = "CMbFieldSpec";

    // Get Spec type
    $first_space = strpos($prop, " ");
    $spec_type = $first_space === false ? $prop : substr($prop, 0, $first_space);

    // Get spec class
    if ($spec_type && (null == $spec_class = CMbArray::get(self::$classes, $spec_type))) {
      throw new Exception("Invalid spec '$prop' for field '$class::$field'");
    }

    return new $spec_class($class, $field, $prop);
  }
}
