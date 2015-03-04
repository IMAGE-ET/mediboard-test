<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPetablissement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Group class (Etablissement)
 */
class CGroups extends CEntity {
  public $group_id;

  // DB Fields
  public $text;
  public $raison_sociale;
  public $adresse;
  public $cp;
  public $ville;
  public $tel;
  public $fax;
  public $mail;
  public $mail_apicrypt;
  public $web;
  public $directeur;
  public $domiciliation;
  public $siret;
  public $ape;
  public $tel_anesth;
  public $service_urgences_id;
  public $pharmacie_id;
  public $finess;
  public $chambre_particuliere;
  public $ean;
  public $rcc;
  public $lat;
  public $lon;
  public $legal_entity_id;

  // Form fields
  public $_cp_court;
  public $_is_ipp_supplier = false;
  public $_is_nda_supplier = false;

  /** @var CFunctions[] */
  public $_ref_functions;

  /** @var CBlocOperatoire[] */
  public $_ref_blocs;

  /** @var CPosteSSPI[] */
  public $_ref_postes;

  /** @var CDMICategory[] */
  public $_ref_dmi_categories;

  /** @var CService[] */
  public $_ref_services;

  /** @var CFunctions */
  public $_ref_pharmacie;

  /** @var CFunctions */
  public $_ref_service_urgences;

  /** @var self */
  static $_ref_current = null;

