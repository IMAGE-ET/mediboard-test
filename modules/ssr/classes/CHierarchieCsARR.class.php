<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Hierarchie CsARR
 */
class CHierarchieCsARR extends CCsARRObject {
  var $code          = null;
  var $libelle       = null;
  
  static $cached = array();
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'hierarchie';
    $spec->key   = 'code';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"]    = "str notNull length|11 seekable";
    $props["libelle"] = "str notNull seekable";
 
    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->code;
    $this->_shortview = $this->code;
  }
  
	/**
	 * Get an instance from the code
	 * @param $code string
	 * @return CActiviteCdARR
	 **/
  static function get($code) {
  	if (!$code) {
  	  return new self(); 
  	}
  	
    if (!isset(self::$cached[$code])) {
      $hierarchie = new self();
      $hierarchie->load($code);
      self::$cached[$code] = $hierarchie;
    }
    
    return self::$cached[$code];
  }
}

?>