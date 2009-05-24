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
  static $cidc = null;
  
  static $paths = array (
    "dhe" => "/InterfaceCabinets/Generique/AppelDHE.aspx",
    "soap" => array (
      "documents" => "/GestionDocumentaire.asmx",
    ),
  );
  
  static $urls = array();
  
  /**
   * Computes URLs from module config
   */
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

  static $tags = null;
  
  /**
   * Get tags for current group
   * $param string $which Tag you want [IPP|DOS|USR|DOC|CAR], 
   *   null if you just want to build them
   */
  static function getTag($which = null) {
    static $init = true; 
    if ($init) {
			$idExt = new CIdSante400;
			$idExt->loadLatestFor(CGroups::loadCurrent(), "eCap");
			self::$cidc = $cidc = $idExt->id400;
			
			self::$tags = array(
			  "PA" => "eCap CIDC:$cidc",
				"SJ" => "eCap NDOS CIDC:$cidc",
				"AT" => "eCap DHE CIDC:$cidc",
				"IN" => "eCap CINT CIDC:$cidc",
				"US" => "eCap CIDC:$cidc",
				"DO" => "eCap document",
				"DT" => "eCap type-category",
			);
      
      $init = false;
    }
    
    return $which ? self::$tags[$which] : null;
  }  
}

?>