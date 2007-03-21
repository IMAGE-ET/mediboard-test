<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

class CConsultation extends CMbObject {
  const PLANIFIE = 16;
  const PATIENT_ARRIVE = 32;
  const EN_COURS = 48;
  const TERMINE = 64;
  
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
  var $premiere      = null;
  var $tarif         = null;
  var $type_tarif    = null;
  var $arrivee       = null;

  // Form fields
  var $_etat           = null;
  var $_hour           = null;
  var $_min            = null;
  var $_check_premiere = null; // CheckBox: must be present in all forms!
  var $_somme          = null;
  var $_date           = null; // updated at loadRefs()
  var $_types_examen   = null;

  // Fwd References
  var $_ref_patient      = null;
  var $_ref_plageconsult = null;
  var $_ref_chir         = null; //pseudo RefFwd, get that in plageConsult

  // Back References
  var $_ref_consult_anesth = null;
  var $_ref_examaudio      = null;
  var $_ref_examcomp       = null;
  var $_ref_examnyha       = null;
  var $_ref_exampossum     = null;

  function CConsultation() {
    $this->CMbObject("consultation", "consultation_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "plageconsult_id" => "notNull ref class|CPlageconsult",
      "patient_id"      => "ref class|CPatient",
      "heure"           => "notNull time",
      "duree"           => "numchar maxLength|1",
      "secteur1"        => "currency min|0",
      "secteur2"        => "currency min|0",
      "chrono"          => "notNull enum list|16|32|48|64",
      "annule"          => "bool",
      "paye"            => "bool",
      "date_paiement"   => "date",
      "motif"           => "text",
      "rques"           => "text",
      "examen"          => "text",
      "traitement"      => "text",
      "premiere"        => "bool",
      "tarif"           => "str",
      "arrivee"         => "dateTime",
      "type_tarif"      => "enum list|cheque|CB|especes|tiers|autre default|cheque"
    );
  }
  
  function getSeeks() {
    return array(
      "plageconsult_id" => "ref|CPlageconsult",
      "patient_id"      => "ref|CPatient",
      "motif"           => "like",
      "rques"           => "like",
      "examen"          => "like",
      "traitement"      => "like"
    ); 
  }
  
  function getHelpedFields(){
    return array(
      "motif"         => null,
      "rques"         => null,
      "examen"        => null,
      "traitement"    => null,
      "compte_rendu"  => null
    );
  }
  
  function getEtat() {
    $etat = array();
    $etat[self::PLANIFIE]       = "Plan.";
    $etat[self::PATIENT_ARRIVE] = mbTranformTime(null, $this->arrivee, "%Hh%M");
    $etat[self::EN_COURS]       = "En cours";
    $etat[self::TERMINE]        = "Term.";
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
      if ($this->patient_id === 0) {
        $msg .= "Patient non valide<br />";
      }
    }
    return $msg . parent::check();
  }
  
  function loadView() {
    $this->loadRefPlageConsult();
    $this->loadRefsExamAudio();
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

  function loadRefsDocs() {
  	$this->loadRefConsultAnesth();
    if($this->_ref_consult_anesth->consultation_anesth_id) {
      $this->_ref_documents = new CCompteRendu();
      $where = array();
      $where[] = "(`object_class` = 'CConsultation' && `object_id` = '$this->consultation_id')
               || (`object_class` = 'CConsultAnesth' && `object_id` = '".$this->_ref_consult_anesth->consultation_anesth_id."')";
      $order = "nom";
      $this->_ref_documents = $this->_ref_documents->loadList($where, $order);
      $docs_valid = count($this->_ref_documents);
    }else{
      $docs_valid = parent::loadRefsDocs();
    }
    return $docs_valid;
  }

  function getNumDocsAndFiles(){
    if(!$this->_nb_files_docs){
      parent::getNumDocsAndFiles();
    }
    if($this->_nb_files_docs) {
      $this->getEtat();
      $this->_etat .= " ($this->_nb_files_docs Doc)";
    }
  }
  
  function getNumDocs(){
  	$this->loadRefConsultAnesth();
    if($this->_ref_consult_anesth->consultation_anesth_id) {
      $select = "count(`compte_rendu_id`) AS `total`";  
      $table  = "compte_rendu";
      $where  = array();
      $where[] = "(`object_class` = 'CConsultation' && `object_id` = '$this->consultation_id')
               || (`object_class` = 'CConsultAnesth' && `object_id` = '".$this->_ref_consult_anesth->consultation_anesth_id."')";
      $sql = new CRequest();
      $sql->addTable($table);
      $sql->addSelect($select);
      $sql->addWhere($where);
      $nbDocs = db_loadResult($sql->getRequest());
    }else{
      $nbDocs = parent::getNumDocs();
    }
    return $nbDocs;
  }
  
  function loadRefConsultAnesth() {
    $this->_ref_consult_anesth = new CConsultAnesth;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_consult_anesth->loadObject($where);
  }
  
  function loadRefsExamAudio(){
  	 $this->_ref_examaudio = new CExamAudio;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_examaudio->loadObject($where);
  }
  
  function loadRefsExamNyha(){
    $this->_ref_examnyha = new CExamNyha;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_examnyha->loadObject($where);
  }
  function loadRefsExamPossum(){
    $this->_ref_exampossum = new CExamPossum;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_exampossum->loadObject($where);
  }
  
  function loadRefsBack() {
    // Backward references
    $this->loadRefsFilesAndDocs();
    $this->getNumDocsAndFiles();
    $this->loadRefConsultAnesth();
    
    $this->loadRefsExamAudio();
    $this->loadExamsComp();
    $this->loadRefsExamNyha();
    $this->loadRefsExamPossum();
  }
  
  function loadExamsComp(){
  	$this->_ref_examcomp = new CExamComp;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $order = "examen";
    $this->_ref_examcomp = $this->_ref_examcomp->loadList($where,$order);
    
    foreach($this->_ref_examcomp as $keyExam => &$currExam){
      $this->_types_examen[$currExam->realisation][$keyExam] = $currExam;
    }
    
  }
  
  function getPerm($permType) {
    if(!$this->_ref_plageconsult) {
      $this->loadRefPlageConsult();
    }
    return $this->_ref_plageconsult->getPerm($permType);
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
    $template->addProperty("Consultation - motif"     , $this->motif);
    $template->addProperty("Consultation - remarques" , $this->rques);
    $template->addProperty("Consultation - examen"    , $this->examen);
    $template->addProperty("Consultation - traitement", $this->traitement);
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
      "joinon"    => "`file_class` = 'CConsultation'"
    );
    $tables[] = array (
      "label"     => "document(s)", 
      "name"      => "compte_rendu", 
      "idfield"   => "compte_rendu_id", 
      "joinfield" => "object_id",
      "joinon"    => "`object_class` = 'CConsultation'"
    );
    return parent::canDelete($msg, $oid, $tables);
  }
  
  function delete() {
    $msg1 = null;
    $msg2 = null;
    $this->loadRefConsultAnesth();
    $test1 = $this->canDelete($msg1);
    if($this->_ref_consult_anesth->consultation_anesth_id) {
      $test2 = $this->_ref_consult_anesth->canDelete($msg2);
    } else {
      $test2 = true;
    }
    if($test1 && $test2) {
      $this->_ref_consult_anesth->delete();
      return parent::delete();
    } else {
      if($msg1) {
        return $msg1;
      } else {
        return $msg2;
      }
    }
  }
}

?>