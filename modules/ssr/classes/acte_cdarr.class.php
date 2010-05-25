<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CActeCdARR extends CMbObject {
  // DB Table key
	var $acte_cdarr_id = null;
	
	// DB Fields
  var $evenement_ssr_id = null;
	var $code = null;
 
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'acte_cdarr';
    $spec->key         = 'acte_cdarr_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["evenement_ssr_id"] = "ref notNull class|CEvenementSSR cascade";
    $props["code"]             = "str notNull length|4";
    return $props;
  }

  function updateFormFields(){
  	parent::updateFormFields();
		$this->_view = $this->code;
  }
	
	function loadRefEvenementSSR(){
		$this->_ref_evenement_ssr = $this->loadFwdRef("evenement_ssr_id", true);
	}
}

?>