<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage mediusers
 *  @version $Revision: $
 *  @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("mbobject"));

require_once($AppUI->getModuleClass("mediusers", "mediusers"));

/**
 * The CDiscipline Class
 */
class CDiscipline extends CMbObject {
  // DB Table key
  var $discipline_id = NULL;

  // DB Fields
  var $text = NULL;

  // Object References
  var $_ref_users = null;

  function CDiscipline() {
    $this->CMbObject("discipline", "discipline_id");
    $this->_props["text"] = "str|notNull";
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
      "label" => "utilisateurs", 
      "name" => "users_mediboard", 
      "idfield" => "user_id", 
      "joinfield" => "discipline_id"
    );
    
    return parent::canDelete( $msg, $oid, $tables );
  }
  
  // Backward references
  function loadRefsBack() {
    $where = array(
      "discipline_id" => "= '$this->discipline_id'");
    $this->_ref_users = new CMediusers;
    $this->_ref_users = $this->_ref_users->loadList($where);
  }
}

?>