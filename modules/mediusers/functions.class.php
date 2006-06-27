<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage mediusers
 *	@version $Revision$
 *  @author Romain Ollivier
*/

require_once($AppUI->getSystemClass('mbobject'));

require_once($AppUI->getModuleClass('mediusers', 'groups'));
require_once($AppUI->getModuleClass('mediusers', 'mediusers'));

/**
 * The CFunction Class
 */
class CFunctions extends CMbObject {
  // DB Table key
	var $function_id = NULL;

  // DB Fields
	var $text = NULL;
	var $color = NULL;

  // DB References
	var $group_id = NULL;
  
  // Object References
  var $_ref_group = null;
  var $_ref_users = null;

	function CFunctions() {
		$this->CMbObject('functions_mediboard', 'function_id');
    
    $this->_props["text"] = "str|notNull|confidential";
    $this->_props["color"] = "str|length|6|notNull";
    $this->_props["group_id"] = "ref|notNull";
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
      'label' => 'utilisateurs', 
      'name' => 'users_mediboard', 
      'idfield' => 'user_id', 
      'joinfield' => 'function_id'
    );
    
    $tables[] = array (
      'label' => 'plages opératoires', 
      'name' => 'plagesop', 
      'idfield' => 'id', 
      'joinfield' => 'id_spec'
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
  
  // @todo : ameliorer le choix des spécialités
  // (loadfunction($groupe, $permtype) par exemple)
  function loadSpecialites ($perm_type = null) {
    $sql = "SELECT $this->_tbl.*" .
      "\nFROM $this->_tbl, groups_mediboard" .
      "\nWHERE $this->_tbl.group_id = groups_mediboard.group_id" .
      "\nAND groups_mediboard.text IN ('Cabinets')" .
      "\nORDER BY $this->_tbl.text";
  
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