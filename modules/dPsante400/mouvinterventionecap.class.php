<?php

global $AppUI;
require_once $AppUI->getModuleClass("dPsante400", "mouvsejourecap");

class CMouvInterventionECap extends CMouvSejourEcap {  
  
  function __construct() {
    parent::__construct();
    $this->base = "ECAPFILE";
    $this->table = "TRINT";
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
    $this->findSejour();
    $this->syncSej();
    $this->syncDHE();
    $this->syncOperation();
    $this->syncNaissance();
  }
  
  /**
   * Find Mb sejour associated with DHE or Sej
   */
  function findSejour() {
    // Séjour DHE eCap
    $IDAT = $this->consume("IDAT");
    $tags[] = "eCap";
    $tags[] = "DHE";
    $tags[] = "CIDC:{$this->id400Etab->id400}";
    $this->id400DHE = new CIdSante400();
    $this->id400DHE->id400 = $IDAT;
    $this->id400DHE->object_class = "CSejour";
    $this->id400DHE->tag = join(" ", $tags);
    
    $sejourDHE = $this->id400DHE->getMbObject();
    
    // Séjour Sej eCap
    $NDOS = $this->consume("NDOS");
    $tags[] = "eCap";
    $tags[] = "NDOS";
    $tags[] = "CIDC:{$this->id400Etab->id400}";
    $this->id400Sej = new CIdSante400();
    $this->id400Sej->id400 = $NDOS;
    $this->id400Sej->object_class = "CSejour";
    $this->id400Sej->tag = join(" ", $tags);
    
    // Ne pas charger les séjours en cache
    $sejourSej = $this->id400Sej->getMbObject();
    
    // Vérifier que c'est le même si existant
    if ($sejourDHE->_id &&  $sejourSej->_id && $sejourDHE->_id != $sejourSej->_id) {
      throw new Exception("2 séjours différents trouvés pour DHE et Séjour");
    }
    
    // On choisit le Sejour DHE
    if ($sejourDHE->_id) {
      $this->sejour = $sejourDHE;
    }

    // On choisit le séjour Sej
    if ($sejourSej->_id) {
      $this->sejour = $sejourSej;
    }
    
    // Aucun trouvé, il faut aller chercher dans la base eCap
    if (!$sejourDHE->_id && !$sejourSej->_id) {
      $this->sejour = new CSejour;
      $this->sejour->group_id = $this->etablissement->_id;
      $this->sejour->patient_id = $this->patient->_id;
    }
  }
      
  function syncDHE() {
    // L'intervention n'a pas de séjour
    if ("0" == $this->id400DHE->id400) {
      $this->trace("0", "Attendu inexistant");
      $this->setStatus(self::STATUS_SEJOUR);
      return;
    }
    
    // Déjà synchronisé, on laisse
    if ($this->id400DHE->_id) {
      $this->markStatus(self::STATUS_SEJOUR);
      $this->markStatus(self::STATUS_PRATICIEN);
      return;
    }

    // Chargement de la DHE
    $values = array (
      $this->id400Etab->id400,
      $this->id400DHE->id400,
    );
    
    $query = "SELECT * " .
        "\nFROM $this->base.ECATPF " .
        "\nWHERE ATCIDC = ? " .
        "\nAND ATIDAT = ? ";

    // Recherche de la DHE
    $dheECap = new CRecordSante400();
    $dheECap->valuePrefix = "AT";
    $dheECap->query($query, $values);
    
    // Si l'enregistrement existe toujours
    if ($dheECap->data) {
      $this->mapDHE($dheECap);
      $this->trace($this->sejour->getProps(), "Séjour à enregistrer depuis DHE eCap");
      $this->id400DHE->bindObject($this->sejour);
    }

    $this->markStatus(self::STATUS_SEJOUR);
  }  
  
  function syncSej() {
    // L'intervention n'a pas de séjour
    if ("NumProvi" == $this->id400Sej->id400) {
      $this->trace("NumProvi", "Séjour provisoire");
      $this->setStatus(self::STATUS_SEJOUR);
      return;
    }

    // Déjà synchronisé, on laisse
    if ($this->id400DHE->_id) {
      $this->markStatus(self::STATUS_SEJOUR);
      return;
    }
    
    // Chargement du séjour
    $values = array (
      $this->id400Etab->id400,
      $this->id400Sej->id400,
    );
    
    $query = "SELECT * " .
        "\nFROM $this->base.ECSJ00 " .
        "\nWHERE SJCIDC = ? " .
        "\nAND SJNDOS = ? ";

    // Recherche du séjour
    $sejECap = new CRecordSante400();
    $sejECap->valuePrefix = "SJ";
    $sejECap->query($query, $values);
    
    // Si l'enregistrement existe toujours
    if ($sejECap->data) {
	    $this->mapSej($sejECap);
	    $this->trace($this->sejour->getProps(), "Séjour à enregistrer depuis Sej eCap");
	    $this->id400Sej->bindObject($this->sejour);
    }
    
    $this->markStatus(self::STATUS_SEJOUR);
  }
  
  function syncOperation() {
    // Mapping et binding
    $this->mapBindOperation($this);
    
    // Status
    $this->markStatus(self::STATUS_OPERATION, count($this->operations));
    if (!count($this->operations)) {
	    $this->setStatus(self::STATUS_ACTES);
	    $this->setStatus(self::STATUS_PRATICIEN);
	    return;
    }
    
    // Annulation de l'operation si mouvement de suppression
    $operation = reset($this->operations);
    mbExport($operation->annulee, "Opération annulation before for $this->rec");
    $operation->annulee = $this->type == "S" ? '1' : '0';
    mbExport($operation->annulee, "Opération annulation after for $this->rec");
//    mbExport($operation, "opération");
//    $operation->store();
    $this->trace("Opération annulée", "Mouvement de suppression");
  }

  function syncActes($CINT) {
    parent::syncActes($CINT);
  }
}
?>
