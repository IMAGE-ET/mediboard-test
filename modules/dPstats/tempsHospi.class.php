<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPStats
 *  @version $Revision: $
 *  @author Romain OLLIVIER
 */

/**
 * The CTempsHospi class
 */
class CTempsHospi extends CMbObject {
  // DB Table key
  var $temps_hospi_id = null;
  
  // DB Fields
  var $praticien_id = null;
  var $type         = null;
  var $ccam         = null;
  var $nb_sejour    = null;
  var $duree_moy    = null;
  var $duree_ecart  = null;
  
  // Object References
  var $_ref_praticien = null;

  
  function CTempsHospi() {
    $this->CMbObject("temps_hospi", "temps_hospi_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }   

  function getSpecs() {
    return array (
      "praticien_id" => "ref|notNull",
      "type"         => "enum|comp|ambu|seances|ssr|psy|notNull",
      "nb_sejour"    => "num|pos",
      "duree_moy"    => "currency|pos",
      "duree_ecart"  => "currency|pos",
      "ccam"         => "str"
    );
  } 

  function loadRefsFwd(){
    $this->_ref_praticien = new CMediusers;
    $this->_ref_praticien->load($this->praticien_id);
  }
  
  function getTime($praticien_id = 0, $ccam = null, $type = null){
    $time = 0;
    $where = array();
    $total = array();
    $total["duree_somme"]  = 0;
    $total["nbSejours"]    = 0;
    $where["praticien_id"] = "= '$praticien_id'";
    if($type) {
      $where["type"] = "= '$type'";
    }
    
    if(is_array($ccam)) {
      foreach($ccam as $keyccam => $code){
        $where[] = "ccam LIKE '%".strtoupper($code)."%'";
      }
    } elseif($ccam) {
      $where["ccam"] = "LIKE '%".strtoupper($ccam)."%'";
    }
    
    $temp = new CTempsHospi;
    $liste = $temp->loadList($where);
    foreach($liste as $keyTemps => $temps) {
      $total["nbSejours"]   += $temps->nb_sejour;
      $total["duree_somme"] += $temps->nb_sejour * $temps->duree_moy;
    }
    if($total["nbSejours"]) {
      $time = $total["duree_somme"] / $total["nbSejours"];
    } else {
      $time = 0;
    }
    return $time;
  }
}
?>