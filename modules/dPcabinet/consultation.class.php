<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("mbobject" ));

require_once($AppUI->getModuleClass("dPpatients"   , "patients"));
require_once($AppUI->getModuleClass("dPcabinet"    , "consultAnesth"));
require_once($AppUI->getModuleClass("dPcabinet"    , "plageconsult"));
require_once($AppUI->getModuleClass("dPfiles"      , "files"));
require_once($AppUI->getModuleClass("dPcabinet"    , "examaudio"));
require_once($AppUI->getModuleClass("dPcompteRendu", "compteRendu"));
require_once($AppUI->getModuleClass("dPcabinet"    , "examComp"));

// Enum for Consultation.chrono
if(!defined("CC_PLANIFIE")) {
  define("CC_PLANIFIE"      , 16);
  define("CC_PATIENT_ARRIVE", 32);
  define("CC_EN_COURS"      , 48);
  define("CC_TERMINE"       , 64);
}

class CConsultation extends CMbObject {
  // DB Table key
  var $consultation_id = null;

  // DB References
  var $plageconsult_id = null;
  var $patient_id      = null;

  // DB fields
  var $heure         = null;
  var $duree         = null;
  var $secteur1      = null;
  var $secteur2      = null;
  var $chrono        = null;
  var $annule        = null;
  var $paye          = null;
  var $date_paiement = null;
  var $motif         = null;
  var $rques         = null;
  var $examen        = null;
  var $traitement    = null;
  var $compte_rendu  = null;
  var $premiere      = null;
  var $tarif         = null;
  var $type_tarif    = null;
  var $arrivee       = null;
  
  // Document fields:  to be externalized
  var $cr_valide  = null;
  var $ordonnance = null;
  var $or_valide  = null;
  var $courrier1  = null;
  var $c1_valide  = null;
  var $courrier2  = null;
  var $c2_valide  = null;

  // Form fields
  var $_etat           = null;
  var $_hour           = null;
  var $_min            = null;
  var $_check_premiere = null; // CheckBox: must be present in all forms!
  var $_somme          = null;
  var $_date           = null; // updated at loadRefs()

  // Fwd References
  var $_ref_patient      = null;
  var $_ref_plageconsult = null;
  var $_ref_chir         = null; //pseudo RefFwd, get that in plageConsult

  // Back References
  var $_ref_files          = null;
  var $_ref_documents      = null; // Pseudo backward references to documents
  var $_ref_consult_anesth = null;
  var $_ref_examaudio      = null;
  var $_ref_examcomp       = null;

  function CConsultation() {
    $this->CMbObject("consultation", "consultation_id");

    static $props = array (
      "plageconsult_id" => "ref|notNull",
      "patient_id"      => "ref|notNull",
      "heure"           => "time|notNull",
      "duree"           => "num",
      "secteur1"        => "currency|min|0",
      "secteur2"        => "currency|min|0",
      "chrono"          => "enum|16|32|48|64|notNull",
      "annule"          => "enum|0|1",
      "paye"            => "enum|0|1",
      "date_paiement"   => "date",
      "motif"           => "text",
      "rques"           => "text",
      "examen"          => "text",
      "traitement"      => "text",
      "compte_rendu"    => "html",
      "ordonnance"      => "html",
      "courrier1"       => "html",
      "courrier2"       => "html",
      "premiere"        => "enum|0|1",
      "tarif"           => "str",
      "type_tarif"      => "str" // En faire un enum
    );
    
    $this->_props =& $props;
    
    static $seek = array(
      "plageconsult_id" => "ref|CPlageconsult",
      "patient_id"      => "ref|CPatient",
      "motif"           => "like",
      "rques"           => "like",
      "examen"          => "like",
      "traitement"      => "like"
    ); 
    $this->_seek =& $seek;
  }
  
