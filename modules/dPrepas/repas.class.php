<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPrepas
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CRepas class
 */
class CRepas extends CMbObject {
  // DB Table key
  var $repas_id     = null;
    
  // DB Fields
  var $affectation_id = null;
  var $menu_id        = null;
  var $plat1          = null;
  var $plat2          = null;
  var $plat3          = null;
  var $plat4          = null;
  var $plat5          = null;
  var $boisson        = null;
  var $pain           = null;
  var $date           = null;
  var $typerepas_id   = null;
  var $modif          = null;
  
  // Object References
  var $_ref_affectation = null;
  var $_ref_menu        = null;
  var $_ref_plat1       = null;
  var $_ref_plat2       = null;
  var $_ref_plat3       = null;
  var $_ref_plat4       = null;
  var $_ref_plat5       = null;
  var $_ref_boisson     = null;
  var $_ref_pain        = null;
  
  // Form fields
  var $_is_modif        = null;
  var $_no_synchro      = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'repas';
    $spec->key   = 'repas_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "affectation_id" => "ref notNull class|CAffectation",
      "menu_id"        => "ref class|CMenu",
      "plat1"          => "ref class|CPlat",
      "plat2"          => "ref class|CPlat",
      "plat3"          => "ref class|CPlat",
      "plat4"          => "ref class|CPlat",
      "plat5"          => "ref class|CPlat",
      "boisson"        => "ref class|CPlat",
      "pain"           => "ref class|CPlat",
      "date"           => "date",
      "typerepas_id"   => "ref notNull class|CTypeRepas",
      "modif"          => "bool"
    );
    return array_merge($specsParent, $specs);
  }
  
  function check(){
    $msg = parent::check();
    if(!$msg){
      $where = array();
      $where["date"]           = $this->_spec->ds->prepare("= %", $this->date);
      $where["affectation_id"] = $this->_spec->ds->prepare("= %", $this->affectation_id);
      $where["typerepas_id"]   = $this->_spec->ds->prepare("= %", $this->typerepas_id);
      if($this->repas_id){
        $where["repas_id"]   = $this->_spec->ds->prepare("!= %", $this->repas_id);
      }
      $select = "count(`".$this->_spec->key."`) AS `total`";
      
      $sql = new CRequest();
      $sql->addTable($this->_spec->table);
      $sql->addSelect($select);
      $sql->addWhere($where);
      
      $nbRepas = $this->_spec->ds->loadResult($sql->getRequest());
      
      if($nbRepas){
        $msg .= "Un repas a déjà été créé, vous ne pouvez pas en créer un nouveau.";
      }
    }
    return $msg;
  }
  
  function store() {
    $this->updateDBFields();
    if(!$this->_no_synchro){
      $service = $this->getService();
      $where = array();
      $where["date"]         = $this->_spec->ds->prepare("= %", $this->date);
      $where["service_id"]   = $this->_spec->ds->prepare("= %", $service->_id);
      $where["typerepas_id"] = $this->_spec->ds->prepare("= %", $this->typerepas_id);
      $validationrepas = new CValidationRepas;
      $validationrepas->loadObject($where);
      if($validationrepas->validationrepas_id){
        $validationrepas->modif = 1;
        $validationrepas->store();
        $this->modif = 1;
      }
    }
    return parent::store();
  }
  
  function loadRemplacements(){
    $this->_ref_plat1   = new CPlat;
    $this->_ref_plat2   = new CPlat;
    $this->_ref_plat3   = new CPlat;
    $this->_ref_plat4   = new CPlat;
    $this->_ref_plat5   = new CPlat;
    $this->_ref_boisson = new CPlat;
    $this->_ref_pain    = new CPlat;
    
    $this->_ref_plat1->load($this->plat1);
    $this->_ref_plat2->load($this->plat2);
    $this->_ref_plat3->load($this->plat3);
    $this->_ref_plat4->load($this->plat4);
    $this->_ref_plat5->load($this->plat5);
    $this->_ref_boisson->load($this->boisson);
    $this->_ref_pain->load($this->pain);
    
    if($this->plat1 || $this->plat2 || $this->plat3 || $this->plat4 || $this->plat5 || $this->boisson || $this->pain){
      $this->_is_modif = true;
    }
  }
  
  function loadRefMenu(){
    $this->_ref_menu = new CMenu;
    $this->_ref_menu->load($this->menu_id);
  }
  
  function getService(){
    $this->loadRefAffectation();
    $this->_ref_affectation->loadRefLit();
    $this->_ref_affectation->_ref_lit->loadCompleteView();
    return $this->_ref_affectation->_ref_lit->_ref_chambre->_ref_service;
  }
  
  function loadRefAffectation(){
    $this->_ref_affectation = new CAffectation;
    $this->_ref_affectation->load($this->affectation_id);
  }
  
  function loadRefsFwd() {
    $this->loadRefAffectation();
    $this->loadRefMenu();
  }
}
?>