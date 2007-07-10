<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage mediusers
 *	@version $Revision$
 *  @author Romain Ollivier
 */

global $utypes, $utypes_flip;
global $AppUI;
require_once($AppUI->getModuleClass("admin"));

$utypes_flip = array_flip($utypes);

/**
 * The CMediusers class
 */
class CMediusers extends CMbObject {
  // DB Table key
  var $user_id = null;

  // DB Fields
  var $remote        = null;
  var $adeli         = null;
  var $titres        = null;
  var $commentaires  = null;
  var $actif         = null;
  var $deb_activite  = null;
  var $fin_activite  = null;

  // DB References
  var $function_id   = null;
  var $discipline_id = null;
  var $spec_cpam_id  = null;

  // dotProject user fields
  var $_user_type       = null;
  var $_user_username   = null;
  var $_user_password   = null;
  var $_user_password2  = null;
  var $_user_first_name = null;
  var $_user_last_name  = null;
  var $_user_email      = null;
  var $_user_phone      = null;
  var $_user_adresse    = null;
  var $_user_cp         = null;
  var $_user_ville      = null;
  var $_user_last_login = null;

  // Other fields
  var $_view       = null;
  var $_shortview  = null;
  var $_profile_id = null;

  // Object references
  var $_ref_function   = null;
  var $_ref_discipline = null;
  var $_ref_packs      = array();
  var $_ref_protocoles = array();

