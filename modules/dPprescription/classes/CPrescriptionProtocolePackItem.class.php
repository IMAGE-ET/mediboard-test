<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrescriptionProtocolePackItem extends CMbObject {
  // DB Table key
  var $prescription_protocole_pack_item_id = null;
  
  // DB Fields
  var $prescription_protocole_pack_id = null;
  var $prescription_id = null;
  
  // Object references
  var $_ref_prescription = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_protocole_pack_item';
    $spec->key   = 'prescription_protocole_pack_item_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["prescription_protocole_pack_id"] = "ref notNull class|CPrescriptionProtocolePack";
    $specs["prescription_id"]                = "ref notNull class|CPrescription";
    return $specs;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->loadRefPrescription();
    $this->_view = $this->_ref_prescription->_view;
  }
  
  
  function loadRefPrescription(){
    $this->_ref_prescription = new CPrescription();
    $this->_ref_prescription->load($this->prescription_id);
  }
  
  function loadRefsFwd(){
    parent::loadRefsFwd();
    $this->loadRefPrescription();
  }
}

?>