<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage mediusers
 *  @version $Revision$
 *  @author Romain Ollivier
 */

CAppUI::requireModuleClass("admin", "admin");

/**
 * The CMediusers class
 */
class CMediusers extends CMbObject {
  // DB Table key
  var $user_id = null;

  // DB Fields
  var $remote                      = null;
  var $adeli                       = null;
  var $rpps                        = null;
  var $titres                      = null;
  var $commentaires                = null;
  var $actif                       = null;
  var $deb_activite                = null;
  var $fin_activite                = null;
  var $compte                      = null;
  var $banque_id                   = null;

  // DB References
  var $function_id                 = null;
  var $discipline_id               = null;
  var $spec_cpam_id                = null;
  
  var $code_intervenant_cdarr      = null;

  var $secteur = null;
  // Champs utilisés pour l'affichage des ordonnances ALD
  var $cab  = null;
  var $conv = null;  
  var $zisd = null;
  var $ik   = null;

  // dotProject user fields
  var $_user_type                  = null;
  var $_user_username              = null;
  var $_user_password              = null;
  var $_user_password2             = null;
  var $_user_first_name            = null;
  var $_user_last_name             = null;
  var $_user_email                 = null;
  var $_user_phone                 = null;
  var $_user_adresse               = null;
  var $_user_cp                    = null;
  var $_user_ville                 = null;
  var $_user_last_login            = null;
  var $_user_template              = null;

  // Other fields
  var $_profile_id                 = null;
  var $_is_praticien               = null;
  var $_is_secretaire              = null;
  var $_is_anesth                  = null;
  var $_is_infirmiere              = null;
  var $_user_password_weak         = null;
  var $_user_password_strong       = null;
  var $_basic_info                 = null;
   var $_is_urgentiste             = null;
  
  // Distant fields
  var $_group_id                   = null;

  // CPS
  var $_bind_cps                   = null;
  var $_id_cps                     = null;

  // Object references
  var $_ref_banque                 = null;
  var $_ref_function               = null;
  var $_ref_discipline             = null;
  var $_ref_profile                = null;
  var $_ref_user                   = null;
  var $_ref_code_intervenant_cdarr = null;
  var $_ref_packs                  = array();
  var $_ref_protocoles             = array();
  
  // Object references per day
  var $_ref_plages                 = null;
	var $_ref_plages_conge        = null;
  var $_ref_urgences               = null;
  var $_ref_deplacees              = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'users_mediboard';
    $spec->key   = 'user_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $phone_number_format = str_replace(' ', 'S', CAppUI::conf("system phone_number_format"));

    // Note: notamment utile pour les seeks
    // Dans les faits c'est plus logique puisque la classe n'est pas autoincremented
    $specs["user_id"]                = "ref class|CUser seekable show|0";
    
    $specs["remote"]                 = "bool default|1 show|0";
    $specs["adeli"]                  = "numchar length|9 confidential mask|99S9S99999S9 control|luhn";
    $specs["rpps"]                   = "numchar length|11 confidential mask|99999999999 control|luhn";
    $specs["function_id"]            = "ref notNull class|CFunctions seekable";
    $specs["discipline_id"]          = "ref class|CDiscipline";
    $specs["titres"]                 = "text";
    $specs["commentaires"]           = "text";
    $specs["actif"]                  = "bool default|1";
    $specs["deb_activite"]           = "date";
    $specs["fin_activite"]           = "date";
    $specs["spec_cpam_id"]           = "ref class|CSpecCPAM";
    $specs["compte"]                 = "code rib confidential mask|99999S99999S99999999999S99 show|0";
    $specs["banque_id"]              = "ref class|CBanque show|0";
    $specs["code_intervenant_cdarr"] = "str length|2";
		$specs["secteur"]                = "enum list|1|2";
    $specs["cab"]                    = "str";
    $specs["conv"]                   = "str";  
    $specs["zisd"]                   = "str";
    $specs["ik"]                     = "str";
    
    $specs["_group_id"]              = "ref notNull class|CGroups";
    
