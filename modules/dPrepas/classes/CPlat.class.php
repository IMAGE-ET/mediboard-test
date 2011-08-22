<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dPrepas
 *  @version $Revision$
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
  
  function getBackProps() {
   $backProps = parent::getBackProps();
   $backProps["repas1"] = "CRepas plat1";
   $backProps["repas2"] = "CRepas plat2";
   $backProps["repas3"] = "CRepas plat3";
   $backProps["repas4"] = "CRepas plat4";
   $backProps["repas5"] = "CRepas plat5";
   $backProps["repas_boisson"] = "CRepas boisson";
   $backProps["repas_pain"] = "CRepas pain";
   return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["nom"]       = "str notNull";
    $specs["group_id"]  = "ref notNull class|CGroups";
    $specs["type"]      = "enum notNull list|plat1|plat2|plat3|plat4|plat5|boisson|pain";
    $specs["typerepas"] = "ref notNull class|CTypeRepas";
    return $specs;
  }
  
  function loadRefsFwd() {
    $this->_ref_typerepas = new CTypeRepas;
    $this->_ref_typerepas->load($this->typerepas);
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
    
  //  @todo: refactor this !
  function canDeleteEx() {
    $query       = "SELECT COUNT(DISTINCT repas.repas_id) AS number
                    FROM repas WHERE (`$this->type` IS NOT NULL AND `$this->type` = '$this->plat_id')";
    $obj = null;
    if (!$this->_spec->ds->loadObject($query, $obj)) {
      return $this->_spec->ds->error();
    }
    if ($obj->number) {
      return CAppUI::tr("CMbObject-msg-nodelete-backrefs") . ": " . $obj->number . " repas";
    }
    return null;
  }
}
?>