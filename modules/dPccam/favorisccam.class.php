<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

/**
 * The CFavoriCCAM Class
 */
class CFavoriCCAM extends CMbObject {
	var $favoris_id = null;
	var $favoris_user = null;
  var $favoris_code = null;

	function CFavoriCCAM() {
		$this->CMbObject("ccamfavoris", "favoris_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
	}

  function getSpecs() {
    return array (
      "favoris_user" => "ref|notNull",
      "favoris_code" => "str|length|7|notNull"
    );
  }
  
  function getSeeks() {
    return array (
      "favoris_code" => "equal"
    );
  }
}
?>