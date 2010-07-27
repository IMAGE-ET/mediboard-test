<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CEnumSpec extends CMbFieldSpec {
  
  var $list = null;
  var $_list = null;
  var $_locales = null;
  
  function __construct($className, $field, $prop = null, $aProperties = array()) {
    parent::__construct($className, $field, $prop, $aProperties);
    foreach ($this->_list = explode('|', $this->list) as $value) {
      $this->_locales[$value] = CAppUI::tr("$className.$field.$value");
    }
  }
  
  function getSpecType() {
    return "enum";
  }
  
  function getDBSpec() {
    return "ENUM('".str_replace('|', "','", $this->list)."')";
  }
  
  function getOptions(){
    return parent::getOptions() + array(
      'list' => 'list',
    );
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return htmlspecialchars(CAppUI::tr("$object->_class_name.$fieldName.$propValue"));
  }
  
  function checkValues(){
    parent::checkValues();
    if (!$this->list){
      trigger_error("Spécification 'list' manquante pour le champ '$this->fieldName' de la classe '$this->className'", E_USER_WARNING);
    }
  }
  
  function checkProperty($object){
    $propValue = $object->{$this->fieldName};
    $specFragments = explode('|', $this->list);
    if (!in_array($propValue, $specFragments)) {
      return "N'a pas une valeur possible";
    }
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $specFragments = $this->class ? $this->getClassList() : explode('|', $this->list);
    $object->{$this->fieldName} = self::randomString($specFragments, 1);
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    $field         = htmlspecialchars($this->fieldName);
    $typeEnum      = CMbArray::extract($params, "typeEnum", "select");
    $separator     = CMbArray::extract($params, "separator");
    $cycle         = CMbArray::extract($params, "cycle", 1);
    $defaultOption = CMbArray::extract($params, "defaultOption");
    $alphabet      = CMbArray::extract($params, "alphabet", false);
    
    if ($emptyLabel = CMbArray::extract($params, "emptyLabel")) {
      $defaultOption = "&mdash; ". CAppUI::tr($emptyLabel);
    }
    
    $extra         = CMbArray::makeXmlAttributes($params);
    $locales       = $this->_locales;
    $className     = htmlspecialchars(trim("$className $this->prop"));
    $sHtml         = "";
    
    if ($alphabet) {
      asort($locales); 
    }
    
    switch ($typeEnum) {
      default:
      case "select":
        $sHtml      .= "<select name=\"$field\" class=\"$className\" $extra>";
        
        if($defaultOption){
          if($value === null) {
            $sHtml    .= "\n<option value=\"\" selected=\"selected\">$defaultOption</option>";
          } else {
            $sHtml    .= "\n<option value=\"\">$defaultOption</option>";
          }
        }
        foreach ($locales as $key => $item){
          if(($value !== null && $value === "$key") || ($value === null && "$key" === "$this->default" && !$defaultOption)){
            $selected = " selected=\"selected\""; 
          }else{
            $selected = "";
          }
          $sHtml    .= "\n<option value=\"$key\" $selected>$item</option>";
        }
        $sHtml      .= "\n</select>";
        return $sHtml;

      case "radio":
        $compteur = 0;
        
        foreach ($locales as $key => $item){
          if(($value !== null && $value === "$key") || ($value === null && "$key" === "$this->default")){
            $selected = " checked=\"checked\""; 
          }else{
            $selected = "";
          }
          $sHtml .= "\n<input type=\"radio\" name=\"$field\" value=\"$key\" $selected class=\"$className\" $extra />
                       <label for=\"{$field}_{$key}\">$item</label> ";
          $compteur++;
          
          $modulo = $compteur % $cycle;
          if($separator != null && $modulo == 0 && $compteur < count($locales)){
            $sHtml  .= $separator;
          }
        }
        return $sHtml;
    }
  }
  
  function getLabelForElement($object, &$params){
    // to extract the XHTML invalid attribute "typeEnum"
    $typeEnum = CMbArray::extract($params, "typeEnum");
    return parent::getLabelForElement($object, $params);
  }
}
