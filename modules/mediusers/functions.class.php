<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage mediusers
 *	@version $Revision$
 *  @author Romain Ollivier
*/

/**
 * The CFunction Class
 */
class CFunctions extends CMbObject {
  // DB Table key
	var $function_id = null;

  // DB References
  var $group_id = null;

  // DB Fields
  var $type  = null;
	var $text  = null;
	var $color = null;
  
  // Object References
  var $_ref_group = null;
  var $_ref_users = null;

	function CFunctions() {
		$this->CMbObject("functions_mediboard", "function_id");
    
    $this->_props["group_id"] = "ref|notNull";
    $this->_props["type"]     = "enum|administratif|cabinet|notNull";
    $this->_props["text"]     = "str|notNull|confidential";
    $this->_props["color"]    = "str|length|6|notNull";
    
    $this->_seek["text"] = "like";
    
    $this->buildEnums();
	}
  
  function updateFormFields() {
		parent::updateFormFields();

    $this->_view = $this->text;
    if(strlen($this->text) > 25)
      $this->_shortview = substr($this->text, 0, 23)."...";
    else
      $this->_shortview = $this->text;
	}
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "utilisateurs", 
      "name"      => "users_mediboard", 
      "idfield"   => "user_id", 
      "joinfield" => "function_id"
    );
    
    $tables[] = array (
      "label"     => "plages opératoires", 
      "name"      => "plagesop", 
      "idfield"   => "plageop_id", 
      "joinfield" => "spec_id"
    );
    
    return parent::canDelete( $msg, $oid, $tables );
  }

  // Forward references
  function loadRefsFwd() {
    $this->_ref_group = new CGroups();
    $this->_ref_group->load($this->group_id);
  }
  
  // Backward references
  function loadRefsBack() {
    $where = array(
      "function_id" => "= '$this->function_id'");
    $this->_ref_users = new CMediusers;
    $this->_ref_users = $this->_ref_users->loadList($where);
  }
  
  function canRead($withRefs = true) {
    $this->_canRead = isMbAllowed(PERM_READ, "mediusers", $this->function_id);
    return $this->_canRead;
  }

  function canEdit($withRefs = true) {
    $this->_canEdit = isMbAllowed(PERM_EDIT, "mediusers", $this->function_id);
    return $this->_canEdit;
  }
  
  // @todo : ameliorer le choix des spécialités
  // (loadfunction($groupe, $permtype) par exemple)
  function loadSpecialites($perm_type = null) {
    $sql = "SELECT `$this->_tbl`.*" .
      "\nFROM `$this->_tbl`" .
      "\nWHERE `$this->_tbl`.`type` = 'cabinet'" .
      "\nORDER BY `$this->_tbl`.`text`";
  
    $basespecs = db_loadObjectList($sql, $this);
    $specs = null;
  
    // Filter with permissions
    if ($perm_type) {
      foreach ($basespecs as $spec) {
        if (isMbAllowed($perm_type, "mediusers", $spec->function_id)) {
          $specs[] = $spec;
        }          
      }
    } else {
      $specs = $basespecs;
    }
    return $specs;
  }
}
?>