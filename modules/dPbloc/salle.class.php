<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPbloc
 *	@version $Revision$
 *  @author Romain Ollivier
 */

/**
 * The CSalle class
 */
class CSalle extends CMbObject {
  // DB Table key
	var $salle_id = null;
  
  // DB references
  var $group_id = null;
	
  // DB Fields
  var $nom   = null;
  var $stats = null;
  
  var $_ref_group = null;

	function CSalle() {
		$this->CMbObject("sallesbloc", "salle_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
	}

  function getSpecs() {
    return array (
      "group_id" => "ref|notNull",
      "nom"      => "str|notNull",
      "stats"    => "enum|0|1|notNull"
    );
  }
  
  function getSeeks() {
    return array (
      "nom" => "like"
    );
  }
  
  function updateFormFields() {
    $this->_view = $this->nom;
  }

  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "plages opératoires", 
      "name"      => "plagesop", 
      "idfield"   => "plageop_id", 
      "joinfield" => "salle_id"
    );
    
    return CMbObject::canDelete($msg, $oid, $tables);
  }
  
  function loadRefsFwd(){
    // Chargement de l'établissement correspondant
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
}
?>