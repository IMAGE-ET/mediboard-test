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
}

?>