<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass("ecap", "mouvementecap");

class CMouvSejourEcap extends CMouvementEcap {  
  const STATUS_ETABLISSEMENT = 0;
  const STATUS_FONCSALL      = 1;
  const STATUS_PRATICIEN     = 2;
  const STATUS_PATIENT       = 3;
  const STATUS_SEJOUR        = 4;
  const STATUS_OPERATION     = 5;
  const STATUS_ACTES         = 6;
  const STATUS_NAISSANCE     = 7;
  
  const PROFIL_MEDECIN      =  1;
  const PROFIL_CHIRURGIEN   =  2;
  const PROFIL_ANESTHESISTE =  4;
  const PROFIL_RADIOLOGUE   =  8;
  const PROFIL_BIOLOGISTE   = 16;
  const PROFIL_DENTISTE     = 32;
  
  public $sejour        = null;
  public $etablissement = null;
  public $fonction      = null;
  public $salle         = null;
  public $patient       = null;
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
  protected $dheCINT = null;
  
  function __construct() {
    parent::__construct();
    $this->table = "TRSJ0";
  }

  function synchronize() {
    $this->syncEtablissement();
    $this->syncFonction();
    $this->syncSalle();
    
    // Praticien du séjour si aucune DHE
    $this->syncPatient();
    
    // Le séjour ne trouve pas son patient
    if (!$this->patient->_id) {
      $this->trace("Introuvable", "Patient");
      $this->starStatus(self::STATUS_SEJOUR);
      $this->starStatus(self::STATUS_OPERATION);
      $this->starStatus(self::STATUS_PRATICIEN);
      $this->starStatus(self::STATUS_ACTES);
      $this->starStatus(self::STATUS_NAISSANCE);
      return;
    }
    
    // Contenu du séjour
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
    
    $query = "SELECT * 
			FROM $this->base.ECTXPF
      WHERE TXCIDC = ?  AND TXCDIV = '01' AND TXCSDV = '01'
      AND TXTABL = ?
      AND TXZONE = ?
      AND TXCL = ?";
    
    $tx400 = new CRecordSante400;
    $tx400->query($query, $values);
    return $tx400->data["TXTX"];
  }
  
  function syncEtablissement() {
    $CIDC = $this->consume("CIDC");
    
    $this->id400Etab = new CIdSante400();
    $this->id400Etab->id400 = $CIDC;
    $this->id400Etab->tag = "eCap";
    $this->id400Etab->object_class = "CGroups";

    $this->etablissement = $this->id400Etab->getCachedObject();
    if ($this->etablissement->_id) {
      $this->trace($this->etablissement->getDBFields(), "Etablissement depuis le cache");
      $this->markCache(self::STATUS_ETABLISSEMENT);
      return;    
    } 
        
    $etab400 = new CRecordSante400();
    $query = "SELECT * FROM $this->base.ECCIPF WHERE CICIDC = ?";
    $values = array (
      $CIDC,       
    );
    
    $etab400->query($query, $values);
    $etab400->valuePrefix = "CI";
    $this->etablissement->adresse        = $etab400->consumeMulti("ZAD1", "ZAD2");
    $this->etablissement->cp             = $etab400->consume("CPO");
    $this->etablissement->ville          = $etab400->consume("ZLOC");
    $this->etablissement->tel            = $etab400->consume("ZTEL");
    $this->etablissement->fax            = $etab400->consume("ZFAX");
    $this->etablissement->web            = $etab400->consume("ZWEB");
    $this->etablissement->mail           = $etab400->consume("MAIL");
    $this->etablissement->domiciliation  = $etab400->consume("FINS");
		
    $etablissement = new CGroups;
    $etablissement->text = $etab400->consume("ZIDC");
    $etablissement->raison_sociale = $etablissement->text;

    $this->trace($this->etablissement->getDBFields(), "Etablissement à enregistrer");

    $this->id400Etab->bindObject($this->etablissement, $etablissement);

    $id400EtabSHS = new CIdSante400();
    $id400EtabSHS->loadLatestFor($this->etablissement, "eCap SHS");
    $id400EtabSHS->last_update = mbDateTime();
    $id400EtabSHS->id400 =  $etab400->consume("CSHS");
    $id400EtabSHS->store();
    
    $this->etablissement->loadBlocs();
    if (!count($this->etablissement->_ref_blocs)) {
    	$bloc = new CBlocOperatoire();
    	$bloc->group_id = $this->etablissement->_id;
    	$bloc->nom = "Bloc Import eCap";
    	$bloc->store();
    }
    
    $this->trace($etab400->data, "Données établissement non importées");
    
    $this->markStatus(self::STATUS_ETABLISSEMENT);
  }
  
