<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
 *  @author Thomas Despoix
*/

/**
 * Classe CService. 
 * @abstract Gère les services d'hospitalisation
 * - contient de chambres
 */
class CService extends CMbObject {
  // DB Table key
	var $service_id = null;	
  
  // DB references
  var $group_id       = null;
  var $responsable_id = null;

  // DB Fields
  var $nom         = null;
  var $type_sejour = null;
  var $description = null;
  var $cancelled   = null;
  var $hospit_jour = null;
  var $urgence     = null;
  var $uhcd        = null;
	
  
  // Object references
  var $_ref_chambres = null;
  var $_ref_group    = null;
  var $_ref_validrepas = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'service';
    $spec->key   = 'service_id';
    return $spec;
  }

	function getBackProps() {
	  $backProps = parent::getBackProps();
    $backProps["chambres"]               = "CChambre service_id";
    $backProps["sejours"]                = "CSejour service_id";
    $backProps["protocoles"]             = "CProtocole service_id";
    $backProps["product_deliveries"]     = "CProductDelivery service_id";
	  $backProps["product_stock_services"] = "CProductStockService service_id";
	  $backProps["valid_repas"]            = "CValidationRepas service_id";
	  $backProps["config_moment"]          = "CConfigMomentUnitaire service_id";
	  $backProps["config_service"]         = "CConfigService service_id";
    $backProps["endowments"]             = "CProductEndowment service_id";
		$backProps["services_entree"]        = "CSejour service_entree_mutation_id";
		$backProps["services_sortie"]        = "CSejour service_mutation_id";
	  return $backProps;
	}

  function getProps() {
  	$props = parent::getProps();
    $props["group_id"]       = "ref notNull class|CGroups";
    $props["responsable_id"] = "ref notNull class|CMediusers";

    $sejour = new CSejour;
    $props["type_sejour"] = CMbString::removeToken($sejour->_props["type"], " ", "notNull");

    $props["nom"]         = "str notNull seekable";
    $props["description"] = "text seekable";
    $props["urgence"]     = "bool";
    $props["uhcd"]        = "bool";
    $props["hospit_jour"] = "bool";
    $props["cancelled"  ] = "bool";

    return $props;
  }
  
  function updateFormFields() {
  	parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  /**
   * Load list overlay for current group
   */
  function loadGroupList($where = array(), $order = 'nom', $limit = null, $groupby = null, $ljoin = array()) {
    // Filtre sur l'établissement
		$group = CGroups::loadCurrent();
		$where["group_id"] = "= '$group->_id'";
    
    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }

  function loadRefsChambres() {
  	return $this->_ref_chambres = $this->loadBackRefs("chambres", "nom");
  }

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
    if(!$this->_ref_group) {
      $this->loadRefsFwd();
    }
    return (CPermObject::getPermObject($this, $permType) && $this->_ref_group->getPerm($permType));
  }
   
  function validationRepas($date, $listTypeRepas = null){
    $this->_ref_validrepas[$date] = array();
    $validation =& $this->_ref_validrepas[$date];
    if(!$listTypeRepas){
      $listTypeRepas = new CTypeRepas;
      $order = "debut, fin, nom";
      $listTypeRepas = $listTypeRepas->loadList(null,$order);
    }
    
    $where               = array();
    $where["date"]       = $this->_spec->ds->prepare(" = %", $date);
    $where["service_id"] = $this->_spec->ds->prepare(" = %", $this->service_id);
    foreach($listTypeRepas as $keyType=>$typeRepas){
      $where["typerepas_id"] = $this->_spec->ds->prepare("= %",$keyType);
      $validrepas = new CValidationRepas;
      $validrepas->loadObject($where);
      $validation[$keyType] = $validrepas;
    }
  }
  
  /**
   * Charge les services d'urgence de l'établissement courant
   * @return array|CService
   */
  static function loadServicesUrgence() {
    global $g;
    $service = new CService();
    $service->group_id = $g;
    $service->urgence = "1";
    $services = $service->loadMatchingList();
    foreach ($services as $_service) {
      $_service->loadRefsBack();
      foreach ($_service->_ref_chambres as $_chambre) {
        $_chambre->loadRefsBack();
      }
    }
    
    return $services;
  }
  
  function loadListWithPerms($permType = PERM_READ, $where = array(), $order = "nom", $limit = null, $group = null, $leftjoin = null) {
    if ($where !== null && !isset($where["group_id"])) {
      $where["group_id"] = "='".CGroups::loadCurrent()->_id."'";
    }
    
    return parent::loadListWithPerms($permType, $where, $order, $limit, $group, $leftjoin);
  }
}
?>