<?php

global $AppUI;
require_once $AppUI->getModuleClass("dPsante400", "recordsante400");

class CMouvSejourTonkin extends CRecordSante400 {
  var $dbh = null;
  var $base = "GT_EAI";
  var $table = "SEJMDB";
  var $status = null;
  
  var $sejour = null;
  var $etablissement = null;
  var $fonction = null;
  var $patient = null;
  var $praticien = null;
  var $naissance = null;
  
  function __construct() {
  }

  function load() {
    $this->query("SELECT * FROM $this->base.$this->table");
  }
  
  function deleteRow() {
  }
  
  function markRow() {
  }
  
  function markStatus($letter) {
    $this->status .= $letter;
  }

  function proceed() {
    $this->status = "";
    try {
      $this->synchronize();
      $this->deleteRow();
    } catch (Exception $e) {
      trigger_error($e->getMessage(), E_USER_WARNING);
      $this->markRow();
    }

    mbTrace($this->data, "Mouvement de séjour");
    mbTrace($this->status, "Status de l'import");
  }
  
  function synchronize() {
    
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
    $id400Etab->bindObject($this->etablissement, $CODETB);
    
    $this->markStatus("E");

    // Fonction
    $this->fonction = new CFunctions();
    $this->fonction->group_id = $this->etablissement->group_id;
    $this->fonction->loadMatchingObject();
    $this->fonction->text = "Import Santé400";
    $this->fonction->color = "00FF00";

    $id400Func = new CIdSante400();
    $id400Func->bindObject($this->fonction, $CODETB);
    
    $this->markStatus("F");

    // Praticien
    $CODMEDREF = $this->consume("CODMEDREF");
    $mdp01 = new CRecordSante400();
    $mdp01->query("SELECT * FROM PICLIN$CODETB.MDP01 WHERE MDPRAT = $CODMEDREF");
    
    $nomsPraticien     = split(" ", $mdp01->consume("MDNOMS"));
    $prenomsPraticiens = split(" ", $mdp01->consume("MDPRES"));

    $this->praticien = new CMediusers;
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
    $praticien->_user_password = $praticien->_user_username;

    $id400Prat = new CIdSante400();
    $id400Prat->bindObject($this->praticien, $CODMEDREF, $praticien);
    
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
    $id400Pat->bindObject($this->patient, $this->consume("NIP"));

    $this->markStatus("P");

    // Import du séjour
    static $transformHospi = array (
      "HO" => "hospi",
      "AM" => "ambu",
      "EX" => "exte",
      "CH" => "ambu",
      "DI" => "ambu",
    );

    static $transformHospiDP = array (
      "CH" => "Z082",
      "DI" => "Z49",
    );

    $this->sejour = new CSejour;
    $this->sejour->entree_prevue = $this->consumeDateTime("DATENTPRV", "HREENTPRV");
    $this->sejour->sortie_prevue = $this->consumeDateTime("DATSORPRV", "HRESORPRV");
    $this->sejour->entree_reelle = $this->consumeDateTime("DATENT", "HREENT");
    $this->sejour->sortie_rellle = $this->consumeDateTime("DATSOR", "HRESOR");

    $hospi = $this->consume("TYPSEJ");
    $this->sejour->type = @$transformHospi[$hospi];
    $this->sejour->DP   = @$transformHospiDP[$hospi];

    mbTrace($this->sejour->getProps(), "Sejour");
        
    // Import de la naissance
    $this->naissance = new CNaissance(); 
    $this->naissance->nom_enfant      = $this->consume("NOMBEB");
    $this->naissance->prenom_enfant   = $this->consume("PRENOMBEB");
    $this->naissance->date_prevue     = $this->consume("DATACCPRV");
    $this->naissance->date_reelle     = $this->consume("DATACC");
    $this->naissance->debut_grossesse = $this->consume("DATDEBGRO");
    
//    if ($this->naissance->check()) {
//      $id400Nais = new CIdSante400();
//      $id400Nais->bindObject($this->naissance, );
//    }
    
    $this->markStatus("N");
  }
}
?>
