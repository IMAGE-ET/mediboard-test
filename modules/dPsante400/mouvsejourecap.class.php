<?php

global $AppUI;
require_once $AppUI->getModuleClass("dPsante400", "mouvement400");

class CMouvSejourEcap extends CMouvement400 {  
  const STATUS_ETABLISSEMENT = 0;
  const STATUS_FONCTION      = 1;
  const STATUS_PRATICIEN     = 2;
  const STATUS_PATIENT       = 3;
  const STATUS_SEJOUR        = 4;
  const STATUS_OPERATION     = 5;
  const STATUS_ACTES         = 6;
  const STATUS_NAISSANCE     = 7;
  
  public $sejour = null;
  public $etablissement = null;
  public $fonction = null;
  public $patient = null;
  public $praticiens = array();
  public $operations = array();
  public $naissance = null;
  
  protected $id400Sej = null;
  protected $id400DHE = null;
  protected $id400Etab = null;
  protected $id400Pat = null;
  protected $id400Prats = array();
  protected $id400Opers = array();
  
  // Identifiant unique d'intervention stocké en dur dand la DHE
  protected $dheCIDC = null;
  
  function __construct() {
    $this->base = "ECAPFILE";
    $this->table = "TRSJ0";
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
    $this->syncSej();
    $this->syncDHE();
    $this->syncOperations();
    $this->syncNaissance();
  }
  
  function loadExtensionField($table, $field, $id, $exists) {
    if (!$exists) {
      return;
    }
    
    $values = array (
      $this->id400Etab->id400,
      $table,
      $field,
      $id
    );
    
    $query = "SELECT * " .
        "\nFROM $this->base.ECTXPF " .
        "\nWHERE TXCIDC = ? " .
        "\nAND TXTABL = ? " .
        "\nAND TXZONE = ?" .
        "\nAND TXCL = ?";
    
    $tx400 = new CRecordSante400;
    $tx400->query($query, $values);
    return $tx400->data["TXTX"];
  }
  
  function syncEtablissement() {
    $CIDC = $this->consume("CIDC");
    
    $this->id400Etab = new CIdSante400();
    $this->id400Etab->id400 = $CIDC;
    $this->tag = "eCap";
    $this->id400Etab->object_class = "CGroups";

    $this->etablissement = $this->id400Etab->getCachedObject();
    if ($this->etablissement->_id) {
      $this->trace($this->etablissement->getProps(), "Etablissement depuis le cache");
      $this->markCache(self::STATUS_ETABLISSEMENT);
      return;    
    } 
        
    $etab400 = new CRecordSante400();
    $query = "SELECT * FROM $this->base.ECCIPF " .
        "\nWHERE CICIDC = ?";
    $values = array (
      $CIDC,       
    );
    
    $etab400->query($query, $values);
    $etab400->valuePrefix = "CI";
    $this->etablissement->text           = $etab400->consume("ZIDC");
    $this->etablissement->raison_sociale = $this->etablissement->text;
    $this->etablissement->adresse        = $etab400->consumeMulti("ZAD1", "ZAD2");
    $this->etablissement->cp             = $etab400->consume("CPO");
    $this->etablissement->ville          = $etab400->consume("ZLOC");
    $this->etablissement->tel            = $etab400->consume("ZTEL");
    $this->etablissement->fax            = $etab400->consume("ZFAX");
    $this->etablissement->web            = $etab400->consume("ZWEB");
    $this->etablissement->mail           = $etab400->consume("MAIL");
    $this->etablissement->domiciliation  = $etab400->consume("FINS");

    $this->trace($this->etablissement->getProps(), "Etablissement à enregistrer");

    $this->id400Etab->bindObject($this->etablissement);

    $id400EtabSHS = new CIdSante400();
    $id400EtabSHS->loadLatestFor($this->etablissement, "eCap SHS");
    $id400EtabSHS->last_update = mbDateTime();
    $id400EtabSHS->id400 =  $etab400->consume("CSHS");
    $id400EtabSHS->store();
    
    $this->trace($etab400->data, "Données établissement non importées");
    
    $this->markStatus(self::STATUS_ETABLISSEMENT);
  }
  