  function syncFonction() {
    $id400Func = new CIdSante400();
    $id400Func->id400 = $this->id400Etab->id400;
    $id400Func->tag = "eCap";
    $id400Func->object_class = "CFunctions";

    $this->fonction = $id400Func->getCachedObject();
    if ($this->fonction->_id) {
      $this->trace($this->fonction->getDBFields(), "Cabinet depuis le cache");
      $this->markCache(self::STATUS_FONCSALL);
      return;    
    } 

    $this->fonction->group_id = $this->etablissement->_id;
    $this->fonction->loadMatchingObject();
    $this->fonction->type = "cabinet";
    $this->fonction->text = "Import eCap";
    $this->fonction->color = "00FF00";
    $this->fonction->compta_partagee = '0';
    
    $this->trace($this->fonction->getDBFields(), "Cabinet à enregistrer");

    $id400Func->bindObject($this->fonction);
    
    $this->markStatus(self::STATUS_FONCSALL);
  }
  
  function syncSalle() {
    $id400Salle = new CIdSante400();
    $id400Salle->id400 = $this->id400Etab->id400;
    $id400Salle->tag = "eCap";
    $id400Salle->object_class = "CSalle";

    $this->salle = $id400Salle->getCachedObject();
    if ($this->salle->_id) {
      $this->trace($this->salle->getDBFields(), "Salle depuis le cache");
      $this->markCache(self::STATUS_FONCSALL);
      return;
    } 
    $this->etablissement->loadBlocs();
    $bloc = reset($this->etablissement->_ref_blocs);
    $this->salle->bloc_id = $bloc->_id;
    $this->salle->nom = "Import eCap";
    $this->salle->stats = "0";
    
    $this->trace($this->salle->getDBFields(), "Salle à enregistrer");

    $id400Salle->bindObject($this->salle);
    
    $this->markStatus(self::STATUS_FONCSALL);
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
      $this->trace($praticien->getDBFields(), "Praticien depuis le cache");
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
      $query = "SELECT * FROM $this->base.ECPRPF 
				WHERE PRCIDC = ? AND PRCDIV = '01' AND PRCSDV = '01' 
				AND PRCPRT = ?";
      $values = array (
        $this->id400Etab->id400, 
        $CPRT,
      );
       
      $prat400 = new CRecordSante400();
      $prat400->loadOne($query, $values);
      $prat400->valuePrefix = "PR";
      $this->trace($prat400->data, "Données praticien à importer");
  
      $praticien->_user_last_name  = $prat400->consume("ZNOM");
      $praticien->_user_first_name = $prat400->consume("ZPRE");
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
      
      $query = "SELECT * FROM $this->base.ECSPPF
				WHERE SPCSPE = ?";
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
    
    // Uniquement utilisé à la première utilisation
    $pratDefault = new CMediusers;
    $pratDefault->function_id = $this->fonction->_id;
    
		// Permettre plusieurs praticiens en doublant avec des CPRT différents
    $nomsPraticien     = split(" ", $praticien->_user_last_name);
    $prenomsPraticiens = split(" ", $praticien->_user_first_name);
		$pratDefault->makeUsername($prenomsPraticiens[0], join($nomsPraticien, ""), $CPRT);
    
    // Type de mediuser
    if (isset($prat400)) {
	    $PROF = $prat400->consume("PROF");
	    if ($PROF & self::PROFIL_MEDECIN)      $pratDefault->_user_type = 13;
	    if ($PROF & self::PROFIL_CHIRURGIEN)   $pratDefault->_user_type = 3;
	    if ($PROF & self::PROFIL_ANESTHESISTE) $pratDefault->_user_type = 4;
    }

    $this->trace($praticien->getValues(), "Praticien à enregistrer");
    
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
      $this->trace($this->patient->getDBFields(), "Patient depuis le cache");
      $this->markCache(self::STATUS_PATIENT);
      return;
    }
    
