<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CBoolSpec extends CMbFieldSpec {
  
  function checkValues(){
    parent::checkValues();
    if($this->default === null){
      $this->default = 0;
    }
  }
  
  function getValue($object, $smarty, $params = null) {
    global $AppUI;
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return $AppUI->_("bool.".$propValue);
  }
  
  function getSpecType() {
    return("bool");
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $propValue = $this->checkNumeric($propValue, false);
    if($propValue === null){
      return "N'est pas une chaîne numérique";
    }
    if($propValue!=0 && $propValue!=1){
      return "Ne peut être différent de 0 ou 1";
    }
    return null;
  }

  function getDBSpec(){
    return "ENUM('0','1')";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    $sHtml        = "";
    $field        = htmlspecialchars($this->fieldName);
    $separator    = CMbArray::extract($params, "separator");
    $disabled     = CMbArray::extract($params, "disabled");
    $default      = CMbArray::extract($params, "default", $this->default);
    $extra        = CMbArray::makeXmlAttributes($params);
    

    // Attributes for all inputs
    $attributes = array(
      "type" => "radio",
      "name" => $field,
    );
    
    if (null === $value) {
      $value = "$default";
    }
    
    for ($i = 1; $i >= 0; $i--) {
      $attributes["value"] = "$i"; 
      $attributes["checked"] = $value === "$i" ? "checked" : null; 
      $attributes["disabled"] = $disabled === "$i" ? "disabled" : null; 
      $attributes["class"] = $i == 1 ? $this->prop : null;
      $attributes["class"].= $className ? $className : null;
      
      $xmlAttributes = CMbArray::makeXmlAttributes($attributes);
      
      $sHtml .= "\n<input $xmlAttributes $extra />";
      
      $sTr = CAppUI::tr("bool.$i");
      $sHtml .= "\n<label for=\"{$field}_$i\">$sTr</label> ";
      
      if ($i != 0 && $separator){
        $sHtml .= "\n$separator";
      }
    }
    return $sHtml;
  }
  
  function getLabelForElement($object, $params){
    return $this->fieldName."_1";
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $propValue = rand(0,1);

  }
  
}

?>