    $specs["_user_username"]         = "str notNull minLength|4 reported";
    $specs["_user_password2"]        = "password sameAs|_user_password reported";
    $specs["_user_first_name"]       = "str reported";
    $specs["_user_last_name"]        = "str notNull confidential reported";
    $specs["_user_email"]            = "str confidential reported";
    $specs["_user_phone"]            = "numchar confidential length|10 mask|$phone_number_format reported";
    $specs["_user_adresse"]          = "str confidential reported";
    $specs["_user_last_login"]       = "dateTime reported";
    $specs["_user_cp"]               = "num length|5 confidential reported";
    $specs["_user_ville"]            = "str confidential reported";
    $specs["_profile_id"]            = "ref reported class|CUser";
    $specs["_user_type"]             = "num notNull min|0 max|20 reported";
    
    // The different levels of security are stored to be usable in JS
    $specs["_user_password_weak"]    = "password minLength|4";
    $specs["_user_password_strong"]  = "password minLength|6 notContaining|_user_username notNear|_user_username alphaAndNum";

    $specs["_user_password"]         = $specs["_user_password_weak"]." reported";

    return $specs;
  }
  
  /** Update the object's specs */
  function updateSpecs() {
    $oldSpec = $this->_specs['_user_password'];
    
    $strongPassword = ((CAppUI::conf("admin CUser strong_password") == "1") && ($this->remote == 0));
    
    // If the global strong password config is set to TRUE and the user can connect remotely
    $this->_specs['_user_password'] = $strongPassword?
      $this->_specs['_user_password_strong']:
      $this->_specs['_user_password_weak'];
    
    $this->_specs['_user_password']->fieldName = $oldSpec->fieldName;
    
    $this->_props['_user_password'] = $strongPassword?
      $this->_props['_user_password_strong']:
      $this->_props['_user_password_weak'];
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["secondary_functions"]             = "CSecondaryFunction user_id";
    $backProps["actes_ccam_executes"]             = "CActeCCAM executant_id";
    $backProps["actes_ngap_executes"]             = "CActeNGAP executant_id";
    $backProps["administrations"]                 = "CAdministration administrateur_id";
    $backProps["aides_saisie"]                    = "CAideSaisie user_id";
    $backProps["modeles"]                         = "CCompteRendu chir_id";
    $backProps["documents_ged"]                   = "CDocGed user_id";
    $backProps["suivis__ged"]                     = "CDocGedSuivi user_id";
    $backProps["examens"]                         = "CExamenLabo realisateur";
    $backProps["users"]                           = "CFicheEi user_id";
    $backProps["valid_users"]                     = "CFicheEi valid_user_id";
    $backProps["service_valid_users"]             = "CFicheEi service_valid_user_id";
    $backProps["qualite_users"]                   = "CFicheEi qualite_user_id";
    $backProps["owned_files"]                     = "CFile file_owner";
    $backProps["forum_messages"]                  = "CForumMessage user_id";
    $backProps["forum_threads"]                   = "CForumThread user_id";
    $backProps["hprim21_medecins"]                = "CHprim21Medecin user_id";
    $backProps["listes_choix"]                    = "CListeChoix chir_id";
    $backProps["mails_sent"]                      = "CMbMail from";
    $backProps["mails_received"]                  = "CMbMail to";
    $backProps["owned_notes"]                     = "CNote user_id";
    $backProps["observations"]                    = "CObservationMedicale user_id";
    $backProps["operations_chir"]                 = "COperation chir_id";
    $backProps["operations_anesth"]               = "COperation anesth_id";
    $backProps["packs"]                           = "CPack chir_id";
    $backProps["prescription_line_mixes"]         = "CPrescriptionLineMix praticien_id";
    $backProps["prescription_line_mixes_0"]       = "CPrescriptionLineMix creator_id";
    $backProps["personnels"]                      = "CPersonnel user_id";
    $backProps["plages_op_chir"]                  = "CPlageOp chir_id";
    $backProps["plages_op_anesth"]                = "CPlageOp anesth_id";
    $backProps["plages_consult"]                  = "CPlageconsult chir_id";
    $backProps["plages_conge"]                    = "CPlageCOnge user_id";
    $backProps["consults_anesth"]                 = "CConsultAnesth chir_id";
    $backProps["plages_ressource"]                = "CPlageressource prat_id";
    $backProps["prescriptions"]                   = "CPrescription praticien_id";
    $backProps["prescriptions_labo"]              = "CPrescriptionLabo praticien_id";
    $backProps["prescription_comments"]           = "CPrescriptionLineComment praticien_id";
    $backProps["prescription_comments_crees"]     = "CPrescriptionLineComment creator_id";
    $backProps["prescription_comments_executes"]  = "CPrescriptionLineComment user_executant_id";
    $backProps["prescription_elements"]           = "CPrescriptionLineElement praticien_id";
    $backProps["prescription_elements_crees"]     = "CPrescriptionLineElement creator_id";
    $backProps["prescription_elements_executes"]  = "CPrescriptionLineElement user_executant_id";
    $backProps["prescription_medicaments"]        = "CPrescriptionLineMedicament praticien_id";
    $backProps["prescription_medicaments_crees"]  = "CPrescriptionLineMedicament creator_id";
    $backProps["prescription_protocole_packs"]    = "CPrescriptionProtocolePack praticien_id";
    $backProps["prescription_dmis"]               = "CPrescriptionLineDMI praticien_id";
    $backProps["protocoles"]                      = "CProtocole chir_id";
    $backProps["remplacements"]                   = "CPlageConge replacer_id";
    $backProps["sejours"]                         = "CSejour praticien_id";
    $backProps["tarifs"]                          = "CTarif chir_id";
    $backProps["techniciens"]                     = "CTechnicien kine_id";
    $backProps["temps_hospi"]                     = "CTempsHospi praticien_id";
    $backProps["temps_chir"]                      = "CTempsOp chir_id";
    $backProps["temps_prepa"]                     = "CTempsPrepa chir_id";
    $backProps["transmissions"]                   = "CTransmissionMedicale user_id";
    $backProps["visites_anesth"]                  = "COperation prat_visite_anesth_id";
    $backProps["checked_lists"]                   = "CDailyCheckList validator_id";
    $backProps["evenements_ssr"]                  = "CEvenementSSR therapeute_id";
		$backProps["activites_rhs"]                   = "CLigneActivitesRHS executant_id";
		$backProps["replacement"]                     = "CReplacement replacer_id";
    return $backProps;
  }
   
  function createUser() {
    $user = new CUser();
    $user->user_id = $this->user_id;
		
		$user->user_type        = $this->_user_type;
    $user->user_username    = $this->_user_username;
    $user->_user_password   = $this->_user_password;
    $user->user_first_name  = $this->_user_first_name;
    $user->user_last_name   = $this->_user_last_name;
    $user->user_email       = $this->_user_email;
    $user->user_phone       = $this->_user_phone;
    $user->user_address1    = $this->_user_adresse;
    $user->user_zip         = $this->_user_cp;
    $user->user_city        = $this->_user_ville;
    $user->template         = 0;
    $user->profile_id       = $this->_profile_id;

    $user->_merging = $this->_merging;
    return $user;
  }

  function delete() {
    $msg = null;
    // Delete corresponding dP user first
    if (!$msg = $this->canDeleteEx()) {
      $user = $this->createUser();
      if ($msg = $user->delete()) {
        return $msg;
      }
    }

    return parent::delete();
  }

  function updateFormFields() {
    parent::updateFormFields();

    $user = new CUser();
    if ($user->load($this->user_id)) {
      $this->_user_type       = $user->user_type;
      $this->_user_username   = $user->user_username;
      $this->_user_password   = $user->user_password;
      $this->_user_first_name = CMbString::capitalize($user->user_first_name);
      $this->_user_last_name  = CMbString::upper($user->user_last_name);
      $this->_user_email      = $user->user_email;
      $this->_user_phone      = $user->user_phone;
      $this->_user_adresse    = $user->user_address1;
      $this->_user_cp         = $user->user_zip;
      $this->_user_ville      = $user->user_city;
      $this->_user_last_login = $user->user_last_login;
      $this->_user_template   = $user->template;
      $this->_profile_id      = $user->profile_id;

      // Encrypt this datas
      $this->checkConfidential();
      $this->_view            = "$this->_user_last_name $this->_user_first_name";
      $this->_shortview       = "";

      // Initiales du prénom      
      foreach (explode("-", $this->_user_first_name) as $value) {
        if ($value != '') 
          $this->_shortview .= $value[0];
      }
      
      // Initiales du nom
      foreach (explode(" ", $this->_user_last_name) as $value) {
        if ($value != '')
          $this->_shortview .= $value[0];
      } 
      
      $this->_shortview = strtoupper($this->_shortview);
    }
    
    $this->_ref_user = $user;
    $this->updateSpecs();
  }

  function loadRefBanque(){
    $this->_ref_banque = $this->loadFwdRef("banque_id", true);
  }

  function loadRefProfile(){
    $this->_ref_profile = $this->loadFwdRef("_profile_id", true);
  }
  
  function loadRefFunction() {
    $this->_ref_function = $this->loadFwdRef("function_id", true);
    $this->_group_id     = $this->_ref_function->group_id;
  }

  function loadRefDiscipline() {
    $this->_ref_discipline = $this->loadFwdRef("discipline_id", true);
  }
  
  function loadRefSpecCPAM(){
    $this->_ref_spec_cpam = $this->loadFwdRef("spec_cpam_id", true);
  }
  
  function loadRefCodeIntervenantCdARR() {
    $this->_ref_code_intervenant_cdarr = new CIntervenantCdARR();
    $this->_ref_code_intervenant_cdarr->load($this->code_intervenant_cdarr);
  }
  
  function loadRefsFwd() {
    // Forward references
    $this->loadRefFunction();
    $this->loadRefDiscipline();
  }

  function loadRefsBack() {
    $where = array("chir_id" => "= '$this->user_id'");
    $packs = new CPack;
    $this->_ref_packs = $packs->loadList($where);
  }

  function getPerm($permType) {
    global $AppUI;
    $this->loadRefFunction();
    if($this->user_id == $AppUI->user_id) {
      return true;
    }
    else {
    	return CPermObject::getPermObject($this, $permType, $this->_ref_function);
    }
  }

  function loadProtocoles() {
    $where = array("chir_id" => "= '$this->user_id'");
    $protocoles = new CProtocole;
    $this->_ref_protocoles = $protocoles->loadList($where, "libelle_sejour, libelle, codes_ccam");
  }

  /**
   * Lie une numéro de lecture de CPS au Mediuser
   * @return string Store-like message
   */
  function bindCPS() {
    if (null == $intermax = CValue::postOrSessionAbs("intermax")) {
      return;
    }

    // Make id400
    $cps = $intermax["CPS"];
    $cpsNumero = $cps["CPS_NUMERO_LOGICMAX"];
    $id_cps = new CIdSante400();
    $id_cps->object_class = $this->_class_name;
    $id_cps->id400 = $cpsNumero;
    $id_cps->tag = "LogicMax CPSNumero";
    $id_cps->loadMatchingObject();

    // Autre association ?
    if ($id_cps->object_id && $id_cps->object_id != $this->_id) {
      $id_cps->loadTargetObject();
      $medOther =& $id_cps->_ref_object;
      return sprintf("CPS déjà associée à l'utilisateur %s (ADELI: '%s')",
      $medOther->_view,
      $medOther->adeli);
    }

    $id_cps->object_id = $this->_id;
    $id_cps->last_update = mbDateTime();
    return $id_cps->store();
  }

  function loadIdCPS() {
    $id_cps = new CIdSante400();
    if (!$id_cps->_ref_module) {
      return;
    }

    $id_cps->setObject($this);
    $id_cps->tag = "LogicMax CPSNumero";
    $id_cps->loadMatchingObject();
    $this->_id_cps = $id_cps->id400;
  }

  function loadFromIdCPS($numero_cps) {
    // Make id vitale
    $id_cps = new CIdSante400();
    $id_cps->object_class = $this->_class_name;
    $id_cps->id400 = $numero_cps;
    $id_cps->tag = "LogicMax CPSNumero";
    $id_cps->loadMatchingObject();

    // Load patient from found id vitale
    if ($id_cps->object_id) {
      $this->load($id_cps->object_id);
    }
  }

  /**
   * Map les valeurs venant d'une CPS
   * @return void
   */
  function getValuesFromCPS() {
    if (null == $intermax = CValue::postOrSessionAbs("intermax")) {
      return;
    }

    $cps = $intermax["CPS"];

    $this->adeli = $cps["CPS_ADELI_NUMERO_CPS"];
    $this->_user_first_name = $cps["CPS_PRENOM"];
    $this->_user_last_name  = $cps["CPS_NOM"];
  }

  function check() {
    // TODO: voir a fusionner cette fonction avec celle de admin.class.php qui est exactement la meme
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
    } else {
      $this->_user_password = null;
    }

    return parent::check();
  }
   
  function store() {
    $this->updateDBFields();
    $this->updateSpecs();

    if ($msg = $this->check()) {
      return CAppUI::tr($this->_class_name) .
      CAppUI::tr("CMbObject-msg-check-failed") .
      CAppUI::tr($msg);
    }

    // Store corresponding dP user first
    $user = $this->createUser();
    if ($msg = $user->store()) {
      return $msg;
    }

    // User might have been re-created
    if ($this->user_id != $user->user_id) {
      $this->user_id = null;
    }

    // Can't use parent::store cuz user_id don't auto-increment
    if ($this->user_id) {
      $ret = $this->_spec->ds->updateObject($this->_spec->table, $this, $this->_spec->key);
    } else {
      $this->user_id = $user->user_id;
      $ret = $this->_spec->ds->insertObject($this->_spec->table, $this, $this->_spec->key);
    }

    if ($msg = $this->_spec->ds->error()) {
      return $msg;
    }

    // Bind CPS
    if ($this->_bind_cps && $this->_id) {
      return $this->bindCPS();
    }
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

  function loadListFromType($user_types = null, $permType = PERM_READ, $function_id = null, $name = null, $secondary = false) {
    
  	$where = array();
    $ljoin = array();
    
  	if($function_id) {
  		if($secondary) {
  			$ljoin["secondary_function"] = "`users_mediboard`.`user_id` = `secondary_function`.`user_id`";
  			$where[] = "`users_mediboard`.`function_id` = '$function_id' OR `secondary_function`.`function_id` = '$function_id'";
  		} else {
        $where["users_mediboard.function_id"] = "= '$function_id'";
  		}
    }
    
    $where["users_mediboard.actif"] = "= '1'";
    
    // Filters on users values
    $ljoin["users"] = "`users`.`user_id` = `users_mediboard`.`user_id`";

    if ($name) {
      $where["users.user_last_name"] = "LIKE '$name%'";
    }
    
    $ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";
    // Filter on current group
    $g = CGroups::loadCurrent();
    $where["functions_mediboard.group_id"] = "= '$g->_id'";

    $utypes_flip = array_flip(CUser::$types);
    if (is_array($user_types)) {
      foreach ($user_types as $key => $value) {
        $user_types[$key] = $utypes_flip[$value];
      }

      $where["users.user_type"] = CSQLDataSource::prepareIn($user_types);
    }

    $order = "`users`.`user_last_name`, `users`.`user_first_name`";

    // Get all users
    $mediuser = new CMediusers;
    $mediusers = $mediuser->loadListWithPerms($permType, $where, $order, null, null, $ljoin);

    // Associate already loaded function
    foreach ($mediusers as $_mediuser) {
      $_mediuser->loadRefFunction();
    }

    return $mediusers;
  }

  static function loadEtablissements($permType = PERM_READ) {
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
   * Load list overlay for current group
   */
  function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array()) {
    $ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";
    // Filtre sur l'établissement
    $g = CGroups::loadCurrent();
    $where["functions_mediboard.group_id"] = "= '$g->_id'";
    
    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }

  /**
   * Load functions with permissions for given group, current group by default
   * @param $permType perm_constant Level of permission
   * @param $group_id ref|CGroup filter on group
   * @return array<CFunctions> Found functions
   */
  static function loadFonctions($permType = PERM_READ, $group_id = null, $type = null) {
    $group = CGroups::loadCurrent(); 
    $functions = new CFunctions;
    $functions->group_id = CValue::first($group_id, $group->_id);

    if ($type) {
      $functions->type = $type;
    }

    $order = "text";
    $functions = $functions->loadMatchingList($order);

    if ($permType) {
      foreach ($functions as $_id => &$function) {
        if (!$function->getPerm($permType)) {
          unset($functions[$_id]);
        }
        $function->_ref_group = $group;
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

  function loadPraticiens($permType = PERM_READ, $function_id = null, $name = null, $secondary = false) {
    return $this->loadListFromType(array("Chirurgien", "Anesthésiste", "Médecin"), $permType, $function_id, $name, $secondary);
  }

  function loadProfessionnelDeSante($permType = PERM_READ, $function_id = null, $name = null, $secondary = false) {
    return $this->loadListFromType(array("Chirurgien", "Anesthésiste", "Médecin", "Infirmière", "Kinesitherapeute", "Sage Femme"), $permType, $function_id, $name, $secondary);
  }
  
  function loadPersonnels($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Personnel"), $permType, $function_id, $name);
  }

  function loadKines($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Kinesitherapeute"), $permType, $function_id, $name);
  }

  function isFromType($user_types) {
    // Warning: !== operator
    return array_search(@CUser::$types[$this->_user_type], $user_types) !== false;
  }

  /**
   * Check whether user is a pratician
   * @return bool
   */
  function isPraticien () {
    return $this->_is_praticien = $this->isFromType(array("Médecin", "Chirurgien", "Anesthésiste"));
  }

  /**
   * Check whether user is an anesthesist
   * @return bool
   */
  function isAnesth () {
    return $this->_is_anesth = $this->isFromType(array("Anesthésiste"));
  }
  
  /**
   * Check whether user is a nurse
   * @return bool
   */
  function isInfirmiere () {
    return $this->_is_infirmiere = $this->isFromType(array("Infirmière"));
  }
  
  /**
   * Check whether user is a secretary
   * @return bool
   */
  function isSecretaire () {
    return $this->_is_secretaire = $this->isFromType(array("Secrétaire", "Administrator"));
  }
  
  /**
   * Check whether user is a medical user
   * @return bool
   */
  function isMedical() {
    return $this->isFromType(array("Administrator", "Chirurgien", "Anesthésiste", "Infirmière", "Médecin", "Kinesitherapeute", "Sage Femme"));
  }
	
  /**
   * Check whether user is a kine
   * @return bool
   */
  function isKine() {
    return $this->isFromType(array("Kinesitherapeute"));
  }

  function isAdmin() {
    return $this->isFromType(array("Administrator"));
  }

  /**
   * Check whether user is a urgentiste
   * @return bool
   */
  function isUrgentiste () {
    return $this->_is_urgentiste = ($this->function_id == CGroups::loadCurrent()->service_urgences_id);
  }
  
  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_function->fillTemplate($template);
    $template->addProperty("Praticien - nom"       , $this->_user_last_name );
    $template->addProperty("Praticien - prénom"    , $this->_user_first_name);
    $template->addProperty("Praticien - spécialité", $this->_ref_discipline->_view);
    $template->addProperty("Praticien - titres"    , $this->titres);
    $template->addProperty("Praticien - ADELI"     , $this->adeli);
    $template->addProperty("Praticien - RPPS"      , $this->rpps);
  }
  
  /**
   * Charge la liste de plages et interventions pour un jour donné
   * Analogue à CSalle::loadRefsForDay
   * @param $date date Date to look for
   */
  function loadRefsForDay($date) {
    $this->loadBackRefs("secondary_functions");
    $secondary_specs = array();
    foreach($this->_back["secondary_functions"] as  $curr_sec_spec) {
      $curr_sec_spec->loadRefsFwd();
      $curr_function = $curr_sec_spec->_ref_function;
      $secondary_specs[$curr_function->_id] = $curr_function;
    }
    // Plages d'intervention
    $plages = new CPlageOp;
    $where = array();
    $where["date"] = "= '$date'";
    $where[] = "plagesop.chir_id = '$this->_id' OR plagesop.spec_id = '$this->function_id' OR plagesop.spec_id ".CSQLDataSource::prepareIn(array_keys($secondary_specs));
    $order = "debut";
    $this->_ref_plages = $plages->loadList($where, $order);
    foreach ($this->_ref_plages as &$plage) {
      $plage->loadRefs(0);
      $plage->_unordered_operations = array();
      foreach ($plage->_ref_operations as $key_op => &$operation) {
        if($operation->chir_id != $this->_id) {
          unset($plage->_ref_operations[$key_op]);
        } else {
          $operation->_ref_chir = $this;
          $operation->loadRefPatient();
          $operation->loadExtCodesCCAM();
          $operation->updateSalle();
        
          // Extraire les interventions non placées
          if ($operation->rank == 0) {
            $plage->_unordered_operations[$operation->_id] = $operation;
            unset($plage->_ref_operations[$operation->_id]);
          }
        }
      }
    }
    
    // Interventions déplacés
    $deplacees = new COperation;
    $ljoin = array();
    $ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
    $where = array();
    $where["operations.plageop_id"] = "IS NOT NULL";
    $where["operations.annulee"]    = "= '0'";
    $where["plagesop.salle_id"]     = "!= operations.salle_id";
    $where["plagesop.date"]         = "= '$date'";
    $where["plagesop.chir_id"]      = "= '$this->_id'";
    $order = "operations.time_operation";
    $this->_ref_deplacees = $deplacees->loadList($where, $order, null, null, $ljoin);
    foreach ($this->_ref_deplacees as &$deplacee) {
      $deplacee->loadRefChir();
      $deplacee->loadRefPatient();
      $deplacee->loadExtCodesCCAM();
    }

    // Urgences
    $urgences = new COperation;
    $where = array();
    $where["plageop_id"] = "IS NULL";
    $where["date"]       = "= '$date'";
    $where["chir_id"]    = "= '$this->_id'";
    $where["annulee"]    = "= '0'";
    $order = "chir_id";
    $this->_ref_urgences = $urgences->loadList($where);
    foreach($this->_ref_urgences as &$urgence) {
      $urgence->loadRefChir();
      $urgence->loadRefPatient();
      $urgence->loadExtCodesCCAM();
    }
  }
  
  function getBasicInfo(){
    $this->updateFormFields();
    $this->loadRefFunction();
    $this->_ref_function->loadRefGroup();
    return $this->_basic_info = array(
      'id' => $this->_id,
			'guid' => $this->_guid,
      'view' => $this->_view,
      'function' => array(
        'id' => $this->_ref_function->_id,
				'guid' => $this->_ref_function->_guid,
        'view' => $this->_ref_function->_view,
        'color' => $this->_ref_function->color
      ),
      'group' => array(
			  'guid' => $this->_ref_function->_ref_group->_guid,
        'id' => $this->_ref_function->_ref_group->_id,
        'view' => $this->_ref_function->_ref_group->_view,
      )
    );
  }
  
  function makeUsernamePassword($first_name, $last_name, $id = null) {
    $length = 20 - strlen($id);
    $lp = substr(preg_replace("/[^a-z]/i", "", strtolower(CMbString::removeDiacritics(($first_name ? $first_name[0] : '').$last_name))),0,$length) . $id;
    $this->_user_username = $lp;
    $this->_user_password = $lp;
  }
}

?>