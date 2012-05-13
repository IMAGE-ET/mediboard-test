<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 *  NOTE: the user_type field in the users table must be changed to a TINYINT
 */
class CUser extends CMbObject {
  // DB key
  var $user_id          = null;
  
  // DB fields
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
  var $dont_log_connection = null;

  // Derived fields
  var $_user_password        = null;
  var $_user_password_weak   = null;
  var $_user_password_strong = null;
  var $_login_locked         = null;
  var $_ldap_linked          = null;
  var $_user_actif           = null;
  var $_user_cps             = null;
  var $_user_deb_activite    = null;
  var $_user_fin_activite    = null;
  var $_count_connections    = null;

  // Behaviour fields
  var $_purge_connections = null;
  
  // Form fields
  var $_user_type_view    = null;
  
  // Object references
  var $_ref_preferences = null;
  var $_ref_mediuser    = null;
  
  // Object collections
  var $_ref_profiled_users = null;
  
  static $types = array(
    // DEFAULT USER (nothing special)
//    0  => "-- Choisir un type",
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
    14 => "Personnel",
    15 => "Rééducateur",
    16 => "Sage Femme",
    17 => "Pharmacien",
    18 => "Aide soignant",
    19 => "Dentiste"
  );
  
  static $ps_types = array(3, 4, 13, 16, 17, 19);
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'users';
    $spec->key   = 'user_id';
    $spec->measureable = true;
    $spec->uniques["username"] = array("user_username");
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["favoris_CCAM"]       = "CFavoriCCAM favoris_user";
    $backProps["favoris_CIM10"]      = "CFavoriCIM10 favoris_user";
    $backProps["favoris_TARMED"]     = "CFavoriTarmed favoris_user";
    $backProps["permissions_module"] = "CPermModule user_id";
    $backProps["permissions_objet"]  = "CPermObject user_id";
    $backProps["owned_logs"]         = "CUserLog user_id";
    $backProps["profiled_users"]     = "CUser profile_id";
    return $backProps;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["user_username"]   = "str notNull maxLength|20";
    $specs["user_password"]   = "str show|0";
    $specs["user_type"]       = "num notNull min|0 max|20";
    $specs["user_first_name"] = "str maxLength|50 seekable|begin";
    $specs["user_last_name"]  = "str notNull maxLength|50 confidential seekable|begin";
    $specs["user_email"]      = "str maxLength|255";
    $specs["user_phone"]      = "phone";
    $specs["user_mobile"]     = "phone";
    $specs["user_address1"]   = "str";
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
    $specs["dont_log_connection"] = "bool default|0";
      
    // The different levels of security are stored to be usable in JS
    $specs["_user_password_weak"]   = "password minLength|4";
    $specs["_user_password_strong"] = "password minLength|6 notContaining|user_username notNear|user_username alphaAndNum";

    if(CAppUI::conf("admin CUser strong_password")) {
      $specs["_user_password"] = $specs["_user_password_strong"];
    } else {
      $specs["_user_password"] = $specs["_user_password_weak"];
    }
    
    $specs["_ldap_linked"]       = "bool";
    $specs["_user_type_view"]    = "str";
    $specs["_count_connections"] = "num";
    
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

  /**
   * Lazy access to a given user, defaultly connected user
   * @param $user_id ref|CUser The user id, connected user if null;
   * @return CUser
   */
  static function get($user_id = null) {
    $user = new CUser;
    return $user->getCached(CValue::first($user_id, CAppUI::$instance->user_id));
  }
  
  function loadView() {
    parent::loadView();
    $this->loadRefMediuser();
    $this->_ref_mediuser->loadView();
  }
  
  /**
   * @return CMediusers
   */
  function loadRefMediuser() {
    $mediuser = new CMediusers();
    if ($mediuser->isInstalled()) {
      $mediuser->load($this->_id);
      $this->_ref_mediuser = $mediuser;
    }
    
    return $mediuser;
  }
  
  /**
   * Return true if user login count system is ready
   * @return bool
   */
  function loginErrorsReady() {
    return $this->_spec->ds->loadField($this->_spec->table, "user_login_errors");
  }
  
  function updatePlainFields() {
    parent::updatePlainFields();
    
    $this->user_password = null;

    // Nullify no to empty in database
    if ($this->_user_password && !preg_match('/^[0-9a-f]{32}$/i', $this->_user_password)) {
      $this->user_password = md5($this->_user_password);
    }
  }

