<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Gère les services d'hospitalisation
 * - contient de chambres
 */
class CService extends CInternalStructure {
  // DB Table key
  public $service_id;

  // DB references
  public $group_id;
  public $responsable_id;
  public $secteur_id;

  // DB Fields
  public $nom;
  public $type_sejour;
  public $cancelled;
  public $hospit_jour;
  public $urgence;
  public $uhcd;
  public $externe;
  public $neonatalogie;
  public $radiologie;
  public $default_orientation;
  public $default_destination;
  public $is_soins_continue;
  public $use_brancardage;

  /** @var CChambre[] */
  public $_ref_chambres;

  /** @var CGroups */
  public $_ref_group;
  /** @var CSecteur */
  public $_ref_secteur;

  /** @var CValidationRepas[] */
  public $_ref_validrepas;

  /** @var  @var CAffectation[] */
  public $_ref_affectations;

  /** @var  @var CAffectation[] */
  public $_ref_affectations_couloir;

  /** @var CUniteFonctionnelle[] */
  public $_ref_ufs;

  /** @var CUniteFonctionnelle */
  public $_ref_uf_soins;

  /** @var CAffectationUniteFonctionnelle[] */
  public $_ref_affectations_uf;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'service';
    $spec->key   = 'service_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["chambres"]               = "CChambre service_id";
    $backProps["sejours"]                = "CSejour service_id";
    $backProps["protocoles"]             = "CProtocole service_id";
    $backProps["valid_repas"]            = "CValidationRepas service_id";
    $backProps["config_moment"]          = "CConfigMomentUnitaire service_id";
    $backProps["config_service"]         = "CConfigService service_id";
    $backProps["endowments"]             = "CProductEndowment service_id";
    $backProps["services_entree"]        = "CSejour service_entree_id";
    $backProps["services_sortie"]        = "CSejour service_sortie_id";
    $backProps["affectations"]           = "CAffectation service_id";
    $backProps["product_deliveries"]     = "CProductDelivery service_id";
    $backProps["product_stock_services"] = "CProductStockService object_id";
    $backProps["stock_locations"]        = "CProductStockLocation object_id";
    $backProps["ufs"]                    = "CAffectationUniteFonctionnelle object_id";
    $backProps["refus_dispensation"]     = "CRefusDispensation service_id";
    $backProps["regle_sectorisation_service"] = "CRegleSectorisation service_id";
    $backProps["origine_brancardage"]    = "CBrancardage origine_id";
    $backProps["origine_item"]           = "CBrancardageItem destination_id";

    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["group_id"]       = "ref notNull class|CGroups";
    $props["responsable_id"] = "ref class|CMediusers";
    $props["secteur_id"]     = "ref class|CSecteur";

    $sejour = new CSejour;
    $props["type_sejour"] = CMbString::removeToken($sejour->_props["type"], " ", "notNull");

    $props["nom"]                 = "str notNull seekable";
    $props["urgence"]             = "bool default|0";
    $props["uhcd"]                = "bool default|0";
    $props["hospit_jour"]         = "bool default|0";
    $props["externe"]             = "bool default|0";
    $props["cancelled"  ]         = "bool default|0";
    $props["neonatalogie"]        = "bool default|0";
    $props["radiologie"]          = "bool default|0";
    $props["default_orientation"] = "enum list|".implode("|", CRPU::$orientation_value);
    $props["default_destination"] = "enum list|".implode("|", CSejour::$destination_values);
    $props["is_soins_continue"]   = "bool default|0 notNull";
    $props["use_brancardage"]     = "bool default|1";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

  /**
   * @see parent::mapEntityTo()
   */
  function mapEntityTo () {
    $this->_name = $this->nom;
    $this->user_id = $this->responsable_id;
  }

  /**
   * @see parent::mapEntityFrom()
   */
  function mapEntityFrom () {
    if ($this->_name != null) {
      $this->nom = $this->_name;
    }
    if ($this->user_id != null) {
      $this->responsable_id = $this->user_id;
    }
  }

  /**
   * @see parent::store()
   */
  function store(){
    $is_new = !$this->_id;

    if ($msg = parent::store()) {
      return $msg;
    }

    if ($is_new) {
      CConfigService::emptySHM();
      CConfigMomentUnitaire::emptySHM();
    }

    return null;
  }

