<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage sherpa
* @version $Revision$
* @author Sherpa
*/

/**
 * Abstract class for sherpa objects
 * - base association
 */
class CSpObject extends CMbObject {  
  public $_ref_id400 = null;
  
  public $_current_group_id = null;
  
  function CSpObject() {
    foreach (array_keys($this->getProps()) as $prop) {
      $this->$prop = null;
    }
    parent::__construct();
  }
  
  function loadExternal() {
    $this->_external = true;
  }
  
  /**
   * Fournit la data source pour l'établissement courant
   * @return CSQLDataSource
   */
  function getCurrentDataSource() {
    global $g;
    $this->changeDSN($g);
    return $this->_spec->ds;
  }
  
  /**
   * Change the data source
   * @param int $g group_id
   */
  function changeDSN($g) {
    $this->_current_group_id = $g;
    $this->_spec->dsn = "sherpa-$g";
    $this->_spec->init();
    
    // Declare unquotable columns
    if ($ds =& $this->_spec->ds) {
      $ds->unquotable = array(
        "es_entccam" => array("idinterv", "valigs"),
        "es_detccam" => array("idinterv", "idacte", "dephon", "codsig"),
        "es_detcim" => array("idinterv", "iddiag"),
        "es_ngap" => array("idacte", "idinterv", "quant", "coeff", "valdep"),
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
   * Check if the SpObject is really concerned by the MbObject
   * according to its state
   * @param CMbObject $mbObject
   * @return bool
   */
  function isConcernedBy(CMbObject &$mbObject) {
    return true;
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

  function getBackProps() {
    return array();
  }
 
  function getProps() {
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
  static function makeDate($date) {
    if ("0000-00-00" == $date) {
      return "00/00/0000";
    }
    
    return mbTransformTime(null, $date, "%d/%m/%Y");
  }
  
  /**
   * Mediboard string to Sherpa string
   * @param string $string to convert
   * @param int $length to truncate to
   * @return string
   */
  static function makeString($string, $length = 200) {
//    $string = str_replace("\r\n", " - ", $string);
    return $string ? strtoupper(removeAccent(substr($string, 0, $length))) : "";
  }

  /**
   * Mediboard  to Sherpa phone number
   * @param string $string to convert
   * @return string
   */
  static function makePhone($tel) {
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
  
  /**
   * Sherpa to Mediboard DateTime (will remove seconds)
   * @param string $dateTime
   * @return string
   */
  function importDateTime($dateTime) {
    $return =  preg_replace("/(\d{2}):(\d{2}):(\d{2})/", "$1:$2:00", mbDateToLocale($dateTime));
    return strlen($return) == 19 ? $return : ""; 
  }
}

?>