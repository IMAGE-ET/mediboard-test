<?php /* $Id: service.class.php,v 1.6 2005/09/21 19:18:41 rhum1 Exp $ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision: 1.6 $
 *  @author Thomas Despoix
*/

require_once($AppUI->getSystemClass('mbobject'));
require_once($AppUI->getModuleClass('dPhospi', 'chambre'));

/**
 * Classe CService. 
 * @abstract Gère les services d'hospitalisation
 * - contient de chambres
 */
class CService extends CMbObject {
  // DB Table key
	var $service_id = null;	

  // DB Fields
  var $nom = null;
  var $description = null;
  
  // Object references
  var $_ref_chambres = null;

	function CService() {
		$this->CMbObject( 'service', 'service_id' );
    
    $this->_props["nom"] = "str|notNull|confidential";
    $this->_props["description"] = "str|confidential";
	}

  function loadRefsBack() {
    // Backward references
    $where["service_id"] = "= '$this->service_id'";
    $order = "nom";
    $this->_ref_chambres = new CChambre;
    $this->_ref_chambres = $this->_ref_chambres->loadList($where, $order);
  }

  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      'label' => 'Chambres', 
      'name' => 'chambre', 
      'idfield' => 'chambre_id', 
      'joinfield' => 'service_id'
    );
        
    return CDpObject::canDelete( $msg, $oid, $tables );
  }
}
?>