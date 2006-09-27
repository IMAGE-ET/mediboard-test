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