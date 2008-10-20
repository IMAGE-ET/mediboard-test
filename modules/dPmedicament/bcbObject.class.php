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
  static $objDatabaseGestion = null;
  static $TypeDatabaseGestion = null;
  
  var $distObj   = null;
  var $distClass = null;
  var $_view     = null;
  
  function initBCBConnection() {
    
    global $dPconfig;
    include_once("lib/bcb/PackageBCB.php");
    
    // Connexion a la base BCB
    if(!self::$objDatabase) {  
      // Connexion a la base BCB
      $objDatabase = new BCBConnexion();
      $TypeDatabase=2;
      $Result = $objDatabase->ConnectDatabase("org.gjt.mm.mysql.Driver", 
        $dPconfig["db"]["bcb"]["dbhost"], 
        $dPconfig["db"]["bcb"]["dbname"], 
        $dPconfig["db"]["bcb"]["dbuser"], 
        $dPconfig["db"]["bcb"]["dbpass"], 
        $TypeDatabase
      );
      
      if ($Result < 1) {
        trigger_error("Erreur base " . $Result . " : " . $objDatabase->GetLastError(), E_USER_ERROR);
        CApp::rip();
      }
      
      self::$objDatabase = $objDatabase;
      self::$TypeDatabase = $TypeDatabase;
    }  

    // Connexion a la base BCB Gestion
    if(!self::$objDatabaseGestion) { 
      global $AppUI;
      // Test de connexion a bcbges
      if (!CSQLDataSource::get("bcbges")) {
        $AppUI->stepAjax("Connexion vers la DSN bcbges échouée", UI_MSG_ERROR);
      }
      
      // Connexion a la base BCB Gestion
      $objDatabaseGestion = new BCBConnexionGestion();
      $TypeDatabaseGestion=2;
      $Result = $objDatabaseGestion->ConnectDatabase("org.gjt.mm.mysql.Driver",
        $dPconfig["db"]["bcbges"]["dbhost"], 
        $dPconfig["db"]["bcbges"]["dbname"],
        $dPconfig["db"]["bcbges"]["dbuser"],
        $dPconfig["db"]["bcbges"]["dbpass"],
        $TypeDatabaseGestion
      );
      
      if ($Result < 1) {
        trigger_error("Erreur base gestion " . $Result . " : " . $objDatabase->GetLastError(), E_USER_ERROR);
        CApp::rip();
      }

      self::$objDatabaseGestion = $objDatabaseGestion;
      self::$TypeDatabaseGestion = $TypeDatabaseGestion;   
    }
  }
  
  function CBcbObject() {
    $this->initBCBConnection();
    // Creation de la connexion
    $this->distObj = new $this->distClass;
    $result = $this->distObj->InitConnexion(CBcbObject::$objDatabase->LinkDB, CBcbObject::$TypeDatabase);
  }
   
  function updateFormFields() {
    $this->_view = "Object ".$this->distClass;
  }
}

?>