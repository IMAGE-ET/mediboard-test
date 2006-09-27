<?php

global $AppUI;

require_once($AppUI->getModuleClass("dPsante400", "idsante400"));

$etablissements = array(
  "310" => "St Louis",
  "474" => "Sauvegarde",
  "927" => "clinique du Tonkin"
);

class CMouvSejourTonkin {
  var $dbh = null;
  var $base = "GT_EAI";
  var $table = "SEJMDB";
  
  var $sejour = null;
  var $etablissement = null;
  var $patient = null;
  
  function __construct() {
  }
  
  function load() {
    $sql = "SELECT * FROM $this->base.$this->table";
    foreach ($this->query($sql) as $key => $value) {
      $this->$key = $value;
    }
    
    mbTrace($this, "Mouvement de séjour");
  }
  
  /**
   * Transforms a DDMMYYYY AS400 date into a YYYY-MM-DD SQL date 
   */
  function transformDate($date400) {
    return preg_replace("/(\d{2})(\d{2})(\d{4})/i", "$3-$2-$1", $date400);
  }
  
  function proceed() {
    // Import de l'établissement
    $etp01 = $this->query("SELECT * FROM PICLIN$this->CODETB.ETP01");
    $this->etablissement = new CGroups;
    $this->etablissement->text = $etp01["ETRSOC"];
    $this->etablissement->raison_sociale = $etp01["ETRSOC"];
    $this->etablissement->adresse = $etp01["ETADRE"] . "\n" . $etp01["ETADRS"];
    $this->etablissement->cp = $etp01["ETCODP"];
    $this->etablissement->ville = $etp01["ETVILE"];
    $this->etablissement->tel = $etp01["ETTLPH"];
    $this->etablissement->directeur = $etp01["ETNOMD"];
    $this->etablissement->domiciliation = $etp01["ETNDOM"];
    $this->etablissement->siret = $etp01["ETSIRT"];
//    $this->etablissement->ape = $etp01["ETCAPE"];

    $id400Etab = new CIdSante400();
    $id400Etab->bindObject($this->etablissement, $this->CODETB);

    // Mise à jour du patient
    $this->patient = new CPatient;
    $this->patient->nom              = $this->NOMPAT;
    $this->patient->prenom           = $this->PRENOMPAT;
    $this->patient->nom_jeune_fille  = $this->NOMJFIPAT;
    $this->patient->naissance        = $this->transformDate($this->DATNAIPAT);
//    $this->patient->sexe             = $this->;
    $this->patient->adresse          = $this->ADRPAT . "\n" . $this->ADRSUIPAT;
    $this->patient->ville            = $this->VILPAT;
    $this->patient->cp               = $this->CODPOSPAT;
//    $this->patient->tel              = $this->;
//    $this->patient->tel2             = $this->;
//    $this->patient->medecin_traitant = $this->;
//    $this->patient->medecin1         = $this->;
//    $this->patient->medecin2         = $this->;
//    $this->patient->medecin3         = $this->;
//    $this->patient->incapable_majeur = $this->;
//    $this->patient->ATNC             = $this->;
//    $this->patient->matricule        = $this->;
//    $this->patient->SHS              = $this->;
//    $this->patient->regime_sante     = $this->;
//    $this->patient->rques            = $this->;
//    $this->patient->listCim10        = $this->;
//    $this->patient->cmu              = $this->;
//    $this->patient->ald              = $this->;
    mbTrace($this->patient, "Patient");
  }
  
  function query($sql) {
    $dsn = "sante400";
    
    try {
      if (!$this->dbh) {
        $this->dbh = new PDO("odbc:$dsn", "", "");
      }
      $sth = $this->dbh->prepare($sql);
      $sth->execute();
      return $sth->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      trigger_error("Error querying '$sql' on data source name '$dsn' ! : " . $e->getMessage(), E_USER_ERROR);
    }    
  }
}
?>
