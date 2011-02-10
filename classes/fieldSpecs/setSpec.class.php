<?php /* $Id: enumSpec.class.php 11092 2011-01-13 13:30:35Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 11092 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("fieldSpecs/enumSpec");

class CSetSpec extends CEnumSpec {
  
  function getSpecType() {
    return "set";
  }
  
  function getDBSpec() {
    return "TEXT";
  }
  
  function getOptions(){
    return parent::getOptions() + array(
      'list' => 'list',
      'typeEnum' => array(/*'select', */'checkbox'),
    );
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $fieldName = $this->fieldName;
    $propValue = explode('|', $object->$fieldName);
		
    $ret = array();
    foreach ($propValue as $_value) {
      $ret[] = htmlspecialchars(CAppUI::tr("$object->_class_name.$fieldName.$_value"));
    }
		
    return implode(", ", $ret);
  }
  
  function checkProperty($object){
    $propValue = explode('|', $object->{$this->fieldName});
    $specFragments = explode('|', $this->list);

    $diff = array_diff($propValue, $specFragments);

    if (!empty($diff)) {
      return "Contient une valeur non valide";
    }
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $specFragments = $this->class ? $this->getClassList() : explode('|', $this->list);
    $object->{$this->fieldName} = self::randomString($specFragments, 1);
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    $field         = htmlspecialchars($this->fieldName);
    $locales       = $this->_locales;
		
    $typeEnum      = CMbArray::extract($params, "typeEnum", $this->typeEnum ? $this->typeEnum : "checkbox");
    $separator     = CMbArray::extract($params, "separator", "<br />");
    $cycle         = CMbArray::extract($params, "cycle", 1);
    $defaultOption = CMbArray::extract($params, "defaultOption");
    $alphabet      = CMbArray::extract($params, "alphabet", false);
    $size          = CMbArray::extract($params, "size", min(3, count($locales)));
    $form          = CMbArray::extract($params, "form"); // needs to be extracted
    
    $extra         = CMbArray::makeXmlAttributes($params);
    $className     = htmlspecialchars(trim("$className $this->prop"));
		
    $uid = uniqid();
		
    $sHtml         = "<input type=\"hidden\" name=\"$field\" id=\"$uid\" value=\"$value\" class=\"$className\" $extra />\n";
		
    $sHtml         .= "<script type=\"text/javascript\">
      Main.add(function(){
        var element = \$('$uid'),
            tokenField = new TokenField(element);

        element.up('form').select('.token$uid').invoke('observe', 'click', function(event){
          var elt = Event.element(event);
          tokenField.toggle(elt.value, elt.checked);
        });
      });
    </script>";
    
    if ($alphabet) {
      asort($locales); 
    }
    
    $value = explode("|", $value);
    
    switch ($typeEnum) {
      case "select":
        /*$sHtml      .= "<select name=\"$field\" class=\"$className\" multiple=\"multiple\" size=\"$size\" $extra>";
				
        foreach ($locales as $key => $item){
          if (!empty($value) && in_array($key, $value)) {
            $selected = " selected=\"selected\""; 
          }
					else {
            $selected = "";
          }
          $sHtml    .= "\n<option value=\"$key\" $selected>$item</option>";
        }
				
        $sHtml      .= "\n</select>";
        return $sHtml;
        */
      default:
      case "checkbox":
        $compteur = 0;
        
        foreach ($locales as $key => $item){
          if (!empty($value) && in_array($key, $value)) {
            $selected = " checked=\"checked\""; 
          }
					else {
            $selected = "";
          }
          $sHtml .= "\n<label>
            <input type=\"checkbox\" name=\"_{$field}_{$key}\" value=\"$key\" onclick=\"\" class=\"token$uid\" $selected />
              $item
            </label> ";
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
