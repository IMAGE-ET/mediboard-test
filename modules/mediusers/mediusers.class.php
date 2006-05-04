<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage mediusers
 *	@version $Revision$
 *  @author Romain Ollivier
 */

global $utypes, $utypes_flip;

require_once($AppUI->getSystemClass('mbobject'));

require_once($AppUI->getModuleClass('admin'));
require_once($AppUI->getModuleClass('mediusers', "functions"));
require_once($AppUI->getModuleClass('dPcompteRendu', "pack"));
require_once($AppUI->getModuleClass('dPplanningOp', "planning"));

$utypes_flip = array_flip($utypes);

/**
 * The CMediusers class
 */
class CMediusers extends CMbObject {
  // DB Table key
	var $user_id = null;

  // DB Fields
  var $remote = null;
  var $adeli  = null;

  // DB References
	var $function_id = null;

  // dotProject user fields
  var $_user_type       = null;
	var $_user_username   = null;
	var $_user_password   = null;
	var $_user_first_name = null;
	var $_user_last_name  = null;
	var $_user_email      = null;
	var $_user_phone      = null;

  // Other fields
  var $_view = null;
  var $_shortview = null;

  // Object references
  var $_ref_function = null;
  var $_ref_packs = array();
  var $_ref_protocoles = array();

	function CMediusers() {
		$this->CMbObject( 'users_mediboard', 'user_id' );

    $this->_props["remote"] = "enum|0|1";
    $this->_props["adeli"] = "num|length|9|confidential";
    $this->_props["function_id"] = "ref|notNull";
    
    $this->_user_props["_user_username"] = "notNull|str|minLength|3|confidential";
    $this->_user_props["_user_first_name"] = "str|confidential";
    $this->_user_props["_user_last_name"] = "notNull|str|confidential";
    $this->_user_props["_user_email"] = "str|confidential";
    $this->_user_props["_user_phone"] = "num|length|10|confidential";
	}

  function createUser() {
    $user = new CUser();
    $user->user_id = $this->user_id;
    
    $user->user_type       = $this->_user_type      ;
    $user->user_username   = $this->_user_username  ;
    $user->user_password   = $this->_user_password  ;
    $user->user_first_name = $this->_user_first_name;
    $user->user_last_name  = $this->_user_last_name ;
    $user->user_email      = $this->_user_email     ;
    $user->user_phone      = $this->_user_phone     ;

    return $user;
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      'label' => 'opération(s) ', 
      'name' => 'operations', 
      'idfield' => 'operation_id', 
      'joinfield' => 'chir_id'
    );

    $tables[] = array (
      'label' => 'acte(s) CCAM', 
      'name' => 'acte_ccam', 
      'idfield' => 'acte_id', 
      'joinfield' => 'executant_id'
    );

    $tables[] = array (
      'label' => 'plage(s) de consultation', 
      'name' => 'plageconsult', 
      'idfield' => 'plageconsult_id', 
      'joinfield' => 'chir_id'
    );

    $tables[] = array (
      'label' => 'plage(s) opératoire(s) (chirurgien) ', 
      'name' => 'plagesop', 
      'idfield' => 'id', 
      'joinfield' => 'chir_id'
    );

    $tables[] = array (
      'label' => 'plage(s) opératoire(s) (anesthésiste) ', 
      'name' => 'plagesop', 
      'idfield' => 'id', 
      'joinfield' => 'anesth_id'
    );

    $tables[] = array (
      'label' => 'Pack(s) de documents', 
      'name' => 'pack', 
      'idfield' => 'pack_id', 
      'joinfield' => 'chir_id'
    );

