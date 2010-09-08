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
    $specs["pack_id"]     = "ref class|CPack";
    return $specs;
  }
  
  function loadAllModelesFor($pack_id) {
  	$modeles = array();
  	$where = array();
  	$where["pack_id"] = " = $pack_id";
  	$modeles = $this->loadList($where);
  	return $modeles;
  }
  
  function deleteAllModelesFor($pack_id) {
  	$modelestopack = $this->loadAllModelesFor($pack_id);
  	foreach($modelestopack as $_modele) {
      if ($msg = $_modele->delete()) {
  		 CAppUI::setMsg( $msg, UI_MSG_ERROR );
      }
  	}
  }
}
?>