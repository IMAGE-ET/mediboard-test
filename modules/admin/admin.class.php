<?php /* ADMIN $Id: admin.class.php,v 1.9 2006/04/21 14:42:46 rhum1 Exp $ */

// user types
$utypes = array(
// DEFAULT USER (nothing special)
  0 => '',
// DO NOT CHANGE ADMINISTRATOR INDEX !
  1 => 'Administrator',
// you can modify the terms below to suit your organisation
  2 => 'Hotesse',
  3 => 'Chirurgien',
  4 => 'Anesthésiste',
  5 => 'Directeur',
  6 => 'Comptable',
  7 => 'Infirmière',
  8 => 'PMSI',
  9 => 'Qualite',
  10 => 'Secrétaire',
  12 => 'Surveillante de bloc',
  13 => 'Médecin'
);

##
##  NOTE: the user_type field in the users table must be changed to a TINYINT
##

/**
* User Class
*/

require_once($AppUI->getSystemClass("dp"));

class CUser extends CDpObject {
	var $user_id = null;
	var $user_username = null;
	var $user_password = null;
	var $user_parent = null;
	var $user_type = null;
	var $user_first_name = null;
	var $user_last_name = null;
	var $user_company = null;
	var $user_department = null;
	var $user_email = null;
	var $user_phone = null;
	var $user_home_phone = null;
	var $user_mobile = null;
	var $user_address1 = null;
	var $user_address2 = null;
	var $user_city = null;
	var $user_state = null;
	var $user_zip = null;
	var $user_country = null;
	var $user_icq = null;
	var $user_aol = null;
	var $user_birthday = null;
	var $user_pic = null;
	var $user_owner = null;
	var $user_signature = null;

	function CUser() {
		$this->CDpObject( 'users', 'user_id' );
	}

	function check() {
		if ($this->user_id === null) {
			return 'user id is null';
		}
		if ($this->user_password !== null) {
			$this->user_password = db_escape( trim( $this->user_password ) );
		}
		// TODO MORE
		return null; // object is ok
	}

	function store() {
		$msg = $this->check();
		if( $msg ) {
			return get_class( $this )."::store-check failed";
		}
		if( $this->user_id ) {
		// save the old password
			$sql = "SELECT user_password FROM users WHERE user_id = $this->user_id";
			db_loadHash( $sql, $hash );
			$pwd = $hash['user_password'];	// this will already be encrypted

			$ret = db_updateObject( 'users', $this, 'user_id', false );

		// update password if there has been a change
			$sql = "UPDATE users SET user_password = MD5('$this->user_password')"
				."\nWHERE user_id = $this->user_id AND user_password != '$pwd'";
			db_exec( $sql );
		} else {
			$ret = db_insertObject( 'users', $this, 'user_id' );
		// encrypt password
			$sql = "UPDATE users SET user_password = MD5('$this->user_password')"
				."\nWHERE user_id = $this->user_id";
			db_exec( $sql );
		}
		if( !$ret ) {
			return get_class( $this )."::store failed <br />" . db_error();
		} else {
			return null;
		}
	}
  
  function updateFormFields () {
    parent::updateFormFields();

    $this->user_last_name = strtoupper($this->user_last_name);
    $this->user_first_name = ucwords(strtolower($this->user_first_name));
    $this->_view = "$this->user_last_name $this->user_first_name";
  }
  
  /**
	 * @return string error message when necessary, null otherwise
	 */
  function copyPermissionsFrom($user_id, $delExistingPerms = false) {
    if (!$user_id) {
			return null;
		}    
 
    // Copy user type
    $profile = new CUser();
    $profile->load($user_id);
    $this->user_type = $profile->user_type;
    if ($msg = $this->store()) {
      return $msg;
    }
        
    // Delete existing permissions
    if ($delExistingPerms) {
      if (!db_delete( 'permissions', 'permission_user', $this->user_id )) {
        return "Can't delete permissions";
      }
		}    

    // Get other user's permissions 
    $perms = new CPermission;
    $perms = $perms->loadList("permission_user = $user_id");

    // Copy them
    foreach($perms as $perm) {
      $perm->permission_id = null;
      $perm->permission_user = $this->user_id;
      $perm->store();
    }
    
    return null;
  }
}

/**
* Permissions class
*/

// Permission flags used in the DB
define( 'PERM_EDIT', '-1' );
define( 'PERM_DENY', '0' );
define( 'PERM_READ', '1' );