  /** @var CLegalEntity */
  public $_ref_legal_entity;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'groups_mediboard';
    $spec->key   = 'group_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["aides_saisie"]              = "CAideSaisie group_id";
    $backProps["categories_prescription"]   = "CCategoryPrescription group_id";
    $backProps["category_DM"]               = "CCategoryDM group_id";
    $backProps["config_moment"]             = "CConfigMomentUnitaire group_id";
    $backProps["config_service"]            = "CConfigService group_id";
    $backProps["check_item_types"]          = "CDailyCheckItemType group_id";
    $backProps["dmi_categories"]            = "CDMICategory group_id";
    $backProps["documents_ged"]             = "CDocGed group_id";
    $backProps["etablissements_sherpa"]     = "CSpEtablissement group_id";
    $backProps["functions"]                 = "CFunctions group_id";
    $backProps["listes_choix"]              = "CListeChoix group_id";
    $backProps["modeles"]                   = "CCompteRendu group_id";
    $backProps["menus"]                     = "CMenu group_id";
    $backProps["messages"]                  = "CMessage group_id";
    $backProps["packs"]                     = "CPack group_id";
    $backProps["packs_categorie_prescription"] = "CPrescriptionCategoryGroup group_id";
    $backProps["plats"]                     = "CPlat group_id";
    $backProps["blocs"]                     = "CBlocOperatoire group_id";
    $backProps["sejours"]                   = "CSejour group_id";
    $backProps["services"]                  = "CService group_id";
    $backProps["types_repas"]               = "CTypeRepas group_id";
    $backProps["chapitres_qualite"]         = "CChapitreDoc group_id";
    $backProps["themes_qualite"]            = "CThemeDoc group_id";
    $backProps["plateaux_techniques"]       = "CPlateauTechnique group_id";
    $backProps["prestations"]               = "CPrestation group_id";
    $backProps["product_orders"]            = "CProductOrder group_id";
    $backProps["product_address_orders"]    = "CProductOrder address_id";
    $backProps["product_receptions"]        = "CProductReception group_id";
    $backProps["product_stock_locations"]   = "CProductStockLocation group_id";
    $backProps["product_stocks"]            = "CProductStockGroup group_id";
    $backProps["protocoles_prescription"]   = "CPrescription group_id";
    $backProps["packs_prescription"]        = "CPrescriptionProtocolePack group_id";
    $backProps["reception_bills"]           = "CProductReceptionBill group_id";
    $backProps['object_configs']            = "CGroupsConfig object_id";
    $backProps["stock_locations"]           = "CProductStockLocation object_id";
    $backProps["destinataires_hprim"]       = "CDestinataireHprim group_id";
    $backProps["destinataires_sigems"]      = "CDestinataireSigems group_id";
    $backProps["destinataires_hprim21"]     = "CDestinataireHprim21 group_id";
    $backProps["destinataires_hprimsante"]  = "CReceiverHprimSante group_id";
    $backProps["destinataires_HL7v2"]       = "CReceiverHL7v2 group_id";
    $backProps["destinataires_HL7v3"]       = "CReceiverHL7v3 group_id";
    $backProps["destinataires_dmp"]         = "CReceiverDMP group_id";
    $backProps["echanges_generique"]        = "CExchangeAny group_id";
    $backProps["echanges_hprim"]            = "CEchangeHprim group_id";
    $backProps["echanges_hprim21"]          = "CEchangeHprim21 group_id";
    $backProps["echanges_hprimsante"]       = "CExchangeHprimSante group_id";
    $backProps["echanges_hl7v2"]            = "CExchangeHL7v2 group_id";
    $backProps["echanges_hl7v3"]            = "CExchangeHL7v3 group_id";
    $backProps["echanges_dmp"]              = "CExchangeDMP group_id";
    $backProps["echanges_phast"]            = "CExchangePhast group_id";
    $backProps["echanges_dicom"]            = "CExchangeDicom group_id";
    $backProps["echanges_mvsante"]          = "CExchangeMVSante group_id";
    $backProps["extract_passages"]          = "CExtractPassages group_id";
    $backProps["destinataires_phast"]       = "CPhastDestinataire group_id";
    $backProps["senders_ftp"]               = "CSenderFTP group_id";
    $backProps["senders_sftp"]              = "CSenderSFTP group_id";
    $backProps["senders_soap"]              = "CSenderSOAP group_id";
    $backProps["senders_mllp"]              = "CSenderMLLP group_id";
    $backProps["senders_fs"]                = "CSenderFileSystem group_id";
    $backProps["view_sender_sources"]       = "CViewSenderSource group_id";
    $backProps["modeles_etiquette"]         = "CModeleEtiquette group_id";
    $backProps["unites_fonctionnelles"]     = "CUniteFonctionnelle group_id";
    $backProps["ex_classes"]                = "CExClass group_id";
    $backProps["prestations_journalieres"]  = "CPrestationJournaliere group_id";
    $backProps["prestations_ponctuelles"]   = "CPrestationPonctuelle group_id";
    $backProps["supervision_graphs"]        = "CSupervisionGraph owner_id";
    $backProps["supervision_graph_packs"]   = "CSupervisionGraphPack owner_id";
    $backProps["supervision_timed_data"]    = "CSupervisionTimedData owner_id";
    $backProps["supervision_timed_picture"] = "CSupervisionTimedPicture owner_id";
    $backProps["ressources_materielles"]    = "CRessourceMaterielle group_id";
    $backProps["type_ressources"]           = "CTypeRessource group_id";
    $backProps["secteurs"]                  = "CSecteur group_id";
    $backProps["protocoles"]                = "CProtocole group_id";
    $backProps["charges"]                   = "CChargePriceIndicator group_id";
    $backProps["postes"]                    = "CPosteSSPI group_id";
    $backProps["group_domains"]             = "CGroupDomain group_id";
    $backProps["modes_entree_sejour"]       = "CModeEntreeSejour group_id";
    $backProps["modes_sortie_sejour"]       = "CModeSortieSejour group_id";
    $backProps["dicom_sender"]              = "CDicomSender group_id";
    $backProps["dicom_session"]             = "CDicomSession group_id";
    $backProps["pyxvital_receiver"]         = "CReceiverPyxVital group_id";
    $backProps["diet"]                      = "CDiet group_id";
    $backProps["diet_property"]             = "CDietProperty group_id";
    $backProps["dish"]                      = "CDish group_id";
    $backProps["meal_menu"]                 = "CMealMenu group_id";
    $backProps["meal_type"]                 = "CMealType group_id";
    $backProps["regle_sectorisation_group"] = "CRegleSectorisation group_id";
    $backProps["tarif_group"]               = "CTarif group_id";
    $backProps["extension_unites"]          = "CExtensionProduitUnite group_id";
    $backProps["extension_voies"]           = "CExtensionProduitVoie group_id";
    $backProps["extension_produits_subst"]  = "CExtensionProduitSubstitution group_id";
    $backProps["daily_check_lists"]         = "CDailyCheckList group_id";
    $backProps["daily_check_list_types"]    = "CDailyCheckListType group_id";
    $backProps["grossesses"]                = "CGrossesse group_id";
    $backProps["libelle_mvsante"]           = "CLibelleOp group_id";
    $backProps["cn_test"]                   = "CCnTest group_id";
    $backProps["digital_pen_printer"]       = "CDigitalPenPrinter group_id";
    $backProps["ex_links"]                  = "CExLink group_id";
    $backProps["group_fact_cab"]            = "CFactureCabinet group_id";
    $backProps["group_fact_etab"]           = "CFactureEtablissement group_id";
    $backProps["notifications"]             = "CNotification group_id";
    $backProps["notification_objects"]      = "CNotificationObject group_id";
    $backProps["type_anesth_group"]         = "CTypeAnesth group_id";
    $backProps["bris_de_glace_group"]       = "CBrisDeGlace group_id";
    $backProps["log_access_medical_data"]   = "CLogAccessMedicalData group_id";
    $backProps["group_astreinte"]           = "CPlageAstreinte group_id";
    $backProps["extension_produit_docs"]    = "CExtensionProduitDocument group_id";
    $backProps["group_um"]                  = "CUniteMedicaleInfos group_id";
    $backProps["search_thesaurus_entry"]    = "CSearchThesaurusEntry group_id";
    $backProps["links_cat_chap"]            = "CLinkBonCatChap group_id";
    $backProps["supervision_instant_data"]  = "CSupervisionInstantData owner_id";
    $backProps["type_brancard_group"]       = "CBrancardageTransport group_id";
    $backProps["drawing_category_group"]    = "CDrawingCategory group_id";
    //$backProps["ex_object"]                 = "CExObject group_id"; // DO NOT ADD THIS BACK PROP
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["text"]                = "str notNull confidential seekable";
    $props["raison_sociale"]      = "str maxLength|50";
    $props["adresse"]             = "text confidential";
    $props["cp"]                  = "str minLength|4 maxLength|10";
    $props["ville"]               = "str maxLength|50 confidential";
    $props["tel"]                 = "phone";
    $props["fax"]                 = "phone";
    $props["tel_anesth"]          = "phone";
    $props["service_urgences_id"] = "ref class|CFunctions";
    $props["pharmacie_id"]        = "ref class|CFunctions";
    $props["directeur"]           = "str maxLength|50";
    $props["domiciliation"]       = "str maxLength|9";
    $props["siret"]               = "str length|14";
    $props["ape"]                 = "str maxLength|6 confidential";
    $props["mail"]                = "email";
    $props["mail_apicrypt"]       = "email";
    $props["web"]                 = "str";
    $props["finess"]              = "numchar length|9 confidential mask|9xS9S99999S9 control|luhn";
    $props["chambre_particuliere"]= "bool notNull default|0";
    $props["ean"]                 = "str";
    $props["rcc"]                 = "str";
    $props["lat"]                 = "float";
    $props["lon"]                 = "float";
    $props["legal_entity_id"]     = "ref class|CLegalEntity";

