<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

class CMbFieldSpecFact {
  
  function CMbFieldSpecFact() {
  }
   
  function getSpec($object, $field, $propSpec = null){
    
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
      "birthDate"    => "CBirthDate",
      "float"        => "CFloatSpec",
      "currency"     => "CCurrencySpec",
      "pct"          => "CPctSpec",
      "text"         => "CTextSpec",
      "html"         => "CHtmlSpec",
      "email"        => "CEmailSpec",
      "code"         => "CCodeSpec"
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

?>