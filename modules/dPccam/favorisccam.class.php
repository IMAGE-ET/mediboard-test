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

    static $props = array (
      "favoris_user" => "ref|notNull",
      "favoris_code" => "str|notNull"
    );
    $this->_props =& $props;

    static $seek = array (
      "favoris_code" => "equal"
    );
    $this->_seek =& $seek;

    static $enums = null;
    if (!$enums) {
      $enums = $this->getEnums();
    }
    
    $this->_enums =& $enums;
    
    static $enumsTrans = null;
    if (!$enumsTrans) {
      $enumsTrans = $this->getEnumsTrans();
    }
    
    $this->_enumsTrans =& $enumsTrans;
	}
}
?>