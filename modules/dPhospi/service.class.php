<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
 *  @author Thomas Despoix
*/

/**
 * Classe CService. 
 * @abstract Gère les services d'hospitalisation
 * - contient de chambres
 */
class CService extends CMbObject {
  // DB Table key
	var $service_id = null;	
  
  // DB references
  var $group_id = null;

  // DB Fields
  var $nom = null;
  var $description = null;
  
  // Object references
  var $_ref_chambres = null;
  var $_ref_group    = null;
  var $_ref_validrepas = null;

	function CService() {
		$this->CMbObject("service", "service_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
	}

  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["chambres"] = "CChambre service_id";
      $backRefs["valid_repas"] = "CValidationRepas service_id";
     return $backRefs;
  }

  function getSpecs() {
    return array (
      "group_id"    => "notNull ref class|CGroups",
      "nom"         => "notNull str",
      "description" => "text confidential"
    );
  }
  
  function getSeeks() {
    return array (
      "nom"         => "like",
      "description" => "like"
    );
  }
  
  function updateFormFields(){
    $this->_view = $this->nom;
  }

  function loadRefsBack() {
    // Backward references
    $where["service_id"] = "= '$this->service_id'";
    $order = "nom";
    $this->_ref_chambres = new CChambre;
    $this->_ref_chambres = $this->_ref_chambres->loadList($where, $order);
  }

  function loadRefsFwd(){
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_group) {
      $this->loadRefsFwd();
    }
    return (CPermObject::getPermObject($this, $permType) && $this->_ref_group->getPerm($permType));
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "Chambres", 
      "name"      => "chambre", 
      "idfield"   => "chambre_id", 
      "joinfield" => "service_id"
    );
        
    return CMbObject::canDelete( $msg, $oid, $tables );
  }
  
  function validationRepas($date, $listTypeRepas = null){
    $this->_ref_validrepas[$date] = array();
    $validation =& $this->_ref_validrepas[$date];
    if(!$listTypeRepas){
      $listTypeRepas = new CTypeRepas;
      $order = "debut, fin, nom";
      $listTypeRepas = $listTypeRepas->loadList(null,$order);
    }
    
    $where               = array();
    $where["date"]       = db_prepare(" = %", $date);
    $where["service_id"] = db_prepare(" = %", $this->service_id);
    foreach($listTypeRepas as $keyType=>$typeRepas){
      $where["typerepas_id"] = db_prepare("= %",$keyType);
      $validrepas = new CValidationRepas;
      $validrepas->loadObject($where);
      $validation[$keyType] = $validrepas;
    }
  }
}
?>