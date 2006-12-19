<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CMenu class
 */
class CMenu extends CMbObject {
  // DB Table key
  var $menu_id     = null;
    
  // DB Fields
  var $nom         = null;
  var $group_id    = null;
  var $typerepas   = null;
  var $plat1       = null;
  var $plat2       = null;
  var $plat3       = null;
  var $plat4       = null;
  var $plat5       = null;
  var $boisson     = null;
  var $pain        = null;
  var $diabete     = null;
  var $sans_sel    = null;
  var $sans_residu = null;
  var $modif       = null;
  var $debut       = null;
  var $fin         = null;
  var $repetition  = null;
  
  // Object References
  var $_ref_typerepas = null;
  
  function CMenu() {
    $this->CMbObject("menu", "menu_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "nom"         => "str|notNull",
      "group_id"    => "ref|notNull",
      "typerepas"   => "ref|notNull",
      "plat1"       => "str",
      "plat2"       => "str",
      "plat3"       => "str",
      "plat4"       => "str",
      "plat5"       => "str",
      "boisson"     => "str",
      "pain"        => "str",
      "diabete"     => "bool",
      "sans_sel"    => "bool",
      "sans_residu" => "bool",
      "modif"       => "bool",
      "debut"       => "date|notNull",
      "fin"         => "date|moreThan|debut|notNull",
      "repetition"  => "num|pos|notNull"
    );
  }
  
  function loadByDate($date, $typerepas_id = null){
    global $g;
    $where = array();
    if($typerepas_id){
      $where["typerepas"] = db_prepare("= %",$typerepas_id);
    }
    $where["group_id"] = db_prepare("= %",$g);
    $where["debut"]    = db_prepare("<= %",$date);
    $where["fin"]      = db_prepare(">= %",$date);
    $order = "nom";
    
    $listRepas = new CMenu;
    $listRepas = $listRepas->loadList($where, $order);
    foreach($listRepas as $keyRepas => &$repas){
      if(!$repas->is_actif($date)){
        unset($listRepas[$keyRepas]);
      }
    }
    return $listRepas;
  }
  
  function is_actif($date){
    if($date < $this->debut || $date > $this->fin){
      return false;
    }
    
    $nbDays  = mbDaysRelative($this->debut, $date);
    $nbWeeks = floor($nbDays / 7);
    if (!$nbWeeks || !fmod($nbWeeks, $this->repetition)) {
      return true;
    }
    return false;
  }
  
  function loadRefsFwd() {
    $this->_ref_typerepas = new CTypeRepas;
    $this->_ref_typerepas->load($this->typerepas);
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  function updateDBFields() {
    if($this->debut){
      $this->debut = mbDate("last sunday", $this->debut);
      $this->debut = mbDate("+1 day", $this->debut);
    }
    if($this->fin){
      $this->fin = mbDate("next monday", $this->fin);
      $this->fin = mbDate("-1 day", $this->fin);
    }
  }
}
?>