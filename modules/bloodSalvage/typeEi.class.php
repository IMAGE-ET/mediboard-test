<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CTypeEi extends CMbObject {
	
	//DB Table key
	var $type_ei_id = null;
	
	//DB Fields 
	var $name = null;
	var $concerne = null;
	var $desc = null;
	var $type_signalement = null;
	var $evenements = null;
	
	var $_ref_evenement = null ;
  var $_ref_items           = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'type_ei';
    $spec->key   = 'type_ei_id';
    return $spec;
  }
  
  /*
   * Spécifications. Indique les formats des différents éléments et références de la classe.
   */
  function getProps() {
    $specs= parent::getProps();
    $specs["name"]     = "str notNull maxLength|30";
    $specs["concerne"] = "enum notNull list|pat|vis|pers|med|mat";
    $specs["desc"]     = "text";
    $specs["type_signalement"] = "enum notNull list|inc|ris";
    $specs["evenements"] = "str notNull maxLength|255";
    return $specs;
  }
  
	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["blood_salvages"] = "CBloodSalvage type_ei_id";
	  return $backProps;
	}

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
    
    if($this->evenements){
      $this->_ref_evenement = explode("|", $this->evenements);
    } 
  }
  
  function loadRefItems() {
    $this->_ref_items = array();
    foreach ($this->_ref_evenement as $evenement) {
      $ext_item = new CEiItem();
      $ext_item->load($evenement);
      $this->_ref_items[] = $ext_item;
    }
  }
}
?>