  function getEtat() {
    $etat = array();
    $etat[CC_PLANIFIE]       = "Planif.";
    $etat[CC_PATIENT_ARRIVE] = "Arrivé ".mbTranformTime(null, $this->arrivee, "%Hh%M");
    $etat[CC_EN_COURS]       = "En cours";
    $etat[CC_TERMINE]        = "Term.";
    if($this->chrono)
      $this->_etat = $etat[$this->chrono];
    if ($this->annule) {
      $this->_etat = "Ann.";
    }
  }
  
  
  function updateFormFields() {
    parent::updateFormFields();
  	$this->_somme = $this->secteur1 + $this->secteur2;
    if($this->date_paiement == "0000-00-00")
      $this->date_paiement = null;
    $this->_hour = intval(substr($this->heure, 0, 2));
    $this->_min  = intval(substr($this->heure, 3, 2));
    $this->_check_premiere = $this->premiere;
    $this->getEtat();
    $this->_view = "Consultation ".$this->_etat;
  }
   
  function updateDBFields() {
  	if (($this->_hour !== null) && ($this->_min !== null)) {
      $this->heure = $this->_hour.":".$this->_min.":00";
    }
    if($this->date_paiement == "0000-00-00")
      $this->date_paiement = null;
    if(($this->_somme !== null) && ($this->_somme != $this->secteur1 + $this->secteur2)){
      $this->secteur1 = 0;
      $this->secteur2 = $this->_somme;
    }
    // @todo : verifier si on ne fait ça que si _check_premiere est non null
    $this->premiere = $this->_check_premiere ? 1 : 0;
  }

  function check() {
    // Data checking
    $msg = null;
    if(!$this->consultation_id) {
      if (!$this->plageconsult_id) {
        $msg .= "Plage de consultation non valide<br />";
      }
      if (!$this->patient_id) {
        $msg .= "Patient non valide<br />";
      }
    }
    return $msg . parent::check();
  }
  

  function loadRefPatient() {
    $this->_ref_patient = new CPatient;
    $this->_ref_patient->load($this->patient_id);
  }
  
  function loadRefPlageConsult() {
    $this->_ref_plageconsult = new CPlageconsult;
    $this->_ref_plageconsult->load($this->plageconsult_id);
    $this->_ref_plageconsult->loadRefsFwd();
    $this->_ref_chir =& $this->_ref_plageconsult->_ref_chir;
    $this->_date = $this->_ref_plageconsult->date;
  }
  
  function loadRefsFwd() {
    $this->loadRefPatient();
    $this->loadRefPlageConsult();
    $this->_view = "Consult. de ".$this->_ref_patient->_view." par le Dr. ".$this->_ref_plageconsult->_ref_chir->_view;
    $this->_view .= " (".mbTranformTime(null, $this->_ref_plageconsult->date, "%d/%m/%Y").")";
  }

  function loadRefsFiles() {
    $this->_ref_files = new CFile();
    $this->_ref_files = $this->_ref_files->loadFilesForObject($this);
  }

  function loadRefsDocs() {
    $this->_ref_documents = array();
    $this->_ref_documents = new CCompteRendu();
    $where = array();
    $this->loadRefConsultAnesth();
    if($this->_ref_consult_anesth->consultation_anesth_id) {
    	$where[] = "(`type` = 'consultation' && `object_id` = '$this->consultation_id') || (`type` = 'consultAnesth' && `object_id` = '".$this->_ref_consult_anesth->consultation_anesth_id."')";
    } else {
      $where["type"] = "= 'consultation'";
      $where["object_id"] = "= '$this->consultation_id'";
    }
    $order = "nom";
    $this->_ref_documents = $this->_ref_documents->loadList($where, $order);
    $docs_valid = 0;
    foreach ($this->_ref_documents as $curr_doc) {
      if ($curr_doc->source) {
        $docs_valid++;
      }
    }
    if($docs_valid) {
      $this->getEtat();
      $this->_etat .= " ($docs_valid Doc.)";
    }
  }

