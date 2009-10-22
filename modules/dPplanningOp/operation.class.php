<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("dPccam", "codable");

class COperation extends CCodable {
  // DB Table key
  var $operation_id  = null;

  // DB References
  var $sejour_id  = null;
  var $chir_id    = null;
  var $anesth_id  = null;
  var $plageop_id = null;

  // DB Fields S@nté.com communication
  var $code_uf    = null;
  var $libelle_uf = null;

  // DB Fields
  var $salle_id       = null;
  var $date           = null;
  var $libelle        = null;
  var $cote           = null;
  var $temp_operation = null;
  var $pause          = null;
  var $time_operation = null;
  var $examen         = null;
  var $materiel       = null;
  var $commande_mat   = null;
  var $info           = null;
  var $type_anesth    = null;  
  var $rques          = null;
  var $rank           = null;
  var $anapath        = null;
  var $labo           = null;
  var $prothese       = null;

  var $depassement        = null;
  var $forfait            = null;
  var $fournitures        = null;
  var $depassement_anesth = null;
  
  var $annulee = null;
  
  // Timings enregistrés
  var $entree_bloc     = null;
  var $entree_salle    = null;
  var $pose_garrot     = null;
  var $debut_op        = null;
  var $fin_op          = null;
  var $retrait_garrot  = null;
  var $sortie_salle    = null;
  var $entree_reveil   = null;
  var $sortie_reveil   = null;
  var $induction_debut = null;
  var $induction_fin   = null;
  var $horaire_voulu   = null;
  
  // Vérification du côté
  var $cote_admission      = null;
  var $cote_consult_anesth = null;
  var $cote_hospi          = null;
  var $cote_bloc           = null;
  
  // Visite de préanesthésie
  var $date_visite_anesth    = null;
  var $prat_visite_anesth_id = null;
  var $rques_visite_anesth   = null;
  var $autorisation_anesth   = null;
  
  // Form fields
  var $_hour_op        = null;
  var $_min_op         = null;
  var $_hour_urgence   = null;
  var $_min_urgence    = null;
  var $_lu_type_anesth = null;
  var $_codes_ccam     = array();
  var $_duree_interv   = null;
  var $_presence_salle = null;
  var $_hour_voulu     = null;
  var $_min_voulu      = null;
  var $_deplacee       = null;
  var $_compteur_jour  = null;
  var $_pause_min      = null;
  var $_pause_hour     = null;
  var $_protocole_prescription_anesth_id = null;
  var $_protocole_prescription_chir_id   = null;
  var $_move           = null;
  var $_password_visite_anesth = null;
  
  // Distant fields
  var $_datetime          = null;
  var $_datetime_reel     = null;
  var $_datetime_reel_fin = null;
  var $_ref_affectation   = null;
  
  // Links
  var $_link_editor = null;
  var $_link_viewer = null;

  // DB References
  var $_ref_chir           = null;
  var $_ref_plageop        = null;
  var $_ref_salle          = null;
  var $_ref_anesth         = null;
  var $_ref_type_anesth    = null;
  var $_ref_consult_anesth = null;
  var $_ref_anesth_visite  = null;
  var $_ref_actes_ccam     = array();
  var $_ref_hprim_files    = null;

  // External references
  var $_ext_codes_ccam = null;

 //Filter Fields
  var $_date_min	 	 = null;
  var $_date_max 		 = null;
  var $_plage 			 = null;
  var $_service 		 = null;
  var $_intervention = null;
  var $_specialite 	 = null;
  var $_scodes_ccam  = null;
  var $_prat_id      = null;
  var $_bloc_id      = null;
  var $_ccam_libelle = null;

