<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CTempsOp class
 */
class CTempsOp extends CMbObject {
  // DB Table key
  var $temps_op_id = null;
  
  // DB Fields
  var $chir_id         = null;
  var $ccam            = null;
  var $nb_intervention = null;
  var $estimation      = null;
  var $occup_moy       = null;
  var $occup_ecart     = null;
  var $duree_moy       = null;
  var $duree_ecart     = null;
  
  // Object References
  var $_ref_praticien = null;

  
  function CTempsOp() {
    $this->CMbObject("temps_op", "temps_op_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }	  

  function getSpecs() {
    return array (
      "temps_op_id"     => "ref",
      "chir_id"         => "ref",
      "nb_intervention" => "num|pos",
      "estimation"      => "time",
      "occup_moy"       => "time",
      "occup_ecart"     => "time",
      "duree_moy"       => "time",
      "duree_ecart"     => "time"
    );
  }	
  
  
  function loadRefsFwd(){
    $this->_ref_praticien = new CMediusers;
    $this->_ref_praticien->load($this->chir_id);
  }
  
  function getTime($chir_id = 0, $ccam = null){
    $time = 0;
    $where = array();
    $total = array();
    $total["occup_somme"] = 0;
    $total["nbInterventions"] = 0;
    $where["chir_id"] = "= '$chir_id'";
    
    if(is_array($ccam)){
      foreach($ccam as $keyccam => $code){
        $where[] = "ccam LIKE '%".strtoupper($code)."%'";
      }
    }elseif($ccam){
      $where["ccam"] = "LIKE '%".strtoupper($ccam)."%'";
    }
    
    $temp = new CTempsOp;
    $liste = $temp->loadList($where);
    foreach($liste as $keyTemps => $temps) {
      $total["nbInterventions"] += $temps->nb_intervention;
      $total["occup_somme"] += $temps->nb_intervention * strtotime($temps->occup_moy);
    }
    if($total["nbInterventions"]) {
      $time = $total["occup_somme"] / $total["nbInterventions"];
    } else {
      $time = 0;
    }
    return $time;
  }
}
?>