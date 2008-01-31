<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

require_once("bcbObject.class.php");

class CBcbComposition extends CBcbObject {

  // Spéciale composition
  var $exprime_par      = null;
  var $real_exprime_par = null;
  var $excipients       = null;
  var $principes_actifs = null;

  
  // Constructeur
  function CBcbComposition(){
    $this->distClass = "BCBComposition";
    parent::__construct();
  }
 
  // Chargement d'une composition a partir d'un code CIP
  function load($CIP){
    $this->distObj->Search($CIP);
    $this->exprime_par = $this->distObj->DataCompo->ExprimePar;
    $this->real_exprime_par = $this->distObj->DataCompo->RealExprimePar;
    $this->excipients = $this->distObj->DataCompo->Excipients;
    $this->principes_actifs = $this->distObj->DataCompo->PA;
  }
}
