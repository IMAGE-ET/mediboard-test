<?php /*  */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: 2249 $
* @author Sherpa
*/

/**
 * Abstract class for bcb objects
 */
  

class CBcbObject {  
  static $objDatabase = null;
  static $TypeDatabase = null;
  
  var $distObj   = null;
  var $distClass = null;
  var $_view     = null;
  
  function initBCBConnection() {
    if(!self::$objDatabase) {
      include_once("lib/bcb/packageBCB.php");
      include_once("lib/bcb/bd_config.inc.php");
      self::$objDatabase = $objDatabase;
      self::$TypeDatabase = $TypeDatabase;
    }
  }
  
  function CBcbObject() {
    $this->initBCBConnection();
    // Creation de la connexion
    $this->distObj = new $this->distClass;
    $result = $this->distObj->InitConnexion(CBcbObject::$objDatabase->LinkDB, CBcbObject::$TypeDatabase);
  }
  
  function load() {
    $this->_updateFormFields();
  }
  
  function updateFormFields() {
    $this->_view = "Object ".$this->distClass;
  }
}

?>