<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage mediusers
 *  @version $Revision$
 *  @author Romain Ollivier
 */

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
  var $cps                         = null;
  var $titres                      = null;
  var $commentaires                = null;
  var $actif                       = null;
  var $deb_activite                = null;
  var $fin_activite                = null;
  var $compte                      = null;
  var $banque_id                   = null;
  var $mail_apicrypt               = null;
  var $compta_deleguee             = null;
  var $num_astreinte               = null;  //phone number for astreinte

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
  var $ean   = null;
  var $rcc   = null;
  var $adherent   = null;

  // CUser reported fields fields
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
  var $_is_dentiste                = null;
  var $_is_secretaire              = null;
  var $_is_anesth                  = null;
  var $_is_infirmiere              = null;
  var $_is_aide_soignant           = null;
  var $_user_password_weak         = null;
  var $_user_password_strong       = null;
  var $_basic_info                 = null;
  var $_is_urgentiste              = null;
  var $_force_merge                = false;
  var $_user_id                    = null;
  var $_keep_user                  = null;
  var $_user_type_view             = null;

  // Distant fields
  var $_group_id                   = null;

  // CPS
  var $_bind_cps                   = null;
  var $_id_cps                     = null;

  // Object references
  var $_ref_banque                 = null;
  var $_ref_function               = null;
  var $_ref_spec_cpam              = null;
  var $_ref_discipline             = null;
  var $_ref_profile                = null;
  var $_ref_user                   = null;
  var $_ref_intervenant_cdarr = null;
  var $_ref_protocoles             = array();
  var $_count_protocoles           = null;
  var $_ref_current_functions      = null;

  // Object references per day
  var $_ref_plages                 = null;
  var $_ref_plages_conge        = null;
  var $_ref_urgences               = null;
  var $_ref_deplacees              = null;

  /**
   * Lazy access to a given user, defaultly connected user
   * @param $user_id ref|CMediuser The user id, connected user if null;
   *
   * @return CMediusers
   */
  static function get($user_id = null) {
    // CAppUI::$user is available *after* CAppUI::$instance->_ref_user
    $user = CAppUI::$instance->_ref_user;
    return $user_id ? $user->getCached($user_id) : $user;
  }

  static function loadCurrentFunctions() {
    $user = CMediusers::get();
    $group_id = CGroups::loadCurrent()->_id;
    $secondary_function = new CSecondaryFunction();
    $ljoin = array();
    $where = array();
    $where["group_id"] = "= '$group_id'";
    $where["user_id"]  = "= '$user->_id'";
    $ljoin["functions_mediboard"] = "functions_mediboard.function_id = secondary_function.function_id";

    return $user->_ref_current_functions = $secondary_function->loadList($where, null, null, null, $ljoin);
  }

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'users_mediboard';
    $spec->key   = 'user_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // Note: notamment utile pour les seeks
    // Dans les faits c'est plus logique puisque la classe n'est pas autoincremented
    $props["user_id"]                = "ref class|CUser seekable show|0";

    $props["remote"]                 = "bool default|1 show|0";
    $props["adeli"]                  = "numchar length|9 confidential mask|99S9S99999S9 control|luhn";
    $props["rpps"]                   = "numchar length|11 confidential mask|99999999999 control|luhn";
    $props["cps"]                    = "str";
    $props["function_id"]            = "ref notNull class|CFunctions seekable";
    $props["discipline_id"]          = "ref class|CDiscipline";
    $props["titres"]                 = "text";
    $props["commentaires"]           = "text";
    $props["actif"]                  = "bool default|1";
    $props["deb_activite"]           = "date";
    $props["fin_activite"]           = "date";
    $props["spec_cpam_id"]           = "ref class|CSpecCPAM";
    $props["compte"]                 = "code rib confidential mask|99999S99999S99999999999S99 show|0";
    $props["banque_id"]              = "ref class|CBanque show|0";
    $props["code_intervenant_cdarr"] = "str length|2";
    $props["secteur"]                = "enum list|1|2";
    $props["cab"]                    = "str";
    $props["conv"]                   = "str";
    $props["zisd"]                   = "str";
    $props["ik"]                     = "str";
    $props["ean"]                    = "str";
    $props["rcc"]                    = "str";
    $props["adherent"]               = "str";
    $props["mail_apicrypt"]          = "email";
    $props["compta_deleguee"]        = "bool default|0";
    $props["num_astreinte"]          = "phone confidential";

    $props["_group_id"]              = "ref notNull class|CGroups";

    $props["_user_username"]         = "str notNull minLength|3 reported";
    $props["_user_password2"]        = "password sameAs|_user_password reported";
    $props["_user_first_name"]       = "str reported";
    $props["_user_last_name"]        = "str notNull confidential reported";
    $props["_user_email"]            = "str confidential reported";
    $props["_user_phone"]            = "phone confidential reported";
    $props["_user_adresse"]          = "str confidential reported";
    $props["_user_last_login"]       = "dateTime reported";
    $props["_user_cp"]               = "num length|5 confidential reported";
    $props["_user_ville"]            = "str confidential reported";
    $props["_profile_id"]            = "ref reported class|CUser";
    $props["_user_type"]             = "num notNull min|0 max|20 reported";
    $props["_user_type_view"]        = "str";

    // The different levels of security are stored to be usable in JS
    $props["_user_password_weak"]    = "password minLength|4";
    $props["_user_password_strong"]  = "password minLength|6 notContaining|_user_username notNear|_user_username alphaAndNum";

    $props["_user_password"]         = $props["_user_password_weak"]." reported";

    return $props;
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
    $backProps["actes_tarmed_executes"]           = "CActeTarmed executant_id";
    $backProps["actes_caisse_executes"]           = "CActeCaisse executant_id";
    $backProps["administrations"]                 = "CAdministration administrateur_id";
    $backProps["aides_saisie"]                    = "CAideSaisie user_id";
    $backProps["modeles"]                         = "CCompteRendu user_id";
    $backProps["documents_ged"]                   = "CDocGed user_id";
    $backProps["suivis__ged"]                     = "CDocGedSuivi user_id";
    $backProps["examens"]                         = "CExamenLabo realisateur";
    $backProps["users"]                           = "CFicheEi user_id";
    $backProps["valid_users"]                     = "CFicheEi valid_user_id";
    $backProps["service_valid_users"]             = "CFicheEi service_valid_user_id";
    $backProps["qualite_users"]                   = "CFicheEi qualite_user_id";
    $backProps["owned_files"]                     = "CFile author_id";
    $backProps["forum_messages"]                  = "CForumMessage user_id";
    $backProps["forum_threads"]                   = "CForumThread user_id";
    $backProps["hprim21_medecins"]                = "CHprim21Medecin user_id";
    $backProps["listes_choix"]                    = "CListeChoix user_id";
    $backProps["mails_sent"]                      = "CUserMessage from";
    $backProps["mails_received"]                  = "CUserMessage to";
    $backProps["owned_notes"]                     = "CNote user_id";
    $backProps["observations"]                    = "CObservationMedicale user_id";
    $backProps["operations_chir"]                 = "COperation chir_id";
    $backProps["operations_chir2"]                = "COperation chir_2_id";
    $backProps["operations_chir3"]                = "COperation chir_3_id";
    $backProps["operations_chir4"]                = "COperation chir_4_id";
    $backProps["operations_anesth"]               = "COperation anesth_id";
    $backProps["packs"]                           = "CPack user_id";
    $backProps["prescription_line_mixes"]         = "CPrescriptionLineMix praticien_id";
    $backProps["prescription_line_mixes_0"]       = "CPrescriptionLineMix creator_id";
    $backProps["personnels"]                      = "CPersonnel user_id";
    $backProps["plages_op_chir"]                  = "CPlageOp chir_id";
    $backProps["plages_op_anesth"]                = "CPlageOp anesth_id";
    $backProps["plages_consult"]                  = "CPlageconsult chir_id";
    $backProps["plages_conge"]                    = "CPlageConge user_id";
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
    $backProps["services"]                        = "CService responsable_id";
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
    $backProps["replacements"]                    = "CReplacement replacer_id";
    $backProps["frais_divers"]                    = "CFraisDivers executant_id";
    $backProps["expediteur_ftp"]                  = "CSenderFTP user_id";
    $backProps["expediteur_soap"]                 = "CSenderSOAP user_id";
    $backProps["expediteur_mllp"]                 = "CSenderMLLP user_id";
    $backProps["expediteur_fs"]                   = "CSenderFileSystem user_id";
    $backProps["ufs"]                             = "CAffectationUniteFonctionnelle object_id";
    $backProps["documents_crees"]                 = "CCompteRendu author_id";
    $backProps["devenirs_dentaires"]              = "CDevenirDentaire etudiant_id";
    $backProps["plages_remplacees"]               = "CPlageconsult remplacant_id";
    $backProps["plages_pour_compte_de"]           = "CPlageconsult pour_compte_id";
    $backProps["poses_disp_vasc_operateur"]       = "CPoseDispositifVasculaire operateur_id";
    $backProps["poses_disp_vasc_encadrant"]       = "CPoseDispositifVasculaire encadrant_id";
    $backProps["praticien_facture"]               = "CFactureConsult praticien_id";
    $backProps["tokens"]                          = "CViewAccessToken user_id";

    return $backProps;
  }

  function createUser() {
    $user = new CUser();
    $user->user_id = ($this->_user_id) ? $this->_user_id : $this->user_id;
    $user->user_type        = $this->_user_type;
    $user->user_username    = $this->_user_username;
    if (isset($this->_ldap_store)) {
      $user->user_password     = $this->_user_password;
    }
    else {
      $user->_user_password    = $this->_user_password;
    }
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

    if (!isset($this->_keep_user)) {
      // Delete corresponding dP user first
      if (!$msg = $this->canDeleteEx()) {
        $user = $this->createUser();
        if ($msg = $user->delete()) {
          return $msg;
        }
      }
    }

    $this->_keep_user = null;

    return parent::delete();
  }

  function merge($objects = array/*<CMbObject>*/(), $fast = false) {
    if ($this->_force_merge) {
      return parent::merge($objects, $fast);
    }
    return CAppUI::tr("CMediusers-merge-impossible");
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
      $this->_user_type_view = CValue::read(CUser::$types, $this->_user_type);
    }

    $this->_ref_user = $user;
    $this->updateSpecs();
  }

  function loadView() {
    parent::loadView();
    $this->isPraticien();
    $this->loadRefFunction();
    $this->loadRefSpecCPAM();
    $this->loadRefDiscipline();
    $this->loadNamedFile("identite.jpg");
  }

  /**
   * @return CBanque
   */
  function loadRefBanque() {
    return $this->_ref_banque = $this->loadFwdRef("banque_id", true);
  }

  /**
   * @return CUser
   */
  function loadRefProfile(){
    return $this->_ref_profile = $this->loadFwdRef("_profile_id", true);
  }

  /**
   * @return CFunction
   */
  function loadRefFunction() {
    $this->_ref_function = $this->loadFwdRef("function_id", true);
    $this->_group_id     = $this->_ref_function->group_id;
    return $this->_ref_function;
  }

  function loadRefDiscipline() {
    return $this->_ref_discipline = $this->loadFwdRef("discipline_id", true);
  }

  function loadRefSpecCPAM(){
    return $this->_ref_spec_cpam = $this->loadFwdRef("spec_cpam_id", true);
  }

  function loadRefIntervenantCdARR() {
    return $this->_ref_intervenant_cdarr = CIntervenantCdARR::get($this->code_intervenant_cdarr);
  }

  function loadRefsFwd() {
    $this->loadRefFunction();
    $this->loadRefSpecCPAM();
    $this->loadRefDiscipline();
  }

  function getPerm($permType) {
    if ($this->user_id == CAppUI::$user->_id) {
      return true;
    }

    $this->loadRefFunction();
    return CPermObject::getPermObject($this, $permType, $this->_ref_function);
  }

  function loadProtocoles($type = null) {
    $this->loadRefFunction();
    $functions = array($this->function_id);
    $this->loadBackRefs("secondary_functions");
    foreach ($this->_back["secondary_functions"] as $curr_sec_func) {
      $functions[] = $curr_sec_func->function_id;
    }
    $list_functions = implode(",", $functions);
    $where = array(
      "protocole.chir_id = '$this->_id' OR protocole.function_id IN ($list_functions)"
    );

    if ($type) {
      $where["type"] = "= '$type'";
    }

    $protocole = new CProtocole();
    $this->_ref_protocoles = $protocole->loadList($where, "libelle_sejour, libelle, codes_ccam");
  }

  function countProtocoles($type = null) {
    $this->loadRefFunction();
    $functions = array($this->function_id);
    $this->loadBackRefs("secondary_functions");
    foreach ($this->_back["secondary_functions"] as $curr_sec_func) {
      $functions[] = $curr_sec_func->function_id;
    }
    $list_functions = implode(",", $functions);
    $where = array(
      "protocole.chir_id = '$this->_id' OR protocole.function_id IN ($list_functions)"
    );

    if ($type) {
      $where["type"] = "= '$type'";
    }

    $protocole = new CProtocole();
    $this->_count_protocoles = $protocole->countList($where);
  }

  function getOwners() {
    $func = $this->loadRefFunction();
    $etab = $func->loadRefGroup();
    return array(
      "prat" => $this,
      "func" => $func,
      "etab" => $etab,
    );
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
      if ($target = $pwdSpecs->notContaining) {
        if ($field = $this->$target) {
          if (stristr($pwd, $field)) {
          return "Le mot de passe ne doit pas contenir '$field'";
          }
        }
      }

      // notNear
      if ($target = $pwdSpecs->notNear) {
        if ($field = $this->$target) {
          if (levenshtein($pwd, $field) < 3) {
            return "Le mot de passe ressemble trop à '$field'";
      } } }

      // alphaAndNum
      if ($pwdSpecs->alphaAndNum) {
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

  /**
   * @todo Use CStoredObject->store()
   */
  function store() {
    // Properties checking
    $this->updatePlainFields();

    $this->loadOldObject();

    if (CAppUI::conf("readonly")) {
      return CAppUI::tr($this->_class) .
        CAppUI::tr("CMbObject-msg-store-failed") .
        CAppUI::tr("Mode-readonly-msg");
    }

    if ($msg = $this->check()) {
      return CAppUI::tr($this->_class) .
      CAppUI::tr("CMbObject-msg-check-failed") .
      CAppUI::tr($msg);
    }

    // Trigger before event
    $this->notify("BeforeStore");

    $spec = $this->_spec;

    /// <diff>
      // Store corresponding core user first
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
        $ret = $spec->ds->updateObject($spec->table, $this, $spec->key, $spec->nullifyEmptyStrings);
      }
      else {
        $this->user_id = $user->user_id;
        $keyToUpdate = $spec->incremented ? $spec->key : null;
        $ret = $spec->ds->insertObject($spec->table, $this, $keyToUpdate);
      }
    /// </diff>

    if (!$ret) {
      return CAppUI::tr($this->_class) .
        CAppUI::tr("CMbObject-msg-store-failed") .
        $spec->ds->error();
    }

    /// <diff>
      // Bind CPS
      if ($this->_bind_cps && $this->_id && CModule::getActive("fse")) {
        $cps = CFseFactory::createCPS();
        if ($cps) {
          if ($msg = $cps->bindCPS($this)) {
            return $msg;
          }
        }
      }
    /// </diff>

    // Préparation du log, doit être fait AVANT $this->load()
    $this->prepareLog();

    // Load the object to get all properties
    //$this->load(); // peut poser probleme, à tester

    // Enregistrement du log une fois le store terminé
    $this->doLog();

    // Trigger event
    $this->notify("AfterStore");

    $this->_old = null;
    return null;
  }

  function delFunctionPermission() {
    $where = array();
    $where["user_id"]      = "= '$this->user_id'";
    $where["object_class"] = "= 'CFunctions'";
    $where["object_id"]    = "= '$this->function_id'";

    $perm = new CPermObject;
    if ($perm->loadObject($where)) {
      $perm->delete();
    }
  }

  function insFunctionPermission() {
    $where = array();
    $where["user_id"]      = "= '$this->user_id'";
    $where["object_class"] = "= 'CFunctions'";
    $where["object_id"]    = "= '$this->function_id'";

    $perm = new CPermObject;
    if (!$perm->loadObject($where)) {
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
    if (!$perm->loadObject($where)) {
      $perm = new CPermObject;
      $perm->user_id      = $this->user_id;
      $perm->object_class = "CGroups";
      $perm->object_id    = $function->group_id;
      $perm->permission   = PERM_EDIT;
      $perm->store();
    }
  }

  function loadListFromType($user_types = null, $permType = PERM_READ, $function_id = null, $name = null, $secondary = false, $actif = true, $reverse = false) {

    $where = array();
    $ljoin = array();

    if ($function_id) {
      if ($secondary) {
        $ljoin["secondary_function"] = "`users_mediboard`.`user_id` = `secondary_function`.`user_id`";
        $where[] = "`users_mediboard`.`function_id` = '$function_id' OR `secondary_function`.`function_id` = '$function_id'";
      }
      else {
        $where["users_mediboard.function_id"] = "= '$function_id'";
      }
    }

    if($actif) {
      $where["users_mediboard.actif"] = "= '1'";
    }

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
      if ($reverse) {
        $where["users.user_type"] = CSQLDataSource::prepareNotIn($user_types);
      }
      else {
        $where["users.user_type"] = CSQLDataSource::prepareIn($user_types);
      }
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
    foreach ($groups as $keyGroupe => $groupe) {
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
    $group = CGroups::loadCurrent();
    $where["functions_mediboard.group_id"] = "= '$group->_id'";

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
    $functions->actif = 1;
    $functions->group_id = CValue::first($group_id, $group->_id);

    if ($type) {
      $functions->type = $type;
    }

    $order = "text";
    $functions = $functions->loadMatchingList($order);

    if ($permType && $functions) {
      foreach ($functions as $_id => &$function) {
        if (!$function->getPerm($permType)) {
          unset($functions[$_id]);
        }
        $function->_ref_group = $group;
      }
    }
    return $functions;
  }

  function loadUsers($permType = PERM_READ, $function_id = null, $name = null, $actif = true) {
    return $this->loadListFromType(null, $permType, $function_id, $name, $actif);
  }

  function loadMedecins($permType = PERM_READ, $function_id = null, $name = null, $actif = true) {
    return $this->loadListFromType(array("Médecin"), $permType, $function_id, $name, $actif);
  }

  function loadChirurgiens($permType = PERM_READ, $function_id = null, $name = null, $actif = true) {
    return $this->loadListFromType(array("Chirurgien", "Dentiste"), $permType, $function_id, $name, $actif);
  }

  function loadAnesthesistes($permType = PERM_READ, $function_id = null, $name = null, $actif = true) {
    return $this->loadListFromType(array("Anesthésiste"), $permType, $function_id, $name, $actif);
  }

  function loadPraticiens($permType = PERM_READ, $function_id = null, $name = null, $secondary = false, $actif = true) {
    return $this->loadListFromType(array("Chirurgien", "Anesthésiste", "Médecin", "Dentiste"), $permType, $function_id, $name, $secondary, $actif);
  }

  function loadProfessionnelDeSante($permType = PERM_READ, $function_id = null, $name = null, $secondary = false, $actif = true) {
    return $this->loadListFromType(array("Chirurgien", "Anesthésiste", "Médecin", "Infirmière", "Rééducateur", "Sage Femme", "Dentiste"), $permType, $function_id, $name, $secondary, $actif);
  }

  function loadNonProfessionnelDeSante($permType = PERM_READ, $function_id = null, $name = null, $secondary = false, $actif = true) {
    return $this->loadListFromType(array("Chirurgien", "Anesthésiste", "Médecin", "Infirmière", "Rééducateur", "Sage Femme", "Dentiste"), $permType, $function_id, $name, $secondary, $actif, true);
  }
  function loadPraticiensCompta(){
    $is_admin      = in_array(CUser::$types[$this->_user_type], array("Administrator"));
    $is_secretaire = in_array(CUser::$types[$this->_user_type], array("Secrétaire"));
    $is_directeur  = in_array(CUser::$types[$this->_user_type], array("Directeur"));
    $listPrat = array();
    $this->loadRefFunction();
    // Liste des praticiens du cabinet
    if ($is_admin || $is_secretaire || $is_directeur || $this->_ref_function->compta_partagee) {
      $function = null;
      if (!CAppUI::conf("dPcabinet Comptabilite show_compta_tiers") && $this->_user_username != "admin") {
        $function = $this->function_id;
      }

      if ($is_admin) {
        if (CAppUI::pref("pratOnlyForConsult", 1)) {
          $listPrat = $this->loadPraticiens(PERM_EDIT, $function);
        }
        else {
          $listPrat = $this->loadProfessionnelDeSante(PERM_EDIT, $function);
        }
      }
      else {
        if (CAppUI::pref("pratOnlyForConsult", 1)) {
          $listPrat = $this->loadPraticiens(PERM_EDIT, $this->function_id);
        }
        else {
          $listPrat = $this->loadProfessionnelDeSante(PERM_EDIT, $this->function_id);
        }
        // On ajoute les praticiens qui ont délégués leurs compta
        $where = array();
        $where[] = "users_mediboard.compta_deleguee = '1' ||  users_mediboard.user_id ". CSQLDataSource::prepareIn(array_keys($listPrat));
        // Filters on users values
        $where["users_mediboard.actif"] = "= '1'";

        $ljoin["users"] = "users.user_id = users_mediboard.user_id";
        $order = "users.user_last_name, users.user_first_name";

        $mediuser = new CMediusers();
        // les praticiens WithPerms sont déjà chargés
        // $mediusers = $mediuser->loadListWithPerms(PERM_EDIT, $where, $order, null, null, $ljoin);
        $mediusers = $mediuser->loadList($where, $order, null, null, $ljoin);

        // Associate already loaded function
        foreach ($mediusers as $key => $_mediuser) {
          $_mediuser->loadRefFunction();
        }
        $listPrat = $mediusers;
      }
    }
    else if ($this->isPraticien() && !$this->compta_deleguee) {
      $listPrat = array($this->_id => $this);
    }

    return $listPrat;
  }

  function loadPersonnels($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Personnel"), $permType, $function_id, $name);
  }

  function loadKines($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Rééducateur"), $permType, $function_id, $name);
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
    return $this->_is_praticien = $this->isFromType(array("Médecin", "Chirurgien", "Anesthésiste", "Dentiste"));
  }

  /**
   * Check whether user is an anesthesist
   * @return bool
   */
  function isAnesth () {
    return $this->_is_anesth = $this->isFromType(array("Anesthésiste"));
  }

  /**
   * Check whether user is a dentist
   * @return
   */
  function isDentiste () {
    return $this->_is_dentiste = $this->isFromType(array("Dentiste"));
  }

  /**
   * Check whether user is a nurse
   * @return bool
   */
  function isInfirmiere () {
    return $this->_is_infirmiere = $this->isFromType(array("Infirmière"));
  }

  function isAideSoignant () {
    return $this->_is_aide_soignant = $this->isFromType(array("Aide soignant"));
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
    return $this->isFromType(array("Administrator", "Chirurgien", "Anesthésiste", "Infirmière", "Médecin", "Rééducateur", "Sage Femme", "Dentiste"));
  }

  function isExecutantPrescription() {
    return $this->isFromType(array("Infirmière", "Aide soignant", "Rééducateur"));
  }

  /**
   * Check whether user is a kine
   * @return bool
   */
  function isKine() {
    return $this->isFromType(array("Rééducateur"));
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
    $template->addProperty("Praticien - Nom"            , $this->_user_last_name );
    $template->addProperty("Praticien - Prénom"         , $this->_user_first_name);
    $template->addProperty("Praticien - Initiales"      , $this->_shortview);
    $template->addProperty("Praticien - Spécialité"     , $this->_ref_discipline->_view);
    $template->addProperty("Praticien - Titres"         , $this->titres);
    $template->addProperty("Praticien - ADELI"          , $this->adeli);
    $template->addBarcode("Praticien - Code barre ADELI", $this->adeli, array("barcode" => array(
      "title" => CAppUI::tr("{$this->_class}-adeli")
    )));
    $template->addProperty("Praticien - RPPS"           , $this->rpps);
    $template->addBarcode("Praticien - Code barre RPPS" , $this->rpps, array("barcode" => array(
      "title" => CAppUI::tr("{$this->_class}-rpps")
    )));
    $template->addProperty("Praticien - E-mail"         , $this->_user_email);
    $template->addProperty("Praticien - E-mail Apicrypt", $this->mail_apicrypt);

    // Identité
    $identite = $this->loadNamedFile("identite.jpg");
    $template->addImageProperty("Praticien - Photo d'identite", $identite->_id);

    // Signature
    $signature = $this->loadNamedFile("signature.jpg");
    $template->addImageProperty("Praticien - Signature", $signature->_id);
  }

  /**
   * Charge la liste de plages et interventions pour un jour donné
   * Analogue à CSalle::loadRefsForDay
   * @param $date date Date to look for
   */
  function loadRefsForDay($date) {
    $this->loadBackRefs("secondary_functions");
    $secondary_specs = array();
    foreach ($this->_back["secondary_functions"] as  $curr_sec_spec) {
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
        if ($operation->chir_id != $this->_id) {
          unset($plage->_ref_operations[$key_op]);
        }
        else {
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
    foreach ($this->_ref_urgences as &$urgence) {
      $urgence->loadRefChir();
      $urgence->loadRefPatient();
      $urgence->loadExtCodesCCAM();
    }
  }

  function getBasicInfo(){
    $this->updateFormFields();
    $this->loadRefFunction()->loadRefGroup();
    return $this->_basic_info = array (
      'id'    => $this->_id,
      'guid'  => $this->_guid,
      'view'  => $this->_view,
      'login' => $this->_user_username,
      'function' => array (
        'id' => $this->_ref_function->_id,
        'guid'  => $this->_ref_function->_guid,
        'view'  => $this->_ref_function->_view,
        'color' => $this->_ref_function->color
      ),
      'group' => array (
        'guid' => $this->_ref_function->_ref_group->_guid,
        'id'   => $this->_ref_function->_ref_group->_id,
        'view' => $this->_ref_function->_ref_group->_view,
      )
    );
  }

  function makeUsernamePassword($first_name, $last_name, $id = null, $number = false, $prepass = "mdp") {
    $length = 20 - strlen($id);
    $this->_user_username = substr(preg_replace($number ? "/[^a-z0-9]/i" : "/[^a-z]/i", "", strtolower(CMbString::removeDiacritics(($first_name ? $first_name[0] : '').$last_name))),0,$length) . $id;
    $this->_user_password = $prepass . substr(preg_replace($number ? "/[^a-z0-9]/i" : "/[^a-z]/i", "", strtolower(CMbString::removeDiacritics($last_name))),0,$length) . $id;
  }

  function getNbJoursPlanningSSR($date){
    $sunday = mbDate("next sunday", mbDate("- 1 DAY", $date));
    $saturday = mbDate("-1 DAY", $sunday);

    $_evt = new CEvenementSSR();
    $where = array();
    $where["debut"] = "BETWEEN '$sunday 00:00:00' AND '$sunday 23:59:59'";
    $where["sejour_id"] = " = '$this->_id'";
    $count_event_sunday = $_evt->countList($where);

    $nb_days = 7;

    // Si aucun evenement le dimanche
    if (!$count_event_sunday) {
      $nb_days = 6;
      $where["debut"] = "BETWEEN '$saturday 00:00:00' AND '$saturday 23:59:59'";
      $count_event_saturday= $_evt->countList($where);
      // Aucun evenement le samedi et aucun le dimanche
      if (!$count_event_saturday) {
        $nb_days = 5;
      }
    }
    return $nb_days;
  }

  function getAutocompleteList($keywords, $where = null, $limit = null, $ljoin= null) {
    $ljoin = array_merge($ljoin, array("users" => "users.user_id = users_mediboard.user_id"));
    $list = $this->seek($keywords, $where, $limit, null, $ljoin, "users.user_last_name");

    foreach ($list as $_mediuser) {
      $_mediuser->loadRefFunction();
    }

    return $list;
  }

  /**
   * Construit le tag Mediusers en fonction des variables de configuration
   * @param $group_id Permet de charger l'id externe d'un Mediuser pour un établissement donné si non null
   * @return string
   */
  static function getTagMediusers($group_id = null) {
    // Pas de tag Mediusers
    if (null == $tag_mediusers = CAppUI::conf("mediusers tag_mediuser")) {
      return;
    }

    // Permettre des id externes en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }

    return str_replace('$g', $group_id, $tag_mediusers);
  }
}

?>