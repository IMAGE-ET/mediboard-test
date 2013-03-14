<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Gestes (activités) complémentaires CsARR
 */
class CGesteComplementaireCsARR extends CCsARRObject {
  
  var $code_source = null;
  var $code_cible  = null;
    
  var $_ref_code_source = null;
  var $_ref_code_cible  = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'geste_complementaire';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code_source"] = "str notNull length|7";
    $props["code_cible" ] = "str notNull length|7";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->code_source => $this->code_cible";
  }
  
  function loadRefCodeSource() {
    return $this->_ref_code_source = CActiviteCdARR::get($this->code_source);
  }

  function loadRefCodeCible() {
    return $this->_ref_code_cible = CActiviteCdARR::get($this->code_cible);
  }
	
	function loadView(){
    parent::loadView();
    $this->loadRefCodeSource();
    $this->loadRefCodeCible();
  }
}

?>