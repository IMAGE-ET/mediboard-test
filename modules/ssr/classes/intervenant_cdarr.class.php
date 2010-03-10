<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("ssr", "cdarrObject");

/**
 * Intervenant d'activité CdARR
 */
class CIntervenantCdARR extends CCdARRObject {  
  var $code    = null;
	var $libelle = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'intervenant';
    $spec->key         = 'code';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"]    = "str notNull length|2";
    $props["libelle"] = "str notNull maxLength|50";
    
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->code - $this->libelle";
    $this->_shortview = $this->code;
  }
	
	/**
	 * Get an instance from the code
	 * @param $code string
	 * @return CIntervenantCdARR
	 **/
	static function get($code) {
		$found = new CIntervenantCdARR();
    $found->load($code);
		return $found;
	}
}

?>