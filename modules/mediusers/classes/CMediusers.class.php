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
  public $inami;
  public $cps;
  public $titres;
  public $initials;
  public $color;
  public $commentaires;
  public $actif;
  public $deb_activite;
  public $fin_activite;
  public $compte;
  public $banque_id;
  public $mail_apicrypt;
  public $compta_deleguee;
  public $last_ldap_checkout;
  public $other_specialty_id;
  public $use_bris_de_glace;

  // DB References
  public $function_id;
  public $discipline_id;
  public $spec_cpam_id;

  public $code_intervenant_cdarr;

  public $secteur;
  public $contrat_acces_soins;
  public $option_coordination;
  // Champs utilisés pour l'affichage des ordonnances ALD
  public $cab;
  public $conv;
  public $zisd;
  public $ik;
  public $ean;
  public $ean_base;
  public $rcc;
  public $adherent;
  public $debut_bvr;
  public $electronic_bill;
  public $specialite_tarmed;
  public $role_tarmed;
  public $place_tarmed;
  public $reminder_text;

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
  /** @var bool Does the password need to be changed */
  public $_force_change_password;

  // Other fields
  public $_profile_id;
  public $_is_praticien;
  public $_is_professionnel_sante;
  public $_is_dentiste;
  public $_is_secretaire;
  public $_is_anesth;
  public $_is_infirmiere;
  public $_is_aide_soignant;
  public $_is_sage_femme;
  public $_is_pharmacien;
  public $_user_password_weak;
  public $_user_password_strong;
  public $_basic_info;
  public $_is_urgentiste;
  public $_force_merge = false;
  public $_user_id;
  public $_keep_user;
  public $_user_type_view;
  public $_common_name;

  // Distant fields
  public $_group_id;

  public $_color; // color following this or function
  public $_font_color;

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

  /** @var CSpecialtyAsip */
  public $_ref_other_spec;

  /** @var CDiscipline */
  public $_ref_discipline;

  /** @var CUser */
  public $_ref_profile;

  /** @var CUser */
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

  /** @var CFile */
  public $_ref_signature;

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
   * @see parent::isInstalled()
   */
  function isInstalled() {
    // Prevents zillions of uncachable SQL queries on table existence
    return CModule::getInstalled("mediusers");
  }

  /**
   * @return CFunctions[]
   */
  static function loadCurrentFunctions() {
    $user                         = CMediusers::get();
    $group_id                     = CGroups::loadCurrent()->_id;
    $secondary_function           = new CSecondaryFunction();
    $ljoin                        = array();
    $where                        = array();
    $where["group_id"]            = "= '$group_id'";
    $where["user_id"]             = "= '$user->_id'";
    $ljoin["functions_mediboard"] = "functions_mediboard.function_id = secondary_function.function_id";

    return $user->_ref_current_functions = $secondary_function->loadList($where, null, null, null, $ljoin);
  }

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec        = parent::getSpec();
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
    $props["user_id"] = "ref class|CUser seekable show|0";

    $props["remote"]                 = "bool default|1 show|0";
    $props["adeli"]                  = "numchar length|9 confidential mask|99S9S99999S9 control|luhn";
    $props["rpps"]                   = "numchar length|11 confidential mask|99999999999 control|luhn";
    $props['inami']                  = 'numchar length|11 confidential mask|99999999999';
    $props["cps"]                    = "str";
    $props["function_id"]            = "ref notNull class|CFunctions seekable";
    $props["discipline_id"]          = "ref class|CDiscipline";
    $props["other_specialty_id"]     = "ref class|CSpecialtyAsip autocomplete|libelle";
    $props["titres"]                 = "text";
    $props["initials"]               = "str";
    $props["color"]                  = "color";
    $props["use_bris_de_glace"]      = "bool default|0";
    $props["commentaires"]           = "text";
    $props["actif"]                  = "bool default|1";
    $props["deb_activite"]           = "date";
    $props["fin_activite"]           = "date";
    $props["spec_cpam_id"]           = "ref class|CSpecCPAM";
    $props["compte"]                 = "code rib confidential mask|99999S99999S***********S99 show|0";
    $props["banque_id"]              = "ref class|CBanque show|0";
    $props["code_intervenant_cdarr"] = "str length|2";
    $props["secteur"]                = "enum list|1|2";
    $props['contrat_acces_soins']    = "bool";
    $props['option_coordination']    = "bool";
    $props["cab"]                    = "str";
    $props["conv"]                   = "str";
    $props["zisd"]                   = "str";
    $props["ik"]                     = "str";
    $props["ean"]                    = "str";
    $props["ean_base"]               = "str";
    $props["rcc"]                    = "str";
    $props["adherent"]               = "str";
    $props["debut_bvr"]              = "str maxLength|10";
    $props["electronic_bill"]        = "bool default|0";
    $props["specialite_tarmed"]      = "numchar length|4";
    $props["role_tarmed"]            = "str";
    $props["place_tarmed"]           = "str";
    $props["reminder_text"]          = "text";
    $props["mail_apicrypt"]          = "email";
    $props["compta_deleguee"]        = "bool default|0";
    $props["last_ldap_checkout"]     = "date";

    $props["_group_id"] = "ref notNull class|CGroups";

    $props["_user_username"]         = "str notNull minLength|3 reported";
    $props["_user_password2"]        = "password sameAs|_user_password reported";
    $props["_user_first_name"]       = "str reported show|1";
    $props["_user_last_name"]        = "str notNull confidential reported show|1";
    $props["_user_email"]            = "str confidential reported";
    $props["_user_phone"]            = "phone confidential reported";
    $props["_user_astreinte"]        = "str confidential reported";
    $props["_user_adresse"]          = "str confidential reported";
    $props["_user_last_login"]       = "dateTime reported";
    $props["_user_cp"]               = "num length|5 confidential reported";
    $props["_user_ville"]            = "str confidential reported";
    $props["_profile_id"]            = "ref reported class|CUser";
    $props["_user_type"]             = "num notNull min|0 max|21 reported";
    $props["_user_type_view"]        = "str";
    $props["_force_change_password"] = "bool default|0";

    // The different levels of security are stored to be usable in JS
    $props["_user_password_weak"]   = "password minLength|4 randomizable";
    $props["_user_password_strong"] = "password minLength|6 notContaining|_user_username notNear|_user_username alphaAndNum randomizable";

    $props["_user_password"] = $props["_user_password_weak"] . " reported";

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
    $this->_specs['_user_password'] = $strongPassword ?
      $this->_specs['_user_password_strong'] :
      $this->_specs['_user_password_weak'];

    $this->_specs['_user_password']->fieldName = $oldSpec->fieldName;

    $this->_props['_user_password'] = $strongPassword ?
      $this->_props['_user_password_strong'] :
      $this->_props['_user_password_weak'];
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps                                     = parent::getBackProps();
    $backProps["secondary_functions"]              = "CSecondaryFunction user_id";
    $backProps["actes_ccam_executes"]              = "CActeCCAM executant_id";
    $backProps["actes_ngap_executes"]              = "CActeNGAP executant_id";
    $backProps["actes_tarmed_executes"]            = "CActeTarmed executant_id";
    $backProps["actes_caisse_executes"]            = "CActeCaisse executant_id";
    $backProps["administrations"]                  = "CAdministration administrateur_id";
    $backProps["aides_saisie"]                     = "CAideSaisie user_id";
    $backProps["handled_alerts"]                   = "CAlert handled_user_id";
    $backProps["modeles"]                          = "CCompteRendu user_id";
    $backProps["documents_ged"]                    = "CDocGed user_id";
    $backProps["suivis__ged"]                      = "CDocGedSuivi user_id";
    $backProps["examens"]                          = "CExamenLabo realisateur";
    $backProps["users"]                            = "CFicheEi user_id";
    $backProps["valid_users"]                      = "CFicheEi valid_user_id";
    $backProps["service_valid_users"]              = "CFicheEi service_valid_user_id";
    $backProps["qualite_users"]                    = "CFicheEi qualite_user_id";
    $backProps["owned_files"]                      = "CFile author_id";
    $backProps["forum_messages"]                   = "CForumMessage user_id";
    $backProps["forum_threads"]                    = "CForumThread user_id";
    $backProps["hprim21_medecins"]                 = "CHprim21Medecin user_id";
    $backProps["listes_choix"]                     = "CListeChoix user_id";
    $backProps["owned_notes"]                      = "CNote user_id";
    $backProps["observations"]                     = "CObservationMedicale user_id";
    $backProps["operations_chir"]                  = "COperation chir_id";
    $backProps["operations_chir2"]                 = "COperation chir_2_id";
    $backProps["operations_chir3"]                 = "COperation chir_3_id";
    $backProps["operations_chir4"]                 = "COperation chir_4_id";
    $backProps["operations_anesth"]                = "COperation anesth_id";
    $backProps["packs"]                            = "CPack user_id";
    $backProps["prescription_line_mixes"]          = "CPrescriptionLineMix praticien_id";
    $backProps["prescription_line_mixes_0"]        = "CPrescriptionLineMix creator_id";
    $backProps["personnels"]                       = "CPersonnel user_id";
    $backProps["plages_op_chir"]                   = "CPlageOp chir_id";
    $backProps["plages_op_anesth"]                 = "CPlageOp anesth_id";
    $backProps["plages_consult"]                   = "CPlageconsult chir_id";
    $backProps["plages_conge"]                     = "CPlageConge user_id";
    $backProps["consults_anesth"]                  = "CConsultAnesth chir_id";
    $backProps["plages_ressource"]                 = "CPlageressource prat_id";
    $backProps["prescriptions"]                    = "CPrescription praticien_id";
    $backProps["prescriptions_labo"]               = "CPrescriptionLabo praticien_id";
    $backProps["prescription_comments"]            = "CPrescriptionLineComment praticien_id";
    $backProps["prescription_comments_crees"]      = "CPrescriptionLineComment creator_id";
    $backProps["prescription_comments_executes"]   = "CPrescriptionLineComment user_executant_id";
    $backProps["prescription_elements"]            = "CPrescriptionLineElement praticien_id";
    $backProps["prescription_elements_crees"]      = "CPrescriptionLineElement creator_id";
    $backProps["prescription_elements_executes"]   = "CPrescriptionLineElement user_executant_id";
    $backProps["prescription_medicaments"]         = "CPrescriptionLineMedicament praticien_id";
    $backProps["prescription_medicaments_crees"]   = "CPrescriptionLineMedicament creator_id";
    $backProps["prescription_protocole_packs"]     = "CPrescriptionProtocolePack praticien_id";
    $backProps["prescription_dmis"]                = "CPrescriptionLineDMI praticien_id";
    $backProps["protocoles"]                       = "CProtocole chir_id";
    $backProps["remplacements"]                    = "CPlageConge replacer_id";
    $backProps["sejours"]                          = "CSejour praticien_id";
    $backProps["services"]                         = "CService responsable_id";
    $backProps["services_entity"]                  = "CService user_id";
    $backProps["secteurs"]                         = "CSecteur user_id";
    $backProps["groups"]                           = "CGroups user_id";
    $backProps["legal_entities"]                   = "CLegalEntity user_id";
    $backProps["tarifs"]                           = "CTarif chir_id";
    $backProps["techniciens"]                      = "CTechnicien kine_id";
    $backProps["temps_hospi"]                      = "CTempsHospi praticien_id";
    $backProps["temps_chir"]                       = "CTempsOp chir_id";
    $backProps["temps_prepa"]                      = "CTempsPrepa chir_id";
    $backProps["transmissions"]                    = "CTransmissionMedicale user_id";
    $backProps["visites_anesth"]                   = "COperation prat_visite_anesth_id";
    $backProps["checked_lists"]                    = "CDailyCheckList validator_id";
    $backProps["evenements_ssr"]                   = "CEvenementSSR therapeute_id";
    $backProps["activites_rhs"]                    = "CLigneActivitesRHS executant_id";
    $backProps["replacements"]                     = "CReplacement replacer_id";
    $backProps["frais_divers"]                     = "CFraisDivers executant_id";
    $backProps["expediteur_ftp"]                   = "CSenderFTP user_id";
    $backProps["expediteur_sftp"]                  = "CSenderSFTP user_id";
    $backProps["expediteur_soap"]                  = "CSenderSOAP user_id";
    $backProps["expediteur_mllp"]                  = "CSenderMLLP user_id";
    $backProps["expediteur_fs"]                    = "CSenderFileSystem user_id";
    $backProps["ufs"]                              = "CAffectationUniteFonctionnelle object_id";
    $backProps["documents_crees"]                  = "CCompteRendu author_id";
    $backProps["devenirs_dentaires"]               = "CDevenirDentaire etudiant_id";
    $backProps["plages_remplacees"]                = "CPlageconsult remplacant_id";
    $backProps["plages_pour_compte_de"]            = "CPlageconsult pour_compte_id";
    $backProps["poses_disp_vasc_operateur"]        = "CPoseDispositifVasculaire operateur_id";
    $backProps["poses_disp_vasc_encadrant"]        = "CPoseDispositifVasculaire encadrant_id";
    $backProps["praticien_facture_cabinet"]        = "CFactureCabinet praticien_id";
    $backProps["praticien_facture_etab"]           = "CFactureEtablissement praticien_id";
    $backProps["tokens"]                           = "CViewAccessToken user_id";
    $backProps["didacticiel_avancement"]           = "CDidacticielAvancement user_id";
    $backProps["astreintes"]                       = "CPlageAstreinte user_id";
    $backProps["dicom_sender"]                     = "CDicomSender user_id";
    $backProps["cps_pyxvital"]                     = "CPvCPS id_mediuser";
    $backProps["affectation"]                      = "CAffectation praticien_id";
    $backProps["regles_sectorisation_mediuser"]    = "CRegleSectorisation praticien_id";
    $backProps["retrocession"]                     = "CRetrocession praticien_id";
    $backProps["user_debiteur"]                    = "CDebiteurOX user_id";
    $backProps["user_bioserveur"]                  = "CUserBioserveur user_id";
    $backProps["bioserveur_account"]               = "CBioServeurAccount user_id";
    $backProps["compte_rendu"]                     = "CCompteRendu locker_id";
    $backProps["long_request_log"]                 = "CLongRequestLog user_id";
    $backProps["tasking_assigned_to"]              = "CTaskingTicket assigned_to_id";
    $backProps["tasking_supervisor"]               = "CTaskingTicket supervisor_id";
    $backProps["tasking_message_author"]           = "CTaskingTicketMessage user_id";
    $backProps["tasking_contact_interlocutor"]     = "CTaskingContactEvent interlocutor_user_id";
    $backProps["usermessage_created"]              = "CUserMessage creator_id";
    $backProps["usermessage_dest_to"]              = "CUserMessageDest to_user_id";
    $backProps["usermessage_dest_from"]            = "CUserMessageDest from_user_id";
    $backProps["constantes"]                       = "CConstantesMedicales user_id";
    $backProps["sejours_sortie_confirmee"]         = "CSejour confirme_user_id";
    $backProps["ops_sortie_validee"]               = "COperation sortie_locker_id";
    $backProps["ticket_requests_referer"]          = "CRequestTicket user_referer_id";
    $backProps["notification_praticien"]           = "CNotification praticien_id";
    $backProps["mbhost_installations"]             = "CMbHostInstallation user_id";
    $backProps["plages_op_owner"]                  = "CPlageOp original_owner_id";
    $backProps["documents_sisra_receiver"]         = "CSisraDocument account_id";
    $backProps["bris_de_glace_user"]               = "CBrisDeGlace user_id";
    $backProps["log_access_user"]                  = "CLogAccessMedicalData user_id";
    $backProps["dmp_documents_sent"]               = "CDMPFile author_id";
    $backProps["dmp_log"]                          = "CDMPLogUser user_id";
    $backProps["patient_state"]                    = "CPatientState mediuser_id";
    $backProps["ide_responsable"]                  = "CRPU ide_responsable_id";
    $backProps["segments"]                         = "CPrescriptionLineSegment user_id";
    $backProps["ox_operations_as_requester"]       = "COXOperation requester_id";
    $backProps["ox_updates_as_requester"]          = "COXUpdate requester_id";
    $backProps["ox_prod_updates_as_requester"]     = "COXProdUpdate requester_id";
    $backProps["ox_training_updates_as_requester"] = "COXTrainingUpdate requester_id";
    $backProps["ox_testing_updates_as_requester"]  = "COXTestingUpdate requester_id";
    $backProps["ox_operations_as_main"]            = "COXOperation main_executor_id";
    $backProps["ox_updates_as_main"]               = "COXUpdate main_executor_id";
    $backProps["ox_prod_updates_as_main"]          = "COXProdUpdate main_executor_id";
    $backProps["ox_training_updates_as_main"]      = "COXTrainingUpdate main_executor_id";
    $backProps["ox_testing_updates_as_main"]       = "COXTestingUpdate main_executor_id";
    $backProps["ox_operations_as_secondary"]       = "COXOperation secondary_executor_id";
    $backProps["ox_updates_as_secondary"]          = "COXUpdate secondary_executor_id";
    $backProps["ox_prod_updates_as_secondary"]     = "COXProdUpdate secondary_executor_id";
    $backProps["ox_training_updates_as_secondary"] = "COXTrainingUpdate secondary_executor_id";
    $backProps["ox_testing_updates_as_secondary"]  = "COXTestingUpdate secondary_executor_id";
    $backProps["request_tickets_as_supervisor"]    = "CRequestTicket supervisor_id";
    $backProps["favoris_protocoles"]               = "CFavoriProtocole user_id";
    $backProps['codage_ccam']                      = 'CCodageCCAM praticien_id';
    $backProps['remboursement_noemie']             = 'CPvRemboursementNoemie praticien_id';
    $backProps["search_thesaurus_entry"]           = "CSearchThesaurusEntry user_id";
    $backProps["files_user_view"]                  = "CFileUserView user_id";
    $backProps['mssante_account']                  = 'CMSSanteUserAccount user_id';
    $backProps['drawing_category_user']            = 'CDrawingCategory user_id';
    $backProps['cleanup_operator']                 = 'CBedCleanup cleanup_operator_id';
    $backProps['antecedents']                      = 'CAntecedent owner_id';
    $backProps['traitements']                      = 'CTraitement owner_id';
    $backProps['pathologies']                      = 'CPathologie owner_id';
    $backProps['rejets_prat']                      = 'CFactureRejet praticien_id';
    $backProps['dim']                              = 'CTraitementDossier dim_id';
    $backProps['appels_user']                      = 'CAppelSejour user_id';
    $backProps["news_archived"]                    = "CVidalNewsArchived user_id";
    return $backProps;
  }

  /**
   * Création d'un utilisateur
   *
   * @return CUser
   */
  function createUser() {
    $user                = new CUser();
    $user->user_id       = ($this->_user_id) ? $this->_user_id : $this->user_id;
    $user->user_type     = $this->_user_type;
    $user->user_username = $this->_user_username;

    if (isset($this->_ldap_store)) {
      $user->user_password = $this->_user_password;
    }
    else {
      $user->_user_password = $this->_user_password;
    }

    $user->user_first_name       = $this->_user_first_name;
    $user->user_last_name        = $this->_user_last_name;
    $user->user_email            = $this->_user_email;
    $user->user_phone            = $this->_user_phone;
    $user->user_astreinte        = $this->_user_astreinte;
    $user->user_address1         = $this->_user_adresse;
    $user->user_zip              = $this->_user_cp;
    $user->user_city             = $this->_user_ville;
    $user->profile_id            = $this->_profile_id;
    $user->force_change_password = $this->_force_change_password;
    $user->template              = 0;

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
  function merge($objects = array/*<CMbObject>*/
    (), $fast = false) {
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

    $this->updateColor();
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
   * @see parent::loadQueryList()
   */
  function loadQueryList($query) {
    /** @var self[] $mediusers */
    CMediusers::$user_autoload = false;
    $mediusers                 = parent::loadQueryList($query);
    CMediusers::$user_autoload = true;

    if (!count($mediusers)) {
      return array();
    }

    // Mass user speficic preloading
    $user = new CUser();
    $user->loadAll(array_keys($mediusers));

    // Attach cached user
    foreach ($mediusers as $_mediuser) {
      $_mediuser->updateFormFields();
    }

    return $mediusers;
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
      $this->_user_type             = $user->user_type;
      $this->_user_username         = $user->user_username;
      $this->_user_password         = $user->user_password;
      $this->_user_first_name       = CMbString::capitalize($user->user_first_name);
      $this->_user_last_name        = CMbString::upper($user->user_last_name);
      $this->_user_email            = $user->user_email;
      $this->_user_phone            = $user->user_phone;
      $this->_user_astreinte        = $user->user_astreinte;
      $this->_user_adresse          = $user->user_address1;
      $this->_user_cp               = $user->user_zip;
      $this->_user_ville            = $user->user_city;
      $this->_user_template         = $user->template;
      $this->_profile_id            = $user->profile_id;
      $this->_force_change_password = $user->force_change_password;

      // Encrypt this datas
      $this->checkConfidential();
      $this->_view      = "$this->_user_last_name $this->_user_first_name";
      $this->_shortview = "";

      // Initiales
      if (!$this->_shortview = $this->initials) {
        $this->_shortview .= CMbString::makeInitials($this->_user_first_name, "-");
        $this->_shortview .= CMbString::makeInitials($this->_user_last_name);
      }

      $this->_user_type_view = CValue::read(CUser::$types, $this->_user_type);
    }

    $this->_ref_user = $user;

    $this->mapPerson();
    $this->updateSpecs();

    return $this->_ref_user;
  }

  /**
   * @return CBanque
   */
  function loadRefBanque() {
    return $this->_ref_banque = $this->loadFwdRef("banque_id", true);
  }

  /**
   * Chargement du profil associé
   *
   * @return CUser
   */
  function loadRefProfile() {
    return $this->_ref_profile = $this->loadFwdRef("_profile_id", true);
  }

  /**
   * Chargement de la fonction principale
   *
   * @return CFunctions
   */
  function loadRefFunction() {
    /** @var CFunctions $function */
    $function            = $this->loadFwdRef("function_id", true);
    $this->_group_id     = $function ? $function->group_id : null;
    $this->_ref_function = $function;
    $this->updateColor();

    return $this->_ref_function;
  }

  /**
   * Retourne la liste des fonctions secondaires dde l'utilisateur
   *
   * @return CFunctions[]
   */
  function loadRefsSecondaryFunctions() {
    $this->loadBackRefs("secondary_functions");
    $secondary_functions = array();
    foreach ($this->_back["secondary_functions"] as $_sec_func) {
      /** @var CSecondaryFunction $_sec_func */
      $_sec_func->loadRefFunction();
      $_sec_func->loadRefUser();
      $_function                            = $_sec_func->_ref_function;
      $secondary_functions[$_function->_id] = $_function;
    }
    return $secondary_functions;
  }

  /**
   * Utilisation de la couleur de l'utilisateur si définie
   * sinon de la fonction
   *
   * @return string User color
   */
  function updateColor() {
    $function_color    = $this->_ref_function ? $this->_ref_function->color : null;
    $this->_color      = $this->color ? $this->color : $function_color;
    $this->_font_color = CColorSpec::get_text_color($this->_color) > 130 ? "000000" : "ffffff";

    return $this->_color;
  }

  /**
   * Chargement de la discipline médicale
   *
   * @return CDiscipline
   */
  function loadRefDiscipline() {
    return $this->_ref_discipline = $this->loadFwdRef("discipline_id", true);
  }

  /**
   * Chargement de la spécialité CPAM
   *
   * @return CSpecCPAM
   */
  function loadRefSpecCPAM() {
    return $this->_ref_spec_cpam = $this->loadFwdRef("spec_cpam_id", true);
  }

  /**
   * Chargement de l'aute spécialité
   *
   * @return CSpecialtyAsip
   */
  function loadRefOtherSpec() {
    return $this->_ref_other_spec = $this->loadFwdRef("other_specialty_id", true);
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
    if ($perm = CPermObject::getPermObject($this, $permType, $this->_ref_function)) {
      return $perm;
    }

    $this->loadBackRefs("secondary_functions");
    foreach ($this->_back["secondary_functions"] as $_link) {
      /** @var  CSecondaryFunction $_link */
      $fonction = $_link->loadRefFunction();
      $fonction->load($_link->function_id);
      if ($perm = $perm || CPermObject::getPermObject($this, $permType, $fonction)) {
        return $perm;
      }
    }

    return $perm;
  }

  /**
   * Chargement de la liste des protocoles de DHE de l'utilisateur
   *
   * @param string $type type du séjour
   *
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
    $where          = array(
      "protocole.chir_id = '$this->_id' OR protocole.function_id IN ($list_functions)"
    );

    if ($type) {
      $where["type"] = "= '$type'";
    }

    $protocole             = new CProtocole();
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
    $where          = array(
      "protocole.chir_id = '$this->_id' OR protocole.function_id IN ($list_functions)"
    );

    if ($type) {
      $where["type"] = "= '$type'";
    }

    $protocole               = new CProtocole();
    $this->_count_protocoles = $protocole->countList($where);
  }

  /**
   * Tableau comprenant l'utilisateur et son organigramme
   *
   * @return CMbObject[]
   */
  function getOwners() {
    $func  = $this->loadRefFunction();
    $etab  = $func->loadRefGroup();

    return array(
      "prat"  => $this,
      "func"  => $func,
      "etab"  => $etab,
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

    if ($this->fieldModified("remote", 0) && !CAppUI::$user->isAdmin()) {
      if (!$this->_user_password) {
        return "Veuillez saisir à nouveau votre mot de passe";
      }
    }

    /*
    if (!CAppUI::$user->isAdmin()) {
      if ($this->fieldModified("_user_type", 1) || (!$this->_id && $this->_user_type)) {
        return "Opération interdite";
      }
    }
    */

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
      $vars = $this->getPlainFields();
      $ret  = $spec->ds->updateObject($spec->table, $vars, $spec->key, $spec->nullifyEmptyStrings);
    }
    else {
      $this->user_id = $user->user_id;
      $vars          = $this->getPlainFields();
      $keyToUpdate   = $spec->incremented ? $spec->key : null;
      $ret           = $spec->ds->insertObject($spec->table, $this, $vars, $keyToUpdate);
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
    $where                 = array();
    $where["user_id"]      = "= '$this->user_id'";
    $where["object_class"] = "= 'CFunctions'";
    $where["object_id"]    = "= '$this->function_id'";

    $perm = new CPermObject;
    if ($perm->loadObject($where)) {
      $perm->delete();
    }
  }

  /**
   * Ajout de la permission sur sa fonction à un utilisateur
   *
   * @return void
   */
  function insFunctionPermission() {
    $where                 = array();
    $where["user_id"]      = "= '$this->user_id'";
    $where["object_class"] = "= 'CFunctions'";
    $where["object_id"]    = "= '$this->function_id'";

    $perm = new CPermObject;
    if (!$perm->loadObject($where)) {
      $perm               = new CPermObject;
      $perm->user_id      = $this->user_id;
      $perm->object_class = "CFunctions";
      $perm->object_id    = $this->function_id;
      $perm->permission   = PERM_EDIT;
      $perm->store();
    }
  }

  /**
   * Ajout de la permission sur son établissement à un utilisateur
   *
   * @return void
   */
  function insGroupPermission() {
    $function = new CFunctions;
    $function->load($this->function_id);
    $where                 = array();
    $where["user_id"]      = "= '$this->user_id'";
    $where["object_class"] = "= 'CGroups'";
    $where["object_id"]    = "= '$function->group_id'";

    $perm = new CPermObject;
    if (!$perm->loadObject($where)) {
      $perm               = new CPermObject;
      $perm->user_id      = $this->user_id;
      $perm->object_class = "CGroups";
      $perm->object_id    = $function->group_id;
      $perm->permission   = PERM_EDIT;
      $perm->store();
    }
  }

  /**
   * Chargement de la liste des utilisateurs à partir de leur type
   *
   * @param array  $user_types  Tableau des types d'utilisateur
   * @param int    $permType    Niveau de permission
   * @param int    $function_id Filtre sur une fonction spécifique
   * @param string $name        Filtre sur un nom d'utilisateur
   * @param bool   $actif       Filtre sur les utilisateurs actifs
   * @param bool   $secondary   Inclut les fonctions secondaires dans le filtre sur les fonctions
   * @param bool   $reverse     Utilise les types en inclusion ou en exclusion
   *
   * @return CMediusers[]
   */
  function loadListFromType(
    $user_types = null, $permType = PERM_READ, $function_id = null,
    $name = null, $actif = true, $secondary = false, $reverse = false
  ) {
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
    $ljoin["secondary_function"]  = "secondary_function.user_id = users_mediboard.user_id";
    $ljoin[]                      = "functions_mediboard AS sec_fnc_mb ON sec_fnc_mb.function_id = secondary_function.function_id";

    if ($function_id) {
      if ($secondary) {
        $where[] = "'$function_id' IN (users_mediboard.function_id, secondary_function.function_id)";
      }
      else {
        $where["users_mediboard.function_id"] = "= '$function_id'";
      }
    }

    // Filter on current group or users in secondaries functions
    $group   = CGroups::loadCurrent();
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

    $order    = "`users`.`user_last_name`, `users`.`user_first_name`";
    $group_by = array("user_id");

    // Get all users
    $mediuser = new CMediusers();
    /** @var CMediusers[] $mediusers */
    $mediusers = $mediuser->loadList($where, $order, null, $group_by, $ljoin);

    // Mass fonction standard preloading
    self::massLoadFwdRef($mediusers, "function_id");
    self::massCountBackRefs($mediusers, "secondary_functions");

    // Filter a posteriori to unable mass preloading of function
    self::filterByPerm($mediusers, $permType);

    // Associate cached function
    foreach ($mediusers as $_mediuser) {
      $_mediuser->loadRefFunction();
    }

    return $mediusers;
  }

  /**
   * Liste de Tous les établissements
   *
   * @param int $permType Type de permission à valider
   *
   * @return CGroups[]
   */
  static function loadEtablissements($permType = PERM_READ) {
    return CGroups::loadGroups($permType);
  }

  /**
   * Load list overlay for current group
   *
   * @param array  $where   list of SQL WHERE statements
   * @param array  $order   list of SQL ORDER statement
   * @param string $limit   SQL limit statement
   * @param string $groupby SQL GROUP BY statement
   * @param array  $ljoin   list of SQL LEFT JOIN statements
   *
   * @return self[]
   */
  function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array()) {
    $ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";
    // Filtre sur l'établissement
    $group                                 = CGroups::loadCurrent();
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
  static function loadFonctions($permType = PERM_READ, $group_id = null, $type = null, $name = "") {
    $group = CGroups::loadCurrent();

    $function = new CFunctions();
    $where = array();

    $where["actif"] = "= '1'";
    $where["group_id"] = "= '" . CValue::first($group_id, $group->_id) . "'";

    if ($type) {
      $where["type"] = "= '$type'";
    }

    if ($name) {
      $where["text"] = "LIKE '$name%'";
    }

    $order = "text";

    /** @var CFunctions[] $functions */
    $functions = $function->loadList($where, $order);
    CMbObject::filterByPerm($functions, $permType);

    // Group association
    foreach ($functions as $function) {
      $function->_ref_group = $group;
    }

    return $functions;
  }

  /**
   * @param int    $permType    permission
   * @param int    $function_id fontion
   * @param string $name        nom
   * @param bool   $actif       actif
   *
   * @return CMediusers[]
   */
  function loadUsers($permType = PERM_READ, $function_id = null, $name = null, $actif = true) {
    return $this->loadListFromType(null, $permType, $function_id, $name, $actif);
  }

  /**
   * @param int    $permType    permission
   * @param int    $function_id fontion
   * @param string $name        nom
   * @param bool   $actif       actif
   *
   * @return CMediusers[]
   */
  function loadMedecins($permType = PERM_READ, $function_id = null, $name = null, $actif = true) {
    return $this->loadListFromType(array("Médecin"), $permType, $function_id, $name, $actif);
  }

  /**
   * @param int    $permType    permission
   * @param int    $function_id fontion
   * @param string $name        nom
   * @param bool   $actif       actif
   *
   * @return CMediusers[]
   */
  function loadChirurgiens($permType = PERM_READ, $function_id = null, $name = null, $actif = true) {
    return $this->loadListFromType(array("Chirurgien", "Dentiste"), $permType, $function_id, $name, $actif);
  }

  /**
   * @param int    $permType    permission
   * @param int    $function_id fontion
   * @param string $name        nom
   * @param bool   $actif       actif
   *
   * @return CMediusers[]
   */
  function loadAnesthesistes($permType = PERM_READ, $function_id = null, $name = null, $actif = true) {
    return $this->loadListFromType(array("Anesthésiste"), $permType, $function_id, $name, $actif);
  }

  function loadProfessionnelDeSanteByPref($permType = PERM_READ, $function_id = null, $name = null, $secondary = false, $actif = true) {
    $list = array();
    if (CAppUI::pref("take_consult_for_chirurgien")) {
      $list[] = "Chirurgien";
    }
    if (CAppUI::pref("take_consult_for_anesthesiste")) {
      $list[] = "Anesthésiste";
    }
    if (CAppUI::pref("take_consult_for_medecin")) {
      $list[] = "Médecin";
    }
    if (CAppUI::pref("take_consult_for_dentiste")) {
      $list[] = "Dentiste";
    }
    if (CAppUI::pref("take_consult_for_infirmiere")) {
      $list[] = "Infirmière";
    }
    if (CAppUI::pref("take_consult_for_reeducateur")) {
      $list[] = "Rééducateur";
    }
    if (CAppUI::pref("take_consult_for_sage_femme")) {
      $list[] = "Sage Femme";
    }
    if (CAppUI::pref("take_consult_for_dieteticien")) {
      $list[] = "Diététicien";
    }

    return $this->loadListFromType($list, $permType, $function_id, $name, $actif, $secondary);
  }

  /**
   * @param int    $permType    permission
   * @param int    $function_id fontion
   * @param string $name        nom
   * @param bool   $secondary   secondary
   * @param bool   $actif       actif
   *
   * @return CMediusers[]
   */
  function loadPraticiens($permType = PERM_READ, $function_id = null, $name = null, $secondary = false, $actif = true) {
    return $this->loadListFromType(array("Chirurgien", "Anesthésiste", "Médecin", "Dentiste"), $permType, $function_id, $name, $actif, $secondary);
  }

  /**
   * @param int    $permType    permission
   * @param int    $function_id fontion
   * @param string $name        nom
   * @param bool   $secondary   secondary
   * @param bool   $actif       actif
   *
   * @return CMediusers[]
   */
  function loadProfessionnelDeSante($permType = PERM_READ, $function_id = null, $name = null, $secondary = false, $actif = true) {
    return $this->loadListFromType(array("Chirurgien", "Anesthésiste", "Médecin", "Infirmière", "Rééducateur", "Sage Femme", "Dentiste", "Diététicien"), $permType, $function_id, $name, $actif, $secondary);
  }

  /**
   * @param int    $permType    permission
   * @param int    $function_id fontion
   * @param string $name        nom
   * @param bool   $secondary   secondary
   * @param bool   $actif       actif
   *
   * @return CMediusers[]
   */
  function loadNonProfessionnelDeSante($permType = PERM_READ, $function_id = null, $name = null, $secondary = false, $actif = true) {
    return $this->loadListFromType(array("Chirurgien", "Anesthésiste", "Médecin", "Infirmière", "Rééducateur", "Sage Femme", "Dentiste", "Diététicien"), $permType, $function_id, $name, $secondary, $actif, true);
  }

  /**
   * @param int    $permType    permission
   * @param int    $function_id fontion
   * @param string $name        nom
   *
   * @return CMediusers[]
   */
  function loadPersonnels($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Personnel"), $permType, $function_id, $name);
  }

  /**
   * @param int    $permType    permission
   * @param int    $function_id fontion
   * @param string $name        nom
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
   * Check whether user is a medical professionnal
   *
   * @return bool
   */
  function isProfessionnelDeSante() {
    return $this->_is_professionnel_sante = $this->isFromType(
      array("Chirurgien", "Anesthésiste", "Médecin", "Infirmière", "Rééducateur", "Sage Femme", "Dentiste", "Diététicien")
    );
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
   * @return bool
   */
  function isSageFemme() {
    return $this->_is_sage_femme = $this->isFromType(array("Sage Femme"));
  }

  /**
   * Check whether user is a secretary
   *
   * @return bool
   */
  function isSecretaire() {
    return $this->_is_secretaire = $this->isFromType(array("Secrétaire", "Administrator"));
  }


  /**
   * Check whether user is a medical user
   *
   * @return bool
   */
  function isMedical() {
    return $this->isFromType(array("Administrator", "Chirurgien", "Anesthésiste", "Infirmière", "Médecin", "Rééducateur", "Sage Femme", "Dentiste", "Pharmacien", "Diététicien"));
  }

  /**
   * @return bool
   */
  function isExecutantPrescription() {
    return $this->isFromType(array("Infirmière", "Aide soignant", "Rééducateur", "Sage Femme", "Diététicien"));
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
   * Check whether user is a dieteticien
   *
   * @return bool
   */
  function isDieteticien() {
    return $this->isFromType(array("Diététicien"));
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
  function isUrgentiste() {
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
    $this->loadRefFunction();
    $this->loadRefSpecCPAM();
    $this->loadRefDiscipline();
    $this->_ref_function->fillTemplate($template);
    $template->addProperty("Praticien - Nom", $this->_user_last_name);
    $template->addProperty("Praticien - Prénom", $this->_user_first_name);
    $template->addProperty("Praticien - Initiales", $this->_shortview);
    $template->addProperty("Praticien - Discipline", $this->_ref_discipline->_view);
    $template->addProperty("Praticien - Spécialité", $this->_ref_spec_cpam->_view);
    $template->addProperty("Praticien - CAB", $this->cab);
    $template->addProperty("Praticien - CONV", $this->conv);
    $template->addProperty("Praticien - ZISD", $this->zisd);
    $template->addProperty("Praticien - IK", $this->ik);

    $template->addProperty("Praticien - Titres", $this->titres);
    $template->addProperty("Praticien - ADELI", $this->adeli);
    $template->addBarcode("Praticien - Code barre ADELI", $this->adeli, array("barcode" => array(
      "title" => CAppUI::tr("{$this->_class}-adeli")
    )));
    $template->addProperty("Praticien - RPPS", $this->rpps);
    $template->addBarcode("Praticien - Code barre RPPS", $this->rpps, array("barcode" => array(
      "title" => CAppUI::tr("{$this->_class}-rpps")
    )));
    $template->addProperty("Praticien - E-mail", $this->_user_email);
    $template->addProperty("Praticien - E-mail Apicrypt", $this->mail_apicrypt);

    // Identité
    $identite = $this->loadNamedFile("identite.jpg");
    $template->addImageProperty("Praticien - Photo d'identite", $identite->_id);

    // Signature
    $signature = $this->loadRefSignature();
    $template->addImageProperty("Praticien - Signature", $signature->_id);
  }

  /**
   * Charge la liste de plages et interventions pour un jour donné
   * Analogue à CSalle::loadRefsForDay
   *
   * @param string $date        Date to look for
   * @param bool   $second_chir Use chir_2, chir_3 and chir_4
   *
   * @return void
   */
  function loadRefsForDay($date, $second_chir = false) {
    $this->loadBackRefs("secondary_functions");
    $secondary_specs = array();
    foreach ($this->_back["secondary_functions"] as $_sec_spec) {
      /** @var CSecondaryFunction $_sec_spec */
      $_sec_spec->loadRefFunction();
      $_sec_spec->loadRefUser();
      $_function                        = $_sec_spec->_ref_function;
      $secondary_specs[$_function->_id] = $_function;
    }
    // Plages d'intervention
    $plage     = new CPlageOp();
    $ljoin     = array();
    $add_where = "";
    if ($second_chir) {
      $ljoin["operations"] = "plagesop.plageop_id = operations.plageop_id";
      $add_where           = " OR operations.chir_id = '$this->_id' OR operations.chir_2_id = '$this->_id'
                    OR operations.chir_3_id = '$this->_id' OR operations.chir_4_id = '$this->_id'";
    }
    $where                  = array();
    $where["plagesop.date"] = "= '$date'";
    $where[]                = "plagesop.chir_id = '$this->_id' OR plagesop.spec_id = '$this->function_id' OR plagesop.spec_id " . CSQLDataSource::prepareIn(array_keys($secondary_specs)) . $add_where;
    $order                  = "debut";
    $this->_ref_plages      = $plage->loadList($where, $order, null, "plageop_id", $ljoin);

    // Chargement d'optimisation

    CMbObject::massLoadFwdRef($this->_ref_plages, "chir_id");
    CMbObject::massLoadFwdRef($this->_ref_plages, "anesth_id");
    CMbObject::massLoadFwdRef($this->_ref_plages, "spec_id");
    CMbObject::massLoadFwdRef($this->_ref_plages, "salle_id");

    CMbObject::massCountBackRefs($this->_ref_plages, "notes");
    CMbObject::massCountBackRefs($this->_ref_plages, "affectations_personnel");

    foreach ($this->_ref_plages as $_plage) {
      /** @var CPlageOp $_plage */
      $_plage->loadRefChir();
      $_plage->loadRefAnesth();
      $_plage->loadRefSpec();
      $_plage->loadRefSalle();
      $_plage->makeView();
      $_plage->loadRefsOperations();
      $_plage->loadRefsNotes();
      $_plage->loadAffectationsPersonnel();
      $_plage->_unordered_operations = array();

      // Chargement d'optimisation
      CMbObject::massLoadFwdRef($_plage->_ref_operations, "chir_id");
      $sejours = CMbObject::massLoadFwdRef($_plage->_ref_operations, "sejour_id");
      CMbObject::massLoadFwdRef($sejours, "patient_id");

      foreach ($_plage->_ref_operations as $_operation) {
        if ($_operation->chir_id != $this->_id && (!$second_chir || ($_operation->chir_2_id != $this->_id && $_operation->chir_3_id != $this->_id && $_operation->chir_4_id != $this->_id))) {
          unset($_plage->_ref_operations[$_operation->_id]);
        }
        else {
          $_operation->_ref_chir = $this;
          $_operation->loadExtCodesCCAM();
          $_operation->updateSalle();
          $_operation->loadRefPatient();

          // Extraire les interventions non placées
          if ($_operation->rank == 0) {
            $_plage->_unordered_operations[$_operation->_id] = $_operation;
            unset($_plage->_ref_operations[$_operation->_id]);
          }
        }
      }
      if (count($_plage->_ref_operations) + count($_plage->_unordered_operations) < 1) {
        unset($this->_ref_plages[$_plage->_id]);
      }
    }

    // Interventions déplacés
    $deplacee                       = new COperation();
    $ljoin                          = array();
    $ljoin["plagesop"]              = "operations.plageop_id = plagesop.plageop_id";
    $where                          = array();
    $where["operations.plageop_id"] = "IS NOT NULL";
    $where["operations.annulee"]    = "= '0'";
    $where["plagesop.salle_id"]     = "!= operations.salle_id";
    $where["plagesop.date"]         = "= '$date'";
    $where[]                        = "plagesop.chir_id = '$this->_id'" . $add_where;
    $order                          = "operations.time_operation";
    $this->_ref_deplacees           = $deplacee->loadList($where, $order, null, "operation_id", $ljoin);

    // Chargement d'optimisation
    CMbObject::massLoadFwdRef($this->_ref_deplacees, "chir_id");
    $sejours_deplacees = CMbObject::massLoadFwdRef($this->_ref_deplacees, "sejour_id");
    CMbObject::massLoadFwdRef($sejours_deplacees, "patient_id");

    foreach ($this->_ref_deplacees as $_deplacee) {
      /** @var COperation $_deplacee */
      $_deplacee->loadRefChir();
      $_deplacee->loadRefPatient();
      $_deplacee->loadExtCodesCCAM();
    }

    // Urgences
    $urgence             = new COperation();
    $where               = array();
    $where["plageop_id"] = "IS NULL";
    $where["date"]       = "= '$date'";
    if ($second_chir) {
      $where[] = "chir_id = '$this->_id' OR chir_2_id = '$this->_id' OR chir_3_id = '$this->_id' OR chir_4_id = '$this->_id'";
    }
    else {
      $where["chir_id"] = "= '$this->_id'";
    }
    $where["annulee"] = "= '0'";

    $this->_ref_urgences = $urgence->loadList($where, null, null, "operation_id");

    // Chargement d'optimisation
    CMbObject::massLoadFwdRef($this->_ref_urgences, "chir_id");
    $sejours_urgences = CMbObject::massLoadFwdRef($this->_ref_urgences, "sejour_id");
    CMbObject::massLoadFwdRef($sejours_urgences, "patient_id");

    foreach ($this->_ref_urgences as $_urgence) {
      /** @var COperation $_urgence */
      $_urgence->loadRefChir();
      $_urgence->loadRefPatient();
      $_urgence->loadExtCodesCCAM();
    }
  }

  /**
   * Builds a structure containing basic information about the user, to be used in JS in window.User
   *
   * @return array|null
   */
  function getBasicInfo() {
    if (!$this->_ref_module) {
      return null;
    }

    $this->updateFormFields();
    $this->loadRefFunction()->loadRefGroup();

    return $this->_basic_info = array(
      'id'       => $this->_id,
      'guid'     => $this->_guid,
      'view'     => $this->_view,
      'login'    => $this->_user_username,
      'function' => array(
        'id'    => $this->_ref_function->_id,
        'guid'  => $this->_ref_function->_guid,
        'view'  => $this->_ref_function->_view,
        'color' => $this->_ref_function->color
      ),
      'group'    => array(
        'guid' => $this->_ref_function->_ref_group->_guid,
        'id'   => $this->_ref_function->_ref_group->_id,
        'view' => $this->_ref_function->_ref_group->_view,
      )
    );
  }

  function makeUsernamePassword($first_name, $last_name, $id = null, $number = false, $prepass = "mdp") {
    $length               = 20 - strlen($id);
    $this->_user_username = substr(preg_replace($number ? "/[^a-z0-9]/i" : "/[^a-z]/i", "", strtolower(CMbString::removeDiacritics(($first_name ? $first_name[0] : '') . $last_name))), 0, $length) . $id;
    $this->_user_password = $prepass . substr(preg_replace($number ? "/[^a-z0-9]/i" : "/[^a-z]/i", "", strtolower(CMbString::removeDiacritics($last_name))), 0, $length) . $id;
  }

  function getNbJoursPlanningSSR($date) {
    $sunday   = CMbDT::date("next sunday", CMbDT::date("- 1 DAY", $date));
    $saturday = CMbDT::date("-1 DAY", $sunday);

    $_evt               = new CEvenementSSR();
    $where              = array();
    $where["debut"]     = "BETWEEN '$sunday 00:00:00' AND '$sunday 23:59:59'";
    $where["sejour_id"] = " = '$this->_id'";
    $count_event_sunday = $_evt->countList($where);

    $nb_days = 7;

    // Si aucun evenement le dimanche
    if (!$count_event_sunday) {
      $nb_days              = 6;
      $where["debut"]       = "BETWEEN '$saturday 00:00:00' AND '$saturday 23:59:59'";
      $count_event_saturday = $_evt->countList($where);
      // Aucun evenement le samedi et aucun le dimanche
      if (!$count_event_saturday) {
        $nb_days = 5;
      }
    }

    return $nb_days;
  }

  /**
   * @see parent::getAutocompleteList()
   */
  function getAutocompleteList($keywords, $where = null, $limit = null, $ljoin = null, $order = null) {
    $ljoin = array_merge($ljoin, array("users" => "users.user_id = users_mediboard.user_id"));
    /** @var CMediusers[] $list */
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
   * @param int $group_id group_id
   *
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

  /**
   * @see parent::getDynamicTag
   */
  function getDynamicTag() {
    return CAppUI::conf("mediusers tag_mediuser");
  }

  /**
   * Is the user a robot?
   *
   * @return bool
   */
  function isRobot() {
    if (!$this->_id) {
      return false;
    }
    $tag_software = CMediusers::getTagSoftware();

    if (CModule::getActive("dPsante400") && $tag_software) {
      if (CIdSante400::getMatch($this->_class, $tag_software, null, $this->_id)->_id != null) {
        return true;
      }
    }

    if (!$this->_ref_user || !$this->_ref_user->_id) {
      $this->loadRefUser();
    }

    return $this->_ref_user->dont_log_connection;
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

  static function loadFromAdeli($adeli) {
    $cache = new Cache(__METHOD__, func_get_args(), Cache::INNER);
    if ($cache->exists()) {
      return $cache->get();
    }

    $mediuser                       = new CMediusers();
    $where                          = array();
    $where["users_mediboard.adeli"] = " = '$adeli'";
    $mediuser->loadObject($where);

    return $cache->put($mediuser, false);
  }

  function getLastLogin() {
    return $this->_user_last_login = $this->_ref_user->getLastLogin();
  }

  function loadRefSignature() {
    return $this->_ref_signature = $this->loadNamedFile("signature.jpg");
  }
}