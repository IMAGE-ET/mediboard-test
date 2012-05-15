<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CGroups extends CMbObject {
  // DB Table key
  var $group_id       = null;  

  // DB Fields
  var $text                = null;
  var $raison_sociale      = null;
  var $adresse             = null;
  var $cp                  = null;
  var $ville               = null;
  var $tel                 = null;
  var $fax                 = null;
  var $mail                = null;
  var $mail_apicrypt       = null;
  var $web                 = null;
  var $directeur           = null;
  var $domiciliation       = null;
  var $siret               = null;
  var $ape                 = null;
  var $tel_anesth          = null;
  var $service_urgences_id = null;
  var $pharmacie_id        = null;
  var $finess              = null;
  var $chambre_particuliere= null;
  var $_cp_court = null;
  
  // Object References
  var $_ref_functions = null;
  var $_ref_blocs = null;
  var $_ref_dmi_categories = null;
  var $_ref_services = null;
  var $_ref_pharmacie = null;
  var $_ref_service_urgences = null;
  
  
  static $_ref_current = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'groups_mediboard';
    $spec->key   = 'group_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["aides_saisie"]            = "CAideSaisie group_id";
    $backProps["categories_prescription"] = "CCategoryPrescription group_id";
    $backProps["category_DM"]             = "CCategoryDM group_id";
    $backProps["config_moment"]           = "CConfigMomentUnitaire group_id";
    $backProps["config_service"]          = "CConfigService group_id";
    $backProps["check_item_types"]        = "CDailyCheckItemType group_id";
    $backProps["dmi_categories"]          = "CDMICategory group_id";
    $backProps["documents_ged"]           = "CDocGed group_id";
    $backProps["etablissements_sherpa"]   = "CSpEtablissement group_id";
    $backProps["functions"]               = "CFunctions group_id";
    $backProps["listes_choix"]            = "CListeChoix group_id";
    $backProps["modeles"]                 = "CCompteRendu group_id";
    $backProps["menus"]                   = "CMenu group_id";
    $backProps["messages"]                = "CMessage group_id";
    $backProps["packs"]                   = "CPack group_id";
    $backProps["packs_categorie_prescription"] = "CPrescriptionCategoryGroup group_id";
    $backProps["plats"]                   = "CPlat group_id";
    $backProps["blocs"]                   = "CBlocOperatoire group_id";
    $backProps["sejours"]                 = "CSejour group_id";
    $backProps["services"]                = "CService group_id";
    $backProps["types_repas"]             = "CTypeRepas group_id";
    $backProps["chapitres_qualite"]       = "CChapitreDoc group_id";
    $backProps["themes_qualite"]          = "CThemeDoc group_id";
    $backProps["plateaux_techniques"]     = "CPlateauTechnique group_id";
    $backProps["prestations"]             = "CPrestation group_id";
    $backProps["product_orders"]          = "CProductOrder group_id";
    $backProps["product_receptions"]      = "CProductReception group_id";
    $backProps["product_stock_locations"] = "CProductStockLocation group_id";
    $backProps["product_stocks"]          = "CProductStockGroup group_id";
    $backProps["protocoles_prescription"] = "CPrescription group_id";
    $backProps["packs_prescription"]      = "CPrescriptionProtocolePack group_id";
    $backProps["reception_bills"]         = "CProductReceptionBill group_id";
    $backProps['object_configs']          = "CGroupsConfig object_id";
    $backProps["stock_locations"]         = "CProductStockLocation object_id";
    $backProps["stock_first_config"]      = "CCegiStockFirstConfig object_id";
    $backProps["destinataires_hprim"]     = "CDestinataireHprim group_id";
    $backProps["destinataires_sigems"]    = "CDestinataireSigems group_id";
    $backProps["destinataires_hprim21"]   = "CDestinataireHprim21 group_id";
    $backProps["destinataires_ihe"]       = "CReceiverIHE group_id";
    $backProps["echanges_generique"]      = "CExchangeAny group_id";
    $backProps["echanges_hprim"]          = "CEchangeHprim group_id";
    $backProps["echanges_hprim21"]        = "CEchangeHprim21 group_id";
    $backProps["echanges_ihe"]            = "CExchangeIHE group_id";
    $backProps["echanges_phast"]          = "CPhastEchange group_id";
    $backProps["extract_passages"]        = "CExtractPassages group_id";
    $backProps["destinataires_phast"]     = "CPhastDestinataire group_id";  
    $backProps["senders_ftp"]             = "CSenderFTP group_id";
    $backProps["senders_soap"]            = "CSenderSOAP group_id";
    $backProps["senders_mllp"]            = "CSenderMLLP group_id";
    $backProps["senders_fs"]              = "CSenderFileSystem group_id";
    $backProps["view_sender_sources"]     = "CViewSenderSource group_id";
    $backProps["modeles_etiquette"]       = "CModeleEtiquette group_id";
    $backProps["unites_fonctionnelles"]   = "CUniteFonctionnelle group_id";
    $backProps["ex_classes"]              = "CExClass group_id";
    $backProps["config_constantes_medicales"] = "CConfigConstantesMedicales group_id";
    $backProps["prestations_journalieres"] = "CPrestationJournaliere group_id";
    $backProps["prestations_ponctuelles"] = "CPrestationPonctuelle group_id";
    $backProps["supervision_graphs"]      = "CSupervisionGraph owner_id";
    $backProps["incrementers"]            = "CIncrementer group_id";
    return $backProps;
  }
  
  function getProps() {
    $props = parent::getProps();
    
    $props["text"]                = "str notNull confidential seekable";
    $props["raison_sociale"]      = "str maxLength|50";
    $props["adresse"]             = "text confidential";
    $props["cp"]                  = "numchar length|5";
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
    $props["_cp_court"]           = "numchar length|2";
    
    return $props;
  }
  
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->text;
    $this->_shortview = CMbString::truncate($this->text);
    $this->_cp_court = substr($this->cp,0,2);
  }
  
  function store(){
    $is_new = !$this->_id;
    
    if ($msg = parent::store()) {
      return $msg;
    }
    
    if ($is_new) {
      CConfigService::emptySHM();
      CConfigMomentUnitaire::emptySHM();
      CConfigConstantesMedicales::emptySHM();
    }
  }

  /**
   * Load functions with given permission
   */
  function loadFunctions($permType = PERM_READ) {
    return $this->_ref_functions = CMediusers::loadFonctions($permType, $this->_id);
  }
  
  /**
   * Load blocs operatoires with given permission
   */
  function loadBlocs($permType = PERM_READ, $load_salles = true) {
    $bloc = new CBlocOperatoire();
    $where = array('group_id' => "='$this->_id'");
    $this->_ref_blocs = $bloc->loadListWithPerms($permType, $where, 'nom');
    
    if ($load_salles) {
      foreach ($this->_ref_blocs as &$bloc) {
        $bloc->loadRefsSalles();
      }
    }
    return $this->_ref_blocs;
  }
  
  function loadRefsBack() {
    $this->loadFunctions();
  }

  function loadRefsService(){
    return $this->_ref_services = $this->loadBackRefs("services", "nom");
  }

  function loadRefPharmacie(){
    return $this->_ref_pharmacie = $this->loadFwdRef("pharmacie_id");
  }

  function loadRefServiceUrgences(){
    return $this->_ref_service_urgences = $this->loadFwdRef("service_urgences_id");
  }
  
  /**
   * Load groups with given permission
   */
  static function loadGroups($permType = PERM_READ) {
    $group = new CGroups;
    $groups = $group->loadList(null, "text");
    self::filterByPerm($groups, $permType);
    return $groups;
  }
  
  function fillLimitedTemplate(&$template) {
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
    $template->addBarCode("Etablissement - Code Barre FINESS", $this->finess, array("barcode" => array(
      "title" => CAppUI::tr("{$this->_class}-finess")
    )));
  }
  
  function fillTemplate(&$template) {
    $this->fillLimitedTemplate($template);
  }
  
  /**
   * Load the current group
   * @return CGroups
   */
  static function loadCurrent() {
    if (!self::$_ref_current) {
      global $g;
      self::$_ref_current = new CGroups();
      self::$_ref_current->load($g);
    }
    return self::$_ref_current;
  }
  
  function loadRefsDMICategories() {
    $this->_ref_dmi_categories = $this->loadBackRefs("dmi_categories", "nom");
  }
  
  /**
   * Construit le tag de l'établissement en fonction des variables de configuration
   * @return string
   */
  function getTagGroup() {
    // Pas de tag sur l'établiessement
    if (null == $tag_group = CAppUI::conf("dPetablissement tag_group")) {
      return;
    }

    return str_replace('$g', $this->_id, $tag_group);
  }
  
  /**
   * Charge l'idex de l'établissement
   */
  function loadIdex() {
    $tag_group = $this->getTagGroup();
    
    if (!$this->_id || !$tag_group) {
      return;
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
  
}
?>