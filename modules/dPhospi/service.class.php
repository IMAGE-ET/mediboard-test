<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
 *  @author Thomas Despoix
*/

require_once($AppUI->getSystemClass("mbobject"));
require_once($AppUI->getModuleClass("dPhospi", "chambre"));
require_once($AppUI->getModuleClass("dPetablissement", "groups"));

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

	function CService() {
		$this->CMbObject("service", "service_id");

    $this->_props["group_id"]    = "ref|notNull";
    $this->_props["nom"]         = "str|notNull|confidential";
    $this->_props["description"] = "str|confidential";

    $this->_seek["nom"]         = "like";
    $this->_seek["description"] = "like";
	}

  function loadRefsBack() {
    // Backward references
    $where["service_id"] = "= '$this->service_id'";
    $order = "nom";
    $this->_ref_chambres = new CChambre;
    $this->_ref_chambres = $this->_ref_chambres->loadList($where, $order);
  }

  function loadRefsFwd(){
    // Chargement de l'établissement correspondant
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  function canRead() {
    $this->loadRefsFwd();
    $this->_canRead = $this->_ref_group->canRead();
    return $this->_canRead;
  }

  function canEdit() {
    $this->loadRefsFwd();
    $this->_canEdit = $this->_ref_group->canEdit();
    return $this->_canEdit;
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "Chambres", 
      "name"      => "chambre", 
      "idfield"   => "chambre_id", 
      "joinfield" => "service_id"
    );
        
    return CDpObject::canDelete( $msg, $oid, $tables );
  }
}
?>