    $pat400 = new CRecordSante400();
    $query = "SELECT * FROM $this->base.ECPAPF 
			WHERE PACIDC = ? AND PACDIV = '01' AND PACSDV = '01'
			AND PADMED = ?";
    $values = array (
      $this->id400Etab->id400,
      $DMED,
    );
    $pat400->query($query, $values);
    if (!$pat400->data) {
			$this->setStatus(self::STATUS_PATIENT);
			$this->starStatus(self::STATUS_SEJOUR);
			return;
    }
    
    $pat400->valuePrefix = "PA";

    $this->patient = new CPatient;
    $this->patient->nom              = $pat400->consume("ZNOM");
    $this->patient->prenom           = mbGetValue($pat400->consume("ZPRE"), "Inconnu");
    
    $this->patient->_specs["naissance"]->mask = null;
    $this->patient->naissance        = $pat400->consumeDate("DNAI");
    $this->patient->loadMatchingPatient();
    
    $this->patient->nom_jeune_fille  = $pat400->consume("ZNJF");
    $this->patient->sexe             = @$transformSexe[$pat400->consume("ZSEX")];
    $this->patient->civilite         = "guess";
    $this->patient->adresse          = $pat400->consumeMulti("ZAD1", "ZAD2");
    $this->patient->ville            = $pat400->consume("ZVIL");
    $this->patient->cp               = $pat400->consume("CPO");
    $this->patient->tel              = $pat400->consumeTel("ZTL1");
    $this->patient->tel2             = $pat400->consumeTel("ZTL2");
    
//  Le matricule n'est actuellement que mal recu par la DHE donc on garde celle de Mediboard
//    $this->patient->matricule         = $pat400->consume("NSEC") . $pat400->consume("CSEC");

    $this->patient->rang_beneficiaire = str_pad($pat400->consume("RBEN"), 2, "0", STR_PAD_LEFT);

//    $this->patient->pays              = $pat400->consume("ZPAY");
    $this->patient->nationalite       = @$transformNationalite[$pat400->consume("CNAT")];

    $this->trace($this->patient->getDBFields(), "Patient à enregistrer");
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
    $this->dheCINT = $dheECap->consume("CINT");

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
      $query = "SELECT * FROM $this->base.ECMOPF 
				WHERE MOCMOT = ?";
          
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
      "5" => "psy",
      "6" => "urg" 
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

    return $IDAT;
  }
  
  function syncDHE() {
    $values = array (
      $this->id400Etab->id400,
      $this->id400Pat->id400,
      $this->id400Sej->id400,
    );
        
    $this->trace($this->sejour->getDBFields(), "Séjour (via DHE) à enregistrer");

    $query = "SELECT * 
			FROM $this->base.ECATPF
			WHERE ATCIDC = ? AND ATCDIV = '01' AND ATCSDV = '01'
			AND ATDMED = ?
		  AND ATNDOS = ?";

    // Recherche de la DHE
    $dheECap = new CRecordSante400();
    $dheECap->valuePrefix = "AT";
    $dheECap->query($query, $values);
    
    // Pas d'attendu pour ce séjour
    if (null == $IDAT = $this->mapDHE($dheECap)) {
      return;
    }
    
    $tag = "eCap DHE CIDC:{$this->id400Etab->id400}";
    $this->id400DHE = new CIdSante400();
    $this->id400DHE->id400 = $IDAT;
    $this->id400DHE->tag = $tag;
        
    $this->sejour->_check_bounds = false;
    $this->id400DHE->bindObject($this->sejour);

    $this->markStatus(self::STATUS_SEJOUR);
  }
  
  function syncOperations() {
    $this->starStatus(self::STATUS_OPERATION);
    $this->starStatus(self::STATUS_ACTES);
  }

  /**
   * Map un séjour eCap en séjour Mediboard
   * 
   */
  function mapSej(CRecordSante400 $sejECap) {
    // Praticien
    $CPRT = $sejECap->consume("CPRT");
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
    
    // En cas de collision, on a affaire au même séjour
    $collisions = $this->sejour->getCollisions();
    if (count($collisions)) {
      $collision = reset($collisions);
      $this->sejour->_id = $collision->_id;
    }
            
    $this->trace($this->sejour->getDBFields(), "Séjour à enregistrer");
    $this->sejour->_check_bounds = false;
    $this->id400Sej->bindObject($this->sejour);
    
    $this->markStatus(self::STATUS_SEJOUR);
  }
  
  function syncNaissance() {
    $this->starStatus(self::STATUS_NAISSANCE);
  }
}
?>
