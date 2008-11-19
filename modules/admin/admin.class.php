<?php /* ADMIN $Id$ */

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
  var $user_last_login  = null;
  var $user_login_errors= null;
  var $template         = null;
  var $profile_id       = null;

  var $_user_password   = null;
  var $_login_locked    = null;

  var $_ref_preferences = null;
  
  static $types = array(
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
    13 => "Médecin",
    14 => "Personnel"
  );
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'users';
    $spec->key   = 'user_id';
    return $spec;
  }

  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["favoris_CCAM"]       = "CFavoriCCAM favoris_user";
    $backRefs["favoris_CIM10"]      = "CFavoricim10 favoris_user";
    $backRefs["permissions_module"] = "CPermModule user_id";
    $backRefs["permissions_objet"]  = "CPermObject user_id";
    $backRefs["logs"]               = "CUserLog user_id";
    $backRefs["profiled_users"]     = "CUser profile_id";
    return $backRefs;
  }

  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["user_username"]   = "notNull str maxLength|20";
    $specs["user_password"]   = "str";
    $specs["user_type"]       = "notNull num minMax|0|20";
    $specs["user_first_name"] = "str maxLength|50";
    $specs["user_last_name"]  = "notNull str maxLength|50 confidential";
    $specs["user_email"]      = "str maxLength|255";
    $specs["user_phone"]      = "str maxLength|30 mask|99S99S99S99S99";
    $specs["user_mobile"]     = "str maxLength|30 mask|99S99S99S99S99";
    $specs["user_address1"]   = "str maxLength|50";
    $specs["user_city"]       = "str maxLength|30";
    $specs["user_zip"]        = "str maxLength|11";
    $specs["user_country"]    = "str maxLength|30";
    $specs["user_birthday"]   = "dateTime";
    $specs["user_pic"]        = "text";
    $specs["user_signature"]  = "text";
    $specs["user_last_login"] = "dateTime";
    $specs["user_login_errors"]= "num";
    $specs["template"]        = "bool notNull default|0";
    $specs["profile_id"]      = "ref class|CUser";

    // The different levels of security are stored to be usable in JS
    $specs["_user_password_weak"]   = "password minLength|4";
    $specs["_user_password_strong"] = "password minLength|6 notContaining|user_username notNear|user_username alphaAndNum";

    if(CAppUI::conf("admin CUser strong_password")) {
      $specs["_user_password"] = $specs["_user_password_strong"];
    } else {
      $specs["_user_password"] = $specs["_user_password_weak"];
    }

    return $specs;
  }
  
  /** Update the object's specs */
  function updateSpecs() {
    $oldSpec = $this->_specs['_user_password'];

    $user = new CMediusers();
    $remote = 0;
    
    if ($user->isInstalled()) {
	    if ($result = $user->load($this->user_id)) {
	    	 $remote = $user->remote;
	    }
    }
    
    $strongPassword = ((CAppUI::conf("admin CUser strong_password") == "1") && ($remote == 0));
    
    // If the global strong password config is set to TRUE and the user can connect remotely
    $this->_specs['_user_password'] = $strongPassword?
      $this->_specs['_user_password_strong']:
      $this->_specs['_user_password_weak'];
    
    $this->_specs['_user_password']->fieldName = $oldSpec->fieldName;
    
    $this->_props['_user_password'] = $strongPassword?
      $this->_props['_user_password_strong']:
      $this->_props['_user_password_weak'];
  }

  function getSeeks() {
    return array (
      "user_last_name"  => "likeBegin",
      "user_first_name" => "likeBegin"
      );
  }

  /**
   * Return true if user login count system is ready
   * @return bool
   */
  function loginErrorsReady() {
    return $this->_spec->ds->loadField($this->_spec->table, "user_login_errors");
  }
  
  function updateDBFields() {
    parent::updateDBFields();

    // Nullify no to empty in database
    $this->user_password = $this->_user_password ? md5($this->_user_password) : null;
  }

  function updateFormFields () {
    parent::updateFormFields();
    $this->user_last_name  =         mb_strtoupper($this->user_last_name);
    $this->user_first_name = ucwords(mb_strtolower($this->user_first_name));
    $this->_view = "$this->user_last_name $this->user_first_name";
    $this->_login_locked = $this->user_login_errors >= CAppUI::conf('admin CUser max_login_attempts');
  }

  function check() {
    // Chargement des specs des attributs du mediuser
    $this->updateSpecs();
    
    $specsObj = $this->getSpecsObj();

    // On se concentre dur le mot de passe (_user_password)
    $pwdSpecs = $specsObj['_user_password'];

    $pwd = $this->_user_password;

    // S'il a été défini, on le contrôle (necessaire de le mettre ici a cause du md5)
    if ($pwd) {

      // minLength
      if ($pwdSpecs->minLength > strlen($pwd)) {
        return "Mot de passe trop court (minimum {$pwdSpecs->minLength})";
      }

      // notContaining
      if($target = $pwdSpecs->notContaining) {
        if ($field = $this->$target) {
          if (stristr($pwd, $field)) {
          return "Le mot de passe ne doit pas contenir '$field'";
      } } }
      
      // notNear
      if($target = $pwdSpecs->notNear) {
        if ($field = $this->$target) {
          if (levenshtein($pwd, $field) < 3) {
            return "Le mot de passe ressemble trop à '$field'";
      } } }
       
      // alphaAndNum
      if($pwdSpecs->alphaAndNum) {
        if (!preg_match("/[A-z]/", $pwd) || !preg_match("/\d+/", $pwd)) {
          return 'Le mot de passe doit contenir au moins un chiffre ET une lettre';
        }
      }
    } else {
      $this->_user_password = null;
    }
    
    return parent::check();
  }
  
  function store() {
  	$this->updateSpecs();
  	parent::store();
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
      $perm = new CPermModule;
      $perm->user_id = $this->user_id;
      foreach ($perm->loadMatchingList() as $_perm) {
        $_perm->delete();
      }

      $perm = new CPermObject;
      $perm->user_id = $this->user_id;
      foreach ($perm->loadMatchingList() as $_perm) {
        $_perm->delete();
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