  function updateFormFields () {
    parent::updateFormFields();
    $user_first_name = CMbString::capitalize($this->user_first_name);
    $user_last_name  = CMbString::upper($this->user_last_name);
    $this->_view = "$user_last_name $user_first_name";
    $this->_login_locked = $this->user_login_errors >= CAppUI::conf('admin CUser max_login_attempts');
    $this->_user_type_view = CValue::read(self::$types, $this->user_type);
  }

  function check() {
    // Chargement des specs des attributs du mediuser
    $this->updateSpecs();
    
    $specs = $this->getSpecs();

    // On se concentre dur le mot de passe (_user_password)
    $pwdSpecs = $specs['_user_password'];

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
    } 
    else {
      $this->_user_password = null;
    }
    
    return parent::check();
  }
  
  function store() {
    $this->updateSpecs();
    
    if ($msg = $this->purgeConnections()) {
      return $msg;
    }
    
    return parent::store();
  }
  
  /**
   * We need to delete the CMediusers
   * @return 
   */
  function delete(){
    if ($msg = $this->canDeleteEx()) {
      return $msg;
    }
    
    $mediuser = $this->loadRefMediuser();
    
    if ($mediuser->_id) {
      $mediuser->_keep_user = true;
      if ($msg = $mediuser->delete()) {
        return $msg;
      }
    }
    
    return parent::delete();
  }
  
  /**
   * Purge all connection user log pour user
   * @return string Store-like message
   */
  function purgeConnections() {
    // Behavioural condition
    if (!$this->_purge_connections) {
      return;
    }
    
    // Pointless/dangerous if no key
    if (!$this->_id) {
      return;
    }
    
    $query = "DELETE
      FROM user_log
      WHERE (`user_id` = '$this->_id')
      AND (`fields` = 'user_last_login')
      AND (`object_id` = '$this->_id')
      AND (`object_class` = '$this->_class')";
    
    $log = new CUserLog;
    $ds = $log->_spec->ds;
    if (!$ds->exec($query)) {
      return CAppUI::tr("CUser-failed-purge_connections") . $ds->error();
    }
  }
  
  /**
   * Prepare the user log before object persistence
   * @see parent
   * @return CUserLog null if not loggable
   */ 
  function prepareLog() {
    if ($this->dont_log_connection && $this->fieldModified("user_last_login")) {
      return;
    }
    
    return parent::prepareLog();
  }
  
  /**
   * Merges an array of objects
   * @see parent
   * @param array An array of CMbObject to merge
   * @param bool $fast Tell wether to use SQL (fast) or PHP (slow but checked and logged) algorithm
   * @return CMbObject
   */
  function merge($objects, $fast = false) {
    if (!$this->_id) {
      return "CUser-merge-alternative-mode-required";
    }
    
    // Fast merging obligatoire
    $fast = true;
    $mediusers = array();
    foreach ($objects as $object) {
      $object->loadRefMediuser();
      $mediusers[] = $object->_ref_mediuser;
      $object->removePerms();
    }
    
    $this->loadRefMediuser();
    $this->_ref_mediuser->_force_merge = true;
    $this->_ref_mediuser->merge($mediusers, $fast);
    
    return parent::merge($objects, $fast);
  }

  function removePerms() {
    $this->completeField("user_id");
    $perm = new CPermModule;
    $perm->user_id = $this->user_id;
    $perms = $perm->loadMatchingList();
    foreach ($perms as $_perm) {
      $_perm->delete();
    }

    $perm = new CPermObject;
    $perm->user_id = $this->user_id;
    $perms = $perm->loadMatchingList();
    foreach ($perms as $_perm) {
      $_perm->delete();
    }
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
      $this->removePerms();
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
  
  /**
   * Tell whether user is linked to an LDAP account
   * 
   * @return boolean 
   */
  function isLDAPLinked() {
    if (!CAppUI::conf("admin LDAP ldap_connection") || !$this->_id) {
      return;
    }
    $this->loadLastId400(CAppUI::conf("admin LDAP ldap_tag"));
    return $this->_ldap_linked = ($this->_ref_last_id400->_id) ? 1 : 0;
  }
  
  /**
   * Count connections for user
   * 
   * @return integer
   */
  function countConnections() {
    $log = new CUserLog;
    $log->user_id = $this->_id; 
    $log->setObject($this);
    $log->fields = "user_last_login";
    return $this->_count_connections = $log->countMatchingList();
  }
  
  /**
   * Get the profiled users when this is a template
   * 
   * @return array<CUser> Profiled users collection
   */
  function loadRefProfiledUsers() {
    return $this->_ref_profiled_users = $this->loadBackRefs("profiled_users", "user_last_name, user_first_name");
  }
  
  function canChangePassword() {
    return (CAppUI::conf("admin CUser allow_change_password") || $this->user_type == 1);
  }
}

?>