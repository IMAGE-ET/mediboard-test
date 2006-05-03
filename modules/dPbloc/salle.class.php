<?php /* $Id: salle.class.php,v 1.9 2005/12/10 19:03:23 rhum1 Exp $ */

/**
 *	@package Mediboard
 *	@subpackage dPbloc
 *	@version $Revision: 1.9 $
 *  @author Romain Ollivier
 */


require_once( $AppUI->getSystemClass('mbobject'));

/**
 * The CGroups class
 */
class CSalle extends CMbObject {
  // DB Table key
	var $id = null;
	
  // DB Fields
  var $nom = null;
  var $stats = null;

	function CSalle() {
		$this->CMbObject( 'sallesbloc', 'id' );

    $this->_props["nom"]   = "str|notNull|confidential";
    $this->_props["stats"] = "enum|0|1|notNull";
	}

  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      'label' => 'plages opératoires', 
      'name' => 'plagesop', 
      'idfield' => 'id', 
      'joinfield' => 'id_salle'
    );
    
    return CDpObject::canDelete( $msg, $oid, $tables );
  }
}
?>