<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPlateauTechnique extends CMbObject {
  // DB Table key
  var $plateau_id = null;
  
  // References
  var $group_id = null;

  // DB Fields
  var $nom      = null;
	
	// Collections
  var $_ref_equipements = null;
  var $_ref_techniciens = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'plateau_technique';
    $spec->key   = 'plateau_id';
    return $spec;
  }

  function getProps() {
  	$props = parent::getProps();
    $props["group_id"] = "ref notNull class|CGroups";
    $props["nom"]      = "str notNull";
    return $props;
  }
		
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["equipements"] = "CEquipement plateau_id";
    $backProps["techniciens"] = "CTechnicien plateau_id";
    return $backProps;
  }
  
	function updateFormFields() {
		parent::updateFormFields();
		$this->_view = $this->nom;
	}
	
	function loadRefsEquipements() {
		$this->_ref_equipements = $this->loadBackRefs("equipements");
	}

  function loadRefsTechniciens() {
    $this->_ref_techniciens = $this->loadBackRefs("techniciens");
		foreach ($this->_ref_techniciens as $_technicien) {
		  $_technicien->loadRefKine();
		}
  }
}

?>