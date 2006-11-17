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
  
  protected $id400EtabECap = null;
  
  function __construct() {
    $this->base = "ECAPFILE";
    $this->table = "TRSJ0";
    $this->completeMark = ">EFCPSN";
    $this->prodField = "ETAT";
    $this->idField = "INDEX";
    $this->typeField = "TRACTION";
  }

  function synchronize() {
    $this->syncEtablissement();
    $this->syncFonction();
    $this->syncPraticien();
//    $this->syncPatient();
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
   
  function syncPraticien() {
    $CPRT = $this->consume("A_CPRT");
    $prat400 = new CRecordSante400();
    $prat400->query("SELECT * FROM $this->base.ECPRPF WHERE PRCIDC = {$this->id400EtabECap->id400} AND PRCPRT = $CPRT");

    $this->trace($prat400->data, "Données praticien");

    $nomsPraticien     = split(" ", $prat400->consume("PRZNOM"));
    $prenomsPraticiens = split(" ", $prat400->consume("PRZPRE"));

    $this->praticien = new CMediusers;
    $this->praticien->_user_type = 3; // Chirurgien
    $this->praticien->_user_username = strtolower($prenomsPraticiens[0] . $nomsPraticien[0]);
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
    
    $CSPE = $prat400->consume("PRCSPE");
    $spec400 = new CRecordSante400;
    $spec400->query("SELECT * FROM $this->base.ECSPPF WHERE SPCSPE= $CSPE");
    $LISP = $spec400->consume("SPLISP");
    $this->praticien->commentaires = "Spécialité : $LISP";
    
    $praticien = new CMediusers;
    $praticien->function_id = $this->fonction->function_id;

    $this->trace($this->praticien->getProps(), "Praticien");
    $this->trace($prat400->data, "Données praticien restantes");
    return;
    $id400Prat = new CIdSante400();
    $id400Prat->id400 = $CODMEDREF;
    $id400Prat->bindObject($this->praticien, $praticien);
    
    $this->markStatus("C");
  }
  
  function syncPatient() {
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
    $this->patient->tel              = $this->consumeTel("TELPAT");
    $this->patient->matricule        = $this->consume("NSSPAT") . $this->consume("CSSPAT");

    $this->patient->profession       = $this->consume("PROPAT");
    $this->patient->pays             = $this->consume("PYSPAT");
    $this->patient->nationalite      = @$transformNationalite[$this->consume("NATPAT")];
    $this->patient->lieu_naissance   = $this->consume("LIENAIPAT");
    
    $this->patient->employeur_nom     = $this->consume("NOMEMP");
    $this->patient->employeur_adresse = $this->consume("ADREMP") . "\n" . $this->consume("ADRSUIEMP");
    $this->patient->employeur_ville   = $this->consume("VILEMP");
    $this->patient->employeur_cp      = $this->consume("CODPOSEMP");
    $this->patient->employeur_tel     = $this->consumeTel("TELEMP");
    $this->patient->employeur_urssaf  = $this->consume("URSSAFEMP");

    $this->patient->prevenir_nom     = $this->consume("NOMPRV");
    $this->patient->prevenir_prenom  = $this->consume("PRENOMPRV");
    $this->patient->prevenir_adresse = $this->consume("ADRPREV");
    $this->patient->prevenir_ville   = $this->consume("VILPRV");
    $this->patient->prevenir_cp      = $this->consume("CODPOSPRV");
    $this->patient->prevenir_tel     = $this->consumeTel("TELPRV");
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

    
  }
}
?>
