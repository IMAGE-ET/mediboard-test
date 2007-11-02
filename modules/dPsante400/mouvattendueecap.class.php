<?php

global $AppUI;
require_once $AppUI->getModuleClass("dPsante400", "mouvsejourecap");

class CMouvAttendueECap extends CMouvSejourEcap {  
  
  function __construct() {
    parent::__construct();
    $this->base = "ECAPFILE";
    $this->table = "TRATT";
    $this->prodField = "ETAT";
    $this->idField = "INDEX";
    $this->typeField = "TRACTION";
    $this->groupField = "CIDC";
  }

  function synchronize() {
    $this->syncEtablissement();
    $this->syncFonction();
    
    // Praticien du séjour si aucune DHE
    $this->syncPatient();
//    $this->syncDHE();
//    $this->syncSej();
//    $this->syncOperations();
//    $this->syncNaissance();
  }
      
  function syncDHE() {
    parent::syncDHE();
  }
  
  function syncOperations() {
    parent::syncOperations();
  }

  function syncActes($CINT) {
    parent::syncActes($CINT);
  }

  function syncSej() {
    parent::syncSej();
  }
}
?>
