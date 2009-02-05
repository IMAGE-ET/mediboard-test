<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPrepas
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CPlat class
 */
class CPlat extends CMbObject {
  // DB Table key
  var $plat_id   = null;
    
  // DB Fields
  var $group_id  = null;
  var $nom       = null;
  var $type      = null;
  var $typerepas = null;
  
  // Object References
  var $_ref_typerepas   = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'plats';
    $spec->key   = 'plat_id';
    return $spec;
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["repas1"] = "CRepas plat1";
      $backRefs["repas2"] = "CRepas plat2";
      $backRefs["repas3"] = "CRepas plat3";
      $backRefs["repas4"] = "CRepas plat4";
      $backRefs["repas5"] = "CRepas plat5";
      $backRefs["repas_boisson"] = "CRepas boisson";
      $backRefs["repas_pain"] = "CRepas pain";
     return $backRefs;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "nom"       => "str notNull",
      "group_id"  => "ref notNull class|CGroups",
      "type"      => "enum notNull list|plat1|plat2|plat3|plat4|plat5|boisson|pain",
      "typerepas" => "ref notNull class|CTypeRepas"
    );
    return array_merge($specsParent, $specs);
  }
  
  function loadRefsFwd() {
    $this->_ref_typerepas = new CTypeRepas;
    $this->_ref_typerepas->load($this->typerepas);
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
    
  function canDeleteEx() {
    global $AppUI;
    $select       = "\nSELECT COUNT(DISTINCT repas.repas_id) AS number";
    $from        = "\nFROM repas ";
    $sql_where   = "\nWHERE (`$this->type` IS NOT NULL AND `$this->type` = '$this->plat_id')";
    
    $sql = $select . $from . $sql_where;
    $obj = null;
    
    if (!$this->_spec->ds->loadObject($sql, $obj)) {
      return $this->_spec->ds->error();
    }
    if ($obj->number) {
      return CAppUI::tr("CMbObject-msg-nodelete-backrefs") . ": " . $obj->number . " repas";
    }
    return null;
  }
}
?>