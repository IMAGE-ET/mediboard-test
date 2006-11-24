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
  
  protected $id400Sej = null;
  protected $id400EtabECap = null;
  protected $id400Pat = null;
  protected $id400Prat = null;
  
  function __construct() {
    $this->base = "ECAPFILE";
    $this->table = "TRSJ0";
    $this->completeMark = ">EFCPSN";
    $this->prodField = "ETAT";
    $this->idField = "INDEX";
    $this->typeField = "TRACTION";
  }

  function synchronize() {
    if ($this->type == "S") {
      $this->trace("synchronisation annulée", "Mouvement de type suppression");
      return;
    }

    $this->syncEtablissement();
    $this->syncFonction();
    
    // Praticien du séjour si aucune DHE
    $CPRT = $this->consume("A_CPRT");
    $this->syncPraticien($CPRT);

    $this->syncPatient();
    $this->syncSejour();
    $this->syncDHE();
    $this->syncOperations();
  }
  
  function loadExtensionField($table, $field, $id, $exists) {
    if (!$exists) {
      return;
    }
    
    $queryValues = array (
      $this->id400EtabECap->id400,
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
    $tx400->query($query, $queryValues);
    return $tx400->data["TXTX"];
  }
  
  function syncEtablissement() {
    $CIDC = $this->consume("A_CIDC");
    $etab400 = new CRecordSante400();
    $etab400->query("SELECT * FROM $this->base.ECCIPF WHERE CICIDC = $CIDC");
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

    $this->id400EtabECap = new CIdSante400();
    $this->id400EtabECap->id400 = $etab400->consume("CICIDC");
    $this->id400EtabECap->bindObject($this->etablissement);

    $id400EtabSHS = new CIdSante400();
    $id400EtabSHS->loadLatestFor($this->etablissement, "SHS");
    $id400EtabSHS->last_update = mbDateTime();
    $id400EtabSHS->id400 =  $etab400->consume("CICSHS");
    $id400EtabSHS->store();
    
    $this->trace($etab400->data, "Données établissement non importées");
    $this->markStatus("E");
  }
  
  function syncFonction() {
    $this->fonction = new CFunctions();
    $this->fonction->group_id = $this->etablissement->group_id;
    $this->fonction->loadMatchingObject();
    $this->fonction->text = "Import eCap";
    $this->fonction->color = "00FF00";

    $id400Func = new CIdSante400();
    $id400Func->id400 = $this->id400EtabECap->id400;
    $id400Func->bindObject($this->fonction);
    
    $this->markStatus("F");
  }
   
  function syncPraticien($CPRT) {
    $prat400 = new CRecordSante400();
    $prat400->query("SELECT * FROM $this->base.ECPRPF WHERE PRCIDC = ? AND PRCPRT = ?", array (
      $this->id400EtabECap->id400, 
      $CPRT));

    $nomsPraticien     = split(" ", $prat400->consume("PRZNOM"));
    $prenomsPraticiens = split(" ", $prat400->consume("PRZPRE"));

    $this->praticien = new CMediusers;
    $this->praticien->_user_type = 3; // Chirurgien
    $this->praticien->_user_username = substr(strtolower($prenomsPraticiens[0] . $nomsPraticien[0]), 0, 20);
    $this->praticien->_user_last_name  = join(" ", $nomsPraticien);
    $this->praticien->_user_first_name = join(" ", $prenomsPraticiens);
    $this->praticien->_user_email      = $prat400->consume("PRMAIL");
    $this->praticien->_user_phone      = mbGetValue(
      $prat400->consume("PRZTL1"), 
      $prat400->consume("PRZTL2"), 
      $prat400->consume("PRZTL3"));
    $this->praticien->_user_adresse    = $prat400->consumeMulti("PRZAD1", "PRZAD2");
    $this->praticien->_user_cp         = $prat400->consume("PRCPO");
    $this->praticien->_user_ville      = $prat400->consume("PRZVIL");
    $this->praticien->adeli            = $prat400->consume("PRCINC");
    $this->praticien->actif            = $prat400->consume("PRACTI");
    $this->praticien->deb_activite     = $prat400->consumeDate("PRDTA1");
    $this->praticien->fin_activite     = $prat400->consumeDate("PRDTA2");
    
    // Import de la spécialité eCap
    $CSPE = $prat400->consume("PRCSPE");
    $spec400 = new CRecordSante400;
    $spec400->query("SELECT * FROM $this->base.ECSPPF WHERE SPCSPE= $CSPE");
    $LISP = $spec400->consume("SPLISP");
    $this->praticien->commentaires = "Spécialité eCap : $LISP";
    
    // Import des spécialités à nomenclature officielles
    $CSP = array (
      $CSP1 = $prat400->consume("PRCSP1"),
      $CSP2 = $prat400->consume("PRCSP2"),
      $CSP3 = $prat400->consume("PRCSP3")
    );
    
    $CSP = join(" ", $CSP);
    $this->praticien->commentaires .= "\nSpécialité (Nomenclature) : $CSP";
    
    $praticien = new CMediusers;
    $praticien->function_id = $this->fonction->function_id;

    // Gestion des id400
    $tag = "CIDC:{$this->id400EtabECap->id400}";
    $this->id400Prat = new CIdSante400();
    $this->id400Prat->id400 = $CPRT;
    $this->id400Prat->tag = $tag;
    $this->id400Prat->bindObject($this->praticien, $praticien);
    
    $id400PratSHS = new CIdSante400();
    $id400PratSHS->loadLatestFor($this->praticien, "SHS $tag");
    $id400PratSHS->last_update = mbDateTime();
    $id400PratSHS->id400 =  $prat400->consume("PRSIH");
    $id400PratSHS->store();
    
    $this->trace($prat400->data, "Données praticien non importées");
    $this->markStatus("C");
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

    $DMED = $this->consume("A_DMED");
    $pat400 = new CRecordSante400();
    $pat400->query("SELECT * FROM $this->base.ECPAPF WHERE PACIDC = ? AND PADMED = ?", array (
      $this->id400EtabECap->id400,
      $DMED));

    $this->patient = new CPatient;
    $this->patient->nom              = $pat400->consume("PAZNOM");
    $this->patient->prenom           = $pat400->consume("PAZPRE");
    $this->patient->nom_jeune_fille  = $pat400->consume("PAZNJF");
    $this->patient->naissance        = $pat400->consumeDate("PADNAI");
    
    $this->patient->sexe             = @$transformSexe[$pat400->consume("PAZSEX")];
    $this->patient->adresse          = $pat400->consumeMulti("PAZAD1", "PAZAD2");
    $this->patient->ville            = $pat400->consume("PAZVIL");
    $this->patient->cp               = $pat400->consume("PACPO");
    $this->patient->tel              = $pat400->consumeTel("PAZTL1");
    $this->patient->tel2             = $pat400->consumeTel("PAZTL2");
    
    $this->patient->matricule         = $pat400->consume("PANSEC") . $pat400->consume("PACSEC");
    $this->patient->rang_beneficiaire = $pat400->consume("PARBEN");;

//    $this->patient->pays              = $pat400->consume("PAZPAY");
    $this->patient->nationalite       = @$transformNationalite[$pat400->consume("PACNAT")];
    $this->patient->lieu_naissance    = null;
    $this->patient->profession        = null;
        
    $this->patient->employeur_nom     = null;
    $this->patient->employeur_adresse = null;
    $this->patient->employeur_ville   = null;
    $this->patient->employeur_cp      = null;
    $this->patient->employeur_tel     = null;
    $this->patient->employeur_urssaf  = null;

    $this->patient->prevenir_nom     = null;
    $this->patient->prevenir_prenom  = null;
    $this->patient->prevenir_adresse = null;
    $this->patient->prevenir_ville   = null;
    $this->patient->prevenir_cp      = null;
    $this->patient->prevenir_tel     = null;
    $this->patient->prevenir_parente = null;
    
    $this->patient->medecin_traitant = null;
    $this->patient->medecin1         = null;
    $this->patient->medecin2         = null;
    $this->patient->medecin3         = null;
    $this->patient->incapable_majeur = null;
    $this->patient->ATNC             = null;
    $this->patient->SHS              = null;
    $this->patient->regime_sante     = null;
    $this->patient->rques            = null;
    $this->patient->listCim10        = null;
    $this->patient->cmu              = null;
    $this->patient->ald              = null;

    
    // Gestion des id400
    $tag = "CIDC:{$this->id400EtabECap->id400}";
    $this->id400Pat = new CIdSante400();
    $this->id400Pat->id400 = $DMED;
    $this->id400Pat->tag = $tag;
    $this->id400Pat->bindObject($this->patient);

    $this->trace($pat400->data, "Données patients non importées");
    $this->markStatus("P");
  }

  function syncDHE() {
    $queryValues = array (
      $this->id400EtabECap->id400,
      $this->id400Sej->id400,
      $this->id400Pat->id400,
    );
    
    $query = "SELECT * " .
        "\nFROM $this->base.ECATPF " .
        "\nWHERE ATCIDC = ? " .
        "\nAND ATNDOS = ? " .
        "\nAND ATDMED = ?";

    // Recherche de la DHE
    $dheECap = new CRecordSante400();
    $dheECap->query($query, $queryValues);
    if (!$dheECap->data) {
      return;
    }
    
    $NSEJ = $dheECap->consume("ATNSEJ");
    $IDAT = $dheECap->consume("ATIDAT");
    
    // Praticien de la DHE prioritaire
    $CPRT = $dheECap->consume("ATCPRT");
    if ($CPRT != $this->id400Prat->id400) {
      $this->syncPraticien($CPRT);
      $this->sejour->praticien_id = $this->praticien->_id;
    }
    
    // Cration du log de création du séjour
    $log = new CUserLog();
    $log->setObject($this->sejour);
    $log->user_id = $this->praticien->_id;
    $log->type = "create";
    $log->date = mbDateTime($dheECap->consumeDate("ATDDHE"));
    $log->loadMatchingObject();

    // Motifs d'hospitalisations
    $CMOT = $dheECap->consume("ATCMOT");
    $motECap = new CRecordSante400();
    $motECap->query("SELECT * FROM $this->base.ECMOPF WHERE MOCMOT = ?", array($CMOT));
    $LIMO = $motECap->consume("MOLIMO");
    $this->sejour->rques = "Motif: $LIMO";
    
    // Horodatage
    $entree = $dheECap->consumeDateTime("ATDTEN", "ATHREN");
    $duree = $dheECap->consume("ATDMSJ");
    $sortie = mbDate("+$duree days", $entree);
    
    // Type d'hospitalisation
    $typeHospi = array (
      "0" => "comp",
      "1" => "ambu",
      "2" => "exte",
      "3" => "seances",
      "4" => "ssr",
      "5" => "psy"
    );
    
    $TYHO = $dheECap->consume("ATTYHO");
    $this->sejour->type = $typeHospi[$TYHO];
    
    // Hospitalisation
    $this->sejour->chambre_seule      = $dheECap->consume("ATCHPA");
    $this->sejour->hormone_croissance = $dheECap->consume("ATHOCR");
    $this->sejour->lit_accompagnant   = $dheECap->consume("ATLIAC");
    $this->sejour->isolement          = $dheECap->consume("ATISOL");
    $this->sejour->television         = $dheECap->consume("ATTELE");
    $this->sejour->repas_diabete      = $dheECap->consume("ATDIAB");
    $this->sejour->repas_sans_sel     = $dheECap->consume("ATSASE");
    $this->sejour->repas_sans_residu  = $dheECap->consume("ATSARE");
    
    // Champs étendus
    $TXCL = "0$IDAT"; // La clé demande 10 chiffres
    $OBSH = $this->loadExtensionField("ECATPF", "ATOBSH", $TXCL, $dheECap->consume("ATOBSH"));
    $EXBI = $this->loadExtensionField("ECATPF", "ATEXBI", $TXCL, $dheECap->consume("ATEXBI"));
    $TRPE = $this->loadExtensionField("ECATPF", "ATTRPE", $TXCL, $dheECap->consume("ATTRPE"));
    $REM  = $this->loadExtensionField("ECATPF", "ATREM" , $TXCL, $dheECap->consume("ATREM" ));
    
    $remarques = array (
      "Services: $OBSH",
      "Autre: $REM"
    );
    
    
    $this->sejour->rques = join($remarques, "\n");

    $tags[] = "DHE";
    $tags[] = "CIDC:{$this->id400EtabECap->id400}";
    $this->idDHECap = new CIdSante400();
    $this->idDHECap->id400 = $NSEJ;
    $this->idDHECap->tag = join(" ", $tags);
    $this->idDHECap->bindObject($this->sejour);

    // $TRPE et $EXBI à gérer
    
    $this->trace($dheECap->data, "Données DHE non traitées"); 
  }
  
  
  function syncOperations() {
    $queryValues = array (
      $this->id400EtabECap->id400,
      $this->id400Sej->id400,
      $this->id400Pat->id400,
    );
    
    $query = "SELECT * " .
        "\nFROM $this->base.ECINPF " .
        "\nWHERE INCIDC = ? " .
        "\nAND INNDOS = ? " .
        "\nAND INDMED = ?";

    // Recherche de la DHE
    $opersECap = CRecordSante400::multipleLoad($query, $queryValues);
    if (!count($opersECap)) {
      return;
    }

    $this->trace($this->rec, "Opérations eCap trouvées pour mouvement");
  }


  function syncSejour() {
    
    $NDOS = $this->consume("A_NDOS");
    

    $this->sejour = new CSejour;  
    $this->sejour->group_id     = $this->etablissement->_id;
    $this->sejour->patient_id   = $this->patient->_id;
    $this->sejour->praticien_id = $this->praticien->_id;
    
    $entree = $this->consumeDateTime("A_DTEN", "A_HREN");
    $sortie = $this->consumeDateTime("A_DTSO", "A_HRSO");
    
    switch ($this->consume("A_PRES")) {
      case "0": // Prévu
      $this->sejour->entree_prevue = $entree;
      $this->sejour->sortie_prevue = $sortie;
      break;
    
      case "1": // Présent
      $this->sejour->entree_reelle = $entree;
      $this->sejour->sortie_prevue = $sortie;
      
      case "2": // Sorti
      $this->sejour->entree_reelle = $entree;
      $this->sejour->sortie_reelle = $sortie;
      break;
    }
        
    // Gestion des identifiants
    $tags[] = "CIDC:{$this->id400EtabECap->id400}";
    $tags[] = "DMED:{$this->id400Pat->id400}";
    $this->id400Sej = new CIdSante400();
    $this->id400Sej->id400 = $NDOS;
    $this->id400Sej->tag = join(" ", $tags);
    $this->id400Sej->bindObject($this->sejour);
    
    // Rectifications sur les dates prévues
    // Pervents updateFormFields()
    $this->sejour->_hour_entree_prevue = null;
    $this->sejour->_hour_sortie_prevue = null;
    
    $nullDate = "0000-00-00 00:00:00";
    if ($this->sejour->entree_prevue == $nullDate) {
      $this->sejour->entree_prevue = $this->sejour->entree_reelle;
    }
    
    if ($this->sejour->sortie_prevue == $nullDate) {
      $this->sejour->sortie_prevue = 
        $this->sejour->sortie_reelle > $this->sejour->entree_reelle ? 
        $this->sejour->sortie_reelle : // Date de sortie fournie, on l'utilise 
        mbDateTime("+ 1 days", $this->sejour->entree_prevue); // On simule la date de sortie
    }

    // Sauvegarde après rectifications
    if ($msg = $this->sejour->store()) {
      throw new Exception($msg);
    }
    
    
    $this->trace($this->sejour->getProps(), "Séjour sauvegardé");
    $this->markStatus("S");
  }
}
?>
