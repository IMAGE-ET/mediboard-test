<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass("ecap", "mouvsejourecap");

class CMouvInterventionECap extends CMouvSejourEcap {  
  
  function __construct() {
    parent::__construct();
    $this->table = "TRINT";
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
    $tags = array();
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
    $tags = array();
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
      throw new Exception("2 séjours différents trouvés pour DHE et Séjour (MbID: $sejourDHE->_id et $sejourSej->_id)");
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
      $this->trace($this->sejour->getDBFields(), "Séjour à enregistrer depuis DHE eCap");
      $this->sejour->_check_bounds = false;
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
	    $this->trace($this->sejour->getDBFields(), "Séjour à enregistrer depuis Sej eCap");
      $this->sejour->_check_bounds = false;
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
    $operation->annulee = $this->type == "S" ? '1' : '0';
    $this->trace("Opération annulée", "Mouvement de suppression");
  }

  function mapBindOperation(CRecordSante400 $operECap) {
    if (!$this->sejour->_id) {
      return;
    }
    
    $this->trace($operECap->data, "Opération trouvée"); 
    
    $operation = new COperation;
    $operation->sejour_id = $this->sejour->_id;
    $operation->chir_id   = $this->sejour->praticien_id;
    $operation->salle_id  = $this->salle->_id;
    
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
    $operation->debut_op = mbTime($entree_reelle);
    $operation->fin_op   = mbTime($sortie_reelle);
    
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

    $this->trace($operation->getDBFields(), "Opération à enregistrer");

    $id400Oper->bindObject($operation);
    
    $this->id400Opers[$CINT] = $id400Oper;      
    
    $this->operations[$CINT] = $operation;
    $this->syncActes($CINT);
  }

  function syncActes($CINT) {
    // Ne synchroniser ler actes qu'après validation
    if ($this->consume("CBLQ") !== "03") {
//    if (!in_array("CBLQ", $this->changedFields) || $this->consume("CBLQ") !== "03") {
      $this->starStatus(self::STATUS_ACTES);
      return;
    }
    
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
      
      // Acte non validé
      if ($CPRT == "0") {
        continue;
      }
      
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

      $this->trace($acte->getDBFields(), "Acte à enregistrer");
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
}
?>
