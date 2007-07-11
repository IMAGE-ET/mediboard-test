<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;
require_once($AppUI->getModuleClass("dPccam", "codableCCAM"));

class COperation extends CCodableCCAM {
  // DB Table key
  var $operation_id  = null;

  // DB References
  var $sejour_id  = null;
  var $chir_id    = null; // dupliqué en $sejour->praticien_id
  var $anesth_id  = null; // dupliqué en $plageop->anesth_id
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
  var $entree_bloc    = null;
  var $entree_salle   = null;
  var $pose_garrot    = null;
  var $debut_op       = null;
  var $fin_op         = null;
  var $retrait_garrot = null;
  var $sortie_salle   = null;
  var $entree_reveil  = null;
  var $sortie_reveil  = null;
  var $induction_debut= null;
  var $induction_fin  = null;

  // Form fields
  var $_hour_op        = null;
  var $_min_op         = null;
  var $_hour_urgence   = null;
  var $_min_urgence    = null;
  var $_lu_type_anesth = null;
  var $_codes_ccam     = array();
  var $_duree_interv   = null;
  var $_presence_salle = null;

  // Shortcut fields
  var $_datetime = null;
  
  // Links
  var $_link_editor = null;
  var $_link_viewer = null;

  // DB References
  var $_ref_chir           = null;
  var $_ref_plageop        = null;
  var $_ref_salle          = null;
  var $_ref_anesth         = null;
  var $_ref_sejour         = null;
  var $_ref_consult_anesth = null;
  var $_ref_actes_ccam     = array();

  // External references
  var $_ext_codes_ccam = null;

 //Filter Fields
  var $_date_min	 	= null;
  var $_date_max 		= null;
  var $_plage 			= null;
  var $_service 		= null;
  var $_intervention 	= null;
  var $_specialite 		= null;
  var $_scodes_ccam     = null;
  var $_prat_id     	= null;

