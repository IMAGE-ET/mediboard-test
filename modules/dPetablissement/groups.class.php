<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPetablissement
 *	@version $Revision$
 *  @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("mbobject"));
require_once($AppUI->getModuleClass("mediusers", "functions"));

/**
 * The CGroups class
 */
class CGroups extends CMbObject {
  // DB Table key
	var $group_id = null;	

  // DB Fields
	var $text = null;

  // Object References
  var $_ref_functions = null;

  function CGroups() {
    $this->CMbObject("groups_mediboard", "group_id");

    $this->_props["text"] = "str|notNull|confidential";
    
    $this->_seek["text"] = "like";
  }
  
  function updateFormFields () {
    parent::updateFormFields();

    $this->_view = $this->text;
    if(strlen($this->text) > 25)
      $this->_shortview = substr($this->text, 0, 23)."...";
    else
      $this->_shortview = $this->text;
  }

  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "Fonctions", 
      "name"      => "functions_mediboard", 
      "idfield"   => "function_id", 
      "joinfield" => "group_id"
    );
    $tables[] = array (
      "label"     => "Sejours", 
      "name"      => "sejour", 
      "idfield"   => "sejour_id", 
      "joinfield" => "group_id"
    );
    
    return CDpObject::canDelete( $msg, $oid, $tables );
  }

  // Backward References
  function loadRefsBack() {
  	$where = array(
      "group_id" => "= '$this->group_id'");
    $order = "type, text";
    $this->_ref_functions = new CFunctions;
    $this->_ref_functions = $this->_ref_functions->loadList($where, $order);
  }
}
?>