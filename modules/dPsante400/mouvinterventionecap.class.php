<?php

global $AppUI;
require_once $AppUI->getModuleClass("dPsante400", "mouvsejourecap");

class CMouvInterventionECap extends CMouvSejourEcap {  
  
  function __construct() {
    $this->base = "ECAPFILE";
    $this->table = "TRSJ0";
    $this->prodField = "ETAT";
    $this->idField = "INDEX";
    $this->typeField = "TRACTION";
    $this->groupField = "CIDC";
  }

  function synchronize() {
    parent::synchronize();
  }
    
  function syncDHE() {
    parent::syncDHE();
  }
  
  function syncOperations() {
    $query = "SELECT * " .
        "\nFROM $this->base.ECINPF " .
        "\nWHERE INCIDC = ? " .
        "\nAND ((INNDOS = ? AND INDMED = ?) OR INCINT = ?)";

    $values = array (
      $this->id400EtabECap->id400,
      $this->id400Sej->id400,
      $this->id400Pat->id400,
      $this->dheCIDC,
    );
    
    // Recherche des opérations
    $opersECap = CRecordSante400::multipleLoad($query, $values);
    foreach ($opersECap as $operECap) {
      $this->trace($operECap->data, "Opération trouvée"); 

      $operECap->valuePrefix = "IN";
      
      $operation = new COperation;
      $operation->sejour_id = $this->sejour->_id;
      $operation->chir_id = $this->sejour->praticien_id;
      
      // Côté indeterminé pour le moment
      $operation->cote = "total";

      // Entrée/sortie prévue/réelle
      $entree_prevue = $operECap->consumeDateTime("DTEP", "HREP");
      $sortie_prevue = $operECap->consumeDateTime("DTSP", "HRSP");
      $entree_reelle = $operECap->consumeDateTime("DTER", "HREM");
      $sortie_reelle = $operECap->consumeDateTime("DTSR", "HRSR");

      $duree_prevue = $sortie_prevue > $entree_prevue ? 
        mbTimeRelative($entree_prevue, $sortie_prevue) : 
        "01:00:00"; 
        
      $operation->date = mbDate($entree_prevue);
      $operation->time_operation = mbTime($entree_prevue);
      $operation->temp_operation = $duree_prevue;
      $operation->entree_salle = mbTime($entree_reelle);
      $operation->sortie_salle = mbTime($sortie_reelle);
      
      // Anesthésiste
      if ($CPRT = $operECap->consume("CPRT")) {
        $this->syncPraticien($CPRT);
        $operation->anesth_id = $this->praticiens[$CPRT]->_id;
      }
      
      // Textes
      $operation->libelle = $operECap->consume("CNAT");
      $operation->rques   = $operECap->consume("CCOM");
            
      // Dossier d'anesthésie
      $CASA = $operECap->consume("CASA"); // A mettre dans une CConsultAnesth
      
      // Gestion des id400
      $CINT = $operECap->consume("CINT");
      $tags = array (
        "CINT",
        "CIDC:{$this->id400EtabECap->id400}"
      );
      $id400Oper = new CIdSante400();
      $id400Oper->id400 = $CINT;
      $id400Oper->tag = join($tags, " ");

      $this->trace($operation->getProps(), "Opération à enregistrer");

      $id400Oper->bindObject($operation);
      
      $this->id400Opers[$CINT] = $id400Oper;      
      
      $this->operations[$CINT] = $operation;
      $this->syncActes($CINT);
    }

    // Status
    $this->markStatus(self::STATUS_OPERATION, count($opersECap));
    if (!count($opersECap)) {
      $this->markStatus(self::STATUS_ACTES, 0);
    }
  }

  function syncActes($CINT) {
    parent::syncActes($CINT);
  }

  function syncSejour() {
    parent::syncSejour();
  }
}
?>
