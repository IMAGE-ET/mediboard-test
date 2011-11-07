<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CRessourceSoin extends CMbObject {
  
  // DB Table key
  var $ressource_soin_id  = null;
  
  // DB Fields
  var $cout = null;
  var $libelle = null;
  var $code = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'ressource_soin';
    $spec->key   = 'ressource_soin_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["code"]    = "str notNull";
    $props["libelle"] = "str notNull";
    $props["cout"]    = "currency";
    return $props;
  }
  
	function updateFormFields(){
		parent::updateFormFields();
	
	  $this->_view = $this->libelle;	
	}
	
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["indices_couts"]   = "CIndiceCout ressource_soin_id";
    
    return $backProps;
  }
}

?>