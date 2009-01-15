<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Sébastien Fillonneau
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CTimeSpec extends CMbFieldSpec {
  
  function getValue($object, $smarty, $params = null) {
    require_once $smarty->_get_plugin_filepath('modifier','date_format');
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    $format = mbGetValue(@$params["format"], "%Hh%M");
    return $propValue ? smarty_modifier_date_format($propValue, $format) : "";
  }
  
  function getSpecType() {
    return("time");
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
  
    if (!preg_match ("/^([0-9]{1,2}):([0-9]{1,2})(:([0-9]{1,2}))?$/", $propValue)) { 
    	if($object->$fieldName == 'current'|| $object->$fieldName ==  'now') {
        $object->$fieldName = mbTime();
        return null;
      }  
      return "format de time invalide";
    }
    return null;
  }

  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $propValue = $this->randomString(CMbFieldSpec::$hours, 1).":".$this->randomString(CMbFieldSpec::$mins, 1).":".$this->randomString(CMbFieldSpec::$mins, 1);
  }
  
  function getDBSpec(){
    return "TIME";
  }
  
  function getFormHtmlElement($object, $params, $value, $className) {
    CMbArray::defaultValue($params, "size", 3);
    CMbArray::defaultValue($params, "maxLength", 5);
    if ($object->_locked) {
      $params["readonly"] = "readonly";
    }

    $class = htmlspecialchars(trim("$className $this->prop"));
    $field = htmlspecialchars($this->fieldName);
    
    $form  = CMbArray::extract($params, "form");
    $id    = $form.'_'.$field;
    $extra = CMbArray::makeXmlAttributes($params);
    
    $html = array();
    $html[] = '<div class="control '.$class.'">';
    $html[] = '<input type="text" name="'.$field.'" class="'.$class.'" value="'.substr($value, 0, 5).'" '.$extra.' />';
    if ($form) {
      $html[] = '<img id="'.$id.'_trigger" src="./images/icons/time.png" alt="Choisir l\'heure" class="trigger" />';
      if (!$this->notNull) {
        $html[] = '<button id="'.$id.'_cancel" class="cancel notext" type="button" onclick="$V(this.form.'.$field.', null, true);">'.CAppUI::tr("Delete").'</button>';
      }
      $html[] = '<script type="text/javascript">Main.add(function() { new TimePicker("'.$form.'", "'.$field.'"); } ); </script>';
    }
    $html[] = '</div>';
    
    return implode("\n", $html);
  }
}

?>