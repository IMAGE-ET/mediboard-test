<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPhospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

/**
 * Classe CService.
 * @abstract Gère les services d'hospitalisation
 * - contient de chambres
 */
class CService extends CMbObject {
  // DB Table key
  public $service_id;

  // DB references
  public $group_id;
  public $responsable_id;
  public $secteur_id;

  // DB Fields
  public $nom;
  public $type_sejour;
  public $description;
  public $cancelled;
  public $hospit_jour;
  public $urgence;
  public $uhcd;
  public $externe;
  public $neonatalogie;

  /**
   * @var CChambre[]
   */
  public $_ref_chambres;

  /**
   * @var CGroups
   */
  public $_ref_group;
  public $_ref_validrepas;

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

    $props["nom"]          = "str notNull seekable";
    $props["description"]  = "text seekable";
    $props["urgence"]      = "bool default|0";
    $props["uhcd"]         = "bool default|0";
    $props["hospit_jour"]  = "bool default|0";
    $props["externe"]      = "bool default|0";
    $props["cancelled"  ]  = "bool default|0";
    $props["neonatalogie"] = "bool default|0";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

  function store(){
    $is_new = !$this->_id;

    if ($msg = parent::store()) {
      return $msg;
    }

    if ($is_new) {
      CConfigService::emptySHM();
      CConfigMomentUnitaire::emptySHM();
    }
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
   * @return self[]
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

    return $this->_ref_chambres = $this->_back["chambres"] = $chambre->loadList($where, "nom");
  }

  /**
   * @return CGroups
   */
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }

  function loadRefsBack() {
    $this->loadRefsChambres();
  }

  function loadRefsFwd(){
    $this->loadRefGroup();
  }

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
   * Charge les services d'UHCD et d'urgence de l'établissement courant
   *
   * @return CService[]
   */
  static function loadServicesUHCDRPU() {
    $where = array();
    $where[]            = "uhcd = '1' OR urgence = '1'";
    $where["cancelled"] = " = '0'";
    $service = new CService();
    /** @var CService[] $services */
    $services = $service->loadGroupList($where);
    foreach ($services as $_service) {
      $_service->loadRefsChambres();
      foreach ($_service->_ref_chambres as $_chambre) {
        $_chambre->loadRefsBack();
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
}
