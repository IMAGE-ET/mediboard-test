<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Equipement de SSR, fait parti d'un plateau technique
 */
class CEquipement extends CMbObject {
  // DB Table key
  var $equipement_id = null;
  
  // References
  var $plateau_id = null;

  // DB Fields
  var $nom      = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'equipement';
    $spec->key   = 'equipement_id';
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["plateau_id"] = "ref notNull class|CPlateauTechnique";
    $specs["nom"]           = "str notNull";
    return $specs;
  }
	
	function updateFormFields() {
		parent::updateFormFields();
		$this->_view = $this->nom;
	}
}

?>