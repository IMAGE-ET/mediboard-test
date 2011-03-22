<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrescriptionProtocolePack extends CMbObject {
  // DB Table key
  var $prescription_protocole_pack_id = null;
  
  // DB Fields
  var $libelle      = null;
  var $praticien_id = null;  // Pack associé à un praticien
  var $function_id  = null;  // Pack associé à un cabinet
  var $group_id     = null;  // Pack associé à un établissement
  
  var $object_class = null;
  
  // FwdRefs
  var $_ref_praticien = null;
  var $_ref_function  = null;
  var $_ref_group     = null;
  
  // BackRefs
  var $_ref_protocole_pack_items = null;
	var $_ref_protocole_pack_items_by_type = null;
	var $_counts_by_chapitre = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_protocole_pack';
    $spec->key   = 'prescription_protocole_pack_id';
    $spec->xor["owner"] = array("praticien_id", "function_id", "group_id");
    return $spec;
  }
    
  function getProps() {
  	$specs = parent::getProps();
    $specs["praticien_id"]  = "ref class|CMediusers";
    $specs["function_id"]   = "ref class|CFunctions";  
    $specs["group_id"]      = "ref class|CGroups";
    $specs["libelle"]       = "str notNull";
    $specs["object_class"]  = "enum notNull list|CSejour|CConsultation";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["prescription_protocole_pack_items"] = "CPrescriptionProtocolePackItem prescription_protocole_pack_id";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = "Pack: " . $this->libelle;
  }
  
  /*
   * Chargement des item de packs (protocoles)
   */
  function loadRefsPackItems(){
    $this->_ref_protocole_pack_items = $this->loadBackRefs("prescription_protocole_pack_items");
  }
  
  function loadRefsPackItemsByType($type = null) {
    if (!$this->_ref_protocole_pack_items) {
      $this->loadRefsPackItems();
    }
    if ($type) {
      $this->_ref_protocole_pack_items_by_type = array($type => array());
    }
    else {
      $this->_ref_protocole_pack_items_by_type = $this->loadTypes();
    }
    $protocoles = $this->_ref_protocole_pack_items;
    foreach ($protocoles as $_protocole) {
      if ($type && $_protocole->_ref_prescription->type != $type) {
        continue;
      }
      $this->_ref_protocole_pack_items_by_type[$_protocole->_ref_prescription->type][] = $_protocole;
    }
  }
  
  function loadTypes() {
    return $this->object_class == "CSejour" ?
      array("pre_admission"  => array(),
            "sejour" => array(),
            "sortie" => array()) :
      array("externe" => array());
  }
  
  function loadRefsBack(){
    parent::loadRefsBack();
    $this->loadRefsPackItems();
  }

  function loadRefPraticien(){
    $this->_ref_praticien = new CMediusers();
    $this->_ref_praticien->load($this->praticien_id);  
  }
  
  function loadRefFunction(){
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);
  }

  function loadRefGroup(){
    $this->_ref_group = new CGroups();
    $this->_ref_group->load($this->group_id);
  }
  
  function loadRefsFwd(){
    parent::loadRefsFwd();
    $this->loadRefPraticien();
    $this->loadRefFunction();
    $this->loadRefGroup();
  }
	
	function countElementsByChapitre(){
		$this->loadRefsPackItems();
	  foreach($this->_ref_protocole_pack_items as $_pack_item){
	    $_pack_item->loadRefPrescription();
	    $_prescription =& $_pack_item->_ref_prescription; 
	    $_prescription->countLinesMedsElements();
	    foreach($_prescription->_counts_by_chapitre as $chapitre => $_count_chapitre){
	      if($_count_chapitre){
	        if(!isset($this->_counts_by_chapitre[$chapitre])){
	          $this->_counts_by_chapitre[$chapitre] = 0;
	        }
	        $this->_counts_by_chapitre[$chapitre] += $_count_chapitre;
	      }
	    }
	  }
	}
}

?>