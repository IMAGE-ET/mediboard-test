<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSejourTask extends CMbObject {
  
  // DB Table key
  var $sejour_task_id = null;
  
	// DB Fields
	var $sejour_id      = null;
	var $description    = null;
	var $realise        = null;
	var $resultat       = null;
	
	var $_ref_sejour    = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sejour_task';
    $spec->key   = 'sejour_task_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
		$specs["sejour_id"]   = "ref notNull class|CSejour";
    $specs["description"] = "text notNull helped";
		$specs["realise"]     = "bool default|0";
		$specs["resultat"]    = "text helped";
    return $specs;
  }

  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->description;
  }
	
	function loadRefSejour() {
    $this->_ref_sejour = new CSejour();
    $this->_ref_sejour = $this->_ref_sejour->getCached($this->sejour_id);
  }
}
  
?>