  function COperation() {
    global $dPconfig;
    
    $this->CMbObject("operations", "operation_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
    
    $this->_locked = $dPconfig["dPplanningOp"]["COperation"]["locked"];
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
    $specs["cote"]           = "notNull enum list|droit|gauche|bilatéral|total";
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
    $specs["_date_min"] 	 = "date";
    $specs["_date_max"] 	 = "date moreEquals|_date_min";
    $specs["_plage"] 		 = "bool";
    $specs["_intervention"]  = "text";
    $specs["_prat_id"]  	 = "text";
    $specs["_specialite"]    = "text";
    $specs["_codes_ccam"]    = "text";
    return $specs;
  }
  
  
  function getExecutant_id($code) {
  	$this->loadRefChir();
  	$this->loadRefPlageOp();
  	return ($code == 4 ? $this->_ref_anesth->user_id: $this->chir_id);
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
  
  function check() {
    // Data checking
    $msg = null;
    if(!$this->operation_id) {
      if (!$this->chir_id) {
        $msg .= "Praticien non valide";
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
    if ($this->type_anesth != null) {
      $anesth = new CTypeAnesth;
      $orderanesth = "name";
      $anesth->load($this->type_anesth);;
      $this->_lu_type_anesth = $anesth->name;
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
  }

  function store($checkobject = true, $reorder = true) {
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
  }
  
  function loadRefPlageOp() {
    $this->_ref_anesth = new CMediusers;
    $this->_ref_anesth->load($this->anesth_id);
    $this->_ref_plageop = new CPlageOp;
    if($this->plageop_id) {
      $this->_ref_plageop->load($this->plageop_id);
      $this->_ref_plageop->loadRefsFwd();
      $this->_ref_salle =& $this->_ref_plageop->_ref_salle;
      if(!$this->anesth_id) {
        $this->_ref_anesth =& $this->_ref_plageop->_ref_anesth;
      }
      $this->_datetime = $this->_ref_plageop->date;
    } else {
      $this->_datetime = $this->date;
      $this->_ref_salle = new CSalle;
      $this->_ref_salle->load($this->salle_id);
    }
    $this->_datetime .= " ".$this->time_operation;
    $this->_acte_execution = mbAddDateTime($this->temp_operation, $this->_datetime);
  }
  
  function preparePossibleActes() {
  	$this->loadRefPlageOp();
  }
  
  
  function loadRefsConsultAnesth() {
    $this->_ref_consult_anesth = new CConsultAnesth();
    $where = array();
    $where["operation_id"] = "= '$this->operation_id'";
    $order = "consultation_anesth_id ASC";
    $this->_ref_consult_anesth->loadObject($where, $order);
  }
  
  function loadRefSejour() {
    $this->_ref_sejour = new CSejour();
    $this->_ref_sejour->load($this->sejour_id);
  }
  
  function loadRefsFwd() {
    $this->loadRefsConsultAnesth();
    $this->_ref_consult_anesth->loadRefConsultation();
    $this->_ref_consult_anesth->_ref_consultation->getNumDocsAndFiles();
    $this->_ref_consult_anesth->_ref_consultation->canRead();
    $this->_ref_consult_anesth->_ref_consultation->canEdit();
    $this->loadRefChir();
    $this->loadRefPlageOp();
    $this->loadRefsCodesCCAM();
    $this->loadRefSejour();
    $this->_ref_sejour->loadRefsFwd();
    $this->_view = "Intervention de {$this->_ref_sejour->_ref_patient->_view} par le Dr. {$this->_ref_chir->_view}";
  }
  
  function loadRefsBack() {
    $this->loadRefsFiles();
    $this->loadRefsActesCCAM();
    $this->loadRefsDocs();
  }
  
  function getPerm($permType) {
    if(!$this->_ref_chir){
      $this->loadRefChir();
    }if(!$this->_ref_anesth){
      $this->loadRefPlageOp();
    }
    return ($this->_ref_chir->getPerm($permType) || $this->_ref_anesth->getPerm($permType));
  }

 
  
  function fillTemplate(&$template) {
  	$this->loadRefsFwd();
    $this->_ref_sejour->loadRefsFwd();
    $this->_ref_chir->fillTemplate($template);
    $this->_ref_sejour->_ref_patient->fillTemplate($template);
    $this->fillLimitedTemplate($template);
  }
  
  function fillLimitedTemplate(&$template) {
    $this->loadRefsFwd();

    $dateFormat = "%d / %m / %Y";
    $timeFormat = "%Hh%M";

    $template->addProperty("Admission - Date"                 , mbTranformTime(null, $this->_ref_sejour->entree_prevue, $dateFormat));
    $template->addProperty("Admission - Heure"                , mbTranformTime(null, $this->_ref_sejour->entree_prevue, $timeFormat));
    $template->addProperty("Hospitalisation - Durée"          , $this->_ref_sejour->_duree_prevue);
    $template->addProperty("Hospitalisation - Date sortie"    , mbTranformTime(null, $this->_ref_sejour->sortie_prevue, $dateFormat));
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
    $template->addProperty("Opération - date"                 , mbTranformTime(null, $this->_datetime, $dateFormat));
    $template->addProperty("Opération - heure"                , mbTranformTime(null, $this->time_operation, $timeFormat));
    $template->addProperty("Opération - durée"                , mbTranformTime(null, $this->temp_operation, $timeFormat));
    $template->addProperty("Opération - entrée bloc"          , mbTranformTime(null, $this->entree_salle, $timeFormat));
    $template->addProperty("Opération - pose garrot"          , mbTranformTime(null, $this->pose_garrot, $timeFormat));
    $template->addProperty("Opération - début op"             , mbTranformTime(null, $this->debut_op, $timeFormat));
    $template->addProperty("Opération - fin op"               , mbTranformTime(null, $this->fin_op, $timeFormat));
    $template->addProperty("Opération - retrait garrot"       , mbTranformTime(null, $this->retrait_garrot, $timeFormat));
    $template->addProperty("Opération - sortie bloc"          , mbTranformTime(null, $this->sortie_salle, $timeFormat));
    $template->addProperty("Opération - depassement"          , $this->depassement);
    $template->addProperty("Opération - exams pre-op"         , $this->examen);
    $template->addProperty("Opération - matériel"             , $this->materiel);
    $template->addProperty("Opération - convalescence"        , $this->_ref_sejour->convalescence);
  }
}

?>