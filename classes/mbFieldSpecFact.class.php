<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Sébastien Fillonneau
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CMbFieldSpecFact {
  
  static function getSpec($object, $field, $propSpec = null){
    
    static $aClass = array(
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
      
    $specObject     = null;
    $nameClass      = null;
    $aProperties    = array();
    $aSpecFragments = explode(" ", $propSpec);
    foreach($aSpecFragments as $spec){
      $aFrag = explode("|", $spec);
      if(count($aFrag) == 1){
        $aProperties[$spec] = true;
      }else{
        $aSpec = $aFrag;
        $currSpec = array_shift($aSpec);
        if(count($aSpec)){
          $aProperties[$currSpec] = implode("|", $aSpec);
        }else{
          $aProperties[$currSpec] = true;
        }
      }

      if(array_key_exists($aFrag[0], $aClass)){
       if(array_key_exists($aFrag[0], $aProperties)){
         unset($aProperties[$aFrag[0]]);
       }
       $nameClass = $aFrag[0];
      }
    }

    if($nameClass){
      $specObject = new $aClass[$nameClass]($object->_class_name, $field, $propSpec, $aProperties);
    } else {
      $specObject = new CMbFieldSpec($object->_class_name, $field);
    }
    return $specObject;
  }
}

/**
 * Nouvelle implémentation simplifiée 
 * Non encore opérationnelle, cf. plus bas
 */
class CMbFieldSpecFactEx {
  
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
   
  static function getSpec($className, $fieldName, $strSpec = null) {
    $specFragments = explode(" ", $strSpec);
    $specName = CMbArray::extract($specFragments, 0, true);
    $specClassName = CMbArray::get(self::$classes, $specName, "CMbObjectSpec");
    
    $specOptions = array();
    foreach ($specFragments as $specFragment) {
      $options = explode("|", $specFragment);
      $optionName = CMbArray::extract($options, 0, null, true);

      // La class n'est pas forcément en premier
      // @TODO A changer dans les specs
      if (isset(self::$classes[$optionName])) {
        $specClassName = self::$classes[$optionName];
        continue;
      }

      $specOptions[$optionName] = count($options) ? implode("|", $options) : true;
    }

    return new $specClassName($className, $fieldName, $strSpec, $specOptions);
  }
  
}

?>