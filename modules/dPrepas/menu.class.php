<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPrepas
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
  var $repetition  = null;
  var $nb_repet    = null;
  
  // Object References
  var $_ref_typerepas = null;
  
  function CMenu() {
    $this->CMbObject("menu", "menu_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["repas"] = "CRepas menu_id";
     return $backRefs;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "nom"         => "notNull str",
      "group_id"    => "notNull ref class|CGroups",
      "typerepas"   => "notNull ref class|CTypeRepas",
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
      "debut"       => "notNull date",
      "repetition"  => "notNull num pos",
      "nb_repet"    => "notNull num pos"
    );
    return array_merge($specsParent, $specs);
  }
  
  function loadByDate($date, $typerepas_id = null){
    global $g;
    $where = array();
    if($typerepas_id){
      $where["typerepas"] = $this->_spec->ds->prepare("= %",$typerepas_id);
    }
    $where["group_id"] = $this->_spec->ds->prepare("= %",$g);
    $where["debut"]    = $this->_spec->ds->prepare("<= %",$date);
    //$where["fin"]      = $this->_spec->ds->prepare(">= %",$date);
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
    $date_debut = mbDate("last sunday", $this->debut);
    $date_debut = mbDate("+1 day"     , $date_debut);
    $numDayMenu  = mbDaysRelative($date_debut, $this->debut);
    
    $nb_weeks = (($this->nb_repet * $this->repetition) - 1);
    $date_fin = mbDate("+$nb_weeks week" , $date_debut);
    $date_fin = mbDate("next monday"     , $date_fin);
    $date_fin = mbDate("-1 day"          , $date_fin);
    
    if($date < $this->debut || $date > $date_fin){  
      return false;
    }
    
    $nbDays  = mbDaysRelative($date_debut, $date);
    $nbWeeks = floor($nbDays / 7);
    $numDay = $nbDays - ($nbWeeks * 7);
    if (!$nbWeeks || !fmod($nbWeeks, $this->repetition)) {
      if($numDay == $numDayMenu){
        return true;
      }
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
  }
}
?>