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
      
      include_once("lib/bcb/PackageBCB.php");
      
      global $dPconfig;
      $objDatabase = new BCBConnexion();
      $TypeDatabase=2;
      $Result = $objDatabase->ConnectDatabase("org.gjt.mm.mysql.Driver", 
        $dPconfig["db"]["bcb"]["dbhost"], 
        $dPconfig["db"]["bcb"]["dbname"], 
        $dPconfig["db"]["bcb"]["dbuser"], 
        $dPconfig["db"]["bcb"]["dbpass"], 
        $TypeDatabase
      );
      
      if ($Result < 1) die("Erreur base " . $Result . " :" . $objDatabase->GetLastError());
      
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