  function syncFonction() {
    $id400Func = new CIdSante400();
    $id400Func->id400 = $this->id400Etab->id400;
    $id400Func->object_class = "CFunctions";

    $this->fonction = $id400Func->getCachedObject();
    if ($this->fonction->_id) {
      $this->trace($this->fonction->getProps(), "Cabinet depuis le cache");
      $this->markCache(self::STATUS_FONCTION);
      return;    
    } 

    $this->fonction->group_id = $this->etablissement->group_id;
    $this->fonction->loadMatchingObject();
    $this->fonction->type = "cabinet";
    $this->fonction->text = "Import eCap";
    $this->fonction->color = "00FF00";

    $this->trace($this->fonction->getProps(), "Cabinet à enregistrer");

    $id400Func->bindObject($this->fonction);
    
    $this->markStatus(self::STATUS_FONCTION);
  }
   
  function syncPraticien($CPRT) {
    if (array_key_exists($CPRT, $this->praticiens)) {
      return;
    }
    
    // Id400 pricipal
    $tag = "eCap CIDC:{$this->id400Etab->id400}";
    $id400Prat = new CIdSante400();
    $id400Prat->object_class = "CMediusers";
    $id400Prat->id400 = $CPRT;
    $id400Prat->tag = $tag;

    $praticien = $id400Prat->getCachedObject();
    if ($praticien->_id) {
      $this->trace($praticien->getProps(), "Praticien depuis le cache");
      $this->id400Prats[$CPRT] = $id400Prat;
      $this->praticiens[$CPRT] = $praticien;
      $this->markCache(self::STATUS_PRATICIEN);
      return;
    }
    
    // Gestion du praticien non renseigné
    if ($CPRT == "0") {
      $praticien->_user_type = 3; // Chirurgien
      $praticien->_user_username = "pnr{$this->id400Etab->id400}";
      $praticien->_user_last_name  = "Non renseigné";
      $praticien->_user_first_name = "Praticien";

      // At least one true mediuser property or update won't work
      $praticien->actif = "0";
    } else {
      $query = "SELECT * FROM $this->base.ECPRPF " .
          "\nWHERE PRCIDC = ? " .
          "\nAND PRCPRT = ?";
      $values = array (
        $this->id400Etab->id400, 
        $CPRT,
      );
       
      $prat400 = new CRecordSante400();
      $prat400->loadOne($query, $values);
      $prat400->valuePrefix = "PR";
      $this->trace($prat400->data, "Données praticien à importer");
  
      $nomsPraticien     = split(" ", $prat400->consume("ZNOM"));
      $prenomsPraticiens = split(" ", $prat400->consume("ZPRE"));
  
      $praticien->_user_type = 3; // Chirurgien
      $praticien->_user_username = substr(strtolower($prenomsPraticiens[0][0] . join($nomsPraticien, "")), 0, 20);
      $praticien->_user_last_name  = join(" ", $nomsPraticien);
      $praticien->_user_first_name = join(" ", $prenomsPraticiens);
      $praticien->_user_email      = $prat400->consume("MAIL");
      $praticien->_user_phone      = mbGetValue(
        $prat400->consume("ZTL1"), 
        $prat400->consume("ZTL2"), 
        $prat400->consume("ZTL3"));
      $praticien->_user_adresse    = $prat400->consumeMulti("ZAD1", "ZAD2");
      $praticien->_user_cp         = $prat400->consume("CPO");
      $praticien->_user_ville      = $prat400->consume("ZVIL");
      $praticien->adeli            = $prat400->consume("CINC");
      $praticien->actif            = $prat400->consume("ACTI");
      $praticien->deb_activite     = $prat400->consumeDate("DTA1");
      $praticien->fin_activite     = $prat400->consumeDate("DTA2");
      
      // Import de la spécialité eCap
      $CSPE = $prat400->consume("CSPE");
      
      $query = "SELECT * FROM $this->base.ECSPPF " .
          "\nWHERE SPCSPE = ?";
      $values = array (
        $CSPE,
      );
      
      $spec400 = new CRecordSante400;
      $spec400->query($query, $values);
      $LISP = $spec400->consume("SPLISP");
      $praticien->commentaires = "Spécialité eCap : $LISP";
      
      // Import des spécialités à nomenclature officielles
      $CSP = array (
        $CSP1 = $prat400->consume("CSP1"),
        $CSP2 = $prat400->consume("CSP2"),
        $CSP3 = $prat400->consume("CSP3")
      );
      
      $CSP = join(" ", $CSP);
      $praticien->commentaires .= "\nSpécialité (Nomenclature) : $CSP";
    }    
    
    $pratDefault = new CMediusers;
    $pratDefault->function_id = $this->fonction->function_id;

    $this->trace($praticien->getProps(), "Praticien à enregistrer");
    
    $id400Prat->bindObject($praticien, $pratDefault);

    $this->id400Prats[$CPRT] = $id400Prat;
    $this->praticiens[$CPRT] = $praticien;

    // Id400 secondaire    
    if ($CPRT != "0") {
      $id400PratSHS = new CIdSante400();
      $id400PratSHS->loadLatestFor($praticien, "$tag SHS");
      $id400PratSHS->last_update = mbDateTime();
      $id400PratSHS->id400 =  $prat400->consume("SIH");
      $id400PratSHS->store();
    }
    
    $this->markStatus(self::STATUS_PRATICIEN);
  }

