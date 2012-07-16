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

class CDateSpec extends CMbFieldSpec {
  var $progressive = null;
  
  function getSpecType() {
    return "date";
  } 
  
  function getDBSpec(){
    return "DATE";
  }
  
  function getOptions(){
    return array(
      'progressive' => 'bool',
    ) + parent::getOptions();
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    if ($smarty) {
      include_once $smarty->_get_plugin_filepath('modifier', 'date_format');
    }
    
    $propValue = $object->{$this->fieldName};
    $format = CValue::first(@$params["format"], CAppUI::conf("date"));
    return ($propValue && $propValue != "0000-00-00") ? 
      ($this->progressive ? $this->progressiveFormat($propValue) : smarty_modifier_date_format($propValue, $format)) :
      "";
    // TODO: test and use strftime($format, strtotime($propValue)) instead of smarty
  }
  
  function progressiveFormat($value) {
    $parts = explode('-', $value);
    return (intval($parts[2]) ? $parts[2].'/' : '').(intval($parts[1]) ? $parts[1].'/' : '').$parts[0];
  }
  
  function checkProperty($object){
    $propValue = &$object->{$this->fieldName};
    
    // Vérification du format
    $matches = array();
    if (!preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", $propValue, $matches)) {
      if ($propValue === 'current'|| $propValue ===  'now') {
        $propValue = mbDate();
        return null;
      } 
      return "Format de date invalide : '$propValue'";
    }
    
    // Mois grégorien
    $mois = intval($matches[2]);
    if (!CMbRange::in($mois, $this->progressive ? 0 : 1, 12)) { // Possibilité de mettre des mois vides ()
      return "Mois '$mois' non compris entre 1 et 12 ('$propValue')";
    }
      
    // Jour grégorien
    $jour = intval($matches[3]);
    if (!CMbRange::in($jour, $this->progressive ? 0 : 1, 31)) {
      return "Jour '$jour' non compris entre 1 et 31 ('$propValue')";
    }
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = sprintf(
      "19%02d-%02d-%02d", 
      self::randomString(CMbFieldSpec::$nums, 2),
      self::randomString(CMbFieldSpec::$months, 1),
      self::randomString(CMbFieldSpec::$days, 1)
    );
  }
  
  function getFormHtmlElement($object, $params, $value, $className) {
    return $this->getFormElementDateTime($object, $params, $value, $className, CAppUI::conf("date"));
  }
}