  function COperation() {
    parent::__construct();
    $this->_locked = CAppUI::conf("dPplanningOp COperation locked");
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'operations';
    $spec->key   = 'operation_id';
    $spec->measureable = true;
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["sejour_id"]          = "ref notNull class|CSejour";
    $specs["chir_id"]            = "ref notNull class|CMediusers seekable";
    $specs["anesth_id"]          = "ref class|CMediusers";
    $specs["plageop_id"]         = "ref class|CPlageOp seekable";
    $specs["pause"]              = "time";
    $specs["salle_id"]           = "ref class|CSalle";
    $specs["date"]               = "date";
    $specs["code_uf"]            = "str length|3";
    $specs["libelle_uf"]         = "str maxLength|35";
    $specs["libelle"]            = "str seekable";
    $specs["cote"]               = "enum notNull list|droit|gauche|bilatéral|total|inconnu default|inconnu";
    $specs["temp_operation"]     = "time";
    $specs["entree_salle"]       = "time";
    $specs["sortie_salle"]       = "time";
    $specs["time_operation"]     = "time";
    $specs["examen"]             = "text helped";
    $specs["materiel"]           = "text helped seekable";
    $specs["commande_mat"]       = "bool";
    $specs["info"]               = "bool";
    $specs["type_anesth"]        = "ref class|CTypeAnesth";
    $specs["rques"]              = "text helped";
    $specs["rank"]               = "num max|255";
    $specs["depassement"]        = "currency min|0 confidential";
    $specs["forfait"]            = "currency min|0 confidential";
    $specs["fournitures"]        = "currency min|0 confidential";
    $specs["depassement_anesth"] = "currency min|0 confidential";
    $specs["annulee"]            = "bool";
    $specs["pose_garrot"]        = "time";
    $specs["debut_op"]           = "time";
    $specs["fin_op"]             = "time";
    $specs["retrait_garrot"]     = "time";
    $specs["entree_reveil"]      = "time";
    $specs["sortie_reveil"]      = "time";
    $specs["induction_debut"]    = "time";
    $specs["induction_fin"]      = "time";
    $specs["entree_bloc"]        = "time";
    $specs["anapath"]            = "enum list|1|0|? default|?";
    $specs["labo"]               = "enum list|1|0|? default|?";
    $specs["prothese"]           = "enum list|1|0|? default|?";
    $specs["horaire_voulu"]      = "time";
    
    $specs["cote_admission"]     = "enum list|droit|gauche";
    $specs["cote_consult_anesth"]= "enum list|droit|gauche";
    $specs["cote_hospi"]         = "enum list|droit|gauche";
    $specs["cote_bloc"]          = "enum list|droit|gauche";
    
    // Visite de préanesthésie
    $specs["date_visite_anesth"]    = "dateTime";
    $specs["prat_visite_anesth_id"] = "ref class|CMediusers";
    $specs["rques_visite_anesth"]   = "text helped";
    $specs["autorisation_anesth"]   = "bool default|0";

    $specs["facture"]                 = "bool default|0";

    $specs["_date_min"]               = "date";
    $specs["_date_max"]               = "date moreEquals|_date_min";
    $specs["_plage"]                  = "bool";
    $specs["_intervention"]           = "text";
    $specs["_prat_id"]                = "text";
    $specs["_bloc_id"]                = "ref class|CBlocOperatoire";
    $specs["_specialite"]             = "text";
    $specs["_ccam_libelle"]           = "bool default|1";
    $specs["_hour_op"]                = "";
    $specs["_min_op"]                 = "";
    $specs["_datetime"]               = "dateTime";
    $specs["_pause_min"]              = "numchar length|2";
    $specs["_pause_hour"]             = "numchar length|2";
    $specs["_move"]                   = "str";
    $specs["_password_visite_anesth"] = "password notNull";
    
    return $specs;
  }
  
