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
    $this->idField = "INDEX";
    $this->typeField = "TRACTION";
  }
  
  function synchronize() {
    // Etablissement
    $A_CIDC = $this->consume("A_CIDC");
    $etab400 = new CRecordSante400();
    $etab400->query("SELECT * FROM $this->base.ECCIPF WHERE CICIDC = $A_CIDC");
    mbTrace($etab400->data, "Data Etablissement à consommer");

    $this->etablissement = new CGroups;
    $this->etablissement->text           = $etab400->consume("CIZIDC");
    $this->etablissement->raison_sociale = $this->etablissement->text;
    $this->etablissement->adresse        = $etab400->consumeMulti("CIZAD1", "CIZAD2");
    $this->etablissement->cp             = $etab400->consume("CICPO");
    $this->etablissement->ville          = $etab400->consume("CIZLOC");
    $this->etablissement->tel            = $etab400->consume("CIZTEL");
    $this->etablissement->fax            = $etab400->consume("CIZFAX");
    $this->etablissement->web            = $etab400->consume("CIZWEB");
    $this->etablissement->mail           = $etab400->consume("CIMAIL");
    $this->etablissement->domiciliation  = $etab400->consume("CIFINS");

    $id400Etab = new CIdSante400();
    $id400Etab->id400 = $etab400->consume("CICIDC");
    $id400Etab->bindObject($this->etablissement);
    mbTrace($etab400->data, "Data Etablissement à non consommée");
    
    $this->markStatus("E");
    
    
  }
}
?>