  function syncPatient() {
    static $transformSexe = array (
      "1" => "m",
      "2" => "f",
    );
    
    static $transformNationalite = array (
      "" => "local",
      "F" => "local",
      "E" => "etranger",
    );

    $DMED = $this->consume("DMED");
    
    // Gestion des id400
    $tag = "eCap CIDC:{$this->id400Etab->id400}";
    $this->id400Pat = new CIdSante400();
    $this->id400Pat->object_class = "CPatient";
    $this->id400Pat->id400 = $DMED;
    $this->id400Pat->tag = $tag;
    
    // Gestion du cache
    $this->patient = $this->id400Pat->getCachedObject();
    
    if ($this->patient->_id) {
      $this->trace($this->patient->getProps(), "Patient depuis le cache");
      $this->markCache(self::STATUS_PATIENT);
      return;
    }
    
    $pat400 = new CRecordSante400();
    
    $query = "SELECT * FROM $this->base.ECPAPF " .
        "\nWHERE PACIDC = ? " .
        "\nAND PADMED = ?";
    $values = array (
      $this->id400Etab->id400,
      $DMED,
    );
    $pat400->query($query, $values);
    $pat400->valuePrefix = "PA";

    $this->patient = new CPatient;
    $this->patient->nom              = $pat400->consume("ZNOM");
    $this->patient->prenom           = $pat400->consume("ZPRE");
    $this->patient->nom_jeune_fille  = $pat400->consume("ZNJF");
    $this->patient->naissance        = $pat400->consumeDate("DNAI");
    $this->patient->loadMatchingObject();
    
    $this->patient->sexe             = @$transformSexe[$pat400->consume("ZSEX")];
    $this->patient->adresse          = $pat400->consumeMulti("ZAD1", "ZAD2");
    $this->patient->ville            = $pat400->consume("ZVIL");
    $this->patient->cp               = $pat400->consume("CPO");
    $this->patient->tel              = $pat400->consumeTel("ZTL1");
    $this->patient->tel2             = $pat400->consumeTel("ZTL2");
    
    $this->patient->matricule         = $pat400->consume("NSEC") . $pat400->consume("CSEC");
    $this->patient->rang_beneficiaire = str_pad($pat400->consume("RBEN"), 2, "0", STR_PAD_LEFT);

//    $this->patient->pays              = $pat400->consume("ZPAY");
    $this->patient->nationalite       = @$transformNationalite[$pat400->consume("CNAT")];

    $this->trace($this->patient->getProps(), "Patient à enregistrer");

    $this->id400Pat->bindObject($this->patient);

    $this->markStatus(self::STATUS_PATIENT);
  }

