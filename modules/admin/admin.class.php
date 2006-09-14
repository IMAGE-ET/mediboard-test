<?php /* ADMIN $Id$ */

// user types
$utypes = array(
// DEFAULT USER (nothing special)
  0  => "",
// DO NOT CHANGE ADMINISTRATOR INDEX !
  1  => "Administrator",
// you can modify the terms below to suit your organisation
  2  => "Hotesse",
  3  => "Chirurgien",
  4  => "Anesthésiste",
  5  => "Directeur",
  6  => "Comptable",
  7  => "Infirmière",
  8  => "PMSI",
  9  => "Qualite",
  10 => "Secrétaire",
  12 => "Surveillante de bloc",
  13 => "Médecin"
);

##
##  NOTE: the user_type field in the users table must be changed to a TINYINT
##

/**
* User Class
*/

class CUser extends CMbObject {
	var $user_id         = null;
	var $user_username   = null;
	var $user_password   = null;
	var $user_parent     = null;
	var $user_type       = null;
	var $user_first_name = null;
	var $user_last_name  = null;
	var $user_company    = null;
	var $user_department = null;
	var $user_email      = null;
	var $user_phone      = null;
	var $user_home_phone = null;
	var $user_mobile     = null;
	var $user_address1   = null;
	var $user_address2   = null;
	var $user_city       = null;
	var $user_state      = null;
	var $user_zip        = null;
	var $user_country    = null;
	var $user_icq        = null;
	var $user_aol        = null;
	var $user_birthday   = null;
	var $user_pic        = null;
	var $user_owner      = null;
	var $user_signature  = null;

	function CUser() {
		$this->CMbObject("users", "user_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
    
    $this->_seek = array();
    $this->_seek["user_last_name"]  = "likeBegin";
    $this->_seek["user_first_name"] = "likeBegin";
	}

	function check() {
		if ($this->user_id === null) {
			return "user id is null";
		}
		if ($this->user_password !== null) {
			$this->user_password = db_escape(trim($this->user_password));
		}
		return null;
	}

	function store() {
		$msg = $this->check();
		if($msg) {
			return get_class($this)."::store-check failed";
		}
		if( $this->user_id ) {
		// save the old password
			$sql = "SELECT user_password FROM users WHERE user_id = $this->user_id";
      $hash = null;
			db_loadHash($sql, $hash);
			$pwd = $hash["user_password"];	// this will already be encrypted

			$ret = db_updateObject("users", $this, "user_id", false);

		// update password if there has been a change
			$sql = "UPDATE users SET user_password = MD5('$this->user_password')"
				."\nWHERE user_id = $this->user_id AND user_password != '$pwd'";
			db_exec($sql);
		} else {
			$ret = db_insertObject("users", $this, "user_id");
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
      if (!db_delete("permissions", "permission_user", $this->user_id)) {
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

?>