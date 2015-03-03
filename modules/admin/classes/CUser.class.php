<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage admin
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 *  NOTE: the user_type field in the users table must be changed to a TINYINT
 */
class CUser extends CPerson {
  // DB key
  public $user_id;

  // DB fields
  public $user_username;
  public $user_password;
  public $user_salt;
  public $user_type;
  public $user_first_name;
  public $user_last_name;
  public $user_email;
  public $user_phone;
  public $user_mobile;
  public $user_astreinte;
  public $user_address1;
  public $user_city;
  public $user_zip;
  public $user_country;
  public $user_birthday;
  public $user_last_login;

  /** @var bool Does the password need to be changed */
  public $force_change_password;

  /** @deprecated */
  public $user_login_errors;
  public $template;
  public $profile_id;
  public $dont_log_connection;
  public $user_password_last_change;

  // Derived fields
  public $_user_password;
  public $_user_password_weak;
  public $_user_password_strong;
  public $_login_locked;
  public $_ldap_linked;
  public $_user_actif;
  public $_user_cps;
  public $_user_deb_activite;
  public $_user_fin_activite;
  public $_count_connections;

  public $_is_logging;
  public $_user_salt;
  public $_user_last_login;

  public $_is_changing;

  // Behaviour fields
  public $_purge_connections;

  // Form fields
  public $_user_type_view;
  public $_bound;
  public $_count_ldap;

  // Object references
  public $_ref_preferences;

  /** @var CUserAuthentication */
  public $_ref_last_auth;

  /** @var CMediusers */
  public $_ref_mediuser;

