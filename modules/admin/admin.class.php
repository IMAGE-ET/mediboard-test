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
13 => "Médecin",
14 => "Personnel"
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
  var $user_last_login  = null;
  var $user_login_errors= null;
  var $template         = null;
  var $profile_id       = null;

  var $_user_password   = null;
  var $_login_locked    = null;

  var $_ref_preferences = null;

  function CUser() {
    $this->CMbObject("users", "user_id");

    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["favoris_CCAM"] = "CFavoriCCAM favoris_user";
    $backRefs["favoris_CIM10"] = "CFavoricim10 favoris_user";
    $backRefs["permissions_module"] = "CPermModule user_id";
    $backRefs["permissions_objet"] = "CPermObject user_id";
    $backRefs["logs"] = "CUserLog user_id";
    return $backRefs;
  }

  function getSpecs() {
    global $dPconfig;
    
  	$specsParent = parent::getSpecs();
     
    $specs = array (
      "user_username"   => "notNull str maxLength|20",
      "user_password"   => "str",
      "user_type"       => "notNull num minMax|0|20",
      "user_first_name" => "str maxLength|50",
      "user_last_name"  => "notNull str maxLength|50 confidential",
      "user_email"      => "str maxLength|255",
      "user_phone"      => "str maxLength|30",
      "user_mobile"     => "str maxLength|30",
      "user_address1"   => "str maxLength|50",
      "user_city"       => "str maxLength|30",
      "user_zip"        => "str maxLength|11",
      "user_country"    => "str maxLength|30",
      "user_birthday"   => "dateTime",
      "user_pic"        => "text",
      "user_signature"  => "text",
      "user_last_login" => "dateTime",
      "user_login_errors"=> "notNull num",
      "template"        => "bool notNull default|0",
      "profile_id"      => "ref class|CUser"
      );

      $specs["_user_password"] = 'password minLength|';

      if ($dPconfig['admin']['CUser']['strong_password'] == '1')
      $specs['_user_password'] .= '6 notContaining|user_username notNear|user_username alphaAndNum';
      else
      $specs['_user_password'] .= 4;

      return array_merge($specsParent, $specs);
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
    $this->user_password = $this->_user_password ? md5($this->_user_password) : null;
  }

  function updateFormFields () {
  	global $dPconfig;
  	
    parent::updateFormFields();
    $this->user_last_name = strtoupper($this->user_last_name);
    $this->user_first_name = ucwords(strtolower($this->user_first_name));
    $this->_view = "$this->user_last_name $this->user_first_name";
    $this->_login_locked = $this->user_login_errors > $dPconfig['admin']['CUser']['max_login_attempts'];
  }

  function check() {
    // Chargement des specs des attributs du mediuser
    $specsObj = $this->getSpecsObj();

    /* On se concentre sur le mot de passe :
     Le mot de passe doit etre controlé sur differents points 
     - Sa longueur 6 en mode "strong_password", 4 sinon
     - Doit contenir des chiffres ET des lettres en mode "strong_password"
     - Ne doit pas contenir le login en mode "strong_password"
     Ensuite, s'il est OK, on passe a la suite sans rien changer de ce qui avait
     été fait dans updateDBFields (md5)
     Sinon on renvoie un message d'erreur qui doit etre geré dans le store et on 
     remet le mot de passe a mettre dans la base de données à null
     */
    $pwdSpecs = $specsObj['_user_password']; // Spec du mot de passe sans _
    $pwd = $this->_user_password; // Le mot de passe récupéré est avec un _

    // S'il a été défini, on le contrôle
    if ($pwd) {
      // minLength
      if ($pwdSpecs->minLength > strlen($pwd)) {
        return "Mot de passe trop court (minimum {$pwdSpecs->minLength})";
      }

      // notContaining
      if($pwdSpecs->notContaining) {
        $target = $pwdSpecs->notContaining;
        if ($field = $this->$target)
        if (stristr($pwd, $field))
        return "Le mot de passe ne doit pas contenir '$field'";
      }
       
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