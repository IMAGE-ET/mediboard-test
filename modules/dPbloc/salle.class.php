<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPbloc
 *	@version $Revision$
 *  @author Romain Ollivier
 */


require_once( $AppUI->getSystemClass("mbobject"));

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

	function CSalle() {
		$this->CMbObject("sallesbloc", "salle_id");

    $this->_props["group_id"] = "ref|notNull";
    $this->_props["nom"]      = "str|notNull";
    $this->_props["stats"]    = "enum|0|1|notNull";
    
    $this->_seek["nom"] = "like";
	}

  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "plages opératoires", 
      "name"      => "plagesop", 
      "idfield"   => "plageop_id", 
      "joinfield" => "salle_id"
    );
    
    return CDpObject::canDelete($msg, $oid, $tables);
  }
}
?>