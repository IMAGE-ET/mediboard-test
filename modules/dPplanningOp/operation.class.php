<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
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

  var $depassement    = null;
  var $forfait        = null;
  var $fournitures    = null;
  var $annulee        = null;
  
  //timings enregistrés
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

  // Distant fields
  var $_datetime = null;
  var $_datetime_reel = null;
  var $_datetime_reel_fin = null;
  
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
  var $_ref_actes_ccam     = array();
  var $_ref_hprim_files    = null;

  // External references
  var $_ext_codes_ccam = null;

 //Filter Fields
  var $_date_min	 	= null;
  var $_date_max 		= null;
  var $_plage 			= null;
  var $_service 		 = null;
  var $_intervention = null;
  var $_specialite 	 = null;
  var $_scodes_ccam  = null;
  var $_prat_id      = null;
  var $_ccam_libelle = null;

  function COperation() {
    parent::__construct();
    $this->_locked = CAppUI::conf("dPplanningOp COperation locked");
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'operations';
    $spec->key   = 'operation_id';
    return $spec;
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["sejour_id"]      = "notNull ref class|CSejour";
    $specs["chir_id"]        = "notNull ref class|CMediusers";
    $specs["anesth_id"]      = "ref class|CMediusers";
    $specs["plageop_id"]     = "ref class|CPlageOp";
    $specs["pause"]          = "time";
    $specs["salle_id"]       = "ref class|CSalle";
    $specs["date"]           = "date";
    $specs["code_uf"]        = "str length|3";
    $specs["libelle_uf"]     = "str maxLength|35";
    $specs["libelle"]        = "str confidential";
    $specs["cote"]           = "notNull enum list|droit|gauche|bilatéral|total|inconnu default|inconnu";
    $specs["temp_operation"] = "time";
    $specs["entree_salle"]   = "time";
    $specs["sortie_salle"]   = "time";
    $specs["time_operation"] = "time";
    $specs["examen"]         = "text confidential";
    $specs["materiel"]       = "text confidential";
    $specs["commande_mat"]   = "bool";
    $specs["info"]           = "bool";
    $specs["type_anesth"]    = "ref class|CTypeAnesth";
    $specs["rques"]          = "text confidential";
    $specs["rank"]           = "num max|255";
    $specs["depassement"]    = "currency min|0 confidential";
    $specs["forfait"]        = "currency min|0 confidential";
    $specs["fournitures"]    = "currency min|0 confidential";
    $specs["annulee"]        = "bool";
    $specs["pose_garrot"]    = "time";
    $specs["debut_op"]       = "time";
    $specs["fin_op"]         = "time";
    $specs["retrait_garrot"] = "time";
    $specs["entree_reveil"]  = "time";
    $specs["sortie_reveil"]  = "time";
    $specs["induction_debut"]= "time";
    $specs["induction_fin"]  = "time";
    $specs["entree_bloc"]    = "time";
    $specs["anapath"]        = "bool";
    $specs["labo"]           = "bool";
    $specs["horaire_voulu"]  = "time";

    $specs["_date_min"]      = "date";
    $specs["_date_max"]      = "date moreEquals|_date_min";
    $specs["_plage"]         = "bool";
    $specs["_intervention"]  = "text";
    $specs["_prat_id"]       = "text";
    $specs["_specialite"]    = "text";
    $specs["_ccam_libelle"]  = "bool default|1";
    $specs["_hour_op"]       = "";
    $specs["_min_op"]        = "";
    
    $specs["_datetime"]  = "dateTime";
    return $specs;
  }
  
  function getExecutantId($code_activite) {
    $this->loadRefChir();
    $this->loadRefPlageOp();
    return ($code_activite == 4 ? $this->_ref_anesth->user_id: $this->chir_id);
  }
  
  function getSeeks() {
    return array (
      "chir_id"    => "ref|CMediusers",
      "plageop_id" => "ref|CPlageOp",
      "libelle"    => "like",
      "materiel"   => "like",
    );
  }

  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["actes_CCAM"]          = "CActeCCAM object_id";
      $backRefs["dossiers_anesthesie"] = "CConsultAnesth operation_id";
      $backRefs["naissances"]          = "CNaissance operation_id";
     return $backRefs;
  }
  
  function getHelpedFields(){
    return array(
      "examen"        => null,
      "materiel"      => null,
      "convalescence" => null,
      "rques"         => null
    );
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
   	    $msg .= "Intervention en dehors du séjour ";
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
      $this->codes_ccam = join($this->_codes_ccam, "|");
    }
    
    if ($this->codes_ccam) {
      $this->codes_ccam = strtoupper($this->codes_ccam);
      $codes_ccam = explode("|", $this->codes_ccam);
      $XPosition = true;
      while($XPosition !== false) {
        $XPosition = array_search("-", $codes_ccam);
        if ($XPosition !== false) {
          array_splice($codes_ccam, $XPosition, 1);
        }
      }
      $this->codes_ccam = implode("|", $codes_ccam);
    }
    if($this->_hour_op !== null and $this->_min_op !== null) {
      $this->temp_operation = 
        $this->_hour_op.":".
        $this->_min_op.":00";
    }
    if($this->_hour_urgence !== null and $this->_min_urgence !== null) {
      $this->time_operation = 
        $this->_hour_urgence.":".
        $this->_min_urgence.":00";
    }
    if($this->_hour_voulu != null and $this->_min_voulu != null) {
      $this->horaire_voulu = 
        $this->_hour_voulu.":".
        $this->_min_voulu.":00";
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
    if ($this->plageop_id) {
      $plageTmp = new CPlageOp;
      $plageTmp->load($this->plageop_id);
      if ($plageTmp->spec_id) {
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
  
  function loadRefChir() {
    $this->_ref_chir = new CMediusers;
    $this->_ref_chir->load($this->chir_id);
    $this->_praticien_id = $this->_ref_chir->_id;
  }
  
  function loadRefPraticien(){
    $this->loadRefChir();
    $this->_ref_praticien =& $this->_ref_chir;
  }
  
  function getActeExecution() {
    $this->loadRefPlageOp();
  }
  
  /**
   * Met à jour les information sur la salle 
   * Nécessiste d'avoir chargé la plage opératoire au préalable
   */
  function updateSalle() {
    if ($this->plageop_id) {
      $this->_deplacee = $this->_ref_plageop->salle_id != $this->salle_id;
    }
    
	  // Evite de recharger la salle quand ce n'est pas nécessaire  
    if ($this->plageop_id && !$this->_deplacee) {
      $this->_ref_salle =& $this->_ref_plageop->_ref_salle;
    }
    else {
	    $this->_ref_salle = new CSalle;
	    $this->_ref_salle->load($this->salle_id);
    }
  }
  
  function loadRefPlageOp() {
    $this->_ref_anesth = new CMediusers;
    $this->_ref_anesth->load($this->anesth_id);
    $this->_ref_plageop = new CPlageOp;
    
    // Avec plage d'opération
    if ($this->plageop_id) {
      $this->_ref_plageop->load($this->plageop_id);
      $this->_ref_plageop->loadRefsFwd();
      
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
    
    // Horraire global
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
  
  function loadRefSejour() {
    if ($this->_ref_sejour) {
      return;
    }

    $this->_ref_sejour = new CSejour();
	  $this->_ref_sejour->load($this->sejour_id);
  }
  
  function loadRefPatient() {
    $this->loadRefSejour();
    $this->_ref_sejour->loadRefPatient();
    $this->_ref_patient =& $this->_ref_sejour->_ref_patient;
  }
  
  function loadRefsFwd() {
    $this->loadRefsConsultAnesth();
    $this->_ref_consult_anesth->loadRefConsultation();
    $this->_ref_consult_anesth->_ref_consultation->getNumDocsAndFiles();
    $this->_ref_consult_anesth->_ref_consultation->canRead();
    $this->_ref_consult_anesth->_ref_consultation->canEdit();
    $this->loadRefChir();
    $this->loadRefPlageOp();
    $this->loadExtCodesCCAM();
    $this->loadRefSejour();
    $this->_ref_sejour->loadRefsFwd();
    $this->_view = "Intervention de {$this->_ref_sejour->_ref_patient->_view} par le Dr {$this->_ref_chir->_view}";
  }
  
  function loadRefsBack() {
    $this->loadRefsFiles();
    $this->loadRefsActesCCAM();
    $this->loadRefsActesNGAP();
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
        return (($this->_ref_chir->getPerm($permType) || $this->_ref_anesth->getPerm($permType)) && $this->_ref_module->getPerm($permType));
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
    $this->loadRefsFwd();

    $dateFormat = "%d / %m / %Y";
    $timeFormat = "%Hh%M";
    $this->loadRefPraticien();
    $template->addProperty("Opération - Chirurgien"           , $this->_ref_praticien->_id ? ("Dr ".$this->_ref_praticien->_view) : '');
    $template->addProperty("Opération - Anesthésiste - nom"   , @$this->_ref_anesth->_user_last_name);
    $template->addProperty("Opération - Anesthésiste - prénom", @$this->_ref_anesth->_user_first_name);
    $template->addProperty("Opération - Anesthésie"           , $this->_lu_type_anesth);
    $template->addProperty("Opération - libellé"              , $this->libelle);
    $template->addProperty("Opération - CCAM - code"          , @$this->_ext_codes_ccam[0]->code);
    $template->addProperty("Opération - CCAM - description"   , @$this->_ext_codes_ccam[0]->libelleLong);
    $template->addProperty("Opération - CCAM2 - code"         , @$this->_ext_codes_ccam[1]->code);
    $template->addProperty("Opération - CCAM2 - description"  , @$this->_ext_codes_ccam[1]->libelleLong);
    $template->addProperty("Opération - CCAM3 - code"         , @$this->_ext_codes_ccam[2]->code);
    $template->addProperty("Opération - CCAM3 - description"  , @$this->_ext_codes_ccam[2]->libelleLong);
    $template->addProperty("Opération - CCAM complet"         , implode(" - ", $this->_codes_ccam));
    $template->addProperty("Opération - salle"                , @$this->_ref_plageop->_ref_salle->nom);
    $template->addProperty("Opération - côté"                 , $this->cote);
    $template->addProperty("Opération - date"                 , mbTransformTime(null, $this->_datetime, $dateFormat));
    $template->addProperty("Opération - heure"                , mbTransformTime(null, $this->time_operation, $timeFormat));
    $template->addProperty("Opération - durée"                , mbTransformTime(null, $this->temp_operation, $timeFormat));
    $template->addProperty("Opération - entrée bloc"          , mbTransformTime(null, $this->entree_salle, $timeFormat));
    $template->addProperty("Opération - pose garrot"          , mbTransformTime(null, $this->pose_garrot, $timeFormat));
    $template->addProperty("Opération - début op"             , mbTransformTime(null, $this->debut_op, $timeFormat));
    $template->addProperty("Opération - fin op"               , mbTransformTime(null, $this->fin_op, $timeFormat));
    $template->addProperty("Opération - retrait garrot"       , mbTransformTime(null, $this->retrait_garrot, $timeFormat));
    $template->addProperty("Opération - sortie bloc"          , mbTransformTime(null, $this->sortie_salle, $timeFormat));
    $template->addProperty("Opération - depassement"          , $this->depassement);
    $template->addProperty("Opération - exams pre-op"         , $this->examen);
    $template->addProperty("Opération - matériel"             , $this->materiel);
    $template->addProperty("Opération - convalescence"        , $this->_ref_sejour->convalescence);
    $template->addProperty("Opération - remarques"            , $this->rques);
  }
}

?>