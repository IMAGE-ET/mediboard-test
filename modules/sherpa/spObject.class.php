<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage sherpa
* @version $Revision: 2249 $
* @author Sherpa
*/

/**
 * Abstract class for sherpa objects
 * - base association
 */
class CSpObject extends CMbObject {  
  public $_ref_id400 = null;
  
  function CSpObject($table, $key) {
    foreach (array_keys($this->getSpecs()) as $prop) {
      $this->$prop = null;
    }
        
    $this->loadRefModule(basename(dirname(__FILE__)));
    if($this->_ref_module) {
      $this->CMbObject($table, $key);
    }
  }
  
  function loadExternal() {
    $this->_external = true;
  }
  
  /**
   * Change the data source
   * @param int $g group_id
   */
  function changeDSN($g) {
    $this->_spec->dsn = "sherpa-$g";
    $this->_spec->init();
    
    // Declare unquotable columns
    if ($ds =& $this->_spec->ds) {
      $ds->unquotable = array(
        "es_entccam" => array("idinterv")
			);
    }
  }
  
  /**
   * Map this to a Mediboard object
   * @param CMbObject $mbObject
   * @return CMbObject the mapped object
   */
  function mapTo() {
  }
  
  /**
   * Map this from a Mediboard object
   * @param CMbObject $mbObject
   * @return null
   */
  function mapFrom(CMbObject &$mbObject) {
  }
      
  function getSpec() {
    global $g;
    $spec = parent::getSpec();
    $spec->dsn = "sherpa-$g";
    $spec->incremented = false;
    $spec->loggable = false;
    $spec->nullifyEmptyStrings = false;
    return $spec;
  }

  function getBackRefs() {
    return array();
  }
 
  function getspecs() {
    return array();
  }
   
  function getSeeks() {
    return array();
  }

  function getHelpedFields(){
    return array();
  }
  
  /**
   * Load Id400 fo current group
   * @return CIdSante400
   */
  function loadId400() {
    $this->_ref_id400 = new CIdSante400;
    if (!$this->_id) {
      return;
    }

    global $g;
    $this->_ref_id400->tag = "sherpa group:$g";
    $this->_ref_id400->object_class = $this->_spec->mbClass;
    $this->_ref_id400->id400 = $this->_id;
    $this->_ref_id400->loadMatchingObject();
    return $this->_ref_id400;
  }
  
  /**
   * Load the linked mediboard object
   * @return CMbObject, null if failed
   */
  function loadMbObject() {
    $this->loadId400();
    $id400 =& $this->_ref_id400;
    if ($id400->_id) {
      $id400->loadRefsFwd();
    }
    
    return $id400->_ref_object;
  }
  
  /**
   * ISO date to Sherpa date
   * @param date YYYY-MM-DD
   * @return string DD/MM/YYYY
   */
  function makeDate($date) {
    if ("0000-00-00" == $date) {
      return "00/00/0000";
    }
    
    return mbTranformTime(null, $date, "%d/%m/%Y");
  }
  
  /**
   * Mediboard string to Sherpa string
   * @param string $string to convert
   * @param int $length to truncate to
   * @return string
   */
  function makeString($string, $length) {
//    $string = str_replace("\r\n", " - ", $string);
    return $string ? strtoupper(substr($string, 0, $length)) : "";
  }

  /**
   * Mediboard  to Sherpa phone number
   * @param string $string to convert
   * @return string
   */
  function makePhone($tel) {
    return preg_replace("/(\d\d)(\d\d)(\d\d)(\d\d)(\d\d)/", "$1.$2.$3.$4.$5", $tel);
  }

  /**
   * Sherpa to Mediboard phone number
   * @param string $string to convert
   * @return string
   */
  function importCodePostal($string) {
    // Quelques 'o'  à la place de 0
    $string = preg_replace("/o/i", "0", $string);
    
    $return = preg_replace("/\D/", "", $string);
    
    if (strlen($return) == 2) {
      $return .= "000";
    }
    
    return strlen($return) == 5 ? $return : ""; 
  }
  
  /**
   * Sherpa to Mediboard phone number
   * @param string $string to convert
   * @return string
   */
  function importPhone($tel) {
    $return = preg_replace("/\D/", "", $tel);
    return strlen($return) == 10 ? $return : ""; 
  }
  
   /**
   * Sherpa to Mediboard matricule 
   * @param string $matricule
   * @param string $cle
   * @return string
   */
  function importMatricule($matricule, $cle) {
    $return =  "$matricule$cle";
    return strlen($return) == 15 ? $return : ""; 
  }
  
}

?>