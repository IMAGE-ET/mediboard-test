<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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

  static $tags = array();
  
  static function makeTags() {
		$idExt = new CIdSante400;
		$idExt->loadLatestFor(CGroups::loadCurrent(), "eCap");
		$codeClinique = $idExt->id400;
		
		self::$tags = array(
		  "IPP"  => "eCap CIDC:$codeClinique",
			"DOS" => "eCap NDOS CIDC:$codeClinique",
			"USR" => "eCap CIDC:$codeClinique",
			"DOC" => "eCap CIDC:$codeClinique"
		);	    
  }
}

?>