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
	var $group_id       = null;	

  // DB Fields
	var $text           = null;
  var $raison_sociale = null;
  var $adresse        = null;
  var $cp             = null;
  var $ville          = null;
  var $tel            = null;
  var $directeur      = null;
  var $domiciliation  = null;
  var $siret          = null;

  // Object References
  var $_ref_functions = null;

  // Form fields
  var $_tel1        = null;
  var $_tel2        = null;
  var $_tel3        = null;
  var $_tel4        = null;
  var $_tel5        = null;
  
  function CGroups() {
    $this->CMbObject("groups_mediboard", "group_id");

    $this->_props["text"]           = "str|notNull|confidential";
    $this->_props["raison_sociale"] = "str|maxLength|50|confidential";
    $this->_props["adresse"]        = "text|confidential";
    $this->_props["cp"]             = "num|length|5|confidential";
    $this->_props["ville"]          = "str|maxLength|50|confidential";
    $this->_props["tel"]            = "num|length|10|confidential";
    $this->_props["directeur"]      = "str|maxLength|50|confidential";
    $this->_props["domiciliation"]  = "str|maxLength|9|confidential";
    $this->_props["siret"]          = "num|length|14|confidential";
    
    $this->_seek["text"] = "like";
  }
  
    
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->text;
    if(strlen($this->text) > 25)
      $this->_shortview = substr($this->text, 0, 23)."...";
    else
      $this->_shortview = $this->text;
    
    $this->_tel1 = substr($this->tel, 0, 2);
    $this->_tel2 = substr($this->tel, 2, 2);
    $this->_tel3 = substr($this->tel, 4, 2);
    $this->_tel4 = substr($this->tel, 6, 2);
    $this->_tel5 = substr($this->tel, 8, 2);
  }
  
  function updateDBFields() {
    if (($this->_tel1 != null) && ($this->_tel2 != null) && ($this->_tel3 != null) && ($this->_tel4 !== null) && ($this->_tel5 !== null)) {
      $this->tel = 
        $this->_tel1 .
        $this->_tel2 .
        $this->_tel3 .
        $this->_tel4 .
        $this->_tel5;
    }
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
    
    return CMbObject::canDelete( $msg, $oid, $tables );
  }

  // Backward References
  function loadRefsBack() {
  	$where = array(
      "group_id" => "= '$this->group_id'");
    $order = "type, text";
    $this->_ref_functions = new CFunctions;
    $this->_ref_functions = $this->_ref_functions->loadList($where, $order);
  }
  
  function canRead($withRefs = true) {
    $this->_canRead = isMbAllowed(PERM_READ, "dPetablissement", $this->group_id);
    return $this->_canRead;
  }

  function canEdit($withRefs = true) {
    $this->_canEdit = isMbAllowed(PERM_EDIT, "dPetablissement", $this->group_id);
    return $this->_canEdit;
  }
}
?>