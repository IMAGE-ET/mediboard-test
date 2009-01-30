<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Sébastien Fillonneau
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CDateSpec extends CMbFieldSpec {
  
  function getValue($object, $smarty = null, $params = null) {
    if ($smarty) require_once $smarty->_get_plugin_filepath('modifier','date_format');
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    $format = mbGetValue(@$params["format"], "%d/%m/%Y");
    return ($propValue && $propValue != "0000-00-00") ? 
      smarty_modifier_date_format($propValue, $format) :
      "";
  }
  
  function getSpecType() {
    return("date");
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    // Vérification du format
    $matches = array();
    if (!preg_match ("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", $propValue, $matches)) {
      if($object->$fieldName === 'current'|| $object->$fieldName ===  'now') {
        $object->$fieldName = mbDate();
        return null;
      } 
      return "format de date invalide : '$propValue'";
    }
    
    // Mois grégorien
    $mois = intval($matches[2]);
    if (!in_range($mois, 1, 12)) {
      return "mois '$mois' non compris entre 1 et 12 ('$propValue')";
    }
      
    // Jour grégorien
    $jour = intval($matches[3]);
    if (!in_range($jour, 1, 31)) {
      return "jour '$jour' non compris entre 1 et 31 ('$propValue')";
    }
    
      
    return null;
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $propValue = "19".$this->randomString(CMbFieldSpec::$nums, 2).
      "-".$this->randomString(CMbFieldSpec::$months, 1).
      "-".$this->randomString(CMbFieldSpec::$days, 1);
  }
  
  function getDBSpec(){
    return "DATE";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementDateTime($object, $params, $value, $className, "%d/%m/%Y");
  }
}

?>