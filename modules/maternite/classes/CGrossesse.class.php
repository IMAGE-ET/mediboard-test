<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CGrossesse extends CMbObject{
  // DB Table key
  public $grossesse_id;
  
  // DB References
  public $parturiente_id;
  
  // DB Fields
  public $terme_prevu;
  public $active;
  public $multiple;
  public $allaitement_maternel;
  public $date_fin_allaitement;
  public $date_dernieres_regles;
  public $lieu_accouchement;
  public $fausse_couche;
  public $rques;

  // DB References
  public $_ref_parturiente;
  
  // Distant fields
  public $_ref_naissances;
  public $_ref_sejours     = array();
  public $_ref_consultations = array();
  public $_ref_last_consult_anesth;
  
  // Form fields
  public $_praticiens;
  public $_date_fecondation;
  public $_semaine_grossesse;
  public $_terme_vs_operation;
  public $_operation_id;
  public $_allaitement_en_cours;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'grossesse';
    $spec->key   = 'grossesse_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["parturiente_id"] = "ref notNull class|CPatient";
    $specs["terme_prevu"]    = "date notNull";
    $specs["active"]         = "bool default|1";
    $specs["multiple"]       = "bool default|0";
    $specs["allaitement_maternel"] = "bool default|0";
    $specs["date_fin_allaitement"] = "date";
    if (CAppUI::conf("maternite CGrossesse date_regles_obligatoire")) {
      $specs["date_dernieres_regles"] = "date notNull";
    }
    else {
      $specs["date_dernieres_regles"] = "date";
    }
    $specs["lieu_accouchement"] = "enum list|sur_site|exte default|sur_site";
    $specs["fausse_couche"]     = "enum list|inf_15|sup_15";
    $specs["rques"]             = "text helped";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["naissances"] = "CNaissance grossesse_id";
    $backProps["consultations"] = "CConsultation grossesse_id";
    $backProps["sejours"] = "CSejour grossesse_id";
    return $backProps;
  }
  
  function loadRefsFwd() {
    $this->loadRefParturiente();
  }
  
  function loadRefParturiente() {
    return $this->_ref_parturiente = $this->loadFwdRef("parturiente_id", true);
  }
  
  function loadRefsNaissances() {
    return $this->_ref_naissances = $this->loadBackRefs("naissances");
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefParturiente();
    $this->_view = "Terme du " . CMbDT::dateToLocale($this->terme_prevu);
    // Nombre de semaines (aménorrhée = 41, grossesse = 39)
    $this->_date_fecondation = CMbDT::date("-41 weeks", $this->terme_prevu);
    $this->_allaitement_en_cours = $this->allaitement_maternel && !$this->active && (!$this->date_fin_allaitement || $this->date_fin_allaitement > CMbDT::date());
    $this->_semaine_grossesse = ceil(CMbDT::daysRelative($this->_date_fecondation, CMbDT::date()) / 7);
  }
  
  function loadRefsSejours() {
    return $this->_ref_sejours = $this->loadBackRefs("sejours");
  }
  
  function loadRefsConsultations() {
    if ($this->_ref_consultations) {
      return $this->_ref_consultations;
    }
    return $this->_ref_consultations = $this->loadBackRefs("consultations");
  }
  
  function loadLastConsultAnesth() {
    $consultations = $this->loadRefsConsultations();
    foreach ($consultations as $_consultation) {
      $consult_anesth = $_consultation->loadRefConsultAnesth();
      if ($consult_anesth->_id) {
        return $this->_ref_last_consult_anesth = $_consultation;
      }
    }
    return $this->_ref_last_consult_anesth = new CConsultation;
  }
  
  function loadView() {
    parent::loadView();
    $naissances = $this->loadRefsNaissances();
    $sejours = CMbObject::massLoadFwdRef($naissances, "sejour_enfant_id");
    CMbObject::massLoadFwdRef($sejours, "patient_id");
    
    foreach ($naissances as $_naissance) {
      $_naissance->loadRefSejourEnfant()->loadRefPatient();
    }
  }
  
  function delete() {
    $consults = $this->loadRefsConsultations();
    $sejours  = $this->loadRefsSejours();
    
    if ($msg = parent::delete()) {
      return $msg;
    }
    
    $msg = "";
    
    foreach ($consults as $_consult) {
      $_consult->grossesse_id = "";
      if ($_msg = $_consult->store()) {
        $msg .= "\n $_msg";
      }
    }
    
    
    foreach ($sejours as $_sejour) {
      $_sejour->grossesse_id = "";
      if ($_msg = $_sejour->store()) {
        $msg .= "\n $_msg";
      }
    }
    
    if ($msg) {
      return $msg;
    }
  }
}
