<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CDestinataireHprim extends CMbObject {
  // DB Table key
  var $dest_hprim_id  = null;
  
  // DB Fields
  var $nom       = null;
  var $group_id  = null;
  var $type      = null;
	var $evenement = null;
  var $actif     = null;
  
  // Forward references
  var $_ref_group = null;
  var $_ref_exchange_source = null;
  
  // Form fields
  var $_tag_patient = null;
  var $_tag_sejour  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'destinataire_hprim';
    $spec->key   = 'dest_hprim_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["nom"]       = "str notNull";
    $specs["group_id"]  = "ref notNull class|CGroups";
    $specs["type"]      = "enum notNull list|cip|sip default|cip";
		$specs["evenement"] = "enum list|pmsi|patients|stock default|patient";
    $specs["actif"]     = "bool notNull";
    
    $specs["_tag_patient"] = "str";
    $specs["_tag_sejour"]  = "str";
    return $specs;
  }
  
  function loadRefsFwd() {
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
    
    $this->_ref_exchange_source = CExchangeSource::get($this->_guid, null, true);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_tag_patient = str_replace('$g', $this->group_id, CAppUI::conf("dPpatients CPatient tag_ipp"));
    $this->_tag_sejour  = str_replace('$g', $this->group_id, CAppUI::conf("dPplanningOp CSejour tag_dossier"));
  }
}
?>