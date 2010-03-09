<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Technicien de SSR, association entre un plateau technique et un utilisateur
 */
class CTechnicien extends CMbObject {
  // DB Table key
  var $technicien_id = null;
  
  // References
  var $plateau_id = null;
  var $kine_id = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'technicien';
    $spec->key   = 'technicien_id';
		$spec->uniques["technicien"] = array("kine_id");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["plateau_id"] = "ref notNull class|CPlateauTechnique";
    $specs["kine_id"]    = "ref notNull class|CMediusers";
    return $specs;
  }
  
  function loadRefKine() {
    $this->_ref_kine = $this->loadFwdRef("kine_id", true);
		$this->_ref_kine->loadRefFunction();
    $this->_view = $this->_ref_kine->_view;
  }
}

?>