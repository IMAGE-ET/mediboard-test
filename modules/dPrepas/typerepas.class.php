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
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'repas_type';
    $spec->key   = 'typerepas_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["menus"] = "CMenu typerepas";
    $backProps["plats"] = "CPlat typerepas";
    $backProps["repas"] = "CRepas typerepas_id";
    $backProps["valid_repas"] = "CValidationRepas typerepas_id";
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["nom"]      = "str notNull";
    $specs["group_id"] = "ref notNull class|CGroups";
    $specs["debut"]    = "time notNull";
    $specs["fin"]      = "time notNull moreThan|debut";
    $specs["_debut"]   = "time notNull";
    $specs["_fin"]     = "time notNull moreThan|_debut";
    return $specs;
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
}
?>