    $props["_cp_court"]           = "numchar length|2";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->_name;
    $this->_shortview = CMbString::truncate($this->_name);
    $this->_cp_court = substr($this->cp, 0, 2);
  }

  /**
   * @see parent::store()
   */
  function store(){
    $is_new = !$this->_id;

    if ($msg = parent::store()) {
      return $msg;
    }

    if ($is_new && CModule::getActive("dPprescription")) {
      CConfigService::emptySHM();
      CConfigMomentUnitaire::emptySHM();
    }

    return null;
  }

  /**
   * Load functions with given permission
   *
   * @param int $permType Permission level
   *
   * @return CFunctions[]
   */
  function loadFunctions($permType = PERM_READ) {
    return $this->_ref_functions = CMediusers::loadFonctions($permType, $this->_id);
  }

  /**
   * Load blocs operatoires with given permission
   *
   * @param int    $permType    Permission level
   * @param bool   $load_salles Load salles
   * @param string $order       Ordre de chargmeent SQL
   * @param array  $where       SQL WHERE parameters
   *
   * @return CBlocOperatoire[]
   */
  function loadBlocs($permType = PERM_READ, $load_salles = true, $order = "nom", $where = array()) {
    $bloc = new CBlocOperatoire();
    $whereGroup = array(
      'group_id' => "= '$this->_id'"
    );
    $where = array_merge($where, $whereGroup);

    /** @var CBlocOperatoire[] $blocs */
    $blocs = $bloc->loadListWithPerms($permType, $where, $order);

    if ($load_salles) {
      CStoredObject::massLoadBackRefs($blocs, "salles", "nom");
      foreach ($blocs as $_bloc) {
        $_bloc->loadRefsSalles();
      }
    }

    return $this->_ref_blocs = $blocs;
  }

  /**
   * Lazy access to a given group, defaultly connected group
   *
   * @param integer $group_id The group id
   *
   * @return self
   */
  static function get($group_id = null) {
    if ($group_id) {
      $group = new self;

      return $group->getCached($group_id);
    }

    return CGroups::loadCurrent();
  }

  /**
   * Load postes SSPI
   *
   * @param int  $permType  Permission level
   * @param bool $load_bloc Load blocs
   *
   * @return CPosteSSPI[]
   */
  function loadPostes($permType = PERM_READ, $load_bloc = true) {
    $poste = new CPosteSSPI();
    $where = array(
      "group_id" => "= '$this->_id'"
    );

    /** @var CPosteSSPI[] $postes */
    $postes = $poste->loadListWithPerms($permType, $where, "nom");

    if ($load_bloc) {
      foreach ($postes as $_poste) {
        $_poste->loadRefBloc();
      }
    }

    return $this->_ref_postes = $postes;
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    $this->loadFunctions();
  }

  /**
   * Get group's services
   *
   * @return CService[]
   */
  function loadRefsServices(){
    return $this->_ref_services = $this->loadBackRefs("services", "nom");
  }

  /**
   * Get pharmacy function
   *
   * @return CFunctions
   */
  function loadRefPharmacie(){
    return $this->_ref_pharmacie = $this->loadFwdRef("pharmacie_id");
  }

  /**
   * Get emergency function
   *
   * @return CFunctions
   */
  function loadRefServiceUrgences(){
    return $this->_ref_service_urgences = $this->loadFwdRef("service_urgences_id");
  }

  /**
   * Load groups with given permission
   *
   * @param int $permType Permission level
   *
   * @return self[]
   */
  static function loadGroups($permType = PERM_READ) {
    $group = new self();
    $groups = $group->loadList(null, "text");
    self::filterByPerm($groups, $permType);
    return $groups;
  }

  /**
   * @see parent::fillLimitedTemplate()
   */
  function fillLimitedTemplate(&$template) {
    $this->notify("BeforeFillLimitedTemplate", $template);

    $template->addProperty("Etablissement - Nom"             , $this->text);
    $template->addProperty("Etablissement - Adresse"         , "$this->adresse \n $this->cp $this->ville");
    $template->addProperty("Etablissement - Ville"           , $this->ville);
    $template->addProperty("Etablissement - Téléphone"       , $this->getFormattedValue("tel"));
    $template->addProperty("Etablissement - Fax"             , $this->getFormattedValue("fax"));
    $template->addProperty("Etablissement - E-mail"          , $this->getFormattedValue("mail"));
    $template->addProperty("Etablissement - E-mail Apicrypt" , $this->getFormattedValue("mail_apicrypt"));
    $template->addProperty("Etablissement - Domiciliation"   , $this->domiciliation);
    $template->addProperty("Etablissement - Siret"           , $this->siret);
    $template->addProperty("Etablissement - Finess"          , $this->finess);
    $template->addProperty("Etablissement - Ape"             , $this->ape);

    $barcode = array("barcode" => array(
      "title" => CAppUI::tr("{$this->_class}-finess")
    ));
    $template->addBarCode("Etablissement - Code Barre FINESS", $this->finess, $barcode);

    $this->notify("AfterFillLimitedTemplate", $template);
  }

  /**
   * @see parent::fillTemplate()
   */
  function fillTemplate(&$template) {
    $this->fillLimitedTemplate($template);
  }

  /**
   * Load the current group
   *
   * @return CGroups
   */
  static function loadCurrent() {
    if (!self::$_ref_current || !self::$_ref_current->_id) {
      global $g;
      self::$_ref_current = new CGroups();
      self::$_ref_current->load($g);
    }

    return self::$_ref_current;
  }

  /**
   * Get DMI categories
   *
   * @return CDMICategory[]
   */
  function loadRefsDMICategories() {
    return $this->_ref_dmi_categories = $this->loadBackRefs("dmi_categories", "nom");
  }

  /**
   * Construit le tag de l'établissement en fonction des variables de configuration
   *
   * @return string|null
   */
  function getTagGroup() {
    // Pas de tag sur l'établiessement
    if (null == $tag_group = CAppUI::conf("dPetablissement tag_group")) {
      return null;
    }

    return str_replace('$g', $this->_id, $tag_group);
  }

  /**
   * Récupère les congés pour un pays
   *
   * @param string $date          the date to check
   * @param bool   $includeRegion are the territory holidays included ?
   *
   * @deprecated use CMbDate::getHolidays instead
   * @return array
   *
   */
  function getHolidays($date = null, $includeRegion = true){
    return CMbDate::getHolidays($date, $includeRegion);
  }

  /**
   * Charge l'idex de l'établissement
   *
   * @return string|null
   */
  function loadIdex() {
    $tag_group = $this->getTagGroup();

    if (!$this->_id || !$tag_group) {
      return null;
    }

    // Récupération du premier idex créé
    $order = "id400 ASC";

    // Recuperation de la valeur de l'id400
    $idex = new CIdSante400();
    $idex->setObject($this);
    $idex->tag = $tag_group;
    $idex->loadMatchingObject($order);

    return $idex->id400;
  }

  /**
   * Load the domain supplier
   *
   * @param string $domain_type Domain type (CSejour, CPatient, etc)
   *
   * @return null|CIncrementer
   */
  function loadDomainSupplier($domain_type) {
    if (!$this->_id) {
      return null;
    }

    $group_domain = new CGroupDomain();
    $group_domain->object_class = $domain_type;
    $group_domain->group_id     = $this->_id;
    $group_domain->master       = true;
    $group_domain->loadMatchingObject();

    if (!$group_domain->_id) {
      return null;
    }

    return $group_domain->loadRefDomain()->loadRefIncrementer();
  }

  /**
   * Is the group a domain supplier ?
   *
   * @param string $domain_type Domain type (CSejour, CPatient, etc)
   *
   * @return bool
   */
  function isNumberSupplier($domain_type) {
    $incrementer = self::loadDomainSupplier($domain_type);
    return !$incrementer || !$incrementer->_id ? 0 : 1;
  }

  /**
   * Is the group an IPP supplier ?
   *
   * @return bool
   */
  function isIPPSupplier() {
    return $this->_is_ipp_supplier = $this->isNumberSupplier("CPatient");
  }

  /**
   * Is the group an NDA supplier ?
   *
   * @return bool
   */
  function isNDASupplier() {
    return $this->_is_nda_supplier = $this->isNumberSupplier("CSejour");
  }

  /**
   * @see parent::mapEntityTo()
   */
  function mapEntityTo () {
    $this->_name = $this->text;
  }

  /**
   * @see parent::mapEntityFrom()
   */
  function mapEntityFrom () {
    $this->text = $this->_name;
  }

  /**
   * Method to load Legal Entity
   *
   * @return CLegalEntity
   */
  function loadRefLegalEntity () {
    return $this->_ref_legal_entity = $this->loadFwdRef("legal_entity_id", true);
  }
}