  /**
   * Load list overlay for current group
   *
   * @param array  $where   Where clause
   * @param string $order   Order clause
   * @param null   $limit   Limit clause
   * @param null   $groupby Group by clause
   * @param array  $ljoin   Left join clause
   *
   * @return static[]
   */
  function loadGroupList($where = array(), $order = 'nom', $limit = null, $groupby = null, $ljoin = array()) {
    // Filtre sur l'établissement
    $group = CGroups::loadCurrent();
    $where["group_id"] = "= '$group->_id'";

    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }

  /**
   * Chargements des chambres du service
   *
   * @param bool $annule Charge les chambres desactivées aussi
   *
   * @return CChambre[]
   */
  function loadRefsChambres($annule = true) {
    $chambre = new CChambre();
    $where = array(
      "service_id" => "= '$this->_id'",
    );

    if (!$annule) {
      $where["annule"] = "= '0'";
    }
    $order = "ISNULL(rank), rank, nom";

    return $this->_ref_chambres = $this->_back["chambres"] = $chambre->loadList($where, $order);
  }

  /**
   *
   */
  function loadRefsLits($annule = false) {
    $lit = new CLit();

    $where = array();
    $ljoin = array();

    $where["chambre.service_id"] = "= '$this->_id'";

    $ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";

    if (!$annule) {
      $where["lit.annule"] = "= '0'";
      $where["chambre.annule"] = "= '0'";
    }

    $order = "ISNULL(chambre.rank), chambre.rank, chambre.nom, ISNULL(lit.rank), lit.rank,lit.nom ";

    $lits = $lit->loadList($where, $order, null, null, $ljoin);
    $this->_ref_chambres = self::massLoadFwdRef($lits, "chambre_id", null, true);

    foreach ($lits as $_lit) {
      $_chambre = $_lit->loadRefChambre();
      $_chambre->_ref_service = $this;
      $_chambre->_ref_lits[$_lit->_id] = $_lit;
    }

    return $lits;
  }

  /**
   * Load affectations
   *
   * @param string $date               Date
   * @param bool   $with_effectue      Avec effectue
   * @param bool   $with_couloir       Avec couloir
   * @param bool   $with_sortie_reelle Avec sortie reelle
   *
   * @return CAffectation[]
   */
  function loadRefsAffectations($date, $with_effectue = true, $with_couloir = true, $with_sortie_reelle = false) {
    $ljoin = array();
    $where = array (
      "affectation.service_id" => "= '$this->_id'",
      "affectation.entree" => "<= '$date 23:59:59'",
      "affectation.sortie" => ">= '$date 00:00:00'"
    );

    if (!$with_effectue) {
      if ($with_sortie_reelle) {
        $complement = "";
        if ($date == CMbDT::date()) {
          $ljoin["sejour"] = "affectation.sejour_id = sejour.sejour_id";
          $complement = "OR (sejour.sortie_reelle >= '".CMbDT::dateTime()."' AND affectation.sortie >= '".CMbDT::dateTime()."')";
        }
        $where[] = "affectation.effectue = '0' $complement";
      }
      else {
        $where["affectation.effectue"] = "= '0'";
      }
    }

    if (!$with_couloir) {
      $where["affectation.lit_id"] = "IS NOT NULL";
    }

    $order = "affectation.sortie DESC";

    $affectation = new CAffectation();
    return $this->_ref_affectations = $affectation->loadList($where, $order, null, null, $ljoin);
  }

  /**
   * Charge les affectations situées dans un couloir du service
   *
   * @param string $date               Date
   * @param bool   $with_effectue      Avec effectue
   * @param bool   $with_sortie_reelle Avec sortie reelle
   *
   * @return CAffectation[]
   */
  function loadRefsAffectationsCouloir($date, $with_effectue = true, $with_sortie_reelle = false) {
    $ljoin = array();
    $where = array (
      "affectation.service_id" => "= '$this->_id'",
      "affectation.entree" => "<= '$date 23:59:59'",
      "affectation.sortie" => ">= '$date 00:00:00'"
    );

    if (!$with_effectue) {
      if ($with_sortie_reelle) {
        $complement = "";
        if ($date == CMbDT::date()) {
          $ljoin["sejour"] = "affectation.sejour_id = sejour.sejour_id";
          $complement = "OR (sejour.sortie_reelle >= '".CMbDT::dateTime()."' AND affectation.sortie >= '".CMbDT::dateTime()."')";
        }
        $where[] = "affectation.effectue = '0' $complement";
      }
      else {
        $where["affectation.effectue"] = "= '0'";
      }
    }

    $where["affectation.lit_id"] = "IS NULL";

    $order = "affectation.sortie DESC";

    $affectation = new CAffectation();
    return $this->_ref_affectations_couloir = $affectation->loadList($where, $order, null, null, $ljoin);
  }