  /**
   * Map une DHE eCap vers le séjour du mouvement
   *
   * @param string $NDOS
   */
  function mapDHE(CRecordSante400 $dheECap) {
    if (!$dheECap->data) {
      return;
    }
    
    $this->trace($dheECap->data, "DHE Trouvée");
    $this->dheCIDC = $dheECap->consume("CINT");

    $NSEJ = null;//$dheECap->consume("NSEJ");
    $IDAT = $dheECap->consume("IDAT");
    
    // Praticien de la DHE prioritaire
    $CPRT = $dheECap->consume("CPRT");
    $this->syncPraticien($CPRT);
    $this->sejour->praticien_id = $this->praticiens[$CPRT]->_id;
    
    // Cration du log de création du séjour
    $log = new CUserLog();
    $log->setObject($this->sejour);
    $log->user_id = $this->praticiens[$CPRT]->_id;
    $log->type = "create";
    $log->date = mbDateTime($dheECap->consumeDate("DDHE"));
    $log->loadMatchingObject();

    // Motifs d'hospitalisations
    if ("0" != $CMOT = $dheECap->consume("CMOT")) {
      $query = "SELECT * FROM $this->base.ECMOPF " .
          "\nWHERE MOCMOT = ?";
          
      $values = array (
        $CMOT
      );
      
      $motECap = new CRecordSante400();
      $motECap->loadOne($query, $values);
      $LIMO = $motECap->consume("MOLIMO");
      $this->sejour->rques = "Motif: $LIMO";
    }
    
    // Horodatage
    $entree = $dheECap->consumeDateTime("DTEN", "HREN");
    $duree = max(1, $dheECap->consume("DMSJ"));
    $sortie = mbDateTime("+$duree days", $entree);
    $this->sejour->entree_prevue = $entree;
    $this->sejour->sortie_prevue = $sortie;

    // Evite le updateFormField()
    $this->sejour->_hour_entree_prevue = null;
    $this->sejour->_hour_sortie_prevue = null;
    
    // Type d'hospitalisation
    $typeHospi = array (
      "0" => "comp",
      "1" => "ambu",
      "2" => "exte",
      "3" => "seances",
      "4" => "ssr",
      "5" => "psy"
    );
    
    $TYHO = $dheECap->consume("TYHO");
    $this->sejour->type = $typeHospi[$TYHO];
    
    // Hospitalisation
    $this->sejour->chambre_seule      = $dheECap->consume("CHPA");
    $this->sejour->hormone_croissance = $dheECap->consume("HOCR");
    $this->sejour->lit_accompagnant   = $dheECap->consume("LIAC");
    $this->sejour->isolement          = $dheECap->consume("ISOL");
    $this->sejour->television         = $dheECap->consume("TELE");
    $this->sejour->repas_diabete      = $dheECap->consume("DIAB");
    $this->sejour->repas_sans_sel     = $dheECap->consume("SASE");
    $this->sejour->repas_sans_residu  = $dheECap->consume("SARE");
    
    // Champs étendus
    $TXCL = "0$IDAT"; // La clé demande 10 chiffres
    $OBSH = $this->loadExtensionField("ECATPF", "ATOBSH", $TXCL, $dheECap->consume("OBSH"));
    $EXBI = $this->loadExtensionField("ECATPF", "ATEXBI", $TXCL, $dheECap->consume("EXBI"));
    $TRPE = $this->loadExtensionField("ECATPF", "ATTRPE", $TXCL, $dheECap->consume("TRPE"));
    $REM  = $this->loadExtensionField("ECATPF", "ATREM" , $TXCL, $dheECap->consume("REM" ));
    
    $remarques = array (
      "Services: $OBSH",
      "Autre: $REM"
    );
    
    
    $this->sejour->rques = join($remarques, "\n");

    // $TRPE et $EXBI à gérer
    
  }
  