  function CMediusers() {
    $this->CMbObject( "users_mediboard", "user_id" );

    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "remote"        => "bool",
      "adeli"         => "numchar length|9 confidential",
      "function_id"   => "notNull ref class|CFunctions",
      "discipline_id" => "ref class|CDiscipline",
      "titres"        => "text",
      "commentaires"  => "text",
      "actif"         => "bool default|1",
      "deb_activite"  => "date",
      "fin_activite"  => "date",
      "spec_cpam_id"  => "ref class|CSpecCPAM",
      
      "_user_username"   => "notNull str minLength|4",
      "_user_password"   => "str minLength|4",
      "_user_password2"  => "str sameAs|_user_password",
      "_user_first_name" => "str",
      "_user_last_name"  => "notNull str confidential",
      "_user_email"      => "str confidential",
      "_user_phone"      => "num length|10 confidential",
      "_user_adresse"    => "str confidential",
      "_user_cp"         => "num length|5 confidential",
      "_user_ville"      => "str confidential"
      );
  }

  function getSeeks() {
    return array (
      "user_id"  => "ref|CUser"
      );
  }

  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["actes_CCAM"] = "CActeCCAM executant_id";
      $backRefs["aides_saisi"] = "CAideSaisie user_id";
      $backRefs["modeles"] = "CCompteRendu chir_id";
      $backRefs["documents_ged"] = "CDocGed user_id";
      $backRefs["suivi_documents_ged"] = "CDocGedSuivi user_id";
      $backRefs["examens"] = "CExamenLabo realisateur";
      $backRefs["users"] = "CFicheEi user_id";
      $backRefs["valid_users"] = "CFicheEi valid_user_id";
      $backRefs["service_valid_users"] = "CFicheEi service_valid_user_id";
      $backRefs["qualite_users"] = "CFicheEi qualite_user_id";
      $backRefs["files"] = "CFile file_owner";
      $backRefs["listes_choix"] = "CListeChoix chir_id";
      $backRefs["notes"] = "CNote user_id";
      $backRefs["operations_chir"] = "COperation chir_id";
      $backRefs["operations_anesth"] = "COperation anesth_id";
      $backRefs["packs"] = "CPack chir_id";
      $backRefs["plages_op_chir"] = "CPlageOp chir_id";
      $backRefs["plages_op_anesth"] = "CPlageOp anesth_id";
      $backRefs["plages_consult"] = "CPlageconsult chir_id";
      $backRefs["plages_ressource"] = "CPlageressource prat_id";
      $backRefs["prescriptions_labo"] = "CPrescriptionLabo praticien_id";
      $backRefs["protocoles"] = "CProtocole chir_id";
      $backRefs["sejours"] = "CSejour praticien_id";
      $backRefs["tarifs"] = "CTarif chir_id";
      $backRefs["temps_hospi"] = "CTempsHospi praticien_id";
      $backRefs["temps_chir"] = "CTempsOp chir_id";
      $backRefs["temps_prepa"] = "CTempsPrepa chir_id";
     return $backRefs;
  }
   
  function createUser() {
    $user = new CUser();
    $user->user_id = $this->user_id;

    $user->user_type        = $this->_user_type      ;
    $user->user_username    = $this->_user_username  ;
    $user->_user_password   = $this->_user_password  ;
    $user->user_first_name  = $this->_user_first_name;
    $user->user_last_name   = $this->_user_last_name ;
    $user->user_email       = $this->_user_email     ;
    $user->user_phone       = $this->_user_phone     ;
    $user->user_address1    = $this->_user_adresse   ;
    $user->user_zip         = $this->_user_cp        ;
    $user->user_city        = $this->_user_ville     ;

    return $user;
  }

  function delete() {
    // @todo delete Favoris CCAM et CIM en cascade

    $msg = null;
    // Delete corresponding dP user first
    if (!$msg = $this->canDeleteEx()) {
      $dPuser = $this->createUser();
      if ($msg = $dPuser->delete()) {
        return $msg;
      }
    }

    return parent::delete();
  }

  function updateFormFields() {
    parent::updateFormFields();
    global $utypes;
    $user = new CUser();
    if($result = $user->load($this->user_id)) {
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
      $this->_user_last_login = $user->user_last_login;
      // Encrypt this datas
      $this->checkConfidential();
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

  function loadRefFunction() {
    $this->_ref_function = new CFunctions;
    $this->_ref_function->load($this->function_id);
  }

  function loadRefsFwd() {
    // Forward references
    $this->loadRefFunction();
    $this->_ref_discipline = new CDiscipline;
    $this->_ref_discipline->load($this->discipline_id);
  }

  function loadRefsBack() {
    $where = array(
      "chir_id" => "= '$this->user_id'");
      $this->_ref_packs = new CPack;
      $this->_ref_packs = $this->_ref_packs->loadList($where);
  }

  function getPerm($permType) {
    if (!$this->_ref_function) {
      $this->loadRefFunction();
    }

    return $this->_ref_function->getPerm($permType);
  }

  function loadProtocoles() {
    $where["chir_id"] = "= '$this->user_id'";
    $order = "codes_ccam";
    $protocoles = new CProtocole;
    $this->_ref_protocoles = $protocoles->loadList($where, $order);
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

    // User might have been re-created
    if ($this->user_id != $dPuser->user_id) {
      $this->user_id = null;
    }

    // Can't use parent::store cuz user_id don't auto-increment
    if ($this->user_id) {
      $ret = db_updateObject($this->_tbl, $this, $this->_tbl_key);
    } else {
      $this->user_id = $dPuser->user_id;
      $ret = db_insertObject($this->_tbl, $this, $this->_tbl_key);
    }

    return db_error();
  }

  function delFunctionPermission() {
    $where = array();
    $where["user_id"]      = "= '$this->user_id'";
    $where["object_class"] = "= 'CFunctions'";
    $where["object_id"]    = "= '$this->function_id'";

    $perm = new CPermObject;
    if($perm->loadObject($where)) {
      $perm->delete();
    }
  }

  function insFunctionPermission() {
    $where = array();
    $where["user_id"]      = "= '$this->user_id'";
    $where["object_class"] = "= 'CFunctions'";
    $where["object_id"]    = "= '$this->function_id'";

    $perm = new CPermObject;
    if(!$perm->loadObject($where)) {
      $perm = new CPermObject;
      $perm->user_id      = $this->user_id;
      $perm->object_class = "CFunctions";
      $perm->object_id    = $this->function_id;
      $perm->permission   = PERM_EDIT;
      $perm->store();
    }
  }

  function insGroupPermission() {
    $function = new CFunctions;
    $function->load($this->function_id);
    $where = array();
    $where["user_id"]      = "= '$this->user_id'";
    $where["object_class"] = "= 'CGroups'";
    $where["object_id"]    = "= '$function->group_id'";

    $perm = new CPermObject;
    if(!$perm->loadObject($where)) {
      $perm = new CPermObject;
      $perm->user_id      = $this->user_id;
      $perm->object_class = "CGroups";
      $perm->object_id    = $function->group_id;
      $perm->permission   = PERM_EDIT;
      $perm->store();
    }
  }

  function loadListFromType($user_types = null, $permType = PERM_READ, $function_id = null, $name = null) {
    global $utypes_flip;

    $functions = $this->loadFonctions($permType);

    // Filter on a single function
    if ($function_id) {
      $functions = array_key_exists($function_id, $functions) ?
      array($function_id => $functions[$function_id]) :
      array();
    }

    $where = array();
    $where["users_mediboard.function_id"] = db_prepare_in(array_keys($functions));

    // Filters on users' values
    $ljoin = array();
    $ljoin["users"] = "`users`.`user_id` = `users_mediboard`.`user_id`";

    if ($name) {
      $where["users.user_last_name"] = "LIKE '$name%'";
    }

    if (is_array($user_types)) {
      foreach ($user_types as $key => $value) {
        $user_types[$key] = $utypes_flip[$value];
      }

      $where["users.user_type"] = db_prepare_in($user_types);
    }

    $order = "`users`.`user_last_name`, `users`.`user_first_name`";

    // Get all users
    $mediuser = new CMediusers;
    $mediusers = $mediuser->loadList($where, $order, null, null, $ljoin);
  
    // Associate already loaded function
    foreach ($mediusers as $keyUser => $mediuser) {
      $mediuser->_ref_function =& $functions[$mediuser->function_id];
    }

    return $mediusers;
  }

  function loadEtablissements($permType = PERM_READ){
    // Liste de Tous les établissements
    $group = new CGroups;
    $order = "text";
    $groups = $group->loadList(null, $order);

    // Filtre
    foreach($groups as $keyGroupe => $groupe){
      if (!$groupe->getPerm($permType)) {
        unset($groups[$keyGroupe]);
      }
    }

    return $groups;
  }

  /**
   * Load functions with permissions for given group, current group by default
   */
  function loadFonctions($permType = PERM_READ, $group_id = null) {
    global $g;
    $functions = new CFunctions;
    $functions->group_id = mbGetValue($group_id, $g);
    $order = "text";
    $functions = $functions->loadMatchingList($order);

    if ($permType) {
      foreach ($functions as $_id => $function) {
        if (!$function->getPerm($permType)) {
          unset($functions[$_id]);
        }
      }
    }
    return $functions;
  }

  function loadUsers($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(null, $permType, $function_id, $name);
  }

  function loadMedecins($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Médecin"), $permType, $function_id, $name);
  }

  function loadChirurgiens($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Chirurgien"), $permType, $function_id, $name);
  }

  function loadAnesthesistes($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Anesthésiste"), $permType, $function_id, $name);
  }

  function loadPraticiens($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Chirurgien", "Anesthésiste", "Médecin"), $permType, $function_id, $name);
  }

  function isFromType($user_types) {
    // Warning: !== operator
    return array_search($this->_user_type, $user_types) !== false;
  }

  function isPraticien () {
    return $this->isFromType(array("Chirurgien", "Anesthésiste"));
  }

  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_function->fillTemplate($template);
    $template->addProperty("Praticien - nom"       , $this->_user_last_name );
    $template->addProperty("Praticien - prénom"    , $this->_user_first_name);
    $template->addProperty("Praticien - spécialité", $this->_ref_function->text);
    $template->addProperty("Praticien - titres"    , $this->titres);
    $template->addProperty("Praticien - ADELI"     , $this->adeli);
  }
}

?>