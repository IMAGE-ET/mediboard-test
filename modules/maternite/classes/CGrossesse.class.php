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
  var $grossesse_id   = null;
  
  // DB References
  var $parturiente_id = null;
  
  // DB Fields
  var $terme_prevu    = null;
  var $active         = null;
  var $multiple       = null;
  var $allaitement_maternel = null;
  var $date_fin_allaitement = null;
  var $date_dernieres_regles = null;
  
  // DB References
  var $_ref_parturiente = null;
  
  // Distant fields
  var $_ref_naissances  = null;
  var $_ref_sejours     = array();
  var $_ref_consultations = array();
  var $_ref_last_consult_anesth = null;
  
  // Form fields
  var $_praticiens      = null;
  var $_date_fecondation = null;
  var $_semaine_grossesse = null;
  var $_terme_vs_operation = null;
  var $_operation_id    = null;
  var $_allaitement_en_cours = null;
  
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
    $specs["allaitement_maternel"] = "bool default|1";
    $specs["date_fin_allaitement"] = "date";
    $specs["date_dernieres_regles"] = "date";
    
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
    $this->_view = "Terme du " . mbDateToLocale($this->terme_prevu);
    // Nombre de semaines (aménorrhée = 41, grossesse = 39)
    $this->_date_fecondation = mbDate("-39 weeks", $this->terme_prevu);
    
    $this->_allaitement_en_cours =
      $this->allaitement_maternel &&
      (!$this->date_fin_allaitement ||
      $this->date_fin_allaitement > mbDate());
    
    $this->_semaine_grossesse = ceil(mbDaysRelative($this->_date_fecondation, mbDate()) / 7);
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
    $naissances = $this->loadRefsNaissances();
    $sejours = CMbObject::massLoadFwdRef($naissances, "sejour_enfant_id");
    CMbObject::massLoadFwdRef($sejours, "patient_id");
    
    foreach ($naissances as $_naissance) {
      $_naissance->loadRefSejourEnfant()->loadRefPatient();
    }
  }
}
?>