    return parent::canDelete($msg, $oid, $tables);
  }
  
	function delete() {
    // @todo delete Favoris CCAM et CIM en cascade
    
    // Delete corresponding dP user first
    if ($this->canDelete($msg)) {
      $dPuser = $this->createUser();
      if ($msg = $dPuser->delete()) {
        return $msg;
      }
    }

    return parent::delete();
	}

  function updateFormFields() {
    global $utypes;
    $user = new CUser();
    if ($user->load($this->user_id)) {
      $this->_user_type       = $utypes[$user->user_type];
      $this->_user_username   = $user->user_username  ;
      $this->_user_password   = $user->user_password  ;
      $this->_user_first_name = ucwords(strtolower($user->user_first_name));
      $this->_user_last_name  = strtoupper($user->user_last_name) ;
      $this->_user_email      = $user->user_email     ;
      $this->_user_phone      = $user->user_phone     ;
      // Encrypt this datas
      $this->checkConfidential($this->_user_props);
      $this->_view            = $this->_user_last_name." ".$this->_user_first_name;
      $this->_shortview       = "";
      $arrayLastName = explode(" ", $this->_user_last_name);
      $arrayFirstName = explode("-", $this->_user_first_name);
      foreach($arrayFirstName as $key => $value) {
      	if($value != '')
      	  $this->_shortview .=  strtoupper($value[0]);
      }
      foreach($arrayLastName as $key => $value) {
      	if($value != '')
      	  $this->_shortview .=  strtoupper($value[0]);
      }
    }
  }

  function loadRefsFwd() {
    // Forward references
    $this->_ref_function = new CFunctions;
    $this->_ref_function->load($this->function_id);
  }

  function loadRefsBack() {
    $where = array(
      "chir_id" => "= '$this->user_id'");
    $this->_ref_packs = new CPack;
    $this->_ref_packs = $this->_ref_packs->loadList($where);
  }
  
  function loadProtocoles() {
    $where["chir_id"] = "= '$this->user_id'";
    $where["pat_id"] = "= 0";
    $where["plageop_id"] = "iS NULL";
    $order = "codes_ccam";
    $protocoles = new COperation;
    $this->_ref_protocoles = $protocoles->loadList($where, $order);
  }
  
  
  function fillTemplate(&$template) {
  	$this->loadRefsFwd();
    $template->addProperty("Praticien - nom"       , $this->_user_last_name );
    $template->addProperty("Praticien - prénom"    , $this->_user_first_name);
    $template->addProperty("Praticien - spécialité", $this->_ref_function->text);
  }
  
	function store() {
    global $AppUI;
    if ($msg = $this->check()) {
      return $AppUI->_(get_class( $this )) . 
        $AppUI->_("::store-check failed:") .
        $AppUI->_($msg);
    }
    
    // Store corresponding dP user first
    $dPuser = $this->createUser();
    if ($msg = $dPuser->store()) {
      return $msg;
    }

    // Can't use parent::store cuz user_id don't auto-increment
    // SQL coded instead
    if ($this->user_id) {
      $sql = "UPDATE `users_mediboard`" .
          "\nSET `function_id` = '$this->function_id'," .
          "\n`remote` = '$this->remote'," .
          "\n`adeli` = '$this->adeli'" .
          "\nWHERE `user_id` = '$this->user_id'";
    } else {
      $this->user_id = $dPuser->user_id;
      $sql = "INSERT INTO `users_mediboard`" .
          "( `user_id` , `function_id`,  `remote`, `adeli`)" .
          "VALUES ('$this->user_id', '$this->function_id', '$this->remote', '$this->adeli')";
    }

    db_exec($sql);
    return db_error();
  }
  
  function delFunctionPermission() {
    $where = array();
    $where["permission_user"    ] = "= '$this->user_id'";
    $where["permission_grant_on"] = "= 'mediusers'";
    $where["permission_item"    ] = "= '$this->function_id'";
    
    $perm = new CPermission;
    if ($perm->loadObject($where)) {
      $perm->delete();
    }
  }
  
  function insFunctionPermission() {
    $perm = new CPermission;
    $perm->permission_user = $this->user_id; 
    $perm->permission_grant_on = 'mediusers';
    $perm->permission_item = $this->function_id;
    $perm->store();
  }

  function loadListFromType($user_types = null, $perm_type = null, $function_id = null, $name = null) {
    global $utypes_flip;
    $sql = "SELECT *" .
      "\nFROM users, users_mediboard" .
      "\nWHERE users.user_id = users_mediboard.user_id";
      
    if ($function_id) {
      $sql .= "\nAND users_mediboard.function_id = $function_id";
    }
    
    if ($name) {
      $sql .= "\nAND users.user_last_name LIKE '$name%'";
    }
    
    if (is_array($user_types)) {
      foreach ($user_types as $key => $value) {
        $value = $utypes_flip[$value];
        $user_types[$key] = "'$value'";
      }
      
      $inClause = implode(", ", $user_types);
      $sql .= "\nAND users.user_type IN ($inClause)";
    }

    $sql .= "\nORDER BY users.user_last_name";

    // Get all users
    $baseusers = db_loadObjectList($sql, new CUser);
    $mediusers = db_loadObjectList($sql, new CMediusers);
   
    $users = array();
     
    // Filter with permissions
    if ($perm_type) {
      foreach ($mediusers as $key => $mediuser) {
        if (isMbAllowed($perm_type, "mediusers", $mediuser->function_id)) {
          $users[$key] = $mediusers[$key];
        }          
      }
    } else {
      $users = $baseusers;
    }
    
    return $users;
    
  }

  function loadChirurgiens($perm_type = null, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Chirurgien"), $perm_type, $function_id, $name);
  }
  
  function loadAnesthesistes($perm_type = null, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Anesthésiste"), $perm_type, $function_id, $name);
  }
  
  function loadPraticiens($perm_type = null, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Chirurgien", "Anesthésiste"), $perm_type, $function_id, $name);
  }
  
  function isAllowed($perm_type = PERM_READ) {
    assert($this->function_id);
    return isMbAllowed($perm_type, "mediusers", $this->function_id);
  }
  
  function isFromType($user_types) {
    // Warning: !== operator
    return array_search($this->_user_type, $user_types) !== false; 
  }
  
  function isPraticien () {
		return $this->isFromType(array("Chirurgien", "Anesthésiste"));
	}
}

?>