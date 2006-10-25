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
    
    $this->loadRefModule(basename(dirname(__FILE__)));
	}

  function getSpecs() {
    return array (
      "group_id" => "ref|notNull",
      "type"     => "enum|administratif|cabinet|notNull",
      "text"     => "str|notNull|confidential",
      "color"    => "str|length|6|notNull"
    );
  }
  
  function getSeeks() {
    return array (
      "text" => "like"
    );
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
    $where = array();
    $where["function_id"] = "= '$this->function_id'";
    $ljoin["users"] = "`users`.`user_id` = `users_mediboard`.`user_id`";
    $order = "`users`.`user_last_name`, `users`.`user_first_name`";
    $this->_ref_users = new CMediusers;
    $this->_ref_users = $this->_ref_users->loadList($where, $order, null, null, $ljoin);
  }
  
  // @todo : ameliorer le choix des spécialités
  // (loadfunction($groupe, $permtype) par exemple)
  function loadSpecialites($perm_type = null) {
    global $g;
    $where = array();
    $where["type"] = "= 'cabinet'";
    $where["group_id"] = "= '$g'";
    $order = "text";
    $specs = $this->loadList($where, $order);
  
    // Filter with permissions
    if ($perm_type) {
      foreach ($specs as $keySpec => $spec) {
        if (!$spec->canRead()) {
          unset($specs[$keySpec]);
        }          
      }
    }
    
    return $specs;
  }
}
?>