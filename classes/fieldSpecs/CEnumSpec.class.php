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

class CEnumSpec extends CMbFieldSpec {
  
  var $list = null;
  var $typeEnum = null;
  var $vertical = null;
  var $columns = null;
  
  var $_list = null;
  var $_locales = null;
  
  function __construct($className, $field, $prop = null, $aProperties = array()) {
    parent::__construct($className, $field, $prop, $aProperties);

    $this->_list = $this->getListValues($this->list);
    $this->_locales = array();
    
    foreach ($this->_list as $value) {
      $this->_locales[$value] = CAppUI::tr("$className.$field.$value");
    }
  }
  
  protected function getListValues($string){
    $list = array();
    
    if ($string !== "" && $string !== null) {
      $list = explode('|', $string);
    }
    
    return $list;
  }
  
  function getSpecType() {
    return "enum";
  }
  
  function getDBSpec() {
    return "ENUM('".str_replace('|', "','", $this->list)."')";
  }
  
  function getOptions(){
    return array(
      'list'     => 'list',
      'typeEnum' => array('radio', 'select'),
      'vertical' => 'bool',
      'columns'  => 'num',
    ) + parent::getOptions();
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return htmlspecialchars(CAppUI::tr("$object->_class.$fieldName.$propValue"));
  }
  
  function checkOptions(){
    parent::checkOptions();
    if (!$this->list) {
      trigger_error("Spécification 'list' manquante pour le champ '$this->fieldName' de la classe '$this->className'", E_USER_WARNING);
    }
  }
  
  function checkProperty($object){
    $propValue = $object->{$this->fieldName};
    $specFragments = $this->getListValues($this->list);
    if (!in_array($propValue, $specFragments)) {
      return "N'a pas une valeur possible";
    }
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $specFragments = $this->getListValues($this->list);
    $object->{$this->fieldName} = self::randomString($specFragments, 1);
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    $field         = htmlspecialchars($this->fieldName);
    $typeEnum      = CMbArray::extract($params, "typeEnum", $this->typeEnum ? $this->typeEnum : "select");
    $columns       = CMbArray::extract($params, "columns", $this->columns ? $this->columns : 1);
    $separator     = CMbArray::extract($params, "separator");
    $cycle         = CMbArray::extract($params, "cycle", 1);
    $alphabet      = CMbArray::extract($params, "alphabet", false);
    $form          = CMbArray::extract($params, "form"); // needs to be extracted

    // Empty label
    if ($emptyLabel = CMbArray::extract($params, "emptyLabel")) {
      $emptyLabel = "&mdash; ". CAppUI::tr($emptyLabel);
    }
    
    // Extra info por HTML generation
    $extra         = CMbArray::makeXmlAttributes($params);
    $locales       = $this->_locales;
    $className     = htmlspecialchars(trim("$className $this->prop"));
    $html          = "";
    
    // Alpha sorting
    if ($alphabet) {
      asort($locales); 
    }

    // Turn readonly to disabled
    $readonly  = CMbArray::extract($params, "readonly");
    $disabled = $readonly ? "disabled=\"1\"" : "";

    switch ($typeEnum) {
      default:
      case "select":
        
        $html .= "<select name=\"$field\" class=\"$className\" $disabled $extra>";
        
        // Empty option label
        if ($emptyLabel) {
          if ($value === null) {
            $html .= "\n<option value=\"\" selected=\"selected\">$emptyLabel</option>";
          }
          else {
            $html .= "\n<option value=\"\">$emptyLabel</option>";
          }
        }
        
        // All other options
        foreach ($locales as $key => $item) {
          $selected = "";
          if (($value !== null && $value === "$key") || ($value === null && "$key" === "$this->default" && !$emptyLabel)) {
            $selected = " selected=\"selected\""; 
          }
          
          $html .= "\n<option value=\"$key\" $selected>$item</option>";
        }
        
        $html .= "\n</select>";
        return $html;

      case "radio":
        $compteur = 0;
        
        foreach ($locales as $key => $item) {
          $selected = "";
          if (($value !== null && $value === "$key") || ($value === null && "$key" === "$this->default")) {
            $selected = " checked=\"checked\""; 
          }
          
          $html .= "\n<input type=\"radio\" name=\"$field\" value=\"$key\" $selected class=\"$className\" $disabled $extra />
                       <label for=\"{$field}_{$key}\">$item</label> ";
          $compteur++;
          
          $modulo = $compteur % $cycle;
          if ($separator != null && $modulo == 0 && $compteur < count($locales)) {
            $html  .= $separator;
          }
 
          if ($this->vertical) {
            $html .= "<br />\n";
          }
        }
        
        return $html;
    }
  }
  
  function getLabelForAttribute($object, &$params){
    // to extract the XHTML invalid attribute "typeEnum"
    $typeEnum = CMbArray::extract($params, "typeEnum");
    return parent::getLabelForAttribute($object, $params);
  }
}