  /**
   * @return CGroups
   */
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }

  /**
   * @return CSecteur
   */
  function loadRefSecteur() {
    return $this->_ref_secteur = $this->loadFwdRef("secteur_id", true);
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    $this->loadRefsChambres();
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd(){
    $this->loadRefGroup();
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!$this->_ref_group) {
      $this->loadRefsFwd();
    }
    return (CPermObject::getPermObject($this, $permType) && $this->_ref_group->getPerm($permType));
  }

  function validationRepas($date, $listTypeRepas = null){
    $this->_ref_validrepas[$date] = array();
    $validation =& $this->_ref_validrepas[$date];
    if (!$listTypeRepas) {
      $listTypeRepas = new CTypeRepas;
      $order = "debut, fin, nom";
      $listTypeRepas = $listTypeRepas->loadList(null, $order);
    }

    $where               = array();
    $where["date"]       = $this->_spec->ds->prepare(" = %", $date);
    $where["service_id"] = $this->_spec->ds->prepare(" = %", $this->service_id);
    foreach ($listTypeRepas as $keyType=>$typeRepas) {
      $where["typerepas_id"] = $this->_spec->ds->prepare("= %", $keyType);
      $validrepas = new CValidationRepas;
      $validrepas->loadObject($where);
      $validation[$keyType] = $validrepas;
    }
  }

  /**
   * Charge les services d'urgence de l'établissement courant
   *
   * @return CService[]
   */
  static function loadServicesUrgence() {
    $service = new CService();
    $service->group_id = CGroups::loadCurrent()->_id;
    $service->urgence   = "1";
    $service->cancelled = "0";
    /** @var CService[] $services */
    $services = $service->loadMatchingList();
    foreach ($services as $_service) {
      $_service->loadRefsChambres(false);
      foreach ($_service->_ref_chambres as $_chambre) {
        $_chambre->loadRefsLits();
      }
    }

    return $services;
  }

  /**
   * Charge les services d'UHCD de l'établissement courant
   *
   * @return CService[]
   */
  static function loadServicesUHCD() {
    $service = new CService();
    $service->group_id = CGroups::loadCurrent()->_id;
    $service->uhcd      = "1";
    $service->cancelled = "0";
    /** @var CService[] $services */
    $services = $service->loadMatchingList();
    foreach ($services as $_service) {
      $_service->loadRefsChambres();
      foreach ($_service->_ref_chambres as $_chambre) {
        $_chambre->loadRefsBack();
      }
    }

    return $services;
  }

  /**
   * Charge les services d'UHCD de l'établissement courant
   *
   * @return CService[]
   */
  static function loadServicesImagerie() {
    $service = new CService();
    $service->group_id   = CGroups::loadCurrent()->_id;
    $service->radiologie = "1";
    $service->cancelled  = "0";
    /** @var CService[] $services */
    $services = $service->loadMatchingList();

    foreach ($services as $_service) {
      $chambres = $_service->loadRefsChambres();
      foreach ($chambres as $_chambre) {
        $_chambre->loadRefsLits();
      }
    }

    return $services;
  }

  /**
   * Charge les services d'UHCD et d'urgence de l'établissement courant
   *
   * @return CService[]
   */
  static function loadServicesUHCDRPU() {
    $where = array();
    $clause = "uhcd = '1' OR urgence = '1'";
    $where[]            = $clause;
    $where["cancelled"] = " = '0'";
    $service = new CService();
    /** @var CService[] $services */
    $services = $service->loadGroupList($where);
    foreach ($services as $_service) {
      $_service->loadRefsChambres();
      foreach ($_service->_ref_chambres as $_chambre) {
        $_chambre->loadRefsLits();
      }
    }

    return $services;
  }

  /**
   * Charge les services externes de l'établissement
   *
   * @param string $group_id Group
   *
   * @return CService
   */
  static function loadServiceExterne($group_id = null) {
    $service            = new CService();
    $service->group_id  = $group_id ? $group_id : CGroups::loadCurrent()->_id;
    $service->externe   = "1";
    $service->cancelled = "0";
    $service->loadMatchingObject();

    return $service;
  }

  /**
   * Charge le service de radiologie de l'établissement
   *
   * @param string $group_id Group
   *
   * @return CService
   */
  static function loadServiceRadiologie($group_id = null) {
    $service             = new CService();
    $service->group_id   = $group_id ? $group_id : CGroups::loadCurrent()->_id;
    $service->radiologie = "1";
    $service->cancelled  = "0";
    $service->loadMatchingObject();

    return $service;
  }

  function loadListWithPerms($permType = PERM_READ, $where = array(), $order = "nom", $limit = null, $group = null, $ljoin = null) {
    if ($where !== null && !isset($where["group_id"])) {
      $where["group_id"] = "='".CGroups::loadCurrent()->_id."'";
    }

    return parent::loadListWithPerms($permType, $where, $order, $limit, $group, $ljoin);
  }

  /**
   * Construit le tag Service en fonction des variables de configuration
   *
   * @param int $group_id Permet de charger l'id externe d'un Service pour un établissement donné si non null
   *
   * @return string
   */
  static function getTagService($group_id = null) {
    // Pas de tag Mediusers
    if (null == $tag_service = CAppUI::conf("dPhospi tag_service")) {
      return;
    }

    // Permettre des id externes en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }

    return str_replace('$g', $group_id, $tag_service);
  }

  /**
   * @see parent::getDynamicTag
   */
  function getDynamicTag() {
    return CAppUI::conf("dPhospi tag_service");
  }

  /**
   * @return CAffectationUniteFonctionnelle[]
   */
  function loadRefAffectationsUF() {
    /** @var CAffectationUniteFonctionnelle[] $affectations_uf */
    $affectations_uf = $this->loadBackRefs("ufs");
    CStoredObject::massLoadFwdRef($affectations_uf, "uf_id");

    foreach ($affectations_uf as $_aff_uf) {
      $_aff_uf->loadRefUniteFonctionnelle();
    }

    return $this->_ref_affectations_uf = $affectations_uf;
  }

  /**
   * @return CUniteFonctionnelle[]
   */
  function loadRefsUFs() {
    if ($this->_ref_ufs === null) {
      $affectations_uf = $this->loadRefAffectationsUF();

      $this->_ref_ufs = array();
      foreach ($affectations_uf as $_affectation_uf) {
        $_uf                       = $_affectation_uf->_ref_uf;
        $this->_ref_ufs[$_uf->_id] = $_uf;
      }
    }

    return $this->_ref_ufs;
  }

  /**
   * @return CUniteFonctionnelle|null
   */
  function loadRefUFSoins() {
    $ufs = $this->loadRefsUFs();

    $this->_ref_uf_soins = null;
    foreach ($ufs as $_uf) {
      if ($_uf->type === "soins") {
        $this->_ref_uf_soins = $_uf;
      }
    }

    return $this->_ref_uf_soins;
  }

  static function getServicesIdsPref($services_ids = array()) {
    // Détection du changement d'établissement
    $group_id = CValue::get("g");

    if (!$services_ids || $group_id) {
      $group_id = $group_id ? $group_id : CGroups::loadCurrent()->_id;

      $pref_services_ids = json_decode(CAppUI::pref("services_ids_hospi"));

      // Si la préférence existe, alors on la charge
      if (isset($pref_services_ids->{"g$group_id"})) {
        $services_ids = $pref_services_ids->{"g$group_id"};
        $services_ids = explode("|", $services_ids);
        CMbArray::removeValue("", $services_ids);
      }
      // Sinon, chargement de la liste des services en accord avec le droit de lecture
      else {
        $service = new CService();
        $where = array();
        $where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
        $where["cancelled"] = "= '0'";
        $services_ids = array_keys($service->loadListWithPerms(PERM_READ, $where, "externe, nom"));
      }
    }

    if (is_array($services_ids)) {
      CMbArray::removeValue("", $services_ids);
    }

    global $m;
    $save_m = $m;
    foreach (array("dPhospi", "dPadmissions") as $_module) {
      $m = $_module;
      CValue::setSession("services_ids", $services_ids);
    }
    $m = $save_m;

    return $services_ids;
  }
}
