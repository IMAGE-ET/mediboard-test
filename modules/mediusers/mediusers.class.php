<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage mediusers
 *	@version $Revision$
 *  @author Romain Ollivier
 */

global $utypes, $utypes_flip;

require_once($AppUI->getSystemClass("mbobject"));

require_once($AppUI->getModuleClass("admin"));
require_once($AppUI->getModuleClass("mediusers"    , "functions"));
require_once($AppUI->getModuleClass("mediusers"    , "discipline"));
require_once($AppUI->getModuleClass("dPcompteRendu", "pack"));
require_once($AppUI->getModuleClass("dPplanningOp" , "planning"));
require_once($AppUI->getModuleClass("dPplanningOp" , "protocole"));

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
	var $function_id   = null;
  var $discipline_id = null;

  // dotProject user fields
  var $_user_type       = null;
	var $_user_username   = null;
	var $_user_password   = null;
	var $_user_first_name = null;
	var $_user_last_name  = null;
	var $_user_email      = null;
	var $_user_phone      = null;
  var $_user_adresse    = null;
  var $_user_cp         = null;
  var $_user_ville      = null;

  // Other fields
  var $_view      = null;
  var $_shortview = null;

  // Object references
  var $_ref_function   = null;
  var $_ref_discipline = null;
  var $_ref_packs      = array();
  var $_ref_protocoles = array();

	function CMediusers() {
		$this->CMbObject( "users_mediboard", "user_id" );

    $this->_props["remote"] = "enum|0|1";
    $this->_props["adeli"] = "num|length|9|confidential";
    $this->_props["function_id"] = "ref|notNull";
    $this->_props["discipline_id"] = "ref";
    
    $this->_user_props["_user_username"] = "notNull|str|minLength|3";
    $this->_user_props["_user_first_name"] = "str";
    $this->_user_props["_user_last_name"] = "notNull|str|confidential";
    $this->_user_props["_user_email"] = "str|confidential";
    $this->_user_props["_user_phone"] = "num|length|10|confidential";
    $this->_user_props["_user_adresse"] = "str|confidential";
    $this->_user_props["_user_cp"] = "num|length|5|confidential";
    $this->_user_props["_user_ville"] = "str|confidential";
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
    $user->user_address1   = $this->_user_adresse   ;
    $user->user_zip        = $this->_user_cp        ;
    $user->user_city       = $this->_user_ville     ;

    return $user;
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "opération(s)", 
      "name"      => "operations", 
      "idfield"   => "operation_id", 
      "joinfield" => "chir_id"
    );

    $tables[] = array (
      "label"     => "acte(s) CCAM", 
      "name"      => "acte_ccam", 
      "idfield"   => "acte_id", 
      "joinfield" => "executant_id"
    );

    $tables[] = array (
      "label"     => "plage(s) de consultation", 
      "name"      => "plageconsult", 
      "idfield"   => "plageconsult_id", 
      "joinfield" => "chir_id"
    );

    $tables[] = array (
      "label"     => "plage(s) opératoire(s) (chirurgien)", 
      "name"      => "plagesop", 
      "idfield"   => "id", 
      "joinfield" => "chir_id"
    );

    $tables[] = array (
      "label"     => "plage(s) opératoire(s) (anesthésiste)", 
      "name"      => "plagesop", 
      "idfield"   => "id", 
      "joinfield" => "anesth_id"
    );

    $tables[] = array (
      "label"     => "Pack(s) de documents", 
      "name"      => "pack", 
      "idfield"   => "pack_id", 
      "joinfield" => "chir_id"
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
      $this->_user_username   = $user->user_username;
      $this->_user_password   = $user->user_password;
      $this->_user_first_name = ucwords(strtolower($user->user_first_name));
      $this->_user_last_name  = strtoupper($user->user_last_name) ;
      $this->_user_email      = $user->user_email;
      $this->_user_phone      = $user->user_phone;
      $this->_user_adresse    = $user->user_address1;
      $this->_user_cp         = $user->user_zip;
      $this->_user_ville      = $user->user_city;
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
    $this->_ref_discipline = new CDiscipline;
    $this->_ref_discipline->load($this->discipline_id);
  }

  function loadRefsBack() {
    $where = array(
      "chir_id" => "= '$this->user_id'");
    $this->_ref_packs = new CPack;
    $this->_ref_packs = $this->_ref_packs->loadList($where);
  }
  
  function loadProtocoles() {
    $where["chir_id"] = "= '$this->user_id'";
    $order = "codes_ccam";
    $protocoles = new CProtocole;
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
      $sql = "UPDATE `users_mediboard` SET";
      if($this->function_id !== null)
        $sql .= "\n`function_id` = '$this->function_id',";
      if($this->function_id !== null)
        $sql .= "\n`discipline_id` = '$this->discipline_id',";
      if($this->remote !== null)
        $sql .= "\n`remote` = '$this->remote',";
      if($this->adeli !== null)
        $sql .= "\n`adeli` = '$this->adeli',";
      $sql .= "\n`user_id` = '$this->user_id'" .
              "\nWHERE `user_id` = '$this->user_id'";
    } else {
      $this->user_id = $dPuser->user_id;
      $sql = "INSERT INTO `users_mediboard`" .
          "( `user_id` , `function_id`, `discipline_id` ,  `remote`, `adeli`)" .
          "VALUES ('$this->user_id', '$this->function_id', '$this->discipline_id' , '$this->remote', '$this->adeli')";
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
    $perm->permission_grant_on = "mediusers";
    $perm->permission_item = $this->function_id;
    $perm->store();
  }
  
  function insGroupPermission() {
    $perm = new CPermission;
    $perm->permission_user = $this->user_id; 
    $perm->permission_grant_on = "dPetablissement";
    $function = new CFunctions;
    $function->load($this->function_id);
    $perm->permission_item = $function->group_id;
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
  
  function loadEtablissement($perm_type = null){
  	// Liste de Tous les établissements
    $sql = "SELECT *" .
      "\nFROM groups_mediboard" .
      "\nORDER BY text";
    $basegroups = db_loadObjectList($sql, new CGroups);  
    
    $groups = array();
    // Filtre
    if ($perm_type) {
      foreach($basegroups as $keyGroupe=>$Groupe){
        if(isMbAllowed($perm_type, "dPetablissement", $Groupe->group_id)){
          $groups[$keyGroupe] = $basegroups[$keyGroupe];
        }
      }
    }else{
      $groups = $basegroups;    
    }
    return $groups;
  }
  
  function loadUsers($perm_type = null, $function_id = null, $name = null) {
    return $this->loadListFromType(null, $perm_type, $function_id, $name);
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