  function syncDHE() {
    $values = array (
      $this->id400Etab->id400,
      $this->id400Sej->id400,
      $this->id400Pat->id400,
    );
    
    $tag = "eCap DHE CIDC:{$this->id400Etab->id400}";
    $this->id400DHE = new CIdSante400();
    $this->id400DHE->id400 = $IDAT;
    $this->id400DHE->tag = $tag;
    
    $this->trace($this->sejour->getProps(), "Séjour (via DHE) à enregistrer");

    $query = "SELECT * " .
        "\nFROM $this->base.ECATPF " .
        "\nWHERE ATCIDC = ? " .
        "\nAND ATNDOS = ? " .
        "\nAND ATDMED = ?";

    // Recherche de la DHE
    $dheECap = new CRecordSante400();
    $dheECap->valuePrefix = "AT";
    $dheECap->query($query, $values);
    
    $this->mapDHE($dheECap);

    $this->id400DHE->bindObject($this->sejour);

    $this->markStatus(self::STATUS_SEJOUR);
  }
  
  function mapBindOperation(CRecordSante400 $operECap) {
    if (!$this->sejour->_id) {
      return;
    }
    
    $this->trace($operECap->data, "Opération trouvée"); 
    
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
    $tag = "eCap CINT CIDC:{$this->id400Etab->id400}";
    $id400Oper = new CIdSante400();
    $id400Oper->id400 = $CINT;
    $id400Oper->tag = $tag;

    $this->trace($operation->getProps(), "Opération à enregistrer");

    $id400Oper->bindObject($operation);
    
    $this->id400Opers[$CINT] = $id400Oper;      
    
    $this->operations[$CINT] = $operation;
    $this->syncActes($CINT);
  }
  
  function syncOperations() {
    $query = "SELECT * " .
        "\nFROM $this->base.ECINPF " .
        "\nWHERE INCIDC = ? " .
        "\nAND ((INNDOS = ? AND INDMED = ?) OR INCINT = ?)";

    $values = array (
      $this->id400Etab->id400,
      $this->id400Sej->id400,
      $this->id400Pat->id400,
      $this->dheCIDC,
    );
    
    // Recherche des opérations
    $opersECap = CRecordSante400::multipleLoad($query, $values);
    foreach ($opersECap as $operECap) {
      $operECap->valuePrefix = "IN";
      $this->mapBindOperation($operECap);
    }

    // Status
    $this->markStatus(self::STATUS_OPERATION, count($opersECap));
    if (!count($opersECap)) {
      $this->markStatus(self::STATUS_ACTES, 0);
    }
  }

