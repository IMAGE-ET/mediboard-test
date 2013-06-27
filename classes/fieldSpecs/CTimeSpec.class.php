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

/**
 * Time value (HH:MM:SS)
 */
class CTimeSpec extends CMbFieldSpec {
  public $min;
  public $max;

  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "time";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return "TIME";
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object){
    $propValue = &$object->{$this->fieldName};

    $time_format = "/^\d{1,2}:\d{1,2}(:\d{1,2})?$/";

    // Format
    if (!preg_match($time_format, $propValue)) {
      if ($propValue === 'current' || $propValue ===  'now') {
        $propValue = CMbDT::time();
        return null;
      }
      
      return "Format d'heure invalide";
    }

    // min
    if ($this->min) {
      if (!preg_match($time_format, $this->min)) {
        trigger_error("Spécification de minimum time invalide (min = $this->min)", E_USER_WARNING);
        return "Erreur système";
      }
      if ($propValue < $this->min) {
        return "Doit avoir une valeur minimale de $this->min";
      }
    }

    // max
    if ($this->max) {
      if (!preg_match($time_format, $this->max)) {
        trigger_error("Spécification de maximum time invalide (max = $this->max)", E_USER_WARNING);
        return "Erreur système";
      }
      if ($propValue > $this->max) {
        return "Doit avoir une valeur maximale de $this->max";
      }
    }
    return null;
  }

  /**
   * @see parent::getOptions()
   */
  function getOptions(){
    return array(
      'min' => 'time',
      'max' => 'time'
    ) + parent::getOptions();
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    include_once $smarty->_get_plugin_filepath('modifier', 'date_format');
    
    $propValue = $object->{$this->fieldName};
    $format = CValue::first(@$params["format"], CAppUI::conf("time"));
    return $propValue ? smarty_modifier_date_format($propValue, $format) : "";
  }

  /**
   * @see parent::sample()
   */
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = 
      self::randomString(CMbFieldSpec::$hours, 1).":".
      self::randomString(CMbFieldSpec::$mins, 1).":".
      self::randomString(CMbFieldSpec::$mins, 1);
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className) {
    return $this->getFormElementDateTime($object, $params, $value, $className, CAppUI::conf("time"));
  }
}
