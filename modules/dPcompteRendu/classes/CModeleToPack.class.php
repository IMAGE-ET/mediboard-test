<?php

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author
 */
class CModeleToPack extends CMbObject {
	// DB Table key
  var $modele_to_pack_id = null;
  
  // DB References
  var $modele_id       = null;
  var $pack_id         = null;
  
  // Referenced objects
  var $_ref_modele     = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'modele_to_pack';
    $spec->key   = 'modele_to_pack_id';
    $spec->uniques['document'] = array('modele_id', 'pack_id');
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["modele_id"]   = "ref class|CCompteRendu";
    $specs["pack_id"]     = "ref class|CPack cascade";
    return $specs;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->loadRefModele()->nom;
  }
  
  function loadRefModele(){
    return $this->_ref_modele = $this->loadFwdRef("modele_id", true);
  }
  
  function loadAllModelesFor($pack_id) {
  	$where = array();
  	$where["pack_id"] = " = $pack_id";
  	return $this->loadList($where);
  }
}
?>