  function syncActes($CINT) {
    $operation = $this->operations[$CINT];
    
    $query = "SELECT * " .
        "\nFROM $this->base.ECACPF " .
        "\nWHERE ACCIDC = ? " .
        "\nAND ACCINT = ? ";

    $values = array (
      $this->id400Etab->id400,
      $CINT,
    );

    $actesECap = CRecordSante400::multipleLoad($query, $values);
    
    foreach ($actesECap as $acteECap) {
      $this->trace($acteECap->data, "Acte trouvé");
      
      $acteECap->valuePrefix = "AC";
      
      $acte = new CActeCCAM;

      // Champs issus de l'opération
      $acte->object_id = $operation->_id;
      $acte->object_class = $operation->_class_name;
      $acte->execution = mbDateTime($operation->sortie_salle, $operation->date);
      
      // Praticien exécutant
      $CPRT = $acteECap->consume("CPRT");
      $this->syncPraticien($CPRT);
      $acte->executant_id = $this->praticiens[$CPRT]->_id;
      
      // Codage
      $acte->code_acte     = $acteECap->consume("CDAC");
      $acte->code_activite = mbGetValue($acteECap->consume("CACT"), 1);
      $acte->code_phase    = $acteECap->consume("CPHA");
      $acte->modificateurs = $acteECap->consume("CMOD");
      $acte->montant_depassement = $acteECap->consume("MDEP");
      
      // Gestion des id400
      $tags = array (
        "eCap",
        "CIDC:{$this->id400Etab->id400}",
        "CINT:$CINT",
        "CPRT:$CPRT",
        "Acte:$acte->code_acte-$acte->code_activite-$acte->code_phase",
      );

      $id400acte = new CIdSante400();
      $id400acte->id400 = $CINT;
      $id400acte->tag = join(" ", $tags);

      $this->trace($acte->getProps(), "Acte à enregistrer");
      $acte->_adapt_object = true;
      $id400acte->bindObject($acte);
            
      // Ajout du code dans l'opération
      if (!in_array($acte->code_acte, $operation->_codes_ccam)) {
        $operation->_codes_ccam[] = $acte->code_acte;
        $operation->store();
      }
    }

    $this->markStatus(self::STATUS_ACTES, count($actesECap));
  }

  /**
   * Map un séjour eCap en séjour Mediboard
   * 
   */
  function mapSej(CRecordSante400 $sejECap) {
    // Praticien
    $CPRT = $this->consume("CPRT");
    $this->syncPraticien($CPRT);
    
    // Références principales
    $this->sejour->group_id     = $this->etablissement->_id;
    $this->sejour->patient_id   = $this->patient->_id;
    $this->sejour->praticien_id = $this->praticiens[$CPRT]->_id;

    $entree = $sejECap->consumeDateTime("DTEN", "HREN");
    $sortie = $sejECap->consumeDateTime("DTSO", "HRSO");

    // Dates prévues et réelles
    switch ($sejECap->consume("PRES")) {
      case "0": // Prévu
      $this->sejour->entree_prevue = $entree;
      $this->sejour->sortie_prevue = mbGetValue($sortie, mbDateTime("+ 1 days", $this->sejour->entree_prevue));
      break;
    
      case "1": // Présent
      $this->sejour->entree_reelle = $entree;
      $this->sejour->sortie_prevue = $sortie;
      
      case "2": // Sorti
      $this->sejour->entree_reelle = $entree;
      $this->sejour->sortie_reelle = $sortie;
      break;
    }
    
    // Absence de dates prévues
    if (!$this->sejour->entree_prevue) {
      $this->sejour->entree_prevue = $this->sejour->entree_reelle;
    }

    if (!$this->sejour->sortie_prevue) {
      $this->sejour->sortie_prevue = 
        $this->sejour->sortie_reelle > $this->sejour->entree_reelle ? 
        $this->sejour->sortie_reelle : // Date de sortie fournie, on l'utilise  
        mbDateTime("+ 1 days", $this->sejour->entree_prevue); // On simule la date de sortie
    }
  }
  
  function syncSej() {
    $NDOS = $this->consume("NDOS");

    // Gestion des identifiants
    $tags[] = "eCap";
    $tags[] = "NDOS";
    $tags[] = "CIDC:{$this->id400Etab->id400}";
    $this->id400Sej = new CIdSante400();
    $this->id400Sej->id400 = $NDOS;
    $this->id400Sej->object_class = "CSejour";
    $this->id400Sej->tag = join(" ", $tags);
    
    // Mapping et binding
    $this->sejour = $this->id400Sej->getCachedObject(0);
    $this->sejour->annule = $this->type == "S" ? '1' : '0';
    $this->mapSej($this);
    $this->trace($this->sejour->getProps(), "Séjour à enregistrer");
    $this->id400Sej->bindObject($this->sejour);
    
    $this->markStatus(self::STATUS_SEJOUR);
  }
  
  function syncNaissance() {
    $this->markStatus(self::STATUS_NAISSANCE, 0);
  }
}
?>
