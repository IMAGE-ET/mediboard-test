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

	function CService() {
		$this->CMbObject("service", "service_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    static $props = array (
      "group_id"    => "ref|notNull",
      "nom"         => "str|notNull|confidential",
      "description" => "str|confidential"
    );
    $this->_props =& $props;

    static $seek = array (
      "nom"         => "like",
      "description" => "like"
    );
    $this->_seek =& $seek;

    static $enums = null;
    if (!$enums) {
      $enums = $this->getEnums();
    }
    
    $this->_enums =& $enums;
    
    static $enumsTrans = null;
    if (!$enumsTrans) {
      $enumsTrans = $this->getEnumsTrans();
    }
    
    $this->_enumsTrans =& $enumsTrans;
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
    return ($this->_ref_group->getPerm($permType));
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
}
?>