<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage mediusers
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The CMediusers class
 */
class CMediusers extends CPerson {
  public $user_id;

  // DB Fields
  public $remote;
  public $adeli;
  public $rpps;
  public $cps;
  public $titres;
  public $initials;
  public $commentaires;
  public $actif;
  public $deb_activite;
  public $fin_activite;
  public $compte;
  public $banque_id;
  public $mail_apicrypt;
  public $compta_deleguee;
  public $last_ldap_checkout;

  // DB References
  public $function_id;
  public $discipline_id;
  public $spec_cpam_id;

  public $code_intervenant_cdarr;

  public $secteur;
  // Champs utilisés pour l'affichage des ordonnances ALD
  public $cab;
  public $conv;
  public $zisd;
  public $ik;
  public $ean;
  public $rcc;
  public $adherent;
  public $debut_bvr;

  // CUser reported fields fields
  public $_user_type;
  public $_user_username;
  public $_user_password;
  public $_user_password2;
  public $_user_first_name;
  public $_user_last_name;
  public $_user_email;
  public $_user_phone;
  public $_user_astreinte;
  public $_user_adresse;
  public $_user_cp;
  public $_user_ville;
  public $_user_last_login;
  public $_user_template;

  // Other fields
  public $_profile_id;
  public $_is_praticien;
  public $_is_dentiste;
  public $_is_secretaire;
  public $_is_anesth;
  public $_is_infirmiere;
  public $_is_aide_soignant;
  public $_is_pharmacien;
  public $_user_password_weak;
  public $_user_password_strong;
  public $_basic_info;
  public $_is_urgentiste;
  public $_force_merge = false;
  public $_user_id;
  public $_keep_user;
  public $_user_type_view;

  // Distant fields
  public $_group_id;

  // Behaviour fields
  static $user_autoload = true;
  public $_bind_cps;
  public $_id_cps;

  /** @var CBanque */
  public $_ref_banque;

  /** @var CFunctions */
  public $_ref_function;

  /** @var CSpecCPAM */
  public $_ref_spec_cpam;

  /** @var CDiscipline */
  public $_ref_discipline;

  /** @var CUser */
  public $_ref_profile;

  /** @var CUser*/
  public $_ref_user;

  /** @var CIntervenantCdARR */
  public $_ref_intervenant_cdarr;

  /** @var CProtocole[] */
  public $_ref_protocoles = array();
  public $_count_protocoles;

  /** @var CFunctions[] */
  public $_ref_current_functions;

  /** @var CPlageOp[] */
  public $_ref_plages;

  /** @var CPlageConge[] */
  public $_ref_plages_conge;

  /** @var COperation[] */
  public $_ref_urgences;

  /** @var COperation[] */
  public $_ref_deplacees;

  /** @var CSourcePOP[] */
  public $_refs_source_pop;
  
  /** @var CRetrocession[] */
  public $_ref_retrocessions;

  /**
   * Lazy access to a given user, defaultly connected user
   *
   * @param integer $user_id The user id, connected user if null
   *
   * @return self
   */
  static function get($user_id = null) {
    if ($user_id) {
      $user = new self;
      return $user->getCached($user_id);
    }

    // CAppUI::$user is available *after* CAppUI::$instance->_ref_user
    return CAppUI::$instance->_ref_user;
  }

  /**
   * @return CFunctions[]
   */
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

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'users_mediboard';
    $spec->key   = 'user_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
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
    $props["initials"]               = "str";
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
    $props["debut_bvr"]              = "str maxLength|10";
    $props["mail_apicrypt"]          = "email";
    $props["compta_deleguee"]        = "bool default|0";
    $props["last_ldap_checkout"]     = "date";

    $props["_group_id"]              = "ref notNull class|CGroups";

    $props["_user_username"]         = "str notNull minLength|3 reported";
    $props["_user_password2"]        = "password sameAs|_user_password reported";
    $props["_user_first_name"]       = "str reported";
    $props["_user_last_name"]        = "str notNull confidential reported";
    $props["_user_email"]            = "str confidential reported";
    $props["_user_phone"]            = "phone confidential reported";
    $props["_user_astreinte"]        = "phone confidential reported";
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

