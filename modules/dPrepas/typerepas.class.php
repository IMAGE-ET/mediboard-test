<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPrepas
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CTypeRepas class
 */
class CTypeRepas extends CMbObject {
  // DB Table key
  var $typerepas_id = null;
    
  // DB Fields
  var $group_id  = null;
  var $nom       = null;
  var $debut     = null;
  var $fin       = null;
  
  // Form fields
  var $_debut = null;
  var $_fin   = null;
  
  function CTypeRepas() {
    $this->CMbObject("repas_type", "typerepas_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "nom"      => "str|notNull",
      "group_id" => "ref|notNull",
      "debut"    => "time|notNull",
      "fin"      => "time|moreThan|debut|notNull"
    );
  }
  
  function updateDBFields() {
    if($this->_debut !== ""){
      $this->debut = $this->_debut .":00";
    }
    if($this->_fin){
      $this->fin = $this->_fin .":00";
    }
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view  = $this->nom;
    $this->_debut = substr($this->debut, 0, 2);
    $this->_fin   = substr($this->fin  , 0, 2);
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "menu(s)", 
      "name"      => "menu",
      "idfield"   => "menu_id", 
      "joinfield" => "typerepas"
    );
    $tables[] = array (
      "label"     => "plat(s)", 
      "name"      => "plats",
      "idfield"   => "plat_id", 
      "joinfield" => "typerepas"
    );
    return parent::canDelete( $msg, $oid, $tables );
  }
}
?>