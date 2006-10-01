<?php

global $AppUI;
require_once $AppUI->getModuleClass("dPsante400", "recordsante400");

class CMouvSejourTonkin extends CRecordSante400{
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
  }
  
  function synchronize() {
    
    // Etablissement
    $CODETB = $this->consume("CODETB");
    $etp01 = new CRecordSante400();
    $etp01->query("SELECT * FROM PICLIN$CODETB.ETP01");

    $this->etablissement = new CGroups;
    $this->etablissement->text           = $etp01->consume("ETRSOC");
    $this->etablissement->raison_sociale = $this->etablissement->text;
    $this->etablissement->adresse        = $etp01->consume("ETADRE") . "\n" . $etp01->consume("ETADRS");
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
    $CODMEDCHI = $this->consume("CODMEDCHI");
    $mdp01 = new CRecordSante400();
    $mdp01->query("SELECT * FROM PICLIN$CODETB.MDP01 WHERE MDPRAT = $CODMEDCHI");
    
    $nomsPraticien     = split(" ", $mdp01->consume("MDNOMS"));
    $prenomsPraticiens = split(" ", $mdp01->consume("MDPRES"));

    $this->praticien = new CMediusers;
    $this->praticien->function_id = $this->fonction->function_id;
    $this->praticien->_user_username =  strtolower($prenomsPraticiens[0] . $nomsPraticien[1]);
    $this->praticien->_user_last_name  = $nomsPraticien[1];
    $this->praticien->_user_first_name = join(" ", $prenomsPraticiens);
    $this->praticien->_user_email      = null;
    $this->praticien->_user_phone      = null;
    $this->praticien->_user_adresse    = null;
    $this->praticien->_user_cp         = null;
    $this->praticien->_user_ville      = null;
    $this->praticien->adeli            = $mdp01->consume("MDNIOM");

    mbTrace($mdp01->data, "Praticien Santé400");
    mbTrace($this->praticien, "Praticien Mediboard");

    
    $this->praticien = new CMediusers;
    
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
    
//    $this->patient->tel2             = $this->;
//    $this->patient->medecin_traitant = $this->;
//    $this->patient->medecin1         = $this->;
//    $this->patient->medecin2         = $this->;
//    $this->patient->medecin3         = $this->;
//    $this->patient->incapable_majeur = $this->;
//    $this->patient->ATNC             = $this->;
//    $this->patient->SHS              = $this->;
//    $this->patient->regime_sante     = $this->;
//    $this->patient->rques            = $this->;
//    $this->patient->listCim10        = $this->;
//    $this->patient->cmu              = $this->;
//    $this->patient->ald              = $this->;

    $id400Pat = new CIdSante400();
    $id400Pat->bindObject($this->patient, $this->consume("NIP"));
    
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
    $this->sejour->sortie_prevue = $this->consumeDateTime("DATDORPRV", "HRESORPRV");
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
    
    // TESTER L'INJECTION SQL
  }
}
?>
