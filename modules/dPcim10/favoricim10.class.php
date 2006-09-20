<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

/**
 * The CFavoricim10 Class
 */
class CFavoricim10 extends CMbObject {
	var $favoris_id   = null;
	var $favoris_code = null;
	var $favoris_user = null;

	function CFavoricim10() {
		$this->CMbObject("cim10favoris", "favoris_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    $this->_props["favoris_user"] = "ref|notNull";
    $this->_props["favoris_code"] = "str|notNull";
    
    $this->_seek["favoris_code"] = "equal";
	}
}
?>