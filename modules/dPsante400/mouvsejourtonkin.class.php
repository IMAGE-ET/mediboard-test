<?php

global $AppUI;
require_once $AppUI->getModuleClass("dPsante400", "recordsante400");

class CMouvSejourTonkin extends CRecordSante400 {
  static $base = "GT_EAI";
  static $table = "SEJMDB";
  static $complete = ">EFCPSN";
  static $verbose = false;
  
  public $status = null;
  public $rec = null;
  
  public $sejour = null;
  public $etablissement = null;
  public $fonction = null;
  public $patient = null;
  public $praticien = null;
  public $naissance = null;
  
  function __construct() {
  }

  function multipleLoad($max = 100) {
    $base  = self::$base;
    $table = self::$table;
    $query = "SELECT * FROM $base.$table";
    return CRecordSante400::multipleLoad($query, $max, "CMouvSejourTonkin");
  }

  function count() {
    $base  = self::$base;
    $table = self::$table;
    $req = new CRecordSante400();
    $query = "SELECT COUNT(*) AS COUNT FROM $base.$table";
    $req->query($query);
    return ($req->consume("COUNT"));
  }
  
  function load($rec = null) {
    $base  = self::$base;
    $table = self::$table;
    $query = "SELECT * FROM $base.$table";

    if ($rec !== null) {
      $rec = intval($rec);
      $query .= " WHERE IDUENR = $rec";
    }
    
    $this->query($query);
  }
  
  function deleteRow() {
  }
  
  function markRow() {
    $base  = self::$base;
    $table = self::$table;
    $query = "UPDATE $base.$table SET RETPRODST = \"START\" WHERE IDUENR = $this->rec";
    mbTrace($query);
   $this->query($query);
  }
  
  function markStatus($letter) {
    $this->status .= $letter;
  }

  function trace($value, $title) {
    if (self::$verbose) {
      mbTrace($value, $title);
    }
  }
  
  function proceed() {
    $this->status = ">";
    try {
      $this->synchronize();
      $this->deleteRow();
      return true;
    } catch (Exception $e) {
      if (self::$verbose) {
        trigger_error($e->getMessage(), E_USER_WARNING);
      }
      $this->markRow();
      $this->trace($this->data, "Données non traitées dans le mouvements");
      return false;
    }
    
  }
  
