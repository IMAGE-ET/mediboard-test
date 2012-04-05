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
  var $_ref_chambre    = null;
  var $_ref_service    = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'uf';
    $spec->key   = 'uf_id';
    return $spec;
  }

  function getProps() {
  	$props = parent::getProps();
    $props["group_id"]    = "ref class|CGroups notNull";
    $props["code"]        = "str notNull seekable";
    $props["libelle"]     = "str notNull seekable";
    $props["description"] = "text";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["affectations_uf"] = "CAffectationUniteFonctionnelle uf_id";
    $backProps["affectations_hebergement"]  = "CAffectation uf_hebergement_id";
    $backProps["affectations_medical"]  = "CAffectation uf_medicale_id";
    $backProps["affectations_soin"]  = "CAffectation uf_soins_id";
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
  }
  
  static function getUF($code_uf) {
    $uf = new self;
    $uf->code = $code_uf;
    $uf->loadMatchingObject();
    
    return $uf;
  }
}

?>