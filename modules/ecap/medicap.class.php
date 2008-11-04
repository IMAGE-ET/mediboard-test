<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: $
 * @author Thomas Despoix
 */

/**
 * @abstract Utility class for eCap modules
 */
class CMedicap {
  static $paths = array (
    "dhe" => "/InterfaceCabinets/Generique/AppelDHE.aspx",
    "soap" => array (
      "documents" => "/GestionDocumentaire.asmx",
    ),
  );
  
  static $urls = array();
  
  static function makeURLs() {
    $soap = CAppUI::conf("ecap soap");
    $dhe = CAppUI::conf("ecap dhe");
    
    self::$urls = array (
    "dhe" => $dhe["rooturl"] . "/InterfaceCabinets/Generique/AppelDHE.aspx",
    "soap" => array (
      "documents" => $soap["rooturl"] . "/GestionDocumentaire.asmx",
      ),    
    );
  }
}

?>