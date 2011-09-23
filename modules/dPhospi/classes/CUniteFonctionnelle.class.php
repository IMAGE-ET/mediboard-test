<?php /* $Id: CTransmissionMedicale.class.php 12900 2011-08-22 14:07:55Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 12900 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @abstract Liste les unités fonctionnelles des établissements 
 */

class CUniteFonctionnelle extends CMbObject {
  // DB Table key
  var $uf_id = null;	
  
  // DB Fields
  var $group_id    = null;
  var $code        = null;
  var $libelle     = null;
  var $description = null;
  
  // References
  var $_ref_group           = null;
  var $_ref_affectations_uf = null;
  
  // Distant references
  var $_ref_praticiens = null;
  var $_ref_lits       = null;
  var $_ref_sejours    = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'uf';
    $spec->key   = 'uf_id';
    return $spec;
  }

  function getProps() {
  	$props = parent::getProps();
    $props["group_id"]    = "ref class|CGroups notNull";
    $props["code"]        = "str notNull";
    $props["libelle"]     = "str notNull";
    $props["description"] = "text";
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
  }
}

?>