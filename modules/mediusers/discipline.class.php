<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage mediusers
 *  @version $Revision: $
 *  @author Romain Ollivier
*/

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
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    $this->_props["text"] = "str|notNull";
    
    $this->_seek["text"] = "like";
  }
  
  function loadUsedDisciplines($where = array(), $order = null) {
    $ljoin["users_mediboard"] = "`users_mediboard`.`discipline_id` = `discipline`.`discipline_id`";
    $where["users_mediboard.discipline_id"] = "IS NOT NULL";
    if(!$order) {
      $order = "`discipline`.`text`";
    }
    return $this->loadList($where, $order, null, null, $ljoin);
  }
  
  function updateFormFields () {
    parent::updateFormFields();

    $this->_view = strtolower($this->text);
    if(strlen($this->_view) > 25)
      $this->_shortview = substr($this->_view, 0, 23)."...";
    else
      $this->_shortview = $this->_view;
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "utilisateurs", 
      "name"      => "users_mediboard", 
      "idfield"   => "user_id", 
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