  function synchronize() {
    $this->rec = $this->consume("IDUENR");
    
    // Etablissement
    $CODETB = $this->consume("CODETB");
    $etp01 = new CRecordSante400();
    $etp01->query("SELECT * FROM PICLIN$CODETB.ETP01");

    $this->etablissement = new CGroups;
    $this->etablissement->text           = $etp01->consume("ETRSOC");
    $this->etablissement->raison_sociale = $this->etablissement->text;
    $this->etablissement->adresse        = $etp01->consumeMulti("ETADRE", "ETADRS");
    $this->etablissement->cp             = $etp01->consume("ETCODP");
    $this->etablissement->ville          = $etp01->consume("ETVILE");
    $this->etablissement->tel            = $etp01->consume("ETTLPH");
    $this->etablissement->directeur      = $etp01->consume("ETNOMD");
    $this->etablissement->domiciliation  = $etp01->consume("ETNDOM");
    $this->etablissement->siret          = $etp01->consume("ETSIRT");
    $this->etablissement->ape            = $etp01->consume("ETCAPE");

    $id400Etab = new CIdSante400();
    $id400Etab->id400 = $CODETB;
    $id400Etab->bindObject($this->etablissement);
    
    $this->markStatus("E");

    // Fonction
    $this->fonction = new CFunctions();
    $this->fonction->group_id = $this->etablissement->group_id;
    $this->fonction->loadMatchingObject();
    $this->fonction->text = "Import Santé400";
    $this->fonction->color = "00FF00";

    $id400Func = new CIdSante400();
    $id400Func->id400 = $CODETB;
    $id400Func->bindObject($this->fonction);
    
    $this->markStatus("F");

    // Praticien
    $CODMEDREF = $this->consume("CODMEDREF");
    $mdp01 = new CRecordSante400();
    $mdp01->query("SELECT * FROM PICLIN$CODETB.MDP01 WHERE MDPRAT = $CODMEDREF");
    
    $nomsPraticien     = split(" ", $mdp01->consume("MDNOMS"));
    $prenomsPraticiens = split(" ", $mdp01->consume("MDPRES"));

    $this->praticien = new CMediusers;
    $this->praticien->_user_type = 3; // Chirurgien
    $this->praticien->_user_username = strtolower($prenomsPraticiens[0] . $nomsPraticien[1]);
    $this->praticien->_user_last_name  = $nomsPraticien[1];
    $this->praticien->_user_first_name = join(" ", $prenomsPraticiens);
    $this->praticien->_user_email      = null;
    $this->praticien->_user_phone      = mbGetValue($mdp01->consume("MDTLPB"), $mdp01->consume("MDTLPH"));
    $this->praticien->_user_adresse    = $mdp01->consumeMulti("MDADRE", "MDADS1");
    $this->praticien->_user_cp         = $mdp01->consume("MDCODP");
    $this->praticien->_user_ville      = $mdp01->consume("MDVIL1");
    $this->praticien->adeli            = $mdp01->consume("MDNIOM");
    
    $praticien = new CMediusers;
    $praticien->function_id = $this->fonction->function_id;

    $id400Prat = new CIdSante400();
    $id400Prat->id400 = $CODMEDREF;
    $id400Prat->bindObject($this->praticien, $praticien);
    
    $this->markStatus("C");

    // Import du patient
    static $transformSexe = array (
      "1" => "m",
      "2" => "f",
      "3" => "j",
      "4" => "m",
      "5" => "f",
    );
    
    static $transformNationalite = array (
      "F" => "local",
      "E" => "etranger",
    );

    static $transformParente = array (
      "1" => "conjoint",
      "2" => "enfant",
      "3" => "ascendant",
      "4" => "divers",
      "5" => "collateral",
    );
    
    $this->patient = new CPatient;
    $this->patient->nom              = $this->consume("NOMPAT");
    $this->patient->prenom           = $this->consume("PRENOMPAT");
    $this->patient->nom_jeune_fille  = $this->consume("NOMJFIPAT");
    $this->patient->naissance        = $this->consumeDateInverse("DATNAIPAT");
    $this->patient->sexe             = @$transformSexe[$this->consume("SEXPAT")];
    $this->patient->adresse          = $this->consume("ADRPAT") . "\n" . $this->consume("ADRSUIPAT");
    $this->patient->ville            = $this->consume("VILPAT");
    $this->patient->cp               = $this->consume("CODPOSPAT");
    $this->patient->tel              = $this->consume("TELPAT");
    $this->patient->matricule        = $this->consume("NSSPAT") . $this->consume("CSSPAT");

    $this->patient->profession       = $this->consume("PROPAT");
    $this->patient->pays             = $this->consume("PYSPAT");
    $this->patient->nationalite      = @$transformNationalite[$this->consume("NATPAT")];
    $this->patient->lieu_naissance   = $this->consume("LIENAIPAT");
    
    $this->patient->employeur_nom     = $this->consume("NOMEMP");
    $this->patient->employeur_adresse = $this->consume("ADREMP") . "\n" . $this->consume("ADRSUIEMP");
    $this->patient->employeur_ville   = $this->consume("VILEMP");
    $this->patient->employeur_cp      = $this->consume("CODPOSEMP");
    $this->patient->employeur_tel     = $this->consume("TELEMP");
    $this->patient->employeur_urssaf  = $this->consume("URSSAFEMP");

    $this->patient->prevenir_nom     = $this->consume("NOMPRV");
    $this->patient->prevenir_prenom  = $this->consume("PRENOMPRV");
    $this->patient->prevenir_adresse = $this->consume("ADRPREV");
    $this->patient->prevenir_ville   = $this->consume("VILPRV");
    $this->patient->prevenir_cp      = $this->consume("CODPOSPRV");
    $this->patient->prevenir_tel     = $this->consume("TELPRV");
    $this->patient->prevenir_parente = @$transformParente[$this->consume("PARPRV")];
    
    $this->patient->tel2             = null;
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

    $id400Pat = new CIdSante400();
    $id400Pat->id400 = $this->consume("NIP");
    $id400Pat->bindObject($this->patient);

    $this->markStatus("P");

    // Import du séjour
    static $transformHospi = array (
      "HO" => "comp",
      "AM" => "ambu",
      "EX" => "exte",
      "CH" => "ambu",
      "DI" => "ambu",
    );

    static $prevDays = array (
      "comp" => 5,
      "ambu" => 1,
      "exte" => 0,
    );

    static $transformHospiDP = array (
      "CH" => "Z082",
      "DI" => "Z49",
    );

    $hospi = $this->consume("TYPSEJ");

    $this->sejour = new CSejour;
    $this->sejour->patient_id   = $this->patient->_id;
    $this->sejour->praticien_id = $this->praticien->_id;
    
    @$transformHospi[$hospi];
    $this->sejour->type = @$transformHospi[$hospi];
    $this->sejour->DP   = @$transformHospiDP[$hospi];

    switch ($this->consume("ETASEJ")) {
    	case "F": // Prévu
      $this->sejour->entree_prevue = $this->consumeDateTime("DATENT", "HREENT");
      $this->sejour->sortie_prevue = $this->consumeDateTime("DATSOR", "HRESOR");
  		break;
    
      case "P": // Présent
      case "S": // Sorti
      $this->sejour->entree_reelle = $this->consumeDateTime("DATENT", "HREENT");
      $this->sejour->sortie_reelle = $this->consumeDateTime("DATSOR", "HRESOR");
      break;
    }

    $id400Sej = new CIdSante400();
    $id400Sej->id400 = $this->consume("NUMDOS");
    $id400Sej->_last_id = $this->consume("DOSPRV");
    $id400Sej->bindObject($this->sejour);
    
    // Rectifications sur les dates prévues
    if ($this->sejour->entree_prevue == "0000-00-00 00:00:00") {
      $this->sejour->entree_prevue = $this->sejour->entree_reelle;
    }
    
    if ($this->sejour->sortie_prevue == "0000-00-00 00:00:00") {
      $nbDays = $prevDays[$this->sejour->type];
      $this->sejour->sortie_prevue = mbDate("+ $nbDays DAYS", $this->sejour->entree_prevue);
    }
    
    $this->sejour->store();
    
    $this->markStatus("S");
        
    // Import de la naissance
    $this->naissance = new CNaissance(); 
    $this->naissance->nom_enfant      = $this->consume("NOMBEB");
    $this->naissance->prenom_enfant   = $this->consume("PRENOMBEB");
    $this->naissance->date_prevue     = $this->consume("DATACCPRV");
    $this->naissance->date_reelle     = $this->consume("DATACC");
    $this->naissance->debut_grossesse = $this->consume("DATDEBGRO");
    
    if (!$this->naissance->check()) {
      $id400Nais = new CIdSante400();
      $id400Nais->id400 = $id400Sej->id400;
      $id400Nais->bindObject($this->naissance);
    }
    
    $this->markStatus("N");
  }
}
?>
