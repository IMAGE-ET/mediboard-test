<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Modulateur d'activités CsARR
 */
class CModulateurCsARR extends CCsARRObject {
  
  var $code       = null;
  var $modulateur = null;
    
  var $_ref_code = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'modulateur';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"       ] = "str notNull length|7";
    $props["modulateur" ] = "str notNull length|2";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->code : $this->modulateur";
  }
  
  function loadRefCode() {
    return $this->_ref_code = CActiviteCdARR::get($this->code);
  }
	
	function loadView(){
    parent::loadView();
    $this->loadRefCode();
  }
}

?>