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

class CSetSpec extends CEnumSpec {
  
  var $_list_default = null;
  
  function __construct($className, $field, $prop = null, $aProperties = array()) {
    parent::__construct($className, $field, $prop, $aProperties);

    $this->_list_default = $this->getListValues($this->default);
  }
  
  function getSpecType() {
    return "set";
  }
  
  function getDBSpec() {
    return "TEXT";
  }
  
  function getOptions(){
    return array(
      'list' => 'list',
      'typeEnum' => array('checkbox', 'select'),
    ) + parent::getOptions();
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $fieldName = $this->fieldName;
    $propValue = $this->getListValues($object->$fieldName);
    
    $ret = array();
    foreach ($propValue as $_value) {
      $ret[] = CMbString::htmlSpecialChars(CAppUI::tr("$object->_class.$fieldName.$_value"));
    }
    
    return implode(", ", $ret);
  }
  
  function checkProperty($object){
    $propValue = $this->getListValues($object->{$this->fieldName});
    $specFragments = $this->getListValues($this->list);

    $diff = array_diff($propValue, $specFragments);

    if (!empty($diff)) {
      return "Contient une valeur non valide";
    }
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    $field         = CMbString::htmlSpecialChars($this->fieldName);
    $locales       = $this->_locales;
    
    $typeEnum      = CMbArray::extract($params, "typeEnum", $this->typeEnum ? $this->typeEnum : "checkbox");
    $separator     = CMbArray::extract($params, "separator", $this->vertical ? "<br />" : null);
    $cycle         = CMbArray::extract($params, "cycle", 1);
    $alphabet      = CMbArray::extract($params, "alphabet", false);
    $size          = CMbArray::extract($params, "size", 0);
    $onchange      = CMbArray::get($params, "onchange");
    $form          = CMbArray::extract($params, "form"); // needs to be extracted
    
    $extra         = CMbArray::makeXmlAttributes($params);
    $className     = CMbString::htmlSpecialChars(trim("$className $this->prop"));

    if ($alphabet) {
      asort($locales); 
    }

    $uid = uniqid();
    $value_array = $this->getListValues($value);
    
    switch ($typeEnum) {
      case "select":
        $sHtml     = "<script type=\"text/javascript\">
          Main.add(function(){
            var select = \$\$('select[data-select_set=$uid]')[0],
                element = select.previous(),
                tokenField = new TokenField(element, {" .($onchange ? "onChange: function(){ $onchange }.bind(element)" : "")."});

            select.observe('change', function(event){
              element.fire('ui:change');
              tokenField.setValues(\$A(select.options).filter(function(o){return o.selected}).pluck('value'));
            });
          });
        </script>";
        $sHtml      .= "<input type=\"hidden\" name=\"$field\" value=\"$value\" class=\"$className\" $extra />\n";
        $sHtml      .= "<select class=\"$className\" multiple=\"multiple\" size=\"$size\" data-select_set=\"$uid\" $extra>";
        
        foreach ($locales as $key => $item) {
          if (!empty($value_array) && in_array($key, $value_array)) {
            $selected = " selected=\"selected\""; 
          }
          else {
            $selected = "";
          }
          $sHtml    .= "\n<option value=\"$key\" $selected>$item</option>";
        }
        
        $sHtml      .= "\n</select>";
        break;

      default:
      case "checkbox":
        $sHtml          = "<span id=\"set-container-$uid\">\n";
        $sHtml         .= "<input type=\"hidden\" name=\"$field\" value=\"$value\" class=\"$className\" $extra />\n";

        $sHtml         .= "<script type=\"text/javascript\">
          Main.add(function(){
            var cont = \$('set-container-$uid'),
                element = cont.down('input[type=hidden]'),
                tokenField = new TokenField(element, {" .($onchange ? "onChange: function(){ $onchange }.bind(element)" : "")."});

            cont.select('input[type=checkbox]').invoke('observe', 'click', function(event){
              element.fire('ui:change');
              var elt = Event.element(event);
              tokenField.toggle(elt.value, elt.checked);
            });
          });
        </script>";
        $compteur = 0;
        
        foreach ($locales as $key => $item) {
          $selected = "";
          
          if (!empty($value_array) && in_array($key, $value_array)) {
            $selected = " checked=\"checked\""; 
          }
          
          $sHtml .= "\n<label>
              <input type=\"checkbox\" name=\"_{$field}_{$key}\" value=\"$key\" class=\"set-checkbox token$uid\" $selected />
              $item
            </label> ";
          $compteur++;
          
          $modulo = $compteur % $cycle;
          if ($separator != null && $modulo == 0 && $compteur < count($locales)) {
            $sHtml  .= $separator;
          }
        }

        $sHtml .= "</span>\n";
    }

    return $sHtml;
  }
  
  function getLabelForAttribute($object, &$params){
    // to extract the XHTML invalid attribute "typeEnum"
    $typeEnum = CMbArray::extract($params, "typeEnum");
    return parent::getLabelForAttribute($object, $params);
  }
}