define( 'PERM_ALL', '-1' );

// Flags for Mediboard modules 
define ('HIDDEN_READNONE_EDITNONE' ,  0); // PERM_DENY in dotProject
define ('HIDDEN_READNONE_EDITALL'  ,  2);
define ('HIDDEN_READALL_EDITNONE'  ,  3);
define ('HIDDEN_READALL_EDITALL'   ,  4);
define ('VISIBLE_READNONE_EDITNONE',  5);
define ('VISIBLE_READNONE_EDITALL' ,  6);
define ('VISIBLE_READALL_EDITNONE' ,  1); // PERM_READ in dotProject
define ('VISIBLE_READALL_EDITALL'  , -1); // PERM_EDIT 

// Access
$module_permission_matrix = 
  array( // _module_visible
    false => array ( // _module_readall
      false => array ( // _module_editall
        false => HIDDEN_READNONE_EDITNONE,
        true  => HIDDEN_READNONE_EDITALL
      ),
      true => array ( // _module_editall
        false => HIDDEN_READALL_EDITNONE,
        true  => HIDDEN_READALL_EDITALL
      )
    ),
    true => array ( // _module_readall
      false => array ( // _module_editall
        false => VISIBLE_READNONE_EDITNONE,
        true  => VISIBLE_READNONE_EDITALL
      ),
      true => array ( // _module_editall
        false => VISIBLE_READALL_EDITNONE,
        true  => VISIBLE_READALL_EDITALL
      )
    )
  );

class CPermission extends CDpObject {
  // DB Table key
	var $permission_id = null;

  // DB Fields
	var $permission_user = null;
	var $permission_grant_on = "all";
	var $permission_item = PERM_ALL;
	var $permission_value = PERM_EDIT;

  // DB Form Fields
  var $_module_visible = null;
  var $_module_editall = null;
  var $_module_readall = null;
  var $_item_deny = null;
  var $_item_read = null;
  var $_item_edit = null;
  
	function CPermission() {
		$this->CDpObject( 'permissions', 'permission_id' );
	}
  
  function updateFormFields() {
    if ($this->permission_item == PERM_ALL) {
      switch ($this->permission_value) {
        case HIDDEN_READNONE_EDITNONE : $this->_module_visible = false; $this->_module_readall = false; $this->_module_editall = false; break;
        case HIDDEN_READNONE_EDITALL  : $this->_module_visible = false; $this->_module_readall = false; $this->_module_editall = true ; break;
        case HIDDEN_READALL_EDITNONE  : $this->_module_visible = false; $this->_module_readall = true ; $this->_module_editall = false; break;
        case HIDDEN_READALL_EDITALL   : $this->_module_visible = false; $this->_module_readall = true ; $this->_module_editall = true ; break;
        case VISIBLE_READNONE_EDITNONE: $this->_module_visible = true ; $this->_module_readall = false; $this->_module_editall = false; break;
        case VISIBLE_READNONE_EDITALL : $this->_module_visible = true ; $this->_module_readall = false; $this->_module_editall = true ; break;
        case VISIBLE_READALL_EDITNONE : $this->_module_visible = true ; $this->_module_readall = true ; $this->_module_editall = false; break;
        case VISIBLE_READALL_EDITALL  : $this->_module_visible = true ; $this->_module_readall = true ; $this->_module_editall = true ; break;
      }

      $this->_item_deny = null;
      $this->_item_read = null;
      $this->_item_edit = null;
		} else {
      $this->_item_deny = $this->permission_value == PERM_DENY;
      $this->_item_read = $this->permission_value == PERM_READ;
      $this->_item_edit = $this->permission_value == PERM_EDIT;
            
      $this->_module_visible = null; 
      $this->_module_readall = null; 
      $this->_module_editall = null;
    return;
    }
  }
  
  function updateDBFields() {
    if ($this->permission_item != PERM_ALL) {
      return;
    }
 
    $this->_module_visible = $this->_module_visible ? 1 : 0; 
    $this->_module_readall = $this->_module_readall ? 1 : 0;
    $this->_module_editall = $this->_module_editall ? 1 : 0;
    
    global $module_permission_matrix;
       
    $this->permission_value = $module_permission_matrix[$this->_module_visible][$this->_module_readall][$this->_module_editall];
  }
}

?>