  /** @var self[] */
  public $_ref_profiled_users;

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
    19 => "Dentiste",
    20 => "Préparateur",
    21 => "Diététicien"
  );

  static $ps_types = array(3, 4, 13, 16, 17, 19);

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'users';
    $spec->key   = 'user_id';
    $spec->measureable = true;
    $spec->uniques["username"] = array("user_username");
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["favoris_CCAM"]       = "CFavoriCCAM favoris_user";
    $backProps["favoris_CIM10"]      = "CFavoriCIM10 favoris_user";
    $backProps["favoris_TARMED"]     = "CFavoriTarmed favoris_user";
    $backProps["permissions_module"] = "CPermModule user_id";
    $backProps["permissions_objet"]  = "CPermObject user_id";
    $backProps["owned_logs"]         = "CUserLog user_id";
    $backProps["profiled_users"]     = "CUser profile_id";
    $backProps["password_keepers"]   = "CPasswordKeeper user_id";
    $backProps["preferences"]        = "CPreferences user_id";
    $backProps["error_logs"]         = "CErrorLog user_id";
    $backProps["authentications"]    = "CUserAuthentication user_id";
    $backProps["authentications_previous"] = "CUserAuthentication previous_user_id";
    $backProps['task_author']        = 'CSejourTask author_id';
    $backProps['task_author_realise']= 'CSejourTask author_realise_id';
    $backProps["ox_message_items"]   = "COXMessageItem user_id";

    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    // Plain fields
    $props["user_username"]             = "str notNull maxLength|20 seekable|begin";
    $props["user_password"]             = "str maxLength|64 show|0 loggable|0";
    $props["user_salt"]                 = "str maxLength|64 show|0 loggable|0";
    $props["user_type"]                 = "num notNull min|0 max|21 default|0";
    $props["user_first_name"]           = "str maxLength|50 seekable|begin";
    $props["user_last_name"]            = "str notNull maxLength|50 confidential seekable|begin";
    $props["user_email"]                = "str maxLength|255";
    $props["user_phone"]                = "phone";
    $props["user_mobile"]               = "phone";
    $props["user_astreinte"]            = "str";
    $props["user_address1"]             = "str";
    $props["user_city"]                 = "str maxLength|30";
    $props["user_zip"]                  = "str maxLength|11";
    $props["user_country"]              = "str maxLength|30";
    $props["user_birthday"]             = "birthDate";
    $props["user_last_login"]           = "dateTime"; // To be removed
    $props["user_login_errors"]         = "num notNull min|0 max|100 default|0";
    $props["template"]                  = "bool notNull default|0";
    $props["profile_id"]                = "ref class|CUser";
    $props["dont_log_connection"]       = "bool default|0";
    $props["user_password_last_change"] = "dateTime notNull";
    $props["force_change_password"]     = "bool default|0";

    // The different levels of security are stored to be usable in JS
    $props["_user_password_weak"]   = "password minLength|4 randomizable";
    $props["_user_password_strong"] = "password minLength|6 notContaining|user_username notNear|user_username alphaAndNum randomizable";

    // The actuel config level
    $props["_user_password"] = CAppUI::conf("admin CUser strong_password") ?
      $props["_user_password_strong"] :
      $props["_user_password_weak"];

    // Derived fields
    $props["_ldap_linked"]       = "bool";
    $props["_user_type_view"]    = "str";
    $props["_count_connections"] = "num";
    $props["_is_logging"]        = "bool";
    $props["_is_changing"]       = "bool";
    $props["_user_salt"]         = "str";
    $props["_login_locked"]      = "bool";
    $props["_user_last_login"]   = "dateTime";

    return $props;
  }

  /**
   * Update the object's specs
   *
   * @return void
   **/
  function updateSpecs() {
    $oldSpec = $this->_specs['_user_password'];

    $user = new CMediusers();
    $remote = 0;

    if ($user->isInstalled()) {
      if ($result = $user->load($this->user_id)) {
        $remote = $user->remote;
      }
    }

    // Determine if password length is sufficient
    $strongPassword = ((CAppUI::conf("admin CUser strong_password") == "1")
      && (($remote == 0) || CAppUI::conf("admin CUser apply_all_users")));

    // If the global strong password config is set to TRUE and the user can connect remotely
    $this->_specs['_user_password'] = $strongPassword ?
      $this->_specs['_user_password_strong'] :
      $this->_specs['_user_password_weak'];

    $this->_specs['_user_password']->fieldName = $oldSpec->fieldName;

    $this->_props['_user_password'] = $strongPassword ?
      $this->_props['_user_password_strong'] :
      $this->_props['_user_password_weak'];
  }

  /**
   * Lazy access to a given user, defaultly connected user
   *
   * @param integer $user_id The user id, connected user if null;
   *
   * @return CUser
   */
  static function get($user_id = null) {
    $user = new CUser;
    return $user->getCached(CValue::first($user_id, CAppUI::$instance->user_id));
  }

  /**
   * @see parent::loadView()
   */
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
    if (CModule::getInstalled("mediusers")) {
      $mediuser->load($this->_id);
      $this->_ref_mediuser = $mediuser;
    }

    return $mediuser;
  }

  /**
   * Return true if user login count system is ready
   *
   * @return boolean
   */
  function loginErrorsReady() {
    return $this->_spec->ds->loadField($this->_spec->table, "user_login_errors");
  }

  /**
   * Return true if new hash system is ready
   *
   * @return boolean
   */
  function loginSaltReady() {
    return $this->_spec->ds->loadField($this->_spec->table, "user_salt");
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updatePlainFields();

    // To prevent from recalculate new salt and re-hash password
    if ($this->_merging) {
      return;
    }
    $this->user_password = null;

    // If no raw password or already hashed, nothing to do
    if (!$this->_user_password || preg_match('/^[0-9a-f]{32}$/i', $this->_user_password)) {
      return;
    }

    // If the new password hashing system is not ready yet
    if (!$this->loginSaltReady()) {
      CValue::setSessionAbs("_pass_deferred", $this->_user_password);
      $this->user_password = md5($this->_user_password);
      return;
    }

    // If user is logging, get the salt value in table
    if (!$this->_is_logging || $this->_is_changing) {
      $this->generateUserSalt();
      return;
    }

    // If user is trying to log in, we have to compare hashes with corresponding user in table
    $where = array(
      "user_username" => " = '$this->user_username'"
    );
    $_user = new CUser();
    $_user->loadObject($where);

    // If user exists, we compare hashes
    if ($_user->_id) {
      // Password is a SHA256 hash, we get user's salt
      if ($this->_user_password && strlen($_user->user_password) == 64) {
        $this->user_password = hash("SHA256", $_user->user_salt.$this->_user_password);
        return;
      }

      // Password is an old MD5 hash, we have to update
      if ($_user->user_password == md5($this->_user_password)) {
        $this->generateUserSalt();
        $_user->_user_password = $this->_user_password;
        $_user->_user_salt     = $this->user_salt;
        $_user->store();
      }
      else {
        // Won't load anything
        $this->user_password = "dontmatch";
      }
    }
  }

  /**
   * Randomly create a 64 bytes salt according to available methods
   * Compute user_password by hashing _user_password + user_salt
   *
   * @return void
   */
  function generateUserSalt() {
    if ($this->_user_salt) {
      $this->user_salt = $this->_user_salt;
    }
    else {
      // Mcrypt has a better CSPRNG method
      if (function_exists('mcrypt_create_iv')) {
        // CSPRNG initialisation
        srand();
        // Random salt if modifying the user
        $this->user_salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
      }
      else {
        // Instead of Mcrypt, we use mt_rand() method
        $this->user_salt = hash("SHA256", mt_rand());
      }
    }

    // DB field to update
    $this->user_password_last_change = CMbDT::dateTime();

    // Compute the hash
    $this->user_password = hash("SHA256", $this->user_salt.$this->_user_password);
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields () {
    parent::updateFormFields();

    $user_first_name = CMbString::capitalize($this->user_first_name);
    $user_last_name  = CMbString::upper($this->user_last_name);

    $this->_view           = "$user_last_name $user_first_name";
    $this->_login_locked   = $this->user_login_errors >= CAppUI::conf('admin CUser max_login_attempts');
    $this->_user_type_view = CValue::read(self::$types, $this->user_type);

    $this->mapPerson();
  }

  /**
   * @see parent::check()
   */
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
      if (($target = $pwdSpecs->notContaining) && ($field = $this->$target) && stristr($pwd, $field)) {
        return "Le mot de passe ne doit pas contenir '$field'";
      }

      // notNear
      if (($target = $pwdSpecs->notNear) && ($field = $this->$target) && (levenshtein($pwd, $field) < 3)) {
        return "Le mot de passe ressemble trop à '$field'";
      }

      // alphaAndNum
      if ($pwdSpecs->alphaAndNum && (!preg_match("/[A-z]/", $pwd) || !preg_match("/\d+/", $pwd))) {
        return 'Le mot de passe doit contenir au moins un chiffre ET une lettre';
      }
    }
    else {
      $this->_user_password = null;
    }

    return parent::check();
  }

  /**
   * @see parent::store()
   */
  function store() {
    $this->updateSpecs();

    if (!$this->_id) {
      $this->user_password_last_change = CMbDT::dateTime();
    }

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
      return null;
    }

    // Pointless/dangerous if no key
    if (!$this->_id) {
      return null;
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
    return null;
  }

  /**
   * Merges an array of objects
   * @see parent
   *
   * @param CUser[] $objects An array of CMbObject to merge
   * @param bool    $fast    Tell wether to use SQL (fast) or PHP (slow but checked and logged) algorithm
   *
   * @return CUser
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
    foreach ($perms as $perm) {
      $perm->perm_module_id = null;
      $perm->user_id = $this->user_id;
      $perm->store();
    }

    //Object permissions
    $perms = new CPermObject;
    $perms = $perms->loadList("user_id = '$user_id'");

    // Copy them
    foreach ($perms as $perm) {
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
      return null;
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
    return $this->_count_connections = $this->countBackRefs("authentications");
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

  static function checkPassword($username, $password, $return_object = false) {
    $new_user = new self;
    $new_user->user_username  = $username;
    $new_user->_user_password = $password;
    $new_user->_is_logging    = true;
    $new_user->loadMatchingObjectEsc();

    if ($return_object) {
      return $new_user;
    }

    return $new_user->_id != null;
  }

  /**
   * Map the class variable with CPerson variable
   *
   * @return void
   */
  function mapPerson() {
    $this->_p_city                = $this->user_city;
    $this->_p_postal_code         = $this->user_zip;
    $this->_p_street_address      = $this->user_address1;
    $this->_p_country             = $this->user_country;
    $this->_p_phone_number        = $this->user_phone;
    $this->_p_mobile_phone_number = $this->user_mobile;
    $this->_p_email               = $this->user_email;
    $this->_p_first_name          = $this->user_first_name;
    $this->_p_last_name           = $this->user_last_name;
    $this->_p_birth_date          = $this->user_birthday;
  }

  /**
   * Get last authentication
   *
   * @return CUserAuthentication|null
   */
  function loadRefLastAuth(){
    if (!$this->_id || !CUserAuthentication::authReady()) {
      return null;
    }

    $authentications = $this->loadBackRefs("authentications", "datetime_login DESC", 1);
    if (!empty($authentications)) {
      $this->_ref_last_auth = reset($authentications);
    }

    return $this->_ref_last_auth;
  }

  /**
   * Get last authentication date
   *
   * @return string|null
   */
  function getLastLogin(){
    if ($this->_user_last_login) {
      return $this->_user_last_login;
    }

    $auth = $this->loadRefLastAuth();

    $last_login = null;
    if ($auth && $auth->_id) {
      $last_login = $auth->datetime_login;
    }

    return $this->_user_last_login = $last_login;
  }
}