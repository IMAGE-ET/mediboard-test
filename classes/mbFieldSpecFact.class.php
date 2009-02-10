<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Thomas Despoix
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


/**
 * @abstract Fabrique de CFieldSpec en fonction des spécifications de propriétés
 */
class CMbFieldSpecFact {
  
  static $classes = array(
    "ref"          => "CRefSpec",
    "str"          => "CStrSpec",
    "numchar"      => "CNumcharSpec",
    "num"          => "CNumSpec",
    "bool"         => "CBoolSpec",
    "enum"         => "CEnumSpec",
    "date"         => "CDateSpec",
    "time"         => "CTimeSpec",
    "dateTime"     => "CDateTimeSpec",
    "birthDate"    => "CBirthDateSpec",
    "float"        => "CFloatSpec",
    "currency"     => "CCurrencySpec",
    "pct"          => "CPctSpec",
    "text"         => "CTextSpec",
    "html"         => "CHtmlSpec",
    "email"        => "CEmailSpec",
    "code"         => "CCodeSpec",
    "password"     => "CPasswordSpec"
  );
   
  /**
   * 
   */
  static function getSpec($object, $fieldName, $strSpec = null) {
    $className = $object->_class_name;
        
    $specFragments = explode(" ", $strSpec);
    $specClassName = "CMbFieldSpec";
    if ($specName = CMbArray::extract($specFragments, 0, true)) {
	    if (null == $specClassName = CMbArray::get(self::$classes, $specName)) {
	      trigger_error("No spec class name for '$className'::'$fieldName' = '$strSpec'", E_USER_ERROR);
	    }
    }    
    
    $specOptions = array();
    foreach ($specFragments as $specFragment) {
      $options = explode("|", $specFragment);
      $optionName = CMbArray::extract($options, 0, null, true);

      $specOptions[$optionName] = count($options) ? implode("|", $options) : true;
    }

    return new $specClassName($className, $fieldName, $strSpec, $specOptions);
  }
  
}

?>