  function getNumDocs(){
  	$sql = "SELECT count(compte_rendu_id) FROM compte_rendu WHERE (";  
  	$this->loadRefConsultAnesth();
    if($this->_ref_consult_anesth->consultation_anesth_id) {
      $where = "(`type` = 'consultation' OR `type` = 'consultAnesth')";
    }else{
      $where = "(`type` = 'consultation')";
    }
    $where .= "\n AND object_id = '$this->consultation_id')";
  	$nbDocs = db_loadResult($sql . $where);
    if($nbDocs) {
      $this->getEtat();
      $this->_etat .= " ($nbDocs Doc.)";
    }
  }
  
  function loadRefConsultAnesth() {
    $this->_ref_consult_anesth = new CConsultAnesth;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_consult_anesth->loadObject($where);
  }
  
  function loadRefsBack() {
    // Backward references
    $this->loadRefsFiles();
    $this->loadRefsDocs();
    $this->loadRefConsultAnesth();
    
    $this->_ref_examaudio = new CExamAudio;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_examaudio->loadObject($where);
    
    $this->_ref_examcomp = new CExamComp;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $order = "examen";
    $this->_ref_examcomp = $this->_ref_examcomp->loadList($where,$order);
  }
  
  function canRead($withRefs = true) {
    if($withRefs) {
      $this->loadRefPlageConsult();
    }
    $this->_canRead = $this->_ref_plageconsult->canRead();
    return $this->_canRead;
  }

  function canEdit($withRefs = true) {
    if($withRefs) {
      $this->loadRefPlageConsult();
    }
    $this->_canEdit = $this->_ref_plageconsult->canEdit() && isMbModuleEditAll("dPcabinet");
    return $this->_canEdit;
  }
  
  function fillTemplate(&$template) {
  	$this->loadRefsFwd();
    $this->_ref_plageconsult->loadRefsFwd();
    $this->_ref_plageconsult->_ref_chir->fillTemplate($template);
    $this->_ref_patient->fillTemplate($template);
    $this->fillLimitedTemplate($template);
  }
  
  function fillLimitedTemplate(&$template) {
    $this->loadRefsFwd();
    $template->addProperty("Consultation - date"      , mbTranformTime("+0 DAY", $this->_ref_plageconsult->date, "%d / %m / %Y") );
    $template->addProperty("Consultation - heure"     , $this->heure);
    $template->addProperty("Consultation - motif"     , nl2br($this->motif));
    $template->addProperty("Consultation - remarques" , nl2br($this->rques));
    $template->addProperty("Consultation - examen"    , nl2br($this->examen));
    $template->addProperty("Consultation - traitement", nl2br($this->traitement));
  }

  function canDelete(&$msg, $oid = null) {
    $this->loadRefPlageConsult();
    if($this->_ref_plageconsult->date < mbDate()){
      $msg = "Imposible de supprimer une consultation passée";
      return false;
    }
    /*
    $tables[] = array (
      "label"     => "consultation(s) d'anesthésie", 
      "name"      => "consultation_anesth", 
      "idfield"   => "consultation_anesth_id", 
      "joinfield" => "consultation_id"
    );*/
    $tables[] = array (
      "label"     => "fichier(s)", 
      "name"      => "files_mediboard", 
      "idfield"   => "file_id", 
      "joinfield" => "file_object_id",
      "joinon"    => "`file_class`='CConsultation'"
    );
    $tables[] = array (
      "label"     => "document(s)", 
      "name"      => "compte_rendu", 
      "idfield"   => "compte_rendu_id", 
      "joinfield" => "object_id",
      "joinon"    => "`type` = 'consultation'"
    );
    return parent::canDelete($msg, $oid, $tables);
  }
  
  function delete() {
    $msg1 = null;
    $msg2 = null;
    $this->loadRefConsultAnesth();
    if($this->_ref_consult_anesth->canDelete($msg1) && $this->canDelete($msg2)) {
      $this->_ref_consult_anesth->delete();
      parent::delete();
    } else {
      return $msg1." et ".$msg2;
    }
  }
}

?>