  function getExecutantId($code_activite) {
    $this->loadRefChir();
    $this->loadRefPlageOp();
    return ($code_activite == 4 ? $this->_ref_anesth->user_id: $this->chir_id);
  }
  
	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["blood_salvages"]           = "CBloodSalvage operation_id";
	  $backProps["dossiers_anesthesie"]      = "CConsultAnesth operation_id";
	  $backProps["naissances"]               = "CNaissance operation_id";
	  $backProps["prescription_elements"]    = "CPrescriptionLineElement operation_id";
	  $backProps["prescription_medicaments"] = "CPrescriptionLineMedicament operation_id";
	  $backProps["perfusion"]                = "CPerfusion operation_id";
	  return $backProps;
	}

  function getTemplateClasses(){
    $this->loadRefsFwd();
    
    $tab = array();
    
    // Stockage des objects liés à l'opération
    $tab['COperation'] = $this->_id;
    $tab['CSejour'] = $this->_ref_sejour->_id;
    $tab['CPatient'] = $this->_ref_sejour->_ref_patient->_id;
    
    $tab['CConsultation'] = 0;
    $tab['CConsultAnesth'] = 0;
    
    return $tab;
  }
  
  function check() {
    global $AppUI;
    $msg = null;
    if(!$this->operation_id && !$this->chir_id) {
      $msg .= "Praticien non valide ";
    }
    
    $old = new COperation();
    $old->load($this->_id);
    
    // Vérification sur les dates des séjours et des plages op
    if (null === $this->plageop_id) {
      $this->plageop_id = $old->plageop_id;
    }
    
    if (null === $this->sejour_id) {
      $this->sejour_id = $old->sejour_id;
    }

    $this->loadRefSejour();
    $this->loadRefPlageOp();
    
    if($this->plageop_id !== null){
      if (!in_range(mbDate($this->_datetime), mbDate($this->_ref_sejour->entree_prevue), mbDate($this->_ref_sejour->sortie_prevue))) {
   	    $msg .= "Intervention en dehors du séjour";
      }
    }
    
    // Vérification de la signature de l'anesthésiste pour la
    // visite de pré-anesthésie
    if($this->prat_visite_anesth_id !== null && $this->prat_visite_anesth_id != $AppUI->user_id) {
      $anesth = new CUser();
      $anesth->load($this->prat_visite_anesth_id);
      if($anesth->user_password != md5($this->_password_visite_anesth)) {
        $msg .= "Mot de passe incorrect";
      }
    }
    return $msg . parent::check();
  }
  
  function delete() {
    $msg = parent::delete();
    $this->loadRefPlageOp();
    $this->_ref_plageop->reorderOp();
    return $msg;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_hour_op = intval(substr($this->temp_operation, 0, 2));
    $this->_min_op  = intval(substr($this->temp_operation, 3, 2));
    $this->_hour_urgence = intval(substr($this->time_operation, 0, 2));
    $this->_min_urgence  = intval(substr($this->time_operation, 3, 2));
    
    if($this->horaire_voulu){
      $this->_hour_voulu = intval(substr($this->horaire_voulu, 0, 2));
      $this->_min_voulu  = intval(substr($this->horaire_voulu, 3, 2)); 
    }
    
    if($this->pause){
      $this->_pause_hour = intval(substr($this->pause, 0, 2));
      $this->_pause_min  = intval(substr($this->pause, 3, 2)); 
    }
      
    if ($this->type_anesth != null) {
      $this->_ref_type_anesth = new CTypeAnesth;
      $this->_ref_type_anesth->load($this->type_anesth);;
      $this->_lu_type_anesth = $this->_ref_type_anesth->name;
    }
    
    if($this->debut_op && $this->fin_op && $this->fin_op>$this->debut_op){
      $this->_duree_interv = mbSubTime($this->debut_op,$this->fin_op);
    }
    if($this->entree_salle && $this->sortie_salle && $this->sortie_salle>$this->entree_salle){
      $this->_presence_salle = mbSubTime($this->entree_salle,$this->sortie_salle);
    }
    if($this->plageop_id) {
      $this->_link_editor = "index.php?m=dPplanningOp&tab=vw_edit_planning&operation_id=".$this->_id;
    } else {
      $this->_link_editor = "index.php?m=dPplanningOp&tab=vw_edit_urgence&operation_id=".$this->_id;
    }
    $this->_acte_depassement = $this->depassement;   
 }
  
  function updateDBFields() {
    if (count($this->_codes_ccam)) {
      $this->codes_ccam = implode("|", $this->_codes_ccam);
    }
    
    if ($this->codes_ccam) {
      $this->codes_ccam = strtoupper($this->codes_ccam);
      $codes_ccam = explode("|", $this->codes_ccam);
      $XPosition = true;
			// @TODO: change it to use removeValue
      while($XPosition !== false) {
        $XPosition = array_search("-", $codes_ccam);
        if ($XPosition !== false) {
          array_splice($codes_ccam, $XPosition, 1);
        }
      }
      $this->codes_ccam = implode("|", $codes_ccam);
    }
    if($this->_hour_op !== null and $this->_min_op !== null) {
      $this->temp_operation = "$this->_hour_op:$this->_min_op:00";
    }
    if($this->_hour_urgence !== null and $this->_min_urgence !== null) {
      $this->time_operation = "$this->_hour_urgence:$this->_min_urgence:00";
    }
    if($this->_hour_voulu != null and $this->_min_voulu != null) {
      $this->horaire_voulu = "$this->_hour_voulu:$this->_min_voulu:00";
    }
    if($this->_pause_hour !== null and $this->_pause_min !== null) {
      $this->pause = "$this->_pause_hour:$this->_pause_min:00";
    }
    
    $this->completeField('rank');
    $this->completeField('plageop_id');
    if($this->_move) {
      $op = new COperation;
      $op->plageop_id = $this->plageop_id;
      
      switch ($this->_move) {
        case 'before':
          $op->rank = $this->rank-1;
          if ($op->loadMatchingObject()) {
            $op->rank = $this->rank;
            $op->store(false);
            $this->rank -= 1;
          }
        break;
        
        case 'after':
          $op->rank = $this->rank+1;
          if ($op->loadMatchingObject()) {
            $op->rank = $this->rank;
            $op->store(false);
            $this->rank += 1;
          }
        break;
        
        case 'out':
          $this->rank = 0;
          $this->time_operation = '00:00:00';
          $this->pause = '00:00:00';
        break;
        
        case 'last':
	        if ($op->loadMatchingObject('rank DESC')) {
		        $this->rank = $op->rank+1;
		      }
		    break;
        default;
      }
      $this->_move = null;
    }
  }

  function store($reorder = true) {
    if ($msg = parent::store()) {
      return $msg;
    }
    // Cas d'une annulation
    if (!$this->annulee) {
      // Si pas une annulation on recupére le sejour
      // et on regarde s'il n'est pas annulé
      $this->loadRefSejour();
      if($this->_ref_sejour->annule) {
        $this->_ref_sejour->annule = 0;
        $this->_ref_sejour->store();
      }

      // Application des protocoles de prescription en fonction de l'operation->_id
      if ($this->_protocole_prescription_chir_id || $this->_protocole_prescription_anesth_id) {
        $this->_ref_sejour->_protocole_prescription_chir_id = $this->_protocole_prescription_chir_id;
        $this->_ref_sejour->_protocole_prescription_anesth_id = $this->_protocole_prescription_anesth_id;
        $this->_ref_sejour->applyProtocolesPrescription($this->_id);
        
        // On les nullify pour eviter de les appliquer 2 fois
        $this->_protocole_prescription_anesth_id = null;
        $this->_protocole_prescription_chir_id = null;
        $this->_ref_sejour->_protocole_prescription_chir_id = null;
        $this->_ref_sejour->_protocole_prescription_anesth_id = null;
      }
    } elseif($this->rank != 0) {
      $this->rank = 0;
      $this->time_operation = "00:00:00";
      $this->store($reorder);
    }
    
    // Vérification qu'on a pas des actes CCAM codés obsolètes
    if($this->codes_ccam) {
      $this->loadRefsActesCCAM();
      foreach($this->_ref_actes_ccam as $keyActe => $acte) {
        if(strpos(strtoupper($this->codes_ccam), strtoupper($acte->code_acte)) === false) {
          $this->_ref_actes_ccam[$keyActe]->delete();
        }
      }
    }
    
    // Cas de la création dans une plage de spécialité
    if($this->plageop_id) {
      $plageTmp = new CPlageOp;
      $plageTmp->load($this->plageop_id);
      if($plageTmp->spec_id) {
        $plageTmp->spec_id = null;
        $chirTmp = new CMediusers;
        $chirTmp->load($this->chir_id);
        $plageTmp->chir_id = $chirTmp->user_id;
        $plageTmp->spec_id = "";
        $plageTmp->store();
      } elseif($reorder) {
        $plageTmp->spec_id = "";
        $plageTmp->store();
      }
    }
    return $msg;
  }
  
  /**
   * Load list overlay for current group
   */
  function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array()) {
		$ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
    // Filtre sur l'établissement
		$g = CGroups::loadCurrent();
		$where["sejour.group_id"] = "= '$g->_id'";
    
    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }
  
  function loadView() {
    $this->loadRefsFwd();
    $this->loadRefsActesCCAM();
    $this->loadExtCodesCCAM(1);
  }
  
  function loadComplete() {
    parent::loadComplete();
    foreach ($this->_ref_actes_ccam as &$acte_ccam) {
      $acte_ccam->loadRefsFwd();
    }
  }
  
  function loadRefChir($cache = 0) {
    $this->_ref_chir = new CMediusers;
    if($cache) {
      $this->_ref_chir = $this->_ref_chir->getCached($this->chir_id);
    } else {
      $this->_ref_chir->load($this->chir_id);
    }
    
    $this->_praticien_id = $this->_ref_chir->_id;
  }
  
  function loadRefPraticien($cache = 0){
    $this->loadRefChir($cache);
    $this->_ref_praticien =& $this->_ref_chir;
  }
  
  function getActeExecution() {
    $this->loadRefPlageOp();
  }
  
  function loadRefAffectation() {
  $this->loadRefPlageOp();
    $where = array();
    $where["sejour_id"] = "= $this->sejour_id";
    $where["entree"] = " <= '$this->_datetime'";
    $where["sortie"] = " >= '$this->_datetime'";
    $this->_ref_affectation = new CAffectation();
    $this->_ref_affectation->loadObject($where);
    $this->_ref_affectation->loadRefsFwd();
    $this->_ref_affectation->_ref_lit->loadRefsFwd();
    $this->_ref_affectation->_ref_lit->_ref_chambre->loadRefsFwd();
  }
  
  /**
   * Met à jour les information sur la salle 
   * Nécessiste d'avoir chargé la plage opératoire au préalable
   */
  function updateSalle() {
    if ($this->plageop_id && $this->salle_id) {
      $this->_deplacee = $this->_ref_plageop->salle_id != $this->salle_id;
    }
    
	  // Evite de recharger la salle quand ce n'est pas nécessaire  
    if ($this->plageop_id && !$this->_deplacee) {
      $this->_ref_salle =& $this->_ref_plageop->_ref_salle;
    }
    else {
	    $salle = new CSalle;
	    $this->_ref_salle = $salle->getCached($this->salle_id);
    }
  }
  
  function loadRefPlageOp($cache = 0) {
    $this->_ref_anesth = new CMediusers;
    if($cache) {
      $this->_ref_anesth = $this->_ref_anesth->getCached($this->anesth_id);
    } else {
      $this->_ref_anesth->load($this->anesth_id);
    }
    $this->_ref_anesth_visite = new CMediusers;
    if($cache) {
      $this->_ref_anesth_visite = $this->_ref_anesth_visite->getCached($this->prat_visite_anesth_id);
    } else {
      $this->_ref_anesth_visite->load($this->prat_visite_anesth_id);
    }
    
    $this->_ref_plageop = new CPlageOp;
    
    // Avec plage d'opération
    if ($this->plageop_id) {
      if($cache) {
        $this->_ref_plageop = $this->_ref_plageop->getCached($this->plageop_id);
      } else {
        $this->_ref_plageop->load($this->plageop_id);
      }
      $this->_ref_plageop->loadRefsFwd($cache);
      
      if (!$this->anesth_id) {
        $this->_ref_anesth =& $this->_ref_plageop->_ref_anesth;
      }
      
      $date = $this->_ref_plageop->date;
    } 
    // Hors plage
    else {
      $date = $this->date;
    }    
    
    $this->updateSalle();
    
    //Calcul du nombre de jour entre la date actuelle et le jour de l'operation
    $this->_compteur_jour = mbDaysRelative($date, mbDate());
    
    // Horaire global
    $this->_datetime          = "$date $this->time_operation";
    $this->_datetime_reel     = "$date $this->debut_op";
    $this->_datetime_reel_fin = "$date $this->fin_op";
    
    // Heure standard d'exécution des actes
    if ($this->fin_op) {
      $this->_acte_execution = $this->_datetime_reel_fin;
    } 
    elseif ($this->debut_op) {
      $this->_acte_execution = mbAddDateTime($this->temp_operation, $this->_datetime_reel);
    } 
    elseif ($this->time_operation != "00:00:00") {
      $this->_acte_execution = mbAddDateTime($this->temp_operation, $this->_datetime);
    } 
    else {
      $this->_acte_execution = mbDateTime();
    }

    $this->_view = "Intervention du ";
    $this->_view .= mbTransformTime(null, $this->_datetime, CAppUI::conf("date"));
  }
  
  function preparePossibleActes() {
    $this->loadRefPlageOp();
  }
  
  function loadRefsConsultAnesth() {
    if ($this->_ref_consult_anesth) {
      return;
    }
    
    $order = "consultation_anesth_id ASC";
    $this->_ref_consult_anesth = new CConsultAnesth();
    $this->_ref_consult_anesth->operation_id = $this->_id;
    $this->_ref_consult_anesth->loadMatchingObject($order);
  }
  
  function loadRefSejour($cache = 0) {
    if ($this->_ref_sejour) {
      return;
    }

    $this->_ref_sejour = new CSejour();
    if($cache) {
      $this->_ref_sejour = $this->_ref_sejour->getCached($this->sejour_id);
    } else {
      $this->_ref_sejour->load($this->sejour_id);
    }
  }
  
  function loadRefPatient($cache = 0) {
    $this->loadRefSejour($cache);
    $this->_ref_sejour->loadRefPatient($cache);
    $this->_ref_patient =& $this->_ref_sejour->_ref_patient;
  }
  
  function loadRefsFwd($cache = 0) {
    $this->loadRefsConsultAnesth();
    $this->_ref_consult_anesth->loadRefConsultation();
    $this->_ref_consult_anesth->_ref_consultation->countDocItems();
    $this->_ref_consult_anesth->_ref_consultation->canRead();
    $this->_ref_consult_anesth->_ref_consultation->canEdit();
    $this->loadRefPlageOp($cache);
    $this->loadExtCodesCCAM();
		
    $this->loadRefChir($cache);
    $this->loadRefPatient($cache);
    $this->_view = "Intervention de {$this->_ref_sejour->_ref_patient->_view} par le Dr {$this->_ref_chir->_view}";
  }
  
  function loadRefsBack() {
    $this->loadRefsFiles();
    $this->loadRefsActes();
    $this->loadRefsDocs();
  }
  
  function loadHprimFiles() {
    $hprimFile = new CHPrimXMLServeurActes();
    $hprimFile->setFinalPrefix($this);
    $hprimFile->getSentFiles();
    $this->_ref_hprim_files = $hprimFile->sentFiles;
  }
  
  function getPerm($permType) {
    switch($permType) {
      case PERM_EDIT :
        if(!$this->_ref_chir){
          $this->loadRefChir();
        }if(!$this->_ref_anesth){
          $this->loadRefPlageOp();
        }
        if($this->plageop_id) {
          return (($this->_ref_chir->getPerm($permType) || $this->_ref_anesth->getPerm($permType)) && $this->_ref_module->getPerm($permType));
        } else {
          return (($this->_ref_chir->getPerm($permType) || $this->_ref_anesth->getPerm($permType)) && $this->_ref_module->getPerm(PERM_READ));
        }
        break;
      default :
        return parent::getPerm($permType);
    }
    
    //if(!$this->_ref_chir){
    //  $this->loadRefChir();
    //}if(!$this->_ref_anesth){
    //  $this->loadRefPlageOp();
    //}
    //return ($this->_ref_chir->getPerm($permType) || $this->_ref_anesth->getPerm($permType));
  }

  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_sejour->loadRefsFwd();
    
    // Chargement du fillTemplate du praticien
    $this->_ref_chir->fillTemplate($template);

    // Chargement du fillTemplate du sejour
    $this->_ref_sejour->fillTemplate($template);
    
    // Chargement du fillTemplate de l'opération 
    $this->fillLimitedTemplate($template);
  }
  
  function fillLimitedTemplate(&$template) {
    $this->loadRefsFwd(1);

    $this->loadRefPraticien();
    $template->addProperty("Opération - Chirurgien"           , $this->_ref_praticien->_id ? ("Dr ".$this->_ref_praticien->_view) : '');
    $template->addProperty("Opération - Anesthésiste - nom"   , @$this->_ref_anesth->_user_last_name);
    $template->addProperty("Opération - Anesthésiste - prénom", @$this->_ref_anesth->_user_first_name);
    $template->addProperty("Opération - Anesthésie"           , $this->_lu_type_anesth);
    $template->addProperty("Opération - libellé"              , $this->libelle);
    $template->addProperty("Opération - CCAM1 - code"         , @$this->_ext_codes_ccam[0]->code);
    $template->addProperty("Opération - CCAM1 - description"  , @$this->_ext_codes_ccam[0]->libelleLong);
    $template->addProperty("Opération - CCAM2 - code"         , @$this->_ext_codes_ccam[1]->code);
    $template->addProperty("Opération - CCAM2 - description"  , @$this->_ext_codes_ccam[1]->libelleLong);
    $template->addProperty("Opération - CCAM3 - code"         , @$this->_ext_codes_ccam[2]->code);
    $template->addProperty("Opération - CCAM3 - description"  , @$this->_ext_codes_ccam[2]->libelleLong);
    $template->addProperty("Opération - CCAM - codes"         , implode(" - ", $this->_codes_ccam));
    $template->addProperty("Opération - CCAM - descriptions"  , implode(" - ", CMbArray::pluck($this->_ext_codes_ccam, "libelleLong")));
    $template->addProperty("Opération - salle"                , @$this->_ref_salle->nom);
    $template->addProperty("Opération - côté"                 , $this->cote);
    
    $template->addDateProperty("Opération - date"             , $this->_datetime);
    $template->addTimeProperty("Opération - heure"            , $this->time_operation);
    $template->addTimeProperty("Opération - durée"            , $this->temp_operation);    
    $template->addTimeProperty("Opération - durée réelle"     , $this->_duree_interv);
    $template->addTimeProperty("Opération - entrée bloc"      , $this->entree_salle);
    $template->addTimeProperty("Opération - pose garrot"      , $this->pose_garrot);
    $template->addTimeProperty("Opération - début op"         , $this->debut_op);
    $template->addTimeProperty("Opération - fin op"           , $this->fin_op);
    $template->addTimeProperty("Opération - retrait garrot"   , $this->retrait_garrot);
    $template->addTimeProperty("Opération - sortie bloc"      , $this->sortie_salle);
    
    $template->addProperty("Opération - depassement"          , $this->depassement);
    $template->addProperty("Opération - exams pre-op"         , $this->examen);
    $template->addProperty("Opération - matériel"             , $this->materiel);
    $template->addProperty("Opération - convalescence"        , $this->_ref_sejour->convalescence);
    $template->addProperty("Opération - remarques"            , $this->rques);
    
    $this->loadAffectationsPersonnel();
    foreach ($this->_ref_affectations_personnel as $emplacement => $affectations) {
      $locale = CAppUI::tr("CPersonnel.emplacement.$emplacement");
      $template->addProperty("Opération - personnel réel - $locale", implode(" - ", CMbArray::pluck(CMbArray::pluck(CMbArray::pluck($affectations, "_ref_personnel"), "_ref_user"), "_view")));
//      $template->addProperty("Opération - personnel - $emplacement", implode(" - ", CMbArray::pluck($affectations, "_ref_personnel", "_ref_user", "_view")));
    }
    
    $plageop = $this->_ref_plageop;
    $plageop->loadAffectationsPersonnel();
    foreach ($plageop->_ref_affectations_personnel as $emplacement => $affectations) {
      $locale = CAppUI::tr("CPersonnel.emplacement.$emplacement");
      $template->addProperty("Opération - personnel prévu - $locale", implode(" - ", CMbArray::pluck(CMbArray::pluck(CMbArray::pluck($affectations, "_ref_personnel"), "_ref_user"), "_view")));
//      $template->addProperty("Opération - personnel - $emplacement", implode(" - ", CMbArray::pluck($affectations, "_ref_personnel", "_ref_user", "_view")));
    }
  }
}

?>