  /**
   * Update the object's specs
   */
  function updateSpecs() {
    $oldSpec = $this->_specs['_user_password'];

    // Determine if password length is sufficient
    $strongPassword = ((CAppUI::conf("admin CUser strong_password") == "1")
      && (($this->remote == 0) || CAppUI::conf("admin CUser apply_all_users")));

    // If the global strong password config is set to TRUE
    $this->_specs['_user_password'] = $strongPassword?
      $this->_specs['_user_password_strong']:
      $this->_specs['_user_password_weak'];

    $this->_specs['_user_password']->fieldName = $oldSpec->fieldName;

    $this->_props['_user_password'] = $strongPassword?
      $this->_props['_user_password_strong']:
      $this->_props['_user_password_weak'];
  }

  /**
   * @see parent::getBackProps()
   */
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
    $backProps["praticien_facture_cabinet"]       = "CFactureCabinet praticien_id";
    $backProps["praticien_facture_etab"]          = "CFactureEtablissement praticien_id";
    $backProps["tokens"]                          = "CViewAccessToken user_id";
    $backProps["etapes_didacticiel"]              = "CEtapeDidacticiel user_id";
    $backProps["astreintes"]                      = "CPlageAstreinte user_id";
    $backProps["dicom_sender"]                    = "CDicomSender user_id";
    $backProps["CPS_pyxvital"]                    = "CPvCPS id_mediuser";
    $backProps["affectation"]                     = "CAffectation praticien_id";
    $backProps["regles_sectorisation_mediuser"]   = "CRegleSectorisation praticien_id";
    $backProps["retrocession"]                    = "CRetrocession praticien_id";
    return $backProps;
  }

  /**
   * @return CUser
   */
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
    $user->user_astreinte   = $this->_user_astreinte;
    $user->user_address1    = $this->_user_adresse;
    $user->user_zip         = $this->_user_cp;
    $user->user_city        = $this->_user_ville;
    $user->profile_id       = $this->_profile_id;
    $user->template         = 0;

    $user->_merging = $this->_merging;
    return $user;
  }

  /**
   * @see parent::delete()
   */
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

  /**
   * @see parent::merge()
   */
  function merge($objects = array/*<CMbObject>*/(), $fast = false) {
    if ($this->_force_merge) {
      return parent::merge($objects, $fast);
    }
    return CAppUI::tr("CMediusers-merge-impossible");
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefUser();
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();
    $this->isPraticien();
    $this->loadRefFunction();
    $this->loadRefSpecCPAM();
    $this->loadRefDiscipline();
    $this->loadNamedFile("identite.jpg");
  }

  /**
   * Chargement de l'utilisateur système
   *
   * @return CUser
   */
  function loadRefUser() {
    $user = new CUser();

    // Usefull hack for mass preloading
    if (self::$user_autoload) {
      $user = $user->getCached($this->user_id);
    }

    if ($user->_id) {
      $this->_user_type       = $user->user_type;
      $this->_user_username   = $user->user_username;
      $this->_user_password   = $user->user_password;
      $this->_user_first_name = CMbString::capitalize($user->user_first_name);
      $this->_user_last_name  = CMbString::upper($user->user_last_name);
      $this->_user_email      = $user->user_email;
      $this->_user_phone      = $user->user_phone;
      $this->_user_astreinte  = $user->user_astreinte;
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

      // Initiales
      if (!$this->_shortview = $this->initials) {
        foreach (explode("-", $this->_user_first_name) as $value) {
          if ($value != '') {
            $this->_shortview .= $value[0];
          }
        }

        // Initiales du nom
        foreach (explode(" ", $this->_user_last_name) as $value) {
          if ($value != '') {
            $this->_shortview .= $value[0];
          }
        }
      }
      $this->_shortview = strtoupper($this->_shortview);
      $this->_user_type_view = CValue::read(CUser::$types, $this->_user_type);
    }

    $this->_ref_user = $user;

    $this->mapPerson();

    $this->updateSpecs();
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
   * @return CFunctions
   */
  function loadRefFunction() {
    $this->_ref_function = $this->loadFwdRef("function_id", true);
    $this->_group_id     = $this->_ref_function->group_id;
    return $this->_ref_function;
  }

  /**
   * @return CDiscipline
   */
  function loadRefDiscipline() {
    return $this->_ref_discipline = $this->loadFwdRef("discipline_id", true);
  }

  /**
   * @return CSpecCPAM
   */
  function loadRefSpecCPAM(){
    return $this->_ref_spec_cpam = $this->loadFwdRef("spec_cpam_id", true);
  }

  /**
   * @return CIntervenantCdARR
   */
  function loadRefIntervenantCdARR() {
    return $this->_ref_intervenant_cdarr = CIntervenantCdARR::get($this->code_intervenant_cdarr);
  }

  /**
   * @see parent::loadRefsFwd()
   * @deprecated
   */
  function loadRefsFwd() {
    $this->loadRefFunction();
    $this->loadRefSpecCPAM();
    $this->loadRefDiscipline();
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if ($this->user_id == CAppUI::$user->_id) {
      return true;
    }

    $this->loadRefFunction();
    return CPermObject::getPermObject($this, $permType, $this->_ref_function);
  }

  /**
   * @return CProtocole[]
   */
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

  /**
   * @return CMbObject[]
   */
  function getOwners() {
    $func = $this->loadRefFunction();
    $etab = $func->loadRefGroup();
    return array(
      "prat" => $this,
      "func" => $func,
      "etab" => $etab,
    );
  }

  /**
   * @see parent::check()
   */
  function check() {
    // TODO: voir a fusionner cette fonction avec celle de admin.class.php qui est exactement la meme
    // Chargement des specs des attributs du mediuser
    $this->updateSpecs();

    $specs = $this->getSpecs();

    // On se concentre dur le mot de passe (_user_password)
    $pwdSpecs = $specs['_user_password'];

    $pwd = $this->_user_password;

    // S'il a été défini, on le contréle (necessaire de le mettre ici a cause du md5)
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
          }
        }
      }

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

    if ($this->fieldModified("remote", 0)) {
      if (!$this->_user_password) {
        return "Veuillez saisir à nouveau votre mot de passe";
      }
    }

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

  /**
   * @param array  $user_types
   * @param int    $permType
   * @param int    $function_id
   * @param string $name
   * @param bool   $secondary
   * @param bool   $actif
   * @param bool   $reverse
   *
   * @return CMediusers[]
   */
  function loadListFromType($user_types = null, $permType = PERM_READ, $function_id = null, $name = null, $actif = true, $secondary = false, $reverse = false) {
    $where = array();
    $ljoin = array();

    if ($actif) {
      $where["users_mediboard.actif"] = "= '1'";
    }

    // Filters on users values
    $ljoin["users"] = "`users`.`user_id` = `users_mediboard`.`user_id`";

    if ($name) {
      $where["users.user_last_name"] = "LIKE '$name%'";
    }

    $ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";
    $ljoin["secondary_function"] = "secondary_function.user_id = users_mediboard.user_id";
    $ljoin[] = "functions_mediboard AS sec_fnc_mb ON sec_fnc_mb.function_id = secondary_function.function_id";

    if ($function_id) {
      if ($secondary) {
        $where[] = "'$function_id' IN (users_mediboard.function_id, secondary_function.function_id)";
      }
      else {
        $where["users_mediboard.function_id"] = "= '$function_id'";
      }
    }

    // Filter on current group or users in secondaries functions
    $group = CGroups::loadCurrent();
    $where[] = "functions_mediboard.group_id = '$group->_id' OR sec_fnc_mb.group_id = '$group->_id'";

    // Filter on user type
    if (is_array($user_types)) {
      $utypes_flip = array_flip(CUser::$types);
      foreach ($user_types as &$_type) {
        $_type = $utypes_flip[$_type];
      }

      $where["users.user_type"] = $reverse ?
        CSQLDataSource::prepareNotIn($user_types) :
        CSQLDataSource::prepareIn($user_types);
    }

    $order = "`users`.`user_last_name`, `users`.`user_first_name`";

    // Get all users
    $mediuser = new CMediusers;
    CMediusers::$user_autoload = false;
    $mediusers = $mediuser->loadList($where, $order, null, null, $ljoin);
    CMediusers::$user_autoload = true;

    // Mass user speficic preloading
    $user = new CUser;
    $user->loadAll(array_keys($mediusers));

    // Attach cached user
    foreach ($mediusers as $_mediuser) {
      $_mediuser->updateFormFields();
    }

    // Mass fonction standard preloading
    CMbObject::massLoadFwdRef($mediusers, "function_id");

    // Filter a posteriori to unable mass preloading of function
    self::filterByPerm($mediusers, $permType);

    // Associate cached function
    foreach ($mediusers as $_mediuser) {
      $_mediuser->loadRefFunction();
    }

    return $mediusers;
  }

  /**
   * @param int $permType
   *
   * @return CGroups[]
   */
  static function loadEtablissements($permType = PERM_READ) {
    // Liste de Tous les établissements
    $group = new CGroups;
    $order = "text";
    return $group->loadListWithPerms($permType, null, $order);
  }

  /**
   * Load list overlay for current group
   *
   * @return self[]
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
   *
   * @param int    $permType Level of permission
   * @param int    $group_id Filter on group
   * @param string $type     Type of function
   *
   * @return CFunctions[] Found functions
   */
  static function loadFonctions($permType = PERM_READ, $group_id = null, $type = null) {
    $group = CGroups::loadCurrent();

    $function = new CFunctions();
    $function->actif = 1;
    $function->group_id = CValue::first($group_id, $group->_id);

    if ($type) {
      $function->type = $type;
    }

    $order = "text";

    /** @var CFunctions[] $functions */
    $functions = $function->loadMatchingList($order);
    CMbObject::filterByPerm($functions, $permType);

    // Group association
    foreach ($functions as $function) {
      $function->_ref_group = $group;
    }

    return $functions;
  }

  /**
   * @param int  $permType
   * @param null $function_id
   * @param null $name
   * @param bool $actif
   *
   * @return CMediusers[]
   */
  function loadUsers($permType = PERM_READ, $function_id = null, $name = null, $actif = true) {
    return $this->loadListFromType(null, $permType, $function_id, $name, $actif);
  }

  /**
   * @param int  $permType
   * @param null $function_id
   * @param null $name
   * @param bool $actif
   *
   * @return CMediusers[]
   */
  function loadMedecins($permType = PERM_READ, $function_id = null, $name = null, $actif = true) {
    return $this->loadListFromType(array("Médecin"), $permType, $function_id, $name, $actif);
  }

  /**
   * @param int  $permType
   * @param null $function_id
   * @param null $name
   * @param bool $actif
   *
   * @return CMediusers[]
   */
  function loadChirurgiens($permType = PERM_READ, $function_id = null, $name = null, $actif = true) {
    return $this->loadListFromType(array("Chirurgien", "Dentiste"), $permType, $function_id, $name, $actif);
  }

  /**
   * @param int  $permType
   * @param null $function_id
   * @param null $name
   * @param bool $actif
   *
   * @return CMediusers[]
   */
  function loadAnesthesistes($permType = PERM_READ, $function_id = null, $name = null, $actif = true) {
    return $this->loadListFromType(array("Anesthésiste"), $permType, $function_id, $name, $actif);
  }

  /**
   * @param int  $permType
   * @param null $function_id
   * @param null $name
   * @param bool $secondary
   * @param bool $actif
   *
   * @return CMediusers[]
   */
  function loadPraticiens($permType = PERM_READ, $function_id = null, $name = null, $secondary = false, $actif = true) {
    return $this->loadListFromType(array("Chirurgien", "Anesthésiste", "Médecin", "Dentiste"), $permType, $function_id, $name, $actif, $secondary);
  }

  /**
   * @param int  $permType
   * @param null $function_id
   * @param null $name
   * @param bool $secondary
   * @param bool $actif
   *
   * @return CMediusers[]
   */
  function loadProfessionnelDeSante($permType = PERM_READ, $function_id = null, $name = null, $secondary = false, $actif = true) {
    return $this->loadListFromType(array("Chirurgien", "Anesthésiste", "Médecin", "Infirmière", "Rééducateur", "Sage Femme", "Dentiste"), $permType, $function_id, $name, $actif, $secondary);
  }

  /**
   * @param int  $permType
   * @param null $function_id
   * @param null $name
   * @param bool $secondary
   * @param bool $actif
   *
   * @return CMediusers[]
   */
  function loadNonProfessionnelDeSante($permType = PERM_READ, $function_id = null, $name = null, $secondary = false, $actif = true) {
    return $this->loadListFromType(array("Chirurgien", "Anesthésiste", "Médecin", "Infirmière", "Rééducateur", "Sage Femme", "Dentiste"), $permType, $function_id, $name, $secondary, $actif, true);
  }

  /**
   * @return CMediusers[]
   */
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
        foreach ($mediusers as $_mediuser) {
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

  /**
   * @param int  $permType
   * @param null $function_id
   * @param null $name
   *
   * @return CMediusers[]
   */
  function loadPersonnels($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Personnel"), $permType, $function_id, $name);
  }

  /**
   * @param int  $permType
   * @param null $function_id
   * @param null $name
   *
   * @return CMediusers[]
   */
  function loadKines($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Rééducateur"), $permType, $function_id, $name);
  }

  function isFromType($user_types) {
    // Warning: !== operator
    return array_search(@CUser::$types[$this->_user_type], $user_types) !== false;
  }

  /**
   * Check whether user is a pratician
   *
   * @return bool
   */
  function isPraticien() {
    return $this->_is_praticien = $this->isFromType(array("Médecin", "Chirurgien", "Anesthésiste", "Dentiste"));
  }

  /**
   * Check whether user is an anesthesist
   *
   * @return bool
   */
  function isAnesth() {
    return $this->_is_anesth = $this->isFromType(array("Anesthésiste"));
  }

  /**
   * Check whether user is an pharmacist
   *
   * @return bool
   */
  function isPharmacien() {
    return $this->_is_pharmacien = $this->isFromType(array("Pharmacien"));
  }

  /**
   * Check whether user is a dentist
   *
   * @return bool
   */
  function isDentiste() {
    return $this->_is_dentiste = $this->isFromType(array("Dentiste"));
  }

  /**
   * Check whether user is a nurse
   *
   * @return bool
   */
  function isInfirmiere() {
    return $this->_is_infirmiere = $this->isFromType(array("Infirmière"));
  }

  /**
   * @return bool
   */
  function isAideSoignant() {
    return $this->_is_aide_soignant = $this->isFromType(array("Aide soignant"));
  }

  /**
   * Check whether user is a secretary
   *
   * @return bool
   */
  function isSecretaire () {
    return $this->_is_secretaire = $this->isFromType(array("Secrétaire", "Administrator"));
  }


  /**
   * Check whether user is a medical user
   *
   * @return bool
   */
  function isMedical() {
    return $this->isFromType(array("Administrator", "Chirurgien", "Anesthésiste", "Infirmière", "Médecin", "Rééducateur", "Sage Femme", "Dentiste"));
  }

  /**
   * @return bool
   */
  function isExecutantPrescription() {
    return $this->isFromType(array("Infirmière", "Aide soignant", "Rééducateur"));
  }

  /**
   * Check whether user is a kine
   *
   * @return bool
   */
  function isKine() {
    return $this->isFromType(array("Rééducateur"));
  }

  /**
   * @return bool
   */
  function isAdmin() {
    return $this->isFromType(array("Administrator"));
  }

  /**
   * Check whether user is a urgentiste
   *
   * @return bool
   */
  function isUrgentiste () {
    return $this->_is_urgentiste = ($this->function_id == CGroups::loadCurrent()->service_urgences_id);
  }

  /**
   * load the list of POP account
   *
   * @return CStoredObject[]
   */
  function loadRefsSourcePop() {
    return $this->_refs_source_pop = $this->loadBackRefs("sources_pop");
  }

  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_function->fillTemplate($template);
    $template->addProperty("Praticien - Nom"            , $this->_user_last_name );
    $template->addProperty("Praticien - Prénom"         , $this->_user_first_name);
    $template->addProperty("Praticien - Initiales"      , $this->_shortview);
    $template->addProperty("Praticien - Discipline"     , $this->_ref_discipline->_view);
    $template->addProperty("Praticien - Spécialité"     , $this->_ref_spec_cpam->_view);
    
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
   *
   * @param string $date Date to look for
   */
  function loadRefsForDay($date) {
    $this->loadBackRefs("secondary_functions");
    $secondary_specs = array();
    foreach ($this->_back["secondary_functions"] as $curr_sec_spec) {
      $curr_sec_spec->loadRefsFwd();
      $curr_function = $curr_sec_spec->_ref_function;
      $secondary_specs[$curr_function->_id] = $curr_function;
    }
    // Plages d'intervention
    $plages = new CPlageOp();
    $where = array();
    $where["date"] = "= '$date'";
    $where[] = "plagesop.chir_id = '$this->_id' OR plagesop.spec_id = '$this->function_id' OR plagesop.spec_id ".CSQLDataSource::prepareIn(array_keys($secondary_specs));
    $order = "debut";
    $this->_ref_plages = $plages->loadList($where, $order);
    foreach ($this->_ref_plages as $plage) {
      /** @var CPlageOp $plage */
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
    $deplacees = new COperation();
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
    foreach ($this->_ref_deplacees as $deplacee) {
      /** @var COperation $deplacee */
      $deplacee->loadRefChir();
      $deplacee->loadRefPatient();
      $deplacee->loadExtCodesCCAM();
    }

    // Urgences
    $urgences = new COperation();
    $where = array();
    $where["plageop_id"] = "IS NULL";
    $where["date"]       = "= '$date'";
    $where["chir_id"]    = "= '$this->_id'";
    $where["annulee"]    = "= '0'";

    $this->_ref_urgences = $urgences->loadList($where);
    foreach ($this->_ref_urgences as $urgence) {
      /** @var COperation $urgence */
      $urgence->loadRefChir();
      $urgence->loadRefPatient();
      $urgence->loadExtCodesCCAM();
    }
  }

  /**
   * Builds a structure containing basic information about the user, to be used in JS in window.User
   *
   * @return array|null
   */
  function getBasicInfo(){
    if (!$this->_ref_module) {
      return null;
    }

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
    $sunday = CMbDT::date("next sunday", CMbDT::date("- 1 DAY", $date));
    $saturday = CMbDT::date("-1 DAY", $sunday);

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

  /**
   * @param string $keywords
   * @param null   $where
   * @param null   $limit
   * @param null   $ljoin
   *
   * @return self[]
   */
  function getAutocompleteList($keywords, $where = null, $limit = null, $ljoin= null) {
    $ljoin = array_merge($ljoin, array("users" => "users.user_id = users_mediboard.user_id"));
    $list = $this->seek($keywords, $where, $limit, null, $ljoin, "users.user_last_name");

    foreach ($list as $_mediuser) {
      $_mediuser->loadRefFunction();
    }

    return $list;
  }

  /**
   * Return idex type if it's special (e.g. software/...)
   *
   * @param CIdSante400 $idex Idex
   *
   * @return string|null
   */
  function getSpecialIdex(CIdSante400 $idex) {
    //identifier les comptes de type logiciel
    if ($idex->tag == self::getTagSoftware()) {
      return "software";
    }
    return null;
  }

  /**
   * return the tag used for identifying "software user"
   *
   * @param null $group_id
   * @return mixed
   */
  static function getTagSoftware($group_id = null) {
    // Pas de tag Mediusers
    if (null == $tag_mediusers_software = CAppUI::conf("mediusers tag_mediuser_software")) {
      return null;
    }

    // Permettre des id externes en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }

    return str_replace('$g', $group_id, $tag_mediusers_software);
  }

  /**
   * Construit le tag Mediusers en fonction des variables de configuration
   *
   * @param int $group_id Permet de charger l'id externe d'un Mediuser pour un établissement donné si non null
   *
   * @return string
   */
  static function getTagMediusers($group_id = null) {
    // Pas de tag Mediusers
    if (null == $tag_mediusers = CAppUI::conf("mediusers tag_mediuser")) {
      return null;
    }

    // Permettre des id externes en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }

    return str_replace('$g', $group_id, $tag_mediusers);
  }

  function isRobot() {
    return (CIdSante400::getMatch($this->_class, CMediusers::getTagSoftware(), null, $this->_id)->_id != null);
  }

  /**
   * Map the class variable with CPerson variable
   *
   * @return void
   */
  function mapPerson() {
    $this->_p_city           = $this->_user_ville;
    $this->_p_postal_code    = $this->_user_cp;
    $this->_p_street_address = $this->_user_adresse;
    $this->_p_phone_number   = $this->_user_phone;
    $this->_p_email          = $this->_user_email;
    $this->_p_first_name     = $this->_user_first_name;
    $this->_p_last_name      = $this->_user_last_name;
  }
  
  /**
   * Fonction récupérant les rétrocessions du praticien
   * 
   * @return $this->_ref_retrocessions
  **/
  function loadRefsRetrocessions() {
    return $this->_ref_retrocessions = $this->loadBackRefs("retrocession");
  }
}