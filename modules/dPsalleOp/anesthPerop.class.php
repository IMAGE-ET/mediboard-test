<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CAnesthPerop extends CMbObject {
  // DB Table key
  var $anesth_perop_id = null;

  // DB References
  var $operation_id = null;
  
	// DB fields
  var $libelle  = null;
  var $datetime = null;
	
	// Fwd References
  var $_ref_operation = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'anesth_perop';
    $spec->key   = 'anesth_perop_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["operation_id"] = "ref notNull class|COperation";
    $specs["libelle"]      = "str notNull helped";
		$specs["datetime"]     = "dateTime notNull";
    return $specs;
  }
  
	function updateFormFields(){
	  $this->_view = "$this->libelle à $this->datetime";
		
	}
	
	function loadRefOperation(){
    $this->_ref_operation = new COperation();
    $this->_ref_operation = $this->_ref_operation->getCached($this->operation_id);
  }
	
  function getPerm($permType) {
    if(!$this->_ref_operation) {
      $this->loadRefOperation();
    }
    return $this->_ref_operation->getPerm($permType);
  }
	
}

?>