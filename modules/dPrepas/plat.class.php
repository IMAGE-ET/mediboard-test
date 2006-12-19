<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
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
  
  function CPlat() {
    $this->CMbObject("plats", "plat_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "nom"       => "str|notNull",
      "group_id"  => "ref|notNull",
      "type"      => "enum|plat1|plat2|plat3|plat4|plat5|boisson|pain|notNull",
      "typerepas" => "ref|notNull"
    );
  }
  
  function loadRefsFwd() {
    $this->_ref_typerepas = new CTypeRepas;
    $this->_ref_typerepas->load($this->typerepas);
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  function canDelete(&$msg, $oid = null) {
    global $AppUI;
    $select       = "\nSELECT COUNT(DISTINCT repas.repas_id) AS number";
    $from        = "\nFROM repas ";
    $sql_where   = "\nWHERE (`$this->type` IS NOT NULL AND `$this->type` = '$this->plat_id')";
    
    $sql = $select . $from . $sql_where;
    $obj = null;
    
    if (!db_loadObject($sql, $obj)) {
      $msg = db_error();
      return false;
    }
    if ($obj->number) {
      $msg = $AppUI->_("noDeleteRecord") . ": " . $obj->number . " repas";
      return false;
    }
    return true;
  }
}
?>