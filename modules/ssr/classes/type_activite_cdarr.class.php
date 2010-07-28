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
 * Catégorie d'activité CdARR
 */
class CTypeActiviteCdARR extends CCdARRObject {
  var $code          = null;
	var $libelle       = null;
	var $libelle_court = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'type_activite';
    $spec->key         = 'code';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"]          = "str notNull length|4";
    $props["libelle"]       = "str notNull maxLength|50";
    $props["libelle_court"] = "str notNull maxLength|50";
    
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view      = "($this->code) $this->libelle";
    $this->_shortview = "($this->code) $this->libelle_court";
  }
	
	/**
	 * Get an instance from the code
	 * @param $code string
	 * @return CTypeActiviteCdARR
	 **/
	static function get($code) {
		$found = new CTypeActiviteCdARR();
    $found->load($code);
		return $found;
	}
}

?>