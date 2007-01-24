<?php /* ADMIN $Id$ */

// user types
global $utypes;
$utypes = array(
// DEFAULT USER (nothing special)
  0  => "-- Choisir un type",
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
  var $user_id          = null;
  var $user_username    = null;
  var $user_password    = null;
  var $user_type        = null;
  var $user_first_name  = null;
  var $user_last_name   = null;
  var $user_email       = null;
  var $user_phone       = null;
  var $user_mobile      = null;
  var $user_address1    = null;
  var $user_city        = null;
  var $user_zip         = null;
  var $user_country     = null;
  var $user_birthday    = null;
  var $user_pic         = null;
  var $user_signature   = null;

  var $_user_password    = null;

  var $_ref_preferences = null;
  
  function CUser() {
    $this->CMbObject("users", "user_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "user_username"   => "str|maxLength|20|notNull",
      "user_password"   => "str|minLength|4",
      "user_type"       => "num|max|20|notNull",
      "user_first_name" => "str|maxLength|50",
      "user_last_name"  => "str|maxLength|50|notNull",
      "user_email"      => "str|maxLength|255",
      "user_phone"      => "str|maxLength|30",
      "user_mobile"     => "str|maxLength|30",
      "user_address1"   => "str|maxLength|50",
      "user_city"       => "str|maxLength|30",
      "user_zip"        => "str|maxLength|11",
      "user_country"    => "str|maxLength|30",
      "user_birthday"   => "dateTime",
      "user_pic"        => "text",
      "user_signature"  => "text"
    );
  }
  
  function getSeeks() {
    return array (
      "user_last_name"  => "likeBegin",
      "user_first_name" => "likeBegin"
    );
  }

  function updateDBFields() {
    parent::updateDBFields();
    
    // Nullify no to empty in database
    $this->user_password = $this->_user_password ? md5($this->_user_password) : $this->user_password;
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
      if (!db_delete("perm_module", "user_id", $this->user_id)) {
        return "Can't delete modules permissions";
      }
      if (!db_delete("perm_object", "user_id", $this->user_id)) {
        return "Can't delete objects permissions";
      }
    }    

    // Get other user's permissions

    // Module permissions
    $perms = new CPermModule;
    $perms = $perms->loadList("user_id = '$user_id'");

    // Copy them
    foreach($perms as $perm) {
      $perm->perm_module_id = null;
      $perm->user_id = $this->user_id;
      $perm->store();
    }

    //Object permissions
    $perms = new CPermObject;
    $perms = $perms->loadList("user_id = '$user_id'");

    // Copy them
    foreach($perms as $perm) {
      $perm->perm_object_id = null;
      $perm->user_id = $this->user_id;
      $perm->store();
    }
 
    return null;
  }
}

?>