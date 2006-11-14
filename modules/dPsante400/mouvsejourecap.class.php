<?php

global $AppUI;
require_once $AppUI->getModuleClass("dPsante400", "mouvement400");

class CMouvSejourEcap extends CMouvement400 {  
  public $sejour = null;
  public $etablissement = null;
  public $fonction = null;
  public $patient = null;
  public $praticien = null;
  public $naissance = null;
  
  function __construct() {
    $this->base = "ECAPFILE";
    $this->table = "TRSJ0";
    $this->completeMark = ">EFCPSN";
    $this->prodField = "ETAT";
  }
  
  function synchronize() {
//    $this->rec = $this->consume("IDUENR");
  $this->data["CODACT"] = "not mapped yet";
  $this->data["RETPRODST"] = "undefinied";
    
  }
}
?>
