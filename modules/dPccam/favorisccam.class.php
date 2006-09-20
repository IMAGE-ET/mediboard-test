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
    
    $this->_props["favoris_user"] = "ref|notNull";
    $this->_props["favoris_code"] = "str|notNull";
    
    $this->_seek["favoris_code"] = "equal";
	}
}
?>