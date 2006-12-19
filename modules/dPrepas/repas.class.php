<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
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
  
  function CRepas() {
    $this->CMbObject("repas", "repas_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "affectation_id" => "ref|notNull",
      "menu_id"        => "ref|notNull",
      "plat1"          => "ref",
      "plat2"          => "ref",
      "plat3"          => "ref",
      "plat4"          => "ref",
      "plat5"          => "ref",
      "boisson"        => "ref",
      "pain"           => "ref",
      "date"           => "date"
    );
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
  
  function loadRefsFwd() {
    $this->_ref_affectation = new CAffectation;
    $this->_ref_affectation->load($this->affectation_id);
    
    $this->loadRefMenu();
  }
}
?>